module.exports = elementorModules.editor.utils.Module.extend( {

	renderField( inputField, item, i, settings ) {
		var itemClasses = _.escape( item.css_classes ),
			required = ( item.required ) ? ' required' : '',
			checked  = ( item.checked_by_default ) ? ' checked="checked"' : '',
			label    = '';
	
		if ( item.acceptance_text ) {
			label = '<label for="form_field_' + i + '" class="cool-form__field-label">' + _.escape( item.acceptance_text ) + '</label>';
		}
	
		return '<div class="mdc-form-field">' +
				 '<div class="mdc-checkbox">' +
				   '<input size="1" type="checkbox" ' + checked +
						  ' class="mdc-checkbox__native-control elementor-size-' + settings.input_size + ' ' + itemClasses + '"' +
						  ' name="form_field_' + i + '" id="form_field_' + i + '"' + required + ' >' +
				   '<div class="mdc-checkbox__background">' +
					 '<svg class="mdc-checkbox__checkmark" viewBox="0 0 24 24">' +
					   '<path class="mdc-checkbox__checkmark-path" fill="none" d="M1.73,12.91 8.1,19.28 22.79,4.59"></path>' +
					 '</svg>' +
					 '<div class="mdc-checkbox__mixedmark"></div>' +
				   '</div>' +
				 '</div>' +
				 label +
			   '</div>';
	},
	
	onInit() {
		elementor.hooks.addFilter( 'cool_formkit/forms/content_template/field/acceptance', this.renderField, 10, 4 );
	},
} );
