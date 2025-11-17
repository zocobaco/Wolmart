<?php
defined( 'ABSPATH' ) || die;
?>
<h2><?php esc_html_e( 'Lazyload', 'wolmart' ); ?></h2>
<form method="post" class="wolmart_submit_form">
	<p><?php esc_html_e( 'This will help you make your site faster by lazyloading images and contents.', 'wolmart' ); ?></p>

	<label class="checkbox checkbox-inline">
		<input type="checkbox" name="lazyload" <?php checked( wolmart_get_option( 'lazyload' ) ); ?>> <?php esc_html_e( 'Lazyload Images', 'wolmart' ); ?>
	</label>
	<p style="margin: 10px 0 20px;">
		<?php esc_html_e( "All image resources will be lazyloaded so that page's loading speed gets faster.", 'wolmart' ); ?>
		<br>
		<?php esc_html_e( 'Use with caution! Disable this option if you have any compability problems.', 'wolmart' ); ?>
	</p>

	<label class="checkbox checkbox-inline">
		<input type="checkbox" name="lazyload_menu" <?php checked( wolmart_get_option( 'lazyload_menu' ) ); ?>> <?php esc_html_e( 'Lazyload Menus', 'wolmart' ); ?>
	</label>
	<p style="margin: 10px 0 20px;">
		<?php esc_html_e( 'Menus will be lazyloaded and cached in browsers for faster load.', 'wolmart' ); ?>
		<br>
		<?php esc_html_e( 'Cached menus will be updated after they have been changed or customizer panel has been saved.', 'wolmart' ); ?>
	</p>

	<label class="checkbox checkbox-inline">
		<input type="checkbox" name="skeleton" <?php checked( wolmart_get_option( 'skeleton_screen' ) ); ?>> <?php esc_html_e( 'Skeleton Screen', 'wolmart' ); ?>
	</label>
	<p style="margin: 10px 0 20px;"><?php esc_html_e( 'Instead of real content, skeleton is used to enhance speed of page loading and makes it more beautiful.', 'wolmart' ); ?></p>

	<label class="checkbox checkbox-inline">
		<input type="checkbox" name="webfont" <?php checked( wolmart_get_option( 'google_webfont' ) ); ?>> <?php esc_html_e( 'Enable Google Web Font Lazyload', 'wolmart' ); ?>
	</label>
	<!-- <p style="margin: 10px 0 20px;"> -->
	<?php
		// printf(
		// 	/* translators: %s values are docs urls */
		// 	esc_html__( 'Using %1$sWeb Font Loader%2$s, you can enhance page loading speed by about 4 percent in %3$sGoogle PageSpeed Insights%4$s for both mobile and desktop.', 'wolmart' ),
		// 	'<a href="https://developers.google.com/fonts/docs/webfont_loader" target="_blank">',
		// 	'</a>',
		// 	'<a href="https://developers.google.com/speed/pagespeed/insights/" target="_blank">',
		// 	'</a>'
		// );
	?>
	<!-- </p> -->

	<p class="wolmart-admin-panel-actions">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-light"><?php esc_html_e( 'Skip this step', 'wolmart' ); ?></a>
		<button type="submit" class="button-dark button button-large button-next" name="save_step" /><?php esc_html_e( 'Save & Continue', 'wolmart' ); ?></button>
		<?php wp_nonce_field( 'wolmart-setup-wizard' ); ?>
	</p>
</form>
