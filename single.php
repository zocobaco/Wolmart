<?php
/**
 * Single post and other post-types template
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

defined( 'ABSPATH' ) || die;

get_header();
do_action( 'wolmart_before_content' );
?>

<div class="page-content">
	<?php wolmart_get_template_part( 'posts/layout' ); ?>
</div>

<?php
do_action( 'wolmart_after_content' );
get_footer();
