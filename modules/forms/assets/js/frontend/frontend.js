export default class coolFormsLite extends elementorModules.Module {
	constructor() {
		super();

		elementorFrontend.elementsHandler.attachHandler( 'cool-form', [
			() => import( /* webpackChunkName: 'js/cool-form-lite' */ './handlers/form-sender' ),
			() => import( /* webpackChunkName: 'js/cool-form-lite' */ './handlers/form-redirect' ),
		] );

		elementorFrontend.elementsHandler.attachHandler( 'subscribe', [
			() => import( /* webpackChunkName: 'js/cool-form-lite' */ './handlers/form-sender' ),
			() => import( /* webpackChunkName: 'js/cool-form-lite' */ './handlers/form-redirect' ),
		] );
	}
}

window.addEventListener( 'elementor/frontend/init', () => {
	new coolFormsLite();
} );

