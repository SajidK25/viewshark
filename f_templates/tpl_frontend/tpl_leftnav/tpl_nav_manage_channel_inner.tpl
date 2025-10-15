                <div class="blue categories-container">
                    <h4 class="nav-title categories-menu-title left-menu-h4 no-display"><i class="icon-settings"></i>{lang_entry key="subnav.entry.channel.my"}</h4>
                    <aside>
                        <nav>
                            <ul class="accordion inner-accordion" id="categories-accordion">
                                <li id="channel-menu-entry1" class="menu-panel-entry{if $smarty.get.r eq ""} menu-panel-entry-active{/if}" rel-m="{href_entry key="manage_channel"}"><a href="javascript:;"{if $smarty.get.r eq ""} class="dcjq-parent active"{/if}><i class="icon-cog"></i>{lang_entry key="manage.channel.menu.general"}</a></li>
                                <li id="channel-menu-entry2" class="menu-panel-entry" rel-m="{href_entry key="manage_channel"}"><a href="javascript:;"><i class="icon-cogs2"></i>{lang_entry key="manage.channel.menu.modules"}</a></li>
                                {if $channel_backgrounds eq 1}
                                <li id="channel-menu-entry3" class="menu-panel-entry{if $smarty.get.r eq "confirm"} menu-panel-entry-active{/if}" rel-m="{href_entry key="manage_channel"}"><a href="javascript:;"{if $smarty.get.r eq "confirm"} class="dcjq-parent active"{/if}><i class="icon-quill"></i>{lang_entry key="manage.channel.menu.art"}</a></li>
                                {/if}
                            </ul>
                        </nav>
                    </aside>
                </div>