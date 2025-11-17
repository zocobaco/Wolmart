<?php

/**
 * Define core functions using in Wolmart
 */

if ( ! function_exists( 'wolmart_strip_script_tags' ) ) :

	/**
	 * Strip script and style tags from content.
	 *
	 * @since 1.0
	 *
	 * @param string $content Content to strip script and style tags.
	 *
	 * @return string stripped text
	 */
	function wolmart_strip_script_tags( $content ) {
		$content = str_replace( ']]>', ']]&gt;', $content );
		$content = preg_replace( '/<script.*?\/script>/s', '', $content ) ? : $content;
		$content = preg_replace( '/<style.*?\/style>/s', '', $content ) ? : $content;
		return $content;
	}
endif;

if ( ! function_exists( 'wolmart_get_col_class' ) ) :

	/**
	 * Get column class from columns count array
	 *
	 * @since 1.0
	 *
	 * @param int[] $col_cnt Array of columns count per each breakpoint.
	 *
	 * @return string columns class
	 */
	function wolmart_get_col_class( $col_cnt = array() ) {

		$class = ' row';
		foreach ( $col_cnt as $w => $c ) {
			if ( $c > 0 ) {
				$class .= ' cols-' . ( 'min' != $w ? $w . '-' : '' ) . floor( $c );
				if ( is_float( $c ) ) {
					$class .= ' cols-' . ( 'min' != $w ? $w . '-half' : 'half' );
				}
			}
		}

		return apply_filters( 'wolmart_get_col_class', $class );
	}
endif;


if ( ! function_exists( 'wolmart_get_slider_class' ) ) {

	/**
	 * Get slider class from settings array
	 *
	 * @since 1.0
	 *
	 * @param array $settings Slider settings array from elementor widget.
	 *
	 * @return string slider class
	 */
	function wolmart_get_slider_class( $settings = array() ) {

		wp_enqueue_script( 'swiper' );

		return 'slider-wrapper';
	}
}

if ( ! function_exists( 'wolmart_get_slider_status_class' ) ) {
	/**
	 * Get slider status class from settings array
	 *
	 * @since 1.0
	 *
	 * @param array $settings Slider settings array from elementor widget.
	 *
	 * @return string slider class
	 */
	function wolmart_get_slider_status_class( $settings = array() ) {

		$class = '';
		// Nav & Dots
		if ( isset( $settings['nav_type'] ) && 'full' == $settings['nav_type'] ) {
			$class .= ' slider-nav-full';
		} else {
			if ( isset( $settings['nav_type'] ) && 'circle' == $settings['nav_type'] ) {
				$class .= ' slider-nav-circle';
			}
			if ( isset( $settings['nav_pos'] ) && 'top' == $settings['nav_pos'] ) {
				$class .= ' slider-nav-top';
			} elseif ( isset( $settings['nav_pos'] ) && 'bottom' == $settings['nav_pos'] ) {
				$class .= ' slider-nav-bottom';
			} elseif ( isset( $settings['nav_pos'] ) && 'inner' != $settings['nav_pos'] ) {
				$class .= ' slider-nav-outer';
			}
		}
		if ( isset( $settings['nav_hide'] ) && 'yes' == $settings['nav_hide'] ) {
			$class .= ' slider-nav-fade';
		}
		if ( isset( $settings['dots_skin'] ) && $settings['dots_skin'] ) {
			$class .= ' slider-dots-' . $settings['dots_skin'];
		}
		if ( isset( $settings['dots_pos'] ) && 'inner' == $settings['dots_pos'] ) {
			$class .= ' slider-dots-inner';
		}
		if ( isset( $settings['dots_pos'] ) && 'outer' == $settings['dots_pos'] ) {
			$class .= ' slider-dots-outer';
		}
		if ( isset( $settings['fullheight'] ) && 'yes' == $settings['fullheight'] ) {
			$class .= ' slider-full-height';
		}
		if ( isset( $settings['box_shadow_slider'] ) && 'yes' == $settings['box_shadow_slider'] ) {
			$class .= ' slider-shadow';
		}

		if ( isset( $settings['slider_vertical_align'] ) && ( 'top' == $settings['slider_vertical_align'] ||
			'middle' == $settings['slider_vertical_align'] ||
			'bottom' == $settings['slider_vertical_align'] ||
			'same-height' == $settings['slider_vertical_align'] ) ) {

			$class .= ' slider-' . $settings['slider_vertical_align'];
		}

		return $class;
	}
}

