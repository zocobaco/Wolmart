<?php
/**
 * Wolmart Customizer
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Wolmart_Customizer' ) ) :

	class Wolmart_Customizer extends Wolmart_Base {

		protected $wp_customize;
		public $blocks;
		public $popups;
		public $product_layouts;
		public $panels;
		public $sections;
		public $fields;

		/**
		 * Constructor
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'customize_controls_print_styles', array( $this, 'load_styles' ) );
			add_action( 'customize_controls_print_scripts', array( $this, 'load_scripts' ), 30 );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_selective_assets' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'load_global_custom_css' ), 21 );
			add_action( 'customize_save_after', array( $this, 'save_theme_options' ), 1 );
			add_action( 'customize_register', array( $this, 'customize_register' ) );

			// Theme Option Import/Export
			add_action( 'wp_ajax_wolmart_import_theme_options', array( $this, 'import_options' ) );
			add_action( 'wp_ajax_nopriv_wolmart_import_theme_options', array( $this, 'import_options' ) );
			add_action( 'wp_ajax_wolmart_export_theme_options', array( $this, 'export_options' ) );
			add_action( 'wp_ajax_nopriv_wolmart_export_theme_options', array( $this, 'export_options' ) );

			// Theme Option Reset
			add_action( 'wp_ajax_wolmart_reset_theme_options', array( $this, 'reset_options' ) );
			add_action( 'wp_ajax_nopriv_wolmart_reset_theme_options', array( $this, 'reset_options' ) );

			// Header Preset
			add_action( 'wp_ajax_wolmart_header_preset', array( $this, 'get_header_preset' ) );
			add_action( 'wp_ajax_nopriv_wolmart_header_preset', array( $this, 'get_header_preset' ) );

			// Get Page Links ( Load other page for previewer )
			add_filter( 'wolmart_admin_vars', array( $this, 'add_local_vars' ) );

			// Customize Navigator
			add_action( 'customize_controls_print_scripts', array( $this, 'customizer_additional_tags' ) );

			add_action( 'wp_ajax_wolmart_save_customize_nav', array( $this, 'customizer_nav_save' ) );
			add_action( 'wp_ajax_nopriv_wolmart_save_customize_nav', array( $this, 'customizer_nav_save' ) );

			// Setup options
			add_action( 'init', array( $this, 'setup_options' ) );

			// Selective Refresh
			add_action( 'customize_register', array( $this, 'selective_refresh' ) );

			// Version Compatibiliy
			if ( defined( 'KIRKI_VERSION' ) && version_compare( KIRKI_VERSION, '4.0.0', '>' ) ) {
				$typos = array( 'typo_default', 'typo_heading', 'typo_custom1', 'typo_custom2', 'typo_custom3', 'typo_ptb_title', 'typo_ptb_subtitle', 'typo_ptb_breadcrumb' );

				foreach ( $typos as $typo ) {
					add_action( "customize_save_{$typo}", array( $this, 'customize_save_typography' ), 10, 1 );
				}
			}
		}

		/**
		 * customize_save_typography
		 *
		 * Action Handler for 'customize_save_{$typo}.
		 *
		 * @since 1.1.6
		 *
		 * @param WP_Customiz_Settings $settings
		 */
		public function customize_save_typography( &$setting ) {
			$id_data = $setting->id_data();
			$value   = $setting->post_value();

			if ( ! empty( $id_data['keys'] ) ) {
				$setting->type = 'none';
			}
		}

		/**
		 * load selective refresh JS
		 */
		public function load_selective_assets() {

			wp_enqueue_script( 'wolmart-selective', WOLMART_ADMIN_URI . '/customizer/selective-refresh.js', array( 'jquery-core' ), WOLMART_VERSION, true );

			wp_localize_script(
				'wolmart-selective',
				'wolmart_selective_vars',
				array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'nonce'    => wp_create_nonce( 'wolmart-selective' ),
				)
			);
		}

		/**
		 * load custom css
		 */
		public function load_global_custom_css() {
			wp_enqueue_style( 'wolmart-preview-custom', WOLMART_ADMIN_URI . '/customizer/preview-custom.min.css' );
			wp_add_inline_style( 'wolmart-preview-custom', wp_strip_all_tags( wp_specialchars_decode( wolmart_get_option( 'custom_css' ) ) ) );
		}

		/**
		 * Add CSS for Customizer Options
		 */
		public function load_styles() {
			wp_enqueue_style( 'wolmart-customizer', WOLMART_ADMIN_URI . '/customizer/customizer' . ( is_rtl() ? '-rtl' : '' ) . '.min.css', null, WOLMART_VERSION, 'all' );
			wp_enqueue_style( 'magnific-popup' );
		}

		/**
		 * Add JS for Customizer Options
		 */
		public function load_scripts() {

			wp_enqueue_script( 'wolmart-customizer', WOLMART_ADMIN_URI . '/customizer/customizer.js', array(), WOLMART_VERSION, true );

			wp_localize_script(
				'wolmart-customizer',
				'wolmart_customizer_vars',
				array(
					'ajax_url' => esc_url( admin_url( 'admin-ajax.php' ) ),
					'nonce'    => wp_create_nonce( 'wolmart-customizer' ),
					'tooltips' => apply_filters(
						'wolmart_customizer_image_tooltips',
						array(
							'#accordion-section-general',
							'#accordion-panel-style',
							'#accordion-panel-blog',
							'#accordion-panel-shop',
							'#accordion-panel-vendor',
							'#accordion-panel-product',
							'#accordion-panel-woocommerce',
							'#accordion-section-ai_generator',
							'#accordion-panel-nav_menus',
							'#accordion-panel-mobile',
							'#accordion-section-share',
							'#accordion-panel-advanced',
						)
					),
				)
			);
		}

		/**
		 * Save theme options
		 * @since 1.0
		 */
		public function save_theme_options() {
			ob_start();
			include WOLMART_FRAMEWORK . '/admin/customizer/dynamic/dynamic_vars.php';

			global $wp_filesystem;
			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}

			try {
				$target      = wp_upload_dir()['basedir'] . '/wolmart_styles/dynamic_css_vars.css';
				$target_path = dirname( $target );
				if ( ! file_exists( $target_path ) ) {
					wp_mkdir_p( $target_path );
				}

				// check file mode and make it writable.
				if ( is_writable( $target_path ) == false ) {
					@chmod( get_theme_file_path( $target ), 0755 );
				}
				if ( file_exists( $target ) ) {
					if ( is_writable( $target ) == false ) {
						@chmod( $target, 0755 );
					}
					@unlink( $target );
				}

				$wp_filesystem->put_contents( $target, ob_get_clean(), FS_CHMOD_FILE );
			} catch ( Exception $e ) {
				var_dump( $e );
				var_dump( 'error occured while saving dynamic css vars.' );
			}
		}

		public function customize_register( $wp_customize ) {
			$this->wp_customize = $wp_customize;
		}

		/**
		 * Import theme options
		 *
		 */
		public function import_options() {
			if ( ! $this->wp_customize->is_preview() ) {
				wp_send_json_error( 'not_preview' );
			}

			if ( ! check_ajax_referer( 'wolmart-customizer', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			if ( empty( $_FILES['file'] ) || empty( $_FILES['file']['name'] ) ) {
				wp_send_json_error( 'Empty file pathname' );
			}

			$filename = $_FILES['file']['name'];

			if ( empty( $_FILES['file']['tmp_name'] ) || '.json' !== substr( $filename, -5 ) ) {
				wp_send_json_error( 'invalid_type' );
			}

			global $wp_filesystem;

			// Initialize the WordPress filesystem, no more using file_put_contents function
			if ( empty( $wp_filesystem ) ) {
				require_once ABSPATH . '/wp-admin/includes/file.php';
				WP_Filesystem();
			}
			$options = $wp_filesystem->get_contents( $_FILES['file']['tmp_name'] );

			if ( $options ) {
				$options = json_decode( $options, true );
			}

			if ( $options ) {
				update_option( 'theme_mods_' . get_option( 'stylesheet' ), $options );
				wp_send_json_success();
			} else {
				wp_send_json_error( 'invalid_type' );
			}
		}

		public function get_menus() {
			$nav_menus = wp_get_nav_menus();
			$menus     = array();
			foreach ( $nav_menus as $menu ) {
				if ( ! preg_match( '/[^a-z\d]/i', $menu->name ) ) { // only for English (demos)
					$menus[ $menu->slug ] = esc_html( $menu->name );
				} else {
					$menus[ $menu->term_id ] = esc_html( $menu->name );
				}
			}
			return $menus;
		}


		public function get_social_shares() {
			$social_shares      = wolmart_get_social_shares();
			$social_shares_list = array();
			foreach ( $social_shares as $share => $data ) {
				$social_shares_list[ $share ] = $data['title'];
			}
			return $social_shares_list;
		}

		/**
		 * Export theme options
		 *
		 */
		public function export_options() {
			if ( ! $this->wp_customize->is_preview() ) {
				wp_send_json_error( 'not_preview' );
			}

			if ( ! check_ajax_referer( 'wolmart-customizer', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			header( 'Content-Description: File Transfer' );
			header( 'Content-type: application/txt' );
			header( 'Content-Disposition: attachment; filename="wolmart_theme_options_backup_' . date( 'Y-m-d' ) . '.json"' );
			header( 'Content-Transfer-Encoding: binary' );
			header( 'Expires: 0' );
			header( 'Cache-Control: must-revalidate' );
			header( 'Pragma: public' );
			echo json_encode( get_theme_mods() );
			exit;
		}

		/**
		 * Reset theme options
		 *
		 */
		public function reset_options() {
			if ( ! $this->wp_customize->is_preview() ) {
				wp_send_json_error( 'not_preview' );
			}

			if ( ! check_ajax_referer( 'wolmart-customizer', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			remove_theme_mods();

			// Delete compiled css in uploads/wolmart_style directory.
			global $wp_filesystem;
			if ( empty( $wp_filesystem ) ) {
				require_once( ABSPATH . '/wp-admin/includes/file.php' );
				WP_Filesystem();
			}

			try {
				$wp_filesystem->delete( wp_upload_dir()['basedir'] . '/wolmart_styles', true );
			} catch ( Exception $e ) {
				wp_send_json_error( 'error occured while deleting compiled css.' );
			}

			wp_send_json_success();
		}

		/**
		 * Get Header Preset Options
		 *
		 */
		public function get_header_preset() {
			if ( ! $this->wp_customize->is_preview() ) {
				wp_send_json_error( 'not_preview' );
			}

			if ( ! check_ajax_referer( 'wolmart-customizer', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			if ( ! isset( $_POST['idx'] ) ) {
				wp_send_json_error( 'Preset index is not correct.' );
			}

			wp_send_json_success( json_encode( array( 'result' => wolmart_header_preset( $_POST['idx'] ) ) ) );
		}

		/**
		 * Get Page Links
		 */
		public function add_local_vars( $vars ) {

			$home_url        = esc_js( home_url() );
			$blog_url        = '';
			$post_url        = '';
			$shop_url        = '';
			$cart_url        = '';
			$checkout_url    = '';
			$compare_url     = '';
			$product_url     = '';
			$vendor_page_url = '';

			$post = get_posts( array( 'posts_per_page' => 1 ) );
			if ( is_array( $post ) && count( $post ) ) {
				$blog_url = esc_js( get_post_type_archive_link( 'post' ) );
				$post_url = esc_js( get_permalink( $post[0] ) );
			}

			if ( class_exists( 'WooCommerce' ) ) {
				$shop_url        = esc_js( wc_get_page_permalink( 'shop' ) );
				$cart_url        = esc_js( wc_get_page_permalink( 'cart' ) );
				$checkout_url    = esc_js( wc_get_page_permalink( 'checkout' ) );
				$compare_url     = esc_js( wc_get_page_permalink( 'compare' ) );
				$product_url     = '';
				$vendor_page_url = '#';
				$product         = get_posts(
					array(
						'posts_per_page' => 1,
						'post_type'      => 'product',
					)
				);
				if ( is_array( $product ) && count( $product ) ) {
					$product_url = esc_js( get_permalink( $product[0] ) );
				}
			}

			$vars['page_links'] = array(
				'blog'                 => array(
					'url'      => $blog_url,
					'is_panel' => true,
				),
				'blog_global'          => array(
					'url'      => $blog_url,
					'is_panel' => false,
				),
				'blog_archive'         => array(
					'url'      => $blog_url,
					'is_panel' => false,
				),
				'blog_single'          => array(
					'url'      => $post_url,
					'is_panel' => false,
				),
				'shop'                 => array(
					'url'      => $shop_url,
					'is_panel' => true,
				),
				'wc_cart'              => array(
					'url'      => $cart_url,
					'is_panel' => false,
				),
				'wc_freeshipping'      => array(
					'url'      => $cart_url,
					'is_panel' => false,
				),
				'woocommerce_checkout' => array(
					'url'      => $checkout_url,
					'is_panel' => false,
				),
				'compare'              => array(
					'url'      => $compare_url,
					'is_panel' => false,
				),
				'product'              => array(
					'url'      => $product_url,
					'is_panel' => true,
				),
			);

			return $vars;
		}

		/**
		 * Comparison function for priority
		 *
		 * @since 1.0
		 */
		public function sort_priority( $a, $b ) {
			$ap = isset( $a['priority'] ) ? (int) $a['priority'] : 10;
			$bp = isset( $b['priority'] ) ? (int) $b['priority'] : 10;
			return $ap - $bp;
		}

		/**
		 * Get Navigator Template
		 */
		public function customizer_additional_tags() {
			$nav_items = wolmart_get_option( 'navigator_items' );

			$options = array();
			foreach ( $this->panels as $panel_id => $panel ) {
				$options[ $panel_id ] = array(
					'title'    => $panel['title'],
					'type'     => 'panel',
					'options'  => array(),
					'priority' => isset( $panel['priority'] ) ? $panel['priority'] : 10,
				);
				foreach ( $this->sections as $section_id => $section ) {
					if ( isset( $section['panel'] ) && $panel_id == $section['panel'] ) {
						$options[ $panel_id ]['options'][ $section_id ] = array(
							'title'    => $section['title'],
							'type'     => 'section',
							'options'  => array(),
							'callback' => isset( $section['custom_callback'] ) ? $section['custom_callback'] : array(),
							'priority' => isset( $section['priority'] ) ? $section['priority'] : 10,
						);

						foreach ( $this->fields as $field_id => $field ) {
							if ( 'custom' == $field['type'] && ! in_array( $field_id, array( 'cs_import_option', 'cs_export_option', 'cs_reset_option' ) ) ) {
								continue;
							}
							if ( in_array( $field_id, array( 'typo_custom1', 'typo_custom2', 'typo_custom3' ) ) ) {
								continue;
							}
							if ( isset( $field['section'] ) && $section_id == $field['section'] ) {
								if ( ( isset( $field['label'] ) || isset( $field['ms_label'] ) ) && ( ! isset( $field['active_callback'] ) || in_array( $field['active_callback'][0]['setting'], array( 'enable_portfolio', 'enable_member' ) ) ) ) {
									$options[ $panel_id ]['options'][ $section_id ]['options'][ $field_id ] = array(
										'title'      => isset( $field['ms_label'] ) ? $field['ms_label'] : $field['label'],
										'callback'   => isset( $field['active_callback'] ) ? $field['active_callback'] : array(),
										'responsive' => ! empty( $field['responsive'] ),
										'priority'   => isset( $field['priority'] ) ? $field['priority'] : 10,
									);
								}
							}
						}
						uasort( $options[ $panel_id ]['options'][ $section_id ]['options'], array( $this, 'sort_priority' ) );
					}
				}
				uasort( $options[ $panel_id ]['options'], array( $this, 'sort_priority' ) );
			}

			foreach ( $this->sections as $section_id => $section ) {
				if ( ! isset( $section['panel'] ) ) {
					$options[ $section_id ] = array(
						'title'    => $section['title'],
						'type'     => 'section',
						'options'  => array(),
						'callback' => isset( $section['custom_callback'] ) ? $section['custom_callback'] : array(),
						'priority' => isset( $section['priority'] ) ? $section['priority'] : 10,
					);

					foreach ( $this->fields as $field_id => $field ) {
						if ( 'custom' == $field['type'] && ! in_array( $field_id, array( 'cs_import_option', 'cs_export_option', 'cs_reset_option' ) ) ) {
							continue;
						}
						if ( in_array( $field_id, array( 'typo_custom1', 'typo_custom2', 'typo_custom3' ) ) ) {
							continue;
						}
						if ( isset( $field['section'] ) && $section_id == $field['section'] ) {
							if ( ( isset( $field['label'] ) || isset( $field['ms_label'] ) ) && ( ! isset( $field['active_callback'] ) || in_array( $field['active_callback'][0]['setting'], array( 'enable_portfolio', 'enable_member' ) ) ) ) {
								$options[ $section_id ]['options'][ $field_id ] = array(
									'title'      => isset( $field['ms_label'] ) ? $field['ms_label'] : $field['label'],
									'callback'   => isset( $field['active_callback'] ) ? $field['active_callback'] : array(),
									'responsive' => ! empty( $field['responsive'] ),
									'priority'   => isset( $field['priority'] ) ? $field['priority'] : 10,
									'type'       => 'field',
								);
							}
						}
					}
					uasort( $options[ $section_id ]['options'], array( $this, 'sort_priority' ) );
				}
			}
			$options = apply_filters( 'wolmart_options_milestone', $options );

			uasort( $options, array( $this, 'sort_priority' ) );

			ob_start();
			?>
			<div class="customizer-nav">
				<h3><?php esc_html_e( 'Navigator', 'wolmart' ); ?><a href="#" class="navigator-toggle" aria-label="<?php esc_attr_e( 'Navigator', 'wolmart' ); ?>" role="button"><i class="w-icon-chevron-left"></i></a></h3>
				<div class="customizer-nav-content">
					<ul class="customizer-nav-items">
						<?php foreach ( $nav_items as $section => $label ) : ?>
						<li>
							<a href="#" data-target="<?php echo esc_attr( $section ); ?>" data-type="<?php echo esc_attr( $label[1] ); ?>" class="customizer-nav-item"><?php echo esc_html( $label[0] ); ?></a>
							<a href="#" class="customizer-nav-remove" aria-label="<?php esc_attr_e( 'Remove', 'wolmart' ); ?>" role="button"><i class="w-icon-trash"></i></a>
						</li>
						<?php endforeach; ?>
					</ul>
				</div>
			</div>
			<?php
			foreach ( $options as $panel_id => $panel ) {
				if ( ! count( $panel['options'] ) ) {
					continue;
				}
				?>
				<div class="customizer-radio-image-preview customizer-panel-milestone" data-target="accordion-<?php echo esc_attr( $panel['type'] . '-' . $panel_id ); ?>" style="display: none">
					<div class="customizer-milestone-contents">
						<?php if ( 'panel' == $panel['type'] ) : ?>
						<div class="preview-row">
							<div class="preview-column title">
								<span data-target="<?php echo esc_attr( $panel_id ); ?>" data-type="panel" class="customizer-nav-home"><?php echo esc_html( $panel['title'] ); ?></span>
							</div>
							<?php if ( $panel['options'] ) : ?>
							<div class="preview-column content">
								<?php
								foreach ( $panel['options'] as $section_id => $section ) {
									// if ( ! count( $section['options'] ) ) {
									// 	continue;
									// }
									?>
									<div class="preview-row">
										<div class="preview-column title">
											<a href="#" data-target="<?php echo esc_attr( $section_id ); ?>" data-type="section" class="customizer-nav-item"<?php echo ( isset( $section['callback'][0] ) ? ( ' data-active-callback="' . esc_attr( json_encode( $section['callback'][0] ) ) . '"' ) : '' ); ?>><?php echo esc_html( $section['title'] ); ?></a>
										</div>
										<?php if ( $section['options'] ) : ?>
										<div class="preview-column content">
											<?php
											foreach ( $section['options'] as $field_id => $field ) {
												if ( ! $field['title'] ) {
													continue;
												}
												?>
											<a href="#" data-target="<?php echo esc_attr( $field_id ) . ( ! empty( $field['responsive'] ) ? '[desktop]' : '' ); ?>" data-type="control" class="customizer-nav-item"<?php echo ( isset( $field['callback'][0] ) ? ( ' data-active-callback="' . esc_attr( json_encode( $field['callback'][0] ) ) . '"' ) : '' ); ?>><?php echo esc_html( $field['title'] ); ?></a>
												<?php
											}
											?>
										</div>
										<?php endif; ?>
									</div>
									<?php
								}
								?>
							</div>
							<?php endif; ?>
						</div>
						<?php else : ?>
						<div class="preview-row">
							<div class="preview-column title">
								<span data-target="<?php echo esc_attr( $panel_id ); ?>" data-type="section" class="customizer-nav-home"><?php echo esc_html( $panel['title'] ); ?></span>
							</div>
							<?php if ( $panel['options'] ) : ?>
							<div class="preview-column content">
								<?php
								foreach ( $panel['options'] as $field_id => $field ) {
									if ( ! $field['title'] ) {
										continue;
									}
									?>
								<a href="#" data-target="<?php echo esc_attr( $field_id ) . ( ! empty( $field['responsive'] ) ? '[desktop]' : '' ); ?>" data-type="control" class="customizer-nav-item"<?php echo ( isset( $field['callback'][0] ) ? ( ' data-active-callback="' . esc_attr( json_encode( $field['callback'][0] ) ) . '"' ) : '' ); ?>><?php echo esc_html( $field['title'] ); ?></a>
									<?php
								}
								?>
							</div>
							<?php endif; ?>
						</div>
						<?php endif; ?>
					</div>
				</div>
							<?php
			}
			?>
			<div class="customizer-radio-image-preview" style="display: none"><img src=""></div>
				<?php
				echo ob_get_clean();
		}

		/**
		 * Save Navigator Items
		 *
		 */
		public function customizer_nav_save() {
			if ( ! check_ajax_referer( 'wolmart-customizer', 'nonce', false ) ) {
				wp_send_json_error( 'invalid_nonce' );
			}

			if ( isset( $_POST['navs'] ) ) {
				set_theme_mod( 'navigator_items', $_POST['navs'] );
				wp_send_json_success();
			}
		}

		public function get_edit_menu_label_control() {
			ob_start();
			?>
			<div class="label-list">
				<label><?php esc_html_e( 'Menu Labels', 'wolmart' ); ?></label>
				<select id="label-select" name="label-select">
				<?php
				$labels = json_decode( wolmart_get_option( 'menu_labels' ), true );
				if ( $labels ) :
					foreach ( $labels as $text => $color ) :
						?>
						<option value="<?php echo esc_html( $color ); ?>"><?php echo esc_html( $text ); ?></option>
						<?php
					endforeach;
				endif;
				?>
				</select>
			</div>
			<div class="menu-label">
				<label><?php esc_html_e( 'Label Text to Change', 'wolmart' ); ?></label>
				<input type="text" class="label-text" value="<?php echo esc_attr( $labels ? array_keys( $labels )[0] : '' ); ?>">
				<label><?php esc_html_e( 'Label Background Color to Change', 'wolmart' ); ?></label>
				<input type="text" class="wolmart-color-picker" value="<?php echo esc_attr( $labels ? $labels[ array_keys( $labels )[0] ] : '' ); ?>">
				<div class="label-actions">
					<button class="button button-primary btn-change-label"><?php esc_html_e( 'Change', 'wolmart' ); ?></button>
					<button class="button btn-remove-label"><?php esc_html_e( 'Remove', 'wolmart' ); ?></button>
				</div>
				<p class="error-msg"></p>
			</div>
			<?php
			return ob_get_clean();
		}

		public function get_new_menu_label_control() {
			ob_start();
			?>
			<div class="menu-label">
				<label><?php esc_html_e( 'Input Label Text', 'wolmart' ); ?></label>
				<input type="text" class="label-text">
				<label><?php esc_html_e( 'Choose Label Background Color', 'wolmart' ); ?></label>
				<input type="text" class="wolmart-color-picker" value="">
				<div class="label-actions">
					<button class="button button-primary btn-add-label"><?php esc_html_e( 'Add', 'wolmart' ); ?></button>
				</div>
				<p class="error-msg"></p>
			</div>
			<?php
			return ob_get_clean();
		}


		public function setup_options() {

			wolmart_set_global_templates_sidebars();

			$this->panels = array(
				'style'     => array(
					'title'    => esc_html__( 'Style', 'wolmart' ),
					'priority' => 20,
				),
				'nav_menus' => array(
					'title'    => esc_html__( 'Menus', 'wolmart' ),
					'priority' => 40,
				),
				'blog'      => array(
					'title'    => esc_html__( 'Blog', 'wolmart' ),
					'priority' => 50,
				),
				// Mobile Options
				'mobile'    => array(
					'title'    => esc_html__( 'Mobile', 'wolmart' ),
					'priority' => 110,
				),
				'widgets'   => array(
					'title'    => esc_html__( 'Widgets', 'wolmart' ),
					'priority' => 100,
				),
				'advanced'  => array(
					'title'    => esc_html__( 'Advanced', 'wolmart' ),
					'priority' => 120,
				),
			);

			$this->sections = array(
				// General Panel
				'general'                    => array(
					'title'    => esc_html__( 'General', 'wolmart' ),
					'priority' => 10,
				),
				// Header Panel
				'header'                     => array(
					'title'    => esc_html__( 'Header', 'wolmart' ),
					'priority' => 10,
				),
				// Footer Panel
				'footer'                     => array(
					'title'    => esc_html__( 'Footer', 'wolmart' ),
					'priority' => 10,
				),
				// AI Panel
				'ai_generator'               => array(
					'title'    => esc_html__( 'AI Generator', 'wolmart' ),
					'priority' => 90,
				),
				// Share Panel
				'share'                      => array(
					'title'    => esc_html__( 'Share', 'wolmart' ),
					'priority' => 110,
				),
				// Custom CSS & JS Panel
				'custom_css_js'              => array(
					'title'    => esc_html__( 'Custom CSS & JS', 'wolmart' ),
					'priority' => 130,
				),
				// Change Orders
				'title_tagline'              => array(
					'title'    => esc_html__( 'Site Identity', 'wolmart' ),
					'priority' => 150,
				),
				'static_front_page'          => array(
					'title'    => esc_html__( 'Homepage Settings', 'wolmart' ),
					'priority' => 160,
				),
				'colors'                     => array(
					'title'    => esc_html__( 'Color', 'wolmart' ),
					'priority' => 160,
				),
				'header_image'               => array(
					'title'    => esc_html__( 'Header Image', 'wolmart' ),
					'priority' => 170,
				),
				'background_image'           => array(
					'title'    => esc_html__( 'Background Image', 'wolmart' ),
					'priority' => 180,
				),
				// Style Panel
				'color'                      => array(
					'title'    => esc_html__( 'Color & Skin', 'wolmart' ),
					'panel'    => 'style',
					'priority' => 10,
				),
				'typo'                       => array(
					'title'    => esc_html__( 'Typography', 'wolmart' ),
					'panel'    => 'style',
					'priority' => 20,
				),
				'title_bar'                  => array(
					'title'    => esc_html__( 'Page Title Bar', 'wolmart' ),
					'panel'    => 'style',
					'priority' => 30,
				),
				'breadcrumb'                 => array(
					'title'    => esc_html__( 'Breadcrumb', 'wolmart' ),
					'panel'    => 'style',
					'priority' => 40,
				),
				// Menus
				'menu_labels'                => array(
					'title'    => esc_html__( 'Menu Labels', 'wolmart' ),
					'panel'    => 'nav_menus',
					'priority' => 3,
				),

				/**
				 * Mobile Options in Menu option panel.
				 *
				 * @deprecated deprecated since version 1.6.0
				 */
				'mobile_menu_deprecated'     => array(
					'title'    => esc_html__( 'Mobile Menu', 'wolmart' ),
					'panel'    => 'nav_menus',
					'priority' => 6,
				),

				'mobile_bar_deprecated'      => array(
					'title'    => esc_html__( 'Mobile Sticky Icon Bar', 'wolmart' ),
					'panel'    => 'nav_menus',
					'priority' => 8,
				),

				/**
				 * Mobile Options
				 *
				 * @since 1.6.0
				 */
				'mobile_menu'                => array(
					'title'    => esc_html__( 'Mobile Menu', 'wolmart' ),
					'panel'    => 'mobile',
					'priority' => 6,
				),
				'mobile_bar'                 => array(
					'title'    => esc_html__( 'Mobile Sticky Icon Bar', 'wolmart' ),
					'priority' => 8,
					'panel'    => 'mobile',
				),
				'mobile_fullscreen_switcher' => array(
					'title'    => esc_html__( 'Mobile Currency & Language Switcher', 'wolmart' ),
					'priority' => 10,
					'panel'    => 'mobile',
				),

				// Blog Panel
				'blog_global'                => array(
					'title'    => esc_html__( 'Blog Global', 'wolmart' ),
					'priority' => 10,
					'panel'    => 'blog',
				),
				'blog_archive'               => array(
					'title'    => esc_html__( 'Blog Page', 'wolmart' ),
					'priority' => 20,
					'panel'    => 'blog',
				),
				'blog_single'                => array(
					'title'    => esc_html__( 'Post Page', 'wolmart' ),
					'priority' => 30,
					'panel'    => 'blog',
				),
				// Advanced Panel
				'lazyload'                   => array(
					'title'    => esc_html__( 'Lazy Load', 'wolmart' ),
					'priority' => 10,
					'panel'    => 'advanced',
				),
				'search'                     => array(
					'title'    => esc_html__( 'Search', 'wolmart' ),
					'priority' => 20,
					'panel'    => 'advanced',
				),
				'images'                     => array(
					'title'    => esc_html__( 'Image Size & Quality', 'wolmart' ),
					'priority' => 30,
					'panel'    => 'advanced',
				),
				'reset_options'              => array(
					'title'    => esc_html__( 'Import/Export/Reset', 'wolmart' ),
					'priority' => 40,
					'panel'    => 'advanced',
				),
				'seo'                        => array(
					'title'    => esc_html__( 'SEO', 'wolmart' ),
					'priority' => 50,
					'panel'    => 'advanced',
				),
				'white_label'                => array(
					'title'    => esc_html__( 'White Label', 'wolmart' ),
					'priority' => 60,
					'panel'    => 'advanced',
				),
			);

			$this->fields = array(
				// General / Site Layout
				'cs_site_layout'                        => array(
					'section' => 'general',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Site Layout', 'wolmart' ) . '</h3>',
				),
				// 'site_type'                       => array(
				// 	'section'   => 'general',
				// 	'type'      => 'radio_buttonset',
				// 	'label'     => esc_html__( 'Site Type', 'wolmart' ),
				// 	'transport' => 'postMessage',
				// 	'choices'   => array(
				// 		'full'   => esc_html__( 'Full', 'wolmart' ),
				// 		'boxed'  => esc_html__( 'Boxed', 'wolmart' ),
				// 		'framed' => esc_html__( 'Framed', 'wolmart' ),
				// 	),
				// ),
				'site_type'                             => array(
					'section'   => 'general',
					'type'      => 'radio_image',
					'label'     => esc_html__( 'Site Type', 'wolmart' ),
					'transport' => 'postMessage',
					'choices'   => array(
						'full'   => WOLMART_ADMIN_URI . '/customizer/assets/site-full.svg',
						'boxed'  => WOLMART_ADMIN_URI . '/customizer/assets/site-boxed.svg',
						'framed' => WOLMART_ADMIN_URI . '/customizer/assets/site-framed.svg',
					),
				),
				'site_width'                            => array(
					'section'         => 'general',
					'type'            => 'text',
					'label'           => esc_html__( 'Site Width (px)', 'wolmart' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'site_type',
							'operator' => '!=',
							'value'    => 'full',
						),
					),
				),
				'site_gap'                              => array(
					'section'         => 'general',
					'type'            => 'text',
					'label'           => esc_html__( 'Site Gap (px)', 'wolmart' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'site_type',
							'operator' => '!=',
							'value'    => 'full',
						),
					),
				),
				'site_bg'                               => array(
					'section'         => 'general',
					'type'            => 'background',
					'label'           => esc_html__( 'Site Background', 'wolmart' ),
					'tooltip'         => esc_html__( 'Change background of outside the frame.', 'wolmart' ),
					'default'         => '',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'site_type',
							'operator' => '!=',
							'value'    => 'full',
						),
					),
				),
				'content_bg'                            => array(
					'section'   => 'general',
					'type'      => 'background',
					'label'     => esc_html__( 'Content Background', 'wolmart' ),
					'default'   => '',
					'transport' => 'postMessage',
				),
				// General / Site Content
				'cs_general_content_title'              => array(
					'section' => 'general',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Site Content', 'wolmart' ) . '</h3>',
				),
				'container'                             => array(
					'section'   => 'general',
					'type'      => 'text',
					'label'     => esc_html__( 'Container Width (px)', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'container_fluid'                       => array(
					'section'   => 'general',
					'type'      => 'text',
					'label'     => esc_html__( 'Container Fluid Width (px)', 'wolmart' ),
					'transport' => 'postMessage',
				),
				// Header
				'cs_header_title'                       => array(
					'section' => 'header',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<p style="margin-bottom: 20px; cursor: auto;">' . esc_html__( 'Create your header template and show it in Layout Builder', 'wolmart' ) . '</p>' .
						(
							class_exists( 'Wolmart_Builders' ) ?
							'<a class="button button-primary button-xlarge" href="' . esc_url( admin_url( 'edit.php?post_type=wolmart_template&wolmart_template_type=header' ) ) . '" target="_blank">' . esc_html__( 'Header Builder', 'wolmart' ) . '</a>' :
							'<p>' . esc_html__( 'Please install Wolmart Core Plugin', 'wolmart' ) . '</p>' .
							'<a class="button button-primary button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=default_plugins' ) ) . '" target="_blank">' . esc_html__( 'Install Plugins', 'wolmart' ) . '</a>'
						) .
						'<a class="button button-primary button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=wolmart-layout-builder' ) ) . '" target="_blank">' . esc_html__( 'Layout Builder', 'wolmart' ) . '</a>',
				),
				// Footer
				'cs_footer_title'                       => array(
					'section' => 'footer',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<p style="margin-bottom: 20px; cursor: auto;">' . esc_html__( 'Create your footer template and show it in Layout Builder', 'wolmart' ) . '</p>' .
					(
						class_exists( 'Wolmart_Builders' ) ?
						'<a class="button button-primary button-xlarge" href="' . esc_url( admin_url( 'edit.php?post_type=wolmart_template&wolmart_template_type=footer' ) ) . '" target="_blank">' :
						'<p>' . esc_html__( 'Please install Wolmart Core Plugin', 'wolmart' ) . '</p>' .
							'<a class="button button-primary button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=wolmart-setup-wizard&step=default_plugins' ) ) . '" target="_blank">' . esc_html__( 'Install Plugins', 'wolmart' ) . '</a>'
					) . esc_html__( 'Footer Builder', 'wolmart' ) . '</a><a class="button button-primary button-xlarge" href="' . esc_url( admin_url( 'admin.php?page=wolmart-layout-builder' ) ) . '" target="_blank">' . esc_html__( 'Layout Builder', 'wolmart' ) . '</a>',
				),
				// Style / Color
				'cs_colors_title'                       => array(
					'section' => 'color',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Colors', 'wolmart' ) . '</h3>',
				),
				'primary_color'                         => array(
					'section'   => 'color',
					'type'      => 'color',
					'label'     => esc_html__( 'Primary Color', 'wolmart' ),
					'choices'   => array(
						'alpha' => true,
					),
					'transport' => 'postMessage',
				),
				'secondary_color'                       => array(
					'section'   => 'color',
					'type'      => 'color',
					'label'     => esc_html__( 'Secondary Color', 'wolmart' ),
					'choices'   => array(
						'alpha' => true,
					),
					'transport' => 'postMessage',
				),
				'dark_color'                            => array(
					'section'   => 'color',
					'type'      => 'color',
					'label'     => esc_html__( 'Dark Color', 'wolmart' ),
					'choices'   => array(
						'alpha' => true,
					),
					'transport' => 'postMessage',
				),
				'light_color'                           => array(
					'section'   => 'color',
					'type'      => 'color',
					'label'     => esc_html__( 'Light Color', 'wolmart' ),
					'choices'   => array(
						'alpha' => true,
					),
					'transport' => 'postMessage',
				),
				'cs_skin_title'                         => array(
					'section' => 'color',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Skin', 'wolmart' ) . '</h3>',
				),
				'rounded_skin'                          => array(
					'section' => 'color',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Rounded Skin', 'wolmart' ),
					'tooltip' => esc_html__( 'Enable rounded border skin for banner, posts and so on.', 'wolmart' ),
				),
				// Style / Typography
				'cs_typo_default_font'                  => array(
					'section' => 'typo',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Default Typography', 'wolmart' ) . '</h3>',
				),
				'typo_default'                          => array(
					'section'   => 'typo',
					'type'      => 'typography',
					'label'     => '',
					'transport' => 'postMessage',
				),
				'cs_typo_heading'                       => array(
					'section' => 'typo',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Heading Typography', 'wolmart' ) . '</h3>',
				),
				'typo_heading'                          => array(
					'section'   => 'typo',
					'type'      => 'typography',
					'label'     => '',
					'transport' => 'postMessage',
				),
				'cs_typo_custom_title'                  => array(
					'section' => 'typo',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Custom Google Fonts', 'wolmart' ) . '</h3>',
				),
				'cs_typo_custom_desc'                   => array(
					'section' => 'typo',
					'type'    => 'custom',
					'default' => '<p style="margin: 0;">' . esc_html__( 'Select other google fonts to download', 'wolmart' ) . '</p>',
				),
				'typo_custom_part'                      => array(
					'section'   => 'typo',
					'type'      => 'radio-buttonset',
					'default'   => '1',
					'transport' => 'postMessage',
					'choices'   => array(
						'1' => esc_html__( 'Custom 1', 'wolmart' ),
						'2' => esc_html__( 'Custom 2', 'wolmart' ),
						'3' => esc_html__( 'Custom 3', 'wolmart' ),
					),
				),
				'typo_custom1'                          => array(
					'section'         => 'typo',
					'type'            => 'typography',
					'label'           => esc_html__( 'Custom Font 1', 'wolmart' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'typo_custom_part',
							'operator' => '==',
							'value'    => '1',
						),
					),
				),
				'typo_custom2'                          => array(
					'section'         => 'typo',
					'type'            => 'typography',
					'label'           => esc_html__( 'Custom Font 2', 'wolmart' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'typo_custom_part',
							'operator' => '==',
							'value'    => '2',
						),
					),
				),
				'typo_custom3'                          => array(
					'section'         => 'typo',
					'type'            => 'typography',
					'label'           => esc_html__( 'Custom Font 3', 'wolmart' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'typo_custom_part',
							'operator' => '==',
							'value'    => '3',
						),
					),
				),
				// Style / Title Bar
				'cs_ptb_bar_style_title'                => array(
					'section'   => 'title_bar',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Title Bar Style', 'wolmart' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'ptb_height'                            => array(
					'section'   => 'title_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Title Bar Height (px)', 'wolmart' ),
					'transport' => 'postMessage',
				),

				'ptb_bg'                                => array(
					'section'   => 'title_bar',
					'type'      => 'background',
					'label'     => esc_html__( 'Title Bar Background', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'cs_ptb_typo_title'                     => array(
					'section'   => 'title_bar',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Title Bar Typography', 'wolmart' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'typo_ptb_title'                        => array(
					'section'   => 'title_bar',
					'type'      => 'typography',
					'label'     => esc_html__( 'Page Title', 'wolmart' ),
					'ms_label'  => esc_html__( 'Page Title Font', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'typo_ptb_subtitle'                     => array(
					'section'   => 'title_bar',
					'type'      => 'typography',
					'label'     => esc_html__( 'Page Subtitle', 'wolmart' ),
					'ms_label'  => esc_html__( 'Page Subtitle Font', 'wolmart' ),
					'transport' => 'postMessage',
				),
				// Style / Breadcrumb
				'cs_ptb_breadcrumb_style_title'         => array(
					'section'   => 'breadcrumb',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Breadcrumb', 'wolmart' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'ptb_home_icon'                         => array(
					'section'   => 'breadcrumb',
					'type'      => 'toggle',
					'label'     => esc_html__( 'Show Home Icon', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'ptb_delimiter'                         => array(
					'section'   => 'breadcrumb',
					'type'      => 'text',
					'label'     => esc_html__( 'Breadcrumb Delimiter', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'ptb_delimiter_use_icon'                => array(
					'section'   => 'breadcrumb',
					'type'      => 'checkbox',
					'label'     => esc_html__( 'Use Icon for Delimiter', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'ptb_delimiter_icon'                    => array(
					'section'         => 'breadcrumb',
					'type'            => 'text',
					'label'           => esc_html__( 'Delimiter Icon', 'wolmart' ),
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'ptb_delimiter_use_icon',
							'operator' => '==',
							'value'    => true,
						),
					),
				),
				'typo_ptb_breadcrumb'                   => array(
					'section'   => 'breadcrumb',
					'type'      => 'typography',
					'label'     => esc_html__( 'Breadcrumb Typography', 'wolmart' ),
					'transport' => 'postMessage',
				),
				// Menus / Menu Labels
				'cs_menu_labels_title'                  => array(
					'section' => 'menu_labels',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Menu Labels', 'wolmart' ) . '</h3>',
				),
				'menu_labels'                           => array(
					'section'           => 'menu_labels',
					'type'              => 'text',
					'label'             => esc_html__( 'Menu Labels', 'wolmart' ),
					'transport'         => 'refresh',
					'sanitize_callback' => 'wp_strip_all_tags',
				),
				'cs_menu_labels'                        => array(
					'section' => 'menu_labels',
					'type'    => 'custom',
					'default' => $this->get_edit_menu_label_control(),
				),
				'cs_new_label'                          => array(
					'section' => 'menu_labels',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'New Label', 'wolmart' ) . '</h3>',
				),
				'cs_new_menu_label'                     => array(
					'section' => 'menu_labels',
					'type'    => 'custom',
					'default' => $this->get_new_menu_label_control(),
				),

				'cs_mobile_menu_deprecated_title'       => array(
					'section' => 'mobile_menu_deprecated',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Mobile Menu', 'wolmart' ) . '</h3>',
				),

				'cs_mobile_menu_deprecated_description' => array(
					'section' => 'mobile_menu_deprecated',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<p class="options-custom-desc">' . sprintf( esc_html__( 'Mobile Menu options are moved to %1$sTheme Options > Mobile > Mobile Menu.%2$s', 'wolmart' ), '<br/><code class="wolmart-option-position">', '</code>' ) . '</p>',
				),

				'cs_mobile_menu_deprecated_navigation'  => array(
					'section' => 'mobile_menu_deprecated',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<a href="#" data-target="mobile_menu" data-type="section" class="button button-primary btn-change-label customizer-nav-item">' . esc_html__( 'Go Options', 'wolmart' ) . '</a>',
				),

				'cs_mobile_bar_deprecated_title'        => array(
					'section'   => 'mobile_bar_deprecated',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Mobile Icon Bar Type', 'wolmart' ) . '</h3>',
					'transport' => 'postMessage',
				),

				'cs_mobile_bar_deprecated_description'  => array(
					'section' => 'mobile_bar_deprecated',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<p class="options-custom-desc">' . sprintf( esc_html__( 'Mobile Menu options are moved to %1$sTheme Options > Mobile > Mobile Sticky Bar.%2$s', 'wolmart' ), '<br/><code class="wolmart-option-position">', '</code>' ) . '</p>',
				),

				'cs_mobile_bar_deprecated_navigation'   => array(
					'section' => 'mobile_bar_deprecated',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<a href="#" data-target="mobile_bar" data-type="section" class="button button-primary btn-change-label customizer-nav-item">' . esc_html__( 'Go Options', 'wolmart' ) . '</a>',
				),

				// Mobile / Mobile Menu
				'cs_mobile_menu_title'                  => array(
					'section'   => 'mobile_menu',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Mobile Menu', 'wolmart' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'mobile_menu_items'                     => array(
					'section'   => 'mobile_menu',
					'type'      => 'sortable',
					'label'     => esc_html__( 'Mobile Menus', 'wolmart' ),
					'transport' => 'postMessage',
					'choices'   => $this->get_menus(),
				),
				// Mobile / Mobile Sticky Icon Bar
				'cs_mobile_bar_type'                    => array(
					'section'   => 'mobile_bar',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Mobile Icon Bar Type', 'wolmart' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'mobile_bar_type'                       => array(
					'section' => 'mobile_bar',
					'type'    => 'radio_image',
					'label'   => esc_html__( 'Sticky Bar Type', 'wolmart' ),
					'choices' => array(
						''         => WOLMART_ASSETS . '/images/options/mobile/sticky-icon-none.jpg',
						'bottom'   => WOLMART_ASSETS . '/images/options/mobile/sticky-icon-bottom.jpg',
						'floating' => WOLMART_ASSETS . '/images/options/mobile/sticky-icon-floating.jpg',
					),
				),
				'cs_mobile_floating_button_title'       => array(
					'section'         => 'mobile_bar',
					'type'            => 'custom',
					'label'           => '',
					'default'         => '<h3 class="options-custom-title">' . esc_html__( 'Mobile Floating Button', 'wolmart' ) . '</h3>',
					'transport'       => 'postMessage',
					'active_callback' => array(
						array(
							'setting'  => 'mobile_bar_type',
							'operator' => '==',
							'value'    => 'floating',
						),
					),
				),
				'mobile_floating_button_type'           => array(
					'section'         => 'mobile_bar',
					'type'            => 'radio_image',
					'label'           => esc_html__( 'Button Type', 'wolmart' ),
					'choices'         => array(
						''          => WOLMART_ASSETS . '/images/options/mobile/floating-icon-default.png',
						'plus'      => WOLMART_ASSETS . '/images/options/mobile/floating-icon-plus.png',
						'hamburger' => WOLMART_ASSETS . '/images/options/mobile/floating-icon-hamburger.png',
						'custom'    => WOLMART_ASSETS . '/images/options/mobile/floating-icon-custom.png',
					),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_bar_type',
							'operator' => '==',
							'value'    => 'floating',
						),
					),
				),
				'mobile_floating_button_custom'         => array(
					'section'         => 'mobile_bar',
					'type'            => 'textarea',
					'label'           => esc_html__( 'Custom SVG', 'wolmart' ),
					'active_callback' => array(
						array(
							'setting'  => 'mobile_bar_type',
							'operator' => '==',
							'value'    => 'floating',
						),
						array(
							'setting'  => 'mobile_floating_button_type',
							'operator' => '==',
							'value'    => 'custom',
						),
					),
				),
				'cs_mobile_bar_title'                   => array(
					'section'   => 'mobile_bar',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Mobile Icon Bar', 'wolmart' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'mobile_bar_icons'                      => array(
					'section'   => 'mobile_bar',
					'type'      => 'sortable',
					'label'     => esc_html__( 'Mobile Bar Icons', 'wolmart' ),
					'transport' => 'postMessage',
					'choices'   => array(
						'menu'     => esc_html__( 'Mobile Menu Toggle', 'wolmart' ),
						'home'     => esc_html__( 'Home', 'wolmart' ),
						'shop'     => esc_html__( 'Shop', 'wolmart' ),
						'wishlist' => esc_html__( 'Wishlist', 'wolmart' ),
						'account'  => esc_html__( 'Account', 'wolmart' ),
						'compare'  => esc_html__( 'Compare', 'wolmart' ),
						'cart'     => esc_html__( 'Cart', 'wolmart' ),
						'search'   => esc_html__( 'Search', 'wolmart' ),
						'top'      => esc_html__( 'To Top', 'wolmart' ),
					),
				),
				'mobile_bar_menu_label'                 => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Menu Label', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_menu_icon'                  => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Menu Icon', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_home_label'                 => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Home Label', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_home_icon'                  => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Home Icon', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_shop_label'                 => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Shop Label', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_shop_icon'                  => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Shop Icon', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_wishlist_label'             => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Wishlist Label', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_wishlist_icon'              => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Wishlist Icon', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_account_label'              => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Account Label', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_account_icon'               => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Account Icon', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_cart_label'                 => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Cart Label', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_cart_icon'                  => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Cart Icon', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_search_label'               => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Search Label', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_search_icon'                => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'Search Icon', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_top_label'                  => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'To Top Label', 'wolmart' ),
					'transport' => 'postMessage',
				),
				'mobile_bar_top_icon'                   => array(
					'section'   => 'mobile_bar',
					'type'      => 'text',
					'label'     => esc_html__( 'To Top Icon', 'wolmart' ),
					'transport' => 'postMessage',
				),
				// Mobile / Mobile Language & Currency Switcher
				'mobile_fs_switcher_title'              => array(
					'section'   => 'mobile_bar',
					'type'      => 'custom',
					'label'     => '',
					'default'   => '<h3 class="options-custom-title">' . esc_html__( 'Mobile FullScreen Switcher', 'wolmart' ) . '</h3>',
					'transport' => 'postMessage',
				),
				'mobile_fs_switcher_enable'             => array(
					'section'   => 'mobile_fullscreen_switcher',
					'type'      => 'toggle',
					'label'     => esc_html__( 'Enable FullScreen', 'wolmart' ),
					'transport' => 'postMessage',
					'tooltip'   => esc_html__( 'You can change language & mobile switcher type to fullscreen mode.', 'wolmart' ),
				),
				// Blog / Blog Global / Excerpt
				'cs_post_type'                          => array(
					'section' => 'blog_global',
					'type'    => 'custom',
					'label'   => '<h3 class="options-custom-title">' . esc_html__( 'Post Type', 'wolmart' ) . '</h3>',
				),
				'cs_post_type_alert'                    => array(
					'section' => 'blog_global',
					'type'    => 'custom',
					'label'   => '<p class="options-description">' . esc_html__( 'Layout builder\'s "Blog Layout/Content/Post Type" option is prior than this in blog pages.', 'wolmart' ) . '</p>',
				),
				'post_type'                             => array(
					'section' => 'blog_global',
					'type'    => 'radio_image',
					'label'   => '',
					'choices' => array(
						''     => esc_html__( 'Simple', 'wolmart' ),
						'mask' => esc_html__( 'Mask', 'wolmart' ),
						'list' => esc_html__( 'List', 'wolmart' ),
					),
					'choices' => array(
						''     => WOLMART_ASSETS . '/images/options/post/default.jpg',
						'mask' => WOLMART_ASSETS . '/images/options/post/mask.jpg',
						'list' => WOLMART_ASSETS . '/images/options/post/list.jpg',
					),
				),
				'post_show_date_box'                    => array(
					'section' => 'blog_global',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Show Date Box', 'wolmart' ),
				),
				'post_overlay'                          => array(
					'section' => 'blog_global',
					'type'    => 'select',
					'label'   => esc_html__( 'Hover Effect', 'wolmart' ),
					'choices' => array(
						''           => esc_html__( 'None', 'wolmart' ),
						'light'      => esc_html__( 'Light', 'wolmart' ),
						'dark'       => esc_html__( 'Dark', 'wolmart' ),
						'zoom'       => esc_html__( 'Zoom', 'wolmart' ),
						'zoom_light' => esc_html__( 'Zoom and Light', 'wolmart' ),
						'zoom_dark'  => esc_html__( 'Zoom and Dark', 'wolmart' ),
					),
				),
				'cs_post_excerpt'                       => array(
					'section' => 'blog_global',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Excerpt', 'wolmart' ) . '</h3>',
				),
				'excerpt_type'                          => array(
					'section'  => 'blog_global',
					'type'     => 'radio_buttonset',
					'label'    => esc_html__( 'Type', 'wolmart' ),
					'ms_label' => esc_html__( 'Excerpt Type', 'wolmart' ),
					'choices'  => array(
						''          => esc_html__( 'Word', 'wolmart' ),
						'character' => esc_html__( 'Letter', 'wolmart' ),
					),
				),
				'excerpt_length'                        => array(
					'section'  => 'blog_global',
					'type'     => 'number',
					'label'    => esc_html__( 'Length', 'wolmart' ),
					'ms_label' => esc_html__( 'Excerpt Length', 'wolmart' ),
				),

				// Blog / Blog Page
				'cs_posts_title'                        => array(
					'section' => 'blog_archive',
					'type'    => 'custom',
					'label'   => '<h3 class="options-custom-title">' . esc_html__( 'Blog', 'wolmart' ) . '</h3>',
				),
				'cs_blog_page_alert'                    => array(
					'section' => 'blog_archive',
					'type'    => 'custom',
					'label'   => '<p class="options-description">' . esc_html__( 'Layout builder\'s "Blog Layout/Content/Post Type" option is prior than this in blog pages.', 'wolmart' ) . '</p>',
				),
				'posts_layout'                          => array(
					'section' => 'blog_archive',
					'type'    => 'radio_buttonset',
					'label'   => esc_html__( 'Layout', 'wolmart' ),
					'tooltip' => esc_html__( 'Masonry layout will use uncropped images.', 'wolmart' ),
					'choices' => array(
						'grid'    => esc_html__( 'Grid', 'wolmart' ),
						'masonry' => esc_html__( 'Masonry', 'wolmart' ),
					),
				),
				'posts_column'                          => array(
					'section' => 'blog_archive',
					'type'    => 'number',
					'label'   => esc_html__( 'Column', 'wolmart' ),
					'choices' => array(
						'min' => 1,
						'max' => 8,
					),
				),
				'posts_filter'                          => array(
					'section' => 'blog_archive',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Filter By Category', 'wolmart' ),
				),
				'blog_ajax'                             => array(
					'section' => 'blog_archive',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Enable Ajax Filter', 'wolmart' ),
				),
				'posts_load'                            => array(
					'section' => 'blog_archive',
					'type'    => 'radio_image',
					'label'   => esc_html__( 'Load More', 'wolmart' ),
					'choices' => array(
						'button' => WOLMART_ASSETS . '/images/options/loadmore-btn.png',
						''       => WOLMART_ASSETS . '/images/options/loadmore-page.png',
						'scroll' => WOLMART_ASSETS . '/images/options/loadmore-scroll.png',
					),
				),

				// Blog / Blog Single
				'cs_post_title'                         => array(
					'section' => 'blog_single',
					'type'    => 'custom',
					'label'   => '<h3 class="options-custom-title">' . esc_html__( 'Show Information', 'wolmart' ) . '</h3>',
				),
				'post_show_info'                        => array(
					'section' => 'blog_single',
					'type'    => 'multicheck',
					'label'   => esc_html__( 'Items to show', 'wolmart' ),
					'choices' => array(
						'image'         => esc_html__( 'Media', 'wolmart' ),
						'author'        => esc_html__( 'Meta Author', 'wolmart' ),
						'date'          => esc_html__( 'Meta Date', 'wolmart' ),
						'comment'       => esc_html__( 'Meta Comments Count', 'wolmart' ),
						'category'      => esc_html__( 'Category', 'wolmart' ),
						'tag'           => esc_html__( 'Tags', 'wolmart' ),
						'author_info'   => esc_html__( 'Author Information', 'wolmart' ),
						'share'         => esc_html__( 'Share', 'wolmart' ),
						'navigation'    => esc_html__( 'Prev and Next', 'wolmart' ),
						'related'       => esc_html__( 'Related Posts', 'wolmart' ),
						'comments_list' => esc_html__( 'Comments', 'wolmart' ),
					),
				),
				'cs_post_related_title'                 => array(
					'section' => 'blog_single',
					'type'    => 'custom',
					'label'   => '<h3 class="options-custom-title">' . esc_html__( 'Related Posts', 'wolmart' ) . '</h3>',
				),
				'post_related_count'                    => array(
					'section'  => 'blog_single',
					'type'     => 'number',
					'label'    => esc_html__( 'Count', 'wolmart' ),
					'ms_label' => esc_html__( 'Related Posts Count', 'wolmart' ),
					'choices'  => array(
						'min' => 1,
						'max' => 50,
					),
				),
				'post_related_column'                   => array(
					'section'  => 'blog_single',
					'type'     => 'number',
					'label'    => esc_html__( 'Column', 'wolmart' ),
					'ms_label' => esc_html__( 'Related Posts Column', 'wolmart' ),
					'choices'  => array(
						'min' => 1,
						'max' => 6,
					),
				),
				'post_related_order'                    => array(
					'section'  => 'blog_single',
					'type'     => 'select',
					'label'    => esc_html__( 'Order By', 'wolmart' ),
					'ms_label' => esc_html__( 'Related Posts Order By', 'wolmart' ),
					'choices'  => array(
						''              => esc_html__( 'Default', 'wolmart' ),
						'ID'            => esc_html__( 'ID', 'wolmart' ),
						'title'         => esc_html__( 'Title', 'wolmart' ),
						'date'          => esc_html__( 'Date', 'wolmart' ),
						'modified'      => esc_html__( 'Modified', 'wolmart' ),
						'author'        => esc_html__( 'Author', 'wolmart' ),
						'comment_count' => esc_html__( 'Comment count', 'wolmart' ),
					),
				),
				'posts_related_orderway'                => array(
					'section'  => 'blog_single',
					'type'     => 'radio_buttonset',
					'label'    => esc_html__( 'Order Way', 'wolmart' ),
					'ms_label' => esc_html__( 'Related Posts Order Way', 'wolmart' ),
					'choices'  => array(
						'asc' => esc_html( 'ASC', 'wolmart' ),
						''    => esc_html( 'DESC', 'wolmart' ),
					),
				),
				// Custom CSS, JS
				'custom_css'                            => array(
					'section'   => 'custom_css_js',
					'type'      => 'code',
					'label'     => esc_html__( 'CSS code', 'wolmart' ),
					'transport' => 'postMessage',
					'choices'   => array(
						'language' => 'css',
						'theme'    => 'monokai',
					),
				),

				// Advanced Features
				'cs_lazyload_title'                     => array(
					'section' => 'lazyload',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Lazy Load', 'wolmart' ) . '</h3>',
				),
				'loading_animation'                     => array(
					'section' => 'lazyload',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Loading Overlay', 'wolmart' ),
					'tooltip' => esc_html__( 'Display overlay animation while loading.', 'wolmart' ),
				),
				'skeleton_screen'                       => array(
					'section' => 'lazyload',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Skeleton Screen', 'wolmart' ),
					'tooltip' => esc_html__( 'Display the virtual area of each element on page while loading.', 'wolmart' ),
				),
				'lazyload_menu'                         => array(
					'section' => 'lazyload',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Menu Lazyload', 'wolmart' ),
					'tooltip' => esc_html__( 'Menus will be saved in browsers after lazyload.', 'wolmart' ),
				),
				'lazyload'                              => array(
					'section' => 'lazyload',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Images Lazyload', 'wolmart' ),
					'tooltip' => esc_html__( 'All images will be lazyloaded.', 'wolmart' ),
				),
				'lazyload_bg'                           => array(
					'section'         => 'lazyload',
					'type'            => 'color',
					'label'           => esc_html__( 'Lazyload Image Initial Color', 'wolmart' ),
					'choices'         => array(
						'alpha' => true,
					),
					'active_callback' => array(
						array(
							'setting'  => 'lazyload',
							'operator' => '==',
							'value'    => true,
						),
					),
				),
				'cs_search_title'                       => array(
					'section' => 'search',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Search', 'wolmart' ) . '</h3>',
				),
				'search_post_type'                      => array(
					'section'   => 'search',
					'type'      => 'radio-buttonset',
					'transport' => 'postMessage',
					'label'     => esc_html__( 'Search Post Type', 'wolmart' ),
					'choices'   => array(
						''        => esc_html__( 'All', 'wolmart' ),
						'product' => esc_html__( 'Product', 'wolmart' ),
						'post'    => esc_html__( 'Post', 'wolmart' ),
					),
				),
				'full_screen_search'                    => array(
					'section' => 'search',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Use Full Screen on Mobile', 'wolmart' ),
					'tooltip' => esc_html__( 'The search results will be presented in full screen on the mobile for a user-friendly experience.', 'wolmart' ),
					'default' => false,
				),
				'live_search'                           => array(
					'section' => 'search',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Live Search', 'wolmart' ),
					'tooltip' => esc_html__( 'Search results will be displayed instantly.', 'wolmart' ),
				),
				'live_relevanssi'                       => array(
					'section'         => 'search',
					'type'            => 'toggle',
					'label'           => esc_html__( 'Use Relevanssi for Live Search', 'wolmart' ),
					/* translators: 1. anchor tag open, 2. anchor tag close. */
					'tooltip'         => sprintf( esc_html__( 'You will need to install and activate this %1$splugin%2$s', 'wolmart' ), '<a href="https://ru.wordpress.org/plugins/relevanssi/" target="_blank">', '</a>' ),
					'active_callback' => array(
						array(
							'setting'  => 'live_search',
							'operator' => '!=',
							'value'    => '',
						),
					),
				),
				'cs_ads_title'                          => array(
					'section'         => 'search',
					'type'            => 'custom',
					'default'         => '<h3 class="options-custom-title">' . esc_html__( 'Ads Images on Search', 'wolmart' ) . '</h3>',
					'active_callback' => array(
						array(
							'setting'  => 'full_screen_search',
							'operator' => '==',
							'value'    => true,
						),
					),
					'priority'        => 90,
				),
				'screen_search_banners'                 => array(
					'section'         => 'search',
					'type'            => 'repeater',
					'row_label'       => array(
						'type'  => 'field',
						'value' => esc_attr__( 'Ads Images on Mobile', 'wolmart' ),
						'field' => 'name',
					),
					'fields'          => array(
						'image' => array(
							'type'  => 'image',
							'label' => esc_html__( 'Ads Banner', 'wolmart' ),
						),
						'url'   => array(
							'type'  => 'url',
							'label' => esc_html__( 'Link of Image', 'wolmart' ),
						),
					),
					'active_callback' => array(
						array(
							'setting'  => 'full_screen_search',
							'operator' => '==',
							'value'    => true,
						),
					),
					'priority'        => 90,
				),
				'screen_search_banners_height'          => array(
					'section'         => 'search',
					'type'            => 'number',
					'label'           => esc_html__( 'Banner Height (px)', 'wolmart' ),
					'active_callback' => array(
						array(
							'setting'  => 'full_screen_search',
							'operator' => '==',
							'value'    => true,
						),
					),
					'choices'         => array(
						'min' => 0,
						'max' => 150,
					),
					'priority'        => 95,
				),
				// 'custom_image_size'                     => array(
				// 	'section' => 'images',
				// 	'type'    => 'dimensions',
				// 	'label'   => esc_html__( 'Register Custom Image Size (px)', 'wolmart' ),
				// 	'tooltip' => esc_html__( 'Don\'t forget to regenerate previously uploaded images.', 'wolmart' ),
				// ),
				'cs_image_quality_title'                => array(
					'section' => 'images',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Image Quality and Threshold', 'wolmart' ) . '</h3>',
				),
				'image_quality'                         => array(
					'section' => 'images',
					'type'    => 'number',
					'label'   => esc_html__( 'Image Quality', 'wolmart' ),
					'tooltip' => esc_html__( 'Quality level between 0 (low) and 100 (high) of the JPEG. After changing this value, please install and run the Regenerate Thumbnails plugin once.', 'wolmart' ),
					'choices' => array(
						'min' => 0,
						'max' => 100,
					),
				),
				'big_image_threshold'                   => array(
					'section' => 'images',
					'type'    => 'number',
					'label'   => esc_html__( 'Big Image Size Threshold', 'wolmart' ),
					'tooltip' => esc_html__( 'Threshold for image height and width in pixels. WordPress will scale down newly uploaded images to this values as max-width or max-height. Set to "0" to disable the threshold completely.', 'wolmart' ),
					'choices' => array(
						'min' => 0,
					),
				),
				/**
				 * Custom Image Size
				 */
				'cs_image_size_title'                   => array(
					'section' => 'images',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Custom Image Size', 'wolmart' ) . '</h3>',
				),
				'custom_image_sizes'                    => array(
					'section'   => 'images',
					'type'      => 'repeater',
					'ms_label'  => esc_html__( 'Custom Image Sizes', 'wolmart' ),
					'row_label' => array(
						'type'  => 'field',
						'value' => esc_attr__( 'image size', 'wolmart' ),
						'field' => 'size_name',
					),
					'fields'    => array(
						'size_name' => array(
							'type'  => 'text',
							'label' => esc_html__( 'Image Size Name', 'wolmart' ),
						),
						'width'     => array(
							'type'  => 'number',
							'label' => esc_html__( 'Width (px)', 'wolmart' ),
						),
						'height'    => array(
							'type'  => 'number',
							'label' => esc_html__( 'Height (px)', 'wolmart' ),
						),
					),
				),

				/**
				* Import/Export/Reset Options
				*/
				'cs_import_title'                       => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Import Options', 'wolmart' ) . '</h3>',
				),
				'import_src'                            => array(
					'section'   => 'reset_options',
					'type'      => 'custom',
					'label'     => esc_html__( 'Please select source option file to import', 'wolmart' ),
					'transport' => 'postMessage',
					'default'   => '<input type="file">',
				),
				'cs_import_option'                      => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'default' => '<button name="import" id="wolmart-import-options" class="button button-primary" disabled>' . esc_html__( 'Import', 'wolmart' ) . '</button>',
				),
				'cs_export_title'                       => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Export Options', 'wolmart' ) . '</h3>',
				),
				'cs_export_option'                      => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'default' => '<p>' . esc_html__( 'Export theme options', 'wolmart' ) . '</p><a href="' . esc_url( admin_url( 'admin-ajax.php?action=wolmart_export_theme_options&wp_customize=on&nonce=' . wp_create_nonce( 'wolmart-customizer' ) ) ) . '" name="export" id="wolmart-export-options-btn" class="button button-primary">' . esc_html__( 'Download Theme Options', 'wolmart' ) . '</a>',
				),
				'cs_reset_title'                        => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Reset Options', 'wolmart' ) . '</h3>',
				),
				'cs_reset_option'                       => array(
					'section' => 'reset_options',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<button name="reset" id="wolmart-reset-options" class="button button-primary">' . esc_html__( 'Reset Theme Options', 'wolmart' ) . '</button>',
				),

				/**
				 * SEO / Options
				 */
				'cs_nofollow_title'                     => array(
					'section' => 'seo',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Use by search engines for ranking', 'wolmart' ) . '</h3>',
				),
				'share_link_nofollow'                   => array(
					'section' => 'seo',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Share &amp; Social Links', 'wolmart' ),
				),
				'menu_item_nofollow'                    => array(
					'section' => 'seo',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Mobile Menu Items', 'wolmart' ),
				),

				/**
				* White Label / Options
				*/
				'cs_white_label_title'                  => array(
					'section' => 'white_label',
					'type'    => 'custom',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'White Label', 'wolmart' ) . '</h3>',
				),
				'white_label_title'                     => array(
					'section' => 'white_label',
					'type'    => 'text',
					'label'   => esc_html__( 'White Label', 'wolmart' ),
					'tooltip' => esc_html__( 'Theme name in AdminPanel', 'wolmart' ),
				),
				'white_label_icon'                      => array(
					'section' => 'white_label',
					'type'    => 'image',
					'label'   => esc_html__( 'White Icon', 'wolmart' ),
					'tooltip' => esc_html__( 'Theme icon in Admin Menu and Admin Bar', 'wolmart' ),
				),
				'white_label_logo'                      => array(
					'section' => 'white_label',
					'type'    => 'image',
					'label'   => esc_html__( 'White Logo', 'wolmart' ),
					'tooltip' => esc_html__( 'Theme logo in AdminPanel', 'wolmart' ),
				),

				// Share
				'cs_share_icon_title'                   => array(
					'section' => 'share',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Share Icons', 'wolmart' ) . '</h3>',
				),
				'social_login'                          => array(
					'section' => 'share',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Enable Social Login', 'wolmart' ),
					'tooltip' => esc_html__( 'Please activate Nextend Social Login plugin and enable social links.', 'wolmart' ),
				),
				'share_icons'                           => array(
					'section' => 'share',
					'type'    => 'sortable',
					'label'   => esc_html__( 'Share Icons', 'wolmart' ),
					'choices' => $this->get_social_shares(),
				),
				'cs_share_icon_style_title'             => array(
					'section' => 'share',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'Share Icons Style', 'wolmart' ) . '</h3>',
				),
				'share_use_hover'                       => array(
					'section' => 'share',
					'type'    => 'toggle',
					'label'   => esc_html__( 'Live Hover Color', 'wolmart' ),
				),
				'share_type'                            => array(
					'section' => 'share',
					'type'    => 'radio_image',
					'label'   => esc_html__( 'Share Icon Type', 'wolmart' ),
					'choices' => array(
						''        => WOLMART_ASSETS . '/images/options/share1.png',
						'stacked' => WOLMART_ASSETS . '/images/options/share3.png',
						'framed'  => WOLMART_ASSETS . '/images/options/share2.png',
					),
				),
				// Feature AI field
				'ai_generate_key_title'                 => array(
					'section' => 'ai_generator',
					'type'    => 'custom',
					'label'   => '',
					'default' => '<h3 class="options-custom-title">' . esc_html__( 'OpenAI Key', 'wolmart' ) . '</h3>',
				),
				'ai_generate_key_desc'                  => array(
					'section' => 'ai_generator',
					'type'    => 'custom',
					'label'   => sprintf( __( 'You can get your API Key in your %1$sOpenAI Acount%2$s. We use the gpt-3.5-turbo-instruct model.  You must install %3$sMeta Box%4$s plugin.', 'wolmart' ), '<a href="https://platform.openai.com/account/api-keys" target="_blank">', '</a>', '<b>', '</b>' ),
				),
				'ai_generate_key'                       => array(
					'section' => 'ai_generator',
					'type'    => 'text',
					'label'   => esc_html__( 'Input AI Key', 'wolmart' ),
					'tooltip' => esc_html__( 'You can gerenate the description, excerpt, meta infos and outline for various post type.', 'wolmart' ),
				),
			);

			if ( current_user_can( 'unfiltered_html' ) ) {
				$this->fields['custom_js'] = array(
					'section'   => 'custom_css_js',
					'type'      => 'code',
					'label'     => esc_html__( 'JS code', 'wolmart' ),
					'transport' => 'postMessage',
					'choices'   => array(
						'language' => 'js',
						'theme'    => 'monokai',
					),
				);
			}

			if ( class_exists( 'WooCommerce' ) ) {

				$this->panels = array_merge(
					$this->panels,
					array(
						'shop'        => array(
							'title'    => esc_html__( 'Shop', 'wolmart' ),
							'priority' => 60,
						),
						'vendor'      => array(
							'title'    => esc_html__( 'Vendor', 'wolmart' ),
							'priority' => 70,
						),
						'product'     => array(
							'title'    => esc_html__( 'Product Page', 'wolmart' ),
							'priority' => 80,
						),
						'woocommerce' => array(
							'title'    => esc_html__( 'WooCommerce', 'wolmart' ),
							'priority' => 90,
						),
					)
				);

				$this->sections = array_merge(
					$this->sections,
					array(

						// Shop
						'products_archive'     => array(
							'title'    => esc_html__( 'Shop Page', 'wolmart' ),
							'panel'    => 'shop',
							'priority' => 20,
						),
						'product_type'         => array(
							'title'    => esc_html__( 'Product Type', 'wolmart' ),
							'panel'    => 'shop',
							'priority' => 30,
						),
						'category_type'        => array(
							'title'    => esc_html__( 'Category Type', 'wolmart' ),
							'panel'    => 'shop',
							'priority' => 40,
						),
						'quickview'            => array(
							'title'    => esc_html__( 'Quickview', 'wolmart' ),
							'panel'    => 'shop',
							'priority' => 50,
						),
						'compare'              => array(
							'title'    => esc_html__( 'Compare', 'wolmart' ),
							'panel'    => 'shop',
							'priority' => 60,
						),
						'shop_pro'             => array(
							'title'    => esc_html__( 'Advanced', 'wolmart' ),
							'panel'    => 'shop',
							'priority' => 90,
						),
						'cookie_law_info'      => array(
							'title'    => esc_html__( 'Privacy Consent Setting', 'wolmart' ),
							'panel'    => 'shop',
							'priority' => 100,
						),
						// Vendor
						'vendor_store'         => array(
							'title'    => esc_html__( 'Vendor Store', 'wolmart' ),
							'panel'    => 'vendor',
							'priority' => 10,
						),
						'vendor_style'         => array(
							'title'    => esc_html__( 'Vendor Style', 'wolmart' ),
							'panel'    => 'vendor',
							'priority' => 20,
						),
						// Product
						'product_layout'       => array(
							'title'    => esc_html__( 'Product Layout', 'wolmart' ),
							'panel'    => 'product',
							'priority' => 10,
						),
						'product_data'         => array(
							'title'    => esc_html__( 'Product Data', 'wolmart' ),
							'panel'    => 'product',
							'priority' => 20,
						),
						'product_reviews'      => array(
							'title'    => esc_html__( 'Product Reviews', 'wolmart' ),
							'panel'    => 'product',
							'priority' => 30,
						),
						'product_related'      => array(
							'title'    => esc_html__( 'Related Products', 'wolmart' ),
							'panel'    => 'product',
							'priority' => 40,
						),
						'product_upsells'      => array(
							'title'    => esc_html__( 'Up-Sells Products', 'wolmart' ),
							'panel'    => 'product',
							'priority' => 40,
						),
						'product_fbt'          => array(
							'title'    => esc_html__( 'Frequently Bought Together', 'wolmart' ),
							'panel'    => 'product',
							'priority' => 50,
						),
						// WooCommerce Panel
						'wc_cart'              => array(
							'title'    => esc_html__( 'Cart Page', 'wolmart' ),
							'panel'    => 'woocommerce',
							'priority' => 20,
						),
						'wc_freeshipping'      => array(
							'title'    => esc_html__( 'Free Shipping Bar', 'wolmart' ),
							'panel'    => 'woocommerce',
							'priority' => 20,
						),
						'woocommerce_checkout' => array(
							'title'    => esc_html__( 'Checkout', 'wolmart' ),
							'panel'    => 'woocommerce',
							'priority' => 20,
						),
					)
				);

				$this->fields = array_merge(
					$this->fields,
					array(

						// Shop / Products Archive
						'cs_products_grid'                => array(
							'section' => 'products_archive',
							'type'    => 'custom',
							'label'   => '<h3 class="options-custom-title">' . esc_html__( 'Shop Products', 'wolmart' ) . '</h3>',
						),
						'cs_shop_page_alert'              => array(
							'section' => 'products_archive',
							'type'    => 'custom',
							'label'   => '<p class="options-description">' . esc_html__( 'Layout builder\'s "Shop Layout/Content/Options" is prior than this theme options in shop page.', 'wolmart' ) . '</p>',
						),
						'products_column'                 => array(
							'section' => 'products_archive',
							'type'    => 'number',
							'label'   => esc_html__( 'Column', 'wolmart' ),
							'choices' => array(
								'min' => 1,
								'max' => 8,
							),
						),
						'products_gap'                    => array(
							'section' => 'products_archive',
							'type'    => 'radio_buttonset',
							'label'   => esc_html__( 'Gap Size', 'wolmart' ),
							'tooltip' => esc_html__( 'Choose gap size between products', 'wolmart' ),
							'choices' => array(
								'no' => esc_html__( 'No', 'wolmart' ),
								'xs' => esc_html__( 'XS', 'wolmart' ),
								'sm' => esc_html__( 'S', 'wolmart' ),
								''   => esc_html__( 'M', 'wolmart' ),
								'lg' => esc_html__( 'L', 'wolmart' ),
							),
						),
						'products_load'                   => array(
							'section' => 'products_archive',
							'type'    => 'radio_image',
							'label'   => esc_html__( 'Load More', 'wolmart' ),
							'choices' => array(
								'button' => WOLMART_ASSETS . '/images/options/loadmore-btn.png',
								''       => WOLMART_ASSETS . '/images/options/loadmore-page.png',
								'scroll' => WOLMART_ASSETS . '/images/options/loadmore-scroll.png',
							),
						),
						'products_load_label'             => array(
							'section'         => 'products_archive',
							'type'            => 'text',
							'label'           => esc_html__( 'Load Button Label', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'products_load',
									'operator' => '==',
									'value'    => 'button',
								),
							),
						),
						'products_count_select'           => array(
							'section' => 'products_archive',
							'type'    => 'text',
							'label'   => esc_html__( 'Products Count Select', 'wolmart' ),
							'tooltip' => esc_html__( 'Input numbers of count select box(9, _12, 24, 36).', 'wolmart' ),
						),
						// Shop / Product Type
						'cs_product_type_title'           => array(
							'section' => 'product_type',
							'type'    => 'custom',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Product Type', 'wolmart' ) . '</h3>',
						),
						'product_type'                    => array(
							'section' => 'product_type',
							'type'    => 'radio_image',
							'label'   => esc_html__( 'Product Type', 'wolmart' ),
							'choices' => array(
								''           => WOLMART_ASSETS . '/images/options/products/product-1.jpg',
								'product-2'  => WOLMART_ASSETS . '/images/options/products/product-2.jpg',
								'product-3'  => WOLMART_ASSETS . '/images/options/products/product-3.jpg',
								'product-4'  => WOLMART_ASSETS . '/images/options/products/product-4.jpg',
								'product-5'  => WOLMART_ASSETS . '/images/options/products/product-5.jpg',
								'product-6'  => WOLMART_ASSETS . '/images/options/products/product-6.jpg',
								'product-8'  => WOLMART_ASSETS . '/images/options/products/product-8.jpg',
								'product-7'  => WOLMART_ASSETS . '/images/options/products/product-7.jpg',
								'product-9'  => WOLMART_ASSETS . '/images/options/products/product-9.jpg',
								'product-10' => WOLMART_ASSETS . '/images/options/products/product-10.jpg',
								'product-11' => WOLMART_ASSETS . '/images/options/products/product-11.jpg',
							),
						),
						'show_in_box'                     => array(
							'section' => 'product_type',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Show In Box', 'wolmart' ),
						),
						'show_hover_shadow'               => array(
							'section' => 'product_type',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Box shadow on Hover', 'wolmart' ),
						),
						'show_media_shadow'               => array(
							'section' => 'product_type',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Media Shadow Effect on Hover', 'wolmart' ),
						),
						'hover_change'                    => array(
							'section' => 'product_type',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Change Image on Hover', 'wolmart' ),
						),
						'hover_style'                     => array(
							'section'         => 'product_type',
							'type'            => 'radio_image',
							'label'           => esc_html__( 'Hover Style', 'wolmart' ),
							'choices'         => array(
								'image-hover'       => WOLMART_ASSETS . '/images/options/products/hover-1.jpg',
								'slider'            => WOLMART_ASSETS . '/images/options/products/hover-2.jpg',
								'multi-image-hover' => WOLMART_ASSETS . '/images/options/products/hover-3.jpg',
							),
							'active_callback' => array(
								array(
									'setting'  => 'hover_change',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
						'show_progress'                   => array(
							'section'         => 'product_type',
							'type'            => 'toggle',
							'label'           => esc_html__( 'Show Sales Bar', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'product_type',
									'operator' => 'not in',
									'value'    => array( 'product-2', 'product-3' ),
								),
							),
						),
						'prod_open_click_mob'             => array(
							'section' => 'product_type',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Open product on second click on mobile', 'wolmart' ),
						),
						'cs_product_show_info'            => array(
							'section' => 'product_type',
							'type'    => 'custom',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Show Information', 'wolmart' ) . '</h3>',
						),
						'show_info'                       => array(
							'section' => 'product_type',
							'type'    => 'multicheck',
							'label'   => esc_html__( 'Items to show', 'wolmart' ),
							'tooltip' => esc_html__( 'This option works only when shop catalog mode is enabled.', 'wolmart' ),
							'choices' => array(
								'category'  => esc_html__( 'Category', 'wolmart' ),
								'label'     => esc_html__( 'Label', 'wolmart' ),
								'price'     => esc_html__( 'Price', 'wolmart' ),
								'sold_by'   => esc_html__( 'Sold By', 'wolmart' ),
								'rating'    => esc_html__( 'Rating', 'wolmart' ),
								'attribute' => esc_html__( 'Attribute Swatches', 'wolmart' ),
								'addtocart' => esc_html__( 'Add To Cart', 'wolmart' ),
								'compare'   => esc_html__( 'Compare', 'wolmart' ),
								'quickview' => esc_html__( 'Quickview', 'wolmart' ),
								'wishlist'  => esc_html__( 'Wishlist', 'wolmart' ),
							),
						),
						'sold_by_label'                   => array(
							'section'         => 'product_type',
							'type'            => 'text',
							'label'           => esc_html__( 'Sold by Label', 'wolmart' ),
							'default'         => esc_html__( 'Sold By', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'show_info',
									'operator' => 'in',
									'value'    => array( 'sold_by' ),
								),
							),
						),
						'cs_product_title'                => array(
							'section' => 'product_type',
							'type'    => 'custom',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Product Title', 'wolmart' ) . '</h3>',
						),
						'prod_title_clamp'                => array(
							'section' => 'product_type',
							'type'    => 'number',
							'label'   => esc_html__( 'Line Clamp', 'wolmart' ),
							'choices' => array(
								'min' => 1,
								'max' => 5,
							),
						),
						'cs_product_excerpt'              => array(
							'section' => 'product_type',
							'type'    => 'custom',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Product Excerpt', 'wolmart' ) . '</h3>',
						),
						'prod_excerpt_type'               => array(
							'section'  => 'product_type',
							'type'     => 'radio_buttonset',
							'label'    => esc_html__( 'Type', 'wolmart' ),
							'ms_label' => esc_html__( 'Excerpt Type', 'wolmart' ),
							'choices'  => array(
								''          => esc_html__( 'Word', 'wolmart' ),
								'character' => esc_html__( 'Letter', 'wolmart' ),
							),
						),
						'prod_excerpt_length'             => array(
							'section'  => 'product_type',
							'type'     => 'number',
							'label'    => esc_html__( 'Length', 'wolmart' ),
							'ms_label' => esc_html__( 'Excerpt Length', 'wolmart' ),
						),
						// Shop / Category Type
						'cs_category_type_title'          => array(
							'section' => 'category_type',
							'type'    => 'custom',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Category Type', 'wolmart' ) . ' </h3>',
						),
						'category_type'                   => array(
							'section' => 'category_type',
							'type'    => 'radio-image',
							'label'   => esc_html__( 'Category Type', 'wolmart' ),
							'choices' => array(
								''          => WOLMART_ASSETS . '/images/options/categories/category-1.jpg',
								'frame'     => WOLMART_ASSETS . '/images/options/categories/category-2.jpg',
								'banner'    => WOLMART_ASSETS . '/images/options/categories/category-3.jpg',
								'simple'    => WOLMART_ASSETS . '/images/options/categories/category-4.jpg',
								'icon'      => WOLMART_ASSETS . '/images/options/categories/category-5.jpg',
								'classic'   => WOLMART_ASSETS . '/images/options/categories/category-6.jpg',
								'classic-2' => WOLMART_ASSETS . '/images/options/categories/category-7.jpg',
								'ellipse'   => WOLMART_ASSETS . '/images/options/categories/category-8.jpg',
								'ellipse-2' => WOLMART_ASSETS . '/images/options/categories/category-9.jpg',
								'group'     => WOLMART_ASSETS . '/images/options/categories/category-10.jpg',
								'group-2'   => WOLMART_ASSETS . '/images/options/categories/category-11.jpg',
								'label'     => WOLMART_ASSETS . '/images/options/categories/category-12.jpg',
							),
						),
						'subcat_cnt'                      => array(
							'section'         => 'category_type',
							'type'            => 'text',
							'label'           => esc_html__( 'Subcategory Count', 'wolmart' ),
							'transport'       => 'refresh',
							'active_callback' => array(
								array(
									'setting'  => 'category_type',
									'operator' => 'in',
									'value'    => array( 'group', 'group-2' ),
								),
							),
						),
						'category_show_icon'              => array(
							'section'         => 'category_type',
							'type'            => 'toggle',
							'label'           => esc_html__( 'Show Icon', 'wolmart' ),
							'transport'       => 'refresh',
							'active_callback' => array(
								array(
									'setting'  => 'category_type',
									'operator' => 'in',
									'value'    => array( 'icon', 'group', 'group-2' ),
								),
							),
						),
						'category_overlay'                => array(
							'section'         => 'category_type',
							'type'            => 'select',
							'label'           => esc_html__( 'Hover Effect', 'wolmart' ),
							'choices'         => array(
								'no'         => esc_html__( 'None', 'wolmart' ),
								'light'      => esc_html__( 'Light', 'wolmart' ),
								'dark'       => esc_html__( 'Dark', 'wolmart' ),
								'zoom'       => esc_html__( 'Zoom', 'wolmart' ),
								'zoom_light' => esc_html__( 'Zoom and Light', 'wolmart' ),
								'zoom_dark'  => esc_html__( 'Zoom and Dark', 'wolmart' ),
							),
							'active_callback' => array(
								array(
									'setting'  => 'category_show_icon',
									'operator' => '==',
									'value'    => '',
								),
							),
						),
						// Shop / Advanced
						'cs_shop_pro_advanced'            => array(
							'section' => 'shop_pro',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Advanced Features', 'wolmart' ) . '</h3>',
						),
						'shop_ajax'                       => array(
							'type'    => 'toggle',
							'label'   => esc_html__( 'Enable Ajax Filter', 'wolmart' ),
							'section' => 'shop_pro',
						),
						'image_swatch'                    => array(
							'type'    => 'toggle',
							'label'   => esc_html__( 'Enable Image Swatch', 'wolmart' ),
							'section' => 'shop_pro',
						),
						'disable_ajax_account'            => array(
							'type'        => 'toggle',
							'label'       => esc_html__( 'Disable Ajax Login/Register on the Header', 'wolmart' ),
							'section'     => 'shop_pro',
							'description' => esc_html__( 'If you set, the account directs to My Account page.', 'wolmart' ),
						),
						'enable_price_chart'              => array(
							'type'        => 'toggle',
							'label'       => esc_html__( 'Show chart on the Price Filter widget', 'wolmart' ),
							'section'     => 'shop_pro',
							'description' => esc_html__( 'If you use \'Filter Products by Price\' widget, price chart will be shown with the price range.', 'wolmart' ),
						),
						'auto_close_mobile_filter'        => array(
							'type'        => 'toggle',
							'label'       => esc_html__( 'Auto Close Filter on Mobile', 'wolmart' ),
							'section'     => 'shop_pro',
							'description' => esc_html__( 'Enable auto close after selecting filter item on mobile.', 'wolmart' ),
						),
						'cart_popup_type'                 => array(
							'section' => 'shop_pro',
							'type'    => 'radio-image',
							'label'   => esc_html__( 'Cart Popup Type', 'wolmart' ),
							'choices' => array(
								'mini-popup' => WOLMART_ASSETS . '/images/options/mini-popup.jpg',
								'cart-popup' => WOLMART_ASSETS . '/images/options/cart-popup.jpg',
							),
						),
						'new_product_period'              => array(
							'type'    => 'number',
							'label'   => esc_html__( 'New Product Period', 'wolmart' ),
							'tooltip' => esc_html__( 'How many days to show new label for new products.', 'wolmart' ),
							'section' => 'shop_notice',
						),
						'cs_quickview_title'              => array(
							'section' => 'quickview',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Quickview', 'wolmart' ) . '</h3>',
						),
						'quickview_type'                  => array(
							'section' => 'quickview',
							'type'    => 'radio-image',
							'label'   => esc_html__( 'Quickview Type', 'wolmart' ),
							'choices' => array(
								''          => WOLMART_ASSETS . '/images/options/quickview-popup.jpg',
								'zoom'      => WOLMART_ASSETS . '/images/options/quickview-zoom.jpg',
								'offcanvas' => WOLMART_ASSETS . '/images/options/quickview-offcanvas.jpg',
							),
						),
						'quickview_thumbs'                => array(
							'section'         => 'quickview',
							'type'            => 'radio-image',
							'label'           => esc_html__( 'Thumbnails Position', 'wolmart' ),
							'choices'         => array(
								'vertical'   => WOLMART_ASSETS . '/images/options/quickview1.png',
								'horizontal' => WOLMART_ASSETS . '/images/options/quickview2.png',
							),
							'active_callback' => array(
								array(
									'setting'  => 'quickview_type',
									'operator' => '!=',
									'value'    => 'offcanvas',
								),
							),
						),
						'cs_compare_advanced'             => array(
							'section' => 'compare',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Compare', 'wolmart' ) . '</h3>',
						),
						'compare_available'               => array(
							'section' => 'compare',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Enable Products Compare', 'wolmart' ),
						),
						'compare_popup_type'              => array(
							'section'   => 'compare',
							'type'      => 'radio_buttonset',
							'label'     => esc_html__( 'Compare Popup Type', 'wolmart' ),
							'transport' => 'postMessage',
							'choices'   => array(
								'mini_popup' => esc_html__( 'Mini Popup', 'wolmart' ),
								'offcanvas'  => esc_html__( 'Off Canvas', 'wolmart' ),
							),
						),
						'compare_limit'                   => array(
							'section'         => 'compare',
							'type'            => 'number',
							'label'           => esc_html__( 'Products Max Count', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'compare_available',
									'operator' => '==',
									'value'    => true,
								),
							),
							'transport'       => 'refresh',
						),
						// Cookie Law Options
						'cs_cookie_law_title'             => array(
							'section' => 'cookie_law_info',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Privacy Consent Setting', 'wolmart' ) . '</h3>',
						),
						'show_cookie_info'                => array(
							'section' => 'cookie_law_info',
							'label'   => esc_html__( 'Show Privacy Consent Info Bar', 'wolmart' ),
							'tooltip' => esc_html__( 'Under GDPR(General Data Protection Regulation), websites must make it clear to visitors who are from EU to control over their personal data that is being store by website. This includes specifically includes cookie', 'wolmart' ),
							'type'    => 'toggle',
						),
						'cookie_text'                     => array(
							'section'     => 'cookie_law_info',
							'label'       => esc_html__( 'Content', 'wolmart' ),
							'description' => esc_html__( 'Place some text here for cookie usage', 'wolmart' ),
							'type'        => 'textarea',
						),
						'choose_cookie_page'              => array(
							'section' => 'cookie_law_info',
							'label'   => esc_html__( 'Choose Privacy Policy Page', 'wolmart' ),
							'tooltip' => esc_html__( 'Choose the page that will contain your privacy policy', 'wolmart' ),
							'type'    => 'select',
							'choices' => wolmart_get_pages_arr(),
						),
						'cookie_version'                  => array(
							'section' => 'cookie_law_info',
							'label'   => esc_html__( 'Cookie Version', 'wolmart' ),
							'type'    => 'text',
						),
						'cookie_agree_btn'                => array(
							'section' => 'cookie_law_info',
							'label'   => esc_html__( 'Privacy Agreement Button Label', 'wolmart' ),
							'type'    => 'text',
						),
						'cookie_decline_btn'              => array(
							'section' => 'cookie_law_info',
							'label'   => esc_html__( 'Privacy Declinature Button Label', 'wolmart' ),
							'type'    => 'text',
						),
						// Vendor / Vendor Store Page
						'cs_vendor_products_title'        => array(
							'section' => 'vendor_store',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Vendor Store Page', 'wolmart' ) . '</h3>',
						),
						'vendor_products_column'          => array(
							'section' => 'vendor_store',
							'type'    => 'number',
							'label'   => esc_html__( 'Products Column', 'wolmart' ),
							'choices' => array(
								'min' => 1,
								'max' => 6,
							),
						),
						// Vendor / Vendor Style
						'cs_vendor_dashboard_style_title' => array(
							'section' => 'vendor_style',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Vendor Dashboard', 'wolmart' ) . '</h3>',
						),
						'vendor_style_option'             => array(
							'section'  => 'vendor_style',
							'type'     => 'radio_buttonset',
							'label'    => esc_html__( 'Style', 'wolmart' ),
							'ms_label' => esc_html__( 'Dashboard Style', 'wolmart' ),
							'tooltip'  => esc_html__( 'Choose style for vendor pages', 'wolmart' ),
							'default'  => 'theme',
							'choices'  => array(
								'theme'  => esc_html__( 'Theme Style', 'wolmart' ),
								'plugin' => esc_html__( 'Plugin Style', 'wolmart' ),
							),
						),

						// Vendor / Sold By Style
						'cs_vendor_sold_by_style'         => array(
							'section' => 'vendor_style',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Vendor Sold By Template', 'wolmart' ) . '</h3>',
						),
						'vendor_soldby_style_option'      => array(
							'section'  => 'vendor_style',
							'type'     => 'radio_buttonset',
							'label'    => esc_html__( 'Style', 'wolmart' ),
							'ms_label' => esc_html__( 'Sold By Template Style', 'wolmart' ),
							'tooltip'  => esc_html__( 'Choose style of sold by template', 'wolmart' ),
							'default'  => 'theme',
							'choices'  => array(
								'theme'  => esc_html__( 'Theme Style', 'wolmart' ),
								'plugin' => esc_html__( 'Plugin Style', 'wolmart' ),
							),
						),

						// Product Page / Product Layout
						'cs_product_layout'               => array(
							'section' => 'product_layout',
							'type'    => 'custom',
							'label'   => '<h3 class="options-custom-title">' . esc_html__( 'Product Layout', 'wolmart' ) . '</h3>',
						),
						'single_product_type'             => array(
							'section' => 'product_layout',
							'type'    => 'select',
							'label'   => esc_html__( 'Single Product Layout', 'wolmart' ),
							'choices' => array(
								''              => esc_html__( 'Horizontal Thumbs', 'wolmart' ),
								'vertical'      => esc_html__( 'Vertical Thumbs', 'wolmart' ),
								'grid'          => esc_html__( 'Grid Images', 'wolmart' ),
								'masonry'       => esc_html__( 'Masonry', 'wolmart' ),
								'gallery'       => esc_html__( 'Gallery', 'wolmart' ),
								'sticky-info'   => esc_html__( 'Sticky Information', 'wolmart' ),
								'sticky-thumbs' => esc_html__( 'Sticky Thumbs', 'wolmart' ),
								'sticky-both'   => esc_html__( 'Left &amp; Right Sticky', 'wolmart' ),
							),
							'tooltip' => esc_html__( 'Layout builder\'s "Product Detail Layout/Content/Single Product Type" option is prior than this.', 'wolmart' ),
						),
						'single_product_sticky'           => array(
							'section' => 'product_layout',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Add To Cart Sticky', 'wolmart' ),
						),
						'single_product_sticky_mobile'    => array(
							'section'         => 'product_layout',
							'type'            => 'toggle',
							'label'           => esc_html__( 'Add To Cart Sticky Mobile', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'single_product_sticky',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
						'same_vendor_products'            => array(
							'section'         => 'product_layout',
							'type'            => 'toggle',
							'label'           => esc_html__( 'Show Same Vendor Products', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'single_product_type',
									'operator' => '!=',
									'value'    => 'sticky-both',
								),
							),

						),

						// Product Page / Product Data / Custom Tab
						'cs_product_data'                 => array(
							'section' => 'product_data',
							'type'    => 'custom',
							'label'   => '<h3 class="options-custom-title">' . esc_html__( 'Product Data Type', 'wolmart' ) . '</h3>',
						),
						'product_data_type'               => array(
							'section' => 'product_data',
							'type'    => 'radio_buttonset',
							'label'   => esc_html__( 'Product Data Type', 'wolmart' ),
							'choices' => array(
								'tab'       => esc_html__( 'Tab', 'wolmart' ),
								'accordion' => esc_html__( 'Accordion', 'wolmart' ),
								'section'   => esc_html__( 'Section', 'wolmart' ),
							),
						),
						'product_description_title'       => array(
							'section' => 'product_data',
							'type'    => 'text',
							'label'   => esc_html__( 'Description Title', 'wolmart' ),
						),
						'product_specification_title'     => array(
							'section'     => 'product_data',
							'type'        => 'text',
							'label'       => esc_html__( 'Specification Title', 'wolmart' ),
							'placeholder' => esc_html__( 'Specification', 'wolmart' ),
						),
						'product_reviews_title'           => array(
							'section'     => 'product_data',
							'type'        => 'text',
							'label'       => esc_html__( 'Reviews Title', 'wolmart' ),
							'placeholder' => esc_html__( 'Customer Reviews', 'wolmart' ),
						),

						// Product Page / Product Data / Vendor Info Tab
						'cs_product_vendor_tab'           => array(
							'section' => 'product_data',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Vendor Info Tab', 'wolmart' ) . '</h3>',
						),
						'product_hide_vendor_tab'         => array(
							'section' => 'product_data',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Hide Vendor Info Tab', 'wolmart' ),
						),
						'product_vendor_info_title'       => array(
							'section'         => 'product_data',
							'type'            => 'text',
							'label'           => esc_html__( 'Vendor Info Title', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'product_hide_vendor_tab',
									'operator' => '!=',
									'value'    => true,
								),
							),
						),

						// Product Page / Product Data / Custom Tab
						'cs_product_custom_tab'           => array(
							'section' => 'product_data',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Custom Tab', 'wolmart' ) . '</h3>',
						),
						'product_tab_title'               => array(
							'section' => 'product_data',
							'type'    => 'text',
							'label'   => esc_html__( 'Custom Tab Title', 'wolmart' ),
							'tooltip' => esc_html__( 'Show custom tab in all product pages.', 'wolmart' ),
						),
						'product_tab_block'               => array(
							'section' => 'product_data',
							'type'    => 'select',
							'label'   => esc_html__( 'Custom Tab Content', 'wolmart' ),
							'choices' => empty( $GLOBALS['wolmart_templates']['block'] ) ? array() : $GLOBALS['wolmart_templates']['block'],
						),

						// Product Page / Product Reviews
						'cs_product_reviews'              => array(
							'section' => 'product_reviews',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Product Review Images', 'wolmart' ) . '</h3>',
						),
						'product_review_image_count'      => array(
							'section' => 'product_reviews',
							'type'    => 'number',
							'label'   => esc_html__( 'Maximum Image Count per Review', 'wolmart' ),
							'choices' => array(
								'min' => 0,
								'max' => 10,
							),
						),
						'product_review_image_size'       => array(
							'section' => 'product_reviews',
							'type'    => 'number',
							'label'   => esc_html__( 'Maximum Upload File Size (MB)', 'wolmart' ),
							'tooltip' => sprintf( esc_html__( 'Set the value in megabytes. Currently your server allows you to upload files up to %s.', 'wolmart' ), size_format( wp_max_upload_size() ) ), //phpcs:ignore
							'choices' => array(
								'min' => 0,
								'max' => size_format( wp_max_upload_size() ),
							),
						),

						// Product Page / Related Products
						'cs_product_related'              => array(
							'section' => 'product_related',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Related Products', 'wolmart' ) . '</h3>',
						),
						'product_related_title'           => array(
							'section' => 'product_related',
							'type'    => 'text',
							'label'   => esc_html__( 'Title', 'wolmart' ),
						),
						'product_related_count'           => array(
							'section' => 'product_related',
							'type'    => 'number',
							'label'   => esc_html__( 'Count', 'wolmart' ),
							'choices' => array(
								'min' => 1,
								'max' => 50,
							),
						),
						'product_related_column'          => array(
							'section' => 'product_related',
							'type'    => 'number',
							'label'   => esc_html__( 'Column', 'wolmart' ),
							'choices' => array(
								'min' => 1,
								'max' => 6,
							),
						),
						'product_related_order'           => array(
							'section' => 'product_related',
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
						),
						'product_related_orderway'        => array(
							'section' => 'product_related',
							'type'    => 'radio_buttonset',
							'label'   => esc_html__( 'Order Way', 'wolmart' ),
							'choices' => array(
								'asc' => esc_html( 'ASC', 'wolmart' ),
								''    => esc_html( 'DESC', 'wolmart' ),
							),
						),
						// Product Page / Up-Sells Products
						'cs_product_upsells'              => array(
							'section' => 'product_upsells',
							'type'    => 'custom',
							'label'   => '',
							'default' => '<h3 class="options-custom-title">' . esc_html__( 'Up-Sells Products', 'wolmart' ) . '</h3>',
						),
						'product_upsells_title'           => array(
							'section' => 'product_upsells',
							'type'    => 'text',
							'label'   => esc_html__( 'Title', 'wolmart' ),
						),
						'product_upsells_count'           => array(
							'section' => 'product_upsells',
							'type'    => 'number',
							'label'   => esc_html__( 'Count', 'wolmart' ),
							'choices' => array(
								'min' => 1,
								'max' => 50,
							),
						),
						'product_upsells_order'           => array(
							'section' => 'product_upsells',
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
						),
						'product_upsells_orderway'        => array(
							'section' => 'product_upsells',
							'type'    => 'radio_buttonset',
							'label'   => esc_html__( 'Order Way', 'wolmart' ),
							'choices' => array(
								'asc' => esc_html__( 'ASC', 'wolmart' ),
								''    => esc_html__( 'DESC', 'wolmart' ),
							),
						),
						// Product Page / Frequently Bought Together
						'product_fbt'                     => array(
							'section' => 'product_fbt',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Show Frequently Bought Together', 'wolmart' ),
							'default' => 1,
						),
						'product_fbt_title'               => array(
							'section' => 'product_fbt',
							'type'    => 'text',
							'label'   => esc_html__( 'Frequently Bought Together Title', 'wolmart' ),
							'default' => esc_html__( 'Frequently Bought Together', 'wolmart' ),
						),
						// WooCommerce Panel
						'cart_show_clear'                 => array(
							'section' => 'wc_cart',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Show Clear Button', 'wolmart' ),
							'tooltip' => esc_html__( 'Show clear cart button on cart page.', 'wolmart' ),
						),
						'cart_auto_update'                => array(
							'section' => 'wc_cart',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Auto Update Quantity', 'wolmart' ),
							'tooltip' => esc_html__( 'Automatically update on quantity change.', 'wolmart' ),
						),
						'show_freeshipping_bar'           => array(
							'section' => 'wc_freeshipping',
							'type'    => 'toggle',
							'label'   => esc_html__( 'Show Free Shipping Bar', 'wolmart' ),
						),
						'fb_location'                     => array(
							'section'         => 'wc_freeshipping',
							'type'            => 'custom',
							'default'         => '<h3 class="options-custom-title">' . esc_html__( 'Location', 'wolmart' ) . '</h3>',
							'active_callback' => array(
								array(
									'setting'  => 'show_freeshipping_bar',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
						'freeshipping_sp'                 => array(
							'section'         => 'wc_freeshipping',
							'type'            => 'toggle',
							'label'           => esc_html__( 'Show on Single Product', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'show_freeshipping_bar',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
						'freeshipping_cart'               => array(
							'section'         => 'wc_freeshipping',
							'type'            => 'toggle',
							'label'           => esc_html__( 'Show on Cart Page', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'show_freeshipping_bar',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
						'freeshipping_checkout'           => array(
							'section'         => 'wc_freeshipping',
							'type'            => 'toggle',
							'label'           => esc_html__( 'Show on Checkout Page', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'show_freeshipping_bar',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
						'freeshipping_minicart'           => array(
							'section'         => 'wc_freeshipping',
							'type'            => 'toggle',
							'label'           => esc_html__( 'Show on Offcanvas/Dropdown', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'show_freeshipping_bar',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
						'fs_message'                      => array(
							'section'         => 'wc_freeshipping',
							'type'            => 'custom',
							'default'         => '<h3 class="options-custom-title">' . esc_html__( 'Message', 'wolmart' ) . '</h3>',
							'active_callback' => array(
								array(
									'setting'  => 'show_freeshipping_bar',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
						'freeshipping_initial'            => array(
							'section'         => 'wc_freeshipping',
							'type'            => 'textarea',
							'label'           => esc_html__( 'Initial Message', 'wolmart' ),
							'default'         => sprintf( esc_html__( '%1$s [remainder] %2$s', 'wolmart' ), 'Add', 'to cart and get free shipping!' ),
							'tooltip'         => esc_html__( 'Message to show before reaching the goal. Use shortcode [remainder] to display the amount left to reach the minimum.', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'show_freeshipping_bar',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
						'freeshipping_success'            => array(
							'section'         => 'wc_freeshipping',
							'type'            => 'textarea',
							'label'           => esc_html__( 'Success message', 'wolmart' ),
							'default'         => esc_html__( 'Your order qualifies for free shipping!', 'wolmart' ),
							'tooltip'         => esc_html__( 'Message to show after reaching 100%.', 'wolmart' ),
							'active_callback' => array(
								array(
									'setting'  => 'show_freeshipping_bar',
									'operator' => '==',
									'value'    => true,
								),
							),
						),
					)
				);
			}

			$this->panels = apply_filters( 'wolmart_customize_panels', $this->panels );
			foreach ( $this->panels as $panel => $settings ) {
				Kirki::add_panel( $panel, $settings );
			}

			$this->sections = apply_filters( 'wolmart_customize_sections', $this->sections );
			foreach ( $this->sections as $section => $settings ) {
				Kirki::add_section( $section, $settings );
			}

			$this->fields = apply_filters( 'wolmart_customize_fields', $this->fields );
			foreach ( $this->fields as $field => $settings ) {
				if ( ! isset( $settings['default'] ) ) {
					$settings['default'] = wolmart_get_option( $field );
				}
				$settings['settings'] = $field;
				Kirki::add_field( 'option', $settings );
			}

			wolmart_unset_global_templates_sidebars();
		}

		public function selective_refresh( $customize ) {
			$customize->selective_refresh->add_partial(
				'selective-post-share',
				array(
					'selector'            => '.post-details .social-icons, .product-single .summary .social-icons',
					'settings'            => array( 'share_type', 'share_icons', 'share_use_hover' ),
					'container_inclusive' => true,
					'render_callback'     => function() {
						if ( function_exists( 'wolmart_print_share' ) ) {
							wolmart_print_share();
						}
					},
				)
			);
			if ( class_exists( 'WooCommerce' ) ) {
				$customize->selective_refresh->add_partial(
					'selective-breadcrumb',
					array(
						'selector'            => '.breadcrumb',
						'settings'            => array( 'ptb_home_icon', 'ptb_delimiter', 'ptb_delimiter_use_icon', 'ptb_delimiter_icon' ),
						'container_inclusive' => true,
						'render_callback'     => function() {
							woocommerce_breadcrumb();
						},
					)
				);
			}
		}
	}
endif;

if ( class_exists( 'Kirki' ) ) {
	Wolmart_Customizer::get_instance();
}
