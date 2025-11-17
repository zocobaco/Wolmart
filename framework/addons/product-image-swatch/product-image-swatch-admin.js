/**
 * Wolmart Swatch Admin Library
 */
(function (wp, $) {
	'use strict';

	window.Wolmart = window.Wolmart || {};
	Wolmart.admin = Wolmart.admin || {};

	/**
	 * Private Properties for Product Image Swatch Admin
	 */
	var file_frame, $btn;

	/**
	 * Product Image Swatch methods for Admin
	 */
	var SwatchAdmin = {
		/**
		 * Initialize Image Swatch for Admin
		 */
		init: function () {
			this.onAddImage = this.onAddImage.bind(this);
			this.onRemoveImage = this.onRemoveImage.bind(this);
			this.onSelectImage = this.onSelectImage.bind(this);
			this.onSave = this.onSave.bind(this);
			this.onCancel = this.onCancel.bind(this);

			$('#swatch_product_options select').on('change', this.requireSave);

			$(document.body)
				.on('click', '#swatch_product_options .button_upload_image', this.onAddImage)
				.on('click', '#swatch_product_options .button_remove_image', this.onRemoveImage)
				.on('click', '#swatch_product_options .wolmart-admin-save-changes', this.onSave)
				.on('click', '#swatch_product_options .wolmart-admin-cancel-changes', this.onCancel);
		},
		/**
		 * Require save
		 */
		requireSave: function () {
			$('#swatch_product_options .wolmart-admin-save-changes').removeAttr('disabled');
			$('#swatch_product_options .wolmart-admin-cancel-changes').removeAttr('disabled');
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
					title: lib_swatch_admin.file_frame_title,
					button: {
						text: lib_swatch_admin.file_frame_btn
					},
					multiple: false
				}),

				// When an image is selected, run a callback.
				file_frame.on('select', this.onSelectImage)
			);

			file_frame.open();
			this.requireSave();
			e.preventDefault();
		},

		/**
		 * Event handler on image removed
		 */
		onRemoveImage: function (e) {
			var $btn = $(e.currentTarget),
				$img = $btn.siblings('img');
			$img.attr('src', lib_swatch_admin.placeholder);
			$btn.siblings('input').val('');
			this.requireSave();
			e.preventDefault();
		},

		/**
		 * Event handler on save
		 */
		onSave: function (e) {
			// confirm("Do you want to reload this page to save?") || e.preventDefault();
		},

		/**
		 * Event handler on save
		 */
		onCancel: function (e) {
			confirm("Changes are cancelled. Do you want to reload this page?") && window.location.reload();
		}
	}


	/**
	 * Product Image Admin Swatch Initializer
	 */
	Wolmart.admin.swatchAdmin = SwatchAdmin;

	$(document).ready(function () {
		Wolmart.admin.swatchAdmin.init();
	});
})(wp, jQuery);
