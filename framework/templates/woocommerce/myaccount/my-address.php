<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || die;

global $wolmart_customer_address;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => esc_html__( 'Billing address', 'woocommerce' ),
			'shipping' => esc_html__( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => esc_html__( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}
?>

<div class="icon-box icon-box-side woocommerce-MyAccount-content-caption justify-content-start mb-4">
	<span class="icon-box-icon text-grey mr-2">
		<i class="w-icon-map-marker"></i>
	</span>
	<div class="icon-box-content">
		<h4 class="icon-box-title text-normal"><?php echo esc_html_e( 'Addresses', 'wolmart' ); ?></h4>
	</div>
</div>

<p>
	<?php echo apply_filters( 'woocommerce_my_account_my_address_description', esc_html__( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
</p>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	<div class="woocommerce-Addresses addresses row">
<?php endif; ?>

<?php foreach ( $get_addresses as $name => $address_title ) : ?>
	<?php
		$address = wc_get_account_formatted_address( $name );
	?>

	<div class="col-lg-6 mb-4">
		<div class="woocommerce-Address">
			<header class="woocommerce-Address-title title">
				<h2><?php echo esc_html( $address_title ); ?></h2>
			</header>
			<address>
				<?php
				if ( ! is_array( $wolmart_customer_address ) ) {
					echo esc_html_e( 'You have not set up this type of address yet.', 'woocommerce' );
				} else {
					?>
					<table class="address-table">
						<?php
						foreach ( $wolmart_customer_address as $key => $value ) {
							if ( $value ) :
								?>
							<tr>
								<th><?php echo esc_html( $key, 'wolmart' ); ?>:</th>
								<td><?php echo esc_html( $value ); ?></td>
							</tr>
								<?php
							endif;
						}
						?>
					</table>
					<?php
				}
				/**
					 * Used to output content after core address fields.
					 *
					 * @param string $name Address type.
					 * @since 8.7.0
					 */
					do_action( 'woocommerce_my_account_after_my_address', $name );
				?>
			</address>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit btn btn-link btn-primary btn-underline mb-4">
				<?php
					printf(
						/* translators: %s: Address title */
						$address ? esc_html__( 'Edit %s', 'woocommerce' ) : esc_html__( 'Add %s', 'woocommerce' ),
						esc_html( $address_title )
					);
				?>
				<i class="w-icon-long-arrow-<?php echo is_rtl() ? 'left' : 'right'; ?>"></i>
			</a>
		</div>
	</div>

<?php endforeach; ?>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) : ?>
	</div>
	<?php
endif;
