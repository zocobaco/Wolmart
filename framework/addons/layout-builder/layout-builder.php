<?php
/**
 * Wolmart Layout Builder
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */

class Wolmart_Layout_Builder extends Wolmart_Base {

	/**
	 * Constructor
	 *
	 * @since 1.0
	 * @access public
	 */
	public function __construct() {

		// Setup layout
		add_action( 'wp', array( $this, 'setup_layout' ), 5 );

		// Get layout options from theme option.
		add_filter( 'wolmart_layout_theme_options_map', array( $this, 'get_theme_options_map' ), 10, 2 );

		// Print layout css vars
		add_action( 'wp_enqueue_scripts', array( $this, 'print_css_vars' ), 99 );

		// Print partial blocks
		add_action( 'wolmart_before_content', array( $this, 'print_part_block' ) );
		add_action( 'wolmart_before_inner_content', array( $this, 'print_part_block' ) );
		add_action( 'wolmart_after_inner_content', array( $this, 'print_part_block' ) );
		add_action( 'wolmart_after_content', array( $this, 'print_part_block' ) );
	}

	/**
	 * Setup layout
	 *
	 * @since 1.0
	 */
	public function setup_layout() {
		global $wolmart_layout;
		$wolmart_layout = $this->get_layout();
	}

	/**
	 * Get controls
	 *
	 * @since 1.0
	 */
	public function get_controls() {

		$controls = array(
			'general'                 => array(
				'wrap'        => array(
					'type'    => 'image',
					'label'   => esc_html__( 'Wrap', 'wolmart' ),
					'options' => array(
						'container'       => array(
							'image' => 'site-boxed.svg',
							'title' => esc_html__( 'Container', 'wolmart' ),
						),
						'container-fluid' => array(
							'image' => 'site-fluid.svg',
							'title' => esc_html__( 'Container Fluid', 'wolmart' ),
						),
						'full'            => array(
							'image' => 'site-full.svg',
							'title' => esc_html__( 'Full', 'wolmart' ),
						),
					),
				),
				'popup'       => array(
					'type'  => 'block_popup',
					'label' => esc_html__( 'Popup', 'wolmart' ),
				),
				'popup_delay' => array(
					'type'  => 'number',
					'label' => esc_html__( 'Popup Delay', 'wolmart' ),
					'unit'  => esc_html( 'seconds', 'wolmart' ),
				),
				'top_bar'     => array(
					'type'  => 'block',
					'label' => esc_html__( 'Top Notice Block', 'wolmart' ),
				),
			),
			'header'                  => array(
				'header' => array(
					'type'  => 'block_header',
					'label' => esc_html__( 'Header', 'wolmart' ),
				),
			),
			'footer'                  => array(
				'footer' => array(
					'type'  => 'block_footer',
					'label' => esc_html__( 'Footer', 'wolmart' ),
				),
			),
			'ptb'                     => array(
				'ptb'             => array(
					'type'  => 'block',
					'label' => esc_html__( 'Page Title Bar', 'wolmart' ),
				),
				'title'           => array(
					'type'  => 'text',
					'label' => esc_html__( 'Page Title', 'wolmart' ),
				),
				'subtitle'        => array(
					'type'  => 'text',
					'label' => esc_html__( 'Page Subtitle', 'wolmart' ),
				),
				'show_breadcrumb' => array(
					'type'  => 'toggle',
					'label' => esc_html__( 'Show Breadcrumb', 'wolmart' ),
				),
				'breadcrumb_wrap' => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Breadcrumb Wrap', 'wolmart' ),
					'options' => array(
						''                => esc_html__( 'Default', 'wolmart' ),
						'container'       => esc_html__( 'Container', 'wolmart' ),
						'container-fluid' => esc_html__( 'Container Fluid', 'wolmart' ),
						'full'            => esc_html__( 'Full', 'wolmart' ),
					),
				),
			),
			'top_block'               => array(
				'top_block' => array(
					'type'  => 'block',
					'label' => esc_html__( 'Top Block', 'wolmart' ),
				),
			),
			'bottom_block'            => array(
				'bottom_block' => array(
					'type'  => 'block',
					'label' => esc_html__( 'Bottom Block', 'wolmart' ),
				),
			),
			'inner_top_block'         => array(
				'inner_top_block' => array(
					'type'  => 'block',
					'label' => esc_html__( 'Inner Top Block', 'wolmart' ),
				),
			),
			'inner_bottom_block'      => array(
				'inner_bottom_block' => array(
					'type'  => 'block',
					'label' => esc_html__( 'Inner Bottom Block', 'wolmart' ),
				),
			),
			'top_sidebar'             => array(
				'top_sidebar' => array(
					'type'  => 'block_sidebar',
					'label' => esc_html__( 'Horizontal Filter Widgets', 'wolmart' ),
				),
			),
			'left_sidebar'            => array(
				'left_sidebar'       => array(
					'type'  => 'block_sidebar',
					'label' => esc_html__( 'Left Sidebar', 'wolmart' ),
				),
				'left_sidebar_type'  => array(
					'type'    => 'image',
					'label'   => esc_html__( 'Sidebar Type', 'wolmart' ),
					'options' => array(
						'classic'   => array(
							'image' => 'ls-classic.svg',
							'title' => esc_html__( 'Classic', 'wolmart' ),
						),
						'offcanvas' => array(
							'image' => 'ls-offcanvas.svg',
							'title' => esc_html__( 'Off Canvas', 'wolmart' ),
						),
					),
				),
				'left_sidebar_width' => array(
					'type'  => 'text',
					'label' => esc_html__( 'Sidebar Width', 'wolmart' ),
				),
			),
			'right_sidebar'           => array(
				'right_sidebar'       => array(
					'type'  => 'block_sidebar',
					'label' => esc_html__( 'Right Sidebar', 'wolmart' ),
				),
				'right_sidebar_type'  => array(
					'type'    => 'image',
					'label'   => esc_html__( 'Right Sidebar Type', 'wolmart' ),
					'options' => array(
						'classic'   => array(
							'image' => 'rs-classic.svg',
							'title' => esc_html__( 'Classic', 'wolmart' ),
						),
						'offcanvas' => array(
							'image' => 'rs-offcanvas.svg',
							'title' => esc_html__( 'Off Canvas', 'wolmart' ),
						),
					),
				),
				'right_sidebar_width' => array(
					'type'  => 'text',
					'label' => esc_html__( 'Sidebar Width', 'wolmart' ),
				),
			),
			'content_error'           => array(
				'error_block' => array(
					'type'  => 'block',
					'label' => esc_html__( 'Error Block', 'wolmart' ),
				),
			),
			'content_archive_post'    => array(
				'post_type'    => array(
					'type'    => 'buttonset',
					'label'   => esc_html__( 'Post Type', 'wolmart' ),
					'options' => array(
						'simple' => esc_html__( 'Simple', 'wolmart' ),
						'mask'   => esc_html__( 'Mask', 'wolmart' ),
						'list'   => esc_html__( 'List', 'wolmart' ),
					),
				),
				'posts_layout' => array(
					'type'    => 'buttonset',
					'label'   => esc_html__( 'Posts Layout', 'wolmart' ),
					'options' => array(
						'grid'    => esc_html__( 'Grid', 'wolmart' ),
						'masonry' => esc_html__( 'Masonry', 'wolmart' ),
					),
				),
				'posts_column' => array(
					'type'  => 'number',
					'label' => esc_html__( 'Posts Column', 'wolmart' ),
					'min'   => 1,
					'max'   => 8,
				),

			),
			'content_single_product'  => array(
				'single_product_type'     => array(
					'type'    => 'select',
					'label'   => esc_html__( 'Single Product Type', 'wolmart' ),
					'options' => array(
						''              => esc_html__( 'Default', 'wolmart' ),
						'vertical'      => esc_html__( 'Vertical Thumbs', 'wolmart' ),
						'horizontal'    => esc_html__( 'Horizontal Thumbs', 'wolmart' ),
						'grid'          => esc_html__( 'Grid Images', 'wolmart' ),
						'masonry'       => esc_html__( 'Masonry', 'wolmart' ),
						'gallery'       => esc_html__( 'Gallery', 'wolmart' ),
						'sticky-info'   => esc_html__( 'Sticky Information', 'wolmart' ),
						'sticky-thumbs' => esc_html__( 'Sticky Thumbs', 'wolmart' ),
						'sticky-both'   => esc_html__( 'Left &amp; Right Sticky', 'wolmart' ),
						'builder'       => esc_html__( 'Use Builder', 'wolmart' ),
					),
				),
				'single_product_template' => array(
					'type'  => 'block_product_layout',
					'label' => esc_html__( 'Single Product Layout', 'wolmart' ),
				),
				'product_data_type'       => array(
					'type'    => 'buttonset',
					'label'   => esc_html__( 'Product Data Type', 'wolmart' ),
					'options' => array(
						'tab'       => esc_html__( 'Tab', 'wolmart' ),
						'accordion' => esc_html__( 'Accordion', 'wolmart' ),
						'section'   => esc_html__( 'Section', 'wolmart' ),
					),
				),
			),
			'content_archive_product' => array(
				// 'cs_products_grid' => array(
				// 	'type'  => 'title',
				// 	'label' => esc_html__( 'Products Grid', 'wolmart' ),
				// ),
				'products_column' => array(
					'type'  => 'number',
					'label' => esc_html__( 'Products Column', 'wolmart' ),
					'min'   => 1,
					'max'   => 8,
				),
				'loadmore_type'   => array(
					'type'    => 'image',
					'label'   => esc_html__( 'Load More', 'wolmart' ),
					'options' => array(
						'page'   => array(
							'image' => 'loadmore-page.png',
							'title' => esc_html( 'Pagination', 'wolmart' ),
						),
						'button' => array(
							'image' => 'loadmore-btn.png',
							'title' => esc_html( 'Button', 'wolmart' ),
						),
						'scroll' => array(
							'image' => 'loadmore-scroll.png',
							'title' => esc_html( 'Infinite Scroll', 'wolmart' ),
						),
					),
				),
				'count_select'    => array(
					'type'    => 'text',
					'label'   => esc_html__( 'Products Count Select', 'wolmart' ),
					'tooltip' => esc_html__( 'Input numbers of count select box(9, _12, 24, 36).', 'wolmart' ),
				),
			),
		);

