<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_registration extends WPH_module_component
        {
            
            function get_component_title()
                {
                    return "Registration";
                }
                                                
            function get_module_component_settings()
                {
                    
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'new_wp_signup_php',
                                                                    'label'         =>  __('New wp-signup&#183;php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Change default sign-up url.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New wp-signup&#183;php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This is the url through which users can register a site or / and a ursername.",    'wp-hide-security-enhancer') . " <br />  <br />
                                                                                                                                            <br /><br /> " . __("The registration status can be controlled through the network super admin interface:",    'wp-hide-security-enhancer') . " <br />  <br /> 
                                                                                                                                            <img src='".  WPH_URL . "/assets/images/help/network-registration-status.jpg' />
                                                                                                                                            <br /> " . __("If being active, it appear like the following URL:",    'wp-hide-security-enhancer') . " <br />  <br /> 
                                                                                                                                            <code>-domain-name-/wp-signup&#183;php</code>",
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-registration/'
                                                                                                        ),
                                                                    
                                                                    'value_description' =>  __('e.g. register',    'wp-hide-security-enhancer'),
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name')),
                                                                    'processing_order'  =>  50
                                                                    );
                                                                    
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_default_wp_signup_php',
                                                                    'label'         =>  __('Block wp-signup&#183;php URL',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block default wp-signup&#183;php file.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block wp-signup&#183;php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Block the default wp-signup&#183;php file. If <b>New wp-signup&#183;php</b> is being used, is save to block the default, the registration process will continue to work.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-registration/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  50
                                                                    
                                                                    );
                                                                    
                                                                    
                    $this->component_settings[]                  =   array(
                                                                                'type'            =>  'split'
                                                                                
                                                                                );
                                                                    
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'new_wp_activate_php',
                                                                    'label'         =>  __('New wp-activate&#183;php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Change default blog activation url.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New wp-activate&#183;php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This is the URL through which a user can activate a registered blog.",    'wp-hide-security-enhancer') . " <br />  <br />
                                                                                                                                            <br /> " . __("The URL appear like the following:",    'wp-hide-security-enhancer') . " <br />  <br /> 
                                                                                                                                            <code>-domain-name-/wp-activate&#183;php?key=2d857216c129e009</code>",
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-registration/'
                                                                                                        ),
                                                                    
                                                                    'value_description' =>  __('e.g. account-activate',    'wp-hide-security-enhancer'),
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name')),
                                                                    'processing_order'  =>  50
                                                                    );
                                                                    
                                                                                        
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_wp_activate_php',
                                                                    'label'         =>  __('Block wp-activate&#183;php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block access to default wp-activate&#183;php file.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block wp-activate&#183;php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Block access to wp-activate&#183;php file. Through this file new users confirm that the activation key that is received in the email after signs up for a new blog, matches the key for that user.",    'wp-hide-security-enhancer') . 
                                                                                                                                            "<br />" . __("If <b>New wp-activate&#183;php</b> is being used, is save to block the default, the registration process will continue to work.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-registration/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  50
                                                                    );
                                                                    
                    
                    return $this->component_settings;
                    
                }
                
         
            function _init_new_wp_signup_php( $saved_field_data )
                {
                    if(empty($saved_field_data))
                        return FALSE; 
 
                    if ( ! $this->ReInit )
                        add_filter( 'wp_signup_location', array( $this, 'wp_signup_location' ));
                    
                    $old_url    =   'wp-signup.php';
                    $new_url    =   $saved_field_data;
                    $this->wph->functions->add_replacement( $old_url ,  $new_url );
                    
                }
                
                
            function _callback_saved_new_wp_signup_php( $saved_field_data )
                {
                    
                    //check if the field is noe empty
                    if(empty($saved_field_data))
                        return  FALSE; 
                        
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if(is_multisite())
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                        }
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'wp-signup.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ] );
                               
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                            
                            if( !is_multisite() )
                                {
                                    $rewrite  .= "\nRewriteRule ^"    .   $rewrite_base  .   ' '. $rewrite_to .' [END,QSA]';
                                }
                                else
                                {
                                    $rewrite  .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base  .   ' '. $rewrite_to .' [END,QSA]';    
                                }
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-new_wp_signup_php" stopProcessing="true">';
                                       
                            if( !is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="true" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="true" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
                        }
                        
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            $rewrite        =   array();
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                               
                            if( ! is_multisite() )
                                {
                                    $rewrite_list['blog_id'] =   $blog_id;
                                }
                                else
                                    $rewrite_list['blog_id'] =   'network';
                                
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}xml_rpc__";';
                                }
                            
                            $rewrite_data   =   '';
                            
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                                
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;   
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                
                    return  $processing_response;   
                }
            
            
            function wp_signup_location()
                {
                    
                    $new_signup     =   $this->wph->functions->get_site_module_saved_value( 'new_wp_signup_php',  $this->wph->functions->get_blog_id_setting_to_use() );
                    
                    $new_url        =   network_site_url( $new_signup );
                    
                    return $new_url;
                    
                }
                
                
            function _init_new_wp_activate_php( $saved_field_data )
                {
                    if(empty($saved_field_data))
                        return FALSE; 
                    
                    $old_url    =   '/wp-activate.php';
                    $new_url    =   '/' . $saved_field_data;
                    $this->wph->functions->add_replacement( $old_url ,  $new_url );
                    
                }
                
                
            function _callback_saved_new_wp_activate_php( $saved_field_data )
                {
                    
                    //check if the field is noe empty
                    if(empty($saved_field_data))
                        return  FALSE; 
                        
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if(is_multisite())
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                        }
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'wp-activate.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ] );
                               
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                            
                            if( !is_multisite() )
                                {
                                    $rewrite  .= "\nRewriteRule ^"    .   $rewrite_base  .   ' '. $rewrite_to .' [END,QSA]';
                                }
                                else
                                {
                                    $rewrite  .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base  .   ' '. $rewrite_to .' [END,QSA]';    
                                }
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-new_wp_activate_php" stopProcessing="true">';
                                       
                            if( !is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="true" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="true" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
                        }
                        
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            $rewrite        =   array();
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                               
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                                
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}xml_rpc__";';
                                }
                            
                            $rewrite_data   =   '';
                            
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;   
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                
                    return  $processing_response;   
                }
                
        }
?>