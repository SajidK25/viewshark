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

class VbeAdvertising
{
    /* get banner group counts */
    public function advCount_group($for)
    {
        global $db;

        switch ($for) {
            case "backend-menu-entry9-sub1":$db_add_query = "WHERE `adv_name` LIKE 'home_promoted_%'";
                break;
            case "backend-menu-entry9-sub2":$db_add_query = "WHERE `adv_name` LIKE 'browse_chan_%'";
                break;
            case "backend-menu-entry9-sub3":$db_add_query = "WHERE `adv_name` LIKE 'browse_files_%'";
                break;
            case "backend-menu-entry9-sub4":$db_add_query = "WHERE `adv_name` LIKE 'view_files_%'";
                break;
            case "backend-menu-entry9-sub5":$db_add_query = "WHERE `adv_name` LIKE 'view_comm_%'";
                break;
            case "backend-menu-entry9-sub6":$db_add_query = "WHERE `adv_name` LIKE 'view_resp_%'";
                break;
            case "backend-menu-entry9-sub7":$db_add_query = "WHERE `adv_name` LIKE 'view_pl_%'";
                break;
            case "backend-menu-entry9-sub8":$db_add_query = "WHERE `adv_name` LIKE 'respond_%'";
                break;
            case "backend-menu-entry9-sub9":$db_add_query = "WHERE `adv_name` LIKE 'register_%'";
                break;
            case "backend-menu-entry9-sub10":$db_add_query = "WHERE `adv_name` LIKE 'login_%'";
                break;
            case "backend-menu-entry9-sub11":$db_add_query = "WHERE `adv_name` LIKE 'search_%'";
                break;
            case "backend-menu-entry9-sub12":$db_add_query = "WHERE `adv_name` LIKE 'footer_%'";
                break;
            case "backend-menu-entry9-sub13":$db_add_query = "WHERE `adv_name` LIKE 'browse_pl_%'";
                break;
            case "backend-menu-entry9-sub14":$db_add_query = "WHERE `adv_name` LIKE 'per_file_%'";
                break;
            default:break;
        }

        $sql = sprintf("SELECT COUNT(*) AS `total` FROM `db_advgroups` %s", $db_add_query);
        $res = $db->execute($sql);

        return $res->fields['total'];
    }
    /* get banner ad counts */
    public function advCount_banner($for)
    {
        global $db;

        switch ($for) {
            case "backend-menu-entry7-sub1":$db_add_query = "WHERE `adv_group` IN(1, 2)";
                break;
            case "backend-menu-entry7-sub2":$db_add_query = "WHERE `adv_group` IN(7, 8, 9, 10, 11, 12)";
                break;
            case "backend-menu-entry7-sub3":$db_add_query = "WHERE `adv_group` IN(13, 14, 15)";
                break;
            case "backend-menu-entry7-sub4":$db_add_query = "WHERE `adv_group` IN(16, 17, 18, 19, 20, 49)";
                break;
            case "backend-menu-entry7-sub5":$db_add_query = "WHERE `adv_group` IN(21, 22, 23, 24)";
                break;
            case "backend-menu-entry7-sub6":$db_add_query = "WHERE `adv_group` IN(25, 26, 27, 28)";
                break;
            case "backend-menu-entry7-sub7":$db_add_query = "WHERE `adv_group` IN(29, 30, 31, 32, 33, 34)";
                break;
            case "backend-menu-entry7-sub8":$db_add_query = "WHERE `adv_group` IN(35, 36)";
                break;
            case "backend-menu-entry7-sub9":$db_add_query = "WHERE `adv_group` IN(37, 38)";
                break;
            case "backend-menu-entry7-sub10":$db_add_query = "WHERE `adv_group` IN(39, 40)";
                break;
            case "backend-menu-entry7-sub11":$db_add_query = "WHERE `adv_group` IN(41, 42)";
                break;
            case "backend-menu-entry7-sub12":$db_add_query = "WHERE `adv_group` IN(43, 44)";
                break;
            case "backend-menu-entry7-sub13":$db_add_query = "WHERE `adv_group` IN(45, 46, 47, 48)";
                break;
            case "backend-menu-entry7-sub14":$db_add_query = "WHERE `adv_group` IN(50, 51, 52, 53, 54, 55)";
                break;
            default:break;
        }

        $sql = sprintf("SELECT COUNT(*) AS `total` FROM `db_advbanners` %s", $db_add_query);
        $res = $db->execute($sql);

        return $res->fields['total'];
    }
    /* main ad group details */
    public function mainAdvBannerDetails($_dsp = '', $entry_id = '', $db_id = '', $ad_state = '', $ad_name = '', $ad_desc = '', $ad_type = '', $ad_group = '', $ad_code = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            $ad_set = self::processBannerEntry();

            if ($_GET['do'] == 'add' and !$ad_set) {
                $entryid = (int) $_POST['hc_id'];
                $ad_type = "adv_type_" . $entryid;

                $ad_name  = $class_filter->clr_str($_POST['frontend_global_name']);
                $ad_desc  = $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_desc']);
                $ad_group = intval($_POST['adv_group_ids']);
                $ad_code  = $_POST['backend_adv_text_code'];
            }
        }

