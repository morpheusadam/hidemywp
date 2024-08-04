<?php


    /**
    * Compatibility     : Dokan
    * Introduced at     : 3.9.4
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_dokan
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    $current_page_slug_raw      =   $_SERVER['REQUEST_URI'];
                    
                    if ( empty ( $current_page_slug_raw ) )
                        return;
                        
                    $current_page_slug_parts    =   explode ( '/', $current_page_slug_raw );
                    $current_page_slug_parts    =   array_filter ( $current_page_slug_parts );
                    $current_page_slug_parts    =   array_values ( $current_page_slug_parts );
                    $current_page_slug  =   isset ( $current_page_slug_parts[0] ) ? $current_page_slug_parts[0] :   ''    ;
                    if ( empty ( $current_page_slug ) )
                        return;
                        
                    global $wpdb;
                    $mysql_query    =   $wpdb->prepare( "SELECT post_content FROM " . $wpdb->posts . " WHERE `post_name`    = %s  ", sanitize_title ( $current_page_slug ) );
                    $post_content   =   $wpdb->get_var ( $mysql_query );
                    
                    if ( stripos( $post_content, '[dokan-dashboard]' ) !== FALSE )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                    array( $this,    'wph_components_init'), 999, 2 );
                            
                            add_filter( 'wph/components/components_run/ignore_component',    array( $this,    'ignore_field_id'), 999, 4 );
                        }

                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'dokan-lite/dokan.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
  
                
            function wph_components_init( $status, $component )
                {
                    if ( $component ==  'rewrite_default' )
                        return FALSE;
                        
                        
                    return $status;
                    
                }
                
                
            function ignore_field_id( $ignore_field, $component_id, $saved_field_value, $_class_instance )
                {
                    
                    if  ( in_array( $component_id, array( 'scripts_remove_id_attribute' ) ) )
                        {
                            $ignore_field   =   TRUE;
                        }
                    
                    return $ignore_field;
                    
                }
            
           
        }
        
        
    new WPH_conflict_handle_dokan();


?>