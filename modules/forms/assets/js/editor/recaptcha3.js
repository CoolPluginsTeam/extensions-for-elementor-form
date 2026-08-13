const createRecaptchaEditorModule = require('./recaptcha-editor-factory');

module.exports = createRecaptchaEditorModule({
	configKey: 'recaptcha_v3',
	fieldType: 'recaptcha_v3',
	filterHook: 'cool_formkit/forms/content_template/field/recaptcha_v3',
	setupMessage:
		'To use reCAPTCHA V3, you need to add the API Key and complete the setup process in Dashboard > Elementor > Cool FormKit Lite > Settings > reCAPTCHA V3.',
	buildDataAttrs: function (item, formConfig) {
		let recaptchaData = 'data-sitekey="' + formConfig.site_key + '" data-type="' + formConfig.type + '"';
		recaptchaData += ' data-action="Form"';
		recaptchaData += ' data-badge="' + item.recaptcha_badge + '"';
		recaptchaData += ' data-size="invisible"';
		return recaptchaData;
	},
});
