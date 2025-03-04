export default class extends elementor.modules.controls.RepeaterRow {
	toggleFieldTypeControl( show ) {
		const fieldTypeModel = this.collection.findWhere( { name: 'field_type' } ),
			fieldTypeControl = this.children.findByModel( fieldTypeModel );

		fieldTypeControl.$el.toggle( show );
	}

	toggleStepField( isStep ) {
		this.$el.toggleClass( 'elementor-repeater-row--form-step', isStep );
	}

	toggleTools( show ) {
		this.ui.removeButton.add( this.ui.duplicateButton ).toggle( show );
	}
}
