isChrome = !!window.chrome && !!window.chrome.webstore;

jQuery("#main_navbar ul.nav li").not(".active").mouseover(function () {
    jQuery(this).find("i").addClass("objblink");
}).mouseout(function () {
    jQuery(this).find("i").removeClass("objblink");
});

jQuery(".user-sub-activity").mouseover(function () {
    jQuery(this).addClass("on");
}).mouseout(function () {
    jQuery(this).removeClass("on");
});

$(function() {
        $( '#lang-menu' ).dlmenu({
                animationClasses : { classin : 'dl-animate-in-5', classout : 'dl-animate-out-5' }
        });
});
$(function() {
        $( '#user-nav-menu' ).dlmenu({
                animationClasses : { classin : 'dl-animate-in-5', classout : 'dl-animate-out-5' }
        });
});

function thumbFade() {
        $('.mediaThumb:not(.loaded)').Lazy({
                effect: 'fadeIn',
                effectTime: 300,
                threshold: 0,
                scrollDirection: 'both',
                afterLoad: function(element) {
                        element.removeAttr("height").addClass("loaded");
                }
        });
}

function getWidth() {
  if (self.innerWidth) {
    return self.innerWidth;
  }

  if (document.documentElement && document.documentElement.clientWidth) {
    return document.documentElement.clientWidth;
  }

  if (document.body) {
    return document.body.clientWidth;
  }
}

var dinamicSizeSetFunction_menu = function () {};
var dinamicSizeSetFunction_thumb = function (selector) {};
var dinamicSizeSetFunction_swiper = function () {};
var jqUpdate = function jqUpdateSize(selector) {};
function resizeDelimiter(){}
function thisresizeDelimiter(){}
var ms = "home";
function isOldSafari() {
    return !!navigator.userAgent.match(' Safari/') && !navigator.userAgent.match(' Chrome') && (!!navigator.userAgent.match(' Version/6.0') || !!navigator.userAgent.match(' Version/5.'));
}
jQuery(document).on({
        click: function () {
        	if (!$('#user-arrow-box').hasClass('hidden') || !$('#notifications-arrow-box').hasClass('hidden')) {
        		$('#ct-header-top .arrow_box').addClass('hidden');
        	}
        }
    }, ".container.container_wrapper, #ct-header-bottom, #logo_container, .search_holder, .push, footer");

jQuery(document).on({
        click: function() {
        	if ($('#user-arrow-box').hasClass('hidden')) {
        		$('#user-arrow-box').removeClass('hidden');
        		$('#notifications-arrow-box').addClass('hidden');
        	} else {
        		$('#user-arrow-box').addClass('hidden');
        	}
        }
}, ".own-profile-image.mt");

jQuery(document).on({
        click: function() {
        	if ($('#notifications-arrow-box').hasClass('hidden')) {
        		$('#notifications-arrow-box').removeClass('hidden');
        		$('#user-arrow-box').addClass('hidden');

        		if (typeof($('.user-sub-activity').html()) == 'undefined') {
        			$('#notifications-box').mask(" ");
        			$('#notifications-box').load(current_url + ms + '?load', function(){
        				$('.tooltip.top').detach();
        				$('#notifications-box-scroll-off').customScrollbar({ skin: "default-skin", hScroll: false, updateOnWindowResize: true, preventDefaultScroll: true });
        				$('#notifications-box').unmask();
        			});
        		}
        		$('.tooltip.top').detach();
        	} else {
        		$('#notifications-arrow-box').addClass('hidden');
        	}
        }
}, ".top-notif");

jQuery(document).on({
        click: function() {
        	t = $(this);
        	i = t.attr("rel-nr");

        	$('#a'+i).mask(" ");
        	$.post( current_url + ms + '?hide', { i: i }, function(data){
        		if (data == 1 && !$('.hidden-notifications').hasClass('active')) {
        			$('#a'+i).detach();
        		}
        		$('#a'+i).unmask();
        	});
        }
}, ".hide-entry");

jQuery(document).on({
        click: function() {
        	t = $(this);
        	i = t.attr("rel-nr");

        	$('#a'+i).mask(" ");
        	$.post( current_url + ms + '?unhide', { i: i }, function(data){
        		$('#a'+i).removeClass("is-hidden");
        		$('#a'+i).unmask();
        		if (data == 1) {
        			$('#a'+i+' i.unhide-entry').removeClass('unhide-entry').addClass('restored-entry').removeClass('icon-undo2').addClass('icon-check');
        		}
        	});
        }
}, ".unhide-entry");

jQuery(document).on({
        click: function() {
        	t = $(this);
        	i = t.attr("rel-page");
        	s = $('.hidden-notifications').hasClass("active") ? "?loadall" : "?load";

        	$('#notifications-box').mask(" ");
        	$.get(current_url + ms + s + "&p=" + i, function(data){
        		$('#notifications-box-list').append(data);
        		$('#notifications-box-scroll-off').customScrollbar("resize", true)
        		$('#notifications-box').unmask();
        		t.attr("rel-page", (parseInt(i)+1));
        		if (data == "") {
        			$('#more-results').detach();
        		}
        	});
        }
}, ".notifications-more");

jQuery(document).on({
        click: function() {
        	t = $(this);
        	s = !t.hasClass("active") ? "?loadall" : "?load";

        	$('#notifications-box-scroll').mask(" ");

        	$('#notifications-box').load(current_url + ms + s, function(){
        		$('.tooltip.top').detach();
        		$('#notifications-box-scroll-off').customScrollbar({ skin: "default-skin", hScroll: false, updateOnWindowResize: true, preventDefaultScroll: true });
        		$('#notifications-box-scroll').unmask();
        		
        		t.toggleClass('active');
        	});
        }
}, ".hidden-notifications");

jQuery(document).on({
        click: function() {
            if (getWidth() <= 800) {
                    m = getWidth() > 640 ? 310 : 155;
                    if (mobileCheck()) {
                        m = m - 15;
                    }
                    $(".search_holder").css({"max-width":(getWidth()-m)+"px", "-webkit-transition":"max-width 0.4s", "transition":"max-width 0.4s"});
                    $(".search_holder").addClass("expand");

            }
        }
}, ".sb-search-input");

jQuery(document).on({
        click: function() {
            if (getWidth() <= 800) {
                if ($(".search_holder").hasClass("expand")) {
                    $(".search_holder").removeAttr("style");
                }
            }
        }
}, "#wrapper > .container, #ct-header-bottom");

function mobileCheck() {
	var check = false;
	(function(a){if(/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino|android|ipad|playbook|silk/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))) check = true;})(navigator.userAgent||navigator.vendor||window.opera);

	return check;
}
