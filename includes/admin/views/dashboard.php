<?php
/**
 * Monitoring Dashboard Template
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$settings = get_settings(); ?>

<h2><?php esc_html_e( 'Oh Dear Monitoring', 'ohdear' ); ?></h2>

<p class="ohdear-ask-for-review">
    <span class="dashicons dashicons-star-filled"></span> <?php printf( wp_kses( __( '<strong>Do you enjoy using Oh Dear</strong>? Then please <strong><a href="%s" target="_blank">leave a short review</a></strong> about the plugin on WordPress.org. This helps a lot!', 'ohdear' ), array(  'a' => array( 'href' => array(), 'target' => '_blank' ), 'strong' => array() ) ), esc_url( 'https://wordpress.org/support/plugin/ohdear/reviews/#new-post' ) ); ?>
</p>

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