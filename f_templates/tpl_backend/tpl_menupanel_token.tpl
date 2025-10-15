                                    <li>
                                        <a href="javascript:;"{if $page_display eq "backend_tpl_token"} class="active"{/if}><i class="iconBe-pie"></i>{lang_entry key="backend.menu.ps.token"}</a>
                                        <ul>
                                            <li><a href="{$main_url}/{$backend_access_url}/{href_entry key="be_tokens"}?rg=1"{if $page_display eq "backend_tpl_token" and $smarty.get.rg eq "1"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.token.report"}</a></li>
                                            <li><a href="{$main_url}/{$backend_access_url}/{href_entry key="be_tokens"}?rp=1"{if $page_display eq "backend_tpl_token" and $smarty.get.rp eq "1"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.token.payout"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry15-sub1" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.token.orders"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry15-sub2" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.menu.token.donations"}</a></li>
                                            <li><a href="javascript:;" id="backend-menu-entry14-sub8" class="menu-panel-entry-sub be-panel" rel-m="{href_entry key="be_settings"}"><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="backend.streaming.token.types"}</a></li>
                                            <li class="no-display"><a href="{$main_url}/{$backend_access_url}/{href_entry key="be_token"}?rp=1"{if $page_display eq "backend_tpl_token" and $smarty.get.rp eq "1"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="account.entry.act.rep"}</a></li>
                                        </ul>
                                    </li>

