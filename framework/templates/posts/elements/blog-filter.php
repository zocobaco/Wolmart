<?php
/**
 * Blog Filter
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */
$terms   = get_terms(
	'category',
	array(
		'hide_empty' => true,
		'count'      => true,
		'pad_counts' => true,
	)
);
$cur_cat = get_query_var( 'category_name' );

$count_posts = ( wp_count_posts() );
if ( isset( $count_posts->publish ) ) {
	$count_posts = (int) $count_posts->publish;
}

// get blog url for all nav filter
$blog_url = 'page' == get_option( 'show_on_front' ) ? get_permalink( get_option( 'page_for_posts' ) ) : get_home_url();

echo '<ul class="nav-filters filter-underline blog-filters mb-4" data-target=".posts">
    <li><a href="' . esc_url( $blog_url ) . '" class="nav-filter blog-filter' . ( $cur_cat ? '' : ' active' ) . '" data-filter="*">' . esc_html__( 'All Blog Posts', 'wolmart' ) . '<span>' . (int) $count_posts . '</span></a></li>';

foreach ( $terms as $term ) {
	echo '<li>';
	echo '<a href="' . esc_url( get_term_link( $term ) ) . '" class="nav-filter blog-filter' . ( $cur_cat == $term->slug ? ' active' : '' ) . '" data-cat="' . (int) $term->term_id . '" data-filter=".' . esc_attr( $term->slug ) . '">';
	echo esc_html( $term->name ) . '<span>' . intval( $term->count ) . '</span></a>';
	echo '</li>';
}

echo '</ul>';
