/**
 * Wolmart Product Data Addons Admin Library
 */
(function (wp, $) {
    'use strict';

    window.WolmartAdmin = WolmartAdmin || {};

    var productDataAddons = {
        init: function () {
            var self = this;

            $('.wolmart_custom_labels .color-picker').wpColorPicker();

            // Datepicker fields
            $('#wolmart_virtual_buy_time').length && $.fn.datepicker &&
                $('#wolmart_virtual_buy_time').datepicker({
                    defaultDate: '',
                    dateFormat: 'yy-mm-dd',
                    numberOfMonths: 1,
                    showButtonPanel: true,
                    maxDate: new Date()
                });

            $('.save_wolmart_product_options').on('click', self.onExtraSave);
            $('#wolmart_add_custom_label').on('click', self.addLabel);
            $('.wolmart_custom_labels').on('click', '.delete', self.removeLabel);
            $('.wolmart_custom_labels').on('change', '.custom_label_type', self.typeSelected);
            $(document).off('click', '.btn_upload_img').on('click', '.btn_upload_img', self.uploadFile);
            self.sortableLabels();
        },

        /**
         * Add a custom label
         */
        addLabel: function () {
            var form = $(this).closest('.form-field');
            form.siblings('.wc-metabox-template').clone().show().appendTo(form.find('.wc-metaboxes'));

            if ($.fn.wpColorPicker) {
                form.find('.color-picker').addClass('wolmart-color-picker');
                form.find('input.wolmart-color-picker').wpColorPicker();
            }
        },

        /**
         * Remove a custom label
         */
        removeLabel: function (e) {
            e.preventDefault();
            $(this).closest('.wc-metabox').remove();
        },

        /**
         * Selected Label Type
         */
        typeSelected: function () {
            if ($(this).val()) {
                $(this).siblings('.text-controls').hide();
                $(this).siblings('.image-controls').show();
            } else {
                $(this).siblings('.image-controls').hide();
                $(this).siblings('.text-controls').show();
            }
        },

        /**
         * Upload Label Image
         */
        uploadFile: function (e) {

            var file_frame;
            var $this = $(this),
                $prev = $this.prev(),
                $next = $this.next();

            // If the media frame already exists, reopen it.
            if (!file_frame) {
                // Create the media frame.
                file_frame = wp.media.frames.downloadable_file = wp.media(
                    {
                        title: 'Choose an image',
                        button: {
                            text: 'Use image'
                        },
                        multiple: false
                    }
                );
            }

            file_frame.open();

            // When an image is selected, run a callback.
            file_frame.on(
                'select',
                function () {
                    var attachment = file_frame.state().get('selection').first().toJSON();
                    $prev.val(attachment.url);
                    file_frame.close();
                    $next.val(attachment.id);
                }
            );
            e.preventDefault();
        },

        /**
         * Sortable Custom Labels
         */
        sortableLabels: function () {
            // Attribute ordering.
            $('.wolmart_custom_labels .wc-metaboxes').sortable(
                {
                    items: '.wc-metabox',
                    cursor: 'move',
                    axis: 'y',
                    handle: 'h3',
                    scrollSensitivity: 40,
                    forcePlaceholderSize: true,
                    helper: 'clone',
                    opacity: 0.65,
                    placeholder: 'wc-metabox-sortable-placeholder',
                    start: function (event, ui) {
                        ui.item.css('background-color', '#f6f6f6');
                    },
                    stop: function (event, ui) {
                        ui.item.removeAttr('style');
                        // attribute_row_indexes();
                    }
                }
            );
        },

        /**
         * Event handler on save
         */
        onExtraSave: function (e) {
            e.preventDefault();

            var extra = [],
                $wrapper = $('#wolmart_data_addons');

            extra['wolmart_custom_labels'] = [];
            extra['wolmart_virtual_buy_time'] = $('#wolmart_virtual_buy_time').val();
            extra['wolmart_virtual_buy_time_text'] = $('#wolmart_virtual_buy_time_text').val();
            $('.wolmart_custom_labels').find('.wc-metabox').each(
                function () {
                    var each = {};
                    each.type = $(this).find('.custom_label_type').val();
                    if (each.type) {
                        each.img_url = $(this).find('.label_image').val();
                        each.img_id = $(this).find('.label_image_id').val();
                    } else {
                        each.label = $(this).find('.label_text').val();
                        each.color = $(this).find('[name="label_color"]').val();
                        each.bgColor = $(this).find('[name="label_bgcolor"]').val();
                    }
                    if ((!each.type && each.label) || (each.type && each.img_url)) {
                        extra['wolmart_custom_labels'].push(each);
                    }
                }
            )

            $wrapper.block(
                {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                }
            );

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: wolmart_product_data_addon_vars.ajax_url,
                data: {
                    action: "wolmart_save_product_extra_options",
                    nonce: wolmart_product_data_addon_vars.nonce,
                    post_id: wolmart_product_data_addon_vars.post_id,
                    wolmart_custom_labels: extra['wolmart_custom_labels'],
                    wolmart_virtual_buy_time: extra['wolmart_virtual_buy_time'],
                    wolmart_virtual_buy_time_text: extra['wolmart_virtual_buy_time_text']
                },
                success: function () {
                    $wrapper.unblock();

                    // Update Meta Fields
                    var metaFields = Array(
                        'wolmart_custom_labels',
                        'wolmart_virtual_buy_time',
                        'wolmart_virtual_buy_time_text'
                    );
                    metaFields.forEach(element => {
                        var metaId = $('[value="' + element + '"]').closest('tr').attr('id'),
                            metaValue = extra[element];

                        $('#' + metaId + '-value').html(JSON.stringify(metaValue));
                    });
                }
            });
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
    WolmartAdmin.productDataAddons = productDataAddons;

    $(document).ready(function () {
        WolmartAdmin.productDataAddons.init();
    });
})(wp, jQuery);
