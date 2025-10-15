	{assign var=c_section value="{href_entry key="subscriptions"}"}
	<div id="menu-panel-wrapper" class="wdmax">
	    {insert name=getUserSubs}
	</div>
	<script type="text/javascript">
	    var current_url ='{$main_url}/';var menu_section='{$c_section}';var fe_mask='on';
	    $(document).ready(function() {ldelim}
		var get_from = '';var h2t = '';var h2u = '';
		if(typeof($("#sub1-menu li.menu-panel-entry").attr("id")) !== 'undefined'){ldelim}
		    get_from = $("#sub1-menu li.menu-panel-entry").attr("id");
		    h2t	= $("#sub1-menu li.menu-panel-entry:first span.mm").text();
		    h2u = "#ct-h2";
		{rdelim} else if(typeof($("#sub2-menu li.menu-panel-entry").attr("id")) !== 'undefined'){ldelim}
		    get_from = $("#sub2-menu li.menu-panel-entry").attr("id");
		    h2t	 = $("#sub2-menu li.menu-panel-entry:first span.mm").text();
		    h2u = "#ct-h3";
		{rdelim}
	    {rdelim});
	</script>
