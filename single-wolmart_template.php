<?php
/**
 * Single wolmart template
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

defined( 'ABSPATH' ) || die;

get_header();

do_action( 'wolmart_before_template' );

$template_type = get_post_meta( get_the_ID(), 'wolmart_template_type', true );

if ( 'header' == $template_type ) {

	/**
	 * Header Template
	 *
	 * Please refer templates/header/header.php file.
	 */

} elseif ( 'footer' == $template_type ) {

	/**
	 * Footer Template
	 *
	 * Please refer footer.php file.
	 */

} elseif ( 'popup' == $template_type ) {
	// In case of WPBakery page builder
	if ( function_exists( 'wolmart_is_wpb_preview' ) && wolmart_is_wpb_preview() ) {
		$id       = get_the_ID();
		$settings = get_post_meta( $id, 'popup_options', true );
		if ( $settings && ! is_array( $settings ) ) {
			$settings = json_decode( $settings, true );
		}
		if ( ! $settings ) {
			$settings          = array();
			$settings['width'] = '600';
			$settings['h_pos'] = 'center';
			$settings['v_pos'] = 'center';
		}
		echo '<div class="mfp-bg mfp-fade mfp-wolmart-' . get_the_ID() . ' mfp-ready"></div>';
		echo '<div class="mfp-wrap mfp-close-btn-in mfp-auto-cursor mfp-fade mfp-wolmart mfp-wolmart-' . $id . ' mfp-ready" tabindex="-1" style="overflow: hidden auto;">';
			echo '<div class="mfp-container mfp-inline-holder">';
				echo '<div class="mfp-content" style="justify-content: ' . esc_attr( $settings['h_pos'] ) . '; align-items: ' . esc_attr( $settings['v_pos'] ) . '">';
					echo '<div id="wolmart-popup-' . $id . '" class="popup mfp-fade" style="width: ' . (int) $settings['width'] . 'px;' . ( ! empty( $settings['top'] ) ? ( 'margin-top: ' . (int) $settings['top'] . 'px;' ) : '' ) . ( ! empty( $settings['right'] ) ? ( 'margin-right: ' . (int) $settings['right'] . 'px;' ) : '' ) . ( ! empty( $settings['bottom'] ) ? ( 'margin-bottom: ' . (int) $settings['bottom'] . 'px;' ) : '' ) . ( ! empty( $settings['left'] ) ? ( 'margin-left: ' . (int) $settings['left'] . 'px;' ) : '' ) . '">';
						echo '<div class="wolmart-popup-content"' . ( ! empty( $settings['border'] ) ? ( 'style="border-radius: ' . (int) $settings['border'] ) . 'px"' : '' ) . '>';
							echo '<div class="wolmart-wpb-edit-area">';
	}

	if ( have_posts() ) {

		the_post();

		the_content();

		wp_reset_postdata();
	}

	if ( function_exists( 'wolmart_is_wpb_preview' ) && wolmart_is_wpb_preview() ) {
							echo '</div>';
						echo '</div>';
					echo '</div>';
				echo '</div>';
			echo '</div>';
		echo '</div>';
	}
} else {

	global $product;

	if ( $product ) {

		/**
		 * Single Product Template
		 */
		wc_get_template_part( 'single-product' );

	} else {

		/**
		 * Block Template
		 */
		if ( have_posts() ) :

			the_post();

			the_content();

			wp_reset_postdata();

		endif;

	}

	do_action( 'wolmart_after_template' );
}

get_footer();
