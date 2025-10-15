{if !$blocked}
	<script type="text/javascript">{literal}!function(e,t,s){var r="JSSocials",a=function(e,s){return t.isFunction(e)?e.apply(s,t.makeArray(arguments).slice(2)):e},n=/(\.(jpeg|png|gif|bmp|svg)$|^data:image\/(jpeg|png|gif|bmp|svg\+xml);base64)/i,i=/(&?[a-zA-Z0-9]+=)?\{([a-zA-Z0-9]+)\}/g,o={G:1e9,M:1e6,K:1e3},l={};function h(e,s){var a=t(e);a.data(r,this),this._$element=a,this.shares=[],this._init(s),this._render()}h.prototype={url:"",text:"",shareIn:"blank",showLabel:function(e){return!1===this.showCount?e>this.smallScreenWidth:e>=this.largeScreenWidth},showCount:function(e){return!(e<=this.smallScreenWidth)||"inside"},smallScreenWidth:640,largeScreenWidth:1024,resizeTimeout:200,elementClass:"jssocials",sharesClass:"jssocials-shares",shareClass:"jssocials-share",shareButtonClass:"jssocials-share-button",shareLinkClass:"jssocials-share-link",shareLogoClass:"jssocials-share-logo",shareLabelClass:"jssocials-share-label",shareLinkCountClass:"jssocials-share-link-count",shareCountBoxClass:"jssocials-share-count-box",shareCountClass:"jssocials-share-count",shareZeroCountClass:"jssocials-share-no-count",_init:function(e){this._initDefaults(),t.extend(this,e),this._initShares(),this._attachWindowResizeCallback()},_initDefaults:function(){this.url=e.location.href,this.text=t.trim(t("meta[name=description]").attr("content")),this.title=t.trim(t("title").text())},_initShares:function(){this.shares=t.map(this.shares,t.proxy(function(e){"string"==typeof e&&(e={share:e});var s=e.share&&l[e.share];if(!s&&!e.renderer)throw Error("Share '"+e.share+"' is not found");return t.extend({url:this.url,text:this.text,title:this.title},s,e)},this))},_attachWindowResizeCallback:function(){t(e).on("resize",t.proxy(this._windowResizeHandler,this))},_detachWindowResizeCallback:function(){t(e).off("resize",this._windowResizeHandler)},_windowResizeHandler:function(){(t.isFunction(this.showLabel)||t.isFunction(this.showCount))&&(e.clearTimeout(this._resizeTimer),this._resizeTimer=setTimeout(t.proxy(this.refresh,this),this.resizeTimeout))},_render:function(){this._clear(),this._defineOptionsByScreen(),this._$element.addClass(this.elementClass),this._$shares=t("<div>").addClass(this.sharesClass).appendTo(this._$element),this._renderShares()},_defineOptionsByScreen:function(){this._screenWidth=t(e).width(),this._showLabel=a(this.showLabel,this,this._screenWidth),this._showCount=a(this.showCount,this,this._screenWidth)},_renderShares:function(){t.each(this.shares,t.proxy(function(e,t){this._renderShare(t)},this))},_renderShare:function(e){var s;(s=t.isFunction(e.renderer)?t(e.renderer()):this._createShare(e)).addClass(this.shareClass).addClass(e.share?"jssocials-share-"+e.share:"").addClass(e.css).appendTo(this._$shares)},_createShare:function(e){var s=t("<div>"),r=this._createShareLink(e).appendTo(s);if(this._showCount){var a="inside"===this._showCount,n=a?r:t("<div>").addClass(this.shareCountBoxClass).appendTo(s);n.addClass(a?this.shareLinkCountClass:this.shareCountBoxClass),this._renderShareCount(e,n)}return s},_createShareLink:function(e){var s=this._getShareStrategy(e).call(e,{shareUrl:this._getShareUrl(e)});return s.addClass(this.shareLinkClass).append(this._createShareLogo(e)),this._showLabel&&s.append(this._createShareLabel(e)),t.each(this.on||{},function(r,a){t.isFunction(a)&&s.on(r,t.proxy(a,e))}),s},_getShareStrategy:function(e){var t=c[e.shareIn||this.shareIn];if(!t)throw Error("Share strategy '"+this.shareIn+"' not found");return t},_getShareUrl:function(e){var t=a(e.shareUrl,e);return this._formatShareUrl(t,e)},_createShareLogo:function(e){var s=e.logo,r=n.test(s)?t("<img>").attr("src",e.logo):t("<i>").addClass(s);return r.addClass(this.shareLogoClass),r},_createShareLabel:function(e){return t("<span>").addClass(this.shareLabelClass).text(e.label)},_renderShareCount:function(e,s){var r=t("<span>").addClass(this.shareCountClass);s.addClass(this.shareZeroCountClass).append(r),this._loadCount(e).done(t.proxy(function(e){e&&(s.removeClass(this.shareZeroCountClass),r.text(e))},this))},_loadCount:function(e){var s=t.Deferred(),r=this._getCountUrl(e);if(!r)return s.resolve(0).promise();var a=t.proxy(function(t){s.resolve(this._getCountValue(t,e))},this);return t.getJSON(r).done(a).fail(function(){t.get(r).done(a).fail(function(){s.resolve(0)})}),s.promise()},_getCountUrl:function(e){var t=a(e.countUrl,e);return this._formatShareUrl(t,e)},_getCountValue:function(e,s){var r=(t.isFunction(s.getCount)?s.getCount(e):e)||0;return"string"==typeof r?r:this._formatNumber(r)},_formatNumber:function(e){return t.each(o,function(t,s){if(e>=s)return e=parseFloat((e/s).toFixed(2))+t,!1}),e},_formatShareUrl:function(t,s){return t.replace(i,function(t,r,a){var n=s[a]||"";return n?(r||"")+e.encodeURIComponent(n):""})},_clear:function(){e.clearTimeout(this._resizeTimer),this._$element.empty()},_passOptionToShares:function(e,s){var r=this.shares;t.each(["url","text"],function(a,n){n===e&&t.each(r,function(t,r){r[e]=s})})},_normalizeShare:function(e){return t.isNumeric(e)?this.shares[e]:"string"==typeof e?t.grep(this.shares,function(t){return t.share===e})[0]:e},refresh:function(){this._render()},destroy:function(){this._clear(),this._detachWindowResizeCallback(),this._$element.removeClass(this.elementClass).removeData(r)},option:function(e,t){if(1===arguments.length)return this[e];this[e]=t,this._passOptionToShares(e,t),this.refresh()},shareOption:function(e,t,s){if(e=this._normalizeShare(e),2===arguments.length)return e[t];e[t]=s,this.refresh()}},t.fn.jsSocials=function(e){var s=t.makeArray(arguments),a=s.slice(1),n=this;return this.each(function(){var s,i=t(this),o=i.data(r);if(o){if("string"==typeof e){if(void 0!==(s=o[e].apply(o,a))&&s!==o)return n=s,!1}else o._detachWindowResizeCallback(),o._init(e),o._render()}else new h(i,e)}),n};var u=function(e){var s;t.isPlainObject(e)?s=h.prototype:(s=l[e],e=arguments[1]||{}),t.extend(s,e)},c={popup:function(s){return t("<a>").attr("href","#").on("click",function(){return e.open(s.shareUrl,null,"width=600, height=400, location=0, menubar=0, resizeable=0, scrollbars=0, status=0, titlebar=0, toolbar=0"),!1})},blank:function(e){return t("<a>").attr({target:"_blank",href:e.shareUrl})},self:function(e){return t("<a>").attr({target:"_self",href:e.shareUrl})}};e.jsSocials={Socials:h,shares:l,shareStrategies:c,setDefaults:u}}(window,jQuery),function(e,t,s,r){t.extend(s.shares,{email:{label:"E-mail",logo:"fa fa-at",shareUrl:"mailto:{to}?subject={title}&body={url}",countUrl:"",shareIn:"self"},twitter:{label:"Twitter",logo:"fa-brands fa-twitter",shareUrl:"https://twitter.com/share?url={url}&text={text}&via={via}&hashtags={hashtags}",countUrl:""},gab:{label:"Gab",logo:"gab-logo",shareUrl:"https://gab.com/compose?url={url}&text={title}",countUrl:""},parler:{label:"Parler",logo:"parler-logo",shareUrl:"https://parler.com/new-post?message={title}&url={url}",countUrl:""},facebook:{label:"Facebook",logo:"fa-brands fa-facebook",shareUrl:"https://facebook.com/sharer/sharer.php?u={url}",countUrl:"https://graph.facebook.com/?id={url}",getCount:function(e){return e.share&&e.share.share_count||0}},vkontakte:{label:"Like",logo:"fa fa-vk",shareUrl:"https://vk.com/share.php?url={url}&title={title}&description={text}",countUrl:"https://vk.com/share.php?act=count&index=1&url={url}",getCount:function(e){return parseInt(e.slice(15,-2).split(", ")[1])}},googleplus:{label:"Google+",logo:"fa-brands fa-google",shareUrl:"https://plus.google.com/share?url={url}",countUrl:""},linkedin:{label:"LinkedIn",logo:"fa-brands fa-linkedin",shareUrl:"https://www.linkedin.com/shareArticle?mini=true&url={url}",countUrl:"https://www.linkedin.com/countserv/count/share?format=jsonp&url={url}&callback=?",getCount:function(e){return e.count}},pinterest:{label:"Pinterest",logo:"fa-brands fa-pinterest",shareUrl:"https://pinterest.com/pin/create/bookmarklet/?media={media}&url={url}&description={text}",countUrl:"https://api.pinterest.com/v1/urls/count.json?&url={url}&callback=?",getCount:function(e){return e.count}},stumbleupon:{label:"Stumble",logo:"fa-brands fa-stumbleupon",shareUrl:"http://www.stumbleupon.com/submit?url={url}&title={title}",countUrl:"https://cors-anywhere.herokuapp.com/https://www.stumbleupon.com/services/1.01/badge.getinfo?url={url}",getCount:function(e){return e.result.views}},telegram:{label:"Telegram",logo:"fa fa-paper-plane",shareUrl:"tg://msg?text={url} {text}",countUrl:"",shareIn:"self"},whatsapp:{label:"WhatsApp",logo:"fa-brands fa-whatsapp",shareUrl:"whatsapp://send?text={url} {text}",countUrl:"",shareIn:"self"},line:{label:"LINE",logo:"fa fa-comment",shareUrl:"http://line.me/R/msg/text/?{text} {url}",countUrl:""},viber:{label:"Viber",logo:"fa fa-volume-control-phone",shareUrl:"viber://forward?text={url} {text}",countUrl:"",shareIn:"self"},pocket:{label:"Pocket",logo:"fa-brands fa-get-pocket",shareUrl:"https://getpocket.com/save?url={url}&title={title}",countUrl:""},messenger:{label:"Share",logo:"fa fa-commenting",shareUrl:"fb-messenger://share?link={url}",countUrl:"",shareIn:"self"}})}(window,jQuery,window.jsSocials);{/literal}</script>
{if (($page_display eq "tpl_view" or $page_display eq "tpl_shorts") and ($smarty.get.v ne "" or $smarty.get.a ne "" or $smarty.get.l ne "" or $smarty.get.s ne ""))}
	{if (($smarty.get.v ne "" or $smarty.get.l ne "" or $smarty.get.s ne "") and $video_player eq "vjs" and ($embed_src eq "local" or $embed_src eq "youtube")) or ($smarty.get.a ne "" and $audio_player eq "vjs")}
	<script type="text/javascript" src="https://vjs.zencdn.net/5.19/video.min.js"></script>
	<script type="text/javascript">var logo="{$logo_file}";var logohref="{$logo_href}";</script>
	{if $vjs_advertising eq 1 and !$is_subbed}
	<script type="text/javascript">var fk='{$file_key}';{if $ad_skip}var ad_skip='{$ad_skip}';{/if}var ad_client='{$ad_client}';var adTagUrl='{$ad_tag_url}';var compjs='{if $is_mobile}ads.mob{else}{if $ad_tag_comp eq "1"}ads.comp{else}ads{/if}{/if}';</script>
	{if $ad_client eq "ima" and $embed_src eq "local"}
	<script type="text/javascript" src="https://imasdk.googleapis.com/js/sdkloader/ima3.js"></script>
	<script type="text/javascript" src="{$scripts_url}/shared/videojs/min/ima.min.js"></script>
	{elseif $ad_client eq "vast" and $embed_src eq "local" and !$is_subbed}
	<script type="text/javascript" src="{$scripts_url}/shared/videojs/min/videojs_5.vast.vpaid.min.js"></script>
	{/if}
	{/if}
	<script type="text/javascript" src="{$scripts_url}/shared/videojs/videojs-scripts.min.js"></script>
	{if $smarty.get.l ne ""}
	<script type="text/javascript" src="{$scripts_url}/shared/videojs/videojs-hlsjs-plugin.js"></script>
        <script type="text/javascript" src="{$scripts_url}/shared/nosleep.min.js"></script>
        <script type="text/javascript">
        	{if $is_mobile}var noSleep = new NoSleep();function enableNoSleep(){ldelim}noSleep.enable();document.removeEventListener('click',enableNoSleep,false);{rdelim}document.addEventListener('click', enableNoSleep, false);{/if}
        	window.addEventListener('message',function(e){ldelim}if(e.origin!=="{$live_chat_server}")return;var task=e.data['task'];if(typeof task!='undefined'){ldelim}switch(task){ldelim}case 'fixedchat':$('.inner-block.with-menu').addClass('pfixedoff');break;case 'nofixedchat':$('.inner-block.with-menu').removeClass('pfixedoff');$("html,body").animate({ldelim}scrollTop:0{rdelim},"fast");break;case 'showchat':$('#vs-chat-wrap iframe').css('display','block');$('#vs-chat-wrap i.spinner').detach();var th=$("body").hasClass("dark")?"th0":"th1";document.getElementById("vs-chat").contentWindow.postMessage({ldelim}"viz":th,"location":window.location.href{rdelim},"{$live_chat_server}");break;case 'disconnect':window.location='{$main_url}/{href_entry key="signout"}';break;case 'noscroll': $('html').css('overflow-y','hidden');break;case 'resumescroll': $('html').css('overflow-y','scroll');break;{rdelim}{rdelim}{rdelim});
        </script>
	{/if}
        {/if}
{/if}
{if ($page_display eq "tpl_view" or $page_display eq "tpl_shorts") and ($smarty.get.v ne "" or $smarty.get.l ne "" or $smarty.get.s ne "")}
        <script type="text/javascript">{if $video_player eq "vjs" and ($embed_src eq "local" or $embed_src eq "youtube")}{insert name=getVJSJS usr_key=$usr_key file_key=$file_key file_hd=$hd next=$next pl_key=$pl_key}{/if}</script>
{elseif ($page_display eq "tpl_view" and $smarty.get.i ne "")}
        <script type="text/javascript">{insert name=getImageJS pl_key=$smarty.get.p|sanitize}</script>
{elseif $page_display eq "tpl_view" and $smarty.get.a ne ""}
        <script type="text/javascript">{if $audio_player eq "vjs"}{insert name=getVJSJS usr_key=$usr_key file_key=$file_key file_hd=$hd next=$next pl_key=$pl_key}{/if}</script>
{elseif $page_display eq "tpl_view" and $smarty.get.d ne ""}
		<script type="module">{insert name=getDOCJS usr_key=$usr_key file_key=$file_key file_hd=0 next=$next pl_key=$pl_key}</script>
{/if}
	<script type="text/javascript">
	{literal}
	$(document).ready(function(){
	swiperShorts("suggested");
	$('.vjs-menu-button').on('click',function(e){$('.vjs-menu').removeClass('vjs-lock-showing');});{/literal}{if ($page_display eq "tpl_view" or $page_display eq "tpl_shorts") and (($smarty.get.v ne "" and ($embed_src eq "local" or $embed_src eq "youtube")) or $smarty.get.l ne "" or $smarty.get.a ne "")}{literal}
	$('<div class="vjs-autoplay-switch"><label class="label"><input id="vjstoggle" type="checkbox"{/literal}{if $smarty.session.ap eq 1} checked{/if}{literal}><span class="background">'+'<i class="knob icon-play" id="vjsplay" rel="tooltip" title="{/literal}{lang_entry key="view.files.autoplay.on"}{literal}"></i>'+'<i class="knob icon-pause" id="vjspause" rel="tooltip" title="{/literal}{lang_entry key="view.files.autoplay.off"}{literal}"></i>'+'</span></label></div>').insertBefore('.vjs-playback-rate');
	function vjswitch(){var play=document.getElementById('vjsplay');var pause=document.getElementById('vjspause');var vjsbg=document.querySelector(".vjs-autoplay-switch .background");var vjsinput=document.getElementById("vjstoggle");var n=0;{/literal}{if $smarty.session.ap eq 1}pause.style.display="none";play.style.display="block";n=1;{/if}{literal}vjsbg.addEventListener("click",()=>{if(n===0){pause.style.display="none";play.style.display="block";n=1}else{pause.style.display="block";play.style.display="none";n=0}$("input[name='autoplay_switch_check']").click();})}vjswitch();{/literal}{/if}{literal}
	$('#shorts-arrow-up').on('click',function(){t=$(this).attr("rel-prev");if(typeof t!=='undefined')window.location='{/literal}{$main_url}/{href_entry key="shorts"}/{literal}'+t;});
	$('#shorts-arrow-down').on('click',function(){t=$(this).attr("rel-next");if(typeof t!=='undefined')window.location='{/literal}{$main_url}/{href_entry key="shorts"}/{literal}'+t;});
	$('.showSingle-more-menu li').on('click',function(e){t=$(this);r=t.attr('rel');$('div.showSingle-lb[target="'+r+'"]').click()});
	$(document).on({
	  click:function(e){
		e.preventDefault();mw='50%';t=$(this);s=t.attr('target');
		if(typeof s=="undefined"||s=="")return;
		switch(s){
		  case 'comment':
			var th=$("body").hasClass("dark")?"scroll-dark":"scroll-light";
			$('.posted-comments').addClass('scroll '+th);
			var ww=$('.video-js').width()-30;
			$('.overlay-data').css('left','calc(46% - '+ww+'px)').css('transform', 'translateX(calc(-50% + '+(ww/2)+'px))');
			$('.overlay-comments').show();
			  $('#view-player').addClass('with-comments');
			return;
			break;case 'more':$(this).find(".showSingle-more-menu").toggle();return;break;case 'report':mw='70%';break;
		};
		$.fancybox.open({
		  href:'#div-'+s,type:'inline',afterLoad:function(){$('.tooltip').hide()},opts:{onComplete:function(){}},margin:0,minWidth:'50%',maxWidth:'95%',maxHeight:'90%'
		});
		}},"div.showSingle-lb,div.showSingle-lb-login,a.showSingle-lb-login,i.showSingle-lb-login");

	function swiperShorts(t) {
			const swiperShort = new Swiper("#"+t+"-list .swiper-short-side", {
              direction: "horizontal",
              loop: false,
              slidesPerView: "auto",
              freeMode: false,
              slideToClickedSlide: false,
              spaceBetween: 0,
              grabCursor:true,

              navigation: {
                nextEl: "#"+t+"-list .swiper-button-next-short",
                prevEl: "#"+t+"-list .swiper-button-prev-short",
              },

              on: {
                init: function(){
                    $("#"+t+"-list .swiper-button-prev, #"+t+"-list .swiper-button-next").removeAttr("style");
                    $("#"+t+"-list .swiper-button-prev-short.swiper-button-disabled").prev().addClass("ml-0");
                    this.slideTo($("#"+t+"-list .swiper-slide-current").index());
                },
              },
            });
            swiperShort.on('slideChange', function () {
          	  thumbFade();
          	});
            swiperShort.on('setTranslate', function () {
          	  setTimeout(() => { thumbFade(); }, "100");
          	});
            swiperShort.on('update', function () {
          	  setTimeout(() => { thumbFade(); }, "200");
          	});
	}

	$('.get-media').on('click',function(e){
	  e.preventDefault();
	  var t=$(this).attr('rel-type');
	  $('.swiper-top-main .swiper-slide').removeClass('swiper-slide-current');
	  $(this).parent().addClass('swiper-slide-current');
	  if ($('#'+t+'-list li').length>0){
		$('.suggested-list').hide();
		$('#'+t+'-list').show();
	  }else{
		$('#'+t+'-list').show();
		$('.related-column').mask("");
		$.post('{/literal}{$main_url}/{href_entry key="watch"}?do=side-column',{ldelim}
		  t:t,f:$("input[name='uf_type']").val(),k:"{$file_key}",u:$("input[name='uf_vuid']").val(),c:$("input[name='uf_ct']").val()
		{rdelim},function(data){literal}{
		  $('.suggested-list').hide();
		  $('#'+t+'-list').html(data).show();

		  swiperShorts(t);
		  thumbFade();
		  $('.related-column').unmask();
		});
	  }
	});

	$('.tpl_shorts .share-text').on('click', function(e){e.preventDefault();$(this).prev().click()});});{/literal}
	{literal}$("#share").jsSocials({shares:["twitter","facebook","whatsapp","telegram","gab","parler","googleplus","linkedin","pinterest","pocket","stumbleupon","viber","email"],showCount:false,shareIn:"popup",}){/literal}
	</script>
{/if}