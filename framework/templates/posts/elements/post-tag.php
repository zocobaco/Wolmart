<?php
/**
 * Post Tag
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */

$tags = get_the_tag_list();

if ( $tags ) :
	?>
	<div class="post-tags">
		<label><?php esc_html_e( 'Tags:', 'wolmart' ); ?></label>
		<?php echo wolmart_strip_script_tags( $tags ); ?>
	</div>
	<?php
endif;
