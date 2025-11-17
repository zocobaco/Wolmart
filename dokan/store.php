<?php
/**
 * The Template for displaying all single posts.
 *
 * @package dokan
 * @package dokan - 2014 1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$store_user   = dokan()->vendor->get( get_query_var( 'author' ) );
$store_info   = $store_user->get_shop_info();
$map_location = $store_user->get_location();
$layout       = wolmart_get_option( 'store_layout', 'left' );
$wrapper_cls  = 'plugin' == wolmart_get_option( 'vendor_style_option' ) ? 'dokan-single-store' : 'dokan-single-store col-lg-9';

get_header( 'shop' );
if ( function_exists( 'yoast_breadcrumb' ) && class_exists('WPSEO_Options') && WPSEO_Options::get( 'breadcrumbs-enable' ) ) {
	global $wolmart_layout;
	$wrap_class = '';
	if ( 'full' != $wolmart_layout['breadcrumb_wrap'] ) {
		$wrap_class .= 'container-fluid' == $wolmart_layout['breadcrumb_wrap'] ? 'container-fluid' : 'container';
	}

	echo '<div class="' . esc_attr( $wrap_class ) . '">';
	yoast_breadcrumb( '<p id="breadcrumbs" class="breadcrumb">', '</p>' );
	echo '</div>';
}
?>
	<?php do_action( 'woocommerce_before_main_content' ); ?>
	<div class="dokan-store-wrap mt-0 layout-<?php echo esc_attr( $layout ); ?>">
		<?php
		dokan_get_template_part(
			'store',
			'sidebar',
			array(
				'store_user'   => $store_user,
				'store_info'   => $store_info,
				'map_location' => $map_location,
				'sidebar_pos'  => $layout,
			)
		);
		?>

		<div id="dokan-primary" class="dokan-single-store">

			<div id="dokan-content" class="store-page-wrap woocommerce" role="main">

				<?php dokan_get_template_part( 'store-header' ); ?>

				<?php do_action( 'dokan_store_profile_frame_after', $store_user->data, $store_info ); ?>

				<?php if ( have_posts() ) { ?>

					<div class="seller-items">

						<?php woocommerce_product_loop_start(); ?>

							<?php
							while ( have_posts() ) :
								the_post();
								?>

								<?php wc_get_template_part( 'content', 'product' ); ?>

							<?php endwhile; // end of the loop. ?>

						<?php woocommerce_product_loop_end(); ?>

					</div>

					<?php wolmart_pagination(); ?>

				<?php } else { ?>

					<p class="alert alert-simple"><?php esc_html_e( 'No products were found of this vendor!', 'dokan-lite' ); ?></p>

				<?php } ?>
			</div>

		</div><!-- .dokan-single-store -->

	</div><!-- .dokan-store-wrap -->

	<?php do_action( 'woocommerce_after_main_content' ); ?>

<?php get_footer( 'shop' ); ?>
