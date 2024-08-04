<?php


    /**
    * Compatibility     : Tutor LMS
    * Introduced at     : 2.0.9
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_tutor_lms
        {
                        
            var $wph;
                            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    if ( isset( $_GET['course_ID'] ) )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                        }


                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'tutor/tutor.php' ) ||    is_plugin_active( 'tutor-pro/tutor-pro.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
            
           
   
        }
        
   
    new WPH_conflict_handle_tutor_lms();


?>