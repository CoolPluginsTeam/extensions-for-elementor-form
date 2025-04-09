jQuery(document).ready(function ($) {
    const InputTelFieldRender = (inputField, items, index, settings) => {
        // Check if the field is a tel field and the country code option is enabled.
        if (
            items.hasOwnProperty('ccfef-country-code-field') &&
            'yes' === items['ccfef-country-code-field'] &&
            'tel' === items.field_type
        ) {
            const fieldId = items._id;
            let includeCountries = '';
            let excludeCountries = '';
            let defaultCountry = '';

            // Get and sanitize the exclude countries list.
            if (items.hasOwnProperty('ccfef-country-code-exclude')) {
                excludeCountries = items['ccfef-country-code-exclude'].replace(/[^0-9a-zA-Z,\- ]/g, '');
            }
            // Get and sanitize the include countries list.
            if (items.hasOwnProperty('ccfef-country-code-include')) {
                includeCountries = items['ccfef-country-code-include'].replace(/[^0-9a-zA-Z,\- ]/g, '');
            }
            // Get and sanitize the default country.
            if (items.hasOwnProperty('ccfef-country-code-default')) {
                const def = items['ccfef-country-code-default'];
                // If the default contains any characters other than letters, set it to 'NAN'
                if (/[^a-zA-Z]/.test(def)) {
                    defaultCountry = 'NAN';
                } else {
                    defaultCountry = def;
                }
            }
            
            
            // --- Added code to set data-common-countries ---
            // Create arrays by splitting the comma-separated strings and trimming each value.
            const includeArrayOrig = includeCountries ? includeCountries.split(',').map(item => item.trim()).filter(Boolean) : [];
            const excludeArrayOrig = excludeCountries ? excludeCountries.split(',').map(item => item.trim()).filter(Boolean) : [];
            // Sort the arrays so they can be directly compared.
            const sortedIncludeOrig = [...includeArrayOrig].sort();
            const sortedExcludeOrig = [...excludeArrayOrig].sort();
            // Check if both sorted arrays are identical.
            const isSame = sortedIncludeOrig.length === sortedExcludeOrig.length &&
                           sortedIncludeOrig.every((v, i) => v === sortedExcludeOrig[i]);
            const commonAttr = isSame ? "same" : "";
            // --- End of added code ---

            // For include countries, mimic the PHP behavior: simply trim and reassemble without filtering out excluded countries.
            const trimmedInclude = includeCountries ? includeCountries.split(',').map(item => item.trim()).filter(Boolean).join(',') : '';

            // Build the output HTML for the hidden span with all data attributes.
            return `${inputField}<span class="ccfef-editor-intl-input" data-id="form_field_${index}" data-field-id="${fieldId}" data-default-country="${defaultCountry}" data-exclude-countries="${excludeCountries}" data-include-countries="${trimmedInclude}" data-common-countries="${commonAttr}" style="display: none;"></span>`;
        } else {
            return inputField;
        }
    };

    // Add the filter for rendering telephone fields in Elementor forms.
    elementor.hooks.addFilter('cool_formkit/forms/content_template/field/tel', InputTelFieldRender, 10, 4);
});
