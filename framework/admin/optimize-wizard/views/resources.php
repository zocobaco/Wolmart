<?php
defined( 'ABSPATH' ) || die;
?>
<h2><?php esc_html_e( 'Optimize Resources', 'wolmart' ); ?></h2>

<form method="post" class="wolmart-used-elements-form">
	<p><?php esc_html_e( 'This will help you to optimize theme styles.', 'wolmart' ); ?></p>
	<p class="descripion">
		<?php esc_html_e( 'Wolmart comes with powerful optimization wizard for theme styles. Detailed options for used components and helper classes will optimize your site perfectly.', 'wolmart' ); ?>
		<br>
		<?php esc_html_e( 'All options you have been checked will be saved for next use. After you have finished development, please run this wizard.', 'wolmart' ); ?>
	</p>

	<?php if ( defined( 'WPB_VC_VERSION' ) ) { ?>
		<p style="margin-bottom: 30px;"><?php esc_html_e( 'Please check used resources.', 'wolmart' ); ?></p>
		<div class="wolmart-used-resources">
			<div class="wolmart-loading"><i></i></div>
		</div>
	<?php } ?>

	<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
		<input type="checkbox" name="resource_disable_gutenberg" <?php checked( wolmart_get_option( 'resource_disable_gutenberg' ) ); ?>>
		<strong><?php esc_html_e( 'Gutenberg', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'If any gutenberg block doesn\'t be used in site, check me.', 'wolmart' ); ?></span>
	</label>

	<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
		<input type="checkbox" name="resource_disable_wc_blocks" <?php checked( wolmart_get_option( 'resource_disable_wc_blocks' ) ); ?>>
		<strong><?php esc_html_e( 'WooCommerce blocks for Gutenberg', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'If any WooCommerce Gutenberg blocks for doesn\'t be used in sites, check me.', 'wolmart' ); ?></span>
	</label>

	<?php if ( defined( 'ELEMENTOR_VERSION' ) ) { ?>
		<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
			<input type="checkbox" name="resource_disable_elementor" <?php checked( wolmart_get_option( 'resource_disable_elementor' ) ); ?>>
			<strong><?php esc_html_e( 'Elementor Resources', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'Check this to speed up your elementor site remarkably, if your site has no compatibility issue.', 'wolmart' ); ?></span>
		</label>
	<?php } ?>

	<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
		<input type="checkbox" name="resource_disable_fontawesome" <?php checked( wolmart_get_option( 'resource_disable_fontawesome' ) ); ?>>
		<strong><?php esc_html_e( 'Font Awesome Icons', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'If any font awesome icon doesn\'t be used in site, check me.', 'wolmart' ); ?></span>
	</label>

	<?php if ( class_exists( 'WeDevs_Dokan' ) ) { ?>
		<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
			<input type="checkbox" name="resource_disable_dokan" <?php checked( wolmart_get_option( 'resource_disable_dokan' ) ); ?>>
			<strong><?php esc_html_e( 'Dokan Resources', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'Check this to speed up your dokan site for not logged in users, if your site has no compatibility issue.', 'wolmart' ); ?></span>
		</label>
	<?php } ?>

	<h4 class="sub-title"><?php esc_html_e( ' - Change WordPress Defaults', 'wolmart' ); ?></h4>
	<p style="margin-bottom: .5rem;"><?php esc_html_e( 'You can dequeue WordPress default scripts that are not necessary for most websites.', 'wolmart' ); ?></p>

	<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
		<input type="checkbox" name="resource_disable_emojis" <?php checked( wolmart_get_option( 'resource_disable_emojis' ) ); ?>>
		<strong><?php esc_html_e( 'Emojis Script', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'By using this option, you can remove default WordPress emojis script.', 'wolmart' ); ?></span>
	</label>

	<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
		<input type="checkbox" name="resource_disable_jq_migrate" <?php checked( wolmart_get_option( 'resource_disable_jq_migrate' ) ); ?>>
		<strong><?php esc_html_e( 'jQuery Migrate Script', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'Please use this option if you are not using any deprecated jQuery code.', 'wolmart' ); ?></span>
	</label>

	<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
		<input type="checkbox" name="resource_jquery_footer" <?php checked( wolmart_get_option( 'resource_jquery_footer' ) ); ?>>
		<strong><?php esc_html_e( 'Load jQuery In Footer', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'Check this to enable defer loading of jQuery to the footer of the page.', 'wolmart' ); ?></span>
	</label>

	<h4 class="sub-title"><?php esc_html_e( ' - File Compression', 'wolmart' ); ?></h4>
	<p style="margin-bottom: .5rem;"><?php esc_html_e( 'If you active this option, it can increase the speed of your site. Because it reduces the request count and can cache files.', 'wolmart' ); ?></p>

	<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
		<input type="checkbox" name="resource_merge_stylesheets" <?php checked( wolmart_get_option( 'resource_merge_stylesheets' ) ); ?>>
		<strong><?php esc_html_e( 'Merge javascripts and stylesheets', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'Compile the dynamic CSS to files (a separate file will be created for each page inside of the uploads folder)', 'wolmart' ); ?></span>
	</label>

	<h4 class="sub-title"><?php esc_html_e( ' - Critical Css - Advanced Feature', 'wolmart' ); ?></h4>
	<p style="margin-bottom: .5rem;"><?php esc_html_e( 'If you check this option, you can see it in the admin menu. It helps your site to reduce the rendering time and increase the google page speed.', 'wolmart' ); ?></p>

	<label class="checkbox checkbox-inline" style="margin-bottom: 15px;">
		<input type="checkbox" name="resource_critical_css" <?php checked( wolmart_get_option( 'resource_critical_css' ) ); ?>>
		<strong><?php esc_html_e( 'Critical CSS', 'wolmart' ); ?></strong> - <span><?php esc_html_e( 'Generate the critical CSS.', 'wolmart' ); ?></span>
	</label>


	<p class="wolmart-admin-panel-actions">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-light"><?php esc_html_e( 'Skip this step', 'wolmart' ); ?></a>
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-dark button button-large button-next" data-callback="optimize_resources"><?php esc_html_e( 'Compile & Continue', 'wolmart' ); ?></a>
		<?php wp_nonce_field( 'wolmart-setup-wizard' ); ?>
	</p>
</form>
