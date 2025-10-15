	<link rel="stylesheet" type="text/css" href="{$scripts_url}/shared/codemirror/lib/codemirror.css">
	<div class="wdmax entry-list" id="ct-wrapper">
            <div class="section-top-bar{if $smarty.get.do eq "add"}-add{else} vs-maskx{/if} left-float top-bottom-padding">
                <div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}{include file="tpl_backend/tpl_settings/ct-cancel-top.tpl"}</div>
                <div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
                <div class="clearfix"></div>
            </div>
            <div class="clearfix"></div>
            
            <div class="vs-mask">{generate_html type="lang_types" bullet_id="ct-bullet1" entry_id="ct-entry-details1" bb=1}</div>
            
            {if $smarty.get.do eq "add"}
            <div class="clearfix"></div>
            <div class="section-bottom-bar{if $smarty.get.do eq "add"}-add{else} vs-maskx{/if} left-float top-bottom-padding">
                <div class="clearfix"></div>
                {include file="tpl_backend/tpl_settings/ct-save-top.tpl"}{include file="tpl_backend/tpl_settings/ct-cancel-top.tpl"}
            </div>
            {/if}
	    <form id="lang-set-form" action="" method="post">
		<div class="popupbox-mem" id="popuprel-cb"></div><div id="fade-cb" class="fade"></div>
        	<input type="hidden" name="file_tpl" id="file_tpl" value="" />
        	<input type="hidden" name="file_entry" id="file_entry" value="" />
        	<input type="hidden" name="lang_id" id="file_id" value="" />
    	    </form>
        </div>

        {include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
        {include file="tpl_backend/tpl_settings/ct-actions-js.tpl"}
        <script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
        <script type="text/javascript">{fetch file="f_scripts/shared/codemirror/lib/codemirror.min.js"}</script>
        <script type="text/javascript">{fetch file="f_scripts/shared/codemirror/mode/xml/xml.js"}</script>

        <script type="text/javascript">
        var main_url = "{$main_url}";
        $(document).ready(function(){ldelim}
        $(document).unbind().on("click", ".tpl-save", function() {ldelim}
            $(".fancybox-inner").mask("");
            myCodeMirror.save();

            $.post(current_url + menu_section + "?s={$smarty.get.s|sanitize}&do=tpl-save", $("#lang-set-form").serialize()+'&'+$.param({ 'tpl_page_code': $("#tpl-page-code").val() }), function(data){ldelim}
                $("#tpl-save-update").html(data);
                $(".fancybox-inner").unmask();
            {rdelim});
        {rdelim});
        $("a.popup").click(function(){ldelim}
            var popupid = "popuprel-cb";
            var fid = "-cb";
            var filekey = $(this).attr("id");
            var etype = $(this).attr("rel-type");
            var langid = $(this).attr("rel-id");

            $("#file_entry").val(filekey);
            $("#file_tpl").val("lang-"+etype);
            $("#file_id").val(langid);

            thisresizeDelimiter();
            $.fancybox.open({ldelim}type: 'ajax', minWidth: "75%", margin: 10, href: current_url + menu_section + "?s={$smarty.get.s|sanitize}&do=lang-"+etype+"&f="+langid+"&p="+filekey {rdelim});
        {rdelim});
        {rdelim});
        </script>
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
                                    $("#choices-ipp_select").slideUp(300, function(){ldelim}$('#ct-wrapper').unbind('click', bodyHideSelect);{rdelim});
                            {rdelim}, 100);
                        {rdelim}
		{rdelim});
		
		
		
           $(document).on({ldelim}click: function () {ldelim}
                var _id = "";
                if ($(".fancybox-wrap").width() > 0) {ldelim}
                        _id = ".fancybox-inner ";
                {rdelim}

                $(_id + ".responsive-accordion div.responsive-accordion-head").addClass("active");
                $(_id + ".responsive-accordion div.responsive-accordion-panel").addClass("active").show();
                $(_id + ".responsive-accordion i.responsive-accordion-plus").hide();
                $(_id + ".responsive-accordion i.responsive-accordion-minus").show();
                thisresizeDelimiter();
            {rdelim}{rdelim}, "#all-open");

            $(document).on({ldelim}click: function () {ldelim}
                var _id = "";
                if ($(".fancybox-wrap").width() > 0) {ldelim}
                        _id = ".fancybox-inner ";
                {rdelim}

                $(_id + ".responsive-accordion div.responsive-accordion-head").removeClass("active");
                $(_id + ".responsive-accordion div.responsive-accordion-panel").removeClass("active").hide();
                $(_id + ".responsive-accordion i.responsive-accordion-plus").show();
                $(_id + ".responsive-accordion i.responsive-accordion-minus").hide();
                $("#ct_entry").val("");
                thisresizeDelimiter();
            {rdelim}{rdelim}, "#all-close");



        {rdelim});
        </script>