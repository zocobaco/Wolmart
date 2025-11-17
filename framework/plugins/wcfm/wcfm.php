<?php
/**
 * Wolmart_WCFM class
 *
 * @version 1.1.2
 * @package Wolmart WordPress Framework
 */
defined( 'ABSPATH' ) || die;

class Wolmart_WCFM extends Wolmart_Base {

	/**
	 * Constructor
	 *
	 * @since 1.0.0
	 */
	public function __construct() {
		global $WCFM, $WCFMmp; //phpcs:ignore

		add_action( 'init', array( $this, 'init_vendor_settings' ), 20 );

		// enqueue WCFMmp compatibility scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 50 );

		// add dashboard link to my account dashboard
		// add_filter( 'wolmart_account_dashboard_link', array( $this, 'add_vendor_dashboard_btn' ) );

		// Change the title of link to go to vendor dashboard in WCFM
		add_filter( 'wcfmmp_wcmy_dashboard_page_title', array( $this, 'change_vendor_dashboard_link_title' ) );
		add_filter( 'wolmart_is_vendor_store', array( $this, 'is_vendor_store_page' ) );
		add_filter( 'wcfmmp_stores_args', array( $this, 'set_store_args' ), 50, 3 );

		// add div element with class row if store list has sidebar
		add_action( 'wcfmmp_store_lists_after_map', array( $this, 'set_store_list_start' ) );
		add_action( 'wcfmmp_store_lists_end', array( $this, 'set_store_list_end' ) );

		// add_action( 'template_redirect', 'set_store_list_sidebar_layout' );
		add_filter( 'wcfm_store_lists_wrapper_class', array( $this, 'set_store_lists_wrapper_class' ) );
		add_filter( 'wcfm_store_wrapper_class', array( $this, 'set_store_wrapper_class' ) );
		add_filter( 'wcfmmp_store_sidebar_args', array( $this, 'set_sidebar_widget_args' ) );

		// enqueue wcfm core style to prevent style broken issue because of wcfm_buttons in homepage
		add_action( 'wolmart_enqueue_product_widget_related_scripts', array( $this, 'enqueue_wcfm_core_scripts' ) );

		// Add vendor reg form fields
		add_action( 'wolmart_register_form', array( $this, 'add_vendor_reg_link' ) );

		// Add image swatches under product attributes menu
		add_action( 'after_wcfm_products_manage_variable', array( $this, 'add_image_swatches_area' ), 20, 2 );

		// Get attributes that can be used in variation and swatches from WCFM view
		add_action( 'wp_ajax_generate_image_swatches', array( $this, 'generate_image_swatches' ) );
		add_action( 'wp_ajax_nopriv_generate_image_swatches', array( $this, 'generate_image_swatches' ) );

		// Save product image swatches as product meta
		add_action( 'after_wcfm_products_manage_meta_save', array( $this, 'save_swatch_meta' ), 50, 2 );
	}

