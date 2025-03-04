<?php
namespace Cool_FormKit\Modules\Forms\Widgets;

use Elementor\Controls_Manager;
use Elementor\Core\Kits\Documents\Tabs\Global_Colors;
use Elementor\Core\Kits\Documents\Tabs\Global_Typography;
use Elementor\Group_Control_Background;
use Elementor\Group_Control_Typography;
use Elementor\Modules\DynamicTags\Module as TagsModule;
use Elementor\Repeater;
use Cool_FormKit\Includes\Utils;
use Cool_FormKit\Modules\Forms\Classes\Form_Base;
use Cool_FormKit\Modules\Forms\Classes\Render\Widget_Form_Render;
use Cool_FormKit\Modules\Forms\Components\Ajax_Handler;
use Cool_FormKit\Modules\Forms\Controls\Fields_Repeater;
use Cool_FormKit\Modules\Forms\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Cool_Form extends Form_Base {

	public function get_name() {
		return 'cool-form';
	}

	public function get_title() {
		return esc_html__( 'Cool Form Kit Form', 'cool-formkit' );
	}

	public function get_icon() {
		return 'cool-forms-icon';
	}

	public function get_keywords() {
		return [ 'form', 'forms', 'field', 'button' ];
	}

	protected function is_dynamic_content(): bool {
		return false;
	}

	protected function get_upsale_data(): array {
		return [];
	} 

	/**
	 * Get style dependencies.
	 *
	 * Retrieve the list of style dependencies the widget requires.
	 *
	 * @since 3.24.0
	 * @access public
	 *
	 * @return array Widget style dependencies.
	 */
	public function get_style_depends(): array {
		return [ 'Cool_FormKit-forms','cool-form-material-css','cool-form-material-helper-css' ];
	}

	public function get_script_depends(): array {
		return [ 'Cool_FormKit-forms-fe','cool-form-material-js','cool-form-material-handle-js' ];
	}

	protected function render(): void {
		$render_strategy = new Widget_Form_Render( $this );

		$render_strategy->render();
	}

	protected function content_template() {
		?>
		<#
		view.addRenderAttribute( 'form', 'class', 'cool-form' );


		if ( '' !== settings.form_id ) {
			view.addRenderAttribute( 'form', 'id', settings.form_id );
		}

		if ( '' !== settings.form_name ) {
			view.addRenderAttribute( 'form', 'name', settings.form_name );
		}

		if ( 'custom' === settings.form_validation ) {
			view.addRenderAttribute( 'form', 'novalidate' );
		}
		#>
		<form {{{ view.getRenderAttributeString( 'form' ) }}}>
			<div class="cool-form__wrapper">
				<#
					for ( var i in settings.form_fields ) {
						var item = settings.form_fields[ i ];
						item = elementor.hooks.applyFilters( 'cool_formkit/forms/content_template/item', item, i, settings );

						item.field_type  = _.escape( item.field_type );
						item.field_value = _.escape( item.field_value );

						var options = item.field_options ? item.field_options.split( '\n' ) : [],
							itemClasses = _.escape( item.css_classes ),
							labelVisibility = '',
							placeholder = '',
							required = '',
							inputField = '',
							multiple = '',
							fieldGroupClasses = 'cool-form__field-group has-border elementor-column is-field-type-' + item.field_type,
							printLabel = settings.show_labels && ! [ 'hidden', 'html', 'step' ].includes( item.field_type );

						fieldGroupClasses += ' has-width-' + ( ( '' !== item.width ) ? item.width : '100' );

						fieldGroupClasses += ' has-shape-' + settings.fields_shape

						if ( item.width_tablet ) {
							fieldGroupClasses += ' has-width-md-' + item.width_tablet;
						}

						if ( item.width_mobile ) {
							fieldGroupClasses += ' has-width-sm-' + item.width_mobile;
						}

						if ( item.required ) {
							required = 'required';
							fieldGroupClasses += ' is-field-required';

							if ( settings.mark_required ) {
								fieldGroupClasses += ' is-mark-required';
							}
						}

						if ( item.placeholder ) {
							placeholder = 'placeholder="' + _.escape( item.placeholder ) + '"';
						}

						if ( item.allow_multiple ) {
							multiple = ' multiple';
							fieldGroupClasses += ' is-field-type-' + item.field_type + '-multiple';
						}

						switch ( item.field_type ) {
							case 'textarea':
								inputField = '<label class="cool-form-text mdc-text-field mdc-text-field--outlined mdc-text-field--textarea '+ ((item.field_label === '' || !settings.show_labels) ? 'mdc-text-field--no-label' : '') +' ">';
									inputField += '<span class="mdc-notched-outline">';
										inputField += '<span class="mdc-notched-outline__leading"></span>';
										inputField += '<span class="mdc-notched-outline__notch">';
										if ( item.field_label !== '' && settings.show_labels ) {
											inputField += '<span class="mdc-floating-label" id="textarea-label-' + i + '">' + _.escape( item.field_label ) + '</span>';
										}
											inputField += '</span>';
										inputField += '<span class="mdc-notched-outline__trailing"></span>';
									inputField += '</span>';
									inputField += '<span class="mdc-text-field__resizer">';
										inputField += '<textarea class="mdc-text-field__input cool-form__field" name="form_field_' + i + '" id="form_field_' + i + '" rows="' + item.rows + '" ' + required + ' ' + placeholder + '>' + item.field_value + '</textarea>';
									inputField += '</span>';
								inputField += '</label>';
								break;

							case 'text':
							case 'email':
							case 'url':
							case 'number':
							case 'password':
							case 'search':
								inputField = '<label class="cool-form-text mdc-text-field mdc-text-field--outlined '+ ((item.field_label === '' || !settings.show_labels) ? 'mdc-text-field--no-label' : '') +' cool-field-size-'+settings.input_size+'">';
								inputField += '<span class="mdc-notched-outline">';
								inputField += '<span class="mdc-notched-outline__leading"></span>';
								inputField += '<span class="mdc-notched-outline__notch">';
								if ( item.field_label !== '' && settings.show_labels ) {
									inputField += '<span class="mdc-floating-label" id="text-label-' + i + '">' + _.escape( item.field_label ) + '</span>';
								}
								inputField += '</span>';
								inputField += '<span class="mdc-notched-outline__trailing"></span>';
								inputField += '</span>';
								inputField += '<input type="' + item.field_type + '" class="mdc-text-field__input cool-form__field" name="form_field_' + i + '" id="form_field_' + i + '" value="' + item.field_value + '" ' + required + ' ' + placeholder + '>';
									inputField += '<i aria-hidden="true" class="material-icons mdc-text-field__icon mdc-text-field__icon--trailing cool-+ item.field_type +-error-icon" style="display:none">error</i>';
								inputField += '</label>';
									inputField += '<div class="mdc-text-field-helper-line">' +
												'<div class="mdc-text-field-helper-text" id="cool-+ item.field_type +-error" aria-hidden="true"></div>' +
												'</div>';
								break;

							case 'select':
								if ( options.length ) {
									inputField = '<div class="mdc-select mdc-select--outlined cool-field-size-'+settings.input_size+'">';
										inputField += '<div class="mdc-select__anchor cool-field-size-'+settings.input_size+'" aria-labelledby="select-label-' + i + '">';
											inputField += '<span class="mdc-notched-outline">';
												inputField += '<span class="mdc-notched-outline__leading"></span>';
												inputField += '<span class="mdc-notched-outline__notch">';
												if ( item.field_label !== '' && settings.show_labels ) {
													inputField += '<span class="mdc-floating-label" id="select-label-' + i + '">' + _.escape( item.field_label ) + '</span>';
												}
												inputField += '</span>';
												inputField += '<span class="mdc-notched-outline__trailing"></span>';
											inputField += '</span>';
											inputField += '<span class="mdc-select__selected-text-container"><span class="mdc-select__selected-text"></span></span>';
											inputField += '<span class="mdc-select__dropdown-icon">';
												inputField += '<svg class="mdc-select__dropdown-icon-graphic" viewBox="7 10 10 5" focusable="false">';
													inputField += '<polygon class="mdc-select__dropdown-icon-inactive" stroke="none" fill-rule="evenodd" points="7 10 12 15 17 10"></polygon>';
													inputField += '<polygon class="mdc-select__dropdown-icon-active" stroke="none" fill-rule="evenodd" points="7 15 12 10 17 15"></polygon>';
												inputField += '</svg>';
											inputField += '</span>';
										inputField += '</div>';
										inputField += '<div class="mdc-select__menu mdc-menu mdc-menu-surface mdc-menu-surface--fullwidth">';
											inputField += '<ul class="mdc-list" role="listbox" aria-label="' + _.escape( item.field_label ) + '">';
												for ( var x in options ) {
													var option = options[x],
														option_value = option,
														option_label = option;
													if ( option.indexOf( '|' ) > -1 ) {
														var parts = option.split( '|' );
														option_label = parts[0];
														option_value = parts[1];
													}
													var selected = '';
													if ( item.field_value && item.field_value.split( ',' ).indexOf( option_value ) > -1 ) {
														selected = ' aria-selected="true" class="mdc-list-item--selected"';
													}
													inputField += '<li class="mdc-list-item" role="option" data-value="' + _.escape( option_value ) + '"' + selected + '>';
														inputField += '<span class="mdc-list-item__ripple"></span>';
														inputField += '<span class="mdc-list-item__text">' + _.escape( option_label ) + '</span>';
													inputField += '</li>';
												}
											inputField += '</ul>';
										inputField += '</div>';
									inputField += '</div>';
								}
								break;

							case 'radio':
							case 'checkbox':
								if ( options.length ) {
									var multi = ( item.field_type === 'checkbox' && options.length > 1 ) ? '[]' : '';
									inputField = '<div class="mdc-form-field ' + itemClasses + ' ' + ( item.inline_list === 'elementor-subgroup-inline' ? 'inline-items' : 'ontop-items' ) + '">';
									for ( var x in options ) {
										var option = options[x],
											option_value = option,
											option_label = option;
										if ( option.indexOf( '|' ) > -1 ) {
											var parts = option.split( '|' );
											option_label = parts[0];
											option_value = parts[1];
										}
										var inputId = 'form_field_' + item.field_type + i + '-' + x;
										inputField += '<span class="field-sub-options">';
										inputField += '<div class="' + ( item.field_type === 'radio' ? 'mdc-radio' : 'mdc-checkbox' ) + '">';
											inputField += '<input class="' + ( item.field_type === 'radio' ? 'mdc-radio__native-control' : 'mdc-checkbox__native-control' ) + '" type="' + item.field_type + '" id="' + inputId + '" name="form_field_' + i + multi + '" value="' + _.escape( option_value ) + '" ' + ( option_value === item.field_value ? 'checked' : '' ) + ' ' + required + '>';
											if ( item.field_type === 'radio' ) {
												inputField += '<div class="mdc-radio__background">';
													inputField += '<div class="mdc-radio__outer-circle"></div>';
													inputField += '<div class="mdc-radio__inner-circle"></div>';
												inputField += '</div>';
											} else {
												inputField += '<div class="mdc-checkbox__background">';
													inputField += '<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">';
														inputField += '<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"/>';
													inputField += '</svg>';
													inputField += '<div class="mdc-checkbox__mixedmark"></div>';
												inputField += '</div>';
											}
										inputField += '</div>';
										inputField += '<label for="' + inputId + '">' + _.escape( option_label ) + '</label>';
										inputField += '</span>';
									}
									inputField += '</div>';
								}
								break;

							default:
								inputField = elementor.hooks.applyFilters( 'cool_formkit/forms/content_template/field/' + item.field_type, '', item, i, settings );
						}

						switch ( item.field_type ) {
							case 'textarea':
								<!-- inputField = '<textarea class="cool-form__field elementor-field-textual elementor-size-' + settings.input_size + ' ' + itemClasses + '" name="form_field_' + i + '" id="form_field_' + i + '" rows="' + item.rows + '" ' + required + ' ' + placeholder + '>' + item.field_value + '</textarea>'; -->
								break;

							case 'select':
								if ( options ) {
									var size = '';
									if ( item.allow_multiple && item.select_size ) {
										size = ' size="' + item.select_size + '"';
									}
									<!-- inputField = '<div class="elementor-field elementor-select-wrapper ' + itemClasses + '">'; -->
									<!-- inputField += '<select class="cool-form__field elementor-field-textual elementor-size-' + settings.input_size + '" name="form_field_' + i + '" id="form_field_' + i + '" ' + required + multiple + size + ' >'; -->
									for ( var x in options ) {
										var option_value = options[ x ];
										var option_label = options[ x ];
										var option_id = 'form_field_option' + i + x;

										if ( options[ x ].indexOf( '|' ) > -1 ) {
											var label_value = options[ x ].split( '|' );
											option_label = label_value[0];
											option_value = label_value[1];
										}

										view.addRenderAttribute( option_id, 'value', option_value );
										if ( item.field_value.split( ',' ) .indexOf( option_value ) ) {
											view.addRenderAttribute( option_id, 'selected', 'selected' );
										}
										<!-- inputField += '<option ' + view.getRenderAttributeString( option_id ) + '>' + option_label + '</option>'; -->
									}
									<!-- inputField += '</select></div>'; -->
								}
								break;

							case 'radio':
							case 'checkbox':
								if ( options ) {
									var multiple = '';

									if ( 'checkbox' === item.field_type && options.length > 1 ) {
										multiple = '[]';
									}

									<!-- inputField = '<div class="elementor-field-subgroup ' + itemClasses + ' ' + _.escape( item.inline_list ) + '">'; -->

									for ( var x in options ) {
										var option_value = options[ x ];
										var option_label = options[ x ];
										var option_id = 'form_field_' + item.field_type + i + x;
										if ( options[x].indexOf( '|' ) > -1 ) {
											var label_value = options[x].split( '|' );
											option_label = label_value[0];
											option_value = label_value[1];
										}

										view.addRenderAttribute( option_id, {
											value: option_value,
											type: item.field_type,
											id: 'form_field_' + i + '-' + x,
											name: 'form_field_' + i + multiple
										} );

										if ( option_value ===  item.field_value ) {
											view.addRenderAttribute( option_id, 'checked', 'checked' );
										}

										<!-- inputField += '<span class="elementor-field-option"><input ' + view.getRenderAttributeString( option_id ) + ' ' + required + '> '; -->
										<!-- inputField += '<label for="form_field_' + i + '-' + x + '">' + option_label + '</label></span>'; -->

									}

									<!-- inputField += '</div>'; -->
								}
								break;

							case 'text':
							case 'email':
							case 'url':
							case 'password':
							case 'number':
							case 'search':
								itemClasses = 'cool-form-field-textual ' + itemClasses;
								<!-- inputField = '<input size="1" type="' + item.field_type + '" value="' + item.field_value + '" class="cool-form__field elementor-size-' + settings.input_size + ' ' + itemClasses + '" name="form_field_' + i + '" id="form_field_' + i + '" ' + required + ' ' + placeholder + ' >'; -->
								break;
							default:
								item.placeholder = _.escape( item.placeholder );
								<!-- inputField = elementor.hooks.applyFilters( 'cool_formkit/forms/content_template/field/' + item.field_type, '', item, i, settings ); -->
						}

					#>
						<# if ( printLabel && (item.field_type === "radio" || item.field_type === "checkbox" || item.field_type === "acceptance") ) { #>
							<label class="cool-form__field-label" for="form_field_{{ i }}" {{{ labelVisibility }}}>{{{ item.field_label }}}</label>
						<# } #>

						<div class="{{ fieldGroupClasses }}">
							{{{ inputField }}}
						</div>
					<#

						if ( inputField ) {
							#>
							<!-- <div class="{{ fieldGroupClasses }}">

								<# if ( printLabel && item.field_label ) { #>
									<label class="cool-form__field-label" for="form_field_{{ i }}" {{{ labelVisibility }}}>{{{ item.field_label }}}</label>
								<# } #>

								{{{ inputField }}}
							</div> -->
							<#
						}
					}
					
					// Submit group attributes
					view.addRenderAttribute( 'submit-group', {
						class: [ 'cool-form__submit-group' ]
					} );
					if ( settings.button_width ) {
						view.addRenderAttribute( 'submit-group', 'class', 'has-width-' + settings.button_width );
					}
					if ( settings.button_width_tablet ) {
						view.addRenderAttribute( 'submit-group', 'class', 'has-width-md-' + settings.button_width_tablet );
					}
					if ( settings.button_width_mobile ) {
						view.addRenderAttribute( 'submit-group', 'class', 'has-width-sm-' + settings.button_width_mobile );
					}

					// Button attributes  note the same classes as in render_button()
					var buttonClasses = 'cool-form__button cool-form-submit-button';
					if ( settings.button_border_switcher === 'yes' ) {
						buttonClasses += ' has-border';
					}
					if ( settings.button_shape ) {
						buttonClasses += ' has-shape-' + settings.button_shape;
					}
					if ( settings.button_type ) {
						buttonClasses += ' is-type-' + settings.button_type;
					}
					view.addRenderAttribute( 'button', {
						class: buttonClasses,
						type: 'submit'
					} );
					if ( settings.button_hover_animation ) {
						view.addRenderAttribute( 'button', 'class', 'elementor-animation-' + settings.button_hover_animation );
					}
					if ( settings.button_css_id ) {
						view.addRenderAttribute( 'button', 'id', settings.button_css_id );
					}

					// Button text attributes
					view.addRenderAttribute( 'button-text', {
						class: 'cool-form__button-text'
					} );

					// Render the icon similarly to your render function
					var iconHTML = elementor.helpers.renderIcon( view, settings.selected_button_icon, { 'aria-hidden': true }, 'i', 'object' );
					var migrated = elementor.helpers.isIconMigrated( settings, 'selected_button_icon' );
				#>
				<div {{{ view.getRenderAttributeString( 'submit-group' ) }}}>
					<button {{{ view.getRenderAttributeString( 'button' ) }}}>
						<# if ( settings.button_icon || settings.selected_button_icon ) { #>
							<span class="cool-form-button-icon">
								<# if ( iconHTML && iconHTML.rendered && ( ! settings.button_icon || migrated ) ) { #>
									{{{ iconHTML.value }}}
								<# } else { #>
									<i class="{{ settings.button_icon }}" aria-hidden="true"></i>
								<# } #>
							</span>
						<# } #>
						<# if ( settings.button_text ) { #>
							<span {{{ view.getRenderAttributeString( 'button-text' ) }}}>{{{ settings.button_text }}}</span>
						<# } #>
					</button>
				</div>

			</div>
		</form>
		<?php
	}

	protected function register_controls() {
		$this->add_content_form_fields_section();
		$this->add_content_button_section();
		$this->add_content_actions_after_submit_section();
		$this->add_content_additional_options_section();

		$this->add_style_form_section();
		$this->add_style_fields_section();
		$this->add_style_buttons_section();
		$this->add_style_messages_section();
		$this->add_style_box_section();
	}

	protected function add_content_form_fields_section(): void {
		$repeater = new Repeater();

		$field_types = [
			'text' => esc_html__( 'Text', 'cool-formkit' ),
			'email' => esc_html__( 'Email', 'cool-formkit' ),
			'textarea' => esc_html__( 'Textarea', 'cool-formkit' ),
			'tel' => esc_html__( 'Tel', 'cool-formkit' ),
			'select' => esc_html__( 'Select', 'cool-formkit' ),
			'acceptance' => esc_html__( 'Acceptance', 'cool-formkit' ),
			'number' => esc_html__( 'Number', 'cool-formkit' ),
			'date' => esc_html__( 'Date', 'cool-formkit' ),
			'time' => esc_html__( 'Time', 'cool-formkit' ),
			'checkbox' => esc_html__( 'Checkbox', 'cool-formkit' ),
			'radio' => esc_html__( 'Radio', 'cool-formkit' ),
		];

		$field_types = apply_filters( 'cool_formkit/forms/field_types', $field_types );

		$repeater->start_controls_tabs( 'form_fields_tabs' );

		$repeater->start_controls_tab( 'form_fields_content_tab', [
			'label' => esc_html__( 'Content', 'cool-formkit' ),
		] );

		$repeater->add_control(
			'field_type',
			[
				'label' => esc_html__( 'Type', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT,
				'options' => $field_types,
				'default' => 'text',
			]
		);

		$repeater->add_control(
			'field_label',
			[
				'label' => esc_html__( 'Label', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'placeholder',
			[
				'label' => esc_html__( 'Placeholder', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'tel',
								'text',
								'email',
								'textarea',
								'number',
								'date',
								'time',
							],
						],
					],
				],
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$repeater->add_control(
			'required',
			[
				'label' => esc_html__( 'Required', 'cool-formkit' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'true',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => '!in',
							'value' => [
								'checkbox',
								'recaptcha',
								'recaptcha_v3',
								'hidden',
								'html',
								'step',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'field_options',
			[
				'label' => esc_html__( 'Options', 'cool-formkit' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => '',
				'description' => esc_html__( 'Enter each option in a separate line. To differentiate between label and value, separate them with a pipe char ("|"). For example: First Name|f_name', 'cool-formkit' ),
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'select',
								'checkbox',
								'radio',
							],
						],
					],
				],
			]
		);

		// $repeater->add_control(
		// 	'allow_multiple',
		// 	[
		// 		'label' => esc_html__( 'Multiple Selection', 'cool-formkit' ),
		// 		'type' => Controls_Manager::SWITCHER,
		// 		'return_value' => 'true',
		// 		'conditions' => [
		// 			'terms' => [
		// 				[
		// 					'name' => 'field_type',
		// 					'value' => 'select',
		// 				],
		// 			],
		// 		],
		// 	]
		// );

		// $repeater->add_control(
		// 	'select_size',
		// 	[
		// 		'label' => esc_html__( 'Rows', 'cool-formkit' ),
		// 		'type' => Controls_Manager::NUMBER,
		// 		'min' => 2,
		// 		'step' => 1,
		// 		'conditions' => [
		// 			'terms' => [
		// 				[
		// 					'name' => 'field_type',
		// 					'value' => 'select',
		// 				],
		// 				[
		// 					'name' => 'allow_multiple',
		// 					'value' => 'true',
		// 				],
		// 			],
		// 		],
		// 	]
		// );

		$repeater->add_control(
			'inline_list',
			[
				'label' => esc_html__( 'Inline List', 'cool-formkit' ),
				'type' => Controls_Manager::SWITCHER,
				'return_value' => 'elementor-subgroup-inline',
				'default' => '',
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'checkbox',
								'radio',
							],
						],
					],
				],
			]
		);

		$repeater->add_responsive_control(
			'width',
			[
				'label' => esc_html__( 'Column Width', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'100' => '100%',
					'50' => '50%',
					'33' => '33%',
					'25' => '25%',
				],
				'default' => '100',
				'tablet_default' => '100',
				'mobile_default' => '100',
			]
		);

		$repeater->add_control(
			'rows',
			[
				'label' => esc_html__( 'Rows', 'cool-formkit' ),
				'type' => Controls_Manager::NUMBER,
				'default' => 4,
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'value' => 'textarea',
						],
					],
				],
			]
		);

		$repeater->add_control(
			'css_classes',
			[
				'label' => esc_html__( 'CSS Classes', 'cool-formkit' ),
				'type' => Controls_Manager::HIDDEN,
				'default' => '',
				'title' => esc_html__( 'Add your custom class WITHOUT the dot. e.g: my-class', 'cool-formkit' ),
			]
		);

		$repeater->end_controls_tab();

		$repeater->start_controls_tab(
			'form_fields_advanced_tab',
			[
				'label' => esc_html__( 'Advanced', 'cool-formkit' ),
				'condition' => [
					'field_type!' => 'html',
				],
			]
		);

		$repeater->add_control(
			'field_value',
			[
				'label' => esc_html__( 'Default Value', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
				'conditions' => [
					'terms' => [
						[
							'name' => 'field_type',
							'operator' => 'in',
							'value' => [
								'text',
								'email',
								'textarea',
								'tel',
								'number',
								'date',
								'time',
								'select',
							],
						],
					],
				],
			]
		);

		$repeater->add_control(
			'custom_id',
			[
				'label' => esc_html__( 'ID', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'description' => sprintf(
					/* translators: %1$s: Opening code tag, %2$s: Closing code tag. */
					esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page. This field allows %1$sA-z 0-9%2$s & underscore chars without spaces.', 'cool-formkit' ),
					'<code>',
					'</code>'
				),
				'render_type' => 'none',
				'required' => true,
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$shortcode_template = '{{ view.container.settings.get( \'custom_id\' ) }}';

		$repeater->add_control(
			'shortcode',
			[
				'label' => esc_html__( 'Shortcode', 'cool-formkit' ),
				'type' => Controls_Manager::RAW_HTML,
				'classes' => 'forms-field-shortcode',
				'raw' => '<input class="elementor-form-field-shortcode" value=\'[field id="' . $shortcode_template . '"]\' readonly />',
			]
		);

		$repeater->end_controls_tab();

		$repeater->end_controls_tabs();

		$this->start_controls_section(
			'section_form_fields',
			[
				'label' => esc_html__( 'Form Fields', 'cool-formkit' ),
			]
		);

		$this->add_control(
			'form_name',
			[
				'label' => esc_html__( 'Form Name', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'New Form', 'cool-formkit' ),
				'placeholder' => esc_html__( 'Form Name', 'cool-formkit' ),
			]
		);

		$this->add_control(
			'form_fields',
			[
				'type' => Fields_Repeater::CONTROL_TYPE,
				'fields' => $repeater->get_controls(),
				'default' => [
					[
						'custom_id' => 'name',
						'field_type' => 'text',
						'field_label' => esc_html__( 'Name', 'cool-formkit' ),
						'placeholder' => esc_html__( 'Name', 'cool-formkit' ),
						'width' => '100',
						'dynamic' => [
							'active' => true,
						],
					],
					[
						'custom_id' => 'email',
						'field_type' => 'email',
						'required' => 'true',
						'field_label' => esc_html__( 'Email', 'cool-formkit' ),
						'placeholder' => esc_html__( 'Email', 'cool-formkit' ),
						'width' => '100',
					],
					[
						'custom_id' => 'message',
						'field_type' => 'textarea',
						'field_label' => esc_html__( 'Message', 'cool-formkit' ),
						'placeholder' => esc_html__( 'Message', 'cool-formkit' ),
						'width' => '100',
					],
				],
				'title_field' => '{{{ field_label }}}',
			]
		);

		$this->add_control(
			'show_labels',
			[
				'label' => esc_html__( 'Label', 'cool-formkit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cool-formkit' ),
				'label_off' => esc_html__( 'Hide', 'cool-formkit' ),
				'return_value' => 'true',
				'default' => 'true',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'mark_required',
			[
				'label' => esc_html__( 'Required Mark', 'cool-formkit' ),
				'type' => Controls_Manager::SWITCHER,
				'label_on' => esc_html__( 'Show', 'cool-formkit' ),
				'label_off' => esc_html__( 'Hide', 'cool-formkit' ),
				'default' => '',
				'condition' => [
					'show_labels!' => '',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_content_button_section(): void {
		$this->start_controls_section(
			'section_buttons',
			[
				'label' => esc_html__( 'Button', 'cool-formkit' ),
			]
		);

		$this->add_responsive_control(
			'button_width',
			[
				'label' => esc_html__( 'Column Width', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'100' => '100%',
					'50' => '50%',
					'33' => '33%',
					'25' => '25%',
				],
				'default' => '100',
				'frontend_available' => true,
			]
		);

		$this->add_control(
			'button_text',
			[
				'label' => esc_html__( 'Submit', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => esc_html__( 'Send', 'cool-formkit' ),
				'placeholder' => esc_html__( 'Send', 'cool-formkit' ),
				'dynamic' => [
					'active' => true,
				],
				'ai' => [
					'active' => false,
				],
			]
		);

		$this->add_control(
			'selected_button_icon',
			[
				'label' => esc_html__( 'Icon', 'cool-formkit' ),
				'type' => Controls_Manager::ICONS,
				'skin' => 'inline',
				'label_block' => false,
			]
		);

		$this->add_control(
			'button_css_id',
			[
				'label' => esc_html__( 'Button ID', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => '',
				'ai' => [
					'active' => false,
				],
				'title' => esc_html__( 'Add your custom id WITHOUT the Pound key. e.g: my-id', 'cool-formkit' ),
				'description' => sprintf(
					/* translators: %1$s: Opening code tag, %2$s: Closing code tag. */
					esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page. This field allows %1$sA-z 0-9%2$s & underscore chars without spaces.', 'cool-formkit' ),
					'<code>',
					'</code>'
				),
				'separator' => 'before',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_content_actions_after_submit_section(): void {
		$this->start_controls_section(
			'section_integration',
			[
				'label' => esc_html__( 'Actions After Submit', 'cool-formkit' ),
			]
		);

		$actions = Module::instance()->actions_registrar->get();

		$actions_options = [];

		foreach ( $actions as $action ) {
			$actions_options[ $action->get_name() ] = $action->get_label();
		}

		$default_submit_actions = [ 'cool_email' ];

		/**
		 * Default submit actions.
		 *
		 * Filters the list of submit actions pre deffined by Elementor forms.
		 *
		 * By default, only one submit action is set by Elementor forms, an 'email'
		 * action. This hook allows developers to alter those submit action.
		 *
		 * @param array $default_submit_actions A list of default submit actions.
		 */
		$default_submit_actions = apply_filters( 'cool_formkit/forms/default_submit_actions', $default_submit_actions );

		$this->add_control(
			'submit_actions',
			[
				'label' => esc_html__( 'Add Action', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'options' => $actions_options,
				'render_type' => 'none',
				'label_block' => true,
				'default' => $default_submit_actions,
				'description' => esc_html__( 'Add actions that will be performed after a visitor submits the form (e.g. send an email notification). Choosing an action will add its setting below.', 'elementor-pro' ),
			]
		);


		$this->end_controls_section();

		foreach ( $actions as $action ) {
			$action->register_settings_section( $this );
		}

	}

	protected function add_content_additional_options_section(): void {
		$this->start_controls_section(
			'section_form_options',
			[
				'label' => esc_html__( 'Additional Options', 'cool-formkit' ),
				'tab' => Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'form_id',
			[
				'label' => esc_html__( 'Form ID', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'ai' => [
					'active' => false,
				],
				'placeholder' => 'new_form_id',
				'description' => sprintf(
					/* translators: %1$s: Opening code tag, %2$s: Closing code tag. */
					esc_html__( 'Please make sure the ID is unique and not used elsewhere on the page. This field allows %1$sA-z 0-9%2$s & underscore chars without spaces.', 'cool-formkit' ),
					'<code>',
					'</code>'
				),
				'separator' => 'after',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'custom_messages',
			[
				'label' => esc_html__( 'Custom Messages', 'cool-formkit' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'separator' => 'before',
				'render_type' => 'none',
			]
		);

		$default_messages = Ajax_Handler::get_default_messages();

		$this->add_control(
			'success_message',
			[
				'label' => esc_html__( 'Success Message', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => $default_messages[ Ajax_Handler::SUCCESS ],
				'placeholder' => $default_messages[ Ajax_Handler::SUCCESS ],
				'label_block' => true,
				'condition' => [
					'custom_messages!' => '',
				],
				'render_type' => 'none',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'error_message',
			[
				'label' => esc_html__( 'Form Error', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => $default_messages[ Ajax_Handler::ERROR ],
				'placeholder' => $default_messages[ Ajax_Handler::ERROR ],
				'label_block' => true,
				'condition' => [
					'custom_messages!' => '',
				],
				'render_type' => 'none',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'server_message',
			[
				'label' => esc_html__( 'Server Error', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => $default_messages[ Ajax_Handler::SERVER_ERROR ],
				'placeholder' => $default_messages[ Ajax_Handler::SERVER_ERROR ],
				'label_block' => true,
				'condition' => [
					'custom_messages!' => '',
				],
				'render_type' => 'none',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->add_control(
			'invalid_message',
			[
				'label' => esc_html__( 'Invalid Form', 'cool-formkit' ),
				'type' => Controls_Manager::TEXT,
				'default' => $default_messages[ Ajax_Handler::INVALID_FORM ],
				'placeholder' => $default_messages[ Ajax_Handler::INVALID_FORM ],
				'label_block' => true,
				'condition' => [
					'custom_messages!' => '',
				],
				'render_type' => 'none',
				'dynamic' => [
					'active' => true,
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_style_form_section(): void {
		$this->start_controls_section(
			'section_form_style',
			[
				'label' => esc_html__( 'Form', 'cool-formkit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'column_gap',
			[
				'label' => esc_html__( 'Columns Gap', 'cool-formkit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'default' => [
					'size' => 32,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'max' => 60,
					],
					'em' => [
						'max' => 6,
					],
					'rem' => [
						'max' => 6,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-column-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'row_gap',
			[
				'label' => esc_html__( 'Rows Gap', 'cool-formkit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'default' => [
					'size' => 10,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'max' => 60,
					],
					'em' => [
						'max' => 6,
					],
					'rem' => [
						'max' => 6,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-row-gap: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'heading_label',
			[
				'label' => esc_html__( 'Label', 'cool-formkit' ),
				'type' => Controls_Manager::HEADING,
				'separator' => 'before',
			]
		);

		$this->add_control(
			'label_spacing',
			[
				'label' => esc_html__( 'Spacing', 'cool-formkit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'default' => [
					'size' => 0,
					'unit' => 'px',
				],
				'range' => [
					'px' => [
						'max' => 60,
					],
					'em' => [
						'max' => 6,
					],
					'rem' => [
						'max' => 6,
					],
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-label-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->add_control(
			'label_color',
			[
				'label' => esc_html__( 'Text Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-label-color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_control(
			'mark_required_color',
			[
				'label' => esc_html__( 'Mark Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#FF0000',
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-mark-color: {{VALUE}};',
				],
				'condition' => [
					'mark_required' => 'yes',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name'     => 'label_typography',
				'selector' => '{{WRAPPER}} .cool-form__field-label, {{WRAPPER}} .cool-form-text.mdc-text-field .mdc-floating-label, {{WRAPPER}} .cool-form__field-group .mdc-select .mdc-select__anchor .mdc-notched-outline .mdc-notched-outline__notch .mdc-floating-label',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);		

		$this->end_controls_section();
	}

	protected function add_style_fields_section(): void {
		$this->start_controls_section(
			'section_field_style',
			[
				'label' => esc_html__( 'Fields', 'cool-formkit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'input_size',
			[
				'label' => esc_html__( 'Input Size', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'xs' => esc_html__( 'Extra Small', 'cool-formkit' ),
					'sm' => esc_html__( 'Small', 'cool-formkit' ),
					'md' => esc_html__( 'Medium', 'cool-formkit' ),
					'lg' => esc_html__( 'Large', 'cool-formkit' ),
					'xl' => esc_html__( 'Extra Large', 'cool-formkit' ),
				],
				'default' => 'sm',
				'separator' => 'after',
			]
		);

		$this->add_control(
			'field_text_color',
			[
				'label' => esc_html__( 'Text Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-field-text-color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'field_typography',
				'selector' => '{{WRAPPER}} .cool-form .cool-form__wrapper .cool-form__field-group .mdc-text-field:not(.mdc-text-field--disabled) .mdc-text-field__input, {{WRAPPER}} .mdc-text-field:not(.mdc-text-field--disabled) .mdc-text-field__input::placeholder',
				'global'   => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
			]
		);		

		$this->add_control(
			'field_background_color',
			[
				'label' => esc_html__( 'Background Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'default' => '#ffffff',
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-field-bg-color: {{VALUE}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_control(
			'field_border_switcher',
			[
				'label' => esc_html__( 'Border', 'cool-formkit' ),
				'type' => Controls_Manager::SWITCHER,
				'options' => [
					'yes' => esc_html__( 'Yes', 'cool-formkit' ),
					'no' => esc_html__( 'No', 'cool-formkit' ),
				],
				'default' => 'yes',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'field_border_width',
			[
				'label' => esc_html__( 'Border Width', 'cool-formkit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'default' => [
					'size' => 2,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-field-border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'field_border_switcher' => 'yes',
				],
			]
		);

		$this->add_control(
			'field_border_color',
			[
				'label' => esc_html__( 'Border Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-field-border-color: {{VALUE}};',
				],
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'separator' => 'before',
				'condition' => [
					'field_border_switcher' => 'yes',
				],
			]
		);

		$this->add_control(
			'fields_shape',
			[
				'label' => esc_html__( 'Shape', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => 'Default',
					'sharp' => 'Sharp',
					'rounded' => 'Rounded',
					'round' => 'Round',
				],
				'default' => 'default',
			]
		);

		$this->add_control(
			'round_warning_notice',
			[
				'type'            => Controls_Manager::RAW_HTML,
				'raw'             => '<div>Warning: Make sure you have added labels for all your fields. Round shapes require field labels to maintain the design.</div>',
				'content_classes' => 'elementor-panel-alert elementor-panel-alert-warning',
				'condition'       => [
					'fields_shape' => 'round',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_style_buttons_section(): void {
		$this->start_controls_section(
			'section_button_style',
			[
				'label' => esc_html__( 'Button', 'cool-formkit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'button_type',
			[
				'label' => esc_html__( 'Type', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'button' => esc_html__( 'Button', 'cool-formkit' ),
					'link' => esc_html__( 'Link', 'cool-formkit' ),
				],
				'default' => 'button',
			]
		);

		$this->add_responsive_control(
			'button_align',
			[
				'label' => esc_html__( 'Position', 'cool-formkit' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'flex-start' => [
						'title' => esc_html__( 'Start', 'cool-formkit' ),
						'icon' => 'eicon-h-align-left',
					],
					'center' => [
						'title' => esc_html__( 'Center', 'cool-formkit' ),
						'icon' => 'eicon-h-align-center',
					],
					'flex-end' => [
						'title' => esc_html__( 'End', 'cool-formkit' ),
						'icon' => 'eicon-h-align-right',
					],
				],
				'default' => 'center',
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-button-align: {{VALUE}};',
				],
				'condition' => [
					'button_width!' => '100',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'button_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_ACCENT,
				],
				'selector' => '{{WRAPPER}} .cool-form__button',
			]
		);

		$start = is_rtl() ? 'right' : 'left';
		$end = is_rtl() ? 'left' : 'right';

		$this->add_control(
			'button_icon_align',
			[
				'label' => esc_html__( 'Icon Position', 'cool-formkit' ),
				'type' => Controls_Manager::CHOOSE,
				'default' => is_rtl() ? 'row-reverse' : 'row',
				'options' => [
					'row' => [
						'title' => esc_html__( 'Start', 'cool-formkit' ),
						'icon' => "eicon-h-align-{$start}",
					],
					'row-reverse' => [
						'title' => esc_html__( 'End', 'cool-formkit' ),
						'icon' => "eicon-h-align-{$end}",
					],
				],
				'selectors_dictionary' => [
					'left' => is_rtl() ? 'row-reverse' : 'row',
					'right' => is_rtl() ? 'row' : 'row-reverse',
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-button-icon-position: {{VALUE}};',
				],
				'condition' => [
					'selected_button_icon[value]!' => '',
				],
			]
		);

		$this->add_control(
			'button_icon_indent',
			[
				'label' => esc_html__( 'Icon Spacing', 'cool-formkit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'default' => [
					'size' => 8,
					'unit' => 'px',
				],
				'condition' => [
					'selected_button_icon[value]!' => '',
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-button-icon-spacing: {{SIZE}}{{UNIT}};',
				],
			]
		);

		$this->start_controls_tabs( 'tabs_button_style' );

		$this->start_controls_tab(
			'tab_button_normal',
			[
				'label' => esc_html__( 'Normal', 'cool-formkit' ),
			]
		);

		$this->add_control(
			'button_text_color',
			[
				'label' => esc_html__( 'Text Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_SECONDARY,
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-button-text-color: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .is-type-button.cool-form__button',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
					],
				],
				'condition' => [
					'button_type' => 'button',
				],
			]
		);

		$this->end_controls_tab();

		$this->start_controls_tab(
			'tab_button_hover',
			[
				'label' => esc_html__( 'Hover', 'cool-formkit' ),
			]
		);

		$this->add_control(
			'button_text_color_hover',
			[
				'label' => esc_html__( 'Text Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-button-text-color-hover: {{VALUE}};',
				],
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'button_background_hover',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .is-type-button.cool-form__button:hover, {{WRAPPER}} .is-type-button.cool-form__button:focus',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
					'color' => [
						'global' => [
							'default' => Global_Colors::COLOR_ACCENT,
						],
					],
				],
				'condition' => [
					'button_type' => 'button',
				],
			]
		);

		$this->add_control(
			'button_hover_animation',
			[
				'label' => esc_html__( 'Animation', 'cool-formkit' ),
				'type' => Controls_Manager::HOVER_ANIMATION,
			]
		);

		$this->end_controls_tab();

		$this->end_controls_tabs();

		$this->add_control(
			'button_border_switcher',
			[
				'label' => esc_html__( 'Border', 'cool-formkit' ),
				'type' => Controls_Manager::SWITCHER,
				'options' => [
					'yes' => esc_html__( 'Yes', 'cool-formkit' ),
					'no' => esc_html__( 'No', 'cool-formkit' ),
				],
				'default' => '',
				'separator' => 'before',
			]
		);

		$this->add_control(
			'button_border_width',
			[
				'label' => esc_html__( 'Border Width', 'cool-formkit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', 'custom' ],
				'range' => [
					'px' => [
						'max' => 100,
					],
					'em' => [
						'max' => 10,
					],
					'rem' => [
						'max' => 10,
					],
				],
				'default' => [
					'size' => 2,
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-button-border-width: {{SIZE}}{{UNIT}};',
				],
				'condition' => [
					'button_border_switcher' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_border_color',
			[
				'label' => esc_html__( 'Border Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'global' => [
					'default' => Global_Colors::COLOR_TEXT,
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-button-border-color: {{VALUE}};',
				],
				'condition' => [
					'button_border_switcher' => 'yes',
				],
			]
		);

		$this->add_control(
			'button_shape',
			[
				'label' => esc_html__( 'Shape', 'cool-formkit' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					'default' => 'Default',
					'sharp' => 'Sharp',
					'rounded' => 'Rounded',
					'round' => 'Round',
				],
				'default' => 'default',
			]
		);

		$this->add_responsive_control(
			'button_text_padding',
			[
				'label' => esc_html__( 'Padding', 'cool-formkit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem', 'custom' ],
				'default' => [
					'top' => '8',
					'right' => '40',
					'bottom' => '8',
					'left' => '40',
					'unit' => 'px',
				],
				'mobile_default' => [
					'top' => '8',
					'right' => '40',
					'bottom' => '8',
					'left' => '40',
					'unit' => 'px',
				],
				'tablet_default' => [
					'top' => '8',
					'right' => '40',
					'bottom' => '8',
					'left' => '40',
					'unit' => 'px',
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-button-padding-block-end: {{BOTTOM}}{{UNIT}}; --cool-form-button-padding-block-start: {{TOP}}{{UNIT}}; --cool-form-button-padding-inline-end: {{RIGHT}}{{UNIT}}; --cool-form-button-padding-inline-start: {{LEFT}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}

	protected function add_style_messages_section(): void {
		$this->start_controls_section(
			'section_messages_style',
			[
				'label' => esc_html__( 'Messages', 'cool-formkit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'message_typography',
				'global' => [
					'default' => Global_Typography::TYPOGRAPHY_TEXT,
				],
				'selector' => '{{WRAPPER}} .elementor-message',
			]
		);

		$this->add_control(
			'success_message_color',
			[
				'label' => esc_html__( 'Success Message Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-message.elementor-message-success' => 'color: {{COLOR}};',
				],
			]
		);

		$this->add_control(
			'error_message_color',
			[
				'label' => esc_html__( 'Error Message Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-message.elementor-message-danger' => 'color: {{COLOR}};',
				],
			]
		);

		$this->add_control(
			'inline_message_color',
			[
				'label' => esc_html__( 'Inline Message Color', 'cool-formkit' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .elementor-message.elementor-help-inline' => 'color: {{COLOR}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function add_style_box_section(): void {
		$this->start_controls_section(
			'section_box_style',
			[
				'label' => esc_html__( 'Box', 'cool-formkit' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'box_heading',
			[
				'label' => esc_html__( 'Background', 'cool-formkit' ),
				'type' => Controls_Manager::HEADING,
			]
		);

		$this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name' => 'box_background',
				'types' => [ 'classic', 'gradient' ],
				'exclude' => [ 'image' ],
				'selector' => '{{WRAPPER}} .cool-form',
				'fields_options' => [
					'background' => [
						'default' => 'classic',
					],
				],
			]
		);

		$this->add_responsive_control(
			'content_width',
			[
				'label' => esc_html__( 'Content Width', 'cool-formkit' ),
				'type' => Controls_Manager::SLIDER,
				'size_units' => [ 'px', 'em', 'rem', '%', 'custom' ],
				'range' => [
					'px' => [
						'max' => 1600,
					],
					'%' => [
						'max' => 100,
					],
				],
				'default' => [
					'size' => 100,
					'unit' => '%',
				],
				'tablet_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'mobile_default' => [
					'size' => 100,
					'unit' => '%',
				],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-content-width: {{SIZE}}{{UNIT}};',
				],
				'separator' => 'before',
			]
		);

		$this->add_responsive_control(
			'box_padding',
			[
				'label' => esc_html__( 'Padding', 'cool-formkit' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em', 'rem' ],
				'selectors' => [
					'{{WRAPPER}} .cool-form' => '--cool-form-box-padding-block-end: {{BOTTOM}}{{UNIT}}; --cool-form-box-padding-block-start: {{TOP}}{{UNIT}}; --cool-form-box-padding-inline-end: {{RIGHT}}{{UNIT}}; --cool-form-box-padding-inline-start: {{LEFT}}{{UNIT}};',
				],
				'default' => [
					'top' => '0',
					'right' => '0',
					'bottom' => '0',
					'left' => '0',
					'unit' => 'px',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();
	}
}
