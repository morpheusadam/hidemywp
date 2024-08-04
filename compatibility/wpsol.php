<?php
    
    
    /**
    * Compatibility     : WP Speed of Light
    * Introduced at     : 2.6.3
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_wpsol
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'wpsol_minify_content_return',      array( $this, 'wpsol_minify_content_return'), 999 );       
           
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'wp-speed-of-light/wp-speed-of-light.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            function wpsol_minify_content_return( $buffer )
                {
                                            
                    if  ( $this->wph->ob_callback_late )
                        return $buffer;
                        
                    //do replacements for this url
                    $buffer =   $this->wph->proces_html_buffer( $buffer );
                    
                    $this->wph->ob_callback_late =   TRUE;
                       
                    return $buffer;
                    
                }                            
        }

    
    new WPH_conflict_handle_wpsol();
    
?>