<?php
/**
 * Wolmart WooCommerce Single Product Functions
 *
 * Functions used to display single product.
 */

defined( 'ABSPATH' ) || die;

// Compatiblilty with elementor editor
if ( ! empty( $_REQUEST['action'] ) && 'elementor' == $_REQUEST['action'] && is_admin() ) {
	if ( class_exists( 'WC_Template_Loader' ) ) {
		add_filter( 'woocommerce_product_tabs', array( 'WC_Template_Loader', 'unsupported_theme_remove_review_tab' ) );
		add_filter( 'woocommerce_product_tabs', 'woocommerce_default_product_tabs' );
		add_filter( 'woocommerce_product_tabs', 'woocommerce_sort_product_tabs', 99 );
	}
}

// Wolmart Single Product Navigation
add_filter( 'wolmart_breadcrumb_args', 'wolmart_single_prev_next_product' );

// Single Product Class
add_filter( 'wolmart_single_product_classes', 'wolmart_single_product_extend_class' );

// Single Product - Label, Sale Countdown
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
add_action( 'wolmart_before_wc_gallery_figure', 'woocommerce_show_product_sale_flash' );
add_action( 'woocommerce_available_variation', 'wolmart_variation_add_sale_ends', 100, 3 );

// Single Product - gallery type and sticky-both type
add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_wrap_special_start', 2 );
add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_wrap_special_end', 22 );
add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_wrap_special_start', 22 );
add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_wrap_special_before_end', 69 );
add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_wrap_special_end', 70 );

// Single Product - the other types except gallery and sticky-both types
add_action( 'woocommerce_before_single_product_summary', 'wolmart_single_product_wrap_first_start', 5 );
add_action( 'woocommerce_before_single_product_summary', 'wolmart_single_product_wrap_first_end', 30 );
add_action( 'woocommerce_before_single_product_summary', 'wolmart_single_product_wrap_second_start', 30 );
add_action( 'wolmart_after_product_summary_wrap', 'wolmart_single_product_wrap_second_end', 20 );

// Single Product - sticky-both type
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20 );
add_action( 'woocommerce_before_single_product_summary', 'wolmart_wc_show_product_images_not_sticky_both', 20 );
add_action( 'wolmart_before_product_summary', 'wolmart_wc_show_product_images_sticky_both', 5 );

// Single Product - sticky-info type
add_action( 'woocommerce_before_single_product_summary', 'wolmart_single_product_wrap_sticky_info_start', 40 );
add_action( 'wolmart_after_product_summary_wrap', 'wolmart_single_product_wrap_sticky_info_end', 15 );

// Remove default rendering of wishlist button by YITH
if ( class_exists( 'YITH_WCWL' ) || class_exists( 'YITH_WCWL_Frontend' ) ) {
	add_filter( 'yith_wcwl_show_add_to_wishlist', '__return_false', 20 );
}

// Single Product Media
add_filter( 'wolmart_wc_thumbnail_image_size', 'wolmart_single_product_thumbnail_image_size' );
add_filter( 'wolmart_product_label_group_class', 'wolmart_single_product_vertical_label_group_class' );
add_action( 'wolmart_woocommerce_product_images', 'wolmart_single_product_images' );
add_filter( 'woocommerce_single_product_image_gallery_classes', 'wolmart_single_product_wc_gallery_classes' );
add_filter( 'wolmart_single_product_gallery_main_classes', 'wolmart_single_product_gallery_classes' );
remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );
add_action( 'woocommerce_product_thumbnails', 'wolmart_wc_show_product_thumbnails', 20 );
add_filter( 'woocommerce_get_image_size_gallery_thumbnail', 'wolmart_wc_gallery_thumbnail_image_size' );

// Single Product Summary
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 7 );
add_filter( 'wolmart_single_product_summary_class', 'wolmart_single_product_summary_extend_class' );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price' );
add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 9 );
add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_sale_countdown', 9 );
add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_links_wrap_start', 45 );
if ( class_exists( 'YITH_WCWL_Frontend' ) ) {
	add_action( 'woocommerce_single_product_summary', 'wolmart_print_wishlist_button', 52 );
}
add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_compare', 54 );
add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_links_wrap_end', 55 );

// Single Product Form
add_filter( 'woocommerce_dropdown_variation_attribute_options_args', 'wolmart_wc_dropdown_variation_attribute_options_arg' );
add_filter( 'woocommerce_dropdown_variation_attribute_options_html', 'wolmart_wc_dropdown_variation_attribute_options_html', 10, 2 );
// add_action( 'woocommerce_before_add_to_cart_quantity', 'wolmart_single_product_divider', 10 );
add_action( 'woocommerce_before_add_to_cart_quantity', 'wolmart_single_product_sticky_cart_wrap_start', 15 );
add_action( 'woocommerce_after_add_to_cart_button', 'wolmart_single_product_sticky_cart_wrap_end', 20 );

// Single Product Data Tab
add_filter( 'wolmart_single_product_data_tab_type', 'wolmart_single_product_get_data_tab_type' );
add_filter( 'woocommerce_product_tabs', 'wolmart_wc_product_custom_tabs', 99 );

// Product Listed Attributes (in archive loop and single)
add_action( 'wolmart_wc_product_listed_attributes', 'wolmart_wc_product_listed_attributes_html' );

// Single Product Reviews Tab
add_action( 'woocommerce_review_before', 'wolmart_wc_review_before_avatar', 5 );
add_action( 'woocommerce_review_before', 'wolmart_wc_review_after_avatar', 15 );
add_filter( 'woocommerce_product_review_list_args', 'wolmart_product_show_newest_reviews' );
remove_action( 'woocommerce_review_before_comment_meta', 'woocommerce_review_display_rating' );
add_action( 'woocommerce_review_meta', 'woocommerce_review_display_rating', 15 );

// Single Product - Related Products
add_action( 'woocommerce_output_related_products_args', 'wolmart_related_products_args' );

// Single Product - Up-Sells Products
add_filter( 'woocommerce_upsell_display_args', 'wolmart_upsells_products_args' );

// Woocommerce Comment Form
add_filter( 'woocommerce_product_review_comment_form_args', 'wolmart_comment_form_args' );

// WooCommerce Single Product Notices
remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices', 10 );
add_action( 'woocommerce_before_single_product', 'wolmart_woocommerce_output_all_notices', 10 );


