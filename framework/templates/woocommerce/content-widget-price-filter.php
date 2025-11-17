<?php
/**
 * The template for displaying product price filter widget.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-widget-price-filter.php
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://woo.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

defined( 'ABSPATH' ) || exit;
$enable_chart = function_exists( 'wolmart_get_option' ) && true === wolmart_get_option( 'enable_price_chart' ) ? true : false;
?>
<?php do_action( 'woocommerce_widget_price_filter_start', $args ); ?>

<form method="get" action="<?php echo esc_url( $form_action ); ?>">
	<div class="price_slider_wrapper">
		<?php
		if ( $enable_chart && function_exists( 'wolmart_get_filtered_all_price' ) ) {
			wp_enqueue_script( 'wolmart-price-range-chart', WOLMART_PLUGINS_URI . '/other/apexcharts.js', array( 'wolmart-theme' ), WOLMART_VERSION, true );
			$prices = wolmart_get_filtered_all_price( $min_price, $max_price );
			require_once WOLMART_FRAMEWORK . '/admin/customizer/dynamic/dynamic-color-lib.php';
			?>
			<div id="wolmart_price_filter_chart" class="wolmart_price_filter_chart" data-prices="<?php echo esc_attr( json_encode( $prices ) ); ?>" data-stroke-color="<?php echo esc_attr( wolmart_get_option( 'primary_color' ), 0 ); ?>"  data-bg-color="<?php echo esc_attr( WolmartColorLib::lighten( wolmart_get_option( 'primary_color' ), 10 ) ); ?>"></div>
		<?php } ?>
		<div class="price_slider" style="display:none;">
			<?php
			if ( $enable_chart ) {
				?>
						<div class="wolmart-slider-price-before"></div>
						<div class="wolmart-slider-price-after"></div>
				<?php
			}
			?>
		</div>
		<div class="price_slider_amount" data-step="<?php echo esc_attr( $step ); ?>">
			<label class="screen-reader-text" for="min_price"><?php esc_html_e( 'Min price', 'woocommerce' ); ?></label>
			<input type="text" id="min_price" name="min_price" value="<?php echo esc_attr( $current_min_price ); ?>" data-min="<?php echo esc_attr( $min_price ); ?>" placeholder="<?php echo esc_attr__( 'Min price', 'woocommerce' ); ?>" />
			<label class="screen-reader-text" for="max_price"><?php esc_html_e( 'Max price', 'woocommerce' ); ?></label>
			<input type="text" id="max_price" name="max_price" value="<?php echo esc_attr( $current_max_price ); ?>" data-max="<?php echo esc_attr( $max_price ); ?>" placeholder="<?php echo esc_attr__( 'Max price', 'woocommerce' ); ?>" />
			<?php
			/* translators: Filter: verb "to filter" */
			if ( function_exists( 'wc_wp_theme_get_element_class_name' ) ) {
				?>
			<button type="submit" class="button<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>"><?php echo esc_html__( 'Filter', 'woocommerce' ); ?></button>
			<?php } ?>
			<div class="price_label" style="display:none;">
				<?php echo esc_html__( 'Price:', 'woocommerce' ); ?> <span class="from"></span> &mdash; <span class="to"></span>
			</div>
			<?php echo wc_query_string_form_fields( null, array( 'min_price', 'max_price', 'paged' ), '', true ); ?>
			<div class="clear"></div>
		</div>
	</div>
</form>

<?php do_action( 'woocommerce_widget_price_filter_end', $args ); ?>
