	{if $smarty.get.s ne ""}{assign var="get_s" value=$smarty.get.s|sanitize}{else}{if $page_display eq "tpl_files"}{insert name="getCurrentSection" assign=s}{assign var="get_s" value=$s}{/if}{/if}
	{insert name=currentMenuEntry assign=mm_entry for=$get_s}
        {if ($page_display eq "backend_tpl_comments") or ($page_display eq "tpl_subs") or ($page_display eq "tpl_playlist") or ($page_display eq "tpl_files") or ($page_display eq "backend_tpl_servers") or ($page_display eq "backend_tpl_streaming") or ($page_display eq "backend_tpl_members") or ($page_display eq "backend_tpl_files") or ($page_display eq "backend_tpl_categ") or ($mm_entry eq "backend-menu-entry7") or ($mm_entry eq "backend-menu-entry8") or ($mm_entry eq "backend-menu-entry9") or ($get_s eq "backend-menu-entry5-sub2") or ($get_s eq "backend-menu-entry2-sub16") or ($get_s eq "backend-menu-entry4-sub2") or ($get_s eq "backend-menu-entry4-sub3") or ($mm_entry eq "message-menu-entry2") or ($mm_entry eq "message-menu-entry4") or ($mm_entry eq "message-menu-entry5") or ($mm_entry eq "message-menu-entry6") or ($mm_entry eq "message-menu-entry7") or ($mm_entry eq "message-menu-entry3")}
            {if $smarty.get.do eq "add"}{assign var=check_all value=0}{else}{assign var=check_all value=1}{/if}
            {if $page_display eq "tpl_files" and $file_privacy eq "0" and $file_favorites eq "0" and $file_playlists eq "0" and $file_deleting eq "0" and $file_history eq "0" and $file_watchlist eq "0"}
                {assign var=check_all value=0}
            {/if}
        {else}
            {assign var=check_all value=0}
        {/if}
	{if $page_display ne "tpl_playlist"}
		{if $check_all eq "1" and $get_s != "file-menu-entry6"}<div class="left-float double-check-arrow icheck-box" id="checkselect-all-entries"><input style="display: block;" type="checkbox" id="check-all" /></div>{/if}
		{if $check_all eq "1" and $get_s != "file-menu-entry6"}
		<div class="menu-drop">
		    <div {if $check_all eq "1"}id="entry-action-buttons{if $get_s eq "file-menu-entry7" or $get_s eq "file-menu-entry8"}{/if}" class="dl-menuwrapper"{else}class="ul-no-list"{/if}>
		    		{if $page_display|substr:0:7 eq "backend"}
		    		<span class="dl-trigger actions-trigger" rel="tooltip" title="{lang_entry key="frontend.global.selection.actions"}"></span>
		    		{else}
				<span class="dl-trigger actions-trigger f-16" rel="tooltip" title="{lang_entry key="frontend.global.selection.actions"}"></span>
				{/if}
			    <ul class="dl-menu">
			    {if $mm_entry eq "message-menu-entry4"}
				<li class="count" id="cb-approve"><a href="javascript:;"><i class="icon-check"></i> {lang_entry key="contacts.invites.approve"}</a></li>
				<li class="count" id="cb-disable"><a href="javascript:;"><i class="icon-stop"></i> {lang_entry key="contacts.invites.ignore"}</a></li>
			    {elseif $mm_entry eq "message-menu-entry3"}
				<li class="count" id="cb-enable"><a href="javascript:;"><i class="icon-check"></i> {lang_entry key="contacts.invites.approve"}</a></li>
				<li class="count" id="cb-disable"><a href="javascript:;"><i class="icon-stop"></i> {lang_entry key="contacts.comments.approve"}</a></li>
			    {elseif $page_display eq "backend_tpl_files" or $mm_entry eq "backend-menu-entry10"}
				<li class="count be-count file-action" id="cb-active"><a href="javascript:;"><i class="icon-play"></i> {lang_entry key="files.action.active"}</a></li>
				<li class="count be-count file-action" id="cb-inactive"><a href="javascript:;"><i class="icon-stop"></i> {lang_entry key="files.action.inactive"}</a></li>
			    {if $page_display eq "backend_tpl_files"}
				<li class="count be-count file-action" id="cb-approve"><a href="javascript:;"><i class="icon-check"></i> {lang_entry key="files.action.approve"}</a></li>
				<li class="count be-count file-action" id="cb-disapprove"><a href="javascript:;"><i class="icon-pause"></i> {lang_entry key="files.action.disapprove"}</a></li>
			    {/if}
			    {if $mm_entry eq "backend-menu-entry10"}
			    	<li class="count be-count file-action" id="cb-partner"><a href="javascript:;"><i class="icon-coin"></i> {lang_entry key="files.action.partner"}</a></li>
			    	<li class="count be-count file-action" id="cb-unpartner"><a href="javascript:;"><i class="icon-coin"></i> {lang_entry key="files.action.unpartner"}</a></li>
				<li class="count be-count file-action" id="cb-promote"><a href="javascript:;"><i class="icon-bullhorn"></i> {lang_entry key="files.action.promote"}</a></li>
				<li class="count be-count file-action" id="cb-unpromote"><a href="javascript:;"><i class="icon-bullhorn"></i> {lang_entry key="files.action.unpromote"}</a></li>
			    {/if}
				<li class="count be-count file-action" id="cb-feature"><a href="javascript:;"><i class="icon-star"></i> {lang_entry key="files.action.feature"}</a></li>
				<li class="count be-count file-action" id="cb-unfeature"><a href="javascript:;"><i class="icon-star"></i> {lang_entry key="files.action.unfeature"}</a></li>
			    {if $mm_entry eq "backend-menu-entry10"}
				<li class="count be-count file-action" id="cb-verify"><a href="javascript:;"><i class="icon-envelope"></i> {lang_entry key="account.action.em.ver"}</a></li>
				<li class="count be-count file-action" id="cb-unverify"><a href="javascript:;"><i class="icon-envelope"></i> {lang_entry key="account.action.em.unver"}</a></li>
				<li class="count be-count file-action" id="cb-ban"><a href="javascript:;"><i class="icon-blocked"></i> {lang_entry key="account.action.ip.ban"}</a></li>
				<li class="count be-count file-action" id="cb-unban"><a href="javascript:;"><i class="icon-blocked"></i> {lang_entry key="account.action.ip.unban"}</a></li>
				<li class="count be-count file-action" id="cb-email"><a href="javascript:;"><i class="icon-envelope"></i> {lang_entry key="account.btn.send.email"}</a></li>
				<li class="count be-count file-action" id="cb-delete"><a href="javascript:;"><i class="icon-times"></i> {lang_entry key="frontend.global.delete.sel"}</a></li>
			    {/if}
			    {if $page_display eq "backend_tpl_files"}
				<li class="count be-count file-action" id="cb-promote"><a href="javascript:;"><i class="icon-bullhorn"></i> {lang_entry key="files.action.promote"}</a></li>
				<li class="count be-count file-action" id="cb-unpromote"><a href="javascript:;"><i class="icon-bullhorn"></i> {lang_entry key="files.action.unpromote"}</a></li>
				<li class="count be-count file-action" id="cb-public"><a href="javascript:;"><i class="icon-globe"></i> {lang_entry key="files.action.public"}</a></li>
				<li class="count be-count file-action" id="cb-private"><a href="javascript:;"><i class="icon-key"></i> {lang_entry key="files.action.private"}</a></li>
				<li class="count be-count file-action" id="cb-personal"><a href="javascript:;"><i class="icon-lock"></i> {lang_entry key="files.action.personal"}</a></li>
				<li class="count be-count file-action" id="cb-unflag"><a href="javascript:;"><i class="icon-flag"></i> {lang_entry key="files.action.flagged"}</a></li>
				<li class="count be-count file-action" id="cb-del-files"><a href="javascript:;"><i class="icon-times"></i> {lang_entry key="files.action.del.files"}</a></li>
			    {/if}
			    {elseif $page_display eq "tpl_files" or $page_display eq "tpl_subs"}
			    {if $mm_entry eq "file-menu-entry7"}
				<li class="count file-action" id="cb-enable"><a href="javascript:;"><i class="icon-check"></i> {lang_entry key="contacts.invites.approve"}</a></li>
				<li class="count file-action" id="cb-disable"><a href="javascript:;"><i class="icon-stop"></i> {lang_entry key="contacts.comments.approve"}</a></li>
				<li class="count file-action" id="cb-commdel"><a href="javascript:;"><i class="icon-times"></i> {lang_entry key="frontend.global.delete.sel"}</a></li>
			    {elseif $mm_entry eq "file-menu-entry8"}
				<li class="count file-action" id="cb-renable"><a href="javascript:;"><i class="icon-check"></i> {lang_entry key="contacts.invites.approve"}</a></li>
				<li class="count file-action" id="cb-rdisable"><a href="javascript:;"><i class="icon-stop"></i> {lang_entry key="contacts.comments.approve"}</a></li>
				<li class="count file-action" id="cb-rdel"><a href="javascript:;"><i class="icon-times"></i> {lang_entry key="frontend.global.delete.sel"}</a></li>
			    {else}
				{if $file_privacy eq "1" and $mm_entry eq "file-menu-entry1"}
				<li class="count file-action" id="cb-private"><a href="javascript:;"><i class="icon-key"></i> {lang_entry key="files.action.private"}</a></li>
				<li class="count file-action" id="cb-public"><a href="javascript:;"><i class="icon-globe"></i> {lang_entry key="files.action.public"}</a></li>
				<li class="count file-action" id="cb-personal"><a href="javascript:;"><i class="icon-lock"></i> {lang_entry key="files.action.personal"}</a></li>
				{/if}
				{if $file_favorites eq "1"}
				{if $mm_entry ne "file-menu-entry2"}
				<li class="count file-action" id="cb-favadd"><a href="javascript:;"><i class="icon-heart"></i> {lang_entry key="files.action.fav.add"}</a></li>
				{/if}
				<li class="count file-action" id="cb-favclear"><a href="javascript:;"><i class="icon-heart"></i> {lang_entry key="files.action.fav.clear"}</a></li>
				{/if}
				{if $file_playlists eq "1"}
				{insert name="addToLabel" assign="addToLabel" for="tpl_pl"}{$addToLabel}
				{/if}
			    {/if}
			    {else}
				{if $mm_entry ne "message-menu-entry2" and $mm_entry ne "message-menu-entry5" and $mm_entry ne "message-menu-entry7"}<li class="count" id="cb-enable"><a href="javascript:;"><i class="icon-check"></i> {if $mm_entry eq "message-menu-entry6"}{lang_entry key="msg.entry.not.spam"}{else}{lang_entry key="frontend.global.enable.sel"}{/if}</a></li>{/if}
				{if $mm_entry ne "message-menu-entry6" and $mm_entry ne "message-menu-entry5" and $mm_entry ne "message-menu-entry7"}<li class="count" id="cb-disable"><a href="javascript:;"><i class="icon-spam"></i> {if $page_display eq "tpl_messages" and $mm_entry eq "message-menu-entry2"}{lang_entry key="msg.details.spam.capital"}{else}{lang_entry key="frontend.global.disable.sel"}{/if}</a></li>{/if}
				{if $mm_entry ne "message-menu-entry2" and $mm_entry ne "message-menu-entry6" and $mm_entry ne "message-menu-entry5" and $mm_entry ne "message-menu-entry7"}<li class="count be-count file-action" id="cb-delete"><a href="javascript:;"><i class="icon-times"></i> {lang_entry key="frontend.global.delete.sel"}</a></li>{/if}
			    {/if}
			    {if $page_display eq "tpl_messages" and $custom_labels eq 1}{insert name="addToLabel" assign="addToLabel" for="{$mm_entry}"}
				{$addToLabel}
			    {/if}
			    {if $mm_entry eq "message-menu-entry7"}
			    {if $user_friends eq 1}
				<li class="count" id="cb-addfr"><a href="javascript:;"><i class="icon-users5"></i> {lang_entry key="msg.friend.add"}</a></li>
			    {/if}
			    {if $internal_messaging eq 1 and $allow_multi_messaging eq 1}
				<li class="count" id="cb-sendmsg"><a href="javascript:;"><i class="icon-envelope"></i> {lang_entry key="msg.friend.message"}</a></li>
			    {/if}
			    {if $user_blocking eq 1}
				<li class="count" id="cb-block"><a href="javascript:;"><i class="icon-blocked"></i> {lang_entry key="msg.block.sel"}</a></li>
				<li class="count" id="cb-unblock"><a href="javascript:;"><i class="icon-blocked"></i> {lang_entry key="msg.unblock.sel"}</a></li>
			    {/if}
			    {/if}
			    {if $mm_entry ne "message-menu-entry4" and $page_display ne "tpl_subs"}
				{if ($page_display eq "tpl_files" and $file_deleting eq "1" and $mm_entry eq "file-menu-entry1") or ($page_display ne "tpl_files" and $page_display ne "backend_tpl_adv" and $page_display ne "backend_tpl_files" and $mm_entry ne "backend-menu-entry10")}
				<li class="count file-action{if $page_display ne "tpl_messages" and $mm_entry ne "message-menu-entry4"} hidden{/if}" id="cb-delete"><a href="javascript:;"><i class="icon-times"></i> {lang_entry key="frontend.global.delete.sel"}</a></li>
				{elseif ($page_display eq "tpl_files" and $mm_entry eq "file-menu-entry3")}
				<li class="count file-action" id="cb-likeclear"><a href="javascript:;"><i class="icon-thumbs-up"></i> {lang_entry key="files.action.liked.clear"}</a></li>
				{elseif ($page_display eq "tpl_files" and $mm_entry eq "file-menu-entry4")}
				<li class="count file-action" id="cb-histclear"><a href="javascript:;"><i class="icon-history"></i> {lang_entry key="files.action.hist.clear"}</a></li>
				{elseif ($page_display eq "tpl_files" and $mm_entry eq "file-menu-entry5")}
				<li class="count file-action" id="cb-watchclear"><a href="javascript:;"><i class="icon-clock"></i> {lang_entry key="files.action.watch.clear"}</a></li>
				{/if}
			    {/if}
			    </ul>
		    </div>
		</div>
		{/if}
	    {if $mm_entry eq "file-menu-entry6"}
		<div class="left-float left-margin5">
		    <ul class="ul-no-list">
			<li>
			    <button name="save_new_pl" id="save-new-pl" class="new-label general-button form-button" type="button" onfocus="blur();">
				{lang_entry key="files.action.new"}
			    </button>
			    <ul>
			    {include file="tpl_frontend/tpl_file/tpl_addplaylist.tpl"}
			    </ul>
			</li>
		    </ul>
		</div>
	    {/if}
	    {if $smarty.get.s eq "backend-menu-entry10-sub2" and $smarty.get.do ne "add"}
	    	{generate_html type="user-type-actions-be"}
	    {/if}

	    {if $page_display eq "backend_tpl_files"}
	    	{if $smarty.get.s eq "backend-menu-entry6-sub1"}
	    		{assign var=ft value="{lang_entry key="frontend.global.v.c"}"}
	    		{assign var=uk value="video"}
	    	{elseif $smarty.get.s eq "backend-menu-entry6-sub7"}
	    		{assign var=ft value="{lang_entry key="frontend.global.s.c"}"}
	    		{assign var=uk value="short"}
	    	{elseif $smarty.get.s eq "backend-menu-entry6-sub2"}
	    		{assign var=ft value="{lang_entry key="frontend.global.i.c"}"}
	    		{assign var=uk value="image"}
	    	{elseif $smarty.get.s eq "backend-menu-entry6-sub3"}
	    		{assign var=ft value="{lang_entry key="frontend.global.a.c"}"}
	    		{assign var=uk value="audio"}
	    	{elseif $smarty.get.s eq "backend-menu-entry6-sub4"}
	    		{assign var=ft value="{lang_entry key="frontend.global.d.c"}"}
	    		{assign var=uk value="document"}
	    	{elseif $smarty.get.s eq "backend-menu-entry6-sub5"}
	    		{assign var=ft value="{lang_entry key="frontend.global.b.c"}"}
	    		{assign var=uk value="blog"}
	    	{elseif $smarty.get.s eq "backend-menu-entry6-sub6"}
	    		{assign var=ft value="{lang_entry key="frontend.global.l.c"}"}
	    		{assign var=uk value="live"}
	    	{/if}
	    	<div class="" id="add-new-entry">
	    		<button name="new_entry" id="new-entry" onclick="" class="save-entry-button button-grey search-button form-button new-upload" type="button" value="1" onfocus="blur();"><span>{assign var=x value="{lang_entry key="files.menu.add.new"}"}{$x|replace:"##TYPE##":$ft}</span></button>
	    	</div>
	    	<script type="text/javascript">
	    		$(document).ready(function() {ldelim}
	    			$("#add-new-entry").prependTo("#ct-wrapper");
	    			$("#new-entry").on("click", function() {ldelim}
	    				{if $smarty.get.s eq "backend-menu-entry6-sub5"}
	    					wrapLoad(current_url + "{href_entry key="be_files"}?s=backend-menu-entry6-sub5&do=new-blog&t=blog", "blog");
	    				{elseif $smarty.get.s eq "backend-menu-entry6-sub6"}
	    					wrapLoad(current_url + "{href_entry key="be_files"}?s=backend-menu-entry6-sub6&do=new-broadcast&t=live", "live");
	    				{else}
	    					window.location = current_url + "{href_entry key="be_upload"}?t={$uk}";
	    				{/if}
	    			{rdelim});
	    		{rdelim});
	    	</script>
	    {/if}

	    {if $page_display ne "tpl_files" and $page_display ne "tpl_subs" and $page_display ne "backend_tpl_adv" and $page_display ne "backend_tpl_files"}
	    	<div class="place-left-off" id="add-new-entry">
			{if $mm_entry ne "message-menu-entry3" and $mm_entry ne "message-menu-entry4" and $page_display ne "tpl_files"}
			    {if $page_display eq "backend_tpl_members"}
				{if $smarty.get.do eq "add"}{assign var=b_c value="new-entry"}
				{else}{assign var=b_c value="add-button new-entry"}
				{/if}
			    {elseif $page_display eq "tpl_messages"}
				{assign var=b_c value="new-label save-entry-button"}
			    {elseif $page_display eq "backend_tpl_categ" or $page_display eq "backend_tpl_lang" or $page_display eq "backend_tpl_servers" or $page_display eq "backend_tpl_streaming"}
				{assign var=b_c value="new-entry"}
			    {elseif $page_display eq "backend_tpl_banners" or $page_display eq "backend_tpl_jwads" or $page_display eq "backend_tpl_jwcodes" or $page_display eq "backend_tpl_fpads"}
				{assign var=b_c value="new-entry"}
			    {else}
				{assign var=b_c value="save-button button-blue save-entry-button"}
			    {/if}

				{if $mm_entry eq "message-menu-entry7"}
					<button {if $get_s ne "message-menu-entry7"}disabled="disabled"{/if} name="new_contact" id="new-contact" onclick="$('#add-new-label').stop().slideUp('fast'); $('#ct-contact-add-wrap').stop().slideToggle('fast');" class="save-entry-button button-grey search-button form-button new-contact" type="button" value="1" onfocus="blur();"><span>{lang_entry key="contact.add.new"}</span></button>
				{/if}

			    
			    {if $page_display eq "tpl_messages" and $internal_messaging eq 1}
			    	<button type="button" class="new-message-button save-entry-button button-grey search-button form-button" name="compose-button" id="compose-button"><span>{lang_entry key="msg.btn.compose"}</span></button>
			    {/if}

			    <button name="save_changes" {if $page_display eq "tpl_messages"}id="new-label" {/if}class="{if $page_display eq "tpl_messages" and $custom_labels eq 0}no-display {/if}{if $smarty.get.do eq "add"}save-entry-button {/if}button-grey search-button form-button {$b_c}" type="button" value="1" onfocus="blur();" {if ($page_display eq "tpl_messages" and ($show_new_label eq "no" or $custom_labels eq 0))}disabled="disabled"{/if}>
			    	<span>
				{if $page_display eq "backend_tpl_members"}
				    {if $smarty.get.do eq "add"}{lang_entry key="frontend.global.savenew"}
				    {else}{lang_entry key="frontend.global.addnew"}{/if}
				{elseif $page_display eq "tpl_messages"}
				    {lang_entry key="frontend.global.newlabel"}
				{elseif $page_display eq "backend_tpl_categ" or $page_display eq "backend_tpl_lang" or $page_display eq "backend_tpl_servers" or $page_display eq "backend_tpl_streaming"}
				    {if $smarty.get.do eq "add"}{lang_entry key="frontend.global.savenew"}
				    {else}{lang_entry key="frontend.global.addnew"}{/if}
				{elseif $page_display eq "backend_tpl_banners" or $page_display eq "backend_tpl_jwads" or $page_display eq "backend_tpl_jwcodes" or $page_display eq "backend_tpl_fpads"}
				    {if $smarty.get.do eq "add"}{lang_entry key="frontend.global.savenew"}
				    {else}{lang_entry key="frontend.global.addnew"}{/if}
				{else}
				    {lang_entry key="frontend.global.savechanges"}
				{/if}
				</span>
			    </button>
			{/if}

			{if $page_display eq "tpl_messages" and $custom_labels eq 1}{include file="tpl_frontend/tpl_msg/tpl_addlabel.tpl"}{/if}

	    {if $page_display eq "tpl_messages" and $show_new_label eq "no" and $custom_labels eq 1 and $get_s ne "message-menu-entry7-sub1" and $get_s ne "message-menu-entry7-sub2"}
	    	    <button onclick="$('#rename-label-form').toggle();$('#current-label-name').focus();" value="1" type="button" class="button-grey search-button form-button rename-label save-entry-button" id="rename-label" name="rename_label"><span>{lang_entry key="label.rename.new"}</span></button>
		    <form id="rename-label-form" method="post" action="" class="entry-form-class" style="display:none">
			<div id="label-rename-wrap">
			<input type="text" name="current_label_name" id="current-label-name" value="{insert name="getLabelName" assign=label_name}{$label_name}" class="login-input" size="20" />
			<button name="rename_label_btn" id="rename-label-btn" class="search-button no-display" type="button" value="1">{lang_entry key="frontend.global.rename"}</button>
			</div>
		    </form>
		<script type="text/javascript">$(document).ready(function() {ldelim}$("#rename-label-btn").click(function(){ldelim}$.post(current_url + menu_section + '?s={$get_s}&do=rename', $("#rename-label-form").serialize(), function( data ) {ldelim}wrapLoad(current_url + menu_section + '?s={$get_s}', fe_mask); $(".dcjq-parent.active span.mm").text($("#current-label-name").val());{rdelim}); {rdelim});enterSubmit("#rename-label-form input", "#rename-label-btn"); {rdelim});</script>
	    {/if}
			
		</div>
	    {/if}
				{if $mm_entry eq "message-menu-entry7"}
					<div id="ct-contact-add-wrap" class="place-left" style="display: none;">{include file="tpl_frontend/tpl_msg/tpl_contact_add.tpl"}</div>
				{/if}
	    
	    {if $page_display eq "backend_tpl_files"}
		<div class="sorting">
            	    <form id="sort-file-actions" class="entry-form-class" method="post" action="">
                	{if $smarty.get.s ne "backend-menu-entry6-sub2" and $smarty.get.s ne "backend-menu-entry6-sub3" and $smarty.get.s ne "backend-menu-entry6-sub4" and $smarty.get.s ne "backend-menu-entry6-sub5" and $smarty.get.s ne "backend-menu-entry6-sub6" and $categ_display ne "i" and $categ_display ne "a" and $categ_display ne "d" and $categ_display ne "b" and $categ_display ne "l"}{generate_html type="file-type-actions-be"}{/if}
                	{generate_html type="file-time-actions-be"}
            	    </form>
        	</div>
	    {/if}

	{/if}

	{include file="tpl_backend/tpl_settings/ct-save-top-js.tpl"}
