<?php

    /**
    * Compatibility     : Yoast SEO Premium
    * Introduced at     : 11.4
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_yseop
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter ( 'wpseo_stylesheet_url' , array( $this, 'urls_replacement'), 10, 2);   
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'wordpress-seo-premium/wp-seo-premium.php' )   ||  is_plugin_active( 'wordpress-seo/wp-seo.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                    
            function urls_replacement( $block )
                {
                    $block    =   $this->wph->functions->content_urls_replacement( $block,  $this->wph->functions->get_replacement_list() );   
                    
                    return $block;    
                }
                            
        }
        
        
    new WPH_conflict_handle_yseop();


?>