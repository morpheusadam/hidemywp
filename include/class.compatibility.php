<?php

     if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
     
     class WPH_Compatibility
        {
            
            var $wph                            =   '';
            var $functions                      =   '';
         
            function __construct()
                {
                    global $wph;

                    $this->wph          =   $wph;
                    $this->functions    =   new WPH_functions();
                    
                    $this->init();
                    
                }
                
                
                
            function init()
                {
                    
                    $CompatibilityFiles  =  array(
                                                    '_general.php',
                                                    'astra-addon.php',
                                                    'astra-portfolio.php',
                                                    'accelerated-moblie-pages.php',
                                                    'autoptimize.php',
                                                    'acceleratewp.php',
                                                    'a2-optimized.php',
                                                    'breeze.php',
                                                    'bunnycdn.php',
                                                    'buddypress.php',
                                                    'cache-enabler.php',
                                                    'comet-cache.php',
                                                    'cookie-law-info.php',
                                                    'css-hero.php',
                                                    'classified-listing.php',
                                                    'debloat.php',
                                                    'dokan.php',
                                                    'easy-digital-downloads.php',
                                                    'elementor.php',
                                                    'eventon-action-user.php',
                                                    'fast-velocity-minfy.php',
                                                    'fluentform.php',
                                                    'fusion-builder.php',
                                                    'flying-press.php',
                                                    'hyper-cache.php',
                                                    'js-composer.php',
                                                    'jch-optimize.php',
                                                    'kreativo-pro-speed-optimization.php',
                                                    'litespeed-cache.php',
                                                    'oxygen.php',
                                                    'revslider.php',
                                                    'shortpixel-adaptive-images.php',
                                                    'shortpixel-image-optimiser.php',
                                                    'swift-performance.php',
                                                    'sg-cachepress.php',
                                                    'thrive-visual-editor.php',
                                                    'translatepress-multilingual.php',
                                                    'townhub-add-ons.php',
                                                    'tutor.php',
                                                    'ultimate-member.php',
                                                    'uicore-framework.php',
                                                    'qtranslate-xt.php',
                                                    'w3-cache.php',
                                                    'wc_frontend_manager.php',
                                                    'wepos.php',
                                                    'woo-global-cart.php',
                                                    'woocommerce.php',
                                                    'wordpress-seo-premium.php',
                                                    'wp-asset-clean-up-pro.php',
                                                    'wp-fastest-cache.php',
                                                    'wp-hummingbird.php',
                                                    'wp-job-manager.php',
                                                    'wp-meteor.php',
                                                    'wp-optimize.php',
                                                    'wp-rocket.php',
                                                    'wp-simple-firewall.php',
                                                    'wp-smush.php',
                                                    'wp-speed-of-light.php',
                                                    'wp-super-cache.php',
                                                    'wpforms-lite.php',
                                                    'wpml.php',
                                                    'wpsol.php',
                                                    'yith-woocommerce-multi-vendor.php',
                                                    'perfmatters.php',
                                                    'wp-cloudflare-page-cache.php',
                                                    'waspthemes-yellow-pencil.php',
                                                    'redirection.php'                                                    
                                                    );
                    foreach( $CompatibilityFiles as $CompatibilityFile ) 
                        {
                            if  ( is_file( WPH_PATH . 'compatibility/' . $CompatibilityFile ) )
                                include_once( WPH_PATH . 'compatibility/' . $CompatibilityFile );
                        }
                  
                    
                    /**
                    * Servers             
                    */
                    include_once(WPH_PATH . 'compatibility/host/kinsta.php');
                    
                    /**
                    * Themes
                    */
                    
                    $theme  =   wp_get_theme();
                    
                    if( ! $theme instanceof WP_Theme )
                        return FALSE;
                        
                    $compatibility_themes   =   array(
                                                        'avada'             =>  'avada.php',
                                                        'bricks'            =>  'bricks.php',
                                                        'divi'              =>  'divi.php',
                                                        'listivo'           =>  'listivo.php',
                                                        'woodmart'          =>  'woodmart.php',
                                                        'buddyboss-theme'   =>  'buddyboss-theme.php',
                                                        );
                    
                    if (isset( $theme->template ) )
                        {
                            
                            foreach ( $compatibility_themes as  $theme_slug     =>  $compatibility_file )
                                {
                                    if ( strtolower( $theme->template ) == $theme_slug  ||   strtolower( $theme->name ) == $theme_slug )
                                        {
                                            include_once(WPH_PATH . 'compatibility/themes/' .   $compatibility_file );    
                                        }
                                }
                              
                        }
      
                          
                    do_action('wph/compatibility/init');
                    
                }
            
    
                
        }   
            



?>