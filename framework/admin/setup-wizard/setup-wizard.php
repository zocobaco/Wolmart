<?php
/**
 * Wolmart Setup Wizard
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

define( 'WOLMART_SETUP_WIZARD', WOLMART_ADMIN . '/setup-wizard' );

if ( ! class_exists( 'Wolmart_Setup_Wizard' ) ) :
	/**
	* Wolmart Theme Setup Wizard
	*/
	class Wolmart_Setup_Wizard extends Wolmart_Base {

		protected $version = '1.0';

		protected $theme_name = '';

		protected $step = '';

		protected $steps = array();

		public $page_slug;

		protected $tgmpa_instance;

		protected $tgmpa_menu_slug = 'tgmpa-install-plugins';

		protected $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

		protected $page_url;

		protected $wolmart_url = 'https://d-themes.com/wordpress/wolmart/';

		protected $demo;

		public $demo_import_post_types         = array( 'page', 'wolmart_template', 'post', 'product', 'nav_menu_item' );
		public $demo_import_taxonomies         = array( 'category', 'product_cat', 'product_brand' );
		public $demo_import_product_attributes = array( 'Color', 'Size' );
		public $woopages                       = array(
			'woocommerce_shop_page_id'      => 'Shop',
			'woocommerce_cart_page_id'      => 'Cart',
			'woocommerce_checkout_page_id'  => 'Checkout',
			'woocommerce_myaccount_page_id' => 'My account',
		);

		public function __construct() {
			$this->current_theme_meta();
			$this->init_setup_wizard();
		}

		public function current_theme_meta() {
			$current_theme    = wp_get_theme();
			$this->theme_name = strtolower( preg_replace( '#[^a-zA-Z]#', '', $current_theme->get( 'Name' ) ) );
			$this->page_slug  = 'wolmart-setup-wizard';
			$this->page_url   = 'admin.php?page=' . $this->page_slug;
		}

		public function init_setup_wizard() {
			add_action( 'upgrader_post_install', array( $this, 'upgrader_post_install' ), 10, 2 );

			if ( apply_filters( $this->theme_name . '_enable_setup_wizard', false ) ) {
				return;
			}

			// if ( ! is_child_theme() ) {
			add_action( 'after_switch_theme', array( $this, 'switch_theme' ) );
			// }

			if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
				add_action( 'init', array( $this, 'get_tgmpa_instanse' ), 30 );
				add_action( 'init', array( $this, 'set_tgmpa_url' ), 40 );
			}

			add_action( 'admin_init', array( $this, 'admin_redirects' ), 30 );
			add_action( 'admin_init', array( $this, 'init_wizard_steps' ), 30 );
			add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 30 );

			// Plugin Install
			add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
			add_action( 'wp_ajax_wolmart_setup_wizard_plugins', array( $this, 'ajax_plugins' ) );
			add_action( 'wp_ajax_wolmart_setup_wizard_page_builder', array( $this, 'ajax_page_builder' ) );

			//Demo Import
			add_action( 'wp_ajax_wolmart_reset_menus', array( $this, 'reset_menus' ) );
			add_action( 'wp_ajax_wolmart_reset_widgets', array( $this, 'reset_widgets' ) );
			add_action( 'wp_ajax_wolmart_import_dummy', array( $this, 'import_dummy' ) );
			add_action( 'wp_ajax_wolmart_import_dummy_step_by_step', array( $this, 'import_dummy_step_by_step' ) );
			add_action( 'wp_ajax_wolmart_import_widgets', array( $this, 'import_widgets' ) );
			add_action( 'wp_ajax_wolmart_import_subpages', array( $this, 'import_subpages' ) );
			add_action( 'wp_ajax_wolmart_import_options', array( $this, 'import_options' ) );
			add_action( 'wp_ajax_wolmart_delete_tmp_dir', array( $this, 'delete_tmp_dir' ) );
			add_action( 'wp_ajax_wolmart_download_demo_file', array( $this, 'download_demo_file' ) );

			add_filter( 'wp_import_existing_post', array( $this, 'import_override_contents' ), 10, 2 );
			add_action( 'import_start', array( $this, 'import_dummy_start' ) );
			add_action( 'import_end', array( $this, 'import_dummy_end' ) );

			if ( ( ! empty( $_GET['page'] ) && $this->page_slug === $_GET['page'] ) || ( wp_doing_ajax() && isset( $_REQUEST['action'] ) && 0 === strpos( $_REQUEST['action'], 'wolmart_' ) ) ) {
				require_once 'demo-history.php';
				new Wolmart_Demo_History();
			}

			if ( (int) get_transient( 'wolmart_clean_after_setup' ) && ! wolmart_doing_ajax() ) {
				$this->clean_after_import();
			}
		}

		public function add_admin_menu() {
			add_submenu_page( 'wolmart', esc_html__( 'Setup Wizard', 'wolmart' ), esc_html__( 'Setup Wizard', 'wolmart' ), 'manage_options', $this->page_slug, array( $this, 'view_setup_wizard' ), 2 );
		}

		public function upgrader_post_install( $return, $theme ) {
			if ( is_wp_error( $return ) ) {
				return $return;
			}
			if ( get_stylesheet() != $theme ) {
				return $return;
			}
			update_option( 'wolmart_setup_complete', false );

			return $return;
		}

		public function switch_theme() {
			if ( ! is_child_theme() ) {
				set_transient( '_' . $this->theme_name . '_activation_redirect', 1 );
			} else {
				$this->reset_child_theme_options();
			}
		}

		public function reset_child_theme_options() {
			if ( empty( get_theme_mod( 'container' ) ) ) {
				$parent_theme_options = get_option( 'theme_mods_wolmart' );
				update_option( 'theme_mods_' . get_option( 'stylesheet' ), $parent_theme_options );
			}
		}

		public function admin_redirects() {
			ob_start();

			if ( ! get_transient( '_' . $this->theme_name . '_activation_redirect' ) || get_option( 'wolmart_setup_complete', false ) ) {
				return;
			}
			delete_transient( '_' . $this->theme_name . '_activation_redirect' );
			wp_safe_redirect( admin_url( $this->page_url ) );
			exit;
		}

		public function get_tgmpa_instanse() {
			$this->tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		}

		public function set_tgmpa_url() {

			$this->tgmpa_menu_slug = ( property_exists( $this->tgmpa_instance, 'menu' ) ) ? $this->tgmpa_instance->menu : $this->tgmpa_menu_slug;
			$this->tgmpa_menu_slug = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_menu_slug', $this->tgmpa_menu_slug );

			$tgmpa_parent_slug = ( property_exists( $this->tgmpa_instance, 'parent_slug' ) && 'themes.php' !== $this->tgmpa_instance->parent_slug ) ? 'admin.php' : 'themes.php';

			$this->tgmpa_url = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_url', $tgmpa_parent_slug . '?page=' . $this->tgmpa_menu_slug );

		}

		public function init_wizard_steps() {

			$this->steps['status'] = array(
				'name'    => esc_html__( 'System Status', 'wolmart' ),
				'view'    => array( $this, 'view_status' ),
				'handler' => '',
			);

			$this->steps['customize'] = array(
				'name'    => esc_html__( 'Child Theme', 'wolmart' ),
				'view'    => array( $this, 'view_customize' ),
				'handler' => '',
			);

			if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
				$this->steps['page_builder'] = array(
					'name'    => esc_html__( 'Page Builder', 'wolmart' ),
					'view'    => array( $this, 'view_page_builders' ),
					'handler' => '',
				);

				$this->steps['default_plugins'] = array(
					'name'    => esc_html__( 'Install Plugins', 'wolmart' ),
					'view'    => array( $this, 'view_default_plugins' ),
					'handler' => '',
				);
			}

			$this->steps['demo_content'] = array(
				'name'    => esc_html__( 'Import Demo', 'wolmart' ),
				'view'    => array( $this, 'view_demo_content' ),
				'handler' => array( $this, 'wolmart_setup_wizard_demo_content_save' ),
			);

			$this->steps['ready'] = array(
				'name'    => esc_html__( 'Ready!', 'wolmart' ),
				'view'    => array( $this, 'view_ready' ),
				'handler' => '',
			);

			$this->steps = apply_filters( $this->theme_name . '_theme_setup_wizard_steps', $this->steps );
		}

		// enqueue style & script
		public function enqueue() {

			if ( empty( $_GET['page'] ) || $this->page_slug !== $_GET['page'] ) {
				return;
			}

			$this->step = isset( $_GET['step'] ) ? sanitize_key( $_GET['step'] ) : current( array_keys( $this->steps ) );

			// Style
			wp_enqueue_style( 'wolmart-admin-wiard', WOLMART_ADMIN_URI . '/panel/wizard' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' );
			wp_enqueue_style( 'magnific-popup' );
			wp_enqueue_style( 'wp-admin' );
			wp_enqueue_media();

			// Script
			wp_enqueue_script( 'isotope-pkgd' );
			wp_enqueue_script( 'jquery-magnific-popup' );
			wp_enqueue_script( 'wolmart-admin-wizard', WOLMART_ADMIN_URI . '/panel/wizard.js', array( 'jquery-core' ), true, 50 );
			wp_enqueue_script( 'media' );

			wp_localize_script(
				'wolmart-admin-wizard',
				'wolmart_setup_wizard_params',
				array(
					'tgm_plugin_nonce' => array(
						'update'  => wp_create_nonce( 'tgmpa-update' ),
						'install' => wp_create_nonce( 'tgmpa-install' ),
					),
					'tgm_bulk_url'     => esc_url( admin_url( $this->tgmpa_url ) ),
					'wpnonce'          => wp_create_nonce( 'wolmart_setup_wizard_nonce' ),
					'texts'            => array(
						'confirm_leave'    => esc_html__( 'Are you sure you want to leave?', 'wolmart' ),
						'confirm_override' => esc_html__( 'Are you sure to import demo contents and override old one?', 'wolmart' ),
						/* translators: $1 and $2 opening and closing strong tags respectively */
						'import_failed'    => vsprintf( esc_html__( 'Failed importing! Please check the %1$s"System Status"%2$s tab to ensure your server meets all requirements for a successful import. Settings that need attention will be listed in red. If your server provider does not allow to update settings, please try using alternative import mode.', 'wolmart' ), array( '<a href="' . esc_url( $this->page_url . '&step=status' ) . '" target="_blank">', '</a>' ) ),
						'install_failed'   => esc_html__( ' installation is failed!', 'wolmart' ),
						'install_finished' => esc_html__( ' installation is finished!', 'wolmart' ),
						'installing'       => esc_html__( 'Installing', 'wolmart' ),
						'demo_import'      => esc_html__( 'Demo Import', 'wolmart' ),
						'visit_your_site'  => esc_html__( 'Visit your site.', 'wolmart' ),
						'failed'           => esc_html__( 'Failed', 'wolmart' ),
						'ajax_error'       => esc_html__( 'Ajax error', 'wolmart' ),
					),
				)
			);
		}

		/**
		 * Display setup wizard
		 */
		public function view_setup_wizard() {
			if ( ! Wolmart_Admin::get_instance()->is_registered() ) {
				wp_redirect( admin_url( 'admin.php?page=wolmart' ) );
				exit;
			}
			Wolmart_Admin_Panel::get_instance()->view_header( 'setup_wizard' );
			include WOLMART_SETUP_WIZARD . '/views/index.php';
			Wolmart_Admin_Panel::get_instance()->view_footer();
		}

		public function view_step() {
			$show_content = true;
			if ( ! empty( $_REQUEST['save_step'] ) && isset( $this->steps[ $this->step ]['handler'] ) ) {
				$show_content = call_user_func( $this->steps[ $this->step ]['handler'] );
			}
			if ( $show_content && isset( $this->steps[ $this->step ] ) ) {
				call_user_func( $this->steps[ $this->step ]['view'] );
			}
		}

		/**
		 * Output the step contents
		 */
		public function view_status() {
			include WOLMART_SETUP_WIZARD . '/views/status.php';
		}
		public function view_customize() {
			include WOLMART_SETUP_WIZARD . '/views/customize.php';
		}
		public function view_page_builders() {
			include WOLMART_SETUP_WIZARD . '/views/page-builder.php';
		}
		public function view_default_plugins() {

			tgmpa_load_bulk_installer();
			if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
				die( esc_html__( 'Failed to find TGM', 'wolmart' ) );
			}
			$url     = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'wolmart-setup-wizard' );
			$plugins = $this->_get_plugins();

			$method = '';
			$fields = array_keys( $_POST );

			if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
				return true;
			}

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
				return true;
			}

			include WOLMART_SETUP_WIZARD . '/views/plugins.php';
		}
		public function view_demo_content() {
			$url    = wp_nonce_url( add_query_arg( array( 'demo_content' => 'go' ) ), 'wolmart-setup-wizard' );
			$method = '';
			$fields = array_keys( $_POST );
			if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
				return true;
			}

			if ( ! WP_Filesystem( $creds ) ) {
				request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
				return true;
			}
			include WOLMART_SETUP_WIZARD . '/views/demo.php';
		}
		public function view_support() {
			include WOLMART_SETUP_WIZARD . '/views/support.php';
		}
		public function view_ready() {
			include WOLMART_SETUP_WIZARD . '/views/ready.php';
		}

		/**
		 * Save actions
		 */
		public function wolmart_setup_wizard_welcome_save() {
			check_admin_referer( 'wolmart-setup-wizard' );
			return false;
		}

		public function wolmart_setup_wizard_demo_content_save() {
			check_admin_referer( 'wolmart-setup-wizard' );
			if ( ! empty( $_POST['new_logo_id'] ) ) {
				$new_logo_id = (int) $_POST['new_logo_id'];
				if ( $new_logo_id ) {
					set_theme_mod( 'custom_logo', $new_logo_id );
				}
			}
			wp_redirect( esc_url_raw( $this->get_next_step_link() ) );
			die();
		}

		/**
		 * Create child theme
		 */
		private function _make_child_theme( $new_theme_title ) {

			$parent_theme_title    = 'Wolmart';
			$parent_theme_template = 'wolmart';
			$parent_theme_name     = get_stylesheet();
			$parent_theme_dir      = get_stylesheet_directory();

			$new_theme_name = sanitize_title( $new_theme_title );
			$theme_root     = get_theme_root();

			$new_theme_path = $theme_root . '/' . $new_theme_name;
			if ( ! file_exists( $new_theme_path ) ) {
				wp_mkdir_p( $new_theme_path );

				$plugin_folder = get_parent_theme_file_path( 'framework/admin/setup-wizard/wolmart-child/' );

				ob_start();
				require $plugin_folder . 'style.css.php';
				$css = ob_get_clean();

				// filesystem
				global $wp_filesystem;
				// Initialize the WordPress filesystem, no more using file_put_contents function
				if ( empty( $wp_filesystem ) ) {
					require_once ABSPATH . '/wp-admin/includes/file.php';
					WP_Filesystem();
				}

				if ( ! $wp_filesystem->put_contents( $new_theme_path . '/style.css', $css, FS_CHMOD_FILE ) ) {
					echo '<p class="lead success">';
					/* translators: %s: path */
					printf( esc_html__( 'Directory permission required for %s', 'wolmart' ), '/wp-content/themes.' );
					echo '</p>';
					return;
				}

				// Copy functions.php
				copy( $plugin_folder . 'functions.php', $new_theme_path . '/functions.php' );

				// Copy screenshot
				copy( $plugin_folder . 'screenshot.jpg', $new_theme_path . '/screenshot.jpg' );

				// Make child theme an allowed theme (network enable theme)
				$allowed_themes                    = get_site_option( 'allowedthemes', array() );
				$allowed_themes[ $new_theme_name ] = true;
				update_site_option( 'allowedthemes', $allowed_themes );
			}

			// Switch to theme
			if ( $parent_theme_template !== $new_theme_name ) {

				echo '<p class="lead success">';
				/* translators: %1$s: Theme name, %1$s: br tag, %3$s: path */
				printf( esc_html__( 'Child Theme %1$s has been created and activated!%2$s Folder is located in %3$s', 'wolmart' ), '<strong>' . esc_html( $new_theme_title ) . '</strong>', '<br />', 'wp-content/themes/<strong>' . esc_html( $new_theme_name ) . '</strong>' );
				echo '</p>';
				switch_theme( $new_theme_name, $new_theme_name );
			}
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
				'all'      => array(), // Meaning: all plugins which still have open actions.
				'install'  => array(),
				'update'   => array(),
				'activate' => array(),
			);

			foreach ( $instance->plugins as $slug => $plugin ) {
				if ( 'setup' != $plugin['usein'] || ( $instance->$plugin_func_name( $slug ) && false === $instance->does_plugin_have_update( $slug ) ) ) {
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
			return $plugins;
		}
		public function ajax_plugins() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
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

			$repo_updates = get_site_transient( 'update_plugins' );
			if ( empty( $repo_updates ) ) {
				wp_update_plugins();
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

		public function ajax_page_builder() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce' ) ) {
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

			$builder   = isset( $_POST['builder'] ) ? $_POST['builder'] : 'elementor';
			$uninstall = isset( $_POST['uninstall'] ) && 'true' == $_POST['uninstall'] ? true : false;

			$unchecked_builder = 'elementor' == $builder ? 'js_composer/js_composer.php' : 'elementor/elementor.php';

			update_option( 'wolmart_prefer_page_builder', $builder );
			update_option( 'uninstall_page_builder', $uninstall );

			if ( $uninstall ) {
				deactivate_plugins( $unchecked_builder );
			}
			exit;
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

		/**
		 * Demo Import
		 */
		private function get_demo_file( $demo = false ) {
			if ( ! $demo ) {
				$demo = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
			}

			$this->demo = $demo;

			// Return demo file path.
			require_once WOLMART_ADMIN . '/importer/importer-api.php';

			$importer_api = new Wolmart_Importer_API( $demo );

			$demo_file_path = $importer_api->get_remote_demo();

			if ( ! $demo_file_path ) {
				echo json_encode(
					array(
						'process' => 'error',
						'message' => esc_html__( 'Remote API error.', 'wolmart' ),
					)
				);
				die();
			} elseif ( is_wp_error( $demo_file_path ) ) {
				echo json_encode(
					array(
						'process' => 'error',
						'message' => $demo_file_path->get_error_message(),
					)
				);
				die();
			}
			return $demo_file_path;
		}

		private function get_file_data( $path ) {
			$data = false;
			$path = wp_normalize_path( $path );
			// File System
			global $wp_filesystem;

			// Initialize the WordPress filesystem, no more using file_put_contents function.
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
			if ( $wp_filesystem->exists( $path ) ) {
				$data = $wp_filesystem->get_contents( $path );
			}
			return $data;
		}

		public function download_demo_file() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			$this->get_demo_file();
			echo json_encode( array( 'process' => 'success' ) );
			die();
		}

		/**
		 * Delete temporary directory
		 */
		function delete_tmp_dir() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			$demo = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';

			// Importer remote API.
			require_once WOLMART_ADMIN . '/importer/importer-api.php';
			$importer_api = new Wolmart_Importer_API( $demo );

			$importer_api->delete_temp_dir();
			die();
		}

		function reset_menus() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				do_action( 'wolmart_importer_before_reset_menus' );

				$import_shortcodes = ( isset( $_POST['import_shortcodes'] ) && 'true' == $_POST['import_shortcodes'] ) ? true : false;
				if ( $import_shortcodes ) {
					$menus = array( 'Main Menu', 'Category Menu', 'Top Navigation', 'Currency Switcher', 'Language Switcher', 'Footer Nav 1', 'Footer Nav 2', 'Footer Nav 3', 'Deal Menu', 'Category Menu 1', 'Category Menu 2', 'Header Nav' );
				} else {
					$menus = array( 'Main Menu', 'Category Menu', 'Top Navigation', 'Currency Switcher', 'Language Switcher', 'Footer Nav 1', 'Footer Nav 2', 'Footer Nav 3', 'Deal Menu', 'Category Menu 1', 'Category Menu 2', 'Header Nav' );
				}

				foreach ( $menus as $menu ) {
					wp_delete_nav_menu( $menu );
				}
				esc_html_e( 'Successfully reset menus!', 'wolmart' );
			}
			die;
		}

		function reset_widgets() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				do_action( 'wolmart_importer_before_import_widgets' );

				ob_start();
				$sidebars_widgets = retrieve_widgets();
				foreach ( $sidebars_widgets as $area => $widgets ) {
					foreach ( $widgets as $key => $widget_id ) {
						$pieces       = explode( '-', $widget_id );
						$multi_number = array_pop( $pieces );
						$id_base      = implode( '-', $pieces );
						$widget       = get_option( 'widget_' . $id_base );
						unset( $widget[ $multi_number ] );
						update_option( 'widget_' . $id_base, $widget );
						unset( $sidebars_widgets[ $area ][ $key ] );
					}
				}

				update_option( 'sidebars_widgets', $sidebars_widgets );
				ob_clean();
				ob_end_clean();
				esc_html_e( 'Successfully reset widgets!', 'wolmart' );
			}
			die;
		}

		function import_dummy() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			global $import_logo;
			if ( empty( $import_logo ) ) {
				$import_logo = wolmart_get_option( 'custom_logo' );
			}
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true ); // we are loading importers
			}
			if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
				require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			}
			if ( ! class_exists( 'WP_Import' ) ) { // if WP importer doesn't exist
				require_once WOLMART_ADMIN . '/importer/wordpress-importer.php';
			}

			if ( current_user_can( 'manage_options' ) && class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) { // check for main import class and wp import class

				$demo                        = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
				$process                     = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
				$demo_path                   = $this->get_demo_file();
				$importer                    = new WP_Import();
				$theme_xml                   = $demo_path . '/content.xml';
				$importer->fetch_attachments = true;

				$this->import_before_functions( $demo );

				// ob_start();
				$response = $importer->import( $theme_xml, $process );

				// ob_end_clean();

				if ( 'import_start' == $process && $response ) {
					echo json_encode(
						array(
							'process' => 'importing',
							'count'   => 0,
							'index'   => 0,
							'message' => 'success',
						)
					);
				} else {
					$this->import_after_functions( $demo );
				}
			}
			die();
		}

		function import_override_contents( $post_exists, $post ) {
			$override_contents = ( isset( $_POST['override_contents'] ) && 'true' == $_POST['override_contents'] ) ? true : false;
			if ( ! $override_contents || ( $post_exists && get_post_type( $post_exists ) != 'revision' ) ) {
				return $post_exists;
			}

			// remove posts which have same ID
			$processed_duplicates = get_option( 'wolmart_import_processed_duplicates', array() );
			if ( in_array( $post['post_id'], $processed_duplicates ) ) {
				return false;
			}
			$old_post = get_post( $post['post_id'] );
			if ( $old_post ) {
				if ( $old_post->post_type == $post['post_type'] && in_array( $post['post_type'], $this->demo_import_post_types ) ) {
					return $post['post_id'];
				}
				if ( defined( 'ELEMENTOR_VERSION' ) && 'kit' == get_post_meta( $post['post_id'], '_elementor_template_type', true ) ) {
					$_GET['force_delete_kit'] = true;
				}
				wp_delete_post( $post['post_id'], true );
				if ( $post['post_id'] == get_option( 'elementor_active_kit' ) ) {
					delete_option( 'elementor_active_kit' );
				}
				unset( $_GET['force_delete_kit'] );
			}

			// remove posts which have same title and slug
			global $wpdb;

			$post_title = wp_unslash( sanitize_post_field( 'post_title', $post['post_title'], 0, 'db' ) );
			$post_name  = wp_unslash( sanitize_post_field( 'post_name', $post['post_name'], 0, 'db' ) );

			$query  = "SELECT ID FROM $wpdb->posts WHERE 1=1";
			$args   = array();
			$query .= ' AND post_title = %s';
			$args[] = $post_title;
			$query .= ' AND post_name = %s';
			$args[] = $post_name;

			$old_post = (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) );

			if ( $old_post && get_post_type( $old_post ) == $post['post_type'] ) {
				if ( in_array( $post['post_type'], $this->demo_import_post_types ) ) {
					$processed_duplicates[] = $old_post;
					update_option( 'wolmart_import_processed_duplicates', $processed_duplicates );
					return $old_post;
				}
				if ( defined( 'ELEMENTOR_VERSION' ) && 'kit' == get_post_meta( $old_post, '_elementor_template_type', true ) ) {
					$_GET['force_delete_kit'] = true;
				}
				wp_delete_post( $old_post, true );
				if ( $old_post == get_option( 'elementor_active_kit' ) ) {
					delete_option( 'elementor_active_kit' );
				}
				unset( $_GET['force_delete_kit'] );
			}

			return false;
		}

		function import_dummy_start() {
			$process = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
			if ( current_user_can( 'manage_options' ) && 'import_start' == $process ) {
				delete_option( 'wolmart_import_processed_duplicates' );
			}

			if ( class_exists( 'WC_Comments' ) ) {
				remove_action( 'wp_update_comment_count', array( 'WC_Comments', 'clear_transients' ) );
			}
		}

		function import_dummy_end() {
			if ( current_user_can( 'manage_options' ) && isset( $_POST['action'] ) && 'wolmart_import_dummy' == $_POST['action'] ) {
				ob_end_clean();
				ob_start();
				echo json_encode(
					array(
						'process' => 'complete',
						'message' => 'success',
					)
				);
				ob_end_flush();
				ob_start();
			}

			if ( class_exists( 'WC_Comments' ) ) {
				add_action( 'wp_update_comment_count', array( 'WC_Comments', 'clear_transients' ) );
			}
		}

		function import_dummy_step_by_step() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}

			global $import_logo;
			if ( empty( $import_logo ) ) {
				$import_logo = wolmart_get_option( 'custom_logo' );
			}

			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true ); // we are loading importers
			}

			if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
				$wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
				include $wp_importer;
			}

			if ( ! class_exists( 'Wolmart_WP_Import' ) ) { // if WP importer doesn't exist
				$wp_import = WOLMART_ADMIN . '/importer/theme-wordpress-importer.php';
				include $wp_import;
			}

			if ( current_user_can( 'manage_options' ) && class_exists( 'WP_Importer' ) && class_exists( 'Wolmart_WP_Import' ) ) { // check for main import class and wp import class

				$process   = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
				$demo      = ( isset( $_POST['demo'] ) && $_POST['demo'] ) ? sanitize_text_field( $_POST['demo'] ) : 'landing';
				$index     = ( isset( $_POST['index'] ) && $_POST['index'] ) ? (int) $_POST['index'] : 0;
				$demo_path = $this->get_demo_file();

				$importer                    = new Wolmart_WP_Import();
				$theme_xml                   = $demo_path . '/content.xml';
				$importer->fetch_attachments = true;

				if ( 'import_start' == $process ) {
					$this->import_before_functions( $demo );
				}

				$loop = (int) ( ini_get( 'max_execution_time' ) / 60 );
				if ( $loop < 1 ) {
					$loop = 1;
				}
				if ( $loop > 10 ) {
					$loop = 10;
				}
				$i = 0;
				while ( $i < $loop ) {
					$response = $importer->import( $theme_xml, $process, $index );
					if ( isset( $response['count'] ) && isset( $response['index'] ) && $response['count'] && $response['index'] && $response['index'] < $response['count'] ) {
						++ $i;
						$index = $response['index'];
					} else {
						break;
					}
				}

				echo json_encode( $response );
				ob_start();
				if ( 'complete' == $response['process'] ) {
					$this->import_after_functions( $demo );
				}
				ob_end_clean();
			}
			die();
		}

		function import_widgets() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				do_action( 'wolmart_importer_before_import_widgets' );

				// Import widgets
				$demo_path   = $this->get_demo_file();
				$widget_data = $this->get_file_data( $demo_path . '/widget_data.json' );
				$this->before_replacement();
				$widget_data = preg_replace_callback( '|(\"nav_menu\":)(\d+)|', array( $this, 'replace_term_ids' ), $widget_data );
				$this->import_widget_data( $widget_data );
				esc_html_e( 'Successfully imported widgets!', 'wolmart' );
				flush_rewrite_rules();
			}
			die();
		}

		function import_override_subpages( $post_exists, $post ) {
			// remove posts which have same title and slug
			global $wpdb;

			$post_title = wp_unslash( sanitize_post_field( 'post_title', $post['post_title'], 0, 'db' ) );
			$post_name  = wp_unslash( sanitize_post_field( 'post_name', $post['post_name'], 0, 'db' ) );

			$query  = "SELECT ID FROM $wpdb->posts WHERE 1=1";
			$args   = array();
			$query .= ' AND post_title = %s';
			$args[] = $post_title;
			$query .= ' AND post_name = %s';
			$args[] = $post_name;

			$old_post = (int) $wpdb->get_var( $wpdb->prepare( $query, $args ) );

			if ( $old_post && get_post_type( $old_post ) == $post['post_type'] ) {
				wp_delete_post( $old_post, true );
			}

			return false;
		}

		function import_subpages() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce', false ) ) {
				die();
			}
			if ( ! defined( 'WP_LOAD_IMPORTERS' ) ) {
				define( 'WP_LOAD_IMPORTERS', true ); // we are loading importers
			}
			if ( ! class_exists( 'WP_Importer' ) ) { // if main importer class doesn't exist
				require_once ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			}
			if ( ! class_exists( 'WP_Import' ) ) { // if WP importer doesn't exist
				require_once WOLMART_ADMIN . '/importer/wordpress-importer.php';
			}

			if ( current_user_can( 'manage_options' ) && class_exists( 'WP_Importer' ) && class_exists( 'WP_Import' ) ) { // check for main import class and wp import class
				$process   = ( isset( $_POST['process'] ) && $_POST['process'] ) ? sanitize_text_field( $_POST['process'] ) : 'import_start';
				$builder   = isset( $_REQUEST['builder'] ) ? $_REQUEST['builder'] : '';
				$demo_path = $this->get_demo_file( 'subpages' );

				$importer  = new WP_Import();
				$theme_xml = $demo_path . '/subpages.xml';
				if ( 'wpb' == $builder ) {
					$theme_xml = $demo_path . '/wpb-subpages.xml';
				}
				$importer->fetch_attachments = true;

				add_filter( 'wp_import_existing_post', array( $this, 'import_override_subpages' ), 11, 2 );
				// ob_start();
				$response = $importer->import( $theme_xml, $process );
				// ob_end_clean();
				if ( 'import_start' == $process && $response ) {
					echo json_encode(
						array(
							'process' => 'importing',
							'count'   => 0,
							'index'   => 0,
							'message' => 'success',
						)
					);
				}
				wolmart_call_clean_filter( 'wp_import_existing_post', array( $this, 'import_override_subpages' ), 11 );
				flush_rewrite_rules();
				die();
			}
		}

		function import_options() {
			if ( ! check_ajax_referer( 'wolmart_setup_wizard_nonce', 'wpnonce' ) ) {
				die();
			}
			if ( current_user_can( 'manage_options' ) ) {
				do_action( 'wolmart_importer_before_import_options' );

				$demo_path = $this->get_demo_file();
				ob_start();
				include $demo_path . '/theme_options.php';
				$options = ob_get_clean();

				ob_start();
				$options = str_replace( 'IMPORT_SITE_URL', get_home_url(), $options );
				$options = json_decode( $options, true );
				if ( ! isset( $options['theme'] ) || ! isset( $options['sidebars'] ) ) {
					die();
				}

				ob_clean();
				ob_end_clean();
				echo 'success';
				try {
					update_option( 'wolmart_sidebars', $options['sidebars'] );
					wolmart_import_theme_options( false, $options['theme'] );
				} catch ( Exception $e ) {
					esc_html_e( 'Please compile default css files by publishing options in customize panel.', 'wolmart' );
				}

				// after setup, compile dynamic css vars.
				set_transient( 'wolmart_clean_after_setup', 1 );
			}

			die();
		}

		private function get_post_id_from_imported_id( $import_id, $demo ) {
			global $wpdb;
			$result = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = '_wolmart_demo' AND meta_value = %s LIMIT 1", sanitize_title( $demo ) . '#' . sanitize_title( $import_id ) ) );
			if ( $result ) {
				return array(
					'id'    => (int) $result,
					'title' => '',
				);
			}
			return false;
		}

		// Parsing Widgets Function
		// Reference: http://wordpress.org/plugins/widget-settings-importexport/
		private function import_widget_data( $widget_data ) {
			$json_data = $widget_data;
			$json_data = json_decode( $json_data, true );

			$sidebar_data = $json_data[0];
			$widget_data  = $json_data[1];

			foreach ( $widget_data as $widget_data_title => $widget_data_value ) {
				$widgets[ $widget_data_title ] = array();
				foreach ( $widget_data_value as $widget_data_key => $widget_data_array ) {
					if ( is_int( $widget_data_key ) ) {
						$widgets[ $widget_data_title ][ $widget_data_key ] = 'on';
					}
				}
			}
			unset( $widgets[''] );

			foreach ( $sidebar_data as $title => $sidebar ) {
				$count = count( $sidebar );
				for ( $i = 0; $i < $count; $i++ ) {
					$widget               = array();
					$widget['type']       = trim( substr( $sidebar[ $i ], 0, strrpos( $sidebar[ $i ], '-' ) ) );
					$widget['type-index'] = trim( substr( $sidebar[ $i ], strrpos( $sidebar[ $i ], '-' ) + 1 ) );
					if ( ! isset( $widgets[ $widget['type'] ][ $widget['type-index'] ] ) ) {
						unset( $sidebar_data[ $title ][ $i ] );
					}
				}
				$sidebar_data[ $title ] = array_values( $sidebar_data[ $title ] );
			}

			foreach ( $widgets as $widget_title => $widget_value ) {
				foreach ( $widget_value as $widget_key => $widget_value ) {
					$widgets[ $widget_title ][ $widget_key ] = $widget_data[ $widget_title ][ $widget_key ];
				}
			}

			$sidebar_data = array( array_filter( $sidebar_data ), $widgets );
			$this->parse_import_data( $sidebar_data );
		}
		private function parse_import_data( $import_array ) {
			global $wp_registered_sidebars;
			$sidebars_data    = $import_array[0];
			$widget_data      = $import_array[1];
			$current_sidebars = get_option( 'sidebars_widgets' );
			$new_widgets      = array();

			foreach ( $sidebars_data as $import_sidebar => $import_widgets ) :

				foreach ( $import_widgets as $import_widget ) :
					// if the sidebar exists
					if ( isset( $wp_registered_sidebars[ $import_sidebar ] ) ) :
						$title               = trim( substr( $import_widget, 0, strrpos( $import_widget, '-' ) ) );
						$index               = trim( substr( $import_widget, strrpos( $import_widget, '-' ) + 1 ) );
						$current_widget_data = get_option( 'widget_' . $title );
						$new_widget_name     = $this->get_new_widget_name( $title, $index );
						$new_index           = trim( substr( $new_widget_name, strrpos( $new_widget_name, '-' ) + 1 ) );

						if ( ! empty( $new_widgets[ $title ] ) && is_array( $new_widgets[ $title ] ) ) {
							while ( array_key_exists( $new_index, $new_widgets[ $title ] ) ) {
								++$new_index;
							}
						}
						$current_sidebars[ $import_sidebar ][] = $title . '-' . $new_index;
						if ( array_key_exists( $title, $new_widgets ) ) {
							$new_widgets[ $title ][ $new_index ] = $widget_data[ $title ][ $index ];
							$multiwidget                         = $new_widgets[ $title ]['_multiwidget'];
							unset( $new_widgets[ $title ]['_multiwidget'] );
							$new_widgets[ $title ]['_multiwidget'] = $multiwidget;
						} else {
							$current_widget_data[ $new_index ] = $widget_data[ $title ][ $index ];
							$current_multiwidget               = ( isset( $current_widget_data['_multiwidget'] ) ) ? $current_widget_data['_multiwidget'] : '';
							$new_multiwidget                   = isset( $widget_data[ $title ]['_multiwidget'] ) ? $widget_data[ $title ]['_multiwidget'] : false;
							$multiwidget                       = ( $current_multiwidget != $new_multiwidget ) ? $current_multiwidget : 1;
							unset( $current_widget_data['_multiwidget'] );
							$current_widget_data['_multiwidget'] = $multiwidget;
							$new_widgets[ $title ]               = $current_widget_data;
						}

					endif;
				endforeach;
			endforeach;

			if ( isset( $new_widgets ) && isset( $current_sidebars ) ) {
				update_option( 'sidebars_widgets', $current_sidebars );

				foreach ( $new_widgets as $title => $content ) {
					update_option( 'widget_' . $title, $content );
				}

				return true;
			}

			return false;
		}
		private function get_new_widget_name( $widget_name, $widget_index ) {
			$current_sidebars = get_option( 'sidebars_widgets' );
			$all_widget_array = array();
			foreach ( $current_sidebars as $sidebar => $widgets ) {
				if ( ! empty( $widgets ) && is_array( $widgets ) && 'wp_inactive_widgets' != $sidebar ) {
					foreach ( $widgets as $widget ) {
						$all_widget_array[] = $widget;
					}
				}
			}
			while ( in_array( $widget_name . '-' . $widget_index, $all_widget_array ) ) {
				++$widget_index;
			}
			$new_widget_name = $widget_name . '-' . $widget_index;
			return $new_widget_name;
		}
		private function importer_get_page_by_title( $page_title, $output = OBJECT ) {
			global $wpdb;
			$page = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM $wpdb->posts LEFT JOIN $wpdb->postmeta ON ( $wpdb->posts.ID = $wpdb->postmeta.post_id and $wpdb->postmeta.meta_key = %s ) WHERE $wpdb->posts.post_title = %s AND $wpdb->posts.post_type = %s order by $wpdb->postmeta.meta_value desc limit 1", 'wolmart_imported_date', $page_title, 'page' ) );

			if ( $page ) {
				return get_post( $page, $output );
			}
		}
		private function import_before_functions( $demo ) {

			if ( defined( 'VCV_VERSION' ) ) {
				update_option( 'vcv-post-types', array( 'post', 'page', 'wolmart_template' ) );
			}

			global $wpdb;
			// Remove demo history records from postmeta and termemta table
			$wpdb->delete( $wpdb->postmeta, array( 'meta_key' => '_wolmart_demo' ) );
			$wpdb->delete( $wpdb->termmeta, array( 'meta_key' => '_wolmart_demo' ) );
		}

		private function import_after_functions( $demo ) {
			delete_option( 'wolmart_import_processed_duplicates' );

			foreach ( $this->woopages as $woo_page_name => $woo_page_title ) {
				$woopage = wolmart_get_page_by_title( $woo_page_title );
				if ( isset( $woopage ) && $woopage->ID ) {
					update_option( $woo_page_name, $woopage->ID ); // Front Page
				}
			}

			// We no longer need to install pages
			$notices = array_diff( get_option( 'woocommerce_admin_notices', array() ), array( 'install', 'update' ) );
			update_option( 'woocommerce_admin_notices', $notices );
			delete_option( '_wc_needs_pages' );
			delete_transient( '_wc_activation_redirect' );

			// Set reading options
			$homepage   = $this->importer_get_page_by_title( 'Home' );
			$shop_page  = $this->importer_get_page_by_title( 'Shop' );
			$posts_page = $this->importer_get_page_by_title( 'Blog' );

			if ( ( $homepage && $homepage->ID ) || ( $shop_page && $shop_page->ID ) || ( $posts_page && $posts_page->ID ) ) {
				update_option( 'show_on_front', 'page' );
				if ( $homepage && $homepage->ID ) {
					update_option( 'page_on_front', $homepage->ID ); // Front Page
				} elseif ( $shop_page && $shop_page->ID ) {
					update_option( 'page_on_front', $shop_page->ID ); // Shop Page
				}
				if ( $posts_page && $posts_page->ID ) {
					update_option( 'page_for_posts', $posts_page->ID ); // Blog Page
				}
			}

			update_option( 'permalink_structure', '/%year%/%monthnum%/%day%/%postname%/' );

			/**
			 * Update imported IDs
			 */
			$this->before_replacement();

			// Logo
			global $import_logo, $wolmart_import_posts_map;
			if ( ! empty( $import_logo ) ) {
				$new_id = $import_logo;
				if ( isset( $wolmart_import_posts_map[ $import_logo ] ) ) {
					$new_id = $wolmart_import_posts_map[ $import_logo ];
				}
				set_theme_mod( 'custom_logo', $new_id );
			}

			// Theme Options / Update blocks_menu imported id
			$data = wolmart_get_option( '_wolmart_blocks_menu' );
			if ( $data ) {
				$data = preg_replace_callback( '|(\\\")(\d+)(\\\":)|', array( $this, 'replace_term_ids' ), json_encode( $data ) );
				set_theme_mod( '_wolmart_blocks_menu', json_decode( $data, true ) );
			}

			// update post ids in pages
			$args = array(
				'posts_per_page' => -1,
				'post_type'      => array( 'page', 'wolmart_template' ),
				'post_status'    => 'publish',
				'meta_query'     => array(
					'relation' => 'AND',
					array(
						'meta_key' => '_wolmart_demo',
						'compare'  => 'EXISTS',
					),
				),
			);

			// Update id for Visual Composer posts and wpb posts
			if ( 0 === strpos( $demo, 'vc-' ) || 0 === strpos( $demo, 'wpb-' ) ) {
				$is_vc_demo = ( 0 === strpos( $demo, 'vc-' ) );
				if ( $is_vc_demo ) {
					$args['meta_query'][] = array(
						'meta_key' => 'vcv-pageContent',
						'compare'  => 'EXISTS',
					);
				}
				$post_query = new WP_Query( $args );
				if ( $post_query->have_posts() ) {
					foreach ( $post_query->posts as $post ) {
						$new_content = $post->post_content;
						$new_content = preg_replace_callback( '|(id=")(\d+)(")|', array( $this, 'replace_post_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(category_ids=")([^"]*)(")|', array( $this, 'replace_term_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(product_ids=")([^"]*)(")|', array( $this, 'replace_post_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(image=")(\d+)(")|', array( $this, 'replace_post_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(images=")([^"]*)(")|', array( $this, 'replace_post_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(product_category_ids=")([^"]*)(")|', array( $this, 'replace_term_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(categories=")([^"]*)(")|', array( $this, 'replace_term_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(menu_id=")(\d+)(")|', array( $this, 'replace_term_ids' ), $new_content );
						$new_content = preg_replace_callback( '|(nav_menu=")(\d+)(")|', array( $this, 'replace_term_ids' ), $new_content );
						if ( $post->post_content != $new_content ) {
							$post->post_content = $new_content;
							wp_update_post( $post );
						}
						if ( $is_vc_demo ) {
							$data = rawurldecode( get_post_meta( get_the_ID(), 'vcv-pageContent', true ) );
							if ( $data ) {
								$data = preg_replace_callback( '|(\"nav_menu\":\")(\d+)(\")|', array( $this, 'replace_term_ids' ), $data );
								$data = preg_replace_callback( '|(\"menuId\":\[\")(\d+)(\"\])|', array( $this, 'replace_term_ids' ), $data );
								$data = preg_replace_callback( '|(\"categories\":\[)([^\]]*)(\])|', array( $this, 'replace_term_ids' ), $data );
								$data = preg_replace_callback( '|(\"ids\":\[)([^\]]*)(\])|', array( $this, 'replace_post_ids' ), $data );
								$data = preg_replace_callback( '|(\"category_ids\":\[)([^\]]*)(\])|', array( $this, 'replace_term_ids' ), $data );
								update_metadata( 'post', get_the_ID(), 'vcv-pageContent', rawurlencode( $data ) );
							}
						}
					}
				}
			} else {
				// Update id for Elementor posts
				$args['meta_query'][] = array(
					'meta_key' => '_elementor_data',
					'compare'  => 'EXISTS',
				);
				$post_query           = new WP_Query( $args );
				if ( $post_query->have_posts() ) {
					foreach ( $post_query->posts as $post ) {
						$data = get_post_meta( $post->ID, '_elementor_data', true );
						$data = preg_replace_callback( '|(id=\")(\d+)(\")|', array( $this, 'replace_post_ids' ), $data );
						$data = preg_replace_callback( '|(\"id\":\")(\d+)(\")|', array( $this, 'replace_post_ids' ), $data );
						$data = preg_replace_callback( '|(\"menu_id\":\")(\d+)(\")|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"category_ids\":\")([^\"]*)(\")|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"category_ids\":\[)([^\]]*)(\])|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"product_ids\":\")([^\"]*)(\")|', array( $this, 'replace_post_ids' ), $data );
						$data = preg_replace_callback( '|(\"product_ids\":\[)([^\]]*)(\])|', array( $this, 'replace_post_ids' ), $data );
						$data = preg_replace_callback( '|(\"categories\":\[)([^\]]*)(\])|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"product_category_ids\":\[)([^\]]*)(\])|', array( $this, 'replace_term_ids' ), $data );
						$data = preg_replace_callback( '|(\"nav_menu\":\")(\d+)(\")|', array( $this, 'replace_term_ids' ), $data );

						update_post_meta( $post->ID, '_elementor_data', wp_slash( $data ) );

						delete_post_meta( $post->ID, '_elementor_page_assets' );
					}
				}
			}

			/* Menu Item*/
			$menu_query = new WP_Query(
				array(
					'posts_per_page' => -1,
					'post_type'      => array( 'nav_menu_item' ),
					'post_status'    => 'publish',
					'meta_query'     => array(
						'relation' => 'AND',
						array(
							'meta_key' => '_menu_item_block',
							'compare'  => 'EXISTS',
						),
					),
				)
			);
			if ( $menu_query->have_posts() ) {
				foreach ( $menu_query->posts as $menu_item ) {
					$menu_item_block = get_post_meta( $menu_item->ID, '_menu_item_block', true );
					if ( isset( $wolmart_import_posts_map[ $menu_item_block ] ) ) {
						update_post_meta( $menu_item->ID, '_menu_item_block', $wolmart_import_posts_map[ $menu_item_block ] );
					}
				}
			}

			// Term Thumbnail Updates
			global $wolmart_import_terms_map, $wolmart_import_posts_map;

			error_log( json_encode( $wolmart_import_terms_map ) );

			error_log( json_encode( $wolmart_import_posts_map ) );

			foreach ( $wolmart_import_terms_map as $new_term_id ) {
				$old_thumbnail_id = get_term_meta( $new_term_id, 'thumbnail_id', true );

				if ( $old_thumbnail_id && isset( $wolmart_import_posts_map[ $old_thumbnail_id ] ) ) {
					update_term_meta( $new_term_id, 'thumbnail_id', $wolmart_import_posts_map[ $old_thumbnail_id ] );
				}

				// For Brand Thumbnail Images
				$old_brand_thumbnail_id = get_term_meta( $new_term_id, 'brand_thumbnail_id', true );

				if ( $old_brand_thumbnail_id && isset( $wolmart_import_posts_map[ $old_brand_thumbnail_id ] ) ) {
					update_term_meta( $new_term_id, 'brand_thumbnail_id', $wolmart_import_posts_map[ $old_brand_thumbnail_id ] );
					update_term_meta( $new_term_id, 'thumbnail_id', $wolmart_import_posts_map[ $old_brand_thumbnail_id ] );
				}
			}

			// Set visual composer options
			if ( 0 === strpos( $demo, 'vc-' ) ) {
				if ( defined( 'VCV_VERSION' ) ) {
					$options_helper = vchelper( 'Options' );
					$options_helper->deleteTransient( 'vcv:settings:factoryReset:allow' );
					$options_helper->set( 'settingsResetInitiated', time() );
					vcevent( 'vcv:system:factory:reset' );
					wp_cache_flush();
				}
			} else {
				// Set elementor options
				if ( defined( 'ELEMENTOR_VERSION' ) ) {
					$elementor_cpt_support = get_option( 'elementor_cpt_support' );
					if ( empty( $elementor_cpt_support ) ) {
						$elementor_cpt_support = array( 'post', 'page' );
					}
					$elementor_cpt_support[] = 'wolmart_template';
					update_option( 'elementor_cpt_support', $elementor_cpt_support );
					update_option( 'elementor_disable_color_schemes', 'yes' );
					update_option( 'elementor_disable_typography_schemes', 'yes' );

					// after setup, set elementor options
					set_transient( 'wolmart_clean_after_setup_e', 3 );

					// Disable Elementor Gutenberg Optimization
					if ( version_compare( ELEMENTOR_VERSION, '3.17.0' ) >= 0 ) {
						update_option( \Elementor\Plugin::$instance->experiments->get_feature_option_key( 'block_editor_assets_optimize' ), \Elementor\Plugin::$instance->experiments::STATE_INACTIVE );
					}
				}
			}

			// update conditions
			$all_conditions = wolmart_get_option( 'conditions' );
			if ( ! empty( $all_conditions ) ) {
				$option_keys = array(
					'popup',
					'top_bar',
					'header',
					'footer',
					'ptb',
					'top_block',
					'bottom_block',
					'inner_top_block',
					'inner_bottom_block',
				);

				if ( $all_conditions && is_array( $all_conditions ) ) {
					foreach ( $all_conditions as $category => $conditions ) {
						if ( is_array( $conditions ) ) {
							foreach ( $conditions as $condition_no => $condition ) {
								if ( ! empty( $condition['options'] ) ) {
									$options = $condition['options'];
									if ( is_array( $options ) ) {
										foreach ( $option_keys as $key ) {
											if ( ! empty( $options[ $key ] ) && 'hide' != $options[ $key ] && (int) $options[ $key ] ) {
												$old_post_id = (int) $options[ $key ];
												if ( ! empty( $wolmart_import_posts_map[ $old_post_id ] ) ) {
													$all_conditions[ $category ][ $condition_no ]['options'][ $key ] = $wolmart_import_posts_map[ $old_post_id ];
												}
											}
										}
									}
								}

								if ( ! empty( $condition['scheme'] ) ) {
									$scheme = $condition['scheme'];
									if ( is_array( $scheme ) && empty( $scheme['all'] ) ) {
										foreach ( $scheme as $scheme_key => $scheme_data ) {
											if ( post_type_exists( $scheme_key ) && is_array( $scheme_data ) ) {
												// Posts array
												foreach ( $scheme_data as $i => $post_id ) {
													if ( $post_id && ! empty( $wolmart_import_posts_map[ (int) $post_id ] ) ) {
														$all_conditions[ $category ][ $condition_no ]['scheme'][ $scheme_key ][ $i ] = $wolmart_import_posts_map[ (int) $post_id ];
													}
												}
											} elseif ( 'category' == $scheme_key || 'post_tag' == $scheme_key || taxonomy_exists( $scheme_key ) ) {
												// Terms array
												foreach ( $scheme_data as $i => $term_id ) {
													if ( $term_id && isset( $wolmart_import_terms_map[ (int) $term_id ] ) ) {
														$all_conditions[ $category ][ $condition_no ]['scheme'][ $scheme_key ][ $i ] = $wolmart_import_terms_map[ (int) $term_id ];
													}
												}
											}
										}
									}
								}
							}
						}
					}

					$GLOBALS['wolmart_option']['conditions'] = $all_conditions;
					set_theme_mod( 'conditions', $all_conditions );
				}
			}

			// Clear all woocommerce caches
			if ( class_exists( 'WooCommerce' ) ) {
				wc_update_product_lookup_tables();

				wc_delete_product_transients();
				wc_delete_shop_order_transients();
				delete_transient( 'wc_count_comments' );
				delete_transient( 'as_comment_count' );

				$attribute_taxonomies = wc_get_attribute_taxonomies();

				if ( $attribute_taxonomies ) {
					foreach ( $attribute_taxonomies as $attribute ) {
						delete_transient( 'wc_layered_nav_counts_pa_' . $attribute->attribute_name );
					}
				}

				WC_Cache_Helper::get_transient_version( 'shipping', true );

				wc_delete_expired_transients();

				wc_clear_template_cache();
			}

			if ( class_exists( 'YITH_WCWL' ) ) {
				$wishlist = $this->importer_get_page_by_title( 'Wishlist' );
				if ( $wishlist && $wishlist->ID ) {
					update_option( 'yith-wcwl-page-id', $wishlist->ID );
				}
				update_option( 'yith_wcwl_variation_show', 'no' );
				update_option( 'yith_wcwl_price_show', 'yes' );
				update_option( 'yith_wcwl_stock_show', 'yes' );
				update_option( 'yith_wcwl_show_dateadded', 'no' );
				update_option( 'yith_wcwl_add_to_cart_show', 'yes' );
				update_option( 'yith_wcwl_show_remove', 'yes' );
				update_option( 'yith_wcwl_repeat_remove_button', 'no' );
			}

			// replace term thumbnail ids with changed post ids
			$this->replace_term_thumbnail_ids();
			// Demo imported!
			do_action( 'wolmart_demo_imported', -1, true );
			flush_rewrite_rules();
		}

		private function before_replacement() {
			global $wpdb, $wolmart_import_terms_map, $wolmart_import_posts_map;

			$wolmart_import_posts_map = array();

			$posts_result = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wolmart_demo'" );
			foreach ( $posts_result as $result ) {
				$data = explode( '#', $result->meta_value );
				if ( 2 == count( $data ) ) {
					if ( $this->demo == $data[0] ) {
						$wolmart_import_posts_map[ (int) $data[1] ] = (int) $result->post_id;
					}
				}
			}

			$wolmart_import_terms_map = array();

			$terms_result = $wpdb->get_results( "SELECT term_id, meta_value FROM {$wpdb->termmeta} WHERE meta_key = '_wolmart_demo'" );
			foreach ( $terms_result as $result ) {
				$data = explode( '#', $result->meta_value );
				if ( 2 == count( $data ) ) {
					if ( $this->demo == $data[0] ) {
						$wolmart_import_terms_map[ (int) $data[1] ] = (int) $result->term_id;
					}
				}
			}
		}

		public function replace_post_ids( $matches ) {
			global $wolmart_import_posts_map;
			$ids     = array_map( 'intval', explode( ',', str_replace( '"', '', $matches[2] ) ) );
			$new_ids = array();
			foreach ( $ids as $id ) {
				if ( ! empty( $wolmart_import_posts_map[ $id ] ) ) {
					$new_ids[] = $wolmart_import_posts_map[ $id ];
				} elseif ( $id ) {
					$new_ids[] = $id;
				}
			}
			return $matches[1] . implode( ',', $new_ids ) . $matches[3];
		}

		public function replace_term_ids( $matches ) {
			global $wolmart_import_terms_map;
			$ids     = array_map( 'intval', explode( ',', str_replace( '"', '', $matches[2] ) ) );
			$new_ids = array();
			foreach ( $ids as $id ) {
				if ( ! empty( $wolmart_import_terms_map[ $id ] ) ) {
					$new_ids[] = $wolmart_import_terms_map[ $id ];
				} elseif ( $id ) {
					$new_ids[] = $id;
				}
			}
			return $matches[1] . implode( ',', $new_ids ) . $matches[3];
		}

		public function replace_term_thumbnail_ids() {
			global $wolmart_import_terms_map, $wolmart_import_posts_map;
			if ( ! empty( $wolmart_import_terms_map ) && ! empty( $wolmart_import_posts_map ) ) {
				foreach ( $wolmart_import_terms_map as $old_id => $new_id ) {
					$old_post_id = get_term_meta( $new_id, 'thumbnail_id', true );
					if ( $old_post_id && isset( $wolmart_import_posts_map[ $old_post_id ] ) ) {
						update_term_meta( $new_id, 'thumbnail_id', $wolmart_import_posts_map[ $old_post_id ] );
					}
				}
			}
		}

		public function clean_after_import() {

			// Compile Theme Style
			require_once WOLMART_ADMIN . '/customizer/customizer.php';
			require_once WOLMART_ADMIN . '/customizer/dynamic/dynamic-color-lib.php';
			require_once WOLMART_ADMIN . '/customizer/customizer-function.php';

			Wolmart_Customizer::get_instance()->save_theme_options();

			// Prevent lazyload menu, merge stylesheets, Critical CSS
			set_theme_mod( 'lazyload_menu', false );
			set_theme_mod( 'resource_merge_stylesheets', false );
			set_theme_mod( 'resource_critical_css', false );

			$about_us        = $this->importer_get_page_by_title( 'About Us' );
			$contact_us      = $this->importer_get_page_by_title( 'Contact Us' );
			$become_a_vendor = $this->importer_get_page_by_title( 'Become a Vendor' );
			$faqs            = $this->importer_get_page_by_title( 'FAQs' );
			$coming_soon     = $this->importer_get_page_by_title( 'Coming Soon' );
			$compare         = $this->importer_get_page_by_title( 'Compare' );

			// Add coming soon layout to layout builder.
			$all_conditions = wolmart_get_option( 'conditions' );
			if ( $coming_soon ) {
				$new_condition = array(
					'title'   => 'Coming Soon',
					'scheme'  => array(
						'page' => array( $coming_soon->ID ),
					),
					'options' => array(
						'wrap'            => 'full',
						'footer'          => 'hide',
						'header'          => 'hide',
						'ptb'             => 'hide',
						'show_breadcrumb' => 'no',
					),
				);

				if ( empty( $all_conditions['single_page'] ) ) {
					$all_conditions['single_page'] = array( $new_condition );

				} elseif ( is_array( $all_conditions['single_page'] ) ) {
					$found = false;
					foreach ( $all_conditions['single_page'] as $condition ) {
						if ( ! empty( $condition['scheme'] ) && is_array( $condition['scheme']['page'] ) && in_array( $coming_soon->ID, $condition['scheme']['page'] ) ) {
							$found = true;
						}
					}
					if ( ! $found ) {
						$all_conditions['single_page'][] = $new_condition;
					}
				}
				$GLOBALS['wolmart_option']['conditions'] = $all_conditions;
				set_theme_mod( 'conditions', $all_conditions );
			}

			// Add fullwidth pages layout to layout builder.
			$fullwidth_pages = array();
			if ( $about_us ) {
				$fullwidth_pages[] = $about_us->ID;
			}
			if ( $contact_us ) {
				$fullwidth_pages[] = $contact_us->ID;
			}
			if ( $become_a_vendor ) {
				$fullwidth_pages[] = $become_a_vendor->ID;
			}
			if ( count( $fullwidth_pages ) ) {
				$new_condition = array(
					'title'   => 'Fullwidth Pages',
					'scheme'  => array(
						'page' => $fullwidth_pages,
					),
					'options' => array(
						'wrap' => 'full',
					),
				);

				if ( empty( $all_conditions['single_page'] ) ) {
					$all_conditions['single_page'] = array( $new_condition );

				} elseif ( is_array( $all_conditions['single_page'] ) ) {
					$found = false;
					foreach ( $all_conditions['single_page'] as $condition ) {
						if ( ! empty( $condition['scheme'] ) && is_array( $condition['scheme']['page'] ) && in_array( $about_us->ID, $condition['scheme']['page'] ) ) {
							$found = true;
						}
					}
					if ( ! $found ) {
						$all_conditions['single_page'][] = $new_condition;
					}
				}
				$GLOBALS['wolmart_option']['conditions'] = $all_conditions;
				set_theme_mod( 'conditions', $all_conditions );
			}

			// Update compate page options
			if ( $compare ) {
				update_option( 'woocommerce_compare_page_id', $compare->ID );
			}

			// Update custom links in menu items.
			global $wpdb;
			$menu_items = $wpdb->get_results( $wpdb->prepare( "SELECT posts.ID FROM $wpdb->posts AS posts JOIN $wpdb->postmeta AS meta ON ( posts.ID = meta.post_id and meta.meta_key = %s and meta.meta_value = %s )", '_menu_item_type', 'custom' ) );
			if ( ! empty( $menu_items ) ) {
				foreach ( $menu_items as $item ) {
					$custom_menu = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM $wpdb->postmeta WHERE post_id=%s AND meta_key=%s", $item->ID, '_menu_item_url' ) );
					$custom_menu = $custom_menu[0];
					$post_id     = -1;
					if ( $contact_us && preg_match( '/(contact-us)/', $custom_menu->meta_value ) ) {
						$post_id = $contact_us->ID;
					}
					if ( $become_a_vendor && preg_match( '/(become-a-vendor)/', $custom_menu->meta_value ) ) {
						$post_id = $become_a_vendor->ID;
					}
					if ( $faqs && preg_match( '/(faq)/', $custom_menu->meta_value ) ) {
						$post_id = $faqs->ID;
					}
					if ( $about_us && preg_match( '/(about-us)/', $custom_menu->meta_value ) ) {
						$post_id = $about_us->ID;
					}
					if ( $coming_soon && preg_match( '/(coming-soon)/', $custom_menu->meta_value ) ) {
						$post_id = $coming_soon->ID;
					}

					if ( $post_id > 0 ) {
						update_post_meta( $item->ID, '_menu_item_url', get_permalink( $post_id ) );
					}
				}
			}

			delete_transient( 'wolmart_clean_after_setup' );
		}

		public function wolmart_demo_types() {
			return array(
				'demo-29'    => array(
					'alt'     => 'Demo 29',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-29.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist', 'alpus-flexbox' ),
					'editors' => array( 'elementor' ),
				),
				'demo-28'    => array(
					'alt'     => 'Demo 28',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-28.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-27'    => array(
					'alt'     => 'Demo 27',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-27.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-26'    => array(
					'alt'     => 'Demo 26',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-26.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-25'    => array(
					'alt'     => 'Demo 25',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-25.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-1'     => array(
					'alt'     => 'Demo 1',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-1.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-2'     => array(
					'alt'     => 'Demo 2',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-2.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-3'     => array(
					'alt'     => 'Demo 3',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-3.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-4'     => array(
					'alt'     => 'Demo 4',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-4.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-5'     => array(
					'alt'     => 'Demo 5',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-5.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-6'     => array(
					'alt'     => 'Demo 6',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-6.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-7'     => array(
					'alt'     => 'Demo 7',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-7.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-8'     => array(
					'alt'     => 'Demo 8',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-8.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-9'     => array(
					'alt'     => 'Demo 9',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-9.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-10'    => array(
					'alt'     => 'Demo 10',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-10.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-11'    => array(
					'alt'     => 'Demo 11',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-11.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-12'    => array(
					'alt'     => 'Demo 12',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-12.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-13'    => array(
					'alt'     => 'Demo 13',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-13.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-14'    => array(
					'alt'     => 'Demo 14',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-14.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-15'    => array(
					'alt'     => 'Demo 15',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-15.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor', 'js_composer' ),
				),
				'demo-16'    => array(
					'alt'     => 'Demo 16',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-16.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-17'    => array(
					'alt'     => 'Demo 17',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-17.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-18'    => array(
					'alt'     => 'Demo 18',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-18.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-19'    => array(
					'alt'     => 'Demo 19',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-19.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-20'    => array(
					'alt'     => 'Demo 20',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-20.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-21'    => array(
					'alt'     => 'Demo 21',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-21.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-22'    => array(
					'alt'     => 'Demo 22',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-22.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-23'    => array(
					'alt'     => 'Demo 23',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-23.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'demo-24'    => array(
					'alt'     => 'Demo 24',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/demo-24.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
				'rtl-demo-1' => array(
					'alt'     => 'RTL Demo 1',
					'img'     => WOLMART_ADMIN_URI . '/setup-wizard/images/rtl-demo-1.jpg',
					'filter'  => 'market',
					'plugins' => array( 'woocommerce', 'yith-woocommerce-wishlist' ),
					'editors' => array( 'elementor' ),
				),
			);
		}
	}
endif;

add_action( 'after_setup_theme', 'wolmart_theme_setup_wizard', 10 );

if ( ! function_exists( 'wolmart_theme_setup_wizard' ) ) :
	function wolmart_theme_setup_wizard() {
		$instance = Wolmart_Setup_Wizard::get_instance();
	}
endif;

if ( ! function_exists( 'wolmart_import_theme_options' ) ) {
	function wolmart_import_theme_options( $plugin_options, $imported_options ) {
		update_option( 'theme_mods_' . get_option( 'stylesheet' ), $imported_options );

		// Reset wolmart_option
		unset( $GLOBALS['wolmart_option'] );
		wolmart_get_option( '' );
	}
}
