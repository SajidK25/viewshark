<!DOCTYPE html>
<html lang="en" class="no-js" data-theme="{$theme_name_be}">
    <head profile="http://www.w3.org/2005/10/profile">
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{$page_title}</title>
        <meta name="robots" content="noindex, nofollow">
        <meta name="description" content="{$metaname_description}">
        <meta name="keywords" content="{$metaname_keywords}">
        <meta name="author" content="{$main_url}">
        <link rel="icon" type="image/png" href="{$main_url}/favicon.png">
        <style>{$theme_style}</style>
        <link rel="stylesheet" type="text/css" href="{$styles_url_be}/init0.min.css">
        <link rel="stylesheet" type="text/css" href="{$scripts_url}/shared/icheck/blue/icheck.min.css">
        {if $smarty.session.lang_count gt 1}
        <link rel="preload" href="{$scripts_url}/shared/flagicon/css/flag-icon.min.css" as="style" onload="this.rel='stylesheet'">
        <noscript><link rel="stylesheet" href="{$scripts_url}/shared/flagicon/css/flag-icon.min.css"></noscript>
        {/if}
	    {insert name="loadbecssplugins"}
        <link rel="stylesheet" type="text/css" href="{$styles_url_be}/mediaqueries.min.css">
	    <link rel="stylesheet" type="text/css" href="{$styles_url_be}/theme/{if $theme_name_be|strpos:'dark'}dark{/if}theme_backend.min.css" id="be-color">
	    <link rel="stylesheet" type="text/css" href="{$styles_url}/custom.min.css">
	    <script type="text/javascript" src="{$javascript_url_be}/jquery.min.js"></script>
	    <script type="text/javascript">WebFont.load({ldelim}google:{ldelim}families:['Roboto:300,400,500,600,700']{rdelim}{rdelim});</script>
    </head>
