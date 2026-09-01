<?php

namespace Cool_FormKit\Widgets;

use Cool_FormKit\Widgets\Addons\CoolForm_COUNTRY_CODE_FIELD;
use Cool_FormKit\Widgets\Addons\CoolForm_Create_Conditional_Fields;
use Cool_FormKit\Widgets\Addons\CoolForm_FME_Plugin;
use Cool_FormKit\Widgets\Addons\CoolForm_Whatsapp_Redirect;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'CoolForm_Addons_Loader' ) ) {
	class CoolForm_Addons_Loader extends Base_Addons_Loader {

		protected function init(): void {
			$this->load_addons();
			add_action( 'cool_form/forms/actions/register', array( $this, 'register_new_form_actions' ) );
		}

		public function register_new_form_actions( $actions_registrar ) {
			if ( \CFL_Elements::is_enabled( 'whatsapp_redirect' ) ) {
				require_once CFL_PLUGIN_PATH . 'widgets/addons/coolform-whatsapp-redirect.php';
				$actions_registrar->register( new CoolForm_Whatsapp_Redirect() );
			}
		}

		public function load_addons() {
			if ( \CFL_Elements::is_enabled( 'country_code' ) ) {
				require_once CFL_PLUGIN_PATH . 'widgets/addons/coolform-country-code-addon.php';
				CoolForm_COUNTRY_CODE_FIELD::get_instance();
			}
			if ( \CFL_Elements::is_enabled( 'conditional_logic' ) ) {
				require_once CFL_PLUGIN_PATH . 'widgets/addons/coolform-create-conditional-fields.php';
				new CoolForm_Create_Conditional_Fields();
			}
			if ( \CFL_Elements::is_enabled( 'form_input_mask' ) ) {
				require_once CFL_PLUGIN_PATH . 'widgets/addons/coolform-fme-plugin.php';
				CoolForm_FME_Plugin::instance();
			}
		}
	}
}
