<?php
/**
 * Show options for ordering
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/loop/orderby.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package    WooCommerce/Templates
 * @version     9.7.0
 */

defined( 'ABSPATH' ) || die;

global $wolmart_layout;

if ( 'archive_product' != wolmart_get_page_layout() && ! apply_filters( 'wolmart_is_vendor_store', false ) ) {
	return;
}

$ts = ! empty( $wolmart_layout['top_sidebar'] ) && 'hide' != $wolmart_layout['top_sidebar'] && is_active_sidebar( $wolmart_layout['top_sidebar'] );
$ls = ! empty( $wolmart_layout['left_sidebar'] ) && 'hide' != $wolmart_layout['left_sidebar'] && is_active_sidebar( $wolmart_layout['left_sidebar'] );
$rs = ! empty( $wolmart_layout['right_sidebar'] ) && 'hide' != $wolmart_layout['right_sidebar'] && is_active_sidebar( $wolmart_layout['right_sidebar'] );

if ( $ts ) {
	echo '<div class="toolbox-horizontal">';
	wolmart_get_template_part( 'sidebar', null, array( 'position' => 'top' ) );
}

$id_suffix = wp_unique_id();
?>
<div class="sticky-toolbox sticky-content fix-top toolbox toolbox-top">
	<div class="toolbox-left">
		<?php
		$toggle_class = $ts ? 'top' : ( $ls ? ( is_rtl() ? 'right' : 'left' ) : '' );
		if ( $toggle_class ) :
			$toggle_class .= '-sidebar-toggle';
			if ( $ts || $ls && ( empty( $wolmart_layout['left_sidebar_type'] ) || 'offcanvas' != $wolmart_layout['left_sidebar_type'] ) ) {
				$toggle_class .= ' d-lg-none';
			}
			?>
			<a href="#" class="toolbox-item toolbox-toggle <?php echo esc_attr( $toggle_class ); ?> btn btn-sm btn-outline btn-primary btn-icon-left"><i class="w-icon-category"></i><span class="d-none d-sm-block"><?php esc_html_e( 'Filters', 'wolmart' ); ?></span></a>
		<?php endif; ?>

		<form class="woocommerce-ordering toolbox-item toolbox-sort select-box" method="get">
			<?php if ( ! $ts ) : ?>
			<label for="woocommerce-orderby-<?php echo esc_attr( $id_suffix ); ?>"><?php esc_html_e( 'Sort By :', 'wolmart' ); ?></label>
			<?php endif; ?>
			<select
				name="orderby"
				class="orderby form-control"
			<?php if ( ! $ts ) : ?>
				id="woocommerce-orderby-<?php echo esc_attr( $id_suffix ); ?>"
			<?php else : ?>
				aria-label="<?php esc_attr_e( 'Shop order', 'woocommerce' ); ?>"
			<?php endif; ?>
			>
				<?php foreach ( $catalog_orderby_options as $id => $name ) : ?>
					<option value="<?php echo esc_attr( $id ); ?>" <?php selected( $orderby, $id ); ?>><?php echo esc_html( $name ); ?></option>
				<?php endforeach; ?>
			</select>
			<input type="hidden" name="paged" value="1" />
			<?php wc_query_string_form_fields( null, array( 'orderby', 'submit', 'paged', 'product-page' ) ); ?>
		</form>
	</div>

	<div class="toolbox-right">
		<?php
		wolmart_wc_count_per_page();
		wolmart_wc_shop_show_type();
		?>
	</div>
</div>
<?php
if ( $ts ) {
	echo '</div>';
}
?>
<?php if ( $ts ) : ?>
	<div class="select-items">
		<a href="<?php echo esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ); ?>" class="filter-clean text-primary"><?php esc_html_e( 'Clean All', 'wolmart' ); ?></a>
	</div>
	<?php
endif;

// If shop page's loadmore type is button, do not show pagination.
if ( ! empty( $wolmart_layout['loadmore_type'] ) && 'page' != $wolmart_layout['loadmore_type'] ) {
	remove_action( 'woocommerce_after_shop_loop', 'woocommerce_pagination' );
}
