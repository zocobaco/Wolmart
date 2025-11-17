<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || die;

global $product, $wolmart_layout;

// Check if the product is a valid WooCommerce product and ensure its visibility before proceeding.
if ( ! is_a( $product, WC_Product::class ) || ! $product->is_visible() ) {
	return;
}

$wrap_class      = 'product-wrap'; // classes for product wrap
$wrap_class_temp = '';
$wrapper_attr    = '';

$product_type      = wolmart_wc_get_loop_prop( 'product_type' );
$content_align     = wolmart_wc_get_loop_prop( 'content_align' );
$show_in_box       = wolmart_wc_get_loop_prop( 'show_in_box' );
$show_media_shadow = wolmart_wc_get_loop_prop( 'show_media_shadow' );
$show_hover_shadow = wolmart_wc_get_loop_prop( 'show_hover_shadow' );
$addtocart_pos     = wolmart_wc_get_loop_prop( 'addtocart_pos' );
$row_cnt           = wolmart_wc_get_loop_prop( 'row_cnt' );

if ( wolmart_is_shop() ) {
	/**
	 * Product Archive
	 */

} elseif ( class_exists( 'WooCommerce' ) && is_product() ) {
	/**
	 * Related Products in Single Product Page
	 */
} else {
	/**
	 * Shortcode Products
	 */
	$layout_type = wolmart_wc_get_loop_prop( 'layout_type' );

	// Creative Grid Image Size
	if ( 'creative' == wolmart_wc_get_loop_prop( 'layout_type' ) ) {
		$mode = wolmart_wc_get_loop_prop( 'creative_mode', -1 );
		$idx  = (int) wolmart_wc_get_loop_prop( 'creative_idx' );

		if ( -1 != $mode ) {
			$thumb_size = wolmart_get_creative_image_sizes( $mode, $idx );
			if ( $thumb_size ) {
				$GLOBALS['wolmart_current_product_img_size'] = $thumb_size;
			}

			if ( 'large' == wolmart_wc_get_loop_prop( 'thumbnail_size' ) ) {
				$wrap_class_temp .= ' large-product-wrap';
			}
		}
		$wrap_class_temp .= ' grid-item-' . ( $idx + 1 );
	}
	if ( isset( $GLOBALS['wolmart_current_product_id'] ) ) {
		$sp_insert     = wolmart_wc_get_loop_prop( 'sp_insert' );
		$banner_insert = wolmart_wc_get_loop_prop( 'banner_insert' );
		$banner_class  = wolmart_wc_get_loop_prop( 'banner_class', '' );
		$sp_class      = wolmart_wc_get_loop_prop( 'sp_class', '' );
		$current_id    = $GLOBALS['wolmart_current_product_id'];
		$repeaters     = wolmart_wc_get_loop_prop( 'repeaters' );

		if ( isset( $repeaters['ids'][ $current_id + 1 ] ) ) {
			$wrap_class_temp .= ' ' . $repeaters['ids'][ $current_id + 1 ];

			if ( $current_id + 1 != $sp_insert && $current_id + 1 != $banner_insert ) {
				$GLOBALS['wolmart_current_product_img_size'] = $repeaters['images'][ $current_id + 1 ];
				$global_product_type                         = $product_type;
				$product_type                                = $repeaters['product_type'][ $current_id + 1 ];
				wc_set_loop_prop( 'product_type', $product_type );
				wolmart_before_shop_loop_start();
			}
		}

		$global_id_changed = false;

		// Print single product in products
		if ( (int) $sp_insert - 1 == $current_id ) {
			$html = wolmart_wc_get_loop_prop( 'single_in_products' );
			if ( $html ) {
				$wrapper_attr = ' data-grid-idx="' . ( $current_id + 1 ) . '"';
				$add_class    = $wrap_class_temp . ' product-single-wrap ' . $sp_class;
				if ( wolmart_wc_get_loop_prop( 'sp_show_in_box' ) ) {
					$count = 1;
					$html  = str_replace( 'product-single', 'product-single product-boxed', $html, $count );
				}

				echo '<li class="' . esc_attr( $wrap_class . $add_class ) . '"' . $wrapper_attr . '>' . wolmart_escaped( $html ) . '</li>';


				global $wolmart_products_single_items;
				if ( isset( $wolmart_products_single_items ) && count( $wolmart_products_single_items ) ) {
					wc_set_loop_prop( 'single_in_products', $wolmart_products_single_items[0]['single_in_products'] );
					wc_set_loop_prop( 'sp_id', $wolmart_products_single_items[0]['sp_id'] );
					wc_set_loop_prop( 'sp_insert', $wolmart_products_single_items[0]['sp_insert'] );
					wc_set_loop_prop( 'sp_class', $wolmart_products_single_items[0]['sp_class'] );
					wc_set_loop_prop( 'products_single_atts', $wolmart_products_single_items[0]['products_single_atts'] );

					array_shift( $wolmart_products_single_items );
				}

				$global_id_changed = true;
				++ $GLOBALS['wolmart_current_product_id'];
				wc_set_loop_prop( 'single_in_products', '' );
			}
		}

		// Print banner in products
		if ( (int) $banner_insert - 1 == $current_id ) {
			$html = wolmart_wc_get_loop_prop( 'product_banner', '' );
			if ( $html ) {
				$wrapper_attr = ' data-grid-idx="' . ( $current_id + 1 ) . '"';
				$add_class    = $wrap_class_temp . ' product-banner-wrap ' . $banner_class;
				echo '<li class="' . esc_attr( $wrap_class . $add_class ) . '"' . $wrapper_attr . '>' . wolmart_escaped( $html ) . '</li>';

				$global_id_changed = true;
				++ $GLOBALS['wolmart_current_product_id'];
				wc_set_loop_prop( 'product_banner', '' );

				global $wolmart_products_banner_items;
				if ( isset( $wolmart_products_banner_items ) && count( $wolmart_products_banner_items ) ) {
					wc_set_loop_prop( 'product_banner', $wolmart_products_banner_items[0]['product_banner'] );
					wc_set_loop_prop( 'banner_insert', $wolmart_products_banner_items[0]['banner_insert'] );
					wc_set_loop_prop( 'banner_class', $wolmart_products_banner_items[0]['banner_class'] );
					array_shift( $wolmart_products_banner_items );
				}
			}
		}

		if ( $global_id_changed ) {
			$current_id      = $GLOBALS['wolmart_current_product_id'];
			$wrap_class_temp = ' grid-item-' . ( $current_id + 1 );
			if ( isset( $repeaters['ids'][ $current_id + 1 ] ) ) {
				$wrap_class_temp                             = ' grid-item-' . ( $current_id + 1 ) . ' ' . $repeaters['ids'][ $current_id + 1 ];
				$GLOBALS['wolmart_current_product_img_size'] = $repeaters['images'][ $current_id + 1 ];
				$global_product_type                         = $product_type;
				$product_type                                = $repeaters['product_type'][ $current_id + 1 ];
				wc_set_loop_prop( 'product_type', $product_type );
				wolmart_before_shop_loop_start();
			}
		}
		$wrapper_attr = ' data-grid-idx="' . ( $current_id + 1 ) . '"';

		++ $GLOBALS['wolmart_current_product_id'];

		wc_set_loop_prop( 'creative_idx', $GLOBALS['wolmart_current_product_id'] );

		if ( $row_cnt && 1 != $row_cnt ) {
			if ( 1 == $GLOBALS['wolmart_current_product_id'] % $row_cnt ) {
				echo '<li class="product-col"><ul>';
			}
		}
	}

	if ( isset( $repeaters['ids'][0] ) ) {
		$wrap_class_temp .= ' ' . $repeaters['ids'][0];
	}

	$wrap_class .= $wrap_class_temp;
}

