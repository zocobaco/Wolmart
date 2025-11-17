<?php
/**
 * Header template
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

defined( 'ABSPATH' ) || die;

?>

<!DOCTYPE html>
	<html <?php language_attributes(); ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php bloginfo( 'charset' ); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0" />
		<link rel="profile" href="http://gmpg.org/xfn/11" />
		<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
		<?php
		$preload_fonts = wolmart_get_option( 'preload_fonts' );
		if ( ! empty( $preload_fonts ) ) {
			if ( in_array( 'wolmart', $preload_fonts ) ) {
				echo '<link rel="preload" href="' . WOLMART_ASSETS . '/vendor/wolmart-icons/fonts/wolmart.woff2?png09e" as="font" type="font/woff2" crossorigin>';
			}
			if ( ! wolmart_get_option( 'resource_disable_fontawesome' ) ) {
				if ( in_array( 'fas', $preload_fonts ) ) {
					echo '<link rel="preload" href="' . WOLMART_ASSETS . '/vendor/fontawesome-free/webfonts/fa-solid-900.woff2" as="font" type="font/woff2" crossorigin>';
				}
				if ( in_array( 'far', $preload_fonts ) ) {
					echo '<link rel="preload" href="' . WOLMART_ASSETS . '/vendor/fontawesome-free/webfonts/fa-regular-400.woff2" as="font" type="font/woff2" crossorigin>';
				}
				if ( in_array( 'fab', $preload_fonts ) ) {
					echo '<link rel="preload" href="' . WOLMART_ASSETS . '/vendor/fontawesome-free/webfonts/fa-brands-400.woff2" as="font" type="font/woff2" crossorigin>';
				}
			}
		}
		if ( ! empty( $preload_fonts['custom'] ) ) {
			$font_urls = explode( "\n", $preload_fonts['custom'] );
			foreach ( $font_urls as $font_url ) {
				$dot_pos = strrpos( $font_url, '.' );
				if ( false !== $dot_pos ) {
					$type = substr( $font_url, $dot_pos + 1 );
					echo '<link rel="preload" href="' . esc_url( $font_url ) . '" as="font" type="font/' . esc_attr( $type ) . '" crossorigin>';
				}
			}
		}
		?>

		<?php wp_head(); ?>
	</head>
	<body <?php body_class(); ?>>
		<?php wp_body_open(); ?>

		<?php if ( is_customize_preview() || wolmart_get_option( 'loading_animation' ) ) : ?>
			<?php
			echo apply_filters(
				'wolmart_page_loading_animation',
				'
			<div class="loading-overlay">
				<div class="bounce-loader">
					<div></div>
					<div></div>
					<div></div>
				</div>
			</div>'
			);
			?>
		<?php endif; ?>

		<?php do_action( 'wolmart_before_page_wrapper' ); ?>

		<div class="page-wrapper">

			<?php
			global $wolmart_layout;
			if ( ! empty( $wolmart_layout['top_bar'] ) && 'hide' != $wolmart_layout['top_bar'] ) {
				echo '<div class="top-notification-bar">';
				wolmart_print_template( $wolmart_layout['top_bar'] );
				echo '</div>';
			}

			wolmart_get_template_part( 'header/header' );

			wolmart_print_title_bar();

			?>

			<?php do_action( 'wolmart_before_main' ); ?>

			<main id="main" class="<?php echo apply_filters( 'wolmart_main_class', 'main' ); ?>">
