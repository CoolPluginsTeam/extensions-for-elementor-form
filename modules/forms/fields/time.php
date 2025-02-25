<?php
namespace Cool_FormKit\Modules\Forms\Fields;

use Cool_FormKit\Modules\Forms\Classes;
use Elementor\Controls_Manager;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Time extends Field_Base {

	public $depended_scripts = [
		'flatpickr',
		'handle-time-pickr',
	];

	public $depended_styles = [
		'flatpickr',
		'handle-time-pickr',
	];

	public function get_type() {
		return 'time';
	}

	public function get_name() {
		return esc_html__( 'Time', 'elementor-pro' );
	}


	public function update_controls( $widget ) {
		$elementor = parent::elementor();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = [
			'use_native_time' => [
				'name' => 'use_native_time',
				'label' => esc_html__( 'Native HTML5', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
		];

		foreach ( $control_data['fields'] as $index => $field ) {
			if ( 'placeholder' !== $field['name'] ) {
				continue;
			}
			foreach ( $field['conditions']['terms'] as $condition_index => $terms ) {
				if ( ! isset( $terms['name'] ) || 'field_type' !== $terms['name'] || ! isset( $terms['operator'] ) || 'in' !== $terms['operator'] ) {
					continue;
				}
				$control_data['fields'][ $index ]['conditions']['terms'][ $condition_index ]['value'][] = $this->get_type();
				break;
			}
			break;
		}

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}

	public function render( $item, $item_index, $form ) {
		// Use the Material Design input class and add any additional classes.
		$form->add_render_attribute( 'input' . $item_index, 'class', 'mdc-text-field__input cool-form-time-field' );
		if ( isset( $item['use_native_time'] ) && 'yes' === $item['use_native_time'] ) {
			$form->add_render_attribute( 'input' . $item_index, 'class', 'cool-form-use-native' );
		}
		?>
		<label class="cool-form-text mdc-text-field mdc-text-field--outlined">
			<span class="mdc-notched-outline">
				<span class="mdc-notched-outline__leading"></span>
				<span class="mdc-notched-outline__notch">
					<?php if($item['field_label'] !== ''){?>
						<span class="mdc-floating-label" id="time-label-<?php echo esc_attr( $item_index ); ?>">
							<?php echo esc_html( $item['field_label'] ); ?>
						</span>
					<?php
					}
					?>
				</span>
				<span class="mdc-notched-outline__trailing"></span>
			</span>
			<input type="time" <?php $form->print_render_attribute_string( 'input' . $item_index ); ?> >
		</label>
		<?php
	}	

	public function validation( $field, Classes\Form_Record $record, Ajax_Handler $ajax_handler) {
		if ( empty( $field['value'] ) ) {
			return;
		}

		if ( preg_match( '/^(([0-1][0-9])|(2[0-3])):[0-5][0-9]$/', $field['value'] ) !== 1 ) {
			$ajax_handler->add_error( $field['id'], esc_html__( 'The field should be in HH:MM format.', 'elementor-pro' ) );
		}
	}
}
