<?php

    
    /**
    * Compatibility     : Fusion Builder
    * Introduced at     : 1.4.2
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_fusion_builder
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                                        
                    add_action('wph/settings_changed',  array( $this,    'settings_changed'));
                    
                    add_filter ( 'wph/components/components_run/ignore_field_id', array ( $this, 'components_run_ignore_field_id' ), 10, 3 );
                    
                    add_filter ( 'wp-hide/ob_start_callback'                    ,   array ( $this, 'ob_start_callback' ) , 10, 1 );
                    
                    add_filter ( 'init'                                         ,   array ( $this, 'init' ) );
       
                                        
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'fusion-builder/fusion-builder.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
            
            function init()
                {
                    if ( isset ( $_GET['fb-edit'] ) ||  isset ( $_GET['builder'] ) )
                        {
                            $WPH_module_general_html    =   $this->wph->functions->return_component_instance( 'WPH_module_general_html' );
                            remove_filter('wp-hide/ob_start_callback', array( $WPH_module_general_html, 'remove_html_new_lines'));
                        }
                }
                
            function settings_changed()
                {
                    $fusion_cache = new Fusion_Cache();
                    $fusion_cache->reset_all_caches();
                }
                
                
                
            function components_run_ignore_field_id( $ignore, $field_id, $saved_field_value )
                {
                    if ( $field_id !=   'scripts_remove_id_attribute' )
                        return $ignore;
                    
                    if ( ! isset ( $_GET['fb-edit'] ) )
                        return $ignore;
                        
                    $ignore =   TRUE;
                    
                    return $ignore;
                }
      
            
            function ob_start_callback( $buffer )
                {
                    
                    $html_css_js_replacements    =   $this->wph->functions->get_site_module_saved_value('html_css_js_replacements',   $this->wph->functions->get_blog_id_setting_to_use());
                    
                    //fix the fusion replacement and the captcha fusionOnloadCallback 
                    foreach ( $html_css_js_replacements as $group )
                        {
                            if ( $group[0]  ==  'fusion' )
                                $buffer =   str_replace ( 'fusionOnloadCallback', $group[1] . 'OnloadCallback', $buffer );
                            
                        }
   
                    return $buffer; 
                }
                            
        }
        
    
    new WPH_conflict_fusion_builder();


?>