class DateFieldInputHandler extends elementorModules.frontend.handlers.Base {

    getDefaultSettings() {
        return {
            selectors: {
                calInput: '.cool-form-field-type-date',
                calDiv: '.is-field-type-date',
                form: '.cool-form'
            },
        };
    }

    getDefaultElements() {
        const selectors = this.getSettings('selectors');
        return {
            $calInput: this.$element.find(selectors.calInput),
            $calDiv: this.$element.find(selectors.calDiv),
            $form: this.$element.find(selectors.form),
        };
    }

    bindEvents() {
        this.initFlatpickr();
    }    

    initFlatpickr() {
        const { $calDiv } = this.getDefaultElements();
        if ($calDiv.length && typeof flatpickr !== 'undefined') {
            $calDiv.each(function () {
                const $input = jQuery(this).find('input'); 
                if ($input.length) {
                    if(!$input.hasClass('cool-form-use-native')){
                        const minDate = $input[0].min;
                        const maxDate = $input[0].max;
                        flatpickr($input[0], {
                            enableTime: false, 
                            dateFormat: "Y-m-d",
                            minDate: minDate,
                            maxDate: maxDate
                        });
                    }
                }
            });
        }
    }
}

jQuery(window).on('elementor/frontend/init', () => {
    const calHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(DateFieldInputHandler, {
            $element,
        });
    };
  
    elementorFrontend.hooks.addAction('frontend/element_ready/cool-form.default', calHandler);
}); 




