<?php
/**
 * OhDear_API Class
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class OhDear_API {

    /**
     * API URL
     *
     * @var string
     */
    private $api_url = 'https://ohdear.app/api/';

    /**
     * API Token
     */
    private $api_key;

        /**
         * Oh Dear settings
         */
        private $settings;

    /**
     * OhDear_API constructor.
     */
    public function __construct() {

        $this->settings = get_settings();

        $this->api_key = ( ! empty ( $this->settings['api_key'] ) ) ? esc_html( $this->settings['api_key'] ) : '';
    }

    /**
     * Verify credentials for API
     *
     * @param $api_key
     *
     * @return mixed
     */
    public function verify_api_credentials( $api_key ) {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        $this->api_key = $api_key;

        $verified = $this->get_sites();

        if ( is_array( $verified ) && empty( $verified['error'] ) )
            return $verified;

        return null;
    }

    /**
     * Get a list of sites
     *
     * https://ohdear.app/docs/integrations/api/sites
     *
     * @return mixed
     */
    public function get_sites() {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        $list = get_transient( 'ohdear_site' );

        if ( empty( $list ) ) {

            $list = $this->request( 'sites' );

            if ( empty( $list['error'] ) )
                set_transient( 'ohdear_site', $list, DAY_IN_SECONDS );
        }

//debug_log( '$list:' );
//debug_log( $list );

        return $list;
    }

    /**
     * Get the current site data
     *
     * @return mixed
     */
    public function get_site_data() {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        $data = get_setting( 'current_site_data' );

        if ( is_array( $data ) && ! empty( $data ) )
            return $data;

        $sites = $this->get_sites();

        if ( ! is_array( $sites ) || empty( $sites['data'] ) || ! is_array( $sites['data'] ) || empty( $sites['data'] ) )
            return false;

        //debug_log( '$sites:' );
        //debug_log( $sites );

        foreach ( $sites['data'] as $key => $site_data ) {

            // @Todo: 'mhthemes.com' is just for tests here

            //if ( strpos( home_url(), $site_data['sort_url'] ) !== false ) {
            if ( strpos( 'mhthemes.com', $site_data['sort_url'] ) !== false ) {

                $data = $site_data;
                break;
            }
        }

        //debug_log( '$data:' );
        //debug_log( $data );

        return $data;
    }

    /**
     * Fetch data from Oh Dear API
     *
     * @param string $url
     *
     * @return array|mixed|null|object|string
     */
    private function request( $url = '' ) {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        if ( empty( $this->api_key ) )
            return null;

        $response = wp_remote_get( $this->api_url . $url, array(
            'headers' => array(
                'Authorization' => 'Bearer ' . $this->api_key,
                'Content-Type' => 'application/json'
            ),
            //'body' => wp_json_encode( $args ),
            'timeout' => 15
        ));

        //debug_log( '$response:' );
        //debug_log( $response );

        // Check for error
        if ( is_wp_error( $response ) ) {

            debug_log( $response->get_error_message() );
            return false;
        }

        if ( empty( $response['body'] ) ) {

            debug_log( "Response from '" . $url . "' URL is empty" );
            return false;
        }

        $result = wp_remote_retrieve_body( $response );

        $result = json_decode( $result, true );

        //debug_log( '$result:' );
        //debug_log( $result );

        if ( empty( $result ) )
            debug_log( 'Can not decode response JSON to assoc array' );

        return $result;
    }
}