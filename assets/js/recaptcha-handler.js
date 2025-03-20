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
        };
    }

    bindEvents() {
        this.waitForElementor(() => {
            this.loadRecaptchaScript(() => {

                this.initRecaptcha();
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
            setTimeout(() => this.waitForElementor(callback), 100);
        }
    }

    loadRecaptchaScript(callback) {
        if (typeof grecaptcha !== "undefined") {
            callback();
            return;
        }

        const script = document.createElement("script");
        script.src = "https://www.google.com/recaptcha/api.js?onload=recaptchaLoaded&render=explicit";
        script.async = true;
        script.defer = true;
        document.body.appendChild(script);

        window.recaptchaLoaded = () => {
            callback();
        };
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

            grecaptcha.render($container[0], {
                sitekey: siteKey,
                theme: theme,
                size: size,
            });
        });
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
