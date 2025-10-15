	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet0" input_type="switch" entry_title="backend.menu.entry1.sub3.fb.module" entry_id="ct-entry-details0" input_name="backend_menu_entry1_sub3_fb_module" input_value=$fb_auth bb=1}
	    {generate_html bullet_id="ct-bullet7" input_type="text" entry_title="backend.menu.entry1.sub3.fb.appid" entry_id="ct-entry-details7" input_name="backend_menu_entry1_sub3_fb_appid" input_value=$fb_app_id bb=1}
	    {generate_html bullet_id="ct-bullet8" input_type="text" entry_title="backend.menu.entry1.sub3.fb.secret" entry_id="ct-entry-details8" input_name="backend_menu_entry1_sub3_fb_secret" input_value=$fb_app_secret bb=1}
	    {generate_html bullet_id="ct-bullet9" input_type="switch" entry_title="backend.menu.entry1.sub3.gp.module" entry_id="ct-entry-details9" input_name="backend_menu_entry1_sub3_gp_module" input_value=$gp_auth bb=1}
	    {generate_html bullet_id="ct-bullet10" input_type="text" entry_title="backend.menu.entry1.sub3.gp.appid" entry_id="ct-entry-details10" input_name="backend_menu_entry1_sub3_gp_appid" input_value=$gp_app_id bb=1}
	    {generate_html bullet_id="ct-bullet11" input_type="text" entry_title="backend.menu.entry1.sub3.gp.secret" entry_id="ct-entry-details11" input_name="backend_menu_entry1_sub3_gp_secret" input_value=$gp_app_secret bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.entry1.sub3.fe.signin" entry_id="ct-entry-details3" input_name="backend_menu_entry1_sub3_fe_signin" input_value=$frontend_signin_section bb=1}
	    {generate_html bullet_id="ct-bullet6" input_type="switch" entry_title="backend.menu.entry1.sub3.fe.signin.ct" entry_id="ct-entry-details6" input_name="backend_menu_entry1_sub3_fe_signin_ct" input_value=$frontend_signin_count bb=1}
	    {generate_html bullet_id="ct-bullet4" input_type="switch" entry_title="backend.menu.entry1.sub3.fe.signin.rem" entry_id="ct-entry-details4" input_name="backend_menu_entry1_sub3_fe_signin_rem" input_value=$login_remember bb=0}
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry1.sub3.be.signin" entry_id="ct-entry-details1" input_name="backend_menu_entry1_sub3_be_signin" input_value=$backend_signin_section bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="switch" entry_title="backend.menu.entry1.sub3.be.signin.ct" entry_id="ct-entry-details5" input_name="backend_menu_entry1_sub3_be_signin_ct" input_value=$backend_signin_count bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="switch" entry_title="backend.menu.entry1.sub3.be.signin.rem" entry_id="ct-entry-details2" input_name="backend_menu_entry1_sub3_be_signin_rem" input_value=$backend_remember bb=1}
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
