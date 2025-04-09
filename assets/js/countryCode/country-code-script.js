/**
 * Class for handling country code functionality in Elementor forms.
 */
class CCFEF extends elementorModules.frontend.handlers.Base {
  /**
   * Retrieves the default settings for the country code functionality.
   * @returns {Object} An object containing selector configurations.
   */
  getDefaultSettings() {
    return {
      selectors: {
        intlInputSpan: ".ccfef-editor-intl-input",
        submitButton: "div.elementor-field-type-submit button",
      },
    };
  }

  /**
   * Retrieves the default elements based on the settings defined.
   * @returns {Object} An object containing jQuery elements for the text area and editor span.
   */
  getDefaultElements() {
    const selectors = this.getSettings("selectors");
    return {
      $intlSpanElement: this.$element.find(selectors.intlInputSpan),
      $submitButton: this.$element.find(selectors.submitButton),
    };
  }

  /**
   * Binds events to the elements. This method is intended to be overridden by subclasses to add specific event handlers.
   */
  bindEvents() {
    this.initializeITISettings();
    this.setupInternationalTelephoneInput();
    this.customFlags(); // custom load svg flags
    this.cleanupDOMElements();
    this.validateInternationalInputs();
  }

  /**
   * Initializes settings for International Telephone Input.
   */
  initializeITISettings() {
    this.itiSettingData = {
      Id: [], // Array to store telephone IDs
      defaultCountry: {}, // Object to store default country settings
      includeCountries: {}, // Object to store included countries
      excludeCountries: {}, // Object to store excluded countries
      preferredCountries: {}, // Object to store preferred countries
      autoDetectCountry: {}, // Object to store auto-detect country settings
      apiKey: CCFEFCustomData.geo_lookup_key, // API key for external services
    };

    this.itiObj = {}; // Object to store ITI instances

    this.getIntlUserData(); // Retrieves ITI data from the DOM
  }

  /**
   * Sets up the international telephone input functionality.
   */
  setupInternationalTelephoneInput() {
    this.appendCountryCodeHandler(); // Manages country code functionality
    this.addCountryCodeInputHandler(); // Initializes ITI functionality
  }

  /**
   * Cleans up DOM elements related to telephone inputs.
   */
  cleanupDOMElements() {
    this.$element.find("span.ccfef-editor-intl-input").remove(); // Removes telephone input span elements
  }

  /**
   * Method to handle appending country code.
   */
  appendCountryCodeHandler() {
    this.itiSettingData.Id.forEach((data) => {
      this.addCountryCodeIconHandler(data.formId, data.fieldId, data.customId);
    });
  }

