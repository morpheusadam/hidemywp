<?php

    /**
    * Compatibility     : TranslatePress - Multilingual
    * Introduced at     : 1.9.5
    * 
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_tp_multilingual
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    $this->wph  =   $wph;
                    
                    add_filter('login_url',             array($this,'login_url'), 998, 3 );
                    add_filter( 'trp_is_admin_link', array ( $this, 'trp_is_admin_link' ), 99, 4 );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'translatepress-multilingual/index.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                
            
            function trp_is_admin_link( $is_admin_link, $url, $admin_url, $wp_login_url )
                {
                    $new_wp_login_php       =   $this->wph->functions->get_site_module_saved_value('new_wp_login_php',  $this->wph->functions->get_blog_id_setting_to_use());
                    if ( empty ( $new_wp_login_php ) )
                        return $is_admin_link;
                        
                    $login_url              =   home_url($new_wp_login_php, 'login');
                    
                    $default_login_url      =   site_url( 'wp-login', 'login');
                    
                    if ( strpos( $url, $default_login_url ) !== false || strpos( $url, $new_wp_login_php ) !== false 
                        ||    strpos( $url, $admin_url ) !== false ){
                        $is_admin_link = true;
                    } else {
                        $is_admin_link = false;
                    }  
                    
                    return $is_admin_link;
                       
                }
            
                
            function login_url($login_url, $redirect, $force_reauth)
                {
                    //remove the default wp_login 'login_url' filter if in backtrace
                    if (  ! $this->wph->functions->check_backtrace_for_caller( array ( array ( 'handle_custom_links_and_forms', 'TRP_Translation_Render') , array ('wp_login_url', FALSE) ) ) )
                        return $login_url;
                    
                    $this->wph->functions->remove_anonymous_object_filter( 'login_url',             'WPH_module_admin_new_wp_login_php', 'login_url');
                    
                    return $login_url;   
                }
    
           
        }
        
        
    new WPH_conflict_handle_tp_multilingual();


?>