// Quickview ajax actions & enqueue scripts for quickview
if ( ! function_exists( 'wolmart_doing_quickview' ) ) {
	function wolmart_doing_quickview() {
		return apply_filters( 'wolmart_doing_quickview', wolmart_doing_ajax() && isset( $_REQUEST['action'] ) && 'wolmart_quickview' == $_REQUEST['action'] && isset( $_POST['product_id'] ) );
	}
}
if ( wolmart_doing_quickview() ) {
	add_action( 'wp_ajax_wolmart_quickview', 'wolmart_wc_quickview' );
	add_action( 'wp_ajax_nopriv_wolmart_quickview', 'wolmart_wc_quickview' );
} elseif ( 'disable' != wolmart_get_option( 'quickview_thumbs' ) ) {
	add_action( 'wp_enqueue_scripts', 'wolmart_quickview_add_scripts' );
}

/**
 * Wolmart Single Product Class & Layout
 */
if ( ! function_exists( 'wolmart_single_product_extend_class' ) ) {
	function wolmart_single_product_extend_class( $classes ) {
		$single_product_layout = wolmart_get_single_product_layout();

		if ( 'gallery' != $single_product_layout ) {
			if ( 'sticky-both' == $single_product_layout ) {
				$classes[] = 'sticky-both';
			} else {
				if ( 'sticky-info' == $single_product_layout ) {
					$classes[] = 'sticky-info';
				}
				if ( 'sticky-thumbs' == $single_product_layout ) {
					$classes[] = 'sticky-thumbs';
				}
				if ( ! wolmart_doing_ajax() ) {
					$classes[] = 'row';
				}
			}
		}

		if ( true == wolmart_get_option( 'same_vendor_products' ) ) {
			$classes[] = 'vendor-products';
		}

		return $classes;
	}
}

if ( ! function_exists( 'wolmart_get_single_product_layout' ) ) {
	function wolmart_get_single_product_layout() {
		global $wolmart_layout;

		if ( wolmart_doing_ajax() ) {
			$layout = '';
			if ( 'offcanvas' != wolmart_get_option( 'quickview_type' ) ) {
				$layout = wolmart_get_option( 'quickview_thumbs' );
			}
			if ( ! $layout ) {
				$layout = 'horizontal';
			}
		} else {
			$layout = empty( $wolmart_layout['single_product_type'] ) ? 'horizontal' : $wolmart_layout['single_product_type'];
		}

		return apply_filters( 'wolmart_single_product_layout', $layout );
	}
}

/**
 * Get single product layout
 */
if ( ! function_exists( 'wolmart_wc_show_product_images_not_sticky_both' ) ) {
	function wolmart_wc_show_product_images_not_sticky_both() {
		if ( 'sticky-both' != wolmart_get_single_product_layout() ) {
			woocommerce_show_product_images();

			if ( 'gallery' != wolmart_get_single_product_layout() && true == wolmart_get_option( 'same_vendor_products' ) ) {
				wolmart_single_product_vendor_products();
			}
		}
	}
}

if ( ! function_exists( 'wolmart_wc_show_product_images_sticky_both' ) ) {
	function wolmart_wc_show_product_images_sticky_both() {
		if ( 'sticky-both' == wolmart_get_single_product_layout() ) {
			woocommerce_show_product_images();
		}
	}
}


/**
 * Wolmart Single Product - Gallery Image Functions
 */
if ( ! function_exists( 'wolmart_wc_get_gallery_image_html' ) ) {
	/**
	 * Get html of single product gallery image
	 *
	 * @since 1.0
	 * @param int $attachment_id        Image ID
	 * @param boolean $main_image       True if large image is needed
	 * @param boolean $featured_image   True if attachment is featured image
	 * @param boolean $is_thumbnail     True if thumb wrapper is needed
	 * @return string image html
	 */
	function wolmart_wc_get_gallery_image_html( $attachment_id, $main_image = false, $featured_image = false, $is_thumbnail = true ) {

		if ( $main_image ) {
			// Get large image

			$image_size    = apply_filters( 'woocommerce_gallery_image_size', 'woocommerce_single' );
			$full_size     = apply_filters( 'woocommerce_gallery_full_size', apply_filters( 'woocommerce_product_thumbnails_large_size', 'full' ) );
			$thumbnail_src = wp_get_attachment_image_src( $attachment_id, 'woocommerce_single' );
			$full_src      = wp_get_attachment_image_src( $attachment_id, $full_size );
			$alt_text      = trim( wp_strip_all_tags( get_post_meta( $attachment_id, '_wp_attachment_image_alt', true ) ) );
			$image         = wp_get_attachment_image(
				$attachment_id,
				$image_size,
				false,
				apply_filters(
					'woocommerce_gallery_image_html_attachment_image_params',
					array(
						'title'                   => _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						'data-caption'            => _wp_specialchars( get_post_field( 'post_excerpt', $attachment_id ), ENT_QUOTES, 'UTF-8', true ),
						// 'data-src'                => esc_url( ! empty( $full_src ) ? $full_src[0] : '' ),
						'data-large_image'        => esc_url( ! empty( $full_src[0] ) ? $full_src[0] : '' ),
						'data-large_image_width'  => isset( $full_src[1] ) ? $full_src[1] : '',
						'data-large_image_height' => isset( $full_src[2] ) ? $full_src[2] : '',
						'class'                   => $featured_image ? 'wp-post-image' : '',
					),
					$attachment_id,
					$image_size,
					$main_image
				)
			);

			if ( $is_thumbnail ) {
				$image = '<div data-thumb="' . esc_url( ! empty( $thumbnail_src[0] ) ? $thumbnail_src[0] : '' ) . ( $alt_text ? '" data-thumb-alt="' . esc_attr( $alt_text ) : '' ) . '" class="woocommerce-product-gallery__image"><a href="' . esc_url( ! empty( $full_src[0] ) ? $full_src[0] : '' ) . '" aria-label="' . esc_html__( 'Product Image', 'wolmart' ) . '">' . $image . '</a></div>';
			}
		} else {
			// Get small image

			$thumbnail_size = apply_filters( 'wolmart_wc_thumbnail_image_size', 'woocommerce_thumbnail' );

			if ( $attachment_id ) {
				// If default or horizontal layout, print simple image tag
				$gallery_thumbnail = false;
				if ( 'wolmart-product-thumbnail' == $thumbnail_size ) {
					$image_sizes = wp_get_additional_image_sizes();
					if ( isset( $image_sizes[ $thumbnail_size ] ) ) {
						$gallery_thumbnail = $image_sizes[ $thumbnail_size ];
					}
				}
				if ( ! $gallery_thumbnail ) {
					$gallery_thumbnail = wc_get_image_size( $thumbnail_size );
				}

				if ( 0 == $gallery_thumbnail['height'] ) {
					$full_image_size = wp_get_attachment_image_src( $attachment_id, 'full' );
					if ( isset( $full_image_size[1] ) && $full_image_size[1] ) {
						$gallery_thumbnail['height'] = intval( $gallery_thumbnail['width'] / absint( $full_image_size[1] ) * absint( $full_image_size[2] ) );
					}
				}
				$thumbnail_size = apply_filters( 'woocommerce_gallery_thumbnail_size', array( $gallery_thumbnail['width'], $gallery_thumbnail['height'] ) );
				$image_src      = wp_get_attachment_image_src( $attachment_id, $thumbnail_size );
				$image          = '<img alt="' . esc_attr( _wp_specialchars( get_post_field( 'post_title', $attachment_id ), ENT_QUOTES, 'UTF-8', true ) ) . '" src="' . esc_url( ! empty( $image_src[0] ) ? $image_src[0] : '' ) . '" width="' . (int) ( ! empty( $thumbnail_size[0] ) ? $thumbnail_size[0] : '' ) . '" height="' . (int) ( ! empty( $thumbnail_size[1] ) ? $thumbnail_size[1] : '' ) . '">';

			} else {
				$image = '';
			}

			if ( $is_thumbnail && $image ) {
				$image = '<div class="product-thumb' . ( $featured_image ? ' active' : '' ) . '">' . $image . '</div>';
			}
		}
		return apply_filters( 'wolmart_wc_get_gallery_image_html', $image );
	}
}

