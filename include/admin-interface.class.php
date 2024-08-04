<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_interface
        {
            var $screen_slug;
            var $tab_slug;
            
            var $module;
            var $module_settings;
            var $interface_data;
            
            var $wph;
            var $functions;
                   
            function __construct()
                {
                    global $wph;
                    $this->wph          =   &$wph;
                    
                    $this->functions    =   new WPH_functions();
                          
                }
  
  
            function admin_print_styles()
                {
                    wp_enqueue_style( 'tipsy.css', WPH_URL . '/assets/css/tipsy.css');
                       
                    wp_register_style('wph-styles', WPH_URL . '/assets/css/wph.css');
                    wp_enqueue_style( 'wph-styles');
                    
                    wp_register_style('wph-options', WPH_URL . '/assets/css/wph-options.css');
                    wp_enqueue_style( 'wph-options'); 
                
                }
                
                
            function admin_print_scripts()
                {
                    wp_enqueue_script('jquery.tipsy.js', WPH_URL . '/assets/js/jquery.tipsy.js' ); 
                    
                    wp_register_script('wph-options', WPH_URL . '/assets/js/wph.js');
                                        
                    // Localize the script with new data
                    $translation_array = array(
                                            'reset_page_confirmation'   => __('Are you sure to reset the current page settings? All options will be changed to default. An Save is still required for the page.',    'wp-hide-security-enhancer'),
                                            'reset_confirmation'        => __('Are you sure to reset all settings? All options will be removed. Manual removal of rewrite lines is required if no access from PHP.',    'wp-hide-security-enhancer'),
                                            'run_sample_headers'        => __('This creates a sample setup for Headers. That will overwrite any Headers settings previously created through the plugin options. Are you sure?',    'wp-hide-security-enhancer')
                                        );
                    wp_localize_script( 'wph-options', 'wph_vars', $translation_array );
                    
                    wp_enqueue_script( 'wph-options'); 
                
                }
  
  
            function network_admin_print_styles()
                {
                    wp_enqueue_style( 'tipsy.css', WPH_URL . '/assets/css/tipsy.css');
                      
                    wp_register_style('wph-options', WPH_URL . '/assets/css/wph-options.css');
                    wp_enqueue_style( 'wph-options'); 
                
                }
                
                
            function network_admin_print_scripts()
                {
                    
                    wp_enqueue_script('jquery.tipsy.js', WPH_URL . '/assets/js/jquery.tipsy.js' ); 
                    
                    wp_register_script('wph-options', WPH_URL . '/assets/js/wph.js');
                    wp_enqueue_script( 'wph-options');
                  
                }
    
            
            /**
            * Process interface
            * 
            */
            function process_interface_save()
                {
                    
                    
                    $nonce  =   $_POST['wph-interface-nonce'];
                    if ( ! wp_verify_nonce( $nonce, 'wph/interface_fields' ) )
                        return FALSE;
                    
                    //only for admins
                    If ( !  current_user_can ( 'manage_options' ) )
                        return FALSE;
                        
                    $screen_slug  =   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['page'] );
                    if(empty($screen_slug))
                        return FALSE;
                    
                    //network processing
                    if (  is_multisite() &&  is_network_admin() )
                        {
                            $this->process_interface_network( $screen_slug );
      
                        }
                        else
                        {
                            $this->process_interface( $screen_slug );   
                        }
                    
                }
                
                
            function process_interface_licence_save()
                {

                    $nonce  =   $_POST['wph_license_nonce'];
                    if ( ! wp_verify_nonce( $nonce, 'wph_licence' ) )
                        return FALSE;
                    
                    //only for admins
                    If ( !  current_user_can ( 'manage_options' ) )
                        return FALSE;
                        
                    $screen_slug  =   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['page'] );
                    if(empty($screen_slug))
                        return FALSE;
                    
                    //network processing
                    if (  is_multisite() &&  is_network_admin() )
                        {
                            //$this->_process_licence( );
      
                        }
                        else
                        {
                            //$this->_process_licence( );   
                        }
                    
                    $this->interface_save_redirect( $screen_slug, '', 'settings_updated=true');
                }
            
            
            /**
            * Process the interface for a module
            * 
            * @param mixed $screen_slug
            */
            function process_module_interface( $screen_slug )
                {
                    
                    $tab_slug     =   isset($_GET['component'])   ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['component'] )  :   FALSE;
                        
                    $module =   $this->functions->get_module_by_slug($screen_slug);
                    if ( ! is_object ( $module ) )
                        return FALSE;
                                        
                    //if no tag slug check if module use tabs and use the very first one
                    if(empty($tab_slug)   &&  $module->use_tabs  === TRUE)
                        {
                            //get the first component
                            foreach($module->components   as  $module_component)
                                {
                                    if( ! $module_component->title)
                                        continue;
                                    
                                    $tab_slug =   $module_component->id;
                                    break;
                                }  
                            
                        }

                        
                    //proces the fields
                    $module_settings    =   $this->functions->filter_settings(   $module->get_module_components_settings($tab_slug)    );
                    
                    global $blog_id;
                    
                    $blog_id_settings   =   $this->functions->get_blog_id();
                    
                    $_settings_         =   $this->functions->get_site_modules_settings( $blog_id_settings );
                    
                    //clean up all values if $tab_slug is theme, to prevent deleted themes to still held values which oterwise can't be used anymore
                    if  ( $tab_slug == 'theme' )
                        {
                            $reset_fileds   =   array(
                                                        'new_theme_path',
                                                        'new_style_file_path'
                                                        );
                            foreach($reset_fileds as $reset_filed )
                                {
                                    foreach  ( $_settings_ as   $key    =>  $setting ) 
                                        {
                                            if  ( strpos ( $key, $reset_filed ) !== FALSE )
                                                $_settings_[ $key ] =   '';
                                        }
                                    
                                }  
                            
                        }
                    
                    $unique_require_updated_settings    =   array();
                    
                    $processed_fields   =   array();
                                        
                    foreach ( $module_settings as $module_setting )
                        {
                            if(isset($module_setting['type'])   &&  $module_setting['type'] ==  'split')
                                continue;
                            
                            $field_name =   $module_setting['id'];
                            
                            $processed_fields[] =   $field_name;
                            
                            if ( isset($module_setting['module_option_processing'])    &&  is_callable($module_setting['module_option_processing']))
                                {
                                    $results    =   call_user_func($module_setting['module_option_processing'], $module_setting);
                                    
                                    $value  =   $results['value'];
                                }
                                else
                                {
                                    $_POST_field_name   =   str_replace( ".", "_", $field_name );
                                    
                                    if ( $module_setting['input_type']  ==  'textarea' )
                                        $value      =   isset($_POST[$_POST_field_name])  ?   sanitize_textarea_field($_POST[$_POST_field_name]) :   '';
                                        else if ( $module_setting['input_type']  ==  'checkbox' ) 
                                        {
                                            $value      =   '';
                                            if ( isset($_POST[$_POST_field_name]) )
                                                {
                                                    $value  =   $_POST[$_POST_field_name];
                                                    foreach ( $value    as  $key    =>  $value_item ) 
                                                        $value[ $key ]  =   sanitize_textarea_field( $value_item );
                                                }
                                        }
                                        else if ( $module_setting['input_type']  ==  'HTML' ) 
                                        {
                                            $value      =   '';
                                        }
                                        else
                                        $value      =   isset($_POST[$_POST_field_name])  ?   sanitize_text_field($_POST[$_POST_field_name]) :   '';
                                    
                                    //if empty use the default
                                    if(empty($value))
                                        $value  =   $module_setting['default_value'];
                                             
                                    //sanitize value
                                    foreach($module_setting['sanitize_type']    as  $sanitize)
                                        {
                                            $callback_data  =   array();
                                            $callback_data[]    =   $value;
                                            if ( isset($sanitize[2])    &&  is_array( $sanitize[2] ) )
                                                {
                                                    foreach($sanitize[2]    as   $param_key =>  $param_value)
                                                        $callback_data[$param_key]  =   $param_value;
                                                        
                                                    unset( $sanitize[2] );
                                                }
                                            
                                            $value  =   call_user_func_array( $sanitize, $callback_data );   
                                        }
                                }
                                
                            //held the value
                            if ($module_setting['input_type']   ==  'text'  &&  !empty( $value ))
                                {
                                    //if require unique, save for postprocessing
                                    $unique_require_updated_settings[ $field_name ]  =   array(
                                                                                                'module_name'   =>  $module_setting['label'],
                                                                                                'value'         =>  $value
                                                                                                );
                                }
                                else
                                $_settings_[ $field_name ]  =   $value;
                        }
                    
                    $errors                         =   FALSE;
                    $process_interface_save_errors  =   array();
                    
                    //put the new values into a temporary settings variable
                    foreach($unique_require_updated_settings   as  $field_name =>  $data)
                        {
                            $_settings_[ $field_name ]    =   $data['value'];
                        }
                        
                        
                    //ensure the base slug is not being used by another option
                    // e.g.   skin     skin/module
                    $_settings_for_regex    =   array();
                    foreach ( $_settings_   as $field_name =>   $option_value )
                        {
                            if  (  ! is_string( $option_value ) )
                                continue;
                                             
                            $parts  =   explode("/", $option_value);
                            
                            $_settings_for_regex[ $field_name ] =   $parts[0];
                        } 
                    
                    $reserved_values    =   array();
                    
                    if ( $tab_slug != 'cdn' )
                        {    
                            $reserved_values    =   array(
                                                            '[^\w\-]wp[^\w\-]',
                                                            '[^\w\-]admin[^\w\-]',
                                                            '[^\w\-]?admin-ajax\.php[^\w\-]?',
                                                            '^\/ajax\/$',
                                                            '[^\w\-]dashboard[^\w\-]',
                                                            );
                        }
   
                    if ( $tab_slug == 'registration' )
                        {    
                            $reserved_values[]    =   '[^\w\-]activate[^\w\-]';
                        }
                    
                    $reserved_values    =   apply_filters('wp-hide/interface/process/reserved_values', $reserved_values );
                    
                    
                    //clean the just updated fields within main settings array
                    foreach($unique_require_updated_settings   as  $field_name =>  $data)
                        {
                            if( isset($_settings_[ $field_name ]) )
                                $_settings_[ $field_name ]    =   '';
                            
                            //check if the value already exists in other setting
                            if(array_search( $data['value'] , $_settings_)    !== FALSE)
                                {
                                    $errors =   TRUE;
                                    $process_interface_save_errors[]    =   array(
                                                                                    'type'      =>  'error',
                                                                                    'message'   =>  __('Value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . __('already in use for another option.',     'wp-hide-security-enhancer')
                                                                                    );
                                }
                                else
                                {
                                    
                                    //check for base slug e.g. skin/module
                                    $parts  =   explode ( "/" , $data['value'] );
                                    $_settings_to_search    =   $_settings_for_regex;
                                    unset( $_settings_to_search[ $field_name ] );   
                                    
                                    //if plugins tab, ignore the other options which might use the same base slug
                                    if ( $tab_slug  ==  'plugins' )
                                        {
                                            foreach (  $processed_fields    as  $processed_field )
                                                unset( $_settings_to_search[ $processed_field ] ); 
                                                
                                            //also all other new_plugin_path
                                            foreach ( $_settings_to_search as $item =>  $value )
                                                {
                                                    if ( strpos( $item, "new_plugin_path_" ) === 0 )
                                                        unset( $_settings_to_search[ $item ] );
                                                }  
                                        }
                                    
                                    //ensure the login url has a minimum length of 5
                                    if ( $tab_slug  ==  'wp-login-php' )
                                        {
                                            if ( $data['module_name']   ==  'New wp-login.php'     &&  strlen ( $data['value'] ) < apply_filters( 'wp-hide/interface/process/minimum_slug_length', 5, $data ) )
                                                {
                                                    $errors =   TRUE;
                                                    $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('The value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . sprintf ( __('must be a minimum of %s characters or longer.',     'wp-hide-security-enhancer'), apply_filters( 'wp-hide/interface/process/minimum_slug_length', 5, $data ) )
                                                                                            );   
                                                    
                                                }  
                                        }
                                    if ( $tab_slug  ==  'admin-url' )
                                        {
                                            if ( $data['module_name']   ==  'New Admin Url'     &&  strlen ( $data['value'] ) < apply_filters( 'wp-hide/interface/process/minimum_slug_length', 5, $data ) )
                                                {
                                                    $errors =   TRUE;
                                                    $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('The value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . sprintf ( __('must be a minimum of %s characters or longer.',     'wp-hide-security-enhancer'), apply_filters( 'wp-hide/interface/process/minimum_slug_length', 5, $data ) )
                                                                                            );   
                                                    
                                                }  
                                        }
                                        
                                    
                                    //allow the admin ajax to use new admin slug    
                                    if ( $tab_slug  ==  'admin-ajax-php' )
                                        {
                                            unset ( $_settings_to_search[ 'admin_url' ] );
                                        }
                                        
                                    if( array_search( $parts[0] , $_settings_to_search )    !== FALSE )
                                        {
                                            $errors =   TRUE;
                                            $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('Value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . __('use the same base slug ', 'wp-hide-security-enhancer') . '<b>' . $parts[0] . '</b> ' . __('used for another option.',     'wp-hide-security-enhancer')
                                                                                            );
                                        }   
                                    
                                    
                                }
                                
                            //put the value back
                            $_settings_[ $field_name ]    =   $data['value'];
                            
                            //check for reserved value
                            if( preg_match( "/" . implode ( "|", $reserved_values ) ."/i" , '/' . trim ($data['value'], '/') .'/' )    != 0)
                                {
                                    $errors =   TRUE;
                                    $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                    'message'   =>  __('Value', 'wp-hide-security-enhancer') . ' <b>' . $data['value'] .'</b> ' . __('set for', 'wp-hide-security-enhancer') . ' ' . __($data['module_name'],     'wp-hide-security-enhancer') . ' ' . __(' include a system reserved word.',     'wp-hide-security-enhancer')
                                                                                    );
                                }
                            
                        }
                    
                    $errors   =   apply_filters('wp-hide/interface/process', $errors, $_settings_, $module_settings);
                    
                    /*
                    if  ( ! $errors ) 
                        {
                            $wph_interface_save_errors  =   get_option( 'wph-interface-save-errors');   
                            if  ( is_array( $wph_interface_save_errors ) && count ( $wph_interface_save_errors ) > 0 )
                                $errors =   TRUE;
                        }
                    */
                                                                
                    if( $errors === FALSE)
                        {    
                            
                            $this->functions->update_site_modules_settings( $_settings_ , $blog_id_settings);
                                                        
                            //trigger the settings changed action
                            do_action('wph/settings_changed');
                            
                            //check if the rules applyed so r-init modules using the new settings
                            $wph_rewrite_manual_install =   get_site_option('wph-rewrite-manual-install');   
                            if ( empty ( $wph_rewrite_manual_install ) )
                                {
                                    $this->wph->_init_urls_replacements();
                                    //$this->wph->_modules_components_run();   
                                }
                                                            
                        }
                        else
                        {
                            //store the error for display purpose
                            $wph_interface_save_errors  =   array();
                            
                            $wph_interface_save_errors  =   array_filter ( array_merge( (array)$wph_interface_save_errors, $process_interface_save_errors ) );
                            
                            update_option( 'wph-interface-save-errors', $wph_interface_save_errors );
                        }
                        
                    $this->interface_save_redirect( $screen_slug, $tab_slug, 'settings_updated=true');    
                    
                }
                
                
            /**
            * Redirect the user to new location, if apply
            *     
            * @param mixed $screen_slug
            * @param mixed $tab_slug
            */
            function interface_save_redirect( $screen_slug, $tab_slug   =   '', $append_query    =   '' )
                {
                    global $blog_id;
                    
                    $settings   =   $this->functions->get_site_modules_settings_to_apply( $this->functions->get_blog_id_setting_to_use() );
                    
                    $new_admin_slug     =   isset($settings['admin_url'])   ?   $settings['admin_url']  :   '';
                          
                    //redirect
                    if( is_network_admin() )
                        {
                            if(!empty($new_admin_slug)   &&  $this->functions->is_permalink_enabled())
                                $new_location       =   network_site_url( $new_admin_slug . "/network/admin.php?page="   .   $screen_slug );
                                else
                                $new_location       =   network_site_url( "wp-admin/network/admin.php?page="   .   $screen_slug );
                        }
                        else
                        {
                            if(!empty($new_admin_slug)   &&  $this->functions->is_permalink_enabled())
                                $new_location       =   trailingslashit(    site_url()  )   . $new_admin_slug .  "/admin.php?page="   .   $screen_slug;
                                else
                                $new_location       =   trailingslashit(    site_url()  )   .  "wp-admin/admin.php?page="   .   $screen_slug;
                        }
                    
                    if( ! empty($tab_slug ) )
                        $new_location   .=  '&component=' . $tab_slug;
                    
                    if( ! empty ( $append_query ) )
                        $new_location   .=  '&' . $append_query;
                           
                    wp_redirect($new_location);
                    die();   
                    
                }
            
            
            /**
            * Process intherface for single site
            * 
            * @param mixed $screen_slug
            */
            function process_interface( $screen_slug )
                {
                    $global_settings    =   $this->functions->get_global_settings ( );
                       
                    switch ( $screen_slug )
                        {
                            case 'wp-hide-pro'    : 
                                                        if ( ! is_multisite() )
                                                            $global_settings['self_setup']                      =   isset($_POST['self_setup'])   &&   $_POST['self_setup']   ==  'yes'  ?   'yes'  :   'no';
                                                        
                                                        $global_settings['covert_relative_urls_to_absolute']=   isset($_POST['covert_relative_urls_to_absolute'])   &&   $_POST['covert_relative_urls_to_absolute']   ==  'yes'  ?   'yes'  :   'no';
                                                        
                                                        $global_settings['nginx_generate_simple_rewrite']       =   isset($_POST['nginx_generate_simple_rewrite'])   &&   $_POST['nginx_generate_simple_rewrite']   ==  'yes'  ?   'yes'  :   'no';
                                                        $global_settings['nginx_save_rewrite_to_file']          =   isset($_POST['nginx_save_rewrite_to_file'])   &&   $_POST['nginx_save_rewrite_to_file']   ==  'yes'  ?   'yes'  :   'no';
                                                        $global_settings['nginx_headers_save_rewrite_to_file']  =   isset($_POST['nginx_headers_save_rewrite_to_file'])   &&   $_POST['nginx_headers_save_rewrite_to_file']   ==  'yes'  ?   'yes'  :   'no';
                                                        delete_site_option( 'wph-errors-nginx_rewrites_to_file' );
                                                        
                                                        //check for import
                                                        $this->_process_import();
                                                        
                                                        $this->functions->update_global_settings( $global_settings );
                                                        
                                                        $this->interface_save_redirect( $screen_slug, '', 'settings_updated=true');
                                                                                                      
                                                        break;
                            
                            case 'wp-hide-setup'    : 
                                                        //nothing up to this point
                                                        
                                                        wp_redirect( wp_login_url() );
                                                        
                                                        die();
                                                                                                      
                                                        break;
                                                        
                                                        
                            default                 :
                                                        
                                                        $this->process_module_interface( $screen_slug );                                    
                                                        break;
                            
                        }
                       
                }
            
            
            /**
            * Process the superadmin interface
            * 
            */
            function process_interface_network( $screen_slug )
                {
                    
                    switch ( $screen_slug )
                        {
                            case 'network-wp-hide' :
                                                        $global_settings    =   $this->functions->get_global_settings ( );
                                                        
                                                        $global_settings['self_setup']                      =   isset($_POST['self_setup'])   &&   $_POST['self_setup']   ==  'yes'  ?   'yes'  :   'no';
                                                        $global_settings['covert_relative_urls_to_absolute']=   isset($_POST['covert_relative_urls_to_absolute'])   &&   $_POST['covert_relative_urls_to_absolute']   ==  'yes'  ?   'yes'  :   'no';
                                                        
                                                        //check for import
                                                        $this->_process_import();
                                                                                
                                                        $settings           =   $this->functions->get_site_settings ( 'network' );
                                                        $previous_settings  =   $settings;
                                                        
                                                        $global_settings['nginx_generate_simple_rewrite']       =   isset($_POST['nginx_generate_simple_rewrite'])   &&   $_POST['nginx_generate_simple_rewrite']   ==  'yes'  ?   'yes'  :   'no';
                                                        $global_settings['nginx_save_rewrite_to_file']          =   isset($_POST['nginx_save_rewrite_to_file'])   &&   $_POST['nginx_save_rewrite_to_file']   ==  'yes'  ?   'yes'  :   'no';
                                                        $global_settings['nginx_headers_save_rewrite_to_file']  =   isset($_POST['nginx_headers_save_rewrite_to_file'])   &&   $_POST['nginx_headers_save_rewrite_to_file']   ==  'yes'  ?   'yes'  :   'no';
                                                        delete_site_option( 'wph-errors-nginx_rewrites_to_file' );
                                                        
                                                        $this->functions->update_site_settings( $settings, 'network' );
                                                        $this->functions->update_global_settings( $global_settings );
                                       
                                                        $this->interface_save_redirect( $screen_slug, '', 'settings_updated=true' );
                                                        
                                                        break;
                                                        
                            case 'wp-hide-setup' :
                                                        if (isset($_POST['rewrite-update-confirm']) &&  $_POST['rewrite-update-confirm']    ==  'yes' )
                                                            {
                                                                
                                                                /**
                                                                * At this point we presume the rewrite where applied sucesfull
                                                                */
                                                                
                                                                delete_site_option('wph-rewrite-manual-install');
                                                                
                                                                $this->functions->save_all_sites_options_list();
                                                                
                                                                $this->functions->delete_all_sites_option('wph-rewrite-manual-install');
                                                                $this->functions->delete_all_sites_option('wph-errors-rewrite-to-file');
                                                                
                                                                $this->interface_save_redirect( $screen_slug );   
                                                            }
                                                            
                                                        break;
                                                        
                                                        
                            default                     :
                                                            $this->process_module_interface( $screen_slug );
                                                            break;
                                
                        }
                            
                }
            
            
            
            /**
            * Reset components settings
            * 
            */
            function reset_settings()
                {

                    $nonce  =   $_POST['_wpnonce'];
                    if ( ! wp_verify_nonce( $nonce, 'wp-hide-reset-settings' ) )
                        return FALSE;
                        
                    //only for admins
                    If ( !  current_user_can ( 'manage_options' ) )
                        return FALSE;
                    
                    $screen_slug  =   isset($_POST['wph-page'])         ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_POST['wph-page'] )         :   '';
                    $tab_slug     =   isset($_POST['wph-component'])    ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_POST['wph-component'] )    :   '';
                                        
                    $blog_id_settings   =   $this->functions->get_blog_id();
                    
                    //$modules_settings   =   $this->functions->get_site_modules_settings( $blog_id_settings );
                    $modules_settings   =   array();
                    
                    foreach($this->wph->modules   as  $module)
                        {
                            //proces the fields
                            $module_settings    =   $this->functions->filter_settings(   $module->get_module_components_settings(), TRUE    );
                            
                            foreach($module_settings as $module_setting)
                                {
                                    if(isset($module_setting['type'])   &&  $module_setting['type'] ==  'split')
                                        continue;
                                    
                                    $field_name =   $module_setting['id'];
                                    
                                    $value      =   isset($module_setting['default_value'])  ?   $module_setting['default_value'] :   '';
                         
                                    //save the value
                                    $modules_settings[ $field_name ]  =   $value;
                                }   
                            
                        }
                             
                    //update the settings
                    $this->functions->update_site_modules_settings( $modules_settings, $blog_id_settings );
                    
                    //trigger the settings changed action
                    do_action('wph/settings_reset', $blog_id_settings);
                    
                    //no need to confirm, even if Nginx. presume the rewrite where removed from config
                    $this->wph->functions->rewrite_applied_correctly_to_site();
                    
                    //disable the filters
                    $this->wph->disable_filters   =   TRUE;
                     
                    $this->interface_save_redirect($screen_slug, $tab_slug, 'reset_settings=true');
                       
                }
            
            
            function create_headers_sample_setup()
                {
                    
                    $nonce  =   $_POST['wph-interface-nonce'];
                    if ( ! wp_verify_nonce( $nonce, 'wph/interface_fields' ) )
                        return FALSE;
                        
                    //only for admins
                    If ( !  current_user_can ( 'manage_options' ) )
                        return FALSE;
                        
                    $screen_slug  =   isset ( $_GET['page'] )         ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['page'] )         :   '';
                    $tab_slug     =   isset ( $_GET['component'] )    ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['component'] )    :   '';
                    
                    $site_settings  =   $this->functions->get_site_modules_settings_to_apply( $this->functions->get_blog_id_setting_to_use() );
                    
                    //reset the options
                    $headers    =   array ( 
                                            'cross_origin_embedder_policy',
                                            'cross_origin_opener_policy',
                                            'cross_origin_resource_policy',
                                            'content_security_policy',
                                            'content_security_policy_report_only',
                                            'expect_ct',
                                            'permissions_policy',
                                            'referrer_policy',
                                            'strict_transport_security',
                                            'x_content_type_options',
                                            'x_download_options',
                                            'x_frame_options',
                                            'x_permitted_cross_domain_policies',
                                            'x_xss_protection'                                            
                                            );
                    foreach ( $headers as $header )
                        {
                            if ( ! isset ( $site_settings[ $header ] )   ||  ! is_array ( $site_settings[ $header ]  ) )
                                $site_settings[ $header ]   =   array (
                                                                        'enabled'   =>  'no' 
                                                                        );
                            
                            $site_settings[ $header ]['enabled']    =   'no';
                        }
                        
                        
                    //add the custom headers
                    $site_settings[ 'cross_origin_embedder_policy' ]['enabled']     =   'yes';
                    $site_settings[ 'cross_origin_embedder_policy' ]['value']       =   'unsafe-none';
                    
                    $site_settings[ 'cross_origin_opener_policy' ]['enabled']       =   'yes';
                    $site_settings[ 'cross_origin_opener_policy' ]['value']         =   'unsafe-none';
                    
                    $site_settings[ 'cross_origin_resource_policy' ]['enabled']     =   'yes';
                    $site_settings[ 'cross_origin_resource_policy' ]['value']       =   'cross-origin';
                    
                    $site_settings[ 'content_security_policy' ]['enabled']          =   'yes';
                    $site_settings[ 'content_security_policy' ] =   $this->_reset_header_options( $site_settings[ 'content_security_policy' ] );
                    $site_settings[ 'content_security_policy' ]['report-to']            =   'default';
                    
                    $site_settings[ 'expect_ct' ]['enabled']                        =   'yes';
                    $site_settings[ 'expect_ct' ] =   $this->_reset_header_options( $site_settings[ 'expect_ct' ] );
                    $site_settings[ 'expect_ct' ]['max-age']                        =   '2592000';
                    $site_settings[ 'expect_ct' ]['enforce']                        =   'enforce';
                    
                    $site_settings[ 'permissions_policy' ]['enabled']                        =   'yes';
                    $site_settings[ 'permissions_policy' ] =   $this->_reset_header_options( $site_settings[ 'permissions_policy' ] );
                    $site_settings[ 'permissions_policy' ]['accelerometer']['enabled']       =   'yes';
                    $site_settings[ 'permissions_policy' ]['accelerometer']['selection']     =   'none';
                    $site_settings[ 'permissions_policy' ]['gyroscope']['enabled']           =   'yes';
                    $site_settings[ 'permissions_policy' ]['gyroscope']['selection']         =   'none';
                    $site_settings[ 'permissions_policy' ]['gamepad']['enabled']             =   'yes';
                    $site_settings[ 'permissions_policy' ]['gamepad']['selection']           =   'none';
                    $site_settings[ 'permissions_policy' ]['gyroscope']['enabled']           =   'yes';
                    $site_settings[ 'permissions_policy' ]['gyroscope']['selection']         =   'none';
                    $site_settings[ 'permissions_policy' ]['gyroscope']['enabled']           =   'yes';
                    $site_settings[ 'permissions_policy' ]['gyroscope']['selection']         =   'none';
                    
                    $site_settings[ 'referrer_policy' ]['enabled']                   =   'yes';
                    $site_settings[ 'referrer_policy' ]['value']                     =   'strict-origin-when-cross-origin';
                    
                    $site_settings[ 'strict_transport_security' ]['enabled']                        =   'yes';
                    $site_settings[ 'strict_transport_security' ] =   $this->_reset_header_options( $site_settings[ 'strict_transport_security' ] );
                    $site_settings[ 'strict_transport_security' ]['max-age']                        =   '2592000';
                    $site_settings[ 'strict_transport_security' ]['preload']                        =   'yes';
                    
                    $site_settings[ 'x_download_options' ]['enabled']     =   'yes';
                    $site_settings[ 'x_download_options' ]['value']       =   'noopen';
                    
                    $site_settings[ 'x_frame_options' ]['enabled']     =   'yes';
                    $site_settings[ 'x_frame_options' ]['value']       =   'SAMEORIGIN';
                    
                    $site_settings[ 'x_xss_protection' ]['enabled']     =   'yes';
                    $site_settings[ 'x_xss_protection' ]['value']       =   '1; mode=block';
                        
                    $this->functions->update_site_modules_settings( $site_settings , $this->functions->get_blog_id_setting_to_use() );
                                                        
                    //trigger the settings changed action
                    do_action('wph/settings_changed');
                    
                    //check if the rules applyed so r-init modules using the new settings
                    $wph_rewrite_manual_install =   get_site_option('wph-rewrite-manual-install');
                    if ( empty ( $wph_rewrite_manual_install ) )
                        {
                            $this->wph->_init_urls_replacements();
                        }
                    
                    $this->interface_save_redirect( $screen_slug, $tab_slug, 'settings_updated=true&headers_sample_setup=true');
                    
                }
            
            
            
            function _reset_header_options( $header )
                {
                    foreach ( $header as $key   =>  $data )
                        {
                            if ( $key == 'enabled' )
                                continue;
                            if ( is_array ( $data ) )
                                $header[$key]   =   array();
                                else
                                $header[$key]   =   '';
                        }    
                    
                    return $header;
                }
            
                   
            function _render( $interface_name )
                {
                    
                    $this->screen_slug  =   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['page'] );
                    $this->tab_slug     =   isset($_GET['component'])   ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['component'] )  :   FALSE;
                    
                    //identify the module by slug
                    $this->module   =   $this->functions->get_module_by_slug($this->screen_slug);
                    
                    if(empty($this->tab_slug)   &&  $this->module->use_tabs  === TRUE)
                        {
                            //get the first component
                            foreach($this->module->components   as  $module_component)
                                {
                                    if( ! $module_component->title)
                                        continue;
                                    
                                    $this->tab_slug =   $module_component->id;
                                    break;
                                }  
                            
                        }
                   
                    $this->_load_interface_data();
   
                    $this->_generate_interface_html();
                    
                }
            
            function _load_interface_data()
                {
                    $this->module_settings  =   $this->functions->filter_settings(   $this->module->get_module_components_settings($this->tab_slug ));
                        
                    $this->interface_data   =   $this->module->get_interface_data();                      
                }
                  
            function _generate_interface_html()
                {
                    
                    ?>
                        <div id="wph" class="wrap">
                            <h1><?php echo $this->interface_data['title'] ?></h1>
                         
                            <?php
                                
                                if(is_network_admin())
                                    {
                                        ?><p><span class="dashicons dashicons-admin-post"></span> <?php _e('The following settings are used as default, for other sites in the network',    'wp-hide-security-enhancer');?></p><?php
                                    }
                                
                                
                                if($this->module->use_tabs  === TRUE)
                                    $this->_generate_interface_tabs( $this->tab_slug );
                            
                            ?>
                                                     
                            <div id="poststuff">
                                
                                <?php if(!empty($this->interface_data['handle_title'])) { ?>
                                <div class="postbox">
                                    <h3 class="handle"><?php echo $this->interface_data['handle_title'] ?></h3>
                                </div>
                                <?php } ?>
                                
                                    <div class="inside">
                                           
                                        <form method="post" id="wph-form" action="<?php 
                                            
                                            $args   =   array(
                                                                'page'          =>  isset($_GET['page'])        ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['page'] )  :   '',
                                                                'component'     =>  isset($_GET['component'])   ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['component'] )  :   '',
                                                                );
                                                
                                            $url_query  =   http_build_query( $args );
                                            
                                            if( is_network_admin() )
                                                echo esc_url(network_site_url( 'wp-admin/network/admin.php?' . $url_query ));
                                                else
                                                echo esc_url(admin_url( 'admin.php?' . $url_query));
                                        ?>">
                                            <?php wp_nonce_field( 'wph/interface_fields', 'wph-interface-nonce' ); ?>
                                            <input type="hidden" name="wph-interface-fields" value="true" />
                                            
                                            <div class="options">
                                                <?php
                                                    
                                                    $module_object  =   $this->functions->get_module_component_by_slug ( $this->tab_slug );
                                                    $module_description =   $module_object->get_module_description();
                                                    if ( $module_description    !== FALSE )
                                                        echo $module_description;
                                                
                                                ?>
                                            </div>
                                            
                                            <?php
                                            
                                                $outputed_module    =   FALSE;
                                                $require_save       =   FALSE;
                                                $last_type          =   '';
                                                
                                                foreach($this->module_settings  as  $module_setting)
                                                    {
                                                        
                                                        //check if there are any display conditions
                                                        if(isset($module_setting['display_conditions']) &&  !empty($module_setting['display_conditions'])   &&  is_array($module_setting['display_conditions']) &&  count($module_setting['display_conditions'])    >   0 )
                                                            {   
                                                                $condition_satisfied    =   TRUE;
                                                                foreach($module_setting['display_conditions']   as  $condition)
                                                                    {
                                                                        $condition_satisfied    =   call_user_func_array($condition, array($module_setting['callback_arguments']));
                                                                        if($condition_satisfied === FALSE)
                                                                            break;
                                                                    }
                                                                    
                                                                if($condition_satisfied === FALSE)
                                                                    continue;
                                                            }
                                                        
                                                        //if ( $last_type ==  'split' &&  $module_setting['type'] ==  'split')
                                                           // continue;
                                                            
                                                        $this->_generate_module_html( $module_setting );
                                                        if ( isset ( $module_setting['require_save'] )  &&  $module_setting['require_save'] )
                                                            $require_save   =   TRUE;    
                                                        
                                                        $last_type          =   $module_setting['type'];   
                                                        
                                                        $outputed_module    =   TRUE;
                                                    }
                                            
                                            
                                            ?>
                                            <?php if ( $require_save ) { ?>  
                                            <table class="wph_submit widefat">
                                                <tbody>
                                                    <tr class="submit">
                                                        <td class="label">&nbsp;</td>
                                                        <td class="label">
                                                            <input type="submit" value="<?php _e('Save',    'wp-hide-security-enhancer') ?>" class="button-primary alignright"> 
                                                        </td>    
                                                    </tr>
                                                </tbody>
                                            </table>
                                            <?php } ?>
                                        </form>
                                        
                                        <?php if ( $require_save ) { ?>
                                        <form id="reset_settings_form" action="<?php 
                                            if( is_network_admin() )
                                                echo esc_url(network_site_url( 'wp-admin/network/admin.php?page=wp-hide' ));
                                                else
                                                echo esc_url(admin_url( 'admin.php?page=wp-hide'));
                                        ?>" method="post">
                                            <input type="hidden" name="wph-reset-settings" value="true" />
                                            
                                            <?php
                                            
                                                $screen_slug  =   isset($_GET['page'])         ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['page'] )         :   '';
                                                $tab_slug     =   isset($_GET['component'])    ?   preg_replace( '/[^a-zA-Z0-9\-\_$]/m' , "", $_GET['component'] )    :   '';
                                            
                                            ?>
                                            <input type="hidden" name="wph-page" value="<?php echo $screen_slug ?>" />
                                            <input type="hidden" name="wph-component" value="<?php echo $tab_slug ?>" />
                                            <?php wp_nonce_field( 'wp-hide-reset-settings', '_wpnonce' ); ?>
                                            
                                            <a href="javascript: void(0);" onclick="wph_setting_page_reset_confirmation ();" class="reset_settings button-secondary"><?php _e('Reset Page Settings',    'wp-hide-security-enhancer') ?></a>
                                                <script type='text/javascript'>
                                                    function wph_setting_page_reset_confirmation () 
                                                        {
                                                            var agree   =   confirm(wph_vars.reset_page_confirmation);
                                                            if (!agree)
                                                                return false;
                                                                
                                                            jQuery ('form#wph-form input[type="text"].setting-value' ).each( function() {
                                                                jQuery(this).val('');
                                                            }) 
                                                            jQuery ('form#wph-form textarea.setting-value' ).each( function() {
                                                                jQuery(this).val('');
                                                            })
                                                            jQuery ('form#wph-form input[type="radio"].setting-value' ).each( function() {
                                                                if ( jQuery(this).hasClass('default-value') )
                                                                    jQuery(this).prop("checked", true);
                                                                    else
                                                                    jQuery(this).prop("checked", false);
                                                            })
                                                            jQuery ('form#wph-form input[type="checkbox"].setting-value' ).each( function() {
                                                                jQuery(this).prop("checked", false);
                                                            }) 
                                                        }
                                                    
                                                </script>
                                                
                                            <input type="button" class="reset_settings button-secondary" value="<?php _e( "Reset All Settings", 'wp-hide-security-enhancer' ) ?>" onclick="wph_setting_reset_confirmation ();">
                                            
                                            <script type='text/javascript'>
                                                function wph_setting_reset_confirmation () 
                                                    {
                                                        var agree   =   confirm(wph_vars.reset_confirmation);
                                                        if (!agree)
                                                            return false;
                                                            
                                                        document.getElementById("reset_settings_form").submit();
                                                    }
                                                
                                            </script>
                                        </form>
                                        <?php } ?> 
                                    </div>
                              
                            </div>
                        </div>
                  
                <?php   
                
                $this->wph->interface_expand();
                    
                }
                
                
            function _generate_module_html( $module_setting )
                {
                    
                    if(isset($module_setting['type'])   &&  $module_setting['type']    ==  'split' )
                        {
                            if (    ! empty ( $module_setting['label'] ) )
                                {
                                    ?>
                                    <div class="section_title"><?php echo $module_setting['label'] ?></div>
                                    <?php   
                                }
                                else
                                    {
                                        ?>
                                        <p>&nbsp;</p>
                                        <?php
                                    }
                            
                            return;
                        }
                                             
                    if($module_setting['visible']   === FALSE)
                        return;
                        
                    global $blog_id;
                    
                    if ( is_multisite() &&  is_network_admin() )
                        $blog_id_settings   =   'network';
                        else
                        $blog_id_settings   =   $blog_id;
                        
                    $option_name    =   $module_setting['id'];
                    $value          =   $this->wph->get_setting_value(  $option_name, $module_setting );

                    
                    $is_advanced    =   ! empty ( $module_setting['advanced_option'] )  ?   TRUE    :   FALSE;
                    $hide_advanced  =   ( $is_advanced  &&  ( $value   ==  'no'    ||  empty ( $value ) )) ?    TRUE    :   FALSE;
                    
                    ?>
                        <div class="postbox wph-postbox">
                        <div class="wph_input widefat<?php if ( $module_setting['interface_help_split']   === FALSE ) { echo ' full_width';} ?>">
                            <div class="row cell label">
                                <ul class="options">
                                    <?php if ( $module_setting['input_type'] == 'text' ) { ?>
                                    <li><span class="tips dashicons dashicons-edit"          title='Generate random value for the field' onClick="WPH.randomWord( this, '<?php if  ( ! empty ($module_setting['help']['input_value_extension'])) { echo $module_setting['help']['input_value_extension']; }  ?>' )"></span></li>
                                    <li><span class="tips dashicons dashicons-admin-appearance"  title='Remove the field value'  onClick="WPH.clear( this )"></span></li>
                                    <?php } ?>
                                    <?php
                                        
                                        if ( $module_setting['help'] !==    FALSE   &&  ! empty( $module_setting['help']['option_documentation_url'] ))
                                            {
                                        
                                    ?>
                                    <li><a target="_blank" href="<?php echo $module_setting['help']['option_documentation_url'] ?>"><span class="tips dashicons dashicons-admin-links"       title='Open option help page'></span></a></li>
                                    <?php
                                            }
                                    ?>
                                </ul>
                                <label for=""><?php echo $module_setting['label'] ?></label>
                                <?php
                                    
                                    if(is_array($module_setting['description']) &&  count ( $module_setting['description'] ) > 0 )
                                        {
                                            foreach($module_setting['description']  as  $description)
                                                {
                                                    ?>
                                                        <div class="description"><?php echo nl2br($description) ?></div>
                                                    <?php
                                                }    
                                        }
                                        else  if  ( ! empty ($module_setting['description'] ))
                                        {
                                            ?>
                                                <p class="description"><?php echo nl2br($module_setting['description']) ?></p>
                                            <?php 
                                        } ?>
                                        
                                <?php 
                                        
                                    if  ( $is_advanced && $hide_advanced ) 
                                        { 
                                            ?>
                                            <div class="advanced_notice">
                                                <div class="icon">
                                                    <img src="<?php echo WPH_URL ?>/assets/images/warning.png" />
                                                </div>
                                                <div class="text">
                                                    <?php  echo wpautop ( $module_setting['advanced_option']['description'] )  ?>
                                                </div>
                                                <div class="actions">
                                                    <a href="javascript: void(0)" onclick="WPH.showAdvanced( jQuery(this) )" class="button-primary">SHOW</a>    
                                                </div>
                                            </div>
                                            
                                            <?php
                                        }
                                    
                                ?>
                            </div>
                            <div class="row cell data entry<?php if  ( $is_advanced ) { echo ' advanced';} if  ( $hide_advanced ) { echo ' hide';  }   ?>"> 
                                <?php
                                
                                if ( $module_setting['interface_help_split']    === FALSE ) { ?>
                                <div class="option_help<?php  if ( $module_setting['help'] ===    FALSE ) { echo ' empty'; } ?>">
                                    <div class="text">
                                    <?php if ( ! empty ( $module_setting['help']['title'] ) ) { ?>
                                    <h4><?php echo $module_setting['help']['title'] ?></h3>
                                    <?php } ?>
                                    <?php  if ( $module_setting['help'] !==    FALSE ) { ?>
                                        <p><?php echo wpautop ( $module_setting['help']['description'] )  ?></p>
                                    <?php } else { ?>
                                    <p>There is no help available for this option.</p>
                                    <?php }?>
                                    </div>
                                    
                                </div>
                                <?php } ?>
                                
                                <?php if(!empty($module_setting['options_pre'])) { ?><div class="options_text text_pre"><?php echo $module_setting['options_pre'] ?></div><?php } ?>
                                
                                <div class="orow">
                                <?php
                                
                                if ( isset($module_setting['module_option_html_render'])    &&  is_callable($module_setting['module_option_html_render']))
                                    {
                                        call_user_func($module_setting['module_option_html_render'], $module_setting);
                                    }
                                    else
                                    {
                                    ?>
                                    <?php if(!empty($module_setting['value_description'])) { ?><p class="description"><?php echo $module_setting['value_description'] ?></p><?php } ?>
                                    <!-- WPH Preserve - Start -->
                                    <?php
                                    
                                        $option_name    =   $module_setting['id'];
                                        
                                        //replace the period char into underline
                                        $option_name    =   str_replace(".", "_", $option_name);
                                        
                                        $value          =   $this->functions->get_site_module_saved_value(  $module_setting['id'], $blog_id_settings, 'display' );
                                        
                                        switch($module_setting['input_type'])
                                            {
                                                case 'text' :
                                                                $class          =   'text';
                                                                
                                                                ?><input name="<?php echo $option_name ?>" class="setting-value <?php echo $class ?>" value="<?php echo esc_html( htmlentities( $value) ) ?>" placeholder="<?php echo esc_html($module_setting['placeholder']) ?>" type="text"><?php
                                                                
                                                                break;
                                                                
                                                case 'textarea' :
                                                                $class          =   'textarea';
                                                                
                                                                ?><textarea name="<?php echo $option_name ?>" class="setting-value <?php echo $class ?>"><?php echo esc_html( htmlentities( $value ) ) ?></textarea><?php
                                                                
                                                                break;
                                                                
                                                case 'radio' :
                                                                $class          =   'radio';
                                                                
                                                                if ( empty($value) )
                                                                    $value  =   $module_setting['default_value'];   
                                                                                                                                                                
                                                                ?>
                                                                <fieldset>
                                                                    <?php  
                                                                    
                                                                        foreach($module_setting['options']  as  $option_value  =>  $option_title)
                                                                            {
                                                                                ?><label><input type="radio" class="setting-value <?php
                                                                                                
                                                                                if ( $option_value ==   'no' )
                                                                                    echo 'default-value ';
                                                                                
                                                                                ?><?php echo $class ?>" <?php checked($value, $option_value)  ?> value="<?php echo $option_value ?>" name="<?php echo $option_name ?>"> <span><?php echo esc_html($option_title) ?></span></label><?php
                                                                            }
                                                                    
                                                                    ?>
                                                                </fieldset>
                                                                <?php
                                                                
                                                                break;
                                                                
                                                case 'checkbox' :
                                                                $class          =   'checkbox';
                                                                
                                                                if ( empty($value) )
                                                                    $value  =   $module_setting['default_value'];   
                                                                                                                                                                
                                                                ?>
                                                                <fieldset>
                                                                    <?php  
                                                                    
                                                                        foreach($module_setting['options']  as  $option_value  =>  $option_title)
                                                                            {
                                                                                ?><label><input type="checkbox" class="setting-value <?php echo $class ?>" <?php checked( in_array( $option_value, $value ), TRUE )  ?> value="<?php echo $option_value ?>" name="<?php echo $option_name ?>[]"> <span><?php echo esc_html($option_title) ?></span></label><?php
                                                                            }
                                                                    
                                                                    ?>
                                                                </fieldset>
                                                                <?php
                                                                
                                                                break;    
                                                case 'HTML' :
                                                                $HTML_data  =   $module_setting['HTML_data'];
                                                                echo $HTML_data;
                                                                
                                                                break;
                                            }
                                            
                                    ?>
                                    <!-- WPH Preserve - Stop -->
                                    <?php
                                    }
                                ?>
                                </div>
                                <?php if(!empty($module_setting['options_post'])) { ?><div class="options_text text_post"><?php echo $module_setting['options_post'] ?></div><?php } ?>
                            </div>
                        </div>
                        
                        <?php if ( $module_setting['interface_help_split'] ) { ?>
                        <div class="wph_help option_help<?php  if ( $module_setting['help'] ===    FALSE ) { echo ' empty'; } ?>">
                            <div class="text">
                            <h4><?php echo $module_setting['help']['title'] ?></h3>
                            <?php  if ( $module_setting['help'] !==    FALSE ) { ?>
                                <?php echo wpautop ( $module_setting['help']['description'] ) ?>
                                <?php  if ( ! empty ( $module_setting['help']['option_documentation_url'] ) ) { ?>  <br /> <a class="button read_more" target="_blank" href="<?php echo $module_setting['help']['option_documentation_url'] ?>">Read More</a> <br /><br /><?php } ?>
                            <?php } else { ?>
                            <p>There is no help available for this option.</p>
                            <?php }?>
                            </div>
                            
                        </div>
                        <?php } ?>
                        
                        </div>   
                    
                    <?php   
                    
                }
                
                
            function _generate_interface_tabs( $tab_slug )
                {
                    
                    ?> 
                    <h2 class="nav-tab-wrapper <?php echo $tab_slug ?>">
                        <?php
                            
                            //output all module components as tabs
                            foreach($this->module->components   as  $module_component)
                                {
                                    if( ! $module_component->title)
                                        continue;
                                    
                                    $class  =   '';
                                    if($module_component->id    ==  $this->tab_slug)
                                        $class  =   'nav-tab-active';
                                        
                                    $class  .=   ' ' . $module_component->id;
                                    
                                    if ( is_a ( $this->module,  'WPH_module_security_headers' ) )
                                        {
                                            $module_settings    =   $module_component->get_module_component_settings();
                                            if ( isset ( $module_settings[0] ) )
                                                {
                                                    $module_component_settings   =   $module_settings[0];
                                                    $values =   $this->wph->functions->get_site_module_saved_value( $module_component_settings['id'],  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                                                    if ( isset ( $values['enabled'] )   &&  $values['enabled']  ==  'yes' )
                                                        $class  .=  ' header-active';
                                                }
                                        }
                                    
                                    $component_link =   esc_url ( network_admin_url ( 'admin.php?page=' . $this->screen_slug . '&component=' . $module_component->id ) );
                                                 
                                    ?>   
                                    <a href="<?php echo $component_link ?>" class="nav-tab <?php echo $class ?>"><?php echo $module_component->title ?></a>
                                    <?php                                    
                                }
                        
                        ?>
                        <a href="javascript:void(0)" class="button-secondary cancel alignright" onClick="wph_setting_reset()" id="reset_settings" style="display: none">Reset All Settings</a>
                    </h2>
                    
                    <?php
                    
                }
                
                
                
            function admin_notices()
                {

                    $settings   =   $this->functions->get_current_site_settings();
                    
                    if( ! $this->functions->is_muloader())
                        {
                            echo "<div class='error'><p>". __('Unable to launch WP Hide through mu-plugins/wp-hide-loader.php<br /> Please make sure this location is writable so the plugin create the required file, or manually copy the wp-hide-security-enhancer-pro/mu-loader/wp-hide-loader.php to mu-plugins/wp-hide-loader.php.', 'wp-hide-security-enhancer')  ."</p></div>";
                        }
                        
                    if( ! $this->functions->check_wp_config( FALSE ))
                        {
                            echo "<div class='error'><p>". __('Unable to add required data to wp-config.php<br /> Please make sure this location is writable so the plugin append required data, or manually deploy the data specified withint Setup interface.', 'wp-hide-security-enhancer')  ."</p></div>";
                        }
                        
                    
                    //post processing, once the setting where saved and page redirected
                    if( isset($_GET['settings_updated']) 
                           // ||  (  ( ( isset ( $_GET['activate'] )   &&   $_GET['activate']   ==  'true' ) ||  ( isset ( $_GET['deactivate'] ) && $_GET['deactivate']   ==  'true' ) ) )
                            )
                        {
                            //check if the rules are not applied
                            $this->check_for_rewrite_apply();
                        }
                        else
                        {
                            //the admin manually updated the rewrite on the server and the environment data becomed available
                            $write_check_string =   isset($settings['write_check_string']) ?    $settings['write_check_string'] :   '';
                            if(!empty($write_check_string))
                                {                            
                                    $existing_write_check_string =   $this->functions->get_write_check_string_from_server();
                                    if( !empty($existing_write_check_string)  &&  $existing_write_check_string    ==  $write_check_string)
                                        {
                                            delete_site_option( 'wph-errors-rewrite-to-file');
                                        }
                                }
                        }
                        
                    
                    //check for permalinks enabled
                    if (!$this->functions->is_permalink_enabled())
                        {
                            echo "<div class='error'><p>". __('Permalink is required to be turned ON for WP Hide & Security Enhancer PRO to work', 'wp-hide-security-enhancer')  ."</p></div>";
                        }
                             
                    //Who knows, maybe someone could use a different system to deploy WordPress    
                    if($this->wph->server_htaccess_config    === FALSE && $this->wph->server_web_config   === FALSE &&  $this->wph->server_nginx_config === FALSE)
                        {
                            echo "<div class='error'><p><b>WP Hide</b> ". __('Unable to identify server type!', 'wp-hide-security-enhancer') . '<br />' . __('No rewrite is being applied.', 'wp-hide-security-enhancer') ."</p></div>";
                        }
                    
                                               
                    if(isset($_GET['reset_settings']))
                        {
                            echo "<div class='updated'><p>". __('All Settings where restored to default', 'wp-hide-security-enhancer')  ."</p></div>";
                            
                            //check if the data where sucesfully writted to the rewrite file                            
                            $this->functions->settings_changed_check_for_cache_plugins();
                        }
                    
                    //Only if not on Setup Page
                    if (! isset ( $_GET['page'])    ||  (  isset ( $_GET['page'])   &&  $_GET['page']  !=  'wp-hide-setup' ) )
                        {
                            $disable_message    =   array(
                                                                'rewrite-to-file'           =>  get_site_option('wph-errors-rewrite-to-file'),
                                                                'environment'               =>  get_site_option('wph-errors-environment'),
                                                                'nginx-rewrite-to-file'     =>  get_site_option('wph-errors-nginx_rewrites_to_file')
                                                                );
                            foreach ( $disable_message          as  $error_slug =>  $error_message )
                                {
                                    if (empty ($error_message))
                                        continue;

                                    $this->_output_closable_notice_messages( $error_slug, $error_message );
                                }
                        }
                    
                    
                    if( isset( $_GET['headers_sample_setup'] ) )
                        {
                            echo "<div class='notice notice-success'><p>". __('Headers Sample Setup deployed successfully.', 'wp-hide-security-enhancer')  ."</p></div>";   
                        }
                        
                    
                    //check the required DOOMDocumment if postprocessing is active
                    $css_combine_code   =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    $js_combine_code    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',   $this->wph->functions->get_blog_id_setting_to_use());
                    if( $css_combine_code != 'no'   ||  $js_combine_code != 'no' )
                        {
                            if ( ! class_exists('DOMDocument')) 
                                {
                                    echo "<div class='notice notice-error'><p>". __('The PHP DOMDocument is required for the CSS / JavaScript PostProcessing. Contact your server support to install the DOMDocument module.', 'wp-hide-security-enhancer')  ."</p></div>";   
                                }
                        }
                    
                        
                    if(isset($_GET['settings_updated']))
                        {
                            
               
                            //check for interface save processing errors
                            $messages  =   get_option( 'wph-interface-save-errors' );
                            
                            $found_error       =   FALSE;
                            if( is_array($messages)    &&  count($messages) > 0)
                                {
                                    foreach ( $messages    as $process_interface_save_error )
                                        {                                    
                                            if( is_array ( $process_interface_save_error )  &&  $process_interface_save_error['type']    == 'error')
                                                $found_error   =   TRUE;
                                        }
                                }
                            
                            if( $found_error   === FALSE )
                                echo "<div class='notice notice-success'><p>". __('Settings saved', 'wp-hide-security-enhancer')  ."</p></div>";
                            
                            $this->_output_notice_messages( $messages );
                                
                            delete_option('wph-interface-save-errors');
                            
                            if  ( ! is_array( $messages )   ||  count ( $messages ) < 1 ) 
                                $this->functions->settings_changed_check_for_cache_plugins();
                        }
                    
                    do_action('wp-hide/interface/admin_notices');
                               
                }
                
            
            private function _output_notice_messages( $messages )
                {
                    
                    $found_warning     =   FALSE;
                    $found_error       =   FALSE;
                    
                    if( is_array($messages)    &&  count($messages) > 0)
                        {
                            foreach ( $messages    as $process_interface_save_error )
                                {                                    
                                    if( is_array ( $process_interface_save_error )  && $process_interface_save_error['type']    == 'warning')
                                        $found_warning =   TRUE;
                                        
                                    if( is_array ( $process_interface_save_error )  && $process_interface_save_error['type']    == 'error')
                                        $found_error   =   TRUE;
                                }
                            
                        }
                    
                    if( is_array($messages)    &&  count($messages) > 0)
                        {
                            ?><!-- WPH Preserve - Start --><?php
                            $notices_types  =   array(
                                                        'warning',
                                                        'error'
                                                        );
                            
                            foreach ( $notices_types    as  $notice_type ) 
                                {
                                    
                                    if ( ${'found_' . $notice_type} !==  TRUE )
                                        continue;
                                        
                                    echo "<div class='notice notice-" .$notice_type ."'><!-- WPH Preserve - Start --><p>";
                                    foreach ( $messages    as  $process_interface_save_error )
                                        {
                                            if( is_array ( $process_interface_save_error )  && $process_interface_save_error['type']    == $notice_type)
                                                {
                                                    echo $process_interface_save_error['message'] .'<br />';
                                                }
                                        }
                                    echo "</p><!-- WPH Preserve - Stop --></div>";   
                                    
                                    
                                }
                            ?><!-- WPH Preserve - Stop --><?php
                        }   
                    
                    
                }
                
            
            private function _output_closable_notice_messages( $error_slug, $error_message )
                {
                    $current_url    =   $this->functions->get_current_url();
                    $current_url    =   remove_query_arg('settings_updated', $current_url);
                    $current_url    =   add_query_arg('wph-hide-notice', $error_slug, $current_url);
                    
                    ?>
                        <div class='wph-notice notice-error error'>
                            <a class="wph-message-close notice-dismiss" href="<?php echo esc_url( wp_nonce_url( $current_url, 'wph_hide_notices_nonce', '_wph_notice_nonce' ) ); ?>"></a>
                            <p><?php echo $error_message ?></p>
                        </div>
                    <?php    
                }
            
            
            /**
            * Check if the rewrite has been applied and add messages acordingly
            *     
            */
            function check_for_rewrite_apply ()
                {

                    $global_settings    =   $this->functions->get_global_settings ( );
                    
                    //check if the data where sucesfully writted to the rewrite file
                    if( $this->wph->server_htaccess_config    !== FALSE || $this->wph->server_web_config   !== FALSE )
                        {
                            
                            $settings   =   $this->functions->get_current_site_settings();
                            
                            $wph_rewrite_manual_install =   get_site_option('wph-rewrite-manual-install');
                            //check if the write_check_string's are available through $_SERVER
                            $write_check_string =   isset($settings['write_check_string']) ?    $settings['write_check_string'] :   '';
                                  
                            if( !empty( $write_check_string )  ||  $wph_rewrite_manual_install ==  'yes')
                                {                            
                                    $existing_write_check_string =   $this->functions->get_write_check_string_from_server();
                                                                                       
                                    if(empty($existing_write_check_string)  ||  $existing_write_check_string    !=  $write_check_string)
                                        {
            
                                            update_site_option( 'wph-rewrite-manual-install', 'yes');
                                            
                                            $message    =   '<b>WP Hide</b> - ';
                                            
                                            if ( $global_settings['self_setup']  !=  'yes' )
                                                {
                                                    if($this->wph->server_htaccess_config    === TRUE)
                                                        $message    =    __('Unable to write custom rules to your .htaccess. Is this file writable?', 'wp-hide-security-enhancer');
                                                    if($this->wph->server_web_config     === TRUE)
                                                        $message    =   __('Unable to write custom rules to your web.config. Is this file writable? <br />No mod is being applied.', 'wp-hide-security-enhancer');   
                                                    
                                                    $message    .=  '<br />';
                                                }
                                                else
                                                {
                                                    $message    .=   __('Self set-up is turned on, there will be no automated attempt to write data.', 'wp-hide-security-enhancer'); 
                                                    $message    .=  '<br />';  
                                                }
                                                
                                                
                                            if( is_multisite())
                                                {
                                                    if( current_user_can ('setup_network') )
                                                        $message    .=    __('Check Network Setup menu item for manual rewrite data deploy.', 'wp-hide-security-enhancer');
                                                        else
                                                        $message    .=    __('Network Admin has been notified. He will manually deploy the rewrite data.', 'wp-hide-security-enhancer');
                                                }
                                                else
                                                    {
                                                        $message    .=    __('Check Setup menu for instructions on how to deploy the rewrite rules on your server.', 'wp-hide-security-enhancer');   
                                                    }
                                            
                                            update_site_option( 'wph-errors-rewrite-to-file', $message);
                                        }
                                        else
                                        {
                                            delete_site_option( 'wph-errors-rewrite-to-file' );
                                        }
                                }
                            
                            
                        }
                        else
                        {
                            
                            $ms_settings    =   $this->wph->functions->get_site_settings('network');
                            
                            $require_manual_install =   get_site_option ( 'wph-rewrite-manual-install' );
                            if ( $require_manual_install    ==  'yes' )
                                {
                                    $message    =    __('<b>Your server operates on Nginx!</b><br /> Automatic deployment of the rewrite rules is not possible, manual action is required. Refer to the Setup menu for guidelines on adding the rewrite rules to your server.', 'wp-hide-security-enhancer');
                                    
                                    update_site_option( 'wph-errors-rewrite-to-file', $message);
                                }
                            
                            //write the nginex rewrites    
                            if ( $this->wph->server_nginx_config === TRUE )
                                $this->functions->write_nginx_rewrites_to_files();
                        }    
                    
                }
                
                
            
            function network_admin_notices()
                {

                    
                    if( isset($_GET['settings_updated']) 
                            ||  ( ( isset ( $_GET['activate'] )   &&   $_GET['activate']   ==  'true' ) ||  ( isset ( $_GET['deactivate'] ) && $_GET['deactivate']   ==  'true' ) ) 
                            )
                        {
                            
                            //check if the data where sucesfully writted to the rewrite file
                            $this->check_for_rewrite_apply();
                        }    
                        
                    if($this->wph->server_htaccess_config    === FALSE && $this->wph->server_web_config   === FALSE &&  $this->wph->server_nginx_config === FALSE)
                        {
                            echo "<div class='error'><p><b>WP Hide</b> ". __('Unable to identify server type!', 'wp-hide-security-enhancer') . '<br />' . __('No rewrite is being applied.', 'wp-hide-security-enhancer') ."</p></div>";
                        }
                    
                    if( ! $this->functions->check_wp_config( FALSE ))
                        {
                            echo "<div class='error'><p>". __('Unable to add required data to wp-config.php<br /> Please make sure this location is writable so the plugin append required data, or manually deploy the data specified withint Setup interface.', 'wp-hide-security-enhancer')  ."</p></div>";
                        }                        
                        
                    //Only if not on Setup Page
                    if (! isset ( $_GET['page'])    ||  (  isset ( $_GET['page'])   &&  $_GET['page']  !=  'wp-hide-setup' ) )
                        {
                            $disable_message    =   array(
                                                                'rewrite-to-file'           =>  get_site_option('wph-errors-rewrite-to-file'),
                                                                'environment'               =>  get_site_option('wph-errors-environment'),
                                                                'nginx-rewrite-to-file'     =>  get_site_option('wph-errors-nginx_rewrites_to_file')
                                                                );
                            foreach ( $disable_message          as  $error_slug =>  $error_message )
                                {
                                    if (empty ($error_message))
                                        continue;

                                    $this->_output_closable_notice_messages( $error_slug, $error_message );
                                }
                        }
                    
                    if( isset( $_GET['headers_sample_setup'] ) )
                        {
                            echo "<div class='notice notice-success'><p>". __('Headers Sample Setup deployed successfully.', 'wp-hide-security-enhancer')  ."</p></div>";   
                        }
                    
                    if(isset($_GET['settings_updated']))
                        {
                            //check for interface save processing errors
                            $messages  =   get_option( 'wph-interface-save-errors' );
                            
                            $found_error       =   FALSE;
                            if( is_array($messages)    &&  count($messages) > 0)
                                {
                                    foreach ( $messages    as $process_interface_save_error )
                                        {                                    
                                            if( is_array ( $process_interface_save_error )    &&  $process_interface_save_error['type']    == 'error')
                                                $found_error   =   TRUE;
                                        }
                                }
                            
                            if( $found_error   === FALSE )
                                echo "<div class='notice notice-success'><p>". __('Settings saved', 'wp-hide-security-enhancer')  ."</p></div>";
                            
                            $this->_output_notice_messages( $messages );
                                
                            delete_option('wph-interface-save-errors');
                            
                            $this->functions->settings_changed_check_for_cache_plugins();
                        }
                                                
                    do_action('wp-hide/interface/network_admin_notices');   
                    
                }
            
            
            function global_notices()
                {
                    
                    $license_data   =   $this->wph->licence->get_licence_data();
                    
                    if ( isset ( $license_data['network_message'] ) &&   ! empty ( $license_data['network_message'] ) )
                        echo "<div class='notice notice-error'><p>". strip_tags( $license_data['network_message'] , '<b><a>' )  ."</p></div>";    
                    
                }
                
                
            function admin_no_key_notices()
                {
                    
                    if( $this->wph->licence->licence_key_verify()  === TRUE && ! $this->wph->expanded())
                        return;
                    
                    if ( !current_user_can('manage_options'))
                        return;
                                            
                    if(is_multisite())
                        {
                            ?><div class="error fade"><p><?php _e( "WP Hide & Security Enhancer PRO plugin is inactive, please enter your", 'wp-hide-security-enhancer' ) ?> <a href="<?php echo network_admin_url() ?>admin.php?page=network-wp-hide"><?php _e( "Licence Key", 'wp-hide-security-enhancer' ) ?></a>. <?php _e( "Specific functionality and updates are not available.", 'wp-hide-security-enhancer' ) ?></p></div><?php
                        }
                        else
                        {
                               
                            ?><div class="error fade"><p><?php _e( "WP Hide & Security Enhancer PRO plugin is inactive, please enter your", 'wp-hide-security-enhancer' ) ?> <a href="admin.php?page=wp-hide-pro"><?php _e( "Licence Key", 'wp-hide-security-enhancer' ) ?></a>. <?php _e( "Specific functionality and updates are not available.", 'wp-hide-security-enhancer' ) ?></p></div><?php
                        }
                }
                
                
            function notices_hide()
                {
                    
                    if ( isset( $_GET['wph-hide-notice'] ) &&   $_GET['wph-hide-notice']    ==  'rewrite-to-file'  &&  isset( $_GET['_wph_notice_nonce'] )  &&  wp_verify_nonce( $_GET['_wph_notice_nonce'], 'wph_hide_notices_nonce' ))
                        {
                            delete_site_option( 'wph-errors-rewrite-to-file');
                        }
                    
                    if ( isset( $_GET['wph-hide-notice'] ) &&   $_GET['wph-hide-notice']    ==  'nginx-rewrite-to-file'  &&  isset( $_GET['_wph_notice_nonce'] )  &&  wp_verify_nonce( $_GET['_wph_notice_nonce'], 'wph_hide_notices_nonce' ))
                        {
                            delete_site_option( 'wph-errors-nginx_rewrites_to_file');
                        }
                                     
                    /**
                    * Hide Environment notices    
                    */
                    WPH_Environment::notices_hide();
                    
                    
                    do_action('wp-hide/interface/notices_hide');
                    
                }
                
                
            function _setup_interface()
                {
                    
                    include(WPH_PATH . '/include/admin-interfaces/_setup.php');
                    
                }
                
                
            function _settings_interface()
                {
                    include(WPH_PATH . '/include/admin-interfaces/_settings.php');    
                    
                    
                }
                
                
            function _render_network_settings()
                {
                    
                    include(WPH_PATH . '/include/admin-interfaces/_settings.php');    
                    
                }
                
                
            private function _process_import()
                {
                    if  ( !isset($_POST['import_settings'])  ||  empty($_POST['import_settings']))
                        return;
                        
                    $import_data   =    json_decode(  stripslashes ( $_POST['import_settings'] ) , TRUE );
                    
                    array_walk_recursive($import_data, array($this->functions, "filter_htmlspecialchars_decode"));
                    
                    $process_interface_save_errors  =   (array)get_option( 'wph-interface-save-errors');
                    
                    if ( $import_data   ==  FALSE )
                        {
                            $process_interface_save_errors[]    =   array(
                                                                        'type'      =>  'error',
                                                                        'message'   =>  __('Invalid import data.', 'wp-hide-security-enhancer')
                                                                        ); 

                        }
                        else
                        {
                        
                            $blog_id_settings   =   $this->functions->get_blog_id();
                            $settings           =   $this->functions->get_site_settings ( $blog_id_settings );
                            
                            foreach ( $import_data as $key  =>  $value )
                                {
                                    $key    =   trim ( $key );
                                    if ( ! array ( $value ))
                                        $value  =   trim ( $value );
                                    $settings['module_settings'][$key]    =   $value;
                                }
                            $this->functions->update_site_settings($settings, $blog_id_settings);
                            
                            //trigger the settings changed action
                            do_action('wph/settings_changed');
                            
                            $process_interface_save_errors[]    =   array(
                                                                                'type'      =>  'warning',
                                                                                'message'   =>  __('Import data sucesfully processed.', 'wp-hide-security-enhancer')
                                                                                );
                        } 
                    
                    update_option( 'wph-interface-save-errors', $process_interface_save_errors );                    
                    
                }
                
                
            /*private function _process_licence()
                {
                    
                    //check for de-activation
                    if ( isset($_POST['wph_licence_deactivate']) && wp_verify_nonce($_POST['wph_license_nonce'],'wph_licence'))
                        {
                            $process_interface_save_errors  =   (array)get_option( 'wph-interface-save-errors');
                            
                            $licence_data   =   $this->wph->licence->get_licence_data();
                            $licence_key    =   $licence_data['key'];

                            //build the request query
                            $args = array(
                                                'woo_sl_action'         =>  'deactivate',
                                                'licence_key'           =>  $licence_key,
                                                'product_unique_id'     =>  WPH_PRODUCT_ID,
                                                'domain'                =>  WPH_INSTANCE
                                            );
                            $request_uri    = WPH_UPDATE_API_URL . '?' . http_build_query( $args , '', '&');
                            $data           = wp_remote_get( $request_uri );
                            
                            if(is_wp_error( $data ) || $data['response']['code'] != 200)
                                {
                                    $process_interface_save_errors[] =   array(
                                                                                'type'  =>  'error',
                                                                                'message'  =>  __('There was a problem connecting to ', 'wp-hide-security-enhancer') . WPH_UPDATE_API_URL);
                                    update_option( 'wph-interface-save-errors', $process_interface_save_errors );
                                    
                                    return;  
                                }
                                
                            $response_block = json_decode($data['body']);
                            $response_block = $response_block[count($response_block) - 1];
                            $response = $response_block->message;
                            
                            if(isset($response_block->status))
                                {
                                    if($response_block->status == 'success' && $response_block->status_code == 's201')
                                        {
                                            //the license is active and the software is active
                                            $process_interface_save_errors[] = array(
                                                                                    'type'  =>  'updated',
                                                                                    'message'  =>  $response_block->message);
                                            update_option( 'wph-interface-save-errors', $process_interface_save_errors );
                                                                                        
                                            //save the license
                                            $licence_data   =   $this->wph->licence->reset_licence_data( $licence_data );
                                            $licence_data['last_check']   = time();
                                            
                                            $this->wph->licence->update_licence_data( $licence_data );
                                        }
                                        
                                    else //if message code is e104  force de-activation
                                            if ( $response_block->status_code == 'e002' || $response_block->status_code == 'e004' || $response_block->status_code == 'e104' || $response_block->status_code == 'e110')
                                                {                                           
                                                    //save the license
                                                    $licence_data   =   $this->wph->licence->reset_licence_data( $licence_data );
                                                    $licence_data['last_check']   = time();
                                                    
                                                    $this->wph->licence->update_licence_data( $licence_data );
                                                    
                                                    //delete the update transient
                                                    $this->delete_plugin_update_transient();
                                                }
                                        else
                                        {
                                            $process_interface_save_errors[] =   array(  
                                                                                    'type'  =>  'error',
                                                                                    'message'  =>  __('There was a problem deactivating the licence: ', 'wp-hide-security-enhancer') . $response_block->message);
                                            update_option( 'wph-interface-save-errors', $process_interface_save_errors );
                                            
                                            return;
                                        }   
                                }
                                else
                                {
                                    $process_interface_save_errors[] =   array(  
                                                                                    'type'  =>  'error',
                                                                                    'message'  => __('There was a problem with the data block received from ' . WPH_UPDATE_API_URL, 'wp-hide-security-enhancer'));
                                    update_option( 'wph-interface-save-errors', $process_interface_save_errors );
                                    return;
                                }
                                       
                        }   
                    
                    if ( isset($_POST['wph_licence_activate']) && wp_verify_nonce($_POST['wph_license_nonce'],'wph_licence'))
                        {
                            
                            $licence_key = isset($_POST['licence_key'])? sanitize_key(trim($_POST['licence_key'])) : '';

                            if($licence_key == '')
                                {
                                    $process_interface_save_errors  =   (array)get_option( 'wph-interface-save-errors');
                                    $process_interface_save_errors[] =   array(  
                                                                                    'type'  =>  'error',
                                                                                    'message'  =>  __("Licence Key can't be empty", 'wp-hide-security-enhancer'));
                                    update_option( 'wph-interface-save-errors', $process_interface_save_errors );
                                    
                                    return;
                                }
                                
                            //build the request query
                            $args = array(
                                                'woo_sl_action'         => 'activate',
                                                'licence_key'           => $licence_key,
                                                'product_unique_id'     => WPH_PRODUCT_ID,
                                                'domain'                => WPH_INSTANCE
                                            );
                            $request_uri    = WPH_UPDATE_API_URL . '?' . http_build_query( $args , '', '&');
                            $data           = wp_remote_get( $request_uri );
                            if(is_wp_error( $data ) || $data['response']['code'] != 200)
                                {
                                    $process_interface_save_errors  =   (array)get_option( 'wph-interface-save-errors');
                                    $process_interface_save_errors[] =   array(  
                                                                                    'type'  =>  'error',
                                                                                    'message'  =>  __('There was a problem connecting to ', 'wp-hide-security-enhancer') . WPH_UPDATE_API_URL);
                                    update_option( 'wph-interface-save-errors', $process_interface_save_errors );
                                    
                                    return;  
                                }
                                
                            $response_block = json_decode($data['body']);
                            //retrieve the last message within the $response_block
                            $response_block = $response_block[count($response_block) - 1];
                            $response = $response_block->message;
                            
                            if(isset($response_block->status))
                                {
                                    if( $response_block->status == 'success' && ( $response_block->status_code == 's100' || $response_block->status_code == 's101' ) )
                                        {
                                            $process_interface_save_errors  =   (array)get_option( 'wph-interface-save-errors');
                                            //the license is active and the software is active
                                            $process_interface_save_errors[] =   array(  
                                                                                    'type'  =>  'updated',
                                                                                    'message'  =>  $response_block->message);
                                            update_option( 'wph-interface-save-errors', $process_interface_save_errors );
                                            
                                            $licence_data   =   $this->wph->licence->get_licence_data();
                                            
                                            //save the license
                                            $licence_data['key']                = $licence_key;
                                            $licence_data['last_check']         = time();
                                            $licence_data['licence_status']     = isset( $response_block->licence_status ) ?    $response_block->licence_status :   ''  ;
                                            $licence_data['licence_expire']     = isset( $response_block->licence_expire ) ?    $response_block->licence_expire :   ''  ;
                                            $licence_data['activated']          = 's1';
                                            
                                            $this->wph->licence->update_licence_data( $licence_data );
                                            
                                            delete_site_option(base64_decode('d3BoX2V4cGFuZA==' ));
                                            
                                            //delete the update transient
                                            $this->delete_plugin_update_transient();
                                        }
                                        else
                                        {
                                            $process_interface_save_errors  =   (array)get_option( 'wph-interface-save-errors');
                                            $process_interface_save_errors[] =   array(  
                                                                                    'type'  =>  'error',
                                                                                    'message'  =>  __('There was a problem activating the licence: ', 'wp-hide-security-enhancer') . $response_block->message);
                                            update_option( 'wph-interface-save-errors', $process_interface_save_errors );
                                            
                                            return;
                                        }   
                                }
                                else
                                {
                                    $process_interface_save_errors  =   (array)get_option( 'wph-interface-save-errors');
                                    $process_interface_save_errors[] =   array(  
                                                                                    'type'  =>  'error',
                                                                                    'message'  =>  __('There was a problem with the data block received from ' . WPH_UPDATE_API_URL, 'wp-hide-security-enhancer'));
                                    update_option( 'wph-interface-save-errors', $process_interface_save_errors );
                                    
                                    return;
                                }
            
                        }   
                    
                }
                
                
            function delete_plugin_update_transient()
                {
                    global $wpdb;
                    
                    if ( is_multisite() )
                        $mysl_query =   "DELETE FROM " . $wpdb->sitemeta . " WHERE `meta_key` LIKE '%wphide-pro-check_for_plugin_update_%'";
                        else
                        $mysl_query =   "DELETE FROM " . $wpdb->options . " WHERE `option_name` LIKE '%wphide-pro-check_for_plugin_update_%'";
                        
                    $results    =   $wpdb->query( $mysl_query );
                    
                }
                
                */
        } 


?>