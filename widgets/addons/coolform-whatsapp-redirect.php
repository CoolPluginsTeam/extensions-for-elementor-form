<?php
namespace Cool_FormKit\Widgets\Addons;

use Cool_FormKit\Modules\Forms\Classes\Action_Base;
use Cool_FormKit\Includes\Actions\Whatsapp_Redirect_Action_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/actions/whatsapp-redirect-action-trait.php';

/**
 * Class CoolForm_Whatsapp_Redirect
 */
class CoolForm_Whatsapp_Redirect extends Action_Base {
	use Whatsapp_Redirect_Action_Trait;

	protected function get_submit_actions_setting_key(): string {
		return 'submit_actions';
	}

	protected function should_guard_duplicate_section_registration(): bool {
		return false;
	}
}
