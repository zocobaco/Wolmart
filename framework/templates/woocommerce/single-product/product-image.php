<?php
/**
 * Single Product Image
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/product-image.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || die;

// Note: `wc_get_gallery_image_html` was added in WC 3.3.2 and did not exist prior. This check protects against theme overrides being used on older versions of WC.
if ( ! function_exists( 'wc_get_gallery_image_html' ) ) {
	return;
}

global $product;

if ( $product ) :
	do_action( 'wolmart_single_product_before_image' );

	$columns           = apply_filters( 'woocommerce_product_thumbnails_columns', 4 );
	$post_thumbnail_id = $product->get_image_id();
	$buttons           = apply_filters( 'wolmart_single_product_gallery_buttons', '' );
	$wrapper_classes   = apply_filters(
		'woocommerce_single_product_image_gallery_classes',
		array(
			'woocommerce-product-gallery',
			'woocommerce-product-gallery--' . ( $product->get_image_id() ? 'with-images' : 'without-images' ),
			'woocommerce-product-gallery--columns-' . absint( $columns ),
			'images',
		)
	);
	?>
	<div class="<?php echo esc_attr( implode( ' ', array_map( 'sanitize_html_class', $wrapper_classes ) ) ); ?>" data-columns="<?php echo esc_attr( $columns ); ?>" data-buttons="<?php echo esc_attr( $buttons ); ?>">

		<?php do_action( 'wolmart_before_wc_gallery_figure' ); ?>

		<figure class="<?php echo esc_attr( implode( ' ', apply_filters( 'wolmart_single_product_gallery_main_classes', array( 'woocommerce-product-gallery__wrapper' ) ) ) ); ?>">
			<?php
			do_action( 'wolmart_before_product_gallery' );
			do_action( 'wolmart_woocommerce_product_images' );
			do_action( 'woocommerce_product_thumbnails' );
			do_action( 'wolmart_after_product_gallery' );
			?>
		</figure>

		<?php do_action( 'wolmart_after_wc_gallery_figure' ); ?>

	</div>
	<?php
endif;
