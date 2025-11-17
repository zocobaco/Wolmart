<?php
/**
 * Theme Functions
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

/**
 * Get Theme Option
 *
 * @since 1.0.0
 */
function wolmart_get_option( $option, $fallback = '' ) {

	global $wolmart_option;

	if ( ! isset( $wolmart_option ) ) {

		require WOLMART_FRAMEWORK . '/theme-options.php';
		$options_temp = $wolmart_option;

		$theme_mods = get_theme_mods();
		if ( is_array( $theme_mods ) ) {
			$wolmart_option = array_merge( $wolmart_option, $theme_mods );
		}

		if ( empty( $wolmart_option['typo_default'] ) ) {
			$wolmart_option['typo_default'] = $options_temp['typo_default'];
		}

		if ( is_customize_preview() ) {
			try {
				if ( isset( $_POST['customized'] ) ) {
					$modified = json_decode( stripslashes_deep( $_POST['customized'] ), true );

					if ( ! empty( $modified ) && is_array( $modified ) ) {
						foreach ( $modified  as $key => $value ) {

							if ( is_array( $value ) ) {
								$value = array_map( 'stripslashes_deep', $value );
							} else {
								$value = urldecode( $value );
							}
							$wolmart_option[ $key ] = $value;
						}
					}
				}
			} catch ( Exception $e ) {
			}
		}

		if ( empty( $wolmart_option['conditions'] ) ) {
			$wolmart_option['conditions'] = $default_conditions;
		}
	}

	return isset( $wolmart_option[ $option ] ) ? $wolmart_option[ $option ] : $fallback;

}

/**
 * Call remove filter callbacks
 *
 * @since 1.0.0
 */
function wolmart_call_clean_filter( $hook, $callback, $priority = 10 ) {
	if ( function_exists( 'wolmart_clean_filter' ) ) {
		wolmart_clean_filter( $hook, $callback, $priority );
	}
}


/**
 * Get page list
 *
 * @since 1.0.0
 *
 * @return {Array} $pages
 */
function wolmart_get_pages_arr() {
	$pages = array();

	foreach ( get_pages() as $page ) {
		$pages[ $page->ID ] = $page->post_title;
	}

	return $pages;
}


function wolmart_get_social_shares() {
	return apply_filters(
		'wolmart_social_links',
		array(
			'facebook'  => array(
				'title' => esc_html__( 'Facebook', 'wolmart' ),
				'icon'  => 'w-icon-facebook',
				'link'  => 'https://www.facebook.com/sharer.php?u=$permalink',
			),
			'twitter'   => array(
				'title' => esc_html__( 'Twitter', 'wolmart' ),
				'icon'  => 'w-icon-twitter',
				'link'  => 'https://twitter.com/intent/tweet?text=$title&amp;url=$permalink',
			),
			'linkedin'  => array(
				'title' => esc_html__( 'Linkedin', 'wolmart' ),
				'icon'  => 'w-icon-linkedin-in',
				'link'  => 'https://www.linkedin.com/shareArticle?mini=true&amp;url=$permalink&amp;title=$title',
			),
			'email'     => array(
				'title' => esc_html__( 'Email', 'wolmart' ),
				'icon'  => 'w-icon-envelop3',
				'link'  => 'mailto:?subject=$title&amp;body=$permalink',
			),
			'pinterest' => array(
				'title' => esc_html__( 'Pinterest', 'wolmart' ),
				'icon'  => 'w-icon-pinterest-p',
				'link'  => 'https://pinterest.com/pin/create/button/?url=$permalink&amp;media=$image',
			),
			'reddit'    => array(
				'title' => esc_html__( 'Reddit', 'wolmart' ),
				'icon'  => 'w-icon-reddit-alien',
				'link'  => 'http://www.reddit.com/submit?url=$permalink&amp;title=$title',
			),
			'tumblr'    => array(
				'title' => esc_html__( 'Tumblr', 'wolmart' ),
				'icon'  => 'w-icon-tumblr',
				'link'  => 'http://www.tumblr.com/share/link?url=$permalink&amp;name=$title&amp;description=$excerpt',
			),
			'vk'        => array(
				'title' => esc_html__( 'VK', 'wolmart' ),
				'icon'  => 'w-icon-vk',
				'link'  => 'https://vk.com/share.php?url=$permalink&amp;title=$title&amp;image=$image&amp;noparse=true',
			),
			'whatsapp'  => array(
				'title' => esc_html__( 'WhatsApp', 'wolmart' ),
				'icon'  => 'w-icon-whatsapp',
				'link'  => 'whatsapp://send?text=$title-$permalink',
			),
			'xing'      => array(
				'title' => esc_html__( 'Xing', 'wolmart' ),
				'icon'  => 'w-icon-xing',
				'https://www.xing-share.com/app/user?op=share;sc_p=xing-share;url=$permalink',
			),
			'instagram' => array(
				'title' => esc_html__( 'Instagram', 'wolmart' ),
				'icon'  => 'w-icon-instagram',
				'link'  => '',
			),
		)
	);
}

function wolmart_load_google_font( $extra_fonts = array() ) {
	$typos        = array( 'typo_default', 'typo_heading', 'typo_custom1', 'typo_custom2', 'typo_custom3', 'typo_ptb_title', 'typo_ptb_subtitle', 'typo_ptb_breadcrumb' );
	$weights      = array();
	$fonts        = array();
	$google_fonts = class_exists( 'Kirki_Fonts' ) ? Kirki_Fonts::get_google_fonts() : array();

	foreach ( $typos as $typo ) {
		// $family = wolmart_get_option( $typo )['font-family'];
		$family = empty( wolmart_get_option( $typo )['font-family'] ) ? '' : wolmart_get_option( $typo )['font-family'];

		if ( 'inherit' == $family || 'initial' == $family || '' == $family ) {
			continue;
		}

		$t = wolmart_get_option( $typo );

		if ( ! isset( $t['variant'] ) ) {
			$weight = '400';
		} elseif ( 'normal' == $t['variant'] || 'regular' == $t['variant'] ) {
			$weight = '400';
		} elseif ( 'italic' == $t['variant'] ) {
			$weight = '400italic';
		} else {
			$weight = $t['variant'];
		}

		if ( ! array_key_exists( $family, $weights ) ) {
			$weights[ $family ] = array( '300', '400', '500', '600', '700' );
		}

		if ( ! in_array( $weight, $weights[ $family ] ) ) {
			$weights[ $family ][] = $weight;
		}
	}

	global $wolmart_layout;
	if ( ! empty( $wolmart_layout['used_blocks'] ) ) {
		foreach ( $wolmart_layout['used_blocks'] as $block_id => $block_content ) {
			$block_fonts = json_decode( rawurldecode( get_post_meta( $block_id, 'wolmart_vc_google_fonts', true ) ), true );
			if ( ! empty( $block_fonts ) ) {
				$weights = array_merge_recursive( $weights, $block_fonts );
			}
		}
	}

	if ( is_singular() ) {
		$page_id    = get_the_ID();
		$page_fonts = json_decode( rawurldecode( get_post_meta( $page_id, 'wolmart_vc_google_fonts', true ) ), true );

		if ( ! empty( $page_fonts ) ) {
			$weights = array_merge_recursive( $weights, $page_fonts );
		}
	}

	foreach ( $weights as $family => $weight ) {
		foreach ( $weight as &$variant ) {
			if ( 'normal' == $variant || 'regular' == $variant ) {
				$variant = '400';
			} elseif ( 'italic' == $variant ) {
				$variant = '400italic';
			} else {
				$variant = $variant;
			}
		}
		$weight  = array_unique( $weight );
		$fonts[] = str_replace( ' ', '+', $family ) . ( ! empty( $google_fonts ) && isset( $google_fonts[ $family ] ) && 1 >= count( $google_fonts[ $family ]['variants'] ) ? '' : ':' . implode( ',', $weight ) );
	}

	if ( ! empty( $extra_fonts ) ) {
		foreach ( $extra_fonts as $f_family ) {
			if ( ! isset( $weights[ $f_family ] ) ) {
				$fonts[] = str_replace( ' ', '+', $f_family ) . ':300,400,500,600,700';
			}
		}
	}

	if ( $fonts ) {
		if ( is_admin() || wolmart_get_option( 'google_webfont' ) ) {
			$fonts_str = implode( "','", $fonts );
			if ( wolmart_get_option( 'font_face_display' ) ) {
				$fonts_str .= '&display=swap';
			}
			?>
			<script>
				WebFontConfig = {
					google: { families: [ '<?php echo wolmart_strip_script_tags( $fonts_str ); ?>' ] }
				};
				(function(d) {
					var wf = d.createElement('script'), s = d.scripts[0];
					wf.src = '<?php echo WOLMART_JS; ?>/webfont.js';
					wf.async = true;
					s.parentNode.insertBefore(wf, s);
				})(document);
			</script>
			<?php
		} else {
			$g_link = 'https://fonts.googleapis.com/css?family=' . implode( '%7C', $fonts );
			if ( wolmart_get_option( 'font_face_display' ) ) {
				$g_link .= '&display=swap';
			}
			wp_enqueue_style( 'wolmart-google-fonts', $g_link );
		}
	}
}

