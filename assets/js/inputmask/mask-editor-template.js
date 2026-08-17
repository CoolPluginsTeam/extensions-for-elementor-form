jQuery(document).ready(function ($) {
    elementor.hooks.addFilter('elementor_pro/forms/content_template/item', function (field) {
        var maskClass = CFKEF.buildMaskEditorClasses(field);
        if (maskClass) {
            field.css_classes = (field.css_classes || '') + ' fme-mask-input ' + maskClass;
        }
        return field;
    }, 10, 4);
});
