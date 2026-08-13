<?php
namespace Cool_FormKit\Includes\Frontend;

use Cool_FormKit\Includes\Actions\Whatsapp_Redirect;
use Cool_FormKit\Includes\Actions\Register_Post;

use Cool_FormKit\Includes\Frontend\Widget\Custom_Success_Message;
use Cool_FormKit\Includes\Frontend\Widget\CFL_Create_Conditional_Fields;
use Cool_FormKit\Includes\Frontend\Widget\CFL_COUNTRY_CODE_FIELD;
use Cool_FormKit\Includes\Frontend\Widget\FME_Plugin;

/**
 * The public-facing functionality of the plugin.
 *
 * @package    Cool_FormKit
 * @subpackage Cool_FormKit/frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CFKEF_Frontend' ) ) {
	class CFKEF_Frontend {

		public function __construct() {
			add_action( 'elementor_pro/forms/actions/register', array( $this, 'cfef_register_new_form_actions' ) );
			$this->include_addons();
		}

		public function include_addons() {
			include_once CFL_PLUGIN_PATH . 'includes/frontend/widget/class-custom-success-message.php';
			$custom_success_message = new Custom_Success_Message();
			$custom_success_message->set_hooks();

			if ( \CFL_Elements::is_enabled( 'conditional_logic' ) ) {
				require_once CFL_PLUGIN_PATH . 'includes/frontend/widget/create-conditional-fields.php';
				new CFL_Create_Conditional_Fields();
			}
			if ( \CFL_Elements::is_enabled( 'country_code' ) ) {
				require_once CFL_PLUGIN_PATH . 'includes/frontend/widget/country-code-addon.php';
				new CFL_COUNTRY_CODE_FIELD();
			}
			if ( \CFL_Elements::is_enabled( 'form_input_mask' ) ) {
				require_once CFL_PLUGIN_PATH . 'includes/frontend/widget/class-fme-plugin.php';
				FME_Plugin::instance();
			}
		}

		public function cfef_register_new_form_actions( $form_actions_registrar ) {
			if ( \CFL_Elements::is_enabled( 'whatsapp_redirect' ) ) {
				require_once CFL_PLUGIN_PATH . 'includes/actions/class-whatsapp-redirect.php';
				$form_actions_registrar->register( new Whatsapp_Redirect() );
			}
			require_once CFL_PLUGIN_PATH . 'includes/actions/class-register-post.php';
			$form_actions_registrar->register( new Register_Post() );
		}
	}
}
