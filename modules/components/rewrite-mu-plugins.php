<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_mu_plugins extends WPH_module_component
        {
            
            function get_component_title()
                {
                    return "MU Plugins";
                }
                                    
            function get_module_component_settings()
                {
                                                                    
                    $this->component_settings[]                  =   array(
                                                                        'id'            =>  'block_mu_plugins_url',
                                                                        'label'         =>  __('Block MU Plugins URL',    'wp-hide-security-enhancer'),
                                                                        'description'   =>  __('Block default /wp-content/mu-plugins/ files from being accesible through default urls.',    'wp-hide-security-enhancer'),
                                                                        
                                                                        'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block MU Plugins URL',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This blocks the default wp-content/mu-plugins/ url.",    'wp-hide-security-enhancer'),
                                                                                                        ),
                                                                        
                                                                        'input_type'    =>  'radio',
                                                                        'options'       =>  array(
                                                                                                    'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                    'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                    ),
                                                                        'default_value' =>  'no',
                                                                        
                                                                        'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                        'processing_order'  =>  18
                                                                        
                                                                        );
                    
                    
                    
                                                                    
                    return $this->component_settings;   
                }
                
                
                
            function _init_block_mu_plugins_url ( $saved_field_data )
                {
                    
                    
                }

                
            function _callback_saved_block_mu_plugins_url($saved_field_data)
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    $processing_response    =   array();                       
                    $rewrite                =   '';
                    
                    $wp_content_slug            =   $this->wph->functions->get_rewrite_path( $this->wph->default_variables['content_directory'], [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                                                        
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                    
                            $rewrite  .=    "\n" . 'RedirectMatch 404 ^' . $wp_content_slug .'/mu-plugins(/?|/.*)$';
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
  
                        }
                    
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                         
                           
                        }
                    
                               
                    $processing_response['rewrite'] = $rewrite;            
                                
                    return  $processing_response;     
                    
                    
                }


        }
?>