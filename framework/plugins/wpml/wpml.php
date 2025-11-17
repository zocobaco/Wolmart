<?php
/**
 * Wolmart_WPML class
 *
 * @version 1.0
 * @package Wolmart WordPress Framework
 * @since 1.8.6
 */
defined( 'ABSPATH' ) || die;

class Wolmart_WPML extends Wolmart_Base {
    /**
     * Constructor
     */
    public function __construct() {
        if ( defined( 'WCML_VERSION' ) ) {
            $this->wcml_init();
        }
    }

    /**
     * Init
     */
    public function wcml_init() {
        add_filter( 'wcml_multi_currency_ajax_actions', array( $this, 'ajax_actions' ), 10, 1 );
    }

    public function ajax_actions( $ajax_actions ) {
        $ajax_actions[] = 'wolmart_add_to_cart';
        $ajax_actions[] = 'wolmart_quickview';
        $ajax_actions[] = 'wolmart_ajax_add_to_cart';
        $ajax_actions[] = 'wolmart_loadmore';
        $ajax_actions[] = 'wolmart_cart_item_remove';
        $ajax_actions[] = 'wolmart_update_mini_wishlist';
        $ajax_actions[] = 'remove_from_wishlist';
        $ajax_actions[] = 'wolmart_remove_cart_item';
        $ajax_actions[] = 'wolmart_cart_related_products';
        $ajax_actions[] = 'wolmart_add_to_compare';
        $ajax_actions[] = 'wolmart_remove_from_compare';

        return $ajax_actions;
    }
}
Wolmart_WPML::get_instance();
