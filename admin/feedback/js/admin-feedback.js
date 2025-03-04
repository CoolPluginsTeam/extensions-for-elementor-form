(function($){
    $(document).ready(function(){
        let plugin_name = 'extensions-for-elementor-form';
        let plugin_slug = 'eef';
        
        $target = $('#the-list').find('[data-slug="'+plugin_name+'"] span.deactivate a');
        var plugin_deactivate_link = $target.attr('href');

        $($target).on('click', function(event){
            event.preventDefault();
            $('#wpwrap').css('opacity','0.4');

            $("#cool-plugins-feedback-"+plugin_slug).animate({
                opacity:1
            },200,function(){
                $("#cool-plugins-feedback-"+plugin_slug).removeClass('hide-feedback-popup');
                $("#cool-plugins-feedback-"+plugin_slug).find('#cool-plugin-submitNdeactivate').addClass(plugin_slug);
                $("#cool-plugins-feedback-"+plugin_slug).find('#cool-plugin-skipNdeactivate').addClass(plugin_slug);
            });
        });

        $('#wpwrap').on('click', function(ev){
            if( $("#cool-plugins-feedback-"+plugin_slug+".hide-feedback-popup").length==0 ){
                ev.preventDefault();
                $("#cool-plugins-feedback-"+plugin_slug).animate({
                    opacity:0
                },200,function(){
                    $("#cool-plugins-feedback-"+plugin_slug).addClass("hide-feedback-popup");
                    $("#cool-plugins-feedback-"+plugin_slug).find('#cool-plugin-submitNdeactivate').removeClass(plugin_slug);
                    $("#cool-plugins-feedback-"+plugin_slug).find('#cool-plugin-skipNdeactivate').removeClass(plugin_slug);
                    $('#wpwrap').css('opacity','1');
                })

            }
        })

        $(document).on('click','#cool-plugin-submitNdeactivate.'+plugin_slug, function(event){
            let nonce = $('#_wpnonce').val();
            let reason = $('.cp-feedback-input:checked').val();
            let reasonkeychecked = $('.cp-feedback-input-wrapper>input').is(":checked");
            let message = '';
            $('.cp-feedback-error').remove();
            if(reasonkeychecked){
                if(  $('#cool-plugins-feedback-'+plugin_slug + ' #cp-feedback-terms-input').is(":checked") === false){
                    var getSelectedValue = document.getElementsByClassName('cp-feedback-terms-input');
                    var para = document.createElement("p");
                    var content = document.createTextNode('* Please agree to the details by checking the box before submitting the form.');
                    para.appendChild(content);
                    para.classList.add('cp-feedback-error');
                    $(getSelectedValue).parent().append(para);
                    return;
                }
                else{
                    if($('#cool-plugins-feedback-'+plugin_slug + ' textarea[name="reason_'+reason+'"]').val() === ''){
                        message = "N/A";
                    }else{
                        message = $('#cool-plugins-feedback-'+plugin_slug + ' textarea[name="reason_'+reason+'"]').val();                        
                    }
                }

                $.ajax({
                    url:ajaxurl,
                    method:'POST',
                    data:{
                        'action':plugin_slug+'_submit_deactivation_response',
                        '_wpnonce':nonce,
                        'reason':reason,
                        'message':message,
                    },
                    beforeSend:function(data){
                        $('#cool-plugin-submitNdeactivate').text('Deactivating...');
                        $('#cool-plugin-submitNdeactivate').attr('id','deactivating-plugin');
                        $('.cp-feedback-loader').show();
                        $('#cool-plugin-skipNdeactivate').remove();
                    },
                    success:function(res){
                        $('.cp-feedback-wrapper').hide();
                        $('.cp-feedback-loader').hide();
                        window.location = plugin_deactivate_link;
                        $('#deactivating-plugin').text('Deactivated');
                    }
                })
            }
            else{
                var getSelectedValue = document.getElementsByClassName('cp-feedback-terms-input');   
                var para = document.createElement("p");
                var content = document.createTextNode('* Please select at least one reason.');
                para.appendChild(content);
                para.classList.add('cp-feedback-error');
                $(getSelectedValue).parent().append(para);
            }
        });

        $(document).on('click', '#cool-plugin-skipNdeactivate.'+plugin_slug, function(){
            $('#cool-plugin-skipNdeactivate').attr('id','deactivating-plugin');
            window.location = plugin_deactivate_link;
        });

    });
})(jQuery);
