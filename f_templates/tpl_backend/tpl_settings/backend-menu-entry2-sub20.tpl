	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet0" input_type="text" entry_title="backend.menu.entry1.sub1.recaptcha.key" entry_id="ct-entry-details0" input_name="backend_menu_entry1_sub1_recaptcha_key" input_value=$recaptcha_site_key bb=1}
	    {generate_html bullet_id="ct-bullet13" input_type="switch" entry_title="backend.menu.entry1.sub1.captcha.l.b" entry_id="ct-entry-details13" input_name="backend_menu_entry1_sub1_captcha_l_b" input_value=$signin_captcha_be bb=1}
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry1.sub2.be.passrec.ver" entry_id="ct-entry-details1" input_name="backend_menu_entry1_sub2_be_passrec_ver" input_value=$backend_password_recovery_captcha bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.entry1.sub2.be.userrec.ver" entry_id="ct-entry-details3" input_name="backend_menu_entry1_sub2_be_userrec_ver" input_value=$backend_username_recovery_captcha bb=1}
	    {generate_html bullet_id="ct-bullet11" input_type="switch" entry_title="backend.menu.entry1.sub1.captcha" entry_id="ct-entry-details11" input_name="backend_menu_entry1_sub1_captcha" input_value=$signup_captcha bb=1}
            </div>
            <div class="vs-column half fit vs-mask">
            {generate_html bullet_id="ct-bullet4" input_type="text" entry_title="backend.menu.entry1.sub1.recaptcha.secret" entry_id="ct-entry-details4" input_name="backend_menu_entry1_sub1_recaptcha_secret" input_value=$recaptcha_secret_key bb=1}
            {generate_html bullet_id="ct-bullet12" input_type="switch" entry_title="backend.menu.entry1.sub1.captcha.l" entry_id="ct-entry-details12" input_name="backend_menu_entry1_sub1_captcha_l" input_value=$signin_captcha bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="switch" entry_title="backend.menu.entry1.sub2.fe.passrec.ver" entry_id="ct-entry-details5" input_name="backend_menu_entry1_sub2_fe_passrec_ver" input_value=$password_recovery_captcha bb=1}
	    {generate_html bullet_id="ct-bullet7" input_type="switch" entry_title="backend.menu.entry1.sub2.fe.userrec.ver" entry_id="ct-entry-details7" input_name="backend_menu_entry1_sub2_fe_userrec_ver" input_value=$username_recovery_captcha bb=1}
	    {generate_html bullet_id="ct-bullet9" input_type="switch" entry_title="backend.menu.entry1.sub5.em.captcha" entry_id="ct-entry-details9" input_name="backend_menu_entry1_sub5_em_captcha" input_value=$email_change_captcha bb=1}
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
	$(function() {ldelim}
		SelectList.init("backend_menu_entry1_sub2_be_passrec_lev");
		SelectList.init("backend_menu_entry1_sub2_be_userrec_lev");
		SelectList.init("backend_menu_entry1_sub1_captchalevel");
		SelectList.init("backend_menu_entry1_sub2_fe_passrec_lev");
		SelectList.init("backend_menu_entry1_sub2_fe_userrec_lev");
		SelectList.init("backend_menu_entry1_sub5_em_captcha_lev");
	{rdelim});
	</script>