        return self::advBannerDetails($_dsp, $entry_id, $db_id, $ad_state, $ad_name, $ad_desc, $ad_type, $ad_group, $ad_code);
    }
    /* main jw files details */
    public function mainAdvCodeDetails($_dsp = '', $entry_id = '', $db_id = '', $db_state = '', $db_type = '', $db_name = '', $db_key = '', $db_code = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            $ad_set = self::processJWfileEntry();

            if ($_GET['do'] == 'add' and !$ad_set) {
                $db_name = $class_filter->clr_str($_POST['frontend_global_name']);
                $db_type = $class_filter->clr_str($_POST['jw_type']);
                $db_code = $class_filter->clr_str($_POST['backend_adv_file_code']);
            }
        }

        return self::JWadvCodes($_dsp, $entry_id, $db_id, $db_state, $db_type, $db_name, $db_key, $db_code);
    }
    /* main videojs ad entries details */
    public function vjsAdvAdDetails($_dsp = '', $entry_id = '', $ad_id = '', $ad_state = '', $ad_name = '', $ad_key = '', $ad_type = '', $ad_position = '', $ad_offset = '', $ad_duration = '', $ad_client = '', $ad_format = '', $ad_server = '', $ad_file = '', $ad_tag = '', $ad_comp_div = '', $ad_comp_id = '', $ad_comp_w = '', $ad_comp_h = '', $ad_click_track = '', $ad_click_url = '', $ad_track_events = '', $ad_impressions = '', $ad_clicks = '', $ad_primary = '', $ad_mobile = '', $ad_custom = '', $ad_custom_url = '', $ad_skip = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            $ad_set = self::processVJSadEntry();

            if ($_GET['do'] == 'add' and !$ad_set) {
                $entryid   = (int) $_POST['hc_id'];
                $ad_client = "vjs_client_" . $entryid;
                $ad_custom = "vjs_custom_" . $entryid;
                $ad_type   = "vjs_type_" . $entryid;

                $ad_name       = $class_filter->clr_str($_POST['frontend_global_name']);
                $ad_tag        = $_POST['backend_adv_vjs_tag'];
                $ad_custom_url = $_POST['backend_adv_vjs_clickthrough'];
                $ad_mobile     = (int) $_POST['backend_adv_vjs_ad_mobile'];
                $ad_comp_div   = intval($_POST['backend_adv_vjs_ad_comp']);
                $ad_skip       = intval($_POST['backend_adv_jw_skip']);
                $ad_comp_id    = $class_filter->clr_str($_POST['backend_adv_vjs_ad_comp_unit']);
                $ad_comp_w     = intval($_POST['backend_adv_vjs_ad_comp_width']);
                $ad_comp_h     = intval($_POST['backend_adv_vjs_ad_comp_height']);
            }
        }
        echo $jw_ads = VGenerate::declareJS('$(document).ready(function(){' . self::VJSadvDetailsJS() . '});');
        return self::VJSadvDetails($_dsp, $entry_id, $db_id, $ad_state, $ad_name, $ad_key, $ad_type, $ad_position, $ad_offset, $ad_duration, $ad_client, $ad_format, $ad_server, $ad_file, $ad_width, $ad_height, $ad_bitrate, $ad_tag, $ad_comp_div, $ad_comp_id, $ad_comp_w, $ad_comp_h, $ad_click_track, $ad_click_url, $ad_impressions, $ad_clicks, $ad_primary, $ad_mobile, $ad_custom, $ad_custom_url, $ad_skip);
    }
    /* main jw ad entries details */
    public function mainAdvAdDetails($_dsp = '', $entry_id = '', $ad_id = '', $ad_state = '', $ad_name = '', $ad_key = '', $ad_type = '', $ad_position = '', $ad_offset = '', $ad_duration = '', $ad_client = '', $ad_format = '', $ad_server = '', $ad_file = '', $ad_tag = '', $ad_comp_div = '', $ad_comp_id = '', $ad_comp_w = '', $ad_comp_h = '', $ad_click_track = '', $ad_click_url = '', $ad_track_events = '', $ad_impressions = '', $ad_clicks = '', $ad_primary = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            $ad_set = self::processJWadEntry();

            if ($_GET['do'] == 'add' and !$ad_set) {
                $entryid     = (int) $_POST['hc_id'];
                $jw_client   = "jw_client_" . $entryid;
                $ad_type     = "jw_type_" . $entryid;
                $jw_format   = "jw_format_" . $entryid;
                $jw_server   = "jw_server_" . $entryid;
                $jw_file     = "jw_file_" . $entryid;
                $jw_position = "jw_position_" . $entryid;

                $ad_name        = $class_filter->clr_str($_POST['frontend_global_name']);
                $ad_position    = $class_filter->clr_str($_POST[$jw_position]);
                $ad_offset      = intval($_POST['backend_adv_jw_offset']);
                $ad_duration    = floatval($_POST['backend_adv_jw_duration']);
                $ad_client      = $class_filter->clr_str($_POST[$jw_client]);
                $ad_format      = $class_filter->clr_str($_POST[$jw_format]);
                $ad_server      = $class_filter->clr_str($_POST[$jw_server]);
                $ad_file        = $class_filter->clr_str($_POST[$jw_file]);
                $ad_width       = $class_filter->clr_str($_POST['backend_adv_jw_width']);
                $ad_height      = $class_filter->clr_str($_POST['backend_adv_jw_height']);
                $ad_bitrate     = $class_filter->clr_str($_POST['backend_adv_jw_bitrate']);
                $ad_tag         = $_POST['backend_adv_jw_tag'];
                $ad_comp_div    = intval($_POST['backend_adv_jw_ad_comp']);
                $ad_comp_id     = $class_filter->clr_str($_POST['backend_adv_jw_ad_comp_id']);
                $ad_comp_w      = intval($_POST['backend_adv_jw_ad_comp_w']);
                $ad_comp_h      = intval($_POST['backend_adv_jw_ad_comp_h']);
                $ad_click_track = intval($_POST['backend_adv_jw_clicktracking']);
                $ad_click_url   = $_POST['backend_adv_jw_clickthrough'];
            }
        }
        echo $jw_ads = VGenerate::declareJS('$(document).ready(function(){' . self::JWadvDetailsJS() . '});');

        return self::JWadvDetails($_dsp, $entry_id, $db_id, $ad_state, $ad_name, $ad_key, $ad_type, $ad_position, $ad_offset, $ad_duration, $ad_client, $ad_format, $ad_server, $ad_file, $ad_width, $ad_height, $ad_bitrate, $ad_tag, $ad_comp_div, $ad_comp_id, $ad_comp_w, $ad_comp_h, $ad_click_track, $ad_click_url, $ad_impressions, $ad_clicks, $ad_primary);
    }
    /* main fp ad entries details */
    public function fpAdvAdDetails($_dsp = '', $entry_id = '', $ad_id = '', $ad_state = '', $ad_name = '', $ad_key = '', $ad_cuepoint = '', $ad_file = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            $ad_set = self::processFPadEntry();

            if ($_GET['do'] == 'add' and !$ad_set) {
                $ad_name     = $class_filter->clr_str($_POST['frontend_global_name']);
                $ad_cuepoint = $class_filter->clr_str($_POST['backend_adv_fp_cuepoint']);
                $ad_file     = $class_filter->clr_str($_POST['fp_file']);
            }
        }
        echo $jw_ads = VGenerate::declareJS('$(document).ready(function(){' . self::JWadvDetailsJS() . '});');

        return self::FPadvDetails($_dsp, $entry_id, $db_id, $ad_state, $ad_name, $ad_key, $ad_cuepoint, $ad_file);
    }
    /* player ads, count menu entries */
    public function playerAdCount($for)
    {
        global $db;

        switch ($for) {
            case "ad_codes":
                $tbl = 'db_jwadcodes';
                break;
            case "vjs_ads":
                $tbl = 'db_vjsadentries';
                break;
            case "jw_ads":
                $tbl = 'db_jwadentries';
                break;
            case "fp_ads":
                $tbl = 'db_fpadentries';
                break;
        }
        $t = $db->execute(sprintf("SELECT COUNT(*) AS `total` FROM `%s`;", $tbl));

        return $t->fields['total'];
    }
    /* jwplayer files, codes */
    public function JWadvCodes($_dsp = '', $entry_id = '', $db_id = '', $db_state = '', $db_type = '', $db_name = '', $db_key = '', $db_code = '')
    {
        global $class_filter, $language;
        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_dsp  = $_init[0];

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block-off;') : null;

        $_sel1 = '<select name="jw_type_' . ((int) $db_id) . '" class="backend-select-input wd300">';
        $_sel1 .= '<option' . ($db_type == 'code' ? ' selected="selected"' : null) . ' value="code">' . $language['backend.adv.jw.code'] . '</option>';
        $_sel1 .= '<option' . ($db_type == 'file' ? ' selected="selected"' : null) . ' value="file">' . $language['backend.adv.jw.file'] . '</option>';
        $_sel1 .= '</select>';

        $html .= '<div id="entry' . $db_id . '-response" class="wd98p left-float"></div>';
        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="db-entry-form' . $db_id . '" method="post" action="" class="entry-form-class" encType="multipart/formdata">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($db_state == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.name'] . '</label>' . $language['frontend.global.required'], 'left-float', 'frontend_global_name', 'backend-text-input wd300', $db_name);

        if ($db_type == 'code' and $_GET['do'] != 'add') {
            $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.text.url'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_file_code', 'backend-textarea-input wd300', $db_code);
        } else {
            if ($_GET['do'] == 'add') {
                $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['frontend.global.type'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel1));

                $js = '$(function(){SelectList.init("jw_type_' . ((int) $db_id) . '");});';

                $html .= VGenerate::declareJS($js);
            } else {
                $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.jw.file'] . '</label>') . VGenerate::simpleDivWrap('left-float lh20', 'adfilename' . $db_id, $db_code) . VGenerate::simpleDivWrap('left-float lh20 left-padding10', '', '<a href="javascript:;" onclick="$(\'#jw-file' . $db_id . '\').click();">' . $language['frontend.global.change'] . '</a>'));
                $html .= '<div class="row"><input type="file" class="no-display" name="jw_adfile" id="jw-file' . $db_id . '" onchange="$(\'#db-entry-form' . $db_id . '\').submit();" /></div>';

                $js = '$(document).ready(function(){';
                $js .= 'var bgurl = current_url + menu_section + \'?s=' . $class_filter->clr_str($_GET['s']) . '&do=file_upload\';';
                $js .= 'var options = { target: "#entry' . $db_id . '-response", beforeSubmit: showRequest, success: showResponse, url: bgurl };';
                $js .= 'function showRequest(){ $("#ct-bullet1-' . $db_id . '").mask(""); }';
                $js .= 'function showResponse(){ $("#ct-bullet1-' . $db_id . '").unmask(); }';
                $js .= '$("#db-entry-form' . $db_id . '").submit(function() { $(this).ajaxSubmit(options); return false; });';
                $js .= '});';

                $html .= VGenerate::declareJS($js);
            }

        }
        $html .= '</div>'; //end vs-mask
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</form></div>';

        return $html;
    }
    /* ad file upload/update */
    public function beAdvFileUpload()
    {
        global $class_filter, $cfg, $db, $language;

        $ufn              = $_FILES['jw_adfile']['name'];
        $upload_file_type = strtoupper(VFileinfo::getExtension($ufn));
        $upload_file_orig = md5($_FILES['jw_adfile']['name']) . '.' . strtolower($upload_file_type);
        $upload_file_name = $class_filter->clr_str($_FILES['jw_adfile']['tmp_name']);
        $upload_in        = $cfg['player_dir'] . '/ad_files/';
        $upload_dst       = $upload_in . $upload_file_orig;
        $upload_allowed   = array('FLV', 'MP4', 'WEBM', 'SWF', 'GIF', 'JPG', 'JPEG', 'PNG');

        $error_message    = !in_array($upload_file_type, $upload_allowed) ? $language['notif.error.invalid.request'] : null;
        echo $show_error  = $error_message != '' ? VGenerate::noticeTpl('', $error_message, '') : null;
        echo $clear_input = $error_message != '' ? VGenerate::declareJS('$(\'input[name$="jw_adfile"]\').val("");') : null;

        if ($error_message == '') {
            $db_id = intval($_POST['hc_id']);
            $f     = $db->execute(sprintf("SELECT `db_code` FROM `db_jwadcodes` WHERE `db_type`='file' AND `db_id`='%s' LIMIT 1;", $db_id));

            if (file_exists($upload_dst)) {@unlink($upload_dst);}
            if ($f->fields['db_code'] != '' and file_exists($upload_in . $f->fields['db_code'])) {
                @unlink($upload_in . $f->fields['db_code']);
            }
            if (rename($upload_file_name, $upload_dst)) {
                @chmod($upload_dst, 0644);
                $db->execute(sprintf("UPDATE `db_jwadcodes` SET `db_code`='%s' WHERE `db_id`='%s' LIMIT 1;", $upload_file_orig, $db_id));
                echo VGenerate::declareJS('$("#adfilename' . $db_id . '").html("' . $upload_file_orig . '");');
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            }
        }
    }
    /* videojs ad details, edit */
    public function VJSadvDetails($_dsp = '', $entry_id = '', $db_id = '', $ad_state = '', $ad_name = '', $ad_key = '', $ad_type = '', $ad_position = '', $ad_offset = '', $ad_duration = '', $ad_client = '', $ad_format = '', $ad_server = '', $ad_file = '', $ad_width = '', $ad_height = '', $ad_bitrate = '', $ad_tag = '', $ad_comp_div = '', $ad_comp_id = '', $ad_comp_w = '', $ad_comp_h = '', $ad_click_track = '', $ad_click_url = '', $ad_track_events = '', $ad_impressions = '', $ad_clicks = '', $ad_primary = '', $ad_mobile = '', $ad_custom = '', $ad_custom_url = '', $ad_skip = '')
    {
        global $class_filter, $language, $db;
        $_init  = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_dsp   = $_init[0];
        $int_id = (int) $db_id;

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block-off;') : null;

        $_sel0 = '<select name="vjs_type_' . $int_id . '" class="ad-type backend-select-input wd300" rel-id="' . $db_id . '">';
        $_sel0 .= '<option' . ($ad_type == 'shared' ? ' selected="selected"' : null) . ' value="shared">' . $language['backend.adv.text.shared'] . '</option>';
        $_sel0 .= '<option' . ($ad_type == 'dedicated' ? ' selected="selected"' : null) . ' value="dedicated">' . $language['backend.adv.text.dedicated'] . '</option>';
        $_sel0 .= '</select>';
        $_sel1 = '<select name="vjs_client_' . $int_id . '" class="ad-client backend-select-input wd300" rel-id="' . $db_id . '">';
        $_sel1 .= '<option' . ($ad_client == 'ima' ? ' selected="selected"' : null) . ' value="ima">' . $language['backend.adv.jw.client.ima'] . '</option>';
        $_sel1 .= '<option' . ($ad_client == 'vast' ? ' selected="selected"' : null) . ' value="vast">' . $language['backend.adv.jw.client.vast'] . '</option>';
        $_sel1 .= '<option' . ($ad_client == 'custom' ? ' selected="selected"' : null) . ' value="custom">' . $language['backend.adv.jw.client.custom'] . '</option>';
        $_sel1 .= '</select>';

        $sql = sprintf("SELECT `db_key`, `db_type`, `db_name`, `db_code` FROM `db_jwadcodes` WHERE `db_active`='1';");
        $res = $db->execute($sql);
        if ($res->fields['db_key']) {
            $_sel2 = '<select name="vjs_custom_' . $int_id . '" class="ad-file backend-select-input wd300" rel-id="' . $db_id . '">';
            while (!$res->EOF) {
                $_sel2 .= '<option' . ($ad_custom == $res->fields['db_key'] ? ' selected="selected"' : null) . ' value="' . $res->fields['db_key'] . '">' . $res->fields['db_name'] . '</option>';

                $res->MoveNext();
            }
            $_sel2 .= '</select>';
        }

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="ad-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= '<div class="left-float wd500">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($ad_state == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        if ($ad_client == 'custom') {
            $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 place-left', '', '<label>' . $language['backend.adv.jw.impressions'] . '</label><span class="conf-green">' . $ad_impressions . '</span>'));
            $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 place-left', '', '<label>' . $language['backend.adv.jw.clicks'] . '</label><span class="conf-green">' . $ad_clicks . '</span>'));
            $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 place-left', '', '<label>' . $language['backend.adv.jw.clickrate'] . '</label><span class="conf-green">' . ($ad_impressions > 0 ? round((($ad_clicks / $ad_impressions) * 100), 2) : 0) . '%</span>'));
        }
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.name'] . '</label>' . $language['frontend.global.required'], 'left-float', 'frontend_global_name', 'backend-text-input wd300', $ad_name);
        $html .= VGenerate::simpleDivWrap('row no-display1', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.text.type'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel0));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.jw.client'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel1));
        $html .= '<div id="' . $entry_id . '-' . $db_id . 'tag" style="display: ' . (($ad_custom != '' and $ad_client == 'custom') ? 'none' : 'block') . ';">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.tag'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_vjs_tag', 'backend-text-input wd300', $ad_tag);
        $html .= '</div>';

        $html .= '<div id="' . $entry_id . '-' . $db_id . 'custom" style="display: ' . (($ad_custom != '' and $ad_client == 'custom') ? 'block' : 'none') . ';">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.jw.ad.file'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel2));
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.clickthrough'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_vjs_clickthrough', 'backend-text-input wd300', $ad_custom_url);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.skip'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_vjs_skip', 'backend-text-input wd300', $ad_skip);
        $html .= '</div>';

        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 icheck-box', '', '<input ' . ($ad_mobile == 1 ? 'checked="checked"' : null) . ' onclick="" type="checkbox" class="ad-off cb-exclude" name="backend_adv_vjs_ad_mobile" value="1" />', '', 'span') . '<label>' . $language['backend.adv.jw.ad.mobile'] . '</label>');
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 icheck-box', '', '<input ' . ($ad_comp_div == 1 ? 'checked="checked"' : null) . ' onclick="" type="checkbox" class="ad-off cb-exclude ac" name="backend_adv_vjs_ad_comp" value="1" />', '', 'span') . '<label>' . $language['backend.adv.jw.ad.comp'] . '</label>');
        $html .= '<div id="' . $entry_id . '-' . $db_id . 'comp" style="display: ' . ($ad_comp_div == 1 ? 'block' : 'none') . ';">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.ad.comp.id'] . '</label>', 'left-float', 'backend_adv_vjs_ad_comp_unit', 'backend-text-input wd300', $ad_comp_id);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.ad.comp.w'] . '</label>', 'left-float', 'backend_adv_vjs_ad_comp_width', 'backend-text-input wd300', $ad_comp_w);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.ad.comp.h'] . '</label>', 'left-float', 'backend_adv_vjs_ad_comp_height', 'backend-text-input wd300', $ad_comp_h);
        $html .= '</div>';

        $html .= VGenerate::declareJS('$(function(){SelectList.init("vjs_client_' . $int_id . '");SelectList.init("vjs_custom_' . $int_id . '");SelectList.init("vjs_type_' . $int_id . '");});');
        $html .= VGenerate::declareJS('$("#' . $entry_id . '-' . $db_id . ' .icheck-box input.ac").on("ifChecked", function(event){$("#' . $entry_id . '-' . $db_id . 'comp").show();});$("#' . $entry_id . '-' . $db_id . ' .icheck-box input").on("ifUnchecked", function(event){$("#' . $entry_id . '-' . $db_id . 'comp").hide();});');
        $html .= VGenerate::declareJS('$("select[name=\'vjs_client_' . $int_id . '\']").on("change", function(){if($(this).val()=="custom"){$("#' . $entry_id . '-' . $db_id . 'custom").show();$("#' . $entry_id . '-' . $db_id . 'tag").hide();}else{$("#' . $entry_id . '-' . $db_id . 'custom").hide();$("#' . $entry_id . '-' . $db_id . 'tag").show();}});');

        $html .= '</div>'; //end vs-mask
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '</form></div>';

        return $html;
    }
    public function VJSadvDetailsJS()
    {
        $js = 'var dsrc = "' . ($_GET['do'] == 'add' ? '#mem-add-new-entry-' : '#ct-entry-details1-') . '";';

        return $js;
    }
    /* jwplayer ad details, edit */
    public function JWadvDetails($_dsp = '', $entry_id = '', $db_id = '', $ad_state = '', $ad_name = '', $ad_key = '', $ad_type = '', $ad_position = '', $ad_offset = '', $ad_duration = '', $ad_client = '', $ad_format = '', $ad_server = '', $ad_file = '', $ad_width = '', $ad_height = '', $ad_bitrate = '', $ad_tag = '', $ad_comp_div = '', $ad_comp_id = '', $ad_comp_w = '', $ad_comp_h = '', $ad_click_track = '', $ad_click_url = '', $ad_track_events = '', $ad_impressions = '', $ad_clicks = '', $ad_primary = '')
    {
        global $class_filter, $language, $db;
        $_init  = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_dsp   = $_init[0];
        $int_id = (int) $db_id;

        $af = $db->execute("SELECT `db_key`, `db_name`, `db_code` FROM `db_jwadcodes` WHERE `db_type`='file' AND `db_active`='1';");

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block-off;') : null;

        $_sel0 = '<select name="jw_type_' . $int_id . '" class="ad-type backend-select-input wd300" rel-id="' . $db_id . '">';
        $_sel0 .= '<option' . ($ad_type == 'shared' ? ' selected="selected"' : null) . ' value="shared">' . $language['backend.adv.text.shared'] . '</option>';
        $_sel0 .= '<option' . ($ad_type == 'dedicated' ? ' selected="selected"' : null) . ' value="dedicated">' . $language['backend.adv.text.dedicated'] . '</option>';
        $_sel0 .= '</select>';
        $_sel1 = '<select name="jw_client_' . $int_id . '" class="ad-client backend-select-input wd300" rel-id="' . $db_id . '">';
        $_sel1 .= '<option' . ($ad_client == 'vast' ? ' selected="selected"' : null) . ' value="vast">' . $language['backend.adv.jw.client.vast'] . '</option>';
        $_sel1 .= '<option' . ($ad_client == 'ima' ? ' selected="selected"' : null) . ' value="ima">' . $language['backend.adv.jw.client.ima'] . '</option>';
        $_sel1 .= '</select>';
        $_sel2 = '<select name="jw_format_' . $int_id . '" class="ad-off backend-select-input wd300">';
        $_sel2 .= '<option' . ($ad_format == 'linear' ? ' selected="selected"' : null) . ' value="linear">' . $language['backend.adv.jw.linear'] . '</option>';
        $_sel2 .= '<option' . ($ad_format == 'nonlinear' ? ' selected="selected"' : null) . ' value="nonlinear">' . $language['backend.adv.jw.nonlinear'] . '</option>';
        $_sel2 .= '</select>';
        $ad_srv = explode(',', $language['backend.adv.jw.server.list']);
        $_sel3  = '<select name="jw_server_' . $int_id . '" class="ad-server backend-select-input wd300" rel-id="' . $db_id . '">';
        foreach ($ad_srv as $a) {
            $_sel3 .= '<option' . ($ad_server == $a ? ' selected="selected"' : null) . ' value="' . $a . '">' . $a . '</option>';
        }
        $_sel3 .= '</select>';

        $ad_evt = explode(',', $language['backend.adv.jw.events']);
        $_sel5  = null;
        foreach ($ad_evt as $b) {
            $_sel5 .= VGenerate::simpleDivWrap('row top-padding3', '', VGenerate::simpleDivWrap('left-float lh20', '', '<input ' . ($ad_click_track == 1 ? 'checked="checked"' : null) . ' type="checkbox" class="cb-exclude" name="backend_adv_jw_clicktracking" value="1" />') . VGenerate::simpleDivWrap('left-float lh20 wd50', '', $b));
        }

        $_sel4 = '<select name="jw_position_' . $int_id . '" class="ad-position backend-select-input wd300" rel-id="' . $db_id . '">';
        $_sel4 .= '<option' . ($ad_position == 'pre' ? ' selected="selected"' : null) . ' value="pre">' . $language['backend.adv.jw.pre'] . '</option>';
        $_sel4 .= '<option' . ($ad_position == 'post' ? ' selected="selected"' : null) . ' value="post">' . $language['backend.adv.jw.post'] . '</option>';
        $_sel4 .= '<option' . ($ad_position == 'offset' ? ' selected="selected"' : null) . ' value="offset">' . $language['backend.adv.jw.offset'] . '</option>';
        $_sel4 .= '</select>';

        if ($af->fields['db_key']) {
            $_sel6 = '<select name="jw_file_' . $int_id . '" class="ad-off backend-select-input wd300">';
            while (!$af->EOF) {
                $_sel6 .= '<option' . ($ad_file == $af->fields['db_key'] ? ' selected="selected"' : null) . ' value="' . $af->fields['db_key'] . '">' . $af->fields['db_name'] . ($af->fields['db_code'] != '' ? ' (' . $af->fields['db_code'] . ')' : ' (code)') . '</option>';
                $af->MoveNext();
            }
            $_sel6 .= '</select>';
        }

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="ad-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= '<div class="left-float wd500">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($ad_state == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.name'] . '</label>' . $language['frontend.global.required'], 'left-float', 'frontend_global_name', 'backend-text-input wd300', $ad_name);
        $html .= VGenerate::simpleDivWrap('row no-display', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.text.type'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel0));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.jw.pos'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel4));
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.offset'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_jw_offset', 'ad-offset backend-text-input wd300', $ad_offset);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.jw.client'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel1));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.jw.server'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel3));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.jw.format'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel2));
        $html .= VGenerate::simpleDivWrap('row no-display', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.jw.ad.file'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel6));
        $html .= VGenerate::declareJS('$(function(){SelectList.init("jw_client_' . $int_id . '");});');
        $html .= VGenerate::declareJS('$(function(){SelectList.init("jw_server_' . $int_id . '");});');
        $html .= VGenerate::declareJS('$(function(){SelectList.init("jw_format_' . $int_id . '");});');
        $html .= VGenerate::declareJS('$(function(){SelectList.init("jw_position_' . $int_id . '");});');
        $html .= VGenerate::declareJS('$(function(){SelectList.init("jw_file_' . $int_id . '");});');
        $html .= VGenerate::declareJS('$(function(){SelectList.init("jw_type_' . $int_id . '");});');

        $html .= '<div class="row">';
        $html .= '<div class="left-float lh20 wd140">&nbsp;</div>';
        $html .= '<div class="left-float left-padding5"><div class="left-float lh20"><label>Width</label></div><div class="left-float left-padding5"><input type="text" value="' . ($_GET['do'] == 'add' ? 480 : $ad_width) . '" class="ad-off backend-text-input wd50" name="backend_adv_jw_width" /></div></div>';
        $html .= '<div class="left-float left-padding5"><div class="left-float lh20"><label>Height</label></div><div class="left-float left-padding5"><input type="text" value="' . ($_GET['do'] == 'add' ? 360 : $ad_height) . '" class="ad-off backend-text-input wd50" name="backend_adv_jw_height" /></div></div>';
        $html .= '<div class="left-float left-padding5"><div class="left-float lh20"><label>Bitrate</label></div><div class="left-float left-padding5"><input type="text" value="' . ($_GET['do'] == 'add' ? 300 : $ad_bitrate) . '" class="ad-off backend-text-input wd50" name="backend_adv_jw_bitrate" /></div></div>';
        $html .= '</div>';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.duration'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_jw_duration', 'ad-off backend-text-input wd300', $ad_duration);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.tag'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_jw_tag', 'backend-text-input wd300', $ad_tag);
        if ($ad_impressions > 0 and $ad_clicks > 0) {
            $ct  = explode('.', (($ad_clicks / $ad_impressions) * 100));
            $ctr = $ct[0] . '.' . substr($ct[1], 0, 2);
        } else {
            $ctr = 0;
        }
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 icheck-box', '', '<input ' . ($ad_click_track == 1 ? 'checked="checked"' : null) . ' type="checkbox" class="ad-off cb-exclude" name="backend_adv_jw_clicktracking" value="1" />', '', 'span') . '<label>' . $language['backend.adv.jw.clicktracking'] . '</label> ' . VGenerate::simpleDivWrap('left-float left-padding15 top-padding3' . ($ad_server == 'custom' ? '' : ' no-display'), '', '<label>Impressions: ' . $ad_impressions . ' / Clicks: ' . $ad_clicks . ' / CTR: ' . $ctr . '%</label>', '', 'span'));
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.jw.clickthrough'] . '</label>', 'left-float', 'backend_adv_jw_clickthrough', 'ad-off backend-text-input wd300', $ad_click_url);
        $html .= '</div>'; //end vs-mask
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '</form></div>';

        return $html;
    }
    /* banner ad details, edit */
    public function advBannerDetails($_dsp = '', $entry_id = '', $db_id = '', $ad_state = '', $ad_name = '', $ad_desc = '', $ad_type = '', $ad_group = '', $ad_code = '')
    {
        global $class_filter, $language;
        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_dsp  = $_init[0];

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>')) : null;

        $_sel0 = '<select name="adv_type_' . ((int) $db_id) . '" class="ad-type backend-select-input wd300" rel-id="' . $db_id . '">';
        $_sel0 .= '<option' . ($ad_type == 'shared' ? ' selected="selected"' : null) . ' value="shared">' . $language['backend.adv.text.shared'] . '</option>';
        $_sel0 .= '<option' . ($ad_type == 'dedicated' ? ' selected="selected"' : null) . ' value="dedicated">' . $language['backend.adv.text.dedicated'] . '</option>';
        $_sel0 .= '</select>';

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($ad_state == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.name'] . '</label>' . $language['frontend.global.required'], 'left-float', 'frontend_global_name', 'backend-text-input wd350', $ad_name);
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language['backend.menu.members.entry1.sub2.entry.desc'] . '</label>', 'left-float', 'backend_menu_members_entry1_sub2_entry_desc', 'backend-textarea-input wd350', $ad_desc);
        $html .= VGenerate::simpleDivWrap('row no-display', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.text.type'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel0));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.text.group'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', VGenerate::adGroupsList((int) $db_id, $ad_group)));
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language['backend.adv.text.code'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_text_code', 'backend-textarea-input wd350', stripslashes($ad_code));
        $html .= '</div>'; //end vs-mask
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language['frontend.global.required.items']);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</form></div>';

        $html .= VGenerate::declareJS('$(function(){SelectList.init("adv_group_ids_' . ((int) $db_id) . '");SelectList.init("adv_type_' . ((int) $db_id) . '");});');

        return $html;
    }
    /* flowplayer ad details, edit */
    public function FPadvDetails($_dsp = '', $entry_id = '', $db_id = '', $ad_state = '', $ad_name = '', $ad_key = '', $ad_cuepoint = '', $ad_css = '', $ad_file = '')
    {
        global $class_filter, $language, $db;
        $_init  = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date  = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_dsp   = $_init[0];
        $int_id = (int) $db_id;

        $af = $db->execute("SELECT `db_key`, `db_type`, `db_name`, `db_code` FROM `db_jwadcodes` WHERE `db_type`='code' AND `db_active`='1';");

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block-off;') : null;

        if ($af->fields['db_key']) {
            $_sel6 = '<select name="fp_file_' . $int_id . '" class="ad-off backend-select-input wd300">';
            while (!$af->EOF) {
                $_sel6 .= '<option' . ($ad_file == $af->fields['db_key'] ? ' selected="selected"' : null) . ' value="' . $af->fields['db_key'] . '">' . $af->fields['db_name'] . ($af->fields['db_type'] == 'code' ? '' : ' (' . $af->fields['db_code'] . ')') . '</option>';
                $af->MoveNext();
            }
            $_sel6 .= '</select>';
        }

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="ad-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= '<div class="left-float wd500">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($ad_state == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.name'] . '</label>' . $language['frontend.global.required'], 'left-float', 'frontend_global_name', 'backend-text-input wd300', $ad_name);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.fp.cuepoint'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_fp_cuepoint', 'backend-text-input wd300', $ad_cuepoint);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.adv.jw.code'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel6));
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language['backend.adv.fp.css'] . '</label>', 'left-float', 'backend_adv_fp_css', 'backend-textarea-input wd300', $ad_css);
        $html .= '</div>'; //end vs-mask
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '</form></div>';
        $html .= VGenerate::declareJS('$(function(){SelectList.init("fp_file_' . $int_id . '");});');

        return $html;
    }
    public function JWadvDetailsJS()
    {
        $js = 'var dsrc = "' . ($_GET['do'] == 'add' ? '#mem-add-new-entry-' : '#ct-entry-details1-') . '";';

        $js .= '$(".ad-client").change(function(){';
        $js .= 'var aid = $(this).attr("rel-id");';
        $js .= 'var aval = $(this).val();';
        $js .= 'if(aval == "vast"){';
        $js .= 'if($(dsrc+aid+" .ad-server").val() == "custom"){';
        $js .= '$(dsrc+aid+" .ad-off, "+dsrc+aid+" .ad-server").prop("disabled", false);';
        $js .= '} else {';
        $js .= '$(dsrc+aid+" .ad-server").prop("disabled", false);';
        $js .= '}';
        $js .= '} else {';
        $js .= '$(dsrc+aid+" .ad-off, "+dsrc+aid+" .ad-server").prop("disabled", true);';
        $js .= '}';
        $js .= '});';
        $js .= '$(".ad-position").change(function(){';
        $js .= 'var aid = $(this).attr("rel-id");';
        $js .= 'var aval = $(this).val();';
        $js .= 'if(aval == "offset"){';
        $js .= '$(dsrc+aid+" .ad-offset").prop("disabled", false);';
        $js .= '} else {';
        $js .= '$(dsrc+aid+" .ad-offset").prop("disabled", true);';
        $js .= '}';
        $js .= '});';
        $js .= '$(".ad-server").change(function(){';
        $js .= 'var aid = $(this).attr("rel-id");';
        $js .= 'var aval = $(this).val();';
        $js .= 'if(aval == "custom"){';
        $js .= '$(dsrc+aid+" .ad-off").prop("disabled", false);';
        $js .= '} else {';
        $js .= '$(dsrc+aid+" .ad-off").prop("disabled", true);';
        $js .= '}';
        $js .= '});';

        $js .= '$(".ad-position").each(function(){';
        $js .= 'var aid = $(this).attr("rel-id");';
        $js .= 'var aval = $(this).val();';
        $js .= 'if(aval == "offset"){';
        $js .= '$(dsrc+aid+" .ad-offset").prop("disabled", false);';
        $js .= '} else {';
        $js .= '$(dsrc+aid+" .ad-offset").prop("disabled", true);';
        $js .= '}';
        $js .= '});';

        $js .= '$(".ad-client").each(function(){';
        $js .= 'var aid = $(this).attr("rel-id");';
        $js .= 'var aval = $(this).val();';
        $js .= 'if(aval == "vast"){';
        $js .= '$(dsrc+aid+" .ad-off, "+dsrc+aid+" .ad-server").prop("disabled", false);';
        $js .= '} else {';
        $js .= '$(dsrc+aid+" .ad-off, "+dsrc+aid+" .ad-server").prop("disabled", true);';
        $js .= '}';
        $js .= '});';

        $js .= '$(".ad-server").each(function(){';
        $js .= 'var aid = $(this).attr("rel-id");';
        $js .= 'var aval = $(this).val();';
        $js .= 'if($(dsrc+aid+" .ad-client").val() == "ima"){$(dsrc+aid+" .ad-server").prop("disabled", true);}';
        $js .= 'if(aval == "custom" && $(dsrc+aid+" .ad-client").val() == "vast"){';
        $js .= '$(dsrc+aid+" .ad-off").prop("disabled", false);';
        $js .= '} else {';
        $js .= '$(dsrc+aid+" .ad-off").prop("disabled", true);';
        $js .= '}';
        $js .= '});';

        return $js;
    }
    /* processing banner entry */
    public function processBannerEntry()
    {
        global $class_database, $db, $language;

        $form           = VArraySection::getArray("adv_banners");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $ad_name  = $form[0]['adv_name'];
            $ad_type  = $form[0]['adv_type'];
            $ad_descr = $form[0]['adv_description'];
            $ad_group = $form[0]['adv_group'];
            $ad_code  = $form[0]['adv_code'];
            $ad_id    = intval($_POST['hc_id']);
            switch ($_GET['do']) {
                case "update":
                    $sql = sprintf("UPDATE `db_advbanners` SET `adv_name`='%s', `adv_type`='%s', `adv_description`='%s', `adv_group`='%s', `adv_code`='%s' WHERE `adv_id`='%s' LIMIT 1;", $ad_name, $ad_type, $ad_descr, $ad_group, $ad_code, $ad_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_advbanners', $form[0]);
                    break;
            }
            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
                return true;
            } else {
                return false;
            }
        }
    }
    /* processing jw ad file entry */
    public function processJWfileEntry()
    {
        global $class_database, $db, $language;

        $form           = VArraySection::getArray("jw_files");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $db_name = $form[0]['db_name'];
            $db_type = $form[0]['db_type'];
            $db_code = $form[0]['db_code'];
            $db_id   = intval($_POST['hc_id']);
            switch ($_GET['do']) {
                case "update":
                    $sql = sprintf("UPDATE `db_jwadcodes` SET `db_name`='%s' %s WHERE `db_id`='%s' LIMIT 1;", $db_name, (isset($_POST['backend_adv_file_code']) ? ", `db_code`='" . $db_code . "'" : null), $db_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_jwadcodes', $form[0]);
                    break;
            }
            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
                return true;
            } else {
                return false;
            }
        }
    }
    /* processing videojs general ad entries */
    public function processVJSadEntry()
    {
        global $class_database, $db, $language;

        $form           = VArraySection::getArray("vjs_ads");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $db_id         = intval($_POST['hc_id']);
            $ad_name       = $form[0]['ad_name'];
            $ad_type       = $form[0]['ad_type'];
            $ad_client     = $form[0]['ad_client'];
            $ad_custom     = $form[0]['ad_custom'];
            $ad_custom_url = $form[0]['ad_custom_url'];
            $ad_skip       = $form[0]['ad_skip'];
            $ad_tag        = $form[0]['ad_tag'];
            $ad_mobile     = $form[0]['ad_mobile'];
            $ad_comp_div   = $form[0]['ad_comp_div'];
            $ad_comp_id    = $form[0]['ad_comp_id'];
            $ad_comp_w     = $form[0]['ad_comp_w'];
            $ad_comp_h     = $form[0]['ad_comp_h'];

            switch ($_GET['do']) {
                case "update":
                    $sql = sprintf("UPDATE `db_vjsadentries` SET
                    `ad_name`='%s',
                    `ad_type`='%s',
                    `ad_client`='%s',
                    `ad_tag`='%s',
                    `ad_mobile`='%s',
                    `ad_comp_div`='%s',
                    `ad_comp_id`='%s',
                    `ad_comp_w`='%s',
                    `ad_comp_h`='%s',
                    `ad_custom`='%s',
                    `ad_custom_url`='%s',
                    `ad_skip`='%s'
                    WHERE `ad_id`='%s' LIMIT 1;", $ad_name, $ad_type, $ad_client, $ad_tag, $ad_mobile, $ad_comp_div, $ad_comp_id, $ad_comp_w, $ad_comp_h, $ad_custom, $ad_custom_url, $ad_skip, $db_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_vjsadentries', $form[0]);
                    break;
            }
            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
                return true;
            } else {
                return false;
            }
        }
    }
    /* processing jw general ad entries */
    public function processJWadEntry()
    {
        global $class_database, $db, $language;

        $form           = VArraySection::getArray("jw_ads");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $db_id          = intval($_POST['hc_id']);
            $ad_name        = $form[0]['ad_name'];
            $ad_type        = $form[0]['ad_type'];
            $ad_position    = $form[0]['ad_position'];
            $ad_offset      = $form[0]['ad_offset'];
            $ad_duration    = $form[0]['ad_duration'];
            $ad_client      = $form[0]['ad_client'];
            $ad_format      = $form[0]['ad_format'];
            $ad_server      = $form[0]['ad_server'];
            $ad_file        = $form[0]['ad_file'];
            $ad_width       = $form[0]['ad_width'];
            $ad_height      = $form[0]['ad_height'];
            $ad_bitrate     = $form[0]['ad_bitrate'];
            $ad_tag         = $form[0]['ad_tag'];
            $ad_comp_div    = $form[0]['ad_comp_div'];
            $ad_comp_id     = $form[0]['ad_comp_id'];
            $ad_comp_w      = $form[0]['ad_comp_w'];
            $ad_comp_h      = $form[0]['ad_comp_h'];
            $ad_click_track = $form[0]['ad_click_track'];
            $ad_click_url   = $form[0]['ad_click_url'];

            switch ($_GET['do']) {
                case "update":
                    $sql = sprintf("UPDATE `db_jwadentries` SET
                    `ad_name`='%s',
                    `ad_type`='%s',
                    `ad_position`='%s',
                    `ad_offset`='%s',
                    `ad_duration`='%s',
                    `ad_client`='%s',
                    `ad_format`='%s',
                    `ad_server`='%s',
                    `ad_file`='%s',
                    `ad_width`='%s',
                    `ad_height`='%s',
                    `ad_bitrate`='%s',
                    `ad_tag`='%s',
                    `ad_comp_div`='%s',
                    `ad_comp_id`='%s',
                    `ad_comp_w`='%s',
                    `ad_comp_h`='%s',
                    `ad_click_track`='%s',
                    `ad_click_url`='%s'
                    WHERE `ad_id`='%s' LIMIT 1;", $ad_name, $ad_type, $ad_position, $ad_offset, $ad_duration, $ad_client, $ad_format, $ad_server, $ad_file, $ad_width, $ad_height, $ad_bitrate, $ad_tag, $ad_comp_div, $ad_comp_id, $ad_comp_w, $ad_comp_h, $ad_click_track, $ad_click_url, $db_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_jwadentries', $form[0]);
                    break;
            }
            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
                return true;
            } else {
                return false;
            }
        }
    }
    /* processing fp general ad entries */
    public function processFPadEntry()
    {
  		return;
        global $class_database, $db, $language;

        $form           = VArraySection::getArray("fp_ads");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $db_id       = intval($_POST['hc_id']);
            $ad_name     = $form[0]['ad_name'];
            $ad_cuepoint = $form[0]['ad_cuepoint'];
            $ad_css      = $form[0]['ad_css'];
            $ad_file     = $form[0]['ad_file'];

            switch ($_GET['do']) {
                case "update":
                    $sql = sprintf("UPDATE `db_fpadentries` SET
                    `ad_name`='%s',
                    `ad_cuepoint`='%s',
                    `ad_css`='%s',
                    `ad_file`='%s'
                    WHERE `ad_id`='%s' LIMIT 1;", $ad_name, $ad_cuepoint, $ad_css, $ad_file, $db_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_fpadentries', $form[0]);
                    break;
            }
            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
                return true;
            } else {
                return false;
            }
        }
    }
    /* ad group details, edit */
    public function advGroupDetails($_dsp = '', $entry_id = '', $db_id = '', $ad_state = '', $ad_name = '', $ad_desc = '', $ad_type = '', $ad_width = '', $ad_height = '', $ad_class, $ad_style)
    {
        global $class_filter, $language;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>')) : null;

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($ad_state == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::simpleDivWrap('row top-padding5', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['frontend.global.name'] . '</label><span class="conf-green">' . $ad_name . '</span>'));
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language['backend.menu.members.entry1.sub2.entry.desc'] . '</label>', 'left-float', 'backend_menu_members_entry1_sub2_entry_desc', 'backend-textarea-input wd350', $ad_desc);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.text.width'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_text_width', 'backend-text-input wd50', $ad_width);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.text.height'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_adv_text_height', 'backend-text-input wd50', $ad_height);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.text.class'] . '</label>', 'left-float', 'backend_adv_text_class', 'backend-text-input wd350', $ad_class);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.adv.text.style'] . '</label>', 'left-float', 'backend_adv_text_style', 'backend-text-input wd350', $ad_style);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 icheck-box', '', '<input ' . ($ad_type == 1 ? 'checked="checked"' : null) . ' type="checkbox" class="cb-exclude" name="backend_adv_text_rotate" value="1" /><label>' . $language['backend.adv.text.rotate'] . '</label>'));
        $html .= '</div>'; //end vs-mask
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language['frontend.global.required.items']);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</form></div>';

        return $html;
    }
    /* processing group entry */
    public function processGroupEntry()
    {
        global $class_database, $db, $language;

        $form           = VArraySection::getArray("adv_groups");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $ad_descr  = $form[0]['adv_description'];
            $ad_width  = $form[0]['adv_width'];
            $ad_height = $form[0]['adv_height'];
            $ad_class  = $form[0]['adv_class'];
            $ad_style  = $form[0]['adv_style'];
            $ad_rot    = $form[0]['adv_rotate'];
            $ad_id     = intval($_POST['hc_id']);
            switch ($_GET['do']) {
                case "update":
                    $sql = sprintf("UPDATE `db_advgroups` SET `adv_description`='%s', `adv_width`='%s', `adv_height`='%s', `adv_class`='%s', `adv_style`='%s', `adv_rotate`='%s' WHERE `db_id`='%s' LIMIT 1;", $ad_descr, $ad_width, $ad_height, $ad_class, $ad_style, $ad_rot, $ad_id);
                    $db->execute($sql);
                    break;
            }

            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            }
        }
    }
}
