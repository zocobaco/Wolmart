<?php
defined( 'ABSPATH' ) || die;
?>
<h2><?php esc_html_e( 'Page Builder', 'wolmart' ); ?></h2>
<p>
	<?php echo esc_html( 'Choose one of the following page builders.', 'wolmart' ); ?>
</p>
<form method="post" class="wolmart-page-builder">
	<ul class="wolmart-plugins">
		<li data-slug="elementor">
			<label class="checkbox checkbox-inline">
				<input type="radio" name="page-builder" <?php checked( 'elementor' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ); ?>>
				<?php echo esc_html( 'Elementor', 'wolmart' ); ?>
				<span class="info"> 
				<?php echo esc_html( 'The World\'s Leading WordPress Website Builder', 'wolmart' ); ?>
				</span>
			</label>
		</li>
		<li data-slug="js_composer">
			<label class="checkbox checkbox-inline">
				<input type="radio" name="page-builder" <?php checked( 'js_composer' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ); ?>>
				<?php echo esc_html( 'WPBakery', 'wolmart' ); ?>
				<span class="info"> <?php echo esc_html( 'WPBakery Page Builder plugin for WordPress', 'wolmart' ); ?> </span>
			</label>
		</li>
	</ul>

	<label class="checkbox checkbox-inline" style="margin-top: 15px;">
		<input type="checkbox" name="uninstall_page_builder" <?php checked( get_option( 'wolmart_uninstall_page_builder', true ) ); ?>>
		<span><?php esc_html_e( 'Automatically deactivate unchecked page builder plugin . ', 'wolmart' ); ?></span>
	</label>

	<p class="wolmart-admin-panel-actions">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-dark button button-large button-next" data-callback="select_page_builder"><?php esc_html_e( 'continue', 'wolmart' ); ?></a>
		<?php wp_nonce_field( 'wolmart - setup - wizard' ); ?>
				</p>
				</form>
