<?php
    
    class WPH_Asset_PostProcessing
        {
            var $action;
            var $file_path;            
            var $full_file_path;
            
            var $actual_file_path;
            
            var $allowed_file_type  =   array( 'css', 'js' );
                        
            var $environment        =   array();
            
            var $file_cache_prefix  =   'postprocessed_';
            
            var $file_type          =   '';
            
            var $ignore_processing  =   FALSE;
            
            function __construct( $action, $file_path )
                {
                    $this->action           =   $action;
                    $this->file_path        =   $file_path;
                    
                    //append doc root to path 
                    $this->full_file_path   =   $_SERVER['DOCUMENT_ROOT'] . '/' . ltrim ( $this->file_path, '/' ); 
                    $this->full_file_path   =   str_replace( '\\', '/', $this->full_file_path);
                    
                    $pathinfo  =   pathinfo ( $this->full_file_path );
                    $extension  =   isset ( $pathinfo['extension'] ) ?  $pathinfo['extension']  :   '';
                    if ( ! in_array ( $extension, $this->allowed_file_type ) )
                        die();
                
                    $this->file_type    =   $extension;
                    $this->define_constants();
                    
                    $this->load_environment();
                    
                    $this->check_file_ignore();
                          
                    //check if the file is already processed and if cached
                    if ( $this->file_already_processed()    &&  ! $this->ignore_processing )
                        {
                            $this->output_data();
                            die();
                        }
 
                    $this->load_wordpress();
                    
                    $this->clear_ob_buffering();
                    
                    $this->reverse_file_url();
                  
                    if ( ! $this->ignore_processing )
                        {
                            $this->process_file_content();
                            $this->output_data();
                        }
                        else
                            $this->output_existing_file();
                }
            
            
            function define_constants()
                {
                    $SCRIPT_NAME    =   $_SERVER['DOCUMENT_ROOT'] . $_SERVER['SCRIPT_NAME'];
                    $SCRIPT_NAME    =   str_replace( '\\', '/', $SCRIPT_NAME);
                    $SCRIPT_NAME_items  =   explode("/", $SCRIPT_NAME);
                    
                    //exclude last 4 as there's never a location for wp-load.php
                    $SCRIPT_NAME_items  =   array_slice($SCRIPT_NAME_items, 0, count($SCRIPT_NAME_items) - 4);
                    
                    while( count( $SCRIPT_NAME_items ) >   0 )
                        {
                            $location   =   implode( '/', $SCRIPT_NAME_items );
                            
                            if ( file_exists ( $location . '/wp-load.php' ) )
                                {
                                    define( 'ABSPATH', $location . '/' );
                                    break;
                                }
                                
                            $SCRIPT_NAME_items  =   array_slice($SCRIPT_NAME_items, 0, count($SCRIPT_NAME_items) - 1);
                            
                        }
                    
                }
                        
            /**
            * Load environment
            * 
            */
            function load_environment()
                {
                    //require_once( ABSPATH . 'environment.php');
                    require_once( '../../../uploads/wph/environment.php');
                    
                    $this->environment  =   json_decode($environment_variable);
                    
                    if ( ! is_object( $this->environment )  ||  ! isset ( $this->environment->cache_path ) )
                        die();
                    
                }
               
            
            function check_file_ignore()
                {
                    //ignore when on admin
                    if ( isset ( $_SERVER['HTTP_REFERER'] ) &&  strpos( $_SERVER['HTTP_REFERER'], 'post.php' )     !== FALSE )
                        $this->ignore_processing    =   TRUE;
                    
                    //Elementor
                    if ( isset ( $_SERVER['HTTP_REFERER'] ) &&  strpos( $_SERVER['HTTP_REFERER'], 'elementor-preview' )     !== FALSE )
                        $this->ignore_processing    =   TRUE;
                    
                    
                }
                
            
            function file_already_processed()
                {
                    if ( file_exists( $this->environment->cache_path . $this->get_cached_file_name( $this->file_path ) ) )
                        return TRUE;
                        
                    return FALSE;
                    
                }
                
                
            function get_cached_file_name ( $file_path )
                {
                    return  $this->file_cache_prefix    .   md5 ( $file_path );   
                }
            
            
            function load_wordpress()
                {
                    require_once( ABSPATH . '/wp-load.php' );   
                    
                }
                
            
            function clear_ob_buffering()
                {
                    wp_ob_end_flush_all();    
                    
                }
            
            function reverse_file_url()
                {
                    
                    $domain_url         =   site_url();
                    $domain_url         =   str_replace( array ( 'http://', 'https://' ) , "", $domain_url );
                    $protocol           =   (is_ssl())  ?   'https://' :   'http://';
                    $domain_url         =   $protocol . $domain_url;
                    
                    global $wph;
                    
                    //revert the file_path
                    $replacement_list   =   $wph->functions->get_replacement_list();
                    //reverse the list
                    $replacement_list   =   array_flip($replacement_list);

                    //replace the urls
                    $this->actual_file_path =   $wph->functions->content_urls_replacement(  $domain_url . '/' . ltrim ( $this->file_path, '/' ) ,  $replacement_list );
                        
                    $this->actual_file_path =   str_replace( $domain_url, "", $this->actual_file_path );
                    $this->actual_file_path =   ltrim ( $this->actual_file_path , '/' );
                    if ( ! is_file ( ABSPATH .  $this->actual_file_path )  ||   ! file_exists ( ABSPATH .  $this->actual_file_path ) )
                        die();   
                    
                }
                
            
            function process_file_content()
                {
                    global $wph;
                    
                    $myfile = fopen( ABSPATH .  $this->actual_file_path , "r") or die("Unable to open file!");
                    $buffer   =   fread ( $myfile, filesize( ABSPATH .  $this->actual_file_path ) );
                    fclose($myfile);
                    
                    if (empty ( $buffer ) )
                        return;
                    
                    $processed_buffer   =   '';

                    switch ( $this->file_type ) 
                        {
                            case  'css' :
                                            $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                                            
                                            $option__css_combine_code    =   $wph->functions->get_site_module_saved_value('css_combine_code',  $wph->functions->get_blog_id_setting_to_use());
                                            if ( in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                                                $processed_buffer =   $WPH_module_general_css_combine->css_recipient_process( $buffer );
                                                else
                                                $processed_buffer =   $WPH_module_general_css_combine->_process_url_replacements( $buffer );  

                                            break;
                                            
                            case  'js' :
                                            $WPH_module_general_js_combine =   new WPH_module_general_js_combine();
                                            
                                            $option__js_combine_code    =   $wph->functions->get_site_module_saved_value('js_combine_code',  $wph->functions->get_blog_id_setting_to_use());
                                            if ( in_array( $option__js_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                                                $processed_buffer =   $WPH_module_general_js_combine->js_recipient_process( $buffer );
                                                else
                                                $processed_buffer =   $WPH_module_general_js_combine->_process_url_replacements( $buffer );  
                                            
                                            
                                            break;
                                            
                        }
                        
                    if ( empty ( $processed_buffer ) )
                        die();
                        
                    global $wp_filesystem;

                    if (empty($wp_filesystem)) 
                        {
                            require_once (ABSPATH . '/wp-admin/includes/file.php');
                            WP_Filesystem();
                        }   
                    
                    $wp_filesystem->put_contents( $this->environment->cache_path . $this->get_cached_file_name( $this->file_path ) , $processed_buffer , FS_CHMOD_FILE );
                    
                }
            
            function output_existing_file()
                {
                    $myfile = fopen( ABSPATH .  $this->actual_file_path , "r") or die("Unable to open file!");
                    $buffer   =   fread ( $myfile, filesize( ABSPATH .  $this->actual_file_path ) );
                    fclose($myfile);  
                    
                    if (empty ( $buffer ) )
                        return;
                        
                    $this->output_data( $buffer );
                }
                
            
            /**
            * Clean the file
            *     
            */
            function output_data( $buffer = '' )
                {
                    
                    
                    if ( empty ( $buffer ) )
                        {
                            $cached_file_path   =   $this->environment->cache_path . $this->get_cached_file_name( $this->file_path );
                            $myfile = fopen( $cached_file_path, "r" ) or die();
                            $buffer   =   fread ( $myfile, filesize( $cached_file_path ) );
                            fclose($myfile);
                            
                            header('Last-Modified: '.gmdate('D, d M Y H:i:s', filemtime( $cached_file_path )).' GMT', true);
                        }
                    
                    switch ( $this->file_type )
                        {
                            case 'js':
                                        header('Content-Type: application/javascript;');
                                        break;
                            case 'css':
                                        header('Content-Type: text/css;');
                                        break;
                        }
                    
                    $expires_offset = 31536000;
                    header('Expires: ' . gmdate( "D, d M Y H:i:s", time() + $expires_offset ) . ' GMT');
                    header("Cache-Control: public, max-age=$expires_offset");
                                        
                    echo $buffer;
                                        
                }
              
       
        }
    
    $action             =   isset($_GET['action'])              ?   filter_var ( $_GET['action'],               FILTER_SANITIZE_STRING)         :   '';
    $file_path          =   isset($_GET['file_path'])           ?   filter_var ( $_GET['file_path'],            FILTER_SANITIZE_STRING)         :   '';
        
    if( empty($action)   ||  empty($file_path) )
        die();
        
    $WPH_FileProcess  =   new WPH_Asset_PostProcessing( $action, $file_path );
    
    
?>