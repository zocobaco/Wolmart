<?php
/**
 * Sidebar template
 *
 * @var $position           Sidebar position of current page.
 * @global $wolmart_layout     Layout options for current page.
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

defined( 'ABSPATH' ) || die;

wp_enqueue_script( 'wolmart-sticky-lib' );

global $wolmart_layout;

$sidebar_class     = array( 'sidebar' );
$layout_name       = wolmart_get_page_layout();
$sidebar           = $wolmart_layout[ $position . '_sidebar' ];
$is_active_sidebar = is_active_sidebar( $sidebar );
$is_render_sidebar = false;
$sidebar_widgets   = get_option( 'sidebars_widgets' );

if ( apply_filters( 'wolmart_is_vendor_store', false ) ) {
	$is_render_sidebar = apply_filters( 'wolmart_vendor_store_sidebar_has_content', $sidebar_widgets );
} else {
	foreach ( $sidebar_widgets as $area => $widgets ) {
		if ( $sidebar == $area && is_array( $widgets ) && count( $widgets ) > 0 ) {
			$is_render_sidebar = true;
		}
	}
}

if ( ! $is_render_sidebar ) {
	return;
}

$toggle_class = 'sidebar-toggle';

if ( 'top' == $position ) { // Horizontal filter widgets in Shop page
	if ( 'archive_product' == $layout_name ) {
		$sidebar_class[] = 'top-sidebar';
		$sidebar_class[] = 'sidebar-fixed';
		$sidebar_class[] = 'shop-sidebar';
		$sidebar_class[] = 'horizontal-sidebar';
	}
} else { // Left & Right sidebar
	if ( 'offcanvas' == $wolmart_layout[ $position . '_sidebar_type' ] ) { // Off-Canvas Type
		$sidebar_class[] = 'sidebar-offcanvas';
		if ( 'left' == $position && wolmart_is_shop() ) {
			$toggle_class .= ' d-lg-none';
		}
	} else { // Classic Type
		$sidebar_class[] = 'sidebar-fixed';
	}

	$sidebar_class[] = 'sidebar-side';
	$sidebar_class[] = $position . '-sidebar';

	if ( 'archive_product' == $layout_name ) {
		$sidebar_class[] = 'shop-sidebar';
	}
}
?>

<aside class="<?php echo esc_attr( implode( ' ', apply_filters( 'wolmart_sidebar_classes', $sidebar_class ) ) ); ?>" id="<?php echo esc_attr( $sidebar ); ?>">

	<div class="sidebar-overlay"></div>
	<a class="sidebar-close" href="#" aria-label="<?php esc_attr_e( 'Close', 'wolmart' ); ?>" role="button"><i class="close-icon"></i></a>

	<?php if ( 'top' == $position && 'archive_product' == $layout_name ) : ?>

		<div class="sidebar-content toolbox-left">
			<?php
			if ( $is_active_sidebar ) {
				dynamic_sidebar( $sidebar );
			}
			?>
		</div>

	<?php else : ?>

		<?php
		// Display arrow toggle except only left sidebar without horizontal filter widgets in shop page.
		if ( ! wolmart_is_shop() || 'right' == $position || (
			! empty( $wolmart_layout['top_sidebar'] ) && 'hide' != $wolmart_layout['top_sidebar'] && is_active_sidebar( $wolmart_layout['top_sidebar'] )
			) ) {
			echo '<a href="#" class="' . esc_attr( $toggle_class ) . '" aria-label="' . esc_html__( 'Sidebar Toggle', 'wolmart' ) . '" role="button"><i class="w-icon-chevron-' . esc_attr( 'left' == $position ? 'right' : 'left' ) . '"></i></a>';
		}
		?>

		<div class="sidebar-content">
			<?php do_action( 'wolmart_sidebar_content_start' ); ?>

			<div class="sticky-sidebar">
				<?php
				if ( $is_active_sidebar ) {
					dynamic_sidebar( $sidebar );
				}
				?>
			</div>

			<?php do_action( 'wolmart_sidebar_content_end' ); ?>

		</div>

	<?php endif; ?>
</aside>
