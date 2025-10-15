	<article>
		<h3 class="content-title"><i class="icon-envelope"></i>{lang_entry key="msg.btn.compose"}{if $smarty.get.r} - {lang_entry key="msg.title.reply"}{/if}</h3>
		<div class="line"></div>
	</article>
	<div class="left-float wdmax bottom-padding10 left-padding101">
	<div class="all-paddings10">
	    <form id="compose-msg-form" method="post" class="entry-form-class">
	    {if $smarty.get.r ne ""}
	    {insert name=getUsername assign=uname user_id=$smarty.post.section_reply_value|sanitize}
	    {insert name=getMessageSubject assign=msubj msg_id=$smarty.post.section_subject_value|sanitize}
	    {insert name=getMessageDate assign=mdate msg_id=$smarty.post.section_subject_value|sanitize}
	    {insert name=getMessageText assign=mtext msg_id=$smarty.post.section_subject_value|sanitize}
		<div class="left-float wdmax">
		{if $smarty.get.f eq ''}
		    <div class="row3">
			<label>{lang_entry key="msg.label.subj"}:</label>
			<span class="compose-right-reply">{$msubj}</span>
		    </div>
		{/if}
		    <div class="row3">
			<label>{lang_entry key="msg.label.date"}:</label>
			<span class="compose-right-reply">{$mdate|date_format:"M d, Y"}</span>
		    </div>
		    <div class="row3">
			<label>{if $smarty.get.f eq ''}{lang_entry key="msg.label.message"}{else}{lang_entry key="upage.text.mod.comment.single"}{/if}:</label>
			<span class="compose-right-reply">{$mtext}</span>
		    </div>
		    <div class="wdmax left-float bottom-border">&nbsp;</div>
		    <div class="left-float top-padding10">&nbsp;</div>
		    <div class="row3">
			<input type="hidden" name="section_reply_value" value="{$smarty.post.section_reply_value|sanitize}" />
			<input type="hidden" name="section_subject_value" value={$smarty.post.section_subject_value|sanitize}"" />
		    </div>
		</div>
	    {/if}
		<div class="row no-top-padding">
		    <label>{lang_entry key="msg.label.from"}</label>
		    <span class="compose-right-label">{$smarty.session.USER_DNAME}</span>
		</div>
		<div class="row">
		    <label>{lang_entry key="msg.label.to"}</label>
		    <span class="left-float"><input type="text"{if $smarty.get.f eq "comm"} readonly="readonly"{/if} name="msg_label_to" class="login-input" id="msg-label-to" value="{if $smarty.get.r eq 1}{$uname}{elseif $smarty.get.src eq 'upage'}{$smarty.session.channel_msg}{else}{$smarty.post.msg_label_to|sanitize}{/if}" /></span>
		</div>
		<div class="row"{if $smarty.get.f eq 'comm'} style="display: none;"{/if}>
		    <label>{lang_entry key="msg.label.subj"}</label>
		    <span class="left-float"><input type="text" name="msg_label_subj" class="login-input" value="{if $smarty.get.r eq 1}{lang_entry key="msg.label.reply"}{$msubj}{else}{$smarty.post.msg_label_subj|sanitize}{/if}" /></span>
		</div>
		<div class="row">
		    <label>{if $smarty.get.f eq ''}{lang_entry key="msg.label.message"}{else}{lang_entry key="upage.text.mod.comment.single"}{/if}</label>
		    <span class="left-float"><textarea name="{if $smarty.get.f eq "comm"}upage_text_mod_comment_single{else}msg_label_message{/if}" class="ta-input">{if $smarty.get.f eq "comm"}{$smarty.post.upage_text_mod_comment_single|sanitize}{else}{$smarty.post.msg_label_message|sanitize}{/if}</textarea></span>
		</div>
	    {if $message_attachments eq 1 and $smarty.get.f eq ''}
	    {if $live_module eq "1"}
		<div class="row">
		    <label>{lang_entry key="msg.label.attch.l"}</label>
		    <div class="compose-right-label selector">{insert name="fileListSelect" for="live"}</div>
		</div>
	    {/if}
            {if $video_module eq "1"}
		<div class="row">
		    <label>{lang_entry key="msg.label.attch.v"}</label>
		    <div class="compose-right-label selector">{insert name="fileListSelect" for="video"}</div>
		</div>
	    {/if}
	    {if $image_module eq "1"}
		<div class="row">
		    <label>{lang_entry key="msg.label.attch.i"}</label>
		    <div class="compose-right-label selector">{insert name="fileListSelect" for="image"}</div>
		</div>
	    {/if}
	    {if $audio_module eq "1"}
		<div class="row">
		    <label>{lang_entry key="msg.label.attch.a"}</label>
		    <div class="compose-right-label selector">{insert name="fileListSelect" for="audio"}</div>
		</div>
	    {/if}
	    {if $document_module eq "1"}
		<div class="row">
		    <label>{lang_entry key="msg.label.attch.d"}</label>
		    <div class="compose-right-label selector">{insert name="fileListSelect" for="doc"}</div>
		</div>
	    {/if}
	    {if $blog_module eq "1"}
		<div class="row">
		    <label>{lang_entry key="msg.label.attch.b"}</label>
		    <div class="compose-right-label selector">{insert name="fileListSelect" for="blog"}</div>
		</div>
	    {/if}
	    {/if}
	    	<div class="clearfix"></div>
		<div class="row d-flex">
			<button class="new-message-button save-entry-button button-grey search-button form-button me-10" name="msg_btn_send" id="msg-btn-send" type="button" value="1"><span>{lang_entry key="msg.btn.send"}</span></button>
			<a href="#" class="link cancel-trigger "id="msg-btn-cancel"><span>{lang_entry key="frontend.global.cancel"}</span></a>
		</div>
		{if $message_attachments eq "1"}
		<div class="row">
		    <span class="place-right">{lang_entry key="msg.label.private.tip"}</span>
		</div>
		{/if}
	    </form>
	    </div>
        </div>

        <script type="text/javascript">
    	$(document).ready(function() {ldelim}
    		$("#msg-label-to").keydown(function(){ldelim}
                $("#msg-label-to").autocomplete({ldelim}
                        type: "post",
                        serviceUrl: current_url + menu_section +"?s=" + $(".menu-panel-entry-active").attr("id") +"&do=autocomplete&m=0",
                        onSearchStart: function() {ldelim}{rdelim},
                        onSelect: function (suggestion) {ldelim}
                        {rdelim}
                {rdelim});
        {rdelim});

    	{if $message_attachments eq 1 and $smarty.get.f eq ''}
    		$(function() {ldelim}
                        {if $live_module eq "1"}SelectList.init("live_attachlist");{/if}
                        {if $video_module eq "1"}SelectList.init("video_attachlist");{/if}
                        {if $image_module eq "1"}SelectList.init("image_attachlist");{/if}
                        {if $audio_module eq "1"}SelectList.init("audio_attachlist");{/if}
                        {if $document_module eq "1"}SelectList.init("doc_attachlist");{/if}
                        {if $blog_module eq "1"}SelectList.init("blog_attachlist");{/if}
                {rdelim});
	{/if}

    	    function msg_gotoInbox(doNotice, mID) {ldelim}
    		wrapLoad(current_url+menu_section+"?s="+mID+"&notice="+doNotice, fe_mask);
    	    {rdelim}

    	    $("#msg-btn-send").click(function(){ldelim}
    		var post_url  = current_url+menu_section+"?do=compose{if $smarty.get.r ne ""}&r=2{/if}{if $smarty.get.f eq "comm"}&f=comm{/if}";
    		var post_form = $("#compose-msg-form");
    		postLoad(post_url, post_form, fe_mask);
    	    {rdelim});

    	    $("#msg-btn-cancel").click(function(){ldelim}
    		msg_gotoInbox(0, $("#categories-accordion .menu-panel-entry-active").attr("id"));
    	    {rdelim});
    	{rdelim});
        </script>