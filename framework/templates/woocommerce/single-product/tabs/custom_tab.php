<?php
/**
 * Single Product Custom Tab
 */
defined( 'ABSPATH' ) || die;

if ( isset( $tab_name ) ) {

	if ( 'wolmart_product_tab' == $tab_name ) {
		$tab_title = wolmart_get_option( 'product_tab_title' );
		$tab_block = wolmart_get_option( 'product_tab_block' );
		echo apply_filters( 'wolmart_product_tab_title', ( $tab_title ? ( '<h2>' . esc_html( $tab_title ) . '</h2>' ) : '' ), $tab_name );
		wolmart_print_template( $tab_block );
	}
	if ( 'wolmart_custom_tab_1st' == $tab_name ) {
		$tab_title = get_post_meta( get_the_ID(), 'wolmart_custom_tab_title_1st', true );
		if ( $tab_title ) {
			$tab_content = get_post_meta( get_the_ID(), 'wolmart_custom_tab_content_1st', true );
			echo '<h2>' . esc_html( $tab_title ) . '</h2>';
			echo wolmart_strip_script_tags( $tab_content );
		}
	} elseif ( 'wolmart_custom_tab_2nd' == $tab_name ) {
		$tab_title = get_post_meta( get_the_ID(), 'wolmart_custom_tab_title_2nd', true );
		if ( $tab_title ) {
			$tab_content = get_post_meta( get_the_ID(), 'wolmart_custom_tab_content_2nd', true );
			echo '<h2>' . esc_html( $tab_title ) . '</h2>';
			echo wolmart_strip_script_tags( $tab_content );
		}
	} elseif ( 'wolmart_pa_block_' == substr( $tab_name, 0, strlen( 'wolmart_pa_block_' ) ) && ! empty( $tab_data['block_id'] ) ) {
		wolmart_print_template( absint( $tab_data['block_id'] ) );
	}
}
