<?php
/**
 * Plugin Name:     Oh Dear
 * Plugin URI:      https://wordpress.org/plugins/ohdear/
 * Description:     This plugin brings Oh Dear uptime, performance and broken links monitoring into your WordPress dashboard.
 * Version:         1.0.2
 * Author:          KryptoniteWP
 * Author URI:      https://kryptonitewp.com
 * Text Domain:     ohdear
 * Domain Path:     /languages
 *
 * @author          KryptoniteWP
 * @copyright       Copyright (c) KryptoniteWP
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main OhDear Class
 */
final class OhDear {
    /** Singleton *************************************************************/

    /**
     * OhDear instance.
     *
     * @access private
     * @var    OhDear
     * @static
     */
    private static $instance;

    /**
     * The version number of OhDear.
     *
     * @access private
     * @var    string
     */
    private $version = '1.0.2';

    /**
     * The api instance variable.
     *
     * @access public
     * @var    OhDear_API
     */
    public $api;

    /**
     * The settings instance variable.
     *
     * @var    OhDear_Settings
     */
    public $settings;

    /**
     * Main OhDear Instance
     *
     * @return OhDear
     * @uses OhDear::setup_constants() Setup the globals needed
     * @uses OhDear::includes() Include the required files
     *
     * @staticvar array $instance
     */
    public static function instance() {

        if ( ! isset( self::$instance ) && ! ( self::$instance instanceof OhDear ) ) {
            self::$instance = new OhDear;

            if ( version_compare( PHP_VERSION, '5.6', '<' ) ) {

                add_action( 'admin_notices', array( 'OhDear', 'below_php_version_notice' ) );

                return self::$instance;
            }

            self::$instance->setup_constants();
            self::$instance->includes();

            add_action( 'plugins_loaded', array( self::$instance, 'setup_objects' ), - 1 );
            add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
        }

        return self::$instance;
    }

    /**
     * Show a warning to sites running PHP < 5.6
     *
     * @static
     * @access public
     * @return void
     */
    public static function below_php_version_notice() {
        echo '<div class="error"><p>' . __( 'Your version of PHP is below the minimum version of PHP required by this plugin. Please contact your host and request that your version be upgraded to 5.6 or later.', 'ohdear' ) . '</p></div>';
    }

    /**
     * Throw error on object clone
     *
     * The whole idea of the singleton design pattern is that there is a single
     * object therefore, we don't want the object to be cloned.
     *
     * @return void
     * @access public
     */
    public function __clone() {
        // Cloning instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ohdear' ), '1.0' );
    }

    /**
     * Disable unserializing of the class
     *
     * @return void
     * @access public
     */
    public function __wakeup() {
        // Unserializing instances of the class is forbidden
        _doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?', 'ohdear' ), '1.0' );
    }

    /**
     * Setup plugin constants
     *
     * @access private
     * @return void
     */
    private function setup_constants() {

        // Plugin version
        if ( ! defined( 'OHDEAR_VERSION' ) ) {
            define( 'OHDEAR_VERSION', $this->version );
        }

        // Plugin Folder Path
        if ( ! defined( 'OHDEAR_PLUGIN_DIR' ) ) {
            define( 'OHDEAR_PLUGIN_DIR', __DIR__ );
        }

        // Plugin Folder URL
        if ( ! defined( 'OHDEAR_PLUGIN_URL' ) ) {
            define( 'OHDEAR_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
        }

        // Plugin Folder name only
        if ( ! defined( 'OHDEAR_PLUGIN_DIR_NAME' ) ) {
            define( 'OHDEAR_PLUGIN_DIR_NAME', basename( __DIR__ ) );
        }

        // Plugin Page Slug
        if ( ! defined( 'OHDEAR_PAGE' ) ) {
            define( 'OHDEAR_PAGE', 'ohdear' );
        }
    }

    /**
     * Include required files
     *
     * @access private
     * @return void
     */
    private function includes() {

        require_once __DIR__ . '/includes/functions.php';

        if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

            require_once __DIR__ . '/includes/admin/scripts.php';
            require_once __DIR__ . '/includes/admin/class-api.php';
            require_once __DIR__ . '/includes/admin/class-menu.php';
            require_once __DIR__ . '/includes/admin/plugins.php';
            require_once __DIR__ . '/includes/admin/class-settings.php';
            require_once __DIR__ . '/includes/hooks.php';
            require_once __DIR__ . '/includes/admin/views/widgets.php';
        }
    }

    /**
     * Setup all objects
     *
     * @access public
     * @return void
     */
    public function setup_objects() {

        if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {

            self::$instance->api = new OhDear_API();
            self::$instance->settings = new OhDear_Settings();
        }
    }

    /**
     * Loads the plugin language files
     *
     * @access public
     * @return void
     */
    public function load_textdomain() {

        // Set filter for plugin's languages directory
        $lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';

        /**
         * Filters the languages directory path to use for OhDear.
         *
         * @param string $lang_dir The languages directory path.
         */
        $lang_dir = apply_filters( 'ohdear_languages_directory', $lang_dir );

        // Traditional WordPress plugin locale filter
        $locale = apply_filters( 'plugin_locale',  get_locale(), 'ohdear' );
        $mofile = sprintf( '%1$s-%2$s.mo', 'ohdear', $locale );

        // Setup paths to current locale file
        $mofile_local  = $lang_dir . $mofile;
        $mofile_global = WP_LANG_DIR . '/ohdear/' . $mofile;

        if ( file_exists( $mofile_global ) ) {
            // Look in global /wp-content/languages/ohdear/ folder
            load_textdomain( 'ohdear', $mofile_global );
        } elseif ( file_exists( $mofile_local ) ) {
            // Look in local /wp-content/plugins/ohdear/languages/ folder
            load_textdomain( 'ohdear', $mofile_local );
        } else {
            // Load the default language files
            load_plugin_textdomain( 'ohdear', false, $lang_dir );
        }
    }
}

/**
 * The main function responsible for returning the one true OhDear
 * Instance to functions everywhere.
 *
 * Example: <?php $ohdear = ohdear(); ?>
 *
 * @return void|OhDear The one true OhDear Instance
 */
function ohdear() {
    return OhDear::instance();
}

ohdear();