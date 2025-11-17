<?php
/**
 * The page template
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

defined( 'ABSPATH' ) || die;

get_header();

do_action( 'wolmart_before_content' );
?>

<div class="page-content">

	<?php
	do_action( 'wolmart_print_before_page_layout' );

	if ( have_posts() ) {
		while ( have_posts() ) {
			the_post();
			the_content();

			wolmart_get_page_links_html();
		}
	} else {
		echo '<h2 class="entry-title">' . esc_html__( 'Nothing Found', 'wolmart' ) . '</h2>';
	}

	do_action( 'wolmart_print_after_page_layout' );
	?>

</div>

<?php
do_action( 'wolmart_after_content' );

get_footer();
