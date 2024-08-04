<?php

    
    /**
    * Compatibility     : Swift Performance
    * Introduced at     : 
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_hummingbird
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'wphb_minify_file_content', array( $this, 'wphb_minify_file_content' ) );
                    
                }
                    
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'wp-hummingbird/wp-hummingbird.php' ) ||  is_plugin_active( 'hummingbird-performance/wp-hummingbird.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
  
                
            function wphb_minify_file_content( $content )
                {
                                        
                    $content         =  $this->wph->functions->content_urls_replacement( $content, $this->wph->functions->get_replacement_list() );
                    
                    return $content;   
                }

                            
        }

    new WPH_conflict_handle_hummingbird();
    
?>