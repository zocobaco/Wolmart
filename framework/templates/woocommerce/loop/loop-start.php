<?php
/**
 * Product Loop Start
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/loop-start.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     3.3.0
 */

defined( 'ABSPATH' ) || die;

use ElementorPro\Modules\ThemeBuilder\Classes\Conditions_Manager;

global $wolmart_layout;

$col_cnt     = array();
$layout_type = 'grid';

// Set product type as theme option
if ( ! wolmart_wc_get_loop_prop( 'widget' ) || 'yes' == wolmart_wc_get_loop_prop( 'follow_theme_option' ) ) {
	wolmart_wc_set_loop_prop();
}

$wrapper_class = wolmart_wc_get_loop_prop( 'wrapper_class', array() );
$wrapper_attrs = wolmart_wc_get_loop_prop( 'wrapper_attrs', '' );

if ( ! $wrapper_class ) {
	// Add classes to wrapper class
	$gap = wolmart_get_option( 'products_gap' );
	if ( $gap ) {
		$wrapper_class[] = 'gutter-' . $gap;
	}
}
$wrapper_class[] = 'products';

// Set default show info for wolmart shop
$show_info = apply_filters( 'wolmart_get_shop_products_show_info', wolmart_wc_get_loop_prop( 'show_info', array() ) );
wc_set_loop_prop( 'show_info', $show_info );

if ( apply_filters( 'wolmart_is_vendor_store', false ) && ! wolmart_wc_get_loop_prop( 'widget' ) ) {

	/**
	 * Vendor Store
	 */
	if ( isset( $_REQUEST['showtype'] ) && 'list' == $_REQUEST['showtype'] ) { // if list view mode
		$col_cnt = array(
			'sm'  => 1,
			'min' => 2,
		);
		wc_set_loop_prop( 'product_type', 'list' );
		wc_set_loop_prop( 'content_align', is_rtl() ? 'right' : 'left' );

		if ( ! wolmart_get_option( 'catalog_mode' ) ) {
			$show_info   = wolmart_wc_get_loop_prop( 'show_info' );
			$show_info[] = 'short_desc';
			wc_set_loop_prop( 'show_info', $show_info );
		}
	} else {
		$col_cnt = wolmart_wc_get_loop_prop( 'col_cnt', wolmart_get_responsive_cols( array( 'lg' => wolmart_get_option( 'vendor_products_column' ) ) ) );
	}
} elseif ( wolmart_is_shop() && ! wolmart_wc_get_loop_prop( 'widget' ) ) {
	// Elementor Pro Compatibility
	if ( ! isset( $wolmart_layout['products_column'] ) ) {
		$wolmart_layout['products_column'] = 4;
	}
	if ( ! isset( $wolmart_layout['loadmore_type'] ) ) {
		$wolmart_layout['loadmore_type'] = false;
	}

	/**
	 * Product Archive (Shop)
	 */
	$col_cnt        = array(
		'sm'  => 1,
		'min' => 2,
	);
	$wrapper_attrs .= ' data-col-list="' . esc_attr( wolmart_get_col_class( $col_cnt ) ) . '"';

	if ( isset( $_REQUEST['showtype'] ) && 'list' == $_REQUEST['showtype'] ) { // if list view mode.

		wc_set_loop_prop( 'product_type', 'list' );
		wc_set_loop_prop( 'content_align', is_rtl() ? 'right' : 'left' );

		if ( ! wolmart_get_option( 'catalog_mode' ) ) {
			$show_info   = wolmart_wc_get_loop_prop( 'show_info' );
			$show_info[] = 'short_desc';
			wc_set_loop_prop( 'show_info', $show_info );
		}

		$wrapper_attrs .= ' data-col-grid="' . esc_attr(
			wolmart_get_col_class(
				wolmart_get_responsive_cols( array( 'lg' => empty( $wolmart_layout['products_column'] ) ? 3 : $wolmart_layout['products_column'] ) )
			)
		) . '"';
	} else {
		$col_cnt        = wolmart_get_responsive_cols( array( 'lg' => $wolmart_layout['products_column'] ) );
		$wrapper_attrs .= ' data-col-grid="' . esc_attr( wolmart_get_col_class( $col_cnt ) ) . '"';
	}

	$loadmore_label = wolmart_get_option( 'products_load_label' );
	wc_set_loop_prop( 'loadmore_type', $wolmart_layout['loadmore_type'] ? $wolmart_layout['loadmore_type'] : 'page' );
	wc_set_loop_prop( 'loadmore_label', $loadmore_label ? $loadmore_label : esc_html__( 'Load More', 'wolmart' ) );
	wc_set_loop_prop( 'loadmore_args', array( 'shop' => true ) );

	if ( isset( $wolmart_layout['product_gap'] ) ) {
		$wrapper_class[] = $wolmart_layout['product_gap'];
	}

	echo '<div class="product-archive">';

} elseif ( wolmart_wc_get_loop_prop( 'name' ) && ! wolmart_wc_get_loop_prop( 'widget' ) ) {

	// Related Products in Single Product Page : related or up-sells or more seller products
	$columns     = wolmart_get_option( 'product_related_column' );
	$layout_type = 'slider';

	// If sidebar is shown, show 3 columns
	if ( ! $columns &&
		( ! empty( $wolmart_layout['left_sidebar'] ) && 'hide' != $wolmart_layout['left_sidebar'] ||
		! empty( $wolmart_layout['right_sidebar'] ) && 'hide' != $wolmart_layout['right_sidebar'] ) ) {

		$columns = 3;
	}

	$col_cnt = wolmart_wc_get_loop_prop( 'col_cnt' );
	if ( ! $col_cnt ) {
		$col_cnt = wolmart_get_responsive_cols( array( 'lg' => $columns ) );
	}

	$wrapper_class[] = wolmart_get_slider_class();
	$wrapper_attrs  .= ' data-slider-options="' . esc_attr(
		json_encode(
			wolmart_get_slider_attrs(
				array(
					'show_dots' => false,
					'col_sp'    => isset( $wolmart_layout['product_gap'] ) ? $wolmart_layout['product_gap'] : '',
				),
				$col_cnt
			)
		)
	) . '"';
} else {

	/**
	 * Shortcode Products
	 */
	$col_cnt = wolmart_wc_get_loop_prop( 'col_cnt', wolmart_get_responsive_cols( array( 'lg' => 3 ) ) );

	$show_info = apply_filters( 'wolmart_get_widget_products_show_info', wolmart_wc_get_loop_prop( 'show_info', array() ) );

	wc_set_loop_prop( 'show_info', $show_info );
}

