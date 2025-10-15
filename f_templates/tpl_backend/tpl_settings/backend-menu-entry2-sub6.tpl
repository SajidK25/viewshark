	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet2" input_type="text" entry_title="backend.menu.entry2.sub6.admin.user" entry_id="ct-entry-details2" input_name="backend_menu_entry2_sub6_admin_user" input_value=$backend_username bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="password" entry_title="backend.menu.entry2.sub6.admin.new.pass" entry_id="ct-entry-details3" input_name="backend_menu_entry2_sub6_admin_new_pass" input_value="" bb=1}
	    {generate_html bullet_id="ct-bullet4" input_type="password" entry_title="backend.menu.entry2.sub6.admin.conf.pass" entry_id="ct-entry-details4" input_name="backend_menu_entry2_sub6_admin_conf_pass" input_value="" bb=0}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet9" input_type="switch" entry_title="backend.menu.entry2.sub4.IPaccess.be" entry_id="ct-entry-details9" input_name="backend_menu_entry2_sub4_IPaccess_be" input_value=$backend_ip_based_access bb=1}
	    {generate_html bullet_id="ct-bullet10" input_type="textarea" entry_title="backend.menu.entry2.sub4.IPlist.be" entry_id="ct-entry-details10" input_name="backend_menu_entry2_sub4_IPlist_be" input_value="{insert name=getListContent from='ip-backend' assign=iplist}{$iplist}" bb=1}
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
