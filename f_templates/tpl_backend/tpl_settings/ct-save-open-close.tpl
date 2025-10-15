		{insert name=currentMenuEntry assign=mm_entry for=$smarty.get.s|sanitize}
		{if ($page_display eq "backend_tpl_servers" or $page_display eq "backend_tpl_members" or $page_display eq "backend_tpl_categ" or $page_display eq "backend_tpl_banners" or $page_display eq "backend_tpl_lang" or $page_display eq "backend_tpl_jwcodes" or $page_display eq "backend_tpl_jwads" or $page_display eq "backend_tpl_fpads") and $smarty.get.do eq "add"}{assign var=cl_display value="none"}{assign var=ca_display value="block"}{else}{assign var=cl_display value="block"}{assign var=ca_display value="none"}{/if}
		<div id="open-close-links" style="display: {$cl_display};">
		    <div class="open-close-links right-float font12 bottom-padding5{if $global_section eq "backend"} right-padding10 top-padding3{/if}" style="float: right;">
			{if $smarty.get.s eq "backend-menu-entry3-sub14"}
			<span title="Resume transfers" rel="tooltip" class="{if $pause_video_transfer eq 0}no-display{/if}"><i class="icon-play resume-mode"></i></span>
			<span title="Pause transfers" rel="tooltip" class="{if $pause_video_transfer eq 1}no-display{/if}"><i class="icon-pause pause-mode"></i></span>
                        {/if}
			{if $smarty.get.s eq "backend-menu-entry3-sub15"}
			<span title="Resume transfers" rel="tooltip" class="{if $pause_image_transfer eq 0}no-display{/if}"><i class="icon-play resume-mode"></i></span>
			<span title="Pause transfers" rel="tooltip" class="{if $pause_image_transfer eq 1}no-display{/if}"><i class="icon-pause pause-mode"></i></span>
                        {/if}
			{if $smarty.get.s eq "backend-menu-entry3-sub16"}
			<span title="Resume transfers" rel="tooltip" class="{if $pause_audio_transfer eq 0}no-display{/if}"><i class="icon-play resume-mode"></i></span>
			<span title="Pause transfers" rel="tooltip" class="{if $pause_audio_transfer eq 1}no-display{/if}"><i class="icon-pause pause-mode"></i></span>
                        {/if}
			{if $smarty.get.s eq "backend-menu-entry3-sub17"}
			<span title="Resume transfers" rel="tooltip" class="{if $pause_doc_transfer eq 0}no-display{/if}"><i class="icon-play resume-mode"></i></span>
			<span title="Pause transfers" rel="tooltip" class="{if $pause_doc_transfer eq 1}no-display{/if}"><i class="icon-pause pause-mode"></i></span>
                        {/if}
                        <span title="{lang_entry key="frontend.global.closeall"}" rel="tooltip"><i class="iconBe-popin icon-contract2" id="all-close{if $mm_entry eq "message-menu-entry7"}-ct{/if}"></i></span>
		    	<span title="{lang_entry key="frontend.global.openall"}" rel="tooltip"><i class="iconBe-popout icon-expand2" id="all-open{if $mm_entry eq "message-menu-entry7"}-ct{/if}"></i></span>
		    	{if $page_display eq "tpl_account"}
		    		<span title="{lang_entry key="frontend.global.savechanges"}" rel="tooltip"><i class="iconBe-floppy-disk" id="all-save" onclick="$('.sortings .save-entry-button').click();"></i></span>
		    	{/if}
		    	{if $page_display eq "tpl_import"}
		    		<span title="{lang_entry key="backend.import.feed.import"}" rel="tooltip"><i class="iconBe-floppy-disk" id="all-save" onclick="$('.sortings .save-videos-button').click();"></i></span>
		    	{/if}
		    </div>
		</div>
		{if $mm_entry eq "backend-menu-entry6" or $mm_entry eq "backend-menu-entry11"}
		    <input type="hidden" id="p-user-key" name="p_user_key" value="{$smarty.get.u|sanitize}" />
		{/if}