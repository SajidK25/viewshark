<link rel="stylesheet" type="text/css" href="{$styles_url_be}/affiliate.css">
<style>
{literal}
.cbp-spmenu-push.vs-column.rg{margin-top: 25px;}
.rev_earned{background:rgba(133, 187, 101, 1);}
.rev_shared{background:rgba(101, 133, 187, 1);}
.rev_total{background:rgba(155, 101, 187, 1);}
.timer-rev{margin-bottom:5px;font-size:30px;}
.dark #time-sort-filters, .dark #week-sort-filters{border-bottom: 1px solid #2e2e2e;}
#time-sort-filters,#week-sort-filters{border-bottom:1px solid #f0f0f0;}
.vs-column.rg .vs-column.sixths{margin-bottom:5px !important}
{/literal}
</style>
            <div class="container-off cbp-spmenu-push vs-column rg">
            {if $smarty.get.f eq "" or $smarty.get.f eq "week" or $smarty.get.f eq "month" or $smarty.get.f eq "year"}
            {assign var=ff value="{if $smarty.get.f eq ""}week{else}{$smarty.get.f|sanitize}{/if}"}
            	<div class="vs-column sixths">
                    <div class="rev_earned files_holder">
                        <div class="counter_title">{lang_entry key="backend.sub.label.earned"}</div>
                        <div class="timer-rev">${$twearned}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-rev">{$twsubscriptions}</div>
                        <div class="status active-status">{lang_entry key="subnav.entry.sub"}</div>
                        <div class="small-inactive-timer small-inactive-timer-rev">{$twsubscribers}</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.subscribers.cap"}</div>
                        <div class="close_but icon-times" rel-close="rev_earned"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            	<div class="vs-column sixths">
                    <div class="rev_shared files_holder">
                        <div class="counter_title">{lang_entry key="backend.sub.label.shared"}</div>
                        <div class="timer-rev">${$twshared}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-rev">{$twsubscriptions}</div>
                        <div class="status active-status">{lang_entry key="subnav.entry.sub"}</div>
                        <div class="small-inactive-timer small-inactive-timer-rev">{$twsubscribers}</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.subscribers.cap"}</div>
                        <div class="close_but icon-times" rel-close="rev_shared"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            	<div class="vs-column sixths">
                    <div class="rev_total files_holder">
                        <div class="counter_title">{lang_entry key="backend.sub.label.total"}</div>
                        <div class="timer-rev">${$twtotal}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-rev">{$twsubscriptions}</div>
                        <div class="status active-status">{lang_entry key="subnav.entry.sub"}</div>
                        <div class="small-inactive-timer small-inactive-timer-rev">{$twsubscribers}</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.subscribers.cap"}</div>
                        <div class="close_but icon-times" rel-close="rev_total"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>


            	<div class="vs-column sixths">
                    <div class="rev_earned files_holder">
                        <div class="counter_title">{lang_entry key="backend.sub.label.earned"}</div>
                        <div class="timer-rev">${$lwearned}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-rev">{$lwsubscriptions}</div>
                        <div class="status active-status">{lang_entry key="subnav.entry.sub"}</div>
                        <div class="small-inactive-timer small-inactive-timer-rev">{$lwsubscribers}</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.subscribers.cap"}</div>
                        <div class="close_but icon-times" rel-close="rev_earned"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            	<div class="vs-column sixths">
                    <div class="rev_shared files_holder">
                        <div class="counter_title">{lang_entry key="backend.sub.label.shared"}</div>
                        <div class="timer-rev">${$lwshared}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-rev">{$lwsubscriptions}</div>
                        <div class="status active-status">{lang_entry key="subnav.entry.sub"}</div>
                        <div class="small-inactive-timer small-inactive-timer-rev">{$lwsubscribers}</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.subscribers.cap"}</div>
                        <div class="close_but icon-times" rel-close="rev_shared"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            	<div class="vs-column sixths fit">
                    <div class="rev_total files_holder">
                        <div class="counter_title">{lang_entry key="backend.sub.label.total"}</div>
                        <div class="timer-rev">${$lwtotal}</div>
                        <div class="clearfix"></div>
                        <div class="small-timer small-timer-rev">{$lwsubscriptions}</div>
                        <div class="status active-status">{lang_entry key="subnav.entry.sub"}</div>
                        <div class="small-inactive-timer small-inactive-timer-rev">{$lwsubscribers}</div>
                        <div class="status inactive-status">{lang_entry key="frontend.global.subscribers.cap"}</div>
                        <div class="close_but icon-times" rel-close="rev_total"></div>
                        <div class="clearfix"></div>
                    </div>
                </div>

                <div class="clearfix"></div><br>

                <div class="vs-column half">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-coin"></i> {if $smarty.get.y > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$smarty.get.y|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$mn[{$smarty.get.m|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.sub.dash.head.week.nr"}{$smarty.get.w|sanitize}{else}{lang_entry key="backend.sub.dash.head1.{$ff}"}{/if}{/if}{/if}</h1>
                            <div class="filters-loading">Loading filters...</div>
                            <div class="Titles-sub icheck-box no-display">
                            <div class="vs-column full">
                            <div class="filter-wrap">
                                <input type="checkbox" name="this_week_filter" value="e" class="this-week-filter" checked="checked"><label>{lang_entry key="backend.sub.label.earned"}</label>
                                <input type="checkbox" name="this_week_filter" value="s" class="this-week-filter" checked="checked"><label>{lang_entry key="backend.sub.label.shared"}</label>
                                <input type="checkbox" name="this_week_filter" value="t" class="this-week-filter" checked="checked"><label>{lang_entry key="backend.sub.label.total"}</label>

                            	<button class="button-grey search-button form-button save-button button-blue save-entry-button graph-update" id="this-week-uploads" type="button" value="1" onfocus="blur();">
			    		<span>{lang_entry key="frontend.global.apply"}</span>
			    	</button>
                            </div>
                            </div>
                            </div>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-1-container"></figure>
                        <ol class="Chartjs-legend" id="legend-1-container"></ol>
                    </div>
                </div>

                <div class="vs-column half fit">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-coin"></i> {if $smarty.get.y > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$smarty.get.y-1|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$mn[{$smarty.get.m-1|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.sub.dash.head.week.nr"}{$smarty.get.w-1|sanitize}{else}{lang_entry key="backend.sub.dash.head2.{$ff}"}{/if}{/if}{/if}</h1>
                            <div class="filters-loading">Loading filters...</div>
                            <div class="Titles-sub icheck-box no-display">
                            <div class="vs-column full">
                            <div class="filter-wrap">
                                <input type="checkbox" name="last_week_filter" value="e" class="last-week-filter" checked="checked"><label>{lang_entry key="backend.sub.label.earned"}</i></label>
                                <input type="checkbox" name="last_week_filter" value="s" class="last-week-filter" checked="checked"><label>{lang_entry key="backend.sub.label.shared"}</label>
                                <input type="checkbox" name="last_week_filter" value="t" class="last-week-filter" checked="checked"><label>{lang_entry key="backend.sub.label.total"}</label>

                            	<button class="button-grey search-button form-button save-button button-blue save-entry-button graph-update" id="last-week-uploads" type="button" value="1" onfocus="blur();">
			    		<span>{lang_entry key="frontend.global.apply"}</span>
			    	</button>
                            </div>
                            </div>
                            </div>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-2-container"></figure>
                        <ol class="Chartjs-legend" id="legend-2-container"></ol>
                    </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="vs-column half">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-users"></i> {if $smarty.get.y > 0}{lang_entry key="backend.sub.dash.subs.month.nr"}{$smarty.get.y|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.sub.dash.subs.month.nr"}{$mn[{$smarty.get.m|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.sub.dash.subs.week.nr"}{$smarty.get.w|sanitize}{else}{lang_entry key="backend.sub.dash.head3.{$ff}"}{/if}{/if}{/if}</h1>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-1a-container"></figure>
                        <ol class="Chartjs-legend" id="legend-1a-container"></ol>
                    </div>
                </div>
                <div class="vs-column half fit">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-users"></i> {if $smarty.get.y > 0}{lang_entry key="backend.sub.dash.subs.month.nr"}{$smarty.get.y-1|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.sub.dash.subs.month.nr"}{$mn[{$smarty.get.m-1|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.sub.dash.subs.week.nr"}{$smarty.get.w-1|sanitize}{else}{lang_entry key="backend.sub.dash.head4.{$ff}"}{/if}{/if}{/if}</h1>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-2a-container"></figure>
                        <ol class="Chartjs-legend" id="legend-2a-container"></ol>
                    </div>
                </div>
                <div class="clearfix"></div>
                <br>
                <div class="vs-column half">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-coin"></i> {if $smarty.get.y > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$smarty.get.y|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$mn[{$smarty.get.m|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.sub.dash.head.week.nr"}{$smarty.get.w|sanitize}{else}{lang_entry key="backend.sub.dash.head1.{$ff}"}{/if}{/if}{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-3-container"></figure>
                        <ol class="Chartjs-legend" id="legend-3-container"></ol>
                    </div>
		</div>
                <div class="vs-column half fit">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-coin"></i> {if $smarty.get.y > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$smarty.get.y-1|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$mn[{$smarty.get.m-1|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.sub.dash.head.week.nr"}{$smarty.get.w-1|sanitize}{else}{lang_entry key="backend.sub.dash.head2.{$ff}"}{/if}{/if}{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-3a-container"></figure>
                        <ol class="Chartjs-legend" id="legend-3a-container"></ol>
                    </div>
		</div>
                {else}
                <div class="vs-column half">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-users"></i> {lang_entry key="backend.dashboard.members.week"}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <figure class="Chartjs-figure" id="chart-4-container"></figure>
                        <ol class="Chartjs-legend" id="legend-4-container"></ol>
                    </div>
                </div>
                
                <div class="vs-column half fit">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="iconBe-coin"></i> {lang_entry key="backend.dashboard.earn.year"}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <figure class="Chartjs-figure" id="chart-8-container"></figure>
                        <ol class="Chartjs-legend" id="legend-8-container"></ol>
                    </div>
                </div>

		<div id="dash-dn">
                <div class="vs-column thirds stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-live"></i> {lang_entry key="backend.dashboard.stats.l"} {if $lcount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-11-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-11-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-11a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-11a-container"></ol>
                        </div>
                    </div>
                </div>

                <div class="vs-column thirds stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main">
                            	<i class="icon-video"></i> {lang_entry key="backend.dashboard.stats.v"}
                            	{if $vcount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}
                            </h1>
                            <div class="Titles-sub"></div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-3-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-3-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-3a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-3a-container"></ol>
                        </div>
                    </div>
                </div>

                <div class="vs-column thirds stats fit">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-image"></i> {lang_entry key="backend.dashboard.stats.i"} {if $icount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-6-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-6-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-6a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-6a-container"></ol>
                        </div>
                    </div>
                </div>
                <div class="vs-column thirds stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-headphones"></i> {lang_entry key="backend.dashboard.stats.a"} {if $acount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-5-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-5-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-5a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-5a-container"></ol>
                        </div>
                    </div>
                </div>

                <div class="vs-column thirds stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-file"></i> {lang_entry key="backend.dashboard.stats.d"} {if $dcount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-7-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-7-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-7a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-7a-container"></ol>
                        </div>
                    </div>
                </div>

                <div class="vs-column thirds fit stats">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-blog"></i> {lang_entry key="backend.dashboard.stats.b"} {if $bcount[0] eq 0}<span class="small-text">{lang_entry key="backend.dashboard.stats.none"}</span>{/if}</h1>
                            <div class="Titles-sub">
                            </div>
                        </header>
                        <div class="vs-column half">
                        	<figure class="Chartjs-figure" id="chart-9-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-9-container"></ol>
                        </div>
                        <div class="vs-column half fit">
                        	<figure class="Chartjs-figure" id="chart-9a-container"></figure>
                        	<ol class="Chartjs-legend" id="legend-9a-container"></ol>
                        </div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            {/if}
            </div>