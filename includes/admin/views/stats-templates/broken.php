<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Broken Links **/ ?>

<div class="stats__section">

    <h3><?php _e( 'Broken Links', 'ohdear' ); ?></h3>

    <?php if ( ! empty( $broken['data'] ) ) :

        $b_links = $broken['data']; ?>

        <p><?php echo __( 'Last time checked', 'ohdear' ) . ': ' . $data['checks'][2]['latest_run_ended_at']; ?></p>

        <table class="wp-list-table widefat striped">
            <thead>
            <tr>
                <th class="column-primary" scope="col">
                    <?php esc_html_e( 'Status Code', 'ohdear' ); ?>
                </th>

                <th scope="col">
                    <?php esc_html_e( 'Broken Link', 'ohdear' ); ?>
                </th>

                <th scope="col">
                    <?php esc_html_e( 'Found on', 'ohdear' ); ?>
                </th>
            </tr>
            </thead>

            <tbody>
            <?php foreach ( $b_links as $b_link ) : ?>
                <tr>
                    <td class="column-primary"> <?php echo $b_link['status_code']; ?></td>

                    <td><?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['crawled_url'] ); ?></td>

                    <td><?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['found_on_url'] ); ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

    <?php else : ?>
        <div class="notice notice-warning inline">
            <p>
                <?php _e( 'No Broken Links was found for current site', 'ohdear' ); ?>
            </p>
        </div>
    <?php endif; ?>

</div><!-- /.stats__section -->

<?php