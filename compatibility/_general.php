<?php
    
    /**
    * 
    * General compatibility class to be used for groups of plugins
    * 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
    
    class WPH_conflict_handle_General
        {
                        
            static function init()
                {
                    add_filter( 'wph/components/rewrite-default/superglobal_variables_replacements', array('WPH_conflict_handle_General', 'superglobal_variables_replacements' ), 999, 3 );
      
                }                        
            
          
            
            static function superglobal_variables_replacements( $status, $key, $global_name )
                {
                    /**
                    * In case the replacements match a site username
                    * Never change it for login username, in case the value match a replacement
                    */
                    if  ( $global_name  ==  'POST'  &&  $key  ==  'log'   &&  isset( $_POST['pwd']) )
                        $status =   FALSE;    
                    
                    if  ( $global_name  ==  'POST'  &&  $key  ==  'user_login' )
                        $status =   FALSE;
                    
                    return $status;    
                }
            
                            
        }
        
        
    WPH_conflict_handle_General::init();


?>