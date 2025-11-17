/**
 * Wolmart Custom Tab Admin Library
 */
(function (wp, $) {
    'use strict';

    window.Wolmart = window.Wolmart || {};
    Wolmart.admin = Wolmart.admin || {};


    var ProductCustomTab = {
        init: function () {
            var self = this;

            $('.save_wolmart_product_desc').on('click', self.onSave);
        },

        /**
         * Event handler on save
         */
        onSave: function (e) {
            e.preventDefault();

            var tabs = [];
            var keys = ['1st', '2nd'];

            var $wrapper = $('#wolmart_custom_tab_options');
            $wrapper.block(
                {
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                }
            );

            keys.forEach(function (item) {
                var title = $('#wolmart_custom_tab_options').find('.wolmart_custom_tab_title_' + item + '_field input').val();
                if (title && tinymce.editors['wolmart_custom_tab_content_' + item]) {
                    var content = tinymce.editors['wolmart_custom_tab_content_' + item].getContent();
                    if (content) {
                        tabs[item] = [];
                        tabs[item][0] = title;
                        tabs[item][1] = content;
                    }
                }
            })

            var data = {
                action: "wolmart_save_product_tabs",
                nonce: wolmart_product_custom_tab_vars.nonce,
                post_id: wolmart_product_custom_tab_vars.post_id,
                wolmart_custom_tabs: tabs,
            };
            if (tabs['1st']) {
                data.wolmart_custom_tab_1st = tabs['1st'];
            }
            if (tabs['2nd']) {
                data.wolmart_custom_tab_2nd = tabs['2nd'];
            }

            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: wolmart_product_custom_tab_vars.ajax_url,
                data: data,
                success: function () {
                    $wrapper.unblock();

                    // Update Meta Fields
                    var metaFields = Array(
                        'wolmart_custom_tab_title_1st',
                        'wolmart_custom_tab_content_1st',
                        'wolmart_custom_tab_title_2nd',
                        'wolmart_custom_tab_content_2nd'
                    );
                    metaFields.forEach(element => {
                        var metaId = $('[value="' + element + '"]').closest('tr').attr('id'),
                            metaValue = $('#' + element).val();

                        if (!$('#wp-' + element + '-wrap').hasClass('html-active') && (element == 'wolmart_custom_tab_content_1st' || element == 'wolmart_custom_tab_content_2nd')) {
                            metaValue = tinymce.editors[element].getContent();
                        }

                        $('#' + metaId + '-value').html(metaValue);
                    });
                }
            });
        },
    }
    /**
     * Product Image Admin Swatch Initializer
     */
    Wolmart.admin.productCustomTab = ProductCustomTab;

    $(document).ready(function () {
        Wolmart.admin.productCustomTab.init();
    });
})(wp, jQuery);
