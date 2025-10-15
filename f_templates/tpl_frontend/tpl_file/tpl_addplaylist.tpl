			    <div id="add-new-label">
				<div id="add-new-label-in" class="lb-margins">
				    <form id="add-new-label-form" method="post" action="" class="entry-form-class">
				    <article>
				    	<h3 class="content-title"><i class="icon-list"></i>{lang_entry key="files.action.new"}</h3>
				    	<div class="line"></div>
				    </article>
				    <div id="add-new-label-response" class=""></div>

				    {if $smarty.get.s eq "file-menu-entry6"}
					<div class="selector">
					    <label>{lang_entry key="files.action.new.type"} {lang_entry key="frontend.global.required"}</label>
					    <select name="add_new_type" class="select-input">
					    {if $live_module eq 1}<option value="live"{if $smarty.get.t eq "live"} selected{/if}>{lang_entry key="frontend.global.l"}</option>{/if}
                                            {if $video_module eq 1}<option value="video"{if $smarty.get.t eq "" or $smarty.get.t eq "video"} selected{/if}>{lang_entry key="frontend.global.v"}</option>{/if}
                                            {if $short_module eq 1}<option value="short"{if $smarty.get.t eq "short"} selected{/if}>{lang_entry key="frontend.global.s"}</option>{/if}
					    {if $image_module eq 1}<option value="image"{if $smarty.get.t eq "image"} selected{/if}>{lang_entry key="frontend.global.i"}</option>{/if}
					    {if $audio_module eq 1}<option value="audio"{if $smarty.get.t eq "audio"} selected{/if}>{lang_entry key="frontend.global.a"}</option>{/if}
					    {if $document_module eq 1}<option value="doc"{if $smarty.get.t eq "doc"} selected{/if}>{lang_entry key="frontend.global.d"}</option>{/if}
					    {if $blog_module eq 1}<option value="blog"{if $smarty.get.t eq "blog"} selected{/if}>{lang_entry key="frontend.global.b"}</option>{/if}
					    </select>
					</div>
				    {/if}
					<label>{lang_entry key="files.action.new.title"} {lang_entry key="frontend.global.required"}</label>
					<input type="text" name="add_new_title" id="add-new-title-input" class="login-input wd300" />
					<label>{lang_entry key="files.action.new.descr"}</label>
					<textarea name="add_new_descr" id="add-new-descr-input" rows="1" cols="1" class="ta-input wd300"></textarea>
					<label>{lang_entry key="files.action.new.tags"} {lang_entry key="frontend.global.required"}</label>
					<input type="text" name="add_new_tags" id="add-new-tags-input" class="login-input wd300" />
					<div class="row" id="save-button-row">
						<button name="add_new_pl_btn" id="add-new-pl-btn" class="save-entry-button button-grey search-button form-button" type="button" value="1"><span>{lang_entry key="files.action.new.create"}</span></button>
                                		<a class="link cancel-trigger" href="#"><span>{lang_entry key="frontend.global.cancel"}</span></a>
                            		</div>
                            		<label id="nl">{lang_entry key="frontend.global.required.items"}</label>
				    </form>
				</div>
			    </div>
			    <script type="text/javascript">
			    	$(function() {ldelim}
			    		SelectList.init("add_new_type");
			    	{rdelim});

				$(document).ready(function() {ldelim}
				    //var lb_url = current_url + menu_section + '?s={$smarty.get.s}&m=1&for=sort-'+$(".view-mode-type.active").attr("id").replace("view-mode-", "")+'&a=pl-add';
				    //var lb_url = current_url + menu_section + '?s={$smarty.get.s}&m=1&a=pl-add';
				    //var lb_url = current_url + menu_section + '?s='+$(".menu-panel-entry-active").attr("id")+'&m=1&a=pl-add';
				    //var lb_url = current_url + menu_section + '?s=file-menu-entry6&m=1&a=pl-add';

				    //$(".new-label").click(function(){ldelim} simpleReverseDiv("add-new-label"); $("#add-new-title-input").focus(); if($(this).hasClass("form-button-active")){ldelim}$(this).removeClass("form-button-active");{rdelim}else{ldelim}$(this).addClass("form-button-active");{rdelim} {rdelim});
				    
				    $(".link").click(function(){ldelim} $.fancybox.close(); {rdelim});

				    enterSubmit("#add-new-label-form input", "#add-new-pl-btn");
				{rdelim});
			    </script>
