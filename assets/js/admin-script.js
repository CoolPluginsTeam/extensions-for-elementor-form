
document.addEventListener('DOMContentLoaded', function () {
    const toggleAll = document.getElementById('cfkef-toggle-all');
    const elementToggles = document.querySelectorAll('.cfkef-element-toggle');

    if(toggleAll !== null && toggleAll !== undefined){
        toggleAll.addEventListener('change', function () {
            const isChecked = this.checked;
            elementToggles.forEach(function (toggle) {
                if(!toggle.hasAttribute('disabled')){
                    toggle.checked = isChecked;
                }
            });
        });
    }
});


jQuery(document).ready(function($) {
    $('#submit').on('click', function() {

        let site_key_v2 = $("#site_key_v2").val();

        let secret_key_v2 = $("#secret_key_v2").val();


        let site_key_v3 = $("#site_key_v3").val();

        let secret_key_v3 = $("#secret_key_v3").val();

        // if(site_key_v2 == "" || secret_key_v2 == ""){
        //     alert("site key or secret key cannot be empty");
        // }

        // else{

            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: {
                    action: 'my_ajax_action',
                    nonce: ajax_object.nonce,
                    site_key_v2: site_key_v2,
                    secret_key_v2: secret_key_v2,
                    site_key_v3: site_key_v3,
                    secret_key_v3: secret_key_v3,
                },
                success: function(response) {
                    if (response.success) {
                        alert(response.data.message);
                    }
                },
                error: function() {
                    alert('AJAX request failed');
                }
            });
        // }

    });
});
