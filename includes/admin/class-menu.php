<?php
/**
 * Class OhDear_Admin_Menu
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class OhDear_Admin_Menu {

    /**
     * OhDear_Admin_Menu constructor.
     */
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'register_menus' ) );
    }

    /**
     * Register menus
     *
     * @param $some_slug
     */
    public function register_menus( $some_slug ) {

        // @Todo: capability 'manage_options' displays for "admins" only. Expand for other user roles

        add_menu_page( esc_html__( 'Oh Dear', 'ohdear' ), esc_html__( 'Oh Dear', 'ohdear' ), 'manage_options', OHDEAR_PAGE, array( $this, 'view_dashboard' ), OHDEAR_PLUGIN_URL . 'assets/images/icon.png' );

        add_submenu_page( OHDEAR_PAGE, esc_html__( 'Dashboard', 'ohdear' ), esc_html__( 'Dashboard', 'ohdear' ), 'manage_options', OHDEAR_PAGE, array( $this, 'view_dashboard' ) );

        add_submenu_page( OHDEAR_PAGE, esc_html__( 'Settings', 'ohdear' ), esc_html__( 'Settings', 'ohdear' ), 'manage_options', OHDEAR_PAGE . '-settings', array( $this, 'view_settings' ) );
    }

    /**
     * Output dashboard page template
     */
    public function view_dashboard() {

//        ob_start();
        ?>
        <div class="wrap">

            <?php include 'views/dashboard.php'; ?>

        </div><!-- /.wrap -->
        <?php
//        echo ob_get_clean();
    }

    /**
     * Output settings page template
     */
    public function view_settings() {
        ?>
        <div class="wrap">

            <?php include 'views/settings.php'; ?>

        </div><!-- /.wrap -->
        <?php
    }

}

$ohdear_menu = new OhDear_Admin_Menu;