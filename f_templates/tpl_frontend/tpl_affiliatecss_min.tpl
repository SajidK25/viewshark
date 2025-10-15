{if ($page_display eq "tpl_affiliate" and $smarty.session.USER_AFFILIATE eq 1) or ($page_display eq "tpl_subscribers" and $smarty.session.USER_PARTNER eq 1) or ($page_display eq "tpl_tokens" and $smarty.session.USER_PARTNER eq 1)}
        <link rel="preload" href="{$scripts_url}/shared/datepicker/tiny-date-picker.min.css" as="style" onload="this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{$scripts_url}/shared/datepicker/tiny-date-picker.min.css"></noscript>
        <link rel="preload" href="{$scripts_url}/shared/datepicker/date-range-picker.min.css" as="style" onload="this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{$scripts_url}/shared/datepicker/date-range-picker.min.css"></noscript>
{/if}
