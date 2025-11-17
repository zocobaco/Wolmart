<?php
/**
 * Footer template
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

defined( 'ABSPATH' ) || die;

?>
			</main>

			<?php do_action( 'wolmart_after_main' ); ?>

			<?php

			global $wolmart_layout;

			if ( 'wolmart_template' == get_post_type() && 'footer' == get_post_meta( get_the_ID(), 'wolmart_template_type', true ) ) {
				/**
				 * View Footer Template
				 */
				?>
				<footer class="footer custom-footer footer-<?php the_ID(); ?>" id="footer">
				<?php
				if ( have_posts() ) :
					the_post();
					the_content();
					wp_reset_postdata();
				endif;
				?>
				</footer>
				<?php
			} elseif ( ! empty( $wolmart_layout['footer'] ) && 'elementor_pro' == $wolmart_layout['footer'] ) {

				/**
				 * Elementor Pro Footer
				 */
				do_action( 'wolmart_elementor_pro_footer_location' );

			} elseif ( ! empty( $wolmart_layout['footer'] ) && 'hide' == $wolmart_layout['footer'] ) {

				// Hide

			} elseif ( ! empty( $wolmart_layout['footer'] ) && 'publish' == get_post_status( intval( $wolmart_layout['footer'] ) ) ) {

				/**
				 * Custom Block Footer
				 */
				?>
				<footer class="footer custom-footer footer-<?php echo intval( $wolmart_layout['footer'] ); ?>" id="footer">
					<?php wolmart_print_template( $wolmart_layout['footer'] ); ?>
				</footer>
				<?php

			} else {
				/**
				 * Default Footer
				 */
				?>
				<footer class="footer footer-copyright" id="footer">
					<?php /* translators: date format */ ?>
					<?php printf( esc_html__( 'Wolmart eCommerce &copy; %s. All Rights Reserved', 'wolmart' ), date( 'Y' ) ); ?>
				</footer>
				<?php
			}
			?>

		</div>

		<?php do_action( 'wolmart_after_page_wrapper' ); ?>

		<?php wolmart_print_mobile_bar(); ?>

		<a id="scroll-top" class="scroll-top" href="#top" title="<?php esc_attr_e( 'Top', 'wolmart' ); ?>" aria-label="<?php esc_attr_e( 'Top', 'wolmart' ); ?>" role="button">
			<i class="w-icon-angle-up"></i>
			<svg  version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 70 70">
				<circle id="progress-indicator" fill="transparent" stroke="#000000" stroke-miterlimit="10" cx="35" cy="35" r="34"/>
			</svg>
		</a>

		<?php if ( ! empty( wolmart_get_option( 'mobile_menu_items' ) ) ) { // if mobile menu has menu items... ?>
			<div class="mobile-menu-wrapper">
				<div class="mobile-menu-overlay"></div>
				<div class="mobile-menu-container" style="height: 100vh;">
					<!-- Need to ajax load mobile menus -->
				</div>
				<a class="mobile-menu-close" href="#" aria-label="<?php esc_attr_e( 'Mobile Menu Close', 'wolmart' ); ?>" role="button"><i class="close-icon"></i></a>
			</div>
		<?php } ?>

		<?php
		// first popup
		if ( function_exists( 'wolmart_is_elementor_preview' ) && ! wolmart_is_elementor_preview() &&
			! empty( $wolmart_layout['popup'] ) && 'hide' != $wolmart_layout['popup'] ) {
			wp_enqueue_script( 'jquery-magnific-popup' );
			wolmart_print_popup_template( $wolmart_layout['popup'] );
		}
		?>

		<?php wp_footer(); ?>
	</body>
</html>
