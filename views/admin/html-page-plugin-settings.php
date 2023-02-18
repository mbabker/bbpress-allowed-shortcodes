<div class="wrap">
	<h1 class="wp-heading-inline">
		<?php esc_html_e( 'bbPress Allowed Shortcodes Settings', 'bbpress-allowed-shortcodes' ); ?></h1>
	<hr class="wp-header-end">
	<form action="options.php" method="post">
		<?php settings_fields( 'bbpress-allowed-shortcodes' ); ?>
		<?php do_settings_sections( 'bbpress-allowed-shortcodes' ); ?>
		<?php submit_button(); ?>
	</form>
</div>