if ( ! function_exists( 'wolmart_single_product_thumbnail_image_size' ) ) {
	function wolmart_single_product_thumbnail_image_size( $image ) {
		if ( wolmart_is_product() ) {
			return 'wolmart-product-thumbnail';
		}
	}
}


/**
 * Wolmart Single Product Navigation
 */
if ( ! function_exists( 'wolmart_single_product_navigation' ) ) {
	function wolmart_single_product_navigation() {
		global $post;
		$prev_post = get_previous_post( true, '', 'product_cat' );
		$next_post = get_next_post( true, '', 'product_cat' );
		$html      = '';

		if ( is_a( $prev_post, 'WP_Post' ) || is_a( $next_post, 'WP_Post' ) ) {
			$html .= '<ul class="product-nav">';

			if ( is_a( $prev_post, 'WP_Post' ) ) {
				$html             .= '<li class="product-nav-prev">';
					$html         .= '<a href="' . esc_url( get_the_permalink( $prev_post->ID ) ) . '" aria-label="' . esc_html__( 'Prev', 'wolmart' ) . '" rel="prev"><i class="w-icon-angle-' . ( is_rtl() ? 'right' : 'left' ) . '"></i>';
						$html     .= '<span class="product-nav-popup">';
							$html .= wolmart_strip_script_tags( get_the_post_thumbnail( $prev_post->ID, apply_filters( 'woocommerce_gallery_thumbnail_size', 'woocommerce_gallery_thumbnail' ) ) );
							$html .= '<span>' . esc_attr( get_the_title( $prev_post->ID ) ) . '</span>';
				$html             .= '</span></a></li>';
			}
			if ( is_a( $next_post, 'WP_Post' ) ) {
				$html             .= '<li class="product-nav-next">';
					$html         .= '<a href="' . esc_url( get_the_permalink( $next_post->ID ) ) . '" aria-label="' . esc_html__( 'Next', 'wolmart' ) . '" rel="next"><i class="w-icon-angle-' . ( is_rtl() ? 'left' : 'right' ) . '"></i>';
						$html     .= ' <span class="product-nav-popup">';
							$html .= wolmart_strip_script_tags( get_the_post_thumbnail( $next_post->ID, apply_filters( 'woocommerce_gallery_thumbnail_size', 'woocommerce_gallery_thumbnail' ) ) );
							$html .= '<span>' . esc_attr( get_the_title( $next_post->ID ) ) . '</span>';
				$html             .= '</span></a></li>';
			}

			$html .= '</ul>';
		}
		return apply_filters( 'wolmart_single_product_navigation', $html );
	}
}

if ( ! function_exists( 'wolmart_single_prev_next_product' ) ) {
	function wolmart_single_prev_next_product( $args ) {
		if ( 'single_product' == wolmart_get_page_layout() ) {
			$args['wrap_before'] = '<div class="product-navigation">' . $args['wrap_before'];
			$args['wrap_after'] .= wolmart_single_product_navigation() . '</div>';
		}
		return apply_filters( 'wolmart_filter_single_prev_next_product', $args );
	}
}

/**
 * Wolmart Single Product Layout Functions
 */
