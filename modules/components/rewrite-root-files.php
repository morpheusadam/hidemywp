<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_root_files extends WPH_module_component
        {
            
            function get_component_title()
                {
                    return "Root Files";
                }
                                                
            function get_module_component_settings()
                {
                                      
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_license_txt',
                                                                    'label'         =>  __('Block license.txt',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block access to license.txt root file',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block license.txt',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This is a text file which contain the licensing terms for WordPress framework. Obviously you don't want that visible as every site containing such file must be a WordPress.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-root-files/'
                                                                                                        ),
                                                                    
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  55
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_readme_html',
                                                                    'label'         =>  __('Block readme.html',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block access to readme.html root file',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block readme.html',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("A Hypertext Markup Language file with general information about installed WordPress, version, instalation steps, updating, requirements, resources etc.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-root-files/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  55
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_wp_activate_php',
                                                                    'label'         =>  __('Block wp-activate.php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block access to wp-activate.php file. This file confirms that the activation key that is sent in an email after a user signs up for a new blog matches the key for that user. If <b>anyone can register</b> on your site, you shold keep this off.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block wp-activate.php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Block access to wp-activate.php file. Through this file new users confirms that the activation key that is received in the email after signs up for a new blog, matches the key for that user.",    'wp-hide-security-enhancer') . 
                                                                                                                                            "<br />" . __("If anyone can register on your site, you should keep this to No.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-root-files/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  55
                                                                    );
                    
                    $local_ip   =   $this->domain_get_ip();
                    $option_description     =   __('Block access to wp-cron.php file. If remote cron calls not being used this can be set to Yes.',    'wp-hide-security-enhancer');
                    if (    $local_ip   === FALSE )
                        {
                            $option_description     .=   '<br /><span class="important">'  .   __('Unable to identify site domain IP, blocking wp-cron.php will stop the site internal WordPress cron functionality.',    'wp-hide-security-enhancer') .   '</span>';   
                        }
                        else
                        {
                            $option_description     .=   '<br /><span class="important">'  .   __('Site domain rezolved to IP',    'wp-hide-security-enhancer') . ' ' . $local_ip . ' ' .  __('If blocked, all internal calls to cron will continue to run fine. All calls from a different IP are blocked, including direct calls.',    'wp-hide-security-enhancer') . '</span>';
                        }
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_wp_cron_php',
                                                                    'label'         =>  __('Block wp-cron.php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  "Block access to wp-cron.php file",
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block wp-cron.php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("The file wp-cron.php is the portion of WordPress that handles scheduled events within a WordPress site. If remote cron calls not being used this can be set to Yes..",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br />" . $option_description,
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-root-files/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  55
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_default_wp_signup_php',
                                                                    'label'         =>  __('Block wp-signup&period;php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block default wp-signup&period;php file. If <b>anyone can register</b> on your site, you shold keep this off.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block default wp-signup&period;php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("The wp-signup&period;php allow for anyone to register to your site. If the registration functionality is turned off, is safe to block the  wp-signup&period;php.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-root-files/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  55
                                                                    
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_default_wp_register_php',
                                                                    'label'         =>  __('Block wp-register.php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block default wp-register.php file. This file is now deprecated however still exists within code and redirected to /register page.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block wp-register.php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This is a deprecated file but still present in many WordPress installs.  When called the user is redirected to /register page. Is safe to block the wp-register.php.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-root-files/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  55
                                                                    
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_other_wp_files',
                                                                    'label'         =>  __('Block other wp-*.php files',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block other wp-*.php files. E.g. wp-blog-header.php, wp-config.php, wp-cron.php. Those files are used internally, blocking those will not affect any functionality. Other root files (wp-activate.php, wp-login.php, wp-signup.php) are ignored, they can be controlled through own setting.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block other wp-*.php files',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Block other wp-*.php files. E.g. wp-blog-header.php, wp-config.php, wp-cron.php. Those files are used internally, blocking those will not affect any functionality. Other root files (wp-activate.php, wp-login.php, wp-signup.php) are ignored, they can be controlled through own setting.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-root-files/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  56
                                                                    
                                                                    );
                                                                    
                    return $this->component_settings;   
                }
                
         
                
            function _callback_saved_block_license_txt($saved_field_data)
                {

                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if(is_multisite())
                        {
                            $blog_details = get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                            //no need to continue if path is not empty  e.g. www.domain.com/subdomain/
                            if ( $blog_details->path !=  '/' )
                                return FALSE;
                        }
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'license.txt', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                                                            
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                            
                            if(!is_multisite() )
                                {
                                    $rewrite   .=   "\nRewriteRule ^" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite   .=   "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                            
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_license_txt" stopProcessing="true">';
                            
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';                            
                        }
                    
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            
                            $rewrite        =   array();                    
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'license.txt', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            if(!is_multisite() )
                                {
                                    $rewrite_list['blog_id'] =   $blog_id;
                                }
                                else
                                    $rewrite_list['blog_id'] =   'network';
                                    
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '';
                                                        
                            $rewrite_data   =   '';
       
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                                }
                            
                            $rewrite_data  .= "\n             rewrite \"^".  $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n         }";
                                }
     
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                                
                        }
                                
                    $processing_response['rewrite'] = $rewrite;            
                                
                    return  $processing_response;     
                    
                    
                }
                
            function _callback_saved_block_readme_html($saved_field_data)
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if(is_multisite())
                        {
                            $blog_details = get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                            //no need to continue if path is not empty  e.g. www.domain.com/subdomain/
                            if ( $blog_details->path !=  '/' )
                                return FALSE;
                        }                                
                        
                    $rewrite                            =  '';
                    
                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'readme.html', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                                        
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                                        

                            if(!is_multisite() )
                                {
                                    $rewrite   .=   "\nRewriteRule ^" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite   .=   "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                            
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_readme_html" stopProcessing="true">';
                            
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
             
                        }
                        
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                              
                            $rewrite        =   array();                    
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'readme.html', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            if(!is_multisite() )
                                {
                                    $rewrite_list['blog_id'] =   $blog_id;
                                }
                                else
                                    $rewrite_list['blog_id'] =   'network';
                                    
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '';
                                                        
                            $rewrite_data   =   '';
    
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                                }
                            
                            $rewrite_data  .= "\n             rewrite \"^".  $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n         }";
                                }
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                                
                        }
                               
                    $processing_response['rewrite'] = $rewrite;            
                                
                    return  $processing_response;     
                    
                    
                }
                
            function _callback_saved_block_wp_activate_php($saved_field_data)
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if ( is_multisite() )
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                        }
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-activate.php', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                                        
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                                        
                            
                            if(!is_multisite() )
                                {
                                    $rewrite   .=   "\nRewriteRule ^" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite   .=   "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }

                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_wp_activate_php" stopProcessing="true">';
                            
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';   
                            
                        }
                        
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            
                            $rewrite        =   array();                    
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-activate.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            if(!is_multisite() )
                                {
                                    $rewrite_list['blog_id'] =   $blog_id;
                                }
                                else
                                    $rewrite_list['blog_id'] =   'network';
                                    
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '';
                                                        
                            $rewrite_data   =   '';

                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                                }
                                
                            $rewrite_data  .= "\n             rewrite \"^".  $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n         }";
                                    $rewrite_data  .=    "\n\n         #" . __('REPLACE THE FOLLOWING LINE WITH YOUR OWN INCLUDE! This can be found within block', 'wp-hide-security-enhancer') ."  location ~ \.php$";
                                    $rewrite_data  .=    "\n" .'         include snippets/fastcgi-php.conf; fastcgi_pass unix:/run/php/php7.0-fpm.sock;';
                                }
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                                
                        }
                                  
                    $processing_response['rewrite'] = $rewrite;            
                                
                    return  $processing_response;     
                    
                    
                }
                
                
            function _callback_saved_block_wp_cron_php($saved_field_data)
                {

                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if ( is_multisite() )
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                        }
                    
                    $local_ips          =   $this->domain_get_ip();
                    
                    $local_ips          =   apply_filters('wph/components/block_wp_cron_php/local_ips', (array)$local_ips );
                    $local_ips          =   array_filter( $local_ips );   
                        
                    $rewrite            =   '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-cron.php', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                                        
                            if  ( $local_ips !== FALSE  &&  is_array ( $local_ips ) &&  count ( $local_ips ) > 0 )
                                {
                                    foreach ( $local_ips as     $local_ip )
                                        {
                                            $rewrite   .=  "\nRewriteCond %{REMOTE_ADDR} !^".  str_replace(".",'\.', $local_ip )  ."$";
                                        }
                                }
                                        
                            $rewrite   .=   "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ." ".  $rewrite_to ." [END]";

                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_wp_cron_php" stopProcessing="true">';
                            
                                                  
                            $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                            if  ( $local_ips !== FALSE  &&  is_array ( $local_ips ) &&  count ( $local_ips ) > 0 )
                                {
                                    foreach ( $local_ips as     $local_ip )
                                        {
                                            $rewrite  .=  '
                                                            <conditions>  
                                                                <add input="{REMOTE_ADDR}" pattern="^'.  str_replace(".",'\.', $local_ip )  . '$" ignoreCase="true" negate="true" />
                                                            </conditions>';
                                        }
                                }
                            $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                            
                            $rewrite .=  "\n" . '</rule>';

                        }
                        
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            
                            $rewrite        =   array();                    
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-cron.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            if(!is_multisite() )
                                {
                                    $rewrite_list['blog_id'] =   $blog_id;
                                }
                                else
                                    $rewrite_list['blog_id'] =   'network';
                                    
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '';
                                                        
                            $rewrite_data   =   '';

                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                                }
                            
                            $rewrite_data  .= "\n             rewrite \"^". $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n         }";
                                    $rewrite_data  .=    "\n\n         #" . __('REPLACE THE FOLLOWING LINE WITH YOUR OWN INCLUDE! This can be found within block', 'wp-hide-security-enhancer') ."  location ~ \.php$";
                                    $rewrite_data  .=    "\n" .'         include snippets/fastcgi-php.conf; fastcgi_pass unix:/run/php/php7.0-fpm.sock;';
                                }
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                                
                        }
                               
                    $processing_response['rewrite'] = $rewrite;            
                                
                    return  $processing_response;     
                    
                    
                }
                
            function _callback_saved_block_default_wp_signup_php($saved_field_data)
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if ( is_multisite() )
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                        }
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-signup.php', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                              
                            if(!is_multisite() )
                                {
                                    $rewrite   .=      "\nRewriteRule ^" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite   .=      "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }

                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_default_wp_signup_php" stopProcessing="true">';
                            
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
                        }
                        
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            
                            $rewrite        =   array();                    
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-signup.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            if(!is_multisite() )
                                {
                                    $rewrite_list['blog_id'] =   $blog_id;
                                }
                                else
                                    $rewrite_list['blog_id'] =   'network';
                                    
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '';
                                                        
                            $rewrite_data   =   '';

                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                                }
                                
                            $rewrite_data  .= "\n             rewrite \"^". $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n         }";
                                    $rewrite_data  .=    "\n\n         #" . __('REPLACE THE FOLLOWING LINE WITH YOUR OWN INCLUDE! This can be found within block', 'wp-hide-security-enhancer') ."  location ~ \.php$";
                                    $rewrite_data  .=    "\n" .'         include snippets/fastcgi-php.conf; fastcgi_pass unix:/run/php/php7.0-fpm.sock;';
                                }
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                                
                        }
                               
                    $processing_response['rewrite'] = $rewrite;    
                                                    
                    return  $processing_response;   
                }
                
                
            function _callback_saved_block_default_wp_register_php( $saved_field_data )
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if ( is_multisite() )
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                        }
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-register.php', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                                        
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                              
                            
                            if(!is_multisite() )
                                {
                                    $rewrite   .=      "\nRewriteRule ^" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite   .=      "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_default_wp_register_php" stopProcessing="true">';
                              
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';   
                        }
                        
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                             
                            $rewrite        =   array();                    
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-register.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if( ! is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                                      
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '';
                                                        
                            $rewrite_data   =   '';

                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                                }
                                
                            $rewrite_data  .= "\n             rewrite \"^". $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n         }";
                                    $rewrite_data  .=    "\n\n         #" . __('REPLACE THE FOLLOWING LINE WITH YOUR OWN INCLUDE! This can be found within block', 'wp-hide-security-enhancer') ."  location ~ \.php$";
                                    $rewrite_data  .=    "\n" .'         include snippets/fastcgi-php.conf; fastcgi_pass unix:/run/php/php7.0-fpm.sock;';
                                }
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                                
                        }
                                    
                    $processing_response['rewrite'] = $rewrite;    
                                                    
                    return  $processing_response;   
                }

            function _callback_saved_block_other_wp_files($saved_field_data)
                {

                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if ( is_multisite() )
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                        }
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-([0-9a-z-_])+.php', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                    
                    $rewrite_conditional=   $this->wph->functions->get_rewrite_base( '', TRUE, FALSE, 'wp_path' );
                                        
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                              
 
                            if(!is_multisite() )
                                {
                                    $rewrite_conditional    =   trailingslashit( $rewrite_conditional );
                                    
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !".$rewrite_conditional."wp-activate.php [NC]";
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !".$rewrite_conditional."wp-cron.php [NC]";
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !".$rewrite_conditional."wp-signup.php [NC]";
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !".$rewrite_conditional."wp-comments-post.php [NC]";
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !".$rewrite_conditional."wp-login.php [NC]";
                                    
                                    $rewrite   .=      "\nRewriteRule ^" . $rewrite_base . " ".  $rewrite_to ." [NC,END]";
                                }
                                else
                                {
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !(/[_0-9a-zA-Z-]+/)?".$rewrite_conditional."wp-activate.php [NC]";
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !(/[_0-9a-zA-Z-]+/)?".$rewrite_conditional."wp-cron.php [NC]";
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !(/[_0-9a-zA-Z-]+/)?".$rewrite_conditional."wp-signup.php [NC]";
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !(/[_0-9a-zA-Z-]+/)?".$rewrite_conditional."wp-comments-post.php [NC]";
                                    $rewrite   .=       "\nRewriteCond %{REQUEST_URI} !(/[_0-9a-zA-Z-]+/)?".$rewrite_conditional."wp-login.php [NC]";
                                    
                                    $rewrite   .=      "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base . " ".  $rewrite_to ." [NC,END]";
                                }
                            
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_other_wp_files" stopProcessing="true">';
                            $rewrite   .=   "\n" . '   <conditions>';
      
                            if(!is_multisite() )
                                {
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="'.$rewrite_conditional.'wp-activate.php" ignoreCase="true" negate="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="'.$rewrite_conditional.'wp-cron.php" ignoreCase="true" negate="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="'.$rewrite_conditional.'wp-signup.php" ignoreCase="true" negate="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="'.$rewrite_conditional.'wp-comments-post.php" ignoreCase="true" negate="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="'.$rewrite_conditional.'wp-login.php" ignoreCase="true" negate="true" />';
                                }
                                else
                                {
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="(/[_0-9a-zA-Z-]+/)?'.$rewrite_conditional.'wp-activate.php" ignoreCase="true" negate="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="(/[_0-9a-zA-Z-]+/)?'.$rewrite_conditional.'wp-cron.php" ignoreCase="true" negate="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="(/[_0-9a-zA-Z-]+/)?'.$rewrite_conditional.'wp-signup.php" ignoreCase="true" negate="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="(/[_0-9a-zA-Z-]+/)?'.$rewrite_conditional.'wp-comments-post.php" ignoreCase="true" negate="true" />';
                                    $rewrite  .=      "\n" .    '       <add input="{REQUEST_FILENAME}" pattern="(/[_0-9a-zA-Z-]+/)?'.$rewrite_conditional.'wp-login.php" ignoreCase="true" negate="true" />';
                                }
                                
                            $rewrite   .=   "\n" . '   </conditions>';
                            
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';   
                        }
                    
                    
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            $rewrite        =   array();                    
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-([0-9a-z-_])+\.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if( ! is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                                      
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '';
                                                        
                            $rewrite_data   =   '';
                            
                            $request_uri_math   =   $this->wph->functions->get_rewrite_path( 'wp-(activate|cron|signup|comments-post|login)', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                                
                            $rewrite_data  .=   'if ($request_uri !~* "'. $request_uri_math .'\.php") {';
                            $rewrite_data  .=   "\n             rewrite \"^". $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                            $rewrite_data  .=   "\n}";
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                                
                        }
                                    
                    $processing_response['rewrite'] = $rewrite;    
                                                    
                    return  $processing_response;   
                }
                
                
            /**
            * Return current domain reversed ip
            *     
            */
            function domain_get_ip()
                {
                    $local_ip  =   get_site_transient( 'wphide-local-ip' );
                    if ( empty ( $local_ip ) )
                        $local_ip   =   FALSE;
                    
                    if ( $local_ip !== FALSE )
                        return $local_ip;
                    
                    $site_domain_parsed =   parse_url( home_url() );
                    if ( $site_domain_parsed !==    FALSE   &&  function_exists('gethostbyname')    &&  function_exists('ip2long') )
                        {
                            $site_domain_is_ip  =   ip2long( $site_domain_parsed['host'] )  === FALSE   ?   FALSE   :   TRUE;
                            $local_ip   =   gethostbyname( $site_domain_parsed['host'] );
                            
                            if  ( $site_domain_is_ip    === FALSE  &&   $local_ip  ==  $site_domain_parsed['host'] )
                                $local_ip   =   FALSE;
                            
                        }
                    
                    set_site_transient( 'wphide-local-ip', $local_ip, 60 * 60 * 48 );
                    
                    return $local_ip;
                }
                
        }
?>