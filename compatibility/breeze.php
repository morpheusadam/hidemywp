<?php


    /**
    * Compatibility     : Breeze
    * Introduced at     : 2.0.15
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_breeze
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'breeze_cache_buffer_before_processing', array ( $this, 'breeze_buffer' ) );
                    
                    add_filter( 'breeze_minify_content_return', array ( $this, 'breeze_buffer' ) );

                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'breeze/breeze.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
            
            function plugins_loaded()
                {
                    ob_start( array( $this, 'breeze_buffer' ) );
                }
                
            function breeze_buffer( $buffer )
                {
                    
                    if  ( $this->wph->ob_callback_late )
                        return $buffer;
                        
                    //do replacements for this url
                    $buffer =   $this->wph->proces_html_buffer( $buffer );
                    
                    $this->wph->ob_callback_late =   TRUE;
                       
                    return $buffer;   
                    
                }
        }


    new WPH_conflict_breeze();
    
?>