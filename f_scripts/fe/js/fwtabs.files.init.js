(function () {
    [].slice.call(document.querySelectorAll('.tabs')).forEach(function (el) {
        new CBPFWTabs(el);
    });
})();


function oldSafariCSSfix() {
    if (isOldSafari()) {
        var tabnr = jQuery(".tab-current").parent().parent().find("ul li").length;
        var width = jQuery('#siteContent').width();

        jQuery(".tab-current").parent().parent().find("ul li").width((width / tabnr) - 1).css("float", "left");
        jQuery(".tabs nav").css("width", (width + 1));
    }
}

(function () {
    jQuery(document).on({
	click: function () {
		var t = $(this);
		var section = jQuery(this).find("a").attr("href").split("-");
		var sort = section[1];
		var main = $("#file-menu-entry7").hasClass("menu-panel-entry-active") ? "comments" : "responses";
		var sid	 = $("#file-menu-entry7").hasClass("menu-panel-entry-active") ? "file-menu-entry7" : "file-menu-entry8";

		if (typeof(sort) == "undefined" || sort.substr(0, 4) == "resp") {
			return;
		}

		var view_mode_type = $(".view-mode-type.active").attr("id").replace("view-mode-", "");
		
		var url = _rel + "?s="+sid+"&do=cr-"+(sort == 'pending' ? 'suspended' : sort)+"&t="+view_mode_type;

		if (typeof($("#section-"+sort+"-"+view_mode_type+"-"+main).html()) == "undefined") {
			return;
		}
		if ($("#section-"+sort+"-"+view_mode_type+"-"+main).html().length > 0) {
			return;
		}

                        jQuery(".list-cr-tabs").mask("");
                        jQuery("#siteContent").load(url, function(){
                                jQuery(".list-cr-tabs").unmask();

                                if (sort != 'approved') {
                                	$(".cr-tabs .tab-current:first").removeClass("tab-current");
                                	$("#main-content").find("section.content-current:first").removeClass("content-current");
                                }
                                t.find("a").parent().addClass("tab-current");
                        });

	}
    }, ".tabs.list-cr-tabs ul:not(#pag-list) li:not(.cr-tabs)");
})();//file comments and responses tabs


(function () {
    jQuery(document).on({
	click: function () {
	    var view_mode_type = $(".view-mode-type.active").attr("id").replace("view-mode-", "");
            var section = jQuery(this).find("a").attr("href").split("-");
            var sort = section[1];

		if (typeof(sort) == "undefined") {
			return;
		}

            var sid = jQuery("#section-" + sort + "-"+view_mode_type+" .main-view-mode-"+view_mode_type+".active").attr("id");

            if (typeof(sid) == "undefined") {
            	return;
            }

            var id = sid.split("-");
            var idnr = id[3];



            var current_sort = jQuery("#section-" + sort + "-"+view_mode_type+" .main-view-mode-"+view_mode_type+".active").val();
            var type_all = current_sort + "-" + view_mode_type;

	    jQuery("#tab-"+view_mode_type).val(sort+"-"+view_mode_type);
	    
                if ($("#" + sid + "-list ul:not(.no-content)").length > 0) {
                	l1 = jQuery("#" + sid + "-list ul").length;
                	l2 = jQuery("#" + sid + "-list ul .thumbs-wrappers input:checked").length;

                	if (l1 == l2) {
                		$("#select-mode").addClass("active");
                	} else {
                		$("#select-mode").removeClass("active");
                	}
                	
                	if ($("#" + sid + "-list ul .thumbs-wrappers").css("display") == 'block') {
                		$("#edit-mode").addClass("active");
                	} else {
                		$("#edit-mode").removeClass("active");
                	}
                }

            if (jQuery("#" + sid + "-list ul:not(.no-content)").length == 0) {
                var ct = "";
                var url = _rel + "?s="+jQuery(".menu-panel-entry-active").attr("id")+"&p=0&m="+idnr+"&sort="+sort+"&t="+view_mode_type;
                
                if (jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry6" || $("#section-playlists").hasClass("active") || $("#ch-pl").hasClass("tab-current") || $("#ch-bl").hasClass("tab-current") || $("#wrapper").hasClass("tpl_search")) {//isplaylist
                	var m = idnr == 1 ? 4 : (idnr == 3 ? 6 : (idnr == 2 ? 5 : 4));
                	url = _rel + "?s="+jQuery(".menu-panel-entry-active").attr("id")+"&p=0&m="+m+"&sort="+sort+"&t="+view_mode_type;

                	if ($("#section-playlists").hasClass("active") || $("#ch-pl").hasClass("tab-current") || $("#wrapper").hasClass("tpl_search")) {//browse playlists section
                                url += "&pp=1";
                        }

                        if (typeof(ch_id) != "undefined") {
                        	url += "&u=" + ch_id;
                	}
                }
                
                if (typeof(jQuery("#categories-accordion li a.selected").attr("rel-name")) != "undefined") {
                    ct = "&c=" + jQuery("#categories-accordion li a.selected").attr("rel-name");
                }
                
                url += ct;

	    	if (typeof($("#sq").val()) != "undefined" && $("#sq").val().length > 3) {
                        url += "&sq=" + $("#sq").val();
                }
                
                $("#edit-mode, #select-mode").removeClass("active");

            	if (typeof(jQuery("#main-view-mode-" + idnr + "-" + type_all + "-list .no-content").html()) == "undefined") {
                	jQuery("#" + sid + "-list").mask("");
                	jQuery("#" + sid + " span").removeClass("icon-thumbs").addClass("spinner icon-spinner");
                	
                	jQuery("#" + sid + "-list").load(url, function(){
                        thumbFade();

                jQuery("#" + sid + " span").addClass("icon-thumbs").removeClass("spinner icon-spinner");
                	});
                }
            }
	}
    }, ".tabs ul:not(#pl-tabs):not(.pl-thumb):not(.fileThumbs):not(#pag-list):not(.cr-tabs):not(.no-content) li:not(li.cr-tabs)");
})();


jQuery(window).load(function () {
    oldSafariCSSfix();
});

jQuery(window).resize(function () {
    oldSafariCSSfix();
});
