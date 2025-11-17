/**
 * Javascript Library for Layout Builder Admin
 * 
 * - Page Layouts Model
 * 
 * @since 1.0
 * @package  Wolmart WordPress Framework
 */
'use strict';

window.WolmartAdmin = window.WolmartAdmin || {};

(function ($) {

    /**
     * Layout Builder Model Class
     * 
     * @since 1.0
     */
    var LayoutBuilderModel = {
        /**
         * Setup layout builder model.
         *
         * @since 1.0
         */
        init: function () {
            this.conditions = JSON.parse(JSON.stringify(wolmart_layout_vars.conditions)) || {};
            this.schemes = wolmart_layout_vars.schemes || {};
            this.clipboard = false;
            this.controls = [];
            for (var part in wolmart_layout_vars.controls) {
                if (!part.startsWith('content')) {
                    for (var key in wolmart_layout_vars.controls[part]) {
                        this.controls.push(key);
                    }
                }
            }
        },

        /**
         * Get conditions by category or get all conditions.
         * 
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         */
        getConditions: function (category = '', conditionNo = -1) {
            if (!category) {
                return this.conditions;
            }
            if (!this.conditions[category]) {
                this.conditions[category] = [];
            }
            if (conditionNo >= 0 && this.conditions[category][conditionNo]) {
                return this.conditions[category][conditionNo];
            }
            return this.conditions[category];
        },

        /**
         * Get layout option values by category.
         * 
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         */
        getOptionValues: function (category, conditionNo) {
            return this.conditions[category] && this.conditions[category][conditionNo] ?
                this.conditions[category][conditionNo].options : false;
        },

        /**
         * Get condition title of given category.
         *
         * @since 1.0
         * @param {string} category 
         * @param {boolean} getLayoutTitle
         */
        getConditionTitle: function (category, getLayoutTitle) {
            if (category && this.schemes[category]) {
                return getLayoutTitle ? this.schemes[category].layout_title : this.schemes[category].title;
            }
            return '';
        },

        /**
         * Get condition title of given category.
         *
         * @since 1.0
         * @param {string} category 
         * @param {boolean} getLayoutTitle
         */
        getConditionDescription: function (category) {
            if (category && this.schemes[category]) {
                return this.schemes[category].layout_description;
            }
            return '';
        },

        /**
         * Set condition title
         * @param {string} category
         * @param {number} conditionNo
         * @param {string} title
         */
        setConditionTitle: function (category, conditionNo, title) {
            if (this.conditions[category][conditionNo]) {
                this.conditions[category][conditionNo].title = title;
            }
            this.requireSave();
        },

        /**
         * Get scheme by category.
         * 
         * @since 1.0
         * @param {string} category
         * @param {string} type
         */
        getScheme: function (category, type = '') {
            return type ?
                this.schemes[category].scheme[type] :
                this.schemes[category].scheme;
        },

        /**
         * Get layout option controls for given layout part.
         * 
         * @since 1.0
         * @param {string} part
         */
        getOptionControls: function (part) {
            return wolmart_layout_vars.controls[part] ? wolmart_layout_vars.controls[part] : false;
        },

        /**
         * Get templates by block type.
         * 
         * @since 1.0
         * @param {string} block_type
         */
        getTemplates: function (block_type) {
            return wolmart_layout_vars.templates[block_type];
        },

        /**
         * Check if new conditions could be added for given category.
         * 
         * @since 1.0
         * @param {string} category Conditions category to check
         * @param {string} type Condition type to check.
         */
        canExtendCondition: function (category, type = '') {
            if (!category ||
                !this.schemes[category] ||
                !this.schemes[category].scheme) {
                return false;
            }
            return !type || (this.schemes[category].scheme[type] && (this.schemes[category].scheme[type].list || this.schemes[category].scheme[type].ajaxselect));
        },

        /**
         * Update condition UI.
         * @since 1.0
         * @param {string} category 
         */
        updateCategoryUI: function (category = '') {

            var _updateCategoryUI = (function (category) {
                // update UI
                var $count = $('.wolmart-condition-cat-' + category + '> .wolmart-condition-count');
                var count = this.conditions[category].filter(function (v) { return v }).length;
                $count.text(count);
                count ? $count.slideDown() : $count.slideUp();
            }).bind(this);


            // update special category
            category && _updateCategoryUI(category);

            // count total
            var count = 0;
            for (var cat in this.conditions) {
                count += this.conditions[cat].filter(function (v) { return v }).length;
                // update all categories
                category || _updateCategoryUI(cat);
            }
            $('.wolmart-condition-cat-site > .wolmart-condition-count').text(count).slideDown();
        },

        /**
         * Add a new empty condition.
         * 
         * @since 1.0
         * @param {string} category
         * @param {string} type
         * @return {number} added index
         */
        addCondition: function (category) {
            if (!this.conditions[category]) {
                this.conditions[category] = [];
            }

            var data = {};
            data.title = this.getConditionTitle(category, true) + ' ' + (this.conditions[category].length + 1);
            data.scheme = {};
            if (this.schemes[category].scheme && this.schemes[category].scheme.all) {
                data.scheme.all = true;
            }
            this.conditions[category].push(data);

            this.updateCategoryUI(category);
            this.requireSave();

            // return added index
            return this.conditions[category].length - 1;
        },

        /**
         * Delete a condition.
         * 
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         */
        deleteCondition: function (category, conditionNo) {
            if ('undefined' != typeof this.conditions[category][conditionNo]) {
                this.conditions[category].splice(conditionNo, 1);
                $('.wolmart-layout-item[data-category=' + category + ']').each(function () {
                    var no = this.getAttribute('data-condition-no');
                    if (no > conditionNo) {
                        this.setAttribute('data-condition-no', no - 1);
                        $(this).data('condition-no', no - 1)
                    }
                })
                $('#wolmart_layout_content').isotope('updateSortData').isotope();
            }
            this.updateCategoryUI(category);
            this.requireSave();
        },

        /**
         * Reset a condition. If no parameter is given, all options will be reset.
         * 
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         */
        // resetCondition: function (category, conditionNo) {
        //     if (category) {
        //         this.conditions[category][conditionNo] =
        //             wolmart_layout_vars.conditions[category] && wolmart_layout_vars.conditions[category][conditionNo] ?
        //                 JSON.parse(JSON.stringify(wolmart_layout_vars.conditions[category][conditionNo])) :
        //                 {};
        //     } else {
        //         this.conditions =
        //             wolmart_layout_vars.conditions ?
        //                 JSON.parse(JSON.stringify(wolmart_layout_vars.conditions)) :
        //                 {};
        //     }
        //     this.requireSave();
        // },

        /**
         * Duplicate a condition.
         *
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         */
        duplicateCondition: function (category, conditionNo) {
            if (category && 'number' == typeof conditionNo && this.conditions[category][conditionNo]) {
                var duplicated = JSON.parse(JSON.stringify(this.conditions[category][conditionNo]));

                $('.wolmart-layout-item[data-category=' + category + ']').each(function () {
                    var no = this.getAttribute('data-condition-no');
                    if (no > conditionNo) {
                        this.setAttribute('data-condition-no', no * 1 + 1);
                        $(this).data('condition-no', no * 1 + 1)
                    }
                })

                this.conditions[category].splice(conditionNo, 0, duplicated);
                this.updateCategoryUI(category);
                this.requireSave();
                return conditionNo + 1;
            }
        },

        /**
         * Copy options
         * 
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         */
        copyOptions: function (category, conditionNo) {
            this.clipboard = {
                category: category,
                options: this.getOptionValues(category, conditionNo)
            };
        },

        /**
         * Paste options
         * 
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         * @param {jQuery} $item
         */
        pasteOptions: function (category, conditionNo, $item) {
            if (this.clipboard) {
                if (category == this.clipboard.category) {
                    // paste all options
                    if (this.conditions[category][conditionNo]) {
                        this.conditions[category][conditionNo].options = this.clipboard.options;
                    } else {
                        this.conditions[category][conditionNo] = { options: this.clipboard.options };
                    }
                } else {
                    if (this.conditions[category][conditionNo].options) {
                        // remove current options except content
                        for (var optionName in this.conditions[category][conditionNo].options) {
                            if (this.controls.indexOf(optionName)) {
                                delete this.conditions[category][conditionNo].options[optionName];
                            }
                        }
                    } else {
                        this.conditions[category][conditionNo].options = {};
                    }
                    // paste copied options except content
                    for (var optionName in this.clipboard.options) {
                        if (this.controls.indexOf(optionName)) {
                            this.conditions[category][conditionNo].options[optionName] = this.clipboard.options[optionName];
                        }
                    }
                }

                LayoutBuilderView.refreshLayoutStatus($item);
                this.requireSave();
            }
        },

        /**
         * Notify that save is required.
         * 
         * @since 1.0
         */
        requireSave: function () {
            $('.wolmart-layouts-save').addClass('require-save');
        },

        /**
         * Add a new condition with type or update existing condition's type.
         *
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         * @param {string} scheme
         * @param {mixed} value {boolean} isChecked or {array} list
         */
        setConditionScheme: function ($item, category, conditionNo, scheme, value) {
            if ('undefined' == typeof this.conditions[category][conditionNo]) { // add
                var v = {};
                v[scheme] = value;
                v.all = true;
                this.conditions[category][conditionNo] = { scheme: v };

            } else if (this.conditions[category][conditionNo]) { // update
                if (!this.conditions[category][conditionNo].scheme) {
                    var v = {};
                    v[scheme] = value;
                    this.conditions[category][conditionNo].scheme = v;
                }
                if (value) {
                    this.conditions[category][conditionNo].scheme[scheme] = value;
                } else {
                    delete this.conditions[category][conditionNo].scheme[scheme];
                }
            }

            $item.addClass('edited');

            this.requireSave();
        },

        /**
         * Set type and list for given condition.
         *
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         * @param {string} type
         * @param {array} list
         */
        setConditionList: function (category, conditionNo, type, list) {
            this.conditions[category][conditionNo] = list ? { type: type, list: list } : { type: type };
            this.requireSave();
        },

        /**
         * Set layout options for given condition.
         *
         * @since 1.0
         * @param {string} category
         * @param {number} conditionNo
         * @param {string} option
         * @param {mixed} value
         */
        setConditionOption: function (category, conditionNo, option, value) {
            if (!this.conditions[category][conditionNo].options) {
                this.conditions[category][conditionNo].options = {};
            }
            if (value) {
                this.conditions[category][conditionNo].options[option] = value;
            } else {
                delete this.conditions[category][conditionNo].options[option];
            }
            this.requireSave();
        },

        /**
         * Save all modifications of conditions.
         * 
         * @since 1.0
         */
        save: function () {
            $('.wolmart-layouts-save').removeClass('require-save');

            if (typeof window.top.wolmart_core_vars.layout_save != 'undefined') {
                window.top.wolmart_core_vars.layout_save = false;
            }

            $.post(wolmart_core_vars.ajax_url, {
                action: 'wolmart_layout_builder_save',
                nonce: wolmart_core_vars.nonce,
                conditions: this.conditions
            }, function () {
                var layouts = window.top.document.querySelector('.layout-builder');
                if ($(layouts).length) {
                    $(layouts).addClass('closed');
                    $(layouts).siblings('.blocks-overlay').addClass('closed');
                }
            }).fail(function () {
                $('.wolmart-layouts-save').addClass('require-save');
                $('.wolmart-modal-message').remove(); // issue : show message
                $('.wolmart-layouts-save').before('<span class="wolmart-modal-message"></span>');
            });
        }
    }

    /**
     * Layout Builder View Class
     * 
     * @since 1.0
     */
    var LayoutBuilderView = {
        /**
         * Setup layout builder view.
         *
         * @since 1.0
         */
        init: function () {

            // Delete button
            this.buttonDelete = '<button class="wolmart-condition-button wolmart-condition-delete w-icon-trash-alt"></button>';
            // Duplicate button
            this.buttonDuplicate = '<button class="wolmart-condition-button wolmart-condition-duplicate w-icon-clone"></button>';
            // Set conditions button
            this.buttonSet = '<button class="wolmart-condition-button wolmart-condition-set w-icon-cog2"></button>';

            // get layout box template.
            this.layoutBoxTemplate = $('#wolmart_layout_template').text();
            $('#wolmart_layout_template').remove();

            // register events.
            $(document.body)
                // events for context menu
                .on('click', '.wolmart-layouts-save', this.onSave)
                .on('contextmenu', '.wolmart-layout-item', this.onContextMenu.bind(this))
                .on('click', '#wolmart_layout_content', this.closeContextMenu)
                .on('click', '.wolmart-condition-menu > a', this.clickContextMenuItem)
                .on('click', '.wolmart-condition-copy', this.copyOptions)
                .on('click', '.wolmart-condition-paste', this.pasteOptions)
                .on('click', '.wolmart-condition-edit-back', this.goBackFromEdit)

                // events for condition
                .on('click', '.wolmart-condition-cat', this.clickCategory.bind(this))
                .on('click', '.wolmart-layout-more', this.addCondition.bind(this))
                .on('click', '.wolmart-condition-delete', this.deleteCondition.bind(this))
                // .on('click', '.wolmart-condition-reset', this.resetCondition.bind(this))
                // .on('click', '.wolmart-layouts-reset', this.resetAll.bind(this))
                .on('click', '.wolmart-condition-duplicate', this.duplicateCondition.bind(this))
                .on('change', '.wolmart-scheme-options > div > label input[type=checkbox]', this.changeConditionScheme.bind(this))
                .on('change', '.wolmart-scheme-list', this.changeConditionItem)

                // events for layout box
                .on('input', '.wolmart-condition-title', this.changeConditionTitle.bind(this))
                .on('click', '.wolmart-layout .layout-part', this.editPart)
                .on('click', '.wolmart-condition-set', this.editCondition)
                .on('click', '.manage-conditions', this.editCondition)
                .on('click', this.clickOther.bind(this))

                // events for layout control
                .on('change', '.wolmart-block-select input', this.changeBlockMode.bind(this))
                .on('change', '.wolmart-layout-options input', this.changeOptionInput.bind(this))
                .on('change', '.wolmart-layout-options select', this.changeOptionInput.bind(this));

            this.setupLayouts();
        },

        /**
         * Initialize plugins for layout controls.
         *
         * @since 1.0
         */
        refreshUI: function (mode) {
            if (!mode || 'layout' == mode || 'add' == mode) {
                $('#wolmart_layout_content').isotope();
            }
            if (!mode || 'add' == mode) {
                this.refreshLayoutStatus();
            }
        },


        /**
         * Display status of layout parts.
         *
         * @since 1.0
         * @param {jQuery} $container This can be omitted.
         */
        refreshLayoutStatus: function ($container) {
            $container || ($container = $('#wolmart_layout_content'));
            $container.is('.wolmart-layout-item') || ($container = $container.find('.wolmart-layout-item'));
            $container.each(function () {
                var $item = $(this);
                var category = $item.data('category');
                var conditionNo = $item.data('conditionNo');
                var optionValues = LayoutBuilderModel.getOptionValues(category, conditionNo);
                if (optionValues) {

                    for (var part in wolmart_layout_vars.controls) {
                        if (LayoutBuilderModel.controls.indexOf(part)) {
                            var optionControls = LayoutBuilderModel.getOptionControls(part);
                            var $part = $item.find('.layout-part[data-part="' + part + '"]');
                            var set = false;

                            // Reset
                            $part.removeClass('set layout-hide');
                            $part.children('.block-value').text('');

                            // Check set
                            for (var control in optionControls) {
                                if (optionValues[control]) {
                                    set = true;
                                    break;
                                }
                            }

                            if ($container.closest('.wolmart-layouts-container.display-conditions').length) {
                                if (part == 'content_single_product') {
                                    part = 'single_product_block';
                                } else if (part == 'content_archive_product') {
                                    part = 'shop_block';
                                } else if (part == 'content_cart') {
                                    part = 'cart_block';
                                } else if (part == 'content_checkout') {
                                    part = 'checkout_block';
                                } else if (part.indexOf('content_single_') != -1) {
                                    part = 'single_block';
                                } else if (part.indexOf('content_archive_') != -1) {
                                    part = 'archive_block';
                                } else if (part == 'general') {
                                    part = 'popup';
                                }
                            }

                            if (optionControls[part] && 'hide' == optionValues[part]) {
                                // Hide
                                $part.addClass('layout-hide');
                            } else if (set) {
                                // Set
                                $part.addClass('set');
                                if (optionControls[part]) {
                                    var blocks = LayoutBuilderModel.getTemplates(optionControls[part].type.replace('block_', ''));
                                    if (blocks && blocks[optionValues[part]]) {
                                        $part.children('.block-value').text(blocks[optionValues[part]]);
                                    }
                                }
                            }
                        }
                    }
                }
            })
        },

        /**
         * Setup layouts
         * 
         * @since 1.0
         */
        setupLayouts: function () {

            var layoutItems = '';
            var schemes = LayoutBuilderModel.schemes;

            if (schemes) {
                for (var category in schemes) {
                    // add layouts already set
                    var layouts = LayoutBuilderModel.getConditions(category);
                    for (var conditionNo in layouts) {
                        layoutItems += this.getNewConditionUI(category, conditionNo);
                    }

                    // add more button
                    if ('site' != category && (LayoutBuilderModel.canExtendCondition(category) || !layouts.length)) {
                        layoutItems += this.getAddMoreUI(category);
                    }
                }

                // show conditions
                $('#wolmart_layout_content').html(layoutItems).isotope({
                    layoutMode: 'fitRows',
                    filter: '.wolmart-layout-item',
                    sortBy: ['category', 'no'],
                    originLeft: $(document.body).hasClass('rtl') || ($('.wolmart-layout_builder').length && 'rtl' == $('.alpha-layout_builder').css('direction')) ? false : true,
                    getSortData: {
                        category: function (el) {
                            var category = el.getAttribute('data-category');
                            var categories = Object.keys(LayoutBuilderModel.schemes);
                            return categories.indexOf(category);
                        },
                        no: function (el) {
                            return parseInt(el.getAttribute('data-condition-no'));
                        }
                    }
                });

                // init plugins
                LayoutBuilderModel.updateCategoryUI();
                this.refreshUI();
            }
        },


        /**
         * Refresh conditions
         * @param {string} category 
         * @param {number} conditionNo
         */
        refreshCondition: function (category, conditionNo) {
            var selector = '.wolmart-layout-item';
            var view = this;

            category && (selector += '[data-category="' + category + '"]');
            conditionNo && (selector += '[data-condition-no="' + conditionNo + '"]');

            $(selector).each(function () {
                view.editPart({
                    currentTarget: $(this).find('.layout-part.active').get(0)
                })
            });
        },

        /**
         * Event handler to save layout controls.
         *
         * @since 1.0
         */
        onSave: function () {
            LayoutBuilderModel.save();
        },

        /**
         * Event handler to show context menu.
         *
         * @since 1.0
         * @param {Event} e 
         */
        onContextMenu: function (e) {
            this.closeContextMenu();

            var $item = $(e.currentTarget);
            var $container = $('.wolmart-admin-panel-content');
            var containerOffset = $container.get(0).getBoundingClientRect();
            var category = $item.data('category');

            var html = '<div class="wolmart-condition-menu" style="left:' + (e.clientX - containerOffset.x + $container.scrollLeft()) + 'px;top:' + (e.clientY - containerOffset.y + $container.scrollTop()) + 'px;">';
            var prefix = '<a href="#" class="wolmart-condition-';

            html += prefix + 'copy"><i class="w-icon-copy"></i>' + wolmart_layout_vars.text_copy + '</a>';
            if (LayoutBuilderModel.clipboard) {
                html += prefix + 'paste"><i class="w-icon-paste"></i>' + wolmart_layout_vars.text_paste + '</a>';
            }

            if (LayoutBuilderModel.canExtendCondition(category)) {
                html += prefix + 'duplicate"><i class="w-icon-clone"></i>' + wolmart_layout_vars.text_duplicate + '</a>';
            }

            html += prefix + 'set"><i class="w-icon-cog2"></i>' + wolmart_layout_vars.text_options + '</a>';

            html += prefix + 'delete"><i class="w-icon-trash-alt"></i>' + wolmart_layout_vars.text_delete + '</a>';

            html += '</div>';

            $container.append(html);
            $('.wolmart-condition-menu').data('item', $item);
            e.preventDefault();
        },

        /**
         * Close context menu of condition.
         *
         * @since 1.0
         */
        closeContextMenu: function () {
            $('.wolmart-condition-menu').remove();
        },

        /**
         * Event handler to show context menu for condition.
         *
         * @since 1.0
         * @param {Event} e 
         */
        clickContextMenuItem: function (e) {
            e.preventDefault();
        },

        /**
         * Event handler to copy options.
         *
         * @since 1.0
         * @param {Event} e 
         */
        copyOptions: function (e) {
            var $menuItem = $(e.currentTarget);
            var $item = $menuItem.parent().data('item');
            LayoutBuilderModel.copyOptions($item.data('category'), $item.data('condition-no'));
        },

        /**
         * Event handler to paste options.
         * 
         * @since 1.0
         * @param {Event} e 
         */
        pasteOptions: function (e) {
            var $item = $(e.currentTarget).parent().data('item'); // from menu item.
            LayoutBuilderModel.pasteOptions($item.data('category'), $item.data('condition-no'), $item);
        },

        /**
         * Event handler to show conditions by category.
         *
         * @since 1.0
         */
        clickCategory: function (e) {
            var $category = $(e.currentTarget).addClass('active');
            var category = $category.data('category');

            // toggle category
            $category.siblings('.active').removeClass('active');

            // filter layouts
            $('#wolmart_layout_content').isotope({
                filter: 'site' == category ? '.wolmart-layout-item' : '[data-category="' + category + '"]'
            });
        },

        /**
         * Event handler to reset condition.
         *
         * @since 1.0
         */
        // resetCondition: function (e) {
        //     var $reset = $(e.currentTarget);
        //     var $item = $reset.is('.wolmart-condition-menu > a') ?
        //         $reset.parent().data('item') :
        //         $reset.closest('.wolmart-layout-item');

        //     LayoutBuilderModel.resetCondition($item.data('category'), $item.data('condition-no'));

        //     var $activePart = $item.find('.layout-part.active');
        //     $activePart.length &&
        //         this.editPart({
        //             currentTarget: $activePart.get(0)
        //         });
        // },

        /**
         * Event handler to reset all conditions.
         *
         * @since 1.0
         */
        // resetAll: function () {
        //     LayoutBuilderModel.resetCondition();
        //     $('.wolmart-condition-cat-all').click();
        // },

        /**
         * Get add more item html.
         * @param {string} category 
         */
        getAddMoreUI: function (category) {
            return '<div class="wolmart-layout-more-wrap" data-category="' + category + '" data-condition-no="999">' +
                '<div class="wolmart-layout-more">' +
                '<div class="wolmart-layout-more-inner"><i class="wolmart-icon-plus"></i>' +
                wolmart_layout_vars.text_create_layout +
                '<b>' + LayoutBuilderModel.getConditionTitle(category) + '</b>' +
                '</div></div></div>';
        },

        /**
         * Get layout item html to add.
         * @param {string} category 
         * @param {number} conditionNo
         */
        getNewConditionUI: function (category, conditionNo = -1) {

            // Create new
            if (conditionNo == -1) {
                conditionNo = LayoutBuilderModel.addCondition(category);
            }

            var layoutData = LayoutBuilderModel.getConditions(category, conditionNo);
            if (layoutData) {

                var conditionHtml = '',
                    dispCondition = '';
                if (LayoutBuilderModel.canExtendCondition(category)) {
                    var scheme = LayoutBuilderModel.getScheme(category);
                    var schemeClass;
                    var schemeData = layoutData.scheme || {};
                    var isSingle = 0 === category.indexOf('single_'),
                        isArchive = 0 === category.indexOf('archive_');

                    // condition heading
                    conditionHtml += '<div class="wolmart-scheme-options"><label class="apply-text">' +
                        wolmart_layout_vars.text_apply_prefix + LayoutBuilderModel.getConditionTitle(category, true).toLowerCase() + wolmart_layout_vars.text_apply_suffix +
                        '</label>';

                    // condition type
                    for (var schemeKey in scheme) {
                        var conditionTemp = '';
                        schemeClass = 'wolmart-scheme-' + schemeKey;
                        if (!(schemeData && schemeData.all && schemeKey == 'all') && !schemeData[schemeKey]) {
                            schemeClass += ' disabled';
                        }

                        conditionHtml += '<div class="' + schemeClass + '" data-scheme="' + schemeKey + '">';
                        conditionHtml += '<label><input type="checkbox"' + (schemeData[schemeKey] ? ' checked' : '') + '>' + scheme[schemeKey].title + '</label>';

                        var list = scheme[schemeKey].list;

                        var description = scheme[schemeKey].title;
                        if (typeof scheme[schemeKey].description != 'undefined') {
                            description = scheme[schemeKey].description;
                        } else if (isSingle) {
                            description = scheme[schemeKey].title + ' ' + wolmart_layout_vars.text_single_prefix;
                        } else if (isArchive) {
                            description = wolmart_layout_vars.text_archive_prefix;
                        }

                        if (list) {
                            conditionHtml += '<select multiple class="wolmart-scheme-list" data-placeholder="' + scheme[schemeKey].placeholder + '">';
                            var selectedItems = '';
                            for (var item in list) {
                                var selected = typeof schemeData[schemeKey] == 'object' && (typeof schemeData[schemeKey][item] != 'undefined' || typeof schemeData[schemeKey][1 * item] != 'undefined');

                                conditionHtml += '<option value="' + item + '"' + (selected ? ' selected' : '') + '>' + list[item] + '</option>';

                                if (selected) {
                                    selectedItems += selectedItems ? (', ' + list[item]) : list[item];
                                }
                            }
                            if (-1 === schemeClass.indexOf('disabled')) {
                                if (selectedItems) {
                                    if (isArchive) {
                                        conditionTemp = '<li>' + description + ' ' + scheme[schemeKey].title + ' ' + wolmart_layout_vars.text_single_prefix + ' ' + selectedItems + '</li>';
                                    } else {
                                        conditionTemp = '<li>' + description + ' ' + selectedItems + '</li>';
                                    }
                                } else if (isArchive) {
                                    conditionTemp = '<li>' + description + ' ' + scheme[schemeKey].title + '</li>';
                                }
                            }
                            conditionHtml += '</select>';
                        } else if (scheme[schemeKey].ajaxselect) {
                            var option = 'child' == schemeKey ? 'page' : schemeKey;
                            option = 'page' == option ? 'page_ex' : option;

                            var value = schemeData[schemeKey],
                                valueHtml = value;
                            if (Array.isArray(value)) {
                                valueHtml = '';
                                schemeData[schemeKey].forEach(function (item) {
                                    if (item) {
                                        valueHtml += valueHtml ? (', ' + item) : item;
                                    }
                                });
                                value = schemeData[schemeKey].join(',');
                            } else if ('object' == typeof value) {
                                valueHtml = '';
                                Object.values(value).forEach(function (item) {
                                    if (item) {
                                        valueHtml += valueHtml ? (', ' + item) : item;
                                    }
                                });
                                value = Object.keys(value).join(',');
                            }

                            conditionHtml += '<select multiple class="wolmart-scheme-list ajaxselect2" data-placeholder="' + scheme[schemeKey].placeholder + '" data-load-option="' + option + '"' + (schemeData[schemeKey] ? ' data-values="' + value + '"' : '') + '>';
                            conditionHtml += '</select>';

                            if (typeof value != 'undefined') {
                                if (!isSingle) {
                                    conditionTemp = '<li>' + description + scheme[schemeKey].title + ' ' + wolmart_layout_vars.text_single_prefix + ' ' + valueHtml + '</li>';
                                } else if (true != value && 'true' != value) {
                                    conditionTemp = '<li>' + description + ' ' + valueHtml + '</li>';
                                }
                            }
                        } else if (schemeData[schemeKey]) {
                            conditionTemp = '<li>' + scheme[schemeKey].title + '</li>';
                        }

                        if ('all' == schemeKey || typeof schemeData['all'] == 'undefined' || (typeof schemeData['all'] != 'undefined' && !schemeData['all'])) {
                            dispCondition += conditionTemp;
                        }
                        conditionHtml += '</div>';
                    }

                    // end condition
                    conditionHtml += '</div>';

                    if (dispCondition) {
                        dispCondition = '<ul>' + dispCondition + '</ul>';
                    }
                } else {
                    if ('site' == category) {
                        dispCondition = LayoutBuilderModel.getConditionDescription(category);
                    } else {
                        dispCondition = '<ul><li>' + LayoutBuilderModel.getConditionDescription(category) + '</li></ul>';
                    }
                }

                var isContentEmpty = !LayoutBuilderModel.getOptionControls('content_' + category);
                this.layoutBoxTemplateReplaced = this.layoutBoxTemplate.replace(
                    'class="layout-part content" data-part="content"',
                    'class="layout-part content' + (isContentEmpty ? ' disabled' : '') + '" data-part="content_' + category + '"'
                );

                if (category != 'archive_product') {
                    this.layoutBoxTemplateReplaced = this.layoutBoxTemplateReplaced.replace(
                        'class="layout-part top-sidebar sidebar"', 'class="layout-part top-sidebar sidebar disabled"'
                    );
                }

                return '<div class="wolmart-layout-item wolmart-layout-item-' + category + '" data-category="' + category + '" data-condition-no="' + conditionNo + '">' +

                    // Layout header
                    '<div class="wolmart-condition">' +

                    '<span class="wolmart-condition-edit-back w-icon-long-arrow-' + ($(document.body).hasClass('rtl') ? 'right' : 'left') + '"></span>' +
                    '<span class="wolmart-condition-title" contenteditable="true">' +
                    (layoutData.title ? layoutData.title : LayoutBuilderModel.getConditionTitle(category, true)) +
                    '</span>' +

                    (LayoutBuilderModel.canExtendCondition(category) ? this.buttonDuplicate + this.buttonSet : '') +
                    ('site' == category ? '' : this.buttonDelete) +

                    '</div>' +

                    // Layout body
                    '<div class="wolmart-condition-layout">' + this.layoutBoxTemplateReplaced +
                    '<div class="wolmart-layout-options"><div></div></div>' + conditionHtml +
                    '</div>' +
                    '<div class="wolmart-condition-disp">' + (dispCondition ? dispCondition : ('<ul class="no-condition"><li>' + wolmart_layout_vars.text_no_condition + '</li></ul>')) + (LayoutBuilderModel.canExtendCondition(category) ? '<span class="manage-conditions">' + wolmart_layout_vars.text_manage_conditions + '</span>' : '') + '</div>' +
                    '</div>';
            }
        },

        /**
         * Add a new condition.
         *
         * @since 1.0
         */
        addCondition: function () {
            var category = $('.wolmart-condition-cat.active').data('category') || 'site';
            var addedUI = $(this.getNewConditionUI(category));

            // remove more
            LayoutBuilderModel.canExtendCondition(category) || $('.wolmart-layout-more-wrap[data-category="' + category + '"]').remove();

            // add new
            $('#wolmart_layout_content').append(addedUI).isotope('appended', addedUI);
            this.refreshUI('add');
        },

        /**
         * Event handler to duplicate a condition.
         *
         * @since 1.0
         *
         * @param {Event} e
         */
        duplicateCondition: function (e) {
            var $duplicate = $(e.currentTarget);
            var $item;
            if ($duplicate.is('.wolmart-condition-menu > a')) {
                $item = $duplicate.parent().data('item');
            } else {
                $item = $duplicate.closest('.wolmart-layout-item');
            }

            var category = $item.data('category');
            var categoryNo = LayoutBuilderModel.duplicateCondition(category, $item.data('condition-no'));
            var addedUI = $(this.getNewConditionUI(category, categoryNo));

            // add duplicated
            $('#wolmart_layout_content').append(addedUI).isotope('appended', addedUI).isotope('updateSortData');
            this.refreshUI('add');
        },

        /**
         * Event handler to delete condition.
         *
         * @since 1.0
         */
        deleteCondition: function (e) {
            if (confirm(wolmart_layout_vars.text_confirm_delete_condition)) {
                var $delete = $(e.currentTarget);
                var $item;
                if ($delete.is('.wolmart-condition-menu > a')) {
                    $item = $delete.parent().data('item'); // context menu item
                } else {
                    $item = $delete.closest('.wolmart-layout-item'); // or layout item's button
                }
                var category = $item.data('category');

                if ('site' != category) {
                    // remove
                    LayoutBuilderModel.deleteCondition(category, $item.data('condition-no'));
                    $item.remove();

                    // add more
                    if (!LayoutBuilderModel.canExtendCondition(category)) {
                        var $more = $(this.getAddMoreUI(category));
                        $('#wolmart_layout_content').append($more).isotope('appended', $more);
                    }

                    this.refreshUI('layout');
                }
            }
        },

        /**
         * Event handler to change condition type.
         *
         * @param {Event} e
         */
        changeConditionScheme: function (e) {
            var $check = $(e.currentTarget);
            var $scheme = $check.closest('.wolmart-scheme-options>div');
            var scheme = $scheme.data('scheme');
            var $item = $scheme.closest('.wolmart-layout-item');
            var category = $item.data('category');
            var conditionNo = $item.data('condition-no');
            var isChecked = $check.is(':checked');

            $scheme.toggleClass('disabled', !isChecked);
            LayoutBuilderModel.setConditionScheme($item, category, conditionNo, scheme, isChecked);
            if (isChecked) {
                if ($check.closest('label').siblings('.wolmart-scheme-list').length) {
                    $check.closest('label').siblings('.wolmart-scheme-list').trigger('change', [true]);
                }
            } else if ($scheme.hasClass('wolmart-scheme-all')) {
                $scheme.siblings().find('input').trigger('change');
            }

            // var $type = $(e.currentTarget);
            // var type = $type.val();
            // var $list = $type.next('.wolmart-scheme-list');

            // if (type) {
            //     var schemeKey = LayoutBuilderModel.getScheme(category, type);
            //     var list = schemeKey.list;

            //     if (list) {
            //         var html = '';

            //         for (var item in list) {
            //             html += '<option value="' + item + '">' + list[item] + '</option>';
            //         }
            //         if ($list.length) {
            //             $list.data('select2') && $list.select2('destroy');
            //             $list.html(html).data('placeholder', schemeKey.placeholder);
            //         } else {
            //             $type.after('<select multiple class="wolmart-scheme-list" data-placeholder="' + schemeKey.placeholder + '">' + html + '</select>');
            //         }

            //         this.refreshUI('condition');
            //     } else {
            //         $list.select2('destroy').remove();
            //     }
            // } else if ($list.length) {
            //     $list.select2('destroy').remove();
            // }
        },

        /**
         * Event handler to change condition item.
         * 
         * @since 1.0
         *
         * @param {Event} e 
         */
        changeConditionItem: function (e, manualTrigger) {
            var $list = $(e.currentTarget);
            var $scheme = $list.closest('.wolmart-scheme-options>div');
            var $item = $scheme.closest('.wolmart-layout-item');
            var category = $item.data('category');
            var conditionNo = $item.data('condition-no');
            var scheme = $scheme.data('scheme');
            var list = $list.val();
            var values = {};

            if (list.length) {
                for (var index in list) {
                    values[list[index]] = $list.find('[value=' + list[index] + ']').text();
                }
            }

            if (LayoutBuilderModel.canExtendCondition(category, scheme) && typeof list == 'object') {
                if (!manualTrigger || Object.keys(values).length) {
                    LayoutBuilderModel.setConditionScheme($item, category, conditionNo, scheme, Object.keys(values).length ? values : 0 === category.indexOf('archive_'));
                }
            }
        },

        /**
         * Event handler to change condition title
         * 
         * @since 1.0
         *
         * @param {Event} e 
         */
        changeConditionTitle: function (e) {
            var $title = $(e.currentTarget);
            var $item = $title.closest('.wolmart-layout-item');
            var category = $item.data('category');
            var conditionNo = $item.data('condition-no');
            LayoutBuilderModel.setConditionTitle(category, conditionNo, $title.text());
        },

        /**
         * Event handler to show layout controls for clicked layout part.
         *
         * @since 1.0
         * 
         * @param {Event} e
         */
        editPart: function (e) {
            var $part = $(e.currentTarget);

            // active part
            if ($part.hasClass('disabled')) {
                return;
            }

            var part = $part.data('part');
            var $layout = $part.closest('.wolmart-layout');
            var $options = $layout.next('.wolmart-layout-options').children();
            var controls_html = ''; // '<h4 class="wolmart-layout-control">' + $part.text() + '</h4>';
            var $item = $part.closest('.wolmart-layout-item');
            var currentCategory = $item.data('category');
            var conditionNo = $item.data('condition-no');

            // show layout options for selected layout part.
            var optionControls = LayoutBuilderModel.getOptionControls(part);
            var optionValues = LayoutBuilderModel.getOptionValues(currentCategory, conditionNo);

            if (optionControls) {
                var randomList = [];
                for (var optionName in optionControls) {

                    // get random number.
                    var random;
                    do {
                        random = Math.floor(Math.random() * 65535);
                    } while (randomList.indexOf(random) >= 0);
                    randomList.push(random);

                    var name = '_wolmart_' + part + '_' + optionName + random;
                    var control = optionControls[optionName];
                    var control_html = '';
                    var optionValue = optionValues && 'undefined' != typeof optionValues[optionName] ? optionValues[optionName] : '';

                    // show label, description
                    if (control.description) {
                        control_html += '<div class="wolmart-layout-desc"><label>' + control.label + '</label><p>' + control.description + '</p></div>';
                    } else {
                        control_html += '<label for="' + name + '" class="wolmart-layout-desc">' + control.label + '</label>';
                    }

                    // show control
                    if ('buttonset' == control.type) {

                        var choice = '';

                        control_html += '<input type="radio" id="' + name + '_' + choice + '" name="' + name + '" value=""' + ('' == optionValue ? ' checked' : '') + ' class="radio-default">';
                        control_html += '<label for="' + name + '_' + choice + '" class="label-default w-icon-redo"' + (optionValue ? '' : ' checked="true"') + '></label>';
                        control_html += '<div class="wolmart-radio-button-set">';
                        for (var choice in control.options) {
                            control_html += '<input type="radio" id="' + name + '_' + choice + '" name="' + name + '" value="' + choice + '"' + (choice == optionValue ? ' checked' : '') + '>'; // check checked
                            control_html += '<label for="' + name + '_' + choice + '" class="wolmart_' + part + '_' + optionName + '_' + choice + '">' + control.options[choice] + '</label>';
                            // control_html += '<img src="' + wolmart_layout_vars.layout_images_url + control.options[choice].image + '" title="' + control.options[choice].title + '">';
                        }
                        control_html += '</div>';

                    } else if ('image' == control.type) {
                        var choice = '';
                        control_html += '<input type="radio" id="' + name + '_' + choice + '" name="' + name + '" value=""' + ('' == optionValue ? ' checked' : '') + ' class="radio-default">';
                        control_html += '<label for="' + name + '_' + choice + '" class="label-default w-icon-redo"' + (optionValue ? '' : ' checked="true"') + '></label>';
                        control_html += '<div class="wolmart-radio-image-set">';
                        for (var choice in control.options) {
                            control_html += '<input type="radio" id="' + name + '_' + choice + '" name="' + name + '" value="' + choice + '"' + (choice == optionValue ? ' checked' : '') + '>'; // check checked
                            control_html += '<label for="' + name + '_' + choice + '" class="wolmart_' + part + '_' + optionName + '_' + choice + '">';
                            control_html += '<img src="' + wolmart_layout_vars.layout_images_url + control.options[choice].image + '" title="' + control.options[choice].title + '">';
                            control_html += '</label>';
                        }
                        control_html += '</div>';

                    } else if (control.type.startsWith('block')) {

                        var blocks = LayoutBuilderModel.getTemplates(control.type.replace('block_', ''));

                        control_html += '<div class="wolmart-block-select' + (optionValue && optionValue != 'hide' ? '' : ' inactive-my') + '">';

                        control_html += '<div class="wolmart-radio-button-set">';
                        control_html += '<input type="radio" name="' + name + '" id="' + name + '_" value=""' + (optionValue ? '' : ' checked') + '>';
                        control_html += '<label class="w-icon-redo" for="' + name + '_" title="' + wolmart_layout_vars.text_default + '"></label>';
                        control_html += '<input type="radio" name="' + name + '" id="' + name + '_hide" value="hide"' + (optionValue == 'hide' ? ' checked' : '') + '>';
                        control_html += '<label class="w-icon-eye-slash"for="' + name + '_hide"  title="' + wolmart_layout_vars.text_hide + '"></label>';
                        control_html += '<input type="radio" name="' + name + '" id="' + name + '_my" value="my"' + (optionValue && optionValue != 'hide' ? ' checked' : '') + '>';
                        control_html += '<label class="w-icon-layer-group" for="' + name + '_my" title="' + wolmart_layout_vars.text_my_templates + '"></label>';
                        control_html += '</div>';
                        // control_html += '<div class="wolmart-radio-button-extend">';
                        // control_html += '<a href="#" class="wolmart-add-new-template w-icon-plus-solid"></a>';
                        // control_html += '<a href="#" class="w-icon-edit"></a>';
                        // control_html += '</div>';

                        if (!optionValue) {
                            if (-1 !== location.search.indexOf('is_elementor_preview')) {
                                var params = window.location.search.substring(1).split('&'),
                                    requests = {};

                                params.forEach(function (item) {
                                    let tempArr = item.split('=');
                                    requests[tempArr[0]] = tempArr[1];
                                });

                                if (typeof requests['post'] != 'undefined') {
                                    optionValue = requests['post'];
                                }
                            }
                        }

                        control_html += '<select class="wolmart-layout-part-select wolmart-layout-part-control" id="' + name + '" name="' + name + '">';
                        for (var block in blocks) {
                            control_html += '<option value="' + block + '"' + (optionValue == block ? ' selected' : '') + '>' + blocks[block] + '</option>';
                        }
                        control_html += '</select>';

                        control_html += '</div>';

                    } else if ('number' == control.type) {

                        if (optionValue) { // check min, max validation.
                            var value = optionValue;
                            'undefined' != typeof control.min && (value = Math.max(control.min, value));
                            'undefined' != typeof control.max && (value = Math.min(control.max, value));
                            optionValue != value && LayoutBuilderModel.setConditionOption(currentCategory, conditionNo, optionName, value);
                        }

                        control_html += '<input type="number" class="wolmart-layout-part-control" name="' + name + '" id="' + name + '"' +
                            ('undefined' == typeof control.min ? '' : ' min="' + control.min + '"') +
                            ('undefined' == typeof control.max ? '' : ' max="' + control.max + '"') +
                            ' step="1" value="' + optionValue + '">';

                    } else if ('select' == control.type) {

                        control_html += '<select class="wolmart-layout-part-select wolmart-layout-part-control" id="' + name + '" name="' + name + '">';
                        for (var choice in control.options) {
                            control_html += '<option value="' + choice + '"' + (choice == optionValue ? ' selected' : '') + '>' + control.options[choice] + '</option>';
                        }
                        control_html += '</select>';

                    } else if ('text' == control.type) {

                        control_html += '<input type="text" name="' + name + '" class="wolmart-layout-part-input wolmart-layout-part-control" id="' + name + '" value="' + optionValue + '"></input>';

                    } else if ('toggle' == control.type) {

                        control_html += '<div class="wolmart-radio-button-set">';
                        control_html += '<input type="radio" name="' + name + '" id="' + name + '_" value=""' + (optionValue ? '' : ' checked') + '>';
                        control_html += '<label class="w-icon-redo" for="' + name + '_" title="' + wolmart_layout_vars.text_default + '"></label>';
                        control_html += '<input type="radio" name="' + name + '" id="' + name + '_no" value="no"' + (optionValue == 'no' ? ' checked' : '') + '>';
                        control_html += '<label class="w-icon-eye-slash" for="' + name + '_no"></label>';
                        control_html += '<input type="radio" name="' + name + '" id="' + name + '_yes" value="yes"' + (optionValue == 'yes' ? ' checked' : '') + '>';
                        control_html += '<label class="w-icon-check2"for="' + name + '_yes"></label>';
                        control_html += '</div>';

                    } else if ('multicheck' == control.type) {
                        control_html += '<ul>';
                        if (control.options) {
                            for (var choice in control.options) {
                                control_html += '<li><label><input type="checkbox" name="' + name + '[]" value="' + choice + '">' + control.options[choice] + '</label></li>';
                            }
                        }
                        control_html += '</ul>';
                    }

                    // custom controls condition
                    var show = true;
                    if (optionName == 'single_product_template' && (!optionValues || 'builder' != optionValues['single_product_type'])) {
                        show = false;
                    }

                    controls_html += '<div class="wolmart-layout-control" ' + (show ? '' : ' style="display:none"') + 'data-option="' + optionName + '">' + control_html + '</div>';
                }
            }

            $options.html(controls_html);

            // Show controls UI.
            $item.addClass('edit');
        },

        /**
         * Event handler to edit condition.
         * @param {Event} e
         */
        editCondition: function (e) {
            var $link = $(e.currentTarget);
            var $item;
            if ($link.is('.wolmart-condition-menu > a')) {
                $item = $link.parent().data('item');
            } else {
                $item = $link.closest('.wolmart-layout-item');
            }

            if ($item.hasClass('edit-condition')) {
                setTimeout(function () {
                    $item.find('.wolmart-scheme-list').each(function () {
                        var $this = $(this);
                        if ($this.data('select2')) {
                            if (!$this.hasClass('ajaxselect2')) {
                                $this.select2('destroy');
                            }
                        }
                    });
                }, 300);

                $(window).trigger('wolmart_condition_edited');
            } else {
                $item.find('.wolmart-scheme-list:not(.select2-hidden-accessible)').each(function () {
                    var $this = $(this); var $this = $(this);
                    if ($this.hasClass('ajaxselect2')) {
                        var option = $this.data('load-option'),
                            values = $this.data('values'),
                            path = wolmart_layout_vars.site_url + '/wp-json/ajaxselect2/v1/' + option + '/';

                        $this.select2({
                            placeholder: $this.attr('data-placeholder'),
                            ajax: {
                                url: path,
                                dataType: 'json',
                                data: function (params) {
                                    var query = {
                                        s: params.term,
                                    }
                                    return query;
                                }
                            },
                            cache: true
                        });

                        $.ajax({
                            url: path,
                            dataType: 'json',
                            data: {
                                ids: values ? values : ''
                            }
                        }).then(function (ret) {
                            if (ret !== null && ret.results.length > 0) {
                                jQuery.each(ret.results, function (i, v) {
                                    var op = new Option(v.text, v.id, true, true);
                                    $this.append(op);
                                });
                                $this.trigger({
                                    type: 'select2:select',
                                    params: {
                                        data: ret
                                    }
                                });
                            }
                        });

                    } else {
                        $this.select2({
                            // dropdownParent: $this.parent(),
                            placeholder: $this.attr('data-placeholder'),
                        })
                    }
                });
            }
            $item.toggleClass('edit-condition');
        },

        /**
         * 
         * @param {Event} e 
         */
        clickOther: function (e) {
            var $target = $(e.target);

            if (!$target.closest('.select2-container').length &&
                !$target.closest('.wolmart-layout-item').length &&
                !$target.closest('.wolmart-condition-set').length &&
                $target.is('body *')) {

                $('.wolmart-layout-item.edit').removeClass('edit');
                $('.wolmart-layout-item.edit-condition').removeClass('edit-condition');
                setTimeout(function () {
                    $('.wolmart-layout-item .wolmart-scheme-list').each(function () {
                        var $this = $(this);
                        if ($this.data('select2')) {
                            if (!$this.hasClass('ajaxselect2')) {
                                $this.select2('destroy');
                            }
                        }
                    });
                }, 300);

                $(window).trigger('wolmart_condition_edited');
            }

            this.closeContextMenu();
        },

        /**
         * Event handler to go back from editing condition.
         * @param {Event} e 
         */
        goBackFromEdit: function (e) {
            var $options = $(e.currentTarget).parent('.wolmart-scheme-options');
            if ($options.length) {
                $options.hide();
                setTimeout(function () {
                    $options.show();
                }, 100);

            } else {
                $(e.currentTarget).closest('.wolmart-layout-item').removeClass('edit edit-condition');
                $(window).trigger('wolmart_condition_edited');
            }
        },

        /**
         * Event handler to change value for block select control.
         *
         * @since 1.0
         */
        changeBlockMode: function (e) {
            var target = e.currentTarget;
            var $target = $(target);
            var $item = $target.closest('.wolmart-layout-item');
            $target.closest('.wolmart-block-select').toggleClass('inactive-my', 'my' != target.value);

            target.name.startsWith('_wolmart_') &&
                LayoutBuilderModel.setConditionOption(
                    $item.data('category'),
                    $item.data('condition-no'),
                    $target.closest('.wolmart-layout-control').data('option'),
                    target.value
                );

            this.refreshLayoutStatus($item);
        },

        /**
         * Event handler to change layout option.
         *
         * @since 1.0
         */
        changeOptionInput: function (e) {
            var $input = $(e.currentTarget);
            var value = $input.val();
            var $block = $input.closest('.wolmart-block-select');
            var name = e.currentTarget.name;

            if ($block.length && e.currentTarget.value == 'my') {
                value = $block.find('select').val();
            }
            if (name.startsWith('_wolmart_')) {
                LayoutBuilderModel.setConditionOption(
                    $input.closest('.wolmart-layout-item').data('category'),
                    $input.closest('.wolmart-layout-item').data('condition-no'),
                    $input.closest('.wolmart-layout-control').data('option'),
                    value
                );

                // Custom control conditions
                if (name.indexOf('single_product_type') >= 0) {
                    var $template = $input.closest('.wolmart-layout-options').find('.wolmart-layout-control[data-option="single_product_template"]');
                    $template.length && $template.toggle(value == 'builder');
                }
            }

            this.refreshLayoutStatus($(e.currentTarget).closest('.wolmart-layout-item'));
        },
    }

    /**
     * Layout Builder Class
     * 
     * @since 1.0
     */
    var LayoutBuilder = {
        init: function () {
            if ($('#wolmart_layout_content').length && 'undefined' != typeof wolmart_layout_vars) {
                this.model.init();
                this.view.init();
                $(window).on('wolmart_condition_edited', this.refreshCondition);
            }
        },
        refreshCondition: function () {
            if ($('.wolmart-layout-item.edited').length) {
                $('.wolmart-layout-item.edited').each(function () {
                    var $item = $(this),
                        $condition_disp = $item.find('.wolmart-condition-disp'),
                        category = $item.attr('data-category'),
                        conditionNo = $item.attr('data-condition-no'),
                        scheme = LayoutBuilderModel.getScheme(category),
                        schemeData = LayoutBuilderModel.getConditions(category, conditionNo),
                        isSingle = 0 === category.indexOf('single_'),
                        isArchive = 0 === category.indexOf('archive_'),
                        dispCondition = '';

                    if (typeof schemeData.scheme != 'undefined') {

                        schemeData = schemeData.scheme;

                        for (var schemeKey in schemeData) {

                            var disabled = false,
                                conditionTemp = '';

                            if (typeof schemeData.all != 'undefined' && schemeData.all && schemeKey != 'all') {
                                disabled = true;
                            }

                            var list = scheme[schemeKey].list;

                            var description = scheme[schemeKey].title;
                            if (typeof scheme[schemeKey].description != 'undefined') {
                                description = scheme[schemeKey].description;
                            } else if (isSingle) {
                                description = scheme[schemeKey].title + ' ' + wolmart_layout_vars.text_single_prefix;
                            } else if (isArchive) {
                                description = wolmart_layout_vars.text_archive_prefix;
                            }

                            var isObject = typeof schemeData[schemeKey] == 'object' && 'undefined' == typeof schemeData[schemeKey][0];

                            if (list) {
                                if (!disabled) {
                                    var selectedItems = '';
                                    if (typeof schemeData[schemeKey] == 'object') {
                                        for (var item in schemeData[schemeKey]) {
                                            var itemHtml = isObject ? schemeData[schemeKey][item] : list[schemeData[schemeKey][item]];
                                            selectedItems += selectedItems ? (', ' + itemHtml) : itemHtml;
                                        }
                                    }
                                    if (selectedItems) {
                                        if (isArchive) {
                                            conditionTemp = '<li>' + description + ' ' + scheme[schemeKey].title + ' ' + wolmart_layout_vars.text_single_prefix + ' ' + selectedItems + '</li>';
                                        } else {
                                            conditionTemp = '<li>' + description + ' ' + selectedItems + '</li>';
                                        }
                                    } else if (isArchive) {
                                        conditionTemp = '<li>' + description + ' ' + scheme[schemeKey].title + '</li>';
                                    }
                                }
                            } else if (scheme[schemeKey].ajaxselect) {
                                var value = schemeData[schemeKey];

                                if (Array.isArray(value)) {
                                    value = schemeData[schemeKey].join(', ');
                                } else if ('object' == typeof value) {
                                    value = Object.values(value).join(', ');
                                }

                                if (value) {
                                    if (!isSingle) {
                                        conditionTemp = '<li>' + description + scheme[schemeKey].title + ' ' + wolmart_layout_vars.text_single_prefix + ' ' + value + '</li>';
                                    } else if (true != value && 'true' != value) {
                                        conditionTemp = '<li>' + description + ' ' + value + '</li>';
                                    }
                                }
                            } else if (schemeData[schemeKey]) {
                                conditionTemp = '<li>' + scheme[schemeKey].title + '</li>';
                            }

                            if ('all' == schemeKey || typeof schemeData['all'] == 'undefined' || (typeof schemeData['all'] != 'undefined' && !schemeData['all'])) {
                                dispCondition += conditionTemp;
                            }
                        }

                        if (dispCondition) {
                            dispCondition = '<ul>' + dispCondition + '</ul>';
                        }

                        $condition_disp.html((dispCondition ? dispCondition : ('<ul class="no-condition"><li>' + wolmart_layout_vars.text_no_condition + '</li></ul>')) + (LayoutBuilderModel.canExtendCondition(category) ? '<span class="manage-conditions">' + wolmart_layout_vars.text_manage_conditions + '</span>' : ''));
                    }
                })

                $('#wolmart_layout_content').isotope('layout');
            }
        },
        view: LayoutBuilderView,
        model: LayoutBuilderModel
    };

    /**
     * Setup Layout Builder
     */
    WolmartAdmin.LayoutBuilder = LayoutBuilder;
    $(document).ready(function () {
        LayoutBuilder.init();

        // Add class for layout builder wrap in elementor preview
        if (location.href.indexOf('noheader') != -1) {
            $(document.body).addClass('alpha-admin-page').parent().addClass('alpha-studio-popup');
        }
    });
})(jQuery);