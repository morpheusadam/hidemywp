<?php

    /**
    * Compatibility     : YellowPencil Pro
    * Introduced at     : 7.5.7
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_yellow_pencil
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    if ( isset( $_GET['yellow_pencil_frame'] ) )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                array( $this,    'wph_components_init'), 999, 2 );
                        }
         
                }                        
            
            static function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'waspthemes-yellow-pencil/yellow-pencil.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function wph_components_init( $status, $component )
                {
                    if ( $component ==  'rewrite_default' )
                        return FALSE;
                        
                    return $status;
                }
   
        }
        
    new WPH_conflict_handle_yellow_pencil();


?>