<?php
/**
 * Post Media
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */
global $post, $wolmart_layout;

if ( ! $single && ! $related && ! $widget && 'creative' == $wolmart_layout['posts_layout'] ) {
	$image_size = 'large';
}

if ( 'video' == get_post_format() && get_post_meta( $post->ID, 'featured_video' ) && ! in_array( $type, array( 'widget', 'mask' ) ) ) :

	wp_enqueue_script( 'jquery-fitvids' );

	$video_code = get_post_meta( $post->ID, 'featured_video', true );
	if ( false !== strpos( $video_code, '[video src="' ) && has_post_thumbnail() ) {
		$video_code = str_replace( '[video src="', '[video poster="' . esc_url( get_the_post_thumbnail_url( null, 'full' ) ) . '" src="', $video_code );
	}
	?>
	<figure class="post-media fit-video">
		<?php echo do_shortcode( $video_code ); ?>
	</figure>
	<?php

else :

	$featured_id = get_post_thumbnail_id();
	// get supported images of the post
	$image_ids = get_post_meta( $post->ID, 'supported_images' );
	if ( $featured_id ) {
		$image_ids = array_merge( array( $featured_id ), $image_ids );
	}

	if ( count( $image_ids ) ) :
		if ( count( $image_ids ) > 1 && 'large' == $image_size ) :
			$col_cnt = wolmart_get_responsive_cols( array( 'lg' => 1 ) );

			$attrs = array( 'col_sp' => 'no' );

			if ( in_array( $type, array( 'mask' ) ) ) {
				$attrs['show_dots'] = '';
			}
			?>
			<div class="post-media-carousel slider-dots-white 
				<?php
				echo wolmart_get_col_class( $col_cnt ) . ' ' . wolmart_get_slider_class(
					array(
						'dots_pos' => 'inner',
					)
				);
				?>
				" data-slider-options="
				<?php
				echo esc_attr(
					json_encode(
						wolmart_get_slider_attrs(
							$attrs,
							$col_cnt
						)
					)
				);
				?>
			">
			<?php
		else :
			$image_ids = array( $image_ids[0] );
		endif;

		foreach ( $image_ids as $thumbnail_id ) :
			?>
			<figure class="post-media">
			<?php if ( ! $single || $related ) : ?>
					<a href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( 'Post Media', 'wolmart' ); ?>">
				<?php endif; ?>
				<?php
				$size = apply_filters( 'post_thumbnail_size', 'custom' == $image_size && isset( $image_custom_size ) ? $image_custom_size : $image_size, $post->ID );

				if ( $thumbnail_id ) {

					do_action( 'begin_fetch_post_thumbnail_html', $post->ID, $thumbnail_id, $size );

					if ( in_the_loop() ) {
						update_post_thumbnail_cache();
					}

					$html = wp_get_attachment_image( $thumbnail_id, $size, false );

					do_action( 'end_fetch_post_thumbnail_html', $post->ID, $thumbnail_id, $size );

				} else {
					$html = '';
				}

				echo apply_filters( 'post_thumbnail_html', $html, $post->ID, $thumbnail_id, $size, '' );

				?>
				<?php if ( ! $single || $related ) : ?>
					</a>
				<?php endif; ?>
				<?php
				if ( isset( $show_datebox ) && $show_datebox ) {
					wolmart_get_template_part( 'posts/elements/post-date-in-media' );
				}

				// Caption
				$caption = get_the_post_thumbnail_caption();
				if ( $caption ) {
					?>
					<div class="thumbnail-caption">
						<?php echo wolmart_strip_script_tags( $caption ); ?>
					</div>
					<?php
				}
				?>
			</figure>
			<?php
		endforeach;

		if ( count( $image_ids ) > 1 ) :
			?>
			</div>
			<?php
		endif;
	endif;
endif;
