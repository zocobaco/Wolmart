/**
 * Javascript Library for Layout Builder Admin
 * 
 * - Page Layouts Model
 * 
 * @since 1.1
 * @package  Wolmart WordPress Framework
 */
'use strict';

window.Wolmart = window.Wolmart || {};

( function ( $ ) {

    /**
     * Comments Pagination Class
     * 
     * @since 1.1
     */
	var CommentsPagination = {
		init: function () {
			Wolmart.$body.on( 'click', '.comments .page-numbers', this.loadComments )
		},

		/**
		 * Load comments by using ajax
		 *
		 * @since 1.1
		 */
		loadComments: function ( e ) {
			e.preventDefault();
			var $number = $( e.target ).closest( '.page-numbers' );
			var $wrapper = $number.closest( '.comments' ).find( '.commentlist' ).eq( 0 );
			var $pagination = $number.closest( '.pagination' );
			var postID = parseInt( $pagination.data( 'post-id' ) );
			var url = $number.attr( 'href' );
			var pageNumber;

			if ( $number.hasClass( 'prev' ) ) {
				pageNumber = parseInt( $number.siblings( '.current' ).text() ) - 1;
			} else if ( $number.hasClass( 'next' ) ) {
				pageNumber = parseInt( $number.siblings( '.current' ).text() ) + 1;
			} else {
				pageNumber = parseInt( $number.text() );
			}

			// Relocate comment reply form's position.
			if ( $wrapper.find( '#cancel-comment-reply-link' ).length ) {
				$wrapper.find( '#cancel-comment-reply-link' )[ 0 ].click();
			}
			$wrapper.addClass( 'loading' );
			Wolmart.doLoading( $pagination, 'small' );

			$.post( wolmart_vars.ajax_url, {
				action: "wolmart_comments_pagination",
				nonce: wolmart_vars.nonce,
				page: pageNumber,
				post: postID
			}, function ( result ) {
				if ( result ) {
					history.pushState( {}, '', url );
					$wrapper.html( result.html );
					$pagination.html( result.pagination );
				}
			} ).always( function () {
				$wrapper.removeClass( 'loading' );
				Wolmart.endLoading( $pagination );
			} );
		}
	};

    /**
     * Setup Comments Pagination
     */
	Wolmart.CommentsPagination = CommentsPagination;
	Wolmart.$window.on( 'wolmart_complete', function () {
		CommentsPagination.init();
	} );
} )( jQuery );
