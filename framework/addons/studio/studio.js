/**
 * Wolmart Studio Library
 */
'use strict';

/**
 * In visual composer, loading start template is earlier than initialization of vc editor
 * for low level servers. So to delay this loading, increase this.
 */
window.wolmart_vc_studio_delay = 1500;

(function ($) {
	var $addStudioSection = false; // Add studio block section when add is triggered
	$(document).ready(function () {
		if ($(document.body).hasClass('elementor-editor-active') && typeof elementor != 'undefined') {
			// Wolmart Elementor Studio
			window.runStudio = function (addButton) {
				$('#wolmart-elementor-panel-wolmart-studio').trigger('click');
				addButton && ($addStudioSection = $(addButton).closest('.elementor-add-section'));
			}
			elementor.on('document:loaded', setupStudioBlocks);
		} else if ($(document.body).hasClass('vcv-wb-editor')) {
			// Wolmart Visual Composer Studio
			$(window).on('init_wolmart_settings', function () {
				setTimeout(setupStudioBlocks, window.wolmart_vc_studio_delay);
			});
		} else {
			// New Template Studio
			$(window).load(setupStudioBlocks);
		}
	});

	function setupStudioBlocks() {

		var filter_status = '',
			isRunning = false,
			page_type = 'e';

		$(document.body).find('#wpb_visual_composer').length > 0 && (page_type = 'w');
		setTimeout(function () {
			$(document.body).find('#vc_navbar').length > 0 && (page_type = 'w');
		}, 1000);
		$(document.body).hasClass('vc_inline-shortcode-edit-form') && (page_type = 'w');
		var wolmart_blocks_cur_page = 1;

		function wpbMergeContent(response, block_id) {
			if (response && response.content) {
				if (typeof vc != 'undefined' && vc.storage) { // WPBakery backend editor
					vc.storage.append(response.content);
					vc.shortcodes.fetch({
						reset: !0
					}), _.delay(function () {
						window.vc.undoRedoApi.unlock();
					}, 50);
				} else if (window.vc_iframe_src) { // WPBakery frontend editor
					var render_data = { action: 'vc_frontend_load_template', block_id: block_id, content: response.content, wpnonce: wolmart_studio.wpnonce, template_unique_id: '1', template_type: 'my_templates', vc_inline: true, _vcnonce: window.vcAdminNonce };
					if (response.meta) {
						render_data.meta = response.meta;
					}
					$.ajax({
						url: window.vc_iframe_src.replace(/&amp;/g, '&'),
						type: 'post',
						data: render_data,
						success: function (html) {
							var template, data;
							_.each($(html), function (element) {
								if ('vc_template-data' === element.id) {
									try {
										data = JSON.parse(element.innerHTML);
									} catch (err) { }
								}
								if ('vc_template-html' === element.id) {
									template = element.innerHTML;
								}
							});
							if (template && data) {
								vc.builder.buildFromTemplate(template, data);
								vc.closeActivePanel();
							}
						},
					});
				}
			}

			if (response && response.meta) {
				if (response.meta.page_css && $(".postbox-container #wpb_visual_composer").length > 0) {
					$('#vc_post-custom-css').val($('#vc_post-custom-css').val() + response.meta.page_css);
					$('#vc_ui-panel-post-settings').css('display', 'none');
					$('#vc_post-settings-button').trigger('click');
					$('#vc_ui-panel-post-settings .vc_ui-panel-footer .vc_ui-button-fw').trigger('click');
					$('#vc_ui-panel-post-settings').css('display', '');
				}
				if (response.meta.page_js && $("#page_js").length > 0) {
					$("#page_js").val($("#page_js").val() + response.meta.page_js);
				}
				if (window.vc_iframe_src) {
					if (typeof wolmart_studio['meta_fields'] == 'undefined') {
						wolmart_studio['meta_fields'] = {};
					}
					if (response.meta.page_css) {
						$('#vc_post-custom-css').val($('#vc_post-custom-css').val() + response.meta.page_css);
						$('#vc_ui-panel-post-settings').css('display', 'none');
						$('#vc_post-settings-button').trigger('click');
						$('#vc_ui-panel-post-settings .vc_ui-panel-footer .vc_ui-button-fw').trigger('click');
						$('#vc_ui-panel-post-settings').css('display', '');
					}
					if (response.meta.page_js) {

						if (typeof wolmart_studio['meta_fields']['page_js'] == 'undefined')
							wolmart_studio['meta_fields']['page_js'] = '';
						if (wolmart_studio['meta_fields']['page_js'].indexOf(response.meta.page_js) === -1)
							wolmart_studio['meta_fields']['page_js'] += response.meta.page_js;
					}
				}
			}
			if (response && response.error) {
				alert(response.error);
			}

		}
		function mergeContent(response) {
			if (response) {
				if (response.content) {
					var addID = function (content) {
						Array.isArray(content) &&
							content.forEach(function (item, i) {
								item.elements && addID(item.elements);
								item.elType && (content[i].id = elementorCommon.helpers.getUniqueId());
							});
					};

					if (Array.isArray(response.content)) {
						var isAllWidgets = true;
						response.content.forEach(function (element) {
							if (element.elType != 'widget') {
								isAllWidgets = false;
								return false;
							}
						});
						if (isAllWidgets) {
							response.content = [{
								elType: 'section',
								elements: [{
									elType: 'column',
									elements: response.content
								}]
							}];
						} else {
							response.content.forEach(function (element, i) {
								if ('widget' == element.elType) {
									response.content[i] = {
										elType: 'section',
										elements: [{
											elType: 'column',
											elements: element
										}]
									};
								} else if ('column' == element.elType) {
									response.content[i] = {
										elType: 'section',
										elements: element
									};
								}
							})
						}
					}

					addID(response.content);

					// import studio block to end or add-section
					elementor.getPreviewView().addChildModel(response.content,
						$addStudioSection && $addStudioSection.parent().hasClass('elementor-section-wrap') ? (
							$addStudioSection.find('.elementor-add-section-close').trigger('click'), {
								at: $addStudioSection.index()
							}) : {}
					);

					// active save button or save elementor
					if (elementor.saver && elementor.saver.footerSaver && elementor.saver.footerSaver.activateSaveButtons) {
						elementor.saver.footerSaver.activateSaveButtons(document, 'publish');
					} else {
						$e.run('document/save/publish');
					}
				}
				if (response.meta) {
					for (var key in response.meta) {
						var value = response.meta[key].replace('/<script.*?\/script>/s', ''),
							key_data = elementor.settings.page.model.get(key);
						if (typeof key_data == 'undefined') {
							key_data = '';
						}
						if (!key_data || key_data.indexOf(value) === -1) {
							elementor.settings.page.model.set(key, key_data + value);
						}
						if ('page_css' == key) {
							elementorFrontend.hooks.doAction('refresh_page_css', key_data + value);
							$('textarea[data-setting="page_css"]').val(key_data + value);
						}
					}
				}
				if (response.error) {
					alert(response.error);
				}
			}
		}

		function showBlocks(e, cur_page, demo_filter) {
			e.preventDefault();

			// if still loading
			if ($('.blocks-wrapper').hasClass('loading')) {
				return false;
			}

			var $this = $(this);

			// if toggle is clicked
			if (e.target.tagName == 'I') { // Toggle children
				$this.siblings('ul').stop().slideToggle(200);
				$this.children('i').toggleClass('w-icon-chevron-down w-icon-chevron-up');
				return false;
			}

			// if active category is clicked
			if ($this.hasClass('active') && !$this.parent().hasClass('filtered') && (typeof cur_page == 'undefined' || cur_page == 1)) {
				return false;
			}

			var $list = $('.blocks-wrapper .blocks-list'),
				$categories = $('.blocks-wrapper .block-categories');

			// if top category is clicked
			if (typeof $this.data('filter-by') == 'undefined' && !$this.parent('.filtered').length) {
				if ($this.hasClass('all')) { // Show all categories
					$categories.removeClass('hide');
					$list.siblings('.coming-soon').remove();

				} else { // Show empty category
					$categories.addClass('hide');
					$list.isotope('remove', $list.children()).css('height', '');
					$list.siblings('.coming-soon').length || $list.before('<div class="coming-soon">' + wolmart_studio.texts.coming_soon + '</div>');
				}
				$('.blocks-wrapper .category-list a').removeClass('active');
				$this.addClass('active');
			} else {
				wolmart_blocks_cur_page = typeof cur_page == 'undefined' ? 1 : parseInt(cur_page, 10);

				if (wolmart_blocks_cur_page > 1) {
					if (!$categories.hasClass('hide')) {
						return;
					}
					$('.blocks-wrapper').addClass('infiniteloading');
				}

				if (!$categories.hasClass('hide')) {
					$list.isotope('remove', $list.children());
					$categories.addClass('hide');
				}

				$list.siblings('.coming-soon').remove();

				var cat = $this.data('filter-by'),
					loaddata = {
						action: 'wolmart_studio_filter_category',
						category_id: cat,
						wpnonce: wolmart_studio.wpnonce,
						page: wolmart_blocks_cur_page,
						type: page_type
					};

				if (typeof demo_filter != 'undefined') {
					loaddata.demo_filter = demo_filter;
				}
				if (!$(document.body).hasClass('elementor-editor-active') && !$(document.body).hasClass('vcv-wb-editor') && !($(document.body).hasClass('vc_inline-shortcode-edit-form') || $(document.body).find('#wpb_visual_composer').length > 0 || $(document.body).find('#vc_navbar').length > 0)) {
					loaddata.new_template = true;
				}
				if ($('.blocks-wrapper .block-category-favourites.active').length && wolmart_blocks_cur_page > 1) {
					loaddata.current_count = $list.data('isotope').items.length;
				}
				$('.blocks-wrapper').addClass('loading');

				$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'html',
					data: loaddata,
					success: function (response) {
						if ('error' == response) {
							$('.blocks-wrapper').removeClass('loading').removeClass('infiniteloading');
							return;
						}

						var $response = $(response);

						// demo filter
						if (typeof demo_filter != 'undefined') {
							var total_page = $response.filter('#total_pages').text();
							$this.data('total-page', total_page ? parseInt(total_page, 10) : 1);
							$response = $response.filter('.block');
						} else {
							$('.blocks-wrapper .btn').prop('disabled', false);
						}

						// first page
						if (wolmart_blocks_cur_page === 1) {
							$list.isotope('remove', $list.children());
						}

						// make category active
						$('.blocks-wrapper .category-list a').removeClass('active');
						$this.addClass('active');

						// layout
						$response.imagesLoaded(function () {
							$list.append($response).isotope('appended', $response).isotope('layout');
							$('.blocks-wrapper').removeClass('loading').removeClass('infiniteloading');
							$('.blocks-wrapper .blocks-section').trigger('scroll');
						});
					}
				}).fail(function () {
					alert(wolmart_studio.texts.loading_failed);
					$('.blocks-wrapper').removeClass('loading').removeClass('infiniteloading');
				});
			}
		}

		function importBlock(block_id, callback, $obj) {
			var jqxhr = $.ajax({
				url: ajaxurl,
				type: 'post',
				dataType: 'json',
				data: {
					action: 'wolmart_studio_import',
					block_id: block_id,
					wpnonce: wolmart_studio.wpnonce,
					type: page_type,
					mine: 'my' == $('.blocks-wrapper .category-list a.active').data('filter-by')
				},
				success: function (response) {
					if (page_type == 'e') {
						mergeContent(response);
					} else if (page_type == 'v') {
						window.wolmartVCMergeContent(response)
					} else {
						wpbMergeContent(response, block_id);
					}
					$obj && $obj.addClass('imported');
				},
				failure: function () {
					alert(wolmart_studio.texts.importing_error);
				}
			});
			callback && jqxhr.always(callback);
		}

		function importBlockHandler(e) {
			e.preventDefault();
			var $this = $(this),
				$block = $this.closest('.block');
			$this.attr('disabled', 'disabled');
			$block.addClass('doing');

			importBlock($this.parent().data('id'), function () {
				$this.prop('disabled', false);
				$block.removeClass('doing');
			}, $block);
		}

		function selectBlock() {
			var $this = $(this),
				category = $(this).parent().data('category');

			if (parseInt(category)) {
				if (category == 36) {
					$('.wolmart-new-template-form .template-type').val('header');
				} else if (category == 37) {
					$('.wolmart-new-template-form .template-type').val('footer');
				} else if (category == 10) {
					$('.wolmart-new-template-form .template-type').val('product_layout');
				} else if (category == 11) {
					$('.wolmart-new-template-form .template-type').val('popup');
				} else {
					$('.wolmart-new-template-form .template-type').val('block');
				}
			} else {
				$('.wolmart-new-template-form .template-type').val(category);
			}

			$('.blocks-wrapper .block.selected').removeClass('selected');
			$('#wolmart-new-template-id').val($this.parent().data('id'));
			if ($('.blocks-wrapper .block-category-my-templates.active').length)
				$('#wolmart-new-template-type').val('my');
			else {
				if ($('#wolmart-elementor-studio').is(':checked'))
					$('#wolmart-new-template-type').val('e');
				else if ($('#wolmart-wpbakery-studio').is(':checked'))
					$('#wolmart-new-template-type').val('w');
				else
					$('#wolmart-new-template-type').val('v');
			}
			$('#wolmart-new-template-name').val($this.closest('.block').addClass('selected').find('.block-title').text());
			closeStudio();
		}

		function favourBlock() {
			var $this = $(this),
				$block = $this.closest('.block').addClass('doing'),
				$list = $('.blocks-wrapper .blocks-list'),
				$count = $('.blocks-wrapper .block-category-favourites span'),
				favourdata = {
					action: 'wolmart_studio_favour_block',
					wpnonce: wolmart_studio.wpnonce,
					block_id: $this.parent().data('id'),
					type: page_type,
					active: $block.hasClass('favour') ? 0 : 1,
				};

			if ($('.blocks-wrapper .block-category-favourites.active').length) {
				favourdata.current_count = $list.data('isotope').items.length;
			}

			$.post(ajaxurl, favourdata, function (response) {
				$block.toggleClass('favour');

				var count = (parseInt($count.text().replace('(', '').replace(')', '')) + ($block.hasClass('favour') ? 1 : -1));
				$count.text('(' + count + ')').parent().data('total-page', Math.ceil(count / wolmart_studio.limit));

				if (typeof favourdata.current_count != 'undefined') {
					var $response = $(response);

					$list.isotope('remove', $block);
					if (response && response.trim()) {
						$list.append($response).isotope('appended', $response);
					}
					$list.isotope('layout');
					wolmart_blocks_cur_page = Math.ceil(favourdata.current_count / wolmart_studio.limit);
				}

			}).always(function () {
				$block.removeClass('doing');
			});
		}

		function saveMetaField(e) {
			if ($('.postbox-container #wpb_visual_composer').length == 0 && wolmart_studio['meta_fields'] && vc_post_id) {
				$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'json',
					data: { action: 'wolmart_studio_save', post_id: vc_post_id, nonce: wolmart_studio.wpnonce, fields: wolmart_studio['meta_fields'] }
				});
			}
		}
		function resetSelected() {
			$('.blocks-wrapper .block.selected').removeClass('selected');
			$('#wolmart-new-template-id').val('');
			$('#wolmart-new-template-type').val('');
			$('#wolmart-new-template-name').val('');
		}

		function changeFilter() {
			if (filter_status != $(this).val()) {
				$('.demo-filter .btn').prop('disabled', false);
			} else {
				$('.demo-filter .btn').attr('disabled', 'disabled');
			}
		}

		function doFilter(e, cur_page) {
			e.preventDefault();
			var $this = $(this),
				prefix = '',
				filter = [];
			if (typeof cur_page == 'undefined') {
				cur_page = 1;
			}
			filter_status = $this.closest('.demo-filter').find('.filter-select').val();

			if ($this.closest('.demo-filter').find('.filter-select').val()) {
				prefix = '';
				if (page_type == 'v')
					prefix = 'vc-';
				else if (page_type == 'w')
					prefix = 'wpb-';
				filter[0] = prefix + $this.closest('.demo-filter').find('.filter-select').val();
			}

			if (filter.length) {
				$('.blocks-wrapper .filtered>a').trigger('click', [cur_page, filter]);
			} else {
				$('.blocks-wrapper .all').trigger('click');
			}
			$this.attr('disabled', 'disabled');
		}

		function openCategory(e) {
			if (this.getAttribute('data-category')) {
				$('.blocks-wrapper .block-category-' + this.getAttribute('data-category')).trigger('click');
			}
			e.preventDefault();
		}

		function closeStudio() {
			$('.blocks-wrapper, .blocks-overlay, .layout-builder').addClass('closed');
		}

		function changeColumn() {
			var $this = $(this);
			var column = $this.addClass('active').data('column');
			$this.siblings().removeClass('active');
			$('.blocks-wrapper .blocks-list')
				.removeClass('column-2 column-3 column-4')
				.addClass(parseInt(column) > 1 ? 'column-' + column : '')
				.isotope('layout');
		}

		function openStudio(e) {

			e.preventDefault();

			if (!isRunning) {
				$('#wolmart_studio_blocks_wrapper_template').after($('#wolmart_studio_blocks_wrapper_template').text()).remove();

				if ($('.blocks-wrapper .blocks-list').length) {
					$(document.body)
						.on('click', '.blocks-wrapper .category-list a', showBlocks)
						.on('click', '.blocks-wrapper .blocks-list .import', importBlockHandler)
						.on('click', '.blocks-wrapper .blocks-list .select', selectBlock)
						.on('click', '.blocks-wrapper .blocks-list .favourite', favourBlock)
						.on('click', '.blocks-wrapper .mfp-close, .layout-builder .mfp-close, .blocks-overlay', closeStudio)
						.on('click', '.blocks-wrapper .block-category', openCategory)
						.on('click', '#vc_button-update', saveMetaField)
						.on('change', '.blocks-wrapper .demo-filter .filter-select', changeFilter)
						.on('click', '.blocks-wrapper .demo-filter .btn', doFilter)
						.on('change', '#wolmart-elementor-studio', resetSelected)
						.on('change', '#wolmart-wpbakery-studio', resetSelected)
						.on('click', '.blocks-wrapper .layout-buttons > button', changeColumn);
				}


				if ($(this).hasClass('disabled')) {
					return false;
				}

				$(this).addClass('disabled');

				$('.blocks-wrapper img[data-original]').each(function () {
					$(this).attr('src', $(this).data('original'));
					$(this).removeAttr('data-original');
				});

				$('.blocks-wrapper').imagesLoaded(function () {
					$('#wolmart-elementor-panel-wolmart-studio, #vce-wolmart-studio-trigger, #wpb-wolmart-studio-trigger, #wolmart-new-studio-trigger').removeClass('disabled');
					$('.blocks-wrapper, .blocks-overlay').removeClass('closed');
					setTimeout(function () {
						if (!$('.blocks-wrapper .blocks-list').hasClass('initialized')) {
							$('.blocks-wrapper .blocks-list').addClass('initialized').isotope({
								itemSelector: '.block',
								layoutMode: 'masonry'
							});

							$('.blocks-wrapper .blocks-section').on('scroll', function () {
								var $this = $(this),
									$wrapper = $this.closest('.blocks-wrapper');
								if ($wrapper.length) {
									var top = $this.children().offset().top + $this.children().height() - $this.offset().top - $this.height();

									if (top <= 10 && !$wrapper.hasClass('loading') && parseInt($wrapper.find('.category-list a.active').data('total-page'), 10) >= wolmart_blocks_cur_page + 1) {
										var filterBy = $wrapper.find('.category-list a.active').data('filter-by');
										if (parseInt(filterBy, 10) || 'blocks' == filterBy || '*' == filterBy || 'my' == filterBy) {
											$wrapper.find('.category-list a.active').trigger('click', [wolmart_blocks_cur_page + 1]);
										} else if ('all' != filterBy) {
											$wrapper.find('.demo-filter .btn').trigger('click', [wolmart_blocks_cur_page + 1]);
										}
									}
								}
							});

							$('.blocks-wrapper .blocks-section').trigger('scroll');
						}
						$('.blocks-wrapper .blocks-list').isotope('layout');
					}, 100);
				});
			} else {
				$('.blocks-wrapper, .blocks-overlay').removeClass('closed');
			}

			isRunning = true;
		}

		function openDisplayCondition(e) {
			e.preventDefault();
			if (!$('.layout-builder').length) {
				$(document.body).on('click', '.blocks-wrapper .mfp-close, .layout-builder .mfp-close, .blocks-overlay', closeStudio)

				$('#wolmart_studio_blocks_wrapper_template').after($('#wolmart_studio_blocks_wrapper_template').text()).remove();
				$('.layout-builder, .blocks-overlay').removeClass('closed');
			} else {
				$('.layout-builder, .blocks-overlay').removeClass('closed');
			}
		}

		function confirmPageType(e) {
			if ('e' == wolmart_studio.page_type || 'v' == wolmart_studio.page_type || 'w' == wolmart_studio.page_type) {
				page_type = wolmart_studio.page_type;
			} else {
				var new_type = '';
				if ($('#wolmart-elementor-studio').is(':checked'))
					new_type = 'e';
				else if ($('#wolmart-wpbakery-studio').is(':checked'))
					new_type = 'w';
				else
					new_type = 'v';
				if (page_type != new_type) {
					page_type = new_type;

					$('.blocks-wrapper').addClass('loading');

					$.ajax({
						url: ajaxurl,
						type: 'post',
						dataType: 'html',
						data: {
							action: 'wolmart_studio_filter_category',
							wpnonce: wolmart_studio.wpnonce,
							page: 1,
							type: page_type,
							full_wrapper: true,
							new_template: true
						},
						success: function (response) {
							if ('error' != response) {
								var $response = $(response),
									$list = $('blocks-wrapper .blocks-list');

								$list.hasClass('initialized') && $list.isotope('remove', $list.children()).css('height', '');
								$('.blocks-wrapper .block-categories.hide').removeClass('hide');
								$('.blocks-wrapper .category-list').html($response.find('.category-list').html());
								$('.blocks-wrapper .filter-select').html($response.find('.filter-select').html());
							}
							$('.blocks-wrapper').removeClass('loading');
						}
					}).fail(function () {
						alert(wolmart_studio.texts.loading_failed);
						$('.blocks-wrapper').removeClass('loading');
					});
				}
			}
			openStudio.call(this, e);
		}

		function importStartTemplate() {
			wolmart_studio.start_template && importBlock(parseInt(wolmart_studio.start_template));
			if (wolmart_studio.start_template_content) {
				if ('e' == page_type) {
					mergeContent(wolmart_studio.start_template_content);
				} else if ('v' == page_type) {
					wolmartVCMergeContent(wolmart_studio.start_template_content)
				} else {
					wpbMergeContent(wolmart_studio.start_template_content);
				}
			}
		}

		$(document.body)
			.on('click', '#wolmart-elementor-panel-wolmart-studio, #vce-wolmart-studio-trigger, #wpb-wolmart-studio-trigger', openStudio)
			.on('click', '#wolmart-elementor-panel-wolmart-display-condition', openDisplayCondition)
			.on('click', '#wolmart-new-studio-trigger', confirmPageType)

		importStartTemplate();
	}
})(jQuery);