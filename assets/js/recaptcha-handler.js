class ReCaptchaHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
      return {
        selectors: {
          recaptchaContainer: ".g-recaptcha",
        },
      };
    }
  
    getDefaultElements() {
      const selectors = this.getSettings("selectors");
      return {
        $recaptchaContainers: this.$element.find(selectors.recaptchaContainer),
        $form: jQuery("form").has(selectors.recaptchaContainer),
      };
    }
  
    bindEvents() {
  
      this.waitForElementor(() => {
  
        this.loadRecaptchaScript(() => {
  
          this.initRecaptcha(); // Ensure this runs
          // this.bindFormSubmit(); // Ensure this runs
        });
      });
    }
  
    /**
     * Waits for Elementor's frontend API to be ready before executing the callback.
     */
    waitForElementor(callback) {
      if (typeof elementorFrontend !== "undefined" && elementorFrontend.hooks) {
        callback();
      } else {
        setTimeout(() => this.waitForElementor(callback), 10000);
      }
    }
  
    loadRecaptchaScript(callback) {
      if (typeof grecaptcha !== "undefined") {
        callback();
        return;
      }
  
      const con_main = this.elements.$recaptchaContainers[0];
  
      const recaptcha_version = con_main.getAttribute("data-recaptcha-version");

      const data_badge =  con_main.getAttribute("data-badge");


  
      // Ensure site key exists
     
        if (recaptcha_version == "v3" && coolFormKitRecaptcha3.site_key) {

            const script = document.createElement("script");
          script.src = `https://www.google.com/recaptcha/api.js?onload=recaptchaLoaded&render=${coolFormKitRecaptcha3.site_key}&badge=${data_badge}`;
        // script.src = "https://www.google.com/recaptcha/api.js?onload=recaptchaLoaded&render=explicit";

          script.async = true;
          script.defer = true;
          document.body.appendChild(script);

          window.recaptchaLoaded = () => {
            callback();
        };


        } else {
          const script = document.createElement("script");
          script.src = "https://www.google.com/recaptcha/api.js?onload=recaptchaLoaded&render=explicit";
          script.async = true;
          script.defer = true;
          document.body.appendChild(script);

          window.recaptchaLoaded = () => {
            callback();
            };
        }
    }
  
    initRecaptcha() {

        
      if (typeof grecaptcha === "undefined") {
        console.error("❌ ERROR: Google reCAPTCHA API not loaded!");
        return;
      }
  
      this.elements.$recaptchaContainers.each(function () {
        const $container = jQuery(this);
        const siteKey = $container.attr("data-sitekey");
        const theme = $container.attr("data-theme") || "light";
        const size = $container.attr("data-size") || "normal";
  
        if (!siteKey) {
          console.warn("⚠️ reCAPTCHA site key is missing!");
          return;
        }
  
        try {
          grecaptcha.render($container[0], {
            sitekey: siteKey,
            theme: theme,
            size: size,
          });
        } catch (error) {
        //   console.log(error);
        }
      });
  
      if (!this.elements.$form.length) {
        console.warn("⚠️ No form found for reCAPTCHA validation.");
        return;
      }
  
      const con_main = this.elements.$recaptchaContainers[0];
  
      const recaptcha_version = con_main.getAttribute("data-recaptcha-version");
  
      if (recaptcha_version == "v3") {


        grecaptcha.ready(() => {
          grecaptcha
            .execute(coolFormKitRecaptcha3.site_key, { action: "submit"})
            .then((token) => {
              if (!token) {
                console.error("❌ reCAPTCHA token missing!");
                return;
              }
  
              // Ensure token is added to the form
              let $tokenField = this.elements.$form.find(
                'input[name="g-recaptcha-response"]'
              );
  
              if (!$tokenField.length) {
                $tokenField = jQuery(
                  '<input type="hidden" name="g-recaptcha-response">'
                );
                this.elements.$form.append($tokenField);
              }
  
              $tokenField.val(token);
            });
        });
      }
    }
  }
  
  // ✅ Ensure Elementor is ready before registering the handler
  jQuery(window).on("elementor/frontend/init", () => {
    const addHandler = ($element) => {
      elementorFrontend.elementsHandler.addHandler(ReCaptchaHandler, {
        $element,
      });
    };
  
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/cool-form.default",
      addHandler
    );
  });
  