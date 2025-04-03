jQuery(document).ready(function (e) {
  e(".elementor-form").submit(function (t) {
    var a = e(this),
      s = e(this).find('input[name="form_id"]').val();
    e(a).addClass(s),
      e(document).ajaxSuccess(function (t) {
        0 !=
          e(".elementor-form-fields-wrapper").hasClass(
            "ele-extensions-hide-form"
          ) && e(a).find(".ele-extensions-hide-form").hide(),
          0 !=
            e("." + s)
              .prev()
              .hasClass("custom-sucess-message") &&
            (e(".elementor-message-success").hide(),
            e("." + s)
              .prev(".custom-sucess-message")
              .delay(100)
              .fadeIn());
      });
  });
});

