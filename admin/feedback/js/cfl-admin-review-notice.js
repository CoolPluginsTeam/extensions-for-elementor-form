jQuery(document).ready(function ($) {

     // Dismiss admin review notice ajax call
		$(".cfl_dismiss_notice").on("click", function (event) {
				var $this = $(this);
				var wrapper=$this.parents(".cfl-review-notice-wrapper");
				var ajaxURL=wrapper.data("ajax-url");
				var nonce = wrapper.data("nonce");
				var ajaxCallback=wrapper.data("ajax-callback");         
				$.post(ajaxURL, { "action":ajaxCallback, cfl_notice_dismiss: true, nonce: nonce }, function( data ) {
					wrapper.slideUp("fast");
				}, "json");
			});
})
 