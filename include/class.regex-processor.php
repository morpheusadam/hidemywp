<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_RegexProcessor
         {

            var $wph                            =   '';
            var $functions                      =   '';
        
            var $replacements                   =   array();
                                  
            function __construct()
                {
                    global $wph;

                    $this->wph          =   $wph;
                    $this->functions    =   new WPH_functions();
                    
                }
                
            
            private function get_regex( $replacements, $content_type )
                {
                    
                    switch ( $content_type  ) 
                        {
                            case 'js':
                                            $regex  =   '#(?:\/\/|\\\/\\\/|\/wp-json)[^\s\'",})]*|(?:@[\w]+)(*SKIP)(*FAIL)|('.   implode("|", array_keys($replacements) )  .')(?!\\\/)#';
                                            break;                    
                            default:
                                            $regex  =   '#(?:\/\/|\\\/\\\/|\/wp-json|url)[^\s\'",})]*|(?:@[\w]+)(*SKIP)(*FAIL)|('.   implode("|", array_keys($replacements) )  .')(?!\\\/)#';
                        }
                    
                    $regex  =   apply_filters('wp-hide/get_regex', $regex, $replacements, $content_type );    
                        
                    return $regex;
                    
                }
                
                
            /**
            * Do regex replacements
            *     
            * @param mixed $content
            * @param mixed $type  content type to be processed
            */
            function do_replacements( $content, $html_css_js_replacements, $content_type )
                {
                    
                    foreach ( $html_css_js_replacements  as  $group )
                        {
                            $this->replacements[ $group[0] ]  =   $group[1];
                        }
                                                
                    $regex  =   $this->get_regex( $this->replacements, $content_type );
                                        
                    $content =   preg_replace_callback( $regex, function( $match ) {
                        
                        if ( ! isset ( $match[1] ) )
                            return $match[0];

                        $replacement    =   $this->replacements[ $match[1] ];
                        
                        $match[0]   =   str_replace( $match[1], $replacement, $match[0]);
                            
                        return $match[0];                        
                        
                    },$content); 
                       
                    return $content;    
                }
             
         }
?>