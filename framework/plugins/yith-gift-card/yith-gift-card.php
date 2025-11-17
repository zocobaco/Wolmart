<?php
/**
 * Wolmart Yith Gift Card
 *
 * @since 1.1.0
 */
if ( ! class_exists( 'Wolmart_Gift_Card' ) ) {

	class Wolmart_Gift_Card extends Wolmart_Base {

		/**
		 * Constructor
		 *
		 * @since 1.1.0
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );
			add_filter( 'ywgc_remove_gift_card_text', array( $this, 'get_remove_gift_card_text' ), 10 );
			if ( class_exists( 'YITH_YWGC_Frontend' ) ) {
				remove_action( 'wp', array( YITH_YWGC_Frontend::get_instance(), 'yith_ywgc_remove_image_zoom_support' ), 100 );
			}
		}

		/**
		 * Enqueue WCFM scripts
		 *
		 * @since 1.1.0
		 */
		public function enqueue_scripts() {
			wp_enqueue_style( 'wolmart-yith-gift-card-style', WOLMART_PLUGINS_URI . '/yith-gift-card/yith-gift-card' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array( 'wolmart-style' ), WOLMART_VERSION );
		}

		/**
		 * Change text of remove gift card button
		 *
		 * @since 1.1.0
		 */
		public function get_remove_gift_card_text() {
			return esc_html__( 'Remove', 'wolmart' );
		}
	}
}

Wolmart_Gift_Card::get_instance();
