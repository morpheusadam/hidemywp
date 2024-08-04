<?php


    /**
    * Compatibility     : Super Cache
    * Introduced at: 
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_wp_super_cache
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;

                    add_filter('wp_cache_ob_callback_filter', array( $this, '_ob_start_callback' ), 999);
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'wp-super-cache/wp-cache.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                  
            function _ob_start_callback( $buffer )
                {
                    
                    if  ( $this->wph->ob_callback_late )
                        return $buffer;
                        
                    //do replacements for this url
                    $buffer =   $this->wph->proces_html_buffer( $buffer );
                    
                    $this->wph->ob_callback_late =   TRUE;
                       
                    return $buffer;   
                    
                }
                            
        }


    new WPH_conflict_handle_wp_super_cache();
        
?>