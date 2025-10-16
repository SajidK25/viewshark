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

class VView
{
    protected static $type;
    protected static $file_key;
    protected static $chat_key;
    protected static $cfg;
    protected static $db;
    private static $db_cache;
    public static $rel;
    protected static $dbc;
    protected static $filter;
    protected static $language;
    protected static $href;
    protected static $section;
    protected static $smarty;
    protected static $userinfo;
    public static $is_shorts;

    public function __construct()
    {
        require 'f_core/config.href.php';

        global $cfg, $class_filter, $class_database, $db, $language, $href, $section, $smarty, $is_shorts;

        self::$cfg = $cfg;
        $_type     = (self::$cfg['short_module'] == 1 and isset($_GET['s'])) ? 'short' : ((self::$cfg['live_module'] == 1 and isset($_GET['l'])) ? 'live' : ((self::$cfg['video_module'] == 1 and isset($_GET['v'])) ? 'video' : ((self::$cfg['image_module'] == 1 and isset($_GET['i'])) ? 'image' : ((self::$cfg['audio_module'] == 1 and isset($_GET['a'])) ? 'audio' : ((self::$cfg['document_module'] == 1 and isset($_GET['d'])) ? 'doc' : ((self::$cfg['blog_module'] == 1 and isset($_GET['b'])) ? 'blog' : null))))));
        if ($section == $href['shorts']) {
            $_type = $href['shorts'];
        }
        self::$type      = $_type == 'document' ? 'doc' : $_type;
        self::$db        = $db;
        self::$dbc       = $class_database;
        self::$filter    = $class_filter;
        self::$file_key  = (int) self::$filter->clr_str($_GET[self::$type[0]]);
        self::$language  = $language;
        self::$href      = $href;
        self::$section   = $section;
        self::$smarty    = $smarty;
        self::$is_shorts = $is_shorts;
        self::$userinfo  = array("user_id" => '', "user_name" => "", "upload_category" => "", "usr_partner" => "", "usr_affiliate" => "", "affiliate_badge" => "", "user_dname" => "", "ch_title" => "", "user_inc" => "", "user_joindate" => "", "user_issub" => "", "user_subtotal" => "", "user_isfollow" => "", "user_followtotal" => "");

        if ($_type == 'live' and self::$file_key != '' and !isset($_GET['s']) and !isset($_GET['do'])) {
            $fcs                          = VServer::getFreeServer('chat');
            $_SESSION['live_chat_server'] = $fcs;
            self::set_gcs($_SESSION['USER_ID'], $fcs);
            self::$smarty->assign('live_chat_server', $fcs);
        }

        self::$db_cache = false; //change here to enable caching
    }
    /* get embed code from url */
    public static function getEmbedCodeFromURL()
    {
        require_once 'f_core/f_classes/class_oembed/vendor/autoload.php';

        if (isset($_POST['ec']) and $p = secured_decrypt($_POST['ec'])) {
            try {
                $embed = new \Embed\Embed();
                $url   = (html_entity_decode($p, ENT_QUOTES, "UTF-8"));
                $url   = (html_entity_decode($url, ENT_QUOTES, "UTF-8"));
                $u     = parse_url($url);
                $h     = explode('.', $u['host']);
                $src   = $h[(count($h) - 2)];
                $src   = ($src == 'youtu' ? 'youtube' : $src);

                switch ($src) {
                    case "facebook":
                        $ec = '<style>.fb_iframe_widget{position:absolute;width:100% !important;height:100% !important}.fb-video > span, .fb-video iframe{width:100% !important;height:100% !important}</style><div id="fb-root"></div><script async defer src="https://connect.facebook.net/en_US/sdk.js#xfbml=1&version=v3.2"></script><div class="fb-video" data-href="' . $url . '" data-allowfullscreen="true" data-width="640px"></div>';
                        break;
                    case "joshwhotv":
                        $ec = '<div class="embed-responsive embed-responsive-16by9" style="position: relative;padding-bottom: 56.25% !important;"><iframe width="640" height="360" style="max-width: 100%;max-height: 100%; border:none;position: absolute;top: 0;left: 0;width: 100%; height: 100%;" src="' . str_replace('/video/', '/videoEmbed/', $url) . '?modestbranding=1&showinfo=1&autoplay=&controls=1&loop=&mute=&t=0&objectFit=" frameborder="0" allow="fullscreen;autoplay;camera *;microphone *;" allowfullscreen="allowfullscreen" mozallowfullscreen="mozallowfullscreen" msallowfullscreen="msallowfullscreen" oallowfullscreen="oallowfullscreen" webkitallowfullscreen="webkitallowfullscreen" scrolling="no" videoLengthInSeconds="1642">iFrame is not supported!</iframe></div>';
                        break;
                    default:
                        $oe = $embed->get($url);
                        $ec = preg_replace('/(width|height)="[^"]*"/', '$1="100%"', $oe->code->html);
                        break;
                }

                return json_encode(["c" => $ec]);
            } catch (Exception $e) {
                $er = VGenerate::noticeTpl('', $e->getMessage(), '');

                return json_encode(["e" => $er]);
            }
        }
    }
    /* set current user temp chat servers */
    private static function set_gcs($uid, $fcs)
    {
        $uid = (int) $uid;
        $fcs = self::$filter->clr_str($fcs);

        self::$db->execute(sprintf("UPDATE `db_accountuser` SET `chat_temp`='%s' WHERE `usr_id`='%s' LIMIT 1;", $fcs, $uid));
    }
    /* get current user temp chat servers */
    private static function get_gcs($uid)
    {
        $uid = (int) $uid;

        return self::$dbc->singleFieldValue('db_accountuser', 'chat_temp', 'usr_id', $uid);
    }
    /* submit like/dislike actions */
    public function likeAction($type)
    {
        $language       = self::$language;
        $class_filter   = self::$filter;
        $class_database = self::$dbc;
        $db             = self::$db;
        $cfg            = self::$cfg;

        $_uid     = (int) $_SESSION['USER_ID'];
        $vtype    = self::$type;
        $vkey     = self::$file_key;
        $vrate    = (int) $_POST['f_vrate'];
        $vlike    = (int) $_POST['f_like'];
        $vdislike = (int) $_POST['f_dislike'];
        $uuid     = (int) $_POST['uf_vuid'];
        $vtxt     = $type == 'file-like' ? $language['view.files.like.txt'] : $language['view.files.dislike.txt'];
        $vtxt     = str_replace('##TYPE##', $language["frontend.global." . $vtype[0]], $vtxt);
        /* get like, dislike numbers */
        $vnr       = $db->execute(sprintf("SELECT `file_like`, `file_dislike` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $vtype, $vkey));
        $likenr    = $vnr->fields['file_like'];
        $dislikenr = $vnr->fields['file_dislike'];
        /* get file likes */
        $vsql  = sprintf("SELECT `db_id` FROM `db_%sliked` WHERE `usr_id`='%s' AND `file_key`='%s' LIMIT 1;", $vtype, $_uid, $vkey);
        $vinfo = $db->execute($vsql);
        $likes = $vinfo->fields['db_id'];
        if (!$likes and $type == 'file-like') {
            $class_database->doInsert('db_' . $vtype . 'liked', ["usr_id" => $_uid, "file_key" => $vkey, "datetime" => date("Y-m-d H:i:s")]);
        } elseif (($likes and $type == 'file-dislike') or ($likes and $type == 'file-like')) {
            $db->execute(sprintf("DELETE FROM `db_%sliked` WHERE `db_id`='%s' LIMIT 1;", $vtype, $likes));
        }
        /* get file votes */
        $vsql  = sprintf("SELECT `file_votes` FROM `db_%srating` WHERE `file_key`='%s' LIMIT 1;", $vtype, $vkey);
        $vinfo = $db->execute($vsql);
        $votes = $vinfo->fields['file_votes'];
        /* start checking */
        $js_extra  = '';
        $q_js      = false;
        $q_str     = false;
        $do_update = false;
        $db_field  = $type == 'file-like' ? 'file_like' : 'file_dislike';
        /* if no votes */
        if ($votes == '') {
            $db_arr    = array(array("usr_id" => $_uid, "usr_vote" => ($type == 'file-like' ? 1 : 0)));
            $v_update  = sprintf("INSERT INTO `db_%srating` (`db_id` ,`file_key` ,`file_votes`) VALUES ('', '%s', '%s');", $vtype, $vkey, serialize($db_arr));
            $db_update = $v_update != '' ? $db->execute($v_update) : null;
            $do_update = $db->Affected_Rows();
            $q_str     = sprintf("`%s`=`%s`+1", $db_field, $db_field);
            $js_extra  = '$(".' . $type . '-thumb").addClass("active");';
        } else {
            /* append to votes */
            $f      = 0;
            $db_arr = $votes != '' ? unserialize($votes) : null;
            if (is_array($db_arr)) {
                /* search if already voted */
                foreach ($db_arr as $key => $val) {
                    if ($f == 0 and $val['usr_id'] == $_SESSION['USER_ID']) {
                        $f    = 1;
                        $vtxt = $language['view.files.liked.already'];
                        if ($db_arr[$key]['usr_vote'] == 1 and $type != 'file-like') {
                            //already liked, voted dislike
                            $q_str = sprintf("`file_dislike`=`file_dislike`+1, `file_like`=`file_like`-1");
                            $q_js  = '$(".likey .likes_count").text("' . VFiles::numFormat($likenr - 1) . '"); $(".dislikey .dislikes_count").text("' . VFiles::numFormat($dislikenr + 1) . '");';
                            $q_js .= '$(".file-like-thumb").removeClass("active");$(".file-dislike-thumb").addClass("active");';
                            $db_arr[$key]['usr_vote'] = ($type == 'file-like' ? 1 : 0);
                        } elseif ($db_arr[$key]['usr_vote'] == 0 and $type == 'file-like') {
                            //already disliked, voted like
                            $q_str = sprintf("`file_dislike`=`file_dislike`-1, `file_like`=`file_like`+1");
                            $q_js  = '$(".likey .likes_count").text("' . VFiles::numFormat($likenr + 1) . '"); $(".dislikey .dislikes_count").text("' . VFiles::numFormat($dislikenr - 1) . '");';
                            $q_js .= '$(".file-like-thumb").addClass("active");$(".file-dislike-thumb").removeClass("active");';
                            $db_arr[$key]['usr_vote'] = ($type == 'file-like' ? 1 : 0);
                        } elseif ($db_arr[$key]['usr_vote'] == 1 and $type == 'file-like') {
                            //already liked, voted like (cancel like)
                            $q_str = sprintf("`file_like`=`file_like`-1");
                            $q_js  = '$(".likey .likes_count").text("' . VFiles::numFormat($likenr - 1) . '");';
                            $q_js .= '$(".file-like-thumb").removeClass("active");$(".file-dislike-thumb").removeClass("active");';
                            unset($db_arr[$key]);
                        } elseif ($db_arr[$key]['usr_vote'] == 0 and $type != 'file-like') {
                            //already disliked, voted dislike (cancel dislike)
                            $q_str = sprintf("`file_dislike`=`file_dislike`-1");
                            $q_js  = '$(".dislikey .dislikes_count").text("' . VFiles::numFormat($dislikenr - 1) . '");';
                            $q_js .= '$(".file-like-thumb").removeClass("active");$(".file-dislike-thumb").removeClass("active");';
                            unset($db_arr[$key]);
                        }
                        $v_update  = sprintf("UPDATE `db_%srating` SET `file_votes`='%s' WHERE `file_key`='%s' LIMIT 1;", $vtype, serialize($db_arr), $vkey);
                        $db_update = $v_update != '' ? $db->execute($v_update) : null;
                        $do_update = $db->Affected_Rows();

                        break;
                    }
                }
                /* if did not already vote */
                if ($f == 0) {
                    $db_arr[]  = array("usr_id" => $_uid, "usr_vote" => ($type == 'file-like' ? 1 : 0));
                    $v_update  = sprintf("UPDATE `db_%srating` SET `file_votes`='%s' WHERE `file_key`='%s' LIMIT 1;", $vtype, serialize($db_arr), $vkey);
                    $db_update = $v_update != '' ? $db->execute($v_update) : null;
                    $do_update = $db->Affected_Rows();
                    $q_str     = sprintf("`%s`=`%s`+1", $db_field, $db_field);
                    if ($type == 'file-like') {
                        $q_js = '$(".likey .likes_count").text("' . VFiles::numFormat($likenr + 1) . '");';
                    } else {
                        $q_js = '$(".dislikey .dislikes_count").text("' . VFiles::numFormat($dislikenr + 1) . '");';
                    }
                    $q_js .= '$(".' . $type . '-thumb").addClass("active");';
                }
            }
        }

        if ($do_update) {
            $db->execute(sprintf("UPDATE `db_%sfiles` SET %s WHERE `file_key`='%s' LIMIT 1", $vtype, $q_str, $vkey));
            $vlike    = $type == 'file-like' ? ($vlike + 1) : $vlike;
            $vdislike = $type == 'file-dislike' ? ($vdislike + 1) : $vdislike;
            $js_extra .= '$(".' . ($type == 'file-like' ? 'f_like' : 'f_dislike') . '").val("' . ($type == 'file-like' ? $vlike : $vdislike) . '");';
            if ($q_js) {
                $js_extra .= $q_js;
            } else {
                $js_extra .= '$(".' . ($type == 'file-like' ? 'likey .likes_count' : 'dislikey .dislikes_count') . '").text("' .
                    ($type == 'file-like' ? VFiles::numFormat($likenr + 1) : VFiles::numFormat($dislikenr + 1))
                    . '");';
            }
            $log = ($cfg['activity_logging'] == 1 and $action = new VActivity((int) $_SESSION['USER_ID'], $uuid)) ? $action->addTo('log_rating', (substr($type, 5) . ':' . $vtype . ':' . $vkey)) : null;
        }

        echo VGenerate::declareJS($js_extra);
        return VGenerate::simpleDivWrap('', 'file-like-stats', self::likeStatBars($vrate, $vlike, $vdislike));
    }

    /* like/dislike stat bars */
    public function likeStatBars($vrate, $vlike, $vdislike)
    {
        $language = self::$language;

        $like_total   = $vlike + $vdislike;
        $like_perc    = $like_total > 0 ? round(100 * $vlike / $like_total) : 0;
        $dislike_perc = $like_total > 0 ? round(100 * $vdislike / $like_total) : 0;

        $vlike_txt = $vrate == 1 ? VGenerate::simpleDivWrap('vote-bar', '', '<span class="file-like-perc" style="width:' . $like_perc . '%; background-color: green; height: 3px;">&nbsp;</span><span class="file-dislike-perc" style="width:' . $dislike_perc . '%; background-color: red; height: 3px;">&nbsp;</span>') : null;

        return $vlike_txt;
    }

    /* view files layout */
    public static function viewLayout()
    {
        $sql   = self::currentSQL(self::$type, self::$file_key);
        $vdata = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_current'], $sql) : self::$db->execute($sql);
        $vid   = self::$file_key;
        $vuid  = $vdata->fields['usr_id'];

        if (self::$type != 'short' and !$vuid) {
            echo '<div class="error-message-text" onclick="jQuery(this).detach();">' . self::$language['notif.error.invalid.request'] . '</div>';
        } else {
            if (self::$type == 'short' and (int) $vid == 0) {
                $last_short = self::$dbc->singleFieldValue('db_accountuser', 'usr_last_short', 'usr_id', (int) $_SESSION['USER_ID']);
                if ($last_short > 0) {
                    $sql = sprintf("SELECT `db_id`, `file_key` FROM `db_shortfiles` WHERE `privacy`='public' AND `approved`='1' AND `deleted`='0' AND `active`='1' AND `db_id` > (SELECT `db_id` FROM `db_shortfiles` WHERE `file_key`='%s' LIMIT 1) ORDER BY `db_id` ASC LIMIT 1", $last_short);
                    $rs  = self::$db->execute($sql);
                    if (!$rs->fields['db_id']) {
                        self::$db->execute(sprintf("UPDATE `db_accountuser` SET `usr_last_short`='0' WHERE `usr_id`='%s' LIMIT 1;", (int) $_SESSION['USER_ID']));
                        $last_short = 0;
                    }
                }
                if ($last_short == 0) {
                    $sql = sprintf("SELECT `db_id`, `file_key` FROM `db_shortfiles` WHERE `privacy`='public' AND `approved`='1' AND `deleted`='0' AND `active`='1' ORDER BY `db_id` ASC LIMIT 1");
                }
                $rs = self::$db->execute($sql);

                if ($rs->fields['db_id']) {
                    header("Location: " . self::$cfg['main_url'] . '/' . VHref::getKey('shorts') . '/' . $rs->fields['file_key']);
                    exit;
                }
            } elseif (self::$type == 'short' and (int) $vid > 0) {
                self::$db->execute(sprintf("UPDATE `db_accountuser` SET `usr_last_short`='%s' WHERE `usr_id`='%s' LIMIT 1;", (int) $vid, (int) $_SESSION['USER_ID']));
            }
        }

        $vuser  = $vdata->fields['usr_user'];
        $duser  = $vdata->fields['usr_dname'];
        $cuser  = $vdata->fields['ch_title'];
        $ukey   = $vdata->fields['usr_key'];
        $ujoin  = $vdata->fields['usr_joindate'];
        $umail  = $vdata->fields['usr_email'];
        $viewnr = $vdata->fields['file_views'];
        $vdate  = $vdata->fields['upload_date'];
        $vtitle = $vdata->fields['file_title'];
        $vdescr = $vdata->fields['file_description'];
        $vtags  = $vdata->fields['file_tags'];
        $vinc   = (int) $_SESSION['USER_PINC'];

        self::$rel                         = VGenerate::prepare($vtitle);
        self::$userinfo['user_id']         = $vuid;
        self::$userinfo['user_inc']        = $vinc;
        self::$userinfo['user_name']       = $vuser;
        self::$userinfo['ch_title']        = $cuser;
        self::$userinfo['user_dname']      = ($duser != '' ? $duser : ($cuser != '' ? $cuser : $vuser));
        self::$userinfo['user_joindate']   = $ujoin;
        self::$userinfo['usr_affiliate']   = $vdata->fields['usr_affiliate'];
        self::$userinfo['usr_partner']     = $vdata->fields['usr_partner'];
        self::$userinfo['affiliate_badge'] = $vdata->fields['affiliate_badge'];

        $db_id                                = $vdata->fields['db_id'];
        $thumb_server                         = $vdata->fields['thumb_server'];
        $thumb_cache                          = $vdata->fields['thumb_cache'];
        $thumb_cache                          = $thumb_cache > 1 ? $thumb_cache : null;
        $vcateg                               = $vdata->fields['ct_name'];
        $vcateg_id                            = $vdata->fields['ct_id'];
        $vcateg_slug                          = $vdata->fields['ct_slug'];
        $ct_lang                              = unserialize($vdata->fields['ct_lang']);
        $vcateg                               = $_SESSION['fe_lang'] != 'en_US' ? ($ct_lang[$_SESSION['fe_lang']] != '' ? $ct_lang[$_SESSION['fe_lang']] : $vdata->fields['ct_name']) : $vdata->fields['ct_name'];
        $vcategid                             = $vdata->fields['ct_id'];
        $vpriv                                = $vdata->fields['privacy'];
        $vappr                                = $vdata->fields['approved'];
        $vdel                                 = $vdata->fields['deleted'];
        $vactive                              = $vdata->fields['active'];
        $vlike                                = $vdata->fields['file_like'];
        $vdislike                             = $vdata->fields['file_dislike'];
        $vcomm                                = $vdata->fields['comments'];
        $vchat                                = $vdata->fields['stream_chat'];
        $vsrc                                 = $vdata->fields['file_type'];
        $followcount                          = $vdata->fields['usr_followcount'];
        $subcount                             = $vdata->fields['usr_subcount'];
        $old_key                              = $vdata->fields['old_file_key'];
        $has_pv                               = $vdata->fields['has_preview'];
        $stream_live                          = $vdata->fields['stream_live'];
        $stream_ended                         = $vdata->fields['stream_ended'];
        $stream_key                           = $vdata->fields['stream_key'];
        $embed_src                            = $vdata->fields['embed_src'] != '' ? $vdata->fields['embed_src'] : 'local';
        $embed_key                            = (self::$type[0] == 'v' or self::$type[0] == 's') ? $vdata->fields['embed_key'] : null;
        $embed_url                            = (self::$type[0] == 'v' or self::$type[0] == 's') ? $vdata->fields['embed_url'] : null;
        $vrate                                = self::$cfg['file_rating'] == 1 ? $vdata->fields['rating'] : 0;
        $vrespond                             = self::$cfg['file_responses'] == 1 ? $vdata->fields['responding'] : 0;
        $session_id                           = (int) $_SESSION['USER_ID'];
        $session_name                         = $_SESSION['USER_NAME'];
        $vembed                               = self::$cfg['file_embedding'] == 1 ? $vdata->fields['embedding'] : 0;
        $vsocial                              = self::$cfg['file_social_sharing'] == 1 ? $vdata->fields['social'] : 0;
        self::$userinfo['upload_category']    = $vcateg;
        self::$userinfo['upload_category_id'] = $vcategid;
        self::$userinfo['user_subtotal']      = $subcount;
        self::$userinfo['user_followtotal']   = $followcount;
        /* default error message */
        $verr = null;
        $err1 = self::errorMessage(self::$type, self::$language['notif.error.available.file']);
        /* running checks */
        if ($vdel == 1) {
//deleted
            $verr = self::errorMessage(self::$type, self::$language['notif.error.deleted.file']);
        } elseif ($vactive == 0) {
//suspeded/inactive
            $verr = $err1;
        } elseif ($vappr == 0) {
//pending approval
            if ($session_id > 0 and $session_id == $vuid) {
                $verr = '';
                $vmsg = self::errorMessage(self::$type, self::$language['notif.message.pending']);
            } else {
                $verr = $err1;
            }
        }

        /* responses */
        if (self::$cfg['file_responses'] == 1 and $verr == '') {
            $sql        = sprintf("SELECT * FROM `db_%sresponses` WHERE `file_key`='%s' AND `active`='1';", self::$type, self::$file_key);
            $vq         = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_responses'], $sql) : self::$db->execute($sql);
            $vresponses = $vq->getrows();
        }
        /* favorites */
        if (self::$cfg['file_favorites'] == 1 and $verr == '') {
            $sql        = sprintf("SELECT `db_id` FROM `db_%sfavorites` WHERE `file_key`='%s' AND `usr_id`='%s' LIMIT 1;", self::$type, self::$file_key, (int) $_SESSION['USER_ID']);
            $vf         = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_responses'], $sql) : self::$db->execute($sql);
            $isfavorite = $vf->fields['db_id'] ? 1 : 0;
        }
        /* watchlist */
        if (self::$cfg['file_watchlist'] == 1 and $verr == '') {
            $sql         = sprintf("SELECT `db_id` FROM `db_%swatchlist` WHERE `file_key`='%s' AND `usr_id`='%s' LIMIT 1;", self::$type, self::$file_key, (int) $_SESSION['USER_ID']);
            $vf          = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_responses'], $sql) : self::$db->execute($sql);
            $iswatchlist = $vf->fields['db_id'] ? 1 : 0;
        }

        /* member status */
        $is_sub = 0;
        if ($session_id > 0 and $verr == '') {
            $view_perm_cache = self::$db_cache ? self::$cfg['cache_view_check_perm'] : false;
            $verr            = $verr == '' ? VUseraccount::checkPerm('view', self::$type[0], $view_perm_cache) : $verr; //check view permissions
            $view_fr_cache   = self::$db_cache ? self::$cfg['cache_view_friend_status'] : false;
            $f_is            = VContacts::getFriendStatus($vuid, $view_fr_cache); //am I friend
            $view_bl_cache   = self::$db_cache ? self::$cfg['cache_view_block_status'] : false;
            $bl_stat         = VContacts::getBlockStatus($vuid, $session_name, $view_bl_cache); //am I blocked
            $view_blc_cache  = self::$db_cache ? self::$cfg['cache_view_block_cfg'] : false;
            $bl_opt          = VContacts::getBlockCfg('bl_files', $vuid, $session_name, $view_blc_cache); //is file access blocked
            $bl_sub          = VContacts::getBlockCfg('bl_subscribe', $vuid, $session_name, $view_blc_cache); //is subscribing blocked
        }

        /* if viewing playlists, run privacy all the checks */
        if (isset($_GET['p']) and $verr == '') {
            $pl_id = self::$filter->clr_str($_GET['p']);
            $pli   = self::$db->execute(sprintf("SELECT `usr_id`, `pl_privacy` FROM `db_%splaylists` WHERE `pl_key`='%s' LIMIT 1;", self::$type, $pl_id));
            $ppriv = $pli->fields['pl_privacy'];
            $ppuid = $pli->fields['usr_id'];

            switch ($ppriv) {
                case "private":
                case "personal":
                case "public":
                    $err1 = self::errorMessage('playlist', self::$language['notif.error.available.file']);
                    if ($verr == '' and $vmsg == '') {
                        if ($session_id > 0 and $bl_stat == 1 and $bl_opt == 1) {
//check if blocked but access to viewing is allowed
                            $verr = self::$language['notif.error.blocked.request'];
                            $vmsg = '';
                        } elseif ($session_id > 0 and $session_id == $ppuid and $ppriv != 'public') {
//check if own file
                            $verr = '';
                            $vmsg = self::errorMessage(self::$language['frontend.global.p'], self::$language["notif.message." . $ppriv]);
                        } else {
                            if ($ppriv == 'private' and $session_id > 0) {
//check permissions for viewing private files/playlists
                                if ($f_is == 1 and ($bl_stat == 0 or ($bl_stat == 1 and $bl_opt == 0))) {
                                    $err1 = null;
                                    $vmsg = self::errorMessage(self::$type, self::$language["notif.message." . $vpriv]);
                                }
                            }
                            $verr = $ppriv != 'public' ? $err1 : null;
                        }
                    }
                    break;
            }
        }

        /* if viewing a regular file, same checks are needed */
        if ($verr == '') {
            switch ($vpriv) {
                case "private":
                case "personal":
                case "public":
                    if ($verr == '' and $vmsg == '') {
                        if ($session_id > 0 and $bl_stat == 1 and $bl_opt == 1) {
//check if blocked but access to viewing is allowed
                            $verr = self::$language['notif.error.blocked.request'];
                            $vmsg = '';
                        } elseif ($session_id > 0 and $session_id == $vuid and $vpriv != 'public') {
//check if own file
                            $verr = '';
                            $vmsg = self::errorMessage(self::$type, self::$language["notif.message." . $vpriv]);
                        } else {
                            if ($vpriv == 'private' and $session_id > 0) {
//check permissions for viewing private files
                                if ($f_is == 1 and ($bl_stat == 0 or ($bl_stat == 1 and $bl_opt == 0))) {
                                    $err1 = null;
                                    $vmsg = self::errorMessage(self::$type, self::$language["notif.message." . $vpriv]);
                                }
                            }
                            $verr = $vpriv != 'public' ? $err1 : null;
                        }
                    }
                    break;
            }
        }
        /* error out */
        if ($verr != '') {
            global $smarty;
            $smarty->assign("blocked", ($bl_stat == 1 and $bl_opt == 1));

            echo '<div class="error-message-text stick">' . $verr . '</div>';
            return false;
        }
        /* shorts next/prev */
        if (self::$is_shorts) {
            $sn         = self::$db->execute(sprintf("SELECT `file_key` FROM `db_%sfiles` WHERE `db_id` > %s AND `privacy`='public' AND `approved`='1' AND `deleted`='0' AND `active`='1' ORDER BY `db_id` ASC LIMIT 1;", self::$type, $db_id));
            $sp         = self::$db->execute(sprintf("SELECT `file_key` FROM `db_%sfiles` WHERE `db_id` < %s AND `privacy`='public' AND `approved`='1' AND `deleted`='0' AND `active`='1' ORDER BY `db_id` DESC LIMIT 1;", self::$type, $db_id));
            $short_next = $sn->fields['file_key'];
            $short_prev = $sp->fields['file_key'];
        }
        /* get file votes */
        $like_active    = null;
        $dislike_active = null;
        $vsql           = sprintf("SELECT `file_votes` FROM `db_%srating` WHERE `file_key`='%s' LIMIT 1;", self::$type, self::$file_key);
        $vinfo          = self::$db->execute($vsql);
        $votes          = unserialize($vinfo->fields['file_votes']);
        if (isset($votes[0])) {
            foreach ($votes as $k => $vote) {
                if ($vote['usr_id'] == $_SESSION['USER_ID']) {
                    if ($vote['usr_vote'] == 1) {
                        $like_active = ' active';
                    }

                    if ($vote['usr_vote'] == 0) {
                        $dislike_active = ' active';
                    }

                    break;
                }
            }
        }

        /* now we can update views and history */
        if (!isset($_GET['do'])) {
            if ($session_id > 0 or ($session_id == 0 and self::$cfg["guest_view_" . self::$type] == 1)) {
                $update_views    = ($session_id == 0 or ($session_id > 0 and $session_id != $vuid)) ? self::updateViewLogs('files') : null;
                $update_history  = self::updateHistory();
                $update_pl_views = isset($_GET['p']) ? self::updatePlaylistViews($pl_id, self::$type) : null;
            }
        }
        /* user subscription status */
        if (self::$cfg['user_subscriptions'] == 1) {
            $sql = sprintf("SELECT `db_id` FROM `db_subscribers` WHERE `usr_id`='%s' AND `sub_id`='%s' LIMIT 1;", (int) $vuid, $session_id);
            $rs  = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_sub_id'], $sql) : self::$db->execute($sql);
            if ($rs->fields['db_id']) {
                $is_sub = 1;
            }

            if ($is_sub == 0) {
                $ts = self::$db->execute(sprintf("SELECT `db_id` FROM `db_subtemps` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `expire_time`>='%s' AND `active`='1' LIMIT 1;", (int) $_SESSION['USER_ID'], (int) $vuid, date("Y-m-d H:i:s")));
                if ($ts->fields['db_id']) {
                    $is_sub = 1;
                }
            }

            self::$userinfo['user_issub'] = $is_sub;
        }
        /* user follows status */
        if (self::$cfg['user_follows'] == 1) {
            $is_sub = 0;
            $sql    = sprintf("SELECT `db_id` FROM `db_followers` WHERE `usr_id`='%s' AND `sub_id`='%s' LIMIT 1;", (int) $vuid, $session_id);
            $rs     = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_sub_id'], $sql) : self::$db->execute($sql);
            if ($rs->fields['db_id']) {
                $is_sub = 1;
            }

            self::$userinfo['user_isfollow'] = $is_sub;
        }
        /* like/dislike percentage bar */
        $vlike_txt = $vrate == 1 ? self::likeStatBars($vrate, $vlike, $vdislike) : null;

        $thumbs = new VBrowse(self::$type);

        $embed_html   = null;
        $email_html   = null;
        $perma_html   = null;
        $down_html    = null;
        $flag_html    = null;
        $visitor_html = '<div class="tabs_signin">' . str_replace(array('##SIGNIN##', '##SIGNUP##'), array('<a href="' . self::$cfg['main_url'] . '/' . VHref::getKey("signin") . '?next=' . (!self::$is_shorts ? VGenerate::fileHref(self::$type[0], self::$file_key) : VHref::getKey("shorts") . '/' . self::$file_key) . '" rel="nofollow">' . self::$language['frontend.global.signin'] . '</a>', '<a href="' . self::$cfg['main_url'] . '/' . VHref::getKey("signup") . '" rel="nofollow">' . self::$language['frontend.global.createaccount'] . '</a>'), self::$language['view.files.use.please']) . '</div>';

        if (self::$cfg['video_player'] == 'vjs' or self::$cfg['audio_player'] == 'vjs') {
            $_cfg = unserialize(self::$dbc->singleFieldValue('db_fileplayers', 'db_config', 'db_name', 'vjs_local'));
        }

        /* JAVASCRIPT */
        $ht_js = 'var c_url = current_url+menu_section+"?' . self::$type[0] . '=' . self::$file_key . '";';

        /* embed tab content */
        if ($vembed == 1) {
            /* embed code player sizes */
            $ps         = array();
            $ps[0]['w'] = 560;
            $ps[0]['h'] = 315;

            $ps[1]['w'] = 640;
            $ps[1]['h'] = 360;

            $ps[2]['w'] = 853;
            $ps[2]['h'] = 480;

            $ps[3]['w'] = 1280;
            $ps[3]['h'] = 720;

            switch (self::$type[0]) {
                case "l":
                case "v":
                case "s":
                    if ($vsrc == 'embed') {
                        $ec = VPlayers::playerEmbedCodes($embed_src, array("url" => $embed_url, "key" => $embed_key, "cfg" => $_cfg), $ps[0]['w'], $ps[0]['h']);
                    } else {
                        $ec = '<iframe id="file-embed-' . md5($vid) . '" type="text/html" width="' . $ps[0]['w'] . '" height="' . $ps[0]['h'] . '" src="' . self::$cfg['main_url'] . '/' . VHRef::getKey('embed') . '?' . self::$type[0] . '=' . $vid . '" frameborder="0" allowfullscreen></iframe>';
                    }
                    break;
                case "a":
                case "b":
                    $ec = '<iframe id="file-embed-' . md5($vid) . '" type="text/html" width="' . $ps[0]['w'] . '" height="' . $ps[0]['h'] . '" src="' . self::$cfg['main_url'] . '/' . (self::$type[0] == 'a' ? VHRef::getKey('embed') : VHRef::getKey('embed_blog')) . '?' . self::$type[0] . '=' . $vid . '" frameborder="0" allowfullscreen></iframe>';
                    break;
                case "d": //embed code for documents is generated from within player cfg after page load (class.players.php)
                    $ec = '<iframe id="file-embed-' . md5($vid) . '" type="text/html" width="' . $ps[0]['w'] . '" height="' . $ps[0]['h'] . '" src="' . self::$cfg['main_url'] . '/' . VHRef::getKey('embed_doc') . '?' . self::$type[0] . '=' . $vid . (VHref::isMobile() ? '&m' : null) . '" frameborder="0" allowfullscreen></iframe>';
                    break;
                case "i":
                    switch (self::$cfg['image_player']) {
                        case "jq":
                            $_js        = null;
                            $thumb_link = VGenerate::thumbSigned(self::$type, $vid, array($ukey, $thumb_cache), 0, 1, 1);
                            $image_link = VGenerate::thumbSigned(self::$type, $vid, array($ukey, $thumb_cache), 0, 1, 0);

                            $ec = '[url=' . $image_link . '][img=320x240]' . $thumb_link . '[/img][/url]';
                            break;
                        case "flow":
                            break;
                    }
                    break;
            }

            $embed_html .= '<div id="file-share-embed-wrap" class="file-share-opt d-flex d-column">';
            $embed_html .= '<div class="place-right"><a class="comm-cancel-action js-textareacopybtn ta-right" rel="nofollow" href="javascript:;"><i class="iconBe-copy"></i>' . self::$language['frontend.global.copy'] . '</a></div>';
            $embed_html .= VGenerate::simpleDivWrap('left-float row no-top-padding', '', sprintf('<form class="entry-form-class"><textarea readonly="readonly" id="file-share-embed" class="js-copytextarea view-ta-input file-share-select-link" cols="1" rows="1" name="file_share_embed">%s</textarea></form>', $ec));

            if (self::$type[0] == 'v' or self::$type[0] == 'l' or self::$type[0] == 'b' or self::$type[0] == 's' or (self::$type[0] == 'i' and (self::$cfg['image_player'] == 'jw' or self::$cfg['image_player'] == 'flow')) or (self::$type[0] == 'a' and (self::$cfg['audio_player'] == 'jw' or self::$cfg['audio_player'] == 'flow')) or (self::$type[0] == 'd' and (self::$cfg['document_player'] == 'reader' or self::$cfg['document_player'] == 'free'))) {
                $embed_html .= '<div id="embed-wh-param" class="">';
                $embed_html .= '<form class="entry-form-class"><span>' . self::$language['view.files.embed.player'] . '</label></span>';
                $embed_html .= '<ul id="embed-size-options">';
                $embed_html .= '<li>';
                $embed_html .= VGenerate::simpleDivWrap('left-float row no-top-padding pointer embed-size embed-size-on', '', VGenerate::simpleDivWrap('span-label', '', $ps[0]['w'] . ' x ' . $ps[0]['h'], '', 'span'), 'width: 49px; height: 29px; border: 1px solid #CCCCCC;', '', 'rel-w="' . $ps[0]['w'] . '" rel-h="' . $ps[0]['h'] . '"');
                $embed_html .= '</li>';
                $embed_html .= '<li>';
                $embed_html .= VGenerate::simpleDivWrap('left-float row no-top-padding pointer embed-size', '', VGenerate::simpleDivWrap('span-label', '', $ps[1]['w'] . ' x ' . $ps[1]['h'], '', 'span'), 'width: 56px; height: 33px; border: 1px solid #CCCCCC;', '', 'rel-w="' . $ps[1]['w'] . '" rel-h="' . $ps[1]['h'] . '"');
                $embed_html .= '</li>';
                $embed_html .= '<li>';
                $embed_html .= VGenerate::simpleDivWrap('left-float row no-top-padding pointer embed-size', '', VGenerate::simpleDivWrap('span-label', '', $ps[2]['w'] . ' x ' . $ps[2]['h'], '', 'span'), 'width: 64px; height: 37px; border: 1px solid #CCCCCC;', '', 'rel-w="' . $ps[2]['w'] . '" rel-h="' . $ps[2]['h'] . '"');
                $embed_html .= '</li>';
                $embed_html .= '<li>';
                $embed_html .= VGenerate::simpleDivWrap('left-float row no-top-padding pointer embed-size', '', VGenerate::simpleDivWrap('span-label', '', $ps[3]['w'] . ' x ' . $ps[3]['h'], '', 'span'), 'width: 75px; height: 44px; border: 1px solid #CCCCCC;', '', 'rel-w="' . $ps[3]['w'] . '" rel-h="' . $ps[3]['h'] . '"');
                $embed_html .= '</li>';
                $embed_html .= '<li>';
                $embed_html .= VGenerate::simpleDivWrap('left-float row no-top-padding pointer embed-size', '', VGenerate::simpleDivWrap('span-label', '', '100% x 100%', '', 'span'), 'width: 86px; height: 55px; border: 1px solid #CCCCCC;', '', 'rel-w="100%" rel-h="100%"');
                $embed_html .= '</li>';
                $embed_html .= '<li class="embed-size-custom">';
                $embed_html .= VGenerate::simpleDivWrap('span-label-off', '', '<span>' . self::$language['view.files.embed.custom'] . '</span>', '', 'span');
                $embed_html .= '<div class="clearfix"></div>';
                $embed_html .= '<div class="custom-embed-code-wrap">';
                $embed_html .= '<form class="entry-form-class">';
                $embed_html .= VGenerate::simpleDivWrap('', '', '<input type="text" name="p_width" id="p-width" class="text-input wd35 custom-size custom-width" placeholder="width">');
                $embed_html .= VGenerate::simpleDivWrap('', '', '<input type="text" name="p_height" id="p-height" class="text-input wd35 custom-size custom-height" placeholder="height">');
                $embed_html .= '</form>';
                $embed_html .= '</div>';
                $embed_html .= '</li>';
                $embed_html .= '</ul>';
                $embed_html .= '</div>';
            }
            $embed_html .= '</div>';
            /* auto select permalink, embed code */
            $ht_js .= '$(".file-share-select-link").click(function(){this.focus();this.select();});';
            $ht_js .= self::$cfg['file_seo_url'] == 1 ? '$(".file-share-perma-seo").val(document.URL);' : null;
            $ht_js .= '$(".file-share-perma-short").val("' . self::$cfg['main_url'] . '/' . VHref::getKey('watch') . '?' . self::$type[0] . '=' . self::$file_key . '");';
            /* click to change/select player size for embed code */
            $ht_js .= '$(".embed-size").click(function(){';
            $ht_js .= 'var embedCode = $("#file-share-embed").val();';
            if (!isset($_GET['d'])) {
                $ht_js .= 'embedCode = embedCode.replace(\'width="\'+$(".embed-size-on").attr("rel-w")+\'"\', \'width="\'+$(this).attr("rel-w")+\'"\').replace(\'height="\'+$(".embed-size-on").attr("rel-h")+\'"\', \'height="\'+$(this).attr("rel-h")+\'"\');';
            } else {
                $ht_js .= 'embedCode = embedCode.replace(\'width="\'+$(".embed-size-on").attr("rel-w")+\'"\', \'width="\'+$(this).attr("rel-w")+\'"\').replace(\'height="\'+$(".embed-size-on").attr("rel-h")+\'"\', \'height="\'+$(this).attr("rel-h")+\'"\');';
                $ht_js .= 'embedCode = embedCode.replace(\'value="\'+$(".embed-size-on").attr("rel-w")+\'" name=\"width\"\', \'value="\'+$(this).attr("rel-w")+\'" name=\"width\"\').replace(\'value="\'+$(".embed-size-on").attr("rel-h")+\'" name=\"height\"\', \'value="\'+$(this).attr("rel-h")+\'" name=\"height\"\');';
            }
            $ht_js .= '$(".embed-size").removeClass("embed-size-on"); $(this).addClass("embed-size-on");';
            $ht_js .= '$("#file-share-embed").val(embedCode);';
            $ht_js .= '});';
            /* set custom width and height on embed code */
            $ht_js .= '$(".custom-size").focus(function(){previous = parseInt(this.value);}).change(function(){';
            $ht_js .= 'var embedCode = $("#file-share-embed").val();';
            $ht_js .= 'var s_val = $(this).val();';
            $ht_js .= 'if(s_val > 50){'; //minimum width and height of 50
            /* setting the custom width and height */
            $ht_js .= 'if($(this).hasClass("custom-width")){';
            if (!isset($_GET['d'])) {
                $ht_js .= 'embedCode = embedCode.replace(\'width="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-w"))+\'"\', \'width="\'+$(this).val()+\'"\');';
            } else {
                $ht_js .= 'embedCode = embedCode.replace(\'width="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-w"))+\'"\', \'width="\'+$(this).val()+\'"\');';
                $ht_js .= 'embedCode = embedCode.replace(\'value="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-w"))+\'" name=\"width\"\', \'value="\'+$(this).val()+\'" name=\"width\"\');';
            }
            $ht_js .= '}';
            $ht_js .= 'if($(this).hasClass("custom-height")){';
            if (!isset($_GET['d'])) {
                $ht_js .= 'embedCode = embedCode.replace(\'height="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-h"))+\'"\', \'height="\'+$(this).val()+\'"\');';
            } else {
                $ht_js .= 'embedCode = embedCode.replace(\'height="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-h"))+\'"\', \'height="\'+$(this).val()+\'"\');';
                $ht_js .= 'embedCode = embedCode.replace(\'value="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-h"))+\'" name=\"height\"\', \'value="\'+$(this).val()+\'" name=\"height\"\');';
            }
            $ht_js .= '}';
            /* unsetting the custom width and height */
            $ht_js .= '}else{';
            $ht_js .= '$(this).val("");';
            $ht_js .= 'if($(this).hasClass("custom-width")){';
            if (!isset($_GET['d'])) {
                $ht_js .= 'embedCode = embedCode.replace(\'width="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-w"))+\'"\', \'width="\'+$(".embed-size-on").attr("rel-w")+\'"\');';
            } else {
                $ht_js .= 'embedCode = embedCode.replace(\'width="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-w"))+\'"\', \'width="\'+$(".embed-size-on").attr("rel-w")+\'"\');';
                $ht_js .= 'embedCode = embedCode.replace(\'value="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-w"))+\'" name=\"width\"\', \'value="\'+$(".embed-size-on").attr("rel-w")+\'" name=\"width\"\');';
            }
            $ht_js .= '}';
            $ht_js .= 'if($(this).hasClass("custom-height")){';
            if (!isset($_GET['d'])) {
                $ht_js .= 'embedCode = embedCode.replace(\'height="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-h"))+\'"\', \'height="\'+$(".embed-size-on").attr("rel-h")+\'"\');';
            } else {
                $ht_js .= 'embedCode = embedCode.replace(\'height="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-h"))+\'"\', \'height="\'+$(".embed-size-on").attr("rel-h")+\'"\');';
                $ht_js .= 'embedCode = embedCode.replace(\'value="\'+(previous > 0 ? previous : $(".embed-size-on").attr("rel-h"))+\'" name=\"height\"\', \'value="\'+$(".embed-size-on").attr("rel-h")+\'" name=\"height\"\');';
            }
            $ht_js .= '}';
            $ht_js .= '}';
            $ht_js .= '$("#file-share-embed").val(embedCode);';
            $ht_js .= '});';
        }
        /* email content */
        if (self::$cfg['file_email_sharing'] == 1) {
            $email_html .= '<div id="file-share-email-wrap" class="file-share-opt no-display1">';
            $email_html .= '<form id="file-share-form" method="post" action="" class="entry-form-class">';
            $email_html .= VGenerate::simpleDivWrap('', 'share-email-response', '');
            $email_html .= '<label>' . self::$language['view.files.mail.to'] . '</label>';
            $email_html .= VGenerate::simpleDivWrap('left-float row no-top-padding', '', VGenerate::basicInput('textarea', 'file_share_mailto', 'view-ta-input file-share-mailto', ''));
            $email_html .= '<label>' . self::$language['view.files.mail.note'] . '</label>';
            $email_html .= VGenerate::simpleDivWrap('left-float row no-top-padding', '', VGenerate::basicInput('textarea', 'file_share_mailnote', 'view-ta-input file-share-mailto', ''));
            $email_html .= '</form>';
            $email_html .= '</div>';
            $email_html .= VGenerate::simpleDivWrap('left-float row', '', VGenerate::basicInput('button', 'send_file_email', 'symbol-button search-button form-button post-share-email', '', 1, '<i class="icon-' . (self::$type == 'doc' ? 'file' : (self::$type == 'blog' ? 'pencil2' : self::$type)) . '"></i><span>' . self::$language['view.files.mail.btn'] . '</span>'));

            $ht_js .= '$(".post-share-email").click(function(){';
            $ht_js .= 'var t = $(this);';
            $ht_js .= 'var post_url = c_url+"&do=file-share";';
            $ht_js .= 'if($(".file-share-mailto").val() != ""){';
            $ht_js .= '$("#file-share-email-wrap").mask("");';
            $ht_js .= 't.find("i").addClass("spinner icon-spinner");';

            $ht_js .= '$.post(post_url, $("#user-files-form, #file-share-form").serialize(), function(data){';
            $ht_js .= '$("#share-email-response").html(data);';
            $ht_js .= '$("#file-share-email-wrap").unmask();';
            $ht_js .= 't.find("i").removeClass("spinner icon-spinner");';
            $ht_js .= '});}';
            $ht_js .= '});';

            if ($session_id == 0) {
                $email_html = $visitor_html;
            }
        }
        /* permalink content */
        if (self::$cfg['file_permalink_sharing'] == 1) {
            $t1 = '<label>' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0]], self::$language['view.files.share.link.short']) . '</label>';
            $t2 = '<label>' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0]], self::$language['view.files.share.link.seo']) . '</label>';

            $perma_html .= '<div id="file-share-perma-wrap" class="file-share-opt">';
            $perma_html .= '<form class="entry-form-class">';
            if (!self::$is_shorts) {
                $perma_html .= VGenerate::simpleDivWrap('', '', $t1 . '<div class="place-right"><a class="comm-cancel-action js-textareacopybtn-p1" rel="nofollow" href="javascript:;"><i class="iconBe-copy"></i>' . self::$language['frontend.global.copy'] . '</a></div>');
                $perma_html .= VGenerate::simpleDivWrap('', '', '<input readonly="readonly" type="text" value="" class="view-login-input file-share-select-link file-share-perma-short" name="file_share_permalink" />');
            }
            $perma_html .= VGenerate::simpleDivWrap('', '', $t2 . '<div class="place-right"><a class="comm-cancel-action js-textareacopybtn-p2" rel="nofollow" href="javascript:;"><i class="iconBe-copy"></i>' . self::$language['frontend.global.copy'] . '</a></div>');
            $perma_html .= VGenerate::simpleDivWrap('', '', '<input readonly="readonly" type="text" value="" class="view-login-input file-share-select-link file-share-perma-seo" name="file_share_permalink" />');
            $perma_html .= '</form>';
            $perma_html .= '</div>';
        }
        /* file downloading content */
        if (self::$cfg['file_downloads'] == 1 and $vsrc != 'embed') {
            $dl .= self::downloadLinks(self::$type, self::$file_key, $ukey);

            $down_html .= ($session_id > 0 or self::$cfg['file_download_reg'] == 0) ? VGenerate::simpleDivWrap('', 'file-down-wrap', $dl) : $visitor_html;
        }
        /* file flagging content */
        if (self::$cfg['file_flagging'] == 1) {
            for ($i = 1; $i <= 7; $i++) {
                $li .= '<li class="count wd180 file-flag-reason" id="view-files-reason-' . $i . '"><a href="javascript:;" rel="nofollow"><i class="' . ((isset($_SESSION['reason'][self::$file_key]) and $_SESSION['reason'][self::$file_key] == $i) ? 'icon-checkbox-checked' : 'icon-checkbox-unchecked') . '"></i> ' . self::$language["view.files.reason." . $i] . '</a></li>';
            }
            /* file flag wrap */
            $flag_ht = '
                    <div class="menu-drop addto-action-menu">
                        <div id="file-flag-reasons" class="dl-menuwrapper-off">
                            <button class="dl-trigger-off dl-active-off actions-trigger-off addto-action symbol-button no-display" onclick="$(this).next().stop().toggle(0, function(){$.fancybox.update()});"><i class="iconBe-chevron-down"></i>' . self::$language['view.files.select.reason'] . '</button>
                            <ul class="dl-menu dl-menuopen-off">' . $li . '</ul>
                        </div>
                    </div>
                    ';

            $flag_html .= $session_id > 0 ? VGenerate::simpleDivWrap('', 'file-flag-wrap', $flag_ht) : $visitor_html;
        }

        $phtml = null;

        $tmb_url = VBrowse::thumbnail($ukey, self::$file_key, $thumb_server, 0);
        /* player width and height */
        $_p      = VPlayers::playerInit('view');
        $_width  = $_p[1];
        $_height = $_p[2];

        switch (self::$type[0]) {
            case "l":
            case "v":
            case "s":
                $href_key = self::$type[0] == 's' ? self::$href['shorts'] : (self::$type[0] == 'l' ? self::$href['broadcasts'] : self::$href['videos']);
                $info     = array("url" => $embed_url, "key" => $embed_key, "cfg" => $_cfg);
                $p_ht     = VPlayers::playerEmbedCodes($embed_src, $info, $_width, $_height);
                break;
            case "i":
                $href_key = 'images';
                break;
            case "a":
                $href_key = 'audios';
                $p_ht     = '<center><img src="' . $tmb_url . '" onclick="jwplayer(\'view-player\').play()" /></center>';
                $p_ht     = null;
                break;
            case "d":
                $href_key = 'documents';
                if (VHref::isMobile()) {
                    $p_ht = '<div class="pdfjs-viewer"></div>';
                }
                break;
            case "b":
                $href_key  = 'blogs';
                $blog_tpl  = self::$cfg['media_files_dir'] . '/' . $ukey . '/b/' . $vid . '.tplb';
                $blog_html = null;

                if (is_file($blog_tpl)) {
                    $blog_html = file_get_contents($blog_tpl);
                    $media     = VGenerate::extract_text($blog_html);

                    if ($media[0]) {
                        foreach ($media as $media_entry) {
                            $a = explode("_", $media_entry);

                            $mtype = $a[1];
                            $mkey  = $a[2];

                            /* embed code player sizes */
                            $ps         = array();
                            $ps[0]['w'] = '100%';
                            $ps[0]['h'] = 360;

                            $ps[1]['w'] = 640;
                            $ps[1]['h'] = 360;

                            $ps[2]['w'] = 853;
                            $ps[2]['h'] = 480;

                            $ps[3]['w'] = 1280;
                            $ps[3]['h'] = 720;

                            switch ($mtype[0]) {
                                case "l":
                                case "v":
                                case "s":
                                    //embed code for video and audio is generated from within player cfg after initialization (class.players.php)
                                    $vi         = sprintf("SELECT A.`file_type`, A.`embed_src`, A.`embed_key`, A.`embed_url`, B.`usr_key` FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`file_key`='%s' LIMIT 1;", $mtype, $mkey);
                                    $mrs        = self::$db->execute($vi);
                                    $msrc       = $mrs->fields['file_type'];
                                    $membed_src = $mrs->fields['embed_src'];
                                    $membed_key = $mrs->fields['embed_key'];
                                    $membed_url = $mrs->fields['embed_url'];
                                    $mukey      = $mrs->fields['usr_key'];

                                    if ($mtype == 'short') {
                                        $ps[0]['h'] = 560;
                                    }
                                    if ($msrc == 'embed') {
                                        $mec = VPlayers::playerEmbedCodes($membed_src, array("url" => $membed_url, "key" => $membed_key, "cfg" => $_cfg), $ps[0]['w'], $ps[0]['h']);
                                    } else {
                                        $mec = '<iframe id="file-embed-' . md5($mkey) . '" type="text/html" width="' . $ps[0]['w'] . '" height="' . $ps[0]['h'] . '" src="' . self::$cfg['main_url'] . '/embed?' . $mtype[0] . '=' . $mkey . '" frameborder="0" allowfullscreen></iframe>';
                                    }
                                    break;
                                case "a": //embed code for video and audio is generated from within player cfg after initialization (class.players.php)
                                    $mec = '<iframe id="file-embed-' . md5($mkey) . '" type="text/html" width="' . $ps[0]['w'] . '" height="' . $ps[0]['h'] . '" src="' . self::$cfg['main_url'] . '/embed?a=' . $mkey . '" frameborder="0" allowfullscreen></iframe>';
                                    break;
                                case "d": //embed code for documents is generated from within player cfg after page load (class.players.php)
                                    $mobile  = VHref::isMobile();
                                    $vi      = sprintf("SELECT A.`file_type`, A.`embed_src`, A.`embed_key`, B.`usr_key` FROM `db_docfiles` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`file_key`='%s' LIMIT 1;", $mkey);
                                    $mrs     = self::$db->execute($vi);
                                    $mukey   = $mrs->fields['usr_key'];
                                    $doc_src = self::$cfg['media_files_dir'] . '/' . $mukey . '/d/' . md5(self::$cfg['global_salt_key'] . $mkey) . '.pdf';
                                    $mdoc    = VGenerate::thumbSigned(array("type" => "doc", "server" => "upload", "key" => '/' . $mukey . '/d/' . md5(self::$cfg['global_salt_key'] . $mkey) . '.pdf'), $mkey, $mukey, 0, 1);
                                    if (!is_file($doc_src)) {
                                        $doc_src = self::$cfg['media_files_dir'] . '/' . $mukey . '/d/' . $mkey . '.pdf';
                                        $mdoc    = VGenerate::thumbSigned(array("type" => "doc", "server" => "upload", "key" => '/' . $mukey . '/d/' . $mkey . '.pdf'), $mkey, $mukey, 0, 1);
                                    }

                                    $mec = ((is_file($doc_src) and !$mobile) ? '<embed src="' . $mdoc . '" width="' . $ps[0]['w'] . '" height="' . $ps[0]['h'] . '">' : ((is_file($doc_src) and $mobile) ? '<iframe id="file-embed-' . md5($mkey) . '" type="text/html" width="' . $ps[0]['w'] . '" height="' . $ps[0]['h'] . '" src="' . self::$cfg['main_url'] . '/' . VHRef::getKey('embed_doc') . '?' . $mtype[0] . '=' . $mkey . ($mobile ? '&m' : null) . '" frameborder="0" allowfullscreen></iframe>' : null));
                                    break;
                                case "i":
                                    $vi    = sprintf("SELECT A.`file_type`, A.`embed_src`, A.`embed_key`, B.`usr_key` FROM `db_imagefiles` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`file_key`='%s' LIMIT 1;", $mkey);
                                    $mrs   = self::$db->execute($vi);
                                    $mukey = $mrs->fields['usr_key'];

                                    switch (self::$cfg['image_player']) {
                                        case "jq":
                                            $_js        = null;
                                            $image_link = VGenerate::thumbSigned($mtype, $mkey, $mukey, 0, 1, 0);

                                            //$mec = '[url=' . $image_link . '][img=320x240]' . $thumb_link . '[/img][/url]';
                                            $mec = '<img src="' . $image_link . '">';
                                            break;
                                    }
                                    break;
                            }

                            $blog_html = str_replace("[' . $media_entry . ']", '<div class="blog-player-' . $mtype . '">' . $mec . '</div>', $blog_html);
                        }
                    }
                }

                $p_ht = '<div id="view-player-blog">' . $blog_html . '</div>';
                break;
        }

        $subbed   = false;
        $previews = (self::$cfg['conversion_video_previews'] == 1 and $has_pv == 1) ? true : false;
        if ($vuid > 0) {
            if ($vuid == (int) $_SESSION['USER_ID']) {
                $previews = false;
                $subbed   = true;
            } else {
                $ss = self::$db->execute(sprintf("SELECT `db_id`, `sub_list` FROM `db_subscriptions` WHERE `usr_id`='%s' LIMIT 1;", (int) $_SESSION['USER_ID']));
                if ($ss->fields['db_id']) {
                    $subs = unserialize($ss->fields['sub_list']);
                    if (in_array($vuid, $subs)) {
                        $sb = self::$db->execute(sprintf("SELECT `db_id` FROM `db_subusers` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `expire_time`>='%s' LIMIT 1;", (int) $_SESSION['USER_ID'], $vuid, date("Y-m-d H:i:s")));
                        if ($sb->fields['db_id'] > 0) {
                            $previews = false;
                            $subbed   = true;
                        } else {
                            $previews = true;
                            $subbed   = false;
                        }
                    }
                }
                if ($previews or !$subbed) {
                    $ts = self::$db->execute(sprintf("SELECT `db_id` FROM `db_subtemps` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `expire_time`>='%s' AND `active`='1' LIMIT 1;", (int) $_SESSION['USER_ID'], $vuid, date("Y-m-d H:i:s")));
                    if ($ts->fields['db_id']) {
                        $previews = false;
                        $subbed   = true;
                    }
                }
            }
        }
        $previews = !$previews ? ($old_key == 1 ? true : false) : $previews;
        $pv_html  = null;

        if ($has_pv and !$subbed and !$old_key and self::$cfg["conversion_" . self::$type . "_previews"] == 1 and $stream_live == 0 and $embed_src == 'local') {
            $pv_html = '<div class="pv-text" onclick="$(this).detach()"><span class="pv-wrap"><span>' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0]], self::$language['view.files.video.preview']) . '<i class="icon-close" onclick="$(\'.pv-text\').detach()"></i></span></span></div>';
        }

        $_cfg['vjs_advertising'] = self::$smarty->getTemplateVars('vjs_advertising');

        if ($_cfg['vjs_advertising'] == 1 and $subbed == 1) {
            $_cfg['vjs_advertising'] = 0;
        }

        $ad_client = self::$smarty->getTemplateVars('ad_client');

        if (self::$type == 'image') {
            $tmb_url_2 = VGenerate::thumbSigned(array("type" => "image", "server" => "upload", "key" => '/' . $ukey . '/i/' . ($previews ? $vid : md5(self::$cfg['global_salt_key'] . $vid)) . '.jpg'), $vid, array($ukey, $thumb_cache), 0, 1);
            $p_ht      = self::$cfg['image_player'] != 'flow' ? '<a class="main-thumb fancybox" href="' . $tmb_url_2 . '" title="' . $vtitle . '" rel="nofollow"><img src="' . $tmb_url . '" /></a>' : null;
        }

        $phtml .= (VHref::isMobile() and self::$type[0] == 'd') ? '
        <div class="pdftoolbar">
            <div class="d-flex ai-center jc-c mb-10">
                <button class="save-entry-button button-grey search-button form-button mb-0" id="pdfViewer-first"><i class="icon-first"><span class="no-display">skip_first</span></i></button>
                <button class="save-entry-button button-grey search-button form-button mb-0" id="pdfViewer-prev"><i class="icon-previous"><span class="no-display">skip_previous</span></i></button>
                <span class="pageno me-10">0/0</span>
                <button class="save-entry-button button-grey search-button form-button mb-0" id="pdfViewer-next"><i class="icon-next"><span class="no-display">skip_next</span></i></button>
                <button class="save-entry-button button-grey search-button form-button mb-0" id="pdfViewer-last"><i class="icon-last"><span class="no-display">skip_last</span></i></button>
            </div>
            <div class="d-flex ai-center jc-c mb-10">
                <button class="save-entry-button button-grey search-button form-button mb-0" id="pdfViewer-zoomOut"><i class="icon-zoomout"><span class="no-display">zoom_out</span></i></button>
                <span class="zoomval me-10">0%</span>
                <button class="save-entry-button button-grey search-button form-button mb-0" id="pdfViewer-zoomIn"><i class="icon-zoomin"><span class="no-display">zoom_in</span></i></button>
                <span class="me-10">&nbsp;</span>
                <button class="save-entry-button button-grey search-button form-button mb-0" id="pdfViewer-zoomWidth"><i class="icon-text-width"><span class="no-display">swap_horiz</span></i></button>
                <button class="save-entry-button button-grey search-button form-button mb-0" id="pdfViewer-zoomHeight"><i class="icon-text-height"><span class="no-display">swap_vert</span></i></button>
                <button class="save-entry-button button-grey search-button form-button mb-0" id="pdfViewer-zoomFit"><i class="icon-expand"><span class="no-display">fit_screen</span></i></button>
            </div>
        </div>' : null;

        $user_theme = isset($_SESSION['USER_THEME']) ? $_SESSION['USER_THEME'] : $_SESSION['theme_name'];
        $phtml .= self::$is_shorts ? '<div id="shorts-preloader"><img id="shorts-preloader-img" src="' . self::$cfg['global_images_url'] . '/loading-' . (strpos($user_theme, 'dark') !== false ? 'dark' : 'light') . '-shorts.gif"></div>' : null;
        $phtml .= '<div id="view-player" class="' . (self::$is_shorts ? 'no-display ' : null) . 'center' . ((((self::$type[0] == 'v' or self::$type[0] == 'l' or self::$type[0] == 's' or self::$type[0] == 'a') and self::$cfg['video_player'] == 'vjs') ? (!self::$is_shorts ? ' vjs-hd' : null) : null)) . '" style="width: ' . $_width . ';' . (self::$type[0] == 'd' ? 'height: ' . $_height . ';' : null) . (!self::$is_shorts ? ' overflow: hidden;' : null) . '">'; //PLAYER HERE
        if (($_cfg['vjs_advertising'] == 1 and !VHref::isMobile()) or ($_cfg['vjs_advertising'] == 1 and VHref::isMobile() and $embed_src == 'local' and $ad_client == 'vast') or ($_cfg['vjs_advertising'] == 1 and VHref::isMobile() and $embed_src != 'local') or ($_cfg['vjs_advertising'] == 0 and (!VHref::isMobile() or (VHref::isMobile() and $embed_src == 'vimeo') or (VHref::isMobile() and ($embed_src == 'local' or $embed_src == 'youtube')))) or (self::$type[0] == 'd' or self::$type[0] == 'i' or self::$type[0] == 'b')) {
            $phtml .= ((self::$type[0] == 'v' or self::$type[0] == 'l' or self::$type[0] == 's') and self::$cfg['video_player'] == 'vjs' and ($embed_src == 'local' or $embed_src == 'youtube')) ? '<video id="view-player-' . $vid . '" preload="none" class="video-js vjs-default-skin ' . $_cfg['vjs_skin'] . ' vjs-big-play-centered" playsinline' . ($_cfg['vjs_autostart'] == '1' ? ' autoplay muted' : null) . ' controlsList="nodownload" oncontextmenu="return false;">' : null;
            $phtml .= (self::$type[0] == 'a' and self::$cfg['audio_player'] == 'vjs') ? '<' . self::$type . ' id="view-player-' . $vid . '" preload="none" class="video-js vjs-default-skin ' . $_cfg['vjs_skin'] . ' vjs-big-play-centered" playsinline controlsList="nodownload" oncontextmenu="return false;">' : null;
            $phtml .= (self::$type[0] != 'a') ? $p_ht : null;
        } else {
            $colors = array('blue' => '#06a2cb', 'cyan' => '#00997a', 'green' => '#199900', 'orange' => '#f28410', 'pink' => '#ec7ab9', 'purple' => '#b25c8b', 'red' => '#dd1e2f');
            $ccode  = '#06a2cb';
            foreach ($colors as $cn => $cc) {
                if (strpos($cfg['theme_name'], $cn) !== false) {
                    $ccode = $cc;
                }
            }
            $svg = '<svg version="1.1" id="play" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" height="100px" width="100px" viewBox="0 0 100 100" enable-background="new 0 0 100 100" xml:space="preserve"><path class="stroke-solid" fill="none" stroke="grey" d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7C97.3,23.7,75.7,2.3,49.9,2.5"/><path class="stroke-dotted" fill="none" stroke="#666"  d="M49.9,2.5C23.6,2.8,2.1,24.4,2.5,50.4C2.9,76.5,24.7,98,50.3,97.5c26.4-0.6,47.4-21.8,47.2-47.7C97.3,23.7,75.7,2.3,49.9,2.5"/><path class="icon" fill="#666" d="M38,69c-1,0.5-1.8,0-1.8-1.1V32.1c0-1.1,0.8-1.6,1.8-1.1l34,18c1,0.5,1,1.4,0,1.9L38,69z"/></svg>';
            $phtml .= $_cfg['vjs_advertising'] == 1 ? '<div id="ima-placeholder" style="background: #000 url(' . $tmb_url . ') no-repeat center center; z-index: 100; background-size: contain;">' . $svg . '</div>' : null;
        }

        if (!VHref::isMobile() and ((self::$cfg['video_player'] == 'vjs' and (self::$type[0] == 'v' or self::$type[0] == 'l')) or (self::$cfg['audio_player'] == 'vjs' and self::$type[0] == 'a'))) {
            $first_sub  = null;
            $other_sub  = null;
            $thumb_file = self::$cfg['media_files_dir'] . '/' . $ukey . '/t/' . $vid . '/p' . (!$previews ? '/' . md5(self::$cfg['global_salt_key'] . $vid) : null) . '/thumbnails.vtt';

            if (is_file($thumb_file)) {
                $first_sub .= '<track kind="metadata" src="' . self::$cfg['media_files_url'] . '/' . $ukey . '/t/' . $vid . '/p' . (!$previews ? '/' . md5(self::$cfg['global_salt_key'] . $vid) : null) . '/thumbnails.vtt"></track>';
            }

            $sub_file = self::$dbc->singleFieldValue('db_' . self::$type . 'subs', 'vjs_subs', 'file_key', $vid);

            if ($sub_file != '') {
                $sub_arr = unserialize($sub_file);

                foreach ($sub_arr as $sub_file => $sub_arr) {
                    if ($sub_arr['default'] == '1') {
                        $first_sub .= '<track kind="captions" default src="' . self::$cfg['main_url'] . '/f_data/data_subtitles/' . VPlayers::FPsubtitle($sub_file) . '" label="' . $sub_arr['label'] . '" />';
                    } else {
                        $other_sub .= '<track kind="captions" src="' . self::$cfg['main_url'] . '/f_data/data_subtitles/' . VPlayers::FPsubtitle($sub_file) . '" label="' . $sub_arr['label'] . '" />';
                    }
                }
            }
            $phtml .= $first_sub . $other_sub;
        }
        $phtml .= '</div>';

        if (!self::$is_shorts and $embed_src == 'local' and $_cfg['vjs_advertising'] == 1) {
            if ((self::$cfg['video_player'] == 'vjs' and (self::$type[0] == 'v' or self::$type[0] == 'l')) or (self::$cfg['audio_player'] == 'vjs' and self::$type[0] == 'a')) {
                if ($_cfg['vjs_advertising'] == 1 and $ad_client != 'vast') {
                    $phtml .= '<div id="ima-companionDiv"><script type="text/javascript">$(document).ready(function(){if (typeof googletag != "undefined") {googletag.cmd.push(function() { googletag.display("ima-companionDiv"); });}});</script></div>';
                    $phtml .= '<script type="text/javascript">$(document).ready(function(){var script = document.createElement("script"); script.src = "' . self::$cfg['scripts_url'] . '/shared/videojs/"+compjs+".js"; script.onload = function () { if (compjs == "ads.comp" || compjs == "ads.mob") {var ads = new Ads();} }; document.head.appendChild(script); }); </script>';
                }
            }
        }

        $vuid     = self::$userinfo['user_id'];
        $susr_key = self::$dbc->singleFieldValue('db_accountuser', 'usr_key', 'usr_id', $vuid, (self::$db_cache ? $cfg['cache_view_related'] : false));
        $_suser   = self::$userinfo['user_name'];

        if (self::$cfg['user_subscriptions'] == 1) {
            $is_sub = self::$userinfo['user_issub'];
            $sub_nr = self::$userinfo['user_subtotal'];

            $sub_txt = ($vuid == (int) $_SESSION['USER_ID'] or (int) $_SESSION['USER_ID'] == 0) ? self::$language['frontend.global.subscribe'] : ($is_sub == 1 ? self::$language['frontend.global.unsubscribe'] : self::$language['frontend.global.subscribe']);
            $sub_cls = ($vuid == intval($_SESSION['USER_ID']) or intval($_SESSION['USER_ID']) == 0) ? 'no-sub' : ($is_sub == 1 ? 'unsubscribe-button' : 'subscribe-button');
            $sub_cls .= intval($_SESSION['USER_ID']) == 0 ? ' showSingle-lb-login" target="subscribe' : null;
        }
        if (self::$cfg['user_follows'] == 1) {
            $is_follow = self::$userinfo['user_isfollow'];
            $follow_nr = self::$userinfo['user_followtotal'];

            $follow_txt = ($vuid == (int) $_SESSION['USER_ID'] or (int) $_SESSION['USER_ID'] == 0) ? self::$language['frontend.global.follow'] : ($is_follow == 1 ? self::$language['frontend.global.unfollow'] : self::$language['frontend.global.follow']);
            $follow_cls = ($vuid == intval($_SESSION['USER_ID']) or intval($_SESSION['USER_ID']) == 0) ? 'no-sub' : ($is_follow == 1 ? 'unfollow-action' : 'follow-action');
            $follow_cls .= intval($_SESSION['USER_ID']) == 0 ? ' showSingle-lb-login" target="follow' : null;
        }

        if (self::$cfg['live_chat'] == 1 and $vchat == 1 and self::$type[0] == 'l') {
            $salt                  = self::$cfg['live_chat_salt'];
            $reg                   = (int) $_SESSION['USER_ID'] > 0 ? true : false;
            $cip                   = VServer::get_remote_ip();
            $cua                   = md5($cip . $salt);
            $badge                 = ((isset($_SESSION['USER_AFFILIATE']) and (int) $_SESSION['USER_AFFILIATE'] == 1) or (isset($_SESSION['USER_PARTNER']) and (int) $_SESSION['USER_PARTNER'] == 1)) ? '1' : '0';
            $cu                    = $reg ? self::ra($_SESSION['USER_NAME']) : 'Guest' . rand(1, 999999999);
            $_SESSION['chat_post'] = !isset($_SESSION['chat_post']) ? 'off' : $_SESSION['chat_post'];
            self::$chat_key        = md5(self::$file_key . $vuid . (int) $_SESSION['USER_ID'] . $cu . $cip . $salt);
            if (!is_array($_SESSION['chat_key'])) {
                $_SESSION['chat_key'] = array();
            }

            $_SESSION['chat_key'][self::$file_key] = self::$chat_key;

            $first = 0;
            if ($vuid != (int) $_SESSION['USER_ID']) {
                $csql  = sprintf("SELECT `db_id` FROM `db_livechat` WHERE `usr_id`='%s' AND `channel_id`='%s' LIMIT 1;", (int) $_SESSION['USER_ID'], $vuid);
                $cr    = self::$db->execute($csql);
                $first = !$cr->fields['db_id'] ? 1 : 0;
            }

            $csql = sprintf("SELECT `channel_id`, `usr_id`, `usr_key`, `chat_user`, `chat_display` FROM `db_livechat` WHERE `stream_id`='%s' AND `chat_id`='%s' LIMIT 1;", $vid, self::$chat_key);

            $cr = self::$db->execute($csql);

            if (!$cr->fields['channel_id']) {
                $data = array(
                    "first" => $first,
                    "inc"   => self::$userinfo['user_inc'],
                    "a"     => self::$chat_key,
                    "b"     => self::$file_key,
                    "c"     => trim($cu),
                    "cd"    => trim($_SESSION['USER_DNAME'] != '' ? $_SESSION['USER_NAME'] : $cu),
                    "d"     => $cip,
                    "e"     => $vuid,
                    "f"     => ($reg ? (string) $_SESSION['USER_ID'] : '0'),
                    "g"     => $cua,
                    "h"     => $vuser,
                    "i"     => ($reg ? (string) $_SESSION['USER_KEY'] : '0'),
                    "j"     => $badge,
                    "k"     => $stream_live,
                );
                $db_data = array(
                    "first"          => $first,
                    "usr_profileinc" => self::$userinfo['user_inc'],
                    "chat_id"        => self::$chat_key,
                    "is_live"        => (int) $stream_live,
                    "channel_id"     => $vuid,
                    "channel_owner"  => $vuser,
                    "usr_id"         => (int) $_SESSION['USER_ID'],
                    "usr_key"        => $_SESSION['USER_KEY'],
                    "stream_id"      => self::$file_key,
                    "chat_user"      => $cu,
                    "chat_display"   => trim($_SESSION['USER_DNAME'] != '' ? $_SESSION['USER_NAME'] : $cu),
                    "chat_ip"        => $cip,
                    "chat_fp"        => $cua,
                    "chat_time"      => date("Y-m-d H:i:s"),
                    "badge"          => $badge,
                    "logged_in"      => ($reg ? 1 : 0),
                );
                $data_string = json_encode($data);

                $insert = self::$dbc->doInsert('db_livechat', $db_data);
            } else {
                $data = array(
                    "first" => $first,
                    "inc"   => self::$userinfo['user_inc'],
                    "a"     => self::$chat_key,
                    "b"     => self::$file_key,
                    "c"     => ($reg ? $cu : $cr->fields['chat_user']),
                    "cd"    => trim($reg ? ($_SESSION['USER_DNAME'] != '' ? $_SESSION['USER_NAME'] : $cu) : $cr->fields['chat_display']),
                    "d"     => $cip,
                    "e"     => $vuid,
                    "f"     => ($reg ? (string) $_SESSION['USER_ID'] : '0'),
                    "g"     => $cua,
                    "h"     => $vuser,
                    "i"     => ($reg ? (string) $_SESSION['USER_KEY'] : '0'),
                    "j"     => $badge,
                    "k"     => $stream_live,
                );
                $data_string = json_encode($data);
            }

            //$uu = $_SESSION['live_chat_server'].'/'.VHref::getKey("chat_url_1").'/'.self::$chat_key.'/'.self::$file_key
            $uu = self::$cfg['main_url'] . '/f_modules/m_frontend/m_cron/chat-server/chat.php';
            $ch = curl_init($uu);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, CA_CERT);
            // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, CA_CERT);
            // curl_setopt($ch, CURLOPT_VERBOSE, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Access-Control-Allow-Origin: *',
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data_string))
            );

            curl_exec($ch) . "\n";
            curl_close($ch);

            $html = null;
            $vmsg = (isset($_GET['fsn']) and isset($_GET['l'])) ? self::$language['notif.success.subscribe.extra'] : (isset($_GET['fsn']) ? self::$language['notif.success.subscribe'] : $vmsg);
            $vmsg = (isset($_GET['fst']) and isset($_GET['l'])) ? self::$language['notif.success.tokens'] : $vmsg;

            if (isset($_GET['fsn']) and isset($_GET['l'])) {
                $html .= VGenerate::declareJS('$(document).ready(function(){if(typeof $("#vs-chat").html()!=="undefined"){setTimeout(function(){document.getElementById("vs-chat").contentWindow.postMessage({"viz":"not","location":window.location.href},"' . $_SESSION['live_chat_server'] . '");},30000);setTimeout(function(){document.getElementById("vs-chat").contentWindow.postMessage({"viz":"not","location":window.location.href},"' . $_SESSION['live_chat_server'] . '");},60000);}});');
            }
        }
        /* update viewers number and likes every 10 minutes */
        if (self::$type == 'live' and $stream_live == 1 and $stream_ended == 0) {
            $ljs = '(function(){
            if(typeof document.getElementById("live-viewers")!=="undefined"){
                var u = "' . self::$cfg['main_url'] . '/' . VHref::getKey("viewers") . '?t=' . self::$file_key . '";
                $.get(u, function(data){ $("#live-viewers").text(data); });
                var u = "' . self::$cfg['main_url'] . '/' . VHref::getKey("viewers") . '?l=' . self::$file_key . '";
                $.get(u, function(data){ $(".likes_count").text(data); });
            }
            setTimeout(arguments.callee, 600000); }
            )();';
            $html .= VGenerate::declareJS($ljs);
        }
        $mobile = VHref::isMobile();

        $_uimg = VUseraccount::getProfileImage(self::$userinfo['user_id']);

        if ($is_sub) {
            $ub   = 1;
            $suid = self::$dbc->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $susr_key, (self::$db_cache ? self::$cfg['cache_home_subs_follows'] : false));
            $sql  = sprintf("SELECT A.`db_id`, A.`expire_time`, B.`pk_name` FROM `db_subusers` A, `db_subtypes` B WHERE A.`usr_id`='%s' AND A.`usr_id_to`='%s' AND A.`pk_id`=B.`pk_id` AND A.`pk_id`>'0' LIMIT 1;", (int) $_SESSION['USER_ID'], $suid);
            $nn   = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_home_subs_follows'], $sql) : self::$db->execute($sql);
            $sn   = $nn->fields['pk_name'] . '<br><span class="csm"><label class="">' . self::$language['frontend.global.active.until'] . '</label> ' . $nn->fields['expire_time'] . '</span>';

            if (!$nn->fields['db_id']) {
                $ub  = 0;
                $sql = sprintf("SELECT A.`db_id`, A.`expire_time`, B.`pk_name` FROM `db_subtemps` A, `db_subtypes` B WHERE A.`usr_id`='%s' AND A.`usr_id_to`='%s' AND A.`pk_id`=B.`pk_id` AND A.`pk_id`>'0' AND A.`expire_time`>='%s' LIMIT 1;", (int) $_SESSION['USER_ID'], $suid, date("Y-m-d H:i:s"));
                $nn  = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_home_subs_follows'], $sql) : self::$db->execute($sql);
                $sn  = $nn->fields['pk_name'] . '<br><span class="csm"><label class="">' . self::$language['frontend.global.active.until'] . '</label> ' . $nn->fields['expire_time'] . '</span>';
            }
        }

        /* GO PAGE LAYOUT */
        $html .= '<div class="page_holder">
                <div class="page_holder_left">
                ' . (self::$is_shorts ? '<div id="shorts-arrows"><div id="shorts-arrows-wrap">' . ($short_prev ? '<div id="shorts-arrow-up" class="showSingle-lb sh_button btn_auto" rel-prev="' . $short_prev . '"><i class="icon-arrow-up2"></i></div>' : null) . ($short_next ? '<div id="shorts-arrow-down" class="showSingle-lb sh_button btn_auto" rel-next="' . $short_next . '"><i class="icon-arrow-down2"></i></div>' : null) . '</div></div>' : null) . '

                ' . VGenerate::advHTML(array(16, 50, $vid)) . '

                <div class="video_player_holder ' . (self::$type[0] != 'd' ? $embed_src : 'd' . ($mobile ? ' local' : null)) . '">
                    ' . $phtml . '
                </div>';

        $html .= $vmsg != '' ? '<div class="notice-message-text" onclick="jQuery(this).detach();">' . $vmsg . '</div>' : null;

        $html .= !$mobile ? VGenerate::advHTML(array(17, 51, $vid)) : null;

        $html .= $pv_html;
        $html .= (self::$type == 'live' and self::$chat_key != '' and $mobile) ? '<style>iframe{width: 1px;min-width: 100%;}</style><div id="vs-chat-wrap" class="border-wrapper ' . (substr(self::$cfg['theme_name'], 0, 4) !== 'dark' ? 'light' : 'dark') . '"><i class="spinner icon-spinner"></i></div>' : null;

        $html .= $mobile ? VGenerate::advHTML(array(17, 51, $vid)) : null;

        $html .= '
                <div id="title-wrapper" class="border-wrapper">
                    <h1 class="title-text">' . $vtitle . '</h1>
                </div>
        ';
        $html .= '
            <div class="d-flex view-details-wrap">
                <div class="d-flex view-details-channel' . (self::$is_shorts ? ' no-display' : null) . '">
                <div class="channel_image">
                                        <a href="' . VHref::channelURL(["username" => $_suser]) . '"><img src="' . $_uimg . '"></a>
                                </div>
                                <div class="p-info p-less vdc-1">
                                    <div><a class="u-info-icon" href="' . VHref::channelURL(["username" => $_suser]) . '">' . VAffiliate::affiliateBadge(((self::$userinfo['usr_affiliate'] == 1 or self::$userinfo['usr_partner'] == 1) ? 1 : 0), self::$userinfo['affiliate_badge']) . (self::$userinfo['user_dname'] != '' ? self::$userinfo['user_dname'] : (self::$userinfo['ch_title'] != '' ? self::$userinfo['ch_title'] : self::$userinfo['user_name'])) . '</a></div>
                                    ' . (self::$cfg['user_follows'] == 1 ? '<div><span class="p-sub">' . VGenerate::nrf((int) $follow_nr) . ' ' . self::$language['frontend.global.followers'] . '</span></div>' : null) . '
                                    ' . (self::$cfg['user_subscriptions'] == 1 ? '<div><span class="p-sub">' . VGenerate::nrf((int) $sub_nr) . ' ' . self::$language['frontend.global.subscribers'] . '</span></div>' : null) . '
                </div>
                <div class="d-flex">
                    <div class="channel-owner-wrap d-flex">
                        ' . ($_SESSION['USER_ID'] != $vuid ? '
                    ' . ((self::$cfg['user_subscriptions'] == 1 or self::$cfg['user_follows'] == 1) ? '
                    <div class="subscribers profile_count no-display1 no-edit">
                            ' . (self::$cfg['user_follows'] == 1 ? '
                            <a href="javascript:;" class="count_link ' . $follow_cls . '" rel="nofollow"><div class="follow-txt"><span>' . $follow_txt . ' </span></div></a>
                            ' : null) . '
                        ' . (self::$cfg['user_subscriptions'] == 1 ? '
                            ' . ($is_sub ? '<a href="javascript:;" onclick="$(\'#uu-' . $susr_key . '\').stop().slideToggle(\'fast\')" class="sub-opt"><div class="sub-txt"><span class="sub-span">' . self::$language['frontend.global.subscription'] . '</span></div></a>' : '<a href="javascript:;" class="count_link ' . $sub_cls . '" rel-usr="' . $susr_key . '" rel="nofollow"><div class="sub-txt sub-txt-' . $susr_key . '"' . ((int) $_SESSION['USER_ID'] == 0 ? ' rel="tooltip" title="' . self::$language['main.text.subscribe'] . '"' : null) . '><span>' . ((int) $_SESSION['USER_ID'] != $vuid ? $sub_txt : self::$language['frontend.global.subscribers.cap']) . ' </span></div></a>') . '
                            ' : null) . '
                    </div>

                    <div id="div-info" class="targetDiv border-wrapper">
                            <div class="uu-wrap">
                            <ul class="uu arrow_box" id="uu-' . $susr_key . '" style="display: none;">
                                <li class="uu1"><i class="icon-star"></i> ' . self::$language['frontend.global.sub.your'] . '</li>
                                <li class="uu2 d-flex">
                                    <div class="d-flex">
                                        <img src="' . $_uimg . '" height="64">
                                    </div>
                                    <div class="sub-item-info">
                                        <a href="' . VHref::channelURL(["username" => self::$userinfo['user_name']]) . '">' . (self::$userinfo['user_dname'] != '' ? self::$userinfo['user_dname'] : (self::$userinfo['ch_title'] != '' ? self::$userinfo['ch_title'] : self::$userinfo['user_name'])) . '</a>
                                        <span>' . $sn . '</span>
                                    </div>
                                </li>
                                <li>
                                    <center>
                                        <button type="button" class="subscribe-button save-entry-button button-grey search-button form-button sub-uu" rel-usr="' . $susr_key . '" value="1" name="upgrade_subscription"><span>' . self::$language['frontend.global.sub.upgrade'] . '</span></button>
                                        ' . ($ub ? '<a class="unsubscribe-button cancel-trigger" rel-usr="' . $susr_key . '" href="javascript:;"><span>' . self::$language['frontend.global.unsubscribe'] . '</span></a>' : null) . '
                                    </center>
                                </li>
                            </ul>


                                            </div>
                    </div>
                    ' : null) . '
                    ' : '
                    <div class="subscribers profile_count no-display1">
                        <a href="' . self::$cfg['main_url'] . '/' . VHref::getKey("files_edit") . '?fe=1&' . self::$type[0] . '=' . self::$file_key . '" class="" rel="nofollow"><div class="follow-txt"><span>' . self::$language["frontend.global." . self::$type[0] . ".edit"] . '</span></div></a>
                    </div>
                    ') . '
                    </div>
                </div>
                </div>
                <div class="d-flex view-actions-wrapper' . (self::$is_shorts ? ' no-display' : null) . '">
                <div>
                        <div class="act-btn d-flex">
                        <div class="d-flex pe-7">
                    ' . ($vrate == 1 ? '
                        <div class="like-wrap">
                        <div class="likey d-flex' . (!$session_id ? ' showSingle-lb-login" target="rate-up' : null) . '" rel="tooltip" title="' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0]], self::$language['view.files.like.alt']) . '">
                            <div class="sh_button file-like-action file-like-thumb d-flex' . $like_active . '">
                                <div class="spinner spinner--steps icon-spinner no-display" aria-hidden="true"></div>
                                ' . ($session_id > 0 ? '<i class="icon-thumbs-up"></i>' : '<i class="icon-thumbs-up" target="rate-up"></i>') . '
                            </div>
                            <div class="sh_button likes_count">' . VFiles::numFormat($vlike) . '</div>
                        </div>
                        <div class="v-border"></div>
                        <div class="dislikey d-flex' . (!$session_id ? ' showSingle-lb-login" target="rate-down' : null) . '" rel="tooltip" title="' . self::$language['view.files.dislike.alt'] . '">
                            <div class="sh_button file-dislike-action file-dislike-thumb d-flex' . $dislike_active . '">
                                <div class="spinner spinner--steps icon-spinner no-display" aria-hidden="true"></div>
                                ' . ($session_id > 0 ? '<i class="icon-thumbs-up2"></i>' : '<i class="icon-thumbs-up2" target="rate-down"></i>') . '
                            </div>
                            <div class="sh_button dislikes_count">' . VFiles::numFormat($vdislike) . '</div>
                        </div>
                        ' . ($vrate == 1 ? VGenerate::simpleDivWrap('', 'like_holder', VGenerate::simpleDivWrap('', 'file-like-stats', $vlike_txt)) : null) . '
                        </div>
                    ' : '<span class="disabled-rating">' . self::$language['view.files.no.rating'] . '</span>') . '
                        ' . ((self::$cfg['file_email_sharing'] == 1 or self::$cfg['file_permalink_sharing'] == 1 or $vembed == 1 or $vsocial == 1) ? '<div class="showSingle-lb sh_button btn_auto" target="share" rel="tooltip" title="' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0] . ".c"], self::$language['view.files.file.share']) . '"><i class="icon-redo2"></i> ' . self::$language['frontend.global.share'] . '</div>' : null) . '
                    </div>
                    <div class="d-flex">
                        ' . (((self::$cfg['file_favorites'] == 1 or self::$cfg['file_playlists'] == 1 or self::$cfg['file_watchlist'] == 1)) ? '<div class="showSingle-lb sh_button btn_auto" target="favorite" rel="tooltip" title="' . self::$language['view.files.add.to'] . '"><i class="icon-plus"></i>' . self::$language['frontend.global.save'] . '</div>' : null) . '
                        ' . ((!self::$is_shorts and self::$cfg['file_downloads'] == 1 and $vsrc != 'embed' and self::$type != 'blog' and self::$type != 'live') ? '<div class="showSingle-lb sh_button btn_auto" target="download" rel="tooltip" title="' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0] . ".c"], self::$language['view.files.download.alt']) . '"><i class="icon-download"></i>' . self::$language['frontend.global.download'] . '</div>' : null) . '
                        ' . (self::$cfg['file_flagging'] == 1 ? '<div class="showSingle-lb sh_button btn_auto no-display" target="report" rel="tooltip" title="' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0] . ".c"], self::$language['view.files.report.txt']) . '"><i class="icon-flag"></i>' . self::$language['view.files.report'] . '</div>' : null) . '
                        ' . (self::$is_shorts == 1 ? '<div class="showSingle-lb sh_button btn_auto no-display" target="description" rel="tooltip" title="' . self::$language['frontend.global.description'] . '"><i class="icon-newspaper"></i>' . self::$language['frontend.global.description'] . '</div>' : null) . '
                        <div class="showSingle-lb sh_button btn_auto more" target="more" rel="tooltip" title="">
                            <i class="icon-menu2"></i>
                            <div class="showSingle-more-menu">
                                <ul>
                                    ' . (self::$is_shorts ? '<li rel="description"><i class="icon-newspaper"></i>' . self::$language['frontend.global.description'] . '</li>' : null) . '
                                    ' . ((self::$cfg['file_favorites'] == 1 or self::$cfg['file_playlists'] == 1 or self::$cfg['file_watchlist'] == 1) ? '<li rel="favorite"><i class="icon-plus"></i>' . self::$language['frontend.global.save'] . '</li>' : null) . '
                                    ' . ((self::$cfg['file_downloads'] == 1 and $vsrc != 'embed' and self::$type != 'blog') ? '<li rel="download"><i class="icon-download"></i>' . self::$language['frontend.global.download'] . '</li>' : null) . '
                                    ' . (self::$cfg['file_flagging'] == 1 ? '<li rel="report"><i class="icon-flag"></i>' . self::$language['view.files.report'] . '</li>' : null) . '
                                </ul>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
                </div>
            </div>
    ';

        $description_html = self::$is_shorts ? '
                <div id="title-wrapper" class="border-wrapper">
                    <h1 class="title-text">' . $vtitle . '</h1>
                </div>
        ' : null;

        $description_html .= self::$is_shorts ? '
                <div class="d-flex view-details-channel">
                <div class="channel_image">
                                        <a href="' . VHref::channelURL(["username" => $_suser]) . '"><img src="' . $_uimg . '"></a>
                                </div>
                                <div class="p-info p-less vdc-1">
                                    <div><a class="" href="' . VHref::channelURL(["username" => $_suser]) . '">' . VAffiliate::affiliateBadge(((self::$userinfo['usr_affiliate'] == 1 or self::$userinfo['usr_partner'] == 1) ? 1 : 0), self::$userinfo['affiliate_badge']) . (self::$userinfo['user_dname'] != '' ? self::$userinfo['user_dname'] : (self::$userinfo['ch_title'] != '' ? self::$userinfo['ch_title'] : self::$userinfo['user_name'])) . '</a></div>
                                    ' . (self::$cfg['user_follows'] == 1 ? '<div><span class="p-sub">' . VGenerate::nrf((int) $follow_nr) . ' ' . self::$language['frontend.global.followers'] . '</span></div>' : null) . '
                                    ' . (self::$cfg['user_subscriptions'] == 1 ? '<div><span class="p-sub">' . VGenerate::nrf((int) $sub_nr) . ' ' . self::$language['frontend.global.subscribers'] . '</span></div>' : null) . '
                </div>
                </div>
                <div class="d-flex description-data">
                    <div class="d-flex d-column"><span class="d-nr">' . VFiles::numFormat($vlike) . '</span><span class="d-i">' . self::$language['frontend.global.likes.stat'] . '</span></div>
                    <div class="d-flex d-column"><span class="d-nr">' . VFiles::numFormat($viewnr) . '</span><span class="d-i">' . self::$language['frontend.global.views.stat'] . '</span></div>
                    <div class="d-flex d-column"><span class="d-nr">' . date('M j', strtotime($vdate)) . '</span><span class="d-i">' . date('Y', strtotime($vdate)) . '</span></div>
                </div>
        ' : null;

        $description_html .= self::$file_key ? '
            <div class="d-flex view-details-description pointer">
            <div class="d-flex">
                <div>
                    <div class="channel_views">
                    <div class="file-views-nr">
                        ' . (self::$cfg['file_views'] == 1 ? ((self::$type != 'live' or (self::$type == 'live' and $stream_live == 0 and $stream_ended == 1)) ? VFiles::numFormat2($viewnr) . ' <span>' . ($viewnr == 1 ? self::$language['frontend.global.view'] : self::$language['frontend.global.views']) . '&nbsp;&nbsp;</span>' : null) : null)
        . ((self::$type == 'live' and $stream_live == 1 and $stream_ended == 0) ? '<span class="viewers-red"><span id="live-viewers">0</span> ' . self::$language['frontend.global.viewers'] . '&nbsp;&nbsp;</span>' : null) . '
                    </div>
                    </div>
                </div>
                <div>
                    <span class="p-date">' . VUserinfo::timeRange($vdate) . '</span>
                </div>
            </div>
            <div class="d-flex vdd-text">
                <div class="p-d-txt more">' . $vdescr . '</div>
                <div>
                    <a href="javascript:;" rel="nofollow">
                    <span class="info-toggle info-more">' . self::$language['view.files.more'] . '</span>
                    <span class="info-toggle info-less no-display">' . self::$language['view.files.less'] . '</span>
                    </a>
                </div>
            </div>
            </div>
    ' : null;

        $html .= !self::$is_shorts ? $description_html : null;
        $html .= '
                ' . ((self::$cfg['file_email_sharing'] == 1 or self::$cfg['file_permalink_sharing'] == 1 or $vembed == 1 or $vsocial == 1) ? '
                <div id="div-share" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-redo2"></i>' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0] . ".c"], self::$language['view.files.file.share']) . '</h3>
                        <div class="line mb-0"></div>
                        <div class="tabs tabs-style-line">
                            <nav>
                                <ul class="px-20">
                                    ' . ($vsocial == 1 ? '<li><a href="#section-social" class="icon icon-facebook" rel="nofollow"><span>' . self::$language['view.files.share.social'] . '</span></a></li>' : null) . '
                                    ' . ($vembed == 1 ? '<li><a href="#section-embed" class="icon icon-embed" rel="nofollow"><span>' . self::$language['view.files.share.embed'] . '</span></a></li>' : null) . '
                                    ' . (self::$cfg['file_permalink_sharing'] == 1 ? '<li><a href="#section-perma" class="icon icon-link" rel="nofollow"><span>' . self::$language['view.files.permalink'] . '</span></a></li>' : null) . '
                                    ' . (self::$cfg['file_email_sharing'] == 1 ? '<li><a href="#section-embed" class="icon icon-envelope" rel="nofollow"><span>' . self::$language['view.files.share.email'] . '</span></a></li>' : null) . '
                                </ul>
                            </nav>
                            <div class="content-wrap ft-' . self::$type . '">
                                <section id="section-social">
                                    <article>
                                        <div>
                                            ' . VGenerate::socialBookmarks() . '
                                        </div>
                                    </article>
                                </section>
                                <section id="section-embed">
                                    <article>
                                        <div>
                                            ' . $embed_html . '
                                        </div>
                                    </article>
                                </section>
                                <section id="section-perma">
                                    <article>
                                        <div>
                                            ' . $perma_html . '
                                        </div>
                                    </article>
                                </section>
                                <section id="section-email">
                                    <article>
                                        <div>
                                            ' . $email_html . '
                                        </div>
                                    </article>
                                </section>
                            </div>
                        </div>
                    </div>
                </div>
                ' : null) . '

                ' . ((self::$cfg['file_favorites'] == 1 or self::$cfg['file_playlists'] == 1 or self::$cfg['file_watchlist'] == 1) ? '
                <div id="div-favorite" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-plus"></i>' . self::$language['view.files.add.to'] . '</h3>
                        <div class="line"></div>
                        ' . ($session_id > 0 ? '
                        <div id="addto-types" class="download-buttons-off">
                            <ul class="ul-att">
                                ' . (self::$cfg['file_favorites'] == 1 ? '
                                <li class="att att-1">
                                    ' . ($session_id > 0 ? '
                                        <button class="symbol-button addto-action" id="' . ($isfavorite ? 'cb-favclear' : 'cb-favadd') . '" name="cb_favadd">
                                            <i class="' . ($isfavorite ? 'icon-checkbox-checked' : 'icon-checkbox-unchecked') . '"></i>
                                            <span>' . self::$language['view.files.add.to.fav'] . '</span>
                                        </button>
                                    ' : $visitor_html) . '
                                </li>
                                ' : null) . '
                                ' . (self::$cfg['file_playlists'] == 1 ? '
                                <li class="att att-2">
                                    ' . ($session_id > 0 ? '
                                        <div class="menu-drop addto-action-menu">
                                            <div id="entry-action-pl" class="dl-menuwrapper-off">
                                                <button class="dl-trigger-off actions-trigger-off symbol-button addto-action" onclick="$(this).next().stop().toggle(0, function(){$.fancybox.update()});"><i class="iconBe-chevron-down"></i>' . self::$language['view.files.add.to.pl'] . '</button>
                                                ' . VFiles::addToPl(self::$file_key) . '
                                            </div>
                                        </div>
                                    ' : $visitor_html) . '
                                </li>
                                ' : null) . '

                                ' . (self::$cfg['file_watchlist'] == 1 ? '
                                <li class="att att-3">
                                    ' . ($session_id > 0 ? '
                                        <button class="symbol-button addto-action" id="' . ($iswatchlist ? 'cb-watchclear' : 'cb-watchadd') . '" name="cb_watchadd">
                                            <i class="' . ($iswatchlist ? 'icon-checkbox-checked' : 'icon-checkbox-unchecked') . '"></i>
                                            <span>' . self::$language['view.files.add.to.watch'] . '</span>
                                        </button>
                                    ' : $visitor_html) . '
                                </li>
                                ' : null) . '
                            </ul>
                        </div>
                        ' : '<div id="div-no-favorite">' . $visitor_html . '</div>') . '

                        <div class="clearfix"></div>
                    </div>
                </div>
                ' : null) . '

                ' . ((self::$cfg['file_downloads'] == 1 and $vsrc != 'embed' and self::$type != 'blog') ? '
                <div id="div-download" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-download"></i>' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0] . ".c"], self::$language['view.files.download.alt']) . '</h3>
                        <div class="line"></div>
                        <div>
                            ' . $down_html . '
                        </div>
                    </div>
                </div>
                ' : null) . '

                ' . (self::$cfg['file_flagging'] == 1 ? '
                <div id="div-report" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-flag"></i>' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0] . ".c"], self::$language['view.files.report.txt']) . '</h3>
                        <div class="line"></div>
                        <div>
                            ' . $flag_html . '
                        </div>
                    </div>
                </div>
                ' : null) . '

                ' . (self::$is_shorts == 1 ? '
                <div id="div-description" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-newspaper"></i>' . self::$language['frontend.global.description'] . '</h3>
                        <div class="line"></div>
                        <div>
                            ' . $description_html . '
                        </div>
                    </div>
                </div>
                ' : null) . '

                ' . ($session_id == 0 ? '
                <div id="div-rate-up" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-thumbs-up"></i>' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0]], self::$language['view.files.rate.txt']) . '</h3>
                        <div class="line"></div>
                        <div>
                            ' . $visitor_html . '
                        </div>
                    </div>
                </div>
                ' : null) . '

                ' . ($session_id == 0 ? '
                <div id="div-rate-down" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-thumbs-up2"></i>' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0]], self::$language['view.files.rate.txt']) . '</h3>
                        <div class="line"></div>
                        <div>
                            ' . $visitor_html . '
                        </div>
                    </div>
                </div>
                ' : null) . '

                ' . ($session_id == 0 ? '
                <div id="div-follow" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-user"></i>' . self::$language['frontend.global.follow'] . '</h3>
                        <div class="line"></div>
                        <div>
                            ' . $visitor_html . '
                        </div>
                    </div>
                </div>
                ' : null) . '

                ' . ($session_id == 0 ? '
                <div id="div-subscribe" class="targetDiv inactive border-wrapper-off">
                    <div class="more">
                        <h3 class="content-title"><i class="icon-user"></i>' . self::$language['frontend.global.subscribe'] . '</h3>
                        <div class="line"></div>
                        <div>
                            ' . $visitor_html . '
                        </div>
                    </div>
                </div>
                ' : null);

        $html .= VGenerate::advHTML(array(18, 52, $vid));

        /* COMMENTS */
        if (!self::$is_shorts) {
            $html .= '<div id="div-comments" class="targetDiv border-wrappers">';
            $html .= VGenerate::simpleDivWrap('', 'comment-loader-before', '');
            $html .= (self::$cfg['file_comments'] == 1 and $vcomm != 'none') ? VGenerate::simpleDivWrap('', 'comment-loader', '') : VGenerate::simpleDivWrap('is-disabled', '', '<span>' . self::$language['view.files.comm.disabled'] . '</span>');
            $html .= '</div>';
        }
        $html .= VGenerate::advHTML(array(19, 53, $vid));
        $html .= '<form id="file-rating-form" method="post" action=""><input type="hidden" name="f_vrate" value="' . $vrate . '" /><input type="hidden" name="f_like" class="f_like" value="' . $vlike . '" /><input type="hidden" name="f_dislike" class="f_dislike" value="' . $vdislike . '" /></form>';
        $html .= '<form id="user-files-form" method="post" action=""><input type="hidden" name="uf_type" class="uf-type" value="' . self::$type . '" /><input type="hidden" name="uf_vid" class="uf-vid" value="' . self::$file_key . '" /><input type="hidden" name="uf_vuid" value="' . $vuid . '" /><input type="hidden" name="uf_ct" value="' . self::$userinfo['upload_category_id'] . '" /></form>';
        $html .= VGenerate::simpleDivWrap('no-display', '', '<form class="entry-form-class"><input type="checkbox" name="fileid[]" value="' . self::$file_key . '" id="file-check' . self::$file_key . '" class="list-check"></form>');
        $html .= '</div>'; //end page_holder_left

        if (!self::$is_shorts) {
            $html .= '
            ' . ((self::$type == 'live' and self::$chat_key != '' and !$mobile) ? '<style>iframe{width: 1px;min-width: 100%;}</style><div id="vs-chat-wrap" class="border-wrapper ' . (substr(self::$cfg['theme_name'], 0, 4) !== 'dark' ? 'light' : 'dark') . '"><i class="spinner icon-spinner"></i></div>' : null) . '
                <div class="page_holder_right border-wrapper">
                    ' . ((self::$cfg['file_responses'] == 1 and $vrespond != 'none' and !isset($_GET['p'])) ? '<div id="div-responses" class="targetDiv inactive-off border-wrapper-off">' . VResponses::viewFileResponses(self::$type, $vresponses, $vuid, array($vlike, $vdislike)) . '</div>' : null) . '
                    ' . (isset($_GET['p']) ? self::runningPlaylist(array(self::$type, self::$file_key)) : null) . '
                    ' . VView::sideColumn() . '
                    <div class="line-off toggle-off"></div>

                    <div id="more-results">
                        <center>
                            <a href="javascript:;" rel="nofollow">
                                <span class="info-toggle related-more" rel-key="' . self::$file_key . '" rel-type="' . self::$type[0] . '">&nbsp;</span>
                            </a>
                        </center>
                    </div>
                </div>
            </div>
        ';
        }

        /* MORE JAVASCRIPT */
        if (self::$type == 'short') {
            $ht_js .= '!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof exports?module.exports=e:e(jQuery)}(function(a){var u,r,e=["wheel","mousewheel","DOMMouseScroll","MozMousePixelScroll"],t="onwheel"in window.document||9<=window.document.documentMode?["wheel"]:["mousewheel","DomMouseScroll","MozMousePixelScroll"],f=Array.prototype.slice;if(a.event.fixHooks)for(var n=e.length;n;)a.event.fixHooks[e[--n]]=a.event.mouseHooks;var d=a.event.special.mousewheel={version:"3.1.12",setup:function(){if(this.addEventListener)for(var e=t.length;e;)this.addEventListener(t[--e],i,!1);else this.onmousewheel=i;a.data(this,"mousewheel-line-height",d.getLineHeight(this)),a.data(this,"mousewheel-page-height",d.getPageHeight(this))},teardown:function(){if(this.removeEventListener)for(var e=t.length;e;)this.removeEventListener(t[--e],i,!1);else this.onmousewheel=null;a.removeData(this,"mousewheel-line-height"),a.removeData(this,"mousewheel-page-height")},getLineHeight:function(e){var t=a(e),e=t["offsetParent"in a.fn?"offsetParent":"parent"]();return e.length||(e=a("body")),parseInt(e.css("fontSize"),10)||parseInt(t.css("fontSize"),10)||16},getPageHeight:function(e){return a(e).height()},settings:{adjustOldDeltas:!0,normalizeOffset:!0}};function i(e){var t,n=e||window.event,i=f.call(arguments,1),o=0,l=0,s=0,h=0;if((e=a.event.fix(n)).type="mousewheel","detail"in n&&(s=-1*n.detail),"wheelDelta"in n&&(s=n.wheelDelta),"wheelDeltaY"in n&&(s=n.wheelDeltaY),"wheelDeltaX"in n&&(l=-1*n.wheelDeltaX),"axis"in n&&n.axis===n.HORIZONTAL_AXIS&&(l=-1*s,s=0),o=0===s?l:s,"deltaY"in n&&(o=s=-1*n.deltaY),"deltaX"in n&&(l=n.deltaX,0===s&&(o=-1*l)),0!==s||0!==l)return 1===n.deltaMode?(o*=t=a.data(this,"mousewheel-line-height"),s*=t,l*=t):2===n.deltaMode&&(o*=t=a.data(this,"mousewheel-page-height"),s*=t,l*=t),h=Math.max(Math.abs(s),Math.abs(l)),(!r||h<r)&&c(n,r=h)&&(r/=40),c(n,h)&&(o/=40,l/=40,s/=40),o=Math[1<=o?"floor":"ceil"](o/r),l=Math[1<=l?"floor":"ceil"](l/r),s=Math[1<=s?"floor":"ceil"](s/r),d.settings.normalizeOffset&&this.getBoundingClientRect&&(h=this.getBoundingClientRect(),e.offsetX=e.clientX-h.left,e.offsetY=e.clientY-h.top),e.deltaX=l,e.deltaY=s,e.deltaFactor=r,e.deltaMode=0,i.unshift(e,o,l,s),u&&window.clearTimeout(u),u=window.setTimeout(w,200),(a.event.dispatch||a.event.handle).apply(this,i)}function w(){r=null}function c(e,t){return d.settings.adjustOldDeltas&&"mousewheel"===e.type&&t%120==0}a.fn.extend({mousewheel:function(e){return e?this.on("mousewheel",e):this.trigger("mousewheel")},unmousewheel:function(e){return this.off("mousewheel",e)}})});
            ';
            $ht_js .= '!function(t,e){"use strict";"function"!=typeof t.CustomEvent&&(t.CustomEvent=function(t,n){n=n||{bubbles:!1,cancelable:!1,detail:void 0};var a=e.createEvent("CustomEvent");return a.CustomEvent(t,n.bubbles,n.cancelable,n.detail),a},t.CustomEvent.prototype=t.Event.prototype),e.addEventListener("touchstart",function(t){if("true"===t.target.getAttribute("data-swipe-ignore"))return;l=t.target,r=Date.now(),n=t.touches[0].clientX,a=t.touches[0].clientY,u=0,i=0,o=t.touches.length},!1),e.addEventListener("touchmove",function(t){if(!n||!a)return;var e=t.touches[0].clientX,r=t.touches[0].clientY;u=n-e,i=a-r},!1),e.addEventListener("touchend",function(t){if(l!==t.target)return;var c=parseInt(s(l,"data-swipe-threshold","20"),10),d=s(l,"data-swipe-unit","px"),p=parseInt(s(l,"data-swipe-timeout","500"),10),h=Date.now()-r,v="",b=t.changedTouches||t.touches||[];"vh"===d&&(c=Math.round(c/100*e.documentElement.clientHeight));"vw"===d&&(c=Math.round(c/100*e.documentElement.clientWidth));Math.abs(u)>Math.abs(i)?Math.abs(u)>c&&h<p&&(v=u>0?"swiped-left":"swiped-right"):Math.abs(i)>c&&h<p&&(v=i>0?"swiped-up":"swiped-down");if(""!==v){var E={dir:v.replace(/swiped-/,""),touchType:(b[0]||{}).touchType||"direct",fingers:o,xStart:parseInt(n,10),xEnd:parseInt((b[0]||{}).clientX||-1,10),yStart:parseInt(a,10),yEnd:parseInt((b[0]||{}).clientY||-1,10)};l.dispatchEvent(new CustomEvent("swiped",{bubbles:!0,cancelable:!0,detail:E})),l.dispatchEvent(new CustomEvent(v,{bubbles:!0,cancelable:!0,detail:E}))}n=null,a=null,r=null},!1);var n=null,a=null,u=null,i=null,r=null,l=null,o=0;function s(t,n,a){for(;t&&t!==e.documentElement;){var u=t.getAttribute(n);if(u)return u;t=t.parentNode}return a}}(window,document);';
            $ht_js .= '$("body").on("mousewheel",function(e){if(e.deltaY==1){if(e.target.id==="view-player"){$("#shorts-arrow-up").click()}}else if(e.deltaY==-1){if(e.target.id==="view-player"){$("#shorts-arrow-down").click()}}});';
            $ht_js .= 'document.addEventListener("swiped-up",function(e){if($(e.target).hasClass("v-mask")){$("#shorts-arrow-down").click()}});';
            $ht_js .= 'document.addEventListener("swiped-down",function(e){if($(e.target).hasClass("v-mask")){$("#shorts-arrow-up").click()}});';
            $ht_js .= '$(document).on("keydown",function(e){var code=(e.keyCode?e.keyCode:e.which);switch(code){case 34:case 40:if($(e.target).hasClass("fe")||$(e.target).hasClass("video-js"))$("#shorts-arrow-down").click();break;case 33:case 38:if($(e.target).hasClass("fe")||$(e.target).hasClass("video-js"))$("#shorts-arrow-up").click();break;}});';
        }
        $ht_js .= self::$type == 'live' ? 'var ifrm = document.createElement("iframe");ifrm.setAttribute("id", "vs-chat");document.getElementById("vs-chat-wrap").appendChild(ifrm);ifrm.setAttribute("src", "' . $_SESSION['live_chat_server'] . '/' . VHref::getKey("chat") . '/' . self::$chat_key . '/' . self::$file_key . '");/*ifrm.setAttribute("sandbox","allow-same-origin allow-scripts");*/ifrm.setAttribute("referrerpolicy","same-origin");ifrm.setAttribute("width","100%");ifrm.setAttribute("height","100%");' : null;
        /* comment loading, submit replies */
        $ht_js .= (self::$cfg['file_comments'] == 1 and $vcomm != 'none') ? 'function commentLazyLoad(){if(!$("#comment-load").hasClass("loaded")){$(document).on("inview","#wrapper #div-comments",function(event,isInView,visiblePartX,visiblePartY){if(isInView && !$("#comment-load").hasClass("loaded")){var t = $("#comment-loader");t.html(\'<center><span style="padding:20px 0 80px 0px;display:block;font-size:14px;"><i class="spinner icon-spinner"></i> ' . self::$language['view.files.comm.loading'] . '</span></center>\');setTimeout(function(){$.post(c_url+"&do=comm-load",{comm_type:"' . self::$type . '",comm_uid:"' . $vuid . '"},function(data){t.replaceWith(\'<div id="comment-load" class="border-wrapper loaded">\'+data+\'</div>\');$("#comment-loader-before").detach();$(".comm-body").each(function(){h=$(this).height();$(this).parent().find("[id^=comm-actions2-over] span").css("top",-h);$(this).parent().find("[id^=comment-actions-dd] .accordion.cacc").css("top",-h);});$(".c-pinned").each(function(){t=$(this);if(t.parent().parent().find(".comm-replies-show").length==0){t.parent().parent().parent().find(".response_holder").detach();}});});},5);}});}}if(!$("#comment-load").hasClass("loaded")){commentLazyLoad();}' : null;

        $ht_js .= 'var comm_sec = "' . VHref::getKey("watch") . '";';
        $ht_js .= 'var comm_url = "' . $cfg['main_url'] . '/"+comm_sec+"?' . self::$type[0] . '=' . self::$file_key . '"; var m_loading = "";';
        $ht_js .= 'var p=(parseInt($("#cnr").text())||1);var pag="&page=" + p;';
        /* pagination */
        $ht_js .= '$(document).on({click:function(e){';
        $ht_js .= 'var p=parseInt($("#cnr").text());';
        $ht_js .= 'var comm_link = comm_url + "&do=comm-load&page=" + parseInt(p+1);';
        $ht_js .= '$("#comm-spinner").show().mask("");';
        $ht_js .= '$.post(comm_link, $("#comm-post-form").serialize(), function(data){';
        $ht_js .= '$(data).insertBefore(".comm-pag"); $("#cnr").text( parseInt(p+1) );';
        $ht_js .= '$(".comm-toggle-replies").each(function(){var t = $(this); var _id = t.attr("id").substr(3); if (typeof($("#"+_id+" .response_holder").html()) == "undefined"){t.detach();}});';
        $ht_js .= '$(".comments_activity").each(function(){var t = $(this); var _id = t.attr("id"); if (!t.hasClass("response") && typeof($("#"+_id+" > .response_holder").html()) !== "undefined") {$("#"+_id+" > .response_holder").hide();} });';
        $ht_js .= '$(".comm-body").each(function(){h=$(this).height();$(this).parent().find("[id^=comm-actions2-over] span").css("top",-h);$(this).parent().find("[id^=comment-actions-dd] .accordion.cacc").css("top",-h);});';
        $ht_js .= '$("#comm-spinner").unmask();';
        $ht_js .= '});}}, ".comm-page-next");';

        /* page in view */
        $ht_js .= '$(document).on("inview",".comm-pag",function(event,isInView,visiblePartX,visiblePartY){';
        $ht_js .= 'if(isInView){$(".comm-page-next").click();}';
        $ht_js .= '});';
        if (self::$cfg['file_comments'] == 1 and $vcomm != 'none' and $session_id > 0) {
            /* comment like */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var f1 = $(this).parent().next().html(); var f2 = $("#comm-post-form").serialize(); var frm = "c_key="+f1+"&"+f2;';
            $ht_js .= 'var t = $(this); t.find("i").removeClass("icon-thumbs-up").addClass("spinner icon-spinner");';
            $ht_js .= '$.post(comm_url+"&do=comm-like", frm, function(data){';
            $ht_js .= 't.closest(".ucls-links").append(data);'; // $(".comm-input-action").val("");';
            $ht_js .= 't.find("i").addClass("icon-thumbs-up").removeClass("spinner icon-spinner");';
            $ht_js .= '});';
            $ht_js .= '}}, ".comm-like-action");';
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var f1 = $(this).parent().next().html(); var f2 = $("#comm-post-form").serialize(); var frm = "c_key="+f1+"&"+f2;';
            $ht_js .= 'var t = $(this); t.find("i").removeClass("icon-thumbs-up2").addClass("spinner icon-spinner");';
            $ht_js .= '$.post(comm_url+"&do=comm-dislike", frm, function(data){';
            $ht_js .= 't.closest(".ucls-links").append(data);'; // $(".comm-input-action").val("");';
            $ht_js .= 't.find("i").addClass("icon-thumbs-up2").removeClass("spinner icon-spinner");';
            $ht_js .= '});';
            $ht_js .= '}}, ".comm-dislike-action");';
            /* approve */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var f1 = $(this).parent().parent().next().text(); var f2 = $("#comm-post-form").serialize(); var frm = "c_key="+f1+"&"+f2;';
            $ht_js .= 'var t = $(this); t.find("i").addClass("spinner icon-spinner");';
            $ht_js .= '$.post(comm_url+"&do=comm-approve", frm, function(data){';
            $ht_js .= 't.closest(".ucls-links").append(data);'; // $(".comm-input-action").val("");';
            $ht_js .= 't.parent().addClass("no-display"); t.parent().next().removeClass("no-display");';
            $ht_js .= 't.find("i").removeClass("spinner icon-spinner");';
            $ht_js .= '});';
            $ht_js .= '}},".comm-approve");';
            /* suspend */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var f1 = $(this).parent().parent().next().text(); var f2 = $("#comm-post-form").serialize(); var frm = "c_key="+f1+"&"+f2;';
            $ht_js .= 'var t = $(this); t.find("i").removeClass("icon-lock").addClass("spinner icon-spinner");';
            $ht_js .= '$.post(comm_url+"&do=comm-suspend", frm, function(data){';
            $ht_js .= 't.closest(".ucls-links").append(data);'; // $(".comm-input-action").val("");';
            $ht_js .= 't.find("i").removeClass("spinner icon-spinner");';
            $ht_js .= 't.parent().addClass("no-display"); t.parent().prev().removeClass("no-display");';
            $ht_js .= '});';
            $ht_js .= '}},".comm-suspend");';
            /* block user */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var f1 = $(this).parent().parent().next().text(); var f2 = $("#comm-post-form").serialize(); var frm = "c_key="+f1+"&"+f2;';
            $ht_js .= 'var t = $(this); t.find("i").removeClass("icon-blocked").addClass("spinner icon-spinner");$("#"+f1).mask("");';
            $ht_js .= '$.post(comm_url+"&do=comm-block", frm, function(data){';
            $ht_js .= '$("#comment-load").html(data);'; // $(".comm-input-action").val("");';
            $ht_js .= 't.find("i").removeClass("spinner icon-spinner").addClass("icon-blocked");$("#"+f1).unmask();';
            $ht_js .= 't.parent().addClass("no-display"); t.parent().prev().removeClass("no-display");';
            $ht_js .= '});';
            $ht_js .= '}},".comm-block");';
            /* spam */
            if ($cfg['file_comment_spam'] == 1) {
                $ht_js .= '$(document).on({click: function(e){';
                $ht_js .= 'var f1 = $(this).parent().parent().next().text(); var f2 = $("#comm-post-form").serialize(); var frm = "c_key="+f1+"&"+f2;';
                $ht_js .= 'var t = $(this); t.find("i").removeClass("icon-lock").addClass("spinner icon-spinner");';
                $ht_js .= '$.post(comm_url+"&do=comm-spam", frm, function(data){';
                $ht_js .= '$("#comment-load").html(data);'; // $(".comm-input-action").val("");';
                $ht_js .= 't.find("i").addClass("icon-lock").removeClass("spinner icon-spinner");';
                $ht_js .= '});';
                $ht_js .= '}},".comm-spam");';
            }
            /* delete */
            $ht_js .= '$(document).on({click: function(e){var message = "' . $language['view.files.comm.confirm'] . '";var answer = confirm(message);if(answer){';
            $ht_js .= 'var f1 = $(this).parent().parent().next().text(); var f2 = $("#comm-post-form").serialize(); var frm = "c_key="+f1+"&"+f2;';
            $ht_js .= 'var t = $(this); t.find("i").removeClass("icon-times").addClass("spinner icon-spinner");';
            $ht_js .= '$.post(comm_url+"&do=comm-delete", frm, function(data){';
            $ht_js .= 't.closest(".ucls-links").append(data);'; // $(".comm-input-action").val("");';
            $ht_js .= 't.find("i").addClass("icon-times").removeClass("spinner icon-spinner");';
            $ht_js .= '});}return false;';
            $ht_js .= '}},".comm-delete");';
            /* click to post comment edit */
            $ht_js .= '$(document).on({click:function(e){';
            $ht_js .= 'var f1=$(this).attr("id").substr(3);';
            $ht_js .= 'if($("#e-"+f1).val()!=""){';
            $ht_js .= 'comm_url="' . self::$cfg['main_url'] . '/' . VHref::getKey("watch") . '?' . self::$type[0] . '=' . self::$file_key . '";';
            $ht_js .= '$("#' . self::$type . '-comment"+f1).mask(" ");';
            $ht_js .= '$.post(comm_url+"&do=comm-edit",$("#comm-edit-form"+f1).serialize(),function(data){';
            $ht_js .= '$("#e-"+f1).append(`${data}`);';
            $ht_js .= '$("#' . self::$type . '-comment"+f1).unmask();';
            $ht_js .= 'return false;});';
            $ht_js .= '}}},".edit-comment-button");';
            /* click to post comment reply */
            $ht_js .= '$(document).on({click:function(e){';
            $ht_js .= 'var f1=$(this).attr("id").substr(3);';
            $ht_js .= 'if($("#r-"+f1).val()!=""){';
            $ht_js .= 'comm_url="' . self::$cfg['main_url'] . '/' . VHref::getKey("watch") . '?' . self::$type[0] . '=' . self::$file_key . '";';
            $ht_js .= '$("#' . self::$type . '-comment"+f1).mask(" ");';
            $ht_js .= '$.post(comm_url+"&do=comm-reply",$("#comm-reply-form"+f1).serialize(),function(data){';
            $ht_js .= '$("#comment-load").html(data);$("#r-"+f1).val("");';
            $ht_js .= '$("#' . self::$type . '-comment"+f1).unmask();';
            $ht_js .= '$("#comm-post-response").insertBefore("#"+f1);';
            $ht_js .= 'var sh=!$("#view-player").hasClass("bottom-comments")?$(".video-js").height():360;var ct=sh - ($("#comment-load .file-views-nr").height() + $("#comment-load #comm-post-response").height() + $("#comment-load #comm-post-form").height() + 20);setTimeout(() => {$(".posted-comments").css("height", ct);}, "100");';
            $ht_js .= '$(".response_holder .c-pinned").each(function(){c=$(this).parent().parent().parent().clone(true);c.insertAfter($(".comments_activity:first")).removeClass("response");});';
            $ht_js .= 'return false;});';
            $ht_js .= '}}},".reply-comment-button");';
            /* click to pin comment */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var f1 = $(this).parent().parent().next().text(); var f2 = $("#comm-post-form").serialize(); var frm = "c_key="+f1+"&"+f2;';
            $ht_js .= 'var t = $(this); t.find("i").removeClass("icon-pushpin").addClass("spinner icon-spinner");$("#"+f1).mask("");';
            $ht_js .= '$.post(comm_url+"&do=comm-pin", frm, function(data){';
            $ht_js .= '$("#comment-load").html(data);';
            $ht_js .= 't.find("i").removeClass("spinner icon-spinner").addClass("icon-pushpin");$("#"+f1).unmask();';
            $ht_js .= 't.parent().addClass("no-display"); t.parent().prev().removeClass("no-display");';
            $ht_js .= '});';
            $ht_js .= '}},".comm-pin");';
            /* click to unpin comment */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'var f1 = $(this).parent().parent().next().text(); var f2 = $("#comm-post-form").serialize(); var frm = "c_key="+f1+"&"+f2;';
            $ht_js .= 'var t = $(this); t.find("i").removeClass("icon-pushpin").addClass("spinner icon-spinner");$("#"+f1).mask("");';
            $ht_js .= '$.post(comm_url+"&do=comm-unpin", frm, function(data){';
            $ht_js .= '$("#comment-load").html(data);';
            $ht_js .= 't.find("i").removeClass("spinner icon-spinner").addClass("icon-pushpin");$("#"+f1).unmask();';
            $ht_js .= 't.parent().addClass("no-display"); t.parent().prev().removeClass("no-display");';
            $ht_js .= '});';
            $ht_js .= '}},".comm-unpin");';
            /* click on main comment emotes trigger */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'if(!$("#ntm em-emoji-picker").is(":visible")){';
            $ht_js .= '$(this).removeClass("icon-smiley").addClass("icon-smiley2");$("#ntm em-emoji-picker").css("display", "flex");';
            $ht_js .= '}else{';
            $ht_js .= '$(this).addClass("icon-smiley").removeClass("icon-smiley2");$("#ntm em-emoji-picker").css("display", "none");';
            $ht_js .= '}';
            $ht_js .= '}},"#comment-emotes i");';
            /* click on reply comment emotes trigger */
            $ht_js .= '$(document).on({click: function(e){';
            $ht_js .= 'if(!$(this).next().is(":visible")){';
            $ht_js .= '$(this).removeClass("icon-smiley").addClass("icon-smiley2");$(this).next().css("display", "flex");';
            $ht_js .= '}else{';
            $ht_js .= '$(this).addClass("icon-smiley").removeClass("icon-smiley2");$(this).next().css("display", "none");';
            $ht_js .= '}';
            $ht_js .= '}},".comment-emotes i");';
        }
        /* submit like, dislike */
        if ($vrate == 1 and $session_id > 0) {
            $ht_js .= '$(".file-like-action").parent().click(function(){';
            $ht_js .= 'var post_url = c_url+"&do=file-like";';
            $ht_js .= 'if(!$(".file-like-thumb").hasClass("like-thumb-on")){';
            $ht_js .= '$(".likey i").removeClass("icon-thumbs-up").addClass("spinner icon-spinner");';
            $ht_js .= '$.post(post_url, $("#file-rating-form, #user-files-form").serialize(), function(data){';
            $ht_js .= '$("#like_holder").html(data);';
            $ht_js .= '$(".likey i").addClass("icon-thumbs-up").removeClass("spinner icon-spinner");';
            $ht_js .= '});';
            $ht_js .= '}';
            $ht_js .= '});';

            $ht_js .= '$(".file-dislike-action").parent().click(function(){';
            $ht_js .= 'var post_url = c_url+"&do=file-dislike";';
            $ht_js .= 'if(!$(".file-dislike-thumb").hasClass("dislike-thumb-on")){';
            $ht_js .= '$(".dislikey i").removeClass("icon-thumbs-up2").addClass("spinner icon-spinner");';
            $ht_js .= '$.post(post_url, $("#file-rating-form, #user-files-form").serialize(), function(data){';
            $ht_js .= '$("#like_holder").html(data);';
            $ht_js .= ' $(".dislikey i").addClass("icon-thumbs-up2").removeClass("spinner icon-spinner");';
            $ht_js .= '});}';
            $ht_js .= '});';
        }
        /* add to actions */
        if ((self::$cfg['file_favorites'] == 1 or self::$cfg['file_playlists'] == 1 or self::$cfg['file_watchlist'] == 1) and $session_id > 0) {
            $ht_js .= '$(".addto-action, .addto-action-trigger").click(function(){var t=$(this);';
            $ht_js .= 'if($(this).parent().attr("id")=="entry-action-pl" || $(this).parent().attr("id")=="file-flag-reasons") return;';
            $ht_js .= 'var act_id = $(this).attr("id"); var t = $(this);';
            $ht_js .= '$("#file-check' . self::$file_key . '").prop("checked", true);';
            $ht_js .= 'var post_url = (act_id == "cb-favadd" || act_id == "cb-watchadd" || act_id == "cb-favclear" || act_id == "cb-watchclear") ? c_url+"&do="+act_id : ($(this).hasClass("cb-label-add") ? c_url+"&do=cb-labeladd&a="+act_id : ($(this).hasClass("cb-label-clear") ? c_url+"&do=cb-labelclear&a="+act_id : ""));';
            $ht_js .= 'if (act_id == "cb-favadd" || act_id == "cb-watchadd" || act_id == "cb-favclear" || act_id == "cb-watchclear") { t.find("i").removeClass().addClass("spinner icon-spinner"); } else { t.parent().prev().find("i").removeClass("iconBe-chevron-down").addClass("spinner icon-spinner");}';
            $ht_js .= '$.post(post_url, $(".entry-form-class, #user-files-form").serialize(), function(data){';
            $ht_js .= '$("#cb-response-wrap, #cb-response").detach();';
            $ht_js .= '$(".list-check").attr("checked", false);';
            $ht_js .= 'if (act_id == "cb-favadd" || act_id == "cb-watchadd" || act_id == "cb-favclear" || act_id == "cb-watchclear") { if(act_id == "cb-favadd"){t.attr("id", "cb-favclear")}if(act_id == "cb-favclear"){t.attr("id", "cb-favadd")} if(act_id == "cb-watchadd"){t.attr("id", "cb-watchclear")}if(act_id == "cb-watchclear"){t.attr("id", "cb-watchadd")}  $(data).insertBefore("#addto-types"); t.find("i").removeClass("spinner icon-spinner").addClass((act_id == "cb-favadd" || act_id == "cb-watchadd") ? "icon-checkbox-checked" : "icon-checkbox-unchecked"); } else { $(data).insertBefore("#addto-types"); t.parent().prev().find("i").addClass("iconBe-chevron-down").removeClass("spinner icon-spinner");}';
            $ht_js .= 'if (act_id != "cb-favadd" && act_id != "cb-watchadd" && act_id != "cb-favclear" && act_id != "cb-watchclear") { if(t.hasClass("cb-label-add")){t.find("i").removeClass("icon-checkbox-unchecked").addClass("icon-checkbox-checked");t.removeClass("cb-label-add").addClass("cb-label-clear");t.attr("id", t.attr("id").replace("cb-label-add", "cb-label-clear"))}else{t.find("i").removeClass("icon-checkbox-checked").addClass("icon-checkbox-unchecked");t.removeClass("cb-label-clear").addClass("cb-label-add");t.attr("id", t.attr("id").replace("cb-label-clear", "cb-label-add"))} $("#entry-action-pl .dl-trigger").trigger("click"); }';
            $ht_js .= '});';
            $ht_js .= '});';
        }
        /* download buttons */
        if (self::$cfg['file_downloads'] == 1 and $vsrc != 'embed') {
            $ht_js .= '$("#download-types .symbol-button").click(function(e){';
            $ht_js .= 'if(e.target.className=="dl" || $(this).find(".dl").length>0)return;';
            $ht_js .= 'var t=$(this); var key = t.find("span").attr("rel-href");';
            $ht_js .= 'var u="' . self::$cfg['main_url'] . '/' . VHref::getKey("download") . '?p=" + key;';
            $ht_js .= 't.mask(""); $.post(u, {}, function(data){t.find("p").detach();$(data).insertAfter(t); t.unmask();});';
            $ht_js .= '});';
        }
        /* submit file flag request */
        if (self::$cfg['file_flagging'] == 1) {
            $ht_js .= '$(".file-flag-reason").click(function(){';
            $ht_js .= 'var t = $(this);';
            $ht_js .= 'if(t.find("i").hasClass("icon-checkbox-checked")){return}';
            $ht_js .= 'var crid = "' . (isset($_SESSION['reason'][self::$file_key]) ? (int) $_SESSION['reason'][self::$file_key] : 0) . '";';
            $ht_js .= 'var rid = $(this).attr("id").split("-").slice(-1)[0];';
            $ht_js .= 'var post_url = c_url+"&do=file-flag"+rid;';
            $ht_js .= 'if(!t.find("i").hasClass("icon-checkbox-checked")){t.find("i").removeClass().addClass("spinner icon-spinner");}';
            $ht_js .= '$.post(post_url, $("#user-files-form").serialize(), function(data){';
            $ht_js .= '$("#cb-response-wrap, #cb-response").detach();';
            $ht_js .= '$(data).insertBefore("#file-flag-wrap");';
            $ht_js .= 't.find("i").removeClass("spinner icon-spinner").addClass(((data.includes("error-message")&&!t.find("i").hasClass("icon-checkbox-checked")) ? "icon-checkbox-unchecked" : "icon-checkbox-checked"));';
            $ht_js .= '});';
            $ht_js .= '});';
        }
        /* follow/unfollow action */
        if (self::$cfg['user_follows'] == 1) {
            $ht_js .= '$(document).on("click", ".follow-action", function(e){';
            $ht_js .= '$(".follow-txt span").text("' . self::$language['frontend.global.loading'] . '");';
            $ht_js .= '$.post(c_url+"&do=user-follow", $("#user-files-form").serialize(), function(data){';
            $ht_js .= '$(".follow-txt span").text("' . self::$language['frontend.global.followed'] . '");';
            $ht_js .= 'if(typeof $("#vs-chat").html()!== "undefined")setTimeout(function(){document.getElementById("vs-chat").contentWindow.postMessage({"viz":"not","location":window.location.href},"' . $_SESSION['live_chat_server'] . '");},3000);';
            $ht_js .= '});';
            $ht_js .= '});';
            $ht_js .= '$(document).on("click", ".unfollow-action", function(e){';
            $ht_js .= '$(".follow-txt span").text("' . self::$language['frontend.global.loading'] . '");';
            $ht_js .= '$.post(c_url+"&do=user-unfollow", $("#user-files-form").serialize(), function(data){';
            $ht_js .= '$(".follow-txt span").text("' . self::$language['frontend.global.unfollowed'] . '");';
            $ht_js .= 'if(typeof $("#vs-chat").html()!== "undefined")setTimeout(function(){document.getElementById("vs-chat").contentWindow.postMessage({"viz":"not","location":window.location.href},"' . $_SESSION['live_chat_server'] . '");},3000);';
            $ht_js .= '});';
            $ht_js .= '});';
        }
        /* unsubscribe request */
        if (self::$cfg['user_subscriptions'] == 1) {
            $ht_js .= '$(document).on("click", ".unsubscribe-action", function(e){';
            $ht_js .= 'if($("#sub-wrap .sub-txt:first").text()=="' . self::$language['frontend.global.unsubscribed'] . '")return;';
            $ht_js .= '$("#sub-wrap .sub-txt:first").text("' . self::$language['frontend.global.loading'] . '");';
            $ht_js .= '$.post(c_url+"&do=user-unsubscribe", $("#user-files-form").serialize(), function(data){';
            $ht_js .= '$("#sub-wrap .sub-txt:first, .page_holder_left .sub-txt:first").text("' . self::$language['frontend.global.unsubscribed'] . '");';
            $ht_js .= 'var ht = \'<div class="notice-message-text" onclick="jQuery(this).detach();">' . self::$language['notif.success.subscribe.cancel'] . '</div>\';';
            $ht_js .= '$(ht).insertAfter("#sub-wrap article");';
            $ht_js .= 'if (typeof $("#vs-chat").html()!== "undefined")setTimeout(function(){document.getElementById("vs-chat").contentWindow.postMessage({"viz":"not","location":window.location.href},"' . $_SESSION['live_chat_server'] . '");},3000);';
            $ht_js .= '});';
            $ht_js .= '});';
        }

        $html .= '
                <script type="text/javascript">
                    $(function(){' . $ht_js . '});
                </script>
            ';

        return $html;
    }
    /* sub/unsub popup */
    public static function subHtml($unsub = false, $from = 'view')
    {
        $db       = self::$db;
        $cfg      = self::$cfg;
        $language = self::$language;
        $filter   = self::$filter;

        if (self::$cfg['user_subscriptions'] == 0) {
            return;
        }

        $ht = null;
        $t1 = $unsub ? $language['frontend.global.unsubscribe'] : $language['frontend.global.sub.opt'];

        if ($from == 'channel') {
            $adr   = self::$filter->clr_str($_SERVER['REQUEST_URI']);
            $param = array_pop(explode(self::$href['channel'], $adr));
            $e     = explode('/', $param);
            $c     = explode('?', $param);
            $name  = $filter->clr_str(str_replace(['@', '/'], ['', ''], $c[0]));
            if ($name != '') {
                $cc = $db->execute(sprintf("SELECT `usr_id`, `usr_key`, `usr_user`, `usr_dname` FROM `db_accountuser` WHERE `usr_user`='%s' LIMIT 1;", $name));
                if ($cc->fields['usr_id'] > 0) {
                    $key  = $cc->fields['usr_key'];
                    $name = $cc->fields['usr_dname'] != '' ? $cc->fields['usr_dname'] : $cc->fields['usr_user'];
                    $_url = $cfg['main_url'] . '/' . VHref::getKey("channel") . '?a=&c=' . $key . '&do=sub-continue';
                }
            }
        } elseif ($from == 'home') {
            $key = self::$filter->clr_str($_GET['u']);
            $cc  = $db->execute(sprintf("SELECT `usr_id`, `usr_user`, `usr_dname` FROM `db_accountuser` WHERE `usr_key`='%s' LIMIT 1;", $key));
            if ($cc->fields['usr_id'] > 0) {
                $_url = $cfg['main_url'] . '?do=sub-continue&c=' . $key;
                $name = $cc->fields['usr_dname'] != '' ? $cc->fields['usr_dname'] : $cc->fields['usr_user'];
            }
        } else {
            $cc = $db->execute(sprintf("SELECT A.`usr_id`, A.`usr_user`, A.`usr_dname` FROM `db_accountuser` A, `db_%sfiles` B WHERE B.`file_key`='%s' AND A.`usr_id`=B.`usr_id` LIMIT 1;", self::$type, self::$file_key));
        }

        if ((int) $_SESSION['USER_ID'] == 0 or (int) $_SESSION['USER_ID'] == $cc->fields['usr_id']) {
            return;
        }

        $ht_js = 'c_url=current_url+menu_section;$(document).ready(function(){var t=$("#sub-wrap .p").text().replace("##USER##", "<b>"+' . ($from == 'view' ? '$(".p-info.p-less.vdc-1 div a:first").text()' : ($from == 'channel' ? '"' . $name . '"' : '"' . $name . '"')) . '+"</b>"); $("#sub-wrap .p").html(t);});';
        $ht_js .= '$(".usn").text(' . ($from == 'view' ? '$(".p-info.p-less a:first").text()' : '"' . $name . '"') . ');';

        if ($unsub) {
            $ex  = $db->execute(sprintf("SELECT `expire_time` FROM `db_subusers` WHERE `usr_id`='%s' AND `usr_id_to`='%s' LIMIT 1;", (int) $_SESSION['USER_ID'], $cc->fields['usr_id']));
            $exp = $ex->fields['expire_time'];

            $ht .= '<form id="user-files-form" class="entry-form-class content-wrap">
                        <div class="p">' . $language['files.text.unsub.warn1'] . '</div>' . $language['files.text.unsub.warn2'] . '<b>' . $exp . '</b><div class="clearfix"></div><br>';
            $ht .= '
                        <div class="subscribers-off">
                            <button class="button-grey search-button save-entry-button form-button unsubscribe-action" rel="nofollow" rel-usr="' . $key . '">
                                <span class="sub-txt">' . $language['frontend.global.unsubscribe'] . '</span>
                            </button>
                            <a href="javascript:;" class="cancel-trigger" rel="nofollow" onclick="$(\'.fancybox-close\').click()">
                                <span class="sub-txt-off">' . $language['frontend.global.cancel'] . '</span>
                            </a>
                            <input type="hidden" name="uf_vuid" value="' . $cc->fields['usr_id'] . '">
                        </div>
                    </form>
            ';
        } else {
            $sql = sprintf("SELECT `pk_id`, `pk_name`, `pk_descr`, `pk_price`, `pk_priceunit`, `pk_priceunitname`, `pk_period` FROM `db_subtypes` WHERE `pk_active`='1';");
            $rs  = $db->execute($sql);
            if ($rs->fields['pk_id']) {
                $s     = 0;
                $li_ht = null;
                $ss_ht = null;
                $ln    = explode(",", $language['frontend.pkinfo.pkdur1']);

                while (!$rs->EOF) {
                    $pk_id        = $rs->fields['pk_id'];
                    $pk_key       = md5($cfg['global_salt_key'] . $pk_id);
                    $pk_name      = $rs->fields['pk_name'];
                    $pk_descr     = $rs->fields['pk_descr'];
                    $pk_price     = $rs->fields['pk_price'];
                    $pk_priceunit = $rs->fields['pk_priceunit'];
                    $pk_period    = $rs->fields['pk_period'];

                    switch ($pk_period) {
                        case 1:
                            $tt1 = $ln[0];
                            $_s2 = array('##NR##', '##DUR##');
                            $_r2 = array(3, $language['frontend.global.days']);
                            $_s3 = array('##NR##', '##DUR##');
                            $_r3 = array(6, $language['frontend.global.days']);
                            $tt2 = str_replace($_s2, $_r2, $language['frontend.global.every']);
                            $tt3 = str_replace($_s3, $_r3, $language['frontend.global.every']);
                            break;
                        case 30:
                            $tt1 = $ln[1];
                            $_s2 = array('##NR##', '##DUR##');
                            $_r2 = array(3, $language['frontend.global.month']);
                            $_s3 = array('##NR##', '##DUR##');
                            $_r3 = array(6, $language['frontend.global.month']);
                            $tt2 = str_replace($_s2, $_r2, $language['frontend.global.every']);
                            $tt3 = str_replace($_s3, $_r3, $language['frontend.global.every']);
                            break;
                        case 365:
                            $tt1 = $ln[4];
                            $_s2 = array('##NR##', '##DUR##');
                            $_r2 = array(3, $language['frontend.global.years']);
                            $_s3 = array('##NR##', '##DUR##');
                            $_r3 = array(5, $language['frontend.global.years']);
                            $tt2 = str_replace($_s2, $_r2, $language['frontend.global.every']);
                            $tt3 = str_replace($_s3, $_r3, $language['frontend.global.every']);
                            break;
                        default:
                            $tt1 = $ln[4];
                            $_s1 = array('##NR##', '##DUR##');
                            $_r1 = array($pk_period, $language['frontend.global.days']);
                            $_s2 = array('##NR##', '##DUR##');
                            $_r2 = array((3 * $pk_period), $language['frontend.global.days']);
                            $_s3 = array('##NR##', '##DUR##');
                            $_r3 = array((6 * $pk_period), $language['frontend.global.days']);
                            $tt1 = str_replace($_s1, $_r1, $language['frontend.global.every']);
                            $tt2 = str_replace($_s2, $_r2, $language['frontend.global.every']);
                            $tt3 = str_replace($_s3, $_r3, $language['frontend.global.every']);
                            break;
                    }

                    $li_ht .= '<li><a href="#section' . $s . '" class="icon icon-star" rel="nofollow"><span>' . $pk_name . '</span></a></li>';

                    $ss_ht .= '<section id="section' . $s . '" class="' . ($s == 0 ? 'content-current' : null) . '">';
                    $ss_ht .= '<div id="section-resp' . $pk_id . '"></div>';
                    $ss_ht .= '<form id="entry-form' . $pk_id . '" class="entry-form-class" method="post">';
                    $ss_ht .= '<article><h3 class="content-title">Channel Subscription to <span class="usn">(##USER##)</span></h3><div class="line"></div></article>';
                    $ss_ht .= nl2br($pk_descr);
                    $ss_ht .= '<div class="clearfix"></div>';
                    $ss_ht .= '<div class="price-box">';
                    $ss_ht .= '<div class="price-fig">' . $pk_priceunit . $pk_price . '</div>';
                    $ss_ht .= '<div class="icheck-box"><input type="radio" value="1" name="price_fr" checked="checked"><label>' . $tt1 . '</label></div>';
                    $ss_ht .= '</div>';
                    $ss_ht .= '<div class="price-box">';
                    $ss_ht .= '<div class="price-fig">' . $pk_priceunit . ($pk_price * 3) . '</div>';
                    $ss_ht .= '<div class="icheck-box"><input type="radio" value="3" name="price_fr"><label>' . $tt2 . '</label></div>';
                    $ss_ht .= '</div>';
                    $ss_ht .= '<div class="price-box">';
                    $ss_ht .= '<div class="price-fig">' . $pk_priceunit . ($pk_price * ($pk_period == 365 ? 5 : 6)) . '</div>';
                    $ss_ht .= '<div class="icheck-box"><input type="radio" value="' . ($pk_period == 365 ? 5 : 6) . '" name="price_fr"><label>' . $tt3 . '</label></div>';
                    $ss_ht .= '</div>';
                    $ss_ht .= '<div><br><button onfocus="blur();" value="1" type="button" class="continue-payment-button button-grey search-button form-button" name="btn_continue"><i class="icon-forward"></i> <span>Continue</span></button></div>';
                    $ss_ht .= '<div><input type="hidden" name="sk" value="' . $pk_key . '"><input type="hidden" name="si" value="entry-form' . $pk_id . '"></div>';
                    $ss_ht .= '</form>';
                    $ss_ht .= '</section>';

                    $s += 1;
                    $rs->MoveNext();
                }

                $ht .= '
                <div id="tab-content" class="tabs tabs-style-line">
                    <nav>
                        <ul class="px-20">
                            ' . $li_ht . '
                        </ul>
                    </nav>
                    <div class="content-wrap">
                        ' . $ss_ht . '
                    </div>
                </div>';
                $ht_js .= '(function () {[].slice.call(document.querySelectorAll("#tab-content.tabs")).forEach(function (el) {new CBPFWTabs(el);});})();';
                $ht_js .= '$("#tab-content .icheck-box input").each(function () {var self = $(this);self.iCheck({checkboxClass: "icheckbox_square-blue",radioClass: "iradio_square-blue",increaseArea: "20%"});});';
                $ht_js .= '$(document).ready(function(){$(".price-box").click(function(){var t = $(this);var self = t.find("input");self.iCheck("toggle", function(node){});});$(".continue-payment-button").click(function(){var t = $(this);var f = t.parent().parent().serialize();$(".fancybox-inner").mask("");var u = "' . ($from == 'view' ? $cfg['main_url'] . '/' . VHref::getKey("watch") . '?' . self::$type[0] . '=' . self::$file_key . '&do=sub-continue' : (($from == 'channel' or $from == 'home') ? $_url : null)) . '";if (u == "") return;$.post(u, f, function(data) {$("#section-resp"+t.parent().parent().attr("id").replace("entry-form", "")).html(data);});});});';
            }
        }

        $html = '<style>#sub-wrap .profile_count a{float: left;}.price-box{min-width: 150px; cursor: pointer; padding: 15px; border: 1px solid #aaa; display: inline-block; margin: 15px 15px 0px 0px;}.price-fig{text-align: center; font-size: 20px;}.price-box .icheck-box{text-align: center;}.price-box .icheck-box label{cursor: pointer;}.dark .price-fig{color: #eee;}</style>
        <div class="lb-margins" id="sub-wrap">
            <article>
                    <h3 class="content-title"><i class="icon-users"></i> ' . $t1 . '</h3>
                    <div class="line mb-0"></div>
                </article>
                ' . $ht . '
        </div>';
        $html .= VGenerate::declareJS($ht_js);

        return $html;
    }
    /* process continue request, prepare and redirect to pp */
    public static function subContinue($from = 'view')
    {
        $html = null;

        if ($_POST) {
            $cfg            = self::$cfg;
            $db             = self::$db;
            $class_database = self::$dbc;
            $class_filter   = self::$filter;
            $language       = self::$language;

            $type      = isset($_GET['l']) ? 'live' : (isset($_GET['v']) ? 'video' : (isset($_GET['i']) ? 'image' : (isset($_GET['a']) ? 'audio' : (isset($_GET['d']) ? 'doc' : (isset($_GET['b']) ? 'blog' : (isset($_GET['s']) ? 'short' : $from))))));
            $price_frs = array(1, 3, 5, 6);
            $price_fr  = (int) $_POST['price_fr'];
            $pk_id     = (int) str_replace('entry-form', '', $_POST['si']);
            $pk_key    = $class_filter->clr_str($_POST['sk']);
            $ch_pk_key = md5($cfg['global_salt_key'] . $pk_id);

            if ($pk_id == 0 or !in_array($price_fr, $price_frs) or $pk_key !== $ch_pk_key or !$type) {
                $html .= VGenerate::noticeTpl('', $language['notif.error.invalid.request'], '');

                $ht_js = '$(".fancybox-inner").unmask();';

                $html .= VGenerate::declareJS($ht_js);
            } else {
                $pcfg = $class_database->getConfigurations('paypal_test,paypal_email,paypal_test_email,paypal_logging,discount_codes');
                $rs   = $db->execute(sprintf("SELECT `pk_id`, `pk_name`, `pk_price`, `pk_priceunitname`, `pk_period` FROM `db_subtypes` WHERE `pk_id`='%s' AND `pk_active`='1' LIMIT 1;", $pk_id));

                if ($rs->fields['pk_id']) {
                    $pk_name          = $rs->fields['pk_name'];
                    $pk_price         = $rs->fields['pk_price'];
                    $pk_priceunitname = $rs->fields['pk_priceunitname'];
                    $pk_period        = $rs->fields['pk_period'];
                    $pk_amount        = $pk_price * $price_fr;

                    $pp_base   = $pcfg['paypal_test'] == 1 ? 'https://www.sandbox.paypal.com' : 'https://www.paypal.com';
                    $pp_mail   = rawurlencode($pcfg['paypal_test'] == 1 ? $pcfg['paypal_test_email'] : $pcfg['paypal_email']);
                    $pp_return = rawurlencode($cfg['main_url'] . '/' . VHref::getKey('watch') . '?' . self::$type[0] . '=' . self::$file_key . '&fsn=1');
                    $pp_cancel = rawurlencode($cfg['main_url'] . '/' . VHref::getKey('watch') . '?' . self::$type[0] . '=' . self::$file_key);
                    $pp_a3     = $pk_amount;

                    if ($from == 'view') {
                        $uinfo = $db->execute(sprintf("SELECT A.`usr_user`, A.`usr_id` FROM `db_accountuser` A, `db_%sfiles` B WHERE A.`usr_id`=B.`usr_id` AND B.`file_key`='%s' LIMIT 1", self::$type, self::$file_key));
                        if (!$uinfo->fields['usr_user']) {
                            return;
                        }

                        $pp_user  = $uinfo->fields['usr_user'];
                        $pk_id_to = $uinfo->fields['usr_id'];
                        $pk_str   = self::$type[0] . self::$file_key;
                    } else if ($from == 'channel' or $from == 'home' or $from == 'channels') {
                        $uu    = $class_filter->clr_str($_GET['c']);
                        $uinfo = $db->execute(sprintf("SELECT A.`usr_user`, A.`usr_id` FROM `db_accountuser` A WHERE A.`usr_key`='%s' LIMIT 1;", $uu));
                        if (!$uinfo->fields['usr_user']) {
                            return;
                        }

                        $pp_user  = $uinfo->fields['usr_user'];
                        $pk_id_to = $uinfo->fields['usr_id'];
                        $pk_str   = '0';

                        if ($from == 'channel') {
                            $pp_return = rawurlencode(VHref::channelURL(["username" => $pp_user]) . '?fsn=1');
                            $pp_cancel = rawurlencode(VHref::channelURL(["username" => $pp_user]));
                        } elseif ($from == 'channels') {
                            $pp_return = rawurlencode($cfg['main_url'] . '/' . VHref::getKey('channels') . '?fsn=1');
                            $pp_cancel = rawurlencode($cfg['main_url'] . '/' . VHref::getKey('channels'));
                        } else {
                            $pp_return = rawurlencode($cfg['main_url'] . '/' . VHref::getKey('index') . '?fsn=1');
                            $pp_cancel = rawurlencode($cfg['main_url'] . '/' . VHref::getKey('index'));
                        }
                    }

                    switch ($pk_period) {
                        case 30:
                            $pp_p3 = $price_fr;
                            $pp_t3 = 'M';
                            break;
                        case 365:
                            $pp_p3 = $price_fr;
                            $pp_t3 = 'Y';
                            break;
                        default:
                            $pp_p3 = $pk_period * $price_fr;
                            $pp_t3 = 'D';
                            break;
                    }
                    $item_name   = rawurlencode($cfg['website_shortname'] . ', ' . $language['frontend.global.subscribe.to'] . ' ' . $pp_user . ' - ' . $pk_name);
                    $item_number = rawurlencode('s|' . $pk_id . '|' . $ch_pk_key . '|' . (int) $_SESSION['USER_ID'] . '|' . $pp_p3 . '|' . $pk_id_to . '|' . $price_fr . '|' . $pk_str);

                    $pp_link = sprintf("%s/cgi-bin/webscr?cmd=_xclick-subscriptions&a3=%s&p3=%s&t3=%s&src=1&sra=1&rm=2&business=%s&return=%s&cancel_return=%s&currency_code=%s&item_name=%s&item_number=%s&no_shipping=1&no_note=1",
                        $pp_base, $pk_amount, $pp_p3, $pp_t3, $pp_mail, $pp_return, $pp_cancel, $pk_priceunitname, $item_name, $item_number);

                    if ((int) $_SESSION['USER_ID'] == 0 or (int) $_SESSION['USER_ID'] == $pk_id_to) {
                        $pp_link = $cfg['main_url'];
                    }

                    $ht_js = 'window.location="' . $pp_link . '";';

                    $html .= VGenerate::declareJS($ht_js);
                }
            }
        }

        return $html;
    }
    /* verify paypal response */
    public static function verifyPP()
    {
        if ($_POST) {
            $class_filter   = self::$filter;
            $class_database = self::$dbc;
            $cfg            = self::$cfg;
            $db             = self::$db;
            $language       = self::$language;
            $smarty         = self::$smarty;

            $pcfg      = $class_database->getConfigurations('paypal_log_file,paypal_logging,paypal_test,paypal_email,paypal_test_email,backend_email,backend_username,backend_notification_payment,sub_shared_revenue');
            $ipn_check = (isset($_GET['do']) and $_GET['do'] == 'ipn') ? true : false;

            if ($cfg['user_subscriptions'] == 1 and $ipn_check) {
                $p             = new VPaypalSubscribe;
                $p->paypal_url = $pcfg['paypal_test'] == 1 ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';

                if ($p->validate_ipn()) {
                    $ipn_info     = explode('|', rawurldecode($p->ipn_data['item_number']));
                    $pk_id        = (int) $ipn_info[1];
                    $pk_usr_id    = (int) $ipn_info[3];
                    $pk_usr_id_to = (int) $ipn_info[5];

                    $pk_key     = $ipn_info[2];
                    $ch_pk_key  = md5($cfg['global_salt_key'] . $pk_id);
                    $pk_paid    = (float) $p->ipn_data['mc_gross'];
                    $sub_id     = $class_filter->clr_str($p->ipn_data['subscr_id']);
                    $ipn_pk_per = $ipn_info[6];
                    $ftype      = $ipn_info[7];
                    $notify     = false;

                    /* recurring_payment_suspended_due_to_max_failed_payment */
                    if ($p->ipn_data['txn_type'] === 'recurring_payment_suspended_due_to_max_failed_payment') {
                        //email user (and admin?) about max failed payment?
                    }

                    /* cancelation request */
                    if ($pk_key === $ch_pk_key and ($p->ipn_data['txn_type'] === 'subscr_cancel' or $p->ipn_data['txn_type'] === 'subscr_eot')) {
                        //DO EMAIL NOTIFICATIONS ADMIN AND USER FOR CANCELLING?
                        $sql = sprintf("SELECT A.`pk_period`, B.`expire_time` FROM `db_subtypes` A, `db_subusers` B WHERE A.`pk_id`='%s' AND A.`pk_id`=B.`pk_id` AND A.`pk_active`='1' LIMIT 1;", $pk_id);

                        $pq = $db->execute($sql);
                        if ($pq->fields['pk_period'] and $pk_usr_id > 0 and $pk_usr_id_to > 0) {
                            $ts = $db->execute(sprintf("SELECT `db_id` FROM `db_subtemps` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `active`='1' LIMIT 1;", $pk_usr_id, $pk_usr_id_to));
                            if ($ts->fields['db_id']) {
                                $db->execute(sprintf("UPDATE `db_subtemps` SET `pk_id`='%s', `expire_time`='%s' WHERE `db_id`='%s' LIMIT 1;", $pk_id, $pq->fields['expire_time'], $ts->fields['db_id']));
                            } else {
                                $_ins = array("usr_id" => $pk_usr_id, "usr_id_to" => $pk_usr_id_to, "pk_id" => $pk_id, "expire_time" => $pq->fields['expire_time'], "active" => 1);

                                $class_database->doInsert('db_subtemps', $_ins);
                            }
                            $db->execute(sprintf("UPDATE `db_subpayouts` SET `is_cancel`='1', `cancel_time`='%s' WHERE `sub_id`='%s' LIMIT 1;", date("Y-m-d H:i:s"), $sub_id));
                            $sql = sprintf("UPDATE `db_subusers` SET `pk_id`='0' WHERE `pk_id`='%s' AND `subscriber_id`='%s' LIMIT 1;", $pk_id, $sub_id);
                            $rs  = $db->execute($sql);

                            if ($db->Affected_Rows() > 0) {
                                /* update sub list */
                                //$_SESSION['USER_ID']    = $pk_usr_id;
                                //$_SESSION['USER_NAME']    = $user_data['uname'];
                                $sub_act = VView::chSubscribe(1, $pk_usr_id_to, false, $pk_usr_id, $user_data['uname']);
                                /* request to chat server */
                                $un = VSubscriber::unsub_request($pk_usr_id_to, $pk_usr_id);
                                $d1 = new DateTime($pq->fields['expire_time']);
                                $d2 = new DateTime("now");
                                if ($d1 < $d2) {
                                    $notify_chat = self::sendChatRequest(array(0 => 'unsubscribe', 1 => $ftype), $pk_usr_id_to, $pk_usr_id, $user_data['uname']);
                                }
                            }
                        } else {
                            return;
                        }

                    }

                    /* recurring payment request */
                    if ($pk_key === $ch_pk_key and ($p->ipn_data['txn_type'] === 'subscr_payment' or $p->ipn_data['txn_type'] === 'recurring_payment')) {
                        $pq = $db->execute(sprintf("SELECT `pk_period`, `pk_name` FROM `db_subtypes` WHERE `pk_id`='%s' AND `pk_active`='1' LIMIT 1;", $pk_id));
                        if ($pq->fields['pk_period']) {
                            $pk_name     = $pq->fields['pk_name'];
                            $pk_period   = $ipn_pk_per * $pq->fields['pk_period'];
                            $expire_time = date("Y-m-d H:i:s", strtotime('+' . $pk_period . ' days'));
                        } else {
                            return;
                        }

                        $rs = $db->execute(sprintf("SELECT `db_id`, `subscriber_id`, `expire_time` FROM `db_subusers` WHERE `usr_id`='%s' AND `usr_id_to`='%s' LIMIT 1;", $pk_usr_id, $pk_usr_id_to));
                        if ($rs->fields['db_id']) {
                            $et  = $rs->fields['expire_time'];
                            $sid = $rs->fields['subscriber_id'];
                            $un  = VSubscriber::unsub_request($pk_usr_id_to, $pk_usr_id);
                            $d1  = new DateTime($et);
                            $d2  = new DateTime("now");

                            if ($d1 > $d2) {
                                $expire_time = date("Y-m-d H:i:s", strtotime($et . ' + ' . $pk_period . ' days'));
                            }

                            $rs = $db->execute(sprintf("UPDATE `db_subusers` SET `pk_id`='%s', `pk_paid`='%s', `pk_paid_total`=`pk_paid_total`+%s, `subscriber_id`='%s', `subscribe_time`='%s', `expire_time`='%s' WHERE `db_id`='%s' LIMIT 1;", $pk_id, $pk_paid, $pk_paid, $sub_id, date("Y-m-d H:i:s"), $expire_time, $rs->fields['db_id']));

                            if ($db->Affected_Rows() > 0) {
                                $notify = true;
                            }

                        } else {
                            $sub_array = array(
                                "usr_id"         => $pk_usr_id,
                                "usr_id_to"      => $pk_usr_id_to,
                                "pk_id"          => $pk_id,
                                "pk_paid"        => $pk_paid,
                                "pk_paid_total"  => $pk_paid,
                                "subscriber_id"  => $sub_id,
                                "subscribe_time" => date("Y-m-d H:i:s"),
                                "expire_time"    => $expire_time,
                            );

                            $notify = $class_database->doInsert('db_subusers', $sub_array);
                        }

                        if ($notify) {
                            $cp         = $db->execute(sprintf("SELECT `usr_sub_share`, `usr_sub_perc`, `usr_sub_currency`, `usr_partner` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $pk_usr_id_to));
                            $sp         = $cp->fields['usr_sub_share'] == 1 ? $cp->fields['usr_sub_perc'] : $pcfg['sub_shared_revenue'];
                            $is_partner = $cp->fields['usr_partner'];

                            $pay_array = array(
                                "usr_id"        => $pk_usr_id,
                                "usr_id_to"     => $pk_usr_id_to,
                                "pk_id"         => $pk_id,
                                "pk_paid"       => number_format($pk_paid, 2),
                                "pk_paid_share" => number_format((($sp / 100) * $pk_paid), 2),
                                "sub_id"        => $sub_id,
                                "sub_time"      => date("Y-m-d H:i:s"),
                                "txn_id"        => $p->ipn_data['txn_id'],
                                "is_paid"       => 0,
                            );
                            $class_database->doInsert('db_subpayouts', $pay_array);

                            if ($db->Affected_Rows() > 0 or $new == 1) {
                                $notifier     = new VNotify;
                                $website_logo = $smarty->fetch($cfg['templates_dir'] . '/tpl_frontend/tpl_header/tpl_headerlogo.tpl');
                                $user_data    = VUserinfo::getUserInfo($pk_usr_id);
                                $user_data_to = VUserinfo::getUserInfo($pk_usr_id_to);
                                /* update sub list */
                                //$_SESSION['USER_ID']    = $pk_usr_id;
                                //$_SESSION['USER_NAME']    = $user_data['uname'];
                                $sub_act = VView::chSubscribe('', $pk_usr_id_to, false, $pk_usr_id, $user_data['uname']);
                                /* notification to chat server */
                                $notify_chat = self::sendChatRequest(array(0 => 'subscribe', 1 => $ftype), $pk_usr_id_to, $pk_usr_id, $user_data['uname'], $pk_name);
                                /* user notification */
                                $_replace = array(
                                    '##TITLE##'        => $language['payment.notification.subject.sub.fe'],
                                    '##LOGO##'         => $website_logo,
                                    '##H2##'           => $language['recovery.forgot.password.h2'] . $user_data['uname'] . ',',
                                    '##SUB_CHANNEL##'  => $user_data_to['uname'],
                                    '##PACK_NAME##'    => str_replace(array('&2B', '+'), array('&20', ' '), rawurldecode($p->ipn_data['item_name'])),
                                    '##PAID_TOTAL##'   => $pk_paid . $p->ipn_data['mc_currency'],
                                    '##PACK_EXPIRE##'  => $expire_time,
                                    '##PAID_RECEIPT##' => "",
                                    '##YEAR##'         => date('Y'),
                                );

                                $notifier->msg_subj = $language['payment.notification.subject.sub.fe'];
                                $notifier->dst_mail = VUserinfo::getUserEmail($pk_usr_id);
                                $notifier->dst_name = $user_data['uname'];
                                $notifier->Mail('frontend', 'payment_notification_fe', $_replace);
                                $_output[] = VUserinfo::getUserName($pk_usr_id) . ' -> payment_notification_fe -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                                /* admin notification */
                                if ($pcfg['backend_notification_payment'] == 1) {
                                    $notifier           = new VNotify;
                                    $notifier->msg_subj = $language['payment.notification.subject.sub.be'] . rawurldecode($p->ipn_data['payer_email']);
                                    $notifier->dst_mail = $pcfg['backend_email'];
                                    $notifier->dst_name = $pcfg['backend_username'];
                                    foreach ($p->ipn_data as $key => $value) {$receipt .= $key . ': ' . $value . '<br />';}
                                    $_replace = array(
                                        '##TITLE##'        => $language['payment.notification.subject.sub.be'] . rawurldecode($p->ipn_data['payer_email']),
                                        '##LOGO##'         => $website_logo,
                                        '##H2##'           => $language['recovery.forgot.password.h2'] . $pcfg['backend_username'] . ',',
                                        '##SUB_NAME##'     => $user_data['uname'],
                                        '##SUB_CHANNEL##'  => $user_data_to['uname'],
                                        '##PACK_NAME##'    => str_replace(array('&2B', '+'), array('&20', ' '), rawurldecode($p->ipn_data['item_name'])),
                                        '##PAID_TOTAL##'   => $pk_paid . $p->ipn_data['mc_currency'],
                                        '##PACK_EXPIRE##'  => $expire_time,
                                        '##PAID_RECEIPT##' => str_replace(array('&2B', '+'), array('&20', ' '), rawurldecode($receipt)),
                                        '##YEAR##'         => date('Y'),
                                    );

                                    $notifier->Mail('backend', 'payment_notification_be', $_replace);
                                    $_output[] = $pcfg['backend_username'] . ' -> payment_notification_be -> ' . $notifier->dst_mail . ' -> ' . date("Y-m-d H:i:s");
                                }

                                $log_mail = '.mailer.log';
                                VServer::logToFile($log_mail, implode("\n", $_output));
                            }
                        }
                    }
                }
            }
        }
    }
    /* send chat request */
    private static function sendChatRequest($type, $var1 = false, $var2 = false, $var3 = false, $var4 = false)
    {
        global $db, $cfg, $href, $class_database, $class_filter;

        $cip = date("Y-m-d"); //VServer::get_ip();
        $fk  = self::$file_key;
        if (is_array($type) and ($type[0] == 'subscribe' or $type[0] == 'unsubscribe')) {
            $fk = substr($type[1], 1);
        }

        if (!isset($_SESSION['chat_key']) and !is_array($_SESSION['chat_key'])) {
            $chat_key = md5($fk . $var1 . $var2 . $var3 . $cip . $cfg['live_chat_salt']);
        } else {
            $chat_key = $class_filter->clr_str($_SESSION['chat_key'][$fk]);
        }

        if ($chat_key == '' or $fk == '') {
            return;
        }

        if (!isset($_SESSION['live_chat_server'])) {
            $uid                          = (int) $_SESSION['USER_ID'];
            $_SESSION['live_chat_server'] = $class_database->singleFieldValue('db_accountuser', 'chat_temp', 'usr_id', $uid);
        }
        $url  = ($type == 'unfollow' or $type == 'unsubscribe') ? VHref::getKey("chat_url_4") : VHref::getKey("chat_url_3");
        $data = array(
            "a" => $chat_key,
            "b" => $type,
            "c" => $var1,
            "d" => $var3,
            "e" => $fk,
            "f" => $var2,
            "g" => $var4,
        );
        $data_string = json_encode($data);
        //$uu        = $_SESSION['live_chat_server'].'/'.$url.'/'.$chat_key.'/'.$fk;
        $uu = self::$cfg['main_url'] . '/f_modules/m_frontend/m_cron/chat-server/notify.php';
        $ch = curl_init($uu);
        // curl_setopt($ch, CURLOPT_VERBOSE, true);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, CA_CERT);
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, CA_CERT);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        curl_exec($ch) . "\n";
        curl_close($ch);
    }
    /* running playlist entries */
    public function runningPlaylist($p)
    {
        $class_filter   = self::$filter;
        $class_database = self::$dbc;
        $cfg            = self::$cfg;
        $db             = self::$db;
        $language       = self::$language;

        $type          = $p[0];
        $f_key         = $p[1];
        $pl_key        = $class_filter->clr_str($_GET['p']);
        $res           = $db->execute(sprintf("SELECT A.`pl_files`, A.`pl_name`, A.`pl_privacy`, B.`usr_key` FROM `db_%splaylists` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`pl_key`='%s' LIMIT 1;", $type, $pl_key));
        $duration_show = ($type === 'audio' or $type === 'video' or $type === 'live' or $type === 'short') ? 1 : 0;

        if ($res->fields['pl_files']) {
            $pnr      = 1;
            $li_loop  = null;
            $pl_files = unserialize($res->fields['pl_files']);
            $pl_total = count($pl_files);
            $pl_priv  = $res->fields['pl_privacy'];
            $pl_ukey  = $res->fields['usr_key'];

            if (self::$cfg['file_watchlist'] == 1) {
                $user_watchlist = VBrowse::watchlistEntries();
            }

            $pl_idx = 1 + array_search(self::$file_key, $pl_files);

            $html = '
            <div id="playlist-loader" class="border-wrapper">
                <div class="playlist_holder">
                    <h3 class="">
                        <a class="a1" href="' . $cfg['main_url'] . '/' . VHref::getKey('playlist') . '?' . $type[0] . '=' . $pl_key . '" target="_blank">' . $res->fields['pl_name'] . '</a>
                        ' . (($pl_priv == 'private' or $pl_priv == 'personal') ? '<span class="prv-p"><i class="icon-lock">' . $language["frontend.global." . $pl_priv] . '</i></span>' : null) . '
                        <a class="a2 mb-0" href="' . VHref::channelURL(["username" => self::$userinfo['user_name']]) . '">' . self::$userinfo['user_dname'] . '</a>
                        <span> - ' . $pl_idx . ' / ' . $pl_total . '</span>
                    </h3>
                    <ul class="fileThumbs big clearfix playlist-items scroll">
                        ##LI_LOOP##
                    </ul>
                </div>
            </div>
            ';

            $o1 = array();
            $o2 = array();
            foreach ($pl_files as $k => $v) {
                $o1[] = $v;
                $o2[] = sprintf("'%s'", $v);
            }
            if (isset($o1[0])) {
                $sql = sprintf("SELECT
                         A.`usr_key`, A.`usr_user`, A.`usr_id`,
                         A.`usr_dname`, A.`ch_title`,
                         C.`file_key`, C.`file_title`, C.`upload_date`, C.`thumb_cache`,
                         C.`file_views`, C.`file_comments`, C.`file_favorite`, C.`file_duration`
                         FROM
                         `db_accountuser` A, `db_%sfiles` C
                         WHERE
                         C.`usr_id`=A.`usr_id` AND
                         C.`file_key` IN (%s)
                         ORDER BY FIND_IN_SET(C.`file_key`, '%s')
                         LIMIT %s;", $type, implode(',', $o2), implode(',', $o1), count($o1));

                $usr_sql = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_playlist_entries'], $sql) : $db->execute($sql);

                if ($usr_sql->fields['usr_id']) {
                    while (!$usr_sql->EOF) {
                        $usr_key     = $usr_sql->fields['usr_key'];
                        $usr_id      = $usr_sql->fields['usr_id'];
                        $_user       = $usr_sql->fields['usr_user'];
                        $_duser      = $usr_sql->fields['usr_dname'];
                        $_cuser      = $usr_sql->fields['ch_title'];
                        $_user       = $_duser != '' ? $_duser : ($_cuser != '' ? $_cuser : $_user);
                        $title       = $usr_sql->fields['file_title'];
                        $_views      = $usr_sql->fields['file_views'];
                        $_comm       = $usr_sql->fields['file_comments'];
                        $_fav        = $usr_sql->fields['file_favorite'];
                        $_dur        = VFiles::fileDuration($usr_sql->fields['file_duration']);
                        $datetime    = VUserinfo::timeRange($usr_sql->fields['upload_date']);
                        $key         = $usr_sql->fields['file_key'];
                        $thumb_cache = $usr_sql->fields['thumb_cache'];
                        $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;
                        $tmb_url     = VGenerate::thumbSigned($type, $key, array($usr_key, $thumb_cache), 0, 1, 1);

                        switch ($type) {
                            case "live":
                            case "video":
                            case "short":
                            case "audio":
                            case "image":
                            case "document":
                            case "doc":
                            case "blog":
                                $current = $key != $f_key ? 0 : 1;
                                $a_href  = $cfg['main_url'] . '/' . VGenerate::fileHref($type[0], $key, $title) . '&p=' . $pl_key;
                                break;
                        }

                        if ($cfg['file_watchlist'] == 1) {
                            if (is_array($user_watchlist) and in_array($key, $user_watchlist)) {
                                $watchlist_icon = 'icon-check';
                                $watchlist_text = $language['files.menu.watch.in'];
                                $watchlist_info = null;
                            } else {
                                $watchlist_icon = 'icon-clock';
                                $watchlist_text = $language['files.menu.watch.later'];
                                $watchlist_info = ' rel-key="' . $key . '" rel-type="' . self::$type . '"';
                            }
                        }

                        $li_loop .= '                    <li class="vs-column full-thumbs pp-li">
                                                            <div class="thumbs-wrapper">
                                                            <div class="pl-nr">' . ($_GET[$type[0]] == $key ? '<i class="icon-play6"></i>' : $pnr) . '</div>
                                                            ' . (self::$cfg['file_watchlist'] == 1 ? '
                                                                <div class="watch_later">
                                                                    <div class="watch_later_wrap"' . $watchlist_info . '>
                                                                        <div class="watch_later_holder">
                                                                            <i class="' . $watchlist_icon . '"></i>
                                                                        </div>
                                                                    </div>
                                                                    <span>' . $watchlist_text . '</span>
                                                                </div>
                                                            ' : null) . '
                                                                <figure class="effect-fullT' . $conv_class . '">
                                                                    <img src="' . $tmb_url . '" alt="' . $title . '" onclick="window.location=\'' . $a_href . '\'">
                                                                    ' . ($duration_show == 1 ? '
                                                                    <div class="caption-more">
                                                                        <span class="time-lenght' . ($is_live ? ' t-live' : null) . '">' . ($is_live ? self::$language['frontend.global.live'] : $_dur . $conv) . '</span>
                                                                    </div>
                                                                    ' : null) . '
                                                                </figure>
                                                                <div class="full-details-holder">
                                                                    <h2><a href="' . $a_href . '">' . $title . '</a></h2>
                                                                    <div class="vs-column-off pd">
                                                                        <span class="views-number">' . VFiles::numFormat($_views) . ' ' . ($_views == 1 ? self::$language['frontend.global.view'] : self::$language['frontend.global.views']) . '</span>
                                                                        <span class="i-bullet"></span>
                                                                        <span class="views-number">' . $datetime . '</span>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </li>';

                        $pnr += 1;

                        $usr_sql->MoveNext();
                    }
                }
            }
        }
        return str_replace('##LI_LOOP##', $li_loop, $html);
    }
    /* side column layout */
    public static function sideColumn($more = false)
    {
        $cfg          = self::$cfg;
        $language     = self::$language;
        $class_filter = self::$filter;
        $type         = self::$type;

        if (isset($_POST['f'])) {
            $type           = in_array($_POST['f'], array('video', 'short', 'live', 'image', 'audio', 'document', 'blog')) ? $class_filter->clr_str($_POST['f']) : 'video';
            self::$file_key = (int) $_POST['k'];
        }

        if ($more and !self::$rel) {
            $vdata  = self::$db->execute(sprintf("SELECT `file_title`, `file_tags` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, self::$file_key));
            $vtitle = $vdata->fields['file_title'];

            self::$rel = VGenerate::prepare($vtitle);
        }

        $sql = self::relatedSQL($type, self::$file_key, self::$rel, $more);
        $rdb = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_related'], $sql) : self::$db->execute($sql);

        $t = $rdb->RecordCount();

        if ($rdb->fields['file_key']) {
            if (self::$cfg['file_watchlist'] == 1) {
                $user_watchlist = VBrowse::watchlistEntries();
            }
            $li_loop = null;
            $mobile  = VHref::isMobile();

            $i = 1;
            while (!$rdb->EOF) {
                $v            = $rdb->fields['file_key'];
                $usr_key      = $rdb->fields['usr_key'];
                $usr_id       = $rdb->fields['usr_id'];
                $_user        = $rdb->fields['usr_user'];
                $title        = $rdb->fields['file_title'];
                $thumb_server = $rdb->fields['thumb_server'];
                $thumb_cache  = $rdb->fields['thumb_cache'];
                $thumb_cache  = $thumb_cache > 1 ? $thumb_cache : null;
                $user         = $rdb->fields['usr_user'];
                $displayname  = $rdb->fields['usr_dname'];
                $chname       = $rdb->fields['ch_title'];
                $embed_src    = $rdb->fields['embed_src'];
                $datetime     = VUserinfo::timeRange($rdb->fields['upload_date']);
                $user         = $displayname != '' ? $displayname : ($chname != '' ? $chname : $user);
                $ch_url       = VHref::channelURL(["username" => $_user]);
                /* missing folders fix */
                if (!is_dir($cfg['media_files_dir'] . '/' . $usr_key . '/t/' . $v)) {
                    mkdir($cfg['media_files_dir'] . '/' . $usr_key . '/t/' . $v, 0777, true);
                    copy($cfg['global_images_dir'] . '/0.jpg', $cfg['media_files_dir'] . '/' . $usr_key . '/t/' . $v . '/0' . $thumb_cache . '.jpg');
                    copy($cfg['global_images_dir'] . '/1.jpg', $cfg['media_files_dir'] . '/' . $usr_key . '/t/' . $v . '/1' . $thumb_cache . '.jpg');
                }
                $user_theme = isset($_SESSION['USER_THEME']) ? $_SESSION['USER_THEME'] : $_SESSION['theme_name'];
                $tmb_url    = (self::$type == 'blog' and !file_exists($cfg['media_files_dir'] . '/' . $usr_key . '/t/' . $v . '/0' . $thumb_cache . '.jpg')) ? self::$cfg['global_images_url'] . '/default-blog.png' : VGenerate::thumbSigned($type, $v, array($usr_key, $thumb_cache), 0, 1, 1);
                $def_thumb  = self::$cfg['global_images_url'] . '/loading-' . (strpos($user_theme, 'dark') !== false ? 'dark' : 'light') . '.gif';

                $key    = $v;
                $_dur   = VFiles::fileDuration($rdb->fields['file_duration']);
                $_views = $rdb->fields['file_views'];
                $_comm  = $rdb->fields['file_comments'];
                $_like  = $rdb->fields['file_like'];
                $vpv    = $rdb->fields['thumb_preview'];

                switch ($type) {
                    case "live":
                    case "video":
                    case "short":
                    case "audio":
                    case "image":
                    case "document":
                    case "doc":
                    case "blog":
                        $current = $v != $f_key ? 0 : 1;
                        $a_href  = $current == 0 ? $cfg['main_url'] . '/' . VGenerate::fileHref($type[0], $v, $title) : 'javascript:;';
                        break;
                }

                if ($cfg['file_watchlist'] == 1) {
                    if (is_array($user_watchlist) and in_array($v, $user_watchlist)) {
                        $watchlist_icon = 'icon-check';
                        $watchlist_text = $language['files.menu.watch.in'];
                        $watchlist_info = null;
                    } else {
                        $watchlist_icon = 'icon-clock';
                        $watchlist_text = $language['files.menu.watch.later'];
                        $watchlist_info = ' rel-key="' . $v . '" rel-type="' . self::$type . '"';
                    }
                }

                $ap = (int) $_SESSION['ap'];
                if (($type == 'video' or $type == 'audio' or $type == 'short') and ($embed_src == 'local' or $embed_src == 'youtube')) {
                    $sel_on    = $ap == 1 ? 'selected' : null;
                    $sel_off   = $ap == 0 ? 'selected' : null;
                    $check_on  = $ap == 1 ? 'checked="checked"' : null;
                    $check_off = $ap == 0 ? 'checked="checked"' : null;
                    $sw_on     = $language['frontend.global.switchon'];
                    $sw_off    = $language['frontend.global.switchoff'];

                    $switch = VGenerate::entrySwitch('autoplay-switch', '', $sel_on, $sel_off, $sw_on, $sw_off, 'autoplay_switch', $check_on, $check_off);
                } else {
                    $switch = false;
                }

                if ($i >= 1) {
                    $li_loop .= '
                        <li class="vs-column full-thumbs first-entry" rel-key="' . $v . '">
                                                    <div class="thumbs-wrapper">
                                                    ' . (self::$cfg['file_watchlist'] == 1 ? '
                                                        <div class="watch_later">
                                                            <div class="watch_later_wrap"' . $watchlist_info . '>
                                                                <div class="watch_later_holder">
                                                                    <i class="' . $watchlist_icon . '"></i>
                                                                </div>
                                                            </div>
                                                            <span>' . $watchlist_text . '</span>
                                                        </div>
                                                    ' : null) . '
                                                        <figure class="effect-fullT' . $conv_class . '">
                                                            ' . (($i < 7 and !$more) ? '<img src="' . $def_thumb . '" data-src="' . $tmb_url . '" alt="' . $title . '" class="mediaThumb" onclick="window.location=\'' . $a_href . '\'">' : '<img src="' . $def_thumb . '" data-src="' . $tmb_url . '" alt="' . $title . '" class="mediaThumb" onclick="window.location=\'' . $a_href . '\'">') . '
                                                            ' . (($type == 'video' or $type == 'audio' or $type == 'live' or $type == 'short') ? '<i class="play-btn" onclick="window.location=\'' . $a_href . '\'"></i>' : null) . '
                                                            ' . (($type == 'video' or $type == 'audio' or $type == 'live' or $type == 'short') ? '
                                                            <div class="caption-more">
                                                                <span class="time-lenght">' . $_dur . '</span>
                                                            </div>
                                                            ' : null) . '
                                                            ' . (!$mobile ? '
                                                            <div style="display:none;position:absolute;top:0;width:100%;height:100%" class="vpv">
                                                                <a href="' . $a_href . '">' . $title . '</a>
                                                                ' . ($vpv ? '
                                                                <video loop playsinline="true" muted="true" style="width:100%;height:100%" id="pv' . $v . rand(9999, 999999999) . '" rel-u="' . $usr_key . '" rel-v="' . md5($v . '_preview') . '" onclick="window.location=\'' . $a_href . '\'">
                                                                        <source src="' . self::$cfg['previews_url'] . '/default.mp4" type="video/mp4"></source>
                                                                </video>
                                                                ' : null) . '
                                                            </div>
                                                            ' : null) . '
                                                        </figure>

                                                        <div class="full-details-holder">
                                                            <h3><a href="' . $a_href . '" alt="' . $title . '">' . $title . '</a></h3>
                                                            <div class="d-flex d-column media-meta">
                                                                <a href="' . $ch_url . '" alt="' . $user . '">' . $user . '</a>
                                                                <div class="vs-column pd">
                                                                    <span class="views-number">' . VFiles::numFormat($_views) . ' ' . ($_views == 1 ? self::$language['frontend.global.view'] : self::$language['frontend.global.views']) . '</span>
                                                                    <span class="i-bullet"></span>
                                                                    <span class="views-number">' . $datetime . '</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                            <div class="clearfix"></div>
                            ' . ($i == -1 ? '<div class="line first-line"></div>' : null) . '
                                                </li>
                        ';
                    if ($i == 1 and self::$type != 'short') {
                        $sql = self::relatedSQL('short', self::$file_key, self::$rel, $more);
                        $rs  = self::$db_cache ? self::$db->CacheExecute(self::$cfg['cache_view_related'], $sql) : self::$db->execute($sql);
                        if ($rs->fields['file_key']) {
                            $svpv       = true;
                            $sdef_thumb = self::$cfg['global_images_url'] . '/loading-' . (strpos($user_theme, 'dark') !== false ? 'dark' : 'light') . '-shorts.gif';

                            $li_loop .= '<li class="shortsContent">';
                            $li_loop .= '<div class="line mt-10 mb-0"></div>';
                            $li_loop .= '<article>
                                            <h2 class="content-title mb-0">
                                                <i class="icon-mobile"></i><span class="heading f-18">Shorts</span>
                                            </h2>

                                        </article>';
                            $li_loop .= '<div class="swiper-ph swiper-ph-short"></div>
                                        <div class="swiper-top swiper-top-short-side" style="display:' . VGenerate::ssd() . '">
                                            <div class="swiper-button-prev swiper-button-prev-short"' . VGenerate::sso() . '></div>
                                            <div class="swiper-button-next swiper-button-next-short"' . VGenerate::sso() . '></div>
                                            <div class="swiper swiper-short-side">';
                            $li_loop .= '<div class="fileThumbs1 big1 clearfix1 view-list1 swiper-wrapper">';

                            while (!$rs->EOF) {
                                $sfile_key      = $rs->fields['file_key'];
                                $susr_key       = $rs->fields['usr_key'];
                                $sthumb_server  = $rs->fields['thumb_server'];
                                $sthumb_cache   = $rs->fields['thumb_cache'];
                                $sthumb_cache   = $sthumb_cache > 1 ? $sthumb_cache : null;
                                $stitle         = $rs->fields['file_title'];
                                $surl           = self::$cfg['main_url'] . '/' . VHref::getKey("shorts") . '/' . $sfile_key;
                                $sduration      = VFiles::fileDuration($rs->fields['file_duration']);
                                $sduration_show = 1;
                                $sviews         = VFiles::numFormat($rs->fields['file_views']);
                                $s_rel_v        = $rs->fields['embed_src'] == 'local' ? md5($sfile_key . '_preview') : null;

                                if ($sduration_show == 1 and $sduration == '00:00') {
                                    $sconv       = VFileinfo::get_progress($sfile_key);
                                    $sconv_class = ' converting';
                                    $sthumbnail  = '<img class="mediaThumb" src="' . $sdef_thumb . '" alt="' . $stitle . '">';
                                } else {
                                    $sconv       = null;
                                    $sconv_class = null;
                                    $simg_tmb    = is_file(self::$cfg['media_files_dir'] . '/' . $susr_key . '/t/' . $sfile_key . '/0' . $sthumb_cache . '.jpg');
                                    $simg_src    = VBrowse::thumbnail(array($susr_key, $sthumb_cache), $sfile_key, $sthumb_server);
                                    $sthumbnail  = '<img class="mediaThumb" src="' . $sdef_thumb . '" ' . ($simg_tmb ? 'data-src="' . $simg_src . '"' : null) . ' alt="' . $stitle . '" onclick="window.location=\'' . $surl . '\'">';
                                }

                                $li_loop .= '<div class="vs-column fourths small-thumbs swiper-slide">';
                                $li_loop .= '
                                            <div class="thumbs-wrapper">
                                                <figure class="effect-smallT">
                                                    ' . $sthumbnail . '
                                                    <div style="display:none;position:absolute;top:0px;width:100%;height:100%" class="vpv">
                                                    ' . ($svpv ? '<video loop playsinline="true" muted="true" style="width:100%;height:100%" id="pv' . $sfile_key . rand(9999, 999999999) . '" rel-u="' . $susr_key . '" rel-s="' . $s_rel_v . '" oncontextmenu="return false;" onclick="window.location=\'' . $surl . '\'">
                                                        <source src="' . self::$cfg['previews_url'] . '/default.mp4" type="video/mp4"></source>
                                                        </video>
                                                    ' : null) . '
                                                    </div>
                                                </figure>
                                                <h3><a href="' . $surl . '">' . $stitle . '</a></h3>
                                                <div class="caption">
                                                    <div class="vs-column">
                                                        <span class="views-number">' . $sviews . ' ' . ($sviews == 1 ? $language['frontend.global.view'] : $language['frontend.global.views']) . '</span>
                                                    </div>
                                                </div>
                                            </div>
                                ';
                                $li_loop .= '</div>';

                                $rs->MoveNext();
                            }

                            $li_loop .= '</div></div></div>';
                            $li_loop .= '<div class="line mt-10"></div>';
                            $li_loop .= '</li>';
                        }
                    }
                }

                $i += 1;
                $rdb->MoveNext();
            }
        } else {
            $li_loop = '<div class="d-flex ai-center m-auto">' . $language['frontend.global.results.none'] . '</div>';
        }
        if ($more) {
            return $li_loop;
        }

        $html .= VGenerate::advHTML(array(20, 54, self::$file_key));
        if (!isset($_GET['p'])) {
            $html .= '
                <div class="no-display"><h5>' . $language['frontend.global.up.next'] . '
                    ' . ($switch ? '<div class="place-right">
                        <div class="autoplay-switch">' . $switch . '</div>
                        <div class="autoplay-label">' . $language['view.files.autoplay'] . ' <i class="icon-info" rel="tooltip" title="' . $language['view.files.autoplay.tip'] . '"></i></div>
                    </div>' : null) . '
                </h5></div>';
        }

        $html .= '  <div id="main-content">
                <div class="swiper-ph swiper-ph-main"></div>
                <div class="swiper-top swiper-top-main" style="display:' . VGenerate::ssd() . '">
                    <div class="swiper-button-prev swiper-button-prev-main"' . VGenerate::sso() . '></div>
                    <div class="swiper-button-next swiper-button-next-main"' . VGenerate::sso() . '></div>
                    <div class="swiper swiper-main">
                        <div class="swiper-wrapper">
                            <div class="swiper-slide"><a href="#' . self::$type . '-suggested" class="get-media" rel="nofollow" rel-type="suggested"><span>' . str_replace('##TYPE##', self::$language["frontend.global." . self::$type[0] . ".p"], self::$language['view.files.related.alt']) . '</span></a></div>
                            <div class="swiper-slide"><a href="#' . self::$type . '-user" class="get-media" rel="nofollow" rel-type="user"><span>' . str_replace('##USER##', self::$userinfo['user_dname'], self::$language['view.files.from']) . '</span></a></div>
                            <div class="swiper-slide"><a href="#' . self::$type . '-category" class="get-media" rel="nofollow" rel-type="category"><span>' . self::$userinfo['upload_category'] . '</span></a></div>
                            <div class="swiper-slide"><a href="#' . self::$type . '-recent" class="get-media" rel="nofollow" rel-type="recent"><span>' . self::$language['view.files.recently'] . '</span></a></div>
                            ' . (isset($_SESSION['USER_ID']) ? '<div class="swiper-slide"><a href="#' . self::$type . '-watched" class="get-media" rel="nofollow" rel-type="watched"><span>' . self::$language['view.files.watched'] . '</span></a></div>' : null) . '
                        </div>
                    </div>
                </div>

            <form id="main-content-sort" method="post" action=""><input type="hidden" name="t" value=\'' . secured_encrypt(serialize(array('type' => self::$type, 'file_key' => self::$file_key, 'user' => self::$userinfo['user_id'], 'category' => self::$userinfo['upload_category_id']))) . '\'></form>
    ';
        $html .= '
                <div class="related-column playlist_holder">
                    <h2 class="video-content-title with-lines" style="display: none;"><i class="icon-' . (self::$type == 'doc' ? 'file' : (self::$type == 'blog' ? 'pencil2' : self::$type)) . '"></i>' . (str_replace('##TYPE##', $language["frontend.global." . $type[0] . ".p.c"], $language['view.files.suggestions'])) . '</h2>
                    <ul id="suggested-list" class="suggested-list fileThumbs big clearfix related-carousel">
                                                ' . $li_loop . '
                    </ul>
                    <ul id="user-list" class="suggested-list fileThumbs big clearfix related-carousel" style="display:none"></ul>
                    <ul id="category-list" class="suggested-list fileThumbs big clearfix related-carousel" style="display:none"></ul>
                    <ul id="recent-list" class="suggested-list fileThumbs big clearfix related-carousel" style="display:none"></ul>
                    ' . (isset($_SESSION['USER_ID']) ? '<ul id="watched-list" class="suggested-list fileThumbs big clearfix related-carousel" style="display:none"></ul>' : null) . '
                </div>

            ' . VGenerate::advHTML(array(49, 55, self::$file_key)) . '
            ';

        $html .= VGenerate::declareJS($ht_js);

        return $html;
    }
    /* follow/subscribe to channel user */
    public static function chSubscribe($unsubscribe = '', $fuid = '', $follow = false, $tuid = false, $tusr = false)
    {
        $db             = self::$db;
        $class_database = self::$dbc;
        $cfg            = self::$cfg;
        $class_filter   = self::$filter;

        $suid     = !$tuid ? (int) $_SESSION['USER_ID'] : (int) $tuid;
        $susr     = !$tusr ? $_SESSION['USER_NAME'] : $class_filter->clr_str($tusr);
        $upage_id = (int) $fuid;

        if (!$tuid and $fuid == (int) $_SESSION['USER_ID']) {
            return;
        }
        if ($upage_id == 0 or $suid == 0) {
            return;
        }
        if ($upage_id == $suid) {
            return;
        }

        if (!$follow) {
            $rs  = $db->execute(sprintf("SELECT `db_id` FROM `db_subscribers` WHERE `usr_id`='%s' AND `sub_id`='%s' LIMIT 1;", $upage_id, $suid));
            $sub = $rs->fields['db_id'];
        } else {
            $rs  = $db->execute(sprintf("SELECT `db_id` FROM `db_followers` WHERE `usr_id`='%s' AND `sub_id`='%s' LIMIT 1;", $upage_id, $suid));
            $sub = $rs->fields['db_id'];
        }

        $sub_is      = 0;
        $unsubscribe = $unsubscribe == 1 ? 1 : 0;

        $usql  = sprintf("SELECT `usr_user`, `usr_email` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $upage_id);
        $uinfo = self::$db_cache ? $db->execute(self::$cfg['cache_view_sub_id'], $usql) : $db->execute($usql);

        $uemail = $uinfo->fields['usr_email'];
        $uname  = $uinfo->fields['usr_user'];

        if (!$follow) {
            $qq = $class_database->singleFieldValue('db_subscriptions', 'sub_list', 'usr_id', $suid);

            if ($qq != '') {
                $ss = unserialize($qq);
                $_f = false;
                if ($unsubscribe == 1) {
                    foreach ($ss as $k => $_sub) {
                        if ($_sub == $upage_id and !$_f) {
                            unset($ss[$k]);
                            $_f = true;
                        }
                    }
                } else {

                    if (!in_array($upage_id, $ss)) {
                        $ss[] = $upage_id;
                        $_f   = true;
                    }
                }

                if ($_f) {
                    $db->execute(sprintf("UPDATE `db_subscriptions` SET `sub_list`='%s' WHERE `usr_id`='%s' LIMIT 1;", serialize($ss), $suid));
                }
            } else {
                if ($unsubscribe == 0) {
                    $ins = array("usr_id" => $suid, "sub_list" => serialize(array($upage_id)));

                    $class_database->doInsert('db_subscriptions', $ins);
                }
            }
        }

        if ($sub) {
            if (!$follow) {
                if ($unsubscribe) {
                    $db->execute(sprintf("DELETE FROM `db_subscribers` WHERE `db_id`='%s' LIMIT 1;", $sub));
                    if ($db->Affected_Rows() > 0) {
                        $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_subcount`=`usr_subcount`-1 WHERE `usr_id`='%s' LIMIT 1;", $upage_id));
                    }
                }

            } else {
                if ($unsubscribe) {
                    $db->execute(sprintf("DELETE FROM `db_followers` WHERE `db_id`='%s' LIMIT 1;", $sub));
                    if ($db->Affected_Rows() > 0) {
                        $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_followcount`=`usr_followcount`-1 WHERE `usr_id`='%s' LIMIT 1;", $upage_id));

                        self::sendChatRequest('unfollow', $susr, '', $upage_id);
                    }
                }
            }
        } else {
            //if no subscribers, add db entry
            $ins_ar = array("usr_id" => $upage_id, "sub_id" => $suid, "sub_time" => date("Y-m-d H:i:s"), "sub_type" => "all", "mail_new_uploads" => 1);

            if (!$follow) {
                if ($class_database->doInsert('db_subscribers', $ins_ar)) {
                    $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_subcount`=`usr_subcount`+1 WHERE `usr_id`='%s' LIMIT 1;", $upage_id));
                }
            } else {
                if ($class_database->doInsert('db_followers', $ins_ar)) {
                    $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_followcount`=`usr_followcount`+1 WHERE `usr_id`='%s' LIMIT 1;", $upage_id));

                    self::sendChatRequest('follow', $susr, '', $upage_id);
                }
            }
        }
        /* log action */
        $log = (($unsubscribe == 0 and $sub_is == 0) and $cfg['activity_logging'] == 1 and $action = new VActivity($suid, $upage_id)) ? $action->addTo((!$follow ? 'log_subscribing' : 'log_following'), $uname) : null;
        /* mail notification */
        if ($class_database->singleFieldValue('db_accountuser', (!$follow ? 'usr_mail_chansub' : 'usr_mail_chanfollow'), 'usr_id', (int) $upage_id) == 1 and $sub_is != 1 and $unsubscribe == 0) {
            $notifier           = new VNotify;
            $notifier->dst_mail = $uemail;
            $mail_do            = VNotify::queInit((!$follow ? 'subscribe' : 'follow'), array($notifier->dst_mail), $susr);
        }
    }

    /* update playlist views */
    public function updatePlaylistViews($pl_id, $type)
    {
        $db = self::$db;

        $sql = sprintf("UPDATE `db_%splaylists` SET `pl_views`=`pl_views`+1 WHERE `pl_key`='%s' LIMIT 1;", $type, $pl_id);

        if (!isset($_SESSION['view_pl'])) {
            $_SESSION['view_pl'] = array($pl_id);
            $db->execute($sql);
        } else {
            if (!in_array($pl_id, $_SESSION['view_pl'])) {
                $n                       = count($_SESSION['view_pl']);
                $_SESSION['view_pl'][$n] = $pl_id;
                $db->execute($sql);
            }
        }
    }

    /* check and update vivewing history */
    public function updateHistory()
    {
        $cfg            = self::$cfg;
        $db             = self::$db;
        $class_filter   = self::$filter;
        $class_database = self::$dbc;

        $uid  = intval($_SESSION['USER_ID']);
        $type = self::$type;

        if ($uid == 0 or $uid == self::$userinfo['user_id'] or $cfg[$type . "_module"] == 0 or $cfg['file_history'] == 0) {
            return false;
        }

        $file_key = self::$file_key;
        $res      = $db->execute(sprintf("SELECT `db_id` FROM `db_%shistory` WHERE `file_key`='%s' AND `usr_id`='%s' LIMIT 1;", $type, $file_key, $uid));

        if ($db_id = $res->fields['db_id']) {
            $db->execute(sprintf("UPDATE `db_%shistory` SET `views`=`views`+1, `datetime`='%s' WHERE `db_id`='%s' LIMIT 1;", $type, date("Y-m-d H:i:s"), $db_id));
        } else {
            $insert = ["usr_id" => $uid, "file_key" => $file_key, "views" => 1, "datetime" => date("Y-m-d H:i:s")];
            $class_database->doInsert('db_' . $type . 'history', $insert);
        }
    }
    public function updateViewLogs($for = false, $upage_id = false)
    {
        $type     = $for != 'channel' ? self::$type : $for;
        $file_key = self::$file_key;
        $cfg_ck   = $type != 'channel' ? 'file_views' : 'channel_views';
        $cache_id = self::$db_cache ? self::$cfg['cache_view_user_id'] : false;
        $do_db    = false;

        if (self::$cfg[$cfg_ck] == 0) {
            return false;
        }

        if ($for == 'channel') {
            $upage_id = !$upage_id ? self::$dbc->singleFieldValue('db_' . $type . 'files', 'usr_id', 'file_key', $file_key, $cache_id) : $upage_id;
            if (!isset($_SESSION['ch_views'])) {
                $_SESSION['ch_views']   = array();
                $_SESSION['ch_views'][] = $upage_id;
                $do_db                  = true;
            } elseif (isset($_SESSION['ch_views']) and !in_array($upage_id, $_SESSION['ch_views'])) {
                $_SESSION['ch_views'][] = $upage_id;
                $do_db                  = true;
            }

            if ($do_db) {
                self::$db->execute(sprintf("UPDATE `db_accountuser` SET `ch_lastview`='%s', `ch_views`=`ch_views`+1 WHERE `usr_id`='%s' LIMIT 1;", date('Y-m-d'), $upage_id));
            }
        } elseif ($for != 'channel') {
            if (!isset($_SESSION[$type . "_views"])) {
                $_SESSION[$type . "_views"]   = array();
                $_SESSION[$type . "_views"][] = $file_key;
                $do_db                        = true;
            } elseif (isset($_SESSION[$type . "_views"]) and !in_array($file_key, $_SESSION[$type . "_views"])) {
                $_SESSION[$type . "_views"][] = $file_key;
                $do_db                        = true;
            }

            if ($do_db) {
                self::$db->execute(sprintf("UPDATE `db_%sfiles` SET `last_viewdate`='%s', `file_views`=`file_views`+1 WHERE `file_key`='%s' LIMIT 1;", $type, date('Y-m-d'), $file_key));
            }
        }

        return;
    }

    /* some error messages */
    public function errorMessage($type, $msg)
    {
        return str_replace('##TYPE##', self::$language["frontend.global." . $type[0]], $msg);
    }

    /* current file query */
    public function currentSQL($type, $vid)
    {
        if (self::$section == self::$href['shorts']) {
            $type       = 'short';
            self::$type = $type;
            $sql        = sprintf("SELECT `file_key` FROM `db_%sfiles` WHERE `active`='1' AND `approved`='1' AND `deleted`='0' AND `privacy`='public' ORDER BY `db_id` DESC LIMIT 1", $type);
            $rs         = self::$db->execute($sql);

            if ($rs->field['file_key']) {
                header('Location: ' . self::$cfg['main_url'] . '/' . VHref::getKey("shorts") . '/' . $rs->field['file_key']);
                exit;
            }
        }
        return $vsql = sprintf("SELECT
                    D.`usr_id`, D.`file_title`, D.`file_description`, D.`file_tags`, D.`old_file_key`, D.`has_preview`, D.`thumb_cache`,
                    B.`usr_user`, B.`usr_key`, B.`usr_joindate`, B.`usr_partner`, B.`usr_affiliate`, B.`affiliate_badge`,
                    B.`usr_dname`, B.`ch_title`, B.`usr_profileinc`, B.`usr_followcount`, B.`usr_subcount`,
                    D.`db_id`, D.`comments`, D.`rating`, D.`responding`, D.`embedding`, D.`social`, D.`privacy`, D.`approved`, D.`deleted`, D.`active`,
                    D.`stream_chat`, D.`stream_live`, D.`stream_ended`, D.`stream_key`, D.`file_views`, D.`upload_date`, D.`file_like`, D.`file_dislike`, D.`file_type`, D.`upload_server`, D.`thumb_server`, %s
                    E.`ct_name`, E.`ct_id`, E.`ct_slug`, E.`ct_lang`
                    FROM
                    `db_accountuser` B, `db_%sfiles` D, `db_categories` E
                    WHERE
                    D.`file_key`='%s' AND
                    D.`file_category`=E.`ct_id` AND
                    D.`usr_id`=B.`usr_id`
                    LIMIT 1;", (($type[0] == 'v' or $type[0] == 's') ? "D.`embed_src`, D.`embed_key`, D.`embed_url`," : null), $type, $vid
        );
    }

    /* related files query */
    public static function relatedSQL($type, $vid, $rel, $more = false)
    {
        $limit     = $type == 'short' ? 12 : 15;
        $limit_sql = $limit;
        $do_more   = false;

        if ($more and isset($_GET['do']) and $_GET['do'] != 'side-column') {
            $do_more   = str_replace('recommend-', '', self::$filter->clr_str($_GET['do']));
            $limit_sql = $limit . ", " . $limit;
        }

        if (($do_more and $_POST['t'] !== "suggested") or (isset($_POST['t']) and $_POST['t'] !== "suggested")) {
            $sort_types = ['user', 'category', 'recent', 'watched'];
            $sort_type  = $do_more ?: self::$filter->clr_str($_POST['t']);
            $file_type  = $type ?: self::$filter->clr_str($_POST['f']);
            $file_key   = (int) $_POST['k'];
            $user_id    = (int) $_POST['u'];
            $categ_id   = (int) $_POST['c'];

            if (!in_array($sort_type, $sort_types)) {
                return;
            }

            switch ($sort_type) {
                case "user":
                    return $rsql = "SELECT
                        D.`usr_id`, D.`file_key`, D.`file_title`, D.`thumb_preview`,  D.`thumb_cache`,
                        B.`usr_user`, B.`usr_key`, B.`usr_dname`, B.`ch_title`,
                        D.`privacy`,
                        D.`file_views`, D.`file_like`, D.`file_comments`, D.`file_duration`, D.`thumb_server`, D.`upload_date`, D.`embed_src`
                        FROM `db_accountuser` B, `db_" . $file_type . "files` D
                        WHERE
                        D.`file_key`!='" . $file_key . "' AND
                        D.`usr_id`='" . $user_id . "' AND
                        " . ($file_type == 'live' ? "D.`stream_ended`='1' AND " : null) . "
                        D.`active`='1' AND
                        D.`approved`='1' AND
                        D.`deleted`='0' AND
                        D.`privacy`='public' AND
                        D.`usr_id`=B.`usr_id`
                        ORDER BY `db_id` DESC
                        LIMIT " . $limit_sql . ";";
                    break;

                case "category":
                    return $rsql = "SELECT
                        D.`usr_id`, D.`file_key`, D.`file_title`, D.`thumb_preview`,  D.`thumb_cache`,
                        B.`usr_user`, B.`usr_key`, B.`usr_dname`, B.`ch_title`,
                        D.`privacy`,
                        D.`file_views`, D.`file_like`, D.`file_comments`, D.`file_duration`, D.`thumb_server`, D.`upload_date`, D.`embed_src`
                        FROM `db_accountuser` B, `db_" . $file_type . "files` D
                        WHERE
                        D.`file_key`!='" . $file_key . "' AND
                        D.`file_category`='" . $categ_id . "' AND
                        " . ($file_type == 'live' ? "D.`stream_ended`='1' AND " : null) . "
                        D.`active`='1' AND
                        D.`approved`='1' AND
                        D.`deleted`='0' AND
                        D.`privacy`='public' AND
                        D.`usr_id`=B.`usr_id`
                        ORDER BY `db_id` DESC
                        LIMIT " . $limit_sql . ";";
                    break;

                case "recent":
                    return $rsql = "SELECT
                        D.`usr_id`, D.`file_key`, D.`file_title`, D.`thumb_preview`,  D.`thumb_cache`,
                        B.`usr_user`, B.`usr_key`, B.`usr_dname`, B.`ch_title`,
                        D.`privacy`,
                        D.`file_views`, D.`file_like`, D.`file_comments`, D.`file_duration`, D.`thumb_server`, D.`upload_date`, D.`embed_src`
                        FROM `db_accountuser` B, `db_" . $file_type . "files` D
                        WHERE
                        D.`file_key`!='" . $file_key . "' AND
                        " . ($file_type == 'live' ? "D.`stream_ended`='1' AND " : null) . "
                        D.`active`='1' AND
                        D.`approved`='1' AND
                        D.`deleted`='0' AND
                        D.`privacy`='public' AND
                        D.`usr_id`=B.`usr_id`
                        ORDER BY `db_id` DESC
                        LIMIT " . $limit_sql . ";";
                    break;

                case "watched":
                    if (!isset($_SESSION['USER_ID'])) {
                        return;
                    }

                    return $rsql = "SELECT
                        A.`db_id`,
                        D.`usr_id`, D.`file_key`, D.`file_title`, D.`thumb_preview`, D.`thumb_cache`,
                        B.`usr_user`, B.`usr_key`, B.`usr_dname`, B.`ch_title`,
                        D.`privacy`,
                        D.`file_views`, D.`file_like`, D.`file_comments`, D.`file_duration`, D.`thumb_server`, D.`upload_date`, D.`embed_src`
                        FROM `db_" . $file_type . "history` A, `db_accountuser` B, `db_" . $file_type . "files` D
                        WHERE
                        A.`file_key`=D.`file_key` AND
                        A.`usr_id`='" . (int) $_SESSION['USER_ID'] . "' AND
                        D.`file_key`!='" . $file_key . "' AND
                        " . ($file_type == 'live' ? "D.`stream_ended`='1' AND " : null) . "
                        D.`active`='1' AND
                        D.`approved`='1' AND
                        D.`deleted`='0' AND
                        D.`privacy`='public' AND
                        B.`usr_id`=D.`usr_id`
                        ORDER BY A.`db_id` DESC
                        LIMIT " . $limit_sql . ";";
                    break;
            }
        } else {
            return $rsql = "SELECT
                D.`usr_id`, D.`file_key`, D.`file_title`, D.`thumb_preview`, D.`thumb_cache`,
                B.`usr_user`, B.`usr_key`, B.`usr_dname`, B.`ch_title`,
                D.`privacy`,
                D.`file_views`, D.`file_like`, D.`file_comments`, D.`file_duration`, D.`thumb_server`, D.`upload_date`, D.`embed_src`,
                MATCH(`file_title`)
                AGAINST('" . $rel . "') as Relevance
                FROM `db_accountuser` B, `db_" . $type . "files` D
                WHERE MATCH(`file_title`)
                AGAINST('" . $rel . "' IN BOOLEAN MODE) AND
                D.`file_key`!='" . $vid . "' AND
                " . ($type == 'live' ? "D.`stream_ended`='1' AND " : null) . "
                D.`active`='1' AND
                D.`approved`='1' AND
                D.`deleted`='0' AND
                D.`privacy`='public' AND
                D.`usr_id`=B.`usr_id`
                ORDER BY `Relevance` DESC
                LIMIT " . $limit_sql . ";";
        }
    }

    /* file information */
    public function getFileInfo()
    {
        global $smarty;

        $class_filter = self::$filter;
        $db           = self::$db;
        $cfg          = self::$cfg;
        $type         = self::$type;
        $key          = self::$file_key;

        $pl_key = isset($_GET['p']) ? $class_filter->clr_str($_GET['p']) : false;
        $next   = $pl_key ? self::getPlaylistNext($type, $key, $pl_key) : null;

        $guest_chk = VHref::guestPermissions('guest_view_' . $type, VGenerate::fileHref($type[0], $key));

        $sql = sprintf("SELECT
                        A.`file_hd`, A.`stream_live`, A.`file_responses`, %s
                        B.`usr_id`, B.`usr_key`, A.`file_title`, A.`file_category`, A.`thumb_cache`
                        FROM
                        `db_%sfiles` A, `db_accountuser` B
                        WHERE
                        A.`file_key`='%s' AND
                        A.`usr_id`=B.`usr_id`
                        LIMIT 1;", (($type[0] == 'v' or $type[0] == 'l' or $type[0] == 's') ? "A.`embed_src`, A.`embed_key`," : null), $type, $key);

        $res         = self::$db_cache ? $db->CacheExecute($cfg['cache_view_template_file_info'], $sql) : $db->execute($sql);
        $thumb_cache = $res->fields['thumb_cache'];
        $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;

        if (($type == 'audio' and $cfg['audio_player'] == 'vjs') or (($type == 'video' or $type == 'live' or $type == 'short') and $cfg['video_player'] == 'vjs')) {
            $sql  = "SELECT `db_config` FROM `db_fileplayers` WHERE `db_name`='vjs_local' LIMIT 1;";
            $vjs  = self::$db_cache ? $db->CacheExecute($cfg['cache_view_template_file_info'], $sql) : $db->execute($sql);
            $_vjs = unserialize($vjs->fields['db_config']);

            $subbed = false;
            $vuid   = $res->fields['usr_id'];

            if ($vuid > 0) {
                if ($vuid == (int) $_SESSION['USER_ID']) {
                    $subbed = true;
                } else {
                    $ss = self::$db->execute(sprintf("SELECT `db_id`, `sub_list` FROM `db_subscriptions` WHERE `usr_id`='%s' LIMIT 1;", (int) $_SESSION['USER_ID']));
                    if ($ss->fields['db_id']) {
                        $subs = unserialize($ss->fields['sub_list']);
                        if (in_array($vuid, $subs)) {
                            $sq = sprintf("SELECT `db_id` FROM `db_subusers` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `expire_time`>='%s' LIMIT 1;", (int) $_SESSION['USER_ID'], $vuid, date("Y-m-d H:i:s"));
                            $sb = self::$db->execute($sq);
                            if ($sb->fields['db_id']) {
                                $subbed = true;
                            }

                        }
                    }

                    if (!$subbed) {
                        $ts = self::$db->execute(sprintf("SELECT `db_id` FROM `db_subtemps` WHERE `usr_id`='%s' AND `usr_id_to`='%s' AND `pk_id`>'0' AND `expire_time`>='%s' AND `active`='1' LIMIT 1;", (int) $_SESSION['USER_ID'], $vuid, date("Y-m-d H:i:s")));

                        if ($ts->fields['db_id']) {
                            $subbed = true;
                        }
                    }
                }
            }
            if ($_vjs['vjs_advertising'] == 1 and ($subbed or $type == 'short')) {
                $_vjs['vjs_advertising'] = 0;
            }

            $smarty->assign('vjs_advertising', $_vjs['vjs_advertising']);
            $mob = VHref::isMobile();
            if ($_vjs['vjs_advertising'] == 1) {
                $t   = $db->execute(sprintf("SELECT `vjs_ads` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, $key));
                $ads = $t->fields['vjs_ads'];
                if ($ads != '') {
//found ads assigned to video
                    $ar  = unserialize($ads);
                    $sql = sprintf("SELECT `ad_id`, `ad_key`, `ad_client`, `ad_tag`, `ad_skip`, `ad_comp_div`, `ad_comp_id`, `ad_comp_w`, `ad_comp_h` FROM `db_vjsadentries` WHERE `ad_type`='dedicated' AND `ad_active`='1'%s AND `ad_id` IN (%s) ORDER BY RAND() LIMIT 1;", ($mob ? " AND `ad_mobile`='1'" : null), ($ar[0] > 0 ? implode(',', $ar) : 0));
                    $t   = $db->execute($sql);
                } else {
                    //check for category ads
                    $t   = $db->execute(sprintf("SELECT `ct_ads` FROM `db_categories` WHERE `ct_id`='%s' LIMIT 1;", $res->fields['file_category']));
                    $ads = $t->fields['ct_ads'];
                    if ($ads != '') {
//found ads assigned to category
                        $ar  = unserialize($ads);
                        $sql = sprintf("SELECT `ad_id`, `ad_key`, `ad_client`, `ad_tag`, `ad_skip`, `ad_comp_div`, `ad_comp_id`, `ad_comp_w`, `ad_comp_h` FROM `db_vjsadentries` WHERE `ad_type`='dedicated' AND `ad_active`='1'%s AND `ad_id` IN (%s) ORDER BY RAND() LIMIT 1;", ($mob ? " AND `ad_mobile`='1'" : null), ($ar[0] > 0 ? implode(',', $ar) : 0));
                        $t   = $db->execute($sql);
                    } else {
//no video ads assigned/generate a random ad
                        $sql = sprintf("SELECT `ad_id`, `ad_key`, `ad_client`, `ad_tag`, `ad_skip`, `ad_comp_div`, `ad_comp_id`, `ad_comp_w`, `ad_comp_h` FROM `db_vjsadentries` WHERE `ad_type`='shared' AND `ad_active`='1'%s ORDER BY RAND() LIMIT 1;", ($mob ? " AND `ad_mobile`='1'" : null));
                        $t   = $db->execute($sql);
                    }
                }
                if ($t->fields['ad_id']) {
                    $ac = $t->fields['ad_client'];
                    $lv = $res->fields['stream_live'];
                    if ($mob and $lv and $ac == 'ima') {
                        $ac = 'custom';
                    }

                    $smarty->assign('ad_client', ($ac == 'custom' ? 'vast' : $t->fields['ad_client']));
                    $smarty->assign('ad_tag_url', ($ac == 'custom' ? $cfg['main_url'] . '/' . VHref::getKey('vast') . '?t=vjs&v=' . $t->fields['ad_key'] : $t->fields['ad_tag']));
                    $smarty->assign('ad_skip', ($ac == 'custom' ? (!$t->fields['ad_skip'] ? 5 : $t->fields['ad_skip']) : false));
                    $smarty->assign('ad_tag_comp', $t->fields['ad_comp_div']);
                    $smarty->assign('ad_tag_comp_id', $t->fields['ad_comp_id']);
                    $smarty->assign('ad_tag_comp_w', $t->fields['ad_comp_w']);
                    $smarty->assign('ad_tag_comp_h', $t->fields['ad_comp_h']);
                    $smarty->assign('is_mobile', $mob);
                } else {
                    $_vjs['vjs_advertising'] = 0;
                    $smarty->assign('vjs_advertising', $_vjs['vjs_advertising']);
                }
            }
            if ($_vjs['vjs_logo_file'] != '') {
                $smarty->assign('logo_file', $_vjs['vjs_logo_file']);
                $smarty->assign('logo_href', $cfg['main_url'] . '/' . VGenerate::fileHref($type[0], $key, $res->fields['file_title']));
            }
        }

        if ($res->fields['usr_key']) {
            $hd        = $res->fields['file_hd'];
            $usr_key   = $res->fields['usr_key'];
            $responses = $res->fields['file_responses'];
            $embed_src = ($type[0] == 'v' or $type[0] == 'l' or $type[0] == 's') ? $res->fields['embed_src'] : null;
            $embed_key = ($type[0] == 'v' or $type[0] == 's') ? $res->fields['embed_key'] : null;

            $smarty->assign('hd', $hd);
            $smarty->assign('file_key', $key);
            $smarty->assign('pl_key', $pl_key);
            $smarty->assign('usr_key', $usr_key);
            $smarty->assign('next', $next);
            $smarty->assign('load_player', $cfg[$type . "_player"]);
            $smarty->assign('load_responses', $responses);
            $smarty->assign('embed_src', $embed_src);
            $smarty->assign('embed_key', $embed_key);
            $smarty->assign('file_title', $res->fields['file_title']);
            $smarty->assign('file_type', $type);
            $smarty->assign('media_files_url', VGenerate::fileURL($type, $key, 'thumb'));
            $smarty->assign('is_subbed', $subbed);
            $smarty->assign('is_live', $res->fields['stream_live']);
            $smarty->assign('vjs_advertising', $_vjs['vjs_advertising']);
            $smarty->assign('can_href', $cfg['main_url'] . '/' . VGenerate::fileHref($type[0], $key, $res->fields['file_title']));
            $smarty->assign('thumb_cache', $thumb_cache);
        }
    }

    /* get next in playlist (video) */
    public function getPlaylistNext($type, $key, $pl_key)
    {
        $db = self::$db;

        if ($pl_key != '') {
            $pl_sql = sprintf("SELECT `pl_files` FROM `db_%splaylists` WHERE `pl_key`='%s' LIMIT 1;", $type, $pl_key);
            $pl_res = $db->execute($pl_sql);

            if ($pl_res->fields['pl_files']) {
                $pl_arr = unserialize($pl_res->fields['pl_files']);

                if ($pl_arr[1] != '') {
                    foreach ($pl_arr as $k => $v) {
                        if ($v == $key) {
                            return $next = $pl_arr[$k + 1];
                        }
                    }
                    return null;
                }
            }
        }
    }

    /* download links */
    public function downloadLinks($type, $file_key, $usr_key)
    {
        $cfg            = self::$cfg;
        $class_database = self::$dbc;
        $language       = self::$language;

        $_file_src = $class_database->singleFieldValue('db_' . $type . 'files', 'file_name', 'file_key', $file_key);
        $_a        = $type[0] . '#' . $usr_key . strrev($file_key);
        $ss        = md5($cfg['global_salt_key'] . $file_key);

        switch ($type[0]) {
            case "v": //video download links
                $ht = '<button class="symbol-button"><i class="icon-video"></i>####</button>';

                if ($cfg['file_download_s1'] == 1) {
                    $_mp4_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.360p.mp4';
                    $_mp4_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.360p.mp4';
                    $_ht_dl   = is_file($_mp4_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '1', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.mp45'] . '</span>', $ht) . '</li>' : null;
                    $_mp4_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.360p.mp4';
                    $_mp4_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.360p.mp4';
                    $_ht_dl .= is_file($_mp4_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '1', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.mp45'] . '</span>', $ht) . '</li>' : null;

                    $_mp4_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.480p.mp4';
                    $_mp4_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.480p.mp4';
                    $_ht_dl .= is_file($_mp4_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '6', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.mp46'] . '</span>', $ht) . '</li>' : null;
                    $_mp4_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.480p.mp4';
                    $_mp4_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.480p.mp4';
                    $_ht_dl .= is_file($_mp4_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '6', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.mp46'] . '</span>', $ht) . '</li>' : null;
                }
                if ($cfg['file_download_s3'] == 1) {
                    $_mp4_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.720p.mp4';
                    $_mp4_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.720p.mp4';
                    $_ht_dl .= is_file($_mp4_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '2', $_a) . '" class="download-file left-padding15" rel="nofollow">' . $language['view.files.down.format.mp41'] . '</span>', $ht) . '</li>' : null;
                    $_mp4_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.720p.mp4';
                    $_mp4_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.720p.mp4';
                    $_ht_dl .= is_file($_mp4_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '2', $_a) . '" class="download-file left-padding15" rel="nofollow">' . $language['view.files.down.format.mp41'] . '</span>', $ht) . '</li>' : null;

                    $_mp4_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.1080p.mp4';
                    $_mp4_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.1080p.mp4';
                    $_ht_dl .= is_file($_mp4_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '5', $_a) . '" class="download-file left-padding15" rel="nofollow">' . $language['view.files.down.format.mp43'] . '</span>', $ht) . '</li>' : null;
                    $_mp4_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.1080p.mp4';
                    $_mp4_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.1080p.mp4';
                    $_ht_dl .= is_file($_mp4_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '5', $_a) . '" class="download-file left-padding15" rel="nofollow">' . $language['view.files.down.format.mp43'] . '</span>', $ht) . '</li>' : null;
                }
                if ($cfg['file_download_s4'] == 1) {
                    $_mob_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.mob.mp4';
                    $_mob_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.mob.mp4';
                    $_ht_dl .= is_file($_mob_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '3', $_a) . '" class="download-file left-padding15" rel="nofollow">' . $language['view.files.down.format.mp42'] . '</span>', $ht) . '</li>' : null;
                    $_mob_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.mob.mp4';
                    $_mob_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.mob.mp4';
                    $_ht_dl .= is_file($_mob_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '3', $_a) . '" class="download-file left-padding15" rel="nofollow">' . $language['view.files.down.format.mp42'] . '</span>', $ht) . '</li>' : null;
                }
                if ($cfg['file_download_s2'] == 1) {
                    $_src_dir = $cfg['upload_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $_file_src;
                    $_src_url = $cfg['upload_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $_file_src;
                    $_ht_dl .= is_file($_src_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '4', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.src'] . '</span>', $ht) . '</li>' : null;
                }
                break;

            case "l": //live download links
                $ht     = '<button class="symbol-button"><i class="icon-live"></i>####</button>';
                $_ht_dl = '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '4', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.src'] . '</span>', $ht) . '</li>';
                break;

            case "i": //image download links
                $ht = '<button class="symbol-button"><i class="icon-image"></i>####</button>';

                if ($cfg['file_download_s1'] == 1) {
                    $_jpg_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.jpg';
                    $_jpg_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.jpg';
                    $_ht_dl   = is_file($_jpg_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '1', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.jpg'] . '</span>', $ht) . '</li>' : null;
                    $_jpg_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.jpg';
                    $_jpg_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.jpg';
                    $_ht_dl   = is_file($_jpg_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '1', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.jpg'] . '</span>', $ht) . '</li>' : null;
                }
                if ($cfg['file_download_s2'] == 1) {
                    $_src_dir = $cfg['upload_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $_file_src;
                    $_src_url = $cfg['upload_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $_file_src;
                    $_ht_dl .= is_file($_src_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '4', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.src'] . '</span>', $ht) . '</li>' : null;
                }
                break;

            case "a": //audio download links
                $ht = '<button class="symbol-button"><i class="icon-headphones"></i>####</button>';

                if ($cfg['file_download_s1'] == 1) {
                    $_mp3_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.mp3';
                    $_mp3_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.mp3';
                    $_ht_dl   = is_file($_mp3_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '1', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.mp3'] . '</span>', $ht) . '</li>' : null;
                    $_mp3_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.mp3';
                    $_mp3_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.mp3';
                    $_ht_dl   = is_file($_mp3_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '1', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.mp3'] . '</span>', $ht) . '</li>' : null;
                }
                if ($cfg['file_download_s2'] == 1) {
                    $_src_dir = $cfg['upload_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $_file_src;
                    $_src_url = $cfg['upload_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $_file_src;
                    $_ht_dl .= is_file($_src_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '4', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.src'] . '</span>', $ht) . '</li>' : null;
                }
                break;

            case "d": //document download links
                $ht = '<button class="symbol-button"><i class="icon-file"></i>####</button>';

                if ($cfg['file_download_s1'] == 1) {
                    $_pdf_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.pdf';
                    $_pdf_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '.pdf';
                    $_ht_dl   = is_file($_pdf_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '1', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.pdf'] . '</span>', $ht) . '</li>' : null;
                    $_pdf_dir = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.pdf';
                    $_pdf_url = $cfg['media_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $ss . '.pdf';
                    $_ht_dl   = is_file($_pdf_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '1', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.pdf'] . '</span>', $ht) . '</li>' : null;
                }
                if ($cfg['file_download_s2'] == 1) {
                    $_src_dir = $cfg['upload_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $_file_src;
                    $_src_url = $cfg['upload_files_url'] . '/' . $usr_key . '/' . $type[0] . '/' . $_file_src;
                    $_ht_dl .= is_file($_src_dir) ? '<li>' . str_replace('####', '<span rel-href="' . str_replace('#', '4', $_a) . '" class="download-file" rel="nofollow">' . $language['view.files.down.format.src'] . '</span>', $ht) . '</li>' : null;
                }
                break;
        }
        $html = VGenerate::simpleDivWrap('download-buttons', 'download-types', '<ul class="download-text">' . $language['view.files.dl.text'] . '</ul><ul>' . $_ht_dl . '</ul>');

        return $html;
    }

    /* submit flag requests */
    public function fileFlagging(int $rid)
    {
        $class_database = self::$dbc;
        $cfg            = self::$cfg;
        $language       = self::$language;
        $db             = self::$db;
        $type           = self::$type;
        $key            = self::$file_key;

        if (!isset($_SESSION['reported']) or !in_array($key, $_SESSION['reported'])) {
            $_SESSION['reported'][]   = $key;
            $_SESSION['reason'][$key] = $rid;

            $mail_do = VNotify::queInit('file_flagging', array($cfg['backend_email']), $rid);
            $res     = $db->execute(sprintf("UPDATE `db_%sfiles` SET `file_flag`=`file_flag`+1 WHERE `file_key`='%s' LIMIT 1;", $type, $key));

            echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
        } else {
            echo VGenerate::noticeTpl('', $language['view.files.is.reported'], '');
        }
    }

    public static function ra($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string)) {
            return $string;
        }

        if (seems_utf8($string)) {
            $chars = array(
                // Decompositions for Latin-1 Supplement.
                'ª' => 'a',
                'º' => 'o',
                'À' => 'A',
                'Á' => 'A',
                'Â' => 'A',
                'Ã' => 'A',
                'Ä' => 'A',
                'Å' => 'A',
                'Æ' => 'AE',
                'Ç' => 'C',
                'È' => 'E',
                'É' => 'E',
                'Ê' => 'E',
                'Ë' => 'E',
                'Ì' => 'I',
                'Í' => 'I',
                'Î' => 'I',
                'Ï' => 'I',
                'Ð' => 'D',
                'Ñ' => 'N',
                'Ò' => 'O',
                'Ó' => 'O',
                'Ô' => 'O',
                'Õ' => 'O',
                'Ö' => 'O',
                'Ù' => 'U',
                'Ú' => 'U',
                'Û' => 'U',
                'Ü' => 'U',
                'Ý' => 'Y',
                'Þ' => 'TH',
                'ß' => 's',
                'à' => 'a',
                'á' => 'a',
                'â' => 'a',
                'ã' => 'a',
                'ä' => 'a',
                'å' => 'a',
                'æ' => 'ae',
                'ç' => 'c',
                'è' => 'e',
                'é' => 'e',
                'ê' => 'e',
                'ë' => 'e',
                'ì' => 'i',
                'í' => 'i',
                'î' => 'i',
                'ï' => 'i',
                'ð' => 'd',
                'ñ' => 'n',
                'ò' => 'o',
                'ó' => 'o',
                'ô' => 'o',
                'õ' => 'o',
                'ö' => 'o',
                'ø' => 'o',
                'ù' => 'u',
                'ú' => 'u',
                'û' => 'u',
                'ü' => 'u',
                'ý' => 'y',
                'þ' => 'th',
                'ÿ' => 'y',
                'Ø' => 'O',
                // Decompositions for Latin Extended-A.
                'Ā' => 'A',
                'ā' => 'a',
                'Ă' => 'A',
                'ă' => 'a',
                'Ą' => 'A',
                'ą' => 'a',
                'Ć' => 'C',
                'ć' => 'c',
                'Ĉ' => 'C',
                'ĉ' => 'c',
                'Ċ' => 'C',
                'ċ' => 'c',
                'Č' => 'C',
                'č' => 'c',
                'Ď' => 'D',
                'ď' => 'd',
                'Đ' => 'D',
                'đ' => 'd',
                'Ē' => 'E',
                'ē' => 'e',
                'Ĕ' => 'E',
                'ĕ' => 'e',
                'Ė' => 'E',
                'ė' => 'e',
                'Ę' => 'E',
                'ę' => 'e',
                'Ě' => 'E',
                'ě' => 'e',
                'Ĝ' => 'G',
                'ĝ' => 'g',
                'Ğ' => 'G',
                'ğ' => 'g',
                'Ġ' => 'G',
                'ġ' => 'g',
                'Ģ' => 'G',
                'ģ' => 'g',
                'Ĥ' => 'H',
                'ĥ' => 'h',
                'Ħ' => 'H',
                'ħ' => 'h',
                'Ĩ' => 'I',
                'ĩ' => 'i',
                'Ī' => 'I',
                'ī' => 'i',
                'Ĭ' => 'I',
                'ĭ' => 'i',
                'Į' => 'I',
                'į' => 'i',
                'İ' => 'I',
                'ı' => 'i',
                'Ĳ' => 'IJ',
                'ĳ' => 'ij',
                'Ĵ' => 'J',
                'ĵ' => 'j',
                'Ķ' => 'K',
                'ķ' => 'k',
                'ĸ' => 'k',
                'Ĺ' => 'L',
                'ĺ' => 'l',
                'Ļ' => 'L',
                'ļ' => 'l',
                'Ľ' => 'L',
                'ľ' => 'l',
                'Ŀ' => 'L',
                'ŀ' => 'l',
                'Ł' => 'L',
                'ł' => 'l',
                'Ń' => 'N',
                'ń' => 'n',
                'Ņ' => 'N',
                'ņ' => 'n',
                'Ň' => 'N',
                'ň' => 'n',
                'ŉ' => 'n',
                'Ŋ' => 'N',
                'ŋ' => 'n',
                'Ō' => 'O',
                'ō' => 'o',
                'Ŏ' => 'O',
                'ŏ' => 'o',
                'Ő' => 'O',
                'ő' => 'o',
                'Œ' => 'OE',
                'œ' => 'oe',
                'Ŕ' => 'R',
                'ŕ' => 'r',
                'Ŗ' => 'R',
                'ŗ' => 'r',
                'Ř' => 'R',
                'ř' => 'r',
                'Ś' => 'S',
                'ś' => 's',
                'Ŝ' => 'S',
                'ŝ' => 's',
                'Ş' => 'S',
                'ş' => 's',
                'Š' => 'S',
                'š' => 's',
                'Ţ' => 'T',
                'ţ' => 't',
                'Ť' => 'T',
                'ť' => 't',
                'Ŧ' => 'T',
                'ŧ' => 't',
                'Ũ' => 'U',
                'ũ' => 'u',
                'Ū' => 'U',
                'ū' => 'u',
                'Ŭ' => 'U',
                'ŭ' => 'u',
                'Ů' => 'U',
                'ů' => 'u',
                'Ű' => 'U',
                'ű' => 'u',
                'Ų' => 'U',
                'ų' => 'u',
                'Ŵ' => 'W',
                'ŵ' => 'w',
                'Ŷ' => 'Y',
                'ŷ' => 'y',
                'Ÿ' => 'Y',
                'Ź' => 'Z',
                'ź' => 'z',
                'Ż' => 'Z',
                'ż' => 'z',
                'Ž' => 'Z',
                'ž' => 'z',
                'ſ' => 's',
                // Decompositions for Latin Extended-B.
                'Ș' => 'S',
                'ș' => 's',
                'Ț' => 'T',
                'ț' => 't',
                // Euro sign.
                '€' => 'E',
                // GBP (Pound) sign.
                '£' => '',
                // Vowels with diacritic (Vietnamese).
                // Unmarked.
                'Ơ' => 'O',
                'ơ' => 'o',
                'Ư' => 'U',
                'ư' => 'u',
                // Grave accent.
                'Ầ' => 'A',
                'ầ' => 'a',
                'Ằ' => 'A',
                'ằ' => 'a',
                'Ề' => 'E',
                'ề' => 'e',
                'Ồ' => 'O',
                'ồ' => 'o',
                'Ờ' => 'O',
                'ờ' => 'o',
                'Ừ' => 'U',
                'ừ' => 'u',
                'Ỳ' => 'Y',
                'ỳ' => 'y',
                // Hook.
                'Ả' => 'A',
                'ả' => 'a',
                'Ẩ' => 'A',
                'ẩ' => 'a',
                'Ẳ' => 'A',
                'ẳ' => 'a',
                'Ẻ' => 'E',
                'ẻ' => 'e',
                'Ể' => 'E',
                'ể' => 'e',
                'Ỉ' => 'I',
                'ỉ' => 'i',
                'Ỏ' => 'O',
                'ỏ' => 'o',
                'Ổ' => 'O',
                'ổ' => 'o',
                'Ở' => 'O',
                'ở' => 'o',
                'Ủ' => 'U',
                'ủ' => 'u',
                'Ử' => 'U',
                'ử' => 'u',
                'Ỷ' => 'Y',
                'ỷ' => 'y',
                // Tilde.
                'Ẫ' => 'A',
                'ẫ' => 'a',
                'Ẵ' => 'A',
                'ẵ' => 'a',
                'Ẽ' => 'E',
                'ẽ' => 'e',
                'Ễ' => 'E',
                'ễ' => 'e',
                'Ỗ' => 'O',
                'ỗ' => 'o',
                'Ỡ' => 'O',
                'ỡ' => 'o',
                'Ữ' => 'U',
                'ữ' => 'u',
                'Ỹ' => 'Y',
                'ỹ' => 'y',
                // Acute accent.
                'Ấ' => 'A',
                'ấ' => 'a',
                'Ắ' => 'A',
                'ắ' => 'a',
                'Ế' => 'E',
                'ế' => 'e',
                'Ố' => 'O',
                'ố' => 'o',
                'Ớ' => 'O',
                'ớ' => 'o',
                'Ứ' => 'U',
                'ứ' => 'u',
                // Dot below.
                'Ạ' => 'A',
                'ạ' => 'a',
                'Ậ' => 'A',
                'ậ' => 'a',
                'Ặ' => 'A',
                'ặ' => 'a',
                'Ẹ' => 'E',
                'ẹ' => 'e',
                'Ệ' => 'E',
                'ệ' => 'e',
                'Ị' => 'I',
                'ị' => 'i',
                'Ọ' => 'O',
                'ọ' => 'o',
                'Ộ' => 'O',
                'ộ' => 'o',
                'Ợ' => 'O',
                'ợ' => 'o',
                'Ụ' => 'U',
                'ụ' => 'u',
                'Ự' => 'U',
                'ự' => 'u',
                'Ỵ' => 'Y',
                'ỵ' => 'y',
                // Vowels with diacritic (Chinese, Hanyu Pinyin).
                'ɑ' => 'a',
                // Macron.
                'Ǖ' => 'U',
                'ǖ' => 'u',
                // Acute accent.
                'Ǘ' => 'U',
                'ǘ' => 'u',
                // Caron.
                'Ǎ' => 'A',
                'ǎ' => 'a',
                'Ǐ' => 'I',
                'ǐ' => 'i',
                'Ǒ' => 'O',
                'ǒ' => 'o',
                'Ǔ' => 'U',
                'ǔ' => 'u',
                'Ǚ' => 'U',
                'ǚ' => 'u',
                // Grave accent.
                'Ǜ' => 'U',
                'ǜ' => 'u',
            );

            // Used for locale-specific rules.
            $locale = get_locale();

            if (in_array($locale, array('de_DE', 'de_DE_formal', 'de_CH', 'de_CH_informal', 'de_AT'), true)) {
                $chars['Ä'] = 'Ae';
                $chars['ä'] = 'ae';
                $chars['Ö'] = 'Oe';
                $chars['ö'] = 'oe';
                $chars['Ü'] = 'Ue';
                $chars['ü'] = 'ue';
                $chars['ß'] = 'ss';
            } elseif ('da_DK' === $locale) {
                $chars['Æ'] = 'Ae';
                $chars['æ'] = 'ae';
                $chars['Ø'] = 'Oe';
                $chars['ø'] = 'oe';
                $chars['Å'] = 'Aa';
                $chars['å'] = 'aa';
            } elseif ('ca' === $locale) {
                $chars['l·l'] = 'll';
            } elseif ('sr_RS' === $locale || 'bs_BA' === $locale) {
                $chars['Đ'] = 'DJ';
                $chars['đ'] = 'dj';
            }

            $string = strtr($string, $chars);
        } else {
            $chars = array();
            // Assume ISO-8859-1 if not UTF-8.
            $chars['in'] = "\x80\x83\x8a\x8e\x9a\x9e"
                . "\x9f\xa2\xa5\xb5\xc0\xc1\xc2"
                . "\xc3\xc4\xc5\xc7\xc8\xc9\xca"
                . "\xcb\xcc\xcd\xce\xcf\xd1\xd2"
                . "\xd3\xd4\xd5\xd6\xd8\xd9\xda"
                . "\xdb\xdc\xdd\xe0\xe1\xe2\xe3"
                . "\xe4\xe5\xe7\xe8\xe9\xea\xeb"
                . "\xec\xed\xee\xef\xf1\xf2\xf3"
                . "\xf4\xf5\xf6\xf8\xf9\xfa\xfb"
                . "\xfc\xfd\xff";

            $chars['out'] = 'EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy';

            $string              = strtr($string, $chars['in'], $chars['out']);
            $double_chars        = array();
            $double_chars['in']  = array("\x8c", "\x9c", "\xc6", "\xd0", "\xde", "\xdf", "\xe6", "\xf0", "\xfe");
            $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string              = str_replace($double_chars['in'], $double_chars['out'], $string);
        }

        return $string;
    }
}

