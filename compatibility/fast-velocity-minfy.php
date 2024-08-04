<?php

    /**
    * Compatibility         :   Fast Velocity Minify
    * Introduced at         :   2.5.8
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_fast_velocity_minify
        {
                        
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action('fvm_get_url',               array ( $this, 'fvm_get_url' ), 99 );  
                    
                    add_action('wp_print_styles',           array ( $this, 'update_styles_src' ), -99 );
                    add_action('wp_print_footer_scripts',   array ( $this, 'update_styles_src' ), -99 );
                    
                    add_action('wp_print_scripts',          array ( $this, 'update_scripts_src' ), 5 );
                    add_action('wp_print_footer_scripts',   array ( $this, 'update_scripts_src' ), 9.999998 );
                    
                    add_filter( 'fvm_after_download_and_minify_code', array ( $this, 'process_buffer'), 99, 2 );
                    
                    //ignore the files which where cached through the Cache plugin, as they where already processed
                    $js_postprocessing_type    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',   $this->wph->functions->get_blog_id_setting_to_use());
                    $css_postprocessing_type   =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    //ignore the files which where cached through the Cache plugin, as they where already processed
                    if ( in_array ( $js_postprocessing_type, array ( 'yes', 'combine-encode-inline' ) ) )
                        add_filter( 'wp-hide/module/general_js_combine/ignore_file' ,   array ( $this, '__general_js_combine_ignore_file' ), 99, 2 );
                    if ( in_array ( $css_postprocessing_type, array ( 'yes', 'combine-encode-inline' ) ) )
                        add_filter( 'wp-hide/module/general_css_combine/ignore_file' ,  array ( $this, '__general_css_combine_ignore_file' ), 99, 2 );

                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'fast-velocity-minify/fvm.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            
            
            function fvm_get_url( $url )
                {
                    
                    $url    =   $this->wph->functions->content_urls_replacement( $url,  $this->wph->functions->get_replacement_list() );      
                    
                    return $url;
                    
                }
            
            /**
            * The plugin retrieve the assets using URLs not PATH, so ensure the urls are updated before retrieving any file
            * 
            */
            function update_styles_src()
                {
                    
                    global $wp_styles;
                    
                    if ( ! is_object ( $wp_styles ) ||  ! is_array ( $wp_styles->registered ) )
                        return;
                    
                    $repalcement_list   =   $this->wph->functions->get_replacement_list();
                        
                    foreach ( $wp_styles->registered    as  $handle    =>  $data )
                        {
                            if ( ! isset ( $data->src ) ||  is_bool( $data->src ) ||    empty ( $data->src ) )
                                continue;
                                                        
                            //if inline, process the code
                            if ( isset( $wp_styles->registered[$handle]->extra['after']) && is_array ( $wp_styles->registered[$handle]->extra[ 'after' ] ) )
                                {
                                    foreach ( $wp_styles->registered[$handle]->extra['after']   as  $key    =>  $value )
                                        {
                                            if  ( empty ( $value ) )
                                                continue;
                                            
                                            $wp_styles->registered[$handle]->extra['after'][ $key ] =   $this->process_buffer( $value, 'css' );
                                        }
                                }
                             
                        }
                }
                
                
            /**
            * The plugin retrieve the assets using URLs not PATH, so ensure the urls are updated before retrieving any file
            * 
            */
            function update_scripts_src()
                {
                    
                    global $wp_scripts;
                    
                    if ( ! is_object ( $wp_scripts ) ||  ! is_array ( $wp_scripts->registered ) )
                        return;
                    
                    $repalcement_list   =   $this->wph->functions->get_replacement_list();
                        
                    foreach ( $wp_scripts->registered    as  $handle    =>  $data )
                        {
                            if ( ! isset ( $data->src ) ||  is_bool( $data->src ) ||    empty ( $data->src ) )
                                continue;
                            
                            //if inline, process the code
                            if ( isset( $wp_scripts->registered[$handle]->extra['after']) && is_array ( $wp_scripts->registered[$handle]->extra[ 'after' ] ) )
                                {
                                    foreach ( $wp_scripts->registered[$handle]->extra['after']   as  $key    =>  $value )
                                        {
                                            if  ( empty ( $value ) )
                                                continue;
                                                
                                            $wp_scripts->registered[$handle]->extra['after'][ $key ] =   $this->process_buffer( $value, 'js' );
                                        }
                                }
                            if ( isset( $wp_scripts->registered[$handle]->extra['before']) && is_array ( $wp_scripts->registered[$handle]->extra[ 'before' ] ) )
                                {
                                    foreach ( $wp_scripts->registered[$handle]->extra['before']   as  $key    =>  $value )
                                        {
                                            if  ( empty ( $value ) )
                                                continue;
                                                
                                            $wp_scripts->registered[$handle]->extra['before'][ $key ] =   $this->process_buffer( $value, 'js' );
                                        }
                                }
                             
                             
                        }
                }
                
                
                
            /**
            * Process teh buffer before being saved locally
            *     
            * @param mixed $uffer
            * @param mixed $file_type
            */
            function process_buffer ( $buffer, $file_type )
                {
                                        
                    switch ( $file_type ) 
                        {
                            case                        'css' :
                            case                    'text/css':
                                                                $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                                                                
                                                                $option__css_combine_code    =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                                                                if ( in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                                                                    $buffer =   $WPH_module_general_css_combine->css_recipient_process( $buffer );
                                                                    else
                                                                    $buffer =   $WPH_module_general_css_combine->_process_url_replacements( $buffer );  

                                                                break;
                            
                            case    'application/x-javascript':                
                            case                         'js' :
                                                                $WPH_module_general_js_combine =   new WPH_module_general_js_combine();
                                                                
                                                                $option__js_combine_code    =   $this->wph->functions->get_site_module_saved_value('js_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                                                                if ( in_array( $option__js_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                                                                    $buffer =   $WPH_module_general_js_combine->js_recipient_process( $buffer );
                                                                    else
                                                                    $buffer =   $WPH_module_general_js_combine->_process_url_replacements( $buffer );  
                                                                
                                                                
                                                                break;   
                            
                            default:
                                            
                                                                $buffer =   $this->wph->proces_html_buffer( $buffer );
                                                                                    
                                                                break;        
                        }
                                
                    return $buffer;     
                          
                }
                
            
            
            function __general_js_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/fvm/' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
                
            function __general_css_combine_ignore_file( $ignore, $file_src )
                {
                    
                    if ( stripos( $file_src, '/fvm/' ) )
                        $ignore =   TRUE;    
                    
                    return $ignore;   
                }
            
                            
        }


    new WPH_conflict_handle_fast_velocity_minify();
        
?>