if ( ! function_exists( 'wolmart_single_product_wrap_special_start' ) ) {
	function wolmart_single_product_wrap_special_start() {

		$single_product_layout = wolmart_get_single_product_layout();

		if ( 'gallery' == $single_product_layout || 'sticky-both' == $single_product_layout ) {
			$wrap_class = 'col-md-6';

			if ( 'sticky-both' == $single_product_layout ) {
				$wrap_class .= ' col-lg-3';
			}

			echo '<div class="' . esc_attr( $wrap_class ) . '">';

			if ( 'sticky-both' == $single_product_layout ) {
				wp_enqueue_script( 'wolmart-sticky-lib' );
				echo '<div class="sticky-sidebar">';
			}
		}
	}
}
if ( ! function_exists( 'wolmart_single_product_wrap_special_end' ) ) {
	function wolmart_single_product_wrap_special_end() {

		$single_product_layout = wolmart_get_single_product_layout();

		if ( 'gallery' == $single_product_layout || 'sticky-both' == $single_product_layout ) {
			if ( 'sticky-both' == $single_product_layout ) {
				echo '</div>';
			}

			echo '</div>';
		}
	}
}
if ( ! function_exists( 'wolmart_single_product_wrap_first_start' ) ) {
	function wolmart_single_product_wrap_first_start() {

		$single_product_layout = wolmart_get_single_product_layout();

		if ( ( ( wolmart_doing_ajax() && 'offcanvas' != wolmart_get_option( 'quickview_type' ) ) ||
			( ! wolmart_doing_ajax() || ( function_exists( 'wolmart_is_elementor_preview' ) && wolmart_is_elementor_preview() ) ) ) &&
			'gallery' != $single_product_layout && 'sticky-both' != $single_product_layout ) {

			echo 'grid' == $single_product_layout ? '<div class="col-md-7">' : '<div class="col-md-6">';
		}
	}
}
if ( ! function_exists( 'wolmart_single_product_wrap_first_end' ) ) {
	function wolmart_single_product_wrap_first_end() {

		$single_product_layout = wolmart_get_single_product_layout();

		if ( ( ( wolmart_doing_ajax() && 'offcanvas' != wolmart_get_option( 'quickview_type' ) ) || ( ! wolmart_doing_ajax() || wolmart_is_elementor_preview() ) ) && 'gallery' != $single_product_layout && 'sticky-both' != $single_product_layout ) {
			echo '</div>';
		}
	}
}
if ( ! function_exists( 'wolmart_single_product_wrap_second_start' ) ) {
	function wolmart_single_product_wrap_second_start() {

		$single_product_layout = wolmart_get_single_product_layout();

		if ( ( ( wolmart_doing_ajax() && 'offcanvas' != wolmart_get_option( 'quickview_type' ) ) || ( ! wolmart_doing_ajax() || wolmart_is_elementor_preview() ) ) && 'gallery' != $single_product_layout && 'sticky-both' != $single_product_layout ) {
			echo 'grid' == $single_product_layout ? '<div class="col-md-5">' : '<div class="col-md-6">';
		}
	}
}
if ( ! function_exists( 'wolmart_single_product_wrap_second_end' ) ) {
	function wolmart_single_product_wrap_second_end() {

		$single_product_layout = wolmart_get_single_product_layout();

		if ( ( ( wolmart_doing_ajax() && 'offcanvas' != wolmart_get_option( 'quickview_type' ) ) || ( ! wolmart_doing_ajax() || wolmart_is_elementor_preview() ) ) && 'gallery' != $single_product_layout && 'sticky-both' != $single_product_layout ) {
			echo '</div>';
		}
	}
}
if ( ! function_exists( 'wolmart_single_product_wrap_sticky_info_start' ) ) {
	function wolmart_single_product_wrap_sticky_info_start() {
		$layout = wolmart_get_single_product_layout();
		if ( 'sticky-info' == $layout || 'sticky-thumbs' == $layout || 'grid' == $layout || 'masonry' == $layout ) {
			wp_enqueue_script( 'wolmart-sticky-lib' );
			echo '<div class="sticky-sidebar" data-sticky-options="{\'minWidth\': 767}">';
		}
	}
}
if ( ! function_exists( 'wolmart_single_product_wrap_sticky_info_end' ) ) {
	function wolmart_single_product_wrap_sticky_info_end() {
		$layout = wolmart_get_single_product_layout();
		if ( 'sticky-info' == $layout || 'sticky-thumbs' == $layout || 'grid' == $layout || 'masonry' == $layout ) {
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'wolmart_single_product_sticky_cart_wrap_start' ) ) {
	function wolmart_single_product_sticky_cart_wrap_start() {
		global $wolmart_layout, $product;
		if ( ! wolmart_doing_ajax() && ! $product->is_type( 'grouped' ) && apply_filters( 'wolmart_single_product_sticky_cart_enabled', ! empty( $wolmart_layout['single_product_sticky'] ) ) ) {
			echo '<div class="sticky-content product-sticky-content" data-sticky-options="{\'minWidth\':' . ( wolmart_get_option( 'single_product_sticky_mobile' ) ? '1' : '768' ) . ', \'scrollMode\': true}"><div class="container">';
		}
	}
}

/**
 * wolmart_single_product_sticky_cart_wrap_end
 *
 * @since 1.0
 * @since 1.1.10 - Fixed - single product sidebar not showing properly in external products.
 */
if ( ! function_exists( 'wolmart_single_product_sticky_cart_wrap_end' ) ) {
	function wolmart_single_product_sticky_cart_wrap_end() {
		global $wolmart_layout, $product;
		if ( ! wolmart_doing_ajax() && ! $product->is_type( 'grouped' ) && ! $product->is_type( 'external' ) && apply_filters( 'wolmart_single_product_sticky_cart_enabled', ! empty( $wolmart_layout['single_product_sticky'] ) ) ) {
			echo '</div></div>';
		}
	}
}

if ( ! function_exists( 'wolmart_single_product_sticky_both_class' ) ) {
	function wolmart_single_product_sticky_both_class( $classes ) {
		$classes[] = 'sticky-both';
		return $classes;
	}
}

/**
 * Wolmart Single Product Media Functions
 */
if ( ! function_exists( 'wolmart_single_product_vertical_label_group_class' ) ) {
	function wolmart_single_product_vertical_label_group_class( $class ) {
		if ( 'vertical' == wolmart_get_single_product_layout() && (
			wolmart_doing_quickview() ||
			apply_filters( 'wolmart_is_single_product_widget', false ) ||
			( wolmart_is_product() && ! wolmart_wc_get_loop_prop( 'name' ) )
		) ) {

			$class .= ' pg-vertical-label';
		}
		return $class;
	}
}
if ( ! function_exists( 'wolmart_single_product_images' ) ) {
	function wolmart_single_product_images() {
		global $product;
		global $wolmart_layout;

		$single_product_layout = wolmart_get_single_product_layout();
		$post_thumbnail_id     = $product->get_image_id();
		$attachment_ids        = $product->get_gallery_image_ids();

		if ( $post_thumbnail_id ) {
			$html = apply_filters( 'woocommerce_single_product_image_thumbnail_html', wolmart_wc_get_gallery_image_html( $post_thumbnail_id, true, true ), $post_thumbnail_id );
		} else {
			$wrapper_classname = $product->is_type( 'variable' ) && ! empty( $product->get_available_variations( 'image' ) ) ?
				'woocommerce-product-gallery__image woocommerce-product-gallery__image--placeholder' :
				'woocommerce-product-gallery__image--placeholder';
			$html              = sprintf( '<div class="%s">', esc_attr( $wrapper_classname ) );
			$html             .= sprintf( '<img src="%s" alt="%s" class="wp-post-image">', esc_url( wc_placeholder_img_src( 'woocommerce_single' ) ), esc_html__( 'Awaiting product image', 'woocommerce' ) );
			$html             .= '</div>';
		}

		if ( $single_product_layout ) {
			if ( $attachment_ids && $post_thumbnail_id ) {
				foreach ( $attachment_ids as $attachment_id ) {
					$html .= apply_filters( 'woocommerce_single_product_image_thumbnail_html', wolmart_wc_get_gallery_image_html( $attachment_id, true ), $attachment_id );
				}
			}
		}
		if ( 'vertical' == $single_product_layout || 'horizontal' == $single_product_layout ) {
			$html = '<div class="product-single-carousel-wrap slider-nav-fade"><div class="product-single-carousel slider-wrapper row cols-1 gutter-no">' . $html . '</div></div>';
		} elseif ( 'gallery' == $single_product_layout ) {
			$cols     = ! empty( $wolmart_layout['left_sidebar'] ) && 'hide' != $wolmart_layout['left_sidebar'] &&
						( empty( $wolmart_layout['left_sidebar_type'] ) || 'offcanvas' != $wolmart_layout['left_sidebar_type'] )
						||
						! empty( $wolmart_layout['right_sidebar'] ) && 'hide' != $wolmart_layout['right_sidebar'] &&
						( empty( $wolmart_layout['right_sidebar_type'] ) || 'offcanvas' != $wolmart_layout['right_sidebar_type'] )
						? 2 : 3;
			$cols_cnt = wolmart_get_responsive_cols(
				array(
					'lg'  => (int) $cols,
					'min' => 1,
					'sm'  => 2,
				)
			);
			$cols_cnt = apply_filters( 'wolmart_single_product_gallery_columns', $cols_cnt );

			$attr = apply_filters( 'wolmart_single_product_gallery_type_attr', '' );
			if ( false === strpos( $attr, 'data-slider-options=' ) ) {
				$attr .= ' data-slider-options="' . esc_attr(
					json_encode(
						wolmart_get_slider_attrs( array(), $cols_cnt )
					)
				) . '"';
			}
			//wolmart_get_col_class
			$html = '<div class="product-gallery-carousel slider-wrapper' .
				apply_filters(
					'wolmart_single_product_gallery_type_class',
					wolmart_get_col_class( $cols_cnt )
				)
				. '" data-slider-status="slider-same-height slider-nav-inner slider-nav-fade"' . $attr . '>' . $html . '</div>';
		} elseif ( 'sticky-thumbs' == $single_product_layout ) {
			$html = '<div class="product-sticky-images">' . $html . '</div>';
		}
		echo wolmart_escaped( $html );
	}
}

if ( ! function_exists( 'wolmart_single_product_wc_gallery_classes' ) ) {
	function wolmart_single_product_wc_gallery_classes( $classes ) {
		$single_product_layout = wolmart_get_single_product_layout();
		if ( 'sticky-both' == $single_product_layout ) {
			$classes[] = 'col-lg-6';
		}
		return $classes;
	}
}

if ( ! function_exists( 'wolmart_single_product_gallery_classes' ) ) {
	function wolmart_single_product_gallery_classes( $classes ) {
		$single_product_layout = wolmart_get_single_product_layout();
		$classes[]             = 'product-gallery';

		if ( 'vertical' == $single_product_layout ) {
			wp_enqueue_script( 'swiper' );
			$classes[] = 'pg-vertical';
		} elseif ( 'horizontal' == $single_product_layout ) {
			wp_enqueue_script( 'swiper' );
		} elseif ( 'gallery' == $single_product_layout ) {
			wp_enqueue_script( 'swiper' );
			$classes[] = 'pg-gallery';
		} elseif ( 'grid' == $single_product_layout ) {
			$classes[] = 'row';
			$classes[] = 'cols-sm-2';
		} elseif ( 'sticky-both' == $single_product_layout ) {
			$classes[] = 'row';
			$classes[] = 'cols-sm-2 cols-lg-1';
		} elseif ( 'masonry' == $single_product_layout ) {
			$classes[] = 'row';
			$classes[] = 'cols-sm-2';
			$classes[] = 'product-masonry-type';
		} elseif ( 'sticky-info' == $single_product_layout ) {
			$classes[] = 'row';
			$classes[] = 'gutter-no';
		}
		return $classes;
	}
}

if ( ! function_exists( 'wolmart_wc_show_product_thumbnails' ) ) {
	function wolmart_wc_show_product_thumbnails() {

		$single_product_layout = wolmart_get_single_product_layout();

		if ( 'vertical' == $single_product_layout || 'horizontal' == $single_product_layout ) {
			?>
			<div class="product-thumbs-wrap">
				<div class="product-thumbs slider-wrapper row gutter-no">
					<?php woocommerce_show_product_thumbnails(); ?>
				</div>
			</div>
			<?php
		} elseif ( 'sticky-thumbs' == $single_product_layout ) {
			wp_enqueue_script( 'wolmart-sticky-lib' );
			?>
			<div class="product-sticky-thumbs">
				<div class="product-sticky-thumbs-inner sticky-sidebar" data-sticky-options="{'minWidth': 319}">
					<?php woocommerce_show_product_thumbnails(); ?>
				</div>
			</div>
			<?php
		}
	}
}

if ( ! function_exists( 'wolmart_wc_gallery_thumbnail_image_size' ) ) {
	function wolmart_wc_gallery_thumbnail_image_size( $size ) {

		$single_product_layout = wolmart_get_single_product_layout();

		if ( 'vertical' == $single_product_layout || 'horizontal' == $single_product_layout ) {
			$size['width']  = 150;
			$size['height'] = 150;
		}
		return $size;
	}
}

/**
 * Wolmart Single Product Meta - Social Sharing Wrapper Functions
 */
if ( ! function_exists( 'wolmart_single_product_ms_wrap_start' ) ) {
	function wolmart_single_product_ms_wrap_start() {
		echo '<div class="product-ms-wrapper">';
	}
}

if ( ! function_exists( 'wolmart_single_product_ms_wrap_end' ) ) {
	function wolmart_single_product_ms_wrap_end() {
		echo '</div>';
	}
}

/**
 * Wolmart Single Product Summary Functions
 */
if ( ! function_exists( 'wolmart_single_product_sale_countdown' ) ) {

	/**
	 * Display sale countdown for simple & variable product in single product page.
	 *
	 * @since 1.0
	 * @param string $ends_label
	 * @return void
	 */
	function wolmart_single_product_sale_countdown( $ends_label = '' ) {

		global $product;

		if ( $product->is_on_sale() ) {

			$extra_class = '';

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
					$extra_class .= ' countdown-variations';
					$date_diff    = date( 'Y/m/d H:i:s', (int) $sale_date );
				}
			} else {
				$date_diff = $product->get_date_on_sale_to();
				if ( $date_diff ) {
					$date_diff = $date_diff->date( 'Y/m/d H:i:s' );
				}
			}

			if ( $date_diff ) {
				wp_enqueue_script( 'jquery-countdown' );
				?>
				<div class="product-countdown-container<?php echo esc_attr( $extra_class ); ?>">
					<?php echo empty( $ends_label ) ? esc_html__( 'Offer Ends In:', 'wolmart' ) : esc_html( $ends_label ); ?>
					<div class="countdown product-countdown countdown-compact" data-until="<?php echo esc_attr( $date_diff ); ?>" data-compact="true">0<?php echo esc_html__( 'days', 'wolmart' ); ?>, 00 : 00 : 00</div>
				</div>
				<?php
			}
		}
	}
}

