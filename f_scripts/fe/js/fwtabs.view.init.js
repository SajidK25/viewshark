(function () {
    [].slice.call(document.querySelectorAll('.tabs')).forEach(function (el) {
        new CBPFWTabs(el);
    });
})();

function oldSafariCSSfix() {
    if (isOldSafari()) {
        var tabnr = 4;
        var width = jQuery('#siteContent').width() - 32;

        jQuery("#div-share nav ul li").width((width / tabnr) - 1).css("float", "left");
        jQuery(".tabs nav").css("width", (width + 1));
    }
}

$(document).ready(function() {
    $(document).on("click", ".tabs ul:not(.fileThumbs) li", function() {
            if ($(this).find("a").attr("href") == "#section-thumb") {
            		$(".save-entry-button").hide();
            } else {
            		$(".save-entry-button").show();
            }
	});
});

jQuery(window).load(function () {
    oldSafariCSSfix();
});

jQuery(window).resize(function () {
    oldSafariCSSfix();
});
