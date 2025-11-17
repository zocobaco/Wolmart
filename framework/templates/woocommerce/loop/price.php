<?php
/**
 * Loop Price
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$show_info = wolmart_wc_get_loop_prop( 'show_info', false );

if ( is_array( $show_info ) && ! in_array( 'price', $show_info ) ) {
	return;
}

global $product;

$price_html = $product->get_price_html();
if ( $price_html ) :
	?>
	<span class="price"><?php echo wolmart_strip_script_tags( $price_html ); ?></span>
	<?php
endif;

if ( is_array( $show_info ) && in_array( 'rating', $show_info ) && 'product-11' == wolmart_wc_get_loop_prop( 'product_type' ) ) {
	echo '</div>';
}
