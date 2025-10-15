{if $error_message eq ""}
    <div class="container cbp-spmenu-push">
	<div id="upload-wrapper" class="left-float">
		<div class="pointer left-float no-top-padding wdmax section-bottom-border" id="cb-response-wrap" style="display: none;"><div class="centered"><div class="pointer left-float wdmax" id="cb-response">
                <div class="notice-message" id="notice-message" onclick="$(this).replaceWith(''); $('#cb-response').replaceWith(''); $('#cb-response-wrap').replaceWith('');">
                    <p class="notice-message-text">{lang_entry key="upload.text.public"}</p>
                </div>
                </div></div></div>

	    <form id="upload-form" method="post" action="{href_entry key="be_submit"}?t={$smarty.get.t|sanitize}">
		<div id="upload-wrapper" class="left-float wd960">
		    <div class="left-float wdmax">
    			<div id="uploader">
            		    <p class="all-paddings10">Loading Upload Panel...</p>
    			</div>
		    </div>
		</div>
		<div id="options-wrapper" class="left-float wd450 no-display">
		    <div class="plupload_header">
			<span class="left-padding5 left-float">{$file_category}</span>
			<span id="upload-category">{$upload_category}</span>
		    </div>
		</div>
		<input type="hidden" name="UFNAME" id="UFNAME" value="" />
		<input type="hidden" name="UFSIZE" id="UFSIZE" value="" />
		<input type="hidden" name="UFBE" id="UFBE" value="1" />
	    </form>
	</div>
    </div>
{/if}