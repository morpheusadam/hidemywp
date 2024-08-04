<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_document_loaded_assets_postprocessing extends WPH_module_component
        {
            
            function get_component_title()
                {
                    return "Document Loaded Assets PostProcessing";
                }
                                        
            function get_module_component_settings()
                {
                              
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'document_loaded_assets_postprocessing',
                                                                    'label'         =>  __('Document Loaded Assets PostProcessing',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  '' ,
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  '',
                                                                                                'description'               =>  '<span>' . __('This options works with the features ', 'wp-hide-security-enhancer') . '<a href="admin.php?page=wp-hide-postprocessing&component=css-post-processing">' . __('CSS Post-Processing', 'wp-hide-security-enhancer') . '</a> ' . __('and/or', 'wp-hide-security-enhancer') . ' <a href="admin.php?page=wp-hide-postprocessing&component=javascript-post-processing">' . __('JavaScript Post-Processing', 'wp-hide-security-enhancer') . '</a>' . __(' are active.', 'wp-hide-security-enhancer') . '</span>' .
                                                                                                                                    '<br />' .  __('When the PostProcessing function is active, the plugin core applies the Replacements for all page elements including assets, before the HTML content is sent back to the browser. Rarely, some JavaScripts load additional assets, once the DOM document has loaded. In such a case, a Replacement filter is not possible as the browser loads the content directly through the file link. ',  'wp-hide-security-enhancer') .
                                                                                                                                    '<br /><br />' . '<span>' . __('Through this component, such asset URLs ( JavaScript and StyleSheet files ) can be provided to instruct the core to rewrite the link through a post-processor and apply the Replacements.', 'wp-hide-security-enhancer') . '</span>' .
                                                                                                                                    '<br /><br />' . '<span>' . __('Provide the URLs, one per line, as they show within the browser console ( network ). On Save, the plugin attempts to reverse the URLs to the default format. ', 'wp-hide-security-enhancer') . '</span>' ,
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/how-postprocessing-works-with-assets-loaded-outside-of-page-html-dataset/'
                                                                                                ),
                                                                                                
                                                                    'interface_help_split'  =>  FALSE,
                                                                    
                                                                    'input_type'    =>  'textarea',
                                                                    'default_value' =>  '',
                                                                                                                                        
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                                    'processing_order'  =>  10
                                                                    );
                                                                           
                    return $this->component_settings;  
                     
                }
                   
                
            function _module_option_processing( $field_name )
                {
                    $results            =   array();
                    
                    $process_interface_save_errors  =   array();
                                        
                    $data                       =   $_POST['document_loaded_assets_postprocessing'];
                    $data                       =   trim ( $data ) ;
                    
                    $processed_data             =   array();
                    $processed_data['value']    =   '';
                    
                    $wp_content_slug            =   trim ( $this->wph->default_variables['content_directory'], '\/' );
                    
                    if  ( ! empty ( $data )  )
                        {
                            $data   =   preg_split( "/\r\n|\n|\r/", $data );
                            
                            foreach(    $data   as  $key    =>  $url )
                                {
                                   
                                    $url    =   stripslashes( $url );
                                    $url    =   trim( $url );
                                    $url    =   preg_replace("/[^A-Za-z0-9_.:\-\/*\(\)\?\\\\]/", '', $url);
                                    $url    =   ltrim ( $url, '/' );
                                    
                                    $url_parsed    =   parse_url( preg_quote ( $url ) );
                                    
                                    if ( ! isset ( $url )    ||   empty ( $url ) ||   strpos( $url, $wp_content_slug ) === FALSE )
                                        {
                                            $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('The URL ', 'wp-hide-security-enhancer') . ' <b>' . $url. '</b> '  .  __('couldn\'t be processed as appears invalid.', 'wp-hide-security-enhancer')
                                                                                        );
                                            
                                            unset  ( $data[ $key ] );
                                            
                                            continue;
                                        }
                                        
                                    $pathinfo   =   pathinfo ( $url );
                                        if ( ! isset ( $pathinfo['extension'] )    ||  ! in_array ( strtolower($pathinfo['extension']), array ( 'css', 'js') ) )
                                        {
                                            $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('Invalid file type' , 'wp-hide-security-enhancer') . ' <b>' . $url. '</b> '  .  __('only CSS and JavaScript files can be provides.', 'wp-hide-security-enhancer')
                                                                                        );
                                            
                                            unset  ( $data[ $key ] );
                                            
                                            continue;
                                        }
                                        
                                    $data[ $key ]   =   '/' . ltrim ( $url, '/' );
                                }
                            
                            $processed_data['value'] =   implode( PHP_EOL, $data );
                        }
                                        
                    if  (  count ( $process_interface_save_errors ) > 0 )
                        {
                            $wph_interface_save_errors  =   get_option( 'wph-interface-save-errors');
                            
                            $wph_interface_save_errors  =   array_filter ( array_merge( (array)$wph_interface_save_errors, $process_interface_save_errors) ) ;
                            
                            update_option( 'wph-interface-save-errors', $wph_interface_save_errors );  
                        }
                    
                    return $processed_data;
                    
                }
                
                
            function _callback_saved_document_loaded_assets_postprocessing( $saved_field_data )
                {
                    
                    //check if the field is noe empty
                    if( empty ( $saved_field_data ) )
                        return  FALSE;
                        
                    $processing_response    =   array(); 
                              
                    $rewrite                            =  '';
                    
                    $data   =   preg_split( "/\r\n|\n|\r/", $saved_field_data );
                    if ( count ( $data ) < 1 )
                        return $processing_response;
                        
                    
                    
                    $file_processor =   $this->wph->default_variables['network']['plugins_path'];
                    $file_processor =   trailingslashit( $file_processor ) . 'wp-hide-security-enhancer-pro/router/asset-postprocessing.php';    
                    $rewrite_to     =   $this->wph->functions->get_rewrite_path( $file_processor, [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ] );
                    
                    
                    $wph_rewrite_manual_install =   get_site_option('wph-rewrite-manual-install');
                    if ( $wph_rewrite_manual_install != 'yes' )
                        {
                            $replacement_list       =   $this->wph->functions->get_replacement_list();        
                        }
                        else
                        {
                            $default_replacement_list   =   $this->wph->urls_replacement;
                    
                            $site_settings  =   $this->wph->functions->get_site_modules_settings( 'network' );
                            
                            //generate the replacements for the non-deployed rules. 
                            $this->wph->_modules_components_run( TRUE, $site_settings );
                            
                            $replacement_list       =   $this->wph->functions->get_replacement_list();
                            
                            $this->wph->urls_replacement    =   $default_replacement_list;
                            
                        }
                    
                    $WPH_module_rewrite_map_custom_urls =   new WPH_module_rewrite_map_custom_urls();
                    
                    $home_url           =   home_url();
                    $home_url_parsed    =   parse_url($home_url);
                    $protocol   =   (is_ssl())  ?   'https://' :   'http://';
                    $domain_url         =   $protocol . $home_url_parsed['host'];
                    
                    foreach ( $data as $url )
                        {
                            $rewrited_url    =   $domain_url . $url;
                            //replace the urls
                            $rewrited_url            =   $this->wph->functions->content_urls_replacement( $rewrited_url,  $replacement_list );                            
                            //Custom urls map
                            $rewrited_url           =    $WPH_module_rewrite_map_custom_urls->_do_html_replacements( $rewrited_url );
                            
                            $rewrited_url   =   str_replace ( $domain_url, "", $rewrited_url );
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $rewrited_url, [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                            if($this->wph->server_htaccess_config   === TRUE)           
                                {   
                                    if( ! is_multisite() )
                                        {
                                            $rewrite .= "\nRewriteRule ^("    .   $rewrite_base   .   ')$ '. $rewrite_to .'?action=replacements&file_path=$1 [QSA,END]';
                                        }
                                        else
                                        {
                                            $rewrite .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?("    .   $rewrite_base   .   ')$ '. $rewrite_to .'?action=replacements&file_path=$2 [QSA,END]';    
                                        }    
                                }
                                
                            if($this->wph->server_web_config   === TRUE)
                                {
                      
                                }
                                
                            if($this->wph->server_nginx_config   === TRUE)           
                                {
                                    global $blog_id;
                                    
                                    if ( ! is_array ( $rewrite ) )
                                        $rewrite        =   array();
                                    $rewrite_list   =   array();
                                    $rewrite_rules  =   array();
                                    
                                    $global_settings    =   $this->wph->functions->get_global_settings ( );
                                    
                                    $rewrite_base   =   $this->wph->functions->get_rewrite_path( $rewrited_url, [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                                       
                                    if( ! is_multisite() )
                                        $rewrite_list['blog_id'] =   $blog_id;
                                        else
                                        $rewrite_list['blog_id'] =   'network';
                                        
                                    $rewrite_list['type']        =   'location';
                                    $rewrite_list['description'] =   '~ ^__WPH_SITES_SLUG__/' . untrailingslashit($rewrite_base) ;
                                    
                                    if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                        {
                                            $rewrite_rules[]  =   '         set $wph_remap  "${wph_remap}style_clean__";';
                                        }
                                    
                                    $rewrite_data   =   '';
                                    
                                    if( ! is_multisite() )
                                        $rewrite_data .= "\n         rewrite \"^(". $rewrite_base .")\" ". $rewrite_to . '?action=replacements&file_path=$1 last;';
                                        else
                                        $rewrite_data .= "\n         rewrite \"^(". $rewrite_base .")\" ". $rewrite_to . '?action=replacements&file_path=$2 last;';
                                    
                                    $rewrite_rules[]            =   $rewrite_data;
                                    $rewrite_list['data']       =   $rewrite_rules;
                                    
                                    $rewrite[]  =   $rewrite_list;
                                }
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                
                    return  $processing_response;    
                    
                }
  
        }
?>