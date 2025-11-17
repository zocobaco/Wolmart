<?php
/**
 * Wolmart Optimize Wizard
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

define( 'WOLMART_OPTIMIZE_WIZARD', WOLMART_ADMIN . '/optimize-wizard' );

if ( ! class_exists( 'Wolmart_Optimize_Wizard' ) ) :
	/**
	* Wolmart Theme Optimize Wizard
	*/
	class Wolmart_Optimize_Wizard extends Wolmart_Base {

		protected $version = '1.0';

		protected $theme_name = '';

		protected $step = '';

		protected $steps = array();

		public $page_slug;

		protected $tgmpa_instance;

		protected $tgmpa_menu_slug = 'tgmpa-install-plugins';

		protected $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

		protected $page_url;

		protected $files;


		public function __construct() {
			$this->current_theme_meta();
			$this->init_actions();
		}

		public function current_theme_meta() {
			$current_theme    = wp_get_theme();
			$this->theme_name = strtolower( preg_replace( '#[^a-zA-Z]#', '', $current_theme->get( 'Name' ) ) );
			$this->page_slug  = 'wolmart-optimize-wizard';
			$this->page_url   = 'admin.php?page=' . $this->page_slug;
		}

		public function init_actions() {
			add_action( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 2 );

			if ( apply_filters( $this->theme_name . '_enable_optimize_wizard', false ) ) {
				return;
			}

			if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
				add_action( 'init', array( $this, 'get_tgmpa_instanse' ), 30 );
				add_action( 'init', array( $this, 'set_tgmpa_url' ), 40 );
			}

			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'wp_ajax_wolmart_optimize_wizard_resources_load', array( $this, 'ajax_load_resources' ) );
			add_action( 'wp_ajax_wolmart_optimize_wizard_resources_optimize', array( $this, 'ajax_optimize_resources' ) );
			add_action( 'wp_ajax_wolmart_optimize_wizard_plugins', array( $this, 'ajax_plugins' ) );
			add_action( 'wp_ajax_wolmart_optimize_wizard_plugins_deactivate', array( $this, 'ajax_deactivate_plugins' ) );

			if ( isset( $_GET['page'] ) && $this->page_slug === $_GET['page'] ) {
				add_action( 'admin_init', array( $this, 'admin_redirects' ), 30 );
				add_action( 'admin_init', array( $this, 'init_wizard_steps' ), 30 );
				add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 30 );
			}
		}

		public function add_admin_menu() {
			add_submenu_page( 'wolmart', esc_html__( 'Optimize Wizard', 'wolmart' ), esc_html__( 'Optimize Wizard', 'wolmart' ), 'manage_options', $this->page_slug, array( $this, 'view_optimize_wizard' ), 3 );
		}

		public function upgrader_post_install( $return, $theme ) {
			if ( is_wp_error( $return ) ) {
				return $return;
			}
			if ( get_stylesheet() != $theme ) {
				return $return;
			}
			update_option( 'wolmart_optimize_complete', false );

			return $return;
		}

		public function admin_redirects() {
			ob_start();

			if ( ! get_transient( '_' . $this->theme_name . '_activation_redirect' ) || get_option( 'wolmart_optimize_complete', false ) ) {
				return;
			}

			delete_transient( '_' . $this->theme_name . '_activation_redirect' );
			wp_safe_redirect( admin_url( $this->page_url ) );
			exit;
		}

		/**
		 * Display optimize wizard
		 */
		public function enqueue() {

			if ( empty( $_GET['page'] ) || $this->page_slug !== $_GET['page'] ) {
				return;
			}

			$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

			// Style
			wp_enqueue_style( 'wolmart-setup_wiard', WOLMART_ADMIN_URI . '/panel/wizard' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' );
			wp_enqueue_style( 'wp-admin' );
			wp_enqueue_media();

			// Script
			wp_enqueue_script( 'wolmart-admin-wizard', WOLMART_ADMIN_URI . '/panel/wizard.js', array( 'jquery-core' ), true, 50 );
			wp_enqueue_script( 'media' );

			require_once WOLMART_ADMIN . '/importer/importer-api.php';
			$importer_api = new Wolmart_Importer_API();

			wp_localize_script(
				'wolmart-admin-wizard',
				'wolmart_optimize_wizard_params',
				array(
					'tgm_plugin_nonce'    => array(
						'update'  => wp_create_nonce( 'tgmpa-update' ),
						'install' => wp_create_nonce( 'tgmpa-install' ),
					),
					'url_plugin_list_add' => $importer_api->get_url( 'plugin_list_add' ),
					'tgm_bulk_url'        => esc_url( admin_url( $this->tgmpa_url ) ),
					'wpnonce'             => wp_create_nonce( 'wolmart_optimize_wizard_nonce' ),
					'texts'               => array(
						'loading_failed' => esc_html__( 'Loading Failed', 'wolmart' ),
						'failed'         => esc_html__( 'Failed', 'wolmart' ),
						'ajax_error'     => esc_html__( 'Ajax error', 'wolmart' ),
					),
				)
			);

			ob_start();

		}

		/**
		 * Display optimize wizard
		 */
		public function view_optimize_wizard() {
			if ( ! Wolmart_Admin::get_instance()->is_registered() ) {
				wp_redirect( admin_url( 'admin.php?page=wolmart' ) );
				exit;
			}
			Wolmart_Admin_Panel::get_instance()->view_header( 'optimize_wizard' );
			include WOLMART_OPTIMIZE_WIZARD . '/views/index.php';
			Wolmart_Admin_Panel::get_instance()->view_footer();
		}

		public function view_step() {
			$show_content = true;
			if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
				$show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
			}
			if ( $show_content && isset( $this->steps[ $this->step ] ) ) {
				call_user_func( $this->steps[ $this->step ]['view'] );
			} else {
				$this->view_resources();
			}
		}

		public function get_tgmpa_instanse() {
			$this->tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		}

		public function set_tgmpa_url() {

			$this->tgmpa_menu_slug = ( property_exists( $this->tgmpa_instance, 'menu' ) ) ? $this->tgmpa_instance->menu : $this->tgmpa_menu_slug;
			$this->tgmpa_menu_slug = apply_filters( $this->theme_name . '_theme_optimize_wizard_tgmpa_menu_slug', $this->tgmpa_menu_slug );

			$tgmpa_parent_slug = ( property_exists( $this->tgmpa_instance, 'parent_slug' ) && 'themes.php' !== $this->tgmpa_instance->parent_slug ) ? 'admin.php' : 'themes.php';

			$this->tgmpa_url = apply_filters( $this->theme_name . '_theme_optimize_wizard_tgmpa_url', $tgmpa_parent_slug . '?page=' . $this->tgmpa_menu_slug );
		}

		/**
		 * Install plugins
		 */
		public function tgmpa_load( $status ) {
			return is_admin() || current_user_can( 'install_themes' );
		}

		private function _get_plugins() {
			$instance         = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
			$plugin_func_name = 'is_plugin_active';
			$plugins          = array(
				'all'               => array(), // Meaning: all plugins which still have open actions.
				'install'           => array(),
				'update'            => array(),
				'activate'          => array(),
				'installed'         => array(), // all plugins that installed.
				'network_activated' => array(),
			);

			foreach ( $instance->plugins as $slug => $plugin ) {
				if ( ( isset( $plugin['usein'] ) && 'optimize' != $plugin['usein'] ) || ! isset( $plugin['visibility'] ) || 'optimize_wizard' != $plugin['visibility'] || $instance->$plugin_func_name( $slug ) && false === $instance->does_plugin_have_update( $slug ) ) {
					continue;
				} else {
					$plugins['all'][ $slug ] = $plugin;

					if ( ! $instance->is_plugin_installed( $slug ) ) {
						$plugins['install'][ $slug ] = $plugin;
					} else {
						if ( false !== $instance->does_plugin_have_update( $slug ) ) {
							$plugins['update'][ $slug ] = $plugin;
						}

						if ( $instance->can_plugin_activate( $slug ) ) {
							$plugins['activate'][ $slug ] = $plugin;
						}
					}
				}
			}

			$current = get_option( 'active_plugins', array() );
			if ( is_multisite() ) {
				$network_current = get_site_option( 'active_sitewide_plugins', array() );
			}
			foreach ( $current as $plugin ) {
				$plugins['installed'][ $plugin ] = get_plugin_data( WP_PLUGIN_DIR . '/' . $plugin );
			}

			if ( isset( $network_current ) ) {
				$plugins['network_activated'] = $network_current;

				foreach ( $network_current as $slug => $plugin ) {
					$plugins['network_activated'][ $slug ] = get_plugin_data( WP_PLUGIN_DIR . '/' . $slug );
				}
			}

			return $plugins;
		}

		/**
		 * Get All Shortcodes
		 */
		private function get_all_shortcodes() {
			$shortcodes = array();
			if ( class_exists( 'WPBMap' ) ) {
				$all_wpb_shortcodes = WPBMap::getAllShortCodes();
				if ( ! empty( $all_wpb_shortcodes ) ) {
					foreach ( $all_wpb_shortcodes as $key => $value ) {
						if ( 0 === strpos( $key, 'wpb_wolmart' ) || 'vc_section' == $key || 'vc_row' == $key || 'vc_row_inner' == $key || 'vc_column' == $key || 'vc_column_inner' == $key ) {
							continue;
						}
						$shortcodes[] = $key;
					}
				}
			}

			return apply_filters( 'wolmart_all_shortcodes', $shortcodes );
		}

		/**
		 * Get used shortcodes
		 */
		private function get_used_shortcodes( $shortcodes = array() ) {
			if ( empty( $shortcodes ) ) {
				$shortcodes = $this->get_all_shortcodes();
			}
			global $wpdb, $wolmart_settings;
			$post_contents = $wpdb->get_results( $wpdb->prepare( "SELECT ID, post_content, post_excerpt FROM $wpdb->posts WHERE post_type not in (%s, %s) AND post_status = 'publish' AND ( post_content != '' or post_excerpt != '')", 'revision', 'attachment' ) );

			$sidebars_array = get_option( 'sidebars_widgets' );
			if ( empty( $post_contents ) || ! is_array( $post_contents ) ) {
				$post_contents = array();
			}
			foreach ( $sidebars_array as $sidebar => $widgets ) {
				if ( ! empty( $widgets ) && is_array( $widgets ) ) {
					foreach ( $widgets as $sidebar_widget ) {
						$widget_type = trim( substr( $sidebar_widget, 0, strrpos( $sidebar_widget, '-' ) ) );
						if ( ! array_key_exists( $widget_type, $post_contents ) ) {
							$post_contents[ $widget_type ] = get_option( 'widget_' . $widget_type );
						}
					}
				}
			}

			$used = array();

			$excerpt_arr = array(
				'post_content',
				'post_excerpt',
			);
			foreach ( $post_contents as $post_content ) {
				foreach ( $excerpt_arr as $excerpt_key ) {
					if ( is_string( $post_content ) && 'post_excerpt' == $excerpt_key ) {
						break;
					}
					if ( ! is_string( $post_content ) && 'post_excerpt' == $excerpt_key && ! isset( $post_content->post_excerpt ) ) {
						break;
					}
					$content = is_string( $post_content ) ? $post_content : ( isset( $post_content->{$excerpt_key} ) ? $post_content->{$excerpt_key} : '' );

					foreach ( $shortcodes as $shortcode ) {
						if ( false === strpos( $content, '[' ) ) {
							continue;
						}
						if ( ! in_array( $shortcode, $used ) && ( stripos( $content, '[' . $shortcode ) !== false ) ) {
							$used[] = $shortcode;
						}
					}
				}
			}
			return apply_filters( 'wolmart_wpb_get_used_shortcodes', $used );
		}

		public function ajax_plugins() {
			if ( ! check_ajax_referer( 'wolmart_optimize_wizard_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => esc_html__(
							'No Slug Found',
							'wolmart'
						),
					)
				);
			}
			$json = array();
			// send back some json we use to hit up TGM
			$plugins = $this->_get_plugins();
			// what are we doing with this plugin?
			foreach ( $plugins['activate'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => esc_url( admin_url( $this->tgmpa_url ) ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-activate',
						'action2'       => -1,
						'message'       => esc_html__( 'Activating Plugin', 'wolmart' ),
					);
					break;
				}
			}
			foreach ( $plugins['update'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => esc_url( admin_url( $this->tgmpa_url ) ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-update',
						'action2'       => -1,
						'message'       => esc_html__( 'Updating Plugin', 'wolmart' ),
					);
					break;
				}
			}
			foreach ( $plugins['install'] as $slug => $plugin ) {
				if ( $_POST['slug'] == $slug ) {
					$json = array(
						'url'           => esc_url( admin_url( $this->tgmpa_url ) ),
						'plugin'        => array( $slug ),
						'tgmpa-page'    => $this->tgmpa_menu_slug,
						'plugin_status' => 'all',
						'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
						'action'        => 'tgmpa-bulk-install',
						'action2'       => -1,
						'message'       => esc_html__( 'Installing Plugin', 'wolmart' ),
					);
					break;
				}
			}

			if ( $json ) {
				$json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
				wp_send_json( $json );
			} else {
				wp_send_json(
					array(
						'done'    => 1,
						'message' => esc_html__(
							'Success',
							'wolmart'
						),
					)
				);
			}
			exit;
		}
		public function ajax_deactivate_plugins() {
			if ( ! check_ajax_referer( 'wolmart_optimize_wizard_nonce', 'wpnonce' ) || empty( $_POST['url'] ) ) {
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => esc_html__(
							'No Slug Found',
							'wolmart'
						),
					)
				);
			}

			if ( ! current_user_can( 'deactivate_plugin', $plugin ) ) {
				wp_die( esc_html__( 'Sorry, you are not allowed to deactivate this plugin.', 'wolmart' ) );
			}

			deactivate_plugins( array( $_POST['url'] ), true, false );
			die();
		}

		/**
		 * Step links
		 */
		public function get_step_link( $step ) {
			return add_query_arg( 'step', $step, admin_url( 'admin.php?page=' . $this->page_slug ) );
		}
		public function get_next_step_link() {
			$keys = array_keys( $this->steps );
			return add_query_arg( 'step', $keys[ array_search( $this->step, array_keys( $this->steps ) ) + 1 ], remove_query_arg( 'translation_updated' ) );
		}

		public function init_wizard_steps() {
			$this->steps = array(
				'resources'   => array(
					'name'    => esc_html__( 'Resources', 'wolmart' ),
					'view'    => array( $this, 'view_resources' ),
					'handler' => '',
				),
				'lazyload'    => array(
					'name'    => esc_html__( 'Lazyload', 'wolmart' ),
					'view'    => array( $this, 'view_lazyload' ),
					'handler' => array( $this, 'view_lazyload_save' ),
				),
				'performance' => array(
					'name'    => esc_html__( 'Peformance', 'wolmart' ),
					'view'    => array( $this, 'view_performance' ),
					'handler' => array( $this, 'view_performance_save' ),
				),
			);

			if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
				$this->steps['plugins'] = array(
					'name'    => esc_html__( 'Plugins', 'wolmart' ),
					'view'    => array( $this, 'view_plugins' ),
					'handler' => '',
				);
			};

			$this->steps['ready'] = array(
				'name'    => esc_html__( 'Ready!', 'wolmart' ),
				'view'    => array( $this, 'view_ready' ),
				'handler' => '',
			);

			$this->steps = apply_filters( $this->theme_name . '_theme_optimize_wizard_steps', $this->steps );
		}

		// View for each step content
		public function view_welcome() {
			include WOLMART_OPTIMIZE_WIZARD . '/views/welcome.php';
		}

		public function view_resources() {
			// wolmart_compile_dynamic_css( 'optimize', get_theme_mod( 'used_elements' ) );
			include WOLMART_OPTIMIZE_WIZARD . '/views/resources.php';
		}

		public function ajax_load_resources() {
			if ( ! check_ajax_referer( 'wolmart_optimize_wizard_nonce', 'wpnonce' ) ) {
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => esc_html__(
							'Nonce Error',
							'wolmart'
						),
					)
				);
			}

			$wolmart_used_elements = array();
			// $checked_elements = get_theme_mod( 'used_elements', false );
			// include WOLMART_ADMIN . '/customizer/dynamic/dynamic_conditions.php';
			// foreach ( $checked_elements as $element => $used ) {
			// 	if ( ! ( isset( $wolmart_used_elements[ $element ] ) && $wolmart_used_elements[ $element ] ) ) {
			// 		$wolmart_used_elements[ $element ] = $used;
			// 	}
			// }

			// $used_shortcodes    = array();
			// $checked_shortcodes = array();

			// get used shortcodes
			if ( defined( 'WPB_VC_VERSION' ) ) {
				$checked_shortcodes = get_theme_mod( 'used_wpb_shortcodes', false );
				$used_shortcodes    = $this->get_used_shortcodes();
				if ( $checked_shortcodes ) {
					$checked_shortcodes = array_merge( $used_shortcodes, $checked_shortcodes );
				} else {
					$checked_shortcodes = $used_shortcodes;
				}
				include WOLMART_OPTIMIZE_WIZARD . '/views/resources_load.php';
				die;
			}
		}

		public function ajax_optimize_resources() {
			if ( ! check_ajax_referer( 'wolmart_optimize_wizard_nonce', 'wpnonce' ) ) {
				wp_send_json_error(
					array(
						'error'   => 1,
						'message' => esc_html__(
							'Nonce Error',
							'wolmart'
						),
					)
				);
			}

			$elements = array();
			if ( ! empty( $_POST['used'] ) ) {
				foreach ( $_POST['used'] as $used_element ) {
					$elements[ $used_element ] = 1;
				}
			}
			if ( ! empty( $_POST['unused'] ) ) {
				foreach ( $_POST['unused'] as $unused_element ) {
					$elements[ $unused_element ] = 0;
				}
			}

			set_theme_mod( 'used_elements', $elements );
			// wolmart_compile_dynamic_css( 'optimize', $elements );

			if ( defined( 'WPB_VC_VERSION' ) && function_exists( 'wolmart_wpb_shortcode_compile_css' ) ) {
				$wpb_shortcodes = array();
				if ( ! empty( $_POST['used_shortcode'] ) ) {
					foreach ( $_POST['used_shortcode'] as $used_shortcode ) {
						$wpb_shortcodes[] = $used_shortcode;
					}
				}

				set_theme_mod( 'used_wpb_shortcodes', $wpb_shortcodes );

				// Compile WPBakery Shortcodes
				$wpb_shortcodes_to_remove = array();
				if ( isset( $_POST['unused_shortcode'] ) && ! empty( $_POST['unused_shortcode'] ) ) {
					$wpb_shortcodes_to_remove = array_map( 'sanitize_text_field', $_POST['unused_shortcode'] );
				}

				wolmart_wpb_shortcode_compile_css( $wpb_shortcodes_to_remove );
			}

			set_theme_mod( 'resource_disable_gutenberg', isset( $_POST['resource_disable_gutenberg'] ) && 'true' == $_POST['resource_disable_gutenberg'] );
			set_theme_mod( 'resource_disable_wc_blocks', isset( $_POST['resource_disable_wc_blocks'] ) && 'true' == $_POST['resource_disable_wc_blocks'] );
			set_theme_mod( 'resource_disable_elementor', isset( $_POST['resource_disable_elementor'] ) && 'true' == $_POST['resource_disable_elementor'] );
			set_theme_mod( 'resource_disable_fontawesome', isset( $_POST['resource_disable_fontawesome'] ) && 'true' == $_POST['resource_disable_fontawesome'] );
			set_theme_mod( 'resource_disable_dokan', isset( $_POST['resource_disable_dokan'] ) && 'true' == $_POST['resource_disable_dokan'] );

			set_theme_mod( 'resource_disable_emojis', isset( $_POST['resource_disable_emojis'] ) && 'true' == $_POST['resource_disable_emojis'] );
			set_theme_mod( 'resource_disable_jq_migrate', isset( $_POST['resource_disable_jq_migrate'] ) && 'true' == $_POST['resource_disable_jq_migrate'] );
			set_theme_mod( 'resource_jquery_footer', isset( $_POST['resource_jquery_footer'] ) && 'true' == $_POST['resource_jquery_footer'] );
			set_theme_mod( 'resource_merge_stylesheets', isset( $_POST['resource_merge_stylesheets'] ) && 'true' == $_POST['resource_merge_stylesheets'] );
			set_theme_mod( 'resource_critical_css', isset( $_POST['resource_critical_css'] ) && 'true' == $_POST['resource_critical_css'] );

			echo 'success';
			die;
		}

		public function view_lazyload() {
			include WOLMART_OPTIMIZE_WIZARD . '/views/lazyload.php';
		}

		public function view_lazyload_save() {
			check_admin_referer( 'wolmart-setup-wizard' );

			set_theme_mod( 'lazyload', isset( $_POST['lazyload'] ) );
			set_theme_mod( 'lazyload_menu', isset( $_POST['lazyload_menu'] ) );
			set_theme_mod( 'skeleton_screen', isset( $_POST['skeleton'] ) );
			set_theme_mod( 'google_webfont', isset( $_POST['webfont'] ) );
			wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
			die;
		}

		public function view_performance() {
			include WOLMART_OPTIMIZE_WIZARD . '/views/performance.php';
		}

		public function view_performance_save() {
			check_admin_referer( 'wolmart-setup-wizard' );

			set_theme_mod( 'mobile_disable_animation', isset( $_POST['mobile_disable_animation'] ) );
			set_theme_mod( 'mobile_disable_slider', isset( $_POST['mobile_disable_slider'] ) );

			$preload_fonts = wolmart_get_option( 'preload_fonts' );
			if ( empty( $preload_fonts ) ) {
				$preload_fonts = array();
			}
			if ( isset( $_POST['preload_fonts'] ) ) {
				$preload_fonts = array_map( 'sanitize_text_field', $_POST['preload_fonts'] );
			} else {
				$preload_fonts = array();
			}
			if ( isset( $_POST['preload_fonts_custom'] ) ) {
				$preload_fonts['custom'] = sanitize_textarea_field( $_POST['preload_fonts_custom'] );
			}
			set_theme_mod( 'preload_fonts', $preload_fonts );

			if ( isset( $_POST['font_face_display'] ) ) {
				set_theme_mod( 'font_face_display', $_POST['font_face_display'] );
			}

			set_theme_mod( 'resource_async_js', isset( $_POST['resource_async_js'] ) );
			set_theme_mod( 'resource_split_tasks', isset( $_POST['resource_split_tasks'] ) );
			set_theme_mod( 'resource_idle_run', isset( $_POST['resource_idle_run'] ) );
			set_theme_mod( 'resource_after_load', isset( $_POST['resource_after_load'] ) );
			wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
			die();
		}

		public function view_plugins() {
			include WOLMART_OPTIMIZE_WIZARD . '/views/plugins.php';
		}

		public function view_ready() {
			include WOLMART_OPTIMIZE_WIZARD . '/views/ready.php';
		}
	}
endif;

add_action( 'after_setup_theme', 'wolmart_theme_optimize_optimize_wizard', 10 );

if ( ! function_exists( 'wolmart_theme_optimize_optimize_wizard' ) ) :
	function wolmart_theme_optimize_optimize_wizard() {
		Wolmart_Optimize_Wizard::get_instance();
	}
endif;
