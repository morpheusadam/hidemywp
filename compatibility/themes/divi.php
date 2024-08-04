<?php
    
    /**
    * Theme Compatibility   :   DIVI
    * Version               :   4.3.2
    * 
    * Introduced at version :   3.17.6* 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    
    class WPH_conflict_theme_divi
        {
            
            var $preserved_texts    =   array();
               
            function __construct()
                {
                    $this->init();
                }
                        
            public function init()
                {
                                        
                    add_action('et_builder_custom_fonts',                       array('WPH_conflict_theme_divi',    'process_et_builder_custom_fonts'));
                    
                    add_action('et_core_page_resource_get_data',                array('WPH_conflict_theme_divi',    'process'), 99, 3);
                    
                    add_action( 'wph/settings_changed',                         array( 'WPH_conflict_theme_divi',   'settings_changed') );
                    
                    
                    if ( isset( $_GET['et_fb'] ) )
                        {
                            add_filter ('wph/components/css_combine_code',  '__return_false');
                            add_filter ('wph/components/js_combine_code',   '__return_false' );
                        }
                        
                        
                    add_action( 'wp-hide/before_ob_start_callback',  array('WPH_conflict_theme_divi',    'start_ob_start_callback'));
                    
                    
                    add_filter( 'wph/components/components_run/ignore_component',    array( $this,    'ignore_field_id'), 999, 4 );
                        
                }                        
            
              
            static public function process( $resource_data, $context, $object )
                {   
                    
                    global $wph;
                    
                    $option__css_combine_code       =   $wph->functions->get_site_module_saved_value('css_combine_code',  $wph->functions->get_blog_id_setting_to_use());
                    $replacement_list               =   $wph->functions->get_replacement_list();
                    $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                    
                    foreach ( $resource_data as $priority => $data_part ) 
                        {
                            foreach ( $data_part as $key    =>  $data ) 
                                {
                                    if ( in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                                        $resource_data[ $priority ][ $key ] =   $WPH_module_general_css_combine->css_recipient_process( $data );
                                        else
                                        $resource_data[ $priority ][ $key ] =   $wph->functions->content_urls_replacement( $data,  $replacement_list ); 
                                }
                        }
                        
                    return $resource_data;
                               
                }
            
            
            /**
            * Process the cutom fonts
            *     
            * @param mixed $all_custom_fonts
            */
            static public function process_et_builder_custom_fonts( $all_custom_fonts )
                {
                    
                    if  ( ! is_array($all_custom_fonts)     ||  count ( $all_custom_fonts ) < 1 )
                        return $all_custom_fonts;
                    
                    global $wph;
                    
                    $replacement_list   =   $wph->functions->get_replacement_list();
                        
                    foreach  ( $all_custom_fonts as $font   =>  $font_data )
                        {
                            $font_urls  =   $font_data['font_url'];
                            if ( !is_array( $font_urls ) || count ( $font_urls ) < 1 )
                                continue;
                                
                            foreach ( $font_urls    as  $type   =>  $url )
                                {
                                    $font_urls[$type]  =   $wph->functions->content_urls_replacement( $url,  $replacement_list );   
                                }
                            
                            $all_custom_fonts[$font]['font_url']    =   $font_urls;
                        }
                    
                    return $all_custom_fonts;
                       
                }
                
                
            static function settings_changed()
                {
                    
                    ET_Core_PageResource::remove_static_resources( 'all', 'all' );
                    
                }
                
                
            static function start_ob_start_callback ( $buffer )     
                {
                    global $wph;
                    
                    $wph->functions->remove_anonymous_object_filter( 'wp-hide/ob_start_callback',             'WPH_module_general_styles', 'ob_start_callback_remove_id');  
                    
                }
                
                
            public static function ignore_field_id( $ignore_field, $component_id, $saved_field_value, $_class_instance )
                {
                    
                    if  ( in_array( $component_id, array( 'remove_html_comments' ) ) )
                        {
                            $ignore_field   =   TRUE;
                        }
                    
                    return $ignore_field;
                    
                }
                                
        }
        
        
    new WPH_conflict_theme_divi();
    

?>