if ( ! function_exists( 'wolmart_variation_add_sale_ends' ) ) {
	/**
	 * Single Product Sale Countdown for variable product.
	 *
	 * @since 1.0
	 * @param array $vars
	 * @param object $product
	 * @param object $variation
	 * @return array $vars
	 */
	function wolmart_variation_add_sale_ends( $vars, $product, $variation ) {

		if ( $variation->is_on_sale() ) {
			$date_diff = $variation->get_date_on_sale_to();
			if ( $date_diff ) {
				$vars['wolmart_date_on_sale_to'] = $date_diff->date( 'Y/m/d H:i:s' );
			}
		}
		return $vars;
	}
}

if ( ! function_exists( 'wolmart_single_product_summary_extend_class' ) ) {
	function wolmart_single_product_summary_extend_class( $class ) {
		$single_product_layout = wolmart_get_single_product_layout();
		if ( 'gallery' == $single_product_layout || 'sticky-both' == $single_product_layout ) {
			$class .= ' row';
		} elseif ( wolmart_doing_ajax() ) {
			$class .= ' scrollable';
		}
		return $class;
	}
}

if ( ! function_exists( 'wolmart_single_product_divider' ) ) {
	function wolmart_single_product_divider() {
		echo apply_filters( 'wolmart_single_product_divider', '<hr class="product-divider">' );
	}
}

