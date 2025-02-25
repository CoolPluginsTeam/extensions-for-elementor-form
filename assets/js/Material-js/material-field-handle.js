(function($) {
    // Function to handle the custom functionality
    const addHandler = ($element) => {
        const forms = $element.find('.cool-form');

        forms.each(function(formIndex) {
            const textFields = document.querySelectorAll('.cool-form-text');
            textFields.forEach(field => {
                // const mdcField = mdc.textField.MDCTextField.attachTo(field);
                const mdcField = mdc.textfield.MDCTextField.attachTo(field);
                const mainInput = mdcField.input
                if(mainInput.type == 'number'){
                    mainInput.addEventListener('input', function(e) {
                        let inputVal = e.target.value
                        let inputMin = mainInput.min
                        let inputMax = mainInput.max
                        let helperText = mdcField.helperText.foundation.adapter


                        if(inputVal === ''){
                            mdcField.valid = false
                            mdcField.trailingIcon.root.style.display = 'initial'
                            helperText.setContent(`Please enter a number value to the field`)
                        }else{
                            if(inputMin !== ""){
                                if(inputVal < Number(inputMin)){
                                    mdcField.valid = false
                                    mdcField.trailingIcon.root.style.display = 'initial'
                                    helperText.setContent(`Value must be greater than or equal to ${inputMin}`)
                                }
                            }
                            if(inputMax !== ''){
                                if(inputVal > Number(inputMax)){
                                    mdcField.valid = false
                                    mdcField.trailingIcon.root.style.display = 'initial'
                                    helperText.setContent(`Value must be less than or equal to ${inputMax}`)
                                }
                            }
                        }
                    });
                }else if(mainInput.type == 'tel'){
                    let helperTextAdapter = mdcField.helperText.foundation.adapter;

                    const validateTel = (e) => {
                        const value = e.target.value;
                        const pattern = mainInput.pattern; 
                        const regex = new RegExp(pattern);

                        if(value !== ''){
                            if (!regex.test(value)) {
                                mdcField.valid = false;
                                mdcField.trailingIcon.root.style.display = 'initial'
                                helperTextAdapter.setContent('The field accepts only numbers and phone characters (#, -, *, etc).');
                            }else {
                                mdcField.valid = true;
                                helperTextAdapter.setContent('');
                            }
                        }else{
                            mdcField.trailingIcon.root.style.display = 'none'
                        }
                    };

                    mainInput.addEventListener('input', validateTel);
                    mainInput.addEventListener('blur', validateTel);
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

        forms.find('.cool-form-submit-button').click((e) => {
            const $invalidField = forms.find('.mdc-text-field--invalid');
            if ($invalidField.length) {
                e.preventDefault();
                $invalidField.find('.mdc-text-field__input').focus();
                return false;
            }
        });
        
    };

    // For frontend
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/cool-form.default', addHandler);
    });

})(jQuery);
