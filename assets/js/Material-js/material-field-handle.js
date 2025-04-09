(function($) {
    // Function to handle the custom functionality
    const addHandler = ($element) => {
        const forms = $element.find('.cool-form');

        // attach form fields with basic validation
        forms.each(function(formIndex) {
            const textFields = document.querySelectorAll('.cool-form-text');
            textFields.forEach(field => {
                // const mdcField = mdc.textField.MDCTextField.attachTo(field);
                const mdcField = mdc.textfield.MDCTextField.attachTo(field);
                const mainInput = mdcField.input
                if (mainInput.type === 'number') {
                    mainInput.addEventListener('input', function(e) {
                        let inputVal = e.target.value;
                        let inputMin = mainInput.min;
                        let inputMax = mainInput.max;
                        let helperText = mdcField.helperText.foundation.adapter;
                
                        if (inputVal === '') {
                            mdcField.valid = false;
                            mdcField.trailingIcon.root.style.display = 'initial';
                            helperText.setContent('Please enter a number value to the field');
                        } else {
                            const numVal = Number(inputVal);
                
                            if (inputMin !== "" && numVal < Number(inputMin)) {
                                mdcField.valid = false;
                                mdcField.trailingIcon.root.style.display = 'initial';
                                helperText.setContent(`Value must be greater than or equal to ${inputMin}`);
                            }
                            else if (inputMax !== "" && numVal > Number(inputMax)) {
                                mdcField.valid = false;
                                mdcField.trailingIcon.root.style.display = 'initial';
                                helperText.setContent(`Value must be less than or equal to ${inputMax}`);
                            }
                            else {
                                mdcField.valid = true;
                                mdcField.trailingIcon.root.style.display = 'none';
                                helperText.setContent('');
                            }
                        }
                    });
                }else if(mainInput.type == 'tel'){
                    let helperTextAdapter = mdcField.helperText.foundation.adapter;

                    setTimeout(()=>{
                        if(jQuery(mainInput.parentNode).hasClass('iti')){
                            return
                        }else{  
                            mainInput.addEventListener('input', validateTel);
                            mainInput.addEventListener('blur', validateTel);
                        }
                    },100)
                    
                    const validateTel = (e) => {
                        const value = e.target.value;
                        const pattern = '^' + mainInput.pattern + '$';
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
                } else if ( mainInput.type === 'email' ) {
                    let helperTextAdapter = mdcField.helperText.foundation.adapter;
                    const validateEmail = (e) => {
                        const value = e.target.value;
                        // Basic email regex that checks for a pattern like "user@domain.tld"
                        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
                        if ( value !== '' ) {
                            if (!regex.test(value)) {
                                mdcField.valid = false;
                                mdcField.trailingIcon.root.style.display = 'initial';
                                helperTextAdapter.setContent('Please enter a valid email address.');
                            } else {
                                mdcField.valid = true;
                                mdcField.trailingIcon.root.style.display = 'none';
                                helperTextAdapter.setContent('');
                            }
                        } else {
                            mdcField.trailingIcon.root.style.display = 'none';
                        }
                    };
                    mainInput.addEventListener('input', validateEmail);
                    mainInput.addEventListener('blur', validateEmail);
                }
            });

            // append select field value to hiddedn select tag for correct submission
            document.querySelectorAll('.mdc-select').forEach(selectEl => {
                const select = new mdc.select.MDCSelect(selectEl);
                if (select.selectedIndex === -1) {
                    select.selectedIndex = 0;
                    const hiddenSelect = selectEl.querySelector('select');
                    if (hiddenSelect) {
                        hiddenSelect.innerHTML = '';
                        const newOption = document.createElement('option');
                        newOption.value = select.value; 
                        newOption.textContent = select.selectedText.textContent;
                        hiddenSelect.appendChild(newOption);
                    }
                }
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

        // handle form submission with required fields validation
        forms.find('.cool-form-submit-button').click((e) => {
            const $invalidField = forms.find('.mdc-text-field--invalid');
        
            // Check each required field container for empty text inputs or select elements.
            const $emptyRequired = forms.find('.is-field-required').filter(function() {
                
                const $this = $(this);

                
                let textEmpty = false, selectEmpty = false,acceptanceEmpty = false;

                // check for all text form fields
                if ($this.find('.mdc-text-field__input').length) {
                    textEmpty = $this.find('.mdc-text-field__input').first().val().trim() === "";

                    var classes = $this.attr('class').split(/\s+/);
                    var fieldTypeValue = '';
                    classes.forEach(function(className) {
                        var match = className.match(/^is-field-type-(.+)$/);
                        if (match) {
                            fieldTypeValue = match[1];
                        }
                    });

                    if(textEmpty){
                        $this.find(`#cool-${fieldTypeValue}-error`).text('This Field is required').css('color','#bb2a46');
                        $this.find(`.cool-${fieldTypeValue}-error-icon`).css('display','initial');
                    }else{
                        $this.find(`#cool-${fieldTypeValue}-error`).text('');
                        $this.find(`.cool-${fieldTypeValue}-error-icon`).css('display','none');
                    }
                }

                // check for select form field
                if ($this.find('select').length) {
                    let selectMainWrapper = $this.find('.mdc-select').first();
                    selectEmpty = $this.find('select').first().val().trim() === "";
                    if(selectEmpty){
                        selectMainWrapper.addClass('mdc-select--invalid');
                        $this.find('#cool-select-error').text('This Field is required').css('color','#bb2a46');
                    }else{
                        selectMainWrapper.removeClass('mdc-select--invalid');
                        $this.find('#cool-select-error').text('');
                    }
                }

                // check for acceptance form field
                if($this.find('.mdc-checkbox').length > 0){
                    acceptanceEmpty = !$this.find('.mdc-checkbox').find('input')[0].checked 
                    if(acceptanceEmpty){
                        $this.find(`#cool-acceptance-error`).text('This Field is required').css('color','#bb2a46')
                    }else{
                        $this.find(`#cool-acceptance-error`).text('')
                    }
                }                

                return textEmpty || selectEmpty || acceptanceEmpty;
            });
            
           
            // add focus effect if field has any type of error
            if ($invalidField.length || $emptyRequired.length) {
                e.preventDefault();
                
                let $target;
                if ($invalidField.length) {
                    $target = $invalidField.first().find('.mdc-text-field__input');
                    $target.focus();
                } else {
                    if($emptyRequired.find('label').hasClass('cool-form-text')){
                        $target = $emptyRequired.find('.mdc-text-field__input').first();

                        $target.focus();
                    } else if($emptyRequired.hasClass('is-field-type-acceptance')){
                        $target = $emptyRequired;
                    } else if($emptyRequired.hasClass('is-field-type-select')){
                        if($emptyRequired.find('select').val().trim() === ''){
                            let selectMainWrapper = $emptyRequired.find('.mdc-select').first();
                            $target = selectMainWrapper;
                        }
                    }
                }
                
                if ($target && $target.length) {
                    $('html, body').animate({
                        scrollTop: $target.offset().top - 100 
                    }, 100);
                }
                return false;
            }
        });
        
    };

    // For frontend
    $(window).on('elementor/frontend/init', () => {
        elementorFrontend.hooks.addAction('frontend/element_ready/cool-form.default', addHandler);
    });

})(jQuery);
