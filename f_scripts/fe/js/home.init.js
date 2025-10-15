var dinamicSizeSetFunction_view = function () {};
function sizeInit(type) {}

jQuery(window).load(function () {
	thumbFade();
});

function oldSafariCSSfix() {
    if (isOldSafari()) {
        var tabnr = $(".full_width .tabs nav ul li").length;
        var width = jQuery('.container:first').width() - 32;

        jQuery(".tabs nav ul li").width((width / tabnr) - 1).css("float", "left");
        jQuery(".tabs nav").css("width", (width + 1));
    }
}

jQuery(window).load(function () {
    oldSafariCSSfix();
    
    $('.recommended_section ul.fileThumbs li').each(function() {
    	if ($(this).is(':hidden')) {
    		$(this).detach();
    	}
    });
});

jQuery(window).resize(function () {
    oldSafariCSSfix();
});

jQuery(document).ready(function () {

var owl = $('.view-list:not(.owl-loaded)');
/*
owl.mouseover(function(){
	$(this).addClass('view-on');
}).mouseout(function(){
	$(this).removeClass('view-on');
});
*/
function toggleArrows(t){
                if(t.find(".owl-item").last().hasClass('active') && 
                   t.find(".owl-item.active").index() == t.find(".owl-item").first().index()){
                    t.find('.owl-nav .owl-next').addClass("off");
                    t.find('.owl-nav .owl-prev').addClass("off");
                }
                //disable next
                else if(t.find(".owl-item").last().hasClass('active')){
                    t.find('.owl-nav .owl-next').addClass("off");
                    t.find('.owl-nav .owl-prev').removeClass("off");
                }
                //disable previus
                else if(t.find(".owl-item.active").index() == t.find(".owl-item").first().index()) {
                    t.find('.owl-nav .owl-next').removeClass("off");
                    t.find('.owl-nav .owl-prev').addClass("off");
                }
                else{
                    t.find('.owl-nav .owl-next,.owl-nav .owl-prev').removeClass("off");
                }
            }

owl.each(function() {
	t = $(this);
	//turn off buttons if last or first - after change
	to = 360;
	owl.on('initialized.owl.carousel', function (event) { t = $(this);toggleArrows(t); });
	owl.on('translated.owl.carousel', function (event) { t = $(this);toggleArrows(t); });
	owl.on('translate.owl.carousel', function (event) { setTimeout(function(){thumbFade()}, to) });
});

owlinit(owl);

});


function owlinit(owl) {
if (typeof (owl.html()) !== "undefined" && !owl.hasClass("owl-loaded")) {
	var hh = document.body.offsetWidth;
	var it = 5;
	if (mobileCheck()) {
		it = hh == 360 ? 1 : (hh == 640 ? 3 : it);
	}
        owl.owlCarousel({
                items: 6,
                loop: false,
                margin: 20,
                navSpeed: 100,
                autoplay: false,
                dots: true,
                scrollPerPage: true,
                nav: true,
                navText: [
                "<i class='iconBe iconBe-chevron-left'></i>",
                "<i class='iconBe iconBe-chevron-right'></i>"
                ],
                onInitialized: false,
                responsive: {
                0: { items: 1, slideBy: 1 },
                401: { items: 2, slideBy: 2 },
                560: { items: 3, slideBy: 3 },
                768: { items: 3, slideBy: 3 },
                769: { items: 4, slideBy: 4 },
                981: { items: 5, slideBy: 5 },
                1200: { items: it, slideBy: it },
                1564: { items: 6, slideBy: 6 }
                }
        });
        $(owl).addClass("view-on");
        owl.on('changed.owl.carousel', function (event) {
        	if (event.item.count - event.page.size == event.item.index) {
        		$(event.target).find('.owl-dots div:last').addClass('active').siblings().removeClass('active');
        	}
        });
        owl.on('translated.owl.carousel',function(){setTimeout(function(){thumbFade()},200)});
        }
}


    jQuery(document).on({
        click: function () {
            var file_key = jQuery(this).attr("rel-key");
            var file_type = jQuery(this).attr("rel-type");
            var url = _rel + "?a=cb-watchadd&for=sort-" + file_type;
            var _this = jQuery(this);
            
            if (_this.find((jslang["lss"] == "1" ? "icon-check" : "icon-warning")).hasClass("icon-check")) {
                return;
            }
            
            _this.parent().next().mask("");
            _this.next().text(jslang["loading"]);
  
            jQuery.post(url, {"fileid[0]": file_key}, function(result) {
                _this.find(".icon-clock").removeClass("icon-clock").addClass((jslang["lss"] == "1" ? "icon-check" : "icon-warning"));
                _this.next().text((jslang["lss"] == "1" ? jslang["inwatchlist"] : jslang["nowatchlist"]));
                _this.parent().next().unmask();
            });
        }
    }, ".watch_later_wrap");

    jQuery(document).on({
        click: function() {
        	t = $(this);
        	w = getWidth();
        	n1 = 24;
        	n2 = 12;

        	if (w <= 1563) {
        		n1 = 20;
        		n2 = 10;
        	}
        	if (w <= 980) {
        		n1 = 16;
        		n2 = 8;
        	}
        	if (w <= 768) {
        		n1 = 12;
        		n2 = 6;
        	}
        	if (w <= 560) {
        		n1 = 8;
        		n2 = 4;
        	}
        	t.hide();

        	if (typeof($('.more-loaded').html()) != "undefined") {
        		$('.more-loaded').show();
        		t.removeClass('more').addClass('less').html(jslang["showless"]).show();
        		return;
        	}

        	$('.more-load').css({'margin-top': '-17px', 'margin-right': '10px'}).mask(' ');

        	$.get(current_url + "home?rc&rn="+n2, function(data){
        		$(data).insertAfter('#main-view-mode-1-featured-video ul.fileThumbs').addClass('more-loaded');

       			t.removeClass('more').addClass('less').html(jslang["showless"]).show();
       			$('.more-load').unmask();
       			thumbFade();
        	});
        }
    }, ".more-recommended a.more");

    jQuery(document).on({
        click: function() {
        	t = $(this);
        	w = getWidth();
        	n1 = 12;
        	n2 = 24;
        	
        	if (w <= 1563) {
        		n1 = 10;
        		n2 = 20;
        	}
        	if (w <= 980) {
        		n1 = 8;
        		n2 = 16;
        	}
        	if (w <= 768) {
        		n1 = 6;
        		n2 = 12;
        	}
        	if (w <= 560) {
        		n1 = 4;
        		n2 = 8;
        	}

		$('.more-loaded').hide();
		t.removeClass('less').addClass('more').html(jslang["showmore"]);
        }
    }, ".more-recommended a.less");

