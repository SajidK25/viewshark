	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	    <div class="s-wrap">
		<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
		<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
	    </div>
	    <div class="clearfix"></div>

		<div class="vs-column half vs-mask">
	    {generate_html bullet_id="ct-bullet1" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.approve" entry_id="ct-entry-details1" input_name="backend_menu_entry1_sub7_file_opt_approve" input_value=$file_approval bb=1}
	    {generate_html bullet_id="ct-bullet2" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.views" entry_id="ct-entry-details2" input_name="backend_menu_entry1_sub7_file_opt_views" input_value=$file_views bb=1}
	    {generate_html bullet_id="ct-bullet3" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.del" entry_id="ct-entry-details3" input_name="backend_menu_entry1_sub7_file_opt_del" input_value=$file_deleting bb=1}
	    {generate_html bullet_id="ct-bullet15" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.promo" entry_id="ct-entry-details15" input_name="backend_menu_entry1_sub7_file_opt_promo" input_value=$file_promo bb=1}
	    {generate_html bullet_id="ct-bullet4" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.fav" entry_id="ct-entry-details4" input_name="backend_menu_entry1_sub7_file_opt_fav" input_value=$file_favorites bb=1}
	    {generate_html bullet_id="ct-bullet5" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.pl" entry_id="ct-entry-details5" input_name="backend_menu_entry1_sub7_file_opt_pl" input_value=$file_playlists bb=1}
	    {generate_html bullet_id="ct-bullet7" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.comm" entry_id="ct-entry-details7" input_name="backend_menu_entry1_sub7_file_opt_comm" input_value=$file_comments bb=1}
	    {generate_html bullet_id="ct-bullet20" input_type="text" entry_title="backend.menu.entry1.sub6.comments.cons.f" entry_id="ct-entry-details20" input_name="backend_menu_entry1_sub6_comments_cons_f" input_value=$fcc_limit bb=1}
	    {generate_html bullet_id="ct-bullet8" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.vote" entry_id="ct-entry-details8" input_name="backend_menu_entry1_sub7_file_opt_vote" input_value=$file_comment_votes bb=1}
	    {generate_html bullet_id="ct-bullet22" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.spam" entry_id="ct-entry-details22" input_name="backend_menu_entry1_sub7_file_opt_spam" input_value=$file_comment_spam bb=1}
            {generate_html bullet_id="ct-bullet21" input_type="minmax" entry_title="backend.menu.entry1.sub6.comments.length.f" entry_id="ct-entry-details21" input_name="backend_menu_entry1_sub6_comments_length_f" input_value="file_comment_length" bb=1}
            	</div>
            	<div class="vs-column half fit vs-mask">
	    {generate_html bullet_id="ct-bullet13" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.history" entry_id="ct-entry-details13" input_name="backend_menu_entry1_sub7_file_opt_history" input_value=$file_history bb=1}
	    {generate_html bullet_id="ct-bullet14" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.watchlist" entry_id="ct-entry-details14" input_name="backend_menu_entry1_sub7_file_opt_watchlist" input_value=$file_watchlist bb=1}
	    {generate_html bullet_id="ct-bullet6" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.privacy" entry_id="ct-entry-details6" input_name="backend_menu_entry1_sub7_file_opt_privacy" input_value=$file_privacy bb=1}
	    {generate_html bullet_id="ct-bullet9" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.rate" entry_id="ct-entry-details9" input_name="backend_menu_entry1_sub7_file_opt_rate" input_value=$file_rating bb=1}
	    {generate_html bullet_id="ct-bullet10" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.resp" entry_id="ct-entry-details10" input_name="backend_menu_entry1_sub7_file_opt_resp" input_value=$file_responses bb=1}
	    {generate_html bullet_id="ct-bullet16" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.down" entry_id="ct-entry-details16" input_name="backend_menu_entry1_sub7_file_opt_down" input_value=$file_downloads bb=1}
	    {generate_html bullet_id="ct-bullet11" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.embed" entry_id="ct-entry-details11" input_name="backend_menu_entry1_sub7_file_opt_embed" input_value=$file_embedding bb=1}
	    {generate_html bullet_id="ct-bullet17" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.flag" entry_id="ct-entry-details17" input_name="backend_menu_entry1_sub7_file_opt_flag" input_value=$file_flagging bb=1}
	    {generate_html bullet_id="ct-bullet12" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.social" entry_id="ct-entry-details12" input_name="backend_menu_entry1_sub7_file_opt_social" input_value=$file_social_sharing bb=1}
	    {generate_html bullet_id="ct-bullet18" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.file" entry_id="ct-entry-details18" input_name="backend_menu_entry1_sub7_file_opt_file" input_value=$file_email_sharing bb=1}
	    {generate_html bullet_id="ct-bullet19" input_type="switch" entry_title="backend.menu.entry1.sub7.file.opt.perma" entry_id="ct-entry-details19" input_name="backend_menu_entry1_sub7_file_opt_perma" input_value=$file_permalink_sharing bb=0}
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
        	$('#slider-backend_menu_entry1_sub6_comments_cons_f').noUiSlider({ start: [ {$fcc_limit} ], step: 1, range: { 'min': [ 0 ], 'max': [ 100 ] }, format: wNumb({ decimals: 0 }) });
        	$("#slider-backend_menu_entry1_sub6_comments_cons_f").Link('lower').to($('#ct-entry-details20-input'));
        	$("#slider-backend_menu_entry1_sub6_comments_length_f_min").noUiSlider({ start: [ {$file_comment_min_length} ], step: 1, range: { 'min': [ 1 ], 'max': [ 100 ] }, format: wNumb({ decimals: 0 }) });
        	$("#slider-backend_menu_entry1_sub6_comments_length_f_min").Link('lower').to($("input[name='backend_menu_entry1_sub6_comments_length_f_min']"));
        	$("#slider-backend_menu_entry1_sub6_comments_length_f_max").noUiSlider({ start: [ {$file_comment_max_length} ], step: 1, range: { 'min': [ 10 ], 'max': [ 1000 ] }, format: wNumb({ decimals: 0 }) });
        	$("#slider-backend_menu_entry1_sub6_comments_length_f_max").Link('lower').to($("input[name='backend_menu_entry1_sub6_comments_length_f_max']"));
    	{rdelim});
	</script>