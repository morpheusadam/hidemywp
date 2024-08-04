<?php

    
    /**
    * Compatibility     : ShortPixel Image Optimizer
    * Introduced at     : 4.15.3
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_shortpixel_image_optimizer
        {
                           
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action('shortpixel_image_urls',        array( $this, 'shortpixel_image_urls') , 99, 2 );   
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'shortpixel-image-optimiser/wp-shortpixel.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
          
                
            function shortpixel_image_urls( $urls, $handler_id )
                {
                    
                    if ( empty ( $urls ) )
                        return $urls; 
                                        
                    //retrieve the replacements list
                    $replacement_list   =   $this->wph->functions->get_replacement_list();
                    
                    foreach ( $urls as  $key    =>  $url )
                        {
                            $urls[ $key ] =   $this->wph->functions->content_urls_replacement( $urls[ $key ],  $replacement_list );
                        }
                       
                    return $urls;    
                }           
        }
        
        
    new WPH_conflict_shortpixel_image_optimizer();


?>