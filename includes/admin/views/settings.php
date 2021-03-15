<?php
/**
 * Settings Template
 */

namespace OhDear;

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit; ?>

<h2><?php esc_html_e( 'Oh Dear Settings', 'ohdear' ); ?></h2>

<form method="post" action="options.php">
    <table class="form-table">
        <?php
            settings_fields( 'ohdear_settings' );
            do_settings_fields( 'ohdear_settings_settings', 'ohdear_settings_settings' );
        ?>
    </table>
    <?php submit_button(); ?>
</form>