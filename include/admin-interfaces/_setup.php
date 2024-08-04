<?php   
        
        if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

        global $blog_id, $wph;
                        
        include_once( WPH_PATH . '/include/class.rewrite-process.php' );
        $rewrite_process    =   new WPH_Rewrite_Process( TRUE );
        
        $_blog_id   =   $blog_id;
        if ( is_multisite() )
            {
                $ms_settings    =   $this->wph->functions->get_site_settings('network'); 
                $_blog_id   =   'network';
            }

        $global_settings    =   $this->functions->get_global_settings ( );
    
        ?>
        <div id="wph" class="wrap">
            <h1><span class="dashicons dashicons-admin-tools"></span> WP Hide & Security Enhancer <span class="plugin-mark">PRO</span> - <?php _e( "Setup", 'wp-hide-security-enhancer' ) ?></h1>
            <?php if( !   $wph->licence->licence_key_verify() || $wph->expanded() )
            {
                include( WPH_PATH . 'include/admin-interfaces/_licence.php' );
                return;
            } ?>
            <br />
            <div class="start-container">
                <div class="text">
             
                    <h2><?php _e('ReWrite', 'wp-hide-security-enhancer') ?></h2>
                    <?php
                    
                    $wph_rewrite_manual_install =   get_site_option('wph-rewrite-manual-install');                            
                    if  ( ! empty ($wph_rewrite_manual_install ))
                        {
                            
                            if (  $wph->server_htaccess_config  === TRUE )
                                {
                                    ?>
                                    <p><?php _e('Add the following to your', 'wp-hide-security-enhancer') ?> <code>.htaccess</code> <?php _e('file in', 'wp-hide-security-enhancer') ?> <code><?php echo ABSPATH ?></code> <?php _e('<strong>above</strong> all other code', 'wp-hide-security-enhancer') ?>.</p>
                                    <p><?php _e('Remove any existing rewrite rules within', 'wp-hide-security-enhancer') ?> <strong># BEGIN WP Hide & Security Enhancer</strong> <?php _e('and', 'wp-hide-security-enhancer') ?> <strong># END WP Hide & Security Enhancer</strong></p>
                                    <?php 
                                }
                                
                            if (  $wph->server_web_config  === TRUE )
                                {
                                    ?>
                                    <p><?php _e('Add the following to your', 'wp-hide-security-enhancer') ?> <code>web.config</code> <?php _e('file in', 'wp-hide-security-enhancer') ?> <code><?php echo ABSPATH ?></code></p>
                                    <p><?php _e('Remove any existing rewrite rules named <b>wph</b>, then add the following into &#x3C;rules&#x3E;, above any other rule.', 'wp-hide-security-enhancer') ?></p>
                                    <?php 
                                }
                                
                            if (  $wph->server_nginx_config  === TRUE )
                                {
                              
                                    //Check if use Wpengine
                                    if (    $wph->functions->server_is_wpengine() )
                                        {
                                            ?>
                                            <p><?php _e('Your site use WPEngine! You need to get in touch with live support and forward the following details:', 'wp-hide-security-enhancer') ?></p>
                                            <p>- <?php _e('Ask the support representative to add the following Nginx rewrites to your account at "before" (not "before-in-location") spot! This can be for Staging or Production, depends on which the current site is deployed.', 'wp-hide-security-enhancer') ?></p>
                                            <p>- <?php _e('Explicitly specify the code lines must be used as is, they are not redirects but rewrite rules.', 'wp-hide-security-enhancer') ?></p>
                                            <?php
                                        }
                                    else if (    $wph->functions->server_is_kinsta() )
                                        {
                                            ?>
                                            <p><?php _e('Your site use Kinsta! You need to get in touch with live support and forward the following details:', 'wp-hide-security-enhancer') ?></p>
                                            <p>- <?php _e('Ask the support representative to add the following Nginx rewrites to your account! This can be for Staging or Production, depends on which the current site is deployed.', 'wp-hide-security-enhancer') ?></p>
                                            <p>- <?php _e('Explicitly specify the code lines must be used as is, they are not redirects but rewrite rules.', 'wp-hide-security-enhancer') ?></p>
                                            <?php
                                        }
                                        else
                                     
                                        {
                                    
                                            $global_settings    =   $this->functions->get_global_settings ( );
                                            if ( $global_settings['nginx_save_rewrite_to_file'] ==  'yes' ) {
                                            
                                            ?>
                                            <h4><?php _e('There are two ways to deply the custom nginx rewrites:', 'wp-hide-security-enhancer') ?></h2>
                                            <?php } ?>
                                            <li>
                                            <p><?php _e('Add the following lines to your Nginx config file located usually at', 'wp-hide-security-enhancer') ?> /etc/nginx/sites-available/
                                            <br /><?php _e('Replace any existing rewrite rules within', 'wp-hide-security-enhancer') ?> <strong># BEGIN WP Hide & Security Enhancer</strong> <?php _e('and', 'wp-hide-security-enhancer') ?> <strong># END WP Hide & Security Enhancer</strong>
                                            <br /><?php _e('After config file updated', 'wp-hide-security-enhancer') ?>, <strong><?php _e('Test', 'wp-hide-security-enhancer') ?></strong> <?php _e('the new data using ', 'wp-hide-security-enhancer') ?> <strong>nginx -t</strong>. <?php _e('If successfully compile, restart the Nginx service.', 'wp-hide-security-enhancer') ?> <a href="https://wp-hide.com/documentation/setup-the-plugin-for-nginx/" target="_blank"><?php _e('read more..', 'wp-hide-security-enhancer') ?></a></p>
                                            </li>
                                            <?php if ( $global_settings['nginx_save_rewrite_to_file'] ==  'yes' ) { ?>
                                            <p>OR</p>
                                            <li><?php if ( ! empty ( get_site_option ( 'wph-errors-nginx_rewrites_to_file' ) ) ) { ?> <span class="dashicons dashicons-flag"></span><span style="color: red"><?php _e('Caution: The system encountered an issue while attempting to write the custom rewrites to wphide-nginx.conf. Resolve the problem or follow the steps above.  ', 'wp-hide-security-enhancer') ?></span <br />
                                            <?php } _e('Correspondingly, with the option "Save the Nginx Rewrites to a wphide-nginx.conf file," the following lines have been added to a file named wphide-nginx.conf located on site root. You can incorporate this file into your nginx.config using the command ', 'wp-hide-security-enhancer') ?>:<br /><strong>include <?php echo ABSPATH ?>wphide-nginx.conf;</strong> 
                                            <br /><?php _e('After the update, test the new data using ', 'wp-hide-security-enhancer') ?> <strong>nginx -t</strong>. <?php _e('If successfully compile, restart the Nginx service.', 'wp-hide-security-enhancer') ?> <a href="https://wp-hide.com/include-the-wphide-nginx-conf-containing-custom-rewrites-to-your-server-configuration/" target="_blank"><?php _e('read more..', 'wp-hide-security-enhancer') ?></a></p>
                                            </li>
                                            <?php
                                            }
                                        } 
                                }
                
                        
                        } else { ?>
                    <p><?php _e('There is no change in rewrite data, no additional action is necessary.', 'wp-hide-security-enhancer') ?></p>
                    
                    <?php } ?>

                </div>
            </div>
            <br />
 
            <form method="post" action="WPH-preserved-url-<?php
            
            $admin_slug   =   $wph->functions->get_site_module_saved_value( 'admin_url', $_blog_id);
            if ( empty ($admin_slug) )
                $admin_slug =   'wp-admin';
            
            if ( is_multisite() &&  is_network_admin() )
                {
                    $form_location       =   network_site_url( $admin_slug . "/network/admin.php?page=wp-hide-setup");
                }
                else
                {
                    $form_location       =   trailingslashit( site_url() ) . $admin_slug . "/admin.php?page=wp-hide-setup";
                }
            
            echo md5($form_location);
            $wph->functions->add_preserved_url ('WPH-preserved-url-' . md5($form_location), $form_location);
            
            ?>">
                <?php wp_nonce_field( 'wph/interface_fields', 'wph-interface-nonce' ); ?>    
                
                <?php  if (  $wph->server_nginx_config  === TRUE ) { ?>
                <?php
                    
                    $readable_processed_rewrite =   $rewrite_process->get_readable_rewrite_data();
                    if ( isset($readable_processed_rewrite['map']) )
                        {
                ?><p><?php _e('The following code need to be placed before', 'wp-hide-security-enhancer') ?> <strong>server { .. }</strong> <?php _e('block, which is the http { .. } section.', 'wp-hide-security-enhancer') ?></p>
                <!-- WPH Preserve - Start -->
                <textarea onclick="this.focus();this.select()" class="code" readonly="readonly" style="width: 100%" rows="12"><?php echo "\n" . $readable_processed_rewrite['map']; ?></textarea>
                <!-- WPH Preserve - Stop -->
                <?php
                        }
                        
                    if ( isset($readable_processed_rewrite['firewall']) )
                        {
                ?><p><?php _e('The following code need to be placed before', 'wp-hide-security-enhancer') ?> <strong>server { .. }</strong> <?php _e('block, which is the http { .. } section.', 'wp-hide-security-enhancer') ?></p>
                <p><?php _e('Or use an include for the file ', 'wp-hide-security-enhancer') ?> <strong>include <?php echo ABSPATH ?>wphide-firewall-nginx.conf;</strong></p>
                <!-- WPH Preserve - Start -->
                <textarea onclick="this.focus();this.select()" class="code" readonly="readonly" style="width: 100%" rows="12"><?php echo "\n" . $readable_processed_rewrite['firewall']; ?></textarea>
                <!-- WPH Preserve - Stop -->
                <?php
                        }
                        
                    if (    isset($readable_processed_rewrite['default_variables']) ||  isset($readable_processed_rewrite['location']) ||  isset($readable_processed_rewrite['header']) ||  isset($readable_processed_rewrite['firewall_conditionals']) )
                        {    
                            $text       =   isset($readable_processed_rewrite['default_variables'])? $readable_processed_rewrite['default_variables'] :   '';
                            $text      .=   isset($readable_processed_rewrite['location'])? $readable_processed_rewrite['location'] :   '';
                            $text      .=   isset($readable_processed_rewrite['header'])? $readable_processed_rewrite['header'] :   '';
                            $text      .=   isset($readable_processed_rewrite['firewall_conditionals'])? $readable_processed_rewrite['firewall_conditionals'] :   '';
                            
                            //remove duplicated comments
                            $text   =   str_replace( "# END WP Hide & Security Enhancer\n# BEGIN WP Hide & Security Enhancer\n", "", $text );
                
                //if (  !  $wph->functions->server_is_wpengine() &&   ! $wph->functions->server_is_kinsta() )
                    {
                ?>
                <p><?php _e('The following code need to be placed before', 'wp-hide-security-enhancer') ?> <strong>location /</strong> <?php _e('block.', 'wp-hide-security-enhancer') ?>. <?php if ( $global_settings['nginx_save_rewrite_to_file'] ==  'yes' ) { _e('Or use an include for the file ', 'wp-hide-security-enhancer') ?> <strong>include <?php echo ABSPATH ?>wphide-nginx.conf;</strong><?php } ?></p>
                <?php } ?>
                
                <!-- WPH Preserve - Start -->
                <textarea onclick="this.focus();this.select()" class="code" readonly="readonly" style="width: 100%" rows="12"><?php echo "\n" . $text ?></textarea>
                <!-- WPH Preserve - Stop -->
                <?php
                        }
                ?>
                <?php } else { ?>
                <!-- WPH Preserve - Start -->
                <textarea onclick="this.focus();this.select()" class="code" readonly="readonly" style="width: 100%" rows="12"><?php echo "\n" . $rewrite_process->get_readable_rewrite_data(); ?></textarea>
                <!-- WPH Preserve - Stop -->
                <?php } ?>
                <?php
                if  ( ! empty ($wph_rewrite_manual_install ))
                        {
                    ?>
                <p><?php _e('Once all above steps completed, confirm throught the following button.', 'wp-hide-security-enhancer') ?></p>
                
                
                <input type="hidden" name="rewrite-update-confirm" value="yes" />
                <?php } ?>
            </form>
            
            <?php
                if  ( ! empty ($wph_rewrite_manual_install ))
                        {
                            $wp_login_slug   =   $wph->functions->get_site_module_saved_value( 'new_wp_login_php', $_blog_id, 'display');
                            if ( empty ( $wp_login_slug ) )
                                $wp_login_slug  =   'wp-login.php';
                                
                            
                            //?redirect_to=<?php //echo urlencode( $wph->functions->get_current_url() )
                    ?>
            <div id="ruc-actions">
                <!-- WPH Preserve - Start -->
                <input name="submit" id="ruc-submit" class="button button-primary" value="Confirm" type="submit" onClick="rewrite_save_confirm('<?php $home_url   =   preg_replace('/:[0-9]+/', '', str_replace(array ("https:" , "http:"), "", site_url())); echo trailingslashit($home_url); ?>index.php', '<?php echo trailingslashit ( $home_url ) . $wp_login_slug; ?>', '<?php echo wp_create_nonce( 'ruc-nonce' ); ?>', false)" />
                <!-- WPH Preserve - Stop -->
                
                <div id="ruc-loading" class="apto-spinner">
                  <div class="rect1"></div>
                  <div class="rect2"></div>
                  <div class="rect3"></div>
                  <div class="rect4"></div>
                  <div class="rect5"></div>
                </div>
                
                
<script type="text/javascript">

    function rewrite_save_confirm ( home_url, login_url, _nonce, force_confirm ) 
        {
            document.querySelectorAll("input#ruc-submit")[0].setAttribute("disabled", "disabled");
            document.querySelectorAll("#ruc-actions .apto-spinner")[0].style.display = "inline-block";

            var params = new FormData(); 
            params.append( 'wph-action', 'ruc');
            params.append( '_nonce', _nonce);
            params.append( 'force_confirm', force_confirm);
            
            var wph_xmlhttp = new XMLHttpRequest();
            wph_xmlhttp.open("POST", home_url, true);
            wph_xmlhttp.send( params );
            
            wph_xmlhttp.onreadystatechange = function() {
                if (wph_xmlhttp.readyState == XMLHttpRequest.DONE) {
                    
                    document.querySelectorAll("input#ruc-submit")[0].removeAttribute("disabled");
                    document.querySelectorAll("#ruc-actions .apto-spinner")[0].style.display = "none";
                    if (wph_xmlhttp.status == 200) 
                       {
                                               
                           var data = JSON.parse( wph_xmlhttp.responseText ); 
                           
                           if ( data.status == 'success')
                                {
                                    window.location.href = login_url;
                                }
                                else
                                {
                                    alert( data.message );   
                                }
                           
                       }
                   else 
                       {
                                               
                           alert('There is a problem. Please check again the rewrite data on your server.');
                       }
                }
            };
            
        }

</script>
                
                
                
            </div>
            <?php } ?>
            
                                    
            <p><br /></p>

            <div class="start-container">
                <div class="text">
             
                    <h2><?php _e('Environment File', 'wp-hide-security-enhancer') ?></h2>
                    <?php
                    
                        $WPH_Environment    =   new WPH_Environment();
                        $environment_status =   $WPH_Environment->is_correct_environment() === TRUE ?   'Correct'   :   'Incorect';
                        if ( $environment_status    ==  'Incorect')
                            {
                                $wp_upload_dir              =   wp_upload_dir();
                                
                                $WPH_Environment->write_environment();
                                $environment_status =   $WPH_Environment->is_correct_environment() === TRUE ?   'Correct'   :   'Incorect';   
                            }
                        
                        $disabled = "";
                        
                        if ( $environment_status    ==  'Incorect' )
                            {
                                ?>
                                <p><?php _e('Add the following to ', 'wp-hide-security-enhancer'); ?> <code><?php echo $wp_upload_dir['basedir'] . '/wph/environment.php' ?></code> <?php _e('<strong>Replacing</strong> anything inside the file', 'wp-hide-security-enhancer') ?>:</p>
                                <?php   
                            }
                            else { 
                                $disabled   =   'disabled="disabled"';
                                ?>    
                            <p><br /><?php _e('The environment file contain correct data, no additional action is necessary.', 'wp-hide-security-enhancer') ?></p>
                            <?php } ?>
                </div>
            </div>
            <br />
            
            <textarea <?php echo $disabled ?> onclick="this.focus();this.select()" class="code" readonly="readonly" style="width: 100%" rows="12"><?php echo $WPH_Environment->get_environment_content(); ?></textarea>
            
            
            <p><br /></p>

            <div class="start-container">
                <div class="text">
             
                    <h2><?php _e('Wp-config.php', 'wp-hide-security-enhancer') ?></h2>
                    <?php
                    
                        $wp_config_status =   $wph->functions->check_wp_config() === TRUE ?   'Correct'   :   'Incorect';
                        
                        $disabled = "";
                        
                        if ( $wp_config_status    ==  'Incorect' )
                            {
                                ?>
                                <p><?php _e('Add the following code to ', 'wp-hide-security-enhancer'); ?> <code><?php echo $wph->functions->get_wp_config_path() ?></code> <?php _e(' at top, right after &#60;?php tag. If exists, <strong>Replace</strong> any existing lines betwen <strong># START WP Hide & Security Enhancer</strong> and <strong># END WP Hide & Security Enhancer</strong>', 'wp-hide-security-enhancer') ?>:</p>
                                <?php   
                            }
                            else { 
                                $disabled   =   'disabled="disabled"';
                                ?>    
                            <p><br /><?php _e('The wp-config.php file contain correct data, no additional action is necessary.', 'wp-hide-security-enhancer') ?></p>
                            <?php } ?>
                </div>
            </div>
            <br />
            <?php $wph->interface_expand(); ?>
            <textarea <?php echo $disabled ?> onclick="this.focus();this.select()" class="code" readonly="readonly" style="width: 100%" rows="12"># BEGIN WP Hide & Security Enhancer
<?php echo implode("\n", $wph->functions->get_wp_config_data()); ?>

# END WP Hide & Security Enhancer</textarea>
                                       
        </div>
    
    
    <?php


?>