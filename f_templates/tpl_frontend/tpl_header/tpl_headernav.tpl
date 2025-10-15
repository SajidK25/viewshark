{if $website_offline_mode eq "1"}{assign var=disabled_input value='disabled="disabled"'}{else}{assign var=disabled_input value=""}{/if}
{assign var="access_url" value="{$main_url}"}
{assign var="href_s" value="{href_entry key="account"}"}
{assign var="href_m" value="{href_entry key="messages"}"}
{assign var="href_c" value="{href_entry key="contacts"}"}
{assign var="href_cm" value="{href_entry key="comments"}"}
{assign var="href_sub" value="{href_entry key="subscriptions"}"}
{assign var="lang_s" value="{lang_entry key="subnav.entry.settings"}"}
{assign var="lang_f" value="{lang_entry key="subnav.entry.files.my"}"}

            <header id="main_header">
                <div class="dynamic_container">
                    <div id="header_top">
                        <div class="container">

                            <div class="welcome-text">{lang_entry key="frontend.global.welcome.to"} {$website_shortname}</div>

                            <div class="search_holder">
                                <div id="sb-search" class="sb-search">
                                    <form method="get" action="{$main_url}/{href_entry key="search"}">
                                        <input class="sb-search-input" placeholder="{lang_entry key="frontend.global.search.term"}" type="text" value="" name="q" id="search">
                                        <input class="sb-search-submit" type="submit" value="">
                                        <span class="sb-icon-search icon-search"></span>
                                    </form>
                                </div>
                            </div>

                            <div class="but_register">
                            	<div id="user-nav-menu" class="dl-menuwrapper">
                            		<span class="dl-trigger"><span>{if $smarty.session.USER_ID eq ""}{lang_entry key="frontend.global.account"}{else}{$smarty.session.USER_NAME}{/if}</span></span>
                            		<ul class="dl-menu">
                            		{if $smarty.session.USER_ID eq ""}
                            			<li><a href="{$main_url}/{href_entry key="signin"}" rel="nofollow"><i class="icon-enter"></i> {lang_entry key="frontend.global.signin"}</a></li>
                            			<li><a href="{$main_url}/{href_entry key="signup"}" rel="nofollow"><i class="icon-signup"></i> {lang_entry key="frontend.global.signup"}</a></li>
                            			<li><a href="{$main_url}/{href_entry key="service"}/{href_entry key="x_recovery"}" rel="nofollow"><i class="icon-support"></i> {lang_entry key="frontend.global.recovery"}</a></li>
                            		{else}
                            			<li><a href="{$main_url}/{href_entry key="files"}"><i class="icon-upload"></i> {$lang_f}</a></li>
                            			<li><a href="{$main_url}/{$href_sub}"><i class="icon-users5"></i> {lang_entry key="subnav.entry.sub"}</a></li>
                            			<li><a href="{$main_url}/{if $internal_messaging eq 1 and ($user_friends eq 1 or $user_blocking eq 1)}{$href_m}{elseif $internal_messaging eq 0 and ($user_friends eq 1 or $user_blocking eq 1)}{$href_c}{else}{$href_cm}{/if}"><i class="icon-envelope"></i> {if $internal_messaging eq 1 and ($user_friends eq 1 or $user_blocking eq 1)}{lang_entry key="subnav.entry.contacts.messages"}{elseif $internal_messaging eq 0 and ($user_friends eq 1 or $user_blocking eq 1)}{lang_entry key="subnav.entry.contacts"}{else}{lang_entry key="subnav.entry.comments"}{/if} {insert name="newMessages"}</a></li>
                            			<li><a href="{$main_url}/{href_entry key="manage_channel"}"><i class="icon-settings"></i> {lang_entry key="subnav.entry.channel.my"}</a></li>
                            			<li><a href="{$main_url}/{$href_s}"><i class="icon-profile"></i> {$lang_s}</a></li>
                            			<li><a href="{$main_url}/{href_entry key="signout"}"><i class="icon-exit"></i> {lang_entry key="frontend.global.signout"}</a></li>
                            		{/if}
                            		</ul>
                            	</div>
                            </div>
                            
                            <div class="but_upload" onclick="window.location='{$main_url}/{href_entry key="upload"}'"><span>{lang_entry key="frontend.global.upload"}</span></div>
                            <a style="display:none;" id="up" href="{$main_url}/{href_entry key="upload"}" rel="nofollow">{lang_entry key="frontend.global.upload"}</a>
                            <div class="social_links">
                                <a href="{$facebook_link}" target="_blank" rel="me">
                                    <i class="icon-facebook2"></i>
                                </a>
                                <a href="{$twitter_link}" target="_blank" rel="me">
                                    <i class="icon-twitter22"></i>
                                </a>
                                <a href="{$gplus_link}" target="_blank" rel="me">
                                    <i class="icon-googleplus3"></i>
                                </a>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="header_bottom">
                        <div class="container">
                            <div id="main_navbar" class="navbar navbar-default" role="navigation">
                                <div class="container-fluid">
                                    <div class="navbar-header">
                                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                                            <span class="sr-only">Toggle navigation</span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                            <span class="icon-bar"></span>
                                        </button>
                                        <a class="navbar-brand" href="{$main_url}" title="Home" id="logo" rel="nofollow"></a>
                                    </div>
                                    <div class="navbar-collapse collapse">
                                        <ul class="nav navbar-nav navbar-right">
                                            <li class="{if $page_display eq "tpl_index"}active{/if}">
                                                <a href="{$main_url}"><i class="icon-home"></i>{lang_entry key="frontend.global.home"}</a>
                                            </li>
                                            {if $live_module eq 1}
                                            <li class="{if $page_display eq "tpl_browse" and $type_display eq "live"}active{/if}">
                                                <a href="{$main_url}/{href_entry key="broadcasts"}"><i class="icon-live"></i>{lang_entry key="frontend.global.l.p.c"}</a>
                                            </li>
                                            {/if}
                                            {if $video_module eq 1}
                                            <li class="{if $page_display eq "tpl_browse" and $type_display eq "video"}active{/if}">
                                                <a href="{$main_url}/{href_entry key="videos"}"><i class="icon-video"></i>{lang_entry key="frontend.global.v.p.c"}</a>
                                            </li>
                                            {/if}
                                            {if $image_module eq 1}
                                            <li class="{if $page_display eq "tpl_browse" and $type_display eq "image"}active{/if}">
                                                <a href="{$main_url}/{href_entry key="images"}"><i class="icon-image"></i>{lang_entry key="frontend.global.i.p.c"}</a>
                                            </li>
                                            {/if}
                                            {if $audio_module eq 1}
                                            <li class="{if $page_display eq "tpl_browse" and $type_display eq "audio"}active{/if}">
                                                <a href="{$main_url}/{href_entry key="audios"}"><i class="icon-headphones"></i>{lang_entry key="frontend.global.a.p.c"}</a>
                                            </li>
                                            {/if}
                                            {if $document_module eq 1}
                                            <li class="{if $page_display eq "tpl_browse" and $type_display eq "document"}active{/if}">
                                                <a href="{$main_url}/{href_entry key="documents"}"><i class="icon-file"></i>{lang_entry key="frontend.global.d.p.c"}</a>
                                            </li>
                                            {/if}
                                            {if $blog_module eq 1}
                                            <li id="section-blogs" class="{if $page_display eq "tpl_browse" and $type_display eq "blog"}active{/if}">
                                                <a href="{$main_url}/{href_entry key="blogs"}"><i class="icon-pencil2"></i>{lang_entry key="frontend.global.blogs"}</a>
                                            </li>
                                            {/if}
                                            {if $public_channels eq 1}
                                            <li class="{if $page_display eq "tpl_channels"}active{/if}">
                                                <a href="{$main_url}/{href_entry key="channels"}"><i class="icon-users"></i>{lang_entry key="frontend.global.channels"}</a>
                                            </li>
                                            {/if}
                                            {if $file_playlists eq 1}
                                            <li id="section-playlists" class="{if $page_display eq "tpl_playlists"}active{/if}">
                                                <a href="{$main_url}/{href_entry key="playlists"}"><i class="icon-list"></i>{lang_entry key="frontend.global.playlists"}</a>
                                            </li>
                                            {/if}
                                        </ul>
                                    </div><!--/.nav-collapse -->
                                </div><!--/.container-fluid -->
                            </div>
                        </div>
                    </div> <!-- /.header_bottom -->
                </div>
            </header><!-- /header -->
            