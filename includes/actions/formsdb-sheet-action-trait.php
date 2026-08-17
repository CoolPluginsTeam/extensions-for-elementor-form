<?php
/**
 * Shared FormsDB Google Sheet marketing stub for Elementor Pro / Cool Form / Hello Plus.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait FormsDB_Sheet_Action_Trait {

	/**
	 * @return string
	 */
	public function get_name(): string {
		return 'Save Submissions in Google Sheet';
	}

	/**
	 * @return string
	 */
	public function get_label(): string {
		return esc_html__( 'Save Submissions in Google Sheet', 'extensions-for-elementor-form' );
	}

	/**
	 * @param string $id Control id fragment.
	 * @return string
	 */
	protected function add_prefix( $id ) {
		return 'fdbgp_' . $id;
	}

	/**
	 * Form settings key for submit actions.
	 *
	 * @return string
	 */
	protected function get_sheet_submit_actions_key() {
		return 'submit_actions';
	}

	/**
	 * Optional Elementor controls tab for the settings section.
	 *
	 * @return string|null
	 */
	protected function get_sheet_settings_section_tab() {
		return null;
	}

	/**
	 * @param \Elementor\Widget_Base $widget Widget instance.
	 * @return void
	 */
	public function register_settings_section( $widget ) {
		$section = array(
			'label'     => esc_html__( 'Save Submissions in Google Sheet', 'extensions-for-elementor-form' ),
			'condition' => array(
				$this->get_sheet_submit_actions_key() => $this->get_name(),
			),
		);

		$tab = $this->get_sheet_settings_section_tab();
		if ( $tab ) {
			$section['tab'] = $tab;
		}

		$widget->start_controls_section(
			$this->add_prefix( 'section_google_sheets' ),
			$section
		);

		$widget->add_control(
			$this->add_prefix( 'fdbgp_plugin_marketing' ),
			array(
				'name'  => 'fdbgp_plugin_marketing',
				'label' => '',
				'type'  => \Elementor\Controls_Manager::RAW_HTML,
				'raw'   => cfl_formsdb_marketing_raw_html(),
			)
		);

		$widget->end_controls_section();
	}

	/**
	 * @param array $element Element data.
	 * @return array
	 */
	public function on_export( $element ) {
		return $element;
	}

	/**
	 * @param mixed $record Form record.
	 * @param mixed $ajax_handler Ajax handler.
	 * @return void
	 */
	public function run( $record, $ajax_handler ) {
	}
}
