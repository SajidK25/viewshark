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
            
    	    {generate_html type="file_manager_blog" bullet_id="ct-bullet1" entry_id="ct-entry-details1" bb=1}
	</div>

	{include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
	{include file="tpl_backend/tpl_settings/ct-actions-js.tpl"}
	<script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
        <script type="text/javascript" src="{$scripts_url}/shared/tinymce/tinymce.min.js"></script>
<!--
        <script type="text/javascript" src="{$scripts_url}/shared/tinymce/tinymce.min.js"></script>
        <script type="text/javascript">var _u = current_url + menu_section + '?be=1';{if $video_module eq "1"}var vm=1;{/if}{if $image_module eq "1"}var im=1;{/if}{if $audio_module eq "1"}var am=1;{/if}{if $document_module eq "1"}var dm=1;{/if}</script>
        <script type="text/javascript" src="{$javascript_url}/tinymce.init.js"></script>
-->
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
