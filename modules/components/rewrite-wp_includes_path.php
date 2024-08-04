<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_new_include_path extends WPH_module_component
        {
                                    
            function get_component_title()
                {
                    return "WP includes";
                }
            
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'new_include_path',
                                                                    'label'         =>  __('New Includes Path',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Change default /wp-includes path.',    'wp-hide-security-enhancer') ,
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('New Includes Path',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("As default a WordPress installation contain a wp-include folder which store files and resources used by WordPress core, themes and plugin. The wp-includes is a common fingerprint, which makes easily to anyone to identify the site as being created on WordPress.",    'wp-hide-security-enhancer') ." <br />  <br />
                                                                                                                                            <code>&lt;script type='text/javascript' src='https://-domain-name-/wp-include/js/jquery/jquery.js'&gt;&lt;/script&gt;</code>
                                                                                                                                            <br /><br /> " . __("After filling in this option e.g. resources the links will change to this:",    'wp-hide-security-enhancer') . " <br />  <br /> 
                                                                                                                                            <code>&lt;script type='text/javascript' src='https://-domain-name-/resources/js/jquery/jquery.js'&gt;&lt;/script&gt;</code>",
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-wp-includes/'
                                                                                                        ),
                                                                    
                                                                    'value_description' =>  __('e.g. my_includes',    'wp-hide-security-enhancer'),
                                                                    'input_type'    =>  'text',
                                                                    
                                                                    'sanitize_type' =>  array(array($this->wph->functions, 'sanitize_file_path_name')),
                                                                    'processing_order'  =>  20
                                                                    );
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'block_wpinclude_url',
                                                                    'label'         =>  __('Block wp-includes URL',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Block /wp-includes/ files from being accesible through default urls. <br />Apply only if <b>New Includes Path</b> is not empty.',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Block wp-includes URL',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("This blocks the default wp-includes urls only for non loged-in users.<br />The functionality apply only if <b>New Includes Path</b> option is filled in.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-wp-includes/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  21
                                                                    );
                                                                    
                    return $this->component_settings;   
                }
                
                
                
            function _init_new_include_path($saved_field_data)
                {
                    if(empty($saved_field_data))
                        return FALSE;
                        
                    $new_include_path   =  $this->wph->functions->prepare_path (    $this->wph->default_variables['url']  . '/' . $saved_field_data    );
                    $this->wph->functions->add_replacement( $this->wph->functions->prepare_path (    site_url()  . '/wp-includes' ), $new_include_path );
                }
                
            function _callback_saved_new_include_path($saved_field_data)
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

                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $saved_field_data, [ 'left_slash'  =>  FALSE, 'right_slash' =>  TRUE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( $this->wph->default_variables['network']['include_path'], [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE ] );
                    
                    if($this->wph->server_htaccess_config   === TRUE)           
                        {
                            
                            if( ! is_multisite() )
                                {
                                    $rewrite .= "\nRewriteRule ^"    .   $rewrite_base   .   '(.+) '. $rewrite_to .'$1 [QSA,END]';
                                }
                                else
                                {
                                    $rewrite .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base   .   '(.+) '. $rewrite_to .'$2 [QSA,END]';    
                                }    
                        }
                        
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-new_include_path" stopProcessing="true">';
                                        
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
                               
                            if( !is_multisite() )
                                $rewrite_list['blog_id'] =   $blog_id;
                                else
                                $rewrite_list['blog_id'] =   'network';
                                
                            $rewrite_list['type']        =   'location';
                            $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                            
                            if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                {
                                    $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}wp_includes__";';
                                }
                            
                            $rewrite_data   =   '';
                            
                            $rewrite_data .= "\n         rewrite \"^". $rewrite_base ."(.+)\" ". $rewrite_to .'$__WPH_REGEX_MATCH_2__ '.  $this->wph->functions->get_nginx_flag_type() .';';
                                 
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                
                    return  $processing_response;   
                }
                      
                
            function _callback_saved_block_wpinclude_url($saved_field_data)
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
                    $new_path       =   $this->wph->functions->get_site_module_saved_value('new_include_path',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    if (empty(  $new_path ))
                        return FALSE;
                                        
                    $rewrite                            =  '';
                    
                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-includes', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( 'index.php?wph-throw-404', [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ]  );
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                    
                                
                            if( ! is_multisite() )
                                {
                                    $rewrite   .=  "\nRewriteRule ^". $rewrite_base ."(.+) ".  $rewrite_to ." [END]";
                                }
                                else
                                {
                                    $rewrite   .=  "\nRewriteRule ^([_0-9a-zA-Z-]+/)?". $rewrite_base ."(.+) ".  $rewrite_to ." [END]";
                                }
                            
                        }
                    
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-block_wpinclude_url" stopProcessing="true">';
                                       
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
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( 'wp-includes', [ 'left_slash'  =>  TRUE, 'right_slash' =>  TRUE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
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
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            $rewrite[]                  =   $rewrite_list;
                            
                            
                            
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            if( ! is_multisite() )
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