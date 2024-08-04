<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_slash extends WPH_module_component
        {
            function get_component_title()
                {
                    return "URL Slash";
                }
                                        
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'add_slash',
                                                                    'label'         =>  __('URL\'s add Slash',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Add an end slash to all links which does not include one.',    'wp-hide-security-enhancer'). '<br /> ',

                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('URL\'s add Slash',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("As default the WordPress url's format include an ending slash. ",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br />" . __("There are situations when this slash is not being append. Turning on this option, all links will get a slash if not included as default. Disguise the existence of files and folders, since they will not be slashed as deafault, all receive an ending slashed.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br />" . __("For example the following link:" ,    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><code>https://-domain-name-/map/data</code>
                                                                                                                                            <br />" . __("will be redirected to:",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><code>https://-domain-name-/map/data/</code>
                                                                                                                                            <br /><br />" . __('On certain servers this can produce a small lag measured in milliseconds, for each url.',    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-url-slash/',
                                                                                                        'input_value_extension'     =>  'php'
                                                                                                        ),

                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  3
                                                                    );
                                                                    
                    return $this->component_settings;   
                }
                
            
            function _init_add_slash($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return;
                        
                    //nothing to do at the moment
                }
                
            function _callback_saved_add_slash($saved_field_data)
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
         
                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( '', [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                                    
                    if($this->wph->server_htaccess_config   === TRUE)                             
                        {
                                     
                            if(!is_multisite() )
                                {
                                    $rewrite    .=  "\nRewriteCond %{REQUEST_URI} /+[^\.]+$";
                                    $rewrite    .=  "\nRewriteCond %{REQUEST_METHOD} !POST";
                                    $rewrite    .=  "\nRewriteRule ^" . $rewrite_base . "(.+[^/])$ %{REQUEST_URI}/ [R=301,END]";
                                }
                                else
                                {
                                    $rewrite    .=  "\nRewriteCond %{REQUEST_URI} (/[_0-9a-zA-Z-]+/)?/+[^\.]+$";
                                    $rewrite    .=  "\nRewriteCond %{REQUEST_METHOD} !POST";
                                    $rewrite    .=  "\nRewriteRule ^([_0-9a-zA-Z-]+/)?" . $rewrite_base . "(.+[^/])$ %{REQUEST_URI}/ [R=301,END]";
                                }   
                            
                                                            
                        }
                                                            
                    if($this->wph->server_web_config   === TRUE)
                        {
                            $rewrite    =   "\n" . '<rule name="wph-add_slash" stopProcessing="true">';
                            $rewrite  .=      "\n" .    '   <conditions>';
 
                            $rewrite  .=      "\n" .    '   <add input="{REQUEST_URI}" matchType="Pattern" pattern="/+[^\.]+$"  />';
                            $rewrite  .=      "\n" .    '   </conditions>';
                            
                            if(!is_multisite() )
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^(.+[^/])$" />';
                                    $rewrite .=   "\n" .    '    <action type="Redirect" redirectType="Permanent" url="{R:1}/" />';
                                }
                                else
                                {
                                    $rewrite .=  "\n"  .    '    <match url="^(.+[^/])$" />';
                                    $rewrite .=   "\n" .    '    <action type="Redirect" redirectType="Permanent" url="{R:1}/" />';
                                }
                            
                            $rewrite .=  "\n" . '</rule>';
                  
                        }
                        
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            //Not implemented
                               
                        }
                    
                    $processing_response['rewrite'] = $rewrite;
                                    
                    return  $processing_response;   
                }
                
           
         

        }
?>