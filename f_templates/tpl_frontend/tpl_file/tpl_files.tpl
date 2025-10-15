	{assign var=c_section value="{href_entry key="files"}"}
	<script type="text/javascript">var current_url='{$main_url}/';var menu_section='{$c_section}';var fe_mask='on';function thisresizeDelimiter(){ldelim}{rdelim}</script>
        {insert name="getCurrentSection" assign=s}
        {if $s eq "file-menu-entry6" or $smarty.post.do_reload eq "1"}
        	{generate_html type="playlist_layout" bullet_id="ct-bullet1" entry_id="ct-entry-details1" section="files" bb="1"}
        	<script type="text/javascript">
                                $(document).ready(function() {ldelim}
                                    var lb_url = current_url + menu_section + '?s=file-menu-entry6&m=1&a=pl-add';
                                    $(".link").click(function(){ldelim} $.fancybox.close(); {rdelim});
                                    $(document).on("click","#add-new-pl-btn",function(e) {ldelim}
                                    		e.stopImmediatePropagation();
                                                $("#add-new-label-in").mask(" ");
                                                $.post(lb_url, $("#add-new-label-form").serialize(), function(data){ldelim}
                                                        $("#add-new-label-response").html(data);
                                                        $("#add-new-label-in").unmask();
                                                {rdelim});
                                    {rdelim});
                                    //enterSubmit("#add-new-label-form input", "#add-new-pl-btn");
                                {rdelim});
                            </script>
        {elseif $s eq "file-menu-entry7" or $s eq "file-menu-entry8"}
        	{if $s eq "file-menu-entry7"}
        		{generate_html type="file_comments_layout" bullet_id="ct-bullet1" entry_id="ct-entry-details1" section="files" bb="1"}
        	{else}
        		{generate_html type="file_responses_layout" bullet_id="ct-bullet1" entry_id="ct-entry-details1" section="files" bb="1"}
        	{/if}
        	{if $smarty.get.s eq ""}
        	<script type="text/javascript">$(function(){ldelim}$('#entry-action-buttons').dlmenu({ldelim}animationClasses:{ldelim}classin:'dl-animate-in-5',classout:'dl-animate-out-5'{rdelim}{rdelim});{rdelim});</script>
        	{/if}
        	<script type="text/javascript">
//        		if (typeof CBPFWTabs == "undefined") {ldelim}
//        			var script = document.createElement('script');
//        			script.setAttribute('src','{$javascript_url}/fwtabs.js');
//        			$("#siteContent").prepend(script);
//        		{rdelim}
//        		(function(){ldelim}[].slice.call(document.querySelectorAll('.tabs:not(#t-nav)')).forEach(function(el){ldelim}new CBPFWTabs(el);{rdelim});{rdelim})();
        		{literal}
                                  $(document).ready(function(){
                                  $(".list-cr-tabs .mp-sort-by li").on("click", function(){
                                        event.preventDefault();
                                        event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);
                                        var _t = $(this);
                                        var t=_t.parent();
                                        var f=0;
                                        var h=_t.find("a").attr("href").replace("#section-", "");
                                        //var vm=$(".view-mode-type.active").attr("id").replace("view-mode-", "");
                                        $(".list-cr-tabs .mp-sort-by li").each(function(){
                                                var tt=$(this);
                                                if(tt.hasClass("iss")) {
                                                        f+=1;
                                                }
                                        });
                                        if (f == 0) {
                                                t.addClass("issm");
                                                $("body").addClass("hissm");
                                                t.find("li").addClass("iss").stop().slideDown("fast");
                                        } else {
                                                t.find("li").removeClass("iss").stop().slideUp("fast");
                                                t.removeClass("issm");
                                                $("body").removeClass("hissm");
                                                $(".loadmask, .loadmask-msg").detach();
                                                t.find("li:first-of-type").stop().slideDown(10, function(){
                                                        var tc=$("#main-content li.tab-current a").attr("href").replace("#section-", "");

                                                        if (h !== tc) {
                                                        	//$(".list-cr-tabs section").removeClass("content-current");
                                                        	//$("#section-"+h).addClass("content-current");

                                                                $("#main-content.content-wrap>nav").find("a[href=#section-"+h+"]").parent().click();
                                                        }
                                                });

                                                $(".list-cr-tabs .mp-sort-by li").each(function(){
                                                        var t=$(this);
                                                        t.prepend(t.find("a[href=#section-"+h+"]").parent());
                                                });
                                        }
                                        });
                                });
                                jQuery(document).on({
                                  click: function(event) {
                                        //var vm=$(".view-mode-type.active").attr("id").replace("view-mode-", "");
                                        var t = $(".list-cr-tabs .mp-sort-by");
                                        //var t = $("#"+vm+"-content .content-current .mp-sort-by");

                                        t.find("li").removeClass("iss").stop().slideUp("fast");
                                        t.removeClass("issm");
                                        $("body").removeClass("hissm");
                                        t.find("li:first-of-type").stop().slideDown(10);
                                }}, "body.hissm");

