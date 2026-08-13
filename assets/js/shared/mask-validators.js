/**
 * Shared input-mask validation helpers (Elementor Pro, Cool Form, Hello Plus, Atomic Form).
 */
window.CFKEF = window.CFKEF || {};

window.CFKEF.MaskValidators = (function () {
	'use strict';

	function isValidPhoneUSA(phoneStr) {
		return /^\(\d{3}\) \d{3}-\d{4}$/.test(phoneStr);
	}

	function isValidPhone8(phoneStr) {
		return /^\d{4}-\d{4}$/.test(phoneStr);
	}

	function isValidPhoneDDD8(phoneStr) {
		return /^\(\d{2}\) \d{4}-\d{4}$/.test(phoneStr);
	}

	function isValidPhoneDDD9(phoneStr) {
		return /^\(\d{2}\) 9\d{4}-\d{4}$/.test(phoneStr);
	}

	function isValidDateTime(value, format) {
		var regexPattern;
		var expectedParts;

		switch (format) {
			case 'DMY':
				regexPattern = /^(\d{2})\/(\d{2})\/(\d{4})$/;
				expectedParts = ['day', 'month', 'year'];
				break;
			case 'MDY':
				regexPattern = /^(\d{2})\/(\d{2})\/(\d{4})$/;
				expectedParts = ['month', 'day', 'year'];
				break;
			case 'HMS':
				regexPattern = /^(\d{2}):(\d{2}):(\d{2})$/;
				expectedParts = ['hour', 'minute', 'second'];
				break;
			case 'HM':
				regexPattern = /^(\d{2}):(\d{2})$/;
				expectedParts = ['hour', 'minute'];
				break;
			case 'DMY-HM':
				regexPattern = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/;
				expectedParts = ['day', 'month', 'year', 'hour', 'minute'];
				break;
			case 'MDY-HM':
				regexPattern = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/;
				expectedParts = ['month', 'day', 'year', 'hour', 'minute'];
				break;
			case 'MY':
				regexPattern = /^(\d{2})\/(\d{4})$/;
				expectedParts = ['month', 'year'];
				break;
			default:
				return false;
		}

		var match = value.match(regexPattern);
		if (!match) {
			return false;
		}

		var parts = {};
		expectedParts.forEach(function (part, index) {
			parts[part] = parseInt(match[index + 1], 10);
		});

		if (parts.year && (parts.year < 1500 || parts.year > 3000)) {
			return false;
		}
		if (parts.month && (parts.month < 1 || parts.month > 12)) {
			return false;
		}
		if (parts.day) {
			var daysInMonth = new Date(parts.year, parts.month, 0).getDate();
			if (parts.day < 1 || parts.day > daysInMonth) {
				return false;
			}
		}
		if (parts.hour && (parts.hour < 0 || parts.hour >= 24)) {
			return false;
		}
		if (parts.minute && (parts.minute < 0 || parts.minute >= 60)) {
			return false;
		}
		if (parts.second && (parts.second < 0 || parts.second >= 60)) {
			return false;
		}

		return true;
	}

	function isValidExpiryDate(value, format) {
		var regexPattern = format === 'MM/YY' ? /^(\d{2})\/(\d{2})$/ : /^(\d{2})\/(\d{4})$/;
		var match = value.match(regexPattern);
		if (!match) {
			return false;
		}

		var month = parseInt(match[1], 10);
		var year = parseInt(match[2], 10);
		var currentYear = new Date().getFullYear();
		var currentMonth = new Date().getMonth() + 1;

		if (format === 'MM/YY') {
			year += 2000;
		}
		if (month < 1 || month > 12) {
			return false;
		}
		if (year < currentYear || (year === currentYear && month < currentMonth)) {
			return false;
		}
		return true;
	}

	function isValidCreditCard(cardNumber) {
		var cleaned = String(cardNumber).replace(/\D/g, '');
		if (cleaned.length < 15 || cleaned.length > 16) {
			return false;
		}
		var sum = 0;
		var shouldDouble = false;
		for (var i = cleaned.length - 1; i >= 0; i--) {
			var digit = parseInt(cleaned.charAt(i), 10);
			if (shouldDouble) {
				digit *= 2;
				if (digit > 9) {
					digit -= 9;
				}
			}
			sum += digit;
			shouldDouble = !shouldDouble;
		}
		return sum % 10 === 0;
	}

	function isValidCNPJ(cnpj) {
		cnpj = String(cnpj).toUpperCase().replace(/[.\-\/]/g, '');
		if (!/^[A-Z0-9]{12}\d{2}$/.test(cnpj)) {
			return false;
		}
		if (/^(.)\1{13}$/.test(cnpj)) {
			return false;
		}
		var calcCheckDigit = function (str, length) {
			var weights = length === 12
				? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2]
				: [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
			var s = 0;
			for (var i = 0; i < weights.length; i++) {
				s += (str.charCodeAt(i) - 48) * weights[i];
			}
			var remainder = s % 11;
			return remainder < 2 ? 0 : 11 - remainder;
		};
		var firstCheck = calcCheckDigit(cnpj, 12);
		var secondCheck = calcCheckDigit(cnpj.slice(0, 12) + firstCheck, 13);
		return firstCheck === parseInt(cnpj.charAt(12), 10) && secondCheck === parseInt(cnpj.charAt(13), 10);
	}

	function isValidCPF(cpf) {
		cpf = String(cpf).replace(/\D/g, '');
		if (cpf.length !== 11) {
			return false;
		}
		if (/^(\d)\1+$/.test(cpf)) {
			return false;
		}
		var validateCPFDigit = function (str, length) {
			var s = 0;
			for (var i = 0; i < length; i++) {
				s += parseInt(str.charAt(i), 10) * (length + 1 - i);
			}
			var result = (s * 10) % 11;
			if (result === 10) {
				result = 0;
			}
			return result === parseInt(str.charAt(length), 10);
		};
		return validateCPFDigit(cpf, 9) && validateCPFDigit(cpf, 10);
	}

	function isValidCEP(cep) {
		return /^\d{5}-\d{3}$/.test(cep);
	}

	function isValidIPv4(ip) {
		var ipv4Pattern = /^(?:\d{1,3}\.){3}\d{1,3}$/;
		if (!ipv4Pattern.test(ip)) {
			return false;
		}
		return ip.split('.').every(function (octet) {
			var num = parseInt(octet, 10);
			return num >= 0 && num <= 255;
		});
	}

		return {
		isValidPhoneUSA: isValidPhoneUSA,
		isValidPhone8: isValidPhone8,
		isValidPhoneDDD8: isValidPhoneDDD8,
		isValidPhoneDDD9: isValidPhoneDDD9,
		isValidDateTime: isValidDateTime,
		isValidExpiryDate: isValidExpiryDate,
		isValidDateDMY: function (v) { return isValidDateTime(v, 'DMY'); },
		isValidDateMDY: function (v) { return isValidDateTime(v, 'MDY'); },
		isValidTimeHMS: function (v) { return isValidDateTime(v, 'HMS'); },
		isValidTimeHM: function (v) { return isValidDateTime(v, 'HM'); },
		isValidDateDMYHM: function (v) { return isValidDateTime(v, 'DMY-HM'); },
		isValidDateMDYHM: function (v) { return isValidDateTime(v, 'MDY-HM'); },
		isValidDateMY: function (v) { return isValidDateTime(v, 'MY'); },
		isValidExpiryMMYY: function (v) { return isValidExpiryDate(v, 'MM/YY'); },
		isValidExpiryMMYYYY: function (v) { return isValidExpiryDate(v, 'MM/YYYY'); },
		isValidCreditCard: isValidCreditCard,
		isValidCNPJ: isValidCNPJ,
		isValidCPF: isValidCPF,
		isValidCEP: isValidCEP,
		isValidIPv4: isValidIPv4,
	};
})();

