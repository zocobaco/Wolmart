<?php
/**
 * The Template for displaying product archives, including the main shop page which is a post type archive
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/archive-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.6.0
 */

defined( 'ABSPATH' ) || die;

/**
 * Load only posts.
 */
$only_posts = wolmart_doing_ajax() && isset( $_GET['only_posts'] );

if ( ! $only_posts ) {
	get_header( 'shop' );
} else {
	wolmart_print_title_bar();
}
global $wolmart_layout;
do_action( 'wolmart_before_content' );

/**
 * Hook: woocommerce_before_main_content.
 *
 * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
 * @removed woocommerce_breadcrumb - 20
 * @hooked WC_Structured_Data::generate_website_data() - 30
 */
do_action( 'woocommerce_before_main_content' );
/**
 * Hook: woocommerce_shop_loop_header.
 *
 * @since 8.6.0
 *
 * @hooked woocommerce_product_taxonomy_archive_header - 10
 */
do_action( 'woocommerce_shop_loop_header' );

if ( woocommerce_product_loop() ) {

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @removed woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */
	do_action( 'woocommerce_before_shop_loop' );

	woocommerce_product_loop_start();

	if ( wolmart_wc_get_loop_prop( 'total' ) ) {
		while ( have_posts() ) {
			the_post();

			/**
			 * Hook: woocommerce_shop_loop.
			 */
			do_action( 'woocommerce_shop_loop' );

			wc_get_template_part( 'content', 'product' );
		}
	}

	woocommerce_product_loop_end();

	$total    = wolmart_wc_get_loop_prop( 'total', 0 );
	$per_page = wolmart_wc_get_loop_prop( 'per_page', 0 );
	if ( ! ( 1 == $total || $total <= $per_page || -1 == $per_page || ( ! empty( $wolmart_layout['loadmore_type'] ) && 'page' != $wolmart_layout['loadmore_type'] ) ) ) {
		?>

		<div class="toolbox toolbox-pagination">

			<?php
			/**
			 * Hook: wolmart_result_count.
			 *
			 * @added woocommerce_result_count
			 */
			do_action( 'wolmart_wc_result_count' );

			/**
			 * Hook: woocommerce_after_shop_loop.
			 *
			 * @hooked woocommerce_pagination - 10
			 */
			do_action( 'woocommerce_after_shop_loop' );
			?>

		</div>

		<?php
	}
} else {
	wolmart_wc_shop_top_sidebar();

	/**
	 * Hook: woocommerce_before_shop_loop.
	 *
	 * @hooked woocommerce_output_all_notices - 10
	 * @removed woocommerce_result_count - 20
	 * @hooked woocommerce_catalog_ordering - 30
	 */

	// woocommerce_product_loop_start();

	/**
	 * Hook: woocommerce_no_products_found.
	 *
	 * @hooked wc_no_products_found - 10
	 */
	do_action( 'woocommerce_no_products_found' );

	// woocommerce_product_loop_end();
}

/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action( 'woocommerce_sidebar' );

/**
 * Hook: woocommerce_after_main_content.
 *
 * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
 */
do_action( 'woocommerce_after_main_content' );

do_action( 'wolmart_after_content' );

if ( ! $only_posts ) {
	get_footer( 'shop' );
}
