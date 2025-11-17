<?php
defined( 'ABSPATH' ) || die;
?>
<h2><?php esc_html_e( 'Install Plugins', 'wolmart' ); ?></h2>
<form method="post">

	<?php
	$plugins = $this->_get_plugins();
	if ( count( $plugins['all'] ) ) {
		?>
		<p>
			<?php esc_html_e( 'This will install the default plugins which are used in Wolmart.', 'wolmart' ); ?>
			<br>
			<?php esc_html_e( 'Please check the plugins to install:', 'wolmart' ); ?>
		</p>
		<ul class="wolmart-plugins">
			<?php
			$idx          = 0;
			$loadmore     = false;
			$page_builder = get_option( 'wolmart_prefer_page_builder', 'elementor' );
			foreach ( $plugins['all'] as $slug => $plugin ) {
				if ( isset( $plugin['visibility'] ) && 'speed_wizard' == $plugin['visibility'] ) {
					continue;
				}
				if ( ( 'elementor' == $page_builder && 'js_composer' == $slug ) || ( 'js_composer' == $page_builder && 'elementor' == $slug ) ) {
					continue;
				}
				++ $idx;
				?>
				<?php
				if ( $idx > 10 && ! $loadmore ) :
					?>
					<li class="separator">
						<a href="#" class="button-load-plugins" role="button"><b><?php esc_html_e( 'Load more', 'wolmart' ); ?></b> <i class="w-icon-chevron-down"></i></a>
					</li>
					<?php
					$loadmore = true;
				endif;
				?>
				<li data-slug="<?php echo esc_attr( $slug ); ?>"<?php echo 9 < $idx ? ' class="hidden"' : ''; ?>>
					<label class="checkbox checkbox-inline">
						<input type="checkbox" name="setup-plugin"<?php echo ! $plugin['required'] ? '' : ' checked="checked"'; ?>>
						<?php echo esc_html( $plugin['name'] ); ?>
						<span class="info">
						<?php
							$key = '';
						if ( isset( $plugins['install'][ $slug ] ) ) {
							$key = esc_html__( 'Installation', 'wolmart' );
						} elseif ( isset( $plugins['update'][ $slug ] ) ) {
							$key = esc_html__( 'Update', 'wolmart' );
						} elseif ( isset( $plugins['activate'][ $slug ] ) ) {
							$key = esc_html__( 'Activation', 'wolmart' );
						}
						if ( $key ) {
							if ( $plugin['required'] ) {
								/* translators: %s: Plugin name */
								printf( esc_html__( '%s required', 'wolmart' ), $key );
							} else {
								/* translators: %s: Plugin name */
								printf( esc_html__( '%s recommended for certain demos', 'wolmart' ), $key );
							}
						}
						?>
						</span>
					</label>
				</li>
				<?php if ( 'wolmart-core' == $plugin['slug'] ) : ?>
					<li class="separator"></li>
				<?php endif; ?>
			<?php } ?>
		</ul>
		<div class="use-multiple-editors notice-warning notice-alt notice-large" style="display: none;margin-bottom:0">
			<?php /* translators: $1 and $2 opening and closing bold tags respectively */ ?>
			<?php printf( esc_html__( 'Using %1$sElementor%2$s and %1$sVisual Composer%2$s togther affects your site performance.', 'wolmart' ), '<b>', '</b>' ); ?>
		</div>
		<?php
	} else {
		echo '<p class="lead">' . esc_html__( 'Good news! All plugins are already installed and up to date. Please continue.', 'wolmart' ) . '</p>';
	}
	?>

	<p class="wolmart-admin-panel-actions">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button-dark button button-large button-next" data-callback="install_plugins"><?php esc_html_e( 'Continue', 'wolmart' ); ?></a>
		<?php wp_nonce_field( 'wolmart-setup-wizard' ); ?>
	</p>
</form>
