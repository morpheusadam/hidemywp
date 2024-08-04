<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_permissions_policy extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "Permissions Policy";
                }
                                    
            function get_module_component_settings()
                {
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'permissions_policy',
                                                                    'label'         =>  __('Permissions Policy',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Permissions Policy',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Provides a mechanism to allow and deny the use of browser features in its own frame, and in iframes that it embeds.",    'wp-hide-security-enhancer') .
                                                                                                                                
                                                                                                                                "<br /><br />" . __("Directives:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>accelerometer</b> - "  . __("Controls whether the current document is allowed to gather information about the acceleration of the device through the Accelerometer interface.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>ambient-light-sensor</b> - "  . __("Controls whether the current document is allowed to gather information about the amount of light in the environment around the device through the AmbientLightSensor interface.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>autoplay</b> - "  . __("Controls whether the current document is allowed to autoplay media requested through the HTMLMediaElement interface. When this policy is disabled and there were no user gestures, the Promise returned by HTMLMediaElement.play() will reject with a DOMException. The autoplay attribute on <audio> and <video> elements will be ignored.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>battery</b> - "  . __("Controls whether the use of the Battery Status API is allowed. When this policy is disabled, the Promise returned by Navigator.getBattery() will reject with a NotAllowedError DOMException.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>camera</b> - "  . __("Controls whether the current document is allowed to use video input devices. When this policy is disabled, the Promise returned by getUserMedia() will reject with a NotAllowedError DOMException.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>display-capture</b> - "  . __("Controls whether or not the current document is permitted to use the getDisplayMedia() method to capture screen contents. When this policy is disabled, the promise returned by getDisplayMedia() will reject with a NotAllowedError if permission is not obtained to capture the display's contents.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>document-domain</b> - "  . __("Controls whether the current document is allowed to set document.domain. When this policy is disabled, attempting to set document.domain will fail and cause a SecurityError DOMException to be thrown.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>encrypted-media</b> - "  . __("Controls whether the current document is allowed to use the Encrypted Media Extensions API (EME). When this policy is disabled, the Promise returned by Navigator.requestMediaKeySystemAccess() will reject with a DOMException.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>execution-while-not-rendered</b> - "  . __("Controls whether tasks should execute in frames while they're not being rendered (e.g. if an iframe is hidden or display: none).",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>execution-while-out-of-viewport</b> - "  . __("Controls whether tasks should execute in frames while they're outside of the visible viewport.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>fullscreen</b> - "  . __("Controls whether the current document is allowed to use Element.requestFullScreen(). When this policy is disabled, the returned Promise rejects with a TypeError.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>gamepad</b> - "  . __("Controls whether the current document is allowed to use the Gamepad API. When this policy is disabled, calls to Navigator.getGamepads() will throw a SecurityError DOMException, and the gamepadconnected and gamepaddisconnected events will not fire. ",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>geolocation</b> - "  . __("Controls whether the current document is allowed to use the Geolocation Interface. When this policy is disabled, calls to getCurrentPosition() and watchPosition() will cause those functions' callbacks to be invoked with a GeolocationPositionError code of PERMISSION_DENIED.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>gyroscope</b> - "  . __("Controls whether the current document is allowed to gather information about the orientation of the device through the Gyroscope interface.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>layout-animations</b> - "  . __("Controls whether the current document is allowed to show layout animations.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>legacy-image-formats</b> - "  . __("Controls whether the current document is allowed to display images in legacy formats.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>magnetometer</b> - "  . __("Controls whether the current document is allowed to gather information about the orientation of the device through the Magnetometer interface.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>microphone</b> - "  . __("Controls whether the current document is allowed to use audio input devices. When this policy is disabled, the Promise returned by MediaDevices.getUserMedia() will reject with a NotAllowedError.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>midi</b> - "  . __("Controls whether the current document is allowed to use the Web MIDI API. When this policy is disabled, the Promise returned by Navigator.requestMIDIAccess() will reject with a DOMException.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>navigation-override</b> - "  . __("Controls the availability of mechanisms that enables the page author to take control over the behavior of spatial navigation, or to cancel it outright.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>oversized-images</b> - "  . __("Controls whether the current document is allowed to download and display large images.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>payment</b> - "  . __("Controls whether the current document is allowed to use the Payment Request API. When this policy is enabled, the PaymentRequest() constructor will throw a SecurityError DOMException.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>picture-in-picture</b> - "  . __("Controls whether the current document is allowed to play a video in a Picture-in-Picture mode via the corresponding API.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>publickey-credentials-get</b> - "  . __("Controls whether the current document is allowed to use the Web Authentication API to retrieve already stored public-key credentials, i.e. via navigator.credentials.get({publicKey: ..., ...}).",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>speaker-selection</b> - "  . __("Controls whether the current document is allowed to use the Audio Output Devices API to list and select speakers.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>sync-xhr</b> - "  . __("Controls whether the current document is allowed to make synchronous XMLHttpRequest requests.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>unoptimized-images</b> - "  . __("Controls whether the current document is allowed to download and display unoptimized images.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>unsized-media</b> - "  . __("Controls whether the current document is allowed to change the size of media elements after the initial layout is complete.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>usb</b> - "  . __("Controls whether the current document is allowed to use the WebUSB API.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>screen-wake-lock</b> - "  . __("Controls whether the current document is allowed to use Screen Wake Lock API to indicate that device should not turn off or dim the screen.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>web-share</b> - "  . __("Controls whether or not the current document is allowed to use the Navigator.share() of Web Share API to share text, links, images, and other content to arbitrary destinations of user's choice, e.g. mobile apps.",    'wp-hide-security-enhancer') ,
                                                                                                                                "<br /><b>xr-spatial-tracking</b> - "  . __("Controls whether or not the current document is allowed to use the WebXR Device API to interact with a WebXR session.",    'wp-hide-security-enhancer') ,
                                                                                                'option_documentation_url'  =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Feature-Policy'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'custom',
                                                                                                 
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                                      
                                                                    ); 
                  
                                                                    
                    return $this->component_settings;   
                }
            
            function _get_default_options()
                {
                    
                    $options    =   array ( 
                                            'enabled'                           =>  'no',
                                            'accelerometer'                     =>  array(),
                                            'ambient-light-sensor'              =>  array(),
                                            'autoplay'                          =>  array(),
                                            'battery'                           =>  array(),
                                            'camera'                            =>  array(),
                                            'display-capture'                   =>  array(),
                                            'document-domain'                   =>  array(),
                                            'encrypted-media'                   =>  array(),
                                            'execution-while-not-rendered'      =>  array(),
                                            'execution-while-out-of-viewport'   =>  array(),
                                            'fullscreen'                        =>  array(),
                                            'gamepad'                           =>  array(),
                                            'geolocation'                       =>  array(),
                                            'gyroscope'                         =>  array(),
                                            'layout-animations'                 =>  array(),
                                            'legacy-image-formats'              =>  array(),
                                            'magnetometer'                      =>  array(),
                                            'microphone'                        =>  array(),
                                            'midi'                              =>  array(),
                                            'navigation-override'               =>  array(),
                                            'oversized-images'                  =>  array(),
                                            'payment'                           =>  array(),
                                            'picture-in-picture'                =>  array(),
                                            'publickey-credentials-get'         =>  array(),
                                            'speaker-selection'                 =>  array(),
                                            'sync-xhr'                          =>  array(),
                                            'unoptimized-images'                =>  array(),
                                            'unsized-media'                     =>  array(),
                                            'usb'                               =>  array(),
                                            'screen-wake-lock'                  =>  array(),
                                            'web-share'                         =>  array(),
                                            'xr-spatial-tracking'               =>  array()
                                            );
                    return $options;
                }
                
            function _init_permissions_policy( $saved_field_data )
                {
                    
                }
            
               
            function _module_option_html( $module_settings )
                {
                    
                    $values =   $this->wph->functions->get_site_module_saved_value( $module_settings['id'],  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    $module_settings =   shortcode_atts ( $this->_get_default_options(), (array)$values )        
                    
                    ?>
                        <div class="row xspacer header">
                            <p><?php _e('Enable Header',    'wp-hide-security-enhancer') ?></p>
                            <fieldset>
                                <label>
                                    <input type="radio" class="setting-value default-value radio" value="no" name="enabled" <?php if ( $module_settings['enabled'] == 'no' ) { ?>checked="checked"<?php } ?>> <span>No</span>
                                </label>
                                <label>
                                    <input type="radio" class="setting-value radio" value="yes" name="enabled" <?php if ( $module_settings['enabled'] == 'yes' ) { ?>checked="checked"<?php } ?>> <span>Yes</span>
                                </label>                                                                
                            </fieldset>
                        </div>
                       
                        <?php
                       
                        unset ( $module_settings['enabled'] );
                        
                        foreach ( $module_settings as $option_key  =>  $option_data )
                            {
                                ?>
                                    <div class="separator">&nbsp;</div>
                                    <p><b><?php _e( $option_key,    'wp-hide-security-enhancer') ?></b></p>
                                    <div class="row group option-item">
                                        <fieldset>
                                            <input id="<?php echo $option_key ?>_enabled" name="<?php echo $option_key ?>[enabled]" type="checkbox" class="setting-value" value="yes" <?php if ( ! empty ( $option_data['enabled'] ) ) { ?>checked="checked"<?php } ?>> <label for="<?php echo $option_key ?>_enabled"><?php _e ( 'Enabled',  'wp-hide-security-enhancer') ?></label>
                                        </fieldset>
                                        <fieldset>
                                            <label>
                                                <select name="<?php echo $option_key ?>[selection]">
                                                    <option value="*" <?php if ( ! empty ( $option_data['selection'] )  &&  $option_data['selection']   ==  '*' ) { ?>selected="selected"<?php } ?>>*</option>
                                                    <option value="self" <?php if ( ! empty ( $option_data['selection'] )  &&  $option_data['selection']   ==  'self' ) { ?>selected="selected"<?php } ?>>self</option>
                                                    <option value="none" <?php if ( ! empty ( $option_data['selection'] )  &&  $option_data['selection']   ==  'none' ) { ?>selected="selected"<?php } ?>>none</option>
                                                    <option value="origins" <?php if ( ! empty ( $option_data['selection'] )  &&  $option_data['selection']   ==  'origins' ) { ?>selected="selected"<?php } ?>>origin(s)</option>
                                                </select>
                                            </label>                                                                
                                        </fieldset>
                                        <fieldset>
                                            <input style="display: none;" type="text" id="<?php echo $option_key ?>_urls" name="<?php echo $option_key ?>[urls]" value="<?php if ( ! empty ( $option_data['urls'] ) ) { echo htmlspecialchars( $option_data['urls'] ); } ?>">
                                        </fieldset>
                                    </div>
                                    
                                <?php
                            }
                            
                        ?>
                        <script type='text/javascript'>
                            jQuery( '.option-item select' ).each(function() {
                                  var val   =   jQuery( this ).val();
                                  if ( val == 'self'    ||  val == 'origins' )
                                    jQuery(this).closest('.option-item').find('input[type="text"]').show();
                                });
                                
                            jQuery( '.option-item select' ).on('change', function() {
                                  var val   =   jQuery( this ).val();
                                  if ( val == 'self'    ||  val == 'origins' )
                                    jQuery(this).closest('.option-item').find('input[type="text"]').show('fast');
                                    else
                                    jQuery(this).closest('.option-item').find('input[type="text"]').hide('fast');
                                });
                        </script>
                        <?php

                }
                
                
            function _module_option_processing( $field_name )
                {
                    
                    $results            =   array();
                    
                    $directive_options  =   array(
                                                    'enabled',
                                                    'selection',
                                                    'urls'
                                                    );
                    
                    $module_settings =   shortcode_atts ( $this->_get_default_options(), array() );
                    foreach ( $module_settings   as  $setting_name  =>  $setting_value )
                        {
                            if ( ! isset ( $_POST[ $setting_name ] ) )
                                continue;
                            
                            if ( is_array ( $_POST[ $setting_name ] ) )
                                {
                                    $values  =   preg_replace( '/[^a-zA-Z0-9-_\*\:\. \/]/m' , '', $_POST[ $setting_name ] );
                                    
                                    if ( ! is_array ( $values ) )
                                        continue;
                                        
                                    foreach ( $values   as  $key    =>  $value )
                                        {
                                            if ( array_search ( $key , $directive_options  )  === FALSE )
                                                unset ( $values[ $key ] );
                                        }
                                    
                                    $module_settings[ $setting_name ]   =   array_filter( $values );
                                }
                                else
                                {    
                                    $value  =   preg_replace( '/[^a-zA-Z0-9-_\:\.]/m' , '', $_POST[ $setting_name ] );
                                    if ( empty ( $value ) )
                                        continue;
                                        
                                    $module_settings[ $setting_name ]   =   $value;
                                }
                        }
                                        
                    $results['value']   =   $module_settings;
                       
                    return $results;
                    
                }
                
                    
            function _callback_saved_permissions_policy( $saved_field_data )
                {
                    
                    if ( empty ( $saved_field_data ) ||  ! is_array ( $saved_field_data ) || ! isset ( $saved_field_data['enabled'] ) || $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite_line                            =  '';
                    
                    $all_options    =   $saved_field_data;
                    unset ( $all_options['enabled'] );
                    
                    foreach ( $all_options  as $option_key  =>  $option )
                        {
                            if ( empty ( $option['enabled'] )   ||  $option['enabled']  !=  'yes' )
                                continue;
                            
                            $option_rewrite =   '';
                                         
                            $option_rewrite    .=  $option_key . '=(';
                                
                            if ( $option['selection']   !=  'origins'   &&  $option['selection']   !=  'none' )
                                $option_rewrite    .=  " '" . $option['selection'] . "'";
                            
                            if ( $option['selection']   ==  'self'   ||  $option['selection']   ==  'origins' )
                                {
                                    $urls   =   trim ( $option['urls'] );
                                    $urls   =   explode ( " ", $urls );
                                    $urls   =   array_map ( 'trim', $urls );
                                        
                                    $option_rewrite    .=  " '" . implode ( "' '", $urls )  . "' ";
                                }
                                
                            $option_rewrite     =   rtrim ( $option_rewrite ) . "), ";
                            $rewrite_line    .=  $option_rewrite;
                        }
                        
                    $rewrite_line    =   trim ( $rewrite_line );
                    $rewrite_line    =   rtrim ( $rewrite_line, ',' );     
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {                            
                            $rewrite    =  "\n" . '        Header set Permissions-Policy "' . $rewrite_line .'"';
                            
                            $processing_response['type']    =   'header';
                        }
                        
                    if( $this->wph->server_nginx_config   === TRUE )
                        {  
                            $rewrite        =   array();
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $rewrite_list['type']        =   'header';
                            $rewrite_list['description'] =   '';
                            
                            $rewrite_data  =   '         add_header Permissions-Policy "' . $rewrite_line . '";';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                               
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                      
                    return  $processing_response;
                    
                } 
            

        }
?>