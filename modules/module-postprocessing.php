<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_postprocessing extends WPH_module
        {
      
            function load_components()
                {
                    
                    //add components
                    include_once(WPH_PATH . "/modules/components/general-css-combine.php");
                    $this->components[]  =   new WPH_module_general_css_combine();
                    
                    include_once(WPH_PATH . "/modules/components/general-js-combine.php");
                    $this->components[]  =   new WPH_module_general_js_combine();
                    
                    include_once(WPH_PATH . "/modules/components/general-html-css-js-replacements.php");
                    $this->components[]  =   new WPH_module_general_html_css_js_replacements();
                    
                    if ( defined ( 'WPH_DOCUMENT_LOADED_ASSETS_POSTPROCESSING' )    &&  WPH_DOCUMENT_LOADED_ASSETS_POSTPROCESSING   === TRUE )
                        {
                            include_once(WPH_PATH . "/modules/components/general-document-loaded-assets-postprocessing.php");
                            $this->components[]  =   new WPH_module_general_document_loaded_assets_postprocessing();
                        }
                    
                    //action available for mu-plugins
                    do_action('wp-hide/module_load_components', $this);
                    
                }
            
            function use_tabs()
                {
                    
                    return TRUE;
                }
            
            function get_module_id()
                {
                    
                    return 'postprocessing';
                }
                
            function get_module_slug()
                {
                    
                    return 'wp-hide-postprocessing';   
                }
    
            function get_interface_menu_data()
                {
                    $interface_data                     =   array();
                    
                    $interface_data['menu_title']       =   __('<span class="wph-info">Hide&rarr;</span> Post-Processing',    'wp-hide-security-enhancer');
                    $interface_data['menu_slug']        =   self::get_module_slug();
                    $interface_data['menu_position']    =   30;
                    
                    return $interface_data;
                }
    
            function get_interface_data()
                {
      
                    $interface_data                     =   array();
                    
                    $interface_data['title']              =   __('WP Hide & Security Enhancer <span class="plugin-mark">PRO</span> - Post-Processing',    'wp-hide-security-enhancer');
                    $interface_data['description']        =   '';
                    $interface_data['handle_title']       =   '';
                    
                    return $interface_data;
                    
                }

                
        }
    
 
?>