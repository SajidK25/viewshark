<?php
/*******************************************************************************************************************
| Software Name        : EasyStream
| Software Description : High End YouTube Clone Script with Videos, Shorts, Streams, Images, Audio, Documents, Blogs
| Software Author      : (c) Sami Ahmed
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the EasyStream Proprietary License Agreement.
| 
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2025 Sami Ahmed. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

class VbeStreaming
{
    /* main server details */
    public function mainServerDetails($_dsp = '', $entry_id = '', $db_id = '', $srv_state = '', $srv_name = '', $srv_slug = '', $srv_host = '', $srv_https = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            self::processEntry();

            $srv_name  = $class_filter->clr_str($_POST['frontend_global_name']);
            $srv_slug  = $class_filter->clr_str($_POST['frontend_global_slug']);
            $srv_host  = $class_filter->clr_str($_POST['backend_streaming_servers_host']);
            $srv_port  = (isset($_GET['s']) and ($_GET['s'] == 'backend-menu-entry14-sub2' or $_GET['s'] == 'backend-menu-entry14-sub3' or $_GET['s'] == 'backend-menu-entry14-sub5' or $_GET['s'] == 'backend-menu-entry14-sub6' or $_GET['s'] == 'backend-menu-entry14-sub7')) ? $class_filter->clr_str($_POST['backend_streaming_servers_port']) : null;
            $srv_https = (isset($_GET['s']) and ($_GET['s'] == 'backend-menu-entry14-sub2' or $_GET['s'] == 'backend-menu-entry14-sub3' or $_GET['s'] == 'backend-menu-entry14-sub5' or $_GET['s'] == 'backend-menu-entry14-sub6' or $_GET['s'] == 'backend-menu-entry14-sub7')) ? $class_filter->clr_str($_POST['backend_streaming_servers_ssl']) : null;
        }

        return self::serverDetails($_dsp, $entry_id, $db_id, $srv_state, $srv_name, $srv_slug, $srv_host, $srv_port, $srv_https);
    }
    /* main token details */
    public function mainTokenDetails($_dsp = '', $entry_id = '', $db_id = '', $tk_state = '', $tk_name = '', $tk_slug = '', $tk_amount = '', $tk_price = '', $tk_vat = '', $tk_shared = '', $tk_currency = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            self::processTokenEntry();

            $tk_name     = $class_filter->clr_str($_POST['backend_streaming_token_name']);
            $tk_slug     = $class_filter->clr_str($_POST['backend_streaming_token_slug']);
            $tk_amount   = $class_filter->clr_str($_POST['backend_streaming_token_amount']);
            $tk_price    = $class_filter->clr_str($_POST['backend_streaming_token_price']);
            $tk_currency = $class_filter->clr_str($_POST['backend_streaming_token_currency']);
            $tk_shared   = $class_filter->clr_str($_POST['backend_streaming_token_shared']);
            $tk_vat      = $class_filter->clr_str($_POST['backend_streaming_token_vat']);
        }

        return self::tokenDetails($_dsp, $entry_id, $db_id, $tk_state, $tk_name, $tk_slug, $tk_amount, $tk_price, $tk_vat, $tk_shared, $tk_currency);
    }
    /* server details edit */
    public function serverDetails($_dsp = '', $entry_id = '', $db_id = '', $srv_state = '', $srv_name = '', $srv_slug = '', $srv_host = '', $srv_port = '', $srv_https = '')
    {
        global $class_filter, $language, $cfg;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_sct  = 'discount_codes';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block;') : null;
        if ($_GET['do'] == 'add') {
            switch ($_GET['s']) {
                case "backend-menu-entry2-sub5l":$srv_type = 'live';
                    break;
                case "backend-menu-entry2-sub5v":$srv_type = 'video';
                    break;
                case "backend-menu-entry2-sub5s":$srv_type = 'short';
                    break;
                case "backend-menu-entry2-sub5i":$srv_type = 'image';
                    break;
                case "backend-menu-entry2-sub5a":$srv_type = 'audio';
                    break;
                case "backend-menu-entry2-sub5d":$srv_type = 'doc';
                    break;
                case "backend-menu-entry2-sub5c":$srv_type = 'channel';
                    break;
                case "backend-menu-entry2-sub5b":$srv_type = 'blog';
                    break;
                default:$srv_type = '';
                    $opt              = '<option value="live">' . $language['frontend.global.l'] . '</option><option value="video">' . $language['frontend.global.v'] . '</option><option value="short">' . $language['frontend.global.s'] . '</option><option value="image">' . $language['frontend.global.i'] . '</option><option value="audio">' . $language['frontend.global.a'] . '</option><option value="doc">' . $language['frontend.global.d'] . '</option><option value="blog">' . $language['frontend.global.b'] . '</option><option value="channel">' . $language['frontend.global.c'] . '</option>';
                    break;
            }
        }

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= '<div id="categ-details-' . (int) $db_id . '">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($srv_state == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.name'] . '</label>' . $language['frontend.global.required'], 'ct-name', 'frontend_global_name', 'backend-text-input wd350', $srv_name);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.slug'] . '</label>' . $language['frontend.global.required'], 'ct-name', 'frontend_global_slug', 'backend-text-input wd350', $srv_slug);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.servers.host'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_streaming_servers_host', 'backend-text-input wd350', $srv_host);
        if (isset($_GET['s']) and ($_GET['s'] == 'backend-menu-entry14-sub2' or $_GET['s'] == 'backend-menu-entry14-sub3' or $_GET['s'] == 'backend-menu-entry14-sub5' or $_GET['s'] == 'backend-menu-entry14-sub6' or $_GET['s'] == 'backend-menu-entry14-sub7')) {
//vod servers port
            $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.servers.port'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_streaming_servers_port', 'backend-text-input wd350', $srv_port);
            $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="backend_streaming_servers_ssl" class=""' . ($srv_https == 1 ? ' checked="checked"' : null) . '><label>' . $language['backend.streaming.servers.ssl'] . '</label>'));
        }
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language['frontend.global.required.items']);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '</form></div>';

        return $html;
    }
    /* token details edit */
    public function tokenDetails($_dsp = '', $entry_id = '', $db_id = '', $tk_state = '', $tk_name = '', $tk_slug = '', $tk_amount = '', $tk_price = '', $tk_vat = '', $tk_shared = '', $tk_currency = '')
    {
        global $class_filter, $language, $cfg;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_sct  = 'discount_codes';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $_btn      = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>')) : null;
        $sel_opts  = null;
        $_currency = explode(',', $language['supported_currency_names']);
        foreach ($_currency as $v) {
            $sel_opts .= '<option value="' . $v . '"' . (($v == $tk_currency or $tk_currency == '' and $v == 'USD') ? ' selected="selected"' : null) . '>' . $v . '</option>';
        }

        $ht_currency = VGenerate::simpleDivWrap('left-float lh20 selector', '', '<select name="backend_streaming_token_currency_' . (int) $db_id . '" class="select-input wd100">' . $sel_opts . '</select>');

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= '<div id="categ-details-' . (int) $db_id . '">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($tk_state == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.token.name'] . '</label>' . $language['frontend.global.required'], 'ct-name', 'backend_streaming_token_name', 'backend-text-input wd350', $tk_name);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.token.slug'] . '</label>' . $language['frontend.global.required'], 'ct-name', 'backend_streaming_token_slug', 'backend-text-input wd350', $tk_slug);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.token.amount'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_streaming_token_amount', 'backend-text-input wd350', $tk_amount);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.token.price'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_streaming_token_price', 'backend-text-input wd350', $tk_price);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="backend_streaming_token_vat" class=""' . ($tk_vat == 1 ? ' checked="checked"' : null) . '><label>' . $language['backend.streaming.token.vat'] . '</label>'));
        $html .= '<div class="no-display">' . VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.token.shared'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_streaming_token_shared', 'backend-text-input wd350', $tk_shared) . '</div>';

        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.streaming.token.currency'] . '</label>' . $language['frontend.global.required']) . $ht_currency);
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language['frontend.global.required.items']);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '</form></div>';

        $html .= VGenerate::declareJS('$(function(){SelectList.init("backend_streaming_token_currency_' . ((int) $db_id) . '");});');

        return $html;
    }
    /* processing entry */
    public function processEntry()
    {
        global $class_database, $db, $language, $cfg;

        $form           = VArraySection::getArray("live_streaming");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $srv_id   = intval($_POST['hc_id']);
            $srv_name = $form[0]['srv_name'];
            $srv_slug = $form[0]['srv_slug'];
            $srv_host = $form[0]['srv_host'];

            switch ($_GET['do']) {
                case "update":
                    $q = null;
                    if (isset($_GET['s']) and ($_GET['s'] == 'backend-menu-entry14-sub2' or $_GET['s'] == 'backend-menu-entry14-sub3' or $_GET['s'] == 'backend-menu-entry14-sub5' or $_GET['s'] == 'backend-menu-entry14-sub6' or $_GET['s'] == 'backend-menu-entry14-sub7')) {
                        $srv_port  = $form[0]['srv_port'];
                        $srv_https = $form[0]['srv_https'];

                        $q = sprintf("`srv_port`='%s', `srv_https`='%s',", $srv_port, $srv_https);
                    }
                    $sql = sprintf("UPDATE `db_liveservers` SET %s `srv_name`='%s', `srv_slug`='%s', `srv_host`='%s' WHERE `srv_id`='%s' LIMIT 1;", $q, $srv_name, $srv_slug, $srv_host, $srv_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_liveservers', $form[0]);
                    break;
            }
            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            }

        }
    }
    /* processing token entry */
    public function processTokenEntry()
    {
        global $class_database, $db, $language, $cfg;

        $form           = VArraySection::getArray("live_streaming_token");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $tk_id       = intval($_POST['hc_id']);
            $tk_name     = $form[0]['tk_name'];
            $tk_slug     = $form[0]['tk_slug'];
            $tk_amount   = $form[0]['tk_amount'];
            $tk_price    = $form[0]['tk_price'];
            $tk_currency = $form[0]['tk_currency'];
            $tk_shared   = $form[0]['tk_shared'];
            $tk_vat      = $form[0]['tk_vat'];

            switch ($_GET['do']) {
                case "update":
                    $q   = null;
                    $sql = sprintf("UPDATE `db_livetoken` SET %s `tk_name`='%s', `tk_slug`='%s', `tk_amount`='%s', `tk_price`='%s', `tk_currency`='%s', `tk_shared`='%s', `tk_vat`='%s' WHERE `tk_id`='%s' LIMIT 1;", $q, $tk_name, $tk_slug, $tk_amount, $tk_price, $tk_currency, $tk_shared, $tk_vat, $tk_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_livetoken', $form[0]);
                    break;
            }

            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            }
        }
    }
}
