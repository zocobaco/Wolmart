/**
 * Wolmart Theme Library
 * 
 * @package Wolmart WordPress theme
 * @version 1.0
 */
'use strict';

window.Wolmart || (window.Wolmart = {});

(function ($) {
	/**
	 * jQuery Window Object
	 * 
	 * @var {jQuery} $window
	 * @since 1.0
	 */
	Wolmart.$window = $(window);

	/**
	 * jQuery Body Object
	 * 
	 * @var {jQuery} $body
	 * @since 1.0
	 */
	Wolmart.$body = $(document.body);

	/**
	 * Status
	 * 
	 * @var {string} status
	 * @since 1.0
	 */
	Wolmart.status = 'loading';

	/**
	 * Hash
	 * 
	 * @var {string} hash
	 * @since 1.0
	 */
	Wolmart.hash = location.hash.indexOf('&') > 0 ? location.hash.substring(0, location.hash.indexOf('&')) : location.hash;

	/**
	 * Detect Internet Explorer
	 * 
	 * @var {boolean} isIE
	 * @since 1.0
	 */
	Wolmart.isIE = navigator.userAgent.indexOf("Trident") >= 0;

	/**
	 * Detect Edge
	 * 
	 * @var {boolean} isEdge
	 * @since 1.0
	 */
	Wolmart.isEdge = navigator.userAgent.indexOf("Edge") >= 0;

	/**
	 * Detect Mobile
	 * 
	 * @var {boolean} isMobile
	 * @since 1.0
	 */
	Wolmart.isMobile = /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);

	/**
	 * Detect Mobile & Tablet  
	 *
	 * @since 1.1.5
	 */
	Wolmart.isMobileAndTablet = function () {
		let check = false;
		(function (a) { if (/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a) || /1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0, 4))) check = true; })(navigator.userAgent || navigator.vendor || window.opera);
		return check;
	};

	// Canvas Size
	Wolmart.canvasWidth = (Wolmart.isMobileAndTablet ? window.outerWidth : window.innerWidth);
	Wolmart.resizeTimeStamp = 0;
	Wolmart.resizeChanged = false;
	Wolmart.scrollbarSize = -1;

	/**
	 * Default options
	 * 
	 * @var {Object} defaults
	 * @since 1.0
	 */
	Wolmart.defaults = {
		stickySidebar: {
			autoInit: true,
			minWidth: 991,
			containerSelector: '.sticky-sidebar-wrapper',
			autoFit: true,
			activeClass: 'sticky-sidebar-fixed',
			padding: {
				top: 0,
				bottom: 0
			},
		},
		isotope: {
			itemsSelector: '.grid-item',
			layoutMode: 'masonry',
			percentPosition: true,
			masonry: {
				columnWidth: '.grid-space'
			},
			getSortData: {
				order: '[data-creative-order] parseInt',
				order_lg: '[data-creative-order-lg] parseInt',
				order_md: '[data-creative-order-md] parseInt',
			},
		},
		lazyload: {
			effect: 'fadeIn',
			data_attribute: 'lazy',
			data_srcset: 'lazyset',
			effect_speed: 400,
			failure_limit: 1000,
			event: 'scroll update_lazyload',
			load: function () {
				if ('IMG' == this.tagName) {
					this.style['padding-top'] = '';
					this.classList.remove('w-lazyload');
				} else {
					if (this.classList.contains('elementor-element-populated') || this.classList.contains('elementor-section')) {
						this.style['background-image'] = '';
					}
				}
				this.removeAttribute('data-lazy');
				this.removeAttribute('data-lazyset');
				this.removeAttribute('data-sizes');
			}
		},
		sticky: {
			minWidth: 992,
			maxWidth: 20000,
			top: false,
			bottomOrigin: false,
			// hide: false, // hide when it is not sticky.
			max_index: 1059, // maximum z-index of sticky contents
			scrollMode: false
		},
		animation: {
			name: 'fadeIn',
			duration: '1.2s',
			delay: '.2s'
		},
		stickyMobileBar: {
			minWidth: 0,
			maxWidth: 767,
			top: 150,
			// hide: true,
			scrollMode: true
		},
		stickyToolbox: {
			minWidth: 0,
			maxWidth: 767,
			scrollMode: true
		},
		minipopup: {
			content: '',
			delay: 4000, // milliseconds
		}
	};

	/**
	 * Create a macro task
	 *
	 * @since 1.0
	 * @param {function} fn  Function to handle task.
	 * @param {number} delay Delay time
	 * @return {void}
	 */
	Wolmart.call = function (fn, delay) {
		wolmart_vars.a || delay ? setTimeout(fn, delay) : fn();
	}

	/**
	 * Get DOM element by id
	 * 
	 * @since 1.0
	 * @param {string} id    ID attribute of element to find
	 * @return {HTMLElement} Matched element
	 */
	Wolmart.byId = function (id) {
		return document.getElementById(id);
	}

	/**
	 * Get DOM elements by tagName
	 * 
	 * @since 1.0
	 * @param {string} tagName   Tag name to find
	 * @param {HTMLElement} root Root element. This can be omitted.
	 * @return {HTMLCollection}
	 */
	Wolmart.byTag = function (tagName, root) {
		return (root ? root : document).getElementsByTagName(tagName);
	}

	/**
	 * Get DOM elements by className
	 * 
	 * @since 1.0
	 * @param {string} className Class name to find
	 * @param {HTMLElement} root Root elements
	 * @return {HTMLCollection}  Matched elements
	 */
	Wolmart.byClass = function (className, root) {
		return root ? root.getElementsByClassName(className) : document.getElementsByClassName(className);
	}

	/**
	 * Get jQuery object
	 * 
	 * @since 1.0
	 * @param {string|jQuery} selector	Selector to find
	 * @param {string|jQuery} find		Find from selector root
	 * @return {jQuery|Object}			jQuery Object or {each: $.noop}
	 */
	Wolmart.$ = function (selector, find) {
		if (typeof selector == 'string' && typeof find == 'string') {
			return $(selector + ' ' + find);
		}
		if (selector instanceof jQuery) {
			if (selector.is(find)) {
				return selector;
			}
			if (typeof find == 'undefined') {
				return selector;
			}
			return selector.find(find);
		}
		if (typeof selector == 'undefined' || !selector) {
			return $(find);
		}
		if (typeof find == 'undefined') {
			return $(selector);
		}
		return $(selector).find(find);
	}


	/**
	 * Get Cache Object
	 * 
	 * @since 1.0
	 * @return {Object} 
	 */
	Wolmart.getCache = function () {
		return localStorage[wolmart_vars.wolmart_cache_key] ? JSON.parse(localStorage[wolmart_vars.wolmart_cache_key]) : {};
	}

	/**
	 * Set Cache Object
	 * 
	 * @since 1.0
	 * @param {mixed} cache
	 * @return {void}
	 */
	Wolmart.setCache = function (cache) {
		localStorage[wolmart_vars.wolmart_cache_key] = JSON.stringify(cache);
	}

	/**
	 * Request timeout by using requestAnimationFrame
	 * 
	 * @since 1.0
	 * @param {function} fn
	 * @param {number} delay
	 * @return {Object} handle
	 */
	Wolmart.requestTimeout = function (fn, delay) {
		var handler = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame;
		if (!handler) {
			return setTimeout(fn, delay);
		}
		delay || (delay = 0);
		var start, rt = new Object();

		function loop(timestamp) {
			if (!start) {
				start = timestamp;
			}
			var progress = timestamp - start;
			progress >= delay ? fn() : rt.val = handler(loop);
		};

		rt.val = handler(loop);
		return rt;
	}

	/**
	 * Request frame by using requestAnimationFrame
	 * 
	 * @since 1.0
	 * @param {function} fn
	 * @return {Object} handle
	 */
	Wolmart.requestFrame = function (fn) {
		return { val: (window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame)(fn) };
	}

	/**
	 * Request interval by using requestAnimationFrame
	 *
	 * @since 1.0
	 * @param {function} fn
	 * @param {number} step
	 * @param {number} timeOut
	 * @return {Object} handle
	 */
	Wolmart.requestInterval = function (fn, step, timeOut) {
		var handler = window.requestAnimationFrame || window.webkitRequestAnimationFrame || window.mozRequestAnimationFrame;
		if (!handler) {
			if (!timeOut)
				return setTimeout(fn, timeOut);
			else
				return setInterval(fn, step);
		}
		var start, last, rt = new Object();
		function loop(timestamp) {
			if (!start) {
				start = last = timestamp;
			}
			var progress = timestamp - start;
			var delta = timestamp - last;
			if (!timeOut || progress < timeOut) {
				if (delta > step) {
					rt.val = handler(loop);
					fn();
					last = timestamp;
				} else {
					rt.val = handler(loop);
				}
			} else {
				fn();
			}
		};
		rt.val = handler(loop);
		return rt;
	}

	/**
	 * Delete timeout by using requestAnimationFrame
	 * 
	 * @since 1.0
	 * @param {number} timerID
	 * @return {void}
	 */
	Wolmart.deleteTimeout = function (timerID) {
		if (!timerID) {
			return;
		}
		var handler = window.cancelAnimationFrame || window.webkitCancelAnimationFrame || window.mozCancelAnimationFrame;
		if (!handler) {
			return clearTimeout(timerID);
		}
		if (timerID.val) {
			return handler(timerID.val);
		}
	}

	function debounce(func, threshold, execAsap) {
		var timeout;
		return function debounced() {
			var obj = this, args = arguments;
			function delayed() {
				execAsap || func.apply(obj, args);
				timeout = null;
			}

			if (timeout)
				Wolmart.deleteTimeout(timeout);
			else if (execAsap)
				func.apply(obj, args);

			timeout = Wolmart.requestTimeout(delayed, threshold || 100);
		};
	};

	/**
	 * Smart resize
	 */
	$.fn.smartresize = function (fn) {
		fn ? this.get(0).addEventListener('resize', debounce(fn), { passive: true }) : this.trigger('smartresize');
	};

	/**
	 * Smart scroll
	 */
	$.fn.smartscroll = function (fn) {
		fn ? this.get(0).addEventListener('scroll', debounce(fn), { passive: true }) : this.trigger('smartscroll');
	};

	/**
	 * Parse options string to object
	 * 
	 * @since 1.0
	 * @param {string} options	Options string
	 * @return {object}
	 */
	Wolmart.parseOptions = function (options) {
		return 'string' == typeof options ? JSON.parse(options.replace(/'/g, '"').replace(';', '')) : {};
	}

	/**
	 * Check if given element is on screen
	 * 
	 * @since 1.0
	 * @param {HTMLElement} el
	 * @param {number} dx
	 * @param {number} dy
	 * @return {boolean}
	 */
	Wolmart.isOnScreen = function (el, dx, dy) {
		var a = window.pageXOffset,
			b = window.pageYOffset,
			o = el.getBoundingClientRect(),
			x = o.left + a,
			y = o.top + b,
			ax = typeof dx == 'undefined' ? 0 : dx,
			ay = typeof dy == 'undefined' ? 0 : dy;

		return y + o.height + ay >= b &&
			y <= b + window.innerHeight + ay &&
			x + o.width + ax >= a &&
			x <= a + window.innerWidth + ax;
	}


	/**
	 * Run appear animation
	 * 
	 * @since 1.0
	 * @param {HTMLElement} el DOM Element to appear
	 * @param {function} fn    Callback function
	 * @param {object} intObsOptions Options
	 * @return {void}
	 */
	Wolmart.appear = function (el, fn, intObsOptions) {

		var $this = $(el);

		if ($this.data('observer-init')) {
			return;
		}

		var interSectionObserverOptions = {
			rootMargin: '0px 0px 200px 0px',
			threshold: 0,
			alwaysObserve: true
		};

		if (intObsOptions && Object.keys(intObsOptions).length) {
			interSectionObserverOptions = $.extend(interSectionObserverOptions, intObsOptions);
		}

		var observer = new IntersectionObserver(function (entries) {
			for (var i = 0; i < entries.length; i++) {
				var entry = entries[i];

				if (entry.intersectionRatio > 0) {
					if (typeof fn === 'string') {
						var func = Function('return ' + functionName)();
					} else {
						var callback = fn;
						callback.call(entry.target);
					}
				}
			}
		}, interSectionObserverOptions);

		observer.observe(el);

		$this.data('observer-init', true);

		return this;
	}


	/**
	 * Fit posts' videos
	 *
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.fitVideoSize = function (selector) {
		if ($.fn.fitVids) {
			var $selector = (typeof $selector == 'undefined' ? $('.fit-video') : Wolmart.$(selector).find('.fit-video'));

			$selector.each(function () {
				var $this = $(this),
					$video = $this.find('video'),
					w = $video.attr('width'),
					h = $video.attr('height'),
					cw = $this.outerWidth();

				$video.css({ width: cw, height: cw / w * h });

				if (window.wp.mediaelement) {
					window.wp.mediaelement.initialize();
				}

				$this.fitVids();

				$this.hasClass('d-none') && $this.removeClass('d-none');
			})

			Wolmart.status == 'loading' &&
				window.addEventListener('resize', function () {
					$('.fit-video').fitVids();
				}, { passive: true });
		}
	}

	/**
	 * Run isotopes
	 *
	 * @since 1.0
	 * @param {string} selector
	 * @param {Object} options
	 * @return {void}
	 */
	Wolmart.isotopes = (function () {
		function _isotopeSort(e, $selector) {
			var $grid = $selector ? $selector : $('.grid');

			if (!$grid.length) {
				return;
			}

			$grid.each(function (e) {
				var $this = $(this);
				if (!$this.attr('data-creative-breaks') || $this.hasClass('float-grid')) {
					return;
				}

				$this.children('.grid-item').css({ 'animation-fill-mode': 'none', '-webkit-animation-fill-mode': 'none' });

				var width = window.innerWidth,
					breaks = JSON.parse($this.attr('data-creative-breaks')),
					cur_break = $this.attr('data-current-break');

				if (width >= breaks['lg']) {
					width = '';
				} else if (width >= breaks['md'] && width < breaks['lg']) {
					width = 'lg';
				} else if (width < breaks['md']) {
					width = 'md';
				}

				if (width == cur_break) {
					return;
				}

				if ($this.data('isotope')) {
					$this.isotope({
						sortBy: 'order' + (width ? '_' + width : ''),
					}).isotope('layout');
				} else {
					var options = Wolmart.parseOptions($this.attr('data-grid-options'));
					options.sortBy = 'order' + (width ? '_' + width : '');
					$this.attr('data-grid-options', JSON.stringify(options));
				}
				$this.attr('data-current-break', width);
			});
		}

		return function (selector, options) {
			if (!$.fn.imagesLoaded || !$.fn.isotope) {
				return;
			}
			Wolmart.$(selector).each(function () {
				var $this = $(this);
				if ($this.hasClass('grid-float')) {
					return;
				}

				var settings = $.extend(true, {},
					Wolmart.defaults.isotope,
					Wolmart.parseOptions(this.getAttribute('data-grid-options')),
					options ? options : {},
					$this.hasClass('masonry') ? { horizontalOrder: true } : {}
				);

				_isotopeSort('', $this);

				if (settings.masonry.columnWidth && !$this.children(settings.masonry.columnWidth).length) {
					delete settings.masonry.columnWidth;
				}

				Object.setPrototypeOf(this, HTMLElement.prototype);
				$this.children().each(function () {
					Object.setPrototypeOf(this, HTMLElement.prototype);
				});

				$this.imagesLoaded(function () {
					$this.addClass('isotope-loaded').isotope(settings);
					'undefined' != typeof elementorFrontend && $this.trigger('resize.waypoints');
				});
			});

			Wolmart.$window.on('resize', _isotopeSort);
		}
	})();

	/**
	 * Make sidebar sticky
	 *
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.stickySidebar = function (selector) {
		if ($.fn.themeSticky) {
			Wolmart.$(selector).each(
				function () {
					var $this = $(this),
						aside = $this.closest('.sidebar'),
						options = Wolmart.defaults.stickySidebar,
						top = 0;

					// Do not sticky for off canvas sidebars.
					if (aside.hasClass('sidebar-offcanvas')) {
						return;
					}

					// Add wrapper class
					(aside.length ? aside : $this.parent()).addClass('sticky-sidebar-wrapper');

					$('.sticky-sidebar > .filter-actions').length || $('.sticky-content.fix-top').each(function (e) {
						if ($(this).hasClass('sticky-toolbox')) {
							return;
						}

						var $fixed = $(this).hasClass('fixed');

						top += $(this).addClass('fixed').outerHeight();

						$fixed || $(this).removeClass('fixed');
					});

					// VC Sticky Content
					$('.sticky-sidebar > .filter-actions').length || $('[data-vce-sticky-element=true]').each(function (e) {
						top += $(this).outerHeight();
					});

					options['padding']['top'] = top;

					$this.themeSticky($.extend({}, options, Wolmart.parseOptions($this.attr('data-sticky-options'))));

					// issue: tab change of single product's tab in summary sticky sidebar
					Wolmart.$window.on('wolmart_complete', function () {
						Wolmart.refreshLayouts();
						$this.on('click', '.nav-link', function () {
							setTimeout(function () {
								$this.trigger('recalc.pin');
							});
						});
					});
				}
			);
		}
	}

	/**
	 * Refresh layouts
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.refreshLayouts = function () {
		$('.sticky-sidebar').trigger('recalc.pin');
		Wolmart.$window.trigger('update_lazyload');
	}

	/**
	 * Force lazyLoad
	 *
	 * @since 1.0
	 * @param {jQuery|string} selector
	 * @return {void}
	 */
	Wolmart._lazyload_force = function (selector) {
		Wolmart.$(selector).each(function () {
			var src = this.getAttribute('data-lazy');
			if (src) {
				if (this.tagName == 'IMG') {
					var srcset = this.getAttribute('data-lazyset');
					if (srcset) {
						this.setAttribute('srcset', srcset)
						this.removeAttribute('data-lazyset');
					}
					this.style['padding-top'] = '';
					this.setAttribute('src', src);
					this.classList.remove('w-lazyload');
				} else {
					this.style['background-image'] = 'url(' + src + ')';
				}
				this.removeAttribute('data-lazy');
				this.removeAttribute('data-lazyset');
			}
		})
	}

	/**
	 * LazyLoad
	 *
	 * @since 1.0
	 * @param {jQuery|string} selector
	 * @return {void}
	 */
	Wolmart.lazyload = function (selector) {
		$.fn.lazyload && Wolmart.$(selector, '[data-lazy]').lazyload(Wolmart.defaults.lazyload);
	}

	/**
	 * Initialize price slider
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initPriceSlider = function () {
		if ($.fn.slider && $('.price_slider').length) {
			$('input#min_price, input#max_price').hide();
			$('.price_slider, .price_label').show();

			var min_price = $('.price_slider_amount #min_price').data('min'),
				max_price = $('.price_slider_amount #max_price').data('max'),
				step = $('.price_slider_amount').data('step') || 1,
				current_min_price = $('.price_slider_amount #min_price').val(),
				current_max_price = $('.price_slider_amount #max_price').val();

			$('.price_slider:not(.ui-slider)').slider({
				range: true,
				animate: true,
				min: min_price,
				max: max_price,
				step: step,
				values: [current_min_price, current_max_price],
				create: function () {
					$('.price_slider_amount #min_price').val(current_min_price);
					$('.price_slider_amount #max_price').val(current_max_price);
					$(document.body).trigger('price_slider_create', [current_min_price, current_max_price]);
				},
				slide: function (e, ui) {
					$('input#min_price').val(ui.values[0]);
					$('input#max_price').val(ui.values[1]);
					$(document.body).trigger('price_slider_slide', [ui.values[0], ui.values[1]]);
				},
				change: function (e, ui) {
					$(document.body).trigger('price_slider_change', [ui.values[0], ui.values[1]]);
				}
			})
		}
	}

	/**
	 * Show loading overlay
	 * 
	 * @since 1.0
	 * @param {string|jQuery} selector 
	 * @param {string} type
	 * @return {void}
	 */
	Wolmart.doLoading = function (selector, type) {
		var $selector = Wolmart.$(selector);
		if (typeof type == 'undefined') {
			$selector.append('<div class="w-loading"><i></i></div>');
		} else if (type == 'small') {
			$selector.append('<div class="w-loading small"><i></i></div>');
		} else if (type == 'simple') {
			$selector.append('<div class="w-loading small"></div>');
		}

		if ('static' == $selector.css('position')) {
			Wolmart.$(selector).css('position', 'relative');
		}
	}

	/**
	 * Hide loading overlay
	 * 
	 * @since 1.0
	 * @param {string|jQuery} selector
	 * @return {void}
	 */
	Wolmart.endLoading = function (selector) {
		Wolmart.$(selector).find('.w-loading').remove();
		Wolmart.$(selector).css('position', '');
	}

	/**
	 * Set current menu items
	 * 
	 * @since 1.0
	 * @param {string|jQuery} selector
	 * @return {void}
	 */
	Wolmart.setCurrentMenuItems = function (selector) {
		if (Wolmart.getUrlParam(location.href, 's')) {
			// if search page
			return;
		}
		var $current = Wolmart.$(selector, 'a[href="' + location.origin + location.pathname + '"]');
		$current.parent('li').each(function () {
			var $this = $(this);
			if ($this.hasClass('menu-item-object-page')) {
				$this.addClass('current_page_item')
					.parent().closest('.mobile-menu li').addClass('current_page_parent')
				$this.parents('.mobile-menu li').addClass('current_page_ancestor');
			}
			$this.addClass('current-menu-item')
				.parent().closest('.mobile-menu li').addClass('current-menu-parent');
			$this.parents('.mobile-menu li').addClass('current-menu-ancestor');
		})
	}

	/**
	 * LazyLoad menu
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.lazyloadMenu = function () {
		// lazyload menu
		var lazyMenus = $('.lazy-menu').map(function () {
			return this.getAttribute('id').slice(5); // remove prefix 'menu-'
		}).get();

		// If lazy menu exists
		if (lazyMenus && lazyMenus.length) {

			// Function to change loaded menu
			function changeLoadedMenu(menuId, menuContent) {
				var $submenus = $(Wolmart.byId('menu-' + menuId)).removeClass('lazy-menu').children('li');
				$(menuContent).filter('li').each(function () {
					var $newli = $(this),
						$oldli = $submenus.eq($newli.index());
					$oldli.children('ul').remove();
					$oldli.append($newli.children('ul'));
				});

				Wolmart.setCurrentMenuItems('#menu-' + menuId);
			}

			// Cache
			var cache = Wolmart.getCache(),
				cachedMenus = cache.menus ? cache.menus : {},
				nonCachedMenus = [];

			// Check if latest menu cache exists
			if (wolmart_vars.lazyload_menu && cache.menus && cache.menuLastTime && wolmart_vars.menu_last_time &&
				parseInt(cache.menuLastTime) >= parseInt(wolmart_vars.menu_last_time)) {

				for (var id in lazyMenus) {
					var menuId = lazyMenus[id];
					if (cachedMenus[menuId]) {
						changeLoadedMenu(menuId, cachedMenus[menuId]);
					} else {
						nonCachedMenus.push(menuId);
					}
				}
			} else {
				// no cache
				nonCachedMenus = lazyMenus;
			}

			// Fetch menus from server 
			if (nonCachedMenus.length) {
				$.ajax({
					type: 'POST',
					url: wolmart_vars.ajax_url,
					dataType: 'json',
					data: {
						action: "wolmart_load_menu",
						menus: nonCachedMenus,
						nonce: wolmart_vars.nonce,
						load_menu: true,
					},
					success: function (menus) {
						if (menus) {
							for (var menuId in menus) {
								var result = menus[menuId];
								if (result) {
									result = result.replace(/(class=".*)current_page_parent\s*(.*")/, '$1$2');
									changeLoadedMenu(menuId, result);
									cachedMenus[menuId] = result;
								}
							}
						}
						Wolmart.menu && Wolmart.menu.addToggleButtons('.collapsible-menu li');
						Wolmart.showEditPageTooltip && Wolmart.showEditPageTooltip();

						// save menu cache
						cache.menus = cachedMenus;
						cache.menuLastTime = wolmart_vars.menu_last_time;
						Wolmart.setCache(cache);
					}
				});
			}
		}
	}

	/**
	 * Disable mobile animations
	 * 
	 * @since 1.0
	 */
	Wolmart.disableMobileAnimations = function () {
		if ($(document.body).hasClass('wolmart-disable-mobile-animation') && window.innerWidth < 768) {
			$('.elementor-invisible').removeAttr('data-settings').removeData('settings').removeClass('elementor-invisible')
				.add($('.appear-animate').removeClass('appear-animate'))
				.add($('[data-vce-animate]').removeAttr('data-vce-animate').removeData('vce-animate'))
		}
	}

	/**
	 * Initialize layouts
	 * 
	 * @since 1.0
	 */
	Wolmart.initLayout = function () {
		Wolmart.fitVideoSize();									// Fit Video Size
		Wolmart.isotopes('.grid');								// Masonry Layout
		Wolmart.stickySidebar('.sticky-sidebar');				// Sticky Sidebar
		Wolmart.lazyload();										// Lazy Load
		Wolmart.$body.one('mouseenter touchstart', '.lazy-menu', Wolmart.lazyloadMenu); // Lazy Load Menu
		Wolmart.initPriceSlider();								// Initialize price sliders.

		Wolmart.status == 'loading' && (Wolmart.status = 'load');
		Wolmart.$window.trigger('wolmart_load');
		wolmart_vars.resource_after_load ?
			Wolmart.call(Wolmart.initAsync) :
			Wolmart.initAsync();

		// fix compatibility issue with the Yith Wishlist Pro plugin
		if ($.fn.imagesLoaded && typeof Wolmart.skeleton === 'function' && Wolmart.$body.find('.product').length) {
			Wolmart.$(document).trigger('yith_infs_added_elem');
		}
	}

	/**
	 * Disable mobile animations
	 */
	Wolmart.disableMobileAnimations(); // Disable mobile animations if it's enabled

	/**
	 * Store Swiper Class
	 */
	if (typeof Swiper == 'function') {
		Wolmart.Swiper = Swiper;
	} else if (!($(document.body).hasClass('wolmart-disable-mobile-slider') && ('ontouchstart' in document) && (window.innerWidth < 1200))) {
		var swiperScript;
		swiperScript = document.getElementById('swiper-js');
		if (!swiperScript && wolmart_vars.swiper_url) {
			var s = document.scripts[0];
			swiperScript = document.createElement('script');
			swiperScript.src = wolmart_vars.swiper_url;
			swiperScript.async = true;
			swiperScript = s.parentNode.insertBefore(swiperScript, s);
		}
		if (swiperScript) {
			swiperScript.addEventListener('load', function () {
				Wolmart.Swiper = Swiper;
			})
		}
	}

	/**
	 * Wolmart Theme Setup
	 */
	$(window).on('load', function () {
		Wolmart.$body.addClass('loaded');
		// Touch is enabled?
		$('html').addClass('ontouchstart' in document ? 'touchable' : 'untouchable');

		// Run skeleton and init
		if ($.fn.imagesLoaded && typeof Wolmart.skeleton === 'function') {
			if (wolmart_vars.resource_after_load) {
				Wolmart.call(function () {
					Wolmart.skeleton($('.skeleton-body'), Wolmart.initLayout);
				})
			} else {
				Wolmart.skeleton($('.skeleton-body'), Wolmart.initLayout);
			}
		} else {
			if (wolmart_vars.resource_after_load) {
				Wolmart.call(Wolmart.initLayout);
			} else {
				Wolmart.initLayout();
			}
		}
	})
})(jQuery);