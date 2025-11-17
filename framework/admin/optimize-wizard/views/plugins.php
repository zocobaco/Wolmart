<?php
defined( 'ABSPATH' ) || die;
?>
<h2><?php esc_html_e( 'Plugins', 'wolmart' ); ?></h2>
<form method="post">
	<h3><?php esc_html_e( '1. Recommended Plugins', 'wolmart' ); ?></h3>
	<?php
	$plugins = $this->_get_plugins();
	if ( count( $plugins['all'] ) ) {
		?>
		<p style="margin-bottom: 5px;">
			<?php esc_html_e( 'This will install the plugins which can acclerate your site.', 'wolmart' ); ?><br>
			<?php esc_html_e( 'You should disable below plugins while in development. Changes may not be applied because of them.', 'wolmart' ); ?>
		</p>
		<ul class="wolmart-plugins">
			<?php
			foreach ( $plugins['all'] as $slug => $plugin ) {
				?>
				<li data-slug="<?php echo esc_attr( $slug ); ?>"<?php echo isset( $plugin['visibility'] ) && 'hidden' === $plugin['visibility'] ? ' class="hidden"' : ''; ?>>
					<label class="checkbox checkbox-inline">
						<input type="checkbox" name="setup-plugin"<?php echo ! $plugin['required'] ? '' : ' checked="checked"'; ?>><?php echo esc_html( $plugin['name'] ); ?>
						<span></span>
					</label>
					<div class="spinner"></div>
					<?php if ( $plugin['desc'] ) : ?>
						<p style="margin-top: 5px;margin-bottom: 15px;">
							<?php /* translators: %s: Plugin url and name */ ?>
							<?php printf( ' <a href="%s" target="_blank">%s</a>', 'https://wordpress.org/plugins/' . esc_attr( $slug ) . '/', $plugin['name'] ); ?><?php echo esc_html( $plugin['desc'] ); ?>
						</p>
					<?php endif; ?>
				</li>
				<?php if ( 'wolmart-core' === $plugin['slug'] ) : ?>
					<li class="separator"></li>
				<?php endif; ?>
			<?php } ?>
		</ul>
		<ul style="margin-bottom: 20px;">
			<li class="howto">
				<a href="https://gtmetrix.com/leverage-browser-caching.html" target="_blank" style="font-style: normal;"><?php esc_html_e( 'How to enable leverage browser caching.', 'wolmart' ); ?></a>
				<p style="margin-top: 0;font-style: normal;"><?php esc_html_e( 'Page loading duration can be significantly improved by asking visitors to save and reuse the files included in your website.', 'wolmart' ); ?></p>
			</li>
		</ul>
		<?php
	} else {
		echo '<p>' . esc_html__( 'Good News! All recommended plugins are already installed up-to-date.', 'wolmart' ) . '</p>';
	}
	?>

	<hr style="margin-bottom: 30px"/>

	<h3><?php esc_html_e( '2. Installed Plugins', 'wolmart' ); ?></h3>
	<p style="margin-bottom: 5px;"><?php esc_html_e( 'Please check active plugins. You can deactivate unnecessary plugins.', 'wolmart' ); ?></p>

	<ul class="installed-plugins">
		<li class="plugins-label">
			<label><?php echo esc_html__( 'Plugin Name', 'wolmart' ); ?></label>
			<span><?php echo esc_html__( 'Action', 'wolmart' ); ?></span>
		</li>
		<?php
		foreach ( $plugins['installed'] as $slug => $plugin ) {
			?>
		<li>
			<label data-version="<?php echo esc_attr( $plugin['Version'] ); ?>"><?php echo esc_html( $plugin['Name'] ); ?></label>
			<a href="<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Deactivate', 'wolmart' ); ?></a>
		</li>
			<?php
		}
		?>
		<?php
		if ( isset( $plugins['network_activated'] ) ) {
			foreach ( $plugins['network_activated'] as $slug => $plugin ) {
				?>
		<li>
			<label data-version="<?php echo esc_attr( $plugin['Version'] ); ?>"><?php echo esc_html( $plugin['Name'] ); ?></label>
			<span><?php echo esc_html__( 'Network Activate', 'wolmart' ); ?></span>
		</li>
				<?php
			}
		}
		?>
	</ul>

	<div class="form-checkbox">
		<input type="checkbox" id="share_plugins" name="allow_plugins_share" checked/><label style="font-weight: 600;"  for="share_plugins"><?php echo esc_html__( 'Share Plugins Information', 'wolmart' ); ?></label>
		<p style="margin: 5px 0 0;" ><?php echo esc_html__( 'Please contribute to upgrade theme and your site to the best one! Your cooperation would be highly appreciated.', 'wolmart' ); ?></p>
		<p class="info-qt light-info" style="margin-top: 0;margin-bottom: 20px;"><?php esc_html_e( 'We will never collect any sensivite or private data such as IP addresses, email, usernames, or passwords.', 'wolmart' ); ?></p>
	</div>

	<p class="wolmart-admin-panel-actions">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-dark button button-large button-next btn-plugins" data-callback="install_plugins"><?php esc_html_e( 'Continue', 'wolmart' ); ?></a>
		<?php wp_nonce_field( 'wolmart-setup-wizard' ); ?>
	</p>
</form>
