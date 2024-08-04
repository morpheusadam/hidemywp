<?php


    /**
    * Compatibility     : WP Rocket
    * Introduced at     : 
    */
    
    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class WPH_conflict_handle_wp_rocket
        {
            var $wph;
                            
            function __construct()
                {
                    if( !   $this->is_plugin_active() )
                        return FALSE;
                  
                    define ('WP_ROCKET_WHITE_LABEL_FOOTPRINT', true);
                  
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    //add_filter( 'rocket_buffer',                    array( 'WPH_conflict_handle_wp_rocket', 'rocket_buffer'), 999 );       
                    
                    add_filter( 'rocket_js_url',                        array( $this,   'rocket_js_url'), 999 );
                    
                    $options = get_option( 'wp_rocket_settings' );
                    if ( ! isset ( $options['optimize_css_delivery'] )  ||  $options['optimize_css_delivery']   != '1' )
                        {
                            add_filter( 'rocket_css_content',                   array( $this,   'rocket_css_content'), 999, 3 );
                            /**
                            * 
                            * STILL THEY ARE MISSING A FILTER FOR JS Content !!!!!!   ....
                            */
                        }
                    
                    $js_postprocessing_type    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',   $this->wph->functions->get_blog_id_setting_to_use());
                    $css_postprocessing_type   =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    //ignore the files which where cached through the Cache plugin, as they where already processed
                    if ( in_array ( $js_postprocessing_type, array ( 'yes', 'combine-encode-inline' ) ) )
                        add_filter( 'wp-hide/module/general_js_combine/ignore_file' ,   array ( $this, '__general_js_combine_ignore_file' ), 99, 2 );
                    if ( in_array ( $css_postprocessing_type, array ( 'yes', 'combine-encode-inline' ) ) )
                        add_filter( 'wp-hide/module/general_css_combine/ignore_file' ,  array ( $this, '__general_css_combine_ignore_file' ), 99, 2 );
                        
                        
                    //Process JS files using custom src attribute  e.g. data-rocket-src
                    add_filter ( 'wp-hide/module/general_js_variables_replace/placeholder_javascript_type',             array ( $this, 'placeholder_javascript_type' ) );
                    add_filter ( 'wp-hide/module/general_js_variables_replace/placeholder_javascript_src',              array ( $this, 'placeholder_javascript_src' ), 10, 2 );
            
                    
                    //Ignore critical css
                    add_filter( 'wp-hide/module/general_css_variables_replace/placeholder_ignore_css',  array ( $this, '__general__placeholder_ignore_css' ), 99, 3 );

                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if( is_plugin_active( 'wp-rocket/wp-rocket.php' ) )
                        return TRUE;
                        else
                        return FALSE;
                }
                
                
            function rocket_buffer( $buffer )
                {
                    
                    $buffer =   $this->wph->ob_start_callback( $buffer );
                    
                    return $buffer;
                    
                }
                
                
            /**
            * Replace js urls
            *     
            * @param mixed $url
            */
            function rocket_js_url( $buffer )
                {
                    
                    //retrieve the replacements list
                    $buffer    =   $this->wph->functions->content_urls_replacement( $buffer,  $this->wph->functions->get_replacement_list() );  
                    
                    return $buffer ;   
                }
            
            
            
            /**
            * Process the Cache CSS content
            * 
            * @param mixed $content
            */
            function rocket_css_content( $buffer, $source = FALSE , $target = FALSE )
                {
                   
                    $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                    
                    if ( $target !== FALSE )
                        {
                            $target_url =   FALSE;
                            $_target     =   str_replace ( $_SERVER['DOCUMENT_ROOT'], '', wp_normalize_path ( $target ) );
                            if ( $_target != $target )
                                $target_url     =   trailingslashit ( site_url() ) .  ltrim( $_target , '/' );  
                            
            
                            $buffer     =   $WPH_module_general_css_combine->_convert_relative_urls ( $buffer, $target_url );
                        }
                                            
                    $option__css_combine_code    =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    if ( in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                        $buffer     =   $WPH_module_general_css_combine->css_recipient_process( $buffer );
                        else
                        $buffer     =   $WPH_module_general_css_combine->_process_url_replacements( $buffer, TRUE );
                    
                    return $buffer;   
                }
            
            
            
            function placeholder_javascript_type( $allowed_types )
                {
                    $allowed_types[]    =   'rocketlazyloadscript';  
                    
                    return $allowed_types;    
                }
            
            function placeholder_javascript_src( $element_src, $code_block )
                {
                    
                    $doc = new DOMDocument();
                    $doc->loadHTML( $code_block );
                    
                    $attribute  =   $doc->getElementsByTagName('script')[0]->getAttribute('data-rocket-type');
                    if ( ! empty ( $attribute ) )
                        {
                            if ( empty ( $doc->getElementsByTagName('script')[0]->getAttribute( $element_src ) )    &&  ! empty ( $doc->getElementsByTagName('script')[0]->getAttribute( 'data-rocket-src' ) ) )
                                $element_src    =   'data-rocket-src';   
                        }
                    
                    return $element_src;    
                }
            
                
            function __general_js_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/min/' )    ||  stripos( $file_src, '/cache/critical-css/' ) ||  stripos( $file_src, '/cache/busting/' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
                
            
            function __general_css_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/cache/min/' )    ||  stripos( $file_src, '/cache/critical-css/' ) ||  stripos( $file_src, '/cache/busting/' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
                
                
            function __general__placeholder_ignore_css( $ignode, $code, $type )
                {
                    $ignore =    FALSE;
                    
                    if ( preg_match( '#<style[^>]*(critical)#i', $code ) > 0 )
                        $ignore =   TRUE;
                    
                    return $ignore;   
                }

                            
        }


        new WPH_conflict_handle_wp_rocket();
        
?>