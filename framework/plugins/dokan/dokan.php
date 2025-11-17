<?php
/**
 * Wolmart_Dokan class
 *
 * @since 1.0.0
 * @package Wolmart WordPress Framework
 */
defined( 'ABSPATH' ) || die;

class Wolmart_Dokan extends Wolmart_Base {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		if ( 'theme' == wolmart_get_option( 'vendor_style_option' ) ) {
			add_action( 'init', array( $this, 'init_vendor_settings' ) );
			add_action( 'wolmart_sidebar', array( $this, 'set_dashboard_sidebar' ) );
			add_filter( 'wolmart_main_content_wrap_cls', array( $this, 'add_content_wrap_class' ) );
			add_filter( 'dokan_store_widget_args', array( $this, 'set_store_widget_args' ) );
			add_filter( 'dokan_load_hamburger_menu', '__return_false' );
			add_filter( 'dokan_get_post_status_label_class', array( $this, 'get_post_status_label_class' ) );
			add_action( 'dokan_dashboard_wrap_start', array( $this, 'dashboard_wrap_start' ) );
			add_action( 'dokan_dashboard_wrap_end', array( $this, 'dashboard_wrap_end' ) );
		}

		// Enqueue wolmart-dokan script
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_style' ), 50 );
		add_filter( 'dokan_store_widget_args', array( $this, 'set_sidebar_widget_args' ), 20 );
		add_filter( 'dokan_profile_social_fields', array( $this, 'set_social_icons' ) );

		// Remove dokan goto vendor dashboard btn
		remove_action( 'woocommerce_account_dashboard', array( $this, 'dokan_set_go_to_vendor_dashboard_btn' ) );
		add_filter( 'wolmart_account_dashboard_link', array( $this, 'add_vendor_dashboard_btn' ) );

		// Check whether current page is vendor store or not
		add_filter( 'wolmart_is_vendor_store', array( $this, 'is_store_page' ) );
		add_filter( 'wolmart_get_vendor_store_url', array( $this, 'wolmart_dokan_vendor_store_url' ) );

		// Set body class
		add_filter( 'body_class', array( $this, 'set_body_class' ) );

		// Reset customizer options.
		add_filter( 'wolmart_customize_sections', array( $this, 'reset_customize_sections' ) );
		add_filter( 'wolmart_customize_fields', array( $this, 'reset_customize_fields' ) );
		add_filter( 'wolmart_get_layout', array( $this, 'remove_theme_sidebar' ) );

		// Dokan / Single Product
		remove_action( 'woocommerce_product_tabs', 'dokan_set_more_from_seller_tab', 10 );
		add_action( 'woocommerce_after_single_product_summary', 'wolmart_dokan_get_more_products_from_seller', 13 );

		// Set wolmart_layout's titles
		add_action( 'wp', array( $this, 'set_layout_titles' ) );

		// Add vendor reg form fields
		add_action( 'wolmart_register_form', array( $this, 'add_vendor_reg_link' ) );

