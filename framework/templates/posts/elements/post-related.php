<?php
/**
 * Post Related
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */

global $wolmart_layout;

if ( ! isset( $args ) ) {
	$args = array();
}

if ( 'attachment' == get_post_type() ) {
	$related_posts = '';
} else {
	$args = wp_parse_args(
		$args,
		array(
			'post__not_in'        => array( get_the_ID() ),
			'ignore_sticky_posts' => 0,
			'category__in'        => wp_get_post_categories( get_the_ID() ),
			'posts_per_page'      => empty( $wolmart_layout['related_count'] ) ? 4 : $wolmart_layout['related_count'],
			'orderby'             => empty( $wolmart_layout['related_order'] ) ? '' : $wolmart_layout['related_order'],
			'orderway'            => empty( $wolmart_layout['related_orderway'] ) ? '' : $wolmart_layout['related_orderway'],
		)
	);

	$related_posts = new WP_Query( apply_filters( 'wolmart_filter_related_posts_args', $args ) );
}

if ( $related_posts && $related_posts->have_posts() ) :

	?>
	<section class="related-posts">
		<h3 class="title title-simple"><?php esc_html_e( 'Related Posts', 'wolmart' ); ?></h3>
		<?php
		$col_cnt = wolmart_get_responsive_cols( array( 'lg' => empty( $wolmart_layout['related_column'] ) ? 4 : $wolmart_layout['related_column'] ), 'post' );
		?>
		<div class="<?php echo wolmart_get_col_class( $col_cnt ) . ' ' . wolmart_get_slider_class(); ?>" data-slider-options="
		<?php
		echo esc_attr(
			json_encode(
				wolmart_get_slider_attrs(
					array(
						'autoheight' => 'yes',
						'show_nav'   => true,
						'show_dots'  => false,
						'nav_pos'    => 'top',
					),
					$col_cnt
				)
			)
		);
		?>
		">
		<?php

		while ( $related_posts->have_posts() ) :
			$related_posts->the_post();
			wolmart_get_template_part( 'posts/post', null, array( 'related' => true ) );
			endwhile;

		wp_reset_postdata();
		?>
		</div>
	</section>
	<?php

endif;
