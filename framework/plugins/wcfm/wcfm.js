/**
 * Wolmart-WCFM frontend manager
 */
'use strict';
window.Wolmart || (window.Wolmart = {});

(function ($) {
	/**
	 * Private Properties for Product Image Swatch Admin
	 */
	var file_frame, $btn;

	/**
	 * Product Image Swatch methods for WCFM
	 */
	var Wcfm = {
		/**
		 * Initialize Wcfm object
		 */
		init: function () {
			$(document.body).on('click', '#wcfm_products_manage_form_swatch_head', function (e) {
				e.preventDefault();
				Wcfm.renderSwatchView();
			});

			if ($('#product_type').length > 0) {
				// var pro_types = ["simple", "variable", "grouped", "external", "booking"];
				$('#product_type').change(function () {
					var product_type = $(this).val();

					if ('variable' == product_type) {
						Wcfm.renderSwatchView();
					}
				});
			}
			this.renderSwatchView = this.renderSwatchView.bind(this);
			this.onAddImage = this.onAddImage.bind(this);
			this.onSelectImage = this.onSelectImage.bind(this);
			this.onRemoveImage = this.onRemoveImage.bind(this);
		},

		/**
		 * render interface to add swatches
		 */
		renderSwatchView: function () {
			var data = {
				action: 'generate_image_swatches',
				wcfm_products_manage_form: $('#wcfm_products_manage_form').serialize(),
				wcfm_swatch_nonce: wcfm_lib_swatch.wcfm_swatch_nonce,
			}

			$.ajax({
				type: 'POST',
				url: wolmart_vars.ajax_url,
				data: data,
				success: function (response) {
					if (response.attr_enabled) {
						$('#wcfm_products_manage_form_image_swatches_empty_expander').addClass('wcfm_custom_hide');
						$('#wcfm_products_manage_form_image_swatches_expander').removeClass('wcfm_custom_hide');

						// if new attributes are added for swatch or removed, re-render the swatch option area in shop manager.
						if (response.swatch_count !== $('#wcfm_products_manage_form_image_swatches_expander table tbody tr').length) {
							$('#wcfm_products_manage_form_image_swatches_expander').html(response.template);
						}
					}

					if ($('.image-swatches-accordion').length) {
						// Make each attribute swatch accordion
						Wolmart.accordion('#wolmart-wcfm-swatch-options .card-header > a');

						// Resize the height of wrapper
						$('.image-swatches-accordion .card-header').on('click', Wcfm.resizeWrapperHeight);
					}

					if ($('.img_tip, .text_tip').length > 0) {
						// Initialize Tooltip
						Wcfm.initToolTip();
					}

					if ($('.wolmart_wcfm_add_image_button').length > 0) {
						$(document.body).on('click', '.wolmart_wcfm_add_image_button', Wcfm.onAddImage);
					}

					if ($('.wolmart_wcfm_remove_image_button').length > 0) {
						$(document.body).on('click', '.wolmart_wcfm_remove_image_button', Wcfm.onRemoveImage);
					}

					if ($('.wolmart-wcfm-save-changes').length > 0) {
						$(document.body).on('click', '.wolmart-wcfm-save-changes', Wcfm.onSave);
					}
				},
				dataType: 'json'
			});
		},

		/**
		 * Initialize tooltip
		 */
		initToolTip: function () {
			$('.img_tip, .text_tip').each(function () {
				$(this).qtip({
					content: $(this).attr('data-tip'),
					position: {
						my: 'top center',
						at: 'bottom center',
						viewport: $(window)
					},
					show: {
						//event: 'mouseover mouseenter',
						solo: true,
					},
					hide: {
						inactive: 60000,
						fixed: true
					},
					style: {
						classes: 'qtip-dark qtip-shadow qtip-rounded qtip-wcfm-css qtip-wcfm-core-css'
					}
				});
			});
		},

		/**
		 * Resize wrapper's height according the swatch's container height
		 */
		resizeWrapperHeight: function () {
			var $this = $(this);
			var wrapper = $('.wcfm-tabWrap');
			if ($this.parent().parent().find('.card-body.expanded').length > 0) {
				wrapper.height(wrapper.height() - $this.parent().parent().find('.card-body.expanded').height());
			}

			setTimeout(function () {
				var p_height = $this.parent().find('.card-body').height();
				if ($this.find('.collapse').length > 0) {
					wrapper.height(wrapper.height() + p_height);
				}
			}, 400)
		},

		/**
		 * Require save
		 */
		requireSave: function () {
			$('#wolmart-wcfm-swatch-options .wolmart-wcfm-save-changes').removeAttr('disabled');
			$('#wolmart-wcfm-swatch-options .wolmart-wcfm-cancel-changes').removeAttr('disabled');
		},

		/**
		 * Event handler on image selected
		 */
		onSelectImage: function () {
			var attachment = file_frame.state().get('selection').first().toJSON(),
				$img = $btn.siblings('img');
			$img.attr('src', attachment.url);
			$btn.siblings('input').val(attachment.id);
			file_frame.close();
			this.requireSave();
		},

		/**
		 * Event handler on image added
		 */
		onAddImage: function (e) {
			$btn = $(e.currentTarget);

			// If the media frame already exists
			file_frame || (
				// Create the media frame.
				file_frame = wp.media.frames.downloadable_file = wp.media({
					title: wcfm_lib_swatch.file_frame_title,
					button: {
						text: wcfm_lib_swatch.file_frame_btn
					},
					multiple: false
				}),

				// When an image is selected, run a callback.
				file_frame.on('select', this.onSelectImage)
			);

			file_frame.open();
			e.preventDefault();
		},

		/**
		 * Event handler on remove image
		 */
		onRemoveImage: function (e) {
			var $btn = $(e.currentTarget),
				$img = $btn.siblings('img');
			$img.attr('src', wcfm_lib_swatch.placeholder);
			$btn.siblings('input').val('');
			this.requireSave();
			e.preventDefault();
		},

		/**
		 * Event handler on save image 
		 */
		onSave: function (e) {
			e.preventDefault();
			$('#wcfm_products_simple_submit_button').trigger('click');
		},
	}

	Wolmart.Wcfm = Wcfm;

	Wolmart.$window.on('wolmart_complete', function () {
		Wolmart.Wcfm.init();
	})
})(jQuery);