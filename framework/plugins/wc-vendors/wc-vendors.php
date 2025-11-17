<?php

/**
 * Wolmart_WC_Vendors class
 *
 * @since 1.0.0
 * @package Wolmart WordPress Framework
 */

defined( 'ABSPATH' ) || die;

class Wolmart_WC_Vendors extends Wolmart_Base {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		// Remove vendor info tab from single product page
		if ( true == wolmart_get_option( 'product_hide_vendor_tab' ) && class_exists( 'WCV_Vendor_Shop' ) ) {
			wolmart_call_clean_filter( 'woocommerce_product_tabs', array( 'WCV_Vendor_Shop', 'seller_info_tab' ) );
		} else {

			// Change default title of vendor info tab
			if ( wolmart_get_option( 'product_vendor_info_title' ) ) {
				add_filter( 'wcvendors_seller_info_label', array( $this, 'set_vendor_info_tab_title' ) );
			}
		}

		// Enqueue wc-vendor compatibility scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );

		// Register sidebar
		add_action( 'widgets_init', array( $this, 'register_sidebar' ) );

		// Add body class to wc vendor page
		add_filter( 'body_class', array( $this, 'set_body_class' ) );

		// Add dashboard item to my account dashboard
		add_filter( 'wolmart_account_dashboard_link', array( $this, 'add_vendor_dashboard_btn' ) );

		// Social icon setting
		add_filter( 'wcvendors_social_media_settings', array( $this, 'set_social_icons' ) );

		// Disable product rating tab in single product
		add_filter( 'wcv_vendor_ratings_tab', array( $this, 'disable_sp_default_rating_tab' ) );

		// Remove default sold by from product meta
		if ( class_exists( 'WCV_Vendor_Cart' ) && method_exists( 'WCV_Vendor_Cart', 'sold_by_meta' ) ) {
			remove_action( 'woocommerce_product_meta_start', array( 'WCV_Vendor_Cart', 'sold_by_meta' ), 10, 2 );
			add_action( 'woocommerce_single_product_summary', array( $this, 'print_sold_by_template' ), 60 );
		}

		// Add vendor reg form fields
		add_action( 'wolmart_register_form', array( $this, 'add_vendor_reg_link' ) );

		// Set sidebar in store page
		add_filter( 'wolmart_get_layout', array( $this, 'set_store_sidebar' ) );

