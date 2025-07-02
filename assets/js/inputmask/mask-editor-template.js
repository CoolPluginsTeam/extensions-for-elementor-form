jQuery(document).ready(function ($) {
    // Register the filter
    elementor.hooks.addFilter('elementor_pro/forms/content_template/item', function (field, item, form) {
        
        if (field.field_type == "text") {

           field.css_classes += `mask_control_@${field.fme_mask_control} money_mask_format_@${field.fme_money_mask_format} mask_prefix_@${field.fme_money_mask_prefix}  mask_decimal_places_@${field.fme_money_mask_decimal_places} mask_time_mask_format_@${field.fme_time_mask_format} fme_phone_format_@${field.fme_phone_format} credit_card_options_@${field.fme_credit_card_options} mask_auto_placeholder_@${field.fme_mask_auto_placeholders} fme_brazilian_formats_@${field.fme_brazilian_formats}`
           
        }
        return field;
    }, 10, 4);
});
  
  