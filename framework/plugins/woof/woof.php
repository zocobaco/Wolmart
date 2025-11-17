<?php
/**
 * Woof Compatibility
 *
 * @since 1.1.0
 */

if ( ! class_exists( 'Wolmart_WOOF' ) ) {

	/**
	 * Wolmart Woof Class
	 */
	class Wolmart_WOOF extends Wolmart_Base {

		protected $counter;

		/**
		 * Main Class construct
		 *
		 * @since 1.1.0
		 */
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );
		}

		/**
		 * Custom style for Yith Featured Video
		 *
		 * @since 1.1.0
		 */
		function enqueue_scripts() {
			wp_enqueue_style( 'wolmart-woof-style', WOLMART_PLUGINS_URI . '/woof/woof' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array( 'wolmart-style' ), WOLMART_VERSION );
		}
	}
}

Wolmart_WOOF::get_instance();
