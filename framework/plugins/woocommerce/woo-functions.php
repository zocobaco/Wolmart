<?php
/**
 * Wolmart WooCommerce Functions
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;


// Woocommerce Comment Form
// add_filter( 'woocommerce_product_review_comment_form_args', 'wolmart_comment_form_args' );

// Woocommerce Mini Cart
add_filter( 'woocommerce_add_to_cart_fragments', 'wolmart_wc_add_to_cart_fragment' );
add_filter( 'woocommerce_cart_item_name', 'wolmart_wc_cart_item_name', 10, 4 );
add_action( 'wp_ajax_wolmart_cart_item_remove', 'wolmart_wc_cart_item_remove' );
add_action( 'wp_ajax_nopriv_wolmart_cart_item_remove', 'wolmart_wc_cart_item_remove' );
add_action( 'wp_ajax_wolmart_add_to_cart', 'wolmart_wc_add_to_cart' );
add_action( 'wp_ajax_nopriv_wolmart_add_to_cart', 'wolmart_wc_add_to_cart' );

// Wolmart Ajax Add to Cart in Quickview and Single Product Widget
add_action( 'wp_ajax_wolmart_ajax_add_to_cart', 'wolmart_ajax_add_to_cart' );
add_action( 'wp_ajax_nopriv_wolmart_ajax_add_to_cart', 'wolmart_ajax_add_to_cart' );

// Woocommerce Breadcrumb
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20 );
add_filter( 'woocommerce_breadcrumb_defaults', 'wolmart_wc_breadcrumb_args' );

// Woocommerce Notice Skin
add_filter( 'wc_add_to_cart_message_html', 'wolmart_wc_add_to_cart_message_html' );
add_filter( 'wolmart_wc_notice_class', 'wolmart_wc_notice_class', 10, 3 );
add_action( 'wolmart_wc_before_notice', 'wolmart_wc_notice_action', 10, 2 );
add_action( 'wolmart_wc_after_notice', 'wolmart_wc_notice_close', 10, 2 );

// Woocommerce Checkout Page
add_filter( 'woocommerce_default_address_fields', 'wolmart_wc_address_fields_change_form_row' );
add_filter( 'woocommerce_billing_fields', 'wolmart_wc_billing_fields_change_form_row' );
add_filter( 'woocommerce_form_field_args', 'wolmart_wc_form_field_args' );

// Woocommerce Cart Page
// change position of cross sell product
remove_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' );
add_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' );
add_action( 'template_redirect', 'wolmart_clear_cart_action' );

// Change columns and total of cross sell
add_filter( 'woocommerce_cross_sells_columns', 'wolmart_cross_sell_columns' );
add_filter( 'woocommerce_cross_sells_total', 'wolmart_cross_sell_products_count' );

add_filter( 'woocommerce_formatted_address_force_country_display', 'wolmart_formatted_address_force_country_display' );
add_filter( 'woocommerce_formatted_address_replacements', 'wolmart_formatted_address_replacements', 10, 2 );

// My Accont Page
add_action( 'woocommerce_save_account_details', 'wolmart_wc_save_account_description' );
add_filter( 'woocommerce_account_menu_items', 'wolmart_woocommerce_account_menu_items' );

// YITH Wishlist Page
add_filter( 'yith_wcwl_edit_title_icon', 'wolmart_yith_wcwl_edit_title_icon' );
add_filter( 'yith_wcwl_wishlist_params', 'wolmart_yith_wcwl_wishlist_params', 10, 5 );

// YITH Mini Wishlist
add_action( 'wp_ajax_wolmart_update_mini_wishlist', 'wolmart_yith_update_mini_wishlist' );
add_action( 'wp_ajax_nopriv_wolmart_update_mini_wishlist', 'wolmart_yith_update_mini_wishlist' );

// YITH Wishlist Remove Notice
if ( class_exists( 'WooCommerce' ) && defined( 'YITH_WCWL' ) ) {
	add_action( 'wp_ajax_remove_from_wishlist', 'wolmart_yith_wcwl_before_remove_notice', 3 );
	add_action( 'wp_ajax_nopriv_remove_from_wishlist', 'wolmart_yith_wcwl_before_remove_notice', 3 );
	add_action( 'wp', 'wolmart_yith_wcwl_remove_notice' );
	add_action( 'wp_ajax_wolmart_account_form', 'wolmart_yith_wcwl_remove_notice', 5 );
	add_action( 'wp_ajax_nopriv_wolmart_account_form', 'wolmart_yith_wcwl_remove_notice', 5 );
}

// YITH ajax filter
add_filter( 'yith_wcan_list_type_empty_filter_class', 'wolmart_yith_empty_filter_class' );
add_filter( 'yith_wcwl_localize_script', 'wolmart_yith_wcwl_localize_script' );

// Add recently viewed products
remove_action( 'template_redirect', 'wc_track_product_view', 20 );
add_action( 'template_redirect', 'wolmart_wc_track_product_view', 20 );

// Product Brand
if ( isset( $GLOBALS['WC_Brands'] ) ) {
	remove_action( 'woocommerce_product_meta_end', array( $GLOBALS['WC_Brands'], 'show_brand' ) );
}

/**
 * Cart Item Count Ajax Action
 *
 * @since 1.4.0
 */
