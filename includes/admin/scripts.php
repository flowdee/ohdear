<?php

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Determines whether the current admin page is Oh Dear.
 *
 * Only works after the `wp_loaded` hook, & most effective
 * starting on `admin_menu` hook.
 *
 * @return bool True if Oh Dear admin page.
 */
function is_admin_page() {

    $ret = false;

	if ( isset( $_GET['page'] ) )
	    $page = sanitize_text_field( $_GET['page'] );

	$pages = array( OHDEAR_PAGE, OHDEAR_PAGE . '-settings' );

	if ( ! empty( $page ) )
	    $ret = in_array( $page, $pages );

	/**
	 * Filters whether the current page is Oh Dear admin page.
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
function admin_scripts( $hook_suffix ) {

    if ( $hook_suffix == 'index.php' )
        wp_enqueue_script( 'ohdear-admin-dashboard', OHDEAR_PLUGIN_URL . 'assets/js/admin-dashboard.js', array( 'jquery', 'ohdear-apexcharts' ), OHDEAR_VERSION, false );

	if ( is_admin_page() || $hook_suffix == 'index.php' ) {

        wp_enqueue_script( 'ohdear-apexcharts', OHDEAR_PLUGIN_URL . 'assets/js/apexcharts.min.js', array(), OHDEAR_VERSION );
        wp_enqueue_style( 'ohdear-admin-css', OHDEAR_PLUGIN_URL . 'assets/css/admin.css', array(), OHDEAR_VERSION );
    }
}
add_action( 'admin_enqueue_scripts', '\OhDear\admin_scripts' );