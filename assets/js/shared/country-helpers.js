/**
 * Shared country-field helpers used by Elementor/Cool Form/Hello Plus and Atomic Form.
 */
window.CFKEF = window.CFKEF || {};

window.CFKEF.CountryHelpers = (function ($) {
	'use strict';

	function splitCountryList(value) {
		if (!value || value === '') {
			return [];
		}
		if (Array.isArray(value)) {
			return value.map(function (country) {
				return String(country).trim();
			}).filter(Boolean);
		}
		return String(value)
			.split(',')
			.map(function (country) {
				return country.trim();
			})
			.filter(Boolean);
	}

	function resolveCountryLists(includeRaw, excludeRaw) {
		var includeArr = splitCountryList(includeRaw);
		var excludeArr = splitCountryList(excludeRaw);
		var commonArr = [];

		if (excludeArr.length > 0 && includeArr.length > 0) {
			commonArr = includeArr.filter(function (code) {
				return excludeArr.indexOf(code) !== -1;
			});
			includeArr = includeArr.filter(function (code) {
				return excludeArr.indexOf(code) === -1;
			});
		}

		return {
			includeArr: includeArr,
			excludeArr: excludeArr,
			commonArr: commonArr,
		};
	}

	function reorderPreferredCountries(itiInstance, preferredCountries) {
		if (!itiInstance || !itiInstance.countryList || !preferredCountries || !preferredCountries.length) {
			return;
		}
		var countryList = $(itiInstance.countryList);
		preferredCountries.slice().reverse().forEach(function (countryCode, index) {
			var countryListItem = countryList.find('li[data-country-code="' + String(countryCode).toLowerCase() + '"]');
			if (!countryListItem.length) {
				countryListItem = countryList.find('li[data-country-code="' + String(countryCode) + '"]');
			}
			if (countryListItem.length) {
				if (index === 0) {
					countryList.prepend('<hr></hr>');
				}
				countryList.prepend(countryListItem);
			}
		});
	}

	function fetchGeoIPData(defaultCountry, uniqueId) {
		if (!window._ccfefGeoIpPromises) {
			window._ccfefGeoIpPromises = {};
		}
		if (window._ccfefGeoIpPromises[uniqueId]) {
			return window._ccfefGeoIpPromises[uniqueId];
		}

		var geo = (typeof CCFEFCustomData !== 'undefined' && CCFEFCustomData.geoLookup)
			|| (typeof CYFEFCustomData !== 'undefined' && CYFEFCustomData.geoLookup)
			|| {};

		if (!geo.ajaxUrl || !geo.action || !geo.nonce) {
			return Promise.resolve(defaultCountry);
		}

		var body = new URLSearchParams();
		body.set('action', geo.action);
		body.set('nonce', geo.nonce);

		window._ccfefGeoIpPromises[uniqueId] = fetch(geo.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: body.toString(),
		})
			.then(function (res) {
				return res.json();
			})
			.then(function (json) {
				var code = json && json.success && json.data && json.data.country
					? String(json.data.country).toLowerCase()
					: defaultCountry;
				return code || defaultCountry;
			})
			.catch(function () {
				return defaultCountry;
			});

		return window._ccfefGeoIpPromises[uniqueId];
	}

	return {
		splitCountryList: splitCountryList,
		resolveCountryLists: resolveCountryLists,
		reorderPreferredCountries: reorderPreferredCountries,
		fetchGeoIPData: fetchGeoIPData,
	};
})(window.jQuery);
