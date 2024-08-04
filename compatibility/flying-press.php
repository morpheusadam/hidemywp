<?php


    /**
    * Compatibility     : FlyingPress
    * Introduced at     : 4.5.1
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_flying_press
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_filter ( 'wp-hide/module/general_js_variables_replace/placeholder_javascript_src',              array ( $this, 'placeholder_javascript_src' ), 10, 2 );
                    add_filter ( 'wp-hide/module/js_postprocessing/placeholders_process/validate_tag_as_javascript',    array ( $this, 'validate_tag_as_javascript' ), 10, 2 );
                    
                    //$js_postprocessing_type    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',   $this->wph->functions->get_blog_id_setting_to_use());
                    //$css_postprocessing_type   =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    add_filter ( 'wp-hide/module/general_css_combine/href_attribute',                                   array ( $this, 'general_css_combine_href_attribute' ), 10, 2);
                    
                    add_action ( 'wp-hide/module/general_css_combine/placeholders_process/completed',                                   array ( $this, 'wp_hide_module_general_css_combine_placeholders_process_completed' ), 10, 2);
                                            
                    add_filter( 'flying_press_optimization:after', array ( $this, 'process_buffer' ) );
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'flying-press/flying-press.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }

            
            function placeholder_javascript_src( $element_src, $code_block )
                {
                    
                    $doc = new DOMDocument();
                    $doc->loadHTML( $code_block );
                    
                    $attribute  =   $doc->getElementsByTagName('script')[0]->getAttribute('data-src');
                    if ( ! empty ( $attribute ) )
                        {
                            if ( empty ( $doc->getElementsByTagName('script')[0]->getAttribute( $element_src ) )    &&  ! empty ( $doc->getElementsByTagName('script')[0]->getAttribute( 'data-src' ) ) )
                                $element_src    =   'data-src';   
                        }
                    
                    return $element_src;    
                }
                
            function validate_tag_as_javascript( $status, $code_block )
                {
                    
                    $doc = new DOMDocument();
                    $doc->loadHTML( $code_block );
                    
                    $attribute  =   $doc->getElementsByTagName('script')[0]->getAttribute('data-src');
                    if ( ! empty ( $attribute ) )
                        $status =   TRUE;
                    
                    return $status;    
                }
                
                
            function wp_hide_module_general_css_combine_placeholders_process_completed( $css_recipient_content, &$CSSProcessor )
                {
                    $current_placeholder    =   $CSSProcessor->current_placeholder;
                    
                    $local_url_parsed   =   parse_url( home_url() );
                    $CDN_urls   =   (array)$this->wph->functions->get_site_module_saved_value('cdn_url',  $this->wph->functions->get_blog_id_setting_to_use());
                    $CDN_urls    =   array_filter( array_map("trim", $CDN_urls) ) ;
                    $use_cdn     =   '';
                    if  ( count ( $CDN_urls ) > 0 )
                        $use_cdn    =   $CDN_urls[0];
                    
                    $document_root      =   isset($_SERVER['DOCUMENT_ROOT'])    &&  ! empty( $_SERVER['DOCUMENT_ROOT'] )    ?   $_SERVER['DOCUMENT_ROOT']   :   ABSPATH;
                    
                    foreach ( $CSSProcessor->placeholders[ $current_placeholder ] as  $placeholder    =>  $origin_code_block )
                        {
                            if ( ! preg_match ( '/<style[^>]*original-href/i', $origin_code_block ) )
                                continue;
                            
                            $doc = new DOMDocument();
                            $doc->loadHTML( $origin_code_block );
                            $element_href       =   $doc->getElementsByTagName( 'style' )[0]->getAttribute( 'original-href' );
                            
                            //check if the resource is on local
                            $resurce_url_parsed =   parse_url( $element_href );
                                                                
                            if ( ! isset ( $resurce_url_parsed['host'] )    ||  $local_url_parsed['host']  !=  $resurce_url_parsed['host'] &&  $use_cdn    !=  $resurce_url_parsed['host'] )
                                continue;
                               
                            $resurce_path   =   $resurce_url_parsed['path'];
                            if  ( is_multisite() &&  $this->wph->default_variables['network']['current_blog_path']  !=  '/' )
                                {
                                    $resurce_path   =   preg_replace("/^". preg_quote( $this->wph->default_variables['network']['current_blog_path'], '/' ) ."/i", $this->wph->default_variables['site_relative_path'] , $resurce_url_parsed['path']);
                                    if ( strpos($resurce_path, "/") !== 0 )
                                        $resurce_path   =   '/' .   $resurce_path;
                                }
                                
                            //attempt to retrieve the file locally
                            $local_file_path    =   urldecode( $document_root .    $resurce_path );
                            if ( !  file_exists ( $local_file_path ) )
                                continue;
                                
                            $resurce_url_file_info =   pathinfo( $resurce_path );
                            if  ( ! isset($resurce_url_file_info['extension'])  ||  $resurce_url_file_info['extension'] !=  'css')
                                continue;
                            
                            $local_file_content =   @file_get_contents ( $local_file_path );
                            
                            $file_url   =   $CSSProcessor->write_file( $local_file_content );
                            
                            $CSSProcessor->placeholders[ $current_placeholder ][ $placeholder ] =   str_replace ( $element_href, $file_url, $CSSProcessor->placeholders[ $current_placeholder ][ $placeholder ] );   
                            
                        }
                          
                }
            
            
            function general_css_combine_href_attribute( $href_attribute, $code_block )
                {
                    if ( strpos( $code_block, 'data-href' )   !==     FALSE )
                        $href_attribute =   'data-href';
                        
                    return $href_attribute;   
                }
                
                
            function process_buffer( $buffer )
                {
                    
                    if  ( $this->wph->ob_callback_late )
                        return $buffer;
                        
                    //do replacements for this url
                    $buffer =   $this->wph->proces_html_buffer( $buffer );
                    
                    $this->wph->ob_callback_late =   TRUE;
                       
                    return $buffer;   
                    
                }

        }


    new WPH_conflict_flying_press();
    
?>