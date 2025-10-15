    <div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.section.access" entry_id="ct-entry-details1" input_name="backend_menu_section_access" input_value=$global_signup bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="textarea" entry_title="backend.menu.close.message" entry_id="ct-entry-details2" input_name="backend_menu_close_message" input_value=$disabled_signup_message bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="switch" entry_title="backend.menu.section.IPaccess" entry_id="ct-entry-details5" input_name="backend_menu_section_IPaccess" input_value=$signup_ip_access bb=1}
	    {generate_html bullet_id="ct-bullet6" input_type="textarea" entry_title="backend.menu.section.IPlist" entry_id="ct-entry-details6" input_name="backend_menu_section_IPlist" input_value="{insert name=getListContent from='ip-signup' assign=iplist}{$iplist}" bb=1}
	    {generate_html bullet_id="ct-bullet7" input_type="switch" entry_title="backend.menu.entry1.sub1.mailres" entry_id="ct-entry-details7" input_name="backend_menu_entry1_sub1_mailres" input_value=$signup_domain_restriction bb=1}
	    {generate_html bullet_id="ct-bullet8" input_type="textarea" entry_title="backend.menu.entry1.sub1.maillist" entry_id="ct-entry-details8" input_name="backend_menu_entry1_sub1_maillist" input_value="{insert name=getListContent from='email-domains' assign=maillist}{$maillist}" bb=1}
	    {generate_html bullet_id="ct-bullet18" input_type="switch" entry_title="backend.menu.entry1.sub2.fe.act.approval" entry_id="ct-entry-details18" input_name="backend_menu_entry1_sub2_fe_act_approval" input_value=$account_approval bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet19" input_type="switch" entry_title="backend.menu.entry1.sub2.fe.act.mver" entry_id="ct-entry-details19" input_name="backend_menu_entry1_sub2_fe_act_mver" input_value=$account_email_verification bb=1}
	    {generate_html bullet_id="ct-bullet14" input_type="textarea" entry_title="backend.menu.entry1.sub1.userlist" entry_id="ct-entry-details14" input_name="backend_menu_entry1_sub1_userlist" input_value="{insert name=getListContent from='usernames' assign=userlist}{$userlist}" bb=1}
	    {generate_html bullet_id="ct-bullet17" input_type="uformat" entry_title="backend.menu.entry1.sub1.uformat" entry_id="ct-entry-details17" input_name="backend_menu_entry1_sub1_uformat" input_value="" bb=1}
	    {generate_html bullet_id="ct-bullet11" input_type="minmax" entry_title="backend.menu.entry1.sub1.userlen" entry_id="ct-entry-details11" input_name="backend_menu_entry1_sub1_userlen" input_value="username_length" bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.entry1.sub1.uavail" entry_id="ct-entry-details3" input_name="backend_menu_entry1_sub1_uavail" input_value=$signup_username_availability bb=1}
	    {generate_html bullet_id="ct-bullet4" input_type="switch" entry_title="backend.menu.entry1.sub1.pmeter" entry_id="ct-entry-details4" input_name="backend_menu_entry1_sub1_pmeter" input_value=$signup_password_meter bb=1}
	    {generate_html bullet_id="ct-bullet12" input_type="minmax" entry_title="backend.menu.entry1.sub1.passlen" entry_id="ct-entry-details12" input_name="backend_menu_entry1_sub1_passlen" input_value="password_length" bb=1}
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
                $('.icheck-box input').each(function () {ldelim}
                        var self = $(this);
                        self.iCheck({ldelim}
                                checkboxClass: 'icheckbox_square-blue',
                                radioClass: 'iradio_square-blue',
                                increaseArea: '20%'
                                
                        {rdelim});
                {rdelim});
                $('#slider-backend_menu_entry1_sub1_userlen_min').noUiSlider({ start: [ {$signup_min_username} ], step: 1, range: { 'min': [ 1 ], 'max': [ 20 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_menu_entry1_sub1_userlen_min").Link('lower').to($("input[name='backend_menu_entry1_sub1_userlen_min']"));
                $('#slider-backend_menu_entry1_sub1_userlen_max').noUiSlider({ start: [ {$signup_max_username} ], step: 1, range: { 'min': [ 1 ], 'max': [ 30 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_menu_entry1_sub1_userlen_max").Link('lower').to($("input[name='backend_menu_entry1_sub1_userlen_max']"));

                $('#slider-backend_menu_entry1_sub1_passlen_min').noUiSlider({ start: [ {$signup_min_password} ], step: 1, range: { 'min': [ 5 ], 'max': [ 20 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_menu_entry1_sub1_passlen_min").Link('lower').to($("input[name='backend_menu_entry1_sub1_passlen_min']"));
                $('#slider-backend_menu_entry1_sub1_passlen_max').noUiSlider({ start: [ {$signup_max_password} ], step: 1, range: { 'min': [ 5 ], 'max': [ 100 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_menu_entry1_sub1_passlen_max").Link('lower').to($("input[name='backend_menu_entry1_sub1_passlen_max']"));
        {rdelim});
	</script>
