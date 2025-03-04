export class FormSanitizeId extends $e.modules.hookData.Dependency {
	ID_SANITIZE_FILTER = /[^\w]/g;

	getCommand() {
		return 'document/elements/settings';
	}

	getId() {
		return 'cool-forms-sanitize-ids';
	}

	getContainerType() {
		return 'widget';
	}

	getConditions( args ) {
		return undefined !== args.settings.form_id;
	}

	apply( args ) {
		const { container, settings } = args;
		const { form_id: formId } = settings;

		// Re-render with old settings.
		if ( formId.match( this.ID_SANITIZE_FILTER ) ) {
			const formIdView = container.panel.getControlView( 'form_id' );

			formIdView.render();
			formIdView.$el.find( 'input' ).trigger( 'focus' );

			// Hook-Break.
			return false;
		}

		return true;
	}
}

export default FormSanitizeId;
