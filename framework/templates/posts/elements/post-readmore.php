<?php
/**
 * Post Readmore
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */

if ( in_array( 'readmore', $show_info ) ) {
	printf(
		'<a href="%s" class="btn %s" title="%s" aria-label="' . esc_html__( 'Read More', 'wolmart' ) . '">%s</a>',
		esc_url( get_the_permalink() ),
		$read_more_class ? esc_attr( $read_more_class ) : 'btn-link btn-underline',
		esc_attr( 'Read More', 'wolmart' ),
		$read_more_class ? $read_more_label : $read_more_label . '<i class="w-icon-long-arrow-' . ( is_rtl() ? 'left' : 'right' ) . '"></i>'
	);
}
