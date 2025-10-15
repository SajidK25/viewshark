			{if ($page_display eq "backend_tpl_servers" or $page_display eq "backend_tpl_members" or $page_display eq "backend_tpl_categ" or $page_display eq "backend_tpl_streaming" or $page_display eq "backend_tpl_banners" or $page_display eq "backend_tpl_lang" or $page_display eq "backend_tpl_jwcodes" or $page_display eq "backend_tpl_jwads" or $page_display eq "backend_tpl_fpads") and $smarty.get.do eq "add"}{assign var=cl_display value="none"}{assign var=ca_display value="block"}{else}{assign var=cl_display value="block"}{assign var=ca_display value="none"}{/if}
			<div id="cancel-link" style="display: {$ca_display};">
				<a href="javascript:;" class="cancel-trigger"><span>{lang_entry key="frontend.global.cancel"}</span></a>
			</div>
