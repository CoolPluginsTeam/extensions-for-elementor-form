<?php
namespace Cool_FormKit\Widgets\Addons;

use Cool_FormKit\Modules\Forms\Classes\Action_Base;
use Cool_FormKit\Includes\Actions\Whatsapp_Redirect_Action_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/actions/whatsapp-redirect-action-trait.php';

/**
 * Cool Form WhatsApp redirect. Must extend Cool Form Action_Base for the registrar.
 */
class CoolForm_Whatsapp_Redirect extends Action_Base {
	use Whatsapp_Redirect_Action_Trait;

	public function __construct() {
		$this->apply_whatsapp_config(
			array(
				'submit_actions_key' => 'submit_actions',
				'guard_duplicate'    => false,
			)
		);
	}
}
