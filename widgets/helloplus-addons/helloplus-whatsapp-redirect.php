<?php
namespace Cool_FormKit\Widgets\HelloPlusAddons;

use HelloPlus\Modules\Forms\Classes\Action_Base;
use Cool_FormKit\Includes\Actions\Whatsapp_Redirect_Action_Trait;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/actions/whatsapp-redirect-action-trait.php';

/**
 * Hello Plus WhatsApp redirect. Must extend Hello Plus Action_Base for the registrar.
 */
class HelloPlus_Whatsapp_Redirect extends Action_Base {
	use Whatsapp_Redirect_Action_Trait;

	public function __construct() {
		$this->apply_whatsapp_config(
			array(
				'submit_actions_key' => 'cool_formkit_submit_actions',
				'guard_duplicate'    => true,
				'extra_conditions'   => array(
					'cool_formkit_submit_actions' => $this->get_name(),
				),
			)
		);
	}
}
