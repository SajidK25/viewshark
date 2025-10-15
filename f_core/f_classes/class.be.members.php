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

class VbeMembers
{
    /* count members */
    public function userCount($p)
    {
        global $db;

        $rs = $db->execute("SELECT COUNT(*) AS `total` FROM `db_accountuser`;");

        return $rs->fields["total"];
    }
    /* add new account */
    public function addNewAccount($_dsp = '', $entry_id = '', $db_id = '')
    {
        global $class_filter, $language, $db, $cfg;

        $pk_html = null;
        /* process new account */
        if ($_POST and $_GET["do"] == 'add') {
            $process = self::processNewAccount();
            $form    = VArraySection::getArray("new_user");

            $usr_name      = $form[0]["usr_user"];
            $usr_pass      = $form[0]["usr_password"];
            $usr_pass_conf = $form[0]["usr_password_conf"];
            $usr_email     = $form[0]["usr_email"];
            $email_ignore  = intval($_POST["ignore_email"]);
            $usr_pk        = $form[0]["pk_id"];
        }
        /* packages select list */
        if ($cfg["paid_memberships"] == 1) {
            $pk_res = $db->execute("SELECT `pk_id`, `pk_name` FROM `db_packtypes` WHERE `pk_active`='1' ORDER BY `pk_id`;");

            if ($pk_res->fields["pk_id"]) {
                $pk_html = '<select name="account_new_pack_' . ((int) $db_id) . '" class="select-input wd300">';
                $pk_html .= '<option value="0">---</option>';
                while (!$pk_res->EOF) {
                    $pk_html .= '<option value="' . $pk_res->fields["pk_id"] . '"' . ($usr_pk == $pk_res->fields["pk_id"] ? ' selected="selected"' : null) . '>' . $pk_res->fields["pk_name"] . '</option>';
                    @$pk_res->MoveNext();
                }
                $pk_html .= '</select>';
            }
        }
        /* new account layout */
        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_sct  = 'new_user';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];
        $_lnk  = $_GET["do"] == 'add' ? '&nbsp;' . $language["frontend.global.or"] . ' <a href="javascript:;" onclick="$(\'#cancel-return\').click();">' . $language["frontend.global.lowercancel"] . '</a>' : null;

        $_btn = VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button ' . ($_GET["do"] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, ($_GET["do"] == 'add' ? $language["frontend.global.savenew"] : $language["frontend.global.saveupdate"])), 'display: inline-block-off;') . $_lnk;
        $_btn = null;

