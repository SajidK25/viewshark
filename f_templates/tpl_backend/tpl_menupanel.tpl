                <ul id="gn-menu" class="gn-menu-main">
                    <li class="gn-trigger">
                        <a class="gn-icon gn-icon-menu gn-selected"><span>Menu</span></a>
                        <a class="logo" href="{$main_url}/{$backend_access_url}/{href_entry key="be_dashboard"}"></a>

                        <nav class="gn-menu-wrapper">
                            <div class="blue categories-container">
                                <ul class="accordion" id="categories-accordion">

                                    <li class="gn-search-item">
                                        <input placeholder="Search" type="search" class="gn-search">
                                        <a class="gn-icon gn-icon-search"><span>Search</span></a>
                                    </li>
                                    
                                    <li>
                                        <a href="javascript:;"{if $page_display eq "backend_tpl_dashboard" or $page_display eq "backend_tpl_analytics"} class="active"{/if}><i class="iconBe-pie"></i>{lang_entry key="backend.menu.dash"}</a>
                                        <ul>
                                            <li><a href="{$main_url}/{$backend_access_url}/{href_entry key="be_dashboard"}"{if $page_display eq "backend_tpl_dashboard"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="frontend.global.home"}</a></li>
                                        </ul>
                                    </li>
                                    {include file="tpl_backend/tpl_menupanel_affiliate.tpl"}
                                    {include file="tpl_backend/tpl_menupanel_subscriber.tpl"}
                                    {include file="tpl_backend/tpl_menupanel_token.tpl"}
                                    <li><a href="javascript:;"><i class="iconBe-key"></i>{lang_entry key="backend.menu.ac"}</a>
                                        <ul>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub6" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.ac.admin"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub4" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.ac.front"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub8" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.ac.guest"}</a></li>
                                        </ul>
                                    </li>

                                    <li><a href="javascript:;"><i class="iconBe-cogs"></i>{lang_entry key="backend.menu.sc"}</a>
                                        <ul>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub1" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.metadata"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub7" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.modules"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub5" class="menu-panel-entry-sub be-panel sub_menu" rel-m="{href_entry key="be_settings"}">{lang_entry key="backend.menu.sc.categories"}</a>
                                                    <ul>
                                                        <li><a href="javascript:;" id="backend-menu-entry2-sub5v" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "v"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.categ.v"}</a></li>
                                                        {include file="tpl_backend/tpl_menupanel_live_categ.tpl"}
                                                        <li><a href="javascript:;" id="backend-menu-entry2-sub5i" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "i"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.categ.i"}</a></li>
                                                        <li><a href="javascript:;" id="backend-menu-entry2-sub5a" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "a"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.categ.a"}</a></li>
                                                        <li><a href="javascript:;" id="backend-menu-entry2-sub5d" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "d"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.categ.d"}</a></li>
                                                        <li><a href="javascript:;" id="backend-menu-entry2-sub5b" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "b"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.categ.b"}</a></li>
                                                        <li><a href="javascript:;" id="backend-menu-entry2-sub5c" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "c"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.categ.c"}</a></li>
                                                    </ul>
                                            </li>
                                            {include file="tpl_backend/tpl_menupanel_affiliate_conf.tpl"}
                                            <li><a href="javascript:;" id="backend-menu-entry4-sub1" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_members"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.paid"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub24" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.ond"}</a></li>
                                            {include file="tpl_backend/tpl_menupanel_live_conf.tpl"}
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub12" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.upload"}</a></li>
                                            {include file="tpl_backend/tpl_menupanel_import_conf.tpl"}
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub13" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.file"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub18" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.signin"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub17" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.signup"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub19" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.recovery"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub20" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.captcha"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub15" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.messaging"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry5-sub1" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_members"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.channels"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub3" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.sitemap"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub16" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.lang"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub9" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sc.static"}</a></li>
                                        </ul>
                                    </li>

                                    <li><a href="javascript:;"{if $page_display eq "backend_tpl_members" and $smarty.get.u ne ""} class="active"{/if}><i class="iconBe-users"></i>{lang_entry key="backend.menu.am"}</a>
                                        <ul>
                                            <li><a href="javascript:;" id="backend-menu-entry10-sub2" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_members" and $smarty.get.u ne ""} active{/if}" rel-m="{href_entry key="be_members"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.am.users"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry4-sub4" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_members"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.sub.types"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry4-sub2" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_members"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.am.types"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry4-sub3" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_members"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.am.codes"}</a></li>
                                        </ul>
                                    </li>

                                    {include file="tpl_backend/tpl_menupanel_live.tpl"}

                                    <li><a href="javascript:;"{if $page_display eq "backend_tpl_files" and $smarty.get.u ne ""} class="active"{/if}><i class="icon-video"></i>{lang_entry key="backend.menu.fm"}</a>
                                        <ul>
                                            <li><a href="javascript:;" id="backend-menu-entry6-sub1" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "v"} active{/if}" rel-m="{href_entry key="be_files"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.v"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry6-sub7" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "s"} active{/if}" rel-m="{href_entry key="be_files"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.s"}</a></li>
                                    	    {include file="tpl_backend/tpl_menupanel_live_manage.tpl"}
                                            <li><a href="javascript:;" id="backend-menu-entry6-sub2" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "i"} active{/if}" rel-m="{href_entry key="be_files"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.i"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry6-sub3" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "a"} active{/if}" rel-m="{href_entry key="be_files"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.a"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry6-sub4" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "d"} active{/if}" rel-m="{href_entry key="be_files"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.d"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry6-sub5" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_files" and $smarty.get.u[0] eq "b"} active{/if}" rel-m="{href_entry key="be_files"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fm.b"}</a></li>

                                            <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.fm.categ"}</a>
                                                <ul>
                                                    <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.fm.categ.v"}</a>
                                                        {insert name="beFileCategories" type="video"}
                                                    </li>
                                                    {include file="tpl_backend/tpl_menupanel_live_manage_categ.tpl"}
                                                    <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.fm.categ.i"}</a>
                                                        {insert name="beFileCategories" type="image"}
                                                    </li>

                                                    <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.fm.categ.a"}</a>
                                                        {insert name="beFileCategories" type="audio"}
                                                    </li>

                                                    <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.fm.categ.d"}</a>
                                                        {insert name="beFileCategories" type="doc"}
                                                    </li>

                                                    <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.fm.categ.b"}</a>
                                                        {insert name="beFileCategories" type="blog"}
                                                    </li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>

                                    <li><a href="javascript:;"><i class="icon-comment"></i>{lang_entry key="subnav.entry.comments"}</a>
                                        <ul>
                                            <li><a href="javascript:;" id="backend-menu-entry16-sub1" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_settings" and $smarty.get.c[0] eq "v"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="frontend.global.comm.v"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry16-sub8" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_settings" and $smarty.get.c[0] eq "s"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="frontend.global.comm.s"}</a></li>
                                            {include file="tpl_backend/tpl_menupanel_live_comm.tpl"}
                                            <li><a href="javascript:;" id="backend-menu-entry16-sub3" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_settings" and $smarty.get.c[0] eq "i"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="frontend.global.comm.i"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry16-sub4" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_settings" and $smarty.get.c[0] eq "a"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="frontend.global.comm.a"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry16-sub5" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_settings" and $smarty.get.c[0] eq "d"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="frontend.global.comm.d"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry16-sub6" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_settings" and $smarty.get.c[0] eq "b"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="frontend.global.comm.b"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry16-sub7" class="menu-panel-entry-sub be-panel{if $page_display eq "backend_tpl_settings" and $smarty.get.c[0] eq "c"} active{/if}" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="frontend.global.comm.c"}</a></li>
                                        </ul>
                                    </li>

                                    <li><a href="javascript:;"{if ($page_display eq "backend_tpl_upload" and $smarty.get.t ne "") or $page_display eq "backend_tpl_import"} class="active"{/if}><i class="iconBe-upload"></i>{lang_entry key="backend.menu.fu"}</a>
                                        <ul>
                                            <li><a href="{$backend_url}/{href_entry key="be_upload"}?t=video"{if $page_display eq "backend_tpl_upload" and $smarty.get.t eq "video"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fu.v"}</a></li>
                                            <li><a href="{$backend_url}/{href_entry key="be_upload"}?t=short"{if $page_display eq "backend_tpl_upload" and $smarty.get.t eq "short"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fu.s"}</a></li>
                                            <li><a href="{$backend_url}/{href_entry key="be_upload"}?t=image"{if $page_display eq "backend_tpl_upload" and $smarty.get.t eq "image"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fu.i"}</a></li>
                                            <li><a href="{$backend_url}/{href_entry key="be_upload"}?t=audio"{if $page_display eq "backend_tpl_upload" and $smarty.get.t eq "audio"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fu.a"}</a></li>
                                            <li><a href="{$backend_url}/{href_entry key="be_upload"}?t=document"{if $page_display eq "backend_tpl_upload" and $smarty.get.t eq "document"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.fu.d"}</a></li>
                                            {include file="tpl_backend/tpl_menupanel_import.tpl"}
                                        </ul>
                                    </li>
                                    
                                    {include file="tpl_backend/tpl_menupanel_cdn.tpl"}

                                    <li><a href="javascript:;"><i class="iconBe-coin"></i>{lang_entry key="backend.menu.adv"}</a>
                                        <ul>
                                            <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.adv.player"}</a>
                                            	<ul>
                                            		<li><a href="javascript:;" id="backend-menu-entry8-sub4" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.18"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry8-sub2" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.16"}</a></li>
                                            	</ul>
                                            </li>
                                            <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.adv.banner"}</a>
                                            	<ul>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub1" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.1"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub14" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.14"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub15" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.19"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub3" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.3"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub4" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.4"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub2" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.2"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub13" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.13"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub5" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.5"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub7" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.7"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub8" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.8"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub9" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.9"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub10" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.10"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry7-sub12" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.12"}</a></li>
                                            	</ul>
                                            </li>
                                            <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.adv.group"}</a>
                                            	<ul>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub1" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.1"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub14" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.14"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub15" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.19"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub3" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.3"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub4" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.4"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub2" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.2"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub13" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.13"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub5" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.5"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub7" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.7"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub8" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.8"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub9" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.9"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub10" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.10"}</a></li>
                                            		<li><a href="javascript:;" id="backend-menu-entry9-sub12" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_advertising"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.adv.sub.12"}</a></li>
                                            	</ul>
                                            </li>
                                        </ul>
                                    </li>
                                    <li><a href="javascript:;"><i class="iconBe-play"></i>{lang_entry key="backend.menu.pc"}</a>
                                        <ul>
                                            <li><a href="javascript:;" class="sub_menu">{lang_entry key="backend.menu.pc.vjs"}</a>
                                                <ul>
                                                    <li><a href="javascript:;" id="backend-menu-entry11-sub1" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_players"}"><i class="iconBe-arrow-right in-menu"></i>Hosted</a></li>
                                                    <li><a href="javascript:;" id="backend-menu-entry11-sub2" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_players"}"><i class="iconBe-arrow-right in-menu"></i>Embedded</a></li>
                                                </ul>
                                            </li>
                                        </ul>
                                    </li>

                                    <li><a href="javascript:;"><i class="iconBe-equalizer"></i>{lang_entry key="backend.menu.es"}</a>
                                        <ul>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub2" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.es.v"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub6" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.es.i"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub3" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.es.a"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub4" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.es.d"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub20" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.es.mp4"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub23" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.es.mob"}</a></li>
                                        </ul>
                                    </li>

                                    <li><a href="javascript:;"><i class="iconBe-steam"></i>{lang_entry key="backend.menu.st"}</a>
                                        <ul>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub1" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.st.mail"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub5" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.st.ban"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry2-sub11" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.st.act"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub12" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.st.sess"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub18" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.st.time"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub9" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.st.sysinfo"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry3-sub7" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.st.phpinfo"}</a></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                        </nav>
                    </li>
                    <div class="reenforce">

                    <div style="display:block" id="top_actions" class="no-session-off">
                    	<li class="main profile_holder">
                    		<div class="user-thumb-xlarge top">
                    		<span class="own-profile-image mt no-display"></span>
                    		<span class="no-session-icon" onclick="$('.own-profile-image.mt').click();"><i class="mt-open"></i></span>

                    		<div class="arrow_box blue hidden" id="user-arrow-box">{include file="tpl_backend/tpl_headernav_pop.tpl"}</div>
                    		</div>
                    	</li>
                        <li class="main profile_holder no-display">
                            <div class="profile_details">
                                <div class="profile_image" onclick="$(this).next().click();">
                                    <i class="icon-switch"></i>
                                </div>
                                <div class="profile_name" onclick="window.location='{$main_url}/{$backend_access_url}/{href_entry key="be_signout"}'">{lang_entry key="frontend.global.signout"}</div>
                            </div>
                            <section class="buttonset" style="display:none;">
                                <button id="showLeftPush" style="display:none">Show/Hide Left Push Menu</button>
                                <button id="showBottom" class="gn-icon gn-icon-menu"><span>Menu</span></button>
                            </section>

                        </li>
                        <li class="main messages_holder">
                            <div class="head_but messages">
                                <i class="icon-notification"></i>
                                <div class="items_count item_inactive"><span id="new-notifications-nr"><span></div>
                            </div>
                        </li>
                    </div>
                    </div>
                </ul>
