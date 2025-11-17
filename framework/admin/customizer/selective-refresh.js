/**
 * Selective Refresh for Customize
 * 
 * @package  Wolmart WordPress theme
 * @since 1.0
 */
'use strict';
jQuery(document).ready(function ($) {

    function getCustomize(option) {
        var o = wp.customize(option);
        return o ? o.get() : '';
    }

    var options = [
        'container', 'container_fluid',
        ['site_type', 'site_bg', 'content_bg', 'site_width', 'site_gap'],
        ['breakpoint_tab', 'breakpoint_mob'],
        ['primary_color', 'secondary_color', 'dark_color', 'light_color'],
        ['typo_default'],
        'typo_heading',
        'ptb_bg', 'ptb_height', 'prod_title_clamp',
        'typo_ptb_title', 'typo_ptb_subtitle', 'typo_ptb_breadcrumb',
        'share_color',
        ['custom_css'],
        ['rounded_skin'],
    ];
    var tooltips = [{
        target: '.main-content .product-single .social-icons, .main-content .post-single .social-icons',
        text: 'Share',
        elementID: 'share',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '.page-header',
        text: 'Page Title Bar',
        elementID: 'title_bar',
        pos: 'bottom',
        type: 'section'
    }, {
        target: '.post-archive',
        text: 'Archive Page',
        elementID: 'blog_archive',
        pos: 'top',
        type: 'section'
    }, {
        target: '.single .post-single',
        text: 'Single Page',
        elementID: 'blog_single',
        pos: 'top',
        type: 'section'
    }, {
        target: '.main-content > .products, .main-content > .yit-wcan-container > .products',
        text: 'Product Archive Page',
        elementID: 'products_archive',
        pos: 'top',
        type: 'section'
    }, {
        target: '.single .product-single',
        text: 'Product Page',
        elementID: 'product_layout',
        pos: 'top',
        type: 'section'
    }, {
        target: '.products .product-wrap .product',
        text: 'Product Type',
        elementID: 'product_type',
        pos: 'center',
        type: 'section'
    }, {
        target: '.products .category-wrap .product-category',
        text: 'Category Type',
        elementID: 'category_type',
        pos: 'center',
        type: 'section'
    }];

    $.fn.wolmartTooltip = function (options) {
        options.target = escape(options.target.replace(/"/g, ''));
        $('.wolmart-tooltip[data-target="' + options.target + '"]').remove();
        return $(this).each(function () {
            if ($(this).hasClass('wolmart-tooltip-initialized')) {
                return;
            }

            var $this = $(this),
                $tooltip = $('<div class="wolmart-tooltip" data-target="' + options.target + '" style="display: none; position: absolute; z-index: 9999;">' + options.text + '</div>').appendTo('body');
            $tooltip.data('triggerObj', $this);
            if (options.init) {
                $tooltip.data('initCall', options.init);
            }
            $this.on('mouseenter', function () {
                $tooltip.text(options.text);
                if (options.position == 'top') {
                    $tooltip.css('top', $this.offset().top - $tooltip.outerHeight() / 2).css('left', $this.offset().left + $this.outerWidth() / 2 - $tooltip.outerWidth() / 2);
                } else if (options.position == 'bottom') {
                    $tooltip.css('top', $this.offset().top + $this.outerHeight() - $tooltip.outerHeight() / 2).css('left', $this.offset().left + $this.outerWidth() / 2 - $tooltip.outerWidth() / 2);
                } else if (options.position == 'left') {
                    $tooltip.css('top', $this.offset().top + $this.outerHeight() / 2 - $tooltip.outerHeight() / 2).css('left', $this.offset().left - $tooltip.outerWidth() / 2);
                } else if (options.position == 'right') {
                    $tooltip.css('top', $this.offset().top + $this.outerHeight() / 2 - $tooltip.outerHeight() / 2).css('left', $this.offset().left + $this.outerWidth() - $tooltip.outerWidth() / 2);
                } else if (options.position == 'center') {
                    $tooltip.css('top', $this.offset().top + $this.outerHeight() / 2 - $tooltip.outerHeight() / 2).css('left', $this.offset().left + $this.outerWidth() / 2 - $tooltip.outerWidth() / 2);
                }
                $tooltip.stop().fadeIn(100);
                $this.addClass('wolmart-tooltip-active');
            }).on('mouseleave', function () {
                $tooltip.stop(true, true).fadeOut(400);
                $this.removeClass('wolmart-tooltip-active');
            }).addClass('wolmart-tooltip-initialized');
        });
    }

    function initTooltipSection(e, $obj) {
        if (e.elementID && 'custom' != e.type) {
            if (!e.type) {
                e.type = 'section';
            }
            window.parent.wp.customize[e.type](e.elementID).focus();
            if (e.type == 'section' && window.parent.wp.customize[e.type](e.elementID).contentContainer) {
                window.parent.jQuery('body').trigger('initReduxFields', [window.parent.wp.customize[e.type](e.elementID).contentContainer]);
            } else if (e.type == 'control' && window.parent.wp.customize[e.type](e.elementID).container) {
                window.parent.jQuery('body').trigger('initReduxFields', [window.parent.wp.customize[e.type](e.elementID).container.closest('.control-section')]);
            }
        } else if ('custom' == e.type && e.elementID) {
            window.parent.wp.customize.section('wolmart_header_layouts').focus();
            var index = $(e.target, '.header-wrapper').index($obj),
                isMobile = $obj.closest('.visible-for-sm:visible').length ? true : false;
            $('.wolmart-header-builder .header-wrapper-' + (isMobile ? 'mobile' : 'desktop') + ' .header-builder-wrapper', window.parent.document).find('[data-id="' + e.elementID + '"]').eq(index).trigger('click');
        }
    }

    function initCustomizerTooltips($parent) {
        tooltips.forEach(function (e) {
            if ($(e.target).is($parent) || $parent.find($(e.target)).length) {
                e.type || (e.type = 'control');
                $(e.target).wolmartTooltip({
                    position: e.pos,
                    text: e.text,
                    target: e.target,
                    init: function ($obj) {
                        initTooltipSection(e, $obj);
                    }
                });
            }
        });

        $(document.body).on('mouseenter', '.wolmart-tooltip', function () {
            $(this).stop(true, true).show();
            var obj = $(this).data('triggerObj');
            if (obj) {
                obj.addClass('wolmart-tooltip-active');
            }
        }).on('mouseleave', '.wolmart-tooltip', function () {
            $(this).stop().fadeOut(400);
            var obj = $(this).data('triggerObj');
            if (obj) {
                obj.removeClass('wolmart-tooltip-active');
            }
        }).on('click', '.wolmart-tooltip', function () {
            var initCall = $(this).data('initCall');
            if (initCall) {
                initCall.call(this, $(this).data('triggerObj'));
            }
        });
    }

    function initDynamicCSS() {
        var handles = ['wolmart-theme-shop', 'wolmart-theme-blog', 'wolmart-theme-shop-other', 'wolmart-theme-single-product', 'wolmart-theme-single-post'],
            h = 'wolmart-theme',
            style = '';

        handles.forEach(function (value, idx) {
            if ('wolmart-theme' != h) {
                return;
            }

            if ($('#' + value + '-inline-css').length) {
                h = value;
            }
        });

        style = $('#' + h + '-inline-css').text();

        var res_keys = style.matchAll(/\n(--[^\: ]*)\:/g),
            res_values = style.matchAll(/\n--[^\: ]*\: ([^\;]*)\;/g),
            keys = [],
            values = [],
            htmlStyle = $('html')[0].style;

        for (var key of res_keys) {
            keys.push(key[1]);
        }
        for (var value of res_values) {
            values.push(value[1]);
        }

        for (var i = 0; i < keys.length; i++) {
            htmlStyle.setProperty(keys[i], values[i]);
        }

		// fixed in 1.3.0
		if ( 'wolmart-theme' == h ) {
			$('#' + h + '-inline-css').text( style.replace( /html {[^}]*}/, '' ) );
		} else {
            $('#' + h + '-inline-css').text('');
        }
	}

    initCustomizerTooltips($('body'));
    initDynamicCSS();
    eventsInit();

    for (var i = 0; i < options.length; i++) {
        if (Array.isArray(options[i])) {
            var option = options[i];
        } else {
            var option = [options[i]];
        }

        for (var j = 0; j < option.length; j++) {
            wp.customize(option[j], function (e) {
                var event = option[0];
                e.bind(function (value) {
                    $(document.body).trigger(event);
                });
            });
        }

        $(document.body).trigger(option[0]);
    }

    function eventsInit() {
        var style = $('html')[0].style;

        $(document.body).on('container', function () {
            style.setProperty('--wolmart-container-width', getCustomize('container') + 'px');
            style.setProperty('--wolmart-container-width-max', getCustomize('container') - 1 + 'px');
        })
        $(document.body).on('container_fluid', function () {
            style.setProperty('--wolmart-container-fluid-width', getCustomize('container_fluid') + 'px');
            style.setProperty('--wolmart-container-fluid-width-max', getCustomize('container_fluid') - 1 + 'px');
        })

        $(document.body).on('site_type', function () {
            var site_type = getCustomize('site_type');
            if ('full' != site_type) {
                $(document.body).addClass('site-boxed');
                wolmart_selective_background(style, 'site', getCustomize('site_bg'));
                style.setProperty('--wolmart-site-width', getCustomize('site_width') + 'px');
                style.setProperty('--wolmart-site-margin', '0 auto');
                if ('boxed' == site_type) {
                    style.setProperty('--wolmart-site-gap', '0 ' + getCustomize('site_gap') + 'px');
                } else {
                    style.setProperty('--wolmart-site-gap', getCustomize('site_gap') + 'px');
                }
            } else {
                $(document.body).removeClass('site-boxed');
                wolmart_selective_background(style, 'site', { 'background-color': '#fff' });
                style.setProperty('--wolmart-site-width', 'false');
                style.setProperty('--wolmart-site-margin', '0');
                style.setProperty('--wolmart-site-gap', '0');
            }

            wolmart_selective_background(style, 'page-wrapper', getCustomize('content_bg'));
        })

        $(document.body).on('rounded_skin', function () {
            $(document.body).toggleClass('wolmart-rounded-skin', getCustomize('rounded_skin'));
        })

        $(document.body).on('primary_color', function () {
            style.setProperty('--wolmart-primary-color', getCustomize('primary_color'));
            style.setProperty('--wolmart-primary-color-hover', getLighten(getCustomize('primary_color')));
            style.setProperty('--wolmart-primary-color-op-90', getColorA(getCustomize('primary_color'), 0.9));
            style.setProperty('--wolmart-secondary-color', getCustomize('secondary_color'));
            style.setProperty('--wolmart-secondary-color-hover', getLighten(getCustomize('secondary_color')));
            style.setProperty('--wolmart-dark-color', getCustomize('dark_color'));
            style.setProperty('--wolmart-dark-color-hover', getLighten(getCustomize('dark_color')));
            style.setProperty('--wolmart-light-color', getCustomize('light_color'));
            style.setProperty('--wolmart-light-color-hover', getLighten(getCustomize('light_color')));
        })
        $(document.body).on('typo_default', function () {
            wolmart_selective_typography(style, 'body', getCustomize('typo_default'));
        })
        $(document.body).on('typo_heading', function () {
            wolmart_selective_typography(style, 'heading', getCustomize('typo_heading'));
        })
        $(document.body).on('ptb_bg', function () {
            wolmart_selective_background(style, 'ptb', getCustomize('ptb_bg'));
        })
        $(document.body).on('ptb_height', function () {
            style.setProperty('--wolmart-ptb-height', getCustomize('ptb_height') + 'px');
        })
        $(document.body).on('prod_title_clamp', function () {
            style.setProperty('--wolmart-prod-title-clamp', getCustomize('prod_title_clamp'));
        })
        $(document.body).on('ptb_breadcrumb_bg', function () {
            style.setProperty('--wolmart-ptb-breadcrumb-background', getCustomize('ptb_breadcrumb_bg'));
        })
        $(document.body).on('typo_ptb_title', function () {
            wolmart_selective_typography(style, 'ptb-title', getCustomize('typo_ptb_title'));
        })
        $(document.body).on('typo_ptb_subtitle', function () {
            wolmart_selective_typography(style, 'ptb-subtitle', getCustomize('typo_ptb_subtitle'));
        })
        $(document.body).on('typo_ptb_breadcrumb', function () {
            wolmart_selective_typography(style, 'ptb-breadcrumb', getCustomize('typo_ptb_breadcrumb'));
        })

        $(document.body).on('custom_css', function () {
            if (!$('style#wolmart-preview-custom-inline-css').length) {
                $('<style id="wolmart-preview-custom-inline-css"></style>').insertAfter('#wolmart-preview-custom-css');
            }

            $('style#wolmart-preview-custom-inline-css').html(getCustomize('custom_css'));
        })
    }

    function wolmart_selective_background(style, id, bg) {
        if (bg['background-color']) {
            style.setProperty('--wolmart-' + id + '-bg-color', bg['background-color']);
        } else {
            style.removeProperty('--wolmart-' + id + '-bg-color');
        }
        if (bg['background-image']) {
            style.setProperty('--wolmart-' + id + '-bg-image', 'url(' + bg['background-image'] + ')');

            if (bg['background-repeat']) {
                style.setProperty('--wolmart-' + id + '-bg-repeat', bg['background-repeat']);
            }
            if (bg['background-position']) {
                style.setProperty('--wolmart-' + id + '-bg-position', bg['background-position']);
            }
            if (bg['background-size']) {
                style.setProperty('--wolmart-' + id + '-bg-size', bg['background-size']);
            }
            if (bg['background-attachment']) {
                style.setProperty('--wolmart-' + id + '-bg-attachment', bg['background-attachment']);
            }
        } else {
            style.removeProperty('--wolmart-' + id + '-bg-image');
            style.removeProperty('--wolmart-' + id + '-bg-repeat');
            style.removeProperty('--wolmart-' + id + '-bg-position');
            style.removeProperty('--wolmart-' + id + '-bg-size');
            style.removeProperty('--wolmart-' + id + '-bg-attachment');
        }
    }

    function wolmart_selective_typography(style, id, typo) {
        if (typo['font-family'] && 'inherit' != typo['font-family']) {
            style.setProperty('--wolmart-' + id + '-font-family', "'" + typo['font-family'] + "', sans-serif");

            if (!typo['variant']) {
                typo['variant'] = 400;
            }
        } else {
            style.removeProperty('--wolmart-' + id + '-font-family');
        }
        if (typo['variant']) {
            style.setProperty('--wolmart-' + id + '-font-weight', 'regular' == typo['variant'] ? 400 : typo['variant']);
        } else if ('heading' == id) {
            style.setProperty('--wolmart-' + id + '-font-weight', 600);
        } else {
            style.removeProperty('--wolmart-' + id + '-font-weight');
        }
        if (typo['font-size'] && '' != typo['font-size']) {
            style.setProperty('--wolmart-' + id + '-font-size', (Number(typo['font-size']) ? (typo['font-size'] + 'px') : typo['font-size']));
        } else {
            style.removeProperty('--wolmart-' + id + '-font-size');
        }
        if (typo['line-height'] && '' != typo['line-height']) {
            style.setProperty('--wolmart-' + id + '-line-height', typo['line-height']);
        } else {
            style.removeProperty('--wolmart-' + id + '-line-height');
        }
        if (typo['letter-spacing'] && '' != typo['letter-spacing']) {
            style.setProperty('--wolmart-' + id + '-letter-spacing', typo['letter-spacing']);
        } else {
            style.removeProperty('--wolmart-' + id + '-letter-spacing');
        }
        if (typo['text-transform'] && '' != typo['text-transform']) {
            style.setProperty('--wolmart-' + id + '-text-transform', typo['text-transform']);
        } else {
            style.removeProperty('--wolmart-' + id + '-text-transform');
        }
        if (typo['color'] && '' != typo['color']) {
            style.setProperty('--wolmart-' + id + '-color', typo['color']);
        } else {
            style.removeProperty('--wolmart-' + id + '-color');
        }
    }

    function getHSL(color) {
        color = Number.parseInt(color.slice(1), 16);
        var $blue = color % 256;
        color /= 256;
        var $green = color % 256;
        var $red = color = color / 256;

        var $min = Math.min($red, $green, $blue);
        var $max = Math.max($red, $green, $blue);

        var $l = $min + $max;
        var $d = Number($max - $min);
        var $h = 0;
        var $s = 0;

        if ($d) {
            if ($l < 255) {
                $s = $d / $l;
            } else {
                $s = $d / (510 - $l);
            }

            if ($red == $max) {
                $h = 60 * ($green - $blue) / $d;
            } else if ($green == $max) {
                $h = 60 * ($blue - $red) / $d + 120;
            } else if ($blue == $max) {
                $h = 60 * ($red - $green) / $d + 240;
            }
        }

        return [($h + 360) % 360, ($s * 100), ($l / 5.1 + 7)];
    }

    function getLighten(color) {
        var hsl = getHSL(color);
        return 'hsl(' + hsl[0] + ', ' + hsl[1] + '%, ' + hsl[2] + '%)';
    }

    function getColorA(color, opacity) {
        var hsl = getHSL(color);
        return 'hsl(' + hsl[0] + ', ' + hsl[1] + '%, ' + hsl[2] + '%, ' + opacity + ')';
    }
})