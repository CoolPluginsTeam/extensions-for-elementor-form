<?php
namespace Cool_FormKit\Includes\Actions;

use Cool_FormKit\Includes\Actions\Whatsapp_Redirect_Action_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/actions/whatsapp-redirect-action-trait.php';

/**
 * Class Whatsapp_Redirect
 */
class Whatsapp_Redirect extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	use Whatsapp_Redirect_Action_Trait;

	protected function get_submit_actions_setting_key(): string {
		return 'submit_actions';
	}

	protected function should_guard_duplicate_section_registration(): bool {
		return false;
	}

	protected function should_bail_on_empty_whatsapp_to(): bool {
		return false;
	}
}
