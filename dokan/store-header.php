<?php
$store_user    = dokan()->vendor->get( get_query_var( 'author' ) );
$store_info    = $store_user->get_shop_info();
$social_info   = $store_user->get_social_profiles();
$store_tabs    = dokan_get_store_tabs( $store_user->get_id() );
$social_fields = dokan_get_social_profile_fields();

$dokan_store_times = ! empty( $store_info['dokan_store_time'] ) ? $store_info['dokan_store_time'] : [];
$current_time      = dokan_current_datetime();
$today             = strtolower( $current_time->format( 'l' ) );

$dokan_appearance = get_option( 'dokan_appearance' );
$profile_layout   = empty( $dokan_appearance['store_header_template'] ) ? 'default' : $dokan_appearance['store_header_template'];
$store_address    = dokan_get_seller_short_address( $store_user->get_id(), false );

$dokan_store_time_enabled = isset( $store_info['dokan_store_time_enabled'] ) ? $store_info['dokan_store_time_enabled'] : '';
$store_open_notice        = isset( $store_info['dokan_store_open_notice'] ) && ! empty( $store_info['dokan_store_open_notice'] ) ? $store_info['dokan_store_open_notice'] : __( 'Store Open', 'dokan-lite' );
$store_closed_notice      = isset( $store_info['dokan_store_close_notice'] ) && ! empty( $store_info['dokan_store_close_notice'] ) ? $store_info['dokan_store_close_notice'] : __( 'Store Closed', 'dokan-lite' );
$show_store_open_close    = dokan_get_option( 'store_open_close', 'dokan_appearance', 'on' );

$general_settings = get_option( 'dokan_general', [] );
$banner_width     = dokan_get_option( 'store_banner_width', 'dokan_appearance', 625 );


if ( ( 'default' === $profile_layout ) || ( 'layout2' === $profile_layout ) ) {
	$profile_img_class = 'profile-img-circle';
} else {
	$profile_img_class = 'profile-img-square';
}

if ( 'layout3' === $profile_layout ) {
	unset( $store_info['banner'] );

	$no_banner_class      = ' profile-frame-no-banner';
	$no_banner_class_tabs = ' dokan-store-tabs-no-banner';

} else {
	$no_banner_class      = '';
	$no_banner_class_tabs = '';
}

