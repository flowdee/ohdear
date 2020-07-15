<?php

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Determines whether the current admin page is Oh Dear Analytics.
 *
 * Only works after the `wp_loaded` hook, & most effective
 * starting on `admin_menu` hook.
 *
 * @return bool True if Oh Dear Analytics admin page.
 */
function is_admin_page() {

    $ret = false;

	if ( isset( $_GET['page'] ) )
	    $page = sanitize_text_field( $_GET['page'] );

	$pages = array( OHDEAR_PAGE );

	if ( ! empty( $page ) )
	    $ret = in_array( $page, $pages );

	/**
	 * Filters whether the current page is Oh Dear Analytics admin page.
	 *
	 * @param bool $ret Whether the current page is either a given admin page
	 *                  or any whitelisted admin page.
	 */
	return apply_filters( 'ohdear_is_admin_page', $ret );
}

/**
 *  Load the admin scripts
 *
 *  @return void
 */
function admin_scripts() {

	if ( ! is_admin_page() )
	    return;

    wp_enqueue_script( 'ohdear-apexcharts', OHDEAR_PLUGIN_URL . 'includes/third-party/apexcharts.min.js', array(), OHDEAR_VERSION );
}
add_action( 'admin_enqueue_scripts', '\OhDear\admin_scripts' );