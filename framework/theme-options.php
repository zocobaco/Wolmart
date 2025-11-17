<?php
/**
 * Default Theme Options
 *
 * @package Wolmart WordPress Framework
 * @since 1.0.0
 *
 * @var array $wolmart_option
 */
defined( 'ABSPATH' ) || die;

$default_conditions = array(
	'site'            => array(
		array(
			'title' => esc_html__( 'Global Layout', 'wolmart' ),
		),
	),
	'archive_product' => array(
		array(
			'title'   => esc_html__( 'Shop Layout', 'wolmart' ),
			'scheme'  => array(
				'all' => true,
			),
			'options' => array(
				'left_sidebar'    => 'shop-sidebar',
				'ptb'             => 'hide',
				'show_breadcrumb' => 'yes',
			),
		),
	),
	'single_product'  => array(
		array(
			'title'   => esc_html__( 'Product Page Layout', 'wolmart' ),
			'scheme'  => array(
				'all' => true,
			),
			'options' => array(
				'right_sidebar'   => 'product-sidebar',
				'ptb'             => 'hide',
				'show_breadcrumb' => 'yes',
			),
		),
	),
	'archive_post'    => array(
		array(
			'title'   => esc_html__( 'Blog Layout', 'wolmart' ),
			'scheme'  => array(
				'all' => true,
			),
			'options' => array(
				'right_sidebar' => 'blog-sidebar',
				'post_type'     => 'list',
			),
		),
	),
	'single_post'     => array(
		array(
			'title'   => esc_html__( 'Post Page Layout', 'wolmart' ),
			'scheme'  => array(
				'all' => true,
			),
			'options' => array(
				'right_sidebar' => 'blog-sidebar',
				'ptb'           => 'hide',
			),
		),
	),
	'error'           => array(
		array(
			'title'   => esc_html__( '404 Page Layout', 'wolmart' ),
			'options' => array(
				'wrap' => 'full',
				'ptb'  => 'hide',
			),
		),
	),
);

