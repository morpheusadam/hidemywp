<?php


    /**
    * Compatibility     : WPBakery Page Builder / JS Composer
    * Introduced at     : 2.5.16
    * Last Checked      : 7.0
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_js_composer
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    $_found_action  =   FALSE;
                    
                    if ( ( isset( $_GET['vc_editable'] ) )  ||   (  isset( $_POST['action'] )  &&  ( in_array ( $_POST['action'], array ( 'vc_edit_form', 'vc_inline' ) )   ||  strpos ( $_POST['action'], 'vc_get_' )  === 0 ) ) )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                array( $this,    'wph_components_init'), 999, 2 );
                            
                            $_found_action  =   TRUE;
                        }
                                        
                    add_filter( 'wph/components/components_run/ignore_field_id',array( $this,    'ignore_field_id'), 999, 3 );
                    
                    if ( $_found_action ) 
                        add_action ( 'wp-hide/modules_components_run/completed', array ( $this, '_modules_components_run_completed' ) );

                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if ( is_plugin_active ( 'js_composer/js_composer.php' )  ||  is_plugin_active ( 'uncode-js_composer/js_composer.php' ) ||  is_plugin_active ( 'js_composer_salient/js_composer.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                
            
            function ignore_field_id( $ignore_field, $field_id, $saved_field_value )
                {
                    
                    if  ( in_array( $field_id, array( 'js_combine_code', 'css_combine_code' ) ) )
                        {
                            if  (  isset( $_GET['vc_editable'] )    &&  isset( $_GET['vc_post_id'] ) )
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
                
                
            function _modules_components_run_completed()
                {
                    $this->wph->functions->remove_anonymous_object_filter('wp-hide/ob_start_callback',             'WPH_module_general_scripts', 'ob_start_callback');
                }
    
        }
        
        
    new WPH_conflict_js_composer();


?>