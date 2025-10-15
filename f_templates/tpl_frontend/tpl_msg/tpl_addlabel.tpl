			    {if $custom_labels eq 1}<div id="custom-labels" class="d-flex"></div>{/if}
			    <div id="add-new-label" style="display: none; padding: 0px 0px 10px 0px;">
				<div id="add-new-label-in">
				    <form id="add-new-label-form" method="post" action="" class="entry-form-class">
					<label>{lang_entry key="label.add.new"}</label>
					<input type="text" name="add_new_label" id="add-new-label-input" class="login-input"><br>
					<div class="d-flex" style="margin-top: 10px;">
						<button name="add_new_label_btn" id="add-new-label-btn" class="my-0 me-10 save-entry-button button-grey search-button form-button" type="button" value="1"><span>{lang_entry key="frontend.global.create"}</span></button> 
						<a class="link cancel-trigger" href="#"><span>{lang_entry key="frontend.global.cancel"}</span></a>
					</div>
				    </form>
				</div>
			    </div>
			    <script type="text/javascript">
				$(document).ready(function() {ldelim}
				    var lb_url = current_url + menu_section + '?s={$smarty.get.s|sanitize}&do=label';
				    $(".new-label").click(function(){ldelim}$("#ct-contact-add-wrap").stop().slideUp('fast'); $("#add-new-label").stop().slideToggle('fast'); $("#add-new-label-input").focus(); if($(this).hasClass("form-button-active")){ldelim}$(this).removeClass("form-button-active");{rdelim}else{ldelim}$(this).addClass("form-button-active");{rdelim}{rdelim});
				    $(".link").click(function(){ldelim}$("#add-new-label").stop().slideUp('fast'); $(".new-label").removeClass("form-button-active"); {rdelim});
				    $("#add-new-label-btn").click(function(){ldelim}
				    	if($("#add-new-label-input").val() != '') {ldelim}
				    		$("#ct-wrapper").mask(" ");
				    		$.post(lb_url, $("#add-new-label-form").serialize(), function( data ) {ldelim}
				    			if (data > 0) {ldelim}
				    				location.reload();
				    			{rdelim}
				    		{rdelim});
				    	{rdelim}
				    {rdelim});
				    enterSubmit("#add-new-label-form input", "#add-new-label-btn");
				{rdelim});
			    </script>
