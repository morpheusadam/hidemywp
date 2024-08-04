<?php


    /**
    * Compatibility     : Fluent Forms - Best Form Plugin for WordPress 
    * Introduced at     : 3.5.5
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_fluentform
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action('wp-hide/content_urls_replacement',        array( $this,    '_content_urls_replacement' ), 10, 2 );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'fluentform/fluentform.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                       
            function _content_urls_replacement( $text, $_replacements )
                {
                                            
                    global $wph;
                    
                    /**
                    * Process Double json encoded urls
                    */
                    foreach( $_replacements   as $old_url =>  $new_url )
                        {
                            $old_url    =   trim(json_encode( trim(json_encode( $old_url), '"') ), '"');
                            $new_url    =   trim(json_encode( trim(json_encode( $new_url), '"') ), '"');
                  
                            $text =   str_ireplace(    $old_url, $new_url  ,$text   );
                        }
                       
                    return $text; 
                    
                }

           
        }
        
        
    new WPH_conflict_handle_fluentform();



?>