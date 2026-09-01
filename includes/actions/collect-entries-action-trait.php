<?php
/**
 * Shared Collect Entries settings UI for Cool Form and Hello Plus.
 *
 * @package Cool_FormKit
 */

namespace Cool_FormKit\Includes\Actions;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

trait Collect_Entries_Action_Trait {

	/**
	 * @return string
	 */
	public function get_label(): string {
		return esc_html__( 'Collect Entries', 'extensions-for-elementor-form' );
	}

	/**
	 * Form settings key for submit actions.
	 *
	 * @return string
	 */
	protected function get_collect_entries_submit_actions_key(): string {
		return 'submit_actions';
	}

	/**
	 * @param \Elementor\Widget_Base $widget Widget instance.
	 * @return void
	 */
	public function register_settings_section( $widget ) {
		$widget->start_controls_section(
			'section_collect_entries',
			array(
				'label'     => $this->get_label(),
				'tab'       => \Elementor\Controls_Manager::TAB_CONTENT,
				'condition' => array(
					$this->get_collect_entries_submit_actions_key() => $this->get_name(),
				),
			)
		);

		$widget->add_control(
			'collect_entries_field_message',
			array(
				'type'       => \Elementor\Controls_Manager::ALERT,
				'alert_type' => 'info',
				'content'    => sprintf(
					/* translators: %s: Learn More link */
					esc_html__( 'This action will collect the entries and store it in a variable. You can use this variable in the next action or in the same form. %s', 'extensions-for-elementor-form' ),
					sprintf( '<a href="%s" target="_blank">%s</a>', admin_url( 'admin.php?page=cool-formkit' ), esc_html__( 'Learn More', 'extensions-for-elementor-form' ) )
				),
			)
		);

		$widget->add_control(
			'collect_entries_meta_data',
			array(
				'label'       => esc_html__( 'Collect Entries Meta Data', 'extensions-for-elementor-form' ),
				'type'        => \Elementor\Controls_Manager::SELECT2,
				'options'     => array(
					'remote_ip'  => esc_html__( 'User IP', 'extensions-for-elementor-form' ),
					'user_agent' => esc_html__( 'User Agent', 'extensions-for-elementor-form' ),
				),
				'render_type' => 'none',
				'multiple'    => true,
				'label_block' => true,
				'default'     => array(
					'remote_ip',
					'user_agent',
				),
			)
		);

		$widget->end_controls_section();
	}

	/**
	 * @param array $element Element data.
	 * @return array
	 */
	public function on_export( $element ) {
		unset( $element['settings']['collect_entries_meta_data'] );
		return $element;
	}
}
