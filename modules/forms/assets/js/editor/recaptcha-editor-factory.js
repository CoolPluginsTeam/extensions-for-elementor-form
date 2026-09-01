/**
 * Shared Cool Form editor reCAPTCHA field preview module factory.
 *
 * @param {Object} config
 * @param {string} config.configKey elementor.config.forms key (recaptcha | recaptcha_v3)
 * @param {string} config.fieldType Field type to match
 * @param {string} config.filterHook Content-template field filter hook
 * @param {string} [config.setupMessage]
 * @param {Function} [config.buildDataAttrs] (item, config) => string
 */
function createRecaptchaEditorModule(config) {
	return elementorModules.editor.utils.Module.extend({
		enqueueRecaptchaJs: function (url, type) {
			if (!elementorFrontend.elements.$body.find('[src="' + url + '"]').length) {
				elementorFrontend.elements.$body.append(
					'<scr' + 'ipt src="' + url + '" id="recaptcha-' + type + '"></scri' + 'pt>'
				);
			}
		},
		renderField: function (inputField, item) {
			inputField += '<div class="cool-form-field ' + item.field_type + '">';
			inputField += this.getDataSettings(item);
			inputField += '</div>';
			return inputField;
		},
		getDataSettings: function (item) {
			const formConfig = elementor.config.forms[config.configKey];
			const srcURL = 'https://www.google.com/recaptcha/api.js?onload=recaptchaLoaded&render=explicit';
			if (!formConfig.enabled) {
				return (
					'<div class="elementor-alert elementor-alert-info"> ' +
					(config.setupMessage || formConfig.setup_message || '') +
					' </div>'
				);
			}
			let recaptchaData = '';
			if (item.field_type === config.fieldType) {
				recaptchaData = typeof config.buildDataAttrs === 'function'
					? config.buildDataAttrs(item, formConfig)
					: 'data-sitekey="' + formConfig.site_key + '" data-type="' + formConfig.type + '"';
			}
			this.enqueueRecaptchaJs(srcURL, formConfig.type);
			return '<div class="cool-form-recaptcha" ' + recaptchaData + '></div>';
		},
		filterItem: function (item) {
			if (config.fieldType === item.field_type) {
				item.field_label = false;
			}
			return item;
		},
		onInit: function () {
			elementor.hooks.addFilter('cool_formkit/forms/content_template/item', this.filterItem);
			elementor.hooks.addFilter(config.filterHook, this.renderField, 10, 4);
		},
	});
}

module.exports = createRecaptchaEditorModule;
