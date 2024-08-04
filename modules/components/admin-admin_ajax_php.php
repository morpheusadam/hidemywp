<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_admin_admin_ajax_php extends WPH_module_component
        {
            function get_component_title()
                {
                    return "admin-ajax&#46;php";
                }
                                    
            function get_module_component_settings()
                {
     
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'new_admin_ajax_php',
                                                                    'label'         =>  __('New admin-ajax&#46;php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Map a new slug for admin-ajax&#46;php.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New admin-ajax&#46;php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("The admin-ajax&#46;php is being used by WordPress core and many plugins to initiate AJAX calls from dashboard and front side. This is specific to WordPress, a site using such slug is an easy target to hack attempts.",    'wp-hide-security-enhancer') .
                                                                                                                                        "<br /><br />" . __("Default url can be found in html source like this",    'wp-hide-security-enhancer').
                                                                                                                                        "<br /><code>http://-domain-name-/wp&#45;admin/admin-ajax&#46;php</code>
                                                                                                                                        <br />". __("or like this",    'wp-hide-security-enhancer') .
                                                                                                                                        "<br /><code>var ajaxurl = '-domain-name-/wp&#45;admin/admin-ajax&#46;php'</code>" .
                                                                                                                                        "<br /><br />" . __("Rewriting the admin-ajax&#46;php to another slug to increase overall security for a WordPress site. It also allow to restrict admin access for a range of IPs without disabling the admin-ajax.php functionality calls.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/admin-change-default-admin-ajax-php/',
                                                                                                        'input_value_extension'     =>  'php'
                                                                                                        ),
                                                                    
                                                                    'options_pre'   =>  '<div class="icon">
                                                                                                <img src="' . WPH_URL . '/assets/images/warning.png" />
                                                                                            </div>
                                                                                            <div class="text">
                                                                                                <p>' . __('The new ajax slug should not include a PHP extension ( e.g. admin-ajax.php ) to allow a cookie to be set on the new AJAX path, if being different from admin URL.',  'wp-hide-security-enhancer') .'</p>
                                                                                            </div>' ,
                                                                                                               
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array( array($this->wph->functions, 'sanitize_file_path_name'), array($this->wph->functions, 'extension_required', array('extension' => 'php')) ),
                                                                    'processing_order'  =>  50
                                                                    
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_default_admin_ajax_php',
                                                                    'label'         =>  __('Block default admin-ajax&#46;php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block default admin-ajax&#46;php from being accesible.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block default admin-ajax&#46;php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("If set to Yes, the old ajax url will be blocked and a default theme 404 error page will be returned.",    'wp-hide-security-enhancer') ,
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/admin-change-wp-admin/'
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
                    
                                                                    
                    return $this->component_settings;   
                }
                
                
                
            function _init_new_admin_ajax_php($saved_field_data)
                {
                    if(empty($saved_field_data))
                        return FALSE;
                    
                    if ( ! $this->ReInit )
                        {    
                            add_action('set_auth_cookie',       array($this,'set_auth_cookie'), 999, 5);
                            add_action('wp_logout',             array($this,'wp_logout'), 999, 5);
                        }
  
                    //add replacement
                    $this->wph->functions->add_replacement( 'wp-admin/admin-ajax.php',  $saved_field_data );
                    
                    $new_url    =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use());
                    if  ( ! empty ( $new_url ) )
                        {
                            $this->wph->functions->add_replacement( $new_url . '/admin-ajax.php',  $saved_field_data );
                            
                            $this->wph->functions->add_replacement( $new_url . '/' . $saved_field_data,  $saved_field_data );
                            
                            //add a direct replacement,  mainly to be used in JavaScript code replacement
                            $this->wph->functions->add_replacement( 'admin-ajax.php',  $saved_field_data );
                               
                        }
                }
                
                
            function set_auth_cookie($auth_cookie, $expire, $expiration, $user_id, $scheme) 
                {                    
                    $new_admin_ajax_php =   $this->wph->functions->get_site_module_saved_value('new_admin_ajax_php',  $this->wph->functions->get_blog_id_setting_to_use());

                    if ( $scheme == 'secure_auth' ) 
                        {
                            $auth_cookie_name = SECURE_AUTH_COOKIE;
                            $secure = TRUE;
                        } 
                    else 
                        {
                            $auth_cookie_name = AUTH_COOKIE;
                            $secure = FALSE;
                        }        
                    
                    $sitecookiepath =   empty($this->wph->default_variables['wordpress_directory']) ?   SITECOOKIEPATH  :   rtrim(SITECOOKIEPATH, trailingslashit($this->wph->default_variables['wordpress_directory']));
                    if (empty ($sitecookiepath))
                        $sitecookiepath =   '/';
                                        
                    
                    $parts = explode ( "/" , $new_admin_ajax_php );
                    if ( ! in_array ( $parts[0], array ('wp-admin' ) ))
                        {
                            setcookie($auth_cookie_name, $auth_cookie, $expire, $sitecookiepath  .   $new_admin_ajax_php, COOKIE_DOMAIN, $secure, true);
                        }
                }
                
                
            function wp_logout()
                {
                    $new_admin_ajax_php =   $this->wph->functions->get_site_module_saved_value('new_admin_ajax_php',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    $sitecookiepath =   empty($this->wph->default_variables['wordpress_directory']) ?   SITECOOKIEPATH  :   rtrim(SITECOOKIEPATH, trailingslashit($this->wph->default_variables['wordpress_directory']));
                    if (empty ($sitecookiepath))
                        $sitecookiepath =   '/';
                    
                    setcookie( AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, $sitecookiepath  .   $new_admin_ajax_php, COOKIE_DOMAIN );
                    setcookie( SECURE_AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, $sitecookiepath  .   $new_admin_ajax_php, COOKIE_DOMAIN );
                                        
                }
            
                         
            function _callback_saved_new_admin_ajax_php($saved_field_data)
                {
                    
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
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( '/wp-admin/admin-ajax.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ] );
                                        
                    $admin_url          =   $this->wph->functions->get_site_module_saved_value( 'admin_url',  $this->wph->functions->get_blog_id_setting_to_use(), 'display' );
                    if  ( empty ( $admin_url ) )
                        $admin_url  =   'wp-admin';
                        
                    $rewrite_base_default_admin   =   $this->wph->functions->get_rewrite_path( trailingslashit( $admin_url ) . $saved_field_data, [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                               
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                            if( ! is_multisite() )
                                {
                                    $rewrite    .=  "\nRewriteRule ^"    .   $rewrite_base     .   '(.*) '. $rewrite_to .'$1 [END,QSA]';
                                    $rewrite    .=  "\nRewriteRule ^"    .   $rewrite_base_default_admin     .   '(.*) '. $rewrite_to .'$1 [END,QSA]';
                                }
                                else
                                {
                                    $rewrite    .=  "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base     .   '(.*) '. $rewrite_to .'$2 [END,QSA]';
                                    $rewrite    .=  "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $this->wph->functions->get_rewrite_base( trailingslashit( $admin_url ) . $saved_field_data, FALSE, FALSE )     .   '(.*) '. $rewrite_to .'$2 [END,QSA]';
                                }
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-new_admin_ajax_php" stopProcessing="true">';
                               
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'{R:1}"  appendQueryString="true" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'{R:2}"  appendQueryString="true" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
                            
                            
                            $rewrite    .=   "\n" . '<rule name="wph-new_admin_ajax_php2" stopProcessing="true">';
                            
                                     
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $this->wph->functions->get_rewrite_base( trailingslashit( $admin_url ) . $saved_field_data, FALSE, FALSE )   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'{R:1}"  appendQueryString="true" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $this->wph->functions->get_rewrite_base( trailingslashit( $admin_url ) . $saved_field_data, FALSE, FALSE )   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'{R:2}"  appendQueryString="true" />';
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
                            $rewrite_base_default_admin   =   $this->wph->functions->get_rewrite_path( trailingslashit( $admin_url ) . $saved_field_data, [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                               
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                                
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}new_wp_login__";';
                                }
                            
                            $rewrite_data   =   '';
                                                        
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."(.*)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                            
                            
                            
                            
                            $rewrite_rules  =   array();            
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . $rewrite_base_default_admin ;
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}new_wp_login__";';
                                }
                            
                            $rewrite_data   =   '';
                            
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base_default_admin ."(.*)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                            
                            
                        }
                    
                    $processing_response['rewrite'] = $rewrite;
                                
                    return  $processing_response;   
                }
                
                
            function _init_block_default_admin_ajax_php($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
  
                }
                
            function _callback_saved_block_default_admin_ajax_php($saved_field_data)
                {
                    
                    if( $saved_field_data   !=  'yes')
                        return  FALSE;
                        
                    $processing_response    =   array();
                    
                    global $blog_id;
                    
                    if ( is_multisite() )
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network'); 
                        }
                    
                    //prevent from blocking if the new_wp_login_php is not modified
                    $new_path       =   $this->wph->functions->get_site_module_saved_value('new_admin_ajax_php',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    if (empty(  $new_path ))
                        return FALSE;
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-admin/admin-ajax.php', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE ]  );
                      
                    if($this->wph->server_htaccess_config   === TRUE)
                        {           
                            
                            if(!is_multisite() )
                                {
                                    $rewrite   .=       "\nRewriteRule ^" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite   .=       "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }

                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_default_admin_ajax_php" stopProcessing="true">';
                            
                            if(!is_multisite() )
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
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-admin/admin-ajax.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if ( $global_settings['nginx_generate_simple_rewrite']   ==  'yes' )
                                {
                                    
                                    if ( ! is_multisite() )
                                        {
                                            $rewrite        =   array();    
                                            $rewrite_list   =   array();
                                            $rewrite_rules  =   array();
                                            
                                            $rewrite_list['blog_id']        =   'network';
                                            $rewrite_list['type']           =   'location';
                                            $rewrite_list['description']    =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '(/.*\.php)'; 
                                            
                                            $rewrite_data               =   "rewrite \"^". $rewrite_base ."(.+)\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';    
                                            
                                            $rewrite_rules[]            =   $rewrite_data;
                                            $rewrite_list['data']       =   $rewrite_rules; 
                                            
                                            $rewrite[]                  =   $rewrite_list; 
                                        }
                                    
                                    $processing_response['rewrite'] = $rewrite;            
                                    return  $processing_response;    
                                }
                            
                            $rewrite        =   array();    
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                                         
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '';
                                                        
                            $rewrite_data   =   '';

                            $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                            $rewrite_data  .= "\n             rewrite ^__WPH_SITES_SLUG__/". $rewrite_base ." ". $rewrite_to .' last;';
                            $rewrite_data  .=    "\n         }";
                            $rewrite_data  .=    "\n\n         #" . __('REPLACE THE FOLLOWING LINE WITH YOUR OWN INCLUDE! This can be found within block', 'wp-hide-security-enhancer') ."  location ~ \.php$";
                            $rewrite_data  .=    "\n" .'         include snippets/fastcgi-php.conf; fastcgi_pass unix:/run/php/php7.0-fpm.sock;';                               
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;    
                        }
                               
                    $processing_response['rewrite'] = $rewrite;    
                                                    
                    return  $processing_response;   
                }
                
            
                            

        }
?>