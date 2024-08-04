<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_json_rest extends WPH_module_component
        {
            
            function get_component_title()
                {
                    return "JSON REST";
                }
                                                
            function get_module_component_settings()
                {
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'clean_json_base_route',
                                                                    'label'         =>  __('Clean the REST API response',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('As default, when calling the REST API base route ( e.g. /wp-json/ ) the service outputs all available namespaces and routes.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Clean the REST API response',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("When calling the site REST API base route ( e.g. /wp-json/ or ?rest_route=/ ) the service outputs all available namespaces and routes for current site. This can be a breach for the system, as outputs important information regarding certain used theme and plugins. ",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br />" . __("Recommended selection for this option is Yes, to ensure no inside data is being exposed. ",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-json-rest/'
                                                                                                        ),
                                                        
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  58
                                                                    
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                                'type'            =>  'split'
                                                                                
                                                                                );
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'new_json_path',
                                                                    'label'         =>  __('New JSON Path',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('The default JSON REST path is set to /wp-json.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New JSON Path',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Use any alphanumeric symbols for this field which will be used as the new slug for JSON API. Presuming the `apps-api` slug is being used, the new url becomes to:",    'wp-hide-security-enhancer') . "<br />  <br />
                                                                                                                                            <code>http://-domain-name-/apps-api/</code>",
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-plugins/'
                                                                                                        ),
                                                                    
                                                                    'value_description' =>  __('e.g. api-json',    'wp-hide-security-enhancer'),
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name')),
                                                                    'processing_order'  =>  50
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_json',
                                                                    'label'         =>  __('Block default /wp-json',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block default /wp-json endpoint. This also can be used to block any JSON service version, even if not being re-mapped',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block default /wp-json',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This blocks the JSON REST API service.",    'wp-hide-security-enhancer') . "<br />". 
                                                                                                                                        __("When selecting the <b>Non logged-in</b> the service is blocked for all non-authenticated users.",    'wp-hide-security-enhancer') .  "<br />" .
                                                                                                                                        __("When selecting the <b>All</b> the service is blocked for everyone.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br ><span class='important'>" . __("This might be required by specific plugins, including new WordPress editor Gutenberg. So if required to block the API, the <b>Non logged-in</b> is the appropriate option to use.",    'wp-hide-security-enhancer') . "</span>",
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/rewrite-json-rest/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'            =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'non-logged-in' =>  __('Non logged-in',    'wp-hide-security-enhancer'),
                                                                                                'yes'           =>  __('All',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  45
                                                                    
                                                                    );                                                
                    
                    $this->component_settings[]                  =   array(
                                                                                'type'            =>  'split'
                                                                                
                                                                                );
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_json_rest_v1',
                                                                    'label'         =>  __('Disable JSON REST V1 service',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('An API service for WordPress which is active by default.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable JSON REST V1 service',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("The WordPress REST API is an easy-to-use set of HTTP endpoints which allows access a site data in simple JSON format. That including users, posts, taxonomies and more. Retrieving or updating is as simple as sending a HTTP request.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br />" . __("A REST API can be consumed everywhere. On mobile applications, on front-end (web apps) or any other devices that have access on the net, practically everything can connect from anywhere to your site and interact though JSON REST API service.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br />" . __("V1 was the first development version of API, which currently is deprecated. To disable the usage of it, simply chose Yes.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-json-rest/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  58
                                                                    
                                                                    );
                                                                    
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_json_rest_v2',
                                                                    'label'         =>  __('Disable JSON REST V2 service',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('An API service for WordPress which is active by default.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable JSON REST V2 service',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("The WordPress REST API is an easy-to-use set of HTTP endpoints which allows access a site data in simple JSON format. That including users, posts, taxonomies and more. Retrieving or updating is as simple as sending a HTTP request.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br />" . __("A REST API can be consumed everywhere. On mobile applications, on front-end (web apps) or any other devices that have access on the net, practically everything can connect from anywhere to your site and interact though JSON REST API service.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br />" . __("V2 is the current development version of API, which is included into WordPress as default. To disable the usage of it, simply chose Yes." ,    'wp-hide-security-enhancer') .
                                                                                                                                            "<br ><span class='important'>" . __("This might be required by specific plugins, including new WordPress editor Gutenberg.",    'wp-hide-security-enhancer') . "</span>",
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-json-rest/'
                                                                                                        ),
                                                                    
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  58
                                                                    
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                                'type'            =>  'split'
                                                                                
                                                                                );
 
                                                                    
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_json_rest_wphead_link',
                                                                    'label'         =>  __('Disable output the REST API link tag into page header',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('By default a REST API link tag is being append to HTML.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable output the REST API link tag into page header',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("As default the API url is being append into the front html head tag. Using this option, it will be replaced.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-json-rest/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  58
                                                                    
                                                                    );
                    
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_json_rest_xmlrpc_rsd',
                                                                    'label'         =>  __('Disable JSON REST WP RSD endpoint from XML-RPC responses',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('By default a WP RSD endpoint is being append to the XML respose.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable JSON REST WP RSD endpoint from XML-RPC responses',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Disable any RSD endpoint from a XML-RPC response.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-json-rest/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  58
                                                                    
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_json_rest_template_redirect',
                                                                    'label'         =>  __('Disable Sends a Link header for the REST API',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('On template_redirect, disable Sends a Link header for the REST API.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable Sends a Link header for the REST API',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Disable Sends a Link header for the REST API, on template_redirect",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-json-rest/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  58
                                                                    
                                                                    );
                    
                                                                    
                    return $this->component_settings;   
                }
                
            function _init_clean_json_base_route( $saved_field_data )
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;

                    //ignore when Jetpack App
                    if ( isset ( $_SERVER['HTTP_USER_AGENT'] )  &&  stripos( $_SERVER['HTTP_USER_AGENT'], 'Jetpack' ) )
                        return;
                    
                    if ( ! $this->ReInit )    
                        {
                            add_filter( 'rest_request_after_callbacks', array ( $this, 'rest_request_after_callbacks'), 999, 3 );
                        }
                    
                }
                
            function rest_request_after_callbacks( $response, $handler, $request )
                {
                    
                    if ( $request->get_route() !=  '/' )
                        return $response;
                        
                    if (  isset ( $response->data )    &&  isset ( $response->data['namespaces'] )   &&  is_array ( $response->data['namespaces'] ) )
                        {
                            $response->data['namespaces']   =   array();
                            $response->data['routes']       =   array();
                        }
                    
                    return $response;   
                }    
            
            
            function _init_new_json_path($saved_field_data)
                {
                    if(empty($saved_field_data))
                        return FALSE;
                    
                    //add default plugin path replacement
                    $old_url    =   trailingslashit(    home_url()  )   . 'wp-json';
                    $new_url    =   trailingslashit(    $this->wph->default_variables['home_url']  )   . $saved_field_data;
                    $this->wph->functions->add_replacement( $old_url ,  $new_url );
                    
                    if ( ! $this->ReInit )    
                        {
                            add_filter( 'rest_url_prefix',              array ( $this, 'rest_url_prefix'), 999 );
                        }
                }
                
            function rest_url_prefix( $url_prefix )
                {
                    $url_prefix =   $this->wph->functions->get_site_module_saved_value('new_json_path',  $this->wph->functions->get_blog_id_setting_to_use());    
                    
                    return $url_prefix;
                }
                
            function _callback_saved_new_json_path($saved_field_data)
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
                    
                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE, 'exclude_wp_dir'    =>  TRUE  ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( '/index.php?rest_route=', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'exclude_wp_dir'    =>  TRUE  ] );
                               
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
      
                            
                            if(!is_multisite() )
                                {
                                    $rewrite  .= "\nRewriteRule ^"    .   $rewrite_base  .   '/?$ '. $rewrite_to .'/ [END,QSA]';
                                }
                                else
                                {
                                    $rewrite  .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base  .   '/?$ '. $rewrite_to .'/ [END,QSA]';    
                                }

                            
                            if(!is_multisite() )
                                {
                                    $rewrite  .= "\nRewriteRule ^"    .   $rewrite_base  .   '/(.*)? '. $rewrite_to .'/$1 [END,QSA]';
                                }
                                else
                                {
                                    $rewrite  .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base  .   '/(.*)? '. $rewrite_to .'/$2 [END,QSA]';    
                                }
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-new_json_path1" stopProcessing="true">';
                             
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'/?$"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'/"  appendQueryString="true" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'/?$"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'/"  appendQueryString="true" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
                            
                            $rewrite    =   "\n" . '<rule name="wph-new_json_path2" stopProcessing="true">';
        
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'/(.*)?"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'/{R:1}"  appendQueryString="true" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'/(.*)?"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'/{R:1}"  appendQueryString="true" />';
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
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}json__";';
                                }
                            
                            $rewrite_data   =   '';
       
                            
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base .'/?$" '. $rewrite_to .'/ '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                            
                            
                            
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                               
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                                
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}json__";';
                                }
                            
                            $rewrite_data   =   '';
                                
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base .'/(.*)?" '. $rewrite_to .'/$1 '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                            
                               
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                
                    return  $processing_response;   
                }
            
            
                
            function _init_disable_json_rest_v1($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    if ( ! $this->ReInit )
                        {
                            add_filter('json_enabled', '__return_false');
                            add_filter('json_jsonp_enabled', '__return_false');
                        }
                    
                }
                
                
            function _init_disable_json_rest_v2($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;

                    if ( ! $this->ReInit )
                        {
                            add_filter('rest_authentication_errors', array ( $this, 'rest_authentication_errors' ) );
                            add_filter('rest_jsonp_enabled', '__return_false');
                        }
                    
                }
            
            function rest_authentication_errors( $result )
                {
                    
                    return new WP_Error( 'rest_disabled', 'The service is currently disabled.', array( 'status' => 400 ) );
 
                }
                
                
            function _callback_saved_block_json($saved_field_data)
                {
                    $processing_response    =   array();
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    global $blog_id;
                    
                    $_blog_id   =   $blog_id;
                    if ( is_multisite() )
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network'); 
                            $_blog_id   =   'network';
                        }
                    
                    $rewrite                            =  '';

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-json', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE, 'exclude_wp_dir' =>  TRUE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                    $new_json_path      =   $this->wph->functions->get_site_module_saved_value('new_json_path',  $this->wph->functions->get_blog_id_setting_to_use() ,'display' );
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                                        
                            if ( $saved_field_data == 'non-logged-in' )
                                {
                                    $rewrite  .=   "\nRewriteCond %{HTTP_COOKIE} !^.*wordpress_logged_in.*$ [NC]".
                                                    "\n" .    'RewriteCond %{HTTP_USER_AGENT}  !^Jetpack\ by\ WordPress\.com$ [NC]' .
                                                    "\nRewriteCond %{HTTP:Authorization} ^$ [NC]";
                                }
                                else if ( $saved_field_data == 'yes' )
                                {
                                    $rewrite  .=   "\n" .    'RewriteCond %{HTTP_USER_AGENT}  !^Jetpack\ by\ WordPress\.com$ [NC]' .
                                                    "\nRewriteCond %{HTTP:Authorization} ^$ [NC]";   
                                }
                                            
                            if( ! is_multisite() )
                                {                                    
                                    $rewrite  .=   "\nRewriteRule ^".   $rewrite_base   ."(.*)? ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite  .=   "\nRewriteRule ^([_0-9a-zA-Z-]+/)?".   $rewrite_base   ."(.*)? ".  $rewrite_to ." [END]";
                                }
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_json_rest" stopProcessing="true">';
                            
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'(.*)?"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="false" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^([_0-9a-zA-Z-]+/)?'.  $rewrite_base   .'(.*)?"  />';
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
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-json', [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'exclude_wp_dir' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            if(!is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                                    
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . $rewrite_base . '';
                                                        
                            $rewrite_data   =   '';
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_data  .=    "\n".'         if ( $wph_remap = "" ) {';
                                }
                            
                            if ( $saved_field_data == 'non-logged-in' )
                                {
                                    $rewrite_data  .= "\n" . '  set $no_redirect "1";

                                                             if ( $http_cookie ~* "wordpress_logged_in" ) {
                                                                    set $no_redirect "2";
                                                                }
                                                             if ($http_user_agent ~* "^Jetpack by WordPress\.com$") {
                                                                    set $no_redirect "2";
                                                                }

                                                             if ($no_redirect = "1") { ';
                                    $rewrite_data  .= "\n             rewrite \"^". $rewrite_base ."(.*)\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                                    $rewrite_data  .= "\n}";
                                }
                            else if ( $saved_field_data == 'yes' )
                                {
                                    $rewrite_data  .= "\n" . ' if ( $http_user_agent !~* "^Jetpack\ by\ WordPress\.com$" ) {';
                                    $rewrite_data  .= "\n             rewrite \"^". $rewrite_base ."(.*)\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                                    $rewrite_data  .= "\n}";
                                }
   
                            
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
            
            
            function _init_disable_json_rest_wphead_link($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;

                    if ( ! $this->ReInit )
                        remove_action( 'wp_head', 'rest_output_link_wp_head', 10 );
                    
                }
            
                
            function _init_disable_json_rest_xmlrpc_rsd($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;

                    if ( ! $this->ReInit )
                        remove_action( 'xmlrpc_rsd_apis', 'rest_output_rsd' );
                    
                }
           
           
            function _init_disable_json_rest_template_redirect($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;

                    if ( ! $this->ReInit )
                        remove_action( 'template_redirect', 'rest_output_link_header', 11 );
                    
                }

        }
?>