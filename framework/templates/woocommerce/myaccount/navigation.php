<?php
/**
 * My Account navigation
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/navigation.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_account_navigation' );
?>

<nav class="woocommerce-MyAccount-navigation col-md-3 mb-8" aria-label="<?php esc_attr_e( 'Account pages', 'woocommerce' ); ?>">
	<ul>
		<?php
		foreach ( wc_get_account_menu_items() as $endpoint => $label ) :
			if ( 'wishlist' == $endpoint ) {
				$url = defined( 'YITH_WCWL' ) ? YITH_WCWL()->get_wishlist_url() : get_home_url();
			} elseif ( 'vendor_dashboard' == $endpoint ) {
				$url   = apply_filters( 'wolmart_account_dashboard_link', '' );
				$label = esc_html__( 'Vendor Dashboard', 'wolmart' );
			} else {
				$url = wc_get_account_endpoint_url( $endpoint );
			}
			if ( ! $url ) {
				continue;
			}

			?>
			<li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
				<a href="<?php echo esc_url( $url ); ?>" <?php echo function_exists( 'wc_is_current_account_menu_item' ) && wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
					<?php echo esc_html( $label ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
