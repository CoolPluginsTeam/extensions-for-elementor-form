export default elementorModules.frontend.handlers.Base.extend( {
	getDefaultSettings() {
		return {
			selectors: {
				form: '.cool-form',
			},
		};
	},

	getDefaultElements() {
		var selectors = this.getSettings( 'selectors' ),
			elements = {};

		elements.$form = this.$element.find( selectors.form );

		return elements;
	},

	bindEvents() {
		this.elements.$form.on( 'form_destruct', this.handleSubmit );
	},

	handleSubmit( event, response ) {
		if ( 'undefined' !== typeof response.data.redirect_url ) {
			location.href = response.data.redirect_url;
		}
	},
} );
