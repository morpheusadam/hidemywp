<?php

    /**
    * Compatibility     : Shield Security
    * Introduced at     : 9.2.1
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    use FernleafSystems\Wordpress\Plugin\Shield;
    use FernleafSystems\Wordpress\Plugin\Shield\Modules\LoginGuard;
    use FernleafSystems\Wordpress\Services\Services;
    
    class WPH_conflict_handle_wp_simple_firewall
        {
            
            var $wph;
            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                        
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    //mark as being loaded
                    define('WPH_conflict_handle_wp_simple_firewall', TRUE );
                    
                    add_action('plugins_loaded',    array( $this, 'on_plugins_loaded' ), 5);
                    
                }
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'wp-simple-firewall/icwp-wpsf.php' ))
                        return TRUE;
                        else
                        return FALSE;
    
                }
                
            
            public function on_plugins_loaded()
                {
                            
                    $oICWP_Wpsf_Controller =   Shield\Controller\Controller::GetInstance( WP_PLUGIN_DIR . '/wp-simple-firewall/src/login_protect.php' );
                                                      
                    //check if custom login is active
                    if( method_exists( $oICWP_Wpsf_Controller->oFeatureHandlerLoginProtect, 'isCustomLoginPathEnabled')  &&  $oICWP_Wpsf_Controller->oFeatureHandlerLoginProtect->isCustomLoginPathEnabled())
                        return FALSE;
                        else
                    //version 10.0.3 and later 
                    if( method_exists( $oICWP_Wpsf_Controller->oFeatureHandlerLoginProtect, 'getCustomLoginPath')  &&  $oICWP_Wpsf_Controller->oFeatureHandlerLoginProtect->getCustomLoginPath() != '' )
                        return FALSE;
                    
                    
                    global $wph;
                        
                    $new_login  =   $this->wph->functions->get_site_module_saved_value('new_wp_login_php',  $this->wph->functions->get_blog_id_setting_to_use());
                    if ( empty ( $new_login ) )
                        return FALSE;
                    
                    add_action('admin_notices',                                         array( $this, 'admin_notice' ));   
                    
                }
                
                
            static function admin_notice()
                {
                    global $current_user ;
                    
                    $user_id = $current_user->ID;
                    
                    //only for admins
                    if (    !   current_user_can( 'install_plugins' ) )
                        return;
                                            
                    ?>
                    <div id="WPH_conflict_handle_wp_simple_firewall_login" class="error notice">
                        <p>
                            <?php _e('<b>Conflict notice</b>: <b>Shield Security</b> - Login Protection -> Hide Login -> use similar functionality as to WP Hide plugin - Admin Login Url change.  ', 'wp-hide-security-enhancer'); ?>
                        </p>
                    </div>
                    
                    <?php
                    
                }

                
        }

    new WPH_conflict_handle_wp_simple_firewall();    
        
?>