<?php   
        
        if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

        global $wph;

        $blog_id_settings   =   $wph->functions->get_blog_id();
        
        $global_settings    =   $wph->functions->get_global_settings ( );
        $settings           =   $wph->functions->get_site_settings ( $blog_id_settings );
        
        if ( is_multisite() )
            $title =    __( "Network Settings", 'wp-hide-security-enhancer' );
            else
            $title =    __( "Settings", 'wp-hide-security-enhancer' );
                        
        ?>
        <div id="wph" class="wrap">
            <h1><span class="dashicons dashicons-admin-tools"></span> WP Hide & Security Enhancer <span class="plugin-mark">PRO</span> - <?php echo $title; ?></h1>
            
            <?php  

            if( !   $wph->licence->licence_key_verify() || $wph->expanded() )
                include( WPH_PATH . 'include/admin-interfaces/_licence.php' );
                else
                include( WPH_PATH . 'include/admin-interfaces/_licence_deactivate.php' );
            
            if(  $wph->licence->licence_key_verify() && ! $wph->expanded() )
                {
            ?>
            
            <h3><?php _e( "Settings", 'wp-hide-security-enhancer' ) ?></h3>
                             
            <form id="form_data" name="form" method="post">
                <input type="hidden" name="wph-interface-fields" value="true" />
                <?php wp_nonce_field( 'wph/interface_fields', 'wph-interface-nonce' ); ?>   
                <table class="form-table">
                    <tbody>
                        
                        <tr valign="top">
                            <th scope="row">
                                <a class="button" href="javascript: void(0)" onClick="jQuery('#export_settings').slideDown('fast')"><?php _e( "Export Settings", 'wp-hide-security-enhancer' ) ?></a>
                            </th>
                            <td>
                                <p><label><?php _e( "Export current settings", 'wp-hide-security-enhancer' ) ?></label></p>
                                <!-- WPH Preserve - Start -->
                                <p><textarea onclick="this.focus();this.select()" id="export_settings" class="code" readonly="readonly" style="width: 100%; display: none" rows="12"><?php  
                                    
                                    $output_settings    =   $settings['module_settings'];
                                    if ( isset ( $output_settings ) )
                                        unset ( $output_settings['document_loaded_assets_postprocessing'] );
                                    
                                    echo htmlspecialchars( json_encode( $output_settings ) )  
                                    
                                    ?></textarea></p>
                                <!-- WPH Preserve - Stop -->
                            </td>
                        </tr>
                
                        <tr valign="top">
                            <th scope="row">
                                <a class="button" href="javascript: void(0)" onClick="jQuery('#import_settings').slideDown('fast')"><?php _e( "Import Settings", 'wp-hide-security-enhancer' ) ?></a>
                            </th>
                            <td>
                                <p><label><?php _e( "Import previously saved settings", 'wp-hide-security-enhancer' ) ?></label></p>
                                <p><textarea id="import_settings" class="code" name="import_settings" style="width: 100%; display: none" rows="12"></textarea></p>
                            </td>
                        </tr>
                        <?php  if ( $wph->server_nginx_config   === TRUE ) {   ?>
                        <tr valign="top">
                            <th scope="row">
                                <select id="nginx_generate_simple_rewrite"  name="nginx_generate_simple_rewrite" <?php if ( $wph->functions->server_is_wpengine() ||   $wph->functions->server_is_kinsta() )  { ?>disabled="disabled"<?php } ?>>
                                    <option value="no" <?php selected('no', $global_settings['nginx_generate_simple_rewrite']); ?>><?php _e( "No", 'wp-hide-security-enhancer' ) ?></option>
                                    <option value="yes" <?php selected('yes', $global_settings['nginx_generate_simple_rewrite']); ?>><?php _e( "Yes", 'wp-hide-security-enhancer' ) ?></option>
                                </select>
                            </th>
                            <td>
                                <label for="nginx_generate_simple_rewrite"><b><?php _e( "Generate simple Rewrite Rules for Nginx.", 'wp-hide-security-enhancer' ) ?> <?php if ( $wph->functions->server_is_wpengine()  ||  $wph->functions->server_is_kinsta())  { ?><span class="warning">You use <?php if ( $wph->functions->server_is_wpengine() ) { echo 'WPEngine';} if ( $wph->functions->server_is_kinsta() ) { echo 'Kinsta';} ?> which require simple rewrite.</span><?php } ?></b></label>
                                <p><?php _e( "Not all servers runing Nginx can handle full Rewrite rules as recommended by developers at", 'wp-hide-security-enhancer' ) ?> https://www.nginx.com/blog/creating-nginx-rewrite-rules/  <?php _e( "When active, this option generate simple version. Generally a server works with either full or simple style rewrite rules.", 'wp-hide-security-enhancer' ) ?></p>
                            </td>
                        </tr>
                        <?php } $wph->interface_expand(); ?>
                        
                        <?php  if ( $wph->server_nginx_config   === TRUE ) {   ?>
                        <tr valign="top">
                            <th scope="row">
                                <select name="nginx_save_rewrite_to_file" id="nginx_save_rewrite_to_file">
                                    <option value="no" <?php selected('no', $global_settings['nginx_save_rewrite_to_file']); ?>><?php _e( "No", 'wp-hide-security-enhancer' ) ?></option>
                                    <option value="yes" <?php selected('yes', $global_settings['nginx_save_rewrite_to_file']); ?>><?php _e( "Yes", 'wp-hide-security-enhancer' ) ?></option>
                                </select>
                            </th>
                            <td>
                                <label for="nginx_save_rewrite_to_file"><b><?php _e( "Save the Nginx Rewrites to a wphide-nginx.conf file inside the site root ( Nginx ).", 'wp-hide-security-enhancer' ) ?></b></label>
                                <p><?php _e( "To incorporate the necessary dynamically generated Nginx rewrites into a file named nginx.conf situated in the site's root directory, you can utilize this option.", 'wp-hide-security-enhancer' ) ?></p>
                            </td>
                        </tr>
                        <!--
                        <tr valign="top">
                            <th scope="row">
                                <select name="nginx_headers_save_rewrite_to_file" id="nginx_headers_save_rewrite_to_file">
                                    <option value="no" <?php selected('no', $global_settings['nginx_headers_save_rewrite_to_file']); ?>><?php _e( "No", 'wp-hide-security-enhancer' ) ?></option>
                                    <option value="yes" <?php selected('yes', $global_settings['nginx_headers_save_rewrite_to_file']); ?>><?php _e( "Yes", 'wp-hide-security-enhancer' ) ?></option>
                                </select>
                            </th>
                            <td>
                                <label for="nginx_headers_save_rewrite_to_file"><b><?php _e( "Save the Nginx Rewrite Headers to a wphide-nginx-headers.conf file inside the site root ( Nginx ).", 'wp-hide-security-enhancer' ) ?></b></label>
                                <p><?php _e( "When enabled, dynamically generated Nginx rewrite headers will be automatically inserted into a file named wphide-nginx-headers.conf located in the root directory of the site. If this option is disabled, the rewrite rules will instead be incorporated into the default wphide-nginx.conf file.", 'wp-hide-security-enhancer' ) ?></p>
                            </td>
                        </tr>
                        -->
                        <?php } ?>
             
                        <tr valign="top">
                            <th scope="row">
                                <select name="self_setup" id="self_setup">
                                    <option value="no" <?php selected('no', $global_settings['self_setup']); ?>><?php _e( "No", 'wp-hide-security-enhancer' ) ?></option>
                                    <option value="yes" <?php selected('yes', $global_settings['self_setup']); ?>><?php _e( "Yes", 'wp-hide-security-enhancer' ) ?></option>
                                </select>
                            </th>
                            <td>
                                <label for="self_setup"><b><?php _e( "I'll set-up the rewrite data myself ( Apache ).", 'wp-hide-security-enhancer' ) ?></b></label>
                                <p><?php _e( "Use this option if don`t want the application to attempt to modify rewrite data on your server and prefer to do that manually. The plugin try to automatically apply the rewrite when using mod_rewrite or IIS rewrite.", 'wp-hide-security-enhancer' ) ?></p>
                            </td>
                        </tr>
                        
                        <tr valign="top">
                            <th scope="row">
                                <select name="covert_relative_urls_to_absolute" id="covert_relative_urls_to_absolute">
                                    <option value="no" <?php selected('no', $global_settings['covert_relative_urls_to_absolute']); ?>><?php _e( "No", 'wp-hide-security-enhancer' ) ?></option>
                                    <option value="yes" <?php selected('yes', $global_settings['covert_relative_urls_to_absolute']); ?>><?php _e( "Yes", 'wp-hide-security-enhancer' ) ?></option>
                                </select>
                            </th>
                            <td>
                                <label for="covert_relative_urls_to_absolute"><b><?php _e( "Convert Relative URLs to Absolute URLs.", 'wp-hide-security-enhancer' ) ?></b></label>
                                <p><?php _e( "If the site uses relative URLs, some of the default slugs may not change. This option ensures the Relative URLs are changed to Absolute URLs, thus changes apply accordingly. Use the option if necessarily, otherwise should  keep to No.", 'wp-hide-security-enhancer' ) ?></p>
                            </td>
                        </tr>
                        
                    </tbody>
                </table>
                                
                <p class="submit">
                    <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save Settings', 'wp-hide-security-enhancer') ?>">
                </p>
                
            </form>
            
            <br /><br />

                 
            <h3><?php _e( "Recovery", 'wp-hide-security-enhancer' ) ?></h3>
            <p class="important"><span class="dashicons dashicons-warning important" alt="f534"></span> <?php _e('Copy the following link to a safe place. You can use later to reset all plugin options, if something go wrong:',    'wp-hide-security-enhancer') ?><br /> <b><span id="wph-recovery-link" onClick="WPH.selectText( 'wph-recovery-link' )"><?php echo trailingslashit ( site_url() ) ?>?wph-recovery=<?php  echo $wph->functions->get_recovery_code() ?></span></b></p>

            
            <br /><br />
            <form id="form_data" name="form" method="post">
                <?php wp_nonce_field( 'wp-hide-cache-clear', '_wpnonce' ); ?>
                <input type="hidden" name="wph-cache-clear" value="true" />
                 
                <h3><?php _e( "Data Collection Status", 'wp-hide-security-enhancer' ) ?></h3>
                <p><?php _e( "The Data Collection consist on a list of post-processed assets, used internally and generated when using CSS or/and JavaScript PostProcessing options.", 'wp-hide-security-enhancer' ) ?><br /><?php _e( "The Data Collection is NOT required to be cleared, unless the layout appear broken.", 'wp-hide-security-enhancer' ) ?></p>
                <p><?php _e( "Data Collection size is", 'wp-hide-security-enhancer' ) ?> <b><?php printf( _n( '%s file', '%s files', $wph->functions->get_cache_size(), 'wp-hide-security-enhancer' ), number_format_i18n( $wph->functions->get_cache_size() ) ); ?></b></p>
                <a class="button" href="javascript: void(0)" onclick="jQuery(this).closest('form').submit();"><?php _e( "Data Collection Clear", 'wp-hide-security-enhancer' ) ?></a>
            </form>
            
            <?php  }  ?>
        </div>