  /**
   * Method to handle adding country code icon.
   * @param {string} formId - The ID of the form.
   * @param {string} widgetId - The widget ID.
   * @param {string} inputId - The input ID.
   */
  addCountryCodeIconHandler(formId, widgetId, inputId) {
    const utilsPath = `${CCFEFCustomData.pluginDir}assets/js/countryCode/utils.js`;
    const telField = jQuery(
      `.elementor-widget.elementor-widget-cool-form[data-id="${formId}"] .cool-form__field-group input[type="tel"]#${inputId}`
    ).get(0);

    // setTimeout(() => {
    //   let iti_tag = jQuery(".iti__selected-country")[0];
    //   let tel_input = jQuery(".cool-form-field-type-tel")[0];
    //   let coutries = jQuery(".iti__country-list li");

    //   coutries.forEach((country)=>{
    //     country.addEventListener("click", function () {
    //         tel_input.focus();
    //     });
    //   });

    // //   console.log(coutries);

    //   iti_tag.style.display = "none";
    //   tel_input.addEventListener("focus", function () {
    //     iti_tag.style.display = "block";
    //     iti_tag.style.visibility = "visible";
    //   });

    //   tel_input.addEventListener("blur", function () {
    //     iti_tag.style.visibility = "hidden";
    //   });
    // }, 1);

    const settingsData = this.itiSettingData;
    if (telField) {
      const uniqueId = `${formId}${widgetId}`;
      const includeCountries = settingsData.includeCountries[uniqueId] || [];
      const excludeCountries = settingsData.excludeCountries[uniqueId] || [];
      const preferredCountries =
        settingsData.preferredCountries[uniqueId] || [];

      const itiOptions = {
        utilsScript: utilsPath,
        separateDialCode: true,
        formatOnDisplay: false,
        formatAsYouType: true,
        autoFormat: false,
        containerClass: "cfefp-intl-container",
        useFullscreenPopup: false,
        onlyCountries: includeCountries,
        excludeCountries: excludeCountries,
        customPlaceholder: (
          selectedCountryPlaceholder,
          selectedCountryData
        ) => {
          let placeholder = selectedCountryPlaceholder;
          placeholder = placeholder.replace(/^0+/, "");

          return "" !== placeholder
            ? `+${selectedCountryData.dialCode} ${placeholder}`.replace(
                /\s/g,
                ""
              )
            : "";
        },
      };

      const iti = window.intlTelInput(telField, itiOptions);
      this.itiObj[uniqueId] = iti;

      if (preferredCountries.length) {
        this.reorderPreferredCountries(iti, preferredCountries);
      }

      this.setInitialCountry(iti, excludeCountries, uniqueId);
    }
  }

  /**
   * Reorders the country list based on preferred countries.
   * @param {Object} itiInstance - The intl-tel-input instance.
   * @param {Array} preferredCountries - List of preferred country codes.
   */
  reorderPreferredCountries(itiInstance, preferredCountries) {
    const countryList = jQuery(itiInstance.countryList);
    preferredCountries.reverse().forEach((countryCode, index) => {
      const countryListItem = countryList.find(
        `li[data-country-code="${countryCode}"]`
      );
      if (countryListItem.length) {
        if (index === 0) {
          countryList.prepend("<hr></hr>");
        }
        countryList.prepend(countryListItem);
      }
    });
  }

  /**
   * Sets the initial selected country in the dropdown.
   * @param {Object} itiInstance - The intl-tel-input instance.
   * @param {string} autoDetectCountry - Auto-detect country setting.
   * @param {string} defaultCountry - Default country code.
   * @param {string} apiKey - API key for geo-location services.
   * @param {Array} excludeCountries - List of countries to exclude.
   */
  setInitialCountry(itiInstance, excludeCountries, uniqueId) {
    const defaultCountry = this.itiSettingData.defaultCountry[uniqueId] || "";
    const autoDetectCountry =
      this.itiSettingData.autoDetectCountry[uniqueId] || "";
    const apiKey = this.itiSettingData.apiKey;
    const defaultCountries = [
      "in",
      "us",
      "gb",
      "ru",
      "fr",
      "de",
      "br",
      "cn",
      "jp",
      "it",
    ];
    const itiCountriesList = itiInstance.p.map((data) => data.iso2);

    const setCountry = (countryCode) => {
      if (itiCountriesList.length <= 0) {
        return;
      }
      const inputField = itiInstance.a;

      const normalizedCountryCode = countryCode
        ? countryCode.toLowerCase()
        : "";
      if (
        normalizedCountryCode &&
        itiCountriesList.includes(normalizedCountryCode)
      ) {
        itiInstance.setCountry(normalizedCountryCode);
      } else if (defaultCountry && itiCountriesList.includes(defaultCountry)) {
        itiInstance.setCountry(defaultCountry);
      } else {
        const availableCountries = defaultCountries.filter(
          (country) =>
            !excludeCountries.includes(country) &&
            itiCountriesList.includes(country)
        );
        const fallbackCountry =
          availableCountries.length > 0
            ? availableCountries[0]
            : itiCountriesList[0];
        itiInstance.setCountry(fallbackCountry);
      }
    };

    if (autoDetectCountry && autoDetectCountry !== "no") {
      const trimmedApiKey = apiKey.trim();
      const geoIpUrl =
        trimmedApiKey && "" !== trimmedApiKey
          ? `https://ipapi.co/json/?key=${trimmedApiKey}`
          : "https://ipapi.co/json";
      fetch(geoIpUrl)
        .then((response) => response.json())
        .then((jsonData) => {
          setCountry(jsonData.country_code);
        })
        .catch((error) => {
          console.error("Error fetching geo IP data:", error);
          setCountry(defaultCountry || itiCountriesList[0]);
        });
    } else if (defaultCountry) {
      setCountry(defaultCountry);
    }
  }

