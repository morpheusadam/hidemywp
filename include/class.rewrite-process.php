<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_Rewrite_Process
        {
            
            var $wph                            =   '';
            var $functions                      =   '';
            
            /**
            * Only return the rules for view
            * 
            * @var mixed
            */
            var $view_mode                      =   FALSE;
            
            var $processed_data                 =   array();
            var $map_sites_to_processed_data    =   array();
            var $sites_write_check              =   array();
            var $previous_sites_write_check     =   array();
            
            var $sites_using_rewrite_rules      =   array();
            
            var $site_caller                    =   '';
            
            var $require_update_rewrite_file    =   TRUE;
            
            var $_rewrite_data_mod_rewrite      =   array();
            var $_rewrite_data_mod_headers      =   array();
                                  
            function __construct( $view_mode =  FALSE)
                {
                    
                    $this->view_mode    =   $view_mode;
                    
                    global $wph, $blog_id;
                    $this->wph          =   $wph;
                    
                    $this->functions    =   new WPH_functions();
                    
                    $this->site_caller  =   $blog_id;
                    
                    $this->_get_rewrite_data();
                }   
            
            
            
            /**
            * Return the components rules
            *     
            */
            private function get_components_rules()
                {
                    
                    //on uninstall return empty data
                    if ( $this->wph->uninstall  === TRUE )
                        return;
                       
                    $blog_id_settings   =   $this->functions->get_blog_id();
                               
                    if(is_multisite() )
                        {
                            $ms_settings    =   $this->functions->get_site_settings('network');
                            
                            if( is_multisite() )
                                $blog_id_settings   =   'network';   
                        }
                        
                    $all_components  =   array ();
                    
                    foreach($this->wph->modules   as  $module)
                        {
                            //process the module fields
                            $module_components  =   $this->functions->filter_settings(   $module->get_module_components_settings(), TRUE    );
                            
                            foreach ( $module_components    as $item )
                                {
                                    $all_components[]   =   $item;
                                }
                        }
                        
                    if ( is_array( $all_components )   && count( $all_components ) > 0)
                        {
                            usort( $all_components, array($this->functions, 'array_sort_by_rewrite_processing_order'));
                            foreach( $all_components    as  $module_setting)
                                {
                                    
                                    $field_id               =   $module_setting['id'];
                                                                                                       
                                    $_class_instance        =   isset($module_setting['class_instance'])  ?   $module_setting['class_instance'] :   '';
                                    $_callback              =   isset($module_setting['callback_saved'])  ?   $module_setting['callback_saved'] :   '';
                                    $_callback_arguments    =   isset($module_setting['callback_arguments'])  ?   $module_setting['callback_arguments'] :   '';
                                    if(empty($_callback))
                                        $_callback      =   '_callback_saved_'    .   $field_id;
                                    
                                    if (method_exists($_class_instance, $_callback)   && is_callable(array($_class_instance, $_callback)))
                                        {
                                            $component_rewrite_data =   $this->_run_component_callback( $blog_id_settings, $field_id, $_callback, $_callback_arguments, $_class_instance );
                                                                                      
                                            $this->processed_data[]                 =   $component_rewrite_data;
                                            $this->map_sites_to_processed_data[]    =   $blog_id_settings;

                                        }
                                        
                                }
                        }
                        
                    //filter the $processing_data, eliminate any empty/false 
                    foreach ($this->processed_data   as $key =>  $data)
                        {
                            if ( empty ( $data ) )   
                                {
                                    unset( $this->processed_data[ $key ] );
                                    unset( $this->map_sites_to_processed_data[ $key ] );
                                }
                        }
                    
                    $this->_set_sites_using_rwrite_rules();
                    
                }
            
            
            /**
            * Retrieve the rewrite results from component
            * 
            */
            private function _run_component_callback( $blog_id_settings, $field_id, $_callback, $_callback_arguments, $_class_instance )
                {
                    
                    //create a write check number if not exists
                    $this->get_site_write_check_string( );
                    
                    $site_modules_settings      =   $this->functions->get_site_modules_settings( $blog_id_settings );
                    $saved_field_value          =   isset( $site_modules_settings[ $field_id ]) ?   $site_modules_settings[ $field_id ]    :   '';
                    
                    if ( ! empty($_callback_arguments)  &&  is_array($_callback_arguments) &&   count($_callback_arguments) >   0 )
                        $module_mod_rewrite_rules   =   call_user_func_array( array($_class_instance, $_callback), array_values ( array_merge( array( 'field_value'    =>  $saved_field_value), $_callback_arguments ) ) );
                        else
                        $module_mod_rewrite_rules   =   call_user_func(array($_class_instance, $_callback), $saved_field_value);
                        
                    $module_mod_rewrite_rules   =   apply_filters('wp-hide/module_mod_rewrite_rules', $module_mod_rewrite_rules, $_class_instance);   
                    
                    return $module_mod_rewrite_rules;
                    
                }
                
            
            /**
            * Create an array with rewrite data
            * 
            */
            private function _get_rewrite_data()
                {
                    
                    $this->get_components_rules();
                    
                    //post-process the htaccess data    
                    foreach($this->processed_data    as  $response)
                        {
                            if(isset($response['rewrite']) &&  !empty($response['rewrite']))
                                {
                                    if ( isset ( $response['type'] ) &&  $response['type'] == 'header' )
                                        {
                                            $this->_rewrite_data_mod_headers[]    =   $response['rewrite'];    
                                            continue;    
                                        }
                                        
                                    if ( isset ( $response['rewrite'] ) &&  ! empty ( $response['rewrite'] ) )
                                        {
                                            $this->_rewrite_data_mod_rewrite[]   =   $response['rewrite'];
                                        }
                                }
                        }
  
                    //add the write-check if IIS
                    if ( $this->wph->server_web_config  === TRUE )
                        $this->iis_add_additionals();
                        
                    /*
                    if ( $this->wph->server_nginx_config  === TRUE )
                        $this->nginx_add_additionals();
                    */
                    
                }
                
                
            /**
            * Process the list of components rules and return Apache rewrite code
            * 
            */
            function apache_process_rewrite_rules( )
                {
                    
                    $rules  =   "";

                    $_rewrite_text =   $this->apache_get_readable_rewrite_data();
                    
                    global $blog_id;
                                  
                    if  ( $this->view_mode  === TRUE )
                        $this->require_update_rewrite_file  =   FALSE;
                    
                    if( $this->require_update_rewrite_file  === TRUE )
                        {
                            $global_settings    =   $this->functions->get_global_settings ( );                            

                            $home_path      = $this->functions->get_home_path();
                            $htaccess_file  = $home_path . DIRECTORY_SEPARATOR . '.htaccess';
                            
                            //check if .htaccess file exists and is writable
                            if( !   $this->functions->is_writable_htaccess_config_file( $htaccess_file )    ||  $global_settings['self_setup']  ==  'yes' )
                                {
                                    /**
                                    * Mark the variable to know the rules where not updated
                                    * SO KEEP THE OLD SETTING UNTIL REWRITE ARE SAVED !!
                                    */
                                    $this->require_manual_setup_add_markers();
                                    
                                    return TRUE;
                                }
                            
                            //replace markers
                            $_rewrite_text  =   str_ireplace("# BEGIN WP Hide & Security Enhancer",     "", $_rewrite_text);
                            $_rewrite_text  =   str_ireplace("# END WP Hide & Security Enhancer",       "", $_rewrite_text);
                                        
                            $args   =   array(
                                                        'marker'            =>  'WP Hide & Security Enhancer',
                                                        'insertion'         =>  $_rewrite_text,
                                                        'before_marker'     =>  'top',
                                                        );
                            $status =   $this->functions->insert_with_markers( $htaccess_file, $args );
                            if ( $status    === TRUE )
                                {
                                    $_blog_id_settings  =   $this->functions->get_blog_id_setting_to_use();
                                    
                                    $this->functions->save_current_options_list( $_blog_id_settings );
                                   
                                    delete_site_option( 'wph-rewrite-manual-install');
                                    delete_site_option( 'wph-errors-rewrite-to-file');
                                    
                                    if( is_multisite() )                                    
                                        {
                                            $this->functions->delete_all_sites_option( 'wph-rewrite-manual-install' );
                                            $this->functions->delete_all_sites_option( 'wph-errors-rewrite-to-file' );
                                        }

                                        
                                    $this->functions->save_current_options_list( $this->functions->get_blog_id() );
                                }
                                else
                                {
                                    $this->require_manual_setup_add_markers();
                                }
                            
                        }
                              
                }
                
            
            
            /**
            * Process the list of components rules and return IIS7 rewrite code
            * 
            */
            function iis_process_rewrite_rules()
                {

                    $home_path          = $this->functions->get_home_path();
                    $web_config_file    = $home_path . DIRECTORY_SEPARATOR . 'web.config';
                    
                    $global_settings    =   $this->functions->get_global_settings ( );
                    if ( $global_settings['self_setup']  ==  'yes' )
                        {
                            $this->require_manual_setup_add_markers();
                            return;
                        } 
                    
                    //check if writable file
                    if ( $this->functions->is_writable_web_config_file() ===    FALSE )
                        {
                            $this->require_manual_setup_add_markers();
                            return;
                        }   
 
                     //delete all WPH rules
                    $status =   $this->iis_delete_rewrite_rules($web_config_file);
                    if ( $status    === FALSE )
                        {
                            $this->require_manual_setup_add_markers();
                            return;
                        }
                    
                    $status =   $this->iis_add_rewrite_rule( $this->_rewrite_data_mod_rewrite, $web_config_file );
                    if ( $status    === FALSE )
                        {
                            $this->require_manual_setup_add_markers();
                            return;
                        }
                    
                    $_blog_id_settings  =   $this->functions->get_blog_id_setting_to_use();
                                            
                    $this->functions->save_current_options_list( $_blog_id_settings );
                    
                    if(is_multisite() )
                        $ms_settings    =   $this->functions->get_site_settings('network');
                                    
                    
                    delete_site_option( 'wph-rewrite-manual-install');   
                    delete_site_option( 'wph-errors-rewrite-to-file');
                    
                    return TRUE;                    
                    
                }
            
            
            /**
            * Process the list of components rules and return IIS7 rewrite code
            * 
            */
            function nginx_process_rewrite_rules()
                {

                    $this->require_manual_setup_add_markers();

                    return TRUE;                    
                    
                }
            
                
                
            /**
            * Add a rewrite rule within specified file
            * 
            * @param mixed $filename
            */
            function  iis_add_rewrite_rule( $rules, $filename )
                {
                    
                    if (!is_array($rules)    ||  count($rules)   <   1)
                        return false;
                    
                    if ( ! class_exists( 'DOMDocument', false ) ) {
                        return false;
                    }

                    // If configuration file does not exist then we create one.
                    if ( ! file_exists($filename) ) {
                        $fp = fopen( $filename, 'w');
                        fwrite($fp, '<configuration/>');
                        fclose($fp);
                    }
                    
                    $doc = new DOMDocument();
                    $doc->preserveWhiteSpace = false;

                    if ( $doc->load($filename) === false )
                        return false;

                    $xpath = new DOMXPath($doc);
        
                    // Check the XPath to the rewrite rule and create XML nodes if they do not exist
                    $xmlnodes = $xpath->query('/configuration/system.webServer/rewrite/rules');
                    if ( $xmlnodes->length > 0 ) {
                        $rules_node = $xmlnodes->item(0);
                    } else {
                        $rules_node = $doc->createElement('rules');

                        $xmlnodes = $xpath->query('/configuration/system.webServer/rewrite');
                        if ( $xmlnodes->length > 0 ) {
                            $rewrite_node = $xmlnodes->item(0);
                            $rewrite_node->appendChild($rules_node);
                        } else {
                            $rewrite_node = $doc->createElement('rewrite');
                            $rewrite_node->appendChild($rules_node);

                            $xmlnodes = $xpath->query('/configuration/system.webServer');
                            if ( $xmlnodes->length > 0 ) {
                                $system_webServer_node = $xmlnodes->item(0);
                                $system_webServer_node->appendChild($rewrite_node);
                            } else {
                                $system_webServer_node = $doc->createElement('system.webServer');
                                $system_webServer_node->appendChild($rewrite_node);

                                $xmlnodes = $xpath->query('/configuration');
                                if ( $xmlnodes->length > 0 ) {
                                    $config_node = $xmlnodes->item(0);
                                    $config_node->appendChild($system_webServer_node);
                                } else {
                                    $config_node = $doc->createElement('configuration');
                                    $doc->appendChild($config_node);
                                    $config_node->appendChild($system_webServer_node);
                                }
                            }
                        }
                    }

                    //append before other rules
                    $ref_node   =   $xpath->query('/configuration/system.webServer/rewrite/rules/rule[starts-with(@name,\'wordpress\')] | /configuration/system.webServer/rewrite/rules/rule[starts-with(@name,\'WordPress\')]');
                         
                    foreach($rules  as  $rule)
                        {
                            $rule_fragment = $doc->createDocumentFragment();
                            $rule_fragment->appendXML($rule);
                            
                            if($ref_node->length > 0)
                                $rules_node->insertBefore($rule_fragment, $ref_node->item(0));
                                else
                                $rules_node->appendChild($rule_fragment);
                        }

                    $doc->encoding = "UTF-8";
                    $doc->formatOutput = true;
                    saveDomDocument($doc, $filename);
             
                    return true;   
                    
                    
                }
           
           
           
            /**
            * Delete all wph rules within specified filename
            * 
            * @param mixed $filename
            */
            function iis_delete_rewrite_rules( $filename )
                {
                    
                    if ( ! file_exists($filename) )
                        return true;

                    if ( ! class_exists( 'DOMDocument', false ) ) {
                        return false;
                    }

                    $doc = new DOMDocument();
                    $doc->preserveWhiteSpace = false;

                    if ( $doc -> load($filename) === false )
                        return false;
                        
                    $xpath = new DOMXPath($doc);
                    $rules = $xpath->query('/configuration/system.webServer/rewrite/rules/rule[starts-with(@name,\'wph\')]');
                    if ( $rules->length > 0 ) 
                        {
                            foreach($rules  as  $child)
                                {
                                    $parent = $child->parentNode;
                                    $parent->removeChild($child);        
                                }
                            
                            $doc->formatOutput = true;
                            saveDomDocument($doc, $filename);
                        }
                        
                    //delete the comments
                    $rules = $xpath->query('/configuration/system.webServer/rewrite/rules/comment()');
                    if ( $rules->length > 0 ) 
                        {
                      
                            foreach($rules  as  $child)
                                {
                                    $el_html    =   $child->ownerDocument->saveXML( $child );
                                    
                                    if ( ! in_array($el_html, array( '<!--  BEGIN WP Hide & Security Enhancer -->','<!--  END WP Hide & Security Enhancer -->' )))
                                        continue;
                                    
                                    $parent = $child->parentNode;
                                    $parent->removeChild($child);        
                                }
                            
                            $doc->formatOutput = true;
                            saveDomDocument($doc, $filename);
                        }
                       
                    return true;   
                    
                }
            
            
            /**
            * Add WriteCheckStrings to internal _rewrite_data_mod_rewrite variable
            * 
            */
            private function iis_add_additionals()
                {
                    $rule   =   '';
                    
                    //add write_check_string's for sites which use at least a rewrite rule
                    foreach( $this->sites_write_check   as $blog_id =>  $sites_write_check)
                        {
                            
                            if( in_array($blog_id, $this->sites_using_rewrite_rules) )
                                {
                                    $rule  =   "\n" . '<rule name="wph-rewrite-check-' . $blog_id   .'"><!-- WPH_REWRITE_' . $blog_id   .':'. $this->sites_write_check[ $blog_id ] .' --></rule>';
                                    array_unshift($this->_rewrite_data_mod_rewrite, $rule);
                                }
                                else
                                $this->sites_write_check[ $blog_id ]    =   '';
                                                                
                            //update the WriteCheckString if different from previous
                            if ( $this->sites_write_check[ $blog_id ]   !=  $this->previous_sites_write_check[ $blog_id ] )
                                {
                                    $site_settings                          =   $this->functions->get_site_settings( $blog_id );
                                    $site_settings['write_check_string']    =   $this->sites_write_check[ $blog_id ];
                                    
                                    $this->functions->update_site_settings( $site_settings, $blog_id );
                                }
                        }
                        
                    array_unshift(  $this->_rewrite_data_mod_rewrite, "\n\n\n" . '<!--  BEGIN WP Hide & Security Enhancer -->');
                    array_push(     $this->_rewrite_data_mod_rewrite, "\n" . '<!--  END WP Hide & Security Enhancer -->' . "\n\n\n");    
                }
                
                
            private function nginx_add_additionals()
                {
                    
                    if ( is_multisite() &&  defined('SUBDOMAIN_INSTALL')    &&  SUBDOMAIN_INSTALL   === FALSE )
                        {
                                
                            $sites_slug_map =   implode("|", $this->get_sites_slug_map());
                            
                            /*
                            $wp_rewrite_data    =   array();
                            $wp_rewrite_data[]  =   "\n\n       #WordPress MultiSite Subdirectory rules";
                            $wp_rewrite_data[]  =   '       if (!-e $request_filename) {';
                            $wp_rewrite_data[]  =   '           rewrite /wp-admin$ $scheme://$host$uri/ permanent;';
                            $wp_rewrite_data[]  =   '           rewrite ^(/'.$sites_slug_map.')?(/wp-(content|admin|includes).*) $2 last;';
                            $wp_rewrite_data[]  =   '           rewrite ^(/'.$sites_slug_map.')?(/.*\.php) $2 last;';
                            $wp_rewrite_data[]  =   "        }";
                            $wp_rewrite_data[]  =   "       #End WordPress rules\n\n";
                            */
                            
                            $wp_rewrite_data    =   array();
                            $wp_rewrite_data[]  =   "\n\n       map $request_uri $file_path_exists {";
                            $wp_rewrite_data[]  =   '           ~^(/'.$sites_slug_map.')?(?<file_path>(/.*\.php))  $file_path;';
                            $wp_rewrite_data[]  =   '       }';
                            
                            $this->rewrite_data     =   array_merge( $wp_rewrite_data, $this->_rewrite_data_mod_rewrite );
                        }
                    
                    array_unshift(  $this->_rewrite_data_mod_rewrite, "\n" . '       # BEGIN WP Hide & Security Enhancer');
                    
                    
                    //add the php include block if being used on any rules
                    //++++++ check if there's a otion to use that
                    $custom_data    =   array();
                    
                    $custom_data[]  =   "\n\n" .'       location @php_include {';
                    $custom_data[]  =   "\n           #REPLACE with your server rules, usualy found at    location ~ \.php\$ {     ";
                    $custom_data[]  =   '           include snippets/fastcgi-php.conf;';
                    $custom_data[]  =   '           fastcgi_pass unix:/run/php/php7.0-fpm.sock;';
                    $custom_data[]  =    "           #END replace\n";
                    $custom_data[]  =   '       }';
                             
                    $this->_rewrite_data_mod_rewrite     =   array_merge( $this->_rewrite_data_mod_rewrite, $custom_data );
                    
                    array_push(     $this->_rewrite_data_mod_rewrite, "\n" . '       # END WP Hide & Security Enhancer' . "\n");
                }
            
            
            /**
            * Return an array of sites slug
            * 
            */
            public function get_sites_slug_map( )
                {
                    global $wph;
                    
                    if ( ! is_multisite() )
                        return array();
                    
                    $sites_slug_map =   array();
                    
                    $network_sites  =   $this->functions->ms_get_plugin_active_blogs();     
                    foreach($network_sites   as  $site_to_process)
                        {
                            $path   =   $site_to_process->path;
                            $path   =   trim($path, '/' );
                            
                            if ( !empty($path) )
                                $sites_slug_map[]   =   $path;
                        }
                        
                    return $sites_slug_map;   
                }
            
            
            /**
            * Return a WriteCheckString hash for current site, or generate one if not exists
            * 
            */
            function get_site_write_check_string()
                {
                    
                    global $blog_id;
                    
                    //Always change for caller site
                    //Always generate when called by SuperAdmin
                    if ( $this->view_mode  === FALSE     &&  ( $this->site_caller ==  $blog_id    ||  ( is_multisite() &&  is_network_admin() ) ) )
                        {
                            $write_check_string     =   $this->generate_site_write_check( );
                            
                            $site_settings                      =   $this->functions->get_current_site_settings();
                            if ( isset($site_settings['write_check_string'])    &&  ! empty ($site_settings['write_check_string']) )
                                $this->previous_sites_write_check[ $blog_id ]   =   $site_settings['write_check_string'];
                                else
                                $this->previous_sites_write_check[ $blog_id ]   =   '';
                            
                        }
                        else
                        {
                            //atempt to retrive from settings
                            $site_settings                      =   $this->functions->get_site_settings( $blog_id );
                            
                            if ( isset($site_settings['write_check_string'])    &&  ! empty ($site_settings['write_check_string']) )
                                {
                                    $write_check_string     =   $site_settings['write_check_string'];
                                    $this->previous_sites_write_check[ $blog_id ]   =   $write_check_string;
                                }
                                else
                                {
                                    $write_check_string     =   $this->generate_site_write_check( );
                                    $this->previous_sites_write_check[ $blog_id ]   =   '';
                                }
                               
                        }
                        
                    $this->sites_write_check[ $blog_id ]      =   $write_check_string;
                    
                    return $write_check_string;
                       
                }
            
            
            
            /**
            * Generate a unique write_check hash for current site, if not exists
            * 
            * @param mixed $sites_write_check
            */
            function generate_site_write_check(  )
                {
                    $settings                      =   $this->functions->get_current_site_settings();
                        
                    return hash( 'crc32', json_encode( $settings['module_settings'] ), FALSE );
          
                }
            
            
            /**
            * Create a list of sites which use at least a rewrite rule
            * 
            */
            private function _set_sites_using_rwrite_rules()
                {
                    
                    $this->sites_using_rewrite_rules    =   array_values( array_unique( $this->map_sites_to_processed_data ) );
                    
                }
                
            
            
            
            
            /**
            * Return readable text formated rewrite rules for current server type
            * 
            */
            function get_readable_rewrite_data()
                {
                    
                    if ( $this->wph->server_htaccess_config  === TRUE )
                        return $this->apache_get_readable_rewrite_data();
                        
                    if ( $this->wph->server_web_config  === TRUE )
                        return $this->iis_get_readable_rewrite_data();
                        
                    if ( $this->wph->server_nginx_config  === TRUE )
                        return $this->nginx_get_readable_rewrite_data();
                    
                }
            
                
            /**
            * Return a readable text of rewrite data for Apache
            * 
            */
            function apache_get_readable_rewrite_data()
                {
                    //on uninstall return empty data
                    if ( $this->wph->uninstall  === TRUE )
                        return;
                        
                    $rules  =   '';

                    /**
                    * Process the mod_rewrite rules
                    * Add write_check_string's for sites which use at least a rewrite rule
                    */
                    foreach( $this->sites_write_check   as $blog_id =>  $sites_write_check)
                        {
                            
                            if( in_array($blog_id, $this->sites_using_rewrite_rules) )
                                {
                                    //$rules  .=  "#WriteCheckString-" . $blog_id . ":" . $this->sites_write_check[ $blog_id ] . "\n";
                                    $rules  .=  "#RewriteRule .* - [E=WPH_REWRITE_" . $blog_id .":" . $this->sites_write_check[ $blog_id ] ."]" . "\n";
                                }
                                else
                                $this->sites_write_check[ $blog_id ]    =   '';
                                                                
                            //update the WriteCheckString if different from previous
                            if ( $this->sites_write_check[ $blog_id ]   !=  $this->previous_sites_write_check[ $blog_id ] )
                                {
                                    $site_settings                          =   $this->functions->get_site_settings( $blog_id );
                                    $site_settings['write_check_string']    =   $this->sites_write_check[ $blog_id ];
                                    
                                    $this->functions->update_site_settings( $site_settings, $blog_id );
                                }
                        }
                      
                    if(count($this->_rewrite_data_mod_rewrite)   >   0)
                        {
                            foreach($this->_rewrite_data_mod_rewrite as  $_htaccess_data_line)   
                                {
                                    $rules .=   "\n"    .   $_htaccess_data_line;
                                }                            
                        }
                        
                    if ( is_multisite() )
                        {
                            //subdirectory only
                            if ( defined ( 'SUBDOMAIN_INSTALL') &&  SUBDOMAIN_INSTALL   === FALSE )
                                {
                                    $rules  =   'RewriteCond %{REQUEST_URI} ^/([_0-9a-zA-Z-]+/)(wp-(content|admin|includes).*)$ [OR]' . "\n"
                                              . 'RewriteCond %{REQUEST_URI} ^/([_0-9a-zA-Z-]+/)(.*\.php)$' . "\n"
                                              . 'RewriteRule .* - [E=WPH_IS_SUBSITE:ON]'. "\n" . $rules;
                                }
                                        
                            //subdomain
                        }
                            
                    $rules      =   apply_filters('wp-hide/mod_rewrite_rules', $rules, 'apache');
                    
                    /**
                    * Process the mod_headers
                    */
                    $headers_rules  =   '';
                    if ( count ( $this->_rewrite_data_mod_headers ) > 0 )
                        {
                            foreach( $this->_rewrite_data_mod_headers as  $_htaccess_data_line)   
                                {
                                    $headers_rules .=   $_htaccess_data_line;
                                }
                        }
                    $headers_rules      =   apply_filters('wp-hide/mod_headers_rules', $headers_rules, 'apache');
                    
                    
                    $home_root  =   untrailingslashit ( $this->functions->get_domain_home_path() );
                    if ( empty ( $home_root ) )
                        $home_root  =   '/';
                    
                    $rules  =   "# BEGIN WP Hide & Security Enhancer \n"
                                . "<IfModule mod_rewrite.c> \n" 
                                . "RewriteEngine On \n"
                                . "RewriteBase ". $home_root ." \n"
                                . $rules
                                . "\n"
                                . "</IfModule> \n"
                                . "# END WP Hide & Security Enhancer \n";
                    
                    
                    if ( ! empty ( $headers_rules ) )
                        $rules .= "<IfModule mod_headers.c>"
                                  . $headers_rules . "\n"
                                  . '</IfModule>';
                                                    
                    return $rules;
                }
                
            
            /**
            * Return a readable text of rewrite data for IIS
            * 
            */
            function iis_get_readable_rewrite_data()
                {
                    //on uninstall return empty data
                    if ( $this->wph->uninstall  === TRUE )
                        return;
                    
                    $rules  =   '';
                       
                    if(count($this->_rewrite_data_mod_rewrite)   >   0)
                        {
                            foreach($this->_rewrite_data_mod_rewrite as  $rewrite_line)   
                                {
                                    $rules .=   "\n"    .   $rewrite_line;
                                }                            
                        }
                    
                    $rules      =   apply_filters('wp-hide/mod_rewrite_rules', $rules, 'iis');
                        
                    return $rules;   
                    
                }
                
            
            /**
            * Return a readable text of rewrite data for Nginx
            * 
            */
            function nginx_get_readable_rewrite_data()
                {
                    $readable_rules  =   array();
                    
                    //create one dimensional array with all nested data
                    $rewrite_rules  =   call_user_func_array('array_merge', $this->_rewrite_data_mod_rewrite);
                    
                    $_replacement_tags  =   array();                    
                    
                    $global_settings    =   $this->wph->functions->get_global_settings ( );
                    $ms_settings        =   $this->wph->functions->get_site_settings('network');
                               
                    $rules_map                  =   array();
                    $processed_rules_map        =   array();
                    
                    //create a list of rules types
                    foreach( $rewrite_rules as  $key    =>  $rule_block )
                        {
                            if ( ! isset($rules_map[$rule_block['type']]) )
                                {
                                    $rules_map[$rule_block['type']][]   =   $rule_block;    
                                }
                                else
                                $rules_map[$rule_block['type']][]     =   $rule_block;
                        }
                        
                    foreach ( $rules_map    as  $rule_type  =>  $rules_list )
                        {
                            
                            //build the rules
                            while( count ($rules_list) > 0)
                                {
                                    $current_rule   =   '';
                                    reset($rules_list);
                                    $current_rule   =   current ( $rules_list );
                                    array_shift($rules_list);
                                    
                                    $similar_rules  =   array( $current_rule );
                                    
                                    if ( count ($rules_list ) > 0 )
                                        {
                                            foreach($rules_list as  $key    =>  $rule_block)
                                                {
                                                    if ( trim($current_rule['description'])  !=  trim($rule_block['description']) )
                                                        continue;
                                                    
                                                    $similar_rules[]    =   $rule_block;
                                                    
                                                    unset($rules_list[$key]);
                                                }
                                        }
                                    
                                    $processed_rules_map[$rule_type][ $current_rule['description'] ]    =   $similar_rules;
                                }
                        }
                    
                    //postprocess the data
                    foreach ( $processed_rules_map  as  $rule_type  =>  $all_rules_blocks )
                        {
                            $post_processed_rules   =   array();
                            
                            foreach ( $all_rules_blocks as  $rule_description   =>  $similar_rules_blocks )
                                {
                                    $readable_block =   '';
                                    switch($rule_type)
                                        {
                                            case 'map'    :
                                                                            reset($similar_rules_blocks);
                                                                            $current_rule   =   current ( $similar_rules_blocks );
                                                                            
                                                                            $readable_block .=  '       map ' . trim($current_rule['description']) . ' {' ."\n";
                                                                            $readable_block .=  '           __WPH_RULES_DATA__' ."\n";
                                                                            $readable_block .=  '       }' ."\n\n";
                                                                                                                              
                                                                            //prepare the rewrite rules
                                                                            //Create a list with all unique rules
                                                                            $unique_sites_rules     =   array();
                                                                            $unique_sites_rules_raw =   array();
                                                                            foreach ( $similar_rules_blocks as  $site_rules )
                                                                                {
                                                                                    foreach($site_rules['data'] as  $rule_data)
                                                                                        if ( ! in_array(trim($rule_data), $unique_sites_rules))
                                                                                            {
                                                                                                $unique_sites_rules[]   =   trim($rule_data);
                                                                                                $unique_sites_rules_raw[]   =   $rule_data;
                                                                                            }
                                                                                }
                                                                                
                                                                            $__WPH_RULES_DATA__ =   array();
                                                                            //build the rules and make the tag replacements
                                                                            foreach($unique_sites_rules as  $key    =>  $unique_sites_rule)
                                                                                {
                                                        
                                                                                    $_replacement_tags['__WPH_SITES_SLUG__']    =   $this->_get_slug_map( $this->get_sites_slug_map(), array( 'append_slash'   =>  TRUE ) );   
                                                                                    
                                                                                    $rule_data  =   $unique_sites_rules_raw[$key];
                                                                                    foreach ($_replacement_tags   as  $tag  =>  $replacement )
                                                                                        $rule_data =   str_replace($tag, $replacement, $rule_data);
                                                                                    
                                                                                    $__WPH_RULES_DATA__[]   =   $rule_data;
                                                                                }
                                                                            
                                                                            //put the rules in the main block
                                                                            $readable_block =   str_replace('__WPH_RULES_DATA__', implode( "\n" , $__WPH_RULES_DATA__) , $readable_block);
                                                                            
                                                                            break;
                                            
                                            case 'default_variables'    :
                                                                            reset($similar_rules_blocks);
                                                                            $current_rule   =   current ( $similar_rules_blocks );
                                                                            
                                                                            $readable_block .=  '       ' . trim($current_rule['description']). "\n";
                                                                            
                                                                            $_replacement_tags['__WPH_SITES_SLUG__']    =   $this->_get_slug_map( $this->get_sites_slug_map(), array( 'append_slash'   =>  TRUE ) );
                                                                            
                                                                            foreach ($_replacement_tags   as  $tag  =>  $replacement )
                                                                                $readable_block =   str_replace($tag, $replacement, $readable_block);
                                                                            
                                                                            break;
                                            
                                            case 'location'  :
                                            case 'header'  :
                                                                
                                                                reset($similar_rules_blocks);
                                                                $current_rule   =   current ( $similar_rules_blocks );
                                                                
                                                                if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                                                    $readable_block .=  '       location ' . trim($current_rule['description']) . ' {' ."\n";
                                                                    
                                                                $readable_block .=  '           __WPH_RULES_DATA__' ."\n";
                                                                
                                                                if( $global_settings['nginx_generate_simple_rewrite']   !=  'yes' )
                                                                    $readable_block .=  '       }' ."\n\n";
                                                                
                                                                //rpalce the tags foudn so far
                                                                /**
                                                                * DO for multisite individual config ONLY
                                                                * 
                                                                * @var mixed
                                                                */
                                                                
                                                                $_replacement_tags['__WPH_SITES_SLUG__']    =   $this->_get_slug_map( $this->get_sites_slug_map(), array( 'append_slash'   =>  TRUE, 'regex_quantifier' =>  TRUE ) );
                                                                
                                                                foreach ($_replacement_tags   as  $tag  =>  $replacement )
                                                                    $readable_block =   str_replace($tag, $replacement, $readable_block);
                                                                
                                                                //prepare the rewrite rules
                                                                //Create a list with all unique rules
                                                                $unique_sites_rules     =   array();
                                                                $unique_sites_rules_raw =   array();
                                                                foreach ( $similar_rules_blocks as  $site_rules )
                                                                    {
                                                                        foreach($site_rules['data'] as  $rule_data)
                                                                            if ( ! in_array(trim($rule_data), $unique_sites_rules))
                                                                                {
                                                                                    $unique_sites_rules[]   =   trim($rule_data);
                                                                                    $unique_sites_rules_raw[]   =   $rule_data;
                                                                                }
                                                                    }
                                                                    
                                                                $__WPH_RULES_DATA__ =   array();
                                                                //build the rules and make the tag replacements
                                                                foreach($unique_sites_rules as  $key    =>  $unique_sites_rule)
                                                                    {

                                                                        $rule_data  =   $unique_sites_rules_raw[$key];
                                                                        
                                                                        if ( is_multisite() &&  defined('SUBDOMAIN_INSTALL')    &&  SUBDOMAIN_INSTALL   === false )
                                                                            $rule_data  =   str_replace( '__WPH_SITES_SLUG__/', '/__WPH_SITES_SLUG__', $rule_data );
                                                                        
                                                                        $_replacement_tags['__WPH_SITES_SLUG__']    =   $this->_get_slug_map( $this->get_sites_slug_map(), array( 'append_slash'   =>  TRUE, 'regex_quantifier' =>  TRUE ) );   
                                                                        
                                                                        //any regex match
                                                                        $founds =   preg_match_all('/__WPH_REGEX_MATCH_([\d]+)__/i', $rule_data, $_regex_match);
                                                                        if ( ! empty($founds))
                                                                            {
                                                                                foreach ($_regex_match   as  $_regex_match_block)
                                                                                    {
                                                                                        foreach ($_regex_match_block    as  $_regex_key    =>  $_regex_match_item)
                                                                                            {
                                                                                                $_regex_match_number    =   $_regex_match[1][$_regex_key];
                                                                                                if ( strpos($_replacement_tags['__WPH_SITES_SLUG__'], "(" ) === FALSE )
                                                                                                    $_regex_match_number--;
                                                                                                    
                                                                                                $_replacement_tags[$_regex_match[0][$_regex_key]]    =   $_regex_match_number;
                                                                                            }
                                                                                        
                                                                                        break;
                                                                                    }
                                                                            }
                                                                        
                                                                        foreach ($_replacement_tags   as  $tag  =>  $replacement )
                                                                            $rule_data =   str_replace($tag, $replacement, $rule_data);
                                                                        
                                                                        $__WPH_RULES_DATA__[]   =   $rule_data;
                                                                    }
                                                                
                                                                //put the rules in the main block
                                                                $readable_block =   str_replace('__WPH_RULES_DATA__', implode( "\n" , $__WPH_RULES_DATA__) , $readable_block);
                                                                                                                            
                                                                break;   
                                            
                                            case 'firewall' :
                                                                foreach ( $similar_rules_blocks as  $site_rules )
                                                                    {
                                                                        foreach($site_rules['data'] as  $rule_data)
                                                                            {
                                                                                $readable_block .=  $rule_data;
                                                                            }
                                                                    }
                                                                
                                                                break;
                                                                
                                            case 'firewall_conditionals' :
                                                                foreach ( $similar_rules_blocks as  $site_rules )
                                                                    {
                                                                        foreach($site_rules['data'] as  $rule_data)
                                                                            {
                                                                                $readable_block .=  $rule_data;
                                                                            }
                                                                    }
                                                                
                                                                break;
                                        }
                                        
                                    
                                    $post_processed_rules[]   =   $readable_block;
                                }
                            
                            $type_readable_rules    =   '';

                            if(count($post_processed_rules)   >   0)
                                {
                                    foreach($post_processed_rules as  $rewrite_line)   
                                        {
                                            $type_readable_rules .=   "\n"    .   $rewrite_line;
                                        }                            
                                }
                            
                            
                            //trim extra lines if simple rules
                            if( $global_settings['nginx_generate_simple_rewrite']   ==  'yes' )
                                {
                                    $type_readable_rules    =   preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $type_readable_rules);
                                }
                            
                            $readable_rules[ $rule_type ]   =   "\n# BEGIN WP Hide & Security Enhancer"  .   $type_readable_rules    .   "# END WP Hide & Security Enhancer";

                        }
                        
                        
                    $readable_rules      =   apply_filters('wp-hide/mod_rewrite_rules', $readable_rules, 'nginx');
     
                    return $readable_rules;   
                    
                }
                
            
            /**
            * Return a repalcement for a tag
            * 
            * @param mixed $rules_sites_slug_map
            */
            private function _get_slug_map( $rules_sites_slug_map, $attr   =   array() )
                {
                    
                    $defaults   = array (
                                            'append_slash'      =>      FALSE,
                                            'regex_quantifier'  =>      FALSE
                                        );
                                        
                    // Parse incoming $args into an array and merge it with $defaults
                    $attr   =   wp_parse_args( $attr, $defaults );
                    
                    
                    $map_text   =   '';
                    
                    $rules_sites_slug_map   =   array_unique($rules_sites_slug_map);
                    
                    $regex_optional =   "";
                    
                    if( in_array("", $rules_sites_slug_map)     ||  $attr['regex_quantifier'] )
                        $regex_optional =   "?";
                        
                    $rules_sites_slug_map   =   array_filter($rules_sites_slug_map);
    
                    if ( is_multisite() )
                        {
                            if ( defined('SUBDOMAIN_INSTALL')   &&  SUBDOMAIN_INSTALL   === false )
                                $map_text   .=  '([_0-9a-zA-Z-]+/)' . $regex_optional; 
                                else
                                $map_text   .=  '';                                    
                        }
                        else
                        {
                            $map_text   .=  '';    
                        }
              
                    
                    return $map_text;
                    
                }
            
                
            static public function require_manual_setup_add_markers()
                {
                    
                    $rewrite_process                    =   new WPH_Rewrite_Process( TRUE );
                    $readable_processed_rewrite         =   $rewrite_process->get_readable_rewrite_data();
                    $readable_processed_rewrite_hash    =   md5 ( json_encode ( $readable_processed_rewrite ) );
                    
                    //check if the previous hash
                    $previous_readable_processed_rewrite_hash    =   get_site_option ( 'wph_rewrite_hash' );
                    if ( ! empty ( $readable_processed_rewrite_hash )   && $previous_readable_processed_rewrite_hash    ==  $readable_processed_rewrite_hash  )
                        return;
                    
                    update_site_option( '__wph_transient_rewrite_hash', $readable_processed_rewrite_hash );
                    
                    /**
                    * Mark the variable to know the rules where not updated
                    * SO KEEP THE OLD SETTING UNTIL REWRITE ARE SAVED !!
                    */
                    update_site_option( 'wph-rewrite-manual-install', 'yes');
                    
                }
            
        }


?>