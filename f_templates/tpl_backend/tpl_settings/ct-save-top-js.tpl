		<script type="text/javascript">
		    var paging_link = '';
		    var do_link = '';
		    if ($(".menu-panel-entry-sub.menu-panel-entry-sub-active").attr("rel-m")) {
		    	menu_section = $(".menu-panel-entry-sub.menu-panel-entry-sub-active").attr("rel-m");
		    }
		    if ($(".menu-panel-entry.menu-panel-entry-active").attr("rel-m")) {
		    	menu_section = $(".menu-panel-entry.menu-panel-entry-active").attr("rel-m");
		    }
        	    {if $smarty.get.page ne ""}paging_link = '&page={$smarty.get.page|sanitize}&ipp={$smarty.get.ipp|sanitize}';{/if}
        	    {if $page_display eq "backend_tpl_files"}do_link = '{if $smarty.get.u ne ""}&u={$smarty.get.u|sanitize}{/if}{if $smarty.get.do ne ""}&do={$smarty.get.do|sanitize}{/if}{if $smarty.get.for ne ""}&for={$smarty.get.for|sanitize}{/if}';{/if}
		    var upd_url = current_url + menu_section + '?s={$smarty.get.s|sanitize}&{if $page_display eq "backend_tpl_files"}f{/if}do=update'+paging_link+do_link;
		    var add_url = current_url + menu_section + '?s={$smarty.get.s|sanitize}&do=add';
		    {if $page_display eq "tpl_browse"}
		    {else}
		    var c_url   = current_url + menu_section{if $page_display ne "tpl_playlist"}+'?s={if $page_display eq "tpl_subs"}'+$(".menu-panel-entry-active").attr("id")+'{else}{if $smarty.get.s ne ""}{$smarty.get.s|sanitize}{else}{$s}{/if}{/if}'{/if};
		    {/if}
		    var act_link = '';
		    var cr_section = 0;
		    $(function() {ldelim}
		    	$( '#entry-action-buttons' ).dlmenu({ldelim}
		    		animationClasses : {ldelim} classin : 'dl-animate-in-5', classout : 'dl-animate-out-5' {rdelim}
		    	{rdelim});
		    {rdelim});

		    $(document).ready(function() {ldelim}
			$("#entry-actions, #sort-file-time, button[id^='thumb-actions-']").click(function(){ldelim} $("#add-new-label-form a.link").click(); {rdelim});
		    {if $check_all eq "1" or $page_display eq "tpl_files" or $page_display eq "tpl_view" or $page_display eq "tpl_respond_extra" or $page_display eq "tpl_browse" or $page_display eq "tpl_search"}
			$("#check-all").on("click", function () {ldelim}
			    if ($("#check-all").is(':checked')) {ldelim}
				$('input[name="entryid"]').iCheck('check');
			    {if $page_display eq "tpl_files" or $page_display eq "tpl_subs"}
				$(".list-check").parent().parent().removeClass("file-selected");
			    {/if}
			    {rdelim} else {ldelim}
				$('input[name="entryid"]').iCheck('uncheck');
			    {if $page_display eq "tpl_files" or $page_display eq "tpl_subs"}
				$(".list-check").parent().parent().addClass("file-selected");
			    {/if}
			    {rdelim}
			    {if $page_display eq "tpl_messages"}
				var c_sel = $("input.ct-entry-check:checked").length;
				if(c_sel > 0){ldelim}
				    $("input.ct-entry-check:checked").each(function() {ldelim}
					$("#ec-"+$(this).val()).removeClass("no-display");
				    {rdelim});
				    $("#ct-no-details").addClass("no-display");
				{rdelim} else {ldelim}
				    $("#ct-contact-details-wrap>div").addClass("no-display");
				    $("#ct-no-details").removeClass("no-display");
				    $(".ct-entries").removeClass("ct-entry-bg");
				{rdelim}
				$("#ct-header-count").html(c_sel);
			    {/if}
			    {if $mm_entry eq "message-menu-entry7"}
				$(".link").click();
			    {/if}
			    thisresizeDelimiter();
			{rdelim});

		    	$('input#check-all').on('ifChecked', function(event) {ldelim} $('input[name="entryid"]').iCheck('check'); {rdelim});
                        $('input#check-all').on('ifUnchecked', function(event) {ldelim} $('input[name="entryid"]').iCheck('uncheck'); {rdelim});

		    {if $page_display eq "tpl_messages" or $page_display eq "tpl_files" or $page_display eq "tpl_playlist" or $page_display eq "tpl_browse" or $page_display eq "tpl_search"}
			$(".count-label").bind("click", function () {ldelim}
			    var class_ar = $(this).attr("class").split(" ");
			    var label_ac = class_ar[1];
			    var class_id = $(this).attr("id");
			    var form_id = $("#"+class_id).find("form").attr("id");
			    var f1 = $(".entry-form-class").serialize();
			    var f2 = $("#"+form_id).serialize();
			    {if $mm_entry eq "message-menu-entry7"}var f3 = $("#ct-entry-selection").serialize();{/if}
			    var the_form = {if $mm_entry eq "message-menu-entry7"}f2+"&"+f3;{else}f1+"&"+f2;{/if}

			    $("#siteContent").mask(" ");

			    $.post(current_url + menu_section + '?s={$smarty.get.s|sanitize}&do='+label_ac+paging_link, the_form, function(data) {ldelim}
			    	{if $page_display eq "tpl_messages"}
					$("#siteContent").html(data);
				{else}
					$(".container").html(data);
				{/if}
				$("#siteContent").unmask();
			    {rdelim});
			{rdelim});
			$(".file-action").bind("click", function () {ldelim}
			    act_link = "&a="+$(this).attr("id");
			{rdelim});
		    {/if}
			$(".ftype").on("click", function() {ldelim}
			    if($(this).hasClass("sort-active")) return;
			    act_link = $(this).hasClass("sort-menu") ? "" : act_link;
			    $("#file-type-div-val").val($(this).attr("id"));
			    var post_url = c_url + "&do=" + $(".sort-active").attr("id") + "&for=" + $("#file-type-div-val").val() + act_link + paging_link;
			    {if $mm_entry eq "message-menu-entry7"}var f_class = '#ct-entry-selection';
                            {elseif $mm_entry eq "file-menu-entry7"}var f_class = '#gen-file-actions';
                            {else}var f_class = '.entry-form-class';{/if}
                            $(".type-menu").removeClass("sort-active");
                            $(this).addClass("sort-active");

			    $(".container").mask(" ");
			    $.post(post_url, $(f_class).serialize(), function(data) {ldelim}
				$(".container").html(data);
                                $(".container").unmask();
			    {rdelim});
			{rdelim});

			$(".count").unbind().on("click", function() {ldelim}
			    var act_id = $(this).attr("id");
			    if($(this).hasClass("sort-active")) return;
			    if($(this).hasClass("menu-action-src")){ldelim}
                                $("#file-type-div-val").val(act_id);
                            {rdelim}
                            if($(this).hasClass("menu-action-type")){ldelim}
                                $("#file-sort-div-val").val(act_id);
                            {rdelim}
			    var fr_inv = '{$approve_friends}';
			    var msk_in = (act_id == "cb-addfr" && fr_inv == 1) ? "{lang_entry key="contacts.add.wait"}" : " ";
			    if ((act_id == 'cb-delete' || act_id == 'cb-del-files' || act_id == 'cb-commdel' || act_id == 'cb-rdel') && confirm('{lang_entry key="notif.confirm.delete.multi"}') || (act_id != '' && act_id != 'cb-delete' && act_id != 'cb-del-files' && act_id != 'cb-commdel' && act_id != 'cb-rdel')) {ldelim}
			    {if $mm_entry eq "message-menu-entry7"}var f_class = '#ct-entry-selection';
			    {elseif $mm_entry eq "file-menu-entry7"}var f_class = '#gen-file-actions';
			    {else}var f_class = '.entry-form-class';{/if}
			{if $page_display eq "tpl_files" or $page_display eq "tpl_subs" or $page_display eq "tpl_browse"}
			    if($(this).hasClass("sort-menu")){ldelim}
				$(".count").removeClass("sort-active"); $(this).addClass("sort-active");
			    {rdelim}
			    {if $page_display ne "tpl_browse" and $page_display ne "tpl_files"}act_link = "&a="+act_id;{/if}
			    $("#file-sort-div-val").val(act_id);

			    if($("#file-sort-div-val").val() == 'sort-order'){ldelim}paging_link = '&page=1&ipp=all';{rdelim}
			    {if $page_display eq "tpl_browse"}
				c_url	= current_url + menu_section + '?s='+(typeof($(".menu-panel-entry-active").attr("id")) != "undefined" ? $(".menu-panel-entry-active").attr("id") : $(".menu-panel-entry:first").attr("id"));
				$("#file-sort-div-val").val(act_id);
				if(!$(this).hasClass("file-action")){ldelim}
				    $("#main-h2").html($(this).first().text());
				{rdelim}
			    {/if}
			    act_link = $(this).hasClass("sort-menu") ? "" : act_link;
			    var post_url = c_url + "&do=" + $(".sort-active").attr("id") + "&for=" + $("#file-type-div-val").val() + act_link + paging_link;

			    if ($("#file-menu-entry7").hasClass("menu-panel-entry-active") || $("#file-menu-entry8").hasClass("menu-panel-entry-active")) {ldelim}
			    	cr_section = 1;
			    	var crsection = $(".list-cr-tabs .swiper-slide-current").find("a").attr("href").split("-");
			    	var crsort = crsection[1];
			    	act_link = "&a="+act_id;

			    	var post_url = c_url + "&do=cr-" + crsort + "&t=" + $(".view-mode-type.active").attr("id").replace("view-mode-", "") + act_link + paging_link;
			    {rdelim}
			{elseif $page_display eq "tpl_search"}
			    if($(this).hasClass("type-menu")){ldelim}
			        $(".type-menu.count").removeClass("sort-active"); $(this).addClass("sort-active");
			    {rdelim} else {ldelim}
			        $(".sort-menu.count").removeClass("sort-active"); $(this).addClass("sort-active");
			    {rdelim}
			    act_link = $(this).hasClass("sort-menu") ? "" : act_link;
			    var fe_link = "?q="+encodeURIComponent($("#search-query").val())+"&do=ct-load&show="+$(".menu-panel-entry-active").attr("id")+($(".menu-panel-entry-active").attr("id") == "search-pl" ? "&for="+$(".type-menu.sort-active").attr("id") : "")+"&sort="+$(".sort-active").attr("id");
			    var post_url = current_url + menu_section + fe_link + act_link + paging_link;
			{elseif $page_display eq "tpl_playlist"}
			    var post_url = c_url + "&a="+act_id+paging_link;
			{elseif $page_display eq "tpl_respond_extra"}
			    {if $smarty.get.v ne ""}{assign var=ftype value=v}{elseif $smarty.get.i ne ""}{assign var=ftype value=i}{elseif $smarty.get.a ne ""}{assign var=ftype value=a}{elseif $smarty.get.d ne ""}{assign var=ftype value=d}{/if}
			    var post_url = "{$main_url}/{href_entry key="see_responses"}?{$ftype}={$smarty.get.$ftype|sanitize}&do="+act_id+paging_link;
			{elseif $page_display eq "backend_tpl_files"}
			    var action_id = $(this).attr("id");
				if($(this).hasClass("be-count")){ldelim}
				    act_link = "&a="+action_id;
				{rdelim}
				var u_link = ($("#p-user-key").val() != "" ? "&u="+$("#p-user-key").val() : "");
				var post_url = c_url + act_link + u_link + "&for=" + $("#file-type-div-val").val() + "&do=" + $("#file-sort-div-val").val() + paging_link;
			{elseif $page_display eq "backend_tpl_members" and $smarty.get.s eq "backend-menu-entry10-sub2"}
			    var action_id = $(this).attr("id");
				if($(this).hasClass("be-count")){ldelim}
				    act_link = "&a="+action_id;
				{rdelim}
				var post_url = c_url + act_link + "&do=" + $("#file-sort-div-val").val() + paging_link;
			{else}
			    var post_url = c_url + "&do="+act_id+paging_link;
			{/if}
			    if(typeof($("#sq").val()) != "undefined"){ldelim}
				post_url += "&sq="+$("#sq").val();
			    {rdelim}

			{if $page_display eq "tpl_respond_extra"}
			    $("#response-ct").mask(msk_in);
			{elseif $page_display eq "tpl_browse" or $page_display eq "tpl_search"}
			    $("#v-thumbs").mask(msk_in);
			{elseif $page_display eq "tpl_messages"}
				$("#ct-wrapper").mask(msk_in);
			{else}
				if (cr_section == 0) {ldelim}
			    		$(".container").mask(msk_in);
			    	{rdelim} else {ldelim}
			    		$("#siteContent").mask(msk_in);
			    	{rdelim}
			{/if}
			$(".mce-tinymce").detach();
			    $.post(post_url, $(f_class).serialize(),
			    function(data) {ldelim}
				{if $page_display eq "tpl_respond_extra"}
				    $("#response-response").html(data);
				{elseif $page_display eq "tpl_playlist"}
				    $("#file-action-response").html(data);
				{elseif $page_display eq "tpl_browse" or $page_display eq "tpl_search"}
				    if($("#"+act_id+".count").hasClass("file-action")){ldelim}
					$("#file-action-response").html(data);
				    {rdelim} else {ldelim}
					$(".col-left-ct").html(data);
				    {rdelim}
				{elseif $page_display eq "tpl_messages"}
					$("#siteContent").html(data);
				{else}
					if (cr_section == 0) {ldelim}
			    			$(".container").html(data);
			    		{rdelim} else {ldelim}
			    			$(($("#file-menu-entry7").hasClass("menu-panel-entry-active") || $("#file-menu-entry8").hasClass("menu-panel-entry-active")) ? "#cr-sections" : "#siteContent").html(data);
			    			{if ($smarty.get.do ne "" and $smarty.get.do ne "cr-approved" and $smarty.get.a != 'cr-suspended' and $smarty.get.a != 'cr-today') or ($smarty.get.a ne "" and $smarty.get.a ne "cr-approved")}
			    				$(".cr-tabs .tab-current:first").removeClass("tab-current");
			    			{/if}
			    			if ($("#file-menu-entry7").hasClass("menu-panel-entry-active") || $("#file-menu-entry8").hasClass("menu-panel-entry-active")){ldelim}
			    				$("#cr-sections").click();
			    			{rdelim}
			    		{rdelim}
				{/if}
				{if $page_display eq "tpl_respond_extra"}
				    $("#response-ct").unmask();
				{elseif $page_display eq "tpl_browse" or $page_display eq "tpl_search"}
				    if($("#"+act_id+".count").hasClass("file-action")){ldelim}
					$(".list-check").attr("checked", false);
				    {rdelim}
				    $("#v-thumbs").unmask();
				{else}
					if (cr_section == 0) {ldelim}
			    			$(".container").unmask();
			    		{rdelim} else {ldelim}
			    			$("#siteContent").unmask();
			    		{rdelim}
				{/if}
				thisresizeDelimiter();
				$(".lv-wrap:last").removeClass("lv-wrap");
				$(".lv-wrap-fm:last").removeClass("lv-wrap-fm");
			    {rdelim});
			    {rdelim}
			{rdelim});

			$(".list-check").on("click", function () {ldelim}
			    var val_id = $(this).attr("value");
			    if ($(this).is(':checked')) {ldelim}
				$('#hcs-id'+val_id).attr('checked', true);
			    {rdelim} else {ldelim}
				$('#hcs-id'+val_id).attr('checked', false);
			    {rdelim}
			{rdelim});
		    {/if}

			$(".cancel-trigger").unbind().on("click", function () {ldelim}
				var t = $(this);
				if (typeof(t.parent().parent().attr("id")) != "undefined") {ldelim}
					if (t.parent().parent().attr("id") == "add-new-label-form") {ldelim}
						$("#add-new-label").stop().slideUp('fast');
					{rdelim}
					if (t.parent().parent().attr("id") == "add-new-contact-form") {ldelim}
						$("#ct-contact-add-wrap").stop().slideUp('fast');
					{rdelim}
					if (t.parent().parent().attr("id").substr(0,11) == "ct-editform") {ldelim}
					{rdelim}
					return;
				{rdelim}
				t.removeClass("cancel-trigger").addClass("cancel-trigger-loading");
			    $(".vs-mask").mask(" ");
			    $(".container").one().load(c_url, function () {ldelim}
			    	t.addClass("cancel-trigger").removeClass("cancel-trigger-loading");
				$(".vs-mask").unmask();
			    {rdelim});
			{rdelim});

			$(".new-entry").unbind().on("click", function () {ldelim}
				var t = $(this);
			    $(".vs-mask").mask(" ");
			    if (!t.hasClass("save-entry-button")) { $(".loadmask-msg").show(); }
			    t.removeClass("new-entry").addClass("new-entry-loading");
			    $.post(add_url, $("#add-entry-form, #categ-entry-form, #user-entry-form, #ban-entry-form, #lang-entry-form, #db-entry-form, #ad-entry-form").serialize(),
			    function(data) {ldelim}
				closeDiv("open-close-links");
				$(".container").html(data);
				$(".vs-mask").unmask();
				t.addClass("new-entry").removeClass("new-entry-loading");
			    {rdelim});
			{rdelim});

			$(".update-entry").bind("click", function () {ldelim}
				var t = $(this);
			    var form_id = $($(this).closest("form")).attr("id");
			    t.removeClass("new-entry").addClass("new-entry-loading");
			    $("#"+form_id+" .vs-mask").mask(" ");
			    $.post(upd_url, $("#"+form_id).serialize(),
			    function(data) {ldelim}
				$(".container").html(data);
				$("#"+form_id+" .vs-mask").unmask();
				t.addClass("new-entry").removeClass("new-entry-loading");
				$("#cb-response").prependTo("#"+form_id);

				setTimeout(function(){ $(".responsive-accordion-panel.p-msover").addClass("active"); }, 1000);
			    {rdelim});
			{rdelim});

			$(".add-button").bind("click", function () {ldelim}
				var t = $(this);
			    $(".vs-mask").mask(" ");
			    if (!t.hasClass("save-entry-button")) { $(".loadmask-msg").show(); }
			    t.removeClass("new-entry").addClass("new-entry-loading");
			    $(".container").load(add_url, function () {ldelim}
			    	$(".vs-mask").unmask();
			    	t.addClass("new-entry").removeClass("new-entry-loading");
			    {rdelim});
			{rdelim});

			$(".new-categ, .new-banner").bind("click", function () {ldelim}
			    $(".container").mask(" ");
			    $(".container").load(current_url + menu_section + "?s={$smarty.get.s|sanitize}&do=add", function () {ldelim}
				$(".container").unmask();
			    {rdelim});
			{rdelim});

			$("div.link").mouseover(function() {ldelim} $(this).addClass("underlined"); {rdelim}).mouseout(function() {ldelim} $(this).removeClass("underlined"); {rdelim});
		    {rdelim});
		</script>