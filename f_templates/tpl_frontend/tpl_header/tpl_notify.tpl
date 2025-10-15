	    {if $error_message ne ""}{assign var=notif_class value="error"}{assign var=the_message value=$error_message}{elseif $notice_message ne ""}{assign var=notif_class value="notice"}{assign var=the_message value=$notice_message}{/if}
	    <!-- NOTIFICATION CONTAINER -->
	    {if $error_message ne "" or $notice_message ne ""} 
		<div class="{$notif_class}-message" id="{$notif_class}-message" onclick="$(this).replaceWith(''); $('#cb-response').replaceWith(''); $('#cb-response-wrap').replaceWith(''); resizeDelimiter();">
		    <p class="{$notif_class}-message-text">{$the_message}</p>
		</div>
		<script type="text/javascript">$(document).ready(function(){ldelim}$(document).on("click","#{$notif_class}-message",function(){ldelim}$("#{$notif_class}-message").replaceWith("");$("#cb-response").replaceWith("");$("#cb-response-wrap").replaceWith("");resizeDelimiter();if($("#wrapper").hasClass("tpl_shorts")){ldelim}var sh=!$('#view-player').hasClass('bottom-comments')?$('.video-js').height():360;var ct=sh-($('#comment-load .file-views-nr').height()+$('#comment-load #comm-post-response').height()+$('#comment-load #comm-post-form').height()+20);$('.posted-comments').css('height',ct);{rdelim}{rdelim});{rdelim});</script>
	    {/if} <!-- END NOTIFICATION CONTAINER -->
