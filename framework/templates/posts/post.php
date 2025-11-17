<?php
/**
 * post.php
 */

defined( 'ABSPATH' ) || die;

$widget     = isset( $widget );
$single     = isset( $single ) ? $single : false;
$related    = isset( $related ) ? $related : false;
$wrap_class = array();
$wrap_attrs = '';
$image_size = 'large';
$classes    = get_post_class();

if ( ! in_array( 'post', $classes ) ) {
	$classes[] = 'post';
}

if ( $related || $widget ) {
	$layout = Wolmart_Layout_Builder::get_instance()->get_layout( 'archive_post' );
} else {
	global $wolmart_layout;
	$layout = $wolmart_layout;
}

if ( ! isset( $posts_layout ) ) {
	if ( isset( $layout['posts_layout'] ) ) {
		$posts_layout = $layout['posts_layout'];
	} else {
		$posts_layout = '';
	}
}


// For Widget
if ( $widget ) {
	if ( 'creative' == $posts_layout ) {
		$wrap_class[] = 'grid-item';

		if ( isset( $post_idx ) && isset( $repeaters['ids'][ $post_idx + 1 ] ) ) {
			$wrap_class[] = $repeaters['ids'][ $post_idx + 1 ];
		}

		if ( isset( $repeaters['ids'][0] ) ) {
			$wrap_class[] = $repeaters['ids'][0];
		}

		$wrap_attrs = ' data-grid-idx="' . ( $post_idx + 1 ) . '"';
	} elseif ( 'slider' == $posts_layout && isset( $post_idx ) ) {
		if ( 1 == ( $post_idx + 1 ) % $row_cnt ) {
			echo '<div class="post-col">';
		}
	}
}

if ( $widget && ! isset( $follow_theme_option ) ) { // For "custom options" Widget
	if ( ! $excerpt_type ) {
		$excerpt_type = $layout['excerpt_type'];
	}
	if ( ! $excerpt_length ) {
		$excerpt_length = (int) $layout['excerpt_length'];
	}
	if ( isset( $overlay ) && $overlay ) {
		$classes[] = wolmart_get_overlay_class( $overlay );
	}
} else { // Archive, Single Or "follow theme option" Widget
	$show_info = array( 'image', 'author', 'date', 'category', 'comment', 'content' );

	if ( ! $single || $related || isset( $follow_theme_option ) ) { // Archive page or Related posts

		$type    = $layout['post_type'];                                                  // Type
		$cnt_row = $layout['posts_column'];                                               // Column
		// $show_info  = array( 'image', 'category', 'author', 'date', 'content', 'readmore' ); // Show Info
		$show_info  = array( 'image', 'author', 'date', 'content', 'readmore' ); // Show Info
		$image_size = 'wolmart-post-small';

		if ( $related ) {
			$type = '';
			// $show_info = array( 'image', 'category', 'author', 'date', 'readmore' ); // issue:remove category for theme check
			$show_info = array( 'image', 'author', 'date', 'readmore' );
		} elseif ( (int) $cnt_row > 1 ) {
			if ( 2 == $cnt_row ) {
				$image_size = 'wolmart-post-medium';
			}
			$show_info[] = 'comment';
		} else {
			$classes[]  = 'post-lg';
			$image_size = 'full';
			// $show_info[] = 'comment';
		}

		$image_size = apply_filters( 'wolmart_post_image_size', $image_size, $type, $cnt_row );

		// Excerpt
		$excerpt_length = (int) $layout['excerpt_length'];
		$excerpt_type   = $layout['excerpt_type'];

		// Overlay
		$classes[] = wolmart_get_overlay_class( $layout['post_overlay'] );

		// Show Date Box
		$show_datebox = $layout['show_datebox'];

		// Read More
		$read_more_label = '';

		// Except Widget
		if ( ! isset( $follow_theme_option ) ) {
			// Show Filter
			$posts_layout = $layout['posts_layout'];
			$posts_filter = $layout['posts_filter'];
			if ( $posts_filter || 'creative' == $posts_layout ) {
				$wrap_class[] = 'grid-item';
			}
			if ( $posts_filter ) {
				$cs = get_the_category( get_the_ID() ); // categories of each post.

				foreach ( $cs as $cat ) {
					$wrap_class[] = $cat->slug;
				}
			}
		}
	} else { // Single Post Page
		$show_info = wolmart_get_option( 'post_show_info' );
		if ( in_array( 'meta', $show_info ) ) {
			$show_info[] = 'author';
			$show_info[] = 'date';
			$show_info[] = 'comment_count';
		}

		$type            = '';
		$cnt_row         = '';
		$excerpt_length  = '';
		$excerpt_type    = '';
		$read_more_label = '';
		$show_datebox    = '';
	}
}

if ( $type ) { // List or Mask
	if ( 'list' != $type || 'creative' != $posts_layout ) {
		$classes[] = 'post-' . $type;
	}

	if ( 'mask' == $type ) { // Mask Type
		$show_info = array( 'image', 'category', 'date', 'author', 'comment' );
	}
}

