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

class VbeComments
{
    /* main category details */
    public function mainCommentDetails($_dsp = '', $entry_id = '', $db_id = '', $file_key = '', $c_usr_id = '', $c_key = '', $c_body = '', $c_datetime = '', $c_approved = '', $c_active = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            self::processEntry();

            $this_file_type = "this_file_type_" . ((int) $db_id);
            $c_body         = $class_filter->clr_str($_POST['c_body']);
        }

        return self::commentDetails($_dsp, $entry_id, $db_id, $file_key, $c_usr_id, $c_key, $c_body, $c_datetime, $c_approved, $c_active);
    }
    /* comment details edit */
    public function commentDetails($_dsp = '', $entry_id = '', $db_id = '', $file_key = '', $c_usr_id = '', $c_key = '', $c_body = '', $c_datetime = '', $c_approved = '', $c_active = '')
    {
        global $class_filter, $language, $class_database, $backend_access_url;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_sct  = 'discount_codes';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block-off;') : null;

        switch ($_GET['s']) {
            case "backend-menu-entry16-sub1":$ct_type = 'video';
                break;
            case "backend-menu-entry16-sub8":$ct_type = 'short';
                break;
            case "backend-menu-entry16-sub2":$ct_type = 'live';
                break;
            case "backend-menu-entry16-sub3":$ct_type = 'image';
                break;
            case "backend-menu-entry16-sub4":$ct_type = 'audio';
                break;
            case "backend-menu-entry16-sub5":$ct_type = 'doc';
                break;
            case "backend-menu-entry16-sub6":$ct_type = 'blog';
                break;
            case "backend-menu-entry16-sub7":$ct_type = 'channel';
                break;
            default:$ct_type = 'video';
                $opt             = '<option value="live">' . $language['frontend.global.l'] . '</option><option value="video">' . $language['frontend.global.v'] . '</option><option value="image">' . $language['frontend.global.i'] . '</option><option value="audio">' . $language['frontend.global.a'] . '</option><option value="doc">' . $language['frontend.global.d'] . '</option><option value="blog">' . $language['frontend.global.b'] . '</option><option value="channel">' . $language['frontend.global.c'] . '</option>';
                break;
        }

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));

        if ($ct_type == 'channel') {
            $title = $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_id', $file_key);
            $title = '<a href="' . $cfg['main_url'] . '/' . $backend_access_url . '/' . VHref::getKey("be_members") . '?u=' . $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_id', $file_key) . '" target="_blank">' . $title . '</a>';
        } else {
            $title = $class_database->singleFieldValue('db_' . $ct_type . 'files', 'file_title', 'file_key', $file_key);
            $title = '<a href="' . $cfg['main_url'] . '/' . $backend_access_url . '/' . VHref::getKey("be_files") . '?k=' . $ct_type[0] . $file_key . '" target="_blank">' . $title . '</a>';
        }
        $html .= '<div id="comment-details-' . (int) $db_id . '">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($c_active == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 place-left', '', '<label>' . $language['frontend.global.edate'] . '</label> ' . VUserinfo::timeRange($c_datetime)));
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 place-left', '', '<label>' . $language["frontend.global." . $ct_type[0] . ".c"] . '</label> ' . $title));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= '<div class="no-display-off">' . VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . ucfirst($language['frontend.global.file.comment']) . '</label>', 'left-float', 'c_body', 'backend-textarea-input wd350', $c_body) . '</div>';
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '</form></div>';

        return $html;
    }
    /* processing entry */
    public function processEntry()
    {
        global $class_database, $db, $language;

        $form           = VArraySection::getArray("file_comments");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            switch ($_GET['s']) {
                case "backend-menu-entry16-sub1":$db_type = 'video';
                    break;
                case "backend-menu-entry16-sub8":$db_type = 'short';
                    break;
                case "backend-menu-entry16-sub2":$db_type = 'live';
                    break;
                case "backend-menu-entry16-sub3":$db_type = 'image';
                    break;
                case "backend-menu-entry16-sub4":$db_type = 'audio';
                    break;
                case "backend-menu-entry16-sub5":$db_type = 'doc';
                    break;
                case "backend-menu-entry16-sub6":$db_type = 'blog';
                    break;
                case "backend-menu-entry16-sub7":$db_type = 'channel';
                    break;
            }

            $c_id   = intval($_POST['hc_id']);
            $c_body = $form[0]['c_body'];

            switch ($_GET['do']) {
                case "update":
                    $sql = sprintf("UPDATE `db_%scomments` SET `c_body`='%s' WHERE `c_id`='%s' LIMIT 1;", $db_type, $c_body, $c_id);
                    $db->execute($sql);
                    break;
                case "add":
                    break;
            }

            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            }
        }
    }
}
