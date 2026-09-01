const createRecaptchaEditorModule = require('./recaptcha-editor-factory');

module.exports = createRecaptchaEditorModule({
	configKey: 'recaptcha',
	fieldType: 'recaptcha',
	filterHook: 'cool_formkit/forms/content_template/field/recaptcha',
	setupMessage:
		'To use reCAPTCHA, you need to add the API Key and complete the setup process in Dashboard > Elementor > Cool FormKit Lite > Settings > reCAPTCHA.',
	buildDataAttrs: function (item, formConfig) {
		let recaptchaData = 'data-sitekey="' + formConfig.site_key + '" data-type="' + formConfig.type + '"';
		recaptchaData += ' data-theme="' + item.recaptcha_style + '"';
		recaptchaData += ' data-size="' + item.recaptcha_size + '"';
		return recaptchaData;
	},
});
