                <div class="blue categories-container">
                    <h4 class="nav-title categories-menu-title left-menu-h4"><i class="icon-user"></i>{lang_entry key="account.entry.my.account"}</h4>
                    <aside>
                        <nav>
                            <ul class="accordion" id="categories-accordion">
                                <li id="account-menu-entry1" class="menu-panel-entry menu-panel-entry-active" rel-m="{href_entry key="account"}"><a class="dcjq-parent active" href="javascript:;"><i class="icon-user"></i>{lang_entry key="account.entry.overview"}</a></li>
                                <li id="account-menu-entry2" class="menu-panel-entry" rel-m="{href_entry key="account"}"><a href="javascript:;"><i class="icon-profile"></i>{lang_entry key="account.entry.profile.setup"}</a></li>
                                <li id="account-menu-entry4" class="menu-panel-entry" rel-m="{href_entry key="account"}"><a href="javascript:;"><i class="icon-envelope"></i>{lang_entry key="account.entry.mail.opts"}</a></li>
                            {if $activity_logging eq 1}
                                <li id="account-menu-entry5" class="menu-panel-entry" rel-m="{href_entry key="account"}"><a href="javascript:;"><i class="icon-share"></i>{lang_entry key="account.entry.act.share"}</a></li>
                            {/if}
                                <li id="account-menu-entry6" class="menu-panel-entry" rel-m="{href_entry key="account"}"><a href="javascript:;"><i class="icon-key"></i>{lang_entry key="account.entry.act.manage"}</a></li>
                            </ul>
                        </nav>
                    </aside>
                </div>