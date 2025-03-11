(function($) {
    "use strict";

    // function for all dynamic tag to show the Id's of all elements
    $( document ).ready( function () { 

        
        jQuery(document).on('click','.elementor-control-form_fields_conditions_tab',(event)=>{
            const mainWrp=jQuery(event.currentTarget).closest('.elementor-repeater-row-controls.editable');
            const addBtn=mainWrp.find('.elementor-control-cfl_repeater_data .elementor-repeater-add');
            jQuery(addBtn).text('+ Add Conditions');
            const logicMeet = mainWrp.find('.elementor-control-cfl_logic_meet .elementor-control-content .elementor-control-field .elementor-control-input-wrapper select option')[1];
            const logicMode = mainWrp.find('.elementor-control-cfl_logic_mode .elementor-control-content .elementor-control-field .elementor-control-input-wrapper .elementor-choices input');
            const showHideFieldLableText = mainWrp.find('.elementor-control-cfl_repeater_data .elementor-control-content label span')
            function updateLabel() {
                for (let i = 0; i < logicMode.length; i++) {
                    if (logicMode[i].checked) {
                        if (logicMode[i].value === 'hide') {
                            showHideFieldLableText[0].textContent = 'Hide fields if';
                        } else {
                            showHideFieldLableText[0].textContent = 'Show fields if';
                        }
                        break; // Exit the loop once a checked radio button is found
                    }
                }
            }

            // Update label on initial page load
            updateLabel();

            // Event listener to update label when radio button selection changes
            logicMode.on('change', updateLabel);
            if (!logicMeet.innerHTML.includes('(PRO)')) {
                logicMeet.innerHTML += ' (PRO)';
                logicMeet.setAttribute('disabled', 'disabled');
                logicMeet.style.backgroundColor = '#00000015';
            }

            const controlsWrp=mainWrp.find('.elementor-control-cfl_logic_field_id')?.closest('.elementor-repeater-row-controls');

            controlsWrp?.addClass('editable');
        });
        jQuery(document).on('mouseenter', '.elementor-repeater-row-controls.editable .elementor-control-cfl_repeater_data .elementor-repeater-fields-wrapper', function() {
            const optionsFields = jQuery('.elementor-repeater-row-controls.editable .elementor-control-cfl_repeater_data .elementor-repeater-fields-wrapper .elementor-repeater-fields .elementor-repeater-row-controls .elementor-control-cfl_logic_field_is .elementor-control-content .elementor-control-field .elementor-control-input-wrapper select option');
            for(let i = 0;i < optionsFields.length;i++){
                if (optionsFields[i].value !== "==" && optionsFields[i].value !== "!=" && optionsFields[i].value !== ">" && optionsFields[i].value !== "<" && !optionsFields[i].innerHTML.includes('(PRO)')) {
                    optionsFields[i].innerHTML += ' (PRO)';
                    optionsFields[i].setAttribute('disabled', 'disabled');
                    optionsFields[i].style.backgroundColor = '#00000015';
                    }
            }
        });


        $("body").on("click",'.elementor-control-tag-area[data-setting="cfl_logic_field_id"]',function(event){
            if( $(this).data("isChecked") != "ok" ) {
              $(this).after('<div class="elementor-control-dynamic-switcher elementor-control-unit-1 cfl-add-tag" data-tooltip="add Tags" original-title=""><i class="eicon-database" aria-hidden="true"></i><span class="elementor-screen-only">Dynamic Tags</span></div>');
              $(this).data("isChecked","ok");
            }
        }) 

        // create list of id's of form fields for dynamic tags
        $("body").on("click",".cfl-add-tag",function(event){
           var idList ='<ul class="cfl-dynamic-tag">';
           var formWrapper = $(this).closest(".elementor-repeater-fields-wrapper").parents(".elementor-repeater-fields-wrapper");
           $(".elementor-form-field-shortcode",formWrapper).each(function() {
            const regexPattren = /\".*?\"/gm;
            let matches;
            var fieldName = $(this).val();
            while ((matches = regexPattren.exec(fieldName)) !== null) {
                if (matches.index === regexPattren.lastIndex) {
                    regexPattren.lastIndex++;
                }
                matches.forEach((match, groupIndex) => {
                    fieldName = match.replaceAll('"', "");
                });
            }
            $(this).data("actual-id", fieldName);

            idList += '<li title="Field ID" data-field-id="' + fieldName + '">' + fieldName + '</li>';
            //  idList +='<li title="Field ID">'+fieldName+'</li>'; 
           });
          idList +='</ul>';
          $(this).closest(".elementor-control-input-wrapper").append(idList);
        })
        
        $(document).mouseup(function(event) 
        {
            var container = $(".cfl-dynamic-tag");
        
            if (!container.is(event.target) && container.has(event.target).length === 0) 
            {
                container.hide();
            }
        });

            // function to copy the id of the selected option from dynamic tag
            $("body").on("click", ".cfl-dynamic-tag li", function (event) {
                var selectedValue = $(this).data("field-id");
                $(this).parent().siblings().val(selectedValue);                
                $(this).parent().siblings().trigger('input');
                if($('#elementor-panel-saver-button-publish')[0].classList.contains('elementor-disabled')){
                    $('#elementor-panel-saver-button-publish')[0].classList.remove('elementor-disabled');
                }
                setTimeout(function () {
                    $(".cfl-dynamic-tag").remove();
                }, 500);
        });

        // Elementor Review Notice Start
        jQuery(document).on('click','#cfl_elementor_review_dismiss',(event)=>{
            jQuery(".cfl_elementor_review_notice").hide();
            const btn=jQuery(event.target);
            const nonce=btn.data('nonce');
            const url=btn.data('url');

            jQuery.ajax({
                type: 'POST',
                // eslint-disable-next-line no-undef
                url: url, // Set this using wp_localize_script
                data: {
                    action: 'cfl_elementor_review_notice',
                    cfl_notice_dismiss: true,
                    nonce: nonce
                },
                success: (response) => {
                    btn.closest('.elementor-control').remove();
                },
                error: (xhr, status, error) => {
                    console.log(xhr.responseText);
                    console.log(error);
                    console.log(status);
                }
            });
        });
        // Elementor Review Notice End
    })
})(jQuery);