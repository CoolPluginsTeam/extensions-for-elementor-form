class TimeFieldInputHandler extends elementorModules.frontend.handlers.Base {

    getDefaultSettings() {
        return {
            selectors: {
                calInput: '.cool-form-field-type-time',
                calDiv: '.is-field-type-time',
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
                        flatpickr($input[0], {
                            enableTime: true,         
                            noCalendar: true,         
                            dateFormat: "H:i",        
                        });
                    }
                }
            });
        }
    }
    
}

jQuery(window).on('elementor/frontend/init', () => {
    const calHandler = ($element) => {
        elementorFrontend.elementsHandler.addHandler(TimeFieldInputHandler, {
            $element,
        });
    };
  
    elementorFrontend.hooks.addAction('frontend/element_ready/cool-form.default', calHandler);
}); 




