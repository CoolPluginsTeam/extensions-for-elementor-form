"use strict";
(globalThis["webpackChunkcool_formkit"] = globalThis["webpackChunkcool_formkit"] || []).push([["js/cool-form-lite"],{

/***/ "./modules/forms/assets/js/frontend/handlers/form-redirect.js":
/*!********************************************************************!*\
  !*** ./modules/forms/assets/js/frontend/handlers/form-redirect.js ***!
  \********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (elementorModules.frontend.handlers.Base.extend({
  getDefaultSettings() {
    return {
      selectors: {
        form: '.cool-form'
      }
    };
  },
  getDefaultElements() {
    var selectors = this.getSettings('selectors'),
      elements = {};
    elements.$form = this.$element.find(selectors.form);
    return elements;
  },
  bindEvents() {
    this.elements.$form.on('form_destruct', this.handleSubmit);
  },
  handleSubmit(event, response) {
    if ('undefined' !== typeof response.data.redirect_url) {
      location.href = response.data.redirect_url;
    }
  }
}));

/***/ }),

/***/ "./modules/forms/assets/js/frontend/handlers/form-sender.js":
/*!******************************************************************!*\
  !*** ./modules/forms/assets/js/frontend/handlers/form-sender.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (elementorModules.frontend.handlers.Base.extend({
  getDefaultSettings() {
    return {
      selectors: {
        form: '.cool-form',
        submitButton: '[type="submit"]'
      },
      action: 'coolformkit_forms_send_form',
      ajaxUrl: elementorFrontendConfig.urls.ajaxurl,
      nonce: coolFormsData.nonce
    };
  },
  getDefaultElements() {
    const selectors = this.getSettings('selectors'),
      elements = {};
    elements.$form = this.$element.find(selectors.form);
    elements.$submitButton = elements.$form.find(selectors.submitButton);
    return elements;
  },
  bindEvents() {
    this.elements.$form.on('submit', this.handleSubmit);
  },
  beforeSend() {
    const $form = this.elements.$form;
    $form.animate({
      opacity: '0.45'
    }, 500).addClass('elementor-form-waiting');
    $form.find('.elementor-message').remove();
    $form.find('.elementor-error').removeClass('elementor-error');
    $form.find('div.elementor-field-group').removeClass('error').find('span.elementor-form-help-inline').remove().end().find(':input').attr('aria-invalid', 'false');
    this.elements.$submitButton.attr('disabled', 'disabled').find('> span').prepend('<span class="elementor-button-text elementor-form-spinner"><i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
  },
  getFormData() {
    const formData = new FormData(this.elements.$form[0]);
    formData.append('action', this.getSettings('action'));
    formData.append('nonce', this.getSettings('nonce'));
    formData.append('referrer', location.toString());
    return formData;
  },
  onSuccess(response) {
    const $form = this.elements.$form;
    this.elements.$submitButton.removeAttr('disabled').find('.elementor-form-spinner').remove();
    $form.animate({
      opacity: '1'
    }, 100).removeClass('elementor-form-waiting');
    if (!response.success) {
      if (response.data.errors) {
        jQuery.each(response.data.errors, function (key, title) {
          const $inputField = $form.find('#form-field-' + key);
          if ($inputField.length) {
            const $parent = $inputField.parent();
            $parent.addClass('elementor-error');
            $parent.find('.elementor-message-danger').remove();
            $inputField.after('<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">' + title + '</span>');
            $inputField.attr('aria-invalid', 'true');
          }
        });
        $form.trigger('error');
      }
      $form.append('<div class="elementor-message elementor-message-danger" role="alert"></div>');
      $form.find('.elementor-message-danger')[$form.find('.elementor-message-danger').length - 1].textContent = response.data.message;
    } else {
      $form.trigger('submit_success', response.data);

      // For actions like redirect page
      $form.trigger('form_destruct', response.data);
      $form.trigger('reset');
      let successClass = 'elementor-message elementor-message-success';
      if (elementorFrontendConfig.experimentalFeatures.e_font_icon_svg) {
        successClass += ' elementor-message-svg';
      }
      if ('undefined' !== typeof response.data.message && '' !== response.data.message) {
        $form.append('<div class="' + successClass + '" role="alert"></div>');
        $form.find('.elementor-message-success').text(response.data.message);
      }
    }
  },
  onError(xhr, desc) {
    const $form = this.elements.$form;
    $form.append('<div class="elementor-message elementor-message-danger" role="alert"></div>');
    $form.find('.elementor-message-danger').text(desc);
    this.elements.$submitButton.html(this.elements.$submitButton.text()).removeAttr('disabled');
    $form.animate({
      opacity: '1'
    }, 100).removeClass('elementor-form-waiting');
    $form.trigger('error');
  },
  handleSubmit(event) {
    const self = this,
      $form = this.elements.$form;
    event.preventDefault();
    if ($form.hasClass('elementor-form-waiting')) {
      return false;
    }
    this.beforeSend();
    jQuery.ajax({
      url: self.getSettings('ajaxUrl'),
      type: 'POST',
      dataType: 'json',
      data: self.getFormData(),
      processData: false,
      contentType: false,
      success: self.onSuccess,
      error: self.onError
    });
  }
}));

/***/ })

}]);
//# sourceMappingURL=cool-form-lite.js.map?ver=1464a49bbdaeea909ccb