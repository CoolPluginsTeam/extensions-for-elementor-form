import * as hooks from './hooks/';

export default class Component extends $e.modules.ComponentBase {
	getNamespace() {
		return 'cool-forms-lite';
	}

	defaultHooks() {
		return this.importHooks( hooks );
	}
}
