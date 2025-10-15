	<div class="wdmax" id="ct-wrapper">
	<form id="ct-set-form" action="">
	    <div class="wdmax top-bottom-padding left-float s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet2" input_type="server_paths_video" entry_title="backend.menu.entry6.sub1.conv.path" entry_id="ct-entry-details2" input_name="" input_value="" bb=1}
	    </div>
	    <div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry6.sub1.conv.que" entry_id="ct-entry-details1" input_name="backend_menu_entry6_sub1_conv_que" input_value=$conversion_video_que bb=1}
	    </div>
	    <div class="clearfix"></div>
	    <div class="wdmax top-bottom-padding left-float s-wrap">
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
        {rdelim});
        $(function() {ldelim}
                SelectList.init("thumbs_method");
        {rdelim});
        $('#slider-thumbs_nr').noUiSlider({ start: [ {$thumbs_nr} ], step: 1, range: { 'min': [ 1 ], 'max': [ 20 ] }, format:wNumb({ decimals: 0 }) });
        $("#slider-thumbs_nr").Link('lower').to($("input[name='thumbs_nr']"));
        </script>
