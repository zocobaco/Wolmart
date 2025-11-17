<?php

class Wolmart_Skeleton extends Wolmart_Base {
	public $is_doing = '';

	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 20 );

		if ( ! defined( 'WOOF_VERSION' ) ) {
			// Sidebar skeleton
			add_action( 'wolmart_sidebar_content_start', array( $this, 'sidebar_content_start' ) );
			add_action( 'wolmart_sidebar_content_end', array( $this, 'sidebar_content_end' ) );
			add_filter( 'wolmart_sidebar_classes', array( $this, 'sidebar_classes' ) );

			// Posts (archive + single) skeleton
			add_filter( 'wolmart_post_loop_wrapper_classes', array( $this, 'post_loop_wrapper_class' ) );
			add_filter( 'wolmart_post_single_class', array( $this, 'post_loop_wrapper_class' ) );
			add_action( 'wolmart_post_loop_before_item', array( $this, 'post_loop_before_item' ) );
			add_action( 'wolmart_post_loop_after_item', array( $this, 'post_loop_after_item' ) );

			// Archive products & categories skeleton
			add_filter( 'wolmart_product_loop_wrapper_classes', array( $this, 'product_loop_wrapper_class' ) );
			add_action( 'wolmart_product_loop_before_item', array( $this, 'product_loop_before_item' ) );
			add_action( 'wolmart_product_loop_before_cat', array( $this, 'product_loop_before_cat' ) );
			add_action( 'wolmart_product_loop_after_cat', array( $this, 'product_loop_after_cat' ) );
			add_action( 'wolmart_product_loop_after_item', array( $this, 'product_loop_after_item' ) );
		}

		// Single product skeleton
		add_filter( 'wolmart_single_product_classes', array( $this, 'single_product_classes' ) );
		add_action( 'wolmart_before_product_gallery', array( $this, 'before_product_gallery' ), 20 );
		add_action( 'wolmart_after_product_gallery', array( $this, 'after_product_gallery' ), 20 );

		if ( ! defined( 'WOLMART_VENDORS' ) && ! class_exists( 'Uni_Cpo' ) ) {
			// We disable skeleton screen for single product page's summary and tabs,
			// because it has too many compatibility issues.
			add_action( 'wolmart_before_product_summary', array( $this, 'before_product_summary' ), 20 );
			add_action( 'wolmart_after_product_summary', array( $this, 'after_product_summary' ), 20 );
			add_action( 'wolmart_wc_product_before_tabs', array( $this, 'before_product_tabs' ), 20 );
			add_action( 'woocommerce_product_after_tabs', array( $this, 'after_product_tabs' ), 20 );
		}
		// Menu lazyload skeleton
		add_filter( 'wolmart_menu_lazyload_content', array( $this, 'menu_skeleton' ), 10, 3 );
	}

	public function enqueue_scripts() {
		wp_enqueue_style( 'wolmart-skeleton', WOLMART_ADDONS_URI . '/skeleton/skeleton' . ( is_rtl() ? '-rtl' : '' ) . '.min.css' );
		wp_enqueue_script( 'wolmart-skeleton', WOLMART_ADDONS_URI . '/skeleton/skeleton' . ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js' ), array( 'wolmart-theme' ), WOLMART_VERSION, true );
		wp_localize_script(
			'wolmart-skeleton-js',
			'lib_skeleton',
			array(
				'lazyload' => wolmart_get_option( 'lazyload' ),
			)
		);
	}

	public function sidebar_content_start() {
		$layout_type = wolmart_get_page_layout();

		if ( 'archive_post' == $layout_type || 'single_post' == $layout_type || 'archive_product' == $layout_type || 'single_product' == $layout_type ) {
			ob_start();
			$this->is_doing = 'sidebar';
		}
	}

	public function sidebar_content_end() {
		if ( 'sidebar' == $this->is_doing ) {
			echo '<script type="text/template">' . json_encode( ob_get_clean() ) . '</script>';
			echo '<div class="widget-2"></div>';
		}

		$this->is_doing = '';
	}

	public function sidebar_classes( $class ) {
		$layout_type = wolmart_get_page_layout();

		if ( ! in_array( 'top-sidebar', $class ) && ( 'archive_post' == $layout_type || 'single_post' == $layout_type || 'archive_product' == $layout_type || 'single_product' == $layout_type ) ) {
			$class[] = 'skeleton-body';
		}
		return $class;
	}

	public function product_loop_wrapper_class( $classes ) {
		if ( ! $this->is_doing ) {
			$layout_type = wolmart_get_page_layout();
			if ( 'archive_product' == $layout_type || 'single_product' == $layout_type ) {
				$classes[] = 'skeleton-body';
			}
		}
		return $classes;
	}

	public function product_loop_before_item() {
		if ( ! $this->is_doing ) {
			$layout_type = wolmart_get_page_layout();
			if ( 'archive_product' == $layout_type || 'single_product' == $layout_type ) {
				ob_start();
				$this->is_doing = 'product';
			}
		}
	}

	public function product_loop_after_item( $product_type ) {
		if ( 'product' == $this->is_doing ) {
			$layout_type = wolmart_get_page_layout();
			if ( 'archive_product' == $layout_type || 'single_product' == $layout_type ) {
				echo '<script type="text/template">' . json_encode( ob_get_clean() ) . '</script>';
				echo '<div class="skel-pro' . ( 'list' == $product_type ? ' skel-pro-list' : '' ) . '"></div>';
				$this->is_doing = '';
			}
		}
	}

	public function product_loop_before_cat() {
		if ( ! $this->is_doing ) {
			$layout_type = wolmart_get_page_layout();
			if ( 'archive_product' == $layout_type || 'single_product' == $layout_type ) {
				ob_start();
				$this->is_doing = 'product_cat';
			}
		}
	}

	public function product_loop_after_cat( $product_type ) {
		if ( 'product_cat' == $this->is_doing ) {
			$layout_type = wolmart_get_page_layout();
			if ( 'archive_product' == $layout_type || 'single_product' == $layout_type ) {
				echo '<script type="text/template">' . json_encode( ob_get_clean() ) . '</script>';
				echo '<div class="skel-cat"></div>';
				$this->is_doing = '';
			}
		}
	}

	public function post_loop_wrapper_class( $classes ) {
		if ( ! $this->is_doing ) {
			$layout_type = wolmart_get_page_layout();
			if ( 'archive_post' == $layout_type || 'single_post' == $layout_type ) {
				$classes[] = 'skeleton-body';
			}
		}
		return $classes;
	}

	public function post_loop_before_item() {
		if ( ! $this->is_doing ) {
			$layout_type = wolmart_get_page_layout();
			if ( 'archive_post' == $layout_type || 'single_post' == $layout_type ) {
				ob_start();
				$this->is_doing = 'post';
			}
		}
	}

	public function post_loop_after_item( $type ) {
		if ( 'post' == $this->is_doing ) {
			$layout_type = wolmart_get_page_layout();
			if ( 'archive_post' == $layout_type || 'single_post' == $layout_type ) {
				echo '<script type="text/template">' . json_encode( ob_get_clean() ) . '</script>';
				$class = 'skel-post';
				if ( 'list' == $type ) {
					$class .= '-list';
				} elseif ( 'mask' == $type ) {
					$class .= '-mask';
				}
				echo '<div class="' . wolmart_escaped( $class ) . '"></div>';
				$this->is_doing = '';
			}
		}
	}

	public function single_product_classes( $classes ) {
		if ( ! $this->is_doing ) {
			$classes[] = 'skeleton-body';
		}
		return $classes;
	}

	public function before_product_gallery() {
		if ( ! $this->is_doing ) {
			ob_start();
			$this->is_doing = 'product_gallery';
		}
	}

	public function after_product_gallery() {
		if ( 'product_gallery' == $this->is_doing ) {
			echo '<script type="text/template">' . json_encode( ob_get_clean() ) . '</script>';
			echo '<div class="skel-pro-gallery"></div>';
			$this->is_doing = '';
		}
	}

	public function before_product_summary() {
		if ( ! $this->is_doing ) {
			ob_start();
			$this->is_doing = 'product_summary';
		}
	}

	public function after_product_summary() {
		if ( 'product_summary' == $this->is_doing ) {
			echo '<script type="text/template">' . json_encode( ob_get_clean() ) . '</script>';
			echo '<div class="skel-pro-summary"></div>';
			$this->is_doing = '';
		}
	}

	public function before_product_tabs() {
		if ( ! $this->is_doing ) {
			ob_start();
			$this->is_doing = 'product_tabs';
		}
	}

	public function after_product_tabs() {
		if ( 'product_tabs' == $this->is_doing ) {
			echo '<script type="text/template">' . json_encode( ob_get_clean() ) . '</script>';
			echo '<div class="skel-pro-tabs"></div>';
			$this->is_doing = '';
		}
	}

	public function menu_skeleton( $content, $megamenu_width, $megamenu_pos ) {
		if ( ! $this->is_doing && wolmart_get_option( 'lazyload_menu' ) ) {
			if ( $megamenu_width ) {
				return '<ul class="megamenu mp-' . $megamenu_pos . ' skel-megamenu" style="width: ' . $megamenu_width . 'px; ' . ( 'center' == $megamenu_pos ? 'left: calc( 50% - ' . $megamenu_width / 2 . 'px );' : '' ) . '">';
			} else {
				return '<ul class="submenu skel-menu">';
			}
		}
		return $content;
	}

	static public function prevent_skeleton() {
		Wolmart_Skeleton::get_instance()->is_doing = 'stop';
	}

	static public function stop_prevent_skeleton() {
		Wolmart_Skeleton::get_instance()->is_doing = '';
	}
}

Wolmart_Skeleton::get_instance();