if ( ! function_exists( 'wolmart_get_slider_attrs' ) ) {

	/**
	 * Get slider data attribute from settings array
	 *
	 * @since 1.0
	 *
	 * @param array $settings Slider settings array from elementor widget.
	 * @param array $col_cnt  Columns count
	 * @param string $id      Hash string for element
	 *
	 * @return string slider data attribute
	 */
	function wolmart_get_slider_attrs( $settings, $col_cnt, $id = '' ) {

		$max_breakpoints = wolmart_get_breakpoints();

		$extra_options = array();

		if ( ! empty( $settings['slide_effect'] ) ) {
			$extra_options['effect'] = $settings['slide_effect'];
		}

		$extra_options['spaceBetween'] = wolmart_get_grid_space( isset( $settings['col_sp'] ) ? $settings['col_sp'] : '' );

		if ( isset( $settings['loop'] ) && 'yes' == $settings['loop'] ) { // default is false
			$extra_options['loop'] = true;
		}

		// Auto play
		if ( isset( $settings['autoplay'] ) && 'yes' == $settings['autoplay'] ) { // default is false
			if ( isset( $settings['autoplay_timeout'] ) && 5000 !== (int) $settings['autoplay_timeout'] ) { // default is 5000
				$extra_options['autoplay'] = array(
					'delay' => (int) $settings['autoplay_timeout'],
				);
			}
		}

		if ( isset( $settings['dots_type'] ) && 'thumb' == $settings['dots_type'] && $id ) {
			$extra_options['dotsContainer'] = '.slider-thumb-dots-' . $id;
		}
		if ( ! empty( $settings['show_nav'] ) ) {
			$extra_options['navigation'] = true;
		}
		if ( ! empty( $settings['show_dots'] ) ) {
			$extra_options['pagination'] = true;
		}
		if ( isset( $settings['autoheight'] ) && 'yes' == $settings['autoheight'] ) {
			$extra_options['autoHeight'] = true;
		}

		$responsive = array();
		$col_cnt    = wolmart_get_responsive_cols( $col_cnt );
		foreach ( $col_cnt as $w => $c ) {
			$responsive[ $max_breakpoints[ $w ] ] = array(
				'slidesPerView' => $c,
			);
		}

		if ( isset( $col_cnt['xl'] ) ) {
			$extra_options['slidesPerView'] = $col_cnt['xl'];
		} elseif ( isset( $col_cnt['lg'] ) ) {
			$extra_options['slidesPerView'] = $col_cnt['lg'];
		}

		if ( isset( $settings['dots_type'] ) && 'thumb' == $settings['dots_type'] && $id ) {
			$extra_options['pagination'] = false;
			foreach ( $responsive as $w => $c ) {
				$responsive[ $w ]['pagination'] = false;
			}
		}

		$extra_options['breakpoints'] = $responsive;

		$extra_options['statusClass'] = trim( ( empty( $settings['status_class'] ) ? '' : $settings['status_class'] ) . wolmart_get_slider_status_class( $settings ) );

		return $extra_options;
	}
}

if ( ! function_exists( 'wolmart_escaped' ) ) {
	/**
	 * Get already escaped text.
	 *
	 * @since 1.0
	 *
	 * @param string $html_escaped Escaped text
	 *
	 * @return string Original escaped text
	 */
	function wolmart_escaped( $html_escaped ) {
		return $html_escaped;
	}
}

if ( ! function_exists( 'wolmart_get_breakpoints' ) ) {

	/**
	 * Get breakpoints
	 *
	 * @since 1.0
	 *
	 * @param string $screen_mode Screen mode
	 *
	 * @return int|array Breakpoints array or given breakpoint number.
	 */
	function wolmart_get_breakpoints( $screen_mode = '' ) {
		if ( 'min' == $screen_mode ) {
			return 0;
		} elseif ( 'sm' == $screen_mode ) {
			return 576;
		} elseif ( 'md' == $screen_mode ) {
			return 768;
		} elseif ( 'lg' == $screen_mode ) {
			return 992;
		} elseif ( 'xl' == $screen_mode ) {
			return 1200;
		}
		return array(
			'min' => 0,
			'sm'  => 576,
			'md'  => 768,
			'lg'  => 992,
			'xl'  => 1200,
		);
	}
}

if ( ! function_exists( 'wolmart_add_url_parameters' ) ) {
	/**
	 * Add parameters with value to url
	 *
	 * @since 1.0
	 */
	function wolmart_add_url_parameters( $url, $name, $value ) {

		$url_data = parse_url( str_replace( '#038;', '&', $url ) );
		if ( ! isset( $url_data['query'] ) ) {
			$url_data['query'] = '';
		}
		$params = array();
		parse_str( $url_data['query'], $params );
		$params[ $name ]   = $value;
		$url_data['query'] = http_build_query( $params );

		$url = '';

		if ( isset( $url_data['host'] ) ) {

			$url .= $url_data['scheme'] . '://';

			if ( isset( $url_data['user'] ) ) {

				$url .= $url_data['user'];

				if ( isset( $url_data['pass'] ) ) {

					$url .= ':' . $url_data['pass'];
				}

				$url .= '@';

			}

			$url .= $url_data['host'];

			if ( isset( $url_data['port'] ) ) {

				$url .= ':' . $url_data['port'];
			}
		}

		if ( isset( $url_data['path'] ) ) {

			$url .= $url_data['path'];
		}

		if ( isset( $url_data['query'] ) ) {

			$url .= '?' . $url_data['query'];
		}

		if ( isset( $url_data['fragment'] ) ) {

			$url .= '#' . $url_data['fragment'];
		}

		return wolmart_woo_widget_clean_link( $url );
	}
}