CFKEF.getMaskValidationDefs = function () {
	var MV = CFKEF.MaskValidators || {};
	return [
		{ sel: '.mask-cnpj', errorClass: 'error-cnpj', validate: MV.isValidCNPJ, key: 'mask-cnpj' },
		{ sel: '.mask-cpf', errorClass: 'error-cpf', validate: MV.isValidCPF, key: 'mask-cpf' },
		{ sel: '.mask-cep', errorClass: 'error-cep', validate: MV.isValidCEP, key: 'mask-cep' },
		{ sel: '.mask-phus', errorClass: 'error-phus', validate: MV.isValidPhoneUSA, key: 'mask-phus' },
		{ sel: '.mask-ph8', errorClass: 'error-ph8', validate: MV.isValidPhone8, key: 'mask-ph8' },
		{ sel: '.mask-ddd8', errorClass: 'error-ddd8', validate: MV.isValidPhoneDDD8, key: 'mask-ddd8' },
		{ sel: '.mask-ddd9', errorClass: 'error-ddd9', validate: MV.isValidPhoneDDD9, key: 'mask-ddd9' },
		{ sel: '.mask-dmy', errorClass: 'error-dmy', validate: MV.isValidDateDMY, key: 'mask-dmy' },
		{ sel: '.mask-mdy', errorClass: 'error-mdy', validate: MV.isValidDateMDY, key: 'mask-mdy' },
		{ sel: '.mask-hms', errorClass: 'error-hms', validate: MV.isValidTimeHMS, key: 'mask-hms' },
		{ sel: '.mask-hm', errorClass: 'error-hm', validate: MV.isValidTimeHM, key: 'mask-hm' },
		{ sel: '.mask-dmyhm', errorClass: 'error-dmyhm', validate: MV.isValidDateDMYHM, key: 'mask-dmyhm' },
		{ sel: '.mask-mdyhm', errorClass: 'error-mdyhm', validate: MV.isValidDateMDYHM, key: 'mask-mdyhm' },
		{ sel: '.mask-my', errorClass: 'error-my', validate: MV.isValidDateMY, key: 'mask-my' },
		{ sel: '.mask-ccs', errorClass: 'error-ccs', validate: MV.isValidCreditCard, key: 'mask-ccs' },
		{ sel: '.mask-cch', errorClass: 'error-cch', validate: MV.isValidCreditCard, key: 'mask-cch' },
		{ sel: '.mask-ccmy', errorClass: 'error-ccmy', validate: MV.isValidExpiryMMYY, key: 'mask-ccmy' },
		{ sel: '.mask-ccmyy', errorClass: 'error-ccmyy', validate: MV.isValidExpiryMMYYYY, key: 'mask-ccmyy' },
		{ sel: '.mask-ipv4', errorClass: 'error-ipv4', validate: MV.isValidIPv4, key: 'mask-ipv4' },
	];
};