		// Add sold by to product loop before add to cart
		if ( class_exists( 'WCV_Vendor_Shop' ) && apply_filters( 'wcvendors_disable_sold_by_labels', wc_string_to_bool( get_option( 'wcvendors_display_label_sold_by_enable', 'no' ) ) ) ) {
			remove_action( 'woocommerce_after_shop_loop_item', array( 'WCV_Vendor_Shop', 'template_loop_sold_by' ), 9 );

			if ( in_array( 'sold_by', wolmart_get_option( 'show_info' ) ) ) {
				add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_sold_by_to_loop' ), 10 );
			}
		}
	}


	/**
	 * Enqueue WC Vendor compatibility script
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_style( 'wolmart-wc-vendors-style', WOLMART_PLUGINS_URI . '/wc-vendors/wc-vendors' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array( 'wolmart-style' ), WOLMART_VERSION );
	}


	/**
	 * Change the title of vendor info tab
	 *
	 * @since 1.0.0
	 * @param string title
	 * @return string title
	 */
	public function set_vendor_info_tab_title( $title ) {
		return wolmart_get_option( 'product_vendor_info_title' );
	}


	/**
	 * Set body class in the case of wc-vendors is active
	 *
	 * @since 1.0.0
	 * @param array[string] $classes
	 * @return array[string] $classes
	 */
	public function set_body_class( $classes ) {

		if ( class_exists( 'WC_Vendors' ) ) {

			$orders_page_id      = get_option( 'wcvendors_product_orders_page_id' );
			$shop_settings_page  = get_option( 'wcvendors_shop_settings_page_id' );
			$shop_dashboard_page = get_option( 'wcvendors_vendor_dashboard_page_id' );

			if ( is_page( $orders_page_id ) ) {
				$classes[] = 'wolmart-wc-vendors wolmart-wc-vendors-orders';
			} elseif ( get_the_ID() == $shop_settings_page ) {
				$classes[] = 'wolmart-wc-vendors wolmart-wc-vendors-shop-settings';
			} elseif ( get_the_ID() == $shop_dashboard_page ) {
				$classes[] = 'wolmart-wc-vendors wolmart-wc-vendors-dashboard';
			} elseif ( wolmart_is_shop() ) {
				$classes[] = 'wolmart-wc-vendors wolmart-wc-vendor-shop';
			} elseif ( wolmart_is_product() ) {
				$classes[] = 'wolmart-wc-single-product';
			}
		}

		return $classes;
	}


	/**
	 * Add WC Vendors dashboard item to my account dashboard
	 *
	 * @since 1.0.0
	 */
	public function add_vendor_dashboard_btn() {

		$user = get_user_by( 'id', get_current_user_id() );

		if ( ! WCV_Vendors::is_vendor( $user->ID ) ) {
			return '';
		}

		if ( defined( 'WCV_PRO_VERSION' ) ) {
			$dashboard_page_ids = (array) get_option( 'wcvendors_dashboard_page_id' );
			$dashboard_page_id  = reset( $dashboard_page_ids );
		} else {
			$dashboard_page_id = get_option( 'wcvendors_vendor_dashboard_page_id' );
		}

		return get_permalink( $dashboard_page_id );
	}



	/**
	 * Set wc-vendor's social icons newly
	 *
	 * @since 1.0.0
	 * @param array $settings
	 * @return array $settings
	 */
	public function set_social_icons( $settings ) {
		$settings = array(
			'twitter'   => array(
				'id'                  => '_wcv_twitter_username',
				'label'               => __( 'Twitter Username', 'wcvendors-pro' ),
				'placeholder'         => __( 'YourTwitterUserHere', 'wcvendors-pro' ),
				'desc_tip'            => 'true',
				'description'         => __( 'Your <a href="https://twitter.com/">Twitter</a> username without the url.', 'wcvendors-pro' ),
				'type'                => 'text',
				'icon'                => 'twitter-square',
				'url_template'        => '//twitter.com/%s',
				'w_icon'              => 'twitter',
				'w_class'             => 'twitter',
				'admin_signup_form'   => array(
					'title'   => __( 'Twitter', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_signup_social_twitter',
					'type'    => 'checkbox',
					'default' => false,
				),
				'admin_settings_form' => array(
					'title'   => __( 'Twitter', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_settings_social_twitter',
					'type'    => 'checkbox',
					'default' => false,
				),
			),
			'instagram' => array(
				'id'                  => '_wcv_instagram_username',
				'label'               => __( 'Instagram Username', 'wcvendors-pro' ),
				'placeholder'         => __( 'YourInstagramUsername', 'wcvendors-pro' ),
				'desc_tip'            => 'true',
				'description'         => __( 'Your <a href="https://instagram.com/">Instagram</a> username without the url.', 'wcvendors-pro' ),
				'type'                => 'text',
				'icon'                => 'instagram',
				'url_template'        => '//instagram.com/%s',
				'w_icon'              => 'instagram',
				'w_class'             => 'instagram',
				'admin_signup_form'   => array(
					'title'   => __( 'Instagram', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_signup_social_instagram',
					'type'    => 'checkbox',
					'default' => false,
				),
				'admin_settings_form' => array(
					'title'   => __( 'Instagram', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_settings_social_instagram',
					'type'    => 'checkbox',
					'default' => false,
				),
			),
			'facebook'  => array(
				'id'                  => '_wcv_facebook_url',
				'label'               => __( 'Facebook URL', 'wcvendors-pro' ),
				'placeholder'         => __( 'http://yourfacebookurl/here', 'wcvendors-pro' ),
				'desc_tip'            => 'true',
				'description'         => __( 'Your <a href="https://facebook.com/">Facebook</a> url.', 'wcvendors-pro' ),
				'type'                => 'text',
				'icon'                => 'facebook-square',
				'url_template'        => '%s',
				'w_icon'              => 'facebook',
				'w_class'             => 'facebook',
				'admin_signup_form'   => array(
					'title'   => __( 'Facebook', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_signup_social_facebook',
					'type'    => 'checkbox',
					'default' => false,
				),
				'admin_settings_form' => array(
					'title'   => __( 'Facebook', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_settings_social_facebook',
					'type'    => 'checkbox',
					'default' => false,
				),
			),
			'linkedin'  => array(
				'id'                  => '_wcv_linkedin_url',
				'label'               => __( 'LinkedIn URL', 'wcvendors-pro' ),
				'placeholder'         => __( 'http://linkedinurl.com/here', 'wcvendors-pro' ),
				'desc_tip'            => 'true',
				'description'         => __( 'Your <a href="https://linkedin.com/">LinkedIn</a> url.', 'wcvendors-pro' ),
				'type'                => 'url',
				'icon'                => 'linkedin',
				'url_template'        => '%s',
				'w_icon'              => 'linkedin-in',
				'w_class'             => 'linkedin',
				'admin_signup_form'   => array(
					'title'   => __( 'Linkedin', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_signup_social_linkedin',
					'type'    => 'checkbox',
					'default' => false,
				),
				'admin_settings_form' => array(
					'title'   => __( 'Linkedin', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_settings_social_linkedin',
					'type'    => 'checkbox',
					'default' => false,
				),
			),
			'youtube'   => array(
				'id'                  => '_wcv_youtube_url',
				'label'               => __( 'YouTube URL', 'wcvendors-pro' ),
				'placeholder'         => __( 'http://youtube.com/here', 'wcvendors-pro' ),
				'desc_tip'            => 'true',
				'description'         => __( 'Your <a href="https://youtube.com/">Youtube</a> url.', 'wcvendors-pro' ),
				'type'                => 'url',
				'icon'                => 'youtube-square',
				'url_template'        => '%s',
				'w_icon'              => 'youtube-solid',
				'w_class'             => 'youtube',
				'admin_signup_form'   => array(
					'title'   => __( 'Youtube', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_signup_social_youtube',
					'type'    => 'checkbox',
					'default' => false,
				),
				'admin_settings_form' => array(
					'title'   => __( 'Youtube', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_settings_social_youtube',
					'type'    => 'checkbox',
					'default' => false,
				),
			),
			'pinterest' => array(
				'id'                  => '_wcv_pinterest_url',
				'label'               => __( 'Pinterest URL', 'wcvendors-pro' ),
				'placeholder'         => __( 'https://www.pinterest.com/username/', 'wcvendors-pro' ),
				'desc_tip'            => 'true',
				'description'         => __( 'Your <a href="https://www.pinterest.com/">Pinterest</a> url.', 'wcvendors-pro' ),
				'type'                => 'url',
				'icon'                => 'pinterest-square',
				'url_template'        => '%s',
				'w_icon'              => 'pinterest',
				'w_class'             => 'pinterest',
				'admin_signup_form'   => array(
					'title'   => __( 'Pinterest', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_signup_social_pinterest',
					'type'    => 'checkbox',
					'default' => false,
				),
				'admin_settings_form' => array(
					'title'   => __( 'Pinterest', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_settings_social_pinterest',
					'type'    => 'checkbox',
					'default' => false,
				),
			),
			'snapchat'  => array(
				'id'                  => '_wcv_snapchat_username',
				'label'               => __( 'Snapchat Username', 'wcvendors-pro' ),
				'placeholder'         => __( 'snapchatUsername', 'wcvendors-pro' ),
				'desc_tip'            => 'true',
				'description'         => __( 'Your snapchat username.', 'wcvendors-pro' ),
				'type'                => 'text',
				'icon'                => 'snapchat',
				'url_template'        => '//www.snapchat.com/add/%s',
				'w_icon'              => 'snapchat',
				'w_class'             => 'snapchat',
				'admin_signup_form'   => array(
					'title'   => __( 'Snapchat', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_signup_social_snapchat',
					'type'    => 'checkbox',
					'default' => false,
				),
				'admin_settings_form' => array(
					'title'   => __( 'Snapchat', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_settings_social_snapchat',
					'type'    => 'checkbox',
					'default' => false,
				),
			),
			'telegram'  => array(
				'id'                  => '_wcv_telegram_username',
				'label'               => __( 'Telegram Username', 'wcvendors-pro' ),
				'placeholder'         => __( 'TelegramUsername', 'wcvendors-pro' ),
				'desc_tip'            => 'true',
				'description'         => __( 'Your telegram username.', 'wcvendors-pro' ),
				'type'                => 'text',
				'icon'                => 'telegram-square',
				'url_template'        => '//telegram.me/%s',
				'w_icon'              => 'telegram',
				'w_class'             => 'telegram',
				'admin_signup_form'   => array(
					'title'   => __( 'Telegram', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_signup_social_telegram',
					'type'    => 'checkbox',
					'default' => false,
				),
				'admin_settings_form' => array(
					'title'   => __( 'Telegram', 'wcvendors-pro' ),
					'id'      => 'wcvendors_hide_settings_social_telegram',
					'type'    => 'checkbox',
					'default' => false,
				),
			),
		);

		return $settings;
	}


	/**
	 * Disable Product Rating tab in single Product page
	 *
	 * @since 1.0.0
	 * @param array
	 * @return boolean false
	 */
	public function disable_sp_default_rating_tab( $arr ) {

		return array(
			'title'    => '',
			'priority' => 200,
			'callback' => '__return_false',
		);
	}


	/**
	 * Print wc sold by template
	 *
	 * @since 1.0.0
	 */
	public function print_sold_by_template( $loop = false ) {

		global $product;

		$vendor_id     = WCV_Vendors::get_vendor_from_product( $product->get_id() );
		$sold_by_label = wolmart_get_option( 'sold_by_label' ) ? wolmart_get_option( 'sold_by_label' ) : get_option( 'wcvendors_label_sold_by' );
		$sold_by       = wcv_get_sold_by_link( $vendor_id );
		$class         = ! $loop ? 'mt-2' : '';
		?>
		<div class="wolmart-sold-by-container <?php echo esc_attr( $class ); ?>">
			<span class="sold-by-label"><?php echo esc_html( $sold_by_label ); ?></span>
		<?php echo wolmart_escaped( $sold_by ); ?>
		</div>
		<?php
	}


	/**
	 * Add link to signup as a vendor to login popup
	 *
	 * @since 1.0.0
	 */
	public function add_vendor_reg_link() {
		if ( wp_doing_ajax() ) {
			$register_link = wc_get_page_permalink( 'myaccount' );
			$register_text = esc_html__( 'Signup as a vendor?', 'wolmart' );
			echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
				echo '<a class="register_as_vendor" href="' . esc_url( $register_link ) . '">' . $register_text . '</a>';
			echo '</p>';
		}
	}


	/**
	 * Register WC Vendor Store sidebar
	 *
	 * @since 1.0.0
	 */
	public function register_sidebar() {

		register_sidebar(
			array(
				'name'          => esc_html__( 'WC Vendors Store Sidebar', 'wolmart' ),
				'id'            => 'wcv-store-sidebar',
				'before_widget' => '<nav id="%1$s" class="widget %2$s widget-collapsible">',
				'after_widget'  => '</nav>',
				'before_title'  => '<h3 class="widget-title"><span class="wt-area">',
				'after_title'   => '</span></h3>',
			)
		);
	}



	/**
	 * Set store sidebar
	 *
	 * @since 1.0.0
	 */
	public function set_store_sidebar( $layout ) {

		if ( WCV_Vendors::is_vendor_page() ) {
			$layout['left_sidebar']  = 'wcv-store-sidebar';
			$layout['right_sidebar'] = '';
		}

		return $layout;
	}


	/**
	 * Add sold by to product loop
	 *
	 * @since 1.0.0
	 */
	public function add_sold_by_to_loop() {
		$this->print_sold_by_template( true );
	}
}

Wolmart_WC_Vendors::get_instance();

/**
 * Get social icons from WC vendor setting
 *
 * @since 1.0.0
 */
if ( ! function_exists( 'wolmart_wcv_format_store_social_icons' ) ) {

	/**
	 * Print social icons
	 *
	 * @since 1.0.0
	 * @param string $vendor_id
	 * @param array $hidden
	 * @return string html_template
	 */
	function wolmart_wcv_format_store_social_icons( $vendor_id, $hidden = array() ) {
		ob_start();

		foreach ( wcv_get_social_media_settings() as $key => $setting ) {
			if ( in_array( $key, $hidden ) ) {
				continue;
			}

			$value = get_user_meta( $vendor_id, $setting['id'], true );

			if ( ! $value ) {
				continue;
			}
			?>
			<li>
				<a href="<?php printf( $setting['url_template'], $value ); ?>" class="social-icon stacked social-<?php echo esc_attr( $setting['w_class'] ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'Social Icon', 'wolmart' ); ?>">
					<i class="w-icon-<?php echo esc_attr( $setting['w_icon'] ); ?>"></i>
				</a>
			</li>
			<?php
		}

		$list = trim( ob_get_clean() );
		if ( ! $list ) {
			return;
		}

		return '<ul class="social-icons">' . wolmart_escaped( $list ) . '</ul>';
	}
}