		// Add sold by vendor to product loop
		if ( in_array( 'sold_by', wolmart_get_option( 'show_info' ) ) ) {
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_sold_by_to_loop' ), 10 );
		}
	}


	/**
	 * Remove Dokan Default Dashboard Sidebar
	 *
	 * @since 1.0.0
	 */
	public function init_vendor_settings() {
		if ( class_exists( 'WeDevs_Dokan' ) && class_exists( 'WeDevs\Dokan\Dashboard\Templates\Main' ) && has_action( 'dokan_dashboard_content_before', array( WeDevs\Dokan\Dashboard\Templates\Main::class, 'dashboard_side_navigation' ) ) ) {
			remove_action( 'dokan_dashboard_content_before', array( WeDevs\Dokan\Dashboard\Templates\Main::class, 'dashboard_side_navigation' ), 10 );
		}

		// Remove dokan vendor info tab
		if ( true == wolmart_get_option( 'product_hide_vendor_tab' ) ) {
			wolmart_call_clean_filter( 'woocommerce_product_tabs', 'dokan_seller_product_tab' );
		} else {

			// Change default tab title for vendor from theme options
			if ( wolmart_get_option( 'product_vendor_info_title' ) ) {
				wolmart_call_clean_filter( 'woocommerce_product_tabs', 'dokan_seller_product_tab' );
				add_filter( 'woocommerce_product_tabs', array( $this, 'set_vendor_info_tab' ) );
			}
		}
	}


	/**
	 * Change vendor info tabs
	 *
	 * @since 1.0.0
	 * @param array $tabs
	 */
	public function set_vendor_info_tab( $tabs ) {
		$tabs['seller'] = array(
			'title'    => wolmart_get_option( 'product_vendor_info_title' ),
			'priority' => 90,
			'callback' => 'dokan_product_seller_tab',
		);

		return $tabs;
	}


	/**
	 * Render Dokan dashboard sidebar
	 *
	 * @since 1.0.0
	 */
	public function set_dashboard_sidebar() {

		if ( $this->is_dashboard() ) :

			global $wp;

			wp_enqueue_script( 'wolmart-sticky-lib' );

			$request = $wp->request;
			$active  = explode( '/', $request );

			unset( $active[0] );

			if ( $active ) {
				$active_menu = implode( '/', $active );

				if ( 'new-product' == $active_menu ) {
					$active_menu = 'products';
				}

				if ( get_query_var( 'edit' ) && is_singular( 'product' ) ) {
					$active_menu = 'products';
				}
			} else {
				$active_menu = 'dashboard';
			}
			?>
			<aside class="dokan-dash-sidebar sidebar left-sidebar sidebar-fixed">
				<div class="sidebar-overlay"></div>
				<a class="sidebar-close" href="#" aria-label="<?php esc_attr_e( 'Close', 'wolmart' ); ?>" role="button"><i class="close-icon"></i></a>
				<a href="#" class="sidebar-toggle" aria-label="<?php esc_attr_e( 'Sidebar Toggle', 'wolmart' ); ?>" role="button"><i class="w-icon-chevron-right"></i></a>
				<div class="sidebar-content">
					<div class="sticky-sidebar">
						<?php
						global $allowedposttags;

							// These are required for the hamburger menu.
						if ( is_array( $allowedposttags ) ) {
							$allowedposttags['input'] = array(
								'id'      => array(),
								'type'    => array(),
								'checked' => array(),
							);
						}

						echo wp_kses( dokan_dashboard_nav( $active_menu ), $allowedposttags );
						?>
					</div>
				</div>
			</aside>
			<?php
		endif;
	}


	/**
	 * Add additional class to dokan dashboard content wrapper
	 *
	 * @since 1.0.0
	 */
	public function add_content_wrap_class( $cls ) {
		if ( $this->is_dashboard() ) {
			$cls = $cls . ' row gutter-lg';
		}

		return $cls;
	}


	/**
	 * Redefine dokan store widget arguments
	 *
	 * @since 1.0.0
	 */
	public function set_store_widget_args( $args ) {
		$args['before_widget'] = '<aside id="%1$s" class="widget dokan-store-widget %2$s widget-collapsible">';
		$args['before_title']  = '<h3 class="widget-title"><span class="wt-area">';
		$args['after_title']   = '</span></h3>';
		return $args;
	}


	/**
	 * Get dokan label class
	 *
	 * @since 1.0.0
	 */
	public function get_post_status_label_class( $atts ) {
		return  array(
			'publish' => 'dokan-label-primary',
			'draft'   => 'dokan-label-default',
			'pending' => 'dokan-label-danger',
			'future'  => 'dokan-label-warning',
		);
	}


	/**
	 * Wrap dokan dashboard with container or container-fluid
	 *
	 * @since 1.0.0
	 */
	public function dashboard_wrap_start() {
		if ( ! is_page() ) {
			wolmart_print_layout_before();
		}
	}


	/**
	 * Wrap dokan dashboard
	 *
	 * @since 1.0.0
	 */
	public function dashboard_wrap_end() {

		if ( ! is_page() ) {
			wolmart_print_layout_after();
		}
	}


	/**
	 * Overwrite dokan store sidebar widget args
	 *
	 * @since 1.0.0
	 */
	public function set_sidebar_widget_args( $args = array() ) {
		return array(
			'name'          => __( 'Dokan Store Sidebar', 'dokan-lite' ),
			'id'            => 'sidebar-store',
			'before_widget' => '<aside id="%1$s" class="widget dokan-store-widget widget-collapsible %2$s">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title"><span class="wt-area">',
			'after_title'   => '</span></h3>',
		);
	}


	/**
	 * Replace social icons from dokan default style
	 *
	 * @since 1.0.0
	 */
	public function set_social_icons( $args = array() ) {
		$fields = array(
			'fb'        => array(
				'icon'  => 'facebook',
				'title' => __( 'Facebook', 'dokan-lite' ),
			),
			'gplus'     => array(
				'icon'  => 'google',
				'title' => __( 'Google', 'dokan-lite' ),
			),
			'twitter'   => array(
				'icon'  => 'twitter',
				'title' => __( 'Twitter', 'dokan-lite' ),
			),
			'pinterest' => array(
				'icon'  => 'pinterest',
				'title' => __( 'Pinterest', 'dokan-lite' ),
			),
			'youtube'   => array(
				'icon'  => 'youtube',
				'title' => __( 'Youtube', 'dokan-lite' ),
			),
			'instagram' => array(
				'icon'  => 'instagram',
				'title' => __( 'Instagram', 'dokan-lite' ),
			),
		);

		return $fields;
	}


	/**
	 * Add item to go to vendor dashboard in account dashboard
	 *
	 * @since 1.0.0
	 */
	public function add_vendor_dashboard_btn() {
		return dokan_is_user_seller( get_current_user_id() ) ? dokan_get_navigation_url() : '';
	}


	/**
	 * Set wrapper class of dokan store
	 *
	 * @param boolean
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_store_page( $arg = false ) {
		return function_exists( 'dokan_is_store_page' ) ? dokan_is_store_page() : false;
	}


	/**
	 * Enqueue Dokan Style
	 *
	 * @since 1.0.0
	 */
	public function enqueue_style() {
		if ( 'theme' == wolmart_get_option( 'vendor_style_option' ) ) {
			wp_enqueue_style( 'wolmart-dokan-theme-style', WOLMART_PLUGINS_URI . '/dokan/dokan-theme' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array( 'dokan-style' ), WOLMART_VERSION );
		}
		wp_enqueue_style( 'wolmart-dokan-style', WOLMART_PLUGINS_URI . '/dokan/dokan' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array( 'dokan-style' ), WOLMART_VERSION );
	}


	/**
	 * Move dokan's customize section to theme's section.
	 * And add vendor more products section to product page panel.
	 *
	 * @param boolean
	 * @return boolean
	 * @since 1.0.0
	 */
	public function reset_customize_sections( $sections ) {

		unset( $sections['vendor_store'] );

		$sections['dokan_store'] = array(
			'title'    => esc_html__( 'Vendor Store', 'wolmart' ),
			'panel'    => 'vendor',
			'priority' => 10,
		);

		$sections['vendor_more_products'] = array(
			'title'    => esc_html__( 'Vendor\'s More Products', 'wolmart' ),
			'panel'    => 'product',
			'priority' => 45,
		);

		return $sections;
	}


	/**
	 * Move dokan's customize fields to theme's fields.
	 *
	 * @param boolean
	 * @return boolean
	 * @since 1.0.0
	 */
	public function reset_customize_fields( $fields ) {

		$fields['cs_vendor_products_title'] = array(
			'section'  => 'dokan_store',
			'type'     => 'custom',
			'label'    => '',
			'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Vendor Store Page', 'wolmart' ) . '</h3>',
			'priority' => 0,
		);

		$fields['vendor_products_column'] = array(
			'section'  => 'dokan_store',
			'type'     => 'number',
			'label'    => esc_html__( 'Products Column', 'wolmart' ),
			'choices'  => array(
				'min' => 1,
				'max' => 6,
			),
			'priority' => 0,
		);

		$fields['vendor_use_sidebar'] = array(
			'section'  => 'dokan_store',
			'type'     => 'toggle',
			'label'    => esc_html__( 'Use theme sidebars too', 'wolmart' ),
			'priority' => 0,
		);

		$fields['cs_vendor_products_title_dokan'] = array(
			'section'  => 'dokan_store',
			'type'     => 'custom',
			'label'    => '',
			'default'  => '<h3 class="options-custom-title">' . esc_html__( 'Dokan Options', 'wolmart' ) . '</h3>',
			'priority' => 0,
		);

		$fields['cs_product_more']       = array(
			'section' => 'vendor_more_products',
			'type'    => 'custom',
			'label'   => '',
			'default' => '<h3 class="options-custom-title">' . esc_html__( 'Vendor\'s More Products', 'wolmart' ) . '</h3>',
		);
		$fields['product_more_title']    = array(
			'section' => 'vendor_more_products',
			'type'    => 'text',
			'label'   => esc_html__( 'Title', 'wolmart' ),
		);
		$fields['product_more_count']    = array(
			'section' => 'vendor_more_products',
			'type'    => 'number',
			'label'   => esc_html__( 'Count', 'wolmart' ),
			'choices' => array(
				'min' => 1,
				'max' => 50,
			),
		);
		$fields['product_more_order']    = array(
			'section' => 'vendor_more_products',
			'type'    => 'select',
			'label'   => esc_html__( 'Order', 'wolmart' ),
			'choices' => array(
				''              => esc_html__( 'Default', 'wolmart' ),
				'ID'            => esc_html__( 'ID', 'wolmart' ),
				'title'         => esc_html__( 'Title', 'wolmart' ),
				'date'          => esc_html__( 'Date', 'wolmart' ),
				'modified'      => esc_html__( 'Modified', 'wolmart' ),
				'price'         => esc_html__( 'Price', 'wolmart' ),
				'rand'          => esc_html__( 'Random', 'wolmart' ),
				'rating'        => esc_html__( 'Rating', 'wolmart' ),
				'popularity'    => esc_html__( 'popularity', 'wolmart' ),
				'comment_count' => esc_html__( 'Comment count', 'wolmart' ),
			),
		);
		$fields['product_more_orderway'] = array(
			'section' => 'vendor_more_products',
			'type'    => 'radio_buttonset',
			'label'   => esc_html__( 'Order Way', 'wolmart' ),
			'choices' => array(
				'asc' => esc_html( 'ASC', 'wolmart' ),
				''    => esc_html( 'DESC', 'wolmart' ),
			),
		);

		return $fields;
	}


	/**
	 * Remove theme sidebar if "use theme sidebar" option is off.
	 *
	 * @since 1.0
	 * @return object
	 */
	public function remove_theme_sidebar( $layout ) {

		if ( ! wolmart_get_option( 'vendor_use_sidebar' ) && $this->is_store_page() ) {
			$dokan_appearance = get_option( 'dokan_appearance' );
			if ( empty( $dokan_appearance['enable_theme_store_sidebar'] ) || 'on' != $dokan_appearance['enable_theme_store_sidebar'] ) {
				$layout['left_sidebar']  = '';
				$layout['right_sidebar'] = '';
			}
		}

		return $layout;
	}


	/**
	 * Set wolmart_layout's titles.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function set_layout_titles() {

		global $wolmart_layout;

		if ( apply_filters( 'wolmart_is_vendor_store', false ) ) {
			$store_user                 = dokan()->vendor->get( get_query_var( 'author' ) );
			$wolmart_layout['title']    = $store_user->get_shop_name();
			$wolmart_layout['subtitle'] = esc_html__( 'Vendor Store', 'wolmart' );
		}
	}


	/**
	 * Set body class for dokan
	 *
	 * @since 1.0.0
	 * @param array[string] $classes
	 * @return array[string] $classes
	 */
	public function set_body_class( $classes ) {

		if ( class_exists( 'Dokan_Pro' ) ) {
			$classes[] = 'wolmart-dokan-pro';
		} else {
			$classes[] = 'wolmart-dokan-lite';
		}

		return $classes;
	}



	/**
	 * Check whether current page is for dokan dashboard or not
	 *
	 * @since 1.0.0
	 */
	public function is_dashboard() {
		if ( class_exists( 'WeDevs_Dokan' ) && function_exists( 'dokan_is_seller_dashboard' ) && ( dokan_is_seller_dashboard() || ( get_query_var( 'edit' ) && is_singular( 'product' ) ) ) ) {
			return true;
		}
		return false;
	}



	/**
	 * Add vendor register link to login popup
	 *
	 * @since 1.0.0
	 */
	public function add_vendor_reg_link() {
		if ( wolmart_doing_ajax() ) {
			$register_link = wc_get_page_permalink( 'myaccount' );
			$register_text = esc_html__( 'Signup as a vendor?', 'wolmart' );
			echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
				echo '<a class="register_as_vendor" href="' . esc_url( $register_link ) . '">' . $register_text . '</a>';
			echo '</p>';
		}
	}


	/**
	 * Add sold by label to product loop
	 *
	 * @since 1.0.0
	 */
	public function add_sold_by_to_loop() {

		if ( ! function_exists( 'dokan_get_store_url' ) ) {
			return;
		}

		global $product;

		$author_id = get_post_field( 'post_author', $product->get_id() );
		$author    = get_user_by( 'id', $author_id );

		if ( empty( $author ) ) {
			return;
		}

		$shop_info    = get_user_meta( $author_id, 'dokan_profile_settings', true );
		$shop_name    = $author->display_name;
		$sold_by_text = wolmart_get_option( 'sold_by_label' ) ? wolmart_get_option( 'sold_by_label' ) : __( 'Sold By: ', 'wolmart' );

		if ( $shop_info && isset( $shop_info['store_name'] ) && $shop_info['store_name'] ) {
			$shop_name = $shop_info['store_name'];
		}
		?>
		<div class="wolmart-sold-by-container">
			<span class="sold-by-label"><?php echo esc_html( $sold_by_text ); ?> </span>
			<a class="sold-by-name" href="<?php echo esc_url( dokan_get_store_url( $author_id ) ); ?>"><?php echo esc_html( $shop_name ); ?></a>
		</div>

		<?php
	}
}



