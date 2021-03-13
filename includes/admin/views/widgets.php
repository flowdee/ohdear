<?php
/**
 * Wordpress Dashboard Widgets
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register Dashboard Widgets
 *
 * @return void
 */
function register_dashboard_widgets() {

    $settings = get_settings();

    if ( empty( $settings['api_status'] ) || empty( $settings['site_selector'] ) )
        return;

    if ( ! is_user_roles_granted() )
        return;

    wp_add_dashboard_widget( 'dashboard_ohdear_uptime',      __( 'Oh Dear Uptime','ohdear' ),       '\OhDear\render_dashboard_widget' );
    wp_add_dashboard_widget( 'dashboard_ohdear_performance', __( 'Oh Dear Performance','ohdear' ),  '\OhDear\render_dashboard_widget' );
    wp_add_dashboard_widget( 'dashboard_ohdear_broken',      __( 'Oh Dear Broken Links','ohdear' ), '\OhDear\render_dashboard_widget' );
}
add_action( 'wp_dashboard_setup', '\OhDear\register_dashboard_widgets' );

/**
 * Render Dashboard Widgets
 *
 * @return void
 */
function render_dashboard_widget() {
	?>
        <div class="community-events-loading hide-if-no-js"><?php _e( 'Loading&hellip;' ); ?></div>
    <?php
}

/**
 * Load Uptime Dashboard Widget
 *
 * @return void
 */
function load_uptime_widget() {

    $data = prepare_widget_data();

    if ( ! $data )
        die();
    ?>

	<div class="dashboard_ohdear">

        <?php
            $u_height = '200';
            $u_days = 7;

            include_once 'templates/uptime.php';

            render_widget_bottom();
        ?>
	</div>

	<?php die();
}
add_action( 'wp_ajax_ohdear_load_uptime_widget', '\OhDear\load_uptime_widget' );

/**
 * Load Performance Dashboard Widget
 *
 * @return void
 */
function load_perf_widget() {

    $data = prepare_widget_data();

    if ( ! $data )
        die();
    ?>

    <div class="dashboard_ohdear">

        <?php
            $p_height = '200';
            $p_hide_legend = 1;

            include_once 'templates/performance.php';

            render_widget_bottom();
        ?>
	</div>

	<?php die();
}
add_action( 'wp_ajax_ohdear_load_perf_widget', '\OhDear\load_perf_widget' );

/**
 * Load Broken Links Dashboard Widget
 *
 * @return void
 */
function load_broken_widget() {

    $data = prepare_widget_data();

    if ( ! $data )
        die();
    ?>

    <div class="dashboard_ohdear">
        <?php
            include_once 'templates/broken.php';

            render_widget_bottom( 'broken links' );
        ?>
	</div>

	<?php die();
}
add_action( 'wp_ajax_ohdear_load_broken_widget', '\OhDear\load_broken_widget' );

/**
 * @return array|bool|mixed
 */
function prepare_widget_data() {

    $settings = get_settings();

    if ( empty( $settings['api_status'] ) || empty( $settings['site_selector'] ) )
        return false;

    if ( ! is_user_roles_granted() )
        return false;

    $data = get_site_data( $settings['site_selector'] );

    if ( empty( $data['id'] ) || empty( $data['url'] ) ) {

        debug_log( 'ERROR: ' . 'admin/views/widgets.php' . ' | ' . '$data is incorrect' );
        return false;
    }

    return $data;
}

/**
 * @param string $name
 *
 * @return void
 */
function render_widget_bottom( $name = 'statistics' ) {
    ?>
        <p class="community-events-footer help" style="text-align: center;">
            <?php
                /* translators: 1: Plugin page link, 2: Phrase: 'View all statistics' or 'View all broken links' */
                printf( '<a href="%1$s" title="%2$s">%2$s</a>',
                        esc_url( ( admin_url( 'admin.php?page=' . OHDEAR_PAGE ) ) ),
                        __( 'View all ' . $name, 'ohdear' )
                );
            ?>
        </p>
    <?php
}