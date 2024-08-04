<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite extends WPH_module
        {
                
            function load_components()
                {
                    
                    //add components
                    include_once(WPH_PATH . "/modules/components/rewrite-default.php");
                    $this->components[]  =   new WPH_module_rewrite_default();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-wp_content_path.php");
                    $this->components[]  =   new WPH_module_rewrite_wp_content_path();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-wp_includes_path.php");
                    $this->components[]  =   new WPH_module_rewrite_new_include_path();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-theme_path.php");
                    $this->components[]  =   new WPH_module_rewrite_new_theme_path();
                                        
                    include_once(WPH_PATH . "/modules/components/rewrite-plugin_path.php");
                    $this->components[]  =   new WPH_module_rewrite_new_plugin_path();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-mu-plugins.php");
                    $this->components[]  =   new WPH_module_rewrite_mu_plugins();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-upload_path.php");
                    $this->components[]  =   new WPH_module_rewrite_new_upload_path();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-comments.php");
                    $this->components[]  =   new WPH_module_rewrite_comments();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-author.php");
                    $this->components[]  =   new WPH_module_rewrite_author();
                    
                    //include_once(WPH_PATH . "/modules/components/rewrite-search.php");
                    //$this->components[]  =   new WPH_module_rewrite_search();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-xml-rpc-path.php");
                    $this->components[]  =   new WPH_module_rewrite_new_xml_rpc_path();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-json-rest.php");
                    $this->components[]  =   new WPH_module_rewrite_json_rest();
                    
                    include_once(WPH_PATH . "/modules/components/general-feed.php");
                    $this->components[]  =   new WPH_module_general_feed(); 
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-registration.php");
                    $this->components[]  =   new WPH_module_rewrite_registration();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-root-files.php");
                    $this->components[]  =   new WPH_module_rewrite_root_files();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-slash.php");
                    $this->components[]  =   new WPH_module_rewrite_slash();
                    
                    include_once(WPH_PATH . "/modules/components/rewrite-map_custom_urls.php");
                    $this->components[]  =   new WPH_module_rewrite_map_custom_urls();
                    
                    
                    //action available for mu-plugins
                    do_action('wp-hide/module_load_components', $this);
                    
                }
            
            function use_tabs()
                {
                    
                    return TRUE;
                }
            
            function get_module_id()
                {
                    return 'rewrite';
                }
                
            function get_module_slug()
                {
                    return 'wp-hide';   
                }
    
            function get_interface_menu_data()
                {
                    $interface_data                     =   array();
                    
                    $interface_data['menu_title']       =   __('<span class="wph-info">Hide&rarr;</span> Rewrite',    'wp-hide-security-enhancer');
                    $interface_data['menu_slug']        =   self::get_module_slug();
                    $interface_data['menu_position']    =   10;
                    
                    return $interface_data;
                }
    
            function get_interface_data()
                {
                    $interface_data                     =   array();
                    
                    $interface_data['title']              =   __('WP Hide & Security Enhancer',    'wp-hide-security-enhancer') . ' <span class="plugin-mark">PRO</span> - ' . __('Rewrite',    'wp-hide-security-enhancer');
                    $interface_data['description']        =   '';
                    $interface_data['handle_title']       =   '';
                    
                    return $interface_data;
                }
                
            
                
        }
    
 
?>