function wolmart_icl_disp_language( $native_name, $translated_name = false, $lang_native_hidden = false, $lang_translated_hidden = false ) {
	if ( function_exists( 'icl_disp_language' ) ) {
		return icl_disp_language( $native_name, $translated_name, $lang_native_hidden, $lang_translated_hidden );
	}
	$ret = '';

	if ( ! $native_name && ! $translated_name ) {
		$ret = '';
	} elseif ( $native_name && $translated_name ) {
		$hidden1 = '';
		$hidden2 = '';
		$hidden3 = '';
		if ( $lang_native_hidden ) {
			$hidden1 = 'style="display:none;"';
		}
		if ( $lang_translated_hidden ) {
			$hidden2 = 'style="display:none;"';
		}
		if ( $lang_native_hidden && $lang_translated_hidden ) {
			$hidden3 = 'style="display:none;"';
		}

		if ( $native_name != $translated_name ) {
			$ret =
				'<span ' .
				$hidden1 .
				' class="icl_lang_sel_native">' .
				$native_name .
				'</span> <span ' .
				$hidden2 .
				' class="icl_lang_sel_translated"><span ' .
				$hidden1 .
				' class="icl_lang_sel_native">(</span>' .
				$translated_name .
				'<span ' .
				$hidden1 .
				' class="icl_lang_sel_native">)</span></span>';
		} else {
			$ret = '<span ' . $hidden3 . ' class="icl_lang_sel_current">' . esc_html( $native_name ) . '</span>';
		}
	} elseif ( $native_name ) {
		$ret = $native_name;
	} elseif ( $translated_name ) {
		$ret = $translated_name;
	}

	return $ret;
}

function wolmart_get_responsive_cols( $cols, $type = 'product' ) {
	$result = array();
	$base   = $cols['lg'] ? $cols['lg'] : 4;

	if ( 6 < $base ) { // 7, 8
		if ( ! isset( $cols['xl'] ) ) {
			$result = array(
				'xl'  => $base,
				'lg'  => 6,
				'md'  => 4,
				'sm'  => 3,
				'min' => 2,
			);
		} else {
			$result = array(
				'lg'  => $base,
				'md'  => 6,
				'sm'  => 4,
				'min' => 3,
			);
		}
	} elseif ( 4 < $base ) { // 5, 6
		$result = array(
			'lg'  => $base,
			'md'  => 4,
			'sm'  => 3,
			'min' => 2,
		);

		if ( ! isset( $cols['xl'] ) ) {
			$result['xl'] = $base;
			$result['lg'] = 4;
		}
	} elseif ( 2 < $base ) { // 3, 4
		$result = array(
			'lg'  => $base,
			'md'  => 3,
			'sm'  => 2,
			'min' => 2,
		);

		if ( 'post' == $type ) {
			$result['min'] = 1;
		}
	} else { // 1, 2
		$result = array(
			'lg'  => $base,
			'md'  => $base,
			'sm'  => 1,
			'min' => 1,
		);
	}

	foreach ( $cols as $w => $c ) {
		if ( 'lg' != $w && $c > 0 ) {
			$result[ $w ] = $c;
		}
	}

	return apply_filters( 'wolmart_filter_reponsive_cols', $result, $cols );
}

if ( ! function_exists( 'wolmart_get_grid_space' ) ) {

	/**
	 * Get columns' gutter size value from size string
	 *
	 * @since 1.0
	 *
	 * @param string $col_sp Columns gutter size string
	 *
	 * @return int Gutter size value
	 */
	function wolmart_get_grid_space( $col_sp ) {
		if ( 'no' == $col_sp ) {
			return 0;
		} elseif ( 'sm' == $col_sp ) {
			return 10;
		} elseif ( 'lg' == $col_sp ) {
			return 30;
		} elseif ( 'xs' == $col_sp ) {
			return 2;
		} else {
			return 20;
		}
	}
}

