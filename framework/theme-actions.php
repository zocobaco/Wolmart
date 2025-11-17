<?php
/**
 * Theme Actions & Filters
 *
 * @package Wolmart WordPress Framework
 * @version 1.0
 */
defined( 'ABSPATH' ) || die;

// The body tag's Class
add_filter( 'body_class', 'wolmart_add_body_class' );

// The main tag's class
add_filter( 'wolmart_main_class', 'wolmart_add_main_class' );

// add aria label to search cat for seo purpose
add_filter( 'wp_dropdown_cats', 'wolmart_add_search_cat_aria_label' );

// Page layout
add_action( 'wolmart_print_before_page_layout', 'wolmart_print_layout_before' );
add_action( 'wolmart_print_after_page_layout', 'wolmart_print_layout_after' );

// Cookie Law information popup
add_action( 'wolmart_after_page_wrapper', 'wolmart_print_cookie_popup' );

// Comment
add_filter( 'wolmart_filter_comment_form_args', 'wolmart_comment_form_args' );
add_action( 'comment_form_before_fields', 'wolmart_comment_form_before_fields' );
add_action( 'comment_form_after_fields', 'wolmart_comment_form_after_fields' );
add_filter( 'pre_get_avatar_data', 'wolmart_set_avatar_size' );

// Author date
add_filter( 'wolmart_filter_author_date_pattern', 'wolmart_author_date_pattern' );

// Cookie
add_action( 'init', 'wolmart_set_cookies' );

// Contact Form
add_action( 'wpcf7_init', 'wolmart_wpcf7_add_form_tag_submit', 20, 0 );
add_filter( 'wpcf7_form_novalidate', 'wolmart_wpcf7_form_novalidate' );

// Widget Compatabilities
add_filter( 'widget_nav_menu_args', 'wolmart_widget_nav_menu_args', 10, 4 );

// Image Quality and Big Image Size Threshold
add_filter( 'jpeg_quality', 'wolmart_set_image_quality' );
add_filter( 'wp_editor_set_quality', 'wolmart_set_image_quality' );
add_filter( 'big_image_size_threshold', 'wolmart_set_big_image_size_threshold' );

// Wolmart Ajax Actions
add_action( 'wp_ajax_wolmart_loadmore', 'wolmart_loadmore' );
add_action( 'wp_ajax_nopriv_wolmart_loadmore', 'wolmart_loadmore' );
if ( class_exists( 'WooCommerce' ) ) {
	add_action( 'wp_ajax_wolmart_account_form', 'wolmart_ajax_account_form' );
	add_action( 'wp_ajax_nopriv_wolmart_account_form', 'wolmart_ajax_account_form' );
}
add_action( 'wp_ajax_wolmart_account_signin_validate', 'wolmart_account_signin_validate' );
add_action( 'wp_ajax_nopriv_wolmart_account_signin_validate', 'wolmart_account_signin_validate' );
add_action( 'wp_ajax_wolmart_account_signup_validate', 'wolmart_account_signup_validate' );
add_action( 'wp_ajax_nopriv_wolmart_account_signup_validate', 'wolmart_account_signup_validate' );
add_action( 'wp_ajax_wolmart_load_mobile_menu', 'wolmart_load_mobile_menu' );
add_action( 'wp_ajax_nopriv_wolmart_load_mobile_menu', 'wolmart_load_mobile_menu' );
add_action( 'wp_ajax_wolmart_load_menu', 'wolmart_load_menu' );
add_action( 'wp_ajax_nopriv_wolmart_load_menu', 'wolmart_load_menu' );
add_action( 'wp_ajax_comment-feeling', 'wolmart_ajax_comment_feeling' );
add_action( 'wp_ajax_nopriv_comment-feeling', 'wolmart_ajax_comment_feeling' );
add_action( 'wp_ajax_wolmart_print_popup', 'wolmart_ajax_print_popup' );
add_action( 'wp_ajax_nopriv_wolmart_print_popup', 'wolmart_ajax_print_popup' );

// Wolmart Defer Scripts
add_filter( 'script_loader_tag', 'wolmart_defer_parsing_of_js', 999, 3 );

/**
 * Wolmart Defer Parsing JS
 *
 * @since 1.6.0
 */
if ( ! function_exists( 'wolmart_defer_parsing_of_js' ) ) {
	function wolmart_defer_parsing_of_js( $tag, $handle, $src ) {
		if ( false == strpos( $tag, '.js' ) ) {
			return $tag;
		}

		$defer_handles = apply_filters( 'wolmart_defer_scripts', array() );

		if ( ! empty( $defer_handles ) && true == in_array( $handle, $defer_handles ) ) {
			return str_replace( ' src', ' defer src', $tag );
		}

		return $tag;
	}
}

