{if $smarty.session.USER_ID gt 0}
<div class="tacc-img">
	<img height="40" class="own-profile-image mt-off" title="{$smarty.session.USER_NAME}" alt="{$smarty.session.USER_NAME}" src="{insert name="getProfileImage" assign="profileImage" for="{$smarty.session.USER_ID}"}{$profileImage}">
</div>
<div class="tacc-nfo">
        <span>
                <span class="tacc-uu">{if $smarty.session.USER_ID gt 0}<a href="{$main_url}/@{$smarty.session.USER_NAME}">{if $smarty.session.USER_DNAME ne ''}{$smarty.session.USER_DNAME}{else}{$smarty.session.USER_NAME}{/if}</a>{else}{lang_entry key="frontend.global.guest.rand"}{/if}</span>
                <span class="tacc-us">
                {if $user_follows}
                	<span class="r2">{if $smarty.session.USER_ID gt 0}{insert name="getFollowCount"}{else}0{/if} <label>{lang_entry key="frontend.global.followers"}</label></span>
                {/if}
                {if $user_subscriptions}
                	<span class="r1">{if $smarty.session.USER_ID gt 0}{insert name="getSubCount"}{else}0{/if} <label>{lang_entry key="frontend.global.subscribers"}</label></span>
                {/if}
                </span>
        </span>
</div>
<div class="clearfix"></div>
{/if}

<ul class="accordion tacc" id="top-session-accordion">
{if $smarty.session.USER_ID gt 0}
<li class=""><a class="dcjq-parent" href="{$main_url}/{href_entry key="files"}"><i class="icon-upload"></i> {lang_entry key="subnav.entry.files.my"}</a></li>
{if $user_subscriptions eq 1}
<li class=""><a class="dcjq-parent" href="{$main_url}/{href_entry key="subscribers"}"><i class="icon-coin"></i> {lang_entry key="subnav.entry.subpanel"}</a></li>
{/if}
{if $user_tokens eq 1}
<li class=""><a class="dcjq-parent" href="{$main_url}/{href_entry key="tokens"}"><i class="icon-coin"></i> {lang_entry key="subnav.entry.tokenpanel"}</a></li>
{/if}
<li class=""><a class="dcjq-parent" href="{$main_url}/{href_entry key="account"}"><i class="icon-user"></i> {lang_entry key="subnav.entry.account.my"}</a></li>
<li class=""><a class="dcjq-parent" href="{$main_url}/{href_entry key="manage_channel"}"><i class="icon-settings"></i> {lang_entry key="subnav.entry.channel.my"}</a></li>
<li class=""><a class="dcjq-parent" href="{$main_url}/{if $internal_messaging eq 1 and ($user_friends eq 1 or $user_blocking eq 1)}{href_entry key="messages"}{elseif $internal_messaging eq 0 and ($user_friends eq 1 or $user_blocking eq 1)}{href_entry key="contacts"}{/if}"><i class="icon-envelope"></i> {if $internal_messaging eq 1 and ($user_friends eq 1 or $user_blocking eq 1)}{lang_entry key="subnav.entry.contacts.messages"}{elseif $internal_messaging eq 0 and ($user_friends eq 1 or $user_blocking eq 1)}{lang_entry key="subnav.entry.contacts"}{/if} {insert name="newMessages"}</a></li>
<li><div class="line"></div></li>
{/if}
<li class="">
	<a class="dcjq-parent a-dt" href="javascript:;" rel="nofollow"><i class="icon-contrast"></i> {lang_entry key="frontend.global.darktheme"}: <span id="dark-mode-state-text">{if $theme_name|strpos:'dark'}{lang_entry key="frontend.global.on.text"}{else}{lang_entry key="frontend.global.off.text"}{/if}</span><i class="iconBe-chevron-right place-right"></i></a>
</li>
<li id="l-dt" style="display:none">
	<div class="dm-wrap">
		<div class="dm-head dm-head-dt"><i class="icon-arrow-left2"></i> {lang_entry key="frontend.global.darktheme"}</div>
		<p>{lang_entry key="frontend.global.darktheme.tip1"}<br><br>{lang_entry key="frontend.global.darktheme.tip2"}
		<div>
			<span class="label">{lang_entry key="frontend.global.darktheme"}</span>
			{insert name="themeSwitch"}
		</div>
	</div>
</li>
{if $smarty.session.lang_count gt 1}
<li class="">
	{insert name="langInit"}
</li>
{/if}
{if $smarty.session.USER_ID gt 0}
<li class=""><a class="dcjq-parent" href="{$main_url}/{href_entry key="signout"}"><i class="icon-exit"></i> {lang_entry key="frontend.global.signout"}</a></li>
{else}
<li class=""><a class="dcjq-parent" href="{$main_url}/{href_entry key="signin"}"><i class="icon-exit"></i> {lang_entry key="frontend.global.signin"}</a></li>
{/if}
</ul>
{literal}
<script type="text/javascript">
$('a.a-dt').click(function(){$('#user-arrow-box #top-session-accordion li').hide();$('#l-dt').show()});
$('.dm-head-dt').click(function(){$('#user-arrow-box #top-session-accordion li').show();$('#l-dt, #l-ln').hide()});
$('a.a-ln').click(function(){$('#user-arrow-box #top-session-accordion li').hide();$('#l-ln').show()});
$('.dm-head-ln').click(function(){$('#user-arrow-box #top-session-accordion li').show();$('#l-ln, #l-dt').hide()});
</script>
{/literal}
