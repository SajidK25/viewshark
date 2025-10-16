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

class VMessages
{
    /* get number of unread messages */
    public static function getnew_nr()
    {
        global $db;

        $sql   = sprintf("SELECT COUNT(`msg_id`) AS `total` FROM `db_messaging` WHERE `msg_to`='%s' AND `msg_seen`='0' AND `msg_active`='1' AND `msg_inbox_deleted`='0';", (int) $_SESSION['USER_ID']);
        $rs    = $db->execute($sql);
        $total = $rs->fields['total'];

        if ($total > 0) {
            echo ' (' . $total . ')';
        }
    }
    /* delete label */
    public function deleteLabel()
    {
        global $db, $smarty, $class_filter;

        if ($_POST) {
            $id      = $class_filter->clr_str($_POST['input_label']);
            $id_arr  = explode('-', $id);
            $_id     = intval(substr(strstr($id, 'del'), 3));
            $_sid    = intval($_SESSION['USER_ID']);
            $_for    = $id_arr[0] . '-' . $id_arr[1] . '-' . $id_arr[2];
            $_delete = ($_sid > 0 and $_id > 0) ? $db->execute(sprintf("DELETE FROM `db_userlabels` WHERE `lb_id`='%s' AND `usr_id`='%s' AND `lb_for`='%s' LIMIT 1;", $_id, $_sid, $_for)) : null;

            if ($db->Affected_Rows() > 0) {
                echo 1;
            }
        }

        echo 0;
    }
    /* new label */
    public function addNewLabel()
    {
        global $class_database, $class_filter, $smarty, $db;

        $do_insert = 0;

        if ($_POST) {
            $smarty->assign('get_from', $class_filter->clr_str($_GET['s']));
            $insert_array = array(
                "usr_id"  => intval($_SESSION['USER_ID']),
                "lb_name" => $class_filter->clr_str($_POST['add_new_label']),
                "lb_for"  => $class_filter->clr_str($_GET['s']));

            $do_insert = ($_POST['add_new_label'] != '' and $class_database->doInsert('db_userlabels', $insert_array)) ? $db->Insert_ID() : 0;
        }

        echo $do_insert;
    }
    /* go to compose and process any message */
    public function composeMessage()
    {
        global $smarty;

        if ($_POST and $_GET['r'] != 1) {
            $process = $_GET['f'] == '' ? self::processMessage() : ($_GET['f'] == 'comm' ? self::processComment() : null);
            $notify  = ($process != '') ? VGenerate::noticeWrap(array($process[0], $process[1], VGenerate::noticeTpl('', $process[0], $process[1]))) : null;
        }
        $smarty->display('tpl_frontend/tpl_msg/tpl_compose.tpl');

        echo '<script type="text/javascript">$("#cb-response-wrap").prependTo("#compose-msg-form");</script>';
    }
    /* mark message as read */
    public function readMessage()
    {
        global $db, $class_filter;

        $mark_id = ($_GET['s'] == 'file-menu-entry7') ? $class_filter->clr_str($_POST['section_subject_value']) : intval($_POST['section_subject_value']);

        if ($_GET['s'] == 'message-menu-entry3') {
            $mark_db = $mark_id > 0 ? $db->execute(sprintf("UPDATE `db_channelcomments` SET `c_seen`='1' WHERE `c_id`='%s' AND `usr_id`='%s' LIMIT 1;", $mark_id, intval($_SESSION['USER_ID']))) : null;
        } elseif ($_GET['s'] == 'file-menu-entry7') {
            $type    = substr($class_filter->clr_str($_GET['for']), 5);
            $mark_db = $db->execute(sprintf("UPDATE `db_%scomments` SET `c_seen`='1' WHERE `c_key`='%s' LIMIT 1;", $type, $mark_id));
        } else {
            $mark_db = $mark_id > 0 ? $db->execute(sprintf("UPDATE `db_messaging` SET `msg_seen`='1' WHERE `msg_id`='%s' AND `msg_to`='%s' AND `msg_seen`='0' LIMIT 1;", $mark_id, intval($_SESSION['USER_ID']))) : null;
        }
    }
    /* css class to reflect new messages */
    public function newMessageClass($msg_id)
    {
        global $class_database, $class_filter;

        $_s = substr($class_filter->clr_str($_GET['s']), 0, 19);
        switch ($_s) {
            case "message-menu-entry2":
                return $class = $class_database->singleFieldValue('db_messaging', 'msg_seen', 'msg_id', $msg_id) == 0 ? ' new-message' : null;
                break;
            case "message-menu-entry3":
                return $class = $class_database->singleFieldValue('db_channelcomments', 'c_seen', 'c_id', $msg_id) == 0 ? ' new-message' : null;
                break;
        }
    }
    /* get id of current label */
    public function getLabelID()
    {
        global $class_filter;

        $get_array     = explode('-', $class_filter->clr_str($_GET['s']));
        return $sub_id = substr($get_array[3], 3);
    }
    /* name for current label */
    public function getLabelName()
    {
        global $db;
        $r = $db->execute(sprintf("SELECT `lb_name` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_for`='%s' AND `lb_id`='%s' LIMIT 1;", intval($_SESSION['USER_ID']), self::currentMenuEntry($_GET['s']), self::getLabelID()));
        return $r->fields['lb_name'];
    }
    /* renaming label */
    public function labelRename()
    {
        global $db, $class_filter;
        $_s          = $class_filter->clr_str($_GET['s']);
        $new_label   = $class_filter->clr_str($_POST['current_label_name']);
        $rename      = $new_label != '' ? $db->execute(sprintf("UPDATE `db_userlabels` SET `lb_name`='%s' WHERE `usr_id`='%s' AND `lb_for`='%s' AND `lb_id`='%s' LIMIT 1;", $new_label, intval($_SESSION['USER_ID']), self::currentMenuEntry($_s), self::getLabelID())) : null;
        echo $return = $db->Affected_Rows() > 0 ? VGenerate::declareJS('$("#' . $_s . '").addClass("menu-panel-entry-active");') : null;
    }
    /* selected menu entry */
    public function currentMenuEntry($for)
    {
        global $smarty, $class_filter;
        $for               = $class_filter->clr_str($for);
        $get_array         = explode('-', $for);
        $new_label         = $smarty->assign("show_new_label", (count($get_array) > 3 ? 'no' : 'yes'));
        return $menu_entry = $get_array[0] . '-' . $get_array[1] . '-' . $get_array[2];
    }
    /* build query for non label entries */
    public function noLabelQuery($lb_s = '')
    {
        global $db, $cfg, $class_filter;
        if ($cfg['custom_labels'] == 0) {return false;}

        $lb_fld = array();
        $lb_for = $lb_s != '' ? explode("-", $class_filter->clr_str($lb_s)) : ($_GET['s'] != '' ? explode("-", $class_filter->clr_str($_GET['s'])) : null);

        if (count($lb_for) == 3) {
            $lb_msg = $db->execute(sprintf("SELECT `lb_ids` FROM `db_userlabels` WHERE `lb_for`='%s' AND `usr_id`='%s' AND `lb_active`='1' AND `lb_ids`!='';", ($lb_s != '' ? $lb_s : self::currentMenuEntry($_GET['s'])), intval($_SESSION['USER_ID'])));
            $lb_fld = $lb_msg->getrows();
        }
        if (count($lb_fld) > 0 and count($lb_for) == 3) {
            foreach ($lb_fld as $lb_msg_arr) {
                $lb_msg_array = unserialize($lb_msg_arr[0]);
                foreach ($lb_msg_array as $lb_msg_id) {
                    $query .= "`msg_id` != '" . $lb_msg_id . "' AND ";
                }
            }
            return " AND (" . substr($query, 0, -5) . ") ";
        }
    }
    /* queries for inbox/outbox/spam */
    public function dbListQuery()
    {
        switch (self::currentMenuEntry($_GET['s'])) {
            case "message-menu-entry2":$db_add_query = "WHERE `msg_to`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='1' AND `msg_inbox_deleted`='0' AND `msg_invite`='0'" . self::noLabelQuery();
                $div_count                               = 'message-menu-entry6';
                break;
            case "message-menu-entry3":$db_add_query = "WHERE `usr_id`='" . intval($_SESSION['USER_ID']) . "'";
                break;
            case "message-menu-entry4":$db_add_query = "WHERE `msg_to`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='1' AND `msg_invite`='1'";
                $div_count                               = 'message-menu-entry4';
                break;
            case "message-menu-entry5":$db_add_query = "WHERE `msg_from`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='1' AND `msg_outbox_deleted`='0' AND `msg_invite`='0'" . self::noLabelQuery();
                $div_count                               = 'message-menu-entry5';
                break;
            case "message-menu-entry6":$db_add_query = "WHERE `msg_to`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='0' AND `msg_active_deleted`='0' AND `msg_invite`='0'" . self::noLabelQuery();
                $div_count                               = 'message-menu-entry2';
                break;
        }
        return $db_add_query;
    }
    /* labels, inc. friends and blocked */
    public function sectionLabel($id)
    {
        global $cfg, $class_filter, $db, $smarty, $language;

        $lb       = $db->execute(sprintf("SELECT `lb_id`, `lb_name` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_for`='%s' AND `lb_active`='1' ORDER BY `lb_id` ASC;", intval($_SESSION['USER_ID']), $id));
        $lb_array = $lb->getrows();
        $lb_count = count($lb_array);

        if ($lb_count > 0 or $id == 'message-menu-entry7') {
            $lb_js = '$(document).ready(function() { ' . $lb_j . ' $(".menu-panel-entry").mouseover(function() { var this_id = $("#"+$(this).attr("id")+" a.no-display").attr("id"); $("#"+this_id).removeClass("no-display").addClass("display"); }).mouseout(function(){ var this_id = $("#"+$(this).attr("id")+" a.display").attr("id"); $("#"+this_id).removeClass("display").addClass("no-display"); }); });';

            $html = '<li id="' . $id . '-sub-entries" style="display: block;" class="">';
            $html .= '<ul class="sort-nav">';

            if ($cfg['custom_labels'] == 1) {
                for ($i = 0; $i < $lb_count; $i++) {

                    $sub_label = $cfg['message_count'] == 1 ? '&nbsp;<span class="right-float mm-count" id="' . $id . '-sub' . $lb_array[$i][0] . '-count">' . self::messageCount($id . '-sub' . $lb_array[$i][0]) . '</span>' : null;

                    $html .= '<li id="' . $id . '-sub' . $lb_array[$i][0] . '" class="menu-panel-entry pl-entry">';
                    $html .= '<form id="label-form-' . $id . '-entry-del' . $lb_array[$i][0] . '" method="post" action="" class="entry-form-class">';
                    $html .= '<a href="javascript:;">';
                    $html .= '<span class="mm" style="margin-left: 0px;"><i class="icon-tag"></i>' . VUserinfo::truncateString($lb_array[$i][1], 24) . '</span>';
                    $html .= $sub_label;
                    $html .= '<span id="' . $id . '-entry-del' . $lb_array[$i][0] . '" class="label-del place-right right-margin10"><i class="icon-times" rel="tooltip" title="' . $language['frontend.global.remlabel'] . '"></i></span>';
                    $html .= '</a>';
                    $html .= '<input type="hidden" name="input_label" value="' . $id . '-entry-del' . $lb_array[$i][0] . '" />';
                    $html .= '</form>';
                    $html .= '</li>';
                }
            }
            $html .= ($id == 'message-menu-entry7' and $cfg['user_friends'] == 1) ? '<form id="label-form-' . $id . '-entry-friends" method="post" action="" class="entry-form-class"><li id="' . $id . '-sub1" class="menu-panel-entry"><a href="javascript:;"><i class="icon-users5"></i><span class="mm">' . $language['msg.entry.friends'] . '</span>' . ($cfg['message_count'] == 1 ? '&nbsp;<span class="right-float mm-count" id="mme7c1">' . VContacts::getContactCount("message-menu-entry7-sub1") . '</span>' : null) . '</a></li></form>' : null;
            $html .= ($id == 'message-menu-entry7' and $cfg['user_blocking'] == 1) ? '<form id="label-form-' . $id . '-entry-blocked" method="post" action="" class="entry-form-class"><li id="' . $id . '-sub2" class="menu-panel-entry"><a href="javascript:;"><i class="icon-blocked"></i><span class="mm">' . $language['msg.entry.blocked.users'] . '</span>' . ($cfg['message_count'] == 1 ? '&nbsp;<span class="right-float mm-count" id="mme7c2">' . VContacts::getContactCount("message-menu-entry7-sub2") . '</span>' : null) . '</a></li></form>' : null;
            $html .= '</ul>';
            $html .= '</li>';
            $html .= VGenerate::declareJS($lb_js);
        }
        return $html;
    }
    /* add to label, remove from label menus */
    public function addToLabel($for, $type = false)
    {
        global $language, $db, $cfg, $class_database, $class_filter;

        $cfg[] = $class_database->getConfigurations("file_counts");
        switch ($for) {
            case "tpl_pl":
                $type    = !$type ? $class_filter->clr_str($_GET['t']) : $type;
                $menu_db = null;
                $type    = ($type == '' and $menu_db != '' and $cfg[($menu_db != 'doc' ? $menu_db : 'document') . "_module"] == 1) ? $menu_db : $type;
                $db_tbl  = $type == '' ? ($cfg['video_module'] == 1 ? 'video' : ($cfg['short_module'] == 1 ? 'short' : ($cfg['live_module'] == 1 ? 'live' : ($cfg['image_module'] == 1 ? 'image' : ($cfg['audio_module'] == 1 ? 'audio' : ($cfg['document_module'] == 1 ? 'doc' : null)))))) : $type;

                $lb       = $db->execute(sprintf("SELECT `pl_id`, `pl_name` FROM `db_%splaylists` WHERE `usr_id`='%s';", $db_tbl, intval($_SESSION['USER_ID'])));
                $lb_add   = $language['files.action.pl.add'] . ' [<i class="iconBe-plus f10"></i>]';
                $lb_clear = $language['files.action.pl.clear'] . ' [<i class="iconBe-minus f10"></i>]';
                $li_class = 'count file-action';
                $id       = 'playlist-' . $type;
                $icon     = 'icon-list';
                break;
            default:
                $lb       = $db->execute(sprintf("SELECT `lb_id`, `lb_name` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_for`='%s' AND `lb_active`='1' ORDER BY `lb_id` ASC;", intval($_SESSION['USER_ID']), $for));
                $lb_add   = $language['msg.label.add'] . ' [<i class="iconBe-plus f10"></i>]';
                $lb_clear = $language['msg.label.clear'] . ' [<i class="iconBe-minus f10"></i>]';
                $li_class = 'count-label';
                $id       = 'cb-label';
                $icon     = 'icon-tag';
                $type[0]  = 'v';
                break;
        }

        $lb_array = $lb->getrows();
        $lb_count = count($lb_array);

        if ($lb_count > 0) {
            for ($i = 0; $i < $lb_count; $i++) {
                $p_total = 0;
                $li_add .= '<li class="' . $li_class . ' cb-label-add" id="cb-label-add' . $lb_array[$i][0] . '"><a href="javascript:;"><i class="' . $icon . '"></i> ' . VUserinfo::truncateString($lb_array[$i][1], 17);
                $li_add .= '<form id="label-add-form' . $lb_array[$i][0] . '" class="label-form-class entry-form-class" method="post" action="">';
                $li_add .= '<input type="hidden" name="label_add_val" value="' . $lb_array[$i][0] . '" />';
                $li_add .= '</form></a>';
                $li_add .= '</li>';
                $li_cl .= '<li class="' . $li_class . ' cb-label-clear" id="cb-label-clear' . $lb_array[$i][0] . '"><a href="javascript:;"><i class="' . $icon . '"></i> ' . VUserinfo::truncateString($lb_array[$i][1], 17);
                $li_cl .= '<form id="label-cl-form' . $lb_array[$i][0] . '" class="label-form-class entry-form-class" method="post" action="">';
                $li_cl .= '<input type="hidden" name="label_cl_val" value="' . $lb_array[$i][0] . '" />';
                $li_cl .= '</form></a>';
                $li_cl .= '</li>';
            }

            $html = '<li class="count2 pl-menu' . ($type[0] != 'v' ? ' hidden' : null) . '" id="' . $id . '-add"><a href="javascript:;"><i class="' . $icon . '"></i> ' . $lb_add . ' </a><ul class="dl-submenu">';
            $html .= $li_add;
            $html .= '</ul></li><li class="count2 pl-menu' . ($type[0] != 'v' ? ' hidden' : null) . '" id="' . $id . '-remove"><a href="javascript:;"><i class="' . $icon . '"></i> ' . $lb_clear . '</a><ul class="dl-submenu">';
            $html .= $li_cl;
            $html .= '</ul></li>';
        }
        return $html;
    }
    /* js update main label count */
    public function labelTotal()
    {
        global $cfg, $class_filter;

        $_s = $class_filter->clr_str($_GET['s']);

        echo VGenerate::declareJS('$(document).ready(function(){ $("#message-menu-entry2-count").html("' . self::messageCount('message-menu-entry2') . '"); $("#message-menu-entry3-count").html("' . self::messageCount('message-menu-entry3') . '"); $("#message-menu-entry5-count").html("' . self::messageCount('message-menu-entry5') . '");  $("#message-menu-entry6-count").html("' . self::messageCount('message-menu-entry6') . '"); $("#' . $_s . '-count").html("' . self::messageCount($_s) . '"); });');
    }
    /* js update sub label count */
    public function subLabelTotal($msg_id)
    {
        global $db, $cfg, $class_filter;
        if ($cfg['custom_labels'] == 0) {return false;}

        $_s = $class_filter->clr_str($_GET['s']);

        $q  = $db->execute("SELECT `lb_ids`, `lb_id` FROM `db_userlabels` WHERE `usr_id`='" . intval($_SESSION['USER_ID']) . "' AND `lb_for`='" . self::currentMenuEntry($_s) . "' AND `lb_ids` LIKE '%;i:" . $msg_id . ";%';");
        $qa = $q->getrows();
        if (count($qa) > 0) {
            foreach ($qa as $unval) {
                $q_ar              = unserialize($unval[0]);
                $r_ar              = array();
                $r_ar[]            = $msg_id;
                $f_ar              = array_values(array_diff($q_ar, $r_ar));
                $f_val             = (count($f_ar) > 0 ? serialize($f_ar) : '');
                $f_q               = $db->execute(sprintf("UPDATE `db_userlabels` SET `lb_ids`='%s' WHERE `lb_id`='%s' AND `usr_id`='%s' AND `lb_for`='%s' LIMIT 1;", $f_val, $unval[1], intval($_SESSION['USER_ID']), self::currentMenuEntry($$_s)));
                echo $update_total = VGenerate::declareJS('$(document).ready(function(){$("#' . self::currentMenuEntry($_s) . '-sub' . $unval[1] . '-count").html("(' . count($f_ar) . ')"); });');
            }
        }
    }
    /* add/remove from label */
    public function addToLabelActions($action_type, $count, $extra_ar_id = '', $extra_lb_id = '')
    {
        global $db, $class_filter;

        $_ar       = array();
        $_tolabel  = $action_type == 'cb-label-add' ? intval($_POST['label_add_val']) : (($action_type == 'contact-add' or $action_type == 'contact-del') ? $extra_lb_id : intval($_POST['label_cl_val']));
        $for_label = self::currentMenuEntry($class_filter->clr_str($_GET['s']));
        $_p        = $db->execute(sprintf("SELECT `lb_ids` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_for`='%s' AND `lb_id`='%s' AND `lb_active`='1' LIMIT 1;", intval($_SESSION['USER_ID']), $for_label, $_tolabel));
        $_msg      = $_p->fields['lb_ids'];
        $_arr      = array();

        if ($_msg != '') {
            $_arr = unserialize($_msg);
            $_ct  = count($_arr);
        }

        if ($extra_lb_id == '') {
            for ($i = 0; $i < $count; $i++) {
                $_ar[$i] = intval($_POST['current_entry_id'][$i]);
            }
        } else {
            $_ar[] = $extra_ar_id;
        }

        $_array       = ($_ct > 0 and ($action_type == 'cb-label-add' or $action_type == 'contact-add')) ? array_merge($_arr, $_ar) : (($_ct > 0 and ($action_type == 'cb-label-clear' or $action_type == 'contact-del')) ? array_diff($_arr, $_ar) : (($action_type == 'cb-label-add' or $action_type == 'contact-add') ? $_ar : $_arr));
        $_final_array = $_ct > 0 ? array_values(array_unique($_array)) : $_array;
        $_do          = $db->execute(sprintf("UPDATE `db_userlabels` SET `lb_ids`='%s' WHERE (`lb_id`='%s' AND `usr_id`='%s' AND `lb_for`='%s');", (count($_final_array) > 0 ? serialize($_final_array) : ''), $_tolabel, intval($_SESSION['USER_ID']), $for_label));
        echo $_cdo    = VGenerate::declareJS("$('#" . $for_label . "-sub" . $_tolabel . "-count').html('(" . (is_array($_final_array) ? count($_final_array) : 0) . ")');");
    }
    /* count messages */
    public function messageCount($for)
    {
        global $db, $cfg;

        switch ($for) {
            case "message-menu-entry3":
            case "comment-new":break;
            case "message-new":
                $db_add_query = "WHERE `msg_to`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='1' AND `msg_inbox_deleted`='0' AND `msg_invite`='0' AND `msg_seen`='0' ";
                break;
            case "invites-new":
                $db_add_query = "WHERE `msg_to`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='1' AND `msg_inbox_deleted`='0' AND `msg_invite`='1' AND `msg_seen`='0' ";
                break;
            case "message-menu-entry2":
                $db_add_query = "WHERE `msg_to`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='1' AND `msg_inbox_deleted`='0' AND `msg_invite`='0'" . self::noLabelQuery($for);
                break;
            case "message-menu-entry4":
                $db_add_query = "WHERE `msg_to`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='1' AND `msg_invite`='1'";
                break;
            case "message-menu-entry5":
                $db_add_query = "WHERE `msg_from`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='1' AND `msg_outbox_deleted`='0' AND `msg_invite`='0'" . self::noLabelQuery($for);
                break;
            case "message-menu-entry6":
                $db_add_query = "WHERE `msg_to`='" . intval($_SESSION['USER_ID']) . "' AND `msg_active`='0' AND `msg_active_deleted`='0' AND `msg_invite`='0'" . self::noLabelQuery($for);
                break;
            case "message-menu-entry7":
                return VContacts::getAllContactCount();
                break;
            default:
                if ($cfg['custom_labels'] == 0) {return false;}

                $lb_id = substr(strstr($for, "sub"), 3);
                if ($lb_id > 0) {
                    $for  = self::currentMenuEntry($for);
                    $_p   = $db->execute(sprintf("SELECT `lb_ids` FROM `db_userlabels` WHERE `usr_id`='%s' AND `lb_for`='%s' AND `lb_id`='%s' AND `lb_active`='1' LIMIT 1;", intval($_SESSION['USER_ID']), $for, $lb_id));
                    $_msg = $_p->fields['lb_ids'];
                    if ($_msg != '') {
                        $_arr = unserialize($_msg);
                        $_ct  = count($_arr);
                        return '(' . $_ct . ')';
                    } else {
                        return '(0)';
                    }

                }
                break;
        }

        $q_totalres = ($for == 'message-menu-entry3' or $for == 'comment-new') ? $db->execute(sprintf("SELECT COUNT(*) AS `total` FROM `db_channelcomments` WHERE `usr_id`='%s'%s;", intval($_SESSION['USER_ID']), ($for == 'comment-new' ? " AND `c_seen`='0'" : null))) : $db->execute(sprintf("SELECT COUNT(*) AS `total` FROM `%s` " . $db_add_query, 'db_messaging'));
        return '(' . $q_totalres->fields['total'] . ')';
    }
    /* message details/body */
    public function messageDetails($_dsp = 'none', $entry_id = '', $db_id = '', $msg_body = '', $from_user = '', $from_id = '')
    {
        global $cfg, $language, $class_filter, $class_database;

        $section  = VMessages::currentMenuEntry($_GET['s']);
        $msg_id   = $class_database->singleFieldValue('db_messaging', 'msg_from', 'msg_id', $db_id);
        $_att     = self::messageDetailsAttachment($db_id);
        $_reply   = $msg_id > 0 ? VGenerate::simpleDivWrap('place-left', '', VGenerate::basicInput('button', ($section == 'message-menu-entry4' ? 'approve_invite' : 'message_reply'), 'save-entry-button button-grey search-button form-button ' . ($section == 'message-menu-entry4' ? 'approve-invite' : 'reply-msg') . ' ', '', $entry_id, ($section == 'message-menu-entry4' ? '<span>' . $language['frontend.global.approve'] . '</span>' : '<span>' . $language['frontend.global.reply'] . '</span>'))) : null;
        $_delete  = VGenerate::simpleDivWrap('place-left wd250 left-align left-padding5', '', VGenerate::basicInput('button', $db_id, 'save-entry-button button-grey search-button form-button ' . ($section == 'message-menu-entry4' ? 'ignore-invite' : 'delete-msg') . ' ', '', $entry_id, ($section == 'message-menu-entry4' ? '<span>' . $language['frontend.global.ignore'] . '</span>' : '<span>' . $language['frontend.global.delete'] . '</span>')));
        $_actions = ($_GET['s'] != 'message-menu-entry5' and $_SESSION['USER_ID'] != self::getMessageInfo('msg_from', $db_id)) ? VGenerate::simpleDivWrap('place-left', '', '<span class="left-align">' . (($cfg['user_blocking'] == 1 and $_SESSION['USER_NAME'] != $from_user and $msg_id > 0) ? VGenerate::basicInput('button', $db_id, 'save-entry-button button-grey search-button form-button block-user', '', $entry_id, '<span>' . $language['msg.details.block'] . '</span>') : null)) : null;
        $_attach  = $_att != '' ? VGenerate::simpleDivWrap('place-left', '', '<button onfocus="blur();" onclick="$(\'.m' . $db_id . '\').stop().slideToggle(\'fast\'); return;" value="1" type="button" class="save-entry-button button-grey search-button form-button toggle-attach" id="btn-attch' . $entry_id . '-' . $db_id . '" name=""><span>' . $language['msg.details.att.show'] . '</span></button>') : null;
        $from     = VUserinfo::getUserInfo($from_id);

        $html = '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="msg-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('left-float wdmax', '', VGenerate::simpleDivWrap('wd90 place-left left-padding5 no-top-padding', '', '<span class="user-thumb-large-off"><a href="' . ($msg_id > 0 ? VHref::channelURL(["username" => $from['uname']]) : 'javascript:;') . '"><img src="' . VUseraccount::getProfileImage(self::getMessageInfo((self::currentMenuEntry($_GET['s']) == 'message-menu-entry5' ? 'msg_to' : 'msg_from'), $db_id)) . '" alt="" height="60" /></a></span>') . VGenerate::simpleDivWrap('msg-body', '', '<pre>' . $msg_body . '</pre>') . '<div class="clearfix"></div>');
        $html .= VGenerate::simpleDivWrap('msg-buttons d-flex', '', $_attach . $_reply . $_delete . $_actions);
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('', '', $_att);
        $html .= '<div class="clearfix"></div>';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_reply_value" value="' . ($_GET['s'] == 'message-menu-entry3' ? $class_database->singleFieldValue('db_channelcomments', 'c_usr_id', 'c_id', $db_id) : self::getMessageInfo('msg_from', $db_id)) . '" />';
        $html .= '<input type="hidden" name="section_subject_value" value="' . $db_id . '" />';
        $html .= '</form></div>';

        return $html;
    }
    /* attachments in message details */
    public function messageDetailsAttachment($db_id, $style = '')
    {
        global $db, $cfg, $class_database;

        $cfg[]     = $class_database->getConfigurations('thumbs_width,thumbs_height');
        $rs        = $db->execute(sprintf("SELECT `msg_live_attch`, `msg_video_attch`, `msg_short_attch`, `msg_image_attch`, `msg_audio_attch`, `msg_doc_attch`, `msg_blog_attch` FROM `db_messaging` WHERE `msg_id`='%s' LIMIT 1;", intval($db_id)));
        $mod_array = array("live" => "live", "video" => "video", "short" => "short", "image" => "image", "audio" => "audio", "document" => "doc", "blog" => "blog");

        $html = '';

        foreach ($mod_array as $db_key => $mod) {
            if ($cfg[$db_key . "_module"] == 1) {
                $f_key  = $rs->fields["msg_" . $mod . "_attch"];
                $f_info = VFiles::getFileInfo($f_key);

                if ($f_key != '0') {
                    $html .= '<div class="m' . $db_id . ' attach-ct"' . ($style == '' ? ' style="display: none;"' : null) . '>';
                    $html .= $style == '' ? '<div class="">' : '<div style="float: left; width: 100%; clear: both; padding-top: 7px;">';
                    $html .= $style == '' ? '<div class="list-file-thumb place-left">' : '<div style="float: left; margin-right: 5px; border: 1px solid #D3D3D3; padding: 2px;">';
                    $html .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($mod[0], $f_key, '') . '" class="a-thumb-wrap-larger"' . ($style != '' ? ' style="display: inline-block; float: left; position: relative;"' : null) . '>';
                    $html .= $style == '' ? '<span class="file-thumb-wrap file-thumb-wd128"><span class="file-img">' : '<span position: relative;"><span style="display: block;">';

                    $u_info      = VUserinfo::getUserInfo($class_database->singleFieldValue('db_' . $mod . 'files', 'usr_id', 'file_key', $f_key));
                    $u_key       = $u_info['key'];
                    $thumb       = $f_key;
                    $thumb_cache = $f_info['thumb_cache'];
                    $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;
                    $tmb_url     = $style == '' ? VGenerate::thumbSigned($db_key, $thumb, array($u_key, $thumb_cache)) : VGenerate::thumbURL($mod, $u_key, $f_key);
                    $tmb_img     = $thumb != '' ? '<img height="120" alt="" src="' . $tmb_url . '" title="" />' : null;

                    $html .= $tmb_img;
                    $html .= '</span></span>';
                    $html .= '</a></div>';
                    $html .= $style == '' ? '<div class="msg-about">' : '<div style="float: left; width: 350px;">';
                    $html .= '<div class="row">';
                    $html .= '<a class="file-title wrapword" href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($mod[0], $f_key, $f_info['title']) . '">' . $f_info['title'] . '</a>';
                    $html .= $style == '' ? '<div class="row"><pre>' . VUserinfo::truncateString($f_info['description'], 250) . '</pre></div>' : '<div style="clear: both; padding-top: 7px;"><pre>' . $f_info['description'] . '</pre></div>';
                    $html .= '</div>';
                    $html .= '</div></div>';
                    $html .= '</div><div class="clearfix"></div>';
                }
            }
        }
        return $html;
    }
    /* select list of files */
    public function fileListSelect($for)
    {
        global $db, $cfg, $language, $class_filter, $class_database;

        /* uploads */
        $sql_u = intval($_SESSION['USER_ID']);
        $sql_a = "A.`usr_id`='" . $sql_u . "'";
        $sql_b = "A.`privacy`!='personal' AND A.`approved`='1' AND A.`active`='1'";
        $sql   = sprintf("SELECT A.`file_title`, A.`file_key`, A.`privacy` FROM `db_%sfiles` A WHERE %s AND %s;", $for, $sql_a, $sql_b);

        $rs    = $db->execute($sql);
        $names = $rs->getrows();

        $html = '<select name="' . $for . '_attachlist" class="message-attach signup-select" onchange="$(\'#input-loc\').val(this.options[this.selectedIndex].text);">';
        $html .= '<option value="" class="italic">' . $language["msg.label.attch.own." . $for[0]] . '</option>';
        //if(count($names) > 0){
        if (is_array($names)) {
            foreach ($names as $key => $val) {
                $sel = $_POST[$for . "_attachlist"] == $val[1] ? ' selected="selected"' : null;
                $html .= '<option' . $sel . ' value="' . $val[1] . '">' . VUserinfo::truncateString($val[0], 70) . ($val[2] == 'private' ? $language['msg.label.private'] : null) . '</option>';
            }
        }
        /* favorites */
        if ($cfg['file_favorites'] == 0) {$html .= '</select>';return $html;}

        if ($cfg['file_favorites'] == 1) {
            $html .= '<option value="" class="italic">' . $language["msg.label.attch.fav." . $for[0]] . '</option>';

            $sql_f = sprintf("SELECT A.`file_title`, A.`file_key`, A.`privacy`, B.`db_id` FROM `db_%sfiles` A, `db_%sfavorites` B WHERE B.`usr_id`='%s' AND A.`file_key`=B.`file_key` AND A.`approved`='1';", $for, $for, $sql_u);
            $rs    = $db->execute($sql_f);
            $favs  = $rs->getrows();

            if (count($favs) > 0) {
                foreach ($favs as $fkey => $fval) {
                    $sel = $_POST[$for . "_attachlist"] == 'f' . $fval[1] ? ' selected="selected"' : null;
                    $html .= '<option' . $sel . ' value="f' . $fval[1] . '">' . VUserinfo::truncateString($fval[0], 70) . ($fval[2] == 'private' ? $language['msg.label.private'] : null) . '</option>';
                }
            }
        }
        $html .= '</select>';
        $post    = isset($_POST[$for . "_attachlist"]) ? $class_filter->clr_str($_POST[$for . "_attachlist"]) : null;
        $postval = $class_database->singleFieldValue('db_' . $for . 'files', 'file_title', 'file_key', (strlen($post) == 12 ? substr($post, 1) : $post));
        $html .= '<input type="hidden" readonly="readonly" name="' . $for . '_attachlist_sel" class="login-input" id="input-loc" value="' . $postval . '" />';
        $html .= '<input type="hidden" readonly="readonly" name="' . $for . '_attachlist_tmp" class="login-input no-display" value="' . $postval . '" />';

        return $html;
    }

