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
