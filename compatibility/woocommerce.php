<?php

    
    /**
    * Compatibility     : WooCommerce
    * Introduced at     : 4.0.1 
    */

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_conflict_handle_woocommerce
        {
            
            var $wph;
                           
            function __construct()
                {
                    if( !   $this->is_plugin_active())
                        return FALSE;
                    
                    global $wph;
                    
                    $this->wph  =   $wph;
                    
                    add_action('plugins_loaded',                                array( $this, 'run') , -1);    
                    
                    add_action('wp-hide/get_regex',                             array( $this, '_get_regex') , 10, 3);
                    
                    //check for ignore the woo replacements
                    $this->ignore_woo_replacements();
                    
                    if ( defined('DOING_AJAX')  &&  isset ( $_POST['action'] )  &&  in_array( $_POST['action'], array( 'woocommerce_load_variations', 'woocommerce_add_attribute' )) )
                        {
                            add_filter ('wph/components/css_combine_code', '__return_false');
                            add_filter ('wph/components/js_combine_code', '__return_false');
                            
                            add_filter ('wph/components/_init/',                array( $this,    'wph_components_init'), 999, 2 );
                        }
                        
                    //check for block
                    if( isset( $_GET['wph-throw-404'] )   )
                        add_filter ('woocommerce_is_rest_api_request', '__return_false' );
                        
                    
                    add_filter('admin_url',                                     array($this, 'admin_url'),      20, 3);
                           
                }                        
            
            function is_plugin_active()
                {
                    
                    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
                    
                    if(is_plugin_active( 'woocommerce/woocommerce.php' ))
                        return TRUE;
                        else
                        return FALSE;
                }
            
            function run()
                {   
                                                             
                    add_action('woocommerce_product_get_downloads',             array( $this, 'woocommerce_product_get_downloads'), 99, 2);
                    
                    add_filter('woocommerce_update_order_review_fragments',     array( $this, 'woocommerce_update_order_review_fragments') );
                                  
                }
                
            function woocommerce_product_get_downloads( $data, $product)
                {
                    
                    //only when downloading a file
                    if( ! isset($_GET['download_file']) ||  ! isset($_GET['key'])   )
                        return $data;                    
                    
                    if( !is_array( $data )  ||  count( $data ) < 1)
                        return $data;
                    
                    //if no change on the upload slug, return as is
                    $new_upload_path    =   $this->wph->functions->get_site_module_saved_value('new_upload_path');
                    if( empty ( $new_upload_path ) )
                        return $data;
                        
                    foreach ( $data as  $key    =>  $product_download )
                        {
                            $file  =   $product_download->get_file();
                            
                            $replace   =   trailingslashit ( site_url() ) .  $new_upload_path;
                            $replace   =   str_replace(array("http:", "https:") , "", $replace );
                            
                            $replace_with   =   $this->wph->default_variables['url'] . $this->wph->default_variables['uploads_directory'];
                            $replace_with   =   str_replace(array("http:", "https:") , "", $replace_with );
                            
                            $file           =   str_replace($replace, $replace_with , $file);
                            
                            //attempt to change back the url
                            $product_download->set_file( $file );
                            
                            $data[$key] =   $product_download;
                            
                        }
                    
                       
                    return $data;    
                }
                
                
            function woocommerce_update_order_review_fragments ( $fragments )
                {
                    
                    $option__css_combine_code    =   $this->wph->functions->get_site_module_saved_value('css_combine_code',  $this->wph->functions->get_blog_id_setting_to_use());
                    if ( ! in_array( $option__css_combine_code,   array( 'yes', 'in-place', 'in-place-encode-inline' ) ) )
                        return $fragments;
                    
                    //process the fragments keys
                    $WPH_module_general_css_combine =   new WPH_module_general_css_combine();
                    
                    foreach ( $fragments    as $key =>  $data ) 
                        {
                            $_processed_key =   $WPH_module_general_css_combine->css_recipient_process( $key );
                            if ( $key !=    $_processed_key )
                                {
                                    $fragments[ $_processed_key ]   =   $fragments[ $key ];
                                    unset ( $fragments[ $key ] );
                                }   
                        }
                        
                    return $fragments;    
                }
  
  
            function wph_components_init( $status, $component )
                {
                    if ( $component ==  'rewrite_default' )
                        return FALSE;
                        
                        
                    return $status;
                    
                }
                
                
                
            function _get_regex( $regex, $replacements, $content_type )
                {
                    //check for woocommerce replacements
                    if  (  ! isset ( $replacements['woocommerce'] ) )
                        return $regex;
                        
                    $regex  =   str_replace( '(?!\\\\/)#', '(?!\\\\/|_cart_hash)#', $regex );
                    
                    return $regex;
                       
                }
                
            
            
            /**
            * Fix wrong admin url
            * 
            * @param mixed $url
            * @param mixed $path
            * @param mixed $blog_id
            */
            function admin_url( $url, $path, $blog_id )
                {
                    $admin_url     =   $this->wph->functions->get_site_module_saved_value('admin_url',  $this->wph->functions->get_blog_id_setting_to_use());
                    
                    if ( empty ( $admin_url ) )
                        return $url;

                    if ( strpos ( $url, '/wp-admin/' . $admin_url .'/' ) !==    FALSE )
                        $url    =   str_replace( '/' . $admin_url . '/', '/', $url);   
                        
                    return $url;   
                    
                }
            
            
            function ignore_woo_replacements()
                {
                    $current_page_slug_raw      =   $_SERVER['REQUEST_URI'];
                    
                    if ( empty ( $current_page_slug_raw ) )
                        return;
                        
                    $current_page_slug_parts    =   explode ( '/', $current_page_slug_raw );
                    $current_page_slug_parts    =   array_filter ( $current_page_slug_parts );
                    $current_page_slug_parts    =   array_values ( $current_page_slug_parts );
                    $current_page_slug  =   isset ( $current_page_slug_parts[0] ) ? $current_page_slug_parts[0] :   ''    ;
                    if ( empty ( $current_page_slug ) )
                        return;
                        
                    global $wpdb;
                    $mysql_query    =   $wpdb->prepare( "SELECT post_content FROM " . $wpdb->posts . " WHERE `post_name`    = %s  ", sanitize_title ( $current_page_slug ) );
                    $post_content   =   $wpdb->get_var ( $mysql_query );
                    
                    if ( empty ( $post_content ) )
                        return;
                        
                    if ( stripos( $post_content, '<!-- wp:woocommerce/cart' ) !== FALSE    ||  stripos( $post_content, 'wp:woocommerce/checkout' ) !== FALSE )
                        {
                            add_filter ('wph/components/css_combine_code',      '__return_false');
                            add_filter ('wph/components/js_combine_code',       '__return_false');
                            
                            add_filter ('wph/components/_init/',                    array( $this,    'wph_components_init'), 999, 2 );
                        } 
                }    
                            
        }
        
    new WPH_conflict_handle_woocommerce();    
        
?>