add_action( 'wp_ajax_wolmart_cart_item_count', 'wolmart_wc_cart_item_count' );
add_action( 'wp_ajax_nopriv_wolmart_cart_item_count', 'wolmart_wc_cart_item_count' );

add_action( 'wp_ajax_wolmart_remove_cart_item', 'wolmart_remove_cart_item' );
add_action( 'wp_ajax_nopriv_wolmart_remove_cart_item', 'wolmart_remove_cart_item' );

/**
 * Get Related Products By ID.
 *
 * @since 1.4.0
 */
add_action( 'wp_ajax_wolmart_cart_related_products', 'wolmart_cart_related_products' );
add_action( 'wp_ajax_nopriv_wolmart_cart_related_products', 'wolmart_cart_related_products' );

/**
 * Wolmart Woocommerce Mini Cart Functions
 */
if ( ! function_exists( 'wolmart_wc_add_to_cart_fragment' ) ) {
	function wolmart_wc_add_to_cart_fragment( $fragments ) {
		$_cart_total                           = WC()->cart->get_cart_subtotal();
		$fragments['.cart-toggle .cart-price'] = '<span class="cart-price">' . $_cart_total . '</span>';
		$_cart_qty                             = WC()->cart->cart_contents_count;
		$_cart_qty                             = ( $_cart_qty > 0 ? $_cart_qty : '0' );
		$fragments['.cart-toggle .cart-count'] = '<span class="cart-count">' . ( (int) $_cart_qty ) . '</span>';
		return $fragments;
	}
}

if ( ! function_exists( 'wolmart_wc_add_to_cart' ) ) {
	/**
	 * AJAX add to cart.
	 */
	function wolmart_wc_add_to_cart() {
		ob_start();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product           = wc_get_product( $product_id );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		$variation_id      = 0;
		$variation         = array();

		if ( $product && 'variation' === $product->get_type() ) {
			$variation_id = $product_id;
			$product_id   = $product->get_parent_id();
			$variation    = $product->get_variation_attributes();
			if ( ! empty( $variation ) ) {
				foreach ( $variation as $k => $v ) {
					if ( empty( $v ) && ! empty( $_REQUEST[ $k ] ) ) {
						$variation[ $k ] = wp_unslash( $_REQUEST[ $k ] );
					}
				}
			}
		}

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			WC_AJAX::get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
		// phpcs:enable
	}
}

