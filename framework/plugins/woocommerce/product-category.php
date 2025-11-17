<?php
/**
 * Wolmart WooCommerce Product Category Functions
 *
 * Functions used to display product category.
 */

defined( 'ABSPATH' ) || die;

remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open' );
remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close' );

// Category Thumbnail
add_action( 'woocommerce_before_subcategory_title', 'wolmart_before_subcategory_thumbnail', 5 );
remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail' );
add_action( 'woocommerce_before_subcategory_title', 'wolmart_wc_subcategory_thumbnail' );
add_action( 'woocommerce_before_subcategory_title', 'wolmart_after_subcategory_thumbnail', 15 );
add_filter( 'subcategory_archive_thumbnail_size', 'wolmart_wc_category_thumbnail_size' );

// Category Content
remove_action( 'woocommerce_shop_loop_subcategory_title', 'woocommerce_template_loop_category_title' );
add_action( 'woocommerce_shop_loop_subcategory_title', 'wolmart_wc_template_loop_category_title' );
add_action( 'woocommerce_after_subcategory_title', 'wolmart_wc_after_subcategory_title' );

/**
 * Wolmart Category Thumbnail Functions
 */
if ( ! function_exists( 'wolmart_before_subcategory_thumbnail' ) ) {
	function wolmart_before_subcategory_thumbnail( $category ) {
		$category_type = wolmart_wc_get_loop_prop( 'category_type' );
		echo '<a href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '"' .
			( wolmart_wc_get_loop_prop( 'run_as_filter' ) ? ' data-cat="' . $category->term_id . '"' : '' ) . ' aria-label="' . esc_html__( 'Category Thumbnail', 'wolmart' ) . '">';
		if ( 'label' != $category_type && 'icon' != $category_type ) {
			echo '<figure>';
		}
	}
}

if ( ! function_exists( 'wolmart_wc_subcategory_thumbnail' ) ) {
	function wolmart_wc_subcategory_thumbnail( $category ) {
		$category_type = wolmart_wc_get_loop_prop( 'category_type' );

		if ( 'label' != $category_type ) {
			if ( wolmart_wc_get_loop_prop( 'show_icon', false ) ) {
				$icon_class = get_term_meta( $category->term_id, 'product_cat_icon', true );
				$icon_class = $icon_class ? $icon_class : 'w-icon-heart3';
				echo '<i class="' . $icon_class . '"></i>';
			} else {

				$html           = '';
				$thumbnail_size = apply_filters( 'subcategory_archive_thumbnail_size', 'woocommerce_thumbnail' );
				if ( isset( $GLOBALS['wolmart_current_cat_img_size'] ) ) {
					$thumbnail_size = $GLOBALS['wolmart_current_cat_img_size'];
					unset( $GLOBALS['wolmart_current_cat_img_size'] );
				}
				$thumbnail_id = get_term_meta( $category->term_id, 'thumbnail_id', true );
				$dimensions   = false;

				if ( ! in_array( str_replace( 'woocommerce_', '', $thumbnail_size ), array( 'shop_single', 'single', 'shop_catalog', 'thumbnail', 'shop_thumbnail', 'gallery_thumbnail' ) ) ) {
					if ( 'full' == $thumbnail_size ) {
						$dimensions = wp_get_attachment_metadata( $thumbnail_id );
					} else {
						$dimensions = image_get_intermediate_size( $thumbnail_id, array( $thumbnail_size ) );
					}
				}
				if ( ! $dimensions ) {
					$dimensions = wc_get_image_size( $thumbnail_size );
				}

				if ( $thumbnail_id ) {
					if ( isset( $dimensions['url'] ) && $dimensions['url'] ) {
						$image = $dimensions['url'];
					} else {
						$image = isset( wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size )[0] ) ? wp_get_attachment_image_src( $thumbnail_id, $thumbnail_size )[0] : '';
					}
					$image_srcset = wp_get_attachment_image_srcset( $thumbnail_id, $thumbnail_size );
					$image_meta   = wp_get_attachment_metadata( $thumbnail_id );
					$image_sizes  = wp_get_attachment_image_sizes( $thumbnail_id, $thumbnail_size, $image_meta );

					if ( 0 == $dimensions['height'] ) {
						$full_image_size = wp_get_attachment_image_src( $thumbnail_id, 'full' );
						if ( isset( $full_image_size[1] ) && $full_image_size[1] ) {
							$dimensions['height'] = intval( $dimensions['width'] / absint( $full_image_size[1] ) * absint( $full_image_size[2] ) );
						}
					}

					// If image's width is smaller than thumbnail size, use real image's size.
					if ( is_array( $dimensions ) && is_array( $image_meta ) && isset( $dimensions['width'] ) && isset( $image_meta['width'] ) && $dimensions['width'] > $image_meta['width'] ) {
						$dimensions['width']  = $image_meta['width'];
						$dimensions['height'] = $image_meta['height'];
					}
				} else {
					$image        = wc_placeholder_img_src();
					$image_srcset = false;
					$image_sizes  = false;
				}

				if ( $image ) {
					// Prevent esc_url from breaking spaces in urls for image embeds.
					// Ref: https://core.trac.wordpress.org/ticket/23605.
					$image = str_replace( ' ', '%20', $image );

					// Add responsive image markup if available.
					if ( $image_srcset && $image_sizes ) {
						$html = '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" srcset="' . esc_attr( $image_srcset ) . '" sizes="' . esc_attr( $image_sizes ) . '" />';
					} else {
						$html = '<img src="' . esc_url( $image ) . '" alt="' . esc_attr( $category->name ) . '" width="' . esc_attr( $dimensions['width'] ) . '" height="' . esc_attr( $dimensions['height'] ) . '" />';
					}
				}

				echo apply_filters( 'wolmart_wc_subcategory_thumbnail_html', $html );
			}
		} elseif ( wolmart_wc_get_loop_prop( 'show_icon', false ) ) {
			$icon_class = get_term_meta( $category->term_id, 'product_cat_icon', true );
			$icon_class = $icon_class ? $icon_class : 'w-icon-heart3';
			echo '<i class="' . $icon_class . '"></i>';
		}
	}
}

