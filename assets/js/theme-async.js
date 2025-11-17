/**
 * Wolmart Theme Async Library
 * 
 * @package Wolmart WordPress theme
 * @version 1.0
 */
'use strict';

(function ($) {
	/**
	 * jQuery easing
	 * 
	 * @since 1.0
	 */
	$.extend($.easing, {
		def: 'easeOutQuad',
		swing: function (x, t, b, c, d) {
			return $.easing[$.easing.def](x, t, b, c, d);
		},
		easeOutQuad: function (x, t, b, c, d) {
			return -c * (t /= d) * (t - 2) + b;
		},
		easeInOutQuart: function (x, t, b, c, d) {
			if ((t /= d / 2) < 1) return c / 2 * t * t * t * t + b;
			return -c / 2 * ((t -= 2) * t * t * t - 2) + b;
		},
		easeOutQuint: function (x, t, b, c, d) {
			return c * ((t = t / d - 1) * t * t * t * t + 1) + b;
		}
	});

	Wolmart.defaults.popup = {
		fixedContentPos: true,
		closeOnBgClick: false,
		removalDelay: 350,
		callbacks: {
			beforeOpen: function () {
				if (this.fixedContentPos) {
					var scrollBarWidth = window.innerWidth - document.body.clientWidth;
					$('.sticky-content.fixed').css('padding-right', scrollBarWidth);
					$('.mfp-wrap').css('overflow', 'hidden auto');
				}
			},
			close: function () {
				if (this.fixedContentPos) {
					$('.mfp-wrap').css('overflow', '');
					$('.sticky-content.fixed').css('padding-right', '');
				}
			}
		},
	}

	Wolmart.defaults.popupPresets = {
		login: {
			type: 'ajax',
			mainClass: "mfp-login mfp-fade",
			tLoading: '<div class="login-popup"><div class="w-loading"><i></i></div></div>',
			preloader: true,
			items: {
				src: wolmart_vars.ajax_url,
			},
			ajax: {
				settings: {
					method: 'post',
					data: {
						action: 'wolmart_account_form',
						nonce: wolmart_vars.nonce
					}
				}, cursor: 'mfp-ajax-cur' // CSS class that will be added to body during the loading (adds "progress" cursor)
			}
		},
		video: {
			type: 'iframe',
			mainClass: "mfp-fade",
			preloader: false,
			closeBtnInside: false
		},
		firstpopup: {
			type: 'inline',
			mainClass: 'mfp-popup-template mfp-newsletter-popup mfp-flip-popup',
			callbacks: {
				beforeClose: function () {
					// if "do not show" is checked
					$('.mfp-wolmart .popup .hide-popup input[type="checkbox"]').prop('checked') && Wolmart.setCookie('hideNewsletterPopup', true, 7);
				}
			}
		},
		popup_template: {
			type: 'ajax',
			mainClass: "mfp-popup-template mfp-flip-popup",
			tLoading: '<div class="popup-template"><div class="w-loading"><i></i></div></div>',
			preloader: true,
			items: {
				src: wolmart_vars.ajax_url,
			},
			ajax: {
				settings: {
					method: 'post',
				}, cursor: 'mfp-ajax-cur' // CSS class that will be added to body during the loading (adds "progress" cursor)
			}
		},
	}

	Wolmart.defaults.slider = {
		a11y: false,
		containerModifierClass: 'slider-container-', // NEW
		slideClass: 'slider-slide',
		wrapperClass: 'slider-wrapper',
		slideActiveClass: 'slider-slide-active',
		slideDuplicateClass: 'slider-slide-duplicate',
		speed: 1000,
	}

	/**
	 * Prevent default handler
	 *
	 * @since 1.0
	 * @param {Event} e
	 * @return {void}
	 */
	Wolmart.preventDefault = function (e) { e.preventDefault() }

	/**
	 * Initialize template's content.
	 * 
	 * @since 1.0
	 * @param {jQuery} $template
	 * @return {void}
	 */
	Wolmart.initTemplate = function ($template) {
		Wolmart.lazyload($template);
		Wolmart.slider($template.find('.slider-wrapper'));
		Wolmart.isotopes($template.find('.grid'));
		Wolmart.shop.initProducts($template);
		Wolmart.countdown($template.find('.countdown'));
		Wolmart.call(function () {
			Wolmart.$window.trigger('wolmart_loadmore');
		}, 300);
		Wolmart.$body.trigger('wolmart_init_tab_template');
	}

	/**
	 * Load template's content.
	 * 
	 * @since 1.0
	 * @param {jQuery} $template
	 * @return {void}
	 */
	Wolmart.loadTemplate = function ($template) {
		var html = '';
		var orignal_split = wolmart_vars.resource_split_tasks;

		// To run carousel immediately
		wolmart_vars.resource_split_tasks = 0;

		$template.children('.load-template').each(function () {
			html += this.text;
		});
		if (html) {
			$template.html(html);
			if (Wolmart.skeleton) {
				Wolmart.skeleton($('.skeleton-body'), function () {
					Wolmart.initTemplate($template);
				});
			} else {
				Wolmart.initTemplate($template);
			}
		}

		wolmart_vars.resource_split_tasks = orignal_split;
	}

	/**
	 * Check if window's width is really resized.
	 * 
	 * @since 1.0
	 * @param {number} timeStamp
	 * @return {boolean}
	 */
	Wolmart.windowResized = function (timeStamp) {
		if (timeStamp == Wolmart.resizeTimeStamp) {
			return Wolmart.resizeChanged;
		}

		if (Wolmart.canvasWidth != (Wolmart.isMobileAndTablet ? window.outerWidth : window.innerWidth)) {
			Wolmart.resizeChanged = true;
		} else {
			Wolmart.resizeChanged = false;
		}

		Wolmart.canvasWidth = Wolmart.isMobileAndTablet ? window.outerWidth : window.innerWidth;
		Wolmart.resizeTimeStamp = timeStamp;

		return Wolmart.resizeChanged;
	}

	/**
	 * Set cookie
	 * 
	 * @since 1.0
	 * @param {string} name Cookie name
	 * @param {string} value Cookie value
	 * @param {number} exdays Expire period
	 * @return {void}
	 */
	Wolmart.setCookie = function (name, value, exdays) {
		var date = new Date();
		date.setTime(date.getTime() + (exdays * 24 * 60 * 60 * 1000));
		document.cookie = name + "=" + value + ";expires=" + date.toUTCString() + ";path=/";
	}

	/**
	 * Get cookie
	 *
	 * @since 1.0
	 * @param {string} name Cookie name
	 * @return {string} Cookie value
	 */
	Wolmart.getCookie = function (name) {
		var n = name + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; ++i) {
			var c = ca[i];
			while (c.charAt(0) == ' ') {
				c = c.substring(1);
			}
			if (c.indexOf(n) == 0) {
				return c.substring(n.length, c.length);
			}
		}
		return "";
	}

	/**
	 * Scroll to given target in given duration.
	 *
	 * @since 1.0
	 * @param {mixed}  target   This can be number or string seletor or jQuery object.
	 * @param {number} duration This can be omitted.
	 * @return {void}
	 */
	Wolmart.scrollTo = function (target, duration) {
		var _duration = typeof duration == 'undefined' ? 0 : duration;
		var offset;

		if (typeof target == 'number') {
			offset = target;
		} else {
			var $target = Wolmart.$(target).closest(':visible');
			if ($target.length) {
				var offset = $target.offset().top;
				var $wpToolbar = $('#wp-toolbar');
				window.innerWidth > 600 && $wpToolbar.length && (offset -= $wpToolbar.parent().outerHeight());
				$('.sticky-content.fix-top.fixed').each(function () {
					offset -= this.offsetHeight;
				})
			}
		}

		$('html,body').stop().animate({ scrollTop: offset }, _duration);
	}

	/**
	 * Scroll to fixed content
	 * 
	 * @since 1.0
	 * @param {number} arg
	 * @param {number} duration
	 * @return {void}
	 */
	Wolmart.scrollToFixedContent = function (arg, duration) {
		var stickyTop = 0,
			toolbarHeight = window.innerWidth > 600 && $('#wp-toolbar').parent().length ? $('#wp-toolbar').parent().outerHeight() : 0;

		$('.sticky-content.fix-top').each(function () {
			if ($(this).hasClass('toolbox-top')) {
				var pt = $(this).css('padding-top').slice();
				if (pt.length > 2) {
					stickyTop -= Number(pt.slice(0, -2));
				}
				return;
			}

			var fixed = $(this).hasClass('fixed');

			stickyTop += $(this).addClass('fixed').outerHeight();

			fixed || $(this).removeClass('fixed');
		})

		Wolmart.scrollTo(arg - stickyTop - toolbarHeight, duration);
	}

	/**
	 * Get value by given param from url
	 *
	 * @since 1.0
	 * @param {string} href
	 * @param {string} name
	 * @return {string} value
	 */
	Wolmart.getUrlParam = function (href, name) {
		var url = document.createElement('a'), s, r;
		url.href = decodeURIComponent(decodeURI(href));
		s = url.search;
		if (s.startsWith('?')) {
			s = s.substr(1);
		}
		var params = {};
		s.split('&').forEach(function (v) {
			var i = v.indexOf('=');
			if (i >= 0) {
				params[v.substr(0, i)] = v.substr(i + 1);
			}
		});
		return params[name] ? params[name] : '';
	}

	/**
	 * Add param to url
	 *
	 * @since 1.0
	 * @param {string} href
	 * @param {string} name
	 * @param {mixed} value
	 * @return {string}
	 */
	Wolmart.addUrlParam = function (href, name, value) {
		var url = document.createElement('a'), s, r;
		href = decodeURIComponent(decodeURI(href));
		url.href = href;
		s = url.search;
		if (0 <= s.indexOf(name + '=')) {
			r = s.replace(new RegExp(name + '=[^&]*'), name + '=' + value);
		} else {
			r = (s.length && 0 <= s.indexOf('?')) ? s : '?';
			r.endsWith('?') || (r += '&');
			r += name + '=' + value;
		}
		return encodeURI(href.replace(s, '') + r.replace(/&+/, '&'));
	}

	/**
	 * Remove param from url
	 *
	 * @since 1.0
	 * @param {string} href
	 * @param {string} name
	 * @return {string}
	 */
	Wolmart.removeUrlParam = function (href, name) {
		var url = document.createElement('a'), s, r;
		href = decodeURIComponent(decodeURI(href));
		url.href = href;
		s = url.search;
		if (0 <= s.indexOf(name + '=')) {
			r = s.replace(new RegExp(name + '=[^&]*'), '').replace(/&+/, '&').replace('?&', '?');
			r.endsWith('&') && (r = r.substr(0, r.length - 1));
			r.endsWith('?') && (r = r.substr(0, r.length - 1));
			r = r.replace('&&', '&');
		} else {
			r = s;
		}
		return encodeURI(href.replace(s, '') + r);
	}

	/**
	 * Show More
	 *
	 * @since 1.0
	 * @param {string} selector
	 */
	Wolmart.showMore = function (selector) {
		Wolmart.$(selector).after('<div class="w-loading relative"><i></i></div>');
	}

	/**
	 * Hide more
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.hideMore = function (selector) {
		Wolmart.$(selector).children('.w-loading').remove();
	}

	/**
	 * Start count to number
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.countTo = function (selector) {
		if ($.fn.countTo) {
			Wolmart.$(selector).each(function () {
				var $this = $(this);
				setTimeout(function () {
					var options = {
						onComplete: function () {
							$this.addClass('complete');
						}
					};

					$this.data('duration') && (options.speed = $this.data('duration'));
					$this.data('from-value') && (options.from = $this.data('from-value'));
					$this.data('to-value') && (options.to = $this.data('to-value'));
					$this.countTo(options);
				}, 300);
			});
		}
	}

	/**
	 * Start countdown
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @param {object} options
	 * @return {void}
	 */
	Wolmart.countdown = function (selector, options) {
		if ($.fn.countdown) {
			Wolmart.$(selector).each(function () {
				var $this = $(this),
					untilDate = $this.attr('data-until'),
					compact = $this.attr('data-compact'),
					dateFormat = (!$this.attr('data-format')) ? 'DHMS' : $this.attr('data-format'),
					newLabels = (!$this.attr('data-labels-short')) ? wolmart_vars.countdown.labels : wolmart_vars.countdown.labels_short,
					newLabels1 = (!$this.attr('data-labels-short')) ? wolmart_vars.countdown.label1 : wolmart_vars.countdown.label1_short;

				$this.data('countdown') && $this.countdown('destroy');

				$this.countdown($.extend(
					$this.hasClass('user-tz') ?
						{
							until: (!$this.attr('data-relative')) ? new Date(untilDate) : untilDate,
							format: dateFormat,
							padZeroes: true,
							compact: compact,
							compactLabels: [' y', ' m', ' w', ' days, '],
							timeSeparator: ' : ',
							labels: newLabels,
							labels1: newLabels1,
							serverSync: new Date($(this).attr('data-time-now'))
						} : {
							until: (!$this.attr('data-relative')) ? new Date(untilDate) : untilDate,
							format: dateFormat,
							padZeroes: true,
							compact: compact,
							compactLabels: [' y', ' m', ' w', ' days, '],
							timeSeparator: ' : ',
							labels: newLabels,
							labels1: newLabels1
						},
					options)
				);
			});
		}
	}

	/**
	 * Initialize Parallax Background
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.parallax = function (selector, options) {
		if ($.fn.themePluginParallax) {
			Wolmart.$(selector).each(function () {
				var $this = $(this);
				$this.themePluginParallax(
					$.extend(true, Wolmart.parseOptions($this.attr('data-parallax-options')), options)
				);
			});
		}
	}

	// Show loading overlay when $.fn.block is called
	var funcBlock = $.fn.block;
	$.fn.block = function (opts) {
		if (Wolmart.status == 'complete') { // To prevent single product widget's found variation blocking while page loading
			this.append('<div class="w-loading"><i></i></div>');
			funcBlock.call(this, opts);
		}
		return this;
	}

	// Hide loading overlay when $.fn.block is called
	var funcUnblock = $.fn.unblock;
	$.fn.unblock = function (opts) {
		if (Wolmart.status == 'complete') { // To prevent single product widget's found variation blocking while page loading
			funcUnblock.call(this, opts);
			this.hasClass('processing') || this.parents('.processing').length || this.children('.w-loading').remove();
			Wolmart.shop.initAlertAction();
		}
		return this;
	}

	/**
	 * Initialize Sticky Content
	 * 
	 * @class StickyContent
	 * @since 1.0
	 * @param {string, Object} selector
	 * @param {Object} options
	 * @return {void}
	 */
	Wolmart.stickyContent = (function () {
		function StickyContent($el, options) {
			return this.init($el, options);
		}

		function refreshAll() {
			Wolmart.$window.trigger('sticky_refresh.wolmart', {
				index: 0,
				offsetTop: window.innerWidth > 600 && $('#wp-toolbar').length && $('#wp-toolbar').parent().is(':visible') ? $('#wp-toolbar').parent().outerHeight() : 0
			});
		}

		function refreshAllSize(e) {
			if (!e || Wolmart.windowResized(e.timeStamp)) {
				Wolmart.$window.trigger('sticky_refresh_size.wolmart');
				Wolmart.requestFrame(refreshAll);
			}
		}

		StickyContent.prototype.init = function ($el, options) {
			this.$el = $el;
			this.options = $.extend(true, {}, Wolmart.defaults.sticky, options, Wolmart.parseOptions($el.attr('data-sticky-options')));

			Wolmart.$window
				.on('sticky_refresh.wolmart', this.refresh.bind(this))
				.on('sticky_refresh_size.wolmart', this.refreshSize.bind(this));
		}

		StickyContent.prototype.refreshSize = function (e) {
			var beWrap = window.innerWidth >= this.options.minWidth && window.innerWidth <= this.options.maxWidth;

			this.scrollPos = window.pageYOffset; // issue: heavy js performance : 30.7ms
			if (typeof this.top == 'undefined') {
				this.top = this.options.top;
			}

			if (window.innerWidth >= 768 && this.getTop) {
				this.top = this.getTop();
			} else if (!this.options.top) {
				this.top = this.isWrap ?
					this.$el.parent().offset().top :
					this.$el.offset().top + this.$el[0].offsetHeight;

				// if sticky header has toggle dropdown menu, increase top
				if (this.$el.find('.toggle-menu.show-home').length && this.$el.find('.toggle-menu .dropdown-box').length) {
					this.top += this.$el.find('.toggle-menu .dropdown-box')[0].offsetHeight;
				}
			}

			if (!this.isWrap) {
				beWrap && this.wrap();
			} else {
				beWrap || this.unwrap();
			}

			e && Wolmart.requestTimeout(this.refreshSize.bind(this), 50);
		}

		StickyContent.prototype.wrap = function () {
			this.$el.wrap('<div class="sticky-content-wrapper"></div>');
			this.isWrap = true;
		}

		StickyContent.prototype.unwrap = function () {
			this.$el.unwrap('.sticky-content-wrapper');
			this.isWrap = false;
		}

		StickyContent.prototype.refresh = function (e, data) {
			var pageYOffset = window.pageYOffset + data.offsetTop; // issue: heavy js performance, 6.7ms
			var $el = this.$el;

			this.refreshSize();

			// Make sticky
			if (pageYOffset > this.top && this.isWrap) {

				// calculate height
				this.height = $el[0].offsetHeight;
				$el.hasClass('fixed') || $el.parent().css('height', this.height + 'px');

				// update sticky order
				if ($el.hasClass('fix-top')) {
					$el.css('margin-top', data.offsetTop + 'px');
					this.zIndex = this.options.max_index - data.index;
				} else if ($el.hasClass('fix-bottom')) {
					$el.css('margin-bottom', data.offsetBottom + 'px');
					this.zIndex = this.options.max_index - data.index;
				} else {
					$el.css({ 'transition': 'opacity .5s', 'z-index': this.zIndex });
				}

				// update sticky status
				if (this.options.scrollMode) {
					if (this.scrollPos >= pageYOffset && $el.hasClass('fix-top') ||
						this.scrollPos <= pageYOffset && $el.hasClass('fix-bottom')) {

						$el.addClass('fixed');
						this.onFixed && this.onFixed();
					} else {
						$el.removeClass('fixed').css('margin-top', '').css('margin-bottom', '');
						this.onUnfixed && this.onUnfixed();
					}
					this.scrollPos = pageYOffset;
				} else {
					$el.addClass('fixed');
					this.onFixed && this.onFixed();
				}

				// stack offset
				if ($el.hasClass('fixed')) {
					if ($el.hasClass('fix-top')) {
						data.offsetTop += $el[0].offsetHeight;
					} else if ($el.hasClass('fix-bottom')) {
						data.offsetBottom += $el[0].offsetHeight;
					}
				}
			} else {
				$el.parent().css('height', '');
				$el.removeClass('fixed').css({ 'margin-top': '', 'margin-bottom': '', 'z-index': '' });
				this.onUnfixed && this.onUnfixed();
			}
		}

		Wolmart.$window.on('wolmart_complete', function () {
			window.addEventListener('scroll', refreshAll, { passive: true });
			Wolmart.$window.on('resize', refreshAllSize);
			setTimeout(function () {
				refreshAllSize();
			}, 1000);
		})

		return function (selector, options) {
			Wolmart.$(selector).each(function () {
				var $this = $(this);
				$this.data('sticky-content') || $this.data('sticky-content', new StickyContent($this, options));
			})
		}
	})()


	/**
	 * Register events for alert
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.alert = function (selector) {
		Wolmart.$body.on('click', selector + ' .btn-close', function (e) {
			e.preventDefault();
			$(this).closest(selector).fadeOut(function () {
				$(this).remove();
			});
		});
	}

	/**
	 * Register events for accordion
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.accordion = function (selector) {
		Wolmart.$body.on('click', selector, function (e) {
			var $this = $(this),
				$body = $this.closest('.card'),
				$parent = $this.closest('.accordion');

			var link = $this.attr('href');
			if ('#' == link) {
				$body = $body.children(".card-body");
			} else {
				$body = $body.find('#' == link[0] ? $this.attr('href') : '#' + $this.attr('href'));
			}
			if (!$body.length) {
				return;
			}
			e.preventDefault();

			if (!$parent.find(".collapsing").length && !$parent.find(".expanding").length) {
				if ($body.hasClass('expanded')) {
					$parent.hasClass('radio-type') || slideToggle($body);
				} else if ($body.hasClass('collapsed')) {
					if ($parent.find('.expanded').length > 0) {
						if (Wolmart.isIE) {
							slideToggle($parent.find('.expanded'), function () {
								slideToggle($body);
							});
						} else {
							slideToggle($parent.find('.expanded'));
							slideToggle($body);
						}
					} else {
						slideToggle($body);
					}
				}
			}
		});

		// define slideToggle method
		var slideToggle = function ($wrap, callback) {
			var $header = $wrap.closest('.card').find(selector);
			if ($wrap.hasClass("expanded")) {
				$header.removeClass("collapse").addClass("expand");
				$wrap.addClass("collapsing").slideUp(300, function () {
					$wrap.removeClass("expanded collapsing").addClass("collapsed");
					callback && callback();
				});
			} else if ($wrap.hasClass("collapsed")) {
				$header.removeClass("expand").addClass("collapse");
				$wrap.addClass("expanding").slideDown(300, function () {
					$wrap.removeClass("collapsed expanding").addClass("expanded");
					callback && callback();
				});
			}
		};
	}

	/**
	 * Register events for tab
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.tab = function (selector) {

		Wolmart.$body
			// tab nav link
			.on('click', selector + ' .nav-link', function (e) {
				var $link = $(this);

				// if tab is loading, return
				if ($link.closest(selector).hasClass('loading')) {
					return;
				}

				// get href
				var href = 'SPAN' == this.tagName ? $link.data('href') : $link.attr('href');

				// get panel
				var $panel;
				if ('#' == href) {
					$panel = $link.closest('.nav').siblings('.tab-content').children('.tab-pane').eq($link.parent().index());
				} else {
					$panel = $(('#' == href.substring(0, 1) ? '' : '#') + href);
				}
				if (!$panel.length) {
					return;
				}

				e.preventDefault();

				var $activePanel = $panel.parent().children('.active');


				if ($link.hasClass("active") || !href) {
					return;
				}
				// change active link
				$link.parent().parent().find('.active').removeClass('active');
				$link.addClass('active');

				Wolmart.loadTemplate($panel);
				Wolmart.slider($panel.find('.slider-wrapper'));
				$activePanel.removeClass('in active');
				$panel.addClass('active in');
				Wolmart.refreshLayouts();
			})
	}

	/**
	 * Playable video
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.playableVideo = function (selector) {
		$(selector + ' .video-play').on('click', function (e) {
			var $video = $(this).closest(selector);
			if ($video.hasClass('playing')) {
				$video.removeClass('playing')
					.addClass('paused')
					.find('video')[0].pause();
			} else {
				$video.removeClass('paused')
					.addClass('playing')
					.find('video')[0].play();
			}
			e.preventDefault();
		});
		$(selector + ' video').on('ended', function () {
			$(this).closest('.post-video').removeClass('playing');
		});
	}


	/**
	 * Run appear animation
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.appearAnimate = function (selector) {
		var appearClass = typeof selector == 'string' && selector.indexOf('elementor-invisible') > 0 ? 'elementor-invisible' : 'appear-animate';

		Wolmart.$(selector).each(function () {
			var el = this;
			Wolmart.appear(el, function () {
				if (el.classList.contains(appearClass) && !el.classList.contains('appear-animation-visible')) {
					var settings = Wolmart.parseOptions(el.getAttribute('data-settings')),
						duration = 1000;

					if (el.classList.contains('animated-slow')) {
						duration = 2000;
					} else if (el.classList.contains('animated-fast')) {
						duration = 750;
					}

					Wolmart.call(function () {
						el.style['animation-duration'] = duration + 'ms';
						el.style['animation-delay'] = settings._animation_delay + 'ms';
						el.style['transition-property'] = 'visibility, opacity';
						el.style['transition-duration'] = '0s';
						el.style['transition-delay'] = settings._animation_delay + 'ms';

						var animation_name = settings.animation || settings._animation || settings._animation_name;
						animation_name && el.classList.add(animation_name);

						el.classList.add('appear-animation-visible');
						setTimeout(
							function () {
								el.style['transition-property'] = '';
								el.style['transition-duration'] = '';
								el.style['transition-delay'] = '';
							},
							settings._animation_delay ? settings._animation_delay + 500 : 500
						);
					});
				}
			});
		});

		if (typeof elementorFrontend == 'object') {
			Wolmart.$window.trigger('resize.waypoints');
		}
	}

	var videoIndex = {
		youtube: 'youtube.com',
		vimeo: 'vimeo.com/',
		gmaps: '//maps.google.',
		hosted: ''
	}

	/**
	 * Initialize popups
	 *
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initPopups = function () {

		// Register "Play Video" Popup
		Wolmart.$body.on('click', '.btn-video-iframe', function (e) {
			e.preventDefault();
			Wolmart.popup({
				items: {
					src: '<video src="' + $(this).attr('href') + '" autoplay loop controls>',
					type: 'inline'
				},
				mainClass: 'mfp-video-popup'
			}, 'video');
		});

		// Close mangific popup by mousedown on outside of the content
		function closePopupByClickBg(e) {
			if (!$(e.target).closest('.mfp-content').length || $(e.target).hasClass('mfp-content')) {
				$.magnificPopup.instance.close();
			}
		}

		Wolmart.$body.on('mousedown', '.mfp-wrap', closePopupByClickBg);
		if ('ontouchstart' in document) {
			document.body.addEventListener('touchstart', closePopupByClickBg, { passive: true });
		}

		/**
		 * Open first popup
		 * 
		 * @since 1.0
		 */
		function openFirstPopup($this) {
			var options = Wolmart.parseOptions($this.attr('data-popup-options')),
				popupParams = options.popup_params;

			if (Wolmart.getCookie('hideNewsletterPopup')) {
				return;
			}

			if (popupParams.show_on == 'page_load') {
				setTimeout(function () {
					openPopup($this, options);
				}, 1000 * popupParams.delay);
			} else if (popupParams.show_on == 'page_scroll') {
				var previousScroll = 0;
				var scrolltrigger = popupParams.scroll_amount / 100;
				window.addEventListener('scroll', pageScrollTrigger);

				function pageScrollTrigger() {
					var currentScroll = $(this).scrollTop();
					var docheight = $(document).height();
					var winheight = $(window).height();
					if (popupParams.scroll_dir == 'down' && currentScroll > previousScroll && (currentScroll / (docheight - winheight)) > scrolltrigger ||
						popupParams.scroll_dir == 'up' && currentScroll < previousScroll) {
						openPopup($this, options);
						window.removeEventListener('scroll', pageScrollTrigger);
					}
					previousScroll = currentScroll;
				}
			} else if (popupParams.show_on == 'scroll_element') {
				if ($(popupParams.scroll_element_selector).length) {
					window.addEventListener('scroll', scrollElementTrigger);

					function scrollElementTrigger() {
						var top = $(popupParams.scroll_element_selector).offset().top;
						var bottom = $(popupParams.scroll_element_selector).offset().top + $(popupParams.scroll_element_selector).outerHeight();
						var toBottom = $(window).scrollTop() + $(window).innerHeight();
						var toTop = $(window).scrollTop();

						if ((toBottom > top) && (toTop < bottom)) {
							openPopup($this, options);
							window.removeEventListener('scroll', scrollElementTrigger);
						}
					}
				}
			} else if (popupParams.show_on == 'click_counts') {
				var clicks = 0,
					maxClicks = popupParams.click_count;
				window.addEventListener('click', clickTrigger);

				function clickTrigger() {
					clicks++;
					if (clicks >= maxClicks) {
						openPopup($this, options);
						window.removeEventListener('click', clickTrigger);
					}
				}
			} else if (popupParams.show_on == 'click_element') {
				if ($(popupParams.click_element_selector).length) {
					$(popupParams.click_element_selector).bind('click', clickElementTrigger);
				}

				function clickElementTrigger(e) {
					e.preventDefault();
					openPopup($this, options);
				}
			} else {
				window.addEventListener('mouseout', exitTrigger);

				function exitTrigger(e) {
					if (!e.toElement && !e.relatedTarget) {
						openPopup($this, options);
						window.removeEventListener('mouseout', exitTrigger);
					}
				}
			}

			function openPopup($self, options) {
				var removalDelay = typeof options.popup_duration == 'undefined' ? 350 : parseInt(options.popup_duration);

				$this.imagesLoaded(function () {
					Wolmart.popup({
						mainClass: 'mfp-fade mfp-wolmart mfp-wolmart-' + options.popup_id,
						items: {
							src: $this.get(0)
						},
						removalDelay: removalDelay,
						callbacks: {
							open: function () {
								this.content.css({ 'animation-duration': options.popup_duration, 'animation-timing-function': 'linear' });
								if (Wolmart.$body.hasClass('vcwb')) {
									this.content.attr('data-vce-animate', 'vce-o-animate--' + options.popup_animation);
									this.content.attr('data-vcv-o-animated', 'true');
								}
								else {
									this.content.addClass(options.popup_animation + ' animated');
								}

								$('#wolmart-popup-' + options.popup_id).css('display', '');

								if (this.container.find('.slider-wrapper').length) {
									var $slider = this.container.find('.slider-wrapper');
									$slider.each(function () {
										if ($(this).data('slider')) {
											$(this).data('slider').update();
										}
									})
								}
							}
						}
					}, 'firstpopup');
				});
			}
		}

		// Open first popup
		$('body > .popup').each(function (e) {
			var $this = $(this);
			if ($this.attr('data-popup-options')) {
				openFirstPopup($this);
			}
		});

		// Popup on click event
		Wolmart.$body.on('click', '.show-popup', function (e) {

			e.preventDefault();

			var id = -1;
			for (var className of this.classList) {
				className && className.startsWith('popup-id-') && (id = className.substr(9));
			}

			Wolmart.popup({
				mainClass: 'mfp-wolmart mfp-wolmart-' + id,
				ajax: {
					settings: {
						data: {
							action: 'wolmart_print_popup',
							nonce: wolmart_vars.nonce,
							popup_id: id
						}
					}
				},
				callbacks: {
					afterChange: function () {
						this.container.html('<div class="mfp-content"></div><div class="mfp-preloader"><div class="popup-template"><div class="w-loading"><i></i></div></div></div>');
						this.contentContainer = this.container.children('.mfp-content');
						this.preloader = false;
					},
					beforeClose: function () {
						this.container.empty();
					},
					ajaxContentAdded: function () {
						var self = this,
							$popupContainer = this.container.find('.popup'),
							options = JSON.parse($popupContainer.attr('data-popup-options'));

						self.contentContainer.next('.mfp-preloader').css('max-width', $popupContainer.css('max-width'));
						setTimeout(function () {
							self.contentContainer.next('.mfp-preloader').remove();
						}, 10000);

						// $('html').css('overflow-y', 'hidden');
						// $('body').css('overflow-x', 'visible');
						// $('.mfp-wrap').css('overflow', 'hidden auto');
						// $('.sticky-content.fixed').css('margin-right', window.innerWidth - document.body.clientWidth);

						this.container.css({ 'animation-duration': options.popup_duration, 'animation-timing-function': 'linear' });
						if (Wolmart.$body.hasClass('vcwb')) {
							this.container.attr('data-vce-animate', 'vce-o-animate--' + options.popup_animation);
							this.container.attr('data-vcv-o-animated', 'true');
						}
						else {
							this.container.addClass(options.popup_animation + ' animated');
						}

						$('#wolmart-popup-' + id).css('display', '');
					}
				}
			}, 'popup_template');
		})
	}

	/**
	 * Initialize scroll to top button
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initScrollTopButton = function () {
		// register scroll top button
		var domScrollTop = $('#scroll-top:not(.mobile-item)').get(0);
		if (domScrollTop) {
			Wolmart.$body.on('click', '#scroll-top, .scroll-top', function (e) {
				Wolmart.scrollTo(0);
				e.preventDefault();
			})

			function _refreshScrollTop() {
				if (window.pageYOffset > 200) { // issue: heavy js performance, 8.3ms
					domScrollTop.classList.add('show');

					// Show scroll position percent in scroll top button
					var d_height = $(document).height(),
						w_height = $(window).height(),
						c_scroll_pos = $(window).scrollTop();

					var perc = c_scroll_pos / (d_height - w_height) * 214;

					if ($('#progress-indicator').length > 0) {
						$('#progress-indicator').css('stroke-dasharray', perc + ', 400');
					}
				} else {
					domScrollTop.classList.remove('show');
				}
			}

			Wolmart.call(_refreshScrollTop, 500);
			window.addEventListener('scroll', _refreshScrollTop, { passive: true });
		}
	}

	/**
	 * Initialize scroll to.
	 *
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initScrollTo = function () {

		// Scroll to hash target
		Wolmart.scrollTo(Wolmart.hash);

		// Scroll to target by click button.
		Wolmart.$body.on('click', '.scroll-to', function (e) {
			var target = $(this).attr('href').replace(location.origin + location.pathname, '');
			if (target.startsWith('#') && target.length > 1) {
				e.preventDefault();
				Wolmart.scrollTo(target);
			}
		})
	}

	/**
	 * Initialize contact forms.
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initContactForms = function () {
		$('.wpcf7-form [aria-required="true"]').prop('required', true);
	}


	/**
	 * Initialize search form.
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initSearchForm = function () {
		var $search = $('.hs-toggle');

		Wolmart.$body.on('click', '.hs-toggle .search-toggle', Wolmart.preventDefault)

		if ('ontouchstart' in document) {
			if (Wolmart.$body.find('.search-fullscreen').length) {
				Wolmart.$body.on('click', '.hs-toggle .search-toggle, .floating-icons-wrapper .search-toggle', function (e) {
					e.preventDefault();
					var showAlready = false;
					Wolmart.$body.find('.search-fullscreen').each(function () {
						var $this = $(this),
							height = $this.height();
						if (height > 0 && !showAlready) {
							showAlready = true;
							$this.parent().height($this.height());
							$this.addClass('active-ready');
							var $slider = $this.find('.slider-wrapper').data('slider');
							if ($slider) {
								$slider.update();
							}
						}
					});
				});
			} else {
				$search.find('.search-toggle').on('click', function (e) {
					$search.toggleClass('show');
				});
				Wolmart.$body.on('click', function (e) {
					$search.removeClass('show');
				})
				$search.on('click', function (e) {
					Wolmart.preventDefault(e);
					e.stopPropagation();
				})
			}
		} else {
			$search.find('.form-control').on('focusin', function (e) {
				$search.addClass('show');
			}).on('focusout', function (e) {
				$search.removeClass('show');
			});
		}
	}

	/**
	 * Compatibility with Elementor
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initElementor = function () {
		if ('undefined' != typeof elementorFrontend) {
			// Compatibility with Elementor Counter Widget
			$('.elementor-counter-number').each(function () {
				var el = this;
				Wolmart.appear(el, function () {
					var $this = $(el),
						data = $this.data(),
						decimalDigits = data.toValue.toString().match(/\.(.*)/);

					if (decimalDigits) {
						data.rounding = decimalDigits[1].length;
					}

					$this.numerator(data);
				}, { alwaysObserve: false });
			});
		}
	}


	/**
	 * Compatibility with Vendor plugins
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initVendorCompatibility = function () {

		// Dokan / 
		Wolmart.$body.on('keydown', '.store-search-input', function (e) {
			if (e.keyCode == 13) {
				setTimeout(function () {
					$('#dokan-store-listing-filter-form-wrap #apply-filter-btn').trigger('click');
				}, 150);
			}
		});

		// WC Marketplace
		Wolmart.$body
			.on('click', '.wcmp-report-abouse-wrapper .close', function (e) {
				$(".wcmp-report-abouse-wrapper #report_abuse_form_custom").fadeOut(100);
			})
			.on('click', '.wcmp-report-abouse-wrapper #report_abuse', function (e) {
				$(".wcmp-report-abouse-wrapper #report_abuse_form_custom").fadeIn(100);
			});

		$('select#rating').prev('p.stars').prevAll('p.stars').remove();

		// Single product / summary / "more products" button
		Wolmart.$body.on('click', '.goto_more_offer_tab', function (e) {
			e.preventDefault();
			if (!$('.singleproductmultivendor_tab').hasClass('active')) {
				$('.singleproductmultivendor_tab a, #tab_singleproductmultivendor').trigger('click');
			}
			if ($('.woocommerce-tabs').length > 0) {
				$('html, body').animate({
					scrollTop: $(".woocommerce-tabs").offset().top - 120
				}, 1500);
			}
		});
	}

	/**
	 * Initialize floating elements
	 * 
	 * @since 1.0
	 * @param {string|jQuery} selector
	 * @return {void}
	 */
	Wolmart.initFloatingElements = function (selector) {
		if ($.fn.parallax) {
			Wolmart.$(selector, '.floating-wrapper').each(function (e) {
				var $this = $(this);
				if ($this.data('parallax')) {
					$this.parallax('disable');
					$this.removeData('parallax');
					$this.removeData('options');
				}
				$this.children('figure, .elementor-widget-container').addClass('layer').attr('data-depth', $this.attr('data-child-depth'));
				$this.parallax($this.data('options'));
			});
		}

	}

	/**
	 * Initialize advanced motions
	 *
	 * @since 1.0
	 * @param {string} selector
	 * @param {string} action
	 * @return {void}
	 */
	Wolmart.initAdvancedMotions = function (selector, action) {
		if (Wolmart.isMobile) {
			return;
		}

		if (typeof skrollr == 'undefined') {
			return;
		}

		Wolmart.$(selector).data({ 'bottom-top': '', 'top-bottom': '', 'center': '', 'center-top': '', 'center-bottom': '' });
		Wolmart.$(selector).removeAttr('data-bottom-top data-top-bottom data-center data-center-top data-center-bottom');

		if (typeof skrollr.get != "undefined") {
			if (skrollr.get() && typeof skrollr.get().destroy != "undefined")
				skrollr.get().destroy();
		}

		if (action == 'destroy') {
			Wolmart.$(selector).data({ 'plugin': '', 'options': '' });
			return;
		}

		Wolmart.$(selector, '.wolmart-motion-effect-widget').each(function () {
			var $this = $(this);

			if ($this.hasClass('wolmart-scroll-effect-widget')) {

				var motions = JSON.parse($this.attr('data-wolmart-scroll-effect-settings'));
				var transforms = {};

				for (var key in motions) {
					var start = '', end = '';
					if (key == 'Vertical') {
						if (motions[key].direction == 'up') {
							start = motions[key].speed + 'vh';
							end = -motions[key].speed + 'vh';
						} else {
							start = -motions[key].speed + 'vh';
							end = motions[key].speed + 'vh';
						}
						transforms.translateY = [start, end];
					} else if (key == 'Horizontal') {
						if (motions[key].direction == 'left') {
							start = motions[key].speed + 'vw';
							end = -motions[key].speed + 'vw';
						} else {
							start = -motions[key].speed + 'vw';
							end = motions[key].speed + 'vw';
						}
						transforms.translateX = [start, end];
					} else if (key == 'Transparency') {
						if (motions[key].direction == 'in') {
							start = (10 - motions[key].speed) * 10 + '%';
							end = '100%';
						} else {
							start = motions[key].speed + '%';
							end = '0%';
						}
						transforms.opacity = [start, end];
					} else if (key == 'Rotate') {
						if (motions[key].direction == 'left') {
							start = 0 + 'deg';
							end = -motions[key].speed * 36 + 'deg';
						} else {
							start = 0 + 'deg';
							end = motions[key].speed * 36 + 'deg';
						}
						transforms.rotate = [start, end];
					} else if (key == 'Scale') {
						if (motions[key].direction == 'in') {
							start = 1 - motions[key].speed / 10;
							end = 1;
						} else {
							start = 1 + motions[key].speed / 10;
							end = 1;
						}
						transforms.scale = [start, end];
					}
				}
				var bottom_top = '', bottom_top_opacity = '',
					top_bottom = '', top_bottom_opacity = '',
					center = '', center_opacity = '',
					center_top = '', center_top_opacity = '',
					center_bottom = '', center_bottom_opacity = '';

				if (typeof transforms.translateY != 'undefined' && typeof transforms.translateX != 'undefined'
					&& transforms.translateY[2] == transforms.translateX[2]) {
					transforms.translate = [transforms.translateX[0] + ',' + transforms.translateY[0], transforms.translateX[1] + ',' + transforms.translateY[1], transforms.translateY[2]];
					delete transforms.translateX;
					delete transforms.translateY;
				}

				for (var transform in transforms) {
					if (motions.viewport == 'centered') {
						if (transform == 'opacity') {
							bottom_top_opacity += 'opacity:' + transforms[transform][0] + ';';
							center_opacity += 'opacity:' + transforms[transform][1] + ';';
						} else {
							if (bottom_top) {
								bottom_top += ' ' + transform + '(' + transforms[transform][0] + ')';
							} else {
								bottom_top += transform + '(' + transforms[transform][0] + ')';
							}
							if (center) {
								center += ' ' + transform + '(' + transforms[transform][1] + ')';
							} else {
								center += transform + '(' + transforms[transform][1] + ')';
							}
						}
					} else if (motions.viewport == 'top_bottom') {
						if (transform == 'opacity') {
							bottom_top_opacity += 'opacity:' + transforms[transform][0] + ';';
							top_bottom_opacity += 'opacity:' + transforms[transform][1] + ';';
						} else {
							if (bottom_top) {
								bottom_top += ' ' + transform + '(' + transforms[transform][0] + ')';
							} else {
								bottom_top += transform + '(' + transforms[transform][0] + ')';
							}
							if (top_bottom) {
								top_bottom += ' ' + transform + '(' + transforms[transform][1] + ')';
							} else {
								top_bottom += transform + '(' + transforms[transform][1] + ')';
							}
						}
					} else if (motions.viewport == 'center_top') {
						if (transform == 'opacity') {
							bottom_top_opacity += 'opacity:' + transforms[transform][0] + ';';
							center_top_opacity += 'opacity:' + transforms[transform][1] + ';';
						} else {
							if (bottom_top) {
								bottom_top += ' ' + transform + '(' + transforms[transform][0] + ')';
							} else {
								bottom_top += transform + '(' + transforms[transform][0] + ')';
							}
							if (center_top) {
								center_top += ' ' + transform + '(' + transforms[transform][1] + ')';
							} else {
								center_top += transform + '(' + transforms[transform][1] + ')';
							}
						}
					} else if (motions.viewport == 'center_bottom') {
						if (transform == 'opacity') {
							bottom_top_opacity += 'opacity:' + transforms[transform][0] + ';';
							center_bottom_opacity += 'opacity:' + transforms[transform][1] + ';';
						} else {
							if (bottom_top) {
								bottom_top += ' ' + transform + '(' + transforms[transform][0] + ')';
							} else {
								bottom_top += transform + '(' + transforms[transform][0] + ')';
							}
							if (center_bottom) {
								center_bottom += ' ' + transform + '(' + transforms[transform][1] + ')';
							} else {
								center_bottom += transform + '(' + transforms[transform][1] + ')';
							}
						}
					}
				}

				bottom_top = bottom_top ? ('transform: ' + bottom_top + ';' + bottom_top_opacity) : bottom_top_opacity;
				top_bottom = top_bottom ? ('transform: ' + top_bottom + ';' + top_bottom_opacity) : top_bottom_opacity;
				center = center ? ('transform: ' + center + ';' + center_opacity) : center_opacity;
				center_top = center_top ? ('transform: ' + center_top + ';' + center_top_opacity) : center_top_opacity;
				center_bottom = center_bottom ? ('transform: ' + center_bottom + ';' + center_bottom_opacity) : center_bottom_opacity;

				if ($this.hasClass('elementor-element')) {
					$this = $this.children('.elementor-widget-container');
				}

				bottom_top && $this.attr('data-bottom-top', bottom_top);
				top_bottom && $this.attr('data-top-bottom', top_bottom);
				center && $this.attr('data-center', center);
				center_top && $this.attr('data-center-top', center_top);
				center_bottom && $this.attr('data-center-bottom', center_bottom);
			}
		})

		if (typeof skrollr.init != 'function') {
			return;
		}

		if (Wolmart.$(selector, '.wolmart-motion-effect-widget').length) {
			Wolmart.skrollr_id = skrollr.init({ forceHeight: false });
		}
	}

	/**
	 * Initialize video player
	 * 
	 * @since 1.0
	 * @param selector 
	 * @return {void}
	 */
	Wolmart.initVideoPlayer = function (selector) {
		if (typeof selector == 'undefined') {
			selector = '.btn-video-player';
		}
		Wolmart.$(selector).on('click', function (e) {
			var video_banner = $(this).closest('.video-banner');
			if (video_banner.length && video_banner.find('video').length) {
				var video = video_banner.find('video');
				video = video[0];

				if (video_banner.hasClass('playing')) {
					video_banner.removeClass('playing').addClass('paused');
					video.pause();
				} else {
					video_banner.removeClass('paused').addClass('playing');
					video.play();
				}
			}

			if (video_banner.find('.parallax-background').length > 0) {
				video_banner.find('.parallax-background').css('z-index', '-1');
			}
			e.preventDefault();
		})
		Wolmart.$(selector).closest('.video-banner').find('video').on('playing', function () {
			$(this).closest('.video-banner').removeClass('paused').addClass('playing');
		})
		Wolmart.$(selector).closest('.video-banner').find('video').on('ended', function () {
			$(this).closest('.video-banner').removeClass('playing').addClass('paused');
		})
	}

	/**
	 * Initialize ajax load post
	 *
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initAjaxLoadPost = (function () {
		/**
		 * Wolmart Ajax Filter
		 *
		 * @class AjaxLoadPost
		 * @since 1.0
		 * - Ajax load for products and posts in archive pages and widgets
		 * - Ajax filter products and posts
		 * - Load more by button or infinite scroll
		 * - Ajax pagination
		 * - Compatibility with YITH WooCommerce Ajax Navigation
		 */
		var AjaxLoadPost = {
			isAjaxShop: wolmart_vars.shop_ajax ? $(document.body).hasClass('wolmart-archive-product-layout') : false,
			isAjaxBlog: wolmart_vars.blog_ajax ? $(document.body).hasClass('wolmart-archive-post-layout') : false,
			scrollWrappers: false,

			/**
			 * Initialize
			 *
			 * @since 1.0
			 * @return {void}
			 */
			init: function () {

				if (AjaxLoadPost.isAjaxShop) {
					Wolmart.$body
						.on('click', '.widget_product_categories a', this.filterByCategory)				// Product Category
						.on('click', '.widget_product_tag_cloud a', this.filterByLink)					// Product Tag Cloud
						.on('click', '.wolmart-price-filter a', this.filterByLink)						// Wolmart - Price Filter
						.on('click', '.woocommerce-widget-layered-nav a', this.filterByLink)			// Filter Products by Attribute
						.on('click', '.widget_price_filter .button', this.filterByPrice)				// Filter Products by Price
						.on('submit', '.wolmart-price-range', this.filterByPriceRange)					// Filter Products by Price Range
						.on('click', '.widget_rating_filter a', this.filterByLink)						// Filter Products by Rating
						.on('click', '.filter-clean', this.filterByLink)								// Reset Filter
						.on('click', '.toolbox-show-type .btn-showtype', this.changeShowType)			// Change Show Type
						.on('change', '.toolbox-show-count .count', this.changeShowCount)				// Change Show Count
						.on('click', '.yith-woo-ajax-navigation a', this.saveLastYithAjaxTrigger)       // Compatibility with YITH ajax navigation
						.on('change', '.sidebar select.dropdown_product_cat', this.filterByCategory)    // Filter by category dropdown
						.on('click', '.categories-filter-shop .product-category a', this.filterByCategory) // Filter by product categories widget in shop page
						.on('click', '.product-archive + div .pagination a', this.loadmoreByPagination) // Load by pagination in shop page

					$('.toolbox .woocommerce-ordering')													// Orderby
						.off('change', 'select.orderby').on('change', 'select.orderby', this.sortProducts);

					$('.product-archive > .woocommerce-info').wrap('<ul class="products"></ul>');

					if (!wolmart_vars.skeleton_screen) {
						$('.sidebar .dropdown_product_cat').off('change');
					}
					// Filter Actions
					AjaxLoadPost.refreshFilerClean('.filter-actions');
				} else {
					Wolmart.$body
						.on('change', '.toolbox-show-count .count', this.changeShowCountPage)            // Change Show Count when ajax disabled
						.on('change', '.sidebar select.dropdown_product_cat', this.changeCategory)		 // Change category by dropdown

					AjaxLoadPost.initSelect2();
				}

				AjaxLoadPost.isAjaxBlog && Wolmart.$body
					.on('click', '.widget_categories a', this.filterPostsByLink)                    // Filter blog by categories
					// .on('click', '.widget_tag_cloud a', this.filterPostsByLink)                  // Filter blog by tag
					.on('click', '.post-archive .blog-filters a', this.filterPostsByLink)           // Filter blog by categories filter
					.on('click', '.post-archive .pagination a', this.loadmoreByPagination)          // Load by pagination in shop page

				Wolmart.$body
					.on('click', '.btn-load', this.loadmoreByButton)						        // Load by button
					.on('click', '.products + .pagination a', this.loadmoreByPagination)              // Load by pagination in products widget
					.on('click', '.products .pagination a', this.loadmoreByPagination)              // Load by pagination in products widget
					.on('click', '.product-filters .nav-filter', this.filterWidgetByCategory)	    // Load by Nav Filter
					.on('click', '.filter-categories a', this.filterWidgetByCategory)		        // Load by Categories Widget's Filter
					.on('click', 'div:not(.post-archive) > .posts + .pagination a', this.loadmoreByPagination)				// Load by pagination in posts widget

				Wolmart.$window.on('wolmart_complete wolmart_loadmore', this.startScrollLoad);	    // Load by infinite scroll


				// YITH AJAX Navigation Plugin Compatibility
				if (typeof yith_wcan != 'undefined') {
					$(document)
						.on('yith-wcan-ajax-loading', this.loadingPage)
						.on('yith-wcan-ajax-filtered', this.loadedPage);

					// Issue for multiple products in shop pages.
					$('.yit-wcan-container').each(function () {
						$(this).parent('.product-archive').length || $(this).children('.products').addClass('ywcps-products').unwrap();
					});
					yith_wcan.container = '.product-archive .products';
				}
			},

			/**
			 * Run select2 js plugin
			 */
			initSelect2: function () {
				if ($.fn.selectWoo) {
					$('.dropdown_product_cat').selectWoo({
						placeholder: wolmart_vars.select_category,
						minimumResultsForSearch: 5,
						width: '100%',
						allowClear: true,
						language: {
							noResults: function () {
								return wolmart_vars.no_matched
							}
						}
					})
				}
			},

			/**
			 * Event handler to change show count for non ajax mode.
			 * 
			 * @since 1.0
			 * @param {Event} e 
			 */
			changeShowCountPage: function (e) {
				if (this.value) {
					location.href = Wolmart.addUrlParam(location.href.replace(/\/page\/\d*/, ''), 'count', this.value);
				}
			},

			/**
			 * Event handler to change category by dropdown
			 * 
			 * @since 1.0
			 * @param {Event} e 
			 */
			changeCategory: function (e) {
				location.href = this.value ? Wolmart.addUrlParam(wolmart_vars.home_url, 'product_cat', this.value) : wolmart_vars.shop_url;
			},

			/**
			 * Event handler to filter posts by link
			 *
			 * @since 1.0
			 * @param {Event} e 
			 */
			filterPostsByLink: function (e) {

				// If link's toggle is clicked, return
				if ((e.target.tagName == 'I' || e.target.classList.contains('toggle-btn')) && e.target.parentElement == e.currentTarget) {
					return;
				}

				var $link = $(e.currentTarget);

				if ($link.is('.nav-filters .nav-filter')) {
					$link.closest('.nav-filters').find('.nav-filter').removeClass('active');
					$link.addClass('active')
				} else if ($link.hasClass('active') || $link.parent().hasClass('current-cat')) {
					return;
				}

				var $container = $('.post-archive .posts');

				if (!$container.length) {
					return;
				}

				if (AjaxLoadPost.isAjaxBlog && AjaxLoadPost.doLoading($container, 'filter')) {
					e.preventDefault();
					var url = Wolmart.addUrlParam(e.currentTarget.getAttribute('href'), 'only_posts', 1);
					var postType = $container.data('post-type');
					if (postType) {
						url = Wolmart.addUrlParam(url, 'post_type', postType);
					}
					$.get(encodeURI(decodeURIComponent(decodeURI(url.replace(/\/page\/(\d*)/, '')))), function (res) {
						res && AjaxLoadPost.loadedPage(0, res, url);
					});
				}
			},

			/**
			 * Event handler to filter products by price
			 *
			 * @since 1.0
			 * @since 1.1.10 Added sidebar automatically close functionality after category selected on Mobile.
			 * 
			 * @param {Event} e 
			 */
			filterByPrice: function (e) {
				e.preventDefault();

				// Sidebar Close
				if (wolmart_vars.auto_close_mobile_filter && Wolmart.canvasWidth <= 992) {
					$('.sidebar-close').trigger('click');
				}

				var url = location.href,
					minPrice = $(e.currentTarget).siblings('#min_price').val(),
					maxPrice = $(e.currentTarget).siblings('#max_price').val();
				minPrice && (url = Wolmart.addUrlParam(url, 'min_price', minPrice));
				maxPrice && (url = Wolmart.addUrlParam(url, 'max_price', maxPrice));
				AjaxLoadPost.loadPage(url);
			},

			/**
			 * Event handler to filter products by price
			 * 
			 * @since 1.0
			 * @since 1.1.10 Added sidebar automatically close functionality after category selected on Mobile.
			 * 
			 * @param {Event} e 
			 */
			filterByPriceRange: function (e) {
				e.preventDefault();

				// Sidebar Close
				if (wolmart_vars.auto_close_mobile_filter && Wolmart.canvasWidth <= 992) {
					$('.sidebar-close').trigger('click');
				}

				var url = location.href,
					minPrice = $(e.currentTarget).find('.min_price').val(),
					maxPrice = $(e.currentTarget).find('.max_price').val();
				url = minPrice ? Wolmart.addUrlParam(url, 'min_price', minPrice) : Wolmart.removeUrlParam(url, 'min_price');
				url = maxPrice ? Wolmart.addUrlParam(url, 'max_price', maxPrice) : Wolmart.removeUrlParam(url, 'max_price');
				url != location.href && AjaxLoadPost.loadPage(url);
			},

			/**
			 * Event handler to filter products by rating
			 * 
			 * @since 1.0
			 * @since 1.1.10 Added sidebar automatically close functionality after category selected on Mobile.
			 * 
			 * @param {Event} e 
			 */
			filterByRating: function (e) {
				// Sidebar Close
				if (wolmart_vars.auto_close_mobile_filter && Wolmart.canvasWidth <= 992) {
					$('.sidebar-close').trigger('click');
				}

				var match = e.currentTarget.getAttribute('href').match(/rating_filter=(\d)/);
				if (match && match[1]) {
					e.preventDefault();
					AjaxLoadPost.loadPage(Wolmart.addUrlParam(location.href, 'rating_filter', match[1]));
				}
			},

			/**
			 * Event handler to filter products by link
			 * 
			 * @since 1.0
			 * @since 1.1.10 Added sidebar automatically close functionality after category selected on Mobile.
			 * 
			 * @param {Event} e 
			 */
			filterByLink: function (e) {
				e.preventDefault();

				// Sidebar Close
				if (wolmart_vars.auto_close_mobile_filter && Wolmart.canvasWidth <= 992) {
					$('.sidebar-close').trigger('click');
				}

				AjaxLoadPost.loadPage(e.currentTarget.getAttribute('href'));

				$('.categories-filter-shop .active').removeClass('active');
			},

			/**
			 * Event handler to filter products by category
			 * 
			 * @since 1.0
			 * @since 1.1.10 Added sidebar automatically close functionality after category selected on Mobile.
			 * 
			 * @param {Event} e 
			 */
			filterByCategory: function (e) {
				if ($(e.target).hasClass('w-icon-angle-down')) {
					return;
				}
				e.preventDefault();

				// Sidebar Close
				if (wolmart_vars.auto_close_mobile_filter && Wolmart.canvasWidth <= 992) {
					$('.sidebar-close').trigger('click');
				}

				var url;
				var isFromFilterWidget = false;

				if (e.type == 'change') { // Dropdown's event
					url = this.value ? Wolmart.addUrlParam(wolmart_vars.home_url, 'product_cat', this.value) : wolmart_vars.shop_url;

				} else { // Link's event
					// If link's toggle is clicked, return
					if (e.target.parentElement == e.currentTarget) {
						return;
					}
					var $link = $(e.currentTarget);

					if ($link.is('.categories-filter-shop .product-category a')) {
						// Products categories widget
						var $category = $link.closest('.product-category');
						if (!$link.closest('.category-list').length && $category.hasClass('active')) {
							return;
						}
						$category.closest('.categories-filter-shop').find('.product-category').removeClass('active');
						$category.addClass('active');
						isFromFilterWidget = true;

					} else {
						// Product categories sidebar widget
						if ($link.hasClass('active') || $link.parent().hasClass('current-cat')) {
							// If it's active, return
							return;
						}
					}
					url = $link.attr('href');
				}

				// Make current category active in categories-filter-shop widgets
				if (!isFromFilterWidget) {
					Wolmart.$body.one('wolmart_ajax_shop_layout', function () {
						$('.categories-filter-shop .product-category a').each(function () {
							$(this).closest('.product-category').toggleClass('active', this.href == location.href);
						})
					});
				}

				AjaxLoadPost.loadPage(url);
			},

			/**
			 * Event handler to filter products by category.
			 * 
			 * @since 1.0
			 * @param {Event} e 
			 */
			saveLastYithAjaxTrigger: function (e) {
				AjaxLoadPost.lastYithAjaxTrigger = e.currentTarget;
			},

			/**
			 * Event handler to change show type.
			 * 
			 * @since 1.0
			 * @param {Event} e 
			 */
			changeShowType: function (e) {
				e.preventDefault();
				if (!this.classList.contains('active')) {
					var type = this.classList.contains('w-icon-list') ? 'list' : 'grid';
					$('.product-archive .products').data('loading_show_type', type)	// For skeleton screen
					$(this).parent().children().toggleClass('active');				// Toggle active class
					AjaxLoadPost.loadPage(
						Wolmart.addUrlParam(location.href, 'showtype', type),
						{ showtype: type }
					);
				}
			},

			/**
			 * Event handler to change order.
			 * 
			 * @since 1.0
			 * @param {Event} e 
			 */
			sortProducts: function (e) {
				AjaxLoadPost.loadPage(Wolmart.addUrlParam(location.href, 'orderby', this.value));
			},

			/**
			 * Event handler to change show count.
			 * 
			 * @since 1.0
			 * @param {Event} e 
			 */
			changeShowCount: function (e) {
				AjaxLoadPost.loadPage(Wolmart.addUrlParam(location.href, 'count', this.value));
			},

			/**
			 * Refresh widgets
			 * 
			 * @since 1.0
			 * @param {string} widgetSelector
			 * @param {jQuery} $newContent 
			 */
			refreshWidget: function (widgetSelector, $newContent) {
				// Other Widgets
				$('.sidebar').each(function (sidebar_index) {
					var newWidgets = $newContent.find('.sidebar').eq(sidebar_index).find(widgetSelector),
						oldWidgets = $(this).find(widgetSelector);

					if ('.widget' == widgetSelector) { // refresh all widgets
						oldWidgets.parent().empty().append(newWidgets);
					} else {

						oldWidgets.length && oldWidgets.each(function (i) {
							// if new widget exists
							if (newWidgets.eq(i).length) {
								this.innerHTML = newWidgets.eq(i).html();
							} else {
								// else
								$(this).empty();
							}
						});
					}
				});
			},
			/**
			 * Refresh filter clean widget
			 * 
			 * @since 1.0
			 * @param {string} widgetSelector
			 * @param {jQuery} $newContent 
			 */
			refreshFilerClean: function (widgetSelector) {
				// Filter Clean Widget
				if ('/shop/' === location.href.slice(-6))
					$('.sidebar ' + widgetSelector).css('display', 'none');
				else {
					$('.sidebar ' + widgetSelector).css('display', 'flex');
				}
			},
			/**
			 * Refresh button
			 * 
			 * @since 1.0
			 * @param {jQuery} $wrapper
			 * @param {jQuery} $newButton
			 * @param {object} options
			 */
			refreshButton: function ($wrapper, $newButton, options) {
				var $btn = $wrapper.siblings('.btn-load');

				if (typeof options != 'undefined') {
					if (typeof options == 'string' && options) {
						options = JSON.parse(options);
					}
					if (!options.args || !options.args.paged || options.max > options.args.paged) {
						if ($btn.length) {
							$btn[0].outerHTML = $newButton.length ? $newButton[0].outerHTML : '';
						} else {
							$newButton.length && $wrapper.after($newButton);
						}
						return;
					}
				}

				$btn.remove();
			},

			/**
			 * Process before load 
			 * 
			 * data can be {showtype: (boolean)} or omitted.
			 *
			 * @since 1.0
			 * @param {string} url
			 * @param {mixed} data
			 */
			loadPage: function (url, data) {
				AjaxLoadPost.loadingPage();

				// If it's not "show type change" load, remove page number from url
				if ('undefined' == typeof showtype) {
					url = encodeURI(decodeURIComponent(decodeURI(url.replace(/\/page\/(\d*)/, ''))));
				}

				// Add show type if current layout is list
				if (data && 'list' == data.showtype || (!data || 'undefined' == typeof data.showtype) && 'list' == Wolmart.getUrlParam(location.href, 'showtype')) {
					url = Wolmart.addUrlParam(url, 'showtype', 'list');
				} else {
					url = Wolmart.removeUrlParam(url, 'showtype');
				}

				// Add show count if current show count is set, except show count change
				if (!Wolmart.getUrlParam(url, 'count')) {
					var showcount = Wolmart.getUrlParam(location.href, 'count');
					if (showcount) {
						url = Wolmart.addUrlParam(url, 'count', showcount);
					}
				}

				$.get(Wolmart.addUrlParam(url, 'only_posts', 1), function (res) {
					res && AjaxLoadPost.loadedPage(0, res, url);
				});
			},

			/**
			 * Process while loading. 
			 * 
			 * @since 1.0
			 * @param {Event} e
			 */
			loadingPage: function (e) {
				var $container = $('.product-archive .products');

				if ($container.length) {
					if (e && e.type == 'yith-wcan-ajax-loading') {
						$container.removeClass('yith-wcan-loading').addClass('product-filtering');
					}
					if (AjaxLoadPost.doLoading($container, 'filter')) {
						Wolmart.scrollToFixedContent(
							($('.toolbox-top').length ? $('.toolbox-top') : $wrapper).offset().top - 20,
							400
						);
					}
				}
			},

			/**
			 * Process after load 
			 * 
			 * @since 1.0
			 * @param {Event} e
			 * @param {string} res
			 * @param {string} url
			 * @param {string} loadmore_type
			 */
			loadedPage: function (e, res, url, loadmore_type) {
				var $res = $(res);
				$res.imagesLoaded(function () {

					var $container, $newContainer;

					// Update browser history (IE doesn't support it)
					if (url && !Wolmart.isIE && loadmore_type != 'button' && loadmore_type != 'scroll') {
						history.pushState({ pageTitle: res && res.pageTitle ? '' : res.pageTitle }, "", Wolmart.removeUrlParam(url, 'only_posts'));
					}

					if (typeof loadmore_type == 'undefined') {
						loadmore_type = 'filter';
					}

					if (AjaxLoadPost.isAjaxBlog) {
						$container = $('.post-archive .posts');
						$newContainer = $res.find('.post-archive .posts');
						if (!$newContainer.length) {
							$newContainer = $res.find('.posts');
						}
					} else if (AjaxLoadPost.isAjaxShop) {
						$container = $('.product-archive .products');
						$newContainer = $res.find('.product-archive .products');
					} else {
						$container = $('.post-archive .posts');
						$newContainer = $res.find('.post-archive .posts');

						// Update Loadmore - Button
						if ($container.hasClass('posts')) { // Blog Archive
							AjaxLoadPost.refreshButton($container, $newContainer.siblings('.btn-load'), $container.attr('data-load'));
						} else {
							$container = $('.product-archive .products');
							$newContainer = $res.find('.product-archive .products');

							if ($container.hasClass('products')) { // Shop Archive
								var $parent = $('.product-archive'),
									$newParent = $res.find('.product-archive');
								AjaxLoadPost.refreshButton($parent, $newParent.siblings('.btn-load'), $container.attr('data-load'));
							}
						}
						return;
					}

					// Change content and update status.
					// When loadmore by button, scroll or pagination is performing, the 'loadmore' function performs this.
					if (loadmore_type == 'filter') {
						$container.html($newContainer.html());
						AjaxLoadPost.endLoading($container, loadmore_type);

						// Update Loadmore
						if ($newContainer.attr('data-load')) {
							$container.attr('data-load', $newContainer.attr('data-load'));
						} else {
							$container.removeAttr('data-load');
						}
					}

					// Change page title bar
					$('.page-title-bar').html($res.find('.page-title-bar').length ? $res.find('.page-title-bar').html() : '');

					// Change Breadcrumb
					if ($('.breadcrumb-container').length) {
						$('.breadcrumb').html($res.find('.breadcrumb').length ? $res.find('.breadcrumb').html() : '');
					}

					if (AjaxLoadPost.isAjaxBlog) { // Blog Archive

						// Update Loadmore - Button
						AjaxLoadPost.refreshButton($container, $newContainer.siblings('.btn-load'), $container.attr('data-load'));

						// Update Loadmore - Pagination
						var $pagination = $container.siblings('.pagination'),
							$newPagination = $newContainer.siblings('.pagination');

						if ($pagination.length) {
							$pagination[0].outerHTML = $newPagination.length ? $newPagination[0].outerHTML : '';
						} else {
							$newPagination.length && $container.after($newPagination);
						}

						// Update sidebar widgets
						AjaxLoadPost.refreshWidget('.widget_categories', $res);
						AjaxLoadPost.refreshWidget('.widget_tag_cloud', $res);

						// Update nav filter
						var $newNavFilters = $res.find('.post-archive .nav-filters');
						$newNavFilters.length && $('.post-archive .nav-filters').html($newNavFilters.html());

						// Init posts
						AjaxLoadPost.fitVideos($container);
						Wolmart.slider('.post-media-carousel');

						Wolmart.$body.trigger('wolmart_ajax_blog_layout', $container, res, url, loadmore_type);

					} else if (AjaxLoadPost.isAjaxShop) { // Products Archive

						var $parent = $('.product-archive'),
							$newParent = $res.find('.product-archive');

						// If new content is empty, show woocommerce info.
						if (!$newContainer.length) {
							$container.empty().append($res.find('.woocommerce-info'));
						}

						// Update Toolbox Title
						var $newTitle = $res.find('.main-content .toolbox .title');
						$newTitle.length && $('.main-content .toolbox .title').html($newTitle.html());

						// Update nav filter
						var $newNavFilters = $res.find('.main-content .toolbox .nav-filters');
						$newNavFilters.length && $('.main-content .toolbox .nav-filters').html($newNavFilters.html());

						// Update Show Count
						if (typeof loadmore_type != 'undefined' && (loadmore_type == 'button' || loadmore_type == 'scroll')) {
							var $span = $('.main-content .woocommerce-result-count > span');
							if ($span.length) {
								var newShowInfo = $span.html(),
									match = newShowInfo.match(/\d+\–(\d+)/);
								if (match && match[1]) {
									var last = parseInt(match[1]) + $newContainer.children().length,
										match = newShowInfo.replace(/\d+\–\d+/, '').match(/\d+/);
									$span.html(match && match[0] && last == match[0] ? wolmart_vars.texts.show_info_all.replace('%d', last) : newShowInfo.replace(/(\d+)\–\d+/, '$1–' + last));
								}
							}
						} else {
							var $count = $('.main-content .woocommerce-result-count');
							var $toolbox = $count.parent('.toolbox-pagination');
							var newShowInfo = $res.find('.woocommerce-result-count').html();

							$count.html(newShowInfo ? newShowInfo : '');
							newShowInfo ? $toolbox.removeClass('no-pagination') : $toolbox.addClass('no-pagination');
						}

						// Update Toolbox Pagination
						var $toolboxPagination = $parent.siblings('.toolbox-pagination'),
							$newToolboxPagination = $newParent.siblings('.toolbox-pagination');

						if (!$toolboxPagination.length) {
							$newToolboxPagination.length && $parent.after($newToolboxPagination);

						} else { // Update Loadmore - Pagination
							var $pagination = $parent.siblings('.toolbox-pagination').find('.pagination'),
								$newPagination = $newParent.siblings('.toolbox-pagination').find('.pagination');

							if ($pagination.length) {
								$pagination[0].outerHTML = $newPagination.length ? $newPagination[0].outerHTML : '';
							} else {
								$newPagination.length && $parent.siblings('.toolbox-pagination').append($newPagination);
							}
						}

						// Update Loadmore - Button
						AjaxLoadPost.refreshButton($parent, $newParent.siblings('.btn-load'), $container.attr('data-load'));

						// Update Sidebar Widgets
						if (loadmore_type == 'filter') {
							// Refresh Filter Clean widgets
							// AjaxLoadPost.refreshWidget('.wolmart-price-filter', $res);
							AjaxLoadPost.refreshWidget('.widget', $res);
							AjaxLoadPost.refreshFilerClean('.filter-actions');
							Wolmart.initPriceSlider();

							// Refresh Filter Products by Attribute Widgets
							// AjaxLoadPost.refreshWidget('.woocommerce-widget-layered-nav:not(.widget_product_brands)', $res);

							if (!e || e.type != "yith-wcan-ajax-filtered") {
								// Refresh YITH Ajax Navigation Widgets
								AjaxLoadPost.refreshWidget('.yith-woo-ajax-navigation', $res);
							} else {
								yith_wcan && $(yith_wcan.result_count).show();
								var $last = $(AjaxLoadPost.lastYithAjaxTrigger);
								$last.closest('.yith-woo-ajax-navigation').is(':hidden') && $last.parent().toggleClass('chosen');
								$('.sidebar .yith-woo-ajax-navigation').show();
							}

							/* Added 2021-11-29, from v1.1.5*/
							// Keep sub categories menu open after refresh sidebar
							if ($('.current-cat-parent ul').length) {
								$('.current-cat-parent ul').css('display', 'block');
							}

							AjaxLoadPost.initSelect2();
						}

						if (!$container.hasClass('skeleton-body')) {
							if ($container.data('loading_show_type')) {
								$container.toggleClass('list-type-products', 'list' == $container.data('loading_show_type'));
								$container.attr('class',
									$container.attr('class').replace(/row|cols\-\d|cols\-\w\w-\d/g, '').replace(/\s+/, ' ') +
									$container.attr('data-col-' + $container.data('loading_show_type'))
								);
								$('.main-content-wrap > .sidebar.closed').length && Wolmart.shop.switchColumns(false);
							}
						}

						// Remove loading show type.
						$container.removeData('loading_show_type');

						// Init products
						Wolmart.shop.initProducts($container);

						Wolmart.$body.trigger('wolmart_ajax_shop_layout', $container, res, url, loadmore_type);

						$container.removeClass('product-filtering');
					}


					$container.removeClass('skeleton-body load-scroll');
					$newContainer.hasClass('load-scroll') && $container.addClass('load-scroll');

					// Sidebar Widget Compatibility
					Wolmart.menu.initCollapsibleWidgetToggle();

					// Isotope Refresh
					if ($container.hasClass('grid')) {
						Wolmart.isotopes($container);
					}

					// countdown init
					Wolmart.countdown($container.find('.countdown'));

					// Update Loadmore - Scroll
					Wolmart.call(AjaxLoadPost.startScrollLoad, 50);

					// Refresh layouts
					Wolmart.call(Wolmart.refreshLayouts, 70);

					if (document.getElementById('wolmart_price_filter_chart')) {
						Wolmart.price_chart(document.getElementById('wolmart_price_filter_chart'));
					}
					Wolmart.$body.trigger('wolmart_ajax_finish_layout', $container, res, url, loadmore_type);
				});
			},

			/**
			 * Check load 
			 * 
			 * @since 1.0
			 * @param {jQuery} $wrapper
			 * @param {string} type
			 */
			canLoad: function ($wrapper, type) {
				// check max
				if (type == 'button' || type == 'scroll') {
					var load = $wrapper.attr('data-load');
					if (load) {
						var options = JSON.parse($wrapper.attr('data-load'));
						if (options && options.args && options.max <= options.args.paged) {
							return false;
						}
					}
				}

				// If it is loading or active, return
				if ($wrapper.hasClass('loading-more') || $wrapper.hasClass('skeleton-body') || $wrapper.siblings('.w-loading').length) {
					return false;
				}

				return true;
			},

			/**
			 * Show loading effects. 
			 * 
			 * @since 1.0
			 * @param {jQuery} $wrapper
			 * @param {string} type
			 */
			doLoading: function ($wrapper, type) {
				if (!AjaxLoadPost.canLoad($wrapper, type)) {
					return false;
				}

				// "Loading start" effect
				if (wolmart_vars.skeleton_screen && $wrapper.closest('.product-archive, .post-archive').length) {

					// Skeleton screen for archive pages

					var count = 12,
						template = '';

					if ($wrapper.closest('.product-archive').length) {
						// Shop Ajax
						count = parseInt(Wolmart.getCookie('wolmart_count'));
						if (!count) {
							var $count = $('.main-content .toolbox-show-count .count');
							$count.length && (count = $count.val());
						}
						count || (count = 12);
					} else if ($wrapper.closest('.post-archive').length) {

						// Blog Ajax
						$wrapper.children('.grid-space').remove();
						count = wolmart_vars.posts_per_page;
					}

					if ($wrapper.hasClass('products')) {
						// product template
						var skelType = $wrapper.hasClass('list-type-products') ? 'skel-pro skel-pro-list' : 'skel-pro';
						if ($wrapper.data('loading_show_type')) {
							skelType = 'list' == $wrapper.data('loading_show_type') ? 'skel-pro skel-pro-list' : 'skel-pro';
						}
						template = '<li class="product-wrap"><div class="' + skelType + '"></div></li>';
					} else {
						// post template
						var skelType = 'skel-post';
						if ($wrapper.hasClass('list-type-posts')) {
							skelType = 'skel-post-list';
						}
						if ($wrapper.attr('data-post-type')) {
							skelType = 'skel-post-' + $wrapper.attr('data-post-type');
						}
						template = '<div class="post-wrap"><div class="' + skelType + '"></div></div>';
					}

					// Empty wrapper
					if (type == 'page' || type == 'filter') {
						$wrapper.html('');
					}

					if ($wrapper.data('loading_show_type')) {
						$wrapper.toggleClass('list-type-products', 'list' == $wrapper.data('loading_show_type'));
						$wrapper.attr('class',
							$wrapper.attr('class').replace(/row|cols\-\d|cols\-\w\w-\d/g, '').replace(/\s+/, ' ') +
							$wrapper.attr('data-col-' + $wrapper.data('loading_show_type'))
						);
					}

					if (Wolmart.isIE) {
						var tmpl = '';
						while (count--) { tmpl += template; }
						$wrapper.addClass('skeleton-body').append(tmpl);
					} else {
						$wrapper.addClass('skeleton-body').append(template.repeat(count));
					}

				} else {
					// Widget or not skeleton in archive pages
					if (type == 'button' || type == 'scroll') {
						Wolmart.showMore($wrapper);
					} else {
						Wolmart.doLoading($wrapper.parent());
					}
				}

				// Scroll to wrapper's top offset
				if (type == 'page') {
					Wolmart.scrollToFixedContent(($('.toolbox-top').length ? $('.toolbox-top') : $wrapper).offset().top - 20, 400);
				}

				if ($wrapper.data('isotope')) {
					$wrapper.isotope('destroy');
				}

				$wrapper.addClass('loading-more');

				return true;
			},

			/**
			 * End loading effect. 
			 * 
			 * @since 1.0
			 * @param {jQuery} $wrapper
			 * @param {string} type
			 */
			endLoading: function ($wrapper, type) {
				// Clear loading effect
				if (wolmart_vars.skeleton_screen && $wrapper.closest('.product-archive, .post-archive').length) { // shop or blog archive
					if (type == 'button' || type == 'scroll') {
						$wrapper.find('.skel-pro,.skel-post').parent().remove();
					}
					$wrapper.removeClass('skeleton-body');
				} else {
					if (type == 'button' || type == 'scroll') {
						Wolmart.hideMore($wrapper.parent());
					} else {
						Wolmart.endLoading($wrapper.parent());
					}
				}
				$wrapper.removeClass('loading-more');
			},

			/**
			 * Filter widgets by category
			 * 
			 * @since 1.0
			 * @param {Event} e
			 */
			filterWidgetByCategory: function (e) {
				var $filter = $(e.currentTarget);

				e.preventDefault();

				// If this is filtered by archive page's toolbox filter or this is active now, return.
				if ($filter.is('.toolbox .nav-filter') || $filter.is('.post-archive .nav-filter') || $filter.hasClass('active')) {
					return;
				}

				// Find Wrapper
				var filterNav, $wrapper, filterCat = $filter.attr('data-cat');

				filterNav = $filter.closest('.nav-filters');
				if (filterNav.length) {
					$wrapper = filterNav.parent().find(filterNav.hasClass('product-filters') ? '.products' : '.posts');
				} else {
					filterNav = $filter.closest('.filter-categories');
					if (filterNav.length) {
						if ($filter.closest('.elementor-section').length) {
							$wrapper = $filter.closest('.elementor-section').find('.products[data-load]').eq(0);
							if (!$wrapper.length) {
								$wrapper = $filter.closest('.elementor-top-section').find('.products[data-load]').eq(0);
							}
						} else if ($filter.closest('.vce-row').length) {
							$wrapper = $filter.closest('.vce-row').find('.products[data-load]').eq(0);
						} else if ($filter.closest('.wpb_row').length) {
							$wrapper = $filter.closest('.wpb_row').find('.products[data-load]').eq(0);

							// If there is no products to be filtered in vc row, just find it in the same section
							if (!$wrapper.length) {
								if ($filter.closest('.vc_section').length) {
									$wrapper = $filter.closest('.vc_section').find('.products[data-load]').eq(0);
								}
							}
						}
					}
				}

				$wrapper.length &&
					AjaxLoadPost.loadmore({
						wrapper: $wrapper,
						page: 1,
						type: 'filter',
						category: filterCat,
						onStart: function () {
							// Toggle active button class
							filterNav.length && (
								filterNav.find('.cat-type-icon').length
									? ( // if category type is icon
										filterNav.find('.cat-type-icon').removeClass('active'),
										$filter.closest('.cat-type-icon').addClass('active'))
									: ( // if not,
										filterNav.find('a').removeClass('active'),
										$filter.addClass('active')
									)
							);
						}
					})
			},

			/**
			 * Load more by button
			 * 
			 * @since 1.0
			 * @param {Event} e
			 */
			loadmoreByButton: function (e) {
				var $btn = $(e.currentTarget); // This will be replaced with new html of ajax content.
				e.preventDefault();

				AjaxLoadPost.loadmore({
					wrapper: $btn.siblings('.product-archive').length ? $btn.siblings('.product-archive').find('.products') : $btn.siblings('.products, .posts'),
					page: '+1',
					type: 'button',
					onStart: function () {
						$btn.data('text', $btn.html())
							.addClass('loading').blur()
							.html(wolmart_vars.texts.loading);
					},
					onFail: function () {
						$btn.text(wolmart_vars.texts.loadmore_error).addClass('disabled');
					}
				});
			},

			/**
			 * Event handler for ajax loading by infinite scroll 
			 * 
			 * @since 1.0
			 */
			startScrollLoad: function () {
				AjaxLoadPost.scrollWrappers = $('.load-scroll');
				if (AjaxLoadPost.scrollWrappers.length) {
					AjaxLoadPost.loadmoreByScroll();
					Wolmart.$window.off('scroll resize', AjaxLoadPost.loadmoreByScroll);
					window.addEventListener('scroll', AjaxLoadPost.loadmoreByScroll, { passive: true });
					window.addEventListener('resize', AjaxLoadPost.loadmoreByScroll, { passive: true });
				}
			},

			/**
			 * Load more by scroll
			 * 
			 * @since 1.0
			 * @param {jQuery} $scrollWrapper
			 */
			loadmoreByScroll: function ($scrollWrapper) {
				var target = AjaxLoadPost.scrollWrappers,
					loadOptions = target.attr('data-load'),
					maxPage = 1,
					curPage = 1;

				if (loadOptions) {
					loadOptions = JSON.parse(loadOptions);
					maxPage = loadOptions.max;
					if (loadOptions.args && loadOptions.args.paged) {
						curPage = loadOptions.args.paged;
					}
				}

				if (curPage >= maxPage) {
					return;
				}

				$scrollWrapper && $scrollWrapper instanceof jQuery && (target = $scrollWrapper);

				// load more
				target.length && AjaxLoadPost.canLoad(target, 'scroll') && target.each(function () {
					var rect = this.getBoundingClientRect();
					if (rect.top + rect.height > 0 &&
						rect.top + rect.height < window.innerHeight) {
						AjaxLoadPost.loadmore({
							wrapper: $(this),
							page: '+1',
							type: 'scroll',
							onDone: function ($result, $wrapper, options) {
								// check max
								if (options.max && options.max <= options.args.paged) {
									$wrapper.removeClass('load-scroll');
								}
								// continue loadmore again
								Wolmart.call(AjaxLoadPost.startScrollLoad, 50);
							},
							onFail: function (jqxhr, $wrapper) {
								$wrapper.removeClass('load-scroll');
							}
						});
					}
				});

				// remove loaded wrappers
				AjaxLoadPost.scrollWrappers = AjaxLoadPost.scrollWrappers.filter(function () {
					var $this = $(this);
					$this.children('.post-wrap,.product-wrap').length || $this.removeClass('load-scroll');
					return $this.hasClass('load-scroll');
				});
				AjaxLoadPost.scrollWrappers.length || (
					window.removeEventListener('scroll', AjaxLoadPost.loadmoreByScroll),
					window.removeEventListener('resize', AjaxLoadPost.loadmoreByScroll)
				)
			},

			/**
			 * Fit videos
			 * 
			 * @since 1.0
			 * @param {jQuery} $wrapper
			 */
			fitVideos: function ($wrapper, fitVids) {
				// Video Post Refresh
				if ($wrapper.find('.fit-video').length) {

					var defer_mecss = (function () {
						var deferred = $.Deferred();
						if ($('#wp-mediaelement-css').length) {
							deferred.resolve();
						} else {
							$(document.createElement('link')).attr({
								id: 'wp-mediaelement-css',
								href: wolmart_vars.ajax_url.replace('wp-admin/admin-ajax.php', 'wp-includes/js/mediaelement/wp-mediaelement.min.css'),
								media: 'all',
								rel: 'stylesheet'
							}).appendTo('body').on(
								'load',
								function () {
									deferred.resolve();
								}
							);
						}
						return deferred.promise();
					})();

					var defer_mecss_legacy = (function () {
						var deferred = $.Deferred();
						if ($('#mediaelement-css').length) {
							deferred.resolve();
						} else {
							$(document.createElement('link')).attr({
								id: 'mediaelement-css',
								href: wolmart_vars.ajax_url.replace('wp-admin/admin-ajax.php', 'wp-includes/js/mediaelement/mediaelementplayer-legacy.min.css'),
								media: 'all',
								rel: 'stylesheet'
							}).appendTo('body').on(
								'load',
								function () {
									deferred.resolve();
								}
							);
						}
						return deferred.promise();
					})();

					var defer_mejs = (function () {
						var deferred = $.Deferred();

						if (typeof window.wp.mediaelement != 'undefined') {
							deferred.resolve();
						} else {

							$('<script>var _wpmejsSettings = { "stretching": "responsive" }; </script>').appendTo('body');

							var defer_mejsplayer = (function () {
								var deferred = $.Deferred();

								$(document.createElement('script')).attr('id', 'mediaelement-core-js')
									.appendTo('body')
									.on('load', function () {
										deferred.resolve();
									})
									.attr('src', wolmart_vars.ajax_url.replace('wp-admin/admin-ajax.php', 'wp-includes/js/mediaelement/mediaelement-and-player.min.js'));

								return deferred.promise();
							})();
							var defer_mejsmigrate = (function () {
								var deferred = $.Deferred();

								setTimeout(function () {
									$(document.createElement('script')).attr('id', 'mediaelement-migrate-js').appendTo('body').on(
										'load',
										function () {
											deferred.resolve();
										}
									).attr('src', wolmart_vars.ajax_url.replace('wp-admin/admin-ajax.php', 'wp-includes/js/mediaelement/mediaelement-migrate.min.js'));
								}, 100);

								return deferred.promise();
							})();
							$.when(defer_mejsplayer, defer_mejsmigrate).done(
								function (e) {
									$(document.createElement('script')).attr('id', 'wp-mediaelement-js').appendTo('body').on(
										'load',
										function () {
											deferred.resolve();
										}
									).attr('src', wolmart_vars.ajax_url.replace('wp-admin/admin-ajax.php', 'wp-includes/js/mediaelement/wp-mediaelement.min.js'));
								}
							);
						}

						return deferred.promise();
					})();

					var defer_fitvids = (function () {
						var deferred = $.Deferred();
						if ($.fn.fitVids) {
							deferred.resolve();
						} else {
							$(document.createElement('script')).attr('id', 'jquery.fitvids-js')
								.appendTo('body')
								.on('load', function () {
									deferred.resolve();
								}).attr('src', wolmart_vars.assets_url + '/vendor/jquery.fitvids/jquery.fitvids.min.js');
						}
						return deferred.promise();
					})();

					$.when(defer_mecss, defer_mecss_legacy, defer_mejs, defer_fitvids).done(
						function (e) {
							Wolmart.call(function () {
								Wolmart.fitVideoSize($wrapper);
							}, 200);
						}
					);
				}
			},

			/**
			 * Event handler for ajax loading by pagination 
			 * 
			 * @since 1.0
			 * @param {Event} e
			 */
			loadmoreByPagination: function (e) {
				var $btn = $(e.currentTarget); // This will be replaced with new html of ajax content

				// Multi-Vendor X
				if (Wolmart.$body.hasClass('wolmart-mvx-vendor-store-page')) {
					return;
				}

				if (Wolmart.$body.hasClass('dokan-store') && $btn.closest('.dokan-single-store').length) {
					return;
				}
				if (Wolmart.$body.hasClass('wcfm-store-page') || Wolmart.$body.hasClass('wcfmmp-store-page')) {
					return;
				}
				e.preventDefault();

				var $pagination = $btn.closest('.toolbox-pagination').length ? $btn.closest('.toolbox-pagination') : $btn.closest('.pagination');

				AjaxLoadPost.loadmore({
					wrapper: $pagination.siblings('.product-archive').length ?
						$pagination.siblings('.product-archive').find('.products') :
						$pagination.siblings('.products, .posts'),

					page: $btn.hasClass('next') ? '+1' :
						($btn.hasClass('prev') ? '-1' : $btn.text()),
					type: 'page',
					onStart: function ($wrapper, options) {
						Wolmart.doLoading($btn.closest('.pagination'), 'simple');
					}
				});
			},

			/**
			 * Load more ajax content 
			 * 
			 * @since 1.0
			 * @param {object} params
			 * @return {boolean}
			 */
			loadmore: function (params) {
				if (!params.wrapper ||
					1 != params.wrapper.length ||
					!params.wrapper.attr('data-load') ||
					!AjaxLoadPost.doLoading(params.wrapper, params.type)) {
					return false;
				}

				// Get wrapper
				var $wrapper = params.wrapper;

				// Get options
				var options = JSON.parse($wrapper.attr('data-load'));
				options.args = options.args || {};
				if (!options.args.paged) {
					options.args.paged = 1;

					// Get correct page number at first in archive pages
					if ($wrapper.closest('.product-archive, .post-archive').length) {
						var match = location.pathname.match(/\/page\/(\d*)/);
						if (match && match[1]) {
							options.args.paged = parseInt(match[1]);
						}
					}
				}
				if ('filter' == params.type) {
					options.args.paged = 1;
					if (params.category) {
						options.args.category = params.category; // filter category
					} else if (options.args.category) {
						delete options.args.category; // do not filter category
					}
				} else if ('+1' === params.page) {
					++options.args.paged;
				} else if ('-1' === params.page) {
					--options.args.paged;
				} else {
					options.args.paged = parseInt(params.page);
				}

				// Get ajax url
				var url = wolmart_vars.ajax_url;
				if ($wrapper.closest('.product-archive, .post-archive').length) { // shop or blog archive
					var pathname = location.pathname;
					if (pathname.endsWith('/')) {
						pathname = pathname.slice(0, pathname.length - 1);
					}
					if (pathname.indexOf('/page/') >= 0) {
						pathname = pathname.replace(/\/page\/\d*/, '/page/' + options.args.paged);
					} else {
						pathname += '/page/' + options.args.paged;
					}

					url = Wolmart.addUrlParam(location.origin + pathname + location.search, 'only_posts', 1);
					if (options.args.category && options.args.category != '*') {
						url = Wolmart.addUrlParam(url, 'product_cat', category);
					}
				}

				// Add product-page param to set current page for pagination
				if ($wrapper.hasClass('products') && !$wrapper.closest('.product-archive').length) {
					url = Wolmart.addUrlParam(url, 'product-page', options.args.paged);
				}

				// Add post type to blog posts' ajax pagination.
				if ($wrapper.closest('.post-archive').length) {
					var postType = $wrapper.data('post-type');
					if (postType) {
						url = Wolmart.addUrlParam(url, 'post_type', postType);
					}
				}

				// Get ajax data
				var data = {
					action: $wrapper.closest('.product-archive, .post-archive').length ? '' : 'wolmart_loadmore',
					nonce: wolmart_vars.nonce,
					props: options.props,
					args: options.args,
					loadmore: params.type
				};
				if (params.type == 'page') {
					data.pagination = 1;
				}

				// Before start loading
				params.onStart && params.onStart($wrapper, options);

				// Do ajax
				$.post(url, data)
					.done(function (result) {
						// In case of posts widget's pagination, result's structure will be {html: '', pagination: ''}.
						var res_pagination = '';
						if ($wrapper.hasClass('posts') && !$wrapper.closest('.post-archive').length && params.type == 'page') {
							result = JSON.parse(result);
							res_pagination = result.pagination;
							result = result.html;
						}

						// In other cases, result will be html.
						var $result = $(result),
							$content;

						$result.imagesLoaded(function () {

							// Get content, except posts widget
							if ($wrapper.closest('.product-archive').length) {
								$content = $result.find('.product-archive .products');
							} else if ($wrapper.closest('.post-archive').length) {
								$content = $result.find('.post-archive .posts');
							} else {
								$content = $wrapper.hasClass('products') ? $result.find('.products') : $result;
							}

							// Change status and content
							if (params.type == 'page' || params.type == 'filter') {
								if ($wrapper.data('slider')) {
									$wrapper.data('slider').destroy();
									$wrapper.removeData('slider');
									$wrapper.data('slider-layout') && $wrapper.addClass($wrapper.data('slider-layout').join(' '));
								}
								$wrapper.data('isotope') && $wrapper.data('isotope').destroy();
								$wrapper.empty();
							}

							if (!$wrapper.hasClass('posts') || $wrapper.closest('.post-archive').length) {
								// Except posts widget, update max page and class
								var max = $content.attr('data-load-max');
								if (max) {
									options.max = parseInt(max);
								}
								// $wrapper.attr('class', $content.attr('class'));
								$wrapper.append($content.children());
							} else {
								// For posts widget
								$wrapper.append($content);
							}

							// Update wrapper status.
							$wrapper.attr('data-load', JSON.stringify(options));

							if ($wrapper.closest('.product-archive').length || $wrapper.closest('.post-archive').length) {
								AjaxLoadPost.loadedPage(0, result, url, params.type);
							} else {
								// Change load controls for widget
								var loadmore_type = params.type == 'filter' ? options.props.loadmore_type : params.type;

								if (loadmore_type == 'button') {
									if (params.type != 'filter' && $wrapper.hasClass('posts')) {
										var $btn = $wrapper.siblings('.btn-load');
										if ($btn.length) {
											if (typeof options.args == 'undefined' || typeof options.max == 'undefined' ||
												typeof options.args.paged == 'undefined' || options.max <= options.args.paged) {
												$btn.remove();
											} else {
												$btn.html($btn.data('text'));
											}
										}
									} else {
										AjaxLoadPost.refreshButton($wrapper, $result.find('.btn-load'), options);
									}

								} else if (loadmore_type == 'page') {
									var $pagination = $wrapper.parent().find('.pagination')
									var $newPagination = $wrapper.hasClass('posts') ? $(res_pagination) : $result.find('.pagination');
									if ($pagination.length) {
										$pagination[0].outerHTML = $newPagination.length ? $newPagination[0].outerHTML : '';
									} else {
										$newPagination.length && $wrapper.after($newPagination);
									}

								} else if (loadmore_type == 'scroll') {
									$wrapper.addClass('load-scroll');
									if (params.type == 'filter') {
										Wolmart.call(function () {
											AjaxLoadPost.loadmoreByScroll($wrapper);
										}, 50);
									}
								}
							}

							// Init products and posts
							$wrapper.hasClass('products') && Wolmart.shop.initProducts($wrapper);
							$wrapper.hasClass('posts') && AjaxLoadPost.fitVideos($wrapper);

							// Refresh layouts
							if ($wrapper.hasClass('grid')) {
								$wrapper.removeData('isotope');
								Wolmart.isotopes($wrapper);
							}
							if ($wrapper.hasClass('slider-wrapper')) {
								Wolmart.slider($wrapper);
							}

							params.onDone && params.onDone($result, $wrapper, options);

							// If category filter is not set in widget and loadmore has been limited to max, remove data-load attribute
							if (!$wrapper.hasClass('filter-products') &&
								!($wrapper.hasClass('products') && $wrapper.parent().siblings('.nav-filters').length) &&
								options.max && options.max <= options.args.paged && 'page' != params.type) {
								$wrapper.removeAttr('data-load');
							}

							AjaxLoadPost.endLoading($wrapper, params.type);
							params.onAlways && params.onAlways(result, $wrapper, options);
							Wolmart.refreshLayouts();
						});
					}).fail(function (jqxhr) {
						params.onFail && params.onFail(jqxhr, $wrapper);
						AjaxLoadPost.endLoading($wrapper, params.type);
						params.onAlways && params.onAlways(result, $wrapper, options);
					});

				return true;
			}
		}
		return function () {
			AjaxLoadPost.init();
			Wolmart.AjaxLoadPost = AjaxLoadPost;
		}
	})();

	/**
	 * Menu Class
	 *
	 * @class Menu
	 * @since 1.0
	 * @return {Object} Menu
	 */
	Wolmart.menu = (function () {

		function _showMobileMenu(e, callback) {
			var $mmenuContainer = $('.mobile-menu-wrapper .mobile-menu-container');
			Wolmart.$body.addClass('mmenu-active');
			e.preventDefault();

			function initMobileMenu() {
				Wolmart.liveSearch && setTimeout(function () { Wolmart.liveSearch('', $('.mobile-menu-wrapper .search-wrapper')) });
				Wolmart.menu.addToggleButtons('.mobile-menu li');
			}

			if (!$mmenuContainer.find('.mobile-menu').length) {
				var cache = Wolmart.getCache(cache);

				// check cached mobile menu.
				if (cache.mobileMenu && cache.mobileMenuLastTime && wolmart_vars.menu_last_time &&
					parseInt(cache.mobileMenuLastTime) >= parseInt(wolmart_vars.menu_last_time)) {

					// fetch mobile menu from cache
					$mmenuContainer.append(cache.mobileMenu);
					initMobileMenu();
					Wolmart.setCurrentMenuItems('.mobile-menu-wrapper');
				} else {
					// fetch mobile menu from server
					Wolmart.doLoading($mmenuContainer);
					$.post(wolmart_vars.ajax_url, {
						action: "wolmart_load_mobile_menu",
						nonce: wolmart_vars.nonce,
						load_mobile_menu: true,
					}, function (result) {
						result && (result = result.replace(/(class=".*)current_page_parent\s*(.*")/, '$1$2'));
						$mmenuContainer.css('height', '');
						Wolmart.endLoading($mmenuContainer);

						// Add mobile menu search
						$mmenuContainer.append(result);
						initMobileMenu();
						Wolmart.setCurrentMenuItems('.mobile-menu-wrapper');

						// save mobile menu cache
						cache.mobileMenuLastTime = wolmart_vars.menu_last_time;
						cache.mobileMenu = result;
						Wolmart.setCache(cache);

						if (typeof callback == 'function') {
							callback();
						}
					});
				}
			} else {
				initMobileMenu();

				if (typeof callback == 'function') {
					callback();
				}
			}
		}

		function _hideMobileMenu(e) {
			if (e && e.type && 'resize' == e.type && !Wolmart.windowResized(e.timeStamp)) {
				return;
			}
			e.preventDefault();
			Wolmart.$body.removeClass('mmenu-active');
		}

		var _initMegaMenu = function () {
			// calc megamenu position
			function _recalcMenuPosition() {
				$('nav .menu.horizontal-menu .megamenu, .elementor-widget .recent-dropdown').each(function () {
					var $this = $(this),
						o = $this.offset(),
						left = o.left - parseInt($this.css('margin-left')),
						outerWidth = $this.outerWidth(),
						offsetLeft = (left + outerWidth) - (window.innerWidth - 20);

					if ($this.hasClass('full-megamenu') && 0 == $this.closest('.container-fluid').length) {
						$this.css("margin-left", ($(window).width() - outerWidth) / 2 - left + 'px');
					} else if (offsetLeft > 0 && left > 20) {
						$this.css("margin-left", -offsetLeft + 'px');;
					}

					$this.addClass('executed');

				});
			}

			if ($('.toggle-menu.dropdown').length) {
				var $togglebtn = $('.toggle-menu.dropdown .vertical-menu');
				var toggleBtnTop = $togglebtn.length > 0 && $togglebtn.offset().top,
					verticalTop = toggleBtnTop;

				$('.vertical-menu .menu-item-has-children').on('mouseenter', function (e) {
					var $this = $(this);
					if ($this.children('.megamenu').length) {
						var $item = $this.children('.megamenu'),
							offset = $item.offset(),
							top = offset.top - parseInt($item.css('margin-top')),
							outerHeight = $item.outerHeight();

						if (window.pageYOffset > toggleBtnTop) {
							verticalTop = $this.closest('.menu').offset().top;
						} else {
							verticalTop = toggleBtnTop;
						};

						if (typeof (verticalTop) !== 'undefined' && top >= verticalTop) {
							var offsetTop = (top + outerHeight) - window.innerHeight - window.pageYOffset;
							if (offsetTop <= 0) {
								$item.css("margin-top", "0px");
							} else if (offsetTop < top - verticalTop) {
								$item.css("margin-top", -(offsetTop + 5) + 'px');
							} else {
								$item.css("margin-top", -(top - verticalTop) + 'px')
							}
						}
					}
				}
				);
			}

			_recalcMenuPosition();
			Wolmart.$window.on('resize recalc_menus', _recalcMenuPosition);
		}

		return {
			init: function () {
				this.initMenu();
				this.initFilterMenu();
				this.initCollapsibleWidget();
				this.initCollapsibleWidgetToggle();
			},
			initMenu: function ($selector) {
				if (typeof $selector == 'undefined') {
					$selector = '';
				}

				Wolmart.$body
					// no link
					.on('click', $selector + ' .menu-item .nolink', Wolmart.preventDefault)
					// mobile menu
					.on('click', '.mobile-menu-toggle', _showMobileMenu)
					.on('click', '.mobile-menu-overlay', _hideMobileMenu)
					.on('click', '.mobile-menu-close', _hideMobileMenu)
					.on('click', '.mobile-item-categories.show-categories-menu', function (e) {
						_showMobileMenu(e, function () {
							$('.mobile-menu-container .nav a[href="#categories"]').trigger('click');
						});
					})

				window.addEventListener('resize', _hideMobileMenu, { passive: true });

				this.addToggleButtons($selector + ' .collapsible-menu li');

				// toggle dropdown
				Wolmart.$body.on("click", '.dropdown-menu-toggle', Wolmart.preventDefault);


				// megamenu
				setTimeout(_initMegaMenu);

				// lazyload menu image
				wolmart_vars.lazyload && Wolmart.call(function () {
					$('.megamenu [data-lazy]').each(function () {
						Wolmart._lazyload_force(this);
					})
				});
			},
			addToggleButtons: function (selector) {
				Wolmart.$(selector).each(function () {
					var $this = $(this);
					if ($this.hasClass('menu-item-has-children') && !$this.children('a').children('.toggle-btn').length && $this.children('ul').text().trim()) {
						$this.children('a').each(function () {
							var span = document.createElement('span');
							span.className = "toggle-btn";
							this.append(span);
						})
					}
				});
			},
			initFilterMenu: function () {
				Wolmart.$body.on('click', '.with-ul > a i, .menu .toggle-btn, .mobile-menu .toggle-btn', function (e) {
					var $this = $(this);
					var $ul = $this.parent().siblings(':not(.count)');
					if ($ul.length > 1) {
						$this.parent().toggleClass("show").next(':not(.count)').slideToggle(300);
					} else if ($ul.length > 0) {
						$ul.slideToggle(300).parent().toggleClass("show");
					}
					setTimeout(function () {
						$this.closest('.sticky-sidebar').trigger('recalc.pin');
					}, 320);
					e.preventDefault();
				});
			},
			initCollapsibleWidgetToggle: function (selector) {
				$('.widget .product-categories li').add('.sidebar .widget.widget_categories li').add('.widget .product-brands li').add('.store-cat-stack-dokan li').each(function () { // updated(47(
					if (this.lastElementChild && this.lastElementChild.tagName === 'UL') {
						var i = document.createElement('i');
						i.className = "w-icon-angle-down";
						this.classList.add('with-ul');
						this.classList.add('cat-item');
						this.firstElementChild.appendChild(i);
					}
				});

				Wolmart.$('undefined' == typeof selector ? '.sidebar .widget-collapsible .widget-title' : selector)
					.each(function () {
						var $this = $(this);
						if ($this.closest('.top-filter-widgets').length ||
							$this.closest('.toolbox-horizontal').length ||  // if in shop pages's top-filter sidebar
							$this.siblings('.slider-wrapper').length) {
							return;
						}
						// generate toggle icon
						if (!$this.children('.toggle-btn').length) {
							var span = document.createElement('span');
							span.className = 'toggle-btn';
							this.appendChild(span);
						}
					});
			},
			initCollapsibleWidget: function () {
				// slideToggle
				Wolmart.$body.on('click', '.sidebar .widget-collapsible .widget-title', function (e) {
					var $this = $(e.currentTarget);

					if ($this.closest('.top-filter-widgets').length ||
						$this.closest('.toolbox-horizontal').length ||  // if in shop pages's top-filter sidebar
						$this.siblings('.slider-wrapper').length ||
						$this.hasClass('sliding')) {
						return;
					}
					var $content = $this.siblings('*:not(script):not(style)');
					$this.hasClass("collapsed") || $content.css('display', 'block');
					$this.addClass("sliding");
					$content.slideToggle(300, function () {
						$this.removeClass("sliding");
						Wolmart.$window.trigger('update_lazyload');
						$('.sticky-sidebar').trigger('recalc.pin');
					});
					$this.toggleClass("collapsed");
				});
			}
		}
	})();

	/**
	 * Open magnific popup
	 *
	 * @since 1.0
	 * @param {Object} options
	 * @param {string} preset
	 * @return {void}
	 */
	Wolmart.popup = function (options, preset) {
		var mpInstance = $.magnificPopup.instance;
		// if something is already opened, retry after 5seconds
		if (mpInstance.isOpen) {
			if (mpInstance.content) {
				setTimeout(function () {
					Wolmart.popup(options, preset);
				}, 5000);
			} else {
				$.magnificPopup.close();
			}
		} else {
			// if nothing is opened, open new
			$.magnificPopup.open(
				$.extend(true, {},
					Wolmart.defaults.popup,
					preset ? Wolmart.defaults.popupPresets[preset] : {},
					options
				)
			);
		}
	}

	/**
	 * Initialize sidebar
	 * Sidebar active class will be added to body tag : "sidebar class" + "-active"
	 * 
	 * @class Sidebar
	 * @since 1.0
	 * @param {string} name
	 * @return {Sidebar}
	 */
	Wolmart.sidebar = (function () {
		function Sidebar(name) {
			return this.init(name);
		}

		Sidebar.prototype.init = function (name) {
			var self = this;

			self.name = name;
			self.$sidebar = $('.' + name);
			// self.isNavigation = false;

			// If sidebar exists
			if (self.$sidebar.length) {
				Wolmart.$window.on('resize', function (e) {
					if (Wolmart.windowResized(e.timeStamp)) {
						Wolmart.$body.removeClass(name + '-active');
						$('.page-wrapper, .sticky-content').css({ 'margin-left': '', 'margin-right': '' });
					}
				});

				// Register toggle event
				self.$sidebar.find('.sidebar-toggle, .sidebar-toggle-btn')
					.add('.' + name + '-toggle')
					.on('click', function (e) {
						self.toggle();
						e.preventDefault();
						Wolmart.$window.trigger('update_lazyload');
						$('.sticky-sidebar').trigger('recalc.pin.left', [400]);
					});

				// Register close event
				self.$sidebar.find('.sidebar-overlay, .sidebar-close')
					.on('click', function (e) {
						e.stopPropagation();
						self.toggle('close');
						e.preventDefault();
						$('.sticky-sidebar').trigger('recalc.pin.left', [400]);
					});

				/* Added 2021-11-29, from v1.1.5*/
				// Keep sub categories menu open after refresh sidebar
				if ($('.current-cat-parent ul').length) {
					$('.current-cat-parent ul').css('display', 'block');
				}

				// run lazyload on scroll
				self.$sidebar.find('.sidebar-content').on('scroll', function () {
					Wolmart.$window.trigger('update_lazyload');
				});
			}
			return false;
		}

		Sidebar.prototype.toggle = function (mode) {
			var isOpened = Wolmart.$body.hasClass(this.name + '-active');
			if (mode && mode == 'close' && !isOpened) {
				return;
			}

			var width = $('.' + this.name + ' .sidebar-content').outerWidth();
			var marginLeft = isOpened ? '' : ('right-sidebar' == this.name ? - width : width);
			var marginRight = isOpened ? '' : ('right-sidebar' == this.name ? width : - width);

			// move close button because of scroll bar width
			this.$sidebar.find('.sidebar-overlay .sidebar-close').css('margin-left', - (window.innerWidth - document.body.clientWidth));

			// activate sidebar
			Wolmart.$body.toggleClass(this.name + '-active').removeClass('closed');

			// move page wrapper
			if (window.innerWidth <= 992) {
				$('.page-wrapper').css({ 'margin-left': marginLeft, 'margin-right': marginRight });

				// move sticky contents
				$('.sticky-content.fixed').css({ 'transition': 'opacity .5s, margin .4s', 'margin-left': marginLeft, 'margin-right': marginRight });
				setTimeout(function () {
					$('.sticky-content.fixed').css('transition', 'opacity .5s');
				}, 400);
			}

			Wolmart.call(Wolmart.refreshLayouts, 300);
		}

		Wolmart.$window.on('wolmart_complete', function () {
			$('.sidebar').length && Wolmart.$window.smartresize(function () {
				setTimeout(function () {
					Wolmart.$window.trigger('update_lazyload');
				}, 300);
			});
		})

		return function (name) {
			return new Sidebar().init(name);
		}
	})();

	/**
	 * Create minipopup object
	 * 
	 * @class Minipopup
	 * @since 1.0
	 * @return {Object} Minipopup
	 */
	Wolmart.minipopup = (function () {
		var timerInterval = 200;
		var $area;
		var boxes = [];
		var timers = [];
		var isPaused = false;
		var timerId = false;
		var timerClock = function () {
			if (isPaused) {
				return;
			}
			for (var i = 0; i < timers.length; ++i) {
				(timers[i] -= timerInterval) <= 0 && this.close(i--);
			}
		}

		return {
			init: function () {
				// init area
				var area = document.createElement('div');
				area.className = "minipopup-area";
				$(Wolmart.byClass('page-wrapper')).append(area);

				$area = $(area);

				// call methods
				this.close = this.close.bind(this);
				timerClock = timerClock.bind(this);
			},
			open: function (options, callback) {
				var self = this,
					settings = $.extend(true, {}, Wolmart.defaults.minipopup, options),
					$box;

				$box = $(settings.content);

				// open
				$box.find("img").on('load', function () {
					setTimeout(function () {
						$box.addClass('show');
					}, 300);
					if ($box.offset().top - window.pageYOffset < 0) {
						self.close();
					}
					$box.on('mouseenter', function () {
						self.pause();
					});
					$box.on('mouseleave', function (e) {
						self.resume();
					});

					$box[0].addEventListener('touchstart', function (e) {
						self.pause();
						e.stopPropagation();
					}, { passive: true });

					Wolmart.$body[0].addEventListener('touchstart', function () {
						self.resume();
					}, { passive: true });

					$box.on('mousedown', function () {
						$box.css('transform', 'translateX(0) scale(0.96)');
					});
					$box.on('mousedown', 'a', function (e) {
						e.stopPropagation();
					});
					$box.on('mouseup', function () {
						self.close(boxes.indexOf($box));
					});
					$box.on('mouseup', 'a', function (e) {
						e.stopPropagation();
					});

					boxes.push($box);
					timers.push(settings.delay);

					(timers.length > 1) || (
						timerId = setInterval(timerClock, timerInterval)
					);

					callback && callback($box);
				}).on('error', function () {
					$box.remove();
				});
				$box.appendTo($area);
			},
			close: function (indexToClose) {
				var self = this;
				var index = ('undefined' === typeof indexToClose) ? 0 : indexToClose;
				var $box = boxes.splice(index, 1)[0];

				if ($box) {
					// remove timer
					timers.splice(index, 1)[0];

					// remove box
					$box.css('transform', '').removeClass('show');
					self.pause();

					setTimeout(function () {
						var $next = $box.next();
						if ($next.length) {
							$next.animate({
								'margin-bottom': -1 * $box[0].offsetHeight - 20
							}, 300, 'easeOutQuint', function () {
								$next.css('margin-bottom', '');
								$box.remove();
							});
						} else {
							$box.remove();
						}
						self.resume();
					}, 300);

					// clear timer
					boxes.length || clearTimeout(timerId);
				}
			},
			pause: function () {
				isPaused = true;
			},
			resume: function () {
				isPaused = false;
			}
		}
	})();

	/**
	 * Cart Popup
	 * 
	 * @since 1.4.0
	 */
	Wolmart.cartpopup = (function () {
		var $area;
		var templateHTML = '<div class="cart-popup-wrapper"><div class="cart-popup-overlay"></div><div class="cart-popup-content"><h3 class="cart-popup-title">' + wp.i18n.__('Added To Cart!', 'wolmart') + '<i class="popup-close mfp-close"></i></h3><div class="cart-item-wrapper"></div><h4 class="related-products-title">' + wp.i18n.__('You may also like', 'wolmart') + '</h4><div class="related-products-wrapper"></div><a href="#" class="btn btn-sm btn-dark btn-continue">' + wp.i18n.__('Continue Shopping', 'wolmart') + '</a></div></div>';

		return {
			init: function () {
				// init area
				var area = document.createElement('div');
				area.className = "cart-popup-area";
				$(Wolmart.byClass('page-wrapper')).append(area);

				$area = $(area);

				// Close popup.
				Wolmart.$body.on('click', '.cart-popup-wrapper .popup-close, .cart-popup-wrapper .cart-popup-overlay, .cart-popup-wrapper .btn-continue', function (e) {
					var $popup = $(this).closest('.cart-popup-wrapper');
					$popup.removeClass('show');

					e.preventDefault();
				});
			},
			open: function (item, productId) {
				// Append HTML
				$area.html(templateHTML);
				$area.find('.cart-item-wrapper').html(item);

				// Get Related Products By Ajax Loading.
				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: wolmart_vars.ajax_url,
					data: {
						action: 'wolmart_cart_related_products',
						product_id: productId,
					},
					success: function (result) {
						if (result['data']) {
							var products = result['data']['html'];
							// Insert Products HTML.
							$area.find('.related-products-wrapper').html(products);

							// Init related products functionality.
							Wolmart.shop.initProducts($area);
						}

						// Show Popup
						$area.find('.cart-popup-wrapper').addClass('show');
					}
				});
			}
		};
	})();


	/**
	 * Create product gallery object
	 * 
	 * @class ProductGallery
	 * @since 1.0
	 * @param {string|jQuery} selector
	 * @return {void}
	 */
	Wolmart.createProductGallery = (function () {
		function ProductGallery($el) {
			return this.init($el);
		}

		var firstScrollTopOnSticky = true;

		function setupThumbs(self) {
			self.$thumbs = self.$wc_gallery.find('.product-thumbs');
			self.$thumbsDots = self.$thumbs.children();
			self.isVertical = self.$thumbs.parent().parent().hasClass('pg-vertical');
			self.$thumbsWrap = self.$thumbs.parent();

			// # setup thumbs slider
			Wolmart.slider(self.$thumbs, {}, true);

			// # refresh thumbs
			self.isVertical && window.addEventListener('resize', function () {
				Wolmart.requestTimeout(function () {
					self.$thumbs.data('slider') && self.$thumbs.data('slider').update();
				}, 100)
			}, { passive: true });
		}

		// Public Properties

		ProductGallery.prototype.init = function ($wc_gallery) {
			var self = this;

			// If woocommmerce product gallery is undefined, create it
			typeof $wc_gallery.data('product_gallery') == 'undefined' && $wc_gallery.wc_product_gallery();
			this.$wc_gallery = $wc_gallery;
			this.wc_gallery = $wc_gallery.data('product_gallery');

			// Remove woocommerce zoom triggers
			$('.woocommerce-product-gallery__trigger').remove();

			// Add full image trigger, and init zoom
			this.$slider = $wc_gallery.find('.product-single-carousel');

			if (this.$slider.length) {
				this.initThumbs(); // init thumbs together for single slider
			} else {
				this.$slider = this.$wc_gallery.find('.product-gallery-carousel');
				if (this.$slider.length) {	// gallery slider
					this.$slider.on('initialized.slider', this.initZoom.bind(this));
				} else { // other types
					this.initZoom();
				}
			}

			// Prevent going to image link
			$wc_gallery
				.off('click', '.woocommerce-product-gallery__image a')
				.on('click', Wolmart.preventDefault);

			if (!$wc_gallery.closest('.product-quickview').length && !$wc_gallery.closest('.product-widget').length) {
				// If only single product page
				$wc_gallery.on('click', '.woocommerce-product-gallery__wrapper .product-image-full', this.openImageFull.bind(this));

				// Initialize sticky thumbs type.
				if ($wc_gallery.find('.product-sticky-thumbs').length) {
					$wc_gallery.on('click', '.product-sticky-thumbs img', this.clickStickyThumbnail.bind(this));
					window.addEventListener('scroll', this.scrollStickyThumbnail.bind(this), { passive: true });
				}
			}

			// init slider after load, such as quickview
			if ('complete' === Wolmart.status) {
				self.$slider && self.$slider.length && Wolmart.slider(self.$slider);
			}

			Wolmart.$window.on('wolmart_complete', function () {
				setTimeout(self.initAfterLazyload.bind(self), 200);
			})
		}

		ProductGallery.prototype.initAfterLazyload = function () {
			this.currentPostImageSrc = this.$wc_gallery.find('.wp-post-image').attr('src');
		}

		/**
		 * Intialize thumbs in vertical thumbs type
		 * 
		 * @since 1.0
		 */
		ProductGallery.prototype.initThumbs = function () {
			var self = this;

			setupThumbs(self);

			// init thumbs
			this.$slider
				.on('initialized.slider', function (e) {
					// init thumbnails
					self.initZoom();
				})
		}

		ProductGallery.prototype.openImageFull = function (e) {
			if (e.target.classList.contains('zoomImg')) {
				return;
			}
			if (wc_single_product_params.photoswipe_options) {
				e.preventDefault();

				// var carousel = this.$wc_gallery.find('.product-single-carousel, .product-gallery-carousel').data('slider');

				// // Carousel Type
				// if (carousel) {
				// 	wc_single_product_params.photoswipe_options.index = carousel.activeIndex;
				// 	// 	var count = carousel.items().length - carousel.clones().length;
				// 	// 	wc_single_product_params.photoswipe_options.index = ($(e.currentTarget).closest('.slider-slide').index() - carousel.clones().length / 2 + count) % count;
				// }

				// Carousel Type
				var carousel = this.$wc_gallery.find('.product-single-carousel').data('slider');
				if (carousel) {
					wc_single_product_params.photoswipe_options.index = carousel.activeIndex;
				}
				// else if (this.$wc_gallery.find('.product-gallery-carousel').length) {
				// 	wc_single_product_params.photoswipe_options.index = $(e.currentTarget).closest('.woocommerce-product-gallery__image').index();
				// }
				if (this.wc_gallery.$images.filter('.yith_featured_content').length) {
					wc_single_product_params.photoswipe_options.index = carousel ? carousel.activeIndex - 1 : $(e.currentTarget).closest('.woocommerce-product-gallery__image').index() - 1;
				}

				this.wc_gallery.openPhotoswipe(e);

				// to disable elementor's light box.
				e.stopPropagation();
			}
		}

		/**
		 * Event handler triggered when sticky thumbnail is clicked
		 *
		 * @since 1.0
		 * @param {Event} e Mouse click event
		 */
		ProductGallery.prototype.clickStickyThumbnail = function (e) {
			var self = this;
			var $thumb = $(e.currentTarget);

			$thumb.addClass('active').siblings('.active').removeClass('active');
			this.isStickyScrolling = true;
			Wolmart.scrollTo(this.$wc_gallery.find('.product-sticky-images > :nth-child(' + ($thumb.index() + 1) + ')'));
			setTimeout(function () {
				self.isStickyScrolling = false;
			}, 300);
		}

		/**
		 * Event handler triggered while scrolling on sticky thumbnails
		 *
		 * @since 1.0
		 */
		ProductGallery.prototype.scrollStickyThumbnail = function () {
			var self = this;
			if (this.isStickyScrolling) {
				return;
			}
			this.$wc_gallery.find('.product-sticky-images img:not(.zoomImg)').each(function () {
				if (Wolmart.isOnScreen(this)) {
					self.$wc_gallery.find('.product-sticky-thumbs-inner > :nth-child(' +
						($(this).closest('.woocommerce-product-gallery__image').index() + 1) + ')')
						.addClass('active').siblings().removeClass('active');
					return false;
				}
			});
		}

		ProductGallery.prototype.initZoomImage = function (zoomTarget) {
			if (wolmart_vars.single_product.zoom_enabled) {
				var $img = zoomTarget.children('img'),
					width = $img.attr('data-large_image_width'),
					// zoom option
					zoom_options = $.extend({
						touch: false
					}, wolmart_vars.single_product.zoom_options);

				$img.attr('data-src', $img.attr('data-large_image'));

				if ('ontouchstart' in document.documentElement) {
					zoom_options.on = 'click';
				}

				zoomTarget.trigger('zoom.destroy').children('.zoomImg').remove();

				// zoom
				if ('undefined' != typeof width && zoomTarget.width() < width) {
					zoomTarget.zoom(zoom_options);

					// show zoom on hover
					setTimeout(function () {
						zoomTarget.find(':hover').length && zoomTarget.trigger('mouseover');
					}, 100);
				}
			}
		}

		ProductGallery.prototype.changePostImage = function (variation) {

			var $image = this.$wc_gallery.find('.wp-post-image');

			// Has post image been changed?
			if ($image.hasClass('w-lazyload') || this.currentPostImageSrc == $image.attr('src')) {
				return;
			} else {
				this.currentPostImageSrc = $image.attr('src');
			}

			// Add found class to form, change nav thumbnail image on found variation
			var $postThumbImage = this.$wc_gallery.find('.product-thumbs img').eq(0),
				$gallery = this.$wc_gallery.find('.product-gallery');

			if ($postThumbImage.length) {
				if (typeof variation != 'undefined') {
					if ('reset' == variation) {
						$postThumbImage.wc_reset_variation_attr('src');
						$postThumbImage.wc_reset_variation_attr('srcset');
						$postThumbImage.wc_reset_variation_attr('sizes');
						$postThumbImage.wc_reset_variation_attr('alt');
					} else {
						$postThumbImage.wc_set_variation_attr('src', variation.image.gallery_thumbnail_src);
						variation.image.alt && $postThumbImage.wc_set_variation_attr('alt', variation.image.alt);
						variation.image.srcset && $postThumbImage.wc_set_variation_attr('srcset', variation.image.srcset);
						variation.image.sizes && $postThumbImage.wc_set_variation_attr('sizes', variation.image.sizes);
					}
				} else {
					$postThumbImage.wc_set_variation_attr('src', this.currentPostImageSrc);
					$image.attr('srcset') && $postThumbImage.wc_set_variation_attr('srcset', $image.attr('srcset'));
					$image.attr('sizes') && $postThumbImage.wc_set_variation_attr('sizes', $image.attr('sizes'));
					$image.attr('alt') && $postThumbImage.wc_set_variation_attr('alt', $image.attr('alt'));
				}
			}

			// Refresh zoom
			this.initZoomImage($image.parent());

			// Refresh if carousel layout
			var carousel = $gallery.children('.product-single-carousel,.product-gallery-carousel').data('slider');
			carousel && (carousel.update());

			if (!firstScrollTopOnSticky) {
				// If sticky, go to top;
				if (this.$wc_gallery.closest('.product').find('.sticky-sidebar .summary').length) {
					Wolmart.scrollTo(this.$wc_gallery, 400);
				}
			}
			firstScrollTopOnSticky = false;
		}

		ProductGallery.prototype.initZoom = function () {
			if (wolmart_vars.single_product.zoom_enabled) {
				var self = this;

				// if not quickview, widget
				if (!this.$wc_gallery.closest('.product-quickview').length && !this.$wc_gallery.closest('.product-widget').length) {
					var buttons = '<button class="product-gallery-btn product-image-full w-icon-zoom" aria-label="' + wolmart_vars.texts.product_zoom_btn + '"></button>' + (this.$wc_gallery.data('buttons') || '');
					// show image full toggler
					if (this.$slider.length && this.$slider.hasClass('product-single-carousel')) {
						// if default or horizontal type, show only one
						this.$slider.after(buttons);
					} else {
						// else other types
						this.$wc_gallery.find('.woocommerce-product-gallery__image > a').each(function () {
							if (!$(this).parent().find('.product-gallery-btn').length) {
								$(this).after(buttons);
							}
						});
					}
				}

				// zoom images
				// var ini = 
				Wolmart.appear(this.$wc_gallery[0], () => {
					this.$wc_gallery.find('.woocommerce-product-gallery__image > a').each(function () {
						self.initZoomImage($(this));
					})
						.on('click', function (e) {
							e.stopPropagation();
							e.preventDefault();
						});
				}, { alwaysObserve: false });
			}
		}

		return function (selector) {
			if ($.fn.wc_product_gallery) {
				Wolmart.$(selector).each(function () {
					var $this = $(this);
					$this.data('wolmart_product_gallery', new ProductGallery($this));
				});
			}
		}
	})();

	/**
	 * Initialize product gallery
	 * 
	 * @class ProductGallery
	 * @since 1.0
	 * @param {string|jQuery} selector
	 * @return {void}
	 */
	Wolmart.initProductGallery = function () {
		function onClickImageFull(e) {
			var $btn = $(e.currentTarget);
			e.preventDefault();

			// Default or horizontal type
			if ($btn.siblings('.product-single-carousel').length) {
				$btn.parent().find('.slider-slide-active a').trigger('click');
			} else {
				$btn.prev('a').trigger('click');
			}
		}

		// Image lightbox toggle
		Wolmart.$body.on('click', '.product-image-full', onClickImageFull);
	}

	/**
	 * Create product single object
	 * 
	 * @class ProductSingle
	 * @since 1.0
	 * @param {string|jQuery} selector 
	 * @return {void}
	 */
	Wolmart.createProductSingle = (function () {
		function ProductSingle($el) {
			return this.init($el);
		}

		// Public Properties
		ProductSingle.prototype.init = function ($el) {
			this.$product = $el;

			// gallery
			$el.find('.woocommerce-product-gallery').each(function () {
				if (!$.fn.wc_product_gallery) {
					$(this).on('wc-product-gallery-after-init', function () {
						Wolmart.createProductGallery($(this));
					})
				} else {
					Wolmart.createProductGallery($(this));
				}
			})

			// variation        
			$('.reset_variations').hide().removeClass('d-none');

			// after load, such as quickview
			if ('complete' === Wolmart.status) {
				// variation form
				if ($.fn.wc_variation_form && typeof wc_add_to_cart_variation_params !== 'undefined') {
					this.$product.find('.variations_form').wc_variation_form();
				}

				// quantity input
				Wolmart.quantityInput(this.$product.find('.qty'));

				// countdown
				Wolmart.countdown(this.$product.find('.product-countdown'));
			} else {
				// sticky add to cart cart
				if (!this.$product.hasClass('product-widget') || this.$product.hasClass('product-quickview')) {
					this.stickyCartForm(this.$product.find('.product-sticky-content'));
				}
			}
		}

		/**
		 * Make cart form as sticky
		 * 
		 * @since 1.0
		 * @param {string|jQuery} selector 
		 * @return {void}
		 */
		ProductSingle.prototype.stickyCartForm = function (selector) {
			var $stickyForm = Wolmart.$(selector);

			if ($stickyForm.length != 1) {
				return;
			}

			var $product = $stickyForm.closest('.product');
			var titleEl = $product.find('.product_title').get(0);
			var $image = $product.find('.woocommerce-product-gallery .wp-post-image').eq(0);
			var imageSrc = wolmart_vars.lazyload ? $image.attr('data-lazy') : $image.attr('src');
			var $price = $product.find('p.price');

			if (!imageSrc) {
				imageSrc = $image.attr('src');
			}

			// setup sticky form
			$stickyForm.find('.quantity-wrapper').before(
				'<div class="sticky-product-details">' +
				($image.length ? '<img src="' + imageSrc + '" width="' + $image.attr('width') + '" height="' + $image.attr('height') + '" alt="' + $image.attr('alt') + '">' : '') +
				'<div>' +
				(titleEl ? titleEl.outerHTML.replace('<h1', '<h3').replace('h1>', 'h3>').replace('product_title', 'product-title') : '') +
				($price.length ? $price[0].outerHTML : '') + '</div>'
			);

			var sticky = $stickyForm.data('sticky-content');
			if (sticky) {
				/**
				 * Register getTop function for sticky "add to cart" form, that runs above 768px.
				 * 
				 * @since 1.0
				 */
				sticky.getTop = function () {
					var $parent;
					if ($stickyForm.closest('.sticky-sidebar').length) {
						$parent = $product.find('.woocommerce-product-gallery');
					} else {
						$parent = $stickyForm.closest('.product-single > *');
						if ($parent.hasClass('elementor')) {
							$parent = $stickyForm.closest('.cart');
						}
					}
					return $parent.offset().top + $parent.height();
				}

				sticky.onFixed = function () {
					Wolmart.$body.addClass('addtocart-fixed');
				}

				sticky.onUnfixed = function () {
					Wolmart.$body.removeClass('addtocart-fixed');
				}
			}

			// Fix top in mobile, fix bottom otherwise
			function _changeFixPos() {
				Wolmart.requestTimeout(function () {
					$stickyForm.removeClass('fix-top fix-bottom').addClass(window.innerWidth < 768 ? 'fix-top' : 'fix-bottom');
				}, 50);
			}

			Wolmart.$window.on('sticky_refresh_size.wolmart', _changeFixPos);

			_changeFixPos();
		}

		return function (selector) {
			Wolmart.$(selector).each(function () {
				var $this = $(this);
				$this.data('wolmart_product_single', new ProductSingle($this));
			});
		}
	})();

	/**
	 * Initilize single product page, and register events for single product.
	 *
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.initProductSingle = (function () {

		/**
		 * Initialize add to cart in ajax in single product
		 */
		function initAjaxAddToCart() {
			Wolmart.$body.on('click', '.single_add_to_cart_button', function (e) {

				var $btn = $(e.currentTarget);

				if ($btn.hasClass('disabled') || $btn.hasClass('has_buy_now')) {
					return;
				}

				var $product = $btn.closest('.product-single');
				if (!$product.length || $product.hasClass('product-type-external') || $product.hasClass('product-type-grouped') ||
					!$product.hasClass('product-widget') && !$product.hasClass('product-quickview')) {
					return;
				}
				e.preventDefault();

				var $form = $btn.closest('form.cart');
				if ($form.hasClass('w-loading')) {
					return;
				}

				var variation_id = $form.find('input[name="variation_id"]').val(),
					product_id = variation_id ? $form.find('input[name="product_id"]').val() : $btn.val(),
					quantity = $form.find('input[name="quantity"]').val(),
					$attributes = $form.find('select[data-attribute_name]'),
					data = {
						product_id: variation_id ? variation_id : product_id,
						quantity: quantity
					};

				$attributes.each(function () {
					var $this = $(this);
					data[$this.attr('data-attribute_name')] = $this.val();
				});

				// Initialize ajax url
				var ajax_url = '';

				// Resolve issue. For the variable product that has any type, ajax add to cart does not work
				// in single product widget and quickview
				// 2021-06-20
				if ($product.hasClass('product-widget') || $product.hasClass('product-quickview')) {
					ajax_url = wolmart_vars.ajax_url;
					data.action = 'wolmart_ajax_add_to_cart';
				} else {
					ajax_url = wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart');
				}

				Wolmart.doLoading($btn, 'small');
				$btn.removeClass('added');

				// Trigger event.
				Wolmart.$body.trigger('adding_to_cart', [$btn, data]);

				$.ajax({
					type: 'POST',
					url: ajax_url,
					data: data,
					dataType: 'json',
					success: function (response) {
						if (!response) {
							return;
						}
						if (response.error && response.product_url) {
							location = response.product_url;
							return;
						}

						// Redirect to cart option
						if (wc_add_to_cart_params.cart_redirect_after_add === 'yes') {
							location = wc_add_to_cart_params.cart_url;
							return;
						}

						// trigger event
						$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $btn]);

						// show minipopup box
						var link = $form.attr('action'),
							image = $product.find('.wp-post-image').attr('src'),
							title = $product.find('.product_title').text(),
							price = variation_id ? $form.find('.woocommerce-variation-price .price').html() : $product.find('.price').html(),
							count = parseInt($form.find('.qty').val()),
							id = $product.attr('id');

						price || (price = $product.find('.price').html());

						if ('cart-popup' == wolmart_vars.cart_popup_type) {
							// open cart popup
							Wolmart.cartpopup.open('<div class="product product-list-sm" id="' + id + '">\
								<figure class="product-media"><a href="' + link + '"><img src="' + image + '"></img></a></figure>\
								<div class="product-details"><a class="product-title" href="' + link + '"><span class="cart-count">' + count + '</span> x ' + title + '</a>' + wolmart_vars.texts.cart_suffix + '</div></div>\
								<div class="minipopup-footer">' + '<a href="' + wolmart_vars.pages.cart + '" class="btn btn-sm btn-rounded">' + wolmart_vars.texts.view_cart + '</a><a href="' + wolmart_vars.pages.checkout + '" class="btn btn-sm btn-dark btn-rounded">' + wolmart_vars.texts.view_checkout + '</a></div>', id);
						} else {
							// open mini popup
							var $popup_product = $('.minipopup-area').find("#" + id);

							if (id == $popup_product.attr('id')) {
								$popup_product.find('.cart-count').html(parseInt($popup_product.find('.cart-count').html()) + count);
							} else {
								Wolmart.minipopup.open({
									content: '<div class="minipopup-box">\
										<div class="product product-list-sm" id="' + id + '">\
											<figure class="product-media"><a href="' + link + '"><img src="' + image + '"></img></a></figure>\
											<div class="product-details"><a class="product-title" href="' + link + '"><span class="cart-count">' + count + '</span> x ' + title + '</a>' + wolmart_vars.texts.cart_suffix + '</div></div>\
											<div class="minipopup-footer">' + '<a href="' + wolmart_vars.pages.cart + '" class="btn btn-sm btn-rounded">' + wolmart_vars.texts.view_cart + '</a><a href="' + wolmart_vars.pages.checkout + '" class="btn btn-sm btn-dark btn-rounded">' + wolmart_vars.texts.view_checkout + '</a></div></div>'
								});
							}
						}
					},
					complete: function () {
						Wolmart.endLoading($btn);
					}
				});
			});
		}

		/**
		 * Initiazlie variable product
		 * 
		 * @since 1.0
		 */
		function initVariableProduct() {
			function onClickListVariation(e) {
				var $btn = $(e.currentTarget);
				if ($btn.hasClass('disabled')) {
					return;
				}
				if ($btn.hasClass('active')) {
					$btn.removeClass('active')
						.parent().next().val('').change();
				} else {
					$btn.addClass('active').siblings().removeClass('active');
					$btn.parent().next().val($btn.attr('name')).change();
				}
			}

			function onClickResetVariation(e) {
				$(e.currentTarget).closest('.variations_form').find('.active').removeClass('active');
			}

			function onToggleResetVariation() {
				var $reset = $(Wolmart.byClass('reset_variations', this));
				$reset.css('visibility') == 'hidden' ? $reset.hide() : $reset.show();
			}

			function onFoundVariation(e, variation) {

				var $product = $(e.currentTarget).closest('.product');
				// Display product of matched variation.
				var gallery = $product.find('.woocommerce-product-gallery').data('wolmart_product_gallery');
				if (gallery) {
					gallery.changePostImage(variation);
				}

				// Display sale countdown of matched variation.
				var $counter = $product.find('.countdown-variations');
				if ($counter.length) {
					if (variation && variation.is_purchasable && variation.wolmart_date_on_sale_to) {
						var $countdown = $counter.find('.countdown');
						if ($countdown.data('until') != variation.wolmart_date_on_sale_to) {
							Wolmart.countdown($countdown, { until: new Date(variation.wolmart_date_on_sale_to) });
							$countdown.data('until', variation.wolmart_date_on_sale_to);
						}
						$counter.slideDown();
					} else {
						$counter.slideUp();
					}
				}
			}

			function onResetVariation(e) {
				var $product = $(e.currentTarget).closest('.product');
				var $gallery = $product.find('.woocommerce-product-gallery');

				if ($gallery.length) {
					var gallery = $gallery.data('wolmart_product_gallery');
					if (gallery) {
						gallery.changePostImage('reset');
					}
				}

				$product.find('.countdown-variations').slideUp();
			}

			function onUpdateVariation() {
				var $form = $(this);
				$form.find('.product-variations>button').addClass('disabled');

				// Loop through selects and disable/enable options based on selections.
				$form.find('select').each(function () {
					var $this = $(this);
					var $buttons = $this.closest('.variations > *').find('.product-variations');
					$this.children('.enabled').each(function () {
						$buttons.children('[name="' + this.getAttribute('value') + '"]').removeClass('disabled');
					});
					$this.children(':selected').each(function () {
						$buttons.children('[name="' + this.getAttribute('value') + '"]').addClass('active');
					});
				});
			}

			// Variation
			Wolmart.$body.on('click', '.variations .product-variations button, .product-variation-wrapper .product-variations button', onClickListVariation)
				.on('click', '.reset_variations', onClickResetVariation)
				.on('check_variations', '.variations_form', onToggleResetVariation)
				.on('found_variation', '.variations_form', onFoundVariation)
				.on('reset_image', '.variations_form', onResetVariation)
				.on('update_variation_values', '.variations_form', onUpdateVariation)
		}

		/**
		 * Initalize guide link
		 * 
		 * @since 1.0
		 */
		function initGuideLink() {
			// Guide Link
			Wolmart.$body.on('click', '.guide-link', function () {
				var $link = $(this.getAttribute('href') + '>a');
				$link.length && $link.trigger('click');
			});

			if (Wolmart.hash.toLowerCase().indexOf('tab-title-wolmart_pa_block_')) {
				$(Wolmart.hash + '>a').trigger('click');
			}
		}

		/**
		 * Initialize woocommerce product data
		 * 
		 * @since 1.0
		 */
		function initProductData() {
			// Init data tab accordion
			Wolmart.$body.on('init', '.woocommerce-tabs.accordion', function () {
				var $tabs = $(this);
				setTimeout(function () {
					var selector = '';
					if (Wolmart.hash.toLowerCase().indexOf('comment-') >= 0 ||
						Wolmart.hash === '#reviews' || Wolmart.hash === '#tab-reviews' ||
						location.href.indexOf('comment-page-') > 0 || location.href.indexOf('cpage=') > 0) {

						selector = '.reviews_tab a';
					} else if (Wolmart.hash === '#tab-additional_information') {
						selector = '.additional_information_tab a';
					} else {
						selector = '.card:first-child > .card-header a';
					}
					$tabs.find(selector).trigger('click');
				}, 100);
			})
		}

		/**
		 * Initialize woocommerce compatility
		 * 
		 * @since 1.0
		 * @param {string|jQuery} selector
		 */
		function initWooCompatibility(selector) {

			// Initialize product gallery again for skeleton screen.
			if (wolmart_vars.skeleton_screen) {
				// wc product gallery
				if ($.fn.wc_product_gallery) {
					$(selector + ' .woocommerce-product-gallery').each(function () {
						var $this = $(this);
						typeof $this.data('product_gallery') == 'undefined' && $this.wc_product_gallery();
					})
				}
			}

			// Initialize variation form
			if ($.fn.wc_variation_form && typeof wc_add_to_cart_variation_params !== 'undefined') {
				Wolmart.$(selector, '.variations_form').each(function () {
					var $form = $(this);
					if (Wolmart.status != 'load' || $form.closest('.summary').length) {
						var data_a = jQuery._data(this, 'events');
						if (!data_a || !data_a['show_variation']) {
							$form.wc_variation_form();
						} else {
							Wolmart.requestTimeout(function () {
								$form.trigger('check_variations');
							}, 100);
						}
					}
				});
			}

			if (wolmart_vars.skeleton_screen && !Wolmart.$body.hasClass('wolmart-use-vendor-plugin')) {
				// init - wc tab
				$('.wc-tabs-wrapper, .woocommerce-tabs').trigger('init');
				// init - wc rating
				Wolmart.$(selector, '#rating').trigger('init');
			} else {
				$('.woocommerce-tabs.accordion').trigger('init');

				// Compatibility with lazyload
				var $image = Wolmart.$('.woocommerce-product-gallery .wp-post-image');
				if ($image.length) {
					if ($image.attr('data-lazy') && $image.attr('data-o_src') && $image.attr('data-o_src').indexOf('lazy.png') >= 0) {
						$image.attr('data-o_src', $image.attr('data-lazy'));
					}

					if ($image.attr('data-lazyset') && $image.attr('data-o_srcset') && $image.attr('data-o_srcset').indexOf('lazy.png') >= 0) {
						$image.attr('data-o_srcset', $image.attr('data-lazyset'));
					}
				}
			}
		}

		return function (selector) {
			if (typeof selector == 'undefined') {
				selector = '';
			}

			initProductData();
			initWooCompatibility();


			// Single product page
			Wolmart.createProductSingle(selector + '.product-single');
			Wolmart.initProductGallery();

			// Register events
			Wolmart.$window.on('wolmart_complete', function () {
				initAjaxAddToCart();
				initVariableProduct();
				initGuideLink();
			})
		}
	})();

	/**
	 * Initialize shop functions
	 *
	 * @class Shop
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.shop = (function () {

		/**
		 * Initialize shop filter menu for horizontal layout (horizontal filter widgets)
		 * 
		 * @since 1.0
		 */
		function initSelectMenu() {

			function _initSelectMenu() {
				// show selected attributes after loading
				$('.toolbox-horizontal .shop-sidebar .widget .chosen').each(function (e) {
					if ($(this).find('a').attr('href') == window.location.href) {
						return;
					}

					$('<a href="#" class="select-item">' + $(this).find('a').text() + '<i class="w-icon-times-solid"></i></a>')
						.insertBefore('.toolbox-horizontal + .select-items .filter-clean')
						.attr('data-type', $(this).closest('.widget').attr('id').split('-').slice(0, -1).join('-'))
						.data('link_id', $(this).closest('.widget').attr('id'))
						.data('link_idx', $(this).index());

					$('.toolbox-horizontal + .select-items').fadeIn();
				})
			}

			function openMenu(e) {
				// close all select menu
				$(this).parent().siblings().removeClass('opened');
				$(this).parent().toggleClass('opened');
				e.stopPropagation();
			}

			function closeMenu(e) {
				$('.toolbox-horizontal .shop-sidebar .widget, .wolmart-filters .select-ul').removeClass('opened');
			}

			function stopPropagation(e) {
				e.stopPropagation();
			}

			function onAddFilterItem(e) {
				var $this = $(this);

				if ($this.closest('.widget').hasClass('yith-woo-ajax-reset-navigation')) {
					return;
				}

				if ($this.closest('.product-categories').length) {
					$('.toolbox-horizontal + .select-items .select-item').remove();
				}

				if ($this.parent().hasClass('chosen')) {
					$('.toolbox-horizontal + .select-items .select-item')
						.filter(function (i, el) {
							return $(el).data('link_id') == $this.closest('.widget').attr('id') &&
								$(el).data('link_idx') == $this.closest('li').index();
						})
						.fadeOut(function () {
							$(this).remove();

							// if only clean all button remains
							if ($('.select-items').children().length < 2) {
								$('.select-items').hide();
							}
						})
				} else {
					var type = $this.closest('.widget').attr('id').split('-').slice(0, -1).join('-');

					if ('wolmart-price-filter' == type) {
						$('.toolbox-horizontal + .select-items').find('[data-type="wolmart-price-filter"]').remove();
						$this.closest('li').addClass('chosen').siblings().removeClass('chosen');
					}

					$('<a href="#" class="select-item">' + $this.text() + '<i class="w-icon-times-solid"></i></a>')
						.insertBefore('.toolbox-horizontal + .select-items .filter-clean')
						.hide().fadeIn()
						.attr('data-type', type)
						.data('link_id', $this.closest('.widget').attr('id'))
						.data('link_idx', $this.closest('li').index()); // link to anchor

					// if only clean all button remains
					if ($('.select-items').children().length >= 2) {
						$('.select-items').show();
					}
				}
			}

			function onAddFiltersWidgetItem(e) {
				e.preventDefault();
				e.stopPropagation();

				if ('or' == $(this).closest('.wolmart-filter').attr('data-filter-query')) {
					$(this).closest('li').toggleClass('chosen');
				} else {
					$(this).closest('li').toggleClass('chosen').siblings().removeClass('chosen');
				}

				var $btn_filter = $(this).closest('.wolmart-filters').find('.btn-filter'),
					link = $btn_filter.attr('href'),
					$filters = $(this).closest('.wolmart-filters');
				link = link.split('/');
				link[link.length - 1] = '';

				$filters.length && $filters.find('.wolmart-filter').each(function (index) {
					var chosens = $(this).find('.chosen');

					if (chosens.length) {
						var values = [],
							attr = $(this).attr('data-filter-attr');

						chosens.each(function () {
							values.push($(this).attr('data-value'));
						})

						link[link.length - 1] += 'filter_' + attr + '=' + values.join(',') + '&query_type_' + attr + '=' + $(this).attr('data-filter-query') + (index != $filters.length ? '&' : '');
					}
				});

				link[link.length - 1] = '?' + link[link.length - 1];
				$btn_filter.attr('href', link.join('/'));
			}

			function onRemoveFilterItem(e) {
				e.preventDefault();
				var $this = $(this);
				var id = $this.data('link_id');
				if (id) {
					var $link = $('.toolbox-horizontal .shop-sidebar #' + id).find('li').eq($this.data('link_idx')).children('a');
					if ($link.length) {
						if ($link.closest('.product-categories').length) {
							$this.siblings('.filter-clean').trigger('click');
						} else {
							$link.trigger('click');
						}
					}
				}
			}

			function onCleanFilterItems(e) {
				e.preventDefault();

				$(this).parent('.select-items').fadeOut(function () {
					$(this).children('.select-item').remove();
				})
			}

			_initSelectMenu();

			Wolmart.$body
				// show or hide select menu
				.on('click', '.toolbox-horizontal .shop-sidebar .widget-title, .wolmart-filters .select-ul-toggle', openMenu)
				.on('click', '.toolbox-horizontal .shop-sidebar .widget-title + *', stopPropagation) // if click in popup area, not hide it
				.on('click', closeMenu)

				// if select item is clicked
				.on('click', '.toolbox-horizontal .shop-sidebar .widget a', onAddFilterItem)
				.on('click', '.toolbox-horizontal + .select-items .select-item', onRemoveFilterItem)
				.on('click', '.toolbox-horizontal + .select-items .filter-clean', onCleanFilterItems)

				// wolmart filters widget / filter item is clicked
				.on('click', '.wolmart-filters .select-ul a', onAddFiltersWidgetItem);
		}


		/**
		 * Ajax add to cart for variation products
		 * 
		 * @since 1.1.0
		 */
		var initProductsAttributeAction = function () {
			Wolmart.$body
				.on('click', '.product-variation-wrapper button', function (e) {
					var $this = $(this),
						$variation = $this.parent(),
						$wrapper = $this.closest('.product-variation-wrapper'),
						attr = 'attribute_' + String($variation.data('attr')),
						variationData = $wrapper.data('product_variations'),
						attributes = $wrapper.data('product_attrs'),
						attrValue = $this.attr('name'),
						$price = $wrapper.closest('.product-loop').find('.price'),
						priceHtml = $wrapper.data('price');

					if ($this.hasClass('disabled')) {
						return;
					}

					var matchedData = variationData;

					// Get Attributes
					if (undefined == attributes) {
						attributes = [];
						$wrapper.find('.product-variations').each(function () {
							attributes.push('attribute_' + String($(this).data('attr')));
						});
						$wrapper.data('product_attrs', attributes);
					}

					// Save HTML
					if (undefined == priceHtml) {
						priceHtml = $price.html();
						$wrapper.data('price', priceHtml);
					}

					// Update Matched Array
					if (attrValue == $wrapper.data(attr)) {
						$wrapper.removeData(attr);
					} else {
						$wrapper.data(attr, attrValue);
					}
					let tempArray = [];
					variationData.forEach(function (item, index) {
						var flag = true;
						attributes.forEach(function (attr_item) {
							if (undefined != $wrapper.data(attr_item) && $wrapper.data(attr_item) != item['attributes'][attr_item] && "" != item['attributes'][attr_item]) {
								flag = false;
							}
						});
						flag && tempArray.push(item);
					});

					matchedData = tempArray;

					var showPrice = true;
					attributes.forEach(function (attr_item) {
						if (attr != attr_item || (attr_item == attr && undefined == $wrapper.data(attr))) {
							let $variation = $wrapper.find('.' + attr_item.slice(10) + ' > *:not(.guide-link)');

							$variation.each(function () {
								var $this = $(this);
								if (!$this.hasClass('select-box')) {
									$this.addClass('disabled');
								} else {
									$this.find('option').css('display', 'none');
								}
							})

							variationData.forEach(function (item) {
								let flag = true;
								attributes.forEach(function (atr_item) {
									if (undefined != $wrapper.data(atr_item) && attr_item != atr_item && item['attributes'][atr_item] != $wrapper.data(atr_item) && "" != item['attributes'][atr_item]) {
										flag = false;
									}
								});
								if (true == flag) {
									if ("" == item['attributes'][attr_item]) {
										$variation.removeClass('disabled');
										$variation.each(function () {
											var $this = $(this);
											if (!$this.hasClass('select-box')) {
												$this.removeClass('disabled');
											} else {
												$this.find('option').css('display', '');
											}
										})
									} else {
										$variation.each(function () {
											var $this = $(this);
											if (!$this.hasClass('select-box')) {
												if ($this.attr('name') == item['attributes'][attr_item]) {
													$this.removeClass('disabled');
												}
											} else {
												$this.find('option').each(function () {
													var $this = $(this);
													if ($this.attr('value') == item['attributes'][attr_item] || $this.attr('value') == '') {
														$this.css('display', '');
													}
												});
											}
										});
									}
								}
							});
						}
						if (undefined == $wrapper.data(attr_item)) {
							showPrice = false;
						}
					});

					if (true == showPrice && 1 == matchedData.length && (!matchedData[0].availability_html || matchedData[0].availability_html.indexOf('out-of-stock') < 0)) {
						$price.closest('.product-loop').data('variation', matchedData[0]['variation_id']);
						$price.html($(matchedData[0]['price_html']).html());
						$price.closest('.product-loop').find('.add_to_cart_button')
							.removeClass('product_type_variable')
							.addClass('product_type_simple');

						$price.closest('.product-loop').find('.add_to_cart_button').text(wolmart_vars.texts.add_to_cart);
					} else {
						$price.html(priceHtml);
						$price.closest('.product-loop').removeData('variation')
							.find('.add_to_cart_button')
							.removeClass('product_type_simple')
							.addClass('product_type_variable');

						$price.closest('.product-loop').find('.add_to_cart_button').text(wolmart_vars.texts.select_options);
					}
				})
				.on('change', '.product-variation-wrapper select', function (e) {
					var $this = $(this),
						$variation = $this.parent(),
						$wrapper = $this.closest('.product-variation-wrapper'),
						attr = $this.data('attribute_name'),
						variationData = $wrapper.data('product_variations'),
						attributes = $wrapper.data('product_attrs'),
						attrValue = $this.val(),
						$price = $wrapper.closest('.product-loop').find('.price'),
						priceHtml = $wrapper.data('price');


					var matchedData = variationData;

					// Get Attributes
					if (undefined == attributes) {
						attributes = [];
						$wrapper.find('.product-variations').each(function () {
							attributes.push('attribute_' + String($(this).data('attr')));
						});
						$wrapper.data('product_attrs', attributes);
					}

					// Save HTML
					if (undefined == priceHtml) {
						priceHtml = $price.html();
						$wrapper.data('price', priceHtml);
					}


					// Update Matched Array
					if ("" == attrValue) {
						$wrapper.removeData(attr);
						let tempArray = [];
						variationData.forEach(function (item, index) {
							var flag = true;
							attributes.forEach(function (attr_item) {
								if (undefined != $wrapper.data(attr_item) && $wrapper.data(attr_item) != item['attributes'][attr_item] && "" != item['attributes'][attr_item]) {
									flag = false;
								}
							});
							if (flag) {
								tempArray.push(item);
							}
						});

						matchedData = tempArray;
					} else {
						$wrapper.data(attr, attrValue);
						let tempArray = [];
						variationData.forEach(function (item, index) {
							var flag = true;
							attributes.forEach(function (attr_item) {
								if (undefined != $wrapper.data(attr_item) && $wrapper.data(attr_item) != item['attributes'][attr_item] && "" != item['attributes'][attr_item]) {
									flag = false;
								}
							});
							if (flag) {
								tempArray.push(item);
							}
						});

						matchedData = tempArray;
					}

					var showPrice = true;
					attributes.forEach(function (attr_item) {
						if (attr != attr_item || (attr_item == attr && undefined == $wrapper.data(attr))) {
							let $variation = $wrapper.find('.' + attr_item.slice(10) + ' > *');

							$variation.each(function () {
								var $this = $(this);
								if (!$this.hasClass('select-box')) {
									$this.addClass('disabled');
								} else {
									$this.find('option').css('display', 'none');
								}
							});

							variationData.forEach(function (item) {
								let flag = true;
								attributes.forEach(function (atr_item) {
									if (undefined != $wrapper.data(atr_item) && attr_item != atr_item && item['attributes'][atr_item] != $wrapper.data(atr_item) && "" != item['attributes'][atr_item]) {
										flag = false;
									}
								});
								if (true == flag) {
									if ("" == item['attributes'][attr_item]) {
										$variation.removeClass('disabled');
										$variation.each(function () {
											var $this = $(this);
											if (!$this.hasClass('select-box')) {
												$this.removeClass('disabled');
											} else {
												$this.find('option').css('display', '');
											}
										});
									} else {
										$variation.each(function () {
											var $this = $(this);
											if (!$this.hasClass('select-box')) {
												if ($this.attr('name') == item['attributes'][attr_item]) {
													$this.removeClass('disabled');
												}
											} else {
												$this.find('option').each(function () {
													var $this = $(this);
													if ($this.attr('value') == item['attributes'][attr_item] || $this.attr('value') == '') {
														$this.css('display', '');
													}
												});
											}
										});
									}
								}
							});
						}
						if (undefined == $wrapper.data(attr_item)) {
							showPrice = false;
						}
					});

					if (true == showPrice && 1 == matchedData.length && (!matchedData[0].availability_html || (matchedData[0].availability_html && matchedData[0].availability_html.indexOf('out-of-stock') < 0))) {
						$price.closest('.product-loop').data('variation', matchedData[0]['variation_id']);
						$price.html($(matchedData[0]['price_html']).html());
						$price.closest('.product-loop').find('.add_to_cart_button')
							.removeClass('product_type_variable')
							.addClass('product_type_simple');
					} else {
						$price.html(priceHtml);
						$price.closest('.product-loop').removeData('variation')
							.find('.add_to_cart_button')
							.removeClass('product_type_simple')
							.addClass('product_type_variable');
					}
				})
				.on('click', '.product-loop.product-type-variable .add_to_cart_button', function (e) {
					var $this = $(this),
						$variations = $this.closest('.product').find('.product-variation-wrapper'),
						attributes = $variations.data('product_attrs'),
						$product = $this.closest('.product-loop');

					if (undefined != $product.data('variation')) {
						let data = {
							action: "wolmart_add_to_cart",
							product_id: $product.data('variation'),
							quantity: 1
						};
						attributes.forEach(function (item) {
							data[item] = $variations.data(item);
						});
						$.ajax({
							type: 'POST',
							dataType: 'json',
							url: wolmart_vars.ajax_url,
							data: data,
							success: function (response) {
								$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $this]);
							}
						});
						e.preventDefault();
					}
				});
		}

		/**
		 * Initialize products quickview action
		 * 
		 * @since 1.0
		 */
		function initProductsQuickview() {

			Wolmart.$body.on('click', '.btn-quickview', function (e) {
				e.preventDefault();

				var $this = $(this);
				var ajax_data = {
					action: 'wolmart_quickview',
					product_id: $this.data('product')
				};
				var quickviewType = wolmart_vars.quickview_type || 'loading';
				if (quickviewType == 'zoom' && window.innerWidth < 768) {
					quickviewType = 'loading';
				}

				if ($this.closest('.shop_table').length) {
					Wolmart.doLoading($this, 'small');
				}

				function finishQuickView() {
					Wolmart.createProductSingle('.mfp-product .product-single');
					if ($this.closest('.shop_table').length) {
						Wolmart.endLoading($this);
					}
				}

				function openQuickview(quickviewType) {
					Wolmart.popup({
						type: 'ajax',
						mainClass: 'mfp-product mfp-fade' + (quickviewType == 'offcanvas' ? ' mfp-offcanvas' : ''),
						items: {
							src: wolmart_vars.ajax_url
						},
						ajax: {
							settings: {
								method: 'POST',
								data: ajax_data
							},
							cursor: 'mfp-ajax-cur', // CSS class that will be added to body during the loading (adds "progress" cursor)
							tError: '<div class="alert alert-warning alert-round alert-inline">' + wolmart_vars.texts.popup_error + '<button type="button" class="btn btn-link btn-close"><i class="close-icon"></i></button></div>'
						},
						preloader: false,
						callbacks: {
							afterChange: function () {
								var skeletonTemplate;
								if (wolmart_vars.skeleton_screen) {
									var extraClass = wolmart_vars.quickview_thumbs == 'horizontal' ? '' : ' pg-vertical';
									if (quickviewType == 'offcanvas') {
										skeletonTemplate = '<div class="product skeleton-body' + extraClass + '"><div class="skel-pro-gallery"></div><div class="skel-pro-summary" style="margin-top: 20px"></div></div>';;
									} else {
										skeletonTemplate = '<div class="product skeleton-body row"><div class="col-md-6' + extraClass + '"><div class="skel-pro-gallery"></div></div><div class="col-md-6"><div class="skel-pro-summary"></div></div></div>';
									}
								} else {
									skeletonTemplate = '<div class="product product-single"><div class="w-loading"><i></i></div></div>';
								}
								this.container.html('<div class="mfp-content"></div><div class="mfp-preloader">' + skeletonTemplate + '</div>');
								this.contentContainer = this.container.children('.mfp-content');
								this.preloader = false;
							},
							beforeClose: function () {
								this.container.empty();
							},
							ajaxContentAdded: function () {
								var self = this;
								this.wrap.imagesLoaded(function () {
									finishQuickView();
								});

								// Move close button out of product because of product's overflow.
								this.wrap.find('.mfp-close').appendTo(this.content);

								// Remove preloader
								setTimeout(function () {
									self.contentContainer.next('.mfp-preloader').remove();
								}, 300);
							}
						}
					});
				}

				// 1. Quickview / Preload skeleton screen for "loading", "offcanvas".

				if (wolmart_vars.skeleton_screen && quickviewType != 'zoom') {
					openQuickview(quickviewType);

				} else if (quickviewType == 'zoom') { // 2. Quickview / Zoomed Product

					var zoomLoadedData = '';
					function zoomInit() {
						var instance = $.magnificPopup.instance;
						if (instance.isOpen && instance.content && instance.wrap.hasClass('zoom-start2') && !instance.wrap.hasClass('zoom-finish') && zoomLoadedData) {

							var i = 1;
							var timer = Wolmart.requestInterval(function () {
								instance.wrap.addClass('zoom-start3');
								if (instance.content) {

									var $data = $(zoomLoadedData);
									var $gallery = $data.find('.woocommerce-product-gallery');
									var $summary = $data.find('.summary');
									var $product = instance.content.find('.product-single');
									$product.children('.col-md-6:first-child').html($gallery);
									$product.find('.col-md-6 > .summary').remove();
									$product.attr('id', $data.attr('id'));
									$product.attr('class', $data.attr('class'));

									instance.content.css('clip-path', i < 30 ? 'inset(0 calc(' + ((31 - i) * 50 / 30) + '% - 20px) 0 0)' : 'none');
									if (i >= 30) {
										Wolmart.deleteTimeout(timer);
										instance.wrap.addClass('zoom-finish');
										$product.children('.col-md-6:last-child').append($summary);

										$('.mfp-animated-image').remove();

										Wolmart.requestTimeout(function () {
											instance.wrap.addClass('zoom-loaded mfp-anim-finish');
											Wolmart.endLoading($product.children('.col-md-6:last-child'));
											finishQuickView();
										}, 50);
									}
									++i;
								} else {
									Wolmart.deleteTimeout(timer);
								}
							}, 16);
						}
					}

					var $image;
					if ($this.parent('.hotspot-product').length) {
						$image = $this.parent().find('.product-media img');
					} else if ($this.closest('.shop_table').length) {
						$image = $this.closest('tr').find('.product-thumbnail img');
					} else {
						$image = $this.closest('.product').find('.product-media img:first-child');
					}
					if (!$image.length) {
						openQuickview('loading');
						return;
					}
					var imageSrc = $image.attr('src');

					$('<img src="' + imageSrc + '">').imagesLoaded(function () {
						$this.data('magnificPoup') ||
							$this.attr('data-mfp-src', imageSrc)
								.magnificPopup({
									type: 'image',
									mainClass: 'mfp-product mfp-zoom mfp-anim',
									preloader: false,
									item: {
										src: $image
									},
									closeOnBgClick: false,
									zoom: {
										enabled: true,
										duration: 550,
										easing: 'cubic-bezier(.55,0,.1,1)',
										opener: function () {
											return $image;
										}
									},
									callbacks: {
										beforeOpen: Wolmart.defaults.popup.callbacks.beforeOpen,
										open: function () {
											var wrapper = '<div class="product-single product-quickview product row product-quickview-loading"><div class="col-md-6"></div><div class="col-md-6"></div></div>';

											if (wolmart_vars.quickview_thumbs != 'horizontal' && window.innerWidth >= 992) {
												this.content.addClass('vertical');
											}

											this.content.find('figcaption').remove();
											this.items[0] && this.items[0].img.wrap(wrapper)
												.after('<div class="thumbs"><img src="' + this.items[0].img.attr("src") + '" /><img src="' + this.items[0].img.attr("src") + '" /><img src="' + this.items[0].img.attr("src") + '" /><img src="' + this.items[0].img.attr("src") + '" /></div>');

											var self = this;
											setTimeout(function () {
												self.bgOverlay.removeClass('mfp-ready');
											}, 16);

											setTimeout(function () {
												self.wrap.addClass('zoom-start');
												Wolmart.requestFrame(function () {
													var $img = self.content.find('.thumbs>img:first-child');
													var w = $img.width();
													var h = $img.height();
													var i = 0;
													self.bgOverlay.addClass('mfp-ready');
													var timer = Wolmart.requestInterval(function () {
														if (self.content) {
															self.content.css(
																'clip-path',
																wolmart_vars.quickview_thumbs != 'horizontal' && window.innerWidth >= 992 ?
																	'inset(' + (30 - i) + 'px calc(50% + ' + (10 - i) + 'px) ' + (30 - i) + 'px ' + ((30 - i) * (30 + w) / 30) + 'px)' :
																	'inset(' + (30 - i) + 'px calc(50% + ' + (10 - i) + 'px) ' + ((30 - i) * (30 + h) / 30) + 'px ' + (30 - i) + 'px)'
															);


															if (i >= 30) {
																Wolmart.deleteTimeout(timer);
																self.wrap.addClass('zoom-start2');
																if (!zoomLoadedData) {
																	Wolmart.doLoading(self.content.find('.product > .col-md-6:first-child'));
																}
																zoomInit();
															} else {
																i += 3;
															}
														} else {
															Wolmart.deleteTimeout(timer);
														}
													}, 16);
												});
											}, 560);
										},
										beforeClose: function () {
											$this.removeData('magnificPopup').removeAttr('data-mfp-src');
											$this.off('click.magnificPopup');
											$('.mfp-animated-image').remove();
										},
										close: Wolmart.defaults.popup.callbacks.close
									}
								});
						$this.magnificPopup('open');
					});

					// Get images loaded ajax content
					$.post(wolmart_vars.ajax_url, ajax_data)
						.done(function (data) {
							$(data).imagesLoaded(function () {
								zoomLoadedData = data;
								zoomInit();
							});
						});

				} else { // 3. Quickview / Loading Icon Inner Product

					Wolmart.doLoading($this.closest('.product').find('.product-media'));

					// Get images loaded ajax content
					$.post(wolmart_vars.ajax_url, ajax_data)
						.done(function (data) {
							$(data).imagesLoaded(function () {
								Wolmart.popup({
									type: 'inline',
									mainClass: 'mfp-product mfp-fade ' + (quickviewType == 'offcanvas' ? 'mfp-offcanvas' : 'mfp-anim'),
									items: {
										src: data
									},
									callbacks: {
										open: function () {
											var self = this;
											function finishLoad() {
												self.wrap.addClass('mfp-anim-finish');
											}

											if (quickviewType == 'offcanvas') {
												setTimeout(finishLoad, 316);
											} else {
												Wolmart.requestFrame(finishLoad);
											}

											finishQuickView();
										}
									}
								})

								Wolmart.endLoading($this.closest('.product').find('.product-media'));
							})
						});
				}
			});
		}

		/**
		 * Initialize products cart action
		 * 
		 * @since 1.0
		 */
		function initProductsCartAction() {
			Wolmart.$body
				// Before product is added to cart
				.on('click', '.add_to_cart_button:not(.product_type_variable)', function (e) {
					$('.minicart-icon').addClass('adding');
					Wolmart.doLoading(e.currentTarget, 'small');
				})

				// Off Canvas cart type
				.on('click', '.cart-offcanvas .cart-toggle', function (e) {
					$(this).parent().toggleClass('opened');
					e.preventDefault();
				})
				.on('click', '.cart-offcanvas .btn-close', function (e) {
					$(this).closest('.cart-offcanvas').removeClass('opened');
				})
				.on('click', '.cart-offcanvas .cart-overlay', function (e) {
					$(this).parent().removeClass('opened');
				})

				// After product is added to cart
				.on('added_to_cart', function (e, fragments, cart_hash, $thisbutton) {

					var $product = $thisbutton.closest('.product');

					// remove newly added "view cart" button.
					$thisbutton.next('.added_to_cart').remove();

					// if not product single, then open cart popup
					if (!$product.hasClass('product-single')) {
						var link = $product.find('.product-media .woocommerce-loop-product__link').attr('href'),
							image = $product.find('.product-media img:first-child').attr('src'),
							title = $product.find('.woocommerce-loop-product__title a').text(),
							price = $product.find('.price').html(),
							id = $product.attr('data-product-id');

						if ($thisbutton.closest('.compare-basic-info').length) {
							var $compare_col = $thisbutton.closest('.compare-col'),
								index = $compare_col.index(),
								$compare_table = $compare_col.closest('.wolmart-compare-table');

							link = $compare_col.find('.product-title').attr('href');
							image = $compare_col.find('.product-media img').attr('src');
							title = $compare_col.find('.product-title').text();
							price = $compare_table.find('.compare-price .compare-col:nth-child(' + index + ')').html();
							id = $compare_col.find('.remove_from_compare').attr('data-product_id');
						}

						if ('cart-popup' == wolmart_vars.cart_popup_type) {
							// open cart popup
							Wolmart.cartpopup.open('<div class="product product-list-sm" data-product-id=' + id + ' id="product-' + id + '">\
								<figure class="product-media"><a href="' + link + '"><img src="' + image + '"></img></a></figure>\
								<div class="product-details"><a class="product-title" href="' + link + '">' + title + '</a><span class="product-price">' + price + '</span></div></div>\
								<div class="cart-popup-footer">' + '<a href="' + wolmart_vars.pages.cart + '" class="btn btn-sm btn-rounded">' + wolmart_vars.texts.view_cart + '</a><a href="' + wolmart_vars.pages.checkout + '" class="btn btn-sm btn-dark btn-rounded">' + wolmart_vars.texts.view_checkout + '</a></div>', id);
						} else {
							// open mini popup
							var $popup_product = $('.minipopup-area').find("#product-" + id);

							if (id == $popup_product.attr('data-product-id')) {
								$popup_product.find('.cart-count').html(parseInt($popup_product.find('.cart-count').html()) + 1);
							} else {
								Wolmart.minipopup.open({
									content: '<div class="minipopup-box">\
										<div class="product product-list-sm" data-product-id=' + id + ' id="product-' + id + '">\
											<figure class="product-media"><a href="' + link + '"><img src="' + image + '"></img></a></figure>\
											<div class="product-details"><a class="product-title" href="' + link + '"><span class="cart-count">1</span> x ' + title + '</a>' + wolmart_vars.texts.cart_suffix + '</div></div>\
											<div class="minipopup-footer">' + '<a href="' + wolmart_vars.pages.cart + '" class="btn btn-sm btn-rounded">' + wolmart_vars.texts.view_cart + '</a><a href="' + wolmart_vars.pages.checkout + '" class="btn btn-sm btn-dark btn-rounded">' + wolmart_vars.texts.view_checkout + '</a></div></div>'
								});
							}
						}
					}

					$('.minicart-icon').removeClass('adding');
				})
				.on('added_to_cart ajax_request_not_sent.adding_to_cart', function (e, f, c, $thisbutton) {
					if (typeof $thisbutton !== 'undefined') {
						Wolmart.endLoading($thisbutton);
					}
				})
				.on('wc_fragments_refreshed', function (e, f) {
					Wolmart.quantityInput('.shop_table .qty');

					setTimeout(function () {
						$('.sticky-sidebar').trigger('recalc.pin');
					}, 400);
				})

				// Refresh cart table when cart item is removed
				.off('click', '.widget_shopping_cart .remove')
				.on('click', '.widget_shopping_cart .remove', function (e) {
					e.preventDefault();
					var $this = $(this);
					var cart_id = $this.data("cart_item_key");

					$.ajax(
						{
							type: 'POST',
							dataType: 'json',
							url: wolmart_vars.ajax_url,
							data: {
								action: "wolmart_cart_item_remove",
								nonce: wolmart_vars.nonce,
								cart_id: cart_id
							},
							success: function (response) {
								var this_page = location.toString(),
									item_count = $(response.fragments['div.widget_shopping_cart_content']).find('.mini_cart_item').length;

								this_page = this_page.replace('add-to-cart', 'added-to-cart');
								$(document.body).trigger('wc_fragment_refresh');

								// Block widgets and fragments
								if (item_count == 0 && ($('body').hasClass('woocommerce-cart') || $('body').hasClass('woocommerce-checkout'))) {
									$('.page-content').block();
								} else {
									$('.shop_table.cart, .shop_table.review-order, .updating, .cart_totals').block();
								}

								// Unblock
								$('.widget_shopping_cart, .updating').stop(true).unblock();

								// Cart page elements
								if (item_count == 0 && ($('body').hasClass('woocommerce-cart') || $('body').hasClass('woocommerce-checkout'))) {
									$('.page-content').load(this_page + ' .page-content:eq(0) > *', function () {
										$('.page-content').unblock();
									});
								} else {
									$('.shop_table.cart').load(this_page + ' .shop_table.cart:eq(0) > *', function () {
										$('.shop_table.cart').unblock();
										Wolmart.quantityInput('.shop_table .qty');
									});

									$('.cart_totals').load(this_page + ' .cart_totals:eq(0) > *', function () {
										$('.cart_totals').unblock();
									});

									// Checkout page elements
									$('.shop_table.review-order').load(this_page + ' .shop_table.review-order:eq(0) > *', function () {
										$('.shop_table.review-order').unblock();
									});
								}
							}
						}
					);
					return false;
				})
				// Removing cart item from minicart
				.on('click', '.remove_from_cart_button', function (e) {
					var product_id = $(this).data('product_id');

					Wolmart.$body.trigger('update_sticky_cart', [product_id]);

					Wolmart.doLoading($(this).closest('.mini_cart_item'), 'small');
				});
		}

		/**
		 * Initialize products wishlist action
		 * 
		 * @since 1.0
		 */
		function initProductsWishlistAction() {
			function updateMiniWishList() {
				var $minilist = $('.mini-basket-dropdown .widget_wishlist_content');

				if (!$minilist.length) {
					return;
				}

				if (!$minilist.find('.w-loading').length) {
					Wolmart.doLoading($minilist, 'small');
				}

				$.ajax({
					url: wolmart_vars.ajax_url,
					data: {
						action: 'wolmart_update_mini_wishlist'
					},
					type: 'post',
					success: function (data) {
						if ($minilist.closest('.mini-basket-dropdown').find('.wish-count').length) {
							$minilist.closest('.mini-basket-dropdown').find('.wish-count').text($(data).find('.wish-count').text());
						}
						$minilist.html($(data).find('.widget_wishlist_content').html());
					}
				});
			};

			Wolmart.$body
				// Add item to wishlist
				.on('click', '.add_to_wishlist', function (e) {
					Wolmart.doLoading($(e.currentTarget).closest('.yith-wcwl-add-to-wishlist'), 'small');
				})
				// Remove from wishlist if item is already in wishlist
				// .on( 'click', '.products .yith-wcwl-wishlistexistsbrowse a, .products .yith-wcwl-wishlistaddedbrowse a', function ( e ) {
				// 	var $link = $( e.currentTarget ),
				// 		$wcwlWrap = $link.closest( '.yith-wcwl-add-to-wishlist' ),
				// 		product_id = $wcwlWrap.data( 'fragment-ref' ),
				// 		fragmentOptions = $wcwlWrap.data( 'fragment-options' ),
				// 		data = {
				// 			action: yith_wcwl_l10n.actions.remove_from_wishlist_action,
				// 			remove_from_wishlist: product_id,
				// 			fragments: fragmentOptions,
				// 			from: 'theme'
				// 		};

				// 	Wolmart.doLoading( $wcwlWrap, 'small' );
				// 	$.ajax( {
				// 		url: yith_wcwl_l10n.ajax_url,
				// 		data: data,
				// 		method: 'post',
				// 		complete: function () {
				// 			Wolmart.endLoading( $wcwlWrap );
				// 		},
				// 		success: function ( data ) {
				// 			if ( fragmentOptions.in_default_wishlist ) {
				// 				delete fragmentOptions.in_default_wishlist;
				// 				$wcwlWrap.attr( JSON.stringify( fragmentOptions ) );
				// 			}
				// 			$wcwlWrap.removeClass( 'exists' );
				// 			$wcwlWrap.find( '.yith-wcwl-wishlistexistsbrowse' ).addClass( 'yith-wcwl-add-button' ).removeClass( 'yith-wcwl-wishlistexistsbrowse' );
				// 			$wcwlWrap.find( '.yith-wcwl-wishlistaddedbrowse' ).addClass( 'yith-wcwl-add-button' ).removeClass( 'yith-wcwl-wishlistaddedbrowse' );
				// 			$link.attr( 'href', location.href + '?post_type=product&amp;add_to_wishlist=' + product_id );
				// 			$link.attr( 'data-product-id', product_id );
				// 			$link.attr( 'data-product-type', fragmentOptions.product_type );
				// 			$link.attr( 'title', wolmart_vars.texts.add_to_wishlist );
				// 			$link.attr( 'data-title', wolmart_vars.texts.add_to_wishlist );
				// 			$link.addClass( 'add_to_wishlist single_add_to_wishlist' );
				// 			// $link.html('<span>' + wolmart_vars.texts.add_to_wishlist + '</span>');
				// 			Wolmart.$body.trigger( 'removed_from_wishlist' );
				// 		}
				// 	} );
				// 	e.preventDefault();
				// } )
				.on('added_to_wishlist', function () {
					$('.wish-count').each(
						function () {
							$(this).html(parseInt($(this).html()) + 1);
						}
					);
					updateMiniWishList();
				})
				.on('removed_from_wishlist', function () {
					$('.wish-count').each(
						function () {
							$(this).html(parseInt($(this).html()) - 1);
						}
					);
					updateMiniWishList();
				})
				.on('added_to_cart', function (e, fragments, cart_hash, $button) {
					if ($button.closest('#yith-wcwl-form').length) {
						$('.wish-count').each(
							function () {
								$(this).html(parseInt($(this).html()) - 1);
							}
						)
					};
					updateMiniWishList();
				})
				.on('click', '.wishlist-dropdown .wishlist-item .remove_from_wishlist', function (e) {
					e.preventDefault();

					var id = $(this).attr('data-product_id'),
						$product = $('.yith-wcwl-add-to-wishlist.add-to-wishlist-' + id),
						$table = $('.wishlist_table #yith-wcwl-row-' + id + ' .remove_from_wishlist');

					Wolmart.doLoading($(this).closest('.wishlist-item'), 'small');

					if ($product.length) {
						$product.find('a').trigger('click');
					} else if ($table.length) {
						$table.trigger('click');
					} else {
						$.ajax({
							url: yith_wcwl_l10n.ajax_url,
							data: {
								action: yith_wcwl_l10n.actions.remove_from_wishlist_action,
								remove_from_wishlist: id,
								from: 'theme'
							},
							method: 'post',
							success: function (data) {
								Wolmart.$body.trigger('removed_from_wishlist');
							}
						});
					}
				})
				.on('click', '.wishlist-offcanvas > .wishlist', function (e) {
					$(this).closest('.wishlist-dropdown').toggleClass('opened');
					e.preventDefault();
				})
				.on('click', '.wishlist-offcanvas .btn-close', function (e) {
					e.preventDefault();
					$(this).closest('.wishlist-dropdown').removeClass('opened');
				})
				.on('click', '.wishlist-offcanvas .wishlist-overlay', function (e) {
					$(this).closest('.wishlist-dropdown').removeClass('opened');
				});
		}

		/**
		 * Initialize products hover in double touch
		 * 
		 * @since 1.0
		 */
		function initProductsHover() {
			if (!$('html').hasClass('touchable') || !wolmart_vars.prod_open_click_mob) {
				return;
			}

			var isTouchFired = false;

			function _clickProduct(e) {
				if (isTouchFired && !$(this).hasClass('hover-active')) {
					e.preventDefault();
					$('.hover-active').removeClass('hover-active');
					$(this).addClass('hover-active');
				}
			}

			function _clickGlobal(e) {
				isTouchFired = e.type == 'touchstart';
				$(e.target).closest('.hover-active').length || $('.hover-active').removeClass('hover-active');
			}

			Wolmart.$body.on('click', '.product-wrap .product', _clickProduct);
			$(document).on('click', _clickGlobal);
			document.addEventListener('touchstart', _clickGlobal, { passive: true });
		}

		/**
		 * Initalize subpages
		 * 
		 * @since 1.0
		 */
		function initSubpages() {
			// Refresh sticky sidebar on shipping calculator in cart page
			Wolmart.$body.on('click', '.shipping-calculator-button', function (e) {
				var btn = e.currentTarget;
				setTimeout(function () {
					$(btn).closest('.sticky-sidebar').trigger('recalc.pin');
				}, 400);
			})

			if (wolmart_vars.cart_auto_update) {
				Wolmart.$body.on('click', '.shop_table .quantity-minus, .shop_table .quantity-plus', function () {
					$('.shop_table button[name="update_cart"]').trigger('click');
				});
				Wolmart.$body.on('keyup', '.shop_table .quantity .qty', function () {
					$('.shop_table button[name="update_cart"]').trigger('click');
				});
			}
		}

		/**
		 * Set quantity
		 *
		 * @since 1.0
		 * @return {void}
		 */
		function handleQTY() {
			var $obj = $(this);
			if ($obj.closest('.quantity').next('.add_to_cart_button[data-quantity]').length) {
				var count = $obj.val();
				if (count) {
					$obj.closest('.quantity').next('.add_to_cart_button[data-quantity]').attr('data-quantity', count);
				}
			}
		}

		/**
		 * Init Multi Image Hover Type
		 * 
		 * @since 1.4.0
		 * @return {void}
		 */
		function initMultiImageHover() {
			Wolmart.$body.on('mouseover', '.wolmart-hover-multi-image-item', function (e) {
				var $item = $(this),
					$image = $item.closest('.product-loop').find('.product-media > a > img'),
					$dots = $item.closest('.product-loop').find('.wolmart-multi-image-dot'),
					src = $item.data('image-url');

				$dots.eq($item.data('number') - 1)
					.addClass('active')
					.siblings().removeClass('active');

				if (src) {
					var image = document.createElement('img'); // use DOM HTMLImageElement
					image.src = src;

					image.onload = function () {
						$image.attr('src', src);
					}
				}
			});

			Wolmart.$body.on('mouseleave', '.wolmart-hover-multi-image-wrapper', function (e) {
				var $wrapper = $(this),
					$image = $wrapper.closest('.product-loop').find('.product-media > a > img'),
					$dots = $wrapper.closest('.product-loop').find('.wolmart-multi-image-dot'),
					src = $wrapper.find('.wolmart-hover-multi-image-item').eq(0).data('image-url');

				$dots.eq(0)
					.addClass('active')
					.siblings().removeClass('active');

				$image.attr('src', src);
			});
		}

		/**
		 * Init Sticky Cart QTY
		 * 
		 * @since 1.4.0
		 * @return {void}
		 */
		function initStickyCartQTY() {
			var updateStickyCart = function (selector, count) {
				var $product = $(selector);

				$product.each(function () {
					var $this = $(this);

					if (count == 0) {
						$this.find('.product-sticky-cart-control')
							.removeClass('show')
							.siblings('.btn-product')
							.removeClass('hide');


						$this.find('.product-sticky-cart-qty').html(count + 1);
					} else {
						$this.find('.product-sticky-cart-control')
							.addClass('show')
							.siblings('.btn-product')
							.addClass('hide');

						$this.find('.product-sticky-cart-control:not(.main-product)').addClass('qty-only');
						$this.find('.product-sticky-cart-control').removeClass('main-product');

						$this.find('.product-sticky-cart-qty').html(count);
					}
				});
			}

			Wolmart.$body.on('added_to_cart', function (e, fragments, cart_hash, $thisbutton) {
				var $product = $thisbutton.closest('.product-loop'),
					productID = $product.find('.add_to_cart_button').data('product_id');

				if ($product.hasClass('product-sticky-cart')) {
					$product.find('.product-sticky-cart-control')
						.addClass('show')
						.siblings('.btn-product')
						.addClass('hide');

					$.ajax({
						type: 'GET',
						dataType: 'json',
						url: wolmart_vars.ajax_url,
						data: {
							action: 'wolmart_cart_item_count',
							product_id: productID,
						},
						success: function (data) {
							$product.find('.product-sticky-cart-control').addClass('main-product');
							updateStickyCart('.product-loop.product-sticky-cart.post-' + productID, data);
						}
					});

					$product.find('.product-add-cart').removeClass('loading')
						.find('.w-loading').remove();
				}
			});

			Wolmart.$body.on('focusout', '.product-sticky-cart.product-loop', function (e) {
				var $product = $(this);

				$(e.originalEvent.relatedTarget).addClass('focused');
				if (0 == $product.find('.focused').length) {
					$product.find('.product-sticky-cart-control').addClass('qty-only');
				} else {
					$(e.originalEvent.relatedTarget).removeClass('focused');
				}
			});

			Wolmart.$body.on('click', '.product-sticky-cart-control', function (e) {
				var $product = $(this).closest('.product-loop');

				$product.find('.product-sticky-cart-control').removeClass('qty-only');
			});

			Wolmart.$body.on('click', '.product-add-cart', function (e) {
				var $this = $(this),
					$product = $(this).closest('.product-loop');

				$this.append('<div class="w-loading"><i></i></div>')
					.addClass('loading');

				$product.find('.product_type_simple.add_to_cart_button').trigger('click');
				$product.trigger('focus');
			});

			Wolmart.$body.on('click', '.product-remove-cart', function (e) {
				var $this = $(this),
					$product = $this.closest('.product-loop'),
					productID = $product.find('.add_to_cart_button').data('product_id');

				$this.append('<div class="w-loading"><i></i></div>')
					.addClass('loading');

				$.ajax({
					type: 'GET',
					dataType: 'json',
					url: wolmart_vars.ajax_url,
					data: {
						action: 'wolmart_remove_cart_item',
						product_id: productID,
					},
					success: function (data) {
						$this.removeClass('loading')
							.find('.w-loading').remove();

						$product.find('.product-sticky-cart-control').addClass('main-product');
						updateStickyCart('.product-loop.product-sticky-cart.post-' + productID, data);

						Wolmart.$body.trigger('wc_fragment_refresh');
					}
				});
			});

			Wolmart.$body.on('update_sticky_cart', function (e, product_id) {
				updateStickyCart('.product-loop.product-sticky-cart.post-' + product_id, 0);
			});
		}

		return {
			init: function () {
				this.removerId = 0;

				// Functions for products
				initProductsAttributeAction();
				initProductsQuickview();
				initProductsCartAction();
				initProductsWishlistAction();
				initProductsHover();

				// Functions for shop page
				initSelectMenu();
				initSubpages();
				initMultiImageHover();
				initStickyCartQTY();

				// Functions for Alert
				this.initAlertAction();
				Wolmart.call(this.initProducts.bind(this), 500);
			},

			/**
			 * Initialize products
			 * - rating tooltip
			 * - product types
			 * - product sales countdown
			 * 
			 * @since 1.0
			 * @since 1.1.10 - Fixed - Product sales countdown doesn`t work properly when it is loaded by Ajax request.
			 * 
			 * @param {HTMLElement|jQuery|string} selector
			 * @return {void}
			 */
			initProducts: function (selector) {
				this.ratingTooltip(selector);
				this.initProductType(selector);

				Wolmart.countdown($(selector).find('.product-countdown'));


				Wolmart.initSlider($(selector).find('.product-hover-slider'));
				// Wolmart.quantityInput(Wolmart.$(selector, '.qty'));
				// Wolmart.$(selector, 'input.qty').off('change', handleQTY).on('change', handleQTY);
			},

			/**
			 * Initialize rating tooltips
			 * Find all .star-rating from selector, and initialize tooltip.
			 * 
			 * @since 1.0
			 * @param {HTMLElement|jQuery|string} selector
			 * @return {void}
			 */
			ratingTooltip: function (selector) {
				var ratingHandler = function () {
					var res = this.firstElementChild.getBoundingClientRect().width / this.getBoundingClientRect().width * 5;
					this.lastElementChild.innerText = res ? res.toFixed(2) : res;
				}

				Wolmart.$(selector, '.star-rating').each(function () {
					if (this.lastElementChild && !this.lastElementChild.classList.contains('tooltiptext')) {
						var span = document.createElement('span');
						span.classList.add('tooltiptext');
						span.classList.add('tooltip-top');

						this.appendChild(span);
						this.addEventListener('mouseover', ratingHandler);
						this.addEventListener('touchstart', ratingHandler, { passive: true });
					}
				});
			},

			/**
			 * Initialize product types
			 * - popup type
			 *
			 * @since 1.0
			 * @param {HTMLElement|jQuery|string} selector
			 * @return {void}
			 */
			initProductType: function (selector) {
				Wolmart.$(selector, '.product-popup .product-details').each(function (e) {
					var $this = $(this),
						hidden_height = $this.find('.product-hide-details').outerHeight(true);

					$this.height($this.height() - hidden_height);
				});

				Wolmart.$(selector, '.product-popup')
					.on('mouseenter touchstart', function (e) {
						var $this = $(this);
						var hidden_height = $this.find('.product-hide-details').outerHeight(true);
						$this.find('.product-details').css('transform', 'translateY(' + ($this.hasClass('product-boxed') ? 11 - hidden_height : -hidden_height) + 'px)');
						$this.find('.product-hide-details').css('transform', 'translateY(' + (-hidden_height) + 'px)');
					})
					.on('mouseleave touchleave', function (e) {
						var $this = $(this);
						$this.find('.product-details').css('transform', 'translateY(0)');
						$this.find('.product-hide-details').css('transform', 'translateY(0)');
					});
			},

			/**
			 * Remove alerts automatically
			 *
			 * @since 1.0
			 * @return {void}
			 */
			initAlertAction: function () {
				this.removerId && clearTimeout(this.removerId);
				this.removerId = setTimeout(function () {
					$('.woocommerce-page .main-content .alert:not(.woocommerce-info) .btn-close').not(':hidden').trigger('click');
				}, 10000);
			}
		}
	})();

	/**
	 * Initialize account
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.initAccount = function () {
		/**
		 * Launch login form popup for both login and register buttons
		 * 
		 * @since 1.0
		 */
		function launchPopup(e) {
			if (this.classList.contains('logout')) {
				return;
			}

			e.preventDefault();

			var isRegister = this.classList.contains('register');
			Wolmart.popup({
				callbacks: {
					afterChange: function () {
						this.container.html('<div class="mfp-content"></div><div class="mfp-preloader"><div class="login-popup"><div class="w-loading"><i></i></div></div></div>');
						this.contentContainer = this.container.children('.mfp-content');
						this.preloader = false;
					},
					beforeClose: function () {
						this.container.empty();
					},
					ajaxContentAdded: function () {
						var self = this;
						if (isRegister) {
							this.wrap.find('[href="signup"]').trigger('click');
						}
						setTimeout(function () {
							self.contentContainer.next('.mfp-preloader').remove();
						}, 200);

						// WP Captcha Plugin Compatibility
						if ('function' == typeof c4wp_loadrecaptcha) {
							c4wp_loadrecaptcha();
						} else if ('undefined' !== typeof turnstile) {
							turnstile.render('.login-popup .login .cf-turnstile');
							turnstile.render('.login-popup .register .cf-turnstile');
						} else if ('undefined' !== typeof friendlyChallenge) {
							window.friendlyChallenge.autoWidget.reset();
						}
					}
				}
			}, 'login');
		}

		/**
		 * Check if user input validation
		 *
		 * @since 1.0
		 */
		function checkValidation(e) {
			var $form = $(this), isLogin = $form[0].classList.contains('login');
			$form.find('p.submit-status').show().text('Please wait...').addClass('loading');
			$form.find('button[type=submit]').attr('disabled', 'disabled');
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: wolmart_vars.ajax_url,
				data: $form.serialize() + '&action=wolmart_account_' + (isLogin ? 'signin' : 'signup') + '_validate',
				success: function (data) {
					$form.find('p.submit-status').html(data.message.replace('/<script.*?\/script>/s', '')).removeClass('loading');
					$form.find('button[type=submit]').removeAttr('disabled');
					if (data.loggedin === true) {
						location.reload();
					}
				}
			});
			e.preventDefault();
		}

		Wolmart.$body
			.on('click', '.header .account:not(.no-ajax) > a:not(.logout)', launchPopup)
			.on('submit', '#customer_login form', checkValidation)
	}

	/**
	 * Create slider object by using swiper js
	 * 
	 * @class Slider
	 * @since 1.0
	 */
	Wolmart.slider = (function () {

		function Slider($el, options) {

			return this.init($el, options);
		}

		function onInitialized() {
			var $wrapper = $(this.slider.wrapperEl);
			var slider = this.slider;

			$wrapper.trigger('initialized.slider', slider);
			$wrapper.find('.slider-slide:not(.slider-slide-active) .appear-animate').removeClass('appear-animate'); // Prevent appear animation of inactive slides

			// Video
			$wrapper.find('video')
				.removeAttr('style')
				.on('ended', function () {
					var $this = $(this);
					if ($this.closest('.slider-slide').hasClass('slider-slide-active')) {

						if (true === slider.params.autoplay.enabled) {
							if (slider.params.loop && slider.slides.length === slider.activeIndex) {
								this.loop = true;
								try {
									this.play();
								} catch (e) { }
							}
							slider.slideNext();
							slider.autoplay.start();
						} else {
							this.loop = true;
							try {
								this.play();
							} catch (e) { }
						}
					}
				});

			sliderLazyload.call(this);
		}

		function onTranslated() {
			$(window).trigger('appear.check');

			var $wrapper = $(this.slider.wrapperEl);
			var slider = this.slider;

			// Video Play
			var $activeVideos = $wrapper.find('.slider-slide-active video');
			$wrapper.find('.slider-slide:not(.slider-slide-active) video').each(function () {
				if (!this.paused) {
					slider.autoplay.start();
				}
				this.pause();
				this.currentTime = 0;
			});

			if ($activeVideos.length) {
				var slider = $wrapper.data('slider');
				if (slider && slider.params && slider.params.autoplay.enabled) {
					slider.autoplay.stop();
				}
				$activeVideos.each(function () {
					try {
						if (this.paused) {
							this.play();
						}
					} catch (e) { }
				});
			}

			sliderLazyload.call(this);
		}

		function onSliderInitialized() {
			var self = this,
				$el = $(this.slider.wrapperEl);

			// carousel content animation
			$el.find('.slider-slide-active .slide-animate').each(function () {
				var $animation_item = $(this),
					settings = $animation_item.data('settings'),
					duration,
					delay = settings._animation_delay ? settings._animation_delay : 0,
					aniName = settings._animation_name;

				if ($animation_item.hasClass('animated-slow')) {
					duration = 2000;
				} else if ($animation_item.hasClass('animated-fast')) {
					duration = 750;
				} else {
					duration = 1000;
				}

				$animation_item.css('animation-duration', duration + 'ms');

				duration = duration ? duration : 750;

				var temp = Wolmart.requestTimeout(function () {
					$animation_item.addClass(aniName);
					$animation_item.addClass('show-content');
					self.timers.splice(self.timers.indexOf(temp), 1)
				}, (delay ? delay : 0));
			});
		}

		function sliderLazyload() {
			if ($.fn.lazyload) {
				$(this.slider.wrapperEl).find('[data-lazy]')
					.filter(function () {
						return !$(this).data('_lazyload_init');
					})
					.data('_lazyload_init', 1)
					.each(function () {
						$(this).lazyload(Wolmart.defaults.lazyload);
					});
			}
		}

		function onSliderResized() {
			$(this.slider.wrapperEl).find('.slider-slide-active .slide-animate').each(function () {
				$(this)
					.addClass('show-content')
					.css({
						'animation-name': '',
						'animation-duration': '',
						'animation-delay': '',
					});
			});
		}

		function onSliderTranslate() {
			var self = this,
				$el = $(this.slider.wrapperEl);
			self.translateFlag = 1;
			self.prev = self.next;
			$el.find('.slider-slide .slide-animate').each(function () {
				var $animation_item = $(this),
					settings = $animation_item.data('settings');
				if (settings) {
					$animation_item.removeClass(settings._animation_name + ' animated appear-animation-visible elementor-invisible appear-animate');
				}
			});
		}

		function onSliderTranslated() {
			var self = this,
				$el = $(this.slider.wrapperEl);
			if (1 != self.translateFlag) {
				return;
			}

			$el.find('.show-content').removeClass('show-content');

			self.next = this.slider.activeIndex;
			if (self.prev != self.next) {
				$el.find('.show-content').removeClass('show-content');

				/* clear all animations that are running. */
				if ($el.hasClass("animation-slider")) {
					for (var i = 0; i < self.timers.length; i++) {
						Wolmart.deleteTimeout(self.timers[i]);
					}
					self.timers = [];
				}

				$(this.slider.slides[this.slider.activeIndex]).find('.slide-animate').each(function () {
					var $animation_item = $(this),
						settings = $animation_item.data('settings'),
						duration,
						delay = settings._animation_delay ? settings._animation_delay : 0,
						aniName = settings._animation_name;

					if ($animation_item.hasClass('animated-slow')) {
						duration = 2000;
					} else if ($animation_item.hasClass('animated-fast')) {
						duration = 750;
					} else {
						duration = 1000;
					}

					$animation_item.css({
						'animation-duration': duration + 'ms',
						'animation-delay': delay + 'ms',
						'transition-property': 'visibility, opacity',
						'transition-duration': duration + 'ms',
						'transition-delay': delay + 'ms',
					}).addClass(aniName);

					if ($animation_item.hasClass('maskLeft')) {
						$animation_item.css('width', 'fit-content');
						var width = $animation_item.width();
						$animation_item
							.css('width', 0)
							.css('transition', 'width ' + (duration ? duration : 750) + 'ms linear ' + (delay ? delay : '0s'))
							.css('width', width);
					}


					duration = duration ? duration : 750;
					$animation_item.addClass('show-content');

					var temp = Wolmart.requestTimeout(function () {
						$animation_item.css('transition-property', '');
						$animation_item.css('transition-delay', '');
						$animation_item.css('transition-duration', '');

						self.timers.splice(self.timers.indexOf(temp), 1)
					}, (delay ? (delay + 200) : 200));
					self.timers.push(temp);
				});
			} else {
				$el.find('.slider-slide').eq(this.slider.activeIndex).find('.slide-animate').addClass('show-content');
			}

			self.translateFlag = 0;
		}

		// Public Properties

		Slider.presets = {
			'product-single-carousel': {
				pagination: false,
				navigation: true,
				autoHeight: true,
				zoom: false,
				thumbs: {
					slideThumbActiveClass: 'active'
				}
			},
			'product-gallery-carousel': {
				spaceBetween: 20,
				slidesPerView: $('.main-content-wrap > .sidebar-fixed').length ? 2 : 3,
				navigation: true,
				pagination: false,
				breakpoints: {
					767: {
						slidesPerView: 2
					},
				},
			},
			'product-thumbs': {
				slidesPerView: 4,
				navigation: true,
				pagination: false,
				spaceBetween: 10,
				normalizeSlideIndex: false,
				freeMode: true,
				watchSlidesVisibility: true,
				watchSlidesProgress: true,
			},
			'products-flipbook': {
				onInitialized: function () {
					function stopDrag(e) {
						$(e.target).closest('.product-single-carousel, .product-gallery-carousel, .product-thumbs').length && e.stopPropagation();
					}
					this.wrapperEl.addEventListener('mousedown', stopDrag);
					if ('ontouchstart' in document) {
						this.wrapperEl.addEventListener('touchstart', stopDrag, { passive: true });
					}
				}
			},
			'product-hover-slider': {
				slidesPerView: 1,
				navigation: true,
				pagination: false,
				spaceBetween: 0,
				loop: true,
			},
		}

		Slider.prototype.init = function ($el, options) {
			this.timers = [];
			this.translateFlag = 0;

			// # Extend settings
			var settings = $.extend(true, {}, Wolmart.defaults.slider);
			$el.attr('class').split(' ').forEach(function (className) {
				Slider.presets[className] && $.extend(true, settings, Slider.presets[className]);
			});
			$.extend(true, settings, Wolmart.parseOptions($el.attr('data-slider-options')), options);

			// # Set all video's loop as false
			$el.find('video')
				.each(function () {
					this.loop = false;
				});

			var $children = $el.children();
			var childrenCount = $children.length;
			if (childrenCount) {
				if ($children.filter('.row').length) {
					$children.wrap('<div class="slider-slide"></div>');
					$children = $el.children();
				} else {
					$children.addClass('slider-slide');
				}
			}

			// # Remove grid classes
			var cls = $el.attr('class');
			var pattern = /gutter\-\w\w|cols\-\d|cols\-\w\w-\d/g;
			var match = cls.match(pattern) || '';
			if (match) {
				match.push('row');
				$el.data('slider-layout', match);
				$el.attr('class', cls.replace(pattern, '').replace(/\s+/, ' ')).removeClass('row');
			}

			// Display helper class for responsive navigation and pagination.
			var displayClass = [];
			if (settings.breakpoints) {
				var hideClasses = ['d-none', 'd-sm-none', 'd-md-none', 'd-lg-none', 'd-xl-none'];
				var showClasses = ['d-block', 'd-sm-block', 'd-md-block', 'd-lg-block', 'd-xl-block'];
				var bi = 0;
				for (var i in settings.breakpoints) {
					if (childrenCount <= settings.breakpoints[i].slidesPerView) {
						displayClass.push(hideClasses[bi]);
					} else if (displayClass.length) {
						displayClass.push(showClasses[bi]);
					}
					++bi;
				}
			}
			displayClass = ' ' + displayClass.join(' ');

			// Add navigation and pagination.
			var nav_dot = '';
			if (!settings.dotsContainer && settings.pagination) {
				nav_dot += '<div class="slider-pagination' + displayClass + '"></div>';
			}
			if (settings.navigation) {
				nav_dot += '<button class="slider-button slider-button-prev' + displayClass + '" aria-label="Prev"></button><button class="slider-button slider-button-next' + displayClass + '" aria-label="Next"></button>';
			}

			// Prepare slider
			$el.siblings('.slider-button,.slider-pagination').remove();
			$el.parent().addClass('slider-container' + (settings.statusClass ? ' ' + settings.statusClass : '') + ($el.attr('data-slider-status') ? ' ' + $el.attr('data-slider-status') : ''))
				.parent().addClass('slider-relative');
			$el.after(nav_dot);

			if (!settings.dotsContainer && settings.pagination) {
				settings.pagination = {
					clickable: true,
					el: $el.siblings('.slider-pagination')[0],
					bulletClass: 'slider-pagination-bullet',
					bulletActiveClass: 'active',
					modifierClass: 'slider-pagination-',
				}
			}
			if (settings.navigation) {
				settings.navigation = {
					prevEl: $el.siblings('.slider-button-prev')[0],
					nextEl: $el.siblings('.slider-button-next')[0],
					hideOnClick: true,
					disabledClass: 'disabled',
					hiddenClass: 'slider-button-hidden',
				}
			}

			// Prepare options for product thumbs carousel
			if ($el.hasClass('product-thumbs')) {
				var isVertical = $el.parent().parent().hasClass('pg-vertical');
				if (isVertical) {
					settings.direction = 'vertical';
					settings.breakpoints = {
						0: {
							slidesPerView: 4,
							direction: 'horizontal'
						},
						992: {
							slidesPerView: 'auto',
							direction: 'vertical'
						}
					}
				}
				if ($el.closest('.container-fluid').length) {
					if (!settings.breakpoints) {
						settings.breakpoints = {};
					}
					settings.breakpoints[1600] = isVertical ? {
						slidesPerView: 'auto',
						direction: 'vertical',
						spaceBetween: 20,
					} : { spaceBetween: 20 };

					if (isVertical) {
						settings.breakpoints[1600].slidesPerView = 'auto';
					}
				}
			}

			if ($el.hasClass('product-single-carousel')) {
				var $thumbs = $el.closest('.product-gallery').find('.product-thumbs');
				settings.thumbs.swiper = $thumbs.data('slider');
			}

			settings.legacy = false;

			if (settings.loop) {
				if (settings.slidesPerView > $el.children().length) {
					settings.loop = false;
				}
			}
			// Setup slider
			this.slider = new Wolmart.Swiper($el[0].parentElement, settings);

			// # Register events for slider
			onInitialized.call(this);
			this.slider.on('resize', sliderLazyload.bind(this));
			this.slider.on('transitionEnd', onTranslated.bind(this));
			settings.onInitialized && settings.onInitialized.call(this.slider);

			// # Register animation slider
			if ($el.hasClass('animation-slider')) {
				onSliderInitialized.call(this);
				this.slider.on('resize', onSliderResized.bind(this));
				this.slider.on('transitionStart', onSliderTranslate.bind(this))
				this.slider.on('transitionEnd', onSliderTranslated.bind(this));
			}

			// # Run thumb dots
			if (settings.dotsContainer && 'preview' != settings.dotsContainer) {
				var slider = this.slider;
				Wolmart.$body.on('click', settings.dotsContainer + ' button', function () {
					slider.slideTo($(this).index());
				});
				this.slider.on('transitionStart', function () {
					$(settings.dotsContainer).children().removeClass('active').eq(this.realIndex).addClass('active');
				})
			}

			// # Mount slider
			$el.trigger('initialize.slider', [this.slider]);
			$el.data('slider', this.slider);
		}

		return function (selector, options, createOnly) {
			// If disable mobile slider is enabled, return
			if (Wolmart.$body.hasClass('wolmart-disable-mobile-slider') && ('ontouchstart' in document) && (Wolmart.$window.width() < 1200)) {
				return;
			}

			Wolmart.$(selector).each(function () {

				var $this = $(this);

				// If slider is already created, return
				if ($this.data('slider')) {
					return;
				}

				// If slider has animated items
				var $anim_items = $this.find('.elementor-invisible, .appear-animate, .animated').filter(':not(.elementor-column, .repeater-animate)');
				if ($anim_items.length) {
					$this.addClass('animation-slider');
					$anim_items.addClass('slide-animate').each(function () {
						var $this = $(this);
						var pre = $this.data('settings');
						if (pre) {
							var settings = {
								'_animation_name': pre._animation ? pre._animation : pre.animation,
							};
							if (pre._animation_delay) {
								settings['_animation_delay'] = Number(pre._animation_delay);
							}
							$this.removeClass('appear-animate')
								.data('settings', settings)
								.attr('data-settings', JSON.stringify(settings));
						}
					});
				}

				var runSlider = function () {
					// if in passive tab
					if (selector == '.slider-wrapper') {
						var $pane = $this.closest('.tab-pane');
						if ($pane.length && !$pane.hasClass('active') && $pane.closest('.elementor-widget-wolmart_widget_products_tab').length) {
							return;
						}
					}

					// create slider
					new Slider($this, options);
				}
				createOnly ? new runSlider : setTimeout(runSlider);
			});
		}
	})();

	/**
	 * Initalize sliders and check their slide animations
	 * 
	 * @since 1.0
	 * @param {string} selector
	 * @return {void}
	 */
	Wolmart.initSlider = function (selector) {

		// Initialize sliders
		Wolmart.slider(selector);
	}

	/**
 * Create quantity input object
 * 
 * @class QuantityInput
 * @since 1.0
 * @param {string} selector
 * @return {void}
 */
	Wolmart.quantityInput = (function () {

		function QuantityInput($el) {
			return this.init($el);
		}

		QuantityInput.min = 1;
		QuantityInput.max = 1000000;

		QuantityInput.prototype.init = function ($el) {
			var self = this;

			self.$minus = false;
			self.$plus = false;
			self.$value = false;
			self.value = false;

			// call Events
			self.startIncrease = self.startIncrease.bind(self);
			self.startDecrease = self.startDecrease.bind(self);
			self.stop = self.stop.bind(self);

			// Variables
			self.min = parseInt($el.attr('min'));
			self.max = parseInt($el.attr('max'));

			self.min || 0 === self.min || ($el.attr('min', self.min = QuantityInput.min))
			self.max || 0 === self.max || ($el.attr('max', self.max = QuantityInput.max))

			// Add DOM elements and event listeners
			self.$value = $el.val(self.value = Math.max(parseInt($el.val()), 1));
			self.$minus = $el.parent().find('.quantity-minus').on('click', Wolmart.preventDefault);
			self.$plus = $el.parent().find('.quantity-plus').on('click', Wolmart.preventDefault);

			if ('ontouchstart' in document && self.$minus.length > 0) {
				self.$minus.on('touchstart', self.startDecrease)
				self.$plus.on('touchstart', self.startIncrease)
			} else {
				self.$minus.on('mousedown', self.startDecrease)
				self.$plus.on('mousedown', self.startIncrease)
			}

			Wolmart.$body.on('mouseup', self.stop)
				.on('touchend', self.stop);
		}

		QuantityInput.prototype.startIncrease = function (e) {
			var self = this;
			self.value = self.$value.val();
			self.value < self.max && (self.$value.val(++self.value), self.$value.trigger('change'));
			self.increaseTimer = Wolmart.requestTimeout(function () {
				self.speed = 1;
				self.increaseTimer = Wolmart.requestInterval(function () {
					self.$value.val(self.value = Math.min(self.value + Math.floor(self.speed *= 1.05), self.max));
				}, 50);
			}, 400);
		}

		QuantityInput.prototype.stop = function (e) {
			(this.increaseTimer || this.decreaseTimer) && this.$value.trigger('change');
			this.increaseTimer && (Wolmart.deleteTimeout(this.increaseTimer), this.increaseTimer = 0);
			this.decreaseTimer && (Wolmart.deleteTimeout(this.decreaseTimer), this.decreaseTimer = 0);
		}

		QuantityInput.prototype.startDecrease = function (e) {
			var self = this;
			self.value = self.$value.val();
			self.value > self.min && (self.$value.val(--self.value), self.$value.trigger('change'));
			self.decreaseTimer = Wolmart.requestTimeout(function () {
				self.speed = 1;
				self.decreaseTimer = Wolmart.requestInterval(function () {
					self.$value.val(self.value = Math.max(self.value - Math.floor(self.speed *= 1.05), self.min));
				}, 50);
			}, 400);
		}

		return function (selector) {
			Wolmart.$(selector).each(function () {
				var $this = $(this);
				// if not initialized
				$this.data('quantityInput') ||
					$this.data('quantityInput', new QuantityInput($this));
			});
		}
	})();


	/**
	 * Initialize cookie law popup
	 * 
	 * @since 1.0.0
	 * 
	 * @return {void}
	 */
	Wolmart.initCookiePopup = function () {
		var cookie_version = wolmart_vars.cookie_version;

		if ('accepted' === Wolmart.getCookie('wolmart_cookies_' + cookie_version)) {
			return;
		}

		var $el = $('.cookies-popup');

		setTimeout(function () {
			$el.addClass('show');

			Wolmart.$body.on('click', '.accept-cookie-btn', function (e) {
				e.preventDefault();
				$el.removeClass('show');
				Wolmart.setCookie('wolmart_cookies_' + cookie_version, 'accepted', 60);
			});

			Wolmart.$body.on('click', '.decline-cookie-btn', function (e) {
				e.preventDefault();
				$el.removeClass('show');
			})
		}, 2500);

	}

	/**
	 * @function floatSVG
	 * @param {string|jQuery} selector 
	 * @param {object} options
	 */
	Wolmart.floatSVG = (function () {
		function FloatSVG(svg, options) {
			this.$el = $(svg);
			this.set(options);
			this.start();
		}

		FloatSVG.prototype.set = function (options) {
			this.options = $.extend({
				delta: 15,
				speed: 10,
				size: 1,
			}, typeof options == 'string' ? JSON.parse(options) : options);
		}

		FloatSVG.prototype.getDeltaY = function (dx) {
			return Math.sin(2 * Math.PI * dx / this.width * this.options.size) * this.options.delta;
		}

		FloatSVG.prototype.start = function () {
			this.update = this.update.bind(this);
			this.timeStart = Date.now() - parseInt(Math.random() * 100);
			this.$el.find('path').each(function () {
				$(this).data('original', this.getAttribute('d').replace(/([\d])\s*\-/g, '$1,-'));
			});

			window.addEventListener('resize', this.update, { passive: true });
			window.addEventListener('scroll', this.update, { passive: true });
			Wolmart.$window.on('check_float_svg', this.update);
			this.update();
		}

		FloatSVG.prototype.update = function () {
			var self = this;

			if (this.$el.length && Wolmart.isOnScreen(this.$el[0])) {
				Wolmart.requestTimeout(function () {
					self.draw();
				}, 16);
			}
		}

		FloatSVG.prototype.draw = function () {
			var self = this,
				_dx = (Date.now() - this.timeStart) * this.options.speed / 200;
			this.width = this.$el.width();
			if (!this.width) {
				return;
			}
			this.$el.find('path').each(function () {
				var dx = _dx, dy = 0;
				this.setAttribute('d', $(this).data('original')
					.replace(/M([\d|\.]*),([\d|\.]*)/, function (match, p1, p2) {
						if (p1 && p2) {
							return 'M' + p1 + ',' + (parseFloat(p2) + (dy = self.getDeltaY(dx += parseFloat(p1)))).toFixed(3);
						}
						return match;
					})
					.replace(/([c|C])[^A-Za-z]*/g, function (match, p1) {
						if (p1) {
							var v = match.slice(1).split(',').map(parseFloat);
							if (v.length == 6) {
								if ('C' == p1) {
									v[1] += self.getDeltaY(_dx + v[0]);
									v[3] += self.getDeltaY(_dx + v[2]);
									v[5] += self.getDeltaY(dx = _dx + v[4]);
								} else {
									v[1] += self.getDeltaY(dx + v[0]) - dy;
									v[3] += self.getDeltaY(dx + v[2]) - dy;
									v[5] += self.getDeltaY(dx += v[4]) - dy;
								}
								dy = self.getDeltaY(dx);

								return p1 + v.map(function (v) {
									return v.toFixed(3);
								}).join(',');
							}
						}
						return match;
					})
				);
			});

			this.update();
		}

		return function (selector) {
			Wolmart.$(selector).each(function () {
				var $this = $(this), float;
				if (this.tagName == 'svg') {
					float = $this.data('float-svg');
					if (float) {
						float.set($this.attr('data-float-options'));
					} else {
						$this.data('float-svg', new FloatSVG(this, $this.attr('data-float-options')));
					}
				}
			})
		};
	})();



	/**
	 * Show edit page tooltip
	 * 
	 * @since 1.0
	 * @return {void}
	 */
	Wolmart.showEditPageTooltip = function () {
		if ($.fn.tooltip) {
			$('.wolmart-edit-link').each(function () {
				var $this = $(this),
					title = $this.data('title');

				$this.next('.wolmart-block').addClass('wolmart-has-edit-link').tooltip({
					html: true,
					template: '<div class="tooltip wolmart-tooltip-wrap" role="tooltip"><div class="arrow"></div><div class="tooltip-inner wolmart-tooltip"></div></div>',
					trigger: 'manual',
					title: '<a href="' + $this.data('link') + '" target="_blank">' + title + '</a>',
					delay: 300
				});
				var tooltipData = $this.next('.wolmart-block').data('bs.tooltip');
				if (tooltipData && tooltipData.element) {
					$(tooltipData.element).on('mouseenter.bs.tooltip', function (e) {
						tooltipData._enter(e);
					});
					$(tooltipData.element).on('mouseleave.bs.tooltip', function (e) {
						tooltipData._leave(e);
					});
				}
			});

			Wolmart.$body.on('mouseenter mouseleave', '.tooltip[role="tooltip"]', function (e) {
				var $element = $('.wolmart-block[aria-describedby="' + $(this).attr('id') + '"]');
				if ($element.length && $element.data('bs.tooltip')) {
					var fn_name = 'mouseenter' == e.type ? '_enter' : '_leave';
					$element.data('bs.tooltip')[fn_name](false, $element.data('bs.tooltip'));
				}
			});
		}
	}


	/**
	 * Enable Currency Switcher
	 * 
	 * @since 1.0.0
	 * @return {void}
	 */
	Wolmart.currencySwitcher = {
		/**
		 * Initialize and register events
		 * 
		 * @returns {void}
		 */
		init: function () {
			this.events();
			return this;
		},

		events: function () {
			var self = this;

			// wcml currency switcher
			$(document.body).on('click', '.wcml-switcher li', function (e) {
				e.preventDefault();

				if ($(this).parent().attr('disabled') == 'disabled')
					return;
				var currency = $(this).attr('rel');
				self.loadCurrency(currency);
			});

			// woocommerce currency switcher
			$(document.body).on('click', '.woocs-switcher li', function (e) {
				if ($(this).parent().attr('disabled') == 'disabled')
					return;
				var currency = $(this).attr('rel');
				self.loadWoocsCurrency(currency);
			});

			return self;
		},

		loadCurrency: function (currency) {
			$('.wcml-switcher').attr('disabled', 'disabled');
			$('.wcml-switcher').append('<li class="loading"></li>');
			var data = { action: 'wcml_switch_currency', currency: currency };
			$.ajax({
				type: 'post',
				url: wolmart_vars.ajax_url,
				data: {
					action: 'wcml_switch_currency',
					currency: currency
				},
				success: function (response) {
					$('.wcml-switcher').removeAttr('disabled');
					$('.wcml-switcher').find('.loading').remove();
					window.location = window.location.href;
				}
			});
		},

		loadWoocsCurrency: function (currency) {
			$('.woocs-switcher').attr('disabled', 'disabled');
			$('.woocs-switcher').append('<li class="loading"></li>');
			var l = window.location.href;
			l = l.split('?');
			l = l[0];
			var string_of_get = '?';
			woocs_array_of_get.currency = currency;

			if (Object.keys(woocs_array_of_get).length > 0) {
				jQuery.each(woocs_array_of_get, function (index, value) {
					string_of_get = string_of_get + "&" + index + "=" + value;
				});
			}
			window.location = l + string_of_get;
		},

		removeParameterFromUrl: function (url, parameter) {
			return url
				.replace(new RegExp('[?&]' + parameter + '=[^&#]*(#.*)?$'), '$1')
				.replace(new RegExp('([?&])' + parameter + '=[^&]*&'), '$1');
		}
	}

	/**
	 * Wolmart Mobile Scripts
	 * 
	 * @since 1.6.0
	 */
	if (wolmart_vars['mobile_scripts'].length > 0) {
		wolmart_vars['mobile_scripts'].forEach(function (script) {
			let scriptElement = document.createElement("script");

			scriptElement.setAttribute("src", script['src']);
			scriptElement.setAttribute("defer", script['defer']);
			scriptElement.setAttribute("id", script['handle']);

			document.body.appendChild(scriptElement);
		});
	}

	/**
	 * Wolmart Mobile FullScreen Switcher
	 * 
	 * @since 1.6.0
	 */
	Wolmart.initMobileSwitcher = function () {
		$('body').on('click', '.mobile-fs-switcher > li > a', function (e) {
			var $this = $(this),
				$wrapper = $this.closest('.mobile-fs-switcher'),
				$list = $wrapper.find('ul');

			// Wrap DIV tag.
			// $list.wrap('<div class="mobile-switcher-wrapper"></div>');
			$wrapper.addClass('show');
			$list.append('<li class="close">×</li>');

			$('body').css('overflow', 'hidden');

			e.preventDefault();
		});

		$('body').on('click', '.mobile-fs-switcher ul > li', function (e) {
			var $this = $(this),
				$wrapper = $this.closest('.mobile-fs-switcher');

			// Wrap DIV tag.
			// $list.wrap('<div class="mobile-switcher-wrapper"></div>');
			$wrapper.removeClass('show');
			$wrapper.find('li.close').remove();

			$('body').css('overflow', '');
		});
	}


	/**
	 * Wolmart Theme Async Setup
	 * 
	 * Initialize Method which runs asynchronously after document has been loaded
	 * 
	 * @since 1.0
	 */
	Wolmart.initAsync = function () {
		Wolmart.appearAnimate('.appear-animate');            // Runs appear animations
		if (wolmart_vars.resource_disable_elementor && typeof elementorFrontend != 'object') {
			Wolmart.appearAnimate('.elementor-invisible');            // Runs appear animations
			Wolmart.countTo('.elementor-counter-number');             // Runs counter
		}

		if ('cart-popup' == wolmart_vars.cart_popup_type) {
			Wolmart.cartpopup.init();							 // Initialize cartpopup
		} else {
			Wolmart.minipopup.init();                            // Initialize minipopup
		}

		Wolmart.stickyContent('.sticky-content:not(.mobile-icon-bar):not(.sticky-toolbox)'); // Initialize sticky content
		Wolmart.stickyContent('.mobile-icon-bar', Wolmart.defaults.stickyMobileBar);		 // Initialize sticky mobile bar
		Wolmart.stickyContent('.sticky-toolbox', Wolmart.defaults.stickyToolbox);			 // Initialize sticky toolbox
		Wolmart.shop.init();                                 // Initialize shop
		Wolmart.initProductSingle();                         // Initialize single product
		setTimeout(function () { Wolmart.initSlider('.slider-wrapper:not(.product-hover-slider):not(.product-thumbs)') });                       // Initialize slider
		setTimeout(function () { Wolmart.initSlider('.product-hover-slider') }, 300);	 // Initialize Product Hover Slider
		Wolmart.sidebar('left-sidebar');                     // Initialize left sidebar
		Wolmart.sidebar('right-sidebar');                    // Initialize right sidebar
		Wolmart.sidebar('top-sidebar');                      // Initialize horizontal filter widgets
		Wolmart.quantityInput('.qty');                       // Initialize quantity input
		Wolmart.playableVideo('.post-video');                // Initialize playable video
		Wolmart.accordion('.card-header > a');               // Initialize accordion
		Wolmart.tab('.nav-tabs:not(.wolmart-comment-tabs)');                            // Initialize tab
		Wolmart.alert('.alert');                             // Initialize alert
		Wolmart.parallax('.parallax');                       // Initialize parallax
		Wolmart.countTo('.count-to');                        // Initialize countTo
		Wolmart.countdown('.product-countdown, .countdown:not(.lottery-time)'); // Initialize countdown
		Wolmart.menu.init();                                 // Initialize menus
		Wolmart.initPopups();                                // Initialize popups: login, register, play video, newsletter popup
		Wolmart.initAccount();                               // Initialize account popup
		Wolmart.initScrollTopButton();                       // Initialize scroll top button.
		setTimeout(Wolmart.initScrollTo);                    // Initialize scroll top button.
		Wolmart.initContactForms();                          // Initialize contact forms
		Wolmart.initSearchForm();                            // Initialize search form
		Wolmart.initVideoPlayer();							 // Initialize VideoPlayer
		Wolmart.initAjaxLoadPost();							 // Initialize AjaxLoadPost
		Wolmart.floatSVG('.float-svg');                      // Floating SVG
		Wolmart.initElementor();							 // Compatibility with Elementor
		Wolmart.initVendorCompatibility();                   // Compatibility with Vendor Plugins
		Wolmart.initFloatingElements()						 // Initialize floating widgets
		setTimeout(Wolmart.initAdvancedMotions);			 // Initialize scrolling widgets
		Wolmart.initCookiePopup(); 							 // Initialize Cookie Popup
		Wolmart.currencySwitcher.init();
		Wolmart.initMobileSwitcher();						 // Initialize mobile switcher

		// Setup Events
		Wolmart.$window.on('resize', Wolmart.onResize);

		// Complete!
		Wolmart.status == 'load' && (Wolmart.status = 'complete');
		Wolmart.$window.trigger('wolmart_complete');

		// For admin
		Wolmart.showEditPageTooltip();
	}
})(jQuery);