/**
 * Hello Plus editor mask class preview.
 */
jQuery(document).ready(function ($) {
	elementor.hooks.addFilter('hello_plus/forms/content_template/item', function (item) {
		var maskClass = CFKEF.buildMaskEditorClasses(item);
		if (maskClass) {
			item.css_classes = (item.css_classes || '') + ' fme-mask-input ' + maskClass;
		}
		return item;
	}, 10);
});
