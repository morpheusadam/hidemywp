<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_admin_admin_url extends WPH_module_component
        {
            function get_component_title()
                {
                    return "Admin URL";
                }
                                    
            function get_module_component_settings()
                {
                                    
                    $this->component_settings[]                  =   array(
                                                                        'id'            =>  'admin_url',
                                                                        'label'         =>  __('New Admin Url',    'wp-hide-security-enhancer'),
                                                                        'description'   =>  array(
                                                                                                    __('Create a new admin url instead default /wp-admin and /login.',  'wp-hide-security-enhancer')
                                                                                                    ),                                                                        
                                                                        
                                                                        'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New Admin Url',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Despite the flexibility of WordPress framework, there are few ways to configure the admin login url customization for making a bit safer against unauthorized access and brute force attempts. All methods are not provided out of the box through WordPress core but require custom code to make it happen.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br />". __("This feature provide an easy way to change the default /wp-admin/ to a different slug.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br />". __("Once changed, the new url will be used to access all Dashboard sections, from Posts and Pages section to Plugins, Appearance and Settings.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/admin-change-wp-admin/'
                                                                                                        ),
                                                                                                                                                                               
                                                                        'input_type'    =>  'text',
                                                                        
                                                                        'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name'), array($this, 'sanitize_path_name')),
                                                                        'processing_order'  =>  60
                                                                        
                                                                        );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                        'id'            =>  'block_default_admin_url',
                                                                        'label'         =>  __('Block default Admin Url',    'wp-hide-security-enhancer'),
                                                                        'description'   =>  __('Block default admin url and files from being accesible.',  'wp-hide-security-enhancer'),
                                                                        
                                                                        'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block default Admin Url',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("If set to Yes, the old admin url will be blocked and a default theme 404 error page will be returned.",    'wp-hide-security-enhancer') ,
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/admin-change-wp-admin/'
                                                                                                        ),
                                                                        'input_type'    =>  'radio',
                                                                        'options'       =>  array(
                                                                                                    'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                    'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                    ),
                                                                        'default_value' =>  'no',
                                                                        
                                                                        'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                        'processing_order'  =>  65
                                                                        
                                                                        );
                                                                    
                    return $this->component_settings;   
                }
                
                
                
            function _init_admin_url($saved_field_data)
                {
                    
                    global $blog_id;
                    
                    $admin_url_to_apply =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    if ( ! $this->ReInit )
                        {
                            if ( ! empty ( $admin_url_to_apply ) )
                                {
                                    add_action('set_auth_cookie',       array($this,'set_auth_cookie'), 999, 5);
                                    add_action('wp_logout',             array($this,'wp_logout'), 999, 5);
                                }
                        }
                    
                    if(empty($saved_field_data))
                        return FALSE;
                        
                    //remove redirects for /admin and /dashboard
                    if ( ! $this->ReInit )
                        remove_action( 'template_redirect', 'wp_redirect_admin_locations', 1000 );
                               
                    //add replacement
                    //else ->simple replacement to ensure it catch all domains in the network, if MultiSite
                    if ( ! is_multisite () )
                        $this->wph->functions->add_replacement( $this->wph->functions->prepare_path (    site_url()  . '/' . 'wp-admin' ) , $this->wph->functions->prepare_path (    site_url()   . '/' .  $saved_field_data ) );
                        else
                        $this->wph->functions->add_replacement( '/wp-admin' , '/' .  $saved_field_data );
                         
                    //make sure the admin url redirect url is updated when updating WordPress Core
                    if ( ! $this->ReInit )
                        {
                            add_filter('user_admin_url',    array($this, 'wp_core_update_user_admin_url'), 999, 2);
                            add_filter('admin_url',         array($this, 'wp_core_update_admin_url'),      999, 3);
                            
                            //ensure admin_url() return correct url
                            add_filter('admin_url',         array($this, 'update_admin_url'),      999, 3);
                        }               
                }
                
            function set_auth_cookie($auth_cookie, $expire, $expiration, $user_id, $scheme) 
                {                    
                    $new_admin_url =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use());

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
                    
                    /*
                    $sitecookiepath =   empty($this->wph->default_variables['wordpress_directory']) ?   SITECOOKIEPATH  :   rtrim(SITECOOKIEPATH, trailingslashit($this->wph->default_variables['wordpress_directory']));
                    if (empty ($sitecookiepath))
                        $sitecookiepath =   '/';
                    */
                    $sitecookiepath =   trailingslashit ( SITECOOKIEPATH . $new_admin_url );
                    
                    setcookie($auth_cookie_name, $auth_cookie, $expire, $sitecookiepath, COOKIE_DOMAIN, $secure, true);
       
                }
                
            function wp_logout()
                {
                    $new_admin_url =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use());
                           
                    $sitecookiepath =   empty($this->wph->default_variables['wordpress_directory']) ?   SITECOOKIEPATH  :   rtrim(SITECOOKIEPATH, trailingslashit($this->wph->default_variables['wordpress_directory']));
                    if (empty ($sitecookiepath))
                        $sitecookiepath =   '/';
                    
                    setcookie( AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, $sitecookiepath  .   $new_admin_url, COOKIE_DOMAIN );
                    setcookie( SECURE_AUTH_COOKIE, ' ', time() - YEAR_IN_SECONDS, $sitecookiepath  .   $new_admin_url, COOKIE_DOMAIN );
                                        
                }

                
            function _callback_saved_admin_url($saved_field_data)
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
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'wp-admin', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE ] );
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                                
                            if( ! is_multisite() )
                                {
                                    $rewrite   .=      "\nRewriteCond %{REQUEST_URI} /".  $rewrite_base ."$";
                                    $rewrite   .=      "\nRewriteRule ^(.*)$ /".  $rewrite_base ."/ [R=301,END]";
                                    $rewrite   .=      "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base    .   '/(.*) '. $rewrite_to .'/$2 [END,QSA]';
                                }
                                else
                                {
                                    $rewrite   .=      "\nRewriteCond %{REQUEST_URI} (/[_0-9a-zA-Z-]+/)?/".  $rewrite_base ."$";
                                    $rewrite   .=      "\nRewriteRule ^(.*)$ /".  $rewrite_base ."/ [R=301,END]";    
                                    $rewrite   .=      "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base    .   '/(.*) '. $rewrite_to .'/$2 [END,QSA]';    
                                }                            
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-admin_url1" stopProcessing="true">';
                            $rewrite   .=   "\n" . '   <conditions>';
       
                            if(!is_multisite() )
                                {
                                    $rewrite   .=      "\n" .   '       <add input="{REQUEST_URI}" matchType="Pattern" pattern="/'. $rewrite_base  .'$"  />';
                                }
                                else
                                {
                                    $rewrite   .=      "\n" .   '       <add input="{REQUEST_URI}" matchType="Pattern" pattern="(/[_0-9a-zA-Z-]+/)?/'. $rewrite_base  .'$"  />';
                                }
                            $rewrite   .=   "\n" . '   </conditions>';
                            
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
                            
                            
                            $rewrite    .=   "\n\n" . '<rule name="wph-admin_url2" stopProcessing="true">';
                            
                            
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
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}new_wp_admin__";';
                                }
                            
                            $rewrite_data   =   '';
                            
                            if ( $this->wph->functions->server_is_wpengine() )
                                {
                                    $rewrite_data .= "\n" . '         if ( $http_cookie ~* "wordpress_logged_in" )';
                                    $rewrite_data .= "\n" . '         { set $is_trusted 1; }';
                                }

                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."/$\" ". $rewrite_to ."/index.php ".  $this->wph->functions->get_nginx_flag_type() .";";
                            
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."(.*)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                        }    
                        
                    $processing_response['rewrite']         =   $rewrite;
                                                    
                    return  $processing_response;   
                }
                
            
            function _init_block_default_admin_url($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
       
                }
                
            function _callback_saved_block_default_admin_url($saved_field_data)
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return  FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    
                    if ( is_multisite() )
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network'); 
                        }
                    
                    //prevent from blocking if the admin_url is empty
                    $new_path       =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    if (empty(  $new_path ))
                        return FALSE;
                        
                    $rewrite                            =  '';
                               
                    $rewrite_base       =   $this->wph->functions->get_rewrite_base( 'wp-admin', FALSE, FALSE, 'wp_path' );
                    $rewrite_to         =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE ]  ); 

                                
                    if($this->wph->server_htaccess_config   === TRUE)
                        {           

                            if(!is_multisite() )
                                {
                                    $rewrite   .=       "\nRewriteCond %{ENV:REDIRECT_STATUS} ^$";
                                    $rewrite   .=      "\nRewriteRule ^" . $rewrite_base ."(.+) " . $rewrite_to . " [L]";
                                }
                                else
                                {
                                    $rewrite   .=       "\nRewriteCond %{ENV:REDIRECT_STATUS} ^$";
                                    $rewrite   .=      "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ."(.+) " . $rewrite_to . " [L]";
                                }
               
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_default_admin_url1" stopProcessing="true">';
                                                        
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="true" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="true" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
                                                        
                        }
                        
                    
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                                                        
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base       =   $this->wph->functions->get_rewrite_path( 'wp-admin', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ]  );
                            
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
                                 
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '(/.*\.php)';
                            
                            $rewrite_data   =   '';
                            $rewrite_data  .=   "\n".'         if ( $wph_remap = "" ) {';
                            $rewrite_data  .=   "\n             rewrite ^__WPH_SITES_SLUG__/". $rewrite_base ."(.+) ". $rewrite_to .' last;';
                            $rewrite_data  .=   "\n         }";
                            $rewrite_data  .=   "\n\n         #" . __('REPLACE THE FOLLOWING LINE WITH YOUR OWN INCLUDE! This can be found within block', 'wp-hide-security-enhancer') ."  location ~ \.php$";
                            $rewrite_data  .=   "\n" .'         include snippets/fastcgi-php.conf; fastcgi_pass unix:/run/php/php7.0-fpm.sock;';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            $rewrite[]                  =   $rewrite_list;
                            
                            
                        }
                               
                    $processing_response['rewrite'] = $rewrite;
                                
                    return  $processing_response;   
                }
                
            
            /**
            * Replace any dots in the slug, as it will confuse the server uppon being an actual file
            *     
            * @param mixed $value
            */
            function sanitize_path_name( $value )
                {
                    
                    $value  =   str_replace(".","-", $value);
                    
                    return $value;   
                    
                }
                
                
                
            function wp_core_update_user_admin_url( $url, $path )
                {
                    
                    if( strpos( $_SERVER['REQUEST_URI'], "/update-core.php")    === FALSE )
                        return $url;
                        
                    //replace the wp-admin with custom slug
                    $admin_url     =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    $url    =   str_replace('/wp-admin', '/' . $admin_url, $url);

                    return $url;
                       
                }

            function wp_core_update_admin_url( $url, $path, $blog_id )
                {
                    
                    if( strpos( $_SERVER['REQUEST_URI'], "/update-core.php")    === FALSE && strpos( $_SERVER['REQUEST_URI'], "/update.php")    === FALSE)
                        return $url;
                    
                    //replace the wp-admin with custom slug
                    $admin_url     =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    $url    =   str_replace('/wp-admin', '/' . $admin_url, $url);
                        
                    return $url;
                       
                }
                
                
            function update_admin_url( $url, $path, $blog_id )
                {
                   
                    //replace the wp-admin with custom slug
                    $admin_url     =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    $url    =   str_replace( '/wp-admin', '/' . $admin_url, $url );
                        
                    return $url;
                       
                }

                
        }
?>