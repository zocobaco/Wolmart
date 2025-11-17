<?php
/**
 * Wolmart WooCommerce Product Loop Functions
 *
 * Functions used to display product loop.
 */

defined( 'ABSPATH' ) || die;

// Before Loop Start
add_action( 'wolmart_before_shop_loop_start', 'wolmart_before_shop_loop_start' );

// Product Loop Media
add_action( 'woocommerce_before_shop_loop_item', 'wolmart_product_loop_figure_open', 5 );

// Compatiblilty with elementor editor
if ( ! empty( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] && is_admin() ) {
	add_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
}

// Product Loop Media - Anchor Tag
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
add_action( 'woocommerce_before_shop_loop_item_title', 'wolmart_product_loop_hover_thumbnail' );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_link_close', 15 );
add_filter( 'single_product_archive_thumbnail_size', 'wolmart_single_product_archive_thumbnail_size' );

// Product Loop Media - Labels and Actions
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash' );
add_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 20 ); // Label
add_action( 'woocommerce_before_shop_loop_item_title', 'wolmart_product_loop_vertical_action', 20 ); // Vertical action
add_action( 'woocommerce_before_shop_loop_item_title', 'wolmart_product_loop_media_action', 20 ); // Media Action
add_action( 'woocommerce_before_shop_loop_item_title', 'wolmart_product_loop_count_deal', 30 ); // Vertical action
add_action( 'woocommerce_before_shop_loop_item_title', 'wolmart_product_loop_figure_close', 40 );

// Product Loop Details
add_action( 'woocommerce_before_shop_loop_item_title', 'wolmart_product_loop_details_open', 50 );
add_action( 'wolmart_shop_loop_item_categories', 'wolmart_shop_loop_item_categories' );
add_action( 'wolmart_shop_loop_item_categories', 'wolmart_product_loop_default_wishlist_action', 15 );
remove_action( 'woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title' );
add_action( 'woocommerce_shop_loop_item_title', 'wolmart_wc_template_loop_product_title' );
add_action( 'woocommerce_after_shop_loop_item_title', 'wolmart_product_loop_is_stock', 5 );
add_action( 'woocommerce_after_shop_loop_item_title', 'wolmart_product_hide_detail_wrapper_start', 15 );
add_action( 'woocommerce_after_shop_loop_item_title', 'wolmart_product_loop_attributes', 20 );
add_action( 'woocommerce_after_shop_loop_item_title', 'wolmart_product_loop_description', 25 );
add_action( 'woocommerce_after_shop_loop_item_title', 'wolmart_product_list_loop_count', 30 );
add_action( 'woocommerce_after_shop_loop_item_title', 'wolmart_product_loop_action', 30 );
add_action( 'woocommerce_after_shop_loop_item_title', 'wolmart_product_grid_loop_count', 40 );
add_action( 'woocommerce_after_shop_loop_item_title', 'wolmart_product_hide_detail_wrapper_end', 50 );
add_action( 'woocommerce_after_shop_loop_item', 'wolmart_product_loop_details_close', 15 );
add_filter( 'woocommerce_product_get_rating_html', 'wolmart_get_rating_html', 10, 3 );

// Product Loop Hide Details (for classic type)
add_action( 'woocommerce_after_shop_loop_item', 'wolmart_product_loop_hide_details', 20 );
add_action( 'wolmart_product_loop_hide_details', 'wolmart_product_loop_action' );

// Remove default AddToCart
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart' );

// Change order of del and ins tag
add_filter( 'woocommerce_format_sale_price', 'wolmart_wc_format_sale_price', 10, 3 );

// Remove default YITH loop positions
if ( defined( 'YITH_WCWL' ) ) {
	add_filter( 'yith_wcwl_loop_positions', 'wolmart_yith_wcwl_loop_positions' );
	add_filter( 'yith_wcwl_add_to_wishlist_params', 'wolmart_yith_wcwl_add_btn_product_icon_class' );
}

// Add post
add_filter( 'shortcode_atts_products', 'wolmart_wc_shortcode_product_add_exclude_attribute', 10, 3 );
add_filter( 'woocommerce_shortcode_products_query', 'wolmart_wc_shortcode_product_add_exclude_arg', 10, 3 );

add_filter( 'wolmart_product_hover_gallery_slider_html', 'wolmart_product_hover_gallery_slider_html', 10 );
add_filter( 'wolmart_product_hover_gallery_multi_hover_html', 'wolmart_product_hover_gallery_multi_hover_html', 10 );

