/**
 * Shared conditional field operator evaluation and required-field helpers.
 */
window.CFKEF = window.CFKEF || {};

CFKEF.checkFieldLogic = function (compareFieldValue, conditionOperation, compareValue) {
	var decode = typeof CFKEF.decodeHtml === 'function'
		? CFKEF.decodeHtml
		: function (text) {
			return text == null ? '' : String(text);
		};

	conditionOperation = decode(conditionOperation);
	compareValue = compareValue === null ? decode(compareValue) : decode(compareValue).trim();
	var valueA = compareFieldValue === null ? decode(compareFieldValue) : String(compareFieldValue).trim();

	var values = valueA.split(',').map(function (v) {
		return v.trim();
	});
	var matchFound = values.some(function (v) {
		return v === compareValue;
	});

	switch (conditionOperation) {
		case '==':
			return matchFound && valueA !== '';
		case '!=':
			return !matchFound && valueA !== '';
		case 'e':
			return valueA === '';
		case '!e':
			return valueA !== '';
		case 'c':
			return valueA.includes(compareValue);
		case '!c':
			return valueA !== '' && !valueA.includes(compareValue);
		case '^':
			return valueA.startsWith(compareValue);
		case '~':
			return valueA.endsWith(compareValue);
		case '>':
			return parseInt(valueA, 10) > parseInt(compareValue, 10);
		case '<':
			return parseInt(valueA, 10) < parseInt(compareValue, 10);
		case '>=':
			return parseInt(valueA, 10) >= parseInt(compareValue, 10);
		case '<=':
			return parseInt(valueA, 10) <= parseInt(compareValue, 10);
		default:
			return false;
	}
};

/**
 * Demo values used when hiding required fields so HTML5 validation still passes.
 *
 * @param {string} nodeName
 * @param {string} type
 * @param {Object} [opts]
 * @param {boolean} [opts.telWithPlus]
 * @return {string|null}
 */
CFKEF.getDemoValueForControl = function (nodeName, type, opts) {
	opts = opts || {};
	nodeName = (nodeName || '').toLowerCase();
	type = (type || '').toLowerCase();

	if (nodeName === 'textarea') {
		return 'cool_plugins';
	}
	if (nodeName === 'select') {
		return null;
	}
	if (type === 'email') {
		return 'cool_plugins@abc.com';
	}
	if (type === 'url') {
		return 'https://testing.com';
	}
	if (type === 'tel') {
		return opts.telWithPlus ? '+1234567890' : '1234567890';
	}
	if (type === 'number') {
		return '000';
	}
	if (type === 'date') {
		return '1003-01-01';
	}
	if (type === 'time') {
		return '11:59';
	}
	return 'cool23plugins';
};

/**
 * Evaluate show/hide logic for a field given a value getter.
 *
 * @param {Object} logicValue
 * @param {Function} getFieldValueFn function(fieldId): string
 * @return {boolean} true when the field should be visible
 */
CFKEF.evaluateLogicRules = function (logicValue, getFieldValueFn) {
	var displayMode = (logicValue && logicValue.display_mode) || 'show';
	var fireAction = (logicValue && logicValue.fire_action) || 'All';
	var logicData = logicValue && Array.isArray(logicValue.logic_data) ? logicValue.logic_data : [];
	var checks = [];

	logicData.forEach(function (rule) {
		if (!rule || !rule.cfef_logic_field_id) {
			return;
		}
		checks.push(
			CFKEF.checkFieldLogic(
				getFieldValueFn(rule.cfef_logic_field_id),
				rule.cfef_logic_field_is,
				rule.cfef_logic_compare_value
			)
		);
	});

	if (!checks.length) {
		return false;
	}

	// Free/Atomic path only evaluates AND ("All"); OR ("Any") is Pro.
	if (fireAction !== 'All') {
		return true;
	}

	var result = checks.every(function (v) {
		return v === true;
	});

	return displayMode === 'show' ? result : !result;
};

/**
 * Apply demo values to required controls inside a collection (Atomic-style).
 *
 * @param {jQuery} $controls
 * @param {Object} [opts]
 * @param {string} [opts.fileUploadClass]
 * @param {boolean} [opts.telWithPlus]
 */
