	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet7" input_type="switch" entry_title="backend.menu.entry1.sub2.be.userrec" entry_id="ct-entry-details7" input_name="backend_menu_entry1_sub2_be_userrec" input_value=$backend_username_recovery bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="switch" entry_title="backend.menu.entry1.sub2.be.passrec" entry_id="ct-entry-details5" input_name="backend_menu_entry1_sub2_be_passrec" input_value=$backend_password_recovery bb=1}
	    {generate_html bullet_id="ct-bullet10" input_type="text" entry_title="backend.menu.entry1.sub2.be.passrec.link" entry_id="ct-entry-details10" input_name="backend_menu_entry1_sub2_be_passrec_link" input_value=$backend_recovery_link_lifetime bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.entry1.sub2.fe.userrec" entry_id="ct-entry-details3" input_name="backend_menu_entry1_sub2_fe_userrec" input_value=$allow_username_recovery bb=1}
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry1.sub2.fe.passrec" entry_id="ct-entry-details1" input_name="backend_menu_entry1_sub2_fe_passrec" input_value=$allow_password_recovery bb=1}
	    {generate_html bullet_id="ct-bullet9" input_type="text" entry_title="backend.menu.entry1.sub2.fe.passrec.link" entry_id="ct-entry-details9" input_name="backend_menu_entry1_sub2_fe_passrec_link" input_value=$recovery_link_lifetime bb=0}
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
	$(document).ready(function(){ldelim}
		$('#slider-backend_menu_entry1_sub2_be_passrec_link').noUiSlider({ start: [ {$backend_recovery_link_lifetime} ], step: 1, range: { 'min': [ 1 ], 'max': [ 100 ] }, format:wNumb({ decimals: 0 }) });
		$("#slider-backend_menu_entry1_sub2_be_passrec_link").Link('lower').to($("input[name='backend_menu_entry1_sub2_be_passrec_link']"));
		$('#slider-backend_menu_entry1_sub2_fe_passrec_link').noUiSlider({ start: [ {$recovery_link_lifetime} ], step: 1, range: { 'min': [ 1 ], 'max': [ 100 ] }, format:wNumb({ decimals: 0 }) });
		$("#slider-backend_menu_entry1_sub2_fe_passrec_link").Link('lower').to($("input[name='backend_menu_entry1_sub2_fe_passrec_link']"));
	{rdelim});
	</script>