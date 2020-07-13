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

        add_submenu_page( 'index.php', esc_html__( 'Oh Dear Analytics', 'ohdear' ), esc_html__( 'Oh Dear Analytics', 'ohdear' ), 'manage_options', OHDEAR_PAGE, array( $this, 'view_template' ) );
    }

    /**
     * Output admin template
     */
    public function view_template() {

        $tabs = $this->get_settings_tabs();

        $active_tab = ( isset( $_GET['tab'] ) && array_key_exists( $_GET['tab'], $tabs ) ) ? $_GET['tab'] : 'stats';

        ob_start();
        ?>
        <div class="wrap">

            <h2 class="nav-tab-wrapper">

                <?php
                foreach ( $tabs as $tab_id => $tab_name ) {
                    $tab_url = add_query_arg( array( 'tab' => $tab_id ) );

                    printf( '<a href="%1$s" alt="%2$s" class="%3$s">%2$s</a>',
                        esc_url( $tab_url ),
                        esc_attr( $tab_name ),
                        ( $active_tab == $tab_id ) ? 'nav-tab nav-tab-active' : 'nav-tab'
                    );
                }
                ?>
            </h2>

            <div id="tab_container">

                <?php include 'views/' . $active_tab . '.php'; ?>

            </div><!-- #tab_container-->

            <noscript>
                <div class="notice notice-warning inline">
                    <p>
                        <?php echo esc_html__( 'This page needs the JavaScript to be enabled', 'ohdear' ); ?>
                    </p>
                </div>
            </noscript>
        </div><!-- /.wrap -->
        <?php
        echo ob_get_clean();
    }

    /**
     * Retrieves the settings tabs.
     *
     * @return array $tabs Settings tabs.
     */
    private function get_settings_tabs() {

        $tabs = array(
            'stats'    => __( 'Stats', 'ohdear' ),
            'settings' => __( 'Settings', 'ohdear' )
        );

        /**
         * Filters the list of settings tabs.
         *
         * @since 1.0
         *
         * @param array $tabs Settings tabs.
         */
        return $tabs;
    }
}

$ohdear_menu = new OhDear_Admin_Menu;