?>
<div class="dokan-profile-frame-wrapper">
	<div class="profile-frame<?php echo esc_attr( $no_banner_class ); ?>">

		<div class="profile-info-box profile-layout-<?php echo esc_attr( $profile_layout ); ?>">
			<?php if ( $store_user->get_banner() ) { ?>
				<img src="<?php echo esc_url( $store_user->get_banner() ); ?>"
					alt="<?php echo esc_attr( $store_user->get_shop_name() ); ?>"
					title="<?php echo esc_attr( $store_user->get_shop_name() ); ?>"
					class="profile-info-img">
			<?php } else { ?>
				<div class="profile-info-img dummy-image">&nbsp;</div>
			<?php } ?>

			<div class="profile-info-summery-wrapper dokan-clearfix">
				<div class="profile-info-summery">
					<div class="profile-info-head">
						<div class="profile-img <?php echo esc_attr( $profile_img_class ); ?>">
							<img src="<?php echo esc_url( $store_user->get_avatar() ); ?>"
								alt="<?php echo esc_attr( $store_user->get_shop_name() ); ?>">
						</div>
						<?php if ( ! empty( $store_user->get_shop_name() ) && 'default' === $profile_layout ) { ?>
							<h1 class="store-name text-capitalize font-weight-bold"><?php echo esc_html( $store_user->get_shop_name() ); ?></h1>
						<?php } ?>
					</div>

					<div class="profile-info">
						<?php if ( ! empty( $store_user->get_shop_name() ) && 'default' !== $profile_layout ) { ?>
							<h4 class="store-name"><?php echo esc_html( $store_user->get_shop_name() ); ?></h4>
						<?php } ?>

						<ul class="dokan-store-info">
							<?php if ( ! dokan_is_vendor_info_hidden( 'address' ) && isset( $store_address ) && ! empty( $store_address ) ) { ?>
								<li class="dokan-store-address"><i class="w-icon-map-marker"></i>
									<?php echo wp_kses_post( $store_address ); ?>
								</li>
							<?php } ?>

							<?php if ( ! dokan_is_vendor_info_hidden( 'phone' ) && ! empty( $store_user->get_phone() ) ) { ?>
								<li class="dokan-store-phone">
									<i class="w-icon-phone"></i>
									<a href="tel:<?php echo esc_html( $store_user->get_phone() ); ?>" role="button"><?php echo esc_html( $store_user->get_phone() ); ?></a>
								</li>
							<?php } ?>

							<?php if ( ! dokan_is_vendor_info_hidden( 'email' ) && $store_user->show_email() == 'yes' ) { ?>
								<li class="dokan-store-email">
									<i class="w-icon-envelope-o"></i>
									<a href="mailto:<?php echo esc_attr( antispambot( $store_user->get_email() ) ); ?>" role="button"><?php echo esc_html( antispambot( $store_user->get_email() ) ); ?></a>
								</li>
							<?php } ?>

							<li class="dokan-store-rating">
								<i class="w-icon-star-full"></i>
								<?php echo wp_kses_post( dokan_get_readable_seller_rating( $store_user->get_id() ) ); ?>
							</li>

							<?php if ( 'on' == $show_store_open_close && 'yes' == $dokan_store_time_enabled ) : ?>
								<li class="dokan-store-open-close">
									<i class="w-icon-cart"></i>
									<div class="store-open-close-notice">
										<?php if ( dokan_is_store_open( $store_user->get_id() ) ) : ?>
											<span class='store-notice'><?php echo esc_attr( $store_open_notice ); ?></span>
										<?php else : ?>
											<span class='store-notice'><?php echo esc_attr( $store_closed_notice ); ?></span>
										<?php endif; ?>

										<span class="w-icon-angle-down-solid"></span>
										<?php
										// Vendor store times template shown here.
										dokan_get_template_part(
											'store-header-times',
											'',
											[
												'dokan_store_times' => $dokan_store_times,
												'today' => $today,
												'dokan_days' => dokan_get_translated_days(),
												'current_time' => $current_time,
												'times_heading' => __( 'Weekly Store Timing', 'dokan-lite' ),
												'closed_status' => __( 'CLOSED', 'dokan-lite' ),
											]
										);
										?>
									</div>
								</li>
							<?php endif ?>

							<?php do_action( 'dokan_store_header_info_fields', $store_user->get_id() ); ?>
						</ul>

						<?php if ( $social_fields ) { ?>
							<div class="store-social-wrapper">
								<ul class="store-social social-icons">
									<?php foreach ( $social_fields as $key => $field ) { ?>
										<?php if ( ! empty( $social_info[ $key ] ) ) { ?>
											<li>
												<a class="social-icon framed use-hover social-<?php echo esc_attr( $field['icon'] ); ?>" href="<?php echo esc_url( $social_info[ $key ] ); ?>" aria-label="<?php esc_attr_e( 'Social Icon', 'wolmart' ); ?>" target="_blank"><i class="w-icon-<?php echo esc_attr( $field['icon'] ); ?>"></i></a>
											</li>
										<?php } ?>
									<?php } ?>
								</ul>
							</div>
						<?php } ?>

					</div> <!-- .profile-info -->
				</div><!-- .profile-info-summery -->
			</div><!-- .profile-info-summery-wrapper -->
		</div> <!-- .profile-info-box -->
	</div> <!-- .profile-frame -->

	<?php if ( $store_tabs ) { ?>
		<div class="dokan-store-tabs mt-4 <?php echo esc_attr( $no_banner_class_tabs ); ?>">
			<ul class="dokan-list-inline br-3" role="tablist">
				<?php foreach ( $store_tabs as $key => $tab ) { ?>
					<?php if ( $tab['url'] ) : ?>
						<li role="tab"><a class="font-weight-semi-bold" href="<?php echo esc_url( $tab['url'] ); ?>"><?php echo esc_html( $tab['title'] ); ?></a></li>
					<?php endif; ?>
				<?php } ?>

				<?php do_action( 'dokan_after_store_tabs', $store_user->get_id() ); ?>
			</ul>
		</div>
	<?php } ?>
</div>
