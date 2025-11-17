<?php
/**
 * Backwards Compatiblity Class
 *
 * Functions include compatibility codes older than function name versions
 *
 * @package    Wolmart WordPress Framework
 * @subpackage Theme
 * @since      1.0
 */
defined( 'ABSPATH' ) || die;

class Wolmart_Backwards extends Wolmart_Base {

	public $current_theme_version;

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		$this->current_theme_version = get_option( 'wolmart_theme_version' );

		add_action(
			'init',
			function() {
				$this->version_1_9_0();
				$this->all_versions();
			}
		);
	}

	public function all_versions() {
		if ( version_compare( $this->current_theme_version, WOLMART_VERSION, '>=' ) ) {
			return;
		}

		$theme_mods = get_theme_mods();
		if ( 1 == count( $theme_mods ) && isset( $theme_mods[0] ) && ! $theme_mods[0] ) {
			set_theme_mod( 'resource_disable_fontawesome', true );
			update_option( 'wolmart_disable_product_brand', true );
		}

		update_option( 'wolmart_theme_version', WOLMART_VERSION );

		if ( class_exists( 'Wolmart_Admin' ) && Wolmart_Admin::get_instance()->is_registered() ) {
			delete_site_transient( 'wolmart_plugins' );
		}
	}

	public function version_1_9_0() {
		if ( version_compare( $this->current_theme_version, '1.9.0', '>=' ) ) {
			return;
		}

		// Elementor compatibility
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			Elementor\Plugin::$instance->files_manager->clear_cache();
			delete_post_meta_by_key( Elementor\Core\Base\Elements_Iteration_Actions\Assets::ASSETS_META_KEY );
		}
	}
}

Wolmart_Backwards::get_instance();
