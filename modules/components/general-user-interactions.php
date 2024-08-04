<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_user_interactions extends WPH_module_component
        {
            
            public $buffer              =   '';
            public $placeholders        =   array();
            public $placeholder_hash    =   '';
            
            function get_component_title()
                {
                    return "User Interactions";
                }
                                    
            function get_module_component_settings()
                {
                    global $wpdb;
                    
                    $wp_roles       =   get_option( $wpdb->prefix . 'user_roles');
                    $option_roles   =   array ();
                    foreach  ( $wp_roles     as  $role_slug  =>  $role )
                        $option_roles[$role_slug]   =   $role['name'];
                        
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_user_interaction_for_role',
                                                                    'label'         =>  __('Disable user interaction function',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable the following user interaction functions for the specified roles.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable user interaction function',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Disable all user interaction functions for the specified roles.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("This is useful, to avoid interfering with the logged-in user actions, like page Edit with an editor ( e.g. Elementor ), on the front-side.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'checkbox',
                                                                    'options'       =>  $option_roles,
                                                                    'default_value' =>  array(),
                                                                    
                                                                    'sanitize_type' =>  array( array ( $this->wph->functions, 'sanitize_array' ) ),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                                                                    
                    $this->component_settings[]                  =   array(
                                                                                'type'            =>  'split'
                                                                                
                                                                                );
                    
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_mouse_right_click',
                                                                    'label'         =>  __('Disable Mouse right click',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable mouse right click on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable right Mouse click',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Disable right mouse click on your pages can protect your site content from being copied.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("Some plugins, mainly visual editors, use mouse right-click, if use such code this option should be set to No.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_text_selection',
                                                                    'label'         =>  __('Disable Text Selection',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable Text Selection on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable Text Selection',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("When the option is active, the text selection on pages is not possible.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("This is useful when don't want the site texts to be copied.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                    
                    
                                                                    
                    $this->component_settings[]                  =      array(
                                                                    'id'            =>  'disable_copy',
                                                                    'label'         =>  __('Disable Copy',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable text copy on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable Copy',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Through this option, the browser copy functionality is disabled.",    'wp-hide-security-enhancer') ,
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_cut',
                                                                    'label'         =>  __('Disable Cut',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable text cut on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable Cut',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Through this option, the browser cut functionality is disabled.",    'wp-hide-security-enhancer') ,
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_paste',
                                                                    'label'         =>  __('Disable Paste',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable text paste on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable Paste',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Through this option, the browser paste functionality is disabled.",    'wp-hide-security-enhancer') ,
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_print',
                                                                    'label'         =>  __('Disable Print',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable Print function on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable Print',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("When using the option, the browser Print dialogue is not available so a site print is disabled.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_print_screen',
                                                                    'label'         =>  __('Disable Print Screen',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable Print Screen function on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable Print Screen',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The Print Screen function captures an image of the entire screen and copies it to the Clipboard in the computer's memory.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("If the functionality is not required, the option helps to disable it.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_developer_tools',
                                                                    'label'         =>  __('Disable Developer Tools',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable the browser Developr Tools on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable Developer Tools',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Every modern web browser includes a powerful tool called Developer Tools. Through the application, a user can inspect currently-loaded HTML, CSS and JavaScript.",    'wp-hide-security-enhancer') .
                                                                                                                                "<br />" . __("To prevent the user from deeply checking into your site architecture, the functionality can be disabled using this option. ",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_view_source',
                                                                    'label'         =>  __('Disable View Source',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable the browser view source on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable View Source',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The page source is an HTML set of tags code. An HTML tag is an element that, along with CSS and JavaScript, tells the Web browser what to do and how to display the text and images.",    'wp-hide-security-enhancer') ,
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'disable_drag_drop',
                                                                    'label'         =>  __('Disable Drag / Drop',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Disable the browser drag and drop for images on your pages.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Disable Drag / Drop',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The Drag and Drop operation describes the action o selecting an object or text on the page and moving it to a different area.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/user-interactions/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );
                                                                    
                    return $this->component_settings;   
                }
                
          
            
            
            function _init_disable_mouse_right_click( $saved_field_data )
                {
                    $disable_user_interaction_for_role    =   $this->wph->functions->get_site_module_saved_value('disable_user_interaction_for_role', $this->wph->functions->get_blog_id_setting_to_use() );
                    
                    if ( is_array ( $disable_user_interaction_for_role )    &&  count ( $disable_user_interaction_for_role )    >   0   &&  is_user_logged_in() )
                        {
                            $current_user   = wp_get_current_user();
                            $user_role      =   isset(  $current_user->roles[0] ) ?     $current_user->roles[0] :   '';
                            if ( ! empty ( $user_role ) && in_array ( $user_role, $disable_user_interaction_for_role ) )
                                return;
                        }
                    
                    if ( ! $this->ReInit )
                        {
                            add_action( 'wp_enqueue_scripts',   array ( $this,  'wp_enqueue_scripts' ) );        
                            add_filter( 'wp_footer' , array ( $this,  'disable_right_mouse_click' ) );  
                        }
                }
            
                
            function wp_enqueue_scripts()
                {
                    
                    $disable_developer_tools    =   $this->wph->functions->get_site_module_saved_value('disable_developer_tools', $this->wph->functions->get_blog_id_setting_to_use() );
                    
                    if ( $disable_developer_tools == 'no' )
                        return;
                        
                    wp_register_script('devtools-detect', WPH_URL . '/assets/js/devtools-detect.js');
                    wp_enqueue_script ( 'devtools-detect' );
                    
                }
                
                    
            function disable_right_mouse_click()
                {
                    
                    $disable_mouse_right_click  =   $this->wph->functions->get_site_module_saved_value('disable_mouse_right_click', $this->wph->functions->get_blog_id_setting_to_use() );
                    $disable_text_selection     =   $this->wph->functions->get_site_module_saved_value('disable_text_selection', $this->wph->functions->get_blog_id_setting_to_use() );
                    $disable_copy               =   $this->wph->functions->get_site_module_saved_value('disable_copy', $this->wph->functions->get_blog_id_setting_to_use() );
                    $disable_cut                =   $this->wph->functions->get_site_module_saved_value('disable_cut', $this->wph->functions->get_blog_id_setting_to_use() );
                    $disable_paste              =   $this->wph->functions->get_site_module_saved_value('disable_paste', $this->wph->functions->get_blog_id_setting_to_use() );
                    $disable_print              =   $this->wph->functions->get_site_module_saved_value('disable_print', $this->wph->functions->get_blog_id_setting_to_use() );
                    $disable_print_screen       =   $this->wph->functions->get_site_module_saved_value('disable_print_screen', $this->wph->functions->get_blog_id_setting_to_use() );
                    $disable_developer_tools    =   $this->wph->functions->get_site_module_saved_value('disable_developer_tools', $this->wph->functions->get_blog_id_setting_to_use() );
                    $disable_view_source        =   $this->wph->functions->get_site_module_saved_value('disable_view_source', $this->wph->functions->get_blog_id_setting_to_use() );
                    $disable_drag_drop          =   $this->wph->functions->get_site_module_saved_value('disable_drag_drop', $this->wph->functions->get_blog_id_setting_to_use() );

                        
                    if ( $disable_mouse_right_click == 'no' &&  $disable_text_selection ==  'no'    &&  $disable_copy ==  'no'    &&  $disable_cut ==  'no'   &&  $disable_paste ==  'no'   &&  $disable_print  ==  'no'    &&  $disable_print_screen   ==  'no'    &&  $disable_developer_tools    ==  'no'    &&  $disable_view_source    ==  'no'    &&  $disable_drag_drop  ==  'no' )
                        return;
                    
                    if ( $disable_print ==  'yes' ) { ?>
                    <style type="text/css" media="print">
                        body { visibility: hidden !important; display: none !important}
                    </style>
                    <?php }
                        
                    ?>
                    <script type="text/javascript">
                    <?php
                        
                        if ( $disable_mouse_right_click ==  'yes' )
                            {
                                ?>document.addEventListener('contextmenu', event => event.preventDefault());
                                <?php
                            }   
                        
                        
                        if ( $disable_text_selection    ==  'yes' )
                            {
                                ?>
                                const disableselect = ( event ) => event.preventDefault();  
                                document.onselectstart = disableselect;  
                                <?php   
                                
                            }
                        
                        
                        $disabled_events    =   array();
                        if ( $disable_copy    ==  'yes' )
                            {
                                $disabled_events[]  =   'copy';
                            }
                        if ( $disable_cut    ==  'yes' )
                            {
                                $disabled_events[]  =   'cut';
                            }
                        if ( $disable_paste    ==  'yes' )
                            {
                                $disabled_events[]  =   'paste';
                            }
                        if ( $disable_drag_drop    ==  'yes' )
                            {
                                $disabled_events[]  =   'drag';
                                $disabled_events[]  =   'drop';
                            }
                        if ( count ( $disabled_events ) >   0 )
                            {
                                ?>
                                const disable_events = ['<?php echo implode( "', '", $disabled_events ); ?>'];
                                disable_events.forEach( function( event_name ) {
                                    document.addEventListener( event_name, function (event) {
                                            event.preventDefault()
                                            return false;    
                                    });    
                                    
                                });
                                
                                <?php   
                                
                            }

                        ?>
                        
                        <?php if ( $disable_developer_tools == 'yes'    ||  $disable_view_source == 'yes'    ||  $disable_print == 'yes'    ||  $disable_print_screen == 'yes'    ||  $disable_developer_tools == 'yes' ) { ?>                  
                        document.addEventListener("keydown",  function (event) {

                            <?php if ( $disable_developer_tools == 'yes' ) { ?>
                            if (    
                                    event.keyCode === 123 
                                    || event.ctrlKey && event.shiftKey && event.keyCode === 67  //c
                                    || event.ctrlKey && event.shiftKey && event.keyCode === 73  //i
                                    || event.ctrlKey && event.shiftKey && event.keyCode === 74
                                    || event.ctrlKey && event.shiftKey && event.keyCode === 75  //k
                            ) {
                                event.preventDefault()
                                return false;
                            }
                            
                            <?php } ?>
                            <?php if ( $disable_view_source ==  'yes' ) { ?>
                            if (event.ctrlKey && event.keyCode === 85) {
                                    event.preventDefault()
                                    return false;
                                }
                            <?php } ?>
                            <?php if ( $disable_print ==  'yes' ) { ?>
                            if (event.ctrlKey && event.keyCode === 80) {
                                    event.preventDefault()
                                    return false;
                                }
                            <?php } ?>
                            <?php if ( $disable_print_screen ==  'yes' ) { ?>
                            if (event.ctrlKey && event.keyCode === 44) {
                                    event.preventDefault()
                                    return false;
                                }
                            <?php } ?>
                            
                            });
                        
                        
                            <?php if ( $disable_developer_tools == 'yes' && !   is_preview() ) { ?>
                        
                            if ( typeof window.devtools !== 'undefined'   &&  window.devtools.isOpen )
                                {
                                    DevToolsIsOpen();
                        
                                    window.addEventListener('devtoolschange', event => {
                                        
                                        if ( event.detail.isOpen )
                                            DevToolsIsOpen();
                                    });
                                }
                            
                            function DevToolsIsOpen()
                                {
                                    if ( navigator.userAgent.indexOf('iPhone') > -1 )
                                        return false;
                                        
                                    var doc_html    =   document.getElementsByTagName("html")[0];
                                    doc_html.innerHTML  =   'Inspector is disabled.';
                                }
                            
                            <?php } ?>
                        <?php } ?>
                    </script>
                    <?php   
                    
                }
        
            

        }
?>