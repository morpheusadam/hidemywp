<?php


    /**
    * Compatibility     : Swift Performance
    * Introduced at     : 
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_swift_performance
        {
            
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    /**
                    * Update the urls, in case the cache plugin add more assets
                    */
                    add_filter( 'swift_performance_buffer',         array( $this , 'swift_performance_buffer' ), 99 );
                    
                    /**
                    * Process the Cache CSS content
                    */
                    add_filter( 'swift_performance_css_content',    array( $this , 'swift_performance_css_content' ), 1, 2 );
                    
                    /**
                    * Process the Cache JS content
                    */
                    add_filter( 'swift_performance_js_content',     array( $this , 'swift_performance_js_content' ), 1, 2 );
                    
                    
                    //ignore the files which where cached through the Cache plugin, as they where already processed through the filer wpfc_buffer_callback_filter
                    $js_postprocessing_type    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',   $this->wph->functions->get_blog_id_setting_to_use());
                    $css_postprocessing_type   =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    //ignore the files which where cached through the Cache plugin, as they where already processed
                    if ( in_array ( $js_postprocessing_type, array ( 'yes', 'combine-encode-inline' ) ) )
                        add_filter( 'wp-hide/module/general_js_combine/ignore_file' ,   array ( $this, '__general_js_combine_ignore_file' ), 99, 2 );
                    if ( in_array ( $css_postprocessing_type, array ( 'yes', 'combine-encode-inline' ) ) )
                        add_filter( 'wp-hide/module/general_css_combine/ignore_file' ,  array ( $this, '__general_css_combine_ignore_file' ), 99, 2 );
                    
                    //ignore critical css
                    add_filter( 'wp-hide/module/general_css_variables_replace/placeholder_ignore_css',  array ( $this, '__general__placeholder_ignore_css' ), 99, 3 );
                    
                    //process the critical css
                    add_filter( 'swift_performance_critical_css_content',     array( $this , 'swift_performance_critical_css_content' ),1 );
                                        
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'swift-performance/performance.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function swift_performance_buffer( $buffer )
                {                    
                    if  ( $this->wph->ob_callback_late )
                        return $buffer;
                        
                    //do replacements for this url
                    $buffer =   $this->wph->proces_html_buffer( $buffer );
                    
                    $this->wph->ob_callback_late =   TRUE;
                       
                    return $buffer;
                        
                }
                
                
            function swift_performance_css_content( $buffer, $key )
                {
                    
                    $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                                            
                    $option__css_combine_code    =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    if ( in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                        $buffer =   $WPH_module_general_css_combine->css_recipient_process( $buffer );
                        else
                        $buffer =   $WPH_module_general_css_combine->_process_url_replacements( $buffer );  
                    
                    return $buffer;   
                }
                
            function swift_performance_js_content( $content )
                {
                    
                    $WPH_module_general_js_combine =   new WPH_module_general_js_combine();
                                            
                    $option__js_combine_code    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    if ( in_array( $option__js_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                        $content =   $WPH_module_general_js_combine->js_recipient_process( $content );
                        else
                        $content =   $WPH_module_general_js_combine->_process_url_replacements( $content );
                     
                    return $content;   
                }
                
                
            function __general_js_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/afrave-speed/' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
                
            function __general_css_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/afrave-speed/' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
                
            
            function __general__placeholder_ignore_css( $ignode, $code, $type )
                {
                    
                    if ( preg_match( '#<style[^>]*(critical)#i', $code ) > 0 )
                        $ignore =   TRUE;
                    
                    return $ignore;   
                }
                
            
            function swift_performance_critical_css_content( $critical_css )
                {
                    
                    $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                                            
                    $option__css_combine_code    =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    if ( in_array( $option__css_combine_code,   array( 'yes', 'in-place' ) ) )
                        $critical_css =   $WPH_module_general_css_combine->css_recipient_process( $critical_css );
                        else
                        $critical_css =   $WPH_module_general_css_combine->_process_url_replacements( $critical_css ); 
                           
                    return $critical_css;
                       
                }
                            
   
        }
        
    new WPH_conflict_handle_swift_performance();


?>