<?php

/**
 * Wolmart Product Catalog class
 *
 * @version 1.0
 */
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Wolmart_Product_Catalog' ) ) {

	/**
	 * Wolmart Product Frequently Bought Together Class
	 */
	class Wolmart_Product_Catalog extends Wolmart_Base {

		public $show_info = array( 'title', 'wishlist', 'quickview', 'compare' );


		/**
		 * Main Class construct
		 *
		 * @since 1.0.0
		 */
		public function __construct() {
			if ( wolmart_get_option( 'catalog_mode' ) ) {
				add_action( wolmart_doing_ajax() ? 'init' : 'wp', array( $this, 'set_catalog_mode' ) );
			}
			add_filter( 'wolmart_customize_fields', array( $this, 'add_catalog_customize_fields' ) );
			add_filter( 'wolmart_theme_option_default_values', array( $this, 'set_theme_option_default_values' ) );
		}



		/**
		 * Add fields for shop catalog mode
		 *
		 * @param {Array} $fields
		 *
		 * @param {Array} $fields
		 *
		 * @since 1.0.0
		 */
		public function add_catalog_customize_fields( $fields ) {

			$fields['catalog_mode'] = array(
				'section'     => 'shop_pro',
				'type'        => 'toggle',
				'label'       => esc_html__( 'Enable Catalog Mode', 'wolmart' ),
				'description' => esc_html__( 'Catalog mode is generally used to hide some product fields such as product price and add to cart button on shop and product detail page.', 'wolmart' ),
			);

			$fields['catalog_price'] = array(
				'section'         => 'shop_pro',
				'type'            => 'toggle',
				'label'           => esc_html__( 'Show Price', 'wolmart' ),
				'active_callback' => array(
					array(
						'setting'  => 'catalog_mode',
						'operator' => '==',
						'value'    => true,
					),
				),
			);

			$fields['catalog_cart'] = array(
				'section'         => 'shop_pro',
				'type'            => 'toggle',
				'label'           => esc_html__( 'Show Add to Cart Button', 'wolmart' ),
				'active_callback' => array(
					array(
						'setting'  => 'catalog_mode',
						'operator' => '==',
						'value'    => true,
					),
				),
			);

			$fields['catalog_review'] = array(
				'section'         => 'shop_pro',
				'type'            => 'toggle',
				'label'           => esc_html__( 'Show Product Review', 'wolmart' ),
				'active_callback' => array(
					array(
						'setting'  => 'catalog_mode',
						'operator' => '==',
						'value'    => true,
					),
				),
			);

			return $fields;
		}



		/**
		 * Set default values for shop catalog mode
		 *
		 * @param {Array} theme options
		 *
		 * @return {Array} default theme options
		 *
		 * @since 1.0.0
		 */
		public function set_theme_option_default_values( $options ) {

			$options['catalog_mode']   = false;
			$options['catalog_price']  = true;
			$options['catalog_cart']   = false;
			$options['catalog_review'] = false;

			return $options;
		}



		/**
		 * Set Woocommerce Loop Props
		 *
		 * @since 1.0.0
		 */
		public function set_catalog_mode() {

			// Products Archive
			if ( wolmart_get_option( 'catalog_price' ) ) {
				array_push( $this->show_info, 'price' );
			}
			if ( wolmart_get_option( 'catalog_cart' ) ) {
				array_push( $this->show_info, 'addtocart' );
			}
			if ( wolmart_get_option( 'catalog_review' ) ) {
				array_push( $this->show_info, 'rating' );
			}

			// Single Product
			if ( ! wolmart_get_option( 'catalog_cart' ) ) {
				remove_action( 'woocommerce_single_variation', 'woocommerce_single_variation_add_to_cart_button', 20 );
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
			}
			if ( ! wolmart_get_option( 'catalog_price' ) ) {
				remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 9 );
			}
			if ( ! wolmart_get_option( 'catalog_review' ) ) {
				add_filter( 'woocommerce_product_tabs', array( $this, 'remove_woocommerce_review_tabs' ), 98 );
				add_filter( 'pre_option_woocommerce_enable_review_rating', array( $this, 'disable_woocommerce_rating' ) );
			}

			// Set show info of widget products
			add_filter( 'wolmart_get_widget_products_show_info', array( $this, 'get_widget_products_prop' ) );
			add_filter(
				'wolmart_get_shop_products_show_info',
				function( $show_info ) {
					return array_intersect( $this->show_info, $show_info );
				}
			);

		}


		/**
		 * Set widget products show info
		 *
		 * @param {Array} $show_info
		 *
		 * @return {Array} $show_info
		 *
		 * @since 1.0.0
		 */
		public function get_widget_products_prop( $show_info ) {

			if ( wolmart_wc_get_loop_prop( 'widget' ) || 'widget' == wolmart_wc_get_loop_prop( 'product_type' ) ) {
				$show_info = array_intersect(
					$this->show_info,
					$show_info
				);
			}

			return $show_info;
		}


		/**
		 * Remove review tab from single product page
		 *
		 * @param {array} $tabs
		 *
		 * @return {array} $tabs
		 *
		 * @since 1.0.0
		 */
		public function remove_woocommerce_review_tabs( $tabs ) {
			unset( $tabs['reviews'] );
			return $tabs;
		}


		/**
		 * Disable feature to leave review for product by user
		 *
		 * @since 1.0.0
		 */
		public function disable_woocommerce_rating( $false ) {
			return 'no';
		}

	}
}

Wolmart_Product_Catalog::get_instance();
