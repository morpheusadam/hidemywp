<?php

    /**
    * Compatibility     : Accelerated Mobile Pages
    * Introduced at     : 1.0.81
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_amp
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_filter( 'wp-hide/module/general_css_variables_replace/placeholder_ignore_css',      array ( $this, 'placeholder_ignore_css' ), 99, 3 );
                }                        
            
            static function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'accelerated-mobile-pages/accelerated-moblie-pages.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function placeholder_ignore_css( $ignode, $code, $type )
                {
                    //large chunks produce errors for some browsers
                    if ( preg_match( '#<style[^>]*(amp-)#i', $code ) > 0 )
                        $ignore =   TRUE;
                    
                    return $ignore;   
                }
   
        }
        
    new WPH_conflict_handle_amp();


?>