	<div class="clearfix"></div>
    <div class="" id="ct-contact-add">
	<div class="">
	<form id="add-new-contact-form" method="post" action="" class="entry-form-class">
	    <label>{lang_entry key="frontend.global.name"}</label>
	    <input type="text" name="frontend_global_name" class="login-input" value="{$smarty.post.frontend_global_name|sanitize}" />
	    <label>{lang_entry key="frontend.global.username"}</label>
	    <input type="text" name="frontend_global_username" class="login-input" value="{$smarty.post.frontend_global_username|sanitize}" />
	    <label>{lang_entry key="frontend.global.email"}</label>
	    <input type="text" name="frontend_global_email" class="login-input" value="{$smarty.post.frontend_global_email|sanitize}" />
	    <label>{lang_entry key="contacts.add.frstatus"}</label>
	    <div class="grayText">{lang_entry key="contacts.add.frstatus.tip"}</div>
	    <label>{lang_entry key="contacts.add.chan"}</label>
	    <div class="grayText">{lang_entry key="contacts.add.chan.tip"}</div>
    {if $custom_labels eq 1}
	{insert name="userLabelCheckboxes" assign="userLabels"}
	{if $userLabels ne ""}
	    <label>{lang_entry key="contacts.add.labels"}</label>
	    <div class="top-padding5">{$userLabels}</div>
	    <div class="row ct-spacer"></div>
	{/if}
    {/if}
    <br>
    		<div class="clearfix"></div>
	    <div class="mb-10 d-flex">
	    	<button name="save_contact_btn" id="save-contact-btn" class="search-button form-button button-grey save-entry-button me-10" type="button" value="1"><span>{lang_entry key="frontend.global.savenew"}</span></button>
	    	<a href="#" class="link cancel-trigger" id="contact-add-cancel"><span>{lang_entry key="frontend.global.cancel"}</span></a>
	    </div>
	</form>
	</div>
    </div>
    <script type="text/javascript">
    $(document).ready(function() {ldelim}
    {if $smarty.post and ($smarty.get.do eq "contact" or $smarty.get.do eq "ctedit")}
    	$('.icheck-box input').each(function () {ldelim}
                 var self = $(this);
                 self.iCheck({ldelim}
                         checkboxClass: 'icheckbox_square-blue',
                         radioClass: 'iradio_square-blue',
                         increaseArea: '20%'
                         //insert: '<div class="icheck_line-icon"></div><label>' + label_text + '</label>'
                 {rdelim});
         {rdelim});
         $('.icheck-box input.list-check, .icheck-box.ct input').on('ifChecked', function(event){ldelim} var _id = $(this).val(); $("#hcs-id" + _id).prop('checked', true); {rdelim});
         $('.icheck-box input.list-check, .icheck-box.ct input').on('ifUnchecked', function(event){ldelim} var _id = $(this).val(); $("#hcs-id" + _id).prop('checked', false); {rdelim});
         $(".icheck-box.ct input").on("ifChecked", function(event){ldelim} i = $(this).parent().parent().parent().attr("id"); $("#"+i+" .ct-entry-name").click();{rdelim});
         $(".icheck-box.ct input").on("ifUnchecked", function(event){ldelim} i = $(this).parent().parent().parent().attr("id"); $("#"+i+" .ct-entry-check").click(); {rdelim});
         $("#check-all").on("ifChecked", function(event){ldelim} $('.icheck-box.ct input').iCheck('check'); {rdelim});
         $("#check-all").on("ifUnchecked", function(event){ldelim} $('.icheck-box.ct input').iCheck('uncheck'); {rdelim});
         $(".efc").click(function(){ var this_val = $(this).val(); if ($(this).is(":checked")) { $("#labelCheckboxes"+this_val+" input[class=dfc]").prop("checked", false); } else { $("#labelCheckboxes"+this_val+" input[class=dfc]").prop("checked", true); } });
         $(".efc").on("ifUnchecked", function(event){ var this_val = $(this).val(); $("#labelCheckboxes"+this_val+" input[class=dfc]").prop("checked", true); });
         $(".efc").on("ifChecked", function(event){ var this_val = $(this).val(); $("#labelCheckboxes"+this_val+" input[class=dfc]").prop("checked", false); });
    {/if}
	$("#save-contact-btn").bind("click", function(){ldelim}
	    $("#ct-contact-add-wrap").mask(" ");
            $.post(current_url + menu_section + '?s={$smarty.get.s|sanitize}&do=contact', $("#add-new-contact-form").serialize(), function(data) {ldelim}
        	$("#ct-contact-add-wrap").html(data);
                $("#ct-contact-add-wrap").unmask();
                resizeDelimiter();
            {rdelim});
	{rdelim});
	$("#contact-add-cancel").bind("click", function(){ldelim}
		$("#ct-contact-add-wrap").stop().slideUp();
		$("#error-message").click();$("#notice-message").click();
		resizeDelimiter();
	{rdelim});
    {rdelim});
    </script>