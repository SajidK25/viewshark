<div class="search_holder_fe">
	<div id="sb-search-fe" class="sb-search-fe{if $is_mobile} sb-search-open{/if}">
		<form name="file_search_form" id="file-search-form" method="get" action="">
			<input class="sb-search-input" placeholder="{lang_entry key="frontend.global.searchtext"}" type="text" value="{$smarty.get.sq|sanitize}" name="sq" id="sq" onclick="this.select()">
			<input class="sb-search-submit file-search" id="file-search-button" type="button" value="">
			<i class="icon icon-search no-display"></i>
			<span title="{lang_entry key="frontend.global.searchtext"}" rel="tooltip" class="sb-icon-search-fe icon-search">
			</span>
		</form>
	</div>
</div>
	<script type="text/javascript">
	$(document).ready(function(){ldelim}
	new UISearch(document.getElementById('sb-search-fe'));
	$("#sq").keydown(function(){ldelim}
	    	$("#sq").autocomplete({ldelim}
                	type: "post",
                	params: {ldelim}"t": $(".view-mode-type.active").attr("id").replace("view-mode-", "") {rdelim},
                	serviceUrl: current_url + menu_section +"?s=" + $(".menu-panel-entry-active").attr("id") +"&do=autocomplete&m=0",
                	onSearchStart: function() {ldelim}{rdelim},
                	onSelect: function (suggestion) {ldelim}
                		$(".file-search").trigger("click");
                	{rdelim}
        	{rdelim});
        {rdelim});

	    $(".file-search").click(function(){ldelim}
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

                                        $("#main-view-mode-" + idnr + "-" + type_all + "-list #paging-bottom").detach();
        
                                        if (page > 1) {
                                                paging_link = "&page=" + page;
                                        }
                                        
                                        var post_url = c_url + "&a=" + a + paging_link;

                                        $('#cb-response').replaceWith(''); $('#cb-response-wrap').replaceWith('');

	    
	    
//		var u_url = (typeof($("#p-user-key").val()) != 'undefined') ? "&u="+$("#p-user-key").val() : "";
//		var c_url = current_url + menu_section +"?s={$smarty.request.s|sanitize}"+u_url+"&do="+$("#file-sort-div-val").val()+"&for="+$("#file-type-div-val").val();

		$("#file-search-form").attr("action", c_url);
		$("#main-content").mask("");
		$.get(c_url, $("#file-search-form").serialize(), function(data){ldelim};
			jQuery(p_str).replaceWith(data);
			$("#cb-response-wrap").insertBefore("#view-type-content");
		    //$(".container").html(data);
		    
		    $("#main-content").unmask();
		    resizeDelimiter();
		    
		    setTimeout(function () {ldelim}
                                                        ViewModeSizeSetFunctions();
                                                {rdelim}, 100);
                                                setTimeout(function () {ldelim}
                                                        thumbFade();
                                                {rdelim}, 200);

		{rdelim});
	    {rdelim});
	    enterSubmit("#file-search-form input", "#file-search-button");
	{rdelim});
	</script>