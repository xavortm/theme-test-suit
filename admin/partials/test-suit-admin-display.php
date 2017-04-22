<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       xavortm.com
 * @since      1.0.0
 *
 * @package    Test_Suit
 * @subpackage Test_Suit/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
	<h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

	<form action="options.php" method="post">

		<?php if ( function_exists( 'wp_nonce_field' ) ) wp_nonce_field( 'plugin-name-action_' . 'yep' ); ?>

		<?php settings_fields( 'test_suit_checkboxes' ); ?>
		<?php do_settings_sections( 'test-suit' ); ?>

		<?php submit_button( 'Save Checklist' ); ?>
	</form>
</div><!-- /wrap -->