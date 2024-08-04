<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_html extends WPH_module_component
        {
            public $buffer              =   '';
            public $placeholders        =   array();
            public $placeholder_hash    =   '';            
            
            function get_component_title()
                {
                    return "HTML";
                }
                                    
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'remove_html_comments',
                                                                    'label'         =>  __('Remove Comments',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove comments from HTML source code.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove Comments',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("The HTML source code usually contain many comment lines, however there is no use for that, unless debugging. Remove all HTML Comments, which usually specify Plugins Name and Version. Any Internet Explorer conditional tags are preserved.",    'wp-hide-security-enhancer'),
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/remove-classes-from-html/'
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
                                                                    'id'            =>  'remove_html_new_lines',
                                                                    'label'         =>  __('Minify',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Minify HTML, Inline Styles, Inline JavaScripts.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Minify',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("When you minify HTML it removes the unnecessary characters and lines in the source code. Indentation, comments, empty lines, etc. are not required in HTML. They just make the file easier to read. Cutting out all this unnecessary stuff can shave down your file size considerably. When you minify HTML code on your website, the server will send a much smaller page to the client making your website load quicker.",    'wp-hide-security-enhancer') .
                                                                                                                                    "<br /><br />" . __("The Minify component include multiple options:",    'wp-hide-security-enhancer') . 
                                                                                                                                    "<br />Html
                                                                                                                                      <br />Html & Css
                                                                                                                                      <br />Html & JavaScript
                                                                                                                                      <br />All
                                                                                                                                      <br /><span class='important'>" . __("Minify JavaScript might produce errors for specific plugins.",    'wp-hide-security-enhancer') . "</span>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/remove-classes-from-html/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'                =>  __('No',                'wp-hide-security-enhancer'),
                                                                                                'html'              =>  __('Html',              'wp-hide-security-enhancer'),
                                                                                                'html_css'          =>  __('Html & Css',        'wp-hide-security-enhancer'),
                                                                                                'html_js'           =>  __('Html & JavaScript', 'wp-hide-security-enhancer'),
                                                                                                'all'               =>  __('All',               'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  80
                                                                    );

                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'clean_body_classes',
                                                                    'label'         =>  __('Remove general classes from body tag',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove general classes from body tag.', 'wp-hide-security-enhancer'),                                                                    
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove general classes from body tag',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Remove general classes from body tag.",    'wp-hide-security-enhancer') .
                                                                                                                                    "<br /><span class='important'>" . __('This can produce layout issues with certain themes, if something break this should be turned off.', 'wp-hide-security-enhancer') ."</span>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/remove-classes-from-html/'
                                                                                                ),
                                                                                                
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  81
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'clean_menu_items_id',
                                                                    'label'         =>  __('Remove ID from Menu items',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove class attribute from all menu items.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove ID from Menu items',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Remove class attribute from all menu items. Any classes which include a 'current' prefix or contain 'has-children' will be preserved.",    'wp-hide-security-enhancer') . 
                                                                                                                                    "<br /><span class='important'>" . __('This can produce layout issues with certain themes, if something break this should be turned off.', 'wp-hide-security-enhancer') ."</span>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/remove-classes-from-html/'
                                                                                                ),
                                                                    
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  81
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'clean_menu_items_classes',
                                                                    'label'         =>  __('Remove class from Menu items',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove class attribute from all menu items.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove class from Menu items',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Remove class attribute from all menu items. Any classes which include a 'current' prefix or contain 'has-children' will be preserved.",    'wp-hide-security-enhancer') . 
                                                                                                                                    "<br /><span class='important'>" . __('This can produce layout issues with certain themes, if something break this should be turned off.', 'wp-hide-security-enhancer') ."</span>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/remove-classes-from-html/'
                                                                                                ),
                                                                    
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  81
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'clean_post_classes',
                                                                    'label'         =>  __('Remove general classes from post',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove general classes from post.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove general classes from post',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Remove general classes from post.",    'wp-hide-security-enhancer') . 
                                                                                                                                    "<br /><span class='important'>" . __('This can produce layout issues with certain themes, if something break this should be turned off.', 'wp-hide-security-enhancer') ."</span>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/remove-classes-from-html/'
                                                                                                ),
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  81
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'clean_image_classes',
                                                                    'label'         =>  __('Remove general classes from images',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Remove general classes from media tags.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Remove general classes from images',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("Remove general classes from media tags.",    'wp-hide-security-enhancer') . 
                                                                                                                                    "<br /><span class='important'>" . __('This can produce layout issues with certain themes, if something break this should be turned off.', 'wp-hide-security-enhancer') ."</span>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/remove-classes-from-html/'
                                                                                                ),
                                                                    'input_type'    =>  'radio',
                                                                    'options'       =>  array(
                                                                                                'no'        =>  __('No',     'wp-hide-security-enhancer'),
                                                                                                'yes'       =>  __('Yes',    'wp-hide-security-enhancer'),
                                                                                                ),
                                                                    'default_value' =>  'no',
                                                                    
                                                                    'sanitize_type' =>  array('sanitize_title', 'strtolower'),
                                                                    'processing_order'  =>  81
                                                                    );
                                                                    
                    return $this->component_settings;    
                }
                
                
                
            function _init_remove_html_comments($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    if ( $this->wp_is_json_request() )
                        return;
                        
                    //do not run when within admin
                    if( defined('WP_ADMIN') &&  ( !defined('DOING_AJAX') ||  ( defined('DOING_AJAX') && DOING_AJAX === FALSE )) && ! apply_filters('wph/components/force_run_on_admin', FALSE, 'remove_html_comments' ) )
                        return;
                    
                    if ( ! $this->ReInit )
                        add_filter('wp-hide/ob_start_callback', array($this, 'remove_html_comments'));
                    
                }
                
                
            public static function remove_html_comments($buffer)
                {
                                            
                    //replace any html comments 
                    $buffer =   preg_replace_callback('/<!--([\s\S]*?)-->/i', function( $match ) {
                                                                                           if  ( preg_match('/(\[if)|(figmeta)|(figma)/i', $match[0] ) )
                                                                                                return $match[0];
                                                                                                
                                                                                            return '';
                    
                                                                                            } , $buffer);
                     
                    //remove empty multiple new lines
                    $buffer =   preg_replace("/(\n){2,}/", "\n", $buffer);
                    
                    return $buffer;
                    
                }
            
            function _init_remove_html_new_lines($saved_field_data)
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                        
                    //do not run when within admin or AJAX
                    if( defined('WP_ADMIN') &&  ( !defined('DOING_AJAX') ||  ( defined('DOING_AJAX') && DOING_AJAX === TRUE )) && ! apply_filters('wph/components/force_run_on_admin', FALSE, 'remove_html_new_lines' ) )
                        return;
                    
                    if ( ! $this->ReInit )
                        add_filter('wp-hide/ob_start_callback', array($this, 'remove_html_new_lines'));
                    
                }
                
                
            function remove_html_new_lines($buffer)
                {
                        
                    if ( in_array( 'remove_html_new_lines' , $this->wph->disable_components ) )
                        return $buffer;
                    
                    $this->buffer           =   $buffer;
                    $this->placeholder_hash =   '%W-P-H-PLACEHOLDER-REPLACEMENT';
                    
                    $settings   =   $this->wph->functions->get_current_site_settings();
                    
                    switch ( $settings['module_settings']['remove_html_new_lines'] )
                        {
                            case 'html'     :
                                                    $this->add_css_placeholders ();
                                                    $this->add_js_placeholders  ();
                                                    
                                                    $this->buffer =   preg_replace( "/\r|\n/", " ", $this->buffer );
                                                    
                                                    break;   
                                                
                            case 'html_css'     :
                                                    $this->add_js_placeholders  ();
                                                    
                                                    $this->buffer =   preg_replace( "/\r|\n/", " ", $this->buffer );
                                                
                                                    break;
                                                
                            case 'html_js'     :
                                                    $this->add_css_placeholders ();
                                                    
                                                    $this->buffer =   preg_replace( "/\r|\n/", " ", $this->buffer );
                                                    
                                                    break;
                            
                            case 'all'     :
                                                    $this->buffer =   preg_replace( "/\r|\n/", " ", $this->buffer );
                                                    break;
                                                                        
                        }
                        
                    //put back any placeholders
                    if( count($this->placeholders) >   0 )
                        {
                            foreach($this->placeholders   as  $placeholder    =>  $data)
                                {
                                    $this->buffer =   str_replace($placeholder, $data, $this->buffer);
                                }
                        }
                    
                    return $this->buffer;
                    
                }
                
            
            
            function add_css_placeholders( )
                {
                    $this->buffer   =   preg_replace_callback( '/(\\s*)<style(\\b[^>]*>)([\\s\\S]*?)<\\/style>(\\s*)/i', array($this, 'add_css_placeholders_callback') , $this->buffer);       
                }
                
            function add_css_placeholders_callback( $match )
                {
                    
                    $pre_space      =   $match[1] === '' ? ''   :   ' ';
                    $tag_attrs      =   $match[2];
                    $tag_content    =   $match[3];
                    $post_space     =   $match[4] === '' ? ''   :   ' ';
                    
                    $match_block    =   $pre_space . '<style' . $tag_attrs . $tag_content . '</style>' . $post_space;
                    
                    $placeholder    =   $this->placeholder_hash . '-css-' . count( $this->placeholders ) . '%';
                    $this->placeholders[ $placeholder ] =   $match_block;
                    
                    return $placeholder;
                    
                }
                
                
            function add_js_placeholders( )
                {
                    
                    $this->buffer   =   preg_replace_callback( '/(\\s*)<script(\\b[^>]*?>)([\\s\\S]*?)<\\/script>(\\s*)/i' ,array($this, 'add_js_placeholders_callback') , $this->buffer);
                
                }   
            
            function add_js_placeholders_callback( $match )
                {
                    
                    $pre_space      =   $match[1] === '' ? ''   :   ' ';
                    $tag_attrs      =   $match[2];
                    $tag_content    =   $match[3];
                    $post_space     =   $match[4] === '' ? ''   :   ' ';
                    
                    $match_block    =   $pre_space . '<script' . $tag_attrs . $tag_content . '</script>' . $post_space;
                    
                    $placeholder    =   $this->placeholder_hash . '-js-' . count( $this->placeholders ) . '%';
                    $this->placeholders[ $placeholder ] =   $match_block;
                    
                    return $placeholder;
                    
                }
               
            
            function _init_clean_body_classes( $saved_field_data )
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    if ( ! $this->ReInit )    
                        add_filter('body_class', array(&$this, 'body_class'), 9, 2);
                    
                }
                
            
            function body_class( $classes, $class )
                {
                    $preserve_classes   =   array(
                                                    'home',
                                                    'archive',
                                                    'single',
                                                    'blog',
                                                    'attachment',
                                                    'search',
                                                    'category',
                                                    'tag',
                                                    'rtl',
                                                    'author',
                                                    'custom-background'
                                                    );
                    
                    if(!empty( $class ))
                        $preserve_classes =   array_merge($preserve_classes, (array) $class );
                    
                    $preserve_classes   =   apply_filters('wp-hide/components/general-html/body_class/preserve', $preserve_classes);;
                    
                    $keep_classes   =   array_intersect($preserve_classes, $classes);
                    
                    //reindex the array
                    $keep_classes   =   array_values($keep_classes);
                       
                    return $keep_classes;
                        
                }
                
                
            function _init_clean_menu_items_id( $saved_field_data )
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    if ( ! $this->ReInit )       
                        add_filter('nav_menu_item_id', array(&$this, 'nav_menu_item_id'), 999);
                    
                }   
                
            
            function nav_menu_item_id($item_id)
                {
                    $item_id    =   '';
                       
                    return $item_id;
                        
                }
                
                
            function _init_clean_menu_items_classes( $saved_field_data )
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    if ( ! $this->ReInit )    
                        add_filter('nav_menu_css_class', array(&$this, 'nav_menu_css_class'), 999);   
                    
                }
                
                
            function nav_menu_css_class( $classes )
                {
                    foreach($classes    as  $key    =>  $class_name)
                        {
                            if(strpos($class_name, 'current-')  === 0   ||  strpos($class_name, 'current_')  === 0  || strpos($class_name, 'has-children')  !== FALSE)
                                continue;
                                
                            unset($classes[$key]);
                            
                        }
                        
                    $classes    =   array_values($classes);
                    
                        
                    return $classes;
                }
                
                
            function _init_clean_post_classes( $saved_field_data )
                {
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    if ( ! $this->ReInit )    
                        add_filter('post_class', array(&$this, 'post_class'), 999, 2);   
                    
                }
                
                
            function post_class( $classes, $class )
                {
                    return $classes;
                    $preserve_classes   =   array(
                                                    'sticky'
                                                    );
                                                    
                    if(!empty( $class ))
                        $preserve_classes =   array_merge($preserve_classes, (array) $class );
                    
                    //preserve post types
                    $post_types =   get_post_types();
                    foreach($post_types as  $post_type)
                        {
                            $preserve_classes[] =   $post_type;
                        }
                        
                    //preserve taxonomies
                    $taxonomies = get_taxonomies( );
                    foreach($taxonomies as  $taxonomy)
                        {
                            $preserve_classes[] =   $taxonomy;
                        }
                    
                    //preserve formats classes
                    foreach( $classes   as  $class)
                        {
                            if(strpos($class, 'format-')   === 0)
                                $preserve_classes[] =   $class;
                        }
                    
                    $preserve_classes   =   apply_filters('wp-hide/components/general-html/post_class/preserve', $preserve_classes);;
                    
                    $keep_classes   =   array_intersect($preserve_classes, $classes);
                    
                    //reindex the array
                    $keep_classes   =   array_values($keep_classes);
                       
                    return $keep_classes;    
                }
                
            
            
            function _init_clean_image_classes( $saved_field_data )
                {
                   
                    if(empty($saved_field_data) ||  $saved_field_data   ==  'no')
                        return FALSE;
                    
                    //do not run when within admin
                    if( defined('WP_ADMIN') &&  ( !defined('DOING_AJAX') ||  ( defined('DOING_AJAX') && DOING_AJAX === FALSE )) && ! apply_filters('wph/components/force_run_on_admin', FALSE, 'clean_image_classes' ) )
                        return;
                    
                    if ( ! $this->ReInit )  
                        add_filter( 'wp-hide/ob_start_callback',         array(&$this, 'ob_start_callback_clean_image_classes'));
                    
                }
                
            
            function ob_start_callback_clean_image_classes( $buffer )
                {
                        
                    $buffer = preg_replace_callback('/<img([^>]+)class=["|\'](.*?)["|\']([^>]+)?>/i', array($this, "clean_image_classes_preg_replace_callback"), $buffer);
                    
                    return $buffer;
                        
                }
                
                
            function clean_image_classes_preg_replace_callback( $matches )
                {
                    $tag        =   isset($matches[0]) ?    $matches[0] :   '';
                    $classes    =   isset($matches[2]) ?    $matches[2] :   '';
   
                    if(empty($tag))
                        return '';
   
                    if(empty($classes))
                        return $tag;
                        
                    $classes_array  =   explode(" ", $classes);
                    $classes_array  =   array_filter( $classes_array );
                    
                    foreach($classes_array  as  $key    =>  $class)
                        {
                            //only wp-image- at the momment
                            if(strpos($class, 'wp-image-')  === 0)
                                {
                                    unset( $classes_array[$key] );
                                }
                        }
                    
                    $classes_array  =   array_values($classes_array);
                    
                    $tag    =   str_replace($classes, implode( " ", $classes_array ), $tag);
                    
                    return $tag;
                
                }
                
                
            /**
             * Checks whether current request is a JSON request, or is expecting a JSON response.
             *
             * @since 5.0.0
             *
             * @return bool True if `Accepts` or `Content-Type` headers contain `application/json`.
             *              False otherwise.
             */
            function wp_is_json_request() {
                if ( isset( $_SERVER['HTTP_ACCEPT'] ) && $this->wp_is_json_media_type( $_SERVER['HTTP_ACCEPT'] ) ) {
                    return true;
                }

                if ( isset( $_SERVER['CONTENT_TYPE'] ) && $this->wp_is_json_media_type( $_SERVER['CONTENT_TYPE'] ) ) {
                    return true;
                }

                return false;
            }

            
            /**
             * Checks whether a string is a valid JSON Media Type.
             *
             * @since 5.6.0
             *
             * @param string $media_type A Media Type string to check.
             * @return bool True if string is a valid JSON Media Type.
             */
            function wp_is_json_media_type( $media_type ) {
                static $cache = array();

                if ( ! isset( $cache[ $media_type ] ) ) {
                    $cache[ $media_type ] = (bool) preg_match( '/(^|\s|,)application\/([\w!#\$&-\^\.\+]+\+)?json(\+oembed)?($|\s|;|,)/i', $media_type );
                }

                return $cache[ $media_type ];
            }   
            

        }
?>