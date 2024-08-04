<?php


    /**
    * Compatibility     : TownHub Add-Ons
    * Introduced at     : 1.7.2
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_townhub_addons
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_action ( 'init' , array ( $this, 'init') );
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'townhub-add-ons/townhub-add-ons.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            function init()
                {
                    if ( is_user_logged_in() ||  ( isset( $_GET['listing_id'] ) &&    ! empty ( $_GET['listing_id'] ) )  )
                        {
                            $this->wph->functions->remove_anonymous_object_filter('wp-hide/ob_start_callback',             'WPH_module_general_scripts', 'ob_start_callback');
                        }
                }
        }


    new WPH_conflict_townhub_addons();
    
?>