<?php
/**
 * Wolmart WooCommerce Free Shipping Bar Functions
 *
 * Functions used to display free shipping bar on single product, cart, checkout and offcanvas/dropdown.
 */

defined( 'ABSPATH' ) || die;

if ( wolmart_get_option( 'show_freeshipping_bar' ) && ( wolmart_get_option( 'freeshipping_sp' ) || wolmart_get_option( 'freeshipping_cart' ) || wolmart_get_option( 'freeshipping_checkout' ) || wolmart_get_option( 'freeshipping_minicart' ) ) ) {
	add_action( 'wp_enqueue_scripts', 'wolmart_free_shipping_style', 20 );
	// Single Product
	if ( wolmart_get_option( 'freeshipping_sp' ) ) {
		add_action( 'woocommerce_single_product_summary', 'wolmart_shipping_progress_bar', 29 );
	}
	if ( wolmart_get_option( 'freeshipping_cart' ) ) {
		// Before cart table of Cart Page
		add_action( 'woocommerce_before_cart_table', 'wolmart_shipping_progress_bar' );
	}
	if ( wolmart_get_option( 'freeshipping_checkout' ) ) {
		// After Total Price of Checkout Page
		add_action( 'wolmart_woocommerce_after_checkout_table', 'wolmart_shipping_progress_bar' );
	}
	if ( wolmart_get_option( 'freeshipping_minicart' ) ) {
		// Cart Dropdown & Cart Offcanvas
		add_action( 'wolmart_before_mini_cart_total', 'wolmart_shipping_progress_bar' );
	}
}

if ( ! function_exists( 'wolmart_shipping_progress_bar' ) ) {
	// Show Progress Bar
	function wolmart_shipping_progress_bar() {
		if ( doing_action( 'woocommerce_single_product_summary' ) && ! is_singular( 'product' ) ) {
			return;
		}

		if ( ! WC()->cart->needs_shipping() || ! WC()->cart->show_shipping() ) {
			return;
		}
		$free_shipping_threshold = 0;
		$subtotal                = (int) WC()->cart->get_displayed_subtotal();
		$classes                 = 'wolmart-free-shipping';
		$free_shipping_by_coupon = false;

		// Check shipping packages.
		$packages = WC()->cart->get_shipping_packages();
		$package  = reset( $packages );
		$zone     = wc_get_shipping_zone( $package );

		foreach ( $zone->get_shipping_methods( true ) as $method ) {
			if ( 'free_shipping' === $method->id ) {
				$free_shipping_threshold = $method->get_option( 'min_amount' );
			}
		}

		// WPML.
		if ( class_exists( 'woocommerce_wpml' ) && ! class_exists( 'WCML_Multi_Currency_Shipping' ) ) {
			global $woocommerce_wpml;

			$multi_currency = $woocommerce_wpml->get_multi_currency();

			if ( ! empty( $multi_currency->prices ) && method_exists( $multi_currency->prices, 'convert_price_amount' ) ) {
				$free_shipping_threshold = $multi_currency->prices->convert_price_amount( $free_shipping_threshold );
			}
		}

		// Check coupons.
		if ( $subtotal && WC()->cart->get_coupons() ) {
			foreach ( WC()->cart->get_coupons() as $coupon ) {
				$subtotal -= WC()->cart->get_coupon_discount_amount( $coupon->get_code(), WC()->cart->display_cart_ex_tax );
				if ( $coupon->get_free_shipping() ) {
					$free_shipping_by_coupon = true;
					break;
				}
			}
		}
		$free_shipping_threshold = (int) str_replace( array( get_woocommerce_currency_symbol(), html_entity_decode( get_woocommerce_currency_symbol() ), wc_get_price_thousand_separator() ), '', apply_filters( 'wolmart_free_shipping_threshold', $free_shipping_threshold ) );

		if ( ! $free_shipping_threshold ) {
			return;
		}

		$classes = apply_filters( 'wolmart_free_shipping_wrap_cls', $classes );

		if ( $subtotal < $free_shipping_threshold && ! $free_shipping_by_coupon ) :
			$percent = floor( ( $subtotal / $free_shipping_threshold ) * 100 );
			?>
			<div class="<?php echo esc_attr( $classes ); ?>">
				<div class="wolmart-free-shipping-notice">
					<label>
						<?php
						$threshold = wc_price( $free_shipping_threshold - $subtotal );
						echo str_replace( '[remainder]', $threshold, trim( wolmart_get_option( 'freeshipping_initial' ) ) );
						?>
					</label>
				</div>
				<progress class="wolmart-free-shipping-bar wolmart-scroll-progress" max="100" value="<?php echo esc_attr( $percent ); ?>"></progress>
			</div>
		<?php else : // Success message. ?>
			<div class="<?php echo esc_attr( $classes ); ?>">
				<div class="wolmart-free-shipping-notice fs-success"><?php esc_html_e( 'Your order qualifies for free shipping!', 'wolmart' ); ?></div>
				<progress class="wolmart-free-shipping-bar wolmart-scroll-progress" max="100" value="100"></progress>
			</div>
			<?php
		endif;
	}
}

function wolmart_free_shipping_style() {
	wp_enqueue_style( 'wolmart-fs-progress-bar', WOLMART_ADDONS_URI . '/free-shipping-bar/free-shipping-bar.css', array(), WOLMART_VERSION );
}
