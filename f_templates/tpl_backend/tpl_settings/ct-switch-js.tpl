	<script type="text/javascript">
		function resizeDelimiter(){}
	    $(document).on({ldelim}click: function () {ldelim}
	    	var _id = "";
	    	if ($(".fancybox-wrap").width() > 0) {ldelim}
	    		_id = ".fancybox-inner ";
	    	{rdelim}
	    	$(_id + ".responsive-accordion div.responsive-accordion-head").addClass("active");
	    	$(_id + ".responsive-accordion div.responsive-accordion-panel").addClass("active").show();
	    	$(_id + ".responsive-accordion i.responsive-accordion-plus").hide();
	    	$(_id + ".responsive-accordion i.responsive-accordion-minus").show();
		thisresizeDelimiter();
	    {rdelim}{rdelim}, "#all-open");
	    $(document).on({ldelim}click: function () {ldelim}
	    	var _id = "";
	    	if ($(".fancybox-wrap").width() > 0) {ldelim}
	    		_id = ".fancybox-inner ";
	    	{rdelim}
	    	$(_id + ".responsive-accordion div.responsive-accordion-head").removeClass("active");
	    	$(_id + ".responsive-accordion div.responsive-accordion-panel").removeClass("active").hide();
	    	$(_id + ".responsive-accordion i.responsive-accordion-plus").show();
	    	$(_id + ".responsive-accordion i.responsive-accordion-minus").hide();
		$("#ct_entry").val("");
		thisresizeDelimiter();
	    {rdelim}{rdelim}, "#all-close");
	$(document).ready(function() {ldelim}
	$(".cb-enable").click(function(){ldelim}
	    var parent = $(this).parents('.switch');
	    $('.cb-disable',parent).removeClass('selected');
	    $(this).addClass('selected');
	    $('.checkbox',parent).attr('checked', true);
	{rdelim});
	$(".cb-disable").click(function(){ldelim}
	    var parent = $(this).parents('.switch');
	    $('.cb-enable',parent).removeClass('selected');
	    $(this).addClass('selected');
	    $('.checkbox',parent).attr('checked', false);
	{rdelim});
	    $(".ct-entry").click(function(e){ldelim}
		var ct_id = $(this).attr("id");
		var en_id = $("#"+ct_id+">div.ct-entry-details").attr("id");
		var ct_target = e.target.type;
	    {if $page_display eq "tpl_messages" or $page_display eq "tpl_files"}
		if($("#" + ct_id + " div.responsive-accordion-head").hasClass("new-message")){ldelim}
		{if $smarty.get.s eq "file-menu-entry7"}
		    var id_arr = ct_id.split("-");
		    $(".section_subject_value").val(id_arr[2]);
		{/if}
		    $.post(current_url + menu_section + "?s={$smarty.get.s|sanitize}&do=read{if $smarty.get.s eq "file-menu-entry7"}&for="+$("#file-type-div-val").val()+"{/if}", {if $smarty.get.s eq "file-menu-entry7"}$(".entry-form-class").serialize(){else}$("#"+ct_id+" div.ct-entry-details form").serialize(){/if}, function(data){ldelim}
			$("#" + ct_id + " div").removeClass("new-message");
			{insert name=currentMenuEntry assign=mm_entry for=$smarty.get.s|sanitize}
			switch("{$mm_entry}"){ldelim}
			    case "message-menu-entry2": var tdiv = "#new-message-count"; break;
			    case "message-menu-entry3": var tdiv = "#new-comment-count"; break;
			    case "message-menu-entry4": var tdiv = "#new-invite-count"; break;
			{rdelim}
			var tval = parseInt($(tdiv).html());
			if(tval > 0){ldelim}
			    if(tval == 1){
				$(tdiv).html("");
				$(tdiv).parent().next().html("");
			    }else{
				$(tdiv).html(tval-1);
			    }
			{rdelim}
			$(".col1").unmask();
		    {rdelim});
		{rdelim}
	    {/if}
		$(".ct-entry>div.ct-bullet-in").removeClass("ct-bullet-in");
		$(".ct-entry>div.ct-bullet-label").removeClass("bold");
		$(".ct-entry>div.ct-entry-details").hide();
		$("#" + ct_id + ">div.ct-bullet-out").addClass("ct-bullet-in");
		$("#" + ct_id + ">div.ct-bullet-label").addClass("bold");
		$("#"+en_id).show();
		$("#ct_entry").val(ct_id);
		thisresizeDelimiter();
	    {rdelim});
	    $(".save-button").bind("click", function() {ldelim}
		var p_url = {if $smarty.get.s eq "account-menu-entry4" or $smarty.get.s eq "account-menu-entry6" or $smarty.get.s eq "backend-menu-entry3-sub11"}current_url+menu_section+"?s={$smarty.get.s|sanitize}"{else}"{$smarty.server.REQUEST_URI}"{/if};
		var trg = ($(".menu-panel-entry-active").attr("rel-m") == "{href_entry key="account"}") ? '#siteContent' : '.vs-column.vs-mask';
		var t = $(this);
		$(trg).each(function(){ldelim}$(this).mask("");{rdelim});
		t.removeClass("save-entry-button").addClass("save-entry-button-loading");
		$.post(p_url, $("#ct-set-form").serialize(), function(data){ldelim}
		    if ($(data).first().attr("id") == "cb-response-wrap") {ldelim}
		    	$("#cb-response-wrap").detach();
		    	$(data).first().insertAfter("#ct-wrapper div.clearfix:first");
		    {rdelim}
		    $(trg).unmask();
		    t.addClass("save-entry-button").removeClass("save-entry-button-loading");
		{rdelim});
		return false;
	    {rdelim});
	{rdelim});
	thisresizeDelimiter();
	</script>