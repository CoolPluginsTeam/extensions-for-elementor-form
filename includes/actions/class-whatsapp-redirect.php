<?php
namespace Cool_FormKit\Includes\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once CFL_PLUGIN_PATH . 'includes/actions/whatsapp-redirect-action-trait.php';

/**
 * Elementor Pro WhatsApp redirect. Must extend Elementor Pro Action_Base for the registrar.
 */
class Whatsapp_Redirect extends \ElementorPro\Modules\Forms\Classes\Action_Base {
	use Whatsapp_Redirect_Action_Trait;

	public function __construct() {
		$this->apply_whatsapp_config(
			array(
				'submit_actions_key' => 'submit_actions',
				'guard_duplicate'    => false,
				'bail_on_empty'      => false,
			)
		);
	}
}
