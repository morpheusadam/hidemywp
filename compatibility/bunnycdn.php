<?php

    /**
    * Compatibility     : bunny.net
    * Introduced at     : 1.0.8
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_bunny_net
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_filter( 'option_bunnycdn', array( $this , 'option_bunnycdn' ), 99, 2 ); 

                }                        
            
            static function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if ( is_plugin_active ( 'bunnycdn/bunnycdn.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function option_bunnycdn( $value, $option )
                {
                    if ( ! is_array ( $value ) ||   ! isset ( $value['directories'] ) )
                        return $value;
                    
                    $custom_urls        =   array();
                    $custom_urls[]      =   $this->wph->functions->get_site_module_saved_value('new_include_path');
                    $custom_urls[]      =   $this->wph->functions->get_site_module_saved_value('new_content_path');
                    $custom_urls[]      =   $this->wph->functions->get_site_module_saved_value('new_upload_path');
                    
                    $custom_urls        =   array_filter ( $custom_urls );
                    
                    $directories        =   explode ( ',', $value['directories'] );
                    $directories        =   array_filter ( $directories );
                    
                    foreach ( $custom_urls  as $custom_url )
                        {
                            if ( array_search ( $custom_url, $directories ) === FALSE )
                                $directories[]  =   $custom_url;
                        }
                        
                    $value['directories']   =   implode ( ",", $directories );
                    
                    return $value;
                        
                }
   
        }
        
    new WPH_conflict_handle_bunny_net();


?>