<?php
/**
 * Settings Template
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
?>
<form method="post" action="options.php">
    <table class="form-table">
        <?php
            settings_fields( 'ohdear_settings' );
            do_settings_fields( 'ohdear_settings_' . $active_tab, 'ohdear_settings_' . $active_tab );
        ?>
    </table>
    <?php submit_button(); ?>
</form>