if ( ! function_exists( 'wolmart_before_shop_loop_start' ) ) {
	function wolmart_before_shop_loop_start() {
		$product_type = wolmart_wc_get_loop_prop( 'product_type' );
		if ( ! $product_type ) {
			$addtocart_pos = '';
			$quickview_pos = 'bottom';
			$wishlist_pos  = '';
		} elseif ( 'product-2' == $product_type ) {
			$addtocart_pos = 'detail_bottom';
			$quickview_pos = 'bottom';
			$wishlist_pos  = 'with_title';
		} elseif ( 'product-3' == $product_type ) {
			$addtocart_pos = 'detail_bottom';
			$quickview_pos = 'bottom';
			$wishlist_pos  = '';
			$content_align = 'center';
		} elseif ( 'product-4' == $product_type ) {
			$addtocart_pos = '';
			$quickview_pos = '';
			$wishlist_pos  = '';
		} elseif ( 'product-5' == $product_type || 'product-6' == $product_type ) {
			$addtocart_pos = '';
			$quickview_pos = '';
			$wishlist_pos  = '';
			if ( 'product-5' == $product_type ) {
				$content_align = 'left';
			} else {
				$content_align = 'center';
			}
		} elseif ( 'product-7' == $product_type ) {
			$addtocart_pos = 'detail_bottom';
			$quickview_pos = '';
			$wishlist_pos  = '';
			$content_align = 'center';
		} elseif ( 'product-8' == $product_type ) {
			$addtocart_pos = 'bottom';
			$quickview_pos = 'bottom';
			$wishlist_pos  = '';
			$content_align = 'center';
		} elseif ( 'list' == $product_type ) {
			$addtocart_pos = '';
			$quickview_pos = '';
			$wishlist_pos  = '';
			$content_align = 'left';
		} elseif ( 'product-9' == $product_type ) {
			$addtocart_pos = 'bottom';
			$quickview_pos = '';
			$wishlist_pos  = '';
		} elseif ( 'product-10' == $product_type ) {
			$addtocart_pos = 'detail_bottom';
			$quickview_pos = '';
			$wishlist_pos  = '';
		} else {
			$addtocart_pos = '';
			$quickview_pos = '';
			$wishlist_pos  = '';
		}
		if ( isset( $content_align ) ) {
			wc_set_loop_prop( 'content_align', $content_align );
		}
		wc_set_loop_prop( 'addtocart_pos', $addtocart_pos );
		wc_set_loop_prop( 'quickview_pos', $quickview_pos );
		wc_set_loop_prop( 'wishlist_pos', $wishlist_pos );
	}
}

/**
 * Wolmart Product Loop Media Functions
 */
if ( ! function_exists( 'wolmart_product_loop_figure_open' ) ) {
	function wolmart_product_loop_figure_open() {
		echo '<figure class="product-media">';
	}
}

if ( ! function_exists( 'wolmart_product_loop_figure_close' ) ) {
	function wolmart_product_loop_figure_close() {
		echo '</figure>';
	}
}

if ( ! function_exists( 'wolmart_product_loop_hover_thumbnail' ) ) {
	function wolmart_product_loop_hover_thumbnail() {
		$product_type = wolmart_wc_get_loop_prop( 'product_type' );

		if ( wolmart_get_option( 'hover_change' ) ) {
			$gallery = get_post_meta( get_the_ID(), '_product_image_gallery', true );
			if ( ! empty( $gallery ) ) {
				$gallery = explode( ',', $gallery );
				if ( ! empty( $gallery[0] ) ) {
					if ( ! wp_is_mobile() && 'widget' != $product_type && 'product-7' != $product_type && 'slider' == wolmart_get_option( 'hover_style' ) && count( $gallery ) > 1 ) {
						echo apply_filters( 'wolmart_product_hover_gallery_slider_html', $gallery );
					} elseif ( ! wp_is_mobile() && 'widget' != $product_type && 'product-7' != $product_type && 'multi-image-hover' == wolmart_get_option( 'hover_style' ) && count( $gallery ) > 1 ) {
						$featured_id = get_post_thumbnail_id();
						array_unshift( $gallery, $featured_id );
						echo apply_filters( 'wolmart_product_hover_gallery_multi_hover_html', $gallery );
					} else {
						$attachment_image = wp_get_attachment_image(
							$gallery[0],
							wolmart_wc_get_loop_prop( 'hover_thumbnail_size' ),
							false
						);

						echo apply_filters( 'wolmart_product_hover_image_html', $attachment_image );
					}
				}
			}
		}
	}
}

/**
 * wolmart_product_hover_gallery_slider_html
 *
 * Render gallery slider html for product gallery hover style.
 *
 * @since 1.4.0
 * @param {Array} $galleries
 * @return {String} HTML content
 */
if ( ! function_exists( 'wolmart_product_hover_gallery_slider_html' ) ) {
	function wolmart_product_hover_gallery_slider_html( $galleries ) {
		ob_start();

		$attrs = apply_filters(
			'wolmart_hover_gallery_slider_attributes',
			array(
				'class' => 'wolmart-hover-slider-wrapper',
			)
		);

		$attr_str = '';
		foreach ( $attrs as $key => $value ) {
			$attr_str = $key . '="' . esc_attr( $value ) . '" ';
		}

		echo '<div ' . $attr_str . '>';

		$hover_slide_options = array(
			'slidesPerView' => 1,
			'navigation'    => true,
			'pagination'    => false,
			'spaceBetween'  => 0,
			'loop'          => true,
		);

		echo '<div class="slider-wrapper product-hover-slider" data-slider-options="' . esc_attr( json_encode( $hover_slide_options ) ) . '">';

		foreach ( $galleries as $gallery ) {
			$attachment_image = wp_get_attachment_image(
				$gallery,
				wolmart_wc_get_loop_prop( 'hover_thumbnail_size' ),
				false
			);

			echo apply_filters( 'wolmart_product_hover_image_html', $attachment_image );
		}

		echo '</div>';

		echo '</div>';

		return ob_get_clean();
	}
}

/**
 * wolmart_product_hover_gallery_multi_hover_html
 *
 * Render gallery multi hover html for product gallery hover style.
 *
 * @since 1.4.0
 * @param {Array} $galleries
 * @return {String} HTML content
 */
