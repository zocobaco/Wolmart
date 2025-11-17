<?php
/**
 * Wolmart WooCommerce Functions
 *
 * @package Wolmart WordPress Framework
 * 
 * @since 1.0
 * @since 1.5.1 - Fixed: WC Cart Fragments compatibility issue.
 */
defined( 'ABSPATH' ) || die;

class Wolmart_WooCommerce extends Wolmart_Base {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		add_filter( 'woocommerce_template_path', array( $this, 'get_template_path' ) );

		if ( ! empty( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] && is_admin() ) {
			if ( version_compare( WC_VERSION, '8.4', '>=' ) ) {
				add_action( 'load-post.php', array( $this, 'load_functions' ), 15 );
			} else {
				add_action( 'init', array( $this, 'load_functions' ), 8 );
			}
		} else {
			$this->load_functions();
		}

		/**
		 * WooCommerce Compatibility for Mini Cart
		 * 
		 * @since 1.5.1
		 */
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_wc_cart_fragments' ), 11 );
	}

	/**
	 * Enqueue WC Cart Fragments Scripts.
	 * 
	 * @since 1.5.1
	 */
	public function enqueue_wc_cart_fragments() {
		wp_enqueue_script( 'wc-cart-fragments' );
	}

	public function get_template_path() {
		return 'framework/templates/woocommerce/';
	}

	/**
	 * Load functions
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function load_functions() {
		require_once WOLMART_PLUGINS . '/woocommerce/woo-functions.php';
		require_once WOLMART_PLUGINS . '/woocommerce/product-loop.php';
		require_once WOLMART_PLUGINS . '/woocommerce/product-category.php';
		require_once WOLMART_PLUGINS . '/woocommerce/product-archive.php';
		require_once WOLMART_PLUGINS . '/woocommerce/product-single.php';
	}
}

Wolmart_WooCommerce::get_instance();
