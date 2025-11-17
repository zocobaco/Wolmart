<?php
/**
 * Wolmart Theme Assets Class
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

class Wolmart_Theme_Assets extends Wolmart_Base {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Initialize
	 *
	 * @since 1.0.0
	 * @access public
	 */
	public function init() {

		/**
		 * Manage Theme and Plugin Assets
		 */
		if ( ! is_admin() ) {
			// Remove WooCommerce Style
			add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );
		}

		add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts' ), 1 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 25 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 30 );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ), 5 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_css' ), 999 );

		// Unnecessary scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'dequeue_unnecessary_scripts' ), 99 );
		if ( defined( 'YITH_WCWL' ) ) {
			add_filter( 'yith_wcwl_main_script_deps', array( $this, 'remove_yith_wcwl_selectbox' ) );
		}

		if ( ! is_admin() ) {
			// Custom JS
			add_action( 'wp_print_footer_scripts', array( $this, 'enqueue_custom_js' ), 20 );
			// Load Google Font in Footer
			add_action( 'wp_footer', 'wolmart_load_google_font' );
		}
	}

	/**
	 * Register styles and scripts.
	 *
	 * @since 1.0
	 */
	public function register_scripts() {
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';

		// Styles
		wp_register_style( 'wolmart-style', WOLMART_URI . '/style.css', array(), WOLMART_VERSION );
		wp_register_style( 'wolmart-icons', WOLMART_ASSETS . '/vendor/wolmart-icons/css/icons.min.css', array(), WOLMART_VERSION );
		wp_register_style( 'wolmart-flag', WOLMART_CSS . '/flags.min.css', array(), WOLMART_VERSION );
		wp_register_style( 'fontawesome-free', WOLMART_ASSETS . '/vendor/fontawesome-free/css/all.min.css', array(), '5.14.0' );
		wp_register_style( 'wolmart-animation', WOLMART_CSS . '/components/animations/animate.min.css' );
		wp_register_style( 'magnific-popup', WOLMART_ASSETS . '/vendor/jquery.magnific-popup/magnific-popup' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), '1.0' );

		// Theme Styles
		$css_files  = array( 'theme', 'blog', 'single-post', 'shop', 'shop-other', 'single-product' );
		$uploads    = wp_upload_dir();
		$upload_dir = $uploads['basedir'];
		$upload_url = $uploads['baseurl'];
		foreach ( $css_files as $file ) {
			$filename = 'theme' . ( 'theme' == $file ? '' : '-' . $file );
			if ( file_exists( wp_normalize_path( $upload_dir . '/wolmart_styles/' . $filename . ( is_rtl() ? '-rtl' : '' ) . '.min.css' ) ) ) {
				wp_register_style( 'wolmart-' . $filename, $upload_url . '/wolmart_styles/' . $filename . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), WOLMART_VERSION );
			} else {
				wp_register_style( 'wolmart-' . $filename, WOLMART_CSS . '/' . ( 'theme' == $file ? '' : 'pages/' ) . $file . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), WOLMART_VERSION );
			}
		}

		if ( file_exists( wp_normalize_path( $upload_dir . '/wolmart_styles/dynamic_css_vars.css' ) ) ) {
			$dynamic_url = $upload_url . '/wolmart_styles/dynamic_css_vars.css';
		} else {
			$dynamic_url = WOLMART_CSS . '/dynamic_css_vars.css';
		}

		/**
		 * Mobile Floating Style
		 *
		 * @since 1.6.0
		 */
		if ( 'floating' == wolmart_get_option( 'mobile_bar_type' ) ) {
			wp_register_style( 'mobile-floating', WOLMART_ASSETS . '/vendor/mobile-floating/mobile-floating.css', array(), WOLMART_VERSION, '(max-width: 991px)' );
		}

		// global css
		$custom_css_handle = 'wolmart-theme';

		if ( ! is_customize_preview() ) {
			wp_register_style( 'wolmart-dynamic-vars', $dynamic_url, array( $custom_css_handle ), WOLMART_VERSION );
		} else {

			global $wp_filesystem;

			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}
			$dynamic_url = str_replace( 'https:', 'http:', $dynamic_url );
			$data        = $wp_filesystem->get_contents( $dynamic_url );
			wp_add_inline_style( $custom_css_handle, $data );
		}

		// Scripts
		wp_register_script( 'wolmart-sticky-lib', WOLMART_JS . '/sticky' . $suffix, array( 'jquery-core' ), WOLMART_VERSION, true );
		wp_register_script( 'wolmart-theme', WOLMART_JS . '/theme' . $suffix, array( 'jquery-core' ), WOLMART_VERSION, true );
		wp_register_script( 'wolmart-theme-async', WOLMART_JS . '/theme-async' . $suffix, array( 'wolmart-theme' ), WOLMART_VERSION, true );
		wp_register_script( 'isotope-pkgd', WOLMART_ASSETS . '/vendor/isotope/isotope.pkgd' . $suffix, array( 'jquery-core', 'imagesloaded' ), '3.0.6', true );
		wp_register_script( WOLMART_WC_103_PREFIX . 'jquery-cookie', WOLMART_ASSETS . '/vendor/jquery.cookie/jquery.cookie' . $suffix, array(), '1.4.1', true );
		wp_register_script( 'jquery-count-to', WOLMART_ASSETS . '/vendor/jquery.count-to/jquery.count-to' . $suffix, array( 'jquery-core' ), false, true );
		wp_register_script( 'jquery-countdown', WOLMART_ASSETS . '/vendor/jquery.countdown/jquery.countdown.min.js', array( 'jquery-core' ), false, true );
		wp_register_script( 'jquery-fitvids', WOLMART_ASSETS . '/vendor/jquery.fitvids/jquery.fitvids.min.js', array( 'jquery-core' ), false, true );
		wp_register_script( 'jquery-magnific-popup', WOLMART_ASSETS . '/vendor/jquery.magnific-popup/jquery.magnific-popup' . $suffix, array( 'jquery-core', 'imagesloaded' ), '1.1.0', true );
		wp_register_script( 'jquery-parallax', WOLMART_ASSETS . '/vendor/parallax/parallax.min.js', array( 'jquery-core' ), false, true );
		wp_register_script( 'three-sixty', WOLMART_ASSETS . '/vendor/threesixty/threesixty.min.js', array( 'jquery-core' ), false, true );
		wp_register_script( 'popper', WOLMART_ASSETS . '/vendor/bootstrap/popper.min.js', array( 'jquery-core' ), '4.1.3', true );
		wp_register_script( 'bootstrap-tooltip', WOLMART_ASSETS . '/vendor/bootstrap/bootstrap.tooltip' . $suffix, array( 'popper' ), '4.1.3', true );

		/**
		 * Mobile Floating Scripts
		 *
		 * @since 1.6.0
		 */
		if ( 'floating' == wolmart_get_option( 'mobile_bar_type' ) ) {
			wolmart_register_defer_script( 'mobile-floating', WOLMART_ASSETS . '/vendor/mobile-floating/mobile-floating' . $suffix, array( 'jquery-core', 'wolmart-theme-async' ), WOLMART_VERSION, true );
			// Register mobile scripts only.
			wolmart_register_mobile_script( 'mobile-floating', WOLMART_ASSETS . '/vendor/mobile-floating/mobile-floating' . $suffix );
		}

		/**
		 * Elementor doesn't register swiper JS for optimization from 3.23.0
		 *
		 * @since 1.8.2
		 */
		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			$swiper_path    = ( ! Elementor\Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) && version_compare( ELEMENTOR_VERSION, '3.28', '<' ) ) ? 'assets/lib/swiper/' : 'assets/lib/swiper/v8/';
			$swiper_version = ( ! Elementor\Plugin::$instance->experiments->is_feature_active( 'e_swiper_latest' ) && version_compare( ELEMENTOR_VERSION, '3.28', '<' ) ) ? '5.3.6' : '8.4.5';
			wp_register_script( 'swiper', ELEMENTOR_URL . $swiper_path . 'swiper' . $suffix, array(), $swiper_version, true );
		} else {
			wp_register_script( 'swiper', WOLMART_ASSETS . '/vendor/swiper/swiper' . $suffix, array(), '6.7.0', true );
		}
	}

	/**
	 * Enqueue styles and scripts for admin.
	 *
	 * @since 1.0
	 */
	public function enqueue_admin_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';

		wp_register_style( 'wolmart-icons', WOLMART_ASSETS . '/vendor/wolmart-icons/css/icons.min.css', array(), WOLMART_VERSION );
		wp_register_style( 'fontawesome-free', WOLMART_ASSETS . '/vendor/fontawesome-free/css/all.min.css', array(), '5.14.0' );
		wp_register_style( 'jquery-select2', WOLMART_ASSETS . '/vendor/select2/select2.css', array(), '4.0.3' );
		wp_register_style( 'magnific-popup', WOLMART_ASSETS . '/vendor/jquery.magnific-popup/magnific-popup' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), '1.0' );
		wp_register_script( 'isotope-pkgd', WOLMART_ASSETS . '/vendor/isotope/isotope.pkgd' . $suffix, array( 'jquery-core', 'imagesloaded' ), '3.0.6', true );
		wp_register_script( 'jquery-magnific-popup', WOLMART_ASSETS . '/vendor/jquery.magnific-popup/jquery.magnific-popup' . $suffix, array( 'jquery-core', 'imagesloaded' ), '1.1.0', true );
		wp_register_script( 'jquery-select2', WOLMART_ASSETS . '/vendor/select2/select2' . $suffix, array( 'jquery' ), '4.0.3', true );
		// Admin Scripts
		wp_enqueue_style( 'wolmart-icons' );
		if ( defined( 'WOLMART_ADMIN_URI' ) ) {
			wp_enqueue_style( 'wolmart-admin', WOLMART_ADMIN_URI . '/admin/admin' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), WOLMART_VERSION );
			wp_enqueue_script( 'wolmart-admin', WOLMART_ADMIN_URI . '/admin/admin' . $suffix, array( 'jquery-core' ), WOLMART_VERSION, true );
		}
		wp_enqueue_script( 'wp-color-picker' );

		// Load google font
		wolmart_load_google_font( array( 'Poppins' ) );

		wp_localize_script(
			'wolmart-admin',
			'wolmart_admin_vars',
			apply_filters(
				'wolmart_admin_vars',
				array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'nonce'    => wp_create_nonce( 'wolmart-admin' ),
				)
			)
		);
	}

	/**
	 * Enqueue frontend styles.
	 *
	 * @since 1.0
	 */
	public function enqueue_styles() {
		if ( ! apply_filters( 'wolmart_resource_disable_fontawesome', wolmart_get_option( 'resource_disable_fontawesome' ) ) ) {
			wp_enqueue_style( 'fontawesome-free' );
		}

		wp_enqueue_style( 'wolmart-icons' );
		wp_enqueue_style( 'wolmart-flag' );

		if ( ! defined( 'ELEMENTOR_VERSION' ) || version_compare( ELEMENTOR_VERSION, '3.24', '<' ) || ( function_exists( 'wolmart_is_elementor_preview' ) && wolmart_is_elementor_preview() ) ) {
			wp_enqueue_style( 'wolmart-animation' );
		}

		wp_enqueue_style( 'magnific-popup' );

		do_action( 'wolmart_before_enqueue_theme_style' );

		wp_enqueue_style( 'wolmart-dynamic-vars' );
		wp_enqueue_style( 'wolmart-theme' );

		// Theme page style
		$custom_css_handle = 'wolmart-theme';
		$layout            = wolmart_get_page_layout();
		if ( 'archive_product' === $layout ) { // Product Archive Page
			$custom_css_handle = 'wolmart-theme-shop';
		} elseif ( 'archive_post' === $layout ) { // Blog Page
			$custom_css_handle = 'wolmart-theme-blog';
		} elseif ( 'single_page' === $layout ) { // Page
			if (
				( defined( 'YITH_WCWL' ) && function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) ||
				( class_exists( 'WooCommerce' ) && ( is_cart() || is_checkout() || is_account_page() ) )
			) {
				$custom_css_handle = 'wolmart-theme-shop-other';
			}
		} elseif ( 'single_product' === $layout ) { // Single Product Page
			$custom_css_handle = 'wolmart-theme-single-product';
			wp_enqueue_script( 'photoswipe' );
		} elseif ( 'single_post' === $layout ) { // Single Post Page
			$custom_css_handle = 'wolmart-theme-single-post';
		}

		if ( 'wolmart-theme' !== $custom_css_handle ) {
			wp_enqueue_style( $custom_css_handle );
		}

		if ( function_exists( 'wolmart_is_elementor_preview' ) && wolmart_is_elementor_preview() && 'wolmart_template' === get_post_type() && 'product_layout' === get_post_meta( get_the_ID(), 'wolmart_template_type', true ) ) {
			wp_enqueue_style( 'wolmart-theme-single-product' );
		}

		// Styles for page editors (edit link tooltip)
		wp_enqueue_style( 'bootstrap-tooltip', WOLMART_ASSETS . '/vendor/bootstrap/bootstrap.tooltip.css', array(), '4.1.3' );

		/**
		 * Mobile Floating CSS
		 *
		 * @version 1.6.0
		 */
		if ( 'floating' == wolmart_get_option( 'mobile_bar_type' ) ) {
			wp_enqueue_style( 'mobile-floating' );
		}

		// Global css
		if ( ! is_customize_preview() ) {
			$custom_css = wolmart_get_option( 'custom_css' );
			if ( $custom_css ) {
				wp_add_inline_style( $custom_css_handle, '/* Global CSS */' . PHP_EOL . wp_strip_all_tags( wp_specialchars_decode( $custom_css ) ) );
			}
		}

		do_action( 'wolmart_after_enqueue_theme_style' );

		// wolmart_load_google_font();
	}

	/**
	 * Dequeue unnecessary styles and scripts.
	 *
	 * @since 1.0
	 */
	public function dequeue_unnecessary_scripts() {

		// YITH WCWL styles & scripts
		if ( defined( 'YITH_WCWL' ) ) {

			// dequeue font awesome
			wp_dequeue_style( 'yith-wcwl-font-awesome' );
			wp_deregister_style( 'yith-wcwl-font-awesome' );

			// enqueue main style again because font-awesome dequeues it.
			wp_dequeue_style( 'yith-wcwl-main' );
			wp_dequeue_style( 'yith-wcwl-font-awesome' );

			// wp_dequeue_style( 'jquery-selectBox' );
			// wp_dequeue_script( 'jquery-selectBox' );

			// checkout
			if ( function_exists( 'is_checkout' ) && is_checkout() ) {
				// wp_dequeue_style( 'selectWoo' );
				// wp_deregister_style( 'selectWoo' );
			}
		}

		// WooCommerce PrettyPhoto(deprecated), but YITH Wishlist use
		if ( class_exists( 'WooCommerce' ) ) {
			wp_dequeue_style( 'woocommerce_prettyPhoto_css' );
			wp_deregister_style( 'woocommerce_prettyPhoto_css' );
			wp_dequeue_script( 'prettyPhoto-init' );
			wp_dequeue_script( 'prettyPhoto' );
		}

		// Optimize disable
		if ( apply_filters( 'wolmart_resource_disable_gutenberg', wolmart_get_option( 'resource_disable_gutenberg' ) ) ) {
			wp_dequeue_style( 'wp-block-library' );
		}
		if ( apply_filters( 'wolmart_resource_disable_wc_blocks', wolmart_get_option( 'resource_disable_wc_blocks' ) ) ) {
			wp_dequeue_style( 'wc-block-style' );
			wp_deregister_style( 'wc-block-style' );
			wp_dequeue_style( 'wc-block-vendors-style' );
			wp_deregister_style( 'wc-block-vendors-style' );
		}

		if ( ! is_admin_bar_showing() ) {
			wp_dequeue_style( 'dashicons' );
		}
	}

	/**
	 * Dequeue jquery-selectBox script.
	 *
	 * @since 1.0
	 */
	public function remove_yith_wcwl_selectbox( $deps ) {
		foreach ( $deps as $i => $dep ) {
			if ( 'jquery-selectBox' == $dep ) {
				array_splice( $deps, $i, 1 );
			}
		}
		return $deps;
	}

	/**
	 * Enqueue custom css
	 *
	 * @since 1.0
	 */
	public function enqueue_custom_css() {

		do_action( 'wolmart_before_enqueue_custom_css' );

		// Theme Style
		wp_enqueue_style( 'wolmart-style' );

		// Enqueue Page CSS
		if ( function_exists( 'wolmart_is_elementor_preview' ) && wolmart_is_elementor_preview() ) {
			$page_css = '';

			wp_enqueue_script( 'isotope-pkgd' );
			wp_enqueue_script( 'jquery-parallax' );
			wp_enqueue_script( 'isotope-plugin' );
			wp_enqueue_script( 'jquery-countdown' );
		} else {
			$page_css = get_post_meta( intval( get_the_ID() ), 'page_css', true );
		}

		if ( $page_css ) {
			wp_add_inline_style( 'wolmart-style', '/* Page CSS */' . PHP_EOL . $page_css );
		}

		do_action( 'wolmart_after_enqueue_custom_style' );
	}

	/**
	 * Enqueue frontend scripts and localize vars.
	 *
	 * @since 1.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( 'imagesloaded' );
		wp_enqueue_script( 'wolmart-theme' );
		wp_enqueue_script( 'wolmart-theme-async' );
		wp_set_script_translations( 'wolmart-theme-async', 'wolmart' );

		$localize_vars = array(
			'ajax_url'                 => esc_js( admin_url( 'admin-ajax.php' ) ),
			'nonce'                    => wp_create_nonce( 'wolmart-nonce' ),
			'lazyload'                 => wolmart_get_option( 'lazyload' ),
			'skeleton_screen'          => wolmart_get_option( 'skeleton_screen' ),
			'container'                => wolmart_get_option( 'container' ),
			'auto_close_mobile_filter' => wolmart_get_option( 'auto_close_mobile_filter' ),
			'assets_url'               => WOLMART_ASSETS,
			'texts'                    => array(
				'loading'          => esc_html__( 'Loading...', 'wolmart' ),
				'loadmore_error'   => esc_html__( 'Loading failed', 'wolmart' ),
				'popup_error'      => esc_html__( 'The content could not be loaded.', 'wolmart' ),
				'product_zoom_btn' => esc_html__( 'Product Image Zoom', 'wolmart' ),
			),
			'resource_async_js'        => wolmart_get_option( 'resource_async_js' ),
			'resource_split_tasks'     => wolmart_get_option( 'resource_split_tasks' ),
			'resource_idle_run'        => wolmart_get_option( 'resource_idle_run' ),
			'resource_after_load'      => wolmart_get_option( 'resource_after_load' ),
			'wolmart_cache_key'        => 'wolmart_cache_' . MD5( home_url() ),
			'lazyload_menu'            => boolval( wolmart_get_option( 'lazyload_menu' ) ),
			'cart_popup_type'          => wolmart_get_option( 'cart_popup_type' ),
			'countdown'                => array(
				'labels'       => array(
					esc_html__( 'Years', 'wolmart' ),
					esc_html__( 'Months', 'wolmart' ),
					esc_html__( 'Weeks', 'wolmart' ),
					esc_html__( 'Days', 'wolmart' ),
					esc_html__( 'Hours', 'wolmart' ),
					esc_html__( 'Minutes', 'wolmart' ),
					esc_html__( 'Seconds', 'wolmart' ),
				),
				'labels_short' => array(
					esc_html__( 'Years', 'wolmart' ),
					esc_html__( 'Months', 'wolmart' ),
					esc_html__( 'Weeks', 'wolmart' ),
					esc_html__( 'Days', 'wolmart' ),
					esc_html__( 'Hrs', 'wolmart' ),
					esc_html__( 'Mins', 'wolmart' ),
					esc_html__( 'Secs', 'wolmart' ),
				),
				'label1'       => array(
					esc_html__( 'Year', 'wolmart' ),
					esc_html__( 'Month', 'wolmart' ),
					esc_html__( 'Week', 'wolmart' ),
					esc_html__( 'Day', 'wolmart' ),
					esc_html__( 'Hour', 'wolmart' ),
					esc_html__( 'Minute', 'wolmart' ),
					esc_html__( 'Second', 'wolmart' ),
				),
				'label1_short' => array(
					esc_html__( 'Year', 'wolmart' ),
					esc_html__( 'Month', 'wolmart' ),
					esc_html__( 'Week', 'wolmart' ),
					esc_html__( 'Day', 'wolmart' ),
					esc_html__( 'Hour', 'wolmart' ),
					esc_html__( 'Min', 'wolmart' ),
					esc_html__( 'Sec', 'wolmart' ),
				),
			),
		);

		// Scripts for page editors (edit link tooltip)
		if ( current_user_can( 'edit_pages' ) ) {
			wp_enqueue_script( 'popper' );
			wp_enqueue_script( 'bootstrap-tooltip' );
		}

		if ( wp_is_mobile() && 'floating' == wolmart_get_option( 'mobile_bar_type' ) ) {
			wp_enqueue_script( 'mobile-floating' );
		}

		if ( 'archive_post' == wolmart_get_page_layout() ) {
			$localize_vars['posts_per_page'] = get_option( 'posts_per_page' );
		}

		if ( wolmart_get_option( 'lazyload_menu' ) ) {
			$localize_vars['menu_last_time'] = wolmart_get_option( 'menu_last_time' );
		}
		if ( wolmart_get_option( 'blog_ajax' ) ) {
			$localize_vars['blog_ajax'] = 1;
		}
		if ( class_exists( 'WooCommerce' ) && wolmart_get_option( 'compare_available' ) ) {
			$localize_vars['compare_limit'] = wolmart_get_option( 'compare_limit' );
		}

		if ( wolmart_get_option( 'show_cookie_info' ) ) {
			$localize_vars['cookie_version'] = wolmart_get_option( 'cookie_version' );
		}

		if ( defined( 'ELEMENTOR_VERSION' ) ) {
			if ( class_exists( 'Elementor\Plugin' ) && Elementor\Plugin::$instance->experiments->is_feature_active( 'e_optimized_assets_loading' ) ) {
				$localize_vars['swiper_url'] = plugins_url( 'elementor/assets/lib/swiper/swiper' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ) );
			}
			if ( apply_filters( 'wolmart_resource_disable_elementor', wolmart_get_option( 'resource_disable_elementor' ) ) && ! current_user_can( 'edit_pages' ) ) {
				$localize_vars['resource_disable_elementor'] = 1;
			}
		}

		if ( class_exists( 'WooCommerce' ) ) {
			if ( wolmart_get_option( 'shop_ajax' ) && ! apply_filters( 'wolmart_is_vendor_store', false ) && wolmart_is_shop() ) {
				$localize_vars['shop_ajax'] = 1;
			}
			if ( wolmart_get_option( 'blog_ajax' ) ) {
				$localize_vars['blog_ajax'] = 1;
			}

			$localize_vars = array_merge_recursive(
				$localize_vars,
				array(
					'home_url'            => esc_js( home_url( '/' ) ),
					'shop_url'            => esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ),
					'quickview_type'      => wolmart_get_option( 'quickview_type' ),
					'quickview_thumbs'    => 'offcanvas' == wolmart_get_option( 'quickview_type' ) ? 'horizontal' : wolmart_get_option( 'quickview_thumbs' ),
					'prod_open_click_mob' => wolmart_get_option( 'prod_open_click_mob' ),
					'texts'               => array(
						/* translators: %d represents loaded products count. */
						'show_info_all'   => esc_html__( 'all %d', 'wolmart' ),
						'already_voted'   => esc_html__( 'You already voted!', 'wolmart' ),
						'view_checkout'   => esc_html__( 'Checkout', 'wolmart' ),
						'view_cart'       => esc_html__( 'View Cart', 'wolmart' ),
						'add_to_wishlist' => esc_html__( 'Add to wishlist', 'wolmart' ),
						'add_to_cart'     => esc_html__( 'Add to cart', 'wolmart' ),
						'select_options'  => esc_html__( 'Select options', 'wolmart' ),
						'cart_suffix'     => esc_html__( 'has been added to cart', 'wolmart' ),
						'select_category' => esc_js( __( 'Select a category', 'woocommerce' ) ),
						'no_matched'      => esc_js( _x( 'No matches found', 'enhanced select', 'woocommerce' ) ),
						'product_zoom'    => esc_html__( 'Product Image Zoom', 'wolmart' ),
					),
					'pages'               => array(
						'cart'     => wc_get_page_permalink( 'cart' ),
						'checkout' => wc_get_page_permalink( 'checkout' ),
					),
					'single_product'      => array(
						'zoom_enabled' => true,
						'zoom_options' => array(),
					),
					'cart_auto_update'    => wolmart_get_option( 'cart_auto_update' ),
				)
			);
		}

		/**
		 * Mobile responsive scripts & styles
		 *
		 * @since 1.6.0
		 */
		if ( ! wp_is_mobile() ) {
			// Mobile Scripts
			$mobile_scripts = apply_filters( 'wolmart_register_mobile_scripts', array() );
			$defer_handles  = apply_filters( 'wolmart_defer_scripts', array() );

			if ( ! empty( $mobile_scripts ) ) {
				foreach ( $mobile_scripts as $key => $mobile_script ) {
					if ( ! empty( $defer_handles ) && true == in_array( $mobile_script['handle'], $defer_handles ) ) {
						$mobile_scripts[ $key ]['defer'] = true;
					} else {
						$mobile_scripts[ $key ]['defer'] = false;
					}
				}
			}

			$localize_vars['mobile_scripts'] = $mobile_scripts;

			// Mobile Styles
			$mobile_styles = apply_filters( 'wolmart_register_mobile_styles', array() );

			$localize_vars['mobile_styles'] = $mobile_styles;
		} else {
			$localize_vars['mobile_scripts'] = array();
			$localize_vars['mobile_styles']  = array();
		}

		wp_localize_script( 'wolmart-theme', 'wolmart_vars', apply_filters( 'wolmart_vars', $localize_vars ) );

		if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
			wp_enqueue_script( 'comment-reply' );
		}
	}

	/**
	 * Enqueue custom js.
	 *
	 * @since 1.0
	 */
	public function enqueue_custom_js() {
		global $wolmart_layout;
		$global_js = wolmart_get_option( 'custom_js' );
		if ( $global_js ) {
			?>
			<script id="wolmart_custom_global_script">
				<?php echo wolmart_strip_script_tags( $global_js ); ?>
			</script>
			<?php
		}
		$page_js = get_post_meta( intval( get_the_ID() ), 'page_js', true );
		if ( $page_js ) {
			?>
			<script id="wolmart_custom_page_script">
				<?php echo wolmart_strip_script_tags( $page_js ); ?>
			</script>
			<?php
		}

		if ( isset( $wolmart_layout['used_blocks'] ) && $wolmart_layout['used_blocks'] ) {
			foreach ( $wolmart_layout['used_blocks'] as $block_id => $value ) {
				if ( $wolmart_layout['used_blocks'][ $block_id ]['js'] ) {
					continue;
				}
				$script = get_post_meta( $block_id, 'page_js', true );
				if ( $script ) {
					?>
				<script id="wolmart_block_<?php echo esc_attr( $block_id ); ?>_script">
					<?php echo wolmart_strip_script_tags( $script ); ?>
				</script>
					<?php
				}

				$wolmart_layout['used_blocks'][ $block_id ]['js'] = true;
			}
		}
	}
}

Wolmart_Theme_Assets::get_instance();
