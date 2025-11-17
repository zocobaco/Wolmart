<?php
/**
 * Wolmart Version_Control
 *
 * @package Wolmart WordPress Framework
 *
 * @since 1.2.0
 * @author @ndy
 */

// Direct access is denied.
defined( 'ABSPATH' ) || die;

/**
 * Wolmart Version Control
 *
 * @since 1.2.0
 */
class Wolmart_Version_Control extends Wolmart_Base {

	/**
	 * Instance
	 *
	 * @since 1.2.0
	 * @access protected
	 * @var $instance
	 */
	protected static $instance;

	/**
	 * Page slug
	 *
	 * @since 1.2.0
	 * @access public
	 * @var $page_slug
	 */
	public $page_slug = 'wolmart-version-control';

	/**
	 * Result
	 *
	 * @since 1.2.0
	 * @access private
	 * @var $result
	 */
	private $result;

	/**
	 * Theme Versions
	 *
	 * @since 1.2.0
	 * @access public
	 * @var $theme_version
	 */
	public $theme_versions = array();

	/**
	 * Plugin Versions
	 *
	 * @since 1.2.0
	 * @access public
	 * @var $plugin_versions
	 */
	public $plugin_versions = array();

	/**
	 * Theme URL
	 *
	 * @since 1.2.0
	 * @access public
	 * @var $theme_url
	 */
	public $theme_url = 'https://d-themes.com/wordpress/wolmart';

	/**
	 * Theme Slug
	 *
	 * @since 1.2.0
	 * @access public
	 * @var $theme_slug
	 */
	public $theme_slug = 'wolmart';

	/**
	 * Constructor
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		add_action( 'wp_ajax_wolmart_modify_theme_auto_updates', array( $this, 'wolmart_modify_theme_auto_updates' ) );
		add_action( 'wp_ajax_wolmart_modify_plugin_auto_updates', array( $this, 'wolmart_modify_plugin_auto_updates' ) );

		add_filter( 'site_transient_update_themes', array( $this, 'wolmart_check_for_update_theme' ), 1 );
		add_filter( 'site_transient_update_plugins', array( $this, 'wolmart_check_for_update_plugin' ), 1 );

		if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || $this->page_slug != $_GET['page'] ) {
			return;
		}

		$this->init();
	}

	/**
	 * Initialize
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function init() {
		$this->theme_versions  = $this->get_theme_versions();
		$this->plugin_versions = $this->get_plugin_versions();

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ), 30 );
	}

	/**
	 * Enqueue Styles & Scripts
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function enqueue() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';

		wp_enqueue_script( 'updates' );
		wp_enqueue_script( 'wolmart-admin-wizard', WOLMART_ADMIN_URI . '/panel/wizard' . $suffix, array( 'jquery-core' ), true, 50 );

		wp_localize_script(
			'wolmart-admin-wizard',
			'wolmart_version_control_params',
			array(
				'wpnonce' => wp_create_nonce( 'wolmart_version_control_nonce' ),
			)
		);
	}

	/**
	 * Get Theme Versions
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return array versions
	 */
	public function get_theme_versions() {
		$rollback_versions = get_site_transient( 'wolmart_theme_rollback_versions' );

		if ( false === $rollback_versions ) {
			$max_version   = 20;
			$current_index = 0;

			require_once WOLMART_ADMIN . '/importer/importer-api.php';
			$importer_api = new Wolmart_Importer_API();

			$versions = $importer_api->get_response( 'theme_rollback_versions' );

			if ( is_wp_error( $versions ) || empty( $versions ) ) {
				return array();
			}

			$rollback_versions = array();

			foreach ( $versions as $version ) {
				if ( $max_version <= $current_index ) {
					break;
				}

				if ( version_compare( $version, WOLMART_VERSION, '>=' ) ) {
					continue;
				}

				$current_index ++;
				$rollback_versions[] = $version;
			}

			if ( ! empty( $rollback_versions ) ) {
				set_site_transient( 'wolmart_theme_rollback_versions', $rollback_versions, WEEK_IN_SECONDS );
			}
		}

		return $rollback_versions;
	}

