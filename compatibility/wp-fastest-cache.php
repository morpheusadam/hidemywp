<?php


    /**
    * Compatibility     : WP Fastest Cache
    * Introduced at     : 0.9.0.6 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_wp_fastest_cache
        {
                        
            var $wph;
                            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_filter( 'wpfc_buffer_callback_filter',                      array( $this , 'wpfc_cache_callback_filter' ), 99, 2 );   
                    
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
                    
                    if(is_plugin_active( 'wp-fastest-cache/wpFastestCache.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function wpfc_cache_callback_filter( $buffer, $extension )
                {
                                        
                    switch ( $extension ) 
                        {
                            case  'css' :
                                            $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                                            
                                            $option__css_combine_code    =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                                            if ( in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                                                $buffer =   $WPH_module_general_css_combine->css_recipient_process( $buffer );
                                                else
                                                $buffer =   $WPH_module_general_css_combine->_process_url_replacements( $buffer );  

                                            break;
                                            
                            case  'js' :
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
                    
                                        
                    return $buffer;
                        
                }
                
                
                
            function __general_js_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/wpfc' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
                
            function __general_css_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/wpfc' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
   
        }
        
   
    new WPH_conflict_handle_wp_fastest_cache();


?>