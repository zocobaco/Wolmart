<?php
/**
 * Initialize TGM plugins
 */
class Wolmart_TGM_Plugins {

	/**
	 * Array of plugins. Required keys are name and slug.
	 * If the source is NOT from the .org repo, then source is also required.
	 */
	protected $plugins = array(
		array(
			'name'      => 'Wolmart Core',
			'slug'      => 'wolmart-core',
			'required'  => true,
			'version'   => '1.1.4',
			'url'       => 'wolmart-core/wolmart-core.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/wolmart-core.svg',
			'usein'     => 'setup',
		),
		array(
			'name'       => 'Alpus Elementor FlexBox Addon',
			'slug'       => 'alpus-flexbox',
			'required'   => false,
			'version'   => '2.0.0',
			'url'        => 'alpus-flexbox/init.php',
			'image_url'  => WOLMART_ADMIN_URI . '/plugins/images/alpus-flexbox.png',
			'usein'      => 'setup',
		),
		array(
			'name'      => 'Alpus AI Product Review Summary',
			'slug'      => 'alpus-aprs',
			'required'  => false,
			'version'   => '1.0.0',
			'url'       => 'alpus-aprs/init.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/alpus-aprs.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'Kirki',
			'slug'      => 'kirki',
			'required'  => true,
			'url'       => 'kirki/kirki.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/kirki.png',
			'usein'     => 'setup',
		),
		array(
			'name'       => 'Customizer Search',
			'slug'       => 'customizer-search',
			'required'   => true,
			'url'        => 'customizer-search/customizer-search.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/custom-search.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'Woocommerce',
			'slug'      => 'woocommerce',
			'required'  => true,
			'url'       => 'woocommerce/woocommerce.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/woocommerce.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'Meta-Box',
			'slug'      => 'meta-box',
			'required'  => true,
			'url'       => 'meta-box/meta-box.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/meta_box.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'Elementor',
			'slug'      => 'elementor',
			'required'  => true,
			'url'       => 'elementor/elementor.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/elementor.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'WPBakery Page Builder',
			'slug'      => 'js_composer',
			'required'  => true,
			'url'       => 'js_composer/js_composer.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/wpb.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'Contact Form 7',
			'slug'      => 'contact-form-7',
			'required'  => true,
			'url'       => 'contact-form-7/wp-contact-form-7.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/contact_form_7.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'YITH Woocommerce Wishlist',
			'slug'      => 'yith-woocommerce-wishlist',
			'required'  => true,
			'url'       => 'yith-woocommerce-wishlist/init.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/yith_wishlist.png',
			'usein'     => 'setup',
		),
		array(
			'name'     => 'PWA for WP',
			'slug'     => 'pwa-for-wp',
			'required' => false,
			'url'      => 'pwa-for-wp/pwa-for-wp.php',
			'usein'    => 'setup',
		),
		array(
			'name'      => 'Regenerate Thumbnails',
			'slug'      => 'regenerate-thumbnails',
			'required'  => false,
			'url'       => 'regenerate-thumbnails/regenerate-thumbnails.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/regenerate_thumbnails.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'Dokan Lite',
			'slug'      => 'dokan-lite',
			'required'  => false,
			'url'       => 'dokan-lite/dokan.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/dokan-lite.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'WCFM - WooCommerce Multivendor Marketplace',
			'slug'      => 'wc-multivendor-marketplace',
			'required'  => false,
			'url'       => 'wc-multivendor-marketplace/wc-multivendor-marketplace.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/wcfm.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'Multivendor Marketplace Solution for WooCommerce - WC Marketplace',
			'slug'      => 'dc-woocommerce-multi-vendor',
			'required'  => false,
			'url'       => 'dc-woocommerce-multi-vendor/dc_product_vendor.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/wcmp.png',
			'usein'     => 'setup',
		),
		array(
			'name'      => 'WC Vendors Marketplace',
			'slug'      => 'wc-vendors',
			'required'  => false,
			'url'       => 'wc-vendors/class-wc-vendors.php',
			'image_url' => WOLMART_ADMIN_URI . '/plugins/images/wcvendors.png',
			'usein'     => 'setup',
		),
		array(
			'name'       => 'WP Super Cache',
			'slug'       => 'wp-super-cache',
			'required'   => false,
			'url'        => 'wp-super-cache/wp-cache.php',
			'visibility' => 'optimize_wizard',
			'desc'       => 'plugin generates static html files from your dynamic WordPress blog.',
			'usein'      => 'optimize',
		),
		array(
			'name'       => 'Fast Velocity Minify',
			'slug'       => 'fast-velocity-minify',
			'required'   => false,
			'url'        => 'fast-velocity-minify/fvm.php',
			'visibility' => 'optimize_wizard',
			'desc'       => 'plugin reduces HTTP requests by merging CSS & Javascript files into groups of files, while attempting to use the least amount of files as possible.',
			'usein'      => 'optimize',
		),
		array(
			'name'       => 'Advanced Database Cleaner',
			'slug'       => 'advanced-database-cleaner',
			'required'   => false,
			'url'        => 'advanced-database-cleaner/advanced-db-cleaner.php',
			'visibility' => 'optimize_wizard',
			'desc'       => 'plugin cleans up database by deleting orphaned items such as old revisions, spam comments, optimize database and more...',
			'usein'      => 'optimize',
		),
		array(
			'name'       => 'Wp Optimize',
			'slug'       => 'wp-optimize',
			'required'   => false,
			'url'        => 'wp-optimize/wp-optimize.php',
			'visibility' => 'optimize_wizard',
			'desc'       => 'plugin cleans your database by removing unnecessary data, tables and data fragmentation, compresses your images and caches your site for your super fast load times',
			'usein'      => 'optimize',
		),
	);