	/**
	 * Initialize wcfm hooks
	 *
	 * @since 1.0.0
	 */
	public function init_vendor_settings() {

		global $WCFM, $WCFMmp; //phpcs:ignore

		// Remove vendor tab
		if ( wolmart_get_option( 'product_hide_vendor_tab' ) ) {
			wolmart_call_clean_filter( 'woocommerce_product_tabs', 'wcfm_product_multivendor_tab', 98 );
		} else {
			// Change default tab title for vendor from theme options
			if ( wolmart_get_option( 'product_vendor_info_title' ) ) {
				add_filter( 'wcfm_product_store_tab_title', array( $this, 'set_vendor_info_tab_title' ) );
			}
		}

		// phpcs:disable
		if ( function_exists( 'wolmart_is_elementor_preview' ) && ! wolmart_is_elementor_preview() ) {
			// Remove default product manage button and set newly

			if ( !is_admin() || defined('DOING_AJAX') ) {
				remove_action( 'woocommerce_before_single_product_summary', array( $WCFM->frontend, 'wcfm_product_manage' ), 4 );
				add_action( 'wolmart_before_wc_gallery_figure', array( $WCFM->frontend, 'wcfm_product_manage' ) );
				
				remove_action( 'woocommerce_before_shop_loop_item', array( $WCFM->frontend, 'wcfm_product_manage' ), 4 );
				add_action( 'woocommerce_before_shop_loop_item', array( $WCFM->frontend, 'wcfm_product_manage' ), 6 );
	
				// Remove all defaut sold by template from WCFM dashboard settings
				remove_action( 'woocommerce_after_shop_loop_item_title', array( $WCFMmp->frontend, 'wcfmmp_sold_by_product' ), 9 );
				remove_action( 'woocommerce_after_shop_loop_item', array( $WCFMmp->frontend, 'wcfmmp_sold_by_product' ), 50 );
				remove_action( 'woocommerce_after_shop_loop_item_title', array( $WCFMmp->frontend, 'wcfmmp_sold_by_product' ), 50 );
			}

			// Set sold by position by theme.
			if ( in_array( 'sold_by', wolmart_get_option( 'show_info' ) ) ) {
				// add_action( 'woocommerce_after_shop_loop_item_title', array( $WCFMmp->frontend, 'wcfmmp_sold_by_product' ), 10 );
				add_action( 'woocommerce_after_shop_loop_item_title', array( $this, 'add_sold_by_to_loop' ), 10 );
			}
		}

		// Set sold by template by theme
		$template_type = $WCFMmp->wcfmmp_vendor->get_vendor_sold_by_template();

		if ( 'theme' == wolmart_get_option( 'vendor_soldby_style_option' ) ) {

			if ( 'tab' != $template_type ) {
				$wcfm_marketplace_options                            = get_option( 'wcfm_marketplace_options', array() );
				$wcfm_marketplace_options['vendor_sold_by_template'] = 'tab';

				// update wcfm settings
				update_option( 'wcfm_marketplace_options', $wcfm_marketplace_options );

				if ( !is_admin() || defined('DOING_AJAX') ) {
					remove_action( 'woocommerce_single_product_summary', array( $WCFMmp->frontend, 'wcfmmp_sold_by_single_product' ), 6 );
					remove_action( 'woocommerce_single_product_summary', array( $WCFMmp->frontend, 'wcfmmp_sold_by_single_product' ), 15 );
					remove_action( 'woocommerce_single_product_summary', array( $WCFMmp->frontend, 'wcfmmp_sold_by_single_product' ), 25 );
					remove_action( 'woocommerce_product_meta_start', array( $WCFMmp->frontend, 'wcfmmp_sold_by_single_product' ), 50 );
				}

				add_filter( 'woocommerce_product_tabs', 'wcfm_product_multivendor_tab', 98 );
			}
		}
		//phpcs:enable
	}


	/**
	 * Change title of vendor info tab
	 *
	 * @since 1.0.0
	 * @param string title
	 * @return string title
	 */
	public function set_vendor_info_tab_title( $title ) {

		return wolmart_get_option( 'product_vendor_info_title' );
	}


	/**
	 * Enqueue WCFM scripts
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';

		wp_enqueue_style( 'wolmart-wcfm-style', WOLMART_PLUGINS_URI . '/wcfm/wcfm' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array( 'wolmart-style' ), WOLMART_VERSION );
		wp_enqueue_style( 'wolmart-theme-shop' );

		if ( 'theme' === wolmart_get_option( 'vendor_style_option' ) ) {
			wp_enqueue_style( 'wolmart-wcfm-theme-style', WOLMART_PLUGINS_URI . '/wcfm/wcfm-theme' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', array( 'wolmart-style' ), WOLMART_VERSION );
		}

		if ( wolmart_get_option( 'image_swatch' ) ) {
			wp_register_script( 'wolmart-wcfm-script', WOLMART_PLUGINS_URI . '/wcfm/wcfm' . $suffix, array( 'wolmart-theme' ), WOLMART_VERSION, true );
			wp_enqueue_script( 'wolmart-wcfm-script' );

			wp_localize_script(
				'wolmart-wcfm-script',
				'wcfm_lib_swatch',
				array(
					'placeholder'       => esc_js( wc_placeholder_img_src() ),
					'file_frame_title'  => __( 'Choose an image', 'wolmart' ),
					'file_frame_btn'    => __( 'Use image', 'wolmart' ),
					'wcfm_swatch_nonce' => wp_create_nonce( 'wcfm_swatch_nonce' ),
				)
			);
		}
	}


	/**
	 * Add item to go to vendor dashboard in account dashboard
	 *
	 * @since 1.0.0
	 */
	public function add_vendor_dashboard_btn() {
		return wcfm_is_vendor() ? get_wcfm_page() : '';
	}


