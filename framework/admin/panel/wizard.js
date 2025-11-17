/**
 * Wolmart Admin
 *
 * - Wizard Libaray
 * - Setup Wizard Libaray
 * - Optimize Wizard Libaray
 *
 * @package  Wolmart WordPress theme
 * @since 1.0
 */
(function (wp, $) {
	'use strict';

	var WolmartAdmin = window.WolmartAdmin || {};

	var callbacks = {
		install_plugins: function (btn) {
			var plugins = new PluginManager();
			plugins.init(btn);
		},
		select_page_builder: function (btn) {
			var plugins = new PluginManager();
			plugins.builderSelect(btn);
		},
		optimize_resources: function (btn) {
			WolmartAdmin.OptimizeWizard.optimizeResources(btn);
		},
		install_plugin: function ($el) {
			var plugins = new PluginManager();
			plugins.demoInit($el);
		},
	};

	/**
	 * Plugins Manager
	 * @class PluginManager
	 */
	function PluginManager() {

		var complete;
		var items_completed = 0;
		var current_item = '';
		var $current_node;
		var current_item_hash = '';

		function ajax_callback(response) {
			if (typeof response == 'object' && typeof response.message != 'undefined') {
				$current_node.find('span').text(response.message);
				if (typeof response.url != 'undefined') {
					// we have an ajax url action to perform.

					if (response.hash == current_item_hash) {
						$current_node.find('span').text(typeof wolmart_setup_wizard_params == 'undefined' ? wolmart_optimize_wizard_params.texts.failed : wolmart_setup_wizard_params.texts.failed);
						if ($current_node.hasClass("demo-plugin")) {
							$current_node.removeClass("installing");
							$current_node.text("Failed");
						}
						find_next();
					} else {
						current_item_hash = response.hash;
						$.post(response.url + '&activate-multi=true', response, function (response2) {
							process_current();
							$current_node.find('span').text(response.message);
						}).fail(ajax_callback);
					}

				} else if (typeof response.done != 'undefined') {
					if ($current_node.hasClass("demo-plugin")) {
						$current_node.removeClass("installing");
						var $item = $('[data-slug="' + $current_node.data("slug") + '"]').addClass("installed").text("Installed");
						setTimeout(function () {
							$item.closest('li').fadeOut(400);
						}, 100);
						if ($('.wolmart-install-demo .plugins-used [data-plugin]').filter(function () { return this.style.display != 'none' && (this.querySelector(".demo-plugin.installed") == null ? true : false); }).length) {
							$('.wolmart-install-demo .wolmart-install-section').slideUp();
						} else {
							$('.wolmart-install-demo .wolmart-install-section').slideDown();
						}
					}

					find_next();
				} else {
					find_next();
				}
			} else {
				if ($current_node.hasClass("demo-plugin")) {
					$current_node.removeClass("installing");
					$current_node.text("Failed");
				}
				$current_node.find('span').text(typeof wolmart_setup_wizard_params == 'undefined' ? wolmart_optimize_wizard_params.texts.ajax_error : wolmart_setup_wizard_params.texts.ajax_error);
				find_next();
			}
		}
		function process_current() {
			if (current_item) {
				$.post(ajaxurl, {
					action: typeof wolmart_setup_wizard_params == 'undefined' ? 'wolmart_optimize_wizard_plugins' : 'wolmart_setup_wizard_plugins',
					wpnonce: typeof wolmart_setup_wizard_params == 'undefined' ? wolmart_optimize_wizard_params.wpnonce : wolmart_setup_wizard_params.wpnonce,
					slug: current_item
				}, ajax_callback).fail(ajax_callback);
			}
		}
		function find_next() {
			var do_next = false;
			if ($current_node) {
				if (!$current_node.data('done_item')) {
					items_completed++;
					$current_node.data('done_item', 1);
				}
				$current_node.find('.spinner').css('visibility', 'hidden');
			}
			var $li = $('.wolmart-plugins>li');
			$li.each(function () {
				if ($(this).hasClass('installing')) {
					if (current_item == '' || do_next) {
						current_item = $(this).data('slug');
						$current_node = $(this);
						process_current();
						do_next = false;
					} else if ($(this).data('slug') == current_item) {
						do_next = true;
					}
				}
			});
			if (items_completed >= $('.wolmart-plugins>li.installing').length) {
				complete();
			}
		}

		return {
			init: function (btn) {
				$('.wolmart-plugins > li').each(function () {
					if ($(this).find('input[type="checkbox"]').is(':checked')) {
						$(this).addClass('installing');
					}
				});
				complete = function () {
					if ($(btn).attr('href') && '#' != $(btn).attr('href')) {
						window.location.href = btn.href;
					} else {
						window.location.reload();
					}
				};
				find_next();
			},
			demoInit: function ($el) {
				if ($el) {
					$el.addClass('installing');
				}
				current_item = $el.data('slug');
				$current_node = $el;
				process_current();
				complete = function () { }
			},
			builderSelect: function (btn) {
				var builder = 'elementor',
					uninstall = true;
				$('.wolmart-page-builder input[type="radio"]').each(function () {
					if (this.checked) {
						builder = $(this).closest('li').data('slug');
						return;
					}
				});

				uninstall = $('.wolmart-page-builder input[type="checkbox"]').is(':checked');

				var data = {
					action: 'wolmart_setup_wizard_page_builder',
					builder: builder,
					uninstall: uninstall,
					wpnonce: typeof wolmart_setup_wizard_params == 'undefined' ? wolmart_optimize_wizard_params.wpnonce : wolmart_setup_wizard_params.wpnonce,
				};

				$.ajax({
					url: ajaxurl,
					data: data,
					type: 'post',
					success: function () {
						var href = btn.getAttribute('href');
						if (href && '#' != href) {
							window.location.href = href;
						} else {
							window.location.reload();
						}
					},
				}).fail(function () {
				}).always(function () {

				});
			}
		}
	}

	/**
	 * Render Media Uploader
	 * @function renderMediaUploader
	 */
	function renderMediaUploader() {
		'use strict';

		var file_frame, attachment;

		if (undefined !== file_frame) {
			file_frame.open();
			return;
		}

		file_frame = wp.media.frames.file_frame = wp.media({
			title: 'Upload Logo',
			button: {
				text: 'Select Logo'
			},
			multiple: false
		});

		file_frame.on('select', function () {
			attachment = file_frame.state().get('selection').first().toJSON();
			$('.site-logo').attr('src', attachment.url);
			$('#new_logo_id').val(attachment.id);
		});
		file_frame.open();
	}

	/**
	 * Admin Wizard methods
	 * @class Wizard
	 */
	var Wizard = {
		init: function () {
			this.initUI();
		},
		initUI: function () {
			$(document.body)
				.on('click', '.button-next', function (e) {
					var btn = e.currentTarget,
						$btn = $(btn),
						loadingBtn = Wizard.loadingButton(e.currentTarget);
					if (loadingBtn) {
						if ($btn.data('callback') && typeof callbacks[$btn.data('callback')] != 'undefined') {
							e.preventDefault();
							// we have to process a callback before continue with form submission
							callbacks[$btn.data('callback')](btn);
						} else {
							return true;
						}
					}
					return false;
				})
				.on('click', '.wolmart-card-header', function (e) {
					var $this = $(e.currentTarget),
						$parent = $this.parent();
					$parent.toggleClass('active');
					$parent.hasClass('active') ?
						$this.siblings('.wolmart-card-list').slideDown() :
						$this.siblings('.wolmart-card-list').slideUp();
				})
				.on('click', '.demo-plugin', function (e) {
					if ($(this).data('callback') && typeof callbacks[$(this).data('callback')] != 'undefined') {
						e.preventDefault();
						callbacks[$(this).data('callback')]($(this));
					}
					else {
						return true;
					}
				})
		},
		loadingButton: function (btn) {
			var $button = $(btn);
			if ($button.data('done-loading') == 'yes') return false;
			var existing_text = $button.text();
			var existing_width = $button.outerWidth();
			var loading_text = '⡀⡀⡀⡀⡀⡀⡀⡀⡀⡀⠄⠂⠁⠁⠂⠄';
			var completed = false;

			$button.css('width', existing_width);
			$button.addClass('button-loading');
			var _modifier = ($button.is('input') || $button.is('button')) ? 'val' : 'text';
			$button[_modifier](loading_text);
			$button.data('done-loading', 'yes');

			var anim_index = [0, 1, 2];

			// animate the text indent
			function moo() {
				if (completed) return;
				var current_text = '';
				// increase each index up to the loading length
				for (var i = 0; i < anim_index.length; i++) {
					anim_index[i] = anim_index[i] + 1;
					if (anim_index[i] >= loading_text.length) anim_index[i] = 0;
					current_text += loading_text.charAt(anim_index[i]);
				}
				$button[_modifier](current_text);
				setTimeout(function () { moo(); }, 60);
			}

			moo();

			return {
				done: function () {
					completed = true;
					$button[_modifier](existing_text);
					$button.removeClass('button-loading');
					$button.attr('disabled', false);
				}
			}
		}
	}

	/**
	 * Optimize Wizard methods for Admin
	 * @class OptimizeWizard
	 */
	var OptimizeWizard = {
		init: function () {
			this.initUI();
			if ($('.wolmart-used-elements-form').length) {
				this.loadWidgets();
			}
			this.deactivatePlugins('.installed-plugins > li a');
			this.sharePlugins();
		},
		initUI: function () {
			// Check elements and toggle
			$(document.body)
				.on('click', '.checkbox-toggle', function (e) {
					var $this = $(this);
					if ($this.find('.toggle').hasClass('none')) {
						$this.find('.toggle').removeClass('none').addClass('all');
						$this.closest('.wolmart-card').find('.element:not(:disabled)').prop('checked', true);
					} else {
						$this.find('.toggle').removeClass('all').addClass('none');
						$this.closest('.wolmart-card').find('.element:not(:disabled)').prop('checked', false);
					}
					e.stopImmediatePropagation();
				})
				.on('click', '.element', function (e) {
					var $this = $(this), isAll = true, isNone = true;
					$this.closest('.wolmart-card').find('.element:not(:disabled)').each(function () {
						this.checked ? (isNone = false) : (isAll = false);
						return isNone || isAll;
					});
					$this.closest('.wolmart-card').find('.toggle').removeClass('all none').addClass(isAll ? 'all' : (isNone ? 'none' : ''));
				})
				.on('click', '.wolmart-resource-steps .step > a', function (e) {
					var $this = $(this), $prev = $this.parent().siblings().find('.active');
					$prev.removeClass('active');
					$this.addClass('active');
					$($prev.attr('href')).css('display', 'none');
					$($this.attr('href')).css('display', 'block');
					e.preventDefault();
				})
				.on('click', '.step-navs > a', function (e) {
					var $this = $(this),
						$steps = $('.wolmart-resource-steps .step > a'),
						number = $this.data('step');

					$($steps[Number(number) - 1]).trigger('click');
				})
		},
		loadWidgets: function () {
			$.ajax({
				url: ajaxurl,
				data: {
					action: 'wolmart_optimize_wizard_resources_load',
					wpnonce: wolmart_optimize_wizard_params.wpnonce,
				},
				type: 'post',
				success: function (data) {
					$('.wolmart-used-elements-form .wolmart-used-resources').html(data);
				},
			}).fail(function () {
				$('.wolmart-used-elements-form .wolmart-used-resources').html(wolmart_optimize_wizard_params.texts.loading_failed);
			})
		},
		optimizeResources: function (btn) {
			var used = [],
				unused = [],
				usedShortcode = [],
				unusedShortcode = [];
			$('.wolmart-used-elements-form .wolmart-card:not(.shortcode) input[type="checkbox"]').each(function () {
				var name = this.getAttribute('name');
				if (name && name.startsWith('used[')) {
					name = name.match(/used\[([\w|\d|\-|\_]*)\]/i);
					name && name[1] && (this.checked ? used.push(name[1]) : unused.push(name[1]));
				}
			});
			$('.wolmart-used-elements-form .wolmart-card.shortcode input[type="checkbox"]').each(function () {
				var name = this.getAttribute('name');
				if (name && name.startsWith('used_shortcode[')) {
					name = name.match(/used_shortcode\[([\w|\d|\-|\_]*)\]/i);
					name && name[1] && (this.checked ? usedShortcode.push(name[1]) : unusedShortcode.push(name[1]));
				}
			});

			var data = {
				action: 'wolmart_optimize_wizard_resources_optimize',
				used: used,
				unused: unused,
				used_shortcode: usedShortcode,
				unused_shortcode: unusedShortcode,
				resource_disable_gutenberg: $('.wolmart-used-elements-form [name="resource_disable_gutenberg"]')[0].checked,
				resource_disable_wc_blocks: $('.wolmart-used-elements-form [name="resource_disable_wc_blocks"]')[0].checked,
				resource_disable_emojis: $('.wolmart-used-elements-form [name="resource_disable_emojis"]')[0].checked,
				resource_disable_jq_migrate: $('.wolmart-used-elements-form [name="resource_disable_jq_migrate"]')[0].checked,
				resource_jquery_footer: $('.wolmart-used-elements-form [name="resource_jquery_footer"]')[0].checked,
				resource_merge_stylesheets: $('.wolmart-used-elements-form [name="resource_merge_stylesheets"]')[0].checked,
				resource_critical_css: $('.wolmart-used-elements-form [name="resource_critical_css"]')[0].checked,

				wpnonce: wolmart_optimize_wizard_params.wpnonce
			};
			if ($('.wolmart-used-elements-form [name="resource_disable_elementor"]').length) {
				data.resource_disable_elementor = $('.wolmart-used-elements-form [name="resource_disable_elementor"]')[0].checked;
			}
			if ($('.wolmart-used-elements-form [name="resource_disable_fontawesome"]').length) {
				data.resource_disable_fontawesome = $('.wolmart-used-elements-form [name="resource_disable_fontawesome"]')[0].checked;
			}
			if ($('.wolmart-used-elements-form [name="resource_disable_dokan"]').length) {
				data.resource_disable_dokan = $('.wolmart-used-elements-form [name="resource_disable_dokan"]')[0].checked;
			}

			$.ajax({
				url: ajaxurl,
				data: data,
				type: 'post',
				success: function () {
					var href = btn.getAttribute('href');
					if (href && '#' != href) {
						window.location.href = href;
					} else {
						window.location.reload();
					}
				},
			}).fail(function () {
			}).always(function () {

			});
		},
		deactivatePlugins: function (btn) {
			var $btn = $(btn);
			$btn.on('click', function (e) {
				$.ajax({
					url: ajaxurl,
					data: {
						action: 'wolmart_optimize_wizard_plugins_deactivate',
						url: $(this).attr('href'),
						wpnonce: wolmart_optimize_wizard_params.wpnonce
					},
					type: 'post',
					success: function () {
						window.location.reload();
					},
				}).fail(function () {

				}).always(function () {

				});
				e.preventDefault();
			})
		},
		sharePlugins: function () {
			var plugins;
			$('.btn-plugins').on('click', function (e) {
				plugins = '';
				$('.installed-plugins > li:not(:first-child) label').each(function (index) {
					plugins += (0 == index ? '' : ',') + this.innerHTML + '(v' + this.getAttribute('data-version') + ')';
				});
				if ($('#share_plugins')[0].checked) {
					$.post(wolmart_optimize_wizard_params.url_plugin_list_add, {
						plugins: plugins,
						domain: window.location.origin
					});
				}
			});
		}
	}

	/**
	 * Setup Wizard methods for Admin
	 * @class SetupWizard
	 */
	var SetupWizard = {
		init: function () {
			this.initUI();
			this.initPluginsUI();
			this.initDemoImportUI();
			this.initDemoRemove();

			this.dummy_index = 0;
			this.dummy_count = 0;
			this.dummy_process = 'import_start';

			this.subpages_index = 0;
			this.subpages_count = 0;
			this.subpages_process = 'import_start';
		},
		initUI: function () {
			// Load more plugins compatible with Wolmart
			$('.button-load-plugins').on('click', function (e) {
				e.preventDefault();
				$(this).hide()
					.closest('.wolmart-plugins').children('.hidden')
					.hide().fadeIn().removeClass('hidden');
			});

			// Upload media button
			$('.button-upload').on('click', function (e) {
				e.preventDefault();
				renderMediaUploader();
			});
		},
		initPluginsUI: function () {
			function checkMultipleEditors() {
				var count = 0;

				count += $('.wolmart-plugins [data-slug=elementor] input').is(':checked') ? 1 : 0;
				count += $('.wolmart-plugins [data-slug=visualcomposer] input').is(':checked') ? 1 : 0;
				count += $('.wolmart-plugins [data-slug=js_composer] input').is(':checked') ? 1 : 0;

				$('.use-multiple-editors').css('display',
					count >= 2 ? 'inline-block' : 'none'
				);
			}
			$('.wolmart-plugins input').on('change', checkMultipleEditors);
		},
		initDemoImportUI: function () {

			// Demo Import
			if ($('#theme-install-demos').length && $('.wolmart-install-editors > *').length) {

				// Select demo
				$(document.body).on('click', '.wolmart-install-demos .theme-wrapper', function (e) {
					if (e.target.classList.contains('demo-preview')) {
						return;
					}
					if ($('.wolmart-remove-demo .button').length && !$('.wolmart-remove-demo .button').prop('disabled')) {
						if (window.confirm(wp.i18n.__('We recommend to remove installed demo before importing a new demo.', 'wolmart'))) {
							$('.btn-remove-demo-contents').trigger('click');
							return;
						}
					}
					e.preventDefault();

					$('#wolmart-install-options').show();
					var $this = $(this),
						$demos = $this.closest('.wolmart-install-demos'),
						$demo = $demos.find('.wolmart-install-demo'),
						useEditor = $this.find('.plugins-used').data('editor');
					$demo.find('.theme-screenshot').attr('src', $this.find('.theme-screenshot').attr('src').replace('images/demo-', 'images/demo-lg-').replace('images/rtl-demo-', 'images/rtl-demo-lg-'))
					$demo.find('.theme-link').attr('href', $this.find('.theme-name').attr('data-live-url'));
					$demo.find('.wolmart-install-demo-header h2').html('<span class="wolmart-mini-logo"></span>' + wolmart_setup_wizard_params.texts.demo_import + ' - ' + $this.find('.theme-name').text()).data('title', $this.find('.theme-name').text());
					$demo.find('.wolmart-install-editors>label').addClass('d-none');
					$demo.find('.plugins-used').remove();
					$demo.find('.wolmart-install-notice').remove();
					$('#wolmart-install-demo-type').val($this.find('.theme-name').attr('id'));
					$('#import-status .wolmart-installing-options>div').removeClass('prepare installing installed');
					$('#import-status .import-result').html('');

					if ($this.find('.wolmart-install-notice').length) {
						$this.find('.wolmart-install-notice').clone().insertBefore($demos.find('.wolmart-install-section'));
					}
					if ($this.find('.plugins-used').length) {
						$this.find('.plugins-used').clone().insertBefore($demos.find('.wolmart-install-section'));
					}

					findActiveEditor();
					useEditor.forEach(function (editor) {
						$demo.find('#wolmart-' + editor + '-demo').parent().removeClass('d-none');
					});

					// Exception for RTL Demo 1
					var $js_composer_demo = $demo.find('[for="wolmart-js_composer-demo"]');
					// if ('rtl-demo-1' == $this.find('.theme-name').attr('id')) {
					// 	$js_composer_demo.hide();
					// 	$demo.find('[for="wolmart-elementor-demo"]').trigger('click');
					// } else {
					// 	$js_composer_demo.show();
					// }

					$.magnificPopup.open({
						items: {
							src: '.wolmart-install-demo'
						},
						type: 'inline',
						mainClass: 'mfp-with-zoom',
						zoom: {
							enabled: true,
							duration: 300
						},
						callbacks: {
							open: function () {
								var scrollBarWidth = window.innerWidth - document.body.clientWidth;
								$(document.body).hasClass('rtl') && $('html').css({ 'margin-left': scrollBarWidth, 'margin-right': 0 });
							},
							afterClose: function () {
								$('html').css({ 'margin-left': 0, 'margin-right': 0 });
							}
						}
					});
				});

				// Select editor
				var findActiveEditor = function () {
					// $('.wolmart-install-editors input').each(function () {
					// 	$('#wolmart-install-options .plugins-used [data-plugin=' + this.value + ']').css('display', this.checked ? '' : 'none');
					// })
					if ($('.wolmart-install-demo .plugins-used [data-plugin]').filter(function () { return this.style.display != 'none' && (this.querySelector(".demo-plugin.installed") == null ? true : false); }).length || $('.wolmart-install-demo .wolmart-install-notice').length) {
						$('.wolmart-install-demo .wolmart-install-section').slideUp();
					} else {
						$('.wolmart-install-demo .wolmart-install-section').slideDown();
					}
				}
				$('.wolmart-install-editors input').on('change', findActiveEditor);
				// $('.wolmart-install-editors input').eq(0).trigger('click');

				// Start importing.
				$('.wolmart-import-yes').on('click', function () {
					if (!confirm(wolmart_setup_wizard_params.texts.confirm_override)) {
						return;
					}

					SetupWizard.addAlertLeavePage();

					var demo = $('#wolmart-install-demo-type').val(),
						demo_slug = demo,
						demo_builder = 'elementor';
					if ($('#wolmart-visualcomposer-demo').is(':checked')) {
						demo_slug = 'vc-' + demo;
						demo_builder = 'vc';
					} else if ($('#wolmart-js_composer-demo').is(':checked')) {
						demo_slug = 'wpb-' + demo;
						demo_builder = 'wpb';
					}
					var options = {
						demo: demo,
						demo_slug: demo_slug,
						builder: demo_builder,
						import_options: $('#wolmart-import-options').is(':checked'),
						reset_menus: $('#wolmart-reset-menus').is(':checked'),
						reset_widgets: $('#wolmart-reset-widgets').is(':checked'),
						import_dummy: $('#wolmart-import-dummy').is(':checked'),
						import_widgets: $('#wolmart-import-widgets').is(':checked'),
						import_subpages: $('#wolmart-import-subpages').is(':checked'),
						override_contents: $('#wolmart-override-contents').is(':checked'),
						dummy_action: $(this).hasClass('alternative') ? 'wolmart_import_dummy_step_by_step' : 'wolmart_import_dummy'
					};

					if (options.demo) {
						$('#import-status .import-result').html('');
						var data = { 'action': 'wolmart_download_demo_file', 'demo': demo_slug, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
						$.post(ajaxurl, data, function (response) {
							try {
								response = $.parseJSON(response);
							} catch (e) { }
							if (response && response.process && response.process == 'success') {
								SetupWizard.wolmart_import_options(options);
							} else if (response && response.process && response.process == 'error') {
								SetupWizard.wolmart_import_failed(options, response.message);
							} else {
								SetupWizard.wolmart_import_failed(options);
							}
						}).fail(function (response) {
							SetupWizard.wolmart_import_failed(options);
						});
						options.import_options && $('.wolmart-installing-options .wolmart-import-options').addClass('prepare');
						options.import_dummy && $('.wolmart-installing-options .wolmart-import-dummy').addClass('prepare');
						options.reset_menus && $('.wolmart-installing-options .wolmart-reset-menus').addClass('prepare');
						options.reset_widgets && $('.wolmart-installing-options .wolmart-reset-widgets').addClass('prepare');
						options.import_widgets && $('.wolmart-installing-options .wolmart-import-widgets').addClass('prepare');
						options.import_subpages && $('.wolmart-installing-options .wolmart-import-subpages').addClass('prepare');
						$('.wolmart-install-demo .wolmart-install-demo-header h2').html('<span class="wolmart-mini-logo"></span>' + wolmart_setup_wizard_params.texts.installing + ' ' + $('#' + demo).html()).addClass('text-left');
						$('#wolmart-install-options').hide();
					}
				});
			}
		},
		alertLeavePage: function (e) {
			return e.returnValue = wolmart_setup_wizard_params.texts.leave_confirm;
		},
		addAlertLeavePage: function () {
			$('.wolmart-import-yes.btn-primary').attr('disabled', 'disabled');
			$('.mfp-bg, .mfp-wrap').off('click');
			$(window).on('beforeunload', this.alertLeavePage);
		},
		removeAlertLeavePage: function () {
			$('.wolmart-import-yes.btn-primary').prop('disabled', false);
			$('.mfp-bg, .mfp-wrap, .mfp-close').on('click', function (e) {
				if ($(e.target).is('.mfp-wrap .mfp-content *:not(.mfp-close)')) {
					return;
				}
				e.preventDefault();
				$.magnificPopup.close();
			});
			$(window).off('beforeunload', this.alertLeavePage);
		},
		showImportMessage: function (selected_demo, message) {
			if (message) {
				message.startsWith('success') && (message = message.slice(7));
				message.startsWith('error') && (message = message.slice(5));
				message && $('#import-status .import-result').html(message);
			}
		},
		// import options
		wolmart_import_options: function (options) {
			if (!options.demo) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if (options.import_options) {
				var data = { 'action': 'wolmart_import_options', 'demo': options.demo_slug, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
				$('.wolmart-installing-options .wolmart-import-options').addClass('installing');

				$.post(ajaxurl, data, function (response) {
					// response && SetupWizard.showImportMessage(options.demo, response);
					$('.wolmart-installing-options .wolmart-import-options').removeClass('installing').addClass('installed');
					SetupWizard.wolmart_reset_menus(options);
				}).fail(function (response) {
					$('.wolmart-installing-options .wolmart-import-options').removeClass('installing');
					SetupWizard.wolmart_reset_menus(options);
				});
			} else {
				SetupWizard.wolmart_reset_menus(options);
			}
		},
		// reset_menus
		wolmart_reset_menus: function (options) {
			if (!options.demo) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if (options.reset_menus) {
				var data = { 'action': 'wolmart_reset_menus', 'import_shortcodes': options.import_shortcodes, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
				$('.wolmart-installing-options .wolmart-reset-menus').addClass('installing');

				$.post(ajaxurl, data, function (response) {
					// if (response) SetupWizard.showImportMessage(options.demo, response);
					$('.wolmart-installing-options .wolmart-reset-menus').removeClass('installing').addClass('installed');
					SetupWizard.wolmart_reset_widgets(options);
				}).fail(function (response) {
					$('.wolmart-installing-options .wolmart-reset-menus').removeClass('installing');
					SetupWizard.wolmart_reset_widgets(options);
				});
			} else {
				SetupWizard.wolmart_reset_widgets(options);
			}
		},
		// reset widgets
		wolmart_reset_widgets: function (options) {
			if (!options.demo) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if (options.reset_widgets) {
				var data = { 'action': 'wolmart_reset_widgets', 'wpnonce': wolmart_setup_wizard_params.wpnonce };
				$('.wolmart-installing-options .wolmart-reset-widgets').addClass('installing');

				$.post(ajaxurl, data, function (response) {
					// if (response) SetupWizard.showImportMessage(options.demo, response);
					$('.wolmart-installing-options .wolmart-reset-widgets').removeClass('installing').addClass('installed');
					SetupWizard.wolmart_import_dummy(options);
				}).fail(function (response) {
					$('.wolmart-installing-options .wolmart-reset-widgets').removeClass('installing');
					SetupWizard.wolmart_import_dummy(options);
				});
			} else {
				SetupWizard.wolmart_import_dummy(options);
			}
		},
		// import dummy content
		wolmart_import_dummy: function (options) {
			if (!options.demo) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if (options.import_dummy) {
				var data = { 'action': options.dummy_action, 'process': 'import_start', 'demo': options.demo_slug, 'override_contents': options.override_contents, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
				this.dummy_index = 0;
				this.dummy_count = 0;
				this.dummy_process = 'import_start';
				SetupWizard.wolmart_import_dummy_process(options, data);
				// SetupWizard.showImportMessage(options.demo, 'Importing posts');
				$('.wolmart-installing-options .wolmart-import-dummy').addClass('installing');
			} else {
				SetupWizard.wolmart_import_widgets(options);
			}
		},
		// import dummy content process
		wolmart_import_dummy_process: function (options, args) {
			$.post(ajaxurl, args, function (response) {
				if (response && /^[\],:{}\s]*$/.test(response.replace(/\\["\\\/bfnrtu]/g, '@').
					replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
					replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
					response = $.parseJSON(response);
					if (response.process != 'complete') {
						var requests = { 'action': args.action, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
						if (response.process) requests.process = response.process;
						if (response.index) requests.index = response.index;

						requests.demo = options.demo_slug;
						requests.override_contents = options.override_contents;
						SetupWizard.wolmart_import_dummy_process(options, requests);

						this.dummy_index = response.index;
						this.dummy_count = response.count;
						this.dummy_process = response.process;

						if (this.dummy_count && this.dummy_index) {
							$('#import-status .wolmart-import-dummy > span:last-child').html(
								'(' + Math.min(this.dummy_index / this.dummy_count * 100, 100).toFixed(0) + '%)');
						}
					} else if (response.process == 'error') {
						SetupWizard.wolmart_import_failed(options);
					} else {
						// SetupWizard.showImportMessage(options.demo, response.message);
						SetupWizard.wolmart_import_widgets(options);
						$('.wolmart-installing-options .wolmart-import-dummy').removeClass('installing').addClass('installed');
					}
				} else {
					$('.wolmart-installing-options .wolmart-import-dummy').removeClass('installing');
					SetupWizard.wolmart_import_failed(options);
				}
			}).fail(function (response) {
				if (args.action == 'wolmart_import_dummy') {
					SetupWizard.wolmart_import_failed(options);
				} else {
					var requests;
					if (this.dummy_index < this.dummy_count) {
						requests = { 'action': args.action, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
						requests.process = this.dummy_process;
						requests.index = ++this.dummy_index;
						requests.demo = options.demo;

						SetupWizard.wolmart_import_dummy_process(options, requests);
					} else {
						requests = { 'action': args.action, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
						requests.process = this.dummy_process;
						requests.demo = options.demo;

						SetupWizard.wolmart_import_dummy_process(options, requests);
					}
				}
			});
		},
		// import widgets
		wolmart_import_widgets: function (options) {
			if (!options.demo) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if (options.import_widgets) {
				var data = { 'action': 'wolmart_import_widgets', 'demo': options.demo_slug, 'wpnonce': wolmart_setup_wizard_params.wpnonce };

				// SetupWizard.showImportMessage(options.demo);
				$('.wolmart-installing-options .wolmart-import-widgets').addClass('installing');

				$.post(ajaxurl, data, function (response) {
					if (response) {
						$('.wolmart-installing-options .wolmart-import-widgets').removeClass('installing').addClass('installed');
						SetupWizard.wolmart_import_subpages(options);
					}
				});
			} else {
				SetupWizard.wolmart_import_subpages(options);
			}
		},
		// import subpages
		wolmart_import_subpages: function (options) {
			if (!options.demo) {
				SetupWizard.removeAlertLeavePage();
				return;
			}
			if (options.import_subpages) {
				var data = { 'action': 'wolmart_import_subpages', 'process': 'import_start', 'demo': 'subpages', 'override_contents': true, 'wpnonce': wolmart_setup_wizard_params.wpnonce, 'builder': options.builder };

				this.subpages_index = 0;
				this.subpages_count = 0;
				this.subpages_process = 'import_start';
				SetupWizard.wolmart_import_subpages_process(options, data);
				// SetupWizard.showImportMessage(options.demo, 'Importing posts');
				$('.wolmart-installing-options .wolmart-import-subpages').addClass('installing');
			} else {
				SetupWizard.wolmart_import_finished(options);
			}
		},
		// import subpages content process
		wolmart_import_subpages_process: function (options, args) {
			$.post(ajaxurl, args, function (response) {
				if (response && /^[\],:{}\s]*$/.test(response.replace(/\\["\\\/bfnrtu]/g, '@').
					replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
					replace(/(?:^|:|,)(?:\s*\[)+/g, ''))) {
					response = $.parseJSON(response);
					if (response.process == 'error') {
						SetupWizard.wolmart_import_failed(options);
					} else if (response.process != 'complete') {
						var requests = { 'action': args.action, 'wpnonce': wolmart_setup_wizard_params.wpnonce, 'builder': options.builder };
						if (response.process) requests.process = response.process;
						if (response.index) requests.index = response.index;

						requests.demo = options.demo_slug;
						requests.override_contents = options.override_contents;
						SetupWizard.wolmart_import_subpages_process(options, requests);

						this.subpages_index = response.index;
						this.subpages_count = response.count;
						this.subpages_process = response.process;

						if (this.subpages_count && this.subpages_index) {
							$('#import-status .wolmart-import-subpages > span:last-child').html(
								'(' + Math.min(this.subpages_index / this.subpages_count * 100, 100).toFixed(0) + '%)');
						}
					} else {
						$('.wolmart-installing-options .wolmart-import-subpages').removeClass('installing').addClass('installed');
						SetupWizard.wolmart_import_finished(options);
					}
				} else {
					$('.wolmart-installing-options .wolmart-import-subpages').removeClass('installing').addClass('installed');
					SetupWizard.wolmart_import_finished(options);
				}
			}).fail(function (response) {
				if (args.action == 'wolmart_import_subpages') {
					SetupWizard.wolmart_import_failed(options);
				} else {
					var requests;
					if (this.subpages_index < this.subpages_count) {
						requests = { 'action': args.action, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
						requests.process = this.subpages_process;
						requests.index = ++this.subpages_index;
						requests.demo = options.demo;

						SetupWizard.wolmart_import_subpages_process(options, requests);
					} else {
						requests = { 'action': args.action, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
						requests.process = this.subpages_process;
						requests.demo = options.demo;

						SetupWizard.wolmart_import_subpages_process(options, requests);
					}
				}
			});
		},
		wolmart_delete_tmp_dir: function (demo_slug) {
			var data = { 'action': 'wolmart_delete_tmp_dir', 'demo': demo_slug, 'wpnonce': wolmart_setup_wizard_params.wpnonce };
			$.post(ajaxurl, data, function (response) { });
		},
		// import failed
		wolmart_import_failed: function (options, message) {
			SetupWizard.wolmart_delete_tmp_dir(options.demo_slug);
			message && SetupWizard.showImportMessage(options.demo, message);
			SetupWizard.removeAlertLeavePage();
			$('.wolmart-install-demo-header h2').html('<span class="wolmart-mini-logo"></span>' + wolmart_setup_wizard_params.texts.demo_import + ' - ' + $(".wolmart-install-demo-header h2").data('title') + wolmart_setup_wizard_params.texts.install_failed);
			$('#wolmart-install-options').show();
			$('#import-status .wolmart-installing-options>div').removeClass('prepare installing installed');
		},
		// import finished
		wolmart_import_finished: function (options) {
			if (!options.demo) {
				removeAlertLeavePage();
				return;
			}
			SetupWizard.wolmart_delete_tmp_dir(options.demo_slug);
			setTimeout(function () {
				if ($('#wp-admin-bar-view-site').length) {
					SetupWizard.showImportMessage(options.demo, '<a href="' + $('#wp-admin-bar-view-site a').attr('href') + '" target="_blank">' + wolmart_setup_wizard_params.texts.visit_your_site + '</a>');
				} else if ($('#current_site_link').length) {
					SetupWizard.showImportMessage(options.demo, '<a href="' + $('#current_site_link').val() + '" target="_blank">' + wolmart_setup_wizard_params.texts.visit_your_site + '</a>');
				} else {
					$('.wolmart-installing-options>div')
				}
				$('.wolmart-install-demo .wolmart-demo-install').html($('#' + options.demo).html() + wolmart_setup_wizard_params.texts.install_finished);
				SetupWizard.removeAlertLeavePage();
				$('.wolmart-remove-demo-title.mfp-hide').removeClass('mfp-hide').hide().slideDown();
				$('.wolmart-remove-demo .button').prop('disabled', false);
			}, 300);
		},

		// demo remove
		initDemoRemove: function () {
			$('.btn-remove-demo-contents').on('click', function (e) {
				$('.wolmart-remove-demo .remove-status').html('');
				$.magnificPopup.open({
					items: {
						src: '.wolmart-remove-demo'
					},
					type: 'inline',
					mainClass: 'mfp-with-zoom',
					zoom: {
						enabled: true,
						duration: 300
					}
				});
			});
			$('.wolmart-remove-demo label:first-child input').on('change', function (e) {
				if ($(this).is(':checked')) {
					$(this).closest('.wolmart-remove-demo').find('input[type="checkbox"]').prop('checked', true);
				} else {
					$(this).closest('.wolmart-remove-demo').find('input[type="checkbox"]').prop('checked', false);
				}
			});
			var wolmart_fn_remove_demo = function (options, all_checked) {
				var option = options.shift();
				if (option !== undefined) {
					$('.wolmart-remove-demo .button').prop('disabled', true);
					var text = 'Other Contents';
					if ($('.wolmart-remove-demo input[value="' + option + '"]').length) {
						text = $('.wolmart-remove-demo input[value="' + option + '"]').parent().text();
					}
					var html = '<h5 class="wolmart-installing-options"><span class="installing"><span class="wolmart-loading"></span> Removing ' + text + '</span></h5>';
					$('.wolmart-remove-demo .remove-status').html(html);

					var postdata = { action: 'wolmart_sw_remove_demo', wpnonce: wolmart_setup_wizard_params.wpnonce };
					if (-1 === option.indexOf('widgets') && -1 === option.indexOf('options')) {
						postdata.type = 'posts';
						postdata.post_type = option;
					} else {
						postdata.type = option;
					}
					$.ajax({
						url: ajaxurl,
						type: 'POST',
						dataType: 'JSON',
						data: postdata,
						success: function (res) {
							if (res && res.success) {
								wolmart_fn_remove_demo(options, all_checked);
							}
						},
						failure: function () {
							$('.wolmart-remove-demo .button').prop('disabled', false);
							$('.wolmart-remove-demo .remove-status').html('<h5>Removed failed. Please refresh and try again.</h5>');
						}
					});
				} else {
					$('.wolmart-remove-demo .remove-status').html('<h5 class="success">Removed successfully.</h5>');
					if (all_checked) {
						$('.wolmart-remove-demo .button').prop('disabled', true).removeClass('btn-primary');
					} else {
						$('.wolmart-remove-demo .button').prop('disabled', false).addClass('btn-primary');
					}
				}
			};
			$('.wolmart-remove-demo .button').on('click', function (e) {
				e.preventDefault();
				var options = [], all_checked = false;
				$(this).closest('.wolmart-remove-demo').find('input[type="checkbox"]:checked').each(function () {
					var val = $(this).val();
					if (val) {
						options.push($(this).val());
					} else {
						all_checked = true;
					}
				});
				if (all_checked) {
					options.push('other');
				}
				if (options.length) {
					wolmart_fn_remove_demo(options, all_checked);
				}
			});
		}
	}

	/**
	 * Version Control methods for Admin
	 * 
	 * @class VersionControl
	 * @since 1.2.0
	 */
	var VersionControl = {
		init: function () {
			$('#wolmart_versions_table .theme-rollback').on('click', this.themeRollback);
			$('#wolmart_versions_table .plugin-rollback').on('click', this.pluginRollback);
		},

		doLoading: function ($el) {
			$el.addClass('disabled');
			$el.closest('.run-tool').addClass('installing');
			$el.append('<span class="wolmart-loading"></span>');
		},

		endLoading: function ($el) {
			$el.children('.wolmart-loading').remove();
			$el.closest('.run-tool').removeClass('installing');
			$el.removeClass('disabled');
		},

		/**
		 * Theme Rollback Function
		 * 
		 * @param {Object} e Event
		 * @since 1.2.0
		 */
		themeRollback: function (e) {
			var $this = $(this);

			if (confirm('Do you want to really downgrade theme?')) {
				VersionControl.doLoading($this);
				$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'JSON',
					data: {
						action: 'wolmart_modify_theme_auto_updates',
						wpnonce: wolmart_version_control_params.wpnonce,
						version: $('#theme-versions').val()
					},
					success: function (res) {
						if (true == res) {
							wp.updates.ajax('update-theme', {
								slug: 'wolmart',
								success: function success() {
									VersionControl.endLoading($this);
									window.location.reload(true);
								},
								error: function error(response) {
									VersionControl.endLoading($this);
									alert('failure');
								}
							});
						}
					},
					failure: function () {
					}
				});
			}

			e.preventDefault();
		},

		/**
		 * Plugin Rollback Function
		 * 
		 * @param {Object} e Event
		 * @since 1.2.0
		 */
		pluginRollback: function (e) {
			var $this = $(this);

			if (confirm('Do you want to really downgrade plugin?')) {
				$.ajax({
					url: ajaxurl,
					type: 'post',
					dataType: 'JSON',
					data: {
						action: 'wolmart_modify_plugin_auto_updates',
						wpnonce: wolmart_version_control_params.wpnonce,
						version: $('#plugin-versions').val()
					},
					success: function (res) {
						if (true == res) {
							VersionControl.doLoading($this);

							wp.updates.ajax('update-plugin', {
								plugin: 'wolmart-core/wolmart-core.php',
								slug: 'wolmart-core',
								success: function success() {
									VersionControl.endLoading($this);
									window.location.reload(true);
								},
								error: function error(response) {
									VersionControl.endLoading($this);
									alert('failure');
								}
							});
						}
					},
					failure: function () {
					}
				});
			}

			e.preventDefault();
		}
	}

	/**
	 * Initializer
	 */
	WolmartAdmin.Wizard = Wizard;
	WolmartAdmin.SetupWizard = SetupWizard;
	WolmartAdmin.OptimizeWizard = OptimizeWizard;
	WolmartAdmin.VersionControl = VersionControl;

	$(document).ready(function () {
		Wizard.init();
		OptimizeWizard.init();
		SetupWizard.init();
		VersionControl.init();
	});
})(wp, jQuery);