<?php   
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_licence
        {
            
            /**
            * Retrieve licence details
            * 
            */
            public function get_licence_data()
                {
                    $licence_data = get_site_option('wph_licence');
                    
                    $default =   array(
                                            'key'               =>  '',
                                            'last_check'        =>  0,
                                            'licence_status'    =>  '',
                                            'licence_expire'    =>  ''
                                            );    
                    $licence_data           =   wp_parse_args( $licence_data, $default );
                    
                    return $licence_data;
                }
            
                
            /**
            * Reset license data
            *     
            * @param mixed $licence_data
            */
            public function reset_licence_data( $licence_data )
                {
                    if  ( ! is_array( $licence_data ) ) 
                        $licence_data   =   array();
                        
                    $licence_data['key']                =   '';
                    $licence_data['last_check']         =   time();
                    $licence_data['licence_status']     =   '';
                    $licence_data['licence_expire']     =   '';
                    
                    return $licence_data;
                }
            
            
            /**
            * Set licence data
            *     
            * @param mixed $licence_data
            */
            public function update_licence_data( $licence_data )
                {
                    //update_site_option('wph_licence', $licence_data);   
                }
            
                
            public function licence_key_verify()
                {
                    $licence_data = $this->get_licence_data();
                             
                    if ( ! isset ( $licence_data['key'] ) || $licence_data['key'] == '' )
                        return FALSE;
                        
                    return TRUE;
                }
        }
               
    
?>