if ( ! function_exists( 'wolmart_wc_cart_item_name' ) ) {
	function wolmart_wc_cart_item_name( $name, $cart_item, $cart_item_key ) {
		if ( $cart_item['data']->is_type( 'variation' ) && is_array( $cart_item['variation'] ) ) {
			$first = true;
			$link  = false;
			foreach ( $cart_item['variation'] as $attr_name => $value ) {
				$taxonomy = wc_attribute_taxonomy_name( str_replace( 'attribute_pa_', '', urldecode( $attr_name ) ) );
				if ( taxonomy_exists( $taxonomy ) ) {
					// If this is a term slug, get the term's nice name.
					$term = get_term_by( 'slug', $value, $taxonomy );
					if ( ! is_wp_error( $term ) && $term && $term->name ) {
						$value = $term->name;
					}
				} else {
					// If this is a custom option slug, get the options name.
					$value = apply_filters( 'woocommerce_variation_option_name', $value, null, $taxonomy, $cart_item['data'] );
				}
				// Check the nicename against the title.
				if ( $value && ! wc_is_attribute_in_product_name( $value, $cart_item['data']->get_name() ) ) {
					if ( $first ) {
						if ( false !== strpos( $name, '</a>' ) ) {
							$link = true;
							$name = str_replace( '</a>', '', $name );
						}
						$name .= ' - ' . $value;
						$first = false;
					} else {
						$name .= ', ' . $value;
					}
				}
			}
			if ( $link ) {
				$name .= '</a>';
			}
		}
		$name = '<span>' . $name . '</span>';
		return $name;
	}
}

if ( ! function_exists( 'wolmart_wc_cart_item_remove' ) ) {
	function wolmart_wc_cart_item_remove() {
		//check_ajax_referer( 'wolmart-nonce', 'nonce' );
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		$cart         = WC()->instance()->cart;
		$cart_id      = sanitize_text_field( $_POST['cart_id'] );
		$cart_item_id = $cart->find_product_in_cart( $cart_id );
		if ( $cart_item_id ) {
			$cart->set_quantity( $cart_item_id, 0 );
		}
		$cart_ajax = new WC_AJAX();
		$cart_ajax->get_refreshed_fragments();
		// phpcs:enable
		exit();
	}
}


if ( ! function_exists( 'wolmart_ajax_add_to_cart' ) ) {

	/**
	 * Wolmart Ajax addtocart feature
	 *
	 * @since 1.0.0
	 */
	function wolmart_ajax_add_to_cart() {

		ob_start();

		// phpcs:disable WordPress.Security.NonceVerification.Missing
		if ( ! isset( $_POST['product_id'] ) ) {
			return;
		}

		$product_id        = apply_filters( 'woocommerce_add_to_cart_product_id', absint( $_POST['product_id'] ) );
		$product           = wc_get_product( $product_id );
		$quantity          = empty( $_POST['quantity'] ) ? 1 : wc_stock_amount( wp_unslash( $_POST['quantity'] ) );
		$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity );
		$product_status    = get_post_status( $product_id );
		$variation_id      = 0;
		$variation         = array();

		if ( $product && 'variation' === $product->get_type() ) {
			$variation_id = $product_id;
			$product_id   = $product->get_parent_id();
			$variation    = $product->get_variation_attributes();
			if ( ! empty( $variation ) ) {
				foreach ( $variation as $k => $v ) {
					if ( empty( $v ) && ! empty( $_REQUEST[ $k ] ) ) {
						$variation[ $k ] = wp_unslash( $_REQUEST[ $k ] );
					}
				}
			}
		}

		if ( $passed_validation && false !== WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation ) && 'publish' === $product_status ) {

			do_action( 'woocommerce_ajax_added_to_cart', $product_id );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				wc_add_to_cart_message( array( $product_id => $quantity ), true );
			}

			WC_AJAX::get_refreshed_fragments();

		} else {

			// If there was an error adding to the cart, redirect to the product page to show any errors.
			$data = array(
				'error'       => true,
				'product_url' => apply_filters( 'woocommerce_cart_redirect_after_error', get_permalink( $product_id ), $product_id ),
			);

			wp_send_json( $data );
		}
	// phpcs:enable
	}
}


/**
 * Wolmart Woocommerce Breadcrumb Functions
 */
