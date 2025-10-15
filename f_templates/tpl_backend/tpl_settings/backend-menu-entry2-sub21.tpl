	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    <div class="no-display">{generate_html bullet_id="ct-bullet1" input_type="grabber_functions" entry_title="backend.import.menu.grabber.functions" entry_id="ct-entry-details1" input_name="backend_import_menu_grabber_functions" input_value="" bb=1}</div>
	    {generate_html bullet_id="ct-bullet4" input_type="text" entry_title="backend.import.yt.api.key" entry_id="ct-entry-details4" input_name="backend_import_yt_api_key" input_value=$youtube_api_key bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="textarea" entry_title="backend.import.menu.yt.list" entry_id="ct-entry-details2" input_name="backend_import_menu_yt_list" input_value=$import_yt_channel_list bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet5" input_type="textarea" entry_title="backend.import.menu.vi.list" entry_id="ct-entry-details5" input_name="backend_import_menu_vi_list" input_value=$import_vi_user_list bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="textarea" entry_title="backend.import.menu.dm.list" entry_id="ct-entry-details3" input_name="backend_import_menu_dm_list" input_value=$import_dm_user_list bb=1}
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
	<script type="text/javascript">
        $(document).ready(function () {ldelim}
                $('.icheck-box input').each(function () {ldelim}
                        var self = $(this);
                        self.iCheck({ldelim}
                                checkboxClass: 'icheckbox_square-blue',
                                radioClass: 'iradio_square-blue',
                                increaseArea: '20%'
                                
                        {rdelim});
                {rdelim});
        {rdelim});
        </script>
