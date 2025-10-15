(function () {
    [].slice.call(document.querySelectorAll('.tabs')).forEach(function (el) {
        new CBPFWTabs(el);
    });
})();

function oldSafariCSSfix() {
    if (isOldSafari()) {
        var tabnr = $(".login-page .tabs nav ul li").length;
        var width = jQuery('.login-page').width() - 32;

        jQuery(".login-page .tabs nav ul li").width((width / tabnr) - 1).css("float", "left");
        jQuery(".login-page .tabs nav").css("width", (width + 1));
    }
}

$(document).ready(function() {
	$(document).on("click", ".tabs ul:not(.fileThumbs) li", function() {
	});
});

jQuery(window).load(function () {
    oldSafariCSSfix();
});

jQuery(window).resize(function () {
    oldSafariCSSfix();
});

function isOldSafari() {
    return !!navigator.userAgent.match(' Safari/') && !navigator.userAgent.match(' Chrome') && (!!navigator.userAgent.match(' Version/6.0') || !!navigator.userAgent.match(' Version/5.'));
}
