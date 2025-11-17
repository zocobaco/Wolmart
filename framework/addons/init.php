<?php
/**
 * Framework Addons
 *
 * 1. Load addons
 * 2. Addons List
 *
 * @package Wolmart WordPress Framework
 * @version 1.0
 */


/**************************************/
/* 1. Load addons                     */
/**************************************/

add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_layout_builder' );
add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_walker' );
add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_skeleton_screen' );
add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_lazyload_image' );
add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_lazyload_menu' );
add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_live_search' );
add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_studio' );
add_action( 'wolmart_framework_addons', 'wolmart_setup_comments_pagination' );
add_action( 'wolmart_framework_addons', 'wolmart_setup_free_shipping_bar' );
if ( class_exists( 'WooCommerce' ) ) {
	add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_product_image_swatch' );
	add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_product_custom_tabs' );
	add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_product_frequently_bought_together' );
	add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_product_catalog' );
	add_action( 'wolmart_framework_addons', 'wolmart_setup_addon_product_buy_now' );
}


/**************************************/
/* 2. Addons List                     */
/**************************************/

// ADDON: Layout Builder
if ( ! function_exists( 'wolmart_setup_addon_layout_builder' ) ) {
	function wolmart_setup_addon_layout_builder( $request ) {
		wolmart_require_once( '/addons/layout-builder/layout-builder.php' );
		if ( $request['can_manage'] && $request['is_admin'] &&
			( 'admin.php' == $GLOBALS['pagenow'] && isset( $_GET['page'] ) && 'wolmart-layout-builder' == $_GET['page'] ) || wp_doing_ajax() ) {
			wolmart_require_once( '/addons/layout-builder/layout-builder-admin.php' );
		}
	}
}

// ADDON: Menu Walker
if ( ! function_exists( 'wolmart_setup_addon_walker' ) ) {
	function wolmart_setup_addon_walker( $request ) {
		if ( 'nav-menus.php' == $GLOBALS['pagenow'] || $request['customize_preview'] || $request['doing_ajax'] ) {
			wolmart_require_once( '/addons/walker/walker.php' );
		}
		wolmart_require_once( '/addons/walker/walker-nav-menu.php' );
	}
}

// ADDON: Skeleton Screen
if ( ! function_exists( 'wolmart_setup_addon_skeleton_screen' ) ) {
	function wolmart_setup_addon_skeleton_screen( $request ) {
		if ( ! $request['doing_ajax'] && ! $request['customize_preview'] && ! $request['is_preview'] && wolmart_get_option( 'skeleton_screen' ) && ! isset( $_REQUEST['only_posts'] ) ) {
			wolmart_require_once( '/addons/skeleton/skeleton.php' );
		}
	}
}

// ADDON: Image Lazyload
if ( ! function_exists( 'wolmart_setup_addon_lazyload_image' ) ) {
	function wolmart_setup_addon_lazyload_image( $request ) {
		add_filter( 'wp_lazy_loading_enabled', 'wolmart_disable_wp_lazyload_img', 10, 2 );
		function wolmart_disable_wp_lazyload_img( $default, $tag_name ) {
			return 'img' == $tag_name ? false : $default;
		}

		if ( ! $request['is_admin'] && ! $request['customize_preview'] && ! $request['doing_ajax'] && wolmart_get_option( 'lazyload' ) ) {
			wolmart_require_once( '/addons/lazyload-images/lazyload.php' );
		}
	}
}

// ADDON: Menu Lazyload
if ( ! function_exists( 'wolmart_setup_addon_lazyload_menu' ) ) {
	function wolmart_setup_addon_lazyload_menu( $request ) {
		if ( $request['is_admin'] ) {
			if ( $request['customize_preview'] ) {
				add_action( 'customize_save_after', 'wolmart_lazyload_menu_update' );
			}
			if ( 'post.php' == $GLOBALS['pagenow'] ) {
				add_action( 'save_post', 'wolmart_lazyload_menu_update' );
			}
			add_action( 'wp_update_nav_menu_item', 'wolmart_lazyload_menu_update', 10, 3 );

			if ( ! function_exists( 'wolmart_lazyload_menu_update' ) ) {
				function wolmart_lazyload_menu_update() {
					set_theme_mod( 'menu_last_time', time() + ( get_option( 'gmt_offset' ) * HOUR_IN_SECONDS ) );
				}
			}
		}
	}
}

