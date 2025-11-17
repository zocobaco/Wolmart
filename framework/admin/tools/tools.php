<?php

// Direct access is denied
defined( 'ABSPATH' ) || die;

/**
 * Wolmart Tools
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

class Wolmart_Tools extends Wolmart_Base {

	/**
	 * Instance
	 *
	 * @since 1.0
	 * @access protected
	 */
	protected static $instance;

	/**
	 * Page slug
	 *
	 * @since 1.0
	 * @access public
	 */
	public $page_slug = 'wolmart-tools';

	/**
	 * Result
	 *
	 * @since 1.0
	 * @access public
	 */
	private $result;
	/**
	 * Constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );

		if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || $this->page_slug != $_GET['page'] ) {
			return;
		}

		$this->handle_request();
	}


	/**
	 * Add Tools to admin menu
	 *
	 * @since 1.0
	 * @access public
	 */
	public function add_admin_menu() {
		add_submenu_page( 'wolmart', esc_html__( 'Tools', 'wolmart' ), esc_html__( 'Tools', 'wolmart' ), 'manage_options', $this->page_slug, array( $this, 'view_tools' ), 5 );
	}

	/**
	 * Handle request to execute tools
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function handle_request() {

		if ( ! current_user_can( 'administrator' ) || ! isset( $_GET['page'] ) || $this->page_slug != $_GET['page'] ) {
			return;
		}

		$tools = $this->get_tools();

		$result_success = true;
		$message        = '';

		if ( ! empty( $_GET['action'] ) ) {
			if ( empty( $_REQUEST['_wpnonce'] ) || ! wp_verify_nonce( $_REQUEST['_wpnonce'], 'wolmart-tools' ) ) {
				wp_die( esc_html__( 'Action failed. Please refresh the page and retry.', 'wolmart' ) );
			}

			$action = wp_unslash( $_GET['action'] ); // WPCS: input var ok.

			if ( array_key_exists( $action, $tools ) ) {
				$this->result = $this->execute_tool( $action );

				$tool = $tools[ $action ];
				$tool = array(
					'id'          => $action,
					'name'        => $tool['action_name'],
					'action'      => $tool['button_text'],
					'description' => $tool['description'],
				);
				$tool = array_merge( $tool, $this->result );

				/**
				 * Fires after a Wolmart tool has been executed.
				 *
				 * @param array  $tool  Details about the tool that has been executed.
				 */
				do_action( 'wolmart_tool_executed', $tool );
			} else {
				$this->result = array(
					'success' => false,
					'message' => __( 'Tool does not exist.', 'wolmart' ),
				);
			}
		}
	}

	/**
	 * Refresh all blocks
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function refresh_blocks() {

	}

	/**
	 * Get available Tools
	 *
	 * @since 1.0
	 * @access public
	 */
	public function get_tools() {
		$tools = array(
			'clear_merge_css_js'      => array(
				'action_name' => __( 'Clear the merged css and js in a file', 'wolmart' ),
				'button_text' => __( 'Clear resources', 'wolmart' ),
				'description' => __( 'This will clear the all combined stylesheets and javascripts.', 'wolmart' ),
			),
			'clear_transients'        => array(
				'action_name' => __( 'Addon transients', 'wolmart' ),
				'button_text' => __( 'Clear transients', 'wolmart' ),
				'description' => __( 'This will clear the Wolmart Addon Features(Brand, Vendor, etc)transients cache.', 'wolmart' ),
			),
			'clear_plugin_transients' => array(
				'action_name' => __( 'Plugin transients', 'wolmart' ),
				'button_text' => __( 'Clear transients', 'wolmart' ),
				'description' => __( 'This tool will clear the plugin(Wolmart Core Plugin, WPBakery Page Builder) update transients cache.', 'wolmart' ),
			),
			'clear_studio_transients' => array(
				'action_name' => __( 'Studio block transients', 'wolmart' ),
				'button_text' => __( 'Clear transients', 'wolmart' ),
				'description' => __( 'This tool will clear the Wolmart Studio block transients cache.', 'wolmart' ),
			),
		);

		return apply_filters( 'wolmart_debug_tools', $tools );
	}

	/**
	 * Execute tool
	 *
	 * @since 1.0
	 * @version 1.1.6 Update clear plugin transients functionality.
	 * @access public
	 *
	 * @param string $tool slug name of tools.
	 */
	public function execute_tool( $tool ) {
		$ran = true;
		switch ( $tool ) {
			case 'clear_transients':
				wolmart_clear_transient();
				delete_site_transient( 'wolmart_patch_transient' );
				delete_transient( 'wc_layered_nav_counts_product_brand' );
				$message = __( 'Addon transients are cleared', 'wolmart' );
				break;
			case 'clear_plugin_transients':
				delete_site_transient( 'wolmart_plugins' );
				delete_site_transient( 'wolmart_theme_rollback_versions' );
				delete_site_transient( 'wolmart_plugin_rollback_versions' );
				$message = __( 'Plugin transients are cleared', 'wolmart' );
				break;
			case 'clear_studio_transients':
				delete_site_transient( 'wolmart_blocks_e' );
				delete_site_transient( 'wolmart_blocks_wpb' );
				$message = __( 'Wolmart Studio transients are cleared', 'wolmart' );
				break;
			case 'clear_merge_css_js':
				$upload_dir  = wp_upload_dir();
				$upload_path = $upload_dir['basedir'] . '/wolmart_merged_resources/';
				if ( file_exists( $upload_path ) ) {
					foreach ( scandir( $upload_path ) as $file ) {
						if ( ! is_dir( $file ) ) {
							unlink( $upload_path . $file );
						}
					}
					rmdir( $upload_path );
				}
				$message = esc_html__( 'Clear all merged javascripts and stylesheets.', 'wolmart' );
				break;
			case 'refresh_blocks':
				$this->refresh_blocks();
				$message = __( 'Refreshed successfully.', 'wolmart' );
				break;
			default:
				$tools = $this->get_tools();

				if ( isset( $tools[ $tool ]['callback'] ) ) {
					$callback = $tools[ $tool ]['callback'];
					$return   = call_user_func( $callback );
					if ( is_string( $return ) ) {
						$message = $return;
					} elseif ( false === $return ) {
						$callback_string = is_array( $callback ) ? get_class( $callback[0] ) . '::' . $callback[1] : $callback;
						$ran             = false;
						/* translators: %s: callback string */
						$message = sprintf( __( 'There was an error calling %s', 'wolmart' ), $callback_string );
					} else {
						$message = __( 'Tool ran.', 'wolmart' );
					}
				} else {
					$ran     = false;
					$message = __( 'There was an error calling this tool. There is no callback present.', 'wolmart' );
				}
				break;
		}

		return array(
			'success' => $ran,
			'message' => $message,
		);
	}


	/**
	 * Render tools page
	 *
	 * @since 1.0
	 * @access public
	 */
	public function view_tools() {
		Wolmart_Admin_Panel::get_instance()->view_header( 'tools' );

		$tools = $this->get_tools();
		$nonce = wp_create_nonce( 'wolmart-tools' );
		?>
		<div class="wolmart-admin-panel-header wolmart-row">
			<div class="wolmart-admin-panel-header-inner">
				<h2><?php esc_html_e( 'Management Tools', 'wolmart' ); ?></h2>
				<p><?php esc_html_e( 'Keep your site health instantly using Wolmart Tools', 'wolmart' ); ?></p>
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
		<div class="wolmart-admin-panel-body wolmart-card-box wolmart-available-tools">
			<table class="wp-list-table widefat" id="wolmart_tools_table">
				<thead>
					<tr>
						<th scope="col" id="title" class="manage-column column-title column-primary"><?php esc_html_e( 'Action Name', 'wolmart' ); ?></th>
						<th scope="col" id="remove" class="manage-column column-remove"><?php esc_html_e( 'Action', 'wolmart' ); ?></th>
					</tr>
				</thead>
				<tbody id="the-list">
					<?php foreach ( $tools as $action => $tool ) : ?>
						<tr class="<?php echo sanitize_html_class( $action ); ?>">
							<th>
								<strong class="action-name"><?php echo esc_html( $tool['action_name'] ); ?></strong>
								<p class="description"><?php echo wp_kses_post( $tool['description'] ); ?></p>
							</th>
							<td class="run-tool">
								<a href="<?php echo esc_url( admin_url( 'admin.php?page=wolmart-tools&action=' . $action . '&&_wpnonce=' . $nonce ) ); ?>" class="button button-large button-light <?php echo esc_attr( $action ); ?>"><?php echo esc_html( $tool['button_text'] ); ?></a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<?php
		Wolmart_Admin_Panel::get_instance()->view_footer();
	}
}

Wolmart_Tools::get_instance();
