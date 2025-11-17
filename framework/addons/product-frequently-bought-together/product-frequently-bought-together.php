<?php
/**
 * Wolmart Product Frequently Bought Together class
 *
 * @version 1.1
 */
defined( 'ABSPATH' ) || die;

if ( ! class_exists( 'Wolmart_Product_Frequently_Bought_Together' ) ) {

	/**
	 * Wolmart Product Frequently Bought Together Class
	 */
	class Wolmart_Product_Frequently_Bought_Together extends Wolmart_Base {

		public $coupon_code   = '';
		public $discount_data = array();

		/**
		 * Main Class construct
		 *
		 * @since 1.0
		 */
		public function __construct() {
			add_action( 'wp', array( $this, 'init' ) );
			add_action( 'wp_loaded', array( $this, 'add_to_cart_action' ), 20 );

			// Discount actions
			add_action( 'wp_loaded', array( $this, 'set_discount_data' ), 21 );
			add_filter( 'woocommerce_coupon_is_valid', array( $this, 'set_coupon_id' ), 9, 2 );
			add_filter( 'woocommerce_get_shop_coupon_data', array( $this, 'add_coupon_data' ), 10, 3 );
			add_action( 'wolmart_fbt_add_to_cart_discount', array( $this, 'save_discount_info' ), 10, 2 );
			add_action( 'wolmart_fbt_add_to_cart_discount', array( $this, 'add_fbt_coupon' ), 99 );
			add_action( 'woocommerce_before_thankyou', array( $this, 'remove_fbt_data' ) );
			add_action( 'woocommerce_removed_coupon', array( $this, 'remove_fbt_data' ) );
			add_action( 'wolmart_fbt_mini_cart_coupon_html', array( $this, 'mini_cart_coupon_html' ) );
			add_action( 'wolmart_fbt_no_coupon_html', array( $this, 'remove_fbt_data' ) );
			add_action( 'woocommerce_before_calculate_totals', array( $this, 'check_cart_products' ), 99 );
			add_filter( 'woocommerce_coupon_error', array( $this, 'remove_coupon_msg' ), 10, 3 );
		}

		/**
		 * Set Custom coupon valid for Multi Vendor plugins
		 *
		 * @param {bool} $valid coupon code is valid or not
		 * @param {object} coupon object
		 * @return bool
		 */
		public function set_coupon_id( $valid, $coupon ) {
			if ( 'frequently-bought-together' == $coupon->get_code() && $this->discount_data ) {
				$id = isset( $this->discount_data['main_product_id'] ) ? $this->discount_data['main_product_id'] : 0;
				$coupon->set_id( absint( $id ) );
			}

			return $valid;
		}

		/**
		 * Remove coupon error message
		 *
		 * @since 1.1
		 * @param string $msg error message
		 * @param string $msg_code message code
		 * @param object $object coupon object
		 * @return message|null
		 */
		public function remove_coupon_msg( $msg, $msg_code, $object ) {
			if ( isset( $object ) && 'frequently-bought-together' == $object->get_code() ) {
				return '';
			}

			return $msg;
		}

		/**
		 * Check frequently bought together products stored in cart
		 *
		 * @since 1.1
		 * @param object $cart_object
		 * @return void
		 */
		public function check_cart_products( $cart_object ) {
			if ( ! $this->discount_data || ! is_array( $this->discount_data ) ) {
				return;
			}

			$cart_content     = $cart_object->get_cart_contents();
			$cart_product_ids = array();
			foreach ( $cart_content as $cart_key => $cart_value ) {
				$qty = $cart_value['quantity'];
				do {
					$id = $cart_value['variation_id'] ? $cart_value['variation_id'] : $cart_value['product_id'];
					if ( in_array( $id, $cart_product_ids ) ) {
						$qty -= 1;
						continue;
					} else {
						$cart_product_ids[] = $id;
					}
					$qty -= 1;
				} while ( $qty > 0 );
			}

			if ( isset( $this->discount_data['fbt_products'] ) && isset( $this->discount_data['fbt_metas'] ) && $this->discount_data['fbt_metas'] ) {
				$fbt_metas        = $this->discount_data['fbt_metas'];
				$fbt_products     = $this->discount_data['fbt_products'];
				$fbt_products_ids = array();
				$fbt_keys         = array();
				$subtotal         = 0;
				foreach ( $fbt_products as $fbt_key => $fbt_value ) {
					$fbt_products_ids[] = $fbt_value;
					$fbt_keys[]         = $fbt_key;
				}

				foreach ( $fbt_keys as $key ) {
					if ( ! isset( $cart_content[ $key ] ) ) {
						continue;
					}

					$item_price = $cart_content[ $key ]['line_subtotal'];
					if ( wc_prices_include_tax() ) {
						$item_price += $cart_content[ $key ]['line_subtotal_tax'];
					}

					$item_each = $item_price / $cart_content[ $key ]['quantity'];
					$subtotal += $item_each;
				}

				if ( $cart_product_ids && $fbt_products_ids ) {
					$real_fbt_products = array_intersect( $cart_product_ids, $fbt_products_ids );
					$fbt_product_count = count( $real_fbt_products );
				}

				if ( $subtotal && $fbt_product_count && ( $subtotal < $fbt_metas['fbt_discount_spend'] || $fbt_product_count < $fbt_metas['fbt_discount_products_count'] ) ) {
					WC()->cart->remove_coupon( 'frequently-bought-together' );
					return;
				} else {
					$this->add_fbt_coupon();
					$this->save_data_session();
				}
			}
		}

		/**
		 * Create frequently bought together coupon html on mini-cart box
		 *
		 * @since 1.1
		 */
		public function mini_cart_coupon_html() {
			$coupon_data = WC()->session->get( 'wolmart_fbt_discount_data', array() );
			if ( isset( $coupon_data['discount_amount'] ) ) {
				$coupon_amount = floatval( $coupon_data['discount_amount'] );
				//get cart total value from woocommerce cart
				$subtotal_value = strip_tags( str_replace( get_woocommerce_currency_symbol(), '', WC()->cart->get_cart_subtotal() ) );
				if ( ',' == get_option( 'woocommerce_price_decimal_sep' ) ) {
					if ( '.' == get_option( 'woocommerce_price_thousand_sep' ) ) {
						$subtotal_value = str_replace( '.', '', $subtotal_value );
					}
					$subtotal_value = (float) str_replace( ',', '.', $subtotal_value );
				} else {
					$subtotal_value = (float) str_replace( ',', '', $subtotal_value );
				}
				$real_total = $subtotal_value > $coupon_amount ? ( $subtotal_value - $coupon_amount ) : $subtotal_value;
				add_filter(
					'woocommerce_cart_subtotal',
					function( $cart_subtotal ) use ( $real_total ) {
						return wc_price( $real_total );
					}
				);
				if ( ',' == get_option( 'woocommerce_price_decimal_sep' ) ) {
					$coupon_amount = number_format( $coupon_data['discount_amount'], 2, ',', get_option( 'woocommerce_price_thousand_sep' ) );
				}
				?>
				<p class="woocommerce-mini-cart__total total">
					<strong><?php echo esc_html__( 'Discount:', 'wolmart' ); ?></strong>
					<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">-<?php echo get_woocommerce_currency_symbol(); ?></span><?php echo esc_html( wolmart_strip_script_tags( $coupon_amount ) ); ?></bdi></span>
				</p>
				<?php
			}
		}

		/**
		 * Remove the session data
		 *
		 * @since 1.1.0
		 */
		public function remove_fbt_data() {
			WC()->session->set( 'wolmart_fbt_discount_data', array() );
		}

		/**
		 * Return discount or coupon html
		 *
		 * @since 1.1
		 * @param $coupon_html
		 * @param object $coupon
		 * @param $discount_amount_html
		 * @return html
		 */
		public function total_coupon_html( $coupon_html, $coupon, $discount_amount_html ) {
			if ( 'frequently-bought-together' === $coupon->get_code() ) {
				return $discount_amount_html;
			}

			return $coupon_html;
		}

		/**
		 * Add coupon
		 *
		 * @since 1.1.0
		 * @param array $data coupon data
		 * @param string $coupon_code coupon code
		 * @return array
		 */

		public function add_coupon_data( $data, $coupon_code, $coupon ) {
			if ( ! 'frequently-bought-together' == $coupon_code ) {
				return;
			}

			$fbt_product_ids = array();
			$amount          = 0;

			if ( $this->discount_data ) {
				foreach ( $this->discount_data as $key => $value ) {
					if ( 'discount_amount' == $key ) {
						$amount += $value;
					} elseif ( 'fbt_products' == $key ) {
						foreach ( $this->discount_data['fbt_products'] as $val ) {
							$fbt_product_ids[] = $val;
						}
					} else {
						continue;
					}
				}
			}

			if ( $amount ) {
				$data = array(
					'code'           => $coupon_code,
					'amount'         => $amount,
					'discount_type'  => 'fixed_cart',
					'usage_limit'    => 1,
					'individual_use' => true,
					'product_ids'    => $fbt_product_ids,
				);
			}

			return $data;
		}

		/**
		 * Init function of this class
		 *
		 * @since 1.0
		 */
		public function init() {
			if ( wolmart_is_product() ) {
				add_action( 'woocommerce_after_single_product_summary', array( $this, 'wolmart_fbt_product' ), 5 );
			}
		}

		/**
		 * Display the frequently bought together section on page
		 *
		 * @since 1.0
		 */
		public function wolmart_fbt_product() {

			global $product;

			$product_id      = $product->get_id();
			$main_product_id = $product_id;
			$fbt_metas       = get_post_meta( $product_id, 'wolmart_fbt_metas', true );
			if ( empty( $fbt_metas ) ) {
				return;
			}

			$discount_enable    = isset( $fbt_metas['fbt_discount_enable'] ) && 'yes' == $fbt_metas['fbt_discount_enable'];
			$discount_condition = isset( $fbt_metas['fbt_discount_condition'] ) && 'yes' == $fbt_metas['fbt_discount_condition'];
			$product_ids        = isset( $fbt_metas['fbt_products'] ) ? $fbt_metas['fbt_products'] : '';

			if ( empty( $product_ids ) ) {
				return;
			}

			if ( is_array( $product_ids ) ) {
				$product_ids = array_merge( array( $product_id ), $product_ids );
			}
			$product_ids = apply_filters( 'wolmart_fbt_product_ids', $product_ids, $product );

			if ( empty( $product_ids ) || ! is_array( $product_ids ) ) {
				return;
			}

			$total_price = 0;
			$class       = 'product-fbt';
			$tab_type    = apply_filters( 'wolmart_single_product_data_tab_type', 'tab' );
			if ( 'section' == $tab_type ) {
				$class .= ' tab-section';
			}

			// Enqueue scripts and localize vars
			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '.js' : '.min.js';
			wp_enqueue_script( 'wolmart-product-frequently-bought-together-js', WOLMART_ADDONS_URI . '/product-frequently-bought-together/frequently-bought-together' . $suffix, array( 'wolmart-theme-async' ), WOLMART_VERSION, true );
			wp_localize_script(
				'wolmart-product-frequently-bought-together-js',
				'wolmart_fbt_vars',
				apply_filters(
					'wolmart_fbt_vars',
					$fbt_metas
				)
			);

			?>

			<div class="<?php echo esc_attr( $class ); ?>">
				<h2 class="title-wrapper<?php echo 'section' == $tab_type ? ' title-underline' : ' title-underline2'; ?>">
					<span class="title"><?php echo esc_html( apply_filters( 'wolmart_single_product_fbt_title', wolmart_get_option( 'product_fbt_title' ) ) ); ?></span>
				</h2>
				<ul class="products row">
					<?php
					$count         = 0;
					$available_ids = array();
					foreach ( $product_ids as $product_id ) {
						$add_class  = '';
						$product_id = apply_filters( 'wolmart_frequently_bought_together_product', $product_id, 'product' );
						$item       = wc_get_product( $product_id );

						if ( empty( $item ) ) {
							continue;
						}

						// remove variable product, remove out of stock product
						if ( $item->is_type( 'variable' ) || 'outofstock' == $item->get_stock_status() ) {
							$add_class = ' current-product disabled';
						} else {
							++ $count;
							$available_ids[] = $product_id;
						}

						// get parent product for variable product
						$data_id = $item->get_id();
						if ( ! $add_class ) {
							$price = wc_get_price_to_display( $item );
							if ( $price ) {
								$total_price += $price;
							}
						}
						$product_name = $item->get_title() . $this->fbt_product_variation( $item );
						?>

						<li class="product product-wrap<?php echo esc_attr( $add_class ); ?>" data-id="<?php echo esc_attr( $data_id ); ?>" class="wolmart_fbt_product-<?php echo esc_attr( $data_id ); ?>">
							<a class="product-media" href="<?php echo esc_url( $item->get_permalink() ); ?>" aria-label="<?php esc_attr_e( 'Product Image', 'wolmart' ); ?>">
								<?php
								$image_id = $item->get_image_id();
								if ( $image_id ) {
									$image = wp_get_attachment_image_src( $image_id, 'thumbnail' );
									if ( is_array( $image ) ) {
										echo wp_get_attachment_image( $image_id, 'thumbnail' );
									} else {
										echo wc_placeholder_img( 'thumbnail' );
									}
								} else {
									echo wc_placeholder_img( 'thumbnail' );
								}
								?>
							</a>
							<div class="custom-input">
								<input type="checkbox" data-id="<?php echo esc_attr( $data_id ); ?>" class="wolmart_fbt_item_<?php echo esc_attr( $product_id ); ?>" data-price="<?php echo esc_attr( $item->get_price() ); ?>"<?php echo ( ! $add_class ? ' checked' : '' ); ?>>								
							</div>
							<div class="product-details">
								<h5 class="woocommerce-loop-product__title">
									<a href="<?php echo esc_url( $item->get_permalink() ); ?>">
										<?php echo esc_html( $product_name ); ?>
									</a>
								</h5>
								<div class="price">
									<?php echo wolmart_strip_script_tags( $item->get_price_html(), '<ins>' ); ?>
								</div>
							</div>
						</li>
						<?php
					}
					$available_ids = implode( ',', $available_ids );

					if ( $discount_enable ) {
						$old_price     = $total_price;
						$discount_type = isset( $fbt_metas['fbt_discount_type'] ) ? $fbt_metas['fbt_discount_type'] : '';
						if ( 'fixed' == $discount_type ) {
							$discount_fixed = isset( $fbt_metas['fbt_discount_fixed'] ) ? intval( $fbt_metas['fbt_discount_fixed'] ) : 0;
							$temp_price     = $old_price - $discount_fixed;
						} elseif ( 'percent' == $discount_type ) {
							$discount_percent = isset( $fbt_metas['fbt_discount_percentage'] ) ? intval( $fbt_metas['fbt_discount_percentage'] ) : 0;
							$temp_price       = $old_price - ( $old_price * $discount_percent / 100 );
						}
						$discount_spend = isset( $fbt_metas['fbt_discount_spend'] ) ? intval( $fbt_metas['fbt_discount_spend'] ) : 0;
						$dicount_count  = isset( $fbt_metas['fbt_discount_products_count'] ) ? intval( $fbt_metas['fbt_discount_products_count'] ) : 2;
					}
					?>

					<li class="product product-buttons">
						<div class="price-box">
							<?php
							if ( $discount_enable ) :
								if ( $discount_condition ) :
									if ( $old_price > $discount_spend && count( $product_ids ) >= $dicount_count ) :
										$total_price = $temp_price;
										?>
									<span class="s-price wolmart_old_price"><?php echo wc_price( $old_price ); ?></span>
									<input type="hidden" data-price="<?php echo esc_attr( $old_price ); ?>" class="wolmart-data-oldprice">

										<?php
									endif;
								else :
									$total_price = $temp_price;
									?>
									<span class="s-price wolmart_old_price"><?php echo wc_price( $old_price ); ?></span>
									<input type="hidden" data-price="<?php echo esc_attr( $old_price ); ?>" class="wolmart-data-oldprice">
									<?php
								endif;
							endif;
							?>
							<span class="s-price wolmart_total_price"><?php echo wc_price( $total_price ); ?></span>
							<input type="hidden" data-price="<?php echo esc_attr( $total_price ); ?>" class="wolmart-data-price">
						</div>
						<label class="bought-count"><?php printf( esc_html__( 'For %1$s %2$s', 'wolmart' ), '<span>' . $count . '</span>', ( $count > 1 ? esc_html__( 'items', 'wolmart' ) : esc_html__( 'item', 'wolmart' ) ) ); ?></label>
						<form class="fbt_cart" action="<?php echo esc_url( $product->get_permalink() ); ?>" method="post" enctype="multipart/form-data">
							<button type="submit" name="wolmart_add_cart" value="<?php echo esc_attr( $available_ids ); ?>"
								class="btn btn-dark mt-5 wolmart_add_to_cart_button ajax_add_to_cart">
								<?php esc_html_e( 'Add All To Cart', 'wolmart' ); ?></button>
							<input type="hidden" name="wolmart_fbt_main_product" value="<?php echo esc_attr( $main_product_id ); ?>" >
						</form>
					</li>
				</ul>
			</div>
			<?php
		}

		/**
		 * Get variation name.
		 *
		 * @since 1.1.0
		 * @param {object} $product
		 * @access private
		 */
		private function fbt_product_variation( $product ) {
			if ( ! $product->is_type( 'variation' ) ) {
				return;
			}

			$attributes = $product->get_variation_attributes();
			$variations = array();

			foreach ( $attributes as $key => $attribute ) {
				$key   = str_replace( 'attribute_', '', $key );
				$terms = get_terms(
					array(
						'taxonomy'   => sanitize_title( $key ),
						'menu-order' => 'ASC',
						'hide_empty' => false,
					)
				);

				foreach ( $terms as $term ) {
					if ( is_object( $term ) && $term->slug === $attribute ) {
						$variations[] = $term->name;
					}
				}
			}

			if ( ! empty( $variations ) ) {
				return ' &ndash; ' . implode( ',', $variations );
			}
			return '';
		}

		/**
		 * Add frequently bought products to cart
		 *
		 * @since 1.0
		 */
		public function add_to_cart_action() {
			if ( ! ( isset( $_REQUEST['wolmart_add_cart'] ) ) && empty( $_REQUEST['wolmart_add_cart'] ) ) {
				return;
			}

			wc_nocache_headers();

			$product_ids   = $_REQUEST['wolmart_add_cart'];
			$product_ids   = explode( ',', $product_ids );
			$main_id       = isset( $_POST['wolmart_fbt_main_product'] ) ? intval( $_POST['wolmart_fbt_main_product'] ) : $product_ids[0];
			$cart_products = [];

			if ( ! is_array( $product_ids ) ) {
				return;
			}

			foreach ( $product_ids as $product_id ) {
				$was_added_to_cart = false;
				$variation_id      = '';
				$variation_attr    = array();

				if ( 'product_variation' === get_post_type( $product_id ) ) {
					$variation_id      = $product_id;
					$variation_product = wc_get_product( $variation_id );
					$variation_attr    = $variation_product->get_variation_attributes();
					$product_id        = wp_get_post_parent_id( $variation_id );
				}

				$product = wc_get_product( $product_id );

				if ( ! $product ) {
					return;
				}

				$quantity          = 1;
				$passed_validation = apply_filters( 'woocommerce_add_to_cart_validation', true, $product_id, $quantity, $variation_id );

				$cart_key = WC()->cart->add_to_cart( $product_id, $quantity, $variation_id, $variation_attr );

				if ( $passed_validation && $cart_key ) {
					$cart_products[ $cart_key ] = $variation_id ? $variation_id : $product_id;
					wc_add_to_cart_message( array( $product_id => $quantity ), true );
					$was_added_to_cart = true;
				}
			}

			do_action( 'wolmart_fbt_add_to_cart_discount', $main_id, $cart_products );

			if ( 'yes' === get_option( 'woocommerce_cart_redirect_after_add' ) ) {
				$url = function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : WC()->cart->get_url();
			} else {
				$url = remove_query_arg( array( 'action', '_wpnonce' ) );
			}

			wp_redirect( esc_url( $url ) );
			exit;
		}

		/**
		 * Set discount data when wp is fully loaded
		 *
		 * @since 1.1.0
		 */
		public function set_discount_data() {

			if ( is_null( WC()->session ) ) {
				return;
			}

			if ( empty( $this->discount_data ) ) {
				$this->discount_data = WC()->session->get( 'wolmart_fbt_discount_data', array() );
			}
		}

		/**
		 * Set discount data if the discount is enabled
		 *
		 * @since 1.1.0
		 * @param string $main_id the main product
		 * @param array $fbt_products frequently boughted products(cart-key and id) inclusive of main product
		 *
		*/
		public function save_discount_info( $main_id, $fbt_products ) {
			if ( class_exists( 'Woocommerce' ) ) {
				$main_product = wc_get_product( intval( $main_id ) );
			}

			$product_count = intval( count( $fbt_products ) );
			$fbt_data      = maybe_unserialize( get_post_meta( $main_id, 'wolmart_fbt_metas', true ) );
			$subtotal      = 0;
			$discount      = 0;
			$discount_flag = false;

			if ( empty( $fbt_data ) || 'no' == $fbt_data['fbt_discount_enable'] ) {
				return;
			}

			if ( ! empty( $fbt_data['fbt_discount_type'] ) ) {
				if ( ! floatval( $fbt_data['fbt_discount_fixed'] ) && ! floatval( $fbt_data['fbt_discount_percentage'] ) ) {
					return;
				} else {
					$discount_amount = ( 'fixed' == $fbt_data['fbt_discount_type'] ) ? floatval( $fbt_data['fbt_discount_fixed'] ) : intval( $fbt_data['fbt_discount_percentage'] );

					if ( ! $subtotal ) {
						$cart_data = WC()->cart->get_cart_contents();
						foreach ( $fbt_products as $key => $id ) {
							if ( ! isset( $cart_data[ $key ] ) ) {
								continue;
							}

							$item_price = $cart_data[ $key ]['line_subtotal'];
							if ( wc_prices_include_tax() ) {
								$item_price += $cart_data[ $key ]['line_subtotal_tax'];
							}

							$item_each = $item_price / $cart_data[ $key ]['quantity'];
							$subtotal += $item_each;
						}
					}

					if ( 'yes' == $fbt_data['fbt_discount_condition'] ) {
						if ( ! floatval( $fbt_data['fbt_discount_spend'] ) || $subtotal < floatval( $fbt_data['fbt_discount_spend'] ) || intval( $fbt_data['fbt_discount_products_count'] ) < 2 || $product_count < intval( $fbt_data['fbt_discount_products_count'] ) ) {
							return;
						}
					}

					if ( $subtotal ) {
						if ( 'fixed' == $fbt_data['fbt_discount_type'] ) {
							$discount = ( $subtotal < $discount_amount ) ? $subtotal : $discount_amount;
						} else {
							$discount = $subtotal * ( $discount_amount / 100 );
						}
					}

					if ( $discount ) {
						$this->discount_data = array(
							'main_product_id' => $main_id,
							'fbt_products'    => $fbt_products,
							'discount_amount' => $discount,
							'fbt_metas'       => $fbt_data,
						);
						$discount_flag       = true;
					}

					if ( $discount_flag ) {
						$this->save_data_session();
					} else {
						$this->remove_fbt_data();
					}
				}
			}
		}

		/**
		 * Save data to Woocommerce session
		 *
		 * @since 1.1.0
		 * @param null|array $data data to save to session
		 */

		public function save_data_session( $data = null ) {
			if ( WC()->session ) {
				if ( is_null( $data ) ) {
					$data = $this->discount_data;
				}
				WC()->session->set( 'wolmart_fbt_discount_data', $data );
			}
		}

		/**
		 * Create coupon code and add to WC coupon group
		 *
		 * @since 1.1.0
		 */
		public function add_fbt_coupon() {
			if ( apply_filters( 'wolmart_add_fbt_coupon', true, $this ) && ! empty( $this->discount_data ) ) {
				if ( empty( $this->coupon_code ) ) {
					$this->coupon_code = apply_filters( 'wolmart_fbt_coupon_code', 'frequently-bought-together' );
				}
				if ( $this->coupon_code && ! WC()->cart->has_discount( $this->coupon_code ) ) {
					WC()->cart->add_discount( $this->coupon_code );
				}
			}
		}
	}
}

Wolmart_Product_Frequently_Bought_Together::get_instance();
