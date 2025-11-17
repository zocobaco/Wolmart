<?php
if ( ! defined( 'ABSPATH' ) ) {
	die();
}
global $wolmart_used_elements;

// Available Post Types
$available_post_types = apply_filters(
	'wolmart_optimize_available_post_types',
	array(
		'post',
		'page',
		'wp_block',
		'elementor_library',
		'product',
		'product_variation',
		'wolmart_template',
		'vcv_templates',
		'wpcf7_contact_form',
	)
);

// Component Patterns
$component_patterns = array(
	'accordion'              => array(
		'e' => '"use_as":"accordion"',
		'v' => '"wolmartAccordion"',
		'c' => 'accordion',
	),
	'banner'                 => array(
		'e' => array( '"use_as":"banner"', 'wolmart_widget_banner' ),
		'v' => '"wolmartBanner"',
	),
	'carousel'               => 1,
	'category-type-default'  => array(
		'e' => '"category_type":""',
		'v' => '"categoryType":""',
		'g' => '"category_type":""',
		'c' => false,
	),
	'category-type-frame'    => array(
		'e' => '"category_type":"frame"',
		'v' => '"categoryType":"frame"',
		'g' => '"category_type":"frame"',
		'c' => false,
	),
	'category-type-banner'   => array(
		'e' => '"category_type":"banner"',
		'v' => '"categoryType":"banner"',
		'g' => '"category_type":"banner"',
		'c' => false,
	),
	'category-type-simple'   => array(
		'e' => '"category_type":"simple"',
		'v' => '"categoryType":"simple"',
		'g' => '"category_type":"simple"',
		'c' => false,
	),
	'category-type-icon'     => array(
		'e' => '"category_type":"icon"',
		'v' => '"categoryType":"icon"',
		'g' => '"category_type":"icon"',
		'c' => false,
	),
	'category-type-classic'  => array(
		'e' => '"category_type":"classic"',
		'v' => '"categoryType":"classic"',
		'g' => '"category_type":"classic"',
		'c' => false,
	),
	'category-type-classic2' => array(
		'e' => '"category_type":"classic-2"',
		'v' => '"categoryType":"classic-2"',
		'g' => '"category_type":"classic-2"',
		'c' => false,
	),
	'category-type-ellipse'  => array(
		'e' => '"category_type":"ellipse"',
		'v' => '"categoryType":"ellipse"',
		'g' => '"category_type":"ellipse"',
		'c' => false,
	),
	'category-type-ellipse2' => array(
		'e' => '"category_type":"ellipse-2"',
		'v' => '"categoryType":"ellipse-2"',
		'c' => false,
	),
	'category-type-group'    => array(
		'e' => '"category_type":"group"',
		'v' => '"categoryType":"group"',
		'g' => '"category_type":"group"',
		'c' => false,
	),
	'category-type-group2'   => array(
		'e' => '"category_type":"group-2"',
		'v' => '"categoryType":"group-2"',
		'g' => '"category_type":"group-2"',
		'c' => false,
	),
	'category-type-block'    => array(
		'e' => '"category_type":"label"',
		'v' => '"categoryType":"label"',
		'g' => '"category_type":"label"',
		'c' => false,
	),
	'countdown'              => array(
		'e' => '"wolmart_widget_countdown"',
		'v' => '"wolmartCounterDown"',
		// 'c' => false,
	),
	'hotspot'                => array(
		'e' => 'wolmart_widget_hotspot',
		'v' => 'wolmartHotSpot',
		'c' => false,
	),
	'icon-box'               => array(
		'g' => 'wp:wolmart/wolmart-icon-box',
		'v' => '"wolmartInfoBox"',
		'c' => 0, // Do not find class
	),
	'image-box'              => array(
		'e' => 'wolmart_widget_imagebox',
		// 'c' => false,
	),
	'tab'                    => array(
		'e' => '"use_as":"accordion"',
		'v' => '"wolmartTab"',
	),
	'testimonial'            => array(
		'e' => 'wolmart_widget_testimonial',
		'v' => '"wolmartTestimonial"',
	),
	'vendor'                 => array(
		'e' => 'wolmart_widget_vendors',
	),
);

