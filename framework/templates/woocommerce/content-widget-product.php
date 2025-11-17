<?php
/**
 * The template for displaying product widget entries.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-product.php.
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.5
 */

defined( 'ABSPATH' ) || die;

global $product;
wc_set_loop_prop( 'product_type', 'widget' );
$show_info = apply_filters( 'wolmart_get_widget_products_show_info', array( 'price', 'rating' ) );

if ( ! is_a( $product, 'WC_Product' ) ) {
	return;
}
?>
<li class="product-list-sm product product-loop product-wrap">
	<?php do_action( 'woocommerce_widget_product_item_start', $args ); ?>

	<a class="product-media" href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>" aria-label="<?php esc_attr_e( 'Product Media', 'wolmart' ); ?>">
		<?php echo wolmart_strip_script_tags( $product->get_image() ); ?>
	</a>
	<div class="product-details">
		<h3 class="woocommerce-loop-product__title">
			<a class="product-title" href="<?php echo esc_url( $product->get_permalink() ); ?>"
				title="<?php echo esc_attr( $product->get_title() ); ?>">
				<?php echo esc_html( $product->get_title() ); ?>
			</a>
		</h3>
		<!-- For WC < 3.0.0  backward compatibility  -->
		<div class="woocommerce-product-rating">
			<?php if ( version_compare( WC_VERSION, '2.7', '>' ) ) : ?>
				<?php
				if ( ! empty( $show_rating ) && in_array( 'rating', $show_info ) ) {
					echo wc_get_rating_html( $product->get_average_rating() );} // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
				?>
			<?php else : ?>
				<?php
				if ( ! empty( $show_rating ) && in_array( 'rating', $show_info ) ) {
					echo wolmart_escaped( $product->get_rating_html() ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
				}
				?>
			<?php endif ?>
		</div>
		<?php if ( in_array( 'price', $show_info ) ) : ?>
		<span class="price">
			<?php echo wolmart_escaped( $product->get_price_html() );  // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped ?>
		</span>
		<?php endif; ?>
	</div>

	<?php do_action( 'woocommerce_widget_product_item_end', $args ); ?>
</li>
