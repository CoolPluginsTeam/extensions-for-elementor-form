
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
    const termsLinks = document.querySelectorAll('.ccpw-see-terms');
    const termsBox = document.getElementById('termsBox');

    termsLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            if (termsBox) {
                // Toggle display using plain JavaScript
                const isVisible = termsBox.style.display === 'block';
                termsBox.style.display = isVisible ? 'none' : 'block';
                link.innerHTML = !isVisible ? 'Hide Terms' : 'See terms';
            }
        });
    });

});

