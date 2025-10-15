<!DOCTYPE html>
<html lang="en" data-theme="{$theme_name}">
    <head profile="http://www.w3.org/2005/10/profile">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
        <title>{insert name=getPageMeta for="title"}</title>
        <meta name="description" content="{insert name=getPageMeta for="description"}">
        <meta name="keywords" content="{insert name=getPageMeta for="tags"}">
        {assign var="noindex" value=0}
        {if $noindex eq 1 or $page_display eq "tpl_search" or $page_display eq "tpl_error" or ($page_display eq "tpl_view" and $smarty.get.p) or ($page_display eq "tpl_view" and $smarty.get.rs)}
        <meta name="robots" content="noindex, nofollow">
        {else}
        <meta name="robots" content="index, follow">
        <meta name="revisit-after" content="2 days">
        {/if}
        {if $page_display eq "tpl_view" or $page_display eq "tpl_shorts"}{include file="tpl_frontend/tpl_headview_min.tpl"}{elseif $page_display eq "tpl_channel"}{include file="tpl_frontend/tpl_headchannel_min.tpl"}{elseif $smarty.get.next eq ""}{include file="tpl_frontend/tpl_headmain_min.tpl"}{/if}
        <link rel="icon" type="image/png" href="{$main_url}/favicon.png">
        {include file="tpl_frontend/tpl_favicon.tpl"}
        <style>{$theme_style}</style>
	    <link rel="stylesheet" type="text/css" href="{$styles_url}/init0.min.css">
	    {if $smarty.session.lang_count gt 1}
        <link rel="preload" href="{$scripts_url}/shared/flagicon/css/flag-icon.min.css" as="style" onload="this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{$scripts_url}/shared/flagicon/css/flag-icon.min.css"></noscript>
        {/if}
        {insert name="loadcssplugins"}
        <link rel="preload" href="{$styles_url}/media_queries.min.css" as="style" onload="this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{$styles_url}/media_queries.min.css"></noscript>
	    <link rel="stylesheet" type="text/css" href="{$styles_url}/theme/yt.min.css">
        <link rel="stylesheet" type="text/css" href="{$styles_url}/theme/{if $theme_name|strpos:'dark'}dark{/if}theme.min.css" id="fe-color">
        <link rel="stylesheet" type="text/css" href="{$styles_url_be}/theme/{if $theme_name|strpos:'dark'}dark{/if}theme_backend.min.css" id="be-color">
	    <link rel="stylesheet" type="text/css" href="{$styles_url}/custom.min.css">
	    <script type="text/javascript" src="{$javascript_url}/jquery.min.js"></script>
	    <script type="text/javascript">jQuery.migrateMute=true;</script>
        <script type="text/javascript">if(navigator.platform==='iPad'||navigator.platform==='iPhone'||navigator.platform==='iPod'){ldelim}WebFont.load({ldelim}google:{ldelim}families:['Roboto:300,400,500,600,700']{rdelim}{rdelim});{rdelim}</script>
        {if $is_mobile}<script src="{$main_url}/index.js?v=1" defer></script>{/if}
    </head>
