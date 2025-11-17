<?php

/**
 * Wolmart_MVX class
 *
 * @version 1.4.0
 * @package Wolmart WordPress Framework
 */

defined( 'ABSPATH' ) || die;

class Wolmart_MVX extends Wolmart_Base {
	/**
	 * Constructor
	 *
	 * @since 1.4.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init_vendor_settings' ), 20 );

		// Enqueue MVX script
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), PHP_INT_MAX - 1 );

		// Add MVX store sidebar widget
		add_action( 'widgets_init', array( $this, 'register_sidebar' ) );
		add_action( 'template_redirect', array( $this, 'set_store_sidebar' ) );

		// Add mvx goto vendor dashboard btn to account dashboard
		add_filter( 'mvx_vendor_goto_dashboard', array( $this, 'hide_default_dashboard_link' ) );
		add_filter( 'wolmart_account_dashboard_link', array( $this, 'add_vendor_dashboard_btn' ) );

		// Check current page is vendor store
		add_filter( 'wolmart_is_vendor_store', 'mvx_is_store_page' );
		add_filter( 'wolmart_vendor_store_sidebar_has_content', array( $this, 'has_sidebar_contents' ) );

		// Set body class for vendor store page
		add_filter( 'body_class', array( $this, 'set_body_class' ) );

		// Add abuse link
		add_action( 'wolmart_after_product_summary', array( $this, 'add_abuse_link' ) );

		// add tabs to vendor store page
		// add_action( 'woocommerce_before_shop_loop', array( $this, 'vendor_store_tab_start' ) );
		// add_action( 'wolmart_after_shop_loop_end', array( $this, 'vendor_store_tab_end' ) );

		// enqueue MVX product related style for preventing style broken issue becuase of MVX product btn in homepage
		add_action( 'wolmart_enqueue_product_widget_related_scripts', array( $this, 'enqueue_mvx_product_scripts' ) );

		// Add vendor reg form fields
		add_action( 'wolmart_register_form', array( $this, 'add_vendor_reg_link' ) );

		// Add sold by template to product loop
		if ( in_array( 'sold_by', wolmart_get_option( 'show_info' ) ) ) {
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_sold_by_to_loop' ), 10 );
		}

		global $MVX;
		remove_action( 'wp_ajax_mvx_edit_product_attribute', array( $MVX->ajax, 'edit_product_attribute_callback' ) );
		add_action( 'wp_ajax_mvx_edit_product_attribute', array( $this, 'edit_product_attribute_callback' ) );
	}

	/**
	 * Edit product attribute callback
	 *
	 * @since 1.4.0
	 * @access public
	 */
	public function edit_product_attribute_callback() {
		global $MVX;
		ob_start();

		check_ajax_referer( 'add-attribute', 'security' );

		if ( ! current_user_can( 'edit_products' ) || ( ! apply_filters( 'mvx_vendor_can_add_custom_attribute', true ) && empty( sanitize_text_field( $_POST['taxonomy'] ) ) ) ) {
			wp_die( -1 );
		}

		$i             = isset( $_POST['i'] ) ? absint( $_POST['i'] ) : 0;
		$metabox_class = array();
		$attribute     = new WC_Product_Attribute();

		$attribute->set_id( wc_attribute_taxonomy_id_by_name( sanitize_text_field( $_POST['taxonomy'] ) ) );
		$attribute->set_name( sanitize_text_field( $_POST['taxonomy'] ) );
		$attribute->set_visible( apply_filters( 'woocommerce_attribute_default_visibility', 1 ) );
		$attribute->set_variation( apply_filters( 'woocommerce_attribute_default_is_variation', 0 ) );

		if ( $attribute->is_taxonomy() ) {
			$metabox_class[] = 'taxonomy';
			$metabox_class[] = $attribute->get_name();
		}

		$MVX->template->get_template(
			'vendor-dashboard/product-manager/views/html-product-attribute.php',
			array(
				'attribute'     => $attribute,
				'i'             => $i,
				'metabox_class' => $metabox_class,
			)
		);
		wp_die();
	}

	/**
	 * Initialize MVX settings
	 *
	 * @since 1.4.0
	 */
	public function init_vendor_settings() {
		global $MVX;

		remove_action( 'mvx_after_main_content', array( $MVX->frontend, 'mvx_after_main_content' ) );

		add_action( 'mvx_before_main_content', array( $this, 'single_vendor_wrapper_start' ) );
		add_action( 'mvx_after_main_content', array( $this, 'single_vendor_wrapper_end' ) );
	}

