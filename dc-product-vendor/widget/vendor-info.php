<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/widget/vendor-info.php
 *
 * @author      WC Marketplace
 * @package     dc-product-vendor/Templates
 * @version     0.0.1
 */

global $WCMp; //phpcs:ignore
?>

<div>
	<div class="wcmp-vendor-info-wrapper">
		<figure class="vendor-logo">
			<?php echo wp_get_attachment_image( $vendor->image, 'thumbnail' ); ?>
		</figure>
		<div class="wcmp-vendor-info ml-4">
			<h4 class="vendor-name"><?php echo esc_html( $vendor->page_title ); ?> </h4>
			<?php
			$description = strip_tags( $vendor->description );
			if ( strlen( $description ) > 50 ) {
				// truncate string
				$string_cut = substr( $description, 0, 50 );

				// make sure it ends in a word so assassinate doesn't become ass...
				$description = substr( $string_cut, 0, strrpos( $string_cut, ' ' ) ) . '...';
			}
			?>
			<p><?php echo esc_html( $description ); ?> </p>
		</div>
	</div>
</div>

<p>
	<a href="<?php echo esc_url( $vendor->permalink ); ?>" title="<?php echo sprintf( __( 'More Products from %1$s', 'dc-woocommerce-multi-vendor' ), $vendor->page_title ); ?>">
		<?php echo sprintf( __( 'More Products from %1$s', 'dc-woocommerce-multi-vendor' ), $vendor->page_title ); ?>
	</a>
</p>
