                <div class="blue categories-container">
                        <aside>
                                <nav>
                                        <ul class="accordion mtmin10" id="{if $smarty.session.USER_ID eq ""}no-session-accordion2{else}session-accordion{/if}">
                                                <li class="{if $page_display eq "tpl_index"}menu-panel-entry menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_index"} active{/if}" rel-a="load" href="{$main_url}"><i class="icon-home"></i>{lang_entry key="frontend.global.home"}</a></li>
                                        {if $short_module eq "1"}
                                                <li class="{if $page_display eq "tpl_browse" and $type_display eq "short"}menu-panel-entry menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_browse" and $type_display eq "short"} active{/if}" rel-a="load" href="{$main_url}/{href_entry key="shorts"}"><i class="icon-mobile"></i>{lang_entry key="frontend.global.s.p.c"}</a></li>
                                        {/if}
                                        </ul>
                                        <div class="clearfix"></div>
                                </nav>
                        </aside>
                </div>
