                                    <li>
                                        <a href="javascript:;"{if $page_display eq "backend_tpl_subscriber"} class="active"{/if}><i class="iconBe-pie"></i>{lang_entry key="backend.menu.ps.dashboard"}</a>
                                        <ul>
                                            <li><a href="{$main_url}/{$backend_access_url}/{href_entry key="be_subscribers"}?rg=1"{if $page_display eq "backend_tpl_subscriber" and $smarty.get.rg eq "1"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="account.entry.act.graph"}</a></li>
                                            <li><a href="{$main_url}/{$backend_access_url}/{href_entry key="be_subscribers"}?rp=1"{if $page_display eq "backend_tpl_subscriber" and $smarty.get.rp eq "1"} class="active"{/if}><i class="iconBe-arrow-right in-menu"></i>{lang_entry key="account.entry.act.rep"}</a></li>
                                        </ul>
                                    </li>

