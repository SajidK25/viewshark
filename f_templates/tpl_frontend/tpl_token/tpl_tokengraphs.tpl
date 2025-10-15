<link rel="stylesheet" type="text/css" href="{$styles_url_be}/dash.css">
<!--<link rel="stylesheet" type="text/css" href="{$styles_url_be}/affiliate.css">-->
<style>
{literal}
.cbp-spmenu-push.vs-column.rg{margin-top: 25px;}
.rev_earned{background:rgba(133, 187, 101, 1);}
.rev_shared{background:rgba(101, 133, 187, 1);}
.rev_total{background:rgba(155, 101, 187, 1);}
.timer-rev{margin-bottom:5px;font-size:30px;}
.Chartjs-figure{margin:auto;}
.Chartjs-legend{margin-top:0;}
.dark .Chartjs-legend{background-color:#131313;}
.Chartjs-legend>li{padding:8px 0 0px 0;}
.Titles{margin:0;}
.Titles-main{padding:10px}
.dark .Titles-main {background:#131313;color:#fff;}
.cbp-spmenu-push .col {width: 100%;padding-bottom:25px;}
.dark #time-sort-filters, .dark #week-sort-filters { border-bottom: 1px solid #2e2e2e; }
#time-sort-filters, #week-sort-filters { border-bottom: 1px solid #f0f0f0; }
@media (max-width: 860px) { section.filter{display:block} .toggle-all-filters{display:none} h3.content-title{width:75%} }
{/literal}
</style>
            <div class="container-off cbp-spmenu-push vs-column rg full">
            {if $smarty.get.f eq "" or $smarty.get.f eq "week" or $smarty.get.f eq "month" or $smarty.get.f eq "year"}
            {assign var=ff value="{if $smarty.get.f eq ""}week{else}{$smarty.get.f|sanitize}{/if}"}
                <div class="clearfix"></div>
                <div class="vs-column half col">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-coin"></i> {if $smarty.get.y > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$smarty.get.y|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$mn[{$smarty.get.m|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.sub.dash.head.week.nr"}{$smarty.get.w|sanitize}{else}{lang_entry key="backend.sub.dash.head1.{$ff}"}{/if}{/if}{/if} - <b>{$twshared/100} {$pcurrency} - {$twshared} {lang_entry key="frontend.global.tokens"}</b></h1>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-1-container"></figure>
                        <ol class="Chartjs-legend" id="legend-1-container"></ol>
                    </div>
                </div>

                <div class="vs-column half col">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-users"></i> {if $smarty.get.y > 0}{lang_entry key="backend.tok.dash.subs.month.nr"}{$smarty.get.y|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$mn[{$smarty.get.m|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.tok.dash.subs.week.nr"}{$smarty.get.w|sanitize}{else}{lang_entry key="backend.tok.dash.head3.{$ff}"}{/if}{/if}{/if} - <b>{$twsubscriptions}</b></h1>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-1a-container"></figure>
                        <ol class="Chartjs-legend" id="legend-1a-container"></ol>
                    </div>
                </div>

                <div class="vs-column half fit col">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-coin"></i> {if $smarty.get.y > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$smarty.get.y-1|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.sub.dash.head.month.nr"}{$mn[{$smarty.get.m-1|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.sub.dash.head.week.nr"}{$smarty.get.w-1|sanitize}{else}{lang_entry key="backend.sub.dash.head2.{$ff}"}{/if}{/if}{/if} - <b>{$lwshared/100} {$pcurrency} - {$lwshared} {lang_entry key="frontend.global.tokens"}</b></h1>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-2-container"></figure>
                        <ol class="Chartjs-legend" id="legend-2-container"></ol>
                    </div>
                </div>
                <div class="clearfix"></div>

                <div class="vs-column half fit col">
                    <div class="block">
                        <header class="Titles">
                            <h1 class="Titles-main"><i class="icon-users"></i> {if $smarty.get.y > 0}{lang_entry key="backend.tok.dash.dons.month.nr"}{$smarty.get.y-1|sanitize}{else}{if $smarty.get.m > 0}{lang_entry key="backend.tok.dash.subs.month.nr"}{$mn[{$smarty.get.m-1|sanitize}]}{else}{if $smarty.get.w > 0}{lang_entry key="backend.tok.dash.subs.week.nr"}{$smarty.get.w-1|sanitize}{else}{lang_entry key="backend.tok.dash.head4.{$ff}"}{/if}{/if}{/if} - <b>{$lwsubscriptions}</b></h1>
                        </header>
                        <div class="clearfix"></div>
                        <figure class="Chartjs-figure" id="chart-2a-container"></figure>
                        <ol class="Chartjs-legend" id="legend-2a-container"></ol>
                    </div>
                </div>
                <div class="clearfix"></div>
                {else}
            <div class="clearfix"></div>
            {/if}
            </div>