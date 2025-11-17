<?php
/**
 * Loop Rating
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/rating.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || die;

if (
	! wc_review_ratings_enabled() ||
	(
		! isset( $is_hide_details ) &&
		'popup' == wolmart_wc_get_loop_prop( 'classic_hover' ) &&
		'list' !== wolmart_wc_get_loop_prop( 'product_type' )
	)
) {
	return;
}

$show_info = wolmart_wc_get_loop_prop( 'show_info', false );
if ( is_array( $show_info ) && ! in_array( 'rating', $show_info ) ) {
	return;
}

if ( is_array( $show_info ) && in_array( 'price', $show_info ) && 'product-11' == wolmart_wc_get_loop_prop( 'product_type' ) ) {
	echo '<div class="product-price-and-rating">';
}

global $product, $is_single_product;
?>
<div class="woocommerce-product-rating">
	<?php
	if ( apply_filters( 'wolmart_single_product_rating_show_number', false ) ) {
		echo esc_html( $product->get_average_rating() );
	} else {
		echo wc_get_rating_html( $product->get_average_rating() );
	}

	if ( apply_filters( 'wolmart_single_product_show_review', comments_open() && 'widget' != wolmart_wc_get_loop_prop( 'product_type' ) ) ) {
		echo wolmart_get_rating_link_html( $product );
	}
	$sold = $product->get_total_sales();
	if ( ! empty( $is_single_product ) && $sold > 0 ) {
		?>
		<span class="wolmart-sold-count">
		<?php
		echo esc_html( $sold . ' ' );
		esc_html_e( 'sold', 'wolmart' );
		?>
		</span>
		<?php
	}
	?>
</div>
