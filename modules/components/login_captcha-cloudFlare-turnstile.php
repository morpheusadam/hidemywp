<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_login_captcha_ct
        {
            
            var $wph;
            
            function __construct()
                {
                    global $wph;
                    
                    $this->wph  =   $wph;
                }
                
            function get_captcha_options()
                {
                 
                    $captcha_options    =   array ( 
                                                    
                                                            'cloudflare_turnstile'  =>  array (
                                                                                                'ct-site-key'           =>  array ( 
                                                                                                                                        'title' =>  __('Site Key',     'wp-hide-security-enhancer'),
                                                                                                                                        'type'  =>  'input'
                                                                                                                                        ),
                                                                                                'ct-site-secret-key'    =>  array ( 
                                                                                                                                        'title' =>  __('Secret Key',     'wp-hide-security-enhancer'),
                                                                                                                                        'type'  =>  'input'
                                                                                                                                        ),
                                                                                                'ct-theme'              =>  array ( 
                                                                                                                                        'title' =>  __('Theme',     'wp-hide-security-enhancer'),
                                                                                                                                        'type'  =>  'select',
                                                                                                                                        'options'   =>  array ( 
                                                                                                                                                                'light' =>  __('Light',     'wp-hide-security-enhancer'),
                                                                                                                                                                'dark'  =>  __('Dark',     'wp-hide-security-enhancer'),
                                                                                                                                                                'auto'  =>  __('Auto',     'wp-hide-security-enhancer'),
                                                                                                                                                                )
                                                                                                                                        ),
                                                                                                'ct-language'              =>  array ( 
                                                                                                                                        'title' =>  __('Language',     'wp-hide-security-enhancer'),
                                                                                                                                        'type'  =>  'select',
                                                                                                                                        'options'   =>  array ( 
                                                                                                                                                                'auto'  => __( 'Auto Detect', 'wp-hide-security-enhancer' ),
                                                                                                                                                                'ar-eg' => __( 'Arabic (Egypt)',        'wp-hide-security-enhancer' ),
                                                                                                                                                                'ar'    => __( 'Arabic',                'wp-hide-security-enhancer' ),
                                                                                                                                                                'de'    => __( 'German',                'wp-hide-security-enhancer' ),
                                                                                                                                                                'en'    => __( 'English',               'wp-hide-security-enhancer' ),
                                                                                                                                                                'es'    => __( 'Spanish',               'wp-hide-security-enhancer' ),
                                                                                                                                                                'fa'    => __( 'Farsi/Persian',         'wp-hide-security-enhancer' ),
                                                                                                                                                                'fr'    => __( 'French',                'wp-hide-security-enhancer' ),
                                                                                                                                                                'id'    => __( 'Indonesian',            'wp-hide-security-enhancer' ),
                                                                                                                                                                'it'    => __( 'Italian',               'wp-hide-security-enhancer' ),
                                                                                                                                                                'ja'    => __( 'Japanese',              'wp-hide-security-enhancer' ),
                                                                                                                                                                'ko'    => __( 'Korean',                'wp-hide-security-enhancer' ),
                                                                                                                                                                'nl'    => __( 'Dutch',                 'wp-hide-security-enhancer' ),
                                                                                                                                                                'pl'    => __( 'Polish',                'wp-hide-security-enhancer' ),
                                                                                                                                                                'pt'    => __( 'Portuguese',            'wp-hide-security-enhancer' ),
                                                                                                                                                                'pt-br' => __( 'Portuguese (Brazil)',   'wp-hide-security-enhancer' ),
                                                                                                                                                                'ru'    => __( 'Russian',               'wp-hide-security-enhancer' ),
                                                                                                                                                                'tlh'   => __( 'Klingon',               'wp-hide-security-enhancer' ),
                                                                                                                                                                'tr'    => __( 'Turkish',               'wp-hide-security-enhancer' ),
                                                                                                                                                                'uk'    => __( 'Ukrainian',             'wp-hide-security-enhancer' ),
                                                                                                                                                                'zh'    => __( 'Chinese',               'wp-hide-security-enhancer' ),
                                                                                                                                                                'zh-cn' => __( 'Chinese (Simplified)',  'wp-hide-security-enhancer' ),
                                                                                                                                                                'zh-tw' => __( 'Chinese (Traditional)', 'wp-hide-security-enhancer' )
                                                                                                                                                                )
                                                                                                                                        ),
                                                                                                )
                                                    );
                    return $captcha_options;   
  
                }
                
                
            function get_options()
                {
                    $html_options    =   $this->get_captcha_options();
                    $html_options    =   $html_options['cloudflare_turnstile'];
                    
                    $values =   (array)$this->wph->functions->get_site_module_saved_value( 'captcha_type',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    foreach ( $html_options as  $option_key =>  $data )
                        {
                            if ( ! isset( $values[ $option_key ] ) )
                                {
                                    switch ( $data['type'] )
                                        {
                                            case 'input' :   
                                                            $values[ $option_key ]  =   '';
                                                            break;
                                            case 'select' :   
                                                            reset ( $data['options'] );
                                                            $values[ $option_key ]  =   key ( $data['options'] );
                                                            break;
                                        }
                                }
                        }                    
                }
                
            
            function get_module_description( $values )
                {
                    $html   =   '';
                    
                    if ( ! isset ( $values['cloudflare_turnstile_checked_for'] ) )
                        $values['cloudflare_turnstile_checked_for'] =   '';
                    if ( ! empty ( $values['ct-site-key'] )  &&  ! empty ( $values['ct-site-secret-key'] )   &&  md5 ( $values['ct-site-key'] . $values['ct-site-secret-key'] )  !=  $values['cloudflare_turnstile_checked_for'] )
                        {
                            ob_start();
                                                                
                            $interface_errors   =   get_transient( 'wph-process_API_interface_errors');
                            delete_transient ( 'wph-process_API_interface_errors' );
                            
                            if ( ! empty ( $interface_errors ) )
                                echo '<p class="important">' . $interface_errors . '</p>';
                            
                            ?><div id="captch_test" class="captcha-integration cloudflare_turnstile">
                            <div><?php $this->ct_field() ?></div>

                            <p></p>
                            <button class="button-primary red" onclick="WPH.captcha_test(); return false;"><?php _e('Test API',  'wp-hide-security-enhancer') ?></button>
                            <p><?php _e('The captcha will not show on the front side until the Test is successful.',  'wp-hide-security-enhancer') ?></p>
                            <input type="hidden" id="api_test" name="api_test" value="" />
                            
                            </div>
                            <?php
                            
                            $html   =   ob_get_clean();
                        }
                        
                    if ( ! empty ( $values['ct-site-key'] )  &&  ! empty ( $values['ct-site-secret-key'] )   &&  md5 ( $values['ct-site-key'] . $values['ct-site-secret-key'] )  ==  $values['cloudflare_turnstile_checked_for'] )
                        {
                            ob_start();
                                   
                            ?><div id="captch_test" class="captcha-integration cloudflare_turnstile">
                            <p class="green"><?php _e('The CloudFlare Turnstile integration is completed.',  'wp-hide-security-enhancer') ?></p>
                            
                            <?php 
                                
                            $this->ct_field();
                            
                            ?></div><?php
                            
                            $html   =   ob_get_clean();
                        }
                        
                    return $html;
                
                }
                
                
            function get_module_help()
                {
                    ?><p><?php _e('You can get your site key and secret key from',  'wp-hide-security-enhancer') ?> <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">https://dash.cloudflare.com/?to=/:account/turnstile</a></p><?php
                    ?><p><?php _e('After filling in and saving the options, remember to click the <b class="important">Test API</b> button located at the top of this section. The CAPTCHA won\'t appear on the front end until the test is successfully completed',  'wp-hide-security-enhancer') ?></p><?php
                }
                
                
            function api_test( $module_settings )
                {
                    if ( isset ( $_POST['cf-turnstile-response'] ) )
                        {
                            
                            $api_response   =   $this->ct_api_check( $_POST['cf-turnstile-response'] );
        
                            if( $api_response->success )
                                {
                                    $settings_hash  =   md5( $module_settings['ct-site-key'] . $module_settings['ct-site-secret-key'] );
                                    $module_settings['cloudflare_turnstile_checked_for']    =   $settings_hash;   
                                }
                                else
                                {
                                    $CaptchaAPIResponse =   '';
                                    foreach ( $api_response->{'error-codes'}    as  $error_code )
                                        {
                                            $CaptchaAPIResponse   .=  $error_code .   ' ';
                                        }
                                                                                
                                    set_transient( 'wph-process_API_interface_errors', __('The API returned an error', 'wp-hide-security-enhancer') . ': ' . $CaptchaAPIResponse, HOUR_IN_SECONDS );    
                                    
                                }
                        }

                    return $module_settings;
                }
                
            
            function init_captcha( $saved_field_data )
                {
                    if ( $saved_field_data['captcha_type']  ==  'cloudflare_turnstile'  &&  ! empty ( $saved_field_data['ct-site-key'] )  &&  ! empty ( $saved_field_data['ct-site-secret-key'] )   &&  isset ( $saved_field_data['cloudflare_turnstile_checked_for'] ) &&  ! empty ( $saved_field_data['cloudflare_turnstile_checked_for'] )   &&  md5 ( $saved_field_data['ct-site-key'] . $saved_field_data['ct-site-secret-key'] )  ==  $saved_field_data['cloudflare_turnstile_checked_for'] )
                        {
                            add_action('login_form',            array ( $this, 'ct_login_form' ) );
                            add_action('authenticate',          array ( $this, 'ct_authenticate' ), 99 );
                            
                            add_action('lostpassword_form',     array ( $this, 'lostpassword_form' ) );
                            add_action('lostpassword_post',     array ( $this, 'lostpassword_post' ), 99 );
                               
                            add_action('register_form',         array ( $this, 'register_form' ) );
                            add_action('registration_errors',   array ( $this, 'registration_errors' ), 99 );   
                        }
                }

            
            function ct_field()
                {
                    
                    $values =   (array)$this->wph->functions->get_site_module_saved_value( 'captcha_type',  $this->wph->functions->get_blog_id_setting_to_use());   
                    
                    $rand   =   rand ( 1, 99999 );
                    
                    ?>
                        <style>
                            #login {width: 350px}
                            div.g-recaptcha {padding-bottom: 20px}
                        </style>
                        <script src="https://challenges.cloudflare.com/turnstile/v0/api.js?onload=onloadTurnstileCallback" defer></script>
                        <div id="cf-turnstile-<?php echo $rand ?>" data-sitekey="<?php echo $values['ct-site-key'] ?>" data-theme="<?php echo $values['ct-theme'] ?>" data-language="<?php echo $values['ct-language'] ?>"></div>
                        
                        <script>
                        
                        window.onloadTurnstileCallback = function () {
                                turnstile.render('#cf-turnstile-<?php echo $rand ?>', {
                                    sitekey: '<?php echo $values['ct-site-key'] ?>'
                                });
                            };
                        
                        </script>
        
                    
                    <?php
                    
                }
                
            function ct_api_check( $postdata )
                {
                    $module_settings =   (array)$this->wph->functions->get_site_module_saved_value( 'captcha_type',  $this->wph->functions->get_blog_id_setting_to_use());
                   
                    $secret     =   $module_settings['ct-site-secret-key'];
                    $headers    =   array(
                                            'body' => [
                                                'secret' => $secret,
                                                'response' => $postdata
                                            ]
                                            );
                    $verify                 =   wp_remote_post('https://challenges.cloudflare.com/turnstile/v0/siteverify', $headers);
                    $verify                 =   wp_remote_retrieve_body( $verify );
                    
                    $response               =   json_decode ( $verify );
                    
                    return $response;

                }
                
                
            function ct_login_form()
                {
                    $this->ct_field();
                }
            
            
            function ct_authenticate( $user )
                {
                    if ( ! is_object ( $user )  ||  ! isset ( $user->ID ) ) 
                        return $user;
                    
                    if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST )  ||  ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) )
                        return $user;

                    if ( is_wp_error ( $user ) )
                        return $user;
                        
                    if ( ! isset ( $_POST['cf-turnstile-response'] ) )
                        return $user;

                    $api_response   =   $this->ct_api_check( $_POST['cf-turnstile-response'] );
                        
                    if( $api_response->success )
                        return $user;
           
                    $user = new WP_Error( 'ct_error', esc_html__('Unable to verify that you are human.', 'wp-hide-security-enhancer') );
                                                    
                    return $user;
                
                }
                
                
            function register_form()
                {
                    $this->ct_field();
                } 
                
            
            function registration_errors( $errors )
                {
                    if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST )  ||  ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) )
                        return $errors;   
                    
                    $api_response   =   $this->ct_api_check( $_POST['cf-turnstile-response'] );
                        
                    if( $api_response->success !==  TRUE )
                        $errors->add( 'ct_error', sprintf( '<strong>%s</strong>: %s', __( 'Error!', 'wp-hide-security-enhancer' ), __('Unable to verify that you are human.', 'wp-hide-security-enhancer') ) );
  
                    return $errors;
                                        
                }
                
                
            function lostpassword_form()
                {
                    $this->ct_field();
                } 
                
            
            function lostpassword_post( $errors )
                {
                    if ( ( defined( 'REST_REQUEST' ) && REST_REQUEST )  ||  ( defined( 'XMLRPC_REQUEST' ) && XMLRPC_REQUEST ) )
                        return $errors;   
                    
                    $api_response   =   $this->ct_api_check( $_POST['cf-turnstile-response'] );
                        
                    if( $api_response->success !==  TRUE )
                        $errors->add( 'ct_error', sprintf( '<strong>%s</strong>: %s', __( 'Error!', 'wp-hide-security-enhancer' ), __('Unable to verify that you are human.', 'wp-hide-security-enhancer') ) );
  
                    return $errors;
                                        
                }
            
            
        }