// Helper classes
$helper_classes = array(
	'w-25',
	'w-50',
	'w-75',
	'w-100',
	'h-100',
	'd-none',
	'd-block',
	'd-inline-block',
	'd-flex',
	'd-inline-flex',
	'justify-content-center',
	'justify-content-start',
	'justify-content-end',
	'justify-content-between',
	'align-items-start',
	'align-items-center',
	'align-items-end',
	'flex-column',
	'flex-wrap',
	'flex-1',
	'overflow-hidden',
	'vertical-top',
	'vertical-main',
	'vertical-bottom',
	'd-sm-none',
	'd-sm-block',
	'd-sm-flex',
	'd-md-none',
	'd-md-block',
	'd-md-flex',
	'd-lg-none',
	'd-lg-block',
	'd-lg-flex',
	'd-xl-none',
	'd-xl-block',
	'd-xl-flex',
	'font-primary',
	'font-secondary',
	'font-tertiary',
	'font-weight-bold',
	'font-weight-semi-bold',
	'font-weight-normal',
	'text-uppercase',
	'text-capitalize',
	'text-normal',
	'font-italic',
	'font-normal',
	'text-white',
	'text-light',
	'text-grey',
	'text-body',
	'text-primary',
	'text-secondary',
	'text-success',
	'text-warning',
	'text-danger',
	'text-light',
	'text-dark',
	'text-black',
	'ls-s',
	'ls-m',
	'ls-l',
	'ls-normal',
	'lh-1',
	'bg-white',
	'bg-dark',
	'bg-grey',
	'bg-light',
	'bg-black',
	'bg-primary',
	'bg-secondary',
	'border-no',
	'order-first',
	'order-last',
	'order-sm-auto',
	'order-sm-first',
	'order-sm-last',
	'order-md-auto',
	'order-md-first',
	'order-md-last',
	'order-lg-auto',
	'order-lg-first',
	'order-lg-last',
	'col-lg-1-5',
	'col-lg-2-5',
	'col-lg-3-5',
	'col-lg-4-5',
	'pr-0',
	'pt-4',
	'pl-0',
	'pb-0',
	'pb-10',
	'mb-1',
	'mb-4',
	'mb-6',
	'text-center',
	'col-lg-4',
	'col-lg-5',
	'col-lg-7',
	'col-lg-8',
	'col-md-6',
	'pr-lg-4',
	'mb-0',
	'mt-2',
);

// Used Components and classes
$used_classes = array(
	'mt-8',
	'ml-auto',
	'mr-auto',
	'mr-1',
	'mr-2',
	'mr-4',
	'mb-0',
	'mb-2',
	'mb-3',
	'mb-4',
	'mb-5',
	'mb-6',
	'mb-8',
	'mt-2',
	'pt-1',
	'pr-0',
	'pb-10',
	'ls-m',
	'd-none',
	'd-lg-none',
	'd-inline-block',
	'font-primary',
	'text-normal',
	'text-primary',
	'order-lg-last',
	'cols-1',
	'cols-2',
	'cols-3',
	'cols-4',
	'cols-sm-1',
	'cols-sm-2',
	'cols-sm-3',
	'cols-sm-4',
	'cols-md-1',
	'cols-md-2',
	'cols-md-3',
	'cols-md-4',
	'cols-md-6',
	'cols-lg-1',
	'cols-lg-2',
	'cols-lg-3',
	'cols-lg-4',
	'cols-lg-5',
	'cols-lg-6',
	'cols-lg-7',
	'cols-lg-8',
	'cols-xl-2',
	'col-sm-1',
	'col-md-3',
	'col-md-6',
	'col-lg-3',
	'col-lg-4',
	'col-lg-6',
	'col-lg-8',
	'pr-0',
	'pt-4',
	'pl-0',
	'pb-0',
	'pb-10',
	'mb-1',
	'mb-4',
	'mb-6',
	'text-center',
	'pr-lg-4',
	'mb-0',
	'mt-2',
	'flex-none',
	'w-auto',
	'p-absolute',
	't-mc',
	'col-lg-5',
	'col-lg-7',
);

// Initialize
foreach ( $component_patterns as $class => $pattern ) {
	if ( ! isset( $wolmart_used_elements[ $class ] ) ) {
		$wolmart_used_elements[ $class ] = false;
	}
}

