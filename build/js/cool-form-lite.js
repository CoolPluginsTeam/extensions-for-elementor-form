<<<<<<< HEAD
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
    this.elements.$submitButton.attr('disabled', 'disabled').find('span.cool-form-button-icon').prepend('<span class="elementor-button-text elementor-form-spinner"><i class="fa fa-spinner fa-spin"></i>&nbsp;</span>');
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
//# sourceMappingURL=cool-form-lite.js.map?ver=3d6061e65a318f939a7b
=======
"use strict";(globalThis.webpackChunkcool_formkit=globalThis.webpackChunkcool_formkit||[]).push([[222],{719:(e,t,r)=>{r.r(t),r.d(t,{default:()=>n});const n=elementorModules.frontend.handlers.Base.extend({getDefaultSettings:()=>({selectors:{form:".cool-form"}}),getDefaultElements(){var e=this.getSettings("selectors"),t={};return t.$form=this.$element.find(e.form),t},bindEvents(){this.elements.$form.on("form_destruct",this.handleSubmit)},handleSubmit(e,t){void 0!==t.data.redirect_url&&(location.href=t.data.redirect_url)}})},746:(e,t,r)=>{r.r(t),r.d(t,{default:()=>n});const n=elementorModules.frontend.handlers.Base.extend({getDefaultSettings:()=>({selectors:{form:".cool-form",submitButton:'[type="submit"]'},action:"coolformkit_forms_send_form",ajaxUrl:elementorFrontendConfig.urls.ajaxurl,nonce:coolFormsData.nonce}),getDefaultElements(){const e=this.getSettings("selectors"),t={};return t.$form=this.$element.find(e.form),t.$submitButton=t.$form.find(e.submitButton),t},bindEvents(){this.elements.$form.on("submit",this.handleSubmit)},beforeSend(){const e=this.elements.$form;e.animate({opacity:"0.45"},500).addClass("elementor-form-waiting"),e.find(".elementor-message").remove(),e.find(".elementor-error").removeClass("elementor-error"),e.find("div.elementor-field-group").removeClass("error").find("span.elementor-form-help-inline").remove().end().find(":input").attr("aria-invalid","false"),this.elements.$submitButton.attr("disabled","disabled").find("> span").prepend('<span class="elementor-button-text elementor-form-spinner"><i class="fa fa-spinner fa-spin"></i>&nbsp;</span>')},getFormData(){const e=new FormData(this.elements.$form[0]);return e.append("action",this.getSettings("action")),e.append("nonce",this.getSettings("nonce")),e.append("referrer",location.toString()),e},onSuccess(e){const t=this.elements.$form;if(this.elements.$submitButton.removeAttr("disabled").find(".elementor-form-spinner").remove(),t.animate({opacity:"1"},100).removeClass("elementor-form-waiting"),e.success){t.trigger("submit_success",e.data),t.trigger("form_destruct",e.data),t.trigger("reset");let r="elementor-message elementor-message-success";elementorFrontendConfig.experimentalFeatures.e_font_icon_svg&&(r+=" elementor-message-svg"),void 0!==e.data.message&&""!==e.data.message&&(t.append('<div class="'+r+'" role="alert"></div>'),t.find(".elementor-message-success").text(e.data.message))}else e.data.errors&&(jQuery.each(e.data.errors,(function(e,r){const n=t.find("#form-field-"+e);if(n.length){const e=n.parent();e.addClass("elementor-error"),e.find(".elementor-message-danger").remove(),n.after('<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">'+r+"</span>"),n.attr("aria-invalid","true")}})),t.trigger("error")),t.append('<div class="elementor-message elementor-message-danger" role="alert"></div>'),t.find(".elementor-message-danger")[t.find(".elementor-message-danger").length-1].textContent=e.data.message},onError(e,t){const r=this.elements.$form;r.append('<div class="elementor-message elementor-message-danger" role="alert"></div>'),r.find(".elementor-message-danger").text(t),this.elements.$submitButton.html(this.elements.$submitButton.text()).removeAttr("disabled"),r.animate({opacity:"1"},100).removeClass("elementor-form-waiting"),r.trigger("error")},handleSubmit(e){const t=this,r=this.elements.$form;if(e.preventDefault(),r.hasClass("elementor-form-waiting"))return!1;this.beforeSend(),jQuery.ajax({url:t.getSettings("ajaxUrl"),type:"POST",dataType:"json",data:t.getFormData(),processData:!1,contentType:!1,success:t.onSuccess,error:t.onError})}})}}]);
>>>>>>> b5ffa067160f2557e7cd0c249972f9d51260c3cc
