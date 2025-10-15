	<div class="wdmax left-float" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="wdmax left-float top-bottom-padding s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet2" input_type="select" entry_title="backend.menu.entry3.sub1.mtype" entry_id="ct-entry-details2" input_name="backend_menu_entry3_sub1_mtype" input_value='' bb=1}
	    {generate_html bullet_id="ct-bullet6" input_type="switch" entry_title="backend.menu.entry2.sub4.email" entry_id="ct-entry-details6" input_name="backend_menu_entry2_sub4_email" input_value=$email_logging bb=0}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="site_emails" entry_title="backend.menu.entry3.sub1.mails" entry_id="ct-entry-details1" input_name="backend_menu_entry3_sub1_mails" input_value='' bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="email_notif_be" entry_title="backend.menu.entry3.sub1.admin.notif" entry_id="ct-entry-details3" input_name="backend_menu_entry3_sub1_admin_notif" input_value='' bb=1}
	    </div>
	    <div class="clearfix"></div>
	    <div class="wdmax left-float top-bottom-padding s-wrap">
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
        $(function() {ldelim}
                SelectList.init("backend_menu_entry3_sub1_mtype");
                SelectList.init("backend_menu_entry3_sub1_smtp_pref");
        {rdelim});
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