    /* some message details */
    public function getMessageInfo($get, $msg_id)
    {
        global $db;
        $dbq = $db->execute(sprintf("SELECT `%s` FROM `db_messaging` WHERE `msg_id`='%s' LIMIT 1;", $get, intval($msg_id)));

        return $dbq->fields[$get];
    }
    /* processing a comment reply */
    public function processComment()
    {
        global $class_filter, $language;

        $msg_to         = VUserinfo::isValidUsername($class_filter->clr_str($_POST['msg_label_to'])) ? trim($class_filter->clr_str($_POST['msg_label_to'])) : null;
        $allowedFields  = array('msg_label_to', 'upage_text_mod_comment_single');
        $requiredFields = $allowedFields;

        $error_message  = VForm::checkEmptyFields($allowedFields, $requiredFields);
        $error_message  = $error_message == '' ? VUserpage::postComment(1) : $error_message;
        $notice_message = $error_message == 1 ? $language['upage.text.comm.posted.appr'] : ($error_message == 2 ? $language['notif.success.request'] : null);
        $error_message  = ($error_message == 1 or $error_message == 2) ? null : $error_message;

        return array($error_message, $notice_message);
    }
    /* sending a new message */
    public function processMessage()
    {
        global $cfg, $language, $class_database, $class_filter, $db;

        $check = $cfg['internal_messaging'] == 0 ? die : null;

        $form_fields    = VArraySection::getArray('compose_message');
        $msg_to         = VUserinfo::isValidUsername($class_filter->clr_str($_POST['msg_label_to'])) ? trim($class_filter->clr_str($_POST['msg_label_to'])) : null;
        $u_array        = array_unique(explode(',', $msg_to));
        $u_count        = count($u_array);
        $allowedFields  = $form_fields[1];
        $requiredFields = $form_fields[2];
        $clear_arr      = array();

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        $error_message = ($error_message == '' and $msg_to == '') ? $language['err.send.multi'] : $error_message;
        $error_message = ($error_message == '' and $u_count > 1 and $cfg['allow_multi_messaging'] == 0) ? $language['err.send.multi'] : $error_message;
        $error_message = ($error_message == '' and !VUserinfo::existingUsername($msg_to) and $u_count == 1) ? $language['err.send.nouser'] . $msg_to : (($cfg['allow_self_messaging'] == 0 and $_SESSION['USER_NAME'] == $msg_to) ? $language['err.send.self'] : $error_message);

        if ($error_message == '' and $u_count > 1 and $cfg['allow_multi_messaging'] == 1) {
            foreach ($u_array as $val) {
                if ($val == '' or !VUserinfo::existingUsername($val) or ($val == $_SESSION['USER_NAME'] and $cfg['allow_self_messaging'] == 0)) {
                    $error_message = $language['err.send.multi'];
                    break;
                }
                $block_status = VContacts::getBlockStatus(VUserinfo::getUserId($val), $_SESSION['USER_NAME']);
                $block_option = VContacts::getBlockCfg("bl_messages", VUserinfo::getUserId($val), $_SESSION['USER_NAME']);

                if ($block_status == 1 and $block_option == 1) {
                    $error_message = $val . $language['err.no.messages'];
                    break;
                }
            }
            $error_message = ($error_message == '' and $cfg['multi_messaging_limit'] > 0 and $u_count > $cfg['multi_messaging_limit']) ? $language['err.send.multi'] : $error_message;

            if ($error_message == '') {
                foreach ($u_array as $key => $val) {
                    $form_fields[0]['msg_to'] = VUserinfo::getUserID(trim($val));
                    $notice_message           = ($error_message == '' and $class_database->doInsert('db_messaging', $form_fields[0])) ? $language['notif.send.success'] : null;
                    $db_id                    = $error_message == '' ? $db->Insert_ID() : 0;

                    if ($error_message == '' and $class_database->singleFieldValue('db_accountuser', 'usr_mail_privmessage', 'usr_id', $form_fields[0]['msg_to']) == 1) {
                        $clear_arr[] = VUserinfo::getUserEmail($form_fields[0]['msg_to']);
                    }

                }
                $notification = $error_message == '' ? self::PMnotification('private_message', $clear_arr, $db_id) : null;
            }
        } elseif ($error_message == '' and $u_count == 1) {
            $u_id           = VUserinfo::getUserId($msg_to);
            $block_status   = VContacts::getBlockStatus($u_id, $_SESSION['USER_NAME']);
            $block_option   = VContacts::getBlockCfg("bl_messages", $u_id, $_SESSION['USER_NAME']);
            $error_message  = ($block_status == 1 and $block_option == 1) ? $msg_to . $language['err.no.messages'] : null;
            $notice_message = ($error_message == '' and $class_database->doInsert('db_messaging', $form_fields[0])) ? $language['notif.send.success'] : null;
            $db_id          = $error_message == '' ? $db->Insert_ID() : 0;

            $clear_arr[0] = VUserinfo::getUserEmail($u_id);
            $notification = ($error_message == '' and $class_database->singleFieldValue('db_accountuser', 'usr_mail_privmessage', 'usr_id', intval($u_id)) == 1) ? self::PMnotification('private_message', $clear_arr, $db_id) : null;
        }
        if ($error_message == '') {
            $log = ($cfg['activity_logging'] == 1 and $action = new VActivity($_SESSION['USER_ID'], $u_id)) ? $action->addTo('log_pmessage', $msg_to . ':' . $db_id) : null;

            $msg_count = self::messageCount('message-new');
            $new_js    = $msg_count > 0 ? '$("span.blued").removeClass("no-display"); $("#new-message-count").html("' . $msg_count . '");' : null;
            $new_nr    = $msg_count > 0 ? VGenerate::declareJS($new_js) : null;

            $reset_js = '$(\'input[name="msg_label_to"]\').val(""); $(\'input[name="msg_label_subj"]\').val(""); $(\'textarea[name="msg_label_message"]\').val("");';
            $reset_js .= '$(\'select[name="live_attachlist"] option\').removeAttr("selected");';
            $reset_js .= '$(\'select[name="video_attachlist"] option\').removeAttr("selected");';
            $reset_js .= '$(\'select[name="short_attachlist"] option\').removeAttr("selected");';
            $reset_js .= '$(\'select[name="image_attachlist"] option\').removeAttr("selected");';
            $reset_js .= '$(\'select[name="audio_attachlist"] option\').removeAttr("selected");';
            $reset_js .= '$(\'select[name="doc_attachlist"] option\').removeAttr("selected");';

            $new_rs = VGenerate::declareJS($reset_js);

            $js = 'var submenu_id = $(".entry-form-class>div.menu-panel-entry-sub-active").attr("id"); var menu_id = $("#menu-panel-wrapper>div.menu-panel-entry-active").attr("id")';
            $js .= 'wrapLoad(current_url+menu_section+"?s="+((typeof(submenu_id) != "undefined") ? submenu_id : menu_id)+"&do=notice", fe_mask);';

            echo $in_nr . $out_nr . $new_nr . $new_rs;
        }

        return array($error_message, $notice_message);
    }
    /* mail notification */
    public function PMnotification($type, $clear_arr, $db_id)
    {
        $mail_do = VNotify::queInit($type, $clear_arr, $db_id);
    }
}
