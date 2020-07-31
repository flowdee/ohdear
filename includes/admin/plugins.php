<?php
/**
 * Plugins
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Plugins row action links
 *
 * @param array $links already defined action links
 * @param string $file plugin file path and name being processed
 *
 * @return array $links
 */
function add_action_links( $links, $file ) {

    if ( $file != OHDEAR_PLUGIN_DIR_NAME . '/' . OHDEAR_PLUGIN_DIR_NAME . '.php' ) {
        return $links;
    }

    $dashboard_link = '<a href="' . admin_url( 'admin.php?page=' . OHDEAR_PAGE ) . '">' . esc_html__( 'Dashboard', 'ohdear' ) . '</a>';
    $settings_link  = '<a href="' . admin_url( 'admin.php?page=' . OHDEAR_PAGE . '-settings' ) . '">' . esc_html__( 'Settings', 'ohdear' ) . '</a>';

    array_unshift( $links, $dashboard_link, $settings_link );

    return $links;
}

add_filter( 'plugin_action_links', 'OhDear\add_action_links', 10, 2 );
