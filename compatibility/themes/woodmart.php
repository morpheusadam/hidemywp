<?php
    
    /**
    * Theme Compatibility   :   Woodmart
    * Introduced at version :   4.2.2 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    
    class WPH_conflict_theme_woodmart
        {
                        
            static function init()
                {
                    add_filter( 'woodmart_get_all_theme_settings_css', array( 'WPH_conflict_theme_woodmart', 'woodmart_get_all_theme_settings_css') );
                }                        
            
              
            static public function woodmart_get_all_theme_settings_css( $css )
                {   
                    global $wph;
                    
                    $option__css_combine_code    =   $wph->functions->get_site_module_saved_value('css_combine_code',  $wph->functions->get_blog_id_setting_to_use());
                    if ( ! in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                        return $wph->functions->content_urls_replacement( $css,  $wph->functions->get_replacement_list() );
                    
                    //process the fragments keys
                    $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                    return $WPH_module_general_css_combine->css_recipient_process( $css );
                               
                }
         
                                
        }
        
        
    WPH_conflict_theme_woodmart::init();
    

?>