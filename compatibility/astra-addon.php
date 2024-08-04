<?php

    
    /**
    * Compatibility             :   Astra Pro
    * Introduced at             :   2.5.1
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

    class WPH_conflict_handle_astra_addon
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    if ( defined('DOING_AJAX') && DOING_AJAX &&  isset( $_POST['action'] ) &&  $_POST['action'] == 'ast_render_popup' )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                array( $this,    'wph_components_init'), 999, 2 );
                        }
                }                            

            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'astra-addon/astra-addon.php' ))
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
        }
        
    
    new WPH_conflict_handle_astra_addon();


?>