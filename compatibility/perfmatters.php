<?php

    /**
    * Compatibility     : Perfmatters
    * Introduced at     : 1.9.1
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_perfmatters
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                        
                    add_filter ( 'wp-hide/module/general_js_variables_replace/placeholder_javascript_type', array ( $this, 'placeholder_javascript_type' ) );
                       
                    add_filter ( 'wp-hide/module/general_css_combine/href_attribute',                       array ( $this, 'general_css_combine_href_attribute' ), 10, 2);
                    add_filter ( 'wp-hide/module/general_css_combine/html_link_tag',                        array ( $this, 'general_css_combine_html_link_tag' ), 10, 3); 
                    
                    //ignore used css
                    add_filter( 'wp-hide/module/general_css_variables_replace/placeholder_ignore_css',      array ( $this, 'placeholder_ignore_css' ), 99, 3 );
                    
                    //process the css before save into cache
                    add_filter ( 'perfmatters_used_css',                                                    array ( $this, 'perfmatters_used_css' ) );
                }                        
            
            static function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'perfmatters/perfmatters.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function placeholder_javascript_type( $allowed_types )
                {
                    $allowed_types[]    =   'pmdelayedscript';  
                    
                    return $allowed_types;    
                }
                
            function general_css_combine_href_attribute( $href_attribute, $code_block )
                {
                    if ( strpos( $code_block, 'data-pmdelayedstyle' )   !==     FALSE )
                        $href_attribute =   'data-pmdelayedstyle';
                        
                    return $href_attribute;   
                }
                
                
            function general_css_combine_html_link_tag( $html_link_tag, $current_link_tag, $media_link )
                {
                    if ( strpos( $current_link_tag, 'data-pmdelayedstyle' )   ===     FALSE )
                        return $html_link_tag;
                        
                    $doc = new DOMDocument();
                    $doc->loadHTML( $current_link_tag );
                    
                    $doc->getElementsByTagName('link')[0]->setAttribute( 'data-pmdelayedstyle', $media_link );

                    $tagHTML    =   $doc->saveHTML( $doc->getElementsByTagName('link')[0] );
                    
                    return $tagHTML;
                }
                
                
            function placeholder_ignore_css( $ignore, $code, $type )
                {
                    //large chunks produce errors for some browsers
                    if ( preg_match( '#<style[^>]*(perfmatters-used-css)#i', $code ) > 0 )
                        $ignore =   TRUE;
                    
                    return $ignore;   
                }
                
                
            function perfmatters_used_css( $used_css_string )
                {
                    
                    $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                                            
                    $option__css_combine_code    =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    if ( in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                        $used_css_string =   $WPH_module_general_css_combine->css_recipient_process( $used_css_string );
                        else
                        $used_css_string =   $WPH_module_general_css_combine->_process_url_replacements( $used_css_string ); 
                           
                    return $used_css_string;   
                    
                }
   
        }
        
    new WPH_conflict_handle_perfmatters();


?>