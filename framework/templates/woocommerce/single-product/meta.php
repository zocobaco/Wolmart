<?php
/**
 * Single Product Meta
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/meta.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     9.7.0
 */

defined( 'ABSPATH' ) || die;

global $product;

$has_brand_image = false;
$brands          = wp_get_post_terms( get_the_ID(), 'product_brand', array( 'fields' => 'id=>name' ) );
$brand_html      = '';

if ( is_array( $brands ) && count( $brands ) ) {
	foreach ( $brands as $brand_id => $brand_name ) {
		if ( class_exists( 'Wolmart_Product_Brand' ) ) {
			$thumbnail_key = 'brand_thumbnail_id';
		} else {
			$thumbnail_key = 'thumbnail_id';
		}
		$brand_image = get_term_meta( $brand_id, $thumbnail_key, true );
		if ( $brand_image ) {
			$has_brand_image = true;
			$brand_html     .= '<a class="brand" href="' . esc_url( get_term_link( $brand_id, 'product_brand' ) ) . '" title="' . esc_attr( $brand_name ) . '" aria-label="' . esc_html__( 'Brand Image', 'wolmart' ) . '">';
			$brand_html     .= wp_get_attachment_image( $brand_image, 'full' );
			$brand_html     .= '</a>';
		} else {
			$brand_html .= '<span>' . esc_html__( 'Brand: ', 'wolmart' ) . '<a href="' . esc_url( get_term_link( $brand_id, 'product_brand' ) ) . '" title="' . esc_attr( $brand_name ) . '">' . esc_html( $brand_name ) . '</a></span>';
		}
	}
}

?>
<div class="product_meta<?php echo ! $has_brand_image ? ' no-brand-image' : ''; ?>">

	<?php
	if ( $has_brand_image && $brand_html ) :
		echo wolmart_strip_script_tags( $brand_html );
	endif;
	?>

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<div class="product-meta-inner">

		<?php
		if ( ! $has_brand_image && $brand_html ) :
			echo wolmart_strip_script_tags( $brand_html );
		endif;
		?>
		<?php echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

		<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

			<span class="sku_wrapper">
				<?php esc_html_e( 'SKU:', 'woocommerce' ); ?>
				<span class="sku">
					<?php
					$sku = $product->get_sku();
					echo wolmart_escaped( $sku ) ? $sku : esc_html__( 'N/A', 'woocommerce' );
					?>
				</span>
			</span>

		<?php endif; ?>

		<?php echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' ); ?>

	</div>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>
</div>
