<?php

namespace Cool_FormKit\Widgets\HelloPlusAddons;
/**
 * Form to Google Sheet Action
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/formsdb-marketing-notice.php';
require_once CFL_PLUGIN_PATH . 'includes/actions/formsdb-sheet-action-trait.php';

use Cool_FormKit\Includes\Actions\FormsDB_Sheet_Action_Trait;
use HelloPlus\Modules\Forms\Classes\Action_Base;

class Sheet_HelloPlus_Action extends Action_Base {
	use FormsDB_Sheet_Action_Trait;

	/**
	 * @return string
	 */
	protected function get_sheet_submit_actions_key() {
		return 'cool_formkit_submit_actions';
	}
}
