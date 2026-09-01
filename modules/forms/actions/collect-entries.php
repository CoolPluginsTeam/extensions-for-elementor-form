<?php

namespace Cool_FormKit\Modules\Forms\Actions;

use Cool_FormKit\Modules\Forms\Classes\Action_Base;
use Cool_FormKit\Collect_Entries\CFKEF_Save_Entries;
use Cool_FormKit\Includes\Actions\Collect_Entries_Action_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once CFL_PLUGIN_PATH . 'includes/actions/collect-entries-action-trait.php';

/**
 * Collect Entries Action
 *
 * This action collects the entries and stores it in a variable.
 * You can use this variable in the next action or in the same form.
 */
class Collect_Entries extends Action_Base {
	use Collect_Entries_Action_Trait;

	/**
	 * Get the name of the action.
	 *
	 * @return string
	 */
	public function get_name(): string {
		return 'cool_collect_entries';
	}

	/**
	 * Run the action.
	 *
	 * @param \Cool_FormKit\Modules\Forms\Classes\Form_Record $record Form record.
	 * @param \Cool_FormKit\Modules\Forms\Components\Ajax_Handler $ajax_handler Ajax handler.
	 */
	public function run( $record, $ajax_handler ) {
		require_once CFL_PLUGIN_PATH . 'includes/collect-entries/class-cfkef-save-entries.php';
		new CFKEF_Save_Entries();

		do_action( 'cfkef/form/entries', $record, $ajax_handler, $this );
	}
}
