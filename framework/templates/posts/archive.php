<?php
/**
 * Post Archive
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */
?>
<div class="post-archive">
	<?php

	if ( have_posts() ) {

		global $wolmart_layout, $wp_query;

		$posts_filter = $wolmart_layout['posts_filter'];
		$wrap_classes = array( 'posts' );
		$wrap_attrs   = '';

		if ( isset( $_REQUEST['post_type'] ) && in_array( $_REQUEST['post_type'], array( 'list', 'mask', 'simple' ) ) ) {
			$wolmart_layout['post_type'] = $_REQUEST['post_type'];
		} elseif ( wolmart_doing_ajax() && ! empty( $_REQUEST['only_posts'] ) ) {
			$wolmart_layout['post_type'] = '';
		}

		$post_type = $wolmart_layout['post_type'];

		// Category Filter
		if ( $posts_filter &&
			( empty( $wolmart_layout['left_sidebar'] ) || 'hide' == $wolmart_layout['left_sidebar'] ) &&
			( empty( $wolmart_layout['right_sidebar'] ) || 'hide' == $wolmart_layout['right_sidebar'] ) &&
			'list' != $post_type ) {
			wolmart_get_template_part( 'posts/elements/blog-filter' );
		}

		// Grid or Masonry

		if ( 'masonry' == $wolmart_layout['posts_layout'] ) {
			$wrap_classes[] = 'grid';
			$wrap_classes[] = 'masonry';
			$wrap_attrs     = " data-grid-options='" . json_encode( array( 'masonry' => array( 'horizontalOrder' => true ) ) ) . "'";
			wp_enqueue_script( 'isotope-pkgd' );
		}

		// List or Grid

		if ( 'list' == $post_type ) {
			$wrap_classes[] = 'list-type-posts';
			$wrap_classes[] = wolmart_get_col_class(
				array(
					'md'  => 1,
					'sm'  => 1,
					'min' => 1,
				)
			);
		} else {
			$cols_cnt        = wolmart_get_responsive_cols( array( 'lg' => intval( $wolmart_layout['posts_column'] ) ) );
			$cols_cnt['min'] = 1;
			$wrap_classes[]  = wolmart_get_col_class( $cols_cnt );
		}

		// Loadmore Button or Pagination
		if ( 1 < $wp_query->max_num_pages ) {
			if ( 'scroll' == $wolmart_layout['loadmore_type'] ) {
				$wrap_classes[] = 'load-scroll';
			}
			$wrap_attrs .= ' ' . wolmart_loadmore_attributes( '', array( 'blog' => true ), 'page', $wp_query->max_num_pages );
		}

		// Print Posts

		$wrap_classes = apply_filters( 'wolmart_post_loop_wrapper_classes', $wrap_classes );

		echo '<div class="' . esc_attr( implode( ' ', $wrap_classes ) ) . '"' . $wrap_attrs . ( $post_type ? ' data-post-type="' . $post_type . '"' : '' ) . '>';

		while ( have_posts() ) :
			the_post();
			wolmart_get_template_part( 'posts/post', null, array( 'single' => false ) );
		endwhile;

		echo '</div>';

		// Loadmore Button or Pagination
		if ( 1 < $wp_query->max_num_pages ) {
			wolmart_loadmore_html( $wp_query, $wolmart_layout['loadmore_type'], esc_html( 'Load More', 'wolmart' ) );
		}
	} else {
		?>
		<h2 class="entry-title"><?php esc_html_e( 'Nothing Found', 'wolmart' ); ?></h2>

		<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
			<p class="alert alert-light alert-info">
				<?php
				printf(
					// translators: %1$s represents open tag of admin url to create new, %2$s represents close tag.
					esc_html__( 'Ready to publish your first post? %1$sGet started here%2$s.', 'wolmart' ),
					sprintf( '<a href="%1$s" target="_blank">', esc_url( admin_url( 'post-new.php' ) ) ),
					'</a>'
				);
				?>
			</p>
		<?php elseif ( is_search() ) : ?>
			<p class="alert alert-light alert-info"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'wolmart' ); ?></p>
		<?php else : ?>
			<p class="alert alert-light alert-info"><?php esc_html_e( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'wolmart' ); ?></p>
		<?php endif; ?>

		<?php
	}
	?>
</div>
