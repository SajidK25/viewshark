	{if $smarty.session.USER_ID gt 0}
	{insert name="getCurrentSection" assign=s}

	{if $page_display ne "tpl_messages" and $page_display ne "tpl_manage_channel" and $page_display ne "tpl_account" and $page_display ne "tpl_affiliate" and $page_display ne "tpl_subscribers"}
        <div class="blue categories-container{if $page_display eq "tpl_playlists" or $page_display eq "tpl_blogs"} hidden{/if}">
            <h4 class="nav-title categories-menu-title left-menu-h4 no-display"><i class="icon-video"></i>{lang_entry key="subnav.entry.files.my"}</h4>
            <aside>
                <nav>
                    <ul class="accordion inner-accordion" id="categories-accordion">
                        <li id="file-menu-entry1" class="menu-panel-entry{if $s eq "file-menu-entry1" or (($s eq "" and $page_display eq "tpl_files") and $smarty.post.do_reload eq "" and $page_display ne "tpl_playlists" and $page_display ne "tpl_blogs" and $page_display ne "tpl_channel" and $page_display ne "tpl_search")} menu-panel-entry-active{/if}" rel-m="{href_entry key="files"}" rel-s="{href_entry key="uploads"}"><a href="javascript:;" class="{if $smarty.post.do_reload eq "" and $page_display ne "tpl_search" and ($page_display eq "tpl_files" and ($s eq "" or $s eq "file-menu-entry1"))}dcjq-parent active{/if}"><i class="icon-upload"></i><span class="mm">{lang_entry key="files.menu.myfiles"}</span></a>{if $file_counts eq 1}<span id="file-menu-entry1-count" class="right-float mm-count"></span>{/if}</li>
                            {if $file_favorites eq "1"}
                        <li id="file-menu-entry2" class="menu-panel-entry{if $s eq "file-menu-entry2"} menu-panel-entry-active{/if}" rel-m="{href_entry key="files"}" rel-s="{href_entry key="favorites"}"><a href="javascript:;" class="{if $smarty.post.do_reload eq "" and $page_display ne "tpl_search" and ($s eq "file-menu-entry2")}dcjq-parent active{/if}"><i class="icon-heart"></i><span class="mm">{lang_entry key="files.menu.myfav"}</span></a>{if $file_counts eq 1}<span id="file-menu-entry2-count" class="right-float mm-count"></span>{/if}</li>
                            {/if}
                            {if $file_rating eq "1"}
                        <li id="file-menu-entry3" class="menu-panel-entry{if $s eq "file-menu-entry3"} menu-panel-entry-active{/if}" rel-m="{href_entry key="files"}" rel-s="{href_entry key="liked"}"><a href="javascript:;" class="{if $smarty.post.do_reload eq "" and $page_display ne "tpl_search" and ($s eq "file-menu-entry3")}dcjq-parent active{/if}"><i class="icon-thumbs-up"></i><span class="mm">{lang_entry key="files.menu.liked"}</span></a>{if $file_counts eq 1}<span id="file-menu-entry3-count" class="right-float mm-count"></span>{/if}</li>
                            {/if}
                            {if $file_history eq "1"}
                        <li id="file-menu-entry4" class="menu-panel-entry{if $s eq "file-menu-entry4"} menu-panel-entry-active{/if}" rel-m="{href_entry key="files"}" rel-s="{href_entry key="history"}"><a href="javascript:;" class="{if $smarty.post.do_reload eq "" and $page_display ne "tpl_search" and ($s eq "file-menu-entry4")}dcjq-parent active{/if}"><i class="icon-history"></i><span class="mm">{lang_entry key="files.menu.history"}</span></a>{if $file_counts eq 1}<span id="file-menu-entry4-count" class="right-float mm-count"></span>{/if}</li>
                            {/if}
                            {if $file_watchlist eq "1"}
                        <li id="file-menu-entry5" class="menu-panel-entry{if $s eq "file-menu-entry5"} menu-panel-entry-active{/if}" rel-m="{href_entry key="files"}" rel-s="{href_entry key="watchlist"}"><a href="javascript:;" class="{if $smarty.post.do_reload eq "" and $page_display ne "tpl_search" and ($s eq "file-menu-entry5")}dcjq-parent active{/if}"><i class="icon-clock"></i><span class="mm">{lang_entry key="files.menu.watch"}</span></a>{if $file_counts eq 1}<span id="file-menu-entry5-count" class="right-float mm-count"></span>{/if}</li>
                            {/if}
                            {if $file_playlists eq "1"}
                        <li id="file-menu-entry6" class="dcjq-parent-li menu-panel-entry{if $smarty.post.do_reload eq "1" or ($s eq "file-menu-entry6" and $page_display ne "tpl_channel") or $page_display eq "tpl_playlists"} menu-panel-entry-active{/if}" rel-m="{href_entry key="files"}" rel-s="{href_entry key="playlists"}"><a href="javascript:;"{if $smarty.post.do_reload eq "1" or ($page_display eq "tpl_search" and $smarty.get.tf eq 5) or ($s eq "file-menu-entry6" and $page_display ne "tpl_channel")} class="dcjq-parent active"{/if}><i class="icon-list"></i><span class="mm">{lang_entry key="files.menu.mypl"}</span></a>{if $file_counts eq 1}<span id="file-menu-entry6-count" class="right-float mm-count"></span>{/if}
                                {insert name=getUserPlaylists assign=upl for="file-menu-entry6"}{$upl}
                        </li>
                            {/if}
                            {if $file_comments eq "1"}
                        <li id="file-menu-entry7" class="menu-panel-entry{if $s eq "file-menu-entry7"} menu-panel-entry-active{/if}" rel-m="{href_entry key="files"}" rel-s="{href_entry key="comments"}"><a href="javascript:;" class="{if $smarty.post.do_reload eq "" and $page_display ne "tpl_search" and ($s eq "file-menu-entry7")}dcjq-parent active{/if}"><i class="icon-comment"></i><span class="mm">{lang_entry key="files.menu.comments"}</span></a>{if $file_counts eq 1}<span id="file-menu-entry7-count" class="right-float mm-count"></span>{/if}</li>
                            {/if}
                            {if $file_responses eq "1"}
                        <li id="file-menu-entry8" class="menu-panel-entry{if $s eq "file-menu-entry8"} menu-panel-entry-active{/if}" rel-m="{href_entry key="files"}" rel-s="{href_entry key="responses"}"><a href="javascript:;" class="{if $smarty.post.do_reload eq "" and $page_display ne "tpl_search" and ($s eq "file-menu-entry8")}dcjq-parent active{/if}"><i class="icon-comments"></i><span class="mm">{lang_entry key="files.menu.responses"}</span></a>{if $file_counts eq 1}<span id="file-menu-entry8-count" class="right-float mm-count"></span>{/if}</li>
                            {/if}
                    </ul>
                </nav>
            </aside>
        </div>
        {/if}
        {else}
        {/if}