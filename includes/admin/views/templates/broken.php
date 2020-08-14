<?php

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/** Broken Links **/

if ( empty( $data['id'] ) ||
     empty( $data['checks'][2]['id'] ) ||
     empty( $data['checks'][2]['latest_run_ended_at'] )
){
    debug_log( 'ERROR: ' . 'admin/views/templates/broken.php' . ' | ' . '$data is incorrect' );
    return;
} ?>

<div class="ohdear-dashboard__section">

    <h3><?php echo __( 'Broken Links', 'ohdear' ) . '&nbsp;&nbsp;';

            /* translators: 1: Oh Dear API link, 2: 'Open on Oh Dear' phrase */
            printf( '<a href="%1$s" title="%2$s" target="_blank" style="font-weight: normal; font-size: small;">%2$s</a>',
                    'https://ohdear.app/sites/' . $data['id'] . '/checks/' . $data['checks'][2]['id'] . '/report/',
                    esc_html__( 'Open on Oh Dear', 'ohdear' )
            );
        ?>
    </h3>

    <?php
        $broken = ohdear()->api->get_broken();

        if ( ! empty( $broken['data'] ) ) : ?>

            <p><?php echo __( 'Last time checked', 'ohdear' ) . ': ' . $data['checks'][2]['latest_run_ended_at']; ?></p>

            <?php

           if ( ! empty( $_GET['action'] ) && $_GET['action'] == 'ohdear_load_broken_widget' ) :

               include_once 'broken-widget.php';

           else : ?>

                <table class="wp-list-table widefat striped">

                    <thead>

                        <tr>
                            <th class="column-primary" scope="col"><?php esc_html_e( 'Status Code', 'ohdear' ); ?></th>

                            <th scope="col"><?php esc_html_e( 'Broken Link', 'ohdear' ); ?></th>

                            <th scope="col"><?php esc_html_e( 'Found on', 'ohdear' ); ?></th>

                            <?php if ( ! empty( $data['sort_url'] ) && is_current_site( $data['sort_url'] ) ) : ?>

                                <th scope="col"><?php esc_html_e( 'Actions', 'ohdear' ); ?></th>

                            <?php endif; ?>
                        </tr>

                    </thead>

                    <tbody>

                    <?php foreach ( $broken['data'] as $b_link ) : ?>

                        <tr>
                            <td class="column-primary"> <?php echo $b_link['status_code']; ?></td>

                            <td><?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['crawled_url'] ); ?></td>

                            <td><?php printf( '<a href="%1$s" target="_blank">%1$s</a>', $b_link['found_on_url'] ); ?></td>

                            <?php if ( ! empty( $data['sort_url'] ) && is_current_site( $data['sort_url'] ) ) : ?>

                                <td>
                                    <?php $post_id = url_to_postid( $b_link['found_on_url'] );

                                    if ( $post_id > 0 ) {

                                        /* translators: 1: Post edit link, 2: 'Edit' word */
                                        printf( '<a href="%1$s" target="_blank">%2$s</a>',
                                            admin_url( 'post.php?post=' . $post_id . '&action=edit' ),
                                            esc_html__( 'Edit', 'ohdear' )
                                        );

                                    } else { echo '&nbsp;'; }
                                    ?>
                                </td>

                            <?php endif; ?>

                        </tr>

                    <?php endforeach; ?>

                    </tbody>

                </table>

           <?php endif;

       else : ?>

            <div class="notice notice-warning inline">
                <p>
                    <?php _e( 'No Broken Links for the current site', 'ohdear' ); ?>
                </p>
            </div>

        <?php endif; ?>

</div><!-- /.ohdear-dashboard__section -->