if ( in_array( wolmart_wc_get_loop_prop( 'product_type' ), array( 'product-5', 'product-6', 'product-11' ) ) ) {
	wc_set_loop_prop( 'is_popup', true );
	$wrapper_class[] = 'slideup';
}

// For Category widget
if ( 'product-category-group' == wolmart_wc_get_loop_prop( 'widget' ) ) {
	$show_info      = wolmart_wc_category_show_info( wolmart_wc_get_loop_prop( 'category_type' ) );
	$category_class = array( wolmart_get_category_classes() );
	wc_set_loop_prop( 'show_link', 'yes' == $show_info['link'] );
	wc_set_loop_prop( 'show_count', 'yes' == $show_info['count'] );
	wc_set_loop_prop( 'category_class', $category_class );
}

if ( wolmart_wc_get_loop_prop( 'run_as_filter' ) ) {
	$wrapper_class[] = 'filter-categories';
}

// If loadmore or ajax category filter, add only pages count.
if ( wolmart_wc_get_loop_prop( 'wolmart_ajax_load' ) ) {

	$wrapper_attrs .= ' data-load-max="' . wolmart_wc_get_loop_prop( 'total_pages' ) . '"';

} else {

	// Load more
	$loadmore_type = wolmart_wc_get_loop_prop( 'loadmore_type' );

	if ( ( $loadmore_type || wolmart_wc_get_loop_prop( 'filter_cat' ) || wolmart_wc_get_loop_prop( 'filter_cat_w' ) ) ) {
		$wrapper_attrs .= ' ' . wolmart_loadmore_attributes(
			wolmart_wc_get_loop_prop( 'loadmore_props' ),   // Props
			wolmart_wc_get_loop_prop( 'loadmore_args' ),    // Args
			$loadmore_type,                         // Type
			wolmart_wc_get_loop_prop( 'total_pages' ),      // Total Pages
			wolmart_wc_get_loop_prop( 'filter_cat' )        // Filter Category
		);

		if ( wolmart_wc_get_loop_prop( 'filter_cat_w' ) ) {
			$wrapper_class[] = 'filter-products';
		}

		if ( 'scroll' == $loadmore_type ) {
			$wrapper_class[] = 'load-scroll';

			if ( wolmart_is_shop() ) {
				$wrapper_attrs .= ' data-load-to=".main-content .products"';
			}
		}
	}
}

if ( 'list' == wolmart_wc_get_loop_prop( 'product_type' ) ) {
	$wrapper_class[] = 'list-type-products';
}

if ( 'creative' != wolmart_wc_get_loop_prop( 'layout_type' ) ) {
	$wrapper_class[] = wolmart_get_col_class( $col_cnt );
}

$wrapper_class = apply_filters( 'wolmart_product_loop_wrapper_classes', $wrapper_class );

/**
 * Hook: wolmart_before_shop_loop_start.
 *
 * @hooked wolmart_before_shop_loop_start - 10
 */
do_action( 'wolmart_before_shop_loop_start' );

echo '<ul class="' . esc_attr( implode( ' ', $wrapper_class ) ) . '"' . wolmart_escaped( $wrapper_attrs ) . '>';

if ( false !== array_search( 'filter-categories', $wrapper_class ) ) {

	if ( wolmart_wc_get_loop_prop( 'show_all_filter' ) && isset( $category_class ) ) {
		echo '<li class="category-wrap nav-filter-clean"><div class="product-category ' . implode( ' ', $category_class ) . '"><a href="#" class="active"><h3 class="woocommerce-loop-category__title">' . esc_html__( 'All', 'wolmart' ) . '</h3></a></div></li>';
	}
}