if ( ! function_exists( 'wolmart_wc_breadcrumb_args' ) ) {
	function wolmart_wc_breadcrumb_args( $args ) {
		$delimiter          = wolmart_strip_script_tags( wolmart_get_option( 'ptb_delimiter' ) );
		$delimiter_use_icon = boolval( wolmart_get_option( 'ptb_delimiter_use_icon' ) );
		$delimiter_icon     = esc_attr( wolmart_get_option( 'ptb_delimiter_icon' ) );
		$home_icon          = wolmart_get_option( 'ptb_home_icon' );
		$extra_class        = '';

		if ( $delimiter_use_icon ) {
			$delimiter = '<i class="' . $delimiter_icon . '"></i>';
		}

		if ( ! $delimiter ) {
			$delimiter = '/';
		}

		if ( $home_icon ) {
			$args['home'] = '<i class="w-icon-home"></i>';
			$extra_class .= ' home-icon';
		}

		$args['delimiter']   = '<li class="delimiter">' . $delimiter . '</li>';
		$args['wrap_before'] = '<ul class="breadcrumb' . $extra_class . '">';
		$args['wrap_after']  = '</ul>';
		$args['before']      = '<li>';
		$args['after']       = '</li>';

		return apply_filters( 'wolmart_breadcrumb_args', $args );
	}
}

/**
 * Woocommerce Notice Skin
 */
if ( ! function_exists( 'wolmart_wc_add_to_cart_message_html' ) ) {
	function wolmart_wc_add_to_cart_message_html( $message ) {
		return str_replace( 'button wc-forward', 'btn btn-success btn-md', $message );
	}
}
if ( ! function_exists( 'wolmart_wc_notice_class' ) ) {
	function wolmart_wc_notice_class( $class, $notice, $type ) {

		if ( strpos( $notice['notice'], 'btn' ) ) {
			$class .= ' alert alert-simple alert-btn alert-' . ( 'error' == $type ? 'danger' : esc_attr( $type ) );
		} else {
			$class .= ' alert alert-simple alert-icon alert-close-top alert-' . ( 'error' == $type ? 'danger' : esc_attr( $type ) );
		}

		return $class;
	}
}
if ( ! function_exists( 'wolmart_wc_notice_action' ) ) {
	function wolmart_wc_notice_action( $notice, $type ) {
		if ( ! strpos( $notice['notice'], 'btn' ) ) {
			if ( 'success' == $type ) {
				echo '<i class="w-icon-check2"></i>';
			} elseif ( 'notice' == $type ) {
				echo '<i class="w-icon-exclamation-circle2"></i>';
			} elseif ( 'error' == $type ) {
				echo '<i class="w-icon-exclamation-triangle2"></i>';
			}
		}
	}
}
if ( ! function_exists( 'wolmart_wc_notice_close' ) ) {
	function wolmart_wc_notice_close() {
		echo '<button type="button" class="btn btn-link btn-close" aria-label="' . esc_attr__( 'Close', 'wolmart' ) . '"><i class="close-icon"></i></button>';
	}
}

/**
 * Wolmart Woocommerce Checkout Page Functions
 */
if ( ! function_exists( 'wolmart_wc_address_fields_change_form_row' ) ) {
	function wolmart_wc_address_fields_change_form_row( $fields ) {
		if ( ! is_cart() ) {
			$fields['city']['class']     = array( 'form-row-first', 'address-field' );
			$fields['state']['class']    = array( 'form-row-last', 'address-field' );
			$fields['postcode']['class'] = array( 'form-row-first', 'address-field' );
		}
		return $fields;
	}
}

if ( ! function_exists( 'wolmart_wc_billing_fields_change_form_row' ) ) {
	function wolmart_wc_billing_fields_change_form_row( $fields ) {
		if ( ! is_cart() ) {
			$fields['billing_phone']['class'] = array( 'form-row-last' );
		}
		return $fields;
	}
}

if ( ! function_exists( 'wolmart_wc_form_field_args' ) ) {
	function wolmart_wc_form_field_args( $args ) {
		$args['custom_attributes']['rows'] = 5;
		return $args;
	}
}


