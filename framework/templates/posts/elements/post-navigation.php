<?php
/**
 * Post Navigation
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */

if ( 'attachment' != get_post_type() ) {
	the_post_navigation(
		apply_filters(
			'wolmart_post_navigation_args',
			array(
				'prev_text' => '<span class="label">' . esc_html__( 'Previous Post', 'wolmart' ) . '</span>' . '<span class="pager-link-title">%title</span>',
				'next_text' => '<span class="label">' . esc_html__( 'Next Post', 'wolmart' ) . '</span>' . '<span class="pager-link-title">%title</span>',
			)
		)
	);
}
