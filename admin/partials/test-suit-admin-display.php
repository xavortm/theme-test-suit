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

		<h2><?php _e( 'Check the links below to grab other testing tools for your theme', 'test-suit' ); ?></h2>

		<div class="test-suit-cards">
			<a target="_blank" class="test-suit-card" href="https://wpcom-themes.svn.automattic.com/demo/theme-unit-test-data.xml">
				<h3 class="card-title">Theme test data</h3>
				<p>Import the test data through the WordPress importer.</p>
			</a>

			<a target="_blank" class="test-suit-card" href="https://wordpress.org/plugins/theme-check/">
				<h3 class="card-title">WordPress Theme Check</h3>
				<p>The theme check plugin is an easy way to test your theme and make sure it’s up to spec with the latest theme review standards.</p>
			</a>

			<a target="_blank" class="test-suit-card" href="https://wordpress.org/plugins/monster-widget/">
				<h3 class="card-title">Monster Widget</h3>
				<p>The Monster widget consolidates all 13 core widgets into a single widget enabling theme developers to create multiple instances with ease.</p>
			</a>
		</div>

		<?php do_settings_sections( 'test-suit' ); ?>
		<?php submit_button( 'Save Checklist' ); ?>
	</form>
</div><!-- /wrap -->