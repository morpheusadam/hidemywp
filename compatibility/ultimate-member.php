<?php

    /**
    * Compatibility     : Ultimate Member
    * Introduced at     : 2.1.5
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_ultimate_member
        {
                        
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action('plugins_loaded',        array( $this,    '_reverse_urls') );
                    
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'ultimate-member/ultimate-member.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                       
            function _reverse_urls()
                {
                    if ( ! isset ( $_POST['action'] )    ||  $_POST['action']   !=  'um_resize_image' )
                        return;
                                                
                    global $wph;
                    
                    $src    =   $_POST['src'];
                    
                    $src    =   $wph->functions->content_urls_replacement( $src,  array_flip ( $wph->functions->get_replacement_list() ) );
                    
                    $_POST['src']   =   $src;                       
                }

           
        }
        
        
    new WPH_conflict_handle_ultimate_member();


?>