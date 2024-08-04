<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_update
        {
            var $wph;
                                  
            function __construct()
                {
                    global $wph;
                    $this->wph          =   &$wph;
                    
                    $this->_run();
                }
                
                
            private function _run()
                {                    
                    
                    $version        =   get_site_option('wph_version');
                    if(empty($version))
                        $version    =   1;
                                                    
                    if (version_compare($version, WPH_CORE_VERSION, '<')) 
                        {
                            //keep track of flushed rules to avoid doing it multiple times
                            $_trigger_flush_rules   =   FALSE;
                            
                            
                            //update from the free version                                                  
                            if(version_compare($version, '1.0.3', '<'))
                                {
                                    //copy over the new mu-loader version
                                    WPH_functions::copy_mu_loader( TRUE );
                                    
                                    $version =   '1.0.3';
                                }
                                
                            if(version_compare($version, '1.0.4', '<'))
                                {
                                    //copy over the new mu-loader version
                                    WPH_functions::copy_mu_loader( TRUE );
                                    
                                    $version =   '1.0.4';
                                }
                                
                            if(version_compare($version, '1.3.4', '<'))
                                {
                                    //some environment variables format has changed, trigger a new set of rules
                                    $_trigger_flush_rules =   TRUE;
                                    
                                    $version =   '1.3.4';
                                }
                                
                            if(version_compare($version, '1.4.8.5', '<'))
                                {
                                    
                                    //do not run if not in admin
                                    if  ( ! is_admin() ) 
                                        {
                                            update_site_option('wph_version', $version);   
                                            return;
                                        }
                                    
                                    
                                    //attempt to import the values from 
                                    $blog_id_settings   =   $this->wph->functions->get_blog_id();
                    
                                    //$modules_settings   =   $this->functions->get_site_modules_settings( $blog_id_settings );
                                    $settings   =   $this->wph->functions->get_site_settings( $blog_id_settings  );
                            
                                    //disable certain options
                                    $css_class_replace  =   $settings['module_settings']['css_class_replace'];
                                    if  ( is_array ( $css_class_replace ) )
                                        {
                                            //clean-up the global *
                                            foreach ( $css_class_replace    as  $key    =>  $group )
                                                {
                                                    foreach  ( $group   as  $g_key   =>  $value)
                                                        {
                                                            $group[ $g_key ]    =   trim( $value, '*' );
                                                        }
                                                        
                                                    $css_class_replace[ $key ]  =   $group;
                                                }
                                                
                                            if ( ! is_array( $css_class_replace ) )
                                                $css_class_replace  =   array();
                                                
                                            $settings['module_settings']['html_css_js_replacements']    =   $css_class_replace;
                                            
                                            //save the new options
                                            $this->wph->functions->update_site_settings( $settings, $blog_id_settings );
                                            
                                        }                                    
                                    
                                    add_action('init', array( $this, 'cache_clear' ));
                                    
                                    $version =   '1.4.8.5';
                                }
                            
                            //check for triggered flush rules
                            if ( $_trigger_flush_rules  === TRUE )
                                {
                                    //on plugin inline code update
                                    if(isset($_GET['action'])   &&  $_GET['action']     ==  'activate-plugin')
                                        add_action('shutdown',        array($this,    'flush_rules') , -1);
                                        else
                                        add_action('wp_loaded',        array($this,    'flush_rules') , -1);
                                        
                                }
                                
                                
                            //always try to clear cache
                            //$this->wph->functions->cache_clear();
                            
                            //Always generate the environment file
                            //$this->wph->environment_check();

                            //save the last code version
                            $version =   WPH_CORE_VERSION;
                            update_site_option('wph_version', $version);
                                    
                        }
                    
                     
                }
            
 
            /**
            * Regenerate rewrite rules
            * 
            */
            function flush_rules()
                {
                    /** WordPress Misc Administration API */
                    require_once(ABSPATH . 'wp-admin/includes/misc.php');
                    
                    /** WordPress Administration File API */
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    
                    flush_rewrite_rules();
                    
                    $this->wph->functions->site_cache_clear();
                    
    
                }
                
    
    
            /**
            * Clear all caches
            * 
            */
            function cache_clear()
                {
                    global $wph;
                    
                    $wph->functions->cache_clear();   
                }
                
        }
        
        
?>