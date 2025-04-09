/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./modules/forms/assets/js/editor/component.js":
/*!*****************************************************!*\
  !*** ./modules/forms/assets/js/editor/component.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Component)
/* harmony export */ });
/* harmony import */ var _hooks___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./hooks/ */ "./modules/forms/assets/js/editor/hooks/index.js");

class Component extends $e.modules.ComponentBase {
  getNamespace() {
    return 'cool-forms-lite';
  }
  defaultHooks() {
    return this.importHooks(_hooks___WEBPACK_IMPORTED_MODULE_0__);
  }
}

/***/ }),

/***/ "./modules/forms/assets/js/editor/fields-map-control.js":
/*!**************************************************************!*\
  !*** ./modules/forms/assets/js/editor/fields-map-control.js ***!
  \**************************************************************/
/***/ ((module) => {

module.exports = elementor.modules.controls.Repeater.extend({
  onBeforeRender() {
    this.$el.hide();
  },
  updateMap(fields) {
    const self = this,
      savedMapObject = {};
    self.collection.each(function (model) {
      savedMapObject[model.get('remote_id')] = model.get('local_id');
    });
    self.collection.reset();
    fields.forEach(function (field) {
      const model = {
        remote_id: field.remote_id,
        remote_label: field.remote_label,
        remote_type: field.remote_type ? field.remote_type : '',
        remote_required: field.remote_required ? field.remote_required : false,
        local_id: savedMapObject[field.remote_id] ? savedMapObject[field.remote_id] : ''
      };
      self.collection.add(model);
    });
    self.render();
  },
  onRender() {
    elementor.modules.controls.Base.prototype.onRender.apply(this, arguments);
    const self = this;
    self.children.each(function (view) {
      const localFieldsControl = view.children.last(),
        options = {
          '': '- ' + __('None', 'elementor') + ' -'
        };
      let label = view.model.get('remote_label');
      if (view.model.get('remote_required')) {
        label += '<span class="elementor-required">*</span>';
      }
      self.elementSettingsModel.get('form_fields').models.forEach(function (model, index) {
        // If it's an email field, add only email fields from thr form
        const remoteType = view.model.get('remote_type');
        if ('text' !== remoteType && remoteType !== model.get('field_type')) {
          return;
        }
        options[model.get('custom_id')] = model.get('field_label') || 'Field #' + (index + 1);
      });
      localFieldsControl.model.set('label', label);
      localFieldsControl.model.set('options', options);
      localFieldsControl.render();
      view.$el.find('.elementor-repeater-row-tools').hide();
      view.$el.find('.elementor-repeater-row-controls').removeClass('elementor-repeater-row-controls').find('.elementor-control').css({
        paddingBottom: 0
      });
    });
    self.$el.find('.elementor-button-wrapper').remove();
    if (self.children.length) {
      self.$el.show();
    }
  }
});

/***/ }),

