        <div class="left-float wdmax row bottom-border bottom-margin10"><h1>{if $my_invite eq 1}{$invite_text_h1}{else}<a href="{$main_url}/{href_entry key="user"}/{$invited}" class="font14pt">{$invited}</a> {lang_entry key="invite.contact.username.subject"}!{/if}</h1></div>
        <div class="left-float row">{lang_entry key="mail.invite.email.more"}</div>
    {if $smarty.session.USER_ID eq ''}
	<div class="left-float row top-padding20"><h3 class="inline-display">{lang_entry key="contacts.friends.h2.a"}</h3><span class="inline-display"> <a href="{$main_url}/{href_entry key="signin"}?next={href_entry key="confirm_friend"}-sid={$smarty.get.sid|sanitize}%uid={$smarty.get.uid|sanitize}">{lang_entry key="frontend.global.click"}</a> {lang_entry key="contacts.friends.h2.a.tip"}</span></div>
	<div class="left-float row"><h3 class="inline-display">{lang_entry key="contacts.friends.h2.b"}</h3><span class="inline-display"> <a href="{$main_url}/{href_entry key="signup"}?next={href_entry key="confirm_friend"}-sid={$smarty.get.sid|sanitize}%uid={$smarty.get.uid|sanitize}">{lang_entry key="frontend.global.click"}</a> {lang_entry key="contacts.friends.h2.b.tip"}</span></div>
    {else}
        <div class="left-float row top-padding20 left-padding10">
    {if $my_invite eq 1}
	{$invite_text_info}
    {else}
            <form id="accept-friend-form" method="post" action="">
                <input type="hidden" name="accept_value" value="1" />
                <input type="button" name="accept_invite" class="search-button form-button" value="{lang_entry key="frontend.global.accept"}" />
                <span> {lang_entry key="frontend.global.or"} </span>
                <a href="javascript:;">{lang_entry key="frontend.global.lowercancel"}</a>
            </form>
    {/if}
        </div>
    {/if}