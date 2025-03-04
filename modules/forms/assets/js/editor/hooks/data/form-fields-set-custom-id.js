export class FormFieldsSetCustomId extends $e.modules.hookData.After {
	getCommand() {
		return 'document/repeater/insert';
	}

	getId() {
		return 'cool-forms-fields-set-custom-ids';
	}

	getContainerType() {
		return 'widget';
	}

	getConditions( args ) {
		return 'form_fields' === args.name;
	}

	apply( args, model ) {
		const { containers = [ args.container ] } = args,
			isDuplicate = $e.commands.isCurrentFirstTrace( 'document/repeater/duplicate' );

		containers.forEach( ( /** Container */ container ) => {
			const itemContainer = container.repeaters.form_fields.children.find( ( childrenContainer ) => {
				// Sometimes, one of children is {Empty}.
				if ( childrenContainer ) {
					return model.get( '_id' ) === childrenContainer.id;
				}

				return false;
			} );

			if ( ! isDuplicate && itemContainer.settings.get( 'custom_id' ) ) {
				return;
			}

			$e.run( 'document/elements/settings', {
				container: itemContainer,
				settings: {
					custom_id: 'field_' + itemContainer.id,
				},
				options: { external: true },
			} );
		} );

		return true;
	}
}

export default FormFieldsSetCustomId;