CFKEF.applyRequiredDemoOnControls = function ($controls, opts) {
	opts = opts || {};
	var $ = window.jQuery;
	if (!$ || !$controls || !$controls.each) {
		return;
	}

	$controls.each(function () {
		var control = $(this);
		var nodeName = (control.prop('nodeName') || '').toLowerCase();
		var type = (control.attr('type') || '').toLowerCase();
		var isRequired =
			control.prop('required') ||
			control.attr('required') !== undefined ||
			control.attr('aria-required') === 'true';

		if (!isRequired) {
			return;
		}

		if (type === 'checkbox' || type === 'radio') {
			var groupControls = $controls.filter('[type="' + type + '"][name="' + control.attr('name') + '"]');
			var checkedControl = groupControls.filter(':checked');
			if (typeof control.data('cfefOriginalCheckedValue') === 'undefined') {
				control.data('cfefOriginalCheckedValue', checkedControl.length ? checkedControl.val() : '');
			}
			if (!checkedControl.length && groupControls.length) {
				var firstControl = groupControls.first();
				firstControl.prop('checked', true);
				firstControl.data('cfefDemoApplied', true);
			} else if (checkedControl.length) {
				groupControls.prop('checked', false);
				var firstChecked = groupControls.first();
				firstChecked.prop('checked', true);
				firstChecked.data('cfefDemoApplied', true);
			}
			return;
		}

		if (nodeName === 'select') {
			if (typeof control.data('cfefOriginalValue') === 'undefined') {
				control.data('cfefOriginalValue', control.val() || '');
			}
			var firstOption = control.find("option[value!='']").first();
			if (!firstOption.length) {
				firstOption = control.find('option').first();
			}
			if (firstOption.length) {
				control.val(firstOption.val());
				control.data('cfefDemoApplied', true);
			}
			return;
		}

		if (opts.fileUploadClass && control.hasClass(opts.fileUploadClass)) {
			var accept = control.attr('accept') || '';
			var firstType = accept.split(',')[0] || 'png';
			var fileName = 'cool-formkit-placeholder.' + firstType;
			var defaultImage = new File([], fileName, { type: 'image/png' });
			var container = new DataTransfer();
			container.items.add(defaultImage);
			control[0].files = container.files;
			return;
		}

		if (typeof control.data('cfefOriginalValue') === 'undefined') {
			control.data('cfefOriginalValue', control.val() || '');
		}
		control.val(CFKEF.getDemoValueForControl(nodeName, type, { telWithPlus: !!opts.telWithPlus }));
		control.data('cfefDemoApplied', true);
	});
};

/**
 * Restore demo values previously applied by applyRequiredDemoOnControls.
 *
 * @param {jQuery} $controls
 * @param {Object} [opts]
 * @param {string} [opts.fileUploadClass]
 */
CFKEF.restoreRequiredDemoOnControls = function ($controls, opts) {
	opts = opts || {};
	var $ = window.jQuery;
	if (!$ || !$controls || !$controls.each) {
		return;
	}

	$controls.each(function () {
		var control = $(this);

		if (opts.fileUploadClass && control.hasClass(opts.fileUploadClass)) {
			var accept = control.attr('accept') || '';
			var firstType = accept.split(',')[0] || 'png';
			var fileName = 'cool-formkit-placeholder.' + firstType;
			var inputValue = control.val() || '';
			if (inputValue.indexOf(fileName) !== -1) {
				control.val('');
			}
			return;
		}

		if (control.data('cfefDemoApplied') !== true) {
			return;
		}

		var type = (control.attr('type') || '').toLowerCase();
		if (type === 'checkbox' || type === 'radio') {
			var groupName = control.attr('name');
			if (groupName) {
				var groupControls = $controls.filter('[type="' + type + '"][name="' + groupName + '"]');
				var originalCheckedValue = control.data('cfefOriginalCheckedValue');
				groupControls.prop('checked', false);
				if (originalCheckedValue) {
					groupControls.filter('[value="' + originalCheckedValue + '"]').first().prop('checked', true);
				}
			} else {
				control.prop('checked', false);
			}
			control.removeData('cfefOriginalCheckedValue');
		} else {
			var originalValue = control.data('cfefOriginalValue');
			control.val(typeof originalValue === 'undefined' ? '' : originalValue);
			control.removeData('cfefOriginalValue');
		}
		control.removeData('cfefDemoApplied');
	});
};

/**
 * Convert YYYY-MM-DD to MM/DD/YYYY for logic comparisons.
 *
 * @param {string} dateStr
 * @return {string}
 */
CFKEF.formatDateToMDY = function (dateStr) {
	if (!dateStr || typeof dateStr !== 'string') {
		return dateStr || '';
	}
	var parts = dateStr.split('-');
	if (parts.length !== 3) {
		return dateStr;
	}
	return parts[1] + '/' + parts[2] + '/' + parts[0];
};

/**
 * Convert 24h HH:MM to 12h display for logic comparisons.
 *
 * @param {string} time
 * @return {string}
 */
CFKEF.convertTimeTo12Hour = function (time) {
	if (!time || typeof time !== 'string') {
		return time || '';
	}
	var parts = time.split(':');
	if (parts.length < 2) {
		return time;
	}
	var hours = parseInt(parts[0], 10);
	var minutes = parts[1];
	var period = hours >= 12 ? 'PM' : 'AM';
	var hours12 = hours % 12 || 12;
	return String(hours12).padStart(2, '0') + ':' + minutes + ' ' + period;
};