// Step 2 : Check theme option
$wolmart_used_elements['product-classic']        = boolval( 'classic' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['product-list']           = true;
$wolmart_used_elements['category-type-default']  = boolval( '' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-frame']    = boolval( 'frame' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-banner']   = boolval( 'banner' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-simple']   = boolval( 'simple' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-icon']     = boolval( 'icon' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-classic']  = boolval( 'classic' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-ellipse']  = boolval( 'ellipse' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-ellipse2'] = boolval( 'ellipse-2' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-group']    = boolval( 'group' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-group2']   = boolval( 'group-2' == wolmart_get_option( 'category_type' ) );
$wolmart_used_elements['category-type-block']    = boolval( 'label' == wolmart_get_option( 'category_type' ) );

$wolmart_used_elements['carousel'] = true;

// Editor
$wolmart_used_elements['elementor'] = false;
$wolmart_used_elements['vc']        = false;
$wolmart_used_elements['gutenberg'] = false;
// Find all posts and classes
$all_classes = '';
foreach ( $available_post_types as $post_type ) {
	$posts = new WP_Query;
	$posts = $posts->query(
		array(
			'posts_per_page' => -1,
			'post_type'      => $post_type,
			'post_status'    => 'publish',
		)
	);
	foreach ( $posts as $post ) {
		$content      = $post->post_content;
		$classes      = wolmart_dynamic_get_classes( '/class="([^"]*)"/', $content );
		$is_gutenburg = true;

		// Elementor Editor
		if ( defined( 'ELEMENTOR_VERSION' ) && get_post_meta( $post->ID, '_elementor_edit_mode', true ) ) {
			$is_gutenburg                       = false;
			$wolmart_used_elements['elementor'] = true;

			$data     = get_post_meta( $post->ID, '_elementor_data', true );
			$classes .= wolmart_dynamic_get_classes( '/class=\\\"([^"]*)\\\"/', $data );
			$classes .= wolmart_dynamic_get_classes( '/"_css_classes":"([^"]*)"/', $data );
			$classes .= wolmart_dynamic_get_classes( '/"css_classes":"([^"]*)"/', $data );
			$classes .= wolmart_dynamic_get_classes( '/"btn_class":"([^"]*)"/', $data );

			foreach ( $component_patterns as $key => $pattern ) {
				if ( $wolmart_used_elements[ $key ] ) {
					continue;
				}
				if ( is_array( $pattern ) ) {
					if ( isset( $pattern['e'] ) ) {
						if ( is_array( $pattern['e'] ) ) {
							foreach ( $pattern['e'] as $e_pattern ) {
								if ( strpos( $data, $e_pattern ) > 0 ) {
									$wolmart_used_elements[ $key ] = true;
								}
							}
						} elseif ( strpos( $data, $pattern['e'] ) > 0 ) {
							$wolmart_used_elements[ $key ] = true;
						}
					}
				}
			}

			// cols classes in shortcodes (elementor)
			$matches = array();
			preg_match_all( '/"col_cnt":"([1-8])"/', $data, $matches, PREG_SET_ORDER );
			foreach ( $matches as $match ) {
				array_push( $used_classes, 'cols-lg-' . intval( $match[1] ) );
			}
			preg_match_all( '/"col_cnt_xl":"([1-8])"/', $data, $matches, PREG_SET_ORDER );
			foreach ( $matches as $match ) {
				array_push( $used_classes, 'cols-xl-' . intval( $match[1] ) );
			}
			preg_match_all( '/"col_cnt_tablet":"([1-8])"/', $data, $matches, PREG_SET_ORDER );
			foreach ( $matches as $match ) {
				array_push( $used_classes, 'cols-md-' . intval( $match[1] ) );
			}
			preg_match_all( '/"col_cnt_mobile":"([1-8])"/', $data, $matches, PREG_SET_ORDER );
			foreach ( $matches as $match ) {
				array_push( $used_classes, 'cols-sm-' . intval( $match[1] ) );
			}
			preg_match_all( '/"col_cnt_min":"([1-8])"/', $data, $matches, PREG_SET_ORDER );
			foreach ( $matches as $match ) {
				array_push( $used_classes, 'cols-' . intval( $match[1] ) );
			}
		}

		// Visual Composer Editor
		if ( defined( 'VCV_VERSION' ) && 'fe' == get_post_meta( $post->ID, 'vcv-be-editor', true ) ) {
			$is_gutenburg                = false;
			$wolmart_used_elements['vc'] = true;

			$data     = rawurldecode( get_post_meta( $post->ID, 'vcv-pageContent', true ) );
			$classes .= wolmart_dynamic_get_classes( '/class=\\\"([^"]*)\\\"/', $data );
			$classes .= wolmart_dynamic_get_classes( '/"customClass":"([^"]*)"/', $data );
			$classes .= wolmart_dynamic_get_classes( '/"el_class":"([^"]*)"/', $data );
			$classes .= wolmart_dynamic_get_classes( '/"btnClass":"([^"]*)"/', $data );

			foreach ( $component_patterns as $key => $pattern ) {
				if ( $wolmart_used_elements[ $key ] ) {
					continue;
				}
				if ( is_array( $pattern ) ) {
					if ( isset( $pattern['v'] ) ) {
						if ( is_array( $pattern['v'] ) ) {
							foreach ( $pattern['v'] as $e_pattern ) {
								if ( strpos( $data, $e_pattern ) > 0 ) {
									$wolmart_used_elements[ $key ] = true;
								}
							}
						} elseif ( strpos( $data, $pattern['v'] ) > 0 ) {
							$wolmart_used_elements[ $key ] = true;
						}
					}
				}
			}
		}

		// Gutenberg Editor
		if ( $is_gutenburg ) {
			$wolmart_used_elements['gutenberg'] = true;
			$classes                           .= wolmart_dynamic_get_classes( '/"className":"([^"]*)"/', $content );
			$classes                           .= wolmart_dynamic_get_classes( '/"icon_class":"([^"]*)"/', $content );

			foreach ( $component_patterns as $key => $pattern ) {
				if ( ! $wolmart_used_elements[ $key ] ) {
					continue;
				}
				if ( is_array( $pattern ) ) {
					if ( isset( $pattern['g'] ) && false !== strpos( $content, $pattern['g'] ) ) {
						$wolmart_used_elements[ $key ] = true;
					}
				}
			}
		}

		// echo esc_html( $content );

		// cols classes in shortcodes (vc)
		$matches = array();
		preg_match_all( '/col_cnt=([1-8])/', $content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			array_push( $used_classes, 'cols-lg-' . intval( $match[1] ) );
		}
		preg_match_all( '/col_cnt_xl=([1-8])/', $content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			array_push( $used_classes, 'cols-xl-' . intval( $match[1] ) );
		}
		preg_match_all( '/col_cnt_tablet=([1-8])/', $content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			array_push( $used_classes, 'cols-md-' . intval( $match[1] ) );
		}
		preg_match_all( '/col_cnt_mobile=([1-8])/', $content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			array_push( $used_classes, 'cols-sm-' . intval( $match[1] ) );
		}
		preg_match_all( '/col_cnt_min=([1-8])/', $content, $matches, PREG_SET_ORDER );
		foreach ( $matches as $match ) {
			array_push( $used_classes, 'cols-' . intval( $match[1] ) );
		}

		// Find shortcodes in content
		foreach ( $shortcode_patterns as $key => $shortcode ) {
			if ( isset( $wolmart_used_elements[ $key ] ) && ! $wolmart_used_elements[ $key ] && false !== strpos( $content, $shortcode ) ) {
				$wolmart_used_elements[ $key ] = true;
			}
		}

		// Find classes in content
		foreach ( $component_patterns as $key => $pattern ) {
			if ( $wolmart_used_elements[ $key ] ) {
				continue;
			}
			if ( is_array( $pattern ) && isset( $pattern['c'] ) && $pattern['c'] && strpos( $classes, $pattern['c'] ) > 0 ) {
				$wolmart_used_elements[ $key ] = true;
			} elseif ( strpos( $classes, $key ) > 0 ) {
				$wolmart_used_elements[ $key ] = true;
			}
		}

		$all_classes .= $classes;
	}
}
foreach ( $used_classes as $class ) {
	$wolmart_used_elements[ $class ] = true;
}
if ( $all_classes ) { // margin, padding, col-*
	preg_match_all( '/(mt-|mr-|mb-|ml-|pt-|pr-|pb-|pl-)(|sm-|md-|lg-|xl-)(1?\d)/', $all_classes, $matches, PREG_SET_ORDER );
	foreach ( $matches as $match ) {
		$wolmart_used_elements[ $match[0] ] = true;
	}
	preg_match_all( '/cols-(|xs-|sm-|md-|xl-)(\d)/', $all_classes, $matches, PREG_SET_ORDER ); // "cols-lg-*" are all used.
	foreach ( $matches as $match ) {
		$wolmart_used_elements[ $match[0] ] = true;
	}
	preg_match_all( '/col-(|xs-|sm-|md-|lg-|xl-)(1?\d)/', $all_classes, $matches, PREG_SET_ORDER );
	foreach ( $matches as $match ) {
		$wolmart_used_elements[ $match[0] ] = true;
	}
	foreach ( $helper_classes as $class ) {
		if ( ! ( isset( $wolmart_used_elements[ $class ] ) && $wolmart_used_elements[ $class ] ) &&
			false !== strpos( $all_classes, $class ) ) {

			$wolmart_used_elements[ $class ] = true;
		}
	}
}


function wolmart_dynamic_get_classes( $pattern, $data ) {
	$classes = '';
	preg_match_all( $pattern, $data, $matches, PREG_SET_ORDER );
	foreach ( $matches as $match ) {
		if ( $match[1] ) {
			$classes .= ' ' . $match[1];
		}
	}
	return $classes;
}
