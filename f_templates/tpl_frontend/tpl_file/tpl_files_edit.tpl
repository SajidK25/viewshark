	{assign var=c_section value="{href_entry key="files"}"}{insert name="currentMenuEntry" assign=menu_entry for=$smarty.get.s}{insert name="fileCount" for="file-menu-entry1" assign="count1"}{insert name="fileCount" for="file-menu-entry2" assign="count2"}{insert name="fileCount" for="file-menu-entry3" assign="count3"}{insert name="fileCount" for="file-menu-entry4" assign="count4"}{insert name="fileCount" for="file-menu-entry5" assign="count5"}
	<script type="text/javascript">
            var current_url  = '{$main_url}/';
            var menu_section = '{$c_section}';
            var fe_mask      = 'on';
        </script>

        <div id="edit-wrapper">
            {generate_html type="files_edit_layout" bullet_id="ct-bullet1" entry_id="ct-entry-details1" section="files" bb="1"}
        </div>
	<script type="text/javascript">
        $(document).ready(function () {ldelim}
        	$(".save-entry-button").click(function() {ldelim}
        		var t = $(this);
        		var form = $(".entry-form-class").serialize();
        		var upd_url = window.location.href;
        		
        		if (typeof($("#blog-edit").html()) != "undefined") {ldelim}
        			form += "&blog_html="+encodeURIComponent($("#blog-edit").html());
        		{rdelim}
        		
        		$(".content-current").mask(" ");
        		t.removeClass("new-entry").addClass("new-entry-loading");
        		
        		$.post(upd_url, form, function(data) {ldelim}
        			$(".content-current").unmask();
        			t.addClass("new-entry").removeClass("new-entry-loading");
        			$("#submit-response").html(data).find("#cb-response").css("padding-top", "10px");
        		{rdelim});
        	{rdelim});
        	
                $('.icheck-box input').each(function () {ldelim}
                        var self = $(this);
                        self.iCheck({ldelim}
                                checkboxClass: 'icheckbox_square-blue',
                                radioClass: 'iradio_square-blue',
                                increaseArea: '20%'
                                //insert: '<div class="icheck_line-icon"></div><label>' + label_text + '</label>'
                        {rdelim});
                {rdelim});
//                $('.icheck-box input').on('ifChecked', function(event){ldelim} var _id = $(this).val(); $("#hcs-id" + _id).prop('checked', true); {rdelim});
//                $('.icheck-box input').on('ifUnchecked', function(event){ldelim} var _id = $(this).val(); $("#hcs-id" + _id).prop('checked', false); {rdelim});
        {rdelim});
	</script>
