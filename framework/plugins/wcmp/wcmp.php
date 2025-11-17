<?php

/**
 * Wolmart_WCMP class
 *
 * @version 1.0.1
 * @package Wolmart WordPress Framework
 */

defined( 'ABSPATH' ) || die;

class Wolmart_WCMP extends Wolmart_Base {


	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {

		add_action( 'init', array( $this, 'init_vendor_settings' ), 20 );

		// version 3.7.8 compatibility
		if ( version_compare( WCMp_PLUGIN_VERSION, '3.7.8' ) >= 0 ) {
			add_filter( 'wcmp_load_default_vendor_list', '__return_true' );
			add_filter( 'wcmp_load_default_vendor_store', '__return_true' );
		}

		// Enqueue Wolmart-WCMP script
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );

		// Add wcmp store sidebar widget
		add_action( 'widgets_init', array( $this, 'register_sidebar' ) );
		add_action( 'template_redirect', array( $this, 'set_store_sidebar' ) );

		// Add wcmp goto vendor dashboard btn to account dashboard
		add_filter( 'wcmp_vendor_goto_dashboard', array( $this, 'hide_default_dashboard_link' ) );
		add_filter( 'wolmart_account_dashboard_link', array( $this, 'add_vendor_dashboard_btn' ) );

		// Check current page is vendor store
		add_filter( 'wolmart_is_vendor_store', array( $this, 'is_store_page' ) );
		add_filter( 'wolmart_vendor_store_sidebar_has_content', array( $this, 'has_sidebar_contents' ) );

		// Set body class for vendor store page
		add_filter( 'body_class', array( $this, 'set_body_class' ) );

		// Add abuse link
		add_action( 'wolmart_after_product_summary', array( $this, 'add_abuse_link' ) );

		// add tabs to vendor store page
		add_action( 'woocommerce_before_shop_loop', array( $this, 'vendor_store_tab_start' ) );
		add_action( 'wolmart_after_shop_loop_end', array( $this, 'vendor_store_tab_end' ) );

		// enqueue wcmp product related style for preventing style broken issue becuase of wcmp product btn in homepage
		add_action( 'wolmart_enqueue_product_widget_related_scripts', array( $this, 'enqueue_wcmp_product_scripts' ) );

		// Add vendor reg form fields
		add_action( 'wolmart_register_form', array( $this, 'add_vendor_reg_link' ) );

		// Add sold by template to product loop
		if ( in_array( 'sold_by', wolmart_get_option( 'show_info' ) ) ) {
			add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_sold_by_to_loop' ), 10 );
		}

