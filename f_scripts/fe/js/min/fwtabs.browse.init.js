(function() {
    [].slice.call(document.querySelectorAll(".tabs")).forEach(function(el) {
        new CBPFWTabs(el);
    });
})();


function mlazyload() {
        var cid = $(".content-current .main-view-mode.active").attr("id");
        $("#" + cid + "-more.more-button:last").unbind().bind('inview', function (event, isInView, visiblePartX, visiblePartY) {
        if (isInView) {
                if (visiblePartY == 'top') {
                } else if (visiblePartY == 'bottom') {
                } else {
                        $("#" + cid + "-more.more-button:last").click();
                }
        } else {
        }
        });
}



(function() {
    jQuery(document).on({
        click: function() {
            var section = jQuery(this).find("a").attr("href").split("-");
            var sort = section[1];
            var sid = jQuery("#section-" + sort + " .main-view-mode.active").attr("id");
            var id = sid.split("-");
            var idnr = id[3];
            if (jQuery("#" + sid + "-list ul").length == 0) {
                var ct = "";
                var url = _rel + "?p=0&m=" + idnr + "&sort=" + sort;
                if (typeof jQuery("#categories-accordion li a.selected").attr("rel-name") != "undefined") {
                    ct = "&c=" + jQuery("#categories-accordion li a.selected").attr("rel-name");
                }
                url += ct;
                if (typeof ch_id != "undefined") {
                    url += "&u=" + ch_id;
                }
                jQuery("#" + sid + "-list").mask("");
                jQuery("#main-view-mode-" + idnr + "-" + sort + " span").removeClass("icon-thumbs").addClass("spinner icon-spinner");
                jQuery("#" + sid + "-list").load(url, function() {
                    jQuery("#" + sid + "-list").unmask();
                    thumbFade();
                    setTimeout(function(){ mlazyload() }, 300);
                    jQuery("#main-view-mode-" + idnr + "-" + sort + " span").addClass("icon-thumbs").removeClass("spinner icon-spinner");
                });
            }
        }
    }, ".tabs ul:not(.fileThumbs):not(.no-content) li");
})();

function oldSafariCSSfix() {
    if (isOldSafari()) {
        var tabnr = $("#main-content.tabs nav ul li").length;
        var width = jQuery("#siteContent").width() - 32;
        jQuery("#main-content nav ul li").width(width / tabnr - 1).css("float", "left");
        jQuery(".tabs nav").css("width", width + 1);
    }
}

jQuery(window).load(function() {
    oldSafariCSSfix();
});

jQuery(window).resize(function() {
    oldSafariCSSfix();
});

var SizeSetFunctions = function() {};

var ViewModeSizeSetFunctions = function() {};

jQuery(window).resize(function() {});

