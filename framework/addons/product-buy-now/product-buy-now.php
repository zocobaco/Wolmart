<?php

/**
 * Wolmart Product Buy Now class
 *
 * @version 1.0
 */
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Wolmart_Product_Buy_Now' ) ) {

	/**
	 * Wolmart Product Buy Now Feature Class
	 */
	class Wolmart_Product_Buy_Now extends Wolmart_Base {

		public $show_info = array( 'title' );

		/**
		 * Main Class construct
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			add_filter( 'wolmart_customize_sections', array( $this, 'add_buy_now_customize_section' ) );
			add_filter( 'wolmart_customize_fields', array( $this, 'add_buy_now_customize_fields' ) );
			add_action( 'woocommerce_after_add_to_cart_button', array( $this, 'add_buy_now_btn' ), 10 );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_script' ) );
			add_filter( 'woocommerce_add_to_cart_redirect', array( $this, 'redirect_checkout_for_buy_now' ), 99 );
		}


		/**
		 * Add buy now feature to custoimzer
		 *
		 * @param {Array} $sections
		 *
		 * @return {Array} $sections
		 *
		 * @since 1.0.0
		 */
		public function add_buy_now_customize_section( $sections ) {
			$sections['product_buy_now'] = array(
				'title'    => esc_html__( 'Product Buy Now', 'wolmart' ),
				'panel'    => 'product',
				'priority' => 35,
			);

			return $sections;
		}


		/**
		 * Add buy now related fields to customizer
		 *
		 * @param {Array} $fields
		 *
		 * @return {Array} $fields
		 *
		 * @since 1.0.0
		 */
		public function add_buy_now_customize_fields( $fields ) {
			$fields['cs_product_buy_now'] = array(
				'section'  => 'product_buy_now',
				'type'     => 'custom',
				'label'    => '',
				'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Product Buy Now', 'wolmart' ) . '</h3>',
				'priority' => 0,
			);

			$fields['show_buy_now_btn'] = array(
				'section'  => 'product_buy_now',
				'type'     => 'toggle',
				'label'    => esc_html__( 'Show Buy Now Button', 'wolmart' ),
				'priority' => 0,
			);

			$fields['buy_now_text'] = array(
				'section'         => 'product_buy_now',
				'type'            => 'text',
				'label'           => esc_html__( 'Buy Now Text', 'wolmart' ),
				'priority'        => 10,
				'active_callback' => array(
					array(
						'setting'  => 'show_buy_now_btn',
						'operator' => '==',
						'value'    => true,
					),
				),
			);

			$fields['buy_now_link'] = array(
				'section'         => 'product_buy_now',
				'type'            => 'text',
				'label'           => esc_html__( 'Buy Now Link', 'wolmart' ),
				'priority'        => 20,
				'active_callback' => array(
					array(
						'setting'  => 'show_buy_now_btn',
						'operator' => '==',
						'value'    => true,
					),
				),
			);

			return $fields;
		}


		/**
		 * Add buy now button after cart button
		 *
		 * @since 1.0.0
		 */
		public function add_buy_now_btn() {
			global $product;

			if ( ! wolmart_get_option( 'show_buy_now_btn' ) || 'external' == $product->get_type() || wolmart_doing_quickview() ) {
				return;
			}

			echo sprintf( '<button class="single_buy_now_button button btn btn-outline btn-primary btn-rounded">%s</button>', wp_kses_post( wolmart_get_option( 'buy_now_text' ) ) );
		}


		/**
		 * Enqueue Script
		 *
		 * @since 1.0.0
		 */
		public function enqueue_script() {
			wp_enqueue_script( 'wolmart-product-buy-now', WOLMART_ADDONS_URI . '/product-buy-now/product-buy-now' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ), array( 'wolmart-theme' ), WOLMART_VERSION, true );
		}


		/**
		 * Redirect to checkout after click buy now button
		 *
		 * @param {String} url
		 *
		 * @return {boolean}
		 *
		 * @since 1.0.0
		 */
		public function redirect_checkout_for_buy_now( $url ) {

			if ( ! isset( $_REQUEST['buy_now'] ) || false == $_REQUEST['buy_now'] ) {
				return $url;
			}

			if ( empty( $_REQUEST['quantity'] ) ) {
				return $url;
			}

			if ( is_array( $_REQUEST['quantity'] ) ) {
				$quantity_set = false;
				foreach ( $_REQUEST['quantity'] as $item => $quantity ) {
					if ( $quantity <= 0 ) {
						continue;
					}
					$quantity_set = true;
				}

				if ( ! $quantity_set ) {
					return $url;
				}
			}

			$redirect = wolmart_get_option( 'buy_now_link' );
			if ( empty( $redirect ) ) {
				return wc_get_checkout_url();
			} else {
				wp_safe_redirect( $redirect );
				exit;
			}
		}
	}
}

Wolmart_Product_Buy_Now::get_instance();
