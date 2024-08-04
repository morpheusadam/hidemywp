<?php

    if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
    
    class WPH_PluginUpdate
    {

        public $api_url;
        private $slug;
        public $plugin;
        private $API_VERSION;

        public function __construct( $api_url, $slug, $plugin )
        {
            $this->api_url = $api_url;
            $this->slug = $slug;
            $this->plugin = $plugin;
            $this->API_VERSION = 1.1;
			global $wph;
			$this->wph = $wph;
        }

        public function check_for_plugin_update( $checked_data )
        {
            if ( !is_object( $checked_data ) || ! isset ( $checked_data->response ) )
                return $checked_data;

            $request_string = $this->prepare_request('plugin_update');
            if($request_string === FALSE)
                return $checked_data;

            global $wp_version;
            $request_uri = $this->api_url . '?' . http_build_query( $request_string , '', '&');
            $data = wp_remote_get( $request_uri, array(
                'timeout' => 20,
                'user-agent' => 'WordPress/' . $wp_version . '; WPHPRO/' . WPH_CORE_VERSION .'; ' . get_bloginfo( 'url' ),
            ));

            if(is_wp_error( $data ) || $data['response']['code'] != 200)
                return $checked_data;

            $response_block = json_decode($data['body']);

            if(!is_array($response_block) || count($response_block) < 1)
                return $checked_data;

            $response_block = $response_block[count($response_block) - 1];
            $response = $this->postprocess_response( $response_block );

            if ( $response ) {
                $checked_data->response[$this->plugin] = $response;
            }

            return $checked_data;
        }

        public function plugins_api_call($def, $action, $args)
        {
            if (!is_object($args) || !isset($args->slug) || $args->slug != $this->slug)
                return $def;

            $request_string = $this->prepare_request($action, $args);
            if($request_string === FALSE)
                return new WP_Error('plugins_api_failed', __('An error occurred when trying to identify the plugin.', 'woo-global-cart'));

            global $wp_version;
            $request_uri = $this->api_url . '?' . http_build_query( $request_string , '', '&');

            $data = wp_remote_get( $request_uri, array(
                'timeout' => 20,
                'user-agent' => 'WordPress/' . $wp_version . '; WPHPRO/' . WPH_CORE_VERSION .'; ' . get_bloginfo( 'url' ),
            ));

            if(is_wp_error( $data ) || $data['response']['code'] != 200)
                return new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.' , 'woo-global-cart'));

            $response_block = json_decode($data['body']);
            $response_block = $response_block[count($response_block) - 1];

            $response = $this->postprocess_response( $response_block );
            if ( $response )
                return $response;
        }

        private function prepare_request($action, $args = array())
        {
            global $wp_version;

            $request_data = array(
                'woo_sl_action' => $action,
                'version' => WPH_CORE_VERSION,
                'product_unique_id' => WPH_PRODUCT_ID,
                'wp-version' => $wp_version,
                'api_version' => $this->API_VERSION
            );

            return $request_data;
        }

        private function postprocess_response( $response_block )
        {
            $response = isset($response_block->message) ? $response_block->message : '';

            if ( is_object( $response ) && ! empty ( $response ) ) {
                $response->slug = $this->slug;
                $response->plugin = $this->plugin;
                if ( isset ( $response->sections ) )
                    $response->sections = (array)$response->sections;
                if ( isset ( $response->banners ) )
                    $response->banners = (array)$response->banners;
                if ( isset ( $response->icons ) )
                    $response->icons = (array)$response->icons;
                return $response;
            }
            return FALSE;
        }

        function in_plugin_update_message( $plugin_data, $response  )
        {
            if ( empty ( $response->upgrade_notice ))
                return;
            echo ' ' .  $response->upgrade_notice;
        }
    }

?>