/**
 * Wolmart Single Product Form Functions
 */
if ( ! function_exists( 'wolmart_wc_dropdown_variation_attribute_options_arg' ) ) {
	function wolmart_wc_dropdown_variation_attribute_options_arg( $args ) {
		// Select Box
		if ( isset( $args['type'] ) && 'select' == $args['type'] ) {
			$args['class'] = isset( $args['class'] ) ? $args['class'] . ' form-control' : 'form-control';
		}
		return $args;
	}
}

if ( ! function_exists( 'wolmart_wc_dropdown_variation_attribute_options_html' ) ) {
	function wolmart_wc_dropdown_variation_attribute_options_html( $html, $args ) {
		if ( isset( $args['type'] ) && 'select' == $args['type'] ) {
			$html = '<div class="select-box">' . $html . '</div>';
		}

		$html = str_replace( '<select id="pa_', '<select data-id="pa_', $html );
		return $html;
	}
}

/**
 * Wolmart Single Product Data Tab Functions
 */
if ( ! function_exists( 'wolmart_single_product_get_data_tab_type' ) ) {
	function wolmart_single_product_get_data_tab_type( $tabs ) {
		global $wolmart_layout;
		if ( isset( $wolmart_layout['product_data_type'] ) ) {
			if ( 'accordion' == $wolmart_layout['product_data_type'] ) {
				return 'accordion';
			} elseif ( 'section' == $wolmart_layout['product_data_type'] ) {
				return 'section';
			}
		}
		return 'tab';
	}
}

if ( ! function_exists( 'wolmart_wc_product_custom_tabs' ) ) {
	function wolmart_wc_product_custom_tabs( $tabs ) {

		// Show reviews at last
		if ( isset( $tabs['reviews'] ) ) {
			$tabs['reviews']['priority'] = 999;
		}

		// Change default titles
		if ( isset( $tabs['description'] ) && isset( $tabs['description']['title'] ) ) {
			$tabs['description']['title'] = wolmart_get_option( 'product_description_title' );
		}

		if ( isset( $tabs['additional_information'] ) && isset( $tabs['additional_information']['title'] ) ) {
			$tabs['additional_information']['title'] = wolmart_get_option( 'product_specification_title' );
		}
		if ( isset( $tabs['reviews'] ) && isset( $tabs['reviews']['title'] ) ) {
			$tabs['reviews']['title'] = wolmart_get_option( 'product_reviews_title' ) . ' <span>(' . $GLOBALS['product']->get_review_count() . ')</span>';
		}
		if ( isset( $tabs['seller'] ) && isset( $tabs['seller']['title'] ) ) {
			$tabs['seller']['title'] = wolmart_get_option( 'product_vendor_info_title' );
		}
		if ( isset( $tabs['vendor'] ) && isset( $tabs['vendor']['title'] ) ) {
			$tabs['vendor']['title'] = wolmart_get_option( 'product_vendor_info_title' );
		}

		// Global tab
		$title = wolmart_get_option( 'product_tab_title' );
		if ( $title ) {
			$tabs['wolmart_product_tab'] = array(
				'title'    => sanitize_text_field( $title ),
				'priority' => 24,
				'callback' => 'wolmart_wc_product_custom_tab',
			);
		}

		// Custom tab for current product
		$title = get_post_meta( get_the_ID(), 'wolmart_custom_tab_title_1st', true );
		if ( $title ) {
			$tabs['wolmart_custom_tab_1st'] = array(
				'title'    => sanitize_text_field( $title ),
				'priority' => 26,
				'callback' => 'wolmart_wc_product_custom_tab',
			);
		}
		$title = get_post_meta( get_the_ID(), 'wolmart_custom_tab_title_2nd', true );
		if ( $title ) {
			$tabs['wolmart_custom_tab_2nd'] = array(
				'title'    => sanitize_text_field( $title ),
				'priority' => 26,
				'callback' => 'wolmart_wc_product_custom_tab',
			);
		}

		// Guide block
		global $product;
		if ( 'variable' == $product->get_type() ) {
			$attributes        = $product->get_attributes();
			$wolmart_pa_blocks = get_option( 'wolmart_pa_blocks' );

			foreach ( $attributes as $key => $attribute ) {
				$name = substr( $key, 3 );
				if ( isset( $wolmart_pa_blocks[ $name ] ) &&
					isset( $wolmart_pa_blocks[ $name ]['block'] ) && $wolmart_pa_blocks[ $name ]['block'] &&
					isset( $wolmart_pa_blocks[ $name ]['text'] ) && $wolmart_pa_blocks[ $name ]['text'] ) {

					$tabs[ 'wolmart_pa_block_' . $name ] = apply_filters(
						"wolmart_product_attribute_{$name}_guide",
						array(
							'title'    => sanitize_text_field( $wolmart_pa_blocks[ $name ]['text'] ),
							'priority' => 28,
							'callback' => 'wolmart_wc_product_custom_tab',
							'block_id' => absint( $wolmart_pa_blocks[ $name ]['block'] ),
						)
					);
				}
			}
		}

		return $tabs;
	}
}

