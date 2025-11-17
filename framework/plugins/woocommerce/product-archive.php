<?php
/**
 * Wolmart WooCommerce Archive Product Functions
 *
 * Functions used to display archive product.
 */

defined( 'ABSPATH' ) || die;

remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
add_filter( 'loop_shop_per_page', 'wolmart_loop_shop_per_page' );
add_action( 'wolmart_wc_result_count', 'woocommerce_result_count' );
add_filter( 'woocommerce_layered_nav_count', 'wolmart_woo_layered_nav_count', 99, 3 );
add_filter( 'woocommerce_widget_get_current_page_url', 'wolmart_woo_widget_get_current_page_url' );
add_filter( 'woocommerce_layered_nav_link', 'wolmart_woo_widget_clean_link' );
add_filter( 'woocommerce_show_page_title', '__return_false' );

// Dokan pro compatibility
remove_action( 'woocommerce_no_products_found', 'wc_no_products_found', 10 );
add_action( 'woocommerce_no_products_found', 'woocommerce_product_loop_start', 10, 0 );
add_action( 'woocommerce_no_products_found', 'wc_no_products_found', 20 );
add_action( 'woocommerce_no_products_found', 'woocommerce_product_loop_end', 30, 0 );

/**
 * Wolmart shop page products count
 */
if ( ! function_exists( 'wolmart_loop_shop_per_page' ) ) {
	function wolmart_loop_shop_per_page( $count_select = '' ) {
		if ( ! empty( $_GET['count'] ) ) {
			return (int) $_GET['count'];
		}

		if ( ! is_array( $count_select ) ) {
			global $wp_query;
			$query = $wp_query->query;

			$count_select = '';

			if ( ! $count_select ) {
				$count_select = wolmart_get_option( 'products_count_select' );
			}

			if ( $count_select ) {
				$count_select = explode( ',', str_replace( ' ', '', $count_select ) );
			} else {
				$count_select = array( '9', '_12', '24', '36' );
			}
		}

		$default = $count_select[0];

		foreach ( $count_select as $num ) {
			if ( is_string( $num ) && '_' == substr( $num, 0, 1 ) ) {
				$default = (int) str_replace( '_', '', $num );
				break;
			}
		}

		return $default;
	}
}

/**
 * Wolmart shop page - select form for products count
 */
if ( ! function_exists( 'wolmart_wc_count_per_page' ) ) {
	function wolmart_wc_count_per_page() {
		global $wolmart_layout;

		$ts = ! empty( $wolmart_layout['top_sidebar'] ) && 'hide' != $wolmart_layout['top_sidebar'] && is_active_sidebar( $wolmart_layout['top_sidebar'] );
		?>
		<div class="toolbox-item toolbox-show-count select-box">
			<select name="count" class="count form-control" aria-label="<?php esc_attr_e( 'Product Show Count', 'wolmart' ); ?>">
				<?php
				if ( isset( $wolmart_layout['count_select'] ) && trim( $wolmart_layout['count_select'] ) ) {
					$count_select = explode( ',', str_replace( ' ', '', $wolmart_layout['count_select'] ) );
				} else {
					$count_select = array( '9', '_12', '24', '36' );
				}

				$current = wolmart_loop_shop_per_page( $count_select );

				foreach ( $count_select as $count ) {
					$num = (int) str_replace( '_', '', $count );
					echo '<option value="' . $num . '" ' . selected( $num == $current, true, false ) . '>' . esc_html__( 'Show ', 'wolmart' ) . $num . '</option>';
				}
				?>
			</select>
			<?php
			$except = array( 'count' );
			// Keep query string vars intact
			foreach ( $_GET as $key => $val ) {
				if ( in_array( $key, $except ) ) {
					continue;
				}

				if ( is_array( $val ) ) {
					foreach ( $val as $inner_val ) {
						echo '<input type="hidden" name="' . esc_attr( $key ) . '[]" value="' . esc_attr( $inner_val ) . '" />';
					}
				} else {
					echo '<input type="hidden" name="' . esc_attr( $key ) . '" value="' . esc_attr( $val ) . '" />';
				}
			}
			?>
		</div>
		<?php
	}
}

/**
 * Wolmart shop page show type
 */
if ( ! function_exists( 'wolmart_wc_shop_show_type' ) ) {
	function wolmart_wc_shop_show_type() {
		$is_list_type = isset( $_GET['showtype'] ) && 'list' == $_GET['showtype'];

		$domain  = parse_url( get_site_url() );
		$cur_url = str_replace( 'http://', 'https://', esc_url( $domain['host'] . remove_query_arg( 'showtype' ) ) );
		?>
		<div class="toolbox-item toolbox-show-type">
			<a href="<?php echo esc_url( wolmart_add_url_parameters( $cur_url, 'showtype', 'grid' ) ); ?>" class="w-icon-grid btn-showtype<?php echo boolval( $is_list_type ) ? '' : ' active'; ?>" aria-label="<?php esc_attr_e( 'Grid Show', 'wolmart' ); ?>" role="button"></a>
			<a href="<?php echo esc_url( wolmart_add_url_parameters( $cur_url, 'showtype', 'list' ) ); ?>" class="w-icon-list btn-showtype<?php echo boolval( $is_list_type ) ? ' active' : ''; ?>" aria-label="<?php esc_attr_e( 'List Show', 'wolmart' ); ?>" role="button"></a>
		</div>
		<?php
		do_action( 'wolmart_wc_archive_after_toolbox' );
	}
}


/**
 * Hide nav list count
 */
if ( ! function_exists( 'wolmart_woo_layered_nav_count' ) ) {
	function wolmart_woo_layered_nav_count( $count_html, $count, $step ) {
		return '<span class="count">' . intval( $count ) . '</span>';
	}
}

if ( ! function_exists( 'wolmart_woo_widget_get_current_page_url' ) ) {
	/**
	 * Add showtype, count params to current page URL when various filtering works.
	 *
	 * @param string $link
	 * @return string
	 */
	function wolmart_woo_widget_get_current_page_url( $link ) {

		if ( isset( $_GET['showtype'] ) && 'list' == $_GET['showtype'] ) {
			$link = wolmart_add_url_parameters( $link, 'showtype', 'list' );
		}

		if ( ! empty( $_GET['count'] ) ) {
			$link = wolmart_add_url_parameters( $link, 'count', (int) $_GET['count'] );
		}

		return $link;
	}
}
