<?php
/**
 * Vendor Review Comments Template
 *
 * Closing li is left out on purpose!.
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/review/rating.php.
 *
 * HOWEVER, on occasion WC Marketplace will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 *
 * @author  WC Marketplace
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 3.3.5
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCMp; //phpcs:ignore
$rating             = round( $rating_val_array['avg_rating'], 1 );
$count              = intval( $rating_val_array['total_rating'] );
$rating_type        = $rating_val_array['rating_type'];
$review_before_text = esc_html__( 'Rating From', 'wolmart' );

if ( 'product-rating' == $rating_type ) {
	$review_text = $count > 1 ? esc_html__( 'Products reviews', 'dc-woocommerce-multi-vendor' ) : esc_html__( 'Product review', 'dc-woocommerce-multi-vendor' );
} else {
	$review_text = $count > 1 ? esc_html__( 'Reviews', 'dc-woocommerce-multi-vendor' ) : esc_html__( 'Review', 'dc-woocommerce-multi-vendor' );
}

?> 

<?php if ( $count > 0 ) { ?>
	<span class="wcmp_total_rating_number">
		<i class="w-icon-star-full"></i>
		<?php echo sprintf( ' %s %s %s %s', $rating, $review_before_text, $count, $review_text ); //phpcs:ignore ?>
	</span>
	<?php
} else {
	?>
	<span>
		<?php esc_html_e( ' No Review Yet ', 'dc-woocommerce-multi-vendor' ); ?>
	</span>
<?php } ?>