{/literal}
        	</script>
        {else}
        	{generate_html type="files_layout" bullet_id="ct-bullet1" entry_id="ct-entry-details1" section="files" bb="1"}

        	{if $smarty.get.s ne ""}
        	<script type="text/javascript">
        		(function() {ldelim}[].slice.call(document.querySelectorAll('.tabs')).forEach(function (el) {ldelim}new CBPFWTabs(el);{rdelim});{rdelim})();
        		$(function() {ldelim}$('#entry-action-buttons').dlmenu({ldelim}animationClasses : {ldelim} classin : 'dl-animate-in-5', classout : 'dl-animate-out-5' {rdelim} {rdelim}); {rdelim});
        		SizeSetFunctions();
        	</script>
        	{/if}
        	<script type="text/javascript">
        		$(document).ready(function() {ldelim}
        			const listItems = document.querySelectorAll('#entry-action-buttons ul.dl-menu > li:not(.hidden)');
        			const lastNonHiddenItem = listItems[listItems.length - 1];
        			lastNonHiddenItem.classList.add('this-last');
        			$(".file-action").click(function() {ldelim}
        				var paging_link = '';
        				{if $smarty.get.page ne ""}paging_link = '&page={$smarty.get.page|sanitize}&ipp={$smarty.get.ipp|sanitize}';{/if}
        				var t = $(this);
        				var a = t.attr("id");
        				view_mode_type = $(".view-mode-type.active").attr("id").replace("view-mode-", "");
        				id = jQuery(".content-current .main-view-mode-"+view_mode_type+".active").attr("id");
        				type = jQuery(".content-current .main-view-mode-"+view_mode_type+".active").val();
        				type_all = type + "-" + view_mode_type;
        				nr = id.split("-");
        				idnr = nr[3];
        				c_url = current_url + menu_section + '?s=' + $(".menu-panel-entry-active").attr("id");
        				c_url+= "&p=0&m="+idnr+"&sort="+type+"&t="+view_mode_type;
        				p_str = "#main-view-mode-" + idnr + "-" + type_all + "-list ul:not(.playlist-entries):not(#pag-list)";
        				var page = parseInt(jQuery("#main-view-mode-" + idnr + "-" + type_all + "-list .pag-wrap a.current").html());
        				$("#main-view-mode-" + idnr + "-" + type_all + "-list #pag-list").detach();
        				if (page > 1) {ldelim}
        					paging_link = "&page=" + page;
        				{rdelim}
        				if ($("#sq").val().length > 3) {ldelim}
        					c_url += "&sq=" + $("#sq").val();
                        		{rdelim}
        				var post_url = c_url + "&a=" + a + paging_link;
        				$('#cb-response').replaceWith(''); $('#cb-response-wrap').replaceWith('');
        				$("#siteContent").mask(" ");
        				$("#entry-action-buttons .dl-trigger").click();
        				var searchIDs = [];
        				$("#main-view-mode-" + idnr + "-" + type_all + "-list input:checkbox:checked").map(function(){ldelim} searchIDs.push($(this).val()); {rdelim});
        				$.post(post_url, { 'fileid[]': searchIDs }, function(data) {ldelim}
        				 	jQuery(p_str).replaceWith(data);
        				 	$("#cb-response-wrap").insertBefore("#view-type-content");
        				 	//more_clone.appendTo("#main-view-mode-" + idnr + "-" + type_all + "-list").find(".more-button").attr("rel-page", page + 1);
        				 	$("#siteContent").unmask();
        				 	if ($("div[id=paging-bottom]").length > 1) {ldelim}
        				 		$(".content-current #paging-bottom:last").detach();
        				 	{rdelim}
        				 	$("#edit-mode, #select-mode").removeClass("active");
        				 	setTimeout(function () {ldelim}
        				 		ViewModeSizeSetFunctions();
        				 	{rdelim}, 300);
        				 	setTimeout(function () {ldelim}
        				 		thumbFade();
        				 	{rdelim}, 100);
        				 {rdelim});
        			{rdelim});
        		{rdelim});
        	</script>
        {/if}

        <script type="text/javascript">
        {if $smarty.get.s eq ""}
        {literal}
        jQuery(document).on({
          click: function(event) {
                event.preventDefault();
                event.stopPropagation ? event.stopPropagation() : (event.cancelBubble=true);
                var _t = $(this);
                var t=_t.parent();
                var f=0;
                var h=_t.find("a").attr("href").replace("#section-", "");
                var vm=$(".view-mode-type.active").attr("id").replace("view-mode-", "");
                $("#"+vm+"-content .content-current .mp-sort-by li").each(function(){
                        var tt=$(this);
                        if(tt.hasClass("iss")) {
                                f+=1;
                        }
                });
                if (f == 0) {
                        t.addClass("issm");
                        $("body").addClass("hissm");
                        t.find("li").addClass("iss").stop().slideDown("fast");
                } else {
                        t.find("li").removeClass("iss").stop().slideUp("fast");
                        t.removeClass("issm");
                        $("body").removeClass("hissm");
                        $(".loadmask, .loadmask-msg").detach();
                        t.find("li:first-of-type").stop().slideDown(10, function(){
                                var tc=$("#main-content li.tab-current a").attr("href").replace("#section-", "");
                                if (h !== tc) {
                                	$("#"+vm+"-content section").removeClass("content-current");
                                	$("#section-"+h).addClass("content-current");

                                        $("#main-content.tabs>nav").find("a[href=#section-"+h+"]").parent().click();
                                } else if (h.startsWith('plpublic')) {
                                	$("#"+vm+"-content section").removeClass("content-current");
                                	$("#section-"+h).addClass("content-current");
                                }
                        });
                        $("#"+vm+"-content .mp-sort-by").each(function(){
                                var t=$(this);
                                t.prepend(t.find("a[href=#section-"+h+"]").parent());
                        });
                }
}}, ".content-current .mp-sort-by li");
                jQuery(document).on({
                  click: function(event) {
                        var vm=$(".view-mode-type.active").attr("id").replace("view-mode-", "");
                        var t = $("#"+vm+"-content .content-current .mp-sort-by");
                        t.find("li").removeClass("iss").stop().slideUp("fast");
                        t.removeClass("issm");
                        $("body").removeClass("hissm");
                        t.find("li:first-of-type").stop().slideDown(10);
                }}, "body.hissm");
{/literal}
{/if}
	$('.tpl_files #t-nav nav>ul>li>a').on('click',function(e){ldelim}e.preventDefault();window.location=$(this).attr("href");return false;{rdelim});
	{if $s ne "file-menu-entry1" and $s ne ""}$(document).ready(function(){ldelim}$("#t-nav li:first").removeClass("tab-current");{rdelim});{/if}
                         </script>
{if !$smarty.get or $s|strpos:"file-menu-entry6"!== false}
{insert name="swiperJS" for="tnav"}
{if $video_module}{insert name="swiperJS" for="video"}{/if}
{if $short_module}{insert name="swiperJS" for="short"}{/if}
{if $live_module}{insert name="swiperJS" for="live"}{/if}
{if $image_module}{insert name="swiperJS" for="image"}{/if}
{if $audio_module}{insert name="swiperJS" for="audio"}{/if}
{if $document_module}{insert name="swiperJS" for="doc"}{/if}
{if $blog_module}{insert name="swiperJS" for="blog"}{/if}
{/if}