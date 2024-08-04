<?php
/*
* Plugin Name: WP Hide & Security Enhancer PRO
* Plugin URI: https://www.wp-hide.com/
* Description: Hide and increase Security for your WordPress website instance using smart techniques. No files are changed on your server.
* Author: Nsp Code
* Author URI: https://www.wp-hide.com/ 
* Version: 6.8.1
* Text Domain: wp-hide-security-enhancer
* Domain Path: /languages/ 
* Network: true
*/
           
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
	
	update_site_option('wph_licence', [
        'key'               =>  'xxxxxxxxxx',
        'last_check'        =>  0,
        'licence_status'    =>  'active',
        'licence_expire'    =>  date( 'Y-m-d', time() + 5 * 365 * 24 * 60 * 60 ),
    ]);
    
    //if mu-plugins component not being loaded trigger a later init
    if ( !defined( 'WPH_PATH' ) )
        {
                        
            define('WPH_PATH',              plugin_dir_path(__FILE__));
            
            include_once(WPH_PATH . '/include/wph.class.php');
            include_once(WPH_PATH . '/include/functions.class.php');
            
            include_once(WPH_PATH . '/include/module.class.php');
            include_once(WPH_PATH . '/include/module.component.class.php');
            
            //attempt to copy over the loader to mu-plugins which will be used starting next loading
            WPH_functions::copy_mu_loader();
            
            global $wph;
            $wph    =   new WPH();
            $wph->init();
            
            /**
            * Early Turn ON buffering to allow a callback
            * 
            */
            ob_start(array($wph, 'ob_start_callback'));
            
        }
    
          
    //load language files
    add_action( 'plugins_loaded', 'WPH_load_textdomain'); 
    function WPH_load_textdomain() 
        {
            load_plugin_textdomain('wp-hide-security-enhancer', FALSE, dirname( plugin_basename( __FILE__ ) ) . '/languages');
        }
    
    
    register_activation_hook(   __FILE__, 'WPH_activated');
    register_deactivation_hook( __FILE__, 'WPH_deactivated');

    function WPH_activated($network_wide) 
        {
            
            do_action('wph/settings_changed');     
            
            global $wph;
            
            //check if permalinks where saved
            $wph->custom_permalinks_applied   =   $wph->functions->rewrite_rules_applied();
            
            //reprocess components if the permalinks where applied
            if($wph->custom_permalinks_applied   === TRUE)
                {
                    $wph->_modules_components_run();
                    
                    //re-do the settings saves witht the components replacements in place
                    do_action('wph/settings_changed');
                }
                
            $version =   WPH_CORE_VERSION;
            update_site_option('wph_version', $version);
            
            //reset the wph-previous-options-list to be used
            if ( is_multisite() )
                update_site_option( 'wph-previous-options-list' , array() );
                else
                update_option( 'wph-previous-options-list' , array() );
            
        }

    function WPH_deactivated() 
        {
            
            global $wph;
            
            $wph->uninstall =   TRUE;
            do_action('wph/settings_changed');
            
            //replace the mu-loader
            WPH_functions::unlink_mu_loader();
            WPH_functions::clean_with_markers( WPH_functions::get_wp_config_path() , 'WP Hide & Security Enhancer' );
            
            delete_option( 'wph-previous-login-url' );
            
            wp_clear_scheduled_hook('WPH_event_cache');
            
        }
    
        
?>