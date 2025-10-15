<?php
/*******************************************************************************************************************
| Software Name        : ViewShark
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) ViewShark
| Website              : https://www.viewshark.com
| E-mail               : support@viewshark.com || viewshark@gmail.com
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the ViewShark End-User License Agreement, available online at:
| https://www.viewshark.com/support/license/
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2013-2024 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

/* footer pages array */
function footerPages()
{
    global $language, $cfg;

    $_fp = array(
        "page-live"      => array("link_name" => $language["footer.menu.item11"], //the link text/name
            "page_name"                           => 'tpl_live.tpl', //load this template when link is clicked
            "page_url"                            => '', //open this link instead (leave empty to load template)
            "page_open"                           => 0), //open in new window

        "page-partner"   => array("link_name" => $language["footer.menu.item13"], //the link text/name
            "page_name"                           => 'tpl_partner.tpl', //load this template when link is clicked
            "page_url"                            => '', //open this link instead (leave empty to load template)
            "page_open"                           => 0), //open in new window

        "page-affiliate" => array("link_name" => $language["footer.menu.item12"], //the link text/name
            "page_name"                           => 'tpl_affiliate.tpl', //load this template when link is clicked
            "page_url"                            => '', //open this link instead (leave empty to load template)
            "page_open"                           => 0), //open in new window

        "page-about"     => array("link_name" => $language["footer.menu.item2"],
            "page_name"                           => 'tpl_about.tpl',
            "page_url"                            => '',
            "page_open"                           => 0),

        "page-copyright" => array("link_name" => $language["footer.menu.item3"],
            "page_name"                           => 'tpl_copyright.tpl',
            "page_url"                            => '',
            "page_open"                           => 0),

        "page-dmca"      => array("link_name" => $language["footer.menu.item14"],
            "page_name"                           => 'tpl_dmca.tpl',
            "page_url"                            => '',
            "page_open"                           => 0),

        "page-terms"     => array("link_name" => $language["footer.menu.item6"],
            "page_name"                           => 'tpl_terms.tpl',
            "page_url"                            => '',
            "page_open"                           => 0),

        "page-privacy"   => array("link_name" => $language["footer.menu.item7"],
            "page_name"                           => 'tpl_privacy.tpl',
            "page_url"                            => '',
            "page_open"                           => 0),
    );

    if ($cfg["user_subscriptions"] == 0) {
        unset($_fp["page-partner"]);
    }

    if ($cfg["live_module"] == 0) {
        unset($_fp["page-live"]);
    }

    if ($cfg["affiliate_module"] == 0) {
        unset($_fp["page-affiliate"]);
    }

    return $_fp;
}