	/**
	 * Get Plugin Versions
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @return array plugin versions
	 */
	public function get_plugin_versions() {
		$rollback_versions = get_site_transient( 'wolmart_plugin_rollback_versions' );

		if ( false === $rollback_versions ) {
			$max_version   = 20;
			$current_index = 0;

			require_once WOLMART_ADMIN . '/importer/importer-api.php';
			$importer_api = new Wolmart_Importer_API();

			$versions = $importer_api->get_response( 'plugin_rollback_versions' );

			if ( is_wp_error( $versions ) || empty( $versions ) ) {
				return array();
			}

			$rollback_versions = array();

			foreach ( $versions as $version ) {
				if ( $max_version <= $current_index ) {
					break;
				}

				if ( version_compare( $version, WOLMART_CORE_VERSION, '>=' ) ) {
					continue;
				}

				$current_index ++;
				$rollback_versions[] = $version;
			}

			set_site_transient( 'wolmart_plugin_rollback_versions', $rollback_versions, WEEK_IN_SECONDS );
		}

		return $rollback_versions;
	}

	/**
	 * Check for update theme.
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $data themes info.
	 * @return array $data
	 */
	public function wolmart_check_for_update_theme( $data ) {
		$transient_data = get_site_transient( 'wolmart_modify_theme_auto_update' );

		if ( $transient_data && isset( $data->response ) ) {
			$data->response[ get_template() ] = $transient_data;
		}

		return $data;
	}

	/**
	 * Check for update plugin
	 *
	 * @since 1.2.0
	 * @access public
	 *
	 * @param array $data themes info.
	 * @return array $data
	 */
	public function wolmart_check_for_update_plugin( $data ) {
		$transient_data = get_site_transient( 'wolmart_modify_plugin_auto_update' );

		if ( ! empty( $transient_data ) && isset( $data->response ) ) {
			$data->response['wolmart-core/wolmart-core.php'] = json_decode( wp_json_encode( $transient_data ), false );
		}

		return $data;
	}

