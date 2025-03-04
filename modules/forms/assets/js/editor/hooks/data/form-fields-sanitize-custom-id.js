export class FormFieldsSanitizeCustomId extends $e.modules.hookData.Dependency {
	ID_SANITIZE_FILTER = /[^\w]/g;

	getCommand() {
		return 'document/elements/settings';
	}

	getId() {
		return 'cool-forms-fields-sanitize-custom-ids';
	}

	getContainerType() {
		return 'repeater';
	}

	getConditions( args ) {
		return undefined !== args.settings.custom_id;
	}

	apply( args ) {
		const { containers = [ args.container ], settings } = args,
			// `custom_id` is the control name.
			{ custom_id: customId } = settings;

		if ( customId.match( this.ID_SANITIZE_FILTER ) ) {
			// Re-render with old settings.
			containers.forEach( ( container ) => {
				const panelView = container.panel.getControlView( 'form_fields' ),
					currentItemView = panelView.children.findByModel( container.settings ),
					idView = currentItemView.children.find( ( view ) => 'custom_id' === view.model.get( 'name' ) );

				idView.render();

				idView.$el.find( 'input' ).trigger( 'focus' );
			} );

			// Hook-Break.
			return false;
		}

		return true;
	}
}

export default FormFieldsSanitizeCustomId;
