<?php

    
    /**
    * Compatibility     :   Slider Revolution
    * Introduced at     :   6.1.2
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_revslider
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                                        
                    
                    add_filter( 'wp-hide/module/general_css_variables_replace/placeholder_ignore_css', array( $this , 'placeholder_ignore_css' ),  10, 3 );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'revslider/revslider.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            function placeholder_ignore_css ( $ignore_status, $element_content, $element_href )
                {
                    
                    if  ( empty ( $element_content ) )
                        return $ignore_status;
                    
                    if ( preg_match( "/id\='rs\-plugin\-settings\-inline\-css'/i" , $element_content))   
                        $ignore_status  =   TRUE;
                        
                    return $ignore_status;
                       
                }
            
                            
        }
        

    new WPH_conflict_handle_revslider();


?>