<?php


    /**
    * Compatibility     : WPML Multilingual CMS
    * Introduced at     : 4.3.12 
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_wpml
        {
            
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                                            
                    add_action('plugins_loaded',        array( $this, '_normalize_replacement_urls') , 0 );
                    
                    //when WPML and ELEMENTOR
                    //add_filter ( 'elementor_pro/frontend/localize_settings',    array ( $this, 'elementor_pro_frontend_localize_settings' ) );
                    
                    add_filter( 'rest_url',                     array ( $this, 'rest_url'), 999, 4 );
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'sitepress-multilingual-cms/sitepress.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }

                          
            
            /**
            * adjust the replacements
            *     
            */
            function _normalize_replacement_urls()
                {
                    global $sitepress;
                    
                    if (!$sitepress) 
                        return;
                    
                    $current_lang       = apply_filters( 'wpml_current_language', NULL );
                    $default_lang       = apply_filters('wpml_default_language', NULL );
                    $domain_per_lang    = $sitepress->get_setting( 'language_negotiation_type' ) == WPML_LANGUAGE_NEGOTIATION_TYPE_DOMAIN ? true : false;
                    if ($current_lang == $default_lang || $domain_per_lang)
                        return;
                    
                    if ( $sitepress->get_setting( 'language_negotiation_type' ) == WPML_LANGUAGE_NEGOTIATION_TYPE_PARAMETER )
                        {
                            $default_home_url   =   $sitepress->convert_url( $sitepress->get_wp_api()->get_home_url(), $default_lang );
                            $default_home_url   =   str_replace( array ( 'https:', 'http:' ), '', $default_home_url );
                            
                            $home_url   =    home_url();
                            $home_url   =   str_replace( array ( 'https:', 'http:' ), '', $home_url );
                            
                            if  ( $home_url ==  $default_home_url ) 
                                return;
                                
                            foreach ( $this->wph->urls_replacement  as  $priority   =>  $list )
                                {
                                    if ( count ( $list ) > 0 )
                                        {
                                            foreach ( $list as  $replaced   =>  $replacement )
                                                {
                                                    $_replacement   =   str_replace( trailingslashit ( $home_url ) , trailingslashit ( $default_home_url ) ,  $replacement );
                                                    if ( $_replacement != $replacement )
                                                        $this->wph->urls_replacement[$priority][$replaced]  =   $_replacement;
                                                }
                                        }
                                }   
                        }
                        else
                        {
                            $default_home_url   =   $sitepress->convert_url( $sitepress->get_wp_api()->get_home_url(), $default_lang );
                            $default_home_url   =   str_replace( array ( 'https:', 'http:' ), '', $default_home_url );
                            
                            foreach ( $this->wph->urls_replacement  as  $priority   =>  $list )
                                {
                                    if ( count ( $list ) > 0 )
                                        {
                                            foreach ( $list as  $replaced   =>  $replacement )
                                                {
                                                    $_replacement   =   str_replace( trailingslashit ( $default_home_url ) . $current_lang  .'/' , trailingslashit ( $default_home_url )    ,  $replacement );
                                                    if ( $_replacement != $replacement )
                                                        $this->wph->urls_replacement[$priority][$replaced]  =   $_replacement;
                                                }
                                        }
                                }
                        }
                    
                }
                
                
            function elementor_pro_frontend_localize_settings( $locale_settings )
                {
                                        
                    if ( is_array ( $locale_settings )  &&  isset ( $locale_settings['urls'] )  &&  isset ( $locale_settings['urls']['rest'] ) )
                        {
                            $locale_settings['urls']['rest']    =   trailingslashit ( site_url() ) . rest_get_url_prefix();
                        }
                       
                    return $locale_settings;    
                }
                
               
            function rest_url( $url, $path, $blog_id, $scheme )
                {
                    if ( empty( $path ) ) {
                        $path = '/';
                    }

                    $path = '/' . ltrim( $path, '/' );

                    if ( is_multisite() && get_blog_option( $blog_id, 'permalink_structure' ) || get_option( 'permalink_structure' ) ) {
                        global $wp_rewrite;

                        if ( $wp_rewrite->using_index_permalinks() ) {
                            $url = get_home_url( $blog_id, $wp_rewrite->index . '/' . rest_get_url_prefix(), $scheme );
                        } else {
                            $url = get_home_url( $blog_id, rest_get_url_prefix(), $scheme );
                        }

                        $url .= $path;
                    } else {
                        $url = trailingslashit( get_home_url( $blog_id, '', $scheme ) );
                        /*
                         * nginx only allows HTTP/1.0 methods when redirecting from / to /index.php.
                         * To work around this, we manually add index.php to the URL, avoiding the redirect.
                         */
                        if ( ! str_ends_with( $url, 'index.php' ) ) {
                            $url .= 'index.php';
                        }

                        $url = add_query_arg( 'rest_route', $path, $url );
                    }

                    if ( is_ssl() && isset( $_SERVER['SERVER_NAME'] ) ) {
                        // If the current host is the same as the REST URL host, force the REST URL scheme to HTTPS.
                        if ( parse_url( get_home_url( $blog_id ), PHP_URL_HOST ) === $_SERVER['SERVER_NAME'] ) {
                            $url = set_url_scheme( $url, 'https' );
                        }
                    }

                    if ( is_admin() && force_ssl_admin() ) {
                        /*
                         * In this situation the home URL may be http:, and `is_ssl()` may be false,
                         * but the admin is served over https: (one way or another), so REST API usage
                         * will be blocked by browsers unless it is also served over HTTPS.
                         */
                        $url = set_url_scheme( $url, 'https' );
                    }
                    
                    return $url;
                } 
                
        }
        
    
    new WPH_conflict_handle_wpml();    
        
?>