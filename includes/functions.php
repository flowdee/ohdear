<?php
/**
 * Functions
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Retrieves Oh Dear settings or empty array when not available
 *
 * @return array
 */
function get_settings() {
    return get_option( 'ohdear_settings', array() );
}

/**
 * Get Oh Dear single setting
 *
 * @param $key
 *
 * @return bool|null|string|array
 */
function get_setting( $key ) {

    $settings = get_settings();

    if ( isset( $settings[$key] ) )
        return $settings[$key];

    return null;
}

/**
 * Delete Oh Dear settings
 */
function delete_settings() {
    delete_option( 'ohdear_settings' );
}

/**
 * Delete Oh Dear cache
 *
 * @param $name
 *
 * @return void
 */
function delete_cache( $name = '' ) {

    //debug_log( __FUNCTION__ );

    global $wpdb;

    if ( ! empty( $name ) ) {

        $sql = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "%_transient_' . $name . '%"';
    } else {
        $sql = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "%_transient_ohdear_%"';
    }

    $wpdb->query( $sql );
}

/**
 * Get current site data
 *
 * @return mixed
 */
function get_site_data() {

    //debug_log( __FUNCTION__ );

    $data = get_transient( 'ohdear_current_site' );

    $settings = get_settings();

    if ( is_array( $data ) && ! empty( $data ) && ! empty( $settings[ 'site_selector' ] ) && $settings[ 'site_selector' ] == $data['id'] )
        return $data;

    $sites = ohdear()->api->get_sites();

    if ( ! is_array( $sites ) || empty( $sites['data'] ) || ! is_array( $sites['data'] ) )
        return false;

    if ( ! empty( $settings[ 'site_selector' ] ) ) {

        foreach ( $sites['data'] as $key => $site_data ) {

            if ( $site_data['id'] == $settings[ 'site_selector' ] ) {

                $data = $site_data;
                break;
            }
        }

    } else {

        foreach ( $sites['data'] as $key => $site_data ) {

            if ( is_current_site( $site_data['sort_url'] ) ) {

                $data = $site_data;
                break;
            }
        }
    }

    if ( ! is_array( $data ) || empty( $data['id'] ) )
        return false;

    //debug_log( '$data:' );
    //debug_log( $data );

    set_transient( 'ohdear_current_site', $data, DAY_IN_SECONDS );

    return $data;
}

/**
 * @param $url
 *
 * @return bool
 */
function is_current_site( $url ) {

// @Todo: 'mhthemes.com' is just for debugging here

//    return ( strpos( home_url(), $url ) !== false );
    return ( strpos( 'mhthemes.com', $url ) !== false );
}

/**
 * @return bool|string
 */
function get_site_id() {

    //debug_log( __FUNCTION__ );

    $data = get_site_data();

    //debug_log( '$data:' );
    //debug_log( $data );

    return ( ! empty( $data['id'] ) ) ? (string) $data['id'] : false;
}

/**
 * Debug logging
 *
 * @param $message
 */
function debug_log( $message ) {

    if ( WP_DEBUG === true ) {
        if ( is_array( $message ) || is_object( $message ) ) {
            error_log( print_r( $message, true ) );
        } else {
            error_log( $message );
        }
    }
}

//delete_settings();
//delete_cache();