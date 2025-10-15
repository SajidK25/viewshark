<div class="container cbp-spmenu-push">
<link rel="stylesheet" type="text/css" href="{$styles_url_be}/affiliate.css">
<style id="be-affiliate-style">
.tabs nav{ldelim}text-align:left{rdelim}
.tabs-style-line nav ul{ldelim}display:flex{rdelim}
.u-chart {ldelim}width:100%;min-height:400px;{rdelim}
#video_table_sort_div.u-chart,#image_table_sort_div.u-chart,#audio_table_sort_div.u-chart,#document_table_sort_div.u-chart,#blog_table_sort_div.u-chart {ldelim}min-height:auto;{rdelim}
.u-chart g text{ldelim}font-size:14px;font-family:Ubuntu;line-height:50px;{rdelim}
.google-visualization-table-table td, .google-visualization-table-th {ldelim}font-family:Ubuntu;{rdelim}
h3.content-filter{ldelim}width:10%;color:#888;font-size:14px;{rdelim}
.content-filters li{ldelim}float:left;font-size:14px;margin-left:10px;line-height:47px{rdelim}
.content-filters i{ldelim}font-size:11px;{rdelim}
/*.content-filters a{ldelim}color:#888;{rdelim}*/
.filter-text{ldelim}font-size:14px;font-weight:normal;margin-left:10px;padding:0px 0;display:inline-block{rdelim}
.content-filters {ldelim}vertical-align: middle;display: inline-block;line-height: 47px;margin-right:10px;{rdelim}
.gapi-analytics-data-chart-styles-table-td{ldelim}font-size:14px !important;{rdelim}
h3.content-title{ldelim}width: 40%;{rdelim}
.view-mode-filters{ldelim}margin-right:0px;{rdelim}
.tfb{ldelim}font-weight: bold !important;{rdelim}
.sb-search-input{ldelim}border:0 !important;{rdelim}
svg > g:last-child > g:last-child {ldelim}pointer-events: none;{rdelim}
.loadmask-msg div:before{ldelim}font-size: 14px;{rdelim}
#view-limits input{ldelim}max-width: 50px; padding: 0 5px;margin-bottom: 0; height: 32px; margin-right: 10px; margin-top: 7px;{rdelim}
#lb-wrapper .responsive-accordion-panel {ldelim}display: flex;{rdelim}
.fancybox-inner #lb-wrapper .responsive-accordion-panel {ldelim}display: block;{rdelim}
#lb-wrapper {ldelim}position: relative; padding:0; display: inline-block; background-color: #ebeef1; width: 100%;{rdelim}
.fancybox-inner #lb-wrapper {ldelim}padding: 0 20px; background-color: #fff;{rdelim}
#lb-wrapper h3.content-title{ldelim}width:auto;font-size: 16px;{rdelim}
.fancybox-inner #lb-wrapper h3.content-title{ldelim}width: 95%;font-size: 16px;{rdelim}
#lb-wrapper .open-close-reports {ldelim}position:absolute;right:0;top:0;display: inline-block; float: right; margin-bottom: 10px;margin-top: 10px;{rdelim}
#lb-wrapper .greyed-out{ldelim}color: darkgrey; margin-left: 10px; font-size: 14px;{rdelim}
.fancybox-inner #lb-wrapper .greyed-out{ldelim}color: #999; margin-left: 0px;{rdelim}
#lb-wrapper .rep-info{ldelim}color: black; margin-left: 10px;{rdelim}
#lb-wrapper .rep-amt{ldelim}color: cadetblue; margin-left: 10px;{rdelim}
.views-details li {ldelim}margin-left: 9px; font-size: 14px;{rdelim}
.views-details li i{ldelim}margin-right: 10px;{rdelim}
.err-red, .conf-green {ldelim}font-size: 14px;{rdelim}
.err-red {ldelim}color:red;{rdelim}
.conf-green {ldelim}color:green;{rdelim}
.tabs nav ul, .content-wrap section, .tabs-style-topline {ldelim}max-width: none;{rdelim}
.content-current ul.responsive-accordion:first-child{ldelim}margin-top: 10px;{rdelim}
.tabs-style-topline nav li.tab-current a{ldelim}background-color: #fff;{rdelim}
.tabs-style-topline nav a span {ldelim}font-size: 13px;{rdelim}
#section-paid, #section-unpaid, #section-all {ldelim}min-height: 100px;{rdelim}
#info-text{ldelim}position: absolute; top: 13px; font-size: 15px;{rdelim}
.r-image{ldelim}max-width: 70%; display:inline-block; height: auto;{rdelim}
.views-details.views-thumbs{ldelim}float: left;display: inline-block; width: 60%;{rdelim}
.backend_tpl_subscriber .expand-entry {ldelim}margin-top: 15px;{rdelim}
.dark #lb-wrapper .rep-info{ldelim}color:#d0d0d0;{rdelim}
.dark .sb-search-fe {ldelim}border: 1px solid #000;{rdelim}
.dark .sb-icon-search-fe {ldelim}border: 0px solid #303030; background: #111;{rdelim}
.dark .tabs-style-topline nav li.tab-current a{ldelim}background-color: #111;{rdelim}
.dark #lb-wrapper {ldelim}background-color: transparent{rdelim}
.toggle-all-filters {ldelim}display: inline-block;margin-top: 20px;margin-right: 15px;cursor:pointer;display: none;{rdelim}
.mt10{ldelim}margin-top:10px;{rdelim}
.dark #time-sort-filters, .dark #week-sort-filters { border-bottom: 1px solid #2e2e2e; }
#time-sort-filters, #week-sort-filters { border-bottom: 1px solid #f0f0f0;border-radius: 7px; margin-top: 15px }
#search-boxes{ldelim}float:right{rdelim}
{literal}@media (max-width:960px){.views-details.views-thumbs{width: 80%;}}{/literal}
{literal}@media (max-width:1310px){#sb-search-fe,#sb-search-be{margin-right:10px;}#search-boxes-off{width: 100%; display: inline-block; margin-top:-7px; margin-bottom:3px; float: none;}}{/literal}
{literal}@media (max-width:960px){#lb-wrapper .rep-wrap{display: block; width: 100%;margin-top: -20px;}#lb-wrapper .greyed-out{margin-left: 30px;}}{/literal}
{literal}@media (max-width:920px){.sb-search-fe.sb-search-open{width: calc(100% - 20px)}#search-boxes .inner-search{width:100%}#sb-search-fe{margin-bottom:auto;}.inner-search,.search_holder_fe{width:auto}}{/literal}
{literal}@media (max-width:860px){.content-filters li{line-height:30px}#time-sort-filters h3.content-title.content-filter{display: none}#search-boxes{width: 100%;margin-bottom:10px;}#time-sort-filters section.filter{float: left;}.toggle-all-filters{display: block;}section.filter,section.inner-search,#search-boxes{display:none;}h3.content-title{width: 100%; display: inline-block;}#search-boxes{margin-top:0}a[rel-def-off="1"]{display:none;}}{/literal}
@media (max-width:640px){ldelim}
#info-text{ldelim}font-size:13px;{rdelim}
.entry-left{ldelim}float:left;width:100%;margin-top:-10px;{rdelim}
.entry-right{ldelim}float:left;width:100%;margin-top:-25px;{rdelim}
#v-tabs .responsive-accordion-panel .vs-column.half{ldelim}width: 100%;display: inline-block;{rdelim}
#v-tabs .responsive-accordion-panel, #lb-wrapper .responsive-accordion-panel{ldelim}display: flex;{rdelim}
#v-tabs .responsive-accordion-panel .vs-column.half.fit{ldelim}margin-top: 7px;{rdelim}
#lb-wrapper h3.content-title{ldelim}white-space: nowrap;overflow: hidden;text-overflow: ellipsis;width:90%{rdelim}
{rdelim}
{literal}@media (max-width:470px){#sb-search-be{margin-right:10px}}{/literal}
</style>
<script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
	{$html_payouts}
</div>
<script type="text/javascript">
{if $smarty.get.rg eq ""}
	{include file="tpl_backend/tpl_token/tpl_tokens_js.tpl"}
{else}
	{include file="tpl_backend/tpl_token/tpl_tokensrg_js.tpl"}
{/if}
</script>