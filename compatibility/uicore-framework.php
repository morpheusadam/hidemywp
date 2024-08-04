<?php

    /**
    * Compatibility     : UiCore Framework
    * Last Checked at   :   5.0.5
    * Introduced at     : 5.0.5
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_uicore
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action('plugins_loaded',        array( $this,    '_reverse_urls') );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'uicore-framework/plugin.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                       
            function _reverse_urls()
                {
                    if ( ! isset ( $_GET['rest_route'] )    ||  $_GET['rest_route']   !=  '/uicore/v1/import-library/' )
                        return;
                                                
                    add_filter ( 'wp-hide/ignore_ob_start_callback', '__return_true' );
                }

           
        }
        
        
    new WPH_conflict_handle_uicore();


?>