if ( ! function_exists( 'wolmart_add_body_class' ) ) {
	/**
	 * Add classes to body
	 *
	 * @since 1.0
	 *
	 * @param array[string] $classes
	 *
	 * @return array[string] $classes
	 */
	function wolmart_add_body_class( $classes ) {
		global $wolmart_layout;

		// Site Layout
		if ( 'full' != wolmart_get_option( 'site_type' ) ) { // Boxed or Framed
			$classes[] = 'site-boxed';
		}

		// Page Type
		$classes[] = 'wolmart-' . str_replace( '_', '-', wolmart_get_page_layout() ) . '-layout';

		// Disable Mobile Slider
		if ( wolmart_get_option( 'mobile_disable_slider' ) ) {
			$classes[] = 'wolmart-disable-mobile-slider';
		}

		// Disable Mobile Animation
		if ( wolmart_get_option( 'mobile_disable_animation' ) ) {
			$classes[] = 'wolmart-disable-mobile-animation';
		}

		// Add single-product-page or shop-page to body class
		if ( wolmart_is_product() ) {
			$classes[] = 'single-product-page';
		} elseif ( wolmart_is_shop() ) {
			$classes[] = 'product-archive-page';
		}

		if ( class_exists( 'WooCommerce' ) && wc_get_page_id( 'compare' ) == get_the_ID() ) {
			$classes[] = 'compare-page';
		}

		global $wolmart_layout;

		$post_type = isset( $wolmart_layout['post_type'] ) ? $wolmart_layout : '';

		// Category Filter
		if ( ( empty( $wolmart_layout['left_sidebar'] ) || 'hide' == $wolmart_layout['left_sidebar'] ) &&
			( empty( $wolmart_layout['right_sidebar'] ) || 'hide' == $wolmart_layout['right_sidebar'] ) &&
			is_archive() && 'post' == get_post_type() && wolmart_get_option( 'posts_filter' ) && 'list' != $post_type ) {
			$classes[] = 'breadcrumb-divider-active';
		}

		if ( wolmart_get_option( 'rounded_skin' ) ) {
			$classes[] = 'wolmart-rounded-skin';
		}

		if ( is_admin_bar_showing() ) {
			$classes[] = 'wolmart-adminbar';
		}
		if ( defined( 'WOLMART_VENDORS' ) ) {
			$classes[] = 'wolmart-use-vendor-plugin';
		}
		return $classes;
	}
}

if ( ! function_exists( 'wolmart_add_search_cat_aria_label' ) ) {
	function wolmart_add_search_cat_aria_label( $output ) {
		$output = str_replace( " name='cat'", " name='cat' aria-label='" . esc_html__( 'Categories to search', 'wolmart' ) . "'", $output );
		$output = str_replace( " name='product_cat'", " name='product_cat' aria-label='" . esc_html__( 'Product categories to search', 'wolmart' ) . "'", $output );
		return $output;
	}
}

if ( ! function_exists( 'wolmart_add_main_class' ) ) {
	function wolmart_add_main_class( $classes ) {
		if ( ( defined( 'YITH_WCWL' ) && function_exists( 'yith_wcwl_is_wishlist_page' ) && yith_wcwl_is_wishlist_page() ) ||
			( class_exists( 'WooCommerce' ) && ( is_cart() || is_checkout() || is_account_page() ) ) ) {
			$classes .= ' pt-lg';
		}
		return $classes;
	}
}

