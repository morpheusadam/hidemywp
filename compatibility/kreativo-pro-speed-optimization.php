<?php


    /**
    * Compatibility     : Kreativo Pro Speed Optimization
    * Introduced at     : 1.2.7 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_kreativo_pso
        {
            var $wph;
                            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                  
                    add_filter ( 'wp-hide/module/general_css_combine/href_attribute',                                   array ( $this, 'general_css_combine_href_attribute' ), 10, 2);

                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'kreativo-pro-speed-optimization/kreativo-pro-speed-optimization.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            function general_css_combine_href_attribute( $href_attribute, $code_block )
                {
                    if ( strpos( $code_block, 'data-kplinkhref' )   !==     FALSE )
                        $href_attribute =   'data-kplinkhref';
                        
                    return $href_attribute;   
                }

                            
        }


        new WPH_conflict_handle_kreativo_pso();
        
?>