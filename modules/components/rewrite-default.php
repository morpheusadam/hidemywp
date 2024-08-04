<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_default extends WPH_module_component
        {
            
            function get_component_id()
                {
                    return '_rewrite_default_';
                    
                }
                                    
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'rewrite_default',
                                                                    'visible'       =>  FALSE,
                                                                    'processing_order'  =>  1
                                                                    );
                                                                    
                    return $this->component_settings;   
                }
                

                
            function _init_rewrite_default (   $saved_field_data   )
                {    
                    if ( ! $this->ReInit )
                        {
                            add_filter ( 'wph/module/general_scripts/remove_id_attribute/ignore_ids', array ( $this, 'ignore_remove_id_attribute_ignore_ids') );
                        }
                    
                    $global_settings    =   $this->wph->functions->get_global_settings();
                    
                    if ( ! $this->ReInit )
                        {
                            if ( $global_settings['covert_relative_urls_to_absolute'] == 'yes' )
                                add_filter ( 'wp-hide/ob_start_callback/pre_replacements', array ( $this, '_ob_start_callback_pre_replacements' ) );
                        }
                    
                    
                    //ensure to revert any urls of the superglobalvariables
                    if ( ! $this->ReInit )
                        add_action( 'wp-hide/modules_components_run/completed', array( $this, '_modules_components_run_completed' ) );
                       
                    $css_combine_code   =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    $js_combine_code    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',   $this->wph->functions->get_blog_id_setting_to_use());
                    
                    //check if the css / js combine is not disabled programatically
                    $enabled_css_combine    =   apply_filters('wph/components/css_combine_code', TRUE );
                    $enabled_js_combine     =   apply_filters('wph/components/js_combine_code', TRUE );
                    
                    if  (  ( ! in_array( $css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) )   &&  ! in_array( $js_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                            ||
                            ( ! $enabled_css_combine    &&  !  $enabled_js_combine )
                    )
                        return;
                    
                    $html_css_js_replacements    =   $this->wph->functions->get_site_module_saved_value('html_css_js_replacements',   $this->wph->functions->get_blog_id_setting_to_use());
                    if  ( ! is_array( $html_css_js_replacements )   ||  count ( $html_css_js_replacements ) < 1 )
                        return;
                    
                                            
                    if( defined('WP_ADMIN') &&  ( !defined('DOING_AJAX') ||  ( defined('DOING_AJAX') && DOING_AJAX === FALSE )) && ! apply_filters('wph/components/force_run_on_admin', FALSE, 'rewrite_default' ) )
                        return;
                    
                    $replacements   =   array();
                    foreach ( $html_css_js_replacements  as  $group )
                        {
                            $replacements[ $group[0] ]  =   '/' . $group[1] . '/';
                        }
                    
                    $this->_do_superglobal_variables_replacements( $replacements );
                    
                    if( $this->wph->functions->is_theme_customize() )
                        return;

                    if ( ! apply_filters('wph/components/_init/', TRUE, 'rewrite_default' ) )
                        return;
                    
                    if ( ! $this->ReInit )      
                        add_filter('wp-hide/ob_start_callback/pre_replacements', array($this, '_do_html_replacements'));
                        
                }
            
                
            function _callback_saved_rewrite_default($saved_field_data)
                {
                    $processing_response    =   array();
                    
                    global $blog_id, $_wph_rewrite_default_run;
                    
                    //run just once
                    /*
                    if ( $_wph_rewrite_default_run  === TRUE )
                        return $processing_response;
                    */
                    
                    add_filter ( 'wp-hide/mod_rewrite_rules', array ( $this,  'mod_rewrite_rules' ), 999, 2 );
                                        
                    $global_settings    =   $this->wph->functions->get_global_settings ( );
                    if ( ! isset( $global_settings['sample_rewrite_hash'] ))
                        {
                            $global_settings['sample_rewrite_hash'] =   md5(    microtime() );
                            $this->wph->functions->update_global_settings( $global_settings );
                        }
                        
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                            $rewrite    =   '';
                            
                            $rewrite    =   'RewriteCond %{ENV:REDIRECT_STATUS} 200' .
                                            "\n" . 'RewriteRule ^ - [L]';
                        }
                    
                    //Add a sample rewrite to be used when Confirm
                    if( $global_settings['nginx_generate_simple_rewrite']   ==  'yes'   &&  $this->wph->server_nginx_config   === TRUE  && $blog_id ==  1)
                        {
                            $rewrite    =   array();
                                
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $global_settings['sample_rewrite_hash'] . '/rewrite_test' , [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            $rewrite_to     =   $this->wph->functions->get_rewrite_path( trailingslashit($this->wph->default_variables['plugins_directory']) . 'wp-hide-security-enhancer-pro/include/rewrite-confirm.php' , [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            $rewrite_list   =   array();
                                               
                            $rewrite_list['blog_id'] =   1;
                                
                            $rewrite_list['type']        =   'default_variables';
                            $rewrite_list['description'] =   "\n         rewrite \"^". $rewrite_base ."\" ". $rewrite_to .' '.  $this->wph->functions->get_nginx_flag_type() .';';
                            
                            $rewrite_data   =   '';
                            $rewrite_rules[]            =   $rewrite_data;                           
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                        }
                    if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes'   &&  $this->wph->server_nginx_config   === TRUE  && $blog_id ==  1)
                        {                                                           
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $global_settings['sample_rewrite_hash'] . '/rewrite_test' , [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            $rewrite_to     =   $this->wph->functions->get_rewrite_path( trailingslashit($this->wph->default_variables['plugins_directory']) . 'wp-hide-security-enhancer-pro/include/rewrite-confirm.php' , [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE, 'type' =>  'nginx' ] );
                            
                            $rewrite_list   =   array();
                                               
                            $rewrite_list['blog_id'] =   1;
                                
                            $rewrite_list['type']        =   'default_variables';
                            $rewrite_list['description'] =   "\n                location ~ ^/" . $rewrite_base  ." {
                                                                     rewrite ^/" . $rewrite_base  ." " . $rewrite_to ." " . $this->wph->functions->get_nginx_flag_type() . ";
                                                                   }";
                            
                            $rewrite_data   =   '';
                            $rewrite_rules[]            =   $rewrite_data;                           
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                        }
                    
                    
                    if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes'   &&  $this->wph->server_nginx_config   === TRUE)           
                        {
                            if ( is_multisite() )
                                $ms_settings    =   $this->wph->functions->get_site_settings('network');
                                
                            //add any map rules
                            if ( is_multisite() &&  SUBDOMAIN_INSTALL   === FALSE )
                                {
                                    $network_sites  =   $this->wph->functions->ms_get_plugin_active_blogs();
                                    $sites_subdomain_slug  =   array();

                                    $section_block  =   $this->_build_exclude_map( 'network' );
                                     
                                    $regex_exclude    =   "";    
                                    if ( count($section_block) > 0 )
                                        {
                                            $regex_exclude  =   '(?!\/' . implode('|\/', $section_block) . ')';
                                        }

                                    $rewrite  =   array_merge($rewrite, $this->_get_roule_map ( 'network', $regex_exclude ));
                                }
                            
                            
                            
                            //add default variables
                            $rewrite_list   =   array();
                               
                            $rewrite_list['blog_id'] =   $blog_id;
                                
                            $rewrite_list['type']        =   'default_variables';
                            $rewrite_list['description'] =   '      set $wph_remap_url "";';
                            
                            $rewrite_data   =   '';
                            $rewrite_rules[]            =   $rewrite_data;                           
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                            
                            if ( is_multisite() &&  SUBDOMAIN_INSTALL   === FALSE )
                                {
                                    $rewrite_list   =   array();
                                       
                                    $rewrite_list['blog_id'] =   $blog_id;
                                        
                                    $rewrite_list['type']        =   'default_variables';
                                    $rewrite_list['description'] =   '          if (-f $document_root$file_php_path_exists) {'
                                                                    ."\n".'                set $wph_remap 1;'
                                                                    ."\n".'                rewrite ^/__WPH_SITES_SLUG__(/.*\.php) $2 last;'
                                                                    ."\n".'            }'
                                                                    ."\n".'            set $conditional_test ""; if ( -e $document_root$file_path_exists ){ set $conditional_test "${conditional_test}A";}  if ( $file_path_exists != "" ) { set $conditional_test "${conditional_test}B"; }'
                                                                    ."\n".'                if ( $conditional_test = AB ){'
                                                                    ."\n".'                set $wph_remap 2;'
                                                                    ."\n".'                rewrite ^/__WPH_SITES_SLUG__(/wp-(content|admin|includes).*) $2 last;'
                                                                    ."\n".'            }';
                                    
                                    $rewrite_data   =   '';
                                    $rewrite_rules[]            =   $rewrite_data;                           
                                    $rewrite_list['data']       =   $rewrite_rules;
                                    
                                    $rewrite[]  =   $rewrite_list;   
                                }
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                    
                    $_wph_rewrite_default_run   =   TRUE;
                                
                    return  $processing_response;   
                }
                
                
                
            private function _build_exclude_map( $blog_id_settings )
                {
                    
                    $section_block  =   array();
                                                    
                    //check wp-includes
                    $site_wp_includes           =   $this->wph->functions->get_site_module_saved_value('new_include_path',              $blog_id_settings, 'display');
                    $site_wp_includes_block     =   $this->wph->functions->get_site_module_saved_value('block_wpinclude_url',           $blog_id_settings, 'display');
                    if ( ! empty ( $site_wp_includes ) &&   $site_wp_includes_block ==  'yes' )
                        $section_block[]    =   'wp-includes';
                        
                    //check wp-content                                                                                                  
                    $site_wp_content            =   $this->wph->functions->get_site_module_saved_value('new_content_path',              $blog_id_settings, 'display');
                    $site_wp_content_block      =   $this->wph->functions->get_site_module_saved_value('block_wp_content_path',         $blog_id_settings, 'display');
                    if ( ! empty ( $site_wp_content ) &&   $site_wp_content_block ==  'yes' )
                        $section_block[]    =   'wp-content';
                        
                    //check plugins block
                    $site_plugins            =   $this->wph->functions->get_site_module_saved_value('new_plugin_path',                  $blog_id_settings, 'display');
                    $site_plugins_block      =   $this->wph->functions->get_site_module_saved_value('block_plugins_url',                $blog_id_settings, 'display');
                    if ( ! empty ( $site_plugins ) &&   $site_plugins_block ==  'yes' )
                        $section_block[]    =   'wp-content/plugins';
                        
                    //check uploads block
                    $site_option                =   $this->wph->functions->get_site_module_saved_value('new_upload_path',               $blog_id_settings, 'display');
                    $site_option_block          =   $this->wph->functions->get_site_module_saved_value('block_upload_url',              $blog_id_settings, 'display');
                    if ( ! empty ( $site_option ) &&   $site_option_block ==  'yes' )
                        $section_block[]    =   'wp-content/uploads';
                        
                    $site_option                =   $this->wph->functions->get_site_module_saved_value('new_wp_comments_post',          $blog_id_settings, 'display');
                    $site_option_block          =   $this->wph->functions->get_site_module_saved_value('block_wp_comments_post_url',    $blog_id_settings, 'display');
                    if ( ! empty ( $site_option ) &&   $site_option_block ==  'yes' )
                        $section_block[]    =   'wp-comments-post.php';
                        
                    $site_option                =   $this->wph->functions->get_site_module_saved_value('new_xml_rpc_path',              $blog_id_settings, 'display');
                    $site_option_block          =   $this->wph->functions->get_site_module_saved_value('block_xml_rpc',                 $blog_id_settings, 'display');
                    if ( ! empty ( $site_option ) &&   $site_option_block ==  'yes' )
                        $section_block[]    =   'xmlrpc.php';
                        
                    $site_option_block          =   $this->wph->functions->get_site_module_saved_value('block_wp_activate_php',         $blog_id_settings, 'display');
                    if ( $site_option_block ==  'yes' )
                        $section_block[]    =   'wp-activate.php';
                    
                    $site_option_block          =   $this->wph->functions->get_site_module_saved_value('block_wp_cron_php',             $blog_id_settings, 'display');
                    if ( $site_option_block ==  'yes' )
                        $section_block[]    =   'wp-cron.php';
                        
                    $site_option_block          =   $this->wph->functions->get_site_module_saved_value('block_default_wp_signup_php',   $blog_id_settings, 'display');
                    if ( $site_option_block ==  'yes' )
                        $section_block[]    =   'wp-signup.php';
                        
                    $site_option_block          =   $this->wph->functions->get_site_module_saved_value('block_default_wp_register_php', $blog_id_settings, 'display');
                    if ( $site_option_block ==  'yes' )
                        $section_block[]    =   'wp-register.php';             
                    
                    $site_option                =   $this->wph->functions->get_site_module_saved_value('new_wp_login_php',              $blog_id_settings, 'display');
                    $site_option_block          =   $this->wph->functions->get_site_module_saved_value('block_default_wp_login_php',    $blog_id_settings, 'display');
                    if ( ! empty ( $site_option ) &&   $site_option_block ==  'yes' )
                        $section_block[]    =   'wp-login.php';                                            
                        
                    //check wp-admin
                    $site_admin_url            =   $this->wph->functions->get_site_module_saved_value('admin_url',                      $blog_id_settings, 'display');
                    $site_admin_url_block      =   $this->wph->functions->get_site_module_saved_value('block_default_admin_url',        $blog_id_settings, 'display');
                    if ( ! empty ( $site_admin_url ) &&   $site_admin_url_block ==  'yes' )
                        $section_block[]    =   'wp-admin';
                    
                    
                    $section_block  =   apply_filters('wp-hide/components/rewrite-default/section_block', $section_block); 
                    
                    return $section_block;
                    
                }
                
        
            private function _get_roule_map ( $blog_id_settings, $regex_exclude )
                {
                    $rewrite    =   array();
                    
                    $rewrite_list                   =   array();
                                                                                   
                    $rewrite_list['blog_id']        =   $blog_id_settings;
                    $rewrite_list['type']           =   'map';
                    $rewrite_list['description']    =   '      $request_uri $file_php_path_exists';
                    
                    $rewrite_rules                  =   array();
                    $rewrite_rules[]                =   '           default     "";';
                    $rewrite_rules[]                =   "\n" . '           ~^__WPH_SITES_SLUG__(?<file_path>('.  $regex_exclude  .'(\/wp-(content|admin|includes))?\/.*\.php))   $file_path;';
                    $rewrite_list['data']           =   $rewrite_rules;
                    
                    $rewrite[]  =   $rewrite_list;
                    
                    
                    
                    $rewrite_list                   =   array();
       
                    $rewrite_list['blog_id']        =   $blog_id_settings;
                    $rewrite_list['type']           =   'map';
                    $rewrite_list['description']    =   '      $request_uri $file_path_exists';
                    
                    $rewrite_rules                  =   array();
                    $rewrite_rules[]                =   '           default     "";';
                    $rewrite_rules[]                =   "\n" . '           ~.*\.php(*SKIP)(*FAIL)|^__WPH_SITES_SLUG__(?<file_path>('.  $regex_exclude  .'(\/wp-(content|admin|includes))?([^?\s]*)))     $file_path;';
                    $rewrite_list['data']           =   $rewrite_rules;
                    
                    $rewrite[]  =   $rewrite_list;
                    
                    return $rewrite;                
                }
            
            
            function _do_html_replacements( $buffer )
                {
                    if ( ! apply_filters('wph/components/rewrite-default/html_replacements', TRUE ) )
                        return $buffer;
                        
                    $html_css_js_replacements    =   $this->wph->functions->get_site_module_saved_value('html_css_js_replacements',   $this->wph->functions->get_blog_id_setting_to_use());
                    
                    $buffer =   $this->wph->regex_processor->do_replacements( $buffer, $html_css_js_replacements, 'html' );
   
                    return $buffer;
                    
                }
                
                
            /**
            * re-Map the replacements to GET/POST/REQUET
            *     
            */
            function _do_superglobal_variables_replacements( $replacements )
                {
                                     
                    if ( count ( $_GET ) >  0   )
                        {
                            foreach  ( $_GET            as  $key    =>  $value)
                                {
                                    if  ( is_array($value) )
                                        {
                                            $_GET[ $key ]  =   $this->_array_replacements_recursivelly( $_GET[ $key ], $replacements, 'GET' );
                                                                        
                                            $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                            if  ( $_key !=  $key )
                                                $_GET[ $_key ]  =   $_GET[ $key ];
                                                
                                            continue;
                                        }
                                        
                                    if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, 'GET' ) )
                                        continue;
                                    
                                    $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                    
                                    //preserve the type
                                    $value_type =   gettype( $value );
                                    $_value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    settype( $_value, $value_type );
                                    
                                    if  ( $_key !=  $key    ||  $_value !=  $value )
                                        $_GET[ $_key ]  =   $_value;
                                }
                        }
                        
                    if ( count ( $_POST ) >  0   )
                        {
                            foreach  ( $_POST            as  $key    =>  $value)
                                {
                                    if  ( is_array($value) )
                                        {
                                            $_POST[ $key ]  =   $this->_array_replacements_recursivelly( $_POST[ $key ], $replacements, 'POST' );
                                                                        
                                            $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                            if  ( $_key !=  $key )
                                                $_POST[ $_key ]  =   $_POST[ $key ];
                                                
                                            continue;
                                        }
                                    
                                    if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, 'POST' ) )
                                        continue;
                                        
                                    $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                    
                                    //preserve the type
                                    $value_type =   gettype( $value );
                                    $_value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    settype( $_value, $value_type );
                                    
                                    if  ( $_key !=  $key    ||  $_value !=  $value )
                                        $_POST[ $_key ]  =   $_value;
                                }
                        }
                        
                    if ( count ( $_REQUEST ) >  0   )
                        {
                            foreach  ( $_REQUEST            as  $key    =>  $value)
                                {
                                    if  ( is_array($value) )
                                        {
                                            $_REQUEST[ $key ]  =   $this->_array_replacements_recursivelly( $_REQUEST[ $key ], $replacements, 'REQUEST' );
                                                                        
                                            $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                            if  ( $_key !=  $key )
                                                $_REQUEST[ $_key ]  =   $_REQUEST[ $key ];
                                                
                                            continue;
                                        }
                                    
                                    if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, 'REQUEST' ) )
                                        continue;
                                        
                                    $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                    
                                    //preserve the type
                                    $value_type =   gettype( $value );
                                    $_value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    settype( $_value, $value_type );
                                    
                                    if  ( $_key !=  $key    ||  $_value !=  $value )
                                        $_REQUEST[ $_key ]  =   $_value;
                                }
                        }
                        
                    if ( count ( $_FILES ) >  0   )
                        {
                            foreach  ( $_FILES            as  $key    =>  $value)
                                {
                                    if  ( is_array($value) )
                                        {
                                            $_FILES[ $key ]  =   $this->_array_replacements_recursivelly( $_FILES[ $key ], $replacements, 'FILES' );
                                                                        
                                            $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                            if  ( $_key !=  $key )
                                                $_FILES[ $_key ]  =   $_FILES[ $key ];
                                                
                                            continue;
                                        }
                                    
                                    if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, 'FILES' ) )
                                        continue;
                                        
                                    $_key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                                    
                                    //preserve the type
                                    $value_type =   gettype( $value );
                                    $_value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    settype( $_value, $value_type );
                                    
                                    if  ( $_key !=  $key    ||  $_value !=  $value )
                                        $_FILES[ $_key ]  =   $_value;
                                }
                        }   
                    
                    
                }
                
                
            
            function _modules_components_run_completed()
                {
                    
                    $replacement_list   =   $this->wph->functions->get_replacement_list();
                    foreach ( $replacement_list as $key =>  $value )
                        {

                            if ( strpos( $key, '/' )    !== 0   &&  strpos( $key, 'http' )  !== 0 ) 
                                {
                                    unset ( $replacement_list[ $key ] );
                                    $key    =   '/' . $key;
                                    $value  =   '/' . ltrim ( $value , '/' ); 
                                    $replacement_list[ $key ]   =   '/' . preg_quote ( $value, '/' ) . '/';        
                                    continue;   
                                }

                            $replacement_list[ $key ]   =   '/' . preg_quote ( $value, '/' ) . '/';
                        }
                        
                    $this->_do_superglobal_variables_replacements( $replacement_list );   
                    
                }
                
                
            function _array_replacements_recursivelly ( $array, $replacements, $superglobal_type ) 
                {
                    if ( !is_array( $array ) ) 
                        return $array;
                    
                    $helper = array();
                    
                    foreach ($array as $key => $value) 
                        {
                            if  (  ! apply_filters('wph/components/rewrite-default/superglobal_variables_replacements', TRUE, $key, $superglobal_type ) )
                                continue;
                            
                            $key       =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $key );
                            
                            if ( is_array( $value ) )
                                $value  =   $this->_array_replacements_recursivelly( $value, $replacements, $superglobal_type );
                                else 
                                {
                                    //preserve the type
                                    $value_type =   gettype( $value );
                                    $value     =   preg_replace( array_values ( $replacements ) , array_keys( $replacements ), $value );
                                    settype( $value, $value_type );
                                }
                            
                            $helper[ $key ] = $value;
                        }
                    
                    return $helper;
                }  
            
            
            
            function _ob_start_callback_pre_replacements( $buffer )
                {
                    $wp_content_slug            =   trim ( $this->wph->default_variables['content_directory'], '\/' );
                    
                    $buffer =   str_ireplace( ' /' . $wp_content_slug . '/', ' ' . trailingslashit ( home_url() ) . $wp_content_slug . '/', $buffer );
                    
                    if ( is_multisite())
                        {
                            global $current_site, $current_blog;
                            
                            if ( $current_site->domain  ==  $current_blog->domain   &&  ! empty ( $current_blog->path )   &&    $current_blog->path !=  '/' )
                                $buffer =   str_ireplace( ' ' . $current_blog->path . $wp_content_slug . '/', ' ' . trailingslashit ( home_url() ) . $wp_content_slug . '/', $buffer );
                        }
                    
                    return $buffer;
                }
                
                
            function ignore_remove_id_attribute_ignore_ids( $ignore )
                {
                    $ignore[]   =   'tmpl-';
                    
                    return $ignore;
                }
                
                
            function mod_rewrite_rules( $readable_rules, $server_type = '' )
                {
                    
                    switch ( $server_type )
                        {
                            case 'nginx'    :   
                                                //process the firewall_conditionals
                                                if ( isset ( $readable_rules['firewall_conditionals'] )     &&  ! empty ( $readable_rules['firewall_conditionals']  ) )
                                                    {
                                                        $data   =   $readable_rules['firewall_conditionals'];
                                                        $readable_rules['firewall_conditionals']    =   '';
                                                        
                                                        $readable_rules['firewall_conditionals']    =   '
# 7G FIREWALL - NGINX v1.6
# @ https://perishablepress.com/7g-firewall-nginx/

set $7g_reason "";
set $7g_drop_bad_bot 0;
set $7g_drop_bad_referer 0;
set $7g_drop_bad_query_string 0;
set $7g_drop_bad_request 0;
set $7g_drop_bad_method 0;
set $7g_drop 0;' ;
                                                        $readable_rules['firewall_conditionals']    .=  $data;
                                                        
                                                        $readable_rules['firewall_conditionals']    .=  '
if ($7g_drop = 1) {

    return 403;

}

if ($7g_drop = 2) {

    return 405;

}';
                                                    }
                                                break;
                        }
                    
                    return $readable_rules;
                    
                } 
                 
        }
?>