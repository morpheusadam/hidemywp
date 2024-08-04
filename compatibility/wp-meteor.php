<?php

    /**
    * Compatibility     : WP Meteor
    * Introduced at     : 2.3.9
    * Last Checked      : 3.1.4
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_wp_meteor
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_filter( 'wp-hide/module/general_js_variables_replace/placeholder_javascript_type',  array( $this , 'placeholder_javascript_type' ) ); 
                    add_filter( 'wp-hide/module/general_js_variables_replace/placeholder_javascript_src',   array( $this , 'placeholder_javascript_src' ), 99, 2 );
                    //add_filter( 'wp-hide/module/general_js_combine/write_to_cache/script_tag_replacement',  array( $this , 'script_tag_replacement' ), 99, 3 ); 
 
                }                        
            
            static function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'wp-meteor/wp-meteor.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function placeholder_javascript_type( $type_list )
                {
                    
                    $type_list[]    =   'javascript/blocked';
                       
                    return $type_list ;
                        
                }
                
            function placeholder_javascript_src( $src, $code_block )
                {
                    if ( stripos( $code_block, 'data-wpmeteor-src' ) !== FALSE )
                        $src    =   'data-wpmeteor-src';
                       
                    return $src ;
                        
                }
                
            function script_tag_replacement( $script_tag_replacement, $code_block, $js_content )
                {
                    if ( stripos( $code_block, 'type="javascript/blocked"' ) === FALSE )
                        return $script_tag_replacement;
                    
                    $doc2 = new DOMDocument();
                    $doc2->loadHTML( $code_block );
                    $element_data_src        =   $doc2->getElementsByTagName('script')[0]->getAttribute( 'data-wpmeteor-src' );
                    
                    $doc = new DOMDocument();
                    $doc->loadHTML( $script_tag_replacement);
                    $element_src        =   $doc->getElementsByTagName('script')[0]->getAttribute( 'src' );
                    
                    $node = $doc2->getElementsByTagName('script')->item(0);
                                        
                    if ( empty ( $element_data_src ) )
                        {
                            $js_combine_code      =   $this->wph->functions->get_site_module_saved_value( 'js_combine_code',   $this->wph->functions->get_blog_id_setting_to_use());
                            
                            if ( $js_combine_code == 'in-place-encode-inline' )
                                {
                                    $inline_content     =   '';
                                    $js_content_blocks  =   explode('#! WPH-JS-Content-Start', $js_content );
                                    $js_content_blocks  =   array_map("trim", $js_content_blocks);
                                    foreach ( $js_content_blocks    as $key =>  $js_content_block )
                                        {
                                            if  (  empty ( $js_content_block ) )
                                                continue;
                                            
                                            $inline_content .= base64_encode( $js_content_block );    
                                        }
                                    
                                    $inline_content   =   'data:text/javascript;base64,' . $inline_content;
                                    $element_src    =   $inline_content;
                                }
                            
                            $fragment = $node->ownerDocument->createDocumentFragment();
                            $fragment->appendXML( '' );
                            while ($node->hasChildNodes())
                                $node->removeChild($node->firstChild);
                            if ( $fragment->childNodes->length  > 0 )
                                $node->appendChild($fragment);
                        }
                        
                    $node->setAttribute('data-wpmeteor-src', $element_src);
                          
                    $code_block =   $doc2->saveHTML( $node );
                    
                    return $code_block;
                        
                }
   
        }
        
    new WPH_conflict_handle_wp_meteor();


?>