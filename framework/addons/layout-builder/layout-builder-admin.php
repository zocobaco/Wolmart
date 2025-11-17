<?php

// Direct access is denied
defined( 'ABSPATH' ) || die;

/**
 * Wolmart Layout Builder Admin
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

class Wolmart_Layout_Builder_Admin extends Wolmart_Base {

	/**
	 * Conditions Map
	 *
	 * @since 1.0
	 * @access public
	 */
	public $conditions;

	/**
	 * Layouts array for different conditions.
	 *
	 * @since 1.0
	 * @access public
	 */
	public $schemes;

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {
		if ( isset( $_REQUEST['page'] ) && 'wolmart-layout-builder' == $_REQUEST['page'] ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
			// Dequeue elementor admin top bar style in studio popup
			if ( isset( $_REQUEST['is_elementor_preview'] ) && $_REQUEST['is_elementor_preview'] ) {
				add_action(
					'admin_enqueue_scripts',
					function() {
						wp_dequeue_script( 'elementor-admin-top-bar' );
					},
					15
				);
			}
		}
		add_action(
			'current_screen',
			function() {
				if ( isset( $_REQUEST['page'] ) && 'wolmart-layout-builder' == $_REQUEST['page'] && isset( $_REQUEST['is_elementor_preview'] ) && $_REQUEST['is_elementor_preview'] ) {
					do_action( 'admin_enqueue_scripts' );
					do_action( 'admin_print_styles' );
				}
			}
		);
		add_action( 'wp_ajax_nopriv_wolmart_layout_builder_save', array( $this, 'ajax_save_layout_builder' ) );
		add_action( 'wp_ajax_wolmart_layout_builder_save', array( $this, 'ajax_save_layout_builder' ) );
	}

	/**
	 * Enqueue scripts.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function enqueue_scripts() {

		$this->get_condition_categories();

		global $wolmart_templates;

		wolmart_set_global_templates_sidebars();

		$this->conditions = wolmart_get_option( 'conditions' );

		// Parsing as array.
		foreach ( $this->conditions as $category => $conditions ) {
			$condition_data = array();
			foreach ( $conditions as $i => $condition ) {
				$condition_r = $condition;
				if ( array_key_exists( 'scheme', $condition ) ) {

					foreach ( $condition['scheme'] as $key => $values ) {
						if ( ! is_array( $values ) ) {
							continue;
						}

						$is_post = false;
						if ( post_type_exists( $key ) || 'child' == $key ) {
							$is_post = true;
						}

						foreach ( $values as $index => $value ) {
							if ( (int) $value ) {
								unset( $condition_r['scheme'][ $key ][ $index ] );
								if ( $is_post ) {
									$condition_r['scheme'][ $key ][ $value ] = get_post_field( 'post_title', $value );
								} else {
									$condition_r['scheme'][ $key ][ $value ] = get_term_field( 'name', $value );
								}
							}
						}
					}
				}
				$condition_data[] = $condition_r;
			}
			$this->conditions[ $category ] = $condition_data;
		}

		// Enqueue styles and scripts.
		wp_enqueue_style( 'jquery-select2' );
		wp_enqueue_script( 'jquery-select2' );
		wp_enqueue_script( 'isotope-pkgd' );
		wp_enqueue_script( 'wolmart-layout-builder-admin', WOLMART_ADDONS_URI . '/layout-builder/layout-builder-admin' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ), array( 'jquery-core' ), WOLMART_VERSION, true );

		// Localize vars.
		wp_localize_script(
			'wolmart-layout-builder-admin',
			'wolmart_layout_vars',
			array(
				'site_url'                      => esc_url( site_url() ),
				'schemes'                       => $this->schemes,
				'controls'                      => Wolmart_Layout_Builder::get_instance()->get_controls(),
				'conditions'                    => $this->conditions,
				'templates'                     => $wolmart_templates,
				'layout_images_url'             => WOLMART_ASSETS . '/images/admin/',
				'text_default'                  => esc_html__( 'Default', 'wolmart' ),
				'text_all'                      => esc_html__( 'All', 'wolmart' ),
				'text_hide'                     => esc_html__( 'Hide', 'wolmart' ),
				'text_my_templates'             => esc_html__( 'My Templates', 'wolmart' ),
				'text_duplicate'                => esc_html__( 'Duplicate', 'wolmart' ),
				'text_options'                  => esc_html__( 'Options', 'wolmart' ),
				'text_apply_prefix'             => esc_html__( 'Apply this ', 'wolmart' ),
				'text_apply_suffix'             => esc_html__( ' for:', 'wolmart' ),
				'text_delete'                   => esc_html__( 'Delete', 'wolmart' ),
				'text_open'                     => esc_html__( 'Open', 'wolmart' ),
				'text_close'                    => esc_html__( 'Close', 'wolmart' ),
				'text_copy'                     => esc_html__( 'Copy Options', 'wolmart' ),
				'text_paste'                    => esc_html__( 'Paste Options', 'wolmart' ),
				'text_confirm_delete_condition' => esc_html__( 'Do you want to delete this layout?', 'wolmart' ),
				'text_create_layout'            => esc_html__( 'Create Layout for ', 'wolmart' ),
				'text_conditions'               => esc_html__( 'Display Conditions', 'wolmart' ),
				'text_no_condition'             => esc_html__( 'No condition selected', 'wolmart' ),
				'text_single_prefix'            => esc_html__( 'in ', 'wolmart' ),
				'text_archive_prefix'           => esc_html__( 'Archive of ', 'wolmart' ),
				'text_manage_conditions'        => esc_html__( 'Manage Conditions', 'wolmart' ),
			)
		);

		wolmart_unset_global_templates_sidebars();
	}

	/**
	 * Get condition categories.
	 *
	 * @since 1.0
	 * @access public
	 */
	public function get_condition_categories() {

		// Add layouts.
		$this->schemes                 = array();
		$this->schemes['site']         = array(
			'title' => esc_html__( 'All Layouts', 'wolmart' ),
		);
		$this->schemes['single_front'] = array(
			'title' => esc_html__( 'Home', 'wolmart' ),
		);

		if ( class_exists( 'WooCommerce' ) ) {
			$this->schemes['archive_product'] = array(
				'title' => esc_html__( 'Shop', 'wolmart' ),
			);
		}
		$this->schemes['archive_post'] = array(
			'title' => esc_html__( 'Blog', 'wolmart' ),
		);
		$this->schemes['search']       = array(
			'title'  => esc_html__( 'Search', 'wolmart' ),
			'scheme' => array(
				'all' => array(
					'title' => esc_html__( 'All Post Types', 'wolmart' ),
				),
			),
		);
		$this->schemes['single_page']  = array(
			'title' => esc_html__( 'Page', 'wolmart' ),
		);
		if ( class_exists( 'WooCommerce' ) ) {
			$this->schemes['single_product'] = array(
				'title' => esc_html__( 'Product Detail', 'wolmart' ),
			);
		}
		$this->schemes['single_post'] = array(
			'title' => esc_html__( 'Post Detail', 'wolmart' ),
		);
		$this->schemes['error']       = array(
			'title' => esc_html__( '404 Page', 'wolmart' ),
		);

		// Get post types to apply layout condition.
		$post_types           = array();
		$post_types_exclude   = apply_filters( 'wolmart_condition_exclude_post_types', array( 'wolmart_template', 'attachment', 'elementor_library' ) );
		$available_post_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach ( $available_post_types as $post_type_slug => $post_type ) {
			if ( ! in_array( $post_type_slug, $post_types_exclude ) ) {
				$post_types[ $post_type_slug ] = array(
					'name'          => $post_type->labels->name,
					'singular_name' => $post_type->labels->singular_name,
					'select_label'  => esc_html__( 'Select', 'wolmart' ) . ' ' . $post_type->labels->name,
				);
			}
		}

		foreach ( $post_types as $post_type => $titles ) {
			if ( empty( $this->schemes[ 'archive_' . $post_type ] ) && apply_filters( 'wolmart_layout_builder_is_available_archive', 'page' != $post_type, $post_type ) ) {
				$this->schemes[ 'archive_' . $post_type ] = array(
					'title' => $titles['name'],
				);
			}
			if ( empty( $this->schemes[ 'single_' . $post_type ] ) && apply_filters( 'wolmart_layout_builder_is_available_single', true, $post_type ) ) {
				$this->schemes[ 'single_' . $post_type ] = array(
					'title' => $titles['singular_name'],
				);
			}
		}

		foreach ( $post_types as $post_type => $titles ) {

			$schemes = array(
				'all' => array(
					'title' => esc_html__( 'All', 'wolmart' ),
				),
			);

			if ( 'product' == $post_type ) { // Products archive

				$schemes['product_cat'] = array(
					'title' => esc_html__( 'Categories', 'wolmart' ),
					'list'  => get_terms(
						array(
							'count'      => 100,
							'taxonomy'   => 'product_cat',
							'fields'     => 'id=>name',
							'hide_empty' => false,
						)
					),
				);

				$schemes['product_brand'] = array(
					'title' => esc_html__( 'Brands', 'wolmart' ),
					'list'  => get_terms(
						array(
							'count'      => 100,
							'taxonomy'   => 'product_brand',
							'fields'     => 'id=>name',
							'hide_empty' => false,
						)
					),
				);

				$schemes['product_tag'] = array(
					'title' => esc_html__( 'Tags', 'wolmart' ),
					'list'  => get_terms(
						array(
							'count'      => 100,
							'taxonomy'   => 'product_tag',
							'fields'     => 'id=>name',
							'hide_empty' => false,
						)
					),
				);
			} elseif ( 'post' == $post_type ) {

				// Posts archive
				$schemes['category'] = array(
					'title' => esc_html__( 'Categories', 'wolmart' ),
					'list'  => get_terms(
						array(
							'count'      => 100,
							'taxonomy'   => 'category',
							'fields'     => 'id=>name',
							'hide_empty' => false,
						)
					),
				);

				$schemes['post_tag'] = array(
					'title' => esc_html__( 'Tags', 'wolmart' ),
					'list'  => get_terms(
						array(
							'count'      => 100,
							'taxonomy'   => 'post_tag',
							'fields'     => 'id=>name',
							'hide_empty' => false,
						)
					),
				);
			}

			foreach ( $schemes as $key => $scheme ) {
				if ( isset( $schemes[ $key ]['list'] ) ) {
					// translators: %s represents post types or taxonomies in the plural.
					$schemes[ $key ]['placeholder'] = sprintf( esc_html__( 'All %s', 'wolmart' ), $scheme['title'] );
				}
			}

			if ( count( $schemes ) ) {

				// Correct archive names of post types.
				if ( 'product' == $post_type ) {
					$schemes['all']['title'] = esc_html__( 'All Shop Pages', 'wolmart' );
				} elseif ( 'post' == $post_type ) {
					$schemes['all']['title'] = esc_html__( 'All Blog Pages', 'wolmart' );
				} elseif ( 'page' == $post_type ) {
					$schemes['all']['title'] = esc_html__( 'All Pages', 'wolmart' );
				} elseif ( 'page' != $post_type ) {
					$schemes['all']['title'] = esc_html__( 'All ', 'wolmart' ) . $titles['singular_name'] . ' ' . esc_html__( 'Archives', 'wolmart' );
				}
				$schemes['all']['title'] = apply_filters( 'wolmart_layout_builder_get_title_of_all_archive', $schemes['all']['title'], $post_type );

				// Add archive scheme for $post_type
				if ( apply_filters( 'wolmart_layout_builder_is_available_archive', 'page' != $post_type, $post_type ) ) {
					$this->schemes[ 'archive_' . $post_type ]['scheme'] = $schemes;
				}

				// Correct single names of post types.
				if ( 'product' == $post_type ) {
					$schemes['all']['title'] = esc_html__( 'All Product Pages', 'wolmart' );
					unset( $schemes['archive_page'] );
				} elseif ( 'post' == $post_type ) {
					$schemes['all']['title'] = esc_html__( 'All Post Pages', 'wolmart' );
					unset( $schemes['archive_page'] );
				} elseif ( 'page' != $post_type ) {
					$schemes['all']['title'] = esc_html__( 'All ', 'wolmart' ) . $titles['singular_name'] . ' ' . esc_html__( 'Pages', 'wolmart' );
				}

				foreach ( $schemes as $key => $scheme ) {
					if ( isset( $schemes[ $key ]['list'] ) ) {
						// translators: %s represents post types or taxonomies in the plural.
						$schemes[ $key ]['placeholder'] = sprintf( esc_html__( 'Select %s', 'wolmart' ), $scheme['title'] );
					}
				}

				$schemes['all']['title'] = apply_filters( 'wolmart_layout_builder_get_title_of_all_single', $schemes['all']['title'], $post_type );

				// Add post pages for each post type.
				$posts        = new WP_Query;
				$posts        = $posts->query(
					array(
						'post_type'      => $post_type,
						'post_status'    => 'publish',
						'posts_per_page' => 100,
					)
				);
				$posts_titles = array();
				foreach ( $posts as $post ) {
					$posts_titles[ $post->ID ] = $post->post_title;
				}

				$schemes[ $post_type ] = array(
					'title'       => $titles['select_label'],
					// translators: %s represents singular name of post type.
					'placeholder' => sprintf( esc_html__( 'Select Individual %s', 'wolmart' ), $titles['name'] ),
					'description' => '',
					'list'        => $posts_titles,
				);

				if ( 'page' == $post_type ) {
					$schemes['child'] = array(
						'title'       => esc_html__( 'Child of Pages', 'wolmart' ),
						'description' => esc_html__( 'Children of', 'wolmart' ),
						'placeholder' => esc_html__( 'Select Parent Pages', 'wolmart' ),
						'ajaxselect'  => true,
					);
				}

				// Add single scheme for $post_type
				if ( apply_filters( 'wolmart_layout_builder_is_available_single', true, $post_type ) ) {
					$this->schemes[ 'single_' . $post_type ]['scheme'] = $schemes;
				}
			}

			// Add post type to search scheme
			$this->schemes['search']['scheme'][ $post_type ] = array(
				'title'       => $titles['singular_name'],
				'description' => esc_html__( 'Search result for', 'wolmart-core' ),
			);
		}

		foreach ( $this->schemes as $category => $scheme ) {
			if ( empty( $this->schemes[ $category ]['layout_title'] ) ) {
				/* translators: %s represents condition category title. */
				$this->schemes[ $category ]['layout_title'] = sprintf( esc_html__( '%s Layout', 'wolmart' ), $this->schemes[ $category ]['title'] );
				if ( 'site' == $category ) {
					$this->schemes[ $category ]['layout_description'] = esc_html__( 'This layout appears globally.', 'wolmart' );
				} else {
					$this->schemes[ $category ]['layout_description'] = $this->schemes[ $category ]['title'];
				}
			}
		}

		$this->schemes = apply_filters( 'wolmart_layout_builder_schemes', $this->schemes );
	}

	/**
	 * Print Builder Content
	 *
	 * @since 1.0
	 * @access public
	 */
	public function view_layout_builder() {
		Wolmart_Admin_Panel::get_instance()->view_header( 'layout_builder' );
		$title = esc_html__( 'Layout Builder', 'wolmart' );
		$desc  = esc_html__( 'Create remarkable site with fully customizable layouts and online library.', 'wolmart' );

		if ( isset( $_REQUEST['layout'] ) && ! empty( $_REQUEST['is_elementor_preview'] ) ) {
			$template_type = $_REQUEST['layout'];
			$post_type     = get_post_meta( (int) $_REQUEST['post'], 'preview', true );
			$post_type     = $post_type ? $post_type : 'post';

			if ( $template_type ) {
				$title = esc_html__( 'Display Conditions', 'wolmart' );
				$desc  = esc_html__( 'Create or choose page layouts you wish to show your editing template, click on the corresponding area and select your editing template.', 'wolmart' );
			}
		}
		?>
		<div class="wolmart-admin-panel-header wolmart-row">
			<div class="wolmart-admin-panel-header-inner">
				<h2><?php echo esc_html( $title ); ?></h2>
				<p><?php echo esc_html( $desc ); ?></p>
			</div>
			<button class="button button-dark button-large wolmart-layouts-save"><i class="w-icon-save"></i><?php esc_html_e( 'Save Layouts', 'wolmart' ); ?></button>
		</div>
		<script type="text/template" id="wolmart_layout_template">
			<div class="wolmart-layout">
				<?php
				if ( isset( $template_type ) && 'block' != $template_type ) {
					$part_class = '';
					$part_attr  = '';
					if ( 'header' == $template_type || 'footer' == $template_type ) {
						$part_class = $template_type;
						$part_attr  = $template_type;
					} elseif ( 'popup' == $template_type ) {
						$part_class = 'general';
						$part_attr  = 'general';
					} else {
						$part_class = 'content';
						if ( 'single_product' == $template_type ) {
							$part_attr = 'content_single_product';
						} elseif ( 'archive_product' == $template_type ) {
							$part_attr = 'content_archive_product';
						} elseif ( 'cart' == $template_type ) {
							$part_attr = 'content_cart';
						} elseif ( 'checkout' == $template_type ) {
							$part_attr = 'content_checkout';
						} elseif ( 'single' == $template_type ) {
							$part_attr = 'content_single_' . $post_type;
						} elseif ( 'archive' == $template_type ) {
							$part_attr = 'content_archive_' . $post_type;
						}
					}
					?>
					<div class="layout-part templates-nav <?php echo esc_attr( $part_class ); ?>" data-part="<?php echo esc_attr( $part_attr ); ?>">
						<i class="w-icon-edit"></i>
					</div>
					<?php
				}
				?>
				<div class="layout-part general" data-part="general">
					<span><?php esc_html_e( 'General Settings', 'wolmart' ); ?></span>
				</div>
				<div class="layout-part header" data-part="header">
					<span class="block-name"><?php esc_html_e( 'Header', 'wolmart' ); ?></span><span class="block-value"></span>
				</div>
				<div class="layout-part ptb" data-part="ptb">
					<span class="block-name"><?php esc_html_e( 'Page Header', 'wolmart' ); ?></span><span class="block-value"></span>
				</div>
				<div class="layout-part top-block" data-part="top_block">
					<span class="block-name"><?php esc_html_e( 'Top Block', 'wolmart' ); ?></span><span class="block-value"></span>
				</div>
				<div class="layout-part top-sidebar sidebar" data-part="top_sidebar">
					<span class="block-name"><?php esc_html_e( 'Horizontal Filter', 'wolmart' ); ?></span><span class="block-value"></span>
				</div>							
				<div class="content-wrapper">
					<div class="layout-part left-sidebar sidebar" data-part="left_sidebar">
						<span class="block-name"><?php esc_html_e( 'Sidebar', 'wolmart' ); ?></span><span class="block-value"></span>
					</div>							
					<div class="content-inwrap">
						<div class="layout-part inner-top-block" data-part="inner_top_block">
							<span class="block-name"><?php esc_html_e( 'Inner Top', 'wolmart' ); ?></span><span class="block-value"></span>
						</div>
						<div class="layout-part content" data-part="content">
							<span class="block-name"><?php esc_html_e( 'Content', 'wolmart' ); ?></span><span class="block-value"></span>
						</div>
						<div class="layout-part inner-bottom-block" data-part="inner_bottom_block">
							<span class="block-name"><?php esc_html_e( 'Inner Bottom', 'wolmart' ); ?></span><span class="block-value"></span>
						</div>							
					</div>
					<div class="layout-part right-sidebar sidebar" data-part="right_sidebar">
						<span class="block-name"><?php esc_html_e( 'Sidebar', 'wolmart' ); ?></span><span class="block-value"></span>
					</div>
				</div>
				<div class="layout-part bottom-block" data-part="bottom_block">
					<span class="block-name"><?php esc_html_e( 'Bottom Block', 'wolmart' ); ?></span><span class="block-value"></span>
				</div>
				<div class="layout-part footer" data-part="footer">
					<span class="block-name"><?php esc_html_e( 'Footer', 'wolmart' ); ?></span><span class="block-value"></span>
				</div>
			</div>
		</script>
		<div class="wolmart-admin-panel-row" id="wolmart_layout_builder">
			<div class="wolmart-admin-panel-side">
				<ul class="wolmart-layout-builder-categories">
					<?php foreach ( $this->schemes as $category => $data ) : ?>
						<li class="wolmart-condition-cat wolmart-condition-cat-<?php echo esc_attr( $category . ( 'site' == $category ? ' active' : '' ) ); ?>"
							data-category="<?php echo esc_attr( $category ); ?>">
							<?php
							$img_name = $category;
							if ( 'site' == $category ) {
								$img_name = 'site';
							}
							if ( ! in_array( $img_name, array( 'site', 'archive_post', 'single_post', 'archive_product', 'single_product', 'error', 'search', 'single_front', 'single_page' ) ) ) {
								if ( 'single' == substr( $img_name, 0, 6 ) ) {
									$img_name = 'single_post';
								} else {
									$img_name = 'archive_post';
								}
							}

							?>
							<img src="<?php echo esc_url( WOLMART_ASSETS . '/images/admin/layout_' . $img_name . '.svg' ); ?>">
							<?php echo esc_html( $data['title'] ); ?>
							<span class="wolmart-condition-count" style="display:none;">0</span>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
			<div class="wolmart-admin-panel-content">
				<div id="wolmart_layout_content" class="wolmart-layouts-container<?php echo ( isset( $template_type ) && 'block' != $template_type ) ? ' display-conditions' : ''; ?>"></div>
			</div>
		</div>
		<?php
		Wolmart_Admin_Panel::get_instance()->view_footer();
	}

	/**
	 * Save layout builder conditions by ajax request
	 */
	public function ajax_save_layout_builder() {
		if ( ! check_ajax_referer( 'wolmart-core-nonce', 'nonce' ) ) {
			wp_send_json_error(
				array(
					'error'   => 1,
					'message' => esc_html__( 'Nonce Error', 'wolmart' ),
				)
			);
		}

		if ( isset( $_POST['conditions'] ) ) {

			// Parsing as array.
			$conditions = $_POST['conditions'];

			foreach ( $conditions as $category => $category_conditions ) {
				$condition_data      = array();
				$category_conditions = array_filter( $category_conditions, 'is_array' );
				foreach ( $category_conditions as $condition ) {
					$condition_r = $condition;
					if ( array_key_exists( 'scheme', $condition ) ) {

						foreach ( $condition['scheme'] as $key => $values ) {
							if ( ! is_array( $values ) ) {
								continue;
							}

							if ( ! isset( $values[0] ) ) {
								$keys = array_keys( $values );
								unset( $condition_r['scheme'][ $key ] );
								$condition_r['scheme'][ $key ] = $keys;
							}
						}
					}
					$condition_data[] = $condition_r;
				}
				usort( $condition_data, 'wolmart_layout_builder_compare_condition' );
				$conditions[ $category ] = $condition_data;
			}

			// Sort by category priorities
			uksort( $conditions, 'wolmart_layout_builder_compare_category' );

			set_theme_mod( 'conditions', $conditions );
		}
	}
}