/**
 * Wolmart Woocommerce Cart Page Functions
 */

/**
 * Wolmart YITH Wishlist Page Functions
 */
if ( ! function_exists( 'wolmart_yith_wcwl_edit_title_icon' ) ) {
	function wolmart_yith_wcwl_edit_title_icon( $icon ) {
		return '<i class="w-icon-pencil-alt"></i>';
	}
}

if ( ! function_exists( 'wolmart_yith_wcwl_wishlist_params' ) ) {
	function wolmart_yith_wcwl_wishlist_params( $additional_params, $action, $action_params, $pagination, $per_page ) {
		$social_shares = wolmart_get_social_shares();

		$additional_params['share_atts']['share_facebook_icon']  = '<i class="' . $social_shares['facebook']['icon'] . '"></i>';
		$additional_params['share_atts']['share_twitter_icon']   = '<i class="' . $social_shares['twitter']['icon'] . '"></i>';
		$additional_params['share_atts']['share_pinterest_icon'] = '<i class="' . $social_shares['pinterest']['icon'] . '"></i>';
		$additional_params['share_atts']['share_email_icon']     = '<i class="' . $social_shares['email']['icon'] . '"></i>';
		$additional_params['share_atts']['share_whatsapp_icon']  = '<i class="' . $social_shares['whatsapp']['icon'] . '"></i>';

		return $additional_params;
	}
}

if ( ! function_exists( 'wolmart_yith_wcwl_localize_script' ) ) {
	function wolmart_yith_wcwl_localize_script( $variables ) {
		$variables['labels']['added_to_cart_message'] = sprintf( '<div class="woocommerce-notices-wrapper"><div class="woocommerce-message alert alert-simple alert-icon alert-success" role="alert"><i class="w-icon-check2"></i>%s<button type="button" class="btn btn-link btn-close" aria-label="' . esc_attr__( 'Close', 'wolmart' ) . '"><i class="close-icon"></i></button></div></div>', apply_filters( 'yith_wcwl_added_to_cart_message', esc_html__( 'Product added to cart successfully', 'yith-woocommerce-wishlist' ) ) );
		return $variables;
	}
}

/**
 * YITH Wishlist Remove Notice
 */
if ( ! function_exists( 'wolmart_yith_wcwl_before_remove_notice' ) ) {
	function wolmart_yith_wcwl_before_remove_notice() {
		if ( ! isset( $_REQUEST['context'] ) || 'frontend' != $_REQUEST['context'] ) {
			wc_add_notice( 'wolmart_yith_wcwl_before_remove_notice' );
		}
	}
}

if ( ! function_exists( 'wolmart_yith_wcwl_remove_notice' ) ) {
	function wolmart_yith_wcwl_remove_notice() {
		if ( WC()->session ) {
			$notices = WC()->session->get( 'wc_notices', array() );
			if ( ! empty( $notices['success'] ) ) {
				$cnt = count( $notices['success'] );

				for ( $i = 0; $i < $cnt; ++$i ) {
					if ( isset( $notices['success'][ $i ]['notice'] ) && 'wolmart_yith_wcwl_before_remove_notice' == $notices['success'][ $i ]['notice'] ) {
						if ( $i < $cnt-- ) {
							array_splice( $notices['success'], $i, 1 );
							if ( $i < $cnt-- ) {
								array_splice( $notices['success'], $i, 1 );
							}
							-- $i;
						}
					}
				}

				WC()->session->set( 'wc_notices', $notices );
			}
		}
	}
}

/**
 * Wolmart YITH Ajax Filter Functions
 */
if ( ! function_exists( 'wolmart_yith_empty_filter_class' ) ) {
	function wolmart_yith_empty_filter_class( $class ) {
		if ( empty( $class ) ) {
			return 'class="empty"';
		} else {
			return substr( $class, 0, -1 ) . ' empty' . "'";
		}
	}
}

/**
 * WooCommerce Horizontal Filter
 */
