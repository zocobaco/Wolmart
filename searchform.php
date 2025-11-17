<?php
/**
 * The search-form template
 *
 * @package Wolmart WordPress Framework
 * @since 1.2.0
 */

defined( 'ABSPATH' ) || die;

$options          = $args['aria_label'];
$where            = isset( $options ) && isset( $options['where'] ) ? $options['where'] : '';
$live_search      = (bool) wolmart_get_option( 'live_search' );
$search_type      = isset( $options['type'] ) ? $options['type'] : wolmart_get_option( 'search_form_type' );
$class            = $search_type;
$search_post_type = isset( $options['search_post_type'] ) ? $options['search_post_type'] : '';
$icon             = isset( $options['icon'] ) ? $options['icon'] : 'w-icon-search';
$show_keywords    = isset( $options['show_keywords'] ) ? $options['show_keywords'] : '';

if ( function_exists( 'wolmart_get_option' ) && wolmart_get_option( 'save_search' ) && 'yes' == $show_keywords ) {
	$keywords_html     = '<div class="search-keywords-container">
	<span>' . esc_html__( 'Popular Searches:', 'wolmart' ) . '</span>';
	$keyword_content   = isset( $options['keyword_content'] ) && $options['keyword_content'] ? $options['keyword_content'] : '';
	$keyword_count     = isset( $options['keyword_count'] ) && $options['keyword_count']['size'] ? $options['keyword_count']['size'] : 8;
	$res_keyword_count = $options['res_keyword_count'] ? (int) $options['res_keyword_count'] : (int) $keyword_count;

	$default_keys = explode( '|', $keyword_content );

	$results = apply_filters( 'wolmart_get_most_used_search_keys', array() );

	if ( count( $default_keys ) > 1 ) {
		foreach ( $default_keys as $item ) {
			array_push(
				$results,
				(object) array(
					'keyword' => sprintf( esc_html__( '%s', 'wolmart' ), $item ),
					'count'   => '1',
				)
			);
		}
	}

	$link = get_home_url();

	if ( 0 !== strpos( strrev( $link ), '/' ) ) {
		$link = $link . '/';
	}

	if ( count( $results ) ) {
		$keywords_html .= '<div class="search-keywords-box">';
		$temp           = array();

		foreach ( $results as $key => $keyword ) {

			if ( count( $temp ) == absint( $keyword_count ) ) {
				break;
			}

			if ( in_array( $keyword->keyword, $temp ) ) {
				continue;
			}

			array_push( $temp, $keyword->keyword );

			if ( $search_post_type ) {
				$link = add_query_arg( 'post_type', $search_post_type, $link );
			}

			$link = add_query_arg( 's', $keyword->keyword, $link );

			$link = str_replace( ' ', '%20', $link );

			if ( $link && $keyword->keyword ) {
				$keywords_html .= '<a rel="nofollow" href="' . esc_url( $link ) . '" role="link">' . esc_html( $keyword->keyword ) . '</a>';
			}
		}

		$keywords_html .= '</div>';

		echo '<style>';

		echo '@media (max-width:' . ( function_exists( 'wolmart_get_option' ) ? (int) wolmart_get_option( 'container' ) - 1 : 1279 ) . 'px) and (min-width: 992px) {';
		echo '.search-keywords-box a:nth-last-child(-n+' . ( (int) $keyword_count - $res_keyword_count ) . ') { display: none; } }';

		echo '</style>';

	} else {
		$keywords_html .= esc_html__( '&nbsp;No Search keywords', 'wolmart' );
	}

	$keywords_html .= '</div>';
}

if ( isset( $options['placeholder'] ) ) {
	$placeholder = $options['placeholder'];
} else {
	if ( 'post' == $search_post_type ) {
		$placeholder = esc_html__( 'Search in Blog', 'wolmart' );
	} else {
		$placeholder = esc_html__( 'Search', 'wolmart' );
	}
}

if ( '' == $where && ! isset( $options['type'] ) ) {
	$search_type = 'hs-simple';
	$class       = 'hs-simple';
}
$is_fullscreen = true === wolmart_get_option( 'full_screen_search' ) && wp_is_mobile();
?>

