module.exports = elementorModules.editor.utils.Module.extend( {

  renderField( inputField, item, i, settings ) {
    var itemClasses = _.escape(item.css_classes),
        required = (item.required) ? ' required' : '',
        min = (item.min_date) ? ' min="' + item.min_date + '"' : '',
        max = (item.max_date) ? ' max="' + item.max_date + '"' : '',
        placeholder = (item.placeholder) ? ' placeholder="' + _.escape(item.placeholder) + '"' : '';

    if ('yes' === item.use_native_date) {
        itemClasses += ' cool-form-use-native';
    }

    return `
    <label class="cool-form-text mdc-text-field mdc-text-field--outlined ${(item.field_label === '' || !settings.show_labels) ? 'mdc-text-field--no-label' : ''} cool-field-size-${settings.input_size}">
      <span class="mdc-notched-outline">
        <span class="mdc-notched-outline__leading"></span>
        <span class="mdc-notched-outline__notch">
          ${ ( item.field_label !== '' && settings.show_labels )
            ? `<span class="mdc-floating-label" id="date-label-${i}">${_.escape(item.field_label)}</span>`
            : '' }
        </span>
        <span class="mdc-notched-outline__trailing"></span>
      </span>
      <input type="date" size="1" ${min} ${max} ${placeholder}
        pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}"
        class="mdc-text-field__input cool-form__field cool-form-date-field elementor-field elementor-size-${settings.input_size} ${itemClasses}"
        name="form_field_${i}" id="form_field_${i}" ${required} >
    </label>
  `;
  
},


	onInit() {
		elementor.hooks.addFilter( 'cool_formkit/forms/content_template/field/date', this.renderField, 10, 4 );
	},
} );