	public function __construct() {
		/* TGM Plugin Activation */
		$plugin = WOLMART_ADMIN . '/plugins/tgm-plugin-activation/class-tgm-plugin-activation.php';
		if ( ! class_exists( 'TGM_Plugin_Activation' ) ) {
			require_once $plugin;
		}

		add_action( 'tgmpa_register', array( $this, 'register_required_plugins' ) );

		add_filter( 'tgmpa_notice_action_links', array( $this, 'update_action_links' ), 10, 1 );

		$this->plugins = $this->get_plugins_list();
	}

	public function get_plugins_list() {
		// get transient
		$plugins = get_site_transient( 'wolmart_plugins' );
		if ( ! $plugins ) {
			$plugins = $this->update_plugins_list();
		}
		if ( ! $plugins ) {
			return $this->plugins;
		}
		return array_merge( $plugins, $this->plugins );
	}

	private function update_plugins_list() {

		require_once WOLMART_ADMIN . '/importer/importer-api.php';
		$importer_api = new Wolmart_Importer_API();
		$plugins      = $importer_api->get_response( 'plugins_version' );

		if ( is_wp_error( $plugins ) || ! $plugins ) {
			return false;
		}

		$args = $importer_api->generate_args( false );

		foreach ( $plugins as $key => $plugin ) {
			$args['plugin']               = $plugin['slug'];
			$plugins[ $key ]['source']    = add_query_arg( $args, $importer_api->get_url( 'plugins' ) );
			$plugins[ $key ]['image_url'] = WOLMART_ADMIN_URI . '/plugins/images/' . $args['plugin'] . '.png';
			if ( ! isset( $plugins[ $key ]['usein'] ) ) {
				$plugins[ $key ]['usein'] = 'setup';
			}
		}
		// set transient
		set_site_transient( 'wolmart_plugins', $plugins, 4 * 24 * HOUR_IN_SECONDS );

		return $plugins;
	}

	public function register_required_plugins() {
		/**
		 * Array of configuration settings. Amend each line as needed.
		 * If you want the default strings to be available under your own theme domain,
		 * leave the strings uncommented.
		 * Some of the strings are added into a sprintf, so see the comments at the
		 * end of each line for what each argument will be.
		 */
		$config = array(
			'domain'       => 'wolmart',                    // Text domain - likely want to be the same as your theme.
			'default_path' => '',                          // Default absolute path to pre-packaged plugins
			'menu'         => 'install-required-plugins',  // Menu slug
			'has_notices'  => true,                        // Show admin notices or not
			'is_automatic' => true,                        // Automatically activate plugins after installation or not
			'message'      => '',                          // Message to output right before the plugins table
		);

		tgmpa( $this->plugins, $config );
	}

	public function update_action_links( $action_links ) {
		$url = add_query_arg(
			array(
				'page' => 'wolmart-setup-wizard',
				'step' => 'default_plugins',
			),
			self_admin_url( 'admin.php' )
		);
		foreach ( $action_links as $key => $link ) {
			if ( $link ) {
				$link                 = preg_replace( '/<a([^>]*)href="([^"]*)"/i', '<a$1href="' . esc_url( $url ) . '"', $link );
				$action_links[ $key ] = $link;
			}
		}
		return $action_links;
	}
}

new Wolmart_TGM_Plugins();
