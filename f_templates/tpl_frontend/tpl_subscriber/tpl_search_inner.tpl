<div class="search_holder_fe">
	<div id="sb-search-fe" class="sb-search-fe{if $is_mobile} sb-search-open{/if}">
		<form name="file_search_form" id="file-search-form" method="get" action="">
			<input class="sb-search-input" placeholder="{lang_entry key="frontend.global.searchtext"}" type="text" value="{$smarty.get.sq|sanitize}" name="sq" id="sq" onclick="this.select()">
			<input class="sb-search-submit file-search" id="file-search-button" type="button" value="">
			<i class="icon icon-search"></i>
			<span title="{lang_entry key="account.entry.search.users"}" rel="tooltip" class="sb-icon-search-fe icon-search"></span>
		</form>
	</div>
</div>
	<script type="text/javascript">{fetch file="f_scripts/fe/js/uisearch.js"}</script>
	<script type="text/javascript">
	new UISearch(document.getElementById('sb-search-fe'));
	
	$(document).ready(function(){ldelim}
	    	$("#sq").autocomplete({ldelim}
	    		showNoSuggestionNotice: true,
                	type: "post",
                	params: {ldelim}"t": "sub" {rdelim},
                	serviceUrl: "?a=1&t=sub&do=autocomplete",
                	onSearchStart: function() {ldelim}{rdelim},
                	onSearchComplete: function(query, suggestion) {ldelim}{rdelim},
                	onSelect: function (suggestion) {ldelim}
                		if (typeof suggestion.data != 'undefined') {ldelim}
                			var u = String(window.location);
                			u = u.replace('&uk={$smarty.get.uk|sanitize}', '');

                			$("#custom-date-form").attr("action", u + '&uk='+suggestion.data);
                                        $("#custom-date-form").submit();
                		{rdelim}
                	{rdelim}
        	{rdelim});
        	$("#file-search-form").submit(function(e){ldelim}e.preventDefault();{rdelim});
	{rdelim});
	</script>