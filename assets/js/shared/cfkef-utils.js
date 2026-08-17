window.CFKEF = window.CFKEF || {};

/**
 * Decode HTML entities via a temporary textarea.
 *
 * @param {*} text
 * @return {string}
 */
window.CFKEF.decodeHtml = function (text) {
	var textArea = document.createElement('textarea');
	textArea.innerHTML = text == null ? '' : String(text);
	return textArea.value;
};
