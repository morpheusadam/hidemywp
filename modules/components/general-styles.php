<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_styles extends WPH_module_component
        {
            function get_component_title()
                {
                    return "Styles";
                }
                                    
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'styles_remove_version',
                                                                    'label'         =>  __('Remove Version',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove version number from enqueued style files.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove Version',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("This provide a method to remove the Style file version number which is being append at the end of every style tag. Generally this is intended to be a plain information upon the style code version, however not being used within any functionality or code run.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br />" . __("Keeping version number for styles provide additional information to hackers which try to identify specific code and version which know as being vulnerable.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/general-html-styles/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower')
                                                                    
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'styles_remove_id_attribute',
                                                                    'label'         =>  __('Remove ID from link tags',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove ID attribute from all link tags which include a stylesheet.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove ID from link tags',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("This provide a method to remove the Style file ID attribute which generally has no usage.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/general-html-styles/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower')
                                                                    
                                                                    );
                                                                    
                    return $this->component_settings;   
                }
                
                
                
            function _init_styles_remove_version($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    if ( defined('WP_ADMIN') &&  ( !defined('DOING_AJAX') ||  ( defined('DOING_AJAX') && DOING_AJAX === FALSE )) && ! apply_filters('wph/components/force_run_on_admin', FALSE, 'styles_remove_version' ) )
                        return FALSE;
                    
                    if ( ! $this->ReInit )    
                        add_filter( 'style_loader_src',         array(&$this, 'remove_file_version'), 999 );
                    
                }
                
            function remove_file_version($src)
                {
                    if( empty($src) )   
                        return $src;
                        
                    $parse_url  =   parse_url( $src );
                    
                    if(empty($parse_url['query']))
                        return $src;
                    
                    parse_str( $parse_url['query'], $query );
                    
                    if(!isset( $query['ver'] ))
                        return $src;
                    
                    unset($query['ver']);    
                    
                    $parse_url['query'] =   http_build_query( $query );
                    if(empty($parse_url['query']))
                        unset( $parse_url['query'] );
                    
                    $url    =   $this->wph->functions->build_parsed_url( $parse_url );
                    
                    return $url;
                    
                }
                
                
            function _init_styles_remove_id_attribute( $saved_field_data )
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    //run only on front syde
                    if( defined('WP_ADMIN') &&  ( !defined('DOING_AJAX') ||  ( defined('DOING_AJAX') && DOING_AJAX === FALSE )) && ! apply_filters('wph/components/force_run_on_admin', FALSE, 'styles_remove_id_attribute' ) )
                        return;
                    
                    if ( ! $this->ReInit )    
                        add_filter( 'wp-hide/ob_start_callback',         array(&$this, 'ob_start_callback_remove_id'));
                    
                }
            
            
            function ob_start_callback_remove_id( $buffer )
                {
                    $buffer               =   preg_replace_callback( '/(<link([^>]+)rel=("|\')stylesheet("|\')([^>]+)?\/?>)/im' ,   array($this, '_process_tags') , $buffer);   
                    $buffer               =   preg_replace_callback( '/(<style[^>]*>)/is' ,         array($this, '_process_tags') , $buffer);   
                    
                    return $buffer;   
                }
                
                
            function _process_tags( $match ) 
                {
                    
                    $tag    =   $match[0];
                    
                    $tag    =   $this->_process_tags_regex( $tag );
                    
                    return $tag;
                    
                }
            
            
            static public function _process_tags_regex( $tag )
                {
                    $regex_ignore   =   '';
                    $ignores = apply_filters( 'wph/module/general_styles/remove_id_attribute/ignore_ids',  array() );
                    if ( ! empty ( $ignores )   &&  is_array ( $ignores )   &&   count ( $ignores ) > 0 )
                        {
                            $regex_ignore   =   '(?!';
                            $regex_ignore   .=   implode ( '|', array_filter ( $ignores ) );                        
                            $regex_ignore   .=   ')';
                        }
                    
                    $regex = '/\s(id=(\'|")' . $regex_ignore  .'[a-zA-Z0-9:;\.\s\(\)\-\,\_]*(\'|"))/i';   
                    
                    $tag = preg_replace( $regex , "", $tag );
                    
                    return $tag;   
                    
                }
 

        }
?>