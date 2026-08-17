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
})(jQuery, window);
