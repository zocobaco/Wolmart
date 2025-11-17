<?php
/**
 * WPBakery Compatibility
 *
 * @since 1.0.0
 */

// WPBakery Templates
if ( function_exists( 'vc_set_shortcodes_templates_dir' ) ) {
	vc_set_shortcodes_templates_dir( WOLMART_FRAMEWORK . '/' . WOLMART_PART . '/wpb' );
}

if ( is_admin() ) {
	if ( function_exists( 'wolmart_is_wpb_preview' ) && wolmart_is_wpb_preview() ) {
		add_action( 'admin_enqueue_scripts', 'wolmart_enqueue_wpb_editor_assets', 999 );
	}
}

function wolmart_enqueue_wpb_editor_assets() {
	wp_enqueue_style( 'bootstrap-datepicker', WOLMART_ASSETS . '/vendor/bootstrap/bootstrap-datepicker.min.css', array(), WOLMART_VERSION );
	// Color Variables
	$custom_css  = 'html {';
	$custom_css .= '--wolmart-primary-color:' . wolmart_get_option( 'primary_color' ) . ';';
	$custom_css .= '--wolmart-secondary-color:' . wolmart_get_option( 'secondary_color' ) . ';';
	$custom_css .= '--wolmart-dark-color:' . wolmart_get_option( 'dark_color' ) . ';';
	$custom_css .= '--wolmart-light-color:' . wolmart_get_option( 'light_color' ) . ';';
	$custom_css .= '}';

	wp_add_inline_style( 'wolmart-js-composer-editor', wp_strip_all_tags( wp_specialchars_decode( $custom_css ) ) );
}

/**
 * Force Visual Composer to initialize as "built into the theme". This will hide certain tabs under the Settings->Visual Composer page
 */
add_action( 'vc_before_init', 'wolmart_vc_set_as_theme' );
function wolmart_vc_set_as_theme() {
	if ( function_exists( 'vc_set_as_theme' ) ) {
		vc_set_as_theme();
	}
}