/**
 * Resolve the .mask-error node for an input.
 *
 * @param {jQuery} $
 * @param {jQuery} $input
 * @param {string} errorClass
 * @param {Object} [opts]
 * @param {Function} [opts.resolveErrorElement]
 * @return {jQuery}
 */
CFKEF.resolveMaskErrorElement = function ($, $input, errorClass, opts) {
	opts = opts || {};
	if ($input.hasClass('hide-fme-mask-input')) {
		return $();
	}
	if (typeof opts.resolveErrorElement === 'function') {
		return opts.resolveErrorElement($input, errorClass);
	}
	if ($input.hasClass('cool-form__field')) {
		return $input.closest('.cool-form__field-group').find('.' + errorClass);
	}
	if ($input.hasClass('ehp-form__field')) {
		return $input.closest('.ehp-form__field-group').find('.' + errorClass);
	}
	return $input.closest('.elementor-field-group').find('.' + errorClass);
};

/**
 * Find mask validation def matching an input's classes.
 *
 * @param {jQuery} $input
 * @return {Object|null}
 */
CFKEF.findMaskDefForInput = function ($input) {
	var defs = CFKEF.getMaskValidationDefs();
	for (var i = 0; i < defs.length; i++) {
		var cls = defs[i].sel.replace(/^\./, '');
		if ($input.hasClass(cls)) {
			return defs[i];
		}
	}
	return null;
};

/**
 * Bind blur/input validation for one mask selector.
 *
 * @param {jQuery} $
 * @param {string} selector
 * @param {string} errorClass
 * @param {Function} validationFunction
 * @param {string} errorMessage
 * @param {Object} [opts]
 */
CFKEF.bindMaskFieldValidation = function ($, selector, errorClass, validationFunction, errorMessage, opts) {
	opts = opts || {};

	$(document).on('blur', selector, function () {
		var $input = $(this);
		if (typeof opts.shouldHandle === 'function' && !opts.shouldHandle($input)) {
			return;
		}
		if ($input.hasClass('hide-fme-mask-input')) {
			return;
		}

		var val = $input.val();
		var $errorEl = CFKEF.resolveMaskErrorElement($, $input, errorClass, opts);

		if (String(val).length === 1 && !/\d/.test(val)) {
			$input.val('');
			$errorEl.hide().text('');
			return;
		}

		if (val !== '' && !validationFunction(val)) {
			$errorEl.text(errorMessage).css('display', 'flex').hide().fadeIn(200);
		} else {
			$errorEl.fadeOut(100, function () {
				$(this).css('display', 'none');
			});
		}
	});

	$(document).on('input', selector, function () {
		var $input = $(this);
		if (typeof opts.shouldHandle === 'function' && !opts.shouldHandle($input)) {
			return;
		}
		if (typeof opts.onInput === 'function') {
			opts.onInput($input, errorClass, validationFunction);
		}

		var $errorEl = CFKEF.resolveMaskErrorElement($, $input, errorClass, opts);
		if ($errorEl.is(':visible')) {
			var val = $input.val();
			if (validationFunction(val)) {
				$errorEl.fadeOut(100, function () {
					$(this).css('display', 'none');
				});
			}
		}
	});
};

