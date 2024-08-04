<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_new_upload_path extends WPH_module_component
        {
            
            function get_component_title()
                {
                    return "Uploads";
                }
                                    
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'new_upload_path',
                                                                    'label'         =>  __('New Uploads Path',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('The default uploads path is set to',    'wp-hide-security-enhancer') . ' <strong>'. $this->wph->default_variables['uploads_directory']  .'</strong>',
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New Uploads Path',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Use any alphanumeric symbols for this field which will be used as the new slug for the uploads folder. Using this option the default media folder can be mapped to another path. Filling with a slug like 'media' the links become like this:",    'wp-hide-security-enhancer') . "<br />  <br />
                                                                                                                                            <code>&lt;img class=&quot;alignnone size-full&quot; src=&quot;http://domain.com/media/106658.jpg&quot; alt=&quot;&quot; width=&quot;640&quot; height=&quot;390&quot; alt=&quot;&quot; /&gt;</code>",
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-uploads/'
                                                                                                        ),
                                                                                                                                        
                                                                    'value_description' =>  __('e.g. my_uploads',    'wp-hide-security-enhancer'),
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name')),
                                                                    'processing_order'  =>  40
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_upload_url',
                                                                    'label'         =>  __('Block default uploads URL',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block default /wp-content/uploads/ media folder from being accesible through default urls.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block default uploads URL',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This blocks the default wp-content/plugins/ url.<br />The functionality apply only if <b>New Uploads Path</b> option is filled in.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-uploads/'
                                                                                                        ),
                                                                        
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  45
                                                                    
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'redirect_default_urls',
                                                                    'label'         =>  __('Redirect old URLs',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('All old URLs using format /wp-content/uploads/ will be redirected to new upload path.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Redirect old URLs',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("If option active, all old URLs using format /wp-content/uploads/ will be redirected to the new upload path.<br />The redirect type is 301 - Moved Permanently, is recommended for SEO purposes. <br />The functionality apply only if <b>New Uploads Path</b> option is filled in.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-uploads/'
                                                                                                        ),
                                                                        
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  45
                                                                    
                                                                    );
                                                                    
                    return $this->component_settings;   
                }
                
                
                
            function _init_new_upload_path($saved_field_data)
                {
                    if(empty($saved_field_data))
                        return FALSE;
                                                      
                    //add default plugin path replacement
                    $new_upload_path        =   $this->wph->functions->untrailingslashit_all(    $this->wph->functions->get_site_module_saved_value('new_upload_path', $this->wph->functions->get_blog_id_setting_to_use() )  );
                    $new_url                =   $this->wph->functions->prepare_path (  site_url() .  '/' . $new_upload_path );
                    
                    if(is_multisite())
                        {
                            global $blog_id;
                            
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                            
                            if (  $blog_id < 2)
                                $this->wph->functions->add_replacement( $this->wph->default_variables['url'] . $this->wph->default_variables['uploads_directory'], $new_url);
                                else
                                {
                                    $this->wph->functions->add_replacement( $this->wph->default_variables['url'] . str_replace("/sites/" . $blog_id , "", $this->wph->default_variables['uploads_directory']), $new_url);
                                }
                        }
                        else
                        $this->wph->functions->add_replacement( $this->wph->default_variables['url'] . $this->wph->default_variables['uploads_directory'], $new_url);
                    
                }
            
                
            function _callback_saved_new_upload_path($saved_field_data)
                {

                    //check if the field is noe empty
                    if(empty($saved_field_data))
                        return  FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if(is_multisite())
                        {
                            $use_blog_id    =   1;
                                
                            $blog_details   =   get_blog_details( $use_blog_id );
                        }
                    
                    $rewrite                            =  '';
                    
                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  FALSE, 'right_slash' =>  TRUE ] );
                    
                    $upload_path    =   isset ( $this->wph->default_variables['network']['uploads_path'] ) ?    $this->wph->default_variables['network']['uploads_path']    :   $this->wph->default_variables['uploads_directory'];
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( $upload_path, [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE  ] );
                               
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                            
                            if( ! is_multisite() )
                                {
                                    $rewrite .= "\nRewriteRule ^"    .   $rewrite_base   .   '(.+) '. $rewrite_to .'$1 [END,QSA]';
                                }
                                else
                                {
                                    $rewrite .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base   .   '(.+) '. $rewrite_to .'$2 [END,QSA]';
                                }
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-new_upload_path" stopProcessing="true">';
                            
                            if( ! is_multisite() )
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
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                               
                            if( ! is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                                
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}uploads__";';
                                }
                            
                            $rewrite_data   =   '';
                                       
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."(.+)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;    
                        }
                    
                    $processing_response['rewrite'] = $rewrite;
                                
                    return  $processing_response;   
                }
                
                                     
            function _callback_saved_block_upload_url($saved_field_data)
                {

                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    
                    if ( is_multisite() )
                        {
                            $use_blog_id    =   1;
                                
                            $blog_details   =   get_blog_details( $use_blog_id ); 
                        }
                    
                    //prevent from blocking if the wp-include is not modified
                    $new_path     =   $this->wph->functions->get_site_module_saved_value('new_upload_path',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    if (empty(  $new_path ))
                        return FALSE;
                        
                        
                    //if rdirect active, no need to block
                    $redirect_default_urls  =   $this->wph->functions->get_site_module_saved_value('redirect_default_urls',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    if ( $redirect_default_urls ==  'yes' )
                        return FALSE;
                        
                    $rewrite                            =  '';
                            
                                        
                    $uploads_path   =   '';
                    if(is_multisite())
                        {
                            $uploads_path   .=   str_replace( $blog_details->domain , "" ,  str_replace(array('http://','https://'), "", $this->wph->default_variables['network']['uploads_path'] )  );  
                        }
                        else
                        {
                            $wp_upload_dir  =   $this->wph->functions->get_wp_upload_dir();
                            
                            $site_url       =   str_replace(array( 'http://', 'https://' ), '', site_url() );
                            $baseurl        =   str_replace(array( 'http://', 'https://' ), '', $wp_upload_dir['baseurl'] );
                            $uploads_path   =   str_replace( $site_url , "" ,  $baseurl   );
                        }
                    
                    $uploads_path   =   ltrim($uploads_path, "/");
                    
                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $uploads_path, [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                    
                                        
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                                        
                            
                            if( ! is_multisite() )
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
                            $rewrite    =   "\n" . '<rule name="wph-block_upload_url" stopProcessing="true">';
                            
                            if( ! is_multisite() )
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
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $uploads_path, [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
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
                            
                            if( ! is_multisite() )
                                {
                                    $rewrite_list['blog_id'] =   $blog_id;
                                    if( is_multisite() )
                                        {
                                            $uploads_path   =   ltrim($this->wph->functions->string_left_replacement($uploads_path, ltrim($blog_details->path, '/')));
                                        }
                                }
                                else
                                    $rewrite_list['blog_id'] =   'network';
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            if( ! is_multisite() )
                                {
                                    $rewrite_list['blog_id'] =   $blog_id;
                                }
                                else
                                    $rewrite_list['blog_id'] =   'network';
                                    
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($uploads_path) . '';
                                                        
                            $rewrite_data   =   '';
                            
                            $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                            $rewrite_data  .= "\n             rewrite ^__WPH_SITES_SLUG__/". $uploads_path ."(.+) ". $rewrite_to .' last;';
                            $rewrite_data  .=    "\n         }";
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;    
                        }
                               
                    $processing_response['rewrite'] = $rewrite;            
                                
                    return  $processing_response;     
                    
                    
                }
                
                
                
            function _callback_saved_redirect_default_urls( $saved_field_data )
                {

                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    
                    if ( is_multisite() )
                        {
                            $use_blog_id    =   1;
                                
                            $blog_details   =   get_blog_details( $use_blog_id ); 
                        }
                    
                    //prevent from blocking if the wp-include is not modified
                    $new_path     =   $this->wph->functions->get_site_module_saved_value('new_upload_path',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    if (empty(  $new_path ))
                        return FALSE;
                        
                    $rewrite                            =  '';
                            
                                        
                    $uploads_path   =   '';
                    if(is_multisite())
                        {
                            $uploads_path   .=   str_replace( $blog_details->domain , "" ,  str_replace(array('http://','https://'), "", $this->wph->default_variables['network']['uploads_path'] )  );  
                        }
                        else
                        {
                            $wp_upload_dir  =   $this->wph->functions->get_wp_upload_dir();
                            
                            $site_url       =   str_replace(array( 'http://', 'https://' ), '', site_url() );
                            $baseurl        =   str_replace(array( 'http://', 'https://' ), '', $wp_upload_dir['baseurl'] );
                            $uploads_path   =   str_replace( $site_url , "" ,  $baseurl   );
                        }
                    
                    $uploads_path   =   ltrim($uploads_path, "/");

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $uploads_path, [ 'left_slash'  =>  FALSE, 'right_slash' =>  TRUE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( $new_path, [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE ] );
                                        
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                                        
                            if( !is_multisite() )
                                {
                                    $rewrite   .=   "\nRewriteRule ^".   $rewrite_base   ."(.+) ".  $rewrite_to .'$1 [R=301,END,QSA]';
                                }
                                else
                                {
                                    $rewrite   .=   "\nRewriteRule ^([_0-9a-zA-Z-]+/)?".   $rewrite_base   ."(.+) ".  $rewrite_to .'$2 [R=301,END,QSA]';
                                }
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-redirect_default_urls" stopProcessing="true">';
                                             
                            if( !is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Redirect" url="'.  $rewrite_to .'{R:1}"  appendQueryString="true" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'(.*)"  />';
                                    $rewrite .=   "\n" .    '    <action type="Redirect" url="'.  $rewrite_to .'{R:2}"  appendQueryString="true" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
         
                        }
                        
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            
                            $global_settings    =   $this->wph->functions->get_global_settings ( );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $uploads_path, [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if ( $global_settings['nginx_generate_simple_rewrite']   ==  'yes' )
                                {
                        
                                    $rewrite        =   array();    
                                    $rewrite_list   =   array();
                                    $rewrite_rules  =   array();
                                    
                                    $rewrite_list['blog_id']        =   'network';
                                    $rewrite_list['type']           =   'location';
                                    $rewrite_list['description']    =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) . '(/.*\.php)'; 
                                    
                                    $rewrite_data               =   "rewrite ^".  $rewrite_base ."(.+) ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ redirect;';    
                                    
                                    $rewrite_rules[]            =   $rewrite_data;
                                    $rewrite_list['data']       =   $rewrite_rules; 
                                    
                                    $rewrite[]                  =   $rewrite_list; 
                                 
                                    
                                    $processing_response['rewrite'] = $rewrite;            
                                    return  $processing_response;    
                                }
                            
                            
                        }
                               
                    $processing_response['rewrite'] = $rewrite;            
                                
                    return  $processing_response;     
                    
                    
                }


        }
?>