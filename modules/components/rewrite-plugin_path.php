<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_new_plugin_path extends WPH_module_component
        {
            
            function get_component_title()
                {
                    return "Plugins";
                }
                                    
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                        'id'            =>  'new_plugin_path',
                                                                        'label'         =>  __('New Plugins Path',    'wp-hide-security-enhancer'),
                                                                        'description'   =>  __('The default plugins path is set to',    'wp-hide-security-enhancer') . ' <strong>'. $this->wph->default_variables['plugins_directory']  .'</strong>',
                                                                        
                                                                        'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New Plugins Path',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("Use any alphanumeric symbols for this field which will be used as the new slug for the plugins folder. Presuming the `apps` slug is being used, all plugins urls become to something like this:",    'wp-hide-security-enhancer') . "<br />  <br />
                                                                                                                                            <code>http://-domain-name-/apps/jetpack/</code>",
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-plugins/'
                                                                                                        ),
                                                               
                                                                        'value_description' =>  __('e.g. my_plugins',    'wp-hide-security-enhancer'),
                                                                        'input_type'    =>  'text',
                                                                        
                                                                        'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name')),
                                                                        'processing_order'  =>  17
                                                                        );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                        'id'            =>  'block_plugins_url',
                                                                        'label'         =>  __('Block plugins URL',    'wp-hide-security-enhancer'),
                                                                        'description'   =>  __('Block default /wp-content/plugins/ files from being accesible through default urls.',    'wp-hide-security-enhancer'),
                                                                        
                                                                        'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block plugins URL',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This blocks the default wp-content/plugins/ url.<br />The functionality apply only if <b>New Plugins Path</b> option is filled in.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-plugins/'
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
                    
                    
                    $this->component_settings[]                  =   array(
                                                                        'type'            =>  'split'
                                                                        
                                                                        );
                    
                    $all_plugins = $this->wph->functions->get_plugins();
                    
                                      
                    if(is_multisite())
                        {
                            
                            if ( !function_exists( 'get_plugins' ) )
                                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                            
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                                   
                            $plugins    =   array();
                                
                            if(count($all_plugins)  >   0)
                                {
                                    foreach($all_plugins    as  $plugin_path    =>  $plugin_data)
                                        {
                                            if( ( is_plugin_active_for_network( $plugin_path ) &&  ! is_network_only_plugin( $plugin_path ) &&  ! in_array( $plugin_path, $plugins ) ) || is_plugin_active( $plugin_path ) )
                                                $plugins[]  =   $plugin_path;
                                        }
                                }    
                            
                        }
                        else
                        {
                            $plugins = (array) get_option( 'active_plugins', array() );
                            sort( $plugins );
                        }
                        
                    foreach($plugins as  $active_plugin)
                        {
                            //exclude this plugins
                            if( in_array($active_plugin, array('wp-hide-security-enhancer/wp-hide.php', 'wp-hide-security-enhancer-pro/wp-hide.php')) )
                                continue; 
                            
                            $plugin_slug    =   sanitize_title($active_plugin);
                            
                            if(!isset($all_plugins[$active_plugin]))
                                continue; 
                                
                            $pluding_data   =   $all_plugins[$active_plugin];
                                                                            
                            $this->component_settings[]                  =   array(
                                                                                'id'            =>  'new_plugin_path_' . $plugin_slug,
                                                                                'label'         =>  __('New Path for',    'wp-hide-security-enhancer') . " <i>" . $pluding_data['Name'] ."</i> ". __('plugin',    'wp-hide-security-enhancer'),
                                                                                'description'   =>  __('This setting if set, overwrites the',    'wp-hide-security-enhancer') . ' ' . __('New Plugin Path',    'wp-hide-security-enhancer') . ' ' . __('value for this plugin.',    'wp-hide-security-enhancer'),
                                                                                
                                                                                'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New Path for',    'wp-hide-security-enhancer') . " <i>" . $pluding_data['Name'] ."</i> ",
                                                                                                        'description'               =>  __( 'Use any alphanumeric symbols for this field which will be used as the new slug for the plugin folder. Presuming the `module_name` slug is being used, this particular plugin urls become to',    'wp-hide-security-enhancer') . ":<br />  <br />
                                                                                                                                            <code>http://-domain-name-/module_name/</code>",
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-plugins/'
                                                                                                        ),
                                                                                
                                                                                
                                                                                'value_description' =>  __( 'e.g. modules/module',    'wp-hide-security-enhancer') . rand( 1,999 ),
                                                                                'input_type'    =>  'text',
                                                                                
                                                                                'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name')),
                                                                                
                                                                                'processing_order'  =>  16
                                                                                );
                                                                        
                        }
                                                                    
                    return $this->component_settings;   
                }
                
                
                
            function _init_new_plugin_path($saved_field_data)
                {
                    
                    //add custom plugins path replacements
                    //get active plugins
                    $active_plugins = (array) get_option( 'active_plugins', array() );
                    
                    if(is_multisite())
                        {
                            $active_sitewide_plugins = get_site_option('active_sitewide_plugins');    
                            
                            if(is_array($active_sitewide_plugins)   &&  count($active_sitewide_plugins) >   0)
                                foreach($active_sitewide_plugins    as  $active_sitewide_plugin =>  $time)
                                    {
                                        $active_plugins[]   =   $active_sitewide_plugin;
                                    }
                                    
                            $active_main_site_plugins = (array) get_option( 'active_plugins', array() );
                            if(is_array( $active_main_site_plugins )   &&  count( $active_main_site_plugins ) >   0)
                                foreach( $active_main_site_plugins    as  $active_main_site_plugin =>  $time)
                                    {
                                        $active_plugins[]   =   $active_main_site_plugin;
                                    }                            
                        }
                    
                    
                    global $blog_id;
                    
                    foreach($active_plugins as  $active_plugin)
                        {
                            //exclude this plugins
                            if( in_array($active_plugin, array('wp-hide-security-enhancer/wp-hide.php', 'wp-hide-security-enhancer-pro/wp-hide.php')) )
                                continue;
                            
                            $active_plugin_split        =   explode('/', $active_plugin);
                            $active_plugin_directory    =   $active_plugin_split[0];
                                     
                            $plugin_slug        =   sanitize_title($active_plugin);
                            $option_namespace   =   'new_plugin_path_' . $plugin_slug;
                                
                            //check if plugin have custom url
                            $plugin_custom_path =   isset ( $this->settings_to_use [ $option_namespace ] ) ? $this->settings_to_use [ $option_namespace ]    :   '' ;
                            if(empty($plugin_custom_path))
                                continue;
                                
                            //add replacement
                            $replace_url            =   $this->wph->functions->prepare_path (    trailingslashit ( WP_PLUGIN_URL )  . trailingslashit ( $active_plugin_directory ) );
                            $replacement_url        =   $this->wph->functions->prepare_path (    trailingslashit ( site_url() ) .  trailingslashit ( $plugin_custom_path  )  );
                            
                            //replace any spaces
                            $replace_url    =   str_replace (" ", "%20", $replace_url );
                            
                            $this->wph->functions->add_replacement( $replace_url, $replacement_url);
                        }
                    
                    
                    if ( empty ( $saved_field_data ) )
                        return FALSE;
                    
                    //add default plugin path replacement
                    $new_plugin_path        =   $this->wph->functions->untrailingslashit_all(    $saved_field_data  );
                    $new_plugin_path        =   $this->wph->functions->prepare_path( site_url()  . '/' . $new_plugin_path );
                    $this->wph->functions->add_replacement( WP_PLUGIN_URL, $new_plugin_path );
                    
                    return TRUE;
                }
        
                
            function _callback_saved_new_plugin_path($saved_field_data)
                {
                    $processing_response    =   array();
                    
                    global $blog_id;
                    
                    if(is_multisite())
                        {
                            $blog_details   =   get_blog_details( $blog_id );
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                        }
                    
                    $global_settings    =   $this->wph->functions->get_global_settings ( );
                    
                    if($this->wph->server_nginx_config   === TRUE)           
                        $rewrite        =   array();
                        else
                        $rewrite        =  '';
                    
                    $plugin_path =   trailingslashit( $this->wph->default_variables['network']['plugins_path'] );
                    
                    $path           =   '';
                    $path           .=  trailingslashit(   $saved_field_data   );
                    
                    //add custom rewrite for plugins
                    //get active plugins
                    if(is_multisite())
                        {
                            
                            if ( !function_exists( 'get_plugins' ) )
                                require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                            
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                                   
                            $active_plugins    =   array();
                            
                            $all_plugins = $this->wph->functions->get_plugins();
                                
                            if(count($all_plugins)  >   0)
                                {
                                    foreach($all_plugins    as  $a_plugin_path    =>  $plugin_data)
                                        {
                                            $active_plugins[]  =   $a_plugin_path;
                                        }
                                }    
                            
                        }
                        else
                        {
                            $active_plugins = (array) get_option( 'active_plugins', array() );
                            sort( $active_plugins );
                        }
                        
                    foreach ( $active_plugins as  $active_plugin )
                        {
                            $active_plugin_split        =   explode('/', $active_plugin);
                            $active_plugin_directory    =   $active_plugin_split[0];
                              
                            $plugin_slug        =   sanitize_title($active_plugin);
                            $option_namespace   =   'new_plugin_path_' . $plugin_slug;  
                                
                            //check if plugin have custom url
                            $plugin_custom_path =   $this->wph->functions->get_site_module_saved_value($option_namespace,  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                            if(empty($plugin_custom_path))
                                continue;
               
                            //$rewrite_base   =   trailingslashit( $plugin_custom_path );
                            //$rewrite_to     =   $this->wph->functions->get_rewrite_to_base( $plugin_path . $active_plugin_directory , TRUE, TRUE, 'full_path' );
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $plugin_custom_path, [ 'left_slash'  =>  FALSE, 'right_slash' =>  TRUE ] );
                            $rewrite_to     =   $this->wph->functions->get_rewrite_path( $plugin_path . $active_plugin_directory, [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE  ] );
                                                     
                            if($this->wph->server_htaccess_config   === TRUE)
                                {
                                    
                                    if(!is_multisite() )
                                        {
                                            $rewrite    .= "\nRewriteRule ^"    .   $rewrite_base   .   '(.+) "'. $rewrite_to .'$1" [END,QSA]';
                                        }
                                        else
                                        {
                                            $rewrite    .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base   .   '(.+) "'. $rewrite_to .'$2" [END,QSA]';
                                        }
                                }
                                
                            if($this->wph->server_web_config   === TRUE)
                                {
                                    $rewrite    .=   "\n" . '<rule name="wph-new_plugin_path-'.  $plugin_slug    .'" stopProcessing="true">';
                                    
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
                                    $rewrite_list   =   array();
                                    $rewrite_rules  =   array();
                                    
                                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $plugin_custom_path, [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                                       
                                    if(!is_multisite() )
                                        $rewrite_list['blog_id'] =   $blog_id;
                                        else
                                        $rewrite_list['blog_id'] =   'network';
                                        
                                    $rewrite_list['type']        =   'location';
                                    $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                                    
                                    if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                        {
                                            $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}wp_plugin__";';
                                        }
                                    
                                    $rewrite_data   =   '';
                                                                        
                                    $rewrite_data .= "\n         rewrite \"^".  $rewrite_base ."(.+)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                                                                            
                                    $rewrite_rules[]            =   $rewrite_data;
                                    $rewrite_list['data']       =   $rewrite_rules;
                                    
                                    $rewrite[]  =   $rewrite_list;
                                }
                            
                        }
                    
                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  FALSE, 'right_slash' =>  TRUE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( $plugin_path , [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE  ] );
                    
                    if( !empty($rewrite_base) &&  !empty($saved_field_data))           
                        {
                            if($this->wph->server_htaccess_config   === TRUE)
                                {
                                    
                                    if(!is_multisite() )
                                        {
                                            $rewrite  .= "\nRewriteRule ^"    .   $rewrite_base   .   '(.+) '. $rewrite_to .'$1 [END,QSA]';
                                        }
                                        else
                                        {
                                            $rewrite  .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base   .   '(.+) '. $rewrite_to .'$2 [END,QSA]';
                                        }
                                    
                                }
                                
                            if($this->wph->server_web_config   === TRUE)
                                {
                                    $rewrite    .=   "\n" . '<rule name="wph-new_plugin_path" stopProcessing="true">';
                                    
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
                                    $rewrite_list   =   array();
                                    $rewrite_rules  =   array();
                                    
                                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                                       
                                    if ( !is_multisite() )
                                        $rewrite_list['blog_id'] =   $blog_id;
                                        else
                                        $rewrite_list['blog_id'] =   'network';
                                        
                                    $rewrite_list['type']        =   'location';
                                    $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                                    
                                    if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                        {
                                            $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}wp_plugins__";';
                                        }
                                    
                                    $rewrite_data   =   '';
                                    
                                    $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."(.+)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                                                                            
                                    $rewrite_rules[]            =   $rewrite_data;
                                    $rewrite_list['data']       =   $rewrite_rules;
                                    
                                    $rewrite[]  =   $rewrite_list;  
                                }
                        }
                    
                    if ( ! empty ( $rewrite ) )    
                        $processing_response['rewrite']    =   $rewrite;
                        else
                        $processing_response    =   FALSE;
                                
                    return  $processing_response;   
                }
                  
                
            function _callback_saved_block_plugins_url($saved_field_data)
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    $processing_response    =   array();
                    
                    global $blog_id;
                    if ( is_multisite() )
                        {
                            $ms_settings    =   $this->wph->functions->get_site_settings('network'); 
                        }    
                                       
                    //prevent from blocking if the wp-include is not modified
                    $new_path     =   $this->wph->functions->untrailingslashit_all ( $this->wph->functions->get_site_module_saved_value( 'new_plugin_path',  $this->wph->functions->get_blog_id_setting_to_use() , 'display') );
                    if (empty(  $new_path ))
                        return FALSE;
                    
                    if(is_multisite())
                        $blog_details = get_blog_details( $blog_id );
                        
                    $rewrite                            =  '';
                          
                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $this->wph->default_variables['network']['plugins_path'], [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                                        
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                    
                            
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
                            $rewrite    =   "\n" . '<rule name="wph-block_plugins_url" stopProcessing="true">';
                            
                            if( !is_multisite() )
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
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $this->wph->default_variables['network']['plugins_path'], [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                               
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
                                {
                                    $rewrite_list['blog_id'] =   $blog_id;
                                    if( is_multisite() )
                                        {
                                            $rewrite_base   =   ltrim($this->wph->functions->string_left_replacement($rewrite_base, ltrim($blog_details->path, '/')));
                                        }
                                }
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