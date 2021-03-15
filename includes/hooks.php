<?php
/**
 * Hooks
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Redirect to plugin settings page if not connected to API
 */
function admin_temporarily_redirect_dashboard_page() {

    //debug_log( __FUNCTION__ );

    $settings = get_settings();

    if ( ! empty( $_GET['page'] ) && $_GET['page'] == OHDEAR_PAGE &&
         ( empty( $settings['api_status'] ) || empty( $settings['site_selector'] ) )
    ){

        $url = add_query_arg( array( 'page' => OHDEAR_PAGE . '-settings' ), admin_url( 'admin.php' ) );

        //debug_log( '$url:' );
        //debug_log( $url );

        wp_safe_redirect( $url );
        exit;
    }
}
add_action( 'admin_init', '\OhDear\admin_temporarily_redirect_dashboard_page' );