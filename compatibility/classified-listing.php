<?php

    /**
    * Compatibility     : Classified Listing – Classified ads & Business Directory Plugin
    * Introduced at     : 2.2.13
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_classified_listing
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'init',         array( $this , 'init' ), 99 );

                }                        
            
            static function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'classified-listing/classified-listing.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            function init()
                {
                    $options =   get_option('rtcl_advanced_settings');    
                    
                    if ( is_array( $options ) &&    isset ( $options['listing_form'] )  &&  ! empty ( $options['listing_form'] ) )
                        {
                            $listing_page   =   get_page    ( $options['listing_form'] );
                            if ( is_object( $listing_page ) &&  isset ( $listing_page->guid ) )
                                {
                                    $home_url   =   str_replace ( array('https:', 'http:'), "", get_home_url() );
                                    $home_url   =   trim ( $home_url, '/' );        

                                    $post_url   =   str_replace ( array('https:', 'http:'), "", get_permalink( $listing_page->ID ) );
                                    $post_url   =   str_replace ( $home_url, "", $post_url );
                                    $post_url   =   trim ( $post_url, '/' );
                                    
                                    if ( strpos( $_SERVER['REQUEST_URI'], $post_url )   !== FALSE  )
                                        {
                                            $this->wph->functions->remove_anonymous_object_filter('wp-hide/ob_start_callback',             'WPH_module_general_scripts', 'ob_start_callback');
                                        }
                                }
                        }   
                }
               
        }
        
    new WPH_conflict_handle_classified_listing();


?>