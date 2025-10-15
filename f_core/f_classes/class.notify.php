<?php
/*******************************************************************************************************************
| Software Name        : ViewShark
| Software Description : High End Live Streaming, Video, Photo, Music, Document & Blog Sharing Script
| Software Author      : (c) ViewShark
| Website              : https://www.viewshark.com
| E-mail               : support@viewshark.com || viewshark@gmail.com support@viewshark.com viewshark@gmail.com
|*******************************************************************************************************************
|
|*******************************************************************************************************************
| This source file is subject to the ViewShark End-User License Agreement, available online at:
| https://www.viewshark.com/support/license/
| By using this software, you acknowledge having read this Agreement and agree to be bound thereby.
|*******************************************************************************************************************
| Copyright (c) 2013-2023 viewshark.com. All rights reserved.
|*******************************************************************************************************************/

defined('_ISVALID') or header('Location: /error');

use PHPMailer\PHPMailer\PHPMailer;

class VNotify
{
    public function __construct()
    {
        global $class_database, $language;
        $cfg = $class_database->getConfigurations('server_path_php,backend_email,backend_email_fromname,website_email,website_email_fromname,noreply_email,noreply_email_fromname,mail_type,mail_pop3_host,mail_pop3_port,mail_pop3_timeout,mail_pop3_username,mail_pop3_password,mail_smtp_host,mail_smtp_auth,mail_smtp_port,mail_smtp_username,mail_smtp_password,mail_debug,mail_smtp_prefix');

        $this->mailer    = $cfg['mail_type'];
        $this->noreply   = $cfg['noreply_email'];
        $this->noreply_n = $cfg['noreply_email_fromname'];
        $this->src_mail  = $cfg['website_email'];
        $this->src_name  = $cfg['website_email_fromname'];
        $this->smtp_auth = $cfg['mail_smtp_auth'];
        $this->smtp_sec  = $cfg['mail_smtp_prefix'];
        $this->dst_mail  = '';
        $this->dst_name  = '';
        $this->msg_subj  = '';
        $this->msg_body  = '';
        $this->msg_alt   = $language['notif.mail.alt.body'];
    }
    public function queInit($type, $clear_arr, $db_id = '', $na = '')
    {
        global $cfg, $class_database, $class_filter, $language;

        $cfg[]     = $class_database->getConfigurations('server_path_php,noreply_email,backend_username');
        $mail_type = $type;
        $mail_usr  = intval($_SESSION['USER_ID']);
        $mail_key  = strtoupper(VUserinfo::generateRandomString(10));
        $mail_to   = serialize($clear_arr);
        $mail_from = $na == '' ? VUserinfo::getUserEmail() : VUserinfo::getUserEmail(VUserinfo::getUserID($na));
        $mail_date = date("Y-m-d H:i:s");
        $php_cgi   = (strpos(php_sapi_name(), 'cgi')) ? 'env -i ' : null;

        switch ($type) {
            case "disk_usage":
                $mail_from  = $cfg['noreply_email'];
                $mail_extra = serialize(array("data" => $db_id));
                break;
            case "welcome":
            case "account_email_verification":
                $mail_from  = $cfg['noreply_email'];
                $mail_extra = serialize(array("sender" => $_SESSION['signup_username'], "usr_id" => ($db_id != '' ? $db_id : $_SESSION['USER_ID'])));
                break;
            case "contact":
                $mail_from  = $cfg['noreply_email'];
                $mail_extra = serialize(array("name" => $class_filter->clr_str($_POST['ft_name']), "email" => $class_filter->clr_str($_POST['ft_email']), "message" => $class_filter->clr_str($_POST['ft_msg']), "ip" => $class_filter->clr_str($_SERVER[REM_ADDR])));
                break;
            case "account_email_verification_confirm":
                $mail_from  = $cfg['noreply_email'];
                $mail_extra = serialize(array("sender" => $_SESSION['USER_NAME']));
                break;
            case "email_digest":
                $mail_from  = $cfg['noreply_email'];
                $mail_extra = serialize(array("sender" => $db_id));
                break;
            case "backend_notification_signup":
                $mail_from  = $cfg['noreply_email'];
                $mail_extra = serialize(array("sender" => $_SESSION['signup_username'], "email" => ($db_id != '' ? $db_id : $class_filter->clr_str($_POST['frontend_signup_emailadd'])), "ip" => $class_filter->clr_str($_SERVER[REM_ADDR]), "admin_user" => $cfg['backend_username']));
                break;
            case "pl_share":
                $mail_extra = serialize(array("pl_type" => $class_filter->clr_str($_POST['h_pl_type']), "pl_msg" => $class_filter->clr_str($_POST['share_pl_msg']), "pl_id" => $class_filter->clr_str($_POST['h_pl_pid']), "username" => $_SESSION['USER_NAME']));
                break;
            case "mobile_feedback":
                $mail_extra = serialize(array("sender" => $class_filter->clr_str($_POST['name']), "email" => $class_filter->clr_str($_POST['email']), "message" => $class_filter->clr_str($_POST['message']), "ip" => $class_filter->clr_str($_SERVER[REM_ADDR])));
                break;
            case "private_message":
            case "private_email":
                $mail_extra = serialize(array("sender" => ($_GET['do'] == 'usr-email' ? $cfg['backend_username'] : $_SESSION['USER_NAME']), "db_id" => $db_id, "es" => $class_filter->clr_str($_POST['email_subject']), "eb" => $class_filter->clr_str($_POST['email_body'])));
                break;
            case "invite_contact":
            case "invite_user":
                $mail_extra = serialize(array("sender" => $_SESSION['USER_NAME'], "usr_id" => $_SESSION['USER_ID'], "ct_id" => $db_id, "subject" => $language['msg.details.invite.subj']));
                break;
            case "subscribe":
            case "follow":
                $mail_extra = serialize(array("sender" => (isset($_SESSION['USER_NAME']) ? $_SESSION['USER_NAME'] : $db_id)));
                break;
            case "file_share":
                $ftype      = $class_filter->clr_str($_POST['uf_type']);
                $fkey       = $class_filter->clr_str($_POST['uf_vid']);
                $mail_extra = serialize(array("sender" => $_SESSION['USER_NAME'], "type" => $language["frontend.global." . $ftype[0]], "key" => $fkey, "title" => $class_database->singleFieldValue('db_' . $ftype . 'files', 'file_title', 'file_key', $fkey), "note" => $class_filter->clr_str($_POST['file_share_mailnote'])));
                break;
            case "file_flagging":
                $ftype      = $class_filter->clr_str($_POST['uf_type']);
                $mail_extra = serialize(array("sender" => $_SESSION['USER_NAME'], "admin" => $cfg['backend_username'], "type" => $language["frontend.global." . $ftype[0]], "key" => $class_filter->clr_str($_POST['uf_vid']), "reason" => $language["view.files.reason." . $db_id]));
                break;
            case "channel_comment":
            case "channel_comment_reply":
                $mail_extra = serialize(array("sender" => $_SESSION['USER_NAME'], "db_id" => $db_id));
                break;
            case "file_comment":
            case "file_comment_reply":
                $mail_extra = serialize(array("type" => $class_filter->clr_str($_POST['comm_type']), "key" => $class_filter->clr_str($_GET[$_POST['comm_type'][0]]), "sender" => $_SESSION['USER_NAME'], "db_id" => $db_id));
                break;
            case "file_response":
                $mail_extra = serialize(array("tbl" => $class_filter->clr_str($_POST['f_type']), "type" => $language["frontend.global." . $_POST['f_type'][0]], "r_key" => $class_filter->clr_str($_POST['response_key']), "key" => $class_filter->clr_str($_GET[$_POST['f_type'][0]]), "sender" => $_SESSION['USER_NAME'], "db_id" => $db_id));
                break;
            case "file_response_upload":
                $mail_extra = serialize(array("tbl" => $class_filter->clr_str(($_GET['t'] == 'document' ? 'doc' : $_GET['t'])), "type" => $language["frontend.global." . $_GET['t'][0]], "r_key" => $class_filter->clr_str($db_id), "key" => $class_filter->clr_str($_GET['r']), "sender" => $_SESSION['USER_NAME'], "db_id" => $db_id));
                break;
            case "new_upload":
            case "new_upload_be":
                $mail_extra = serialize(array("sender" => ($na == '' ? $_SESSION['USER_NAME'] : $na), "db_id" => $db_id, "type" => $language["frontend.global." . $db_id[0]]));
                break;
            case "password_recovery":
                $mail_from  = $cfg['noreply_email'];
                $user_name  = $db_id == 'frontend' ? $class_filter->clr_str($_POST['rec_username']) : $cfg['backend_username'];
                $mail_extra = serialize(array("user" => $user_name));
                $mail_type .= $db_id == 'backend' ? '_be' : null;
                break;
            case "username_recovery":
                $mail_from  = $cfg['noreply_email'];
                $user_mail  = $db_id == 'frontend' ? $class_filter->clr_str($_POST['rec_email']) : $cfg['backend_email'];
                $mail_extra = serialize(array("mail" => $user_mail));
                $mail_type .= $db_id == 'backend' ? '_be' : null;
                break;
            case "change_email":
                $new_email  = $class_filter->clr_str($_POST['account_email_address_new']);
                $mail_extra = serialize(array("sender" => $_SESSION['USER_NAME'], "usr_id" => $_SESSION['USER_ID'], "new_mail" => $new_email));
                break;
            case "account_removal":
                $reason     = ($_POST['account_manage_del_reason'] != '' ? $language['account.manage.del.reason'] . ': ' . $class_filter->clr_str($_POST['account_manage_del_reason']) : '<p></p>');
                $mail_extra = serialize(array("sender" => $_SESSION['USER_NAME'], "usr_id" => $_SESSION['USER_ID'], "reason" => $reason, "admin" => $cfg['backend_username']));
                break;
            case "payment_notification_fe":
            case "payment_notification_be":
            case "payment_affiliate":
            case "payment_subscriber":
            case "payout_tokens":
            case "make-affiliate":
            case "clear-affiliate":
            case "make-partner":
            case "clear-partner":
            case "make-affiliate-email":
            case "clear-affiliate-email":
            case "make-partner-email":
            case "clear-partner-email":
                $mail_from  = $cfg['noreply_email'];
                $mail_extra = serialize($db_id);
                break;
        }
        $mail_do = $class_database->doInsert('db_mailque', array("usr_id" => $mail_usr, "mail_type" => $mail_type, "mail_key" => $mail_key, "mail_from" => $mail_from, "mail_to" => $mail_to, "mail_extra" => $mail_extra, "mail_datetime" => $mail_date));

        $cmd = $php_cgi . $cfg['server_path_php'] . ' ' . $cfg['main_dir'] . '/f_modules/m_frontend/m_cron/mailer.php ' . $mail_type . ' ' . $mail_key;
        exec(escapeshellcmd($cmd) . ' >/dev/null &');

        if ($type == 'pl_share') {
            echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            echo VGenerate::declareJS('$(document).ready(function(){$("#share_pl_to").val("");});');
        }

    }
    /* str_replace associative arrays */
    public function strReplaceAssoc(array $replace, $subject)
    {
        return str_replace(array_keys($replace), array_values($replace), $subject);
    }
    /* adding to que and logging */
    public function Mailer($mail_type, $mail_key)
    {
        global $db, $language, $class_database, $cfg, $class_filter;

        $cfg[] = $class_database->getConfigurations('email_logging');
        $dbq   = $db->execute(sprintf("SELECT `usr_id`, `mail_from`, `mail_to`, `mail_extra` FROM `db_mailque` WHERE `mail_type`='%s' AND `mail_key`='%s' AND `mail_complete`='0';", $mail_type, $mail_key));

        if ($dbq->fields['usr_id'] == '') {echo "denied\n";return false;}

        include 'f_core/config.set.php';

        $mail_usr   = VUserinfo::getUserName($dbq->fields['usr_id']);
        $mail_to    = unserialize($dbq->fields['mail_to']);
        $mail_extra = unserialize($dbq->fields['mail_extra']);
        $notifier   = new VNotify;

        $_output   = array();
        $section   = ($mail_type == 'username_recovery_be' or $mail_type == 'password_recovery_be') ? 'backend' : 'frontend';
        $mail_type = ($mail_type == 'username_recovery_be' or $mail_type == 'password_recovery_be') ? substr($mail_type, 0, -3) : $mail_type;

        foreach ($mail_to as $key => $val) {
            $notifier->dst_mail = $val;

            $do_notify = $notifier->Mail($section, $mail_type, $mail_extra);
            $_output[] = $mail_usr . ' -> ' . $mail_type . ' -> ' . $val . ' -> ' . date("Y-m-d H:i:s");
        }

        $log = '.mailer.log';
        if ($cfg['email_logging'] == 1) {
            VServer::logToFile($log, implode("\n", $_output));
        }

    }
    public static function displayName($info)
    {
        $username = $info['uname'];
        $dname    = $info['dname'];
        $ch_title = $info['ch_title'];

        return ($dname != '' ? $dname : ($ch_title != '' ? $ch_title : $username));
    }
    /* mailing */
    public function Mail($section, $type, $_replace = '', $user_notification = '')
    {
        global $cfg, $class_database, $class_filter, $language, $smarty;
        require 'class_phpmailer/vendor/autoload.php';

        $dash  = new VbeDashboard;
        $ntype = $type;

        switch ($section) {
            case 'frontend':
                $main_url = $cfg['main_url'];
                break;
                $cfg[] = $class_database->getConfigurations('backend_email,backend_username');
            case 'backend':
                include 'f_core/config.backend.php';
                $cfg      = $class_database->getConfigurations('backend_email,backend_username');
                $main_url = $cfg['main_url'] . '/' . $backend_access_url;
                $user_id  = 0;
                break;
        }
        $website_logo = $smarty->fetch($cfg['templates_dir'] . '/tpl_frontend/tpl_header/tpl_headerlogo_mail.tpl');

        switch ($type) {
            case "welcome":
                $response_div   = '';
                $this->msg_subj = $language['mail.verif.welcome'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_welcome.tpl');
                $user_name      = $_replace['sender'];

                $_replace = array(
                    '##TITLE##' => $this->msg_subj,
                    '##LOGO##'  => $website_logo,
                    '##H2##'    => $language['mail.verif.notif.fe.txt3'] . $user_name . '!',
                    '##YEAR##'  => date('Y'),
                );

                $this->dst_name = $user_name;
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "contact":
                $response_div   = '';
                $this->dst_mail = $cfg['backend_email'];
                $this->dst_name = $cfg['backend_username'];
                $this->msg_subj = $language['footer.contact.form.subject'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_contact.tpl');
                $msg_from       = $_replace['name'] . ' / ' . $_replace['email'];
                $msg_details    = $_replace['message'];
                $msg_ip         = $_replace['ip'];

                $_replace = array(
                    '##TITLE##'       => $this->msg_subj,
                    '##LOGO##'        => $website_logo,
                    '##MSG_FROM##'    => $msg_from,
                    '##MSG_DETAILS##' => $msg_details,
                    '##MSG_IP##'      => $msg_ip,
                    '##YEAR##'        => date('Y'),
                );

                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "email_digest":
                global $db, $class_language;
                include_once $class_language->setLanguageFile('frontend', 'language.global');

                $response_div   = '';
                $this->msg_subj = $language['mail.digest.h1'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_emaildigest.tpl');
                $user_name      = $_replace['sender'][0];
                $_from          = $_replace['sender'][1];
                $_period        = $_replace['sender'][2];
                $mod            = array('live', 'video', 'short', 'image', 'audio', 'doc', 'blog');
                $sort           = $_period == 1 ? "AND STR_TO_DATE(A.`upload_date`, '%Y-%m-%d') = CURDATE()" : ($_period == 2 ? "AND YEARweek(STR_TO_DATE(A.`upload_date`, '%Y-%m-%d')) = YEARweek(CURRENT_DATE - INTERVAL 7 DAY)" : null);
                $limit          = $_period == 1 ? 4 : ($_period == 2 ? 8 : 0);

                foreach ($_from as $k => $v) {
                    foreach ($mod as $name) {
                        $_s = 1;
                        if ($cfg[($name == 'doc' ? 'document' : $name) . "_module"] == 1) {
                            $u_info = VUserinfo::getUserInfo($v);
                            $u_usr  = $u_info['uname'];
                            $d_usr  = self::displayName($u_info);

                            $sql = sprintf("SELECT
                            A.`file_key`, A.`upload_date`,
                            A.`file_title`,
                            C.`usr_key`
                            FROM
                            `db_%sfiles` A, `db_accountuser` C
                            WHERE
                            A.`usr_id`=C.`usr_id` AND
                            A.`usr_id`='%s' AND
                            A.`privacy`='public' AND
                            A.`approved`='1' AND
                            A.`deleted`='0' AND
                            A.`active`='1' %s
                            ORDER BY A.`db_id` DESC
                            LIMIT %s;", $name, $v, $sort, $limit);
                            $res = $db->execute($sql);

                            $u_url = '<a href="' . VHref::channelURL(["username" => $u_usr]) . '" style="color: #585858; color: #585858 !important; text-decoration: none;">' . $d_usr . '</a> <span style="font-weight: normal;">(' . $language["frontend.global." . $name[0] . ".p"] . ')' . ($res->fields['file_key'] == '' ? ' - ' . $language['mail.digest.no.files'] : null) . '</span>';
                            $u_img = '<a href="' . VHref::channelURL(["username" => $u_usr]) . '"><img class="link-border" src="' . VUseraccount::getProfileImage($v) . '" alt="" width="32" height="32" /></a>';

                            $body .= '<table cellpadding="0" cellspacing="0" border="0" style="padding-top: 20px;"><tr>';
                            $body .= '<td colspan="8" style="padding-bottom: 7px;"><div style="float: left;">' . $u_img . '</div><div style="float: left; padding-left: 5px; font-weight: bold;">' . $u_url . '</div></td></tr><tr>';
                            if ($res->fields['file_key'] != '') {
                                while (!$res->EOF) {
                                    $tmb = VGenerate::thumbURL($name, $res->fields['usr_key'], $res->fields['file_key']);
                                    $body .= $_s == 5 ? '</tr><tr><td colspan="8" style="height: 15px;"></td></tr><tr>' : null;
                                    $body .= '<td width="180" style="border: 2px solid #EEE;">';
                                    $body .= '<div style="width: 180px; height: 140px; overflow: hidden;">';
                                    $body .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($name[0], $res->fields['file_key'], $res->fields['file_title']) . '" target="_blank" style="display: block;">';
                                    $body .= '<img src="' . $tmb . '" width="180" alt="' . $res->fields['file_title'] . '" style="border: 0;">';
                                    $body .= '</a></div>';
                                    $body .= '<div style="padding-left: 5px; padding-right: 5px; padding-top: 5px; padding-bottom: 5px;">';
                                    $body .= '<div style="font-size: 12px; font-weight: bold; font-family: arial, verdana, sans-serif; width: 150px; text-overflow: ellipsis; -o-text-overflow: ellipsis; -icab-text-overflow: ellipsis; -khtml-text-overflow: ellipsis; -moz-text-overflow: ellipsis; -webkit-text-overflow: ellipsis; white-space: nowrap; overflow: hidden; color: #E12E31;">';
                                    $body .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($name[0], $res->fields['file_key'], $res->fields['file_title']) . '" style="color: #E12E31;" title="">' . $res->fields['file_title'] . '</a>';
                                    $body .= '</div>';
                                    $body .= '<div style="color: #636363; font-size: 11px; font-family: arial, verdana, sans-serif;">' . date('F d, Y', strtotime($res->fields['upload_date'])) . '</div>';
                                    $body .= '</div></td><td width="20"></td>';

                                    $res->MoveNext();
                                    $_s++;
                                }
                            }
                            $body .= '</tr></table>';
                        }
                    }
                }

                $_replace = array(
                    '##TITLE##' => $this->msg_subj,
                    '##LOGO##'  => $website_logo,
                    '##BODY##'  => $body,
                    '##YEAR##'  => date('Y'),
                );

                $this->dst_name = $user_name;
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "mobile_feedback":
                $response_div   = '';
                $this->msg_subj = $language['mobile.feedback.subject'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_mobilefeedback.tpl');
                $user_name      = $_replace['sender'];
                $user_email     = $_replace['email'];
                $user_ip        = $_replace['ip'];
                $user_msg       = '<pre>' . $_replace['message'] . '</pre>';

                $_replace = array(
                    '##TITLE##'   => $this->msg_subj,
                    '##LOGO##'    => $website_logo,
                    '##NAME##'    => '<b>' . $language['mobile.notif.txt2'] . '</b>: ' . $user_name,
                    '##EMAIL##'   => '<b>' . $language['backend.notif.signup.email'] . '</b>: ' . $user_email,
                    '##IP##'      => '<b>' . $language['backend.notif.signup.ip'] . '</b>: ' . $user_ip,
                    '##MESSAGE##' => '<b>' . $language['mobile.notif.txt3'] . '</b>: ' . $user_msg,
                    '##YEAR##'    => date('Y'),
                );

                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "file_flagging":
                $response_div   = '';
                $cfg[]          = $class_database->getConfigurations('thumbs_width,thumbs_height');
                $user_name      = $_replace['sender'];
                $type           = $_replace['type'];
                $file_key       = $_replace['key'];
                $this->msg_subj = $user_name . $language['file.flagged.subject'] . $type;
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_fileflagging.tpl');

                $u_info  = VUserinfo::getUserInfo($class_database->singleFieldValue('db_' . $type . 'files', 'usr_id', 'file_key', $file_key));
                $d_usr   = self::displayName($u_info);
                $u_key   = $u_info['key'];
                $tmb_url = VGenerate::thumbURL($type, $u_key, $file_key);
                $tmb_img = $file_key != '' ? '<img width="' . $cfg['thumbs_width'] . '" height="' . $cfg['thumbs_height'] . '" alt="" src="' . $tmb_url . '" title="" />' : null;
                $_title  = $class_database->singleFieldValue('db_' . $type . 'files', 'file_title', 'file_key', $file_key);

                $file_details = '<div style="background-color: #F7F7F7; border: 1px solid #CCD; padding: 10px 10px 5px 10px; margin-bottom: 15px; width: 98%; display: inline-block">';
                $file_details .= '<div style="float: left; margin: 0 10px 5px 0; border: 1px solid #999; padding: 2px;">';
                $file_details .= '<div style="border: 1px solid #FFF; overflow: hidden; background-color: #FFF;">';
                $file_details .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($type[0], $file_key, $_title) . '">';
                $file_details .= $tmb_img;
                $file_details .= '</a></div></div>';
                $file_details .= '<div class="float: left;"><p style="padding-top: 0px; margin-top: 0px;">';
                $file_details .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($type[0], $file_key, $_title) . '">' . self::eq($_title) . '</a>';
                $file_details .= '</p>';
                $file_details .= '<p>' . nl2br(VUserinfo::truncateString(self::eq($class_database->singleFieldValue('db_' . $type . 'files', 'file_description', 'file_key', $file_key)), 50)) . '</p>';
                $file_details .= '</div></div>';

                $reason   = $_replace['reason'];
                $_replace = array(
                    '##TITLE##'    => $this->msg_subj,
                    '##LOGO##'     => $website_logo,
                    '##H2##'       => $language['recovery.forgot.password.h2'] . $_replace['admin'] . '!',
                    '##USERNAME##' => '<a href="' . VHref::channelURL(["username" => $user_name]) . '">' . $d_usr . '</a>',
                    '##TYPE##'     => $type,
                    '##REASON##'   => $reason,
                    '##FILE##'     => $file_details,
                    '##YEAR##'     => date('Y'),
                );

                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);

                include 'f_core/config.backend.php';
                $main_url = $cfg['main_url'] . '/' . $backend_access_url;

                $notif_body = '
            <div class="place-left"><p><b>' . $reason . '</b></p><br></div>
            <div class="clearfix"></div>
            <div class="vs-column fourths">
                <a href="' . $main_url . '/' . VHref::getKey('be_files') . '?k=' . $type[0] . $file_key . '" target="_blank">' . $tmb_img . '</a>
            </div>
            <div class="vs-column three_fourths fit">
                <a href="' . $main_url . '/' . VHref::getKey('be_files') . '?k=' . $type[0] . $file_key . '" target="_blank">' . $_title . '</a>
            </div>
            <div class="clearfix"></div>
        ';

                $dash->addNotification($ntype, str_replace($user_name, '<a href="' . $main_url . '/' . VHref::getKey("be_members") . '?u=' . $u_key . '" target="_blank">' . $user_name . '</a>', $this->msg_subj), $notif_body);
                break;

            case "file_share":
                $response_div = '';
                $cfg[]        = $class_database->getConfigurations('thumbs_width,thumbs_height');
                $user_name    = $_replace['sender'];
                $type         = $_replace['type'];
                $db_type      = $type[0] == 'd' ? 'doc' : ($type[0] == 'p' ? 'image' : ($type[0] == 'm' ? 'audio' : ($type[0] == 's' ? 'short' : 'video')));
                $file_key     = $_replace['key'];
                $title        = $_replace['title'];
                $note         = $_replace['note'];
                $msg_body     = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_sharefile.tpl');

                $u_info         = VUserinfo::getUserInfo($class_database->singleFieldValue('db_' . $db_type . 'files', 'usr_id', 'file_key', $file_key));
                $d_usr          = self::displayName($u_info);
                $u_key          = $u_info['key'];
                $this->msg_subj = $d_usr . $language['shared.file.subject'] . $type . ': "' . $title . '"';
                $tmb_url        = VGenerate::thumbURL($db_type, $u_key, $file_key);
                $tmb_img        = $file_key != '' ? '<img width="' . $cfg['thumbs_width'] . '" height="' . $cfg['thumbs_height'] . '" alt="" src="' . $tmb_url . '" title="" />' : null;
                $_title         = $class_database->singleFieldValue('db_' . $db_type . 'files', 'file_title', 'file_key', $file_key);

                $file_details = '<div style="background-color: #F7F7F7; border: 1px solid #CCD; padding: 10px 10px 5px 10px; margin-bottom: 15px; width: 98%; display: inline-block">';
                $file_details .= '<div style="float: left; margin: 0 10px 5px 0; border: 1px solid #999; padding: 2px;">';
                $file_details .= '<div style="border: 1px solid #FFF; overflow: hidden; background-color: #FFF;">';
                $file_details .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($db_type[0], $file_key, $_title) . '">';
                $file_details .= $tmb_img;
                $file_details .= '</a></div></div>';
                $file_details .= '<div class="float: left;"><p style="padding-top: 0px; margin-top: 0px;">';
                $file_details .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($db_type[0], $file_key, $_title) . '">' . self::eq($_title) . '</a>';
                $file_details .= '</p>';
                $file_details .= '<p>' . nl2br(VUserinfo::truncateString(self::eq($class_database->singleFieldValue('db_' . $db_type . 'files', 'file_description', 'file_key', $file_key)), 50)) . '</p>';
                $file_details .= '</div></div>';

                $_replace = array(
                    '##TITLE##'    => $this->msg_subj,
                    '##LOGO##'     => $website_logo,
                    '##USERNAME##' => '<a href="' . VHref::channelURL(["username" => $user_name]) . '">' . $d_usr . '</a>',
                    '##TYPE##'     => $type,
                    '##FILE##'     => $file_details,
                    '##NOTE##'     => $note,
                    '##YEAR##'     => date('Y'),
                );

                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "backend_notification_signup":
                $response_div   = '';
                $user_name      = $_replace['sender'];
                $user_mail      = $_replace['email'];
                $user_ip        = $_replace['ip'];
                $admin_name     = $_replace['admin_user'];
                $this->msg_subj = $language['new.member.subject'] . ': ' . $user_name;
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_newmember_be.tpl');

                $_replace = array(
                    '##TITLE##'   => $this->msg_subj,
                    '##LOGO##'    => $website_logo,
                    '##H2##'      => $language['recovery.forgot.password.h2'] . $admin_name . '!',
                    '##U_NAME##'  => $user_name,
                    '##U_EMAIL##' => $user_mail,
                    '##U_IP##'    => $user_ip,
                    '##YEAR##'    => date('Y'),
                );

                $this->dst_name = $admin_name;
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);

                include 'f_core/config.backend.php';
                $u_key    = $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_user', $user_name);
                $main_url = $cfg['main_url'] . '/' . $backend_access_url;

                $notif_body = '
            <div>Username: <a href="' . $main_url . '/' . VHref::getKey("be_members") . '?u=' . $u_key . '">' . $user_name . '</a></div>
            <div>Email: <b>' . $user_mail . '</b></div>
            <div>IP Address: <b>' . $user_ip . '</b></div>
            <div class="clearfix"></div>
        ';

                $dash->addNotification($ntype, str_replace($user_name, '<a href="' . $main_url . '/' . VHref::getKey("be_members") . '?u=' . $u_key . '" target="_blank">' . $user_name . '</a>', $this->msg_subj), $notif_body);
                break;

            case "account_email_verification":
            case "account_email_verification_confirm":
                $response_div   = '';
                $this->msg_subj = $language['mail.verif.subject'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_emailverification.tpl');
                $db_string      = VUserinfo::generateRandomString(10);
                $usr_string     = VUserinfo::generateRandomString();
                $usr_name       = $_replace['sender'];
                $user_codes     = VRecovery::addRecoveryEntry($_replace['usr_id'], $db_string, 'email_verification');

                $_replace = array(
                    '##TITLE##'     => $this->msg_subj,
                    '##LOGO##'      => $website_logo,
                    '##H2##'        => $language['mail.verif.notif.fe.txt3'] . $usr_name . '!',
                    '##USER_LINK##' => $main_url . '/' . VHref::getKey("confirm_email") . '?sid=' . $db_string . '&uid=' . strtoupper($usr_string),
                    '##YEAR##'      => date('Y'),
                );

                $this->dst_name = $usr_name;
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "pl_share":
                switch ($_replace['pl_type']) {
                    case "l":$tbl = 'live';
                        break;
                    case "v":$tbl = 'video';
                        break;
                    case "s":$tbl = 'short';
                        break;
                    case "i":$tbl = 'image';
                        break;
                    case "a":$tbl = 'audio';
                        break;
                    case "d":$tbl = 'doc';
                        break;
                    case "b":$tbl = 'blog';
                        break;
                }

                $response_div = '';
                $cfg[]        = $class_database->getConfigurations('thumbs_width,thumbs_height');
                $msg_body     = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_shareplaylist.tpl');

                $pl_details = '<div style="background-color: #F7F7F7; border: 1px solid #CCD; padding: 10px 10px 5px 10px; margin-bottom: 15px; width: 98%; display: inline-block">';
                $pl_details .= '<div style="float: left; margin: 0 10px 5px 0; border: 1px solid #999; padding: 2px; width: ' . $cfg['thumbs_width'] . 'px; height: ' . $cfg['thumbs_height'] . 'px;">';
                $pl_details .= '<div style="border: 1px solid #FFF; overflow: hidden; background-color: #FFF;">';
                $pl_details .= '<a href="' . $cfg['main_url'] . '/' . VHref::getKey("playlist") . '?' . $_replace['pl_type'] . '=' . $class_database->singleFieldValue('db_' . $tbl . 'playlists', 'pl_key', 'pl_id', $_replace['pl_id']) . '">';
                $thumb = $class_database->singleFieldValue('db_' . $tbl . 'playlists', 'pl_thumb', 'pl_id', $_replace['pl_id']);

                if ($class_database->singleFieldValue('db_' . $tbl . 'files', 'privacy', 'file_key', $thumb) == 'public' and $class_database->singleFieldValue('db_' . $tbl . 'files', 'deleted', 'file_key', $thumb) == '0') {
                    $u_info  = VUserinfo::getUserInfo(VUserinfo::getUserID($_replace['username']));
                    $u_key   = $u_info['key'];
                    $d_usr   = self::displayName($u_info);
                    $tmb_url = VGenerate::thumbURL($tbl, $u_key, $thumb);
                    $tmb_img = $thumb != '' ? '<img width="116" height="68" alt="" src="' . $tmb_url . '" title="" />' : null;
                }
                $pl_details .= $tmb_img;
                $pl_details .= '</a></div></div><div class="float: left;"><p style="padding-top: 0px; margin-top: 0px;"><a href="' . $cfg['main_url'] . '/' . VHref::getKey("playlist") . '?' . $_replace['pl_type'] . '=' . $class_database->singleFieldValue('db_' . $tbl . 'playlists', 'pl_key', 'pl_id', $_replace['pl_id']) . '">' . $class_database->singleFieldValue('db_' . $tbl . 'playlists', 'pl_name', 'pl_id', $_replace['pl_id']) . '</a></p><p>' . $class_database->singleFieldValue('db_' . $tbl . 'playlists', 'pl_descr', 'pl_id', $_replace['pl_id']) . '</p></div></div>';

                $this->msg_subj = $d_usr . $language['shared.playlist.subject'] . '"' . $class_database->singleFieldValue('db_' . $tbl . 'playlists', 'pl_name', 'pl_id', $_replace['pl_id']) . '"';

                $_replace = array(
                    '##TITLE##'            => $this->msg_subj,
                    '##LOGO##'             => $website_logo,
                    '##USER##'             => $d_usr,
                    '##CHURL##'            => VHref::channelURL(["username" => $_replace['username']]),
                    '##PLAYLIST_MESSAGE##' => ($_replace['pl_msg'] != '' ? '<p>' . $_replace['pl_msg'] . '</p>' : null),
                    '##PLAYLIST_DETAILS##' => $pl_details,
                    '##YEAR##'             => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "invite_user":
                $response_div = '';
                $msg_body     = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_inviteuser.tpl');
                $db_string    = $class_database->singleFieldValue('db_usercontacts', 'pwd_id', 'ct_id', $_replace['ct_id']);
                $u_info       = VUserinfo::getUserInfo($_replace['usr_id']);
                $u_key        = $u_info['key'];
                $d_usr        = self::displayName($u_info);

                $this->msg_subj = $d_usr . $language['invite.contact.username.subject'];
                $user_to        = VUserinfo::getUserId($class_database->singleFieldValue('db_usercontacts', 'ct_username', 'ct_id', $_replace['ct_id']));
                $user_codes     = VRecovery::addRecoveryEntry(intval($_replace['usr_id']), $db_string, $type, $user_to);

                $user_msg = $class_database->doInsert('db_messaging', array(
                    "msg_subj"   => $_replace['subject'] . ' ' . $d_usr,
                    "msg_body"   => '<a href="' . VHref::channelURL(["username" => $_replace['sender']]) . '">' . $d_usr . '</a> ' . $language['mail.invite.email.txt7'] . '<input type="hidden" name="inv_key" value="' . $db_string . '" />',
                    "msg_from"   => intval($_replace['usr_id']),
                    "msg_to"     => $user_to,
                    "msg_invite" => 1,
                    "msg_date"   => date("Y-m-d H:i:s")));

                $_replace = array(
                    '##TITLE##' => $this->msg_subj,
                    '##LOGO##'  => $website_logo,
                    '##USER##'  => '<a href="' . VHref::channelURL(["username" => $_replace['sender']]) . '">' . $d_usr . '</a>',
                    '##YEAR##'  => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "invite_contact":
                $response_div = '';
                $msg_body     = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_invitecontact.tpl');
                $db_string    = $class_database->singleFieldValue('db_usercontacts', 'pwd_id', 'ct_id', $_replace['ct_id']);
                $usr_string   = VUserinfo::generateRandomString();
                $u_info       = VUserinfo::getUserInfo($_replace['usr_id']);
                $u_key        = $u_info['key'];
                $d_usr        = self::displayName($u_info);
                $user_codes   = VRecovery::addRecoveryEntry($_replace['usr_id'], $db_string, $type, $this->dst_mail);

                $this->msg_subj = $d_usr . $language['invite.contact.other.subject'];

                $_replace = array(
                    '##TITLE##' => $this->msg_subj,
                    '##LOGO##'  => $website_logo,
                    '##USER##'  => '<a href="' . VHref::channelURL(["username" => $_replace['sender']]) . '">' . $d_usr . '</a>',
                    '##LINK##'  => $main_url . '/' . VHref::getKey("confirm_friend") . '?sid=' . $db_string . '&uid=' . strtoupper($usr_string),
                    '##YEAR##'  => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "subscribe":
                $response_div   = '';
                $usr            = $_replace['sender'];
                $u_id           = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_user', $usr);
                $u_info         = VUserinfo::getUserInfo($u_id);
                $u_key          = $u_info['key'];
                $d_usr          = self::displayName($u_info);
                $this->msg_subj = $d_usr . $language['subscribe.channel.subject'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_subscribe.tpl');

                $_replace = array(
                    '##TITLE##' => $this->msg_subj,
                    '##LOGO##'  => $website_logo,
                    '##USER##'  => $d_usr,
                    '##CHURL##' => VHref::channelURL(["username" => $_replace['sender']]),
                    '##YEAR##'  => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "follow":
                $response_div   = '';
                $usr            = $_replace['sender'];
                $u_id           = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_user', $usr);
                $u_info         = VUserinfo::getUserInfo($u_id);
                $u_key          = $u_info['key'];
                $d_usr          = self::displayName($u_info);
                $this->msg_subj = $d_usr . $language['follow.channel.subject'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_follow.tpl');

                $_replace = array(
                    '##TITLE##' => $this->msg_subj,
                    '##LOGO##'  => $website_logo,
                    '##USER##'  => $d_usr,
                    '##CHURL##' => VHref::channelURL(["username" => $_replace['sender']]),
                    '##YEAR##'  => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "channel_comment":
            case "channel_comment_reply":
                $response_div   = '';
                $c_body         = nl2br($class_database->singleFieldValue('db_channelcomments', 'c_body', 'c_id', $_replace['db_id']));
                $u_id           = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_user', $_replace['sender']);
                $u_info         = VUserinfo::getUserInfo($u_id);
                $u_key          = $u_info['key'];
                $d_usr          = self::displayName($u_info);
                $this->msg_subj = $d_usr . ($type == 'channel_comment' ? $language['post.comment.subject'] : $language['post.comment.reply.subject']);
                $smarty->assign('comm_type', $type);
                $msg_body = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_channelcomment.tpl');

                $_replace = array(
                    '##TITLE##'   => $this->msg_subj,
                    '##LOGO##'    => $website_logo,
                    '##USER##'    => $d_usr,
                    '##CHURL##'   => VHref::channelURL(["username" => $_replace['sender']]),
                    '##COMMENT##' => $c_body,
                    '##YEAR##'    => date('Y'),
                );

                if ($cfg['comment_emoji'] == 1) {
                    require 'f_core/f_functions/functions.emoji.php';
                    $this->msg_body = Emojione\emsg(self::strReplaceAssoc($_replace, $msg_body));
                } else {
                    $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                }

                break;

            case "file_comment":
            case "file_comment_reply":
                $response_div   = '';
                $ftype          = $type;
                $type           = $_replace['type'];
                $c_key          = $_replace['key'];
                $c_body         = nl2br($class_database->singleFieldValue('db_' . $type . 'comments', 'c_body', 'c_id', $_replace['db_id']));
                $u_id           = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_user', $_replace['sender']);
                $u_info         = VUserinfo::getUserInfo($u_id);
                $u_key          = $u_info['key'];
                $d_usr          = self::displayName($u_info);
                $c_title        = self::eq($class_database->singleFieldValue('db_' . $type . 'files', 'file_title', 'file_key', $c_key));
                $f_url          = '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($type[0], $c_key, $c_title) . '">' . $c_title . '</a>';
                $this->msg_subj = ($ftype == 'file_comment_reply' ? $language['post.comment.file.subject.reply'] : $language['post.comment.file.subject']) . '"' . VUserinfo::truncateString($c_title, 40) . '"';
                $smarty->assign('comm_type', $ftype);
                $msg_body = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_filecomment.tpl');

                $_replace = array(
                    '##TITLE##'   => $this->msg_subj,
                    '##LOGO##'    => $website_logo,
                    '##USER##'    => $d_usr,
                    '##CHURL##'   => VHref::channelURL(["username" => $_replace['sender']]),
                    '##COMMENT##' => $c_body,
                    '##FTITLE##'  => $f_url,
                    '##YEAR##'    => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);

                break;

            case "file_response":
            case "file_response_upload":
                $response_div   = '';
                $cfg[]          = $class_database->getConfigurations('thumbs_width,thumbs_height');
                $tbl            = $_replace['tbl'];
                $type           = $_replace['type'];
                $type_s         = $tbl == 'document' ? 'doc' : $tbl;
                $c_key          = $_replace['key'];
                $r_key          = $_replace['r_key'];
                $file_key       = $c_key;
                $c_title        = self::eq($class_database->singleFieldValue('db_' . $type_s . 'files', 'file_title', 'file_key', $c_key));
                $f_url          = '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($type_s[0], $c_key, $c_title) . '">' . $c_title . '</a>';
                $this->msg_subj = str_replace('##TYPE##', $type, $language['response.file.subject']) . '"' . VUserinfo::truncateString($c_title, 40) . '"';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_fileresponse.tpl');
                if ($class_database->singleFieldValue('db_' . $type_s . 'files', 'privacy', 'file_key', $r_key) == 'public') {
                    $u_id    = $class_database->singleFieldValue('db_' . $tbl . 'files', 'usr_id', 'file_key', $r_key);
                    $u_info  = VUserinfo::getUserInfo($u_id);
                    $u_key   = $u_info['key'];
                    $d_usr   = self::displayName($u_info);
                    $tmb_url = VGenerate::thumbURL($tbl, $u_key, $r_key);
                    $tmb_img = $file_key != '' ? '<img width="' . $cfg['thumbs_width'] . '" height="' . $cfg['thumbs_height'] . '" alt="" src="' . $tmb_url . '" title="" />' : null;
                } else {
                    $tmb_img = '';
                }

                $_title = $class_database->singleFieldValue('db_' . $tbl . 'files', 'file_title', 'file_key', $r_key);

                $file_details = '<div style="background-color: #F7F7F7; border: 1px solid #CCD; padding: 10px 10px 5px 10px; margin-bottom: 15px; width: 98%; display: inline-block">';
                $file_details .= '<div style="float: left; margin: 0 10px 5px 0; border: 1px solid #999; padding: 2px;">';
                $file_details .= '<div style="border: 1px solid #FFF; overflow: hidden; background-color: #FFF;">';
                $file_details .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($type_s[0], $r_key, $_title) . '">';
                $file_details .= $tmb_img;
                $file_details .= '</a></div></div>';
                $file_details .= '<div class="float: left;"><p style="padding-top: 0px; margin-top: 0px;">';
                $file_details .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($type_s[0], $r_key, $_title) . '">' . $_title . '</a>';
                $file_details .= '</p>';
                $file_details .= '<p>' . nl2br(VUserinfo::truncateString($class_database->singleFieldValue('db_' . $tbl . 'files', 'file_description', 'file_key', $r_key), 100)) . '</p>';
                $file_details .= '</div></div>';

                $_replace = array(
                    '##TITLE##'        => $this->msg_subj,
                    '##LOGO##'         => $website_logo,
                    '##USER##'         => $d_usr,
                    '##CHURL##'        => VHref::channelURL(["username" => $_replace['sender']]),
                    '##FILE_DETAILS##' => $file_details,
                    '##RESPOND_TO##'   => $f_url,
                    '##RESPOND_TXT##'  => str_replace('##TYPE##', $type, $language['response.notif.txt1']),
                    '##YEAR##'         => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "new_upload":
            case "new_upload_be":
                $response_div = '';
                $cfg[]        = $class_database->getConfigurations('thumbs_width,thumbs_height');
                switch ($_replace['db_id'][0]) {
                    case "l":$tbl = 'live';
                        break;
                    case "v":$tbl = 'video';
                        break;
                    case "s":$tbl = 'short';
                        break;
                    case "i":$tbl = 'image';
                        break;
                    case "a":$tbl = 'audio';
                        break;
                    case "d":$tbl = 'doc';
                        break;
                    case "b":$tbl = 'blog';
                        break;
                }

                $msg_body = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_newupload.tpl');

                $file_key = substr($_replace['db_id'], 1);
                $thumb    = $file_key;

                if ($class_database->singleFieldValue('db_' . $tbl . 'files', 'privacy', 'file_key', $thumb) == 'public' or $type == 'new_upload_be') {
                    $u_id    = $class_database->singleFieldValue('db_' . $tbl . 'files', 'usr_id', 'file_key', $file_key);
                    $u_info  = VUserinfo::getUserInfo($u_id);
                    $u_key   = $u_info['key'];
                    $d_usr   = self::displayName($u_info);
                    $tmb_url = VGenerate::thumbURL($tbl, $u_key, $thumb);
                    $tmb_img = $thumb != '' ? '<img width="' . $cfg['thumbs_width'] . '" height="' . $cfg['thumbs_height'] . '" alt="" src="' . $tmb_url . '" title="" />' : null;
                } else {
                    $tmb_img = '';
                }

                $_title    = $class_database->singleFieldValue('db_' . $tbl . 'files', 'file_title', 'file_key', $file_key);
                $user_name = $d_usr;

                $this->msg_subj = $d_usr . ($tbl == 'live' ? $language['new.upload.subject.live'] : $language['new.upload.subject']) . $_replace['type'];

                $file_details = '<div style="background-color: #F7F7F7; border: 1px solid #CCD; padding: 10px 10px 5px 10px; margin-bottom: 15px; width: 98%; display: inline-block">';
                $file_details .= '<div style="float: left; margin: 0 10px 5px 0; border: 1px solid #999; padding: 2px;">';
                $file_details .= '<div style="border: 1px solid #FFF; overflow: hidden; background-color: #FFF;">';
                $file_details .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($_replace['db_id'][0], $file_key, $_title) . '">';
                $file_details .= $tmb_img;
                $file_details .= '</a></div></div>';
                $file_details .= '<div class="float: left;"><p style="padding-top: 0px; margin-top: 0px;">';
                $file_details .= '<a href="' . $cfg['main_url'] . '/' . VGenerate::fileHref($_replace['db_id'][0], $file_key, $_title) . '">' . self::eq($_title) . '</a>';
                $file_details .= '</p>';
                $file_details .= '<p>' . self::eq($class_database->singleFieldValue('db_' . $tbl . 'files', 'file_description', 'file_key', $file_key)) . '</p>';
                $file_details .= '</div></div>';

                $_replace = array(
                    '##TITLE##'        => $this->msg_subj,
                    '##LOGO##'         => $website_logo,
                    '##USER##'         => $d_usr,
                    '##CHURL##'        => VHref::channelURL(["username" => $_replace['sender']]),
                    '##SUBURL##'       => $cfg['main_url'] . '/' . VHref::getKey("subscriptions"),
                    '##TYPE##'         => $_replace['type'],
                    '##FILE_DETAILS##' => $file_details,
                    '##P_STYLE##'      => ($type == 'new_upload_be' ? 'display: none;' : ''),
                    '##YEAR##'         => date('Y'),
                );

                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);

                if ($type == 'new_upload_be') {
                    include 'f_core/config.backend.php';
                    $main_url = $cfg['main_url'] . '/' . $backend_access_url;

                    $notif_body = '
                <div class="vs-column fourths">
                    <a href="' . $main_url . '/' . VHref::getKey('be_files') . '?k=' . $tbl[0] . $file_key . '" target="_blank">' . $tmb_img . '</a>
                </div>
                <div class="vs-column three_fourths fit">
                    <a href="' . $main_url . '/' . VHref::getKey('be_files') . '?k=' . $tbl[0] . $file_key . '" target="_blank">' . $_title . '</a>
                </div>
                <div class="clearfix"></div>
            ';

                    $dash->addNotification($ntype, str_replace($user_name, '<a href="' . $main_url . '/' . VHref::getKey("be_members") . '?u=' . $u_key . '" target="_blank">' . $user_name . '</a>', $this->msg_subj), $notif_body);
                }
                break;

            case "password_recovery":
                $response_div   = 'x_err';
                $this->msg_subj = $language['recovery.forgot.password.subject'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_passwordrecovery.tpl');
                $user_name      = $_replace['user'];
                $reset_string   = VUserinfo::generateRandomString(10);
                $reset_id       = VUserinfo::generateRandomString();
                if ($section == 'frontend') {
                    $user_id   = VUserinfo::getUserID($user_name);
                    $user_data = VUserinfo::getUserInfo($user_id);
                }
                $user_codes = VRecovery::addRecoveryEntry($user_id, $reset_string);
                $href       = $section == 'frontend' ? VHref::getKey("reset_password") : VHref::getKey("be_reset_password");

                $_replace = array(
                    '##TITLE##'     => $this->msg_subj,
                    '##LOGO##'      => $website_logo,
                    '##H2##'        => $language['recovery.forgot.password.h2'] . $user_name . ',',
                    '##LINK_HREF##' => $main_url . '/' . $href . '?s=' . $reset_string . '&id=' . $reset_id,
                    '##YEAR##'      => date('Y'),
                );

                $this->dst_mail = ($section == 'frontend') ? VUserinfo::getUserEmail($user_id) : $cfg['backend_email'];
                $this->dst_name = ($section == 'frontend') ? (($user_data['fname'] != '' or $user_data['lname'] != '') ? $user_data['fname'] . ' ' . $user_data['lname'] : null) : $cfg['backend_username'];
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "username_recovery":
                $response_div   = 'r_err';
                $this->msg_subj = $language['recovery.forgot.username.subject'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_usernamerecovery.tpl');
                $user_email     = $_replace['mail'];
                if ($section == 'frontend') {
                    $user_id   = VUserinfo::getUserID($user_email, 'usr_email');
                    $user_data = VUserinfo::getUserInfo($user_id);
                }

                $_replace = array(
                    '##TITLE##'    => $this->msg_subj,
                    '##LOGO##'     => $website_logo,
                    '##H2##'       => $language['recovery.forgot.password.h2'] . $user_email . ',',
                    '##USERNAME##' => ($section == 'frontend') ? $user_data['uname'] : $cfg['backend_username'],
                    '##YEAR##'     => date('Y'),
                );

                $this->dst_mail = $user_email;
                $this->dst_name = ($section == 'frontend') ? (($user_data['fname'] != '' or $user_data['lname'] != '') ? $user_data['fname'] . ' ' . $user_data['lname'] : null) : $cfg['backend_username'];
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);

                $log = ($cfg['activity_logging'] == 1 and $action = new VActivity($user_id, 0)) ? $action->addTo('log_urecovery') : null;
                break;

            case "payment_affiliate":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_paymentnotification_affiliate.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "payment_subscriber":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_paymentnotification_subscriber.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "payout_tokens":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_payoutnotification_token.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "usr-make-affiliate":
                include 'f_core/config.backend.php';

                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_affiliate_confirm.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "usr-clear-affiliate":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_affiliate_denied.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "usr-make-partner":
                include 'f_core/config.backend.php';

                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_partner_confirm.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "usr-clear-partner":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_partner_denied.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "make-affiliate-email":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_affiliate_request.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                $dash->addNotification($type, $this->msg_subj . ': ' . $_replace['##USER##'], str_replace('##USER##', $_replace['##USER##'], $language['account.entry.affiliate.request']));
                break;
            case "clear-affiliate-email":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_affiliate_cancel.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                $dash->addNotification($type, $this->msg_subj . ': ' . $_replace['##USER##'], str_replace('##USER##', $_replace['##USER##'], $language['account.entry.affiliate.cancel']));
                break;
            case "make-partner-email":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_partner_request.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                $dash->addNotification($type, $this->msg_subj . ': ' . $_replace['##USER##'], str_replace('##USER##', $_replace['##USER##'], $language['account.entry.partner.request']));
                break;
            case "clear-partner-email":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_partner_cancel.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                $dash->addNotification($type, $this->msg_subj . ': ' . $_replace['##USER##'], str_replace('##USER##', $_replace['##USER##'], $language['account.entry.partner.cancel']));
                break;

            case "token_notification_fe":
                $response_div   = 'x_err';
                $this->msg_subj = $_replace['##TITLE##'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_tokennotification_fe.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "token_notification_be":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_tokennotification_be.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);

                $notif_body = '
                <p>' . $language['payment.notif.token.txt2'] . '</p>
                <p><br></p>
                <p>' . $language['payment.notif.be.txt3'] . '</p>
                <p>/************************************<br />##PAID_RECEIPT##************************************/</p>
                ';

                $dash->addNotification($ntype, str_replace($cfg['website_shortname'], '', $this->msg_subj), self::strReplaceAssoc($_replace, $notif_body));
                break;

            case "token_donation_fe":
                $response_div   = '';
                $this->msg_subj = $_replace['##TITLE##'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_tokendonation_fe.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "token_donation_be":
                $response_div   = '';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_tokendonation_be.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);

                $notif_body = '
                <p>' . $language['payment.notif.donate.txt2'] . '</p>
                ';

                $dash->addNotification($ntype, str_replace($cfg['website_shortname'], '', $_replace['##SUBJ##']), self::strReplaceAssoc($_replace, $notif_body));
                break;

            case "payment_notification_fe":
                $response_div   = 'x_err';
                $this->msg_subj = $this->msg_subj == '' ? $language['payment.notification.subject.fe'] : $this->msg_subj;
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_paymentnotification_fe.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;
            case "payment_notification_be":
                $response_div   = 'x_err';
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_paymentnotification_be.tpl');
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);

                include 'f_core/config.backend.php';
                $u_key    = $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_user', $_replace['##SUB_NAME##']);
                $main_url = $cfg['main_url'] . '/' . $backend_access_url;

                $notif_body = '
                <p>
                ' . $language['payment.notif.be.txt2'] . ' <a href="' . $main_url . '/' . VHref::getKey('be_members') . '?u=' . $u_key . '" target="_blank">##SUB_NAME##</a><br />
                ' . $language['payment.notif.fe.txt2'] . ' ##PACK_NAME##<br />
                ' . $language['payment.notif.fe.txt3'] . ' ##PAID_TOTAL##<br />
                ' . $language['payment.notif.fe.txt4'] . ' ##PACK_EXPIRE##
                </p>
                <p><br></p>
                <p>' . $language['payment.notif.be.txt3'] . '</p>
                <p>/************************************<br />##PAID_RECEIPT##************************************/</p>
        ';

                $dash->addNotification($ntype, str_replace($cfg['website_shortname'], '', $this->msg_subj), self::strReplaceAssoc($_replace, $notif_body));
                break;

            case "private_message":
            case "private_email":
                $cfg[]        = $class_database->getConfigurations('backend_username');
                $response_div = '';
                $m_id         = $_replace['db_id'];
                $m_send       = $_replace['sender'];
                $m_subj       = $type == 'private_email' ? $_replace['es'] : html_entity_decode($class_database->singleFieldValue('db_messaging', 'msg_subj', 'msg_id', $m_id), ENT_QUOTES, "UTF-8");
                $m_body       = $type == 'private_email' ? $_replace['eb'] : $class_database->singleFieldValue('db_messaging', 'msg_body', 'msg_id', $m_id);
                $u_id         = $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_user', $m_send);
                $u_info       = VUserinfo::getUserInfo($u_id);
                $u_key        = $u_info['key'];
                $d_usr        = $_replace['sender'] == $cfg['backend_username'] ? $cfg['backend_username'] : self::displayName($u_info);

                $this->msg_subj = $d_usr . $language['mail.notif.txt1'] . $m_subj;
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_privatemessage.tpl');

                $_replace = array(
                    '##TITLE##'    => $this->msg_subj,
                    '##LOGO##'     => $website_logo,
                    '##USERNAME##' => ($m_send == $cfg['backend_username'] ? $m_send : '<a href="' . VHref::channelURL(["username" => $m_send]) . '">' . $d_usr . '</a>'),
                    '##SUBJECT##'  => $m_subj,
                    '##MESSAGE##'  => nl2br($m_body),
                    '##ATTACH##'   => $type == 'private_message' ? VMessages::messageDetailsAttachment($m_id, 1) : null,
                    '##YEAR##'     => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "account_removal":
                $response_div   = '';
                $this->msg_subj = $_replace['sender'] . $language['mail.del.account.subject'];
                $this->dst_mail = $cfg['backend_email'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_accountremoval.tpl');
                $user_name      = $_replace['sender'];
                $reason         = $_replace['reason'];

                $_replace = array(
                    '##TITLE##'    => $this->msg_subj,
                    '##LOGO##'     => $website_logo,
                    '##H2##'       => $language['recovery.forgot.password.h2'] . $_replace['admin'] . ',',
                    '##USERNAME##' => $user_name,
                    '##REASON##'   => $reason,
                    '##YEAR##'     => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);

                include 'f_core/config.backend.php';
                $u_key    = $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_user', $user_name);
                $main_url = $cfg['main_url'] . '/' . $backend_access_url;

                $notif_body = '
            <p><a href="' . $main_url . '/' . VHref::getKey('be_members') . '?u=' . $u_key . '" target="_blank">' . $user_name . '</a> ' . $language['mail.account.del.txt1'] . '</p>
            <br>
                    <p><b>' . $reason . '</b></p>
        ';

                $dash->addNotification($ntype, str_replace($user_name, '<a href="' . $main_url . '/' . VHref::getKey('be_members') . '?u=' . $u_key . '" target="_blank">' . $user_name . '</a>', $this->msg_subj), $notif_body);
                break;

            case "change_email":
                $response_div   = '';
                $this->msg_subj = $language['mail.address.confirm.subject'];
                $this->dst_mail = $_replace['new_mail'];
                $msg_body       = $smarty->fetch($cfg['ww_templates_dir'] . '/tpl_email/tpl_emailchange.tpl');
                $db_string      = VUserinfo::generateRandomString(10);
                $usr_link       = $main_url . '/' . VHref::getKey("confirm_email") . '?sid=' . $db_string . '&uid=' . strtoupper(VUserinfo::generateRandomString());
                $user_codes     = VRecovery::addRecoveryEntry($_replace['usr_id'], $db_string, 'email_verification');

                $_replace = array(
                    '##TITLE##'     => $this->msg_subj,
                    '##LOGO##'      => $website_logo,
                    '##H2##'        => $language['recovery.forgot.password.h2'] . $_replace['sender'] . ',',
                    '##LINK_TEXT##' => str_replace('##LINK##', $usr_link, $language['mail.confirm.email.txt1']),
                    '##USER_LINK##' => '<a href="' . $usr_link . '">' . $usr_link . '</a>',
                    '##YEAR##'      => date('Y'),
                );
                $this->msg_body = self::strReplaceAssoc($_replace, $msg_body);
                break;

            case "disk_usage":
                $response_div   = '';
                $this->msg_subj = $cfg['website_shortname'] . ' Disk Space Low at ' . $_replace['data'];
                $this->dst_mail = $cfg['backend_email'];
                $this->msg_body = $cfg['website_shortname'] . ' Disk Space Low at ' . $_replace['data'];

                break;
        }

        if ($this->mailer == 'pop3') {
            include_once 'class_phpmailer/class.pop3.php';
            $pop = new POP3();
            $pop->Authorise($cfg['mail_pop3_host'], $cfg['mail_pop3_port'], $cfg['mail_pop3_timeout'], $cfg['mail_pop3_username'], $cfg['mail_pop3_password'], $cfg['mail_debug']);
        }

        $mail           = new PHPMailer;
        $mail->CharSet  = "UTF-8";
        $mail->Encoding = 'base64';

        $mail->SMTPDebug = ($cfg['mail_debug'] == 1 and ($cfg['mail_type'] == 'pop3' or $cfg['mail_type'] == 'smtp')) ? 3 : 0;

        try {
            switch ($this->mailer) {
                case 'sendmail':
                    $mail->IsSendmail();
                    break;
                case 'pop3':
                    $mail->IsSMTP();
                    $mail->SMTPSecure = ($this->smtp_sec != '') ? $this->smtp_sec : null;
                    $mail->Host       = $cfg['mail_pop3_host'];
                    break;
                case 'smtp':
                    $mail->IsSMTP();
                    switch ($this->smtp_auth) {
                        case 'false':
                            $mail->Host = $cfg['mail_smtp_host'];
                            break;
                        case 'true':
                            $mail->SMTPAuth   = $cfg['mail_smtp_auth'];
                            $mail->SMTPSecure = ($this->smtp_sec != '') ? $this->smtp_sec : null;
                            $mail->Host       = $cfg['mail_smtp_host'];
                            $mail->Port       = $cfg['mail_smtp_port'];
                            $mail->Username   = $cfg['mail_smtp_username'];
                            $mail->Password   = $cfg['mail_smtp_password'];
                            break;
                    }
            }

            $mail->AddReplyTo($this->noreply, $this->noreply_n);
            $mail->AddAddress($this->dst_mail, $this->dst_name);
            $mail->SetFrom($this->src_mail, $this->src_name);
            $mail->Subject = $this->msg_subj;
            $mail->AltBody = $this->msg_alt;
            $mail->MsgHTML($this->msg_body);
            $do_send = $mail->Send();
            if ($response_div != '') {self::showNotice('confirmation', $language['notif.success.request'], $response_div);}
        } catch (phpmailerException $e) {
            self::showNotice('error', $e->errorMessage());
        } catch (Exception $e) {
        }
    }

    public function showNotice($type, $msg, $div_id = 'x_err')
    {
        global $language;

        switch ($type) {
            case 'confirmation':$html = VGenerate::noticeTpl('', '', $msg);
                break;
            case 'error':$html = VGenerate::noticeTpl('', $msg, '');
        }

        echo $html;
    }

    private static function eq($text)
    {
        return html_entity_decode($text, ENT_QUOTES, 'UTF-8');
    }
}
