<?php
/**
 * Wolmart Admin Panel
 *
 * @package Wolmart Admin Panel
 * @version 1.0
 */
defined( 'ABSPATH' ) || die;

/**
 * Wolmart Admin Panel Class
 */
class Wolmart_Admin_Panel extends Wolmart_Base {


	/**
	 * Constructor
	 *
	 * @since 1.0
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'add_admin_menus' ), 5 );
	}

	public function add_admin_menus() {

		if ( current_user_can( 'edit_theme_options' ) ) {

			// Menu - wolmart
			$title = esc_html( wolmart_get_option( 'white_label_title' ) );
			$icon  = esc_html( wolmart_get_option( 'white_label_icon' ) );
			add_menu_page( 'Wolmart', $title ? $title : 'Wolmart', 'administrator', 'wolmart', array( $this, 'panel_activate' ), $icon ? $icon : 'dashicons-wolmart-logo', '2' );

			// Menu - wolmart / licence
			add_submenu_page( 'wolmart', esc_html__( 'License', 'wolmart' ), esc_html__( 'License', 'wolmart' ), 'administrator', 'wolmart', array( $this, 'panel_activate' ) );

			// Menu - wolmart / theme options
			add_submenu_page( 'wolmart', esc_html__( 'Theme Options', 'wolmart' ), esc_html__( 'Theme Options', 'wolmart' ), 'administrator', 'customize.php', '' );

			// Menu - wolmart / layout builder
			if ( class_exists( 'Wolmart_Layout_Builder_Admin' ) ) {
				add_submenu_page( 'wolmart', esc_html__( 'Layout Builder', 'wolmart' ), esc_html__( 'Layout Builder', 'wolmart' ), 'manage_options', 'wolmart-layout-builder', array( Wolmart_Layout_Builder_Admin::get_instance(), 'view_layout_builder' ), 4 );
			} else {
				add_submenu_page( 'wolmart', esc_html__( 'Layout Builder', 'wolmart' ), esc_html__( 'Layout Builder', 'wolmart' ), 'manage_options', 'admin.php?page=wolmart-layout-builder', '', 4 );
			}
		}
	}

	public function view_header( $active_page ) {
		require_once WOLMART_ADMIN . '/panel/views/header.php';
	}

	public function view_footer() {
		require_once WOLMART_ADMIN . '/panel/views/footer.php';
	}

	public function panel_activate() {
		$this->view_header( 'license' );
		require_once WOLMART_ADMIN . '/panel/views/license.php';
		$this->view_footer();
	}
}

Wolmart_Admin_Panel::get_instance();
