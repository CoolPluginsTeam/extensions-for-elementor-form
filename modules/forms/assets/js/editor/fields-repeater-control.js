import RepeaterRowView from './fields-repeater-row';

export default class extends elementor.modules.controls.Repeater {
	className() {
		let classes = super.className();

		classes += ' elementor-control-type-repeater';

		return classes;
	}

	getChildView() {
		return RepeaterRowView;
	}

	initialize( ...args ) {
		super.initialize( ...args );

		const formFields = this.container.settings.get( 'form_fields' );

		this
			.listenTo( formFields, 'change', ( model ) => this.onFormFieldChange( model ) )
			.listenTo( formFields, 'remove', ( model ) => this.onFormFieldRemove( model ) );
	}

	getFirstChild() {
		return this.children.findByModel( this.collection.models[ 0 ] );
	}

	lockFirstStep() {
		const firstChild = this.getFirstChild();

		if ( 'step' !== firstChild.model.get( 'field_type' ) ) {
			return;
		}

		const stepFields = this.collection.where( { field_type: 'step' } );

		if ( 1 < stepFields.length ) {
			firstChild.toggleFieldTypeControl( false );

			firstChild.toggleTools( false );
		}

		firstChild.toggleSort( false );
	}

	onFormFieldChange( model ) {
		const fieldType = model.changed.field_type;

		if ( ! fieldType || ( 'step' !== fieldType && 'step' !== model._previousAttributes.field_type ) ) {
			return;
		}

		const isStep = 'step' === fieldType;

		this.children.findByModel( model ).toggleStepField( isStep );

		this.onStepFieldChanged( isStep );
	}

	onFormFieldRemove( model ) {
		if ( 'step' === model.get( 'field_type' ) ) {
			this.onStepFieldChanged( false );
		}
	}

	onStepFieldChanged( isStep ) {
		if ( isStep ) {
			this.lockFirstStep();

			return;
		}

		const stepFields = this.collection.where( { field_type: 'step' } );

		if ( stepFields.length > 1 ) {
			return;
		}

		const firstChild = this.getFirstChild();

		if ( 1 === stepFields.length ) {
			firstChild.toggleTools( true );

			firstChild.toggleFieldTypeControl( true );

			return;
		}

		firstChild.toggleSort( true );
	}

	onAddChild( childView ) {
		super.onAddChild( childView );

		if ( 'step' === childView.model.get( 'field_type' ) ) {
			this.lockFirstStep();

			childView.toggleStepField( true );
		}
	}
}
