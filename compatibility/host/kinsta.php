<?php

    /**
    * Compatibility for server: Kinsta
    * 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_server_kinsta
        {
                        
            static function init()
                {
                    
                    global $wph;
                    
                    if ( $wph->functions->server_is_kinsta()    === FALSE )
                        return;
                    
                    add_filter( 'wp-hide/module_mod_rewrite_rules',         array( 'WPH_conflict_server_kinsta',    'rewrite_update'), 10, 2 );

                }                        
            
            static function rewrite_update( $module_mod_rewrite_rules, $_class_instance )
                {
                    if  ( ! is_a ( $_class_instance, 'WPH_module_admin_new_wp_login_php')) 
                        return $module_mod_rewrite_rules;
                    
                    if ( $module_mod_rewrite_rules    ===    FALSE    ||    count ( $module_mod_rewrite_rules ) < 1 )
                        return $module_mod_rewrite_rules;
                        
                    foreach ( $module_mod_rewrite_rules  as $key =>  $data )
                        {
                            if ( is_array ( $data ) )
                                {
                                    foreach ( $data as $block_key   =>  $rewrite_data )
                                        {
                                            if ( is_array ( $rewrite_data['data'] ) )
                                                {
                                                    foreach ( $rewrite_data['data'] as $rewrite_key   =>  $rewrite )
                                                        {
                                                            $module_mod_rewrite_rules[$key][$block_key]['data'][ $rewrite_key ]   =   str_replace( '/wp-login.php(.+) /index.php', '/wp-login.php /index.php', $rewrite);
                                                        }
                                                }
                                        
                                        }
                                }  
                        }    
                        
                    return $module_mod_rewrite_rules;
                    
                }
                
                
                
      
                            
        }
        
        
    WPH_conflict_server_kinsta::init();


?>