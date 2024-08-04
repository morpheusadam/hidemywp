<?php

    
    /**
    * Compatibility     : YITH WooCommerce Multi Vendor Premium
    * Introduced at     : 3.4.0
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_yith_wmvp
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    if ( count ( $_POST ) < 1 )
                        return;
                        
                    if ( ! isset($_POST['update_vendor_id'])) 
                        return;
                        
                    if ( ! isset($_POST['action'])   ||  strpos( $_POST['action'], '_admin_save_fields') ===  FALSE ) 
                        return;
                    
                    add_filter( 'wph/components/force_run_on_admin',  '__return_true' );
                    
                }                        
            
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'yith-woocommerce-multi-vendor-premium/init.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                       
            
            function smush_filter_generate_cdn_url( $src )
                {
                    
                    $src    =   $this->wph->functions->content_urls_replacement( $src,  $this->wph->functions->get_replacement_list() ); 
                       
                    return $src; 
                    
                }

           
        }
        
        
    new WPH_conflict_handle_yith_wmvp();


?>