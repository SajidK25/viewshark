var SizeSetFunctions = function () {};
var ViewModeSizeSetFunctions = function () {};

jQuery(window).resize(function () {
});


jQuery(window).load(function() {
    jQuery(document).on({
	click: function () {
	    
            var ct = "";
            var id = jQuery("#main-content .content-current .main-view-mode.active").attr("id");
            var type = jQuery("#main-content .content-current .main-view-mode.active").val();
            var nr = id.split("-"); 
            var idnr = nr[3];
            var page = parseInt(jQuery(this).attr("rel-page"));
            
            var more = jQuery("#main-view-mode-" + idnr + "-" + type + "-more").parent();// just remove parent for infinite scroll
            more.detach();
            var more_clone = more.clone(true);

            var url = _rel + "?p=0&m="+idnr+"&sort="+type;

            if (typeof(jQuery("#categories-accordion li a.selected").attr("rel-name")) != "undefined") {
                ct = "&c=" + jQuery("#categories-accordion li a.selected").attr("rel-name");
            }
                
            url += ct;
            
            if (typeof(ch_id) != "undefined") {
                        url += "&u=" + ch_id;
            }

            url += "&page=" + page;
            jQuery("#main-view-mode-" + idnr + "-" + type + "-list ul").mask("");
            jQuery("#main-view-mode-" + idnr + "-" + type + "-list span.load-more.loadmask-img").show();
            jQuery("#main-view-mode-" + idnr + "-" + type + "-list span.load-more-text").hide();

            jQuery.get(url, function(result) {
                jQuery("#main-view-mode-" + idnr + "-" + type + "-list ul").append(result).unmask();
                jQuery("#main-view-mode-" + idnr + "-" + type + "-list span.load-more.loadmask-img").hide();

                more_clone.appendTo("#main-view-mode-" + idnr + "-" + type + "-list").find(".more-button").attr("rel-page", page + 1);
                thumbFade();
            });
	}
    }, ".more-button");
    
    

    
    
    jQuery(document).on({
	click: function () {
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
            
            if (jQuery("#main-view-mode-" + idnr + "-" + type + "-list ul").length > 0)  {
                jQuery("#section-" + type + " .mview").hide();
                jQuery("#main-view-mode-" + idnr + "-" + type + "-list").show();
            } else {
                var ct = "";
                var url = _rel + "?p=0&m="+idnr+"&sort="+type;
                
                if (typeof(jQuery("#categories-accordion li a.selected").attr("rel-name")) != "undefined") {
                    ct = "&c=" + jQuery("#categories-accordion li a.selected").attr("rel-name");
                }

                url += ct;
                
                if (typeof(ch_id) != "undefined") {
                        url += "&u=" + ch_id;
                }

                jQuery("#main-view-mode-" + cnr[3] + "-" + type + "-list").mask("");
                jQuery("#main-view-mode-" + idnr + "-" + type + " span").addClass("spinner icon-spinner");
                jQuery("#main-view-mode-" + idnr + "-" + type + "-list").load(url, function(){

                    jQuery("#section-" + type + " .mview").hide();
                    jQuery("#main-view-mode-" + idnr + "-" + type + "-list").show();
                    jQuery("#main-view-mode-" + cnr[3] + "-" + type + "-list").unmask();
                    jQuery("#main-view-mode-" + idnr + "-" + type + " span").removeClass("spinner icon-spinner");

                    thumbFade();
                });
            }
	}
    }, ".main-view-mode");
    
    

    jQuery(document).on({
	click: function () {
	    if (jQuery(this).hasClass("active")) {
		return;
	    }
	    
            var current = jQuery(".promo-view-mode.active").attr("id").slice(-1);
	    var id = jQuery(this).attr("id").slice(-1);

            jQuery(".promo-view-mode").removeClass("active");
            jQuery("#promo-view-mode-" + id).addClass("active");
            
            if (jQuery("#promo-view-mode-" + id + "-list ul").length > 0)  {
                jQuery('.pview').hide();
                jQuery("#promo-view-mode-" + id + "-list").show();
            } else {
                var ct = "";
                var url = _rel + "?p=1&m="+id;
                
                if (typeof(jQuery("#categories-accordion li a.selected").attr("rel-name")) != "undefined") {
                    ct = "&c=" + jQuery("#categories-accordion li a.selected").attr("rel-name");
                }
                
                url += ct;
                
                jQuery("#promo-view-mode-" + current + "-list").mask("");
                jQuery("#promo-view-mode-" + id + " span").addClass("spinner icon-spinner");
                
                jQuery("#promo-view-mode-" + id + "-list").load(url, function(){
                    jQuery('.pview').hide();
                    jQuery("#promo-view-mode-" + id + "-list").show();
                    jQuery("#promo-view-mode-" + current + "-list").unmask();
                    jQuery("#promo-view-mode-" + id + " span").removeClass("spinner icon-spinner");
                    
                    thumbFade();
                });
            }
	}
    }, ".promo-view-mode");

});