/**
 * Input masks for Elementor atomic form (e-form-input), mirroring assets/js/inputmask/new-input-mask.js.
 */
(function ($) {
	'use strict';

	function prepareMaskInputs($root) {
		$root.find('input.fme-mask-input').each(function () {
			const input = this;
			let maskClass,
				maskFormat,
				maskPrefix,
				maskDecimalPlaces,
				maskTimeMaskFormat,
				phoneFormat,
				creditCardOptions,
				maskAutoPlaceholder,
				brazilianFormats;

			input.classList.forEach(function (className) {
				if (className.includes('mask_control_@')) {
					maskClass = className;
				}
				if (className.includes('money_mask_format_@')) {
					maskFormat = className;
				}
				if (className.includes('mask_prefix_@')) {
					maskPrefix = className;
				}
				if (className.includes('mask_decimal_places_@')) {
					maskDecimalPlaces = className;
				}
				if (className.includes('mask_time_mask_format_@')) {
					maskTimeMaskFormat = className;
				}
				if (className.includes('fme_phone_format_@')) {
					phoneFormat = className;
				}
				if (className.includes('credit_card_options_@')) {
					creditCardOptions = className;
				}
				if (className.includes('mask_auto_placeholder_@')) {
					maskAutoPlaceholder = className;
				}
				if (className.includes('fme_brazilian_formats_@')) {
					brazilianFormats = className;
				}
			});

			if (maskClass) {
				maskClass = maskClass.split('@');
			}
			if (maskFormat) {
				maskFormat = maskFormat.split('@');
			}
			if (maskPrefix) {
				maskPrefix = maskPrefix.split('@');
			}
			if (maskDecimalPlaces) {
				maskDecimalPlaces = maskDecimalPlaces.split('@');
			}
			if (maskTimeMaskFormat) {
				maskTimeMaskFormat = maskTimeMaskFormat.split('@');
			}
			if (phoneFormat) {
				phoneFormat = phoneFormat.split('@');
			}
			if (creditCardOptions) {
				creditCardOptions = creditCardOptions.split('@');
			}
			if (maskAutoPlaceholder) {
				maskAutoPlaceholder = maskAutoPlaceholder.split('@');
			}
			if (brazilianFormats) {
				brazilianFormats = brazilianFormats.split('@');
			}

			if (!$(input).data('mask')) {
				input.setAttribute('data-mask', maskClass && maskClass[1] ? maskClass[1] : '');
			}

			input.setAttribute('data-moneymask-format', maskFormat && maskFormat[1] ? maskFormat[1] : 'dot');
			input.setAttribute('data-moneymask-prefix', maskPrefix && maskPrefix[1] ? maskPrefix[1] : '');
			input.setAttribute('data-decimal-places', maskDecimalPlaces && maskDecimalPlaces[1] ? maskDecimalPlaces[1] : '2');
			input.setAttribute('data-timemask-format', maskTimeMaskFormat && maskTimeMaskFormat[1] ? maskTimeMaskFormat[1] : 'one');
			input.setAttribute('data-phone-format', phoneFormat && phoneFormat[1] ? phoneFormat[1] : 'phone_usa');
			input.setAttribute('data-creditcard-options', creditCardOptions && creditCardOptions[1] ? creditCardOptions[1] : 'hyphen');
			input.setAttribute('data-auto-placeholder', maskAutoPlaceholder && maskAutoPlaceholder[1] ? maskAutoPlaceholder[1] : '');
			input.setAttribute('data-brazilian-formats', brazilianFormats && brazilianFormats[1] ? brazilianFormats[1] : '');
		});
	}

	// -------------------------------------------------------------------------
	// Mask validation (same rules as assets/js/inputmask/custom-mask-script.js),
	// scoped to atomic forms so .mask-error resolves under .ccfef-mask-wrapper.
	// -------------------------------------------------------------------------

	function getAtomicMaskErrorElement($input, errorClass) {
		if ($input.hasClass('hide-fme-mask-input')) {
			return $();
		}
		var $wrap = $input.closest('.ccfef-mask-wrapper');
		if ($wrap.length) {
			var $err = $wrap.find('.' + errorClass);
			if (!$err.length) {
				$err = $input.siblings('.' + errorClass);
			}
			if (!$err.length) {
				$err = $input.next('.mask-error');
			}
			return $err;
		}
		return $input.closest('.elementor-field-group').find('.' + errorClass);
	}

	function isAtomicMaskField($input) {
		return $input.closest('[data-e-type].e-form-base').length > 0;
	}

	function getAtomicFmeErrorMessages() {
		if (typeof window.fmeData !== 'undefined' && window.fmeData.errorMessages) {
			return window.fmeData.errorMessages;
		}
		return {
			'mask-cnpj': 'Invalid CNPJ.',
			'mask-cpf': 'Invalid CPF.',
			'mask-cep': 'Invalid CEP (XXXXX-XXX).',
			'mask-phus': 'Invalid number: (123) 456-7890',
			'mask-ph8': 'Invalid number: 1234-5678',
			'mask-ddd8': 'Invalid number: (DDD) 1234-5678',
			'mask-ddd9': 'Invalid number: (DDD) 91234-5678',
			'mask-dmy': 'Invalid date: dd/mm/yyyy',
			'mask-mdy': 'Invalid date: mm/dd/yyyy',
			'mask-hms': 'Invalid time: hh:mm:ss',
			'mask-hm': 'Invalid time: hh:mm',
			'mask-dmyhm': 'Invalid date: dd/mm/yyyy hh:mm',
			'mask-mdyhm': 'Invalid date: mm/dd/yyyy hh:mm',
			'mask-my': 'Invalid date: mm/yyyy',
			'mask-ccs': 'Invalid credit card number.',
			'mask-cch': 'Invalid credit card number.',
			'mask-ccmy': 'Invalid date.',
			'mask-ccmyy': 'Invalid date.',
			'mask-ipv4': 'Invalid IPv4 address.',
		};
	}

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

	function isValidDateDMY(dateStr) {
		return isValidDateTime(dateStr, 'DMY');
	}
	function isValidDateMDY(dateStr) {
		return isValidDateTime(dateStr, 'MDY');
	}
	function isValidTimeHMS(timeStr) {
		return isValidDateTime(timeStr, 'HMS');
	}
	function isValidTimeHM(timeStr) {
		return isValidDateTime(timeStr, 'HM');
	}
	function isValidDateDMYHM(dateTimeStr) {
		return isValidDateTime(dateTimeStr, 'DMY-HM');
	}
	function isValidDateMDYHM(dateTimeStr) {
		return isValidDateTime(dateTimeStr, 'MDY-HM');
	}
	function isValidDateMY(dateStr) {
		return isValidDateTime(dateStr, 'MY');
	}
	function isValidExpiryMMYY(dateStr) {
		return isValidExpiryDate(dateStr, 'MM/YY');
	}
	function isValidExpiryMMYYYY(dateStr) {
		return isValidExpiryDate(dateStr, 'MM/YYYY');
	}

	function isValidCreditCard(cardNumber) {
		var cleaned = cardNumber.replace(/\D/g, '');
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
			var weights = length === 12 ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2] : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
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
		cpf = cpf.replace(/\D/g, '');
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
			return result === 10 ? 0 : result === parseInt(str.charAt(length), 10);
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
		var octets = ip.split('.');
		return octets.every(function (octet) {
			var num = parseInt(octet, 10);
			return num >= 0 && num <= 255;
		});
	}

	/** Shared list: blur/input binding + synchronous submit-time checks */
	var ATOMIC_MASK_VALIDATION_DEFS = [
		{ sel: '.mask-cnpj', errorClass: 'error-cnpj', validate: isValidCNPJ, key: 'mask-cnpj' },
		{ sel: '.mask-cpf', errorClass: 'error-cpf', validate: isValidCPF, key: 'mask-cpf' },
		{ sel: '.mask-cep', errorClass: 'error-cep', validate: isValidCEP, key: 'mask-cep' },
		{ sel: '.mask-phus', errorClass: 'error-phus', validate: isValidPhoneUSA, key: 'mask-phus' },
		{ sel: '.mask-ph8', errorClass: 'error-ph8', validate: isValidPhone8, key: 'mask-ph8' },
		{ sel: '.mask-ddd8', errorClass: 'error-ddd8', validate: isValidPhoneDDD8, key: 'mask-ddd8' },
		{ sel: '.mask-ddd9', errorClass: 'error-ddd9', validate: isValidPhoneDDD9, key: 'mask-ddd9' },
		{ sel: '.mask-dmy', errorClass: 'error-dmy', validate: isValidDateDMY, key: 'mask-dmy' },
		{ sel: '.mask-mdy', errorClass: 'error-mdy', validate: isValidDateMDY, key: 'mask-mdy' },
		{ sel: '.mask-hms', errorClass: 'error-hms', validate: isValidTimeHMS, key: 'mask-hms' },
		{ sel: '.mask-hm', errorClass: 'error-hm', validate: isValidTimeHM, key: 'mask-hm' },
		{ sel: '.mask-dmyhm', errorClass: 'error-dmyhm', validate: isValidDateDMYHM, key: 'mask-dmyhm' },
		{ sel: '.mask-mdyhm', errorClass: 'error-mdyhm', validate: isValidDateMDYHM, key: 'mask-mdyhm' },
		{ sel: '.mask-my', errorClass: 'error-my', validate: isValidDateMY, key: 'mask-my' },
		{ sel: '.mask-ccs', errorClass: 'error-ccs', validate: isValidCreditCard, key: 'mask-ccs' },
		{ sel: '.mask-cch', errorClass: 'error-cch', validate: isValidCreditCard, key: 'mask-cch' },
		{ sel: '.mask-ccmy', errorClass: 'error-ccmy', validate: isValidExpiryMMYY, key: 'mask-ccmy' },
		{ sel: '.mask-ccmyy', errorClass: 'error-ccmyy', validate: isValidExpiryMMYYYY, key: 'mask-ccmyy' },
		{ sel: '.mask-ipv4', errorClass: 'error-ipv4', validate: isValidIPv4, key: 'mask-ipv4' },
	];

	function findAtomicMaskDefForInput($input) {
		var cls = '';
		for (var i = 0; i < ATOMIC_MASK_VALIDATION_DEFS.length; i++) {
			cls = ATOMIC_MASK_VALIDATION_DEFS[i].sel.replace(/^\./, '');
			if ($input.hasClass(cls)) {
				return ATOMIC_MASK_VALIDATION_DEFS[i];
			}
		}
		return null;
	}

	/**
	 * Run the same checks as blur validation; show errors and return true if submit must be blocked.
	 */
	function atomicFormMaskValidationBlocksSubmit($form) {
		var msg = getAtomicFmeErrorMessages();
		var blocked = false;
		var $scrollTarget = null;

		$form.find('input.fme-mask-input').not('.hide-fme-mask-input').each(function () {
			var $inp = $(this);

			if($inp.closest('.cfef-atomic-field-group').hasClass('cfef-hidden')){
				return;
			}

			var val = String($inp.val() || '').trim();
			if (val.length === 1 && !/\d/.test(val)) {
				$inp.val('');
				val = '';
			}

			var def = findAtomicMaskDefForInput($inp);
			if (!def) {
				return;
			}

			var $err = getAtomicMaskErrorElement($inp, def.errorClass);
			if (val !== '' && !def.validate(val)) {
				var text = msg[def.key] || def.key;
				$err.text(text).css('display', 'flex').show();
				blocked = true;
				if (!$scrollTarget || !$scrollTarget.length) {
					$scrollTarget = $err;
				}
			} else {
				$err.text('').hide().css('display', 'none');
			}
		});

		if (blocked && $scrollTarget && $scrollTarget.length) {
			var off = $scrollTarget.offset();
			if (off) {
				$('html, body').animate({ scrollTop: off.top - 200 }, 300);
			}
		}

		return blocked;
	}

	var cflAtomicMaskAllowSubmitOnce = false;
	var atomicMaskSubmitGuardBound = false;

	function bindAtomicFormMaskSubmitGuard() {
		if (atomicMaskSubmitGuardBound) {
			return;
		}
		atomicMaskSubmitGuardBound = true;

		$(document).on('submit', 'form', function (e) {
			var formEl = this;
			var $form = $(formEl);
			if (!$form.closest('[data-e-type].e-form-base').length) {
				return;
			}
			if (!$form.find('input.fme-mask-input').not('.hide-fme-mask-input').length) {
				return;
			}

			if (cflAtomicMaskAllowSubmitOnce) {
				cflAtomicMaskAllowSubmitOnce = false;
				return;
			}

			if (atomicFormMaskValidationBlocksSubmit($form)) {
				e.preventDefault();
				e.stopImmediatePropagation();
				return;
			}

			e.preventDefault();
			e.stopImmediatePropagation();
			if (typeof formEl.requestSubmit === 'function') {
				cflAtomicMaskAllowSubmitOnce = true;
				formEl.requestSubmit();
			} else {
				formEl.submit();
			}
		});

		$(document).on('click', '[data-e-type].e-form-base button[type="submit"]', function (e) {
			var $btn = $(this);
			var $actualForm = $btn.closest('form');
			if (!$actualForm.length) {
				return;
			}
			if (!$actualForm.find('input.fme-mask-input').not('.hide-fme-mask-input').length) {
				return;
			}
			if (atomicFormMaskValidationBlocksSubmit($actualForm)) {
				e.preventDefault();
				e.stopPropagation();
			}
		});
	}

	function validateAtomicMaskInput(selector, errorClass, validationFunction, errorMessage) {
		$(document).on('blur', selector, function () {
			var $input = $(this);

			if (!isAtomicMaskField($input)) {
				return;
			}
			if ($input.hasClass('hide-fme-mask-input')) {
				return;
			}
			var val = $input.val();
			var $errorEl = getAtomicMaskErrorElement($input, errorClass);

			if (val.length === 1 && !/\d/.test(val)) {
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
			if (!isAtomicMaskField($input)) {
				return;
			}
			var $errorEl = getAtomicMaskErrorElement($input, errorClass);
			if ($errorEl.is(':visible')) {
				var val = $input.val();
				if (validationFunction(val)) {
					$errorEl.fadeOut(100, function () {
						$(this).css('display', 'none');
					});
				}
			}
		});
	}

	var atomicMaskValidationBound = false;

	function bindAtomicMaskValidation() {
		if (atomicMaskValidationBound) {
			return;
		}
		atomicMaskValidationBound = true;

		var msg = getAtomicFmeErrorMessages();
		ATOMIC_MASK_VALIDATION_DEFS.forEach(function (v) {
			var text = msg[v.key] || v.key;
			validateAtomicMaskInput(v.sel, v.errorClass, v.validate, text);
		});
	}

	function applyMasksInRoot($root) {
		const masks = {
			'ev-phone': 'phone',
			'ev-tel': '####-####',
			'ev-tel-ddd': '(##) ####-####',
			'ev-tel-ddd9': '(##) #####-####',
			'ev-tel-us': '(###) ###-####',
			'ev-cpf': '###.###.###-##',
			'ev-cnpj': '##.###.###/####-##',
			'ev-money': '###.###.###.###.###,##',
			'ev-ccard': '####-####-####-####',
			'ev-ccard-valid': '##/##',
			'ev-cep': '#####-###',
			'ev-time': '##:##:##',
			'ev-date': '##/##/####',
			'ev-date_time': '##/##/#### ##:##',
			'ev-ip-address': '###.###.###.###',
			'ev-br_fr': 'brazilian_formats',
		};

		$root.find('input[data-mask]').each(function () {
			const $input = $(this);
			if ($input.data('cflAtomicMaskUi')) {
				return;
			}
			const maskKey = this.dataset.mask;
			const timemaskFormat = this.dataset.timemaskFormat;
			const phoneFormat = this.dataset.phoneFormat;
			const creditcardOptions = this.dataset.creditcardOptions;
			const autoPlaceholder = this.dataset.autoPlaceholder;
			const brazilianFormats = this.dataset.brazilianFormats;

			if (!masks[maskKey]) {
				return;
			}
			if (masks[maskKey] === 'phone') {
				$input.attr('inputmode', 'tel');
			} else {
				$input.attr('inputmode', 'numeric');
			}
			if (masks[maskKey] === 'brazilian_formats') {
				switch (brazilianFormats) {
					case 'fme_cpf':
						$input.addClass('mask-cpf');
						$input.after('<div class="mask-error error-cpf"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XXX.XXX.XXX-XX');
						}
						break;
					case 'fme_cnpj':
						$input.addClass('mask-cnpj');
						$input.after('<div class="mask-error error-cnpj"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX.XXX.XXX/XXXX-XX');
						}
						break;
					case 'fme_cep':
						$input.addClass('mask-cep');
						$input.after('<div class="mask-error error-cep"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XXXXX-XXX');
						}
						break;
					default:
						$input.addClass('mask-cpf');
						$input.after('<div class="mask-error error-cpf"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XXX.XXX.XXX-XX');
						}
				}
			}
			if (masks[maskKey] === '##/##/####') {
				$input.addClass('mask-dmy');
				$input.after('<div class="mask-error error-dmy"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XX/XX/XXXX');
				}
			}
			if (masks[maskKey] === '##.###.###/####-##') {
				$input.addClass('mask-cnpj');
				$input.after('<div class="mask-error error-cnpj"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XX.XXX.XXX/XXXX-XX');
				}
			}
			if (masks[maskKey] === '###.###.###-##') {
				$input.addClass('mask-cpf');
				$input.after('<div class="mask-error error-cpf"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XXX.XXX.XXX-XX');
				}
			}
			if (masks[maskKey] === '#####-###') {
				$input.addClass('mask-cep');
				$input.after('<div class="mask-error error-cep"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XXXXX-XXX');
				}
			}
			if (masks[maskKey] === '(###) ###-####') {
				$input.addClass('mask-phus');
				$input.after('<div class="mask-error error-phus"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', '(XXX) XXX-XXXX');
				}
			}
			if (masks[maskKey] === 'phone') {
				switch (phoneFormat) {
					case 'phone_usa':
						$input.addClass('mask-phus');
						$input.after('<div class="mask-error error-phus"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', '(XXX) XXX-XXXX');
						}
						break;
					case 'phone_d8':
						$input.addClass('mask-ph8');
						$input.after('<div class="mask-error error-ph8"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XXXX-XXXX');
						}
						break;
					case 'phone_ddd8':
						$input.addClass('mask-ddd8');
						$input.after('<div class="mask-error error-ddd8"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', '(XX) XXXX-XXXX');
						}
						break;
					case 'phone_ddd9':
						$input.addClass('mask-ddd9');
						$input.after('<div class="mask-error error-ddd9"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', '(XX) XXXXX-XXXX');
						}
						break;
					default:
						$input.addClass('mask-ph8');
						$input.after('<div class="mask-error error-ph8"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XXXX-XXXX');
						}
						break;
				}
			}
			if (masks[maskKey] === '####-####') {
				$input.addClass('mask-ph8');
				$input.after('<div class="mask-error error-ph8"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XXXX-XXXX');
				}
			}
			if (masks[maskKey] === '(##) ####-####') {
				$input.addClass('mask-ddd8');
				$input.after('<div class="mask-error error-ddd8"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', '(XX) XXXX-XXXX');
				}
			}
			if (masks[maskKey] === '(##) #####-####') {
				$input.addClass('mask-ddd9');
				$input.after('<div class="mask-error error-ddd9"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', '(XX) XXXXX-XXXX');
				}
			}
			if (masks[maskKey] === '##:##:##') {
				switch (timemaskFormat) {
					case 'one':
						$input.addClass('mask-hm');
						$input.after('<div class="mask-error error-hm"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX:XX');
						}
						break;
					case 'two':
						$input.addClass('mask-hms');
						$input.after('<div class="mask-error error-hms"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX:XX:XX');
						}
						break;
					case 'three':
						$input.addClass('mask-dmy');
						$input.after('<div class="mask-error error-dmy"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX/XX/XXXX');
						}
						break;
					case 'four':
						$input.addClass('mask-mdy');
						$input.after('<div class="mask-error error-mdy"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX/XX/XXXX');
						}
						break;
					case 'five':
						$input.addClass('mask-dmyhm');
						$input.after('<div class="mask-error error-dmyhm"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX/XX/XXXX XX:XX');
						}
						break;
					case 'six':
						$input.addClass('mask-mdyhm');
						$input.after('<div class="mask-error error-mdyhm"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX/XX/XXXX XX:XX');
						}
						break;
					case 'seven':
						$input.addClass('mask-my');
						$input.after('<div class="mask-error error-my"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX/XXXX');
						}
						break;
					default:
						$input.addClass('mask-hm');
						$input.after('<div class="mask-error error-hm"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX:XX');
						}
				}
			}
			if (masks[maskKey] === '##:##') {
				$input.addClass('mask-hm');
				$input.after('<div class="mask-error error-hm"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XX:XX');
				}
			}
			if (masks[maskKey] === '##/##/#### ##:##') {
				$input.addClass('mask-dmyhm');
				$input.after('<div class="mask-error error-dmyhm"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XX/XX/XXXX XX:XX');
				}
			}
			if (masks[maskKey] === '####-####-####-####') {
				switch (creditcardOptions) {
					case 'space':
						$input.addClass('mask-ccs');
						$input.after('<div class="mask-error error-ccs"></div>');
						const $errorDivCCS = $input.next('.error-ccs');
						if ($errorDivCCS.next('.card-logo').length === 0) {
							$errorDivCCS.after('<img id="card-logo" class="card-logo" src="" alt="Card Logo">');
						}
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XXXX XXXX XXXX XXXX');
						}
						break;
					case 'hyphen':
						$input.addClass('mask-cch');
						$input.after('<div class="mask-error error-cch"></div>');
						const $errorDiv = $input.next('.error-cch');
						if ($errorDiv.next('#card-logo').length === 0) {
							$errorDiv.after('<img id="card-logo" class="card-logo" src="" alt="Card Logo">');
						}
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XXXX-XXXX-XXXX-XXXX');
						}
						break;
					case 'credit_card_date':
						$input.addClass('mask-ccmy');
						$input.after('<div class="mask-error error-ccmy"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX/XX');
						}
						break;
					case 'credit_card_expiry_date':
						$input.addClass('mask-ccmyy');
						$input.after('<div class="mask-error error-ccmyy"></div>');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XX/XXXX');
						}
						break;
					default:
						$input.addClass('mask-ccs');
						$input.after('<div class="mask-error error-ccs"></div>');
						$('.mask-error').after('<img id="card-logo" class="card-logo" src="" alt="Card Logo">');
						if (autoPlaceholder === 'yes') {
							$input.attr('placeholder', 'XXXX XXXX XXXX XXXX');
						}
				}
			}
			if (masks[maskKey] === '##/##') {
				$input.addClass('mask-ccmy');
				$input.after('<div class="mask-error error-ccmy"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XX/XX');
				}
			}
			if (masks[maskKey] === '##/####') {
				$input.addClass('mask-my');
				$input.after('<div class="mask-error error-my"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XX/XXXX');
				}
			}
			if (masks[maskKey] === '###.###.###.###') {
				$input.addClass('mask-ipv4');
				$input.after('<div class="mask-error error-ipv4"></div>');
				if (autoPlaceholder === 'yes') {
					$input.attr('placeholder', 'XXX.XXX.XXX.XXX');
				}
			}
			if (masks[maskKey] === '###.###.###.###.###,##') {
				$input.addClass('mask-moneyc');
				$input.after('<div class="mask-error error-moneyc"></div>');
				if (autoPlaceholder === 'yes') {
					const moneyPrefix = $input.data('moneymask-prefix') !== '' ? $input.data('moneymask-prefix') : '$';
					const format = $input.data('moneymask-format') === 'dot' ? ',' : '.';
					$input.attr('placeholder', moneyPrefix + '0' + format + '00');
				}
			}

			if ($input.hasClass('mask-cnpj')) {
				$input.attr('inputmode', 'text');
			}

			$input.data('cflAtomicMaskUi', 1);
		});
	}

	function initAtomicMasks($scope) {
		if (!$scope || !$scope.length) {
			return;
		}
		prepareMaskInputs($scope);
		applyMasksInRoot($scope);
	}

	function init() {
		window.addEventListener('elementor/element/render', function (event) {
			const element = event.detail && event.detail.element;
			if (!element) {
				return;
			}
			const $el = $(element);

			if ($el.hasClass('e-form-input-base') || $el.hasClass('ccfef-mask-wrapper') || $el.has('.elementor-field-group.mask-enabled')) {
				const $form = $el.closest('form');

				initAtomicMasks($form.length ? $form : $el);
			}
		});

		document.addEventListener('DOMContentLoaded', function () {
			document.querySelectorAll('[data-e-type].e-form-base').forEach(function (el) {
				initAtomicMasks($(el));
			});
		});

		bindAtomicMaskValidation();
		bindAtomicFormMaskSubmitGuard();
	}

	if (typeof elementorFrontend !== 'undefined' && elementorFrontend.hooks) {
		$(window).on('elementor/frontend/init', function () {
			elementorFrontend.hooks.addAction('frontend/element_ready/e-form-input.default', function ($element) {
				const $form = $element.closest('form');
				initAtomicMasks($form.length ? $form : $element);
			});
		});
	}

	init();
})(jQuery);