if ( ! function_exists( 'wolmart_wc_product_custom_tab' ) ) {
	function wolmart_wc_product_custom_tab( $key, $product_tab ) {
		wc_get_template(
			'single-product/tabs/custom_tab.php',
			array(
				'tab_name' => $key,
				'tab_data' => $product_tab,
			)
		);
	}
}

/**
 * Change default YITH positions
 */
if ( ! function_exists( 'wolmart_yith_wcwl_positions' ) ) {
	function wolmart_yith_wcwl_positions( $position ) {
		$position['after_add_to_cart']['priority'] = 10;
		$position['after_add_to_cart']['hook']     = 'woocommerce_after_add_to_cart_button';

		$position['add-to-cart']['priority'] = 10;
		$position['add-to-cart']['hook']     = 'woocommerce_after_add_to_cart_button';

		$position['thumbnails']['priority'] = 10;
		$position['thumbnails']['hook']     = 'woocommerce_after_add_to_cart_button';

		$position['summary']['priority'] = 10;
		$position['summary']['hook']     = 'woocommerce_after_add_to_cart_button';

		return $position;
	}
}

/**
 * Single Product Reviews Tab
 */
if ( ! function_exists( 'wolmart_wc_review_before_avatar' ) ) {
	function wolmart_wc_review_before_avatar() {
		echo '<figure class="comment-avatar">';
	}
}
if ( ! function_exists( 'wolmart_wc_review_after_avatar' ) ) {
	function wolmart_wc_review_after_avatar() {
		echo '</figure>';
	}
}

if ( ! function_exists( 'wolmart_product_show_newest_reviews' ) ) {
	function wolmart_product_show_newest_reviews( $args ) {
		$args['reverse_top_level'] = true;
		return $args;
	}
}

if ( ! function_exists( 'wolmart_related_products_args' ) ) {
	/**
	 * Wolmart Single Product - Related Products Functions
	 *
	 * @since 1.0
	 * @param array $args
	 * @return array $args
	 */
	function wolmart_related_products_args( $args = array() ) {
		$count    = wolmart_get_option( 'product_related_count' );
		$orderby  = wolmart_get_option( 'product_related_order' );
		$orderway = wolmart_get_option( 'product_related_orderway' );
		if ( $count ) {
			$args['posts_per_page'] = $count;
		}
		if ( $orderby ) {
			$args['orderby'] = $orderby;
		}
		if ( $orderway ) {
			$args['orderway'] = $orderway;
		}
		return $args;
	}
}

if ( ! function_exists( 'wolmart_upsells_products_args' ) ) {
	/**
	 * Wolmart Single Product - Up-Sells Products Functions
	 *
	 * @since 1.0
	 * @param array $args
	 * @return array $args
	 */
	function wolmart_upsells_products_args( $args = array() ) {
		$count    = wolmart_get_option( 'product_upsells_count' );
		$orderby  = wolmart_get_option( 'product_upsells_order' );
		$orderway = wolmart_get_option( 'product_upsells_orderway' );
		if ( $count ) {
			$args['posts_per_page'] = $count;
		}
		if ( $orderby ) {
			$args['orderby'] = $orderby;
		}
		if ( $orderway ) {
			$args['orderway'] = $orderway;
		}
		return $args;
	}
}

/**
 * Wolmart Quickview Ajax Actions
 */
if ( ! function_exists( 'wolmart_wc_quickview' ) ) {
	function wolmart_wc_quickview() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		if ( ! has_action( 'woocommerce_single_product_summary', 'wolmart_single_product_compare', 58 ) ) {
			add_action( 'woocommerce_single_product_summary', 'wolmart_single_product_compare', 58 );
		}

		global $product, $post;
		$product_id = intval( $_POST['product_id'] );
		$post       = get_post( $product_id );
		$product    = wc_get_product( $product_id );

		if ( $product->is_type( 'variation' ) ) {
			$attrs = wc_get_product_variation_attributes( $post->ID );
			if ( ! empty( $attrs ) ) {
				foreach ( $attrs as $key => $val ) {
					$_REQUEST[ $key ] = $val;
				}
			}
			$parent_id = wp_get_post_parent_id( $post );
			if ( $parent_id ) {
				$post    = get_post( (int) $parent_id );
				$product = wc_get_product( $post->ID );
			}
		}

		wc_get_template_part( 'content', 'single-product' );
		// phpcs:enable
		die;
	}
}

