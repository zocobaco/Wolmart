<?php
defined( 'ABSPATH' ) || die;
?>
<div class="wolmart-admin-panel-row">
	<div class="wolmart-setup-demo-header">
		<h2><?php esc_html_e( 'Demo Content Installation', 'wolmart' ); ?></h2>
		<p><?php esc_html_e( 'In this step you can upload your logo and select a demo from the list.', 'wolmart' ); ?></p>
	</div>
	<table class="logo-select">
		<tr>
			<td>
				<label><?php esc_html_e( 'Your Logo:', 'wolmart' ); ?></label>
			</td>
			<td>
				<button id="current-logo" class="button button-upload" aria-label="<?php esc_attr_e( 'Upload Button', 'wolmart' ); ?>">
				<?php
				$image_id  = wolmart_get_option( 'custom_logo' );
				$image_url = '';
				if ( ! empty( $image_id ) ) {
					$image_url = wp_get_attachment_image_url( $image_id, 'full' );
				}

				printf(
					'<img class="site-logo" src="%s" alt="%s" style="max-width:136px; height:auto" />',
					esc_url( $image_url ? $image_url : WOLMART_URI . '/assets/images/logo.png' ),
					get_bloginfo( 'name' )
				);
				?>
				</button>
			</td>
		</tr>
	</table>
</div>
<p style="margin-bottom: 30px;"><a href="#" class="button button-large button-light btn-remove-demo-contents" role="button"><?php esc_html_e( 'Uninstall Demo', 'wolmart' ); ?><i class="w-icon-trash-alt" style="margin-<?php echo is_rtl() ? 'right' : 'left'; ?>: .5rem"></i></a></p>
<div class="wolmart-remove-demo mfp-hide">
	<div class="wolmart-install-demo-header">
		<h2><span class="wolmart-mini-logo"></span><?php esc_html_e( 'Demo Contents Remove', 'wolmart' ); ?></h2>
	</div>
	<div class="wolmart-install-section wolmart-wrap" style="margin: 30px 20px; border: none;">
		<div style="flex: 0 0 40%; max-width: 40%; box-sizing: border-box;">
			<label><input type="checkbox" value="" checked="checked"/> <?php esc_html_e( 'All', 'wolmart' ); ?></label>
			<label><input type="checkbox" value="page" checked="checked"/> <?php esc_html_e( 'Pages', 'wolmart' ); ?></label>
			<label><input type="checkbox" value="post" checked="checked"/> <?php esc_html_e( 'Posts', 'wolmart' ); ?></label>
			<label><input type="checkbox" value="attachment" checked="checked"/> <?php esc_html_e( 'Attachments', 'wolmart' ); ?></label>
			<label><input type="checkbox" value="product" checked="checked"/> <?php esc_html_e( 'Products', 'wolmart' ); ?></label>
			<label><input type="checkbox" value="wolmart_template" checked="checked"/> <?php esc_html_e( 'Builders', 'wolmart' ); ?></label>
			<label><input type="checkbox" value="widgets" checked="checked"/> <?php esc_html_e( 'Widgets', 'wolmart' ); ?></label>
			<label><input type="checkbox" value="options" checked="checked"/> <?php esc_html_e( 'Theme Options', 'wolmart' ); ?></label>
		</div>
		<div style="flex: 0 0 60%; max-width: 60%;  box-sizing: border-box;">
			<div class="notice-warning notice-alt" style="padding: 1rem; margin-bottom: 1.5rem; border-radius: 3px;"><?php esc_html_e( 'Please backup your site before uninstalling. All imported and overridden contents from Wolmart demos would be removed.', 'wolmart' ); ?></div>
			<div class="remove-status" style="width: 100%"></div>
			<button class="button button-primary button-large" <?php disabled( empty( get_option( 'wolmart_demo_history', array() ) ) ); ?> style="width: 100%"><i class="w-icon-trash-alt" style="margin-right: .5rem"></i><?php esc_html_e( 'Uninstall', 'wolmart' ); ?></button>
		</div>
	</div>