		if ( ! defined( 'WPB_VC_VERSION' ) ) {
			unset( $controls['general']['popup_delay'] );
		}

		return $controls;
	}

	/**
	 * Get layout theme options map
	 *
	 * @since 1.0
	 */
	function get_theme_options_map( $options_map, $layout_name ) {

		if ( 'archive_post' == $layout_name ) {

			return array(
				'post_type'      => 'post_type',
				'post_overlay'   => 'post_overlay',
				'posts_column'   => 'posts_column',
				'posts_layout'   => 'posts_layout',
				'posts_filter'   => 'posts_filter',
				'show_datebox'   => 'post_show_date_box',
				'excerpt_type'   => 'excerpt_type',
				'excerpt_length' => 'excerpt_length',
				'loadmore_type'  => 'posts_load',
			);
		} elseif ( 'single_post' == $layout_name ) {

			return array(
				'posts_layout'     => 'posts_layout',
				'related_count'    => 'post_related_count',
				'related_column'   => 'post_related_column',
				'related_order'    => 'post_related_order',
				'related_orderway' => 'posts_related_orderway',
			);
		} elseif ( 'archive_product' == $layout_name ) {

			return array(
				'products_column' => 'products_column',
				'products_gap'    => 'products_gap',
				'count_select'    => 'products_count_select',
				'loadmore_type'   => 'products_load',
			);
		} elseif ( 'single_product' == $layout_name ) {

			return array(
				'single_product_type'   => 'single_product_type',
				'single_product_sticky' => 'single_product_sticky',
				'products_count_select' => 'products_count_select',
				'products_load'         => 'products_load',
				'product_data_type'     => 'product_data_type',
			);
		}
		return array();
	}

	/**
	 * Get layout
	 *
	 * @since 1.0
	 */
	public function get_layout( $layout_name = '' ) {

		global $wp_query;
		$layout         = array();
		$all_conditions = wolmart_get_option( 'conditions' );
		$all_controls   = $this->get_controls();

		if ( ! $layout_name ) {
			$layout_name = wolmart_get_page_layout();
		}

		// create layout value
		foreach ( $all_controls as $part => $controls ) {
			if ( 'content_' == substr( $part, 0, 8 ) ) {
				if ( 'content_' . $layout_name == $part ) {
					// create empty layout content value
					foreach ( $controls as $name => $control ) {
						$layout[ $name ] = '';
					}
				}
				continue;
			}
			foreach ( $controls as $name => $control ) {
				$layout[ $name ] = '';
			}
		}

		// retrieve layout value from theme options.
		$options_map = apply_filters( 'wolmart_layout_theme_options_map', array(), $layout_name );
		foreach ( $options_map as $option => $name ) {
			$layout[ $option ] = wolmart_get_option( $name );
		}

		// Retrieve current term information in single or archive pages.
		$current_term_id    = false;
		$current_taxonomy   = false;
		$current_term       = false;
		$current_post_id    = (string) get_the_ID();
		$current_post_terms = null;
		if ( $wp_query->is_tax || $wp_query->is_category || $wp_query->is_tag ) {
			$current_term = $wp_query->get_queried_object();
			if ( $current_term ) {
				$current_term_id  = $current_term->term_id;
				$current_taxonomy = $current_term->taxonomy;
			}
		}

		// Apply only site layout.
		$apply_only_site_layout = apply_filters( 'wolmart_apply_only_site_layout', apply_filters( 'wolmart_is_vendor_store', false ) );

		// retrieve layout value from layout builder.

		if ( $all_conditions && is_array( $all_conditions ) ) {
			foreach ( $all_conditions as $category => $conditions ) {

				if ( 'site' != $category && $apply_only_site_layout ) {
					continue;
				}

				if (
					is_front_page() && 'single_front' == $category  // if home layout
					|| 'site' == $category                          // if global layout
					|| $layout_name == $category                    // if current post type's single or archive layout
					|| is_search() && 'search' == $category         // if search layout
				) {

					foreach ( $conditions as $condition ) {

						$pass = false;

						if ( 'site' == $category || 'error' == $category || 'single_front' == $category ) {

							// if no condition scheme exists
							$pass = true;

						} elseif ( ! empty( $condition['scheme'] ) ) {

							// check scheme

							$scheme = $condition['scheme'];

							if ( ! empty( $scheme['all'] ) && $scheme['all'] ) {

								// apply for all cases.
								$pass = true;

							} elseif ( is_search() && 'search' == $category ) {

								$type = get_query_var( 'post_type' );
								if ( 'any' == $type ) {
									$type = 'post';
								}

								if ( ! is_array( $scheme ) || ! count( $scheme ) || isset( $scheme[ $type ] ) && $scheme[ $type ] ) {
									$pass = true;
								}
							} elseif ( $current_term || function_exists( 'is_shop' ) && is_shop() || is_home() && 'archive_post' == $category ) { // Archive pages

								foreach ( $scheme as $scheme_key => $scheme_data ) {

									if (
										'category' == $scheme_key && $wp_query->is_category ||
										'post_tag' == $scheme_key && $wp_query->is_tag ||
										taxonomy_exists( $scheme_key ) && $wp_query->is_tax && $current_term->taxonomy == $scheme_key
									) {
										if ( is_array( $scheme_data ) && count( $scheme_data ) ) {
											if ( in_array( (string) $current_term->term_id, $scheme_data ) ) {
												$pass = true;
											}
										} elseif ( $scheme_data ) {
											$pass = true;
										}
									}
								}
							} else { // Single Pages

								foreach ( $scheme as $scheme_key => $scheme_data ) {

									if ( 'child' == $scheme_key ) {
										if ( is_array( $scheme_data ) && in_array( wp_get_post_parent_id( 0 ), $scheme_data ) ) {
											$pass = true;
										}
									} elseif ( taxonomy_exists( $scheme_key ) ) {

										// Has matched term of listed taxonomy

										$found_term = false;

										if ( ! $current_post_terms ) {
											$current_post_terms = get_terms();
										}

										foreach ( $current_post_terms as $term ) {
											if ( $term->taxonomy == $scheme_key ) {
												$found_term = true;
											}
										}

										if ( is_array( $scheme_data ) && count( $scheme_data ) ) {
											foreach ( $current_post_terms as $term ) {
												if ( in_array( (string) $term->term_id, $scheme_data ) ) {
													$pass = true;
												}
											}
										} elseif ( $scheme_data && $found_term ) {
											$pass = true;
										}

										if ( $pass && ! has_term( $scheme_data, $scheme_key ) ) {
											$pass = false;
										}
									} elseif ( post_type_exists( $scheme_key ) && is_singular( $scheme_key ) &&
										is_array( $scheme_data ) && count( $scheme_data ) &&
										in_array( $current_post_id, $scheme_data ) ) {

										// Pass only post's id exists

										$pass = true;
									}
								}
							}
						}

						// if pass
						if ( $pass && isset( $condition['options'] ) && is_array( $condition['options'] ) ) {
							foreach ( $condition['options'] as $name => $value ) {
								if ( $value ) {
									$layout[ $name ] = $value;
								}
							}
						}
					}
				}
			}
		}

		if ( isset( $layout['post_type'] ) && 'simple' == $layout['post_type'] ) {
			$layout['post_type'] = '';
		}

		return apply_filters( 'wolmart_get_layout', $layout );
	}

	/**
	 * Setup title and subtitle
	 *
	 * @since 1.0
	 */
	public function setup_titles( $pure = false ) {
		// If title or subtitle is already set, return
		global $wolmart_layout;
		if ( ! $pure && ! ( empty( $wolmart_layout['title'] ) || empty( $wolmart_layout['subtitle'] ) ) ) {
			return;
		}

		// Get page title and subtitle for titlebar.
		global $wp_query;
		$title    = '';
		$subtitle = '';

		if ( ! $title ) {
			if ( function_exists( 'is_product_category' ) && is_product_category() ) {
				$cats     = explode( '/', $wp_query->query['product_cat'] );
				$term     = get_term_by( 'slug', array_pop( $cats ), 'product_cat' );
				$title    = $term->name;
				$subtitle = sanitize_text_field( get_the_title( wc_get_page_id( 'shop' ) ) );
			} elseif ( function_exists( 'is_product_tag' ) && is_product_tag() ) {
				$term  = get_term_by( 'slug', $wp_query->query['product_tag'], 'product_tag' );
				$title = $term->name;
				/* translators: %s: product tag */
				$subtitle = sprintf( __( 'Products tagged &ldquo;%s&rdquo;', 'woocommerce' ), $term->name );
			} elseif ( wolmart_is_shop() ) {
				$title    = sanitize_text_field( get_the_title( wc_get_page_id( 'shop' ) ) );
				$subtitle = '';

				// Custom Taxonomy Archive
				$term = get_queried_object();
				if ( $term && ! empty( $term->name ) && ! empty( $term->taxonomy ) ) {
					$title    = $term->name;
					$subtitle = sanitize_text_field( get_the_title( wc_get_page_id( 'shop' ) ) );
				}
			} elseif ( is_home() || is_post_type_archive( 'post' ) ) {
				$title    = apply_filters( 'wolmart_blog_ptb_title', esc_html__( 'Blog', 'wolmart' ) );
				$subtitle = '';
			} elseif ( is_search() ) {
				$title    = '<span id="search-results-count">' . $wp_query->found_posts . '</span> ' . esc_html__( 'Search Results Found', 'wolmart' );
				$subtitle = esc_html__( 'You searched for:', 'wolmart' ) . ' &quot;' . esc_html( get_search_query( false ) ) . '&quot;';
			} elseif ( is_archive() ) {

				if ( is_author() ) { // Author

					$title    = get_the_archive_title();
					$subtitle = esc_html__( 'This author has written', 'wolmart' ) . ' ' . get_the_author_posts() . ' ' . esc_html__( 'articles', 'wolmart' );
				} elseif ( is_post_type_archive() ) { // Post Type archive title

					$title = post_type_archive_title( '', false );
				} elseif ( is_day() ) { // Daily archive title
					// translators: %s represents date
					$title = sprintf( esc_html__( 'Daily Archives: %s', 'wolmart' ), get_the_date() );
				} elseif ( is_month() ) { // Monthly archive title
					// translators: %s represents date
					$title = sprintf( esc_html__( 'Monthly Archives: %s', 'wolmart' ), get_the_date( esc_html_x( 'F Y', 'Page title monthly archives date format', 'wolmart' ) ) );
				} elseif ( is_year() ) { // Yearly archive title

					// translators: %s represents date
					$title = sprintf( esc_html__( 'Yearly Archives: %s', 'wolmart' ), get_the_date( esc_html_x( 'Y', 'Page title yearly archives date format', 'wolmart' ) ) );
				} else { // Categories/Tags/Other

					// Get term title
					$title = single_term_title( '', false );

					// Fix for plugins that are archives but use pages
					if ( ! $title ) {
						$title = get_the_title( get_the_ID() );
					}
				}
			} elseif ( is_404() ) {
				$title    = apply_filters( 'wolmart_404_ptb_title', esc_html__( 'Error 404', 'wolmart' ) );
				$subtitle = '';
			} else {
				$title     = sanitize_text_field( get_the_title() );
				$parent_id = wp_get_post_parent_id( get_the_ID() );
				if ( $parent_id ) {
					$subtitle = get_the_title( $parent_id );
				}
			}
		}

		if ( $pure || empty( $wolmart_layout['title'] ) ) {
			$wolmart_layout['title'] = $title;
		}

		if ( $pure || empty( $wolmart_layout['subtitle'] ) ) {
			$wolmart_layout['subtitle'] = $subtitle;
		}
	}

	/**
	 * Print partial block content
	 *
	 * @since 1.0
	 */
	public function print_part_block( $arg = WOLMART_BEFORE_CONTENT ) {
		$block_name = '';

		global $wolmart_layout;

		if ( doing_action( 'wolmart_before_content' ) && ! empty( $wolmart_layout['top_block'] ) && 'hide' != $wolmart_layout['top_block'] ) {
			$block_name = sanitize_text_field( $wolmart_layout['top_block'] );
			echo '<div class="top-block">';
		} elseif ( doing_action( 'wolmart_before_inner_content' ) && ! empty( $wolmart_layout['inner_top_block'] ) && 'hide' != $wolmart_layout['inner_top_block'] ) {
			$block_name = sanitize_text_field( $wolmart_layout['inner_top_block'] );
			echo '<div class="inner-top-block">';
		} elseif ( doing_action( 'wolmart_after_inner_content' ) && ! empty( $wolmart_layout['inner_bottom_block'] ) && 'hide' != $wolmart_layout['inner_bottom_block'] ) {
			$block_name = sanitize_text_field( $wolmart_layout['inner_bottom_block'] );
			echo '<div class="inner-bottom-block">';
		} elseif ( doing_action( 'wolmart_after_content' ) && ! empty( $wolmart_layout['bottom_block'] ) && 'hide' != $wolmart_layout['bottom_block'] ) {
			$block_name = sanitize_text_field( $wolmart_layout['bottom_block'] );
			echo '<div class="bottom-block">';
		}

		wolmart_print_template( $block_name );

		if ( $block_name ) {
			echo '</div>';
		}
	}

	/**
	 * Print css vars of layout builder.
	 *
	 * @since 1.0
	 */
	public function print_css_vars() {
		$style = '';
		global $wolmart_layout;

		if ( ! empty( $wolmart_layout['left_sidebar_width'] ) && ! empty( $wolmart_layout['left_sidebar'] ) && 'hide' != $wolmart_layout['left_sidebar'] ) {
			$v = $this->format_distance( $wolmart_layout['left_sidebar_width'] );
			if ( $v ) {
				$style .= '--wolmart-left-sidebar-width:' . $v . ';';
			}
		}
		if ( ! empty( $wolmart_layout['right_sidebar_width'] ) && ! empty( $wolmart_layout['right_sidebar'] ) && 'hide' != $wolmart_layout['right_sidebar'] ) {
			$v = $this->format_distance( $wolmart_layout['right_sidebar_width'] );
			if ( $v ) {
				$style .= '--wolmart-right-sidebar-width:' . $v . ';';
			}
		}

		if ( $style ) {
			$style = 'html {' . $style . '}';
			wp_add_inline_style( 'wolmart-theme', $style );
		}
	}

	/**
	 * Get format of distance unit.
	 *
	 * @since 1.0
	 * @param string $distance Distance string to format.
	 * @return string Formated distance
	 */
	public function format_distance( $distance ) {
		if ( (string) (float) $distance == $distance ) {
			return $distance . 'px';
		}
		$matches = array();
		preg_match( '/[\d|\.]+[px|rem|%]+/i', $distance, $matches );
		return count( $matches ) && $matches[0] ? $matches[0] : '';
	}
}

Wolmart_Layout_Builder::get_instance();
