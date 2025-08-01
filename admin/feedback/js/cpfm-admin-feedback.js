jQuery(document).ready(function ($) {
    const noticePanel = $("#cpfNoticePanel");
    let isPanelVisible = !1;
    var urlParams = new URLSearchParams(window.location.search);
    var currentPage = urlParams.get("page");
    if (noticePanel.data("auto-show") && adminNotice.autoShowPages.includes(currentPage)) {
        setTimeout(function () {
            noticePanel.fadeIn(300);
            isPanelVisible = !0;
        }, 500);
    }
    $(document).on("click", "#wp-admin-bar-cpf-notice-admin-button > a", function (e) {
        e.preventDefault();
        isPanelVisible ? noticePanel.fadeOut(300) : noticePanel.fadeIn(300);
        isPanelVisible = !isPanelVisible;
    });
    $(document).on("click", "#cpfm_remove_notice", function (e) {
        e.preventDefault();
        noticePanel.fadeOut(300);
        isPanelVisible = false;
    });
    $(document).on("click", ".opt-in-yes, .opt-in-no", function (e) {
        e.preventDefault();
        const button = $(this);
        const category = button.data("category");
        const optIn = button.val();
        const noticeItem = button.closest(".notice-item");
        $.post(adminNotice.ajaxurl, { action: "cpfm_handle_opt_in", nonce: adminNotice.nonce, category: category, opt_in: optIn }, function (response) {
            if (response.success) {
                noticeItem.slideUp(300, function () {
                    noticeItem.remove();
                    const remainingNotices = $("#cpfNoticePanel .notice-item.unread");
                    const count = remainingNotices.length;
                    $(".cpf-notice-admin-button .notice-count").text(count);
                    if (count === 0) {
                        $("#cpfNoticePanel").fadeOut(300);
                        $("#wp-admin-bar-cpf-notice-admin-button").fadeOut(300, function () {
                            $(this).remove();
                        });
                    }
                });
            }
        });
    });
    $(".cpf-toggle-extra").on("click", function () {
        $(this).closest(".notice-item").find(".cpf-extra-info").slideToggle();
    });
});