if ( ! function_exists( 'wolmart_product_hover_gallery_multi_hover_html' ) ) {
	function wolmart_product_hover_gallery_multi_hover_html( $galleries ) {
		ob_start();

		$attrs = apply_filters(
			'wolmart_hover_gallery_multi_hover_attributes',
			array(
				'class' => 'wolmart-hover-multi-image-wrapper',
			)
		);

		$attr_str = '';
		foreach ( $attrs as $key => $value ) {
			$attr_str = $key . '="' . esc_attr( $value ) . '" ';
		}

		echo '<div ' . $attr_str . '>';

		foreach ( $galleries as $index => $gallery ) {
			$attachment_image_url = wp_get_attachment_image_url(
				$gallery,
				wolmart_wc_get_loop_prop( 'hover_thumbnail_size' ),
				false
			);

			echo '<div class="wolmart-hover-multi-image-item" data-number="' . esc_attr( $index + 1 ) . '" data-image-url="' . esc_attr( $attachment_image_url ) . '"></div>';
		}

		echo '</div>';

		echo '<div class="wolmart-multi-image-dots">';

		foreach ( $galleries as $index => $gallery ) {
			if ( $index == 0 ) {
				echo '<span class="wolmart-multi-image-dot active"></span>';
			} else {
				echo '<span class="wolmart-multi-image-dot"></span>';
			}
		}

		echo '</div>';

		return ob_get_clean();
	}
}

if ( ! function_exists( 'wolmart_single_product_archive_thumbnail_size' ) ) {
	function wolmart_single_product_archive_thumbnail_size( $size ) {
		$new_size = $size;
		if ( isset( $GLOBALS['wolmart_current_product_img_size'] ) ) {
			$new_size = $GLOBALS['wolmart_current_product_img_size'];
			unset( $GLOBALS['wolmart_current_product_img_size'] );
		} else {
			$new_size = wolmart_wc_get_loop_prop( 'thumbnail_size', $size );
		}
		if ( 'custom' != $new_size ) {
			$size = $new_size;
		}
		wc_set_loop_prop( 'hover_thumbnail_size', $size );
		return $size;
	}
}

if ( ! function_exists( 'wolmart_product_loop_vertical_action' ) ) {
	function wolmart_product_loop_vertical_action() {
		// if product type is not default, do not print vertical action buttons.

		$product_type = wolmart_wc_get_loop_prop( 'product_type' );
		if ( 'product-8' == $product_type || 'widget' == $product_type ) {
			return;
		}

		global $product;

		$html = '';

		$show_info = wolmart_wc_get_loop_prop( 'show_info', false );

		if ( ! in_array( $product_type, array( 'product-5', 'product-6', 'list' ) ) ) {
			if ( 'product-11' != $product_type ) {
				if ( '' == wolmart_wc_get_loop_prop( 'addtocart_pos' ) ) {
					ob_start();

					woocommerce_template_loop_add_to_cart(
						array(
							'class' => implode(
								' ',
								array_filter(
									array(
										'btn-product-icon',
										'product_type_' . $product->get_type(),
										$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
										$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
									)
								)
							),
						)
					);

					$html .= ob_get_clean();
				}
			}
			if ( ( ! is_array( $show_info ) || in_array( 'wishlist', $show_info ) ) &&
				'' == wolmart_wc_get_loop_prop( 'wishlist_pos' ) && defined( 'YITH_WCWL' ) ) {
				$html .= do_shortcode( '[yith_wcwl_add_to_wishlist container_classes="btn-product-icon"]' );
			}
			if ( wolmart_get_option( 'compare_available' ) && ( ! isset( $show_info ) || ! is_array( $show_info ) || in_array( 'compare', $show_info ) ) ) {
				ob_start();
				wolmart_product_compare( ' btn-product-icon' );
				$html .= ob_get_clean();
			}
		}

		if ( ( ! is_array( $show_info ) || in_array( 'quickview', $show_info ) ) &&
			'' == wolmart_wc_get_loop_prop( 'quickview_pos' ) ) {
			$html .= '<button class="btn-product-icon btn-quickview" data-product="' . $product->get_id() . '" title="' . esc_html__( 'Quick View', 'wolmart' ) . '">' . esc_html__( 'Quick View', 'wolmart' ) . '</button>';
		}

		if ( $html ) {
			echo '<div class="product-action-vertical">' . wolmart_escaped( $html ) . '</div>';
		}
	}
}

/**
 * Wolmart Product Loop Media Action
 *
 * @since 1.0.0
 * @since 1.4.0 - Added - Product Type 9
 */