	/**
	 * Register MVX store sidebar
	 *
	 * @since 1.4.0
	 */
	public function register_sidebar() {
		register_sidebar(
			array(
				'name'          => esc_html__( 'MultiVendorX Store Sidebar', 'wolmart' ),
				'id'            => 'mvx-store-sidebar',
				'before_widget' => '<nav id="%1$s" class="widget %2$s widget-collapsible">',
				'after_widget'  => '</nav>',
				'before_title'  => '<h3 class="widget-title"><span class="wt-area">',
				'after_title'   => '</span></h3>',
			)
		);
	}

	/**
	 * Set sidebar of MVX layout as mvx store sidebar
	 *
	 * @since 1.4.0
	 */
	public function set_store_sidebar() {
		global $wolmart_layout;

		if ( mvx_is_store_page() ) {
			$wolmart_layout['left_sidebar'] = 'mvx-store-sidebar';
		}
	}

	/**
	 * Enqueue MVX style
	 *
	 * @since 1.4.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'wolmart-mvx-style', WOLMART_PLUGINS_URI . '/mvx/mvx' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array(), WOLMART_VERSION );
	}

	/**
	 * Hide default button to go to vendor dashboard in MVX plugin
	 *
	 * @param string $arg
	 *
	 * @since 1.4.0
	 */
	public function hide_default_dashboard_link( $arg = '' ) {
		$dashboard_page_link = mvx_vendor_dashboard_page_id() ? get_permalink( mvx_vendor_dashboard_page_id() ) : '#';

		return '<a class="d-none" href="' . $dashboard_page_link . '">' . __( 'Dashboard - manage your account here', 'wolmart' ) . '</a>';
	}

	/**
	 * Add item to go to vendor dashboard in account dashboard
	 *
	 * @since 1.4.0
	 */
	public function add_vendor_dashboard_btn() {
		$current_user = wp_get_current_user();

		return ! is_user_mvx_pending_vendor( $current_user ) && is_user_mvx_vendor( $current_user ) && mvx_vendor_dashboard_page_id() ?
			get_permalink( mvx_vendor_dashboard_page_id() ) : '';
	}

	/**
	 * Check whether store sidebar has contents or not
	 *
	 * @param array $sidebar_widgets
	 * @since 1.4.0
	 */
	public function has_sidebar_contents( $sidebar_widgets = array() ) {
		$has_contents = false;

		foreach ( $sidebar_widgets as $area => $widgets ) {
			if ( 'mvx-store-sidebar' == $area && is_array( $widgets ) && count( $widgets ) > 0 ) {
				$has_contents = true;
			}
		}

		return $has_contents;
	}

	/**
	 * Set body class for vendor store page
	 *
	 * @since 1.4.0
	 */
	public function set_body_class( $classes ) {
		if ( mvx_is_store_page() ) {
			$classes[] = 'wolmart-mvx-vendor-store-page';
		}
		return $classes;
	}


	/**
	 * Add MVX abuse link in single product page
	 *
	 * @since 1.4.0
	 */
	public function add_abuse_link() {
		global $product;

		if ( apply_filters( 'wolmart_is_single_product_widget', false ) ) {
			return;
		}

		if ( apply_filters( 'mvx_show_report_abuse_link', true, $product ) ) {
			$report_abuse_text = apply_filters( 'mvx_report_abuse_text', __( 'Report Abuse', 'multivendorx' ), $product );
			$show_in_popup     = apply_filters( 'mvx_show_report_abuse_form_popup', true, $product )
			?>

			<div class="mvx-report-abouse-wrapper">
				<a href="javascript:void(0);" id="report_abuse" role="button"><?php echo esc_html( $report_abuse_text ); ?></a>
				<div id="report_abuse_form_custom"  class="<?php echo ! $show_in_popup ? '' : 'report-abouse-modal'; ?>" tabindex="-1" style="display: none;">
					<div class="br-5 <?php echo ! ( $show_in_popup ) ? 'toggle-content' : 'modal-content'; ?>">
						<div class="modal-header">
							<button type="button" class="close" aria-label="<?php esc_attr_e( 'Close', 'wolmart' ); ?>">&times;</button>
							<h2 class="mvx-abuse-report-title1"><?php esc_html_e( 'Report an abuse for product ', 'multivendorx' ) . ' ' . the_title(); ?> </h2>
						</div>
						<div class="modal-body">
							<p class="field-row form-group">
								<input type="text" class="report_abuse_name form-control" id="report_abuse_name" name="report_abuse[name]" value="" style="width: 100%;" placeholder="<?php esc_attr_e( 'Name', 'multivendorx' ); ?>" required="">
								<span class="mvx-report-abuse-error"></span>
							</p>
							<p class="field-row form-group">
								<input type="email" class="report_abuse_email form-control" id="report_abuse_email" name="report_abuse[email]" value="" style="width: 100%;" placeholder="<?php esc_attr_e( 'Email', 'multivendorx' ); ?>" required="">
								<span class="mvx-report-abuse-error"></span>
							</p>
							<p class="field-row form-group">
								<textarea name="report_abuse[message]" class="report_abuse_msg form-control" id="report_abuse_msg" rows="5" style="width: 100%;" placeholder="<?php esc_attr_e( 'Leave a message explaining the reasons for your abuse report', 'multivendorx' ); ?>" required=""></textarea>
								<span class="mvx-report-abuse-error"></span>
							</p>
						</div> 
						<div class="modal-footer">
							<input type="hidden" class="report_abuse_product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">
							<button type="button" class="btn btn-primary btn-rounded submit-report-abuse" name="report_abuse[submit]"><?php esc_html_e( 'Report', 'multivendorx' ); ?></button>
						</div>
					</div>
				</div>
			</div>

			<?php
		}
	}

