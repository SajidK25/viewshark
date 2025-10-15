	<link type="text/css" rel="stylesheet" href="{$main_url}/f_modules/m_backend/m_tools/m_linfo/layout/styles.css">
	<link type="text/css" rel="stylesheet" href="{$main_url}/f_modules/m_backend/m_tools/m_linfo/layout/icons.css">
	<script type="text/javascript" src="{$main_url}/f_modules/m_backend/m_tools/m_linfo/layout/scripts.js"></script>
	<div class="wdmax" id="ct-wrapper">
	    <form id="ct-set-form" action="">
	    	<div class="page-actions" style="display: none;">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    	<div class="clearfix"></div>
		<div id="system-info-wrap" class="left-float wd738 top-bottom-padding ">{lang_entry key="backend.menu.entry3.sub9.system"}</div>
		<input type="hidden" name="ct_entry" id="ct_entry" value="">
	    </form>
	    <script type="text/javascript">
		$("#system-info-wrap").mask("loading");
		$.post("{href_entry key="be_system_info"}", function(data){ldelim}
		    $("#system-info-wrap").html(data);
		    
		    $(".page-actions").clone(true).detach().insertAfter("#system-info-wrap > div.header").show();
		    
		    var s = document.createElement("script");
		    s.type = "text/javascript";
		    s.src = "{$main_url}/f_modules/m_backend/m_tools/m_linfo/layout/settings-accordion.js";
		    $("head").append(s);
		    
		    resizeDelimiter();
		    $("#ct-wrapper").unmask();
		{rdelim});
	    </script>
	</div>
	{include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
