		    <form id="profile-image-form" class="entry-form-class" method="post" action="" enctype="multipart/form-data">
		    	<article>
		    		<h3 class="content-title"><i class="icon-user"></i> {lang_entry key="account.image.change"}</h3>
		    		<div class="line"></div>
		    	</article>

			<div id="intabdiv">
			    <div class="left-float left-align wdmax row" id="overview-userinfo-response"></div>
			    <div class="left-float left-align wdmax">
				<div class="row">
				    <div class="icheck-box up"><input type="radio" name="profile_image_action" id="profile-image-upload" value="new" /><label>{lang_entry key="account.image.upload"}</label></div>
				</div>
				<div class="row" id="overview-userinfo-file">
					<div class="left-float left-padding25 hiddenfile">
						<input type="file" name="profile_image" id="profile-image" size="30" onchange="$('#profile-image-form').submit();" />
					</div>
					<center>
						<button onclick="$('.icheck-box.up input').iCheck('check');$('#profile-image').trigger('click');" class="save-entry-button button-grey search-button form-button new-image" type="button"><span>{lang_entry key="frontend.global.upload.image"}</span></button>
					</center>
					<br>
				</div>
			    </div>
			    <div class="row">
				<div class="icheck-box"><input type="radio" name="profile_image_action" id="profile-image-default" value="default" /><label>{lang_entry key="account.image.upload.default"}</label></div>
			    </div>
			    <div class="row">
				<div class="row" id="save-button-row">
                                	<button name="save_changes_btn" id="save-image-btn" class="save-entry-button button-grey search-button form-button" type="button" value="1"><span>{lang_entry key="frontend.global.savechanges"}</span></button>
                                	<a class="link cancel-trigger" href="#"><span>{lang_entry key="frontend.global.cancel"}</span></a>
                            	</div>
			    </div>
        		</div>
        	    </form>


		<script type="text/javascript">
		    $(document).ready(function() {ldelim}
        		var options = {ldelim}
            		    target: "#overview-userinfo-response",
            		    beforeSubmit: showRequest,
            		    success: showResponse,
            		    url: current_url + menu_section + "?s=account-menu-entry1&do=upload"
        		{rdelim}

        		function showRequest() {ldelim} $('#intabdiv').mask(' '); $("#save-button-row").addClass("hidden"); {rdelim}
        		function showResponse() {ldelim} $('#intabdiv').unmask(); $("#save-button-row").removeClass("hidden"); $(".fancybox-inner").css("height", "auto"); $(".fancybox-opened").css("top", "25%"); {rdelim}

        		$(document).on("submit", "#profile-image-form", function() {ldelim}
            		    $(this).ajaxSubmit(options);
            		    return false;
        		{rdelim});

        		$("#save-image-btn").click(function() {ldelim}
        		    $("#intabdiv").mask(" ");
        		    $.post(current_url + menu_section + '?s=account-menu-entry1&do=save', $("#profile-image-form").serialize(), function(data) {
        			$("#overview-userinfo-response").html(data);
        			$("#intabdiv").unmask();
        		    });
        		{rdelim});

        		$("#profile-image").click(function() {ldelim}
        		    $("#profile-image-upload").prop("checked", true);
        		{rdelim});

			$(document).one("click", ".link", function() {ldelim}
    			    $("#profile-image").replaceWith('<input type="file" name="profile_image" id="profile-image" size="30" />');
    			    $("#intabdiv").mask(" ");
    			    $.post(current_url + menu_section + '?s=account-menu-entry1&do=cancel', $("#profile-image-form").serialize(), function(data) {
    				$("#intabdiv").unmask();
    				$.fancybox.close(true);
    				$(".fancybox-overlay.fancybox-overlay-fixed").hide().detach();
			    });

//    			    $("#fade , #popuprel").hide();
    			    return false;
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

		    {rdelim});
		    $(document).keyup(function(e){ldelim}
			if (e.keyCode == 27) { $(".link").click(); }
		    {rdelim});
		</script>
