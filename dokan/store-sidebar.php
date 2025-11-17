<?php
$vendor_style = wolmart_get_option( 'vendor_style_option' );
$wrapper_cls  = 'plugin' == $vendor_style ? 'dokan-store-sidebar sidebar sidebar-fixed shop-sidebar sidebar-side' : 'sidebar sidebar-fixed shop-sidebar sidebar-side col-lg-3';
$sidebar_pos  = isset( $sidebar_pos ) ? $sidebar_pos : 'left';
$wrapper_cls .= ' ' . $sidebar_pos . '-sidebar';

if ( 'off' == dokan_get_option( 'enable_theme_store_sidebar', 'dokan_appearance', 'off' ) && 'full' != $sidebar_pos ) : ?>

	<?php wp_enqueue_script( 'wolmart-sticky-lib' ); ?>

	<div id="dokan-secondary" class="<?php echo esc_attr( $wrapper_cls ); ?>" role="complementary">

		<div class="sidebar-overlay"></div>
		<a class="sidebar-close" href="#" aria-label="<?php esc_attr_e( 'Close', 'wolmart' ); ?>" role="button"><i class="close-icon"></i></a>
		<a href="#" class="sidebar-toggle" aria-label="<?php esc_attr_e( 'Sidebar Toggle', 'wolmart' ); ?>" role="button"><i class="w-icon-chevron-<?php echo 'right' == $sidebar_pos ? 'left' : 'right'; ?>"></i></a>

		<div class="dokan-widget-area widget-collapse sidebar-content">
			<div class="sticky-sidebar">
				<?php do_action( 'dokan_sidebar_store_before', $store_user->data, $store_info ); ?>
				<?php
				if ( ! dynamic_sidebar( 'sidebar-store' ) ) {
					$args = array(
						'before_widget' => '<aside class="widget dokan-store-widget %s">',
						'after_widget'  => '</aside>',
						'before_title'  => '<h3 class="widget-title"><span class="wt-area">',
						'after_title'   => '</span></h3>',
					);

					if ( dokan()->widgets->is_exists( 'store_category_menu' ) ) {
						the_widget( dokan()->widgets->store_category_menu, array( 'title' => __( 'Store Product Category', 'dokan-lite' ) ), $args );
					}

					// if ( dokan()->widgets->is_exists( 'store_location' ) && dokan_get_option( 'store_map', 'dokan_general', 'on' ) == 'on' && ! empty( $map_location ) ) {
					// 	the_widget( dokan()->widgets->store_location, array( 'title' => __( 'Store Location', 'dokan-lite' ) ), $args );
					// }

					if ( dokan()->widgets->is_exists( 'store_open_close' ) && dokan_get_option( 'store_open_close', 'dokan_general', 'on' ) == 'on' ) {
						the_widget( dokan()->widgets->store_open_close, array( 'title' => __( 'Store Time', 'dokan-lite' ) ), $args );
					}

					if ( dokan()->widgets->is_exists( 'store_contact_form' ) && dokan_get_option( 'contact_seller', 'dokan_general', 'on' ) == 'on' ) {
						the_widget( dokan()->widgets->store_contact_form, array( 'title' => __( 'Contact Vendor', 'dokan-lite' ) ), $args );
					}
				}
				?>

				<?php do_action( 'dokan_sidebar_store_after', $store_user->data, $store_info ); ?>
			</div>
		</div>
	</div><!-- #secondary .widget-area -->
<?php else : ?>
	<?php get_sidebar( 'store' ); ?>
<?php endif; ?>
