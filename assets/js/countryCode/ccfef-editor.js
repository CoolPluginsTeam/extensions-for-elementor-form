jQuery(document).ready(function ($) {
    const InputTelFieldRender = (inputFiled,items,index,settings) => {
        if(items.hasOwnProperty('ccfef-country-code-field') && 'yes' === items['ccfef-country-code-field'] && 'tel' === items.field_type){
            const fieldId=items._id;
            let includeCountries='';    
            let excludeCountries='';
            let preferredCountries ='';
			let autoDetectCountry= '';
            let defaultCountry='';
            if(items.hasOwnProperty('ccfef-country-code-exclude')){
                excludeCountries = items['ccfef-country-code-exclude'].replace(/[^0-9a-zA-Z,\- ]/g, '');
            }
            if(items.hasOwnProperty('ccfef-country-code-include')){
                includeCountries=items['ccfef-country-code-include'].replace(/[^0-9a-zA-Z,\- ]/g, '');
            }
            if(items.hasOwnProperty('ccfef-country-code-prefer')){
                preferredCountries=items['ccfef-country-code-prefer'].replace(/[^0-9a-zA-Z,\- ]/g, '');
            }
            if(items.hasOwnProperty('ccfef-country-code-auto-detect')){
                autoDetectCountry=items['ccfef-country-code-auto-detect'].replace(/[^0-9a-zA-Z,\- ]/g, '');
            }
            if(items.hasOwnProperty('ccfef-country-code-default')){
                defaultCountry=items['ccfef-country-code-default'].replace(/[^0-9a-zA-Z,\- ]/g, '');
            }
    
            return `${inputFiled}<span class="ccfef-editor-intl-input" data-id="form_field_${index}" data-field-id="${fieldId}" data-exclude-countries="${excludeCountries}" data-include-countries="${includeCountries}" data-preferred-countries="${preferredCountries}" data-auto-detect="${autoDetectCountry}" data-default-country="${defaultCountry}" style="display: none;" ></span>`;
        }else{
            return inputFiled;
        }
    }
    elementor.hooks.addFilter('cool_formkit/forms/content_template/field/tel', InputTelFieldRender, 10, 4);
});