    <body class="fe media-width-768 is-fw{if $is_mobile eq 1} is-mobile{/if}{if $page_display eq "tpl_files_edit"} tpl_files_edit{elseif $page_display eq "tpl_subs"} tpl_files{/if}{if $theme_name|strpos:'dark'} dark{/if} scroll scroll-{if $theme_name|strpos:'dark'}dark{else}light{/if}">
  		<div id="theme-preloader" class="" style="display: none"><div class="d-flex ai-center jc-c"><i class="spinner icon-spinner"></i></div></div>
        <div id="wrapper" class="{$page_display}{if $tpl_section ne ""} {$tpl_section}{/if}{if $page_display ne "tpl_files" and $page_display ne "tpl_index"} tpl_files{/if}{if $page_display eq "tpl_channels"} tpl_browse{/if}{if $page_display eq "tpl_tokens"} tpl_subscribers{/if}{if $page_display eq "tpl_search" and $smarty.get.tf eq 6} tpl_channels{/if}{if $smarty.session.sbm eq 0} g5{/if}">
            {include file="tpl_frontend/tpl_header/tpl_headernav_yt.tpl"}
            <div class="spacer"></div>
            {include file="tpl_frontend/tpl_body_main.tpl"}
        </div>
        {include file="tpl_frontend/tpl_footer.tpl"}{include file="tpl_frontend/tpl_footerjs_min.tpl"}
    </body>
{if $page_display eq "tpl_view" and $smarty.get.l ne "" and $is_live eq 1}<script type="text/javascript">var int=self.setInterval(lv,60000);function lv(){ldelim}if(typeof player!=="undefined" && player.currentTime() > 0 && !player.paused() && !player.ended()){ldelim}var u="{$main_url}/{href_entry key="viewers"}?s={$file_key}";$.get(u,function(){ldelim}{rdelim});{rdelim}{rdelim}</script>{/if}
{if $google_analytics ne ""}
    <script async src="https://www.googletagmanager.com/gtag/js?id={$google_analytics}"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){ldelim}dataLayer.push(arguments);{rdelim}gtag('js',new Date());gtag('config','{$google_analytics}');</script>
{/if}
</html>