if ( ! function_exists( 'wolmart_print_title_bar' ) ) {
	function wolmart_print_title_bar() {
		global $wolmart_layout;

		if ( is_front_page() ) {
			// Do not show page title bar and breadcrumb in home page.
		} else {
			if ( ! empty( $wolmart_layout['ptb'] ) && 'hide' != $wolmart_layout['ptb'] ) {
				// Display selected template instead of page title bar.
				wolmart_print_template( $wolmart_layout['ptb'] );

			} elseif ( ( ! empty( $wolmart_layout['ptb'] ) && 'hide' == $wolmart_layout['ptb'] ) || apply_filters( 'wolmart_is_vendor_store', false ) ) {
				// Hide page title bar.

			} elseif ( class_exists( 'WooCommerce' ) && ( is_cart() || is_checkout() ) ) {

				$wolmart_layout['show_breadcrumb'] = 'no';
				?>
				<div class="woo-page-header">
					<div class="<?php echo esc_attr( 'full' == $wolmart_layout['wrap'] ? 'container' : $wolmart_layout['wrap'] ); ?>">
						<ul class="breadcrumb">
							<li class="<?php echo is_cart() ? esc_attr( 'current' ) : ''; ?>">
								<a href="<?php echo esc_url( wc_get_cart_url() ); ?>"><?php esc_html_e( 'Shopping Cart', 'wolmart' ); ?></a>
							</li>
							<li class="<?php echo is_checkout() && ! is_order_received_page() ? esc_attr( 'current' ) : ''; ?>">
								<i class="delimiter"></i>
								<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>"><?php esc_html_e( 'Checkout', 'wolmart' ); ?></a>
							</li>
							<li class="<?php echo is_order_received_page() ? esc_attr( 'current' ) : esc_attr( 'disable' ); ?>">
								<i class="delimiter"></i>
								<a href="#"><?php esc_html_e( 'Order Complete', 'wolmart' ); ?></a>
							</li>
						</ul>
					</div>
				</div>
				<?php
			} else {
				// Show page header
				Wolmart_Layout_Builder::get_instance()->setup_titles();
				?>
				<div class="page-header">
					<div class="page-title-bar">
						<div class="page-title-wrap">
							<?php if ( $wolmart_layout['title'] ) : ?>
							<h2 class="page-title"><?php echo wolmart_strip_script_tags( $wolmart_layout['title'] ); ?></h2>
							<?php endif; ?>
							<?php if ( $wolmart_layout['subtitle'] ) : ?>
							<h3 class="page-subtitle"><?php echo wolmart_strip_script_tags( $wolmart_layout['subtitle'] ); ?></h3>
							<?php endif; ?>
						</div>
					</div>
				</div>
				<?php
			}

			if ( empty( $wolmart_layout['show_breadcrumb'] ) || 'no' != $wolmart_layout['show_breadcrumb'] ) {
				wolmart_breadcrumb();
			}
		}
	}
}

if ( ! function_exists( 'wolmart_print_layout_before' ) ) {
	function wolmart_print_layout_before() {
		global $wolmart_layout;

		$main_content_wrap_class = 'main-content-wrap';
		$has_left_sidebar        = ! empty( $wolmart_layout['left_sidebar'] ) && 'hide' != $wolmart_layout['left_sidebar'];
		$has_right_sidebar       = ! empty( $wolmart_layout['right_sidebar'] ) && 'hide' != $wolmart_layout['right_sidebar'];

		if ( $has_left_sidebar || $has_right_sidebar ) {
			$main_content_wrap_class .= ' row gutter-lg';
		}

		$main_content_wrap_class = apply_filters( 'wolmart_main_content_wrap_cls', $main_content_wrap_class );

		if ( 'full' != $wolmart_layout['wrap'] ) {
			echo '<div class="' . esc_attr( 'container-fluid' == $wolmart_layout['wrap'] ? 'container-fluid' : 'container' ) . '">';
		}

		do_action( 'wolmart_before_main_content' );

		echo '<div class="' . esc_attr( $main_content_wrap_class ) . '">';

		if ( $has_left_sidebar ) {
			wolmart_get_template_part( 'sidebar', null, array( 'position' => 'left' ) );
		}

		if ( $has_right_sidebar ) {
			wolmart_get_template_part( 'sidebar', null, array( 'position' => 'right' ) );
		}

		do_action( 'wolmart_sidebar' );

		echo '<div class="' . esc_attr( apply_filters( 'wolmart_main_content_class', 'main-content' ) ) . '">';

		do_action( 'wolmart_before_inner_content' );
	}
}

if ( ! function_exists( 'wolmart_print_layout_after' ) ) {
	function wolmart_print_layout_after() {
		$ls        = false; // state of left sidebar
		$rs        = false; // state of right sidebar
		$ls_canvas = false; // on_canvas/off_canvas
		$rs_canvas = false; // on_canvas/off_canvas

		global $wolmart_layout;

		do_action( 'wolmart_after_inner_content', $wolmart_layout );

		echo '</div>'; // End of main content wrap

		do_action( 'wolmart_after_main_content' );

		echo '</div>';

		if ( is_page() && ! wolmart_is_shop() && comments_open() ) {
			comments_template();
		}

		if ( 'full' != $wolmart_layout['wrap'] ) { // end of container or container-fluid
			echo '</div>';
		}
	}
}

if ( ! function_exists( 'wolmart_comment_form_before_fields' ) ) {
	function wolmart_comment_form_before_fields() {
		echo '<div class="row">';
	}
}

if ( ! function_exists( 'wolmart_comment_form_after_fields' ) ) {
	function wolmart_comment_form_after_fields() {
		echo '</div>';
	}
}

