	<div class="wdmax left-float" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="wdmax section-bottom-border left-float top-bottom-padding s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet2" input_type="vjs_layout" entry_title="backend.player.menu.vjs.layout" entry_id="ct-entry-details2" input_name="backend_player_menu_vjs_layout" input_value="" bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="vjs_behavior" entry_title="backend.player.menu.vjs.behavior" entry_id="ct-entry-details3" input_name="backend_player_menu_vjs_behavior" input_value="" bb=1}
	    {generate_html bullet_id="ct-bullet4" input_type="vjs_logo" entry_title="backend.player.menu.vjs.logo" entry_id="ct-entry-details4" input_name="backend_player_menu_vjs_logo" input_value="" bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet6" input_type="vjs_advertising" entry_title="backend.player.menu.vjs.adv" entry_id="ct-entry-details6" input_name="backend_player_menu_vjs_adv" input_value="" bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="vjs_rightclick" entry_title="backend.player.menu.vjs.rc" entry_id="ct-entry-details5" input_name="backend_player_menu_vjs_rc" input_value="" bb=1}
	    </div>
	    <div class="clearfix"></div>
	    <div class="wdmax section-top-border left-float top-bottom-padding s-wrap">
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
                $(function() {ldelim}
                	SelectList.init("vjs_layout_controls");
                	SelectList.init("vjs_autostart");
                	SelectList.init("vjs_loop");
                	SelectList.init("vjs_muted");
                	SelectList.init("vjs_related");
                	SelectList.init("vjs_logo_position");
                	SelectList.init("vjs_advertising");
        	{rdelim});
        {rdelim});
        </script>
