<?php


    /**
    * Compatibility     : Easy Digital Downloads
    * Introduced at     : 
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_edd
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_filter( 'edd_start_session', array( $this , 'edd_start_session' ), -1 );   
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'easy-digital-downloads/easy-digital-downloads.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function edd_start_session( $start_session )
                {
                                        
                    $admin_url     =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use());
                    if (empty(  $admin_url ))
                        return $start_session;  
                        
                    $start_session = true;

                    if( ! empty( $_SERVER[ 'REQUEST_URI' ] ) ) {

                        $blacklist = EDD()->session->get_blacklist();
                        $uri       = ltrim( $_SERVER[ 'REQUEST_URI' ], '/' );
                        $uri       = untrailingslashit( $uri );

                        if( in_array( $uri, $blacklist ) ) {
                            $start_session = false;
                        }

                        if( false !== strpos( $uri, 'feed=' ) ) {
                            $start_session = false;
                        }

                        if( is_admin() && false === strpos( $uri, $admin_url . '/admin-ajax.php' ) ) {
                            // We do not want to start sessions in the admin unless we're processing an ajax request
                            $start_session = false;
                        }

                        if( false !== strpos( $uri, 'wp_scrape_key' ) ) {
                            // Starting sessions while saving the file editor can break the save process, so don't start
                            $start_session = false;
                        }

                    }
                    
                    return $start_session;
                        
                }
   
        }
        
    new WPH_conflict_handle_edd();


?>