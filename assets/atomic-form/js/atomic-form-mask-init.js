/**
 * Input masks for Elementor atomic form (e-form-input), mirroring assets/js/inputmask/new-input-mask.js.
 */
(function ($) {
	'use strict';

	function prepareMaskInputs($root) {
		$root.find('input.fme-mask-input').each(function () {
			if (window.CFKEF && typeof CFKEF.prepareMaskInputAttributes === 'function') {
				CFKEF.prepareMaskInputAttributes(this);
			}
		});
	}

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
		return {};
	}

	function atomicMaskOpts() {
		return {
			errorMessages: getAtomicFmeErrorMessages(),
			shouldHandle: isAtomicMaskField,
			shouldValidate: function ($inp) {
				return !$inp.closest('.cfef-atomic-field-group').hasClass('cfef-hidden');
			},
			resolveErrorElement: getAtomicMaskErrorElement,
		};
	}

	function bindAtomicFormMaskSubmitGuard() {
		CFKEF.bindMaskAllowOnceSubmitGuard($, Object.assign({}, atomicMaskOpts(), {
			formMatch: function ($form) {
				return $form.closest('[data-e-type].e-form-base').length > 0;
			},
			clickSelector: '[data-e-type].e-form-base button[type="submit"]',
			validate: function ($form) {
				return CFKEF.maskValidationBlocksSubmit($, $form, atomicMaskOpts());
			},
		}));
	}

	var atomicMaskValidationBound = false;

	function bindAtomicMaskValidation() {
		if (atomicMaskValidationBound) {
			return;
		}
		atomicMaskValidationBound = true;
		CFKEF.bindAllMaskValidations($, atomicMaskOpts());
	}

	function applyMasksInRoot($root) {
		$root.find('input[data-mask]').each(function () {
			const $input = $(this);
			if ($input.data('cflAtomicMaskUi')) {
				return;
			}
			if (typeof CFKEF !== 'undefined' && typeof CFKEF.applyMaskUi === 'function') {
				CFKEF.applyMaskUi($input);
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
