<?php
/**
 * Dokan Widget Content Product Template
 *
 * @since 2.4
 *
 * @package dokan
 */

$img_kses = apply_filters(
	'dokan_product_image_attributes',
	array(
		'img' => array(
			'alt'    => array(),
			'class'  => array(),
			'height' => array(),
			'src'    => array(),
			'width'  => array(),
		),
	)
);

$show_info = [];

if ( class_exists( 'WooCommerce' ) ) {
	$show_info = wolmart_wc_get_loop_prop( 'show_info', array( 'price' ) );
}

?>

<?php if ( $r->have_posts() ) : ?>
	<ul class="dokan-bestselling-product-widget product_list_widget">
	<?php
	while ( $r->have_posts() ) :
		$r->the_post();
		?>
		<?php global $product; ?>
		<li class="product-list-sm product product-loop product-wrap">
			<a class="product-media" href="<?php echo esc_url( get_permalink( dokan_get_prop( $product, 'id' ) ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>" aria-label="<?php esc_attr_e( 'Product Image', 'wolmart' ); ?>">
				<?php echo wolmart_strip_script_tags( $product->get_image() ); ?>
			</a>
			<div class="product-details">
				<h3 class="woocommerce-loop-product__title">
					<a class="product-title" href="<?php echo esc_url( get_permalink( dokan_get_prop( $product, 'id' ) ) ); ?>" title="<?php echo esc_attr( $product->get_title() ); ?>"><?php echo esc_html( $product->get_title() ); ?></a>
				</h3>

				<!-- For WC < 3.0.0  backward compatibility  -->
				<div class="woocommerce-product-rating">
				<?php if ( version_compare( WC_VERSION, '2.7', '>' ) ) : ?>
					<?php
					if ( ! empty( $show_rating ) ) {
						echo wc_get_rating_html( $product->get_average_rating() );} // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
					?>
				<?php else : ?>
					<?php
					if ( ! empty( $show_rating ) ) {
						echo wolmart_escaped( $product->get_rating_html() ); // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
					}
					?>
				<?php endif ?>
				</div>

				<?php
				if ( in_array( 'price', $show_info ) ) {
					echo '<span class="price">' . wolmart_escaped( $product->get_price_html() ) . '</span>'; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		</li>
		<?php endwhile; ?>
	</ul>
<?php else : ?>
	<p><?php esc_html_e( 'No products found', 'dokan-lite' ); ?></p>
<?php endif; ?>
