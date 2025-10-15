	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
		<div class="clearfix"></div>
		<div class="vs-column full php-settings info-message-text" style="margin-bottom: 15px !important;margin-top: 0 !important; display: inline-block"></div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet5" input_type="text" entry_title="backend.menu.entry1.sub7.file.multi" entry_id="ct-entry-details5" input_name="backend_menu_entry1_sub7_file_multi" input_value=$multiple_file_uploads bb=0}
	    {generate_html bullet_id="ct-bullet1" input_type="switch_types" entry_title="backend.menu.entry1.sub7.file.video" entry_id="ct-entry-details1" input_name="backend_menu_entry1_sub7_file_video" input_value=$video_uploads bb=1}
	    {generate_html bullet_id="ct-bullet7" input_type="switch_types" entry_title="backend.menu.entry1.sub7.file.short" entry_id="ct-entry-details7" input_name="backend_menu_entry1_sub7_file_short" input_value=$short_uploads bb=1}
            </div>
            <div class="vs-column half fit vs-mask">
            {generate_html bullet_id="ct-bullet2" input_type="switch_types" entry_title="backend.menu.entry1.sub7.file.image" entry_id="ct-entry-details2" input_name="backend_menu_entry1_sub7_file_image" input_value=$image_uploads bb=1}
            {generate_html bullet_id="ct-bullet3" input_type="switch_types" entry_title="backend.menu.entry1.sub7.file.audio" entry_id="ct-entry-details3" input_name="backend_menu_entry1_sub7_file_audio" input_value=$audio_uploads bb=1}
            {generate_html bullet_id="ct-bullet4" input_type="switch_types" entry_title="backend.menu.entry1.sub7.file.doc" entry_id="ct-entry-details4" input_name="backend_menu_entry1_sub7_file_doc" input_value=$document_uploads bb=1}
	    </div>
	    <div class="clearfix"></div>
	    <div class="s-wrap">
		<div class="sortings left-half">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-keep-open.tpl"}</div>
	    </div>
	    <input type="hidden" name="ct_entry" id="ct_entry" value="" />
	</form>
	</div>
	<script type="text/javascript">
	    $(document).ready(function() {ldelim}
		$(".php-settings").html('{insert name="phpInfoText"}').click(function(){ldelim}$(this).detach();{rdelim});
	    {rdelim});
	</script>
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
                $('#slider-backend_menu_entry1_sub7_file_video_size').noUiSlider({ start: [ {$video_limit} ], step: 1, range: { 'min': [ 10 ], 'max': [ 5000 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_menu_entry1_sub7_file_video_size").Link('lower').to($("input[name='backend_menu_entry1_sub7_file_video_size']"));
                $('#slider-backend_menu_entry1_sub7_file_image_size').noUiSlider({ start: [ {$image_limit} ], step: 1, range: { 'min': [ 1 ], 'max': [ 100 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_menu_entry1_sub7_file_image_size").Link('lower').to($("input[name='backend_menu_entry1_sub7_file_image_size']"));
                $('#slider-backend_menu_entry1_sub7_file_audio_size').noUiSlider({ start: [ {$audio_limit} ], step: 1, range: { 'min': [ 1 ], 'max': [ 500 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_menu_entry1_sub7_file_audio_size").Link('lower').to($("input[name='backend_menu_entry1_sub7_file_audio_size']"));
                $('#slider-backend_menu_entry1_sub7_file_doc_size').noUiSlider({ start: [ {$document_limit} ], step: 1, range: { 'min': [ 1 ], 'max': [ 100 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_menu_entry1_sub7_file_doc_size").Link('lower').to($("input[name='backend_menu_entry1_sub7_file_doc_size']"));
                $('#slider-backend_menu_entry1_sub7_file_multi').noUiSlider({ start: [ {$multiple_file_uploads} ], step: 1, range: { 'min': [ 0 ], 'max': [ 50 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_menu_entry1_sub7_file_multi").Link('lower').to($("input[name='backend_menu_entry1_sub7_file_multi']"));
        {rdelim});
        </script>