	/**
	 * Change the default title of link to go to vendor dashboard in WCFM
	 *
	 * @since 1.1.6
	 */
	public function change_vendor_dashboard_link_title( $title ) {
		$title = esc_html__( 'Store Manager', 'wolmart' );

		return $title;
	}


	/**
	 * Check vendor store page or not
	 *
	 * @param boolean
	 * @return boolean
	 * @since 1.0.0
	 */
	public function is_vendor_store_page( $arg = false ) {
		return wcfmmp_is_store_page();
	}


	/**
	 * Wrapper start with class-row
	 *
	 * @since 1.0.0
	 */
	public function set_store_list_start() {
		global $WCFMmp; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
		if ( $WCFMmp->wcfmmp_vendor->is_store_lists_sidebar() ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			echo '<div class="row gutter-lg store-list-wrap">';
		}
	}


	/**
	 * Wrapper end with class-row
	 *
	 * @since 1.0.0
	 */
	public function set_store_list_end() {
		global $WCFMmp; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

		if ( $WCFMmp->wcfmmp_vendor->is_store_lists_sidebar() ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			echo '</div>';
		}
	}


	/**
	 * Set WCFM store list sidebar
	 *
	 * @since 1.0.0
	 */
	public function set_store_list_sidebar_layout() {

		global $wolmart_layout, $WCFMmp; // phpcs:disable

		if ( wcfmmp_is_stores_list_page() && $WCFMmp->wcfmmp_vendor->is_store_lists_sidebar() ) {
			$store_sidebar_pos = isset( $WCFMmp->wcfmmp_marketplace_options['store_sidebar_pos'] ) ? $WCFMmp->wcfmmp_marketplace_options['store_sidebar_pos'] : 'left';
			// phpcs:enable

			if ( 'left' === $store_sidebar_pos ) {
				$wolmart_layout['left_sidebar'] = 'sidebar-wcfmmp-store-lists';
			} else {
				$wolmart_layout['right_sidebar'] = 'sidebar-wcfmmp-store-lists';
			}
		}
	}


	/**
	 * Enqueue WCFM core style
	 *
	 * @since 1.0.0
	 */
	public function enqueue_wcfm_core_scripts() {
		wp_enqueue_style( 'wcfm_core_css' );
	}


	/**
	 * Set wcfm store lists wrapper class
	 *
	 * @since 1.0.0
	 */
	public function set_store_lists_wrapper_class( $class ) {

		global $WCFMmp; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

		if ( $WCFMmp->wcfmmp_vendor->is_store_lists_sidebar() ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$class .= ' has-sidebar';
		} else {
			$class .= ' no-sidebar';
		}

		return $class;
	}


	/**
	 * Set WCFM store wrapper class
	 *
	 * @since 1.0.0
	 */
	public function set_store_wrapper_class( $class ) {
		global $wolmart_layout;

		if ( 'full' !== $wolmart_layout['wrap'] ) {
			$class .= 'container-fluid' === $wolmart_layout['wrap'] ? 'container-fluid' : 'container';
		}

		$class = 'container';

		return $class;
	}