/***/ "./modules/forms/assets/js/editor/fields-repeater-control.js":
/*!*******************************************************************!*\
  !*** ./modules/forms/assets/js/editor/fields-repeater-control.js ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _fields_repeater_row__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./fields-repeater-row */ "./modules/forms/assets/js/editor/fields-repeater-row.js");

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (class extends elementor.modules.controls.Repeater {
  className() {
    let classes = super.className();
    classes += ' elementor-control-type-repeater';
    return classes;
  }
  getChildView() {
    return _fields_repeater_row__WEBPACK_IMPORTED_MODULE_0__["default"];
  }
  initialize(...args) {
    super.initialize(...args);
    const formFields = this.container.settings.get('form_fields');
    this.listenTo(formFields, 'change', model => this.onFormFieldChange(model)).listenTo(formFields, 'remove', model => this.onFormFieldRemove(model));
  }
  getFirstChild() {
    return this.children.findByModel(this.collection.models[0]);
  }
  lockFirstStep() {
    const firstChild = this.getFirstChild();
    if ('step' !== firstChild.model.get('field_type')) {
      return;
    }
    const stepFields = this.collection.where({
      field_type: 'step'
    });
    if (1 < stepFields.length) {
      firstChild.toggleFieldTypeControl(false);
      firstChild.toggleTools(false);
    }
    firstChild.toggleSort(false);
  }
  onFormFieldChange(model) {
    const fieldType = model.changed.field_type;
    if (!fieldType || 'step' !== fieldType && 'step' !== model._previousAttributes.field_type) {
      return;
    }
    const isStep = 'step' === fieldType;
    this.children.findByModel(model).toggleStepField(isStep);
    this.onStepFieldChanged(isStep);
  }
  onFormFieldRemove(model) {
    if ('step' === model.get('field_type')) {
      this.onStepFieldChanged(false);
    }
  }
  onStepFieldChanged(isStep) {
    if (isStep) {
      this.lockFirstStep();
      return;
    }
    const stepFields = this.collection.where({
      field_type: 'step'
    });
    if (stepFields.length > 1) {
      return;
    }
    const firstChild = this.getFirstChild();
    if (1 === stepFields.length) {
      firstChild.toggleTools(true);
      firstChild.toggleFieldTypeControl(true);
      return;
    }
    firstChild.toggleSort(true);
  }
  onAddChild(childView) {
    super.onAddChild(childView);
    if ('step' === childView.model.get('field_type')) {
      this.lockFirstStep();
      childView.toggleStepField(true);
    }
  }
});

/***/ }),

/***/ "./modules/forms/assets/js/editor/fields-repeater-row.js":
/*!***************************************************************!*\
  !*** ./modules/forms/assets/js/editor/fields-repeater-row.js ***!
  \***************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (class extends elementor.modules.controls.RepeaterRow {
  toggleFieldTypeControl(show) {
    const fieldTypeModel = this.collection.findWhere({
        name: 'field_type'
      }),
      fieldTypeControl = this.children.findByModel(fieldTypeModel);
    fieldTypeControl.$el.toggle(show);
  }
  toggleStepField(isStep) {
    this.$el.toggleClass('elementor-repeater-row--form-step', isStep);
  }
  toggleTools(show) {
    this.ui.removeButton.add(this.ui.duplicateButton).toggle(show);
  }
});

/***/ }),

/***/ "./modules/forms/assets/js/editor/fields/acceptance.js":
/*!*************************************************************!*\
  !*** ./modules/forms/assets/js/editor/fields/acceptance.js ***!
  \*************************************************************/
/***/ ((module) => {

module.exports = elementorModules.editor.utils.Module.extend({
  renderField(inputField, item, i, settings) {
    var itemClasses = _.escape(item.css_classes),
      required = item.required ? ' required' : '',
      checked = item.checked_by_default ? ' checked="checked"' : '',
      label = '';
    if (item.acceptance_text) {
      label = '<label for="form_field_' + i + '" class="cool-form__field-label">' + _.escape(item.acceptance_text) + '</label>';
    }
    return `
		<div class="mdc-form-field">
		  <div class="mdc-checkbox">
			<input size="1" type="checkbox" ${checked}
			  class="mdc-checkbox__native-control elementor-size-${settings.input_size} ${itemClasses}"
			  name="form_field_${i}" id="form_field_${i}" ${required}>
			<div class="mdc-checkbox__background">
			  <svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
				<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"></path>
			  </svg>
			  <div class="mdc-checkbox__mixedmark"></div>
			</div>
		  </div>
		  ${label}
		</div>
	  `;
  },
  onInit() {
    elementor.hooks.addFilter('cool_formkit/forms/content_template/field/acceptance', this.renderField, 10, 4);
  }
});

/***/ }),

/***/ "./modules/forms/assets/js/editor/fields/date.js":
/*!*******************************************************!*\
  !*** ./modules/forms/assets/js/editor/fields/date.js ***!
  \*******************************************************/
