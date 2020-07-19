<?php
/**
 * Stats Template
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$settings = get_settings(); ?>

<h2><?php esc_html_e( 'Oh Dear Monitoring', 'ohdear' ); ?></h2>

<?php if ( ! empty( $settings['api_status'] ) && ! empty( $settings['site_selector'] ) ) :

    $data = get_site_data(); ?>

    <p><?php printf( '<a href="https://ohdear.app/sites/%1$s" title="%2$s" target="_blank">%2$s</a>', $data['id'], $data['url'] ); ?></p>

    <?php
    include_once 'dashboard-templates/uptime.php';
    include_once 'dashboard-templates/performance.php';
    include_once 'dashboard-templates/broken.php';

elseif ( empty( $settings['api_status'] ) ) : ?>

    <div class="notice notice-warning inline">
        <p><?php printf( __( 'Please set the valid Oh Dear API token at <a href="%1$s" title="%2$s">%2$s</a> tab.', 'ohdear' ),
                            esc_url( add_query_arg( array( 'tab' => 'settings' ) ) ),
                            esc_attr( 'Settings', 'ohdear' )
                );
            ?>
        </p>
    </div>

<?php else : ?>

    <div class="notice notice-warning inline">
        <p><?php printf( __( 'Please set the site at <a href="%1$s" title="%2$s">%2$s</a> tab.', 'ohdear' ),
                            esc_url( add_query_arg( array( 'tab' => 'settings' ) ) ),
                            esc_attr( 'Settings', 'ohdear' )
                );
            ?>
        </p>
    </div>

<?php endif;