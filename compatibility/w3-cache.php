<?php

    
    /**
    * Compatibility     : W3 Cache
    * Introduced at     : 0.9.0.6 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_w3_cache
        {
            
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action('plugins_loaded',                            array( $this, 'pagecache') , -1);
                    
                    add_filter( 'w3tc_filename_to_url',                     array( $this, 'w3tc_filename_to_url') , -1);
                                        
                    add_filter( 'w3tc_minify_file_handler_minify_options',  array( $this, 'w3tc_minify_file_handler_minify_options') );
                    
                    add_filter( 'w3tc_uri_cdn_uri',                         array( $this, 'w3tc_uri_cdn_uri') );
                    
                    //ignore the files which where cached through the Cache plugin, as they where already processed through the filer wpfc_buffer_callback_filter
                    $js_postprocessing_type    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',   $this->wph->functions->get_blog_id_setting_to_use());
                    $css_postprocessing_type   =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    //ignore the files which where cached through the Cache plugin, as they where already processed
                    if ( in_array ( $js_postprocessing_type, array ( 'yes', 'combine-encode-inline' ) ) )
                        add_filter( 'wp-hide/module/general_js_combine/ignore_file' ,   array ( $this, '__general_js_combine_ignore_file' ), 99, 2 );
                    if ( in_array ( $css_postprocessing_type, array ( 'yes', 'combine-encode-inline' ) ) )
                        add_filter( 'wp-hide/module/general_css_combine/ignore_file' ,  array ( $this, '__general_css_combine_ignore_file' ), 99, 2 );
                                        
                }                        
            
            function is_plugin_active()
                {
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'w3-total-cache/w3-total-cache.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function pagecache()
                {   
                    
                    if ( preg_match( '/\/cache\/minify\/\w+\.(?:css|js)|\/\?w3tc_minify/i', $_SERVER['REQUEST_URI'] ) )
                        {
                            $this->wph->ob_callback_late =   TRUE;
                            return;
                        }
                        
                    //check if there's a pagecache callback
                    if(isset($GLOBALS['_w3tc_ob_callbacks'])    &&  isset($GLOBALS['_w3tc_ob_callbacks']['pagecache']))
                        {
                            $GLOBALS['WPH_w3tc_ob_callbacks']['pagecache'] =   $GLOBALS['_w3tc_ob_callbacks']['pagecache'];
                            
                            //hijackthe callback
                            $GLOBALS['_w3tc_ob_callbacks']['pagecache'] =   array( $this, 'pagecache_callback');   
                        }
                               
                }
                
            function pagecache_callback( $buffer )
                {
                    if  ( $this->wph->ob_callback_late )
                        return $buffer;
                        
                    //applay the replacements
                    $buffer  =   $this->wph->proces_html_buffer( $buffer );
                    
                    $this->wph->ob_callback_late =   TRUE;
                    
                    //allow the W3-Cache to continur the initial callback
                    $callback = $GLOBALS['WPH_w3tc_ob_callbacks']['pagecache'];
                    if (is_callable($callback)) 
                        {
                            $buffer = call_user_func( $callback, $buffer);
                        }
                    
                    return $buffer;   
                }
                
                
            function w3tc_filename_to_url( $url )
                {
                    
                    if ( is_admin() )
                        return ( $url );
                    
                    global $wph;
                    
                    //do replacements for this url
                    $url    =   $wph->functions->content_urls_replacement($url,  $wph->functions->get_replacement_list() );
                       
                    return $url;   
                }
                
                
            function w3tc_minify_file_handler_minify_options( $serve_options)
                {
                    $serve_options['postprocessor'] =   array( $this, 'w3tc_process_content' );
                    
                    return $serve_options;
                }
                
            function w3tc_process_content( $buffer, $type )
                {
                    switch ( $type ) 
                        {
                            case                        'css' :
                            case                    'text/css':
                                                                $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                                                                
                                                                $option__css_combine_code    =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                                                                if ( in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                                                                    $buffer =   $WPH_module_general_css_combine->css_recipient_process( $buffer );
                                                                    else
                                                                    $buffer =   $WPH_module_general_css_combine->_process_url_replacements( $buffer );  

                                                                break;
                            
                            case    'application/x-javascript':                
                            case                         'js' :
                                                                $WPH_module_general_js_combine =   new WPH_module_general_js_combine();
                                                                
                                                                $option__js_combine_code    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                                                                if ( in_array( $option__js_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                                                                    $buffer =   $WPH_module_general_js_combine->js_recipient_process( $buffer );
                                                                    else
                                                                    $buffer =   $WPH_module_general_js_combine->_process_url_replacements( $buffer );  
                                                                
                                                                
                                                                break;   
                            
                            default:
                                            
                                                                $buffer =   $this->wph->proces_html_buffer( $buffer );
                                                                                    
                                                                break;        
                        }
                        
                        
                    if ( preg_match( '/\/cache\/minify\/\w+\.(?:css|js)|\/\?w3tc_minify/i', $_SERVER['REQUEST_URI'] ) )
                        {
                            $this->wph->ob_callback_late =   TRUE;
                        }
                                
                    return $buffer;   
                }
                
                
            
            function __general_js_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/minify' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
            
            
            function __general_css_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/minify' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
                
                
            function w3tc_uri_cdn_uri( $remote_uri )
                {
                    $replacement_list   =   $this->wph->functions->get_replacement_list();
                    
                    $home_url   =   str_replace ( array('https:', 'http:'), "", get_home_url() );
                    $home_url   =   trim ( $home_url, '/' );
                    $home_url   .=  '/';
                    
                    foreach ( $replacement_list as  $replace    =>  $replacement )
                        {
                            $_replace        =   str_replace ( array ( "http://", "https://", $home_url ) , "", $replace );
                            $_replacement    =   str_replace ( array ( "http://", "https://", $home_url ) , "", $replacement );
                            unset ( $replacement_list[$replace] );
                            $replacement_list[ $_replace ] =    $_replacement;   
                        }
                    $remote_uri =   str_ireplace (   array_keys ( $replacement_list ),    array_values ( $replacement_list ), $remote_uri );
                    
                    return $remote_uri;
                }
                
        }

        
    new WPH_conflict_handle_w3_cache();

?>