if ( ! function_exists( 'wolmart_product_loop_media_action' ) ) {
	function wolmart_product_loop_media_action() {

		global $product;

		$product_type = wolmart_wc_get_loop_prop( 'product_type' );
		$show_info    = wolmart_wc_get_loop_prop( 'show_info', false );

		if ( 'bottom' == wolmart_wc_get_loop_prop( 'addtocart_pos' ) ) {
			if ( 'product-9' == $product_type ) {
				global $product;

				$count      = 0;
				$product_id = $product->get_ID();

				if ( ! empty( WC()->cart ) ) {
					$cart_items = WC()->cart->get_cart();

					if ( ! empty( $cart_items ) && is_array( $cart_items ) ) {
						foreach ( $cart_items as $item_key => $item_values ) {
							// Check if the item matches the product ID
							if ( $item_values['product_id'] == $product_id ) {
								$count += $item_values['quantity'];
							}
						}
					}
				}

				echo '<div class="product-action product-sticky-cart-wrapper">';

				echo '<div class="product-added-qty">';

				echo wolmart_cart_sticky_quantity_html( $count );

				woocommerce_template_loop_add_to_cart(
					array(
						'class' => implode(
							' ',
							array_filter(
								array(
									'btn-product' . ( $count > 0 ? ' hide' : '' ),
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
								)
							)
						),
					)
				);

				echo '</div>';

			} elseif ( 'bottom' == wolmart_wc_get_loop_prop( 'quickview_pos' ) ) {
				if ( is_array( $show_info ) && ! in_array( 'addtocart', $show_info ) && ! in_array( 'wishlist', $show_info ) && ! in_array( 'quickview', $show_info ) ) {
					return;
				}
				echo '<div class="product-action action-panel">';
				woocommerce_template_loop_add_to_cart(
					array(
						'class' => implode(
							' ',
							array_filter(
								array(
									'btn-product-icon',
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
								)
							)
						),
					)
				);

				if ( ( ! is_array( $show_info ) || in_array( 'wishlist', $show_info ) ) && defined( 'YITH_WCWL' ) ) {
					echo do_shortcode( '[yith_wcwl_add_to_wishlist container_classes="btn-product-icon"]' );
				}

				if ( wolmart_get_option( 'compare_available' ) && ( ! is_array( $show_info ) || in_array( 'compare', $show_info ) ) ) {
					echo wolmart_product_compare( ' btn-product-icon' );
				}

				if ( ! is_array( $show_info ) || in_array( 'quickview', $show_info ) ) {
					echo '<button class="btn-product-icon btn-quickview" data-product="' . $product->get_id() . '" title="' . esc_html__( 'Quick View', 'wolmart' ) . '">' . esc_html__( 'Quick View', 'wolmart' ) . '</button>';
				}
			} else {
				echo '<div class="product-action">';
				woocommerce_template_loop_add_to_cart(
					array(
						'class' => implode(
							' ',
							array_filter(
								array(
									'btn-product',
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
								)
							)
						),
					)
				);
			}
			echo '</div>';
		} elseif ( 'bottom' == wolmart_wc_get_loop_prop( 'quickview_pos' ) ) {
			if ( ! is_array( $show_info ) || in_array( 'quickview', $show_info ) ) {
				echo '<div class="product-action"><button class="btn-product btn-quickview" data-product="' . $product->get_id() . '" title="' . esc_html__( 'Quick View', 'wolmart' ) . '">' . esc_html__( 'Quick View', 'wolmart' ) . '</button></div>';
			}
		}
	}
}

if ( ! function_exists( 'wolmart_product_loop_count_deal' ) ) {
	function wolmart_product_loop_count_deal() {
		global $product;

		$show_info = wolmart_wc_get_loop_prop( 'show_info', array() );
		if ( ! in_array( 'countdown', $show_info ) ) {
			return;
		}

		if ( $product->is_on_sale() ) {
			if ( $product->is_type( 'variable' ) ) {
				$variations = $product->get_available_variations( 'object' );
				$date_diff  = '';
				$sale_date  = '';
				foreach ( $variations as $variation ) {
					if ( $variation->is_on_sale() ) {
						$new_date = get_post_meta( $variation->get_id(), '_sale_price_dates_to', true );
						if ( ! $new_date || ( $date_diff && $date_diff != $new_date ) ) {
							$date_diff = false;
						} elseif ( $new_date ) {
							if ( false !== $date_diff ) {
								$date_diff = $new_date;
							}
							$sale_date = $new_date;
						}
						if ( false === $date_diff && $sale_date ) {
							break;
						}
					}
				}
				if ( $date_diff ) {
					$date_diff = date( 'Y/m/d H:i:s', (int) $date_diff );
				} elseif ( $sale_date ) {
					$date_diff = date( 'Y/m/d H:i:s', (int) $sale_date );
				}
			} else {
				$date_diff = $product->get_date_on_sale_to();
				if ( $date_diff ) {
					$date_diff = $date_diff->date( 'Y/m/d H:i:s' );
				}
			}
			if ( $date_diff ) :
				wp_enqueue_script( 'jquery-countdown' );
				?>
				<div class="countdown-container block-type">
					<div class="countdown" data-until="<?php echo esc_attr( strtotime( $date_diff ) - strtotime( 'now' ) ); ?>" data-relative="true" data-labels-short="true"></div>
				</div>
				<?php
			endif;
		}
	}
}

/**
 * Wolmart Product Loop Details Functions
 */
if ( ! function_exists( 'wolmart_product_loop_details_open' ) ) {
	function wolmart_product_loop_details_open() {
		echo '<div class="product-details">';
	}
}

if ( ! function_exists( 'wolmart_product_loop_details_close' ) ) {
	function wolmart_product_loop_details_close() {
		echo '</div>';
	}
}

