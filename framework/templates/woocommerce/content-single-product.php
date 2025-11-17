<?php
/**
 * The template for displaying product content in the single-product.php template
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-single-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || die;

global $product;

$classes               = array( 'product', 'product-single' );
$single_product_layout = wolmart_get_single_product_layout();

if ( wolmart_doing_quickview() ) {
	$view_type = 'quickview';
	$classes[] = 'product-quickview';
	if ( 'offcanvas' != wolmart_get_option( 'quickview_type' ) ) {
		$classes[] = 'row';
	}
} elseif ( apply_filters( 'wolmart_is_single_product_widget', false ) ) {
	$view_type = 'widget';
	$classes[] = 'product-widget';
	if ( ( ! wolmart_doing_ajax() || ( function_exists( 'wolmart_is_elementor_preview' ) && wolmart_is_elementor_preview() ) ) && 'gallery' != $single_product_layout && 'sticky-both' != $single_product_layout ) {
		$classes[] = 'row';
	}
} else {
	$view_type = '';
}

if ( $single_product_layout ) {
	$classes[] = 'product-single-' . $single_product_layout;
}

$is_li_tag = apply_filters( 'wolmart_is_single_product_li_tag', false );

if ( ! $view_type && ! empty( $product ) ) {
	/**
	 * Hook: woocommerce_before_single_product.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 */
	do_action( 'woocommerce_before_single_product' );
}

$single_product_template = ! $view_type && defined( 'WOLMART_SINGLE_PRODUCT_BUILDER' ) ? Wolmart_Single_Product_Builder::get_instance()->get_template() : false;
if ( ! is_numeric( $single_product_template ) ) {
	$single_product_template = $single_product_layout;
}

$classes = apply_filters( 'wolmart_single_product_classes', $classes );

if ( post_password_required() ) {
	echo get_the_password_form(); // WPCS: XSS ok.
	return;
}
?>
<<?php echo boolval( $is_li_tag ) ? 'li' : 'div'; ?> id="product-<?php the_ID(); ?>" <?php wc_product_class( $classes, $product ); ?>>

	<?php

	if ( 'wolmart_template' == get_post_type() && 'product_layout' == get_post_meta( get_the_ID(), 'wolmart_template_type', true ) ) {
		the_content();
	} elseif ( is_numeric( $single_product_template ) ) {
		do_action( 'wolmart_before_single_product_template' );

		wolmart_print_template( $single_product_template );

		do_action( 'wolmart_after_single_product_template' );
	} else {

		/**
		 * Hook: woocommerce_before_single_product_summary.
		 *
		 * @hooked wolmart_single_product_wrap_first_start - 5
		 * @removed woocommerce_show_product_sale_flash - 10
		 * @removed woocommerce_show_product_images - 20
		 * @hooked wolmart_wc_show_product_images_not_sticky_both - 20
		 * @hooked wolmart_single_product_wrap_first_end - 30
		 * @hooked wolmart_single_product_wrap_second_start - 30
		 * @hooked wolmart_single_product_wrap_sticky_info_start - 40
		 */
		do_action( 'woocommerce_before_single_product_summary' );
		?>

		<div class="<?php echo esc_attr( apply_filters( 'wolmart_single_product_summary_class', 'summary entry-summary' ) ); ?>">
			<?php
			/**
			 * Hook: wolmart_before_product_summary
			 *
			 * @hooked wolmart_wc_show_product_images_sticky_both - 5
			 * @hooked Wolmart_Skeleton::get_instance()->before_product_summary - 20
			 */
			do_action( 'wolmart_before_product_summary' );

			/**
			 * Hook: woocommerce_single_product_summary.
			 *
			 * @hooked wolmart_single_product_wrap_special_start - 2
			 * @hooked woocommerce_template_single_title - 5
			 * @hooked wolmart_single_product_ms_wrap_start - 6
			 * @hooked woocommerce_template_single_meta - 7
			 * @hooked wolmart_single_product_ms_wrap_end - 8
			 * @hooked wolmart_single_product_divider - 8
			 * @hooked wolmart_single_product_sale_countdown - 9
			 * @hooked woocommerce_template_single_price - 9
			 * @hooked woocommerce_template_single_rating - 10
			 * @hooked woocommerce_template_single_excerpt - 20
			 * @hooked wolmart_single_product_wrap_special_end - 22
			 * @hooked wolmart_single_product_wrap_special_start - 22
			 * @hooked woocommerce_template_single_add_to_cart - 30
			 * @removed woocommerce_template_single_meta - 40
			 * @hooked wolmart_single_product_links_wrap_start - 45
			 * @hooked woocommerce_template_single_sharing - 50
			 * @hooked wolmart_print_wishlist_button - 52
			 * @hooked wolmart_single_product_compare - 54
			 * @hooked wolmart_single_product_links_wrap_end - 55
			 * @hooked WC_Structured_Data::generate_product_data() - 60
			 * @hooked wolmart_single_product_wrap_special_end - 70
			 */
			do_action( 'woocommerce_single_product_summary' );

			/**
			 * Hook: wolmart_before_product_summary
			 *
			 * @hooked Wolmart_Skeleton::get_instance()->after_product_summary - 20
			 */
			do_action( 'wolmart_after_product_summary' );
			?>
		</div>

		<?php

		/**
		 * Hook: wolmart_after_product_summary_wrap.
		 *
		 * @hooked wolmart_single_product_wrap_sticky_info_end - 15
		 * @hooked wolmart_single_product_wrap_second_end - 20
		 */
		do_action( 'wolmart_after_product_summary_wrap' );

		if ( ! $view_type ) {
			/**
			 * Hook: woocommerce_after_single_product_summary.
			 *
			 * @hooked woocommerce_output_product_data_tabs - 10
			 * @hooked wolmart_dokan_get_more_products_from_seller - 13
			 * @hooked woocommerce_upsell_display - 15
			 * @hooked woocommerce_output_related_products - 20
			 */
			do_action( 'woocommerce_after_single_product_summary' );
		}
	}

	?>
</<?php echo boolval( $is_li_tag ) ? 'li' : 'div'; ?>>

<?php
if ( ! $view_type ) {
	do_action( 'woocommerce_after_single_product' );
}
?>
