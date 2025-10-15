$(document).ready(function() {
	$('#categories-accordion li').mouseout(function() {
		t = $(this);
		setTimeout(function() {
			if (!t.hasClass('menu-panel-entry-active')) {
				if (!t.find('a.active').hasClass('selected')) {
					t.find('a.active').removeClass('active');
				}
			}
		}, 1);
	});
    $(".menu-panel-entry").click(function(event){
	var current_id = $(this).attr("id");
	if ($(this).hasClass('main-menu-panel-entry-active') || current_id == 'account-menu-entry9' || current_id == 'account-menu-entry10' || current_id == 'account-menu-entry11' || current_id == 'account-menu-entry12' || current_id == 'account-menu-entry13') {
                return;
        }
	if (typeof($(this).attr("rel-m")) != "undefined") {
		menu_section = $(this).attr("rel-m");
	}

	if (typeof($(this).attr("rel-s")) != "undefined") {
		inner_section = $(this).attr("rel-s");
		
		if (typeof($("section.filter .viewType").html()) == "undefined" || $('#wrapper').hasClass('tpl_search') || $("#wrapper").hasClass("tpl_subs") || $('#wrapper').hasClass('tpl_channel') || $('#sub1-menu li').hasClass('menu-panel-entry-active')) {
			window.location = current_url + menu_section + "/" + inner_section;

			return false;
		}
	}
	
	if (typeof($(this).attr("rel-usr")) != "undefined") {
		usr = $(this).attr("rel-usr");

		if (typeof($("section.filter .viewType").html()) == "undefined" || $('#categories-accordion li').hasClass('menu-panel-entry-active')) {
			window.location = current_url + menu_section + "/" + usr;

			return false;
		}
	}
	
	if ($(this).hasClass("menu-panel-entry-active") && !$(".menu-panel-entry-sub").hasClass("menu-panel-entry-sub-active")) return false;

	if (!$("#wrapper").hasClass("tpl_files") && !$("#wrapper").hasClass("tpl_manage_channel") && !$("#wrapper").hasClass("tpl_account") && !$("#wrapper").hasClass("tpl_messages")) {
		$("a.dcjq-parent.active").removeClass("active");
		$(".menu-panel-entry-sub, .menu-panel-entry").removeClass("menu-panel-entry-sub-active");
		$(".menu-panel-entry, .pl-entry").removeClass("menu-panel-entry-active");
	} else {
		$("#categories-accordion a.dcjq-parent.active").removeClass("active");
		$(".menu-panel-entry-sub, #categories-accordion .menu-panel-entry").removeClass("menu-panel-entry-sub-active");
		$("#categories-accordion .menu-panel-entry, .pl-entry").removeClass("menu-panel-entry-active");
	}

	$(this).find("a:first").addClass("dcjq-parent active");
	$(this).addClass("menu-panel-entry-active");

	if(typeof(fe_mask) != 'undefined') {
	    if(typeof($("#file-sort-div-val").val()) != "undefined"){
		var fe_link = '&do='+$("#file-sort-div-val").val()+'&for='+($("#type-menu-val").val() != "sort-" ? $("#type-menu-val").val() : $("#file-type-div-val").val());
	    } else if (typeof(s_url) !== 'undefined') {
		if(s_url == 'channels'){
		    var fe_link = '&sort='+$(".sort-active").attr("id");
		}
		if(s_url == 'search'){
		    var fe_link = '&sort='+$(".sort-active").attr("id");
		}
	    } else var fe_link = '';
	    
    	    wrapLoad(current_url + menu_section + "?s="+current_id+fe_link, current_id);
	} else {
	    var u_url = '';
	    if(current_id == 'backend-menu-entry6' || current_id == 'backend-menu-entry11'){
		u_url = '&u='+$("#p-user-key").val();
	    }
	    wrapLoad(current_url + menu_section + "?s="+current_id+u_url);
	}
    });



    $(".menu-panel-entry-sub").on("click", function(event){
    	event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);

	var current_id = $(this).attr("id");
	var h2_title   = $("#"+current_id+" span.bold").text();

	if($(this).hasClass("menu-panel-entry-sub-active") && !$(".menu-panel-entry-sub").hasClass("menu-panel-entry-sub-active")) return false;
	
	if($(this).hasClass("menu-panel-entry-sub") && $(this).hasClass("menu-panel-entry-active")) return false;

	if (!$("#wrapper").hasClass("tpl_files") && !$("#wrapper").hasClass("tpl_manage_channel") && !$("#wrapper").hasClass("tpl_account") && !$("#wrapper").hasClass("tpl_messages")) {
		$("a.dcjq-parent.active").removeClass("active");
		$(".menu-panel-entry-sub, .menu-panel-entry").removeClass("menu-panel-entry-sub-active");
		$(".menu-panel-entry").removeClass("menu-panel-entry-active");
	} else {
		$("#categories-accordion a.dcjq-parent.active").removeClass("active");
		$(".menu-panel-entry-sub, #categories-accordion .menu-panel-entry").removeClass("menu-panel-entry-sub-active");
		$("#categories-accordion .menu-panel-entry").removeClass("menu-panel-entry-active");
	}

	$(this).addClass("menu-panel-entry-active");
	
	view_mode_type = $(".view-mode-type.active").attr("id").replace("view-mode-", "");
	id = jQuery(".content-current .main-view-mode-"+view_mode_type+".active").attr("id");
	type = jQuery(".content-current .main-view-mode-"+view_mode_type+".active").val();
	type_all = type + "-" + view_mode_type;
	nr = id.split("-");
	idnr = nr[3];
	m = idnr == 1 ? 4 : (idnr == 3 ? 6 : 4);

	c_url = current_url + menu_section + "?s="+current_id;
	c_url+= "&p=0&sort="+type+"&t="+view_mode_type;
	
	if ($("#section-playlists").hasClass("active") || $("#ch-pl").hasClass("tab-current")) {
		c_url+= "&pp=1";
		
		if ($("#ch-pl").hasClass("tab-current")) {
			c_url+= "&ch";
		}
	}

	wrapLoad(c_url, current_id);
    });
});
function wrapLoad(the_url, trg) {
	if (typeof(s_url) != 'undefined' && s_url == "uactivity" && typeof($("#uact-id").val()) != "undefined") { var id_trg = ".fancybox-inner"; }
	
	else {
		if ($("#channel-tabs #section-playlists").hasClass("content-current")) {
			var id_trg = "#channel-tabs > .content-wrap";
		} else if (typeof($(".filter-link").html()) !== "undefined") {
			var id_trg = "#ct-wrapper #section-playlists";
		} else {
			var id_trg = "#siteContent";
		}
	}
	
    if (typeof(trg) !== 'undefined' && trg == 'page') {
    	id_trg = p_str;
    	
    	if ($(".content-current div[id=paging-bottom]").length > 1) {
    		$(".content-current #paging-bottom:last").detach();
    	}
    }

    $(id_trg).mask("");
    $(id_trg).load(the_url, function() {
    	if (typeof($(".btn-group.viewType").html()) != "undefined" && typeof($("section.filter").html()) != "undefined") {
    		setTimeout(function () {
                        ViewModeSizeSetFunctions();
                }, 500);
                setTimeout(function () {
                        thumbFade();
                }, 100);
        }
	$(id_trg).unmask();
	if ($("body").hasClass("media-width-480") || $("body").hasClass("media-width-320")) {
		smoothscrolltop();
	}
	if($(window).width()<=768 && $(".sidebar-container").is(":visible")){$(".sidebar-container").hide("fast")}
	if (typeof linkify != "undefined") {$(".comment_h p,p.p-info,pre.hp-pre,.full-details-holder p,.msg-body pre").linkify({defaultProtocol:"https",validate:{email:function(value){return false}},ignoreTags:["script","style"]})}
    });
}
function smoothscrolltop(){
    var currentScroll = document.documentElement.scrollTop || document.body.scrollTop;
    if (currentScroll > 0) {
         window.requestAnimationFrame(smoothscrolltop);
         window.scrollTo (0,currentScroll - (currentScroll/5));
    }
}
function postLoad(the_url, the_form, trg) {
    var id_trg = "#siteContent";
    
    $(id_trg).mask("");
    $.post(the_url, $(the_form).serialize(), function(data) {
	$(id_trg).html(data);
	$(id_trg).unmask();
    });
}
function mobilecheck() {
	var check = false;
	(function(a){if(/(android|ipad|playbook|silk|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|yas\-|your|zeto|zte\-/i.test(a.substr(0,4)))check = true})(navigator.userAgent||navigator.vendor||window.opera);
	return check;
}
function enterSubmit(input_name, button_name) {
    $(input_name).bind("keydown", function(e) {
        if (e.keyCode == 13) {
            $(button_name).click();
            return false;
        }
    });
}
function display_c(){
	return;
	var refresh=1000; // Refresh rate in milli seconds
	mytime=setTimeout('display_ct()',refresh)
}

function display_ct() {
  return;
  var currentTime = new Date();
  var currentHours = currentTime.getHours();
  var currentMinutes = currentTime.getMinutes();
  var currentSeconds = currentTime.getSeconds();
  var currentYear = currentTime.getYear();
  var currentMonth = currentTime.getMonth();
  var currentDate = currentTime.getDate();
  // Pad the minutes and seconds with leading zeros, if required
  currentMinutes = ( currentMinutes < 10 ? "0" : "" ) + currentMinutes;
  currentSeconds = ( currentSeconds < 10 ? "0" : "" ) + currentSeconds;
  // Choose either "AM" or "PM" as appropriate
  var timeOfDay = ( currentHours < 12 ) ? "AM" : "PM";
  // Convert the hours component to 12-hour format if needed
  currentHours = ( currentHours > 12 ) ? currentHours - 12 : currentHours;
  // Convert an hours component of "0" to "12"
  currentHours = ( currentHours == 0 ) ? 12 : currentHours;
  // Convert the year
  var thisYear = currentYear + 1900;
  // Convert the month
  var thisMonth = currentMonth + 1;
  // Compose the string for display
  var currentTimeString = thisYear + "/" + thisMonth + "/" + currentDate + " " + currentHours + ":" + currentMinutes + ":" + currentSeconds + " " + timeOfDay;
  // Update the time display
  document.getElementById("clock").firstChild.nodeValue = currentTimeString;

  tt=display_c();
}