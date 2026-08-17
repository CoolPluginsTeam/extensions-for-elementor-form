/**
 * Shared mask class / error / placeholder UI for Elementor, Cool Form, Hello Plus, and Atomic Form.
 * Call CFKEF.applyMaskUi($input) after data-mask attributes are set on the input.
 */
window.CFKEF = window.CFKEF || {};

CFKEF.MASK_KEY_MAP = {
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

/**
 * @param {jQuery} $input
 * @returns {boolean} true if a known mask was applied
 */
CFKEF.applyMaskUi = function ($input) {
  if (!$input || !$input.length) {
    return false;
  }

  var el = $input[0];
  var masks = CFKEF.MASK_KEY_MAP;
  var maskKey = el.dataset.mask;
  var timemaskFormat = el.dataset.timemaskFormat;
  var phoneFormat = el.dataset.phoneFormat;
  var creditcardOptions = el.dataset.creditcardOptions;
  var autoPlaceholder = el.dataset.autoPlaceholder;
  var brazilianFormats = el.dataset.brazilianFormats;

  if (!masks[maskKey]) {
    return false;
  }

  function addMask(maskClass, errorClass, placeholder) {
    $input.addClass(maskClass);
    $input.after('<div class="mask-error ' + errorClass + '"></div>');
    if (autoPlaceholder === 'yes' && placeholder) {
      $input.attr('placeholder', placeholder);
    }
  }

  function ensureCardLogo($error) {
    if ($error.next('.card-logo').length === 0) {
      $error.after('<img id="card-logo" class="card-logo" src="" alt="Card Logo">');
    }
  }

  if (masks[maskKey] === 'phone') {
    $input.attr('inputmode', 'tel');
  } else {
    $input.attr('inputmode', 'numeric');
  }

  if (masks[maskKey] === 'brazilian_formats') {
    switch (brazilianFormats) {
      case 'fme_cnpj':
        addMask('mask-cnpj', 'error-cnpj', 'XX.XXX.XXX/XXXX-XX');
        break;
      case 'fme_cep':
        addMask('mask-cep', 'error-cep', 'XXXXX-XXX');
        break;
      case 'fme_cpf':
      default:
        addMask('mask-cpf', 'error-cpf', 'XXX.XXX.XXX-XX');
        break;
    }
  }
  if (masks[maskKey] === '##/##/####') {
    addMask('mask-dmy', 'error-dmy', 'XX/XX/XXXX');
  }
  if (masks[maskKey] === '##.###.###/####-##') {
    addMask('mask-cnpj', 'error-cnpj', 'XX.XXX.XXX/XXXX-XX');
  }
  if (masks[maskKey] === '###.###.###-##') {
    addMask('mask-cpf', 'error-cpf', 'XXX.XXX.XXX-XX');
  }
  if (masks[maskKey] === '#####-###') {
    addMask('mask-cep', 'error-cep', 'XXXXX-XXX');
  }
  if (masks[maskKey] === '(###) ###-####') {
    addMask('mask-phus', 'error-phus', '(XXX) XXX-XXXX');
  }
  if (masks[maskKey] === 'phone') {
    switch (phoneFormat) {
      case 'phone_usa':
        addMask('mask-phus', 'error-phus', '(XXX) XXX-XXXX');
        break;
      case 'phone_d8':
        addMask('mask-ph8', 'error-ph8', 'XXXX-XXXX');
        break;
      case 'phone_ddd8':
        addMask('mask-ddd8', 'error-ddd8', '(XX) XXXX-XXXX');
        break;
      case 'phone_ddd9':
        addMask('mask-ddd9', 'error-ddd9', '(XX) XXXXX-XXXX');
        break;
      default:
        addMask('mask-ph8', 'error-ph8', 'XXXX-XXXX');
        break;
    }
  }
  if (masks[maskKey] === '####-####') {
    addMask('mask-ph8', 'error-ph8', 'XXXX-XXXX');
  }
  if (masks[maskKey] === '(##) ####-####') {
    addMask('mask-ddd8', 'error-ddd8', '(XX) XXXX-XXXX');
  }
  if (masks[maskKey] === '(##) #####-####') {
    addMask('mask-ddd9', 'error-ddd9', '(XX) XXXXX-XXXX');
  }
  if (masks[maskKey] === '##:##:##') {
    switch (timemaskFormat) {
      case 'two':
        addMask('mask-hms', 'error-hms', 'XX:XX:XX');
        break;
      case 'three':
        addMask('mask-dmy', 'error-dmy', 'XX/XX/XXXX');
        break;
      case 'four':
        addMask('mask-mdy', 'error-mdy', 'XX/XX/XXXX');
        break;
      case 'five':
        addMask('mask-dmyhm', 'error-dmyhm', 'XX/XX/XXXX XX:XX');
        break;
      case 'six':
        addMask('mask-mdyhm', 'error-mdyhm', 'XX/XX/XXXX XX:XX');
        break;
      case 'seven':
        addMask('mask-my', 'error-my', 'XX/XXXX');
        break;
      case 'one':
      default:
        addMask('mask-hm', 'error-hm', 'XX:XX');
        break;
    }
  }
  if (masks[maskKey] === '##:##') {
    addMask('mask-hm', 'error-hm', 'XX:XX');
  }
  if (masks[maskKey] === '##/##/#### ##:##') {
    addMask('mask-dmyhm', 'error-dmyhm', 'XX/XX/XXXX XX:XX');
  }
  if (masks[maskKey] === '####-####-####-####') {
    switch (creditcardOptions) {
      case 'space':
        addMask('mask-ccs', 'error-ccs', 'XXXX XXXX XXXX XXXX');
        ensureCardLogo($input.next('.error-ccs'));
        break;
      case 'credit_card_date':
        addMask('mask-ccmy', 'error-ccmy', 'XX/XX');
        break;
      case 'credit_card_expiry_date':
        addMask('mask-ccmyy', 'error-ccmyy', 'XX/XXXX');
        break;
      case 'hyphen':
      default:
        addMask('mask-cch', 'error-cch', 'XXXX-XXXX-XXXX-XXXX');
        ensureCardLogo($input.next('.error-cch'));
        break;
    }
  }
  if (masks[maskKey] === '##/##') {
    addMask('mask-ccmy', 'error-ccmy', 'XX/XX');
  }
  if (masks[maskKey] === '##/####') {
    addMask('mask-my', 'error-my', 'XX/XXXX');
  }
  if (masks[maskKey] === '###.###.###.###') {
    addMask('mask-ipv4', 'error-ipv4', 'XXX.XXX.XXX.XXX');
  }
  if (masks[maskKey] === '###.###.###.###.###,##') {
    $input.addClass('mask-moneyc');
    $input.after('<div class="mask-error error-moneyc"></div>');
    if (autoPlaceholder === 'yes') {
      var moneyPrefix = $input.data('moneymask-prefix') !== '' ? $input.data('moneymask-prefix') : '$';
      var format = $input.data('moneymask-format') === 'dot' ? ',' : '.';
      $input.attr('placeholder', moneyPrefix + '0' + format + '00');
    }
  }

  if ($input.hasClass('mask-cnpj')) {
    $input.attr('inputmode', 'text');
  }

  return true;
};
