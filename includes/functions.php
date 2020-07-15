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
 */
function delete_cache() {
    global $wpdb;

    $sql = 'DELETE FROM ' . $wpdb->options . ' WHERE option_name LIKE "%_transient_ohdear_%"';

    $wpdb->query( $sql );
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