if ( ! function_exists( 'wolmart_set_avatar_size' ) ) {
	function wolmart_set_avatar_size( $args ) {
		$args['size']   = 90;
		$args['width']  = 90;
		$args['height'] = 90;
		return $args;
	}
}

if ( ! function_exists( 'wolmart_author_date_pattern' ) ) {
	function wolmart_author_date_pattern( $date ) {
		return date( 'F j, Y \a\t g:s a', strtotime( $date ) );
	}
}

if ( ! function_exists( 'wolmart_set_cookies' ) ) {
	function wolmart_set_cookies() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		if ( ! empty( $_GET['top_filter'] ) ) {
			setcookie( 'top_filter', sanitize_title( $_GET['top_filter'] ), time() + ( 86400 ), '/' );
			$_COOKIE['wolmart_top_filter'] = esc_html( $_GET['top_filter'] );
		}
		// phpcs:enable
	}
}


if ( ! function_exists( 'wolmart_print_cookie_popup' ) ) {

	/**
	 * Print Cookie law information popup
	 *
	 * @since 1.0.0
	 *
	 * @return template
	 */
	function wolmart_print_cookie_popup() {
		if ( ! wolmart_get_option( 'show_cookie_info' ) ) {
			return;
		}

		// $page_id = wolmart_get_option( 'choose_cookie_page' );
		?>
		<div class="cookies-popup bg-dark">
			<div class="container d-flex align-items-center">
				<div class="cookies-info">
					<?php echo wolmart_strip_script_tags( wolmart_get_option( 'cookie_text' ) ); ?>
				</div>
				<div class="cookies-buttons d-flex flex-1 align-items-center justify-content-end">
					<a href="#" rel="nofollow noopener" class="btn btn-sm btn-secondary btn-rounded decline-cookie-btn"><?php echo wolmart_strip_script_tags( wolmart_get_option( 'cookie_decline_btn' ) ); ?></a>
					<a href="#" rel="nofollow noopener" class="btn btn-sm btn-primary btn-rounded accept-cookie-btn"><?php echo wolmart_strip_script_tags( wolmart_get_option( 'cookie_agree_btn' ) ); ?></a>
				</div>
			</div>
			<a href="#" class="close-cookie-btn" aria-label="<?php esc_attr_e( 'Close', 'wolmart' ); ?>" role="button"></a>
		</div>
		<?php
	}
}

/*******************************
	*                          *
	*   Wolmart Ajax Actions   *
	*                          *
	*******************************/

if ( ! function_exists( 'wolmart_loadmore' ) ) {
	function wolmart_loadmore() {

		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification

		if ( isset( $_POST['args'] ) && isset( $_POST['props'] ) ) {
			// Sanitize and validate inputs
			$args  = is_array( $_POST['args'] ) ? $_POST['args'] : [];
			$props = is_array( $_POST['props'] ) ? $_POST['props'] : [];

			if ( 'post' == $args['post_type'] ) {
				$props_escaped = array();
				foreach ( $props as $key => $prop ) {
					if ( is_array( $prop ) ) {
						$prop = array_map( 'sanitize_text_field', $prop );
					} else {
						$prop = sanitize_text_field( $prop );
					}
					$props_escaped[ sanitize_key( $key ) ] = $prop;
				}

				$args_escaped = array();
				foreach ( $args as $key => $arg ) {
					if ( is_array( $arg ) ) {
						$arg = array_map( 'sanitize_text_field', $arg );
					} else {
						$arg = sanitize_text_field( $arg );
					}
					$args_escaped[ sanitize_key( $key ) ] = $arg;
				}

				/**
				 * Load more posts
				 */
				$posts = new WP_Query( $args_escaped );
				if ( $posts ) {

					ob_start();
					while ( $posts->have_posts() ) {
						$posts->the_post();
						if ( function_exists( 'wolmart_get_template_part' ) ) {
							wolmart_get_template_part( 'posts/post', null, $props_escaped );
						}
					}
					$html = ob_get_clean();

					if ( $_POST['pagination'] ) {
						echo json_encode(
							array(
								'html'       => $html,
								'pagination' => wolmart_get_pagination( $posts, 'pagination-load' ),
							)
						);
					} else {
						echo wolmart_escaped( $html );
					}
					wp_reset_postdata();
				}
			} else {
				/**
				 * Load more products
				 */
				$args  = $_POST['args'];
				$props = $_POST['props'];

				if ( isset( $args['paged'] ) && $args['paged'] ) {
					$args['page'] = $args['paged'];
					unset( $args['paged'] );
				}

				if ( isset( $args['total'] ) && $args['total'] ) {
					unset( $args['total'] );
				}

				if ( isset( $props['row_cnt'] ) ) {
					$GLOBALS['wolmart_current_product_id'] = 0;
				}

				wc_set_loop_prop( 'wolmart_ajax_load', true );

				foreach ( $props as $key => $prop ) {
					if ( is_array( $prop ) ) {
						$prop = array_map( 'sanitize_text_field', $prop );
					} else {
						$prop = sanitize_text_field( $prop );
					}
					wc_set_loop_prop( sanitize_key( $key ), $prop );
				}

				$args_str = '';
				foreach ( $args as $key => $value ) {
					$args_str .= ' ' . sanitize_key( $key ) . '="' . esc_attr( is_array( $value ) ? json_encode( $value ) : $value ) . '"';
				}

				$html = do_shortcode( '[products' . $args_str . ']' );

				echo wolmart_escaped( $html );
			}
		}

		exit;

		// phpcs:enable
	}
}