// Classes for product
$product_classes = array( 'product-loop' );

// - content align
if ( in_array( $content_align, array( 'left', 'center', 'right' ) ) ) {
	$product_classes[] = 'content-' . $content_align;
}
// - show in box
if ( 'yes' == $show_in_box ) {
	$product_classes[] = 'product-boxed';
}
// - show media shadow
if ( 'yes' == $show_media_shadow ) {
	$product_classes[] = 'shadow-media';
}
// - Type 6
if ( 'product-5' == $product_type || 'product-6' == $product_type ) {
	$product_classes[] = 'product-classic product-slideup';
} elseif ( 'product-7' == $product_type ) {
	$product_classes[] = 'product-overlay';
} elseif ( 'list' == $product_type ) {
	$product_classes[] = 'product-list';
} elseif ( 'widget' == $product_type ) {
	$product_classes[] = 'product-list-sm';
} elseif ( 'product-9' == $product_type ) {
	$product_classes[] = 'product-sticky-cart';
} elseif ( 'product-10' == $product_type ) {
	$product_classes[] = 'product-cart-bottom';
} elseif ( 'product-11' == $product_type ) {
	$product_classes[] = 'product-classic product-slideup product-cart-full';
} else {
	$product_classes[] = 'product-default';

	// - Cart Popup Types
	if ( 'product-2' == $product_type || 'product-3' == $product_type ) {
		$product_classes[] = 'product-cart-popup';

		// In catalog mode, for the type of cart popup, disable hide of price on hovering.
		if ( wolmart_get_option( 'catalog_mode' ) && ! wolmart_get_option( 'catalog_cart' ) && wolmart_get_option( 'catalog_price' ) ) {
			$product_classes[] = 'no-hide-price';
		}
	}

	// - Centered type
	if ( 'product-4' == $product_type ) {
		$product_classes[] = 'content-center';
	}

	// - show product shadow
	if ( 'yes' == $show_hover_shadow ) {
		$product_classes[] = 'product-shadow';
	}
	// - with QTY
	if ( 'with_qty' == $addtocart_pos ) {
		$product_classes[] = 'product-with-qty';
	}
}

