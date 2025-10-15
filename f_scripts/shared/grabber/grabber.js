$(document).ready(function(){
	$(".assign-username").autocomplete({
		type: "post",
		serviceUrl: "?do=autocomplete",
		onSearchStart: function() {
			$(this).next().val("");
		},
		onSelect: function (suggestion) {
			$(this).next().val(suggestion.data);
		}
	});
	$('.icheck-box.ask input').each(function () {
                        var self = $(this);
                        label = self.parent().parent().find("label").first();
                        label_text = label.text();
                        label.detach();
                        self.iCheck({
                                checkboxClass: 'icheckbox_line-blue place-left',
                                radioClass: 'iradio_line-blue place-left',
                                insert: '<div class="icheck_line-icon"></div>' + label_text
                        });
               });

	$('.icheck-box.all input').each(function () {
                        var self = $(this);
                        label = self.parent().parent().find("label").first();
                        label_text = label.text();
                        label.detach();
                        self.iCheck({
                                checkboxClass: 'icheckbox_line-blue place-left',
                                radioClass: 'iradio_line-blue place-left',
                                insert: '<div class="icheck_line-icon"></div>' + label_text
                        });
               });

	$('.icheck-box.all input[name="select_entries_on"]').on('ifChecked', function(event){ $('.fetched input').iCheck('check'); $('.icheck-box.all input[name="select_entries_off"]').iCheck('uncheck'); });
	$('.icheck-box.all input[name="select_entries_off"]').on('ifChecked', function(event){ $('.fetched input').iCheck('uncheck'); $('.icheck-box.all input[name="select_entries_on"]').iCheck('uncheck'); });
});
$(document).ready(function() {
		var _id = "#left-side ";

		$(_id + '.responsive-accordion').each(function() {
			// Set Expand/Collapse Icons
				//$('.responsive-accordion-minus', this).hide();

			// Hide panels
				//$('.responsive-accordion-panel', this).hide();

			// Bind the click event handler
				$(_id + '.responsive-accordion-head').click(function(e) {
//				$(_id + '.responsive-accordion-head', this).click(function(e) {
					// Get elements
						var	thisAccordion = $(this).parent().parent(),
							thisHead = $(this),
							thisPlus = thisHead.find('.responsive-accordion-plus'),
							thisMinus = thisHead.find('.responsive-accordion-minus'),
							thisPanel = thisHead.siblings('.responsive-accordion-panel');

					// Reset all plus/mins symbols on all headers
						thisAccordion.find('.responsive-accordion-plus').show();
						thisAccordion.find('.responsive-accordion-minus').hide();

					// Reset all head/panels active statuses except for current
						thisAccordion.find('.responsive-accordion-head').not(this).removeClass('active');
						thisAccordion.find('.responsive-accordion-panel').not(this).removeClass('active').stop().slideUp('fast');

					// Toggle current head/panel active statuses
						if (thisHead.hasClass('active')) {
							thisHead.removeClass('active');
							thisPlus.show();
							thisMinus.hide();
							thisPanel.removeClass('active').stop().slideUp('fast', function(){thisresizeDelimiter();});
						} else {
							thisHead.addClass('active');
							thisPlus.hide();
							thisMinus.show();
							thisPanel.addClass('active').stop().slideDown('fast', function(){thisresizeDelimiter();});
						}
				});
		});
	});
$(function(){
SelectList.init("assign_category_file");
SelectList.init("assign_category_feed");
SelectList.init("assign_category_channel");
SelectList.init("assign_category_dm_type");
SelectList.init("assign_category_dm_video");
SelectList.init("assign_category_dm");
SelectList.init("assign_category_vimeo");
SelectList.init("feed_region_dm");
SelectList.init("feed_type_dm_video");
SelectList.init("feed_sort_dm_video");
SelectList.init("feed_results_dm_type");
SelectList.init("feed_results_dm");
SelectList.init("feed_results_vimeo");
SelectList.init("feed_categ");
SelectList.init("feed_channel_yt");
SelectList.init("feed_channel_dm");
SelectList.init("feed_channel_vimeo");
SelectList.init("feed_type_dm");
SelectList.init("feed_type_vimeo");
SelectList.init("feed_def");
SelectList.init("feed_dim");
SelectList.init("feed_dur");
SelectList.init("feed_emb");
SelectList.init("feed_syn");
SelectList.init("feed_type");
SelectList.init("feed_lic");
});

