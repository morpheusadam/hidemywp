<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_component
        {
            var $wph;
            var $component_settings;
            
            var $id;
            var $title;
            
            var $ReInit =   FALSE;
            
            var $settings_to_use;
                     
            function __construct()
                {
                    $this->id       =   $this->get_component_id();
                    $this->title    =   $this->get_component_title();
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    $this->component_settings  =   $this->get_module_component_settings();
                }
            
            function get_component_id()
                {
                    if($this->get_component_title() === FALSE)   
                        return FALSE;
                        
                    return sanitize_title( html_entity_decode ( $this->get_component_title() ) );
                    
                }
                
            function get_component_title()
                {
                    
                    return FALSE;
                }
                
            function get_module_description()
                {
                    
                    return FALSE;
                }
                
            
            function get_module_component_settings()
                {
                    
                    return array();   
                }
                
        }
    
 
?>