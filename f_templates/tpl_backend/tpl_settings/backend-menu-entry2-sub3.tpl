	<div class="" id="ct-wrapper">
	<form id="ct-set-form" action="" method="post">
	        <div class="s-wrap">
            	<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
            	<div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
    	    </div>
    	    <div class="clearfix"></div>
    	    <div class="vs-column half vs-mask">
    	    {generate_html input_type="video_sitemap" bullet_id="ct-bullet1" entry_id="ct-entry-details1" entry_title="backend.menu.entry1.sub11.sitemap.video" bb=1}
    	    {generate_html input_type="short_sitemap" bullet_id="ct-bullet2" entry_id="ct-entry-details2" entry_title="backend.menu.entry1.sub11.sitemap.short" bb=0}
    	    </div>
    	    <div class="vs-column half fit vs-mask">
    	    {generate_html input_type="image_sitemap" bullet_id="ct-bullet3" entry_id="ct-entry-details3" entry_title="backend.menu.entry1.sub11.sitemap.image" bb=1}
    	    {generate_html input_type="global_sitemap" bullet_id="ct-bullet4" entry_id="ct-entry-details4" entry_title="backend.menu.entry1.sub11.sitemap.global" bb=0}
    	    </div>
    	    <div class="clearfix"></div>
    	    <div>
                <div class="sortings left-half">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
                <div class="page-actions">{include file="tpl_backend/tpl_settings/ct-keep-open.tpl"}</div>
            </div>
            <input type="hidden" name="ct_entry" id="ct_entry" value="" />
        </form>
	</div>

	{include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
	{include file="tpl_backend/tpl_settings/ct-actions-js.tpl"}
	<script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
	<script type="text/javascript">{include file="f_scripts/be/js/jquery.nouislider.init.js"}</script>

	<script type="text/javascript">
        var main_url = "{$main_url}";
        $(document).ready(function(){ldelim}
	    $(".sitemap-rebuild").unbind().on("click", function () {ldelim}
    		$(".container").mask("");
    		$.post(current_url + menu_section + '?s={$smarty.get.s|sanitize}&do=global-sitemap', $("#ct-set-form").serialize(), function(data) {ldelim}
                    $("#ct-wrapper").html(data);
                    $(".container").unmask();
                {rdelim});
    	    {rdelim});
    	    $(".sitemap-video-rebuild").unbind().on("click", function () {ldelim}
    		$(".container").mask("");
    		$.post(current_url + menu_section + '?s={$smarty.get.s|sanitize}&do=video-sitemap', $("#ct-set-form").serialize(), function(data) {ldelim}
                    $("#ct-wrapper").html(data);
                    $(".container").unmask();
                {rdelim});
    	    {rdelim});
    	    $(".sitemap-short-rebuild").unbind().on("click", function () {ldelim}
    		$(".container").mask("");
    		$.post(current_url + menu_section + '?s={$smarty.get.s|sanitize}&do=short-sitemap', $("#ct-set-form").serialize(), function(data) {ldelim}
                    $("#ct-wrapper").html(data);
                    $(".container").unmask();
                {rdelim});
    	    {rdelim});
    	    $(".sitemap-image-rebuild").unbind().on("click", function () {ldelim}
                $(".container").mask("");
                $.post(current_url + menu_section + '?s={$smarty.get.s|sanitize}&do=image-sitemap', $("#ct-set-form").serialize(), function(data) {ldelim}
                    $("#ct-wrapper").html(data);
                    $(".container").unmask();
                {rdelim});
            {rdelim});
        {rdelim});
	</script>
	<script type="text/javascript">
	$(document).ready(function(){ldelim}
                $('.icheck-box input').each(function () {ldelim}
                        var self = $(this);
                        self.iCheck({ldelim}
                                checkboxClass: 'icheckbox_square-blue',
                                radioClass: 'iradio_square-blue',
                                increaseArea: '20%'
                                
                        {rdelim});
                {rdelim});
                $('#slider-sm_max_entries').noUiSlider({ start: [ {$sitemap_global_max} ], step: 1, range: { 'min': [ 10 ], 'max': [ 45000 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-sm_max_entries").Link('lower').to($("input[name='sm_max_entries']"));
                $('#slider-sm_max_video').noUiSlider({ start: [ {$sitemap_video_max} ], step: 1, range: { 'min': [ 10 ], 'max': [ 45000 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-sm_max_video").Link('lower').to($("input[name='sm_max_video']"));
                $('#slider-sm_max_short').noUiSlider({ start: [ {$sitemap_short_max} ], step: 1, range: { 'min': [ 10 ], 'max': [ 45000 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-sm_max_short").Link('lower').to($("input[name='sm_max_short']"));
                $('#slider-sm_max_image').noUiSlider({ start: [ {$sitemap_image_max} ], step: 1, range: { 'min': [ 10 ], 'max': [ 1000 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-sm_max_image").Link('lower').to($("input[name='sm_max_image']"));
        {rdelim});
        </script>
        