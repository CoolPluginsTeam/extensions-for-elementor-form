/**
 * Stub for Elementor Pro editor-templates-extended (avoids editor stack overflow).
 */
(function () {
	'use strict';

	window.elementorV2 = window.elementorV2 || {};
	window.elementorV2.editorTemplatesExtended = {
		init: function () {},
		useLoadedTemplates: function () {
			return [];
		},
		isCoreHandlingTemplateStyles: function () {
			return true;
		},
	};
})();
