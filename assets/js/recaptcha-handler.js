
class ReCaptchaHandler extends elementorModules.frontend.handlers.Base {
    getDefaultSettings() {
      return {
        selectors: {
          recaptcha: ".cool-form-recaptcha",
          submit: 'button[type="submit"]',
          recaptchaResponse: '[name="g-recaptcha-response"]',
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
        t = "v3" !== e.recaptchaVersion,
        a = [];
      a.forEach((e) => window.grecaptcha.reset(e));
      const s = window.grecaptcha.render(this.elements.$recaptcha[0], e);

      this.elements.$form.on("reset error", () => {
        window.grecaptcha.reset(s);
        if(e.size == 'compact'){

          let ele = document.querySelector(".cool-form-recaptcha div");
  
          ele.style.width = "310px";
        }

        else if((e.recaptchaVersion == 'v3' && e.badge == "inline") || (e.type == 'v3' && e.badge == "inline")){

          let ele = document.querySelector(".cool-form-recaptcha div");
  
          ele.style.width = "300px";
          ele.style.boxShadow = "none";
  
  
        }
  
        else if((e.recaptchaVersion == 'v3' && e.badge == "bottomleft") || (e.type == 'v3' && e.badge == "bottomleft")){
  
          let ele = document.querySelector(".cool-form-recaptcha div");
  
          ele.style.left = "0px";
          ele.style.width = "300px";
          ele.style.boxShadow = "none";
  
  
        }
  
        else if((e.recaptchaVersion == 'v3' && e.badge == "bottomright") || (e.type == 'v3' && e.badge == "bottomright")){
  
          let ele = document.querySelector(".cool-form-recaptcha div");
  
          ele.style.right = "0px";
          // ele.style.width = "300px";
          ele.style.boxShadow = "none";
  
        }
      }),
        t
          ? this.elements.$recaptcha.data("widgetId", s)
          : (a.push(s),
            this.elements.$submit.on("click", (e) => this.onV3FormSubmit(e, s)));


      if(e.size == 'compact'){

        let ele = document.querySelector(".cool-form-recaptcha div");

        ele.style.width = "310px";
      }

      else if((e.recaptchaVersion == 'v3' && e.badge == "inline") || (e.type == 'v3' && e.badge == "inline")){

        let ele = document.querySelector(".cool-form-recaptcha div");

        ele.style.width = "300px";
        ele.style.boxShadow = "none";


      }

      else if((e.recaptchaVersion == 'v3' && e.badge == "bottomleft") || (e.type == 'v3' && e.badge == "bottomleft")){

        let ele = document.querySelector(".cool-form-recaptcha div");

        ele.style.left = "0px";
        ele.style.width = "300px";
        ele.style.boxShadow = "none";


      }

      else if((e.recaptchaVersion == 'v3' && e.badge == "bottomright") || (e.type == 'v3' && e.badge == "bottomright")){

        let ele = document.querySelector(".cool-form-recaptcha div");

        ele.style.right = "0px";
        // ele.style.width = "300px";
        ele.style.boxShadow = "none";

      }


      
    }
    onV3FormSubmit(e, t) {
      e.preventDefault(),
        window.grecaptcha.ready(() => {
          const e = this.elements.$form;
          grecaptcha
            .execute(t, { action: this.elements.$recaptcha.data("action") })
            .then((t) => {


              this.elements.$recaptchaResponse
                ? this.elements.$recaptchaResponse.val(t)
                : ((this.elements.$recaptchaResponse = jQuery("<input>", {
                    type: "hidden",
                    value: t,
                    name: "g-recaptcha-response",
                  })),
                  e.append(this.elements.$recaptchaResponse));
              const a =
                !e[0].reportValidity || "function" != typeof e[0].reportValidity;
              (a || e[0].reportValidity()) && e.trigger("submit");
            });
        });
    }
    onRecaptchaApiReady() {
      
      window.grecaptcha && window.grecaptcha.render
        ? this.addRecaptcha()
        : setTimeout(() => this.onRecaptchaApiReady(), 350);
    }
  }
  jQuery(window).on("elementor/frontend/init", () => {
    const e = (e) => {
      elementorFrontend.elementsHandler.addHandler(ReCaptchaHandler, {
        $element: e,
      });
    };
    elementorFrontend.hooks.addAction(
      "frontend/element_ready/cool-form.default",
      e
    );
  });