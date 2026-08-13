/**
 * Shared Elementor editor helpers for conditional field label UI.
 */
(function ($, window) {
	'use strict';

	window.CFKEF = window.CFKEF || {};

	/**
	 * Wire conditions-tab button text and show/hide/enable/disable labels.
	 *
	 * @param {Object} [opts]
	 * @param {Object} [opts.modes] Map of mode value => { repeater, title }
	 * @param {string} [opts.addButtonText]
	 * @param {boolean} [opts.hideRawHtml]
	 * @param {Function} [opts.onAfterInit] (mainWrp, $) => void
	 */
	CFKEF.initConditionalEditorLabels = function (opts) {
		opts = opts || {};
		var modes = opts.modes || {
			hide: { repeater: 'Hide fields if', title: 'Hide Fields' },
			show: { repeater: 'Show fields if', title: 'Show Fields' },
		};
		var addButtonText = opts.addButtonText || '+ Add Conditions';
		var hideRawHtml = !!opts.hideRawHtml;
		var onAfterInit = typeof opts.onAfterInit === 'function' ? opts.onAfterInit : null;

		$(document).ready(function () {
			$(document).on('click', '.elementor-control-form_fields_conditions_tab', function (e) {
				var mainWrp = $(e.currentTarget).closest('.elementor-repeater-row-controls.editable');
				if (!mainWrp.length) {
					return;
				}

				var addBtn = mainWrp.find('.elementor-control-cfef_repeater_data .elementor-repeater-add');
				addBtn.text(addButtonText);

				if (hideRawHtml) {
					mainWrp.find('.cfef_custom_html').each(function () {
						this.style.display = 'none';
					});
				}

				var logicMode = mainWrp.find(
					'.elementor-control-cfef_logic_mode .elementor-control-content .elementor-control-field .elementor-control-input-wrapper .elementor-choices input'
				);
				var controllerTitle = mainWrp.find(
					'.elementor-control-cfef_logic_mode .elementor-control-content .elementor-control-field .elementor-control-title'
				);
				var showHideFieldLabelText = mainWrp.find(
					'.elementor-control-cfef_repeater_data .elementor-control-content label span'
				);

				function updateLabel() {
					for (var i = 0; i < logicMode.length; i++) {
						if (!logicMode[i].checked) {
							continue;
						}
						var mode = modes[logicMode[i].value];
						if (!mode) {
							break;
						}
						if (showHideFieldLabelText[0]) {
							showHideFieldLabelText[0].textContent = mode.repeater;
						}
						if (controllerTitle[0] && mode.title) {
							controllerTitle[0].textContent = mode.title;
						}
						break;
					}
				}

				updateLabel();
				logicMode.on('change', updateLabel);

				if (onAfterInit) {
					onAfterInit(mainWrp, $);
				}
			});
		});
	};

	/**
	 * Bind Cool Form submit-button condition label updates (Show/Hide/Enable/Disable).
	 *
	 * @param {Object} [opts]
	 * @param {Object} [opts.modes] Map of mode => { repeater, title }
	 * @param {string} [opts.triggerSelector]
	 */
	CFKEF.initConditionalSubmitEditorLabels = function (opts) {
		opts = opts || {};
		var modes = opts.modes || {
			show: { repeater: 'Show If', title: 'Show button' },
			hide: { repeater: 'Hide If', title: 'Hide button' },
			enable: { repeater: 'Enable If', title: 'Enable button' },
			disable: { repeater: 'Disable If', title: 'Disable button' },
		};
		var triggerSelector = opts.triggerSelector || '.elementor-control-cfef_logic_cfefp_submit';

		$(document).ready(function () {
			$(document).on('mouseenter', triggerSelector, function () {
				var mainWprSubButton = $(triggerSelector);
				var wrpNext = mainWprSubButton.next();
				var logicMode = wrpNext.find(
					'.elementor-control-content .elementor-control-field .elementor-control-input-wrapper .elementor-choices input'
				);
				var labelWrapper = wrpNext.next().next().find('.elementor-control-content label span');
				var controllerTitle = logicMode.prevObject.find(
					'.elementor-control-content .elementor-control-field .elementor-control-title'
				);

				function updateSubmitLabel() {
					for (var i = 0; i < logicMode.length; i++) {
						if (!logicMode[i].checked) {
							continue;
						}
						var mode = modes[logicMode[i].value];
						if (!mode) {
							continue;
						}
						labelWrapper.text(mode.repeater);
						controllerTitle.text(mode.title);
					}
				}

				updateSubmitLabel();
				logicMode.on('change', updateSubmitLabel);
			});
		});
	};

	/**
	 * Bind delegated dynamic-tag UI for conditional field ID pickers.
	 * Cool Form / Hello Plus keep formRepeaterFields-aware binding in global.js.
	 */
	CFKEF.initConditionalDynamicTags = function () {
		$(document).ready(function () {
			$('body').on('click', '.elementor-control-tag-area[data-setting="cfef_logic_field_id"]', function () {
				if ($(this).data('isChecked') === 'ok') {
					return;
				}
				$(this).after(
					'<div class="elementor-control-dynamic-switcher elementor-control-unit-1 cfef-add-tag" data-tooltip="add Tags" original-title=""><i class="eicon-database" aria-hidden="true"></i><span class="elementor-screen-only">Dynamic Tags</span></div>'
				);
				$(this).data('isChecked', 'ok');
			});

			$('body').on('click', '.cfef-add-tag', function () {
				var idList = '<ul class="cfef-dynamic-tag">';
				var formWrapper = $(this).closest('.elementor-repeater-fields-wrapper').parents('.elementor-repeater-fields-wrapper');
				$('.elementor-form-field-shortcode', formWrapper).each(function () {
					var regexPattern = /\".*?\"/gm;
					var matches;
					var fieldName = $(this).val();
					while ((matches = regexPattern.exec(fieldName)) !== null) {
						if (matches.index === regexPattern.lastIndex) {
							regexPattern.lastIndex++;
						}
						matches.forEach(function (match) {
							fieldName = match.replaceAll('"', '');
						});
					}
					$(this).data('actual-id', fieldName);
					idList += '<li title="Field ID" data-field-id="' + fieldName + '">' + fieldName + '</li>';
				});
				idList += '</ul>';
				$(this).closest('.elementor-control-input-wrapper').append(idList);
			});

			$(document).mouseup(function (event) {
				var container = $('.cfef-dynamic-tag');
				if (!container.is(event.target) && container.has(event.target).length === 0) {
					container.hide();
				}
			});

			$('body').on('click', '.cfef-dynamic-tag li', function () {
				var selectedValue = $(this).data('field-id');
				$(this).parent().siblings().val(selectedValue);
				$(this).parent().siblings().trigger('input');
				var publishBtn = $('#elementor-panel-saver-button-publish')[0];
				if (publishBtn && publishBtn.classList.contains('elementor-disabled')) {
					publishBtn.classList.remove('elementor-disabled');
				}
				setTimeout(function () {
					$('.cfef-dynamic-tag').remove();
				}, 500);
			});
		});
	};
})(jQuery, window);
