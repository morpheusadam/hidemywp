<?php

    
    /**
    * Compatibility     : ShortPixel Adaptive Images
    * Introduced at     : 0.9.2
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_shortpixel_ai
        {
                               
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_action('wp_calculate_image_srcset',        array( $this, 'wp_calculate_image_srcset') , -1, 5);   
                    
                    //This replace the urls, making CSS Combine and JS combine imposible
                    add_action( 'init',                            array( $this, 'init_ob'), 2 ); 
                }                        
            
            function is_plugin_active()
                {
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'shortpixel-adaptive-images/short-pixel-ai.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            public function wp_calculate_image_srcset( $sources, $size_array, $image_src, $image_meta, $attachment_id )
                {   
                    
                    //retrieve the replacements list
                    $replacement_list   =   $this->wph->functions->get_replacement_list();
                                            
                    //replace the urls
                    foreach ( $sources as $size =>  $data ) 
                        {
                            $sources[$size]['url'] =   $this->wph->functions->content_urls_replacement( $sources[$size]['url'],  $replacement_list );
                        }
                    
                    return $sources;    
                               
                }
                
                
            public function init_ob()
                {
                    
                    if (is_feed()
                        || (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
                        || (defined('DOING_CRON') && DOING_CRON)
                        || (defined('WP_CLI') && WP_CLI)
                        || (is_admin() && function_exists("is_user_logged_in") && is_user_logged_in()
                            && !(function_exists("wp_doing_ajax") && wp_doing_ajax())
                            && !(defined( 'DOING_AJAX' ) && DOING_AJAX))
                    ) {
                        return;
                    }
                    
                    ob_start( array ( 'WPH_conflict_shortpixel_ai', 'maybe_replace_images_src' ) );   
                    
                }
                
            function maybe_replace_images_src( $content )
                {
                    $content = preg_replace_callback(  '/=("|\')([^"|\']+.(jpg|jpeg|png))/im', array( $this, '_replace_image_slug') , $content);
                       
                    return $content;    
                }
                
            function _replace_image_slug( $match )
                {
                    
                    $found  =   $match[0];
                    
                    $replacements   =   $this->wph->functions->get_replacement_list();
                    
                    //do simple replacements
                    foreach($replacements   as  $replace    =>  $replace_to)
                        {
                            $found  =   str_replace($replace, $replace_to, $found);
                        }
                    
                    return $found;
                }
        }

    new WPH_conflict_shortpixel_ai();

?>