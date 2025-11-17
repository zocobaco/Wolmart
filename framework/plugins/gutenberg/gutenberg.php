<?php
/**
 * Gutenberg Compatibility
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

if ( is_admin() && ! is_customize_preview() ) {
	add_action( 'enqueue_block_editor_assets', 'wolmart_enqueue_block_editor_assets', 999 );
}

if ( ! function_exists( 'wolmart_enqueue_block_editor_assets' ) ) {
	function wolmart_enqueue_block_editor_assets() {
		wp_enqueue_style( 'wolmart-icons' );
		wp_enqueue_style( 'wolmart-blocks-style-editor', WOLMART_PLUGINS_URI . '/gutenberg/gutenberg-editor' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', WOLMART_VERSION );

		wolmart_load_google_font();

		Wolmart_Layout_Builder::get_instance()->setup_layout();

		ob_start();
		include WOLMART_PLUGINS . '/gutenberg/gutenberg-variable.php';
		$output_style = ob_get_clean();

		if ( function_exists( 'wolmart_minify_css' ) ) {
			$output_style = wolmart_minify_css( $output_style );
		}

		wp_add_inline_style( 'wolmart-blocks-style-editor', $output_style );
	}
}
