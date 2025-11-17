<?php
/**
 * Display single product reviews (comments)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product-reviews.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.7.0
 */

defined( 'ABSPATH' ) || die;

global $product;

$average_rate = number_format( $product->get_average_rating(), 1 );
$display_rate = $average_rate * 20;
$count        = $product->get_review_count();

if ( ! comments_open() ) {
	return;
}

?>
<div id="reviews" class="woocommerce-Reviews">

	<?php if ( 'section' == apply_filters( 'wolmart_single_product_data_tab_type', 'tab' ) ) : ?>
		<h2 class="title-wrapper title-underline woocommerce-Reviews-title">
			<span class="title"><?php echo esc_html( wolmart_get_option( 'product_reviews_title' ) ); ?></span>
		</h2>
	<?php endif; ?>

	<div id="comments">
		<div class="row">
			<div class="col-md-4 mb-4">
				<h4 class="avg-rating-container">
					<mark><?php echo '' . $average_rate; ?></mark>
					<span class="avg-rating">
						<span class="avg-rating-title"><?php esc_html_e( 'Average Rating', 'wolmart' ); ?></span>
						<span class="star-rating">
							<span style="width: <?php echo wolmart_escaped( $display_rate ) . '%'; ?>;">
								<?php echo esc_html__( 'Rated', 'wolmart' ); ?>
							</span>
						</span>
						<span class="ratings-review">
							<?php echo sprintf( esc_html__( '(%s Reviews)', 'wolmart' ), $count ); ?>
						</span>
					</span>
				</h4>
				<?php do_action( 'wolmart_helpful_recommended', $product ); ?>
				<div class="ratings-list">
					<?php
					$ratings_count      = $product->get_rating_counts();
					$total_rating_value = 0;

					foreach ( $ratings_count as $key => $value ) {
						$total_rating_value += intval( $key ) * intval( $value );
					}

					for ( $i = 5; $i > 0; $i-- ) {
						$rating_value = isset( $ratings_count[ $i ] ) ? $ratings_count[ $i ] : 0;
						?>
						<div class="ratings-item">
							<div class="star-rating">
								<span style="width: <?php echo absint( $i ) * 20 . '%'; ?>">Rated</span>
							</div>
							<div class="rating-percent">
								<span style="width: 
								<?php
								if ( ! intval( $rating_value ) == 0 ) {
									echo round( floatval( number_format( ( $rating_value * $i ) / $total_rating_value, 3 ) * 100 ), 1 ) . '%';
								} else {
									echo '0%';
								}
								?>
								;"></span>
							</div>
							<div class="progress-value">
								<?php
								if ( ! intval( $rating_value ) == 0 ) {
									echo round( floatval( number_format( ( $rating_value * $i ) / $total_rating_value, 3 ) * 100 ), 1 ) . '%';
								} else {
									echo '0%';
								}
								?>
							</div>
						</div>
						<?php
					}
					?>
				</div>
			</div>
			<div class="col-md-8 mb-4">
			<?php if ( get_option( 'woocommerce_review_rating_verification_required' ) === 'no' || wc_customer_bought_product( '', get_current_user_id(), $product->get_id() ) ) : ?>
				<div id="review_form_wrapper">
					<div id="review_form">
						<?php
						$commenter    = wp_get_current_commenter();
						$comment_form = array(
							/* translators: %s is product title */
							'title_reply'         => have_comments() ? esc_html__( 'Submit Your Review', 'wolmart' ) : sprintf( esc_html__( 'Be the first to review &ldquo;%s&rdquo;', 'woocommerce' ), get_the_title() ),
							/* translators: %s is product title */
							'title_reply_to'      => esc_html__( 'Leave a Reply to %s', 'wolmart' ),
							'title_reply_before'  => '<span id="reply-title" class="comment-reply-title" role="heading" aria-level="3">',
							'title_reply_after'   => '</span>',
							'comment_notes_after' => '',
							'label_submit'        => esc_html__( 'Submit', 'woocommerce' ),
							'logged_in_as'        => '',
							'comment_field'       => '',
						);

						$name_email_required = (bool) get_option( 'require_name_email', 1 );
						$fields              = array(
							'author' => array(
								'label'    => __( 'Name', 'woocommerce' ),
								'type'     => 'text',
								'value'    => $commenter['comment_author'],
								'required' => $name_email_required,
								'autocomplete' => 'name',
							),
							'email'  => array(
								'label'    => __( 'Email', 'woocommerce' ),
								'type'     => 'email',
								'value'    => $commenter['comment_author_email'],
								'required' => $name_email_required,
								'autocomplete' => 'name',
							),
						);

						$comment_form['fields'] = array();

						foreach ( $fields as $key => $field ) {
							$field_html  = '<p class="comment-form-' . esc_attr( $key ) . '">';
							$field_html .= '<label for="' . esc_attr( $key ) . '">' . esc_html( $field['label'] );

							if ( $field['required'] ) {
								$field_html .= '&nbsp;<span class="required">*</span>';
							}

							$field_html .= '</label><input id="' . esc_attr( $key ) . '" name="' . esc_attr( $key ) . '" type="' . esc_attr( $field['type'] ) . '" autocomplete="' . esc_attr( $field['autocomplete'] ) . '" value="' . esc_attr( $field['value'] ) . '" size="30" ' . ( $field['required'] ? 'required' : '' ) . ' /></p>';

							$comment_form['fields'][ $key ] = $field_html;
						}

						$account_page_url = wc_get_page_permalink( 'myaccount' );
						if ( $account_page_url ) {
							/* translators: %s opening and closing link tags respectively */
							$comment_form['must_log_in'] = '<p class="must-log-in">' . sprintf( esc_html__( 'You must be %1$slogged in%2$s to post a review.', 'woocommerce' ), '<a href="' . esc_url( $account_page_url ) . '">', '</a>' ) . '</p>';
						}

						if ( wc_review_ratings_enabled() ) {
							$comment_form['comment_field'] = '<div class="comment-form-rating"><label for="rating" id="comment-form-rating-label">' . esc_html__( 'Your Rating Of This Product', 'wolmart' ) . ( wc_review_ratings_required() ? '&nbsp;<span class="required">:</span>' : '' ) . '</label><select name="rating" id="rating" required>
								<option value="">' . esc_html__( 'Rate&hellip;', 'woocommerce' ) . '</option>
								<option value="5">' . esc_html__( 'Perfect', 'woocommerce' ) . '</option>
								<option value="4">' . esc_html__( 'Good', 'woocommerce' ) . '</option>
								<option value="3">' . esc_html__( 'Average', 'woocommerce' ) . '</option>
								<option value="2">' . esc_html__( 'Not that bad', 'woocommerce' ) . '</option>
								<option value="1">' . esc_html__( 'Very poor', 'woocommerce' ) . '</option>
							</select></div>';
						}

						$comment_form['comment_field'] .= '<p class="comment-form-comment"><label for="comment">' . esc_html__( 'Your review', 'woocommerce' ) . '&nbsp;<span class="required"></span></label><textarea id="comment" name="comment" cols="45" rows="8" required></textarea></p>';

						comment_form( apply_filters( 'woocommerce_product_review_comment_form_args', $comment_form ) );
						?>
					</div>
				</div>
			<?php else : ?>
				<p class="woocommerce-verification-required"><?php esc_html_e( 'Only logged in customers who have purchased this product may leave a review.', 'woocommerce' ); ?></p>
			<?php endif; ?>

			</div>
		</div>

		<?php if ( have_comments() ) : ?>
			<ul id="wolmart_comment_tabs" class="wolmart-comment-tabs nav nav-tabs tab-nav-solid" role="tablist" data-post_id = "<?php the_ID(); ?>">
				<li class="nav-item" role="tab" aria-controls="commentlist">
					<span data-href="#commentlist" class="nav-link active" data-mode="all"><?php esc_html_e( 'Show all', 'wolmart' ); ?></span>
				</li>
				<li class="nav-item" role="tab" aria-controls="commentlist-helpful-positive">
					<span data-href="#commentlist-helpful-positive" data-mode="helpful-positive" class="nav-link">
					<?php esc_html_e( 'Most Helpful Positive', 'wolmart' ); ?>
					</span>
				</li>
				<li class="nav-item" role="tab" aria-controls="commentlist-helpful-negative">
					<span data-href="#commentlist-helpful-negative" data-mode="helpful-negative" class="nav-link">
					<?php esc_html_e( 'Most Helpful Negative', 'wolmart' ); ?>
					</span>
				</li>
				<li class="nav-item" role="tab" aria-controls="commentlist-highrated">
					<span data-href="#commentlist-highrated" data-mode="high-rate" class="nav-link">
					<?php esc_html_e( 'Highest Rating', 'wolmart' ); ?>
					</span>
				</li>
				<li class="nav-item" role="tab" aria-controls="commentlist-lowrated">
					<span data-href="#commentlist-lowrated" data-mode="low-rate" class="nav-link">
					<?php esc_html_e( 'Lowest Rating', 'wolmart' ); ?>
					</span>
				</li>
			</ul>
			<div class="tab-content tab-templates">
				<ol id="commentlist" class="commentlist tab-pane active">
					<?php wp_list_comments( apply_filters( 'woocommerce_product_review_list_args', array( 'callback' => 'woocommerce_comments' ) ) ); ?>
				</ol>
				<ol id="commentlist-helpful-positive" class="commentlist tab-pane" data-empty="<li class='review review-empty'><?php esc_html_e( 'No positive review exists.', 'wolmart' ); ?></li>"></ol>
				<ol id="commentlist-helpful-negative" class="commentlist tab-pane" data-empty="<li class='review review-empty'><?php esc_html_e( 'No negative review exists.', 'wolmart' ); ?></li>"></ol>
				<ol id="commentlist-highrated" class="commentlist tab-pane"></ol>
				<ol id="commentlist-lowrated" class="commentlist tab-pane"></ol>
			</div>

			<?php
			if ( get_comment_pages_count() > 1 && get_option( 'page_comments' ) ) :
				echo '<nav class="woocommerce-pagination pagination">';

				$args  = apply_filters(
					'woocommerce_comment_pagination_args',
					array(
						'echo'      => false,
						'prev_text' => '<i class="w-icon-long-arrow-left"></i> ' . esc_html__( 'Prev', 'wolmart' ),
						'next_text' => esc_html__( 'Next', 'wolmart' ) . ' <i class="w-icon-long-arrow-right"></i>',
					)
				);
				$links = paginate_comments_links( $args );
				if ( $links ) {

					if ( 1 === $page ) {
						$links = sprintf(
							'<span class="prev page-numbers disabled">%s</span>',
							$args['prev_text']
						) . $links;
					} elseif ( get_comment_pages_count() == $page ) {
						$links .= sprintf(
							'<span class="next page-numbers disabled">%s</span>',
							$args['next_text']
						);
					}
				}

				echo wolmart_escaped( $links );

				echo '</nav>';
			endif;
			?>
		<?php endif; ?>
	</div>

	<div class="clear"></div>
</div>
