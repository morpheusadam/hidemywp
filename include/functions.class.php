<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_functions
        {
            var $wph;
                                  
            function __construct()
                {
                    global $wph;
                    $this->wph          =   &$wph;
                }
    
                
            function get_module_component_default_setting()
                {
                    $defaults   = array (
                                            'type'                      =>  'component',
                                            'id'                        =>  '',
                                            'visible'                   =>  TRUE,
                                            'label'                     =>  '',
                                            'description'               =>  '',
                                            'value_description'         =>  '',
                                            'input_type'                =>  'text',
                                            'default_value'             =>  '',
                                            'sanitize_type'             =>  array('sanitize_title'),
                                            
                                            'help'                      =>  FALSE,
                                            'advanced_option'           =>  FALSE,
                                            
                                            'options_pre'               =>  '',
                                            'options'                   =>  array(),
                                            'options_post'              =>  '',                                            
                                            
                                            'interface_help_split'      =>  TRUE,
                                            
                                            'require_save'              =>  TRUE,
                                            
                                            //callback function when components run. Default being set for _init_{$field_id}
                                            'callback'                  =>  '',
                                            //callback function to return the rewrite code, Default being set for _callback_saved_{$field_id}
                                            'callback_saved'            =>  '',
                                            //PassThrough any additional arguments                                            
                                            'callback_arguments'         =>  array(),
                                            
                                            
                                            //conditional to render html for module component option
                                            'display_conditions'        =>  array(),
                                            
                                            //custom html render content for this module component option
                                            'module_option_html_render' =>  '',
                                            
                                            //custom processing (interface save) for this module component option
                                            'module_option_processing' =>  '',
                                            
                                            //processing order, lower means it will be processed earlier
                                            'processing_order'          =>  10,
                                            
                                            'require_save'              =>  TRUE
                                        );   
                    
                    return $defaults;
                }
            
            
            /**
            * Filter module comonent settings (set-up), by removing splits ( if $strip_splits ), and fill in default values for settings with empty data
            *     
            * @param mixed $module_settings
            * @param mixed $strip_splits
            */
            function filter_settings($module_settings, $strip_splits    =   FALSE)
                {
                    if(!is_array($module_settings)  || count($module_settings) < 1)
                        return $module_settings;
                    
                    $defaults   =   $this->get_module_component_default_setting();
                    
                    foreach($module_settings    as  $key    =>  $module_setting)
                        {
                            if(isset($module_setting['type'])   &&  $module_setting['type'] ==  'split')
                                {
                                    if($strip_splits    === TRUE)
                                        unset($module_settings[$key]);
                                        
                                    continue;
                                }
                            
                            $module_setting   =   wp_parse_args( $module_setting, $defaults );
                            
                            if ( ! isset ( $module_setting['rewrite_processing_order'] ) )
                                $module_setting['rewrite_processing_order'] =   $module_setting['processing_order'];
                            
                            switch($module_setting['input_type'])
                                {
                                    case    'text' :
                                                        $defaults_type   = array (
                                                                                'placeholder'                =>  '',
                                                                            );
                                                        $module_setting   =   wp_parse_args( $module_setting, $defaults_type );
                                                        
                                                        break;   
                                    
                                    
                                }
       
                            $module_settings[$key]  =   $module_setting;
                        }
                    
                    $module_settings    =   array_values($module_settings);
                    
                    return $module_settings;
                    
                }
            
            
            /**
            * Attempt to copy the mu loader within mu-plugins folder
            * 
            */
            static function copy_mu_loader( $force_overwrite    =   FALSE   )
                {
                    
                    //check if mu-plugins folder exists
                    if(! is_dir( WPMU_PLUGIN_DIR ))
                        {
                            if (! wp_mkdir_p( WPMU_PLUGIN_DIR ) )
                                return;
                        }
                    
                    //check if file actually exists already
                    if( !   $force_overwrite    )
                        {
                            if( file_exists(WPMU_PLUGIN_DIR . '/wp-hide-loader.php' ))
                                return;
                        }
                        
                    //attempt to copy the file
                    @copy( WP_PLUGIN_DIR . '/wp-hide-security-enhancer-pro/mu-loader/wp-hide-loader.php', WPMU_PLUGIN_DIR . '/wp-hide-loader.php' );
                }
                
            
            /**
            * Attempt to remove the mu loader
            *     
            */
            static function unlink_mu_loader()
                {
                    //check if file actually exists already
                    if( !file_exists(WPMU_PLUGIN_DIR . '/wp-hide-loader.php' ))
                        return;
                        
                    //attempt to copy the file
                    @unlink ( WPMU_PLUGIN_DIR . '/wp-hide-loader.php' );
                }
                
            
            
            /**
            * Return the wp-config.php path depending on WordPress set-up type
            * Some WordPress installs might have wp-config file outside root directory. one level up
            * 
            */
            static public function get_wp_config_path()
                {
                    if ( file_exists( ABSPATH . 'wp-config.php' ) ) 
                        {
                            return ( ABSPATH . 'wp-config.php' );

                        } 
                    elseif ( @file_exists( dirname( ABSPATH ) . '/wp-config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/wp-settings.php' ) ) 
                        {
                            return ( dirname( ABSPATH ) . '/wp-config.php' );
                        }   
                }
                
                
            /**
            * Check if the required lines exists within wp_config.php
            * 
            * @param mixed $update
            */
            function check_wp_config(  $update    =   TRUE )
                {
                    
                    if ( defined('WPH_WPCONFIG_LOADER') &&  WPH_WPCONFIG_LOADER === TRUE )
                        return TRUE;
                        
                    $existing_data  =   $this->extract_from_markers( $this->get_wp_config_path() , 'WP Hide & Security Enhancer');
                        
                    if (  count( $existing_data )    <   1  ||  count(array_diff($existing_data, $this->get_wp_config_data() )) > 0 )
                        {
                            if ( $update    )
                                {
                                    $this->clean_with_markers( $this->get_wp_config_path(), 'WP Hide & Security Enhancer' );
                                    $args   =   array(
                                                        'marker'            =>  'WP Hide & Security Enhancer',
                                                        'insertion'         =>  $this->get_wp_config_data(),
                                                        'before_marker'     =>  "if ( ! defined( 'ABSPATH' ) ) {",
                                                        'before_offset'     =>  0,
                                                        
                                                        'after_marker'      =>  "<?php"
                                                        );
                                    $status =   $this->insert_with_markers( $this->get_wp_config_path(),    $args );
                                    
                                    return $status;
                                }
                                else
                                return FALSE;
                        }
                        
                    return TRUE;
                    
                }
            
            
            /**
            * Return the data to put o wp-config.php file
            * 
            */
            function get_wp_config_data()
                {
                    $root_path  =   '/';
                    
                    //Check if wp-config.php os actually one leve up relative to wordpress root directory
                    if  ( realpath ( ABSPATH )  !=  realpath ( dirname( $this->get_wp_config_path() ) ) )
                        {
                            $subdirectory   =   str_replace( dirname( $this->get_wp_config_path() ), '' , realpath(ABSPATH . '/') );
                            $subdirectory   =   wp_normalize_path( $subdirectory );
                            $subdirectory   =   ltrim( $subdirectory, '/' );
                            $subdirectory   =   trailingslashit($subdirectory);
                            
                            $root_path      .=  $subdirectory;
                        }
                    
                            
                    $data   =   array(  "define('WPH_WPCONFIG_LOADER',          TRUE);",
                                        "@include_once( ( defined('WP_PLUGIN_DIR')    ?     WP_PLUGIN_DIR   .   '/wp-hide-security-enhancer-pro/'    :      ( defined( 'WP_CONTENT_DIR') ? WP_CONTENT_DIR  :   dirname(__FILE__) . '" . $root_path  . "' . 'wp-content' )  . '/plugins/wp-hide-security-enhancer-pro' ) . '/include/wph.class.php');",
                                        'if (class_exists(\'WPH\')) { global $wph; $wph    =   new WPH(); ob_start( array($wph, \'ob_start_callback\')); }',
                                        );
                                        
                    return $data;
                    
                }
                
                
            function settings_changed_check_for_cache_plugins()
                {
                    
                    $active_plugins = (array) get_option( 'active_plugins', array() ); 
                            
                    //cache plugin nottice
                    if(array_search('w3-total-cache/w3-total-cache.php',    $active_plugins)    !== FALSE)  
                        {
                            //check if just flushed
                            if(!isset($_GET['w3tc_note']))
                                echo "<div class='error'><p>". __('W3 Total Cache Plugin is active, make sure you clear the cache for new changes to apply', 'wp-hide-security-enhancer')  ."</p></div>";
                        }
                    if(array_search('wp-super-cache/wp-cache.php',    $active_plugins)    !== FALSE)  
                        {
                            echo "<div class='error'><p>". __('WP Super Cache Plugin is active, make sure you clear the cache for new changes to apply', 'wp-hide-security-enhancer')  ."</p></div>";
                        }
                        
                    if(array_search('wp-fastest-cache/wpFastestCache.php',    $active_plugins)    !== FALSE)  
                        {
                            echo "<div class='error'><p>". __('WP Fastest Cache Plugin is active, make sure you clear the cache for new changes to apply', 'wp-hide-security-enhancer')  ."</p></div>";
                        }    
                    
                }
                
                
            
            /**
            * Check if the site use a cache plugin with integration
            * 
            * Shall be deprecated, when all buffers will move to late init
            * 
            */
            function site_need_late_buffering()
                {
                    
                    if ( !function_exists( 'is_plugin_active' ) )
                        include_once( ABSPATH.'wp-admin/includes/plugin.php' );
                    
                    $integrated =   array (
                                            'wp-rocket/wp-rocket.php',
                                            //'swift-performance/performance.php',
                                            'wp-fastest-cache/wpFastestCache.php',
                                            'litespeed-cache/litespeed-cache.php',
                                            'clsop/clsop.php',
                                            
                                            'nitropack/main.php'
                                            );
                    
                    foreach  ( $integrated  as  $plugin )
                        {
                            if ( is_plugin_active( $plugin ) )
                                return TRUE;
                        }
                    
                    return FALSE;
                        
                }
                
                
                
            /**
            * Return the module class by it's slug
            * 
            * @param mixed $module_slug
            */
            function get_module_by_slug($module_slug)
                {
                    global $wph;
                    
                    $found_module   =   FALSE;
                    
                    foreach($wph->modules     as  $module)
                        {
                            $interface_menu_data    =   $module->get_module_slug();
                            
                            if($interface_menu_data ==  $module_slug)
                                {
                                    $found_module   =   $module;
                                    break;                            
                                }
                        }
                        
                    return $found_module;
                }
                
                
            /**
            * Return the module component class instance by it's slug
            * 
            * @param mixed $module_slug
            */
            function get_module_component_by_slug ( $module_slug )
                {
                    global $wph;
                    
                    $found_module   =   FALSE;
                    
                    foreach ( $wph->modules     as  $module )
                        {
                            foreach ( $module->components  as  $component )
                                {
                                    if ( $component->get_component_id() ==  $module_slug )
                                        return $component;
                                }
                        }
                        
                    return FALSE;
                }
            
            /**
            * Used on early access when WP_Rewrite is not available
            * 
            */
            function is_permalink_enabled()
                {
                    
                    $permalink_structure    =   get_option('permalink_structure');
                    
                    if (    empty($permalink_structure)   )
                        return FALSE;
                        
                    return TRUE;
                        
                }
            
            
            
            /**
            * Return the path to where WordPress index.php reside (WordPress loading point and .htaccess file location)
            * 
            */
            function get_home_path()
                {
                    
                    $home    = set_url_scheme( get_option( 'home' ), 'http' );
                    $siteurl = set_url_scheme( get_option( 'siteurl' ), 'http' );
                    if ( ! empty( $home ) && 0 !== strcasecmp( $home, $siteurl ) ) 
                            {
                                $home_path              =   str_replace( '\\', '/', $_SERVER['DOCUMENT_ROOT'] );
                                $home_path              =   rtrim( $home_path , '/');
                                $home_path              .=  $this->wph->default_variables['site_relative_path'];
                            } 
                        else 
                            {
                                $home_path = ABSPATH;
                            }

                    
                    $home_path      =   trim($home_path, '\\/ ');
                    
                    //not for windows
                    if ( DIRECTORY_SEPARATOR    !=  '\\')
                        $home_path      =   DIRECTORY_SEPARATOR . $home_path;
                    
                    return $home_path;
                       
                }
            
            
            /**
            * Set server type
            * 
            */
            function set_server_type()
                {
                    
                    //Allow to set server type through filter
                    if  ( defined( 'WPH_SERVER_TYPE' ) )
                        {
                            switch ( strtolower( WPH_SERVER_TYPE ) )
                                {
                                    case 'apache'       :
                                                        $this->wph->server_htaccess_config  =   TRUE;
                                                        break;
                                    case 'nginx'        :
                                                        $this->wph->server_nginx_config     =   TRUE;
                                                        break;
                                    case 'iis'          :
                                                        $this->wph->server_web_config       =   TRUE;
                                                        break;
                                }    
                            
                            return;
                        }
                    
                    $Server_SOFTWARE    =   $_SERVER['SERVER_SOFTWARE'];
                    
                    If ( empty ( $Server_SOFTWARE ) )
                        {
                            //unable to identify server type
                            return FALSE;   
                        }
                    
                    //Check for Wpengine.. Unfortunate they require all rewrite (Nginx) to be sent to support and they will do the update
                    if (  $this->server_is_wpengine() || $this->server_is_kinsta() ) 
                        {
                            $this->wph->server_nginx_config  =   TRUE;
                            return;  
                        }
                        
                    //check for Flywheel hosting
                    if ( stripos( $Server_SOFTWARE, 'Flywheel') !== FALSE )
                        {
                            $this->wph->server_nginx_config  =   TRUE;
                            return;   
                        }
    
                    if ( $this->is_apache()   ===    TRUE )
                        $this->wph->server_htaccess_config  =   TRUE;
                    
                    if ( $this->is_IIS()  === TRUE )
                        $this->wph->server_web_config  =   TRUE;
                        
                    if ( $this->is_nginx()  === TRUE )
                        $this->wph->server_nginx_config  =   TRUE;
                        
                }
                
                
    
            /**
            * Return if the server is WPEngine
            * 
            */
            function server_is_wpengine()
                {
                    if (    getenv('IS_WPE')    ==  "1"   ||  getenv('IS_WPE_SNAPSHOT')    == "1" )
                        return TRUE;
                        
                    return FALSE;
                    
                }
                
            /**
            * Return if the server is Kinsta
            * 
            */
            function server_is_kinsta()
                {
                    if (    getenv('KINSTA_CDN_DOMAIN')   !==  FALSE   ||  getenv('KINSTA_CACHE_ZONE')    !==  FALSE )
                        return TRUE;
                        
                    return FALSE;
                    
                }
            
            
            /**
            * return whatever server using the .htaccess config file
            * 
            */
            function server_use_htaccess_config_file()
                {
                    
                    $home_path      = $this->get_home_path();
                    $htaccess_file  = $home_path . DIRECTORY_SEPARATOR . '.htaccess';
                        
                    if ((!file_exists($htaccess_file) && is_writable($home_path) && $this->using_mod_rewrite_permalinks()) || is_writable($htaccess_file)) 
                        {
                            if ( $this->got_mod_rewrite() )
                                return TRUE;
                        }
                    
                    return FALSE;
                    
                }
            
            
            function using_mod_rewrite_permalinks()
                {
                    
                    return $this->is_permalink_enabled() && ! $this->using_index_permalinks();    
                    
                }
            
            
            function using_index_permalinks() 
                {
                    
                    $permalink_structure    =   get_option('permalink_structure');
                    
                    if(empty($permalink_structure))
                        return;

                    $index  =   'index.php';
                        
                    // If the index is not in the permalink, we're using mod_rewrite.
                    return preg_match( '#^/*' . $index . '#', $permalink_structure );
                    
                }
            
            function got_mod_rewrite()
                {
                    
                    if ($this->apache_mod_loaded('mod_rewrite', true))
                        return TRUE;
                    
                    return FALSE;
                    
                }
            
            
            /**
            * Does the specified module exist in the Apache config?
            *
            * @since 2.5.0
            *
            * @global bool $is_apache
            *
            * @param string $mod     The module, e.g. mod_rewrite.
            * @param bool   $default Optional. The default return value if the module is not found. Default false.
            * @return bool Whether the specified module is loaded.
            */
            function apache_mod_loaded($mod, $default = false) 
                {

                    if ( !$this->is_apache() )
                        return false;
                    
                    if ( function_exists( 'apache_get_modules' ) ) 
                        {
                            $mods = apache_get_modules();
                            if ( in_array($mod, $mods) )
                                return true;
                        } 
                    elseif ( function_exists( 'phpinfo' ) && false === strpos( ini_get( 'disable_functions' ), 'phpinfo' ) ) {
                            ob_start();
                            phpinfo(8);
                            $phpinfo = ob_get_clean();
                            if ( false !== strpos($phpinfo, $mod) )
                                return true;
                    
                    }
                            
                    return $default;
                    
                }
                
            
            /**
            * return whatever the htaccess config file is writable
            *     
            */
            function is_writable_htaccess_config_file()
                {
                    $home_path      = $this->get_home_path();
                    $htaccess_file  = $home_path . DIRECTORY_SEPARATOR . '.htaccess';
                    
                    if ((!file_exists($htaccess_file)  && $this->is_permalink_enabled()) || is_writable($htaccess_file))
                        return TRUE;
                        
                    return FALSE;
                    
                }
                
            /**
            * return whatever server using the .htaccess config file
            * 
            */
            function server_use_web_config_file()
                {
                    
                    $is_iis7    = $this->is_IIS7();
                    
                    $supports_permalinks = false;
                    if ( $is_iis7 ) 
                        {

                            $supports_permalinks = class_exists( 'DOMDocument', false ) && isset($_SERVER['IIS_UrlRewriteModule']) && ( PHP_SAPI == 'cgi-fcgi' );
                        }
                    
                    
                    $supports_permalinks    =   apply_filters( 'iis7_supports_permalinks', $supports_permalinks );
                           
                    return $supports_permalinks;
                    
                }
            
            
            /**
            * return whatever the web.config config file is writable
            *     
            */
            function is_writable_web_config_file()
                {
                    $home_path = $this->get_home_path();
                    
                    $web_config_file = $home_path . 'web.config';
                    
                    if ( ( ! file_exists($web_config_file) && $this->is_permalink_enabled() ) || win_is_writable($web_config_file) )
                        return TRUE;
                        
                    return FALSE;
                    
                }          
            
            
            /**
            * Return the end flag to be used.
            * Default: last            * 
            */
            function get_nginx_flag_type()
                {
                                        
                    $flag_type  =   apply_filters('wp-hide/nginx_flag_type', 'last' );
                        
                    return  $flag_type;
                    
                }
            
            
            
            /**
            * Return if the server run Apache
            * 
            */
            function is_apache()
                {
                    $is_apache  =   FALSE;
                    $is_apache  = (stripos($_SERVER['SERVER_SOFTWARE'], 'Apache') !== false || stripos($_SERVER['SERVER_SOFTWARE'], 'LiteSpeed') !== false);
                    
                    return $is_apache;   
                    
                }
                
            
            /**
            * Return if the server run on nginx
            * 
            */
            function is_nginx()
                {
                    $is_nginx   =   FALSE;
                    $is_nginx   = (stripos($_SERVER['SERVER_SOFTWARE'], 'nginx') !== false);
                    
                    return $is_nginx;   
                    
                }
            
            /**
            * Return if the server run on IIS
            * 
            */
            function is_IIS()
                {
                    $is_IIS     =   FALSE;
                    $is_IIS     =   !$this->is_apache() && (stripos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false || stripos($_SERVER['SERVER_SOFTWARE'], 'ExpressionDevServer') !== false);     
   
                    return $is_IIS;
                    
                }
                
            
            /**
            * Return if the server run on IIS version 7 and up
            *     
            */
            function is_IIS7()
                {
                    $is_iis7    =   FALSE;
                    $is_iis7    =   $this->is_IIS() && intval( substr( $_SERVER['SERVER_SOFTWARE'], stripos( $_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS/' ) + 14 ) ) >= 7;   
                    
                    return $is_iis7;
                }
  
            
            /**
            * Return a write_check_string from server to ensure rewrite rules where applied
            * 
            */
            function get_write_check_string_from_server()
                {
                    $home_path      = $this->get_home_path();
                    
                    global $blog_id;
                    
                    $result =   FALSE;
                                        
                    //check for .htaccess 
                    if ( $this->wph->server_htaccess_config === TRUE ) 
                        {
                            
                            //use the SERVER data, as if the rewrite was set correctly it will return the rerite check number.
                            if ( isset ( $_SERVER['REDIRECT_WPH_REWRITE_1'] ) )
                                $result =   $_SERVER['REDIRECT_WPH_REWRITE_1'];
                            else if ( isset ( $_SERVER['WPH_REWRITE_1'] ) )
                                $result =   $_SERVER['WPH_REWRITE_1'];
                            else
                                $result =   FALSE;
                            
                            
                            //Check if using specific hostings, which does not allow to set custom environment variables..
                            if ( $result ===    FALSE )    
                                {
                                    $file_path = $home_path . DIRECTORY_SEPARATOR . '.htaccess';
                                    if( file_exists( $file_path ) )
                                        {
                                            if ( $markerdata = explode( "\n", implode( '', file( $file_path ) ) ));
                                                {
                                                    foreach ( $markerdata as $markerline ) 
                                                        {
                                                            preg_match("/=WPH_REWRITE_1:([0-9a-z]+)\]/i", $markerline, $matches);
                                                            if(isset($matches[1]))
                                                                {
                                                                    $result =   $matches[1]; 
                                                                    break;
                                                                }
                                                        }
                                                }
                                        }
                                }
        
                        }
                    
                    //check for web.config
                    if ( $this->wph->server_web_config   === TRUE )
                        {
                            $file_path  =   $home_path . DIRECTORY_SEPARATOR . 'web.config';
                            if(file_exists( $file_path ))
                                {
                                    if ( $markerdata = explode( "\n", implode( '', file( $file_path ) ) ));
                                        {
                                            foreach ( $markerdata as $markerline ) 
                                                {
                                                    preg_match("'<rule name=\"wph-rewrite-check.*?<!-- WPH_REWRITE_" . $blog_id . ":([0-9a-z]+) --></rule>'si", $markerline, $matches);
                                                    if(isset($matches[1]))
                                                        {
                                                            $result =   $matches[1]; 
                                                        }
                                                        
                                                    if (!isset($matches[1])   &&  strpos($markerline, '<!-- WriteCheckString-" . $blog_id . ":') !== false)
                                                        {
                                                            $result =   trim(str_ireplace( '<!-- WriteCheckString-" . $blog_id . ":',  '', $markerline));
                                                            $result =   trim(str_replace( '-->',  '', $result));
                                                            $result =   trim($result);
                                                         
                                                        }
                                                }
                                        }   
      
                                }
                                
                        }
                        
                    return $result;    
                    
                }
            
                        
            /**
            * Return a status of custom rewrite rules, if being applied correctly
            * Compare with latest write_check_string within the options and environment (saved to server rewrite file)
            * 
            */
            function rewrite_rules_applied()
                {
                    $applied_correctly = TRUE;
                    
                    if  ( $this->wph->server_nginx_config   === TRUE )
                        return $applied_correctly;    
                    
                    if ( is_multisite() )
                        {
                            $settings           =   $this->get_site_settings ( 'network' );

                            return $applied_correctly;    
                            
                        }
                    
                    $global_settings    =   $this->get_global_settings ( );
                    if  ( $global_settings['self_setup']  ==  'yes' )
                        return $applied_correctly;
                    
                    global $blog_id;
                    
                    $site_settings      =   $this->get_site_settings( $blog_id );
                    $write_check_string =   isset ( $site_settings['write_check_string'] ) ? $site_settings['write_check_string']   :   '';
                    
                    if(!empty($write_check_string))
                        {
                            $existing_write_check_string =   $this->get_write_check_string_from_server();
                            if(empty($existing_write_check_string)  ||  $existing_write_check_string    !=  $write_check_string)
                                $applied_correctly   =   FALSE;
                        }
                                   
                    return $applied_correctly;
                }
            
            
            
            function get_rewrite_path( $existing_path, $args    =   array() )
                {
                    $args   =   wp_parse_args ( $args, array (
                                                    'left_slash'            =>  FALSE,
                                                    'right_slash'           =>  FALSE,
                                                    'type'                  =>  '',
                                                    
                                                    'exclude_wp_dir'        =>  FALSE,
                                                    
                                                    'include_full_path'     =>  FALSE
                                                        ) );
                    extract ( $args );
                    
                    $existing_path   =   $this->untrailingslashit_all ( $existing_path );
                    
                    $rewrite_path   =   '';
                    
                    if ( $type ==    'nginx' )
                        {
                            if ( $include_full_path )
                                $rewrite_path   .=  trailingslashit ( $this->wph->default_variables['site_relative_path'] );
                            
                            if ( !  $exclude_wp_dir )
                                $rewrite_path   .=  trailingslashit ( $this->wph->default_variables['wordpress_directory'] );
                                
                            $rewrite_path   =   rtrim ( $rewrite_path, '/' );
                            
                            $rewrite_path   .=  '__WPH_SITES_SLUG__/';
                            $rewrite_path   .=  trailingslashit ( $existing_path );
                        }
                        else
                        {
                            if ( $include_full_path )
                                $rewrite_path   .=  trailingslashit ( $this->wph->default_variables['site_relative_path'] );
                            
                            if ( !  $exclude_wp_dir )
                                $rewrite_path   .=  trailingslashit ( $this->wph->default_variables['wordpress_directory'] );
                            $rewrite_path   .=  trailingslashit ( $existing_path );
                        }
                    
                    $rewrite_path   =   $this->prepare_path( $rewrite_path );
                    $rewrite_path   =   $this->untrailingslashit_all ( $rewrite_path );
                    
                    if( $left_slash === TRUE )
                        $rewrite_path   =   '/' .   $rewrite_path;    
                        
                    if( $right_slash === TRUE )
                        $rewrite_path   .=   '/';
                    
                    if ( $type ==    'nginx' )
                        {    
                            if ( strpos( $rewrite_path, '/__WPH_SITES_SLUG__' ) === 0 )
                                $rewrite_path   =   ltrim ( $rewrite_path, '/' );
                        }
                    
                    return $rewrite_path;
                       
                }
            
            
            /**
            * Return rewrite base
            *
            */
            function get_rewrite_base( $saved_field_data, $left_slash   =   TRUE, $right_slash  =   TRUE, $append_path =   '' )
                {
                    global $blog_id;
                    
                    $saved_field_data   =   $this->untrailingslashit_all($saved_field_data);
                    
                    $path               =   '';
                    switch($append_path)
                        {
                            case 'site_path'    :
                                                    $path               =   !empty($this->wph->default_variables['site_relative_path']) ? trailingslashit( $this->wph->default_variables['site_relative_path'] )  :   '';
                                                    break;
                            
                            case 'wp_path'    :
                                                    $path              .=   !empty($this->wph->default_variables['wordpress_directory']) ? trailingslashit( $this->wph->default_variables['wordpress_directory'] )  :   '';
                                                    break;
                            case 'full_path'    :
                                                    $path               =   !empty($this->wph->default_variables['site_relative_path']) ? trailingslashit( $this->wph->default_variables['site_relative_path'] )  :   '';
                                                    $path              .=   !empty($this->wph->default_variables['wordpress_directory']) ? trailingslashit( $this->wph->default_variables['wordpress_directory'] )  :   '';
                                                    break;                        
                        }
                        
                    if ( is_multisite() )
                        {                            
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                            
                            $use_blog_id    =   $blog_id;
                            $use_blog_id    =   1;
                            
                            $blog_details = get_blog_details( $use_blog_id );
                            
                            $path   .=   trim($blog_details->path, '/') . '/';

                        }
                        
                    //remove the site relative path if not empty
                    if (    ! empty ( $this->wph->default_variables['site_relative_path']  )    &&  ! empty ( trim( $this->wph->default_variables['site_relative_path'], '/' )  ) &&  ! empty ( trim( $path, '/') )   &&  strpos( trim( $path, '/'),  trim( $this->wph->default_variables['site_relative_path'] , '/' ) )   === 0  )
                        {
                            $path   =   '#' .   $path;
                            $path   =   str_replace( '#' . trim( $this->wph->default_variables['site_relative_path'] , '/' ) , '', $path );
                            $path   =   ltrim ( $path , '/' );
                        }
                    
                    $rewrite_base   =   !empty($path) ? trailingslashit( $path ) . $saved_field_data : ( !empty($saved_field_data) ?  '/' .$saved_field_data : '' );
                    if( !empty($rewrite_base))
                        {
                            $rewrite_base   =   $this->untrailingslashit_all( $rewrite_base );
                            
                            if( $left_slash === TRUE )
                                $rewrite_base   =   '/' .   $rewrite_base;    
                                
                            if( $right_slash === TRUE )
                                $rewrite_base   =   $rewrite_base . '/';
                            
                        }
                    
                    return $rewrite_base;
                    
                }
                
            /**
            * Return rewrite to base
            *
            */
            function get_rewrite_to_base( $field_data, $left_slash   =   TRUE, $right_slash  =   TRUE, $append_path =   '')
                {

                    
                    $field_data         =   $this->untrailingslashit_all( $field_data );
                    
                    $path               =   '';
                    switch($append_path)
                        {
                            case 'site_path'    :
                                                    $path               =   !empty($this->wph->default_variables['site_relative_path']) ? trailingslashit( $this->wph->default_variables['site_relative_path'] )  :   '';
                                                    break;
                            
                            case 'wp_path'    :
                                                    $path              .=   !empty($this->wph->default_variables['wordpress_directory']) ? trailingslashit( $this->wph->default_variables['wordpress_directory'] )  :   '';
                                                    break;
                            case 'full_path'    :
                                                    $path               =   !empty($this->wph->default_variables['site_relative_path']) ? trailingslashit( $this->wph->default_variables['site_relative_path'] )  :   '';
                                                    $path              .=   !empty($this->wph->default_variables['wordpress_directory']) ? trailingslashit( $this->wph->default_variables['wordpress_directory'] )  :   '';
                                                    break;                        
                        }
                    
                    $rewrite_to_base    =   !empty($path) ? trailingslashit( $path ) . $field_data : ( !empty( $field_data ) ?  '/' . $field_data : '' );
                    if( !empty($rewrite_to_base))
                        {
                            $rewrite_to_base   =   $this->untrailingslashit_all( $rewrite_to_base );
                            
                            if( $left_slash === TRUE )
                                $rewrite_to_base   =   '/' .   $rewrite_to_base;    
                                
                            if( $right_slash === TRUE )
                                $rewrite_to_base   =   $rewrite_to_base . '/';
                            
                        }
                    
                    return $rewrite_to_base;
                    
                }
            
            
            /**
            * Insert the data using markes in a specified file
            * 
            * @param mixed $filename
            * @param mixed $marker
            * @param mixed $insertion
            * @param mixed $before_marker
            * @return mixed
            */
            function insert_with_markers ( $filename, $args )
                {
                    
                    $defaults   = array (
                                            'marker'            =>  '',
                                            
                                            'insertion'         =>  '',
                                            
                                            'before_marker'     =>  '',
                                            'before_offset'     =>  0,
                                            
                                            'after_marker'      =>  ''
                                        );
                                        
                    // Parse incoming $args into an array and merge it with $defaults
                    $args   =   wp_parse_args( $args, $defaults );
                    extract($args);
                       
                    if ( ! file_exists( $filename ) ) {
                        if ( ! is_writable( dirname( $filename ) ) ) {
                            return false;
                        }
                        if ( ! touch( $filename ) ) {
                            return false;
                        }
                    } elseif ( ! is_writeable( $filename ) ) {
                        return false;
                    }

                    if ( ! is_array( $insertion ) ) {
                        $insertion = explode( "\n", $insertion );
                    }

                    $start_marker = "# BEGIN {$marker}";
                    $end_marker   = "# END {$marker}";

                    $fp = fopen( $filename, 'r+' );
                    if ( ! $fp ) {
                        return false;
                    }

                    // Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
                    flock( $fp, LOCK_EX );

                    $lines = array();
                    while ( ! feof( $fp ) ) {
                        $lines[] = rtrim( fgets( $fp ), "\r\n" );
                    }

                    // Split out the existing file into the preceding lines, and those that appear after the marker
                    $pre_lines = $post_lines = $existing_lines = array();
                    $found_marker = $found_end_marker = false;
                    foreach ( $lines as $line ) {
                        if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
                            $found_marker = true;
                            continue;
                        } elseif ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
                            $found_end_marker = true;
                            continue;
                        }
                        if ( ! $found_marker ) {
                            $pre_lines[] = $line;
                        } elseif ( $found_marker && $found_end_marker ) {
                            $post_lines[] = $line;
                        } else {
                            $existing_lines[] = $line;
                        }
                    }

                    // Check to see if there was a change
                    if ( $existing_lines === $insertion ) {
                        flock( $fp, LOCK_UN );
                        fclose( $fp );

                        return true;
                    }

                    
                    // Generate the new file data
                    if($found_marker && $found_end_marker)
                        {
                            $new_file_data = implode( "\n", array_merge(
                                $pre_lines,
                                array( $start_marker ),
                                $insertion,
                                array( $end_marker ),
                                $post_lines
                            ) );
                        }
                        else
                        {
                            $insert_at  =   FALSE;                            
                            if  ( ! empty ( $before_marker ) )
                                {
                                    $insert_at  =   array_search($before_marker, array_map("trim", $pre_lines) );
                                }
                            
                            if  ( $insert_at    === FALSE  &&  ! empty ( $after_marker ) )
                                {
                                    $insert_at  =   array_search($after_marker , array_map("trim", $pre_lines) );
                                    $insert_at++;
                                }
                                
                            if  ( $insert_at  ===   FALSE )
                                $insert_at  =   0;

                            $pre_lines  =   array_merge( 
                                                            array_slice( $pre_lines, 0, $insert_at, TRUE),
                                                            array( $start_marker ),
                                                            $insertion,
                                                            array( $end_marker ),
                                                            array_slice( $pre_lines, $insert_at, count($pre_lines), TRUE)
                                                            );
                                
                            $new_file_data = implode( "\n", $pre_lines );        
                            
                        }

                    // Write to the start of the file, and truncate it to that length
                    fseek( $fp, 0 );
                    $bytes = fwrite( $fp, $new_file_data );
                    if ( $bytes ) {
                        ftruncate( $fp, ftell( $fp ) );
                    }
                    fflush( $fp );
                    flock( $fp, LOCK_UN );
                    fclose( $fp );

                    return (bool) $bytes;    
                    
                    
                }
            
            
            function extract_from_markers( $filename, $marker ) 
                {
                    $result = array ();

                    if ( ! file_exists( $filename ) ) 
                        {
                            return $result;
                        }

                    $markerdata = explode( "\n", implode( '', file( $filename ) ) );

                    $state = false;
                    foreach ( $markerdata as $markerline ) 
                        {
                            if ( false !== strpos( $markerline, '# END ' . $marker ) ) 
                                {
                                    $state = false;
                                }
                            if ( $state ) 
                                {
                                    $result[] = $markerline;
                                }
                            if ( false !== strpos( $markerline, '# BEGIN ' . $marker ) ) 
                                {
                                    $state = true;
                                }
                        }

                    return $result;
                } 
            
            static public function clean_with_markers( $filename, $marker)
                {
                    
                    if ( ! file_exists( $filename ) ) {
                        if ( ! is_writable( dirname( $filename ) ) ) {
                            return false;
                        }
                        if ( ! touch( $filename ) ) {
                            return false;
                        }
                    } elseif ( ! is_writeable( $filename ) ) {
                        return false;
                    }
              
                    $start_marker = "# BEGIN {$marker}";
                    $end_marker   = "# END {$marker}";

                    $fp = fopen( $filename, 'r+' );
                    if ( ! $fp ) {
                        return false;
                    }

                    // Attempt to get a lock. If the filesystem supports locking, this will block until the lock is acquired.
                    flock( $fp, LOCK_EX );

                    $lines = array();
                    while ( ! feof( $fp ) ) {
                        $lines[] = rtrim( fgets( $fp ), "\r\n" );
                    }

                    // Split out the existing file into the preceding lines, and those that appear after the marker
                    $pre_lines = $post_lines = $existing_lines = array();
                    $found_marker = $found_end_marker = false;
                    foreach ( $lines as $line ) {
                        if ( ! $found_marker && false !== strpos( $line, $start_marker ) ) {
                            $found_marker = true;
                            continue;
                        } elseif ( ! $found_end_marker && false !== strpos( $line, $end_marker ) ) {
                            $found_end_marker = true;
                            continue;
                        }
                        if ( ! $found_marker ) {
                            $pre_lines[] = $line;
                        } elseif ( $found_marker && $found_end_marker ) {
                            $post_lines[] = $line;
                        } else {
                            $existing_lines[] = $line;
                        }
                    }
                         
                    // Generate the new file data
                    if($found_marker && $found_end_marker)
                        {
                            $new_file_data = implode( "\n", array_merge(
                                $pre_lines,
                                $post_lines
                            ) );
                            
                            // Write to the start of the file, and truncate it to that length
                            fseek( $fp, 0 );
                            $bytes = fwrite( $fp, $new_file_data );
                            if ( $bytes ) {
                                ftruncate( $fp, ftell( $fp ) );
                            }
                            fflush( $fp );
                            flock( $fp, LOCK_UN );
                            fclose( $fp );

                            return (bool) $bytes; 
                            
                        }
                
                    return FALSE;   
                    
                    
                }
            
            
            
            /**
            * Left trim string from a list of array
            * 
            */
            function ltrim_array( $string, $strip = array())
                {
                    if ( ! is_array($strip) ||  count( $strip ) <   1   )
                        return $string;
                    
                    foreach ( $strip    as $strip_string )
                        {    
                            if( 0 === strpos($string, $strip_string))
                                {
                                    $string = substr($string, strlen($strip_string));
                                }
                        }
                       
                    return $string;
                    
                }
            
            
            
            /**
            * Check if the plugin started through MU plugin loader
            * 
            */
            function is_muloader()
                {
                    
                    if (defined('WPH_MULOADER'))
                        return TRUE;

                    return FALSE;
                       
                }
            
                
            /**
            * 
            * Check if theme is is customize mode
            *     
            */
            function is_theme_customize()
                {
                    
                    if (    strpos($_SERVER['REQUEST_URI'] ,'customize.php')   !== FALSE    )
                        return TRUE;
                        
                    if (    isset($_POST['wp_customize'])  && sanitize_text_field($_POST['wp_customize'])   ==  "on" )   
                        return TRUE;        
                    
                    if (    isset($_GET['customize_theme']) )
                        return TRUE;
                    
                    return FALSE;
                    
                }
                
            
            /**
            * Return Settings for specified / curren site
            * 
            * @param mixed $blog_id_settings
            * @param mixed $force_reload
            */
            private function _get_settings( $blog_id_settings  )
                {
                    
                    global $blog_id;
                             
                    if (  is_multisite()    &&  $blog_id_settings   >   0 )
                        switch_to_blog( $blog_id_settings );
                    
                            
                    if ( $blog_id_settings ==  'network')
                        {
                            $network_settings   =   get_site_option('wph_settings');
                    
                            $defaults   = array (
                                                    'module_settings'   =>  array()
                                                );
                            
                                  
                            $settings   =   wp_parse_args( $network_settings, $defaults ); 
                                                 
                        }
                        else
                        {
                            $settings   =   get_option('wph_settings');   
                        }
                        
                        
                        
                    //ensure the settings are filled in with defaults if not exists in array
                    $_do_update_settings =   FALSE;
                    if( !isset($settings['module_settings'] ) )
                        {
                            $settings['module_settings']  =   array();    
                            $_do_update_settings    =   TRUE;
                        }
                        
                    //make sure all options exists within modules settings
                    foreach($this->wph->modules   as  $module)
                        {
                            $module_components    =   $this->filter_settings(   $module->get_module_components_settings(), TRUE    );
                            
                            foreach($module_components as $module_component)
                                {
                                    $default_value  =   $module_component['default_value'];
                                    
                                    if(!isset( $settings['module_settings'][ $module_component['id'] ]))
                                        {
                                            $settings['module_settings'][ $module_component['id'] ]   =   $default_value;
                                            $_do_update_settings    =   TRUE;
                                        }
                                }
                        }   
                    
        
                    $settings   =   apply_filters('wp-hide/get_settings', $settings, $blog_id_settings);
                    
                    if($_do_update_settings)
                        $this->update_site_settings( $settings, $blog_id_settings );
                    
                                        
                    //hold the settings within main class for further usage
                    $this->wph->settings[ $blog_id_settings ]    =   $settings;
            
                        
                    if ( is_multisite()    &&  $blog_id_settings   >   0 )
                        restore_current_blog();
                                        
                    return $settings;
                                       
                }
            
            
            /**
            * Ensure settings include all loaded components
            * This is being called after components where loaded
            * 
            */
            function fill_settings()
                {
                    global $blog_id;
                    
                    unset ( $this->wph->settings[ $blog_id ] ) ;
                    
                    $this->_get_settings( $blog_id );
                    
                }
            
            
            /**
            * Return current $blog_id settings
            * 
            */
            function get_current_site_settings ( )
                {
                    
                    global $blog_id;
                    
                    if ( is_multisite() &&  is_network_admin()    &&  ! isset( $this->wph->settings['network'] ))
                        {
                            $settings   =   $this->_get_settings( 'network' );   
                        }
                        else if ( is_multisite() &&  is_network_admin()    &&  isset( $this->wph->settings['network'] ) )
                            {
                                $settings   =   $this->wph->settings['network'];
                            }
                        else if ( ! isset( $this->wph->settings[$blog_id] ) )
                            $settings   =   $this->_get_settings( $blog_id );
                        else
                            $settings   =   $this->wph->settings[$blog_id];    
                    
                    return $settings;
                    
                }
                
            
            /**
            * Return $blog_id settings
            * Use stored settings data set instead self::get_settings()
            * 
            */
            function get_site_settings ( $blog_id )
                {
                                        
                    if ( ! isset( $this->wph->settings[$blog_id] ) )
                        $settings   =   $this->_get_settings( $blog_id );
                        else
                        $settings   =   $this->wph->settings[$blog_id];    
                    
                    return $settings;
                    
                }
                
                
            /**
            * Return th global settings which will be used across any sites
            * 
            */
            function get_global_settings()
                {
                                        
                    $settings   =   get_site_option('wph_global_settings');
                    
                    $defaults   = array (
                                            'self_setup'                            =>  'no',
                                            'covert_relative_urls_to_absolute'      =>  'no',
                                            'nginx_generate_simple_rewrite'         =>  'yes',
                                            'nginx_save_rewrite_to_file'            =>  'no',
                                            'nginx_headers_save_rewrite_to_file'    =>  'no'
                                        );
                    
                    $settings   =   wp_parse_args( $settings, $defaults );
                    
                    //if WPEngine force 'nginx_generate_simple_rewrite'
                    if ( $this->server_is_wpengine()    ||  $this->server_is_kinsta() )
                        {
                            $settings['nginx_generate_simple_rewrite']          =   'yes';
                        }
                    
                    $settings   =   apply_filters('wp-hide/get_global_settings', $settings);
                    
                    return $settings;
                    
                }
                
                
            /**
            * Update global settings
            * 
            */
            function update_global_settings( $settings )
                {
                                        
                    update_site_option('wph_global_settings', $settings);
                    
                }
                
                
            
            /**
            * Return $blog_id settings to apply
            * NOT TO BE USED FOR INTERFACE -> this output the latest options list
            * 
            * This options list is corelated with saved rewrite rules
            */
            function get_site_modules_settings_to_apply ( $blog_id )
                {
                    
                    if ( $blog_id   ==   'network' )
                        {
                            $wph_rewrite_manual_install =   get_site_option('wph-rewrite-manual-install');
                            if ( empty ($wph_rewrite_manual_install) )
                                {
                                    $settings   =   $this->get_site_modules_settings( $blog_id );    
                                }
                                else
                                {
                                    $settings   =   get_site_option('wph-previous-options-list');
                                }   
                            
                        }
                        else
                        {
                            $wph_rewrite_manual_install =   get_site_option('wph-rewrite-manual-install');
                            if ( empty ($wph_rewrite_manual_install) )
                                {
                                    $settings   =   $this->get_site_modules_settings( $blog_id );    
                                }
                                else
                                {
                                    //use previous saved setings
                                    if ( is_multisite() )
                                        switch_to_blog( $blog_id );   
                                    
                                    $wph_previous_options_list  =   get_option('wph-previous-options-list');
                                    if ( ! is_array($wph_previous_options_list))
                                        $wph_previous_options_list  =   array();
                                    
                                    if ( is_multisite() )    
                                        restore_current_blog();                            
                                    
                                    $settings   =   $wph_previous_options_list;
                                }
                        }
                    
                    return $settings;
                    
                }
            
                   
            
            /**
            * Return modules setings for current site
            * 
            * @param mixed $blog_id
            */
            function get_site_modules_settings( $blog_id_settings )
                {
                    
                    if ( isset( $this->wph->settings[ $blog_id_settings ] ) )
                        $settings       =   $this->wph->settings[ $blog_id_settings ];
                        else
                        $settings       =   $this->_get_settings( $blog_id_settings );
                        
                    $modules_settings       =   $settings['module_settings'];
                    
                    return $modules_settings;
                    
                }
            
            
            /**
            * Return a Module Item value setting
            * 
            * If $context is 'display' then it returns the current saved value
            * 
            * @param mixed $item_id
            */
            function get_site_module_saved_value( $option_id, $blog_id_settings =   '', $context = '' )
                {
                    
                    if ( empty( $blog_id_settings ) )
                        {
                            global $blog_id;
                            
                            $blog_id_settings   =   $blog_id;
                        }
                    
                    if ( $context   ==   'display' )
                        $modules_settings   =   $this->get_site_modules_settings( $blog_id_settings );
                        else
                        $modules_settings   =   $this->get_site_modules_settings_to_apply( $blog_id_settings );
                        
                    
                    $value      =   isset($modules_settings[ $option_id ])  ?   $modules_settings[ $option_id] :   '';
                    
                    $value      =   apply_filters( 'wp-hide/get_site_module_saved_value', $value, $option_id );
                    
                    return $value;
                    
                }
                   
        
            
            /**
            * Update the settings for the given $blog_id
            * 
            * @param mixed $settings
            * @param mixed $blog_id_settings
            * @param mixed $update_class_settings
            */
            function update_site_settings( $settings, $blog_id_settings, $update_class_settings =   TRUE )
                {
          
                    if (  $blog_id_settings ==  'network' )
                        {
                            update_site_option('wph_settings', $settings);
                        }
                        else
                        {
                            if ( is_multisite() )
                                switch_to_blog( $blog_id_settings );
                                
                            update_option('wph_settings', $settings); 
                            
                            if ( is_multisite() )
                                restore_current_blog();   
                        }
                        
                    if  (   $update_class_settings  === TRUE )
                        $this->wph->settings[ $blog_id_settings ]   =   $settings;
                    
                }
                
                
            
            /**
            * Update the modules settings for current blog_id
            * 
            * @param mixed $modules_settings
            */
            function update_site_modules_settings( $modules_settings, $blog_id_settings, $update_class_settings =   TRUE )
                {
                        
                    $settings   =   $this->wph->settings[ $blog_id_settings ];
                    
                    $settings['module_settings']    =   $modules_settings;
                    
                    $this->update_site_settings( $settings, $blog_id_settings );
                    
                    if  (   $update_class_settings  === TRUE )
                        $this->wph->settings[ $blog_id_settings ]   =   $settings;
                    
                }
                
            
            
            /**
            * return a hash of current site settings
            * 
            */
            function get_current_site_settings_hash()
                {
                    
                    $settings   =   $this->get_current_site_settings ();
                    
                    //return md5 ( json_encode( $settings['module_settings'] ) );
                    return hash( 'crc32', json_encode( $settings['module_settings'] ), FALSE );
                    
                }
                
            
            /**
            * Return the blog id or network if superadmin dashboard
            * 
            */
            function get_blog_id()
                {
                    global $blog_id;
                    
                    $blog_id_settings   =   '';
                       
                    if ( is_multisite() )
                        $blog_id_settings   =   'network';
                        else
                        $blog_id_settings   =   $blog_id;      
                    
                    return $blog_id_settings;
                    
                }
                
            
            /**
            * Return the blog_id or network as blog_id, to be used to retrieve the settings.
            * This always return $blog_id when Single Site
            * 
            *     
            */
            function get_blog_id_setting_to_use()
                {
                    
                    global $blog_id; 
                    
                    if(is_multisite() )
                        {
                            return 'network';
                        }
                        else
                            return $blog_id;
                        
                }
                
            
            /**
            * Get path from url relative to domain root
            *     
            * @param mixed $url
            * @param mixed $is_file_path
            * @param mixed $relative_to_wordpress_directory
            */    
            function get_url_path($url, $is_file_path   =   FALSE, $relative_to_wordpress_directory    =   FALSE)
                {
                    if(!$is_file_path)
                        $url            =   trailingslashit(    $url    );
                        
                    $url_parse      =   parse_url(  $url   );
                           
                    $path           =   $url_parse['path'];
                    if( $relative_to_wordpress_directory   === TRUE &&  $this->wph->default_variables['wordpress_directory']    !=  '/') 
                        {
                            $path   =   $this->string_left_replacement( $path , trailingslashit ( $this->wph->default_variables['wordpress_directory'] )) ;
                        }
                    
                    if(!$is_file_path)
                        $path           =   trailingslashit(    $path   );
                    
                    if($path    !=  '/' && strlen($path) > 1)
                        {
                            $path   =   ltrim($path, '/');
                            $path   =   '/' .   $path;
                        }
                    
                    if(isset($url_parse['query']))
                        $path   .=  '?' .   $url_parse['query'];
                    
                    $path   =   str_replace( '\\', '/', $path);
                    
                    return $path;
                    
                }
                
            
            /**
            * return the url relative to domain root
            * 
            * @param mixed $url
            */
            function get_url_path_relative_to_domain_root($url)
                {
                    
                    $url    =   str_replace(trailingslashit(  home_url()  ), "" , $url);
                       
                    return $url;
                    
                }
                
                
            /**
            * Replace all slashes from begining and the end of string
            * 
            * @param mixed $value
            */
            function untrailingslashit_all($value)
                {
                    $value  =   ltrim(rtrim($value, "/"),  "/");
                    
                    return $value;
                }    
            
            
            
            /**
            * Replace a prefix from the beginning of a text
            *     
            * @param mixed $string
            * @param mixed $prefix
            */
            function string_left_replacement($string, $prefix)    
                {
                    if (substr($string, 0, strlen($prefix)) == $prefix) 
                        {
                            $string = (string) substr($string, strlen($prefix));
                        }
                        
                    return $string;
                        
                }
            
            
            /**
            * saniteize including a possible extension
            * 
            * @param mixed $value
            */    
            function sanitize_file_path_name($value)
                {
                    $value  =   trim($value);
                    
                    if(empty($value))
                        return $value;
                    
                    //check for any extension
                    $pathinfo   =   pathinfo($value);
                    
                    $dirname    =   (!empty($pathinfo['dirname'])    &&  $pathinfo['dirname']    !=  '.')  ?    $pathinfo['dirname']    :   '';
                    $path       =   !empty($dirname)    ?   trailingslashit($dirname)   .   $pathinfo['filename']   :   $pathinfo['filename'];   
                    
                    $parts  =   explode("/",    $path);
                    $parts  =   array_filter($parts);
                    
                    foreach($parts  as  $key    =>  $part_item)
                        {
                            $parts[$key]    =   sanitize_title($part_item);
                        }
                        
                    $value  =   implode("/", $parts);
                    
                    $value  .=   !empty($pathinfo['extension']) ?   '.' . $pathinfo['extension'] :   '';  
                    
                    $value  =   strtolower($value);
                    
                    return $value;
                }
            
            
            /**
            * saniteize an array
            * 
            * @param mixed $value
            */    
            function sanitize_array( $array )
                {
                    if ( ! is_array ( $array ) )
                        return $array;
                        
                    foreach ( $array    as  $key    =>  $value )
                        {
                            $array[ $key ]  =   sanitize_title( $value );
                        }
                    
                    return $array;
                }
            
            
            /**
            * make sure there's a php extension included within the slug
            * 
            * @param mixed $value
            * @return mixed
            */
            function extension_required($value, $extension)
                {
                    $value  =   trim($value);
                    
                    if($value   ==  '')
                        return '';
                    
                    $file_extension  =   substr($value, -4);
                    if(strtolower( $file_extension )   !=  '.' . $extension )
                        $value  .=  '.' . $extension;    
                                        
                    return $value;
                }
                
            
            /**
            * Return current url
            *     
            */    
            function get_current_url()
                {
                    
                    $current_url    =   'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                    
                    return  $current_url;
                                        
                }
                
            
            /**
            * Add replacement withint the list
            * 
            * @param mixed $old_url
            * @param mixed $new_url
            */
            function add_replacement($old_url, $new_url, $priority  =   'normal')
                {
                
                    if($this->replacement_exists($old_url))
                        return;
                        
                    $this->wph->urls_replacement[ $priority ][ $old_url ]  =   $new_url;   
                    
                }
                
            
            /**
            * Return whatever a replacement exists or not
            * The old url should be provided
            *     
            * @param mixed $old_url
            */
            function replacement_exists($old_url)
                {
                    
                    if(count($this->wph->urls_replacement)  <   1)
                        return FALSE;
                    
                    foreach($this->wph->urls_replacement    as  $priority   =>  $replacements_block)
                        {
                            if(isset($this->wph->urls_replacement[$priority][ $old_url ]))
                                return TRUE;
                        }
                        
                    return FALSE;
                                        
                }
                
                
            
            /**
            * Return a list of replacements
            * 
            */
            function get_replacement_list()
                {
                    
                    $replacements   =   array();
                    
                    if(count($this->wph->urls_replacement)  <   1)
                        return $replacements;
                    
                    foreach($this->wph->urls_replacement    as  $priority   =>  $replacements_block)
                        {
                            if(!is_array($replacements_block)   ||  count($replacements_block) < 1)
                                continue;
                            
                            foreach($replacements_block as  $old_url   =>  $new_url)
                                {
                                    $replacements[ $old_url ] =   $new_url;
                                }
                        }
                        
                    return $replacements;   
                    
                }
            
            
            /**
            * Add a preserved link
            * 
            * @param mixed $preserve_slug
            * @param mixed $new_url
            */
            function add_preserved_url($preserve_slug, $new_url)
                {
                    
                    $this->wph->url_preserve[ $preserve_slug ]  =   $new_url;   
                    
                }
                
            /**
            * Return the prserved links
            * 
            * @param mixed $preserve_slug
            * @param mixed $new_url
            */
            function get_preserved_list()
                {
                    
                    return $this->wph->url_preserve;  
                    
                }
            
            
            /**
            * Preserve Texts between     <!-- WPH Preserve - Start -->       and      <!-- WPH Preserve - Stop -->
            * 
            */
            function text_preserve( $buffer )
                {
                    
                    preg_match_all("'<!-- WPH Preserve - Start -->(.*?)<!-- WPH Preserve - Stop -->'s", $buffer, $matches);
                    
                    if ( $matches === FALSE )
                        return $buffer;
                    
                    foreach ( $matches[1]  as  $key =>  $match )
                        {
                            $hash   =   '%W-P-H-PLACEHOLDER-PRESERVE-' . md5($match);
                            $this->wph->text_preserve[ $hash ]    =   $match;
                            
                            $buffer =   str_replace($matches[0][$key], $hash, $buffer);
                        }
                        
                    return $buffer;
                    
                }
            
            
            /**
            * Restore any preserved texts
            * 
            * @param mixed $buffer
            */
            function text_preserve_restore( $buffer )
                {
                    
                    if ( count ( $this->wph->text_preserve ) < 1 )
                        return $buffer;
                    
                    foreach ( $this->wph->text_preserve as  $hash   =>  $text )
                        {
                            $buffer =   str_ireplace($hash, $text, $buffer);      
                        }
                    
                    return $buffer;
                    
                }
            
            
            /**
            * Replace the urls within given content
            * 
            * @param mixed $text
            * @param mixed $replacements
            */
            function content_urls_replacement( $text, $replacements )
                {
                    //process the replacements
                    if( count($replacements)  <   1)
                        return $text;
                        
                    if  ( is_object( $text ) )
                        return $text;
                    
                    //exclude scheme to match urls without it
                    $_replacements                      =   array();
                    //no protocol
                    $_replacements_np                   =   array();
                    
                    //single quote ; double quote
                    $_relative_url_replacements_sq      =   array();
                    $_relative_url_replacements_dq      =   array();
                    
                    //single quote ; double quote / domain url / domain ssl
                    $_relative_domain_url_replacements_sq  =   array();
                    $_relative_domain_url_replacements_dq  =   array();
                    
                    $home_url           =   home_url();
                    $home_url_parsed    =   parse_url($home_url);
                    $domain_url         =   'http://' . $home_url_parsed['host'];
                    $domain_url_ssl     =   'https://' . $home_url_parsed['host'];
                    
                    /**
                    * 
                    * CDN
                    * 
                    */                    
                    $CDN_urls    =   (array)$this->get_site_module_saved_value('cdn_url',  $this->get_blog_id_setting_to_use());
                    $CDN_urls    =   array_filter( array_map("trim", $CDN_urls) ) ;
                    if  ( count( $CDN_urls ) > 0 )
                        {
                            foreach($replacements   as $old_url =>  $new_url)
                                {
                                    foreach ( $CDN_urls  as  $CDN_url )
                                        {
                                            $replacements[ str_replace($home_url_parsed['host'], $CDN_url, $old_url) ]  =   str_replace($home_url_parsed['host'], $CDN_url, $new_url);
                                        }
                                }
                        }
                    
                    /**
                    * Preserve absolute paths
                    * 
                    */
                    $replace_now    =   array ( 
                                                ABSPATH                         =>  '%W-P-H-PLACEHOLDER-PRESERVE-ABSPATH%',
                                                //jsonencoded
                                                trim(json_encode(ABSPATH), '"') =>  '%W-P-H-PLACEHOLDER-PRESERVE-JSON-ABSPATH%',
                                                //urlencode
                                                trim(urlencode(ABSPATH), '"')   =>  '%W-P-H-PLACEHOLDER-PRESERVE-URLENCODE-ABSPATH%'     
                                                );
                    $text   =   str_ireplace (   array_keys ( $replace_now ),    array_values ( $replace_now ), $text );
                    $replace_now    =   array();
                    
                    foreach ( $replacements   as $old_url =>  $new_url )
                        {
                            $_relative_domain_url_replacements_dq[ '"' . str_ireplace(   array( $domain_url, $domain_url_ssl ),   "", $old_url)   ] =   '"' . str_ireplace(   array( $domain_url, $domain_url_ssl ),   "", $new_url);
                            $_relative_domain_url_replacements_sq[ "'" . str_ireplace(   array( $domain_url, $domain_url_ssl ),   "", $old_url)   ] =   "'" . str_ireplace(   array( $domain_url, $domain_url_ssl ),   "", $new_url);
                            
                            //match urls without protocol
                            $_old_url    =   str_ireplace(   array('http:', 'https:'),   "", $old_url);
                            $_new_url    =   str_ireplace(   array('http:', 'https:'),   "", $new_url);
                            
                            $_replacements_np[$_old_url]    =   $_new_url;
                            
                            $_old_url    =   str_ireplace(   array('http://', 'https://'),   "", $old_url);
                            $_new_url    =   str_ireplace(   array('http://', 'https://'),   "", $new_url);
                            
                            $_replacements[$_old_url]    =   $_new_url;
                        }
                    
                    //merge all replacements
                    $replace_now    =   array_merge( $replace_now, $_replacements_np, $_relative_domain_url_replacements_sq, $_relative_domain_url_replacements_dq );
                                        
                    /**
                    * Check for json encoded urls
                    * Format    domain/old-slug  =>  domain/new-slug
                    * 
                    * Some might not include the domain, to ensure replacing in specific instances  e.g admin url, ajax url
                    */                    
                    foreach($_replacements   as $old_url =>  $new_url)
                        {
                            //JSON some might not using the end forward slash
                            //add ending double quote to ensure end of url, to avoid replacing parts of the data
                            if ( rtrim( $old_url , '/' )    !=  $old_url )
                                {
                                    //$text   =   str_ireplace(   trim( json_encode( rtrim( trim( $old_url, '"'), '/') ), '"' ) . '"'  , trim( json_encode( rtrim( trim ( $new_url, '"'), '/' ) ), '"' ) . '"'  ,$text   );
                                    $replace_now[ trim( json_encode( rtrim( trim( $old_url, '"'), '/') ), '"' ) . '"' ]   =   trim( json_encode( rtrim( trim ( $new_url, '"'), '/' ) ), '"' ) . '"';   
                                }
                            
                            //URL ENCODED
                            $_old_url    =   trim(urlencode($old_url), '"');   
                            $_new_url    =   trim(urlencode($new_url), '"'); 
                            
                            //$text =   str_ireplace(    $_old_url, $_new_url  ,$text   );
                            $replace_now[ $_old_url ]  =    $_new_url;
                            
                            $old_url    =   trim(json_encode($old_url), '"');   
                            $new_url    =   trim(json_encode($new_url), '"'); 
                            
                            //$text =   str_ireplace(    $old_url, $new_url  ,$text   );
                            $replace_now[ $old_url ]  =    $new_url;
                            
                            $old_url    =   trim(urlencode($old_url), '"');   
                            $new_url    =   trim(urlencode($new_url), '"'); 
                            
                            //$text =   str_ireplace(    $old_url, $new_url  ,$text   );
                            $replace_now[ $old_url ]  =    $new_url;
                        }
                    
                    foreach($_relative_domain_url_replacements_dq   as $old_url =>  $new_url)
                        {
                            /*
                            *   JSON always use double quotes
                            *   use double quote type at the start of the string (per json encodync) to avoid replacing for non-local domains    
                            *   e.g. "collectionThumbnail":"https:\/\/wp.envatoextensions.com\/kit-57\/wp-content\/uploads\/sites\/60\/2018\/08\/screenshot-20-1540279812-300x997.jpg"
                            */
                            $replace_now[ '"' .  trim( json_encode( trim( $old_url, '"')), '"' ) ]  =   '"' . trim( json_encode( trim ( $new_url, '"')), '"' );               

                            $replace_now[ '"' . trim( urlencode(trim( $old_url, '"')), '"' ) ]  =  '"' . trim( urlencode(trim ( $new_url, '"')), '"' );
                        }

                    /**
                    * Restore absolute paths
                    */
                    $replace_now    =   array_merge ( $replace_now , array ( 
                                                                            '%W-P-H-PLACEHOLDER-PRESERVE-ABSPATH%'                =>  ABSPATH,
                                                                            //jsonencoded
                                                                            '%W-P-H-PLACEHOLDER-PRESERVE-JSON-ABSPATH%'           =>  trim(json_encode(ABSPATH), '"'),
                                                                            //urlencode
                                                                            '%W-P-H-PLACEHOLDER-PRESERVE-URLENCODE-ABSPATH%'      =>  trim(urlencode(ABSPATH), '"')     
                                                                            ) );
                    
                    $replace_now    =   apply_filters( 'wp-hide/content_urls_replacement/replacement_list', $replace_now, $replacements );
                    
                    $text           =   str_ireplace (   array_keys ( $replace_now ),    array_values ( $replace_now ), $text );                      
                   
                    $text           =   apply_filters( 'wp-hide/content_urls_replacement', $text, $replacements ); 
                                      
                    return $text;
                       
                }
                
                
            
            /**
            * Replace preserved links
            * 
            * @param mixed $text
            * @param mixed $replacements
            */
            function content_preserved_urls_replacement( $text, $replacements )
                {
                    $text =   str_ireplace(    array_keys($replacements), array_values($replacements)  ,$text   );
                       
                    return $text;
                       
                }
                
            
            function default_scripts_styles_replace($object, $replacements)
                {
                    //update default dirs
                    if(isset($object->default_dirs))
                        {
                            foreach($object->default_dirs    as  $key    =>  $value)
                                {
                                    $object->default_dirs[$key]  =   str_replace(array_keys($replacements), array_values($replacements), $value);
                                }
                        }
                       
                    foreach($object->registered    as  $script_name    =>  $script_data)
                        {
                            $script_data->src   =   str_replace(array_keys($replacements), array_values($replacements), $script_data->src);
                            
                            $object->registered[$script_name]  =   $script_data;      
                        }
                        
                    return $object;
                }
                
                
            function check_headers_content_type($header_name, $header_value)
                {
                    
                    $headers    =   headers_list();
                    
                    foreach($headers    as  $header)
                        {
                            if(stripos($header, $header_name)   !== FALSE)
                                {
                                    if(stripos($header, $header_value)   !== FALSE)
                                        return TRUE;     
                                }
                        }
                        
                    
                    return FALSE;
                
                }
                
                
            function array_sort_by_processing_order($a, $b)
                {
                    return $a['processing_order'] - $b['processing_order'];
                }
            
            function array_sort_by_rewrite_processing_order($a, $b)
                {
                    return $a['rewrite_processing_order'] - $b['rewrite_processing_order'];
                }
            
            
            /**
            * Return the recovey code
            * 
            */
            function get_recovery_code()
                {
                    $blog_id_settings   =   $this->get_blog_id();
                    
                    $settings   =   $this->get_site_settings( $blog_id_settings );
                        
                    $recovery_code  =   isset ( $settings['recovery_code'] ) ?  $settings['recovery_code']  :   '';
                    
                    if(empty($recovery_code))
                        {
                            
                            $recovery_code              =   $this->generate_recovery_code();
                            $settings['recovery_code']  =   $recovery_code;
                            
                            $this->update_site_settings( $settings, $blog_id_settings );
                        }
                    
                    return $recovery_code;
                }
            
            
            /**
            * Generate a recovery code
            * 
            */
            function generate_recovery_code()
                {

                    $recovery_code  =   md5(rand(1,9999) . microtime());
                                       
                    return $recovery_code;
                }
                
                
            /**
            * Trigger the recovery actions
            * 
            */
            function do_recovery()
                {
                    //prevent hammering
                    sleep(2);
                    
                    //feetch a new set of settings
                    $recovery_code  =   $this->get_recovery_code();
                    
                    $wph_recovery   =   isset($_GET['wph-recovery']) ?  preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['wph-recovery'] )   :   '';
                    if(empty($wph_recovery) ||  $wph_recovery   !=  $recovery_code)
                        return;
                    
                    $resetOnlyHeaders   =   isset ( $_GET['reset_headers'] )    &&  $_GET['reset_headers']  ==  '1'    ?      TRUE: FALSE;
                    
                    $blog_id_settings   =   $this->get_blog_id();
                    
                    $settings   =   $this->get_site_settings( $blog_id_settings );
                                           
                    $modules_settings   =   array();
                    
                    if ( $resetOnlyHeaders === TRUE )
                        {
                            $modules_settings   =   $settings['module_settings'];
                            
                            $headers    =   array ( 
                                            'cross_origin_embedder_policy',
                                            'cross_origin_opener_policy',
                                            'cross_origin_resource_policy',
                                            'content_security_policy',
                                            'content_security_policy_report_only',
                                            'expect_ct',
                                            'feature_policy',
                                            'referrer-policy',
                                            'strict_transport_security',
                                            'x_content_type_options',
                                            'x_download_options',
                                            'x_frame_options',
                                            'x_permitted_cross_domain_policies',
                                            'x_xss_protection'                                            
                                            );
                            foreach ( $headers as $header )
                                {
                                    if ( ! isset ( $modules_settings[ $header ] )   ||  ! is_array ( $modules_settings[ $header ]  ) )
                                        $modules_settings[ $header ]   =   array (
                                                                                'enabled'   =>  'no' 
                                                                                );
                                    
                                    $modules_settings[ $header ]['enabled']    =   'no';
                                }    
                        }
                        else
                        {
                            foreach($this->wph->modules   as  $module)
                                {
                                    //proces the fields
                                    $module_settings    =   $this->filter_settings(   $module->get_module_components_settings(), TRUE    );
                                    
                                    foreach($module_settings as $module_setting)
                                        {
                                            if(isset($module_setting['type'])   &&  $module_setting['type'] ==  'split')
                                                continue;
                                            
                                            $field_name =   $module_setting['id'];
                                            
                                            $value      =   isset($module_setting['default_value'])  ?   $module_setting['default_value'] :   '';
                                 
                                            //save the value
                                            $modules_settings[ $field_name ]  =   $value;
                                        }   
                                }
                        }
                             
                    $settings['module_settings']    =   $modules_settings;
                    
                    //update the settings
                    $this->update_site_settings( $settings, $blog_id_settings );  
                    
                    $global_settings    =   $this->get_global_settings ( );
                    $global_settings['self_setup'] = 'no';

                    $this->update_global_settings( $global_settings );
                    
                    delete_option( 'wph-previous-login-url' );
                    
                    //available for mu-plugins
                    do_action( 'wph/do_recovery' );
                          
                    //add filter for rewriting the rules
                    if ( $resetOnlyHeaders === TRUE )
                        add_action('wp_loaded',  array($this,    'wp_loaded_trigger_do_recovery_headers'));
                        else
                        add_action('wp_loaded',  array($this,    'wp_loaded_trigger_do_recovery'));
                    
                }
            
                
            function wp_loaded_trigger_do_recovery()
                {
                    /** WordPress Misc Administration API */
                    require_once(ABSPATH . 'wp-admin/includes/misc.php');
                    
                    /** WordPress Administration File API */
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    
                    flush_rewrite_rules();
                        
                    ?><!DOCTYPE html>
                    <html lang="en-US">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        <meta name="viewport" content="width=device-width">
                        <meta name='robots' content='noindex,follow' />
                        <title>WP-Hide - <?php _e('Recovery', 'wp-hide-security-enhancer') ?></title>
                        <style type="text/css">
                            html{background:#f1f1f1}body{background:#fff;color:#444;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.13);box-shadow:0 1px 3px rgba(0,0,0,.13)}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font-size:24px;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}#error-page .wp-die-message,#error-page p{font-size:14px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:14px}a{color:#0073aa}a:active,a:hover{color:#006799}a:focus{color:#124964;-webkit-box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);outline:0}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:13px;line-height:2;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:0 1px 0 #ccc;box-shadow:0 1px 0 #ccc;vertical-align:top}.button.button-large{height:30px;line-height:2.15384615;padding:0 12px 2px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#23282d}.button:focus{border-color:#5b9dd9;-webkit-box-shadow:0 0 3px rgba(0,115,170,.8);box-shadow:0 0 3px rgba(0,115,170,.8);outline:0}.button:active{background:#eee;border-color:#999;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}    
                        </style>
                    </head>                            
                    <body>
  
                        <h1>WP-Hide - <?php _e('Recovery', 'wp-hide-security-enhancer') ?></h1>
                        <p><b><?php _e('The plugin options have been reset successfully.', 'wp-hide-security-enhancer') ?></b></p>
                        <br />
                        <?php
                        
                        if (  $this->wph->server_htaccess_config  === TRUE )
                            {
                                ?>
                                <p><?php _e('Ensure the .htaccess file does not contain any WP-Hide rewrite lines. The plugin already attempts to clear the lines, if the operation fails, they are required to be removed manually. ', 'wp-hide-security-enhancer') ?></p>
                                <?php 
                            }
                            
                        if (  $this->wph->server_web_config  === TRUE )
                            {
                                ?>
                                <p><?php _e('Ensure the web.config file does not contain any WP-Hide rewrite lines. The plugin already attempts to clear the lines, if the operation fails, they are required to be removed manually. ', 'wp-hide-security-enhancer') ?></p>
                                <?php 
                            }
                            
                        if (  $this->wph->server_nginx_config  === TRUE )
                            {
                                
                                //Check if use Wpengine
                                if (    $this->wph->functions->server_is_wpengine() )
                                    {
                                        ?>
                                        <p><?php _e('Your site use WPEngine! You need to get in touch with live support and ask to remove the custom Nginx rewrite code from your account.', 'wp-hide-security-enhancer') ?></p>
                                        <?php
                                    }
                                else if (    $this->wph->functions->server_is_kinsta() )
                                    {
                                        ?>
                                        <p><?php _e('Your site use Kinsta! You need to get in touch with live support and ask to remove the custom Nginx rewrite code from your account.', 'wp-hide-security-enhancer') ?></p>
                                        <?php
                                    }
                                    else
                                    {
                                
                                        ?>
                                        <p><?php _e('Check with your Nginx config file located usually at', 'wp-hide-security-enhancer') ?> /etc/nginx/sites-available/ <?php _e('and remove any existing rewrite rules within', 'wp-hide-security-enhancer') ?> <strong># BEGIN WP Hide & Security Enhancer</strong> <?php _e('and', 'wp-hide-security-enhancer') ?> <strong># END WP Hide & Security Enhancer</strong></p>
                                        <p><?php _e('After config file updated', 'wp-hide-security-enhancer') ?>, <strong><?php _e('Test', 'wp-hide-security-enhancer') ?></strong> <?php _e('the new data using ', 'wp-hide-security-enhancer') ?> <strong>nginx -t</strong>. <?php _e('If successfully compiled, restart the Nginx service.', 'wp-hide-security-enhancer') ?></p>
                                        <?php
                                    } 
                            }
                            
                        
                        
                        ?>
                                                    
                        <p><br /></p>
                        <p><a class="button" href="<?php echo get_site_url() ?>"><?php _e('Continue to your Site', 'wp-hide-security-enhancer') ?></a></p>
                 
                    
                    </body>
                    </html>
                    <?php
                    
                    $this->rewrite_applied_correctly_to_site();   
                    wp_logout();
                        
                    die();
      
                }
            
            
            function wp_loaded_trigger_do_recovery_headers()
                {
                    /** WordPress Misc Administration API */
                    require_once(ABSPATH . 'wp-admin/includes/misc.php');
                    
                    /** WordPress Administration File API */
                    require_once(ABSPATH . 'wp-admin/includes/file.php');
                    
                    flush_rewrite_rules();
                        
                    ?><!DOCTYPE html>
                    <html lang="en-US">
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                        <meta name="viewport" content="width=device-width">
                        <meta name='robots' content='noindex,follow' />
                        <title>WP-Hide - <?php _e('Recovery', 'wp-hide-security-enhancer') ?></title>
                        <style type="text/css">
                            html{background:#f1f1f1}body{background:#fff;color:#444;font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Oxygen-Sans,Ubuntu,Cantarell,"Helvetica Neue",sans-serif;margin:2em auto;padding:1em 2em;max-width:700px;-webkit-box-shadow:0 1px 3px rgba(0,0,0,.13);box-shadow:0 1px 3px rgba(0,0,0,.13)}h1{border-bottom:1px solid #dadada;clear:both;color:#666;font-size:24px;margin:30px 0 0 0;padding:0;padding-bottom:7px}#error-page{margin-top:50px}#error-page .wp-die-message,#error-page p{font-size:14px;line-height:1.5;margin:25px 0 20px}#error-page code{font-family:Consolas,Monaco,monospace}ul li{margin-bottom:10px;font-size:14px}a{color:#0073aa}a:active,a:hover{color:#006799}a:focus{color:#124964;-webkit-box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);box-shadow:0 0 0 1px #5b9dd9,0 0 2px 1px rgba(30,140,190,.8);outline:0}.button{background:#f7f7f7;border:1px solid #ccc;color:#555;display:inline-block;text-decoration:none;font-size:13px;line-height:2;height:28px;margin:0;padding:0 10px 1px;cursor:pointer;-webkit-border-radius:3px;-webkit-appearance:none;border-radius:3px;white-space:nowrap;-webkit-box-sizing:border-box;-moz-box-sizing:border-box;box-sizing:border-box;-webkit-box-shadow:0 1px 0 #ccc;box-shadow:0 1px 0 #ccc;vertical-align:top}.button.button-large{height:30px;line-height:2.15384615;padding:0 12px 2px}.button:focus,.button:hover{background:#fafafa;border-color:#999;color:#23282d}.button:focus{border-color:#5b9dd9;-webkit-box-shadow:0 0 3px rgba(0,115,170,.8);box-shadow:0 0 3px rgba(0,115,170,.8);outline:0}.button:active{background:#eee;border-color:#999;-webkit-box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5);box-shadow:inset 0 2px 5px -3px rgba(0,0,0,.5)}    
                        </style>
                    </head>                            
                    <body>
  
                        <h1>WP-Hide - <?php _e('Headers Recovery', 'wp-hide-security-enhancer') ?></h1>
                        <p><b><?php _e('The plugin Headers options have been disabled successfully.', 'wp-hide-security-enhancer') ?></b></p>
                        <br />
                        <?php
                        
                        if (  $this->wph->server_htaccess_config  === TRUE )
                            {
                                ?>
                                <p><?php _e('Ensure the .htaccess file does not contain any rewrite Header lines. The plugin already attempted to clear the data. If the operation fails, manual removal is required.', 'wp-hide-security-enhancer') ?></p>
                                <?php 
                            }
                            
                        if (  $this->wph->server_web_config  === TRUE )
                            {
                                ?>
                                <p><?php _e('Ensure the web.config file does not contain any rewrite Header lines. The plugin already attempted to clear the data. If the operation fails, manual removal is required.', 'wp-hide-security-enhancer') ?></p>
                                <?php 
                            }
                            
                        if (  $this->wph->server_nginx_config  === TRUE )
                            {
                                
                                //Check if use Wpengine
                                if (    $this->wph->functions->server_is_wpengine() )
                                    {
                                        ?>
                                        <p><?php _e('Your site use WPEngine! You need to get in touch with live support and ask to remove the custom Nginx Header rewrite code from your account.', 'wp-hide-security-enhancer') ?></p>
                                        <?php
                                    }
                                else if (    $this->wph->functions->server_is_kinsta() )
                                    {
                                        ?>
                                        <p><?php _e('Your site use Kinsta! You need to get in touch with live support and ask to remove the custom Nginx Header rewrite code from your account.', 'wp-hide-security-enhancer') ?></p>
                                        <?php
                                    }
                                    else
                                    {
                                
                                        ?>
                                        <p><?php _e('Check with your Nginx config file located usually at', 'wp-hide-security-enhancer') ?> /etc/nginx/sites-available/ <?php _e('and remove any Header rewrite rules within', 'wp-hide-security-enhancer') ?> <strong># BEGIN WP Hide & Security Enhancer</strong> <?php _e('and', 'wp-hide-security-enhancer') ?> <strong># END WP Hide & Security Enhancer</strong></p>
                                        <p><?php _e('After the configuration file update', 'wp-hide-security-enhancer') ?>, <strong><?php _e('Test', 'wp-hide-security-enhancer') ?></strong> <?php _e('the new data using ', 'wp-hide-security-enhancer') ?> <strong>nginx -t</strong>. <?php _e('If successfully compiled, restart the Nginx service.', 'wp-hide-security-enhancer') ?></p>
                                        <?php
                                    } 
                            }
                            
                        
                        
                        ?>
                                                    
                        <p><br /></p>
                        <p><a class="button" href="<?php echo get_site_url() ?>"><?php _e('Continue to your Site', 'wp-hide-security-enhancer') ?></a></p>
                 
                    
                    </body>
                    </html>
                    <?php
                    
                    $this->rewrite_applied_correctly_to_site();   
                    wp_logout();
                        
                    die();
      
                }
            
            
            
            /**
            * Attempt to write the custom nginx rewrite data to a file on the site root
            * 
            */
            function write_nginx_rewrites_to_files()
                {
                    
                    $global_settings    =   $this->get_global_settings ( );    
                    
                    global $wp_filesystem;
                    
                    $error_messages =   array();
                        
                    if ( empty( $wp_filesystem ) ) 
                        {
                            // Initialize the WordPress filesystem
                            require_once ABSPATH . '/wp-admin/includes/file.php';
                            WP_Filesystem();
                        }

                    include_once( WPH_PATH . '/include/class.rewrite-process.php' );
                    $rewrite_process            =   new WPH_Rewrite_Process( TRUE );
                    $readable_processed_rewrite =   $rewrite_process->get_readable_rewrite_data();
                    
                    $file_path = $this->get_home_path() . DIRECTORY_SEPARATOR . 'wphide-firewall-nginx.conf';
                    
                    //check for any firewall rules                    
                    if (    isset( $readable_processed_rewrite['firewall'] ) )
                        {           
                            $write_content       =   $readable_processed_rewrite['firewall'];
                            
                            // Attempt to update the file
                            $write_result = $wp_filesystem->put_contents( $file_path, $write_content, FS_CHMOD_FILE );

                            // Check if the update operation was successful
                            if ( ! $write_result ) 
                                {
                                    $message    =   __('Acordingly the "Save the Nginx Rewrites to a wphide-firewall-nginx.conf file" option, the system encountered an unsuccessful attempt to write the custom Nginx rewrites to the file. Could you check if this file is writable?', 'wp-hide-security-enhancer');
                                    update_site_option( 'wph-errors-nginx_rewrites_to_file', $message);
                                    
                                    return;
                                }
                        }
                        else
                        {
                            $write_content  =   '';
                            
                            // Attempt to update the file
                            $write_result = $wp_filesystem->put_contents( $file_path, $write_content, FS_CHMOD_FILE );

                            // Check if the update operation was successful
                            if ( ! $write_result ) 
                                {
                                    $message    =   __('Acordingly the "Save the Nginx Rewrites to a wphide-firewall-nginx.conf file" option, the system encountered an unsuccessful attempt to reset the wphide-firewall-nginx.conf file. Could you check if this file is writable?', 'wp-hide-security-enhancer');
                                    update_site_option( 'wph-errors-nginx_rewrites_to_file', $message);
                                    
                                    return;
                                }   
                        }

                    if ( $global_settings['nginx_save_rewrite_to_file'] ==  'yes' )
                        {                        
                            $file_path = $this->get_home_path() . DIRECTORY_SEPARATOR . 'wphide-nginx.conf';
                                          
                            if (    isset($readable_processed_rewrite['default_variables']) ||  isset($readable_processed_rewrite['location']) ||  isset($readable_processed_rewrite['header']) ||  isset($readable_processed_rewrite['firewall_conditionals']) )
                                {    
                                    $write_content       =   isset($readable_processed_rewrite['default_variables'])? $readable_processed_rewrite['default_variables'] :   '';
                                    $write_content      .=   isset($readable_processed_rewrite['location'])? $readable_processed_rewrite['location'] :   '';
                                    
                                    if ( $global_settings['nginx_headers_save_rewrite_to_file'] !=  'yes' )
                                        $write_content      .=   isset($readable_processed_rewrite['header'])? $readable_processed_rewrite['header'] :   '';
                                        
                                    $write_content      .=   isset($readable_processed_rewrite['firewall_conditionals'])? $readable_processed_rewrite['firewall_conditionals'] :   '';
                                    
                                    //remove duplicated comments
                                    $write_content   =   str_replace( "# END WP Hide & Security Enhancer\n# BEGIN WP Hide & Security Enhancer\n", "", $write_content );
                                }


                            // Attempt to update the file
                            $write_result = $wp_filesystem->put_contents( $file_path, $write_content, FS_CHMOD_FILE );

                            // Check if the update operation was successful
                            if ( ! $write_result ) 
                                {
                                    $message    =   __('Acordingly the "Save the Nginx Rewrites to a wphide-nginx.conf file" option, the system encountered an unsuccessful attempt to write the custom Nginx rewrites to the file. Could you check if this file is writable?', 'wp-hide-security-enhancer');
                                    update_site_option( 'wph-errors-nginx_rewrites_to_file', $message);
                                }
                        }
                        
                        
                    if ( $global_settings['nginx_headers_save_rewrite_to_file'] ==  'yes' )
                        {                        
                            $file_path = $this->get_home_path() . DIRECTORY_SEPARATOR . 'wphide-nginx-headers.conf';
                                          
                            if (    isset($readable_processed_rewrite['default_variables']) ||  isset($readable_processed_rewrite['location']) ||  isset($readable_processed_rewrite['header']) )
                                {    
                                    $write_content      =   isset($readable_processed_rewrite['header'])? $readable_processed_rewrite['header'] :   '';
                                    
                                    //remove duplicated comments
                                    $write_content   =   str_replace( "# END WP Hide & Security Enhancer\n# BEGIN WP Hide & Security Enhancer\n", "", $write_content );
                                }


                            // Attempt to update the file
                            $write_result = $wp_filesystem->put_contents( $file_path, $write_content, FS_CHMOD_FILE );

                            // Check if the update operation was successful
                            if ( ! $write_result ) 
                                {
                                    $message    =   __('Acordingly the "Save the Nginx Rewrites to a wphide-nginx-headers.conf file" option, the system encountered an unsuccessful attempt to write the custom Nginx rewrites to the file. Could you check if this file is writable?', 'wp-hide-security-enhancer');
                                    update_site_option( 'wph-errors-nginx_rewrites_to_file', $message);
                                }
                        }
                       
                }
            
            
            
            
            
            /**
            * Check if filter / action exists for anonymous object
            * 
            * @param mixed $tag
            * @param mixed $class
            * @param mixed $method
            */
            function anonymous_object_filter_exists($tag, $class, $method)
                {
                    if ( !  isset( $GLOBALS['wp_filter'][$tag] ) )
                        return FALSE;
                    
                    $filters = $GLOBALS['wp_filter'][$tag];
                    
                    if ( !  $filters )
                        return FALSE;
                        
                    foreach ( $filters as $priority => $filter ) 
                        {
                            foreach ( $filter as $identifier => $function ) 
                                {
                                    if ( ! is_array( $function ) )
                                        continue;
                                    
                                    if ( ! $function['function'][0] instanceof $class )
                                        continue;
                                    
                                    if ( $method == $function['function'][1] ) 
                                        {
                                            return TRUE;
                                        }
                                }
                        }
                        
                    return FALSE;
                }
            
            /**
            * Replace a filter / action from anonymous object
            * 
            * @param mixed $tag
            * @param mixed $class
            * @param mixed $method
            * @param mixed $priority
            */
            function remove_anonymous_object_filter( $tag, $class, $method, $priority = '' ) 
                {
                    $filters = false;

                    if ( isset( $GLOBALS['wp_filter'][$tag] ) )
                        $filters = $GLOBALS['wp_filter'][$tag];

                    if ( $filters )
                    foreach ( $filters as $filter_priority => $filter ) 
                        {
                            if ( ! empty ( $priority )  &&   $priority != $filter_priority )
                                continue;
                                
                            foreach ( $filter as $identifier => $function ) 
                                {                                   
                                    if ( ! isset ( $function['function'] ) || ! is_array ( $function['function'] ) )
                                        continue;
                                    
                                    if ( is_string( $function['function'][0] )  &&  $function['function'][0]    == $class   &&  $function['function'][1]    ==  $method )
                                        remove_filter($tag, array( $function['function'][0], $method ), $filter_priority );
                                    else if ( is_object( $function['function'][0] )  &&  get_class( $function['function'][0] )    == $class   &&  $function['function'][1]    ==  $method ) 
                                        remove_filter($tag, array( $function['function'][0], $method ), $filter_priority );
                                }
                        }
                }
            
            /**
            * return class instance
            * 
            * @param mixed $component_class_name
            */
            function return_component_instance( $component_class_name )
                {
                    
                    foreach ( $this->wph->modules   as  $priority   =>  $data )
                        {
                            if ( is_array ( $data->components ) &&  count ( $data->components ) > 0 )
                                {
                                    foreach ( $data->components     as  $component )
                                        {
                                            if ( get_class( $component )    ==  $component_class_name )
                                                return $component;
                                        }
                                }
                        }
                    
                    return FALSE;
                       
                }
                                                  
        
            /**
            * Check the plugins directory and retrieve all plugin files with plugin data.
            *
            * WordPress only supports plugin files in the base plugins directory
            * (wp-content/plugins) and in one directory above the plugins directory
            * (wp-content/plugins/my-plugin). The file it looks for has the plugin data
            * and must be found in those two locations. It is recommended to keep your
            * plugin files in their own directories.
            *
            * The file with the plugin data is the file that will be included and therefore
            * needs to have the main execution for the plugin. This does not mean
            * everything must be contained in the file and it is recommended that the file
            * be split for maintainability. Keep everything in one file for extreme
            * optimization purposes.
            *
            * @since 1.5.0
            *
            * @param string $plugin_folder Optional. Relative path to single plugin folder.
            * @return array Key is the plugin file path and the value is an array of the plugin data.
            */
            function get_plugins($plugin_folder = '') 
                {
                 
                    $wp_plugins = array ();
                    $plugin_root = WP_PLUGIN_DIR;
                    if ( !empty($plugin_folder) )
                        $plugin_root .= $plugin_folder;

                    // Files in wp-content/plugins directory
                    $plugins_dir = @ opendir( $plugin_root);
                    $plugin_files = array();
                    if ( $plugins_dir ) {
                        while (($file = readdir( $plugins_dir ) ) !== false ) {
                            if ( substr($file, 0, 1) == '.' )
                                continue;
                            if ( is_dir( $plugin_root.'/'.$file ) ) {
                                $plugins_subdir = @ opendir( $plugin_root.'/'.$file );
                                if ( $plugins_subdir ) {
                                    while (($subfile = readdir( $plugins_subdir ) ) !== false ) {
                                        if ( substr($subfile, 0, 1) == '.' )
                                            continue;
                                        if ( substr($subfile, -4) == '.php' )
                                            $plugin_files[] = "$file/$subfile";
                                    }
                                    closedir( $plugins_subdir );
                                }
                            } else {
                                if ( substr($file, -4) == '.php' )
                                    $plugin_files[] = $file;
                            }
                        }
                        closedir( $plugins_dir );
                    }

                    if ( empty($plugin_files) )
                        return $wp_plugins;

                    foreach ( $plugin_files as $plugin_file ) {
                        if ( !is_readable( "$plugin_root/$plugin_file" ) )
                            continue;

                        $plugin_data = $this->get_plugin_data( "$plugin_root/$plugin_file", false, false ); //Do not apply markup/translate as it'll be cached.

                        if ( empty ( $plugin_data['Name'] ) )
                            continue;

                        $wp_plugins[plugin_basename( $plugin_file )] = $plugin_data;
                    }

                    uasort( $wp_plugins, array($this, '_sort_uname_callback' ));
                    
                    return $wp_plugins;
                }
                
                
            
            /**
            * Callback to sort array by a 'Name' key.
            * 
            */
            function _sort_uname_callback( $a, $b ) 
                {
                    return strnatcasecmp( $a['Name'], $b['Name'] );
                }
                
            
            /**
            * Parse plugin headers data
            *     
            * @param mixed $plugin_file
            * @param mixed $markup
            * @param mixed $translate
            */
            function get_plugin_data( $plugin_file, $markup = true, $translate = true ) 
                {

                    $default_headers = array(
                        'Name' => 'Plugin Name',
                        'PluginURI' => 'Plugin URI',
                        'Version' => 'Version',
                        'Description' => 'Description',
                        'Author' => 'Author',
                        'AuthorURI' => 'Author URI',
                        'TextDomain' => 'Text Domain',
                        'DomainPath' => 'Domain Path',
                        'Network' => 'Network',
                        // Site Wide Only is deprecated in favor of Network.
                        '_sitewide' => 'Site Wide Only',
                    );

                    $plugin_data = get_file_data( $plugin_file, $default_headers, 'plugin' );

                    // Site Wide Only is the old header for Network
                    if ( ! $plugin_data['Network'] && $plugin_data['_sitewide'] ) {
                        /* translators: 1: Site Wide Only: true, 2: Network: true */
                        _deprecated_argument( __FUNCTION__, '3.0', sprintf( __( 'The %1$s plugin header is deprecated. Use %2$s instead.' ), '<code>Site Wide Only: true</code>', '<code>Network: true</code>' ) );
                        $plugin_data['Network'] = $plugin_data['_sitewide'];
                    }
                    $plugin_data['Network'] = ( 'true' == strtolower( $plugin_data['Network'] ) );
                    unset( $plugin_data['_sitewide'] );

                    if ( $markup || $translate ) {
                        $plugin_data = $this->_get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup, $translate );
                    } else {
                        $plugin_data['Title']      = $plugin_data['Name'];
                        $plugin_data['AuthorName'] = $plugin_data['Author'];
                    }

                    return $plugin_data;
                }
                
                
                
            /**
            * Sanitizes plugin data, optionally adds markup, optionally translates.
            *
            * @since 2.7.0
            * @access private
            * @see get_plugin_data()
            */
            function _get_plugin_data_markup_translate( $plugin_file, $plugin_data, $markup = true, $translate = true ) 
                {

                    // Sanitize the plugin filename to a WP_PLUGIN_DIR relative path
                    $plugin_file = plugin_basename( $plugin_file );

                    // Translate fields
                    if ( $translate ) {
                        if ( $textdomain = $plugin_data['TextDomain'] ) {
                            if ( ! is_textdomain_loaded( $textdomain ) ) {
                                if ( $plugin_data['DomainPath'] ) {
                                    load_plugin_textdomain( $textdomain, false, dirname( $plugin_file ) . $plugin_data['DomainPath'] );
                                } else {
                                    load_plugin_textdomain( $textdomain, false, dirname( $plugin_file ) );
                                }
                            }
                        } elseif ( 'hello.php' == basename( $plugin_file ) ) {
                            $textdomain = 'default';
                        }
                        if ( $textdomain ) {
                            foreach ( array( 'Name', 'PluginURI', 'Description', 'Author', 'AuthorURI', 'Version' ) as $field )
                                $plugin_data[ $field ] = translate( $plugin_data[ $field ], $textdomain );
                        }
                    }

                    // Sanitize fields
                    $allowed_tags = $allowed_tags_in_links = array(
                        'abbr'    => array( 'title' => true ),
                        'acronym' => array( 'title' => true ),
                        'code'    => true,
                        'em'      => true,
                        'strong'  => true,
                    );
                    $allowed_tags['a'] = array( 'href' => true, 'title' => true );

                    // Name is marked up inside <a> tags. Don't allow these.
                    // Author is too, but some plugins have used <a> here (omitting Author URI).
                    $plugin_data['Name']        = wp_kses( $plugin_data['Name'],        $allowed_tags_in_links );
                    $plugin_data['Author']      = wp_kses( $plugin_data['Author'],      $allowed_tags );

                    $plugin_data['Description'] = wp_kses( $plugin_data['Description'], $allowed_tags );
                    $plugin_data['Version']     = wp_kses( $plugin_data['Version'],     $allowed_tags );

                    $plugin_data['PluginURI']   = esc_url( $plugin_data['PluginURI'] );
                    $plugin_data['AuthorURI']   = esc_url( $plugin_data['AuthorURI'] );

                    $plugin_data['Title']      = $plugin_data['Name'];
                    $plugin_data['AuthorName'] = $plugin_data['Author'];

                    // Apply markup
                    if ( $markup ) {
                        if ( $plugin_data['PluginURI'] && $plugin_data['Name'] )
                            $plugin_data['Title'] = '<a href="' . $plugin_data['PluginURI'] . '">' . $plugin_data['Name'] . '</a>';

                        if ( $plugin_data['AuthorURI'] && $plugin_data['Author'] )
                            $plugin_data['Author'] = '<a href="' . $plugin_data['AuthorURI'] . '">' . $plugin_data['Author'] . '</a>';

                        $plugin_data['Description'] = wptexturize( $plugin_data['Description'] );

                        if ( $plugin_data['Author'] )
                            $plugin_data['Description'] .= ' <cite>' . sprintf( __('By %s.'), $plugin_data['Author'] ) . '</cite>';
                    }

                    return $plugin_data;
                }
                
                
            /**
            * Alternative when apache_response_headers() not available
            * 
            */
            function parseRequestHeaders() 
                {
                    $headers = array();
                    foreach($_SERVER as $key => $value) 
                        {
                            if (substr($key, 0, 5) <> 'HTTP_') 
                                continue;
                                
                            $header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
                            $headers[$header] = $value;
                        }
                    
                    return $headers;
                }
                
                
            /**
            * Attempt to update the outputed headers
            * 
            * @param mixed $headers
            * @param mixed $response_headers
            */
            function update_headers( $headers, $response_headers )
                {
                    
                    $replacement_list   =   $this->get_replacement_list();
                    
                    foreach ( $headers as $header )
                        {
                            if(isset($response_headers[ $header ]))
                                {
                                    $header_value   =   $response_headers[ $header ];
                                    $new_header_value   =   $this->content_urls_replacement($header_value,  $replacement_list );
                                    
                                    if($header_value    !=  $new_header_value)
                                        {
                                            header_remove("Location");
                                            header( 'Location: ' . $new_header_value );
                                        }
                                }
                        }
                    
                }
            
            
            
            /**
            * Check if current content is filterable, depending on header content type
            * 
            */
            function is_filterable_content_type()
                {
                   
                    $is_filterable  =   TRUE;

                    $headers_content_type    =   $this->get_headers_list_content_type();
                    
                    if ( $headers_content_type ===  FALSE )
                        return $is_filterable;
                    
                    $allow_type    =   array(
                                                'text/plain',
                                                'text/css',
                                                'text/html',
                                                'text/csv',
                                                'application/javascript',
                                                'text/javascript',
                                                'application/json'
                                                );
                    if  ( ! in_array( $headers_content_type , $allow_type ) )
                        $is_filterable  =   FALSE;
                        
                    return $is_filterable;    
                    
                }
                
                
            function get_headers_list_content_type()
                {
                    $headers        =   headers_list();
                    
                    //there is no header to check
                    if  ( ! is_array( $headers )  ||  count ( $headers ) < 1 )
                        return FALSE;
                        

                    $found  =   preg_grep('/^Content-Type\s?:.*/i', $headers);
                    if  ( ! is_array ( $found ) ||    count ( $found ) <  1   )
                        return FALSE;
                        
                    reset( $found );
                    $header_field           =   $headers[ key( $found ) ];
                    $header_field           =   preg_replace('/Content-Type\s?:/i', '', $header_field);
                    $header_field           =   trim ( $header_field );
                    $header_field_parts     =   explode(";", $header_field);
                    $header_content_type    =   trim( $header_field_parts[0] );   
                    
                    return $header_content_type;
                }
            
            
            /**
            * Get available themes
            * 
            * @param mixed $args
            */
            function get_themes( $args = array() ) 
                {
                    global $wp_theme_directories;

                    $defaults = array( 'errors' => false, 'allowed' => null, 'blog_id' => 0 );
                    $args = wp_parse_args( $args, $defaults );

                    if  ( is_null($wp_theme_directories))
                        $wp_theme_directories   =   array();    
                    
                    // Register the default theme directory root
                    if ( count( $wp_theme_directories ) < 1  ) 
                        register_theme_directory( get_theme_root() );
                    
                    $theme_directories = search_theme_directories();

                    if ( count( $wp_theme_directories ) > 1 ) {
                        // Make sure the current theme wins out, in case search_theme_directories() picks the wrong
                        // one in the case of a conflict. (Normally, last registered theme root wins.)
                        $current_theme = get_stylesheet();
                        if ( isset( $theme_directories[ $current_theme ] ) ) {
                            $root_of_current_theme = get_raw_theme_root( $current_theme );
                            if ( ! in_array( $root_of_current_theme, $wp_theme_directories ) )
                                $root_of_current_theme = WP_CONTENT_DIR . $root_of_current_theme;
                            $theme_directories[ $current_theme ]['theme_root'] = $root_of_current_theme;
                        }
                    }

                    if ( empty( $theme_directories ) )
                        return array();

                    if ( is_multisite() && null !== $args['allowed'] ) {
                        $allowed = $args['allowed'];
                        if ( 'network' === $allowed )
                            $theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed_on_network() );
                        elseif ( 'site' === $allowed )
                            $theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed_on_site( $args['blog_id'] ) );
                        elseif ( $allowed )
                            $theme_directories = array_intersect_key( $theme_directories, WP_Theme::get_allowed( $args['blog_id'] ) );
                        else
                            $theme_directories = array_diff_key( $theme_directories, WP_Theme::get_allowed( $args['blog_id'] ) );
                    }

                    return $theme_directories;
                    
                }
            
            
            /**
            * Parse available themes headers
            * 
            */
            function parse_themes_headers( $all_templates )
                {
                    
                    if ( ! is_array($all_templates) )
                        return $all_templates;
                    
                    foreach( $all_templates as  $directory  =>  $theme_data)
                        {
                            $theme_style_path   =   trailingslashit( $theme_data['theme_root']) . $theme_data['theme_file'];
                            
                            if ( ! file_exists( $theme_style_path ))
                                continue;
                                   
                            $theme_headers      =   $this->get_theme_headers( $theme_style_path );
                            $all_templates[$directory]['headers']   =  $theme_headers;
                            
                        }
                    
                    return $all_templates;
                       
                }
            
            
            
            /**
            * Return headers for a theme
            * 
            * @param mixed $stylesheet_path
            */
            function get_theme_headers($stylesheet_path)
                {
                    
                    $file_headers = array(
                                            'Name'        => 'Theme Name',
                                            'ThemeURI'    => 'Theme URI',
                                            'Description' => 'Description',
                                            'Author'      => 'Author',
                                            'AuthorURI'   => 'Author URI',
                                            'Version'     => 'Version',
                                            'Template'    => 'Template',
                                            'Status'      => 'Status',
                                            'Tags'        => 'Tags',
                                            'TextDomain'  => 'Text Domain',
                                            'DomainPath'  => 'Domain Path',
                                        );
                    
                    $theme_headers = get_file_data( $stylesheet_path, $file_headers, 'theme' );   
                    
                    return $theme_headers;
                    
                }
            
            
            /**
            * Return if a theme is child or not
            * 
            * @param mixed $theme_slug
            * @param mixed $all_themes
            */
            function is_child_theme($theme_slug, $all_themes)
                {
                    if ( ! isset ( $all_themes[ $theme_slug ] ) )
                        return FALSE; 
                           
                    $theme_data =   $all_themes[ $theme_slug ];
                        
                    if( isset($theme_data['headers']['Template']) &&  !empty($theme_data['headers']['Template']))
                        return TRUE;
                        
                    return FALSE;
                      
                }
                
                
            /**
            * Return main theme directory slug
            * 
            * @param mixed $theme_slug
            * @param mixed $all_themes
            */
            function get_main_theme_directory($theme_slug, $all_themes)
                {
                    if ( ! isset ( $all_themes[ $theme_slug ] ) )
                        return FALSE; 
                             
                    $theme_data         =   $all_themes[$theme_slug];
                    $theme_directory    =   $theme_slug;
                    
                    if( isset($theme_data['headers']['Template']) &&  !empty($theme_data['headers']['Template']))
                        {
                            $theme_directory    =   $theme_data['headers']['Template'];
                        }        
                    
                    return $theme_directory;
                    
                }
            
            
            
            function get_site_template_data( )
                {
                              
                    $data   =   array();
                    
                    $data['themes_url']                 =   site_url() . $this->wph->default_variables['templates_directory'];
                    
                    $all_templates  =   $this->get_themes();
                    $all_templates  =   $this->parse_themes_headers($all_templates);
                    
                    $stylesheet     =   get_option( 'stylesheet' );
                                        
                    $data['use_child_theme']            =   $this->is_child_theme($stylesheet, $all_templates);
                    
                    $main_theme_directory                               =   $this->get_main_theme_directory($stylesheet, $all_templates);
                    $data['main']                       =   array();
                    $data['main']['folder_name']        =   $main_theme_directory;
                    
                    if($data['use_child_theme'])
                        {
                            $data['child']         =   array();        
                            $data['child']['folder_name']  =   $stylesheet;
                        }
                        
                    return $data;
                    
                }
            
            
            /**
            * Recreate a url from a parsed array
            * 
            * @param mixed $parts
            */
            function build_parsed_url( $parse_url )
                {
                    $url    =   (isset($parse_url['scheme']) ? "{$parse_url['scheme']}:" : '') . 
                                ((isset($parse_url['user']) || isset($parse_url['host'])) ? '//' : '') . 
                                (isset($parse_url['user']) ? "{$parse_url['user']}" : '') . 
                                (isset($parse_url['pass']) ? ":{$parse_url['pass']}" : '') . 
                                (isset($parse_url['user']) ? '@' : '') . 
                                (isset($parse_url['host']) ? "{$parse_url['host']}" : '') . 
                                (isset($parse_url['port']) ? ":{$parse_url['port']}" : '') . 
                                (isset($parse_url['path']) ? "{$parse_url['path']}" : '') . 
                                (isset($parse_url['query']) ? "?{$parse_url['query']}" : '') . 
                                (isset($parse_url['fragment']) ? "#{$parse_url['fragment']}" : '');
   
                    return $url;
                    
                }
            
            
            
            /**
            * Return upload paths and dirs
            * 
            */
            function get_wp_upload_dir()
                {
                    
                    global $blog_id;
                    
                    $siteurl = get_option( 'siteurl' );
                    $upload_path = trim( get_option( 'upload_path' ) );

                    if ( empty( $upload_path ) || 'wp-content/uploads' == $upload_path ) {
                        $dir = WP_CONTENT_DIR . '/uploads';
                    } elseif ( 0 !== strpos( $upload_path, ABSPATH ) ) {
                        // $dir is absolute, $upload_path is (maybe) relative to ABSPATH
                        $dir = path_join( ABSPATH, $upload_path );
                    } else {
                        $dir = $upload_path;
                    }

                    
                    if(is_multisite())
                        {
                            $blog_details = get_blog_details( $blog_id );
                            
                            $protocol   =   (is_ssl())  ?   'https://' :   'http://';
                            
                            if ( empty($upload_path) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) )
                                    $url = $protocol . $blog_details->domain . $blog_details->path . ltrim($this->wph->default_variables['network']['content_path'], '/') .'/uploads';
                                else
                                    $url = $protocol . $blog_details->domain . $blog_details->path . $upload_path;    
                        }
                        else
                        {
                            if ( !$url = get_option( 'upload_url_path' ) ) 
                                {
                                    if ( empty($upload_path) || ( 'wp-content/uploads' == $upload_path ) || ( $upload_path == $dir ) )
                                        $url = WP_CONTENT_URL . '/uploads';
                                    else
                                        $url = trailingslashit( $siteurl ) . $upload_path;
                                }
                        }

                    /*
                     * Honor the value of UPLOADS. This happens as long as ms-files rewriting is disabled.
                     * We also sometimes obey UPLOADS when rewriting is enabled -- see the next block.
                     */
                    if ( defined( 'UPLOADS' ) && ! ( is_multisite() && get_site_option( 'ms_files_rewriting' ) ) ) {
                        $dir = ABSPATH . UPLOADS;
                        $url = trailingslashit( $siteurl ) . UPLOADS;
                    }

                    // If multisite (and if not the main site in a post-MU network)
                    if ( is_multisite() && ! ( is_main_network() && is_main_site() && defined( 'MULTISITE' ) ) ) {

                        if ( ! get_site_option( 'ms_files_rewriting' ) ) {
                            /*
                             * If ms-files rewriting is disabled (networks created post-3.5), it is fairly
                             * straightforward: Append sites/%d if we're not on the main site (for post-MU
                             * networks). (The extra directory prevents a four-digit ID from conflicting with
                             * a year-based directory for the main site. But if a MU-era network has disabled
                             * ms-files rewriting manually, they don't need the extra directory, as they never
                             * had wp-content/uploads for the main site.)
                             */

                            if ( defined( 'MULTISITE' ) )
                                $ms_dir = '/sites/' . get_current_blog_id();
                            else
                                $ms_dir = '/' . get_current_blog_id();

                            $dir .= $ms_dir;
                            $url .= $ms_dir;

                        } elseif ( defined( 'UPLOADS' ) && ! ms_is_switched() ) {
                            /*
                             * Handle the old-form ms-files.php rewriting if the network still has that enabled.
                             * When ms-files rewriting is enabled, then we only listen to UPLOADS when:
                             * 1) We are not on the main site in a post-MU network, as wp-content/uploads is used
                             *    there, and
                             * 2) We are not switched, as ms_upload_constants() hardcodes these constants to reflect
                             *    the original blog ID.
                             *
                             * Rather than UPLOADS, we actually use BLOGUPLOADDIR if it is set, as it is absolute.
                             * (And it will be set, see ms_upload_constants().) Otherwise, UPLOADS can be used, as
                             * as it is relative to ABSPATH. For the final piece: when UPLOADS is used with ms-files
                             * rewriting in multisite, the resulting URL is /files. (#WP22702 for background.)
                             */

                            if ( defined( 'BLOGUPLOADDIR' ) )
                                $dir = untrailingslashit( BLOGUPLOADDIR );
                            else
                                $dir = ABSPATH . UPLOADS;
                            $url = trailingslashit( $siteurl ) . 'files';
                        }
                    }

                    $basedir = $dir;
                    $baseurl = $url;

                    $subdir = '';
                    if ( get_option( 'uploads_use_yearmonth_folders' ) ) {
                        // Generate the yearly and monthly dirs
                        $time   = current_time( 'mysql' );
                        $y      = substr( $time, 0, 4 );
                        $m      = substr( $time, 5, 2 );
                        $subdir = "/$y/$m";
                    }

                    $dir .= $subdir;
                    $url .= $subdir;

                    return array(
                        'path'    => wp_normalize_path ($dir),
                        'url'     => $url,
                        'subdir'  => $subdir,
                        'basedir' => wp_normalize_path ($basedir),
                        'baseurl' => $baseurl,
                        'error'   => false,
                    );    
                }
                
            /**
            * Return active blogs where the plugin is available
            * 
            */
            function ms_get_plugin_active_blogs()
                {
                    
                    $plugin_slug    =   'wp-hide-security-enhancer-pro/wp-hide.php';
                       
                    $args   =   array(
                                        'public'    =>  1,
                                        'archived'  =>  0,
                                        'spam'      =>  0,
                                        'deleted'   =>  0,
                                        'limit'     =>  9999
                                        );
                    
                    $network_sites  =   get_sites( $args );
                    
                    if ( !function_exists( 'get_plugins' ) )
                        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    //check if plugin available to all sites, being network active
                    if(is_plugin_active_for_network( $plugin_slug ))
                        return $network_sites;
                    
                    //filter out the sites where plugin is not active
                    if ( !function_exists( 'is_plugin_active' ) )
                        include_once(ABSPATH.'wp-admin/includes/plugin.php');
                    
                    foreach ( $network_sites    as   $key   =>  $network_site )
                        {
                    
                            switch_to_blog( $network_site->blog_id );
                            
                            if ( ! is_plugin_active( $plugin_slug ) )
                                unset ( $network_sites[ $key ] );
                            
                            restore_current_blog();
                        }
                        
                    //reindex
                    $network_sites  =   array_values($network_sites);
                    
                    return $network_sites;    
                    
                }

            /**
            * Create a Lock functionality using the MySql 
            * 
            * @param mixed $lock_name
            * @param mixed $release_timeout
            * 
            * @return bool False if a lock couldn't be created or if the lock is still valid. True otherwise.
            */
            function create_lock( $lock_name, $release_timeout = null ) 
                {
                    
                    global $wpdb, $blog_id;
                    
                    if ( ! $release_timeout ) {
                        $release_timeout = 10;
                    }
                    $lock_option = $lock_name . '.lock';
                                     
                    // Try to lock.
                    $lock_result = $wpdb->query( $wpdb->prepare( "INSERT INTO `". $wpdb->sitemeta ."` (`site_id`, `meta_key`, `meta_value`) 
                                                                    SELECT %s, %s, %s FROM DUAL
                                                                    WHERE NOT EXISTS (SELECT * FROM `". $wpdb->sitemeta ."` 
                                                                          WHERE `meta_key` = %s AND `meta_value` != '') 
                                                                    LIMIT 1", $blog_id, $lock_option, time(), $lock_option) );
                                        
                    if ( ! $lock_result ) 
                        {
                            $lock_result    =   $this->get_lock( $lock_option );

                            // If a lock couldn't be created, and there isn't a lock, bail.
                            if ( ! $lock_result ) {
                                return false;
                            }

                            // Check to see if the lock is still valid. If it is, bail.
                            if ( $lock_result > ( time() - $release_timeout ) ) {
                                return false;
                            }

                            // There must exist an expired lock, clear it and re-gain it.
                            $this->release_lock( $lock_name );

                            return $this->create_lock( $lock_name, $release_timeout );
                        }

                    // Update the lock, as by this point we've definitely got a lock, just need to fire the actions.
                    $this->update_lock( $lock_option, time() );

                    return true;
                    
                }

            
            /**
            * Retrieve a lock value
            * 
            * @param mixed $lock_name
            * @param mixed $return_full_row
            */
            private function get_lock( $lock_name, $return_full_row =   FALSE )
                {
                    
                    global $wpdb;
                    
                    $mysq_query =   $wpdb->get_row( $wpdb->prepare("SELECT `site_id`, `meta_key`, `meta_value` FROM  `". $wpdb->sitemeta ."`
                                                                    WHERE `meta_key`    =   %s", $lock_name ) );
                    
                    
                    if ( $return_full_row   === TRUE )
                        return $mysq_query;
                        
                    if ( is_object($mysq_query) && isset ( $mysq_query->meta_value ) )
                        return $mysq_query->meta_value;
                        
                    return FALSE;
                    
                }
                
                
            /**
            * Update lock value
            *     
            * @param mixed $lock_name
            * @param mixed $lock_value
            */
            private function update_lock( $lock_name, $lock_value )
                {
                    
                    global $wpdb;
                    
                    $mysq_query =   $wpdb->query( $wpdb->prepare("UPDATE `". $wpdb->sitemeta ."` 
                                                                    SET meta_value = %s
                                                                    WHERE meta_key = %s", $lock_value, $lock_name) );
                    
                    
                    return $mysq_query;
                    
                }
                
            
            /**
            * Releases an upgrader lock.
            *
            * @param string $lock_name The name of this unique lock.
            * @return bool True if the lock was successfully released. False on failure.
            */
            function release_lock( $lock_name ) 
                {
                    
                    global $wpdb;
                    
                    $lock_option = $lock_name . '.lock';
                    
                    $mysq_query =   $wpdb->query( $wpdb->prepare( "DELETE FROM `". $wpdb->sitemeta ."` 
                                                                    WHERE meta_key = %s", $lock_option ) );
                    
                    return $mysq_query;
                    
                }
                
                
            
            
            /**
            * Delete an opition from all sites
            * 
            * @param mixed $option_name
            */
            function delete_all_sites_option( $option_name )
                {
                        global  $wpdb;
                        
                        $active_sites   =   $this->ms_get_plugin_active_blogs();
                        
                        foreach ( $active_sites as  $active_site) 
                            {
                                $mysql_query    =   "DELETE FROM " . $wpdb->base_prefix . ( $active_site->id > 1 ?  $active_site->id .'_' : '') . "options
                                                            WHERE option_name   =   '". $option_name  ."'";
                                $results   =   $wpdb->get_results( $mysql_query );
                            }
                    
                }
                
                
                
            /**
            * Save the current options list for all sites, to be used further, if any seting changes and rewrite still not applied
            * 
            */
            function  save_current_options_list( $_blog_id = '' )
                {
                    if ( empty ( $_blog_id ) )
                        {
                            global $blog_id; 
                            $_blog_id   =   $blog_id;
                        }
                    
                    $site_modules_settings  =   $this->get_site_modules_settings( $_blog_id );
                    
                    if ( $_blog_id  ==  'network' )                                   
                        update_site_option('wph-previous-options-list', $site_modules_settings);
                        else
                        update_option('wph-previous-options-list', $site_modules_settings);
                    
                }
                
            function save_all_sites_options_list()
                {
                    $active_sites   =   $this->ms_get_plugin_active_blogs();

                    foreach ( $active_sites as  $active_site) 
                        {
                            
                            switch_to_blog( $active_site->blog_id );
                            
                            $this->save_current_options_list( );
                            
                            restore_current_blog();
                            
                        }    
                    
                }
                
                
            
            /**
            * Check any POST actions for this plugin
            * 
            */
            function check_post_actions()
                {
                    
                    //check for rewrite-update-confirm action within SETUP interface
                    if( isset( $_POST['wph-action'] )   &&  $_POST['wph-action']    ==  'ruc'  &&  isset($_POST['_nonce'])  &&  wp_verify_nonce( $_POST['_nonce'], 'ruc-nonce' ) )
                        {
 
                            global $blog_id;
                            
                            $response       =   array();
                            $found_error    =   FALSE;
                            
                            if (is_multisite() )
                                {
                                    
                                    $ms_settings    =   $this->get_site_settings('network');

                                    if ( $this->wph->server_nginx_config   === TRUE )
                                        {
                            
                                            $nginx_rewrite_status =   $this->nginx_test_sample_rewrite(); 
                                                            
                                            if  ( is_array ( $nginx_rewrite_status )    ||  $nginx_rewrite_status   === FALSE ) 
                                                {
                                                    $found_error            =   TRUE;
                                                    $response['status']     =   'error';
                                                    $response['message']    =   __('The Confirmation failed:', 'wp-hide-security-enhancer');
                                                    if ( is_array ( $nginx_rewrite_status ) &&  count ( $nginx_rewrite_status ) > 0 )
                                                        $response['message']    .=   "\n" . __('The system returned the following errors: ', 'wp-hide-security-enhancer') . implode ( ".\n", $nginx_rewrite_status );
                                                        else
                                                        {
                                                            $response['message']   .=   "\n" . __('- The Rewrites does not appear to apply! Ensure the custom lines are placed in correct file and at correct spot.', 'wp-hide-security-enhancer');
                                                            $response['message']   .=   "\n" . __('- The Nginx service is required to be restarted.', 'wp-hide-security-enhancer');
                                                            $response['message']   .=   "\n" . __('- If your site require Basic Authentication ( HTTP password ) the procedure may fail.', 'wp-hide-security-enhancer');
                                                        }
                                                }
                                                else
                                                {
                                                    delete_site_option( 'wph-rewrite-manual-install' );
                                                    delete_site_option( 'wph-errors-rewrite-to-file' );
                                                    delete_site_option( 'wph-errors-nginx_rewrites_to_file' );
                                    
                                                    $this->save_current_options_list( 'network' );
                                                    $response['status'] =   'success';    
                                                    
                                                }
                                        }
                                        else
                                        {
                                            //nothing to check
                                            delete_site_option( 'wph-rewrite-manual-install' );
                                            delete_site_option( 'wph-errors-rewrite-to-file' );
                                            delete_site_option( 'wph-errors-nginx_rewrites_to_file' );
                            
                                            $this->save_current_options_list( 'network' );
                                            $response['status'] =   'success';         
                                            
                                        }
                   
                                        
                                }
                                else
                                {    
                                    if ( $this->wph->server_nginx_config   === TRUE )
                                        {
                                                
                                            $nginx_rewrite_status =   $this->nginx_test_sample_rewrite(); 
                                                                 
                                            if  ( is_array ( $nginx_rewrite_status )    ||  $nginx_rewrite_status   === FALSE ) 
                                                {
                                                    $found_error            =   TRUE;
                                                    $response['status']     =   'error';
                                                    $response['message']    =   __('The Confirmation failed:', 'wp-hide-security-enhancer');
                                                    
                                                    if ( is_array ( $nginx_rewrite_status ) &&  count ( $nginx_rewrite_status ) > 0 )
                                                        $response['message']    .=   "\n" . __('The system returned the following errors: ', 'wp-hide-security-enhancer') . implode ( ".\n", $nginx_rewrite_status );
                                                        else
                                                        {
                                                            $response['message']   .=   "\n" . __('- The Rewrites does not appear to apply! Ensure the custom lines are placed in correct file and at correct spot.', 'wp-hide-security-enhancer');
                                                            $response['message']   .=   "\n" . __('- The Nginx service is required to be restarted.', 'wp-hide-security-enhancer');
                                                            $response['message']   .=   "\n" . __('- If your site require Basic Authentication ( HTTP password ) the procedure may fail.', 'wp-hide-security-enhancer'); 
                                                        }
                                                }
                                                else
                                                {
                                                        
                                                    delete_site_option( 'wph-rewrite-manual-install' );
                                                    delete_site_option( 'wph-errors-rewrite-to-file' );
                                                    delete_site_option( 'wph-errors-nginx_rewrites_to_file' );
                                    
                                                    $this->save_current_options_list( 'network' );
                                                            
                                                    $response['status'] =   'success';
                                                }
                                        }
                                        else
                                        {
                                            $settings   =   $this->get_site_settings($blog_id);
                                            
                                            $get_write_check_string_from_server =   $this->get_write_check_string_from_server();
                                            $write_check_string =   isset($settings['write_check_string']) ?    $settings['write_check_string'] :   '';
                                            if( !empty($write_check_string))
                                                {
                                                    if ( $get_write_check_string_from_server ==  $write_check_string )
                                                        {
                                                            $this->rewrite_applied_correctly_to_site();
                                                            $response['status'] =   'success';
                                                        }
                                                        else
                                                        {   
                                                            $found_error            =   TRUE;
                                                            $response['status']     =   'error';
                                                            $response['message']    =   __('Unable to retrieve specific environment variables. Please check again the rewrite data on your server.', 'wp-hide-security-enhancer');
                                                        }
                                                }
                                                else
                                                {
                                                    if ( empty ( $get_write_check_string_from_server ) )
                                                        {
                                                            $this->rewrite_applied_correctly_to_site();
                                                            $response['status'] =   'success';
                                                        }
                                                        else
                                                        {   
                                                            $found_error            =   TRUE;
                                                            $response['status']     =   'error';
                                                            $response['message']    =   __('Unable to retrieve specific environment variables. Please check again the rewrite data on your server.', 'wp-hide-security-enhancer');
                                                        }  
                                                }
                                        }
                                }    
                            
                            echo json_encode( $response );
                            
                            if ( $found_error   === FALSE )
                                {
                                    $readable_processed_rewrite_hash    =   get_site_option ( '__wph_transient_rewrite_hash' );
                                    if ( ! empty ( $readable_processed_rewrite_hash ) )
                                        {
                                            update_site_option( 'wph_rewrite_hash', $readable_processed_rewrite_hash );
                                            delete_site_option ( '__wph_transient_rewrite_hash' );
                                        }
                                        
                                    wp_logout();
                                }
                            
                            die();
                            
                        }
                    
                }
                
                
            /**
            * Try to access a specific sample url to test the rewrite engine functinality
            * 
            */
            function nginx_test_sample_rewrite()
                {
                    
                    //check for forced confirmation
                    if  ( isset ( $_REQUEST['force_confirm'] ) )
                        return TRUE;
                                             
                    $global_settings    =   $this->get_global_settings ( );
                    
                    $response_errors    =   array();
                    
                    $test_url   =   apply_filters( 'wp-hide/nginx_test_sample_rewrite/url', trailingslashit ( site_url() ) . $global_settings['sample_rewrite_hash'] . '/rewrite_test' );   
                    $response   = wp_remote_get( $test_url, array(
                                                                        'sslverify' => false,
                                                                    )); 
                    
                    if ( is_array( $response ) ) 
                        {
                            
                            if  ( ! isset( $response['response']['code'] ) )
                                return FALSE;
                                   
                            if  ( $response['response']['code'] !=  200 )
                                {
                                    $response_errors[]  =   __( 'The server returned an unespected response code:', 'wp-hide-security-enhancer') . ' ' . $response['response']['code'];
                                    return $response_errors;
                                }
                                
                            $body       =   json_decode( $response['body'] );
                            if ( $body  === null || !isset($body->name) )
                                return FALSE;
                                
                                
                            return TRUE;
                                
                        }
                        else if ( is_a( $response, 'WP_Error' ))
                        {
                            $error_messages = $response->get_error_messages();
                            foreach ( $error_messages as $error_message ) 
                                {
                                    $response_errors[]  =   $error_message;
                                }
                            
                            if ( count ( $response_errors )   >   0 )
                                return $response_errors;
                                
                            return FALSE;
                        }
                          
                    return FALSE;
                
                }    
                
            
            /**
            * Apply appropiate code for site where the rewrite appear to be correct
            *     
            */
            function rewrite_applied_correctly_to_site( )
                {
                    
                    $blog_id_settings   =   $this->get_blog_id();
                    
                    delete_site_option('wph-rewrite-manual-install');
                    delete_site_option('wph-errors-rewrite-to-file');   
                    delete_site_option( 'wph-errors-nginx_rewrites_to_file' );

                    $this->save_current_options_list( $this->get_blog_id() );
                    
                }
                
            
            /**
            * Specific cache code to run on cron trigger
            * 
            */
            function do_cron_cache()
                {
                    
                    //Disabled
                    
                    
                }
                
            
            /**
            * Return the cached file name 
            * 
            * @param mixed $settings_hash
            * @param mixed $content_hash
            */
            function cache_get_file_name( $settings_hash, $content_hash )
                {
                    $wph_cache_file_format  =   get_site_option ( 'wph_cache_file_format');
                    
                    if (    $wph_cache_file_format  ==  '2')
                        $file_name  =   hash('crc32', $settings_hash . '_' . $content_hash, FALSE);
                        else
                        $file_name  =   $settings_hash . '_' . $content_hash;
                    
                    return $file_name;
                }
                
            
            /**
            * Clear the cache
            * 
            */
            function do_cache_clear()
                {
                    $nonce  =   $_POST['_wpnonce'];
                    if ( ! wp_verify_nonce( $nonce, 'wp-hide-cache-clear' ) )
                        return FALSE;   
                    
                    //only for admins
                    If ( !  current_user_can ( 'manage_options' ) )
                        return FALSE;
                        
                    $this->cache_clear();
                    
                }
                
                
            /**
            * Get cache size
            * 
            */
            function get_cache_size()
                {
                    
                    $dir        =   WPH_CACHE_PATH;
                    $cache_size =   0;
                    
                    if ( is_dir( $dir ) ) 
                        {
                            $objects = scandir( $dir );
                            
                            foreach ($objects as $object) 
                                {
                                    if ( is_file( $dir    .   $object ))
                                        $cache_size++;
                                }
                        }
                
                    
                    return $cache_size;                    
                    
                }
                
            
            
            /**
            * Internal cache clear
            * 
            */
            function cache_clear()
                {
                    
                    do_action('wp-hide/before_cache_clear');
                        
                    $this->rrmdir( WPH_CACHE_PATH, TRUE );
                    
                    //clear any plugin cache data
                    $this->site_cache_clear();
                    
                    do_action('wp-hide/after_cache_clear');   

                }
                
                
            /**
            * Clear any cache plugins
            *     
            */
            function site_cache_clear()
                {
                    $cleared_cache  =   FALSE;
                    
                    if ( function_exists('wp_cache_clear_cache'))
                        {
                            wp_cache_clear_cache();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( function_exists('w3tc_flush_all'))
                        {
                            w3tc_flush_all();
                            $cleared_cache  =   TRUE;
                        }
                        
                    if ( function_exists('opcache_reset')    &&  ! ini_get( 'opcache.restrict_api' ) )
                        {
                            @opcache_reset();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( function_exists( 'rocket_clean_domain' ) )
                        {
                            rocket_clean_domain();
                            $cleared_cache  =   TRUE;
                        }
                        
                    if ( function_exists('wp_cache_clear_cache')) 
                        {
                            wp_cache_clear_cache();
                            $cleared_cache  =   TRUE;
                        }
                
                    global $wp_fastest_cache;
                    if ( method_exists( 'WpFastestCache', 'deleteCache' ) && !empty( $wp_fastest_cache ) )
                        {
                            $wp_fastest_cache->deleteCache();
                            $cleared_cache  =   TRUE;
                        }
                
                    //If your host has installed APC cache this plugin allows you to clear the cache from within WordPress
                    if ( function_exists('apc_clear_cache'))
                        {
                            apc_clear_cache();
                            $cleared_cache  =   TRUE;
                        }
                        
                    if ( function_exists('fvm_purge_all'))
                        {
                            fvm_purge_all();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( class_exists( 'autoptimizeCache' ) )     
                        {
                            autoptimizeCache::clearall();
                            $cleared_cache  =   TRUE;
                        }

                    //WPEngine
                    if ( class_exists( 'WpeCommon' ) ) 
                        {
                            if ( method_exists( 'WpeCommon', 'purge_memcached' ) )
                                WpeCommon::purge_memcached();
                            if ( method_exists( 'WpeCommon', 'clear_maxcdn_cache' ) )
                                WpeCommon::clear_maxcdn_cache();
                            if ( method_exists( 'WpeCommon', 'purge_varnish_cache' ) )
                                WpeCommon::purge_varnish_cache();
                            
                            $cleared_cache  =   TRUE;
                        }
                        
                    if (class_exists('Cache_Enabler_Disk') && method_exists('Cache_Enabler_Disk', 'clear_cache'))
                        {
                            Cache_Enabler_Disk::clear_cache();
                            $cleared_cache  =   TRUE;
                        }
                        
                    //Perfmatters
                    if ( class_exists('Perfmatters\CSS') && method_exists('Perfmatters\CSS', 'clear_used_css') )
                        {
                            Perfmatters\CSS::clear_used_css();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( defined( 'BREEZE_VERSION' ) )
                        {
                            do_action( 'breeze_clear_all_cache' );
                            $cleared_cache  =   TRUE;
                        }
                        
                    if ( function_exists('sg_cachepress_purge_everything'))
                        {
                            sg_cachepress_purge_everything();
                            $cleared_cache  =   TRUE;
                        }
                    
                    if ( defined ( 'FLYING_PRESS_VERSION' ) )
                        {
                            do_action('flying_press_purge_everything:before');

                            @unlink(FLYING_PRESS_CACHE_DIR . '/preload.txt');

                            // Delete all files and subdirectories
                            $this->rrmdir( FLYING_PRESS_CACHE_DIR );

                            @mkdir(FLYING_PRESS_CACHE_DIR, 0755, true);

                            do_action('flying_press_purge_everything:after');
                            
                            $cleared_cache  =   TRUE;
                        }
                        
                    if (class_exists('\LiteSpeed\Purge'))
                        {
                            \LiteSpeed\Purge::purge_all();
                            $cleared_cache  =   TRUE;
                        }
                        
                    return $cleared_cache;
                        
                }
            
            
            
            /**
            * Recursivelly remove all fodlers and files within a directory
            * 
            * @param mixed $dir
            */
            function rrmdir( $path, $xclude_parent   =   FALSE ) 
                {
                    if ( !is_dir($path))
                        return false;


                    $files = array_diff(scandir($path), array('.', '..'));

                    foreach ($files as $file) 
                    {
                        $filePath = $path . '/' . $file;

                        if (is_dir($filePath)) {
                            $this->rrmdir($filePath);
                            rmdir($filePath);
                        } else {
                            unlink($filePath);
                        }
                    }

                    if( is_dir($path)   &&  $xclude_parent   !== TRUE)
                        rmdir( $path );
                }
                
            
            /**
            * Filter width htmlspecialchars_decode for multidimensional array 
            *     
            * @param mixed $value
            */
            function filter_htmlspecialchars_decode(    &$value )
                {
                    
                    $value = htmlspecialchars_decode($value);
                        
                }
                
                
            
            
            /**
            * Return the home path relative to domain base
            * e.g. http://develop.com/dev/wp-hide  returns /dev/
            * dev - is a folder
            * wp-hide - is the wordpress install directory; 
            * home option is http://develop.com/dev; siteurl is http://develop.com/dev/wp-hide
            * 
            * if WPML installed, it returns /dev/en/
            * 
            */
            function get_home_root()
                {
                    
                    if(is_multisite())
                        {
                            $slashed_home      = trailingslashit( network_site_url() );
                            $home_root         = parse_url( $slashed_home, PHP_URL_PATH );   
                            
                        }
                        else
                        {
                            $home_root = parse_url(home_url());
                            if ( isset( $home_root['path'] ) )
                                    $home_root = trailingslashit($home_root['path']);
                                else
                                    $home_root = '/';
                        }
                        
                    return $home_root;   
                    
                }
            
            
            /**
            * Return the domain site path base
            * 
            * e.g. http://develop.com/dev/wp-hide  returns /dev/wp-hide
            * dev - is a folder
            * wp-hide - is the wordpress install directory
            * home option is http://develop.com/dev; siteurl is http://develop.com/dev/wp-hide
            * 
            */
            function get_domain_site_path()
                {
                    $domain_site_url    =   '/';    
                    
                    $domain_site_url    .=  $this->wph->default_variables['site_relative_path'];
                    $domain_site_url    .=  $this->wph->default_variables['wordpress_directory'];
                    
                    $domain_site_url    =   preg_replace( '/\/+/', '/', $domain_site_url );
                    
                    return $domain_site_url;   
                }
                
                
            /**
            * Return the domain home path base
            * 
            * e.g. http://develop.com/dev/wp-hide  returns /dev
            * dev - is a folder
            * wp-hide - is the wordpress install directory
            * home option is http://develop.com/dev; siteurl is http://develop.com/dev/wp-hide
            * 
            */
            function get_domain_home_path()
                {
                    $domain_site_url    =   '/';    
                    
                    $domain_site_url    .=  $this->wph->default_variables['site_relative_path'];
                    
                    $domain_site_url    =   preg_replace( '/\/+/', '/', $domain_site_url );
                    
                    return $domain_site_url;   
                }
            
            
            
            function prepare_path( $path )
                {
                    $path   =   str_replace( '://', '!###!', $path );
                    $path   =   preg_replace( '/\/+/', '/', $path );
                    $path   =   str_replace( '!###!', '://', $path );
                    
                    return $path;
                }
            
                
                
            /**
            * Return a list of curent site domain/domains
            * 
            */
            function get_instance_domains()
                {
                    //use cached if exists
                    if ( is_array ( $this->wph->instance_domains )  &&  count ( $this->wph->instance_domains ) > 0 )
                        return $this->wph->instance_domains;
                        
                           
                    if (is_multisite())
                        {
                            $sites_to_process   =   $this->ms_get_plugin_active_blogs();
                            foreach( $sites_to_process   as  $site_to_process )
                                {
                                    if ( array_search( $site_to_process->domain, $this->wph->instance_domains ) === FALSE )
                                        $this->wph->instance_domains[]   =   $site_to_process->domain;                
                                }
                        
                        }
                        else
                        {
                            $domain =   untrailingslashit ( preg_replace('/:[0-9]+/', '', str_replace(array ("https://" , "http://"), "", site_url() )) );
                            $this->wph->instance_domains[]   =   $domain;
                        }
                        
                        
                    return $this->wph->instance_domains;   
                }
                
            
            /**
            * Retrieve a system environment value
            * 
            * @param mixed $environment_name
            */
            function get_phpinfo_data ( $environment_name, $category = 'Default' )
                {
                    
                    $php_info_array =   $this->phpinfo_to_array();
                    
                    if ( $php_info_array    === FALSE )
                        return FALSE;
                        
                    if ( ! isset ( $php_info_array [ $category ] ) )
                        return FALSE;
                        
                    if ( isset ( $php_info_array [ $category ][ $environment_name ] ) )
                        return $php_info_array [ $category ][ $environment_name ];
                        else
                        return FALSE;     
                                       
                }
                
                
            /**
            * Check if the groups are in the stack of callers
            * 
            * e.g.
            * array ( 
            *           array ( 'create_attachment', 'WP_Job_Manager_Form_Submit_Job') , 
            *           array ('validate_fields', 'WP_Job_Manager_Form_Submit_Job') 
            * )
            * 
            * @param mixed $groups
            */
            function check_backtrace_for_caller( $groups )
                {
                    $backtrace  =   debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                    foreach ( $groups   as $group )
                        {
                            $function_name      =   $group[0]; 
                            $class_name         =   isset ( $group[1] ) ?   $group[1]   :   FALSE;
                            
                            foreach ( $backtrace as  $block )
                                {
                                    if ( $block['function']    ==  $function_name )
                                        {
                                            if ( $class_name    ===  FALSE )
                                                return TRUE;
                                            
                                            if ( $class_name    !=  FALSE   &&  !isset( $block['class'] ) )
                                                return FALSE;
                                                
                                            if ( $block['class']    ==  $class_name )
                                                return TRUE;
                                            
                                            return FALSE;
                                            
                                        }
                                
                                }
                        }
                        
                    return FALSE;
                }
                
            
            /**
            * Return the phpinfo data into an array
            *     
            */
            function phpinfo_to_array( $module = INFO_ALL )
                {
                    
                    if ( ! function_exists( 'phpinfo' ) )
                        return FALSE;
                    
                    ob_start();
                    
                    phpinfo( $module );
                    
                    $php_info_array     = array();
                    $info_lines         = explode("\n", strip_tags(ob_get_clean(), "<tr><td><h2>"));
                    $cat                = "Default";
                    
                    foreach(    $info_lines as $line    )
                        {
                            preg_match("~<h2>(.*)</h2>~", $line, $title) ? $cat = $title[1] : null;
                            if(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val))
                                {
                                    $php_info_array[$cat][ trim ( $val[1] ) ] = trim ( $val[2] );
                                }
                            elseif(preg_match("~<tr><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td><td[^>]+>([^<]*)</td></tr>~", $line, $val))
                                {
                                    $php_info_array[$cat][ trim ( $val[1] ) ] = array("local" => trim ( $val[2] ), "master" => trim ( $val[3]) );
                                }
                        }
                    return $php_info_array;
                }
         
            
            /**
            * Safe Print_r to be used inside buffering
            *     
            * @param mixed $var
            * @param mixed $return
            * @param mixed $html
            * @param mixed $level
            */
            function obsafe_print_r($var, $return = false, $html = false, $level = 0) 
                {
                    $spaces = "";
                    $space = $html ? "&nbsp;" : " ";
                    $newline = $html ? "<br />" : "\n";
                    for ($i = 1; $i <= 6; $i++) {
                        $spaces .= $space;
                    }
                    $tabs = $spaces;
                    for ($i = 1; $i <= $level; $i++) {
                        $tabs .= $spaces;
                    }
                    if (is_array($var)) {
                        $title = "Array";
                    } elseif (is_object($var)) {
                        $title = get_class($var)." Object";
                    }
                    $output = $title . $newline . $newline;
                    foreach($var as $key => $value) {
                        if (is_array($value) || is_object($value)) {
                            $level++;
                            $value = $this->obsafe_print_r($value, true, $html, $level);
                            $level--;
                        }
                        $output .= $tabs . "[" . $key . "] => " . $value . $newline;
                    }
                    if ($return) return $output;
                      else echo $output;
                }
                
            
            /**
            * Save a message log to a debug file
            *     
            * @param mixed $text
            */
            function log_save($text)
                {
                    
                    $myfile     = fopen(WPH_PATH . "/debug.txt", "a") or die("Unable to open file!");
                    $txt        =  $text   .   "\n";
                    fwrite($myfile, $txt);
                    fclose($myfile);   
                    
                }
            
               
        }
        
?>