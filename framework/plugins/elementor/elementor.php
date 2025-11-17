<?php
/**
 * Elementor Compatibility
 *
 * @package Wolmart WordPress Framework
 * @since 1.0
 */
defined( 'ABSPATH' ) || die;

add_action( 'wolmart_demo_imported', 'wolmart_update_elementor_settings', 99, 2 );
add_action( 'wolmart_demo_imported', 'wolmart_update_elementor_preferences', 99 );
add_action( 'wolmart_demo_imported', 'wolmart_update_elementor_data', 99 );
add_action( 'elementor/core/files/clear_cache', 'wolmart_update_elementor_data', 99 );
add_action( 'customize_save_after', 'wolmart_update_elementor_settings', 99 );
add_action( 'customize_save_after', 'wolmart_update_elementor_preferences', 99 );
add_action( 'register_new_user', 'wolmart_update_elementor_preferences', 99 );

/**
 * wolmart_update_elementor_settings
 *
 * update default elementor active kit options
 *
 * @since 1.0
 */
function wolmart_update_elementor_settings( $user_id = -1, $add_kit = false ) {

	$default_kit = false;
	$kit         = Elementor\Plugin::$instance->kits_manager->get_active_kit();

	if ( ! $kit->get_id() && $add_kit ) {
		// Create elementor default kit
		$default_kit = Elementor\Plugin::$instance->kits_manager->create_default();
		if ( $default_kit ) {
			update_option( Elementor\Core\Kits\Manager::OPTION_ACTIVE, $default_kit );
		}
	} elseif ( $kit->get_id() ) {
		$default_kit = $kit->get_id();
	}

	if ( $default_kit ) {
		$general_settings = get_post_meta( $default_kit, '_elementor_page_settings', true );
		$changed          = false;

		if ( empty( $general_settings ) ) {
			$general_settings = array();
		}

		// container width
		if ( empty( $general_settings['container_width'] ) || ! isset( $general_settings['container_width']['size'] ) || $general_settings['container_width']['size'] != wolmart_get_option( 'container' ) ) {
			$general_settings['container_width'] = array(
				'size'  => wolmart_get_option( 'container' ),
				'unit'  => 'px',
				'sizes' => array(),
			);
			$changed                             = true;
		}

		if ( version_compare( ELEMENTOR_VERSION, '3.17.0', '>=' ) ) {
			// space between widgets
			if ( empty( $general_settings['space_between_widgets'] ) || ! isset( $general_settings['space_between_widgets']['size'] ) || $general_settings['space_between_widgets']['size'] != 0 || ! isset( $general_settings['space_between_widgets']['column'] ) || $general_settings['space_between_widgets']['column'] != 0 ) {
				$general_settings['space_between_widgets'] = array(
					'size'   => 0,
					'row'    => 0,
					'column' => 0,
					'unit'   => 'px',
					'sizes'  => array(),
				);
				$changed                                   = true;
			}
		} else {
			if ( isset( $general_settings['space_between_widgets'] ) && isset( $general_settings['space_between_widgets']['row'] ) ) {
				unset( $general_settings['space_between_widgets'] );
			}
			// space between widgets
			if ( empty( $general_settings['space_between_widgets'] ) || ! isset( $general_settings['space_between_widgets']['size'] ) || $general_settings['space_between_widgets']['size'] != 0 ) {
				$general_settings['space_between_widgets'] = array(
					'size'  => 0,
					'unit'  => 'px',
					'sizes' => array(),
				);
				$changed                                   = true;
			}
		}
		// responsive breadkpoint
		if ( empty( $general_settings['viewport_lg'] ) || 991 != $general_settings['viewport_lg'] ) {
			$general_settings['viewport_lg'] = 991;
			$changed                         = true;
		}

		if ( ! isset( $general_settings['viewport_tablet'] ) || 991 != $general_settings['viewport_tablet'] || 991 != get_option( 'elementor_viewport_lg', 1025 ) ) {
			$general_settings['viewport_tablet'] = 991;
			update_option( 'elementor_viewport_lg', 991 );
			$changed = true;
		}

		// system colors
		if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][0] ) || $general_settings['system_colors'][0]['color'] != wolmart_get_option( 'primary_color' ) ) {
			$general_settings['system_colors'][0]['_id']   = 'primary';
			$general_settings['system_colors'][0]['title'] = esc_html__( 'Primary', 'wolmart' );
			$general_settings['system_colors'][0]['color'] = wolmart_get_option( 'primary_color' );
			$changed                                       = true;
		}
		if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][1] ) || $general_settings['system_colors'][1]['color'] != wolmart_get_option( 'secondary_color' ) ) {
			$general_settings['system_colors'][1]['_id']   = 'secondary';
			$general_settings['system_colors'][1]['title'] = esc_html__( 'Secondary', 'wolmart' );
			$general_settings['system_colors'][1]['color'] = wolmart_get_option( 'secondary_color' );
			$changed                                       = true;
		}
		// if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][2] ) || $general_settings['system_colors'][2]['color'] != wolmart_get_option( 'typo_default' )['color'] ) {
		if ( isset( wolmart_get_option( 'typo_default' )['color'] ) && ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][2] ) || $general_settings['system_colors'][2]['color'] != wolmart_get_option( 'typo_default' )['color'] ) ) {
			$general_settings['system_colors'][2]['_id']   = 'text';
			$general_settings['system_colors'][2]['title'] = esc_html__( 'Text', 'wolmart' );
			$general_settings['system_colors'][2]['color'] = wolmart_get_option( 'typo_default' )['color'];
			$changed                                       = true;
		}
		/*if ( empty( $general_settings['system_colors'] ) || ! isset( $general_settings['system_colors'][3] ) || ( isset( $general_settings['system_colors'][3]['color'] ) && $general_settings['system_colors'][3]['color'] != wolmart_get_option( 'success_color' ) ) ) {
			$general_settings['system_colors'][3]['_id']   = 'success';
			$general_settings['system_colors'][3]['title'] = esc_html__( 'Success', 'wolmart' );
			$general_settings['system_colors'][3]['color'] = wolmart_get_option( 'success_color' );
			$changed                                       = true;
		}*/

		// system fonts
		if ( empty( $general_settings['system_typography'] ) ) {
			$general_settings['system_typography'] = array(
				array(
					'_id'                    => 'primary',
					'title'                  => esc_html( 'Primary', 'elementor' ),
					'typography_typography'  => 'custom',
					'typography_font_family' => wolmart_get_option( 'typo_default' )['font-family'],
					'typography_font_weight' => 'default',
				),
				array(
					'_id'                    => 'secondary',
					'title'                  => esc_html( 'Secondary', 'elementor' ),
					'typography_typography'  => 'custom',
					'typography_font_family' => 'default',
					'typography_font_weight' => 'default',
				),
				array(
					'_id'                    => 'text',
					'title'                  => esc_html( 'Text', 'elementor' ),
					'typography_typography'  => 'custom',
					'typography_font_family' => 'default',
					'typography_font_weight' => 'default',
				),
				array(
					'_id'                    => 'accent',
					'title'                  => esc_html( 'Accent', 'elementor' ),
					'typography_typography'  => 'custom',
					'typography_font_family' => 'default',
					'typography_font_weight' => 'default',
				),
			);

			$changed = true;
		}

		if ( $changed ) {
			update_post_meta( $default_kit, '_elementor_page_settings', $general_settings );

			try {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			} catch ( Exception $e ) {
			}
		}
	}

	if ( false === get_option( 'elementor_disable_color_schemes', false ) ) {
		update_option( 'elementor_disable_color_schemes', 'yes' );
	}
	if ( false === get_option( 'elementor_disable_typography_schemes', false ) ) {
		update_option( 'elementor_disable_typography_schemes', 'yes' );
	}
	if ( false === get_option( 'elementor_experiment-e_dom_optimization', false ) ) {
		update_option( 'elementor_experiment-e_dom_optimization', 'active' );
	}
}

/**
 * wolmart_update_elementor_preferences
 *
 * update default elementor preference values
 *  - panel width to 340
 *
 * @since 1.0
 */
function wolmart_update_elementor_preferences( $user_id = -1 ) {
	if ( ( is_int( $user_id ) && -1 == $user_id ) || doing_action( 'customize_save_after' ) ) {
		$user_id = get_current_user_id();
	}

	$preference = get_user_meta( $user_id, 'elementor_preferences' );
	if ( empty( $preference[0] ) || empty( $preference[0]['panel_width'] ) ) {
		$preference[0]['panel_width'] = array(
			'unit'  => 'px',
			'size'  => 340,
			'sizes' => array(),
		);
	}

	update_user_meta( $user_id, 'elementor_preferences', $preference[0] );
}

if ( ! function_exists( 'wolmart_update_elementor_data' ) ) {
	function wolmart_update_elementor_data() {
		delete_post_meta_by_key( 'wolmart_elementor_page_assets_saved' );
	}
}