	/**
	 * Set WCFM sidebar widget arguments
	 *
	 * @param array $args
	 * @return array $args
	 * @since 1.0.0
	 */
	public function set_sidebar_widget_args( $args ) {

		return array(
			'name'          => __( 'Vendor Store Sidebar', 'wc-multivendor-marketplace' ),
			'id'            => 'sidebar-wcfmmp-store',
			'before_widget' => '<aside class="widget widget-collapsible">',
			'after_widget'  => '</aside>',
			'before_title'  => '<h3 class="widget-title"> <span class="wt-area">',
			'after_title'   => '</span></h3>',
		);
	}


	/**
	 * Set WCFM store arguments
	 *
	 * @param array $args
	 * @return array $args
	 * @since 1.0.0
	 */
	public function set_store_args( $args, $attr, $search_data ) {
		global $WCFMmp; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase

		if ( $WCFMmp->wcfmmp_vendor->is_store_lists_sidebar() ) { // phpcs:ignore WordPress.NamingConventions.ValidVariableName.VariableNotSnakeCase
			$args['per_row'] = 2;
		}

		return apply_filters( 'set_store_args', $args, $attr, $search_data );
	}


	/**
	 * Add link to signup as a vendor to login popup
	 *
	 * @since 1.0.0
	 */
	public function add_vendor_reg_link() {
		if ( wp_doing_ajax() ) {

			$page_id = absint( get_option( 'wcfm_vendor_registration_page_id' ) );

			if ( $page_id ) {
				$register_link = get_permalink( $page_id );
				$register_text = esc_html__( 'Signup as a vendor?', 'wolmart' );

				echo '<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide">';
					echo '<a class="register_as_vendor" href="' . esc_url( $register_link ) . '">' . $register_text . '</a>';
				echo '</p>';
			}
		}
	}


	/**
	 * Add sold by label to product loop
	 *
	 * @since 1.0.0
	 */
	public function add_sold_by_to_loop() {

		if ( ! class_exists( 'WCFM' ) ) {
			return;
		}

		global $post, $WCFM, $WCFMmp;

		$vendor_id = $WCFM->wcfm_vendor_support->wcfm_get_vendor_id_from_product( $post->ID );

		if ( ! $vendor_id ) {
			return;
		}

		$sold_by_text = wolmart_get_option( 'sold_by_label' ) ? wolmart_get_option( 'sold_by_label' ) : apply_filters( 'wcfmmp_sold_by_label', esc_html__( 'Sold By:', 'wolmart' ) );
		$store_name   = $WCFM->wcfm_vendor_support->wcfm_get_vendor_store_by_vendor( absint( $vendor_id ) );

		?>
		<div class="wolmart-sold-by-container">
			<span class="sold-by-label"><?php echo esc_html( $sold_by_text ); ?></span>
			<?php echo wp_kses_post( $store_name ); ?>
		</div>
		<?php
	}


