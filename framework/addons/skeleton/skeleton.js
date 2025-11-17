/**
 * Wolmart Plugin - Skeleton
 * 
 * @requires imagesLoaded
 */
'use strict';

(function ($) {
	function Skeleton($elems, callback) {
		this.swap = this.swap.bind(this);
		this.init($elems, callback);
		return this;
	}

	Skeleton.prototype.init = function ($elems, callback) {
		var content = '';

		this.$elems = $elems;
		typeof callback == 'undefined' || (this.callback = callback);

		this.$elems.find('script[type="text/template"]').each(function () { content += JSON.parse(this.text); });
		$(content).imagesLoaded(this.swap);
	}

	Skeleton.prototype.swap = function () {
		this.$elems.find('script[type="text/template"]').each(function () {
			this.parentElement.removeChild(this.nextElementSibling);
			this.outerHTML = JSON.parse(this.text);
		});
		this.$elems.removeClass('skeleton-body');

		this.callback && this.callback();

		// Run elementor animation
		if (typeof elementorFrontend != 'undefined') {
			this.$elems.find('.elementor-invisible').each(function () {
				var $this = $(this),
					animation = $this.data('settings') ? $this.data('settings').animation || $this.data('settings')._animation : 'none';

				if ('none' === animation) {
					$this.removeClass('elementor-invisible');
				} else {
					Wolmart.appear(this, function () {
						var animationDelay = $this.data('settings') ? $this.data('settings').animation_delay || $this.data('settings')._animation_delay || 0 : 0;
						$this.removeClass(animation);
						setTimeout(function () {
							$this.removeClass('elementor-invisible').addClass('animated ' + animation);
						}, animationDelay);
					}, { alwaysObserve: false });
				}
			});
		}
	}

	/**
	 * @function skeleton
	 * @param {jQuery} $elems Elements to load skeleton
	 * @param {function} callback Function that run after skeleton has been loaded
	 */
	Wolmart.skeleton = function ($elems, callback) {
		return new Skeleton($elems, callback);
	}
})(jQuery);
