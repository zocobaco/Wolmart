<?php
/**
 * Post Layout
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 * @version 1.0
 */
defined( 'ABSPATH' ) || die;

do_action( 'wolmart_print_before_page_layout' );

if ( is_single() ) :
	wolmart_get_template_part( 'posts/single' );
else :
	wolmart_get_template_part( 'posts/archive' );
endif;

do_action( 'wolmart_print_after_page_layout' );
