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
| Copyright (c) 2013-2020 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

class VbeToken
{
    /* token purchase details */
    public function tokenDetails($_dsp = '', $entry_id = '', $db_id = '', $usr_id = '', $tk_id = '', $tk_amount = '', $tk_price = '', $tk_date = '', $txn_id = '', $txn_receipt = '', $tk_active = '')
    {
        global $class_filter, $language, $cfg;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_sct  = 'token_purchase';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $user_data = VUserinfo::getUserInfo($usr_id);

        $html = '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= '<div id="categ-details-' . (int) $db_id . '">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($tk_active == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['backend.menu.token.pby'] . '</label>' . '<span class="conf-green"><a href="' . VHref::getKey('be_members') . '?u=' . $user_data['key'] . '">' . $user_data['uname'] . '</a></span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['backend.menu.token.pdate'] . '</label>' . '<span class="conf-green">' . $tk_date . '</span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['backend.streaming.token.amount'] . '</label>' . '<span class="conf-green">' . $tk_amount . '</span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['backend.menu.token.ptotal'] . '</label>' . '<span class="conf-green">$' . $tk_price . '</span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['backend.menu.token.preceipt'] . '</label>' . '<span><pre>' . urldecode($txn_receipt) . '</pre></span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '</form></div>';

        return $html;
    }
    /* token donation details */
    public function tokenDonationDetails($_dsp = '', $entry_id = '', $db_id = '', $tk_from = '', $tk_to = '', $tk_from_user = '', $tk_to_user = '', $tk_amount = '', $tk_date = '', $tk_active = '')
    {
        global $class_filter, $language, $cfg;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_sct  = 'token_purchase';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $user_data_from = VUserinfo::getUserInfo($tk_from);
        $user_data_to   = VUserinfo::getUserInfo($tk_to);

        $html = '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= '<div id="categ-details-' . (int) $db_id . '">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($tk_active == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>From</label>' . '<span class="conf-green"><a href="' . VHref::getKey('be_members') . '?u=' . $user_data_from['key'] . '">' . $user_data_from['uname'] . '</a></span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>To</label>' . '<span class="conf-green"><a href="' . VHref::getKey('be_members') . '?u=' . $user_data_to['key'] . '">' . $user_data_to['uname'] . '</a></span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['backend.menu.token.ddate'] . '</label>' . '<span class="conf-green">' . $tk_date . '</span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['backend.streaming.token.amount'] . '</label>' . '<span class="conf-green">' . $tk_amount . '</span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '</form></div>';

        return $html;
    }
    /* processing token entry */
    public function processTokenEntry()
    {
        global $class_database, $db, $language, $cfg;

        $form           = VArraySection::getArray("token_purchase");
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

            switch ($_GET['do']) {
                case "update":
                    $q   = null;
                    $sql = sprintf("UPDATE `db_livetoken` SET %s `tk_name`='%s', `tk_slug`='%s', `tk_amount`='%s', `tk_price`='%s', `tk_currency`='%s' WHERE `tk_id`='%s' LIMIT 1;", $q, $tk_name, $tk_slug, $tk_amount, $tk_price, $tk_currency, $tk_id);
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
