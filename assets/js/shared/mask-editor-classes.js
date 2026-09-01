/**
 * Shared mask editor class-string builder for Elementor / Cool Form / Hello Plus previews.
 */
(function (window) {
	'use strict';

	window.CFKEF = window.CFKEF || {};

	/**
	 * Build mask metadata class string from a form field item.
	 *
	 * @param {Object} item Field item from content_template filter.
	 * @return {string}
	 */
	CFKEF.buildMaskEditorClasses = function (item) {
		if (!item || item.field_type !== 'text' || !item.fme_mask_control || item.fme_mask_control === 'mask') {
			return '';
		}

		return [
			'mask_control_@' + item.fme_mask_control,
			'money_mask_format_@' + (item.fme_money_mask_format || ''),
			'mask_prefix_@' + (item.fme_money_mask_prefix || ''),
			'mask_decimal_places_@' + (item.fme_money_mask_decimal_places || ''),
			'mask_time_mask_format_@' + (item.fme_time_mask_format || ''),
			'fme_phone_format_@' + (item.fme_phone_format || ''),
			'credit_card_options_@' + (item.fme_credit_card_options || ''),
			'mask_auto_placeholder_@' + (item.fme_mask_auto_placeholders || ''),
			'fme_brazilian_formats_@' + (item.fme_brazilian_formats || ''),
		].join(' ');
	};
})(window);
