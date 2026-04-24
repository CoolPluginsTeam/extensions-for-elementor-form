/* Atomic form: navigate when an action returns redirect_url (core only updates form state). */
(function () {
	'use strict';

	if (typeof window.fetch !== 'function') {
		return;
	}

	const origFetch = window.fetch;

	window.fetch = function (input, init) {
		return origFetch.call(this, input, init).then(function (response) {
			try {

				const body = init && init.body;
				if (!(body instanceof FormData)) {
					return response;
				}
				if (body.get('action') !== 'elementor_pro_atomic_forms_send_form') {
					return response;
				}
				const ct = response.headers.get('content-type') || '';
				if (ct.indexOf('application/json') === -1) {
					return response;
				}

				response
					.clone()
					.json()
					.then(function (data) {
						if (!data || !data.success || !data.data || !data.data.data) {
							return;
						}
						const results = data.data.data.actionResults;
						console.log(results);
						if (!Array.isArray(results)) {
							return;
						}
						for (let i = 0; i < results.length; i++) {
							const r = results[i];
							if (
								r &&
								r.status === 'success' &&
								typeof r.redirect_url === 'string' &&
								r.redirect_url
							) {
								try {
									const url = new URL(r.redirect_url, window.location.href);
									if (url.protocol === 'http:' || url.protocol === 'https:') {
										window.location.assign(url.href);
									}
								} catch (e) {
									// Invalid URL — ignore.
								}
								break;
							}
						}
					})
					.catch(function () {});
			} catch (e) {
				// Non-fatal; return normal response.
			}
			return response;
		});
	};
})();
