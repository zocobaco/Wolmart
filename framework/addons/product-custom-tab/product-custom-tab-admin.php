<?php

/**
 * Wolmart_Product_Custom_Tab_Admin class
 *
 * @version 1.0
 */
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Wolmart_Product_Custom_Tab_Admin' ) ) {
	class Wolmart_Product_Custom_Tab_Admin {

		public function __construct() {
			add_filter( 'woocommerce_product_data_tabs', array( $this, 'add_product_data_tab' ), 101 );
			add_action( 'woocommerce_product_data_panels', array( $this, 'add_product_data_panel' ), 99 );

			add_action( 'wp_ajax_wolmart_save_product_tabs', array( $this, 'save_product_tabs' ) );
			add_action( 'wp_ajax_nopriv_wolmart_save_product_tabs', array( $this, 'save_product_tabs' ) );

			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ), 1001 );
		}

		public function add_product_data_tab( $tabs ) {
			$tabs['wolmart_custom_tabs'] = array(
				'label'    => esc_html__( 'Custom Description Tab ', 'wolmart' ),
				'target'   => 'wolmart_custom_tab_options',
				'priority' => 80,
			);
			return $tabs;
		}

		public function add_product_data_panel() {
			global $thepostid;
			?>
			<div id="wolmart_custom_tab_options" class="panel woocommerce_options_panel wc-metaboxes-wrapper hidden">
				<div class="options_group" style="padding-bottom: 9px !important">
					<?php
					woocommerce_wp_text_input(
						array(
							'id'    => 'wolmart_custom_tab_title_1st',
							'label' => esc_html__( 'Tab Title', 'wolmart' ),
						)
					);
					?>
					<div class="form-field wolmart_custom_tab_content_field">
						<label for="wolmart_custom_tab_title_1st"><?php esc_html_e( 'Tab Content', 'wolmart' ); ?></label>
						<?php
						$settings    = array(
							'textarea_name' => 'wolmart_custom_tab_content_1st',
							'quicktags'     => array( 'buttons' => 'em,strong,link' ),
							'tinymce'       => array(
								'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
								'theme_advanced_buttons2' => '',
							),
						);
						$tab_content = get_post_meta( $thepostid, 'wolmart_custom_tab_content_1st', true );

						wp_editor( wp_specialchars_decode( $tab_content, ENT_QUOTES ), 'wolmart_custom_tab_content_1st', apply_filters( 'wolmart_product_custom_tab_content_editor_settings', $settings ) );
						?>
					</div>
				</div>
				<div class="options_group" style="padding-bottom: 9px !important">
					<?php
					woocommerce_wp_text_input(
						array(
							'id'    => 'wolmart_custom_tab_title_2nd',
							'label' => esc_html__( 'Tab Title', 'wolmart' ),
						)
					);
					?>
					<div class="form-field wolmart_custom_tab_content_field">
						<label for="wolmart_custom_tab_title_2nd"><?php esc_html_e( 'Tab Content', 'wolmart' ); ?></label>
						<?php
						$settings    = array(
							'textarea_name' => 'wolmart_custom_tab_content_2nd',
							'quicktags'     => array( 'buttons' => 'em,strong,link' ),
							'tinymce'       => array(
								'theme_advanced_buttons1' => 'bold,italic,strikethrough,separator,bullist,numlist,separator,blockquote,separator,justifyleft,justifycenter,justifyright,separator,link,unlink,separator,undo,redo,separator',
								'theme_advanced_buttons2' => '',
							),
						);
						$tab_content = get_post_meta( $thepostid, 'wolmart_custom_tab_content_2nd', true );

						wp_editor( wp_specialchars_decode( $tab_content, ENT_QUOTES ), 'wolmart_custom_tab_content_2nd', apply_filters( 'wolmart_product_custom_tab_content_editor_settings', $settings ) );
						?>
					</div>
				</div>
				<div class="toolbar clear">
					<button type="button" class="button-primary save_wolmart_product_desc"><?php esc_html_e( 'Save tabs', 'wolmart' ); ?></button>
				</div>
			</div>
			<?php
		}

		public function enqueue_scripts() {
			wp_enqueue_media();

			wp_enqueue_script( 'wolmart-product-custom-tab', WOLMART_ADDONS_URI . '/product-custom-tab/product-custom-tab-admin' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ), array(), WOLMART_VERSION, true );
			wp_localize_script(
				'wolmart-product-custom-tab',
				'wolmart_product_custom_tab_vars',
				array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'post_id'  => get_the_ID(),
					'nonce'    => wp_create_nonce( 'wolmart-product-editor' ),
				)
			);
		}

		public function save_product_tabs() {

			if ( ! check_ajax_referer( 'wolmart-product-editor', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			$post_id = $_POST['post_id'];

			$custom_tab = isset( $_POST['wolmart_custom_tab_1st'] ) ? $_POST['wolmart_custom_tab_1st'] : false;

			if ( ! $custom_tab ) {
				delete_post_meta( $post_id, 'wolmart_custom_tab_title_1st' );
				delete_post_meta( $post_id, 'wolmart_custom_tab_content_1st' );
			} else {
				update_post_meta( $post_id, 'wolmart_custom_tab_title_1st', sanitize_text_field( $custom_tab[0] ) );
				update_post_meta( $post_id, 'wolmart_custom_tab_content_1st', wolmart_strip_script_tags( $custom_tab[1] ) );
			}

			$custom_tab = isset( $_POST['wolmart_custom_tab_2nd'] ) ? $_POST['wolmart_custom_tab_2nd'] : false;
			if ( ! $custom_tab ) {
				delete_post_meta( $post_id, 'wolmart_custom_tab_title_2nd' );
				delete_post_meta( $post_id, 'wolmart_custom_tab_content_2nd' );
			} else {
				update_post_meta( $post_id, 'wolmart_custom_tab_title_2nd', sanitize_text_field( $custom_tab[0] ) );
				update_post_meta( $post_id, 'wolmart_custom_tab_content_2nd', wolmart_strip_script_tags( $custom_tab[1] ) );
			}

			wp_send_json_success();
			die();
		}
	}
}

new Wolmart_Product_Custom_Tab_Admin;