$(".list-video-feed, .list-channel-feed, .list-vimeo-feed, .list-dailymotion-feed, .list-dailymotion-feed-video, .list-metacafe-feed, .list-metacafe-feed-video, .find-video-file, .search-video-file").on("click", function(){
var form=""; var act="";
if($(this).hasClass("list-video-feed")){

form = "#video-feed-form"; act="list-feed";
if ($(form + " .current-page").html() != "##currentPageToken##"){
act+="&token="+$(form + " .current-token").html();
}

}
else if($(this).hasClass("list-channel-feed")){
form = "#channel-feed-form"; act="list-feed";

if ($(form + " .current-page").html() != "##currentPageToken##"){
act+="&token="+$(form + " .current-token").html();
}

}
else if($(this).hasClass("list-vimeo-feed")){form = "#vimeo-user-form"; act="vimeo-feed";}
else if($(this).hasClass("list-dailymotion-feed")){form = "#dailymotion-user-form"; act="dm-feed";}
else if($(this).hasClass("list-dailymotion-feed-video")){form = "#dailymotion-video-form"; act="dm-video";}
else if($(this).hasClass("list-metacafe-feed")){form = "#metacafe-user-form"; act="mc-feed";}
else if($(this).hasClass("list-metacafe-feed-video")){form = "#metacafe-video-form"; act="mc-video";}
else if($(this).hasClass("find-video-file")){form = "#embed-file-form"; act="video-find";}

$("#right-side").mask(" ");
$.post("?do="+act, $(form).serialize(),
function(data){
$("#right-side").html(data);
$("#right-side").unmask();
});
});
/* button actions, clone */
$(".list-video-feed-yt").click(function(){
form = "#" + $(".list-video-feed-hid").val(); act="list-feed";
$nt = $(form + " .next-token");
$pt = $(form + " .prev-token");
$ct = $(form + " .current-token");
if($nt.hasClass("active-token")){
act+="&token="+$nt.html();
}
if($pt.hasClass("active-token")){
act+="&token="+$pt.html();
}
if($ct.hasClass("active-token")){
act+="&token="+$ct.html();
}

$("#right-side").mask(" ");
$.post("?do="+act, $(form).serialize(),
function(data){
$("#right-side").html(data);
$("#right-side").unmask();

if (typeof($nt) != "undefined" || typeof($pt) != "undefined" || typeof($ct) != "undefined") {
if($nt.hasClass("active-token")){$(form + " .next-token").addClass("active-token");}
if($pt.hasClass("active-token")){$(form + " .prev-token").addClass("active-token");}
if($ct.hasClass("active-token")){$(form + " .current-token").addClass("active-token");}
}

});
});
/* button actions, import feeds */

$(document).on({
    click: function () {
    if($('.import-selected:checked').length == 0 && !$(this).hasClass("embed-video-file")) {
    	$(error_html).insertBefore("#right-side .entry-list ul");
    	return;
    }
    
var form=""; var act="";
if($(this).hasClass("import-video-feed")){
form = "#video-feed-form, #right-side-form"; act="import-yt-video";
if ($(form + " .current-page").html() != "##currentPageToken##") {
act+="&token="+$("#video-feed-form .current-token").html();
}

}
else if($(this).hasClass("import-channel-feed")){
form = "#channel-feed-form, #right-side-form"; act="import-yt-channel";
if ($(form + " .current-page").html() != "##currentPageToken##") {
act+="&token="+$("#channel-feed-form .current-token").html();
}

}
else if($(this).hasClass("import-vimeo-feed")){form = "#vimeo-user-form, #right-side-form"; act="import-vimeo-feed";}
else if($(this).hasClass("import-dailymotion-feed")){form = "#dailymotion-user-form, #right-side-form"; act="import-dm-user";}
else if($(this).hasClass("import-dailymotion-feed-video")){form = "#dailymotion-video-form, #right-side-form"; act="import-dm-video";}
else if($(this).hasClass("import-metacafe-feed")){form = "#metacafe-user-form, #right-side-form"; act="import-mc-user";}
else if($(this).hasClass("import-metacafe-feed-video")){form = "#metacafe-video-form, #right-side-form"; act="import-mc-video";}
else if($(this).hasClass("embed-video-file")){form = "#embed-file-form, #right-side-form"; act="video-embed";}

$("#right-side").mask(" ");
$.post("?do="+act, $(form).serialize(),
function(data){
$("#right-side").html(data);
$("#right-side").unmask();
});
}
}, ".import-video-feed, .import-channel-feed, .import-dailymotion-feed-video, .import-dailymotion-feed, .import-metacafe-feed-video, .import-metacafe-feed, .import-vimeo-feed, .embed-video-file");
/* open/close div */
$(".top-wrapper").click(function(){
	$(this).next().children().stop().slideToggle('fast', function(){resizeDelimiter();});
});
/* select link */
$('input[name="be_video_url"]').click(function(){$(this).select();});
