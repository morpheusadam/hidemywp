<?php   
        
        if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
        
        global $wph;
        
        $licence_data   =   $wph->licence->get_licence_data();
?>

            <div id="form_data">
                <h3><?php _e( "Licence", 'wp-hide-security-enhancer' ) ?></h3>
    
                    <form name="form" method="post">    
                        <?php wp_nonce_field('wph_licence','wph_license_nonce'); ?>
                        <input type="hidden" name="wph_licence_form_submit" value="true" />
                        <input type="hidden" name="wph_licence_deactivate" value="true" />

                        <div class="start-container licence-key">
                            <div class="text">
                
                                <h2><?php _e( "License Key", 'wp-hide-security-enhancer' ) ?></h2>
                                <div class="option">
                                    <div class="controls">
                                        <p><b><?php echo substr($licence_data['key'], 0, 20) ?>-xxxxxxxx-xxxxxxxx</b> &nbsp;&nbsp;&nbsp;<a class="button-secondary" title="Deactivate" href="javascript: void(0)" onclick="jQuery(this).closest('form').submit();"><?php _e( "Deactivate", 'wp-hide-security-enhancer' ) ?></a></p>
                                    </div>
                                    <div class="explain"><?php _e( "You can generate more keys at ", 'wp-hide-security-enhancer' ) ?> <a href="https://www.wp-hide.com/my-account/" target="_blank"><?php _e( "My Account", 'wp-hide-security-enhancer' ) ?></a></div>
                                </div>
                                
                                <?php
                                    
                                    if ( isset( $licence_data ) &&  ! empty ( $licence_data['licence_status'] ) )
                                        {
                                            ?><br /><p><?php _e( "License Status", 'wp-hide-security-enhancer' ) ?>: <b><?php echo ucfirst( __( $licence_data['licence_status'], 'wp-hide-security-enhancer' ) ); ?></b></p><?php   
                                        }
                                
                                    if ( isset( $licence_data ) &&  ! empty ( $licence_data['licence_expire'] ) )
                                        {
                                            ?><p><?php _e( "License Expiration", 'wp-hide-security-enhancer' ) ?>: <b><?php echo date_i18n( get_option( 'date_format' ), strtotime( $licence_data['licence_expire'] ) ); ?></b><?php
                                            
                                                if  ( strtotime( $licence_data['licence_expire'] )  <   strtotime( date('Y-m-d') ) )
                                                    {
                                                        ?> &nbsp;&nbsp;&nbsp;&nbsp;<span class="warning"><?php _e( "Licence is expired, plugin <b>Updates</b> and <b>Support</b> are not available", 'wp-hide-security-enhancer' ) ?>.</span><?php
                                                    } 
                                            
                                            ?></p><?php   
                                        }
                                        
                                    if ( isset( $licence_data ) &&  ! empty ( $licence_data['_sl_new_version'] ) )
                                        {
                                            global $wph;
                                            
                                            $plugin_data    =   $wph->functions->get_plugin_data( WPH_PATH . '/wp-hide.php', $markup = true, $translate = true );
                                            $deployed_plugin_verson =   isset ( $plugin_data['Version'] )   ?   $plugin_data['Version'] :   '';
                                            
                                            if (  ! empty ( $deployed_plugin_verson )    &&  version_compare( $deployed_plugin_verson, $licence_data['_sl_new_version'], "<")) 
                                                {
                                            
                                                    ?><p><?php _e( "New plugin version available", 'wp-hide-security-enhancer' ) ?>: <b><?php echo $licence_data['_sl_new_version']; ?></b></p><?php   
                                                }
                                        }
                                ?>
                            </div>
                        </div>
                    </form>
    
            </div>