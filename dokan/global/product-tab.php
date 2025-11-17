<?php
/**
 * Dokan Seller Single product tab Template
 *
 * @since 2.4
 *
 * @param WP_User   $author
 * @param Array     $store_info
 *
 * @package dokan
 */

$store_url = dokan_get_store_url( $author->ID );
?>
<div class="row">
	<div class="col-lg-6 mb-4">
		<figure class="vendor-banner">
			<?php echo apply_filters( 'wolmart_lazyload_images', wp_get_attachment_image( $store_info['banner'], 'full' ) ); ?>
		</figure>
	</div>
	<div class="col-lg-6 pl-lg-6 mb-4">
		<div class="vendor-user">
			<figure class="vendor-logo">
				<a href="<?php echo esc_url( $store_url ); ?>" aria-label="<?php esc_attr_e( 'Avatar', 'wolmart' ); ?>">
					<?php echo get_avatar( $author->ID, 90 ); ?>
				</a>
			</figure>
			<div>
				<?php
				printf( '<a href="%s" class="d-block">%s</a>', esc_url( $store_url ), esc_attr( $author->display_name ) );
				$vendor = dokan()->vendor->get( $author->ID );
				$rating = $vendor->get_rating( $vendor->id );
				if ( ! $rating['count'] ) {
					$html = __( 'No ratings found yet!', 'dokan-lite' );
				} else {
					// translators: %1$s represents rating value, %2$d represents 5
					$text  = sprintf( __( 'Rated %1$s out of %2$d', 'dokan-lite' ), $rating['rating'], number_format( 5 ) );
					$width = ( $rating['rating'] / 5 ) * 100;

					$html = '<span class="seller-rating">
						<span title=" ' . esc_attr( $text ) . '" class="star-rating">
							<span class="width" style="width: ' . $width . '%"></span>
							<span>' . $rating['rating'] . '</span>
						</span>
					</span>' .
					// translators: %s represents count of reviews.
					'<span class="text">' . sprintf( esc_html__( '(%s Reviews)', 'wolmart' ), $rating['count'] ) . '</span>';
				}
				echo wolmart_escaped( $html );
				?>
			</div>
		</div>
		<ul class="list-unstyled list sp-vendor-info">
			<?php do_action( 'dokan_product_seller_tab_start', $author, $store_info ); ?>

			<?php if ( ! empty( $store_info['store_name'] ) ) { ?>
				<li class="store-name">
					<span><?php esc_html_e( 'Store Name:', 'dokan-lite' ); ?></span>
					<span class="details">
						<?php echo esc_html( $store_info['store_name'] ); ?>
					</span>
				</li>
			<?php } ?>

			<?php if ( ! dokan_is_vendor_info_hidden( 'address' ) && ! empty( $store_info['address'] ) ) { ?>
				<li class="store-address">
					<span><?php esc_html_e( 'Address:', 'dokan-lite' ); ?></span>
					<span class="details">
						<?php
						$address = dokan_get_seller_address( $author->ID );
						// translators: this "," represents delimiter of address.
						echo wolmart_strip_script_tags( str_replace( '<br/>', esc_html__( ', ', 'wolmart' ), $address ) );
						?>
					</span>
				</li>
			<?php } ?>

			<?php if ( ! dokan_is_vendor_info_hidden( 'phone' ) && ! empty( $store_info['phone'] ) ) { ?>
				<li class="store-phone">
					<span><?php esc_html_e( 'Phone:', 'dokan-lite' ); ?></span>
					<span class="details">
						<?php printf( '<a href="tel:%1$s" role="button">%1$s</a>', esc_html( $store_info['phone'] ) ); ?>
					</span>
				</li>
			<?php } ?>

			<?php do_action( 'dokan_product_seller_tab_end', $author, $store_info ); ?>
		</ul>
		<a href="<?php echo esc_url( $store_url ); ?>" class="btn btn-link btn-underline"><?php esc_html_e( 'Visit Store', 'wolmart' ); ?><i class="w-icon-long-arrow-<?php echo is_rtl() ? 'left' : 'right'; ?>"></i></a>
	</div>
</div>
<?php
$description = get_user_meta( $author->ID, 'description', true );
if ( $description ) {
	echo '<div class="vendor-description pt-3 pb-3">' . wolmart_strip_script_tags( $description ) . '</div>';
}
?>