if ( ! function_exists( 'wolmart_quickview_add_scripts' ) ) {
	function wolmart_quickview_add_scripts() {
		wp_enqueue_script( 'swiper' );
		wp_enqueue_script( 'jquery-magnific-popup' );
		wp_enqueue_script( 'jquery-countdown' );

		wp_enqueue_script( 'wc-single-product' );
		wp_enqueue_script( 'wc-add-to-cart-variation' );
		wp_enqueue_script( WOLMART_WC_103_PREFIX . 'zoom' );
	}
}

if ( ! function_exists( 'wolmart_single_product_compare' ) ) {
	function wolmart_single_product_compare() {
		wolmart_product_compare( ' btn-product-icon' );
	}
}

if ( ! function_exists( 'wolmart_print_wishlist_button' ) ) {
	function wolmart_print_wishlist_button() {
		echo do_shortcode( '[yith_wcwl_add_to_wishlist container_classes="btn-product-icon"]' );
	}
}

if ( ! function_exists( 'wolmart_single_product_links_wrap_start' ) ) {
	function wolmart_single_product_links_wrap_start() {
		echo '<div class="product-links-wrapper">';
	}
}

if ( ! function_exists( 'wolmart_single_product_links_wrap_end' ) ) {
	function wolmart_single_product_links_wrap_end() {
		echo '</div>';
	}
}

/**
 * Wolmart Same Vendor Products in Single Product page.
 *
 * @since 1.4.0
 * @param $sp_type {String} Single Product Layout Type.
 */
if ( ! function_exists( 'wolmart_single_product_vendor_products' ) ) {
	function wolmart_single_product_vendor_products( $sp_type = '', $show_count = 8 ) {
		if ( wolmart_doing_quickview() || apply_filters( 'wolmart_is_single_product_widget', false ) ) {
			return;
		}

		global $product;
		$product_id    = $product->get_id();
		$vendor_id     = false;
		$store_url     = '#';
		$same_products = array();

		if ( class_exists( 'WCFM' ) && class_exists( 'WCFMmp' ) ) {
			$vendor_id = wcfm_get_vendor_id_by_post( $product_id );
			$store_url = wcfmmp_get_store_url( $vendor_id );

		} elseif ( class_exists( 'WeDevs_Dokan' ) ) {
			$vendor_id = get_post_field( 'post_author', $product_id );
			$store_url = dokan_get_store_url( $vendor_id );
			$user      = new WP_User( $vendor_id );
			$role      = reset( $user->roles );

		} elseif ( class_exists( 'WCV_Vendors' ) ) {
			$vendor_id = get_post_field( 'post_author', $product_id );

			if ( ! WCV_Vendors::is_vendor( $vendor_id ) ) {
				$store_url = get_permalink( wc_get_page_id( 'shop' ) );
			} else {
				$store_url = WCV_Vendors::get_vendor_shop_page( $vendor_id );
			}
		} elseif ( defined( 'MVX_PLUGIN_VERSION' ) ) {
			$vendor = get_mvx_product_vendors( $product_id );

			if ( $vendor ) {
				$vendor_id = $vendor->id;
				$store_url = $vendor->get_permalink();
			} else {
				$store_url = get_permalink( wc_get_page_id( 'shop' ) );
			}
		} elseif ( class_exists( 'WCMp' ) ) {
			$vendor    = get_wcmp_product_vendors( $product_id );
			$vendor_id = $vendor->id;

			if ( $vendor ) {
				$store_url = $vendor->get_permalink();
			} else {
				$store_url = get_permalink( wc_get_page_id( 'shop' ) );
			}
		} else {
			$store_url = get_permalink( wc_get_page_id( 'shop' ) );
		}

		$query = array(
			'post_type'           => 'product',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page '     => $show_count,
			'meta_key'            => 'total_sales',
			'orderby'             => 'meta_value_num',
			'author'              => $vendor_id,
			'post__not_in'        => array( $product_id ),
		);

		$list = new WP_Query( $query );

		if ( 0 == $list->found_posts ) {
			return;
		}

		?>
		<div class="wolmart-same-vendor-products-wrapper <?php echo ( $sp_type == 'gallery' ? 'gallery-type' : '' ); ?>">
			<h4 class="wolmart-same-vendor-products-title"><?php esc_html_e( 'More items from this seller', 'wolmart' ); ?><a href="<?php echo esc_url( $store_url ); ?>"><?php esc_html_e( 'View All', 'wolmart' ); ?></a></h4>
			<div class="wolmart-same-vendor-products">
				<?php
					$index = 0;
				while ( $list->have_posts() && $index < $show_count ) {
					global $post;
					$list->the_post();

					?>
						<figure class="product-media wolmart-vendor-product">
							<a href="<?php esc_url( the_permalink() ); ?>" aria-label="<?php esc_attr_e( 'Product Image', 'wolmart' ); ?>">
						<?php echo get_the_post_thumbnail( $post->ID, 'woocommerce_thumbnail' ); ?>
							</a>
						</figure>
						<?php
						++ $index;
				}
					wp_reset_postdata();
				?>
			</div>
		</div>
		<?php
	}
}

if ( ! function_exists( 'wolmart_single_product_wrap_special_before_end' ) ) {
	function wolmart_single_product_wrap_special_before_end() {
		if ( 'gallery' == wolmart_get_single_product_layout() && true == wolmart_get_option( 'same_vendor_products' ) ) {
			wolmart_single_product_vendor_products( 'gallery', 6 );
		}
	}
}

function wolmart_woocommerce_output_all_notices() {
	$single_product_template = defined( 'WOLMART_SINGLE_PRODUCT_BUILDER' ) ? Wolmart_Single_Product_Builder::get_instance()->get_template() : false;
	if ( is_numeric( $single_product_template ) && is_main_query() ) {
		$el_data = get_post_meta( $single_product_template, '_elementor_data', true );
		if ( empty( $el_data ) || ! defined( 'ELEMENTOR_VERSION' ) ) {
			if ( $el_data = get_post( $single_product_template ) ) {
				$el_data = $el_data->post_content;
			}
		}
		if ( false !== strpos( $el_data, 'wolmart_sproduct_notice' ) || false !== strpos( $el_data, '[wpb_wolmart_sp_notice' ) ) {
			remove_action( 'woocommerce_before_single_product', 'woocommerce_output_all_notices' );
			return;
		}
	}

	echo '<div class="woocommerce-notices-wrapper">';
	wc_print_notices();
	echo '</div>';
}
