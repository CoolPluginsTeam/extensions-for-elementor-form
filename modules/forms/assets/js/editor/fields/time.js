module.exports = elementorModules.editor.utils.Module.extend( {

  renderField( inputField, item, i, settings ) {
    var itemClasses = _.escape(item.css_classes),
        required = (item.required) ? ' required' : '',
        placeholder = (item.placeholder) ? ' placeholder="' + _.escape(item.placeholder) + '"' : '';

    if ('yes' === item.use_native_time) {
        itemClasses += ' cool-form-use-native';
    }

    return `
    <label class="cool-form-text mdc-text-field mdc-text-field--outlined">
      <span class="mdc-notched-outline">
        <span class="mdc-notched-outline__leading"></span>
        <span class="mdc-notched-outline__notch">
          ${ ( item.field_label !== '' && settings.show_labels )
            ? `<span class="mdc-floating-label" id="time-label-${i}">${_.escape(item.field_label)}</span>`
            : '' }
        </span>
        <span class="mdc-notched-outline__trailing"></span>
      </span>
      <input type="time" size="1" ${placeholder}
        class="mdc-text-field__input cool-form-time-field elementor-field elementor-size-${settings.input_size} ${itemClasses}"
        name="form_field_${i}" id="form_field_${i}" ${required} >
    </label>
  `;
  

},

	onInit() {
		elementor.hooks.addFilter( 'cool_formkit/forms/content_template/field/time', this.renderField, 10, 4 );
	},
} );
