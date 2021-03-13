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
 * @param string $site_selector
 *
 * @return array|bool|mixed
 */
function get_site_data( $site_selector = '' ) {

    //debug_log( __FUNCTION__ );

    $data = get_transient( 'ohdear_current_site' );

    if ( is_array( $data ) && ! empty( $data ) && $site_selector == $data['id'] )
        return $data;

    $sites = ohdear()->api->get_sites();

    if ( ! is_array( $sites ) || empty( $sites['data'] ) || ! is_array( $sites['data'] ) )
        return false;

    if ( ! empty( $site_selector ) ) {

        foreach ( $sites['data'] as $key => $site_data ) {

            if ( $site_data['id'] == $site_selector ) {

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

    //debug_log( $url );

    //return ( strpos( 'https://mhthemes.com', $url ) !== false );
    return ( strpos( home_url(), $url ) !== false );
}

/**
 * @param string $site_selector
 *
 * @return bool|string
 */
function get_site_id( $site_selector = '' ) {

    //debug_log( __FUNCTION__ );

    $data = get_site_data( $site_selector );

    //debug_log( '$data:' );
    //debug_log( $data );

    return ( ! empty( $data['id'] ) ) ? (string) $data['id'] : false;
}

/**
 * Check current user role access
 *
 * @return bool
 */
function is_user_roles_granted() {

    if ( empty( get_setting( 'user_roles_access' ) ) ) {

        debug_log( 'ERROR: ' . __FUNCTION__ . ' >> ' . 'user_roles_access is not set' );
        return false;
    }

    if ( empty( array_intersect( wp_get_current_user()->roles, get_setting( 'user_roles_access' ) ) ) )
        return false;

    return true;
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