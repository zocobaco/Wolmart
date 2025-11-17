/**
 * Wolmart Plugin - LiveSearch
 * 
 * @requires jquery.autocomplete
 */
'use strict';
window.Wolmart || (window.Wolmart = {});

(function ($) {
	function LiveSearch(e, $selector) {
		if (!$.fn.devbridgeAutocomplete) {
			return;
		}

		if ('undefined' == typeof $selector) {
			$selector = $('.search-wrapper');
		} else {
			$selector = $selector;
		}

		$selector.each(function () {
			var $this = $(this),
				$appendTo = $this.find('.live-search-list'),
				$searchCat = $this.find('.cat'),
				postType = $this.find('input[name="post_type"]').val(),
				isAllCats = $this.find('select.all-cats').length,
				serviceUrlPrefix =  wolmart_vars.ajax_url + '?action=wolmart_ajax_search&nonce=' +
				wolmart_vars.nonce,
				serviceUrl = serviceUrlPrefix + (postType ? '&post_type=' + postType : '');
			if ( $this.hasClass( 'search-fullscreen' ) ) {
				$this.find( 'input[type="search"]' ).on( 'focus input', function () {
					if ( ! $this.hasClass( 'active-ready' ) ) {
						$this.parent().height( $this.height() );
						$this.addClass( 'active-ready' );
						$('body').addClass( 'overflow-hidden' );
						$('.page-wrapper').addClass( 'fullscreen-search' );
						var $slider = $this.find( '.slider-wrapper' ).data('slider');
						if ( $slider ) {
							$slider.update();
						}
					}
				} );
				$this.find( '.close-btn' ).on( 'click', function () {
					$this.parent().css( 'height', '' );
					$this.removeClass( 'active-ready' );
					$('body').removeClass( 'overflow-hidden' );
					$('.page-wrapper').removeClass( 'fullscreen-search' )
				} );
			}
			$this.find('input[type="search"]').devbridgeAutocomplete({
				minChars: 3,
				appendTo: $appendTo,
				triggerSelectOnValidInput: false,
				serviceUrl: serviceUrl,
				onSearchStart: function () {
					$this.addClass('skeleton-body');
					$appendTo.children().eq(0)
						.html(wolmart_vars.skeleton_screen ? '<div class="skel-pro-search"></div><div class="skel-pro-search"></div><div class="skel-pro-search"></div>' : '<div class="w-loading"><i></i></div>')
						.css({ position: 'relative', display: 'block' });
				},
				onSelect: function (item) {
					if (item.id != -1) {
						window.location.href = item.url;
					}
				},
				onSearchComplete: function (q, suggestions) {
					if (!suggestions.length) {
						$appendTo.children().eq(0).hide();
					}
					if ( $this.hasClass( 'search-fullscreen' ) ) {
						$this.find( 'input[type="search"]' ).off( 'blur.autocomplete' );
					}
				},
				beforeRender: function (container) {
					$(container).removeAttr('style');
				},
				formatResult: function (item, currentValue) {
					var pattern = '(' + $.Autocomplete.utils.escapeRegExChars(currentValue) + ')',
						html = '';
					if (item.img) {
						html += '<img class="search-image" src="' + item.img + '">';
					}
					html += '<div class="search-info">';
					html += '<div class="search-name">' +  ( ! postType ? '<strong>' + item.type + ':</strong> ' : '' ) + item.value.replace(new RegExp(pattern, 'gi'), '<strong>$1<\/strong>') + '</div>';
					if (item.price) {
						html += '<span class="search-price">' + item.price + '</span>';
					}
					html += '</div>';

					return html;
				}
			});

			if ($searchCat.length) {
				var searchForm = $this.find('input[type="search"]').devbridgeAutocomplete();
				$searchCat.on('change', function (e) {
					if ( $searchCat.val() && $searchCat.val() != '0' || $searchCat.val() == '' ) {
						searchForm.setOptions({
							serviceUrl: serviceUrlPrefix + ( isAllCats ? '&post_type=' + ( $this.find( 'option:selected' ).parent('optgroup').length ? $this.find( "option:selected" ).parent().data('type') : '' ) : ( postType ? '&post_type=' + postType : '' ) ) + '&cat=' + $searchCat.val()
						});
					} else {
						searchForm.setOptions({
							serviceUrl: serviceUrl
						});
					}

					searchForm.hide();
					searchForm.onValueChange();
				});
			}
		});
	}

	Wolmart.liveSearch = LiveSearch;
	$(window).on('wolmart_complete', Wolmart.liveSearch);
})(jQuery);