<?php

namespace Cool_FormKit\Widgets;

use Cool_FormKit\Widgets\HelloPlusAddons\Action\HelloPlus_Collect_Entries;
use Cool_FormKit\Widgets\HelloPlusAddons\HelloPlus_Whatsapp_Redirect;
use Cool_FormKit\Widgets\HelloPlusAddons\Action\Save_Form_Data;
use Cool_FormKit\Widgets\HelloPlusAddons\HelloPlus_Create_Conditional_Fields;
use Cool_FormKit\Widgets\HelloPlusAddons\HelloPlus_COUNTRY_CODE_FIELD;
use Cool_FormKit\Widgets\HelloPlusAddons\HelloPlus_FME_Plugin;
use Cool_FormKit\Widgets\HelloPlusAddons\Sheet_HelloPlus_Action;

// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedConstantFound

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'HelloPlus_Addons_Loader' ) ) {
	class HelloPlus_Addons_Loader extends Base_Addons_Loader {

		protected function init(): void {
			if ( is_plugin_active( 'hello-plus/hello-plus.php' ) ) {
				add_action(
					'plugins_loaded',
					function () {
						$this->load_addons();
						$this->load_entries();
						$this->register_action();
					}
				);
				add_action( 'elementor/element/ehp-form/section_integration/after_section_end', array( $this, 'show_actions_on_editor_side' ), 10, 2 );
			}
		}

		public function load_addons() {
			if ( \CFL_Elements::is_enabled( 'conditional_logic' ) ) {
				require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-conditional-fields.php';
				new HelloPlus_Create_Conditional_Fields();
			}

			if ( \CFL_Elements::is_enabled( 'country_code' ) ) {
				require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-country-code-addon.php';
				new HelloPlus_COUNTRY_CODE_FIELD();
			}
			if ( \CFL_Elements::is_enabled( 'form_input_mask' ) ) {
				require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-fme-plugin.php';
				HelloPlus_FME_Plugin::instance();
			}
		}

		public function load_entries() {
			require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/action/collect-entries.php';
			require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/action/save-form-data.php';

			new Save_Form_Data();

			if ( class_exists( 'HelloPlus\Modules\Forms\Module' ) ) {
				$forms_module = \HelloPlus\Modules\Forms\Module::instance();

				if ( $forms_module && isset( $forms_module->actions_registrar ) ) {
					$forms_module->actions_registrar->register( new HelloPlus_Collect_Entries() );
				}
			}
		}

		public function register_action() {
			if ( \CFL_Elements::is_enabled( 'whatsapp_redirect' ) ) {
				if ( class_exists( 'HelloPlus\Modules\Forms\Module' ) ) {
					$forms_module = \HelloPlus\Modules\Forms\Module::instance();
					if ( $forms_module && isset( $forms_module->actions_registrar ) ) {
						require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-whatsapp-redirect.php';
						$forms_module->actions_registrar->register( new HelloPlus_Whatsapp_Redirect() );
					}
				}
			}
		}

		public function show_actions_on_editor_side( $element, $args ) {
			$custom_actions   = array();
			$action_instances = array();

			if ( ! is_plugin_active( 'sb-elementor-contact-form-db/sb_elementor_contact_form_db.php' ) && ! defined( 'formdb_hello_plus_marketing_editor' ) ) {
				define( 'formdb_hello_plus_marketing_editor', true );
				require_once CFL_PLUGIN_PATH . 'includes/helloplus-form-to-sheet.php';

				$instance                                 = new Sheet_HelloPlus_Action();
				$custom_actions[ $instance->get_name() ]  = $instance->get_label();
				$action_instances[]                       = $instance;
			}

			if ( \CFL_Elements::is_enabled( 'whatsapp_redirect' ) ) {
				require_once CFL_PLUGIN_PATH . 'widgets/helloplus-addons/helloplus-whatsapp-redirect.php';
				$instance                                = new HelloPlus_Whatsapp_Redirect();
				$custom_actions[ $instance->get_name() ] = $instance->get_label();
				$action_instances[]                      = $instance;
			}

			if ( empty( $custom_actions ) ) {
				return;
			}

			$element->start_controls_section(
				'cool_formkit_conditional_actions_section',
				array(
					'label' => esc_html__( 'Cool Actions After Submit', 'extensions-for-elementor-form' ),
				)
			);

			$element->add_control(
				'cool_formkit_submit_actions',
				array(
					'label'       => __( 'Actions After Submit', 'extensions-for-elementor-form' ),
					'type'        => \Elementor\Controls_Manager::SELECT2,
					'multiple'    => true,
					'label_block' => true,
					'options'     => $custom_actions,
					'default'     => array(),
					'render_type' => 'template',
				)
			);

			$element->end_controls_section();

			foreach ( $action_instances as $instance ) {
				if ( method_exists( $instance, 'register_settings_section' ) ) {
					$instance->register_settings_section( $element );
				}
			}
		}
	}
}
