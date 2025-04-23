module.exports = elementorModules.editor.utils.Module.extend( {


    enqueueRecaptchaJs(url) {
        if (!elementorFrontend.elements.$body.find('[src="' + url + '"]').length) {
          elementorFrontend.elements.$body.append('<scr' + 'ipt src="' + url + '"></scri' + 'pt>');
        }
      },
      renderField(inputField, item) {
    
    
        inputField += '<div class="cool-form-field ' + item.field_type + '">';
        inputField += this.getDataSettings(item);
        inputField += '</div>';
        return inputField;
      },
      getDataSettings(item) {
    
        const config = elementor.config.forms.cloudflare_recaptcha;
        const srcURL = 'https://challenges.cloudflare.com/turnstile/v0/api.js?render=explicit';
        if (!config.enabled) {
          console.log('reCAPTCHA is not enabled');
          return '<div class="elementor-alert elementor-alert-info"> To use reCAPTCHA, you need to add the API Key and complete the setup process in Dashboard > Elementor > Cool FormKit Lite > Settings > reCAPTCHA. </div>';
        }
        let recaptchaData;
        if (item.field_type == "cloudflare_recaptcha") {
          recaptchaData = 'data-sitekey="' + config.sitekey + '"';
          recaptchaData += ' data-theme="' + item.recaptcha_style + '"';
          recaptchaData += ' data-size="' + item.recaptcha_size + '"';
          recaptchaData += ' data-language="' + item.cloudflare_recaptcha_language + '"';
        }
        this.enqueueRecaptchaJs(srcURL);
        return '<div class="cool-form-cloudflare-recaptcha" ' + recaptchaData + '></div>';
      },
      filterItem(item) {
        if ('cloudflare_recaptcha' === item.field_type) {
          item.field_label = false;
        }
        return item;
      },
      onInit() {
        elementor.hooks.addFilter('cool_formkit/forms/content_template/item', this.filterItem);
        elementor.hooks.addFilter("cool_formkit/forms/content_template/field/cloudflare_recaptcha", this.renderField, 10, 4);
      }

})