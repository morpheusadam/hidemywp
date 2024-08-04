<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_admin_login_php extends WPH_module_component
        {
            function get_component_title()
                {
                    return "Wp-login.php";
                }
                                    
            function get_module_component_settings()
                {
                            
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'new_wp_login_php',
                                                                    'label'         =>  __('New wp-login.php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Map a new wp-login.php instead default. This also need to include <i>.php</i> extension.',  'wp-hide-security-enhancer'),
                                                                                                                               
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New wp-login.php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("There are a lot of security issues that come from having your login page open to the public. Most specifically, brute force attacks. Because of the ubiquity of WordPress, these kinds of attacks are becoming more and more common.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br />" . __("Map a new wp-login.php instead default prevent hackers boot to attempt to brute force a site login. Being known only by the site owner, the url itself becomes private.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br /><span class='important'>" . __("Make sure you keep the new login url to a safe place, in case to forget.",    'wp-hide-security-enhancer') . "</span>",
                                                                                                        'input_value_extension'     =>  'php',
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/admin-change-wp-login-php/'
                                                                                                        ),
                                                                    
                                                                    
                                                                    'options_pre'   =>  '<div class="icon">
                                                                                                <img src="' . WPH_URL . '/assets/images/warning.png" />
                                                                                            </div>
                                                                                            <div class="text">
                                                                                                <p>' . __('Make sure your log-in url is not already modified by another plugin or theme. In such case, you should disable other code and take advantage of these features.',  'wp-hide-security-enhancer') .'</p>
                                                                                            </div>' ,
                                                                    
                                                                    
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array ( array( $this->wph->functions, 'sanitize_file_path_name') ),
                                                                    'processing_order'  =>  50
                                                                    
                                                                    );
                    
                    $this->component_settings[]                  =   array(
                                                                                'type'            =>  'split'
                                                                                
                                                                                );
                    
                                                                                        
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_default_wp_login_php',
                                                                    'label'         =>  __('Block default wp-login.php',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block default wp-login.php file from being accesible.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block default wp-login.php',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("If set to Yes, the old login url will be blocked and a default theme 404 error page will be returned.",    'wp-hide-security-enhancer') .
                                                                                                                                         "<br /><br /><span class='important'>" . __('Ensure the New wp-login.php option works correctly on your server before activate this.',    'wp-hide-security-enhancer') . '</span>',
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/admin-change-wp-login-php/'
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
                                                                    'id'            =>  'new_wp_login_rewrite_mere',
                                                                    'label'         =>  __('Use mere rewrite for Block Default',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('On specific servers, blocking might not work, trigger this setting to make it compatible.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Use mere rewrite for Block Default',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __('When accessing the default login url, even if blocked, on certain servers the link might still be available. This option create an extra rewrite rule which might fix the blocking.',    'wp-hide-security-enhancer') . '</span>',
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/admin-change-wp-login-php/'
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
                
                
                
            function _init_new_wp_login_php( $saved_field_data )
                {
                    
                    $saved_field_data   =   (string)$saved_field_data;
                    
                    //check if the value has changed, e-mail the new url to site administrator                    
                    $previous_url   =   get_option('wph-previous-login-url');
                    if( $saved_field_data    !=  $previous_url )
                        {
                            update_option( 'wph-login-changed-send-email', time() + 5 );                           
                            wp_cache_flush();
                            update_option('wph-previous-login-url', $saved_field_data );  
                        }
                    
                    if ( empty  ( $saved_field_data ) ||  $saved_field_data   ==  'no' )
                        return FALSE;
  
                    if ( ! $this->ReInit )
                        {
                            add_filter('login_url',             array(  $this,'login_url'), 999, 3 );
                            
                            add_filter('site_url',              array(  $this,'site_url'), 999, 3 );
                            
                            //Some plugins (BuddyBoss) when run on MultiSite may not use correct login url
                            add_filter('lostpassword_url',      array(  $this,'lostpassword_url'), 999, 2 );
                        }
  
                    //add replacement
                    $this->wph->functions->add_replacement( trailingslashit(    site_url()  ) .  'wp-login.php',  trailingslashit(    home_url()  ) .  $saved_field_data );
                               
                }
            
            
            function login_url( $login_url, $redirect, $force_reauth )
                {
                    //ensure there is no loop with another plugin
                    $backtrace  =   debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                    foreach ( $backtrace   as $key  =>  $backtrace )
                        {
                            if ( $key < 1 )
                                continue;
                                
                            if ( isset ( $backtrace['file'] )   &&  strpos( wp_normalize_path( $backtrace['file'] ), 'modules/components/admin-login_php.php' ) )
                                return $login_url;
                        }
                    
                    $parse_login_url        =   parse_url ( $login_url );
                    $new_wp_login_php       =   $this->wph->functions->get_site_module_saved_value( 'new_wp_login_php',  $this->wph->functions->get_blog_id_setting_to_use() );
                    
                    //avoid looping
                    static $wph_home_url;
                    if ( is_null ( $wph_home_url ) )
                        $wph_home_url   =   site_url( $new_wp_login_php, 'login' );
                    
                    $login_url          =   $wph_home_url;
                   
                    if ( isset ( $parse_login_url['query'] )    &&   ! empty ( $parse_login_url['query'] ) )
                        $login_url .=   '?' .   $parse_login_url['query'];
                    
                    return $login_url;   
                }
                
                
            function site_url( $url, $path, $scheme )
                {
                    if ( ! in_array ( $scheme, array ( 'login', 'login_post' ) ) )
                        return $url;
                    
                    $new_wp_login_php       =   $this->wph->functions->get_site_module_saved_value( 'new_wp_login_php',  $this->wph->functions->get_blog_id_setting_to_use() );
                    
                    if ( ! empty ( $new_wp_login_php ) )
                        $url    =   str_replace ( 'wp-login.php', $new_wp_login_php, $url );
                        
                    return $url;
                }
                
            
            function lostpassword_url( $lostpassword_url, $redirect )
                {
                    $new_wp_login_php       =   $this->wph->functions->get_site_module_saved_value( 'new_wp_login_php',  $this->wph->functions->get_blog_id_setting_to_use() );
                    
                    $lostpassword_url       =   str_replace( 'wp-login.php', $new_wp_login_php, $lostpassword_url );
                    
                    return $lostpassword_url;   
                }
                
            function _callback_saved_new_wp_login_php($saved_field_data)
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
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'wp-login.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE ] );
                               
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                            if( !is_multisite() )
                                {
                                    $rewrite    .=  "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base     .   '(.*) '. $rewrite_to .'$2 [END,QSA]';
                                }
                                else
                                {
                                    $rewrite    .=  "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base     .   '(.*) '. $rewrite_to .'$2 [END,QSA]';
                                }
                            
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-new_wp_login_php" stopProcessing="true">';
                            
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
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}new_wp_login__";';
                                }
                            
                            $rewrite_data   =   '';
                                 
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."(.*)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                                               
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                        }
                    
                    $processing_response['rewrite'] = $rewrite;
                                
                    return  $processing_response;   
                }
                
            
            static public function check_new_url_email_notice()
                {
                    $wph_login_changed_send_email   =   get_option ( 'wph-login-changed-send-email' );
                    if ( empty ( $wph_login_changed_send_email ) )
                        return;
                    
                    $wph_login_changed_send_email   =   (int) $wph_login_changed_send_email ;
                    if ( empty ( $wph_login_changed_send_email ) )
                        return;
                                            
                    if ( $wph_login_changed_send_email < time ( ) )
                        {
                            delete_option ( 'wph-login-changed-send-email' );
                            wp_cache_flush();
                            self::new_url_email_notice();
                        }
                    
                }
            
            static public function new_url_email_notice()
                {
                    
                    global $wph;
                    
                    $to         =   get_site_option('admin_email');  
                        
                    $subject    =   'New Login Url for your WordPress - ' .get_option('blogname');
                    $message    =   __('Hello',  'wp-hide-security-enhancer') . ", \n\n" 
                                    . __('This is an automated message to inform that your login url has been changed at',  'wp-hide-security-enhancer') . " " .  trailingslashit( home_url() ) . "\n"
                                    . __('The new login url is',  'wp-hide-security-enhancer') .  ": " . wp_login_url() . "\n\n"
                                    . __('Additionally, you can use the following link to recover the default login/admin access: ',  'wp-hide-security-enhancer') .  ": " . trailingslashit ( home_url() ) . '?wph-recovery='.  $wph->functions->get_recovery_code() . "\n\n"
                                    . __('Please ensure the safety of this URL for potential recovery in case of forgetfulness.',  'wp-hide-security-enhancer') . ".";
                    $headers = 'From: '.  get_option('blogname') .' <'.  get_option('admin_email')  .'>' . "\r\n"; 
                    
                    if ( ! function_exists( 'wp_mail' ) ) 
                        require_once ABSPATH . WPINC . '/pluggable.php';
                        
                    wp_mail( $to, $subject, $message, $headers ); 
                }
            
            function _init_block_default_wp_login_php($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
  
                }
                
            function _callback_saved_block_default_wp_login_php($saved_field_data)
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
                    
                    //prevent from blocking if the new_wp_login_php is not modified
                    $new_path       =   $this->wph->functions->get_site_module_saved_value('new_wp_login_php',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');                  
                    if (empty(  $new_path ))
                        return FALSE;
                        
                    $mere_rewrite       =   $this->wph->functions->get_site_module_saved_value('new_wp_login_rewrite_mere',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-login.php', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE ]  );
                      
                    if($this->wph->server_htaccess_config   === TRUE)
                        {           
                            
                            if(!is_multisite() )
                                {
                                    $rewrite   .=       "\nRewriteCond %{ENV:REDIRECT_STATUS} ^$";
                                    $rewrite   .=       "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite   .=       "\nRewriteCond %{ENV:REDIRECT_STATUS} ^$";
                                    $rewrite   .=       "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base ." ".  $rewrite_to ." [END]";
                                }

                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_default_wp_login_php" stopProcessing="true">';
                             
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
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-login.php', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
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
                                            
                                            if  (  $mere_rewrite    !=  'yes' )
                                                $rewrite_data               =   "rewrite \"^". $rewrite_base ."(.+)?\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                                                else
                                                $rewrite_data               =   "rewrite \"^". $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                                            
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