if ( ! function_exists( 'wolmart_after_subcategory_thumbnail' ) ) {
	function wolmart_after_subcategory_thumbnail( $category ) {
		$category_type  = wolmart_wc_get_loop_prop( 'category_type' );
		$content_origin = wolmart_wc_get_loop_prop( 'content_origin' );

		if ( 'label' != $category_type && 'icon' != $category_type ) {
			echo '</figure>';
		}

		if ( 'label' != $category_type ) {
			if ( 'group-2' == $category_type ) { // Group 2
				// Title
				echo '<h3 class="woocommerce-loop-category__title">';
				echo esc_html( $category->name );

				// Count
				if ( wolmart_wc_get_loop_prop( 'show_count', true ) ) {
					echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark>(' . esc_html( $category->count ) . ')</mark>', $category );
				}

				echo '</h3>';
			}
			echo '</a>';
			if ( $content_origin ) {
				echo '<div class="category-content ' . $content_origin . '">';
			} else {
				echo '<div class="category-content">';
			}
		}
	}
}

if ( ! function_exists( 'wolmart_wc_category_thumbnail_size' ) ) {
	function wolmart_wc_category_thumbnail_size( $size ) {
		$size = wolmart_wc_get_loop_prop( 'thumbnail_size', $size );
		if ( 'custom' == $size ) {
			return wolmart_wc_get_loop_prop( 'thumbnail_custom_size', 'woocommerce_thumbnail' );
		}
		return $size;
	}
}

/**
 * Wolmart Category Content Functions
 */
