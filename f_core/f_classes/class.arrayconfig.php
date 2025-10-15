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

class VArrayConfig
{
    public function getCfg()
    {
        $cfg_array = self::cfgSection();
        foreach ($cfg_array as $key => $post_field) {$getcfg .= $key . ',';}
        return substr($getcfg, 0, -1);
    }
    /* sanitize section fields */
    public function cfgSection()
    {
        global $class_filter;

        switch ($_GET['s']) {
            case "account-menu-entry2": //profile, about
                global $language;

                $url = null;
                if ($_POST['account_profile_about_website']) {
                    $url = $class_filter->clr_str($_POST['account_profile_about_website']);
                    if (strpos($url, "http://") === 0 || strpos($url, "https://") === 0) {
                    } else {
                        $url = 'https://' . $url;
                    }
                }
                $theme    = $_POST['default_theme'] == 'dark' ? 'dark' : 'light';
                $cfg_vars = array(
                    "usr_description" => $class_filter->clr_str($_POST['account_profile_about_describe']),
                    "usr_website"     => $url,
                    "usr_phone"       => $class_filter->clr_str($_POST['account_profile_about_phone']),
                    "usr_fax"         => $class_filter->clr_str($_POST['account_profile_about_fax']),
                    "usr_fname"       => $class_filter->clr_str($_POST['account_profile_personal_firstname']),
                    "usr_lname"       => $class_filter->clr_str($_POST['account_profile_personal_lastname']),
                    "usr_dname"       => $class_filter->clr_str($_POST['account_profile_about_displayname']),
                    "usr_theme"       => $theme,
                    "usr_birthday"    => $class_filter->clr_str($_POST['account_profile_bdate_y']) . '-' . $class_filter->clr_str($_POST['account_profile_bdate_m']) . '-' . $class_filter->clr_str($_POST['account_profile_bdate_d']),
                    "usr_gender"      => $class_filter->clr_str($_POST['account_profile_personal_gender']),
                    "usr_relation"    => $class_filter->clr_str($_POST['account_profile_personal_rel']),
                    "usr_showage"     => ($_POST['account_profile_personal_age'] == $language['account.profile.age.array'][0] ? 1 : 0),
                    "usr_town"        => $class_filter->clr_str($_POST['account_profile_location_town']),
                    "usr_city"        => $class_filter->clr_str($_POST['account_profile_location_city']),
                    "usr_zip"         => $class_filter->clr_str($_POST['account_profile_location_zip']),
                    "usr_country"     => $class_filter->clr_str($_POST['account_profile_location_country']),
                    "usr_occupations" => $class_filter->clr_str($_POST['account_profile_job_occup']),
                    "usr_companies"   => $class_filter->clr_str($_POST['account_profile_job_companies']),
                    "usr_schools"     => $class_filter->clr_str($_POST['account_profile_education_school']),
                    "usr_interests"   => $class_filter->clr_str($_POST['account_profile_interests']),
                    "usr_movies"      => $class_filter->clr_str($_POST['account_profile_interests_movies']),
                    "usr_music"       => $class_filter->clr_str($_POST['account_profile_interests_music']),
                    "usr_books"       => $class_filter->clr_str($_POST['account_profile_interests_books']),
                );
                break;

            case "account-menu-entry4": //profile, change email
                global $cfg;
                $cfg_vars = array(
                    "usr_email"            => $class_filter->clr_str($_POST['account_email_address_new']),
                    "usr_password"         => $_POST['account_email_address_pass'],
                    "usr_captcha"          => $class_filter->clr_str($_POST['g-recaptcha-response']),
                    "usr_mail_filecomment" => (intval($_POST['usr_mail_filecomment']) == 1 ? 1 : 0),
                    "usr_mail_chancomment" => (intval($_POST['usr_mail_chancomment']) == 1 ? 1 : 0),
                    "usr_mail_privmessage" => (intval($_POST['usr_mail_privmessage']) == 1 ? 1 : 0),
                    "usr_mail_friendinv"   => (intval($_POST['usr_mail_friendinv']) == 1 ? 1 : 0),
                    "usr_mail_chansub"     => (intval($_POST['usr_mail_chansub']) == 1 ? 1 : 0),
                    "usr_mail_chanfollow"  => (intval($_POST['usr_mail_chanfollow']) == 1 ? 1 : 0),
                    "usr_weekupdates"      => intval($_POST['send_updates']),
                    "usr_emailextras"      => (intval($_POST['occasional_updates']) == 1 ? 1 : 0),
                    "usr_sub_email"        => ($cfg['user_subscriptions'] == 1 ? $class_filter->clr_str($_POST['account_payout_address_sub']) : null),
                    // "affiliate_email"      => ((isset($_SESSION['USER_AFFILIATE']) and $_SESSION['USER_AFFILIATE'] == 1) ? $class_filter->clr_str($_POST['account_payout_address_aff']) : null),
                );
                break;

            case "account-menu-entry5": //profile, activity sharing
                global $cfg;
                $cfg_vars = array(
                    "share_upload"      => (intval($_POST['share_upload']) == 1 ? 1 : 0),
                    "share_rating"      => (intval($_POST['share_rating']) == 1 ? 1 : 0),
                    "share_filecomment" => (intval($_POST['share_filecomment']) == 1 ? 1 : 0),
                    "share_fav"         => (intval($_POST['share_fav']) == 1 ? 1 : 0),
                    "share_responding"  => (intval($_POST['share_responding']) == 1 ? 1 : 0),
                    "share_subscribing" => (intval($_POST['share_subscribing']) == 1 ? 1 : 0),
                    "share_following"   => (intval($_POST['share_following']) == 1 ? 1 : 0),
                );
                $cfg_check = array("file_favorites" => "share_fav", "file_rating" => "share_rating", "file_comments" => "share_filecomment", "user_follows" => "share_following", "user_subscriptions" => "share_subscribing", "file_responses" => "share_responding");
                foreach ($cfg_check as $key => $val) {
                    if ($key == 'file_comments') {
                        if ($cfg[$key] == 0 and $cfg['channel_comments'] == 0) {
                            unset($cfg_vars[$val]);
                        }

                    } else {
                        if ($cfg[$key] == 0) {
                            unset($cfg_vars[$val]);
                        }

                    }
                }
                if (($cfg['short_uploads'] == 0 or $cfg['short_module'] == 0) and ($cfg['video_uploads'] == 0 or $cfg['video_module'] == 0) and ($cfg['live_uploads'] == 0 or $cfg['live_module'] == 0) and ($cfg['image_uploads'] == 0 or $cfg['image_module'] == 0) and ($cfg['audio_uploads'] == 0 or $cfg['audio_module'] == 0) and ($cfg['document_uploads'] == 0 or $cfg['document_module'] == 0)) {
                    unset($cfg_vars['share_upload']);
                }

                break;

            case "account-menu-entry6": //profile, change password
                $cfg_vars = array(
                    "usr_oldpass"    => $_POST['account_manage_pass_verify'],
                    "usr_newpass"    => $_POST['account_manage_pass_new'],
                    "usr_retypepass" => $_POST['account_manage_pass_retype'],
                    "usr_delpass"    => $_POST['account_manage_curr_pass'],
                    "usr_del_reason" => $class_filter->clr_str($_POST['account_manage_del_reason']),
                );
                break;

            case 'backend-menu-entry2-sub1': //global meta data
                $cfg_vars = array(
                    "website_shortname"    => $class_filter->clr_str($_POST['backend_menu_entry2_sub4_shortname']),
                    "head_title"           => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_headtitle']),
                    "metaname_description" => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_metadesc']),
                    "metaname_keywords"    => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_metakeywords']),
                    "google_analytics"     => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_google_an']),
                    // "google_analytics_api"  => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_google_an_api']),
                    // "google_analytics_view" => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_google_an_view']),
                    // "google_analytics_maps" => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_google_an_maps']),
                    "google_webmaster"     => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_google_web']),
                    "custom_tagline"       => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_tagline']),
                    "yahoo_explorer"       => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_yahoo']),
                    "bing_validate"        => $class_filter->clr_str($_POST['backend_menu_entry2_sub1_bing']),
                    "social_media_links"   => serialize($_POST['sml']),
                );
                break;

            case 'backend-menu-entry2-sub4': //general behavior
                $cfg_vars = array(
                    "website_ip_based_access" => intval($_POST['backend_menu_entry2_sub4_IPaccess']),
                    "list_ip_access"          => $class_filter->clr_str($_POST['backend_menu_entry2_sub4_IPlist_path']),
                    "website_offline_mode"    => intval($_POST['backend_menu_entry2_sub4_offmode']),
                    "offline_mode_settings"   => serialize($_POST['sml']),
                    "offline_mode_until"      => $class_filter->clr_str($_POST['backend_menu_entry2_sub4_offuntil']),
                );
                $cfg_vars = (!file_exists($cfg_vars['list_ip_access'])) ? VArraySection::arrayRemoveKey($cfg_vars, "list_ip_access") : $cfg_vars;
                $cfg_vars = (!file_exists($cfg_vars['list_ip_backend'])) ? VArraySection::arrayRemoveKey($cfg_vars, "list_ip_backend") : $cfg_vars;
                break;

            case 'backend-menu-entry2-sub18': //sign in/log in
                $cfg_vars = array(
                    "fb_auth"                 => intval($_POST['backend_menu_entry1_sub3_fb_module']),
                    "fb_app_id"               => $class_filter->clr_str($_POST['backend_menu_entry1_sub3_fb_appid']),
                    "fb_app_secret"           => $class_filter->clr_str($_POST['backend_menu_entry1_sub3_fb_secret']),
                    "gp_auth"                 => intval($_POST['backend_menu_entry1_sub3_gp_module']),
                    "gp_app_id"               => $class_filter->clr_str($_POST['backend_menu_entry1_sub3_gp_appid']),
                    "gp_app_secret"           => $class_filter->clr_str($_POST['backend_menu_entry1_sub3_gp_secret']),
                    "backend_signin_section"  => intval($_POST['backend_menu_entry1_sub3_be_signin']),
                    "backend_signin_count"    => intval($_POST['backend_menu_entry1_sub3_be_signin_ct']),
                    "backend_remember"        => intval($_POST['backend_menu_entry1_sub3_be_signin_rem']),
                    "frontend_signin_section" => intval($_POST['backend_menu_entry1_sub3_fe_signin']),
                    "frontend_signin_count"   => intval($_POST['backend_menu_entry1_sub3_fe_signin_ct']),
                    "login_remember"          => intval($_POST['backend_menu_entry1_sub3_fe_signin_rem']),
                );
                break;

            case 'backend-menu-entry2-sub17': //sign up/registration
                $cfg_vars = array(
                    "global_signup"                => intval($_POST['backend_menu_section_access']),
                    "signup_ip_access"             => intval($_POST['backend_menu_section_IPaccess']),
                    "signup_domain_restriction"    => intval($_POST['backend_menu_entry1_sub1_mailres']),
                    "disabled_signup_message"      => $class_filter->clr_str($_POST['backend_menu_close_message']),
                    "list_ip_signup"               => $class_filter->clr_str($_POST['backend_menu_section_IPlist_path']),
                    "list_email_domains"           => $class_filter->clr_str($_POST['backend_menu_entry1_sub1_maillist_path']),
                    "account_approval"             => intval($_POST['backend_menu_entry1_sub2_fe_act_approval']),
                    "account_email_verification"   => intval($_POST['backend_menu_entry1_sub2_fe_act_mver']),
                    "list_reserved_users"          => $class_filter->clr_str($_POST['backend_menu_entry1_sub1_userlist_path']),
                    "username_format"              => 'strict', //$class_filter->clr_str($_POST['backend_menu_entry1_sub1_uformat']),
                    "username_format_dott"         => intval($_POST['backend_menu_entry1_sub1_uformat_t3']),
                    "username_format_dash"         => intval($_POST['backend_menu_entry1_sub1_uformat_t4']),
                    "username_format_underscore"   => intval($_POST['backend_menu_entry1_sub1_uformat_t5']),
                    "signup_min_username"          => intval($_POST['backend_menu_entry1_sub1_userlen_min']),
                    "signup_max_username"          => intval($_POST['backend_menu_entry1_sub1_userlen_max']),
                    "signup_min_password"          => intval($_POST['backend_menu_entry1_sub1_passlen_min']),
                    "signup_max_password"          => intval($_POST['backend_menu_entry1_sub1_passlen_max']),
                    "signup_username_availability" => intval($_POST['backend_menu_entry1_sub1_uavail']),
                    "signup_password_meter"        => intval($_POST['backend_menu_entry1_sub1_pmeter']),
                );
                $cfg_vars = (!file_exists($cfg_vars['list_ip_signup'])) ? VArraySection::arrayRemoveKey($cfg_vars, "list_ip_signup") : $cfg_vars;
                $cfg_vars = (!file_exists($cfg_vars['list_email_domains'])) ? VArraySection::arrayRemoveKey($cfg_vars, "list_email_domains") : $cfg_vars;
                $cfg_vars = (!file_exists($cfg_vars['list_reserved_users'])) ? VArraySection::arrayRemoveKey($cfg_vars, "list_reserved_users") : $cfg_vars;
                break;

            case 'backend-menu-entry2-sub19': //username/password recovery
                $cfg_vars = array(
                    "backend_password_recovery"      => intval($_POST['backend_menu_entry1_sub2_be_passrec']),
                    "backend_recovery_link_lifetime" => intval($_POST['backend_menu_entry1_sub2_be_passrec_link']),
                    "backend_username_recovery"      => intval($_POST['backend_menu_entry1_sub2_be_userrec']),
                    "allow_password_recovery"        => intval($_POST['backend_menu_entry1_sub2_fe_passrec']),
                    "recovery_link_lifetime"         => intval($_POST['backend_menu_entry1_sub2_fe_passrec_link']),
                    "allow_username_recovery"        => intval($_POST['backend_menu_entry1_sub2_fe_userrec']),
                );
                break;

            case 'backend-menu-entry2-sub15': //internal messaging
                $cfg_vars = array(
                    "internal_messaging"    => intval($_POST['backend_menu_entry1_sub4_messaging_sys']),
                    "allow_self_messaging"  => intval($_POST['backend_menu_entry1_sub4_messaging_self']),
                    "allow_multi_messaging" => intval($_POST['backend_menu_entry1_sub4_messaging_multi']),
                    "multi_messaging_limit" => (intval($_POST['backend_menu_entry1_sub4_messaging_limit']) > 0 ? intval($_POST['backend_menu_entry1_sub4_messaging_limit']) : 0),
                    "message_attachments"   => intval($_POST['backend_menu_entry1_sub4_messaging_attch']),
                    "custom_labels"         => intval($_POST['backend_menu_entry1_sub4_messaging_labels']),
                    "message_count"         => intval($_POST['backend_menu_entry1_sub4_messaging_counts']),
                    "user_friends"          => intval($_POST['backend_menu_entry1_sub4_messaging_friends']),
                    "user_blocking"         => intval($_POST['backend_menu_entry1_sub4_messaging_blocked']),
                    "approve_friends"       => intval($_POST['backend_menu_entry1_sub4_messaging_approval']),
                );
                break;

            case 'backend-menu-entry2-sub20': //captcha configuration
                $cfg_vars = array(
                    "recaptcha_site_key"                => $class_filter->clr_str($_POST['backend_menu_entry1_sub1_recaptcha_key']),
                    "recaptcha_secret_key"              => $class_filter->clr_str($_POST['backend_menu_entry1_sub1_recaptcha_secret']),
                    "backend_password_recovery_captcha" => intval($_POST['backend_menu_entry1_sub2_be_passrec_ver']),
                    "backend_username_recovery_captcha" => intval($_POST['backend_menu_entry1_sub2_be_userrec_ver']),
                    "signup_captcha"                    => intval($_POST['backend_menu_entry1_sub1_captcha']),
                    "signin_captcha"                    => intval($_POST['backend_menu_entry1_sub1_captcha_l']),
                    "signin_captcha_be"                 => intval($_POST['backend_menu_entry1_sub1_captcha_l_b']),
                    "password_recovery_captcha"         => intval($_POST['backend_menu_entry1_sub2_fe_passrec_ver']),
                    "username_recovery_captcha"         => intval($_POST['backend_menu_entry1_sub2_fe_userrec_ver']),
                    "email_change_captcha"              => intval($_POST['backend_menu_entry1_sub5_em_captcha']),
                );
                break;

            case 'backend-menu-entry2-sub12': //file upload
                $cfg_vars = array(
                    "video_uploads"           => intval($_POST['backend_menu_entry1_sub7_file_video']),
                    "video_file_types"        => $class_filter->clr_str($_POST['backend_menu_entry1_sub7_file_video_types']),
                    "video_limit"             => intval($_POST['backend_menu_entry1_sub7_file_video_size']),
                    "short_uploads"           => intval($_POST['backend_menu_entry1_sub7_file_short']),
                    "short_file_types"        => $class_filter->clr_str($_POST['backend_menu_entry1_sub7_file_short_types']),
                    "short_limit"             => intval($_POST['backend_menu_entry1_sub7_file_short_size']),
                    "image_uploads"           => intval($_POST['backend_menu_entry1_sub7_file_image']),
                    "image_file_types"        => $class_filter->clr_str($_POST['backend_menu_entry1_sub7_file_image_types']),
                    "image_limit"             => intval($_POST['backend_menu_entry1_sub7_file_image_size']),
                    "audio_uploads"           => intval($_POST['backend_menu_entry1_sub7_file_audio']),
                    "audio_file_types"        => $class_filter->clr_str($_POST['backend_menu_entry1_sub7_file_audio_types']),
                    "audio_limit"             => intval($_POST['backend_menu_entry1_sub7_file_audio_size']),
                    "document_uploads"        => intval($_POST['backend_menu_entry1_sub7_file_doc']),
                    "document_file_types"     => $class_filter->clr_str($_POST['backend_menu_entry1_sub7_file_doc_types']),
                    "document_limit"          => intval($_POST['backend_menu_entry1_sub7_file_doc_size']),
                    "conversion_source_video" => intval($_POST['conversion_source_video']),
                    "conversion_source_short" => intval($_POST['conversion_source_short']),
                    "conversion_source_image" => intval($_POST['conversion_source_image']),
                    "conversion_source_audio" => intval($_POST['conversion_source_audio']),
                    "conversion_source_doc"   => intval($_POST['conversion_source_doc']),
                    "multiple_file_uploads"   => intval($_POST['backend_menu_entry1_sub7_file_multi']),
                );
                break;

            case 'backend-menu-entry2-sub13': //file permissions/settings
                $cfg_vars = array(
                    "file_approval"           => intval($_POST['backend_menu_entry1_sub7_file_opt_approve']),
                    "file_views"              => intval($_POST['backend_menu_entry1_sub7_file_opt_views']),
                    "file_deleting"           => intval($_POST['backend_menu_entry1_sub7_file_opt_del']),
                    "file_delete_method"      => intval($_POST['backend_menu_entry1_sub7_file_opt_del_method']),
                    "file_favorites"          => intval($_POST['backend_menu_entry1_sub7_file_opt_fav']),
                    "file_playlists"          => intval($_POST['backend_menu_entry1_sub7_file_opt_pl']),
                    "file_promo"              => intval($_POST['backend_menu_entry1_sub7_file_opt_promo']),
                    "file_history"            => intval($_POST['backend_menu_entry1_sub7_file_opt_history']),
                    "file_watchlist"          => intval($_POST['backend_menu_entry1_sub7_file_opt_watchlist']),
                    "file_privacy"            => intval($_POST['backend_menu_entry1_sub7_file_opt_privacy']),
                    "file_comments"           => intval($_POST['backend_menu_entry1_sub7_file_opt_comm']),
                    "file_comment_votes"      => intval($_POST['backend_menu_entry1_sub7_file_opt_vote']),
                    "file_comment_spam"       => intval($_POST['backend_menu_entry1_sub7_file_opt_spam']),
                    "fcc_limit"               => intval($_POST['backend_menu_entry1_sub6_comments_cons_f']),
                    "file_comment_min_length" => intval($_POST['backend_menu_entry1_sub6_comments_length_f_min']),
                    "file_comment_max_length" => intval($_POST['backend_menu_entry1_sub6_comments_length_f_max']),
                    "file_rating"             => intval($_POST['backend_menu_entry1_sub7_file_opt_rate']),
                    "file_responses"          => intval($_POST['backend_menu_entry1_sub7_file_opt_resp']),
                    "file_downloads"          => intval($_POST['backend_menu_entry1_sub7_file_opt_down']),
                    "file_download_s1"        => intval($_POST['dl_1']),
                    "file_download_s2"        => intval($_POST['dl_2']),
                    "file_download_s3"        => intval($_POST['dl_3']),
                    "file_download_s4"        => intval($_POST['dl_4']),
                    "file_download_reg"       => intval($_POST['dl_reg']),
                    "file_embedding"          => intval($_POST['backend_menu_entry1_sub7_file_opt_embed']),
                    "file_flagging"           => intval($_POST['backend_menu_entry1_sub7_file_opt_flag']),
                    "file_social_sharing"     => intval($_POST['backend_menu_entry1_sub7_file_opt_social']),
                    "file_email_sharing"      => intval($_POST['backend_menu_entry1_sub7_file_opt_file']),
                    "file_permalink_sharing"  => intval($_POST['backend_menu_entry1_sub7_file_opt_perma']),
                );
                break;

            case 'backend-menu-entry4-sub1': //subscription system - general setup
                global $cfg;
                $cfg_vars = array(
                    "paid_memberships"          => intval($_POST['backend_menu_members_entry1_sub1_paid']),
                    "user_subscriptions"        => intval($_POST['backend_menu_members_entry1_sub1_subs']),
                    "sub_shared_revenue"        => intval($_POST['backend_menu_members_entry1_sub1_rev']),
                    "sub_threshold"             => intval($_POST['backend_menu_members_entry1_sub1_threshold']),
                    "token_threshold"           => intval($_POST['backend_menu_members_entry1_tok1_threshold']),
                    "partner_requirements_min"  => (int) $_POST['backend_menu_pt_requirements_min'],
                    "partner_requirements_type" => (int) $_POST['backend_menu_pt_requirements_type'],
                    "discount_codes"            => intval($_POST['backend_menu_members_entry1_sub3']),
                    "paypal_email"              => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub1_pp_mail']),
                    "paypal_logging"            => intval($_POST['backend_menu_members_entry1_sub1_pplog']),
                    "paypal_test"               => intval($_POST['backend_menu_members_entry1_sub1_pp_test']),
                    "paypal_test_email"         => $class_filter->clr_str($_POST['backend_menu_members_entry1_sub1_pp_sb_mail']),
                    "paypal_api_user"           => $class_filter->clr_str($_POST['paypal_api_user']),
                    "paypal_api_pass"           => $class_filter->clr_str($_POST['paypal_api_pass']),
                    "paypal_api_sign"           => $class_filter->clr_str($_POST['paypal_api_sign']),
                );
                break;

            case 'backend-menu-entry5-sub1': //personalized channels - general setup
                $cfg_vars = array(
                    "public_channels"               => intval($_POST['backend_menu_members_entry2_sub1_section']),
                    "user_follows"                  => intval($_POST['backend_menu_members_entry2_sub1_follows']),
                    "channel_comments"              => intval($_POST['backend_menu_entry1_sub6_comments_chan']),
                    "channel_views"                 => intval($_POST['backend_menu_members_entry2_sub1_views']),
                    "ucc_limit"                     => intval($_POST['backend_menu_entry1_sub6_comments_cons_c']),
                    "comment_min_length"            => intval($_POST['backend_menu_entry1_sub6_comments_length_c_min']),
                    "comment_max_length"            => intval($_POST['backend_menu_entry1_sub6_comments_length_c_max']),
                    "channel_backgrounds"           => intval($_POST['backend_menu_members_entry2_sub1_bgimage']),
                    "channel_bulletins"             => intval($_POST['backend_menu_members_entry2_sub1_bulletins']),
                    "user_image_allowed_extensions" => $class_filter->clr_str($_POST['backend_menu_members_entry2_sub1_avatar']),
                    "channel_bg_allowed_extensions" => $class_filter->clr_str($_POST['backend_menu_members_entry2_sub1_bg']),
                    "user_image_max_size"           => intval($_POST['backend_menu_members_entry2_sub1_avatar_size']),
                    "channel_bg_max_size"           => intval($_POST['backend_menu_members_entry2_sub1_bg_size']),
                );
                break;

            case 'backend-menu-entry3-sub1': //server configuration - mail service
                global $language;
                $cfg_vars = array(
                    "website_email"                => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_sitemail']),
                    "website_email_fromname"       => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_sitemail_from']),
                    "backend_email"                => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_adminmail']),
                    "backend_email_fromname"       => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_adminmail_from']),
                    "noreply_email"                => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_noreplymail']),
                    "noreply_email_fromname"       => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_noreplymail_from']),
                    "mail_type"                    => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_mtype']),
                    "mail_sendmail_path"           => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_msmpath']),
                    "mail_smtp_host"               => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_smtp_host']),
                    "mail_smtp_port"               => intval($_POST['backend_menu_entry3_sub1_smtp_port']),
                    "mail_smtp_auth"               => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_smtp_auth']),
                    "mail_smtp_username"           => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_smtp_user']),
                    "mail_smtp_password"           => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_smtp_pass']),
                    "mail_smtp_prefix"             => $class_filter->clr_str($_POST['backend_menu_entry3_sub1_smtp_pref']),
                    "mail_debug"                   => intval($_POST['backend_menu_entry3_sub1_smtp_debug']),
                    "backend_notification_signup"  => intval($_POST['backend_notification_signup']),
                    "backend_notification_upload"  => intval($_POST['backend_notification_upload']),
                    "backend_notification_payment" => intval($_POST['backend_notification_payment']),
                    "backend_notification_payment" => intval($_POST['backend_notification_payment']),
                    "email_logging"                => intval($_POST['backend_menu_entry2_sub4_email']),
                );
                $cfg_vars = $_POST['backend_menu_entry3_sub1_smtp_pass'] == $language['backend.menu.entry3.sub1.pass'] ? VArraySection::arrayRemoveKey($cfg_vars, "mail_smtp_password") : $cfg_vars;
                break;

            case 'backend-menu-entry2-sub6': //admin panel access
                $cfg_vars = array(
                    "backend_username"        => $class_filter->clr_str($_POST['backend_menu_entry2_sub6_admin_user']),
                    "backend_ip_based_access" => intval($_POST['backend_menu_entry2_sub4_IPaccess_be']),
                );
                break;

            case 'backend-menu-entry3-sub7': //PHP Information
            case 'backend-menu-entry3-sub9': //Server Details
            case 'backend-menu-entry2-sub9': //Mail/footer templates
                $cfg_vars = array();
                break;
            case 'backend-menu-entry3-sub11': //Backups
                $cfg_vars = array(
                    "server_path_mysqldump" => $class_filter->clr_str($_POST['server_path_mysqldump']),
                    "server_path_tar"       => $class_filter->clr_str($_POST['server_path_tar']),
                    "server_path_gzip"      => $class_filter->clr_str($_POST['server_path_gzip']),
                    "server_path_zip"       => $class_filter->clr_str($_POST['server_path_zip']),
                );
                break;

            case 'backend-menu-entry2-sub11': //Log Files
                $cfg_vars = array(
                    "activity_logging" => intval($_POST['backend_menu_entry2_sub4_activity']),
                    "log_delete"       => intval($_POST['log_delete']),
                    "log_signin"       => intval($_POST['log_signin']),
                    "log_signout"      => intval($_POST['log_signout']),
                    "log_precovery"    => intval($_POST['log_precovery']),
                    "log_urecovery"    => intval($_POST['log_urecovery']),
                    "log_frinvite"     => intval($_POST['log_frinvite']),
                    "log_pmessage"     => intval($_POST['log_pmessage']),
                    "log_rating"       => intval($_POST['log_rating']),
                    "log_filecomment"  => intval($_POST['log_filecomment']),
                    "log_subscribing"  => intval($_POST['log_subscribing']),
                    "log_following"    => intval($_POST['log_following']),
                    "log_fav"          => intval($_POST['log_fav']),
                    "log_upload"       => intval($_POST['log_upload']),
                    "log_responding"   => intval($_POST['log_responding']),
                );
                break;

            case 'backend-menu-entry2-sub24': //ondemand module
                $cfg_vars = array(
                    "conversion_live_previews"  => intval($_POST['backend_menu_entry6_sub1_conv_prev_l']),
                    "conversion_video_previews" => intval($_POST['backend_menu_entry6_sub1_conv_prev_v']),
                    "conversion_image_previews" => intval($_POST['backend_menu_entry6_sub1_conv_prev_i']),
                    "conversion_audio_previews" => intval($_POST['backend_menu_entry6_sub1_conv_prev_a']),
                    "conversion_doc_previews"   => intval($_POST['backend_menu_entry6_sub1_conv_prev_d']),
                );
                break;

            case 'backend-menu-entry3-sub6': //image conversion
                $cfg_vars = array(
                    "conversion_image_que"    => intval($_POST['backend_menu_entry6_sub1_conv_que']),
                    "conversion_image_type"   => $class_filter->clr_str($_POST['conversion_image_type']),
                    "conversion_image_from_w" => intval($_POST['thanw']),
                    "conversion_image_from_h" => intval($_POST['thanh']),
                    "conversion_image_to_w"   => intval($_POST['tow']),
                    "conversion_image_to_h"   => intval($_POST['toh']),
                );
                break;

            case 'backend-menu-entry3-sub3'; //audio conversion
                $cfg_vars = array(
                    "conversion_audio_que"   => intval($_POST['backend_menu_entry6_sub1_conv_que']),
                    "server_path_lame"       => $class_filter->clr_str($_POST['server_path_lame']),
                    "server_path_ffmpeg"     => $class_filter->clr_str($_POST['server_path_ffmpeg']),
                    "server_path_php"        => $class_filter->clr_str($_POST['server_path_php']),
                    "log_audio_conversion"   => intval($_POST['log_audio_conversion']),
                    "conversion_mp3_bitrate" => intval($_POST['conversion_mp3_bitrate_audio']),
                    "conversion_mp3_srate"   => $class_filter->clr_str($_POST['conversion_mp3_srate_audio']),
                    "conversion_mp3_redo"    => intval($_POST['conversion_mp3_redo']),
                );
                break;

            case 'backend-menu-entry3-sub4'; //document conversion
                $cfg_vars = array(
                    "conversion_document_que" => intval($_POST['backend_menu_entry6_sub1_conv_que']),
                    "server_path_convert"     => $class_filter->clr_str($_POST['server_path_convert']),
                    "server_path_unoconv"     => $class_filter->clr_str($_POST['server_path_unoconv']),
                    "server_path_php"         => $class_filter->clr_str($_POST['server_path_php']),
                    "log_doc_conversion"      => intval($_POST['log_doc_conversion']),
                );
                break;

            case 'backend-menu-entry3-sub2': //video conversion
                $cfg_vars = array(
                    "conversion_video_que" => intval($_POST['backend_menu_entry6_sub1_conv_que']),
                    "server_path_ffmpeg"   => $class_filter->clr_str($_POST['server_path_ffmpeg']),
                    "server_path_ffprobe"  => $class_filter->clr_str($_POST['server_path_ffprobe']),
                    "server_path_yamdi"    => $class_filter->clr_str($_POST['server_path_yamdi']),
                    "server_path_qt"       => $class_filter->clr_str($_POST['server_path_qt']),
                    "server_path_php"      => $class_filter->clr_str($_POST['server_path_php']),
                    "log_video_conversion" => intval($_POST['log_video_conversion']),
                );
                break;
            case 'backend-menu-entry3-sub24': //video shorts conversion
                $cfg_vars = array(
                    "conversion_short_que" => intval($_POST['backend_menu_entry6_sub1_conv_que']),
                    "server_path_ffmpeg"   => $class_filter->clr_str($_POST['server_path_ffmpeg']),
                    "server_path_ffprobe"  => $class_filter->clr_str($_POST['server_path_ffprobe']),
                    "server_path_yamdi"    => $class_filter->clr_str($_POST['server_path_yamdi']),
                    "server_path_qt"       => $class_filter->clr_str($_POST['server_path_qt']),
                    "server_path_php"      => $class_filter->clr_str($_POST['server_path_php']),
                    "log_short_conversion" => intval($_POST['log_short_conversion']),
                );
                break;
            case 'backend-menu-entry3-sub20': //mp4 conversion
                $cfg_vars = array(
                    "conversion_mp4_360p_active"         => intval($_POST['conversion_mp4_360p_active']),
                    "conversion_mp4_360p_bitrate_mt"     => $class_filter->clr_str($_POST['conversion_mp4_360p_bitrate_mt']),
                    "conversion_mp4_360p_bitrate_video"  => intval($_POST['conversion_mp4_360p_bitrate_video']),
                    "conversion_mp4_360p_resize"         => intval($_POST['conversion_mp4_360p_resize']),
                    "conversion_mp4_360p_resize_w"       => intval($_POST['conversion_mp4_360p_resize_w']),
                    "conversion_mp4_360p_resize_h"       => intval($_POST['conversion_mp4_360p_resize_h']),
                    "conversion_mp4_360p_bitrate_audio"  => intval($_POST['conversion_mp4_360p_bitrate_audio']),
                    "conversion_mp4_360p_srate_audio"    => intval($_POST['conversion_mp4_360p_srate_audio']),
                    "conversion_mp4_360p_encoding"       => intval($_POST['conversion_mp4_360p_encoding']),

                    "conversion_mp4_480p_active"         => intval($_POST['conversion_mp4_480p_active']),
                    "conversion_mp4_480p_bitrate_mt"     => $class_filter->clr_str($_POST['conversion_mp4_480p_bitrate_mt']),
                    "conversion_mp4_480p_bitrate_video"  => intval($_POST['conversion_mp4_480p_bitrate_video']),
                    "conversion_mp4_480p_resize"         => intval($_POST['conversion_mp4_480p_resize']),
                    "conversion_mp4_480p_resize_w"       => intval($_POST['conversion_mp4_480p_resize_w']),
                    "conversion_mp4_480p_resize_h"       => intval($_POST['conversion_mp4_480p_resize_h']),
                    "conversion_mp4_480p_bitrate_audio"  => intval($_POST['conversion_mp4_480p_bitrate_audio']),
                    "conversion_mp4_480p_srate_audio"    => intval($_POST['conversion_mp4_480p_srate_audio']),
                    "conversion_mp4_480p_encoding"       => intval($_POST['conversion_mp4_480p_encoding']),

                    "conversion_mp4_720p_active"         => intval($_POST['conversion_mp4_720p_active']),
                    "conversion_mp4_720p_bitrate_mt"     => $class_filter->clr_str($_POST['conversion_mp4_720p_bitrate_mt']),
                    "conversion_mp4_720p_bitrate_video"  => intval($_POST['conversion_mp4_720p_bitrate_video']),
                    "conversion_mp4_720p_resize"         => intval($_POST['conversion_mp4_720p_resize']),
                    "conversion_mp4_720p_resize_w"       => intval($_POST['conversion_mp4_720p_resize_w']),
                    "conversion_mp4_720p_resize_h"       => intval($_POST['conversion_mp4_720p_resize_h']),
                    "conversion_mp4_720p_bitrate_audio"  => intval($_POST['conversion_mp4_720p_bitrate_audio']),
                    "conversion_mp4_720p_srate_audio"    => intval($_POST['conversion_mp4_720p_srate_audio']),
                    "conversion_mp4_720p_encoding"       => intval($_POST['conversion_mp4_720p_encoding']),

                    "conversion_mp4_1080p_active"        => intval($_POST['conversion_mp4_1080p_active']),
                    "conversion_mp4_1080p_bitrate_mt"    => $class_filter->clr_str($_POST['conversion_mp4_1080p_bitrate_mt']),
                    "conversion_mp4_1080p_bitrate_video" => intval($_POST['conversion_mp4_1080p_bitrate_video']),
                    "conversion_mp4_1080p_resize"        => intval($_POST['conversion_mp4_1080p_resize']),
                    "conversion_mp4_1080p_resize_w"      => intval($_POST['conversion_mp4_1080p_resize_w']),
                    "conversion_mp4_1080p_resize_h"      => intval($_POST['conversion_mp4_1080p_resize_h']),
                    "conversion_mp4_1080p_bitrate_audio" => intval($_POST['conversion_mp4_1080p_bitrate_audio']),
                    "conversion_mp4_1080p_srate_audio"   => intval($_POST['conversion_mp4_1080p_srate_audio']),
                    "conversion_mp4_1080p_encoding"      => intval($_POST['conversion_mp4_1080p_encoding']),
                );
                break;
            case 'backend-menu-entry3-sub21': //webm conversion
                $cfg_vars = array(
                    "conversion_vpx_360p_active"         => intval($_POST['conversion_vpx_360p_active']),
                    "conversion_vpx_360p_bitrate_mt"     => $class_filter->clr_str($_POST['conversion_vpx_360p_bitrate_mt']),
                    "conversion_vpx_360p_bitrate_video"  => intval($_POST['conversion_vpx_360p_bitrate_video']),
                    "conversion_vpx_360p_resize"         => intval($_POST['conversion_vpx_360p_resize']),
                    "conversion_vpx_360p_resize_w"       => intval($_POST['conversion_vpx_360p_resize_w']),
                    "conversion_vpx_360p_resize_h"       => intval($_POST['conversion_vpx_360p_resize_h']),
                    "conversion_vpx_360p_bitrate_audio"  => intval($_POST['conversion_vpx_360p_bitrate_audio']),
                    "conversion_vpx_360p_srate_audio"    => intval($_POST['conversion_vpx_360p_srate_audio']),
                    "conversion_vpx_360p_encoding"       => intval($_POST['conversion_vpx_360p_encoding']),

                    "conversion_vpx_480p_active"         => intval($_POST['conversion_vpx_480p_active']),
                    "conversion_vpx_480p_bitrate_mt"     => $class_filter->clr_str($_POST['conversion_vpx_480p_bitrate_mt']),
                    "conversion_vpx_480p_bitrate_video"  => intval($_POST['conversion_vpx_480p_bitrate_video']),
                    "conversion_vpx_480p_resize"         => intval($_POST['conversion_vpx_480p_resize']),
                    "conversion_vpx_480p_resize_w"       => intval($_POST['conversion_vpx_480p_resize_w']),
                    "conversion_vpx_480p_resize_h"       => intval($_POST['conversion_vpx_480p_resize_h']),
                    "conversion_vpx_480p_bitrate_audio"  => intval($_POST['conversion_vpx_480p_bitrate_audio']),
                    "conversion_vpx_480p_srate_audio"    => intval($_POST['conversion_vpx_480p_srate_audio']),
                    "conversion_vpx_480p_encoding"       => intval($_POST['conversion_vpx_480p_encoding']),

                    "conversion_vpx_720p_active"         => intval($_POST['conversion_vpx_720p_active']),
                    "conversion_vpx_720p_bitrate_mt"     => $class_filter->clr_str($_POST['conversion_vpx_720p_bitrate_mt']),
                    "conversion_vpx_720p_bitrate_video"  => intval($_POST['conversion_vpx_720p_bitrate_video']),
                    "conversion_vpx_720p_resize"         => intval($_POST['conversion_vpx_720p_resize']),
                    "conversion_vpx_720p_resize_w"       => intval($_POST['conversion_vpx_720p_resize_w']),
                    "conversion_vpx_720p_resize_h"       => intval($_POST['conversion_vpx_720p_resize_h']),
                    "conversion_vpx_720p_bitrate_audio"  => intval($_POST['conversion_vpx_720p_bitrate_audio']),
                    "conversion_vpx_720p_srate_audio"    => intval($_POST['conversion_vpx_720p_srate_audio']),
                    "conversion_vpx_720p_encoding"       => intval($_POST['conversion_vpx_720p_encoding']),

                    "conversion_vpx_1080p_active"        => intval($_POST['conversion_vpx_1080p_active']),
                    "conversion_vpx_1080p_bitrate_mt"    => $class_filter->clr_str($_POST['conversion_vpx_1080p_bitrate_mt']),
                    "conversion_vpx_1080p_bitrate_video" => intval($_POST['conversion_vpx_1080p_bitrate_video']),
                    "conversion_vpx_1080p_resize"        => intval($_POST['conversion_vpx_1080p_resize']),
                    "conversion_vpx_1080p_resize_w"      => intval($_POST['conversion_vpx_1080p_resize_w']),
                    "conversion_vpx_1080p_resize_h"      => intval($_POST['conversion_vpx_1080p_resize_h']),
                    "conversion_vpx_1080p_bitrate_audio" => intval($_POST['conversion_vpx_1080p_bitrate_audio']),
                    "conversion_vpx_1080p_srate_audio"   => intval($_POST['conversion_vpx_1080p_srate_audio']),
                    "conversion_vpx_1080p_encoding"      => intval($_POST['conversion_vpx_1080p_encoding']),
                );
                break;
            case 'backend-menu-entry3-sub22': //ogv conversion
                $cfg_vars = array(
                    "conversion_ogv_360p_active"         => intval($_POST['conversion_ogv_360p_active']),
                    "conversion_ogv_360p_bitrate_mt"     => $class_filter->clr_str($_POST['conversion_ogv_360p_bitrate_mt']),
                    "conversion_ogv_360p_bitrate_video"  => intval($_POST['conversion_ogv_360p_bitrate_video']),
                    "conversion_ogv_360p_resize"         => intval($_POST['conversion_ogv_360p_resize']),
                    "conversion_ogv_360p_resize_w"       => intval($_POST['conversion_ogv_360p_resize_w']),
                    "conversion_ogv_360p_resize_h"       => intval($_POST['conversion_ogv_360p_resize_h']),
                    "conversion_ogv_360p_bitrate_audio"  => intval($_POST['conversion_ogv_360p_bitrate_audio']),
                    "conversion_ogv_360p_srate_audio"    => intval($_POST['conversion_ogv_360p_srate_audio']),
                    "conversion_ogv_360p_encoding"       => intval($_POST['conversion_ogv_360p_encoding']),

                    "conversion_ogv_480p_active"         => intval($_POST['conversion_ogv_480p_active']),
                    "conversion_ogv_480p_bitrate_mt"     => $class_filter->clr_str($_POST['conversion_ogv_480p_bitrate_mt']),
                    "conversion_ogv_480p_bitrate_video"  => intval($_POST['conversion_ogv_480p_bitrate_video']),
                    "conversion_ogv_480p_resize"         => intval($_POST['conversion_ogv_480p_resize']),
                    "conversion_ogv_480p_resize_w"       => intval($_POST['conversion_ogv_480p_resize_w']),
                    "conversion_ogv_480p_resize_h"       => intval($_POST['conversion_ogv_480p_resize_h']),
                    "conversion_ogv_480p_bitrate_audio"  => intval($_POST['conversion_ogv_480p_bitrate_audio']),
                    "conversion_ogv_480p_srate_audio"    => intval($_POST['conversion_ogv_480p_srate_audio']),
                    "conversion_ogv_480p_encoding"       => intval($_POST['conversion_ogv_480p_encoding']),

                    "conversion_ogv_720p_active"         => intval($_POST['conversion_ogv_720p_active']),
                    "conversion_ogv_720p_bitrate_mt"     => $class_filter->clr_str($_POST['conversion_ogv_720p_bitrate_mt']),
                    "conversion_ogv_720p_bitrate_video"  => intval($_POST['conversion_ogv_720p_bitrate_video']),
                    "conversion_ogv_720p_resize"         => intval($_POST['conversion_ogv_720p_resize']),
                    "conversion_ogv_720p_resize_w"       => intval($_POST['conversion_ogv_720p_resize_w']),
                    "conversion_ogv_720p_resize_h"       => intval($_POST['conversion_ogv_720p_resize_h']),
                    "conversion_ogv_720p_bitrate_audio"  => intval($_POST['conversion_ogv_720p_bitrate_audio']),
                    "conversion_ogv_720p_srate_audio"    => intval($_POST['conversion_ogv_720p_srate_audio']),
                    "conversion_ogv_720p_encoding"       => intval($_POST['conversion_ogv_720p_encoding']),

                    "conversion_ogv_1080p_active"        => intval($_POST['conversion_ogv_1080p_active']),
                    "conversion_ogv_1080p_bitrate_mt"    => $class_filter->clr_str($_POST['conversion_ogv_1080p_bitrate_mt']),
                    "conversion_ogv_1080p_bitrate_video" => intval($_POST['conversion_ogv_1080p_bitrate_video']),
                    "conversion_ogv_1080p_resize"        => intval($_POST['conversion_ogv_1080p_resize']),
                    "conversion_ogv_1080p_resize_w"      => intval($_POST['conversion_ogv_1080p_resize_w']),
                    "conversion_ogv_1080p_resize_h"      => intval($_POST['conversion_ogv_1080p_resize_h']),
                    "conversion_ogv_1080p_bitrate_audio" => intval($_POST['conversion_ogv_1080p_bitrate_audio']),
                    "conversion_ogv_1080p_srate_audio"   => intval($_POST['conversion_ogv_1080p_srate_audio']),
                    "conversion_ogv_1080p_encoding"      => intval($_POST['conversion_ogv_1080p_encoding']),
                );
                break;
            case 'backend-menu-entry3-sub23': //mobile conversion
                $cfg_vars = array(
                    "conversion_mp4_ipad_active"        => intval($_POST['conversion_mp4_ipad_active']),
                    "conversion_mp4_ipad_bitrate_mt"    => $class_filter->clr_str($_POST['conversion_mp4_ipad_bitrate_mt']),
                    "conversion_mp4_ipad_bitrate_video" => intval($_POST['conversion_mp4_ipad_bitrate_video']),
                    "conversion_mp4_ipad_resize"        => intval($_POST['conversion_mp4_ipad_resize']),
                    "conversion_mp4_ipad_resize_w"      => intval($_POST['conversion_mp4_ipad_resize_w']),
                    "conversion_mp4_ipad_resize_h"      => intval($_POST['conversion_mp4_ipad_resize_h']),
                    "conversion_mp4_ipad_bitrate_audio" => intval($_POST['conversion_mp4_ipad_bitrate_audio']),
                    "conversion_mp4_ipad_srate_audio"   => intval($_POST['conversion_mp4_ipad_srate_audio']),
                    "conversion_mp4_ipad_encoding"      => intval($_POST['conversion_mp4_ipad_encoding']),
                );
                break;
            case 'backend-menu-entry2-sub7'; //main modules
                $cfg_vars = array(
                    "video_module"    => intval($_POST['backend_menu_entry2_sub7_video']),
                    "short_module"    => intval($_POST['backend_menu_entry2_sub7_short']),
                    "blog_module"     => intval($_POST['backend_menu_entry2_sub7_blog']),
                    "image_module"    => intval($_POST['backend_menu_entry2_sub7_image']),
                    "audio_module"    => intval($_POST['backend_menu_entry2_sub7_audio']),
                    "document_module" => intval($_POST['backend_menu_entry2_sub7_doc']),
                    "live_module"     => intval($_POST['backend_menu_entry2_sub7_live']),
                );
                break;
            case 'backend-menu-entry2-sub10'; //mobile/ipad interface
                $cfg_vars = array(
                    "mobile_module"               => intval($_POST['backend_menu_entry2_sub1_m_conf']),
                    "mobile_detection"            => intval($_POST['backend_menu_entry2_sub1_m_detect']),
                    "mobile_head_title"           => $class_filter->clr_str($_POST['backend_menu_entry2_sub10_headtitle_m']),
                    "mobile_metaname_description" => $class_filter->clr_str($_POST['backend_menu_entry2_sub10_metadesc_m']),
                    "mobile_metaname_keywords"    => $class_filter->clr_str($_POST['backend_menu_entry2_sub10_metakeywords_m']),
                    "mobile_menu"                 => intval($_POST['backend_menu_entry2_sub1_menu_m']),
                );
                break;
            case 'backend-menu-entry3-sub12'; //session settings
                $cfg_vars = array(
                    "session_name"     => $class_filter->clr_str($_POST['backend_menu_entry2_sub3_sessname']),
                    "session_lifetime" => intval($_POST['backend_menu_entry2_sub3_sesslife']),
                );
                break;
            case 'backend-menu-entry3-sub18'; //timezone settings
                $cfg_vars = array(
                    "date_timezone" => $class_filter->clr_str($_POST['backend_menu_entry2_sub3_timezone']),
                );
                break;
            case 'backend-menu-entry2-sub3'; //sitemaps
                $cfg_vars = array(
                    "sitemap_global_frontpage"   => intval($_POST['sm_homepage']),
                    "sitemap_global_content"     => intval($_POST['sm_static']),
                    "sitemap_global_categories"  => intval($_POST['sm_categ']),
                    "sitemap_global_users"       => intval($_POST['sm_users']),
                    "sitemap_global_live"        => intval($_POST['sm_live']),
                    "sitemap_global_video"       => intval($_POST['sm_video']),
                    "sitemap_global_short"       => intval($_POST['sm_short']),
                    "sitemap_global_image"       => intval($_POST['sm_image']),
                    "sitemap_global_audio"       => intval($_POST['sm_audio']),
                    "sitemap_global_document"    => intval($_POST['sm_doc']),
                    "sitemap_global_blog"        => intval($_POST['sm_blog']),
                    "sitemap_global_video_pl"    => intval($_POST['sm_video_pl']),
                    "sitemap_global_short_pl"    => intval($_POST['sm_short_pl']),
                    "sitemap_global_live_pl"     => intval($_POST['sm_live_pl']),
                    "sitemap_global_image_pl"    => intval($_POST['sm_image_pl']),
                    "sitemap_global_audio_pl"    => intval($_POST['sm_audio_pl']),
                    "sitemap_global_document_pl" => intval($_POST['sm_doc_pl']),
                    "sitemap_global_blog_pl"     => intval($_POST['sm_blog_pl']),
                    "sitemap_global_max"         => intval($_POST['sm_max_entries']),
                    "sitemap_video_hd"           => intval($_POST['sm_v_hd']),
                    "sitemap_video_max"          => intval($_POST['sm_max_video']),
                    "sitemap_image_max"          => intval($_POST['sm_max_image']),
                    "sitemap_short_max"          => intval($_POST['sm_max_short']),
                );
                break;
            case 'backend-menu-entry2-sub14'; //file players
                $cfg_vars = array(
                    "video_player" => $class_filter->clr_str($_POST['fp_video']),
                    "audio_player" => $class_filter->clr_str($_POST['fp_audio']),
                );
                break;
            case 'backend-menu-entry2-sub22'; //affiliate module
                $cfg_vars = array(
                    "affiliate_module"            => (int) $_POST['backend_menu_sc_affiliate'],
                    "affiliate_tracking_id"       => $class_filter->clr_str($_POST['backend_menu_af_analytics']),
                    "affiliate_view_id"           => $class_filter->clr_str($_POST['backend_menu_af_aview']),
                    "affiliate_maps_api_key"      => $class_filter->clr_str($_POST['backend_menu_af_maps']),
                    "affiliate_token_script"      => $class_filter->clr_str($_POST['backend_menu_af_token']),
                    "affiliate_payout_figure"     => $class_filter->clr_str($_POST['backend_menu_af_p_figure']),
                    "affiliate_payout_units"      => $class_filter->clr_str($_POST['backend_menu_af_p_units']),
                    "affiliate_payout_currency"   => $class_filter->clr_str($_POST['backend_menu_af_p_currency']),
                    "affiliate_payout_share"      => $class_filter->clr_str($_POST['backend_menu_af_p_share']),
                    "affiliate_requirements_min"  => (int) $_POST['backend_menu_af_requirements_min'],
                    "affiliate_requirements_type" => (int) $_POST['backend_menu_af_requirements_type'],
                    "paypal_test"                 => intval($_POST['backend_menu_members_entry1_sub1_pp_test']),
                );
                break;
            case 'backend-menu-entry2-sub23'; //live streaming module
                $cfg_vars = array(
                    "live_chat"      => $class_filter->clr_str($_POST['backend_menu_live_chat']),
                    "live_chat_salt" => $class_filter->clr_str($_POST['backend_menu_live_chat_salt']),
                    "live_cron_salt" => $class_filter->clr_str($_POST['backend_menu_live_cron_salt']),
                    "live_vod"       => $class_filter->clr_str($_POST['backend_menu_live_vod']),
                    "live_del"       => $class_filter->clr_str($_POST['backend_menu_live_del']),
                    "user_tokens"    => $class_filter->clr_str($_POST['backend_menu_live_token']),
                );
                break;
            case 'backend-menu-entry2-sub8'; //guest permissions
                $cfg_vars = array(
                    "guest_browse_live"     => intval($_POST['backend_menu_entry1_sub13_guest_browse_l']),
                    "guest_browse_video"    => intval($_POST['backend_menu_entry1_sub13_guest_browse_v']),
                    "guest_browse_image"    => intval($_POST['backend_menu_entry1_sub13_guest_browse_i']),
                    "guest_browse_audio"    => intval($_POST['backend_menu_entry1_sub13_guest_browse_a']),
                    "guest_browse_doc"      => intval($_POST['backend_menu_entry1_sub13_guest_browse_d']),
                    "guest_view_live"       => intval($_POST['backend_menu_entry1_sub13_guest_view_l']),
                    "guest_view_video"      => intval($_POST['backend_menu_entry1_sub13_guest_view_v']),
                    "guest_view_image"      => intval($_POST['backend_menu_entry1_sub13_guest_view_i']),
                    "guest_view_audio"      => intval($_POST['backend_menu_entry1_sub13_guest_view_a']),
                    "guest_view_doc"        => intval($_POST['backend_menu_entry1_sub13_guest_view_d']),
                    "guest_view_channel"    => intval($_POST['backend_menu_entry1_sub13_guest_view_c']),
                    "guest_search_page"     => intval($_POST['backend_menu_entry1_sub13_guest_view_s']),
                    "guest_browse_channel"  => intval($_POST['backend_menu_entry1_sub13_guest_browse_ch']),
                    "guest_browse_playlist" => intval($_POST['backend_menu_entry1_sub13_guest_browse_pl']),
                    "guest_browse_blog"     => intval($_POST['backend_menu_entry1_sub13_guest_browse_b']),
                    "guest_view_blog"       => intval($_POST['backend_menu_entry1_sub13_guest_view_b']),
                );
                break;
            case 'backend-menu-entry3-sub10'; //streaming settings
                switch ($class_filter->clr_str($_POST['stream_method'])) {
                    case "progressive":
                        $v = 1;
                        break;
                    case "pseudostreaming":
                        $v = 2;
                        break;
                    case "rtmp":
                        $v = 3;
                        break;
                }
                $cfg_vars = array(
                    "stream_method"          => $v,
                    "stream_server"          => $class_filter->clr_str($_POST['stream_server']),
                    "stream_lighttpd_url"    => $class_filter->clr_str($_POST['stream_url']),
                    "stream_lighttpd_secure" => $class_filter->clr_str($_POST['stream_secure']),
                    "stream_lighttpd_prefix" => $class_filter->clr_str($_POST['stream_prefix']),
                    "stream_lighttpd_key"    => $class_filter->clr_str($_POST['stream_key']),
                    "stream_rtmp_location"   => $class_filter->clr_str($_POST['stream_rtmp_location']),
                );
                break;
            case 'backend-menu-entry2-sub21'; //embed plugin
                $cfg_vars = array(
                    "import_yt_channel_list" => $class_filter->clr_str($_POST['backend_import_menu_yt_list']),
                    "youtube_api_key"        => $class_filter->clr_str($_POST['backend_import_yt_api_key']),
                    "import_dm_user_list"    => $class_filter->clr_str($_POST['backend_import_menu_dm_list']),
                    "import_vi_user_list"    => $class_filter->clr_str($_POST['backend_import_menu_vi_list']),
                    "import_yt"              => intval($_POST['import_yt']),
                    "import_dm"              => intval($_POST['import_dm']),
                    "import_vi"              => intval($_POST['import_vi']),
                    "m_list_yt"              => intval($_POST['m_list_yt']),
                    "m_list_dm"              => intval($_POST['m_list_dm']),
                    "m_list_vi"              => intval($_POST['m_list_vi']),
                    "import_mode"            => $class_filter->clr_str($_POST['import_mode']),
                );
                break;
        }
        return $cfg_vars;
    }
}