if ( ! function_exists( 'wolmart_dokan_get_more_products_from_seller' ) ) {

	/**
	 * Display "more products from this vendor" section.
	 *
	 * @since 1.0.0
	 */
	function wolmart_dokan_get_more_products_from_seller( $args = array() ) {
		global $product, $post, $wolmart_layout;

		// get all products belongs to this author
		$author_id = get_post_field( 'post_author', $product->get_id() );
		$author    = get_user_by( 'id', $author_id );
		$store_url = dokan_get_store_url( $author->ID );
		?>
		<section class="more-seller-product products">
			<div class="title-wrapper title-start title-underline2">
				<h2 class="title title-link"><?php echo esc_html( wolmart_get_option( 'product_more_title' ) ); ?></h2>
				<a class="btn btn-link btn-slide-right btn-infinite" href="<?php echo esc_url( $store_url ); ?>"><?php esc_html_e( 'More Products', 'wolmart' ); ?><i class="w-icon-long-arrow-right"></i></a>
			</div>
			<?php
			wc_set_loop_prop( 'name', 'vendor_products' );

			$args = array(
				'post_type'      => 'product',
				'posts_per_page' => (int) wolmart_get_option( 'product_more_count' ),
				'orderby'        => wolmart_get_option( 'product_more_order' ),
				'orderway'       => wolmart_get_option( 'product_more_orderway' ),
				'post__not_in'   => array( $post->ID ),
				'author'         => $post->post_author,
			);

			$products = new WP_Query( $args );

			if ( $products->have_posts() ) {
				woocommerce_product_loop_start();

				while ( $products->have_posts() ) {
					$products->the_post();
					wc_get_template_part( 'content', 'product' );
				}

				woocommerce_product_loop_end();
			} else {
				echo '<div class="alert alert-info">';
				esc_html_e( 'No product has been found!', 'dokan-lite' );
				echo '</div>';
			}

			wp_reset_postdata();

			?>
		</section>
		<?php
	}
}

Wolmart_Dokan::get_instance();
