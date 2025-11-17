<?php
/**
 * The template for displaying product category thumbnails within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product-cat.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.7.0
 */

defined( 'ABSPATH' ) || die;

$layout_type    = wolmart_wc_get_loop_prop( 'layout_type' );
$hover_effect   = wolmart_wc_get_loop_prop( 'hover_effect' );
$overlay        = wolmart_wc_get_loop_prop( 'overlay' );
$content_align  = wolmart_wc_get_loop_prop( 'content_align' );
$content_origin = wolmart_wc_get_loop_prop( 'content_origin' );
$row_cnt        = wolmart_wc_get_loop_prop( 'row_cnt' );
$category_class = wolmart_wc_get_loop_prop( 'category_class' );
$category_attr  = '';

$category_wrapper_class = '';
if ( 'creative' == $layout_type ) {
	$mode = wolmart_wc_get_loop_prop( 'creative_mode', -1 );
	$idx  = (int) wolmart_wc_get_loop_prop( 'cat_index' );

	if ( -1 != $mode ) {
		$thumb_size = wolmart_get_creative_image_sizes( $mode, $idx );
		if ( $thumb_size ) {
			$GLOBALS['wolmart_current_cat_img_size'] = $thumb_size;
		}
	}

	wc_set_loop_prop( 'cat_index', $idx + 1 );
	$category_wrapper_class .= ' grid-item-' . ( $idx + 1 );

	$repeaters = wolmart_wc_get_loop_prop( 'repeaters' );
	if ( isset( $repeaters['ids'][ $idx + 1 ] ) ) {
		$category_wrapper_class                 .= ' ' . $repeaters['ids'][ $idx + 1 ];
		$GLOBALS['wolmart_current_cat_img_size'] = $repeaters['images'][ $idx + 1 ];
	}

	if ( isset( $repeaters['ids'][0] ) ) {
		$category_wrapper_class .= ' ' . $repeaters['ids'][0];
	}
	$wrapper_attr = ' data-grid-idx="' . ( $idx + 1 ) . '"';
} elseif ( 'slider' == $layout_type && $row_cnt && 1 != $row_cnt ) {
	$idx = (int) wolmart_wc_get_loop_prop( 'cat_index' ) + 1;
	wc_set_loop_prop( 'cat_index', $idx );
	if ( 1 == $idx % $row_cnt ) {
		echo '<li class="product-col"><ul>';
	}
}

if ( 'creative' == $layout_type ) {
	echo '<li class="' . esc_attr( $category_wrapper_class ) . '"' . esc_attr( $wrapper_attr ) . '>';
} else {
	echo '<li class="category-wrap">';
}

if ( empty( $category_class ) ) {
	$category_class = array();
}

$category_class[] = 'category-' . $category->slug;


// Run as shop filt
if ( wolmart_get_option( 'shop_ajax' ) && wolmart_wc_get_loop_prop( 'run_as_filter_shop' ) && is_product_category( $category->term_id ) ) {
	$category_class[] = 'active';
}

// Content Align
if ( $content_align ) {
	$category_class[] = $content_align;
}

// Overlay
$overlay = wolmart_wc_get_loop_prop( 'overlay' );
if ( $overlay ) {
	$category_class[] = wolmart_get_overlay_class( $overlay );
}

do_action( 'wolmart_product_loop_before_cat' );
?>

<div <?php wc_product_cat_class( $category_class, $category ); ?>>
	<?php
	/**
	 * The woocommerce_before_subcategory hook.
	 *
	 * @removed woocommerce_template_loop_category_link_open - 10
	 */
	do_action( 'woocommerce_before_subcategory', $category );

	/**
	 * The woocommerce_before_subcategory_title hook.
	 *
	 * @removed woocommerce_subcategory_thumbnail - 10
	 *
	 * @hooked wolmart_before_subcategory_thumbnail - 5
	 * @hooked wolmart_wc_subcategory_thumbnail - 10
	 * @hooked wolmart_after_subcategory_thumbnail - 15
	 */
	do_action( 'woocommerce_before_subcategory_title', $category );

	/**
	 * The woocommerce_shop_loop_subcategory_title hook.
	 *
	 * @removed woocommerce_template_loop_category_title - 10
	 * @hooked wolmart_wc_template_loop_category_title - 10
	 */

	do_action( 'woocommerce_shop_loop_subcategory_title', $category );

	/**
	 * The woocommerce_after_subcategory_title hook.
	 *
	 * @hooked wolmart_wc_after_subcategory_title - 10
	 */
	do_action( 'woocommerce_after_subcategory_title', $category );

	/**
	 * The woocommerce_after_subcategory hook.
	 *
	 * @removed woocommerce_template_loop_category_link_close - 10
	 * @hooked wolmart_wc_template_loop_category_link_close - 10
	 */
	do_action( 'woocommerce_after_subcategory', $category );
	?>
</div>

<?php
do_action( 'wolmart_product_loop_after_cat' );

echo '</li>';

if ( 'slider' == $layout_type && $row_cnt && 1 != $row_cnt ) {
	if ( 0 == (int) wolmart_wc_get_loop_prop( 'cat_index' ) % $row_cnt ) {
		echo '</ul></li>';
	}
}
