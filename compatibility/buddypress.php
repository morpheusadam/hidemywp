<?php

    
    /**
    * Compatibility     : BuddyPress
    * Introduced at     : 
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_BuddyPress
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                               
                    //adjust bp_core_avatar_url
                    add_filter('bp_core_avatar_url',            array( $this, 'bp_core_avatar_url'), 999);
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'buddypress/bp-loader.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
                
            function bp_core_avatar_url( $url )
                {
                    
                    $url    =   $this->wph->functions->content_urls_replacement( $url,  $this->wph->functions->get_replacement_list() );  ;                    
                
                    return $url;
                    
                }    
                 
            
                            
        }


    new WPH_conflict_handle_BuddyPress()
        
?>