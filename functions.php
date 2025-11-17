<?php
/**
 * Theme Functions
 *
 * To use a child theme:
 *   see http://codex.wordpress.org/Theme_Development
 *   see http://codex.wordpress.org/Child_Themes
 *
 * To override certain functions (wrapped in a function_exists call):
 *   define them in child theme's functions.php file.
 *
 * For more information on hooks, actions, and filters:
 *   see http://codex.wordpress.org/Plugin_API
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

// Direct load is not allowed
defined( 'ABSPATH' ) || die;

// Theme Version
define( 'WOLMART_VERSION', ( is_child_theme() ? wp_get_theme( wp_get_theme()->template ) : wp_get_theme() )->version );

// Define Constants
define( 'WOLMART_PATH', get_parent_theme_file_path() );     // Template directory path
define( 'WOLMART_URI', get_parent_theme_file_uri() );       // Template directory uri
define( 'WOLMART_ASSETS', WOLMART_URI . '/assets' );        // Template assets directory uri
define( 'WOLMART_CSS', WOLMART_ASSETS . '/css' );           // Template css uri

define( 'WOLMART_JS', WOLMART_ASSETS . '/js' );             // Template javascript uri
define( 'WOLMART_PART', 'templates' );                      // Template parts

define( 'WOLMART_WC_103_PREFIX', defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '10.3.0', '>=' ) ? 'wc-' : '' );

function wolmart_require_once( $path ) {
	require_once file_exists( WOLMART_PATH . '/inc' . $path ) ? WOLMART_PATH . '/inc' . $path : WOLMART_PATH . '/framework' . $path;
}
function wolmart_path( $path ) {
	return file_exists( WOLMART_PATH . '/inc' . $path ) ? WOLMART_PATH . '/inc' . $path : WOLMART_PATH . '/framework' . $path;
}

wolmart_require_once( '/init.php' );
