	<link rel="stylesheet" type="text/css" href="{$scripts_url}/shared/codemirror/lib/codemirror.css">
	<div class="wdmax" id="ct-wrapper">
	<form id="ct-set-form" action="">
            <div class="wdmax left-float top-bottom-padding s-wrap">
                <div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
                <div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
            </div>
            <div class="clearfix"></div>

            <div class="vs-mask">{generate_html bullet_id="ct-bullet1" input_type="email_templates" entry_title="backend.menu.entry2.sub9.mail.tpl" entry_id="ct-entry-details1" input_name="" input_value="" bb=1}</div>
            <div class="vs-mask">{generate_html bullet_id="ct-bullet2" input_type="footer_templates" entry_title="backend.menu.entry2.sub9.footer.tpl" entry_id="ct-entry-details2" input_name="" input_value="" bb=0}</div>

            <div class="clearfix"></div>
	        <div class="wdmax top-bottom-padding left-float s-wrap">
                <div class="sortings left-half">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
                <div class="page-actions">{include file="tpl_backend/tpl_settings/ct-keep-open.tpl"}</div>
            </div>
            <div class="popupbox-mem" id="popuprel-cb"></div><div id="fade-cb" class="fade"></div>
            <input type="hidden" name="file_entry" id="file_entry" value="" />
            <input type="hidden" name="file_tpl" id="file_tpl" value="" />
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
        $(document).on("click", ".tpl-save", function() {ldelim}
    	    $(".fancybox-inner").mask("");
    	    myCodeMirror.save();
    	    
    	    $.post(current_url + menu_section + "?s={$smarty.get.s|sanitize}&do=tpl-save", $("#ct-set-form").serialize()+'&'+$.param({ 'tpl_page_code': $("#tpl-page-code").val() }), function(data){ldelim}
    		$("#tpl-save-update").html(data);
    		$(".fancybox-inner").unmask();
    	    {rdelim});
        {rdelim});
        $("a.popup").click(function(){ldelim}
    	    var popupid = "popuprel-cb";
    	    var fid = "-cb";
    	    var filekey = $(this).attr("id");
    	    var etype = $(this).attr("rel-type");

	    $("#file_entry").val(filekey);
	    $("#file_tpl").val(etype);

		$.fancybox.open({ldelim}type: 'ajax', minWidth: "75%", margin: 10, href: current_url + menu_section + "?s={$smarty.get.s|sanitize}&do="+etype+"&p="+filekey {rdelim});
    	{rdelim});
    	$(document).keyup(function(e){ldelim}
    	    if(e.keyCode == 27){ldelim}$(".popup-cancel").click();{rdelim}
    	{rdelim});
        {rdelim});
        </script>
