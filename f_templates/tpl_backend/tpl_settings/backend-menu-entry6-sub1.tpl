	<script type="text/javascript">var menu_section = '{href_entry key="be_files"}';</script>
	<div class="wdmax entry-list" id="ct-wrapper">
	    <div class="section-top-bar{if $smarty.get.do eq "add"}-add{/if} left-float top-bottom-padding">
            	<div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
            	<div class="page-actions">
            		{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}
                	<div class="search-hold">
                		{include file="tpl_frontend/tpl_file/tpl_search_inner_be.tpl"}
                	</div>
            	</div>
            	<div class="clearfix"></div>
    	    </div>
    	    <div class="clearfix"></div>
            
    	    {generate_html type="file_manager_video" bullet_id="ct-bullet1" entry_id="ct-entry-details1" bb=1}
            
    	    <div id="thumb-response"></div>
    	    <input type="hidden" id="p-user-key" name="p_user_key" value="{$smarty.get.u|sanitize}" />
	</div>

	{include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
	{include file="tpl_backend/tpl_settings/ct-actions-js.tpl"}
	<script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
        <script type="text/javascript">var _base = "{$main_url}";</script>
{if $video_player eq "jw"}
        <script type="text/javascript">jwplayer.key="{$jw_license_key}";</script>
{elseif $video_player eq "flow"}
        <script type="text/javascript">{fetch file="f_scripts/shared/flowplayer/flowplayer5.js"}</script>
        <link rel="stylesheet" type="text/css" href="{$main_url}/f_scripts/shared/flowplayer/minimalist.css">
{/if}

        <script type="text/javascript">
        $(document).ready(function () {ldelim}
            $(".thumb-reset").click(function(){ldelim}
                var filekey = $(this).attr("rel");
                $("#file-entry-form"+filekey).mask(" ");
                $("#thumb-response").load(current_url + menu_section + "?do=thumb-reset&f="+filekey, function() {ldelim}
                    $("#file-entry-form"+filekey).unmask();
                {rdelim});
            {rdelim});
            $(".preview-reset").click(function(){ldelim}
                var filekey = $(this).attr("rel");
                $("#file-entry-form"+filekey).mask(" ");
                $("#thumb-response").load(current_url + menu_section + "?do=preview-reset&f="+filekey, function() {ldelim}
                    $("#file-entry-form"+filekey).unmask();
                {rdelim});
            {rdelim});

                $('.icheck-box input').each(function () {ldelim}
                        var self = $(this);
                        self.iCheck({ldelim}
                                checkboxClass: 'icheckbox_square-blue',
                                radioClass: 'iradio_square-blue',
                                increaseArea: '20%'
                                
                        {rdelim});
                {rdelim});
                $('.icheck-box input').on('ifChecked', function(event){ldelim} var _id = $(this).val(); $("#hcs-id" + _id).prop('checked', true); {rdelim});
                $('.icheck-box input').on('ifUnchecked', function(event){ldelim} var _id = $(this).val(); $("#hcs-id" + _id).prop('checked', false); {rdelim});

        	$("#entry-action-buttons .dl-trigger").on("click", function(){ldelim}
        		if ($("#entry-action-buttons ul.dl-menu").hasClass("dl-menuopen")) {ldelim}
        		setTimeout(function () {ldelim}
                                if ($("#file-time-actions ul.dl-menu").hasClass("dl-menuopen")) {ldelim}
                                        $("#file-time-actions .dl-trigger").click();
                                {rdelim}
                                if ($("#file-type-actions ul.dl-menu").hasClass("dl-menuopen")) {ldelim}
                                        $("#file-type-actions .dl-trigger").click();
                                {rdelim}
                                $("#choices-ipp_select").slideUp(300, function(){ldelim}$('#ct-wrapper').unbind('click', bodyHideSelect);{rdelim});
                        {rdelim}, 100);
                        {rdelim}
		{rdelim});
        	$("#file-type-actions .dl-trigger").on("click", function(){ldelim}
        		if ($("#file-type-actions ul.dl-menu").hasClass("dl-menuopen")) {ldelim}
        		setTimeout(function () {ldelim}
                                if ($("#file-time-actions ul.dl-menu").hasClass("dl-menuopen")) {ldelim}
                                        $("#file-time-actions .dl-trigger").click();
                                {rdelim}
                                if ($("#entry-action-buttons ul.dl-menu").hasClass("dl-menuopen")) {ldelim}
                                        $("#entry-action-buttons .dl-trigger").click();
                                {rdelim}
                                $("#choices-ipp_select").slideUp(300, function(){ldelim}$('#ct-wrapper').unbind('click', bodyHideSelect);{rdelim});
                        {rdelim}, 100);
                        {rdelim}
		{rdelim});
        	$("#file-time-actions .dl-trigger").on("click", function(){ldelim}
        		if ($("#file-time-actions ul.dl-menu").hasClass("dl-menuopen")) {ldelim}
        		setTimeout(function () {ldelim}
                                if ($("#file-type-actions ul.dl-menu").hasClass("dl-menuopen")) {ldelim}
                                        $("#file-type-actions .dl-trigger").click();
                                {rdelim}
                                if ($("#entry-action-buttons ul.dl-menu").hasClass("dl-menuopen")) {ldelim}
                                        $("#entry-action-buttons .dl-trigger").click();
                                {rdelim}
                                $("#choices-ipp_select").slideUp(300, function(){ldelim}$('#ct-wrapper').unbind('click', bodyHideSelect);{rdelim});
                        {rdelim}, 100);
                        {rdelim}
		{rdelim});

        {rdelim});
        </script>