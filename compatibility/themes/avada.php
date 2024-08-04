<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_theme_avada
        {
            var $wph;
                            
            function __construct()
                {
                    global $wph;
                    
                    $this->wph  =   $wph;
                       
                    add_action('plugins_loaded',        array('WPH_conflict_theme_avada', 'run') , -1);
                    
                    if ( 
                        isset( $_GET['fb-edit'] )  
                        ||  ( isset ( $_GET['builder'] )    &&  $_GET['builder']    ==  'true'  )  
                        ||  ( isset ( $_POST['action'] )    &&  in_array ( $_POST['action'], array ( 'fusion_app_partial_refresh', 'query-attachments', 'fusion_builder_load_layout', 'fusion_builder_save_layout', 'fusion_get_widget_form', 'fusion_form_update_view', 'get_shortcode_render' ) ) )
                        )
                        {
                            add_filter ('wph/components/css_combine_code',  '__return_false');
                            add_filter ('wph/components/js_combine_code',   '__return_false' );
                            
                        }
                    
                    
                    //for upcoming Avada 7.7
                    add_filter( 'awb_combined_stylesheet_url',          array ( $this, 'awb_combined_stylesheet_url' ), 10, 3 );
                    add_filter( 'wp-hide/ignore_ob_start_callback',     array ( $this, 'ignore_ob_start_callback'), 10, 2 );
                        
                }                        
              
            static public function run()
                {   
                                                            
                    add_filter ('fusion_dynamic_css_final', array('WPH_conflict_theme_avada', 'url_replacement'), 999);
                    
                    //flush avada cache when settings changes
                    if ( function_exists ( 'avada_reset_all_caches' ) )
                        add_action('wph/settings_changed',  'avada_reset_all_caches');
                    if ( function_exists ( 'fusion_reset_all_caches' ) )
                        add_action('wph/settings_changed',  'fusion_reset_all_caches');
                               
                }
                
                
            
            function wph_components_init( $status, $component )
                {
                    if ( $component ==  'rewrite_default' )
                        return FALSE;
                        
                    return $status;
                }
                 
            static function url_replacement( $buffer )
                {
                    
                    global $wph;
                                        
                    $buffer =   $wph->ob_start_callback( $buffer );
                    
                    return $buffer;
     
                }
            
            function awb_combined_stylesheet_url( $src, $site_url, $plugins_url )
                {
                    
                    $src    =   $this->wph->functions->content_urls_replacement( $src,  $this->wph->functions->get_replacement_list() );
                    
                    return $src;    
                }
                
                
            
            function ignore_ob_start_callback( $status, $buffer )
                {
                    if ( $status    === TRUE )    
                        return $status;
                        
                    if ( isset ( $_POST['action'] ) &&  $_POST['action']    ==  'fusion_options_ajax_save' )
                        $status =   TRUE;
                    
                    return $status;
                }
                            
        }
        
        
    new WPH_conflict_theme_avada();


?>