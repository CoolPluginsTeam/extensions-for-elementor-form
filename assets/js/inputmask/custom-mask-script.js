(function($) {
    $(document).ready(function() {
        // ----------------- Global Settings -----------------
        let currencySymbol = "$"; // Change to any currency symbol dynamically
        let decimalPlaces = 2; // Adjust between 2 or 3 dynamically
      
        // ----------------- Formatting Functions -----------------
        function formatString(digits, pattern) {
          let formatted = "";
          let index = 0;
          for (let char of pattern) {
              if (char === "#") {
                  if (index < digits.length) {
                      formatted += digits[index++];
                  } else {
                      break;
                  }
              } else {
                  formatted += char;
              }
          }
          return formatted;
        }
      
        // ----------------- Money Mask Formatting -----------------
        function formatMoneyInput(value, type, prefix,input) {
          let decimalSeparator = type === "C" ? "." : ",";
          let thousandSeparator = type === "C" ? "," : ".";
          let rawDigits = value.replace(/\D/g, "");
          decimalPlaces = 2;
          if(input !== ''){
            decimalPlaces = Number(input.dataset.decimalPlaces)
          }
      
          if (rawDigits.length === 0) {
              return `${prefix}0${decimalSeparator}${"0".repeat(decimalPlaces)}`;
          }
      
          while (rawDigits.length < decimalPlaces + 1) {
              rawDigits = "0" + rawDigits;
          }
      
          let cents = rawDigits.slice(-decimalPlaces);
          let wholeNumber = rawDigits.slice(0, -decimalPlaces).replace(/^0+/, "") || "0";
      
          wholeNumber = wholeNumber.replace(/\B(?=(\d{3})+(?!\d))/g, thousandSeparator);
          return `${prefix}${wholeNumber}${decimalSeparator}${cents}`;
        }
      
        function handleMoneyInput(event) {
          let input = event.target;
          let oldValue = input.value;
          let oldCursorPos = input.selectionStart;
      
          let moneyPrefix = input.dataset.moneymaskPrefix
          let moneymaskFormat = input.dataset.moneymaskFormat
          let type = (moneymaskFormat === 'dot' ? 'D' : 'C')
          let newValue = formatMoneyInput(input.value, type,moneyPrefix,input);
          if (input.value === newValue) return;
      
          input.value = newValue;
          let newCursorPos = oldCursorPos + (newValue.length - oldValue.length);
          setCaretPosition(input, newCursorPos);
        }
      
        // ----------------- Handling Focus & Blur for Money Mask -----------------
        $(document).on("focus", ".mask-moneyc", function() {
          let moneymaskFormat = $(this)[0].dataset.moneymaskFormat
          let type = (moneymaskFormat === 'dot' ? 'D' : 'C')
          let decimalSeparator = type === "C" ? "." : ",";
          let baseFormat = `${currencySymbol}0${decimalSeparator}${"0".repeat(decimalPlaces)}`;
      
          if ($(this).val().trim() === "") {
              $(this).val(baseFormat);
              // setCaretPosition(this, $(this).val().length - (decimalPlaces + 1));
          }
        });
      
        $(document).on("blur", ".mask-moneyc", function() {
          let moneymaskFormat = $(this)[0].dataset.moneymaskFormat
          let type = (moneymaskFormat === 'dot' ? 'D' : 'C')
          let decimalSeparator = type === "C" ? "." : ",";
          let baseFormat = `${currencySymbol}0${decimalSeparator}${"0".repeat(decimalPlaces)}`;
      
          let val = $(this).val().trim();
      
          let numericValue = val.replace(new RegExp(`[^0-9${decimalSeparator}]`, "g"), "").replace(decimalSeparator, ".");
      
          if (parseFloat(numericValue) === 0 || val === baseFormat) {
              $(this).val("");
          }
      });
      
      
        // ----------------- Formatting Credit Card -----------------
        function formatCreditCard(digits, formatType) {
          let cardType = detectCardType(digits);
          
          if (cardType === "American Express") {
              digits = digits.slice(0, 15);
              return formatType === "space" 
                  ? formatString(digits, "#### ###### #####")
                  : formatString(digits, "####-######-#####");
          } else {
              digits = digits.slice(0, 16);
              return formatType === "space" 
                  ? formatString(digits, "#### #### #### ####")
                  : formatString(digits, "####-####-####-####");
          }
        }
      
        // ----------------- Card Logos -----------------
        const cardLogos = {
            "Visa":  fmeData.pluginUrl+ "assets/svg-icons/visa-logo.svg",
            "MasterCard": fmeData.pluginUrl+ "assets/svg-icons/mastercard-logo.svg",
            "American Express": fmeData.pluginUrl+ "assets/svg-icons/amex-logo.svg",
            "Discover": fmeData.pluginUrl+ "assets/svg-icons/discover-logo.svg",
            "JCB": fmeData.pluginUrl+ "assets/svg-icons/jcb-logo.svg",
            "Diners Club": fmeData.pluginUrl+ "assets/svg-icons/cc-logo.svg",
            "Maestro": fmeData.pluginUrl+ "assets/svg-icons/maestro-logo.svg",
            "UnionPay": fmeData.pluginUrl+ "assets/svg-icons/cc-logo.svg",
            "RuPay": fmeData.pluginUrl+ "assets/svg-icons/repay-logo.svg",
            "Unknown": fmeData.pluginUrl+ "assets/svg-icons/cc-logo.svg"
        };
      
        // ----------------- Function to Detect Card Type -----------------
        function detectCardType(number) {
          const cleaned = number.replace(/\D/g, ""); // Remove non-numeric characters
      
          const cardPatterns = {
              "Visa": /^4/,
              "MasterCard": /^5[1-5]/, // MasterCard starts with 51-55
              "American Express": /^3[47]/, // AmEx starts with 34 or 37
              "Discover": /^6(?:011|5)/, // Discover starts with 6011 or 65
              "JCB": /^(?:2131|1800|35)/, // JCB starts with 2131, 1800, or 35
              "Diners Club": /^3(?:0[0-5]|[689])/, // Diners Club starts with 300-305, 36, or 38-39
              "UnionPay": /^(62|81)/, // UnionPay starts with 62 or 81
              "RuPay": /^(60|65|81|82|508)/, // RuPay starts with 60, 65, 81, 82, or 508
              "Maestro": /^(50|5[6-9]|6[0-9])/ // Maestro starts with 50, 56-59, 60-69
          };
      
          for (let card in cardPatterns) {
              if (cardPatterns[card].test(cleaned)) {
                  return card;
              }
          }
      
          return "Unknown"; // Default if no match is found
        }
      
        // ----------------- Function to Update Card Logo Dynamically -----------------
        function updateCardLogo(inputSelector) {
          let input = $(inputSelector);
          let logo = input.siblings(".card-logo"); // Select logo next to input
          let cardNumber = input.val().replace(/\D/g, ''); // Remove non-digit characters
      
          if (cardNumber === "") {
              logo.hide(); // Hide the logo if the input is empty
          } else {
              let cardType = detectCardType(cardNumber);
              if (cardType in cardLogos) {
                  logo.attr("src", cardLogos[cardType]).show();
              } else {
                  logo.hide(); // Hide logo if card type is unknown
              }
          }
        }

        
      
        // ----------------- Universal Field Formatting -----------------
        const formatFunctions = {
          ".mask-cnpj": (digits) => formatString(digits, "##.###.###/####-##"),
          ".mask-cpf": (digits) => formatString(digits, "###.###.###-##"),
          ".mask-cep": (digits) => formatString(digits, "#####-###"),
          ".mask-phus": (digits) => formatString(digits, "(###) ###-####"),
          ".mask-ph8": (digits) => formatString(digits, "####-####"),
          ".mask-ddd8": (digits) => formatString(digits, "(##) ####-####"),
          ".mask-ddd9": (digits) => formatString(digits, "(##) #####-####"),
          ".mask-dmy": (digits) => formatString(digits, "##/##/####"),
          ".mask-mdy": (digits) => formatString(digits, "##/##/####"),
          ".mask-hms": (digits) => formatString(digits, "##:##:##"),
          ".mask-hm": (digits) => formatString(digits, "##:##"),
          ".mask-dmyhm": (digits) => formatString(digits, "##/##/#### ##:##"),
          ".mask-mdyhm": (digits) => formatString(digits, "##/##/#### ##:##"),
          ".mask-my": (digits) => formatString(digits, "##/####"),
          ".mask-ccs": (digits) => formatCreditCard(digits, "space"),
          ".mask-cch": (digits) => formatCreditCard(digits, "hyphen"),
          ".mask-ccmy": (digits) => formatString(digits, "##/##"),
          ".mask-ccmyy": (digits) => formatString(digits, "##/####"),
          ".mask-moneyc": (digits) => formatMoneyInput(digits, "C",'$',''),
          ".mask-moneyd": (digits) => formatMoneyInput(digits, "D",'$',''),
          ".mask-ipv4": (digits) => formatString(digits, "###.###.###.###") // New IPv4 Masking
        };
      
        // Apply formatting dynamically for all fields, including money inputs
        Object.entries(formatFunctions).forEach(([selector, formatFunction]) => {
          $(document).on("input focus", selector, function (event) {
              var input = this;
              var oldCaret = getCaretPosition(input);
      
              // Handle money input separately
              if ($(input).hasClass("mask-moneyc") || $(input).hasClass("mask-moneyd")) {
                  let type = $(input).hasClass("mask-moneyc") ? "C" : "D";
                  handleMoneyInput(event);
                  return;
              }
      
              // Standard digit-based formatting for other fields
              var rawDigits = input.value.replace(/\D/g, ""); // Remove non-digits
              var digitIndex = getDigitIndexFromCaret(input.value, oldCaret);
              var newVal = formatFunction(rawDigits); // Use predefined function for other fields
              var newCaret = mapDigitIndexToCaret(newVal, digitIndex);
      
              input.value = newVal;
              setCaretPosition(input, newCaret || 0); // Keep caret in place
      
              // Update card logo dynamically for credit card fields
              if ($(input).hasClass("mask-ccs") || $(input).hasClass("mask-cch")) {
                  updateCardLogo(input);
              }
          });
        });
      
        // ----------------- Helper Functions for Caret Management -----------------
        function getCaretPosition(input) {
          return input.selectionStart;
        }
        function getDigitIndexFromCaret(formattedStr, caretPos) {
          var count = 0;
          for (var i = 0; i < caretPos; i++) {
            if (/\d/.test(formattedStr.charAt(i))) {
              count++;
            }
          }
          return count;
        }
        function mapDigitIndexToCaret(formattedStr, digitIndex) {
          var count = 0;
          for (var i = 0; i < formattedStr.length; i++) {
            if (/\d/.test(formattedStr.charAt(i))) {
              if (count === digitIndex) {
                return i;
              }
              count++;
            }
          }
          return formattedStr.length;
        }
        function setCaretPosition(elem, pos) {
          if (elem.setSelectionRange) {
            elem.focus();
            elem.setSelectionRange(pos, pos);
          } else if (elem.createTextRange) {
            var range = elem.createTextRange();
            range.collapse(true);
            range.moveEnd('character', pos);
            range.moveStart('character', pos);
            range.select();
          }
        }
      
        // ----------------- Backspace Handling -----------------
        $(document).on("keydown", "input", function (e) {
          if (e.key === "Backspace") {
              var input = this;
      
              // Special handling for money inputs
              if ($(input).hasClass("mask-moneyc") || $(input).hasClass("mask-moneyd")) {
                  let decimalSeparator = $(input).hasClass("mask-moneyc") ? "." : ",";
                  let baseFormat = `${currencySymbol}0${decimalSeparator}${"0".repeat(decimalPlaces)}`;
      
                  if (input.value === baseFormat) {
                      e.preventDefault(); // Prevent deletion beyond the base format
                      return;
                  }
      
                  // Handle money input dynamically and prevent default formatting conflict
                  handleMoneyInput(e);
                  return;
              }
      
              // Standard backspace handling for all other masked inputs
              if (input.selectionStart !== input.selectionEnd) {
                  e.preventDefault();
                  input.value = "";
                  setCaretPosition(input, 0);
                  return;
              }
      
              var caretPos = getCaretPosition(input);
              var digitIndex = getDigitIndexFromCaret(input.value, caretPos);
              if (digitIndex === 0) return;
      
              var rawDigits = input.value.replace(/\D/g, "");
              var newDigits = rawDigits.slice(0, digitIndex - 1) + rawDigits.slice(digitIndex);
      
              // Find matching class for format function
              let matchedClass = Object.keys(formatFunctions).find(cls => $(input).hasClass(cls.substring(1)));
      
              if (matchedClass) {
                  var formatted = formatFunctions[matchedClass](newDigits);
                  input.value = formatted;
                  var newCaretPos = mapDigitIndexToCaret(formatted, digitIndex - 1);
                  setCaretPosition(input, newCaretPos);
              }
          }
        });
        
        // ----------------- Error Validation -----------------
        function validateInput(selector, errorClass, validationFunction, errorMessage) {
          $(document).on("blur", selector, function () {
              let input = $(this);
              let val = input.val();
              let errorElement;

              if(input.hasClass('cool-form__field')){
                errorElement = input.closest('.cool-form__field-group').find("." + errorClass)
              }else if(input.hasClass('ehp-form__field')){
                errorElement = input.closest('.ehp-form__field-group').find("." + errorClass)
              }else{
                errorElement = input.closest('.elementor-field-group').find("." + errorClass);
              }
      
              // Remove predefined symbol if it's the ONLY character left
              if (val.length === 1 && !/\d/.test(val)) {
                  input.val(""); 
                  errorElement.hide().text(""); 
                  return;
              }
      
              // Show error message if validation fails
              if (val !== "" && !validationFunction(val)) {
                  errorElement.text(errorMessage).css("display", "flex").hide().fadeIn(200);
              } else {
                  errorElement.fadeOut(100, function () {
                      $(this).css("display", "none"); 
                  });
              }
          });
      
          $(document).on("input", selector, function () {
              var input = $(this);
              var errorElement = input.closest('.elementor-field-group').find("." + errorClass);
      
              if (errorElement.is(":visible")) { 
                  var val = input.val();
                  if (validationFunction(val)) {
                      errorElement.fadeOut(100, function () {
                          $(this).css("display", "none"); 
                      });
                  }
              }
          });
        }
          
            
        // Apply validation dynamically for all fields
        const validations = {
          ".mask-cnpj": { errorClass: "error-cnpj", validate: isValidCNPJ, msg: fmeData.errorMessages["mask-cnpj"] },
          ".mask-cpf": { errorClass: "error-cpf", validate: isValidCPF, msg: fmeData.errorMessages["mask-cpf"] },
          ".mask-cep": { errorClass: "error-cep", validate: isValidCEP, msg: fmeData.errorMessages["mask-cep"] },
          ".mask-phus": { errorClass: "error-phus", validate: isValidPhoneUSA, msg: fmeData.errorMessages["mask-phus"] },
          ".mask-ph8": { errorClass: "error-ph8", validate: isValidPhone8, msg: fmeData.errorMessages["mask-ph8"] },
          ".mask-ddd8": { errorClass: "error-ddd8", validate: isValidPhoneDDD8, msg: fmeData.errorMessages["mask-ddd8"] },
          ".mask-ddd9": { errorClass: "error-ddd9", validate: isValidPhoneDDD9, msg: fmeData.errorMessages["mask-ddd9"] },
          ".mask-dmy": { errorClass: "error-dmy", validate: isValidDateDMY, msg: fmeData.errorMessages["mask-dmy"] },
          ".mask-mdy": { errorClass: "error-mdy", validate: isValidDateMDY, msg: fmeData.errorMessages["mask-mdy"] },
          ".mask-hms": { errorClass: "error-hms", validate: isValidTimeHMS, msg: fmeData.errorMessages["mask-hms"] },
          ".mask-hm": { errorClass: "error-hm", validate: isValidTimeHM, msg: fmeData.errorMessages["mask-hm"] },
          ".mask-dmyhm": { errorClass: "error-dmyhm", validate: isValidDateDMYHM, msg: fmeData.errorMessages["mask-dmyhm"] },
          ".mask-mdyhm": { errorClass: "error-mdyhm", validate: isValidDateMDYHM, msg: fmeData.errorMessages["mask-mdyhm"] },
          ".mask-my": { errorClass: "error-my", validate: isValidDateMY, msg: fmeData.errorMessages["mask-my"] },
          ".mask-ccs": { errorClass: "error-ccs", validate: isValidCreditCard, msg: fmeData.errorMessages["mask-ccs"] },
          ".mask-cch": { errorClass: "error-cch", validate: isValidCreditCard, msg: fmeData.errorMessages["mask-cch"] },
          ".mask-ccmy": { errorClass: "error-ccmy", validate: isValidExpiryMMYY, msg: fmeData.errorMessages["mask-ccmy"] },
          ".mask-ccmyy": { errorClass: "error-ccmyy", validate: isValidExpiryMMYYYY, msg: fmeData.errorMessages["mask-ccmyy"] },
          ".mask-ipv4": { errorClass: "error-ipv4", validate: isValidIPv4, msg: fmeData.errorMessages["mask-ipv4"] }
      };
      
      
        for (const [selector, { errorClass, validate, msg }] of Object.entries(validations)) {
            validateInput(selector, errorClass, validate, msg);
        }
      
        // ----------------- Validation Functions -----------------
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
      
        // ----------------- Universal Date & Time Validation Function -----------------
        function isValidDateTime(value, format) {
          let regexPattern;
          let expectedParts;
      
          switch (format) {
              case "DMY": // dd/mm/yyyy
                  regexPattern = /^(\d{2})\/(\d{2})\/(\d{4})$/;
                  expectedParts = ["day", "month", "year"];
                  break;
              case "MDY": // mm/dd/yyyy
                  regexPattern = /^(\d{2})\/(\d{2})\/(\d{4})$/;
                  expectedParts = ["month", "day", "year"];
                  break;
              case "HMS": // hh:mm:ss
                  regexPattern = /^(\d{2}):(\d{2}):(\d{2})$/;
                  expectedParts = ["hour", "minute", "second"];
                  break;
              case "HM": // hh:mm
                  regexPattern = /^(\d{2}):(\d{2})$/;
                  expectedParts = ["hour", "minute"];
                  break;
              case "DMY-HM": // dd/mm/yyyy hh:mm
                  regexPattern = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/;
                  expectedParts = ["day", "month", "year", "hour", "minute"];
                  break;
              case "MDY-HM": // mm/dd/yyyy hh:mm
                  regexPattern = /^(\d{2})\/(\d{2})\/(\d{4}) (\d{2}):(\d{2})$/;
                  expectedParts = ["month", "day", "year", "hour", "minute"];
                  break;
              case "MY": // mm/yyyy
                  regexPattern = /^(\d{2})\/(\d{4})$/;
                  expectedParts = ["month", "year"];
                  break;
              default:
                  return false; // Unsupported format
          }
      
          // Match input with regex
          let match = value.match(regexPattern);
          if (!match) return false;
      
          // Extract values dynamically
          let parts = {};
          expectedParts.forEach((part, index) => {
              parts[part] = parseInt(match[index + 1], 10);
          });
      
          // Validate Date (Day, Month, Year)
          if (parts.year && (parts.year < 1500 || parts.year > 3000)) return false;
          if (parts.month && (parts.month < 1 || parts.month > 12)) return false;
          if (parts.day) {
              let daysInMonth = new Date(parts.year, parts.month, 0).getDate();
              if (parts.day < 1 || parts.day > daysInMonth) return false;
          }
      
          // Validate Time (Hour, Minute, Second)
          if (parts.hour && (parts.hour < 0 || parts.hour >= 24)) return false;
          if (parts.minute && (parts.minute < 0 || parts.minute >= 60)) return false;
          if (parts.second && (parts.second < 0 || parts.second >= 60)) return false;
      
          return true;
        }
      
        // ----------------- Expiry Date Validation -----------------
        function isValidExpiryDate(value, format) {
          let regexPattern = format === "MM/YY" ? /^(\d{2})\/(\d{2})$/ : /^(\d{2})\/(\d{4})$/;
          let match = value.match(regexPattern);
      
          if (!match) return false; // Invalid format
      
          let month = parseInt(match[1], 10);
          let year = parseInt(match[2], 10);
      
          let currentYear = new Date().getFullYear();
          let currentMonth = new Date().getMonth() + 1; // JS months are 0-based
      
          // Adjust year format if MM/YY (Assume 20XX for past/future dates)
          if (format === "MM/YY") {
              year += 2000;
          }
      
          // Validate month (01-12)
          if (month < 1 || month > 12) return false;
      
          // Validate expiry date (must be in the future)
          if (year < currentYear || (year === currentYear && month < currentMonth)) {
              return false;
          }
      
          return true;
        }
      
        // ----------------- Exportable Functions Using Universal Validator -----------------
        function isValidDateDMY(dateStr) { return isValidDateTime(dateStr, "DMY"); }
        function isValidDateMDY(dateStr) { return isValidDateTime(dateStr, "MDY"); }
        function isValidTimeHMS(timeStr) { return isValidDateTime(timeStr, "HMS"); }
        function isValidTimeHM(timeStr) { return isValidDateTime(timeStr, "HM"); }
        function isValidDateDMYHM(dateTimeStr) { return isValidDateTime(dateTimeStr, "DMY-HM"); }
        function isValidDateMDYHM(dateTimeStr) { return isValidDateTime(dateTimeStr, "MDY-HM"); }
        function isValidDateMY(dateStr) { return isValidDateTime(dateStr, "MY"); }
        function isValidExpiryMMYY(dateStr) { return isValidExpiryDate(dateStr, "MM/YY"); }
        function isValidExpiryMMYYYY(dateStr) { return isValidExpiryDate(dateStr, "MM/YYYY"); }
      
        // Apply validation for credit card fields
        function isValidCreditCard(cardNumber) {
          const cleaned = cardNumber.replace(/\D/g, ''); // Remove non-digits
          if (cleaned.length < 15 || cleaned.length > 16) return false; // AMEX: 15, Others: 16
          let sum = 0;
          let shouldDouble = false;
          // Luhn Algorithm to validate card number
          for (let i = cleaned.length - 1; i >= 0; i--) {
              let digit = parseInt(cleaned.charAt(i), 10);
      
              if (shouldDouble) {
                  digit *= 2;
                  if (digit > 9) digit -= 9;
              }
      
              sum += digit;
              shouldDouble = !shouldDouble;
          }
          return sum % 10 === 0;
        }
      
        // Validate CNPJ (Brazilian Business ID - 14 digits)
        function isValidCNPJ(cnpj) {
          cnpj = cnpj.replace(/\D/g, ''); // Remove non-numeric characters
          if (cnpj.length !== 14) return false;
      
          // Eliminate obvious invalid CNPJs (repeated digits)
          if (/^(\d)\1+$/.test(cnpj)) return false;
      
          let calcCheckDigit = (cnpj, length) => {
              let weights = length === 12 ? [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2] : [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
              let sum = 0;
      
              for (let i = 0; i < weights.length; i++) {
                  sum += parseInt(cnpj.charAt(i)) * weights[i];
              }
      
              let remainder = sum % 11;
              return remainder < 2 ? 0 : 11 - remainder;
          };
      
          // Validate both check digits
          let firstCheck = calcCheckDigit(cnpj, 12);
          let secondCheck = calcCheckDigit(cnpj, 13);
      
          return firstCheck === parseInt(cnpj.charAt(12)) && secondCheck === parseInt(cnpj.charAt(13));
        }
      
        // Validate CPF (Brazilian Individual ID - 11 digits)
        function isValidCPF(cpf) {
          cpf = cpf.replace(/\D/g, ''); // Remove non-numeric characters
          if (cpf.length !== 11) return false;
      
          // Eliminate obvious invalid CPFs (repeated digits)
          if (/^(\d)\1+$/.test(cpf)) return false;
      
          let validateCPF = (cpf, length) => {
              let sum = 0;
              for (let i = 0; i < length; i++) {
                  sum += parseInt(cpf.charAt(i)) * (length + 1 - i);
              }
              let result = (sum * 10) % 11;
              return result === 10 ? 0 : result === parseInt(cpf.charAt(length));
          };
      
          return validateCPF(cpf, 9) && validateCPF(cpf, 10);
        }
      
        // Validate CEP (Brazilian Postal Code - XXXXX-XXX)
        function isValidCEP(cep) {
          return /^\d{5}-\d{3}$/.test(cep); // Matches format XXXXX-XXX
        }
      
        // Validate IPv4 (Format: 192.168.001.001)
        function isValidIPv4(ip) {
          // Ensure correct format (###.###.###.###)
          const ipv4Pattern = /^(?:\d{1,3}\.){3}\d{1,3}$/;
      
          if (!ipv4Pattern.test(ip)) return false; // Check general format
      
          // Split by dots and validate each octet (0-255)
          const octets = ip.split(".");
          return octets.every(octet => {
              let num = parseInt(octet, 10); // Convert to number
              return num >= 0 && num <= 255; // Each octet must be between 0-255
          });
        }
      
        // ----------------- Form Submit -----------------
        
        $(document).on("click", ".cool-form__submit-group button", function (e) {
            e.preventDefault();
            const $submitBtn = $(this);
            const $form = $submitBtn.closest("form");

            // Prevent multiple rapid clicks
            if ($submitBtn.data("clicked")) {
                e.preventDefault();
                return;
            }
            $submitBtn.data("clicked", true);

            // Trigger blur so masks run their validation
            $form.find("input").trigger("blur");

            // Wait for possible async error display (like from masks)
            setTimeout(() => {
                const $visibleErrors = $form.find(".mask-error").filter(function () {
                    return $(this).text().trim() !== "" && $(this).is(":visible");
                });

                if ($visibleErrors.length > 0) {
                    const $firstError = $visibleErrors.first();
                    $("html, body").animate({
                        scrollTop: $firstError.offset().top - 200
                    }, 400);
                    
                    e.preventDefault();
                    $submitBtn.data("clicked", false);
                    return;
                }

                // No visible errors? Submit the form
                $form[0].requestSubmit();
                $submitBtn.data("clicked", false);
            }, 400); // Wait a bit after blur to allow errors to appear
        });


        $(document).on("click", ".ehp-form__submit-group button", function (e) {
          const $submitBtn = $(this);
          const $form = $submitBtn.closest("form");

          if ($submitBtn.data("clicked")) {
            e.preventDefault();
            return;
          }
          $submitBtn.data("clicked", true);

          $form.find("input").trigger("blur");
          $form[0].classList.add("elementor-form-waiting");

          setTimeout(() => {
            let hasVisibleMaskError = false;

            const $errors = $form.find(".mask-error").filter(function () {
              return $(this).text().trim() !== "" && $(this).is(":visible");
            });

            if ($errors.length > 0) {
              hasVisibleMaskError = true;
              $("html, body").animate({
                scrollTop: $errors.first().offset().top - 200
              }, 300);
            }

            const $emptyRequiredMasked = $form.find("input[required]").filter(function () {
              const val = $(this).val().trim();
              const isVisible = $(this).is(":visible");
              return isVisible && (val === "" || /^[\s_\-\(\)\.:/]+$/.test(val));
            });

            if ($emptyRequiredMasked.length > 0) {
              hasVisibleMaskError = true;
              $("html, body").animate({
                scrollTop: $emptyRequiredMasked.first().offset().top - 200
              }, 300);
              $emptyRequiredMasked.first().focus();
            }

            if (hasVisibleMaskError || !$form[0].checkValidity()) {
              $form[0].classList.remove("elementor-form-waiting");
              $submitBtn.data("clicked", false);
              e.preventDefault();
              return;
            }

            $form[0].classList.remove("elementor-form-waiting");
            $form[0].requestSubmit();
            $submitBtn.data("clicked", false);
          }, 500);
        });



        $(document).on("click", ".elementor-field-type-submit", function (e) {
          var $submitBtn = $(this);
          var $form = $submitBtn.closest("form");

          // Prevent double-clicks
          if ($submitBtn.data("clicked")) {
            e.preventDefault();
            return;
          }
          $submitBtn.data("clicked", true); // Mark as clicked

          // Trigger blur on inputs to ensure validation runs
          $form.find("input").trigger("blur");

          // Add Elementor waiting class
          $form[0].classList.add("elementor-form-waiting");

          // Wait for mask errors or blur logic to complete
          setTimeout(() => {
            let hasVisibleMaskError = false;

            // Check for visible mask error messages
            const $errors = $form.find(".mask-error").filter(function () {
              return $(this).text().trim() !== "" && $(this).is(":visible");
            });

            if ($errors.length > 0) {
              hasVisibleMaskError = true;
              const $firstError = $errors.first();
              $("html, body").animate({
                scrollTop: $firstError.offset().top - 200
              }, 300);
            }

            // ✅ Check for empty required masked fields
            const $emptyRequiredMasked = $form.find("input[required]").filter(function () {
              const val = $(this).val().trim();
              const isVisible = $(this).is(":visible");
              return isVisible && (val === "" || /^[\s_\-\(\)\.:/]+$/.test(val));
            });

            if ($emptyRequiredMasked.length > 0) {
              hasVisibleMaskError = true;
              const $firstEmpty = $emptyRequiredMasked.first();
              $("html, body").animate({
                scrollTop: $firstEmpty.offset().top - 200
              }, 300);
              $firstEmpty.focus();
            }

            // ❌ Validation failed
            if (hasVisibleMaskError || !$form[0].checkValidity()) {
              $form[0].classList.remove("elementor-form-waiting");
              $submitBtn.data("clicked", false);
              e.preventDefault();
              return;
            }

            // ✅ All good — submit the form
            $form[0].classList.remove("elementor-form-waiting");
            $form[0].requestSubmit();
            $submitBtn.data("clicked", false);
          }, 500);
        });

      
    });
})(jQuery);