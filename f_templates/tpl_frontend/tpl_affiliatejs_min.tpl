{if ($page_display eq "tpl_affiliate" and $smarty.session.USER_AFFILIATE eq 1) or ($page_display eq "tpl_subscribers" and $smarty.session.USER_PARTNER eq 1) or ($page_display eq "tpl_tokens" and $smarty.session.USER_PARTNER eq 1)}
        <script type="text/javascript" src="{$scripts_url}/shared/datepicker/tiny-date-picker.min.js"></script>
        <script type="text/javascript" src="{$scripts_url}/shared/datepicker/date-range-picker.min.js"></script>
        {if $page_display eq "tpl_affiliate"}
        <script type="text/javascript" src="https://www.google.com/jsapi"></script>
        <script async defer src="https://maps.googleapis.com/maps/api/js?key={$affiliate_maps_api_key}" type="text/javascript"></script>
        {literal}<script type="text/javascript">(function () {[].slice.call(document.querySelectorAll(".tabs")).forEach(function (el) {new CBPFWTabs(el);});})();</script>{/literal}
        {/if}
        {if ($page_display eq "tpl_subscribers" or $page_display eq "tpl_tokens") and $smarty.get.rp ne ""}
        	{literal}<script type="text/javascript">(function () {[].slice.call(document.querySelectorAll(".tabs")).forEach(function (el) {new CBPFWTabs(el);});})();</script>{/literal}
        {elseif ($page_display eq "tpl_subscribers" or $page_display eq "tpl_tokens") and $smarty.get.rg ne ""}
        	<script type="text/javascript" src="{$javascript_url_be}/jsapi.js"></script><script type="text/javascript" src="{$modules_url_be}/m_tools/m_gasp/dash/Chart.min.js"></script><script type="text/javascript" src="{$modules_url_be}/m_tools/m_gasp/dash/moment.min.js"></script><script type="text/javascript">var ecount=new Array;var scount=new Array;var tcount=new Array;var twcount=new Array;twcount["total"]=0;twcount["shared"]={$twshared};twcount["earned"]=0;var lwcount=new Array;lwcount["total"]=0;lwcount["shared"]={$lwshared};lwcount["earned"]=0;var tw1={$tw2};var sw1={$sw2};var ew1={$ew2};var tw2={$tw1};var sw2={$sw1};var ew2={$ew1};var lws={$lws};var tws={$tws};$(document).ready(function(){ldelim}$('.icheck-box input').each(function(){ldelim}var self=$(this);self.iCheck({ldelim}checkboxClass:'icheckbox_square-blue',radioClass:'iradio_square-blue',increaseArea:'20%'{rdelim});{rdelim});$(".icheck-box").toggleClass("no-display");$(".filters-loading").addClass("no-display");{rdelim});</script><script type="text/javascript" src="{$javascript_url}/{if $page_display eq "tpl_subscribers"}subdashboard.min.js{else}tokendashboard.min.js{/if}"></script>
        {/if}
{/if}
