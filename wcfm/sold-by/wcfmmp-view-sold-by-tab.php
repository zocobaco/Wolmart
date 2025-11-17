<?php
/**
 * The Template for displaying store.
 *
 * @package WCfM Markeplace Views Store Sold By as Tab
 *
 * For edit coping this to yourtheme/wcfm/sold-by
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCFM, $WCFMmp;

$vendor_id = wcfm_get_vendor_id_by_post( $product_id );

if ( $vendor_id ) {
	if ( apply_filters( 'wcfmmp_is_allow_sold_by', true, $vendor_id ) && wcfm_vendor_has_capability( $vendor_id, 'sold_by' ) ) {
		// Check is store Online
		$is_store_offline = get_user_meta( $vendor_id, '_wcfm_store_offline', true );
		if ( $is_store_offline ) {
			return;
		}

		$sold_by_text = $WCFMmp->wcfmmp_vendor->sold_by_label( absint( $vendor_id ) );
		$store_name   = wcfm_get_vendor_store_name( absint( $vendor_id ) );
		$store_user   = wcfmmp_get_store( $vendor_id );
		$store_info   = $store_user->get_shop_info();

		$gravatar              = $store_user->get_avatar();
		$email                 = $store_user->get_email();
		$phone                 = $store_user->get_phone();
		$address               = $store_user->get_address_string();
		$store_url             = wcfmmp_get_store_url( $vendor_id );
		$wcfm_shop_description = apply_filters( 'woocommerce_short_description', $store_user->get_shop_description() );

		$banner_type = $store_user->get_list_banner_type();
		if ( 'video' == $banner_type ) {
			$banner_video = $store_user->get_list_banner_video();
		} else {
			$vendor_data = get_user_meta( $vendor_id, 'wcfmmp_profile_settings', true );
			$banner      = isset( $vendor_data['banner'] ) ? absint( $vendor_data['banner'] ) : 0;
		}

		?>
		<div class="row">
			<div class="col-lg-5 mb-4">
				<?php if ( 'video' == $banner_type ) : ?>
					<?php echo preg_replace( '/\s*[a-zA-Z\/\/:\.]*youtu(be.com\/watch\?v=|.be\/)([a-zA-Z0-9\-_]+)([a-zA-Z0-9\/\*\-\_\?\&\;\%\=\.]*)/i', '<iframe width="100%" height="315" frameborder="0" allow="autoplay; encrypted-media" src="//www.youtube.com/embed/$2?iv_load_policy=3&enablejsapi=1&disablekb=1&autoplay=0&controls=0&showinfo=0&rel=0&loop=1&wmode=transparent&widgetid=1" allowfullscreen></iframe>', $banner_video ); ?>
				<?php endif; ?>

				<?php if ( 'video' != $banner_type ) : ?>
				<figure class="vendor-banner br-5 overflow-hidden">
					<?php echo apply_filters( 'wolmart_lazyload_images', wp_get_attachment_image( $banner, 'full' ) ); ?>
				</figure>
				<?php endif; ?>
			</div>

			<div class="col-lg-7 pl-lg-6 mb-4">
				<div class="vendor-user">

					<?php do_action( 'before_wcfmmp_sold_by_gravatar_product_page', $vendor_id ); ?>

					<figure class="vendor-logo">
						<a href="<?php echo esc_url( $store_url ); ?>" aria-label="<?php esc_attr_e( 'Store Logo', 'wolmart' ); ?>">
							<img src="<?php echo esc_url( $gravatar ); ?>" alt="<?php echo esc_attr( $store_name ); ?>">
						</a>
					</figure>

					<div>
						<?php printf( '<a href="%s">%s</a>', esc_url( $store_url ), esc_attr( $store_name ) ); ?>
						<?php
						if ( apply_filters( 'wcfm_is_pref_vendor_reviews', true ) ) {
							echo wc_get_rating_html( $WCFMmp->wcfmmp_reviews->get_vendor_review_rating( $vendor_id ) ? $WCFMmp->wcfmmp_reviews->get_vendor_review_rating( $vendor_id ) : 0 );
						}
						?>
					</div>
				</div>

				<ul class="list-unstyled list sp-vendor-info">

					<?php do_action( 'before_wcfmmp_sold_by_info_product_page', $vendor_id ); ?>

					<li class="store-name">
						<span><?php esc_html_e( 'Store Name:', 'wolmart' ); ?></span>
						<span class="details">
							<?php echo esc_html( $store_name ); ?>
						</span>
					</li>

					<?php if ( $address && 'no' == ( $store_info['store_hide_address'] ) && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'vendor_address' ) ) : ?>
					<li class="store-address">
						<span><?php esc_html_e( 'Address:', 'wolmart' ); ?></span>
						<span class="details">
							<?php echo esc_html( $address ); ?>
						</span>
					</li>
					<?php endif; ?>

					<?php if ( $email && 'no' == ( $store_info['store_hide_email'] ) && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'vendor_email' ) ) : ?>
					<li class="store-email">
						<span><?php echo esc_html_e( 'Email:', 'wolmart' ); ?></span>
						<span class="details">
							<?php echo esc_html( $email ); ?>
						</span>
					</li>
					<?php endif; ?>

					<?php if ( $phone && ( 'no' == $store_info['store_hide_phone'] ) && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $vendor_id, 'vendor_phone' ) ) : ?>
					<li class="store-phone">
						<span><?php echo esc_html_e( 'Phone:', 'wolmart' ); ?></span>
						<span class="details">
							<?php echo esc_html( $phone ); ?>
						</span>
					</li>
					<?php endif; ?>

					<?php do_action( 'after_wcfmmp_sold_by_info_product_page', $vendor_id ); ?>
				</ul>
				<a href="<?php echo esc_url( $store_url ); ?>" class="btn btn-link btn-underline"><?php esc_html_e( 'Visit Store', 'wolmart' ); ?><i class="w-icon-long-arrow-<?php echo is_rtl() ? 'left' : 'right'; ?>"></i></a>
			</div>
		</div>
		<?php

		if ( $wcfm_shop_description ) {
			echo '<div class="vendor-description pt-3 pb-3">' . wolmart_strip_script_tags( $wcfm_shop_description ) . '</div>';
		}

		if ( apply_filters( 'wcfmmp_is_allow_sold_by_location', true ) ) {
			$api_key      = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_google_map_api'] : '';
			$wcfm_map_lib = isset( $WCFMmp->wcfmmp_marketplace_options['wcfm_map_lib'] ) ? $WCFMmp->wcfmmp_marketplace_options['wcfm_map_lib'] : '';

			if ( ! $wcfm_map_lib && $api_key ) {
				$wcfm_map_lib = 'google';
			} elseif ( ! $wcfm_map_lib && ! $api_key ) {
				$wcfm_map_lib = 'leaftlet';
			}

			$store_lat = isset( $store_info['store_lat'] ) ? esc_attr( $store_info['store_lat'] ) : 0;
			$store_lng = isset( $store_info['store_lng'] ) ? esc_attr( $store_info['store_lng'] ) : 0;

			if ( ( ( ( 'google' == $wcfm_map_lib ) && ! empty( $api_key ) ) || ( 'leaflet' == $wcfm_map_lib ) ) && ! empty( $store_lat ) && ! empty( $store_lng ) && $WCFM->wcfm_vendor_support->wcfm_vendor_has_capability( $store_user->get_id(), 'vendor_map' ) ) {

				echo '<div class="wcfmmp_store_tab_info wcfmmp_store_info_store_location">';

				do_action( 'before_wcfmmp_sold_by_location_product_page', $store_user->get_id() );

				$WCFMmp->template->get_template(
					'store/widgets/wcfmmp-view-store-location.php',
					array(
						'store_user' => $store_user,
						'address'    => $address,
						'store_info' => $store_info,
						'store_lat'  => $store_lat,
						'store_lng'  => $store_lng,
						'map_id'     => 'wcfm_sold_by_tab_map_' . rand( 10, 100 ),
					)
				);

				do_action( 'after_wcfmmp_sold_by_location_product_page', $store_user->get_id() );

				echo '</div>';

				wp_enqueue_script( 'wcfmmp_store_js', $WCFMmp->library->js_lib_url . 'store/wcfmmp-script-store.js', array( 'jquery' ), $WCFMmp->version, true );
				$WCFMmp->library->load_map_lib();

				// Default Map Location
				$default_geolocation = isset( $WCFMmp->wcfmmp_marketplace_options['default_geolocation'] ) ? $WCFMmp->wcfmmp_marketplace_options['default_geolocation'] : array();
				$store_location      = isset( $default_geolocation['location'] ) ? esc_attr( $default_geolocation['location'] ) : '';
				$map_address         = isset( $default_geolocation['address'] ) ? esc_attr( $default_geolocation['address'] ) : '';
				$default_lat         = isset( $default_geolocation['lat'] ) ? esc_attr( $default_geolocation['lat'] ) : apply_filters( 'wcfmmp_map_default_lat', 30.0599153 );
				$default_lng         = isset( $default_geolocation['lng'] ) ? esc_attr( $default_geolocation['lng'] ) : apply_filters( 'wcfmmp_map_default_lng', 31.2620199 );
				$default_zoom        = apply_filters( 'wcfmmp_map_default_zoom_level', 17 );
				$store_icon          = apply_filters( 'wcfmmp_map_store_icon', $WCFMmp->plugin_url . 'assets/images/wcfmmp_map_icon.png', 0, '' );

				wp_localize_script(
					'wcfmmp_store_js',
					'wcfmmp_store_map_options',
					array(
						'default_lat'          => $default_lat,
						'default_lng'          => $default_lng,
						'default_zoom'         => absint( $default_zoom ),
						'store_icon'           => $store_icon,
						'icon_width'           => apply_filters( 'wcfmmp_map_icon_width', 40 ),
						'icon_height'          => apply_filters( 'wcfmmp_map_icon_height', 57 ),
						'is_poi'               => apply_filters( 'wcfmmp_is_allow_map_poi', true ),
						'is_allow_scroll_zoom' => apply_filters( 'wcfmmp_is_allow_map_scroll_zoom', true ),
						'is_rtl'               => is_rtl(),
					)
				);
			}
		}

		do_action( 'after_wcfmmp_sold_by_label_product_page', $vendor_id );

		do_action( 'after_wcfmmp_sold_by_info_product_page', $vendor_id );
	}
}
