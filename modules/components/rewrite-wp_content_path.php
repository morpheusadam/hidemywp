<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_wp_content_path extends WPH_module_component
        {
            function get_component_title()
                {
                    return "WP content";
                }
                                        
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'new_content_path',
                                                                    'label'         =>  __('New Content Path',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Your default wp-content path is set to',    'wp-hide-security-enhancer') . ' <strong>'.   $this->wph->default_variables['content_directory'] .'</strong>',
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New Content Path',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("As default a WordPress installation contain a wp-content folder which store files and resources used by themes and plugin. The wp-content is a common fingerprint, which makes easily to anyone to identify the site as being created on WordPress.",    'wp-hide-security-enhancer') . " <br />  <br />
                                                                                                                                            <code>&lt;script type='text/javascript' src='https://-domain-name-/wp-content/cache/static/asset.js'&gt;&lt;/script&gt;</code>
                                                                                                                                            <br /><br /> " . __("After filling in this option e.g. data the links become:",    'wp-hide-security-enhancer') . " <br />  <br /> 
                                                                                                                                            <code>&lt;script type='text/javascript' src='https://-domain-name-/data/cache/static/asset.js'&gt;&lt;/script&gt;</code>",
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-wp-content/'
                                                                                                        ),
                                                                    
                                                                    'value_description' =>  __('e.g. my_content',    'wp-hide-security-enhancer'),
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name')),
                                                                    'processing_order'  =>  90
                                                                    );
                    
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_wp_content_path',
                                                                    'label'         =>  __('Block wp-content URL',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block default /wp-content/ path. Your default wp-content path is set to',    'wp-hide-security-enhancer') . ' <strong>'.   $this->wph->default_variables['content_directory'] .'</strong>',
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block wp-content URL',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This blocks the default wp-content urls only for non loged-in users.<br />The functionality apply only if <b>New Content Path</b> option is filled in.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-wp-content/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  91
                                                                    );
                                                                    
                    return $this->component_settings;   
                }
                
            
            function _init_new_content_path ( $saved_field_data )
                {
                    if(empty($saved_field_data))
                        return FALSE;
 
                    $content_directory  =   $this->wph->default_variables['content_directory'];
                    
                    //add default plugin path replacement
                    $new_content_path   =   $this->wph->functions->prepare_path (    $this->wph->default_variables['url']  . '/' .  $saved_field_data    );
                    $this->wph->functions->add_replacement( $this->wph->functions->prepare_path (    site_url()  . '/' . $content_directory ), $new_content_path );
                    
                    return TRUE;
                }
                
            function _callback_saved_new_content_path($saved_field_data)
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
                    
                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  FALSE, 'right_slash' =>  TRUE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( $this->wph->default_variables['network']['content_path'], [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE  ] );
                               
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                                
                            if(!is_multisite() )
                                {
                                    $rewrite .= "\nRewriteRule ^"    .   $rewrite_base   .   '(.+) '. $rewrite_to .'$1 [QSA,L]';
                                }
                                else
                                {
                                    $rewrite .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base   .   '(.+) '. $rewrite_to .'$2 [QSA,L]';    
                                }
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-new_content_path" stopProcessing="true">';
                                
                            if(!is_multisite() )
                                {
                                    $rewrite .=     "\n"    .   '    <match url="^'.  $rewrite_base   .'(.*)"  />';
                                    $rewrite .=     "\n"    .   '    <action type="Rewrite" url="'.  $rewrite_to .'{R:1}"  appendQueryString="true" />';
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

                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                               
                            if( ! is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                                
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}wp_content__";';
                                }
                            
                            $rewrite_data   =   '';
                                   
                            //$rewrite_data .= "\n         rewrite \"^". untrailingslashit($home_root_path) ."__WPH_SITES_SLUG__/". $rewrite_base ."(.+)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."(.+)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                                  
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;    
                        }    
                    
                    $processing_response['rewrite'] =   $rewrite;
                                
                    return  $processing_response;   
                }
                
                    
            function _callback_saved_block_wp_content_path($saved_field_data)
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
                    
                    //prevent from blocking if the wp-include is not modified
                    $new_path       =   $this->wph->functions->get_site_module_saved_value('new_content_path',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    if (empty(  $new_path ))
                        return FALSE;
                        
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $this->wph->default_variables['network']['content_path'], [ 'left_slash'  =>  FALSE, 'right_slash' =>  TRUE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE ]  );
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                          
                            //$rewrite  .=    'RewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in.*$ [NC]';
                            $rewrite  .=    "\nRewriteCond %{ENV:REDIRECT_STATUS} ^$";
                            
                            if(!is_multisite() )
                                {
                                    $rewrite   .=   "\nRewriteRule ^".   $rewrite_base   ."(.+) ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite   .=   "\nRewriteRule ^([_0-9a-zA-Z-]+/)?".   $rewrite_base   ."(.+) ".  $rewrite_to ." [END]";
                                }
                                       
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_wp_content_path" stopProcessing="true">';

                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
     
                        }
                                 
                    
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $home_root_path =   $this->wph->functions->get_home_root();
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $this->wph->default_variables['network']['content_path'], [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ]  );
                            
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
                                            
                                            $rewrite_data               =   "rewrite \"^". untrailingslashit($home_root_path) ."__WPH_SITES_SLUG__/". untrailingslashit($rewrite_base) ."(.+)\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
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

                            $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                            $rewrite_data  .= "\n             rewrite ^__WPH_SITES_SLUG__/". $rewrite_base ."(.+) ". $rewrite_to .' last;';
                            $rewrite_data  .=    "\n         }";
                            $rewrite_data  .=    "\n\n         #" . __('REPLACE THE FOLLOWING LINE WITH YOUR OWN INCLUDE! This can be found within block', 'wp-hide-security-enhancer') ."  location ~ \.php$";
                            $rewrite_data  .=    "\n" .'         include snippets/fastcgi-php.conf; fastcgi_pass unix:/run/php/php7.0-fpm.sock;';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            $rewrite[]                  =   $rewrite_list;
                            
                            
                            
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

                            $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                            $rewrite_data  .= "\n             rewrite ^__WPH_SITES_SLUG__/". $rewrite_base ."(.+) ". $rewrite_to .' last;';
                            $rewrite_data  .=    "\n         }";                              
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;    
                        }
                    
                    $processing_response['rewrite'] = $rewrite; 
                                
                    return  $processing_response;     
                }
                
         

        }
?>