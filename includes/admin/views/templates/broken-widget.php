<?php
/**
 * Wordpress Dashboard Broken Links Widget
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( empty( $broken['data'] ) ) {

    debug_log( 'ERROR: ' . 'admin/views/templates/broken-widget.php' . ' | ' . '$broken is incorrect' );
    return;
}

foreach ( $broken['data'] as $b_link ) : ?>

    <div class="community-events-footer">

        <p>
            <span class="bold"><?php echo esc_html__( 'Status Code', 'ohdear' ) . ':'; ?></span>

            <?php echo $b_link['status_code']; ?>
        </p>

        <p>
            <span class="bold"><?php echo esc_html__( 'Broken Link', 'ohdear' ) . ':'; ?></span>

            <?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['crawled_url'] ); ?>
        </p>

        <p>
            <span class="bold"><?php echo esc_html__( 'Found on', 'ohdear' ) . ':'; ?></span>

            <?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['found_on_url'] ); ?>
        </p>

    </div>

<?php endforeach;