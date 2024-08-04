<?php

    /**
    * Compatibility     : wePOS
    * Introduced at     : 1.1.1
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_wepos
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph; 
                        
                    add_filter( 'wp', array( $this , 'wp' ), 1 );   
                }                        
            
            static function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'wepos/wepos.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function wp( )
                {
                    if ( ! wepos_is_frontend() )
                        return;
                        
                    /*    
                    add_filter ('wph/components/css_combine_code',      '__return_false');
                    add_filter ('wph/components/js_combine_code',       '__return_false');
                    
                    add_filter ('wph/components/_init/',                array( $this,    'wph_components_init'), 999, 2 );
                    */
                    
                    add_filter ('wph/components/wp_oembed_add_discovery_links',                 '__return_false');
                    add_filter ('wph/components/wp_oembed_add_host_js',                         '__return_false');    
                }
                
            function wph_components_init( $status, $component )
                {
                    if ( $component ==  'rewrite_default' )
                        return FALSE;
                        
                        
                    return $status;
                    
                }
   
        }
        
    new WPH_conflict_handle_wepos();


?>