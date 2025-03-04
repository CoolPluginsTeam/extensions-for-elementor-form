export class FormFieldsAddFirstStep extends $e.modules.hookData.After {
    getCommand() {
      return 'document/elements/settings';
    }
    getId() {
      return 'cool-forms-fields-first-step';
    }
    getContainerType() {
      return 'repeater';
    }
    getConditions(args) {
      const {
        containers = [args.container]
      } = args;
      return 'cool-form' === containers[0].parent.parent.model.get('widgetType') && 'step' === args.settings.field_type;
    }
    apply(args) {
      const {
        containers = [args.container]
      } = args;
      containers.forEach((/** Container */container) => {
        const firstItem = container.parent.children[0];
        if ('step' === firstItem.settings.get('field_type')) {
          return;
        }
        $e.run('document/repeater/insert', {
          container: container.parent.parent,
          // Widget
          name: 'form_fields',
          model: {
            field_type: 'step'
          },
          options: {
            at: 0,
            external: true
          }
        });
      });
      return true;
    }
}

export default FormFieldsAddFirstStep;