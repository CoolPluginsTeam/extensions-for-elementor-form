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

    // function to handle entries page
    handleEntriesPage();
    // function to handle shake effect
    buttonShakeEffectHandler();
	// function to handle element card tooltip 
	handleElementCardTooltip();
	// function to handle tooltip buttons actions
	handleTooltipButtonAction();
	// function to handle admin notice
	handleAdminNoticeOnEntriesPage();
	// function to add notice text in admin footer of dashboard pages
	handleNoticeTextAdmitFooter();

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
// function for handle entries page
function handleEntriesPage() {
	const helloPlusToggle = document.querySelector('input[name="cfkef_enable_hello_plus"]');
	const formKitToggle = document.querySelector('input[name="cfkef_enable_formkit_builder"]');
	const entriesTab = document.querySelector('.nav-tab[href="?page=cfkef-entries"]');

	if (helloPlusToggle && formKitToggle) {
		function storeStateToLocal() {
			localStorage.setItem('cfkef_enable_hello_plus', helloPlusToggle.checked ? '1' : '0');
			localStorage.setItem('cfkef_enable_formkit_builder', formKitToggle.checked ? '1' : '0');
		}

		helloPlusToggle.addEventListener('change', () => {
			storeStateToLocal();
			updateEntriesVisibility();
		});

		formKitToggle.addEventListener('change', () => {
			storeStateToLocal();
			updateEntriesVisibility();
		});

		storeStateToLocal();
	}

	function updateEntriesVisibility() {
		const helloPlusEnabled = helloPlusToggle ? helloPlusToggle.checked : localStorage.getItem('cfkef_enable_hello_plus') === '1';
		const formKitEnabled = formKitToggle ? formKitToggle.checked : localStorage.getItem('cfkef_enable_formkit_builder') === '1';

		const bothDisabled = !helloPlusEnabled && !formKitEnabled;

		if (entriesTab) {
			entriesTab.style.display = bothDisabled ? 'none' : '';
		}

        const sidebarLink = document.querySelector('.wp-submenu li a[href="admin.php?page=cfkef-entries"]');

		if (sidebarLink) {
			const menuItem = sidebarLink.closest('li');
			if (menuItem) {
				menuItem.style.display = bothDisabled ? 'none' : '';
			}
		} else {
			setTimeout(() => handleEntriesPage(), 300);
		}
	}

	updateEntriesVisibility();
}

function buttonShakeEffectHandler() {
	const wrappers = document.querySelectorAll('.cfkef-form-element-wrapper');

	wrappers.forEach(wrapper => {
		const headerButton = wrapper.querySelector('.wrapper-header .button');
		const bodyInputs = wrapper.querySelectorAll('.wrapper-body input[type="checkbox"]');
		const headerToggleCheckbox = wrapper.querySelector('.wrapper-header input[type="checkbox"]');

		if (!headerButton || bodyInputs.length === 0) return;

		const input1 = wrapper.querySelector('input[name="cfkef_enable_elementor_pro_form"]');
		const input2 = wrapper.querySelector('input[name="cfkef_enable_hello_plus"]');
		const input3 = wrapper.querySelector('input[name="cfkef_enable_formkit_builder"]');

		function triggerShake() {
			headerButton.classList.add('shake-effect');
		}

		if (headerToggleCheckbox) {
			headerToggleCheckbox.addEventListener('change', triggerShake);
		}

		bodyInputs.forEach(input => {
			input.addEventListener('change', function () {
				let shouldTrigger = false;

				if (input1 && input2 && input3) {
					shouldTrigger = input1.checked || input2.checked || input3.checked;
				} else {
					bodyInputs.forEach(i => {
						if (i.checked) shouldTrigger = true;
					});
				}

				if (shouldTrigger) {
					triggerShake();
				}
			});
		});
	});
}

