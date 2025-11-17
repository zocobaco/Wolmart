<?php
/**
 * Wolmart Admin
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

class Wolmart_Admin extends Wolmart_Base {

	private $checked_purchase_code;

	private $activation_url = 'https://dythemes.com/wordpress/dummy/api/includes/verify_purchase.php';

	public function __construct() {
		if ( is_admin_bar_showing() ) {
			add_action( 'wp_before_admin_bar_render', array( $this, 'add_wp_toolbar_menu' ) );
		}

		add_action( 'admin_menu', array( $this, 'custom_admin_menu_order' ) );
		add_action( 'after_switch_theme', array( $this, 'after_switch_theme' ) );
		add_action( 'after_switch_theme', array( $this, 'reset_child_theme_options' ), 15 );

		if ( is_child_theme() && empty( wolmart_get_option( 'container' ) ) ) {
			$parent_theme_options = get_option( 'theme_mods_wolmart' );
			update_option( 'theme_mods_' . get_option( 'stylesheet' ), $parent_theme_options );
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'add_theme_update_url' ), 1001 );

		add_action( 'admin_init', array( $this, 'check_activation' ) );
		add_action( 'admin_init', array( $this, 'show_activation_notice' ) );

		if ( is_admin() ) {
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'pre_set_site_transient_update_themes' ) );
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'pre_set_site_transient_update_plugins' ), 5 );
			add_filter( 'upgrader_pre_download', array( $this, 'upgrader_pre_download' ), 10, 3 );

			if ( defined( 'WPB_VC_VERSION' ) ) {
				add_action( 'init', array( $this, 'disable_vc_updater' ), 20 );
			}
		}
	}

	public function add_wp_toolbar_menu() {

		$target = is_admin() ? '_self' : '_blank';

		if ( current_user_can( 'edit_theme_options' ) ) {

			$title = esc_html( wolmart_get_option( 'white_label_title' ) );
			$icon  = esc_html( wolmart_get_option( 'white_label_icon' ) );
			$this->add_wp_toolbar_menu_item(
				'<span class="ab-icon dashicons ' . ( ! $icon ? 'dashicons-wolmart-logo">' : 'custom-mini-logo"><img src="' . $icon . '" alt="logo" width="20" height="20" />' ) . '</span><span class="ab-label">' . ( $title ? $title : 'Wolmart' ) . '</span>',
				false,
				esc_url( admin_url( 'admin.php?page=wolmart' ) ),
				array(
					'class'  => 'wolmart-menu',
					'target' => $target,
				),
				'wolmart'
			);

			// License

			$this->add_wp_toolbar_menu_item(
				esc_html__( 'License', 'wolmart' ),
				'wolmart',
				esc_url( admin_url( 'admin.php?page=wolmart' ) ),
				array(
					'target' => $target,
				)
			);

			// Theme Options

			$this->add_wp_toolbar_menu_item(
				esc_html__( 'Theme Options', 'wolmart' ),
				'wolmart',
				esc_url( admin_url( 'customize.php' ) ),
				array(
					'target' => $target,
				)
			);

			// Management Submenu

			$this->add_wp_toolbar_menu_item(
				esc_html__( 'Management', 'wolmart' ),
				'wolmart',
				esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard' ) ),
				array(
					'target' => $target,
				),
				'wolmart_management'
			);

			if ( class_exists( 'Wolmart_Setup_Wizard' ) ) {
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Setup Wizard', 'wolmart' ),
					'wolmart_management',
					esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard' ) ),
					array(
						'target' => $target,
					),
					'wolmart_setup'
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Status', 'wolmart' ),
					'wolmart_setup',
					esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard' ) ),
					array(
						'target' => $target,
					)
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Child Theme', 'wolmart' ),
					'wolmart_setup',
					esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=customize' ) ),
					array(
						'target' => $target,
					)
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Plugins', 'wolmart' ),
					'wolmart_setup',
					esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=default_plugins' ) ),
					array(
						'target' => $target,
					),
					'wolmart_setup_plugins'
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Demo', 'wolmart' ),
					'wolmart_setup',
					esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=demo_content' ) ),
					array(
						'target' => $target,
					)
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Ready', 'wolmart' ),
					'wolmart_setup',
					esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=ready' ) ),
					array(
						'target' => $target,
					),
					'wolmart_setup_ready'
				);
			}
			if ( class_exists( 'Wolmart_Optimize_Wizard' ) ) {
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Optimize Wizard', 'wolmart' ),
					'wolmart_management',
					esc_url( admin_url( 'admin.php?page=wolmart-optimize-wizard' ) ),
					array(
						'target' => $target,
					),
					'wolmart_optimize'
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Resources', 'wolmart' ),
					'wolmart_optimize',
					esc_url( admin_url( 'admin.php?page=wolmart-optimize-wizard' ) ),
					array(
						'target' => $target,
					)
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Lazyload', 'wolmart' ),
					'wolmart_optimize',
					esc_url( admin_url( 'admin.php?page=wolmart-optimize-wizard&step=lazyload' ) ),
					array(
						'target' => $target,
					)
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Performance', 'wolmart' ),
					'wolmart_optimize',
					esc_url( admin_url( 'admin.php?page=wolmart-optimize-wizard&step=performance' ) ),
					array(
						'target' => $target,
					)
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Plugins', 'wolmart' ),
					'wolmart_optimize',
					esc_url( admin_url( 'admin.php?page=wolmart-optimize-wizard&step=plugins' ) ),
					array(
						'target' => $target,
					)
				);
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Ready', 'wolmart' ),
					'wolmart_optimize',
					esc_url( admin_url( 'admin.php?page=wolmart-optimize-wizard&step=ready' ) ),
					array(
						'target' => $target,
					)
				);
			}
			if ( class_exists( 'Wolmart_Tools' ) ) {
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Tools', 'wolmart' ),
					'wolmart_management',
					esc_url( admin_url( 'admin.php?page=wolmart-tools' ) ),
					array(
						'target' => $target,
					)
				);
			}

			// Layouts Submenu

			$this->add_wp_toolbar_menu_item(
				esc_html__( 'Layouts', 'wolmart' ),
				'wolmart',
				esc_url( admin_url( 'admin.php?page=wolmart-layout-builder' ) ),
				array(
					'target' => $target,
				),
				'wolmart_layouts'
			);

			$this->add_wp_toolbar_menu_item(
				esc_html__( 'Layout Builder', 'wolmart' ),
				'wolmart_layouts',
				esc_url( admin_url( 'admin.php?page=wolmart-layout-builder' ) ),
				array(
					'target' => $target,
				)
			);

			if ( class_exists( 'Wolmart_Builders' ) ) {
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'All Templates', 'wolmart' ),
					'wolmart_layouts',
					esc_url( admin_url( 'edit.php?post_type=wolmart_template' ) ),
					array(
						'target' => $target,
					)
				);
			}
			if ( class_exists( 'Wolmart_Sidebar_Builder' ) ) {
				$this->add_wp_toolbar_menu_item(
					esc_html__( 'Sidebars', 'wolmart' ),
					'wolmart_layouts',
					esc_url( admin_url( 'admin.php?page=wolmart_sidebar' ) ),
					array(
						'target' => $target,
					)
				);
			}

			if ( class_exists( 'Wolmart_Builders' ) ) {

				global $wolmart_layout;

				if ( ! empty( $wolmart_layout['used_blocks'] ) && count( $wolmart_layout['used_blocks'] ) ) {

					$used_templates = $wolmart_layout['used_blocks'];

					foreach ( $used_templates as $template_id => $data ) {

						$template_type = get_post_meta( $template_id, 'wolmart_template_type', true );
						if ( ! $template_type ) {
							$template_type = 'block';
						}

						$template = get_post( $template_id );

						if ( defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $template_id, '_elementor_edit_mode', true ) ) {
							$edit_link = admin_url( 'post.php?post=' . $template_id . '&action=elementor' );
						} else {
							$edit_link = admin_url( 'post.php?post=' . $template_id . '&action=edit' );
						}

						if ( $template ) {
							$this->add_wp_toolbar_menu_item(
								// translators: %s represents template title.
								'<span class="wolmart-ab-template-title">' . sprintf( esc_html__( 'Edit %s', 'wolmart' ), $template->post_title ) . '</span><span class="wolmart-ab-template-type">' . str_replace( '_', ' ', $template_type ) . '</span>',
								'edit',
								esc_url( $edit_link ),
								array(
									'target' => $target,
								),
								'edit_wolmart_template_' . $template_id
							);
						}
					}
				}
			}

			// Activate Theme

			if ( ! $this->is_registered() ) {
				$this->add_wp_toolbar_menu_item(
					'<span class="ab-icon dashicons dashicons-admin-network"></span><span class="ab-label">' . esc_html__( 'Activate Theme', 'wolmart' ) . '</span>',
					false,
					esc_url( admin_url( 'admin.php?page=wolmart' ) ),
					array(
						'class'  => 'wolmart-menu',
						'target' => $target,
					),
					'wolmart-activate'
				);
			}

			do_action( 'wolmart_add_wp_toolbar_menu', $this );
		}
	}

	public function add_wp_toolbar_menu_item( $title, $parent = false, $href = '', $custom_meta = array(), $custom_id = '' ) {
		global $wp_admin_bar;
		if ( current_user_can( 'edit_theme_options' ) ) {
			if ( ! is_super_admin() || ! is_admin_bar_showing() ) {
				return;
			}
			// Set custom ID
			if ( $custom_id ) {
				$id = $custom_id;
			} else { // Generate ID based on $title
				$id = strtolower( str_replace( ' ', '-', $title ) );
			}
			// links from the current host will open in the current window
			$meta = strpos( $href, home_url() ) !== false ? array() : array( 'target' => '_blank' ); // external links open in new $targetw

			$meta = array_merge( $meta, $custom_meta );
			$wp_admin_bar->add_node(
				array(
					'parent' => $parent,
					'id'     => $id,
					'title'  => $title,
					'href'   => $href,
					'meta'   => $meta,
				)
			);
		}
	}

	public function custom_admin_menu_order() {
		global $menu;

		$admin_menus = array();

		// Change dasbhoard menu order.
		$posts = array();
		$idx   = 0;
		foreach ( $menu as $key => $menu_item ) {
			if ( isset( $menu_item[2] ) && 'alpus-addons' == $menu_item[2] ) {
			} elseif ( 'Posts' == $menu_item[0] ) {
				$admin_menus[9] = $menu_item;
			} elseif ( 'separator1' == $menu_item[2] ) {
				$admin_menus[8] = $menu_item;
			} else {
				$admin_menus[ $key ] = $menu_item;
			}
		}

		$menu = $admin_menus;
	}

	public function check_purchase_code() {

		if ( ! $this->checked_purchase_code ) {
			$code         = isset( $_POST['code'] ) ? sanitize_text_field( $_POST['code'] ) : '';
			$code_confirm = $this->get_purchase_code();

			if ( isset( $_POST['action'] ) && ! empty( $_POST['action'] ) ) {
				preg_match( '/[a-z0-9\-]{1,63}\.[a-z\.]{2,6}$/', parse_url( home_url(), PHP_URL_HOST ), $_domain_tld );
				if ( isset( $_domain_tld[0] ) ) {
					$domain = $_domain_tld[0];
				} else {
					$domain = parse_url( home_url(), PHP_URL_HOST );
				}
				if ( ! $code || $code != $code_confirm ) {
					if ( $code_confirm ) {
						$result = $this->curl_purchase_code( $code_confirm, '', 'remove' );
					}
					if ( 'unregister' == $_POST['action'] && isset( $result ) && isset( $result['result'] ) && 3 == (int) $result['result'] ) {
						$this->checked_purchase_code = 'unregister';
						$this->set_purchase_code( '' );
						return $this->checked_purchase_code;
					}
				}
				if ( $code ) {
					$result = $this->curl_purchase_code( $code, $domain, 'add' );
					if ( ! $result ) {
						$this->checked_purchase_code = 'invalid';
						$code_confirm                = '';
					} elseif ( isset( $result['result'] ) && 1 == (int) $result['result'] ) {
						$code_confirm                = $code;
						$this->checked_purchase_code = 'verified';
					} else {
						$this->checked_purchase_code = $this->get_api_message( $result['message'] );
						$code_confirm                = '';
					}
				} else {
					$code_confirm                = '';
					$this->checked_purchase_code = '';
				}
				$this->set_purchase_code( $code_confirm );
			} else {
				if ( $code && $code_confirm && $code == $code_confirm ) {
					$this->checked_purchase_code = 'verified';
				}
			}
		}
		return $this->checked_purchase_code;
	}

	public function get_api_message( $msg_code ) {
		if ( 'blocked_spam' == $msg_code ) {
			return esc_html__( 'Your ip address is blocked as spam!!!', 'wolmart' );
		} elseif ( 'code_invalid' == $msg_code ) {
			return esc_html__( 'Purchase Code is not valid!!!', 'wolmart' );
		} elseif ( 'already_used' == $msg_code && ! empty( $data['domain'] ) ) {
			return vsprintf( esc_html__( 'This code was already used in %s', 'wolmart' ), $data['domain'] );
		} elseif ( 'reactivate' == $msg_code ) {
			return esc_html__( 'Please re-activate the theme.', 'wolmart' );
		} elseif ( 'unregistered' == $msg_code ) {
			return esc_html__( 'Wolmart Theme is unregistered!', 'wolmart' );
		} elseif ( 'activated' == $msg_code ) {
			return esc_html__( 'Wolmart Theme is activated!', 'wolmart' );
		}
		return '';
	}

	public function curl_purchase_code( $code, $domain, $act ) {

		require_once WOLMART_ADMIN . '/importer/importer-api.php';
		$importer_api = new Wolmart_Importer_API();

		$result = $importer_api->get_response(
			$this->activation_url,
			array(
				'body' => array(
					'item'     => 32947681,
					'code'     => $code,
					'domain'   => $domain,
					'siteurl'  => urlencode( home_url() ),
					'act'      => $act,
					'local'    => ( $importer_api->is_localhost() ? true : '' ),
					'template' => get_template(),
				),
			)
		);

		if ( ! $result || is_wp_error( $result ) ) {
			return false;
		}
		return $result;
	}

	public function get_purchase_code() {
		if ( $this->is_envato_hosted() ) {
			return SUBSCRIPTION_CODE;
		}
		return get_option( 'envato_purchase_code_32947681' );
	}

	public function is_registered() {
		if ( $this->is_envato_hosted() ) {
			return true;
		}
		return get_option( 'wolmart_registered' );
	}

	public function set_purchase_code( $code ) {
		update_option( 'envato_purchase_code_32947681', $code );
	}

	public function is_envato_hosted() {
		return defined( 'ENVATO_HOSTED_KEY' ) ? true : false;
	}

	public function get_ish() {
		if ( ! defined( 'ENVATO_HOSTED_KEY' ) ) {
			return false;
		}
		return substr( ENVATO_HOSTED_KEY, 0, 16 );
	}

	function get_purchase_code_asterisk() {
		$code = $this->get_purchase_code();
		if ( $code ) {
			$code = substr( $code, 0, 13 );
			$code = $code . '-****-****-************';
		}
		return $code;
	}

	public function pre_set_site_transient_update_themes( $transient ) {
		if ( ! $this->is_registered() ) {
			return $transient;
		}

		require_once WOLMART_ADMIN . '/importer/importer-api.php';
		$importer_api   = new Wolmart_Importer_API();
		$new_version    = $importer_api->get_latest_theme_version();
		$theme_template = get_template();
		if ( version_compare( wp_get_theme( $theme_template )->get( 'Version' ), $new_version, '<' ) ) {

			$args = $importer_api->generate_args( false );
			if ( $this->is_envato_hosted() ) {
				$args['ish'] = $this->get_ish();
			}

			$transient->response[ $theme_template ] = array(
				'theme'       => $theme_template,
				'new_version' => $new_version,
				'url'         => $importer_api->get_url( 'changelog' ),
				'package'     => add_query_arg( $args, $importer_api->get_url( 'theme' ) ),
			);

		}
		return $transient;
	}

	/**
	 * Update plugins from Wolmart repository
	 *
	 * @since 1.8.7
	 */
	public function pre_set_site_transient_update_plugins( $transient ) {
		if ( ! $this->is_registered() || empty( $transient->checked ) ) {
			return $transient;
		}

		$plugins = get_site_transient( 'wolmart_plugins' );
		if ( is_array( $plugins ) && ! empty( $transient->response ) && function_exists( 'get_plugin_data' ) ) {
			$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
			foreach ( $plugins as $plugin ) {
				if ( $instance && isset( $plugin['version'], $plugin['url'], $plugin['source'] ) && $instance->is_plugin_installed( $plugin['slug'] ) ) {
					$plugin_info = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin['url'] );
					if ( $plugin_info && isset( $plugin_info['Version'] ) && version_compare( $plugin_info['Version'], $plugin['version'], '<' ) ) {
						$obj                                   = new stdClass();
						$obj->slug                             = $plugin['slug'];
						$obj->new_version                      = $plugin['version'];
						$obj->plugin                           = $plugin['url'];
						$obj->url                              = '';
						$obj->package                          = $plugin['source'];
						$obj->name                             = $plugin['name'];
						$transient->response[ $plugin['url'] ] = $obj;
					}
				}
			}
		}

		return $transient;
	}

	/**
	 * Disable WPBakery auto updater
	 *
	 * @since 1.8.7
	 */
	public function disable_vc_updater() {
		if ( has_filter( 'upgrader_pre_download', array( vc_updater(), 'preUpgradeFilter' ), 10, 4 ) ) {
			remove_filter(
				'upgrader_pre_download',
				array(
					vc_updater(),
					'preUpgradeFilter',
				),
				10,
				4
			);
		}
	}

	public function upgrader_pre_download( $reply, $package, $obj ) {

		require_once WOLMART_ADMIN . '/importer/importer-api.php';
		$importer_api = new Wolmart_Importer_API();
		if ( strpos( $package, $importer_api->get_url( 'theme' ) ) !== false || strpos( $package, $importer_api->get_url( 'plugins' ) ) !== false ) {
			if ( ! $this->is_registered() ) {
				return new WP_Error( 'not_registerd', sprintf( esc_html__( 'Please %s Wolmart theme to get access to pre-built demo websites and auto updates.', 'wolmart' ), '<a href="admin.php?page=wolmart">' . esc_html__( 'register', 'wolmart' ) . '</a>' ) );
			}
			$code   = $this->get_purchase_code();
			$domain = $importer_api->generate_args();
			$domain = $domain['domain'];
			$result = $this->curl_purchase_code( $code, $domain, 'add' );
			if ( ! isset( $result['result'] ) || 1 !== (int) $result['result'] ) {
				$message = isset( $result['message'] ) ? $result['message'] : esc_html__( 'Purchase Code is not valid or could not connect to the API server!', 'wolmart' );
				return new WP_Error( 'purchase_code_invalid', esc_html( $message ) );
			}
		}
		return $reply;
	}

	public function add_theme_update_url() {
		global $pagenow;
		if ( 'update-core.php' == $pagenow ) {

			require_once WOLMART_ADMIN . '/importer/importer-api.php';
			$importer_api   = new Wolmart_Importer_API();
			$new_version    = $importer_api->get_latest_theme_version();
			$theme_template = get_template();

			$url = $importer_api->get_url( 'changelog' );
			if ( version_compare( WOLMART_VERSION, $new_version, '<' ) ) {
				$checkbox_id = md5( wp_get_theme( $theme_template )->get( 'Name' ) );
				wp_add_inline_script( 'wolmart-admin', 'if (jQuery(\'#checkbox_' . $checkbox_id . '\').length) {jQuery(\'#checkbox_' . $checkbox_id . '\').closest(\'tr\').children().last().append(\'<a href="' . esc_url( $url ) . '" target="_blank">' . esc_js( __( 'View Details', 'wolmart' ) ) . '</a>\');}' );
			}

			$checkbox_id = md5( 'wolmart-core/wolmart-core.php' );
			wp_add_inline_script( 'wolmart-admin', 'if (jQuery(\'#checkbox_' . $checkbox_id . '\').length) {jQuery(\'#checkbox_' . $checkbox_id . '\').closest(\'tr\').find(".open-plugin-details-modal").replaceWith(\'<a href="' . esc_url( $url ) . '" target="_blank" rel="noopener noreferrer">' . esc_js( __( 'View Details', 'wolmart' ) ) . '</a>\');}' );
		}
	}

	public function after_switch_theme() {
		if ( $this->is_registered() ) {
			$this->refresh_transients();
		}
	}

	/**
	 * Reset child theme's options.
	 *
	 * @since 1.0
	 */
	public function reset_child_theme_options() {
		if ( is_child_theme() && empty( wolmart_get_option( 'container' ) ) ) {
			update_option( 'theme_mods_' . get_option( 'stylesheet' ), get_option( 'theme_mods_wolmart' ) );
		}
	}

	public function refresh_transients() {
		delete_site_transient( 'wolmart_plugins' );
		delete_site_transient( 'update_themes' );
		unset( $_COOKIE['wolmart_dismiss_activate_msg'] );
		setcookie( 'wolmart_dismiss_activate_msg', '', -1, '/' );
	}

	public function activation_notices() {
		?>
		<div class="notice error notice-error is-dismissible">
			<?php /* translators: $1 and $2 opening and closing strong tags respectively */ ?>
			<p><?php printf( esc_html__( 'Please %1$sregister%2$s wolmart theme to get access to pre-built demo websites and auto updates.', 'wolmart' ), '<a href="admin.php?page=wolmart">', '</a>' ); ?></p>
			<?php /* translators: $1 and $2 opening and closing strong tags respectively, and $3 and $4 are opening and closing anchor tags respectively */ ?>
			<p><?php printf( esc_html__( '%1$s Important! %2$s One %3$s standard license %4$s is valid for only %1$s1 website%2$s. Running multiple websites on a single license is a copyright violation.', 'wolmart' ), '<strong>', '</strong>', '<a target="_blank" href="https://themeforest.net/licenses/standard">', '</a>' ); ?></p>
			<button type="button" class="notice-dismiss wolmart-notice-dismiss"><span class="screen-reader-text"><?php esc_html__( 'Dismiss this notice.', 'wolmart' ); ?></span></button>
		</div>
		<script>
			(function($) {
				var setCookie = function (name, value, exdays) {
					var exdate = new Date();
					exdate.setDate(exdate.getDate() + exdays);
					var val = encodeURIComponent(value) + ((null === exdays) ? "" : "; expires=" + exdate.toUTCString());
					document.cookie = name + "=" + val;
				};
				$(document).on('click.wolmart-notice-dismiss', '.wolmart-notice-dismiss', function(e) {
					e.preventDefault();
					var $el = $(this).closest('.notice');
					$el.fadeTo( 100, 0, function() {
						$el.slideUp( 100, function() {
							$el.remove();
						});
					});
					setCookie('wolmart_dismiss_activate_msg', '<?php echo WOLMART_VERSION; ?>', 30);
				});
			})(window.jQuery);
		</script>
		<?php
	}

	public function activation_message() {
		?>
		<script>
			(function($){
				$(window).load(function() {
					<?php /* translators: $1 and $2 are opening and closing anchor tags respectively */ ?>
					$('.themes .theme.active .theme-screenshot').after('<div class="notice update-message notice-error notice-alt"><p><?php printf( esc_html__( 'Please %1$sverify purchase%2$s to get updates!', 'wolmart' ), '<a href="admin.php?page=wolmart" class="button-link">', '</a>' ); ?></p></div>');
				});
			})(window.jQuery);
		</script>
		<?php
	}

	public function check_activation() {
		if ( isset( $_POST['wolmart_registration'] ) && check_admin_referer( 'wolmart-setup-wizard' ) ) {
			update_option( 'wolmart_register_error_msg', '' );
			$result = $this->check_purchase_code();
			if ( 'verified' == $result ) {
				update_option( 'wolmart_registered', true );
				$this->refresh_transients();
			} elseif ( 'unregister' == $result ) {
				update_option( 'wolmart_registered', false );
				$this->refresh_transients();
			} elseif ( 'invalid' == $result ) {
				update_option( 'wolmart_registered', false );
				update_option( 'wolmart_register_error_msg', esc_html__( 'There is a problem contacting to the Wolmart API server. Please try again later.', 'wolmart' ) );
			} else {
				update_option( 'wolmart_registered', false );
				update_option( 'wolmart_register_error_msg', $result );
			}
		}


		/**
		 *  delete transients after theme update
		 * 
		 * @since 1.8.13
		 */
		$version = get_option( 'wolmart_version', '1.0' );
		if ( version_compare( WOLMART_VERSION, $version, '!=' ) ) {
			// delete plugin transients
			delete_site_transient( 'wolmart_plugins' );

			update_option( 'wolmart_version', WOLMART_VERSION );
		}
	}

	public function show_activation_notice() {
		if ( ! $this->is_registered() ) {
			if ( ( 'themes.php' == $GLOBALS['pagenow'] && isset( $_GET['page'] ) ) ||
				empty( $_COOKIE['wolmart_dismiss_activate_msg'] ) ||
				version_compare( $_COOKIE['wolmart_dismiss_activate_msg'], WOLMART_VERSION, '<' )
			) {
				add_action( 'admin_notices', array( $this, 'activation_notices' ) );
			} elseif ( 'themes.php' == $GLOBALS['pagenow'] ) {
				add_action( 'admin_footer', array( $this, 'activation_message' ) );
			}
		}
	}
}

Wolmart_Admin::get_instance();
