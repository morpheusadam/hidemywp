<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_text_replace extends WPH_module_component
        {
            
            public $placeholder_hash                =   '%W-P-H-PLACEHOLDER-REPLACEMENT';
            var $urls_map   =   array();
            
            
            function get_component_title()
                {
                    return "Text Replace";
                }
                                        
            function get_module_component_settings()
                {
                    
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'text_replace',
                                                                    'label'         =>  __('Text Replace',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Arbitrary text replacement from HTML.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  '',
                                                                                                'description'               =>  __('Arbitrary text replacement from HTML. The substitution is case-sensitive, also spaces in front or at the end are being used. The replacements occur only on front-side.',  'wp-hide-security-enhancer') .
                                                                                                                                    "<br /><br /><span class='important'>" . __('This can produce layout issues, use with caution. Ensure this is used with long texts or html blocks, which allows exact and focused replacements. For individual words, the CSS / JavaScript Replacements function should be considered.', 'wp-hide-security-enhancer') . "<span>",
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/general-html-text-replace/'
                                                                                                ),
                                                                    
                                                                    'interface_help_split'  =>  FALSE,
                                                                    
                                                                    'input_type'    =>  'custom',
                                                                    'default_value' =>  array(),
                                                                    
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                                    
                                                                    'processing_order'  =>  10
                                                                    
                                                                    ); 
                     
                                                                    
                    return $this->component_settings;  
                     
                }
                
                
                
            function _init_text_replace (   $saved_field_data   )
                {
                    if( empty($saved_field_data) ||  ! is_array($saved_field_data) )
                        return FALSE;
                    
                    //only for front side
                    if( defined('WP_ADMIN') &&  ( !defined('DOING_AJAX') ||  ( defined('DOING_AJAX') && DOING_AJAX === FALSE )) && ! apply_filters('wph/components/force_run_on_admin', FALSE, 'text_replace' ) )
                        return;
                    
                    if ( ! $this->ReInit )    
                        add_filter('wp-hide/ob_start_callback/pre_replacements', array($this, '_do_html_replacements'), 5);
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
                        <div class="irow"><input name="<?php echo $module_setting['id'] ?>[replaced][]" class="<?php echo $class ?>" value="" placeholder="String to be Replaced" type="text"> <span alt="f345" class="icon dashicons dashicons-arrow-right-alt2">&nbsp;</span> <input name="<?php echo $module_setting['id'] ?>[replace][]" class="<?php echo $class ?>" value="" placeholder="String to Replace" type="text"> <a class="action" href="javascript: void(0);" onClick="WPH.replace_text_remove_row( jQuery(this).closest('.irow'))"><span alt="f335" class="close dashicons dashicons-no-alt">&nbsp;</span></a> </div>
                    </div>
                    <?php
                    
                    $values =   $this->wph->functions->get_site_module_saved_value('text_replace',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    
                    if ( ! is_array($values))
                        $values =   array();
                    
                    if ( count ( $values )  >   0 )
                        {
                            foreach ( $values   as  $block)
                                {
                                    ?><div class="irow">
                                        <input name="<?php echo $module_setting['id'] ?>[replaced][]" class="<?php echo $class ?>" value="<?php echo htmlspecialchars(stripslashes($block[0])) ?>" placeholder="String to be Replaced" type="text"> <span alt="f345" class="icon dashicons dashicons-arrow-right-alt2">&nbsp;</span> 
                                        <input name="<?php echo $module_setting['id'] ?>[replace][]" class="<?php echo $class ?>" value="<?php echo htmlspecialchars(stripslashes($block[1])) ?>" placeholder="String to Replace" type="text"> 
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
                    
                    $process_interface_save_errors  =   array();
                                        
                    $data       =   $_POST['text_replace'];
                    $values     =   array();
                    
                    if  ( is_array($data )  &&  count ( $data )   >   0     &&  isset($data['replaced'])  )
                        {
                            foreach(    $data['replaced']   as  $key =>  $text )
                                {
                                    $errors =   FALSE;
                                    
                                    $replaced_text  =   stripslashes($text);
                                    $replace_text   =   stripslashes($data['replace'][$key]);
                                    
                                    if  ( empty( $replaced_text ) )
                                        continue;
                                    
                                    if  ( strlen( $replaced_text ) < 5 )
                                        { 
                                            $process_interface_save_errors[]    =   array(  'type'      =>  'error',
                                                                                            'message'   =>  __('Provided replaced word ', 'wp-hide-security-enhancer') . ' <b>' . $replaced_text . '</b> '  .  __('need to be at least 5 chars.', 'wp-hide-security-enhancer')
                                                                                        );
                                            $errors                                     =   TRUE;
                                        }
                                    
                                    
                                    if  ( $errors )
                                        continue;
                                    
                                    if ( $replaced_text !=  $replace_text   &&  ! empty( $replaced_text ) )
                                        {
                                            $values[]  =  array($replaced_text, $replace_text);   
                                            
                                        }
                                    
                                }
                        }
                    
                    $results['value']   =   $values;
                    
                    if  (  count ( $process_interface_save_errors ) > 0 )
                        {
                            $wph_interface_save_errors  =   get_option( 'wph-interface-save-errors');
                            
                            $wph_interface_save_errors  =   array_filter ( array_merge( (array)$wph_interface_save_errors, $process_interface_save_errors) ) ;
                            
                            update_option( 'wph-interface-save-errors', $wph_interface_save_errors );  
                        }  
                    
                    return $results;
                    
                }
                
            function _do_html_replacements( $buffer )
                {
                   
                    //ensure the text replacements are not done over urls
                    //$buffer               =   preg_replace_callback( '/((https?|ftp):)?\/\/[^\s\/$.?#].[^\s>\'"]*/im' ,array($this, 'preserve_buffer_urls') , $buffer);
                    
                    $values =   $this->wph->functions->get_site_module_saved_value( 'text_replace',  $this->wph->functions->get_blog_id_setting_to_use() );
                        
                    if ( count ( $values )  >   0 )
                        {
                            foreach ( $values   as  $block)
                                {
                                    $buffer =   str_replace( stripslashes(htmlspecialchars_decode($block[0])), stripslashes(htmlspecialchars_decode($block[1])), $buffer);
                                }
                        }   
                    
                    //restore the urls
                    //$buffer               =   $this->restore_buffer_urls( $buffer );
                    
                    return $buffer;   
                }
                
                
            function preserve_buffer_urls( $match )
                {
                    $replacement        =   $this->placeholder_hash . count ( $this->urls_map ) . '%';
                    $this->urls_map[]   =   $match[0];
                    
                    return $replacement;   
                }
                
                
            function restore_buffer_urls( $buffer )
                {
                    
                    foreach ( $this->urls_map as $key =>    $url )
                        {
                            $buffer = str_replace( $this->placeholder_hash . $key . '%', $url, $buffer );
                        }
                    
                    $this->urls_map =   array();
                        
                    return $buffer;
                }
  
        }
?>