/**
 * Setup Wolmart Layout Builder
 */
Wolmart_Layout_Builder_Admin::get_instance();

if ( ! function_exists( 'wolmart_layout_builder_compare_category' ) ) {
	/**
	 * Compare priority of two condition categories.
	 *
	 * @since 1.0
	 * @param string $first
	 * @param string $second
	 * @param int compared result
	 */
	function wolmart_layout_builder_compare_category( $first, $second ) {
		if ( $first == $second ) {
			return 0;
		}
		if ( 'site' == $first ) {
			return -1;
		}
		if ( 'site' == $second ) {
			return 1;
		}
		if ( 'error' == $first ) {
			return 1;
		}
		if ( 'error' == $second ) {
			return -1;
		}
		if ( 'single_front' == $first ) {
			return 1;
		}
		if ( 'single_front' == $second ) {
			return -1;
		}
		if ( 'search' == $first ) {
			return 1;
		}
		if ( 'search' == $second ) {
			return -1;
		}
		return strcmp( $first, $second );
	}
}


if ( ! function_exists( 'wolmart_layout_builder_compare_condition' ) ) {
	/**
	 * Compare priority of two condition categories.
	 *
	 * @since 1.0
	 * @param string $first
	 * @param string $second
	 * @param int compared result
	 */
	function wolmart_layout_builder_compare_condition( $first, $second ) {
		if ( empty( $second['scheme'] ) || ! empty( $second['scheme']['all'] ) ) {
			return 1;
		}
		if ( empty( $first['scheme'] ) || ! empty( $first['scheme']['all'] ) ) {
			return -1;
		}
		if ( 1 == count( $first['scheme'] ) ) {
			foreach ( $first['scheme'] as $scheme_key => $scheme_value ) {
				if ( post_type_exists( $scheme_key ) ) {
					return 1;
				}
			}
		}
		if ( 1 == count( $second['scheme'] ) ) {
			foreach ( $second['scheme'] as $scheme_key => $scheme_value ) {
				if ( post_type_exists( $scheme_key ) ) {
					return -1;
				}
			}
		}
		return 0;
	}
}
