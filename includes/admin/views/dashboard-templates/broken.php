<?php

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Broken Links **/ ?>

<div class="dashboard__section">

    <h3><?php _e( 'Broken Links', 'ohdear' );

            printf( '<a href="%1$s" title="%2$s" target="_blank" class="title-link">%2$s</a>',
                    'https://ohdear.app/sites/' . $data['id'] . '/checks/' . $data['checks'][2]['id'] . '/report/',
                    esc_html__( 'Open Broken Links Dashboard', 'ohdear' )
            );
        ?>
    </h3>

    <?php
        $broken = ohdear()->api->get_broken();

        if ( ! empty( $broken['data'] ) ) : ?>

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

                    <th scope="col">
                        <?php esc_html_e( 'Actions', 'ohdear' ); ?>
                    </th>
                </tr>
                </thead>

                <tbody>
                <?php foreach ( $broken['data'] as $b_link ) : ?>
                    <tr>
                        <td class="column-primary"> <?php echo $b_link['status_code']; ?></td>

                        <td><?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['crawled_url'] ); ?></td>

                        <td><?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['found_on_url'] ); ?></td>

                        <td>
                            <?php $post_id = url_to_postid( $b_link['found_on_url'] );

                                if ( $post_id > 0 ) {

                                    printf( '<a href="%1$s" target="_blank">%2$s</a>',
                                            admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
                                            esc_html__( 'Edit', 'ohdear' )
                                    );

                                } else { echo '&nbsp;'; }
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

        <?php else : ?>
            <div class="notice notice-warning inline">
                <p>
                    <?php _e( 'No Broken Links were found for current site', 'ohdear' ); ?>
                </p>
            </div>
        <?php endif; ?>

</div><!-- /.dashboard__section -->