<div class="search-wrapper 
<?php
echo esc_attr( $class );
echo ( $is_fullscreen ? ' search-fullscreen' : '' );
?>
">
	<?php
	if ( $is_fullscreen ) {
		?>
		<a class="close-btn" aria-label="<?php esc_attr_e( 'Close', 'wolmart' ); ?>" role="button">
			<i class="w-icon-times"></i>
		</a>
		<?php
	}
	?>
	<form action="<?php echo esc_url( home_url() ); ?>/" method="get" class="input-wrapper">
		<input type="hidden" name="post_type" value="<?php echo esc_attr( $search_post_type ); ?>"/>

		<?php if ( 'header' == $where && ( 'hs-expanded' == $search_type ) ) : ?>
		<div class="select-box">
			<?php
			if ( '' === $search_post_type ) {
				$post_cats = get_categories( array( 'type' => 'post' ) );
				echo '<select name="cat" aria-label="' . esc_attr( 'Categories to search', 'wolmart' ) . '" id="cat" class="cat all-cats">';
				echo '<option value="0">' . esc_attr( 'All Categories', 'wolmart' ) . '</option>';
				if ( ! empty( $post_cats ) ) {
					echo '<optgroup label="' . esc_attr( 'Post', 'wolmart' ) . '" data-type="post">';
					echo '<option value="">' . esc_attr( 'All Post Categories', 'wolmart' ) . '</option>';
					foreach ( $post_cats as $post_cat ) {
						echo '<option value="' . $post_cat->slug . '">' . $post_cat->name . '</option>';
					}
					echo '</optgroup>';
				}
				$product_cats = get_categories( array( 'taxonomy' => 'product_cat' ) );
				if ( ! empty( $product_cats ) ) {
					echo '<optgroup label="' . esc_attr( 'Product', 'wolmart' ) . '" data-type="product">';
					echo '<option value="">' . esc_attr( 'All Product Categories', 'wolmart' ) . '</option>';
					foreach ( $product_cats as $product_cat ) {
						echo '<option value="' . $product_cat->slug . '">' . $product_cat->name . '</option>';
					}
					echo '</optgroup>';
				}
				echo '</select>';
			} else {
				$args = array(
					'show_option_all' => esc_html__( 'All Categories', 'wolmart' ),
					'hierarchical'    => 1,
					'class'           => 'cat',
					'echo'            => 1,
					'value_field'     => 'slug',
					'selected'        => 1,
					'depth'           => 1,
				);
				if ( 'product' == $search_post_type && class_exists( 'WooCommerce' ) ) {
					$args['taxonomy'] = 'product_cat';
					$args['name']     = 'product_cat';
				}
				wp_dropdown_categories( $args );
			}
			?>
		</div>
		<?php endif; ?>

		<input type="search" aria-label="<?php esc_attr_e( 'Search', 'wolmart' ); ?>" class="form-control" name="s" placeholder="<?php echo esc_attr( $placeholder ); ?>" required="" autocomplete="off">

		<?php if ( $live_search && ! $is_fullscreen ) : ?>
			<div class="live-search-list"></div>
		<?php endif; ?>

		<button class="btn btn-search" aria-label="<?php esc_attr_e( 'Search Button', 'wolmart' ); ?>" type="submit">
			<i class="<?php echo esc_attr( $icon ); ?>"></i>
		</button> 
	</form>
	<?php

	if ( function_exists( 'wolmart_get_option' ) && wolmart_get_option( 'save_search' ) && 'yes' == $show_keywords && $keywords_html ) {
		echo wolmart_escaped( $keywords_html );
	}
	if ( $live_search && $is_fullscreen ) :
		if ( function_exists( 'wolmart_get_option' ) && ! empty( wolmart_get_option( 'screen_search_banners' ) ) ) {
			$height        = wolmart_get_option( 'screen_search_banners_height' ) ? (int) wolmart_get_option( 'screen_search_banners_height' ) : 150;
			$slide_options = array(
				'slidesPerView' => 1,
				'navigation'    => false,
				'pagination'    => true,
				'loop'          => false,
			);
			?>
			<div class="search-banner-slider-wrapper">
				<div class="search-banner-slider" style="--wolmart-ads-banner-height:<?php echo esc_attr( $height ); ?>px">
					<div class="slider-wrapper" data-slider-options="<?php echo esc_attr( json_encode( $slide_options ) ); ?>">
						<?php
						foreach ( wolmart_get_option( 'screen_search_banners' ) as $banner ) {
							if ( ! empty( $banner['image'] ) ) :
								?>
									<a class="mobile-search-ads slider-slide" href="<?php echo esc_attr( ! empty( $banner['url'] ) ? $banner['url'] : '#' ); ?>" aria-label="<?php esc_attr_e( 'Mobile Search', 'wolmart' ); ?>">
										<figure>
											<img src="<?php echo esc_attr( $banner['image'] ); ?>" alt="<?php esc_html_e( 'Ads Banner', 'wolmart' ); ?>" />
										</figure>
									</a>
								<?php
								endif;
						}
						?>
					</div>
				</div>
			</div>
			<?php
		}
		?>
		<div class="live-search-list"></div>
		<?php
	endif;
	?>
</div>
