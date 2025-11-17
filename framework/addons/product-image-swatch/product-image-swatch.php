<?php
/**
 * Wolmart Product Image Swatch for Frontend
 */
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Wolmart_Image_Swatch' ) ) {
	class Wolmart_Image_Swatch {
		public $swatch_options = '';
		public $type           = '';

		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

			add_filter( 'wolmart_check_product_variation_type', array( $this, 'check_variation_type' ), 10, 3 );
			add_filter( 'wolmart_wc_product_listed_attribute_attr', array( $this, 'variation_list_attr' ), 10, 3 );
		}

		public function check_variation_type( $result, $attr_name ) {
			global $product;

			$this->type           = '';
			$this->swatch_options = $product->get_meta( 'swatch_options', true );

			if ( 'variable' == $product->get_type() && $this->swatch_options ) {
				$this->swatch_options = json_decode( $this->swatch_options, true );

				if ( isset( $this->swatch_options[ $attr_name ] ) && 'image' == $this->swatch_options[ $attr_name ]['type'] ) {
					$this->type = 'image';
				}
			}

			return ( 'image' == $this->type ) || $result;
		}

		public function enqueue_scripts() {
			wp_enqueue_script( 'wolmart-image-swatch', WOLMART_ADDONS_URI . '/product-image-swatch/product-image-swatch' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ), array( 'wolmart-theme' ), WOLMART_VERSION, true );
		}

		public function variation_list_attr( $attr, $attribute_name, $term_id_or_name ) {
			if ( isset( $this->swatch_options[ $attribute_name ] ) ) {
				$swatch_option = $this->swatch_options[ $attribute_name ];

				if ( isset( $swatch_option[ $term_id_or_name ] ) ) {
					$swatch_attachment_id = $swatch_option[ $term_id_or_name ];

					if ( $swatch_attachment_id ) {
						$swatch_attachment_src = wp_get_attachment_image_src( $swatch_attachment_id, array( 32, 32 ) );
						if ( $swatch_attachment_src ) {

							// display image
							if ( 'image' == $this->type ) {
								if ( class_exists( 'Wolmart_LazyLoad_Images' ) ) {
									$attr = ' class="image" data-lazy="' . esc_url( $swatch_attachment_src[0] ) . '"';
								} else {
									$attr = ' class="image" style="background-image:url(' . esc_url( $swatch_attachment_src[0] ) . ');"';
								}
							}

							// set image attribute
							$attr .= ' data-image="' . esc_html( wolmart_wc_get_gallery_image_html( $swatch_attachment_id, true, false, false ) ) . '"';
						}
					}
				}
			}
			return $attr;
		}
	}
}

new Wolmart_Image_Swatch;
