<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_cdn_setup extends WPH_module_component
        {
            function get_component_title()
                {
                    return "CDN";
                }
                                    
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'cdn_url',
                                                                    'label'         =>  __('CDN Url',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Some CDN providers (like stackpath.com ) replace site assets with custom url, enter here such url. Otherwise this option should stay empty.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('CDN Url',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("A Content Delivery Network - CDN - is a network of servers located around the globe in fundamental spots with fast access. It takes a while for a web page to load especially if the server is located far away from the user. So they are designed to host and deliver copies of your site's static and dynamic content such as images, CSS, JavaScript, audio and video streams.",    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><br />" . __('Sample CDN url:',    'wp-hide-security-enhancer') .
                                                                                                                                            "<br /><code>cdnjs.cloudflare.com</code><br /><br />" .
                                                                                                                                            __('Enter a CDN Url to allow the plugin to process assets provided through CDN service.',    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/cdn-cdn-url/'
                                                                                                        ),
                                                                    
                                                                    'input_type'    =>  'custom',
                                                                    'default_value' =>  array(),
                                                                    
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                         
                                                                    
                                                                    'sanitize_type' =>  array()
                                                                    
                                                                    );
                                                                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'cdn_use_for_cache_files',
                                                                    'label'         =>  __('Load Cache Files through CDN',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('When creating Cache files, mainly used when using CSS Combine and JavaScript Cobine, the cache files should be loaded through CDN.', 'wp-hide-security-enhancer'),
                                                                                                                                        
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Load Cache Files through CDN',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("If provided a CDN, the processed cache assets will be loaded through it.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/cdn-cdn-url/'
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
                                                                    'id'            =>  'cdn_use_for_assets_inside_cache_files',
                                                                    'label'         =>  __('Load assets within Cache Files through CDN',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('When creating Cache files, mainly used when using CSS Combine and JavaScript Cobine, any assets ( images, fonts) inside cache files should be loaded through CDN.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                        'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('Load assets within Cache Files through CDN',    'wp-hide-security-enhancer'),
                                                                                                        'description'               =>  __("If provided a CDN, all media files inside the cached data will be using CDN to load.",    'wp-hide-security-enhancer'),
                                                                                                        'option_documentation_url'  =>  'https://wp-hide.com/documentation/cdn-cdn-url/'
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
                        <div class="irow"><input name="cdn_url[]" class="text" value="" placeholder="" type="text" /> <a class="action" href="javascript: void(0);" onClick="WPH.replace_text_remove_row( jQuery(this).closest('.irow'))"><span alt="f335" class="close dashicons dashicons-no-alt">&nbsp;</span></a> </div>
                    </div>
                    <?php
                    
                    $values =   $this->wph->functions->get_site_module_saved_value('cdn_url',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    
                    if ( ! is_array($values))
                        {
                            if ( ! empty ( trim ( $values ) ) )
                                $values =   (array)$values;
                                else
                                $values =   array();
                        }
                    
                    if ( count ( $values )  >   0 )
                        {
                            foreach ( $values   as  $value )
                                {
                                    ?><div class="irow">
                                        <input name="cdn_url[]" class="text" value="<?php echo htmlspecialchars(stripslashes( $value )) ?>" placeholder="" type="text" />
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
                                        
                    $data       =   $_POST['cdn_url'];
                    $values     =   array();
                    
                    if  ( is_array($data )  &&  count ( $data )   >   0 )
                        {
                            foreach(    $data   as  $value )
                                {
                                    $value  =   stripslashes($value);
                                    $value  =   trim($value);
                                    
                                    if  ( ! empty ( $value ) ) 
                                        $values[] =   $value;
                                }
                        }
                    
                    $results['value']   =   $values;  
                    
                    return $results;
                    
                }


        }
?>