if ( ! function_exists( 'wolmart_wc_template_loop_product_title' ) ) {
	function wolmart_wc_template_loop_product_title() {
		echo '<h3 class="' . esc_attr( apply_filters( 'woocommerce_product_loop_title_classes', 'woocommerce-loop-product__title', 'product-title' ) ) . '">';
		echo '<a href="' . esc_url( get_the_permalink() ) . '">' . wolmart_strip_script_tags( get_the_title() ) . '</a>';
		echo '</h3>';
		if ( 'product-11' == wolmart_wc_get_loop_prop( 'product_type' ) ) {
			$show_info = wolmart_wc_get_loop_prop( 'show_info', false );
			$name      = wolmart_wc_get_loop_prop( 'name' );

			if ( ( ! is_array( $show_info ) || in_array( 'category', $show_info ) ) && ( 'related' != $name ) ) {
				global $product;
				echo '<div class="product-cat">' . wc_get_product_category_list( $product->get_id(), ', ', '' ) . '</div>';
			}
		}
		if ( 'product-10' == wolmart_wc_get_loop_prop( 'product_type' ) ) {
			echo '<div class="product-rating-stock">';
		}
	}
}

if ( ! function_exists( 'wolmart_shop_loop_item_categories' ) ) {
	function wolmart_shop_loop_item_categories() {
		if ( 'product-11' != wolmart_wc_get_loop_prop( 'product_type' ) ) {
			$show_info = wolmart_wc_get_loop_prop( 'show_info', false );
			$name      = wolmart_wc_get_loop_prop( 'name' );

			if ( ( ! is_array( $show_info ) || in_array( 'category', $show_info ) ) && ( 'related' != $name ) ) {
				global $product;
				echo '<div class="product-cat">' . wc_get_product_category_list( $product->get_id(), ', ', '' ) . '</div>';
			}
		}
	}
}

if ( ! function_exists( 'wolmart_product_hide_detail_wrapper_start' ) ) {
	function wolmart_product_hide_detail_wrapper_start() {
		$product_type = wolmart_wc_get_loop_prop( 'product_type' );
		if ( 'product-2' == $product_type || 'product-3' == $product_type ) {
			echo '<div class="product-hide-details">';
		}
	}
}

