jQuery(document).ready(function ($) {
	elementor.hooks.addFilter('cool_formkit/forms/content_template/item', function (item, i, settings) {

		if (item.field_type === 'text' && item.fme_mask_control && item.fme_mask_control !== 'mask') {
			
			// Generate additional class string
			let maskClass = [
				'mask_control_@' + item.fme_mask_control,
				'money_mask_format_@' + (item.fme_money_mask_format || ''),
				'mask_prefix_@' + (item.fme_money_mask_prefix || ''),
				'mask_decimal_places_@' + (item.fme_mask_decimal_places || ''),
				'mask_time_mask_format_@' + (item.fme_time_mask_format || ''),
				'fme_phone_format_@' + (item.fme_phone_format || ''),
				'credit_card_options_@' + (item.fme_credit_card_options || ''),
				'mask_auto_placeholder_@' + (item.fme_mask_auto_placeholders || ''),
				'fme_brazilian_formats_@' + (item.fme_brazilian_formats || '')
			].join(' ');

			// Inject into custom_mask_attributes
			item.custom_mask_attributes = {
				'data-mask': item.fme_mask_control,
				'class': 'fme-mask-input ' + maskClass
			};

		}

		return item;
	}, 10);
});
