"use strict";
let formRepeaterFields = [];
let formRepeaterFieldsLast = null;
let getTagStatus=false;

// add repeater functionality to conditional field repeater
const addDynamicTags = (element) => {

  jQuery('.elementor-control-tag-area[data-setting="cfef_logic_field_id"]').on("click", function (e) {
    if (jQuery(this).data("check") != "ok") {
      jQuery(this).after('<div class="elementor-control-dynamic-switcher elementor-control-unit-1 cfef-add-tag" data-tooltip="add Tags" original-title=""><i class="eicon-database" aria-hidden="true"></i><span class="elementor-screen-only">Dynamic Tags</span></div>');
      jQuery(this).data("check", "ok");
    }
  })

  // add repeater functionality to conditional field repeater for button section
  jQuery('.elementor-control-tag-area[data-setting="cfef_logic_field_id"]').on("click", function (e) {
    if (jQuery(this).data("check") != "ok") {
      jQuery(this).after('<div class="elementor-control-dynamic-switcher elementor-control-unit-1 cfef-add-tag" data-tooltip="add Tags" original-title=""><i class="eicon-database" aria-hidden="true"></i><span class="elementor-screen-only">Dynamic Tags</span></div>');
      jQuery(this).data("check", "ok");
    }
  })

  // create list of id's of form fields for dynamic tags
  jQuery(".cfef-add-tag").on("click", function (e) {
    var html = '<ul class="cfef-dynamic-tag">';
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
      html += '<li title="Copy ID">' + name + '</li>';
    });
    html += '</ul>';
    jQuery(this).closest(".elementor-control-input-wrapper").append(html);
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

window.addEventListener('elementor/init', function () {
  var RepeaterControlItemView = elementor.modules.controls.Repeater.extend({
    onBeforeRender: function () {
      if (this.$el.hasClass('elementor-control-cfef_repeater_data') || this.$el.hasClass('elementor-control-choices')) {
        customRepeaterField(this.$el);
      }
      if (this.$el.hasClass('elementor-control-cfef_repeater_data_cfefp_submit' || this.$el.hasClass('elementor-control-choices'))) {
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
  jQuery(document).on('click', '#cfef_elementor_review_dismiss', (event) => {
    jQuery(".cfef_elementor_review_notice").hide()
    const btn = jQuery(event.target);
    const nonce = btn.data('nonce');
    const url = btn.data('url');

    jQuery.ajax({
      type: 'POST',
      // eslint-disable-next-line no-undef
      url: url, // Set this using wp_localize_script
      data: {
        action: 'cfef_elementor_review_notice',
        cfef_notice_dismiss: true,
        nonce: nonce,
      },
      success: (response) => {
        btn.closest('.elementor-control').remove();
      },
      error: (xhr, status, error) => {
        console.log(xhr.responseText);
        console.log(error);
        console.log(status);
      },
    });
  });

})(jQuery);