jQuery(window).load(function() {
    thumbFade();
    mlazyload();
    jQuery(document).on({
        click: function() {
            var ct = "";
            var id = jQuery("#main-content .content-current .main-view-mode.active").attr("id");
            var type = jQuery("#main-content .content-current .main-view-mode.active").val();
            var nr = id.split("-");
            var idnr = nr[3];
            var page = parseInt(jQuery(this).attr("rel-page"));
            var more = jQuery("#main-view-mode-" + idnr + "-" + type + "-more").parent();
            var more_clone = more.clone(true);
            var url = _rel + "?p=0&m=" + idnr + "&sort=" + type;
            if (typeof jQuery("#categories-accordion li a.selected").attr("rel-name") != "undefined") {
                ct = "&c=" + jQuery("#categories-accordion li a.selected").attr("rel-name");
            }
            url += ct;
            if (typeof ch_id != "undefined") {
                url += "&u=" + ch_id;
            }
            url += "&page=" + page;
	    jQuery("#main-view-mode-" + idnr + "-" + type + "-list span.load-more-text i").removeClass("iconBe-plus").addClass("spinner icon-spinner");

            jQuery.get(url, function(result) {
            	more.detach();
                jQuery("#main-view-mode-" + idnr + "-" + type + "-list ul").append(result).unmask();
                jQuery("#main-view-mode-" + idnr + "-" + type + "-list span.load-more.loadmask-img").hide();
                more_clone.appendTo("#main-view-mode-" + idnr + "-" + type + "-list").find(".more-button").attr("rel-page", page + 1);
                thumbFade();
            });
        }
    }, ".more-button");
    jQuery(document).on({
        click: function() {
            var file_key = jQuery(this).attr("rel-key");
            var file_type = jQuery(this).attr("rel-type");
            var url = _rel + "?a=cb-watchadd&for=sort-" + file_type;
            var _this = jQuery(this);
            if (_this.find(jslang["lss"] == "1" ? "icon-check" : "icon-warning").hasClass("icon-check")) {
                return;
            }
            _this.parent().next().mask("");
            _this.next().text(jslang["loading"]);
            jQuery.post(url, {
                "fileid[0]": file_key
            }, function(result) {
                _this.find(".icon-clock").removeClass("icon-clock").addClass(jslang["lss"] == "1" ? "icon-check" : "icon-warning");
                _this.next().text(jslang["lss"] == "1" ? jslang["inwatchlist"] : jslang["nowatchlist"]);
                _this.parent().next().unmask();
            });
        }
    }, ".watch_later_wrap");
    jQuery(document).on({
        click: function() {
            if (jQuery(this).hasClass("active")) {
                return;
            }
            var current = jQuery(".main-view-mode.active").attr("id");
            var cnr = current.split("-");
            var id = jQuery(this).attr("id");
            var type = jQuery(this).val();
            var nr = id.split("-");
            var idnr = nr[3];
            jQuery("#section-" + type + " .main-view-mode").removeClass("active");
            jQuery("#" + id).addClass("active");
            if (jQuery("#main-view-mode-" + idnr + "-" + type + "-list ul").length > 0) {
                jQuery("#section-" + type + " .mview").hide();
                jQuery("#main-view-mode-" + idnr + "-" + type + "-list").show();
                ViewModeSizeSetFunctions();
                $.get(_rel + "?p=0&vm=" + id, function(data) {});
            } else {
                var ct = "";
                var url = _rel + "?p=0&m=" + idnr + "&sort=" + type;
                if (typeof jQuery("#categories-accordion li a.selected").attr("rel-name") != "undefined") {
                    ct = "&c=" + jQuery("#categories-accordion li a.selected").attr("rel-name");
                }
                url += ct;
                if (typeof ch_id != "undefined") {
                    url += "&u=" + ch_id;
                }
                jQuery("#main-view-mode-" + cnr[3] + "-" + type + "-list").mask("");
                jQuery("#main-view-mode-" + idnr + "-" + type + " span").addClass("spinner icon-spinner");
                jQuery("#main-view-mode-" + idnr + "-" + type + "-list").load(url, function() {
                    jQuery("#section-" + type + " .mview").hide();
                    jQuery("#main-view-mode-" + idnr + "-" + type + "-list").show();
                    jQuery("#main-view-mode-" + cnr[3] + "-" + type + "-list").unmask();
                    jQuery("#main-view-mode-" + idnr + "-" + type + " span").removeClass("spinner icon-spinner");
                    if (idnr == 2) {
                        $(".comment_h p,p.p-info,.full-details-holder p").linkify({
                            defaultProtocol: "https",
                            validate: {
                                email: function(value) {
                                    return false;
                                }
                            },
                            ignoreTags: [ "script", "style" ]
                        });
                    }
                    thumbFade();
                    setTimeout(function(){ mlazyload() }, 300);
                });
            }
        }
    }, ".main-view-mode");
    jQuery(document).on({
        click: function() {
            if (jQuery(this).hasClass("active")) {
                return;
            }
            var current = jQuery(".promo-view-mode.active").attr("id").slice(-1);
            var id = jQuery(this).attr("id").slice(-1);
            jQuery(".promo-view-mode").removeClass("active");
            jQuery("#promo-view-mode-" + id).addClass("active");
            if (jQuery("#promo-view-mode-" + id + "-list ul").length > 0) {
                jQuery(".pview").hide();
                jQuery("#promo-view-mode-" + id + "-list").show();
                ViewModeSizeSetFunctions();
                $.get(_rel + "?p=1&vm=" + id, function(data) {});
            } else {
                var ct = "";
                var url = _rel + "?p=1&m=" + id;
                if (typeof jQuery("#categories-accordion li a.selected").attr("rel-name") != "undefined") {
                    ct = "&c=" + jQuery("#categories-accordion li a.selected").attr("rel-name");
                }
                url += ct;
                if (typeof ch_id != "undefined") {
                    url += "&u=" + ch_id;
                }
                jQuery("#promo-view-mode-" + current + "-list").mask("");
                jQuery("#promo-view-mode-" + id + " span").addClass("spinner icon-spinner");
                jQuery("#promo-view-mode-" + id + "-list").load(url, function() {
                    jQuery(".pview").hide();
                    jQuery("#promo-view-mode-" + id + "-list").show();
                    jQuery("#promo-view-mode-" + current + "-list").unmask();
                    jQuery("#promo-view-mode-" + id + " span").removeClass("spinner icon-spinner");
                    thumbFade();
                    setTimeout(function(){ mlazyload() }, 300);
                    ViewModeSizeSetFunctions();
                });
            }
        }
    }, ".promo-view-mode");
});