/***/ ((module) => {

module.exports = elementorModules.editor.utils.Module.extend({
  renderField(inputField, item, i, settings) {
    var itemClasses = _.escape(item.css_classes),
      required = item.required ? ' required' : '',
      min = item.min_date ? ' min="' + item.min_date + '"' : '',
      max = item.max_date ? ' max="' + item.max_date + '"' : '',
      placeholder = item.placeholder ? ' placeholder="' + _.escape(item.placeholder) + '"' : '';
    if ('yes' === item.use_native_date) {
      itemClasses += ' cool-form-use-native';
    }
    return `
    <label class="cool-form-text mdc-text-field mdc-text-field--outlined ${item.field_label === '' || !settings.show_labels ? 'mdc-text-field--no-label' : ''} cool-field-size-${settings.input_size}">
      <span class="mdc-notched-outline">
        <span class="mdc-notched-outline__leading"></span>
        <span class="mdc-notched-outline__notch">
          ${item.field_label !== '' && settings.show_labels ? `<span class="mdc-floating-label" id="date-label-${i}">${_.escape(item.field_label)}</span>` : ''}
        </span>
        <span class="mdc-notched-outline__trailing"></span>
      </span>
      <input type="date" size="1" ${min} ${max} ${placeholder}
        pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
        class="mdc-text-field__input cool-form__field cool-form-date-field elementor-field elementor-size-${settings.input_size} ${itemClasses}"
        name="form_field_${i}" id="form_field_${i}" ${required} >
    </label>
  `;
  },
  onInit() {
    elementor.hooks.addFilter('cool_formkit/forms/content_template/field/date', this.renderField, 10, 4);
  }
});

/***/ }),

/***/ "./modules/forms/assets/js/editor/fields/tel.js":
/*!******************************************************!*\
  !*** ./modules/forms/assets/js/editor/fields/tel.js ***!
  \******************************************************/
/***/ ((module) => {

module.exports = elementorModules.editor.utils.Module.extend({
  renderField(inputField, item, i, settings) {
    var itemClasses = _.escape(item.css_classes),
      required = item.required ? ' required' : '',
      placeholder = item.placeholder ? ' placeholder="' + _.escape(item.placeholder) + '"' : '';

    // You can adjust the pattern if needed. Here we use a sample pattern.
    var pattern = ' pattern="[0-9()-]+"';
    return `
			<label class="cool-form-text mdc-text-field mdc-text-field--outlined ${item.field_label === '' || !settings.show_labels ? 'mdc-text-field--no-label' : ''} cool-field-size-${settings.input_size}">
				<span class="mdc-notched-outline">
					<span class="mdc-notched-outline__leading"></span>
					<span class="mdc-notched-outline__notch">
						${item.field_label !== '' && settings.show_labels ? `<span class="mdc-floating-label" id="tel-label-${i}">${_.escape(item.field_label)}</span>` : ''}
					</span>
					<span class="mdc-notched-outline__trailing"></span>
				</span>
				<input 
					type="${item.field_type}" 
					size="1" 
					${placeholder} ${pattern}
					class="mdc-text-field__input cool-form__field elementor-field elementor-size-${settings.input_size} ${itemClasses}"
					name="form_field_${i}" 
					id="form_field_${i}" 
					${required}
				>
				<i aria-hidden="true" class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing cool-tel-error-icon" style="display:none">error</i>
			</label>
			<div class="mdc-text-field-helper-line">
				<div class="mdc-text-field-helper-text" id="cool-tel-error" aria-hidden="true"></div>
			</div>
		`;
  },
  onInit() {
    elementor.hooks.addFilter('cool_formkit/forms/content_template/field/tel', this.renderField, 10, 4);
  }
});

/***/ }),

/***/ "./modules/forms/assets/js/editor/fields/time.js":
/*!*******************************************************!*\
  !*** ./modules/forms/assets/js/editor/fields/time.js ***!
  \*******************************************************/
