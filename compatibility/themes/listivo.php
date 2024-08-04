<?php
    
    /**
    * Theme Compatibility   :   Listivo
    * Lat Checked           :   2.3.53
    * Introduced at         :   2.3.53
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    
    class WPH_conflict_theme_listivo
        {
                           
            function __construct()
                {
                    add_filter( 'wph/components/force_run_on_admin',  array( $this, 'force_run_on_admin' ), 10, 2 );
                }
                        
            public function force_run_on_admin ( $status, $component )
                {
                    if ( $component !=  'rewrite_default' )
                        return $status;
                        
                    if ( isset ( $_GET['action'] )  &&  strpos( $_GET['action'], 'listivo') === 0 )
                        return TRUE;
                        
                    return $status;
                }                        
                  
        }
        
        
    new WPH_conflict_theme_listivo();
    

?>