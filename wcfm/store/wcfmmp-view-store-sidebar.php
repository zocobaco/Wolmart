<?php
/**
 * The Template for displaying store sidebar.
 *
 * @package WCfM Markeplace Views Store Sidebar
 *
 * For edit coping this to yourtheme/wcfm/store
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $WCFM, $WCFMmp;

if ( ! $WCFMmp->wcfmmp_vendor->is_store_sidebar() ) {
	return;
}

$widget_args = apply_filters(
	'wcfmmp_store_sidebar_args',
	array(
		'before_widget' => '<aside class="widget widget-collapsible">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title"> <span class="wt-area">',
		'after_title'   => '</span></h3>',
	)
);

$store_sidebar_pos = isset( $WCFMmp->wcfmmp_marketplace_options['store_sidebar_pos'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_sidebar_pos'] : 'left';
$sidebar_class     = 'left' == $store_sidebar_pos ? ' left-sidebar' : ' right-sidebar';

?>

<div class="widget-area sidebar sidebar-fixed sidebar-side shop-sidebar sticky-sidebar-wrapper <?php echo esc_attr( $sidebar_class ); ?>">

	<div class="sidebar-overlay"></div>
	<a class="sidebar-close" href="#" aria-label="<?php esc_attr_e( 'Close', 'wolmart' ); ?>" role="button"><i class="close-icon"></i></a>
	<a href="#" class="sidebar-toggle" aria-label="<?php esc_attr_e( 'Sidebar Toggle', 'wolmart' ); ?>" role="button"><i class="w-icon-angle-right"></i></a>

	<div class="sidebar-content">
		<div class="sticky-sidebar">
			<?php do_action( 'wcfmmp_store_before_sidabar', $store_user->get_id() ); ?>

			<?php if ( ! dynamic_sidebar( 'sidebar-wcfmmp-store' ) ) { ?>

				<?php the_widget( 'WCFMmp_Store_Product_Search', array( 'title' => __( 'Search', 'wc-multivendor-marketplace' ) ), $widget_args ); ?>

				<?php the_widget( 'WCFMmp_Store_Category', array( 'title' => __( 'Categories', 'wc-multivendor-marketplace' ) ), $widget_args ); ?>

				<?php the_widget( 'WCFMmp_Store_Location', array( 'title' => __( 'Store Location', 'wc-multivendor-marketplace' ) ), $widget_args ); ?>

			<?php } else { ?>
				<?php //get_sidebar( 'store' ); ?>
			<?php } ?>

			<?php do_action( 'wcfmmp_store_after_sidebar', $store_user->get_id() ); ?>
		</div>
	</div>
</div><!-- .left_sidebar -->
