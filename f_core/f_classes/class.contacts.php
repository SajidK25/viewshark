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

class VContacts
{
    /* general contacts layout */
    public function contactsLayout()
    {
        global $class_filter;

        $_s = substr($class_filter->clr_str($_GET["s"]), 0, 23);

        $html = '
            <div class="left-float wdmax">' . ($_POST ? self::cbActions($_GET["do"], 1) : null) . '</div>
            <div class="wdmax left-float ct-master-wrap">
                <div class="left-float wd200 ct-entry-list">
                    <div class="vs-column thirds">
                        <form id="ct-entry-selection" method="post" action="">
                            ' . self::leftContactsList() . '
                            <input type="hidden" name="ct-entry-selection-hfe" value="1" />
                        </form>
                    </div>
                    <div class="vs-column two_thirds fit">
                        <div id="ct-details-wrap">' . self::contactDetails() . '</div>
                    </div>
                </div>
            </div>';

        $html .= VGenerate::declareJS('$(document).ready(function(){
                        var clone = $("#paging-top").clone(true); $("#paging-top").detach(); $(clone).prependTo(".page-actions");
                        var add = $("#add-new-entry").clone(true); $("#add-new-entry").detach(); $(add).insertBefore(".section-top-bar"); $(\'<div class="clearfix"></div>\').insertAfter(add);
                        var c = $("#ct-contact-add-wrap").clone(true); $("#ct-contact-add-wrap").detach(); $(c).insertAfter("#add-new-entry");
                        ' . ($_GET["do"] == 'add' ? '$(".section-top-bar").removeClass("section-top-bar").addClass("section-top-bar-add");' : null) . '
                        ' . ($_s == 'message-menu-entry7-sub' ? '$("#new-contact, #ct-contact-add-wrap, #new-label, #add-new-label").detach();' : null) . '
                        });');

        return $html;
    }
    /* editing a contact */
    public function ctEdit()
    {
        $validate    = self::ctValidation('edit');
        echo $notify = VGenerate::noticeTpl('', $validate[0], $validate[1]);
    }
    /* adding a contact */
    public function addNew()
    {
        global $smarty;

        $validate    = self::ctValidation('new');
        echo $notify = VGenerate::noticeTpl(' top-padding5', $validate[0], $validate[1]);

        $smarty->display('tpl_frontend/tpl_msg/tpl_contact_add.tpl');
    }
    /* validating add/edit */
    public function ctValidation($mode = '')
    {
        global $cfg, $class_database, $class_filter, $db, $language;

        $notice_message = null;
        $email_check    = new VValidation;
        $ct_name        = $class_filter->clr_str($_POST["frontend_global_name"]);
        $ct_user        = $class_filter->clr_str($_POST["frontend_global_username"]);
        $ct_mail        = $class_filter->clr_str($_POST["frontend_global_email"]);
        $update_id      = $mode == 'edit' ? intval($_POST["section_entry_value"]) : null;

        $error_message = ($mode == 'new' and $ct_user == '' and $ct_mail == '') ? $language["err.new.contacts"] : (($mode == 'edit' and self::getContactUsername($update_id) == '' and $ct_mail == '') ? $language["err.new.contacts"] : null);
        $error_message = ($error_message == '' and $mode == 'new' and $ct_user != '' and $class_database->singleFieldValue('db_usercontacts', 'ct_username', 'usr_id', intval($_SESSION["USER_ID"])) == $ct_user) ? $language["notif.contacts.present"] : $error_message;
        $error_message = ($error_message == '' and $mode == 'new' and $ct_mail != '' and $class_database->singleFieldValue('db_usercontacts', 'ct_email', 'usr_id', intval($_SESSION["USER_ID"])) == $ct_mail) ? $language["notif.contacts.present"] : $error_message;
        $error_message = ($error_message == '' and ($ct_user == $_SESSION["USER_NAME"] or $ct_mail == $class_database->singleFieldValue('db_accountuser', 'usr_email', 'usr_id', intval($_SESSION["USER_ID"])))) ? $language["err.self.contacts"] : $error_message;
        $error_message = ($error_message == '' and $ct_user != '' and !VUserinfo::existingUsername($ct_user)) ? $language["err.new.inv.user"] : $error_message;
        $error_message = ($error_message == '' and $ct_mail != '' and !$email_check->checkEmailAddress($ct_mail)) ? $language["frontend.signup.email.invalid"] : $error_message;

        if ($error_message == '') {
            switch ($mode) {
                case "new":
                    $notice_message = $language["notif.contacts.added"];
                    $block_db       = serialize(array("bl_files" => 1, "bl_channel" => 1, "bl_comments" => 1, "bl_messages" => 1, "bl_subscribe" => 1));
                    $update_ar      = array("usr_id" => intval($_SESSION["USER_ID"]), "pwd_id" => VUserinfo::generateRandomString(10), "ct_name" => $ct_name, "ct_username" => $ct_user, "ct_email" => $ct_mail, "ct_datetime" => date("Y-m-d H:i:s"), "ct_block_cfg" => $block_db);
                    $update_do      = $class_database->doInsert('db_usercontacts', $update_ar);
                    $update_id      = $db->Insert_ID();
                    echo $update_js = $update_id > 0 ? VGenerate::declareJS('wrapLoad(current_url + menu_section + "?s=' . $class_filter->clr_str($_GET["s"]) . '&n=' . $update_id . '");') : null;
                    break;
                case "edit":
                    $notice_message = $language["notif.contacts.updated"];
                    $update_ar      = array("ct_name" => $ct_name, "ct_email" => $ct_mail);
                    foreach ($update_ar as $dbf => $val) {$q .= "`" . $dbf . "` = '" . $val . "', ";}
                    $update_do = $db->execute(sprintf("UPDATE `db_usercontacts` SET %s WHERE `ct_id`='%s' LIMIT 1;", (substr($q, 0, -2)), $update_id));
                    $update_js = $db->Affected_Rows() > 0 ? self::updatedContactJS($update_id) : null;
                    break;
            }
            if ($update_id > 0) {
                if (is_array($_POST["contact-add-to-label"]) and count($_POST["contact-add-to-label"]) > 0) {
                    foreach ($_POST["contact-add-to-label"] as $lb_id) {
                        VMessages::addToLabelActions('contact-add', 1, $update_id, $lb_id);
                    }
                }
                if (is_array($_POST["contact-del-from-label"]) and count($_POST["contact-del-from-label"]) > 0) {
                    foreach ($_POST["contact-del-from-label"] as $lb_id) {
                        VMessages::addToLabelActions('contact-del', 1, $update_id, $lb_id);
                    }
                }
            } else {
                $error_message = $language["notif.contacts.present"];
            }

        }
        return array($error_message, $notice_message);
    }
    /* friends/block actions [for cb selected]*/
    public function ctAction($action, $force = '', $channels = '')
    {
        global $cfg, $db, $class_database, $language;

        $f_check = $force == '' ? $_POST["current_entry_id"] : $force;

        if (count($f_check) > 0) {
            $bl_db = null;
            switch ($action) {
                case "cb-block":
                    $bl_fld = 'ct_blocked';
                    $bl_val = 1;
                    break;
                case "cb-unblock":
                    $bl_fld = 'ct_blocked';
                    $bl_val = 0;
                    break;
                case "cb-addfr":
                    $bl_db  = "`ct_datetime`='" . date("Y-m-d H:i:s") . "', ";
                    $bl_fld = 'ct_friend';
                    $bl_val = $cfg["approve_friends"] == 1 ? 0 : 1;
                    break;
                case "cb-remfr":
                    $bl_fld = 'ct_friend';
                    $bl_val = 0;
                    break;
                default:
                    $bl_val = 0;
                    break;
            }

            foreach ($f_check as $ct_id) {
                $me_frdb = $action == 'cb-addfr' ? $db->execute(sprintf("SELECT `ct_friend` from `db_usercontacts` WHERE `usr_id`='%s' AND `ct_username`='%s' LIMIT 1;", VUserinfo::getUserID($class_database->singleFieldValue('db_usercontacts', 'ct_username', 'ct_id', $ct_id)), $_SESSION["USER_NAME"])) : null;
                $me_fr   = $action == 'cb-addfr' ? ($me_frdb->fields["ct_friend"] != 1 ? 0 : 1) : 0;
                $is_fr   = $action == 'cb-addfr' ? $class_database->singleFieldValue('db_usercontacts', 'ct_friend', 'ct_id', $ct_id) : null;
                $bl_val  = (($bl_val == 0 and $is_fr == 1) or $me_fr == 1) ? 1 : $bl_val;
                $bl_stat = $action == 'cb-addfr' ? self::getBlockStatus(VUserinfo::getUserID($class_database->singleFieldValue('db_usercontacts', 'ct_username', 'ct_id', $ct_id)), $_SESSION["USER_NAME"]) : null;
                $bl_opt  = $action == 'cb-addfr' ? self::getBlockCfg('bl_messages', VUserinfo::getUserID($class_database->singleFieldValue('db_usercontacts', 'ct_username', 'ct_id', $ct_id)), $_SESSION["USER_NAME"]) : null;

                if ($action == 'cb-addfr' and $bl_stat == 1 and $bl_opt == 1) {
                    if ($channels != '') {
                        echo VGenerate::noticeTpl('', $language["notif.error.blocked.request"], '');
                        return false;
                    } else {
                        return false;
                    }
                }
                $bl_val     = ($action == 'cb-addfr' and $bl_val == 1 and $bl_stat == 1 and $bl_opt == 1) ? 0 : $bl_val;
                echo $bl_js = ($action == 'cb-addfr' and $bl_val == 0 and $bl_stat == 1 and $bl_opt == 1) ? ($channels == '' ? VGenerate::declareJS('$(".notice-message-text").html("' . $language["notif.error.blocked.request"] . '")') : VGenerate::noticeTpl('', $language["notif.error.blocked.request"], '')) : null;

                $invite = ($action == 'cb-addfr' and $is_fr == 0 and $me_fr != 1 and (($bl_stat == 1 and $bl_opt == 0) or $bl_stat == 0)) ? self::inviteContact($ct_id) : null;

                $db->execute(sprintf("UPDATE `db_usercontacts` SET %s `%s`='%s' WHERE `ct_id`='%s' AND `ct_username`!='' AND `usr_id`='%s' AND `ct_active`='1' LIMIT 1;", $bl_db, $bl_fld, $bl_val, $ct_id, intval($_SESSION["USER_ID"])));
                if ($channels != '') {
                    echo VGenerate::noticeTpl('', '', $language["notif.success.request"]);
                }
            }
        }
    }
    /* invite contacts */
    public function inviteContact($ct_id)
    {
        global $db, $cfg, $class_database, $notifier, $language;

        $mail_arr = array();
        $cts      = $db->execute(sprintf("SELECT `ct_username`, `ct_email`, `ct_blocked` FROM `db_usercontacts` WHERE `ct_id`='%s' AND `ct_friend`='0' LIMIT 1;", $ct_id));
        $ct_user  = $cts->fields["ct_username"];
        $ct_mail  = $cts->fields["ct_email"];
        $ct_blk   = $cts->fields["ct_blocked"];
        $ct_not   = $class_database->singleFieldValue('db_accountuser', 'usr_mail_friendinv', 'usr_id', intval(VUserinfo::getUserID($ct_user)));

        $bl_stat = self::getBlockStatus(VUserinfo::getUserId($ct_user), $_SESSION["USER_NAME"]);
        $bl_opt  = self::getBlockStatus('bl_messages', VUserinfo::getUserId($ct_user), $_SESSION["USER_NAME"]);

        if ($ct_blk == 0 and (($bl_stat == 1 and $bl_opt == 0) or $bt_stat == 0)) {
            if ($ct_user != '' and $ct_mail != '' and $cfg["approve_friends"] == 1) {
                $notify_type        = 'invite_user';
                $mail_arr[0]        = $ct_mail;
                $notifier->dst_mail = $ct_mail;
                $notifier->dst_name = $ct_user;
            } elseif ($ct_user != '' and $ct_mail == '' and $cfg["approve_friends"] == 1) {
                $notify_type        = 'invite_user';
                $mail_arr[0]        = $class_database->singleFieldValue('db_accountuser', 'usr_email', 'usr_user', $ct_user);
                $notifier->dst_mail = $mail_arr[0];
                $notifier->dst_name = $ct_user;
            } elseif ($ct_user == '' and $ct_mail != '') {
                $notify_type        = 'invite_contact';
                $mail_arr[0]        = $ct_mail;
                $notifier->dst_mail = $ct_mail;
            }
            $mail_do = VNotify::queInit($notify_type, $mail_arr, $ct_id);
        }
    }
    /* check invite owner */
    public function inviteCheck()
    {
        global $class_database, $class_filter, $db, $language, $smarty;

        $exp   = 24 * 7; //expires in 7 days
        $db_id = $class_filter->clr_str($_GET["sid"]);
        $db_q  = sprintf("SELECT A.`usr_id`, A.`ct_username`, A.`ct_email`, B.`usr_id`, B.`create_date`, B.`code_used`, B.`code_active` FROM `db_usercontacts` A, `db_usercodes` B WHERE A.`pwd_id`=B.`pwd_id` AND B.`pwd_id`='%s' AND B.`code_active`='1';", $db_id);
        $db_r  = $db->execute($db_q);

        if ($db_r->fields["usr_id"] == '') {
            return 'tpl_error_max';
        }

        if ($_SESSION["USER_ID"] == $db_r->fields["usr_id"]) {
            $own_text = str_replace('##INV##', ($db_r->fields["ct_username"] != '' ? '<a href="' . $cfg["main_url"] . '/' . VHref::getKey("user") . '/' . $db_r->fields["ct_username"] . '" class="font14pt">' . $db_r->fields["ct_username"] . '</a>' : $db_r->fields["ct_email"]), $language["contacts.friends.h1.your"]);
            $own_info = str_replace(array('##DATE##', '##TIME##'), array('<span class="bold">' . date('M. d, Y H:i A', strtotime($db_r->fields["create_date"])) . '</span>', ($exp / 24) . ' ' . $language["frontend.global.days"]), $language["contacts.friends.your.text"]);
            $smarty->assign('invite_text_h1', $own_text);
            $smarty->assign('invite_text_info', $own_info);
            $smarty->assign('my_invite', 1);
        } else {
            if ($_SESSION["USER_ID"] > 0 and $_SESSION["USER_ID"] != $db_r->fields["usr_id"] and $db_r->fields["ct_username"] == '') {return 'tpl_error_max';}
            $smarty->assign('invited', $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_id', $db_r->fields["usr_id"]));
        }
    }
    /* approve invite */
    public function inviteApprove($inv_key = '', $msg_id = '')
    {
        global $db, $cfg, $class_database, $class_filter, $language;

        if ($cfg["approve_friends"] == 0) {
            return false;
        }

        $db_q = sprintf("SELECT A.`type`, A.`usr_id`, A.`pwd_id`, B.`usr_id`, B.`pwd_id`, B.`ct_username` FROM `db_usercodes` A, `db_usercontacts` B WHERE
                    A.`usr_id` = B.`usr_id` AND A.`pwd_id` = B.`pwd_id` AND A.`pwd_id` = '%s' AND A.`code_active` = '1' AND A.`code_used` = '0' AND
                    B.`ct_friend` = '0' AND B.`ct_blocked` = '0'", ($inv_key != '' ? $inv_key : $class_filter->clr_str($_POST["inv_key"])));
        $db_res = $db->execute($db_q);

        if ($db_res->RecordCount() > 0) {
            $db_uid = $db_res->fields["usr_id"];
            $db_pwd = $db_res->fields["pwd_id"];
            $db_str = VUserinfo::generateRandomString(10);
            $db_usr = $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_id', $db_uid);

            $db_q = $db->execute(sprintf("UPDATE `db_usercodes` SET `use_date`='%s', `code_used`='1' WHERE `usr_id`='%s' AND `pwd_id`='%s' LIMIT 1;", date("Y-m-d H:i:s"), $db_uid, $db_pwd));
            $db_q = $db->execute(sprintf("UPDATE `db_usercontacts` SET `ct_friend`='1' WHERE `usr_id`='%s' AND `pwd_id`='%s' AND `ct_username`='%s' AND `ct_friend`='0' LIMIT 1;", $db_uid, $db_pwd, $_SESSION["USER_NAME"]));
            $db_q = $db->execute(sprintf("UPDATE `db_messaging` SET `msg_active`='0' WHERE `msg_id`='%s' AND `msg_to`='%s' LIMIT 1;", ($msg_id != '' ? $msg_id : intval($_POST["section_subject_value"])), intval($_SESSION["USER_ID"])));
            $db_q = !$class_database->doInsert('db_usercontacts', array("usr_id" => intval($_SESSION["USER_ID"]), "pwd_id" => $db_str, "ct_username" => $db_usr, "ct_datetime" => date("Y-m-d H:i:s"), "ct_friend" => 1)) ? $db->execute(sprintf("UPDATE `db_usercontacts` SET `ct_friend`='1' WHERE `usr_id`='%s' AND `ct_username`='%s' AND `ct_friend`='0' LIMIT 1;", intval($_SESSION["USER_ID"]), $db_usr)) : null;

            echo VGenerate::jqHtml('#message-menu-entry4-count', VMessages::messageCount('message-menu-entry4'));
            echo VGenerate::jqHtml('#message-menu-entry7-count', VMessages::messageCount('message-menu-entry7'));
            echo VGenerate::jqHtml('#mme7c1', self::getContactCount('message-menu-entry7-sub1'));
        } elseif ($db_res->fields["code_active"] == 0 and $_GET["do"] != 'cb-approve') {
            echo $db_res->fields["code_active"];
            echo VGenerate::noticeTpl('', $language["notif.error.blocked.request"], '');
        }
    }
    /* send message action [for cb selected] */
    public function messageSelected()
    {
        global $smarty, $class_filter;

        if (count($_POST["current_entry_id"]) > 0) {
            foreach ($_POST["current_entry_id"] as $ct_id) {
                $ct_usr = self::getContactUsername($class_filter->clr_str($ct_id));
                $ct_str .= $ct_usr != '' ? $ct_usr . ',' : null;
            }
            $to         = substr($ct_str, 0, -1);
            echo $to_js = VGenerate::declareJS('$("#ct-wrapper").mask(" "); $("#ct-wrapper").load(current_url+menu_section+"?do=compose", function(){ $("input[name$=\"msg_label_to\"]").val("' . $to . '"); $("#ct-wrapper").unmask(); });');
        }
    }
    /* remove contacts from all labels when using cb delete */
    public function ctLabelClear($ct_id)
    {
        global $db;
        $lb_q   = $db->execute(sprintf("SELECT `lb_id` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_for`='message-menu-entry7' AND lb_ids!='';", intval($_SESSION["USER_ID"])));
        $lb_ids = $lb_q->getrows();
        if (count($lb_ids) > 0) {
            foreach ($lb_ids as $lb_id) {
                $_lb_cl = VMessages::addToLabelActions('contact-del', 1, $ct_id, $lb_id[0]);
            }
        }
    }
    /* label actions [for cb selected] */
    public function ctMassActions($action)
    {
        global $db, $class_filter;

        switch ($action) {
            case "cb-label-add":
            case "cb-label-clear":
                $lb_val = ($action == 'cb-label-add' and $_POST["label_add_val"] > 0) ? intval($_POST["label_add_val"]) : (($action == 'cb-label-clear' and $_POST["label_cl_val"] > 0) ? intval($_POST["label_cl_val"]) : null);
                if (isset($_POST["current_entry_id"]) and is_array($_POST["current_entry_id"]) and count($_POST["current_entry_id"]) > 0) {
                    foreach ($_POST["current_entry_id"] as $ct_id) {
                        $_fp  = ($action == 'cb-label-add' ? 'contact-add' : ($action == 'cb-label-clear' ? 'contact-del' : null));
                        $_act = VMessages::addToLabelActions($_fp, 1, $ct_id, $lb_val);
                    }
                }
                break;
            case "cb-delete":
                if (isset($_POST["current_entry_id"]) and is_array($_POST["current_entry_id"]) and count($_POST["current_entry_id"]) > 0) {
                    foreach ($_POST["current_entry_id"] as $ct_id) {
                        $_q .= sprintf(' OR `ct_id`="%s" ', $class_filter->clr_str($ct_id));
                        $_lb_cl = self::ctLabelClear($class_filter->clr_str($ct_id));
                    }
                    $cb_do = $db->execute(sprintf('DELETE FROM `db_usercontacts` WHERE `usr_id`="%s" AND %s LIMIT %s;', intval($_SESSION["USER_ID"]), '(' . substr($_q, 3) . ')', count($_POST["current_entry_id"])));
                }
                break;
        }
    }
    /* checkbox (cb) actions */
    public function cbActions($action, $notice = '')
    {
        global $cfg, $language;

        $error_message  = ((isset($_POST["current_entry_id"]) and count($_POST["current_entry_id"]) < 1) or !isset($_POST["current_entry_id"])) ? $language["notif.no.contacts.select"] : null;
        $notice_message = ($_POST and $error_message == '' and $action != 'cb-sendmsg') ? $language["notif.success.request"] : null;
        $notify         = VGenerate::noticeTpl('', $error_message, $notice_message);

        if ($error_message != '' and $action != 'cb-approve' and $action != 'block') {return $notify;} elseif ($error_message != '' and $action == 'cb-approve') {echo $notify;return false;}

        switch ($action) {
            case "cb-label-add":
            case "cb-label-clear":
            case "cb-delete":
                $do_cb = self::ctMassActions($action);
                $do_js = $cfg["message_count"] == 1 ? VGenerate::jqHtml('#message-menu-entry7-count', self::getAllContactCount()) : null;
                $do_js = ($cfg["message_count"] == 1 and $cfg["user_friends"] == 1) ? VGenerate::jqHtml('#mme7c1', self::getContactCount('message-menu-entry7-sub1')) : null;
                $do_js = ($cfg["message_count"] == 1 and $cfg["user_blocking"] == 1) ? VGenerate::jqHtml('#mme7c2', self::getContactCount('message-menu-entry7-sub2')) : null;
                switch ($_GET["s"]) {
                    case "message-menu-entry3":$d1 = '#new-comment-count';
                        $d2                            = 'comment-new';
                        break;
                    default:$d1 = '#new-message-count';
                        $d2         = 'message-new';
                        break;
                }
                echo $do_js = $action == 'cb-delete' ? VGenerate::declareJS('$("span.blued").removeClass("no-display"); $("' . $d1 . '").html("' . VMessages::messageCount($d2) . '");') : null;
                break;
            case "cb-addfr":
            case "cb-remfr":
                if ($notice == 1 and $action == 'cb-addfr') {
                    break;
                }

                $do_cb = self::ctAction($action);
                $do_js = ($cfg["message_count"] == 1 and $cfg["user_friends"] == 1) ? VGenerate::jqHtml('#mme7c1', self::getContactCount('message-menu-entry7-sub1')) : null;
                break;
            case "cb-sendmsg":
                $db_cb = self::messageSelected();
                break;
            case "block":
                global $db, $class_database;

                $do_db = $db->execute(sprintf("UPDATE `db_usercontacts` SET `ct_blocked`='1' WHERE `usr_id`='%s' AND `ct_username`='%s' AND `ct_blocked`='0' LIMIT 1;", intval($_SESSION["USER_ID"]), VUserinfo::getUserName(intval($_POST["section_reply_value"]))));
                $db_db = ($db->Affected_Rows() == 0) ? $class_database->doInsert('db_usercontacts', array("usr_id" => intval($_SESSION["USER_ID"]), "pwd_id" => VUserinfo::generateRandomString(10), "ct_username" => VUserinfo::getUserName(intval($_POST["section_reply_value"])), "ct_datetime" => date("Y-m-d H:i:s"), "ct_blocked" => 1)) : null;
                $do_js = ($cfg["message_count"] == 1 and $cfg["user_blocking"] == 1) ? VGenerate::jqHtml('#mme7c2', self::getContactCount('message-menu-entry7-sub2')) : null;
                $do_js = ($cfg["message_count"] == 1 and $cfg["user_blocking"] == 1) ? VGenerate::jqHtml('#message-menu-entry7-count', VMessages::MessageCount('message-menu-entry7')) : null;
                break;
            case "cb-block":
            case "cb-unblock":
                $do_cb = self::ctAction($action);
                $do_js = ($cfg["message_count"] == 1 and $cfg["user_blocking"] == 1) ? VGenerate::jqHtml('#mme7c2', self::getContactCount('message-menu-entry7-sub2')) : null;
                break;
        }
        if ($error_message == '' and $notice_message != '') {
            return $notify;
        }

    }
    /* update edited contact */
    public function updatedContactJS($update_id = '')
    {
        global $class_filter;

        $ct_name  = $class_filter->clr_str($_POST["frontend_global_name"]);
        $ct_mail  = $class_filter->clr_str($_POST["frontend_global_email"]);
        $ct_name1 = $ct_name == '' ? '&nbsp;' : '(' . $ct_name . ')';
        $ct_mail1 = $ct_mail == '' ? $language["frontend.global.none"] : $ct_mail;

        echo $all_js = VGenerate::declareJS('$("#ct-entryname-' . $update_id . '").html("' . $ct_name1 . '");');
        echo $all_js = VGenerate::declareJS('$("#ct-entrymail-' . $update_id . '").html("' . $ct_mail1 . '");');
        echo $all_js = ($ct_mail != '' ? VGenerate::declareJS('$("#ct-entrydetailsmail-' . $update_id . '").html("<a href=\"mailto:' . $ct_mail . '\">' . $ct_mail . '</a>");') : null);
        echo $all_js = (($ct_mail != '' and self::getContactUsername($update_id) == '') ? VGenerate::declareJS('$("#ct-entry-span' . $update_id . '>span").html("' . $ct_mail . '");') : null);
        echo $all_js = ($ct_mail != '' ? VGenerate::declareJS('$("#ct-entrymailinput-' . $update_id . '").val("' . $ct_mail . '");') : null);
    }
    /* get contact username from contact id */
    public function getContactUsername($ct_id)
    {
        global $db;
        $ctq        = $db->execute(sprintf("SELECT `ct_username` FROM `db_usercontacts` WHERE `ct_id`='%s' AND `usr_id`='%s' LIMIT 1;", intval($ct_id), intval($_SESSION["USER_ID"])));
        return $ctu = $ctq->fields["ct_username"];
    }
    /* contacts count */
    public function getContactCount($for)
    {
        return '(' . count(self::contactsListQuery($for)) . ')';
    }
    /* all contacts count, used on cb delete jq update */
    public function getAllContactCount()
    {
        global $db;
        $ct         = $db->execute(sprintf("SELECT COUNT(*) as `ct_total` FROM `db_usercontacts` WHERE `usr_id`='%s' AND `ct_active`='1' ORDER BY `ct_id` DESC;", intval($_SESSION["USER_ID"])));
        return $cts = '(' . $ct->fields["ct_total"] . ')';
    }
    /* build contact queries and get entries */
    public function contactsListQuery($forced = '')
    {
        global $db, $class_filter;

        $_s   = $class_filter->clr_str($_GET["s"]);
        $_for = $forced != '' ? $forced : $_s;

        switch ($_for) {
            case "message-menu-entry7-sub1": //friends
                $ctq = " AND `ct_friend`='1' ";
                break;
            case "message-menu-entry7-sub2": //blocked users
                $ctq = " AND `ct_blocked`='1' ";
                break;
            default:
                $lb_id = VMessages::getLabelID($_s);
                if ($lb_id > 0) {
                    $lq  = $db->execute(sprintf("SELECT `lb_ids` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_id`='%s' AND `lb_for`='%s' AND `lb_active`='1' LIMIT 1;", intval($_SESSION["USER_ID"]), $lb_id, VMessages::currentMenuEntry($_s)));
                    $lqs = $lq->fields["lb_ids"];
                    $lqa = $lqs != '' ? unserialize($lqs) : array();
                    if (count($lqa) > 0) {
                        foreach ($lqa as $lq_id) {
                            $ct_q .= "`ct_id`='" . $lq_id . "' OR ";
                        }
                        $ctq = ' AND (' . substr($ct_q, 0, -4) . ')';
                    } else {
                        $ctq = " AND `ct_id`='0'";
                    }

                }
        }
        $ct         = $db->execute(sprintf("SELECT `ct_id`, `ct_name`, `ct_username`, `ct_email`, `ct_friend`, `ct_blocked`, `ct_datetime`, `pwd_id` FROM `db_usercontacts` WHERE `usr_id`='%s' AND `ct_active`='1' %s ORDER BY `ct_id` DESC;", intval($_SESSION["USER_ID"]), $ctq));
        return $cts = $ct->getrows();
    }
    /* label checkboxes for add/edit */
    public function labelCheckboxes($ct_id = '')
    {
        global $db;
        $lb  = $db->execute(sprintf("SELECT `lb_name`, `lb_id` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_for`='%s' AND `lb_active`='1';", intval($_SESSION["USER_ID"]), VMessages::currentMenuEntry($_GET["s"])));
        $lbs = $lb->getrows();

        if (count($lbs) > 0) {
            foreach ($lbs as $lb_name) {
                $html .= VGenerate::simpleDivWrap('row no-top-padding edit-labels', 'labelCheckboxes' . $lb_name[1], '<input type="checkbox" id="dfc' . $lb_name[1] . '" style="display: none;" class="dfc" name="contact-del-from-label[]" value="' . $lb_name[1] . '" /><div class="icheck-box"><input type="checkbox" ' . self::selectedLabel($ct_id, $lb_name[1]) . ' id="efc' . $lb_name[1] . '" class="efc" name="contact-add-to-label[]" value="' . $lb_name[1] . '" /><label>' . $lb_name[0] . '</label></div>');
            }
            $js = '$(".efc").click(function(){ var this_val = $(this).val(); if ($(this).is(":checked")) { $("#labelCheckboxes"+this_val+" input[class=dfc]").prop("checked", false); } else { $("#labelCheckboxes"+this_val+" input[class=dfc]").prop("checked", true); } });';
            $js .= '$(".efc").on("ifUnchecked", function(event){ var this_val = $(this).val(); $("#labelCheckboxes"+this_val+" input[class=dfc]").prop("checked", true); });';
            $js .= '$(".efc").on("ifChecked", function(event){ var this_val = $(this).val(); $("#labelCheckboxes"+this_val+" input[class=dfc]").prop("checked", false); });';

            $html_js = VGenerate::declareJS($js);
        }
        return $html . $html_js;
    }
    /* right side contact details master wrapper */
    public function contactDetails()
    {
        global $language, $smarty;

        $html = VGenerate::simpleDivWrap('all-paddings7 bottom-border', '', '<article><h3 class="content-title"><i class="icon-user"></i><span class="ct-header" id="ct-header">' . $language["contacts.details.selected"] . ' (<span id="ct-header-count">' . (intval($_GET["n"]) > 0 ? '1' : '0') . '</span>)</span><span class="ct-header no-display" id="ct-header-new">' . $language["contact.add.new"] . '</span><span class="no-display ct-header" id="ct-header-edit">' . $language["contact.edit.ct"] . '</span></h3><div class="line"></div></article>');
        $html .= VGenerate::simpleDivWrap('empty-folder center no-margin' . (intval($_GET["n"]) > 0 ? ' no-display' : null), 'ct-no-details', $language["err.no.contacts"]);
        $html .= VGenerate::simpleDivWrap('left-float wdmax', 'ct-contact-details-wrap', self::rightContactsListing());
        return $html;
    }
    /* left side contacts menu + jquery */
    public function leftContactsList()
    {
        global $language, $class_filter;

        $cts = self::contactsListQuery();
        $_s  = isset($_GET["s"]) ? $class_filter->clr_str($_GET["s"]) : '';

        if (count($cts) > 0) {
            $html = '
                    <article>
                        <h3 class="content-title" style="margin-left: 10px;"><i class="icon-address-book"></i>' . $language["msg.entry.list"] . '</h3>
                        <div class="line"></div>
                    </article>
            ';
            foreach ($cts as $ct_val) {
                $ct_v = $ct_val[2] != '' ? $ct_val[2] : (($ct_val[2] == '' and $ct_val[3] != '') ? $ct_val[3] : null);
                $html .= '
                    <div class="row no-top-padding ct-entries pointer' . (intval($_GET["n"]) == $ct_val[0] ? ' ct-entry-bg' : null) . '" id="ct-entrylisting' . $ct_val[0] . '">
                        <div class="lh20 left-float left-margin7 icheck-box ct">
                            <input type="checkbox" ' . (intval($_GET["n"]) == $ct_val[0] ? 'checked="checked"' : null) . 'class="ct-entry-check" name="current_entry_id[]" value="' . $ct_val[0] . '">
                            <span class="left-float lh20 wd170" id="ct-entry-span' . $ct_val[0] . '">
                                <label class="ct-entry-name">' . $ct_v . '</label>
                            </span>
                        </div>
                    </div>';
            }
            /* click on entry name */
            $js1 = 'var p_id = $(this).parent().attr("id"); $("#contact-add-cancel").click();';
            $js1 .= 'if ($("#"+p_id.replace("-span", "listing")+" .icheck-box.ct>div:first").hasClass("checked")) {';
            $js1 .= '$("#"+p_id.replace("-span", "listing")+" .icheck-box.ct input").iCheck("check");';
            $js1 .= '}';
            $js1 .= '$(".ct-entries").removeClass("ct-entry-bg");';
            $js1 .= '$("#"+p_id.replace("-span", "listing")).addClass("ct-entry-bg");';
            $js1 .= '$("#ec-"+$("#"+p_id.replace("-span", "listing")+" div input").val()).removeClass("no-display");';
            $js1 .= '$("#ct-no-details").addClass("no-display");';
            $js1 .= '$("#"+p_id.replace("-span", "listing")+" .icheck-box.ct>div:first").addClass("checked");';
            $js1 .= '$("#"+p_id.replace("-span", "listing")+" .icheck-box.ct input").iCheck("check");';
            $js1 .= '$("#ct-header-count").html($(".ct-entry-check:checked").length);';

            /* click on checkbox */
            $js2 = '$(".ct-entries").removeClass("ct-entry-bg");';
            $js2 .= '$("#contact-add-cancel").click();';
            $js2 .= 'var c_sel = $("input.ct-entry-check:checked").length-1; $("#ct-header-count").html(c_sel);';
            $js2 .= 'if(c_sel > 0){$("#ct-no-details").addClass("no-display");}else{$("#ct-no-details").removeClass("no-display");}';
            $js2 .= 'if(c_sel == 0){$("#ec-"+$(this).val()).addClass("no-display");$("#ct-header-count").html("0");}else{$("#ec-"+$(this).val()).addClass("no-display");}';
            /* click on entry again */
            $js3 = 'var pr_id = $(this).parent().attr("id");';
            $js3 .= 'if($(this).next().is(":visible"))return;';
            $js3 .= '$(".ct-detailed").addClass("no-display").removeClass("no-display").hide();';
            $js3 .= '$(".ct-bullet-in").removeClass("ct-bullet-in");';
            $js3 .= '$("#"+pr_id+">div.ct-detailed").removeClass("no-display").stop().slideDown();';
            $js3 .= '$("#"+pr_id+">div.ct-bullet-out").addClass("ct-bullet-in");';
            $js3 .= '$("#ct-header-edit").addClass("no-display");';
            $js3 .= '$("#ct-header").removeClass("no-display");';
            /* click on bullet arrow */
            $js4 = 'var pr_id = $(this).parent().attr("id"); $("#"+pr_id+">div.ct-bullet-label").click();';
            /* click on unselect */
            $js5 = '$("#ct-entrylisting"+$(this).val()+" div input").attr("checked", false);';
            $js5 .= '$("#ct-entrylisting"+$(this).val()+" .icheck-box.ct>div:first").removeClass("checked");';
            $js5 .= '$("#ct-entrylisting"+$(this).val()+" .icheck-box.ct input").iCheck("uncheck");';
            $js5 .= '$("#ct-entrylisting"+$(this).val()).removeClass("ct-entry-bg");';
            $js5 .= '$("#ct-detailed-"+$(this).val()).addClass("no-display");';
            $js5 .= '$("#ec-"+$(this).val()+">div").removeClass("ct-bullet-in");';
            $js5 .= '$("#ec-"+$(this).val()).addClass("no-display");';
            $js5 .= '$("#ct-header-count").html($("input.ct-entry-check:checked").length);';
            $js5 .= 'if($("input.ct-entry-check:checked").length==0){$("#ct-no-details").removeClass("no-display");}';
            /* click on all open/all closed */
            $js6 = '$(".ct-bullet-out").addClass("ct-bullet-in"); $(".ct-detailed").removeClass("no-display").show(); resizeDelimiter();';
            $js7 = '$(".ct-bullet-in").removeClass("ct-bullet-in"); $(".ct-detailed").addClass("no-display"); resizeDelimiter();';
            /* click on edit */
            $js8 = '$("#ct-listwrap-"+$(this).val()).stop().slideUp();';
            $js8 .= '$("#ct-editwrap-"+$(this).val()).stop().slideDown();';
            $js8 .= '$("#ct-header-edit").removeClass("no-display");';
            $js8 .= '$("#ct-header").addClass("no-display");';
            /* click on edit/cancel */
            $js9 = '$("#ct-listwrap-"+$(this).attr("rel-val")).stop().slideDown();';
            $js9 .= '$("#ct-editwrap-"+$(this).attr("rel-val")).stop().slideUp();';
            $js9 .= '$("#ct-header-edit").addClass("no-display");';
            $js9 .= '$("#ct-header").removeClass("no-display");';
            $js9 .= '$("#error-message").click();';
            $js9 .= '$("#notice-message").click();';
            /* click on ct/bg */
            $js10 = 'var pr_id = $(this).parent().attr("id"); $("#"+pr_id+">div>button.edit-contact").click();';
            /* click on save changes */
            $js11 = 'var this_id = $(this).val(); var fr_id = $(this).parent().parent().parent().attr("id").replace("editwrap", "editform"); var pr_id = $(this).parent().parent().attr("id");';
            $js11 .= '$("#ec-"+this_id).mask(" "); $.post(current_url + menu_section + "?s=' . $_s . '&do=ctedit", $("#"+fr_id).serialize(), function(data){ $("#ct-noticewrap-"+this_id).html(data); resizeDelimiter(); $("#ec-"+this_id).unmask(); });';
            /* click on reset */
            $js12 = 'var pr_id = $(this).parent().parent().parent().parent().attr("id").replace("detailed", "editform"); $("#"+pr_id)[0].reset();';
            /* click on unblock */
            $js13 = 'var fr_id = "#ct-"+$(this).attr("id");';
            $js13 .= '$(fr_id).parent().mask(" "); $.post(current_url + menu_section + "?s=' . $_s . '&do=cb-unblock", $(fr_id).serialize(), function(data){ $("#siteContent").html(data); $(fr_id).parent().unmask(); });';
            /* click on remove from friends */
            $js14 = 'var fr_id = "#ct-"+$(this).attr("id");';
            $js14 .= '$(".col1").mask(" "); $.post(current_url + menu_section + "?s=' . $_s . '&do=cb-remfr", $(fr_id).serialize(), function(data){ $(".col1").html(data); $(".col1").unmask(); });';
            /* click on show block options */
            $js15 = 'var bl_id = $(this).attr("id"); $("#user-"+bl_id).stop().slideToggle();';
            /* click on the block options */
            //$js16     = 'var fr_id = "#"+$(this).parent().parent().parent().attr("id");';
            //$js16    .= '$.post(current_url + menu_section + "?s='.$_s.'&do=set-bl", $(fr_id).serialize(), function(data){ $(fr_id+"-update").html(data); });';
            $js161 = '$(".icheck-box input.set-bl-opt").on("ifChecked", function(event){ var fr_id = "#"+$(this).parent().parent().parent().attr("id"); $.post(current_url + menu_section + "?s=' . $_s . '&do=set-bl", $(fr_id).serialize(), function(data){ $(fr_id+"-update").html(data); }); });';
            $js161 .= '$(".icheck-box input.set-bl-opt").on("ifUnchecked", function(event){ var fr_id = "#"+$(this).parent().parent().parent().attr("id"); $.post(current_url + menu_section + "?s=' . $_s . '&do=set-bl", $(fr_id).serialize(), function(data){ $(fr_id+"-update").html(data); }); });';
            /* all js functions */
            //$all_js = '$(".ct-entry-name").click(function(){ ' . $js1 . ' });';
            $all_js .= '$(".ct-entry-check").click(function(){ ' . $js2 . ' });';
            $all_js .= '$(".ct-bullet-label").click(function(){ ' . $js3 . ' });';
            $all_js .= '$(".ct-bullet-out").click(function(){ ' . $js4 . ' });';
            $all_js .= '$(".unsel-contact").click(function(){ ' . $js5 . ' });';
            $all_js .= '$("#all-open-ct").click(function(){ ' . $js6 . ' });';
            $all_js .= '$("#all-close-ct").click(function(){ ' . $js7 . ' });';
            $all_js .= '$(".edit-contact").click(function(){ ' . $js8 . ' });';
            $all_js .= '$(".edit-cancel").click(function(){ ' . $js9 . ' });';
            $all_js .= '$(".ct-change").click(function(){ ' . $js10 . ' });';
            $all_js .= '$(".save-contact").click(function(){ ' . $js11 . ' });';
            $all_js .= '$(".reset-ct-form").click(function(){ ' . $js12 . ' });';
            $all_js .= '$(".unblock-wrap").click(function(){ ' . $js13 . ' });';
            $all_js .= '$(".friend-remove").click(function(){ ' . $js14 . ' });';
            $all_js .= '$(".show-bl-opt").click(function(){ ' . $js15 . ' });';
            //$all_js    .= '$(".set-bl-opt").click(function(){ '.$js16.' });';
            $all_js .= $js161;
            $all_js .= '$(".ct-change").mouseover(function(){$(this).addClass("y-bg");}).mouseout(function(){$(this).removeClass("y-bg");}); $(".ct-entry-name").mouseover(function(){ var p_id = $(this).parent().attr("id"); $("#"+p_id+">div>span").addClass("bold"); }).mouseout(function(){ var p_id = $(this).parent().attr("id"); $("#"+p_id+">div>span").removeClass("bold"); });';

            $html .= VGenerate::declareJS('$(document).ready(function() { ' . $all_js . ' });');
        } else {
            $html = '&nbsp;';
        }

        return $html;
    }
    /* right side selected contacts listing and editing contacts */
    public function rightContactsListing()
    {
        global $db, $class_database, $cfg, $language, $smarty;

        $cts = self::contactsListQuery();
        if (count($cts) > 0) {
            $smarty->assign('ct_details', '1');

            foreach ($cts as $ct_val) {
                $usr_key  = $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_user', $ct_val[2]);
                $usr_id   = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_user', $ct_val[2]);
                $uinfo    = $usr_id > 0 ? VUserinfo::getUserInfo($usr_id) : false;
                $ch_title = $uinfo["ch_title"] != '' ? $uinfo["ch_title"] : ($uinfo["usr_dname"] != '' ? $uinfo["usr_dname"] : $uinfo["uname"]);
                $ct_v     = $ct_val[2] != '' ? $ct_val[2] : (($ct_val[2] == '' and $ct_val[3] != '') ? '<span id="ct-entrymail-' . $ct_val[0] . '">' . $ct_val[3] . '</span>' : null);
                $ct_v .= $ct_val[1] == '' ? ' <span id="ct-entryname-' . $ct_val[0] . '"></span>' : ' <span id="ct-entryname-' . $ct_val[0] . '">(' . $ct_val[1] . ')</span>';
                $ct_g  = $ct_val[2] != '' ? VUserinfo::getUserID($ct_val[2]) : 0;
                $u_img = '<img title="" alt="" src="' . VUseraccount::getProfileImage($ct_g) . '" width="32" height="32" />';

                $html .= '<div class="ec-entry left-float wdmax ' . (intval($_GET["n"]) == $ct_val[0] ? '' : 'no-display') . '" id="ec-' . $ct_val[0] . '">';
                $html .= VGenerate::simpleDivWrap('left-float ct-bullet-out' . (intval($_GET["n"]) == $ct_val[0] ? ' ct-bullet-in' : null));
                $html .= VGenerate::simpleDivWrap('left-float ct-bullet-label greyed-out bold', '', $u_img . '<span class="pointer">' . $ct_v . '</span>' . ($ct_val[2] != '' ? ' / <a href="' . VHref::channelURL(["username" => $ct_val[2]]) . '">' . $ct_val[2] . '</a>' : null));

                $htm1 = '<div class="ct-listwrap" id="ct-listwrap-' . $ct_val[0] . '">';
                $htm1 .= '<form class="entry-form-class">';
                $htm1 .= VGenerate::simpleDivWrap('left-float darker-out wd300 ct-change', '', '<label>' . $language["frontend.global.email"] . ':</label><div class="grayText">' . ($ct_val[3] != '' ? '<span id="ct-entrydetailsmail-' . $ct_val[0] . '"><a href="mailto:' . $ct_val[3] . '">' . $ct_val[3] . '</a></span>' : '<span id="ct-entrydetailsmail-' . $ct_val[0] . '">' . $language["frontend.global.none"] . '</span>') . '</div>');

                $iv_stat = $db->execute(sprintf("SELECT `create_date` FROM `db_usercodes` WHERE (`type`='invite_contact' OR `type`='invite_user') AND `usr_id`='%s' AND `pwd_id`='%s' AND `code_active`='1' LIMIT 1;", intval($_SESSION["USER_ID"]), $ct_val[7]));
                $fr_stat = $ct_val[5] == 1 ? $language["contacts.details.blocked"] . '&nbsp;<a href="javascript:;" class="unblock-wrap" id="unblockform-' . $ct_val[0] . '">(' . $language["frontend.global.unblock"] . ')</a>' : (($ct_val[5] == 0 and $ct_val[2] != '' and $ct_val[4] == 1) ? $language["contacts.friends.since"] . date('M. d, Y', strtotime($ct_val[6])) . ' <a href="javascript:;" class="friend-remove" id="friendform-' . $ct_val[0] . '">(' . $language["contacts.friends.remove"] . ')</a>' : ((($ct_val[5] == 0 and $ct_val[2] == '' and $ct_val[4] == 0 and $iv_stat->fields["create_date"] != '') or ($ct_val[5] == 0 and $ct_val[2] != '' and $ct_val[4] == 0 and $iv_stat->fields["create_date"] != '')) ? $language["contacts.friends.sent"] . date('M. d, Y, H:i A', strtotime($iv_stat->fields["create_date"])) : (($cfg["approve_friends"] == 1 or $ct_val[2] == '') ? $language["contacts.add.noinvite"] : $language["contacts.add.nofriend"])));

                //$u_img     = VGenerate::simpleDivWrap('user-thumb-large pointer', '', '<img title="" alt="" src="'.VUseraccount::getProfileImage($ct_g).'" width="60" height="60" />');

                $htm1 .= VGenerate::simpleDivWrap('left-float darker-out wd300', '', '<label>' . $language["contacts.add.frstatus"] . '</label><div class="grayText">' . $fr_stat . ($ct_val[5] == 1 ? ' <span class=""><a href="javascript:;" class="show-bl-opt" id="block-options' . $ct_val[0] . '">' . $language["bl_options"] . '</a></span>' : null) . '</div>');
                $htm1 .= '</form>';
                /* block options */
                $htm1 .= $ct_val[5] == 1 ? VGenerate::simpleDivWrap('left-float all-paddings5', 'user-block-options' . $ct_val[0], self::blockCfg($ct_val[0]), 'display: none;') : null;
                $htm1 .= '<form class="entry-form-class">';
                $htm1 .= $cfg["custom_labels"] == 1 ? VGenerate::simpleDivWrap('left-float darker-out wd300 ct-change', '', '<label>' . $language["contacts.add.labels"] . '</label>' . self::listContactLabels($ct_val[0])) : null;
                $htm1 .= VGenerate::simpleDivWrap('left-float darker-out wd300', '', '<label>' . $language["contacts.add.chan"] . '</label> ' . ($ct_val[2] != '' ? '<a href="' . VHref::channelURL(["username" => $ct_val[2]]) . '">' . $ch_title . '</a>' : '<div class="grayText">' . $language["contacts.add.chan.nosub"] . '</div>'));
                $htm1 .= '<div class="clearfix"></div><br>';
                $htm1 .= VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'edit-contact', 'save-entry-button button-grey search-button form-button edit-contact', $ct_val[0], $ct_val[0], '<span>' . $language["contact.edit.ct"] . '</span>') . VGenerate::basicInput('button', 'unsel-contact', 'save-entry-button button-grey left-margin5 search-button form-button unsel-contact', $ct_val[0], $ct_val[0], '<span>' . $language["frontend.global.unsel"] . '</span>'));
                $htm1 .= '</form>';
                $htm1 .= '</div>';

                $htm2 = '<div class="clearfix"></div><div class="ct-listwrap" id="ct-editwrap-' . $ct_val[0] . '" style="display: none;"><form id="ct-editform-' . $ct_val[0] . '" class="ct-edit-form entry-form-class" method="post" action="">';
                $htm2 .= VGenerate::simpleDivWrap('row left-float no-top-padding', '', VGenerate::simpleDivWrap('darker-out', '', '<label>' . $language["frontend.global.name"] . '</label>') . VGenerate::basicInput('text', 'frontend_global_name', 'login-input', $ct_val[1]));
                $htm2 .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('darker-out row', '', '<label>' . $language["frontend.global.email"] . '</label>') . VGenerate::basicInput('text', 'frontend_global_email', 'login-input', $ct_val[3], 'ct-entrymailinput-' . $ct_val[0]));
                $htm2 .= $cfg["custom_labels"] == 1 ? VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float darker-out wd300', '', '<label>' . $language["contacts.add.labels"] . '</label>' . (self::labelCheckboxes($ct_val[0]) == '' ? '<div class="grayText">' . $language["frontend.global.none"] . '</div>' : self::labelCheckboxes($ct_val[0])))) : null;
                $htm2 .= '<div class="clearfix"></div><br>';
                $htm2 .= VGenerate::simpleDivWrap('left-float wd300 row', '', VGenerate::basicInput('button', 'save-contact', 'save-entry-button button-grey search-button form-button save-contact', $ct_val[0], $ct_val[0], '<span>' . $language["frontend.global.savechanges"] . '</span>') . '<a href="javascript:;" id="unsel-contact" rel-val="' . $ct_val[0] . '" class="link cancel-trigger edit-cancel reset-ct-form"><span>' . $language["frontend.global.cancel"] . '</span></a>');
                $htm2 .= '<input type="hidden" name="section_entry_value" value="' . $ct_val[0] . '" />';
                $htm2 .= '</form></div>';

                if ($cfg["user_blocking"] == 1 and $ct_val[5] == 1) {
//user unblock form
                    $htm2 .= '<form id="ct-unblockform-' . $ct_val[0] . '" class="ct-unblock-form" method="post" action=""><div class="left-float wd300 left-padding10 no-display" id="ct-unblockwrap-' . $ct_val[0] . '">';
                    $htm2 .= VGenerate::simpleDivWrap('left-float', '', '<input type="hidden" name="current_entry_id[]" value="' . $ct_val[0] . '" />');
                    $htm2 .= '</div></form>';
                }
                if ($cfg["user_friends"] == 1 and $ct_val[4] == 1 and $ct_val[5] == 0) {
//user friend remove form
                    $htm2 .= '<form id="ct-friendform-' . $ct_val[0] . '" class="ct-friend-form" method="post" action=""><div class="left-float wd300 left-padding10 no-display" id="ct-friendwrap-' . $ct_val[0] . '">';
                    $htm2 .= VGenerate::simpleDivWrap('left-float', '', '<input type="hidden" name="current_entry_id[]" value="' . $ct_val[0] . '" />');
                    $htm2 .= '</div></form>';
                }

                $notif = VGenerate::simpleDivWrap('ct-noticewrap', 'ct-noticewrap-' . $ct_val[0]);
                //$html    .= VGenerate::simpleDivWrap('left-float wdmax left-padding15 bottom-padding10 ct-detailed'.(intval($_GET["n"]) == $ct_val[0] ? NULL : ' no-display'), 'ct-detailed-'.$ct_val[0], $notif.VGenerate::simpleDivWrap('place-left user-thumb-large pointer', '', '<img title="" alt="" src="'.VUseraccount::getProfileImage($ct_g).'" width="60" height="60" />').$htm1.$htm2);
                $html .= VGenerate::simpleDivWrap('left-float wdmax left-padding15 bottom-padding10 ct-detailed' . (intval($_GET["n"]) == $ct_val[0] ? null : ' no-display'), 'ct-detailed-' . $ct_val[0], $notif . $htm1 . $htm2);
                $html .= '</div>';
                $html .= '<div class="clearfix"></div>';
            }
        }

