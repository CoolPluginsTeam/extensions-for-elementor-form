<?php
namespace Cool_FormKit\Modules\Forms\Classes;

use Elementor\Utils;
use Elementor\Widget_Base;
use Elementor\Icons_Manager;

use Cool_FormKit\Modules\Forms\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Form_Base extends Widget_Base {

	public function on_export( $element ) {
		/** @var \Cool_FormKit\Modules\Forms\Classes\Action_Base[] $actions */
		$actions = Module::instance()->actions_registrar->get();

		foreach ( $actions as $action ) {
			$new_element_data = $action->on_export( $element );
			if ( null !== $new_element_data ) {
				$element = $new_element_data;
			}
		}

		return $element;
	}

	public static function get_button_sizes(): array {
		return [
			'xs' => esc_html__( 'Extra Small', 'cool-formkit' ),
			'sm' => esc_html__( 'Small', 'cool-formkit' ),
			'md' => esc_html__( 'Medium', 'cool-formkit' ),
			'lg' => esc_html__( 'Large', 'cool-formkit' ),
			'xl' => esc_html__( 'Extra Large', 'cool-formkit' ),
		];
	}

	public function make_textarea_field_md( $item, $item_index, $instance ): string {
		ob_start();
		?>
		<label class="cool-form-text mdc-text-field mdc-text-field--outlined mdc-text-field--textarea <?php echo ($item['field_label'] === '' || empty($instance['show_labels'])) ? 'mdc-text-field--no-label' : '' ?>">
			<span class="mdc-notched-outline">
				<span class="mdc-notched-outline__leading"></span>
				<span class="mdc-notched-outline__notch">
					<?php if($item['field_label'] !== '' && !empty($instance['show_labels'])){?>
						<span class="mdc-floating-label" id="textarea-label-<?php echo esc_attr( $item_index ); ?>">
							<?php echo esc_html( $item['field_label'] ); ?>
						</span>
					<?php
					}
					?>
				</span>
				<span class="mdc-notched-outline__trailing"></span>
			</span>
			<span class="mdc-text-field__resizer">
				<textarea
					class="mdc-text-field__input cool-form__field"
					id="<?php echo $this->get_attribute_id( $item ); ?>"
					name="<?php echo $this->get_attribute_name( $item ); ?>"
					rows="<?php echo esc_attr( $item['rows'] ); ?>"
					<?php echo ( ! empty( $item['placeholder'] ) ) ? 'placeholder="' . esc_attr( $item['placeholder'] ) . '"' : ''; ?>
					<?php echo ( ! empty( $item['required'] ) ) ? 'required' : ''; ?>
				><?php echo esc_textarea( $item['field_value'] ?? '' ); ?></textarea>
			</span>
		</label>
		<div class="mdc-text-field-helper-line">
  			<div class="mdc-text-field-helper-text" id="cool-textarea-error" aria-hidden="true"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function make_text_field_md( $item, $item_index, $instance ): string {
		ob_start();		
		?>
		<label class="cool-form-text mdc-text-field mdc-text-field--outlined <?php echo ($item['field_label'] === '' || empty($instance['show_labels'])) ? 'mdc-text-field--no-label' : '' ?> cool-field-size-<?php echo $instance['input_size'] ?>">
			<span class="mdc-notched-outline">
				<span class="mdc-notched-outline__leading"></span>
				<span class="mdc-notched-outline__notch">
					<?php if($item['field_label'] !== '' && !empty($instance['show_labels'])){?>
						<span class="mdc-floating-label" id="text-label-<?php echo esc_attr( $item_index ); ?>">
							<?php echo esc_html( $item['field_label'] ); ?>
						</span>
					<?php
					}
					?>
				</span>
				<span class="mdc-notched-outline__trailing"></span>
			</span>
			<input 
				type="<?php echo esc_attr( $item['field_type'] ); ?>"
				class="mdc-text-field__input cool-form__field cool-field-size-<?php echo $instance['input_size'] ?>"
				id="<?php echo $this->get_attribute_id( $item ); ?>"
				name="<?php echo $this->get_attribute_name( $item ); ?>"
				<?php echo ( ! empty( $item['placeholder'] ) ) ? 'placeholder="' . esc_attr( $item['placeholder'] ) . '"' : ''; ?>
				<?php echo ( ! empty( $item['field_value'] ) ) ? 'value="' . esc_attr( $item['field_value'] ) . '"' : ''; ?>
				<?php echo ( ! empty( $item['required'] ) ) ? 'required' : ''; ?>
			>
			<i aria-hidden="true" class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing cool-<?php echo esc_attr( $item['field_type'] ); ?>-error-icon" style="display:none">error</i>
		</label>
		<div class="mdc-text-field-helper-line">
  			<div class="mdc-text-field-helper-text" id="cool-<?php echo esc_attr( $item['field_type'] ); ?>-error" aria-hidden="true"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function make_select_field_md( $item, $i ,$instance): string {
		ob_start();		
		?>
		<div class="mdc-select mdc-select--outlined cool-field-size-<?php echo $instance['input_size'] ?>">
			<div class="mdc-select__anchor cool-field-size-<?php echo $instance['input_size'] ?>" aria-labelledby="select-label-<?php echo esc_attr( $i ); ?>">
				<span class="mdc-notched-outline">
					<span class="mdc-notched-outline__leading"></span>
					<span class="mdc-notched-outline__notch">
						<?php if($item['field_label'] !== '' && !empty($instance['show_labels'])){?>
							<span class="mdc-floating-label" id="select-label-<?php echo esc_attr( $i ); ?>">
								<?php echo esc_html( $item['field_label'] ); ?>
							</span>
						<?php
						}
						?>
					</span>
					<span class="mdc-notched-outline__trailing"></span>
				</span>
				<span class="mdc-select__selected-text-container">
					<span class="mdc-select__selected-text"></span>
				</span>
				<span class="mdc-select__dropdown-icon">
					<!-- You can insert your SVG icon or use MDC icon classes here -->
					<svg class="mdc-select__dropdown-icon-graphic" viewBox="7 10 10 5" focusable="false">
						<polygon class="mdc-select__dropdown-icon-inactive" stroke="none" fill-rule="evenodd" points="7 10 12 15 17 10"></polygon>
						<polygon class="mdc-select__dropdown-icon-active" stroke="none" fill-rule="evenodd" points="7 15 12 10 17 15"></polygon>
					</svg>
				</span>
			</div>
			<div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-menu-surface--fullwidth">
				<ul class="mdc-list" role="listbox" aria-label="<?php echo esc_attr( $item['field_label'] ); ?>">
					<?php
					$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );
					if ( $options ) {
						foreach ( $options as $key => $option ) {
							$option_value = $option;
							$option_label = $option;
							if ( false !== strpos( $option, '|' ) ) {
								list( $option_label, $option_value ) = explode( '|', $option );
							}
							$selected = '';
							if ( ! empty( $item['field_value'] ) && in_array( $option_value, explode( ',', $item['field_value'] ), true ) ) {
								$selected = 'aria-selected="true" class="mdc-list-item--selected"';
							}
							?>
							<li class="mdc-list-item" role="option" data-value="<?php echo esc_attr( $option_value ); ?>" <?php echo $selected; ?>>
								<span class="mdc-list-item__ripple"></span>
								<span class="mdc-list-item__text"><?php echo esc_html( $option_label ); ?></span>
							</li>
						<?php }
					} ?>
				</ul>
			</div>
			<select name="form_fields[<?php echo $item['custom_id'] ?>]" id="form-field-<?php echo $item['custom_id'] ?>" style="display: none;">
				<option value="">Select an option</option>
				<option value="option1">Option 1</option>
				<option value="option2">Option 2</option>
			</select>
		</div>
		<div class="mdc-select-helper-line">
  			<div class="mdc-select-helper-text" id="cool-select-error" aria-hidden="true" ></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function make_radio_checkbox_field_md( $item, $item_index, $type ): string {
		ob_start();
		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );
		?>
		<div class="mdc-form-field <?php echo esc_attr( $item['css_classes'] ); ?> <?php echo esc_attr($item['inline_list'] === 'elementor-subgroup-inline') ? 'inline-items':'ontop-items'?>">
			<?php
			if ( $options ) {
				foreach ( $options as $key => $option ) {
					$option_value = $option;
					$option_label = $option;
					if ( false !== strpos( $option, '|' ) ) {
						list( $option_label, $option_value ) = explode( '|', $option );
					}
					$input_id = $this->get_attribute_id( $item ) . '-' . $key;
					?>
					<span class="field-sub-options">
						<div class="<?php echo ( 'radio' === $type ? 'mdc-radio' : 'mdc-checkbox' ); ?>">
							<input
								class="<?php echo ( 'radio' === $type ? 'mdc-radio__native-control' : 'mdc-checkbox__native-control' ); ?>"
								type="<?php echo esc_attr( $type ); ?>"
								id="<?php echo esc_attr( $input_id ); ?>"
								name="<?php echo $this->get_attribute_name( $item ) . ( ( 'checkbox' === $type && count( $options ) > 1 ) ? '[]' : '' ); ?>"
								value="<?php echo esc_attr( $option_value ); ?>"
								<?php echo ( ! empty( $item['field_value'] ) && $option_value === $item['field_value'] ) ? 'checked' : ''; ?>
								<?php echo ( ! empty( $item['required'] ) && 'radio' === $type ) ? 'required' : ''; ?>
							>
							<?php if ( 'checkbox' === $type ) : ?>
								<div class="mdc-checkbox__background">
									<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">
										<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>
									</svg>
									<div class="mdc-checkbox__mixedmark"></div>
								</div>
							<?php else : ?>
								<div class="mdc-radio__background">
									<div class="mdc-radio__outer-circle"></div>
									<div class="mdc-radio__inner-circle"></div>
								</div>
							<?php endif; ?>
						</div>
						<label for="<?php echo esc_attr( $input_id ); ?>"><?php echo esc_html( $option_label ); ?></label>
					</span>
					<?php
				}
			}
			?>
		</div>
		<?php
		return ob_get_clean();
	}
	
	public function make_textarea_field( $item, $item_index, $instance ): string {
		$this->add_render_attribute( 'textarea' . $item_index, [
			'class' => [
				'elementor-field-textual',
				'cool-form__field',
				'cool-form__textarea',
				esc_attr( $item['css_classes'] ),
			],
			'name' => $this->get_attribute_name( $item ),
			'id' => $this->get_attribute_id( $item ),
			'rows' => $item['rows'],
		] );

		if ( $item['placeholder'] ) {
			$this->add_render_attribute( 'textarea' . $item_index, 'placeholder', $item['placeholder'] );
		}

		if ( $item['required'] ) {
			$this->add_required_attribute( 'textarea' . $item_index );
		}

		$value = empty( $item['field_value'] ) ? '' : $item['field_value'];

		return '<textarea ' . $this->get_render_attribute_string( 'textarea' . $item_index ) . '>' . $value . '</textarea>';
	}

	public function make_select_field( $item, $i ,$instance) {
		$this->add_render_attribute(
			[
				'select-wrapper' . $i => [
					'class' => [
						'cool-form__field',
						'cool-form__select',
						'remove-before',
						esc_attr( $item['css_classes'] ),
					],
				],
				'select' . $i => [
					'name' => $this->get_attribute_name( $item ) . ( ! empty( $item['allow_multiple'] ) ? '[]' : '' ),
					'id' => $this->get_attribute_id( $item ),
					'class' => [
						'cool-form-field-textual',
						'cool-form__field',
					],
				],
			]
		);

		if ( $item['required'] ) {
			$this->add_required_attribute( 'select' . $i );
		}

		if ( $item['allow_multiple'] ) {
			$this->add_render_attribute( 'select' . $i, 'multiple' );
			if ( ! empty( $item['select_size'] ) ) {
				$this->add_render_attribute( 'select' . $i, 'size', $item['select_size'] );
			}
		}

		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );

		if ( ! $options ) {
			return '';
		}

		ob_start();
		?>
		<div <?php $this->print_render_attribute_string( 'select-wrapper' . $i ); ?>>
			<div class="select-caret-down-wrapper">
				<?php
				if ( ! $item['allow_multiple'] ) {
					$icon = [
						'library' => 'eicons',
						'value' => 'eicon-caret-down',
						'position' => 'right',
					];
					Icons_Manager::render_icon( $icon, [ 'aria-hidden' => 'true' ] );
				}
				?>
			</div>
			<select <?php $this->print_render_attribute_string( 'select' . $i ); ?>>
				<?php
				foreach ( $options as $key => $option ) {
					$option_id = esc_attr( $item['custom_id'] . $key );
					$option_value = esc_attr( $option );
					$option_label = $option;

					if ( false !== strpos( $option, '|' ) ) {
						list( $label, $value ) = explode( '|', $option );
						$option_value = esc_attr( $value );
						$option_label = $label;
					}

					$this->add_render_attribute( $option_id, 'value', $option_value );

					// Support multiple selected values
					if ( ! empty( $item['field_value'] ) && in_array( $option_value, explode( ',', $item['field_value'] ), true ) ) {
						$this->add_render_attribute( $option_id, 'selected', 'selected' );
					} ?>
					<option <?php $this->print_render_attribute_string( $option_id ); ?>><?php
						// PHPCS - $option_label is already escaped
						echo esc_html( $option_label ); ?></option>
				<?php } ?>
			</select>
		</div>
		<?php

		$select = ob_get_clean();
		return $select;
	}

	public function make_radio_checkbox_field( $item, $item_index, $type ): string {
		$options = preg_split( "/\\r\\n|\\r|\\n/", $item['field_options'] );
		$html = '';
		if ( $options ) {
			$html .= '<div class="elementor-field-subgroup ' . esc_attr( $item['css_classes'] ) . ' ' . esc_attr( $item['inline_list'] ) . '">';
			foreach ( $options as $key => $option ) {
				$element_id = esc_attr( $item['custom_id'] ) . $key;
				$html_id = $this->get_attribute_id( $item ) . '-' . $key;
				$option_label = $option;
				$option_value = $option;
				if ( false !== strpos( $option, '|' ) ) {
					list( $option_label, $option_value ) = explode( '|', $option );
				}

				$this->add_render_attribute(
					$element_id,
					[
						'type' => $type,
						'value' => $option_value,
						'id' => $html_id,
						'name' => $this->get_attribute_name( $item ) . ( ( 'checkbox' === $type && count( $options ) > 1 ) ? '[]' : '' ),
					]
				);

				if ( ! empty( $item['field_value'] ) && $option_value === $item['field_value'] ) {
					$this->add_render_attribute( $element_id, 'checked', 'checked' );
				}

				if ( $item['required'] && 'radio' === $type ) {
					$this->add_required_attribute( $element_id );
				}

				$html .= '<span class="elementor-field-option"><input ' . $this->get_render_attribute_string( $element_id ) . '> <label for="' . $html_id . '">' . $option_label . '</label></span>';
			}
			$html .= '</div>';
		}

		return $html;
	}

	public function form_fields_render_attributes( $i, $instance, $item ) {
		$this->add_render_attribute(
			[
				'field-group' . $i => [
					'class' => [
						'cool-form__field-group',
						'is-field-type-' . $item['field_type'],
						'is-field-group-' . $item['custom_id'],
					],
				],
				'input' . $i => [
					'type' => $item['field_type'],
					'name' => $this->get_attribute_name( $item ),
					'id' => $this->get_attribute_id( $item ),
					'class' => [
						'cool-form__field',
						'cool-form-field-type-' . $item['field_type'],
						empty( $item['css_classes'] ) ? '' : esc_attr( $item['css_classes'] ),
					],
				],
				'label' . $i => [
					'for' => $this->get_attribute_id( $item ),
					'class' => 'cool-form__field-label',
				],
			]
		);

		if ( empty( $item['width'] ) ) {
			$item['width'] = '100';
		}

		$this->add_render_attribute( 'field-group' . $i, 'class', 'has-width-' . $item['width'] );

		// if ( $item['allow_multiple'] ) {
		// 	$this->add_render_attribute( 'field-group' . $i, 'class', 'elementor-field-type-' . $item['field_type'] . '-multiple' );
		// }

		if ( ! empty( $item['width_tablet'] ) ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'has-width-md-' . $item['width_tablet'] );
		}

		if ( ! empty( $item['width_mobile'] ) ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'has-width-sm-' . $item['width_mobile'] );
		}

		// Allow zero as placeholder.
		if ( ! Utils::is_empty( $item['placeholder'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'placeholder', $item['placeholder'] );
		}

		if ( ! empty( $item['field_value'] ) ) {
			$this->add_render_attribute( 'input' . $i, 'value', $item['field_value'] );
		}

		if ( ! $instance['show_labels'] ) {
			$this->add_render_attribute( 'label' . $i, 'class', 'elementor-screen-only' );
		}

		if ( ! empty( $item['required'] ) ) {
			$class = 'is-field-required';
			if ( ! empty( $instance['mark_required'] ) ) {
				$class .= ' is-mark-required';
			}
			$this->add_render_attribute( 'field-group' . $i, 'class', $class );
			$this->add_required_attribute( 'input' . $i );
		}

		if ( 'yes' === $instance['field_border_switcher'] ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'has-border' );
		}

		if ( ! empty( $instance['fields_shape'] ) ) {
			$this->add_render_attribute( 'field-group' . $i, 'class', 'has-shape-' . $instance['fields_shape'] );
		}
	}

	public function render_plain_content() {}

	public function get_attribute_name( $item ): string {
		return "form_fields[{$item['custom_id']}]";
	}

	public function get_attribute_id( $item ): string {
		return 'form-field-' . esc_attr( $item['custom_id'] );
	}

	private function add_required_attribute( $element ) {
		$this->add_render_attribute( $element, 'required', 'required' );
		$this->add_render_attribute( $element, 'aria-required', 'true' );
	}

	public function get_categories(): array {
		return [ 'general' ];
	}
}
