<?php
/**
 * Post Content
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */

$readmore_btn = '';
if ( in_array( 'readmore', $show_info ) && ! $related && ! $widget ) {
	$class        = $read_more_class ? esc_attr( $read_more_class ) : 'btn-link btn-primary btn-reveal-right';
	$readmore_btn = '<a href="' . esc_url( get_the_permalink() ) . '" class="btn-readmore ' . $class . '">(' . $read_more_label . ')</a>';
}

if ( $single || ( ! $related && in_array( 'content', $show_info ) ) ) : ?>
	<div class="post-content">
		<?php
		if ( $single ) {
			the_content();
			wolmart_get_page_links_html();
		} else {
			global  $post;

			if ( has_excerpt( $post ) ) {
				echo '<p>' . wp_strip_all_tags( get_the_excerpt( $post ), true ) . '</p>' . wolmart_escaped( $readmore_btn );
			} elseif ( strpos( $post->post_content, '<!--more-->' ) ) {
				echo apply_filters( 'the_content', get_the_content( '' ) ) . wolmart_escaped( $readmore_btn );
			} else {
				$content = wolmart_trim_description( get_the_content(), $excerpt_length, $excerpt_type );
				if ( $content ) {
					echo wolmart_escaped( $content . $readmore_btn );
				}
			}
		}
		?>
	</div>
	<?php
endif;
