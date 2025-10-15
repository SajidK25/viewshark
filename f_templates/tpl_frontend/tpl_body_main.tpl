            <div class="container container_wrapper">
            <div class="dark-overlay dark-overlay-fixed no-display"></div>
                <div class="fluid{if $page_display eq "tpl_view"} viewpage-off{/if}">
                    <div class="inner-spacer"></div>
                    <div class="inner-block{if $smarty.session.sbm eq 1} with-menu{/if}">
                        <section>
                            <article>
                                <div id="siteContent" class="tabs-round tabs-inner{if $page_display eq "tpl_playlists"} playlists-wrapper{/if}">
                            	    {page_display section=$page_display}
                                </div>
                            </article>
                        </section>
                        {insert name=advHTML id="43"}{insert name=advHTML id="44"}
                    </div>
                </div>
{if $page_display eq "tpl_browse" or $page_display eq "tpl_signin" or $page_display eq "tpl_signup" or $page_display eq "tpl_recovery" or $page_display eq "tpl_page" or $page_display eq "tpl_renew" or $page_display eq "tpl_payment"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{if $page_display eq "tpl_browse"}{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}{if !$new_layout}{include file="tpl_frontend/tpl_leftnav/tpl_nav_categ.tpl"}{/if}
                	{else}{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}{if $page_display ne "tpl_renew" and $page_display ne "tpl_payment"}{if !$new_layout}{include file="tpl_frontend/tpl_leftnav/tpl_nav_categ.tpl"}{/if}{/if}
                	{/if}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_index" and $smarty.session.USER_ID > 0}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif ($page_display eq "tpl_index" and $smarty.session.USER_ID eq 0) or $page_display eq "tpl_verify"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $smarty.session.USER_ID > 0 and ($page_display eq "tpl_view" or $page_display eq "tpl_shorts" or $page_display eq "tpl_respond" or $page_display eq "tpl_playlist" or $page_display eq "tpl_upload" or $page_display eq "tpl_search" or $page_display eq "tpl_import")}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container sidebar-right-off"{if $smarty.session.sbm eq 0 or $page_display eq "tpl_view"} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{if !$new_layout}{include file="tpl_frontend/tpl_leftnav/tpl_nav_categ.tpl"}{/if}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $smarty.session.USER_ID eq 0 and ($page_display eq "tpl_view" or $page_display eq "tpl_shorts" or $page_display eq "tpl_respond" or $page_display eq "tpl_playlist" or $page_display eq "tpl_upload" or $page_display eq "tpl_search" or $page_display eq "tpl_import")}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container sidebar-right-off"{if $smarty.session.sbm eq 0 or $page_display eq "tpl_view"} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_files" or $page_display eq "tpl_files_edit" or ($page_display eq "tpl_search" and ($smarty.get.tf eq 5 or $smarty.get.tf eq 7)) or $page_display eq "tpl_playlists" or $page_display eq "tpl_blogs"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                {if $page_display eq "tpl_blogs"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_blogs.tpl"}
                {/if}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_account"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_affiliate"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_subscribers"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_tokens"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                        {include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_manage_channel"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_channel"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_channels"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{if !$new_layout}{include file="tpl_frontend/tpl_leftnav/tpl_nav_categ.tpl"}{/if}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_messages"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_subs"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_user.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_files.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_bottom.tpl"}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{elseif $page_display eq "tpl_error"}
                <div class="fixed-width scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if} sidebar-container"{if $smarty.session.sbm eq 0} style="display: none;"{/if}>
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_footer.tpl"}
                </div>
{/if}
            </div>
