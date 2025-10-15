	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>
	    <div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.members.entry2.sub1.section" entry_id="ct-entry-details1" input_name="backend_menu_members_entry2_sub1_section" input_value=$public_channels bb=1}
	    {generate_html bullet_id="ct-bullet11" input_type="switch" entry_title="backend.menu.members.entry2.sub1.views" entry_id="ct-entry-details11" input_name="backend_menu_members_entry2_sub1_views" input_value=$channel_views bb=1}
	    {generate_html bullet_id="ct-bullet7" input_type="switch" entry_title="backend.menu.entry1.sub6.comments.chan" entry_id="ct-entry-details7" input_name="backend_menu_entry1_sub6_comments_chan" input_value=$channel_comments bb=1}
            {generate_html bullet_id="ct-bullet8" input_type="text" entry_title="backend.menu.entry1.sub6.comments.cons.c" entry_id="ct-entry-details8" input_name="backend_menu_entry1_sub6_comments_cons_c" input_value=$ucc_limit bb=1}
            {generate_html bullet_id="ct-bullet9" input_type="minmax" entry_title="backend.menu.entry1.sub6.comments.length.c" entry_id="ct-entry-details9" input_name="backend_menu_entry1_sub6_comments_length_c" input_value="comment_length" bb=1}
            </div>
            <div class="vs-column half fit vs-mask">
            {generate_html bullet_id="ct-bullet10" input_type="switch" entry_title="backend.menu.members.entry2.sub1.follows" entry_id="ct-entry-details10" input_name="backend_menu_members_entry2_sub1_follows" input_value=$user_follows bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="switch" entry_title="backend.menu.members.entry2.sub1.bgimage" entry_id="ct-entry-details2" input_name="backend_menu_members_entry2_sub1_bgimage" input_value=$channel_backgrounds bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.members.entry2.sub1.bulletins" entry_id="ct-entry-details3" input_name="backend_menu_members_entry2_sub1_bulletins" input_value=$channel_bulletins bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="text" entry_title="backend.menu.members.entry2.sub1.avatar" entry_id="ct-entry-details5" input_name="backend_menu_members_entry2_sub1_avatar" input_value=$user_image_allowed_extensions bb=1}
	    {generate_html bullet_id="ct-bullet6" input_type="text" entry_title="backend.menu.members.entry2.sub1.bg" entry_id="ct-entry-details6" input_name="backend_menu_members_entry2_sub1_bg" input_value=$channel_bg_allowed_extensions bb=0}
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
	$(document).ready(function() {ldelim}
		$('#slider-backend_menu_entry1_sub6_comments_cons_c').noUiSlider({ start: [ {$ucc_limit} ], step: 1, range: { 'min': [ 1 ], 'max': [ 20 ] }, format:wNumb({ decimals: 0 }) });
		$("#slider-backend_menu_entry1_sub6_comments_cons_c").Link('lower').to($("input[name='backend_menu_entry1_sub6_comments_cons_c']"));
		$('#slider-backend_menu_entry1_sub6_comments_length_c_min').noUiSlider({ start: [ {$comment_min_length} ], step: 1, range: { 'min': [ 1 ], 'max': [ 100 ] }, format:wNumb({ decimals: 0 }) });
		$("#slider-backend_menu_entry1_sub6_comments_length_c_min").Link('lower').to($("input[name='backend_menu_entry1_sub6_comments_length_c_min']"));
		$('#slider-backend_menu_entry1_sub6_comments_length_c_max').noUiSlider({ start: [ {$comment_max_length} ], step: 1, range: { 'min': [ 10 ], 'max': [ 1000 ] }, format:wNumb({ decimals: 0 }) });
		$("#slider-backend_menu_entry1_sub6_comments_length_c_max").Link('lower').to($("input[name='backend_menu_entry1_sub6_comments_length_c_max']"));
		$('#slider-backend_menu_members_entry2_sub1_avatar_size').noUiSlider({ start: [ {$user_image_max_size} ], step: 1, range: { 'min': [ 1 ], 'max': [ 10 ] }, format:wNumb({ decimals: 0 }) });
		$("#slider-backend_menu_members_entry2_sub1_avatar_size").Link('lower').to($("input[name='backend_menu_members_entry2_sub1_avatar_size']"));
		$('#slider-backend_menu_members_entry2_sub1_bg_size').noUiSlider({ start: [ {$channel_bg_max_size} ], step: 1, range: { 'min': [ 1 ], 'max': [ 10 ] }, format:wNumb({ decimals: 0 }) });
		$("#slider-backend_menu_members_entry2_sub1_bg_size").Link('lower').to($("input[name='backend_menu_members_entry2_sub1_bg_size']"));
	{rdelim});
	</script>