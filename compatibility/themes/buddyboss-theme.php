<?php
    
    /**
    * Theme Compatibility   :   BuddyBoss Theme
    * Introduced at version :   1.4.1 
    */
        
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    
    class WPH_conflict_theme_buddyboss_theme
        {
                        
            var $wph;
            
            function __construct()
                {
        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    //add_filter( 'init', array ( 'WPH_conflict_theme_buddyboss_theme', 'setup_theme') );

                    add_filter(     'after_setup_theme', array( $this, 'after_setup_theme' ) );
                    
                }
                
            function after_setup_theme()
                {
                    remove_filter(  'login_redirect', 'buddyboss_redirect_previous_page', 10, 3 );
                    add_filter(     'login_redirect', array( $this, 'buddyboss_redirect_previous_page' ), 10, 3 );    
                }

            function buddyboss_redirect_previous_page( $redirect_to, $request, $user )
                {
                
                    if ( buddyboss_theme()->buddypress_helper()->is_active() ) {

                    $bp_pages = false;

                    // Check if Platform plugin is active.
                    if( function_exists('bp_get_option') ){
                        $bp_pages = bp_get_option( 'bp-pages' );
                    }

                    $activate_page_id = !empty( $bp_pages ) && isset( $bp_pages[ 'activate' ] ) ? $bp_pages[ 'activate' ] : null;

                    if ( (int) $activate_page_id <= 0 ) {
                        return $redirect_to;
                    }

                    $activate_page = get_post( $activate_page_id );

                    if ( empty( $activate_page ) || empty( $activate_page->post_name ) ) {
                        return $redirect_to;
                    }

                    $activate_page_slug = $activate_page->post_name;

                    if ( strpos( $request, '/' . $activate_page_slug ) !== false ) {
                        $redirect_to = home_url();
                    }
                }

                $request = wp_get_referer();

                if ( ! $request ) {
                    return $redirect_to;
                }

                // redirect for native mobile app
                if ( ! is_user_logged_in() && wp_is_mobile() ) {
                    $path = wp_parse_url( $request );

                    if ( isset( $path['query'] ) && ! empty( $path['query'] ) ) {
                        parse_str( $path['query'], $output );

                        $redirect_to = ( isset( $output ) && isset( $output['redirect_to'] ) && '' !== $output['redirect_to'] ) ? $output['redirect_to'] : $redirect_to;
                        return $redirect_to;
                    }
                }

                $req_parts          = explode( '/', $request );
                $req_part          = array_pop( $req_parts );
                $url_arr          = [];
                $url_query_string = [];
                
                $new_wp_login_php     =   $this->wph->functions->get_site_module_saved_value('new_wp_login_php',  $this->wph->functions->get_blog_id_setting_to_use());
                if ( empty ( $new_wp_login_php ) )
                    $new_wp_login_php   =   'wp-login.php';
                
                if ( strpos( $req_part, $new_wp_login_php ) === 0 ) {
                    $url_query_string = wp_parse_url( $request );

                    if ( isset( $url_query_string['query'] ) && ! empty( $url_query_string['query'] ) ) {
                        parse_str( $url_query_string['query'], $url_arr );
                        $redirect_to = ( isset( $url_arr ) && isset( $url_arr['redirect_to'] ) && '' !== $url_arr['redirect_to'] ) ? $url_arr['redirect_to'] : $redirect_to;

                        return $redirect_to;
                    } else {
                        return $redirect_to;
                    }
                }

                $request = str_replace( array( '?loggedout=true', '&loggedout=true' ), '', $request );

                return $request;
                
                
                }
                                    
        }
        
        
    new WPH_conflict_theme_buddyboss_theme();
    

?>