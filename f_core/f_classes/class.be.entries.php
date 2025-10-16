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

class VbeEntries
{
    /* update an entry and keep its div wrappers open */
    public function entryInit($display, $db_id, $entry_id)
    {

        $display = (($_GET['do'] == 'update' or $_GET['fdo'] == 'update') and $_POST['hc_id'] == $db_id) ? 'block' : $display;
        $results = array($display, $do_js);

        return $results;
    }
    /* get post id for enable/disable/delete */
    public function getPostID()
    {
        global $class_filter;

        $post_id = $class_filter->clr_str($_POST['section_entry_value']);
        return intval(substr(strstr($post_id, 'del'), 3));
    }
    /* add a new entry */
    public function addNew($section, $db_table)
    {
        global $class_filter, $class_database;

        $insert_array = VArraySection::getArray($section);
        if ($class_database->doInsert($db_table, $insert_array[0])) {
            return true;
        } else {
            return false;
        }

    }
    /* update/edit current entry */
    public function updateCurrent($section, $db_id, $db_src_table, $db_whr_field)
    {
        global $class_filter, $class_database;

        $entry_array = VArraySection::getArray($section);
        $process     = self::processEntry($section, 'update', $db_id);

        if ($_POST['hc_id'] == $db_id and $process[0] == '') {
            switch ($db_src_table) {
                case "db_packdiscounts":$update_array = VArraySection::arrayRemoveKey($entry_array[0], 'dc_date');
                    break;
                case "db_packtypes":
                case "db_subtypes":
                case "db_usertypes":
                case "db_advgroups":$update_array = $entry_array[0];
                    break;
            }
            if ($class_database->doUpdate($db_src_table, $db_whr_field, $update_array)) {
                return true;
            } else {
                return false;
            }

        }
    }
    /* build query for checkbox selection */
    public function buildQ($count, $db_whr_field)
    {
        for ($i = 0; $i < $count; $i++) {
            if (($_GET['s'] != 'backend-menu-entry5-sub2' and $_GET['s'] != 'backend-menu-entry2-sub16') or (($_GET['s'] == 'backend-menu-entry5-sub2' and intval($_POST['current_entry_id'][$i]) != 1) or ($_GET['s'] == 'backend-menu-entry2-sub16' and intval($_POST['current_entry_id'][$i]) != 1))) {
                $q .= "`" . $db_whr_field . "` = '" . intval($_POST['current_entry_id'][$i]) . "' OR ";
            }
        }
        return substr($q, 0, -3);
    }
    /* numeric checks on array */
    public function intCheck($array, $nullcheck = '')
    {
        foreach ($array as $val) {
            if ($nullcheck == 1 and intval($_POST[$val]) < 1) {
                return false;
                break;}
            if (!is_numeric($_POST[$val])) {
                return false;
                break;}
        }
        return true;
    }
    /* entry processing */
    public function processEntry($section, $action, $db_id = '')
    {
        global $smarty, $language;

        $form_fields    = VArraySection::getArray($section);
        $allowedFields  = $form_fields[1];
        $requiredFields = $form_fields[2];
        $error_message  = ($action != 'delete' and $section != 'personal_messages') ? VForm::checkEmptyFields($allowedFields, $requiredFields) : null;

        switch ($section) {
            case "adv_groups":
                $db_table = 'db_advgroups';
                break;
            case "channel_types":
                $db_table = 'db_usertypes';
                break;
            case "up_servers":
                $db_table = 'db_servers';
                break;
            case "membership_types":
                $db_table                                      = 'db_packtypes';
                $entryid                                       = (int) $_POST['hc_id'];
                $backend_menu_members_entry1_sub2_entry_period = "backend_menu_members_entry1_sub2_entry_period_" . $entryid;
                $frontend_global_days                          = "frontend_global_days_" . $entryid;
                $error_message                                 = ($action != 'delete' and $error_message == '' and intval($_POST[$backend_menu_members_entry1_sub2_entry_period]) == 0 and intval($_POST[$frontend_global_days]) < 1) ? $language['notif.error.invalid.request'] : $error_message;
                break;
            case "subscription_types":
                $db_table                                      = 'db_subtypes';
                $entryid                                       = (int) $_POST['hc_id'];
                $backend_menu_members_entry1_sub2_entry_period = "backend_menu_members_entry1_sub2_entry_period_" . $entryid;
                $frontend_global_days                          = "frontend_global_days_" . $entryid;
                $error_message                                 = ($action != 'delete' and $error_message == '' and intval($_POST[$backend_menu_members_entry1_sub2_entry_period]) == 0 and intval($_POST[$frontend_global_days]) < 1) ? $language['notif.error.invalid.request'] : $error_message;
                break;
            case "discount_codes":
                $db_table      = 'db_packdiscounts';
                $error_message = ($action != 'delete' and $error_message == '' and !self::intCheck(VArraySection::arrayRemoveKey($requiredFields, 0), 1)) ? $language['notif.error.invalid.request'] : $error_message;
                break;
        }

        switch ($action) {
            case "add":
                $show_notice    = 1;
                $notice_message = ($error_message == '' and self::addNew($section, $db_table)) ? $language['notif.success.request'] : null;
                break;
            case "update":
                $show_notice    = ($_POST['hc_id'] == $db_id) ? 1 : 0;
                $notice_message = ($error_message == '' and $_POST['hc_id'] == $db_id) ? $language['notif.success.request'] : null;
                break;
            case "delete":
                $show_notice    = 1;
                $notice_message = ($error_message == '') ? $language['notif.success.request'] : null;
                break;
            case "notice":
                $show_notice    = 1;
                $notice_message = ($error_message == '') ? $language['notif.success.request'] : null;
                break;
            case "cb-enable":
            case "cb-disable":
            case "cb-approve":
            case "cb-delete":
            case "cb-label-add":
            case "cb-label-clear":
                $show_notice    = (isset($_POST)) ? 1 : 0;
                $error_message  = ((isset($_POST['current_entry_id']) and count($_POST['current_entry_id']) < 1) or !isset($_POST['current_entry_id'])) ? ($section == 'personal_messages' ? $language['notif.no.messages.select'] : $language['notif.no.multiple.select']) : null;
                $notice_message = ($_POST and $error_message == '') ? $language['notif.success.request'] : null;
                break;
        }

        switch ($error_message) {
            case "":break;
            default:$smarty->assign('error_message', $error_message);
                break;
        }

        switch ($notice_message) {
            case "":break;
            default:$smarty->assign('notice_message', $notice_message);
                break;
        }

        if ($show_notice == 1 and ($notice_message != '' or $error_message != '')) {
            return array($error_message, $notice_message, VGenerate::noticeTpl('', $error_message, $notice_message));
        }
    }
    /* entry actions */
    public function entryActions($section, $action_type, $db_src_table = '', $db_set_field = '', $db_whr_field = '', $db_whr_value = '')
    {
        global $cfg, $db, $class_filter, $class_database;

        $upload_in = $cfg['player_dir'] . '/ad_files/';

        switch ($action_type) {
            case "enable":$q = $db->execute(sprintf("UPDATE `%s` SET `%s`='1' WHERE `%s`='%s' LIMIT 1;", $db_src_table, $db_set_field, $db_whr_field, $db_whr_value));
                $lb              = $section == 'personal_messages' ? VMessages::subLabelTotal($db_whr_value) : null;
                break;
            case "disable":$q = $db->execute(sprintf("UPDATE `%s` SET `%s`='0' WHERE `%s`='%s' %s LIMIT 1;", $db_src_table, $db_set_field, $db_whr_field, $db_whr_value, (($section == 'personal_messages' and $_GET['s'] != 'message-menu-entry3') ? "AND `msg_from`!='" . intval($_SESSION['USER_ID']) . "'" : null)));
                $lb               = ($section == 'personal_messages' and VMessages::getMessageInfo('msg_from', $db_whr_value) != $_SESSION['USER_ID']) ? VMessages::subLabelTotal($db_whr_value) : null;
                break;
            case "cb-approve":
                $html = self::processEntry($section, $action_type);

                if ($html[0] == '') {
                    foreach ($_POST['current_entry_id'] as $db_id) {
                        $db_q    = $db->execute(sprintf("SELECT `msg_body` FROM `db_messaging` WHERE `msg_id`='%s' AND `msg_invite`='1' LIMIT 1;", $db_id));
                        $inv_key = substr(substr(strstr($db_q->fields['msg_body'], "value"), 7), 0, -4);
                        $inv_app = VContacts::inviteApprove($inv_key, $db_id);
                    }
                }
                VGenerate::noticeWrap($html);
                break;
            case "delete_message":
                switch (VMessages::currentMenuEntry($_GET['s'])) {
                    case "message-menu-entry2":$db_set_field = 'msg_inbox_deleted';
                        break;
                    case "message-menu-entry5":$db_set_field = 'msg_outbox_deleted';
                        break;
                    case "message-menu-entry6":$db_set_field = 'msg_active_deleted';
                        break;
                }
                $q    = $db->execute(sprintf("UPDATE `%s` SET `%s`='1' WHERE `%s`='%s' LIMIT 1;", $db_src_table, $db_set_field, $db_whr_field, $db_whr_value));
                $html = ($db->Affected_Rows() > 0) ? VGenerate::noticeWrap(self::processEntry($section, 'delete')) : null;
                VMessages::labelTotal();
                VMessages::subLabelTotal(intval($db_whr_value));

                break;
            case "delete":
                if ($_GET['s'] == 'backend-menu-entry5-sub2' and $db_whr_value == 1) {
//dont allow deleting default member account
                    global $language;
                    echo $html .= VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $language['backend.menu.members.entry2.sub2.del.default'], '')));
                } elseif ($_GET['s'] == 'backend-menu-entry2-sub16' and $db_whr_value == 1) {
//dont allow deleting main english language
                    global $language;
                    echo $html .= VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $language['backend.menu.entry1.sub10.lang.del'], '')));
                } else {
                    if ($_GET['s'] == 'backend-menu-entry8-sub2') {
//get jw ad file name
                        $f = $db->execute(sprintf("SELECT `db_code` FROM `db_jwadcodes` WHERE `db_type`='file' AND `db_id`='%s' LIMIT 1;", $db_whr_value));
                        $c = $f->fields['db_code'];
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub14') {
//get video server ids for deleting xfer
                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_videotransfers` WHERE `q_id`='%s' LIMIT 1;", $db_whr_value));
                        $c = $f->fields['file_key'];
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub19') {
//get short server ids for deleting xfer
                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_shorttransfers` WHERE `q_id`='%s' LIMIT 1;", $db_whr_value));
                        $c = $f->fields['file_key'];
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub15') {
//get image server ids for deleting xfer
                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_imagetransfers` WHERE `q_id`='%s' LIMIT 1;", $db_whr_value));
                        $c = $f->fields['file_key'];
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub16') {
//get audio server ids for deleting xfer
                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_audiotransfers` WHERE `q_id`='%s' LIMIT 1;", $db_whr_value));
                        $c = $f->fields['file_key'];
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub17') {
//get doc server ids for deleting xfer
                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_doctransfers` WHERE `q_id`='%s' LIMIT 1;", $db_whr_value));
                        $c = $f->fields['file_key'];
                    }

                    $_ld = $class_database->singleFieldValue('db_languages', 'lang_id', 'db_id', $db_whr_value);
                    $q   = $db->execute(sprintf("DELETE FROM `%s` WHERE `%s`='%s' LIMIT 1;", $db_src_table, $db_whr_field, $db_whr_value));
                    $ar  = $db->Affected_Rows();

                    if ($_GET['s'] == 'backend-menu-entry2-sub16' and $ar > 0) {
//when deleting language, rename language folder
                        $src = $cfg['language_dir'] . '/' . $_ld;
                        $dst = $cfg['language_dir'] . '/' . $_ld . '-deleted-' . date("Y.m.d_H.i.s");
                        rename($src, $dst);
                    } elseif ($_GET['s'] == 'backend-menu-entry8-sub2' and $ar > 0) {
//delete jw ad file
                        if ($c != '' and file_exists($upload_in . $c)) {
                            @unlink($upload_in . $c);
                        }
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub14' and $ar > 0) {
//reset video server ids when deleting xfer
                        VbeServers::remoteDelete($c, 'video');
                        $r = $db->execute(sprintf("UPDATE `db_videofiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $c));
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub19' and $ar > 0) {
//reset short server ids when deleting xfer
                        VbeServers::remoteDelete($c, 'short');
                        $r = $db->execute(sprintf("UPDATE `db_shortfiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $c));
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub15' and $ar > 0) {
//reset image server ids when deleting xfer
                        VbeServers::remoteDelete($c, 'image');
                        $r = $db->execute(sprintf("UPDATE `db_imagefiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $c));
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub16' and $ar > 0) {
//reset audio server ids when deleting xfer
                        VbeServers::remoteDelete($c, 'audio');
                        $r = $db->execute(sprintf("UPDATE `db_audiofiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $c));
                    } elseif ($_GET['s'] == 'backend-menu-entry3-sub17' and $ar > 0) {
//reset doc server ids when deleting xfer
                        VbeServers::remoteDelete($c, 'doc');
                        $r = $db->execute(sprintf("UPDATE `db_docfiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $c));
                    }

                    $html = ($ar > 0) ? VGenerate::noticeWrap(self::processEntry($section, $action_type)) : null;
                }
                break;
            case "notice":
                $not = VGenerate::noticeWrap(self::processEntry($section, $action_type));
                break;
            case "cb-label-add":
            case "cb-label-clear":
                $count = $db_whr_value;
                $html  = self::processEntry($section, $action_type);

                switch ($html[0]) {
                    case "":
                        VMessages::addToLabelActions($action_type, $count);
                        echo VGenerate::declareJS("wrapLoad(current_url + menu_section + '?s=" . $class_filter->clr_str($_GET['s']) . "&do=notice', 'fe_mask');");
                    default:VGenerate::noticeWrap($html);
                        break;
                }
                break;
            case "cb-enable":
            case "cb-disable":
            case "cb-delete":
                $count = $db_whr_value;
                $html  = self::processEntry($section, $action_type);

                switch ($html[0]) {
                    case "":
                        if ($section == 'personal_messages' and $action_type == 'cb-delete') {
                            switch (VMessages::currentMenuEntry($_GET['s'])) {
                                case "message-menu-entry2":$db_set_field = 'msg_inbox_deleted';
                                    $del_db                                  = 0;
                                    break;
                                case "message-menu-entry5":$db_set_field = 'msg_outbox_deleted';
                                    $del_db                                  = 0;
                                    break;
                                case "message-menu-entry6":$db_set_field = 'msg_active_deleted';
                                    $del_db                                  = 0;
                                    break;
                                case "message-menu-entry3":
                                case "message-menu-entry4":$del_db = 1;
                                    break;
                            }
                            switch ($del_db) {
                                case "0":$db->execute(sprintf("UPDATE `%s` SET `%s`='1' WHERE " . self::buildQ($count, $db_whr_field) . " LIMIT " . $count . ";", $db_src_table, $db_set_field));
                                    break;
                                case "1":
                                    if ($db_whr_value > 0) {
                                        foreach ($_POST['current_entry_id'] as $db_id) {
                                            $db->execute(sprintf("DELETE FROM `%s` WHERE `%s`='%s' LIMIT 1;", $db_src_table, $db_whr_field, $db_id));
                                        }
                                    }
                                    break;
                            }
                        } else {
                            if ($_GET['s'] == 'backend-menu-entry2-sub16' and $action_type == 'cb-delete') {
//when deleting language, rename language folder
                                if ($db_whr_value > 0) {
                                    foreach ($_POST['current_entry_id'] as $db_id) {
                                        if ($db_id != 1) {
                                            $_ld = $class_database->singleFieldValue('db_languages', 'lang_id', 'db_id', $db_id);
                                            $src = $cfg['language_dir'] . '/' . $_ld;
                                            $dst = $cfg['language_dir'] . '/' . $_ld . '-deleted-' . date("Y.m.d_H.i.s");
                                            rename($src, $dst);
                                        }
                                    }
                                }
                            }
                            if ($_GET['s'] == 'backend-menu-entry8-sub2' and $action_type == 'cb-delete') {
//delete jw player ad files
                                if ($db_whr_value > 0) {
                                    foreach ($_POST['current_entry_id'] as $db_id) {
                                        $f = $db->execute(sprintf("SELECT `db_code` FROM `db_jwadcodes` WHERE `db_type`='file' AND `db_id`='%s' LIMIT 1;", $db_id));

                                        if ($f->fields['db_code'] != '' and file_exists($upload_in . $f->fields['db_code'])) {
                                            @unlink($upload_in . $f->fields['db_code']);
                                        }
                                    }
                                }
                            }
                            if ($_GET['s'] == 'backend-menu-entry3-sub14' and $action_type == 'cb-delete') {
//reset video server ids when deleting xfer
                                if ($db_whr_value > 0) {
                                    foreach ($_POST['current_entry_id'] as $db_id) {
                                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_videotransfers` WHERE `q_id`='%s' LIMIT 1;", $db_id));
                                        VbeServers::remoteDelete($f->fields['file_key'], 'video');
                                        $r = $db->execute(sprintf("UPDATE `db_videofiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $f->fields['file_key']));
                                    }
                                }
                            }
                            if ($_GET['s'] == 'backend-menu-entry3-sub19' and $action_type == 'cb-delete') {
//reset short server ids when deleting xfer
                                if ($db_whr_value > 0) {
                                    foreach ($_POST['current_entry_id'] as $db_id) {
                                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_shorttransfers` WHERE `q_id`='%s' LIMIT 1;", $db_id));
                                        VbeServers::remoteDelete($f->fields['file_key'], 'short');
                                        $r = $db->execute(sprintf("UPDATE `db_shortfiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $f->fields['file_key']));
                                    }
                                }
                            }
                            if ($_GET['s'] == 'backend-menu-entry3-sub15' and $action_type == 'cb-delete') {
//reset image server ids when deleting xfer
                                if ($db_whr_value > 0) {
                                    foreach ($_POST['current_entry_id'] as $db_id) {
                                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_imagetransfers` WHERE `q_id`='%s' LIMIT 1;", $db_id));
                                        VbeServers::remoteDelete($f->fields['file_key'], 'image');
                                        $r = $db->execute(sprintf("UPDATE `db_imagefiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $f->fields['file_key']));
                                    }
                                }
                            }
                            if ($_GET['s'] == 'backend-menu-entry3-sub16' and $action_type == 'cb-delete') {
//reset audio server ids when deleting xfer
                                if ($db_whr_value > 0) {
                                    foreach ($_POST['current_entry_id'] as $db_id) {
                                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_audiotransfers` WHERE `q_id`='%s' LIMIT 1;", $db_id));
                                        VbeServers::remoteDelete($f->fields['file_key'], 'audio');
                                        $r = $db->execute(sprintf("UPDATE `db_audiofiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $f->fields['file_key']));
                                    }
                                }
                            }
                            if ($_GET['s'] == 'backend-menu-entry3-sub17' and $action_type == 'cb-delete') {
//reset doc server ids when deleting xfer
                                if ($db_whr_value > 0) {
                                    foreach ($_POST['current_entry_id'] as $db_id) {
                                        $f = $db->execute(sprintf("SELECT `file_key` FROM `db_doctransfers` WHERE `q_id`='%s' LIMIT 1;", $db_id));
                                        VbeServers::remoteDelete($f->fields['file_key'], 'doc');
                                        $r = $db->execute(sprintf("UPDATE `db_docfiles` SET `upload_server`='0', `thumb_server`='0' WHERE `file_key`='%s' LIMIT 1;", $f->fields['file_key']));
                                    }
                                }
                            }

                            $act = $action_type == 'cb-enable' ? 1 : 0;
                            $query .= ($action_type != 'cb-delete') ? 'UPDATE `' . $db_src_table . '` SET `' . $db_set_field . '`=\'' . $act . '\' WHERE (' . self::buildQ($count, $db_whr_field) . ') ' . (($section == 'personal_messages' and $action_type == 'cb-disable' and $_GET['s'] != 'message-menu-entry3') ? 'AND `msg_from`!=\'' . intval($_SESSION['USER_ID']) . '\'' : null) . ' LIMIT ' . $count . ";" : 'DELETE FROM `' . $db_src_table . '` WHERE ' . self::buildQ($count, $db_whr_field) . ' LIMIT ' . $count . '; ';
                            $query_r = $db->execute($query);
                            $q1      = $db->Affected_Rows();
                            $query1  = $section == 'channel_types' ? $db->execute('UPDATE `db_accountuser` SET `ch_type`=\'0\', `ch_custom_fields`=\'\' WHERE (' . self::buildQ($count, 'ch_type') . ');') : null;
                            $q2      = $db->Affected_Rows();
                        }
                        echo $html != '' ? VGenerate::noticeWrap($html) : null;

                        if ($section == 'personal_messages') {
                            for ($i = 0; $i < $count; $i++) {
                                $cid = intval($_POST['current_entry_id'][$i]);
                                $sub = ($action_type == 'cb-disable' and VMessages::getMessageInfo('msg_from', $cid) != $_SESSION['USER_ID']) ? VMessages::subLabelTotal(intval($_POST['current_entry_id'][$i])) : ($action_type != 'cb-disable' ? VMessages::subLabelTotal(intval($_POST['current_entry_id'][$i])) : null);
                            }
                        }
                        break;
                    default:echo VGenerate::noticeWrap($html);
                        break;
                }
                break;
        }
    }
    /* list/manage section entries */
    public function listEntries($section, $bullet_id, $entry_id, $bottom_border)
    {
        global $db, $language, $class_filter, $cfg, $smarty;

        $do     = 1;
        $be     = 0;
        $bb     = $bottom_border == 1 ? 'bottom-border' : ($bottom_border == 2 ? 'double-bottom-border' : null);
        $_pk_id = $_GET['id'] != '' ? substr(strstr(strstr($class_filter->clr_str($_GET['id']), 'details'), '-'), 1) : null;

        switch ($section) {
            case "be_xfer_video": //video transfers
                global $class_database;

                $entry_section = 'be_xfer_video';
                $db_src_table  = 'db_videotransfers';
                $db_set_field  = 'active';
                $db_whr_field  = 'q_id';
                $db_nam_field  = 'file_key';
                $db_dsc_field  = 'state';
                $db_add_query  = '';
                $db_order      = 'DESC';
                $del_action    = 'delete';
                $pg_cfg        = 'page_be_xfers';
                $be            = 1;
                break;
            case "be_xfer_short": //short transfers
                global $class_database;

                $entry_section = 'be_xfer_short';
                $db_src_table  = 'db_shorttransfers';
                $db_set_field  = 'active';
                $db_whr_field  = 'q_id';
                $db_nam_field  = 'file_key';
                $db_dsc_field  = 'state';
                $db_add_query  = '';
                $db_order      = 'DESC';
                $del_action    = 'delete';
                $pg_cfg        = 'page_be_xfers';
                $be            = 1;
                break;
            case "be_xfer_image": //image transfers
                global $class_database;

                $entry_section = 'be_xfer_image';
                $db_src_table  = 'db_imagetransfers';
                $db_set_field  = 'active';
                $db_whr_field  = 'q_id';
                $db_nam_field  = 'file_key';
                $db_dsc_field  = 'state';
                $db_add_query  = '';
                $db_order      = 'DESC';
                $del_action    = 'delete';
                $pg_cfg        = 'page_be_xfers';
                $be            = 1;
                break;
            case "be_xfer_audio": //audio transfers
                global $class_database;

                $entry_section = 'be_xfer_audio';
                $db_src_table  = 'db_audiotransfers';
                $db_set_field  = 'active';
                $db_whr_field  = 'q_id';
                $db_nam_field  = 'file_key';
                $db_dsc_field  = 'state';
                $db_add_query  = '';
                $db_order      = 'DESC';
                $del_action    = 'delete';
                $pg_cfg        = 'page_be_xfers';
                $be            = 1;
                break;
            case "be_xfer_doc": //doc transfers
                global $class_database;

                $entry_section = 'be_xfer_doc';
                $db_src_table  = 'db_doctransfers';
                $db_set_field  = 'active';
                $db_whr_field  = 'q_id';
                $db_nam_field  = 'file_key';
                $db_dsc_field  = 'state';
                $db_add_query  = '';
                $db_order      = 'DESC';
                $del_action    = 'delete';
                $pg_cfg        = 10;
                $be            = 1;
                break;
            case "be_servers": //upload servers
                $entry_section = 'up_servers';
                $db_src_table  = 'db_servers';
                $db_set_field  = 'status';
                $db_whr_field  = 'server_id';
                $db_nam_field  = 'server_name';
                $db_dsc_field  = 'ftp_host';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg_cfg        = 'page_be_xfers';
                $be            = 1;
                break;
            case "vjs_ads": //videojs ads
                $entry_section = $section;
                $db_src_table  = 'db_vjsadentries';
                $db_set_field  = 'ad_active';
                $db_whr_field  = 'ad_id';
                $db_nam_field  = 'ad_name';
                $db_dsc_field  = 'ad_key';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_jw_ads';
                $be            = 1;
                break;
            case "jw_ads": //jwplayer ads
                $entry_section = $section;
                $db_src_table  = 'db_jwadentries';
                $db_set_field  = 'ad_active';
                $db_whr_field  = 'ad_id';
                $db_nam_field  = 'ad_name';
                $db_dsc_field  = 'ad_key';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_jw_ads';
                $be            = 1;
                break;
            case "fp_ads": //flowplayer ads
                $entry_section = $section;
                $db_src_table  = 'db_fpadentries';
                $db_set_field  = 'ad_active';
                $db_whr_field  = 'ad_id';
                $db_nam_field  = 'ad_name';
                $db_dsc_field  = 'ad_key';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_fp_ads';
                $be            = 1;
                break;
            case "jw_files": //jwplayer files
                $entry_section = $section;
                $db_src_table  = 'db_jwadcodes';
                $db_set_field  = 'db_active';
                $db_whr_field  = 'db_id';
                $db_nam_field  = 'db_name';
                $db_dsc_field  = 'db_key';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_jw_codes';
                $be            = 1;
                break;
            case "ch_categ": //public categories
                $entry_section = 'public_categories';
                $db_src_table  = 'db_categories';
                $db_set_field  = 'ct_active';
                $db_whr_field  = 'ct_id';
                $db_nam_field  = 'ct_name';
                $db_dsc_field  = 'ct_descr';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_public_categories';
                $be            = 1;

                switch ($_GET['s']) {
                    case "backend-menu-entry2-sub5l":$db_add_query = "WHERE `ct_type`='live'";
                        break;
                    case "backend-menu-entry2-sub5v":$db_add_query = "WHERE `ct_type`='video'";
                        break;
                    case "backend-menu-entry2-sub5i":$db_add_query = "WHERE `ct_type`='image'";
                        break;
                    case "backend-menu-entry2-sub5a":$db_add_query = "WHERE `ct_type`='audio'";
                        break;
                    case "backend-menu-entry2-sub5d":$db_add_query = "WHERE `ct_type`='doc'";
                        break;
                    case "backend-menu-entry2-sub5c":$db_add_query = "WHERE `ct_type`='channel'";
                        break;
                    case "backend-menu-entry2-sub5b":$db_add_query = "WHERE `ct_type`='blog'";
                        break;
                }
                $languages = $db->execute("SELECT `db_id`, `lang_id`, `lang_name` FROM `db_languages` WHERE `lang_active`='1' ORDER BY `lang_default` DESC;");
                break;
            case "be_live_streaming": //live streaming
                $entry_section = $section;
                $db_src_table  = 'db_liveservers';
                $db_set_field  = 'srv_active';
                $db_whr_field  = 'srv_id';
                $db_nam_field  = 'srv_name';
                $db_dsc_field  = 'srv_host';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_public_categories';
                $be            = 1;

                switch ($_GET['s']) {
                    case "backend-menu-entry14-sub1":$db_add_query = "WHERE `srv_type`='cast'";
                        break;
                    case "backend-menu-entry14-sub2":$db_add_query = "WHERE `srv_type`='stream'";
                        break;
                    case "backend-menu-entry14-sub3":$db_add_query = "WHERE `srv_type`='vod'";
                        break;
                    case "backend-menu-entry14-sub5":$db_add_query = "WHERE `srv_type`='lbb'";
                        break;
                    case "backend-menu-entry14-sub6":$db_add_query = "WHERE `srv_type`='lbs'";
                        break;
                    case "backend-menu-entry14-sub7":$db_add_query = "WHERE `srv_type`='chat'";
                        break;
                }
                break;
            case "be_file_comments": //manage comments
                global $class_database, $backend_access_url;

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

                $entry_section = $section;
                $db_src_table  = 'db_' . $db_type . 'comments';
                $db_set_field  = 'c_active';
                $db_whr_field  = 'c_id';
                $db_nam_field  = 'c_key';
                $db_dsc_field  = 'c_body';
                $db_add_query  = '';
                $db_order      = 'DESC';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_public_categories';
                $be            = 1;
                break;
            case "be_live_streaming_token": //token management
                $entry_section = $section;
                $db_src_table  = 'db_livetoken';
                $db_set_field  = 'tk_active';
                $db_whr_field  = 'tk_id';
                $db_nam_field  = 'tk_name';
                $db_dsc_field  = 'tk_price';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_public_categories';
                $be            = 1;
                break;
            case "be_token_purchase": //token purchases
                global $class_database;

                $entry_section = $section;
                $db_src_table  = 'db_tokenpayments';
                $db_set_field  = 'tk_active';
                $db_whr_field  = 'db_id';
                $db_nam_field  = 'tk_amount';
                $db_dsc_field  = 'tk_price';
                $db_add_query  = '';
                $db_order      = 'DESC';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_public_categories';
                $be            = 1;
                break;
            case "be_token_donation": //token donations
                $entry_section = $section;
                $db_src_table  = 'db_tokendonations';
                $db_set_field  = 'tk_active';
                $db_whr_field  = 'db_id';
                $db_nam_field  = 'tk_amount';
                $db_dsc_field  = 'tk_date';
                $db_add_query  = '';
                $db_order      = 'DESC';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_public_categories';
                $be            = 1;
                break;
            case "ban_list": //ban list
                $entry_section = 'ban_list';
                $db_src_table  = 'db_banlist';
                $db_set_field  = 'ban_active';
                $db_whr_field  = 'ban_id';
                $db_nam_field  = 'ban_ip';
                $db_dsc_field  = 'ban_descr';
                $db_add_query  = '';
                $db_order      = 'DESC';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_ban_list';
                $be            = 1;
                break;
            case "lang_types": //languages
                $entry_section = 'lang_types';
                $db_src_table  = 'db_languages';
                $db_set_field  = 'lang_active';
                $db_whr_field  = 'db_id';
                $db_nam_field  = 'lang_name';
                $db_dsc_field  = 'lang_id';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_languages';
                $be            = 1;
                break;
            case "ch_types": //channel types
                global $cfg;

                $entry_section = 'channel_types';
                $db_src_table  = 'db_usertypes';
                $db_set_field  = 'db_active';
                $db_whr_field  = 'db_id';
                $db_nam_field  = 'db_name';
                $db_dsc_field  = 'db_desc';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_channel_types';
                $be            = 1;
                break;
            case "pk_entry": //membership types
                $entry_section = 'membership_types';
                $db_src_table  = 'db_packtypes';
                $db_set_field  = 'pk_active';
                $db_whr_field  = 'pk_id';
                $db_nam_field  = 'pk_name';
                $db_dsc_field  = 'pk_descr';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_membership_types';
                $be            = 1;
                break;
            case "sub_entry": //subscription types
                $entry_section = 'subscription_types';
                $db_src_table  = 'db_subtypes';
                $db_set_field  = 'pk_active';
                $db_whr_field  = 'pk_id';
                $db_nam_field  = 'pk_name';
                $db_dsc_field  = 'pk_descr';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_membership_types';
                $be            = 1;
                break;
            case "dc_entry": //discount codes
                $entry_section = 'discount_codes';
                $db_src_table  = 'db_packdiscounts';
                $db_set_field  = 'dc_active';
                $db_whr_field  = 'dc_id';
                $db_nam_field  = 'dc_code';
                $db_dsc_field  = 'dc_descr';
                $db_add_query  = '';
                $db_order      = '';
                $del_action    = 'delete';
                $pg            = 1;
                $pg_cfg        = 'page_be_discount_codes';
                $be            = 1;
                break;
            case "adv_groups": //ad groups
                $entry_section = 'adv_groups';
                $db_src_table  = 'db_advgroups';
                $db_set_field  = 'adv_active';
                $db_whr_field  = 'db_id';
                $db_nam_field  = 'adv_name';
                $db_dsc_field  = 'adv_description';
                $pg            = 1;
                $be            = 1;
                $pg_cfg        = 'page_be_ad_groups';

                $get_array  = explode('-', $class_filter->clr_str($_GET['s']));
                $menu_entry = $get_array[0] . '-' . $get_array[1] . '-' . $get_array[2];

                switch ($_GET['s']) {
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
                    case "backend-menu-entry9-sub15":$db_add_query = "WHERE `adv_name` LIKE 'per_category_%'";
                        break;
                }
                $db_order   = '';
                $del_action = 'delete';
                break;
            case "adv_banners": //banner ads
                $entry_section = 'adv_banners';
                $db_src_table  = 'db_advbanners';
                $db_set_field  = 'adv_active';
                $db_whr_field  = 'adv_id';
                $db_nam_field  = 'adv_name';
                $db_dsc_field  = 'adv_description';
                $pg            = 1;
                $be            = 1;
                $pg_cfg        = 'page_be_ad_banners';

                $get_array  = explode('-', $class_filter->clr_str($_GET['s']));
                $menu_entry = $get_array[0] . '-' . $get_array[1] . '-' . $get_array[2];

                switch ($_GET['s']) {
                    case "backend-menu-entry7-sub1":$db_add_query = "WHERE `adv_group` IN(1, 2)";
                        break;
                    case "backend-menu-entry7-sub2":$db_add_query = "WHERE `adv_group` IN(7, 8, 9, 10, 11, 12)";
                        break;
                    case "backend-menu-entry7-sub3":$db_add_query = "WHERE `adv_group` IN(13, 14, 15, 56, 57, 58, 59)";
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
                    case "backend-menu-entry7-sub15":$db_add_query = "WHERE `adv_group` IN(60, 61, 62, 63, 64, 65)";
                        break;
                    default:break;
                }
                $db_order   = '';
                $del_action = 'delete';
                break;
            case "pmsg_entry": //personal messages
                global $cfg;

                $entry_section = 'personal_messages';
                $db_src_table  = $_GET['s'] == 'message-menu-entry3' ? 'db_channelcomments' : 'db_messaging';
                $db_set_field  = $_GET['s'] == 'message-menu-entry3' ? 'c_approved' : 'msg_active';
                $db_whr_field  = $_GET['s'] == 'message-menu-entry3' ? 'c_id' : 'msg_id';
                $db_nam_field  = $_GET['s'] == 'message-menu-entry3' ? $language['upage.text.comm.new'] : 'msg_subj';
                $db_dsc_field  = $_GET['s'] == 'message-menu-entry3' ? 'c_body' : 'msg_body';
                $db_order      = ' DESC ';
                $del_action    = 'delete_message';
                $pg            = 0;
                $pg_cfg        = 'page_message_list';

                $get_array  = explode('-', $class_filter->clr_str($_GET['s']));
                $menu_entry = $get_array[0] . '-' . $get_array[1] . '-' . $get_array[2];

                $db_add_query = VMessages::dbListQuery();

                switch ($_GET['s']) {
                    case "message-menu-entry5":
                        $entry_from = $language['msg.label.to'];
                        $_getfrom   = 2;
                        break;
                    case "message-menu-entry3":
                        $entry_from = $language['msg.label.from'];
                        $_getfrom   = 3;
                        break;
                    case "message-menu-entry2":
                    case "message-menu-entry4":
                    case "message-menu-entry6":
                        $entry_from = $language['msg.label.from'];
                        $_getfrom   = 1;
                        break;
                    default:
                        $lb_id  = substr($get_array[3], 3);
                        $lb_msg = $db->execute(sprintf("SELECT `lb_ids` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_for`='%s' AND `lb_id`='%s' LIMIT 1;", intval($_SESSION['USER_ID']), $menu_entry, intval($lb_id)));
                        $lb_fld = $lb_msg->fields['lb_ids'];

                        if ($lb_fld != '') {
                            $_mar  = unserialize($lb_fld);
                            $_mart = count($_mar);
                            foreach ($_mar as $key => $msg_id) {
                                $_q .= " " . ($key == 0 ? " AND (" : "OR") . " `msg_id`='" . $msg_id . "'" . ($key == $_mart - 1 ? " )" : null);
                            }
                        } else { $db_add_query = "WHERE `msg_id`='-1'";}

                        switch ($menu_entry) {
                            case "message-menu-entry2":$db_add_query .= $_q;
                                $_getfrom   = 1;
                                $entry_from = '<span class="greyed-out">' . $language['msg.label.from'] . '</span>';
                                break;
                            case "message-menu-entry3":$db_add_query .= $_q;
                                $_getfrom   = 3;
                                $entry_from = '<span class="greyed-out">' . $language['msg.label.from'] . '</span>';
                                break;
                            case "message-menu-entry5":$db_add_query .= $_q;
                                $_getfrom   = 2;
                                $entry_from = '<span class="greyed-out">' . $language['msg.label.to'] . '</span>';
                                break;
                            case "message-menu-entry6":$db_add_query .= $_q;
                                $_getfrom   = 1;
                                $entry_from = '<span class="greyed-out">' . $language['msg.label.from'] . '</span>';
                                break;
                        }
                        break;
                }
                break;
        }

        switch ($_GET['do']) {
            case "approve":
                $_POST ? VContacts::inviteApprove() : null;
                break;
            case "notice":
            case "approve":
                self::entryActions($entry_section, 'notice', $db_src_table, $db_set_field, $db_whr_field, '');
                break;
            case "enable":
                self::entryActions($entry_section, 'enable', $db_src_table, $db_set_field, $db_whr_field, self::getPostID());
                $update_total = $section == 'pmsg_entry' ? VMessages::labelTotal() : null;
                break;
            case "disable":
                self::entryActions($entry_section, 'disable', $db_src_table, $db_set_field, $db_whr_field, self::getPostID());
                $update_total = $section == 'pmsg_entry' ? VMessages::labelTotal() : null;
                break;
            case "delete":
                $del_action = ($menu_entry == 'message-menu-entry3' or $menu_entry == 'message-menu-entry4') ? 'delete' : $del_action;
                self::entryActions($entry_section, $del_action, $db_src_table, '', $db_whr_field, self::getPostID());
                break;
            case "update":
                switch ($menu_entry) {
                    case "backend-menu-entry7":VbeAdvertising::processBannerEntry();
                        break;
                    case "backend-menu-entry9":VbeAdvertising::processGroupEntry();
                        break;
                }
                switch ($_GET['s']) {
                    case "backend-menu-entry2-sub5":
                    case "backend-menu-entry2-sub5l":
                    case "backend-menu-entry2-sub5v":
                    case "backend-menu-entry2-sub5i":
                    case "backend-menu-entry2-sub5a":
                    case "backend-menu-entry2-sub5d":
                    case "backend-menu-entry2-sub5c":
                    case "backend-menu-entry2-sub5b":
                        VbeCategories::processEntry();
                        break;
                    case "backend-menu-entry3-sub5":VbeMembers::processBanEntry();
                        break;
                    case "backend-menu-entry2-sub16":VbeLanguage::processLangEntry();
                        break;
                    case "backend-menu-entry8-sub1":VbeAdvertising::processJWadEntry();
                        break; //jw ad general entry
                    case "backend-menu-entry8-sub2":VbeAdvertising::processJWfileEntry();
                        break; //jw ad file entry
                    case "backend-menu-entry8-sub3":VbeAdvertising::processFPadEntry();
                        break; //fp ad general entry
                    case "backend-menu-entry8-sub4":VbeAdvertising::processVJSadEntry();
                        break; //vjs ad general entry
                    case "backend-menu-entry3-sub13":VbeServers::processEntry();
                        break; //up servers general entry
                    case "backend-menu-entry14-sub1":
                    case "backend-menu-entry14-sub2":
                    case "backend-menu-entry14-sub3":
                    case "backend-menu-entry14-sub4":
                    case "backend-menu-entry14-sub5":
                    case "backend-menu-entry14-sub6":
                    case "backend-menu-entry14-sub7":
                        VbeStreaming::processEntry();
                        break; //live streaming, broadcast servers
                    case "backend-menu-entry14-sub8":
                        VbeStreaming::processTokenEntry();
                        break; //live streaming, token management
                    case "backend-menu-entry15-sub1":
                    case "backend-menu-entry15-sub2":
                        break; //token purchases
                    default:self::updateCurrent($entry_section, intval($_POST['hc_id']), $db_src_table, $db_whr_field);
                        break;
                }
                break;
            case "cb-enable":
            case "cb-disable":
            case "cb-approve":
            case "cb-delete":
            case "cb-label-add":
            case "cb-label-clear":
                $n = (isset($_POST['current_entry_id']) and is_array($_POST['current_entry_id'])) ? count($_POST['current_entry_id']) : 0;
                self::entryActions($entry_section, $_GET['do'], $db_src_table, $db_set_field, $db_whr_field, $n);
                $update_total = ($section == 'pmsg_entry') ? VMessages::labelTotal() : null;
                break;
        }

        switch ($_GET['do']) {
            case "add":
                switch ($section) {
                    case "ch_types":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeMembers::chTypes('block', 'mem-add-new-entry'));
                        break;
                    case "pk_entry":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeMembers::packEntry('block', 'mem-add-new-entry'));
                        break;
                    case "sub_entry":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeMembers::subEntry('block', 'mem-add-new-entry'));
                        break;
                    case "dc_entry":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeMembers::discountEntry('block', 'mem-add-new-entry'));
                        break;
                    case "ch_categ":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeCategories::mainCategoryDetails('block', 'mem-add-new-entry'));
                        break;
                    case "be_live_streaming":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeStreaming::mainServerDetails('block', 'mem-add-new-entry'));
                        break;
                    case "be_live_streaming_token":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeStreaming::mainTokenDetails('block', 'mem-add-new-entry'));
                        break;
                    case "adv_banners":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeAdvertising::mainAdvBannerDetails('block', 'mem-add-new-entry'));
                        break;
                    case "ban_list":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeMembers::banDetails('block', 'mem-add-new-entry'));
                        break;
                    case "lang_types":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeLanguage::mainLangDetails('block', 'mem-add-new-entry'));
                        break;
                    case "jw_files":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeAdvertising::mainAdvCodeDetails('block', 'mem-add-new-entry'));
                        break;
                    case "jw_ads":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeAdvertising::mainAdvAdDetails('block', 'mem-add-new-entry'));
                        break;
                    case "vjs_ads":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeAdvertising::vjsAdvAdDetails('block', 'mem-add-new-entry'));
                        break;
                    case "fp_ads":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeAdvertising::fpAdvAdDetails('block', 'mem-add-new-entry'));
                        break;
                    case "be_servers":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeServers::mainServerDetails('block', 'mem-add-new-entry'));
                        break;
                    case "be_xfer_video":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeServers::xferAdd('video'));
                        break;
                    case "be_xfer_short":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeServers::xferAdd('short'));
                        break;
                    case "be_xfer_image":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeServers::xferAdd('image'));
                        break;
                    case "be_xfer_audio":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeServers::xferAdd('audio'));
                        break;
                    case "be_xfer_doc":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeServers::xferAdd('doc'));
                        break;
                    case "be_file_comments":$html = VGenerate::simpleDivWrap('ct-entry-nowrap left-float left-margin10 wd97p', 'mem-add-new-entry-wrapper', VbeComments::mainCommentDetails('block', 'mem-add-new-entry'));
                        break;
                }
                break;

            default:
                $ul_class = 'be-users';

                if ($section == 'be_xfer_video' or $section == 'be_xfer_short' or $section == 'be_xfer_image' or $section == 'be_xfer_audio' or $section == 'be_xfer_doc') {
                    $tbl      = str_replace('be_xfer_', '', $section);
                    $ul_class = 'be-files';
                    /* search query */
                    $sql_1 = null;
                    $sql_2 = null;

                    if (strlen($_GET['sq']) >= 4) {
                        $query = $class_filter->clr_str($_GET['sq']);
                        $rel   = VGenerate::prepare($query);

                        $sql_1 = ", MATCH(B.`file_title`) AGAINST ('" . $rel . "') AS `Relevance` ";
                        $sql_2 = "MATCH(B.`file_title`) AGAINST('" . $rel . "' IN BOOLEAN MODE) AND ";

                        $q_entries = sprintf("SELECT A.*, B.`file_key` %s FROM `db_%stransfers` A, `db_%sfiles` B
                                            WHERE %s A.`file_key`=B.`file_key`", $sql_1, $tbl, $tbl, $sql_2);
                    }
                }

                $tt_class = 'left-float wd50 top-margin4 dark-rounded-border all-paddings5 center entry-tooltip';
                $extra_js .= $section == 'pmsg_entry' ? '$(".block-user").click(function(){
                                        form = $("#"+$(this).parent().parent().parent().parent().attr("id"));
                                        form.mask("");
                                        $.post(current_url + menu_section + "?s=' . $class_filter->clr_str($_GET['s']) . '&do=block"+paging_link, form.serialize(), function(data) {
                                            $("#siteContent").html(data);
                                            form.unmask();
                                        });
                                    });' : null;
                $extra_js .= $_GET['s'] == 'message-menu-entry4' ? '$(".approve-invite").click(function(){
                                            form = $("#"+$(this).parent().parent().parent().attr("id"));
                                            form.mask("");
                                            $.post(current_url + menu_section + "?s=message-menu-entry4&do=approve"+paging_link, form.serialize(),function(data) {
                                                $("#siteContent").html(data);
                                                form.unmask();
                                            });
                                        });' : null;
                $extra_js .= $_GET['s'] == 'message-menu-entry4' ? '$(".ignore-invite").click(function(){
                                            form = $("#"+$(this).parent().parent().parent().attr("id"));
                                            form.mask("");
                                            $.post(current_url + menu_section + "?s=message-menu-entry4&do=disable"+paging_link, form.serialize(),function(data) {
                                                $("#siteContent").html(data);
                                                form.unmask();
                                            });
                                        });' : null;

                $extra = VGenerate::declareJS($extra_js);
                /* pagination start */
                if ($q_entries == '') {
                    $q_entries  = sprintf("SELECT * FROM `%s` %s ORDER BY `%s` %s", $db_src_table, $db_add_query, $db_whr_field, $db_order);
                    $q_total    = sprintf("SELECT count(*) AS `total` FROM `%s` %s", $db_src_table, $db_add_query);
                    $q_totalres = $db->execute($q_total);
                    $db_count   = $q_totalres->fields['total'];
                } else {
                    $q_totalres = $db->execute($q_entries);
                    $db_count   = $q_totalres->recordcount();
                }

                $pages                 = new VPagination;
                $pages->items_total    = $db_count;
                $pages->mid_range      = 5;
                $pages->items_per_page = isset($_GET['ipp']) ? (int) $_GET['ipp'] : $cfg[$pg_cfg];
                $pages->paginate();

                $res = $db->execute($q_entries . $pages->limit . ';');

                $page_of = (($pages->high + 1) > $db_count) ? $db_count : ($pages->high + 1);

                $results_text = $pages->getResultsInfo($page_of, $db_count, 'left');
                $paging_links = $pages->getPaging($db_count, 'right');
                $page_jump    = $paging_links != '' ? $pages->getPageJump('left') : null;
                $ipp_select   = $paging_links != '' ? $pages->getIpp('right') : null;
                /* pagination end */
                $html .= ($section != 'pmsg_entry' and $paging_links != '') ? '<div id="paging-top" class="left-float wdmax section-bottom-border paging-bg-lighter" style="display: inline-block;">' . $page_jump . $ipp_select . '</div>' : null;
                $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger ' . $ul_class . '">';
                $entry_from = $section != 'pmsg_entry' ? '#' : $entry_from;

                while (!$res->EOF) {
                    $db_id   = $res->fields[$db_whr_field];
                    $bb      = ($do == $res->recordcount()) ? ($pg == 0 ? 'bottom-border' : '') : $bb;
                    $new_msg = ($section == 'pmsg_entry' and (VMessages::currentMenuEntry($_GET['s']) == 'message-menu-entry2' or VMessages::currentMenuEntry($_GET['s']) == 'message-menu-entry4' or VMessages::currentMenuEntry($_GET['s']) == 'message-menu-entry6' or VMessages::currentMenuEntry($_GET['s']) == 'message-menu-entry3')) ? VMessages::newMessageClass($db_id) : null;

                    if ($_getfrom >= 1) {
                        $db_v = $_getfrom == 1 ? $res->fields['msg_from'] : ($_getfrom == 2 ? $res->fields['msg_to'] : ($_getfrom == 3 ? $res->fields['c_usr_id'] : null));
                        $from = VUserinfo::getUserInfo($db_v);
                        $date = $_getfrom == 3 ? VUserinfo::timeRange($res->fields['c_datetime']) : VUserinfo::timeRange($res->fields['msg_date']);
                    }

                    if ($section == 'be_xfer_video' or $section == 'be_xfer_short' or $section == 'be_xfer_image' or $section == 'be_xfer_audio' or $section == 'be_xfer_doc') {
                        $date = $language["backend.xfer.state." . $res->fields[$db_dsc_field] . ".s"];
                        $bb   = 'bottom-border';
                    } elseif ($section == 'be_servers') {
                        $date = $res->fields['s3_bucketname'];
                    }

                    $err = false;
                    if ($_POST and $section == 'pmsg_entry' and isset($_GET['do'])) {
                        $a   = $class_filter->clr_str($_GET['do']);
                        $r   = self::processEntry($section, $a, $db_id);
                        $err = ($r[0] != '' ? $r[0] : $r[1]);
                    }

                    $html .= '<li>';
                    $html .= '<div class="left-float ct-entry left-margin-none ' . $bb . ' wd94p" id="' . $bullet_id . '-' . $db_id . '">';

                    $html .= '<div class="responsive-accordion-head entry-head' . (($_POST and $db_id == self::getPostID() and !$err) ? ' active' : null) . $new_msg . '">';
                    $html .= $be ? '<div class="d-flex">' : null;
                    $html .= '<div class="place-left icheck-box"><input type="checkbox" name="entryid" value="' . $db_id . '" class="list-check"></div>';
                    $html .= $section == 'pmsg_entry' ? '<div class="responsive-accordion-title w-100">' : null;
                    $html .= '<div class="d-flex entry-details">';
                    $html .= VGenerate::simpleDivWrap('entry-number ct-bullet-label-off d-flex' . $new_msg, 'lc' . $db_id . '-entry-details', ($entry_from == '#' ? '<span class="entry-number">' . $entry_from . $db_id . '</span>' : ($res->fields['msg_from'] > 0 ? ($section == 'pmsg_entry' ? '<span class="greyed-out">' . $entry_from . '</span> ' . '<a href="' . VHref::channelURL(["username" => $from['uname']]) . '">' . VAffiliate::getAffiliateBadge($from['key']) . ($from['dname'] != '' ? $from['dname'] : ($from['ch_title'] != '' ? $from['ch_title'] : $from['uname'])) . '</a> ' : null) : $entry_from . ' ' . $cfg['backend_username']) . ($section == 'pmsg_entry' ? '' : ' <a href="' . VHref::channelURL(["username" => $from['uname']]) . '">' . ($from['dname'] != '' ? $from['dname'] : ($from['ch_title'] != '' ? $from['ch_title'] : $from['uname'])) . '</a>')));
                    $html .= VGenerate::simpleDivWrap('entry-title-off ct-bullet-label-off d-flex' . $new_msg .
                        ($section != 'pmsg_entry' ? ' link' : null), '',
                        ($_GET['s'] == 'message-menu-entry3' ? $db_nam_field :
                            (($_GET['s'] == 'backend-menu-entry3-sub14' or $_GET['s'] == 'backend-menu-entry3-sub15' or $_GET['s'] == 'backend-menu-entry3-sub16' or $_GET['s'] == 'backend-menu-entry3-sub17') ? $class_database->singleFieldValue('db_' . $tbl . 'files', 'file_title', 'file_key', $res->fields[$db_nam_field]) :
                                (($section == 'adv_banners' or $section == 'adv_groups') ? '<span class="greyed-out me-5">#' . $db_id . '</span> ' : null) .
                                ($section == 'pmsg_entry' ? '<span class="greyed-out me-5">' . $language['msg.label.subj'] . '</span> ' : null) .
                                '<span class="' . ($section == 'pmsg_entry' ? 'msg-subj' : null) . '">' .
                                ($section == 'pmsg_entry' ? '"' : null) . '' .
                                ($section == 'be_token_donation' ? str_replace(array('##USER1##', '##USER2##', '##NR##'), array($res->fields['tk_from_user'], $res->fields['tk_to_user'], $res->fields['tk_amount']), $language['backend.menu.token.text']) :
                                    ($section == 'be_token_purchase' ? str_replace(array('##NR##', '##USER##'), array($res->fields['tk_amount'], $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_id', $res->fields['usr_id'])), $language['backend.menu.token.buy.text']) :
                                        ($section == 'be_file_comments' ? '<span class="ct-bullet-label"><a target="_blank" href="' . $cfg['main_url'] . '/' . $backend_access_url . '/' . VHref::getKey("be_members") . '?u=' . $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_id', $res->fields['c_usr_id']) . '">' . $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_id', $res->fields['c_usr_id']) . '</a></span>' :
                                            VUserinfo::truncateString($res->fields[$db_nam_field], 50)))) . ($section == 'pmsg_entry' ? '"' : null) . '</span>')));
                    $html .= '</div>';
                    $html .= $be ? '</div>' : null;
                    $html .= VGenerate::simpleDivWrap('entry-type ct-bullet-label-off place-left greyed-out' . ($section == 'be_file_comments' ? ' be-comments' : null) . $new_msg, '', '<span class="">' . ($section != 'be_file_comments' ? '[' : null) . ($date == '' ? ($section == 'be_token_purchase' ? '$' : null) . $res->fields[$db_dsc_field] . ($section == 'be_live_streaming_token' ? ' ' . $res->fields['tk_currency'] : ($section == 'be_token_purchase' ? ' - ' . VUserinfo::timeRange($res->fields['tk_date']) : null)) : $date) . ($section != 'be_file_comments' ? ']' : null));

                    $actions = (VMessages::currentMenuEntry($_GET['s']) != 'message-menu-entry4' and $section != 'adv_groups') ? '<a href="javascript:;"><i class="delete-grey" id="ic3-' . $entry_id . '-' . $db_id . '" title="' . $language['frontend.global.delete'] . '" rel="tooltip"></i></a>' : null;

                    switch ($res->fields[$db_set_field]) {
                        case "1":
                            $actions .= (VMessages::currentMenuEntry($_GET['s']) != 'message-menu-entry5') ? '<a href="javascript:;"><i title="' . $language['frontend.global.disable'] . '" rel="tooltip" class="disable-grey" id="ic2-' . $entry_id . '-' . $db_id . '"></i></a>' : null;
                            break;
                        case "0":
                            $actions .= (VMessages::currentMenuEntry($_GET['s']) != 'message-menu-entry5') ? '<a href="javascript:;"><i title="' . $language['frontend.global.enable'] . '" rel="tooltip" class="enable-grey" id="ic1-' . $entry_id . '-' . $db_id . '"></i></a>' : null;
                            break;

                    }
                    $tt_class = 'icon-tag';

                    $html .= '<div class="place-right expand-entry">';
                    $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: ' . (($_POST and $db_id == self::getPostID() and !$err) ? 'none' : 'inline') . ';"></i>';
                    $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: ' . (($_POST and $db_id == self::getPostID() and !$err) ? 'inline' : 'none') . ';"></i>';
                    $html .= ($section != 'be_token_purchase' and $section != 'be_token_donation') ? '<div id="' . $entry_id . $db_id . '-actions" class="entry-details-actions">' . $actions . '</div>' : null;
                    $html .= $section == 'pmsg_entry' ? '</div>' : null; //end responsive-accordion-title
                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '<div class="responsive-accordion-panel' . $_class[0] . '" style="display: ' . (($_POST and $db_id == self::getPostID() and !$err) ? 'block' : 'none') . ';">';

                    switch ($section) {
                        case "pk_entry":$html .= VbeMembers::packEntry('block', $entry_id, $db_id, $res->fields['pk_name'], $res->fields['pk_descr'], $res->fields['pk_space'], $res->fields['pk_bw'], $res->fields['pk_priceunit'], $res->fields['pk_priceunitname'], $res->fields['pk_price'], $res->fields['pk_alimit'], $res->fields['pk_ilimit'], $res->fields['pk_vlimit'], $res->fields['pk_slimit'], $res->fields['pk_dlimit'], $res->fields['pk_llimit'], $res->fields['pk_blimit'], $res->fields['pk_period'], $res->fields['pk_active']);
                            break;
                        case "sub_entry":$html .= VbeMembers::subEntry('block', $entry_id, $db_id, $res->fields['pk_name'], $res->fields['pk_descr'], $res->fields['pk_priceunit'], $res->fields['pk_priceunitname'], $res->fields['pk_price'], $res->fields['pk_period'], $res->fields['pk_active']);
                            break;
                        case "dc_entry":$html .= VbeMembers::discountEntry('block', $entry_id, $db_id, $res->fields[$db_nam_field], $res->fields[$db_dsc_field], $res->fields['dc_amount'], $res->fields['dc_date'], $res->fields[$db_set_field]);
                            break;
                        case "pmsg_entry":$html .= VMessages::messageDetails('block', $entry_id, $db_id, $res->fields[$db_dsc_field], $from['uname'], $res->fields['msg_from']);
                            break;
                        case "ch_categ":$html .= VbeCategories::categoryDetails('block', $entry_id, $db_id, $res->fields['ct_active'], $res->fields['ct_name'], unserialize($res->fields['ct_lang']), $res->fields['ct_slug'], $res->fields['ct_descr'], $res->fields['ct_type'], $res->fields['ct_featured'], $res->fields['sub_id'], $res->fields['ct_menu'], $res->fields['ct_icon'], $res->fields['ct_ads'], $res->fields['ct_banners'], $res->fields['ct_index'], $languages);
                            break;
                        case "be_live_streaming":$html .= VbeStreaming::serverDetails('block', $entry_id, $db_id, $res->fields['srv_active'], $res->fields['srv_name'], $res->fields['srv_slug'], $res->fields['srv_host'], $res->fields['srv_port'], $res->fields['srv_https']);
                            break;
                        case "be_live_streaming_token":$html .= VbeStreaming::tokenDetails('block', $entry_id, $db_id, $res->fields['tk_active'], $res->fields['tk_name'], $res->fields['tk_slug'], $res->fields['tk_amount'], $res->fields['tk_price'], $res->fields['tk_vat'], $res->fields['tk_shared'], $res->fields['tk_currency']);
                            break;
                        case "be_token_purchase":$html .= VbeToken::tokenDetails('block', $entry_id, $db_id, $res->fields['usr_id'], $res->fields['tk_id'], $res->fields['tk_amount'], $res->fields['tk_price'], $res->fields['tk_date'], $res->fields['txn_id'], $res->fields['txn_receipt'], $res->fields['tk_active']);
                            break;
                        case "be_token_donation":$html .= VbeToken::tokenDonationDetails('block', $entry_id, $db_id, $res->fields['tk_from'], $res->fields['tk_to'], $res->fields['tk_from_user'], $res->fields['tk_to_user'], $res->fields['tk_amount'], $res->fields['tk_date'], $res->fields['tk_active']);
                            break;
                        case "ban_list":$html .= VbeMembers::banDetails('block', $entry_id, $db_id, $res->fields['ban_active'], $res->fields['ban_ip'], $res->fields['ban_descr']);
                            break;
                        case "lang_types":$html .= VbeLanguage::langDetails('block', $entry_id, $db_id, $res->fields['lang_active'], $res->fields['lang_name'], $res->fields['lang_flag'], $res->fields['lang_id'], $res->fields['lang_default']);
                            break;
                        case "adv_groups":$html .= VbeAdvertising::advGroupDetails('block', $entry_id, $db_id, $res->fields['adv_active'], $res->fields['adv_name'], $res->fields['adv_description'], $res->fields['adv_rotate'], $res->fields['adv_width'], $res->fields['adv_height'], $res->fields['adv_class'], $res->fields['adv_style']);
                            break;
                        case "adv_banners":$html .= VbeAdvertising::advBannerDetails('block', $entry_id, $db_id, $res->fields['adv_active'], $res->fields['adv_name'], $res->fields['adv_description'], $res->fields['adv_type'], $res->fields['adv_group'], $res->fields['adv_code']);
                            break;
                        case "vjs_ads":$html .= VbeAdvertising::VJSadvDetails('block', $entry_id, $db_id, $res->fields['ad_active'], $res->fields['ad_name'], $res->fields['ad_key'], $res->fields['ad_type'], $res->fields['ad_position'], $res->fields['ad_offset'], $res->fields['ad_duration'], $res->fields['ad_client'], $res->fields['ad_format'], $res->fields['ad_server'], $res->fields['ad_file'], $res->fields['ad_width'], $res->fields['ad_height'], $res->fields['ad_bitrate'], $res->fields['ad_tag'], $res->fields['ad_comp_div'], $res->fields['ad_comp_id'], $res->fields['ad_comp_w'], $res->fields['ad_comp_h'], $res->fields['ad_click_track'], $res->fields['ad_click_url'], $res->fields['ad_track_events'], $res->fields['ad_impressions'], $res->fields['ad_clicks'], $res->fields['ad_primary'], $res->fields['ad_mobile'], $res->fields['ad_custom'], $res->fields['ad_custom_url'], $res->fields['ad_skip']);
                            break;
                        case "jw_ads":$html .= VbeAdvertising::JWadvDetails('block', $entry_id, $db_id, $res->fields['ad_active'], $res->fields['ad_name'], $res->fields['ad_key'], $res->fields['ad_type'], $res->fields['ad_position'], $res->fields['ad_offset'], $res->fields['ad_duration'], $res->fields['ad_client'], $res->fields['ad_format'], $res->fields['ad_server'], $res->fields['ad_file'], $res->fields['ad_width'], $res->fields['ad_height'], $res->fields['ad_bitrate'], $res->fields['ad_tag'], $res->fields['ad_comp_div'], $res->fields['ad_comp_id'], $res->fields['ad_comp_w'], $res->fields['ad_comp_h'], $res->fields['ad_click_track'], $res->fields['ad_click_url'], $res->fields['ad_track_events'], $res->fields['ad_impressions'], $res->fields['ad_clicks'], $res->fields['ad_primary']);
                            break;
                        case "jw_files":$html .= VbeAdvertising::JWadvCodes('block', $entry_id, $db_id, $res->fields['db_active'], $res->fields['db_type'], $res->fields['db_name'], $res->fields['db_key'], $res->fields['db_code']);
                            break;
                        case "fp_ads":$html .= VbeAdvertising::FPadvDetails('block', $entry_id, $db_id, $res->fields['ad_active'], $res->fields['ad_name'], $res->fields['ad_key'], $res->fields['ad_cuepoint'], $res->fields['ad_css'], $res->fields['ad_file']);
                            break;
                        case "be_servers":$html .= VbeServers::serverDetails('block', $entry_id, $db_id, $res->fields['status'], $res->fields['server_name'], $res->fields['server_type'], $res->fields['server_priority'], $res->fields['server_limit'], $res->fields['file_hop'], $res->fields['upload_v_file'], $res->fields['upload_v_thumb'], $res->fields['upload_i_file'], $res->fields['upload_i_thumb'], $res->fields['upload_a_file'], $res->fields['upload_a_thumb'], $res->fields['upload_d_file'], $res->fields['upload_d_thumb'], $res->fields['ftp_transfer'], $res->fields['ftp_passive'], $res->fields['ftp_host'], $res->fields['ftp_port'], $res->fields['ftp_username'], $res->fields['ftp_password'], $res->fields['ftp_root'], $res->fields['url'], $res->fields['total_video'], $res->fields['total_short'], $res->fields['total_image'], $res->fields['total_audio'], $res->fields['total_doc'], $res->fields['current_hop'], $res->fields['last_used'], $res->fields['lighttpd_url'], $res->fields['lighttpd_secdownload'], $res->fields['lighttpd_prefix'], $res->fields['lighttpd_key'], $res->fields['s3_bucketname'], $res->fields['s3_accesskey'], $res->fields['s3_secretkey'], $res->fields['s3_region'], $res->fields['s3_fileperm'], $res->fields['cf_enabled'], $res->fields['cf_dist_type'], $res->fields['cf_dist_price'], $res->fields['cf_signed_url'], $res->fields['cf_signed_expire'], $res->fields['cf_key_pair'], $res->fields['cf_key_file'], $res->fields['upload_s_file'], $res->fields['upload_s_thumb']);
                            break;
                        case "be_xfer_video":
                        case "be_xfer_short":
                        case "be_xfer_image":
                        case "be_xfer_audio":
                        case "be_xfer_doc":$html .= VbeServers::xferDetails('block', $res->fields[$db_nam_field], $entry_id, $db_id, $res->fields['state'], $res->fields['upload_server'], $res->fields['thumb_server'], $res->fields['upload_start_time'], $res->fields['upload_end_time'], $res->fields['thumb_start_time'], $res->fields['thumb_end_time']);
                            break;
                        case "be_file_comments":$html .= VbeComments::commentDetails('block', $entry_id, $db_id, $res->fields['file_key'], $res->fields['c_usr_id'], $res->fields['c_key'], $res->fields['c_body'], $res->fields['c_datetime'], $res->fields['c_approved'], $res->fields['c_active']);
                            break;
                    }

                    $html .= VGenerate::declareJS('$(document).ready(function(){var aclone' . $db_id . ' = $("#' . $entry_id . $db_id . '-actions").clone(true); $(aclone' . $db_id . ').addClass("place-right").insertAfter("#' . $entry_id . '-' . $db_id . ' div.row > div.act"); $("#' . $entry_id . $db_id . '-actions").detach();});');

                    $html .= '</div>';
                    $html .= '</div>';
                    $html .= '</li>';

                    $do = $do + 1;
                    $res->MoveNext();
                }

                echo $up_js = ($section == 'be_xfer_video' or $section == 'be_xfer_short' or $section == 'be_xfer_image' or $section == 'be_xfer_audio' or $section == 'be_xfer_doc' or $section == 'be_servers') ? VbeServers::ftpJS() : null; //up servers js

                $html .= $db_count > 0 ? $extra : null;
                $html .= $db_count > 0 ? '<div id="paging-bottom" class="left-float wdmax paging-top-border paging-bg">' . $paging_links . $results_text . '</div>' : null;
                $html .= '</ul>';
                $html .= $section == 'ch_categ' ? VbeCategories::categJS($class_filter->clr_str($_GET['s'])) : null;
                $html .= ($section == 'lang_types' or $section == 'be_servers') ? '<script type="text/javascript">$(document).ready(function() {$(".uactions-list li a").click(function() {if ($(this).hasClass("popup") || $(this).parent().hasClass("popup")) {return;}$(".uactions-list li").removeClass("active");$(this).parent().addClass("active");});});</script>' : null;

                $html .= VGenerate::declareJS('$(document).ready(function(){var clone = $("#paging-top").clone(true); $("#paging-top").detach(); ' . (substr($section, 0, 7) == 'be_xfer' ? '$(clone).insertBefore("#open-close-links");' : '$(clone).prependTo(".page-actions");') . 'var add = $("#add-new-entry").clone(true); $("#add-new-entry").detach(); $(add).insertBefore(".section-top-bar"); $(\'<div class="clearfix"></div>\').insertAfter(add);' . ($_GET['do'] == 'add' ? '$(".section-top-bar").removeClass("section-top-bar").addClass("section-top-bar-add");' : null) . '});');

                $_s = isset($_GET['s']) ? $class_filter->clr_str($_GET['s']) : null;

                if ($section == 'pmsg_entry') {
                    if ($_s != 'message-menu-entry2' and $_s != 'message-menu-entry4' and $_s != 'message-menu-entry5' and $_s != 'message-menu-entry6') {
                        $html .= VGenerate::declareJS('$(document).ready(function(){$("#new-label, #add-new-label").detach();});');
                    }
                }

                break;
        }
        return $html;
    }
    /* message for no entries */
    public function noEntryMessage()
    {
        global $language, $class_filter;

        $_s   = isset($_GET['s']) ? $class_filter->clr_str($_GET['s']) : null;
        $menu = VMessages::currentMenuEntry($_s);

        if ($menu == 'message-menu-entry7') {
            return $language['err.no.contacts'];
        } else {
            return $language['notif.no.messages'];
        }
    }
}
