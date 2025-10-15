    <body class="be {$page_display} {if $page_display eq "backend_tpl_affiliate" or $page_display eq "backend_tpl_subscriber" or $page_display eq "backend_tpl_token"} tpl_files{/if}{if $page_display eq "backend_tpl_token"} backend_tpl_subscriber{/if}{if $theme_name_be|strpos:'dark'} dark{/if} scroll scroll-{if $theme_name_be|strpos:'dark'}dark{else}light{/if}">
        {include file="tpl_backend/tpl_header/tpl_headernav.tpl"}
{if $smarty.session.ADMIN_NAME ne ""}
        {include file="tpl_backend/tpl_menupanel.tpl"}
{/if}
        {include file="tpl_backend/tpl_footer.tpl"}

        {page_display section=$page_display}

        {include file="tpl_backend/tpl_footerjs_min.tpl"}
    </body>
</html>