/***/ ((module) => {

module.exports = elementorModules.editor.utils.Module.extend({
  renderField(inputField, item, i, settings) {
    var itemClasses = _.escape(item.css_classes),
      required = item.required ? ' required' : '',
      placeholder = item.placeholder ? ' placeholder="' + _.escape(item.placeholder) + '"' : '';
    if ('yes' === item.use_native_time) {
      itemClasses += ' cool-form-use-native';
    }
    return `
    <label class="cool-form-text mdc-text-field mdc-text-field--outlined ${item.field_label === '' || !settings.show_labels ? 'mdc-text-field--no-label' : ''} cool-field-size-${settings.input_size}">
      <span class="mdc-notched-outline">
        <span class="mdc-notched-outline__leading"></span>
        <span class="mdc-notched-outline__notch">
          ${item.field_label !== '' && settings.show_labels ? `<span class="mdc-floating-label" id="time-label-${i}">${_.escape(item.field_label)}</span>` : ''}
        </span>
        <span class="mdc-notched-outline__trailing"></span>
      </span>
      <input type="time" size="1" ${placeholder}
        class="mdc-text-field__input cool-form__field cool-form-time-field elementor-field elementor-size-${settings.input_size} ${itemClasses}"
        name="form_field_${i}" id="form_field_${i}" ${required} >
    </label>
  `;
  },
  onInit() {
    elementor.hooks.addFilter('cool_formkit/forms/content_template/field/time', this.renderField, 10, 4);
  }
});

/***/ }),

/***/ "./modules/forms/assets/js/editor/hooks/data/form-fields-sanitize-custom-id.js":
/*!*************************************************************************************!*\
  !*** ./modules/forms/assets/js/editor/hooks/data/form-fields-sanitize-custom-id.js ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FormFieldsSanitizeCustomId: () => (/* binding */ FormFieldsSanitizeCustomId),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
class FormFieldsSanitizeCustomId extends $e.modules.hookData.Dependency {
  ID_SANITIZE_FILTER = /[^\w]/g;
  getCommand() {
    return 'document/elements/settings';
  }
  getId() {
    return 'cool-forms-fields-sanitize-custom-ids';
  }
  getContainerType() {
    return 'repeater';
  }
  getConditions(args) {
    return undefined !== args.settings.custom_id;
  }
  apply(args) {
    const {
        containers = [args.container],
        settings
      } = args,
      // `custom_id` is the control name.
      {
        custom_id: customId
      } = settings;
    if (customId.match(this.ID_SANITIZE_FILTER)) {
      // Re-render with old settings.
      containers.forEach(container => {
        const panelView = container.panel.getControlView('form_fields'),
          currentItemView = panelView.children.findByModel(container.settings),
          idView = currentItemView.children.find(view => 'custom_id' === view.model.get('name'));
        idView.render();
        idView.$el.find('input').trigger('focus');
      });

      // Hook-Break.
      return false;
    }
    return true;
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (FormFieldsSanitizeCustomId);

/***/ }),

/***/ "./modules/forms/assets/js/editor/hooks/data/form-fields-set-custom-id.js":
/*!********************************************************************************!*\
  !*** ./modules/forms/assets/js/editor/hooks/data/form-fields-set-custom-id.js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FormFieldsSetCustomId: () => (/* binding */ FormFieldsSetCustomId),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
class FormFieldsSetCustomId extends $e.modules.hookData.After {
  getCommand() {
    return 'document/repeater/insert';
  }
  getId() {
    return 'cool-forms-fields-set-custom-ids';
  }
  getContainerType() {
    return 'widget';
  }
  getConditions(args) {
    return 'form_fields' === args.name;
  }
  apply(args, model) {
    const {
        containers = [args.container]
      } = args,
      isDuplicate = $e.commands.isCurrentFirstTrace('document/repeater/duplicate');
    containers.forEach((/** Container */container) => {
      const itemContainer = container.repeaters.form_fields.children.find(childrenContainer => {
        // Sometimes, one of children is {Empty}.
        if (childrenContainer) {
          return model.get('_id') === childrenContainer.id;
        }
        return false;
      });
      if (!isDuplicate && itemContainer.settings.get('custom_id')) {
        return;
      }
      $e.run('document/elements/settings', {
        container: itemContainer,
        settings: {
          custom_id: 'field_' + itemContainer.id
        },
        options: {
          external: true
        }
      });
    });
    return true;
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (FormFieldsSetCustomId);

/***/ }),

