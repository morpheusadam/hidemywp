<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_headers extends WPH_module_component
        {
            function get_component_title()
                {
                    return "Headers";
                }
                                    
            function get_module_component_settings()
                {
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'remove_header_link',
                                                                    'label'         =>  __('Remove Link Header',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove Link Header being set as default by WordPress which outputs the site JSON url.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove Link Header',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("HTTP header fields are components of the header section of a request and response messages in the Hypertext Transfer Protocol (HTTP). They define the operating parameters of an HTTP transaction.",    'wp-hide-security-enhancer') .
                                                                                                                                    "<br /><br />" . __("Sample header:",    'wp-hide-security-enhancer') .
                                                                                                                                    "<br /><code>Link: &lt;http://-domain-name-/wp-json/&gt;; rel=&quot;https://api.w.org/&quot;</code>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/request-headers/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  70
                                                                    );
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'remove_x_powered_by',
                                                                    'label'         =>  __('Remove X-Powered-By Header',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove X-Powered-By Header if being set.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove X-Powered-By Header',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Sample header:",    'wp-hide-security-enhancer') .
                                                                                                                                    "<br /><code>x-powered-by: 'W3 Total Cache/0.9.5'</code>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/request-headers/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  70
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'remove_x_pingback',
                                                                    'label'         =>  __('Remove X-Pingback Header',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove X-Pingback Header if being set.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove X-Pingback Header',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Pingback is one of four types of linkback methods for Web authors to request notification when somebody links to one of their documents. This enables authors to keep track of who is linking to, or referring to their articles. Pingback-enabled resources must either use an X-Pingback header or contain a element to the XML-RPC script.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/request-headers/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  70
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'remove_custom_header',
                                                                    'label'         =>  __('Remove Custom Header',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove a Custom Header if being set. ', 'wp-hide-security-enhancer') ,
                                                                                            
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove Custom Header',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Custom HTTP headers are commonly meant to provide additional information that may be pertinent to a web developer, or for troubleshooting purposes. These headers often begin with <code>X-</code>",    'wp-hide-security-enhancer') .
                                                                                                                                "<br /><br /><span class='important'>" . __("Use with caution, removing specific headers produce malfunction to the site. Generally all headers which stats with X are safe to remove.",    'wp-hide-security-enhancer') ."</span>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/request-headers/'
                                                                                                ),                    
                                                                    
                                                                    'input_type'    =>  'custom',
                                                                    'default_value' =>  array(),
                                                                    
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                    
                                                                    'processing_order'  =>  70
                                                                    );
                                                                    
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'remove_server_signature',
                                                                    'label'         =>  __('Remove Server Signature',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove the Server Signature. ', 'wp-hide-security-enhancer') ,
                                                                                            
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove Server Signature',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("As a default, a server outputs a Server Header which outputs details on used system configuration:",    'wp-hide-security-enhancer') ."</span>" .
                                                                                                                                "<br/ ><br/ ><img src='".  WPH_URL . "/assets/images/help/server-signature.jpg' /> " .
                                                                                                                                "<br/ ><br/ >" . __("This option helps to prevent the details from being shown on the front side. This works for Apache server type and compatibles.",    'wp-hide-security-enhancer') ."</span>" ,
                                                                                                
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/request-headers/'
                                                                                                ),                    
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
        
                                                    
                                                                    'processing_order'  =>  70
                                                                    );
                                                                    
                    return $this->component_settings;   
                }
                
                
            function _init_remove_header_link( $saved_field_data )
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    if ( ! $this->ReInit )
                        remove_action( 'template_redirect', 'rest_output_link_header', 11, 0 );    
                    
                }
                
            function _init_remove_x_powered_by($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
                    
                }
                
            function _callback_saved_remove_x_powered_by($saved_field_data)
                {
                    $processing_response    =   array();
                    
                    
                    //process all headers through the same item, to avoid multiple IfModule lines
                    $headers    =   array();
                    
                    if($this->wph->server_htaccess_config   === TRUE)
                        {
                            $item_option    =   $this->wph->functions->get_site_module_saved_value('remove_x_powered_by',  $this->wph->functions->get_blog_id_setting_to_use());
                            if ( $item_option   ==  'yes'  )
                                $headers[]  =   'Header unset X-Powered-By';
                            
                            $item_option    =   $this->wph->functions->get_site_module_saved_value('remove_x_pingback',  $this->wph->functions->get_blog_id_setting_to_use());
                            if ( $item_option   ==  'yes'  )
                                $headers[]  =   'Header unset X-Pingback';
                                
                            $item_option    =   $this->wph->functions->get_site_module_saved_value('remove_custom_header', $this->wph->functions->get_blog_id_setting_to_use());
                            if ( is_array ( $item_option )  &&  count ( $item_option )  >   0 )
                                {
                                    foreach ( $item_option as $header )
                                        {
                                            $headers[]  =   'Header unset ' . $header;
                                        }
                                }
                                                                
                            if ( count ( $headers ) >   0 )
                                {
                                    $processing_response['rewrite'] =   "\n        " . implode ( "\n        ", $headers );
                                    $processing_response['type']    =   'header';
                                }
                        }
  
                            
                    if($this->wph->server_web_config   === TRUE)
                        {
                            
                            $processing_response['rewrite'] =   '';
                        }
                                
                    return  $processing_response;   
                }
                
            
                
                
            function _module_option_html( $module_setting )
                {
                    if(!empty($module_setting['value_description'])) 
                        { 
                            ?><p class="description"><?php echo $module_setting['value_description'] ?></p><?php 
                        }
                    
                    $class          =   'replacement_field text full_width';
                    
                    ?>
                    <!-- WPH Preserve - Start -->
                    <div id="replacer_read_root" style="display: none">
                        <div class="irow"><input name="<?php echo $module_setting['id'] ?>[replaced][]" class="<?php echo $class ?>" value="" placeholder="Header to Replace" type="text">  <a class="action"  href="javascript: void(0);" onClick="WPH.replace_text_remove_row( jQuery(this).closest('.irow'))"><span alt="f335" class="close dashicons dashicons-no-alt">&nbsp;</span></a> </div>
                    </div>
                    <?php
                    
                    $values =   $this->wph->functions->get_site_module_saved_value('remove_custom_header',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    
                    if ( ! is_array($values))
                        $values =   array();
                    
                    if ( count ( $values )  >   0 )
                        {
                            foreach ( $values   as  $header)
                                {
                                    ?><div class="irow">
                                        <input name="<?php echo $module_setting['id'] ?>[replaced][]" class="<?php echo $class ?>" value="<?php echo htmlspecialchars(stripslashes( $header )) ?>" placeholder="Header to Replace" type="text">
                                        <a class="action" href="javascript: void(0);" onClick="WPH.replace_text_remove_row( jQuery(this).closest('.irow'))"><span alt="f335" class="close dashicons dashicons-no-alt">&nbsp;</span></a> 
                                    </div><?php
                                }
                        }
                                                                        
                    ?>
                        <div id="replacer_insert_root">&nbsp;</div>
                        
                        <p>
                            <button type="button" class="button" onClick="WPH.replace_text_add_row()"><?php _e( "Add New", 'wp-hide-security-enhancer' ) ?></button>
                        </p>
                        
                        <!-- WPH Preserve - Stop -->
                    <?php
                }
                
                
                
            function _module_option_processing( $field_name )
                {
                    
                    $results            =   array();
                                        
                    $data       =   $_POST['remove_custom_header'];
                    $values     =   array();
                    
                    if  ( is_array($data )  &&  count ( $data )   >   0     &&  isset($data['replaced'])  )
                        {
                            foreach(    $data['replaced']   as  $key =>  $text )
                                {
      
                                    $replaced_text  =   stripslashes($text);
                                    $replaced_text  =   trim($replaced_text);
                                                       
                                    if ( ! empty( $replaced_text ) )
                                        {
                                            $values[]  =  $replaced_text;   
                                            
                                        }
                                    
                                }
                        }
                    
                    $results['value']   =   $values;  
                    
                    return $results;
                    
                }
                
                
           
            function _callback_saved_remove_server_signature( $saved_field_data )
                {
                    
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE; 
                        
                    $processing_response    =   array();
                          
                    if($this->wph->server_htaccess_config   === TRUE)
                        {                               
                            $processing_response['rewrite'] = 'ServerSignature Off';
           
                        }
                            
                    if($this->wph->server_web_config   === TRUE)
                        {
                            //Not implemented
                        }
                    
                    if($this->wph->server_nginx_config   === TRUE)           
                        {
                            //Not Implemented
                            //Require a custom module to deply on the server https://github.com/openresty/headers-more-nginx-module#more_set_headers   
                        }
                                
                    return  $processing_response;   
                    
                }
                
        }
?>