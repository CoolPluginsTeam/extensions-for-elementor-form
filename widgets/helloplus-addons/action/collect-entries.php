<?php
namespace Cool_FormKit\Widgets\HelloPlusAddons\Action;

use HelloPlus\Modules\Forms\Classes\Action_Base;
use Cool_FormKit\Collect_Entries\CFKEF_Save_Entries;
use Cool_FormKit\Includes\Actions\Collect_Entries_Action_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

require_once CFL_PLUGIN_PATH . 'includes/actions/collect-entries-action-trait.php';

class HelloPlus_Collect_Entries extends Action_Base {
	use Collect_Entries_Action_Trait;

	public function get_name(): string {
		return 'cool_formkit_collect_entries';
	}

	public function run( $record, $ajax_handler ): void {
		require_once CFL_PLUGIN_PATH . 'includes/collect-entries/class-cfkef-save-entries.php';
		$settings = $record->get_form_settings( 'save_form_data' );
		if ( 'yes' === $settings ) {
			new CFKEF_Save_Entries();
			do_action( 'cfkef/form/entries', $record, $ajax_handler, $this );
		}
	}
}