  /**
   * Method to handle country code input.
   */
  addCountryCodeInputHandler() {
    const itiArr = this.itiObj;

    Object.keys(itiArr).forEach((key) => {
      const iti = itiArr[key];
      const inputElement = iti.a;

      let previousCountryData = iti.getSelectedCountryData();
      let previousCode = `+${previousCountryData.dialCode}`;
      let keyUpEvent = false;

      const resetKeyUpEventStatus = () => {
        keyUpEvent = false;
      };

      const handleCountryChange = (e) => {
        this.customFlags();
        const currentCountryData = iti.getSelectedCountryData();
        const currentCode = `+${currentCountryData.dialCode}`;
        if (e.type === "keydown" || e.type === "input") {
          keyUpEvent = true;
          clearTimeout(resetKeyUpEventStatus);
          setTimeout(resetKeyUpEventStatus, 400);

          if (previousCountryData.dialCode !== currentCountryData.dialCode) {
            previousCountryData = currentCountryData;
          } else if (
            previousCountryData.dialCode === currentCountryData.dialCode &&
            previousCountryData.iso2 !== currentCountryData.iso2
          ) {
            iti.setCountry(previousCountryData.iso2);
          }
        } else if (e.type === "countrychange") {
          if (keyUpEvent) {
            return;
          }

          previousCountryData = currentCountryData;
        }

        this.updateCountryCodeHandler(
          e.currentTarget,
          currentCode,
          previousCode
        );
        previousCode = currentCode;
      };

      // Attach event listeners for keydown,input change and country change events
      inputElement.addEventListener("keydown", handleCountryChange);
      inputElement.addEventListener("input", handleCountryChange);
      inputElement.addEventListener("countrychange", handleCountryChange);
    });
  }

  /**
   * Method to update country code.
   * @param {Element} element - The input element.
   * @param {string} currentCode - The current country code.
   * @param {string} previousCode - The previous country code.
   */
  updateCountryCodeHandler(element, currentCode, previousCode) {
    let value = element.value;

    if (
      (currentCode && "+undefined" === currentCode) ||
      ["", "+"].includes(value)
    ) {
      return;
    }

    if (currentCode !== previousCode) {
      value = value.replace(new RegExp(`^\\${previousCode}`), "");
    }

    if (!value.startsWith(currentCode)) {
      value = value.replace(/\+/g, "");
      element.value = currentCode + value;
    }
  }

  customFlags() {
    const selectedCountries = this.$element.find(
      ".elementor-field-type-tel .cfefp-intl-container .iti__country-container .iti__flag:not(.iti__globe)"
    );

    // Loop through each flag element
    selectedCountries.each(function () {
      const selectedCountry = this; // 'this' refers to the current element in the loop
      const classList = selectedCountry.className.split(" ");

      if (classList[1]) {
        const selectedCountryFlag = classList[1].split("__")[1];
        const svgFlagPath =
          CCFEFCustomData.pluginDir + `assets/flags/${selectedCountryFlag}.svg`;
        // Apply the styles dynamically to the current flag
        selectedCountry.style.backgroundImage = `url('${svgFlagPath}')`;
      }
    });
  }

