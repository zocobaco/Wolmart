<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}

require_once WOLMART_FRAMEWORK . '/admin/customizer/dynamic/dynamic-color-lib.php';

$style = 'html {' . PHP_EOL;

// /* Basic Layout */
$style .= '--wolmart-container-width: ' . wolmart_get_option( 'container' ) . 'px;
			--wolmart-container-fluid-width: ' . wolmart_get_option( 'container_fluid' ) . 'px;' . PHP_EOL;

$site_type = wolmart_get_option( 'site_type' );
if ( 'full' != $site_type ) {
	$style .= wolmart_dynamic_vars_bg( 'site', wolmart_get_option( 'site_bg' ) );
	$style .= '--wolmart-site-width: ' . wolmart_get_option( 'site_width' ) . 'px;
				--wolmart-site-margin: ' . '0 auto;' . PHP_EOL;

	if ( 'boxed' == $site_type ) {
		$style .= '--wolmart-site-gap: ' . '0 ' . wolmart_get_option( 'site_gap' ) . 'px;' . PHP_EOL;
	} else {
		$style .= '--wolmart-site-gap: ' . wolmart_get_option( 'site_gap' ) . 'px;' . PHP_EOL;
	}
} else {
	$style .= wolmart_dynamic_vars_bg( 'site', array( 'background-color' => '#fff' ) );
	$style .= '--wolmart-site-width: false;
				--wolmart-site-margin: 0;
				--wolmart-site-gap: 0;' . PHP_EOL;
}

$style .= wolmart_dynamic_vars_bg( 'page-wrapper', wolmart_get_option( 'content_bg' ) );

/* Color & Typography */
$style .= '--wolmart-primary-color: ' . wolmart_get_option( 'primary_color' ) . ';
			--wolmart-secondary-color: ' . wolmart_get_option( 'secondary_color' ) . ';
			--wolmart-dark-color: ' . wolmart_get_option( 'dark_color' ) . ';
			--wolmart-light-color: ' . wolmart_get_option( 'light_color' ) . ';
			--wolmart-primary-color-hover: ' . WolmartColorLib::lighten( wolmart_get_option( 'primary_color' ), 6.7 ) . ';' . PHP_EOL;

$p_color_rgb = WolmartColorLib::hexToRGB( wolmart_get_option( 'primary_color' ), false );
$style      .= '--wolmart-primary-color-alpha: rgba(' . $p_color_rgb[0] . ',' . $p_color_rgb[1] . ',' . $p_color_rgb[2] . ', 0.8);' . PHP_EOL;

$style .= '--wolmart-secondary-color-hover: ' . WolmartColorLib::lighten( wolmart_get_option( 'secondary_color' ), 6.7 ) . ';
			--wolmart-dark-color-hover: ' . WolmartColorLib::lighten( wolmart_get_option( 'dark_color' ), 6.7 ) . ';
			--wolmart-light-color-hover: ' . WolmartColorLib::lighten( wolmart_get_option( 'light_color' ), 6.7 ) . ';' . PHP_EOL;

$style .= wolmart_dynamic_vars_typo( 'body', wolmart_get_option( 'typo_default' ) );

// $body_size = wolmart_get_option( 'typo_default' )['font-size'];
$body_size = isset( wolmart_get_option( 'typo_default' )['font-size'] ) ? wolmart_get_option( 'typo_default' )['font-size'] : '14px';
if ( false !== strpos( $body_size, 'rem' ) || false !== strpos( $body_size, 'em' ) ) {
	$body_size = intval( str_replace( array( 'rem', 'em' ), '', $body_size ) ) * 10;
} else {
	$body_size = intval( preg_replace( '/[a~zA~Z]/', '', $body_size ) );
}

$style .= '--wolmart-typo-ratio: ' . floatval( $body_size / 14 ) . ';' . PHP_EOL;
$style .= wolmart_dynamic_vars_typo( 'heading', wolmart_get_option( 'typo_heading' ), array( 'font-weight' => 600 ) );

/* PTB */
$style .= wolmart_dynamic_vars_bg( 'ptb', wolmart_get_option( 'ptb_bg' ) );
$style .= '--wolmart-ptb-height: ' . wolmart_get_option( 'ptb_height' ) . 'px;' . PHP_EOL;
$style .= wolmart_dynamic_vars_typo( 'ptb-title', wolmart_get_option( 'typo_ptb_title' ) );
$style .= wolmart_dynamic_vars_typo( 'ptb-subtitle', wolmart_get_option( 'typo_ptb_subtitle' ) );
$style .= wolmart_dynamic_vars_typo( 'ptb-breadcrumb', wolmart_get_option( 'typo_ptb_breadcrumb' ) );

/* Footer */

/* Lazyload Background */
$style .= '--wolmart-lazy-load-bg: ' . wolmart_get_option( 'lazyload_bg' ) . ';' . PHP_EOL;

/* Line Clamp of Product Title */
$style .= '--wolmart-prod-title-clamp: ' . wolmart_get_option( 'prod_title_clamp' ) . ';' . PHP_EOL;

$style .= PHP_EOL . '}' . PHP_EOL;

/* Responsive */
$style .= '@media (max-width: ' . ( (int) wolmart_get_option( 'container_fluid' ) - 1 ) . 'px) {
    .c-fluid>.e-con-inner {
        width: calc( 100% - 40px + var( --wolmart-con-ex-width ) );
    }
}' . PHP_EOL;

$style .= '@media (max-width: ' . ( (int) wolmart_get_option( 'container' ) - 1 ) . 'px) {
    .container-fluid .container {
        padding-left: 0;
        padding-right: 0;
    }
}' . PHP_EOL;

$style .= '@media (max-width: ' . ( (int) wolmart_get_option( 'container' ) - 1 ) . 'px) and (min-width: 480px) {
	.elementor-top-section.elementor-section-boxed > .elementor-column-gap-no,
	.elementor-section-full_width .elementor-section-boxed > .elementor-column-gap-no {
		width: calc(100% - 40px);
	}
	.elementor-top-section.elementor-section-boxed > .elementor-column-gap-default,
	.elementor-section-full_width .elementor-section-boxed > .elementor-column-gap-default {
		width: calc(100% - 20px);
	}
	.elementor-top-section.elementor-section-boxed > .elementor-column-gap-narrow,
	.elementor-section-full_width .elementor-section-boxed > .elementor-column-gap-narrow {
		width: calc(100% - 30px);
	}
	.elementor-top-section.elementor-section-boxed > .elementor-column-gap-extended,
	.elementor-section-full_width .elementor-section-boxed > .elementor-column-gap-extended {
		width: calc(100% - 10px);
	}
	.elementor-top-section.elementor-section-boxed > .elementor-column-gap-wide,
	.elementor-section-full_width .elementor-section-boxed > .elementor-column-gap-wide {
		width: 100%;
	}
	.elementor-top-section.elementor-section-boxed > .elementor-column-gap-wider,
	.elementor-section-full_width .elementor-section-boxed > .elementor-column-gap-wider {
		width: calc(100% + 10px);
	}
	.e-con-boxed .e-con-inner {
        width: calc( 100% - 40px + var( --wolmart-con-ex-width ) );
	}
}' . PHP_EOL;

echo preg_replace( '/[\t]+/', '', $style );