</div>

<h3 style="margin-bottom: 0;"><?php esc_html_e( 'Select Demo', 'wolmart' ); ?></h3>
<form method="post" class="wolmart-install-demos">
	<input type="hidden" id="current_site_link" value="<?php echo esc_url( home_url() ); ?>">
	<?php
	$demos               = $this->wolmart_demo_types();
	$memory_limit        = wp_convert_hr_to_bytes( @ini_get( 'memory_limit' ) );
	$wolmart_plugins_obj = new Wolmart_TGM_Plugins();
	$required_plugins    = $wolmart_plugins_obj->get_plugins_list();
	$uninstalled_plugins = array();
	$all_plugins         = array();
	foreach ( $required_plugins as $plugin ) {
		if ( is_plugin_inactive( $plugin['url'] ) ) {
			$uninstalled_plugins[ $plugin['slug'] ] = $plugin;
		}
		$all_plugins[ $plugin['slug'] ] = $plugin;
	}
	$time_limit    = ini_get( 'max_execution_time' );
	$server_status = $memory_limit >= 268435456 && ( $time_limit >= 600 || 0 == $time_limit );
	?>

	<div class="wolmart-install-demo mfp-hide">
		<div class="wolmart-install-demo-header">
			<h2><span class="wolmart-mini-logo"></span><?php esc_html_e( 'Demo Import', 'wolmart' ); ?></h2>
		</div>
		<div class="wolmart-install-demo-row">
			<div class="theme">
				<div class="theme-wrapper">
					<a class="theme-link" href="#" target="_blank" aria-label="<?php esc_attr_e( 'Demo Install', 'wolmart' ); ?>">
						<img class="theme-screenshot" src="#">
					</a>
				</div>
			</div>
			<div class="theme-import-panel">
				<div id="import-status">
					<div class="wolmart-installing-options">
						<div class="wolmart-import-options"><span class="wolmart-loading"></span><?php esc_html_e( 'Import theme options', 'wolmart' ); ?></div>
						<div class="wolmart-reset-menus"><span class="wolmart-loading"></span><?php esc_html_e( 'Reset menus', 'wolmart' ); ?></div>
						<div class="wolmart-reset-widgets"><span class="wolmart-loading"></span><?php esc_html_e( 'Reset widgets', 'wolmart' ); ?></div>
						<div class="wolmart-import-dummy"><span class="wolmart-loading"></span><?php esc_html_e( 'Import dummy content', 'wolmart' ); ?> <span></span></div>
						<div class="wolmart-import-widgets"><span class="wolmart-loading"></span><?php esc_html_e( 'Import widgets', 'wolmart' ); ?></div>
						<div class="wolmart-import-subpages"><span class="wolmart-loading"></span><?php esc_html_e( 'Import subpages', 'wolmart' ); ?></div>
					</div>
					<p class="import-result"></p>
				</div>
				<div id="wolmart-install-options" class="wolmart-install-options">
					<?php if ( Wolmart_Admin::get_instance()->is_registered() ) : ?>
						<div class="wolmart-install-editors">
							<label for="wolmart-elementor-demo" class="d-none">
								<input type="radio" id="wolmart-elementor-demo" name="wolmart-import-editor" value="elementor" <?php checked( 'elementor' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ); ?>>
								<img src="<?php echo esc_url( WOLMART_URI . '/assets/images/admin/builder_elementor.png' ); ?>" alt="<?php esc_attr_e( 'Elementor', 'wolmart' ); ?>" title="<?php esc_attr_e( 'Elementor', 'wolmart' ); ?>">
							</label>
							<label for="wolmart-js_composer-demo" class="d-none">
								<input type="radio" id="wolmart-js_composer-demo" name="wolmart-import-editor" value="js_composer" <?php checked( 'js_composer' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ); ?>>
								<img src="<?php echo esc_url( WOLMART_URI . '/assets/images/admin/builder_wpbakery.png' ); ?>" alt="<?php esc_attr_e( 'WPBakery Page Builder', 'wolmart' ); ?>" title="<?php esc_attr_e( 'WPBakery Page Builder', 'wolmart' ); ?>">
							</label>
						</div>
						<div class="wolmart-install-section">
							<div class="wolmart-install-options-section">
								<h3><?php esc_html_e( 'Select Content to Import', 'wolmart' ); ?></h3>
								<label for="wolmart-import-options"><input type="checkbox" id="wolmart-import-options" value="1" checked="checked"/> <?php esc_html_e( 'Import theme options', 'wolmart' ); ?></label>
								<input type="hidden" id="wolmart-install-demo-type" value="landing"/>
								<label for="wolmart-reset-menus"><input type="checkbox" id="wolmart-reset-menus" value="1" checked="checked"/> <?php esc_html_e( 'Reset menus', 'wolmart' ); ?></label>
								<label for="wolmart-reset-widgets"><input type="checkbox" id="wolmart-reset-widgets" value="1" checked="checked"/> <?php esc_html_e( 'Reset widgets', 'wolmart' ); ?></label>
								<label for="wolmart-import-dummy"><input type="checkbox" id="wolmart-import-dummy" value="1" checked="checked"/> <?php esc_html_e( 'Import dummy content', 'wolmart' ); ?></label>
								<label for="wolmart-import-widgets"><input type="checkbox" id="wolmart-import-widgets" value="1" checked="checked"/> <?php esc_html_e( 'Import widgets', 'wolmart' ); ?></label>
								<label for="wolmart-override-contents"><input type="checkbox" id="wolmart-override-contents" value="1" checked="checked" /> <?php esc_html_e( 'Override existing contents', 'wolmart' ); ?></label>
							<label for="wolmart-import-subpages"><input type="checkbox" id="wolmart-import-subpages" value="1" checked="checked" /> <?php esc_html_e( 'Import subpages', 'wolmart' ); ?></label>
							</div>
							<div>
								<p style="margin-top: 0;"><?php esc_html_e( 'Do you want to install demo? It can also take a minute to complete.', 'wolmart' ); ?></p>
								<button class="btn <?php echo ! $server_status ? 'btn-quaternary' : 'btn-primary'; ?> wolmart-import-yes"<?php echo ! $server_status ? ' disabled="disabled"' : ''; ?>><?php esc_html_e( 'Standard Import', 'wolmart' ); ?></button>
								<?php if ( ! $server_status ) : ?>
									<p><?php esc_html_e( 'Your server performance does not satisfy Wolmart demo importer engine\'s requirement. We recommend you to use alternative method to perform demo import without any issues but it may take much time than standard import.', 'wolmart' ); ?></p>
								<?php else : ?>
									<p><?php esc_html_e( 'If you have any issues with standard import, please use Alternative mode. But it may take much time than standard import.', 'wolmart' ); ?></p>
								<?php endif; ?>
								<button class="btn btn-secondary wolmart-import-yes alternative"><?php esc_html_e( 'Alternative Mode', 'wolmart' ); ?></button>
							</div>
						</div>
					<?php else : ?>
						<a href="<?php echo esc_url( admin_url( 'admin.php?page=wolmart' ) ); ?>" class="btn btn-dark btn-activate" style="display: inline-block; box-sizing: border-box; text-decoration: none; text-align: center; margin-bottom: 20px;"><?php esc_html_e( 'Activate Theme', 'wolmart' ); ?></a>
					<?php endif; ?>
				</div>
			</div>
		</div>
	</div>
	<div id="theme-install-demos">
		<?php foreach ( $demos as $demo => $demo_details ) : ?>
			<?php
			$uninstalled_demo_plugins = $uninstalled_plugins;
			if ( ! empty( $demo_details['plugins'] ) ) {
				foreach ( $demo_details['plugins'] as $plugin ) {
					if ( is_plugin_inactive( $all_plugins[ $plugin ]['url'] ) ) {
						$uninstalled_demo_plugins[ $plugin ] = $all_plugins[ $plugin ];
					}
				}
			}

			if ( 'landing' == $demo ) {
			} else {
				$demo_url = $this->wolmart_url . $demo;
			}
			?>
			<div class="theme <?php echo esc_attr( $demo_details['filter'] ); ?>">
				<div class="theme-wrapper">
					<img class="theme-screenshot" src="<?php echo esc_url( $demo_details['img'] ); ?>" />
					<h3 class="theme-name" id="<?php echo esc_attr( $demo ); ?>" data-live-url="<?php echo esc_url( $demo_url ); ?>">
						<?php
						echo wolmart_escaped( $demo_details['alt'] );

						echo '<span class="theme-editors">';
						foreach ( $demo_details['editors'] as $editor ) {
							echo '<img src="' . esc_url( $all_plugins[ $editor ]['image_url'] ) . '" height="15" />';
						}
						echo '</span>';
						?>
					</h3>
					<a class="demo-button demo-preview w-icon-up-right-from-square" href="<?php echo esc_url( $demo_url ); ?>" target="_blank" title="<?php esc_attr_e( 'Preview', 'wolmart' ); ?>"><?php esc_html_e( 'Preview', 'wolmart' ); ?></a>
					<a class="demo-button demo-import w-icon-download3" href="#" title="<?php esc_attr_e( 'Import', 'wolmart' ); ?>" role="button"><?php esc_html_e( 'Import', 'wolmart' ); ?></a>
					<?php
					if ( isset( $uninstalled_demo_plugins[ $demo_details['editors'][0] ] ) ) {
						if ( ! isset( $demo_details['editors'][1] ) ) {
							echo '<div class="wolmart-install-notice">' . sprintf( esc_html__( 'Please, select Elementor page builder and install in %1$sPage Builder step%2$s.', 'wolmart' ), '<strong><a href="' . esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=page_builder' ) ) . '">', '</a></strong>' ) . '</div>';
						} elseif ( isset( $uninstalled_demo_plugins[ $demo_details['editors'][1] ] ) ) {
							echo '<div class="wolmart-install-notice">' . sprintf( esc_html__( 'Please, activate one of the page builders from Elementor or WPBakery page builder in %1$sPage Builder step%2$s.', 'wolmart' ), '<strong><a href="' . esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=page_builder' ) ) . '">', '</a></strong>' ) . '</div>';
						} elseif ( 'elementor' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ) {
							echo '<div class="wolmart-install-notice">' . sprintf( esc_html__( 'Please, select WPBakery page builder and install in %1$sPage Builder step%2$s.', 'wolmart' ), '<strong><a href="' . esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=page_builder' ) ) . '">', '</a></strong>' ) . '</div>';
						}
					} else {
						if ( ! isset( $uninstalled_demo_plugins['js_composer'] ) ) {
							echo '<div class="wolmart-install-notice">' . sprintf( esc_html__( 'Please, deactivate one of the builders and leave only ONE plugin either Elementor or WPBakery page builder in %1$sPage Builder step%2$s.', 'wolmart' ), '<strong><a href="' . esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=page_builder' ) ) . '">', '</a></strong>' ) . '</div>';
						} elseif ( 'js_composer' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ) {
							echo '<div class="wolmart-install-notice">' . sprintf( esc_html__( 'Please, select Elementor page builder and install in %1$sPage Builder step%2$s.', 'wolmart' ), '<strong><a href="' . esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=page_builder' ) ) . '">', '</a></strong>' ) . '</div>';
						}
					}
					?>
					<ul class="plugins-used" data-editor="<?php echo esc_attr( json_encode( $demo_details['editors'] ) ); ?>">
					<?php if ( ! empty( $uninstalled_demo_plugins ) ) : ?>
							<?php foreach ( $uninstalled_demo_plugins as $plugin ) : ?>
								<?php
								if ( 'elementor' == $plugin['slug'] && 'js_composer' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ) {
									continue;
								}
								if ( 'js_composer' == $plugin['slug'] && 'elementor' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ) {
									continue;
								}
								?>
								<?php if ( $plugin['required'] || ( isset( $demo_details['plugins'] ) && in_array( $plugin['slug'], $demo_details['plugins'] ) ) ) : ?>
								<li data-plugin="<?php echo esc_attr( $plugin['slug'] ); ?>">
									<div class="thumb">
										<img src="<?php echo esc_url( $plugin['image_url'] ); ?>" />
									</div>
									<div>
										<h5><?php echo esc_html( $plugin['name'] ); ?></h5>
										<a href="#" data-slug="<?php echo esc_attr( $plugin['slug'] ); ?>" data-callback="install_plugin" class="demo-plugin" role="button"><?php esc_html_e( 'Install', 'wolmart' ); ?></a>
									</div>
								</li>
							<?php endif; ?>
							<?php endforeach; ?>
						<?php endif; ?>
					<?php if ( ! empty( $demo_details['editors'] ) ) : ?>
						<?php foreach ( $demo_details['editors'] as $editor ) : ?>
							<?php
							if ( 'elementor' == $editor && 'js_composer' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ) {
								continue;
							}
							if ( 'js_composer' == $editor && 'elementor' == get_option( 'wolmart_prefer_page_builder', 'elementor' ) ) {
								continue;
							}
							?>
							<?php if ( is_plugin_inactive( $all_plugins[ $editor ]['url'] ) ) : ?>
								<li data-plugin="<?php echo esc_attr( $all_plugins[ $editor ]['slug'] ); ?>" class="plugin-editor">
									<div class="thumb">
										<img src="<?php echo esc_url( $all_plugins[ $editor ]['image_url'] ); ?>" />
									</div>
									<div>
										<h5><?php echo esc_html( $all_plugins[ $editor ]['name'] ); ?></h5>
										<a href="#" data-slug="<?php echo esc_attr( $all_plugins[ $editor ]['slug'] ); ?>" data-callback="install_plugin" class="demo-plugin" role="button"><?php esc_html_e( 'Install', 'wolmart' ); ?></a>
									</div>
								</li>
							<?php endif; ?>
						<?php endforeach; ?>
					<?php endif; ?>
						</ul>
				</div>
			</div>
		<?php endforeach; ?>
	</div>
	<p class="info-qt light-info icon-fixed"><?php esc_html_e( 'Installing a demo provides pages, posts, menus, images, theme options, widgets and more.', 'wolmart' ); ?>
	<br /><strong><?php esc_html_e( 'IMPORTANT: ', 'wolmart' ); ?> </strong><span><?php esc_html_e( 'The included plugins need to be installed and activated before you install a demo.', 'wolmart' ); ?></span>
	<?php /* translators: $1: opening A tag which has link to the plugins step $2: closing A tag */ ?>
	<br /><?php printf( esc_html__( 'Please check the %1$sStatus%2$s step to ensure your server meets all requirements for a successful import. Settings that need attention will be listed in red.', 'wolmart' ), '<a href="' . esc_url( $this->get_step_link( 'status' ) ) . '">', '</a>' ); ?></p>

	<input type="hidden" name="new_logo_id" id="new_logo_id" value="">

	<p class="wolmart-admin-panel-actions">
		<a href="<?php echo esc_url( $this->get_next_step_link() ); ?>" class="button button-large button-light button-next button-icon-hide"><?php esc_html_e( 'Skip this step', 'wolmart' ); ?></a>
		<button type="submit" class="button-dark button button-large button-next" name="save_step"><?php esc_html_e( 'Continue', 'wolmart' ); ?></button>
		<?php wp_nonce_field( 'wolmart-setup-wizard' ); ?>
	</p>
</form>