// catalog mode is enabled
if ( wolmart_get_option( 'catalog_mode' ) ) {
	$product_classes[] = 'product-catalog';
}
?>

<li class="<?php echo esc_attr( apply_filters( 'wolmart_product_wrap_class', $wrap_class ) ); ?>"<?php echo esc_attr( $wrapper_attr ); ?>>

	<?php do_action( 'wolmart_product_loop_before_item', $product_type ); ?>

	<div <?php wc_product_class( $product_classes, $product ); ?> data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item.
		 *
		 * @hooked wolmart_product_loop_figure_open - 5
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item' );

		/**
		 * Hook: woocommerce_before_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 * @hooked wolmart_product_loop_hover_thumbnail - 10
		 * @hooked woocommerce_template_loop_product_link_close - 15
		 * @hooked woocommerce_show_product_loop_sale_flash - 20
		 * @hooked wolmart_product_loop_vertical_action - 20
		 * @hooked wolmart_product_loop_media_action - 20
		 * @hooked wolmart_product_loop_figure_close - 40
		 * @hooked wolmart_product_loop_details_open - 50
		 */
		do_action( 'woocommerce_before_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 *
		 * @hooked wolmart_shop_loop_item_categories - 10
		 * @hooked wolmart_product_loop_default_wishlist_action - 15
		 */
		do_action( 'wolmart_shop_loop_item_categories' );

		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 *
		 * @removed woocommerce_template_loop_product_title - 10
		 * @hooked wolmart_wc_template_loop_product_title - 10
		 */
		do_action( 'woocommerce_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 * @hooked wolmart_product_hide_detail_wrapper_start - 15
		 * @hooked wolmart_product_loop_attributes - 20
		 * @hooked wolmart_product_loop_description - 25
		 * @hooked wolmart_product_loop_action - 30
		 * @hooked wolmart_product_loop_count - 40
		 * @hooked wolmart_product_hide_detail_wrapper_end - 50
		 */
		do_action( 'woocommerce_after_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 *
		 * @removed woocommerce_template_loop_product_link_close - 5
		 * @removed woocommerce_template_loop_add_to_cart - 10
		 * @hooked wolmart_product_loop_details_close - 15
		 * @hooked wolmart_product_loop_hide_details - 20
		 */
		do_action( 'woocommerce_after_shop_loop_item' );
		?>
	</div>
	<?php
	if ( isset( $current_id ) && isset( $GLOBALS['wolmart_current_product_id'] ) && isset( $repeaters['ids'][ (int) $current_id + 1 ] ) ) {
		wc_set_loop_prop( 'product_type', $global_product_type );
		wolmart_before_shop_loop_start();
	}
	?>
	<?php do_action( 'wolmart_product_loop_after_item', $product_type ); ?>
</li>

<?php
if ( $row_cnt && 1 != $row_cnt ) {
	if ( 0 == $GLOBALS['wolmart_current_product_id'] % $row_cnt ) {
		echo '</ul></li>';
	}
}
