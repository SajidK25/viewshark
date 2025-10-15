                        <header id="main_header">
                        	{if $is_mobile}
                        	<div class="pwa_header" style="display:none">
                        		<center class="my-10">{lang_entry key="frontend.global.pwa.add"}</center>
                        		<center>
                        			<button class="search-button form-button button-grey px-15 py-5 m-0 tt-c max-w-auto" value="1" name="app_btn" id="app-btn" type="button"><span>{lang_entry key="frontend.global.pwa.install"}</span></button>
                        			<a class="link cancel-trigger mb-0 px-15 py-5 tt-c" href="#" id="app-cancel"><span class="px-0">{lang_entry key="frontend.global.cancel"}</span></a>
                        		</center>
                        	</div>
                        	{/if}
                                <div class="dynamic_container">
                                        <div id="ct-header-top">
                                                <div id="logo_container">
                                                        <i class="icon icon-menu2 menu-trigger"></i>
                                                        <a class="navbar-brand" href="{$main_url}" title="{lang_entry key="frontend.global.home"}" id="logo" rel="nofollow"></a>
                                                        <span id="menu-trigger-response"></span>
                                                </div>
                                                <div id="top_actions"{if $smarty.session.USER_ID eq 0} class="no-session"{/if}>
                                                                <div class="user-thumb-xlarge top">
                                                                <a href="javascript:;" class="sb-icon-search-top place-left pr5" onclick="$('.search_holder').toggleClass('open');$('#user-arrow-box, #notifications-arrow-box').addClass('hidden');$('.dynamic_container').addClass('s-open');$('.navbar-brand').css({ldelim}'visibility':'hidden'{rdelim})"><i class="icon-rs-search icon-search"></i></a>
                                                                {if $smarty.session.USER_ID gt 0}
                                                                        <img height="32" class="own-profile-image mt" title="{$smarty.session.USER_NAME}" alt="{$smarty.session.USER_NAME}" src="{insert name="getProfileImage" assign="profileImage" for="{$smarty.session.USER_ID}"}{$profileImage}">
                                                                {else}
                                                                	<span class="no-session-icon" onclick="$('#user-arrow-box').toggleClass('hidden');"><i class="mt-open"></i></span>
                                                                {/if}

                                                                        <div class="arrow_box hidden blue" id="user-arrow-box">
                                                                        {include file="tpl_frontend/tpl_header/tpl_headernav_pop.tpl"}
                                                                        </div>
                                                                </div>
                                                        {if $smarty.session.USER_ID gt 0}
                                                                <a href="javascript:;" class="top-icon top-notif" title="{lang_entry key="frontend.global.notifications"}">
                                                                        <i class="icon-bell"></i>{if $smarty.session.new_notifications gt 0}<span class="nnr">{$smarty.session.new_notifications}</span>{/if}
                                                                </a>
                                                                <a href="javascript:;" class="top-icon top-upload" title="{lang_entry key="frontend.global.upload"}">
                                                                        <i class="icon-upload"></i>
                                                                </a>
                                                                <div class="arrow_box hidden" id="notifications-arrow-box">
                                                                        <div class="arrow_box_pad">
                                                                                <div id="notifications-box">
                                                                                </div>
                                                                        </div>
                                                                </div>
                                                                <div class="arrow_box blue hidden" id="upload-arrow-box">
                                                                                	<ul class="accordion tacc" id="top-session-accordion">
                                                                                		{if $live_module eq 1 and $live_uploads eq 1}<li{if $smarty.get.t eq "live"} class=""{/if}><a id="new-live" rel-s="{href_entry key="files"}" class="dcjq-parent" href="{$main_url}/{href_entry key="upload"}?t=live"><i class="icon icon-live"></i> {lang_entry key="upload.menu.live"}</a></li>{/if}
                                                                                		{if $video_module eq 1 and $video_uploads eq 1}<li{if $smarty.get.t eq "video"} class=""{/if}><a class="dcjq-parent" href="{$main_url}/{href_entry key="upload"}?t=video"><i class="icon icon-video"></i> {lang_entry key="upload.menu.video"}</a></li>{/if}
                                                                                		{if $video_module eq 1 and $video_uploads eq 1}<li{if $smarty.get.t eq "short"} class=""{/if}><a class="dcjq-parent" href="{$main_url}/{href_entry key="upload"}?t=short"><i class="icon icon-mobile"></i> {lang_entry key="upload.menu.short"}</a></li>{/if}
                                                                                		{if $import_yt eq 1 or $import_dm eq 1 or $import_vi eq 1}<li{if $page_display eq "tpl_import" and $smarty.get.t eq "video"} class=""{/if}><a class="dcjq-parent" href="{$main_url}/{href_entry key="import"}?t=video"><i class="icon icon-embed"></i> {lang_entry key="upload.menu.grab"}</a></li>{/if}
                                                                                		{if $audio_module eq 1 and $audio_uploads eq 1}<li{if $smarty.get.t eq "audio"} class=""{/if}><a class="dcjq-parent" href="{$main_url}/{href_entry key="upload"}?t=audio"><i class="icon icon-headphones"></i> {lang_entry key="upload.menu.audio"}</a></li>{/if}
                                                                                		{if $image_module eq 1 and $image_uploads eq 1}<li{if $smarty.get.t eq "image"} class=""{/if}><a class="dcjq-parent" href="{$main_url}/{href_entry key="upload"}?t=image"><i class="icon icon-image"></i> {lang_entry key="upload.menu.image"}</a></li>{/if}
                                                                                		{if $document_module eq 1 and $document_uploads eq 1}<li{if $smarty.get.t eq "document"} class="tab-current"{/if}><a href="{$main_url}/{href_entry key="upload"}?t=document"><i class="icon icon-file"></i> {lang_entry key="upload.menu.document"}</a></li>{/if}
                                                                                		{if $blog_module eq 1}<li{if $smarty.get.t eq "blog"} class="tab-current"{/if}><a id="new-blog" rel-s="{href_entry key="files"}" href="{$main_url}/{href_entry key="upload"}?t=blog"><i class="icon icon-blog"></i> {lang_entry key="upload.menu.blog"}</a></li>{/if}
                                                                                	</ul>
                                                                </div>
                                                         {/if}
                                                        {if $smarty.session.USER_ID eq 0}
                                                                <a href="{$main_url}/{href_entry key="signin"}" rel="nofollow"><button class="save-entry-button button-grey search-button top-signin" value="1" name="frontend_global_signin"><i class="icon-user"></i><span>{lang_entry key="frontend.global.signin"}</span></button></a>
                                                        {/if}
                                                </div>
                                                <center>
                                                <div class="search_holder{if $smarty.session.USER_ID eq 0} no-session-holder{/if}">
                                                	<a href="javascript:;" class="icon-rs-arrow-left"><i class="icon-arrow-left close-search visible-mobile"></i></a>
                                                	<div class="top-search-form-wrap"><form id="top-search-form" class="entry-form-class">
                                                	<button type="button" id="filter-search-type" value="video" class="dl-menuwrapper viewType_btn viewType_btn-default search-filter-mode">
                                                	  <span class="dl-trigger actions-trigger nbfr"><i class="icon icon-video"></i></span>
                                                	  <ul class="dl-menu dl-search">
                                                		{if $video_module eq 1}<li><a href="" class="icon icon-video" rel-value="1" rel="nofollow"><span>{lang_entry key="frontend.global.v.p.c"}</span></a></li>{/if}
                                                		{if $short_module eq 1}<li><a href="" class="icon icon-mobile" rel-value="9" rel="nofollow"><span>{lang_entry key="frontend.global.s.p.c"}</span></a></li>{/if}
                                                		{if $live_module eq 1}<li><a href="" class="icon icon-live" rel-value="8" rel="nofollow"><span>{lang_entry key="frontend.global.l.p.c"}</span></a></li>{/if}
                                                		{if $audio_module eq 1}<li><a href="" class="icon icon-headphones" rel-value="3" rel="nofollow"><span>{lang_entry key="frontend.global.a.p.c"}</span></a></li>{/if}
                                                		{if $image_module eq 1}<li><a href="" class="icon icon-image" rel-value="2" rel="nofollow"><span>{lang_entry key="frontend.global.i.p.c"}</span></a></li>{/if}
                                                		{if $doc_module eq 1}<li><a href="" class="icon icon-file" rel-value="4" rel="nofollow"><span>{lang_entry key="frontend.global.d.p.c"}</span></a></li>{/if}
                                                		{if $blog_module eq 1}<li><a href="" class="icon icon-blog" rel-value="7" rel="nofollow"><span>{lang_entry key="frontend.global.b.p.c"}</span></a></li>{/if}
                                                		{if $public_channels eq 1}<li><a href="" class="icon icon-users" rel-value="6" rel="nofollow"><span>{lang_entry key="frontend.global.channels"}</span></a></li>{/if}
                                                	  </ul>
                                                	</button>
                                                	</form></div>
                                                        <div id="sb-search" class="sb-search sb-search-open">
                                                                <form method="get" action="{$main_url}/{href_entry key="search"}">
                                                                	<input class="sb-search-ft" type="hidden" name="tf" value="1">
                                                                        <input class="sb-search-input" placeholder="{lang_entry key="frontend.global.searchtext"}" type="text" value="" name="q" id="search">
                                                                        <input class="sb-search-submit" type="submit" value="">
                                                                        <span class="sb-icon-search icon-search"></span>
                                                                </form>
                                                        </div>
                                                </div>
                                                </center>
                                        </div>
                                        <div class="clearfix"></div>
                                </div>
                        </header>