	<div class="wdmax" id="ct-wrapper">
	<form id="ct-set-form" action="">
	    <div class="wdmax section-bottom-border top-bottom-padding left-float s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>

	    <div class="vs-mask">{generate_html bullet_id="ct-bullet3" input_type="select" entry_title="backend.menu.entry2.sub3.timezone" entry_id="ct-entry-details3" input_name="backend_menu_entry2_sub3_timezone" input_value=$date_timezone bb=0}</div>

	    <div class="wdmax section-top-border top-bottom-padding left-float">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-keep-open.tpl"}</div>
	    </div>
	</form>
	</div>
	{include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
        <script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
	<script type="text/javascript">
        $(function() {ldelim}
                SelectList.init("backend_menu_entry2_sub3_timezone");
        {rdelim});
        </script>