	{if $page_display eq "tpl_messages"} {assign var=c_section value="{href_entry key="messages"}"} {/if}
	{insert name="currentMenuEntry" assign=menu_entry for=$smarty.get.s|sanitize}
	<script type="text/javascript">
            var current_url  = '{$main_url}/';
            var menu_section = '{$c_section}';
            var fe_mask      = 'on';
            function thisresizeDelimiter(){ldelim}{rdelim}
	</script>
	<style>
	#add-new-entry{ldelim}display:flex;flex-wrap:wrap;min-height:55px{rdelim}
	#add-new-entry>button{ldelim}margin-bottom:15px;margin-top:15px;{rdelim}
	#compose-button, #new-label, #custom-labels{ldelim}flex-basis:auto{rdelim}
	#add-new-label{ldelim}flex-basis:100%;order:4{rdelim}
	#add-new-label-input{ldelim}margin-bottom:0{rdelim}
	.tabs nav a span{ldelim}vertical-align:middle{rdelim}
	#custom-labels{ldelim}order:3;align-self:center{rdelim}
	#custom-labels ul{ldelim}display:flex;align-self:center{rdelim}
	#custom-labels>ul>li>form{ldelim}margin-right:10px{rdelim}
	#custom-labels .icon-tag{ldelim}font-size:12px;margin-right:3px{rdelim}
	#custom-labels .label-del i{ldelim}vertical-align:middle{rdelim}
	#custom-labels #label-form-message-menu-entry7-entry-friends, #custom-labels #label-form-message-menu-entry7-entry-blocked{ldelim}display:none{rdelim}
	#rename-label-form{ldelim}width:auto;display:flex{rdelim}
	#current-label-name{ldelim}margin-top:15px;margin-bottom:0;padding:10px !important{rdelim}
	#label-rename-wrap{ldelim}margin-right:0px{rdelim}
	@media(max-width:480px){ldelim}
	#add-new-label{ldelim}order:3{rdelim}#custom-labels{ldelim}order:4;margin-bottom:8px{rdelim}
	#rename-label-form{ldelim}width:100%;display:inline-block{rdelim}
	#current-label-name{ldelim}margin-top:0;margin-bottom:7px;padding:15px !important{rdelim}
	{rdelim}
	</style>
{if $smarty.get.s eq ""}
<div style="width:100%;height:100px;display:inline-block">&nbsp;</div>
{else}
        <div id="ct-wrapper" class="entry-list tpl-messages">
        	<article class="no-display">
        		<h3 class="content-title"><i class="{$section_icon}"></i>{$section_title}</h3>
        		<div class="line mb-0"></div>
        	</article>
        	<div class="swiper-ph swiper-ph-tnav"></div>
        	<div class="swiper-top swiper-top-tnav" style="display:{$ssd}">
        		<div class="swiper-button-prev swiper-button-prev-tnav"{$sso}></div>
        		<div class="swiper-button-next swiper-button-next-tnav"{$sso}></div>
        		<div class="swiper swiper-tnav">
        			<div class="swiper-wrapper">
        			{if $internal_messaging eq 1}
        				<div class="swiper-slide{if $smarty.get.s eq "message-menu-entry2" or $smarty.get.s eq ""} swiper-slide-current{/if}"><a href="" rel-s="#message-menu-entry2" rel="nofollow"><span><i class="icon-envelope"></i> {lang_entry key="msg.entry.inbox"}</span></a></div>
        				<div class="swiper-slide{if $smarty.get.s eq "message-menu-entry5"} swiper-slide-current{/if}"><a href="" rel-s="#message-menu-entry5" rel="nofollow"><span><i class="icon-envelope"></i> {lang_entry key="msg.entry.sent"}</span></a></div>
        			{/if}
        			{if $user_friends eq 1 or $user_blocking eq 1 or $custom_labels eq 1}
        				<div class="swiper-slide{if $smarty.get.s eq "message-menu-entry7"} swiper-slide-current{/if}"><a href="" rel-s="#message-menu-entry7" rel="nofollow"><span><i class="icon-address-book"></i> {lang_entry key="msg.entry.adr.book"}</span></a></div>
        				<div class="swiper-slide{if $smarty.get.s eq "message-menu-entry7-sub1"} swiper-slide-current{/if}"><a href="" rel-s="#message-menu-entry7-sub1" rel="nofollow"><span><i class="icon-users5"></i> {lang_entry key="msg.entry.friends"}</span></a></div>
        				<div class="swiper-slide{if $smarty.get.s eq "message-menu-entry7-sub2"} swiper-slide-current{/if}"><a href="" rel-s="#message-menu-entry7-sub2" rel="nofollow"><span><i class="icon-blocked"></i> {lang_entry key="msg.entry.blocked.users"}</span></a></div>
        			{/if}
        			{if $internal_messaging eq 1}
        				<div class="swiper-slide{if $smarty.get.s eq "message-menu-entry6"} swiper-slide-current{/if}"><a href="" rel-s="#message-menu-entry6" rel="nofollow"><span><i class="icon-spam"></i> {lang_entry key="msg.entry.spam"}</span></a></div>
        			{/if}
        			{if $user_friends eq 1 and $approve_friends eq 1}
        				<div class="swiper-slide{if $smarty.get.s eq "message-menu-entry4"} swiper-slide-current{/if}"><a href="" rel-s="#message-menu-entry4" rel="nofollow"><span><i class="icon-notebook"></i> {lang_entry key="msg.entry.fr.invite"}</span></a></div>
        			{/if}
        			</div>
        		</div>
        	</div>
            <div class="section-top-bar button-actions section-bottom-border left-float top-bottom-padding jc-sb ai-center">
                <div class="sortings">{include file="tpl_backend/tpl_settings/ct-save-top.tpl"}</div>
                <div class="page-actions">{include file="tpl_backend/tpl_settings/ct-save-open-close.tpl"}</div>
            </div>
            <div class="clearfix"></div>
            <div class="vs-column full">
            {generate_html type="{if $menu_entry ne "message-menu-entry7"}pmsg_entry{else}contacts_layout{/if}" bullet_id="ct-bullet1" entry_id="ct-entry-details1" bb="1" section="{if $menu_entry ne "message-menu-entry7"}messages{/if}"}
            </div>
            <div class="clearfix"></div>
        </div>
        {include file="tpl_backend/tpl_settings/ct-switch-js.tpl"}
        {include file="tpl_backend/tpl_settings/ct-actions-js.tpl"}
        <script type="text/javascript">{include file="f_scripts/be/js/settings-accordion.js"}</script>
        <script type="text/javascript">
	$(document).ready(function () {ldelim}
		var nma=$('.sidebar-container .menu-panel-entry.menu-panel-entry-active').attr('id');
		var ma=$('.sidebar-container .menu-panel-entry.menu-panel-entry-active').attr('id').slice(0,19);
		if(ma!='message-menu-entry7'){ldelim}$('#custom-labels').append($('#'+ma+'-sub-entries').html());{rdelim}else{ldelim}if(nma=='message-menu-entry7'){ldelim}$('#custom-labels').append('<ul class="sort-nav">'+$('#'+nma+'-sub-entries>ul').html()+'</ul>');{rdelim}else if(nma!='message-menu-entry7-sub1'&&nma!='message-menu-entry7-sub2')$('#custom-labels').append($('#'+ma+'-sub-entries').html());{rdelim}
		$('.tpl-messages nav>ul>li>a').on('click',function(e){ldelim}e.preventDefault();var s=$(this).attr('rel-s');$('.sidebar-container li.menu-panel-entry'+s+' a').click();{rdelim});
		$('.tpl-messages .swiper-tnav .swiper-slide a').on('click',function(e){ldelim}e.preventDefault();var s=$(this).attr('rel-s');$('.sidebar-container li.menu-panel-entry'+s+' a').click();{rdelim});
		$('.tpl-messages #custom-labels>ul>li').on('click',function(e){ldelim}e.preventDefault();var s=$(this).attr('id');$('.sidebar-container li.menu-panel-entry#'+s+' a').click();{rdelim});
		$('.tpl-messages #custom-labels span.label-del').on('click',function(e){ldelim}e.preventDefault();var s=$(this).attr('id');$('.sidebar-container li.menu-panel-entry span.label-del#'+s).click();{rdelim});
		$('.icheck-box input').each(function(){ldelim}var self = $(this);self.iCheck({ldelim}checkboxClass: 'icheckbox_square-blue',radioClass: 'iradio_square-blue',increaseArea: '20%'{rdelim});{rdelim});
                $('.icheck-box input.list-check, .icheck-box.ct input').on('click',function(event){ldelim}var _id = $(this).val();if($(this).is(":checked")){ldelim}$("#hcs-id" + _id).prop('checked',true);{rdelim}else{ldelim}$("#hcs-id" + _id).prop('checked',false);{rdelim}{rdelim});
                $(".icheck-box.ct input").on("ifClicked",function(event){ldelim}event.preventDefault();i=$(this).parent().parent().parent().attr("id").replace("ct-entrylisting","");if($(this).is(":checked")){ldelim}$("#ec-"+i).addClass("no-display");d();if($('.icheck-box.ct input:checked').length==1){ldelim}$('#ct-no-details').removeClass('no-display');$("#ct-header-count").text(0);{rdelim}{rdelim}else{ldelim}$('#ct-no-details').addClass('no-display');$("#ec-"+i).removeClass("no-display");d();{rdelim}d();{rdelim});
                $(".icheck-box.ct").on("click",function(event){ldelim}event.preventDefault();i=$(this).parent().attr("id").replace("ct-entrylisting","");if($("#ec-"+i).hasClass("no-display")){ldelim}$('#ct-no-details').addClass('no-display');$("#ec-"+i).removeClass("no-display");$(this).find('input').iCheck('check');d();{rdelim}else{ldelim}$("#ec-"+i).addClass("no-display");$(this).find('input').iCheck('uncheck');d();if(!$(".icheck-box.ct input").is(":checked")){ldelim}$('#ct-no-details').removeClass('no-display');$("#ct-header-count").text(0);{rdelim}{rdelim}d();{rdelim});
                $("#check-all").on("ifChecked",function(event){ldelim}$('.icheck-box.ct input').iCheck('check');$("#ct-header-count").text($(".ct-entries").length);$('#ct-no-details').addClass('no-display');$('.ec-entry').removeClass('no-display');{rdelim});
                $("#check-all").on("ifUnchecked",function(event){ldelim}$('.icheck-box.ct input').iCheck('uncheck');$("#ct-header-count").text(0);$('#ct-no-details').removeClass('no-display');$('.ec-entry').addClass('no-display');{rdelim});
                function d(){ldelim}setTimeout(()=>{ldelim}$("#ct-header-count").text($('#ct-entry-selection .checked').length){rdelim},"10");{rdelim}
	{rdelim});
	</script>
	{insert name="swiperJS" for="tnav"}
{/if}