		global $WCMp;
		remove_action( 'wp_ajax_wcmp_edit_product_attribute', array( $WCMp->ajax, 'edit_product_attribute_callback' ) );
		add_action( 'wp_ajax_wcmp_edit_product_attribute', array( $this, 'edit_product_attribute_callback' ) );
	}

	/**
	 * Edit product attribute callback
	 *
	 * @since 1.1.10
	 * @access public
	 */
	public function edit_product_attribute_callback() {
		global $WCMp;
		ob_start();

		check_ajax_referer( 'add-attribute', 'security' );

		if ( ! current_user_can( 'edit_products' ) || ( ! apply_filters( 'wcmp_vendor_can_add_custom_attribute', true ) && empty( sanitize_text_field( $_POST['taxonomy'] ) ) ) ) {
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

		$WCMp->template->get_template(
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
	 * Initialize WCMP settings
	 *
	 * @since 1.0.0
	 */
	public function init_vendor_settings() {

		global $WCMp; //phpcs:ignore

		// Remove default report abuse link and rating from
		remove_action( 'woocommerce_product_meta_start', array( $WCMp->product, 'add_report_abuse_link' ), 30 );
		remove_action( 'woocommerce_after_shop_loop', array( $WCMp->review_rating, 'wcmp_seller_review_rating_form', 30 ) );

		// Remove vendor tab
		if ( wolmart_get_option( 'product_hide_vendor_tab' ) ) {
			wolmart_call_clean_filter( 'woocommerce_product_tabs', array( $WCMp->product, 'product_vendor_tab' ) );
		} else {
			// Change title of vendor tab
			if ( wolmart_get_option( 'product_vendor_info_title' ) ) {
				wolmart_call_clean_filter( 'woocommerce_product_tabs', array( $WCMp->product, 'product_vendor_tab' ) );
				add_filter( 'woocommerce_product_tabs', array( $this, 'set_vendor_info_tab' ) );
			}
		}

		remove_action( 'woocommerce_after_shop_loop_item', array( $WCMp->vendor_caps, 'wcmp_after_add_to_cart_form' ), 6 );
	}


	/**
	 * Register WCMP store sidebar
	 *
	 * @since 1.0.0
	 */
	public function register_sidebar() {

		register_sidebar(
			array(
				'name'          => esc_html__( 'WCMP Store Sidebar', 'wolmart' ),
				'id'            => 'wcmp-store-sidebar',
				'before_widget' => '<nav id="%1$s" class="widget %2$s widget-collapsible">',
				'after_widget'  => '</nav>',
				'before_title'  => '<h3 class="widget-title"><span class="wt-area">',
				'after_title'   => '</span></h3>',
			)
		);
	}


	/**
	 * Change vendor info tab title
	 *
	 * @since 1.0.0
	 * @param array $tabs
	 */
	public function set_vendor_info_tab( $tabs ) {
		global $WCMp, $product; //phpcs:ignore

		if ( $product ) {
			$vendor = get_wcmp_product_vendors( $product->get_id() );

			if ( $vendor ) {
				$tabs['vendor'] = array(
					'title'    => wolmart_get_option( 'product_vendor_info_title' ),
					'priority' => 20,
					'callback' => array( $WCMp->product, 'woocommerce_product_vendor_tab' ), //phpcs:ignore
				);
			}
		}

		return $tabs;
	}


	/**
	 * Check vendor store page or not
	 *
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_store_page( $arg = false ) {

		global $WCMp; //phpcs:ignore

		if ( is_tax( $WCMp->taxonomy->taxonomy_name ) ) { //phpcs:ignore
			$vendor_id = get_queried_object()->term_id;
			if ( $vendor_id ) {
				$arg = true;
			}
		}

		return $arg;
	}


	/**
	 * Set body class for vendor store page
	 *
	 * @since 1.0.1
	 */
	public function set_body_class( $classes ) {
		if ( $this->is_store_page() ) {
			$classes[] = 'wolmart-wcmp-vendor-store-page';
		}
		return $classes;
	}


	/**
	 * Set sidebar of wolmart layout as wcmp store sidebar
	 *
	 * @since 1.0.0
	 */
	public function set_store_sidebar() {

		global $wolmart_layout;

		if ( $this->is_store_page() ) {
			$wolmart_layout['left_sidebar'] = 'wcmp-store-sidebar';
		}
	}


	/**
	 * Add item to go to vendor dashboard in account dashboard
	 *
	 * @since 1.0.0
	 */
	public function add_vendor_dashboard_btn() {
		$current_user = wp_get_current_user();
		return ! is_user_wcmp_pending_vendor( $current_user ) && is_user_wcmp_vendor( $current_user ) && wcmp_vendor_dashboard_page_id() ?
			get_permalink( wcmp_vendor_dashboard_page_id() ) : '';
	}


	/**
	 * Hide default button to go to vendor dashboard in WCMp plugin
	 *
	 * @param string $arg
	 *
	 * @since 1.0.0
	 */
	public function hide_default_dashboard_link( $arg = '' ) {

		$dashboard_page_link = wcmp_vendor_dashboard_page_id() ? get_permalink( wcmp_vendor_dashboard_page_id() ) : '#';

		return '<a class="d-none" href="' . $dashboard_page_link . '">' . __( 'Dashboard - manage your account here', 'wolmart' ) . '</a>';
	}


	/**
	 * Check whether store sidebar has contents or not
	 *
	 * @param array $sidebar_widgets
	 * @since 1.0.0
	 */
	public function has_sidebar_contents( $sidebar_widgets = array() ) {

		$has_contents = false;

		foreach ( $sidebar_widgets as $area => $widgets ) {
			if ( 'wcmp-store-sidebar' == $area && is_array( $widgets ) && count( $widgets ) > 0 ) {
				$has_contents = true;
			}
		}

		return $has_contents;
	}


	/**
	 * Enqueue WCMP style
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'wolmart-wcmp-style', WOLMART_PLUGINS_URI . '/wcmp/wcmp.min.css', array(), WOLMART_VERSION );
	}


	/**
	 * Add WCMp abuse link in single product page
	 *
	 * @since 1.0.0
	 */
	public function add_abuse_link() {

		global $product;

		if ( apply_filters( 'wolmart_is_single_product_widget', false ) ) {
			return;
		}

		if ( apply_filters( 'wcmp_show_report_abuse_link', true, $product ) ) {
			$report_abuse_text = apply_filters( 'wcmp_report_abuse_text', __( 'Report Abuse', 'dc-woocommerce-multi-vendor' ), $product );
			$show_in_popup     = apply_filters( 'wcmp_show_report_abuse_form_popup', true, $product )
			?>

			<div class="wcmp-report-abouse-wrapper">
				<a href="javascript:void(0);" id="report_abuse" role="button"><?php echo esc_html( $report_abuse_text ); ?></a>
				<div id="report_abuse_form_custom"  class="<?php echo ! $show_in_popup ? '' : 'report-abouse-modal'; ?>" tabindex="-1" style="display: none;">
					<div class="br-5 <?php echo ! ( $show_in_popup ) ? 'toggle-content' : 'modal-content'; ?>">
						<div class="modal-header">
							<button type="button" class="close" aria-label="<?php esc_attr_e( 'Close', 'wolmart' ); ?>">&times;</button>
							<h2 class="wcmp-abuse-report-title1"><?php esc_html_e( 'Report an abuse for product ', 'dc-woocommerce-multi-vendor' ) . ' ' . the_title(); ?> </h2>
						</div>
						<div class="modal-body">
							<p class="field-row form-group">
								<input type="text" class="report_abuse_name form-control" id="report_abuse_name" name="report_abuse[name]" value="" style="width: 100%;" placeholder="<?php esc_attr_e( 'Name', 'dc-woocommerce-multi-vendor' ); ?>" required="">
								<span class="wcmp-report-abuse-error"></span>
							</p>
							<p class="field-row form-group">
								<input type="email" class="report_abuse_email form-control" id="report_abuse_email" name="report_abuse[email]" value="" style="width: 100%;" placeholder="<?php esc_attr_e( 'Email', 'dc-woocommerce-multi-vendor' ); ?>" required="">
								<span class="wcmp-report-abuse-error"></span>
							</p>
							<p class="field-row form-group">
								<textarea name="report_abuse[message]" class="report_abuse_msg form-control" id="report_abuse_msg" rows="5" style="width: 100%;" placeholder="<?php esc_attr_e( 'Leave a message explaining the reasons for your abuse report', 'dc-woocommerce-multi-vendor' ); ?>" required=""></textarea>
								<span class="wcmp-report-abuse-error"></span>
							</p>
						</div> 
						<div class="modal-footer">
							<input type="hidden" class="report_abuse_product_id" value="<?php echo esc_attr( $product->get_id() ); ?>">
							<button type="button" class="btn btn-primary btn-rounded submit-report-abuse" name="report_abuse[submit]"><?php esc_html_e( 'Report', 'dc-woocommerce-multi-vendor' ); ?></button>
						</div>
					</div>
				</div>
			</div>

			<?php
		}
	}


	/**
	 * Wrap wcmp vendor store tab
	 *
	 * @since 1.0.0
	 */
	public function vendor_store_tab_start() {
		if ( $this->is_store_page() ) :
			?>
		<div class="tab tab-nav-boxed tab-nav-underline tab-nav-sm-center">
			<ul class="nav nav-tabs">
				<li class="nav-item"><a class="nav-link active" href="#vendor-products" role="button"><?php echo esc_html__( 'Products', 'wolmart' ); ?></a></li>
				<li class="nav-item"><a class="nav-link" href="#vendor-reviews" role="button"><?php echo esc_html__( 'Reviews', 'wolmart' ); ?></a></li>
			</ul>
			<div class="tab-content">
				<div class="tab-pane active" id="vendor-products">
			<?php
		endif;
	}


	/**
	 * End wcmp vendor store tab
	 *
	 * @since 1.0.0
	 */
	public function vendor_store_tab_end() {
		if ( $this->is_store_page() ) :
			?>
				</div>
				<div class="tab-pane" id="vendor-reviews">
					<?php
					// phpcs:disable WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
					global $WCMp;
					if ( is_tax( $WCMp->taxonomy->taxonomy_name ) ) {
						if ( version_compare( WCMp_PLUGIN_VERSION, '3.7.8' ) >= 0 ) {
							$WCMp->template->get_template( 'wcmp-vendor-review-form.php', array( 'vendor_id' => wcmp_find_shop_page_vendor() ) );
						} else {
							$queried_object = get_queried_object();
							$WCMp->template->get_template( 'wcmp-vendor-review-form.php', array( 'queried_object' => $queried_object ) );
						}
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
	 * Enqueue WCMP product related styles and scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_wcmp_product_scripts() {

		wp_enqueue_style( 'product_css' );
	}



	/**
	 * Remove WCMp abuse link in single product page and
	 * vendor review form
	 *
	 * @since 1.0.0
	 */
	// public function wolmart_remove_wcmp_abuse_n_review_form() {

	// 	global $WCMp; //phpcs:ignore

	// 	remove_action( 'woocommerce_product_meta_start', array( $WCMp->product, 'add_report_abuse_link' ), 30 );
	// 	remove_action( 'woocommerce_after_shop_loop', array( $WCMp->review_rating, 'wcmp_seller_review_rating_form', 30 ) );
	// }



	/**
	 * Add link to signup as a vendor to login popup
	 *
	 * @since 1.0.0
	 */
	public function add_vendor_reg_link() {
		if ( wp_doing_ajax() ) {
			$page_id = wcmp_vendor_registration_page_id();

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
	 * @since 1.0.0
	 */
	public function add_sold_by_to_loop() {
		global $post;

		if ( ! function_exists( 'get_wcmp_product_vendors' ) ) {
			return;
		}

		// if ( 'Enable' === get_wcmp_vendor_settings( 'sold_by_catalog', 'general' ) && apply_filters( 'wcmp_sold_by_text_after_products_shop_page', true, $post->ID ) ) {
		$vendor = get_wcmp_product_vendors( $post->ID );

		if ( $vendor ) {
			$sold_by_text = wolmart_get_option( 'sold_by_label' ) ? wolmart_get_option( 'sold_by_label' ) : apply_filters( 'wcmp_sold_by_text', __( 'Sold By', 'dc-woocommerce-multi-vendor' ), $post->ID );
			?>
			<div class="wolmart-sold-by-container">
				<span class="sold-by-label"><?php echo esc_html( $sold_by_text ); ?></span>
				<a class="sold-by-name" href="<?php echo esc_url( $vendor->permalink ); ?>"><?php echo esc_html( $vendor->page_title ); ?></a>
			</div>
			<?php
		}
		// }
	}
}

Wolmart_WCMP::get_instance();
