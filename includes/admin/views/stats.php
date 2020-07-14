<?php
/**
 * Stats Template
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

$settings = get_settings();

if ( ! empty( $settings['api_status'] ) ) :

    $data = ohdear()->api->get_site_data();

    if ( is_array( $data ) && ! empty( $data ) ) :

echo '<pre>';
var_dump( $data );
echo '</pre>';

    else : ?>

        <div class="notice notice-warning inline">
            <p>
                <?php _e( 'Sorry, no any data was received for the current site', 'ohdear' );
                ?>
            </p>
        </div>

    <?php endif;
else : ?>

    <div class="notice notice-warning inline">
        <p>
            <?php printf( __( 'Please set the valid Oh Dear API token at the <a href="%1$s" alt="%2$s">%2$s</a> tab.', 'ohdear' ),
                esc_url( add_query_arg( array( 'tab' => 'settings' ) ) ),
                esc_attr( 'Settings', 'ohdear' ) );
            ?>
        </p>
    </div>

<?php endif;