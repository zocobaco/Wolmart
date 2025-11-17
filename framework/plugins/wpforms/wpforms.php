<?php
/**
 * WPForms Lite Compatibility
 *
 * @since 1.1.0
 */

add_action( 'wp_enqueue_scripts', 'wolmart_wpforms_style', 50 );

if ( ! function_exists( 'wolmart_wpforms_style' ) ) {
	function wolmart_wpforms_style() {
		wp_enqueue_style( 'wolmart-wpforms-style', WOLMART_PLUGINS_URI . '/wpforms/wpforms' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array( 'wolmart-style' ), WOLMART_VERSION );
	}
}
