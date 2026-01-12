<?php
namespace Cool_FormKit\Modules\Forms\Fields;

use Elementor\Controls_Manager;
use Cool_FormKit\Includes\Utils;
use Cool_FormKit\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Acceptance extends Field_Base {

	public function get_type() {
		return 'acceptance';
	}
	public function get_id() {
		return 'acceptance';
	}
	public function get_name() {
		return esc_html__( 'Acceptance', 'extensions-for-elementor-form' );
	}

	public function update_controls( $widget ) {
		$elementor = Utils::elementor();

		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = [
			'acceptance_text' => [
				'name' => 'acceptance_text',
				'label' => esc_html__( 'Acceptance Text', 'extensions-for-elementor-form' ),
				'type' => Controls_Manager::TEXTAREA,
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'checked_by_default' => [
				'name' => 'checked_by_default',
				'label' => esc_html__( 'Checked by Default', 'extensions-for-elementor-form' ),
				'type' => Controls_Manager::SWITCHER,
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

	public function render( $item, $item_index, $form ) {
		$label = '';
		// Use the MDC checkbox native control class.
		$form->add_render_attribute( 'input' . $item_index, 'class', 'mdc-checkbox__native-control' );
		$form->add_render_attribute( 'input' . $item_index, 'type', 'checkbox', true );
	
		// Build the label if acceptance text exists.
		if ( ! empty( $item['acceptance_text'] ) ) {
			$label = '<label for="' . $form->get_attribute_id( $item ) . '">' . $item['acceptance_text'] . '</label>';
		}
	
		if ( ! empty( $item['checked_by_default'] ) ) {
			$form->add_render_attribute( 'input' . $item_index, 'checked', 'checked' );
		}
		?>
		<div class="mdc-form-field">
			<div class="mdc-checkbox">
				<input <?php $form->print_render_attribute_string( 'input' . $item_index ); ?>>
				<div class="mdc-checkbox__background">
					<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
						<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
					</svg>
					<div class="mdc-checkbox__mixedmark"></div>
				</div>
			</div>
			<?php
			echo wp_kses( $label, [
				'label' => [
					'for'   => true,
					'class' => true,
					'id'    => true,
					'style' => true,
				],
			] );
			?>
		</div>
		<div class="mdc-text-field-helper-line">
  			<div class="cool-acceptance-error" id="cool-acceptance-error" aria-hidden="true"></div>
		</div>
		<?php
	}	
}