/**
 * Bind blur/input validation for all shared mask defs.
 *
 * @param {jQuery} $
 * @param {Object} [opts]
 */
CFKEF.bindAllMaskValidations = function ($, opts) {
	opts = opts || {};
	var msgs = opts.errorMessages || (window.fmeData && window.fmeData.errorMessages) || {};
	var defs = CFKEF.getMaskValidationDefs();

	defs.forEach(function (def) {
		if (typeof def.validate !== 'function') {
			return;
		}
		CFKEF.bindMaskFieldValidation(
			$,
			def.sel,
			def.errorClass,
			def.validate,
			msgs[def.key] || def.key,
			opts
		);
	});
};

/**
 * Actively validate mask inputs in a form; show errors and optionally scroll.
 *
 * @param {jQuery} $
 * @param {jQuery} $form
 * @param {Object} [opts]
 * @return {boolean} true if submit must be blocked
 */
CFKEF.maskValidationBlocksSubmit = function ($, $form, opts) {
	opts = opts || {};
	var msgs = opts.errorMessages || (window.fmeData && window.fmeData.errorMessages) || {};
	var blocked = false;
	var $scrollTarget = null;
	var inputSelector = opts.inputSelector || 'input.fme-mask-input';

	$form.find(inputSelector).not('.hide-fme-mask-input').each(function () {
		var $inp = $(this);
		if (typeof opts.shouldValidate === 'function' && !opts.shouldValidate($inp)) {
			return;
		}

		var val = String($inp.val() || '').trim();
		if (val.length === 1 && !/\d/.test(val)) {
			$inp.val('');
			val = '';
		}

		var def = CFKEF.findMaskDefForInput($inp);
		if (!def || typeof def.validate !== 'function') {
			return;
		}

		var $err = CFKEF.resolveMaskErrorElement($, $inp, def.errorClass, opts);
		if (val !== '' && !def.validate(val)) {
			$err.text(msgs[def.key] || def.key).css('display', 'flex').show();
			blocked = true;
			if (!$scrollTarget || !$scrollTarget.length) {
				$scrollTarget = $err;
			}
		} else {
			$err.text('').hide().css('display', 'none');
		}
	});

	if (blocked && $scrollTarget && $scrollTarget.length && opts.scroll !== false) {
		var off = $scrollTarget.offset();
		if (off) {
			$('html, body').animate({ scrollTop: off.top - (opts.scrollOffset || 200) }, opts.scrollDuration || 300);
		}
	}

	return blocked;
};

/**
 * Shared Cool Form / Hello Plus (and similar) mask submit click guard.
 *
 * @param {jQuery} $
 * @param {string} selector
 * @param {Object} [opts]
 */
