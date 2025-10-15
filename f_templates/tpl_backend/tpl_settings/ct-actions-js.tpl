	{if $smarty.get.s ne ""}{assign var="get_s" value=$smarty.get.s|sanitize}{else}{if $page_display eq "tpl_files"}{insert name="getCurrentSection" assign=s}{assign var="get_s" value=$s}{/if}{/if}
	<script type="text/javascript">
	$(document).ready(function() {ldelim}
	    var paging_link = '';
	    {if $smarty.get.page ne ""}var paging_link = '&page={$smarty.get.page|sanitize}&ipp={$smarty.get.ipp|sanitize}';{/if}
	    $(".disable-grey").click(function(){ldelim}
		var this_id = $(this).attr("id").replace("ic2-", "");
		var div_id = this_id;
		var form_id = {if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}"gen-file-actions";{else}$("#"+div_id+" form").attr("id");{/if}
		{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}
		    var c_id = div_id.split("-")[3];
		    $(".section_subject_value").val(c_id);
		    var crsection = $(".cr-tabs .tab-current").find("a").attr("href").split("-");
		    var crsort = crsection[1];
		    var dis_url = current_url + menu_section + "?s={$get_s|sanitize}&do={if $get_s eq "file-menu-entry7"}comm-{elseif $get_s eq "file-menu-entry8"}resp-{/if}disable{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}&t="+$(".view-mode-type.active").attr("id").replace("view-mode-", "")+"&a=cr-"+crsort+"{/if}"+paging_link;

			$("#siteContent").mask("");
			$.post(dis_url, $( "#"+form_id ).serialize(), function(data) {ldelim} 
				$("{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}#cr-sections{else}#siteContent{/if}").html(data);
				$("#siteContent").unmask();
				{if ($smarty.get.do ne "" and $smarty.get.do ne "cr-approved") or ($smarty.get.a ne "" and $smarty.get.a ne "cr-approved")}
					$(".cr-tabs .tab-current:first").removeClass("tab-current");
					$(".cr-tabs li a[href=\'"+$(".swiper-slide.tab-current").find("a").attr("href")+"\']").parent().addClass("tab-current");
				{/if}
				
				return;
			{rdelim});
			return;
		{/if}
		var dis_url = current_url + menu_section + "?s={$get_s|sanitize}&do={if $get_s eq "file-menu-entry7"}comm-{elseif $get_s eq "file-menu-entry8"}resp-{/if}disable{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}&t="+$(".view-mode-type.active").attr("id").replace("view-mode-", "")+"&a=cr-"+crsort+"{/if}"+paging_link;
		$(".ct-entry").unbind('click');
		postLoad(dis_url, "#"+form_id, "#"+form_id+" .vs-mask");
	    {rdelim});

	    $(".enable-grey").click(function(){ldelim}
		var this_id = $(this).attr("id").replace("ic1-", "");
		var div_id = this_id;
		var form_id = {if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}"gen-file-actions";{else}$("#"+div_id+" form").attr("id");{/if}
		{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}
		    var c_id = div_id.split("-")[3];
		    $(".section_subject_value").val(c_id);
		    var crsection = $(".cr-tabs .tab-current").find("a").attr("href").split("-");
		    var crsort = crsection[1];
		    var ena_url = current_url + menu_section + "?s={$get_s|sanitize}&do={if $get_s eq "file-menu-entry7"}comm-{elseif $get_s eq "file-menu-entry8"}resp-{/if}enable{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}&t="+$(".view-mode-type.active").attr("id").replace("view-mode-", "")+"&a=cr-"+crsort+"{/if}"+paging_link

		    	$("{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}#cr-sections{else}#siteContent{/if}").mask("");
			$.post(ena_url, $( "#"+form_id ).serialize(), function(data) {ldelim} 
				$("{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}#cr-sections{else}#siteContent{/if}").html(data);
				$("#siteContent").unmask();
				{if ($smarty.get.do ne "" and $smarty.get.do ne "cr-approved") or ($smarty.get.a ne "" and $smarty.get.a ne "cr-approved")}
					$(".cr-tabs .tab-current:first").removeClass("tab-current");
					$(".cr-tabs li a[href=\'"+$(".swiper-slide.tab-current").find("a").attr("href")+"\']").parent().addClass("tab-current");
				{/if}
				
				return;
			{rdelim});
			return;
		{/if}

		var ena_url = current_url + menu_section + "?s={$get_s|sanitize}&do={if $get_s eq "file-menu-entry7"}comm-{elseif $get_s eq "file-menu-entry8"}resp-{/if}enable{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}&t="+$(".view-mode-type.active").attr("id").replace("view-mode-", "")+"&a=cr-"+crsort+"{/if}"+paging_link

		$(".ct-entry").unbind('click');
		postLoad(ena_url, "#"+form_id, "#"+form_id+" .vs-mask");
	    {rdelim});

	    $(".delete-grey").click(function(){ldelim}
		$("#cb-response").click();
		var del_id  = $(this).attr("id");
		var this_id = $(this).attr("id").replace("ic3-", "");
		var div_id = this_id;
		div_id	    = div_id == 'cb-response' ? $("#"+del_id).parent("div").next("div").next("div").attr("id") : div_id;
		var form_id = {if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}"gen-file-actions";{else}$("#"+div_id+" form").attr("id");{/if}
		{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}
		    var c_id = div_id.split("-")[3];
		    $(".section_subject_value").val(c_id);
		    var crsection = $(".cr-tabs .tab-current").find("a").attr("href").split("-");
		    var crsort = crsection[1];
		    var del_confirm = confirm('{if $page_display eq "tpl_messages"}{lang_entry key="notif.confirm.delete.message"}{else}{lang_entry key="notif.confirm.delete"}{/if}');
		    var del_url = current_url + menu_section + "?s={$get_s|sanitize}&do={if $get_s eq "file-menu-entry7"}comm-{elseif $get_s eq "file-menu-entry8"}resp-{/if}delete{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}&t="+$(".view-mode-type.active").attr("id").replace("view-mode-", "")+"&a=cr-"+crsort+"{/if}"+paging_link;

		    if(del_confirm){ldelim}
		    	$("{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}#cr-sections{else}#siteContent{/if}").mask("");
			$.post(del_url, $( "#"+form_id ).serialize(), function(data) {ldelim} 
				$("{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}#cr-sections{else}#siteContent{/if}").html(data);
				$("#siteContent").unmask();
				{if ($smarty.get.do ne "" and $smarty.get.do ne "cr-approved") or ($smarty.get.a ne "" and $smarty.get.a ne "cr-approved")}
					$(".cr-tabs .tab-current:first").removeClass("tab-current");
					$(".cr-tabs li a[href=\'"+$(".swiper-slide.tab-current").find("a").attr("href")+"\']").parent().addClass("tab-current");
				{/if}
				return;
			{rdelim});
			{rdelim}
			return;
		{/if}
		var del_url = current_url + menu_section + "?s={$get_s|sanitize}&do={if $get_s eq "file-menu-entry7"}comm-{elseif $get_s eq "file-menu-entry8"}resp-{/if}delete{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}&for="+$("#file-type-div-val").val()+"&a="+$(".sort-active").attr("id")+"{/if}"+paging_link;
		var del_confirm = confirm('{if $page_display eq "tpl_messages"}{lang_entry key="notif.confirm.delete.message"}{else}{lang_entry key="notif.confirm.delete"}{/if}');

		if(del_confirm){ldelim}
		    postLoad(del_url, "#"+form_id);
		    return false;
		{rdelim}
		return false;
	    {rdelim});

	    $(".reply-msg").click(function(){ldelim}
		var form_id = $(this).parents("form").attr("id");
		postLoad(current_url + menu_section + "?do=compose&r=1{if $get_s eq "message-menu-entry3"}&f=comm{/if}", "#"+form_id);
	    {rdelim});

	    $(".delete-msg").click(function(){ldelim}
		var del_id = $(this).attr("id");
		var form_id = $("#"+del_id).parents("form").attr("id");

		var del_url = current_url + menu_section + "?s={$get_s|sanitize}&do=delete"+paging_link;
		var del_confirm = confirm('{if $page_display eq "tpl_messages"}{lang_entry key="notif.confirm.delete.message"}{else}{lang_entry key="notif.confirm.delete"}{/if}');

		if(del_confirm){ldelim}
		    postLoad(del_url, "#"+form_id);
		    return false;
		{rdelim}
		return false;
	    {rdelim});
	{rdelim});
	</script>