// ajax sign in / sign up form
if ( ! function_exists( 'wolmart_ajax_account_form' ) ) {
	function wolmart_ajax_account_form() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		wc_get_template( 'myaccount/form-login.php' );
		exit();
		// phpcs:enable
	}
}

// sign in ajax validate
function wolmart_account_signin_validate() {
	$nonce_value = wc_get_var( $_REQUEST['woocommerce-login-nonce'], wc_get_var( $_REQUEST['_wpnonce'], '' ) ); // @codingStandardsIgnoreLine.
	$result      = false;
	if ( wp_verify_nonce( $nonce_value, 'woocommerce-login' ) ) {
		try {
			$creds = array(
				'user_login'    => sanitize_text_field( trim( $_POST['username'] ) ),
				'user_password' => $_POST['password'],
				'remember'      => isset( $_POST['rememberme'] ),
			);

			$validation_error = new WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_login_errors', $validation_error, $_POST['username'], $_POST['password'] );

			if ( $validation_error->get_error_code() ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => '<strong>' . esc_html__(
							'Error:',
							'wolmart'
						) . '</strong> ' . $validation_error->get_error_message(),
					)
				);
				die();
			}

			if ( empty( $creds['user_login'] ) ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => '<strong>' . esc_html__(
							'Error:',
							'wolmart'
						) . '</strong> ' . esc_html__(
							'Username is required.',
							'wolmart'
						),
					)
				);
				die();
			}

			// On multisite, ensure user exists on current site, if not add them before allowing login.
			if ( is_multisite() ) {
				$user_data = get_user_by( is_email( $creds['user_login'] ) ? 'email' : 'login', $creds['user_login'] );

				if ( $user_data && ! is_user_member_of_blog( $user_data->ID, get_current_blog_id() ) ) {
					add_user_to_blog( get_current_blog_id(), $user_data->ID, 'customer' );
				}
			}

			// Perform the login
			$user = wp_signon( apply_filters( 'woocommerce_login_credentials', $creds ), is_ssl() );
			if ( ! is_wp_error( $user ) ) {
				$result = true;
			}
		} catch ( Exception $e ) {
		}
	}
	if ( $result ) {
		echo json_encode(
			array(
				'loggedin' => true,
				'message'  => esc_html__(
					'Login successful, redirecting...',
					'wolmart'
				),
			)
		);
	} else {
		if ( isset( $_REQUEST['cf-turnstile-response'] ) && '' == $_REQUEST['cf-turnstile-response'] ) {
			echo json_encode(
				array(
					'loggedin' => false,
					'message'  => esc_html__(
						'Please verify that you are human.',
						'wolmart'
					),
				)
			);
		} else {
			echo json_encode(
				array(
					'loggedin' => false,
					'message'  => esc_html__(
						'Wrong username or password.',
						'wolmart'
					),
				)
			);
		}
	}
	die();
}

// sign up ajax validate
function wolmart_account_signup_validate() {

	$nonce_value = isset( $_POST['_wpnonce'] ) ? $_POST['_wpnonce'] : '';
	$nonce_value = isset( $_POST['woocommerce-register-nonce'] ) ? $_POST['woocommerce-register-nonce'] : $nonce_value;
	$result      = true;

	if ( wp_verify_nonce( $nonce_value, 'woocommerce-register' ) ) {
		$username = 'no' == get_option( 'woocommerce_registration_generate_username' ) ? $_POST['username'] : '';
		$password = 'no' == get_option( 'woocommerce_registration_generate_password' ) ? $_POST['password'] : '';
		$email    = $_POST['email'];

		try {
			$validation_error = new WP_Error();
			$validation_error = apply_filters( 'woocommerce_process_registration_errors', $validation_error, $username, $password, $email );

			if ( $validation_error->get_error_code() ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => $validation_error->get_error_message(),
					)
				);
				die();
			}

			$new_customer = wc_create_new_customer( sanitize_email( $email ), wc_clean( $username ), $password );

			if ( is_wp_error( $new_customer ) ) {
				echo json_encode(
					array(
						'loggedin' => false,
						'message'  => $new_customer->get_error_message(),
					)
				);
				die();
			}

			if ( apply_filters( 'woocommerce_registration_auth_new_customer', true, $new_customer ) ) {
				wc_set_customer_auth_cookie( $new_customer );
			}
		} catch ( Exception $e ) {
			$result = false;
		}
	}
	if ( $result ) {
		echo json_encode(
			array(
				'loggedin' => true,
				'message'  => esc_html__(
					'Register successful, redirecting...',
					'wolmart'
				),
			)
		);
	} else {
		echo json_encode(
			array(
				'loggedin' => false,
				'message'  => esc_html__(
					'Register failed.',
					'wolmart'
				),
			)
		);
	}
	die();
}

