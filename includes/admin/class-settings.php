<?php
/**
 * Settings Class
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

class OhDear_Settings {

    private $settings;

    /**
     * OhDear_Settings constructor.
     */
    public function __construct() {

        $this->settings = get_settings();

        // Set up.
        add_action( 'admin_init', array( $this, 'register_settings' ) );
    }

    /**
     * Add all settings sections and fields
     */
    public function register_settings() {

        if ( false == get_option( 'ohdear_settings' ) )
            add_option( 'ohdear_settings' );

        foreach( $this->get_registered_settings() as $tab => $settings ) {

            add_settings_section(
                'ohdear_settings_' . $tab,
                __return_null(),
                '__return_false',
                'ohdear_settings_' . $tab
            );

            foreach ( $settings as $key => $option ) {

                if( $option['type'] == 'checkbox' || $option['type'] == 'multicheck' || $option['type'] == 'radio' ) {
                    $name = isset( $option['name'] ) ? $option['name'] : '';
                } else {
                    $name = isset( $option['name'] ) ? '<label for="ohdear_settings[' . $key . ']">' . $option['name'] . '</label>' : '';
                }

                $callback = ! empty( $option['callback'] ) ? $option['callback'] : array( $this, $option['type'] . '_callback' );

                add_settings_field(
                    'ohdear_settings[' . $key . ']',
                    $name,
                    is_callable( $callback ) ? $callback : array( $this, 'missing_callback' ),
                    'ohdear_settings_' . $tab,
                    'ohdear_settings_' . $tab,
                    array(
                        'id'       => $key,
                        'desc'     => ! empty( $option['desc'] ) ? $option['desc'] : '',
                        'name'     => isset( $option['name'] ) ? $option['name'] : null,
                        'section'  => $tab,
                        'size'     => isset( $option['size'] ) ? $option['size'] : null,
                        'max'      => isset( $option['max'] ) ? $option['max'] : null,
                        'min'      => isset( $option['min'] ) ? $option['min'] : null,
                        'step'     => isset( $option['step'] ) ? $option['step'] : null,
                        'options'  => isset( $option['options'] ) ? $option['options'] : '',
                        'std'      => isset( $option['std'] ) ? $option['std'] : '',
                        'disabled' => isset( $option['disabled'] ) ? $option['disabled'] : '',
                        'class'    => isset( $option['class'] ) ? $option['class'] : ''
                    )
                );
            }
        }

        // Creates our settings in the options table
        register_setting( 'ohdear_settings', 'ohdear_settings', array( $this, 'sanitize_settings' ) );
    }

    /**
     * Retrieve the array of plugin settings
     *
     * @return array
     */
    function sanitize_settings( $input = array() ) {

        //debug_log( __CLASS__ . ' >>> ' . __FUNCTION__ );

        //debug_log( ' $_POST:' );
        //debug_log( $_POST );

        //debug_log( ' $input:' );
        //debug_log( $input );

        if ( empty( $_POST['_wp_http_referer'] ) )
            return $input;

        parse_str( $_POST['_wp_http_referer'], $referrer );

        $saved = get_settings();

        if ( ! is_array( $saved ) )
            $saved = array();

        $settings = $this->get_registered_settings();
        $tab      = isset( $referrer['tab'] ) ? $referrer['tab'] : 'stats';

        $input = $input ? $input : array();

        /**
         * Handle API connection
         */
        $api_status = ( isset( $this->settings['api_status'] ) ) ? $this->settings['api_status'] : false;
        $api_error  = ( isset( $this->settings['api_error'] ) ) ? $this->settings['api_error'] : '';

        if ( ! empty( $input['api_key'] ) ) {

            $api_key     = ( isset( $this->settings['api_key'] ) ) ? $this->settings['api_key'] : '';
            $api_key_new = esc_html( $input['api_key'] );

            if ( $api_key_new != $api_key ) {

                delete_cache();

                $result = ohdear()->api->verify_api_credentials( $api_key_new );

                // bool
                $api_status = ( is_array( $result ) && empty( $result['error'] ) );

                $api_error = ( ! empty( $result['error'] ) ) ? $result['error'] : '';

                // Sanitized API Key input value
                $input['api_key'] = $api_key_new;
            }

        } else {
            // API key empty leads always to a false status
            $api_status = false;
        }

        $input['api_status'] = $api_status;
        $input['api_error']  = $api_error;

        // Hook
        $input = apply_filters( 'ohdear_settings_validate_input', $input );

        /**
         * Filters the input value for the Oh Dear settings tab.
         *
         * This filter is appended with the tab name, followed by the string `_sanitize`, for example:
         *
         *     `ohdear_settings_stats_sanitize`
         *     `ohdear_settings_settings_sanitize`
         *
         * @param mixed $input The settings tab content to sanitize.
         */
        $input = apply_filters( 'ohdear_settings_' . $tab . '_sanitize', $input );

        // Ensure a value is always passed for every checkbox
        if ( ! empty( $settings[ $tab ] ) ) {
            foreach ( $settings[ $tab ] as $key => $setting ) {

                // Single checkbox
                if ( isset( $settings[ $tab ][ $key ][ 'type' ] ) && 'checkbox' == $settings[ $tab ][ $key ][ 'type' ] ) {
                    $input[ $key ] = ! empty( $input[ $key ] );
                }

                // Multicheck list
                if ( isset( $settings[ $tab ][ $key ][ 'type' ] ) && 'multicheck' == $settings[ $tab ][ $key ][ 'type' ] ) {
                    if( empty( $input[ $key ] ) ) {
                        $input[ $key ] = array();
                    }
                }

                // @Todo: Add Multiselect list
            }
        }

        // Loop through each setting being saved and pass it through a sanitization filter
        foreach ( $input as $key => $value ) {

            // Get the setting type (checkbox, select, etc)
            $type              = isset( $settings[ $tab ][ $key ][ 'type' ] ) ? $settings[ $tab ][ $key ][ 'type' ] : false;
            $sanitize_callback = isset( $settings[ $tab ][ $key ][ 'sanitize_callback' ] ) ? $settings[ $tab ][ $key ][ 'sanitize_callback' ] : false;
            $input[ $key ]     = $value;

            if ( $type ) {

                if ( $sanitize_callback && is_callable( $sanitize_callback ) )
                    add_filter( 'ohdear_settings_sanitize_' . $type, $sanitize_callback, 10, 2 );

                /**
                 * Filters the sanitized value for a setting of a given type.
                 *
                 * This filter is appended with the setting type (checkbox, select, etc), for example:
                 *
                 *     `ohdear_settings_sanitize_checkbox`
                 *     `ohdear_settings_sanitize_select`
                 *
                 * @param array  $value The input array and settings key defined within.
                 * @param string $key   The settings key.
                 */
                $input[ $key ] = apply_filters( 'ohdear_settings_sanitize_' . $type, $input[ $key ], $key );
            }

            /**
             * General setting sanitization filter
             *
             * @param array  $input[ $key ] The input array and settings key defined within.
             * @param string $key           The settings key.
             */
            $input[ $key ] = apply_filters( 'ohdear_settings_sanitize', $input[ $key ], $key );

            // Now remove the filter
            if( $sanitize_callback && is_callable( $sanitize_callback ) ) {

                remove_filter( 'ohdear_settings_sanitize_' . $type, $sanitize_callback, 10 );

            }
        }

        add_settings_error( 'ohdear-notices', '', __( 'Settings updated.', 'ohdear' ), 'updated' );

        $saved['current_site_data'] = ohdear()->api->get_site_data();

        return array_merge( $saved, $input );

    }

    /**
     * Retrieve the array of plugin settings
     *
     * @return array
     */
    public function get_registered_settings() {

        return array(
            'stats' => array(),

            'settings' => array(

                'api_status' => array(
                    'name' => __( 'API Status', 'ohdear' ),
                    'type' => 'descriptive_text',
                    'desc' => $this->api_status_render()
                ),

                'api_key' => array(
                    'name' => __( 'API Token', 'ohdear' ),
                    'type' => 'text',
                    'desc' => ''
                ),

                'exclude_user_roles' => array(
                    'name' => __( 'Exclude the Oh Dear analytics page access from these user roles', 'ohdear' ),
                    'type' => 'multiselect',
                    'desc' => '',
                )
            )
        );
    }

    /**
     * Multicheck Callback
     *
     * Renders multiple checkboxes.
     *
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function multicheck_callback( $args ) {

        if ( ! empty( $args['options'] ) ) {
            foreach( $args['options'] as $key => $option ) {
                if( isset( $this->settings[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
                echo '<label for="ohdear_settings[' . $args['id'] . '][' . $key . ']">';
                echo '<input name="ohdear_settings[' . $args['id'] . '][' . $key . ']" id="ohdear_settings[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
                echo $option . '</label><br/>';
            }
            echo '<p class="description">' . $args['desc'] . '</p>';
        }
    }
    
    /**
     * Multiselect Callback
     *
     * Renders multiselect dropdown.
     *
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function multiselect_callback( $args ) {

        // @Todo: needs enhancement

        echo '<label for="ohdear_settings[' . $args['id'] . '][]">';
        echo '<select multiple name="ohdear_settings[' . $args['id'] . '][]" id="ohdear_settings[' . $args['id'] . '][]">';
        $this->get_user_roles();
        echo '</select>';
    }

    /**
     * Text Callback
     *
     * Renders text fields.
     *
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function text_callback( $args ) {

        if ( isset( $this->settings[ $args['id'] ] ) && ! empty( $this->settings[ $args['id'] ] ) ) {
            $value = $this->settings[ $args['id'] ];
        } else {
            $value = isset( $args['std'] ) ? $args['std'] : '';
        }

        $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
        $html = '<input type="text" class="' . $size . '-text" id="ohdear_settings[' . $args['id'] . ']" name="ohdear_settings[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '" />';
        $html .= '<p class="description">'  . $args['desc'] . '</p>';

        echo $html;
    }

    /**
     * Descriptive text callback.
     *
     * Renders descriptive text onto the settings field.
     *
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function descriptive_text_callback( $args ) {
        $html = wp_kses_post( $args['desc'] );

        echo $html;
    }

    /**
     * Missing Callback
     *
     * If a function is missing for settings callbacks alert the user.
     *
     * @param array $args Arguments passed by the setting
     * @return void
     */
    public function missing_callback($args) {
        printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'ohdear' ), $args['id'] );
    }

    /**
     * API status
     *
     * @return string
     */
    public function api_status_render() {

        // bool
        $api_status = ( isset( $this->settings['api_status'] ) ) ? $this->settings['api_status'] : false;

        $color = ( $api_status ) ? 'green' : 'red';
        $status = __( ( $api_status ) ? 'Connected' : 'Disconnected', 'ohdear' );

        return '<span style="font-weight: bold; color: ' . $color . '">' . $status . '</span>';
    }

    /**
     * Retrieve the array of user roles.
     */
    private function get_user_roles( $selected = array() ) {

//        $selected = array( 'editor', 'contributor' );

        $r = '';
        $editable_roles = get_editable_roles();

        foreach ( $editable_roles as $role => $details ) {
            $name = translate_user_role( $details['name'] );
            $r .= "\n\t<option " . selected( in_array( $role, $selected ) ) . " value='" . esc_attr( $role ) . "'>$name</option>";
        }

        echo $r;
    }
}