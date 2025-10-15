			    {if $smarty.session.signup_pack ne ""}
        			{assign var=pack_value value=$smarty.post.frontend_membership_type|sanitize}
    			    {elseif $smarty.post.frontend_membership_type}
        			{assign var=pack_value value=$smarty.post.frontend_membership_type|sanitize}
    			    {else}
    				{if $smarty.get.p|escape:'b64d' gt 0}
    				    {assign var=pack_value value="{lang_entry key="frontend.membership.type"}: {$pk_info[0].pk_name}"}
    				{else}
        		    	    {assign var=pack_value value="{lang_entry key="frontend.membership.type"}"}
        		        {/if}
    			    {/if}

				<div class="row">
					<label>{lang_entry key="frontend.membership.type.sel"}</label>
				    <div class="input-signup">
					<div id="membership_types" class="selector">
					    <input type="hidden" {$ro} name="frontend_membership_type" class="login-input" id="pack-loc" value="{$pack_value}" />
                                    	    <input type="hidden" {$ro} name="frontend_membership_type_tmp" class="login-input no-display" value="{$pack_value}" />
					    <select class="{if $renew eq "1"}renew-select{else}signup-select{/if}" name="frontend_membership_type_sel" id="frontend_membership_type" {$l_disabled} onchange="$('#pack-loc').val('{lang_entry key="frontend.membership.type"}: '+this.options[this.selectedIndex].text);">
					    {if $smarty.get.p eq "" and $smarty.get.u eq ""}
						<option value="">---</option>
					    {/if}
					    {section name=p loop=$memberships}
						<option value="{$memberships[p].pk_id}" {if $smarty.session.signup_pack eq $memberships[p].pk_id or $pk_info[0].pk_name eq $memberships[p].pk_name}selected="selected"{elseif $smarty.post.frontend_membership_type_sel eq $memberships[p].pk_id or $smarty.get.p|escape:'b64d' eq $memberships[p].pk_id}selected="selected"{/if}>{$memberships[p].pk_name}</option>
					    {/section}
					    </select>
					</div>
				    </div>
				</div>
				<div class="row">
				    <div id="membership-wrapper">
					<div id="membership_info">
					    <div id="membership_entry" style="display: none;"></div>
					{section name=q loop=$memberships}
					    <div id="membership_entry{$memberships[q].pk_id}" style="display: {if $smarty.session.signup_pack eq $memberships[q].pk_id or $smarty.post.frontend_membership_type_sel eq $memberships[q].pk_id or $pk_info[0].pk_name eq $memberships[q].pk_name or $smarty.get.p|escape:'b64d' eq $memberships[q].pk_id}block{else}none{/if};">
						<div class="pk-descr">{$memberships[q].pk_descr}</div>
						<div>
						    <ul class="top-padding10 ul-disc-list">
							<li>{insert name=sizeFormat assign=pk_space size=$memberships[q].pk_space}{if $memberships[q].pk_space eq "0"}{lang_entry key="frontend.pkinfo.unlimited"}{else}<span class="bold">{$pk_space}</span>{/if}{lang_entry key="frontend.pkinfo.upspace"}</li>
							<li>{insert name=sizeFormat assign=pk_bw size=$memberships[q].pk_bw}{if $memberships[q].pk_bw eq "0"}{lang_entry key="frontend.pkinfo.unlimited"}{else}<span class="bold">{$pk_bw}</span>{/if}{lang_entry key="frontend.pkinfo.bwspace"}</li>
							{if $memberships[q].pk_llimit gt 0 and $live_module eq "1"}<li><span class="bold">{$memberships[q].pk_llimit}</span>{lang_entry key="frontend.pkinfo.liveallow"}</li>{elseif $memberships[q].pk_llimit eq 0 and $live_module eq "1"}<li>{lang_entry key="frontend.pkinfo.unlimited"}{lang_entry key="frontend.pkinfo.liveallow"}</li>{/if}
                                                        {if $memberships[q].pk_vlimit gt 0 and $video_module eq "1"}<li><span class="bold">{$memberships[q].pk_vlimit}</span>{lang_entry key="frontend.pkinfo.vidallow"}</li>{elseif $memberships[q].pk_vlimit eq 0 and $video_module eq "1"}<li>{lang_entry key="frontend.pkinfo.unlimited"}{lang_entry key="frontend.pkinfo.vidallow"}</li>{/if}
							{if $memberships[q].pk_ilimit gt 0 and $image_module eq "1"}<li><span class="bold">{$memberships[q].pk_ilimit}</span>{lang_entry key="frontend.pkinfo.imgallow"}</li>{elseif $memberships[q].pk_ilimit eq 0 and $image_module eq "1"}<li>{lang_entry key="frontend.pkinfo.unlimited"}{lang_entry key="frontend.pkinfo.imgallow"}</li>{/if}
							{if $memberships[q].pk_alimit gt 0 and $audio_module eq "1"}<li><span class="bold">{$memberships[q].pk_alimit}</span>{lang_entry key="frontend.pkinfo.audallow"}</li>{elseif $memberships[q].pk_alimit eq 0 and $audio_module eq "1"}<li>{lang_entry key="frontend.pkinfo.unlimited"}{lang_entry key="frontend.pkinfo.audallow"}</li>{/if}
                                                        {if $memberships[q].pk_blimit gt 0 and $blog_module eq "1"}<li><span class="bold">{$memberships[q].pk_blimit}</span>{lang_entry key="frontend.pkinfo.blogallow"}</li>{elseif $memberships[q].pk_blimit eq 0 and $blog_module eq "1"}<li>{lang_entry key="frontend.pkinfo.unlimited"}{lang_entry key="frontend.pkinfo.blogallow"}</li>{/if}
							{if $memberships[q].pk_dlimit gt 0 and $document_module eq "1"}<li><span class="bold">{$memberships[q].pk_dlimit}</span>{lang_entry key="frontend.pkinfo.docallow"}</li>{elseif $memberships[q].pk_dlimit eq 0 and $document_module eq "1"}<li>{lang_entry key="frontend.pkinfo.unlimited"}{lang_entry key="frontend.pkinfo.docallow"}</li>{/if}
							<li>{if $memberships[q].pk_price eq "0"}{lang_entry key="frontend.pkinfo.freereg"}{lang_entry key="frontend.pkinfo.regactive"}<span class="bold">{$memberships[q].pk_period}</span> {lang_entry key="frontend.global.days"}{else}{lang_entry key="frontend.pkinfo.costreg"}<span class="bold">{$memberships[q].pk_priceunit}{$memberships[q].pk_price}</span>{lang_entry key="frontend.pkinfo.regactive"}<span class="bold">{$memberships[q].pk_period}</span> {lang_entry key="frontend.global.days"}{/if}</li>
						    </ul>
						</div>
					    </div>
					{/section}
					</div>
				    </div>
				</div>
				<script type="text/javascript">
				    $(document).ready(function () {ldelim}
					$("#frontend_membership_type").bind("change", function () {ldelim}
					    var pk_val = $(this).val();
					{section name=j loop=$memberships}
					    $("#membership_entry"+pk_val).stop().slideDown();
					    if (pk_val != "{$memberships[j].pk_id}") $("#membership_entry{$memberships[j].pk_id}").stop().slideUp();
					{/section}
					{rdelim});
				    {rdelim});
				</script>
