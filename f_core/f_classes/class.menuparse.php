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

class VMenuparse
{
    public function sectionDisplay($section, $entry)
    {
        global $smarty, $class_language, $language, $class_filter;

        include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
        include_once $class_language->setLanguageFile('backend', 'language.members.entries');

        switch ($entry) {
            case 'backend-menu-entry1':
            case 'backend-menu-entry2':
            case 'backend-menu-entry2-sub2':
            case 'backend-menu-entry3':
            case 'backend-menu-entry4': //subscription system
            case 'backend-menu-entry5': //personalized channels
            case 'backend-menu-entry6': //file manager - files
            case 'backend-menu-entry8': //advertising - player ads
            case 'backend-menu-entry10': //account management
            case 'backend-menu-entry11': //file manager - by category
            case 'backend-menu-entry12': //file players - jw player
            case 'backend-menu-entry13': //file players - flow player
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                break;

            case 'backend-menu-entry2-sub17': //signup/registration
            case 'backend-menu-entry2-sub19': //username/password recovery
            case 'backend-menu-entry2-sub18': //signin/log in
            case 'backend-menu-entry2-sub15': //internal messaging
            case 'backend-menu-entry2-sub20': //captcha verification
            case 'backend-menu-entry2-sub21': //video embed plugin
            case 'backend-menu-entry2-sub12': //file uploading
            case 'backend-menu-entry2-sub13': //file permissions/settings
            case 'backend-menu-entry2-sub1': //global meta data
            case 'backend-menu-entry2-sub4': //general behavior
            case 'backend-menu-entry3-sub1': //server configuration - mail service
            case 'backend-menu-entry4-sub1': //subs.system - general setup
            case 'backend-menu-entry5-sub1': //personalized channels - general setup
            case 'backend-menu-entry3-sub2': //video conversion
            case 'backend-menu-entry3-sub20': //mp4 conversion
            case 'backend-menu-entry3-sub21': //webm conversion
            case 'backend-menu-entry3-sub22': //ogv conversion
            case 'backend-menu-entry3-sub23': //mobile conversion
            case 'backend-menu-entry3-sub6': //image conversion
            case 'backend-menu-entry3-sub3': //audio conversion
            case 'backend-menu-entry3-sub4': //document conversion
            case 'backend-menu-entry3-sub7': //PHP Information
            case 'backend-menu-entry3-sub9': //server details
            case 'backend-menu-entry2-sub11': //activity logging
            case 'backend-menu-entry2-sub6': //admin panel access
            case 'backend-menu-entry2-sub7': //main modules
            case 'backend-menu-entry2-sub9': //email templates, page templates
            case 'backend-menu-entry3-sub12': //session settings
            case 'backend-menu-entry3-sub18': //timezone settings
            case 'backend-menu-entry2-sub3': //sitemaps
            case 'backend-menu-entry2-sub14': //file players
            case 'backend-menu-entry2-sub8': //guest permissions
            case 'backend-menu-entry2-sub23': //live streaming module
            case 'backend-menu-entry2-sub24': //ondemand module
                global $class_database;

                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $cfg         = $class_database->getConfigurations(VArrayConfig::getCfg());
                break;

            case 'backend-menu-entry14-sub1': //live streaming, broadcast servers
            case 'backend-menu-entry14-sub2': //live streaming, streaming servers
            case 'backend-menu-entry14-sub3': //live streaming, VOD servers
            case 'backend-menu-entry14-sub4': //live streaming, broadcast lbs
            case 'backend-menu-entry14-sub5': //live streaming, streaming lbs
            case 'backend-menu-entry14-sub6': //live streaming, streaming lbs
            case 'backend-menu-entry14-sub7': //live streaming, chat servers
            case 'backend-menu-entry14-sub8': //live streaming, token management
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_streaming');
                break;

            case 'backend-menu-entry15-sub1': //token dashboard, token purchases
            case 'backend-menu-entry15-sub2': //token dashboard, token donations
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_token');
                break;

            case 'backend-menu-entry11-sub1': //vjs player, website player
            case 'backend-menu-entry11-sub2': //vjs player, embedded player
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_players');
                break;

            case 'backend-menu-entry6-sub1'; //file management - videos
            case 'backend-menu-entry6-sub2'; //file management - images
            case 'backend-menu-entry6-sub3'; //file management - audio
            case 'backend-menu-entry6-sub4'; //file management - documents
            case 'backend-menu-entry6-sub5'; //file management - blogs
            case 'backend-menu-entry6-sub6'; //file management - broadcasts
            case 'backend-menu-entry6-sub7'; //file management - shorts
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';

                $smarty->assign('page_display', 'backend_tpl_files');
                break;

            case 'backend-menu-entry16-sub1'; //comment management - videos
            case 'backend-menu-entry16-sub2'; //comment management - live
            case 'backend-menu-entry16-sub3'; //comment management - image
            case 'backend-menu-entry16-sub4'; //comment management - audio
            case 'backend-menu-entry16-sub5'; //comment management - doc
            case 'backend-menu-entry16-sub6'; //comment management - blog
            case 'backend-menu-entry16-sub7'; //comment management - channel
            case 'backend-menu-entry16-sub8'; //comment management - shorts
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';

                $smarty->assign('page_display', 'backend_tpl_comments');
                break;

            case 'backend-menu-entry4-sub2': //subs.system - membership types
            case 'backend-menu-entry4-sub3': //subs.system - discount codes
            case 'backend-menu-entry4-sub4': //subs.system - subscription types
            case 'backend-menu-entry5-sub2': //pers. channels - channel types
            case 'backend-menu-entry10-sub2'; //acct management - user accounts
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_members');
                break;
            case 'backend-menu-entry2-sub5': //public categories
            case 'backend-menu-entry2-sub5v': //public categories
            case 'backend-menu-entry2-sub5i': //public categories
            case 'backend-menu-entry2-sub5a': //public categories
            case 'backend-menu-entry2-sub5d': //public categories
            case 'backend-menu-entry2-sub5c': //public categories
            case 'backend-menu-entry2-sub5b': //public categories
            case 'backend-menu-entry2-sub5l': //public categories
                $display_tpl = 'tpl_settings/backend-menu-entry2-sub5.tpl';
                $smarty->assign('page_display', 'backend_tpl_categ');
                break;
            case 'backend-menu-entry3-sub5': //ban list
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_categ');
                break;
            case 'backend-menu-entry8-sub2': //vjs ads
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_jwcodes');
                break;
            case 'backend-menu-entry8-sub4': //vjs ads
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_fpads');
                break;
            case 'backend-menu-entry3-sub13': //up servers
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_servers');
                break;
            case 'backend-menu-entry3-sub14': //video xfers
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_servers');
                break;
            case 'backend-menu-entry3-sub15': //image xfers
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_servers');
                break;
            case 'backend-menu-entry3-sub16': //audio xfers
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_servers');
                break;
            case 'backend-menu-entry3-sub17': //doc xfers
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_servers');
                break;
            case 'backend-menu-entry3-sub19': //short xfers
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_servers');
                break;
            case 'backend-menu-entry2-sub16': //languages
                $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                $smarty->assign('page_display', 'backend_tpl_lang');
                break;
            case 'backend-menu-entry7': //banner ads
            case 'backend-menu-entry7-sub1':
            case 'backend-menu-entry7-sub2':
            case 'backend-menu-entry7-sub3':
            case 'backend-menu-entry7-sub4':
            case 'backend-menu-entry7-sub5':
            case 'backend-menu-entry7-sub6':
            case 'backend-menu-entry7-sub7':
            case 'backend-menu-entry7-sub8':
            case 'backend-menu-entry7-sub9':
            case 'backend-menu-entry7-sub10':
            case 'backend-menu-entry7-sub11':
            case 'backend-menu-entry7-sub12':
            case 'backend-menu-entry7-sub13':
            case 'backend-menu-entry7-sub14':
            case 'backend-menu-entry7-sub15':
                $display_tpl = 'tpl_settings/backend-menu-entry7.tpl';
                $smarty->assign('page_display', 'backend_tpl_banners');
                break;
            case 'backend-menu-entry9': //advertising groups
            case 'backend-menu-entry9-sub1':
            case 'backend-menu-entry9-sub2':
            case 'backend-menu-entry9-sub3':
            case 'backend-menu-entry9-sub4':
            case 'backend-menu-entry9-sub5':
            case 'backend-menu-entry9-sub6':
            case 'backend-menu-entry9-sub7':
            case 'backend-menu-entry9-sub8':
            case 'backend-menu-entry9-sub9':
            case 'backend-menu-entry9-sub10':
            case 'backend-menu-entry9-sub11':
            case 'backend-menu-entry9-sub12':
            case 'backend-menu-entry9-sub13':
            case 'backend-menu-entry9-sub14':
            case 'backend-menu-entry9-sub15':
                $display_tpl = 'tpl_settings/backend-menu-entry9.tpl';
                $smarty->assign('page_display', 'backend_tpl_adv');
                break;
            default:
                if (substr($_GET['s'], 0, 20) == 'backend-menu-entry11' and $_GET['s'] != 'backend-menu-entry11') {
//browse by category
                    $s_arr = explode("-", $class_filter->clr_str($_GET['s']));
                    $s_t   = substr($s_arr[3], 3);

                    switch ($s_t[0]) {
                        case "v":$entry = 'backend-menu-entry6-sub1';
                            break;
                        case "i":$entry = 'backend-menu-entry6-sub2';
                            break;
                        case "a":$entry = 'backend-menu-entry6-sub3';
                            break;
                        case "d":$entry = 'backend-menu-entry6-sub4';
                            break;
                        case "b":$entry = 'backend-menu-entry6-sub5';
                            break;
                        case "l":$entry = 'backend-menu-entry6-sub6';
                            break;
                        case "s":$entry = 'backend-menu-entry6-sub7';
                            break;
                    }

                    $display_tpl = 'tpl_settings/' . $entry . '.tpl';
                    $smarty->assign('categ_display', $s_t[0]);
                    $smarty->assign('page_display', 'backend_tpl_files');
                }
                break;
        }
        $smarty->assign('global_section', 'backend');
        $smarty->display('tpl_' . $section . '/' . $display_tpl);
    }
}