if ( ! function_exists( 'wolmart_get_overlay_class' ) ) {
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

if ( ! function_exists( 'wolmart_sanitize_array' ) ) {
	function wolmart_sanitize_array( $arr ) {
		if ( $arr && is_array( $arr ) ) {
			foreach ( $arr as $index => $a ) {
				if ( is_array( $a ) ) {
					$arr[ $index ] = wolmart_sanitize_array( $a );
				} else {
					$arr[ $index ] = sanitize_text_field( $a );
				}
			}
			return $arr;
		} elseif ( $arr ) {
			return sanitize_text_field( $arr );
		}
		return false;
	}
}

if ( ! function_exists( 'wolmart_loadmore_attributes' ) ) {
	function wolmart_loadmore_attributes( $props, $args, $loadmore_type, $max_num_pages, $is_filter_cat = false ) {
		return 'data-load="' . esc_attr(
			json_encode(
				array(
					'props' => $props,
					'args'  => $args,
					'max'   => $max_num_pages,
				)
			)
		) . '"';
	}
}

if ( ! function_exists( 'wolmart_loadmore_html' ) ) {
	function wolmart_loadmore_html( $query, $loadmore_type, $loadmore_label, $loadmore_btn_style = '', $name_prefix = '' ) {
		if ( 'button' == $loadmore_type ) {
			$class = 'btn btn-load ';

			if ( $loadmore_btn_style ) {
				$class .= function_exists( 'wolmart_widget_button_get_class' ) ? implode( ' ', wolmart_widget_button_get_class( $loadmore_btn_style, $name_prefix ) ) : '';
			} else {
				$class .= 'btn-primary';
			}

			$label = empty( $loadmore_label ) ? esc_html__( 'Load More', 'wolmart' ) : esc_html( $loadmore_label );
			echo '<button class="' . esc_attr( $class ) . '">' . ( $loadmore_btn_style && function_exists( 'wolmart_widget_button_get_label' ) ? wolmart_widget_button_get_label( $loadmore_btn_style, null, $label, $name_prefix ) : $label ) . '</button>';
		} elseif ( 'page' == $loadmore_type || ! $loadmore_type ) {
			echo wolmart_get_pagination( $query, 'pagination-load' );
		}
	}
}

if ( ! function_exists( 'wolmart_get_pagination_html' ) ) {
	function wolmart_get_pagination_html( $paged, $total, $class = '' ) {

		$classes = array( 'pagination' );

		// Set up paginated links.
		$args  = apply_filters(
			'wolmart_filter_pagination_args',
			array(
				'current'   => $paged,
				'total'     => $total,
				'end_size'  => 1,
				'mid_size'  => 2,
				'prev_text' => '<i class="w-icon-long-arrow-left"></i> ' . esc_html__( 'Prev', 'wolmart' ),
				'next_text' => esc_html__( 'Next', 'wolmart' ) . ' <i class="w-icon-long-arrow-right"></i>',
			)
		);
		$links = paginate_links( $args );

		if ( $class ) {
			$classes[] = esc_attr( $class );
		}

		if ( $links ) {

			if ( 1 == $paged ) {
				$links = sprintf(
					'<span class="prev page-numbers disabled">%s</span>',
					$args['prev_text']
				) . $links;
			} elseif ( $paged == $total ) {
				$links .= sprintf(
					'<span class="next page-numbers disabled">%s</span>',
					$args['next_text']
				);
			}

			$links = '<div class="' . implode( ' ', $classes ) . '" aria-label="' . esc_attr__( 'Pagination', 'wolmart' ) . '">' . preg_replace( '/^\s+|\n|\r|\s+$/m', '', $links ) . '</div>';
			$links = str_replace( array( 'class="prev page-numbers"', 'class="next page-numbers"' ), array( 'class="prev page-numbers" rel="prev"', 'class="next page-numbers" rel="next"' ), $links ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}

		return $links;
	}
}

if ( ! function_exists( 'wolmart_get_page_links_html' ) ) :
	function wolmart_get_page_links_html() {
		if ( ! is_singular() ) {
			return;
		}
		global $page, $numpages, $multipage;

		if ( $multipage ) {
			global $wp_rewrite;
			$post       = get_post();
			$query_args = array();
			$prev_link  = '';
			$next_link  = '';

			if ( ! get_option( 'permalink_structure' ) || in_array( $post->post_status, array( 'draft', 'pending' ), true ) ) {
				if ( $page + 1 <= $numpages ) {
					$next_link = add_query_arg( 'page', $page + 1, get_permalink() );
				}
				if ( $page > 1 ) {
					$prev_link = add_query_arg( 'page', $page - 1, get_permalink() );
				}
			} elseif ( 'page' === get_option( 'show_on_front' ) && get_option( 'page_on_front' ) == $post->ID ) {
				if ( $page + 1 <= $numpages ) {
					$next_link = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . ( $page + 1 ), 'single_paged' );
				}
				if ( $page > 1 ) {
					$prev_link = trailingslashit( get_permalink() ) . user_trailingslashit( "$wp_rewrite->pagination_base/" . ( $page - 1 ), 'single_paged' );
				}
			} else {
				if ( $page + 1 <= $numpages ) {
					$next_link = trailingslashit( get_permalink() ) . user_trailingslashit( $page + 1, 'single_paged' );
				}
				if ( $page > 1 ) {
					$prev_link = trailingslashit( get_permalink() ) . user_trailingslashit( $page - 1, 'single_paged' );
				}
			}
			if ( $prev_link ) {
				$prev_html_escaped = '<a class="prev page-numbers" href="' . esc_url( $prev_link ) . '"><i class="w-icon-long-arrow-left"></i> ' . esc_html__( 'Prev', 'wolmart' ) . '</a>';
			} else {
				$prev_html_escaped = '<span class="prev page-numbers disabled"><i class="w-icon-long-arrow-left"></i> ' . esc_html__( 'Prev', 'wolmart' ) . '</span>';
			}
			if ( $next_link ) {
				$next_html_escaped = '<a class="next page-numbers" href="' . esc_url( $next_link ) . '">' . esc_html__( 'Next', 'wolmart' ) . ' <i class="w-icon-long-arrow-right"></i></a>';
			} else {
				$next_html_escaped = '<span class="next page-numbers disabled">' . esc_html__( 'Next', 'wolmart' ) . ' <i class="w-icon-long-arrow-right"></i></span>';
			}

			wp_link_pages(
				array(
					'before' => '<div class="pagination-footer"><div class="links pagination">' . $prev_html_escaped,
					'after'  => $next_html_escaped . '</div></div>',
				)
			);
		}
	}
endif;

if ( ! function_exists( 'wolmart_get_pagination' ) ) {
	function wolmart_get_pagination( $query = '', $class = '' ) {

		if ( ! $query ) {
			global $wp_query;
			$query = $wp_query;
		}

		$paged = $query->get( 'paged' ) ? $query->get( 'paged' ) : ( $query->get( 'page' ) ? $query->get( 'page' ) : 1 );
		$total = $query->max_num_pages;

		return wolmart_get_pagination_html( $paged, $total, $class );
	}
}

if ( ! function_exists( 'wolmart_pagination' ) ) {
	function wolmart_pagination( $query = '', $class = '' ) {
		echo wolmart_get_pagination( $query, $class );
	}
}

function wolmart_trim_description( $text = '', $limit = 45, $unit = 'words' ) {
	$content = wp_strip_all_tags( $text );
	$content = strip_shortcodes( $content );

	if ( ! $limit ) {
		$limit = 45;
	}

	if ( ! $unit ) {
		$unit = 'words';
	}

	if ( 'words' == $unit ) {
		$content = wp_trim_words( $content, $limit );
	} else { // by characters
		$affix = ( strlen( $content ) < $limit ? '' : ' ...' );
		if ( function_exists( 'mb_substr' ) ) {
			$content = mb_substr( $content, 0, $limit ) . $affix;
		} else {
			$content = substr( $content, 0, $limit ) . $affix;
		}
	}

	if ( $content ) {
		$content = '<p>' . wp_strip_all_tags( $content ) . '</p>';
	}
	return apply_filters( 'wolmart_filter_trim_description', $content );
}

if ( ! function_exists( 'wolmart_post_comment' ) ) {
	function wolmart_post_comment( $comment, $args, $depth ) {
		$GLOBALS['comment'] = $comment;
		?>
		<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">

			<?php if ( 'pingback' == $comment->comment_type || 'trackback' == $comment->comment_type ) { ?>
			<div id="comment-<?php comment_ID(); ?>" class="comment comment-container">
				<p><?php esc_html_e( 'Pingback:', 'wolmart' ); ?> <span><span><?php comment_author_link( get_comment_ID() ); ?></span></span> <?php edit_comment_link( esc_html__( '(Edit)', 'wolmart' ), '<span class="edit-link">', '</span>' ); ?></p>
			</div>
			<?php } else { ?>
			<div class="comment">
				<figure class="comment-avatar">
					<?php echo get_avatar( $comment, 50 ); ?>
				</figure>

				<div class="comment-text">
					<h4 class="comment-name">
						<?php echo get_comment_author_link( get_comment_ID() ); ?>
					</h4>

					<?php /* translators: %s represents the date of the comment. */ ?>
					<h5 class="comment-date"><?php printf( esc_html__( '%1$s at %2$s', 'wolmart' ), get_comment_date(), get_comment_time() ); ?></h5>
					<?php if ( '0' == $comment->comment_approved ) : ?>
						<em><?php esc_html_e( 'Your comment is awaiting moderation.', 'wolmart' ); ?></em>
						<br />
					<?php endif; ?>
					<?php comment_text(); ?>
					<div class="comment-action">
						<?php
						comment_reply_link(
							array_merge(
								$args,
								array(
									'add_below' => 'comment',
									'depth'     => $depth,
									'max_depth' => $args['max_depth'],
								)
							)
						);
						?>
					</div>
				</div>
			</div>
				<?php
			}
	}
}

if ( ! function_exists( 'wolmart_doing_ajax' ) ) {
	function wolmart_doing_ajax() {
		// WordPress ajax
		if ( wp_doing_ajax() || isset( $_REQUEST['vcv-ajax'] ) ) {
			return true;
		}

		return apply_filters( 'wolmart_core_filter_doing_ajax', false );
	}
}

function wolmart_get_period_from( $time ) {
	$time = time() - $time;     // to get the time since that moment
	$time = ( $time < 1 ) ? 1 : $time;

	$tokens = array(
		31536000 => 'year',
		2592000  => 'month',
		604800   => 'week',
		86400    => 'day',
		3600     => 'hour',
		60       => 'minute',
		1        => 'second',
	);

	foreach ( $tokens as $unit => $text ) {
		if ( $time < $unit ) {
			continue;
		}
		$number_of_units = floor( $time / $unit );
		return $number_of_units . ' ' . $text . ( ( $number_of_units > 1 ) ? 's' : '' );
	}
}



/**
 * Compile Dynamic CSS
 */
function wolmart_compile_dynamic_css( $arg = '', $used_elements = '' ) {
	$css_files = array( 'theme', 'blog', 'single-post', 'shop', 'shop-other', 'single-product' );

	$dynamic = '';

	// "Optimize Wizard/Optimize CSS" needs customizer functions.
	require_once WOLMART_ADMIN . '/customizer/customizer-function.php';

	ob_start();
	include WOLMART_FRAMEWORK . '/admin/customizer/dynamic/dynamic_config.php';

	// Optimize
	if ( 'optimize' == $arg ) {
		if ( is_array( $used_elements ) ) {
			echo '$is_component_optimize: true; $use_map:(';
			foreach ( $used_elements as $used_element => $used ) {
				if ( $used ) {
					echo esc_html( $used_element ) . ': true,';
				}
			}
			echo ');';
		}
	}

	$dynamic = ob_get_clean();

	// Compile CSS
	foreach ( $css_files as $file ) {
		ob_start();

		require WOLMART_PATH . '/assets/sass/theme/' . ( 'theme' == $file ? 'theme' : 'pages/' . $file ) . '.scss';
		$config_scss = '$is_preview: false !default;' . wp_strip_all_tags( str_replace( '// @set_theme_configuration', $dynamic, ob_get_clean() ) );

		$src = WOLMART_PATH . '/assets/sass/theme' . ( 'theme' == $file ? '' : '/pages' );

		$target = wp_upload_dir()['basedir'] . '/wolmart_styles/theme' . ( 'theme' == $file ? '' : '-' . $file ) . '.min.css';

		wolmart_compile_css( $target, $config_scss, $src );
	}
}

function wolmart_compile_css( $target, $config_scss, $src, $optimize = false ) {
	// filesystem
	global $wp_filesystem;
	// Initialize the WordPress filesystem, no more using file_put_contents function
	if ( empty( $wp_filesystem ) ) {
		require_once ABSPATH . '/wp-admin/includes/file.php';
		WP_Filesystem();
	}

	if ( ! class_exists( 'Compiler' ) ) {
		require_once WOLMART_ADMIN . '/customizer/scssphp/scss.inc.php';
	}

	$scss = new ScssPhp\ScssPhp\Compiler();
	$scss->setImportPaths( $src );

	$scss->setFormatter( 'scss_formatter_crunched' );
	// $scss->setFormatter( 'scss_formatter' );

	try {
		$css = $scss->compile( $config_scss );
		// $css = $config_scss;
		$target_path = dirname( $target );

		$css = preg_replace( '/url\(\'(..\/)+/i', "url('" . esc_url( WOLMART_ASSETS ) . '/', $css );

		if ( ! file_exists( $target_path ) ) {
			wp_mkdir_p( $target_path );
		}

		// check file mode and make it writable.
		if ( is_writable( $target_path ) == false ) {
			@chmod( get_theme_file_path( $target ), 0755 ); // phpcs:ignore  WordPress.PHP.NoSilencedErrors.Discouraged
		}
		if ( file_exists( $target ) ) {
			if ( is_writable( $target ) == false ) {
				@chmod( $target, 0755 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
			}
			@unlink( $target );
		}

		$wp_filesystem->put_contents( $target, wolmart_minify_css( $css ), FS_CHMOD_FILE );
	} catch ( Exception $e ) {
		var_dump( $e );
		var_dump( 'error occured while SCSS compiling.' );
	}
}

function wolmart_minify_css( $style ) {
	if ( ! $style ) {
		return;
	}

	// Change ::before, ::after to :before, :after
	$style = str_replace( array( '::before', '::after' ), array( ':before', ':after' ), $style );
	$style = preg_replace( '/\s+/', ' ', $style );
	$style = preg_replace( '/;(?=\s*})/', '', $style );
	$style = preg_replace( '/(,|:|;|\{|}|\*\/|>) /', '$1', $style );
	$style = preg_replace( '/ (,|;|\{|})/', '$1', $style );
	$style = preg_replace( '/(:| )0\.([0-9]+)(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}.${2}${3}', $style );
	$style = preg_replace( '/(:| )(\.?)0(%|em|ex|px|in|cm|mm|pt|pc)/i', '${1}0', $style );
	$style = preg_replace( '/\/\*[^\/]*\*\//', '', $style );
	$style = str_replace( array( '}100%{', ',100%{', ' !important', ' >' ), array( '}to{', ',to{', '!important', '>' ), $style );

	// Trim
	$style = trim( $style );
	return $style;
}

if ( ! function_exists( 'wolmart_get_page_layout' ) ) {

	/**
	 * Get current page's layout name.
	 *
	 * @since   1.0
	 * @return  {string}
	 */
	function wolmart_get_page_layout() {

		$type = get_post_type();

		if ( is_404() ) {
			return 'error';
		} elseif ( wolmart_is_shop() ) { // product archive
			return 'archive_product';
		} elseif ( is_home() || is_archive() || is_search() ) {

			if ( 'post' == $type || 'attachment' == $type || is_search() && 'any' == get_query_var( 'post_type' ) ) {
				return 'archive_post';
			}
			return 'archive_' . $type; // custom post type archive

		} elseif ( is_page() || 'wolmart_template' == $type ) { // single page
			return 'single_page';
		} elseif ( wolmart_is_product() ) { // product single
			return 'single_product';
		} elseif ( is_single() ) {

			if ( 'post' == $type || 'attachment' == $type ) {
				return 'single_post';
			}
			return 'single_' . $type; // custom post type single page

		}

		return '';
	}
}

if ( ! function_exists( 'wolmart_breadcrumb' ) ) {

	/**
	 * Display breadcrumb using WooCommerce.
	 *
	 * @since   1.0
	 */
	function wolmart_breadcrumb() {
		if ( function_exists( 'dokan_is_store_page' ) && dokan_is_store_page() && function_exists( 'yoast_breadcrumb' ) && class_exists( 'WPSEO_Options' ) && WPSEO_Options::get( 'breadcrumbs-enable' ) ) {
			return;
		}
		global $wolmart_layout;

		$wrap_class = 'breadcrumb-container';
		if ( 'full' != $wolmart_layout['breadcrumb_wrap'] ) {
			$wrap_class .= 'container-fluid' == $wolmart_layout['breadcrumb_wrap'] ? ' container-fluid' : ' container';
		}

		echo '<div class="' . esc_attr( $wrap_class ) . '">';
		if ( function_exists( 'rank_math_the_breadcrumbs' ) && ( RankMath\Helper::get_settings( 'general.breadcrumbs' ) ) ) {
			echo '<div class="breadcrumb">';
			rank_math_the_breadcrumbs();
			echo '</div>';
		} elseif ( function_exists( 'woocommerce_breadcrumb' ) ) {
			woocommerce_breadcrumb();
		}
		echo '</div>';
	}
}

function wolmart_is_shop() {
	if ( class_exists( 'WooCommerce' ) && ( is_shop() || is_product_category() || is_product_tag() || ( is_search() && isset( $_POST['post_type'] ) && 'product' == $_POST['post_type'] ) ) ) { // Shop Page
		return apply_filters( 'wolmart_is_shop', true );
	} else {
		$term = get_queried_object();
		if ( class_exists( 'WooCommerce' ) && $term && isset( $term->taxonomy ) &&
			in_array(
				$term->taxonomy,
				get_taxonomies(
					array(
						'object_type' => array( 'product' ),
					),
					'names',
					'and'
				)
			) ) {
			return apply_filters( 'wolmart_is_shop', true );
		}
	}

	return apply_filters( 'wolmart_is_shop', false );
}

function wolmart_is_product() {
	if ( class_exists( 'WooCommerce' ) && is_product() ) {
		return true;
	}
	return apply_filters( 'wolmart_is_product', false );
}

function wolmart_wc_set_loop_prop() {
	// Category Props //////////////////////////////////////////////////////////////////////////////
	wc_set_loop_prop( 'category_type', wolmart_get_option( 'category_type' ) );
	wc_set_loop_prop( 'subcat_cnt', wolmart_get_option( 'subcat_cnt' ) );
	wc_set_loop_prop( 'show_icon', wolmart_get_option( 'category_show_icon' ) );
	wc_set_loop_prop( 'overlay', wolmart_get_option( 'category_overlay' ) );

	// Product Props ///////////////////////////////////////////////////////////////////////////////
	wc_set_loop_prop( 'product_type', wolmart_get_option( 'product_type' ) );
	wc_set_loop_prop( 'show_in_box', wolmart_get_option( 'show_in_box' ) ? 'yes' : 'no' );
	wc_set_loop_prop( 'show_media_shadow', wolmart_get_option( 'show_media_shadow' ) ? 'yes' : 'no' );
	wc_set_loop_prop( 'show_hover_shadow', wolmart_get_option( 'show_hover_shadow' ) ? 'yes' : 'no' );
	if ( ! wolmart_wc_get_loop_prop( 'widget' ) ) {
		wc_set_loop_prop( 'show_progress', wolmart_get_option( 'show_progress' ) );
	}
	wc_set_loop_prop( 'is_popup', wolmart_get_option( 'is_popup' ) );

	if ( wolmart_is_shop() || wolmart_is_product() ) {
		wc_set_loop_prop( 'show_labels', array( 'hot', 'sale', 'new', 'stock' ) );
	}

	global $wolmart_layout;
	$info   = wolmart_get_option( 'show_info' );
	$info[] = 'countdown';

	wc_set_loop_prop( 'show_info', $info );
}

if ( ! function_exists( 'wolmart_wc_get_loop_prop' ) ) {
	/**
	 * Gets a property from the woocommerce_loop global.
	 *
	 * @since 1.1
	 * @param string $prop Prop to get.
	 * @param string $default Default if the prop does not exist.
	 * @return mixed
	 */
	function wolmart_wc_get_loop_prop( $prop, $default = '' ) {

		if ( empty( $GLOBALS['woocommerce_loop'] ) ) {
			wc_setup_loop(); // Ensure shop loop is setup.
		}

		return isset( $GLOBALS['woocommerce_loop'], $GLOBALS['woocommerce_loop'][ $prop ] ) ? $GLOBALS['woocommerce_loop'][ $prop ] : $default;
	}
}

if ( ! function_exists( 'wolmart_wc_show_info_for_role' ) ) {
	/**
	 * wolmart_wc_show_info_for_role
	 *
	 * checks if current user can see product info item
	 *
	 * @since 1.0.1
	 */
	function wolmart_wc_show_info_for_role( $item ) {
		$show_info = wolmart_wc_get_loop_prop( 'show_info', false );

		if ( is_array( $show_info ) && ! in_array( $item, $show_info ) ) { // if item is not in show_info list, return false
			return false;
		}

		if ( ! wolmart_get_option( 'change_product_info_role' ) ) { // if different role option is not enabled, return true
			return true;
		}

		$access_roles  = wolmart_get_option( 'product_role_info_' . $item );
		$current_roles = wp_get_current_user()->roles;
		if ( empty( $current_roles ) ) {
			$current_roles[] = 'visitor';
		}

		foreach ( $current_roles as $role ) {
			if ( in_array( $role, $access_roles ) ) {
				return true;
			}
		}

		return false;
	}
}



if ( ! function_exists( 'wolmart_print_mobile_bar' ) ) {

	/**
	 * Print wolmart mobile navigation bar
	 *
	 * @since 1.0.0
	 * @since 1.6.0 - Added Mobile Floating Button Type
	 */
	function wolmart_print_mobile_bar() {
		$mobile_bar_type = wolmart_get_option( 'mobile_bar_type', false );

		if ( 'bottom' == $mobile_bar_type ) {
			$mobile_bar = wolmart_get_option( 'mobile_bar_icons' );
			$result     = '';
			$cnt        = 0;

			if ( ! is_array( $mobile_bar ) ) {
				return;
			}

			foreach ( $mobile_bar as $item ) {
				$icon  = wolmart_get_option( 'mobile_bar_' . $item . '_icon' );
				$label = wolmart_get_option( 'mobile_bar_' . $item . '_label' );

				if ( 'menu' == $item ) {
					if ( wolmart_get_option( 'mobile_menu_items' ) ) {
						$result .= '<a href="#" class="mobile-menu-toggle mobile-item"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
						++ $cnt;
					}
				} elseif ( 'home' == $item ) {
					$result .= '<a href="' . esc_url( home_url() ) . '" class="mobile-item' . ( is_front_page() ? ' active' : '' ) . '"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
					++ $cnt;
				} elseif ( 'shop' == $item && class_exists( 'WooCommerce' ) ) {
					$item_class   = 'mobile-item mobile-item-categories';
					$mobile_menus = wolmart_get_option( 'mobile_menu_items' );

					if ( ! empty( $mobile_menus ) && count( $mobile_menus ) ) {
						foreach ( $mobile_menus as $menu ) {
							if ( 'categories' == $menu ) {
								$item_class .= ' show-categories-menu';
							}
						}
					}
					$result .= '<a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="' . esc_attr( $item_class ) . '"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
					++ $cnt;
				} elseif ( 'wishlist' == $item && class_exists( 'WooCommerce' ) && defined( 'YITH_WCWL' ) ) {
					$result .= '<a href="' . esc_url( YITH_WCWL()->get_wishlist_url() ) . '" class="mobile-item"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
					++ $cnt;
				} elseif ( 'compare' == $item && class_exists( 'WooCommerce' ) ) {
					$result .= '<a href="' . esc_url( wc_get_page_permalink( 'compare' ) ) . '" class="mobile-item compare-open"><i class="w-icon-compare"></i><span>' . esc_html__( 'Compare', 'wolmart' ) . '</span></a>';
					$cnt ++;
				} elseif ( 'account' == $item && class_exists( 'WooCommerce' ) ) {
					$result .= '<a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" class="mobile-item"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
					++ $cnt;
				} elseif ( 'cart' == $item && class_exists( 'WooCommerce' ) ) {
					ob_start();
					$cart_qty = WC()->cart->cart_contents_count;
					$cart_qty = ( $cart_qty > 0 ? $cart_qty : '0' );
					?>
	
					<div class="dropdown cart-dropdown dir-up badge-type">
						<a class="cart-toggle mobile-item" href="<?php echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>" role="button">
							<i class="<?php echo esc_attr( $icon ); ?>">
								<span class="cart-count"> <?php echo esc_html( (int) $cart_qty ); ?></span>
							</i>
							<span><?php echo esc_html( $label ); ?></span>
						</a>
					</div>
	
					<?php
					$result .= ob_get_clean();
					++ $cnt;
				} elseif ( 'search' == $item ) {
					ob_start();
					?>
					<div class="search-wrapper hs-toggle rect">
						<a href="#" class="search-toggle mobile-item" role="button"><i class="<?php echo esc_attr( $icon ); ?>"></i><span><?php echo esc_html( $label ); ?></span></a>
						<form action="<?php echo esc_url( home_url() ); ?>/" method="get" class="input-wrapper">
							<input type="hidden" name="post_type" value="<?php echo esc_attr( wolmart_get_option( 'search_post_type' ) ); ?>"/>
							<input type="search" class="form-control" name="s" placeholder="<?php echo esc_attr( esc_html__( 'Search', 'wolmart' ) ); ?>" required="" autocomplete="off">
	
							<?php if ( wolmart_get_option( 'live_search' ) ) : ?>
								<div class="live-search-list"></div>
							<?php endif; ?>
	
							<button class="btn btn-search" type="submit" aria-label="<?php esc_attr_e( 'Search', 'wolmart' ); ?>">
								<i class="w-icon-search"></i>
							</button>
						</form>
					</div>
					<?php
					$result .= ob_get_clean();
				} elseif ( 'top' == $item ) {
					$result .= '<a href="#" class="mobile-item scroll-top"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
					++ $cnt;
				} else {
					$result .= '<a href="#" class="mobile-item"><i class="' . $icon . '"></i><span>' . $label . '</span></a>';
					++ $cnt;
				}
			}

			if ( $result ) {
				echo '<div class="mobile-icon-bar sticky-content fix-bottom items-' . $cnt . '">' . $result . '</div>';
			}
		} elseif ( 'floating' == $mobile_bar_type && wp_is_mobile() ) {
			$mobile_bar = wolmart_get_option( 'mobile_bar_icons' );
			$result     = '';
			$cnt        = 0;

			ob_start();
			?>
			<div id="floating-snap-btn-wrapper">
				<!-- BEGIN :: Floating Button -->
				<div class="fab-btn">
					<?php
					if ( '' == wolmart_get_option( 'mobile_floating_button_type' ) ) {
						?>
							<svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px"
								viewBox="0 0 70 70" style="enable-background:new 0 0 70 70;" xml:space="preserve">
								<path d="M32.17,61.77c-20.86,0-26.6-17.73-26.6-24.71c0-6.98,3.71-26.81,25.16-26.81c20.1,0,23.81,13.7,23.81,13.7h14.78
									c0,0-9.96-21.1-35.04-21.1C9.2,2.85,0,21.17,0,36.14c0,19.08,17.06,31.01,33.35,31.01c16.3,0,25.08-11.51,25.08-11.51H48.05
									C48.05,55.63,44.08,61.77,32.17,61.77z M58.01,28.4L53.2,42.44c0,0-0.59,3.19-2.45,3.36c-1.86,0.17-5.57-0.25-5.57-0.25l6-17.15
									H39.1l-5.49,16.81l-6.76,0.34l7.09-17.15H21.28l-5.74,16.31c0,0-2.45,6.72,1.94,6.72c4.39,0,8.78,0.04,8.78,0.04
									s3.72-0.37,6.16-2.14c2.2-1.59,1.52,2.02,4.48,2.1c2.96,0.08,20.27,0,20.27,0s4.81,0.17,7.77-6.89C67.89,37.48,70,28.4,70,28.4
									H58.01z"/>
							</svg>
						<?php
					} elseif ( 'plus' == wolmart_get_option( 'mobile_floating_button_type' ) ) {
						?>
							<i class="w-icon-plus"></i>
						<?php
					} elseif ( 'hamburger' == wolmart_get_option( 'mobile_floating_button_type' ) ) {
						?>
						<i class="w-icon-hamburger"></i>
						<?php
					} elseif ( 'custom' == wolmart_get_option( 'mobile_floating_button_type' ) ) {
						echo wolmart_get_option( 'mobile_floating_button_custom' );
					}
					?>
					<i class="w-icon-times fab-close-icon"></i>
				</div>
				<!-- END :: Floating Button --> 
				<!-- BEGIN :: Expand Section -->
				<ul class="floating-icons-wrapper">
					<?php
					foreach ( $mobile_bar as $item ) {
						$icon  = wolmart_get_option( 'mobile_bar_' . $item . '_icon' );
						$label = wolmart_get_option( 'mobile_bar_' . $item . '_label' );

						if ( 'menu' == $item ) {
							if ( wolmart_get_option( 'mobile_menu_items' ) ) {
								$result .= '<li><a href="#" class="mobile-menu-toggle mobile-floating-item" aria-label="' . esc_html__( 'Mobile Menu Toggle', 'wolmart' ) . '"><i class="' . $icon . '"></i></a></li>';
								++ $cnt;
							}
						} elseif ( 'home' == $item ) {
							$result .= '<li><a href="' . esc_url( home_url() ) . '" class="mobile-floating-item' . ( is_front_page() ? ' active' : '' ) . '" aria-label="' . esc_html__( 'Home', 'wolmart' ) . '"><i class="' . $icon . '"></i></a></li>';
							++ $cnt;
						} elseif ( 'shop' == $item && class_exists( 'WooCommerce' ) ) {
							$item_class   = 'mobile-floating-item mobile-floating-item-categories';
							$mobile_menus = wolmart_get_option( 'mobile_menu_items' );

							if ( ! empty( $mobile_menus ) && count( $mobile_menus ) ) {
								foreach ( $mobile_menus as $menu ) {
									if ( 'categories' == $menu ) {
										$item_class .= ' show-categories-menu';
									}
								}
							}
							$result .= '<li><a href="' . esc_url( wc_get_page_permalink( 'shop' ) ) . '" class="' . esc_attr( $item_class ) . '" aria-label="' . esc_html__( 'Shop', 'wolmart' ) . '"><i class="' . $icon . '"></i></a></li>';
							++ $cnt;
						} elseif ( 'wishlist' == $item && class_exists( 'WooCommerce' ) && defined( 'YITH_WCWL' ) ) {
							$result .= '<li><a href="' . esc_url( YITH_WCWL()->get_wishlist_url() ) . '" class="mobile-floating-item" aria-label="' . esc_html__( 'Wishlist', 'wolmart' ) . '"><i class="' . $icon . '"></i></a></li>';
							++ $cnt;
						} elseif ( 'compare' == $item && class_exists( 'WooCommerce' ) ) {
							$result .= '<li><a href="' . esc_url( wc_get_page_permalink( 'compare' ) ) . '" class="mobile-floating-item compare-open" aria-label="' . esc_html__( 'Compare', 'wolmart' ) . '"><i class="w-icon-compare"></i></a></li>';
							$cnt ++;
						} elseif ( 'account' == $item && class_exists( 'WooCommerce' ) ) {
							$result .= '<li><a href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '" class="mobile-floating-item" aria-label="' . esc_html__( 'Account', 'wolmart' ) . '"><i class="' . $icon . '"></i></a></li>';
							++ $cnt;
						} elseif ( 'cart' == $item && class_exists( 'WooCommerce' ) ) {
							ob_start();
							$cart_qty = WC()->cart->cart_contents_count;
							$cart_qty = ( $cart_qty > 0 ? $cart_qty : '0' );
							?>
			
							<li>
								<a class="cart-toggle mobile-floating-item" href="<?php echo esc_url( wc_get_page_permalink( 'cart' ) ); ?>" aria-label="<?php esc_attr_e( 'Cart', 'wolmart' ); ?>" role="button">
									<i class="<?php echo esc_attr( $icon ); ?>"></i>
									<span class="cart-count"> <?php echo esc_html( (int) $cart_qty ); ?></span>
								</a>
							</li>
			
							<?php
							$result .= ob_get_clean();
							++ $cnt;
						} elseif ( 'search' == $item ) {
							ob_start();
							?>
								<li><a href="#" class="search-toggle mobile-floating-item" aria-label="<?php esc_attr_e( 'Search Toggle', 'wolmart' ); ?>" role="button"><i class="<?php echo esc_attr( $icon ); ?>"></i></a></li>
							<?php
							$result .= ob_get_clean();
						} elseif ( 'top' == $item ) {
							$result .= '<li><a href="#" class="mobile-floating-item" id="scroll-top" aria-label="' . esc_html__( 'Scroll Top', 'wolmart' ) . '" role="button"><i class="' . $icon . '"></i></a></li>';
							++ $cnt;
						} else {
							$result .= '<li><a href="#" class="mobile-floating-item" aria-label="' . esc_html__( 'Mobile Floating Item', 'wolmart' ) . '"><i class="' . $icon . '"></i></a></li>';
							++ $cnt;
						}
					}

					echo $result;
					?>
				</ul>
				<!-- END :: Expand Section -->
			</div>
			<?php
		}
	}
}


/**
 * Print Wolmart Block
 */
function wolmart_print_template( $block_name ) {
	if ( $block_name && defined( 'WOLMART_CORE_PATH' ) ) {
		$atts = array( 'name' => $block_name );
		require wolmart_core_path( '/widgets/block/render-block.php' );
	}
}

/**
 * Check Used Blocks
 */
if ( ! function_exists( 'wolmart_get_page_blocks' ) ) :
	function wolmart_get_page_blocks() {
		global $wolmart_layout;
		$used_blocks = array();
		if ( empty( $wolmart_layout ) || ! isset( $wolmart_layout['header'] ) ) {
			return array();
		}

		if ( ! empty( $wolmart_layout['used_blocks'] ) ) {
			return $wolmart_layout['used_blocks'];
		}

		// blocks set from layout builder
		$fields = array( 'header', 'footer', 'ptb', 'top_block', 'inner_top_block', 'inner-bottom-block', 'bottom-block', 'top_bar', 'popup', 'error_block' );
		foreach ( $fields as $field ) {
			if ( ! empty( $wolmart_layout[ $field ] ) && 'hide' != $wolmart_layout[ $field ] ) {
				$used_blocks[] = $wolmart_layout[ $field ];
			}
		}

		// Sidebar blocks
		$widgets         = array();
		$sidebar_widgets = get_option( 'sidebars_widgets' );
		if ( isset( $wolmart_layout['top_sidebar'] ) && isset( $sidebar_widgets[ $wolmart_layout['top_sidebar'] ] ) ) {
			$widgets = array_merge( $widgets, $sidebar_widgets[ $wolmart_layout['top_sidebar'] ] );
		}
		if ( isset( $wolmart_layout['left_sidebar'] ) && isset( $sidebar_widgets[ $wolmart_layout['left_sidebar'] ] ) ) {
			$widgets = array_merge( $widgets, $sidebar_widgets[ $wolmart_layout['left_sidebar'] ] );
		}
		if ( isset( $wolmart_layout['right_sidebar'] ) && isset( $sidebar_widgets[ $wolmart_layout['right_sidebar'] ] ) ) {
			$widgets = array_merge( $widgets, $sidebar_widgets[ $wolmart_layout['right_sidebar'] ] );
		}
		if ( ! empty( $widgets ) ) {
			global $wp_registered_widgets;
			foreach ( $widgets as $key ) {
				if ( isset( $wp_registered_widgets[ $key ] ) && isset( $wp_registered_widgets[ $key ]['callback'] ) ) {
					$widget_obj = $wp_registered_widgets[ $key ]['callback'][0];
					if ( ! empty( $widget_obj ) && 'block-widget' == $widget_obj->id_base ) {
						$args = get_option( $widget_obj->option_name );
						if ( ! empty( $args[ $widget_obj->number ] ) ) {
							$used_blocks[] = $args[ $widget_obj->number ]['id'];
						}
					}
				}
			}
		}

		// single product layout builder
		if ( wolmart_is_product() &&
			! empty( $wolmart_layout['single_product_type'] ) && 'builder' == $wolmart_layout['single_product_type'] &&
			! empty( $wolmart_layout['single_product_template'] ) ) {
			$used_blocks[] = $wolmart_layout['single_product_template'];
		}

		// blocks in page-content
		if ( is_singular() ) {
			$page_blocks = get_post_meta( get_the_ID(), '_wolmart_vc_blocks_content', true );
			if ( ! empty( $page_blocks ) && is_array( $page_blocks ) ) {
				$used_blocks = array_merge( $used_blocks, $page_blocks );
			}
		}

		return array_fill_keys(
			array_unique( $used_blocks, SORT_NUMERIC ),
			array(
				'css' => false,
				'js'  => false,
			)
		);
	}
endif;

if ( ! function_exists( 'wolmart_print_popup_template' ) ) {
	function wolmart_print_popup_template( $popup_id ) {
		global $wolmart_layout;

		if ( defined( 'ELEMENTOR_VERSION' ) && ! empty( get_post_meta( $popup_id, '_elementor_data', true ) ) ) {
			$settings = get_post_meta( $popup_id, '_elementor_page_settings', true );
		} elseif ( defined( 'WPB_VC_VERSION' ) ) {
			$settings = get_post_meta( $popup_id, 'popup_options', true );
			if ( $settings && ! is_array( $settings ) ) {
				$settings = json_decode( $settings, true );
			}

			if ( $settings && is_array( $settings ) ) {
				// Add style from popup options
				$selector = '.mfp-wolmart-' . $popup_id;
				$style    = $selector . ' .popup {width: ' . (int) $settings['width'] . 'px;';
				if ( $settings['top'] ) {
					$style .= 'margin-top: ' . (int) $settings['top'] . 'px;';
				}
				if ( $settings['right'] ) {
					$style .= 'margin-right: ' . (int) $settings['right'] . 'px;';
				}
				if ( $settings['bottom'] ) {
					$style .= 'margin-bottom: ' . (int) $settings['bottom'] . 'px;';
				}
				if ( $settings['left'] ) {
					$style .= 'margin-left: ' . (int) $settings['left'] . 'px;';
				}
				$style .= '}';
				$style .= $selector . ' .mfp-content{';
				if ( ! empty( $settings['h_pos'] ) ) {
					$style .= 'justify-content:' . esc_attr( $settings['h_pos'] ) . ';';
				}
				if ( ! empty( $settings['h_pos'] ) ) {
					$style .= 'align-items: ' . esc_attr( $settings['v_pos'] ) . ';';
				}
				$style .= '}';
				if ( ! empty( $settings['border'] ) ) {
					$style .= $selector . ' .popup .wolmart-popup-content {border-radius: ' . (int) $settings['border'] . 'px;}';
				}
				ob_start();
				echo '<style>' . wolmart_minify_css( $style ) . '</style>';
				wolmart_filter_inline_css( ob_get_clean() );
			}
		}

		$params = array(
			'show_on' => isset( $settings['popup_show_on'] ) ? $settings['popup_show_on'] : 'page_load',
		);

		if ( 'page_load' == $params['show_on'] ) {
			if ( isset( $settings['popup_page_load_delay'] ) ) {
				$params['delay'] = $settings['popup_page_load_delay'];
			} elseif ( isset( $wolmart_layout['popup_delay'] ) ) {
				$params['delay'] = $wolmart_layout['popup_delay'];
			} else {
				$params['delay'] = 0;
			}
		} elseif ( 'page_scroll' == $params['show_on'] ) {
			$params['scroll_dir']    = isset( $settings['popup_page_scroll_dir'] ) ? $settings['popup_page_scroll_dir'] : 'down';
			$params['scroll_amount'] = isset( $settings['popup_page_scroll_amount']['size'] ) ? $settings['popup_page_scroll_amount']['size'] : 50;
		} elseif ( 'scroll_element' == $params['show_on'] ) {
			$params['scroll_element_selector'] = isset( $settings['popup_scroll_element_selector'] ) ? $settings['popup_scroll_element_selector'] : '';
		} elseif ( 'click_counts' == $params['show_on'] ) {
			$params['click_count'] = isset( $settings['popup_click_counts'] ) ? $settings['popup_click_counts'] : 1;
		} elseif ( 'click_element' == $params['show_on'] ) {
			$params['click_element_selector'] = isset( $settings['popup_click_element_selector'] ) ? $settings['popup_click_element_selector'] : '';
		}

		echo '<div id="wolmart-popup-' . $popup_id . '" class="popup" data-popup-options=' . "'" . json_encode(
			array(
				'popup_id'        => $popup_id,
				'popup_params'    => $params,
				'popup_animation' => isset( $settings['popup_animation'] ) ? $settings['popup_animation'] : 'fadeIn',
				'popup_duration'  => isset( $settings['popup_anim_duration'] ) ? $settings['popup_anim_duration'] . 'ms' : '400ms',
			)
		) . "'" . ' style="display: none">';

		echo '<div class="wolmart-popup-content">';

		wolmart_print_template( $popup_id );

		echo '</div>';

		echo '</div>';
	}
}


/**
 * Clear wolmart transient
 *
 * @since 1.0
 */
if ( ! function_exists( 'wolmart_clear_transient' ) ) {
	function wolmart_clear_transient() {
		global $wpdb;

		$cache_key        = 'wolmart-clear-brand-transient';
		$brand_transients = wp_cache_get( $cache_key, 'wolmart-transient' );
		$results          = array();

		if ( false == $brand_transients ) {
			$brand_transients = $wpdb->get_results(
				"SELECT 
					AVG(rating) AS rating,
					term_taxonomy_id,
					term_id,
					COUNT(temp1.post_id) AS review_count
				FROM
					(SELECT 
						post_id,
						meta_value AS rating 
					FROM
						wp_2_postmeta AS pm 
						INNER JOIN wp_2_posts AS wpp 
						ON pm.post_id = wpp.ID 
					WHERE wpp.post_type = 'product' 
						AND pm.meta_key = '_wc_average_rating' 
					GROUP BY post_id) AS temp1 
					LEFT JOIN 
						(SELECT 
							wptt.term_taxonomy_id,
							term_id,
							wtr.object_id AS post_id 
						FROM
							wp_2_term_taxonomy AS wptt 
							INNER JOIN wp_2_term_relationships AS wtr 
								ON wptt.term_taxonomy_id = wtr.term_taxonomy_id 
						WHERE wptt.taxonomy = 'product_brand' 
						GROUP BY post_id) AS temp2 
						ON temp1.post_id = temp2.post_id
				WHERE term_id IS NOT NULL 
				GROUP BY term_id"
			);

			wp_cache_set( $cache_key, $brand_transients, 'wolmart-transient', 3600 * 6 );
		}

		if ( is_array( $brand_transients ) && count( $brand_transients ) > 0 ) {
			foreach ( $brand_transients as $transient ) {
				update_term_meta( $transient->term_id, 'review_count', absint( $transient->review_count ) );
				update_term_meta( $transient->term_id, 'rating', abs( round( $transient->rating, 2 ) ) );
			}
		}
	}
}


/**
 * Get list of templates and sidebars
 *
 * @since 1.0
 */
function wolmart_set_global_templates_sidebars() {

	global $wp_registered_sidebars;
	global $wolmart_templates;

	$template_types    = array( 'header', 'footer', 'popup', 'block', 'product_layout' );
	$wolmart_templates = array();

	// Get Wolmart Templates
	foreach ( $template_types as $template_type ) {
		$posts = get_posts(
			array(
				'post_type'   => 'wolmart_template',
				'meta_key'    => 'wolmart_template_type',
				'meta_value'  => $template_type,
				'numberposts' => -1,
			)
		);
		sort( $posts );

		foreach ( $posts as $post ) {
			$wolmart_templates[ $template_type ][ $post->ID ] = $post->post_title;
		}
	}

	// Get Sidebars
	$wolmart_templates['sidebar'] = array();
	foreach ( $wp_registered_sidebars as $id => $sidebar ) {
		$wolmart_templates['sidebar'][ $id ] = $sidebar['name'];
	}
}

/**
 * Unset list of templates and sidebars.
 *
 * @since 1.0
 */
function wolmart_unset_global_templates_sidebars() {
	unset( $GLOBALS['wolmart_templates'] );
}

if ( ! function_exists( 'wolmart_check_file_write_permission' ) ) {
	function wolmart_check_file_write_permission( $filename ) {
		if ( is_writable( dirname( $filename ) ) == false ) {
			@chmod( dirname( $filename ), 0755 );
		}
		if ( file_exists( $filename ) ) {
			if ( is_writable( $filename ) == false ) {
				@chmod( $filename, 0755 );
			}
			@unlink( $filename );
		}
	}
}


/**
 * Get page object by Title
 *
 * @since 1.5.0
 *
 * @param {String} $title
 * @return {Object} $page_got_by_title
 */
if ( ! function_exists( 'wolmart_get_page_by_title' ) ) {
	function wolmart_get_page_by_title( $title ) {
		$query = new WP_Query(
			array(
				'post_type'              => 'page',
				'title'                  => $title,
				'post_status'            => 'all',
				'posts_per_page'         => 1,
				'no_found_rows'          => true,
				'ignore_sticky_posts'    => true,
				'update_post_term_cache' => false,
				'update_post_meta_cache' => false,
				'orderby'                => 'post_date ID',
				'order'                  => 'ASC',
			)
		);

		if ( ! empty( $query->post ) ) {
			$page_got_by_title = $query->post;
		} else {
			$page_got_by_title = null;
		}

		return $page_got_by_title;
	}
}

/**
 * Wolmart Register Defer Scripts
 *
 * @since 1.6.0
 */
if ( ! function_exists( 'wolmart_register_defer_scripts' ) ) {
	function wolmart_register_defer_script( $handle, $src, $deps = array(), $ver = false, $in_footer = false ) {
		add_filter(
			'wolmart_defer_scripts',
			function( $handles ) use ( $handle ) {
				$handles[] = $handle;

				return $handles;
			}
		);

		wp_register_script( $handle, $src, $deps, $ver, $in_footer );
	}
}

/**
 * Wolmart Mobile Scripts
 *
 * @since 1.6.0
 * @param String $handle  JavaScript file handle
 * @param String $src  JavaScript file src
 * @return Array $scripts
 */
if ( ! function_exists( 'wolmart_register_mobile_script' ) ) {
	function wolmart_register_mobile_script( $handle, $src ) {
		add_filter(
			'wolmart_register_mobile_scripts',
			function( $scripts ) use ( $handle, $src ) {
				$scripts[] = array(
					'handle' => $handle,
					'src'    => $src,
				);

				return $scripts;
			}
		);
	}
}