if ( wolmart_doing_ajax() && ! empty( $_REQUEST['post_image'] ) ) {
	$image_size = wp_unslash( $_REQUEST['post_image'] );
}

if ( empty( $read_more_label ) ) {
	$read_more_label = esc_html__( 'read more', 'wolmart' );
}
if ( empty( $read_more_class ) ) {
	$read_more_class = '';
}

$classes = apply_filters( 'wolmart_post_loop_classes', $classes );

// Render
?>
<div class="post-wrap <?php echo esc_attr( implode( ' ', $wrap_class ) ); ?>"<?php echo esc_attr( $wrap_attrs ); ?> data-post-image="<?php echo esc_attr( $image_size ); ?>">
	<?php do_action( 'wolmart_post_loop_before_item', $type ); ?>
	<article class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php
		if ( 'list-xs' !== $type ) {
			if ( in_array( 'image', $show_info ) ) {

				$props = array(
					'type'         => $type,
					'image_size'   => $image_size,
					'related'      => $related,
					'single'       => $single,
					'type'         => $type,
					'show_datebox' => $show_datebox,
					'widget'       => $widget,
				);

				if ( isset( $thumbnail_size ) ) {
					$props['image_size'] = $thumbnail_size;
					if ( 'custom' == $thumbnail_size && isset( $thumbnail_custom_dimension ) ) {
						$props['image_custom_size'] = $thumbnail_custom_dimension;
					}
				}

				if ( isset( $post_idx ) && isset( $repeaters['images'][ $post_idx + 1 ] ) ) {
					$props['image_size'] = $repeaters['images'][ $post_idx + 1 ];
				}

				wolmart_get_template_part(
					'posts/elements/post',
					'media',
					$props
				);
			}
		} else {
			wolmart_get_template_part(
				'posts/elements/post',
				'date-in-media'
			);
		}
		?>
		<div class="post-details">
			<?php
			if ( 'mask' == $type ) {
				echo '<div class="post-meta-visible">';
			}
			if ( ! $related ) {
				wolmart_get_template_part(
					'posts/elements/post',
					'category',
					array(
						'related'   => $related,
						'show_info' => $show_info,
					)
				);
			}

			if ( ( $related || $widget || $single ) && 'mask' != $type ) {
				wolmart_get_template_part(
					'posts/elements/post',
					'meta',
					array(
						'single'    => $single,
						'related'   => $related,
						'show_info' => $show_info,
						'widget'    => $widget,
					)
				);
			}
			wolmart_get_template_part(
				'posts/elements/post',
				'title',
				array(
					'single'  => $single,
					'related' => $related,
				)
			);
			if ( 'mask' != $type ) {
				wolmart_get_template_part(
					'posts/elements/post',
					'content',
					array(
						'single'          => $single,
						'related'         => $related,
						'show_info'       => $show_info,
						'excerpt_length'  => $excerpt_length,
						'excerpt_type'    => $excerpt_type,
						'read_more_class' => $read_more_class,
						'read_more_label' => $read_more_label,
						'widget'          => $widget,
					)
				);
			}
			if ( 'mask' == $type ) {
				echo '</div>';
			}
			if ( ( ( ! $related && ! $widget && ! $single ) || ( 'mask' == $type ) ) && 'product' != get_post_type() ) {
				wolmart_get_template_part(
					'posts/elements/post',
					'meta',
					array(
						'single'    => $single,
						'related'   => $related,
						'show_info' => $show_info,
						'widget'    => $widget,
					)
				);
			}
			if ( $related || $widget ) {
				wolmart_get_template_part(
					'posts/elements/post',
					'readmore',
					array(
						'show_info'       => $show_info,
						'read_more_class' => $read_more_class,
						'read_more_label' => $read_more_label,
					)
				);
			}
			if ( $single && ! $related ) {
				if ( ( in_array( 'tag', $show_info ) && get_the_tag_list() ) || ( in_array( 'share', $show_info ) && function_exists( 'wolmart_print_share' ) ) ) {
					if ( in_array( 'tag', $show_info ) && get_the_tag_list() ) {
						wolmart_get_template_part( 'posts/elements/post-tag' );
					}
					if ( in_array( 'share', $show_info ) && function_exists( 'wolmart_print_share' ) ) {
						wolmart_print_share();
					}

					if ( in_array( 'author_info', $show_info ) ) {
						wolmart_get_template_part( 'posts/elements/post-author' );
					}
				}
			}
			?>
		</div>
	</article>
	<?php do_action( 'wolmart_post_loop_after_item', $type ); ?>
</div>

<?php
if ( 'slider' == $posts_layout && isset( $post_idx ) ) {
	if ( 0 == ( $post_idx + 1 ) % $row_cnt ) {
		echo '</div>';
	}
}
