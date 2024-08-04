<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_security_header_content_security_policy extends WPH_module_component
        {
            
            private $headers = array ();
            
            function get_component_title()
                {
                    return "Content-Security-Policy";
                }
                                    
            function get_module_component_settings()
                {
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'content_security_policy',
                                                                    'label'         =>  __('Content-Security-Policy',    'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Content-Security-Policy',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The HTTP Content-Security-Policy response header allows web site administrators to control resources the user agent is allowed to load for a given page. With a few exceptions, policies mostly involve specifying server origins and script endpoints. This helps guard against cross-site scripting attacks (Cross-site_scripting).",    'wp-hide-security-enhancer') .
                                                                                                                                
                                                                                                                                "<br /><br />" . __("Directives:",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>default-src</b> - "  . __("Serves as a fallback for the other fetch directives..",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>child-src</b> - "  . __("Defines the valid sources for web workers and nested browsing contexts loaded using elements such as &#60;frame&#62; and &#60;iframe&#62;.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>connect-src</b> - "  . __("Restricts the URLs which can be loaded using script interfaces.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>font-src</b> - "  . __("Specifies valid sources for fonts loaded using @font-face.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>frame-src</b> - "  . __("Specifies valid sources for nested browsing contexts loading using elements such as &#60;frame&#62; and &#60;iframe&#62;.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>img-src</b> - "  . __("Specifies valid sources of images and favicons..",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>manifest-src</b> - "  . __("Specifies valid sources of application manifest files.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>media-src</b> - "  . __("Specifies valid sources for loading media using the &#60;audio&#62; , &#60;video&#62; and &#60;track&#62; elements.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>object-src</b> - "  . __("Specifies valid sources for the &#60;object&#62;, &#60;embed&#62;, and &#60;applet&#62; elements.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>prefetch-src</b> - "  . __("Specifies valid sources for nested browsing contexts loading using elements such as &#60;frame&#62; and &#60;iframe&#62;.",    'wp-hide-security-enhancer') . 
                                                                                                                                "<br /><b>script-src</b> - "  . __("Specifies valid sources for JavaScript.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>script-src-elem</b> - "  . __("Specifies valid sources for JavaScript &#60;script&#62; elements.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>script-src-attr</b> - "  . __("Specifies valid sources for JavaScript inline event handlers.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>style-src</b> - "  . __("Specifies valid sources for stylesheets.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>style-src-elem</b> - "  . __("Specifies valid sources for stylesheets &#60;style&#62; elements and &#60;link&#62; elements with rel='stylesheet'. ",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>style-src-attr</b> - "  . __("Specifies valid sources for inline styles applied to individual DOM elements.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>worker-src</b> - "  . __("Specifies valid sources for Worker, SharedWorker, or ServiceWorker scripts.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>base-uri</b> - "  . __("Restricts the URLs which can be used in a document's &#60;base&#62; element. ",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>sandbox</b> - "  . __("Enables a sandbox for the requested resource similar to the &#60;iframe&#62; sandbox attribute.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>form-action</b> - "  . __("Restricts the URLs which can be used as the target of a form submissions from a given context.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>frame-ancestors</b> - "  . __("Specifies valid parents that may embed a page using &#60;frame&#62;, &#60;iframe&#62;, &#60;object&#62;, &#60;embed&#62;, or &#60;applet&#62;. ",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>navigate-to</b> - "  . __("Restricts the URLs to which a document can initiate navigation by any means, including &#60;form&#62; (if form-action is not specified), &#60;a&#62;, window.location, window.open, etc.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>report-uri</b> - "  . __("The directive instructs the user agent to report attempts to violate the Content Security Policy. These violation reports consist of JSON documents sent via an HTTP POST request to the specified URI.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>report-to</b> - "  . __("Fires a SecurityPolicyViolationEvent. These violation reports consist of JSON documents sent via an HTTP POST request to the specified URI.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>require-sri-for</b> - "  . __("Requires the use of SRI for scripts or styles on the page.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>require-trusted-types-for</b> - "  . __("Enforces Trusted Types at the DOM XSS injection sinks.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>trusted-types</b> - "  . __("Used to specify an allow-list of Trusted Types policies. Trusted Types allows applications to lock down DOM XSS injection sinks to only accept non-spoofable, typed values in place of strings.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>upgrade-insecure-requests</b> - "  . __("Instructs user agents to treat all of a site's insecure URLs (those served over HTTP) as though they have been replaced with secure URLs (those served over HTTPS). This directive is intended for web sites with large numbers of insecure legacy URLs that need to be rewritten.",    'wp-hide-security-enhancer') .
                                                                                                                                
                                                                                                                                "<br />&nbsp;<br />" . __("Values:",    'wp-hide-security-enhancer') . 
                                                                                                                                "<br /><b>none</b> - "  . __("Won't allow loading of any resources.",    'wp-hide-security-enhancer') . 
                                                                                                                                "<br /><b>self</b> - "  . __("Only allow resources from the current origin.",    'wp-hide-security-enhancer') . 
                                                                                                                                "<br /><b>strict-dynamic</b> - "  . __("The trust granted to a script in the page due to an accompanying nonce or hash is extended to the scripts it loads.",    'wp-hide-security-enhancer') . 
                                                                                                                                "<br /><b>report-sample</b> - "  . __("Require a sample of the violating code to be included in the violation report.",    'wp-hide-security-enhancer') . 
                                                                                                                                "<br /><b>unsafe-inline</b> - "  . __("Allow use of inline resources.",    'wp-hide-security-enhancer') . 
                                                                                                                                "<br /><b>unsafe-eval</b> - "  . __("Allow use of dynamic code evaluation such as eval, setImmediate , and window.execScript.",    'wp-hide-security-enhancer') . 
                                                                                                                                "<br /><b>Host</b> - "  . __("Only allow loading of resources from a specific host, with optional scheme, port, and path. e.g. example.com, *.example.com, https://*.example.com:12/path/to/file.js.",    'wp-hide-security-enhancer') . 
                                                                                                                                "<br />- "  . __("Path parts in the CSP that end in / match any path they are a prefix of. e.g. example.com/api/ will match URLs like example.com/api/users/new.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />- "  . __("Other path parts in the CSP are matched exactly e.g. example.com/file.js will match http://example.com/file.js and https://example.com/file.js, but not https://example.com/file.js/file2.js.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><b>Scheme</b> - "  . __("Only allow loading of resources over a specific scheme e.g. https:, http:, data:, mediastream:, blob:, filesystem:",    'wp-hide-security-enhancer').
                                                                                                                                "<br /><b>Nonce / Hash</b> - "  . __("Alternatively, a JavaScript hash can be used instead.  The CSP Level 2 specification allows to compute the SHA-256 / sha384 / sha512 for the inline JavaScript code.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Content-Security-Policy'
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
                                            'enabled'           =>  'no',
                                            'default-src'       =>  array(),
                                            'child-src'         =>  array(),
                                            'connect-src'       =>  array(),
                                            'font-src'          =>  array(),
                                            'frame-src'         =>  array(),
                                            'img-src'           =>  array(),
                                            'font-src'          =>  array(),
                                            'frame-src'         =>  array(),
                                            'img-src'           =>  array(),
                                            'manifest-src'      =>  array(),
                                            'media-src'         =>  array(),
                                            'object-src'        =>  array(),
                                            'prefetch-src'      =>  array(),
                                            'script-src'        =>  array(),
                                            'script-src-elem'   =>  array(),
                                            'script-src-attr'   =>  array(),
                                            'style-src'         =>  array(),
                                            'style-src-elem'    =>  array(),
                                            'style-src-attr'    =>  array(),
                                            'worker-src'        =>  array(),
                                            'base-uri'          =>  array(),
                                            'report-uri'        =>  '',
                                            'report-to'         =>  '',
                                            'sandbox'           =>  array(),
                                            'require-sri-for'   =>  array(),
                                            'block-all-mixed-content'   =>  '',
                                            'upgrade-insecure-requests' =>  ''
                                            );
                    return $options;
                }
                
            function _init_content_security_policy( $saved_field_data )
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
                        
                        
                            $directives =   array(
                                                    'default-src',
                                                    'child-src',
                                                    'connect-src',
                                                    'font-src',
                                                    'frame-src',
                                                    'img-src',
                                                    'manifest-src',
                                                    'media-src',
                                                    'object-src',
                                                    'prefetch-src',
                                                    'script-src',
                                                    'script-src-elem',
                                                    'script-src-attr',
                                                    'style-src',
                                                    'style-src-elem',
                                                    'style-src-attr',
                                                    'worker-src',
                                                    'base-uri'
                                                    );
                        
                            $options  = array (
                                                array ( 'checkbox' ,  '*' ),
                                                array ( 'checkbox' ,  'none' ),
                                                array ( 'checkbox' ,  'self' ),
                                                array ( 'checkbox' ,  'strict-dynamic' ),
                                                array ( 'checkbox' ,  'report-sample' ),
                                                array ( 'checkbox' ,  'unsafe-inline' ),
                                                array ( 'checkbox' ,  'unsafe-eval' ),
                                                array ( 'checkbox' ,  'http:' ),
                                                array ( 'checkbox' ,  'https:' ),
                                                array ( 'checkbox' ,  'data:' ),
                                                array ( 'checkbox' ,  'mediastream:' ),
                                                array ( 'checkbox' ,  'blob:' ),
                                                array ( 'checkbox' ,  'filesystem:' ),
                                                array ( 'text'     ,  'host' ),
                                                array ( 'text'     ,  'nonce / hash' ) 
                                                );
                        
                            
                            foreach ( $directives   as  $directive )
                                {
                                    ?>
                                        <div class="separator">&nbsp;</div>
                                        <p><b><?php _e( $directive,    'wp-hide-security-enhancer') ?></b></p>
                                    <?php
                                    
                                    foreach ( $options as $data )
                                        {
                                            $type   =   $data[0];
                                            $option =   $data[1];
                                            
                                            ?>
                                                <div class="row group">
                                                    <fieldset>
                                                        <?php
                                                            
                                                            switch ( $type )
                                                                {
                                                                    case 'checkbox' :
                                                                                        ?>
                                                                                        <input id="<?php echo $directive ?>_<?php echo $option ?>" name="<?php echo $directive ?>[<?php echo $option ?>]" type="checkbox" class="setting-value" value="<?php echo $option ?>" <?php if ( isset ( $module_settings[$directive][$option] ) && ! empty ( $module_settings[$directive][$option] ) ) { ?>checked="checked"<?php } ?>> <label for="<?php echo $directive ?>_<?php echo $option ?>"><?php echo $option ?></label>
                                                                                        <?php
                                                                                        break;   
                                                                    case 'text' :
                                                                                        ?>
                                                                                        <label><?php echo $option ?></label>
                                                                                        <input name="<?php echo $directive ?>[<?php echo $option ?>]" type="text" class="setting-value" value="<?php if ( isset ( $module_settings[$directive][$option] ) && ! empty ( $module_settings[$directive][$option] ) ) { echo htmlspecialchars ( $module_settings[$directive][$option] ); } ?>">
                                                                                        <?php
                                                                                        break;
                                                                    
                                                                    
                                                                }
                                                        
                                                        ?>
                                                    </fieldset>
                                                </div>
                                            <?php
                                        }
                                }
                                
                            
                            ?>
                                <div class="separator">&nbsp;</div>
                                <p><b><?php _e( 'report-uri',    'wp-hide-security-enhancer') ?></b></p>
                                <div class="row group">
                                    <fieldset>
                                        <input name="report-uri" type="text" class="setting-value" value="<?php if ( isset ( $module_settings['report-uri'] ) && ! empty ( $module_settings['report-uri'] ) ) { echo htmlspecialchars ( $module_settings['report-uri'] ); } ?>">
                                    </fieldset>
                                </div>
                                
                                <p><b><?php _e( 'report-to',    'wp-hide-security-enhancer') ?></b></p>
                                <div class="row group">
                                    <fieldset>
                                        <input name="report-to" type="text" class="setting-value" value="<?php if ( isset ( $module_settings['report-to'] ) && ! empty ( $module_settings['report-to'] ) ) { echo htmlspecialchars ( $module_settings['report-to'] ); } ?>">
                                    </fieldset>
                                </div>
                                <div class="separator">&nbsp;</div>
                                <p><b><?php _e( 'sandbox',    'wp-hide-security-enhancer') ?></b></p>
                                <?php
                                
                                    $options  = array (
                                                array ( 'checkbox' ,  'allow-downloads' ),
                                                array ( 'checkbox' ,  'allow-downloads-without-user-activation' ),
                                                array ( 'checkbox' ,  'allow-forms' ),
                                                array ( 'checkbox' ,  'allow-modals' ),
                                                array ( 'checkbox' ,  'allow-orientation-lock' ),
                                                array ( 'checkbox' ,  'allow-pointer-lock' ),
                                                array ( 'checkbox' ,  'allow-popups' ),
                                                array ( 'checkbox' ,  'allow-popups-to-escape-sandbox' ),
                                                array ( 'checkbox' ,  'allow-presentation' ),
                                                array ( 'checkbox' ,  'allow-same-origin' ),
                                                array ( 'checkbox' ,  'allow-scripts' ),
                                                array ( 'checkbox' ,  'allow-storage-access-by-user-activation' ),
                                                array ( 'checkbox' ,  'allow-top-navigation' ),
                                                array ( 'checkbox' ,  'allow-top-navigation-by-user-activation' )
                                                );
                                    
                                    $directive = 'sandbox';
                                    
                                    foreach ( $options as $data )
                                        {
                                            
                                            $type   =   $data[0];
                                            $option =   $data[1];
                                                
                                            ?>
                                                <div class="row group">
                                                    <fieldset>
                                                        <?php
                                                                
                                                            switch ( $type )
                                                                {
                                                                    case 'checkbox' :
                                                                                        ?>
                                                                                        <input id="<?php echo $directive ?>_<?php echo $option ?>" name="<?php echo $directive ?>[<?php echo $option ?>]" type="checkbox" class="setting-value" value="<?php echo $option ?>" <?php if ( isset ( $module_settings[$directive][$option] ) && ! empty ( $module_settings[$directive][$option] ) ) { ?>checked="checked"<?php } ?>> <label for="<?php echo $directive ?>_<?php echo $option ?>"><?php echo $option ?></label>
                                                                                        <?php
                                                                                        break;   
                                                                    case 'text' :
                                                                                        ?>
                                                                                        <label><?php echo $option ?></label>
                                                                                        <input name="<?php echo $directive ?>[<?php echo $option ?>]" type="text" class="setting-value" value="<?php if ( isset ( $module_settings[$directive][$option] ) && ! empty ( $module_settings[$directive][$option] ) ) { echo htmlspecialchars ( $module_settings[$directive][$option] ); } ?>">
                                                                                        <?php
                                                                                        break;
                                                                    
                                                                    
                                                                }
                                                        
                                                        ?>
                                                    </fieldset>
                                                </div>
                                            <?php
                                        }
                            
                                ?>
                                <div class="separator">&nbsp;</div>
                                <p><b><?php _e( 'require-sri-for',    'wp-hide-security-enhancer') ?></b></p>
                                <?php
                                
                                    $options  = array (
                                                array ( 'checkbox' ,  'script' ),
                                                array ( 'checkbox' ,  'style' )
                                                );
                                    
                                    $directive = 'require-sri-for';
                                    
                                    foreach ( $options as $data )
                                        {
                                            
                                            $type   =   $data[0];
                                            $option =   $data[1];
                                                
                                            ?>
                                                <div class="row group">
                                                    <fieldset>
                                                        <?php
                                                                
                                                            switch ( $type )
                                                                {
                                                                    case 'checkbox' :
                                                                                        ?>
                                                                                        <input id="<?php echo $directive ?>_<?php echo $option ?>" name="<?php echo $directive ?>[<?php echo $option ?>]" type="checkbox" class="setting-value" value="<?php echo $option ?>" <?php if ( isset ( $module_settings[$directive][$option] ) && ! empty ( $module_settings[$directive][$option] ) ) { ?>checked="checked"<?php } ?>> <label for="<?php echo $directive ?>_<?php echo $option ?>"><?php echo $option ?></label>
                                                                                        <?php
                                                                                        break;   
                                                                    case 'text' :
                                                                                        ?>
                                                                                        <label><?php echo $option ?></label>
                                                                                        <input name="<?php echo $directive ?>[<?php echo $option ?>]" type="text" class="setting-value" value="<?php if ( isset ( $module_settings[$directive][$option] ) && ! empty ( $module_settings[$directive][$option] ) ) { echo htmlspecialchars ( $module_settings[$directive][$option] ); } ?>">
                                                                                        <?php
                                                                                        break;
                                                                    
                                                                    
                                                                }
                                                        
                                                        ?>
                                                    </fieldset>
                                                </div>
                                            <?php
                                        }
                            
                                ?>
                                <div class="separator">&nbsp;</div>
                                <p><b><?php _e( 'block-all-mixed-content',    'wp-hide-security-enhancer') ?></b></p>
                                <div class="row group">
                                    <fieldset>
                                        <input id="block-all-mixed-content" name="block-all-mixed-content" type="checkbox" class="setting-value" value="block-all-mixed-content" <?php if ( isset ( $module_settings['block-all-mixed-content'] ) && ! empty ( $module_settings['block-all-mixed-content'] ) ) { ?>checked="checked"<?php } ?>> <label for="block-all-mixed-content"><?php _e ( 'Yes',  'wp-hide-security-enhancer') ?></label>
                                    </fieldset>
                                </div>
                                <div class="separator">&nbsp;</div>
                        
                                <p><b><?php _e( 'upgrade-insecure-requests',    'wp-hide-security-enhancer') ?></b></p>
                                <div class="row group">
                                    <fieldset>
                                        <input id="upgrade-insecure-requests" name="upgrade-insecure-requests" type="checkbox" class="setting-value" value="upgrade-insecure-requests" <?php if ( isset ( $module_settings['upgrade-insecure-requests'] ) && ! empty ( $module_settings['upgrade-insecure-requests'] ) ) { ?>checked="checked"<?php } ?>> <label for="upgrade-insecure-requests"><?php _e ( 'Yes',  'wp-hide-security-enhancer') ?></label>
                                    </fieldset>
                                </div>
                        
                        
                    <?php
                }
                
                
            function _module_option_processing( $field_name )
                {
                    
                    $results            =   array();
                    
                    $module_settings =   shortcode_atts ( $this->_get_default_options(), array() );
                    foreach ( $module_settings   as  $setting_name  =>  $setting_value )
                        {
                            if ( ! isset ( $_POST[ $setting_name ] ) )
                                continue;
                            
                            if ( is_array ( $_POST[ $setting_name ] ) )
                                {
                                    $values  =   preg_replace( '/[^a-zA-Z0-9-_\*\:\.\/ ]/m' , '', $_POST[ $setting_name ] );
                                    
                                    $module_settings[ $setting_name ]   =   array_filter( $values );
                                }
                                else
                                {    
                                    $value  =   preg_replace( '/[^a-zA-Z0-9-_\:\.\/ ]/m' , '', $_POST[ $setting_name ] );
                                    if ( empty ( $value ) )
                                        continue;
                                        
                                    $module_settings[ $setting_name ]   =   $value;
                                }
                        }
                                        
                    $results['value']   =   $module_settings;
                       
                    return $results;
                    
                }
                
                    
            function _callback_saved_content_security_policy($saved_field_data)
                {
                    
                    if ( empty ( $saved_field_data ) ||  ! is_array ( $saved_field_data ) || ! isset ( $saved_field_data['enabled'] ) || $saved_field_data['enabled']   ==  'no' )
                        return FALSE;
                        
                    $processing_response    =   array();
                                                         
                    $rewrite_line                            =  '';
                    
                    $all_options    =   $saved_field_data;
                    unset ( $all_options['enabled'] );
                    
                    foreach ( $all_options  as $option_key  =>  $option )
                        {
                            if ( is_array ( $option ) )
                                {
                                    if ( count ( $option )    <   1 )
                                        continue;
                                    
                                    $rewrite_line    .= " " . $option_key; 
                                    
                                    foreach ( $option   as $item_key    =>  $item )
                                        {
                                            if ( substr( $item_key, - strlen( ':' )) === ':' )
                                                $rewrite_line    .= ' ' . $item_key;
                                                else if ( in_array ( $item_key, array ( 'host' ) ) )
                                                $rewrite_line    .=  " " . $item;
                                                else if ( in_array ( $item_key, array ( 'nonce / hash' ) ) )
                                                {
                                                    $items  =   explode ( " ", $item );
                                                    $rewrite_line    .=  " '" . implode ( "' '" , $items ) . "'" ;
                                                }
                                                else    
                                                $rewrite_line    .=  " '" . $item_key . "'";
                                        }
                                    
                                    $rewrite_line    .=  ';';
                                    
                                    continue;
                                }
                            
                            if ( is_string ( $option )  &&   ! empty ( $option ) )
                                {
                                    if (  in_array ( $option_key, array ( 'block-all-mixed-content', 'upgrade-insecure-requests' ) ) )
                                        $rewrite_line    .=  " " . $option . ';';
                                        else
                                        $rewrite_line    .=  " " . $option_key . " " . $option . ';';
                                    continue;
                                }
                        }
                    
                    $rewrite_line    =   trim ( $rewrite_line );
                    $rewrite_line    =   rtrim ( $rewrite_line, ';' );   
                    
                    
                                        
                    if($this->wph->server_htaccess_config   === TRUE)                               
                        {
                            $rewrite    =  "\n" . '        Header set Content-Security-Policy "' . $rewrite_line . '"';
                            
                            $processing_response['type']    =   'header';
                        }
                        
                    if( $this->wph->server_nginx_config   === TRUE )
                        {  
                            $rewrite        =   array();
                            $rewrite_list   =   array();
                            $rewrite_rules  =   array();
                            
                            $rewrite_list['type']        =   'header';
                            $rewrite_list['description'] =   '';
                            
                            $rewrite_data  =   '         add_header Content-Security-Policy "' . $rewrite_line . '";';
                            
                            $rewrite_rules[]            =   $rewrite_data;
                            $rewrite_list['data']       =   $rewrite_rules;
                            
                            $rewrite[]  =   $rewrite_list;
                               
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                                          
                    return  $processing_response;
                    
                } 
            

        }
?>