
class cloudflare_ReCaptchaHandler extends elementorModules.frontend.handlers.Base {

    

    getDefaultSettings() {
      return {
        selectors: {
          recaptcha: ".cool-form-cloudflare-recaptcha",
          submit: 'button[type="submit"]',
          recaptchaResponse: '[name="cool-form-cloudflare-recaptcha-response"]',
        },
      };
    }
    getDefaultElements() {
      const { selectors: e } = this.getDefaultSettings(),
        t = { $recaptcha: this.$element.find(e.recaptcha) };
      return (
        (t.$form = t.$recaptcha.parents("form")),
        (t.$submit = t.$form.find(e.submit)),
        t
      );
    }
    bindEvents() {

      this.onRecaptchaApiReady();
    }
    isActive(e) {
      const { selectors: t } = this.getDefaultSettings();
      return e.$element.find(t.recaptcha).length;
    }

    addRecaptcha() {

      
      
      const e = this.elements.$recaptcha.data(),
      a = [];
      
      a.forEach((e) => window.turnstile.reset(e));

      let captchaLoaded = false;
      
      const s = window.turnstile.render('.cool-form-cloudflare-recaptcha', {
        e,
        callback: (token) => {
          captchaLoaded = true;
        },
        "error-callback": () => {
          captchaLoaded = true;
          console.error("Turnstile error occurred.");

          this.elements.$recaptcha.html(`
            <div class="recaptcha-error" style="color:red; font-size:14px; margin-top:10px;">
              CAPTCHA failed to load. Check site key or network and try again.
            </div>
          `);
        }
      });

      this.elements.$form.on("reset error", () => {
        window.turnstile.reset(s);
      })
      
    }
    onRecaptchaApiReady() {

      
      window.turnstile && window.turnstile.render
        ? this.addRecaptcha()
        : setTimeout(() => this.onRecaptchaApiReady(), 350);
    }
  }



  jQuery(window).on("elementor/frontend/init", () => {
    const e = (e) => {

      elementorFrontend.elementsHandler.addHandler(cloudflare_ReCaptchaHandler, {
        $element: e,
      });
    };
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/cool-form.default",
      e
    );
  });

