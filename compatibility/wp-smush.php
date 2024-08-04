<?php

    
    /**
    * Compatibility     : WP Smush  and WP Smush PRO
    * Introduced at     : 3.4.0
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_wp_smush
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'smush_filter_generate_cdn_url',                    array( $this, 'smush_filter_generate_cdn_url'), 1 );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'wp-smushit/wp-smush.php' )   ||  is_plugin_active( 'wp-smush-pro/wp-smush.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                       
            function smush_filter_generate_cdn_url( $src )
                {
                    
                    $src    =   $this->wph->functions->content_urls_replacement( $src,  $this->wph->functions->get_replacement_list() ); 
                       
                    return $src; 
                    
                }

           
        }
        
        
    new WPH_conflict_handle_wp_smush();


?>