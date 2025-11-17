<?php
defined( 'ABSPATH' ) || die;

$white_label_logo = wolmart_get_option( 'white_label_logo' );
if ( empty( $white_label_logo ) ) {
	$white_label_logo = WOLMART_URI . '/assets/images/logo.png';
}

$active_class = $active_page;
if ( 'setup_wizard' == $active_page && isset( $_REQUEST['step'] ) ) {
	if ( 'demo_content' == $_REQUEST['step'] || 'default_plugins' == $_REQUEST['step'] ) {
		$active_page = $_REQUEST['step'];
	}
}
?>
<div class="wolmart-wrap<?php echo ! $active_class ? '' : ( ' alpha-' . $active_class ); ?>"<?php echo isset( $_GET['noheader'] ) && is_rtl() ? ' style="direction: rtl"' : ''; ?>>
	<div class="wolmart-admin-panel">

	<?php if ( ! ( isset( $_REQUEST['page'] ) && 'wolmart-layout-builder' == $_REQUEST['page'] && isset( $_REQUEST['is_elementor_preview'] ) && $_REQUEST['is_elementor_preview'] ) ) : // Hide if called in elementor preview ?>
		<nav class="wolmart-admin-nav">
			<img class="logo" src="<?php echo esc_url( $white_label_logo ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name', 'display' ) ); ?>" width="144" height="45" />
			<ul class="wolmart-admin-menu">
				<?php
				printf(
					'<li%s><a href="%s">%s</a></li>',
					'license' == $active_page ? ' class="active"' : '',
					esc_url( admin_url( 'admin.php?page=wolmart' ) ),
					esc_html__( 'License', 'wolmart' )
				);

				printf(
					'<li%s><a href="%s">%s</a>',
					( 'setup_wizard' == $active_page || 'optimize_wizard' == $active_page || 'tools' == $active_page ) ? ' class="active"' : '',
					esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard' ) ),
					esc_html__( 'Management', 'wolmart' )
				);
				?>
				<ul class="wolmart-admin-submenu">
					<?php
					if ( Wolmart_Admin::get_instance()->is_registered() ) {
						printf(
							'<li%s><a href="%s"><img src="%s"/>%s<span>%s</span></a></li>',
							'setup_wizard' == $active_page ? ' class="active"' : '',
							esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard' ) ),
							esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_setup_wizard.svg' ),
							esc_html__( 'Setup Wizard', 'wolmart' ),
							esc_html__( 'Setup your site quickly.', 'wolmart' )
						);
						printf(
							'<li%s><a href="%s"><img src="%s"/>%s<span>%s</span></a></li>',
							'optimize_wizard' == $active_page ? ' class="active"' : '',
							esc_url( admin_url( 'admin.php?page=wolmart-optimize-wizard' ) ),
							esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_optimize_wizard.svg' ),
							esc_html__( 'Optimize Wizard', 'wolmart' ),
							esc_html__( 'Enhance your site speed.', 'wolmart' )
						);
					} else {
						printf(
							'<li%s><a href="%s" class="disabled"><img src="%s"/>%s<span>%s</span></a></li>',
							'setup_wizard' == $active_page ? ' class="active"' : '',
							esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard' ) ),
							esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_setup_wizard.svg' ),
							esc_html__( 'Setup Wizard', 'wolmart' ),
							esc_html__( 'Please activate the theme.', 'wolmart' )
						);
						printf(
							'<li%s><a href="%s" class="disabled"><img src="%s"/>%s<span>%s</span></a></li>',
							'optimize_wizard' == $active_page ? ' class="active"' : '',
							esc_url( admin_url( 'admin.php?page=wolmart-optimize-wizard' ) ),
							esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_optimize_wizard.svg' ),
							esc_html__( 'Optimize Wizard', 'wolmart' ),
							esc_html__( 'Please activate the theme.', 'wolmart' )
						);
					}
					printf(
						'<li%s><a href="%s"><img src="%s"/>%s<span>%s</span></a></li>',
						'tools' == $active_page ? ' class="active"' : '',
						esc_url( admin_url( 'admin.php?page=wolmart-tools' ) ),
						esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_tools.svg' ),
						esc_html__( 'Tools', 'wolmart' ),
						esc_html__( 'Keep your site health.', 'wolmart' )
					);
					if ( defined( 'WOLMART_VERSION' ) && wolmart_get_option( 'resource_critical_css' ) ) {
						printf(
							'<li%s><a href="%s"><img src="%s"/>%s<span>%s</span></a></li>',
							'critical' == $active_page ? ' class="active"' : '',
							esc_url( admin_url( 'admin.php?page=wolmart-critical' ) ),
							esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_critical.svg' ),
							esc_html__( 'Critical CSS', 'wolmart' ),
							esc_html__( 'Genereate Critical CSS.', 'wolmart' )
						);
					}
					printf(
						'<li%s><a href="%s"><img src="%s"/>%s<span>%s</span></a></li>',
						'patcher' == $active_page ? ' class="active"' : '',
						esc_url( admin_url( 'admin.php?page=wolmart-patcher' ) ),
						esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_patcher.svg' ),
						esc_html__( 'Patcher', 'wolmart' ),
						esc_html__( 'Keep up-to-date.', 'wolmart' )
					);
					printf(
						'<li%s><a href="%s"><img src="%s"/>%s<span>%s</span></a></li>',
						'version_control' == $active_page ? ' class="active"' : '',
						esc_url( admin_url( 'admin.php?page=wolmart-version-control' ) ),
						esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_rollback.svg' ),
						esc_html__( 'Version Control', 'wolmart' ),
						esc_html__( 'Rollback to a previous version.', 'wolmart' )
					);

					do_action( 'wolmart_add_admin_panel_header', $active_page );
					?>
				</ul>
				<?php
				echo '</li>';

				printf(
					'<li%s><a href="%s">%s</a>',
					'layout_builder' == $active_page || 'templates_builder' == $active_page || 'sidebars_builder' == $active_page ? ' class="active"' : '',
					esc_url( admin_url( 'admin.php?page=wolmart-layout-builder' ) ),
					esc_html__( 'Layouts', 'wolmart' )
				);
				?>
					<ul class="wolmart-admin-submenu">
						<?php
						printf(
							'<li%s><a href="%s"><img src="%s"/>%s<span>%s</span></a></li>',
							'layout_builder' == $active_page ? ' class="active"' : '',
							esc_url( admin_url( 'admin.php?page=wolmart-layout-builder' ) ),
							esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_layout_builder.svg' ),
							esc_html__( 'Layout Builder', 'wolmart' ),
							esc_html__( 'Edit your site layouts.', 'wolmart' )
						);

						if ( class_exists( 'Wolmart_Builders' ) ) {
							printf(
								'<li%s><a href="%s"><img src="%s"/>%s<span>%s</span></a></li>',
								'templates_builder' == $active_page ? ' class="active"' : '',
								esc_url( admin_url( 'edit.php?post_type=wolmart_template' ) ),
								esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_templates.svg' ),
								esc_html__( 'Templates', 'wolmart' ),
								esc_html__( 'Create unique styled site.', 'wolmart' )
							);
							printf(
								'<li%s><a href="%s"><img src="%s"/>%s<span>%s</span></a></li>',
								'sidebars_builder' == $active_page ? ' class="active"' : '',
								esc_url( admin_url( 'admin.php?page=wolmart_sidebar' ) ),
								esc_url( WOLMART_ADMIN_URI . '/panel/assets/menu_sidebars.svg' ),
								esc_html__( 'Sidebars', 'wolmart' ),
								esc_html__( 'Create unlimited sidebars.', 'wolmart' )
							);
						}
						?>
					</ul>
				<?php
				echo '</li>';
				?>
			</ul>
			<?php printf( '<a href="%s" class="button button-large button-light"><i class="dashicons dashicons-admin-customizer"></i><span>%s</span></a>', esc_url( admin_url( 'customize.php' ) ), esc_html__( 'Theme Options', 'wolmart' ) ); ?>
		</nav>
	<?php endif; ?>