  /**
   * Retrieves and stores unique telephone input IDs from the Elementor editor span elements.
   */
  getIntlUserData() {
    const intelInputElement = this.elements.$intlSpanElement;
    const previousIds = [];

    intelInputElement.each((_, ele) => {
      const element = jQuery(ele);
      const dataAttributes = {
        defaultCountry: element.data("defaultCountry"),
        includeCountries: element.data("includeCountries"),
        excludeCountries: element.data("excludeCountries"),
        preferredCountries: element.data("preferredCountries"),
        autoDetectCountry: element.data("autoDetect"),
        inputId: element.data("id"),
        fieldId: element.data("field-id"),
        formId: element
          .closest(".elementor-element.elementor-widget-cool-form")
          .data("id"),
      };
      const currentId = `${dataAttributes.formId}${dataAttributes.fieldId}`;

      Object.keys(dataAttributes).forEach((attr) => {
        if (
          dataAttributes[attr] &&
          attr !== "inputId" &&
          attr !== "fieldId" &&
          attr !== "formId"
        ) {
          if (dataAttributes[attr] !== "") {
            const splitData = ["defaultCountry", "autoDetectCountry"].includes(
              attr
            )
              ? dataAttributes[attr]
              : dataAttributes[attr].split(",");
            this.itiSettingData[attr][currentId] = splitData;
          }
        }
      });

      if (!previousIds.includes(currentId)) {
        this.itiSettingData.Id.push({
          formId: dataAttributes.formId,
          fieldId: dataAttributes.fieldId,
          customId: dataAttributes.inputId,
        });
        previousIds.push(currentId);
      }
    });
  }

  /**
   * Validates the international telephone input fields when the submit button is clicked.
   * It checks if the number is valid and displays appropriate error messages next to the input field.
   */
  validateInternationalInputs() {
    this.elements.$submitButton.on("click", (e) => {
      const itiArr = this.itiObj;

      if (Object.keys(itiArr).length > 0) {
        Object.keys(itiArr).forEach((data) => {
          const iti = itiArr[data];
          const inputTelElement = iti.a;

          if ("" !== inputTelElement.value) {
            inputTelElement.value = inputTelElement.value.replace(
              /[^0-9+]/g,
              ""
            );
          }

          const parentWrp = inputTelElement.closest(".elementor-field-group");
          const telContainer = parentWrp.querySelector(".cfefp-intl-container");

          if (telContainer && inputTelElement.offsetHeight) {
            telContainer.style.setProperty(
              "--cfefp-intl-tel-button-height",
              `${inputTelElement.offsetHeight}px`
            );
          }

          const errorContainer = jQuery(inputTelElement).parent();
          errorContainer.find("span.elementor-message").remove();
          const errorMap = CCFEFCustomData.errorMap;
          let errorMsgHtml =
            '<span class="elementor-message elementor-message-danger elementor-help-inline elementor-form-help-inline" role="alert">';
          if (inputTelElement.value.trim() === "") {
            return;
          }
          if (iti.isValidNumber()) {
            jQuery(inputTelElement)
              .closest(".cfefp-intl-container")
              .removeClass("elementor-error");
          } else {
            const errorType = iti.getValidationError();
            if (errorType !== undefined && errorMap[errorType]) {
              errorMsgHtml += errorMap[errorType] + "</span>";
              jQuery(inputTelElement)
                .closest(".cfefp-intl-container")
                .addClass("elementor-error");
              jQuery(inputTelElement).after(errorMsgHtml);
              e.preventDefault();
            }
          }
        });
      }
    });
  }
}

jQuery(window).on("elementor/frontend/init", () => {
  const addHandler = ($element) => {
    elementorFrontend.elementsHandler.addHandler(CCFEF, {
      $element,
    });
  };

  elementorFrontend.hooks.addAction(
    "frontend/element_ready/cool-form.default",
    addHandler
  );
});
