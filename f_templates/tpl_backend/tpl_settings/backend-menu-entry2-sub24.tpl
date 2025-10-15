	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    	{generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry6.sub1.conv.prev.l" entry_id="ct-entry-details1" input_name="backend_menu_entry6_sub1_conv_prev_l" input_value=$conversion_live_previews bb=1}
	    	{generate_html bullet_id="ct-bullet2" input_type="switch" entry_title="backend.menu.entry6.sub1.conv.prev.v" entry_id="ct-entry-details2" input_name="backend_menu_entry6_sub1_conv_prev_v" input_value=$conversion_video_previews bb=1}
	    	{generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.entry6.sub1.conv.prev.i" entry_id="ct-entry-details3" input_name="backend_menu_entry6_sub1_conv_prev_i" input_value=$conversion_image_previews bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    	{generate_html bullet_id="ct-bullet4" input_type="switch" entry_title="backend.menu.entry6.sub1.conv.prev.a" entry_id="ct-entry-details4" input_name="backend_menu_entry6_sub1_conv_prev_a" input_value=$conversion_audio_previews bb=1}
	    	{generate_html bullet_id="ct-bullet5" input_type="switch" entry_title="backend.menu.entry6.sub1.conv.prev.d" entry_id="ct-entry-details5" input_name="backend_menu_entry6_sub1_conv_prev_d" input_value=$conversion_doc_previews bb=1}
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
