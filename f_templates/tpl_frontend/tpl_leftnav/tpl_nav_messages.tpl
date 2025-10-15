	<div class="blue categories-container">
                    <h4 class="nav-title categories-menu-title left-menu-h4"><i class="icon-envelope"></i>{lang_entry key="subnav.entry.contacts.messages"}</h4>
                    <aside>
                        <nav>
                            <ul class="accordion" id="categories-accordion">
	{if $internal_messaging eq 1}
	    <li id="message-menu-entry2" class="menu-panel-entry{if $smarty.get.s eq "message-menu-entry2"} menu-panel-entry-active{/if}">
	    	<a href="javascript:;" class="dcjq-parent active">
	    		<i class="icon-envelope"></i><span class="mm">{lang_entry key="msg.entry.inbox"}</span>{if $message_count eq 1}&nbsp;<span id="message-menu-entry2-count" class="right-float mm-count">{insert name="msgCount" for="message-menu-entry2" assign="m_count"}{$m_count}</span>{/if}
	    	</a>
	   </li>
		{if $custom_labels eq 1}{insert name=sectionLabel assign=sectionLabel for="message-menu-entry2"}{$sectionLabel}{/if}
	{/if}
	{if $internal_messaging eq 1}
	    <li id="message-menu-entry5" class="menu-panel-entry{if $smarty.get.s eq "message-menu-entry5"} menu-panel-entry-active{/if}">
	    	<a href="javascript:;"><i class="icon-envelope"></i><span class="mm">{lang_entry key="msg.entry.sent"}</span>{if $message_count eq 1}&nbsp;<span id="message-menu-entry5-count" class="right-float mm-count">{insert name="msgCount" for="message-menu-entry5" assign="m_count"}{$m_count}</span>{/if}</a>
	    </li>
		{if $custom_labels eq 1}{insert name=sectionLabel assign=sectionLabel for="message-menu-entry5"}{$sectionLabel}{/if}
	{/if}
	{if $internal_messaging eq 1}
	    <li id="message-menu-entry6" class="greyed-out menu-panel-entry{if $smarty.get.s eq "message-menu-entry6"} menu-panel-entry-active{/if}">
	    	<a href="javascript:;"><i class="icon-spam"></i><span class="mm">{lang_entry key="msg.entry.spam"}</span>{if $message_count eq 1}&nbsp;<span id="message-menu-entry6-count" class="right-float mm-count">{insert name="msgCount" for="message-menu-entry6" assign="m_count"}{$m_count}</span>{/if}</a>
	    </li>
		{if $custom_labels eq 1}{insert name=sectionLabel assign=sectionLabel for="message-menu-entry6"}{$sectionLabel}{/if}
	{/if}
	{if $user_friends eq 1 and $approve_friends eq 1}
	    <li id="message-menu-entry4" class="darker-out menu-panel-entry{if $smarty.get.s eq "message-menu-entry4"} menu-panel-entry-active{/if}">
	    	<a href="javascript:;"><i class="icon-notebook"></i><span class="mm">{lang_entry key="msg.entry.fr.invite"}</span>{if $message_count eq 1}&nbsp;<span id="message-menu-entry4-count" class="right-float mm-count">{insert name="msgCount" for="message-menu-entry4" assign="m_count"}{$m_count}</span>{/if}</a>
	    </li>
	{/if}

    {if $user_friends eq 1 or $user_blocking eq 1 or $custom_labels eq 1}
	    <li id="message-menu-entry7" class="menu-panel-entry{if $smarty.get.s eq "message-menu-entry7"} menu-panel-entry-active{/if}">
	    	<a href="javascript:;"><i class="icon-address-book"></i><span class="mm">{lang_entry key="msg.entry.adr.book"}</span>{if $message_count eq 1}&nbsp;<span id="message-menu-entry7-count" class="right-float mm-count">{insert name="msgCount" for="message-menu-entry7" assign="m_count"}{$m_count}</span>{/if}</a>
	    </li>
	{if $user_friends eq 1 or $user_blocking eq 1 or $custom_labels eq 1}{insert name=sectionLabel assign=sectionLabel for="message-menu-entry7"}{$sectionLabel}{/if}
    {/if}
                            </ul>
                        </nav>
                    </aside>
                </div>


	<script type="text/javascript">
	    var paging_link = '';
	    {if $smarty.get.page ne ""}var paging_link = '&page={$smarty.get.page|sanitize}&ipp={$smarty.get.ipp|sanitize}';{/if}

	    $(document).ready(function() {ldelim}
	    {if $smarty.get.s eq ""}
		var get_from = {if $internal_messaging eq 1}'message-menu-entry2'{elseif $internal_messaging eq 0 and ($user_friends eq 1 or $user_blocking eq 1)}'message-menu-entry7'{else}'message-menu-entry3'{/if};
		{if $menu_section ne ""}
		    get_from = "{$menu_section}";
		{/if}
		$("#"+get_from).addClass("menu-panel-entry-active");

		{if $smarty.get.src eq 'upage' and $smarty.get.mid ne '' and $smarty.session.channel_msg ne ''}
		    var compose_url = current_url+menu_section+"?do=compose&src=upage";
                    var h2_c_title  = $("#compose-button>span").text();
                    wrapLoad(compose_url, fe_mask);
		{else}
		    wrapLoad(current_url+menu_section+"?s="+get_from);
		{/if}
	    {else}
	    {/if}

		function label_del(del_id, del_url, m_url) {ldelim}
		    var del_answer = confirm("{lang_entry key="notif.confirm.delete.label"}");
		    if (del_answer){ldelim}
		    	$("#ct-wrapper").mask(" ");
    			$.post(del_url, $("#label-form-"+del_id).serialize(), function( data ) {ldelim}
    				if (data > 0) {ldelim}
    					$("#label-form-"+del_id).detach();
    					$("#"+get_from).addClass("menu-panel-entry-active");
    					$("#"+get_from+" > a:first").addClass("dcjq-parent active");
    					
    					wrapLoad(current_url+menu_section+"?s="+get_from);
    				{rdelim}
    			{rdelim});
    			return false;
		    {rdelim}
		    return false;
		{rdelim}

		$(".label-del").bind("click", function(){ldelim}
		    var del_id = $(this).attr("id");
		    var del_ar = del_id.split("-");
		    var m_section = del_ar[0]+"-"+del_ar[1]+"-"+del_ar[2];
		    var m_url = current_url+ menu_section + "?s="+m_section;
		    var del_url = m_url+"&do=delete_label"+paging_link;

		    label_del(del_id, del_url, m_url);
		{rdelim});

		$(document).on("click", "#compose-button", function() {ldelim}
		    var compose_url = current_url+menu_section+"?do=compose";
////		    var h2_c_title  = $("#compose-button>span").text();
////		    $("h2").text(h2_c_title);
		    wrapLoad(compose_url, fe_mask);
		{rdelim});
	    {rdelim});
	</script>
