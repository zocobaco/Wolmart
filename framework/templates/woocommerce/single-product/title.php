<?php
/**
 * Single Product title
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/title.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see        https://docs.woocommerce.com/document/template-structure/
 * @package   WooCommerce/Templates
 * @version    1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$title = get_the_title();

if ( strlen( $title ) ) {

	$tag = apply_filters( 'wolmart_single_product_title_tag', wolmart_doing_quickview() ? 'h2' : 'h1' );

	if ( ! in_array( $tag, array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6' ) ) ) {
		$tag = 'h1';
	}

	if ( apply_filters( 'wolmart_is_single_product_widget', false ) || wolmart_doing_quickview() ) {
		$title = '<a href="' . get_the_permalink() . ' ">' . $title . '</a>';
	}

	echo  '<' . esc_html( $tag ) . ' class="product_title entry-title">' . wolmart_escaped( $title ) . '</' . esc_html( $tag ) . '>';
}
