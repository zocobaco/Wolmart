<?php
/**
 * Wolmart Live Search
 *
 * Search posts or products, post types too.
 * Search products by sku, tag, categories.
 * Support relevanssi plugin for live search
 *
 * @package Wolmart Core WordPress Framework
 * @since 1.0
 */

defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Wolmart_Live_Search' ) ) :

	class Wolmart_Live_Search {
		public function __construct() {
			add_action( 'wp_enqueue_scripts', array( $this, 'add_script' ) );
			add_action( 'wp_ajax_wolmart_ajax_search', array( $this, 'ajax_search' ) );
			add_action( 'wp_ajax_nopriv_wolmart_ajax_search', array( $this, 'ajax_search' ) );
		}

		public function add_script() {
			wp_enqueue_script( 'jquery-autocomplete', WOLMART_ADDONS_URI . '/live-search/jquery.autocomplete' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ), array( 'jquery-core' ), false, true );
			wp_enqueue_script( 'wolmart-live-search', WOLMART_ADDONS_URI . '/live-search/live-search' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ), false, WOLMART_VERSION, true );
		}

		public function ajax_search() {
			check_ajax_referer( 'wolmart-nonce', 'nonce' );

			$query  = apply_filters( 'wolmart_live_search_query', sanitize_text_field( $_REQUEST['query'] ) );
			$posts  = array();
			$result = array();
			$args   = array(
				's'                   => $query,
				'orderby'             => '',
				'post_status'         => 'publish',
				'posts_per_page'      => 50,
				'ignore_sticky_posts' => 1,
				'post_password'       => '',
				'suppress_filters'    => false,
			);

			if ( ! isset( $_REQUEST['post_type'] ) || empty( $_REQUEST['post_type'] ) || 'product' == $_REQUEST['post_type'] ) {
				if ( class_exists( 'WooCommerce' ) ) {
					$posts = $this->search_products( 'product', $args );
					$posts = array_merge( $posts, $this->search_products( 'sku', $args ) );
					$posts = array_merge( $posts, $this->search_products( 'tag', $args ) );
				}
				if ( ! isset( $_REQUEST['post_type'] ) || empty( $_REQUEST['post_type'] ) ) {
					$posts = array_merge( $posts, $this->search_posts( $args, $query ) );
				}
			} else {
				$posts = $this->search_posts( $args, $query, array( sanitize_text_field( $_REQUEST['post_type'] ) ) );
			}

			foreach ( $posts as $post ) {
				if ( class_exists( 'WooCommerce' ) && ( 'product' == $post->post_type || 'product_variation' == $post->post_type ) ) {
					$product       = wc_get_product( $post );
					$product_image = wp_get_attachment_image_src( get_post_thumbnail_id( $product->get_id() ) );
					$title         = $product->get_title();

					// Ignore hidden products.
					if ( 'hidden' != $product->get_catalog_visibility() ) {
						$result[] = array(
							'type'  => 'Product',
							'id'    => $product->get_id(),
							'value' => $title ? $title : esc_html__( '(no title)', 'wolmart' ),
							'url'   => esc_url( $product->get_permalink() ),
							'img'   => esc_url( $product_image[0] ),
							'price' => $product->get_price_html(),
						);
					}

				} else {
					$title = get_the_title( $post->ID );

					$result[] = array(
						'type'  => get_post_type( $post->ID ),
						'id'    => $post->ID,
						'value' => $title ? $title : esc_html__( '(no title)', 'wolmart' ),
						'url'   => esc_url( get_the_permalink( $post->ID ) ),
						'img'   => esc_url( get_the_post_thumbnail_url( $post->ID, 'thumbnail' ) ),
						'price' => '',
					);
				}
			}

			wp_send_json( array( 'suggestions' => $result ) );
		}

		private function search_posts( $args, $query, $post_type = array( 'post' ) ) {
			$args['s']         = $query;
			$args['post_type'] = apply_filters( 'wolmart_live_search_post_type', $post_type );
			$args              = $this->search_add_category_args( $args );

			return $this->search( $args );
		}

		private function search_products( $search_type, $args ) {
			$args['post_type']  = 'product';
			$args['meta_query'] = WC()->query->get_meta_query(); // WPCS: slow query ok.
			$args               = $this->search_add_category_args( $args );

			switch ( $search_type ) {
				case 'product':
					$args['s'] = apply_filters( 'wolmart_live_search_products_query', sanitize_text_field( $_REQUEST['query'] ) );
					break;
				case 'sku':
					$query                = apply_filters( 'wolmart_live_search_products_by_sku_query', sanitize_text_field( $_REQUEST['query'] ) );
					$args['s']            = '';
					$args['post_type']    = array( 'product', 'product_variation' );
					$args['meta_query'][] = array(
						'key'   => '_sku',
						'value' => $query,
					);
					break;
				case 'tag':
					$args['s']           = '';
					$args['product_tag'] = apply_filters( 'wolmart_live_search_products_by_tag_query', sanitize_text_field( $_REQUEST['query'] ) );
					break;
			}
			return $this->search( $args );
		}

		private function search( $args ) {
			$search_query   = http_build_query( $args );
			$search_funtion = apply_filters( 'wolmart_live_search_function', 'get_posts', $search_query, $args );

			if ( 'get_posts' == $search_funtion || ! function_exists( $search_funtion ) ) {

				if ( wolmart_get_option( 'live_relevanssi' ) && function_exists( 'relevanssi_do_query' ) ) {

					$defaults = array(
						'numberposts'      => 5,
						'category'         => 0,
						'orderby'          => 'date',
						'order'            => 'DESC',
						'include'          => array(),
						'exclude'          => array(),
						'meta_key'         => '',
						'meta_value'       => '',
						'post_type'        => 'post',
						'suppress_filters' => true,
					);

					$parsed_args = wp_parse_args( $args, $defaults );
					if ( empty( $parsed_args['post_status'] ) ) {
						$parsed_args['post_status'] = ( 'attachment' === $parsed_args['post_type'] ) ? 'inherit' : 'publish';
					}
					if ( ! empty( $parsed_args['numberposts'] ) && empty( $parsed_args['posts_per_page'] ) ) {
						$parsed_args['posts_per_page'] = $parsed_args['numberposts'];
					}
					if ( ! empty( $parsed_args['category'] ) ) {
						$parsed_args['cat'] = $parsed_args['category'];
					}
					if ( ! empty( $parsed_args['include'] ) ) {
						$incposts                      = wp_parse_id_list( $parsed_args['include'] );
						$parsed_args['posts_per_page'] = count( $incposts );  // Only the number of posts included.
						$parsed_args['post__in']       = $incposts;
					} elseif ( ! empty( $parsed_args['exclude'] ) ) {
						$parsed_args['post__not_in'] = wp_parse_id_list( $parsed_args['exclude'] );
					}

					$parsed_args['ignore_sticky_posts'] = true;
					$parsed_args['no_found_rows']       = true;

					return relevanssi_do_query( new WP_Query( $parsed_args ) );
				}

				return get_posts( $args );

			} else {
				$search_funtion( $search_query, $args );
			}
		}

		private function search_add_category_args( $args ) {
			if ( isset( $_REQUEST['cat'] ) && $_REQUEST['cat'] && '0' != $_REQUEST['cat'] ) {
				if ( 'product' == $_REQUEST['post_type'] ) {
					$args['tax_query'] = array(
						array(
							'taxonomy' => 'product_cat',
							'field'    => 'slug',
							'terms'    => sanitize_text_field( $_REQUEST['cat'] ),
						),
					);
				} elseif ( !isset($_REQUEST['post_type']) || 'post' == $_REQUEST['post_type'] ) {
					$args['category'] = get_terms( array( 'slug' => sanitize_text_field( $_REQUEST['cat'] ) ) )[0]->term_id;
				}
			}
			return $args;
		}
	}
	new Wolmart_Live_Search;
endif;
