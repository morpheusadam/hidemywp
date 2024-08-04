<?php


    /**
    * Compatibility     : Elementor
    * Introduced at     : 2.5.16
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    use Elementor\Core\Files\Manager as Files_Manager;
    
    class WPH_conflict_elementor
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action( 'wph/settings_changed',                             array( $this,    'settings_changed') );
                    
                    //change any internal urls
                    //add_action( 'elementor/element/parse_css',  array( 'WPH_conflict_elementor',    'elementor_element_parse_css') ); 
                    
                    if ( isset( $_GET['elementor-preview'] ) )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                    array( $this,    'wph_components_init'), 999, 2 );
                        }
                        
                    if ( isset( $_GET['elementor_library'] ) || strpos( $_SERVER['REQUEST_URI'], 'template-library/templates' ) !== FALSE )                    
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                    array( $this,    'wph_components_init'), 999, 2 );
                        }
                        
                    if ( isset( $_POST['action'] )  &&  ( $_POST['action']    ==  'elementor_ajax'  ||  $_POST['action']    ==  'heartbeat'  ) )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                    array( $this,    'wph_components_init'), 999, 2 );
                        }
                        
                    add_filter( 'wph/components/components_run/ignore_field_id',    array( $this,    'ignore_field_id'), 999, 3 );
                    
                    //filter the urls of the outputed widget content since there's no way to catch the outrputed buffer, elementor does this on it's own..
                    add_filter( 'elementor/widget/render_content',                  array( $this, 'elementor_widget_render_content'), 999, 2);
                    
                    add_filter ('init' , array ( $this, 'init') );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'elementor/elementor.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            function settings_changed()
                {
                    
                    $files_manager = new Files_Manager();
                    $files_manager->clear_cache();
                    
                }
                
            
            function ignore_field_id( $ignore_field, $field_id, $saved_field_value )
                {
                    
                    if  ( in_array( $field_id, array( 'js_combine_code', 'css_combine_code' ) ) )
                        {
                            if  (  isset( $_GET['elementor-preview'] ) )
                                {
                                    $ignore_field   =   TRUE;
                                }
                            
                        }
                    
                    return $ignore_field;
                    
                }
                
            function elementor_widget_render_content( $widget_content, $class )
                {
                    
                    //do replacements for this url
                    $widget_content    =   $this->wph->functions->content_urls_replacement($widget_content,  $this->wph->functions->get_replacement_list() );                    
                                       
                    return $widget_content;
                }
                
                
            function wph_components_init( $status, $component )
                {
                    if ( $component ==  'rewrite_default' )
                        return FALSE;
                        
                        
                    return $status;
                    
                }
                
                
            function init ( )
                {
                    
                    
                    //disable the "Remove ID from script tag" optio when in editor
                    if ( isset ( $_GET['action'] )  &&  $_GET['action'] ==  'elementor'  )
                        {                    
                            $this->wph->functions->remove_anonymous_object_filter( 'wp-hide/ob_start_callback',             'WPH_module_general_scripts', 'ob_start_callback');  
                        }
                }
                      
        }


    new WPH_conflict_elementor();
    
?>