        return $html;
    }
    /* get friend status */
    public function getFriendStatus($usr_id, $cache_time = false)
    {
        global $db;

        $sql = sprintf("SELECT `ct_id` FROM `db_usercontacts` WHERE `usr_id`='%s' AND `ct_username`='%s' AND `ct_friend`='1';", $usr_id, $_SESSION["USER_NAME"]);
        $fs  = $cache_time > 0 ? $db->CacheExecute($cache_time, $sql) : $db->execute($sql);

        if ($fs->fields["ct_id"] != '') {
            return 1;
        } else {
            return 0;
        }

    }
    /* get block status */
    public function getBlockStatus($usr_id, $ct_user, $cache_time = false)
    {
        global $db;

        $sql = sprintf("SELECT `ct_blocked` FROM `db_usercontacts` WHERE `usr_id`='%s' AND `ct_username`='%s' AND `ct_active`='1';", $usr_id, $ct_user);
        $bl  = $cache_time > 0 ? $db->CacheExecute($cache_time, $sql) : $db->execute($sql);

        return $bl->fields["ct_blocked"];
    }
    /* get block option */
    public function getBlockCfg($cfg_val, $usr_id, $ct_user, $cache_time = false)
    {
        global $db;

        $sql   = sprintf("SELECT `ct_block_cfg` FROM `db_usercontacts` WHERE `usr_id`='%s' AND `ct_username`='%s' LIMIT 1;", $usr_id, $ct_user);
        $bls   = $cache_time > 0 ? $db->CacheExecute($cache_time, $sql) : $db->execute($sql);
        $bl_ar = unserialize($bls->fields["ct_block_cfg"]);

        return $val = $bl_ar[$cfg_val] == 1 ? 1 : 0;
    }
    /* user block options */
    public function blockCfg($ct_id)
    {
        global $cfg, $db, $language;

        $bls = $db->execute(sprintf("SELECT `ct_blocked`, `ct_block_cfg` FROM `db_usercontacts` WHERE `ct_id`='%s' LIMIT 1;", $ct_id));
        if ($cfg["user_blocking"] == 1 and $bls->fields["ct_blocked"] == 1) {
            $bl_ar = unserialize($bls->fields["ct_block_cfg"]);
            $html .= '<form id="set-bl-form' . $ct_id . '" method="post" action="" class="entry-form-class set-bl-form">';
            $html .= VGenerate::simpleDivWrap('left-float', 'set-bl-form' . $ct_id . '-update', '');
            $html .= '<input type="hidden" name="ct_id" value="' . $ct_id . '" />';

            if (!empty($bl_ar)) {
                foreach ($bl_ar as $key => $val) {

                    $html .= VGenerate::simpleDivWrap('row no-top-padding icheck-box', '', '<input ' . ($val == 1 ? 'checked="checked"' : null) . ' id="' . $key . '" class="set-bl-opt" name="' . $key . '" value="1" type="checkbox"><label>' . $language[$key] . '</label>');
                }
            }
            $html .= '</form>';
        } else {
            return false;
        }

        return $html;
    }
    /* get user block post values */
    public function blockPostCfg($for)
    {
        return $value = intval($_POST[$for] == 1) ? 1 : 0;
    }
    /* save user block optios */
    public function setBlockCfg($ct_id = 0)
    {
        global $db;

        $ct_id      = ($_POST["ct_id"] > 0) ? intval($_POST["ct_id"]) : 0;
        $cfg_array  = serialize(array("bl_files" => self::blockPostCfg("bl_files"), "bl_channel" => self::blockPostCfg("bl_channel"), "bl_comments" => self::blockPostCfg("bl_comments"), "bl_messages" => self::blockPostCfg("bl_messages"), "bl_subscribe" => self::blockPostCfg("bl_subscribe")));
        $cfg_update = $db->execute(sprintf("UPDATE `db_usercontacts` SET `ct_block_cfg`='%s' WHERE `ct_id`='%s' AND `ct_blocked`='1' AND `usr_id`='%s' LIMIT 1;", $cfg_array, $ct_id, intval($_SESSION["USER_ID"])));
    }
    /* get labels for contact */
    public function contactLabelQuery($ct_id = '')
    {
        global $db;
        $lb  = $db->execute(sprintf("SELECT `lb_name`, `lb_ids`, `lb_id` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_active`='1' AND `lb_ids`!='';", intval($_SESSION["USER_ID"])));
        $lbs = $lb->getrows();
        if (count($lbs) > 0) {
            $lb_i = array();
            foreach ($lbs as $key => $lb_val) {
                $lb_ar = unserialize($lb_val[1]);
                $lb_t .= in_array($ct_id, $lb_ar) ? $lb_val[0] . '&nbsp+&nbsp;' : null;
                $lb_i[] .= in_array($ct_id, $lb_ar) ? $lb_val[2] : null;
            }
            $lb_t = $lb_t != '' ? substr($lb_t, 0, -7) : $lb_t;
            return array($lb_t, $lb_i);
        }
    }
    /* show text of contact labels */
    public function listContactLabels($ct_id = '')
    {
        global $language;

        $lb_t        = self::contactLabelQuery($ct_id);
        return $lb_t = '<div class="grayText">' . ($lb_t[0] != '' ? $lb_t[0] : $language["frontend.global.none"]) . '</div>';
    }
    /* checkboxes of contact labels */
    public function selectedLabel($ct_id = '', $lb_id = '')
    {
        $lb_t = self::contactLabelQuery($ct_id);
        if ($lb_t[1] == '') {
            return false;
        }

        if (in_array($lb_id, $lb_t[1])) {
            return 'checked="checked"';
        }

    }
}
