<?php
/**
 * Framework
 *
 * 1. Define Constants
 * 2. Load the theme base
 * 3. Analyse the current request
 * 4. Load the plugin functions
 * 5. Load addons
 * 6. Load admin
 *
 * @package Wolmart WordPress Framework
 * @version 1.0
 */
defined( 'ABSPATH' ) || die;

/**
 * 1. Define Constants
 */
define( 'WOLMART_FRAMEWORK', WOLMART_PATH . '/framework' );
define( 'WOLMART_FRAMEWORK_URI', WOLMART_URI . '/framework' );
define( 'WOLMART_PLUGINS', WOLMART_FRAMEWORK . '/plugins' );
define( 'WOLMART_PLUGINS_URI', WOLMART_FRAMEWORK_URI . '/plugins' );
define( 'WOLMART_ADDONS', WOLMART_FRAMEWORK . '/addons' );
define( 'WOLMART_ADDONS_URI', WOLMART_FRAMEWORK_URI . '/addons' );

/**
 * 2. Load the theme base
 */
if ( ! defined( 'WOLMART_CORE_VERSION' ) ) {
	wolmart_require_once( '/class-base.php' );
}
wolmart_require_once( '/class-theme.php' );
wolmart_require_once( '/class-theme-assets.php' );
wolmart_require_once( '/class-backwards.php' );
if ( ! defined( 'WOLMART_CORE_VERSION' ) ) {
	wolmart_require_once( '/core-functions.php' );
}
wolmart_require_once( '/theme-functions.php' );
wolmart_require_once( '/theme-actions.php' );
wolmart_require_once( '/addons/init.php' );

/**
 * 3. Analyse the current request
 */
$request = array(
	'doing_ajax'        => wolmart_doing_ajax(),
	'customize_preview' => is_customize_preview(),
	'can_manage'        => current_user_can( 'manage_options' ),
	'is_admin'          => is_admin(),
	'is_preview'        => function_exists( 'wolmart_is_elementor_preview' ) && wolmart_is_elementor_preview() ||
							function_exists( 'wolmart_is_wpb_preview' ) && wolmart_is_wpb_preview(),
	'product_edit_page' => ( 'post-new.php' == $GLOBALS['pagenow'] && isset( $_GET['post_type'] ) && 'product' == $_GET['post_type'] ) ||
							( 'post.php' == $GLOBALS['pagenow'] && isset( $_GET['post'] ) && 'product' == get_post_type( $_GET['post'] ) ),
);


/**
 * Fires after framework init
 *
 * @since 1.2
 */
do_action( 'wolmart_after_framework_init', $request );

/**
 * 4. Load the plugin functions
 */

// WooCommerce Functions
if ( class_exists( 'WooCommerce' ) ) {
	wolmart_require_once( '/plugins/woocommerce/woocommerce.php' );
}

// Elementor Functions
if ( defined( 'ELEMENTOR_VERSION' ) ) {
	wolmart_require_once( '/plugins/elementor/elementor.php' );
}

// WPBakery Functions
if ( defined( 'WPB_VC_VERSION' ) ) {
	wolmart_require_once( '/plugins/wpb/wpb.php' );
}

// Gutenberg Functions
wolmart_require_once( '/plugins/gutenberg/gutenberg.php' );

// Multi-Vendor Functions
if ( class_exists( 'WeDevs_Dokan' ) || class_exists( 'WCFM' ) || class_exists( 'WCMp' ) || class_exists( 'WC_Vendors' ) || defined( 'MVX_PLUGIN_VERSION' ) ) {
	define( 'WOLMART_VENDORS', WOLMART_PLUGINS );
}

// Dokan Functions
if ( class_exists( 'WeDevs_Dokan' ) ) {
	wolmart_require_once( '/plugins/dokan/dokan.php' );
}

// WCFM Functions
if ( class_exists( 'WCFM' ) && class_exists( 'WCFMmp' ) ) {
	wolmart_require_once( '/plugins/wcfm/wcfm.php' );
}


// MultiVendorX Functions
if ( defined( 'MVX_PLUGIN_VERSION' ) ) {
	wolmart_require_once( '/plugins/mvx/mvx.php' );
} elseif ( class_exists( 'WCMp' ) ) {
	// WCMP Functions
	wolmart_require_once( '/plugins/wcmp/wcmp.php' );
}


// WCVendors Functions
if ( class_exists( 'WC_Vendors' ) ) {
	wolmart_require_once( '/plugins/wc-vendors/wc-vendors.php' );
}

// Woof Functions
if ( class_exists( 'WOOF' ) ) {
	wolmart_require_once( '/plugins/woof/woof.php' );
}

// WPForms Lite Functions
if ( class_exists( 'WPForms' ) ) {
	wolmart_require_once( '/plugins/wpforms/wpforms.php' );
}

// WPML Functions
if ( defined( 'WCML_VERSION' ) || defined( 'ICL_SITEPRESS_VERSION' ) ) {
	wolmart_require_once( '/plugins/wpml/wpml.php' );
}

// Yith WCWL Functions
if ( defined( 'YITH_WCWL' ) ) {
	wolmart_require_once( '/plugins/yith/wcwl.php' );
}


do_action( 'wolmart_framework_plugins', $request );

/**
 * 5. Load addons
 */
do_action( 'wolmart_framework_addons', $request );


/**
 * 6. Load Admin
 */

// Merge and Critical css for Optimize
wolmart_require_once( '/admin/optimize-wizard/optimize-stylesheets.php' );

if ( $request['can_manage'] ) {

	// Define Constants
	define( 'WOLMART_ADMIN', WOLMART_FRAMEWORK . '/admin' );
	define( 'WOLMART_ADMIN_URI', WOLMART_FRAMEWORK_URI . '/admin' ); // Template plugins directory uri

	global $pagenow;

	// Load Admin Functions
	if ( 'admin.php' == $pagenow || 'admin-ajax.php' == $pagenow || $request['is_admin'] ) {
		wolmart_require_once( '/admin/plugins/plugins.php' );                    // Load admin plugins
	}
	wolmart_require_once( '/admin/admin/admin.php' );                        // Load admin
	wolmart_require_once( '/admin/panel/panel.php' );                        // Load admin panel
	wolmart_require_once( '/admin/setup-wizard/setup-wizard.php' );          // Load admin setup wizard
	wolmart_require_once( '/admin/optimize-wizard/optimize-wizard.php' );    // Load admin optimize wizard
	wolmart_require_once( '/admin/tools/tools.php' );                        // Load admin tools
	wolmart_require_once( '/admin/version-control/version-control.php' );    // Load admin version control
	wolmart_require_once( '/admin/patcher/patcher.php' );    // Load admin patcher control

	if ( $request['customize_preview'] ) {                                       // Load admin customizer
		wolmart_require_once( '/admin/customizer/customizer.php' );
		wolmart_require_once( '/admin/customizer/customizer-function.php' );
	}

	do_action( 'wolmart_framework_admin', $request );
}