CFKEF.bindMaskSubmitGuard = function ($, selector, opts) {
	opts = opts || {};
	var delay = opts.delay != null ? opts.delay : 400;
	var preventDefaultAlways = !!opts.preventDefaultAlways;
	var checkRequiredEmpty = !!opts.checkRequiredEmpty;
	var addWaitingClass = !!opts.addWaitingClass;
	var checkHtml5Validity = !!opts.checkHtml5Validity;
	var scrollOffset = opts.scrollOffset || 200;
	var scrollDuration = opts.scrollDuration || 400;
	var errorDisplayFlex = !!opts.errorDisplayFlex;
	var onBefore = typeof opts.onBefore === 'function' ? opts.onBefore : null;
	var onAfterValid = typeof opts.onAfterValid === 'function' ? opts.onAfterValid : null;
	var onBlocked = typeof opts.onBlocked === 'function' ? opts.onBlocked : null;
	var shouldHandle = typeof opts.shouldHandle === 'function' ? opts.shouldHandle : null;

	$(document).on('click', selector, function (e) {
		var $submitBtn = $(this);

		if (shouldHandle && !shouldHandle($submitBtn, e)) {
			return;
		}

		if (preventDefaultAlways) {
			e.preventDefault();
		}

		var $form = $submitBtn.closest('form');

		if ($submitBtn.data('clicked')) {
			e.preventDefault();
			return;
		}
		$submitBtn.data('clicked', true);

		if (onBefore) {
			onBefore($submitBtn, $form, e);
		}

		$form.find('input').trigger('blur');
		if (addWaitingClass && $form[0]) {
			$form[0].classList.add('elementor-form-waiting');
		}

		setTimeout(function () {
			var hasError = false;
			var $errors = $form.find('.mask-error').filter(function () {
				var visible = errorDisplayFlex
					? $(this).css('display') === 'flex'
					: $(this).is(':visible');
				return $(this).text().trim() !== '' && visible;
			});

			if ($errors.length > 0) {
				hasError = true;
				$('html, body').animate(
					{ scrollTop: $errors.first().offset().top - scrollOffset },
					scrollDuration
				);
			}

			if (checkRequiredEmpty) {
				var $emptyRequiredMasked = $form.find('input[required]').filter(function () {
					if ($(this).hasClass('hide-fme-mask-input')) {
						return false;
					}
					var val = String($(this).val() || '').trim();
					var isVisible = errorDisplayFlex
						? $(this).css('display') === 'flex'
						: $(this).is(':visible');
					return isVisible && (val === '' || /^[\s_\-\(\)\.:/]+$/.test(val));
				});

				if ($emptyRequiredMasked.length > 0) {
					hasError = true;
					$('html, body').animate(
						{ scrollTop: $emptyRequiredMasked.first().offset().top - scrollOffset },
						scrollDuration
					);
					$emptyRequiredMasked.first().focus();
				}
			}

			if (hasError || (checkHtml5Validity && $form[0] && !$form[0].checkValidity())) {
				if (addWaitingClass && $form[0]) {
					$form[0].classList.remove('elementor-form-waiting');
				}
				$submitBtn.data('clicked', false);
				e.preventDefault();
				if (onBlocked) {
					onBlocked($submitBtn, $form, e);
				}
				return;
			}

			if (addWaitingClass && $form[0]) {
				$form[0].classList.remove('elementor-form-waiting');
			}

			if (onAfterValid) {
				onAfterValid($submitBtn, $form, e);
				$submitBtn.data('clicked', false);
				return;
			}

			if ($form[0] && typeof $form[0].requestSubmit === 'function') {
				$form[0].requestSubmit();
			}
			$submitBtn.data('clicked', false);
		}, delay);
	});
};

/**
 * Atomic-style form submit guard: validate masks then allow one re-entrant submit.
 *
 * @param {jQuery} $
 * @param {Object} [opts]
 */
CFKEF.bindMaskAllowOnceSubmitGuard = function ($, opts) {
	opts = opts || {};
	if (CFKEF._maskAllowOnceSubmitGuardBound) {
		return;
	}
	CFKEF._maskAllowOnceSubmitGuardBound = true;

	var formMatch = typeof opts.formMatch === 'function' ? opts.formMatch : function () { return true; };
	var hasMaskInputs = typeof opts.hasMaskInputs === 'function'
		? opts.hasMaskInputs
		: function ($form) {
			return $form.find('input.fme-mask-input').not('.hide-fme-mask-input').length > 0;
		};
	var validate = typeof opts.validate === 'function' ? opts.validate : function ($form) {
		return CFKEF.maskValidationBlocksSubmit($, $form, opts);
	};
	var clickSelector = opts.clickSelector || 'button[type="submit"]';
	var allowOnce = false;

	$(document).on('submit', 'form', function (e) {
		var formEl = this;
		var $form = $(formEl);
		if (!formMatch($form)) {
			return;
		}
		if (!hasMaskInputs($form)) {
			return;
		}

		if (allowOnce) {
			allowOnce = false;
			return;
		}

		if (validate($form)) {
			e.preventDefault();
			e.stopImmediatePropagation();
			return;
		}

		e.preventDefault();
		e.stopImmediatePropagation();
		if (typeof formEl.requestSubmit === 'function') {
			allowOnce = true;
			formEl.requestSubmit();
		} else {
			formEl.submit();
		}
	});

	$(document).on('click', clickSelector, function (e) {
		var $btn = $(this);
		var $form = $btn.closest('form');
		if (!$form.length || !formMatch($form) || !hasMaskInputs($form)) {
			return;
		}
		if (validate($form)) {
			e.preventDefault();
			e.stopPropagation();
		}
	});
};
