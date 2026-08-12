/**
 * Shared Elementor review-notice dismiss binder.
 * Usage: CFKEF.bindReviewDismiss('#buttonId', 'ajax_action', 'post_field');
 */
window.CFKEF = window.CFKEF || {};

CFKEF.bindReviewDismiss = function (buttonId, ajaxAction, dismissField) {
	var noticeSelector = '.' + String(buttonId).replace(/^#/, '').replace(/_dismiss$/, '_notice');

	jQuery(document).on('click', buttonId, function (event) {
		jQuery(noticeSelector).hide();
		var btn = jQuery(event.target);
		var data = {
			action: ajaxAction,
			nonce: btn.data('nonce')
		};
		data[dismissField] = true;

		jQuery.ajax({
			type: 'POST',
			url: btn.data('url'),
			data: data,
			success: function () {
				btn.closest('.elementor-control').remove();
			}
		});
	});
};