	/**
	 * Generate image swatch form and send it to user
	 *
	 * @since 1.1.2
	 */
	public function generate_image_swatches() {

		if ( ! check_ajax_referer( 'wcfm_swatch_nonce', 'wcfm_swatch_nonce', false ) ) {
			wp_send_json_error( __( 'Invalid nonce! Refresh your page and try again.', 'wolmart' ) );
			wp_die();
		}

		global $WCFM;

		$wcfm_products_manage_form_data = array();
		parse_str( sanitize_text_field( $_POST['wcfm_products_manage_form'] ), $wcfm_products_manage_form_data );

		// get product id of existing product or 0
		$product_id     = isset( $wcfm_products_manage_form_data['pro_id'] ) ? $wcfm_products_manage_form_data['pro_id'] : 0;
		$product        = wc_get_product( $product_id );
		$attribute_data = isset( $wcfm_products_manage_form_data['attributes'] ) ? $wcfm_products_manage_form_data['attributes'] : array();
		$attributes     = array();
		$html           = '';
		$attr_enabled   = false;
		$swatch_count   = 0;

		foreach ( $attribute_data as $attribute ) {
			if ( isset( $attribute['is_variation'] ) && 'enable' === $attribute['is_variation'] ) {
				if ( ( isset( $attribute['is_active'] ) && 'enable' === $attribute['is_active'] ) && ( isset( $attribute['is_visible'] ) && 'enable' === $attribute['is_visible'] ) && ( isset( $attribute['is_taxonomy'] ) && '1' === $attribute['is_taxonomy'] ) ) {
					$attributes[] = $attribute;
				}
			}
		}

		if ( $product ) {
			$swatch_options = $product->get_meta( 'swatch_options', true );
			if ( $swatch_options ) {
				$swatch_options = json_decode( $swatch_options, true );
			}
		}

		if ( count( $attributes ) > 0 ) { // if there are attributes that are enabled to product.
			ob_start();

			echo '<div id="wolmart-wcfm-swatch-options" class="accordion image-swatches-accordion accordion-simple">';

				echo '<div class="inline notice woocommerce-message mb-6">';
					echo '<p>';
						echo esc_html_e( 'This will replace product image with following uploaded image when attribute button is clicked.', 'wolmart' );
					echo '</p>';
				echo '</div>';

			foreach ( $attributes as $attribute ) :
				$metabox_class   = array();
				$metabox_class[] = 'taxonomy';
				$metabox_class[] = $attribute['tax_name'];

				$swatch_type = $swatch_options && isset( $swatch_options[ $attribute['tax_name'] ] ) ? $swatch_options[ $attribute['tax_name'] ]['type'] : 'image';
				?>
					<div data-taxonomy="<?php echo esc_attr( $attribute['tax_name'] ); ?>" class="woocommerce_attribute card wc-metabox <?php echo esc_attr( implode( ' ', $metabox_class ) ); ?>">
						<div class="card-header">
							<a href="#attribute-swatch-<?php echo esc_attr( $attribute['tax_name'] ); ?>" class="expand" role="tab"><?php echo wc_attribute_label( $attribute['tax_name'] ); ?></a>
						</div>

						<div id="attribute-swatch-<?php echo esc_attr( $attribute['tax_name'] ); ?>" class="woocommerce_attribute_data wc-metabox-content card-body collapsed">
							<p class="form-field pb-3">
								<label><?php esc_html_e( 'Button Type', 'wolmart' ); ?></label>
								<select class="swatch-type" id="swatch_options[<?php echo esc_attr( $attribute['tax_name'] ); ?>][type]" name="swatch_options[<?php echo esc_attr( $attribute['tax_name'] ); ?>][type]">
									<option value="label" <?php selected( $swatch_type, 'label' ); ?>><?php esc_html_e( 'Default', 'wolmart' ); ?></option>
									<option value="image" <?php selected( $swatch_type, 'image' ); ?>><?php esc_html_e( 'Image', 'wolmart' ); ?></option>
								</select>
								<span class="img_tip wcfmfa w-icon-question" data-tip="<?php echo esc_attr_e( 'Select button type as image to show image on button.', 'wolmart' ); ?>" data-hasqtip="46" aria-describedby="qtip-46"></span>
							</p>

							<table class="product_custom_swatches">
								<thead>
									<th><?php esc_html_e( 'Attribute', 'wolmart' ); ?></th>
									<th><?php esc_html_e( 'Image', 'wolmart' ); ?></th>
								</thead>
								<tbody>
							<?php
							foreach ( $attribute['value'] as $option ) {
								$swatch_count++;
								$term = get_term( $option );
								if ( $term ) {
									$attr_label = $term->name;
								} else {
									$attr_label = $option;
									$option     = preg_replace( '/\s+/', '_', $option );
								}
								$src    = wc_placeholder_img_src();
								$src_id = $swatch_options && isset( $swatch_options[ $attribute['tax_name'] ] ) && isset( $swatch_options[ $attribute['tax_name'] ][ $option ] ) ? $swatch_options[ $attribute['tax_name'] ][ $option ] : '';
								if ( $src_id ) {
									$src = wp_get_attachment_image_src( $src_id )[0];
								}
								?>
									<tr>
										<td><?php echo esc_html( $attr_label ); ?></td>
										<td>
											<img src="<?php echo esc_url( $src ); ?>" alt="<?php esc_attr_e( 'Thumbnail Preview', 'wolmart' ); ?>" width="32" height="32">
											<input class="upload_image_url" type="hidden" name="swatch_options[<?php echo esc_attr( $attribute['tax_name'] ); ?>][<?php echo esc_attr( $option ); ?>]" value="<?php echo esc_attr( $src_id ); ?>" />
											<button class="wolmart_wcfm_add_image_button btn btn-sm btn-outline btn-dark"><?php esc_html_e( 'Upload/Add image', 'wolmart' ); ?></button>
											<button class="wolmart_wcfm_remove_image_button btn btn-sm btn-outline btn-dark"><?php esc_html_e( 'Remove image', 'wolmart' ); ?></button>
										</td>
									</tr>
								<?php
							}
							?>
								</tbody>
							</table>
						</div>
					</div>
					<?php
					endforeach;
			?>
				<div class="toolbar">
					<a href="#" class="btn btn-sm btn-primary wolmart-wcfm-save-changes" disabled="disabled" role="button"><?php esc_html_e( 'Save changes', 'wolmart' ); ?></a>
				</div>
			</div>
			<?php
			$html         = ob_get_clean();
			$attr_enabled = true;
		}

		$response = array(
			'template'     => $html,
			'swatch_count' => $swatch_count,
			'attr_enabled' => $attr_enabled,
		);
		echo json_encode( $response );
		die;
	}


