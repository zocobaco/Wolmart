<?php
/**
 * Product Loop End
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-end.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     2.0.0
 */

defined( 'ABSPATH' ) || die;

global $wolmart_layout;
$layout_type = wolmart_wc_get_loop_prop( 'layout_type' );

if ( isset( $GLOBALS['wolmart_current_product_id'] ) ) {

	// Print single product in products
	$sp_insert     = wolmart_wc_get_loop_prop( 'sp_insert' );
	$banner_insert = wolmart_wc_get_loop_prop( 'banner_insert' );
	$current_id    = $GLOBALS['wolmart_current_product_id'];
	$repeater_ids  = wolmart_wc_get_loop_prop( 'repeater_ids' );

	// Print single product in products
	if ( 'last' == $sp_insert || ( (int) $sp_insert >= $current_id ) ) { // at last or after max
		$html = wolmart_wc_get_loop_prop( 'single_in_products', '' );
		if ( $html ) {
			$wrap_class = 'product-wrap product-single-wrap';
			if ( isset( $repeater_ids[ $current_id + 1 ] ) ) {
				$wrap_class .= ' ' . $repeater_ids[ $current_id + 1 ];
			}
			if ( wolmart_wc_get_loop_prop( 'sp_show_in_box' ) ) {
				$count = 1;
				$html  = str_replace( 'product-single', 'product-single product-boxed', $html, $count );
			}

			echo '<li class="' . esc_attr( $wrap_class ) . '">' . wolmart_escaped( $html ) . '</li>';

			wc_set_loop_prop( 'single_in_products', '' );
		}
	}

	// Print banner in products
	if ( 'last' == $banner_insert || ( (int) $sp_insert >= $current_id ) ) { // at last or after max
		$html = wolmart_wc_get_loop_prop( 'product_banner', '' );
		if ( $html ) {
			wc_set_loop_prop( 'product_banner', '' );
			echo wolmart_escaped( $html );
		}
	}

	// Close multiple slider
	$row_cnt = wolmart_wc_get_loop_prop( 'row_cnt' );
	if ( $row_cnt && 1 != $row_cnt ) {
		if ( 0 != $current_id % $row_cnt ) {
			echo '</ul></li>';
		}
	}
}

echo '</ul>';

if ( ! apply_filters( 'wolmart_is_vendor_store', false ) && wolmart_is_shop() && ! wolmart_wc_get_loop_prop( 'widget' ) ) {
	echo '</div>'; // end of div.product-archive
}


// Load More
$loadmore_type      = wolmart_wc_get_loop_prop( 'loadmore_type' );
$loadmore_btn_style = wolmart_wc_get_loop_prop( 'loadmore_btn_style' );

if ( $loadmore_type ) {
	$page        = absint( empty( $_GET['product-page'] ) ? wolmart_wc_get_loop_prop( 'current_page', 1 ) : $_GET['product-page'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$total_pages = wolmart_wc_get_loop_prop( 'total_pages' );

	if ( $total_pages > 1 ) {
		if ( 'page' === $loadmore_type ) {
			if ( wolmart_wc_get_loop_prop( 'widget', false ) ) {
				echo wolmart_get_pagination_html( $page, $total_pages, 'pagination-load' );
			}
		} else {
			wolmart_loadmore_html( '', $loadmore_type, wolmart_wc_get_loop_prop( 'loadmore_label' ), $loadmore_btn_style );
		}
	}
}

/**
 * Hook: wolmart_after_shop_loop_end.
 *
 * @hooked vendor_store_tab_end - 10
 */
if ( ! wolmart_wc_get_loop_prop( 'widget' ) ) {
	do_action( 'wolmart_after_shop_loop_end' );
}
