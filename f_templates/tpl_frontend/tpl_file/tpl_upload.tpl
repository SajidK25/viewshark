{if $usr_verified eq "0"}
    <div id="verify-response" class="row no-top-padding"></div>
    <div class="left-float wdmax top-padding10">
	<h3 class="greyed-out"> - {$usr_mail} -</h3>
	<h3 class="top-padding10 darker-out">{lang_entry key="upload.err.mail.ver1"}</h3>
	<div class="row">{lang_entry key="upload.err.mail.ver2"}</div>
	<br>
	<form id="verify-form" name="verify_form" method="post" action="" class="entry-form-class">
	    <div class="row top-padding20">
                <span class="left-float wd140 lh20">{lang_entry key="upload.text.ver.code"}</span>
                <span class="left-float"><input type="text" id="verification-captcha" name="verification_captcha" class="text-input" style="max-width:300px"/></span>
                <span class="left-float left-padding5"><img id="c-image" src="{$main_url}/{href_entry key="captcha"}?t={$c_rand}" alt="" /></span>
            </div>
            <div class="row no-top-padding">
                <span class="left-float wd140" style="width:120px;display:inline-block;">&nbsp;</span>
                <span class=""><button type="button" class="save-entry-button button-grey search-button form-button resend-verification" name="send_verification">{lang_entry key="upload.text.ver.send"}</button></span>
            </div>
	    <div class="row no-display"><input type="hidden" name="verify_email" value="{$usr_mail}" /></div>
	</form>
    </div>
    <script type="text/javascript">
	$(document).ready(function(){ldelim}
	    $(".resend-verification").click(function(){ldelim}
		$(".col1").mask(" ");
                $.post("{$main_url}/{href_entry key="upload"}{if $smarty.get.t eq ""}?do=reverify{else}?t={$smarty.get.t|sanitize}&do=reverify{/if}", $("#verify-form").serialize(), function(data){ldelim}
                    $("#verify-response").html(data);
                    $(".col1").unmask();
                {rdelim});
	    {rdelim});
	{rdelim});
    </script>
{else}
    {if $perm_err eq "" and $smarty.session.USER_KEY ne ""}
    {if $smarty.get.r ne ""}{insert name="uploadResponse"}{/if}
    <div class="sp-container">
	<div class="tabs tabs-style-line no-display">
		<nav>
			<ul>
                                {if $video_module eq 1 and $video_uploads eq 1}<li{if $smarty.get.t eq "video"} class="tab-current"{/if}><a class="icon icon-video" href="{$main_url}/{href_entry key="upload"}?t=video"><span>{lang_entry key="upload.menu.video"}</span></a></li>{/if}
                                {if $video_module eq 1 and $video_uploads eq 1}<li{if $smarty.get.t eq "short"} class="tab-current"{/if}><a class="icon icon-video" href="{$main_url}/{href_entry key="upload"}?t=short"><span>{lang_entry key="upload.menu.video"}</span></a></li>{/if}
				{if $import_yt eq 1 or $import_dm eq 1 or $import_vi eq 1}<li{if $page_display eq "tpl_import" and $smarty.get.t eq "video"} class="tab-current"{/if}><a class="icon icon-video" href="{$main_url}/{href_entry key="import"}?t=video"><span>{lang_entry key="upload.menu.grab"}</span></a></li>{/if}
				{if $image_module eq 1 and $image_uploads eq 1}<li{if $smarty.get.t eq "image"} class="tab-current"{/if}><a class="icon icon-image" href="{$main_url}/{href_entry key="upload"}?t=image"><span>{lang_entry key="upload.menu.image"}</span></a></li>{/if}
				{if $audio_module eq 1 and $audio_uploads eq 1}<li{if $smarty.get.t eq "audio"} class="tab-current"{/if}><a class="icon icon-headphones" href="{$main_url}/{href_entry key="upload"}?t=audio"><span>{lang_entry key="upload.menu.audio"}</span></a></li>{/if}
				{if $document_module eq 1 and $document_uploads eq 1}<li{if $smarty.get.t eq "document"} class="tab-current"{/if}><a class="icon icon-file" href="{$main_url}/{href_entry key="upload"}?t=document"><span>{lang_entry key="upload.menu.document"}</span></a></li>{/if}
			</ul>
		</nav>
	</div>
	{if $error_message ne ""}
                <div class="error-message" id="error-message" onclick="$(this).replaceWith(''); $('#cb-response').replaceWith(''); $('#cb-response-wrap').replaceWith('');">
                    <p class="error-message-text">{$error_message}</p>
                </div>
                <div class="clearfix"></div>
	{else}
	<div class="clearfix"></div>
	<div id="upload-wrapper" class="left-float">
		<div class="pointer left-float no-top-padding wdmax section-bottom-border" id="cb-response-wrap" style="display: none;"><div class="centered"><div class="pointer left-float wdmax" id="cb-response">
                <div class="notice-message" id="notice-message" onclick="$(this).replaceWith(''); $('#cb-response').replaceWith(''); $('#cb-response-wrap').replaceWith('');">
                    <p class="notice-message-text">{lang_entry key="upload.text.public"}</p>
                </div>
                </div></div></div>

            <form id="upload-form" method="post" action="{href_entry key="be_submit"}?t={$smarty.get.t|sanitize}{if $smarty.get.r ne ""}&r={$smarty.get.r|sanitize}{/if}">
                <div id="upload-wrapper" class="left-float wd960">
                    <div class="left-float wdmax">
                    	{if $smarty.get.t eq "short"}
                    	<p class="info-message-text mb-0">{lang_entry key="upload.text.info.shorts"}</p>
                    	{/if}
                        <div id="uploader" class="frontend">
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
		<input type="hidden" name="UFSUID" value="{$smarty.session.USER_KEY}" />
		<input type="hidden" name="UFUID" value="{$smarty.session.USER_ID}" />
		<input type="hidden" name="UFNAME" id="UFNAME" value="" />
		<input type="hidden" name="UFSIZE" id="UFSIZE" value="" />
            </form>
        </div>

		{if $paid_memberships eq "1"}
		<div class="">
		    <div class="" id="fsUploadStats" style="display: none;">
		    	<article>
                        	<h3 class="content-title">
                        		<i class="icon-user"></i>{lang_entry key="upload.text.mem.limit"}
                        		
                        	</h3>
                        	<div class="line"></div>
                	</article>
                	<div id="the-stats" style="display: block;">
		    		{$the_stats}
		    	</div>
		    </div>
		    <div class="no-display">
			<input type="hidden" id="total-uploads" name="total_uploads" value="0" />
			<input type="hidden" id="type-uploads" name="type_uploads" value="{$smarty.get.t[0]|sanitize}" />
			<span id="replace-uploads">{lang_entry key="upload.err.msg.9.1"}</span>
		    </div>
		</div>
		{/if}
	{/if}
    </div>
    {elseif $error_message ne ""}
        {$error_message}
    {/if}
{/if}
