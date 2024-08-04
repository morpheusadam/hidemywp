<?php

    
    /**
    * Compatibility     : WCFM - WooCommerce Frontend Manager
    * Introduced at     : 6.5.1
    * Latest chcked on  : 6.7.0
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_wc_frontend_manager
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'wcfm_attachment_url',                    array( $this, 'wcfm_attachment_url'), 1 );
                    
                    add_filter ( 'wp'   , array ( $this, 'init' ) ); 
                    
                                        
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'wc-frontend-manager/wc_frontend_manager.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                       
            function wcfm_attachment_url( $attachment_url )
                {
                    
                    $replacement_list   =   $this->wph->functions->get_replacement_list();
                    
                    //reverse the list
                    $replacement_list   =   array_flip($replacement_list);
                    
                    $attachment_url         =   $this->wph->functions->content_urls_replacement( $attachment_url,  $replacement_list );
                    
                    return $attachment_url;
                    
                }
                
                
            function init ( )
                {
                    global $post;
                    
                    if ( ! is_object ( $post )  || strpos( $post->post_content, 'wc_frontend_manage' ) ===  FALSE )
                        return;
                    
                    add_filter ('wph/components/css_combine_code',      '__return_false');
                    add_filter ('wph/components/js_combine_code',       '__return_false');
                    
                    add_filter ('wph/components/_init/',                array( $this,    'wph_components_init'), 999, 2 );  
                    
                    $this->wph->functions->remove_anonymous_object_filter('wp-hide/ob_start_callback',             'WPH_module_general_scripts', 'ob_start_callback');
                }

           
        }
        
        
    new WPH_conflict_handle_wc_frontend_manager();


?>