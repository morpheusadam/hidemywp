<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_module_general_html_wp_die extends WPH_module_component
        {
            public $buffer              =   '';
     
            
            function get_component_title()
                {
                    return "WP Die";
                }
                                    
            function get_module_component_settings()
                {
                    $this->component_settings[]                  =   array(
                                                                    'id'            =>  'wp_die',
                                                                    'label'         =>  __('WP Die Layout',    'wp-hide-security-enhancer'),
                                                                    'description'   =>  __('On WP Die event, replace the default layout with a custom one.', 'wp-hide-security-enhancer'),
                                                                    
                                                                    'help'          =>  array(
                                                                                                'title'                     =>  __('Help',    'wp-hide-security-enhancer') . ' - ' . __('WP Die Layout',    'wp-hide-security-enhancer'),
                                                                                                'description'               =>  __("WordPress provides a default error screen, which displays a standardized layout for various error or notice situations. By default, the layout consists of a simple message with minimal styling.",    'wp-hide-security-enhancer') . " <br />  <br /> 
                                                                                                                                            <br /> " . __("This is how a default layout appears:",    'wp-hide-security-enhancer') . " <br />  <br /> 
                                                                                                                                            <img src='".  WPH_URL . "/assets/images/help/wp-hide-wp-die-layout.jpg' />
                                                                                                                                            <br /> " . __("This option provides a way to change the screen layout with a custom one, that diguise WordPress. The new screen will show up as follows:",    'wp-hide-security-enhancer') . " <br />  <br /> 
                                                                                                                                            <img src='".  WPH_URL . "/assets/images/help/wp-hide-customised-wp-die-layout.jpg' />",
                                                                                                'option_documentation_url'  =>  'https://wp-hide.com/documentation/general-html-wp-die/'
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
                                                                    
                    add_filter('wp_die_handler', array($this, 'wp_die_handler'));                                                
            
                                                                    
                    return $this->component_settings;    
                }
                
            
            function wp_die_handler( $callback )
                {
                    $option =   $this->wph->functions->get_site_module_saved_value('wp_die',  $this->wph->functions->get_blog_id_setting_to_use());
                    if ( $option    !=  'yes' )
                        return $callback;
                        
                    return array ( $this, 'wp_die_handler_processor' );   
                }
                  
            
            /**
             * Kills WordPress execution and displays HTML page with an error message.
             *
             * This is the default handler for wp_die(). If you want a custom one,
             * you can override this using the {@see 'wp_die_handler'} filter in wp_die().
             *
             * @since 3.0.0
             * @access private
             *
             * @param string|WP_Error $message Error message or WP_Error object.
             * @param string          $title   Optional. Error title. Default empty string.
             * @param string|array    $args    Optional. Arguments to control behavior. Default empty array.
             */    
            public function wp_die_handler_processor( $message, $title = '', $args = array() )
                {
                    list( $message, $title, $parsed_args ) = _wp_die_process_input( $message, $title, $args );

                    if ( is_string( $message ) ) {
                        if ( ! empty( $parsed_args['additional_errors'] ) ) {
                            $message = array_merge(
                                array( $message ),
                                wp_list_pluck( $parsed_args['additional_errors'], 'message' )
                            );
                            $message = "<ul>\n\t\t<li>" . implode( "</li>\n\t\t<li>", $message ) . "</li>\n\t</ul>";
                        }
  
                        $message = sprintf(
                            '<p>%s</p>',
                            $message
                        );
                    }

                    $have_gettext = function_exists( '__' );

                    if ( ! empty( $parsed_args['link_url'] ) && ! empty( $parsed_args['link_text'] ) ) {
                        $link_url = $parsed_args['link_url'];
                        if ( function_exists( 'esc_url' ) ) {
                            $link_url = esc_url( $link_url );
                        }
                        $link_text = $parsed_args['link_text'];
                        $message  .= "\n<p><a href='{$link_url}'>{$link_text}</a></p>";
                    }

                    if ( isset( $parsed_args['back_link'] ) && $parsed_args['back_link'] ) {
                        $back_text = $have_gettext ? __( '&laquo; Back' ) : '&laquo; Back';
                        $message  .= "\n<p><a href='javascript:history.back()'>$back_text</a></p>";
                    }

                    if ( ! did_action( 'admin_head' ) ) :
                        if ( ! headers_sent() ) {
                            header( "Content-Type: text/html; charset={$parsed_args['charset']}" );
                            status_header( $parsed_args['response'] );
                            nocache_headers();
                        }

                        $text_direction = $parsed_args['text_direction'];
                        $dir_attr       = "dir='$text_direction'";

                        // If `text_direction` was not explicitly passed,
                        // use get_language_attributes() if available.
                        if ( empty( $args['text_direction'] )
                            && function_exists( 'language_attributes' ) && function_exists( 'is_rtl' )
                        ) {
                            $dir_attr = get_language_attributes();
                        }
                        ?>
                    <!DOCTYPE html>
                    <html <?php echo $dir_attr; ?>>
                    <head>
                        <meta http-equiv="Content-Type" content="text/html; charset=<?php echo $parsed_args['charset']; ?>" />
                            <?php
                            if ( function_exists( 'wp_robots' ) && function_exists( 'wp_robots_no_robots' ) && function_exists( 'add_filter' ) ) {
                                add_filter( 'wp_robots', 'wp_robots_no_robots' );
                                wp_robots();
                            }
                            ?>
                        <title><?php echo $title; ?></title>

                        <meta http-equiv="X-UA-Compatible" content="IE=edge">
                        <meta name="viewport" content="width=device-width, initial-scale=1">

                        <style media="all">
                
                        @font-face {
                          font-family: 'Montserrat';
                          font-style: normal;
                          font-weight: 200;
                          font-display: swap;
                          src: url( <?php echo WPH_URL ?>/assets/fonts/font1.woff2) format('woff2');
                          unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
                        }

                        @font-face {
                          font-family: 'Montserrat';
                          font-style: normal;
                          font-weight: 400;
                          font-display: swap;
                          src: url( <?php echo WPH_URL ?>/assets/fonts/font2.woff2) format('woff2');
                          unicode-range: U+0000-00FF, U+0131, U+0152-0153, U+02BB-02BC, U+02C6, U+02DA, U+02DC, U+0304, U+0308, U+0329, U+2000-206F, U+2074, U+20AC, U+2122, U+2191, U+2193, U+2212, U+2215, U+FEFF, U+FFFD;
                        }
                        
                        *{    -webkit-box-sizing:border-box;    box-sizing:border-box}html { height: 100%; }body{    padding:0;    margin:0;    height: 100%;}.notfound{    max-width:750px;    width:100%;    line-height:1.4;    text-align:center;    margin: 0px auto;    position: relative;    height: 100%;}.notfound .notfound-404{     position: absolute;  top: 50%;  -ms-transform: translateY(-50%);  transform: translateY(-50%);    width: 100%;}.notfound .notfound-404 h1{    font-family:montserrat,sans-serif;    font-size:150px;    font-weight:200;    margin:0;    color:#211b19;    text-transform:uppercase;    line-height: 160px;}.notfound .notfound-404 h2{    font-family:montserrat,sans-serif;    font-size:22px;    font-weight:400;    text-transform:uppercase;    color:#211b19;    background:#fff;    margin:auto;    }.notfound .notfound-404 h2 + h2 {padding-bottom: 50px; font-size:18px;}.notfound .notfound-404 p{    font-family:montserrat,sans-serif;    font-size:14px;    font-weight:400;    color:#211b19;    background:#fff;    margin:auto;    padding-bottom: 10px;}.notfound a{    font-family:montserrat,sans-serif;    display:inline-block;    font-weight:700;    text-decoration:none;    color:#fff;    padding:5px 15px;    background:#ff6300;    font-size:14px;    -webkit-transition:.2s all;    transition:.2s all}.notfound a:hover{    color:#ff6300;    background:#211b19}@media only screen and (max-width:767px){    .notfound .notfound-404 h1{        font-size:148px    }}@media only screen and (max-width:480px){    .notfound .notfound-404{        height:148px;        margin:0 auto 10px    }    .notfound .notfound-404 h1{        font-size:86px    }    .notfound .notfound-404 h2{        font-size:16px    }    .notfound a{        padding:7px 15px;        font-size:14px    }}
                        </style>
   

                        </head>
                        <body>
                        <?php endif; // ! did_action( 'admin_head' ) ?>

                        <div class="notfound">
                        <div class="notfound-404">
                            <h1>Oops!</h1>
                            <h2><?php echo $title ?></h2>
                            <h2>Error Type: <?php echo $args['response'] ?></h2>
                            <?php echo $message ?>
                        </div>
                        
                        </div>
                        

                        </body>
                        </html>
                        <?php
                        if ( $parsed_args['exit'] ) {
                            die();
                        }                            
                    
                    
                }
                
            

        }
?>