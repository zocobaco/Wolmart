<?php
defined( 'ABSPATH' ) || die;
?>
<h2 style="margin-bottom: 20px"><?php esc_html_e( 'Performance', 'wolmart' ); ?></h2>

<form method="post" class="wolmart_submit_form">

	<h3><?php esc_html_e( '1. Optimize Mobile', 'wolmart' ); ?></h3>

	<label class="checkbox checkbox-inline" style="margin-bottom: 10px">
		<input type="checkbox" name="mobile_disable_animation" <?php checked( wolmart_get_option( 'mobile_disable_animation' ) ); ?>> <?php esc_html_e( 'Disable Mobile Animations', 'wolmart' ); ?>
	</label>
	<p style="margin: 0 0 20px;"><?php esc_html_e( 'Disable appear and slide animations in mobile.', 'wolmart' ); ?></p>

	<label class="checkbox checkbox-inline" style="margin-bottom: 10px">
		<input type="checkbox" name="mobile_disable_slider" <?php checked( wolmart_get_option( 'mobile_disable_slider' ) ); ?>> <?php esc_html_e( 'Disable Mobile Sliders', 'wolmart' ); ?>
	</label>
	<p style="margin: 0 0 20px;"><?php esc_html_e( 'Use scrollbar instead of carousel for only products and posts in mobile.', 'wolmart' ); ?></p>

	<h3><?php esc_html_e( '2. Serve Fonts', 'wolmart' ); ?></h3>
	<h4 class="sub-title">
		<?php esc_html_e( '- Preload Webfonts', 'wolmart' ); ?>
	</h4>
	<p style="margin-bottom: .5rem">
		<?php /* translators: Google Page Speed url */ ?>
		<?php printf( esc_html__( 'This improves page load time as the browser caches preloaded resources so they are available immediately when needed. By using this option, you can increase page speed about 1 ~ 4 percent in %1$sGoogle PageSpeed Insights%2$s for both of mobile and desktop.', 'wolmart' ), '<a href="https://developers.google.com/speed/pagespeed/insights/" target="_blank">', '</a>' ); ?>
	</p>
	<p>
		<label class="checkbox checkbox-inline">
		<?php
			$preload_fonts = wolmart_get_option( 'preload_fonts' );
		if ( empty( $preload_fonts ) ) {
			$preload_fonts = array();
		}
		?>
			<input type="checkbox" value="wolmart" name="preload_fonts[]" <?php checked( in_array( 'wolmart', $preload_fonts ) ); ?>> <?php esc_html_e( 'Wolmart Icons', 'wolmart' ); ?>
		</label>&nbsp;
		<?php if ( ! wolmart_get_option( 'resource_disable_fontawesome' ) ) : ?>
		<label class="checkbox checkbox-inline">
			<input type="checkbox" value="fas" name="preload_fonts[]" <?php checked( in_array( 'fas', $preload_fonts ) ); ?>> <?php esc_html_e( 'Font Awesome 6 Solid', 'wolmart' ); ?>
		</label>&nbsp;
		<label class="checkbox checkbox-inline">
			<input type="checkbox" value="far" name="preload_fonts[]" <?php checked( in_array( 'far', $preload_fonts ) ); ?>> <?php esc_html_e( 'Font Awesome 6 Regular', 'wolmart' ); ?>
		</label>&nbsp;
		<label class="checkbox checkbox-inline">
			<input type="checkbox" value="fab" name="preload_fonts[]" <?php checked( in_array( 'fab', $preload_fonts ) ); ?>> <?php esc_html_e( 'Font Awesome 6 Brands', 'wolmart' ); ?>
		</label>&nbsp;
		<?php endif; ?>
		<br>
		<br>
		<label><?php esc_html_e( 'Please input other resources that will be pre loaded. Ex. https://d-themes.com/wordpress/wolmart/wp-content/themes/wolmart-child/fonts/custom.woff2.', 'wolmart' ); ?></label>
		<textarea class="form-control input-text" name="preload_fonts_custom" style="width: 100%; margin-top: .4rem" rows="4" value="<?php echo isset( $preload_fonts['custom'] ) ? esc_attr( $preload_fonts['custom'] ) : ''; ?>"><?php echo isset( $preload_fonts['custom'] ) ? esc_html( $preload_fonts['custom'] ) : ''; ?></textarea>
	</p>

	<h4 class="sub-title">
		<?php esc_html_e( '-Font Face Rendering', 'wolmart' ); ?>
	</h4>
	<p style="margin-bottom: .5rem">
		<?php /* translators: Google Page Speed url */ ?>
		<?php printf( esc_html__( 'Choosing "Swap" for font-display will ensure text remains visible during webfont load and this will improve page speed score in %1$sGoogle PageSpeed Insights%2$s for both of mobile and desktop.', 'wolmart' ), '<a href="https://developers.google.com/speed/pagespeed/insights/" target="_blank">', '</a>' ); ?>
	</p>
	<p>
		<label class="checkbox checkbox-inline">
			<input type="checkbox" name="font_face_display" <?php checked( wolmart_get_option( 'font_face_display' ) ); ?>> <?php esc_html_e( 'Swap for font display', 'wolmart' ); ?>
		</label>
	</p>

	<h3><?php esc_html_e( '3. Asynchronous Scripts', 'wolmart' ); ?></h3>

	<label class="checkbox checkbox-inline">
		<input type="checkbox" name="resource_async_js" <?php checked( wolmart_get_option( 'resource_async_js' ) ); ?>> <?php esc_html_e( 'Asynchronous load', 'wolmart' ); ?>
	</label>
	<p><?php esc_html_e( 'Some javascript libraries does not affect first paint. And you can increase page loading speed by loading them asynchronously.', 'wolmart' ); ?></p>

	<label class="checkbox checkbox-inline">
		<input type="checkbox" name="resource_split_tasks" <?php checked( wolmart_get_option( 'resource_split_tasks' ) ); ?>> <?php esc_html_e( 'Split tasks', 'wolmart' ); ?>
	</label>
	<p><?php esc_html_e( 'Long time tasks may cause unintentional rendering suspension or affect to its performance. To make pages faster, please check split task option.', 'wolmart' ); ?></p>

	<label class="checkbox checkbox-inline">
		<input type="checkbox" name="resource_idle_run" <?php checked( wolmart_get_option( 'resource_idle_run' ) ); ?>> <?php esc_html_e( 'Only necessary JS at loading', 'wolmart' ); ?>
	</label>
	<p><?php esc_html_e( 'While page is loaded, there exists a lot of unnecessary javascripts running during initialization. If they works in idle time and only necessary ones runs while loading time, page speed will be faster.', 'wolmart' ); ?></p>

	<label class="checkbox checkbox-inline">
		<input type="checkbox" name="resource_after_load" <?php checked( wolmart_get_option( 'resource_after_load' ) ); ?>> <?php esc_html_e( 'Process after load event', 'wolmart' ); ?>
	</label>
	<p><?php esc_html_e( 'This will accelerate page\'s load time. But this may cause compatibility issue since page still not be ready. It will be in ready state after document or window load event is ready. To fix this problem, Please add event handlers to window\'s "wolmart_complete" event.', 'wolmart' ); ?></p>

	<p class="wolmart-admin-panel-actions">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-light"><?php esc_html_e( 'Skip this step', 'wolmart' ); ?></a>
		<button type="submit" class="button-dark button button-large button-next" name="save_step"><?php esc_html_e( 'Save & Continue', 'wolmart' ); ?></button>
		<input type="hidden" name="css_js" id="css_js" value="<?php echo checked( wolmart_get_option( 'minify_css_js' ), true, false ) ? 'true' : 'false'; ?>">
		<input type="hidden" name="font_icons" id="font_icons" value="<?php echo checked( wolmart_get_option( 'minify_font_icons' ), true, false ) ? 'true' : 'false'; ?>">
		<?php wp_nonce_field( 'wolmart-setup-wizard' ); ?>
	</p>
</form>
