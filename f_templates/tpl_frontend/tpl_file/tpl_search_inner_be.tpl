	<div id="sb-search" class="sb-search">
		<form name="file_search_form" id="file-search-form" method="get" action="">
			<input class="sb-search-input" placeholder="{lang_entry key="frontend.global.searchtext"}" type="text" value="{$smarty.get.sq|sanitize}" name="sq" id="sq" onclick="this.select()">
			<input class="sb-search-submit file-search" id="file-search-button" type="button" value="">
			<span title="{lang_entry key="frontend.global.searchtext"}" rel="tooltip" class="sb-icon-search">
			</span>
		</form>
	</div>
	<script type="text/javascript">
	$(document).ready(function(){ldelim}
	    new UISearch(document.getElementById('sb-search'));
	    {if $page_display eq "backend_tpl_members" or $page_display eq "backend_tpl_files" or $page_display eq "backend_tpl_servers"}
	    	$("#sq").autocomplete({ldelim}
                type: "post",
                serviceUrl: current_url + menu_section +"?s={$smarty.request.s|sanitize}&do=autocomplete",
                onSearchStart: function() {ldelim}
                {rdelim},
                onSelect: function (suggestion) {ldelim}
                	$(".file-search").trigger("click");
                {rdelim}
        {rdelim});

	    {/if}
	    $(".file-search").click(function(){ldelim}
		var u_url = (typeof($("#p-user-key").val()) != 'undefined') ? "&u="+$("#p-user-key").val() : "";
		var c_url = current_url + menu_section +"?s={$smarty.request.s|sanitize}"+u_url+"&do="+$("#file-sort-div-val").val()+"&for="+$("#file-type-div-val").val();

		$("#file-search-form").attr("action", c_url);
		$(".container").mask("");
		$.get(c_url, $("#file-search-form").serialize(), function(data){ldelim};
		    $(".container").html(data);
		    $(".container").unmask();
		    resizeDelimiter();
		{rdelim});
	    {rdelim});
	    enterSubmit("#file-search-form input", "#file-search-button");
	{rdelim});
	</script>