if ( ! function_exists( 'wolmart_load_mobile_menu' ) ) {
	function wolmart_load_mobile_menu() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		?>
		<!-- Search Form -->
			<div class="search-wrapper hs-simple">
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
		$mobile_menus = wolmart_get_option( 'mobile_menu_items' );

		if ( ! empty( $mobile_menus ) ) {
			?>
			<div class="nav-wrapper">
				<?php
				if ( count( $mobile_menus ) > 1 ) {
					?>
					<div class="tab tab-nav-simple tab-nav-boxed">
						<ul class="nav nav-tabs nav-fill" role="tablist">
							<?php
							$first = true;
							foreach ( $mobile_menus as $menu ) :
								$menu_obj = wp_get_nav_menu_object( $menu );
								?>
								<li class="nav-item">
									<a class="nav-link<?php echo ! $first ? '' : ' active'; ?>" href="#<?php echo esc_html( $menu ); ?>" role="tab"><?php echo esc_html( $menu_obj->name ); ?></a>
								</li>
								<?php $first = false; ?>
							<?php endforeach; ?>
						</ul>
						<div class="tab-content">
							<?php
							$first = true;
							foreach ( $mobile_menus as $menu ) :
								?>
								<div class="tab-pane<?php echo ! $first ? '' : ' active in'; ?>" id="<?php echo esc_html( strtolower( $menu ) ); ?>">
									<?php
									wp_nav_menu(
										array(
											'menu'       => $menu,
											'container'  => 'nav',
											'container_class' => $menu,
											'items_wrap' => '<ul id="%1$s" class="mobile-menu">%3$s</ul>',
											'walker'     => new Wolmart_Walker_Nav_Menu(),
											'theme_location' => '',
										)
									);
									$first = false;
									?>
								</div>
							<?php endforeach; ?>
						</div>
					<?php
				} else {
					foreach ( $mobile_menus as $menu ) {
						wp_nav_menu(
							array(
								'menu'            => $menu,
								'container'       => 'nav',
								'container_class' => $menu,
								'items_wrap'      => '<ul id="%1$s" class="mobile-menu">%3$s</ul>',
								'walker'          => new Wolmart_Walker_Nav_Menu(),
								'theme_location'  => '',
							)
						);
					}
				}
				?>
			</div>
			<?php
		}

		if ( wolmart_doing_ajax() && $_REQUEST['action'] && 'wolmart_load_mobile_menu' == $_REQUEST['action'] ) {
			die;
		}

		// phpcs:enable
	}
}

if ( ! function_exists( 'wolmart_load_menu' ) ) {
	function wolmart_load_menu() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification

		if ( isset( $_POST['menus'] ) && is_array( $_POST['menus'] ) ) {
			$menus = $_POST['menus'];
			if ( ! empty( $menus ) ) {
				$result = array();
				foreach ( $menus as $menu ) {
					$result[ $menu ] = wp_nav_menu(
						array(
							'menu'       => sanitize_text_field( $menu ),
							'container'  => '',
							'items_wrap' => '%3$s',
							'walker'     => new Wolmart_Walker_Nav_Menu(),
							'echo'       => false,
						)
					);
				}
				echo json_encode( $result );
			}
		}

		exit;

		// phpcs:enable
	}
}

// Wolmart Contact Form Functions
if ( ! function_exists( 'wolmart_wpcf7_add_form_tag_submit' ) ) {
	function wolmart_wpcf7_add_form_tag_submit() {
		wpcf7_remove_form_tag( 'submit' );
		wpcf7_add_form_tag( 'submit', 'wolmart_wpcf7_submit_form_tag_handler' );
	}
}

