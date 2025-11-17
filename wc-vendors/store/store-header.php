<?php
/**
 * The Template for displaying a store header
 *
 * Override this template by copying it to yourtheme/wc-vendors/store
 *
 * @package    WCVendors_Pro
 * @version    1.6.2
 */

// Verified vendor
$verified_vendor       = ( array_key_exists( '_wcv_verified_vendor', $vendor_meta ) ) ? $vendor_meta['_wcv_verified_vendor'] : false;
$verified_vendor_label = get_option( 'wcvendors_verified_vendor_label' ); // phpcs:ignore
// $verified_vendor_icon_src 	= get_option( 'wcvendors_verified_vendor_icon_src' );

// Store title
$store_title       = ( is_product() ) ? '<a href="' . WCV_Vendors::get_vendor_shop_page( $post->post_author ) . '">' . $vendor_meta['pv_shop_name'] . '</a>' : $vendor_meta['pv_shop_name'];
$store_description = ( array_key_exists( 'pv_shop_description', $vendor_meta ) ) ? $vendor_meta['pv_shop_description'] : '';

// Migrate to store address array
$phone = ( array_key_exists( '_wcv_store_phone', $vendor_meta ) ) ? $vendor_meta['_wcv_store_phone'] : '';

$store_icon_src   = wp_get_attachment_image_src(
	get_user_meta( $vendor_id, '_wcv_store_icon_id', true ),
	array( 150, 150 )
);
$store_icon       = '';
$store_banner_src = wp_get_attachment_image_src( get_user_meta( $vendor_id, '_wcv_store_banner_id', true ), 'full' );
$store_banner     = '';

// see if the array is valid
if ( is_array( $store_icon_src ) ) {
	$store_icon = '<img src="' . $store_icon_src[0] . '" alt="' . esc_attr( $store_title ) . '" class="store-icon" />';
}

if ( is_array( $store_banner_src ) ) {
	$store_banner = '<img src="' . $store_banner_src[0] . '" alt="' . esc_attr( $store_title ) . '" class="store-banner" />';
} else {
	// Getting default banner
	$default_banner_src = get_option( 'wcvendors_default_store_banner_src' );
	$store_banner       = '<img src="' . $default_banner_src . '" alt="' . esc_attr( $store_title ) . '" class="wcv-store-banner" style="max-height: 200px;"/>';
}

// This is where you would load your own custom meta fields if you stored any in the settings page for the dashboard
?>

<?php do_action( 'wcv_before_vendor_store_header' ); ?>

<div class="wcv-header-container">

	<div class="wcv-store-grid wcv-store-header">
		<div id="banner-wrap">
			<?php echo wolmart_escaped( $store_banner ); ?>
			<div id="inner-element">
				<?php if ( ! empty( $store_icon ) ) : ?>
					<div class="store-brand">
						<div class="store-icon-img">
							<?php echo wolmart_escaped( $store_icon ); ?>
						</div>
					</div>
				<?php endif; ?>

					<?php if ( ! empty( $store_icon ) ) : ?>
					<div class="store-info">
						<?php else : ?>
					<div class="store-info">
						<?php endif; ?>

						<?php do_action( 'wcv_before_vendor_store_title' ); ?>
						<h3 class="store-name"><?php echo esc_html( $store_title ); ?></h3>
						<?php do_action( 'wcv_after_vendor_store_title' ); ?>

						<?php do_action( 'wcv_before_vendor_store_rating' ); ?>
						<?php
						if ( ! wc_string_to_bool( get_option( 'wcvendors_ratings_management_cap', 'no' ) ) && class_exists( 'WCVendors_Pro_Ratings_Controller' ) ) {
							echo WCVendors_Pro_Ratings_Controller::ratings_link( $vendor_id, true );
						}
						?>
						<?php do_action( 'wcv_after_vendor_store_rating' ); ?>

						<div class="social-icons-container">
							<?php echo wolmart_wcv_format_store_social_icons( $vendor_id ); ?>
						</div>

						<?php do_action( 'wcv_before_vendor_store_description' ); ?>
						<?php do_action( 'wcv_after_vendor_store_description' ); ?>

						<?php if ( empty( $store_icon ) ) : ?>
							<?php echo wolmart_wcv_format_store_social_icons( $vendor_id ); ?>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>
		<div class="wcv-store-address-container wcv-store-grid ">
			<?php if ( function_exists( 'wcv_format_store_address' ) ) : ?>
			<div class="store-address">
				<address>
					<i class="w-icon-map-marker"></i>
					<?php echo wcv_format_store_address( $vendor_id ); ?>
				</address>
			</div>
			<?php endif; ?>

			<div class="store-phone">
				<?php if ( '' != $phone ) : ?>
					<a href="tel:<?php echo esc_attr( $phone ); ?>" role="button">
						<i class="w-icon-phone"></i>
						<?php echo esc_html( $phone ); ?>
					</a>
				<?php endif; ?>
			</div>
			<?php if ( wc_string_to_bool( get_option( 'wcvendors_show_store_total_sales' ) ) && class_exists( 'WCVendors_Pro' ) ) : ?>
				<div class="store-sales">
					<i class="w-icon-cart"></i>
					<?php
					$label = WCVendors_Pro_Vendor_Controller::get_total_sales_label( $vendor_id, 'store' );
					echo do_shortcode( '[wcv_pro_vendor_totalsales vendor_id="' . $vendor_id . '" label="' . $label . '"]' );
					?>
				</div>
			<?php endif; ?>
			<?php if ( $verified_vendor ) : ?>
				<div class="wcv-verified-vendor">
					<span><i class="w-icon-check"></i> <?php echo esc_html__( 'Verified Vendor', 'wolmart' ); ?></span>
				</div>
			<?php endif; ?>
		</div>
	</div>

	<?php do_action( 'wcv_after_vendor_store_header' ); ?>
