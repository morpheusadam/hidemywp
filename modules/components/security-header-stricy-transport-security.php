<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_strict_transport_security extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "Strict-Transport-Security (HSTS)";
                }
                                    
            function get_module_component_settings()
                {
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'strict_transport_security',
                                                                    'label'         =>  __('Strict-Transport-Security (HSTS)',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Strict-Transport-Security (HSTS)',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The HTTP Strict-Transport-Security response header (often abbreviated as HSTS) informs browsers that the site should only be accessed using HTTPS, and that any future attempts to access it using HTTP should automatically be converted to HTTPS.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />"  . __("Options:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>max-age</b> - "  . __("The time, in seconds, that the browser should remember that a site is only to be accessed using HTTPS.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>includeSubDomains</b> - "  . __("If this optional parameter is specified, this rule applies to all of the site's subdomains as well.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>preload</b> - "  . __("See Preloading Strict Transport Security for details. Not part of the specification.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security'
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
                                            'max-age'           =>  '',
                                            'includeSubDomains' =>  '',
                                            'preload'           =>  ''
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
                                    <select name="max-age">
                                        <option value="0" <?php if ( $module_settings['max-age'] == '0' ) { ?>selected="selected"<?php } ?>>0 ( Remove the cached HSTS Policy )</option>
                                        <option value="3600" <?php if ( $module_settings['max-age'] == '3600' ) { ?>selected="selected"<?php } ?>>1 <?php _e('hour',    'wp-hide-security-enhancer') ?></option>
                                        <option value="86400" <?php if ( $module_settings['max-age'] == '86400' ) { ?>selected="selected"<?php } ?>>1 <?php _e('day',    'wp-hide-security-enhancer') ?></option>
                                        <option value="604800" <?php if ( $module_settings['max-age'] == '604800' ) { ?>selected="selected"<?php } ?>>7 <?php _e('days',    'wp-hide-security-enhancer') ?></option>
                                        <option value="2592000" <?php if ( $module_settings['max-age'] == '2592000' ) { ?>selected="selected"<?php } ?>>30 <?php _e('days',    'wp-hide-security-enhancer') ?></option>
                                        <option value="7776000" <?php if ( $module_settings['max-age'] == '7776000' ) { ?>selected="selected"<?php } ?>>90 <?php _e('days',    'wp-hide-security-enhancer') ?></option>
                                        <option value="31536000" <?php if ( $module_settings['max-age'] == '31536000' ) { ?>selected="selected"<?php } ?>>1 <?php _e('year',    'wp-hide-security-enhancer') ?></option>
                                        <option value="63072000" <?php if ( $module_settings['max-age'] == '63072000' ) { ?>selected="selected"<?php } ?>>2 <?php _e('years',    'wp-hide-security-enhancer') ?></option>    
                                    </select> <span>max-age</span>
                                </label>                                                                
                            </fieldset>
                        </div>
                        <div class="row spacer">
                            <fieldset>
                                <label>
                                    <input name="includeSubDomains" type="checkbox" class="setting-value" value="yes" <?php if ( $module_settings['includeSubDomains'] == 'yes' ) { ?>checked="checked"<?php } ?>> <span>includeSubDomains</span>
                                </label>                                                                
                            </fieldset>
                        </div>
                        <div class="row spacer">
                            <fieldset>
                                <label>
                                    <input name="preload" type="checkbox" class="setting-value" value="yes" <?php if ( $module_settings['preload'] == 'yes' ) { ?>checked="checked"<?php } ?>> <span>preload</span>
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
                
                
            function _callback_saved_strict_transport_security( $saved_field_data )
                {
                    
                    if ( empty ( $saved_field_data ) ||  ! is_array ( $saved_field_data ) || ! isset ( $saved_field_data['enabled'] ) || $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                    
                    $saved_field_data   =   shortcode_atts ( $this->_get_default_options(), $saved_field_data );
                        
                    $processing_response    =   array();
                                                         
                    $rewrite_line                            =  '';
                    
                    if ( ! empty ( $saved_field_data['max-age'] ) )
                        $rewrite_line    .=   'max-age=' . intval ( $saved_field_data['max-age'] );
                    if ( $saved_field_data['includeSubDomains'] ==  'yes' )
                        $rewrite_line    .=   '; includeSubDomains';
                    if ( $saved_field_data['preload'] ==  'yes' )
                        $rewrite_line    .=   '; preload';
                    
                    $rewrite_line    =   trim ( $rewrite_line );
                    $rewrite_line    =   rtrim ( $rewrite_line, ';' ); 
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    =  "\n" . '        Header set Strict-Transport-Security "' . $rewrite_line . '"';
                            
                            $processing_response['type']    =   'header';
                        }
                        
                    if( $this->wph->server_nginx_config   === TRUE )
                        {  
                            $rewrite        =   array();
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $rewrite_list['type']        =   'header';
                            $rewrite_list['description'] =   '';
                            
                            $rewrite_data  =   '         add_header Strict-Transport-Security "' . $rewrite_line . '";';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                               
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                      
                    return  $processing_response;
                    
                } 
            

        }
?>