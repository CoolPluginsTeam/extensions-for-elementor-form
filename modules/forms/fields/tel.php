<?php
namespace Cool_FormKit\Modules\Forms\Fields;

use Cool_FormKit\Modules\Forms\Classes;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Tel extends Field_Base {

	public function get_type() {
		return 'tel';
	}

	public function get_name() {
		return esc_html__( 'Tel', 'cool-formkit' );
	}

	public function render( $item, $item_index, $form ) {
		$settings = $form->get_settings();

		// Use the Material Design input class.
		$form->add_render_attribute( 'input' . $item_index, 'class', 'mdc-text-field__input' );
		$form->add_render_attribute( 'input' . $item_index, 'pattern', '[0-9()#&+\*\-\.=]+' );
		$form->add_render_attribute( 'input' . $item_index, 'title', esc_html__( 'Only numbers and phone characters (#, -, *, etc) are accepted.', 'cool-formkit' ) );
		?>
		<label class="cool-form-text mdc-text-field mdc-text-field--outlined">
			<span class="mdc-notched-outline">
				<span class="mdc-notched-outline__leading"></span>
				<span class="mdc-notched-outline__notch">
					<?php if($item['field_label'] !== '' && !empty($settings['show_labels'])){?>
						<span class="mdc-floating-label" id="tel-label-<?php echo esc_attr( $item_index ); ?>">
							<?php echo esc_html( $item['field_label'] ); ?>
						</span>
					<?php
					}
					?>
				</span>
				<span class="mdc-notched-outline__trailing"></span>
			</span>
			<input type="tel" size="1" <?php $form->print_render_attribute_string( 'input' . $item_index ); ?>>
			<i aria-hidden="true" class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing cool-tel-error-icon" style="display:none">error</i>
		</label>
		<div class="mdc-text-field-helper-line">
  			<div class="mdc-text-field-helper-text" id="cool-tel-error" aria-hidden="true"></div>
		</div>
		<?php
	}
	

	public function validation( $field, Classes\Form_Record $record, Ajax_Handler $ajax_handler ) {
		if ( empty( $field['value'] ) ) {
			return;
		}
		if ( preg_match( '/^[0-9()#&+*-=.]+$/', $field['value'] ) !== 1 ) {
			// $ajax_handler->add_error( $field['id'], esc_html__( 'The field accepts only numbers and phone characters (#, -, *, etc).', 'cool-formkit' ) );
		}
	}
}