function handleElementCardTooltip() {
	const cardElms = document.querySelectorAll('.cfkef-form-element-card.cfkef-has-tooltip');

	cardElms.forEach(el => {
		el.addEventListener('click', function () {
			const tooltip = el.querySelector('.cfkef-tooltip');
			if (!tooltip) return;

			// Reset tooltip content
			tooltip.innerHTML = '';

			const action = el.dataset.action;
			const slug = el.dataset.slug;
			const init = el.dataset.init;
			const getCurrentTheme = el.dataset.gettheme; // e.g. 'hello-biz'

			// Default message based on plugin
			let defaultMessage = 'Requires plugin to be activated.';
			if (slug === 'hello-plus') {
				defaultMessage = 'Requires Hello Plus plugin to be activated.';
			} else if (slug === 'elementor-pro') {
				defaultMessage = 'Requires Elementor Pro plugin to be activated.';
			}

			// Toggle visibility
			if (tooltip.style.display === 'block') {
				tooltip.style.display = 'none';
				tooltip.innerHTML = defaultMessage;
			} else {
				tooltip.style.display = 'block';

				if (slug === 'hello-plus' && getCurrentTheme !== 'Hello Biz') {
					tooltip.innerHTML = 'Hello Plus requires Hello Biz theme to be activated. <button class="cfkef-activate-check-theme" data-buttonrole="redirect">Check Theme</button>';
					return; 
				}

				tooltip.innerHTML = defaultMessage;

				// Add the button if valid action
				if (action === 'activate') {
					tooltip.innerHTML += `<button class="cfkef-activate-plugin-btn" data-slug="${slug}" data-init="${init}">Activate Plugin</button>`;
				} else if (action === 'install') {
					let extraCss = '';
					if (el.classList.contains('need-install') && slug === 'elementor-pro') {
						extraCss = 'redirect-elementor-page';
					}
					tooltip.innerHTML += `<button class="cfkef-install-plugin-btn ${extraCss}" data-slug="${slug}" data-init="${init}">Install Plugin</button>`;
				}
			}
		});
	});

	// Hide tooltip if clicked outside
	document.addEventListener('click', function (e) {
		if(e.target.dataset.buttonrole && e.target.dataset.buttonrole === 'redirect'){
			window.open('https://wordpress.org/themes/hello-biz/', '_blank');
		}

		if (!e.target.closest('.cfkef-form-element-card')) {
			document.querySelectorAll('.cfkef-tooltip').forEach(tip => {
				const parentCard = tip.closest('.cfkef-form-element-card');
				if (parentCard) {
					const slug = parentCard.dataset.slug;
					let resetMsg = 'Requires Hello Plus plugin to be activated.';
					if (slug === 'hello-plus') {
						resetMsg = 'Requires Hello Plus plugin to be activated.';
					} else if (slug === 'elementor-pro') {
						resetMsg = 'Requires Elementor Pro plugin to be activated.';
					}
					tip.style.display = 'none';
					tip.innerHTML = resetMsg;
				}
			});
		}
	});
}
function handleTooltipButtonAction(){
	document.addEventListener('click', function (e) {
		let ajaxLoader = jQuery('#cfkef-loader');

		if (e.target.classList.contains('cfkef-install-plugin-btn') && !e.target.classList.contains('redirect-elementor-page')) {
			const slug = e.target.dataset.slug;
			const init = e.target.dataset.init;

			
			ajaxLoader.show();

			// First: Install plugin
			jQuery.ajax({
				type: 'POST',
				url: cfkef_plugin_vars.ajaxurl,
				data: {
					action: 'cfkef_plugin_install',
					slug: slug,
					_ajax_nonce: cfkef_plugin_vars.installNonce
				},
				success: function (res) {
					if (res.success) {
						// After successful install, activate the plugin
						jQuery.ajax({
							type: 'POST',
							url: cfkef_plugin_vars.ajaxurl,
							data: {
								action: 'cfkef_plugin_activate',
								init: init,
								security: cfkef_plugin_vars.nonce
							},
							success: function (res) {
								if (res.success) {
									window.location.reload();
								} else {
									alert('Activation error. Please try to activate manually.');
								}
							},
							error: function () {
								alert('Activation failed. Please try to activate manually.');
							},
							complete: function () {
								ajaxLoader.hide();
							}
						});
					} else {
						alert('Installation error. Please try to install manually.');
						ajaxLoader.hide();
					}
				},
				error: function () {
					alert('Installation failed. Please try to install manually.');
					ajaxLoader.hide();
				}
			});
		} else if(e.target.classList.contains('redirect-elementor-page')){	
			window.open('https://elementor.com/', '_blank');
		}

		if (e.target.classList.contains('cfkef-activate-plugin-btn')) {
			const init = e.target.dataset.init;

			ajaxLoader.show();
			jQuery.ajax({
				type: 'POST',
				url: cfkef_plugin_vars.ajaxurl,
				data: {
					action: 'cfkef_plugin_activate',
					init: init,
					security: cfkef_plugin_vars.nonce
				},
				success: function (res) {
					window.location.reload()
				},
				error: function () {
					alert('Activation failed. Please try to activate manually.');
				},
				complete: function () {
					ajaxLoader.hide();
				}
			});
		}
	});
}
function handleAdminNoticeOnEntriesPage(){
	if(window.location.search !== '?page=cfkef-entries'){
		return;
	}
	const notice = document.querySelector('.coolformkit-license-notice');
    const target = document.querySelector('.cfkef-dashboard-tabs');

    if (notice && target) {
        // Move the notice before .cfkef-dashboard-tabs
		setTimeout(()=>{
			target.parentNode.insertBefore(notice, target);
			// Apply top styling
			notice.style.position = 'relative';
			notice.style.top = '70px';
		},100)
    }
}
function handleNoticeTextAdmitFooter(){
	let wpFooter = document.querySelector('#wpfooter')
	jQuery(wpFooter).append('<p>Enjoyed Cool Formkit Lite? Please leave us a <a href="https://wordpress.org/support/plugin/extensions-for-elementor-form/reviews/#new-post" target="_blank">★★★★★</a> rating. We really appreciate your support!</p>')
}