if ( ! function_exists( 'wolmart_wpcf7_submit_form_tag_handler' ) ) {
	function wolmart_wpcf7_submit_form_tag_handler( $tag ) {
		$class = wpcf7_form_controls_class( $tag->type );

		$atts = array();

		$atts['class']    = $tag->get_class_option( $class );
		$atts['id']       = $tag->get_id_option();
		$atts['tabindex'] = $tag->get_option( 'tabindex', 'signed_int', true );

		$value = isset( $tag->values[0] ) ? $tag->values[0] : '';

		if ( empty( $value ) ) {
			$value = esc_html__( 'Send', 'wolmart' );
		}

		$atts['type']  = 'submit';
		$atts['value'] = $value;

		$atts = wpcf7_format_atts( $atts );

		$html = sprintf( '<button %1$s>%2$s</button>', $atts, esc_html( $value ) );

		return $html;
	}
}
function wolmart_wpcf7_form_novalidate() {
	return '';
}

// Wolmart Widget Compatability Functions
if ( ! function_exists( 'wolmart_widget_nav_menu_args' ) ) {
	function wolmart_widget_nav_menu_args( $nav_menu_args, $menu, $args, $instance ) {
		$nav_menu_args['items_wrap'] = '<ul id="%1$s" class="menu collapsible-menu">%3$s</ul>';
		return $nav_menu_args;
	}
}


// Image Quality
if ( ! function_exists( 'wolmart_set_image_quality' ) ) {
	function wolmart_set_image_quality() {
		return wolmart_get_option( 'image_quality', 82 );
	}
}

// Big Image Size Threshold
if ( ! function_exists( 'wolmart_set_big_image_size_threshold' ) ) {
	function wolmart_set_big_image_size_threshold() {
		return wolmart_get_option( 'big_image_threshold', 2560 );
	}
}

// comment feeling
if ( ! function_exists( 'wolmart_ajax_comment_feeling' ) ) {
	function wolmart_ajax_comment_feeling() {
		// check_ajax_referer( 'wolmart-nonce', 'nonce' );
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification
		$id = isset( $_POST['comment_id'] ) ? (int) $_POST['comment_id'] : 0;
		if ( $id ) {
			$action        = $_POST['button'];
			$status        = isset( $_COOKIE[ 'wolmart_comment_feeling_' . $id ] ) ? (int) $_COOKIE[ 'wolmart_comment_feeling_' . $id ] : 0;
			$like_count    = get_comment_meta( $id, 'like_count', true );
			$dislike_count = get_comment_meta( $id, 'dislike_count', true );

			if ( 'like' == $action ) {
				if ( 1 == $status ) {
					-- $like_count;
					$status = 0;
				} else {
					if ( -1 == $status ) {
						-- $dislike_count;
					}

					++ $like_count;
					$status = 1;
				}
			} else {
				if ( -1 == $status ) {
					-- $dislike_count;
					$status = 0;
				} else {
					if ( 1 == $status ) {
						-- $like_count;
					}

					++ $dislike_count;
					$status = -1;
				}
			}

			$like_count    = max( 0, $like_count );
			$dislike_count = max( 0, $dislike_count );

			if ( $status ) {
				setcookie( 'comment_feeling_' . $id, $status, time() + 360 * 24 * 60 * 60, '/' );
			} else {
				setcookie( 'comment_feeling_' . $id, '', time() - 360 * 24 * 60 * 60, '/' );
			}

			update_comment_meta( $id, 'like_count', $like_count );
			update_comment_meta( $id, 'dislike_count', $dislike_count );

			echo json_encode( array( $status, intval( $like_count ), intval( $dislike_count ) ) );
		}

		// phpcs:enable
		exit();
	}
}


if ( ! function_exists( 'wolmart_comment_form_args' ) ) {

	/**
	 * Set comment form arguments
	 *
	 * @since 1.0.0
	 */
	function wolmart_comment_form_args( $args ) {
		$args['title_reply_before'] = '<h3 id="reply-title" class="comment-reply-title">';
		$args['title_reply_after']  = '</h3>';
		$args['fields']['author']   = '<div class="col-md-6"><input name="author" type="text" class="form-control" value="" placeholder="' . esc_attr__( 'Your Name', 'wolmart' ) . '"> </div>';
		$args['fields']['email']    = '<div class="col-md-6"><input name="email" type="text" class="form-control" value="" placeholder="' . esc_attr__( 'Your Email', 'wolmart' ) . '"> </div>';

		$args['comment_field']  = isset( $args['comment_field'] ) ? $args['comment_field'] : '';
		$args['comment_field']  = substr( $args['comment_field'], 0, strpos( $args['comment_field'], '<p class="comment-form-comment">' ) );
		$args['comment_field'] .= '<textarea name="comment" id="comment" class="form-control" rows="6" maxlength="65525" required="required" placeholder="' . esc_attr__( 'Write Your Review Here&hellip;', 'wolmart' ) . '"></textarea>';
		$args['submit_button']  = '<button type="submit" class="btn btn-dark btn-submit">' .
			( wolmart_is_product() ? esc_html__( 'Submit Review', 'wolmart' ) : esc_html__( 'Post Comment', 'wolmart' ) . ' <i class=" w-icon-long-arrow-' . ( is_rtl() ? 'left' : 'right' ) . '"></i>' ) . '</button>';

		return $args;
	}
}

