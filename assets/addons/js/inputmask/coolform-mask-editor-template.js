jQuery(document).ready(function ($) {
	elementor.hooks.addFilter('cool_formkit/forms/content_template/item', function (item) {
		var maskClass = CFKEF.buildMaskEditorClasses(item);
		if (maskClass) {
			item.custom_mask_attributes = {
				'data-mask': item.fme_mask_control,
				'class': 'fme-mask-input ' + maskClass
			};
		}
		return item;
	}, 10);
});
