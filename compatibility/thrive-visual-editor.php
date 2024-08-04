<?php


    /**
    * Compatibility     : Thrive Architect
    * Introduced at     : 2.5.9
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_thrive_visual_editor
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                                  
                    if ( isset( $_GET['tve'] )  &&  $_GET['tve']    ==  'true' )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                array( $this,    'wph_components_init'), 999, 2 );
                        }
                                        
                    add_filter( 'wph/components/components_run/ignore_field_id',array( $this,    'ignore_field_id'), 999, 3 );

                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'thrive-visual-editor/thrive-visual-editor.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
            
            function ignore_field_id( $ignore_field, $field_id, $saved_field_value )
                {
                    
                    if  ( in_array( $field_id, array( 'js_combine_code', 'css_combine_code' ) ) )
                        {
                            if ( isset( $_GET['tve'] )  &&  $_GET['tve']    ==  'true' )
                                {
                                    $ignore_field   =   TRUE;
                                }
                            
                        }
                    
                    return $ignore_field;
                    
                }
                
                
            function wph_components_init( $status, $component )
                {
                    if ( $component ==  'rewrite_default' )
                        return FALSE;
                        
                        
                    return $status;
                    
                }
    
        }
        
        
    new WPH_conflict_thrive_visual_editor();


?>