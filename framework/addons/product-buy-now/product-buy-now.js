/**
 * Wolmart Plugin - Product Buy Now
 * 
 * @instance single
 */
'use strict';
window.Wolmart || ( window.Wolmart = {} );

( function ( $ ) {
    var BuyNow = {
        /**
         * Register event for buy now button
         */
        init: function () {
            var self = this;

            // Initialize buy now action
            Wolmart.$body.find( 'form.cart' ).on( 'click', '.single_buy_now_button', function ( e ) {
                e.preventDefault();
                self.buyNow( e.target );
            } );

            // If variable product, disable Buy Now button until choose attribute
            Wolmart.$body.find( '.variations_form' ).on( 'hide_variation', function ( e ) {
                e.preventDefault();
                self.disableBuyNow();
            } );

            // Enable Buy Now button soon after choose attribute
            Wolmart.$body.find( '.variations_form' ).on( 'show_variation', function ( e, variation, purchasable ) {
                e.preventDefault();
                self.enableBuyNow( variation, purchasable );
            } );
        },
        buyNow: function ( el ) {
            var $form = $( el ).closest( 'form.cart' ),
                is_disabled = $( el ).is( ':disabled' );

            if ( is_disabled ) {
                $( 'html, body' ).animate( {
                    scrollTop: $( el ).offset().top - 200
                }, 600 );
            } else {
                $form.append( '<input type="hidden" value="true" name="buy_now" />' );
                $form.find( '.single_add_to_cart_button' ).addClass( 'has_buy_now' );
                $form.find( '.single_add_to_cart_button' ).trigger( 'click' );
            }
        },
        disableBuyNow: function ( e ) {
            $( '.variations_form' ).find( '.single_buy_now_button' ).addClass( 'disabled wc-variation-selection-needed' );
        },
        enableBuyNow: function ( variation, purchasable ) {
            if ( purchasable ) {
                $( '.variations_form' ).find( '.single_buy_now_button' ).removeClass( 'disabled wc-variation-selection-needed' );
            } else {
                $( '.variations_form' ).find( '.single_buy_now_button' ).addClass( 'disabled wc-variation-selection-needed' );
            }
        }
    };

    Wolmart.BuyNow = BuyNow;

    Wolmart.$window.on( 'wolmart_complete', function () {
        Wolmart.BuyNow.init();
    } )
} )( jQuery );