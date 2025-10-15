	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet4" input_type="switch" entry_title="backend.menu.live.vod" entry_id="ct-entry-details4" input_name="backend_menu_live_vod" input_value=$live_vod bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="text" entry_title="backend.menu.live.del" entry_id="ct-entry-details5" input_name="backend_menu_live_del" input_value=$live_del bb=1}
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.live.token" entry_id="ct-entry-details1" input_name="backend_menu_live_token" input_value=$user_tokens bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.live.chat" entry_id="ct-entry-details3" input_name="backend_menu_live_chat" input_value=$live_chat bb=1}
	    {generate_html bullet_id="ct-bullet8" input_type="text" entry_title="backend.menu.live.chat.salt" entry_id="ct-entry-details8" input_name="backend_menu_live_chat_salt" input_value=$live_chat_salt bb=1}
	    {generate_html bullet_id="ct-bullet9" input_type="text" entry_title="backend.menu.live.cron.salt" entry_id="ct-entry-details9" input_name="backend_menu_live_cron_salt" input_value=$live_cron_salt bb=1}
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
	$(function(){ldelim}SelectList.init("backend_menu_af_p_currency");SelectList.init("backend_menu_af_requirements_type");{rdelim});
        $(document).ready(function () {ldelim}
        	$("input[name=backend_menu_af_p_figure]").keyup(function(){ldelim}
        		t = $(this);
        		v = $("input[name=backend_menu_af_p_share]").val();
        		v = (v < 1 ? 1 : (v > 100 ? 100 : v));
        		$("#s-pf").text(v);
        		tp = parseFloat($("input[name='backend_menu_af_p_figure']").val());
        		r = Math.round(v * tp) / 100;
        		$("#s-pc-off").text(r);
        	{rdelim});
        	$("input[name=backend_menu_af_p_units]").keyup(function(){ldelim}
        		$("#s-pv").text($(this).val());
        	{rdelim});
        	$("input[name=backend_menu_af_p_share]").keyup(function(){ldelim}
        		t = $(this);
        		v = t.val();
        		v = (v < 1 ? 1 : (v > 100 ? 100 : v));
        		$("#s-pc").text(v);
        		tp = parseFloat($("input[name='backend_menu_af_p_figure']").val());
        		r = Math.round(v * tp) / 100;
        		$("#s-pc-off").text(r);
        		t.val(v);
        	{rdelim});
        	$("select[name=backend_menu_af_p_currency]").change(function(){ldelim}
        		$("#s-cr").text($(this).val());
        	{rdelim});
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
