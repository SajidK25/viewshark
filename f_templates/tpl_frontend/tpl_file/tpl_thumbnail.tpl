		    <form id="fedit-image-form" class="entry-form-class" method="post" action="" enctype="multipart/form-data">
			<div id="intabdiv">
				<article>
					<h3 class="content-title"><i class="icon-{if $smarty.get.v ne ""}video{elseif $smarty.get.i ne ""}image{elseif $smarty.get.a ne ""}audio{elseif $smarty.get.d ne ""}file{elseif $smarty.get.b ne ""}pencil2{/if}"></i>{lang_entry key="files.text.edit.thumb"}</h3>
					<div class="line"></div>
				</article>

			    <div class="left-float left-align wdmax row" id="thumb-change-response"></div>
			    <div class="left-float left-align wdmax">
				<div class="row">
				    <div class="icheck-box up"><input type="radio" name="fedit_image_action" id="fedit-image-upload" value="new" /><label>{lang_entry key="files.text.edit.thumb.text"}</label></div>
				</div>
				<div class="row" id="overview-userinfo-file">
					<div class="left-float left-padding25 hiddenfile">
					<input type="file" name="fedit_image" id="fedit-image" size="30" onchange="$('#fedit-image-form').submit();" />
				</div>
				<br>
				<center>
				<button class="save-entry-button button-grey search-button form-button new-image" type="button" onclick="$('.icheck-box.up input').iCheck('check');$('#fedit-image').trigger('click');"><span>{lang_entry key="frontend.global.upload.image"}</span></button>
				<br><br>
				<label>{lang_entry key="files.text.thumb.sizes"}</label>
				</center>
				<br><br>
			    </div>
			    {if $src eq "local"}
			    <div class="row">
				<div class="icheck-box"><input type="radio" name="fedit_image_action" id="fedit-image-default" value="default" /><label>{if $smarty.get.v eq ""}{lang_entry key="account.image.upload.default"}{else}{lang_entry key="account.image.upload.grab"}{/if}</label></div>
			    </div>
			    <br>
			    {/if}
			    <div class="row" id="save-button-row">
				<button name="save_changes_btn" id="save-image-btn" class="save-entry-button button-grey search-button form-button" type="button" value="1"><span>{lang_entry key="files.text.save.thumb"}</span></button>
				<a class="link cancel-trigger" href="#"><span>{lang_entry key="frontend.global.cancel"}</span></a>
			    </div>
        		</div>
        	    </form>
        	    {include file="tpl_frontend/tpl_file/tpl_thumbnail_js.tpl"}