if ( ! function_exists( 'wolmart_product_hide_detail_wrapper_end' ) ) {
	function wolmart_product_hide_detail_wrapper_end() {
		$product_type = wolmart_wc_get_loop_prop( 'product_type' );
		if ( 'product-2' == $product_type || 'product-3' == $product_type ) {
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'wolmart_product_loop_attributes' ) ) {
	function wolmart_product_loop_attributes() {
		$show_info = wolmart_wc_get_loop_prop( 'show_info' );
		if ( ! is_array( $show_info ) || in_array( 'attribute', $show_info ) ) {
			wolmart_wc_product_listed_attributes_html();
		}
	}
}

if ( ! function_exists( 'wolmart_product_loop_description' ) ) {
	function wolmart_product_loop_description() {
		$show_info = wolmart_wc_get_loop_prop( 'show_info', false );
		if ( /*'list' == wolmart_wc_get_loop_prop( 'product_type' ) &&*/ ( ! is_array( $show_info ) || in_array( 'short_desc', $show_info ) ) ) {
			global $product;

			$excerpt_type   = wolmart_get_option( 'prod_excerpt_type' );
			$excerpt_length = wolmart_get_option( 'prod_excerpt_length' );
			// echo '<div class="short-desc">' . wolmart_trim_description( $product->get_short_description(), 30, 'words', 'product-short-desc' ) . '</div>';
			echo '<div class="short-desc">' . wolmart_trim_description( do_shortcode( $product->get_short_description() ), $excerpt_length, $excerpt_type ) . '</div>';
		}
	}
}

if ( ! function_exists( 'wolmart_product_loop_default_wishlist_action' ) ) {
	function wolmart_product_loop_default_wishlist_action() {
		$show_info = wolmart_wc_get_loop_prop( 'show_info', false );
		if ( defined( 'YITH_WCWL' ) &&
				'with_title' == wolmart_wc_get_loop_prop( 'wishlist_pos' ) &&
				( ! is_array( $show_info ) || in_array( 'wishlist', $show_info ) ) ) {

			echo do_shortcode( '[yith_wcwl_add_to_wishlist]' );
		}
	}
}

if ( ! function_exists( 'wolmart_product_loop_is_stock' ) ) {
	function wolmart_product_loop_is_stock() {
		global $product;

		if ( 'product-10' == wolmart_wc_get_loop_prop( 'product_type' ) ) {
			$labels = wolmart_wc_get_loop_prop( 'show_labels', array( 'hot', 'stock', 'sale', 'new' ) );
			if ( in_array( 'stock', $labels ) ) {
				if ( $product->is_in_stock() ) {
					echo '<div class="stock-status"><i class="w-icon-check"></i>' . esc_html__( 'In stock', 'wolmart' ) . '</div>';
				} else {
					echo '<div class="stock-status out-stock">' . esc_html__( 'Out of stock', 'wolmart' ) . '</div>';
				}
			}
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'wolmart_product_loop_action' ) ) {
	function wolmart_product_loop_action( $details = '' ) {
		global $product;

		$product_type = wolmart_wc_get_loop_prop( 'product_type' );

		if ( in_array( $product_type, array( 'product-5', 'product-6', 'product-11', 'list' ) ) ) {

			if ( 'hide-details' !== $details && wolmart_wc_get_loop_prop( 'is_popup' ) && 'list' != $product_type ) {
				return;
			}

			$content_align = wolmart_wc_get_loop_prop( 'content_align' );
			$show_info     = wolmart_wc_get_loop_prop( 'show_info', false );

			if ( defined( 'YITH_WCWL' ) && ( ! is_array( $show_info ) || in_array( 'wishlist', $show_info ) ) ) {
				$wishlist = do_shortcode( '[yith_wcwl_add_to_wishlist container_classes="btn-product-icon"]' );
			} else {
				$wishlist = '';
			}

			if ( 'product-11' != $product_type ) {
				if ( wolmart_get_option( 'compare_available' ) && ( ! is_array( $show_info ) || in_array( 'compare', $show_info ) ) ) {
					ob_start();
					wolmart_product_compare( ' btn-product-icon' );
					$compare = ob_get_clean();
				} else {
					$compare = '';
				}
			}

			echo '<div class="product-action">';

			if ( 'center' == $content_align || ( ( ! is_rtl() && 'right' == $content_align ) || ( is_rtl() && 'left' == $content_align ) ) ) {
				echo wolmart_escaped( $wishlist );
			}
			if ( 'product-11' != $product_type && ( ( ! is_rtl() && 'right' == $content_align ) || ( is_rtl() && 'left' == $content_align ) ) ) {
				echo wolmart_escaped( $compare );
			}
			woocommerce_template_loop_add_to_cart(
				array(
					'class' => implode(
						' ',
						array_filter(
							array(
								'btn-product',
								'product_type_' . $product->get_type(),
								$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
								$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
							)
						)
					),
				)
			);

			if ( ( ! is_rtl() && 'left' == $content_align ) || ( is_rtl() && 'right' == $content_align ) ) {
				echo wolmart_escaped( $wishlist );
			}
			if ( 'product-11' != $product_type && ( ( ! is_rtl() && 'right' !== $content_align ) || ( is_rtl() && 'left' !== $content_align ) ) ) {
				echo wolmart_escaped( $compare );
			}

			echo '</div>';

		} elseif ( 'widget' == $product_type ) {

			woocommerce_template_loop_add_to_cart(
				array(
					'class' => implode(
						' ',
						array_filter(
							array(
								'btn-product',
								'product_type_' . $product->get_type(),
								$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
								$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
							)
						)
					),
				)
			);
		} else {
			if ( 'detail_bottom' == wolmart_wc_get_loop_prop( 'addtocart_pos' ) ) {
				echo '<div class="product-action">';
				woocommerce_template_loop_add_to_cart(
					array(
						'class' => implode(
							' ',
							array_filter(
								array(
									'btn-product',
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
								)
							)
						),
					)
				);
				echo '</div>';
			} elseif ( 'with_qty' == wolmart_wc_get_loop_prop( 'addtocart_pos' ) ) {
				echo '<div class="product-action">';

				if ( 'simple' == $product->get_type() ) {
					woocommerce_quantity_input(
						array(
							'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
							'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
							'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( sanitize_text_field( wp_unslash( $_POST['quantity'] ) ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
						)
					);
				}

				woocommerce_template_loop_add_to_cart(
					array(
						'class' => implode(
							' ',
							array_filter(
								array(
									'btn-product',
									'product_type_' . $product->get_type(),
									$product->is_purchasable() && $product->is_in_stock() ? 'add_to_cart_button' : '',
									$product->supports( 'ajax_add_to_cart' ) && $product->is_purchasable() && $product->is_in_stock() ? 'ajax_add_to_cart' : '',
								)
							)
						),
					)
				);
				echo '</div>';
			}
		}
	}
}
if ( ! function_exists( 'wolmart_product_list_loop_count' ) ) {
	function wolmart_product_list_loop_count() {
		$product_type = wolmart_wc_get_loop_prop( 'product_type' );

		if ( 'list' == $product_type ) {
			wolmart_product_loop_count();
		}
	}
}
if ( ! function_exists( 'wolmart_product_grid_loop_count' ) ) {
	function wolmart_product_grid_loop_count() {
		$product_type = wolmart_wc_get_loop_prop( 'product_type' );

		if ( 'list' != $product_type ) {
			wolmart_product_loop_count();
		}
	}
}

if ( ! function_exists( 'wolmart_product_loop_count' ) ) {
	function wolmart_product_loop_count() {
		$html          = '';
		$show_progress = wolmart_wc_get_loop_prop( 'show_progress', '' );
		$show_progress = ( 'false' === $show_progress || ! $show_progress ) ? false : true;
		$count_text    = wolmart_wc_get_loop_prop( 'count_text', esc_html( 'Sold: %s', 'wolmart' ) );

		if ( ( $show_progress && $count_text ) ) {
			global $product;
			$sales = $product->get_total_sales();
			$stock = $product->get_stock_quantity();
			$total = $sales + $stock;

			if ( $show_progress ) {
				?>
				<div class="count-progress"><div class="count-now" style="width:<?php echo ( ! $total && null == $stock ) ? 100 : intval( ( null == $stock ? $total : ( $total - $sales ) ) * 100 / $total ); ?>%;"></div></div>
				<?php
			}

			if ( $count_text ) {
				?>
				<div class="count-text">
					<span><?php printf( __( 'Available: %s', 'wolmart' ), null == $stock ? esc_html__( 'n/a', 'wolmart' ) : $stock ); ?></span>
					<span>
					<?php
					echo wolmart_strip_script_tags(
						apply_filters(
							'wolmart_product_loop_quantity_text',
							$stock ? sprintf( $count_text, $sales, $total ) : rtrim( sprintf( $count_text, $sales, '', $total ), '/' ),
							$product,
							$sales
						)
					);
					?>
					</span>
				</div>
				<?php
			}
		}
	}
}

if ( ! function_exists( 'wolmart_get_rating_html' ) ) {
	function wolmart_get_rating_html( $html, $rating, $count ) {
		if ( 0 == $rating ) {
			/* translators: %s: rating */
			$label = sprintf( esc_html__( 'Rated %s out of 5', 'wolmart' ), $rating );
			$html  = '<div class="star-rating" role="img" aria-label="' . esc_attr( $label ) . '">' . wc_get_star_rating_html( $rating, $count ) . '</div>';
		}
		return $html;
	}
}

if ( ! function_exists( 'wolmart_get_rating_link_html' ) ) {
	function wolmart_get_rating_link_html( $product ) {
		if ( 'product-10' == wolmart_wc_get_loop_prop( 'product_type' ) ) {
			return '<a href="' . esc_url( get_the_permalink( $product->get_id() ) ) . '#reviews" class="woocommerce-review-link scroll-to" rel="nofollow">' . $product->get_review_count() . '</a>';
		}
		return '<a href="' . esc_url( get_the_permalink( $product->get_id() ) ) . '#reviews" class="woocommerce-review-link scroll-to" rel="nofollow">' . $product->get_review_count() . ' ' . esc_html__( 'reviews', 'wolmart' ) . '</a>';
	}
}

/**
 * Wolmart Product Loop Hide Details (for classic type) Functions
 */
if ( ! function_exists( 'wolmart_product_loop_hide_details' ) ) {
	function wolmart_product_loop_hide_details() {
		if ( wolmart_wc_get_loop_prop( 'is_popup' ) && 'list' != wolmart_wc_get_loop_prop( 'product_type' ) ) {
			echo '<div class="product-hide-details">';
			do_action( 'wolmart_product_loop_hide_details', 'hide-details' );
			echo '</div>';
		}
	}
}

/**
 * Change order of del and ins tag.
 */
if ( ! function_exists( 'wolmart_wc_format_sale_price' ) ) {
	function wolmart_wc_format_sale_price( $price, $regular_price, $sale_price ) {
		return '<ins>' . ( is_numeric( $sale_price ) ? wc_price( $sale_price ) : $sale_price ) . '</ins> <del>' . ( is_numeric( $regular_price ) ? wc_price( $regular_price ) : $regular_price ) . '</del>';
	}
}

/**
 * Remove default YITH loop positions
 */
if ( ! function_exists( 'wolmart_yith_wcwl_loop_positions' ) ) {
	function wolmart_yith_wcwl_loop_positions( $positions ) {
		$positions['before_image']['hook']     = '';
		$positions['before_image']['priority'] = 10;
		return $positions;
	}
}

if ( ! function_exists( 'wolmart_yith_wcwl_add_btn_product_icon_class' ) ) {
	function wolmart_yith_wcwl_add_btn_product_icon_class( $args ) {
		$args['container_classes'] .= ' btn-product-icon';
		return $args;
	}
}

/**
 * Wolmart product compare function
 */
if ( ! function_exists( 'wolmart_product_compare' ) ) {
	function wolmart_product_compare( $extra_class = '' ) {
		if ( ! class_exists( 'Wolmart_Product_Compare' ) ) {
			return;
		}

		global $product;

		$css_class  = 'compare' . $extra_class;
		$product_id = $product->get_id();
		$url        = '#';

		if ( Wolmart_Product_Compare::get_instance()->is_compared_product( $product_id ) ) {
			$url         = get_permalink( wc_get_page_id( 'compare' ) );
			$css_class  .= ' added';
			$button_text = apply_filters( 'wolmart_woocompare_added_label', esc_html__( 'Added', 'wolmart' ) );
		} else {
			$button_text = apply_filters( 'wolmart_woocompare_add_label', esc_html__( 'Compare', 'wolmart' ) );
		}

		printf( '<a href="%s" class="%s" title="%s" data-product_id="%d"></a>', esc_url( $url ), esc_attr( $css_class ), esc_html( $button_text ), $product_id );
	}
}

/**
 * Product Listed Attributes (in archive loop and single)
 */
if ( ! function_exists( 'wolmart_wc_product_listed_attributes_html' ) ) {
	function wolmart_wc_product_listed_attributes_html( $attributes = '' ) {

		global $product;

		if ( 'variable' != $product->get_type() || ! $product->is_purchasable() ) {
			return;
		}

		$show_attrs      = '';
		$is_product_loop = false;

		// // Get attributes for loop product
		// if ( '' == $attributes ) {
		// 	$attributes         = $product->get_variation_attributes();
		// 	$is_product_loop    = true;
		// 	$theme_option_attrs = wolmart_get_option( 'product_show_attrs' );
		// } else {
		// 	// Print attributes
		// 	$theme_option_attrs = array();
		// 	foreach ( wc_get_attribute_taxonomies() as $key => $value ) {
		// 		$theme_option_attrs[] = 'pa_' . $value->attribute_name;
		// 	}
		// }

		if ( '' == $attributes ) {
			$attributes      = $product->get_variation_attributes();
			$is_product_loop = true;
		}

		// Print attributes
		$theme_option_attrs = array();
		foreach ( wc_get_attribute_taxonomies() as $key => $value ) {
			$theme_option_attrs[] = 'pa_' . $value->attribute_name;
		}
		ob_start();
		foreach ( $attributes as $attribute_name => $options ) {
			// wolmart_doing_quickview() || apply_filters( 'wolmart_is_single_product_widget', false ) || ( wolmart_is_product() && ! wc_get_loop_prop( 'name' ) ) ||
			if ( in_array( $attribute_name, $theme_option_attrs ) && apply_filters( 'wolmart_check_product_variation_type', true, $attribute_name ) ) {

				if ( 'pa_' == substr( $attribute_name, 0, 3 ) ) {
					$attribute_id = wc_attribute_taxonomy_id_by_name( $attribute_name );
				} else {
					$attribute_id = '';
				}

				if ( $attribute_id ) {
					$attribute_type = wc_get_attribute( $attribute_id )->type;
				} else {
					$attribute_type = 'select';
				}

				$terms = wc_get_product_terms(
					$product->get_id(),
					$attribute_name,
					array(
						'fields' => 'all',
					)
				);

				echo '<div class="product-variations ' . esc_attr( 'list' == $attribute_type ? 'list-type ' : 'dropdown-type ' ) . esc_attr( $terms ? $attribute_name : 'pa_custom_' . strtolower( $attribute_name ) ) . '" data-attr="' . esc_attr( $terms ? $attribute_name : 'pa_custom_' . strtolower( $attribute_name ) ) . '">';

				if ( ! empty( $options ) ) {
					if ( 'list' == $attribute_type ) {
						foreach ( $options as $term_id_or_slug ) {
							$term = get_term_by( is_numeric( $term_id_or_slug ) ? 'id' : 'slug', $term_id_or_slug, $attribute_name );

							if ( $term ) {
								$attr_label = sanitize_text_field( get_term_meta( $term->term_id, 'attr_label', true ) );
								$attr_color = sanitize_hex_color( get_term_meta( $term->term_id, 'attr_color', true ) );
							} else {
								$attr_label = $term_id_or_slug;
								$attr_color = '';
							}

							printf(
								'<button type="button" name="%s"%s title="%s">%s</button>',
								esc_attr( $term ? $term->slug : $term_id_or_slug ),
								apply_filters(
									'wolmart_wc_product_listed_attribute_attr',
									$attr_color ? ' class="color" style="background-color:' . esc_attr( $attr_color ) . ';color:' . esc_attr( $attr_color ) . '"' : '',
									$attribute_name,
									$term ? $term->term_id : $term_id_or_slug
								),
								esc_attr( $term ? $term->name : $term_id_or_slug ),
								$attr_label ? $attr_label : $term->name
							);
						}
					} elseif ( true == $is_product_loop ) {
						wc_dropdown_variation_attribute_options(
							array(
								'options'   => $options,
								'attribute' => $attribute_name,
								'product'   => $product,
								'type'      => $attribute_type,
							)
						);
					}
					do_action( 'wolmart_after_product_variation', $options, $attribute_name, $terms );
				}
				echo '</div>';
			}
		}
		$html = ob_get_clean();
		if ( $html && ( wolmart_is_shop() || wc_get_loop_prop( 'name' ) || apply_filters( 'wolmart_is_vendor_store', false ) ) ) {
			$variations_json = wp_json_encode( $product->get_available_variations() );
			$variations_attr = function_exists( 'wc_esc_json' ) ? wc_esc_json( $variations_json ) : _wp_specialchars( $variations_json, ENT_QUOTES, 'UTF-8', true );

			echo '<div class="product-variation-wrapper" data-product_variations="' . esc_attr( $variations_attr ) . '">' . wolmart_escaped( $html ) . '</div>';
		} else {
			echo wolmart_escaped( $html );
		}
	}
}

/**
 * Add exclude attribute to products shortcode.
 *
 * @since 1.0
 *
 * @param array  $out       The output array of shortcode attributes.
 * @param array  $pairs     The supported attributes and their defaults.
 * @param array  $atts      The user defined shortcode attributes.
 */
function wolmart_wc_shortcode_product_add_exclude_attribute( $out, $pairs, $atts ) {
	if ( isset( $atts['exclude'] ) ) {
		$out['exclude'] = $atts['exclude'];
	}
	return $out;
}

/**
 * Add exclude arg to woocommerce shortcode product.
 *
 * @since 1.0
 * @param array $query_args
 * @param array $attributes
 * @param string $type
 * @return array $query_args
 */
function wolmart_wc_shortcode_product_add_exclude_arg( $query_args, $attributes, $type ) {

	if ( ! empty( $attributes['exclude'] ) ) {
		$query_args['post__not_in'] = array_map( 'trim', explode( ',', $attributes['exclude'] ) );
	}
	return $query_args;
}


/**
 * Wolmart Cart Sticky Quantity HTML
 *
 * @since 1.4.0
 * @param $count
 */
if ( ! function_exists( 'wolmart_cart_sticky_quantity_html' ) ) {
	function wolmart_cart_sticky_quantity_html( $count ) {
		ob_start();
		?>
		<div class="product-sticky-cart-control <?php echo $count > 0 ? 'show qty-only' : ''; ?>">
			<button class="product-remove-cart" aria-label="<?php esc_attr_e( 'Remove from Cart', 'wolmart' ); ?>"><i class="w-icon-minus-solid"></i></button>
			<span class="product-sticky-cart-qty"><?php echo esc_html( $count > 0 ? $count : '1' ); ?></span>
			<button class="product-add-cart" aria-label="<?php esc_attr_e( 'Add to Cart', 'wolmart' ); ?>"><i class="w-icon-plus-solid"></i></button>
		</div>
		<?php

		return ob_get_clean();
	}
}
