<?php if ( get_the_author_meta( 'description' ) ) : ?>
<div class="post-author-detail">
	<figure class="author-avatar">
		<?php echo get_avatar( get_the_ID(), 50 ); ?>
	</figure>
	<div class="author-body">
		<div class="author-header">
			<?php
				$author_name = get_the_author_meta( 'display_name' );
				$author_link = get_author_posts_url( get_the_author_meta( 'ID' ) );
			?>
			<div class="author-meta">
				<h3 class="author-name"><?php echo esc_html( $author_name ); ?></h3>
				<span class="author-date"><?php echo apply_filters( 'wolmart_filter_author_date_pattern', esc_html( get_the_author_meta( 'user_registered' ) ) ); ?></span>
			</div>
			<a class="author-link" href="<?php echo esc_url( $author_link ); ?>"><?php printf( esc_html__( 'View all posts by %s', 'wolmart' ), esc_html( $author_name ) ); ?> <i class="w-icon-long-arrow-right"></i></a>
		</div>
		<div class="author-content"><?php echo wolmart_strip_script_tags( get_the_author_meta( 'description' ) ); ?></div>
	</div>
</div>
	<?php
endif;
