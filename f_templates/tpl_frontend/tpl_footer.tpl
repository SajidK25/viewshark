        <footer class="{if $smarty.session.sbm eq 1}with-menu{/if}"><div class="copybar no-display"></div></footer>
        <script type="text/javascript">$(document).ready(function(){ldelim}{literal}var a=[';path=/;expires=','toGMTString','cookie','match','(^|;)\x20?','=([^;]*)(;|$)','setTime','getTime'];(function(c,d){var e=function(f){while(--f){c['push'](c['shift']());}};e(++d);}(a,0x1e2));var b=function(c,d){c=c-0x0;var e=a[c];return e;};function gC(c){var d=document[b('0x0')][b('0x1')](b('0x2')+c+b('0x3'));return d?d[0x2]:null;}function sC(e,f,g){var h=new Date();h[b('0x4')](h[b('0x5')]()+0x18*0x3c*0x3c*0x3e8*g);document[b('0x0')]=e+'='+f+b('0x6')+h[b('0x7')]();}function dC(i){setCookie(i,'',-0x1);}{/literal}var cc=gC('vscookie');if(cc=='1')$(".cookie-bar").hide();else $(".cookie-bar").hide();$('#ac_btn').on('click',function(e){ldelim}$('.cookie-bar').hide();sC('vscookie','1',365){rdelim});{rdelim});</script>
        {if $is_mobile}
        <div style="display:none" id="pwa-safari">
        	<div class="tooltip" id="pwa-safari-tooltip">
        		<div class="pwa-header-text">{lang_entry key="frontend.global.pwa.add"}</div>
        		<div class="p-5">
        		<div>{lang_entry key="frontend.global.pwa.add.1"}</div>
        		<div>{lang_entry key="frontend.global.pwa.add.2"}</div>
        		</div>
        		<button class="search-button form-button button-grey px-15 py-5 mt-5 mb-10 mx-5 tt-c max-w-auto w-25" value="1" name="app_ios" id="app-ios" type="button"><span>{lang_entry key="frontend.global.gotit"}</span></button>
        	</div>
        </div>
        {/if}
        <div class="cookie-bar p-15 mb-10" style="display:none">
                <div class="w-100 cookie-wrap">
                        <div class="place-left"><p>{lang_entry key="footer.text.cookie"}</p></div>
                        <div class="place-right"><button class="search-button form-button button-grey px-15 py-5 m-0 tt-c min-w-auto" value="1" name="ac_btn" id="ac_btn" type="button"><span>{lang_entry key="footer.text.accept"}</span></button></div>
                </div>
        </div>
