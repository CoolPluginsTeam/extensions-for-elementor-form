"use strict";
let formRepeaterFields = [];
let getTagStatus=false;

// add repeater functionality to conditional field repeater
const addDynamicTags = (element) => {

  jQuery('.elementor-control-tag-area[data-setting="cfef_logic_field_id"]').on("click", function (e) {
    if (jQuery(this).data("check") != "ok") {
      jQuery(this).after('<div class="elementor-control-dynamic-switcher elementor-control-unit-1 cfef-add-tag" data-tooltip="add Tags" original-title=""><i class="eicon-database" aria-hidden="true"></i><span class="elementor-screen-only">Dynamic Tags</span></div>');
      jQuery(this).data("check", "ok");
    }
  })

  // create list of id's of form fields for dynamic tags
  jQuery(".cfef-add-tag").on("click", function (e) {
    const $list = jQuery( '<ul/>', { class: 'cfef-dynamic-tag' } );
    let formFields = jQuery(this).closest(".elementor-repeater-fields-wrapper").parents(".elementor-repeater-fields-wrapper").find('.elementor-form-field-shortcode');
    if(this.closest('.elementor-control-cfef_repeater_data_cfefp_submit')){
      formFields=formRepeaterFields; 
    }

    jQuery(formFields).each(function () {
      const regex = /\".*?\"/gm;
      let m;
      var name = jQuery(this).val();
      while ((m = regex.exec(name)) !== null) {
        if (m.index === regex.lastIndex) {
          regex.lastIndex++;
        }
        m.forEach((match, groupIndex) => {
          name = match.replaceAll('"', "");
        });
      }
      jQuery( '<li/>', { title: 'Copy ID' } ).text( name ).appendTo( $list );
    });
    jQuery(this).closest(".elementor-control-input-wrapper").append( $list );
  })

  // function to copy the id of selected option from dynamic tag
  jQuery(".cfef-dynamic-tag li").on("click", function (event) {
    var selectedValue = this.innerHTML;
    jQuery(this).parent().siblings().val(selectedValue);
    jQuery(this).parent().siblings().trigger('input');
    if (jQuery('#elementor-panel-saver-button-publish')[0].classList.contains('elementor-disabled')) {
      jQuery('#elementor-panel-saver-button-publish')[0].classList.remove('elementor-disabled')
    }
    setTimeout(function () {
      jQuery(".cfef-dynamic-tag").remove();
    }, 500);
  });
};

const stopBubbling = (event) => {
  addDynamicTags();
  event.stopPropagation();
}
// add repeater functionality to conditional field repeater
const customRepeaterField = (element) => {
  element[0].addEventListener('click', stopBubbling);
};

/**
 * Keep nested repeater rows (form_fields -> cfef_repeater_data) from mutating the document config.
 *
 * Elementor's BaseSettings.handleRepeaterData() writes a Backbone.Collection back into the attrs
 * object it receives. For a repeater row that holds another repeater, that object is the raw row
 * from elementor.documents.getCurrent().config.elements, so the config ends up holding a circular
 * Backbone.Collection. Elementor Pro's editor-templates-extended package then walks the config
 * recursively (collectShortcodeTemplateIdsFromValue) and throws "Maximum call stack size exceeded".
 * Cloning the rows first means the Collection is written onto a copy, not onto the config.
 *
 * Runs once on load and again on elementor/init, in case elementorModules is not ready yet.
 */
const cfefPatchNestedRepeaterSettings = () => {
  const models = window.elementorModules && elementorModules.editor && elementorModules.editor.elements && elementorModules.editor.elements.models;
  const proto = models && models.BaseSettings && models.BaseSettings.prototype;

  if (!proto || typeof proto.convertRepeaterValueToCollection !== 'function' || proto.cfefNestedRepeaterPatched) {
    return;
  }

  const originalConvert = proto.convertRepeaterValueToCollection;
  const hasNestedRepeater = (control) => Object.values(control.fields || {}).some(
    (field) => field && ('repeater' === field.type || field.is_repeater)
  );

  proto.convertRepeaterValueToCollection = function (attrs, repeaterControl) {
    const rows = attrs ? attrs[repeaterControl.name] : null;

    if (Array.isArray(rows) && hasNestedRepeater(repeaterControl)) {
      const clonedRows = rows.map((row) => (
        row && 'object' === typeof row && !(row instanceof Backbone.Model) ? Object.assign({}, row) : row
      ));
      attrs = Object.assign({}, attrs, { [repeaterControl.name]: clonedRows });
    }

    return originalConvert.call(this, attrs, repeaterControl);
  };
  proto.cfefNestedRepeaterPatched = true;
};

cfefPatchNestedRepeaterSettings();
window.addEventListener('elementor/init', cfefPatchNestedRepeaterSettings);

window.addEventListener('elementor/init', function () {
  var RepeaterControlItemView = elementor.modules.controls.Repeater.extend({
    onBeforeRender: function () {
      if (this.$el.hasClass('elementor-control-cfef_repeater_data') || this.$el.hasClass('elementor-control-choices')) {
        customRepeaterField(this.$el);
      }
      if (this.$el.hasClass('elementor-control-cfef_repeater_data_cfefp_submit') || this.$el.hasClass('elementor-control-choices')) {
        customRepeaterField(this.$el);
      }
      if (this.$el.hasClass('elementor-control-cfef_repeater_data') || this.$el.hasClass('elementor-control-cfef_repeater_data_cfefp_submit')) {
        setTimeout(() => {
          addDynamicTags(this.$el);
        })
      }
    },
    onDestroy: function(){
      if(this.$el.closest('.elementor-control-type-form-fields-repeater')){
        const elements=this.$el.closest('.elementor-repeater-fields-wrapper').find('.elementor-form-field-shortcode');
        setTimeout(()=>{
          if(!getTagStatus){
            formRepeaterFields=[];
            getTagStatus=true;
            elements.each((indx,ele)=>{
              formRepeaterFields.push(ele); 
            });
          }
        })

        setTimeout(()=>{
          if(getTagStatus){
          getTagStatus=false;
          }
        },300)
      }
    }
  });
  elementor.addControlView('repeater', RepeaterControlItemView);
});

(function ($) {
  CFKEF.bindReviewDismiss('#cfef_elementor_review_dismiss', 'cfef_elementor_review_notice', 'cfef_notice_dismiss');
})(jQuery);