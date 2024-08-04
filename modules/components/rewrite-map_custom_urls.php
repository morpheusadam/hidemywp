<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_rewrite_map_custom_urls extends WPH_module_component
        {
            function get_component_title()
                {
                    return "Map Urls";
                }
                                        
            function get_module_component_settings()
                {
                        
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'map_custom_urls',
                                                                    'label'         =>  __('Map Custom Urls',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('Custom URLs mapping for links in HTML. The substitution is case-insensitive.',  'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  '',
                                                                                                'description'               =>  __('Any domain url can be mapped to something else. Any type of asset works with re-mapping e.g JavaScript, Cascading Style Sheets, Media Images etc.', 'wp-hide-security-enhancer') 
                                                                                                                                . '<br /><br />' . __('For example the following link can be changed from:', 'wp-hide-security-enhancer') 
                                                                                                                                . '<br /><br />' . '<code>&#47;wp-content&#47;plugins&#47;woocommerce&#47;assets&#47;js&#47;frontend&#47;woocommerce.min.js</code>'
                                                                                                                                . '<br />' . __('to:', 'wp-hide-security-enhancer') 
                                                                                                                                . '<br />' . '<code>&#47;ecommerce.min.js</code>',
                                                                                                'option_documentation_url'  =>  'https://www.wp-hide.com/documentation/rewrite-map-urls/'
                                                                                                ),
                                                                    
                                                                    'interface_help_split'  =>  FALSE,
                                                                                        
                                                                    'input_type'    =>  'custom',
                                                                    'default_value' =>  array(),
                                                                    
                                                                    'module_option_html_render' =>  array( $this, '_module_option_html' ),
                                                                    
                                                                    'module_option_processing'  =>  array( $this, '_module_option_processing' ),
                                                                    
                                                                    ); 
                     
                                                                    
                    return $this->component_settings;  
                     
                }
                
                
                
            function _init_map_custom_urls (   $saved_field_data   )
                {
                    if( empty($saved_field_data) ||  ! is_array($saved_field_data) )
                        return FALSE;
                    
                    if ( ! $this->ReInit )    
                        add_filter('wp-hide/ob_start_callback/text_preserve', array($this, '_do_html_replacements'), 999 );
                }
                
            
            function _callback_saved_map_custom_urls( $saved_field_data )
                {
                    $values =   $this->_filter_values( $saved_field_data ); 
                    
                    if ( ! is_array( $values ) ||   count ( $values ) < 1 )
                        return  FALSE;
                        
                        
                    $processing_response    =   array();
                              
                    if($this->wph->server_nginx_config   === TRUE)           
                        $rewrite        =   array();
                        else
                        $rewrite        =  '';
                    
                    $site_url           =   $this->wph->default_variables['url'];
                    $site_url_parsed    =   parse_url( $site_url );
                    
                    foreach ( $values   as  $key    =>  $block)
                        {
                            
                            $replaced_parsed        =   parse_url ( $block[1] );
                            $replacement_parsed     =   parse_url ( $block[0] );
                            
                            $home_root  =   $this->wph->functions->get_home_root();
                            $replaced_parsed_path    =   $replaced_parsed['path'];
                            if ( strpos( $replaced_parsed_path, $home_root ) === 0 )
                                $replaced_parsed_path   =   substr_replace( $replaced_parsed_path, "", 0, strlen( $home_root ) ); 
                            
                            $rewrite_base   =   $this->wph->functions->get_rewrite_path( $replaced_parsed_path, [ 'left_slash'  =>  FALSE, 'right_slash' =>  FALSE ] );
                            $rewrite_to     =   $this->wph->functions->get_rewrite_path( $replacement_parsed['path'], [ 'left_slash'  =>  TRUE, 'right_slash' =>  FALSE, 'include_full_path' =>  TRUE  ] );
                            
                            //append query if exists
                            if  ( isset( $replaced_parsed['query'] ) &&  ! empty ( $replaced_parsed['query'] ) )
                                $rewrite_base   .=  '?' .   $replaced_parsed['query'];
                            if  ( isset( $replacement_parsed['query'] ) &&  ! empty ( $replacement_parsed['query'] ) )
                                $rewrite_to   .=  '?' .   $replacement_parsed['query'];                                
                            
                            $global_match   =   FALSE;
                            if (substr( $block[0] , -1) == '/')
                                $global_match   =   TRUE;
                                
                            if ( $global_match  === TRUE )
                                {
                                    $rewrite_base   .=  '/(.+)'; 
                                    $rewrite_to     .=  '/$2';  
                                }
                                
                            //Attempt to revert mapped to default
                            foreach  ( $this->wph->urls_replacement  as  $priority   =>  $remapp_data )
                                {
                                    
                                    if ( count( $remapp_data ) < 1 )
                                        continue;
                                        
                                    foreach ( $remapp_data   as  $default_url    =>  $remapped_url)
                                        {
                                            $remapp_replaced_parsed     =   parse_url ( $default_url );
                                            $remapp_replacement_parsed  =   parse_url ( $remapped_url );
                                        
                                            if ( isset ( $remapp_replacement_parsed['path'] )   &&  strpos( $rewrite_to, $remapp_replacement_parsed['path'] ) === 0 ) 
                                                {
                                                    $rewrite_to =   str_replace ( $remapp_replacement_parsed['path'] ,  $remapp_replaced_parsed['path'], $rewrite_to);
                                                    break;
                                                }
                                        }     
                                    
                                }
                                       
                            if($this->wph->server_htaccess_config   === TRUE)
                                {
                                    if ( isset ( $replacement_parsed['host'] )  &&  ! empty ( $replacement_parsed['host'] ) )
                                        {
                                            $rewrite .= "\nRewriteCond %{HTTP_HOST} ^". $replacement_parsed['host']  . '$';
                                        }
                                    $rewrite .= "\nRewriteRule ^([_0-9a-zA-Z-]+/)?"    .   $rewrite_base   .   ' '. $rewrite_to .' [END]'; 
                                }
                                
                            if($this->wph->server_web_config   === TRUE)
                                {
                                    $rewrite    =   "\n" . '<rule name="wph-map_custom_urls" stopProcessing="true">';
                                    
                                    $rewrite .=  "\n"  .    '    <match url="^'.  $rewrite_base   .'"  />';
                                    $rewrite .=   "\n" .    '    <action type="Rewrite" url="'.  $rewrite_to .'"  appendQueryString="true" />';
                                    
                                    $rewrite .=  "\n" . '</rule>';
                  
                                }
                                
                            if($this->wph->server_nginx_config   === TRUE)           
                                {
                                    $rewrite_list   =   array();
                                    $rewrite_rules  =   array();
                                    
                                    $rewrite_data   =   '';
                                    
                                    $rewrite_list['description']    =   '~ ^__WPH_SITES_SLUG__/' . $rewrite_base; 
                                    
                                    $global_settings    =   $this->wph->functions->get_global_settings ( );
                                        
                                    if( $global_settings['nginx_generate_simple_rewrite']   ==  'yes' )
                                        {
                                            $rewrite_data .= "\n         rewrite \"^/". ltrim( $rewrite_base, '/' ) ."\" ". $rewrite_to . ' '.  $this->wph->functions->get_nginx_flag_type() .';';
                                        }
                                    
                                    $rewrite_rules[]            =   $rewrite_data;
                                    $rewrite_list['data']       =   $rewrite_rules;
                                    
                                    $rewrite_list['type']        =   'location';
                                    
                                    $rewrite[]  =   $rewrite_list;

                                }    
                        }
                    
                    $processing_response['rewrite'] =   $rewrite;
                                
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
                        <div class="irow"><input name="<?php echo $module_setting['id'] ?>[replaced][]" class="<?php echo $class ?>" value="" placeholder="URL to be Replaced" type="text"> <span alt="f345" class="icon dashicons dashicons-arrow-right-alt2">&nbsp;</span> <input name="<?php echo $module_setting['id'] ?>[replace][]" class="<?php echo $class ?>" value="" placeholder="URL to Replace" type="text"> <a class="action" href="javascript: void(0);" onClick="WPH.replace_text_remove_row( jQuery(this).closest('.irow'))"><span alt="f335" class="close dashicons dashicons-no-alt">&nbsp;</span></a> </div>
                    </div>
                    
                    <!-- WPH Preserve - Start -->
                    <?php
                    
                    $values =   $this->wph->functions->get_site_module_saved_value('map_custom_urls',  $this->wph->functions->get_blog_id_setting_to_use(), 'display');
                    
                    if ( ! is_array($values))
                        $values =   array();
                    
                    if ( count ( $values )  >   0 )
                        {
                            foreach ( $values   as  $block)
                                {
                                    ?><div class="irow">
                                        <input name="<?php echo $module_setting['id'] ?>[replaced][]" class="<?php echo $class ?>" value="<?php echo htmlspecialchars($block[0]) ?>" placeholder="URL to be Replaced" type="text"> <span alt="f345" class="icon dashicons dashicons-arrow-right-alt2">&nbsp;</span> 
                                        <input name="<?php echo $module_setting['id'] ?>[replace][]" class="<?php echo $class ?>" value="<?php echo htmlspecialchars($block[1]) ?>" placeholder="URL to Replace" type="text"> 
                                        <a class="action" href="javascript: void(0);" onClick="WPH.replace_text_remove_row( jQuery(this).closest('.irow'))"><span alt="f335" class="close dashicons dashicons-no-alt">&nbsp;</span></a>
                                    </div><?php
                                }
                        }
                                                                        
                    ?>
                    <!-- WPH Preserve - Stop -->
                    
                        <div id="replacer_insert_root">&nbsp;</div>
                        
                        <p>
                            <button type="button" class="button" onClick="WPH.replace_text_add_row()">Add New</button>
                        </p>
                        
                        <!-- WPH Preserve - Stop -->
                    <?php
                }
                
                
                
            function _module_option_processing( $field_name )
                {
                    
                    $results            =   array();
                                        
                    $data       =   $_POST['map_custom_urls'];
                    $values     =   array();
                    
                    if  ( is_array($data )  &&  count ( $data )   >   0     &&  isset($data['replaced'])  )
                        {
                            foreach(    $data['replaced']   as  $key =>  $text )
                                {
                                    $replaced_text  =   stripslashes($text);
                                    $replaced_text  =   trim($replaced_text);
                                    $replace_text   =   stripslashes($data['replace'][$key]);
                                    $replace_text   =   trim($replace_text);
                                    
                                    if ( $replaced_text ==  $replace_text   ||  empty( $replaced_text ) )
                                        continue;
                                    
                                    $search_results = array();
                                    $this->array_search_recursive( $values, "0", $replaced_text, $search_results);
                                    if ( count  ( $search_results ) > 0 )
                                        continue;
                                        
                                    $values[]  =  array($replaced_text, $replace_text); 
                                    
                                }
                        }
                    
                    $results['value']   =   $values;  
                    
                    return $results;
                    
                }
                
                
                
            function array_search_recursive($array, $key, $value, &$search_results)
                {
                    if (    !is_array( $array ) )
                        return;

                    if (isset($array[$key]) && $array[$key] == $value)
                        $search_results[] = $array;

                    foreach ($array as $subarray)
                        $this->array_search_recursive($subarray, $key, $value, $search_results);
                        
                }
                
            function _do_html_replacements( $buffer )
                {
                    
                    $values =   $this->wph->functions->get_site_module_saved_value( 'map_custom_urls',  $this->wph->functions->get_blog_id_setting_to_use() );
                    
                    $values =   $this->_filter_values( $values );
                    
                        
                    if ( count ( $values )  >   0 )
                        {
                            foreach ( $values   as  $block)
                                {
                                    $buffer =   str_replace( $block[0], $block[1], $buffer);
                                    
                                    //attempt to do replacements for json encoded urls
                                    $buffer =   str_replace( json_encode( $block[0] ), json_encode( $block[1] ), $buffer);
                                    
                                    //check if the url contain a slash, if so try a replacement for values which does not include one and are wrapped in single/double quotes
                                    if  (  rtrim( $block[0], '/' )  !== $block[0] )
                                        {
                                            $block[0]   =   rtrim( $block[0], '/' );   
                                            $block[1]   =   rtrim( $block[1], '/' );
                                            
                                            //single quote
                                            $buffer =   str_replace( trim ( json_encode( $block[0] ), '"' ) , trim ( json_encode( $block[1] ), '"' ) , $buffer);
                                            //double quote
                                            $buffer =   str_replace( trim ( json_encode( $block[0] ), '"' ) , trim ( json_encode( $block[1] ), '"' ) , $buffer);
                                        }
                                    
                                }
                        }   
                    
                    return $buffer;   
                }
            
            
            
            function _filter_values( $values )
                {
                    if ( ! is_array ( $values ) )
                        $values =   array();
                        
                    $site_url           =   site_url();
                    $site_url_parsed    =   parse_url( $site_url );
                    
                    $filtered_data  =   array();
                    
                    if  ( $values   === FALSE   ||  count ( $values ) < 1 ) 
                        return $filtered_data;    
                        
                    foreach ( $values  as   $key    =>  $value )
                        {
                            $value[0]   =   trim ( $value[0] );
                            $value[1]   =   trim ( $value[1] );
                            
                            if ( strpos( $value[0], '/' ) !==   0   ||  strpos( $value[1], '/' ) !==   0 ) 
                                continue;
                                    
                            $filtered_data[]    =   array(
                                                            trim( $value[0] ),
                                                            trim( $value[1] )  
                                                            );
                        
                        }    
                    
                    return $filtered_data;
                    
                }
      
                
            function _display_condition_available_for_site( $module_setting_args    =   array() )
                {
                    if  ( is_multisite() )
                        return FALSE;
                    
                    return TRUE;

                }
                
  
        }
?>