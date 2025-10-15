<div class="tp-h">{$smarty.session.ADMIN_NAME}</div>
<div class="tp-menu">

<div class="clearfix"></div>
<ul class="accordion tacc" id="top-session-accordion">

<li class=""><a class="dcjq-parent" href="{$backend_url}/{href_entry key="be_dashboard"}"><i class="icon-pie"></i> {lang_entry key="backend.menu.dash"}</a></li>
<li class=""><a class="dcjq-parent" href="{$backend_url}/{href_entry key="be_subscribers"}?rg=1"><i class="icon-pie"></i> {lang_entry key="backend.menu.ps.dashboard"}</a></li>
<li class=""><a class="dcjq-parent" href="{$backend_url}/{href_entry key="be_tokens"}?rg=1"><i class="icon-pie"></i> {lang_entry key="backend.menu.ps.token"}</a></li>

<li class="">
	<a class="dcjq-parent a-dt" href="javascript:;" rel="nofollow"><i class="icon-contrast"></i> {lang_entry key="frontend.global.darktheme"}: <span id="dark-mode-state-text">{if $theme_name_be|strpos:'dark'===0}{lang_entry key="frontend.global.on.text"}{else}{lang_entry key="frontend.global.off.text"}{/if}</span><i class="iconBe-chevron-right place-right"></i></a>
</li>
<li id="l-dt" style="display:none">
	<div class="dm-wrap">
		<div class="dm-head dm-head-dt"><i class="icon-arrow-left2"></i> {lang_entry key="frontend.global.darktheme"}</div>
		<p>{lang_entry key="frontend.global.darktheme.tip1"}<br><br>{lang_entry key="frontend.global.darktheme.tip2"}
		<div>
			<span class="label">{lang_entry key="frontend.global.darktheme"}</span>
			{insert name="themeSwitch"}
		</div>
	</div>
</li>
{if $smarty.session.lang_count gt 1}
<li class="">
	{insert name="langInit"}
</li>
{/if}
<li class=""><a class="dcjq-parent" href="{$backend_url}/{href_entry key="signout"}"><i class="icon-exit"></i> {lang_entry key="frontend.global.signout"}</a></li>
</ul>
</div>

{literal}
<script type="text/javascript">
$('a.a-dt').click(function(){$('#top-session-accordion li').hide();$('#l-dt').show()});
$('.dm-head-dt').click(function(){$('#top-session-accordion li').show();$('#l-dt, #l-ln').hide()});
$('a.a-ln').click(function(){$('#top-session-accordion li').hide();$('#l-ln').show()});
$('.dm-head-ln').click(function(){$('#top-session-accordion li').show();$('#l-ln, #l-dt').hide()});
</script>
{/literal}