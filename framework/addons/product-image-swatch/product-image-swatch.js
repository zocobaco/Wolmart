/**
 * Wolmart Plugin - Product Swatch
 * 
 * @instance single
 */
'use strict';
window.Wolmart || ( window.Wolmart = {} );

( function ( $ ) {

	var Swatch = {
		/**
		 * Register events for swatch
		 * 
		 * @since 1.0
		 */
		init: function () {
			this.skipReset = false;

			Wolmart.$body
				// Archive product
				.on( 'click', 'li>.product .product-variations>button', function ( e ) {
					var $btn = $( e.currentTarget );
					if ( !$btn.closest( '.product' ).hasClass( 'product-single' ) ) {
						Swatch.previewArchive( $btn );
					}
				} )

				// Single product
				.on( 'click', '.product-single .product-variations > button', function ( e ) {
					var $btn = $( e.currentTarget );
					Swatch.skipReset = true;
					Wolmart.requestTimeout( function () {
						Swatch.skipReset = false;
						Swatch.previewSingle( $btn );
					}, 50 );
				} );

			var wc_reset_variation_attr = $.fn.wc_reset_variation_attr;
			$.fn.wc_reset_variation_attr = function ( attr ) {
				Swatch.skipReset || wc_reset_variation_attr.call( this, attr );
			};
		},

		/**
		 * Preview swatch image for archive products
		 * 
		 * @since 1.0
		 */
		previewArchive: function ( $btn ) {
			if ( $btn.hasClass( 'disabled' ) ) {
				return;
			}
			var isActive = $btn.hasClass( 'active' );

			if ( ! $btn.closest( '.product-variation-wrapper' ).length && ! isActive ) {
				$btn.closest( '.product-variations' ).children( 'button' ).removeClass( 'active' );
				$btn.addClass( 'active' );
			}

			if ( $btn.data( 'image' ) ) {
				var $img = $btn.closest( '.product' ).find( '.product-media img:first-child' );
				if ( isActive ) {
					$img
						.attr( 'src', $img.data( 'origin-src' ) )
						.attr( 'srcset', $img.data( 'origin-srcset' ) );
				} else {
					var match = $btn.data( 'image' ).match( /src="([^"]*)"/ );
					if ( match && match.length == 2 ) {
						$img.data( 'origin-src' ) || $img.data( 'origin-src', $img.attr( 'src' ) );
						$img.attr( 'src', match[ 1 ] );
					}
					match = $btn.data( 'image' ).match( /srcset="([^"]*)"/ );
					if ( match && match.length == 2 ) {
						$img.data( 'origin-srcset' ) || $img.data( 'origin-srcset', $img.attr( 'srcset' ) );
						$img.attr( 'srcset', match[ 1 ] );
					}
				}
			}
		},

		/**
		 * Preview swatch image for single product
		 * 
		 * @since 1.0
		 */
		previewSingle: function ( $btn ) {
			var $form = $btn.closest( '.variations_form' ),
				variationImage = $form.attr( 'current-image' );

			// If no variation is matched
			if ( !variationImage ) {
				var $product = $btn.closest( '.product' );

				// if deactive image, find active image button
				if ( !$btn.hasClass( 'active' ) ) {
					$btn = $form.find( '.image.active' ).not( $btn ).first();
				}

				if ( $btn.length ) {
					// activate swatch image
					var swatchImageHtml = $btn.attr( 'data-image' );
					if ( swatchImageHtml ) {
						var $product_img = $product.find( '.woocommerce-product-gallery__wrapper .wp-post-image' ),
							$swatchImage = $( swatchImageHtml );

						$product_img.wc_set_variation_attr( 'src', $swatchImage.attr( 'src' ) );
						$product_img.wc_set_variation_attr( 'height', $swatchImage.attr( 'height' ) );
						$product_img.wc_set_variation_attr( 'width', $swatchImage.attr( 'width' ) );
						$product_img.wc_set_variation_attr( 'srcset', $swatchImage.attr( 'srcset' ) );
						$product_img.wc_set_variation_attr( 'sizes', $swatchImage.attr( 'sizes' ) );
						$product_img.wc_set_variation_attr( 'title', $swatchImage.attr( 'title' ) );
						$product_img.wc_set_variation_attr( 'data-caption', $swatchImage.attr( 'data-caption' ) );
						$product_img.wc_set_variation_attr( 'alt', $swatchImage.attr( 'alt' ) );
						$product_img.wc_set_variation_attr( 'data-src', $swatchImage.attr( 'data-src' ) );
						$product_img.wc_set_variation_attr( 'data-large_image', $swatchImage.attr( 'data-large_image' ) );
						$product_img.wc_set_variation_attr( 'data-large_image_width', $swatchImage.attr( 'data-large_image_width' ) );
						$product_img.wc_set_variation_attr( 'data-large_image_height', $swatchImage.attr( 'data-large_image_height' ) );
					}
				} else {
					// reset
					$form.wc_variations_image_reset();
				}

				// refresh gallery
				var gallery = $product.find( '.woocommerce-product-gallery' ).data( 'wolmart_product_gallery' );
				gallery & gallery.changePostImage();
			}
		},
	};

	Wolmart.Swatch = Swatch;

	Wolmart.$window.on( 'wolmart_complete', function () {
		Wolmart.Swatch.init();
	} )
} )( jQuery );