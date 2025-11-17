<?php
/**
 * Related Products
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/related.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     10.3.0
 */

defined( 'ABSPATH' ) || die;

if ( $related_products ) :
	/**
	 * Ensure all images of related products are lazy loaded by increasing the
	 * current media count to WordPress's lazy loading threshold if needed.
	 * Because wp_increase_content_media_count() is a private function, we
	 * check for its existence before use.
	 */
	if ( function_exists( 'wp_increase_content_media_count' ) ) {
		$content_media_count = wp_increase_content_media_count( 0 );
		if ( $content_media_count < wp_omit_loading_attr_threshold() ) {
			wp_increase_content_media_count( wp_omit_loading_attr_threshold() - $content_media_count );
		}
	}

	global $product;
	// get all categories that product belongs to
	$categories = $product->get_category_ids(); ?>

	<section class="related products">

		<?php
		$title = wolmart_get_option( 'product_related_title' );
		if ( ! $title ) {
			$title = esc_html__( 'Related products', 'woocommerce' );
		}
		$heading = apply_filters( 'woocommerce_product_related_products_heading', $title );

		if ( $heading ) :
			?>
			<div class="title-wrapper title-start title-underline2">
				<h2 class="title title-link"><?php echo esc_html( $heading ); ?></h2>
				<a class="btn btn-link btn-slide-right btn-infinite" href="<?php echo esc_url( get_category_link( $categories[0] ) ); ?>"><?php esc_html_e( 'More Products', 'wolmart' ); ?><i class="w-icon-long-arrow-right"></i></a>
			</div>
		<?php endif; ?>

		<?php woocommerce_product_loop_start(); ?>

			<?php foreach ( $related_products as $related_product ) : ?>

					<?php
					$post_object = get_post( $related_product->get_id() );

					setup_postdata( $GLOBALS['post'] = $post_object ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited, Squiz.PHP.DisallowMultipleAssignments.Found

					wc_get_template_part( 'content', 'product' );
					?>

			<?php endforeach; ?>

		<?php woocommerce_product_loop_end(); ?>

	</section>
	<?php
endif;

wp_reset_postdata();
