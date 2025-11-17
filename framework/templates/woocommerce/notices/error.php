<?php
/**
 * Show error messages
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/notices/error.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || die;

if ( ! $notices ) {
	return;
}

?>
<ul class="woocommerce-error" role="alert">
	<?php foreach ( $notices as $notice ) : ?>
		<li class="<?php echo apply_filters( 'wolmart_wc_notice_class', '', $notice, 'error' ); ?>" <?php echo wc_get_notice_data_attr( $notice ); ?>>
			<?php
			do_action( 'wolmart_wc_before_notice', $notice, 'error' );
			echo wc_kses_notice( $notice['notice'] );
			do_action( 'wolmart_wc_after_notice', $notice, 'error' );
			?>
		</li>
	<?php endforeach; ?>
</ul>
