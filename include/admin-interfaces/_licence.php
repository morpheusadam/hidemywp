<?php   
        
        if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
?>

            <div id="form_data">
                <h3><?php _e( "Licence", 'wp-hide-security-enhancer' ) ?></h3>
    
                    <form name="form" method="post">    
                        <?php wp_nonce_field('wph_licence','wph_license_nonce'); ?>
                        <input type="hidden" name="wph_licence_form_submit" value="true" />
                        <input type="hidden" name="wph_licence_activate" value="true" />

                        <div class="start-container licence-key">
                            <div class="text">
                
                                <h2><?php _e( "License Key", 'wp-hide-security-enhancer' ) ?></h2>
                                <div class="option">
                                    <div class="controls">
                                        <p><input type="text" value="" name="licence_key" class="text-input"></p>
                                    </div>
                                    <div class="explain"><?php _e( "Enter the Licence Key you received when purchased this product. If you lost the key, you can always retrieve it from", 'wp-hide-security-enhancer' ) ?> <a href="https://www.wp-hide.com/my-account/" target="_blank"><?php _e( "My Account", 'wp-hide-security-enhancer' ) ?></a></div>
                                </div>
                                <p class="submit">
                                    <input type="submit" name="Submit" class="button-primary" value="<?php _e('Save', 'wp-hide-security-enhancer') ?>">
                                </p> 
                            </div>
                        </div>
                    </form>
    
            </div>