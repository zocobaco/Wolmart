<?php
add_action( 'init', array( 'Wolmart_LazyLoad_Images', 'init' ) );

/**
 * Lazyload Image
 */
if ( ! class_exists( 'Wolmart_LazyLoad_Images' ) ) :

	class Wolmart_LazyLoad_Images {

		static $lazy_image_escaped;

		static function init() {
			add_action( 'wp_head', array( __CLASS__, 'setup' ), 99 );
			add_filter( 'wolmart_lazyload_images', array( __CLASS__, 'add_image_placeholders' ), 9999 );
		}

		static function setup() {

			Wolmart_LazyLoad_Images::$lazy_image_escaped = esc_url( get_parent_theme_file_uri( 'assets/images/lazy.png' ) );

			add_filter( 'the_content', array( __CLASS__, 'add_image_placeholders' ), 9999 );
			add_filter( 'post_thumbnail_html', array( __CLASS__, 'add_image_placeholders' ), 11 );
			add_filter( 'woocommerce_product_get_image', array( __CLASS__, 'add_image_placeholders' ), 11 );
			add_filter( 'get_avatar', array( __CLASS__, 'add_image_placeholders' ), 11 );
			add_filter( 'wolmart_product_hover_image_html', array( __CLASS__, 'add_image_placeholders' ), 11 );
			add_filter( 'wolmart_wc_subcategory_thumbnail_html', array( __CLASS__, 'add_image_placeholders' ), 11 );
			add_filter( 'woocommerce_single_product_image_thumbnail_html', array( __CLASS__, 'add_image_placeholders' ), 9999 );

			wp_enqueue_script( 'wolmart-lazyload', WOLMART_ADDONS_URI . '/lazyload-images/lazyload' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ), array( 'jquery-core' ), false, true );
		}

		static function add_image_placeholders( $content ) {
			if ( is_feed() || is_preview() ) {
				return $content;
			}

			if ( class_exists( 'Wolmart_Critical' ) ) {
				$preloads = Wolmart_Critical::get_instance()->get_preloads();
			}

			$matches = array();
			preg_match_all( '/<img[\s\r\n]+.*?>/is', $content, $matches );

			$search  = array();
			$replace = array();

			foreach ( $matches[0] as $img_html ) {
				if ( ! empty( $preloads ) ) {
					$skip = false;
					foreach ( $preloads as $preload ) {
						if ( false !== strpos( $img_html, $preload ) ) {
							$skip = true;
							break;
						}
					}
					if ( $skip ) {
						continue;
					}
				}

				if ( false !== strpos( $img_html, 'data-lazy' ) || preg_match( "/src=['\"]data:image/is", $img_html ) ) {
					continue;
				}

				// replace the src and add the data-oi
				$replace_html = '';
				$style        = '';

				if ( preg_match( '/width=["\']/i', $img_html ) && preg_match( '/height=["\']/i', $img_html ) ) {
					preg_match( '/width=(["\'])(.*?)["\']/is', $img_html, $match_width );
					preg_match( '/height=(["\'])(.*?)["\']/is', $img_html, $match_height );
					if ( isset( $match_width[2] ) && $match_width[2] && is_numeric( $match_width[2] ) && isset( $match_height[2] ) && $match_height[2] && is_numeric( $match_height[2] ) ) {
						$style = 'padding-top : ' . round( $match_height[2] / $match_width[2] * 100, 2 ) . '%;';
					} else {
						continue;
					}
				} else {
					continue;
				}

				$replace_html = preg_replace( '/<img(.*?)src=/is', '<img$1src="' . Wolmart_LazyLoad_Images::$lazy_image_escaped . '" data-lazy=', $img_html );
				$replace_html = preg_replace( '/<img(.*?)srcset=/is', '<img$1srcset="' . Wolmart_LazyLoad_Images::$lazy_image_escaped . '" data-lazyset=', $replace_html );
				$replace_html = preg_replace( '/<img(.*?)sizes=/is', '<img$1 data-sizes=', $replace_html );

				if ( $style ) {
					if ( preg_match( '/style=["\']/i', $replace_html ) ) {
						$replace_html = preg_replace( '/style=(["\'])(.*?)["\']/is', 'style=$1' . $style . '$2$1', $replace_html );
					} else {
						$replace_html = preg_replace( '/<img/is', '<img style="' . $style . '"', $replace_html );
					}
				}

				if ( preg_match( '/class=["\']/i', $replace_html ) ) {
					$replace_html = preg_replace( '/class=(["\'])(.*?)["\']/is', 'class=$1w-lazyload $2$1', $replace_html );
				} else {
					$replace_html = preg_replace( '/<img/is', '<img class="w-lazyload"', $replace_html );
				}

				array_push( $search, $img_html );
				array_push( $replace, $replace_html );
			}

			$search  = array_unique( $search );
			$replace = array_unique( $replace );
			$content = str_replace( $search, $replace, $content );

			// Background Image Lazyload
			$content = preg_replace_callback(
				'/style="([^"]*)background-image:\s*url\(([^)]*)\);+/is',
				function( $matches ) {
					if ( 'assets/images/lazy.png' == $matches[2] ) {
						return $matches[0];
					}
					return ' data-lazy="' . trim( $matches[2], '\'' ) . '" style="';
				},
				$content
			);
			$content = str_replace( ' style=""', '', $content );

			return $content;
		}
	}
endif;