/***/ "./modules/forms/assets/js/editor/hooks/data/form-fields-step.js":
/*!***********************************************************************!*\
  !*** ./modules/forms/assets/js/editor/hooks/data/form-fields-step.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FormFieldsAddFirstStep: () => (/* binding */ FormFieldsAddFirstStep),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
class FormFieldsAddFirstStep extends $e.modules.hookData.After {
  getCommand() {
    return 'document/elements/settings';
  }
  getId() {
    return 'cool-forms-fields-first-step';
  }
  getContainerType() {
    return 'repeater';
  }
  getConditions(args) {
    const {
      containers = [args.container]
    } = args;
    return 'cool-form' === containers[0].parent.parent.model.get('widgetType') && 'step' === args.settings.field_type;
  }
  apply(args) {
    const {
      containers = [args.container]
    } = args;
    containers.forEach((/** Container */container) => {
      const firstItem = container.parent.children[0];
      if ('step' === firstItem.settings.get('field_type')) {
        return;
      }
      $e.run('document/repeater/insert', {
        container: container.parent.parent,
        // Widget
        name: 'form_fields',
        model: {
          field_type: 'step'
        },
        options: {
          at: 0,
          external: true
        }
      });
    });
    return true;
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (FormFieldsAddFirstStep);

/***/ }),

/***/ "./modules/forms/assets/js/editor/hooks/data/form-sanitize-id.js":
/*!***********************************************************************!*\
  !*** ./modules/forms/assets/js/editor/hooks/data/form-sanitize-id.js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FormSanitizeId: () => (/* binding */ FormSanitizeId),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
class FormSanitizeId extends $e.modules.hookData.Dependency {
  ID_SANITIZE_FILTER = /[^\w]/g;
  getCommand() {
    return 'document/elements/settings';
  }
  getId() {
    return 'cool-forms-sanitize-ids';
  }
  getContainerType() {
    return 'widget';
  }
  getConditions(args) {
    return undefined !== args.settings.form_id;
  }
  apply(args) {
    const {
      container,
      settings
    } = args;
    const {
      form_id: formId
    } = settings;

    // Re-render with old settings.
    if (formId.match(this.ID_SANITIZE_FILTER)) {
      const formIdView = container.panel.getControlView('form_id');
      formIdView.render();
      formIdView.$el.find('input').trigger('focus');

      // Hook-Break.
      return false;
    }
    return true;
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (FormSanitizeId);

/***/ }),

/***/ "./modules/forms/assets/js/editor/hooks/data/index.js":
/*!************************************************************!*\
  !*** ./modules/forms/assets/js/editor/hooks/data/index.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FormFieldsAddFirstStep: () => (/* reexport safe */ _form_fields_step__WEBPACK_IMPORTED_MODULE_2__.FormFieldsAddFirstStep),
/* harmony export */   FormFieldsSanitizeCustomId: () => (/* reexport safe */ _form_fields_sanitize_custom_id__WEBPACK_IMPORTED_MODULE_0__.FormFieldsSanitizeCustomId),
/* harmony export */   FormFieldsSetCustomId: () => (/* reexport safe */ _form_fields_set_custom_id__WEBPACK_IMPORTED_MODULE_1__.FormFieldsSetCustomId),
/* harmony export */   FormSanitizeId: () => (/* reexport safe */ _form_sanitize_id__WEBPACK_IMPORTED_MODULE_3__.FormSanitizeId)
/* harmony export */ });
/* harmony import */ var _form_fields_sanitize_custom_id__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./form-fields-sanitize-custom-id */ "./modules/forms/assets/js/editor/hooks/data/form-fields-sanitize-custom-id.js");
/* harmony import */ var _form_fields_set_custom_id__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./form-fields-set-custom-id */ "./modules/forms/assets/js/editor/hooks/data/form-fields-set-custom-id.js");
/* harmony import */ var _form_fields_step__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./form-fields-step */ "./modules/forms/assets/js/editor/hooks/data/form-fields-step.js");
/* harmony import */ var _form_sanitize_id__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./form-sanitize-id */ "./modules/forms/assets/js/editor/hooks/data/form-sanitize-id.js");





/***/ }),