if ( ! function_exists( 'wolmart_ajax_print_popup' ) ) {

	/**
	 * Render popup template when a specific selector is clicked
	 *
	 * @since 1.0.0
	 */
	function wolmart_ajax_print_popup() {
		// phpcs:disable WordPress.Security.NonceVerification.NoNonceVerification

		$id = isset( $_POST['popup_id'] ) ? $_POST['popup_id'] : 0;
		$id = is_numeric( $id ) ? ( (int) $id ) : sanitize_text_field( $id );

		if ( $id ) {
			wolmart_print_popup_template( $id );
		}

		// phpcs:enable
		exit();
	}
}


/**
 * Check social login is enabled
 *
 * @param {String} $social
 * @since 1.0.0
 */
function wolmart_nextend_social_login( $social ) {
	$res = '';

	if ( class_exists( 'NextendSocialLogin', false ) ) {
		$res = NextendSocialLogin::isProviderEnabled( $social );
	} else {
		if ( 'facebook' == $social ) {
			$res = defined( 'NEW_FB_LOGIN' );
		} elseif ( 'google' == $social ) {
			$res = defined( 'NEW_GOOGLE_LOGIN' );
		} elseif ( 'twitter' == $social ) {
			$res = defined( 'NEW_TWITTER_LOGIN' );
		}
	}
	return apply_filters( 'wolmart_nextend_social_login', $res, $social );
}


if ( ! function_exists( 'wolmart_print_social_login_fields' ) ) {

	/**
	 * Print Social login options
	 *
	 * @since 1.0.0
	 */
	function wolmart_print_social_login_fields() {
		$is_facebook_login = wolmart_nextend_social_login( 'facebook' );
		$is_google_login   = wolmart_nextend_social_login( 'google' );
		$is_twitter_login  = wolmart_nextend_social_login( 'twitter' );

		if ( ( $is_facebook_login || $is_google_login || $is_twitter_login ) ) {
			?>

			<div class="social-login title-center title-cross text-center">
				<h4 class="title">
					<?php
					if ( 'woocommerce_login_form_end' == current_action() ) {
						esc_html_e( 'or Login With', 'wolmart' );
					} else {
						esc_html_e( 'or Signup With', 'wolmart' );
					}
					?>
				</h4>
				<div class="social-icons">
				<?php do_action( 'wolmart_before_login_social' ); ?>
				<?php if ( $is_google_login ) { ?>
					<a class="social-icon framed rounded social-google" href="<?php echo wp_login_url(); ?>?loginGoogle=1&redirect=<?php echo the_permalink(); ?>" onclick="window.location.href = '<?php echo wp_login_url(); ?>?loginGoogle=1&redirect='+window.location.href; return false" aria-label="<?php esc_attr_e( 'Social Login', 'wolmart' ); ?>">
						<i class="w-icon-google2"></i></a>
				<?php } ?>
				<?php if ( $is_facebook_login ) { ?>
					<a class="social-icon framed rounded social-facebook" href="<?php echo wp_login_url(); ?>?loginFacebook=1&redirect=<?php echo the_permalink(); ?>" onclick="window.location.href = '<?php echo wp_login_url(); ?>?loginFacebook=1&redirect='+window.location.href; return false" aria-label="<?php esc_attr_e( 'Social Login', 'wolmart' ); ?>"><i class="w-icon-facebook"></i></a>
				<?php } ?>
				<?php if ( $is_twitter_login ) { ?>
					<a class="social-icon framed rounded social-twitter" href="<?php echo wp_login_url(); ?>?loginSocial=twitter&redirect=<?php echo the_permalink(); ?>" onclick="window.location.href = '<?php echo wp_login_url(); ?>?loginSocial=twitter&redirect='+window.location.href; return false" aria-label="<?php esc_attr_e( 'Social Login', 'wolmart' ); ?>">
						<i class="w-icon-twitter"></i></a>  
				<?php } ?>
				<?php do_action( 'wolmart_after_login_social' ); ?>
				</div>
			</div>

			<?php
		}
	}
}