if ( ! function_exists( 'wolmart_wc_shop_top_sidebar' ) ) {
	function wolmart_wc_shop_top_sidebar() {
		$show_default_orderby    = 'menu_order' == apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', 'menu_order' ) );
		$catalog_orderby_options = apply_filters(
			'woocommerce_catalog_orderby',
			array(
				'menu_order' => esc_html__( 'Default sorting', 'woocommerce' ),
				'popularity' => esc_html__( 'Sort by popularity', 'woocommerce' ),
				'rating'     => esc_html__( 'Sort by average rating', 'woocommerce' ),
				'date'       => esc_html__( 'Sort by latest', 'woocommerce' ),
				'price'      => esc_html__( 'Sort by price: low to high', 'woocommerce' ),
				'price-desc' => esc_html__( 'Sort by price: high to low', 'woocommerce' ),
			)
		);

		$default_orderby = wolmart_wc_get_loop_prop( 'is_search' ) ? 'relevance' : apply_filters( 'woocommerce_default_catalog_orderby', get_option( 'woocommerce_default_catalog_orderby', '' ) );
		// phpcs:disable WordPress.Security.NonceVerification.Recommended
		$orderby = isset( $_GET['orderby'] ) ? wc_clean( wp_unslash( $_GET['orderby'] ) ) : $default_orderby;
		// phpcs:enable WordPress.Security.NonceVerification.Recommended

		if ( wolmart_wc_get_loop_prop( 'is_search' ) ) {
			$catalog_orderby_options = array_merge( array( 'relevance' => esc_html__( 'Relevance', 'woocommerce' ) ), $catalog_orderby_options );

			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( ! $show_default_orderby ) {
			unset( $catalog_orderby_options['menu_order'] );
		}

		if ( ! wc_review_ratings_enabled() ) {
			unset( $catalog_orderby_options['rating'] );
		}

		if ( ! array_key_exists( $orderby, $catalog_orderby_options ) ) {
			$orderby = current( array_keys( $catalog_orderby_options ) );
		}

		wc_get_template(
			'loop/orderby.php',
			array(
				'catalog_orderby_options' => $catalog_orderby_options,
				'orderby'                 => $orderby,
				'show_default_orderby'    => $show_default_orderby,
			)
		);
	}
}

/**
 * Cart Page
 */
if ( ! function_exists( 'wolmart_clear_cart_action' ) ) {
	/**
	 * Clear cart action
	 *
	 * @since 1.0
	 */
	function wolmart_clear_cart_action() {
		if ( wolmart_get_option( 'clear_cart_button' ) ) {
			return;
		}

		if ( ! empty( $_POST['clear_cart'] ) && wp_verify_nonce( wc_get_var( $_REQUEST['woocommerce-cart-nonce'] ), 'woocommerce-cart' ) ) {
			WC()->cart->empty_cart();
			wc_add_notice( esc_html__( 'Cart is cleared.', 'wolmart' ) );

			$referer = wp_get_referer() ? remove_query_arg(
				array(
					'remove_item',
					'add-to-cart',
					'added-to-cart',
				),
				add_query_arg( 'cart_emptied', '1', wp_get_referer() )
			) : wc_get_cart_url();
			wp_safe_redirect( $referer );
			exit;
		}
	}
}

// return count of columns of cross sell products
if ( ! function_exists( 'wolmart_cross_sell_columns' ) ) {
	function wolmart_cross_sell_columns() {
		return apply_filters( 'wolmart_cross_sell_columns', 4 );
	}
}

// return the total number of products of cross sell
if ( ! function_exists( 'wolmart_cross_sell_products_count' ) ) {
	function wolmart_cross_sell_products_count() {
		return apply_filters( 'wolmart_cross_sell_products_count', 4 );
	}
}


if ( ! function_exists( 'wolmart_formatted_address_force_country_display' ) ) {
	function wolmart_formatted_address_force_country_display() {
		return true;
	}
}

// change default address format with wolmart's one
if ( ! function_exists( 'wolmart_formatted_address_replacements' ) ) {
	function wolmart_formatted_address_replacements( $replacements, $args ) {
		global $wolmart_customer_address;

		$state        = $args['state'];
		$country      = $args['country'];
		$full_country = '';
		$full_state   = '';

		if ( class_exists( 'WooCommerce' ) ) {
			$countries = apply_filters( 'woocommerce_countries', include WC()->plugin_path() . '/i18n/countries.php' );
			$states    = apply_filters( 'woocommerce_states', include WC()->plugin_path() . '/i18n/states.php' );

			// Handle full country name.
			$full_country = ( isset( $countries[ $country ] ) ) ? $countries[ $country ] : $country;

			// Handle full state name.
			$full_state = ( $country && $state && isset( $states[ $country ][ $state ] ) ) ? $states[ $country ][ $state ] : $state;
		}

		$wolmart_customer_address = array(
			__( 'Name', 'wolmart' )     => $args['first_name'] . ' ' . $args['last_name'],
			__( 'Company', 'wolmart' )  => $args['company'],
			__( 'Address', 'wolmart' )  => $args['address_1'] . ' ' . $args['address_2'],
			__( 'City', 'wolmart' )     => isset( $full_state ) ? $args['city'] . ', ' . $full_state : $args['city'],
			__( 'Country', 'wolmart' )  => $full_country,
			__( 'Postcode', 'wolmart' ) => $args['postcode'],
			__( 'Phone', 'wolmart' )    => isset( $args['phone'] ) ? $args['phone'] : '',
		);

		return $replacements;
	}
}

if ( ! function_exists( 'wolmart_wc_save_account_description' ) ) {
	/**
	 * Update account description in save action of "My Account / Account Details" page.
	 *
	 * @since 1.0
	 * @see woocommerce_save_account_details
	 * @param int $user_ID User ID
	 */
	function wolmart_wc_save_account_description( $user_ID ) {
		$description = ! empty( $_POST['user_description'] ) ? wolmart_strip_script_tags( $_POST['user_description'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized, WordPress.Security.ValidatedSanitizedInput.MissingUnslash
		update_user_meta( $user_ID, 'description', $description );
	}
}

if ( ! function_exists( 'wolmart_woocommerce_account_menu_items' ) ) {
	/**
	 * Update my account menu items
	 *
	 * @since 1.1.0
	 * @see woocommerce_account_menu_items
	 * @param array $items
	 */
	function wolmart_woocommerce_account_menu_items( $items ) {
		$has_logout = false;

		// Move customer logout to last
		if ( isset( $items['customer-logout'] ) ) {
			$has_logout = $items['customer-logout'];
			unset( $items['customer-logout'] );
		}

		// add wishlist
		if ( defined( 'YITH_WCWL' ) ) {
			$items['wishlist'] = esc_html__( 'Wishlist', 'wolmart' );
		}

		if ( defined( 'WOLMART_VENDORS' ) ) {
			$items['vendor_dashboard'] = esc_html__( 'Vendor Dashboard', 'wolmart' );
		}

		if ( $has_logout ) {
			$items['customer-logout'] = $has_logout;
		}

		return $items;
	}
}

if ( ! function_exists( 'wolmart_wc_track_product_view' ) ) {
	/**
	 * Track recently viewed products even if recently viewed widget is not active.
	 *
	 * @since 1.0
	 * @see wc_track_product_view
	 */
	function wolmart_wc_track_product_view() {
		if ( ! is_singular( 'product' ) ) {
			return;
		}

		global $post;

		$cookie_handle = 'woocommerce_recently_viewed_' . get_current_blog_id();

		if ( empty( $_COOKIE[ $cookie_handle ] ) ) { // @codingStandardsIgnoreLine.
			$viewed_products = array();
		} else {
			$viewed_products = wp_parse_id_list( (array) explode( '|', wp_unslash( $_COOKIE[ $cookie_handle ] ) ) ); // @codingStandardsIgnoreLine.
		}

		// Unset if already in viewed products list.
		$keys = array_flip( $viewed_products );

		if ( isset( $keys[ $post->ID ] ) ) {
			unset( $viewed_products[ $keys[ $post->ID ] ] );
		}

		$viewed_products[] = $post->ID;

		if ( count( $viewed_products ) > 15 ) {
			array_shift( $viewed_products );
		}

		// Store for session only.
		wc_setcookie( $cookie_handle, implode( '|', $viewed_products ) );
	}
}

if ( ! function_exists( 'wolmart_yith_update_mini_wishlist' ) ) {
	/**
	 * wolmart_yith_update_mini_wishlist
	 *
	 * update mini wishlit when product is added or removed
	 *
	 * @since 1.1.0
	 */
	function wolmart_yith_update_mini_wishlist() {
		ob_start();

		if ( defined( 'WOLMART_HEADER_BUILDER' ) ) {
			$atts = array(
				'miniwishlist' => true,
				'show_count'   => true,
				'show_icon'    => true,
			);

			require WOLMART_HEADER_BUILDER . '/widgets/wishlist/render-wishlist-elementor.php';
		}

		wp_send_json( ob_get_clean() );
	}
}


/**
 * Get cart item count by product ID
 *
 * @since 1.4.0
 */
if ( ! function_exists( 'wolmart_wc_cart_item_count' ) ) {
	function wolmart_wc_cart_item_count() {
		// Get the cart contents
		$cart_items = WC()->cart->get_cart();
		$product_id = trim( $_REQUEST['product_id'] );

		$count = 0;
		foreach ( $cart_items as $item_key => $item_values ) {
			// Check if the item matches the product ID
			if ( $item_values['product_id'] == $product_id ) {
				$count += $item_values['quantity'];
			}
		}

		echo (int) $count;
		die();
	}
}

/**
 * Remove Cart Item
 *
 * @since 1.4.0
 */
if ( ! function_exists( 'wolmart_remove_cart_item' ) ) {
	function wolmart_remove_cart_item() {
		$product_id = trim( $_REQUEST['product_id'] );

		// Get the cart contents
		$cart_items = WC()->cart->get_cart();

		$current_quantity = 0;

		// Loop through the items to find the one with the matching ID and remove one item
		foreach ( $cart_items as $item_key => $item_values ) {
			// Check if the item matches the product ID
			if ( $item_values['product_id'] == $product_id ) {
				// Get the current quantity of the item
				$current_quantity = $item_values['quantity'];

				// Remove one item from the cart for this item key
				if ( $current_quantity > 1 ) {
					WC()->cart->set_quantity( $item_key, $current_quantity - 1 );
				} else {
					WC()->cart->remove_cart_item( $item_key );
				}

				// Exit the loop since we've found and updated the item
				break;
			}
		}

		echo $current_quantity - 1;
		die();
	}
}

/**
 * Get related products by ID
 *
 * @since 1.4.0
 */
if ( ! function_exists( 'wolmart_cart_related_products' ) ) {
	function wolmart_cart_related_products() {
		$product_id      = empty( $_REQUEST['product_id'] ) ? false : (int) $_REQUEST['product_id'];
		$relate_products = array();

		ob_start();
		if ( $product_id ) {
			$relate_product_ids = wc_get_related_products( $product_id, 3 );

			echo '<div class="products row cols-3">';

			foreach ( $relate_product_ids as $relate_product_id ) {
				$product = wc_get_product( $relate_product_id );

				?>
				<div class="product product-cart-popup">
					<figure class="product-media">
						<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
							<?php echo $product->get_image(); ?>
						</a>
					</figure>
					<div class="product-detail">
						<h4 class="product-title">
							<a href="<?php echo esc_url( $product->get_permalink() ); ?>"><?php echo $product->get_title(); ?></a>
						</h4>
						<div class="product-price"><?php echo $product->get_price_html(); ?></div>
					</div>
				</div>
				<?php
			}

			echo '</div>';
		}

		$html = ob_get_clean();

		wp_send_json_success(
			array(
				'html' => $html,
			)
		);
		exit();
	}
}
