/**
 * Wolmart Frquently Bought Together
 *
 * @version 1.1
 */

'use strict';

window.Wolmart || ( window.Wolmart = {} );

( function ( $ ) {
	/**
	 * Initialize frequently bought together
	 * 
	 * @since 1.1
	 */
	function initFrequentlyBoughtTogether() {

		var discount = {
			enable: wolmart_fbt_vars.fbt_discount_enable,
			type: wolmart_fbt_vars.fbt_discount_type,
			fixed: wolmart_fbt_vars.fbt_discount_fixed,
			percent: wolmart_fbt_vars.fbt_discount_percentage,
			condition: wolmart_fbt_vars.fbt_discount_condition,
			spend: wolmart_fbt_vars.fbt_discount_spend,
			productCount: wolmart_fbt_vars.fbt_discount_products_count,
		}

		var $fbtProducts = $( '.product-fbt' ).eq( 0 ),
			$old = $fbtProducts.find( '.wolmart-data-oldprice' );

		/**
		 * Init frequently bought together price value
		 * 
		 * @return {void}
		 */
		function init() {
			var total = 0,
				real = 0,
				count = 0,
				compare = true,
				$fbt_old = $fbtProducts.find( '.wolmart_old_price' );


			$fbtProducts.find( '.product-wrap' ).each( function () {
				var $this = $( this ),
					$input = $this.find( 'input[type="checkbox"]' ),
					itemPrice = parseFloat( $input.data( 'price' ) );

				if ( $input.is( ':checked' ) ) {
					total = total + itemPrice;
					count += 1;
				}

			} )
			real = getDiscountAmount( parseFloat( total ) );
			$fbtProducts.find( '.bought-count span' ).text( parseInt( count ) );
			compare = discountEnable( total, count );

			if ( compare ) {
				$fbt_old.show();
			} else { $fbt_old.hide(); real = total; }

			if ( real && total ) {
				$fbt_old.find( '.woocommerce-Price-amount' ).html( '<bdi>' + $fbt_old.find( '.woocommerce-Price-currencySymbol' ).get( 0 ).outerHTML + total.toFixed( 2 ) + '</bdi>' );
				$fbtProducts.find( '.wolmart-data-oldprice' ).data( 'price', total.toFixed( 2 ) ).attr( 'data-price', total.toFixed( 2 ) );
				$fbtProducts.find( '.wolmart_total_price .woocommerce-Price-amount' ).html( '<bdi>' + $fbtProducts.find( '.wolmart_total_price .woocommerce-Price-currencySymbol' ).get( 0 ).outerHTML + real.toFixed( 2 ) + '</bdi>' );
				$fbtProducts.find( '.wolmart-data-price' ).data( 'price', real.toFixed( 2 ) ).attr( 'data-price', real.toFixed( 2 ) );
			} else {
				$fbtProducts.find( '.wolmart_total_price .woocommerce-Price-amount' ).html( '<bdi>' + $fbtProducts.find( '.wolmart_total_price .woocommerce-Price-currencySymbol' ).get( 0 ).outerHTML + real.toFixed( 2 ) + '</bdi>' );
				$fbtProducts.find( '.wolmart-data-price' ).data( 'price', real.toFixed( 2 ) ).attr( 'data-price', real.toFixed( 2 ) );
			}
		}

		/**
		 * Get the real Price for frequently bought together products to add to cart
		 * 
		 * @param {Float} totalPrice 
		 * @return {Float}
		 */
		function getDiscountAmount( totalPrice ) {
			var type = discount[ 'type' ],
				realPrice = 0;

			if ( totalPrice == 0 ) {
				return 0;
			}

			if ( type ) {
				if ( type == 'fixed' ) {
					realPrice = totalPrice > parseFloat( discount[ 'fixed' ] ) ? parseFloat( totalPrice - parseFloat( discount[ 'fixed' ] ) ) : totalPrice;
				} else {
					realPrice = parseFloat( totalPrice - totalPrice * parseInt( discount[ 'percent' ] ) / 100 );
				}
			}

			return realPrice;
		}

		/**
		 * Set the discount enable or not
		 * 
		 * @param {Float} totalprice products total price
		 * @param {Int} count products count
		 * @return {bool}
		 */
		function discountEnable( totalprice, count ) {
			var discount_cond = discount[ 'condition' ];
			if ( discount_cond != 'yes' ) {
				return true;
			}

			var spendAmount = parseFloat( discount[ 'spend' ] ),
				productCount = parseInt( discount[ 'productCount' ] );

			if ( ( spendAmount > 0 && productCount > 0 ) && spendAmount > totalprice || productCount > count ) {
				return false;
			}

			return true;
		}

		/**
		 * Event handler to add or remove frequently bought together products.
		 * 
		 * @since 1.0
		 * @param {Event} e 
		 */
		function onChangeFrequentlyBoughtTogetherItems( e ) {
			var $fbtProducts = $( e.currentTarget ).closest( ".product-fbt" ),
				$count = $fbtProducts.find( '.bought-count span' ),
				oldPrice = $old.length ? parseFloat( $fbtProducts.find( '.wolmart-data-oldprice' ).data( 'price' ) ) : 0,
				realPrice = parseFloat( $fbtProducts.find( '.wolmart-data-price' ).data( 'price' ) ),
				totalPrice = parseFloat( $fbtProducts.find( '.wolmart-data-price' ).data( 'price' ) ),
				$this = $( this ),
				currentPrice = $this.data( 'price' ) ? parseFloat( $this.data( 'price' ) ) : 0;

			if ( $old.length ) {
				totalPrice = oldPrice;
			}

			e.preventDefault();

			if ( !$this.is( ':checked' ) ) {
				$this.closest( '.product' ).addClass( 'inactive' );
				totalPrice -= currentPrice;
				$count.text( parseInt( $count.text() ) - 1 );
			} else {
				$this.closest( '.product' ).removeClass( 'inactive' );
				totalPrice += currentPrice;
				$count.text( parseInt( $count.text() ) + 1 );
			}

			var product_ids = '';
			$fbtProducts.find( 'input' ).each( function () {
				if ( $( this ).is( ':checked' ) ) {
					product_ids += ( product_ids ? ',' : '' ) + $( this ).data( 'id' );
				}
			} );

			totalPrice == 0 && ( realPrice = 0 );

			if ( $old.length ) {
				realPrice = getDiscountAmount( totalPrice );

				$fbtProducts.find( '.wolmart_old_price .woocommerce-Price-amount' ).html( '<bdi>' + $fbtProducts.find( '.wolmart_old_price .woocommerce-Price-currencySymbol' ).get( 0 ).outerHTML + totalPrice.toFixed( 2 ) + '</bdi>' );
				$fbtProducts.find( '.wolmart-data-oldprice' ).data( 'price', totalPrice.toFixed( 2 ) ).attr( 'data-price', totalPrice.toFixed( 2 ) );
			} else {
				realPrice = totalPrice;
			}

			var compare = discountEnable( totalPrice, parseInt( $count.text() ) );
			if ( !compare ) {
				$fbtProducts.find( '.wolmart_old_price' ).hide();
				realPrice = totalPrice;
			} else {
				$fbtProducts.find( '.wolmart_old_price' ).show();
			}

			$fbtProducts.find( '.wolmart_add_to_cart_button' ).attr( 'value', product_ids );
			$fbtProducts.find( '.wolmart_total_price .woocommerce-Price-amount' ).html( '<bdi>' + $fbtProducts.find( '.wolmart_total_price .woocommerce-Price-currencySymbol' ).get( 0 ).outerHTML + realPrice.toFixed( 2 ) + '</bdi>' );
			$fbtProducts.find( '.wolmart-data-price' ).data( 'price', realPrice.toFixed( 2 ) ).attr( 'data-price', realPrice.toFixed( 2 ) );
		}

		/**
		 * Recalculate the frequently bought products total price for variable product
		 * 
		 * @param {Event} e 
		 * @param {} variation 
		 */
		function onFBTFoundVariation( e, variation ) {
			if ( !$( '.product-fbt' ).length || !variation.is_in_stock ) {
				return;
			}
			var variationPrice = variation.display_price,
				variationId = variation.variation_id,
				variationMedia = variation.image;

			var $currentProduct = $( '.current-product' ),
				currency = $currentProduct.find( '.woocommerce-Price-currencySymbol' ).get( 0 ).outerHTML;

			var $fbtProducts = $( ".product-fbt" ),
				$count = $fbtProducts.find( '.bought-count span' ),
				oldPrice = $old.length ? parseFloat( $fbtProducts.find( '.wolmart-data-oldprice' ).data( 'price' ) ) : 0,
				realPrice = parseFloat( $fbtProducts.find( '.wolmart-data-price' ).data( 'price' ) ),
				totalPrice = parseFloat( $fbtProducts.find( '.wolmart-data-price' ).data( 'price' ) ),
				productIds = $fbtProducts.find( '.wolmart_add_to_cart_button' ).attr( 'value' );

			if ( $old.length ) {
				totalPrice = oldPrice;
			}

			if ( $currentProduct.hasClass( 'disabled' ) ) {
				$count.text( parseInt( $count.text() ) + 1 );
				$currentProduct.attr( 'data-content', $currentProduct.get( 0 ).outerHTML );
			} else {
				totalPrice -= $currentProduct.find( '.price' ).data( 'price' );
				productIds = productIds.split( ',' );
				productIds = productIds.filter( function ( idx ) {
					return idx != ( '' + $currentProduct.data( 'id' ) );
				} ).join( ',' );
			}

			totalPrice += variationPrice;
			productIds = productIds + ( productIds ? ',' : '' ) + variationId;

			totalPrice == 0 && ( realPrice = 0 );

			if ( $old.length ) {
				realPrice = getDiscountAmount( totalPrice );

				$fbtProducts.find( '.wolmart_old_price .woocommerce-Price-amount' ).html( '<bdi>' + currency + totalPrice.toFixed( 2 ) + '</bdi>' );
				$fbtProducts.find( '.wolmart-data-oldprice' ).data( 'price', totalPrice.toFixed( 2 ) );
			} else {
				realPrice = totalPrice;
			}

			var compare = discountEnable( totalPrice, parseInt( $count.text() ) );
			if ( !compare ) {
				$fbtProducts.find( '.wolmart_old_price' ).hide();
				realPrice = totalPrice;
			} else {
				$fbtProducts.find( '.wolmart_old_price' ).show();
			}

			$fbtProducts.find( '.wolmart_add_to_cart_button' ).attr( 'value', productIds );
			$fbtProducts.find( '.wolmart_total_price .woocommerce-Price-amount' ).html( '<bdi>' + currency + realPrice.toFixed( 2 ) + '</bdi>' );
			$fbtProducts.find( '.wolmart-data-price' ).data( 'price', realPrice.toFixed( 2 ) );

			$currentProduct.removeClass( 'disabled' );
			$currentProduct.find( 'input' ).prop( 'checked', true ).data( 'price', variationPrice );

			// Update current variatble product as matched variation
			$currentProduct.find( '.price' ).get( 0 ).outerHTML = '<div class="price" data-price="' + variationPrice + '">' + currency + variationPrice;
			$currentProduct.data( 'id', variationId );
			if ( $currentProduct.find( 'img' ).attr( 'src' ) != variationMedia.src ) {
				$currentProduct.find( 'img' )
					.attr( 'src', variationMedia.src )
					.attr( 'alt', variationMedia.alt )
					.attr( 'width', variationMedia.width )
					.attr( 'height', variationMedia.height )
			}
		}

		/**
		 * Recalculate the frequently bought products total price for variable product
		 * 
		 * @param {Event} e 
		 * @param {} variation 
		 */
		function onFBTResetData( e ) {
			if ( !$( '.product-fbt' ).length ) {
				return;
			}

			var $currentProduct = $( '.current-product' );

			if ( $currentProduct.attr( 'data-content' ) ) {

				var $fbtProducts = $( ".product-fbt" ),
					$count = $fbtProducts.find( '.bought-count span' ),
					oldPrice = $old.length ? parseFloat( $fbtProducts.find( '.wolmart-data-oldprice' ).data( 'price' ) ) : 0,
					realPrice = parseFloat( $fbtProducts.find( '.wolmart-data-price' ).data( 'price' ) ),
					totalPrice = parseFloat( $fbtProducts.find( '.wolmart-data-price' ).data( 'price' ) ),
					price = parseFloat( $currentProduct.find( '.price' ).data( 'price' ) ),
					productIds = $fbtProducts.find( '.wolmart_add_to_cart_button' ).attr( 'value' ),
					currency = $currentProduct.find( '.woocommerce-Price-currencySymbol' ).get( 0 ).outerHTML;

				if ( $old.length ) {
					totalPrice = oldPrice;
				}

				if ( $currentProduct.find( 'input' ).is( ':checked' ) ) {

					totalPrice -= price;

					productIds = productIds.split( ',' );

					productIds = productIds.filter( function ( idx ) {
						return idx != ( '' + $currentProduct.data( 'id' ) );
					} ).join( ',' );

					totalPrice == 0 && ( realPrice = 0 );

					if ( $old.length ) {
						realPrice = getDiscountAmount( totalPrice );

						$fbtProducts.find( '.wolmart_old_price .woocommerce-Price-amount' ).html( '<bdi>' + currency + totalPrice.toFixed( 2 ) + '</bdi>' );
						$fbtProducts.find( '.wolmart-data-oldprice' ).data( 'price', totalPrice.toFixed( 2 ) );
					} else {
						realPrice = totalPrice;
					}

					var compare = discountEnable( totalPrice, parseInt( $count.text() ) );
					if ( !compare ) {
						$fbtProducts.find( '.wolmart_old_price' ).hide();
						realPrice = totalPrice;
					} else {
						$fbtProducts.find( '.wolmart_old_price' ).show();
					}

					$fbtProducts.find( '.wolmart_add_to_cart_button' ).attr( 'value', productIds );
					$fbtProducts.find( '.wolmart_total_price .woocommerce-Price-amount' ).html( '<bdi>' + currency + realPrice.toFixed( 2 ) + '</bdi>' );
					$fbtProducts.find( '.wolmart-data-price' ).data( 'price', realPrice.toFixed( 2 ) );

					var $count = $fbtProducts.find( '.bought-count span' );
					$count.text( parseInt( $count.text() ) - 1 );
				}

				$currentProduct.get( 0 ).outerHTML = $currentProduct.attr( 'data-content' );
			}
		}

		init();
		Wolmart.$body.on( 'change', '.product-fbt input', onChangeFrequentlyBoughtTogetherItems )
			.on( 'found_variation', '.variations_form', onFBTFoundVariation )
			.on( 'reset_data', '.variations_form', onFBTResetData )
	}

	$( window ).on( 'wolmart_complete', initFrequentlyBoughtTogether );
} )( jQuery )