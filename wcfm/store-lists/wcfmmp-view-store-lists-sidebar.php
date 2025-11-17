<?php
/**
 * The Template for displaying store sidebar.
 *
 * @package WCfM Markeplace Views Store Lists Sidebar
 *
 * For edit coping this to yourtheme/wcfm/store-lists
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCFM, $WCFMmp;

if ( ! $WCFMmp->wcfmmp_vendor->is_store_lists_sidebar() ) {
	return;
}

// enqueue sticky library to make sidebar sticky
wp_enqueue_script( 'wolmart-sticky-lib' );

$widget_args = apply_filters(
	'wcfmmp_store_lists_sidebar_args',
	array(
		'before_widget' => '<aside class="widget">',
		'after_widget'  => '</aside>',
		'before_title'  => '<div class="sidebar_heading"><h4 class="widget-title">',
		'after_title'   => '</h4></div>',
	)
);

$store_sidebar_pos = isset( $WCFMmp->wcfmmp_marketplace_options['store_sidebar_pos'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_sidebar_pos'] : 'left';
$sidebar_class     = 'left' == $store_sidebar_pos ? ' left-sidebar' : ' right-sidebar';
?>

<div id="wcfmmp-store-lists-sidebar" class="widget-area sidebar sidebar-fixed sticky-sidebar-wrapper<?php echo esc_attr( $sidebar_class ); ?>">
	<div class="sidebar-overlay"></div>
	<a class="sidebar-close" href="#" aria-label="<?php esc_attr_e( 'Close', 'wolmart' ); ?>" role="button"><i class="close-icon"></i></a>
	<a href="#" class="sidebar-toggle" aria-label="<?php esc_attr_e( 'Sidebar Toggle', 'wolmart' ); ?>" role="button"><i class="w-icon-angle-right"></i></a>
	<div class="sidebar-content">
		<div class="sticky-sidebar">
			<form role="search" method="get" class="wcfmmp-store-search-form">

				<?php do_action( 'wcfmmp_store_lists_before_sidabar' ); ?>

				<?php if ( ! dynamic_sidebar( 'sidebar-wcfmmp-store-lists' ) ) { ?>

					<?php the_widget( 'WCFMmp_Store_Lists_Search', array( 'title' => __( 'Search', 'wc-multivendor-marketplace' ) ), $widget_args ); ?>

					<?php the_widget( 'WCFMmp_Store_Lists_Category_Filter', array( 'title' => __( 'Filter by Category', 'wc-multivendor-marketplace' ) ), $widget_args ); ?>

					<?php
					if ( $radius ) {
						the_widget( 'WCFMmp_Store_Lists_Radius_Filter', array( 'title' => __( 'Filter by Location', 'wc-multivendor-marketplace' ) ), $widget_args );
					} else {
						the_widget( 'WCFMmp_Store_Lists_Location_Filter', array( 'title' => __( 'Filter by Location', 'wc-multivendor-marketplace' ) ), $widget_args );
					}
					?>

				<?php } else { ?>
					<?php //get_sidebar( 'store' ); ?>
				<?php } ?>

				<?php do_action( 'wcfmmp_store_lists_after_sidebar' ); ?>

				<input type="hidden" id="pagination_base" name="pagination_base" value="<?php echo esc_attr( $pagination_base ); ?>" />
				<input type="hidden" id="wcfm_paged" name="wcfm_paged" value="<?php echo esc_attr( $paged ); ?>" />
				<input type="hidden" id="nonce" name="nonce" value="<?php echo wp_create_nonce( 'wcfmmp-stores-list-search' ); ?>" />
				<div class="wcfmmp-overlay" style="display: none;"><span class="wcfmmp-ajax-loader"></span></div>
			</form>
		</div>
	</div>
</div><!-- .left_sidebar -->
