<?php

    /**
    * Compatibility     : WP-Optimize - Clean, Compress, Cache
    * Introduced at     : 3.0.11
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_wp_optimize
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_filter( 'wpo_pre_cache_buffer', array( $this , 'wpo_pre_cache_buffer' ), 99, 2 ); 
                    
                    $option_remove_html_comments =   $this->wph->functions->get_site_module_saved_value( 'remove_html_comments' );
                    if ( $option_remove_html_comments == 'yes' )
                        add_filter ( 'wpo_cache_show_cached_by_comment', "__return_false" );
                        
                    if ( isset( $_POST['action'] )  &&  in_array ( $_POST['action'], array ( 'wp_optimize_ajax' ) ) )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                array( $this,    'wph_components_init'), 999, 2 );
                        }
                          
                }                        
            
            static function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'wp-optimize/wp-optimize.php' ) ||    is_plugin_active( 'wp-optimize-premium/wp-optimize.php'  ) )
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function wpo_pre_cache_buffer( $buffer, $flags )
                {
                    
                    if  ( $this->wph->ob_callback_late )
                        return $buffer;
                        
                    //do replacements for this url
                    $buffer =   $this->wph->proces_html_buffer( $buffer );
                    
                    $this->wph->ob_callback_late =   TRUE;
                       
                    return $buffer;
                        
                }
                
            function wph_components_init( $status, $component )
                {
                    if ( $component ==  'rewrite_default' )
                        return FALSE;
                        
                        
                    return $status;
                    
                }
   
        }
        
    new WPH_conflict_handle_wp_optimize();


?>