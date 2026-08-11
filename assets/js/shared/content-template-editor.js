(function ($, window) {
	'use strict';

	window.CFKEF = window.CFKEF || {};

	CFKEF.initCountryCodeEditor = function (filterHook) {
		const InputTelFieldRender = (inputField, items, index) => {
			const isTelField =
				items.hasOwnProperty('ccfef-country-code-field') &&
				'yes' === items['ccfef-country-code-field'] &&
				('tel' === items.field_type || 'ehp-tel' === items.field_type);

			if (!isTelField) {
				return inputField;
			}

			const fieldId = items._id;
			let includeCountries = '';
			let excludeCountries = '';
			let defaultCountry = '';
			const dialCodeVisibility = items['ccfef-dial-code-visibility'];
			const countryStrictMode = items['ccfef-strict-mode'];

			if (items.hasOwnProperty('ccfef-country-code-exclude')) {
				excludeCountries = items['ccfef-country-code-exclude'].replace(/[^0-9a-zA-Z,\- ]/g, '');
			}
			if (items.hasOwnProperty('ccfef-country-code-include')) {
				includeCountries = items['ccfef-country-code-include'].replace(/[^0-9a-zA-Z,\- ]/g, '');
			}
			if (items.hasOwnProperty('ccfef-country-code-default')) {
				const def = items['ccfef-country-code-default'];
				defaultCountry = /[^a-zA-Z]/.test(def) ? 'NAN' : def;
			}

			const includeArrayOrig = includeCountries
				? includeCountries.split(',').map((item) => item.trim()).filter(Boolean)
				: [];
			const excludeArrayOrig = excludeCountries
				? excludeCountries.split(',').map((item) => item.trim()).filter(Boolean)
				: [];
			const sortedIncludeOrig = [...includeArrayOrig].sort();
			const sortedExcludeOrig = [...excludeArrayOrig].sort();
			const isSame =
				sortedIncludeOrig.length === sortedExcludeOrig.length &&
				sortedIncludeOrig.every((v, i) => v === sortedExcludeOrig[i]);
			const commonAttr = isSame ? 'same' : '';
			const trimmedInclude = includeCountries
				? includeCountries.split(',').map((item) => item.trim()).filter(Boolean).join(',')
				: '';

			return `${inputField}<span class="ccfef-editor-intl-input"
                data-id="form_field_${index}"
                data-field-id="${fieldId}"
                data-default-country="${defaultCountry}"
                data-exclude-countries="${excludeCountries}"
                data-include-countries="${trimmedInclude}"
                data-common-countries="${commonAttr}"
                data-dial-code-visibility="${dialCodeVisibility}"
                data-strict-mode="${countryStrictMode}"
                style="display: none;"></span>`;
		};

		elementor.hooks.addFilter(filterHook, InputTelFieldRender, 10, 4);
	};

	CFKEF.initSelect2Editor = function (filterHook, options) {
		const config = options || {};
		const multiSelectLimitKey = config.multiSelectLimitKey || 'select_size';
		const isMultiSelectEnabled = config.isMultiSelectEnabled || function (field) {
			return field.hasOwnProperty('allow_multiple') && field.allow_multiple === 'true';
		};

		elementor.hooks.addFilter(filterHook, function (field, item, form) {
			if (field.field_type === 'select' && field['select-field-control'] === 'yes') {
				let multiSelect = '';
				let multiSelectLimit = '';
				const borderColor = form['field_border_color'] ? form['field_border_color'] : '#646060';
				const borderWidth = form['field_border_width']['top'] ? form['field_border_width']['top'] : '1';
				const borderUnit = form['field_border_width']['unit'] ? form['field_border_width']['unit'] : 'px';

				if (isMultiSelectEnabled(field)) {
					multiSelect = field['allow_multiple'];
					multiSelectLimit = field[multiSelectLimitKey] ? field[multiSelectLimitKey] : '';
				}

				const selectFieldControl = field['select-field-control'] ? field['select-field-control'] : '';
				const placeholder = field['select-field-placeholder'] ? field['select-field-placeholder'] : '';
				const customId = field['custom_id'] ? field['custom_id'] : '';
				const uniqueId = field['_id'] ? field['_id'] : '';
				const fieldType = field['field_type'] ? field['field_type'] : '';

				field.css_classes += 'select_data_js';
				field.css_classes += ' ';
				field.css_classes += `cfefuniqueId_${uniqueId} cfefselectFieldControl_${selectFieldControl} cfefmultiSelect_${multiSelect} cfefmultiSelectLimit_${multiSelectLimit} cfefcustomId_${customId} cfeffieldType_${fieldType} cfefplaceholder_${placeholder} cfefborderColor_${borderColor} cfefborderWidth_${borderWidth} cfefborderUnit_${borderUnit}`;
			}

			return field;
		}, 10, 4);
	};

	CFKEF.initCheckboxRadioEditor = function (filterHook) {
		elementor.hooks.addFilter(filterHook, function (field) {
			if ((field.field_type === 'radio' || field.field_type === 'checkbox') && field['cr-style'] !== 'cfkef-default') {
				const style = field['cr-style'] ? field['cr-style'] : '';
				const size = field['cr-style-size']['size'] ? field['cr-style-size']['size'] : '';
				const styleColor = field['cr-style-color'] ? field['cr-style-color'] : '';
				const styleDefaultColor = field['cr-style-default-color'] ? field['cr-style-default-color'] : '';
				const customId = field['custom_id'] ? field['custom_id'] : '';
				const uniqueId = field['_id'] ? field['_id'] : '';
				const fieldType = field['field_type'] ? field['field_type'] : '';

				field.css_classes += 'editor_data_js';
				field.css_classes += ' ';
				field.css_classes += `cfefuniqueId_${uniqueId} cfefstyle_${style} cfefsize_${size} cfefstyleColor_${styleColor} cfefcustomId_${customId} cfeffieldType_${fieldType} cfefstyleDefaultColor_${styleDefaultColor}`;
			}

			return field;
		}, 10, 4);
	};

	CFKEF.initRestrictDateEditor = function (filterHook) {
		elementor.hooks.addFilter(filterHook, function (field) {
			if (field.field_type === 'date') {
				const minFieldId = field['link_min_fieldId'] ? field['link_min_fieldId'] : '';
				const maxFieldId = field['link_max_fieldId'] ? field['link_max_fieldId'] : '';
				const fieldId = field['_id'] ? field['_id'] : '';
				const customId = field['custom_id'] ? field['custom_id'] : '';

				if (!field.attributes) {
					field.attributes = {};
				}

				if (minFieldId) {
					field.attributes['ccfef_link_min_fieldId'] = minFieldId;
				}
				if (maxFieldId) {
					field.attributes['ccfef_link_max_fieldId'] = maxFieldId;
				}
				if (fieldId) {
					field.attributes['ccfef_fieldId'] = fieldId;
				}
				if (customId) {
					field.attributes['ccfef_customId'] = customId;
				}

				field.attributes += ` ccfef_link_min_fieldId_"${minFieldId}" ccfef_link_max_fieldId_"${maxFieldId}"`;
				field.css_classes += ` ccfef_link_min_fieldId_"${minFieldId}" ccfef_link_max_fieldId_"${maxFieldId}" ccfef_fieldId_"${fieldId}" ccfef_customId_"${customId}" `;
			}

			return field;
		}, 10, 4);
	};
})(jQuery, window);
