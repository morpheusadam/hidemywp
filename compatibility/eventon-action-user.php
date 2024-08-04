<?php


    /**
    * Compatibility     : EventON - Action User
    * Introduced at     : 2.4.2
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_eventon_action_user
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    add_action ( 'wp', array ( $this, 'wp' ) );
                    
                    
                    /*
                    if ( ( isset ( $_POST['action'] ) &&  $_POST['action']    ==  'evoau_get_form' )    ||  ( isset ( $_POST['method'] ) &&  $_POST['method']    ==  'editevent' ) )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                        }
                    */
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'eventon-action-user/eventon-action-user.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
                
         
            function wp()
                {
                    global $post; 
                    
                    if ( ! is_object ( $post )  ||  ! isset ( $post->post_content ) )
                        return;
                    
                    if ( strpos( $post->post_content, 'add_evo_submission_form' ) )
                        {
                            add_filter ('wph/components/css_combine_code',                                  '__return_false');
                            add_filter ('wph/components/js_combine_code',                                   '__return_false');                   
                            
                            add_filter ('wph/components/rewrite-default/html_replacements',                 '__return_false');
                        }
                }
                
                
            function wph_components_init( $status, $component )
                {
                    if ( $component ==  'rewrite_default' )
                        return FALSE;
                        
                        
                    return $status;
                    
                }
                            
        }


    new WPH_conflict_eventon_action_user();
    
?>