/**
 * Javascript Library for Admin
 * 
 * - Page Layouts Model
 * 
 * @since 1.0
 * @package  Wolmart WordPress Framework
 */
'use strict';

window.WolmartAdmin = window.WolmartAdmin || {};


// Admin Dashboard
( function ( wp, $ ) {

    /**
    * Show a dialog
    * 
    * @since 1.5.0
    */
    WolmartAdmin.prompt = {
        options: {
            title: wp.i18n.__( 'Title', 'wolmart' ),
            content: '',
            closeOnOverlay: true,
            actions: [ {
                title: wp.i18n.__( 'OK', 'wolmart' ),
            } ]
        },
        init: function () {
            if ( $( '.wolmart-dialog-wrapper' ).length ) {
                return;
            }

            $( document.body ).append( '<div class="wolmart-dialog-wrapper"><div class="wolmart-dialog-overlay"></div><div class="wolmart-dialog"></div></div>' );
            this.dialog = $( '.wolmart-dialog-wrapper' );

            $( document.body ).on( 'click', '.wolmart-dialog-wrapper .wolmart-dialog-close', function ( e ) {
                e.preventDefault();

                $( this ).closest( '.wolmart-dialog-wrapper' ).addClass( 'wolmart-dialog-closing' ).delay( 600 ).queue( function () {
                    $( this ).removeClass( 'show wolmart-dialog-closing' ).dequeue();
                } );
            } );

            $( document.body ).on( 'click', '.wolmart-dialog-wrapper .btn-yes', function ( e ) {
                e.preventDefault();
                if ( this.options.actions[ 0 ].callback ) {
                    this.options.actions[ 0 ].callback();
                }
                if ( 'undefined' == typeof ( this.options.actions[ 0 ].noClose ) ) {
                    $( e.currentTarget ).closest( '.wolmart-dialog-wrapper' ).find( '.wolmart-dialog-close' ).trigger( 'click' );
                }

            }.bind( this ) );

            $( document.body ).on( 'click', '.wolmart-dialog-wrapper .btn-no', function ( e ) {
                e.preventDefault();
                if ( this.options.actions[ 1 ].callback ) {
                    this.options.actions[ 1 ].callback();
                }
                if ( 'undefined' == typeof ( this.options.actions[ 1 ].noClose ) ) {
                    $( e.currentTarget ).closest( '.wolmart-dialog-wrapper' ).find( '.wolmart-dialog-close' ).trigger( 'click' );
                }
            }.bind( this ) );

            $( document.body ).on( 'click', '.wolmart-dialog-wrapper.close-on-overlay .wolmart-dialog-overlay', function ( e ) {
                e.preventDefault();
                $( e.currentTarget ).closest( '.wolmart-dialog-wrapper' ).find( '.wolmart-dialog-close' ).trigger( 'click' );
            } );

            document.addEventListener( 'keydown', function ( e ) {
                var keyName = e.key;
                if ( 'Escape' == keyName ) {
                    if ( $( '.wolmart-dialog-wrapper' ).length ) {
                        $( '.wolmart-dialog-wrapper' ).find( '.wolmart-dialog-close' ).trigger( 'click' );
                    }
                }
            } );
        },
        showDialog: function ( options ) {

            this.init();
            this.options = $.extend( {}, this.options, options );

            if ( !this.dialog.length ) {
                return;
            }

            if ( this.options.closeOnOverlay ) {
                this.dialog.addClass( 'close-on-overlay' );
            }

            var dialogClose = '<a href="#" class="wolmart-dialog-close"><svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="19px" height="19px" viewBox="0 0 19 19" version="1.1"><g><path style="fill:#3a3a3a;" d="M 2.734375 2.9375 L 2.925781 2.746094 L 16.234375 16.054688 L 16.042969 16.246094 Z M 2.734375 2.9375 "/><path style="fill:#3a3a3a;" d="M 2.734375 16.054688 L 16.042969 2.746094 L 16.234375 2.9375 L 2.925781 16.246094 Z M 2.734375 16.054688 "/></g></svg></a>', dialogHtml = '';

            dialogHtml += '<div class="wolmart-dialog-header"><h3 class="wolmart-dialog-title">' + this.options.title + '</h3>' + dialogClose + '</div>';
            dialogHtml += '<div class="wolmart-dialog-content"><p>' + this.options.content + '</p></div>';
            dialogHtml += '<div class="wolmart-dialog-footer">';

            var yesBtn = '', noBtn = '';

            if ( this.options.actions.length < 2 ) {
                yesBtn = '<button class="btn-yes">' + this.options.actions[ 0 ].title + '</button>';
            } else {
                yesBtn = '<button class="btn-yes">' + ( 'undefined' != typeof this.options.actions[ 0 ].title ? this.options.actions[ 0 ].title : wp.i18n.__( 'Yes', 'wolmart' ) ) + '</button>';
                noBtn = '<button class="btn-no">' + ( 'undefined' != typeof this.options.actions[ 1 ].title ? this.options.actions[ 1 ].title : wp.i18n.__( 'No', 'wolmart' ) ) + '</button>';
            }
            dialogHtml += noBtn + yesBtn + '</div></div>';

            this.dialog.find( '.wolmart-dialog' ).html( dialogHtml );
            this.dialog.addClass( 'show' );

        }
    };

} )( wp, jQuery );
