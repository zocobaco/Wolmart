<?php
/**
 * Post Category
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */
if ( in_array( 'category', $show_info ) ) {
	$cats = get_the_category_list( ' , ' );
	if ( $cats ) {
		echo '<div class="post-cats">' . wolmart_strip_script_tags( $cats ) . '</div>';
	}
}
