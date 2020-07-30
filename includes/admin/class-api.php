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

        $this->api_key = ( ! empty( $this->settings['api_key'] ) ) ? esc_html( $this->settings['api_key'] ) : '';
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

        return false;
    }

    /**
     * Get a list of sites registered in Oh Dear account.
     *
     * https://ohdear.app/docs/integrations/api/sites
     *
     * @return mixed
     */
    public function get_sites() {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        $data = get_transient( 'ohdear_sites' );

        if ( empty( $data ) ) {

            $data = $this->request( 'sites' );

            if ( ! empty( $data['error'] ) ) {

                //debug_log( 'API REQUEST ERROR:' );
                //debug_log( $data['error'] );

                return false;
            }

            set_transient( 'ohdear_sites', $data, DAY_IN_SECONDS );
        }

        //debug_log( '$data:' );
        //debug_log( $data );

        return $data;
    }

    /**
     * Get uptime stats
     *
     * @return bool|mixed
     */
    public function get_uptime() {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        $uptime = get_transient( 'ohdear_uptime' );

        if ( ! empty( $uptime ) )
            return $uptime;

        $site_id = get_site_id( $this->settings['site_selector'] );

        if ( empty( $site_id ) )
            return false;

        // Last 30 days
        $args = array(
            'split'  => 'day',
            'filter' => array(
                'started_at' => date( 'YmdHis', mktime( 0, 0, 0, date('m'), date('d')-29, date('Y') ) ),
                'ended_at'   => date( 'YmdHis', mktime( 23, 59, 59, date('m'), date('d'), date('Y') ) )
            )
        );

        $data = $this->request( 'sites/' . $site_id . '/uptime', $args );

        //debug_log( '$data:' );
        //debug_log( $data );

        if ( empty( $data['error'] ) )
            set_transient( 'ohdear_uptime', $data, DAY_IN_SECONDS );

        return $data;
    }

    /**
     * Get performance stats
     *
     * @return bool|mixed
     */
    public function get_perf() {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        $perf = get_transient( 'ohdear_performance' );

        if ( ! empty( $perf ) )
            return $perf;

        $site_id = get_site_id( $this->settings['site_selector'] );

        if ( empty( $site_id ) )
            return false;

        // Last 7 days
        $args = array(
            'filter' => array(
                'start'     => date( 'YmdHis', mktime( null, null, null, date('m'), date('d')-6, date('Y') ) ),
                'end'       => date( 'YmdHis' ),
                'timeframe' => '1h'
            )
        );

        $data = $this->request( 'sites/' . $site_id . '/performance-records', $args );

        //debug_log( '$data:' );
        //debug_log( $data );

        if ( empty( $data['error'] ) )
            set_transient( 'ohdear_performance', $data, DAY_IN_SECONDS );

        return $data;
    }

    /**
     * Get broken links
     *
     * @return bool|mixed
     */
    public function get_broken() {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        $broken = get_transient( 'ohdear_broken' );

        if ( ! empty( $broken ) )
            return $broken;

        $site_id = get_site_id( $this->settings['site_selector'] );

        if ( empty( $site_id ) )
            return false;

        $data = $this->request( 'broken-links/' . $site_id );

        //debug_log( '$data:' );
        //debug_log( $data );

        if ( empty( $data['error'] ) )
            set_transient( 'ohdear_broken', $data, DAY_IN_SECONDS );

        return $data;
    }

    /**
     * Fetch data from Oh Dear API
     *
     * @param string $url
     * @param array $args
     *
     * @return bool|mixed
     */
    private function request( $url = '', $args = array() ) {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        //debug_log( '$url: ' . $url );

        if ( empty( $this->api_key ) )
            return false;

        if ( is_array( $args ) && sizeof( $args ) > 0 ) {

            $query_args = array();

            foreach ( $args as $arg_key => $arg_value ) {

                if ( ! empty( $arg_value ) ) {

                    // Comma separated values must be converted to arrays
                    if ( is_string( $arg_value ) && strpos( $arg_value, ',' ) !== false ) {
                        $arg_value = explode( ',', $arg_value );
                    }

                    // Add query args
                    $query_args[ $arg_key ] = $arg_value;
                }
            }

            if ( sizeof( $query_args ) > 0 ) {
                // Extended "http_build_query" in order to add multiple args with the same key
                $query = http_build_query( $query_args, null, '&' );
                $query_string = preg_replace( '/%5B(?:[0-9]|[1-9][0-9]+)%5D=/', '=', $query );
                $url .= '?' . $query_string;
            }
        }

        try {

            $response = wp_remote_get( $this->api_url . $url, array(
                'headers' => array(
                    'Authorization' => 'Bearer ' . $this->api_key,
                    'Content-Type'  => 'application/json'
                ),
                'timeout' => 15
            ));

            //debug_log( '$response:' );
            //debug_log( $response );

            // Check for error
            if ( is_wp_error( $response ) )
                throw new \Exception( 'API REQUEST ERROR: ' . $response->get_error_message() );

            if ( empty( $response['body'] ) )
                throw new \Exception( "Response from '" . $url . "' URL is empty" );

            $result = wp_remote_retrieve_body( $response );

            $result = json_decode( $result, true );

            //debug_log( '$result:' );
            //debug_log( $result );

            if ( empty( $result ) )
                throw new \Exception( 'Can not decode response JSON to assoc array' );

            if ( ! empty( $result['error'] ) )
                throw new \Exception( 'API RESPONSE ERROR: ' . $result['error'] );

        } catch ( \Exception $e ) {

            debug_log( $e->getMessage() );
            return false;
        }

        return $result;
    }

}