	/**
	 * Wrap MVX vendor store tab
	 *
	 * @since 1.4.0
	 */
	public function vendor_store_tab_start() {
		if ( mvx_is_store_page() ) :
			?>
		<div class="tab tab-nav-boxed tab-nav-underline tab-nav-sm-center">
			<ul class="nav nav-tabs">
				<li class="nav-item"><a class="nav-link active" href="#vendor-products" role="tab"><?php echo esc_html__( 'Products', 'wolmart' ); ?></a></li>
				<li class="nav-item"><a class="nav-link" href="#vendor-reviews" role="tab"><?php echo esc_html__( 'Reviews', 'wolmart' ); ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="vendor-products">
			<?php
		endif;
	}


	/**
	 * End MVX vendor store tab
	 *
	 * @since 1.4.0
	 */
	public function vendor_store_tab_end() {
		if ( mvx_is_store_page() ) :
			?>
				</div>
				<div class="tab-pane" id="vendor-reviews">
					<?php
                    // phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					global $MVX;
					if ( is_tax( $MVX->taxonomy->taxonomy_name ) ) {
						$MVX->template->get_template( 'mvx-vendor-review-form.php', array( 'vendor_id' => mvx_find_shop_page_vendor() ) );
					}
                    // phpcs:enable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					?>
				</div>
			</div>
		</div>
			<?php
		endif;
	}

	/**
	 * Enqueue MVX product related styles and scripts
	 *
	 * @since 1.4.0
	 */
	public function enqueue_mvx_product_scripts() {

		wp_enqueue_style( 'product_css' );
	}


	/**
	 * Add link to signup as a vendor to login popup
	 *
	 * @since 1.4.0
	 */
	public function add_vendor_reg_link() {
		if ( wp_doing_ajax() ) {
			$page_id = mvx_vendor_registration_page_id();

			$register_link = $page_id ? get_permalink( $page_id ) : '#';
			$register_text = esc_html__( 'Signup as a vendor?', 'wolmart' );

			if ( '#' != $register_link ) {
				echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
					echo '<a class="register_as_vendor" href="' . esc_url( $register_link ) . '">' . $register_text . '</a>';
				echo '</p>';
			}
		}
	}

	/**
	 * Add sold by template to product loop
	 *
	 * @since 1.4.0
	 */
	public function add_sold_by_to_loop() {
		global $post;

		if ( ! function_exists( 'get_mvx_product_vendors' ) ) {
			return;
		}

		$vendor = get_mvx_product_vendors( $post->ID );

		if ( $vendor ) {
			$sold_by_text = wolmart_get_option( 'sold_by_label' ) ? wolmart_get_option( 'sold_by_label' ) : apply_filters( 'mvx_sold_by_text', __( 'Sold By', 'multivendorx' ), $post->ID );
			?>
			<div class="wolmart-sold-by-container">
				<span class="sold-by-label"><?php echo esc_html( $sold_by_text ); ?></span>
				<a class="sold-by-name" href="<?php echo esc_url( $vendor->permalink ); ?>"><?php echo esc_html( $vendor->page_title ); ?></a>
			</div>
			<?php
		}
	}

	/**
	 * Single Vendor Start Wrapper
	 *
	 * @since 1.4.0
	 */
	public function single_vendor_wrapper_start() {
		wc_get_template( 'global/wrapper-start.php' );
	}

	/**
	 * Single Vendor End Wrapper
	 *
	 * @since 1.4.0
	 */
	public function single_vendor_wrapper_end() {
		wc_get_template( 'global/wrapper-end.php' );
	}
}

Wolmart_MVX::get_instance();