/***/ "./modules/forms/assets/js/editor/hooks/index.js":
/*!*******************************************************!*\
  !*** ./modules/forms/assets/js/editor/hooks/index.js ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FormFieldsAddFirstStep: () => (/* reexport safe */ _data___WEBPACK_IMPORTED_MODULE_0__.FormFieldsAddFirstStep),
/* harmony export */   FormFieldsSanitizeCustomId: () => (/* reexport safe */ _data___WEBPACK_IMPORTED_MODULE_0__.FormFieldsSanitizeCustomId),
/* harmony export */   FormFieldsSetCustomId: () => (/* reexport safe */ _data___WEBPACK_IMPORTED_MODULE_0__.FormFieldsSetCustomId),
/* harmony export */   FormFieldsUpdateShortCode: () => (/* reexport safe */ _ui___WEBPACK_IMPORTED_MODULE_1__.FormFieldsUpdateShortCode),
/* harmony export */   FormSanitizeId: () => (/* reexport safe */ _data___WEBPACK_IMPORTED_MODULE_0__.FormSanitizeId)
/* harmony export */ });
/* harmony import */ var _data___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./data/ */ "./modules/forms/assets/js/editor/hooks/data/index.js");
/* harmony import */ var _ui___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ui/ */ "./modules/forms/assets/js/editor/hooks/ui/index.js");



/***/ }),

/***/ "./modules/forms/assets/js/editor/hooks/ui/form-fields-update-shortcode.js":
/*!*********************************************************************************!*\
  !*** ./modules/forms/assets/js/editor/hooks/ui/form-fields-update-shortcode.js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FormFieldsUpdateShortCode: () => (/* binding */ FormFieldsUpdateShortCode),
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
class FormFieldsUpdateShortCode extends $e.modules.hookUI.After {
  getCommand() {
    return 'document/elements/settings';
  }
  getId() {
    return 'cool-forms-forms-fields-update-shortcodes';
  }
  getContainerType() {
    return 'repeater';
  }
  getConditions(args) {
    if (!$e.routes.isPartOf('panel/editor') || undefined === args.settings.custom_id) {
      return false;
    }
    return true;
  }
  apply(args) {
    const {
      containers = [args.container]
    } = args;
    containers.forEach((/** Container */container) => {
      const panelView = container.panel.getControlView('form_fields'),
        currentItemView = panelView.children.find(view => container.id === view.model.get('_id')),
        shortcodeView = currentItemView.children.find(view => 'shortcode' === view.model.get('name'));
      shortcodeView.render();
    });
  }
}
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (FormFieldsUpdateShortCode);

/***/ }),

/***/ "./modules/forms/assets/js/editor/hooks/ui/index.js":
/*!**********************************************************!*\
  !*** ./modules/forms/assets/js/editor/hooks/ui/index.js ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   FormFieldsUpdateShortCode: () => (/* reexport safe */ _form_fields_update_shortcode__WEBPACK_IMPORTED_MODULE_0__.FormFieldsUpdateShortCode)
/* harmony export */ });
/* harmony import */ var _form_fields_update_shortcode__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./form-fields-update-shortcode */ "./modules/forms/assets/js/editor/hooks/ui/form-fields-update-shortcode.js");


/***/ }),

/***/ "./modules/forms/assets/js/editor/module.js":
/*!**************************************************!*\
  !*** ./modules/forms/assets/js/editor/module.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ FormsModule)
/* harmony export */ });
/* harmony import */ var _component__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./component */ "./modules/forms/assets/js/editor/component.js");
/* harmony import */ var _fields_map_control__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./fields-map-control */ "./modules/forms/assets/js/editor/fields-map-control.js");
/* harmony import */ var _fields_map_control__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_fields_map_control__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _fields_repeater_control__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./fields-repeater-control */ "./modules/forms/assets/js/editor/fields-repeater-control.js");



