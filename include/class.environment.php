<?php

     if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
     
     class WPH_Environment
        {
            
            var $wph                            =   '';
            var $functions                      =   '';
        
            var $environment_variable           =   array();
            
                                  
            function __construct()
                {
                    global $wph;

                    $this->wph          =   $wph;
                    $this->functions    =   new WPH_functions();
                    
                    $this->setup_variable();
                    
                }
            
                
            /**
            * create the environment content variable
            * 
            */
            private function setup_variable()
                {
                    global $blog_id; 
                    
                    $settings           =   $this->functions->get_site_settings ( 'network' );
                    
                    $templates_data =   array();
                    if(is_multisite())
                        {
                            $sites_to_process   =   $this->functions->ms_get_plugin_active_blogs();
                            foreach($sites_to_process   as  $site_to_process)        
                                {
                                    switch_to_blog( $site_to_process->blog_id );
                                                            
                                    $data  =  $this->functions->get_site_template_data();   
                                    
                                    $templates_data[$site_to_process->blog_id]    =   $data;
                                                 
                                    restore_current_blog();   
                                    
                                }
                        }
                        else
                        {
                            $templates_data[$blog_id]    =   $this->functions->get_site_template_data();   
                        }
                    
                    
                    $this->environment_variable['themes'] =   $templates_data;
                    
                    if(is_array($this->environment_variable['themes'])    &&  count ($this->environment_variable['themes'] ) > 0 )
                        {
                            foreach( $this->environment_variable['themes'] as  $_blog_id   =>  $data )
                                {
                                    if (is_multisite())
                                        $settings   =   $this->functions->get_site_settings( 'network' );
                                        else
                                        $settings   =   $this->functions->get_site_settings( $_blog_id );
                                    
                                    $this->environment_variable['themes'][$_blog_id]['main']['mapped_name']  =   isset($settings['module_settings']['new_theme_path_'    .   $data['main']['folder_name'] ])  ?   $settings['module_settings']['new_theme_path_' .   $data['main']['folder_name'] ]  :   '';
                                        
                                    if( $this->environment_variable['themes'][$_blog_id]['use_child_theme']    === TRUE )
                                        $this->environment_variable['themes'][$_blog_id]['child']['mapped_name']  =   isset ( $settings['module_settings']['new_theme_child_path_' .   $data['child']['folder_name']] )    ?   $settings['module_settings']['new_theme_child_path_' .   $data['child']['folder_name']]    :   '';
                                }
                        }
                    
                    //set the allowe paths
                    $this->environment_variable['allowed_paths']                    =   apply_filters('wp-hide/environment_file/allowed_paths', array( trim ( wp_normalize_path ( $_SERVER['DOCUMENT_ROOT'] )   .   untrailingslashit ( $this->functions->get_domain_site_path() ) .   $this->wph->default_variables['templates_directory']  ) ) );
                    
                    $this->environment_variable['wordpress_directory']              =   $this->wph->default_variables['wordpress_directory'];
                    $this->environment_variable['site_relative_path']               =   $this->wph->default_variables['site_relative_path'];
                    $this->environment_variable['cache_path']                       =   wp_normalize_path  ( WPH_CACHE_PATH );
                                  
                }
                
            
            /**
            * Check if the environment file exists and include correct data
            * 
            */
            public function is_correct_environment()
                {
                    
                    $wp_upload_dir              =   wp_upload_dir();
                    $environment_variable       =   '';
                            
                    if( file_exists(  $wp_upload_dir['basedir'] . '/wph/environment.php' ) )
                        {
                            require_once( $wp_upload_dir['basedir'] . '/wph/environment.php' );
                        }
                        else
                        return FALSE;
                                            
                    //if nothing has changed exit
                    if ( $environment_variable   ==  json_encode($this->environment_variable) )
                        {
                            //clear any notices regarding this file which is not correct
                            self::delete_all_notices();
                            
                            return TRUE;
                        }
                        
                    return FALSE;
                    
                }
                
            
            /**
            * Delete the notices
            * 
            */
            public static function notices_hide()
                {
                    if ( isset( $_GET['wph-hide-notice'] ) &&   $_GET['wph-hide-notice']    ==  'environment'  &&  isset( $_GET['_wph_notice_nonce'] )  &&  wp_verify_nonce( $_GET['_wph_notice_nonce'], 'wph_hide_notices_nonce' ))
                        {
                            if ( is_network_admin() )
                                delete_site_option('wph-errors-environment');
                            else
                                delete_option( 'wph-errors-environment');   
                        }
                }
                
                
            public static function delete_all_notices()
                {
                    delete_site_option('wph-errors-environment');
                    
                    //+++++ Delete this notice on all sites!!
                    delete_option( 'wph-errors-environment');    
                }
      
            
            function get_environment_content()
                {
                    
                    ob_start();
                    
                    echo "<?php ";
                    echo "if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly\n\n";
                    echo '$environment_variable = \''. json_encode($this->environment_variable) .'\';';
                    echo " ?>";
                    
                    $file_data = ob_get_contents();
                    ob_end_clean();
                    
                    return $file_data;    
                    
                }
            
            function write_environment()
                {
                    
                    global $wp_filesystem;

                    if (empty($wp_filesystem)) 
                        {
                            require_once (ABSPATH . '/wp-admin/includes/file.php');
                            WP_Filesystem();
                        }

                    $file_data  =   $this->get_environment_content();
                    
                    $wp_upload_dir              =   wp_upload_dir();
                    
                    if ( ! is_dir( $wp_upload_dir['basedir'] . '/wph/' ) ) 
                        {
                           wp_mkdir_p( $wp_upload_dir['basedir'] . '/wph/' );
                        } 
                    
                    if ( is_wp_error( $wp_filesystem->errors ) && $wp_filesystem->errors->get_error_code() )
                        {
                            
                            $message  = __('<b>WP Hide</b> - Unable to create environment static file. The system returned the following error: ', 'wp-hide-security-enhancer') . implode("," , (array)$wp_filesystem->errors->get_error_messages() );
                                                                                   
                            set_transient( 'wph-process_set_static_environment_errors', $process_interface_save_errors, HOUR_IN_SECONDS );
                            
                            return;   
                        }
                        
                    if( ! $wp_filesystem->put_contents( $wp_upload_dir['basedir'] . '/wph/environment.php' , $file_data , FS_CHMOD_FILE) ) 
                        {
                            $environments_errors    =   array();
                            
                            $message    =   '<b>WP Hide</b> - ' . __('Unable to create environment data at ', 'wp-hide-security-enhancer') . $wp_upload_dir['basedir'] . '/wph/environment.php ' . __('Is file writable', 'wp-hide-security-enhancer') . '? ' . __('Check with Setup menu item or contact server administrator.', 'wp-hide-security-enhancer');
                            
                            //save the message for superadmin
                            if ( is_multisite() )
                                update_site_option( 'wph-errors-environment', $message);
                                
                            
                            $message    .=   '<br /><b>Remove description header from Style file</b> and <b>Child - Remove description header from Style file</b> ' . __('will not work correctly, so where turned off.', 'wp-hide-security-enhancer');
                            
                            update_option( 'wph-errors-environment', $message);
                            
                            global $blog_id; 
                            
                            $settings   =   $this->functions->get_site_settings( $blog_id );
                            
                            //disable certain options
                            $settings['module_settings']['style_file_clean']          =   'no';
                            $settings['module_settings']['child_style_file_clean']    =   'no';
                            
                            //save the new options
                            $this->functions->update_site_settings( $settings, $blog_id );
                            
                            //regenerate permalinks
                            $this->wph->settings_changed();
                            
                        }    
                        else
                        {
                            self::delete_all_notices();
                        }
                    
                    
                }
                
        }   
            



?>