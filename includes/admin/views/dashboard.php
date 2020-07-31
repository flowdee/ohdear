<?php
/**
 * Monitoring Dashboard Template
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$settings = get_settings();

if ( empty( $settings['api_status'] ) || empty( $settings['site_selector'] ) ) {

// @Todo: finish redirect to settings page
//    $url = add_query_arg( array( 'page' => OHDEAR_PAGE . '-settings' ), admin_url( 'admin.php' ) );
//
//debug_log( '$url: ' . $url );
//
//    wp_safe_redirect( $url ); exit;
} ?>

<h2><?php esc_html_e( 'Oh Dear Monitoring', 'ohdear' ); ?></h2>

<?php if ( empty( $settings['api_status'] ) ) : ?>

    <div class="notice notice-warning inline">
        <p>
            <?php /* translators: 1: Plugin settings page link, 2: 'Settings' word */
                printf( __( 'Please set the valid Oh Dear API token at <a href="%1$s" title="%2$s">%2$s</a>.', 'ohdear' ),
                        esc_url( add_query_arg( array( 'page' => OHDEAR_PAGE . '-settings' ), admin_url( 'admin.php' ) ) ),
                        esc_attr__( 'Settings page', 'ohdear' )
                );
            ?>
        </p>
    </div>

<?php elseif ( empty( $settings['site_selector'] ) ) : ?>

    <div class="notice notice-warning inline">
        <p>
            <?php /* translators: 1: Plugin settings page link, 2: 'Settings' word */
                printf( __( 'Please set the site at <a href="%1$s" title="%2$s">%2$s</a>.', 'ohdear' ),
                    esc_url( add_query_arg( array( 'page' => OHDEAR_PAGE . '-settings' ), admin_url( 'admin.php' ) ) ),
                        esc_attr__( 'Settings page', 'ohdear' )
                );
            ?>
        </p>
    </div>

<?php else :

    $data = get_site_data( $settings['site_selector'] );

    if ( empty( $data['id'] ) || empty( $data['url'] ) ) {

        debug_log( 'ERROR: ' . 'admin/views/dashboard.php' . ' | ' . '$data is incorrect' );
        return;
    }
?>
    <p><?php printf( '<a href="https://ohdear.app/sites/%1$s" title="%2$s" target="_blank">%2$s</a>', $data['id'], $data['url'] ); ?></p>

<?php
    include_once 'templates/uptime.php';
    include_once 'templates/performance.php';
    include_once 'templates/broken.php';

endif;