	/**
	 * Add image swatches under product attributes menu
	 *
	 * @param int $product_id
	 * @param string $product_type
	 * @since 1.1.2
	 */
	public function add_image_swatches_area( $product_id, $product_type ) {

		if ( ! wolmart_get_option( 'image_swatch' ) ) {
			return;
		}

		?>
		<div class="page_collapsible product-image-swatches-header variable variable-subscription pw-gift-card"  id="wcfm_products_manage_form_swatch_head">
			<label class="wcfmfa w-icon-swatchbook"></label><?php esc_html_e( 'Product Image Swatches', 'wolmart' ); ?><span></span>
		</div>

		<div id="wolmart-wcfm-image-swatches" class="wcfm-container simple variable external grouped <?php echo apply_filters( 'wcfm_pm_block_custom_class_linked', '' ); ?>">
			<div id="wcfm_products_manage_form_image_swatches_empty_expander" class="wcfm-content">
				<?php printf( __( 'Before you can add a variation you need to add some variation attributes on the Attributes tab. %1$sLearn more%2$s', 'wc-frontend-manager' ), '<br /><h2><a class="wcfm_dashboard_item_title" target="_blank" href="' . apply_filters( 'wcfm_variations_help_link', 'https://docs.woocommerce.com/document/variable-product/' ) . '">', '</a></h2>' ); ?>
			</div>
			<div id="wcfm_products_manage_form_image_swatches_expander" class="wcfm-content">
			</div>
		</div>
		<?php
	}


	/**
	 * Save swatches as product meta
	 *
	 * @hooked 'after_wcfm_products_manage_meta_save'
	 *
	 * @param int $product_id
	 * @param mixed $wcfm_products_manage_form_data
	 * @since 1.1.2
	 */
	public function save_swatch_meta( $product_id, $wcfm_products_manage_form_data = array() ) {

		if ( isset( $wcfm_products_manage_form_data['product_type'] ) && 'variable' === $wcfm_products_manage_form_data['product_type'] ) {
			// get product
			$product        = wc_get_product( $product_id );
			$swatch_options = isset( $wcfm_products_manage_form_data['swatch_options'] ) ? $wcfm_products_manage_form_data['swatch_options'] : false;

			if ( $swatch_options && is_array( $swatch_options ) ) {
				$product->update_meta_data( 'swatch_options', json_encode( $swatch_options ) );
			} else {
				$product->delete_meta_data( 'swatch_options' );
			}

			$product->save_meta_data();
		}
	}
}

Wolmart_WCFM::get_instance();