$wolmart_option = array(
	// Navigator
	'navigator_items'               => array(
		'ai_generator'   => array( esc_html__( 'AI Content Generator', 'wolmart' ), 'section' ),
		'custom_css_js'  => array( esc_html__( 'Style / Additional CSS & Script', 'wolmart' ), 'section' ),
		'color'          => array( esc_html__( 'Color & Skin', 'wolmart' ), 'section' ),
		'blog_global'    => array( esc_html__( 'Blog / Blog Global', 'wolmart' ), 'section' ),
		'product_type'   => array( esc_html__( 'Shop / Product Type', 'wolmart' ), 'section' ),
		'category_type'  => array( esc_html__( 'Shop / Category Type', 'wolmart' ), 'section' ),
		'product_layout' => array( esc_html__( 'Product Page / Product Layout', 'wolmart' ), 'section' ),
		'product_data'   => array( esc_html__( 'Product Page / Product Data', 'wolmart' ), 'section' ),
		'share'          => array( esc_html__( 'Share', 'wolmart' ), 'section' ),
		'lazyload'       => array( esc_html__( 'Advanced / Lazy Load', 'wolmart' ), 'section' ),
		'search'         => array( esc_html__( 'Advanced / Search', 'wolmart' ), 'section' ),
	),

	// Conditions
	'conditions'                    => $default_conditions,

	// General
	'site_type'                     => 'full',
	'site_width'                    => '1400',
	'site_gap'                      => '20',
	'container'                     => '1280',
	'container_fluid'               => '1820',
	'content_bg'                    => array(
		'background-color' => '#fff',
	),
	'site_bg'                       => array(
		'background-color' => '#fff',
	),

	// Colors
	'primary_color'                 => '#2879FE',
	'secondary_color'               => '#f93',
	'dark_color'                    => '#333',
	'light_color'                   => '#ccc',

	// Skin
	'rounded_skin'                  => true,

	// Typography
	'typo_default'                  => array(
		'font-family'    => 'Poppins',
		'variant'        => '400',
		'font-size'      => '14px',
		'line-height'    => '1.6',
		'letter-spacing' => '',
		'color'          => '#666',
	),
	'typo_heading'                  => array(
		'font-family'    => 'inherit',
		'variant'        => '600',
		'line-height'    => '1.2',
		'letter-spacing' => '-0.025em',
		'text-transform' => 'none',
		'color'          => '#333',
	),
	'typo_custom1'                  => array(
		'font-family' => 'inherit',
	),
	'typo_custom2'                  => array(
		'font-family' => 'inherit',
	),
	'typo_custom3'                  => array(
		'font-family' => 'inherit',
	),

	/**
	 * Mobile Options
	 *
	 * @since 1.6.0
	 *
	 * - Mobile Sticky Icon Bar
	 * - Mobile Menu
	 **/

	// Mobile Sticky Icon Bar Type values: '', 'bottom', 'right'
	'mobile_bar_type'               => 'bottom',

	// Mobile Floating Button Styles
	'mobile_floating_button_type'   => '',
	'mobile_floating_button_custom' => '',

	// Mobile Sticky Icon Bottom Bar
	'mobile_bar_icons'              => array( 'home', 'shop', 'account', 'cart', 'search' ),
	'mobile_bar_menu_label'         => esc_html__( 'Menu', 'wolmart' ),
	'mobile_bar_menu_icon'          => 'w-icon-bars',
	'mobile_bar_home_label'         => esc_html__( 'Home', 'wolmart' ),
	'mobile_bar_home_icon'          => 'w-icon-home',
	'mobile_bar_shop_label'         => esc_html__( 'Categories', 'wolmart' ),
	'mobile_bar_shop_icon'          => 'w-icon-category',
	'mobile_bar_wishlist_label'     => esc_html__( 'Wishlist', 'wolmart' ),
	'mobile_bar_wishlist_icon'      => 'w-icon-heart',
	'mobile_bar_account_label'      => esc_html__( 'Account', 'wolmart' ),
	'mobile_bar_account_icon'       => 'w-icon-account',
	'mobile_bar_cart_label'         => esc_html__( 'Cart', 'wolmart' ),
	'mobile_bar_cart_icon'          => 'w-icon-cart',
	'mobile_bar_search_label'       => esc_html__( 'Search', 'wolmart' ),
	'mobile_bar_search_icon'        => 'w-icon-search',
	'mobile_bar_top_label'          => esc_html__( 'To Top', 'wolmart' ),
	'mobile_bar_top_icon'           => 'w-icon-long-arrow-up',

	// Mobile Language & Currency Swicher
	'mobile_fs_switcher_enable'     => true,

	// Mobile Menu Items
	'mobile_menu_items'             => array( 'main-menu' ),

	// Menu
	'menu_labels'                   => '',

	'top_button_size'               => '100',
	'top_button_pos'                => 'right',

	// Share
	'social_login'                  => true,
	'share_type'                    => 'framed',
	'share_icons'                   => array( 'facebook', 'twitter', 'pinterest', 'whatsapp', 'linkedin' ),

	// Page Title Bar
	'ptb_bg'                        => array(
		'background-color' => '#eee',
	),
	'ptb_height'                    => '180',
	'ptb_delimiter'                 => '>',
	'ptb_delimiter_use_icon'        => false,
	'ptb_delimiter_icon'            => '',
	'typo_ptb_title'                => array(
		'font-family'    => 'inherit',
		'variant'        => '700',
		'font-size'      => '34px',
		'line-height'    => '1.125',
		'letter-spacing' => '-0.025em',
		'text-transform' => 'capitalize',
		'color'          => '#333',
	),
	'typo_ptb_subtitle'             => array(
		'font-family'    => 'inherit',
		'variant'        => '',
		'font-size'      => '18px',
		'line-height'    => '1.8',
		'letter-spacing' => '',
		'color'          => '#666',
	),
	'typo_ptb_breadcrumb'           => array(
		'font-family'    => 'inherit',
		'font-size'      => '13px',
		'line-height'    => '',
		'letter-spacing' => '',
		'text-transform' => '',
		'color'          => '#333',
	),

	// Blog
	'blog_ajax'                     => false,
	'post_overlay'                  => 'zoom',
	'posts_layout'                  => 'grid',
	'posts_gap'                     => 'md',
	'posts_column'                  => 4,
	'post_related_count'            => 3,
	'post_related_column'           => 3,
	'excerpt_length'                => 35,
	'post_show_info'                => array(
		'image',
		'author',
		'date',
		'category',
		'comment',
		'tag',
		'author_info',
		'share',
		'related',
		'navigation',
		'comments_list',
	),

	// Products
	'products_column'               => 4,

	// Single Product
	'product_data_type'             => 'tab',
	'single_product_sticky'         => true,
	'single_product_sticky_mobile'  => true,
	'same_vendor_products'          => true,
	'product_description_title'     => esc_html__( 'Description', 'wolmart' ),
	'product_specification_title'   => esc_html__( 'Specification', 'wolmart' ),
	'product_reviews_title'         => esc_html__( 'Customer Reviews', 'wolmart' ),
	'show_buy_now_btn'              => false,
	'buy_now_text'                  => esc_html__( 'Buy Now', 'wolmart' ),

	'product_vendor_info_title'     => esc_html__( 'Vendor Info', 'wolmart' ),
	'product_fbt'                   => true,
	'product_fbt_title'             => esc_html__( 'Frequently Bought Together', 'wolmart' ),
	'product_upsells_count'         => 4,
	'product_related_count'         => 4,
	'product_type_new_period'       => 7,
	'product_more_title'            => esc_html__( 'More Products From This Vendor', 'wolmart' ),
	'product_more_order'            => 'rand',
	'compare_available'             => true,
	'compare_limit'                 => 4,
	'compare_popup_type'            => 'offcanvas',

	// GDPR Options
	'show_cookie_info'              => false,
	// translators: %s represents post types or taxonomies in the plural.
	'cookie_text'                   => sprintf( esc_html__( 'We are using cookies to improve your experience on our website. By browsing this website, you agree to our %1$sPrivacy Policy%2$s', 'wolmart' ), '<a href="#">', '</a>' ),
	'cookie_version'                => 1,
	'cookie_agree_btn'              => esc_html__( 'I Agree', 'wolmart' ),
	'cookie_decline_btn'            => esc_html__( 'Decline', 'wolmart' ),

	'product_hide_vendor_tab'       => false,

	// Products Comment Image
	'product_review_image_size'     => 1,
	'product_review_image_count'    => 2,

	// Product Title
	'prod_title_clamp'              => 1,

	// Product Excerpt
	'prod_excerpt_type'             => '',
	'prod_excerpt_length'           => 20,

	// Shop Advanced
	'new_product_period'            => 7,
	'shop_ajax'                     => false,
	'image_swatch'                  => false,
	'disable_ajax_account'          => false,
	'auto_close_mobile_filter'      => true,
	'prod_open_click_mob'           => true,
	'catalog_mode'                  => false,
	'catalog_price'                 => true,
	'catalog_cart'                  => false,
	'catalog_review'                => false,
	'cart_popup_type'               => 'mini-popup',

	// layouts
	'layout_default_wrap'           => 'container',
	'archive_layout_right_sidebar'  => 'blog-sidebar',
	'single_layout_right_sidebar'   => 'blog-sidebar',
	'shop_layout_left_sidebar'      => 'shop-sidebar',
	'error_layout_wrap'             => 'full',
	'error_layout_ptb'              => 'hide',

	// Vendor related options
	'vendor_products_column'        => 3,
	'vendor_style'                  => 'default',
	'vendor_style_option'           => 'theme',
	'vendor_soldby_style_option'    => 'theme',

	// Shop / Product Type
	'product_type'                  => '',
	'classic_hover'                 => '',
	'addtocart_pos'                 => '',
	'quickview_pos'                 => 'bottom',
	'wishlist_pos'                  => '',
	'show_in_box'                   => false,
	'show_media_shadow'             => false,
	'show_hover_shadow'             => false,
	'show_progress'                 => false,
	'show_info'                     => array(
		'category',
		'label',
		'price',
		'rating',
		'addtocart',
		'quickview',
		'wishlist',
		'compare',
		'sold_by',
	),
	'sold_by_label'                 => esc_html__( 'Sold By', 'wolmart' ),
	'hover_change'                  => true,
	'hover_style'                   => 'image-hover',
	'quickview_type'                => '',
	'quickview_thumbs'              => 'horizontal',
	'content_align'                 => 'left',
	'split_line'                    => false,

	// Shop / Category Type
	'category_type'                 => '',
	'subcat_cnt'                    => '5',
	'category_show_icon'            => '',
	'category_overlay'              => '',

	// WooCommerce
	'cart_show_clear'               => true,
	'freeshipping_initial'          => sprintf( esc_html__( '%1$s [remainder] %2$s', 'wolmart' ), 'Add', 'to cart and get free shipping!' ),
	'freeshipping_success'          => esc_html__( 'Your order qualifies for free shipping!', 'wolmart' ),

	// Advanced / Lazyload
	'skeleton_screen'               => false,
	'lazyload'                      => false,
	'lazyload_bg'                   => '#f4f4f4',
	'loading_animation'             => false,

	// Advanced / Search
	'full_screen_search'            => false,
	'live_search'                   => true,
	'search_post_type'              => 'product',
	'sales_popup'                   => '',
	'sales_popup_title'             => esc_html__( 'Someone Purchased', 'wolmart' ),
	'sales_popup_count'             => 5,
	'sales_popup_start_delay'       => 60,
	'sales_popup_interval'          => 60,
	'sales_popup_category'          => '',
	'sales_popup_mobile'            => true,
	'custom_image_sizes'            => array(),
	'custom_image_size'             => array(
		'Width'  => '',
		'Height' => '',
	),
	'image_quality'                 => 82,
	'big_image_threshold'           => 2560,

	// setup wizard
	'prefer_page_builder'           => 'elementor',
	'uninstall_page_builder'        => true,

	// optimize wizard
	'google_webfont'                => false,
	'lazyload_menu'                 => false,
	'menu_last_time'                => 0,
	'mobile_disable_slider'         => false,
	'mobile_disable_animation'      => false,

	'preload_fonts'                 => array( 'wolmart', 'fas', 'fab' ),
	'resource_disable_gutenberg'    => false,
	'resource_disable_wc_blocks'    => false,
	'resource_disable_elementor'    => false,
	'resource_disable_dokan'        => false,
	'resource_async_js'             => true,
	'resource_split_tasks'          => true,
	'resource_idle_run'             => true,
	'resource_after_load'           => true,
	'resource_disable_emojis'       => false,
	'resource_disable_jq_migrate'   => false,
	'resource_jquery_footer'        => false,
	'resource_merge_stylesheets'    => false,
	'resource_critical_css'         => false,

	// Custom CSS & JS
	'custom_css'                    => '',
	'custom_js'                     => '',
);

$wolmart_option['menu_labels'] = json_encode(
	array(
		'new' => get_theme_mod( 'primary_color', $wolmart_option['primary_color'] ),
		'hot' => get_theme_mod( 'secondary_color', $wolmart_option['secondary_color'] ),
	)
);

$social_shares = wolmart_get_social_shares();

foreach ( $social_shares as $key => $data ) {
	$wolmart_option[ 'social_addr_' . $key ] = '';
}

$wolmart_option = apply_filters( 'wolmart_theme_option_default_values', $wolmart_option );
