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

        function stripCNPJ(value) {
          var s = String(value).toUpperCase().replace(/[^A-Z0-9]/g, "");
          if (s.length <= 12) return s;
          return s.slice(0, 12) + s.slice(12).replace(/\D/g, "").slice(0, 2);
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
          let newValue = '';

          if(input.value != '$0,00'){

            newValue = formatMoneyInput(input.value, type, moneyPrefix, input);
          }else{
            newValue = "";
          }  
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
            "RuPay": fmeData.pluginUrl+ "assets/svg-icons/rupay-logo.svg",
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
              var isCnpj = $(input).hasClass("mask-cnpj");
              var rawDigits = isCnpj ? stripCNPJ(input.value) : input.value.replace(/\D/g, "");
              var digitIndex = getDigitIndexFromCaret(input.value, oldCaret, isCnpj);
              var newVal = formatFunction(rawDigits); // Use predefined function for other fields
              var newCaret = mapDigitIndexToCaret(newVal, digitIndex, isCnpj);
      
              if(newVal != '('){

                input.value = newVal;
              }else{
                input.value = "";
              }
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
        function getDigitIndexFromCaret(formattedStr, caretPos, alphanumeric) {
          var count = 0;
          var pattern = alphanumeric ? /[A-Za-z0-9]/ : /\d/;
          for (var i = 0; i < caretPos; i++) {
            if (pattern.test(formattedStr.charAt(i))) {
              count++;
            }
          }
          return count;
        }
        function mapDigitIndexToCaret(formattedStr, digitIndex, alphanumeric) {
          var count = 0;
          var pattern = alphanumeric ? /[A-Za-z0-9]/ : /\d/;
          for (var i = 0; i < formattedStr.length; i++) {
            if (pattern.test(formattedStr.charAt(i))) {
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
              var isCnpj = $(input).hasClass("mask-cnpj");
              var digitIndex = getDigitIndexFromCaret(input.value, caretPos, isCnpj);
              if (digitIndex === 0) return;
      
              var rawDigits = isCnpj ? stripCNPJ(input.value) : input.value.replace(/\D/g, "");
              var newDigits = rawDigits.slice(0, digitIndex - 1) + rawDigits.slice(digitIndex);
      
              // Find matching class for format function
              let matchedClass = Object.keys(formatFunctions).find(cls => $(input).hasClass(cls.substring(1)));
      
              if (matchedClass) {
                  var formatted = formatFunctions[matchedClass](newDigits);
                  input.value = formatted;
                  var newCaretPos = mapDigitIndexToCaret(formatted, digitIndex - 1, isCnpj);
                  setCaretPosition(input, newCaretPos);
              }
          }
        });
        
        // ----------------- Error Validation -----------------
        // Apply validation dynamically for all fields (shared CFKEF.MaskValidators)
        // Bound after nextbtnVisibility is defined below so step handling can hook onInput.
        
        let maskErrorArr = {};
        let nextBtnOriginalClicks = {};
        let clickStatus={};
        let recaptchaEvent = {};
        let submitBtnEvent = {};


        const nextbtnVisibility = (errorClass, input, validationFunction) => {

          const closesWidget = input.closest(".elementor-widget-form");
          const widgetId = closesWidget.data('id');
          const inuptId = input.attr('id');
          const fieldStep=input.closest(".elementor-field-type-step");
          const form = input.closest(".elementor-form");

          const currectStepFields = form.find(".elementor-form-fields-wrapper.elementor-labels-above").children("div:not(.elementor-hidden)").find('input, textarea, select');

          const submtBtnTag = form.find("button[type='submit']");

          if (!closesWidget.length || !fieldStep.length) {
            return;
          }

          const nextBtn = fieldStep.find(".e-form__buttons__wrapper__button[data-direction='next']");

          

          if ((nextBtn.length == 0 && submtBtnTag.length > 0 ) && (submtBtnTag.length == 0 && nextBtn.length == 0) ) {
            return;
          }



          let val = input.val();

          if (val.length === 1 && !/\d/.test(val)) {
            val = '';
          }

          // Show error message if validation fails
          if (  val !== "" && !validationFunction(val)) {


            if (closesWidget.length > 0) {


              if (!maskErrorArr[widgetId]) {
                maskErrorArr[widgetId] = [];
              }

              if (!maskErrorArr[widgetId].includes(inuptId)) {
                maskErrorArr[widgetId].push(inuptId);
              }
            }

            if (!nextBtnOriginalClicks[widgetId] || !nextBtnOriginalClicks[widgetId].length) {



              if(nextBtn.length > 0){

                const origninalClicks = jQuery._data(nextBtn[0], "events");
      
                if (origninalClicks && (!nextBtnOriginalClicks[widgetId] || !nextBtnOriginalClicks[widgetId].length === 0)) {
                  nextBtnOriginalClicks[widgetId] = origninalClicks && origninalClicks.click ? origninalClicks.click.map(h => h.handler) : [];
      
                }
              }


            }
          } else {
            if (maskErrorArr[widgetId] && maskErrorArr[widgetId].includes(inuptId)) {
              maskErrorArr[widgetId] = maskErrorArr[widgetId].filter(item => item !== inuptId);
            }
          }


          if (maskErrorArr[widgetId] && maskErrorArr[widgetId].length > 0) {

            if(nextBtn.length > 0){

              const origninalClicks = jQuery._data(nextBtn[0], "events");

            if (origninalClicks && (!nextBtnOriginalClicks[widgetId] || !nextBtnOriginalClicks[widgetId].length)) {


              nextBtnOriginalClicks[widgetId] = origninalClicks && origninalClicks.click ? origninalClicks.click.map(h => h.handler) : [];

            }
            
            if (nextBtnOriginalClicks[widgetId] && nextBtnOriginalClicks[widgetId].length > 0) {
              nextBtn.off("click");
            }


            }


            
          } else {


            // when no error in mask validation

            if(recaptchaEvent[widgetId]){
              submtBtnTag.on("click", recaptchaEvent[widgetId]);
            }

            if(submitBtnEvent[widgetId]){
              form.on("submit", submitBtnEvent[widgetId]);
            }



            if (nextBtnOriginalClicks[widgetId] && nextBtnOriginalClicks[widgetId].length > 0) {




              let isfieldsValid = true

              for (let i = 0; i < currectStepFields.length; i++) {


                if (currectStepFields[i].checkValidity() == false) {

                  isfieldsValid = false;
                  break;

                }

              }

              if (isfieldsValid) {





                if(nextBtn.length > 0){

                  // 2️⃣ First, clear existing handlers to avoid duplication
                  nextBtn.off("click");
                  
                  // Re-attach original click handlers only once
                  nextBtnOriginalClicks[widgetId].forEach(fn => {
                    nextBtn.one("click", fn); // use .one instead of .on
                  });
                }


              }
                
              }

          }
        }


        

        // handle next button event before click

        $(document).on("mousedown", ".e-form__buttons__wrapper__button[data-direction='next']", function(e){

          const form = $(this).closest(".elementor-form");

          const mask_error_div = form.find(".elementor-form-fields-wrapper.elementor-labels-above").children("div:not(.elementor-hidden)").find('div.mask-error');

          const closesWidget = $(this).closest(".elementor-widget-form");
          const widgetId = closesWidget.data('id');

          let mask_error = false;

          // finding mask error
          for(let i=0; i<mask_error_div.length; i++){
            if(mask_error_div[i].value != "" && mask_error_div[i].style.display == 'flex'){
              mask_error = true;
              break;
            }
          }

          // reattcach next button event when no mask error

          if (maskErrorArr[widgetId] && !maskErrorArr[widgetId].length > 0 && !mask_error) {
              // Re-attach original click handlers only once
                
              if (nextBtnOriginalClicks[widgetId] && nextBtnOriginalClicks[widgetId].length > 0) {
                  $(this).off("click");

                  nextBtnOriginalClicks[widgetId].forEach(fn => {
                    $(this).one("click", fn); // use .one instead of .on
                  });
                }


          }

        })


        // handling next button event when previous button click

        $(document).on("click", ".e-form__buttons__wrapper__button[data-direction='previous']", function(e){

          const form = $(this).closest(".elementor-form");
          
          const closesWidget = form.closest(".elementor-widget-form");
          const widgetId = closesWidget.data('id');
          maskErrorArr[widgetId] = [];

          const currectStepFields = form.find(".elementor-form-fields-wrapper.elementor-labels-above").children("div:not(.elementor-hidden)")

          const nextBtn = currectStepFields.find(".e-form__buttons__wrapper__button[data-direction='next']");

          // attaching events to next button
          
          if (nextBtnOriginalClicks[widgetId] && nextBtnOriginalClicks[widgetId].length > 0) {
            nextBtn.off("click");

            nextBtnOriginalClicks[widgetId].forEach(fn => {
                    nextBtn.one("click", fn); // use .one instead of .on
              });

          }

        })

        // handle mask validation on submit button of step field form
        $(document).on("mousedown", ".elementor-field-type-submit", function (e){

          var $submitBtn = $(this);

          var $form = $submitBtn.closest("form");

          const closesWidget = $form.closest(".elementor-widget-form");
          const widgetId = closesWidget.data('id');

          const currectStepFields = $form.find(".elementor-form-fields-wrapper.elementor-labels-above").children("div:not(.elementor-hidden)")

          const previousBtn = currectStepFields.find(".e-form__buttons__wrapper__button[data-direction='previous']");

          const inputMaskFields = currectStepFields.find("input.fme-mask-input");

          // run only first time when click on submit button 
          // if maskerror found in the form widget this will remove submit button and form events to prevent submit


          if(previousBtn && previousBtn.length && inputMaskFields && inputMaskFields.length && maskErrorArr[widgetId] &&maskErrorArr[widgetId].length){

            var $subBtnTag = $submitBtn.find("button");

          
          // getting submit button recaptcha and click events

          var $form = $submitBtn.closest("form");

          const origninalclick = jQuery._data($subBtnTag[0], "events");

          if(origninalclick && origninalclick.click){

            origninalclick.click.forEach((ele) => {
            if(ele.handler.toString().trim().includes("onV3FormSubmit")){

              if(!recaptchaEvent[widgetId]){

                recaptchaEvent[widgetId] = ele.handler;

              }

            }
          })

        }




          // getting form apply step events

          const origninalSubmit = jQuery._data($form[0], "events");

          if(origninalSubmit && origninalSubmit.submit){

            origninalSubmit.submit.forEach((ele) => {
            if(ele.handler.toString().trim().includes("resetForm")){

              if(!submitBtnEvent[widgetId]){

                submitBtnEvent[widgetId] = ele.handler;

              }

            }
          })

          }



          if(submitBtnEvent[widgetId]){
            // removing form apply step event
            $form.off("submit", submitBtnEvent[widgetId]);

          }


          if(recaptchaEvent[widgetId]){

            // removing submit button click events
            $subBtnTag.off("click", recaptchaEvent[widgetId]);
          }



          }
        })

        // end - step field mask error handling

        CFKEF.bindAllMaskValidations($, {
          onInput: function (input, errorClass, validationFunction) {
            nextbtnVisibility(errorClass, input, validationFunction);
          },
        });

      
        // ----------------- Form Submit -----------------
        
        CFKEF.bindMaskSubmitGuard($, '.cool-form__submit-group button', {
          preventDefaultAlways: true,
          delay: 400,
          scrollDuration: 400,
        });

        CFKEF.bindMaskSubmitGuard($, '.ehp-form__submit-group button', {
          delay: 500,
          scrollDuration: 300,
          addWaitingClass: true,
          checkRequiredEmpty: true,
          checkHtml5Validity: true,
        });

        CFKEF.bindMaskSubmitGuard($, '.elementor-field-type-submit', {
          delay: 500,
          scrollDuration: 300,
          addWaitingClass: true,
          checkRequiredEmpty: true,
          errorDisplayFlex: true,
          shouldHandle: function ($submitBtn) {
            return !$submitBtn.find('button').hasClass('cfkef-prevent-submit');
          },
          onAfterValid: function ($submitBtn, $form) {
            const closesWidget = $form.closest('.elementor-widget-form');
            const widgetId = closesWidget.data('id');
            const submtBtnTag = $form.find("button[type='submit']");

            if (recaptchaEvent[widgetId]) {
              submtBtnTag.on('click', recaptchaEvent[widgetId]);
              submtBtnTag.trigger('click');
            }

            if (submitBtnEvent[widgetId]) {
              $form.on('submit', submitBtnEvent[widgetId]);
              submtBtnTag.trigger('click');
            }

            if (!recaptchaEvent[widgetId]) {
              const error_messages = $form.find('.elementor-form-fields-wrapper').find('.elementor-message');
              if (error_messages && error_messages.length === 0) {
                $form[0].requestSubmit();
              }
            }
          },
        });

      
    });
})(jQuery);