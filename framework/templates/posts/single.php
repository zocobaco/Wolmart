<?php
/*
 * Single Post
 */
?>

<div class="<?php echo esc_attr( implode( ' ', apply_filters( 'wolmart_post_single_class', array( 'post-single' ) ) ) ); ?>">

<?php
$single_info_items = wolmart_get_option( 'post_show_info' );
if ( have_posts() ) {
	while ( have_posts() ) {
		the_post();
		wolmart_get_template_part( 'posts/post', null, array( 'single' => true ) );

		if ( in_array( 'navigation', $single_info_items ) ) {
			wolmart_get_template_part( 'posts/elements/post-navigation' );
		}

		if ( in_array( 'related', $single_info_items ) ) {
			wolmart_get_template_part( 'posts/elements/post-related' );
		}
	}
}

if ( in_array( 'comments_list', $single_info_items ) ) {
	comments_template();
}
?>

</div>
