<?php
defined( 'ABSPATH' ) || die;

/**
 * Class Wolmart_Walker_Nav_Menu
 */
if ( ! class_exists( 'Wolmart_Walker_Nav_Menu' ) ) {
	class Wolmart_Walker_Nav_Menu extends Walker_Nav_Menu {

		public $menu_type;
		public $megamenu;
		public $megamenu_width;
		public $megamenu_pos;

		public function __construct( $menu_type = '' ) {
			$this->menu_type = $menu_type;
		}

		/**
		 * Starts the list before the elements are added.
		 *
		 * @param    string    $output    Used to append additional content (passed by reference).
		 * @param    int       $depth     Depth of the item.
		 * @param    array     $args      An array of additional arguments.
		 */
		public function start_lvl( &$output, $depth = 0, $args = array() ) {
			$indent  = str_repeat( "\t", $depth );
			$content = '';

			if ( $this->megamenu ) {
				// The first child is not shown and is used for only columns.
				if ( 1 == $depth ) {
					return;
				} elseif ( 2 == $depth ) {
					$content .= "\n" . $indent . '<ul>' . "\n";
				} else {
					$content .= "\n" . $indent . '<ul class="megamenu mp-' . $this->megamenu_pos . '" style="width: ' . $this->megamenu_width . 'px; ' . ( 'center' == $this->megamenu_pos ? 'left: calc( 50% - ' . $this->megamenu_width / 2 . 'px );' : '' ) . '">' . "\n";
				}
				$output .= apply_filters( 'wolmart_menu_lazyload_content', $content, $this->megamenu ? $this->megamenu_width : false, $this->megamenu ? $this->megamenu_pos : false );
			} else {
				if ( isset( $args->lazy ) && $args->lazy ) {
					$content  = '<ul class="d-loading">';
					$content .= '<li><i></i></li>';
					$content  = apply_filters( 'wolmart_menu_lazyload_content', $content, false, false );
				} else {
					$content = '<ul>';
				}
				$output .= "\n" . $indent . $content . "\n";
			}
		}

		/**
		 * Ends the list of after the elements are added.
		 *
		 * @param    string    $output    Used to append additional content (passed by reference).
		 * @param    int       $depth     Depth of the item.
		 * @param    array     $args      An array of additional arguments.
		 */
		public function end_lvl( &$output, $depth = 0, $args = array() ) {
			$indent = str_repeat( "\t", $depth );

			if ( $this->megamenu ) {

				if ( 1 == $depth ) {
					return;
				} else {
					$output .= "\n" . $indent . '</ul>' . "\n";
				}
			} else {
				$output .= "\n" . $indent . '</ul>' . "\n";
			}
		}

		/**
		 * Starts the element output.
		 *
		 * @param    string      $output    Used to append additional content (passed by reference).
		 * @param    WP_Post     $item      Menu item data object.
		 * @param    int         $depth     Depth of menu item. Used for padding.
		 * @param    stdClass    $args      An object of wp_nav_menu() arguments.
		 * @param    int         $id        Current item ID.
		 */
		public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
			$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

			if ( is_array( $args ) ) {
				$args = (object) $args;
			}

			if ( 0 == $depth ) {
				$this->megamenu = (bool) get_post_meta( $item->ID, '_menu_item_megamenu', true );
			}

			if ( $this->megamenu ) {
				$this->megamenu_width = (int) get_post_meta( $item->ID, '_menu_item_megamenu_width', true );
				$this->megamenu_pos   = get_post_meta( $item->ID, '_menu_item_megamenu_pos', true );

				if ( ! $this->megamenu_width ) {
					$this->megamenu_width = 618;
				}
				if ( ! $this->megamenu_pos ) {
					$this->megamenu_pos = 'left';
				}
			}

			if ( isset( $args->lazy ) && $args->lazy ) {
				if ( ! $this->megamenu && 1 == $depth ) {
					return;
				}
			}

			$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
			$classes[] = 'menu-item-' . $item->ID;

			/**
			 * Filters the arguments for a single nav menu item.
			 *
			 * @since 4.4.0
			 *
			 * @param stdClass $args  An object of wp_nav_menu() arguments.
			 * @param WP_Post  $item  Menu item data object.
			 * @param int      $depth Depth of menu item. Used for padding.
			 */
			$args = apply_filters( 'nav_menu_item_args', $args, $item, $depth );

			$lazyload_menu_enabled = ! wp_doing_ajax() &&
			! is_customize_preview() &&
			! ( function_exists( 'wolmart_is_elementor_preview' ) &&
				wolmart_is_elementor_preview()
			) && wolmart_get_option( 'lazyload_menu' );

			/**
			 *  Leave menu-item-has-children class for Lazyload menu 
			 * 
			 *  @since Wolmart Theme 1.7.4
			 */
			if ( $lazyload_menu_enabled && isset( $args->depth ) ) {
				$args->depth = 0;
			}
			/**
			 * Filters the CSS classes applied to a menu item's list item element.
			 *
			 * @since 3.0.0
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param string[] $classes Array of the CSS classes that are applied to the menu item's `<li>` element.
			 * @param WP_Post  $item    The current menu item.
			 * @param stdClass $args    An object of wp_nav_menu() arguments.
			 * @param int      $depth   Depth of menu item. Used for padding.
			 */

			$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth ) );
			if ( $lazyload_menu_enabled && isset( $args->depth ) ) {
				$args->depth = 1;
			}

			$class_attr  = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

			/**
			 * Filters the ID applied to a menu item's list item element.
			 *
			 * @since 3.0.1
			 * @since 4.1.0 The `$depth` parameter was added.
			 *
			 * @param string   $menu_id The ID that is applied to the menu item's `<li>` element.
			 * @param WP_Post  $item    The current menu item.
			 * @param stdClass $args    An object of wp_nav_menu() arguments.
			 * @param int      $depth   Depth of menu item. Used for padding.
			 */
			$id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );
			$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

			$li_style = get_post_meta( $item->ID, '_menu_item_image', true );
			if ( $li_style ) {
				$li_style = 'style="background-image: url(' . esc_url( $li_style ) . ');"';
			}

			if ( ! $this->megamenu || 2 != $depth ) {
				$output .= $indent . '<li' . $id . $class_attr . ' ' . $li_style . '>';
			}

			if ( $li_style ) {
				return;
			}

			$li_block = get_post_meta( $item->ID, '_menu_item_block', true );
			if ( $li_block && defined( 'WOLMART_CORE_PATH' ) ) {
				if ( wolmart_doing_ajax() && isset( $_POST['load_mobile_menu'] ) ) {
					return;
				}

				ob_start();
				wolmart_print_template( $li_block );
				$output .= ob_get_clean();
				return;
			}

			if ( ! $this->megamenu || 1 != $depth ) {
				$atts           = array();
				$atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
				$atts['target'] = ! empty( $item->target ) ? $item->target : '';
				if ( '_blank' == $item->target && empty( $item->xfn ) ) {
					$atts['rel'] = 'noopener noreferrer';
				} else {
					$atts['rel'] = $item->xfn;
				}
				$item->nolink       = get_post_meta( $item->ID, '_menu_item_nolink', true );
				$atts['href']       = 'nolink' != $item->nolink ? ( ! empty( $item->url ) ? $item->url : '' ) : '#';
				$atts['class']      = $item->nolink;
				$menu_item_nofollow = wolmart_get_option( 'menu_item_nofollow' );
				if ( ! empty( $menu_item_nofollow ) ) {
					$atts['rel'] = 'nofollow';
				}

				if ( 2 == $depth ) {
					$atts['class'] .= ' ' . $class_names;
				}

				/**
				 * Filters the HTML attributes applied to a menu item's anchor element.
				 *
				 * @since 3.6.0
				 * @since 4.1.0 The `$depth` parameter was added.
				 *
				 * @param array $atts {
				 *     The HTML attributes applied to the menu item's `<a>` element, empty strings are ignored.
				 *
				 *     @type string $title        Title attribute.
				 *     @type string $target       Target attribute.
				 *     @type string $rel          The rel attribute.
				 *     @type string $href         The href attribute.
				 *     @type string $aria_current The aria-current attribute.
				 * }
				 * @param WP_Post  $item  The current menu item.
				 * @param stdClass $args  An object of wp_nav_menu() arguments.
				 * @param int      $depth Depth of menu item. Used for padding.
				 */
				$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

				$attributes = '';
				foreach ( $atts as $attr => $value ) {
					if ( is_scalar( $value ) && '' !== $value && false !== $value ) {
						$value       = ( 'href' == $attr ) ? esc_url( $value ) : esc_attr( $value );
						$attributes .= ' ' . $attr . '="' . $value . '"';
					}
				}

				/** This filter is documented in wp-includes/post-template.php */
				$title = apply_filters( 'the_title', $item->title, $item->ID );
				/**
				 * Filters a menu item's title.
				 *
				 * @since 4.4.0
				 *
				 * @param string   $title The menu item's title.
				 * @param WP_Post  $item  The current menu item.
				 * @param stdClass $args  An object of wp_nav_menu() arguments.
				 * @param int      $depth Depth of menu item. Used for padding.
				 */
				$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

				$item->icon = get_post_meta( $item->ID, '_menu_item_icon', true );
				if ( $item->icon ) {
					$args->link_before = '<i class="' . esc_attr( $item->icon ) . '"></i>';
				} else {
					$args->link_before = '';
				}

				$item_output      = $args->before;
				$item_output     .= '<a' . $attributes . '>';
				$item_output     .= $args->link_before . $title . $args->link_after;
				$item->label_name = esc_html( get_post_meta( $item->ID, '_menu_item_label_name', true ) );
				if ( $item->label_name ) {
					$labels = json_decode( wolmart_get_option( 'menu_labels' ), true );
					if ( $labels && $item->label_name && isset( $labels[ $item->label_name ] ) ) {
						$item_output .= '<span class="tip" style="background-color: ' . $labels[ $item->label_name ] . '">' . $item->label_name . '</span>';
					}
				}
				$item_output .= '</a>';
				$item_output .= $args->after;
			}

			/**
			 * Filters a menu item's starting output.
			 *
			 * The menu item's starting output only includes `$args->before`, the opening `<a>`,
			 * the menu item's title, the closing `</a>`, and `$args->after`. Currently, there is
			 * no filter for modifying the opening and closing `<li>` for a menu item.
			 *
			 * @since 3.0.0
			 *
			 * @param string   $item_output The menu item's starting HTML output.
			 * @param WP_Post  $item        Menu item data object.
			 * @param int      $depth       Depth of menu item. Used for padding.
			 * @param stdClass $args        An object of wp_nav_menu() arguments.
			 */
			$output .= apply_filters( 'walker_nav_menu_start_el', isset( $item_output ) ? $item_output : '', $item, $depth, $args );
		}

		/**
		 * Ends the element output.
		 *
		 * @param    string    $output    Used to append additional content (passed by reference).
		 * @param    object    $object    The data object.
		 * @param    int       $depth     Depth of the item.
		 * @param    array     $args      An array of additional arguments.
		 */
		public function end_el( &$output, $item, $depth = 0, $args = array() ) {
			if ( isset( $args->lazy ) && $args->lazy ) {
				if ( 1 <= $depth ) {
					return;
				}
			}

			if ( $this->megamenu && 2 == $depth ) {
				return;
			}

			if ( $this->has_children ) {
				if ( isset( $args->lazy ) && $args->lazy && 0 == $depth && ! $this->megamenu ) {
					$content  = '<ul class="d-loading">';
					$content .= '<li><i></i></li>';
					$output  .= apply_filters( 'wolmart_menu_lazyload_content', $content, $this->megamenu ? $this->megamenu_width : false, $this->megamenu ? $this->megamenu_pos : false ) . '</ul>';
				}
			}

			$output .= '</li>' . "\n";
		}
	}
}