        $html .= '<div class="ct-entry-nowrap">';
        $html .= '<div class="ct-entry-details wd98p left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="user-entry-form" method="post" action="" class="entry-form-class">';
        $html .= '<div class="left-float">';
        $html .= VGenerate::simpleDivWrap('row', '', '<label>' . $language["account.profile.account"] . '</label><span class="err-red">' . $language["frontend.global.inactive"]);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["account.new.username"] . '</label>' . $language["frontend.global.required"], 'left-float', 'account_new_username', 'backend-text-input wd300', $usr_name);
        $html .= VGenerate::sigleInputEntry('password', 'left-float lh20 wd140', '<label>' . $language["account.new.password"] . '</label>' . $language["frontend.global.required"], 'left-float', 'account_new_password', 'backend-text-input wd300', $usr_pass);
        $html .= VGenerate::sigleInputEntry('password', 'left-float lh20 wd140', '<label>' . $language["account.new.password.conf"] . '</label>' . $language["frontend.global.required"], 'left-float', 'account_new_password_conf', 'backend-text-input wd300', $usr_pass_conf);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["frontend.global.email"] . '</label>' . $language["frontend.global.required"], 'left-float', 'frontend_global_email', 'backend-text-input wd300', $usr_email);
        $html .= VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="ignore_email" class=""' . ($email_ignore == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.mail.ignore"] . '</label>');
        $html .= $pk_html != '' ? VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language["account.overview.sub.name"] . '</label>' . $language["frontend.global.required"]) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $pk_html)) : null;
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language["frontend.global.required.items"]);
        $html .= '</form></div></div>';
        $html .= VGenerate::declareJS('$(function(){SelectList.init("account_new_pack_' . ((int) $db_id) . '");});');

        return $html;
    }
    /* processing new account */
    public function processNewAccount()
    {
        global $class_database, $class_filter, $db, $language, $cfg;

        $form           = VArraySection::getArray("new_user");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];
        /* check empty fields */
        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        /* more checks */
        if ($error_message == '') {
            $email_check = new VValidation;
            $hasher      = new VPasswordHash(8, false);

            $usr_name      = $form[0]["usr_user"];
            $usr_pass      = $form[0]["usr_password"];
            $usr_pass_conf = $form[0]["usr_password_conf"];
            $usr_email     = $form[0]["usr_email"];
            $email_ignore  = intval($_POST["ignore_email"]);
            $usr_pk        = $form[0]["pk_id"];
            /* username check */
            $error_message = $usr_name == '' ? $language["frontend.signup.ucheck.invalid"] : null;
            /* username check */
            $error_message = (!VUserinfo::usernameVerification($usr_name) and $error_message == '') ? $language["notif.error.invalid.user"] : $error_message;
            //check for password match
            $error_message = (md5($usr_pass) != md5($usr_pass_conf) and $error_message == '') ? $language["notif.error.pass.nomatch"] : $error_message;
            //check for valid email format
            $error_message = (!$email_check->checkEmailAddress($usr_email) and $error_message == '') ? $language["frontend.signup.email.invalid"] : $error_message;
            //check for email domain restriction
            $error_message = ($cfg["signup_domain_restriction"] == 1 and $error_message == '' and !VIPaccess::emailDomainCheck($usr_email)) ? $language["notif.error.nodomain"] : $error_message;
            //check for existing registered email
            $error_message = ($email_ignore == 0 and VUserinfo::existingEmail($usr_email) and $error_message == '') ? $language["notif.error.existing.email"] : $error_message;
            /* show error message */
            if ($error_message != '') {
                echo VGenerate::noticeTpl('', $error_message, '');
            }

            /* no more errors, add a new account */
            if ($error_message == '') {
                $usr_key  = VUserinfo::generateRandomString(10);
                $enc_pass = $class_filter->clr_str($hasher->HashPassword($usr_pass));
                /* permissions array */
                $perm_arr = array(
                    "perm_upload_v"         => 1,
                    "perm_upload_s"         => 1,
                    "perm_upload_i"         => 1,
                    "perm_upload_a"         => 1,
                    "perm_upload_d"         => 1,
                    "perm_upload_b"         => 1,
                    "perm_upload_l"         => 1,
                    "perm_view_v"           => 1,
                    "perm_view_s"           => 1,
                    "perm_view_i"           => 1,
                    "perm_view_a"           => 1,
                    "perm_view_d"           => 1,
                    "perm_view_b"           => 1,
                    "perm_view_l"           => 1,
                    "perm_live_chat"        => 1,
                    "perm_live_vod"         => 1,
                    "perm_embed_single"     => 1,
                    "perm_embed_yt_video"   => 0,
                    "perm_embed_yt_channel" => 0,
                    "perm_embed_dm_video"   => 0,
                    "perm_embed_dm_user"    => 0,
                    "perm_embed_mc_video"   => 0,
                    "perm_embed_mc_user"    => 0,
                    "perm_embed_vi_user"    => 0,
                );
                /* channel info arrays */
                $ch_cfg = serialize(array(
                    "ch_visible"         => 1,
                    "ch_m_comments"      => 1,
                    "ch_m_friends"       => 1,
                    "ch_m_channels"      => 1,
                    "ch_m_events"        => 1,
                    "ch_m_activity"      => 1,
                    "ch_m_subscribers"   => 1,
                    "ch_m_subscriptions" => 1,
                    "ch_m_followers"     => 1,
                    "ch_m_following"     => 1,
                    "ch_v_upfiles"       => 1,
                    "ch_v_favorites"     => 1,
                    "ch_v_playlists"     => 1,
                    "ch_v_all"           => 1,
                    "ch_m_home"          => 1,
                    "ch_m_videos"        => 1,
                    "ch_m_shorts"        => 1,
                    "ch_m_live"          => 1,
                    "ch_m_images"        => 1,
                    "ch_m_audios"        => 1,
                    "ch_m_documents"     => 1,
                    "ch_m_blogs"         => 1,
                    "ch_m_playlists"     => 1,
                    "ch_m_discussion"    => 1,
                    "ch_m_about"         => 1,
                    "ch_v_layout"        => "player",
                    "ch_v_content"       => "all",
                    "ch_v_default"       => "video",
                    "ch_v_featured"      => "",
                    "ch_v_autoplay"      => 0,
                    "ch_v_pl_ids"        => "",
                    "ch_ev_expired"      => 1,
                    "ch_ev_map"          => 0,
                    "ch_comm_perms"      => "free",
                    "ch_comm_spam"       => "yes",
                ));

                $ch_pfields = serialize(array(
                    "profile_edit_name"      => 0,
                    "profile_edit_total"     => 0,
                    "profile_edit_age"       => 0,
                    "profile_edit_last"      => 0,
                    "profile_edit_subs"      => 0,
                    "profile_edit_infl"      => 0,
                    "profile_edit_style"     => 0,
                    "profile_edit_descr"     => 0,
                    "profile_edit_about"     => 0,
                    "profile_edit_site"      => 0,
                    "profile_edit_town"      => 0,
                    "profile_edit_country"   => 0,
                    "profile_edit_occup"     => 0,
                    "profile_edit_companies" => 0,
                    "profile_edit_school"    => 0,
                    "profile_edit_interes"   => 0,
                    "profile_edit_movies"    => 0,
                    "profile_edit_music"     => 0,
                    "profile_edit_books"     => 0,
                ));

                $ch_rownum = serialize(array("r_friends" => 2, "r_subscribers" => 2, "r_subscriptions" => 2, "r_activity" => 10));

                /* user array */
                $ins_array1 = array(
                    "usr_key"         => $usr_key,
                    "usr_user"        => $usr_name,
                    "usr_password"    => $enc_pass,
                    "usr_email"       => $usr_email,
                    "usr_emailextras" => 0,
                    "usr_joindate"    => date("Y-m-d H:i:s"),
                    "usr_IP"          => $class_filter->clr_str($_SERVER[REM_ADDR]),
                    "usr_theme"       => "light",
                    "usr_perm"        => serialize($perm_arr),
                    "usr_verified"    => 1,
                    "usr_active"      => 1,
                    "usr_status"      => 1,
                    "usr_birthday"    => "",
                    "usr_gender"      => "",
                    "usr_country"     => "",
                    "usr_photo"       => "file",
                    "usr_dname"       => $usr_name,
                    "ch_user"         => $usr_name,
                    "ch_cfg"          => $ch_cfg,
                    "ch_pfields"      => $ch_pfields,
                    "ch_rownum"       => $ch_rownum,
                );
                $account_q = $class_database->doInsert('db_accountuser', $ins_array1);
                $user_id   = $db->Insert_ID();

                if ($user_id > 0) {
                    /* create user folders */
                    VSignup::createUserFolders($usr_key);

                    /* activity tracking */
                    if ($cfg["activity_logging"] == 1) {
                        $db->execute(sprintf("INSERT INTO `db_trackactivity` SET `usr_id`='%s';", $user_id));
                    }
                    /* set paid or free subscription */
                    if ($cfg["paid_memberships"] == 1) {
                        $q = $db->execute(sprintf("INSERT INTO `db_packusers` SET `usr_id`='%s';", $user_id));
                        $q = $db->execute(sprintf("SELECT `pk_price`, `pk_period` FROM `db_packtypes` WHERE `pk_id`='%s' LIMIT 2;", $usr_pk));

                        switch ($q->fields["pk_price"]) {
                            case "":break;
                            case "0":
                                $expire_time = date("Y-m-d H:i:s", strtotime("+" . $q->fields["pk_period"] . " day"));
                                $sub_usage   = VPayment::updateFreeUsage($user_id);
                                $sub_update  = VPayment::updateFreeAccount($usr_pk, $expire_time, $user_id);
                                break;
                            default:
                                $expire_time = date("Y-m-d H:i:s", strtotime("+" . $q->fields["pk_period"] . " day"));
                                $db->execute(sprintf("UPDATE
                                            `db_accountuser`, `db_packusers`
                                            SET
                                            `db_packusers`.`pk_id`='%s',
                                            `db_packusers`.`pk_usedspace`='0',
                                            `db_packusers`.`pk_usedbw`='0',
                                            `db_packusers`.`pk_total_video`='0',
                                            `db_packusers`.`pk_total_image`='0',
                                            `db_packusers`.`pk_total_audio`='0',
                                            `db_packusers`.`pk_total_doc`='0',
                        `db_packusers`.`pk_total_blog`='0',
                        `db_packusers`.`pk_total_live`='0',
                                            `db_packusers`.`subscribe_time`='%s',
                                            `db_packusers`.`expire_time`='%s',
                                            `db_accountuser`.`usr_active`='1',
                        `db_accountuser`.`usr_status`='1'
                                            WHERE
                                            `db_accountuser`.`usr_id`='%s' AND
                                            `db_packusers`.`usr_id`='%s';", $usr_pk, date("Y-m-d H:i:s"), $expire_time, $user_id, $user_id));

                                break;
                        }
                    }
                    if ($user_id > 0) {
                        echo VGenerate::noticeTpl('', '', $language["notif.success.request"]);
                    }
                }
            }
        }
    }
    /* checkbox selection query */
    public function cbSQL()
    {
        $db_arr = array();
        foreach ($_POST["current_entry_id"] as $k => $v) {
            $db_arr[] = "'" . intval($v) . "'";
        }

        return $db_q = sprintf("`usr_id` IN (%s)", implode(', ', $db_arr));
    }
    /* account manager layout */
    public function accountManager()
    {
        global $db, $language, $class_filter, $cfg;

        $error  = null;
        $notice = null;
        $p_do   = $class_filter->clr_str($_GET["do"]);
        $p_a    = $class_filter->clr_str($_GET["a"]);
        $p_ip   = $class_filter->clr_str($_SERVER[REM_ADDR]);

        /* add new member */
        switch ($p_do) {
            case "add":return self::addNewAccount('block', 'mem-add-new-entry', 'wrapper');
        }
        /* checkbox actions */
        if ($_POST and substr($p_a, 0, 2) == 'cb') {
            if (!is_array($_POST["current_entry_id"]) or count($_POST["current_entry_id"]) == 0) {
                $error = VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $language["notif.no.multiple.select"], '')));
            } else {
                switch ($p_a) {
                    case "cb-active":
                    case "cb-inactive":
                        $db_upd = 1;
                        $db_f   = 'usr_status';
                        $db_val = $p_a == 'cb-active' ? 1 : 0;
                        break;
                    case "cb-feature":
                    case "cb-unfeature":
                        $db_upd = 1;
                        $db_f   = 'usr_featured';
                        $db_val = $p_a == 'cb-feature' ? 1 : 0;
                        break;
                    case "cb-affiliate":
                    case "cb-unaffiliate":
                    case "cb-partner":
                    case "cb-unpartner":
                        $db_upd = 1;
                        $db_f   = 'usr_partner';
                        $db_val = $p_a == 'cb-partner' ? 1 : 0;

                        $js = 'var popupid = "popuprel-cb";';
                        $js .= 'var userid = "-cb";';
                        $js .= 'var userkey = "sel";';

                        $js .= '$.fancybox.open({ type: "ajax", minWidth: "70%", height: "auto", margin: 10, afterShow: function(){$("#cb-in2, #cb-in3").val("' . rawurlencode(self::cbSQL()) . '");}, href: current_url + menu_section + "?do=' . ($p_a == 'cb-partner' ? 'make-partner' : 'clear-partner') . '&u="+userkey });';
                        break;
                    case "cb-promote":
                    case "cb-unpromote":
                        $db_upd = 1;
                        $db_f   = 'usr_promoted';
                        $db_val = $p_a == 'cb-promote' ? 1 : 0;
                        break;
                    case "cb-verify":
                    case "cb-unverify":
                        $db_upd = 1;
                        $db_f   = 'usr_verified';
                        $db_val = $p_a == 'cb-verify' ? 1 : 0;
                        break;
                    case "cb-ban":
                    case "cb-unban":
                        $db_upd = 0;
                        $db_val = $p_a == 'cb-ban' ? 1 : 0;

                        $sql_ip  = sprintf("SELECT DISTINCT `usr_IP` FROM `db_accountuser` WHERE %s AND `usr_ip`!='%s';", self::cbSQL(), $p_ip);
                        $sql_res = $db->execute($sql_ip);
                        if ($sql_res->fields["usr_IP"]) {
                            $ipq = array();
                            while (!$sql_res->EOF) {
                                $ipq[] = "('', '" . $sql_res->fields["usr_IP"] . "', '', '" . date("Y-m-d H:i:s") . "', '1')";
                                @$sql_res->MoveNext();
                            }
                            $f_q = implode(',', $ipq);
                        }
                        $sql = sprintf("INSERT INTO `db_banlist` (`ban_id`, `ban_ip`, `ban_descr`, `ban_start`, `ban_active`) VALUES %s ON DUPLICATE KEY UPDATE `ban_active`='%s';", $f_q, $db_val);
                        $res = $db->execute($sql);
                        break;
                    case "cb-email":
                    case "cb-delete":
                        $db_upd = 0;

                        $js .= 'var popupid = "popuprel-cb";';
                        $js .= 'var userid = "-cb";';
                        $js .= 'var userkey = "sel";';

                        $js .= '$.fancybox.open({ type: "ajax", minWidth: "70%", height: "auto", margin: 10, afterShow: function(){$("#cb-in2, #cb-in3").val("' . rawurlencode(self::cbSQL()) . '");}, href: current_url + menu_section + "?do=' . ($p_a == 'cb-email' ? 'send-email' : 'del-account') . '&u="+userkey });';
                        echo VGenerate::declareJS('$(document).ready(function(){' . $js . '});');
                        break;
                }
                if ($db_upd == 1) {
                    $sql = sprintf("UPDATE `db_accountuser` SET `%s`='%s' WHERE %s;", $db_f, $db_val, self::cbSQL());
                    $res = $db->execute($sql);
                }
                if ($db->Affected_Rows() > 0 or (($p_a == 'cb-affiliate' or $p_a == 'cb-unaffiliate') or ($p_a == 'cb-partner' or $p_a == 'cb-unpartner'))) {
                    if ($p_a == 'cb-affiliate' or $p_a == 'cb-partner') {
                        $db->execute(sprintf("UPDATE `db_accountuser` SET `partner_date`='%s' WHERE %s", date("Y-m-d H:i:s"), self::cbSQL()));
                    }
                    echo VGenerate::declareJS('$(document).ready(function(){' . $js . '});');
                }

                $notice = ($db->Affected_Rows() > 0) ? VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', '', $language["notif.success.request"]))) : null;
            }
        }

        $def_sql = " ORDER BY A.`usr_id`";
        $act_sql = "AND A.`usr_status`='1'";
        $s_sql   = null;
        switch ($p_do) {
            case "sort-active":$sort_sql = $act_sql . $def_sql . " DESC";
                break;
            case "sort-inactive":$sort_sql = "AND A.`usr_status`='0'" . $def_sql . " DESC";
                break;
            case "sort-deleted":$sort_sql = "AND A.`usr_deleted`='1'" . $def_sql . " DESC";
                break;
            case "sort-featured":$sort_sql = "AND A.`usr_featured`='1'" . $act_sql . $def_sql . " DESC";
                break;
            case "sort-promoted":$sort_sql = "AND A.`usr_promoted`='1'" . $act_sql . $def_sql . " DESC";
                break;
            case "sort-affiliated":
            case "sort-partnered":$sort_sql = "AND A.`usr_partner`='1'" . $act_sql . $def_sql . " DESC";
                break;
            case "sort-recent":$sort_sql = "ORDER BY A.`usr_joindate` DESC";
                break;
            case "sort-views":$sort_sql = $act_sql . " ORDER BY A.`ch_views` DESC";
                break;
            case "sort-lcount":$sort_sql = $act_sql . " ORDER BY A.`usr_logins` DESC";
                break;
            case "sort-lvcount":$sort_sql = $act_sql . " ORDER BY A.`usr_l_count` DESC";
                break;
            case "sort-vcount":$sort_sql = $act_sql . " ORDER BY A.`usr_v_count` DESC";
                break;
            case "sort-scount":$sort_sql = $act_sql . " ORDER BY A.`usr_s_count` DESC";
                break;
            case "sort-icount":$sort_sql = $act_sql . " ORDER BY A.`usr_i_count` DESC";
                break;
            case "sort-acount":$sort_sql = $act_sql . " ORDER BY A.`usr_a_count` DESC";
                break;
            case "sort-dcount":$sort_sql = $act_sql . " ORDER BY A.`usr_d_count` DESC";
                break;
            case "sort-bcount":$sort_sql = $act_sql . " ORDER BY A.`usr_b_count` DESC";
                break;
            default:$sort_sql = $act_sql . " ORDER BY A.`usr_joindate` DESC";
                break;
        }
        /* search users */
        if ($_GET["sq"] != '') {
            $sq    = $class_filter->clr_str($_GET["sq"]);
            $s_sql = sprintf("AND A.`usr_user`='%s'", $sq);
            if (strstr($sq, '*') !== false) {
                $s_sql = sprintf("AND A.`usr_user` LIKE '%s'", str_replace('*', '%', $sq));
            }
        }
        $sql = sprintf("SELECT
                    A.`usr_id`, A.`usr_key`, A.`usr_user`, A.`usr_email`, A.`usr_affiliate`, A.`usr_partner`, A.`usr_l_count`, A.`usr_v_count`, A.`usr_s_count`, A.`usr_i_count`, A.`usr_a_count`, A.`usr_d_count`, A.`usr_b_count`, A.`usr_IP`, A.`usr_featured`, A.`usr_promoted`, A.`usr_logins`, A.`usr_lastlogin`, A.`usr_joindate`, A.`usr_verified`, A.`usr_status`, A.`usr_active`, A.`usr_deleted`,
                    A.`usr_dname`, A.`usr_description`, A.`usr_website`, A.`usr_fname`, A.`usr_lname`, A.`usr_photo`, A.`usr_town`, A.`usr_city`, A.`usr_zip`, A.`usr_country`, A.`usr_birthday`, A.`usr_gender`,
                    A.`usr_relation`, A.`usr_showage`, A.`usr_occupations`, A.`usr_companies`, A.`usr_schools`, A.`usr_interests`, A.`usr_movies`, A.`usr_music`, A.`usr_books`, A.`usr_del_reason`,
                    A.`ch_type`, A.`ch_views`
                    FROM
                    `db_accountuser` A
                    WHERE
                    A.`usr_id`>'0'
                    %s %s
                ", $s_sql, $sort_sql);

        $tsql = sprintf("SELECT
                    A.`usr_id`,
                    COUNT(*) AS `total`
                    FROM
                    `db_accountuser` A
                    WHERE
                    A.`usr_id`>'0'
                    %s %s
                ", $s_sql, $sort_sql);

        $tres     = $db->execute($tsql);
        $db_count = $tres->fields["total"];
        /* pagination start */
        $pages                 = new VPagination;
        $pages->items_total    = $db_count;
        $pages->mid_range      = 5;
        $pages->items_per_page = isset($_GET["ipp"]) ? (int) $_GET["ipp"] : $cfg["page_be_user_accounts"];
        $pages->paginate();

        $final_sql = $sql . $q_for . $q_sort . $pages->limit . ';';
        $res       = $db->execute($final_sql);
        $page_of   = (($pages->high + 1) > $db_count) ? $db_count : ($pages->high + 1);

        $results_text = $pages->getResultsInfo($page_of, $db_count, 'left');
        $paging_links = $pages->getPaging($db_count, 'right');
        $page_jump    = $paging_links != '' ? $pages->getPageJump('left') : null;
        $ipp_select   = $paging_links != '' ? $pages->getIpp('right') : null;
        /* pagination end */
        $bullet_id = 'ct-bullet1';
        $entry_id  = 'ct-entry-details1';
        $bb        = 'bottom-border';
        $do        = 1;

        $html .= $error != '' ? $error : $notice;
        $html .= ($paging_links != '' and $db_count > 0) ? '<div id="paging-top" class="left-float wdmax section-bottom-border paging-bg-lighter" style="display: inline-block-off;">' . $page_jump . $ipp_select . '</div>' : null;
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger be-users">';
        while (!$res->EOF) {
            $bb          = ($do == $res->recordcount()) ? '' : $bb;
            $_user_id    = $res->fields["usr_id"];
            $_user_key   = $res->fields["usr_key"];
            $_user_name  = $res->fields["usr_user"];
            $_user_act   = $res->fields["usr_status"];
            $_user_email = $res->fields["usr_email"];
            $_user_ver   = $res->fields["usr_verified"];
            $_user_ip    = $res->fields["usr_IP"];
            $_user_feat  = $res->fields["usr_featured"];
            $_user_login = $res->fields["usr_logins"];
            $_user_last  = $res->fields["usr_lastlogin"];
            $_user_lastf = date('M d, Y H:i A', strtotime($_user_last));
            $_user_date  = $res->fields["usr_joindate"];
            $_user_datef = date('M d, Y H:i A', strtotime($_user_date));
            $_user_del   = $res->fields["usr_deleted"];
            $_user_v     = $res->fields["usr_v_count"];
            $_user_i     = $res->fields["usr_i_count"];
            $_user_a     = $res->fields["usr_a_count"];
            $_user_d     = $res->fields["usr_d_count"];
            $_user_b     = $res->fields["usr_b_count"];
            $_user_l     = $res->fields["usr_l_count"];
            $_user_s     = $res->fields["usr_s_count"];
            /* more */
            $_user_descr = $res->fields["usr_description"];
            $_user_web   = $res->fields["usr_website"];
            $_user_fname = $res->fields["usr_fname"];
            $_user_lname = $res->fields["usr_lname"];
            $_user_dname = $res->fields["usr_dname"];
            $_user_pic   = $res->fields["usr_photo"];
            $_user_town  = $res->fields["usr_town"];
            $_user_city  = $res->fields["usr_city"];
            $_user_zip   = $res->fields["usr_zip"];
            $_user_ctry  = $res->fields["usr_country"];
            $_user_bday  = $res->fields["usr_birthday"];
            $_user_gen   = $res->fields["usr_gender"];
            $_user_rel   = $res->fields["usr_relation"];
            $_user_age   = $res->fields["usr_showage"];
            $_user_occup = $res->fields["usr_occupations"];
            $_user_comp  = $res->fields["usr_companies"];
            $_user_sch   = $res->fields["usr_schools"];
            $_user_inter = $res->fields["usr_interests"];
            $_user_mov   = $res->fields["usr_movies"];
            $_user_music = $res->fields["usr_music"];
            $_user_book  = $res->fields["usr_books"];
            $_user_delr  = $res->fields["usr_del_reason"];
            $_user_promo = $res->fields["usr_promoted"];
            $_user_aff   = $res->fields["usr_affiliate"];
            $_user_prt   = $res->fields["usr_partner"];
            /* more */
            $_user_chan  = $res->fields["ch_type"];
            $_user_views = $res->fields["ch_views"];

            $info_array = array($_user_id, $_user_key, $_user_name, $_user_act, $_user_email, $_user_ip, $_user_feat, $_user_login, $_user_lastf, $_user_datef, $_user_del, $_user_ver, $_user_v, $_user_i, $_user_a, $_user_d, $_user_b, $_user_l, $_user_s);
            $pr_array   = array($_user_descr, $_user_web, $_user_fname, $_user_lname, $_user_pic, $_user_town, $_user_city, $_user_zip, $_user_ctry, $_user_bday, $_user_gen, $_user_rel, $_user_age,
                $_user_occup, $_user_comp, $_user_sch, $_user_inter, $_user_mov, $_user_music, $_user_book, $_user_delr, $_user_promo, $_user_aff, $_user_prt);
            $ch_array = array($_user_chan, $_user_views);

            switch ($p_do) {
                case "sort-views":
                    $ht = self::numFormat($_user_views);
                    break;
                case "sort-lcount":
                    $ht = self::numFormat($_user_login);
                    break;
                case "sort-lvcount":
                    $l  = 'l';
                    $ht = self::numFormat($_user_l);
                    break;
                case "sort-vcount":
                    $l  = 'v';
                    $ht = self::numFormat($_user_v);
                    break;
                case "sort-scount":
                    $l  = 's';
                    $ht = self::numFormat($_user_s);
                    break;
                case "sort-icount":
                    $l  = 'i';
                    $ht = self::numFormat($_user_i);
                    break;
                case "sort-acount";
                    $l  = 'a';
                    $ht = self::numFormat($_user_a);
                    break;
                case "sort-dcount";
                    $l  = 'd';
                    $ht = self::numFormat($_user_d);
                    break;
                case "sort-bcount";
                    $l  = 'b';
                    $ht = self::numFormat($_user_b);
                    break;
                default:
                    $ht = -1;
                    break;
            }
            $ht .= ($ht >= 0 and $p_do != 'sort-views' and $p_do != 'sort-lcount') ? ' ' . ($ht != 1 ? $language["frontend.global." . $l . ".p"] : $language["frontend.global." . $l]) :
            (($ht >= 0 and $p_do == 'sort-lcount') ? ' ' . ($ht != 1 ? $language["frontend.global.logins.count"] : $language["frontend.global.login.count"]) :
                (($ht >= 0 and $p_do == 'sort-views') ? ' ' . ($ht != 1 ? $language["frontend.global.views"] : $language["frontend.global.view"]) : null));
            $html .= '<li>';
            $html .= '<div class="left-float ct-entry left-margin10 ' . $bb . ' wd94p" id="' . $bullet_id . '-' . $_user_key . '">';
            $html .= '<div class="responsive-accordion-head entry-head' . (($_POST and $_GET["do"] == 'update' and $db_id == self::getPostID()) ? ' active' : null) . '">';
            $html .= '<div class="d-flex ai-center">';
            $html .= VGenerate::simpleDivWrap('place-left icheck-box', '', '<input type="checkbox" name="entryid" value="' . $_user_id . '" class="list-check" />');
            $html .= VGenerate::simpleDivWrap('entry-number ct-bullet-label place-left right-padding10' . $new_msg, 'lc' . $_user_id . '-entry-details', '#' . $_user_id);
            $html .= VGenerate::simpleDivWrap('entry-title ct-bullet-label place-left right-padding10 link', '', $_user_name . ($_user_dname != '' ? ' / ' . $_user_dname : null));
            $html .= '</div>';
            $html .= $ht != -1 ? VGenerate::simpleDivWrap('entry-type ct-bullet-label greyed-out d-flex ai-center', '', '[' . $ht . ']') : null;
            $html .= VGenerate::simpleDivWrap('entry-type ct-bullet-label d-flex ai-center', '', '<span class="greyed-out">' . $_user_datef . '</span>');
            $html .= '<div class="place-right expand-entry">';
            $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: ' . (($_POST and $_GET["do"] == 'update' and $db_id == self::getPostID()) ? 'none' : 'block') . ';"></i>';
            $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: ' . (($_POST and $_GET["do"] == 'update' and $db_id == self::getPostID()) ? 'block' : 'none') . ';"></i>';
            $html .= '</div>';
            $html .= '</div>';

            $html .= '<div class="responsive-accordion-panel' . $_class[0] . '" style="display: ' . (($_POST and $_GET["do"] == 'update' and $db_id == self::getPostID()) ? 'block' : 'none') . ';">';
            /* user details */
            $html .= self::userEntry('block', $type, $entry_id, $info_array, $pr_array, $ch_array);
            //right side icons maybe
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</li>';

            $do = $do + 1;
            @$res->MoveNext();
        }
        $html .= '</ul>';
        $html .= $db_count > 0 ? '<div id="paging-bottom" class="left-float wdmax paging-top-border paging-bg">' . $paging_links . $results_text . '</div>' : '<div class="left-float wdmax center top-bottom-padding">' . $language["backend.menu.members.entry3.sub5.nores"] . '</div>';
        $html .= '<div class="popupbox-mem no-display" id="popuprel-cb"></div><div id="fade-cb" class="fade no-display"></div><div class="no-display"><input type="hidden" name="cb_in" id="cb-in" value="" /></div>';

        $js = '$(document).ready(function(){var clone = $("#paging-top").clone(true); $("#paging-top").detach(); $(clone).insertBefore("#open-close-links");' . ($_GET["do"] == 'add' ? '$(".section-top-bar").removeClass("section-top-bar").addClass("section-top-bar-add");' : null) . ' add = $("#add-new-entry").clone(true); $("#add-new-entry").detach(); $(add).insertBefore(".section-top-bar"); $(\'<div class="clearfix"></div>\').insertAfter(add);});';
        /* popup box */
        $js .= '$("li.popup").click(function(){';
        $js .= 'var popupid = $(this).attr("rel");';
        $js .= 'var userid = $(this).attr("rel-id");';
        $js .= 'var userkey = $(this).attr("rel-key");';
        $js .= 'var lid = $(this).attr("id");';
        $js .= 'if(lid == "del-account"){ $(".list-check").attr("checked", false); $("#ct-bullet1-"+userkey).prev().find(".list-check").prop("checked", true); }';

        $js .= 'if(lid == "del-account"){ $(".list-check").attr("checked", false); $("#ct-bullet1-"+userkey).prev().find(".list-check").prop("checked", true); }';
        $js .= 'if(lid == "del-account"){ $("#cb-in2, #cb-in3").val(encodeURIComponent("`usr_id` IN ("+userid+")")); }';
        $js .= '$.fancybox.open({ wrapCSS: "default-lb", type: "ajax", minWidth: "70%", height: "auto", margin: 20, href: current_url + menu_section + "?do="+lid+"&u="+userkey+(lid == "user-activity" ? "&a=show-all" : "") });';
        $js .= '});';
        /* package type select change */
        $_js .= 'function subDateChange(v){';
        $_js .= '$("#h-pk-id").val(v);';
        $_js .= '$("#pk-update"+$("#pk-key").html()).load(current_url + menu_section + "?do=sub-change&u="+$("#pk-key").html()+"&pk="+v, function(){';
        $_js .= '});';
        $_js .= '}';

        /* declare JS */
        $html .= VGenerate::declareJS($_js . '$(document).ready(function(){' . $js . '});');
        $html .= VGenerate::swiperjs('main');
        $html .= VGenerate::declareJS('$(document).ready(function(){
            $(".swiper-top-main .swiper-slide a").on("click", function(){
               var i = $(this).attr("rel");
               var f = $(this).closest("form").attr("id");
               $("#" + f).find("ul.uactions-list li#" + i).click();
            });
        });');

        return $html;
    }
    /* count user accounts */
    public function accountCountAll()
    {
        return;

        global $db;

        $res = $db->execute("SELECT COUNT(*) AS `total` FROM `db_accountuser`;");
        $tt  = $res->fields["total"];

        echo VGenerate::declareJS('$("#backend-menu-entry10-sub2-count").html("' . $tt . '");');
    }
    /* close popup link */
    public function userAction_close($t = '')
    {
        global $language, $class_filter, $class_database;

        $_u = $class_filter->clr_str($_GET["u"]);
        switch ($t) {
            case "folder-info":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'fix-missing button-grey search-button form-button save-entry-button usr-update', '', 0, '<span>' . $language["backend.menu.members.entry10.sub2.create"] . '</span>'), 'display: inline-block-off;');

                $_js .= '$(".fix-missing").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$(".fancybox-inner").load(current_url + menu_section + "?do=' . $class_filter->clr_str($_GET["do"]) . '&u=' . $_u . '&a=reset", function(){$(".fancybox-skin").unmask();});';
                $_js .= '});';
                break;
            case "sub-info":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'sub-update button-grey search-button form-button save-entry-button usr-update', '', 0, '<span>' . $language["backend.menu.members.entry10.sub2.update.m"] . '</span>') . VGenerate::basicInput('button', 'save_changes', 'sub-reset button-grey search-button form-button save-entry-button usr-update', '', 0, '<span>' . $language["backend.menu.members.entry10.sub2.reset.bw"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');

                $_js .= '$(".sub-update").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=sub-update&u=' . $_u . '", $("#sub-info-form").serialize(), function(data) {';
                $_js .= '$("#pk-update' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                $_js .= '$(".sub-reset").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=sub-reset&u=' . $_u . '", $("#sub-info-form").serialize(), function(data) {';
                $_js .= '$("#pk-update' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                break;
            case "paid-sub":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'paid-sub-update button-grey search-button form-button save-entry-button usr-update', '', 0, '<span>' . $language["backend.menu.members.entry10.sub2.update.s"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');

                $_js .= '$(".paid-sub-update").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=paid-sub-update&u=' . $_u . '", $("#paid-sub-form").serialize(), function(data) {';
                $_js .= '$("#pk-update' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                $_js .= '$("#ct-entry-details4-input").on("keyup", function() {var t=$(this).val();if(t<0)$(this).val(0);if(t>100)$(this).val(100);$("#sub-share-nr").text($(this).val());});';
                $_js .= '$("#paid-sub-form .icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        }); });
                        SelectList.init("backend_menu_sub_p_currency");
                        ';
                break;
            case "user-activity":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button usr-update', '', 0, '<span>' . $language["backend.menu.members.entry10.sub2.update.a"] . '</span>'), 'display: inline-block-off;');

                $_js .= '$(".act-enable, .act-disable, .act-delete").click(function(){';
                $_js .= 'var paging_link = ' . ($_GET["page"] != '' ? '"&page=' . $class_filter->clr_str($_GET["page"]) . '&ipp=' . $class_filter->clr_str($_GET["ipp"]) . '";' : '"";');
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=user-activity&u=' . $_u . ($_GET["a"] != '' ? '&a=' . $class_filter->clr_str($_GET["a"]) : null) . '&cb="+$(this).attr("class")+paging_link, $("#usr-activity-form").serialize(), function(data) {';
                $_js .= '$(".fancybox-inner").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';

                $_js .= '$(".usr-update").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=act-update&u=' . $_u . '", $("#usr-log-form").serialize(), function(data) {';
                $_js .= '$("#usr-update' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                break;
            case "change-email":
                $ht = VGenerate::simpleDivWrap('row', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button usr-update', '', 0, '<span>' . $language["backend.menu.members.entry10.sub2.update.a"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');

                $_js .= '$(".usr-update").unbind().click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=usr-update&u=' . $_u . '", $("#usr-info-form").serialize(), function(data) {';
                $_js .= '$("#usr-update' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                $_js .= '$("#em-ignore.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        }); });
                        ';
                break;
            case "change-password":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button usr-password', '', 0, '<span>' . $language["backend.menu.members.entry10.sub2.update.a"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');

                $_js .= '$(".usr-password").unbind().click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=passw-update&u=' . $_u . '", $("#usr-info-form").serialize(), function(data) {';
                $_js .= '$("#usr-updatep' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                $_js .= '$("#em-ignore.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        }); });
                        ';
                $_js .= '$("a.showp").on("click",function(e){e.preventDefault();$(this).prev().attr("type", "text");$(this).addClass("no-display");$(this).next().removeClass("no-display")});$("a.hidep").on("click",function(e){e.preventDefault();$(this).prev().prev().attr("type", "password");$(this).addClass("no-display");$(this).prev().removeClass("no-display")});';
                break;
            case "del-account":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button usr-delete', '', 0, '<span>' . $language["frontend.global.confirm"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');

                $_js .= '$(".usr-delete").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=usr-delete&u=' . $_u . '", $("#usr-del-form").serialize(), function(data) {';
                $_js .= '$("#usr-update' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                $_js .= '$("#act-del.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        }); });
                        ';
                break;
            case "affiliate-payout":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button usr-save-affiliate', '', 0, '<span>' . $language["frontend.global.confirm"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');

                $_js .= '$(".usr-save-affiliate").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=usr-save-affiliate&u=' . $_u . '", $("#usr-affiliate-form").serialize(), function(data) {';
                $_js .= '$("#usr-update").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                break;
            case "make-partner":
            case "make-affiliate":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button usr-make-partner', '', 0, '<span>' . $language["backend.menu.members.entry2.sub2.aff.btn1a"] . '</span>') . VGenerate::lb_Cancel($language["backend.menu.members.entry2.sub2.aff.btn3"]), 'display: inline-block-off;');

                $_js .= '$(".usr-make-partner").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=usr-make-partner&u=' . $_u . '", $("#usr-affiliate-form").serialize(), function(data) {';
                $_js .= '$("#usr-update").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                break;
            case "clear-partner":
            case "clear-affiliate":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button usr-clear-partner n1', '', 0, '<span>' . $language["backend.menu.members.entry2.sub2.aff.btn1"] . '</span>') . VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button usr-clear-partner n2', '', 0, '<span>' . $language["backend.menu.members.entry2.sub2.aff.btn2"] . '</span>') . VGenerate::lb_Cancel($language["backend.menu.members.entry2.sub2.aff.btn3"]), 'display: inline-block-off;');

                $_js .= '$(".usr-clear-partner").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=usr-clear-partner&n="+($(this).hasClass("n1") ? "n1" : ($(this).hasClass("n2") ? "n2" : 0))+"&u=' . $_u . '", $("#usr-affiliate-form").serialize(), function(data) {';
                $_js .= '$("#usr-update").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                break;
            case "send-email":
                $ht = null;

                $_js .= '$(".send-email-button").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=usr-email&u=' . $_u . '", $("#usr-mail-form").serialize(), function(data) {';
                $_js .= '$("#mailmsg-update' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                $_js .= '$(".send-message-button").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=usr-email&u=' . $_u . '", $("#usr-msg-form").serialize(), function(data) {';
                $_js .= '$("#mailmsgs-update' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                $_js .= '$("#pm-notify.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        }); });
                        ';
                break;
            case "user-perm":
                $ht = VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button usr-update', '', 0, '<span>' . $language["backend.menu.members.entry10.sub2.update.a"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');

                $_js .= '$(".usr-update").click(function(){';
                $_js .= '$(".fancybox-skin").mask("");';
                $_js .= '$.post(current_url + menu_section + "?do=perm-update&u=' . $_u . '", $("#usr-perm-form").serialize(), function(data) {';
                $_js .= '$("#perm-update' . $_u . '").html(data);';
                $_js .= '$(".fancybox-skin").unmask();';
                $_js .= '});';
                $_js .= '});';
                $_js .= '$("#usr-perm .icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        }); });
                        ';
                break;
        }

        $html .= $ht;
        $js .= $_js;

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* user actions, delete account/ window popup */
    public function userAction_delaccount()
    {
        global $language, $class_filter, $class_database, $cfg, $smarty;

        $usr_key  = $class_filter->clr_str($_GET["u"]);
        $usr_user = $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_key', $usr_key);
        $usr_id   = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $usr_key);

        $html .= '<div id="lb-wrapper">';
        $html .= '<div class="entry-list vs-column full">';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle('<span class="">' . $language["backend.menu.members.entry2.sub2.del.acct"] . '</span><span class="bold">' . ($usr_user == '' ? '(' . $language["frontend.global.selected"] . ')' : $usr_user) . '</span>', 'icon-times');
        $html .= VGenerate::simpleDivWrap('row', 'usr-update' . $usr_key, '');
        $html .= VGenerate::simpleDivWrap('row', 'del-update' . $usr_key, '');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active">';
        $html .= '<form id="usr-del-form" method="post" action="" class="entry-form-class">';
        $html .= '<div class="icheck-box d-column" id="act-del" style="align-items:start">';
        $html .= VGenerate::simpleDivWrap('row icheck-box', '', VGenerate::simpleDivWrap('d-flex ai-center', '', '<input type="radio" value="db" name="user_del_type" class=""><label>' . $language["backend.menu.members.entry2.sub2.del.m1"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row icheck-box no-top-padding', '', VGenerate::simpleDivWrap('d-flex ai-center', '', '<input type="radio" value="all" name="user_del_type" class=""><label>' . $language["backend.menu.members.entry2.sub2.del.m2"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="hidden" name="cb_in2" id="cb-in2" value="" />');
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="hidden" name="cb_in3" id="cb-in3" value="" />');
        $html .= '<div class="clearfix">&nbsp;</div>';
        $html .= VGenerate::simpleDivWrap('row right-float top-padding15', '', self::userAction_close('del-account'));
        $html .= '</div>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<script type="text/javascript">' . $smarty->fetch($cfg["javascript_dir_be"] . '/settings-accordion.js') . '</script>';

        return $html;
    }
    /* search for array key => val */
    public function multidimensional_search($parents, $searched)
    {
        if (empty($searched) || empty($parents)) {
            return false;
        }
        foreach ($parents as $key => $value) {
            $exists = true;
            foreach ($searched as $skey => $svalue) {
                $exists = ($exists && isset($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
            }
            if ($exists) {return $key;}
        }
        return false;
    }

    /* delete user entries from database */
    public function userDelete_db($del_type)
    {
        global $db, $language, $class_filter, $class_database;

        $rt       = 0;
        $af       = 0;
        $usr_key  = $class_filter->clr_str($_GET["u"]);
        $usr_id   = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $usr_key);
        $usr_user = $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_key', $usr_key);

        $tbl_ar = array(
            "db_accountuser",
            "db_channelcomments",
            "db_channelevents",
            "db_filetypemenu",
            "db_mailque",
            "db_packusers",
            "db_followers",
            "db_subscriptions",
            "db_subscribers",
            "db_subusers",
            "db_subtemps",
            "db_subinvoices",
            "db_subpayouts",
            "db_trackactivity",
            "db_useractivity",
            "db_usercodes",
            "db_usercontacts",
            "db_userlabels",
            "db_userthemes",
            "db_videofavorites",
            "db_videofiles",
            "db_videohistory",
            "db_videoliked",
            "db_videopayouts",
            "db_videoplaylists",
            "db_videowatchlist",
            "db_shortfavorites",
            "db_shortfiles",
            "db_shorthistory",
            "db_shortliked",
            "db_shortpayouts",
            "db_shortplaylists",
            "db_shortwatchlist",
            "db_imagefavorites",
            "db_imagefiles",
            "db_imagehistory",
            "db_imageliked",
            "db_imagepayouts",
            "db_imageplaylists",
            "db_imagewatchlist",
            "db_audiofavorites",
            "db_audiofiles",
            "db_audiohistory",
            "db_audioliked",
            "db_audiopayouts",
            "db_audioplaylists",
            "db_audiowatchlist",
            "db_docfavorites",
            "db_docfiles",
            "db_dochistory",
            "db_docliked",
            "db_docpayouts",
            "db_docplaylists",
            "db_docwatchlist",
            "db_blogfavorites",
            "db_blogfiles",
            "db_bloghistory",
            "db_blogliked",
            "db_blogpayouts",
            "db_blogplaylists",
            "db_blogwatchlist",
            "db_livefavorites",
            "db_livefiles",
            "db_livehistory",
            "db_liveliked",
            "db_livepayouts",
            "db_liveplaylists",
            "db_livewatchlist",
        );
        $tbl_0 = $tbl_ar[0];
        /* selected users */
        $cb_in = $_POST["cb_in2"] != '' ? rawurldecode($class_filter->clr_str($_POST["cb_in2"])) : null;
        $_q    = sprintf("SELECT `usr_id`, `usr_key` FROM `db_accountuser` WHERE %s", ($cb_in == '' ? "`usr_key`='" . $usr_key . "' LIMIT 1;" : $cb_in));
        $rs    = $db->execute($_q);
        $srs   = $rs->getrows();
        $af    = 1;

        if ($af > 0) {
            /* delete from more tables */
            $rs_ar = array(
                "db_channelcomments" => array("0" => "c_usr_id", "1" => $usr_id),
                "db_messaging"       => array("0" => "msg_from", "1" => $usr_id),
                "db_messaging"       => array("0" => "msg_to", "1" => $usr_id),
                "db_videocomments"   => array("0" => "c_usr_id", "1" => $usr_id),
                "db_shortcomments"   => array("0" => "c_usr_id", "1" => $usr_id),
                "db_imagecomments"   => array("0" => "c_usr_id", "1" => $usr_id),
                "db_audiocomments"   => array("0" => "c_usr_id", "1" => $usr_id),
                "db_doccomments"     => array("0" => "c_usr_id", "1" => $usr_id),
                "db_blogcomments"    => array("0" => "c_usr_id", "1" => $usr_id),
                "db_livecomments"    => array("0" => "c_usr_id", "1" => $usr_id),
                "db_followers"       => array("0" => "sub_id", "1" => $usr_id),
                "db_subscribers"     => array("0" => "sub_id", "1" => $usr_id),
                "db_subtemps"        => array("0" => "usr_id_to", "1" => $usr_id),
                "db_subusers"        => array("0" => "usr_id_to", "1" => $usr_id),
                "db_subpayouts"      => array("0" => "usr_id_to", "1" => $usr_id),
                "db_useractivity"    => array("0" => "usr_id_to", "1" => $usr_id),
            );

            foreach ($rs_ar as $rk => $rv) {
                $_sql  = ($cb_in == '' and $usr_id > 0) ? sprintf("DELETE FROM `%s` WHERE `%s`='%s';", $rk, $rv[0], $rv[1]) : sprintf("DELETE FROM `%s` WHERE %s;", $rk, str_replace('usr_id', $rv[0], $cb_in));
                $r_sql = $db->execute($_sql);
                $rt += $db->Affected_Rows();
            }
            /* delete username from remaining activity */
            if ($cb_in == '' and $usr_id > 0) {
                $a_sql = sprintf("DELETE FROM `db_useractivity` WHERE `act_type` LIKE '%s';", '%' . $usr_user . '%');
                $a_res = $db->execute($a_sql);
            } else {
                foreach ($srs as $selk => $selv) {
                    $a_sql = sprintf("DELETE FROM `db_useractivity` WHERE `act_type` LIKE '%s';", '%' . $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_id', $selv["usr_id"]) . '%');
                    $a_res = $db->execute($a_sql);
                }
            }
        }
        /* delete main database tables */
        foreach ($tbl_ar as $tv) {
            $_sql  = ($cb_in == '' and $usr_id > 0) ? sprintf("DELETE FROM `%s` WHERE `usr_id`='%s';", $tv, $usr_id) : sprintf("DELETE FROM `%s` WHERE %s;", $tv, $cb_in);
            $a_sql = $db->execute($_sql);
            $af += $db->Affected_Rows();
        }
        $affected = $af + $rt;

        $_js = '$.fancybox.close(); wrapLoad(current_url + menu_section + "?s="+$(".menu-panel-entry-sub-active").attr("id"));';
        /* delete all files from server */
        if ($del_type == 'all' and $affected > 0) {
            if ($cb_in == '' and $usr_id > 0) {
//delete single user entry
                $del_q = self::userDelete_dbque($usr_key);
                $del_f = self::userDelete_files();

                echo VGenerate::declareJS('$(document).ready(function(){' . $_js . '});');
            } else {
//delete selected users
                foreach ($srs as $s_k => $s_v) {
                    $del_k = $s_v["usr_key"];
                    $del_q = self::userDelete_dbque($del_k);
                    $del_f = self::userDelete_files($del_k);
                }
                echo VGenerate::declareJS('$(document).ready(function(){' . $_js . '});');
            }
        } else {
            if ($affected > 0) {
                echo VGenerate::declareJS('$(document).ready(function(){' . $_js . '});');
            }
        }
    }
    public function userDelete_dbque($usr_key)
    {
        global $db;

        $db->execute(sprintf("DELETE FROM `db_videoque` WHERE `usr_key`='%s';", $usr_key));
        $db->execute(sprintf("DELETE FROM `db_shortque` WHERE `usr_key`='%s';", $usr_key));
        $db->execute(sprintf("DELETE FROM `db_imageque` WHERE `usr_key`='%s';", $usr_key));
        $db->execute(sprintf("DELETE FROM `db_audioque` WHERE `usr_key`='%s';", $usr_key));
        $db->execute(sprintf("DELETE FROM `db_docque` WHERE `usr_key`='%s';", $usr_key));
    }
    /* delete user files/folders */
    public function userDelete_files($k = '')
    {
        global $class_database, $class_filter, $cfg;

        $usr_key = $k == '' ? $class_filter->clr_str($_GET["u"]) : $k;
        $usr_id  = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $usr_key);

        $_dir1 = $cfg["media_files_dir"] . '/' . $usr_key;
        $_dir2 = $cfg["upload_files_dir"] . '/' . $usr_key;
        $_dir3 = $cfg["channel_views_dir"] . '/' . $usr_key;
        $_dir4 = $cfg["profile_images_dir"] . '/*' . $usr_key . '*';

        $_cmd = sprintf("rm -rfv %s %s %s %s", $_dir1, $_dir2, $_dir3, $_dir4);

        exec($_cmd, $out);
        /* do something with $out */
        $rn = is_dir($_dir1) ? rename($_dir1, $_dir1 . '-deleted-' . date("Y-m-d")) : null;
        $rn = is_dir($_dir2) ? rename($_dir2, $_dir2 . '-deleted-' . date("Y-m-d")) : null;
        $rn = is_dir($_dir3) ? rename($_dir3, $_dir3 . '-deleted-' . date("Y-m-d")) : null;
    }
    /* user actions, send email/message window popup */
    public function userAction_sendemail()
    {
        global $language, $class_database, $class_filter, $db, $smarty, $cfg;

        $usr_key      = $class_filter->clr_str($_GET["u"]);
        $res          = $db->execute(sprintf("SELECT `usr_id`, `usr_user`, `usr_email` FROM `db_accountuser` WHERE `usr_key`='%s' LIMIT 1;", $usr_key));
        $usr_id       = $res->fields["usr_id"];
        $usr_user     = $res->fields["usr_user"];
        $usr_mail     = $res->fields["usr_email"];
        $email_ignore = 0;
        $msg_notify   = 0;
        $_btn         = VGenerate::simpleDivWrap('row', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button send-email-button', '', $usr_id, '<span>' . $language["frontend.global.send"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');

        $html = '<div id="lb-wrapper">';
        $html .= '<div class="entry-list vs-column full">';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li><div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle($language["backend.menu.members.entry10.sub2.to.e"] . '<span class="bold">' . $usr_user . '</span> (' . ($_GET["u"] != 'sel' ? $usr_mail : '<span class="bold">' . $language["frontend.global.selected"] . '</span>') . ')<span id="u-key" class="no-display">' . $usr_key . '</span>', 'icon-envelope');
        $html .= VGenerate::simpleDivWrap('row', 'mailmsg-update' . $usr_key, '');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active">';

        $html .= '<form id="usr-mail-form" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry10.sub2.subj.e"] . '</label>', 'left-float', 'email_subject', 'backend-text-input wd350 input-email-sub', $usr_sube);
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry10.sub2.body.e"], 'left-float', 'email_body', 'backend-textarea-input wd350 input-email-text', $usr_bodye);
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="hidden" name="cb_in2" id="cb-in2" value="" />');
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';

        $html .= '<li><div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle($language["backend.menu.members.entry10.sub2.to.m"] . '<span class="bold">' . ($_GET["u"] != 'sel' ? $usr_user : '(<span class="bold">' . $language["frontend.global.selected"] . '</span>)') . '</span><span id="u-key" class="no-display">' . $usr_key . '</span>', 'icon-envelope');
        $html .= VGenerate::simpleDivWrap('row', 'mailmsgs-update' . $usr_key, '');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active">';

        $html .= '<form id="usr-msg-form" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry10.sub2.subj.m"] . '</label>', 'left-float', 'message_subject', 'backend-text-input wd350 input-msg-sub', $usr_subm);
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry10.sub2.body.m"] . '</label>', 'left-float', 'message_body', 'backend-textarea-input wd350 input-msg-text', $usr_bodym);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', 'pm-notify', '<input type="checkbox" value="1" name="message_notify" class=""' . ($msg_notify == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pm.not"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="hidden" name="cb_in3" id="cb-in3" value="" />');
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', str_replace('send-email', 'send-message', $_btn)));
        $html .= VGenerate::simpleDivWrap('row right-float top-padding15', '', self::userAction_close('send-email'));
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
    /* user actions, change email/password window popup */
    public function userAction_changeemail($err = '')
    {
        global $language, $class_database, $class_filter, $smarty, $cfg;

        $usr_key      = $class_filter->clr_str($_GET["u"]);
        $usr_id       = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $usr_key);
        $usr_email    = $class_database->singleFieldValue('db_accountuser', 'usr_email', 'usr_id', $usr_id);
        $email_ignore = 0;

        $html = '<div id="lb-wrapper">';
        $html .= '<div class="entry-list vs-column full">';
        $html .= '<form id="usr-info-form" method="post" action="" class="entry-form-class">';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle('<span class="bold">' . $language["backend.menu.members.change.email"] . $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_key', $usr_key) . '</span><span id="u-key" class="no-display">' . $usr_key . '</span>', 'icon-envelope');
        $html .= VGenerate::simpleDivWrap('row', 'usr-update' . $usr_key, '');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active d-flex d-column">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["account.email.address"] . '</label>', 'left-float', 'account_email_address', 'backend-text-input wd350', $usr_email);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', 'em-ignore', '<input type="checkbox" value="1" name="ignore_email" class=""' . ($email_ignore == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.mail.ignore"] . '</label>'));
        $html .= '<div class="clearfix"></div><br>';
        $html .= VGenerate::simpleDivWrap('', '', self::userAction_close('change-email'));
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle('<span class="bold">' . $language["backend.menu.members.change.password"] . $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_key', $usr_key) . '</span><span id="u-key" class="no-display">' . $usr_key . '</span>', 'icon-key');
        $html .= VGenerate::simpleDivWrap('row', 'usr-updatep' . $usr_key, '');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active d-flex d-column">';
        $html .= VGenerate::sigleInputEntry('password', 'left-float lh20 wd140', '<label>' . $language["account.manage.pass.new"] . '</label>', 'left-float p-relative', 'account_manage_pass_new', 'backend-text-input wd350', '');
        $html .= VGenerate::sigleInputEntry('password', 'left-float lh20 wd140', '<label>' . $language["account.manage.pass.retype"] . '</label>', 'left-float p-relative', 'account_manage_pass_retype', 'backend-text-input wd350', '');
        $html .= '<div class="clearfix"></div><br>';
        $html .= VGenerate::simpleDivWrap('mb-10', '', self::userAction_close('change-password'));
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
    /* user actions, user permissions window popup */
    public function userAction_userperm($err = '')
    {
        global $language, $class_database, $class_filter, $class_language, $db, $cfg, $smarty;

        $usr_key      = $class_filter->clr_str($_GET["u"]);
        $res          = $db->execute(sprintf("SELECT `usr_id`, `usr_email`, `usr_perm` FROM `db_accountuser` WHERE `usr_key`='%s' LIMIT 1;", $usr_key));
        $usr_id       = $res->fields["usr_id"];
        $usr_email    = $res->fields["usr_email"];
        $usr_perm     = unserialize($res->fields["usr_perm"]);
        $email_ignore = 0;
        if (!isset($usr_perm["perm_upload_s"])) {
            $usr_perm["perm_upload_s"] = 1;
        }
        if (!isset($usr_perm["perm_view_s"])) {
            $usr_perm["perm_view_s"] = 1;
        }

        $html = '<div id="lb-wrapper">';
        $html .= '<div class="entry-list vs-column full">';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li><div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle('<span class="bold">' . $language["backend.menu.members.perm.for"] . $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_key', $usr_key) . '</span><span id="u-key" class="no-display">' . $usr_key . '</span>', 'icon-checkmark-circle');
        $html .= VGenerate::simpleDivWrap('row', 'perm-update' . $usr_key, '');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active">';
        $html .= '<form id="usr-perm-form" method="post" action="" class="entry-form-class">';
        $html .= '<div class="icheck-box-off" id="usr-perm">';
        $html .= '<div class="vs-column half">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_upload_l" class=""' . ($usr_perm["perm_upload_l"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pu.l"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_live_chat" class=""' . ($usr_perm["perm_live_chat"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pu.c"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_live_vod" class=""' . ($usr_perm["perm_live_vod"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pu.r"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_view_l" class=""' . ($usr_perm["perm_view_l"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pv.l"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_upload_v" class=""' . ($usr_perm["perm_upload_v"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pu.v"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_view_v" class=""' . ($usr_perm["perm_view_v"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pv.v"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_upload_s" class=""' . ($usr_perm["perm_upload_s"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pu.s"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_view_s" class=""' . ($usr_perm["perm_view_s"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pv.s"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_embed_single" class=""' . ($usr_perm["perm_embed_single"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.import.perm.allow.embed"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row no-top-padding', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_embed_yt_video" class=""' . ($usr_perm["perm_embed_yt_video"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.import.perm.allow.yt.video"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row no-top-padding', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_embed_yt_channel" class=""' . ($usr_perm["perm_embed_yt_channel"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.import.perm.allow.yt.channel"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row no-top-padding', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_embed_dm_video" class=""' . ($usr_perm["perm_embed_dm_video"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.import.perm.allow.dm.video"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row no-top-padding', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_embed_dm_user" class=""' . ($usr_perm["perm_embed_dm_user"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.import.perm.allow.dm.user"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row no-top-padding', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_embed_vi_user" class=""' . ($usr_perm["perm_embed_vi_user"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.import.perm.allow.vi.user"] . '</label>'));

        $html .= '</div>';
        $html .= '<div class="vs-column half fit">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_upload_i" class=""' . ($usr_perm["perm_upload_i"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pu.i"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_view_i" class=""' . ($usr_perm["perm_view_i"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pv.i"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_upload_a" class=""' . ($usr_perm["perm_upload_a"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pu.a"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_view_a" class=""' . ($usr_perm["perm_view_a"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pv.a"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_upload_d" class=""' . ($usr_perm["perm_upload_d"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pu.d"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_view_d" class=""' . ($usr_perm["perm_view_d"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pv.d"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_upload_b" class=""' . ($usr_perm["perm_upload_b"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pu.b"] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="perm_view_b" class=""' . ($usr_perm["perm_view_b"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry10.sub2.pv.b"] . '</label>'));

        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="clearfix"></div><br>' . VGenerate::simpleDivWrap('row right-float top-padding15', '', self::userAction_close('user-perm'));
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
    /* user actions, activity window popup */
    public function userAction_useractivity()
    {
        global $cfg, $db, $class_database, $class_filter, $language, $smarty;

        $_a       = $class_filter->clr_str($_GET["a"]);
        $_cb      = $class_filter->clr_str($_GET["cb"]);
        $usr_key  = $class_filter->clr_str($_GET["u"]);
        $res      = $db->execute(sprintf("SELECT `usr_id`, `usr_user` FROM `db_accountuser` WHERE `usr_key`='%s' LIMIT 1;", $usr_key));
        $usr_id   = $res->fields["usr_id"];
        $usr_user = $res->fields["usr_user"];
        /* checkbox actions */
        switch ($_cb) {
            case "act-enable":
            case "act-disable":
            case "act-delete":
                $_ct = isset($_POST["cb_uaction"]) ? count($_POST["cb_uaction"]) : 0;

                if ($_ct == 0) {
                    $error = VGenerate::noticeTpl('', $language["notif.no.multiple.select"], '');
                    break;
                }

                if ($_ct > 0) {
                    $_aq = array();
                    foreach ($_POST["cb_uaction"] as $ak => $av) {
                        $_aq[] = sprintf("'%s'", $av);
                    }
                    $_id = sprintf("`act_id` IN (%s)", implode(", ", $_aq));
                }
                if ($_ct > 0 and $_cb != 'act-delete') {
                    $_asql = sprintf("UPDATE `db_useractivity` SET `act_visible`='%s' WHERE %s;", ($_cb == 'act-enable' ? 1 : 0), $_id);
                } elseif ($_ct > 0 and $_cb == 'act-delete') {
                    $_asql = sprintf("DELETE FROM `db_useractivity` WHERE %s;", $_id);
                }

                $_cb_db = ($_POST and $_asql != '') ? $db->execute($_asql) : null;
                $error  = ($_asql != '' and $db->Affected_Rows() > 0) ? VGenerate::noticeTpl('', '', $language["notif.success.request"]) : null;
                break;
        }
        /* activity sort */
        switch (str_replace('show_', '', $_a)) {
            case "upload":$_sq = " AND `act_type` LIKE '%upload%'";
                break;
            case "filecomment":$_sq = " AND `act_type` LIKE '%comments%'";
                break;
            case "rating":$_sq = " AND `act_type` LIKE '%like%'";
                break;
            case "fav":$_sq = " AND `act_type` LIKE '%favorite%'";
                break;
            case "subscribing":$_sq = " AND `act_type` LIKE '%subscribes%'";
                break;
            case "following":$_sq = " AND `act_type` LIKE '%follows%'";
                break;
            case "pmessage":$_sq = " AND `act_type` LIKE '%private%'";
                break;
            case "frinvite":$_sq = " AND `act_type` LIKE '%invite%'";
                break;
            case "delete":$_sq = " AND `act_type` LIKE '%delete%'";
                break;
            case "signin":$_sq = " AND `act_type` LIKE '%sign in%'";
                break;
            case "signout":$_sq = " AND `act_type` LIKE '%sign out%'";
                break;
            case "precovery":$_sq = " AND `act_type` LIKE '%password recovery%'";
                break;
            case "urecovery":$_sq = " AND `act_type` LIKE '%username recovery%'";
                break;
            default:$_sq = null;
        }
        /* list activities */
        $q_entries  = sprintf("SELECT `act_id`, `act_type`, `act_time`, `act_ip`, `act_visible` FROM `db_useractivity` WHERE `usr_id`='%s' %s ORDER  BY `act_time` DESC ", $usr_id, $_sq);
        $q_total    = sprintf("SELECT COUNT(*) AS `total` FROM `db_useractivity` WHERE `usr_id`='%s' %s ", $usr_id, $_sq);
        $q_totalres = $db->execute($q_total);
        $db_count   = $q_totalres->fields["total"];

        $opt_array = array(
            "log_upload"      => $language["backend.menu.entry6.sub6.log.upload"],
            "log_filecomment" => $language["backend.menu.entry6.sub6.log.comment"],
            "log_rating"      => $language["backend.menu.entry6.sub6.log.rate"],
            "log_fav"         => $language["backend.menu.entry6.sub6.log.favorite"],
            "log_subscribing" => $language["backend.menu.entry6.sub6.log.subscribe"],
            "log_following"   => $language["backend.menu.entry6.sub6.log.follow"],
            "log_pmessage"    => $language["backend.menu.entry6.sub6.log.pm"],
            "log_frinvite"    => $language["backend.menu.entry6.sub6.log.invite"],
            "log_delete"      => $language["backend.menu.entry6.sub6.log.delete"],
            "log_signin"      => $language["backend.menu.entry6.sub6.log.signin"],
            "log_signout"     => $language["backend.menu.entry6.sub6.log.signout"],
            "log_precovery"   => $language["backend.menu.entry6.sub6.log.pr"],
            "log_urecovery"   => $language["backend.menu.entry6.sub6.log.ur"],
        );

        $cls_ar                = array('left-float wd240', 'left-float wd100 act-value', 'left-float wd50 act-value');
        $pages                 = new VPagination;
        $pages->items_total    = $db_count;
        $pages->mid_range      = 5;
        $pg_cfg                = 'page_be_user_activity';
        $pages->items_per_page = isset($_GET["ipp"]) ? (int) $_GET["ipp"] : $cfg[$pg_cfg];
        $pages->paginate('user_activity');
        $sql     = $q_entries . $pages->limit . ';';
        $q       = $db->execute($sql);
        $page_of = (($pages->high + 1) > $db_count) ? $db_count : ($pages->high + 1);

        $results_text = $pages->getResultsInfo($page_of, $db_count, 'left');
        $paging_links = $pages->getPaging($db_count, 'right');

        $html = '<div class="lb-margins">' . VGenerate::headingArticle('<span class="bold">' . $language["backend.menu.members.activity.for"] . $usr_user . '</span>', 'icon-history') . '</div>';
        $html .= '<div class="entry-list vs-column two_thirds fit" id="lb-wrapper">';
        $html .= '<div class="section-top-bar">';
        $html .= '<div class="left-float left-padding5 d-flex ai-center">';
        $html .= '<div class="place-left icheck-box all" id="checkselect-all-entries"><input type="checkbox" class="no-top-margin" name="cb_allaction" value="1" onclick="if($(this).is(\':checked\')){$(\'.cb-uaction\').attr(\'checked\', true);}else{$(\'.cb-uaction\').attr(\'checked\', false);}" /></div>';
        $html .= '<div class="sortings">';
        $html .= self::userAction_actionMenu();
        $html .= self::userAction_activityMenu($opt_array, $usr_key);
        $html .= '</div>';
        $html .= '<div class="page-actions">' . $smarty->fetch("tpl_backend/tpl_settings/ct-save-open-close.tpl") . '</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="entry-response" id="usr-update' . $usr_key . '">' . $error . '</div>';
        $html .= '<form id="usr-activity-form" method="post" action="">';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger activity-accordion">';
        if (!$q->fields["act_id"]) {
            $html .= '<center>' . $language["backend.menu.members.entry2.sub2.no.act"] . '</center>';
        } else {
            while (!$q->EOF) {
                $act_v = $q->fields["act_visible"];

                $html .= '<li>';
                $html .= '<div>';
                $html .= '<div class="responsive-accordion-head">';
                $html .= '<span class="icheck-box fetched"><input type="checkbox" class="no-top-margin cb-uaction" name="cb_uaction[]" value="' . $q->fields["act_id"] . '" /></span>';
                $html .= self::userActionText($q->fields["act_type"]);
                $html .= '<div class="place-right expand-entry">';
                $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: block;"></i>';
                $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: none;"></i>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="responsive-accordion-panel" style="display: none;">';
                $html .= '<div class="vs-column full">';
                $html .= '<div style="padding-left: 22px;">';
                $html .= VGenerate::simpleDivWrap($cls_ar[2], '', 'Active: ' . ($act_v == 1 ? '<span class="conf-green">' . $language["frontend.global.yes"] . '</span>' : '<span class="err-red">' . $language["frontend.global.no"] . '</span>'), '', 'span');
                $html .= '<br>';
                $html .= VGenerate::simpleDivWrap($cls_ar[1], '', 'Date/time: <span class="conf-green">' . $q->fields["act_time"] . '</span>', '', 'span');
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<div class="clearfix"></div>';
                $html .= '</div>';
                $html .= '</li>';

                $q->MoveNext();
            }
        }
        $html .= '</ul>';
        $html .= '</form>';

        $q = $db->execute(sprintf("SELECT `log_upload`, `log_filecomment`, `log_rating`, `log_fav`, `log_subscribing`, `log_following`, `log_pmessage`, `log_frinvite`, `log_delete`, `log_signin`, `log_signout`, `log_precovery`, `log_urecovery` FROM `db_trackactivity` B WHERE `usr_id`='%s' LIMIT 1;", $usr_id));

        $ht .= '<div class="entry-list">';
        $ht .= '<div class="section-top-bar vs-column full">';
        $ht .= '<div class="vs-column full"><span style="margin-top: 4px; float: left; padding: 2px;">' . $language["backend.menu.members.activity.log"] . '</span></div>';
        $ht .= '<div class="clearfix"></div>';
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '<div class="clearfix"></div>';
        foreach ($opt_array as $key => $val) {
            $cfg[]     = $class_database->getConfigurations($key);
            $cfg_check = $q->fields[$key] == 1 ? ' checked="checked"' : null;
            $ht .= $cfg[$key] == 1 ? '<div class="row no-top-padding left-padding5 icheck-box log-action"><input class="activity_logging_cb" type="checkbox"' . $cfg_check . ' name="' . $key . '" value="1" /><label>' . $language["backend.menu.entry6.sub6.log.comp"] . $val . '</label></div>' : null;
        }
        $ht .= VGenerate::simpleDivWrap('row right-float top-padding15', '', self::userAction_close('user-activity'));

        $html .= '<div class="clearfix"></div>';
        $html .= ($paging_links != '' or $db_count > 0) ? '<div class="left-float wd450 paging-top-border activity-paging" id="paging-bottom">' . $paging_links . $results_text . '</div>' : '<div class="left-float wdmax center top-bottom-padding no-display">' . $language["files.text.none"] . '</div>';
        $html .= '</div>';
        $html .= '<div class="vs-column thirds fit">';
        $html .= VGenerate::simpleDivWrap('', 'log-actions-types', '<form id="usr-log-form" class="entry-form-class" name="usr_log_form" method="post" action="">' . $ht . '</form>');
        $html .= VGenerate::simpleDivWrap('no-display', '', '<input type="hidden" name="uact_id" id="uact-id" value="' . $usr_id . '" />');
        $html .= VGenerate::declareJS('var s_url = "uactivity";');
        $html .= '</div>';

        $js = '$(".icheck-box.all input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        });
               });
               $(".icheck-box.fetched input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        });
               });
               $(".icheck-box.log-action input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                        });
               });
               $("#checkselect-all-entries input").on("ifChecked", function () { $(".activity-accordion input").iCheck("check");});
               $("#checkselect-all-entries input").on("ifUnchecked", function () { $(".activity-accordion input").iCheck("uncheck"); });
               ';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');
        $html .= '<script type="text/javascript">' . $smarty->fetch($cfg["javascript_dir_be"] . '/settings-accordion.js') . '</script>';

        return $html;
    }
    /* user activity actions menu */
    public function userAction_actionMenu()
    {
        global $cfg, $language, $class_filter;

        $ht .= '<div class="menu-drop">';
        $ht .= '<div id="nav-actions" class="dl-menuwrapper">';
        $ht .= '<span class="dl-trigger" rel="tooltip" title="' . $language["frontend.global.selection.actions"] . '"></span>';
        $ht .= '<ul id="act-list" class="dl-menu">';

        $ht .= '<li class="act-enable">';
        $ht .= '<a href="javascript:;" class="normal lh20"><i class="icon-play"></i> ' . $language["frontend.global.enable.sel"] . '</a>';
        $ht .= '</li>';
        $ht .= '<li class="act-disable">';
        $ht .= '<a href="javascript:;" class="normal lh20"><i class="icon-stop"></i> ' . $language["frontend.global.disable.sel"] . '</a>';
        $ht .= '</li>';
        $ht .= '<li class="act-delete">';
        $ht .= '<a href="javascript:;" class="normal lh20"><i class="icon-times"></i> ' . $language["frontend.global.delete.sel"] . '</a>';
        $ht .= '</li>';
        $ht .= '</ul>';
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '<script type="text/javascript">$(function() { $( "#nav-actions" ).dlmenu({ animationClasses : { classin : "dl-animate-in-5", classout : "dl-animate-out-5" } }); });</script>';

        return $ht;
    }
    /* user activity sort menu */
    public function userAction_activityMenu($opt_array, $usr_key)
    {
        global $cfg, $language, $class_filter;

        $ico_array = array(
            "log_upload"      => "icon-upload",
            "log_filecomment" => "icon-comment",
            "log_rating"      => "icon-thumbs-up",
            "log_fav"         => "icon-heart",
            "log_responding"  => "icon-reply",
            "log_subscribing" => "icon-users5",
            "log_following"   => "icon-users5",
            "log_pmessage"    => "icon-envelope",
            "log_frinvite"    => "icon-users",
            "log_delete"      => "icon-times",
            "log_signin"      => "icon-enter",
            "log_signout"     => "icon-exit",
            "log_precovery"   => "icon-support",
            "log_urecovery"   => "icon-support",
        );

        $_a  = $_GET["a"] != 'show-all' ? $class_filter->clr_str($_GET["a"]) : null;
        $_mt = $_a == '' ? $language["backend.menu.members.entry10.sub2.act.all"] : $opt_array[str_replace('show', 'log', $_a)];

        $ht .= '<div class="menu-drop">';
        $ht .= '<div id="nav-activity" class="dl-menuwrapper">';
        $ht .= '<span class="dl-trigger sort-trigger" rel="tooltip" title="' . $language["frontend.global.apply.filter"] . '"><span class="sort-selected">' . $_mt . '</span></span>';
        $ht .= '<ul id="act-list" class="dl-menu">';

        $ht .= '<li class="count2 pointer wd140" rel-act="show-all">';
        $ht .= '<a href="javascript:;" class="normal lh20"><i class="icon-list"></i> ' . $language["backend.menu.members.entry10.sub2.act.all"] . '</a>';
        $ht .= '</li>';
        foreach ($opt_array as $lk => $lv) {
            $ht .= '<li class="count2 pointer wd140" rel-act="' . str_replace('log', 'show', $lk) . '">';
            $ht .= '<a href="javascript:;" class="normal lh20"><i class="' . $ico_array[$lk] . '"></i> ' . $lv . '</a>';
            $ht .= '</li>';
        }
        $ht .= '</ul>';
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '<script type="text/javascript">$(function() { $( "#nav-activity" ).dlmenu({ animationClasses : { classin : "dl-animate-in-5", classout : "dl-animate-out-5" } }); });</script>';

        $js = '$("#act-list .count2").click(function(){';
        $js .= '$(".fancybox-inner").mask(" ");';
        $js .= '$.post(current_url + menu_section + "?do=user-activity&u=' . $usr_key . '&a="+$(this).attr("rel-act"), $("#ct-set-form").serialize(), function(data){';
        $js .= '$(".fancybox-inner").html(data);';
        $js .= '$(".fancybox-inner").unmask();';
        $js .= '});';
        $js .= '});';

        $js .= '$("#nav-actions .dl-trigger").on("click", function(){
                        if ($("#nav-actions ul.dl-menu").hasClass("dl-menuopen")) {
                        setTimeout(function () {
                                if ($("#nav-activity ul.dl-menu").hasClass("dl-menuopen")) {
                                        $("#nav-activity .dl-trigger").click();
                                }
                        }, 100);
                        }
                });
                $("#nav-activity .dl-trigger").on("click", function(){
                        if ($("#nav-activity ul.dl-menu").hasClass("dl-menuopen")) {
                        setTimeout(function () {
                                if ($("#nav-actions ul.dl-menu").hasClass("dl-menuopen")) {
                                        $("#nav-actions .dl-trigger").click();
                                }
                        }, 100);
                        }
                });

        ';

        $ht .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $ht;
    }
    /* user action text */
    public function userActionText($a)
    {
        global $class_database;

        $_act = substr($a, 0, 3);

        switch ($_act) {
            case "fav":$i = 'icon-heart';
                break;
            case "upl":$i = 'icon-upload';
                break;
            case "lik":$i = 'icon-thumbs-up';
                break;
            case "dis":$i = 'icon-thumbs-up2';
                break;
            case "del":$i = 'icon-times';
                break;
            case "com":$i = 'icon-comment';
                break;
            case "pri":$i = 'icon-envelope';
                break;
            case "sub":$i = 'icon-users5';
                break;
            case "fol":$i = 'icon-users5';
                break;
            case "use":$i = 'icon-support';
                break;
            case "pas":$i = 'icon-support';
                break;
            case "fri":$i = 'icon-users';
                break;
            default:
                $i = $a == 'sign in' ? 'icon-enter' : ($a == 'sign out' ? 'icon-exit' : 'icon-list');
                break;
        }

        switch ($_act) {
            case "fav":
            case "upl":
            case "lik":
            case "dis":
            case "del":
                $_ar = explode(":", $a);
                $_ft = $class_database->singleFieldValue('db_' . $_ar[1] . 'files', 'file_title', 'file_key', $_ar[2]);
                $_t  = '<label><i class="' . $i . '"></i> ' . $_ar[0] . ': ' . $_ar[1] . '</label><span class="greyed-out entry-type">' . VUserinfo::truncateString(($_ft == '' ? $_ar[2] : $_ft), 45) . '</span>';
                break;
            case "com":
                $_ar = explode(":", $a);
                $_fr = explode(" ", $_ar[0]);

                if ($_fr[2] == 'channel') {
                    $_t = '<label><i class="' . $i . '"></i> ' . $_ar[0] . '</label><span class="greyed-out entry-type">' . $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_id', $_ar[1]) . '</span>';
                } else {
                    $_t = '<label><i class="' . $i . '"></i> ' . $_ar[0] . '</label><span class="greyed-out entry-type">' . VUserinfo::truncateString($class_database->singleFieldValue('db_' . $_fr[2] . 'files', 'file_title', 'file_key', $_ar[1]), 45) . '</span>';
                }
                break;
            case "pri":
                $_ar = explode(":", $a);
                $_t  = '<i class="' . $i . '"></i> ' . $_ar[0];
                break;
            case "sub":
            case "fol":
            case "use":
            case "pas":
            case "fri":
                $_t = '<label><i class="' . $i . '"></i> ' . $a . '</label>';
                break;
            default:
                $_t = ($a == 'sign in' or $a == 'sign out') ? '<label><i class="' . $i . '"></i> ' . $a . '</label>' : null;
                break;
        }
        return $_t;
    }
    /* update subscription, in window popup */
    public function subUpdate($t)
    {
        global $class_filter, $class_database, $db, $language;

        $error_message = null;
        $usr_key       = $class_filter->clr_str($_GET["u"]);
        $usr_pk        = intval($_POST["sub_pk_id"]);
        $usr_id        = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $usr_key);
        $usr_mail      = $class_database->singleFieldValue('db_accountuser', 'usr_email', 'usr_key', $usr_key);

        switch ($t) {
            case "sub-reset":
                $up_sql = $db->execute(sprintf("UPDATE `db_packusers` SET `pk_usedbw`='0' WHERE `usr_id`='%s' LIMIT 1;", $usr_id));
                $notice = ($db->Affected_Rows() > 0) ? 1 : 0;
                echo $notice == 1 ? VGenerate::noticeTpl('', '', $language["notif.success.request"]) : null;
                return;
                break;
            case "paid-sub-update":
                if ($_POST) {
                    $sub_custom   = (int) $_POST["custom_subscription"];
                    $sub_perc     = $class_filter->clr_str($_POST["backend_menu_members_entry1_sub1_rev"]);
                    $sub_currency = $class_filter->clr_str($_POST["backend_menu_sub_p_currency"]);
                    $sub_update   = $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_sub_share`='%s', `usr_sub_perc`='%s', `usr_sub_currency`='%s' WHERE `usr_id`='%s' LIMIT 1;", $sub_custom, $sub_perc, $sub_currency, $usr_id));
                    $notice       = ($db->Affected_Rows() > 0) ? 1 : 0;
                    echo $notice == 1 ? VGenerate::noticeTpl('', '', $language["notif.success.request"]) : null;

                    if ($notice) {
                        $r = $db->execute(sprintf("SELECT `db_id`, `pk_paid`, `pk_paid_share` FROM `db_subpayouts` WHERE `usr_id_to`='%s';", $usr_id));
                        while (!$r->EOF) {
                            $db_id         = $r->fields["db_id"];
                            $pk_paid       = $r->fields["pk_paid"];
                            $pk_paid_share = $r->fields["pk_paid_share"];

                            $db_owed = number_format((($sub_perc / 100) * $pk_paid), 1);
                            if ($pk_paid_share != $db_owed) {
                                $db->execute(sprintf("UPDATE `db_subpayouts` SET `pk_paid_share`='%s' WHERE `db_id`='%s' LIMIT 1;", $db_owed, $db_id));
                            }

                            $r->MoveNext();
                        }
                    }
                    return;
                }
                break;
            case "sub-update":
                $expire_time = $class_filter->clr_str($_POST["sub_expire"]);
                $pk_id       = $class_database->singleFieldValue('db_packusers', 'pk_id', 'usr_id', $usr_id);
                $sql_chk     = ($pk_id != '') ? 1 : 0;
                $pk_price    = $class_database->singleFieldValue('db_packtypes', 'pk_price', 'pk_id', $usr_pk);
                $up_sql      = sprintf("UPDATE `db_accountuser`, `db_packusers`
                                            SET
                                            `db_packusers`.`pk_id`='%s',
                                            `db_packusers`.`expire_time`='%s',
                                            `db_packusers`.`pk_paid`='%s'
                                            WHERE
                                            `db_accountuser`.`usr_id`='%s' AND
                                            `db_packusers`.`usr_id`='%s';", $usr_pk, $expire_time, $pk_price, $usr_id, $usr_id);
                if ($sql_chk > 0) {
                    $db->execute($up_sql);
                } else {
                    $db->execute(sprintf("INSERT INTO `db_packusers` SET `usr_id`='%s', `pk_id`='%s', `subscribe_time`='%s';", $usr_id, $usr_pk, date("Y-m-d H:i:s")));
                    $db->execute($up_sql);
                }
                $notice = ($db->Affected_Rows() > 0) ? 1 : 0;
                echo $notice == 1 ? VGenerate::noticeTpl('', '', $language["notif.success.request"]) : null;
                return;
                break;
            case "act-update":
                global $cfg;

                $t         = 0;
                $log_array = array(
                    "log_upload",
                    "log_filecomment",
                    "log_rating",
                    "log_fav",
                    "log_responding",
                    "log_subscribing",
                    "log_following",
                    "log_pmessage",
                    "log_frinvite",
                    "log_delete",
                    "log_signin",
                    "log_signout",
                    "log_precovery",
                    "log_urecovery",
                );
                foreach ($log_array as $lv) {
                    $cfg[] = $class_database->getConfigurations($lv);
                    if ($cfg[$lv] == 1) {
                        $val = intval($_POST[$lv]);
                        $db->execute(sprintf("UPDATE `db_trackactivity` SET `%s`='%s' WHERE `usr_id`='%s' LIMIT 1;", $lv, $val, $usr_id));
                        $t += $db->Affected_Rows();
                    }
                }
                echo $html = ($_POST and $t > 0) ? VGenerate::noticeTpl(' bottom-padding10', $error_message, $language["notif.success.request"]) : null;
                break;
            case "perm-update":
                if ($_POST) {
                    $error    = null;
                    $p        = array();
                    $perm_arr = array(
                        "perm_upload_v"         => 1,
                        "perm_upload_s"         => 1,
                        "perm_upload_l"         => 1,
                        "perm_upload_i"         => 1,
                        "perm_upload_a"         => 1,
                        "perm_upload_d"         => 1,
                        "perm_upload_b"         => 1,
                        "perm_view_v"           => 1,
                        "perm_view_s"           => 1,
                        "perm_view_l"           => 1,
                        "perm_view_i"           => 1,
                        "perm_view_a"           => 1,
                        "perm_view_d"           => 1,
                        "perm_view_b"           => 1,
                        "perm_live_chat"        => 1,
                        "perm_live_vod"         => 1,
                        "perm_embed_single"     => 1,
                        "perm_embed_yt_video"   => 0,
                        "perm_embed_yt_channel" => 0,
                        "perm_embed_dm_video"   => 0,
                        "perm_embed_dm_user"    => 0,
                        "perm_embed_mc_video"   => 0,
                        "perm_embed_mc_user"    => 0,
                        "perm_embed_vi_user"    => 0,
                    );

                    foreach ($perm_arr as $pk => $pv) {
                        $q .= sprintf("`%s`='%s', ", $pk, intval($_POST[$pk]));
                        $p[$pk] = intval($_POST[$pk]);
                    }
                    $up_sql = sprintf("UPDATE `db_accountuser` SET `usr_perm`='%s' WHERE `usr_key`='%s' LIMIT 1;", serialize($p), $usr_key);
                    $db->execute($up_sql);
                    $notice      = ($db->Affected_Rows() > 0) ? $language["notif.success.request"] : null;
                    return $html = ($_POST and ($error != '' or $notice != '')) ? VGenerate::noticeTpl('', $error, $notice) : null;
                }
                break;
            case "usr-delete":
                $del_type = $class_filter->clr_str($_POST["user_del_type"]);
                $del_act  = ($del_type == 'db' or $del_type == 'all') ? self::userDelete_db($del_type) : null;
                break;
            case "usr-email":
                $es    = $_POST["email_subject"];
                $eb    = $_POST["email_body"];
                $ms    = $class_filter->clr_str($_POST["message_subject"]);
                $mb    = $class_filter->clr_str($_POST["message_body"]);
                $m_not = intval($_POST["message_notify"]);

                if ($es != '' and $eb != '') {
                    if ($_POST["cb_in2"] != '' and $_GET["u"] == 'sel') {
                        $pq = rawurldecode($_POST["cb_in2"]);
                        $sq = sprintf("SELECT `usr_email` FROM `db_accountuser` WHERE %s", $pq);
                        $pr = $db->execute($sq);
                        $ma = array();
                        if ($pr->fields["usr_email"]) {
                            while (!$pr->EOF) {
                                $ma[] = $pr->fields["usr_email"];
                                @$pr->MoveNext();
                            }
                        }
                        echo VGenerate::declareJS('$(".input-email-sub").val(""); $(".input-email-text").val("");');
                    } else {
                        $ma[] = $usr_mail;
                        echo VGenerate::declareJS('$("#usr-mail-form").get(0).reset();');
                    }

                    $mail_do = VNotify::queInit('private_email', $ma, 0);
                    $notice  = $language["notif.success.request"];
                } elseif ($es != '' or $eb != '') {
                    $error = $language["notif.error.required.field"] . '<span class="underlined">' . ($es != '' ? $language["backend.menu.members.entry10.sub2.body.e"] : $language["backend.menu.members.entry10.sub2.subj.e"]) . '</span>';
                }

                if ($ms != '' and $mb != '') {
                    if ($_POST["cb_in3"] != '' and $_GET["u"] == 'sel') {
                        $pq = rawurldecode($_POST["cb_in3"]);
                        $sq = sprintf("SELECT `usr_id`, `usr_email` FROM `db_accountuser` WHERE %s", $pq);
                        $pr = $db->execute($sq);
                        $ma = array();
                        if ($pr->fields["usr_id"]) {
                            while (!$pr->EOF) {
                                $ins_arr = array("msg_subj" => $ms, "msg_body" => $mb, "msg_from" => 0, "msg_to" => $pr->fields["usr_id"], "msg_date" => date("Y-m-d H:i:s"));
                                $ins_db  = $class_database->doInsert('db_messaging', $ins_arr);
                                $db_id   = $db->Insert_ID();
                                $mail_do = $m_not == 1 ? VNotify::queInit('private_message', array($pr->fields["usr_email"]), $db_id) : null;

                                @$pr->MoveNext();
                            }
                            $notice = $language["notif.success.request"];
                            echo VGenerate::declareJS('$(".input-msg-sub").val(""); $(".input-msg-text").val("");');
                        }
                    } else {
                        $ins_arr = array("msg_subj" => $ms, "msg_body" => $mb, "msg_from" => 0, "msg_to" => $usr_id, "msg_date" => date("Y-m-d H:i:s"));
                        $ins_db  = $class_database->doInsert('db_messaging', $ins_arr);
                        $notice  = $language["notif.success.request"];
                        $db_id   = $db->Insert_ID();
                        echo VGenerate::declareJS('$("#usr-msg-form").get(0).reset();');
                        $mail_do = $m_not == 1 ? VNotify::queInit('private_message', array($usr_mail), $db_id) : null;
                    }
                } elseif ($ms != '' or $mb != '') {
                    $error = $language["notif.error.required.field"] . '<span class="underlined">' . ($ms != '' ? $language["backend.menu.members.entry10.sub2.body.m"] : $language["backend.menu.members.entry10.sub2.subj.m"]) . '</span>';
                }

                return $html = ($_POST and ($error != '' or $notice != '')) ? VGenerate::noticeTpl('', $error, $notice) : null;
                break;
            case "usr-update":
            case "passw-update":
                $email_check  = new VValidation;
                $hasher       = new VPasswordHash(8, false);
                $usr_email    = $class_filter->clr_str($_POST["account_email_address"]);
                $email_ignore = intval($_POST["ignore_email"]);
                //check for valid email format
                $error_message = ($t == 'usr-update' and !$email_check->checkEmailAddress($usr_email) and $error_message == '') ? $language["frontend.signup.email.invalid"] : $error_message;
                //check for email domain restriction
                $error_message = ($t == 'usr-update' and $cfg["signup_domain_restriction"] == 1 and $error_message == '' and !VIPaccess::emailDomainCheck($usr_email)) ? $language["notif.error.nodomain"] : $error_message;
                //check for existing registered email
                $error_message = ($t == 'usr-update' and $email_ignore == 0 and VUserinfo::existingEmail($usr_email) and $error_message == '') ? $language["notif.error.existing.email"] : $error_message;
                //check for password match
                $error_message = ($t == 'passw-update' and md5($_POST["account_manage_pass_new"]) != md5($_POST["account_manage_pass_retype"]) and $error_message == '') ? $language["notif.error.pass.nomatch"] : $error_message;

                if ($error_message == '') {
                    if ($t == 'usr-update') {
                        $up_sql = sprintf("UPDATE `db_accountuser` SET `usr_email`='%s' WHERE `usr_id`='%s' LIMIT 1;", $usr_email, $usr_id);
                    } elseif ($t == 'passw-update') {
                        $enc_pass = $class_filter->clr_str($hasher->HashPassword($_POST["account_manage_pass_new"]));
                        $up_sql   = sprintf("UPDATE `db_accountuser` SET %s WHERE `usr_id`='%s' LIMIT 1;", (($_POST["account_manage_pass_new"] != '' and $_POST["account_manage_pass_retype"] != '') ? " `usr_password`='" . $enc_pass . "'" : null), $usr_id);
                    }

                    $db->execute($up_sql);
                    if ($db->Affected_Rows() > 0) {
                        $notice_message = $language["notif.success.request"];
                        echo VGenerate::declareJS('$("#usr-entry-email' . $usr_id . '").html("' . $usr_email . '");');
                    }
                }

                $html .= ($_POST and ($error_message != '' or $notice_message != '')) ? VGenerate::noticeTpl('', $error_message, $notice_message) : null;
                return $html;
                break;
        }
    }
    /* loop folder array */
    public function userAction_folderarray($f, $k)
    {
        global $language;
        $l = ' .......... ';

        foreach ($f[1][$k] as $fk => $fv) {
            $_s = VFileinfo::getDirectorySize($fv);

            $html .= '<li><span><i class="icon-folder-open"></i>' . $fv . $l . VUseraccount::numberFormat($_s, 1) . $l . ($_s["count"] . ' ' . $language["frontend.global.files"]) . $l . (is_dir($fv) ? VFileinfo::getOwnerships($fv) : '<span class="err-red">' . $language["frontend.global.not.found"] . '</span>') . '</span></li>';
        }
        return $html;
    }
    /* user actions, membership details window popup */
    public function userAction_subinfo($notice = '')
    {
        global $class_filter, $class_database, $language, $cfg;

        $usr_key = $class_filter->clr_str($_GET["u"]);
        $usr_id  = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $usr_key);
        $pk_id   = $class_database->singleFieldValue('db_packusers', 'pk_id', 'usr_id', $usr_id);
        $pk_id   = $pk_id != '' ? $pk_id : 0;

        $html .= '<link rel="stylesheet" type="text/css" href="' . $cfg["styles_url_be"] . '/account.css">';
        $html .= '<div id="lb-wrapper">';
        $html .= VGenerate::headingArticle('<span class="bold">' . $language["backend.menu.members.mem.for"] . $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_key', $usr_key) . '</span><span id="pk-key" class="no-display">' . $usr_key . '</span>', 'icon-coin');
        $html .= '<div id="pk-update' . $usr_key . '"></div>';
        $html .= '<form id="sub-info-form" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('vs-column full', '', VUseraccount::subscriptionStats(0, $usr_id, 1));
        $html .= '<div class="clearfix"></div><br>';
        $html .= VGenerate::simpleDivWrap('row right-float top-padding15', '', self::userAction_close('sub-info'));
        $html .= '<input type="hidden" name="sub_pk_id" id="h-pk-id" value="' . $pk_id . '" />';
        $html .= '</form>';
        $html .= '</div>';

        return $html;
    }
    /* user actions, membership details window popup */
    public function userAction_paidsub($notice = '')
    {
        global $class_filter, $class_database, $language, $cfg, $db;

        $usr_key = $class_filter->clr_str($_GET["u"]);
        $ui      = $db->execute(sprintf("SELECT `usr_id`, `usr_user`, `usr_sub_share`, `usr_sub_perc`, `usr_sub_currency` FROM `db_accountuser` WHERE `usr_key`='%s' LIMIT 1;", $usr_key));
        $usr_id  = $ui->fields["usr_id"];
        $pk_id   = 0;

        if (!$usr_id) {
            return;
        }

        $share_df = $cfg["sub_shared_revenue"];
        $share_nr = $ui->fields["usr_sub_perc"];
        $share    = $ui->fields["usr_sub_share"];
        $share_p  = $share == 1 ? $share_nr : $share_df;
        $user     = $ui->fields["usr_user"];
        $currency = $ui->fields["usr_sub_currency"];

        $_currency = explode(',', $language["supported_currency_names"]);
        $sel_opts  = null;
        foreach ($_currency as $v) {
            $sel_opts .= '<option value="' . $v . '"' . ($v == $currency ? ' selected="selected"' : null) . '>' . $v . '</option>';
        }

        $html .= '<div id="lb-wrapper">';
        $html .= VGenerate::headingArticle('<span class="bold">' . $language["backend.menu.members.sub.for"] . $ui->fields["usr_user"] . '</span><span id="pk-key" class="no-display">' . $usr_key . '</span>', 'icon-coin');
        $html .= '<div id="pk-update' . $usr_key . '"></div>';
        $html .= '<form id="paid-sub-form" method="post" action="" class="entry-form-class">';
        $html .= '<div><span class="">' . $user . ' is now receiving <b><span id="sub-share-nr">' . $share_p . '</span>%</b> from every paid channel subscription.</span></div><br>';
        $html .= VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="custom_subscription" class=""' . ($share == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry1.sub1.scp"] . $user . '</label>');
        $html .= '<br><label>' . $language["backend.menu.members.entry1.sub1.perc"] . '</label>';
        $html .= '<input type="text" name="backend_menu_members_entry1_sub1_rev" class="backend-text-input wd350" value="' . $share_p . '" id="ct-entry-details4-input">';
        $html .= '<div class="selector"><label>' . $language["backend.menu.af.p.currency"] . '</label><select name="backend_menu_sub_p_currency" class="backend-select-input">' . $sel_opts . '</select></div>';
        $html .= '<div class="clearfix"></div><br><br>';
        $html .= VGenerate::simpleDivWrap('row right-float top-padding15', '', self::userAction_close('paid-sub'));
        $html .= '<input type="hidden" name="sub_pk_id" id="h-pk-id" value="' . $pk_id . '" />';
        $html .= '</form>';
        $html .= '</div>';

        return $html;
    }
    /* update pk date */
    public function pkDate($pk)
    {
        global $class_database;

        $days        = $pk > 0 ? $class_database->singleFieldValue('db_packtypes', 'pk_period', 'pk_id', $pk) : 0;
        $expire_time = date("Y-m-d H:i:s", strtotime("+" . $days . " day"));

        echo VGenerate::declareJS('$(".sub-expire").val("' . $expire_time . '");');
    }
    /* subscription list */
    public function subscriptionList()
    {
        global $class_filter, $class_database, $db;

        $usr_key = $class_filter->clr_str($_GET["u"]);
        $usr_id  = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $usr_key);
        $pk_id   = $class_database->singleFieldValue('db_packusers', 'pk_id', 'usr_id', $usr_id);
        $sql     = $db->execute(sprintf("SELECT `pk_id`, `pk_name`, `pk_period` FROM `db_packtypes` WHERE `pk_active`='1' ORDER BY `pk_id`;"));

        if ($sql->fields["pk_id"]) {
            $ht = '<br><select name="sub_pk" id="sub-pk-list" class="select-input wd150 right-align" onchange="subDateChange(this.value);">';
            $ht .= '<option value=""> --- </option>';
            while (!$sql->EOF) {
                $ht .= '<option value="' . $sql->fields["pk_id"] . '"' . ($sql->fields["pk_id"] == $pk_id ? ' selected="selected"' : null) . ' rel-key="' . $usr_key . '">' . $sql->fields["pk_name"] . '</option>';
                @$sql->MoveNext();
            }
            $ht .= '</select>';

            $ht .= '    <script type="text/javascript">
            $(function(){
                SelectList.init("sub_pk");
            });
            </script>
        ';
        }

        $html = VGenerate::simpleDivWrap('selector', '', $ht);

        return $html;
    }
    /* user actions, folder info window popup */
    public function userAction_folderinfo()
    {
        global $class_database, $class_filter, $language, $cfg, $smarty;

        $usr_key = $class_filter->clr_str($_GET["u"]);
        $f       = VSignup::getUserFolders($usr_key);

        $_s1 = VFileinfo::getDirectorySize($f[0][0]);
        $_s2 = VFileinfo::getDirectorySize($f[0][1]);
        $_s3 = VFileinfo::getDirectorySize($f[0][2]);

        $uf = $_GET["a"] == 'reset' ? VSignup::createUserFolders($class_filter->clr_str($_GET["u"])) : null;

        $ht .= '<link type="text/css" rel="stylesheet" href="' . $cfg["main_url"] . '/f_modules/m_backend/m_tools/m_treeview/jquery.treeview.css">';
        $ht .= '<script type="text/javascript" src="' . $cfg["main_url"] . '/f_modules/m_backend/m_tools/m_treeview/jquery.treeview.js"></script>';
        $ht .= '<div id="lb-wrapper">';
        $ht .= '<div class="entry-list vs-column full">';
        $ht .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $ht .= '<li>';
        $ht .= '<div>';
        $ht .= '<div class="responsive-accordion-head-off active">';
        $ht .= VGenerate::headingArticle('<span>' . $language["backend.menu.members.server.loc"] . $class_database->singleFieldValue('db_accountuser', 'usr_user', 'usr_key', $usr_key) . '</span>', 'icon-folder');
        $ht .= '</div>';
        $ht .= '<div class="responsive-accordion-panel active d-flex d-column">';
        $ht .= '<ul id="browser" class="filetree treeview">';
        $ht .= '<li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span><i class="icon-folder-open"></i>' . $f[0][0] . ' - [' . $language["backend.menu.members.entry10.sub2.f1"] . ']</span>';
        $ht .= '<ul>' . self::userAction_folderarray($f, 0) . '</ul>';
        $ht .= '</li>';
        $ht .= '<li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span><i class="icon-folder-open"></i>' . $f[0][1] . ' - [' . $language["backend.menu.members.entry10.sub2.f2"] . ']</span>';
        $ht .= '<ul>' . self::userAction_folderarray($f, 1) . '</ul>';
        $ht .= '</li>';
        $ht .= '<li class="collapsable"><div class="hitarea collapsable-hitarea"></div><span><i class="icon-folder-open"></i>' . $f[0][2] . ' - [' . $language["backend.menu.members.entry10.sub2.f3"] . ']</span>';
        $ht .= '<ul>' . self::userAction_folderarray($f, 2) . '</ul>';
        $ht .= '</li>';
        $ht .= '</ul>';
        $ht .= VGenerate::simpleDivWrap('row right-float user-action-wrap', '', self::userAction_close('folder-info'));
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '</li>';
        $ht .= '</ul>';
        $ht .= '</div>';
        $ht .= '</div>';

        $html = $ht;
        $html .= '<script type="text/javascript">' . $smarty->fetch($cfg["javascript_dir_be"] . '/settings-accordion.js') . '</script>';

        return $html;
    }
    /* user accounts template */
    public function userEntry($_dsp, $type, $entry_id, $info_array, $pr_array, $ch_array)
    {
        global $language, $class_database, $cfg, $db, $backend_access_url;

        $_user_id    = $info_array[0];
        $_user_key   = $info_array[1];
        $_user_name  = $info_array[2];
        $_user_act   = $info_array[3];
        $_user_email = $info_array[4];
        $_user_ip    = $info_array[5];
        $_user_feat  = $info_array[6];
        $_user_login = $info_array[7];
        $_user_last  = $info_array[8];
        $_user_date  = $info_array[9];
        $_user_del   = $info_array[10];
        $_user_ver   = $info_array[11];
        $_user_v     = $info_array[12];
        $_user_i     = $info_array[13];
        $_user_a     = $info_array[14];
        $_user_d     = $info_array[15];
        $_user_b     = $info_array[16];
        $_user_l     = $info_array[17];
        $_user_s     = $info_array[18];
        /* more */
        $_user_descr = $pr_array[0];
        $_user_web   = $pr_array[1];
        $_user_fname = $pr_array[2];
        $_user_lname = $pr_array[3];
        $_user_pic   = $pr_array[4];
        $_user_town  = $pr_array[5];
        $_user_city  = $pr_array[6];
        $_user_zip   = $pr_array[7];
        $_user_ctry  = $pr_array[8];
        $_user_bday  = $pr_array[9];
        $_user_gen   = $pr_array[10];
        $_user_rel   = $pr_array[11];
        $_user_age   = VUserinfo::ageFromString($_user_bday);
        $_user_occup = $pr_array[13];
        $_user_comp  = $pr_array[14];
        $_user_sch   = $pr_array[15];
        $_user_inter = $pr_array[16];
        $_user_mov   = $pr_array[17];
        $_user_music = $pr_array[18];
        $_user_book  = $pr_array[19];
        $_user_delr  = $pr_array[20];
        $_user_promo = $pr_array[21];
        $_user_aff   = $pr_array[22];
        $_user_prt   = $pr_array[23];
        /* more */
        $_user_chan  = $ch_array[0];
        $_user_views = $ch_array[1];
        $_user_ch    = $class_database->singleFieldValue('db_usertypes', 'db_name', 'db_id', $_user_chan);
        $_user_ch    = $_user_ch == '' ? '-' : $_user_ch;

        $_init = VbeEntries::entryInit($_dsp, $_user_id, $entry_id);
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $tabs = '<div class="swiper-ph swiper-ph-main"></div>';
        $tabs .= '<div class="swiper-top swiper-top-main" style="display:' . VGenerate::ssd() . '">';
        $tabs .= '<div class="swiper-button-prev swiper-button-prev-main"' . VGenerate::sso() . '></div>';
        $tabs .= '<div class="swiper-button-next swiper-button-next-main"' . VGenerate::sso() . '></div>';
        $tabs .= '<div class="swiper swiper-main">';
        $tabs .= '<div class="swiper-wrapper">';
        $tabs .= '<div class="swiper-slide swiper-slide-current"><a class="icon icon-list" rel="user_details" href="javascript:;"><span>' . $language["account.btn.details"] . '</span></a></div>';
        $tabs .= '<div class="swiper-slide"><a class="icon icon-envelope" rel="send-email" href="javascript:;"><span>' . $language["account.btn.send.email"] . '</span></a></div>';
        $tabs .= '<div class="swiper-slide"><a class="icon icon-key" rel="change-email" href="javascript:;"><span>' . $language["account.btn.change.email"] . '</span></a></div>';
        $tabs .= '<div class="swiper-slide"><a class="icon icon-history" rel="user-activity" href="javascript:;"><span>' . $language["account.btn.activity"] . '</span></a></div>';
        $tabs .= '<div class="swiper-slide"><a class="icon icon-checkmark-circle" rel="user-perm" href="javascript:;"><span>' . $language["account.btn.perm"] . '</span></a></div>';
        $tabs .= '<div class="swiper-slide"><a class="icon icon-folder" rel="folder-info" href="javascript:;"><span>' . $language["account.btn.folder.info"] . '</span></a></div>';
        $tabs .= ($cfg["user_subscriptions"] == 1 and $_user_prt == 1) ? '<div class="swiper-slide"><a class="icon icon-coin" rel="paid-sub" href="javascript:;"><span>' . $language["account.btn.subscription"] . '</span></a></div>' : null;
        $tabs .= ($cfg["affiliate_module"] == 1 and $_user_aff == 1) ? '<div class="swiper-slide"><a class="icon icon-coin" rel="affiliate-payout" href="javascript:;"><span>' . $language["account.entry.payout.custom"] . '</span></a></div>' : null;
        $tabs .= $cfg["paid_memberships"] == 1 ? '<div class="swiper-slide"><a class="icon icon-coin" rel="sub-info" href="javascript:;"><span>' . $language["account.btn.membership"] . '</span></a></div>' : null;
        $tabs .= '<div class="swiper-slide"><a class="icon icon-times" rel="del-account" href="javascript:;"><span>' . $language["frontend.global.delete"] . '</span></a></div>';
        $tabs .= '</div></div></div>';
        // $html .= '<div class="popupbox-mem" id="popuprel' . $_user_id . '"></div><div id="fade' . $_user_id . '" class="fade"></div>';
        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $_user_id . '" style="display: ' . $_dsp . ';"><form id="add-entry-form' . $_user_id . '" method="post" action="" class="entry-form-class">';
        /* top buttons */
        $html .= '<div class="tabs tabs-style-topline">';
        $html .= $tabs;
        $html .= '<nav class="no-display">';
        $html .= '<ul class="ul-no-list uactions-list left-float top-bottom-padding bottom-border">';
        $html .= '<li class="user-details active" id="user_details" name="send_email" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '" rel-id="' . $_user_id . '"><a class="icon icon-list" href="javascript:;"><span>' . $language["account.btn.details"] . '</span></a></li>';
        $html .= '<li class="send-email popup" id="send-email" name="send_email" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '" rel-id="' . $_user_id . '"><a class="icon icon-envelope" href="javascript:;"><span>' . $language["account.btn.send.email"] . '</span></a></li>';
        $html .= '<li class="change-email popup" id="change-email" name="change_email" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '"  rel-id="' . $_user_id . '"><a class="icon icon-key" href="javascript:;"><span>' . $language["account.btn.change.email"] . '</span></a></li>';
        $html .= '<li class="user-activity popup" id="user-activity" name="user_activity" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '"  rel-id="' . $_user_id . '"><a class="icon icon-history" href="javascript:;"><span>' . $language["account.btn.activity"] . '</span></a></li>';
        $html .= '<li class="user-perm popup" id="user-perm" name="user_perm" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '"  rel-id="' . $_user_id . '"><a class="icon icon-checkmark-circle" href="javascript:;"><span>' . $language["account.btn.perm"] . '</span></a></li>';
        $html .= '<li class="folder-info popup" id="folder-info" name="folder_info" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '"  rel-id="' . $_user_id . '"><a class="icon icon-folder" href="javascript:;"><span>' . $language["account.btn.folder.info"] . '</span></a></li>';
        $html .= ($cfg["user_subscriptions"] == 1 and $_user_prt == 1) ? '<li class="paid-sub popup" id="paid-sub" name="paid_sub" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '"  rel-id="' . $_user_id . '"><a class="icon icon-coin" href="javascript:;"><span>' . $language["account.btn.subscription"] . '</span></a></li>' : null;
        $html .= ($cfg["affiliate_module"] == 1 and $_user_aff == 1) ? '<li class="affiliate-payout popup" id="affiliate-payout" name="affiliate_payout" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '"  rel-id="' . $_user_id . '"><a class="icon icon-coin" href="javascript:;"><span>' . $language["account.entry.payout.custom"] . '</span></a></li>' : null;
        $html .= $cfg["paid_memberships"] == 1 ? '<li class="sub-info popup" id="sub-info" name="sub_info" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '"  rel-id="' . $_user_id . '"><a class="icon icon-coin" href="javascript:;"><span>' . $language["account.btn.membership"] . '</span></a></li>' : null;
        $html .= '<li class="folder-info popup" id="del-account" name="del_account" rel="popuprel' . $_user_id . '" rel-key="' . $_user_key . '"  rel-id="' . $_user_id . '"><a class="icon icon-times" href="javascript:;"><span>' . $language["frontend.global.delete"] . '</span></a></li>';
        $html .= '</ul></nav>';
        $html .= '</div>';
        /* left side details */
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($_user_id)));
        $html .= '<div class="entry-details-wrap">';
        $html .= '<div class="vs-column fourths">';
        $html .= '<ul class="entry-details">';
        $html .= '<li><div>' . $language["account.profile.account"] . '</div><div>' . ($_user_act == 1 ? '<span class="conf-green">' . $language["frontend.global.active"] . '</span>' : '<span class="err-red">' . $language["frontend.global.inactive"] . '</span>') . '</div></li>';
        $html .= '<li><div>' . $language["account.profile.deleted"] . '</div><div>' . ($_user_del == 0 ? '<span class="conf-green">' . $language["frontend.global.no"] . '</span>' : '<span class="err-red">' . $language["frontend.global.yes"] . '</span>') . '</div></li>';
        $html .= '<li><div>' . $language["files.menu.featured"] . '</div><div>' . ($_user_feat == 1 ? '<span class="conf-green">' . $language["frontend.global.yes"] . '</span>' : '<span class="err-red">' . $language["frontend.global.no"] . '</span>') . '</div></li>';
        $html .= '<li><div>' . $language["files.menu.promoted"] . '</div><div>' . ($_user_promo == 1 ? '<span class="conf-green">' . $language["frontend.global.yes"] . '</span>' : '<span class="err-red">' . $language["frontend.global.no"] . '</span>') . '</div></li>';

        $html .= '<li><div>' . $language["account.email.address"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', 'usr-entry-email' . $_user_id, $_user_email, '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.profile.email.verified"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_ver == 1 ? '<span class="conf-green">' . $language["frontend.global.yes"] . '</span>' : '<span class="err-red">' . $language["frontend.global.no"] . '</span>', '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.profile.joined"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_date, '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.profile.last.login"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_last, '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.profile.login.count"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_login, '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.profile.login.ip"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_ip . self::checkBannedIP($_user_ip), '', 'span') . '</div></li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '<div class="vs-column fourths">';
        $html .= '<ul class="entry-details">';
        $html .= '<li><div>' . $language["account.overview.live.up"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20', '', '<a href="' . $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_files') . '?u=l' . $_user_key . '">' . self::numFormat($_user_l) . '</a>', '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.live.view"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', self::fileHits('live', $_user_id), '', 'span') . '</div></li>';

        $html .= '<li><div>' . $language["account.overview.vid.up"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20', '', '<a href="' . $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_files') . '?u=v' . $_user_key . '">' . self::numFormat($_user_v) . '</a>', '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.vid.view"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', self::fileHits('video', $_user_id), '', 'span') . '</div></li>';

        $html .= '<li><div>' . $language["account.overview.short.up"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20', '', '<a href="' . $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_files') . '?u=s' . $_user_key . '">' . self::numFormat($_user_s) . '</a>', '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.short.view"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', self::fileHits('short', $_user_id), '', 'span') . '</div></li>';

        $html .= '<li><div>' . $language["account.overview.img.up"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20', '', '<a href="' . $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_files') . '?u=i' . $_user_key . '">' . self::numFormat($_user_i) . '</a>', '', 'span') . '</div></li>';

        $html .= '<li><div>' . $language["account.overview.img.view"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', self::fileHits('image', $_user_id), '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.aud.up"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20', '', '<a href="' . $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_files') . '?u=a' . $_user_key . '">' . self::numFormat($_user_a) . '</a>', '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.aud.view"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', self::fileHits('audio', $_user_id), '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.doc.up"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20', '', '<a href="' . $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_files') . '?u=d' . $_user_key . '">' . self::numFormat($_user_d) . '</a>', '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.doc.view"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', self::fileHits('doc', $_user_id), '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.blog.up"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20', '', '<a href="' . $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey('be_files') . '?u=b' . $_user_key . '">' . self::numFormat($_user_b) . '</a>', '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.blog.view"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', self::fileHits('blog', $_user_id), '', 'span') . '</div></li>';

        $html .= '</ul>';
        $html .= '</div>';
        /* right side details */
        $html .= '<div class="vs-column fourths">';
        $html .= '<ul class="entry-details">';
        $html .= '<li><div>' . $language["account.profile.key"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_key, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.personal.firstname"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_fname, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.personal.lastname"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_lname, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.about.describe"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_descr, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.about.website"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', '<a href="' . $_user_web . '" target="_blank">' . $_user_web . '</a>', '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.location.country"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_ctry, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.location.city"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_city, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.location.town"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_town, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.location.zip"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_zip, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.overview.chan.type"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_ch, '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.chan.view"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', self::numFormat($_user_views), '', 'span') . '</div></li>';
        $html .= '<li><div>' . $language["account.overview.chan.subs"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', self::numFormat(VUserpage::getSubCount($_user_id)), '', 'span') . '</div></li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '<div class="vs-column fourths fit">';
        $html .= '<ul class="entry-details">';
        $html .= '<li><div>' . $language["account.profile.personal.bday"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_bday, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.personal.age"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_age, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.personal.gender"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_gen, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.personal.rel"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_rel, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.job.occup"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_occup, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.job.companies"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_comp, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.education.school"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_sch, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.interests"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_inter, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.interests.movies"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_mov, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.interests.music"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_music, '', 'span') . '</div>';
        $html .= '<li><div>' . $language["account.profile.interests.books"] . '</div><div>' . VGenerate::simpleDivWrap('left-float lh20 conf-green', '', $_user_book, '', 'span') . '</div>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $_user_id . '" />';
        $html .= '</form></div><div class="clearfix no-display"></div>';

        return $html;
    }
    /* list menu actions, sort files */
    public function listMenuActions($type)
    {
        global $cfg, $language, $section, $href, $class_filter, $class_database;

        $btn_id   = 'sort-file-time';
        $div_id   = 'file-sort-div';
        $type     = 'file-time-actions';
        $menu_arr = array(
            "sort-active"    => "files.menu.active",
            "sort-inactive"  => "files.menu.inactive",
            "sort-deleted"   => "files.menu.deleted",
            "sort-recent"    => "files.menu.recent",
            // "sort-affiliated" => "files.menu.affiliated",
            "sort-partnered" => "files.menu.partnered",
            "sort-promoted"  => "files.menu.promoted",
            "sort-featured"  => "files.menu.featured",
            "sort-views"     => "files.menu.ch.views",
            "sort-lcount"    => "files.menu.l.count",
        );
        $menu_arr_icons = array(
            "sort-active"    => "icon-play",
            "sort-inactive"  => "icon-stop",
            "sort-deleted"   => "icon-times",
            "sort-recent"    => "icon-clock",
            // "sort-affiliated" => "icon-coin",
            "sort-partnered" => "icon-coin",
            "sort-promoted"  => "icon-bullhorn",
            "sort-featured"  => "icon-star",
            "sort-views"     => "icon-eye",
            "sort-lcount"    => "icon-enter",
        );

        if ($cfg["live_module"] == 1) {
            $menu_arr["sort-lvcount"]       = 'files.menu.lv.count';
            $menu_arr_icons["sort-lvcount"] = 'icon-live';
        }
        if ($cfg["video_module"] == 1) {
            $menu_arr["sort-vcount"]       = 'files.menu.v.count';
            $menu_arr_icons["sort-vcount"] = 'icon-video';
        }
        if ($cfg["short_module"] == 1) {
            $menu_arr["sort-scount"]       = 'files.menu.s.count';
            $menu_arr_icons["sort-scount"] = 'icon-mobile';
        }
        if ($cfg["image_module"] == 1) {
            $menu_arr["sort-icount"]       = 'files.menu.i.count';
            $menu_arr_icons["sort-icount"] = 'icon-image';
        }
        if ($cfg["audio_module"] == 1) {
            $menu_arr["sort-acount"]       = 'files.menu.a.count';
            $menu_arr_icons["sort-acount"] = 'icon-headphones';
        }
        if ($cfg["document_module"] == 1) {
            $menu_arr["sort-dcount"]       = 'files.menu.d.count';
            $menu_arr_icons["sort-dcount"] = 'icon-file';
        }
        if ($cfg["blog_module"] == 1) {
            $menu_arr["sort-bcount"]       = 'files.menu.b.count';
            $menu_arr_icons["sort-bcount"] = 'icon-pencil2';
        }

        $int_val  = $_POST[str_replace('-', '_', $div_id) . '_val'] == '' ? (($_GET["do"] != '' and array_key_exists($_GET["do"], $menu_arr)) ? $_GET["do"] : 'sort-recent') : $_POST[str_replace('-', '_', $div_id) . '_val'];
        $menu_top = ($_GET["do"] == '' or $language[$menu_arr[$int_val]] == '') ? $language["files.menu.recent"] : $language[$menu_arr[$int_val]];
        unset($menu_arr[$int_val]);

        $m_count = count($menu_arr);
        $html .= '<div class="left-float menu-drop">';
        $html .= '<div id="' . $type . '" class="dl-menuwrapper">';
        $html .= '<span class="dl-trigger sort-trigger" rel="tooltip" title="' . $language["frontend.global.apply.filter"] . '"><span class="sort-selected" id="' . $int_val . '">' . $menu_top . '</span></span>';
        $html .= '<ul' . ($m_count < 1 ? ' style="display: none;"' : null) . ' class="dl-menu">';

        foreach ($menu_arr as $key => $val) {
            $html .= '<li><a id="' . $key . '" href="javascript:;" class="count" rel="nofollow" onclick="$(\'#' . $div_id . '-val\').val($(this).attr(\'id\'));"><i class="' . $menu_arr_icons[$key] . '"></i> ' . trim($language[$val]) . '</a></li>';
        }

        $html .= '</ul>';

        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="no-display"><form id="sort-user-actions" class="entry-form-class" method="post" action=""><input type="hidden" id="' . $div_id . '-val" name="' . str_replace('-', '_', $div_id) . '_val" value="' . ($int_val) . '" /></form></div>';
        $html .= VGenerate::declareJS('$(function() { $( "#' . $type . '" ).dlmenu({ animationClasses : { classin : "dl-animate-in-5", classout : "dl-animate-out-5" } }); });');

        return $html;
    }

    /* ban details */
    public function banDetails($_dsp = '', $entry_id = '', $db_id = '', $ban_active = '', $ban_ip = '', $ban_descr = '')
    {
        global $class_filter, $language, $db;

        if ($_POST and $_GET["do"] == 'add') {
            self::processBanEntry();

            $form      = VArraySection::getArray("ban_list");
            $ban_ip    = $form[0]["ban_ip"];
            $ban_descr = $form[0]["ban_descr"];

            if ($db->Affected_Rows() > 0) {
                $ban_ip    = null;
                $ban_descr = null;
            }
        }

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_sct  = 'ban_list';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $_btn = $_GET["do"] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET["do"] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET["do"] == 'add' ? $language["frontend.global.savenew"] : $language["frontend.global.saveupdate"]) . '</span>'), 'display: inline-block-off;') : null;

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="ban-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language["frontend.global.estate"] . '</label>' . ($ban_active == 1 ? '<span class="conf-green">' . $language["frontend.global.active"] . '</span>' : '<span class="err-red">' . $language["frontend.global.inactive"] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::simpleDivWrap('row top-padding5', '', '<label>' . str_replace($language["backend.menu.blank.lines"], '', $language["backend.menu.network.ranges"]) . '</label>');
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry3.sub5.ip"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry3_sub5_ip', 'backend-text-input wd350', $ban_ip);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["frontend.global.note"] . '</label>', 'left-float', 'frontend_global_note', 'backend-text-input wd350', $ban_descr);
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language["frontend.global.required.items"]);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</form></div>';

        return $html;
    }
    /* process ban entry */
    public function processBanEntry()
    {
        global $class_database, $class_filter, $language, $db;

        $rem_ip         = $class_filter->clr_str($_SERVER[REM_ADDR]);
        $form           = VArraySection::getArray("ban_list");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];
        $ban_ip         = $form[0]["ban_ip"];
        $ban_descr      = $form[0]["ban_descr"];
        $ct_id          = intval($_POST["hc_id"]);
        $error_message  = VForm::checkEmptyFields($allowedFields, $requiredFields);
        $error_message  = ($error_message == '' and VIPaccess::banIPrange_single($rem_ip, $ban_ip) == 1) ? $language["notif.error.invalid.request"] : $error_message;
        $error_message  = ($error_message == '' and $rem_ip == $ban_ip) ? $language["notif.error.invalid.request"] : $error_message;

        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            switch ($_GET["do"]) {
                case "update":
                    $sql = sprintf("UPDATE `db_banlist` SET `ban_ip`='%s', `ban_descr`='%s' WHERE `ban_id`='%s' LIMIT 1;", $ban_ip, $ban_descr, $ct_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_banlist', $form[0]);
                    break;
            }
            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language["notif.success.request"]);
            }

        }
    }
    /* check for banned IP */
    public function checkBannedIP($ip)
    {
        global $class_database, $language;

        $is_ban = $class_database->singleFieldValue('db_banlist', 'ban_active', 'ban_ip', $ip);
        return ($is_ban == 1 ? '<span class="bold"> ' . $language["account.text.banned"] . '</span>' : null);
    }
    /* file hits count */
    public function fileHits($type, $uid)
    {
        global $db;

        $sql = sprintf("SELECT SUM(`file_views`) AS `total` FROM `db_%sfiles` WHERE `usr_id`='%s'", $type, $uid);
        $rs  = $db->execute($sql);

        return $rs->fields["total"];
    }
    /* number format */
    public function numFormat($for)
    {
        return number_format($for, 0, '', ',');
    }
    /* channel types template */
    public function chTypes($_dsp = '', $entry_id = '', $db_id = '', $db_name = '', $db_desc = '', $db_style = '', $db_infl = '', $db_state = '')
    {
        global $language;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_sct  = 'channel_types';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        if ($_POST) {
            $all_array   = VArraySection::getArray($_sct);
            $entry_array = $all_array[0];

            switch ($_GET["do"]) {
                case 'add':
                    $results = VbeEntries::processEntry($_sct, 'add');
                    $html    = $results[2];
                    if ($results[0] != '') {
                        $db_name  = $entry_array["db_name"];
                        $db_desc  = $entry_array["db_desc"];
                        $db_style = $entry_array["db_styles"];
                        $db_infl  = $entry_array["db_influences"];
                    }break;
                case 'update':
                    $results = VbeEntries::processEntry($_sct, 'update', $db_id);
                    $html    = $results[2];
                    break;
            }
        }

        $_btn = VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'search-button form-button ' . ($_GET["do"] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, $language["frontend.global.saveupdate"]));

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="add-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', $language["frontend.global.estate"]) . VGenerate::simpleDivWrap('left-float lh20', '', $db_state == 1 ? '<span class="conf-green">' . $language["frontend.global.active"] . '</span>' : '<span class="err-red">' . $language["frontend.global.inactive"] . '</span>'));
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', $language["backend.menu.members.entry1.sub2.entry.name"] . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_name', 'backend-text-input wd350', $db_name);
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', $language["backend.menu.members.entry1.sub2.entry.desc"], 'left-float', 'backend_menu_members_entry1_sub2_entry_desc', 'backend-textarea-input wd350', $db_desc);
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', $language["backend.menu.members.entry2.sub2.style"], 'left-float', 'backend_menu_members_entry2_sub2_style', 'backend-textarea-input wd350', $db_style);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', $language["backend.menu.members.entry2.sub2.infl"]) . VGenerate::simpleDivWrap('left-float lh20', '', '<input ' . ($db_infl == 1 ? 'checked="checked"' : null) . ' type="checkbox" class="cb-exclude" name="backend_menu_members_entry2_sub2_infl" value="1" />'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language["frontend.global.required.items"]);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</form>';

        if ($_GET["do"] != 'add') {
            $html .= '<div class="left-float wd97p top-border">';
            $html .= '<form id="source-fields-form' . $db_id . '" method="post" action="">';

            $html .= '<div class="left-float wdmax top-bottom-padding">';
            $html .= '<div class="left-float lh20 wd140">' . $language["backend.menu.members.entry2.sub2.create.custom"] . '</div>';
            $html .= '<div class="left-float"><select name="custom_fields" class="backend-select-input wd100 change-custom-field insert-custom-field' . $db_id . '"><option>---</option><option value="text">' . $language["backend.menu.members.entry2.sub2.opt.text"] . '</option><option value="input">' . $language["backend.menu.members.entry2.sub2.opt.input"] . '</option><option value="link">' . $language["backend.menu.members.entry2.sub2.opt.link"] . '</option><option value="select">' . $language["backend.menu.members.entry2.sub2.opt.select"] . '</option></select></div>';
            $html .= '<div class="left-float lh20 left-padding10"><a href="javascript:;" class="insert-custom-field" id="custom-field' . $db_id . '">' . $language["backend.menu.members.entry2.sub2.insert"] . '</a></div>';
            $html .= '</div>';

            $html .= '<div class="source-custom-field' . $db_id . '">';
            $html .= self::customFieldsHTML("text");
            $html .= self::customFieldsHTML("input");
            $html .= self::customFieldsHTML("link");
            $html .= self::customFieldsHTML("select");
            $html .= '</div>'; // end "custom-fields-source"
            $html .= '<div class="left-float no-display"><input type="hidden" name="custom_db_id" value="' . $db_id . '" /></div>';
            $html .= '</form>'; // end "source-fields-form"
            $html .= '<div class="custom-fields-response left-float wdmax">' . self::listCustomFields($db_id) . '</div>';
            $html .= '</div>'; // end "left-float wd97p top-border"
            $html .= '</div>';
        }

        return $html;
    }
    /* custom fields html templates */
    public function customFieldsHTML($type, $left = '', $right = '', $arr_key = '', $thedb_id = '')
    {
        global $language;

        $ht_left    = $left != '' ? ' value="' . $left . '"' : null;
        $ht_right   = $right != '' ? ' value="' . $right . '"' : null;
        $cancel_txt = $left != '' ? $language["backend.menu.members.entry2.sub2.this.remove"] : $language["backend.menu.members.entry2.sub2.this.cancel"];
        $edit_class = $left != '' ? 'edit' : null;
        $rem_class  = $left != '' ? 'remove' : 'cancel';
        $db_id      = $left != '' ? $arr_key : rand(0, 99999);
        $save_id    = $left != '' ? 'id="save-custom-edit-' . $arr_key . '" ' : null;
        $save_txt   = $left != '' ? $language["backend.menu.members.entry2.sub2.this.save"] : $language["backend.menu.members.entry2.sub2.this.save.new"];

        $html .= '<div id="custom-wrap' . $db_id . '" class="row left-float wdmax light-bg top-bottom-padding cc-border custom-' . $type . ($left == '' ? ' no-display custom-wrap-' . $type : null) . '">';
        $html .= $left != '' ? '<form id="save-fields-form' . $db_id . '-' . $thedb_id . '" method="post" action="">' : null;
        switch ($type) {
            case "text":
                $html .= '<div class="row no-top-padding"><div class="left-float wd140 lh20 left-padding5"><span class="source_text_label_req">' . $language["backend.menu.members.entry2.sub2.label"] . '</span>' . $language["frontend.global.required"] . '</div><div class="left-float"><input type="text" name="source_text_label" class="wd350 backend-text-input"' . $ht_left . ' /></div><div class="right-float right-padding10 lh20 save-text-response"></div></div>';
                $html .= '<div class="row wdmax"><div class="left-float wd140 lh20 left-padding5"><span class="source_text_descr_req">' . $language["backend.menu.members.entry2.sub2.value"] . '</span>' . $language["frontend.global.required"] . '</div><div class="left-float"><input type="text" name="source_text_descr" class="wd350 backend-text-input"' . $ht_right . ' /></div>';
                $html .= '<div class="right-float right-padding10 lh20"><a href="javascript:;" ' . $save_id . 'class="savecustomtext' . $edit_class . '">' . $save_txt . '</a> - <a href="javascript:;" id="' . $rem_class . '-text-field-' . $db_id . '" class="' . $rem_class . '-custom-field">' . $cancel_txt . '</a></div></div>';
                break;
            case "input":
                $html .= '<div class="row no-top-padding"><div class="left-float wd140 lh20 left-padding5"><span class="source_input_label_req">' . ($type == 'input' ? $language["backend.menu.members.entry2.sub2.label"] : $language["backend.menu.members.entry2.sub2.link.name"]) . '</span>' . $language["frontend.global.required"] . '</div><div class="left-float"><input type="text" name="source_input_label" class="wd350 backend-text-input"' . $ht_left . ' /></div><div class="right-float right-padding10 lh20 save-input-response"></div></div>';
                $html .= '<div class="row wdmax"><div class="left-float wd140 lh20 left-padding5">' . ($type == 'input' ? $language["backend.menu.members.entry2.sub2.input.value"] : $language["backend.menu.members.entry2.sub2.link.href"]) . '</div><div class="left-float"><input type="text" style="font-style:italic;" readonly="readonly" name="source_input_descr" value="' . $language["backend.menu.members.entry2.sub2.set.text"] . '" class="wd350 backend-text-input" /></div>';
                $html .= '<div class="right-float right-padding10 lh20"><a href="javascript:;" ' . $save_id . 'class="savecustom' . $type . $edit_class . '">' . $save_txt . '</a> - <a href="javascript:;" id="' . $rem_class . '-input-field-' . $db_id . '" class="' . $rem_class . '-custom-field">' . $cancel_txt . '</a></div></div>';
                break;
            case "link":
                $html .= '<div class="row no-top-padding"><div class="left-float wd140 lh20 left-padding5"><span class="source_link_label_req">' . $language["backend.menu.members.entry2.sub2.link.name"] . '</span>' . $language["frontend.global.required"] . '</div><div class="left-float"><input type="text" name="source_link_label" class="wd350 backend-text-input"' . $ht_left . ' /></div><div class="right-float right-padding10 lh20 save-link-response"></div></div>';
                $html .= '<div class="row wdmax"><div class="left-float wd140 lh20 left-padding5">' . $language["backend.menu.members.entry2.sub2.link.href"] . '</div><div class="left-float"><input type="text" style="font-style:italic;" readonly="readonly" name="source_link_descr" value="' . $language["backend.menu.members.entry2.sub2.set.text"] . '" class="wd350 backend-text-input" /></div>';
                $html .= '<div class="right-float right-padding10 lh20"><a href="javascript:;" ' . $save_id . 'class="savecustom' . $type . $edit_class . '">' . $save_txt . '</a> - <a href="javascript:;" id="' . $rem_class . '-link-field-' . $db_id . '" class="' . $rem_class . '-custom-field">' . $cancel_txt . '</a></div></div>';
                $html .= '<div class="row wdmax"><div class="left-float wd140 lh20 left-padding5">&nbsp;</div><div class="left-float"><span class="left-float lh20"><input type="checkbox" ' . ($right == 'img_link' ? 'checked="checked"' : null) . ' name="allow_img_linking" class="cb-exclude" value="1" /></span><span class="left-float lh20">' . $language["backend.menu.members.entry2.sub2.set.img"] . '</span></div></div>';
                break;
            case "select":
                $ht_right = $right != '' ? str_replace(",", "\n", $right) : null;
                $html .= '<div class="row no-top-padding"><div class="left-float wd140 lh20 left-padding5"><span class="source_select_label_req">' . $language["backend.menu.members.entry2.sub2.label"] . '</span>' . $language["frontend.global.required"] . '</div><div class="left-float"><input type="text" name="source_select_label" class="wd350 backend-text-input"' . $ht_left . ' /></div><div class="right-float right-padding10 lh20 save-select-response"></div></div>';
                $html .= '<div class="row wdmax"><div class="left-float wd140 lh20 left-padding5"><span class="source_select_descr_req">' . $language["backend.menu.members.entry2.sub2.sel.opt"] . '</span>' . $language["frontend.global.required"] . '</div><div class="left-float"><textarea cols="1" rows="1" class="backend-textarea-input wd350" name="source_select_descr">' . $ht_right . '</textarea></div>';
                $html .= '<div class="right-float right-padding10 lh20"><a href="javascript:;" ' . $save_id . 'class="savecustomselect' . $edit_class . '">' . $save_txt . '</a> - <a href="javascript:;" id="' . $rem_class . '-select-field-' . $db_id . '" class="' . $rem_class . '-custom-field">' . $cancel_txt . '</a></div></div>';
                break;
        }
        $html .= $left != '' ? '<div class="custom-save-response left-float wdmax"></div>' : null;
        $html .= $left != '' ? '<div class="left-float no-display"><input type="hidden" name="custom_save_id" class="custom_save_id" value="' . $thedb_id . '" /></div>' : null;
        $html .= $left != '' ? '</form>' : null;
        $html .= '</div>';
        return $html;
    }
    /* js error class */
    public function setJSerr($div_id, $input1 = '', $input2 = '')
    {
        $js = '$(document).ready(function(){';
        $js .= 'var val1 = "' . $_POST[$input1] . '";';
        $js .= $input2 != '' ? 'var val2 = "' . $_POST[$input2] . '";' : null;
        $js .= 'if( val1 == "" ) { $("' . $div_id . ' span.' . $input1 . '_req").addClass("err-red"); }';
        $js .= 'else if( val1 != "" ) { $("' . $div_id . ' span.' . $input1 . '_req").removeClass("err-red"); }';
        $js .= $input2 != '' ? 'if( val2  == "" ) { $("' . $div_id . ' span.' . $input2 . '_req").addClass("err-red"); }' : null;
        $js .= $input2 != '' ? 'else if( val2  != "" ) { $("' . $div_id . ' span.' . $input2 . '_req").removeClass("err-red"); }' : null;
        $js .= '});';

        echo VGenerate::declareJS($js);
    }
    /* saving new custom fields */
    public function saveCustomFields($type)
    {
        global $class_database, $class_filter, $db;

        $error      = 0;
        $db_id      = intval($_POST["custom_db_id"]);
        $db_fields  = array();
        $db_array   = array("type" => substr($type, 10), "left" => "", "right" => "");
        $post_array = array("source_text_label" => "",
            "source_text_descr"                     => "",
            "source_input_label"                    => "",
            "source_input_descr"                    => "",
            "source_link_label"                     => "",
            "source_link_descr"                     => "",
            "allow_img_linking"                     => "",
            "source_select_label"                   => "",
            "source_select_descr"                   => "",
        );
        $db_data   = $class_database->singleFieldValue('db_usertypes', 'db_custom_fields', 'db_id', $db_id);
        $db_fields = unserialize($db_data);
        $db_count  = $db_data != '' ? count($db_fields) : 0;

        foreach ($post_array as $key => $val) {
            $post_array[$key] = $class_filter->clr_str(trim($_POST[$key]));
        }

        switch ($type) {
            case "savecustomtext":
                $input1 = 'source_text_label';
                $input2 = 'source_text_descr';
                if ($post_array[$input1] == '' or $post_array[$input2] == '') {
                    echo self::setJSerr('.custom-wrap-text', $input1, $input2);
                    $error = 1;}

                $db_array["left"]  = $post_array[$input1];
                $db_array["right"] = $post_array[$input2];
                break;
            case "savecustominput":
            case "savecustomlink":
                $input1   = $type == 'savecustominput' ? 'source_input_label' : 'source_link_label';
                $input2   = '';
                $input3   = 'allow_img_linking';
                $js_class = $type == 'savecustominput' ? '.custom-wrap-input' : '.custom-wrap-link';
                if ($post_array[$input1] == '') {
                    echo self::setJSerr($js_class, $input1, $input2);
                    $error = 1;}

                $db_array["left"]  = $post_array[$input1];
                $db_array["right"] = $type == 'savecustominput' ? '' : (($type == 'savecustomlink' and $post_array[$input3] == '1') ? 'img_link' : '');
                break;
            case "savecustomselect":
                $input1 = 'source_select_label';
                $input2 = 'source_select_descr';
                if ($post_array[$input1] == '' or $post_array[$input2] == '') {
                    echo self::setJSerr('.custom-wrap-select', $input1, $input2);
                    $error = 1;}

                $db_array["left"]  = $post_array[$input1];
                $db_array["right"] = str_replace("\n", ",", $post_array[$input2]);
                break;
        }

        $db_fields[($db_count > 0 ? $db_count : 0)] = $db_array;

        if (count($db_fields) > 0 and $error == 0) {
            $_POST["hc_id"] = $db_id;
            $db_update      = $class_database->doUpdate('db_usertypes', 'db_id', array("db_custom_fields" => serialize($db_fields)));

            if ($db->Affected_Rows() > 0) {
                echo VGenerate::declareJS('$(document).ready(function(){$("#source-fields-form' . $db_id . '").get(0).reset(); $("#source-fields-form' . $db_id . ' span.err-red").removeClass("err-red"); $(".source-custom-field' . $db_id . '>div").addClass("no-display"); resizeDelimiter();});');
            }
        }
        echo self::listCustomFields($db_id);
    }
    /* saving/deleting edited fields */
    public function saveEditedFields($type, $delete = 0)
    {
        global $db, $class_database, $class_filter, $language;

        $db_key         = intval($_GET["id"]);
        $arr_key        = intval($_GET["key"]);
        $_POST["hc_id"] = $db_key;

        $db_array = unserialize($class_database->singleFieldValue('db_usertypes', 'db_custom_fields', 'db_id', $db_key));

        switch ($type) {
            case "savecustomtextedit":
                $input1   = 'source_text_label';
                $input2   = 'source_text_descr';
                $resp_div = '.save-text-response';
                if ($_POST[$input1] == '' or $_POST[$input2] == '') {return self::setJSerr('#custom-wrap' . $arr_key, $input1, $input2);}

                $db_array[$arr_key]["left"]  = $class_filter->clr_str($_POST[$input1]);
                $db_array[$arr_key]["right"] = $class_filter->clr_str($_POST[$input2]);
                break;
            case "savecustominputedit":
            case "savecustomlinkedit":
                $input1   = $type == 'savecustominputedit' ? 'source_input_label' : 'source_link_label';
                $input2   = '';
                $input3   = 'allow_img_linking';
                $resp_div = $type == 'savecustominputedit' ? '.save-input-response' : '.save-link-response';

                if ($_POST[$input1] == '') {return self::setJSerr('#custom-wrap' . $arr_key, $input1, '');}

                $db_array[$arr_key]["left"]  = $class_filter->clr_str($_POST[$input1]);
                $db_array[$arr_key]["right"] = $type == 'savecustominputedit' ? '' : (($type == 'savecustomlinkedit' and $_POST[$input3] == '1') ? 'img_link' : '');
                break;
            case "savecustomselectedit":
                $input1   = 'source_select_label';
                $input2   = 'source_select_descr';
                $resp_div = '.save-select-response';
                if ($_POST[$input1] == '' or $_POST[$input2] == '') {return self::setJSerr('#custom-wrap' . $arr_key, $input1, $input2);}

                $db_array[$arr_key]["left"]  = $class_filter->clr_str($_POST[$input1]);
                $db_array[$arr_key]["right"] = str_replace("\n", ",", $class_filter->clr_str($_POST[$input2]));
                break;
        }

        if ($delete == 1) {
            unset($db_array[$arr_key]);

            $db_array = array_merge($db_array);
            $db_clean = self::delCustomField($db_key, $arr_key, $db_array);
        }
        $db_update       = $class_database->doUpdate('db_usertypes', 'db_id', array("db_custom_fields" => serialize($db_array)));
        echo $clr_js_err = VGenerate::declareJS('$("#custom-wrap' . $arr_key . ' span.err-red").removeClass("err-red"); $("#save-fields-form' . $arr_key . '-' . intval($_GET["id"]) . ' ' . $resp_div . '").html("[' . $language["frontend.global.saved"] . ']"); setTimeout(function(){$("#save-fields-form' . $arr_key . '-' . intval($_GET["id"]) . ' ' . $resp_div . '").html("");}, 3000);');
    }
    /* delete custom fields function */
    public function delCustomField_off($db_key, $arr_key, $db_array)
    {
        global $db;

        if ($db_key > 0 and is_array($db_array)) {
            $db_n = array();
            $db_f = $db->execute(sprintf("SELECT `ch_custom_fields`, `db_id` FROM `db_accountuser` WHERE `ch_type`='%s';", $db_key));
            $db_r = $db_f->getrows();

            foreach ($db_r as $db_v) {
                $db_c  = unserialize($db_v[0]);
                $db_id = $db_v[1];

                unset($db_c[$db_array[$arr_key]["type"] . $arr_key]);
                $ak = array_keys($db_c);

                foreach ($ak as $key => $val) {
                    $db_c_val        = $db_c[$val];
                    $db_c_key        = $db_array[$key]["type"] . $key;
                    $db_n[$db_c_key] = $db_c_val;
                }
                $db_update = $db->execute(sprintf("UPDATE `db_accountuser` SET `ch_custom_fields`='%s' WHERE `db_id`='%s' LIMIT 1;", serialize($db_n), $db_id));
            }
        }
    }
    /* custom fields js */
    public function customFieldsJS()
    {
        $js .= '$(".remove-custom-field").die();';
        $js .= '$(".remove-custom-field").live("click", function(){'; //removing custom fields
        $js .= 'var rm_key = $(this).attr("id").split("-");';
        $js .= 'var save_form = "#" + $(this).closest("form").attr("id");';
        $js .= 'var save_id = $(save_form+" .custom_save_id").val();';
        $js .= 'var save_url = current_url + menu_section + "?s=' . $_GET["s"] . '&do=removecustomfield&key="+rm_key[3]+"&id="+save_id;';
        $js .= '$(save_form).parent().mask(" "); $.post(save_url, $(save_form).serialize(), function(data){';
        $js .= '$(save_form+">div.custom-save-response").html(data);';
        $js .= '$(save_form).parent().unmask();';
        $js .= '$(save_form).parent().parent().replaceWith("");';
        $js .= 'resizeDelimiter();';
        $js .= '});';
        $js .= '});';

        $js .= '$(".savecustomtextedit, .savecustominputedit, .savecustomlinkedit, .savecustomselectedit").die();';
        $js .= '$(".savecustomtextedit, .savecustominputedit, .savecustomlinkedit, .savecustomselectedit").live("click", function(){'; //click on "save this" - edit existing fields
        $js .= 'var save_form = "#" + $(this).closest("form").attr("id");';
        $js .= 'var save_key = $(this).attr("id").split("-");';
        $js .= 'var save_id = $(save_form+" .custom_save_id").val();';
        $js .= 'var save_url = current_url + menu_section + "?s=' . $_GET["s"] . '&do="+$(this).attr("class")+"&key="+save_key[3]+"&id="+save_id;';
        $js .= '$(save_form).parent().mask(" "); $.post(save_url, $(save_form).serialize(), function(data){';
        $js .= '$(save_form+">div.custom-save-response").html(data);';
        $js .= '$(save_form).parent().unmask();';
        $js .= '});';
        $js .= '});';

        $js .= '$(".savecustomtext, .savecustominput, .savecustomlink, .savecustomselect").die();';
        $js .= '$(".savecustomtext, .savecustominput, .savecustomlink, .savecustomselect").live("click", function(){'; //click on "save this" - save new field
        $js .= 'var save_url = current_url + menu_section + "?s=' . $_GET["s"] . '&do="+$(this).attr("class");';
        $js .= 'var save_form = "#" + $(this).closest("form").attr("id");';
        $js .= '$(save_form).mask(" "); $.post(save_url, $(save_form).serialize(), function(data){';
        $js .= '$(save_form).next().html(data);';
        $js .= '$(save_form).unmask();';
        $js .= '});';
        $js .= '});';

        $js .= '$(".cancel-custom-field").die();';
        $js .= '$(".cancel-custom-field").live("click", function(){'; //click on "cancel this"
        $js .= 'var cn_id = $(this).attr("id").split("-");';
        $js .= '$(this).closest("form").get(0).reset();';
        $js .= '$(this).parent().parent().parent().addClass("no-display"); $(".insert-custom-"+cn_id[2]).val("---");';
        $js .= '});';

        $js .= '$(".change-custom-field").change(function(){'; //changing select list options
        $js .= '$(this).parent().parent().next().children().addClass("no-display");';
        $js .= '});';

        $js .= '$(".insert-custom-field").die();';
        $js .= '$(".insert-custom-field").live("click",function(){'; //click in "insert"
        $js .= 'var ins_id = $(this).attr("id");';
        $js .= 'var ins_type = $(".insert-" + $(this).attr("id")).val();';
        $js .= 'if(ins_type == "text"){ $(".source-"+ins_id+">div").addClass("no-display"); $(".source-"+ins_id+">div.custom-text").removeClass("no-display"); }';
        $js .= 'else if(ins_type == "input"){ $(".source-"+ins_id+">div").addClass("no-display"); $(".source-"+ins_id+">div.custom-input").removeClass("no-display"); }';
        $js .= 'else if(ins_type == "link"){ $(".source-"+ins_id+">div").addClass("no-display"); $(".source-"+ins_id+">div.custom-link").removeClass("no-display"); }';
        $js .= 'else if(ins_type == "select"){ $(".source-"+ins_id+">div").addClass("no-display"); $(".source-"+ins_id+">div.custom-select").removeClass("no-display"); }';
        $js .= 'else if(ins_type == "---"){ $(".source-"+ins_id+">div").addClass("no-display"); }';
        $js .= 'resizeDelimiter();';
        $js .= '});';

        return VGenerate::declareJS($js);
    }
    /* list db custom fields */
    public function listCustomFields($db_id)
    {
        global $class_database, $language;

        $db_data   = $class_database->singleFieldValue('db_usertypes', 'db_custom_fields', 'db_id', $db_id);
        $db_fields = unserialize($db_data);

        if ($db_data != '') {
            foreach ($db_fields as $key => $val) {
                $html .= '<div class="left-float row wdmax">' . self::customFieldsHTML($val["type"], $val["left"], $val["right"], $key, $db_id) . '</div>';
            }
            return $html;
        }
    }
    /* discount template */
    public function discountEntry($d = 'none', $entry_id = '', $db_id = '', $dc_name = '', $dc_desc = '', $dc_amount = '', $dc_date = '', $dc_state = '')
    {
        global $language, $class_filter;

        $error_message  = '';
        $notice_message = '';
        $_init          = VbeEntries::entryInit($d, $db_id, $entry_id);
        $_date          = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_sct           = 'discount_codes';
        $_dsp           = $_init[0];
        $_btn           = $_init[1];

        if ($_POST) {
            $all_array   = VArraySection::getArray($_sct);
            $entry_array = $all_array[0];

            switch ($_GET["do"]) {
                case 'add':
                    $results = VbeEntries::processEntry($_sct, 'add');
                    $html    = $results[2];
                    if ($results[0] != '') {
                        $dc_name   = $entry_array["dc_code"];
                        $dc_desc   = $entry_array["dc_descr"];
                        $dc_amount = $entry_array["dc_amount"];
                    }break;
                case 'update':
                    $results = VbeEntries::processEntry($_sct, 'update', $db_id);
                    $html    = $results[2];
                    break;
            }
        }

        $_btn = $_GET["do"] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET["do"] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . $language["frontend.global.saveupdate"] . '</span>'), 'display: inline-block-off;') : null;

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="add-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language["frontend.global.estate"] . '</label>' . ($dc_state == 1 ? '<span class="conf-green">' . $language["frontend.global.active"] . '</span>' : '<span class="err-red">' . $language["frontend.global.inactive"] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= $_GET["do"] != 'add' ? VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language["frontend.global.edate"] . '</label><span class="conf-green">' . $_date . '</span>')) : null;
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub3.entry.name"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub3_entry_name', 'backend-text-input wd350', $dc_name);
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.desc"] . '</label>', 'left-float', 'backend_menu_members_entry1_sub2_entry_desc', 'backend-textarea-input wd350', $dc_desc);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub3.entry.amount"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub3_entry_amount_' . ((int) $db_id), 'backend-text-input wd50', $dc_amount);
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language["frontend.global.required.items"]);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</form></div>';

        $db_id = (int) $db_id;
        $js .= '$("#slider-backend_menu_members_entry1_sub3_entry_amount_' . $db_id . '").noUiSlider({ start: [ ' . ((int) $dc_amount) . ' ], step: 1, range: { "min": [ 1 ], "max": [ 1000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub3_entry_amount_' . $db_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub3_entry_amount_' . $db_id . '\']"));';
        $js .= $_GET["do"] == 'add' ? 'var clone = $("#cb-response").clone(true); $("#cb-response").detach(); $(clone).insertBefore(".ct-entry-nowrap");' : null;

        $html .= VGenerate::declareJS($js);

        return $html;
    }
    /* subscription types */
    public function subEntry($d = 'none', $entry_id = '', $pk_id = '', $pk_name = '', $pk_descr = '', $pk_priceunit = '', $pk_priceunitname = '', $pk_price = '', $pk_period = '', $pk_active = '')
    {
        global $language, $cfg;

        $error_message  = '';
        $notice_message = '';
        $price_array    = explode(',', $language["supported_currency_names"]);
        $unit_array     = explode(',', $language["supported_currency_codes"]);
        $subs_per       = explode(',', $language["subscription_periods"]);
        $subs_num       = explode(',', $language["subscription_numbers"]);
        $subs_count     = count($subs_per);
        $_init          = VbeEntries::entryInit($d, $pk_id, $entry_id);
        $_sct           = 'subscription_types';
        $_dsp           = $_init[0];
        $_btn           = $_init[1];

        if ($_POST) {
            $all_array   = VArraySection::getArray($_sct);
            $entry_array = $all_array[0];

            switch ($_GET["do"]) {
                case 'add':
                    $results = VbeEntries::processEntry($_sct, 'add');
                    $html    = $results[2];
                    if ($results[0] != '') {
                        $pk_name          = $entry_array["pk_name"];
                        $pk_descr         = $entry_array["pk_descr"];
                        $pk_priceunitname = $entry_array["pk_priceunitname"];
                        $pk_price         = $entry_array["pk_price"];
                        $pk_period        = $entry_array["pk_period"];
                    }break;
                case 'update':
                    $results = VbeEntries::processEntry($_sct, 'update', $pk_id);
                    $html    = $results[2];
                    break;
            }
        }
        $_btn = $_GET["do"] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET["do"] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . $language["frontend.global.saveupdate"] . '</span>'), 'display: inline-block-off;') : null;

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $pk_id . '" style="display: ' . $_dsp . ';"><form id="add-entry-form' . $pk_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($pk_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language["frontend.global.estate"] . '</label>' . ($pk_active == 1 ? '<span class="conf-green">' . $language["frontend.global.active"] . '</span>' : '<span class="err-red">' . $language["frontend.global.inactive"] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.name"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_name', 'backend-text-input wd350', $pk_name);
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.desc"] . '</label>', 'left-float', 'backend_menu_members_entry1_sub2_entry_desc', 'backend-textarea-input wd350', $pk_descr);

        $do = (int) $pk_id;

        foreach ($price_array as $key => $val) {
            $sel_check = ($pk_priceunitname == $val or ($pk_priceunitname == '' and $val == 'USD')) ? 'selected="selected"' : null;
            $option . $do .= '<option id="' . $val . '" value="' . $val . '" ' . $sel_check . '>' . $val . '</option>';
        }
        $pr = '<div class="selector"><select name="pk_priceunit_' . ((int) $pk_id) . '" class="backend-select-input" onchange="$(\'#pk_priceunitname' . $pk_id . '\').val(this.options[this.selectedIndex].id)">' . $option . $do . '</select><input type="hidden" name="pk_priceunitname_' . ((int) $pk_id) . '" id="pk_priceunitname' . $pk_id . '" value="' . ($pk_priceunitname == '' ? 'USD' : $pk_priceunitname) . '" /></div>';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.price"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_price_' . ((int) $pk_id), 'backend-text-input wd50', $pk_price);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language["backend.menu.members.entry1.sub2.entry.priceunit"] . '</label>' . $language["frontend.global.required"] . $pr));
        $html .= VGenerate::declareJS('$(function(){SelectList.init("pk_priceunit_' . ((int) $pk_id) . '");});');

        $do1 = $do + 100;

        foreach ($subs_per as $key1 => $val1) {
            $display = 'none';
            if ($pk_period == $subs_num[$key1]) {$sel_check1 = 'selected="selected"';} else { $sel_check1 = '';}

            $options . $do1 .= '<option value="' . $subs_num[$key1] . '" ' . $sel_check1 . '>' . $val1 . '</option>';
        }
        $sub = '<div class="selector"><select name="backend_menu_members_entry1_sub2_entry_period_' . ((int) $pk_id) . '" class="backend-select-input" onchange="if(this.options[this.selectedIndex].value == \'0\'){ showDiv(\'pkc0' . $pk_id . '\', 1); } else { hideDiv(\'pkc0' . $pk_id . '\', 1); }">' . $options . $do1 . '</select></div>';

        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language["backend.menu.members.entry1.sub2.entry.period"] . '</label>' . $language["frontend.global.required"]) . VGenerate::simpleDivWrap('left-float', '', $sub));
        $html .= '<div class="row"><div class="left-float" id="pkc0' . $pk_id . '" style="display: ' . $display . ';"><label>' . $language["frontend.global.days"] . '</label>' . VGenerate::basicInput('text', 'frontend_global_days_' . ((int) $pk_id), 'backend-text-input wd50', $pk_period) . '</div></div>';
        $html .= '</div>'; //end vs-mask
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language["frontend.global.required.items"]);
        $html .= VGenerate::declareJS('$(function(){SelectList.init("backend_menu_members_entry1_sub2_entry_period_' . ((int) $pk_id) . '");});');
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $pk_id . '" />';
        $html .= '</form></div>';

        $pk_id = (int) $pk_id;
        $js    = '$("#slider-backend_menu_members_entry1_sub2_entry_price_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_price) . ' ], step: 1, range: { "min": [ 1 ], "max": [ 1000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_price_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_price_' . $pk_id . '\']"));';
        $js .= '$("#slider-frontend_global_days_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_period) . ' ], step: 1, range: { "min": [ 1 ], "max": [ 365 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-frontend_global_days_' . $pk_id . '").Link("lower").to($("input[name=\'frontend_global_days_' . $pk_id . '\']"));';
        $js .= $_GET["do"] == 'add' ? 'var clone = $("#cb-response").clone(true); $("#cb-response").detach(); $(clone).insertBefore(".ct-entry-nowrap");' : null;

        $html .= VGenerate::declareJS($js);

        return $html;
    }
    /* pack/membership template */
    public function packEntry($d = 'none', $entry_id = '', $pk_id = '', $pk_name = '', $pk_descr = '', $pk_space = '', $pk_bw = '', $pk_priceunit = '', $pk_priceunitname = '', $pk_price = '', $pk_alimit = '', $pk_ilimit = '', $pk_vlimit = '', $pk_slimit = '', $pk_dlimit = '', $pk_llimit = '', $pk_blimit = '', $pk_period = '', $pk_active = '')
    {
        global $language, $cfg;

        $error_message  = '';
        $notice_message = '';
        $price_array    = explode(',', $language["supported_currency_names"]);
        $unit_array     = explode(',', $language["supported_currency_codes"]);
        $subs_per       = explode(',', $language["subscription_periods"]);
        $subs_num       = explode(',', $language["subscription_numbers"]);
        $subs_count     = count($subs_per);
        $_init          = VbeEntries::entryInit($d, $pk_id, $entry_id);
        $_sct           = 'membership_types';
        $_dsp           = $_init[0];
        $_btn           = $_init[1];

        if ($_POST) {
            $all_array   = VArraySection::getArray($_sct);
            $entry_array = $all_array[0];

            switch ($_GET["do"]) {
                case 'add':
                    $results = VbeEntries::processEntry($_sct, 'add');
                    $html    = $results[2];
                    if ($results[0] != '') {
                        $pk_name          = $entry_array["pk_name"];
                        $pk_descr         = $entry_array["pk_descr"];
                        $pk_space         = $entry_array["pk_space"];
                        $pk_bw            = $entry_array["pk_bw"];
                        $pk_priceunitname = $entry_array["pk_priceunitname"];
                        $pk_price         = $entry_array["pk_price"];
                        $pk_alimit        = $entry_array["pk_alimit"];
                        $pk_ilimit        = $entry_array["pk_ilimit"];
                        $pk_vlimit        = $entry_array["pk_vlimit"];
                        $pk_slimit        = $entry_array["pk_slimit"];
                        $pk_dlimit        = $entry_array["pk_dlimit"];
                        $pk_llimit        = $entry_array["pk_llimit"];
                        $pk_blimit        = $entry_array["pk_blimit"];
                        $pk_period        = $entry_array["pk_period"];
                    }break;
                case 'update':
                    $results = VbeEntries::processEntry($_sct, 'update', $pk_id);
                    $html    = $results[2];
                    break;
            }
        }
        $_btn = $_GET["do"] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET["do"] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . $language["frontend.global.saveupdate"] . '</span>'), 'display: inline-block-off;') : null;

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $pk_id . '" style="display: ' . $_dsp . ';"><form id="add-entry-form' . $pk_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($pk_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language["frontend.global.estate"] . '</label>' . ($pk_active == 1 ? '<span class="conf-green">' . $language["frontend.global.active"] . '</span>' : '<span class="err-red">' . $language["frontend.global.inactive"] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.name"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_name', 'backend-text-input wd350', $pk_name);
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.desc"] . '</label>', 'left-float', 'backend_menu_members_entry1_sub2_entry_desc', 'backend-textarea-input wd350', $pk_descr);

        $do = (int) $pk_id;

        foreach ($price_array as $key => $val) {
            $sel_check = ($pk_priceunitname == $val) ? 'selected="selected"' : null;
            $option . $do .= '<option id="' . $val . '" value="' . $val . '" ' . $sel_check . '>' . $val . '</option>';
        }
        $pr = '<div class="selector"><select name="pk_priceunit_' . ((int) $pk_id) . '" class="backend-select-input" onchange="$(\'#pk_priceunitname' . $pk_id . '\').val(this.options[this.selectedIndex].id)">' . $option . $do . '</select><input type="hidden" name="pk_priceunitname_' . ((int) $pk_id) . '" id="pk_priceunitname' . $pk_id . '" value="' . $pk_priceunitname . '" /></div>';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.price"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_price_' . ((int) $pk_id), 'backend-text-input wd50', $pk_price);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language["backend.menu.members.entry1.sub2.entry.priceunit"] . '</label>' . $language["frontend.global.required"] . $pr));
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.space"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_space_' . ((int) $pk_id), 'backend-text-input wd50', $pk_space);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.bw"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_bw_' . ((int) $pk_id), 'backend-text-input wd50', $pk_bw);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.vlimit"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_vlimit_' . ((int) $pk_id), 'backend-text-input wd50', $pk_vlimit);
        $html .= $cfg["live_module"] == 1 ? VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.slimit"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_slimit_' . ((int) $pk_id), 'backend-text-input wd50', $pk_slimit) : null;
        $html .= $cfg["live_module"] == 1 ? VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.llimit"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_llimit_' . ((int) $pk_id), 'backend-text-input wd50', $pk_llimit) : null;
        $html .= $cfg["image_module"] == 1 ? VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.ilimit"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_ilimit_' . ((int) $pk_id), 'backend-text-input wd50', $pk_ilimit) : null;
        $html .= $cfg["audio_module"] == 1 ? VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.alimit"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_alimit_' . ((int) $pk_id), 'backend-text-input wd50', $pk_alimit) : null;
        $html .= $cfg["document_module"] == 1 ? VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.dlimit"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_dlimit_' . ((int) $pk_id), 'backend-text-input wd50', $pk_dlimit) : null;
        $html .= $cfg["blog_module"] == 1 ? VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.members.entry1.sub2.entry.blimit"] . '</label>' . $language["frontend.global.required"], 'left-float', 'backend_menu_members_entry1_sub2_entry_blimit_' . ((int) $pk_id), 'backend-text-input wd50', $pk_blimit) : null;

        $html .= VGenerate::declareJS('$(function(){SelectList.init("pk_priceunit_' . ((int) $pk_id) . '");});');

        $do1 = $do + 100;

        foreach ($subs_per as $key1 => $val1) {
            $display = 'none';
            if ($pk_period == $subs_num[$key1]) {$sel_check1 = 'selected="selected"';} else { $sel_check1 = '';}
            if ($key1 == ($subs_count - 1) and !in_array($pk_period, $subs_num)) {
                $sel_check1 = 'selected="selected"';
                $display    = 'block';}

            $options . $do1 .= '<option value="' . $subs_num[$key1] . '" ' . $sel_check1 . '>' . $val1 . '</option>';
        }
        $sub = '<div class="selector"><select name="backend_menu_members_entry1_sub2_entry_period_' . ((int) $pk_id) . '" class="backend-select-input" onchange="if(this.options[this.selectedIndex].value == \'0\'){ showDiv(\'pkc0' . $pk_id . '\', 1); } else { hideDiv(\'pkc0' . $pk_id . '\', 1); }">' . $options . $do1 . '</select></div>';

        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language["backend.menu.members.entry1.sub2.entry.period"] . '</label>' . $language["frontend.global.required"]) . VGenerate::simpleDivWrap('left-float', '', $sub));
        $html .= '<div class="row"><div class="left-float" id="pkc0' . $pk_id . '" style="display: ' . $display . ';"><label>' . $language["frontend.global.days"] . '</label>' . VGenerate::basicInput('text', 'frontend_global_days_' . ((int) $pk_id), 'backend-text-input wd50', $pk_period) . '</div></div>';
        $html .= '</div>'; //end vs-mask
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language["frontend.global.required.items"]);
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language["backend.menu.members.entry1.sub2.entry.unlim"]);

        $html .= VGenerate::declareJS('$(function(){SelectList.init("backend_menu_members_entry1_sub2_entry_period_' . ((int) $pk_id) . '");});');

        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $pk_id . '" />';
        $html .= '</form></div>';

        $pk_id = (int) $pk_id;
        $js    = '$("#slider-backend_menu_members_entry1_sub2_entry_price_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_price) . ' ], step: 1, range: { "min": [ 0 ], "max": [ 1000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_price_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_price_' . $pk_id . '\']"));';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_space_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_space) . ' ], step: 5, range: { "min": [ 0 ], "max": [ 10000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_space_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_space_' . $pk_id . '\']"));';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_bw_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_bw) . ' ], step: 5, range: { "min": [ 0 ], "max": [ 10000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_bw_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_bw_' . $pk_id . '\']"));';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_vlimit_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_vlimit) . ' ], step: 1, range: { "min": [ 0 ], "max": [ 10000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_vlimit_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_vlimit_' . $pk_id . '\']"));';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_slimit_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_slimit) . ' ], step: 1, range: { "min": [ 0 ], "max": [ 10000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_slimit_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_slimit_' . $pk_id . '\']"));';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_ilimit_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_ilimit) . ' ], step: 1, range: { "min": [ 0 ], "max": [ 10000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_ilimit_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_ilimit_' . $pk_id . '\']"));';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_alimit_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_alimit) . ' ], step: 1, range: { "min": [ 0 ], "max": [ 10000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_alimit_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_alimit_' . $pk_id . '\']"));';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_dlimit_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_dlimit) . ' ], step: 1, range: { "min": [ 0 ], "max": [ 10000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_dlimit_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_dlimit_' . $pk_id . '\']"));';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_llimit_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_llimit) . ' ], step: 1, range: { "min": [ 0 ], "max": [ 10000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_llimit_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_llimit_' . $pk_id . '\']"));';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_blimit_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_blimit) . ' ], step: 1, range: { "min": [ 0 ], "max": [ 10000 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-backend_menu_members_entry1_sub2_entry_blimit_' . $pk_id . '").Link("lower").to($("input[name=\'backend_menu_members_entry1_sub2_entry_blimit_' . $pk_id . '\']"));';
        $js .= '$("#slider-frontend_global_days_' . $pk_id . '").noUiSlider({ start: [ ' . ((int) $pk_period) . ' ], step: 1, range: { "min": [ 1 ], "max": [ 365 ] }, format:wNumb({ decimals: 0 }) });';
        $js .= '$("#slider-frontend_global_days_' . $pk_id . '").Link("lower").to($("input[name=\'frontend_global_days_' . $pk_id . '\']"));';
        $js .= $_GET["do"] == 'add' ? 'var clone = $("#cb-response").clone(true); $("#cb-response").detach(); $(clone).insertBefore(".ct-entry-nowrap");' : null;

        $html .= VGenerate::declareJS($js);

        return $html;
    }
    /* custom affiliate payout */
    public static function affiliatePayout()
    {
        return;

        global $db, $cfg, $language, $class_filter, $class_database, $class_language, $smarty;
        include_once $class_language->setLanguageFile('backend', 'language.members.entries');

        $_do      = $class_filter->clr_str($_GET["do"]);
        $_u       = $class_filter->clr_str($_GET["u"]);
        $u        = $db->execute(sprintf("SELECT `usr_user`, `affiliate_pay_custom`, `affiliate_custom`, `affiliate_maps_key` FROM `db_accountuser` WHERE `usr_key`='%s' LIMIT 1;", $_u));
        $usr_user = $u->fields["usr_user"];
        $ac       = unserialize($u->fields["affiliate_custom"]);
        $cp       = $u->fields["affiliate_pay_custom"];
        $maps     = $u->fields["affiliate_maps_key"];

        if ($ac["share"] != '') {
            $cfg["affiliate_payout_share"] = $ac["share"];
        }

        if ($ac["figure"] != '') {
            $cfg["affiliate_payout_figure"] = $ac["figure"];
        }

        if ($ac["currency"] != '') {
            $cfg["affiliate_payout_currency"] = $ac["currency"];
        }

        if ($ac["units"] != '') {
            $cfg["affiliate_payout_units"] = $ac["units"];
        }

        if ($ac["maps"] != '') {
            $cfg["affiliate_geo_maps"] = $ac["maps"];
        }

        $_currency = explode(',', $language["supported_currency_names"]);
        $sel_opts  = null;
        foreach ($_currency as $v) {
            $sel_opts .= '<option value="' . $v . '"' . ($v == $cfg["affiliate_payout_currency"] ? ' selected="selected"' : null) . '>' . $v . '</option>';
        }

        $html = '<style>#usr-affiliate-form p {padding: 0 0 10px;}</style>';
        $html .= '<div id="lb-wrapper">';
        $html .= '<div class="entry-list vs-column full">';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle('<span class="">' . $language["account.entry.payout.settings"] . '</span> <span class="bold">' . ($usr_user == '' ? '(' . $language["frontend.global.selected"] . ')' : $usr_user) . '</span>', 'icon-coin');
        $html .= VGenerate::simpleDivWrap('row', 'usr-update', '');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel-off active">';
        $html .= '<form id="usr-affiliate-form" method="post" action="" class="entry-form-class">';
        $html .= '<p><span>' . $usr_user . ' is now receiving <span id="s-pc-off">' . round((($cfg["affiliate_payout_share"] * $cfg["affiliate_payout_figure"]) / 100), 2) . '</span> <span id="s-cr">' . $cfg["affiliate_payout_currency"] . '</span> for every <span id="s-pv">' . $cfg["affiliate_payout_units"] . '</span> unique video views</span></p>';
        $html .= VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="affiliate_geo_maps" class=""' . ($cfg["affiliate_geo_maps"] == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry1.sub1.maps"] . $usr_user . '</label>');
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.af.maps"] . '</label>', 'left-float', 'backend_menu_af_p_maps_api_key', 'backend-text-input wd350', $maps);
        $html .= '<br>';
        $html .= VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="custom_payout" class=""' . ($cp == 1 ? ' checked="checked"' : null) . '><label>' . $language["backend.menu.members.entry1.sub1.scp"] . $usr_user . '</label>');
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.af.p.share"] . '</label>', 'left-float', 'backend_menu_af_p_share', 'backend-text-input wd350', $cfg["affiliate_payout_share"]);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.af.p.units"] . '</label>', 'left-float', 'backend_menu_af_p_units', 'backend-text-input wd350', $cfg["affiliate_payout_units"]);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.menu.af.p.figure"] . '</label>', 'left-float', 'backend_menu_af_p_figure', 'backend-text-input wd350', $cfg["affiliate_payout_figure"]);
        $html .= '<div class="selector"><label>' . $language["backend.menu.af.p.currency"] . '</label><select name="backend_menu_af_p_currency" class="backend-select-input">' . $sel_opts . '</select></div>';
        $html .= '<div class="icheck-box-off" id="act-affiliate">';
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="hidden" name="cb_in2" id="cb-in2" value="" />');
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="hidden" name="cb_in3" id="cb-in3" value="" />');
        $html .= '<div class="clearfix">&nbsp;</div>';
        $html .= VGenerate::simpleDivWrap('row right-float top-padding15', '', self::userAction_close($_do));
        $html .= '</div>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<script type="text/javascript">SelectList.init("backend_menu_af_p_currency");</script>';
        $html .= '<script type="text/javascript">' . $smarty->fetch($cfg["javascript_dir_be"] . '/settings-accordion.js') . '</script>';
        $html .= '  <script type="text/javascript">
                $(document).ready(function () {
                $("input[name=backend_menu_af_p_figure]").keyup(function(){
                    t = $(this);
                    v = $("input[name=backend_menu_af_p_share]").val();
                    v = (v < 1 ? 1 : (v > 100 ? 100 : v));
                    $("#s-pf").text(v);
                    tp = parseFloat($("input[name=\'backend_menu_af_p_figure\']").val());
                    r = Math.round(v * tp) / 100;
                    $("#s-pc-off").text(r);
                });
                $("input[name=backend_menu_af_p_units]").keyup(function(){
                        $("#s-pv").text($(this).val());
                });
                $("input[name=backend_menu_af_p_share]").keyup(function(){
                    t = $(this);
                    v = t.val();
                    v = (v < 1 ? 1 : (v > 100 ? 100 : v));
                    $("#s-pc").text(v);
                    tp = parseFloat($("input[name=\'backend_menu_af_p_figure\']").val());
                    r = Math.round(v * tp) / 100;
                    $("#s-pc-off").text(r);
                    t.val(v);
                });
                $("select[name=backend_menu_af_p_currency]").change(function(){
                        $("#s-cr").text($(this).val());
                });
                $("#usr-affiliate-form .icheck-box input").each(function () {var self = $(this); self.iCheck({checkboxClass: "icheckbox_square-blue",radioClass: "iradio_square-blue",increaseArea: "20%"});});
        });
            </script>';

        return $html;
    }
    /* save custom payout */
    public static function affiliatePayoutSave()
    {
        global $db, $cfg, $class_filter, $language;

        $_u = $class_filter->clr_str($_GET["u"]);

        if ($_POST and $cfg["affiliate_module"] == 1) {
            $backend_menu_af_maps       = (int) $_POST["affiliate_geo_maps"];
            $backend_menu_af_maps_key   = $class_filter->clr_str($_POST["backend_menu_af_p_maps_api_key"]);
            $backend_menu_af_cp         = (int) $_POST["custom_payout"];
            $backend_menu_af_p_share    = $class_filter->clr_str($_POST["backend_menu_af_p_share"]);
            $backend_menu_af_p_figure   = $class_filter->clr_str($_POST["backend_menu_af_p_figure"]);
            $backend_menu_af_p_currency = $class_filter->clr_str($_POST["backend_menu_af_p_currency"]);
            $backend_menu_af_p_units    = $class_filter->clr_str($_POST["backend_menu_af_p_units"]);
            if ($backend_menu_af_p_share == '') {
                $backend_menu_af_p_share = $cfg["affiliate_payout_share"];
            }

            if ($backend_menu_af_p_figure == '') {
                $backend_menu_af_p_figure = $cfg["affiliate_payout_figure"];
            }

            if ($backend_menu_af_p_currency == '') {
                $backend_menu_af_p_currency = $cfg["affiliate_payout_currency"];
            }

            if ($backend_menu_af_p_units == '') {
                $backend_menu_af_p_units = $cfg["affiliate_payout_units"];
            }

            $_a = array("share" => $backend_menu_af_p_share, "figure" => $backend_menu_af_p_figure, "currency" => $backend_menu_af_p_currency, "units" => $backend_menu_af_p_units, "maps" => $backend_menu_af_maps);

            $sql = sprintf("UPDATE `db_accountuser` SET `affiliate_pay_custom`='%s', `affiliate_custom`='%s', `affiliate_maps_key`='%s' WHERE `usr_key`='%s' AND `usr_affiliate`='1' LIMIT 1;", $backend_menu_af_cp, serialize($_a), $backend_menu_af_maps_key, $_u);

            $rs = $db->execute($sql);

            if ($db->Affected_Rows() > 0) {
                return VGenerate::noticeTpl('', '', $language["notif.success.request"]);
            }
        }
    }
    /* affiliate confirmation emails */
    public static function affiliateConfirmationEmail()
    {
        global $cfg, $language, $smarty, $class_filter, $db, $class_database, $class_language;

        include_once $class_language->setLanguageFile('frontend', 'language.email.notif');

        $_do = $class_filter->clr_str($_GET["do"]);

        switch ($_do) {
            case "usr-make-affiliate":
                $s1 = $language["affiliate.subject.approved"];
                $s2 = 'affiliate_confirm';
                break;
            case "usr-clear-affiliate":
                $s1     = (isset($_GET["n"]) and $_GET["n"] == 'n2') ? $language["affiliate.subject.closed"] : $language["affiliate.subject.denied"];
                $s2     = (isset($_GET["n"]) and $_GET["n"] == 'n2') ? 'affiliate_closed' : 'affiliate_denied';
                $reason = (isset($_GET["n"]) and $_GET["n"] == 'n2') ? $language["account.entry.affiliate.closed"] : $language["account.entry.affiliate.denied"];
                break;
            case "usr-make-partner":
                $s1 = $language["partner.subject.approved"];
                $s2 = 'partner_confirm';
                break;
            case "usr-clear-partner":
                $s1     = (isset($_GET["n"]) and $_GET["n"] == 'n2') ? $language["partner.subject.closed"] : $language["partner.subject.denied"];
                $s2     = (isset($_GET["n"]) and $_GET["n"] == 'n2') ? 'partner_closed' : 'partner_denied';
                $reason = (isset($_GET["n"]) and $_GET["n"] == 'n2') ? $language["account.entry.partner.closed"] : $language["account.entry.partner.denied"];
                break;
        }
        if (isset($_POST["cb_in2"]) and $_POST["cb_in2"] != '') {
            $p  = urldecode($_POST["cb_in2"]);
            $rs = $db->execute(sprintf("SELECT `usr_id`, `usr_email` FROM `db_accountuser` WHERE %s", $p));

            if ($rs->fields["usr_id"]) {
                while (!$rs->EOF) {
                    $user_id   = $rs->fields["usr_id"];
                    $user_data = VUserinfo::getUserInfo($user_id);
                    $mailto    = $rs->fields["usr_email"];

                    $notifier     = new VNotify;
                    $website_logo = $smarty->fetch($cfg["templates_dir"] . '/tpl_frontend/tpl_header/tpl_headerlogo.tpl');
                    $cfg          = $class_database->getConfigurations('backend_email,backend_username,affiliate_payout_currency');

                    $notifier->msg_subj = $s1;
                    $_replace           = array(
                        '##TITLE##'  => $s1,
                        '##LOGO##'   => $website_logo,
                        '##H2##'     => $language["recovery.forgot.password.h2"] . $user_data["uname"] . ',',
                        '##REASON##' => $reason,
                        '##YEAR##'   => date('Y'),
                    );
                    $notifier->dst_mail = $mailto;
                    $notifier->dst_name = (($user_data["fname"] != '' or $user_data["lname"] != '') ? $user_data["fname"] . ' ' . $user_data["lname"] : $user_data["uname"]);
                    $notifier->Mail('frontend', $_do, $_replace);
                    $_output[] = $cfg["backend_username"] . ' -> ' . $s2 . ' -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                    $log_mail  = '.mailer.log';
                    VServer::logToFile($log_mail, implode("\n", $_output));

                    $rs->MoveNext();
                }
            }
        }
    }
    /* affiliate confirmation lightbox */
    public static function affiliateConfirmation()
    {
        global $cfg, $language, $smarty, $class_filter;

        $_do = $class_filter->clr_str($_GET["do"]);

        switch ($_do) {
            case "make-affiliate":
                $l1 = $language["backend.menu.members.entry2.sub2.aff.text1"];
                break;
            case "clear-affiliate":
                $l1 = $language["backend.menu.members.entry2.sub2.aff.text2"];
                break;
            case "make-partner":
                $l1 = $language["backend.menu.members.entry2.sub2.prt.text1"];
                break;
            case "clear-partner":
                $l1 = $language["backend.menu.members.entry2.sub2.prt.text2"];
                break;
        }

        $html = '<div id="lb-wrapper">';
        $html .= '<div class="entry-list vs-column full">';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle('<span class="">' . $language["backend.menu.members.entry2.sub2.aff.mail"] . '</span> <span class="bold">' . ($usr_user == '' ? '(' . $language["frontend.global.selected"] . ')' : $usr_user) . '</span>', 'icon-coin');
        $html .= VGenerate::simpleDivWrap('row', 'usr-update', '');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active">';
        $html .= '<form id="usr-affiliate-form" method="post" action="" class="entry-form-class">';
        $html .= '<p>' . $l1 . '</p>';
        $html .= '<div class="icheck-box" id="act-affiliate">';
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="hidden" name="cb_in2" id="cb-in2" value="" />');
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="hidden" name="cb_in3" id="cb-in3" value="" />');
        $html .= '<div class="clearfix">&nbsp;</div>';
        $html .= VGenerate::simpleDivWrap('row right-float top-padding15', '', self::userAction_close($_do));
        $html .= '</div>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<script type="text/javascript">' . $smarty->fetch($cfg["javascript_dir_be"] . '/settings-accordion.js') . '</script>';

        return $html;
    }
}
