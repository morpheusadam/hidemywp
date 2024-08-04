<?php

    
    /**
    * Compatibility: Comet Cache
    * Introduced at: 170220 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
                                  
    class WPH_conflict_handle_comet_cache
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_action('plugins_loaded',        array( $this , 'run') , -1);    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'comet-cache/comet-cache.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function run()
                {   
                                        
                    add_action('plugins_loaded', array( $this , 'plugins_loaded'));
                               
                }
                
            function plugins_loaded()
                {
                    ob_start(array( $this , "callback"));
                }
            
            
            function callback( $buffer )
                {
                    
                    if  ( $this->wph->ob_callback_late )
                        return $buffer;
                        
                    //applay the replacements
                    $buffer  =   $this->wph->proces_html_buffer( $buffer );
                    
                    $this->wph->ob_callback_late =   TRUE;
                    
                    
                    return $buffer;
                    
                }
                            
        }

    
    new WPH_conflict_handle_comet_cache();

        
?>