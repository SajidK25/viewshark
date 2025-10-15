	<div id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
            {generate_html bullet_id="ct-bullet6" input_type="switch" entry_title="backend.menu.entry2.sub7.live" entry_id="ct-entry-details6" input_name="backend_menu_entry2_sub7_live" input_value=$live_module bb=1}
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry2.sub7.video" entry_id="ct-entry-details1" input_name="backend_menu_entry2_sub7_video" input_value=$video_module bb=1}
	    {generate_html bullet_id="ct-bullet7" input_type="switch" entry_title="backend.menu.entry2.sub7.short" entry_id="ct-entry-details7" input_name="backend_menu_entry2_sub7_short" input_value=$short_module bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.entry2.sub7.audio" entry_id="ct-entry-details3" input_name="backend_menu_entry2_sub7_audio" input_value=$audio_module bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
            {generate_html bullet_id="ct-bullet2" input_type="switch" entry_title="backend.menu.entry2.sub7.image" entry_id="ct-entry-details2" input_name="backend_menu_entry2_sub7_image" input_value=$image_module bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="switch" entry_title="backend.menu.entry2.sub7.blog" entry_id="ct-entry-details5" input_name="backend_menu_entry2_sub7_blog" input_value=$blog_module bb=0}
            {generate_html bullet_id="ct-bullet4" input_type="switch" entry_title="backend.menu.entry2.sub7.doc" entry_id="ct-entry-details4" input_name="backend_menu_entry2_sub7_doc" input_value=$document_module bb=0}
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
        