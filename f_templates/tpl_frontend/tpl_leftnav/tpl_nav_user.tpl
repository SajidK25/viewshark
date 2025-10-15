		{if $smarty.session.USER_ID gt 0}
		{include file="tpl_frontend/tpl_leftnav/tpl_nav_shorts.tpl"}
                <div class="blue categories-container">
                	<h4 class="nav-title categories-menu-title left-menu-h4"><i class="icon-video"></i>{lang_entry key="frontend.global.your"}</h4>
                        <aside>
                                <nav>
                                        <ul class="accordion" id="{if $smarty.session.USER_ID eq ""}no-session-accordion{else}session-accordion{/if}">
                                                        <li class="{if $page_display eq "tpl_files" or $page_display eq "tpl_subs_off"}menu-panel-entry main-menu-panel-entry-active{/if} pt-0"><a class="dcjq-parent{if $page_display eq "tpl_files" or $page_display eq "tpl_subs_off"} active{/if}" href="{$main_url}/{href_entry key="files"}"><i class="icon-video"></i>{lang_entry key="subnav.entry.files.my"}</a></li>
                                                        {if $page_display eq "tpl_files"}<div class="this-inner no-display">{include file="tpl_frontend/tpl_leftnav/tpl_nav_files_inner.tpl"}</div>{/if}
                                                        {if $user_subscriptions eq 1}
                                                        <li class="{if $page_display eq "tpl_subscribers"}menu-panel-entry main-menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_subscribers"} active{/if}" href="{$main_url}/{href_entry key="subscribers"}"><i class="icon-coin"></i>{lang_entry key="subnav.entry.subpanel"}</a></li>
                                                        {if $page_display eq "tpl_subscribers"}<div class="this-inner no-display">{include file="tpl_frontend/tpl_leftnav/tpl_nav_subscribers_inner.tpl"}</div>{/if}
                                                        {/if}
                                                        {if $user_tokens eq 1}
                                                        <li class="{if $page_display eq "tpl_tokens"}menu-panel-entry main-menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_tokens"} active{/if}" href="{$main_url}/{href_entry key="tokens"}"><i class="icon-coin"></i>{lang_entry key="subnav.entry.tokenpanel"}</a></li>
                                                        {if $page_display eq "tpl_tokens"}<div class="this-inner no-display">{include file="tpl_frontend/tpl_leftnav/tpl_nav_tokens_inner.tpl"}</div>{/if}
                                                        {/if}
                                                        <li class="{if $page_display eq "tpl_account"}menu-panel-entry main-menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_account"} active{/if}" href="{$main_url}/{href_entry key="account"}"><i class="icon-user"></i>{lang_entry key="subnav.entry.account.my"}</a></li>
                                                        {if $page_display eq "tpl_account"}<div class="this-inner no-display">{include file="tpl_frontend/tpl_leftnav/tpl_nav_account_inner.tpl"}</div>{/if}
                                                        <li class="{if $page_display eq "tpl_manage_channel"}menu-panel-entry main-menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_manage_channel"} active{/if}" href="{$main_url}/{href_entry key="manage_channel"}"><i class="icon-settings"></i>{lang_entry key="subnav.entry.channel.my"}</a></li>
                                                        {if $page_display eq "tpl_manage_channel"}<div class="this-inner no-display">{include file="tpl_frontend/tpl_leftnav/tpl_nav_manage_channel_inner.tpl"}</div>{/if}
                                                        <li class="{if $page_display eq "tpl_messages"}menu-panel-entry main-menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_messages"} active{/if}" href="{$main_url}/{if $internal_messaging eq 1 and ($user_friends eq 1 or $user_blocking eq 1)}{href_entry key="messages"}{elseif $internal_messaging eq 0 and ($user_friends eq 1 or $user_blocking eq 1)}{href_entry key="contacts"}{/if}"><i class="icon-envelope"></i>{if $internal_messaging eq 1 and ($user_friends eq 1 or $user_blocking eq 1)}{lang_entry key="subnav.entry.contacts.messages"}{elseif $internal_messaging eq 0 and ($user_friends eq 1 or $user_blocking eq 1)}{lang_entry key="subnav.entry.contacts"}{/if} {insert name="newMessages"}</a></li>
                                                        {if $page_display eq "tpl_messages"}<div class="this-inner no-display">{include file="tpl_frontend/tpl_leftnav/tpl_nav_messages_inner.tpl"}</div>{/if}
                                        </ul>
                                        <div class="clearfix"></div>
                                </nav>
                        </aside>
                </div>
                {/if}
