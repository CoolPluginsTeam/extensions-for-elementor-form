<?php
namespace Cool_FormKit\Modules\Forms\Fields;

use Elementor\Widget_Base;
use Cool_FormKit\Modules\Forms\Classes;
use Elementor\Controls_Manager;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;


if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Number extends Field_Base {

	public function get_type() {
		return 'number';
	}

	public function get_name() {
		return esc_html__( 'Number', 'cool-formkit' );
	}

	public function render( $item, $item_index, $form ) {

		$settings = $form->get_settings();

		$form->add_render_attribute( 'input' . $item_index, 'class', 'mdc-text-field__input' );

		if ( isset( $item['num_field_min'] ) ) {
			$form->add_render_attribute( 'input' . $item_index, 'min', esc_attr( $item['num_field_min'] ) );
		}
		if ( isset( $item['num_field_max'] ) ) {
			$form->add_render_attribute( 'input' . $item_index, 'max', esc_attr( $item['num_field_max'] ) );
		}

		?>
			<!-- <input <?php // $form->print_render_attribute_string( 'input' . $item_index ); ?> > -->

		<?php		
		?>
		<label class="cool-form-text mdc-text-field mdc-text-field--outlined <?php echo ($item['field_label'] === '' || empty($settings['show_labels'])) ? 'mdc-text-field--no-label' : '' ?> cool-field-size-<?php echo $settings['input_size'] ?>">
			<span class="mdc-notched-outline">
				<span class="mdc-notched-outline__leading"></span>
				<span class="mdc-notched-outline__notch">
					<?php if($item['field_label'] !== '' && !empty($settings['show_labels'])){?>
						<span class="mdc-floating-label" id="number-label-<?php echo esc_attr( $item_index ); ?>">
							<?php echo esc_html( $item['field_label'] ); ?>
						</span>
					<?php
					}
					?>
				</span>
				<span class="mdc-notched-outline__trailing"></span>
			</span>
			<input type="number" <?php $form->print_render_attribute_string( 'input' . $item_index ); ?> data-index="<?php echo $item_index ?>" />
			<i aria-hidden="true" class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing cool-number-error-icon" style="display:none">error</i>
		</label>
		<div class="mdc-text-field-helper-line">
  			<div class="mdc-text-field-helper-text" id="cool-number-error" aria-hidden="true"></div>
		</div>
		<?php
	}

	/**
	 * @param Widget_Base $widget
	 */
	public function update_controls( $widget ) {
		$elementor = parent::elementor();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );


		if ( is_wp_error( $control_data ) ) {
			return;
		}


		$field_controls = [
			'num_field_min' => [
				'name' => 'num_field_min',
				'label' => esc_html__( 'Min. Value', 'cool-formkit' ),
				'type' => Controls_Manager::NUMBER,
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'num_field_max' => [
				'name' => 'num_field_max',
				'label' => esc_html__( 'Max. Value', 'cool-formkit' ),
				'type' => Controls_Manager::NUMBER,
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
		];

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );

	}

	public function validation( $field, Classes\Form_Record $record, Ajax_Handler $ajax_handler) {
		
		$search_id = $field['id'];

		$form_fields = $record->form_settings['form_fields']; 

		foreach ($form_fields as $field_data) {
			if (isset($field_data['custom_id']) && $field_data['custom_id'] === $search_id) {

				if ( ! empty( $field_data['num_field_max'] ) && $field_data['num_field_max'] < (int) $field['value'] ) {
					/* translators: %s: The value of max field. */
					// $ajax_handler->add_error( $field['id'], sprintf( esc_html__( 'The field value must be less than or equal to %s.', 'cool-formkit' ), $field_data['num_field_max'] ) );
				}
		
				if ( ! empty( $field_data['num_field_min'] ) && $field_data['num_field_min'] > (int) $field['value'] ) {
					/* translators: %s: The value of min field. */
					// $ajax_handler->add_error( $field['id'], sprintf( esc_html__( 'The field value must be greater than or equal to %s.', 'cool-formkit' ), $field_data['num_field_min'] ) );
				}
			}
		}


		// if ( ! empty( $field['num_field_max'] ) && $field['num_field_max'] < (int) $field['value'] ) {
		// 	/* translators: %s: The value of max field. */
		// 	$ajax_handler->add_error( $field['id'], sprintf( esc_html__( 'The field value must be less than or equal to %s.', 'cool-formkit' ), $field['num_field_max'] ) );
		// }

		// if ( ! empty( $field['num_field_min'] ) && $field['num_field_min'] > (int) $field['value'] ) {
		// 	/* translators: %s: The value of min field. */
		// 	$ajax_handler->add_error( $field['id'], sprintf( esc_html__( 'The field value must be greater than or equal to %s.', 'cool-formkit' ), $field['num_field_min'] ) );
		// }
	}

	public function sanitize_field( $value, $field ) {
		return intval( $value );
	}
}
