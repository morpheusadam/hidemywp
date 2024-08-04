<?php

    /**
    * Compatibility     : CookieYes | GDPR Cookie Consent
    * Introduced at     : 3.0.8
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_cookie_law_info
        {
                                                   
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                        
                    add_filter( 'wph/module/general_styles/remove_id_attribute/ignore_ids',     array ( $this, 'wph_module_general_styles_remove_id_attribute_ignore_ids' ) );
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'cookie-law-info/cookie-law-info.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function  wph_module_general_styles_remove_id_attribute_ignore_ids( $ignores )
                {
                    $ignores[]  =   'cky-style-inline';
                    
                    return $ignores;   
                }
   
        }
        
    new WPH_conflict_handle_cookie_law_info();


?>