// ADDON: Live Search
if ( ! function_exists( 'wolmart_setup_addon_live_search' ) ) {
	function wolmart_setup_addon_live_search( $request ) {
		if ( ( ! $request['is_admin'] || $request['doing_ajax'] ) && wolmart_get_option( 'live_search' ) ) {
			wolmart_require_once( '/addons/live-search/live-search.php' );
		}
	}
}

// Product Image Swatch
if ( ! function_exists( 'wolmart_setup_addon_product_image_swatch' ) ) {
	function wolmart_setup_addon_product_image_swatch( $request ) {
		if ( wolmart_get_option( 'image_swatch' ) ) {
			if ( $request['is_admin'] && ( $request['doing_ajax'] || $request['product_edit_page'] ||
				! empty( $_POST['action'] ) && 'editpost' == $_POST['action'] ) ) {
				wolmart_require_once( '/addons/product-image-swatch/product-image-swatch-admin.php' );
			}
			wolmart_require_once( '/addons/product-image-swatch/product-image-swatch.php' );
		}
	}
}

// Product Custom Tabs
if ( ! function_exists( 'wolmart_setup_addon_product_custom_tabs' ) ) {
	function wolmart_setup_addon_product_custom_tabs( $request ) {
		if ( $request['is_admin'] && ( $request['doing_ajax'] || $request['product_edit_page'] ) ) {
			wolmart_require_once( '/addons/product-custom-tab/product-custom-tab-admin.php' );
			wolmart_require_once( '/addons/product-data-addons/product-data-addons-admin.php' );
		}
	}
}

// Frequently Bought Together
if ( ! function_exists( 'wolmart_setup_addon_product_frequently_bought_together' ) ) {
	function wolmart_setup_addon_product_frequently_bought_together( $request ) {
		if ( wolmart_get_option( 'product_fbt' ) ) {
			if ( $request['is_admin'] && ( $request['doing_ajax'] || $request['product_edit_page'] ||
				! empty( $_POST['action'] ) && 'editpost' == $_POST['action'] ) ) {
				wolmart_require_once( '/addons/product-frequently-bought-together/product-frequently-bought-together-admin.php' );
			}
			wolmart_require_once( '/addons/product-frequently-bought-together/product-frequently-bought-together.php' );
		}
	}
}

// ADDON: Product Catalog
if ( ! function_exists( 'wolmart_setup_addon_product_catalog' ) ) {
	function wolmart_setup_addon_product_catalog( $request ) {
		wolmart_require_once( '/addons/product-catalog/product-catalog.php' );
	}
}

// ADDON: Product Buy Now
if ( ! function_exists( 'wolmart_setup_addon_product_buy_now' ) ) {
	function wolmart_setup_addon_product_buy_now( $requrest ) {
		wolmart_require_once( '/addons/product-buy-now/product-buy-now.php' );
	}
}

// ADDON: Studio
if ( ! function_exists( 'wolmart_setup_addon_studio' ) ) {
	function wolmart_setup_addon_studio( $request ) {
		if ( defined( 'WOLMART_CORE_VERSION' ) && ( current_user_can( 'edit_posts' ) || current_user_can( 'edit_pages' ) ) &&
				( $request['doing_ajax'] || $request['is_preview'] || 'edit.php' == $GLOBALS['pagenow'] && isset( $_REQUEST['post_type'] ) && 'wolmart_template' == $_REQUEST['post_type'] ) ) {

			wolmart_require_once( '/addons/studio/studio.php' );
		}
	}
}

// ADDON: Comments Pagination
if ( ! function_exists( 'wolmart_setup_comments_pagination' ) ) {
	function wolmart_setup_comments_pagination() {
		wolmart_require_once( '/addons/comments-pagination/comments-pagination.php' );
	}
}

// ADDON: Free Shipping Bar
if ( ! function_exists( 'wolmart_setup_free_shipping_bar' ) ) {
	function wolmart_setup_free_shipping_bar() {
		wolmart_require_once( '/addons/free-shipping-bar/free-shipping-bar.php' );
	}
}