	/**
	 * Modify theme auto updates
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function wolmart_modify_theme_auto_updates() {
		if ( ! isset( $_REQUEST['wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['wpnonce'], 'wolmart_version_control_nonce' ) ) {
			wp_send_json( false );
			die();
		}

		delete_transient( 'wolmart_modify_theme_auto_update' );

		require_once WOLMART_ADMIN . '/importer/importer-api.php';
		$importer_api = new Wolmart_Importer_API();

		$args            = $importer_api->generate_args( false );
		$version         = isset( $_REQUEST['version'] ) ? wp_unslash( sanitize_text_field( $_REQUEST['version'] ) ) : '';
		$args['version'] = $version;
		$package_url     = add_query_arg( $args, $importer_api->get_url( 'theme_rollback' ) );

		$transient_data = array(
			'theme'           => get_template(),
			'new_version'     => $version,
			'release_version' => $version,
			'url'             => $this->theme_url,
			'package'         => $package_url,
		);

		set_site_transient( 'wolmart_modify_theme_auto_update', $transient_data, WEEK_IN_SECONDS );

		add_filter( 'site_transient_update_themes', array( $this, 'wolmart_check_for_update_theme' ), 1 );

		wp_send_json( true );
		die();
	}

	/**
	 * Modify plugin auto updates
	 *
	 * @since 1.2.0
	 * @access public
	 */
	public function wolmart_modify_plugin_auto_updates() {
		if ( ! isset( $_REQUEST['wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['wpnonce'], 'wolmart_version_control_nonce' ) ) {
			wp_send_json( false );
			die();
		}

		delete_transient( 'wolmart_modify_plugin_auto_update' );

		require_once WOLMART_ADMIN . '/importer/importer-api.php';
		$importer_api = new Wolmart_Importer_API();

		$args            = $importer_api->generate_args( false );
		$version         = isset( $_REQUEST['version'] ) ? wp_unslash( $_REQUEST['version'] ) : '';
		$args['version'] = $version;
		$package_url     = add_query_arg( $args, $importer_api->get_url( 'plugin_rollback' ) );

		$transient_data = array(
			'slug'            => 'wolmart-core',
			'plugin'          => 'wolmart-core/wolmart-core.php',
			'new_version'     => $version,
			'release_version' => $version,
			'url'             => $this->wolmart_url,
			'package'         => $package_url,
		);

		set_site_transient( 'wolmart_modify_plugin_auto_update', $transient_data, WEEK_IN_SECONDS );

		add_filter( 'site_transient_update_plugins', array( $this, 'wolmart_check_for_update_plugin' ), 1 );

		wp_send_json( true );
		die();
	}


	/**
	 * Add Tools to admin menu
	 *
	 * @since 1.0
	 * @access public
	 */
	public function add_admin_menu() {
		add_submenu_page( 'wolmart', esc_html__( 'Version Control', 'wolmart' ), esc_html__( 'Version Control', 'wolmart' ), 'manage_options', $this->page_slug, array( $this, 'view_tools' ), 7 );
	}

	/**
	 * Render tools page
	 *
	 * @since 1.0
	 * @access public
	 */
	public function view_tools() {
		Wolmart_Admin_Panel::get_instance()->view_header( 'version_control' );

		$nonce = wp_create_nonce( 'wolmart-version-control' );
		?>
		<div class="wolmart-admin-panel-header wolmart-row">
			<div class="wolmart-admin-panel-header-inner">
				<h2><?php echo esc_html__( 'Version Control', 'wolmart' ); ?></h2>
				<p><?php echo esc_html__( 'Experiencing an issue with New version? Rollback to a previous version before the issue appeared.', 'wolmart' ); ?></p>
			</div>
		</div>
		<?php
		if ( isset( $this->result ) ) {
			if ( $this->result['success'] ) {
				echo '<div class="wolmart-notify updated inline"><p>' . esc_html( $this->result['message'] ) . '</p></div>';
			} else {
				echo '<div class="wolmart-notify error inline"><p>' . esc_html( $this->result['message'] ) . '</p></div>';
			}
		}
		?>
		<div class="wolmart-admin-panel-body wolmart-card-box wolmart-version-control">
			<table class="wp-list-table widefat" id="wolmart_versions_table">
				<thead>
					<tr>
						<th scope="col" id="title" class="manage-column column-title column-primary"><?php esc_html_e( 'Action Name', 'wolmart' ); ?></th>
						<th scope="col" id="remove" class="manage-column column-remove"><?php esc_html_e( 'Action', 'wolmart' ); ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
					<tr class="theme-version" id="wolmart-theme-version">
						<th>
							<strong class="action-name"><?php echo esc_html__( 'Wolmart Theme', 'wolmart' ); ?></strong>
							<p class="description warning"><?php echo esc_html__( 'Warning: Please backup your database before making the rollback.', 'wolmart' ); ?></p>
						</th>
						<td class="run-tool">
							<select class="version-select theme-versions" id="theme-versions">
								<?php
								foreach ( $this->theme_versions as $version ) {
									?>
										<option value="<?php echo esc_attr( $version ); ?>"><?php echo esc_html( $version ); ?></option>
										<?php
								}
								?>
							</select>
							<a href="#" class="button button-large button-light theme-rollback" role="button"><?php echo esc_html__( 'Downgrade', 'wolmart' ); ?></a>
						</td>
					</tr>
					<tr class="plugin-version" id="wolmart-plugin-version">
						<th>
							<strong class="action-name"><?php echo esc_html__( 'Wolmart Plugin', 'wolmart' ); ?></strong>
							<p class="description warning"><?php echo esc_html__( 'Warning: Please backup your database before making the rollback.', 'wolmart' ); ?></p>
						</th>
						<td class="run-tool">
							<select class="version-select plugin-versions" id="plugin-versions">
								<?php
								foreach ( $this->plugin_versions as $version ) {
									?>
										<option value="<?php echo esc_attr( $version ); ?>"><?php echo esc_html( $version ); ?></option>
										<?php
								}
								?>
							</select>
							<a href="#" class="button button-large button-light plugin-rollback" role="button"><?php echo esc_html__( 'Downgrade', 'wolmart' ); ?></a>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
		<?php
		Wolmart_Admin_Panel::get_instance()->view_footer();
	}
}

Wolmart_Version_Control::get_instance();
