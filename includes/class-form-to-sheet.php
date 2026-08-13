<?php

/**
 * Form to Google Sheet Action
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// --------------------------------------------------
// Detect platform base class
// --------------------------------------------------

if ( class_exists( 'ElementorPro\Modules\Forms\Classes\Action_Base' ) ) {

	// Elementor Pro Forms API available (also preferred when Hello Plus is active)
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
	abstract class Form_to_Sheet_Action extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	}
} elseif ( class_exists( 'HelloPlus\Modules\Forms\Classes\Action_Base' ) ) {

	// Hello Plus only
	// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
	abstract class Form_to_Sheet_Action extends \HelloPlus\Modules\Forms\Classes\Action_Base {
	}
} else {
	// Neither Elementor Pro Forms nor Hello Plus Forms API available
	return;
}

require_once __DIR__ . '/formsdb-marketing-notice.php';
require_once __DIR__ . '/actions/formsdb-sheet-action-trait.php';

use Cool_FormKit\Includes\Actions\FormsDB_Sheet_Action_Trait;

// phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedClassFound
class Sheet_Action extends Form_to_Sheet_Action {
	use FormsDB_Sheet_Action_Trait;

	/**
	 * @return string|null
	 */
	protected function get_sheet_settings_section_tab() {
		return 'connect_google_sheets_tab';
	}
}
