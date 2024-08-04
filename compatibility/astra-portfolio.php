<?php

    
    /**
    * Compatibility             :   WP Portfolio
    * Introduced at             :   1.8
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

    class WPH_conflict_handle_wp_portfolio
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    //add_filter('wp-hide/ignore_ob_start_callback',                                array( 'WPH_conflict_handle_wp_portfolio', 'pre_replacements'), 10, 2);                    
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'astra-portfolio/astra-portfolio.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            
            function pre_replacements( $status, $buffer )
                {
                    global $wph;
                    
                    //check if there is a replacement for astra
                    $replacements =   $wph->functions->get_site_module_saved_value('css_class_replace',  $wph->functions->get_blog_id_setting_to_use());
                    
                    if ( ! is_array ( $replacements )   ||  count ( $replacements ) < 1 )
                        return $status;
                    
                    $found = TRUE;
                    foreach( $replacements    as  $replacement_block )
                        {
                            $find_me        =   $replacement_block[0];
                            $replacement    =   $replacement_block[1];   
                            
                            $find_me    =   trim ( $find_me, '*' );
                            
                            if  (   $find_me    ==  'astra' )
                                $found  =   TRUE;
                        }
                        
                    if  ( $found ===    FALSE )
                        return $status;
                        
                    //check for portfolio
                    if  ( strpos( $buffer, 'id="astra-portfolio"' ) !== FALSE )
                        {
                            //unregister the Css and JavaScript combine 
                            $wph->functions->remove_anonymous_object_filter('wp-hide/ob_start_callback/pre_replacements',             'WPH_module_general_css_combine', '_css_process_html');                             
                            $wph->functions->remove_anonymous_object_filter('wp-hide/ob_start_callback/pre_replacements',             'WPH_module_general_js_combine', '_js_process_html');  
                        }
                                        
                    return $status;
                }
        }
        
    
    new WPH_conflict_handle_wp_portfolio();


?>