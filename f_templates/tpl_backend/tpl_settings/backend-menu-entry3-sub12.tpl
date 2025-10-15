	<div class="wdmax" id="ct-wrapper">
	<form id="ct-set-form" action="">
	    <div class="wdmax top-bottom-padding left-float s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="text" entry_title="backend.menu.entry2.sub3.sessname" entry_id="ct-entry-details1" input_name="backend_menu_entry2_sub3_sessname" input_value=$session_name bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet2" input_type="text" entry_title="backend.menu.entry2.sub3.sesslife" entry_id="ct-entry-details2" input_name="backend_menu_entry2_sub3_sesslife" input_value=$session_lifetime bb=1}
	    </div>
	    <div class="clearfix"></div>
	    <div class="wdmax top-bottom-padding left-float s-wrap">
		<div class="sortings left-half">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-keep-open.tpl"}</div>
	    </div>
	</form>
	</div>
	{include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
        <script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
        <script type="text/javascript">{include file="f_scripts/be/js/jquery.nouislider.init.js"}</script>
	<script type="text/javascript">
	$(document).ready(function(){ldelim}
		$('#slider-backend_menu_entry2_sub3_sesslife').noUiSlider({ start: [ {$session_lifetime} ], step: 1, range: { 'min': [ 1 ], 'max': [ 600 ] }, format:wNumb({ decimals: 0 }) });
		$("#slider-backend_menu_entry2_sub3_sesslife").Link('lower').to($("input[name='backend_menu_entry2_sub3_sesslife']"));
	{rdelim});
	</script>