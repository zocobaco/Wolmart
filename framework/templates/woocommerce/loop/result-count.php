<?php
/**
 * Result Count
 *
 * Shows text: Showing x - x of x results.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/result-count.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     9.9.0
 */

defined( 'ABSPATH' ) || die;

global $wolmart_layout;

$ts = isset( $wolmart_layout['top_sidebar'] ) && isset( $wolmart_layout['top_sidebar']['slug'] ) && is_active_sidebar( $wolmart_layout['top_sidebar']['slug'] );

echo '<p class="woocommerce-result-count show-info" role="alert" aria-relevant="all"' . ( ( empty( $orderedby ) || 1 === intval( $total ) ) ? '' : ' data-is-sorted-by="true"' ) . '>';
if ( 1 == $total ) {
	esc_html_e( 'Showing the single result', 'woocommerce' );
} elseif ( $total <= $per_page || -1 == $per_page ) {
	$orderedby_placeholder = empty( $orderedby ) ? '%4$s' : '<span class="screen-reader-text">%4$s</span>';
		/* translators: 1: total results 2: sorted by */
	printf( esc_html( _n( 'Showing %1$sall %2$d%3$s Product', 'Showing %1$sall %2$d%3$s Products', $total, 'wolmart' ) ) . $orderedby_placeholder, '<span>', $total, '</span>', esc_html( $orderedby ) );
} else {
	$first                 = ( $per_page * $current ) - $per_page + 1;
	$last                  = min( $total, $per_page * $current );
	$orderedby_placeholder = empty( $orderedby ) ? '%6$s' : '<span class="screen-reader-text">%6$s</span>';
	/* translators: 1: first result 2: last result 3: total results 4: sorted by */
	printf( esc_html( _nx( 'Showing %4$s%1$d&ndash;%2$d of %3$d%5$s Product', 'Showing %4$s%1$d&ndash;%2$d of %3$d%5$s Products', $total, 'with first and last result', 'wolmart' ) ) . $orderedby_placeholder, $first, $last, $total, '<span>', '</span>', esc_html( $orderedby ) );
}
echo '</p>';
