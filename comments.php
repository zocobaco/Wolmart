<?php
/**
 * Comments template
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

do_action( 'wolmart_before_comments' );

if ( post_password_required() ) {
	return;
}

if ( ! comments_open() && get_comments_number() < 1 ) {
	return;
}

?>

<div id="comments" class="comments">

	<h3 class="title title-simple"><?php comments_number( esc_html__( 'No Comments', 'wolmart' ), esc_html__( 'One Comment', 'wolmart' ), esc_html( _n( '% Comment', '% Comments', get_comments_number(), 'wolmart' ) ) ); ?></h3>

	<?php if ( have_comments() ) : ?>

		<ol class="commentlist">
			<?php
			// List comments
			wp_list_comments(
				apply_filters(
					'wolmart_filter_comment_args',
					array(
						'callback'          => 'wolmart_post_comment',
						'style'             => 'ol',
						'format'            => 'html5',
						'short_ping'        => true,
						'reverse_top_level' => true,
					)
				)
			);
			?>
		</ol>

		<?php
		// Are there comments to navigate through?
		if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
			echo '<div class="pagination" data-post-id="' . get_the_ID() . '">';

			echo apply_filters( 'wolmart_get_comments_pagination_html', '' );

			echo '</div>';
		endif;

		if ( ! comments_open() && get_comments_number() ) :
			?>
			<p class="no-comments"><?php esc_html_e( 'Comments are closed.', 'wolmart' ); ?></p>
			<?php
		endif;
	endif;
	?>
</div>

<?php
if ( comments_open() ) {

	$submit_label  = wolmart_is_product() ? esc_html__( 'Submit', 'wolmart' ) : esc_html__( 'Post Comment', 'wolmart' );
	$submit_label .= '<i class="w-icon-long-arrow-' . ( is_rtl() ? 'left' : 'right' ) . '"></i>';

	comment_form(
		apply_filters(
			'wolmart_filter_comment_form_args',
			array(
				'title_reply_before' => '<h3 id="reply-title" class="comment-reply-title">',
				'title_reply_after'  => '</h3>',
				'fields'             => array(
					'author' => '<div class="col-md-6"><input name="author" type="text" class="form-control" value="" placeholder="' . esc_attr__( 'Name', 'wolmart' ) . '*"></div>',
					'email'  => '<div class="col-md-6"><input name="email" type="text" class="form-control" value="" placeholder="' . esc_attr__( 'Email', 'wolmart' ) . '*"> </div>',
				),
				'comment_field'      => '<textarea name="comment" id="comment" class="form-control" rows="4" maxlength="65525" required="required" placeholder="' . esc_attr__( 'Comment', 'wolmart' ) . '*"></textarea>',
				'submit_button'      => '<button type="submit" class="btn btn-dark btn-submit">' . $submit_label . '</button>',
			)
		)
	);
}