if ( ! function_exists( 'wolmart_woo_widget_clean_link' ) ) {
	/**
	 * Get clean link
	 *
	 * @since 1.0
	 */
	function wolmart_woo_widget_clean_link( $link ) {
		return str_replace( '#038;', '&', str_replace( '%2C', ',', $link ) );
	}
}

if ( ! function_exists( 'wolmart_get_template_part' ) ) {
	/**
	 * Include template part
	 *
	 * @since 1.0.0
	 * @param string $slug
	 * @param string $name
	 * @param array $args
	 * @return void
	 */
	function wolmart_get_template_part( $slug, $name = null, $args = array() ) {

		if ( ! defined( 'WOLMART_FRAMEWORK' ) ) {
			return;
		}

		// Add WOLMART_PART to slug, if it hasn't
		if ( WOLMART_PART != substr( $slug, strlen( WOLMART_PART ) ) ) {
			$slug = WOLMART_PART . '/' . $slug;
		}

		// Get template path
		$template = '';
		$name     = (string) $name;
		if ( $name ) {
			$template = locate_template( array( "{$slug}-{$name}.php", "inc/{$slug}-{$name}.php", "framework/{$slug}-{$name}.php" ) );
		}
		if ( ! $template ) {
			$template = locate_template( array( "{$slug}.php", "inc/{$slug}.php", "framework/{$slug}.php" ) );
		}
		$template = apply_filters( 'wolmart_get_template_part', $template, $slug, $name );

		// Extract args and include template
		if ( $template ) {
			if ( ! empty( $args ) && is_array( $args ) ) {
				extract( $args ); // @codingStandardsIgnoreLine
			}
			include $template;
		}
	}
}

if ( ! function_exists( 'wolmart_core_get_template' ) ) {
	/**
	 * Include core template part
	 *
	 * @since 1.0.0
	 * @param string $path
	 * @param array $atts
	 * @return void
	 */
	function wolmart_core_get_template( $path, $atts = array() ) {

		if ( ! defined( 'WOLMART_CORE_FRAMEWORK' ) ) {
			return;
		}

		// Get template path
		$template = locate_template( array( "{$path}.php" ) );
		if ( ! $template ) {
			$fallback = WOLMART_CORE_FRAMEWORK . "/templates/{$path}.php";
			$template = file_exists( $fallback ) ? $fallback : '';
		}
		$template = apply_filters( 'wolmart_core_get_template', $template, $path );

		// Include template
		if ( $template ) {
			include $template;
		}
	}
}


if ( ! function_exists( 'wolmart_get_overlay_class' ) ) {

	/**
	 * Get banner overlay setting
	 *
	 * @since 1.0.0
	 */
	function wolmart_get_overlay_class( $overlay ) {
		if ( 'light' === $overlay ) {
			return 'overlay-light';
		}
		if ( 'dark' === $overlay ) {
			return 'overlay-dark';
		}
		if ( 'zoom' === $overlay ) {
			return 'overlay-zoom';
		}
		if ( 'zoom_light' === $overlay ) {
			return 'overlay-zoom overlay-light';
		}
		if ( 'zoom_dark' === $overlay ) {
			return 'overlay-zoom overlay-dark';
		}
		if ( 0 == strncmp( $overlay, 'effect-', 7 ) ) {
			return 'overlay-' . $overlay;
		}
		return '';
	}
}

/**
 * Echo or Return inline css.
 * This function only uses for composed by style tag.
 *
 * @since 1.2.0
 */
if ( ! function_exists( 'wolmart_filter_inline_css' ) ) :
	function wolmart_filter_inline_css( $inline_css, $is_echo = true ) {
		if ( ! class_exists( 'Wolmart_Optimize_Stylesheets' ) ) {
			return;
		}
		if ( empty( Wolmart_Optimize_Stylesheets::get_instance()->is_merged ) ) { // not merge
			if ( $is_echo ) {
				echo wolmart_escaped( $inline_css );
			} else {
				return $inline_css;
			}
		} else {
			if ( 'no' == Wolmart_Optimize_Stylesheets::get_instance()->has_merged_css() ) {
				global $wolmart_body_merged_css;
				if ( isset( $wolmart_body_merged_css ) ) {
					$inline_css               = str_replace( PHP_EOL, '', $inline_css );
					$inline_css               = preg_replace( '/<style.*?>/s', '', $inline_css ) ? : $inline_css;
					$inline_css               = preg_replace( '/<\/style.*?>/s', '', $inline_css ) ? : $inline_css;
					$wolmart_body_merged_css .= $inline_css;
				}
			}
			return '';
		}
	}
endif;