if ( ! function_exists( 'wolmart_wc_template_loop_category_title' ) ) {
	function wolmart_wc_template_loop_category_title( $category ) {

		$category_type = wolmart_wc_get_loop_prop( 'category_type' );

		// Title
		if ( 'group-2' !== $category_type ) {
			echo '<h3 class="woocommerce-loop-category__title">';

			if ( 'frame' !== $category_type && 'banner' !== $category_type && 'classic' !== $category_type && 'classic-2' !== $category_type && 'label' !== $category_type ) {
				echo '<a href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '"' .
					( wolmart_wc_get_loop_prop( 'run_as_filter' ) ? ' data-cat="' . $category->term_id . '"' : '' ) . '>';
				echo esc_html( $category->name );
				echo '</a>';
			} else {
				echo esc_html( $category->name );
			}

			// Count
			if ( 'frame' == $category_type && wolmart_wc_get_loop_prop( 'show_count', true ) ) {
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark>(' . esc_html( $category->count ) . ')</mark>', $category );
			}

			echo '</h3>';

			// Count
			if ( 'frame' !== $category_type && wolmart_wc_get_loop_prop( 'show_count', true ) ) {
				echo apply_filters( 'woocommerce_subcategory_count_html', '<mark>' . esc_html( $category->count ) . ' ' . esc_html__( 'Products', 'wolmart' ) . '</mark>', $category );
			}
		}
		// Link
		if ( wolmart_wc_get_loop_prop( 'show_link', true ) ) {
			$link_text  = wolmart_wc_get_loop_prop( 'link_text' );
			$link_class = 'btn btn-underline btn-link';
			echo '<a class="' . esc_html( $link_class ) . '"' .
				( wolmart_wc_get_loop_prop( 'run_as_filter' ) ? ' data-cat="' . $category->term_id . '"' : '' ) .
				' href="' . esc_url( get_term_link( $category, 'product_cat' ) ) . '">' .
				( $link_text ? esc_html( $link_text ) : esc_html__( 'Shop Now', 'wolmart' ) ) .
				'</a>';
		}
		if ( 'group' == $category_type || 'group-2' == $category_type ) {
				$terms = get_terms(
					'product_cat',
					array(
						'parent'     => $category->term_id, // $parent ),
						'hide_empty' => false,
						'number'     => wolmart_wc_get_loop_prop( 'subcat_cnt', 5 ),
					)
				);
			if ( is_array( $terms ) ) {
				echo '<ul class="category-list">';
				if ( ! count( $terms ) && function_exists( 'wolmart_is_elementor_preview' ) && wolmart_is_elementor_preview() ) {
					for ( $i = 1; $i <= 5; ++ $i ) {
						echo '<li><a href="#">';
						/* translators: %d represents a virtual number from 1 to 5. */
						printf( esc_html__( 'Subcategory %d', 'wolmart' ), $i );
						echo '</a></li>';
					}
				} else {
					foreach ( $terms as $term ) {
						echo '<li><a href="' . get_term_link( $term ) . '"' .
						( wolmart_wc_get_loop_prop( 'run_as_filter' ) ? ' data-cat="' . $term->term_id . '"' : '' ) . '>' . $term->name . '</a></li>';
					}
				}

				echo '</ul>';
			}
		}
	}
}

if ( ! function_exists( 'wolmart_wc_after_subcategory_title' ) ) {
	function wolmart_wc_after_subcategory_title() {
		$category_type = wolmart_wc_get_loop_prop( 'category_type' );
		if ( 'label' == $category_type ) {
			echo '</a>';
		} else {
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'wolmart_wc_category_show_info' ) ) {
	function wolmart_wc_category_show_info( $type = '' ) {
		$cat_options = array(
			''          => array(
				'link'  => '',
				'count' => '',
			),
			'frame'     => array(
				'link'  => 'yes',
				'count' => '',
			),
			'banner'    => array(
				'link'  => 'yes',
				'count' => 'yes',
			),
			'label'     => array(
				'link'  => '',
				'count' => '',
			),
			'icon'      => array(
				'link'  => '',
				'count' => '',
			),
			'classic'   => array(
				'link'  => '',
				'count' => 'yes',
			),
			'classic-2' => array(
				'link'  => '',
				'count' => '',
			),
			'ellipse'   => array(
				'link'  => '',
				'count' => 'yes',
			),
			'ellipse-2' => array(
				'link'  => '',
				'count' => '',
			),
			'group'     => array(
				'link'  => '',
				'count' => '',
			),
			'group-2'   => array(
				'link'  => '',
				'count' => '',
			),
			'simple'    => array(
				'link'  => '',
				'count' => 'yes',
			),
		);
		return $cat_options[ $type ];
	}
}

if ( ! function_exists( 'wolmart_get_category_classes' ) ) {
	function wolmart_get_category_classes() {

		$category_type = wolmart_wc_get_loop_prop( 'category_type' );

		if ( 'frame' == $category_type ) {
			return 'cat-type-frame cat-type-absolute';
		} elseif ( 'banner' == $category_type ) {
			return 'cat-type-banner cat-type-absolute';
		} elseif ( 'simple' === $category_type ) {
			return 'cat-type-simple';
		} elseif ( 'label' == $category_type ) {
			return 'cat-type-block';
		} elseif ( 'icon' == $category_type ) {
			return 'cat-type-icon';
		} elseif ( 'classic' == $category_type ) {
			return 'cat-type-classic cat-type-absolute';
		} elseif ( 'classic-2' == $category_type ) {
			return 'cat-type-classic cat-type-classic-2 cat-type-absolute';
		} elseif ( 'ellipse' == $category_type ) {
			return 'cat-type-ellipse';
		} elseif ( 'ellipse-2' === $category_type ) {
			return 'cat-type-ellipse2';
		} elseif ( 'group' == $category_type ) {
			return 'cat-type-group';
		} elseif ( 'group-2' == $category_type ) {
			return 'cat-type-group2';
		} else {
			return 'cat-type-default cat-type-absolute';
		}
	}
}
