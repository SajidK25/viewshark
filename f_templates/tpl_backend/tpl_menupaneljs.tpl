	{if $page_display eq "backend_tpl_settings" or $page_display eq "backend_tpl_dashboard" or $page_display eq "backend_tpl_analytics" or $page_display eq "backend_tpl_upload" or $page_display eq "backend_tpl_import"}
	    {assign var=get_from value="backend-menu-entry2"}
	    {assign var=get_menu value="backend-menu-entry2"}
	    {assign var=c_section value="{href_entry key="be_settings"}"}
	    {assign var=sub_menu value=0}
	{elseif $page_display eq "backend_tpl_files"}
	    {if $smarty.get.u[0] eq "v" or $smarty.get.k[0] eq "v"}
				{assign var=get_from value="backend-menu-entry6-sub1"}
				{assign var=sub_menu value=1}
	    {elseif $smarty.get.u[0] eq "i" or $smarty.get.k[0] eq "i"}
                {assign var=get_from value="backend-menu-entry6-sub2"}
                {assign var=sub_menu value=1}
        {elseif $smarty.get.u[0] eq "a" or $smarty.get.k[0] eq "a"}
                {assign var=get_from value="backend-menu-entry6-sub3"}
                {assign var=sub_menu value=1}
        {elseif $smarty.get.u[0] eq "d" or $smarty.get.k[0] eq "d"}
                {assign var=get_from value="backend-menu-entry6-sub4"}
                {assign var=sub_menu value=1}
        {elseif $smarty.get.u[0] eq "b" or $smarty.get.k[0] eq "b"}
                {assign var=get_from value="backend-menu-entry6-sub5"}
                {assign var=sub_menu value=1}
        {elseif $smarty.get.u[0] eq "l" or $smarty.get.k[0] eq "l"}
                {assign var=get_from value="backend-menu-entry6-sub6"}
                {assign var=sub_menu value=1}
        {elseif $smarty.get.u[0] eq "s" or $smarty.get.k[0] eq "s"}
                {assign var=get_from value="backend-menu-entry6-sub7"}
                {assign var=sub_menu value=1}
	    {else}
		{assign var=get_from value="backend-menu-entry6"}
		{assign var=sub_menu value=0}
	    {/if}
	    {assign var=get_menu value="backend-menu-entry6"}
	    {assign var=c_section value="{href_entry key="be_files"}"}
	{elseif $page_display eq "backend_tpl_members"}
	    {if $smarty.get.u ne ""}
		{assign var=get_from value="backend-menu-entry10-sub2"}
		{assign var=sub_menu value=1}
	    {else}
		{assign var=get_from value="backend-menu-entry10"}
		{assign var=sub_menu value=0}
	    {/if}
	    {assign var=get_menu value="backend-menu-entry10"}
	    {assign var=c_section value="{href_entry key="be_members"}"}
	{elseif $page_display eq "backend_tpl_advertising"}
	    {assign var=get_from value="backend-menu-entry8-sub2"}
	    {assign var=get_menu value="backend-menu-entry8"}
	    {assign var=sub_menu value=1}
	    {assign var=c_section value="{href_entry key="be_advertising"}"}
	{elseif $page_display eq "backend_tpl_players"}
	    {assign var=get_from value="backend-menu-entry12"}
	    {assign var=get_menu value="backend-menu-entry12"}
	    {assign var=sub_menu value=0}
	    {assign var=c_section value="{href_entry key="be_players"}"}
	{/if}
	<script type="text/javascript">
	    var current_url  = '{$main_url}/{$backend_access_url}/';
    	    var menu_section = '{$c_section}';
    	    $(document).ready(function() {ldelim}
    		var get_from = "{$get_from}";
    		var get_menu = "{$get_menu}";
    		{if $smarty.request.u ne "" or $smarty.request.k ne ""}
    		wrapLoad(current_url+menu_section+"?s="+get_from+"{if $smarty.request.u ne ""}&u={$smarty.request.u|sanitize}{/if}{if $smarty.request.k ne ""}&k={$smarty.request.k|sanitize}{/if}{if $page_display eq "backend_tpl_members" and $smarty.get.u ne ""}&sq={insert name="getUserNameKey" key="{$smarty.get.u|sanitize}"}{/if}");
    		{/if}
    	    {if $page_display eq "backend_tpl_files" and $smarty.get.u ne ""}
    		$(".sort-user-name").html("{insert name="getUserNameKey" key="{$smarty.get.u|sanitize}"}");
    		$(".sort-user-key").html("{$smarty.get.u|sanitize}");
    	    {/if}
    		$("#"+get_menu).addClass("menu-panel-entry-active");
    	    {if $sub_menu eq "1"}
    		$("#"+get_from).addClass("menu-panel-entry-sub-active");
    	    {/if}
    	    {rdelim});
	</script>
