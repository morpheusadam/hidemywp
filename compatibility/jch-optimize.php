<?php


    /**
    * Compatibility     : JCH Optimize
    * Introduced at     : 3.1
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_jch_optimize
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter( 'jch_optimize_save_content',                    array( $this, 'proces_html_buffer'), 999 );
                    add_filter( 'jch_optimize_get_http2_preloads',              array( $this, 'jch_optimize_get_http2_preloads'), 999 );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'jch-optimize/jch-optimize.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                       
            function proces_html_buffer( $buffer )
                {
                                            
                    if  ( $this->wph->ob_callback_late )
                        return $buffer;
                        
                    //do replacements for this url
                    $buffer =   $this->wph->proces_html_buffer( $buffer );
                    
                    $this->wph->ob_callback_late =   TRUE;
                       
                    return $buffer;
                    
                }
                
                
            function jch_optimize_get_http2_preloads( $preloads )
                {
                    
                    if ( ! is_array ( $preloads )   ||  ! isset ( $preloads['link'] )   ||  ! is_array ( $preloads['link'] )  ||  count ( $preloads['link'] ) < 1 )
                        return $preloads;
                        
                    foreach ( $preloads['link'] as  $key    =>  $preload_link )
                        {
                            if ( ! is_array ( $preload_link )   ||  ! isset ( $preload_link['href'] ) )
                                continue;
                            
                            $href   =   $preload_link['href'];
                            if ( strpos( $href, '/wp-' ) === 0 )
                                $href   =   untrailingslashit ( home_url() ) .    $href;
                                
                            $preloads['link'][$key]['href']    =   $this->wph->functions->content_urls_replacement( $href,  $this->wph->functions->get_replacement_list() ); 
                        }
                    
                    return $preloads;   
                }
           
        }
        
        
    new WPH_conflict_handle_jch_optimize();


?>