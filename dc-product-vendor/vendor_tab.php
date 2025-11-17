<?php

/**
 * The template for displaying single product page vendor tab
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor_tab.php
 *
 * @author      WC Marketplace
 * @package     dc-product-vendor/Templates
 * @version   2.2.0
 */
global $WCMp, $product; //phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

$vendor = get_wcmp_product_vendors( $product->get_id() );
$banner = get_user_meta( $vendor->id, '_vendor_banner', true ) ? get_user_meta( $vendor->id, '_vendor_banner', true ) : 0;

if ( $vendor ) {
	if ( 'section' == apply_filters( 'wolmart_single_product_data_tab_type', 'tab' ) ) : ?>
		<h2 class="title-wrapper title-underline">
			<span class="title"><?php echo esc_html( wolmart_get_option( 'product_vendor_info_title' ) ); ?></span>
		</h2>
	<?php endif; ?>

	<div class="row">
		<div class="col-lg-5 mb-4">
			<figure class="vendor-banner">
				<?php echo apply_filters( 'wolmart_lazyload_images', wp_get_attachment_image( $banner, 'full' ) ); ?>
			</figure>
		</div>
		<div class="col-lg-7 pl-lg-6 mb-4">
			<div class="vendor-user">
				<figure class="vendor-logo">
					<a href="<?php echo esc_url( $vendor->permalink ); ?>" aria-label="<?php esc_attr_e( 'Avatar', 'wolmart' ); ?>">
						<?php echo get_avatar( $vendor->id, 90 ); ?>
					</a>
				</figure>

				<div>
					<?php printf( '<a href="%s">%s</a>', esc_url( $vendor->permalink ), esc_attr( $vendor->user_data->data->display_name ) ); ?>

					<?php

					$rating = wcmp_get_vendor_review_info( $vendor->term_id );

					if ( ! $rating['total_rating'] ) {
						$html = __( 'No ratings found yet!', 'wolmart' );
					} else {
						// translators: %1$s represents rating value, %2$d represents 5
						$text  = sprintf( __( 'Rated %1$s out of %2$d', 'wolmart' ), $rating['avg_rating'], number_format( 5 ) );
						$width = ( $rating['avg_rating'] / 5 ) * 100;

						$html = '<span class="seller-rating">
							<span title=" ' . esc_attr( $text ) . '" class="star-rating" itemtype="http://schema.org/Rating" itemscope="">
								<span class="width" style="width: ' . $width . '%"></span>
								<span style=""><strong>' . $rating['avg_rating'] . '</strong></span>
							</span>
						</span>' .
						// translators: %s represents count of reviews.
						'<span class="text">' . sprintf( esc_html__( '(%s Reviews)', 'wolmart' ), $rating['total_rating'] ) . '</span>';
					}
					echo wolmart_escaped( $html );
					?>
				</div>
			</div>

			<ul class="list-unstyled list sp-vendor-info">

				<?php if ( null != apply_filters( 'wcmp_vendor_lists_single_button_text', $vendor->page_title ) ) { ?>
					<li class="store-name">
						<span><?php esc_html_e( 'Store Name:', 'wolmart' ); ?></span>
						<span class="details">
							<?php echo esc_html( apply_filters( 'wcmp_vendor_lists_single_button_text', $vendor->page_title ) ); ?>
						</span>
					</li>
				<?php } ?>

				<?php if ( $vendor->get_formatted_address() ) { ?>
					<li class="store-address">
						<span><?php esc_html_e( 'Address:', 'wolmart' ); ?></span>
						<span class="details">
							<?php echo esc_html( $vendor->get_formatted_address() ); ?>
						</span>
					</li>
				<?php } ?>

				<?php if ( $vendor->phone ) { ?>
					<li class="store-phone">
						<span><?php esc_html_e( 'Phone:', 'wolmart' ); ?></span>
						<span class="details">
							<?php printf( '<a href="tel:%1$s" role="button">%1$s</a>', esc_html( $vendor->phone ) ); ?>
						</span>
					</li>
				<?php } ?>

			</ul>
			<a href="<?php echo esc_url( $vendor->permalink ); ?>" class="btn btn-link btn-underline"><?php esc_html_e( 'Visit Store', 'wolmart' ); ?><i class="w-icon-long-arrow-<?php echo is_rtl() ? 'left' : 'right'; ?>"></i></a>
		</div>
	</div>

	<?php

	if ( $vendor->description ) {
		echo '<div class="vendor-description pt-3 pb-3">' . wolmart_strip_script_tags( $vendor->description ) . '</div>';
	}
	do_action( 'wcmp_after_vendor_tab' );
}
