export class FormFieldsUpdateShortCode extends $e.modules.hookUI.After {
	getCommand() {
		return 'document/elements/settings';
	}

	getId() {
		return 'cool-forms-forms-fields-update-shortcodes';
	}

	getContainerType() {
		return 'repeater';
	}

	getConditions( args ) {
		if ( ! $e.routes.isPartOf( 'panel/editor' ) || undefined === args.settings.custom_id ) {
			return false;
		}

		return true;
	}

	apply( args ) {
		const { containers = [ args.container ] } = args;

		containers.forEach( ( /** Container */ container ) => {
			const panelView = container.panel.getControlView( 'form_fields' ),
				currentItemView = panelView.children.find( ( view ) => container.id === view.model.get( '_id' ) ),
				shortcodeView = currentItemView.children.find( ( view ) => 'shortcode' === view.model.get( 'name' ) );

			shortcodeView.render();
		} );
	}
}

export default FormFieldsUpdateShortCode;
