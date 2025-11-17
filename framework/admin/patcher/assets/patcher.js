/**
 * Wolmart Patcher Admin JS
 * 
 * @since 1.4.0
 */
jQuery( document ).ready( function( $ ) {
    'use strict';
    $( '#patch-apply:not(.inactive)' ).on( 'click', function( e ) {
        e.preventDefault();
        $( '#patcher-tbody' ).addClass( 'loading' ).append( '<i class="wolmart-ajax-loader"></i>' );
        $( '.wolmart-patch-layout .button' ).attr( 'disabled', true );
        $.ajax( {
            url: wolmart_admin_vars.ajax_url,
            type: 'POST',
            data: {
                'action': 'wolmart_apply_patches',
                'nonce': wolmart_admin_vars.nonce,
            },
            success: function( response ) {
                $( '.apply-alert' ).remove();
                if ( response.success ) {
                    if ( response.data ) {
                        var update_patches = response.data.update,
                            delete_patches = response.data.delete;
                        if ( 'object' == typeof update_patches ) {
                            update_patches = Object.keys( update_patches );
                            update_patches.forEach( patch => {
                                $( '[data-path="update-' + patch ).remove();
                            } );
                        }

                        if ( 'object' == typeof delete_patches ) {
                            delete_patches = Object.keys( delete_patches );
                            delete_patches.forEach( patch => {
                                $( '[data-path="delete-' + patch ).remove();
                            } );
                        }
                    }


                    if ( response.data.error ) {
                        console.log( response.data );
                        $( '.wolmart-patch-table-main' ).prepend( '<div class="apply-alert error"><p>' + wp.i18n.__( 'The below patches could not be applied. Because your files have write permission or aren\'t existed.', 'wolmart' ) + '</p></div>' );
                    } else {
                        $( '.wolmart-patch-table-main' ).prepend( '<div class="apply-alert updated"><p>' + wp.i18n.__( 'All files patched successfully.', 'wolmart' ) + '</p></div>' );

                        // Remove Apply Patch Button
                        $('.action-footer .button-primary').remove();
                    }
                } else {
                    $( '.wolmart-patch-table-main' ).prepend( '<div class="apply-alert error"><p>' + wp.i18n.__( 'The Wolmart patches server could not be reached.', 'wolmart' ) + '</p></div>' );
                }
                $( '#patcher-tbody' ).removeClass( 'loading' ).find( '.wolmart-ajax-loader' ).remove();
                $( '.wolmart-patch-layout .button' ).removeAttr( 'disabled' );
            },
        } );
    } )
} );