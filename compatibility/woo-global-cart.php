<?php

    /**
    * Compatibility     : WooGlobalCart
    * Introduced at     : 1.3.8
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_woogc
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action('plugins_loaded',        array( $this, 'run') , -1);    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'woo-global-cart/woo-global-cart.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            public function run()
                {   
                                        
                    add_filter ('woogc/on_shutdown/ob_buferring_output', array( $this, 'status_ob_buferring_output'), 10, 2);
                               
                }
                 
            function status_ob_buferring_output( $status, $ob_get_status )
                {
                    
                    if  ( is_array( $ob_get_status )    &&  ( $ob_get_status['name']  ==  'WPH::ob_start_callback'  ||  $ob_get_status['name']  ==  'WPH::ob_start_callback_late') )
                        {
                            $status =   FALSE;
                        }    
                    
                    return $status;    
                }
                            
        }

    new WPH_conflict_handle_woogc();

?>