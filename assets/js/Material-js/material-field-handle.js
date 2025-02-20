(function($) {
    // Function to handle the custom functionality
    const addHandler = ($element) => {
        const forms = $element.find('.cool-form');

        forms.each(function(formIndex) {
            const textFields = document.querySelectorAll('.cool-form-text');
            textFields.forEach(field => {
                const mdcField = mdc.textField.MDCTextField.attachTo(field);
                const mainInput = mdcField.input
                if(mainInput.type == 'number'){
                    mainInput.addEventListener('input', function(e) {
                        let inputMin = mainInput.min
                        let inputMax = mainInput.max
                        let helperText = mdcField.helperText.foundation.adapter
                        if(e.target.value < Number(inputMin)){
                            mdcField.valid = false
                            helperText.setContent(`Value must be greater than or equal to ${inputMin}`)
                        }else if(e.target.value > Number(inputMax)){
                            mdcField.valid = false
                            helperText.setContent(`Value must be less than or equal to ${inputMax}`)
                        }
                    });
                }else if(mainInput.type == 'tel'){
                    let helperText = mdcField.helperText.foundation.adapter
                    mainInput.addEventListener('input', function(e) {
                        const value = e.target.value;
                        const pattern = mainInput.pattern; 
                        const regex = new RegExp(pattern);
                        
                        if (!regex.test(value)) {
                            mdcField.valid = false
                            helperText.setContent(`The field accepts only numbers and phone characters (#, -, *, etc).`)
                        }else{
                            helperText.setContent('')
                            mdcField.valid = true
                        }
                    });
                }
            });

            document.querySelectorAll('.mdc-select').forEach(selectEl => {
                const select = new mdc.select.MDCSelect(selectEl);
                select.listen('MDCSelect:change', function() {
                    const hiddenSelect = selectEl.querySelector('select');
                    if (hiddenSelect) {
                        hiddenSelect.innerHTML = '';
                        // Create a new option.

                        const newOption = document.createElement('option');
                        newOption.value = select.value; 
                        newOption.textContent = select.selectedText.textContent; 

                        hiddenSelect.appendChild(newOption);
                        
                    }
                });
            });
        });
    };

    // For frontend
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/cool-form.default', addHandler);
    });

})(jQuery);
