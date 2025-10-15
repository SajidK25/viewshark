	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry1.sub4.messaging.sys" entry_id="ct-entry-details1" input_name="backend_menu_entry1_sub4_messaging_sys" input_value=$internal_messaging bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="switch" entry_title="backend.menu.entry1.sub4.messaging.self" entry_id="ct-entry-details2" input_name="backend_menu_entry1_sub4_messaging_self" input_value=$allow_self_messaging bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.entry1.sub4.messaging.multi" entry_id="ct-entry-details3" input_name="backend_menu_entry1_sub4_messaging_multi" input_value=$allow_multi_messaging bb=1}
	    {generate_html bullet_id="ct-bullet4" input_type="text" entry_title="backend.menu.entry1.sub4.messaging.limit" entry_id="ct-entry-details4" input_name="backend_menu_entry1_sub4_messaging_limit" input_value=$multi_messaging_limit bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="switch" entry_title="backend.menu.entry1.sub4.messaging.attch" entry_id="ct-entry-details5" input_name="backend_menu_entry1_sub4_messaging_attch" input_value=$message_attachments bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet6" input_type="switch" entry_title="backend.menu.entry1.sub4.messaging.labels" entry_id="ct-entry-details6" input_name="backend_menu_entry1_sub4_messaging_labels" input_value=$custom_labels bb=1}
	    {generate_html bullet_id="ct-bullet7" input_type="switch" entry_title="backend.menu.entry1.sub4.messaging.counts" entry_id="ct-entry-details7" input_name="backend_menu_entry1_sub4_messaging_counts" input_value=$message_count bb=1}
	    {generate_html bullet_id="ct-bullet8" input_type="switch" entry_title="backend.menu.entry1.sub4.messaging.friends" entry_id="ct-entry-details8" input_name="backend_menu_entry1_sub4_messaging_friends" input_value=$user_friends bb=1}
	    {generate_html bullet_id="ct-bullet9" input_type="switch" entry_title="backend.menu.entry1.sub4.messaging.blocked" entry_id="ct-entry-details9" input_name="backend_menu_entry1_sub4_messaging_blocked" input_value=$user_blocking bb=1}
	    {generate_html bullet_id="ct-bullet10" input_type="switch" entry_title="backend.menu.entry1.sub4.messaging.approval" entry_id="ct-entry-details10" input_name="backend_menu_entry1_sub4_messaging_approval" input_value=$approve_friends bb=0}
	    </div>
	    <div class="clearfix"></div>
	    <div class="s-wrap">
		<div class="sortings left-half">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-keep-open.tpl"}</div>
	    </div>
	    <input type="hidden" name="ct_entry" id="ct_entry" value="" />
	</form>
	</div>
	{include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
        <script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
        <script type="text/javascript">{include file="f_scripts/be/js/jquery.nouislider.init.js"}</script>
	<script type="text/javascript">
	$(document).ready(function () {ldelim}
		$('#slider-backend_menu_entry1_sub4_messaging_limit').noUiSlider({ start: [ {$multi_messaging_limit} ], step: 1, range: { 'min': [ 1 ], 'max': [ 50 ] }, format:wNumb({ decimals: 0 }) });
		$("#slider-backend_menu_entry1_sub4_messaging_limit").Link('lower').to($("input[name='backend_menu_entry1_sub4_messaging_limit']"));
	{rdelim});
	</script>