class FormsModule extends elementorModules.editor.utils.Module {
  onElementorInit() {
    const ReplyToField = __webpack_require__(/*! ./reply-to-field */ "./modules/forms/assets/js/editor/reply-to-field.js");
    this.replyToField = new ReplyToField();

    // Form fields
    const AcceptanceField = __webpack_require__(/*! ./fields/acceptance */ "./modules/forms/assets/js/editor/fields/acceptance.js"),
      TelField = __webpack_require__(/*! ./fields/tel */ "./modules/forms/assets/js/editor/fields/tel.js"),
      DateField = __webpack_require__(/*! ./fields/date */ "./modules/forms/assets/js/editor/fields/date.js"),
      TimeField = __webpack_require__(/*! ./fields/time */ "./modules/forms/assets/js/editor/fields/time.js");
    this.Fields = {
      tel: new TelField('cool-form'),
      acceptance: new AcceptanceField('cool-form'),
      date: new DateField('cool-form'),
      time: new TimeField('cool-form')
    };
    elementor.addControlView('Fields_map', (_fields_map_control__WEBPACK_IMPORTED_MODULE_1___default()));
    elementor.addControlView('form-fields-repeater', _fields_repeater_control__WEBPACK_IMPORTED_MODULE_2__["default"]);
    this.onElementorInitComponents();
  }
  onElementorInitComponents() {
    $e.components.register(new _component__WEBPACK_IMPORTED_MODULE_0__["default"]({
      manager: this
    }));
  }
}

/***/ }),

/***/ "./modules/forms/assets/js/editor/reply-to-field.js":
/*!**********************************************************!*\
  !*** ./modules/forms/assets/js/editor/reply-to-field.js ***!
  \**********************************************************/
/***/ ((module) => {

module.exports = function () {
  var editor, editedModel, replyToControl;
  var setReplyToControl = function () {
    replyToControl = editor.collection.findWhere({
      name: 'email_reply_to'
    });
  };
  var getReplyToView = function () {
    return editor.children.findByModelCid(replyToControl.cid);
  };
  var refreshReplyToElement = function () {
    var replyToView = getReplyToView();
    if (replyToView) {
      replyToView.render();
    }
  };
  var updateReplyToOptions = function () {
    var settingsModel = editedModel.get('settings'),
      emailModels = settingsModel.get('form_fields').where({
        field_type: 'email'
      }),
      emailFields;
    emailModels = _.reject(emailModels, {
      field_label: ''
    });
    emailFields = _.map(emailModels, function (model) {
      return {
        id: model.get('custom_id'),
        label: sprintf('%s Field', model.get('field_label'))
      };
    });
    replyToControl.set('options', {
      '': replyToControl.get('options')['']
    });
    _.each(emailFields, function (emailField) {
      replyToControl.get('options')[emailField.id] = emailField.label;
    });
    refreshReplyToElement();
  };
  var updateDefaultReplyTo = function (settingsModel) {
    replyToControl.get('options')[''] = settingsModel.get('email_from');
    refreshReplyToElement();
  };
  var onFormFieldsChange = function (changedModel) {
    // If it's repeater field
    if (changedModel.get('custom_id')) {
      if ('email' === changedModel.get('field_type')) {
        updateReplyToOptions();
      }
    }
    if (changedModel.changed.email_from) {
      updateDefaultReplyTo(changedModel);
    }
  };
  var onPanelShow = function (panel, model) {
    editor = panel.getCurrentPageView();
    editedModel = model;
    setReplyToControl();
    var settingsModel = editedModel.get('settings');
    settingsModel.on('change', onFormFieldsChange);
    updateDefaultReplyTo(settingsModel);
    updateReplyToOptions();
  };
  var init = function () {
    elementor.hooks.addAction('panel/open_editor/widget/form-lite', onPanelShow);
  };
  init();
};

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be in strict mode.
(() => {
"use strict";
/*!*******************************************!*\
  !*** ./modules/forms/assets/js/editor.js ***!
  \*******************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _editor_module__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./editor/module */ "./modules/forms/assets/js/editor/module.js");

const Cool_FormKitForms = new _editor_module__WEBPACK_IMPORTED_MODULE_0__["default"]();
window.Cool_FormKitForms = Cool_FormKitForms;
})();

/******/ })()
;
//# sourceMappingURL=Cool_FormKit-forms-editor.js.map