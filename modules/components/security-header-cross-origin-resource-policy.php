<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_cross_origin_resource_policy extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "Cross-Origin-Resource-Policy (CORP)";
                }
                                    
            function get_module_component_settings()
                {
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'cross_origin_resource_policy',
                                                                    'label'         =>  __('Cross-Origin-Resource-Policy (CORP)',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Cross-Origin-Resource-Policy',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The HTTP Content-Security-Policy response header allows web site administrators to control resources the user agent is allowed to load for a given page. With a few exceptions, policies mostly involve specifying server origins and script endpoints. This helps guard against cross-site scripting attacks (Cross-site_scripting). ",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />"  . __("Options:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>same-site</b> - "  . __("Only requests from the same Site can read the resource.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>same-origin</b> - "  . __("Only requests from the same origin (i.e. scheme + host + port) can read the resource.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>cross-origin</b> - "  . __("Requests from any origin (both same-site and cross-site) can read the resource. This is useful when COEP is used",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Cross-Origin-Resource-Policy'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'custom',
                                                                                                 
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                                    
                                                                    ); 
                  
                                                                    
                    return $this->component_settings; 
                    
                }
                
            function _get_default_options()
                {
                    
                    $options    =   array ( 
                                            'enabled'           =>  'no',
                                            'value'             =>  'same-site'
                                            );
                    return $options;
                }
            
            
            function _init_cross_origin_resource_policy( $saved_field_data )
                {
                    
                }
                
            function _module_option_html( $module_settings )
                {
                    
                    $values =   $this->wph->functions->get_site_module_saved_value( $module_settings['id'],  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    $module_settings =   shortcode_atts ( $this->_get_default_options(), (array)$values )        
                    
                    ?>
                        <div class="row xspacer header">
                            <p><?php _e('Enable Header',    'wp-hide-security-enhancer') ?></p>
                            <fieldset>
                                <label>
                                    <input type="radio" class="setting-value default-value radio" value="no" name="enabled" <?php if ( $module_settings['enabled'] == 'no' ) { ?>checked="checked"<?php } ?>> <span>No</span>
                                </label>
                                <label>
                                    <input type="radio" class="setting-value radio" value="yes" name="enabled" <?php if ( $module_settings['enabled'] == 'yes' ) { ?>checked="checked"<?php } ?>> <span>Yes</span>
                                </label>                                                                
                            </fieldset>
                        </div>
                        
                        <p><?php _e('Header Options',    'wp-hide-security-enhancer') ?></p>
                        <div class="row spacer">
                            <fieldset>
                                <label>
                                    <input type="radio" class="radio" value="same-site" name="value" <?php if ( $module_settings['value'] == 'same-site' ) { ?>checked="checked"<?php } ?>> <span>same-site</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="same-origin" name="value" <?php if ( $module_settings['value'] == 'same-origin' ) { ?>checked="checked"<?php } ?>> <span>same-origin</span>
                                </label>
                                <label>
                                    <input type="radio" class="radio" value="cross-origin" name="value" <?php if ( $module_settings['value'] == 'cross-origin' ) { ?>checked="checked"<?php } ?>> <span>cross-origin</span>
                                </label>
                            </fieldset>
                        </div>
 
                        
                    
                    <?php
                }
                
                
            function _module_option_processing( $field_name )
                {
                    
                    $results            =   array();
                    
                    $module_settings =   shortcode_atts ( $this->_get_default_options(), array() );
                    foreach ( $module_settings   as  $setting_name  =>  $setting_value )
                        {
                            if ( ! isset ( $_POST[ $setting_name ] ) )
                                continue;
                                
                            $value  =   preg_replace( '/[^a-zA-Z0-9-_]/m' , '', $_POST[ $setting_name ] );
                            if ( empty ( $value ) )
                                continue;
                                
                            $module_settings[ $setting_name ]   =   $value;
                        }
                                        
                    $results['value']   =   $module_settings;
                       
                    return $results;
                    
                }
                
                
            function _callback_saved_cross_origin_resource_policy($saved_field_data)
                {
                    
                    if ( empty ( $saved_field_data ) ||  ! is_array ( $saved_field_data ) || ! isset ( $saved_field_data['enabled'] ) || $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite                            =  '';
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    .=  "\n" . '        Header set Cross-Origin-Resource-Policy "' . $saved_field_data['value'] .'"';
                            $processing_response['type']    =   'header';
                        }
                        
                    if ( $this->wph->server_nginx_config   === TRUE )
                        {  
                            $rewrite        =   array();
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $rewrite_list['type']        =   'header';
                            $rewrite_list['description'] =   '';
                            
                            $rewrite_data  =   '        add_header Cross-Origin-Resource-Policy "' . $saved_field_data['value'] . '";';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;    
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                      
                    return  $processing_response;
                    
                } 
            

        }
?>