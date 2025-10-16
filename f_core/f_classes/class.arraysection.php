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

class VArraySection
{
    /* remove from array based on key */
    public function arrayRemoveKey()
    {
        $args = func_get_args();
        return array_diff_key($args[0], array_flip(array_slice($args, 1)));
    }
    /* multi array pop */
    public function array_mpop($array, $iterate)
    {
        if (!is_array($array) && is_int($iterate)) {
            return false;
        }

        while (($iterate--) != false) {
            array_pop($array);
        }

        return $array;
    }
    /* more sanitized forms and fields */
    public function getArray($section)
    {
        global $class_filter, $cfg;

        switch ($section) {
            case "be_xfer_video":
            case "be_xfer_short":
                $_array         = array();
                $allowedFields  = array();
                $requiredFields = array();
                break;
            case "up_servers":
                $entryid                       = (int) $_POST['hc_id'];
                $backend_servers_type          = "backend_servers_type_" . $entryid;
                $server_type                   = $class_filter->clr_str($_POST[$backend_servers_type]);
                $st                            = ($server_type == 's3' or $server_type == 'ws') ? $server_type : 's3';
                $backend_servers_transfer      = "backend_servers_transfer_" . $entryid;
                $backend_servers_passive       = "backend_servers_passive_" . $entryid;
                $backend_s3_region             = "backend_" . $st . "_region_" . $entryid;
                $backend_servers_cf_priceclass = "backend_servers_cf_priceclass_" . $entryid;
                $backend_servers_priority      = "backend_servers_priority_" . $entryid;
                $backend_servers_limit         = "backend_servers_limit_" . $entryid;
                $backend_servers_hop           = "backend_servers_hop_" . $entryid;

                $_array = array(
                    "server_name"          => $class_filter->clr_str($_POST['frontend_global_name']),
                    "server_type"          => $server_type,
                    "server_priority"      => intval($_POST[$backend_servers_priority]),
                    "server_limit"         => intval($_POST[$backend_servers_limit]),
                    "file_hop"             => intval($_POST[$backend_servers_hop]),
                    "upload_v_file"        => intval($_POST['backend_servers_ct_video']),
                    "upload_v_thumb"       => intval($_POST['backend_servers_ct_vthumb']),
                    "upload_s_file"        => intval($_POST['backend_servers_ct_short']),
                    "upload_s_thumb"       => intval($_POST['backend_servers_ct_sthumb']),
                    "upload_i_file"        => intval($_POST['backend_servers_ct_image']),
                    "upload_i_thumb"       => intval($_POST['backend_servers_ct_ithumb']),
                    "upload_a_file"        => intval($_POST['backend_servers_ct_audio']),
                    "upload_a_thumb"       => intval($_POST['backend_servers_ct_athumb']),
                    "upload_d_file"        => intval($_POST['backend_servers_ct_doc']),
                    "upload_d_thumb"       => intval($_POST['backend_servers_ct_dthumb']),
                    "ftp_host"             => $class_filter->clr_str($_POST['backend_servers_host']),
                    "ftp_port"             => $class_filter->clr_str($_POST['backend_servers_port']),
                    "ftp_transfer"         => $class_filter->clr_str($_POST[$backend_servers_transfer]),
                    "ftp_passive"          => intval($_POST[$backend_servers_passive]),
                    "ftp_username"         => $class_filter->clr_str($_POST['backend_servers_user']),
                    "ftp_password"         => $class_filter->clr_str($_POST['backend_servers_pass']),
                    "ftp_root"             => $class_filter->clr_str($_POST['backend_servers_root']),
                    "url"                  => $class_filter->clr_str($_POST['backend_servers_url']),
                    "lighttpd_url"         => $class_filter->clr_str($_POST['stream_url']),
                    "lighttpd_secdownload" => intval($_POST['stream_secure']),
                    "lighttpd_prefix"      => $class_filter->clr_str($_POST['stream_prefix']),
                    "lighttpd_key"         => $class_filter->clr_str($_POST['stream_key']),
                    "s3_bucketname"        => $class_filter->clr_str($_POST["backend_" . $st . "_bucketname"]),
                    "s3_accesskey"         => $class_filter->clr_str($_POST["backend_" . $st . "_accesskey"]),
                    "s3_secretkey"         => $class_filter->clr_str($_POST["backend_" . $st . "_secretkey"]),
                    "s3_region"            => $class_filter->clr_str($_POST[$backend_s3_region]),
                    "s3_fileperm"          => $class_filter->clr_str($_POST["backend_" . $st . "_fileperm"]),
                    "cf_enabled"           => intval($_POST[($server_type == 'ws' ? "backend_servers_ws_cf_enable" : "backend_servers_cf_enable")]),
                    "cf_dist_type"         => $class_filter->clr_str($_POST[($server_type == 'ws' ? "backend_servers_ws_cf_dist" : "backend_servers_cf_dist")]),
                    "cf_dist_price"        => $class_filter->clr_str($_POST[$backend_servers_cf_priceclass]),
                    "cf_signed_url"        => intval($_POST[($server_type == 'ws' ? "backend_servers_ws_surl_enable" : "backend_servers_surl_enable")]),
                    "cf_signed_expire"     => intval($_POST[($server_type == 'ws' ? "backend_servers_ws_surl_time" : "backend_servers_surl_time")]),
                    "cf_key_pair"          => $class_filter->clr_str($_POST[($server_type == 'ws' ? "backend_servers_ws_keypair_id" : "backend_servers_keypair_id")]),
                    "cf_key_file"          => $class_filter->clr_str($_POST[($server_type == 'ws' ? "backend_servers_ws_keypair_file" : "backend_servers_keypair_file")]),
                );

                $allowedFields  = array("frontend_global_name", "backend_servers_host", "backend_servers_port", $backend_servers_transfer, $backend_servers_passive, "backend_servers_user", "backend_servers_pass", "backend_servers_root", "backend_servers_url", "stream_url", "stream_secure", "stream_prefix", "stream_key", $backend_servers_type, "backend_" . $st . "_bucketname", $backend_s3_region, "backend_servers_cf_enable", "backend_servers_cf_dist", "backend_servers_surl_enable", "backend_servers_surl_time", "backend_servers_keypair_id", "backend_servers_keypair_file", "backend_" . $st . "_accesskey", "backend_" . $st . "_secretkey");
                $requiredFields = array("frontend_global_name", $backend_servers_priority, $backend_servers_limit, $backend_servers_hop, $backend_servers_type);

                if ($_POST[$backend_servers_type] == 'ftp') {
                    $requiredFields[] = 'backend_servers_host';
                    $requiredFields[] = 'backend_servers_port';
                    $requiredFields[] = $backend_servers_transfer;
                    $requiredFields[] = $backend_servers_passive;
                    $requiredFields[] = 'backend_servers_user';
                    $requiredFields[] = 'backend_servers_pass';
                    $requiredFields[] = 'backend_servers_root';
                    $requiredFields[] = 'backend_servers_url';
                } elseif ($server_type == 's3' or $server_type == 'ws') {
                    $requiredFields[] = 'backend_' . $st . '_accesskey';
                    $requiredFields[] = 'backend_' . $st . '_secretkey';
                    $requiredFields[] = 'backend_' . $st . '_bucketname';
                }
                break;
            case "public_categories":
                $entryid  = (int) $_POST['hc_id'];
                $postname = "this_file_type_" . $entryid;
                $subcateg = "sub_id_" . $entryid;
                $_array   = array(
                    "ct_index"    => (int) $_POST['categ_index'],
                    "ct_name"     => trim($class_filter->clr_str($_POST['frontend_global_name'])),
                    "ct_lang"     => serialize($_POST['frontend_global_translated']),
                    "ct_slug"     => trim($class_filter->clr_str($_POST['frontend_global_slug'])),
                    "ct_descr"    => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_desc']),
                    "ct_type"     => $class_filter->clr_str($_POST[$postname]),
                    "ct_featured" => (int) $_POST['categ_featured'],
                    "sub_id"      => (int) $_POST[$subcateg],
                    "ct_menu"     => (int) $_POST['categ_fe_menu'],
                    "ct_icon"     => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_categ_icon']),
                );
                $allowedFields  = array("frontend_global_index", "frontend_global_name", "frontend_global_slug", "backend_menu_members_entry1_sub2_entry_desc", "categ_fe_menu", "categ_featured", $postname, $subcateg);
                $requiredFields = array("frontend_global_index", "frontend_global_name", "frontend_global_slug", $postname);
                break;
            case "file_comments":
                $entryid  = (int) $_POST['hc_id'];
                $postname = "this_file_type_" . $entryid;
                $subcateg = "sub_id_" . $entryid;
                $_array   = array(
                    "c_body" => trim($class_filter->clr_str($_POST['c_body'])),
                );
                $allowedFields  = array("c_body");
                $requiredFields = $allowedFields;
                break;
            case "live_streaming":
                $entryid = (int) $_POST['hc_id'];
                $_array  = array(
                    "srv_name" => trim($class_filter->clr_str($_POST['frontend_global_name'])),
                    "srv_slug" => trim($class_filter->clr_str($_POST['frontend_global_slug'])),
                    "srv_host" => trim($class_filter->clr_str($_POST['backend_streaming_servers_host'])),
                );
                $allowedFields = array("frontend_global_name", "backend_streaming_servers_host");
                switch ($_GET['s']) {
                    case "backend-menu-entry14-sub1":$_array['srv_type'] = 'cast';
                        break;
                    case "backend-menu-entry14-sub2":$_array['srv_type'] = 'stream';
                        $_array['srv_port']                                  = (int) $_POST['backend_streaming_servers_port'];
                        $_array['srv_https']                                 = (int) $_POST['backend_streaming_servers_ssl'];
                        break;
                    case "backend-menu-entry14-sub3":$_array['srv_type'] = 'vod';
                        $_array['srv_port']                                  = (int) $_POST['backend_streaming_servers_port'];
                        $_array['srv_https']                                 = (int) $_POST['backend_streaming_servers_ssl'];
                        break;
                    case "backend-menu-entry14-sub5":$_array['srv_type'] = 'lbb';
                        $_array['srv_port']                                  = (int) $_POST['backend_streaming_servers_port'];
                        $_array['srv_https']                                 = (int) $_POST['backend_streaming_servers_ssl'];
                        break;
                    case "backend-menu-entry14-sub6":$_array['srv_type'] = 'lbs';
                        $_array['srv_port']                                  = (int) $_POST['backend_streaming_servers_port'];
                        $_array['srv_https']                                 = (int) $_POST['backend_streaming_servers_ssl'];
                        break;
                    case "backend-menu-entry14-sub7":$_array['srv_type'] = 'chat';
                        $_array['srv_port']                                  = (int) $_POST['backend_streaming_servers_port'];
                        $_array['srv_https']                                 = (int) $_POST['backend_streaming_servers_ssl'];
                        break;
                }
                $requiredFields = $allowedFields;
                break;
            case "live_streaming_token":
                $entryid     = (int) $_POST['hc_id'];
                $tk_currency = "backend_streaming_token_currency_" . $entryid;
                $_array      = array(
                    "tk_name"     => trim($class_filter->clr_str($_POST['backend_streaming_token_name'])),
                    "tk_slug"     => trim($class_filter->clr_str($_POST['backend_streaming_token_slug'])),
                    "tk_amount"   => trim($class_filter->clr_str($_POST['backend_streaming_token_amount'])),
                    "tk_price"    => trim($class_filter->clr_str($_POST['backend_streaming_token_price'])),
                    "tk_vat"      => trim($class_filter->clr_str($_POST['backend_streaming_token_vat'])),
                    "tk_currency" => trim($class_filter->clr_str($_POST[$tk_currency])),
                    "tk_shared"   => trim($class_filter->clr_str($_POST['backend_streaming_token_shared'])),
                );
                $allowedFields = array("backend_streaming_token_name", "backend_streaming_token_slug", "backend_streaming_token_amount", "backend_streaming_token_price", "backend_streaming_token_currency", "backend_streaming_token_shared", "backend_streaming_token_vat", $tk_currency);
                unset($allowedFields['backend_streaming_token_vat']);
                unset($allowedFields['backend_streaming_token_shared']);
                $requiredFields = $allowedFields;
                break;
            case "lang_types":
                $_array = array(
                    "lang_id"      => $class_filter->clr_str($_POST['backend_menu_entry1_sub10_lang_id']),
                    "lang_name"    => $class_filter->clr_str($_POST['frontend_global_name']),
                    "lang_flag"    => $class_filter->clr_str($_POST['frontend_global_flagicon']),
                    "lang_default" => intval($_POST['lang_default']),
                );
                $allowedFields  = array("backend_menu_entry1_sub10_lang_id", "frontend_global_name");
                $requiredFields = $allowedFields;
                break;
            case "ban_list":
                $_array = array(
                    "ban_ip"    => $class_filter->clr_str($_POST['backend_menu_members_entry3_sub5_ip']),
                    "ban_descr" => $class_filter->clr_str($_POST['frontend_global_note']),
                );
                if ($_GET['do'] == 'add') {
                    $_array['ban_active'] = 1;
                    $_array['ban_start']  = date("Y-m-d H:i:s");
                }
                $allowedFields  = array("frontend_global_note", "backend_menu_members_entry3_sub5_ip");
                $requiredFields = array("backend_menu_members_entry3_sub5_ip");
                break;
            case "adv_groups":
                $_array = array(
                    "adv_description" => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_desc']),
                    "adv_width"       => $class_filter->clr_str($_POST['backend_adv_text_width']),
                    "adv_height"      => $class_filter->clr_str($_POST['backend_adv_text_height']),
                    "adv_class"       => $class_filter->clr_str($_POST['backend_adv_text_class']),
                    "adv_style"       => $class_filter->clr_str($_POST['backend_adv_text_style']),
                    "adv_rotate"      => intval($_POST['backend_adv_text_rotate']),
                );
                $allowedFields  = array("backend_menu_members_entry1_sub2_entry_desc", "backend_adv_text_rotate", "adv_width", "adv_height");
                $requiredFields = array("adv_width", "adv_height");
                break;
            case "adv_banners":
                $entryid   = (int) $_POST['hc_id'];
                $adv_group = "adv_group_ids_" . $entryid;
                $adv_type  = "adv_type_" . $entryid;
                $_array    = array(
                    "adv_name"        => $class_filter->clr_str($_POST['frontend_global_name']),
                    "adv_type"        => $class_filter->clr_str($_POST[$adv_type]),
                    "adv_description" => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_desc']),
                    "adv_group"       => intval($_POST[$adv_group]),
                    "adv_code"        => $_POST['backend_adv_text_code'],
                );
                $allowedFields  = array("frontend_global_name", "backend_menu_members_entry1_sub2_entry_desc", $adv_type, $adv_group, "backend_adv_text_code");
                $requiredFields = array("frontend_global_name", $adv_type, $adv_group, "backend_adv_text_code");
                break;
            case "jw_files":
                $entryid = (int) $_POST['hc_id'];
                $jw_type = "jw_type_" . $entryid;

                $_array = array(
                    "db_name" => $class_filter->clr_str($_POST['frontend_global_name']),
                    "db_type" => $class_filter->clr_str($_POST[$jw_type]),
                    "db_code" => $_POST['backend_adv_file_code'],
                    "db_key"  => VUserinfo::generateRandomString(10),
                );
                $allowedFields  = array("frontend_global_name", $jw_type, "backend_adv_file_code");
                $requiredFields = array("frontend_global_name");
                break;
            case "vjs_ads":
                $entryid    = (int) $_POST['hc_id'];
                $vjs_client = "vjs_client_" . $entryid;
                $vjs_type   = "vjs_type_" . $entryid;
                $vjs_custom = "vjs_custom_" . $entryid;

                $_array = array(
                    "ad_name"       => $class_filter->clr_str($_POST['frontend_global_name']),
                    "ad_type"       => $class_filter->clr_str($_POST[$vjs_type]),
                    "ad_client"     => $class_filter->clr_str($_POST[$vjs_client]),
                    "ad_custom"     => $class_filter->clr_str($_POST[$vjs_custom]),
                    "ad_tag"        => $_POST['backend_adv_vjs_tag'],
                    "ad_custom_url" => $_POST['backend_adv_vjs_clickthrough'],
                    "ad_mobile"     => (int) $_POST['backend_adv_vjs_ad_mobile'],
                    "ad_skip"       => intval($_POST['backend_adv_vjs_skip']),
                    "ad_comp_div"   => intval($_POST['backend_adv_vjs_ad_comp']),
                    "ad_comp_id"    => $class_filter->clr_str($_POST['backend_adv_vjs_ad_comp_unit']),
                    "ad_comp_w"     => intval($_POST['backend_adv_vjs_ad_comp_width']),
                    "ad_comp_h"     => intval($_POST['backend_adv_vjs_ad_comp_height']),
                    "ad_key"        => VUserinfo::generateRandomString(10),
                );
                $allowedFields  = array("frontend_global_name", $vjs_type, $vjs_client, $vjs_custom, "backend_adv_vjs_tag", "backend_adv_vjs_ad_comp", "backend_adv_vjs_ad_comp_unit", "backend_adv_vjs_ad_comp_width", "backend_adv_vjs_ad_comp_height", "backend_adv_vjs_clickthrough", "backend_adv_vjs_skip");
                $requiredFields = array("frontend_global_name", $vjs_type, $vjs_client);

                break;
            case "jw_ads":
                $entryid     = (int) $_POST['hc_id'];
                $jw_client   = "jw_client_" . $entryid;
                $jw_format   = "jw_format_" . $entryid;
                $jw_server   = "jw_server_" . $entryid;
                $jw_file     = "jw_file_" . $entryid;
                $jw_position = "jw_position_" . $entryid;
                $jw_type     = "jw_type_" . $entryid;

                $_array = array(
                    "ad_name"        => $class_filter->clr_str($_POST['frontend_global_name']),
                    "ad_type"        => $class_filter->clr_str($_POST[$jw_type]),
                    "ad_position"    => $class_filter->clr_str($_POST[$jw_position]),
                    "ad_offset"      => intval($_POST['backend_adv_jw_offset']),
                    "ad_duration"    => floatval($_POST['backend_adv_jw_duration']),
                    "ad_client"      => $class_filter->clr_str($_POST[$jw_client]),
                    "ad_format"      => $class_filter->clr_str($_POST[$jw_format]),
                    "ad_server"      => $class_filter->clr_str($_POST[$jw_server]),
                    "ad_file"        => $class_filter->clr_str($_POST[$jw_file]),
                    "ad_width"       => $class_filter->clr_str($_POST['backend_adv_jw_width']),
                    "ad_height"      => $class_filter->clr_str($_POST['backend_adv_jw_height']),
                    "ad_bitrate"     => $class_filter->clr_str($_POST['backend_adv_jw_bitrate']),
                    "ad_tag"         => $_POST['backend_adv_jw_tag'],
                    "ad_comp_div"    => intval($_POST['backend_adv_jw_ad_comp']),
                    "ad_comp_id"     => $class_filter->clr_str($_POST['backend_adv_jw_ad_comp_id']),
                    "ad_comp_w"      => intval($_POST['backend_adv_jw_ad_comp_w']),
                    "ad_comp_h"      => intval($_POST['backend_adv_jw_ad_comp_h']),
                    "ad_click_track" => intval($_POST['backend_adv_jw_clicktracking']),
                    "ad_click_url"   => $_POST['backend_adv_jw_clickthrough'],
                    "ad_key"         => VUserinfo::generateRandomString(10),
                );
                $allowedFields  = array("frontend_global_name", $jw_type, $jw_position, "backend_adv_jw_offset", "backend_adv_jw_duration", $jw_client, $jw_format, $jw_server, $jw_file, "backend_adv_jw_tag", "backend_adv_jw_ad_comp", "backend_adv_jw_ad_comp_id", "backend_adv_jw_ad_comp_w", "backend_adv_jw_ad_comp_h", "backend_adv_jw_clicktracking", "backend_adv_jw_clickthrough");
                $requiredFields = array("frontend_global_name", $jw_type, $jw_position, "backend_adv_jw_offset", "backend_adv_jw_duration", $jw_client, $jw_format, $jw_server, $jw_file, "backend_adv_jw_tag");
                break;
            case "fp_ads":
                $entryid = (int) $_POST['hc_id'];
                $fp_file = "fp_file_" . $entryid;
                $_array  = array(
                    "ad_name"     => $class_filter->clr_str($_POST['frontend_global_name']),
                    "ad_cuepoint" => $class_filter->clr_str($_POST['backend_adv_fp_cuepoint']),
                    "ad_css"      => $class_filter->clr_str($_POST['backend_adv_fp_css']),
                    "ad_file"     => $class_filter->clr_str($_POST[$fp_file]),
                    "ad_key"      => VUserinfo::generateRandomString(10),
                );
                $allowedFields  = array("frontend_global_name", "backend_adv_fp_cuepoint", $fp_file, "backend_adv_fp_css");
                $requiredFields = array("frontend_global_name", "backend_adv_fp_cuepoint", $fp_file);
                break;
            case "channel_events":
                $_array = array(
                    "usr_id"     => intval($_SESSION['USER_ID']),
                    "e_datetime" => (intval($_POST['ev_years']) . '-' . $class_filter->clr_str($_POST['ev_months']) . '-' . $class_filter->clr_str($_POST['ev_days']) . ' ' . $class_filter->clr_str($_POST['ev_hours']) . ':' . $class_filter->clr_str($_POST['ev_minutes']) . ':00'),
                    "e_venue"    => $class_filter->clr_str($_POST['e_venue']),
                    "e_descr"    => $class_filter->clr_str($_POST['e_descr']),
                    "e_address"  => $class_filter->clr_str($_POST['e_address']),
                    "e_city"     => $class_filter->clr_str($_POST['e_city']),
                    "e_zip"      => $class_filter->clr_str($_POST['e_zip']),
                    "e_country"  => $class_filter->clr_str($_POST['e_country']),
                    "e_ticket"   => $class_filter->clr_str($_POST['e_ticket']));
                $allowedFields  = '';
                $requiredFields = '';
                break;
            case "channel_setup_tab":
                global $ch;
                $_array = array(
                    "ch_title" => $class_filter->clr_str($_POST['ch_title']),
                    "ch_tags"  => $class_filter->clr_str($_POST['ch_tags']),
                    "ch_descr" => $class_filter->clr_str($_POST['ch_descr']),
                    "ch_type"  => intval($_POST['ch_type']),
                    "ch_cfg"   => '');
                $allowedFields  = array("up_settings_title", "ch_type", "up_settings_tags");
                $requiredFields = '';

                if (intval($_POST['ch_type']) > 0 and intval($_POST['ch_type']) != $ch[0]['ch_type']) {
                    $_array['ch_custom_fields'] = '';
                }
                break;
            case "change_email":
                $_array         = '';
                $allowedFields  = array('account_email_address_new', 'account_email_address_pass');
                $requiredFields = $allowedFields;
                break;
            case "purge_account":
                $_array         = '';
                $allowedFields  = array('account_manage_curr_pass', 'account_manage_del_reason');
                $requiredFields = array('account_manage_curr_pass');
                break;
            case "change_password":
                global $db;

                $_array        = '';
                $allowedFields = array('account_manage_pass_verify', 'account_manage_pass_new', 'account_manage_pass_retype');

                $ui = $db->execute(sprintf("SELECT `oauth_password` FROM `db_accountuser` WHERE `usr_id`='%s' AND `oauth_uid` > '0' LIMIT 1;", (int) $_SESSION['USER_ID']));
                $up = $ui->fields['oauth_password'];

                if ($up == 0) {
                    unset($allowedFields['account_manage_pass_verify']);
                }

                $requiredFields = $allowedFields;
                break;
            case "signup":
                $_array        = '';
                $allowedFields = array('frontend_signin_username', 'frontend_signup_emailadd', 'frontend_signup_setpass', 'frontend_signup_setpassagain', 'frontend_global_submit');
                if ($cfg['paid_memberships'] == 1) {
                    $allowedFields[] = 'frontend_membership_type_sel';
                }
                $requiredFields = $allowedFields;
                break;

            case "channel_types":
                $_array = array(
                    "db_name"       => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_name']),
                    "db_desc"       => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_desc']),
                    "db_styles"     => $class_filter->clr_str($_POST['backend_menu_members_entry2_sub2_style']),
                    "db_influences" => (intval($_POST['backend_menu_members_entry2_sub2_infl']) == 1 ? 1 : 0));
                $allowedFields  = array('backend_menu_members_entry1_sub2_entry_name', 'backend_menu_members_entry1_sub2_entry_desc', 'backend_menu_members_entry2_sub2_style');
                $requiredFields = array('backend_menu_members_entry1_sub2_entry_name');
                break;

            case "subscription_types":
                global $language;

                $price_array = explode(',', $language['supported_currency_names']);
                $unit_array  = explode(',', $language['supported_currency_codes']);

                $entryid       = (int) $_POST['hc_id'];
                $priceunitname = "pk_priceunitname_" . $entryid;

                $_k = array_search($_POST[$priceunitname], $price_array);
                $_s = $unit_array[$_k];

                $backend_menu_members_entry1_sub2_entry_period = "backend_menu_members_entry1_sub2_entry_period_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_price  = "backend_menu_members_entry1_sub2_entry_price_" . $entryid;
                $frontend_global_days                          = "frontend_global_days_" . $entryid;

                $pk_period = $_POST[$backend_menu_members_entry1_sub2_entry_period] == 0 ? $class_filter->clr_str($_POST[$frontend_global_days]) : $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_period]);
                $_array    = array(
                    "pk_name"          => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_name']),
                    "pk_descr"         => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_desc']),
                    "pk_price"         => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_price]),
                    "pk_priceunitname" => $class_filter->clr_str($_POST[$priceunitname]),
                    "pk_priceunit"     => $_s,
                    "pk_period"        => $pk_period);

                $allowedFields = array('backend_menu_members_entry1_sub2_entry_name',
                    'backend_menu_members_entry1_sub2_entry_desc',
                    $backend_menu_members_entry1_sub2_entry_price,
                    $priceunitname,
                    $backend_menu_members_entry1_sub2_entry_period,
                    $frontend_global_days);

                $requiredFields = array('backend_menu_members_entry1_sub2_entry_name',
                    $backend_menu_members_entry1_sub2_entry_price,
                    $priceunitname,
                    $backend_menu_members_entry1_sub2_entry_period);

                if ($_POST[$backend_menu_members_entry1_sub2_entry_period] == 0) {
                    $requiredFields[] = $frontend_global_days;
                }
                break;

            case "membership_types":
                global $language;

                $price_array = explode(',', $language['supported_currency_names']);
                $unit_array  = explode(',', $language['supported_currency_codes']);

                $entryid       = (int) $_POST['hc_id'];
                $priceunitname = "pk_priceunitname_" . $entryid;

                $_k = array_search($_POST[$priceunitname], $price_array);
                $_s = $unit_array[$_k];

                $backend_menu_members_entry1_sub2_entry_period = "backend_menu_members_entry1_sub2_entry_period_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_price  = "backend_menu_members_entry1_sub2_entry_price_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_space  = "backend_menu_members_entry1_sub2_entry_space_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_bw     = "backend_menu_members_entry1_sub2_entry_bw_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_vlimit = "backend_menu_members_entry1_sub2_entry_vlimit_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_slimit = "backend_menu_members_entry1_sub2_entry_slimit_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_ilimit = "backend_menu_members_entry1_sub2_entry_ilimit_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_alimit = "backend_menu_members_entry1_sub2_entry_alimit_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_dlimit = "backend_menu_members_entry1_sub2_entry_dlimit_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_llimit = "backend_menu_members_entry1_sub2_entry_llimit_" . $entryid;
                $backend_menu_members_entry1_sub2_entry_blimit = "backend_menu_members_entry1_sub2_entry_blimit_" . $entryid;
                $frontend_global_days                          = "frontend_global_days_" . $entryid;

                $pk_period = $_POST[$backend_menu_members_entry1_sub2_entry_period] == 0 ? $class_filter->clr_str($_POST[$frontend_global_days]) : $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_period]);
                $_array    = array(
                    "pk_name"          => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_name']),
                    "pk_descr"         => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_desc']),
                    "pk_space"         => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_space]),
                    "pk_bw"            => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_bw]),
                    "pk_price"         => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_price]),
                    "pk_priceunit"     => $_s,
                    "pk_priceunitname" => $class_filter->clr_str($_POST[$priceunitname]),
                    "pk_alimit"        => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_alimit]),
                    "pk_ilimit"        => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_ilimit]),
                    "pk_vlimit"        => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_vlimit]),
                    "pk_slimit"        => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_slimit]),
                    "pk_dlimit"        => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_dlimit]),
                    "pk_llimit"        => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_llimit]),
                    "pk_blimit"        => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub2_entry_blimit]),
                    "pk_period"        => $pk_period);

                $allowedFields = array('backend_menu_members_entry1_sub2_entry_name',
                    'backend_menu_members_entry1_sub2_entry_desc',
                    $backend_menu_members_entry1_sub2_entry_space,
                    $backend_menu_members_entry1_sub2_entry_bw,
                    $backend_menu_members_entry1_sub2_entry_price,
                    $priceunitname,
                    $backend_menu_members_entry1_sub2_entry_alimit,
                    $backend_menu_members_entry1_sub2_entry_ilimit,
                    $backend_menu_members_entry1_sub2_entry_vlimit,
                    $backend_menu_members_entry1_sub2_entry_slimit,
                    $backend_menu_members_entry1_sub2_entry_dlimit,
                    $backend_menu_members_entry1_sub2_entry_llimit,
                    $backend_menu_members_entry1_sub2_entry_blimit,
                    $backend_menu_members_entry1_sub2_entry_period,
                    $frontend_global_days);

                $requiredFields = array('backend_menu_members_entry1_sub2_entry_name',
                    $backend_menu_members_entry1_sub2_entry_space,
                    $backend_menu_members_entry1_sub2_entry_bw,
                    $backend_menu_members_entry1_sub2_entry_price,
                    $priceunitname,
                    $backend_menu_members_entry1_sub2_entry_alimit,
                    $backend_menu_members_entry1_sub2_entry_ilimit,
                    $backend_menu_members_entry1_sub2_entry_vlimit,
                    $backend_menu_members_entry1_sub2_entry_slimit,
                    $backend_menu_members_entry1_sub2_entry_dlimit,
                    $backend_menu_members_entry1_sub2_entry_llimit,
                    $backend_menu_members_entry1_sub2_entry_blimit,
                    $backend_menu_members_entry1_sub2_entry_period);

                if ($_POST[$backend_menu_members_entry1_sub2_entry_period] == 0) {
                    $requiredFields[] = $frontend_global_days;
                }
                break;

            case "discount_codes":
                $entryid                                       = (int) $_POST['hc_id'];
                $backend_menu_members_entry1_sub3_entry_amount = "backend_menu_members_entry1_sub3_entry_amount_" . $entryid;

                $_array = array(
                    "dc_code"   => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub3_entry_name']),
                    "dc_descr"  => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_desc']),
                    "dc_amount" => $class_filter->clr_str($_POST[$backend_menu_members_entry1_sub3_entry_amount]),
                    "dc_date"   => date("Y-m-d H:i:s"));
                $allowedFields  = array('backend_menu_members_entry1_sub3_entry_name', 'backend_menu_members_entry1_sub2_entry_desc', $backend_menu_members_entry1_sub3_entry_amount);
                $requiredFields = array('backend_menu_members_entry1_sub3_entry_name', $backend_menu_members_entry1_sub3_entry_amount);
                break;

            case "compose_message":
                $_array = array(
                    "msg_subj" => $class_filter->clr_str($_POST['msg_label_subj']),
                    "msg_body" => $class_filter->clr_str($_POST['msg_label_message']),
                    "msg_from" => intval($_SESSION['USER_ID']),
                    "msg_to"   => VUserinfo::getUserID($class_filter->clr_str($_POST['msg_label_to'])),
                    "msg_date" => date("Y-m-d H:i:s"));

                if ($cfg['message_attachments'] == 1) {
                    $mod_array = array("live" => "live", "video" => "video", "short" => "short", "image" => "image", "audio" => "audio", "document" => "doc", "blog" => "blog");

                    foreach ($mod_array as $db_key => $mod) {
                        if ($cfg[$db_key . "_module"] == 1) {
                            $mod_key                          = $class_filter->clr_str($_POST[$mod . "_attachlist"]);
                            $mod_key                          = $mod_key == '' ? 0 : $mod_key;
                            $_array["msg_" . $mod . "_attch"] = (strlen($mod_key) > 10) ? substr($mod_key, 1) : $mod_key;
                        }
                    }
                }

                $allowedFields  = array('msg_label_to', 'msg_label_subj', 'msg_label_message');
                $requiredFields = $allowedFields;
                break;

            case "edit_file":
                $_array = array(
                    "file_title"       => trim($class_filter->clr_str($_POST['files_text_file_title'])),
                    "file_description" => $class_filter->clr_str($_POST['file_descr']),
                    "file_tags"        => trim($class_filter->clr_str($_POST['file_tags'])),
                    "file_category"    => intval($_POST[(isset($_GET['u']) ? 'file_category_sel_u' : 'file_category_sel')]),
                    "privacy"          => $class_filter->clr_str($_POST['option_privacy']),
                    "comments"         => $class_filter->clr_str($_POST['option_comments']),
                    "comment_votes"    => intval($_POST['option_votes']),
                    "comment_spam"     => intval($_POST['option_spam']),
                    "rating"           => intval($_POST['option_rating']),
                    "responding"       => $class_filter->clr_str($_POST['option_responses']),
                    "embedding"        => intval($_POST['option_embed']),
                    "social"           => intval($_POST['option_social']),
                );
                if (isset($_GET['l'])) {
                    $err = VUseraccount::checkPerm('upload', 'l');
                    if ($err != '') {
                        $_array['stream_key']  = '';
                        $_array['stream_chat'] = 0;
                        $_array['stream_vod']  = 0;
                    } else {
                        $_array['stream_chat'] = $class_filter->clr_str($_POST['option_chat']);
                        $_array['stream_vod']  = $class_filter->clr_str($_POST['option_vod']);
                    }
                }
                $allowedFields  = array('files_text_file_title', 'file_descr', 'file_tags', 'option_privacy', 'option_comments', 'option_votes', 'option_rating', 'option_responses', 'option_embedding', 'option_social');
                $requiredFields = array('files_text_file_title', 'option_privacy', 'option_comments', 'option_votes', 'option_rating', 'option_responses', 'option_embedding', 'option_social');

                if (isset($_GET['l'])) {
                    $allowedFields[] = 'option_chat';
                    $allowedFields[] = 'option_vod';
                }
                break;

            case "new_user":
                $entryid          = (int) $_POST['hc_id'];
                $account_new_pack = "account_new_pack_" . $entryid;
                $_array           = array(
                    "usr_user"          => (($cfg['username_format'] == 'strict' and VUserinfo::isValidUsername($_POST['account_new_username'])) ? $class_filter->clr_str($_POST['account_new_username']) : ($cfg['username_format'] == 'loose' and VUserinfo::isValidUsername($_POST['account_new_username'])) ? VUserinfo::clearString($_POST['account_new_username']) : null),
                    "usr_password"      => $_POST['account_new_password'],
                    "usr_password_conf" => $_POST['account_new_password_conf'],
                    "usr_email"         => $class_filter->clr_str($_POST['frontend_global_email']),
                );

                if ($cfg['paid_memberships'] == 1) {
                    $_array['pk_id'] = $class_filter->clr_str($_POST[$account_new_pack]);
                }

                $allowedFields = array('account_new_username', 'account_new_password', 'account_new_password_conf', 'frontend_global_email');

                if ($cfg['paid_memberships'] == 1) {
                    $allowedFields[] = $account_new_pack;
                }

                $requiredFields = $allowedFields;
                break;
        }

        return array($_array, $allowedFields, $requiredFields);
    }
}
