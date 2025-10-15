                {if !$smarty.session.USER_ID}
                	{include file="tpl_frontend/tpl_leftnav/tpl_nav_shorts.tpl"}
                {/if}
                <div class="blue categories-container">
                	<h4 class="nav-title categories-menu-title left-menu-h4"><i class="icon-eye"></i>{lang_entry key="frontend.global.explore"}</h4>
                        <aside>
                                <nav>
                                        <ul class="accordion mtmin10" id="{if $smarty.session.USER_ID eq ""}no-session-accordion2{else}session-accordion{/if}">
                                        {if $video_module eq "1"}
                                                <li class="{if $page_display eq "tpl_browse" and $type_display eq "video"}menu-panel-entry menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_browse" and $type_display eq "video"} active{/if}" rel-a="load" href="{$main_url}/{href_entry key="videos"}"><i class="icon-video"></i>{lang_entry key="frontend.global.v.p.c"}</a></li>
                                        {/if}
                                        {if $live_module eq "1"}
                                                <li class="{if $page_display eq "tpl_browse" and $type_display eq "live"}menu-panel-entry menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_browse" and $type_display eq "live"} active{/if}" rel-a="load" href="{$main_url}/{href_entry key="broadcasts"}"><i class="icon-live"></i>{lang_entry key="frontend.global.l.p.c"}</a></li>
                                        {/if}
                                        {if $audio_module eq "1"}
                                        	<li class="{if $page_display eq "tpl_browse" and $type_display eq "audio"}menu-panel-entry menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_browse" and $type_display eq "audio"} active{/if}" rel-a="load" href="{$main_url}/{href_entry key="audios"}"><i class="icon-headphones"></i>{lang_entry key="frontend.global.a.p.c"}</a></li>
                                        {/if}
                                        {if $image_module eq "1"}
                                        	<li class="{if $page_display eq "tpl_browse" and $type_display eq "image"}menu-panel-entry menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_browse" and $type_display eq "image"} active{/if}" rel-a="load" href="{$main_url}/{href_entry key="images"}"><i class="icon-image"></i>{lang_entry key="frontend.global.i.p.c"}</a></li>
                                        {/if}
                                        {if $document_module eq "1"}
                                        	<li class="{if $page_display eq "tpl_browse" and $type_display eq "document"}menu-panel-entry menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_browse" and $type_display eq "document"} active{/if}" rel-a="load" href="{$main_url}/{href_entry key="documents"}"><i class="icon-file"></i>{lang_entry key="frontend.global.d.p.c"}</a></li>
                                        {/if}
                                        {if $blog_module eq "1"}
                                                <li class="{if $page_display eq "tpl_browse" and $type_display eq "blog"}menu-panel-entry menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_browse" and $type_display eq "blog"} active{/if}" rel-a="load" href="{$main_url}/{href_entry key="blogs"}"><i class="icon-blog"></i>{lang_entry key="frontend.global.b.p.c"}</a></li>
                                        {/if}
                                        {if $public_channels eq "1" and $smarty.session.USER_ID}
                                                <li class="{if $page_display eq "tpl_channels"}menu-panel-entry menu-panel-entry-active{/if}"><a class="dcjq-parent{if $page_display eq "tpl_channels"} active{/if}" rel-a="load" href="{$main_url}/{href_entry key="channels"}"><i class="icon-users"></i>{lang_entry key="frontend.global.channels"}</a></li>
                                        {/if}
                                        </ul>
                                        <div class="clearfix"></div>
                                </nav>
                        </aside>
                </div>
                {if !$smarty.session.USER_ID}
                <div class="blue categories-container">
                        <aside>
                                <nav>
                                	<ul class="accordion mtmin10" id="{if $smarty.session.USER_ID eq ""}no-session-accordion2{else}session-accordion{/if}">
                                        {if $public_channels eq "1"}
                                                <li class="{if $page_display eq "tpl_channels"}menu-panel-entry menu-panel-entry-active {/if}mt-10"><a class="dcjq-parent{if $page_display eq "tpl_channels"} active{/if}" rel-a="load" href="{$main_url}/{href_entry key="channels"}"><i class="icon-users"></i>{lang_entry key="frontend.global.browse.channels"}</a></li>
                                        {/if}
                                	</ul>
                                        <div class="clearfix"></div>
                                </nav>
                        </aside>
                </div>
                <div class="blue categories-container">
                	<h5 class="nav-title categories-menu-title left-menu-h5">{lang_entry key="frontend.global.signinto"}</h5>
                        <aside>
                                <nav>
                                	<ul class="accordion mtmin10">
                                		<li><a href="{$main_url}/{href_entry key="signin"}" rel="nofollow" class="left-nav-signin"><button class="save-entry-button button-grey search-button top-signin" value="1" name="frontend_global_signin"><i class="icon-user"></i><span>{lang_entry key="frontend.global.signin"}</span></button></a></li>
                                	</ul>
                                        <div class="clearfix"></div>
                                </nav>
                        </aside>
                </div>
                {/if}