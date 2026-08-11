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
