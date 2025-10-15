                        <link rel="stylesheet" type="text/css" href="{$styles_url}/view.min.css">
    <div class="left-float wdmax">
	<div id="overview-userinfo">
	    <div class="statsBox">
		<article class="no-display">
                	<h3 class="content-title"><i class="icon-user"></i>{lang_entry key="account.entry.account.overview"}</h3>
                	<div class="line"></div>
        	</article>
			<div class="swiper-ph swiper-ph-tnav"></div>
			<div class="swiper-top swiper-top-tnav" style="display:{$ssd}">
				<div class="swiper-button-prev swiper-button-prev-tnav"{$sso}></div>
				<div class="swiper-button-next swiper-button-next-tnav"{$sso}></div>
				<div class="swiper swiper-tnav">
					<div class="swiper-wrapper">
                            	<div id="account-menu-entry13" class="swiper-slide{if $smarty.get.rg eq "" and $smarty.get.rp eq ""} swiper-slide-current{/if}" rel-m="{href_entry key="subscribers"}"><a class="{if $smarty.get.rg eq "" and $smarty.get.rp eq ""}dcjq-parent active{/if}" href="{$main_url}/{href_entry key="subscribers"}"><span><i class="icon-user"></i> {lang_entry key="account.entry.overview"}</span></a></div>
                            	{if $smarty.session.USER_PARTNER eq 1}
                            	<div id="account-menu-entry9" class="swiper-slide{if $smarty.get.rg ne ""} swiper-slide-current{/if}" rel-m="{href_entry key="subscribers"}"><a class="{if $smarty.get.rg ne ""}dcjq-parent active{/if}" href="{$main_url}/{href_entry key="subscribers"}?rg={$smarty.session.USER_KEY|md5}"><span><i class="icon-bars"></i> {lang_entry key="account.entry.act.graph"}</span></a></div>
                            	<div id="account-menu-entry12" class="swiper-slide{if $smarty.get.rp ne ""} swiper-slide-current{/if}" rel-m="{href_entry key="subscribers"}"><a class="{if $smarty.get.rp ne ""}dcjq-parent active{/if}" href="{$main_url}/{href_entry key="subscribers"}?rp={$smarty.session.USER_KEY|md5}"><span><i class="icon-paypal"></i> {lang_entry key="account.entry.payout.rep"}</span></a></div>
                            	{/if}
					</div>
				</div>
			</div>
		<div class="vs-column fourths">
		    <div class="user-thumb-xlarge">
			<div><center><a href="{$main_url}/@{$smarty.session.USER_NAME}"><img id="own-profile-image" title="{$smarty.session.USER_NAME}" alt="{$smarty.session.USER_NAME}" src="{insert name="getProfileImage" assign="profileImage" for="{$smarty.session.USER_ID}"}{$profileImage}"></a></center></div>
		    </div>
		    <div class="imageChange">
		    <form method="post" action="" class="entry-form-class overview-form">
                        <center>
                                {if !$smarty.session.USER_PARTNER and $smarty.session.USER_PARTNER_REQUEST}
                                <button class="save-entry-button button-grey search-button form-button usr-delete" type="button" onclick="$('.partner-request-popup').trigger('click');"><span>{lang_entry key="account.entry.btn.request.prt"}</span></button>
                                <a href="javascript:;" rel="popuprel" class="partner-request-popup hidden">{lang_entry key="account.entry.btn.request.prt"}</a>
                                {elseif $smarty.session.USER_PARTNER}
                                <button class="save-entry-button button-grey search-button form-button purge-button" type="button" onclick="$('.partner-cancel-popup').trigger('click');"><span>{lang_entry key="account.entry.btn.terminate.prt"}</span></button>
                                <a href="javascript:;" rel="popuprel" class="partner-cancel-popup hidden">{lang_entry key="account.entry.btn.terminate.prt"}</a>
                                {/if}
                        </center>
                    </form>
                    </div>
		    <div class="popupbox" id="popuprel"></div>
		    <div id="fade"></div>
		</div>
		
		<div class="vs-column three_fourths fit">
			{insert name="getUserStats" type="subs"}
		</div>
	    </div>
			<div class="clearfix"></div>
	</div>
    </div>
<script type="text/javascript">
    $(document).on("click", ".partner-request-popup", function() {ldelim}
	af_url = current_url + menu_section + "?s=account-menu-entry1&do=make-partner";
	$.fancybox({ldelim} type: "ajax", minWidth: "80%", margin: 20, href: af_url {rdelim});
    {rdelim});
{if $smarty.session.USER_PARTNER eq 1}
    $(function() {ldelim}SelectList.init("user_partner_badge");{rdelim});
    $(document).on("click", ".partner-cancel-popup", function() {ldelim}
	af_url = current_url + menu_section + "?s=account-menu-entry1&do=clear-partner";
	$.fancybox({ldelim} type: "ajax", minWidth: "80%", margin: 20, href: af_url {rdelim});
    {rdelim});
    $(document).on("change", ".badge-select-input", function() {ldelim}
        v = $(this).val();
        h = '<i id="affiliate-icon" class="'+v+'"></i>';
        $("#affiliate-icon").replaceWith(h);
        if (v == "") $("#affiliate-icon").hide();
        af_url = current_url + menu_section + "?s=account-menu-entry13&do=save-subscriber";
        $.post(af_url, $("#ct-set-form").serialize(), function(data) {ldelim}
                $("#affiliate-response").html(data);
        {rdelim});
    {rdelim});
    $(document).on("keydown", ".user-partner-paypal", function(e) {ldelim}
        var code = e.which;
        if (code == 13) {ldelim}
                e.preventDefault();
                af_url = current_url + menu_section + "?s=account-menu-entry13&do=save-subscriber";
                $.post(af_url, $("#ct-set-form").serialize(), function(data) {ldelim}
                        $("#affiliate-response").html(data);
                {rdelim});
        {rdelim}
    {rdelim});
{/if}
</script>
{insert name="swiperJS" for="tnav"}