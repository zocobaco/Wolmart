<?php
/**
 * Wolmart_YITH_WCWL class
 *
 * @version 1.0
 * @package Wolmart WordPress Framework
 * @since 1.8.8
 */
defined( 'ABSPATH' ) || die;

class Wolmart_YITH_WCWL extends Wolmart_Base {
    /**
     * Init
     */
    public function init() {
        if ( is_admin() && class_exists( 'YITH_WCWL_Rendering_Method_Admin_Handler' ) ) {
            remove_action( 'admin_notices', array( YITH_WCWL_Rendering_Method_Admin_Handler::get_instance(), 'add_notices' ) );
        }

        add_action( 'customize_save_after', array( $this, 'disable_react_rendering_mode' ), 99 );
        add_action( 'wolmart_demo_imported', array( $this, 'disable_react_rendering_mode' ) );
    }

    public function disable_react_rendering_mode() {
        if ( 'php-templates' !== get_option( 'yith_wcwl_rendering_method' ) ) {
            update_option( 'yith_wcwl_rendering_method', 'php-templates' );
        }
    }

}
Wolmart_YITH_WCWL::get_instance()->init();
