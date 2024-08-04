<?php


    /**
    * Compatibility     : Asset CleanUp Pro: Page Speed Booster
    * Introduced at     :  1.1.7.6
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_wpacu
        {
            var $wph;
                            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'wpacu_html_source_after_optimization',             array( $this,   'process_buffer'), 999 );       
                    
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
                    
                    if( is_plugin_active( 'wp-asset-clean-up-pro/wpacu.php' ) || is_plugin_active( 'wp-asset-clean-up/wpacu.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            function process_buffer( $buffer )
                {
                         
                    //do replacements for this url
                    $buffer =   $this->wph->proces_html_buffer( $buffer );
                                           
                    return $buffer;
                    
                }
                
        
        
                
            function __general_js_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/asset-cleanup/' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
                
            
            function __general_css_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/asset-cleanup/' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
     
                            
        }


        new WPH_conflict_handle_wpacu();
        
?>