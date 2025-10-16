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

class VUseraccount
{
    /* own drectory sizes */
    public function getUsedSpace($m)
    {
        global $cfg;

        $u_key = $_SESSION['USER_KEY'];
        $nd    = $cfg['numeric_delimiter'];
        $p1    = $cfg['media_files_dir'] . '/' . $u_key . '/' . $m[0] . '/';
        $p2    = $cfg['media_files_dir'] . '/' . $u_key . '/t/';

        $s1 = VFileinfo::getDirectorySize($p1);
        $s2 = VFileinfo::getDirectorySize($p2);

        return number_format(($s1['size'] + $s2['size']) / (1024 * 1024), 2, $nd, $nd);
    }
    /* loop info array */
    public function infoArray($info_array, $uid = '')
    {
        global $language;

        foreach ($info_array as $key => $val) {
            $html .= '<div class="left-float wdmax bottom-border-dotted lh20 uinfo-entry"><span class="grayText left-float">' . $key . ' ' . (($key == $language['account.overview.sub.bw.limit'] and $uid > 0) ? '<a href="javascript:;" class="sub-reset">(reset)</a>' : null) . '</span><span class="right-float">' . $val . '</span></div>';
        }
        return $html;
    }
    /* account statistics */
    public function getUserStats($type = 'stats')
    {
        global $cfg, $language, $db, $class_database;

        $mod = array("video", "short", "live", "image", "audio", "document", "blog");

        $sql = sprintf("SELECT
                         A.`usr_id`, A.`usr_affiliate`, A.`usr_partner`, A.`usr_sub_email`, A.`affiliate_badge`, A.`partner_date`, A.`usr_joindate`,
                         B.`pk_price`, B.`pk_name`,
                         C.`pk_id`
                         FROM
                         `db_accountuser` AS A
                         LEFT JOIN `db_packusers` AS C ON (A.`usr_id`=C.`usr_id`)
                         LEFT JOIN `db_packtypes` AS B ON (B.`pk_id`=C.`pk_id`)
                         WHERE
                         A.`usr_id`='%s' LIMIT 1;", (int) $_SESSION['USER_ID']);

        $info = $db->execute($sql);

        $join       = $type != 'subs' ? $info->fields['usr_joindate'] : $info->fields['partner_date'];
        $is_partner = $info->fields['usr_partner'];

        $is_affiliate = $info->fields['usr_affiliate'];
        $pkname       = $info->fields['pk_price'] == 0 ? $language['account.overview.account.free'] : $info->fields['pk_name'];
        $join_date    = date('F j, o, H:i A', strtotime($join));

        if ($type == 'subs' and !$is_partner) {
            $join_date = 'n/a';
        }

        /* channel statistics */
        $html_ch = null;
        if ($cfg['public_channels'] == 1) {
            $html_ch .= self::channelCountStats();
        }
        /* subscription statistics */
        $s        = 0;
        $html_sub = null;
        $html_sub .= $html_ch;

        if ($type != 'subs') {
            foreach ($mod as $m) {
                $html_sub .= $cfg[$m . "_module"] == 1 ? self::fileCountStats(($m == 'document' ? 'doc' : $m), $s) : null;
                $s += 1;
            }
        }

        $at = array();
        if ($is_affiliate) {
            $at[] = $language['account.overview.account.affiliate'];
        }

        if ($is_partner) {
            $at[] = $language['account.overview.account.partner'];
        }

        $sub_html = '
                <div class="account-stats">
                    <div class="vs-column half sc">
                        <h3 class="content-title">
                            <span class="label">' . ($type != 'subs' ? $language['account.overview.setup.date'] : $language['account.overview.prt.setup.date']) . '</span>' . $join_date . '
                        </h3>
                        <div class="line"></div>
                        <h3>' . $language['account.overview.account.type'] . '</h3>
                        <h2>' . (isset($at[0]) ? implode(' & ', $at) : $language['account.overview.account.member']) . ($_SESSION['USER_BADGE'] != '' ? ' <i id="affiliate-icon" class="' . $_SESSION['USER_BADGE'] . '"></i>' : null) . '</h2>
                    ' . ($cfg['paid_memberships'] == 1 ? '<br>
                        <h3>' . $language['account.overview.membership.type'] . '</h3>
                        <h2>' . strtoupper($pkname) . '</h2>
                        <br>
                    ' : null) . '
                    </div>
                    <div class="vs-column half fit sc">
                    ' . ($type != 'subs' ? self::subscriptionStats() : ($is_partner ? self::setPaymentEmail($info) : null)) . '
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="clearfix"></div>
            ';

        if ($type == 'subs' and $is_partner) {
            $sub_html .= '</div><div>';

            $html_sub = self::subsCountStats();
        }

        $stats_html = ' <div class="vs-column full">
                    <article>
                        <h3 class="content-title"><i class="iconBe-' . ($type == 'subs' ? 'coin' : 'pie') . '"></i> ' . ($type == 'subs' ? $language['account.overview.subs.stats'] : $language['account.overview.stats']) . '</h3>
                        <div class="line"></div>
                    </article>
                    <div class="">
                        ' . $html_sub . '
                    </div>
                </div>
                <div class="clearfix"></div>
    ';

        if ($type == 'stats') {
            return $stats_html;
        } else if ($type == 'subs') {
            return $sub_html . $stats_html;
        } else {
            return $sub_html;
        }
    }
    /* partner payment email */
    private static function setPaymentEmail($info)
    {
        global $language;

        $sub_email = $info->fields['usr_sub_email'];
        $af_badge  = $info->fields['affiliate_badge'];
        $badges    = array('icon-check', 'icon-user', 'icon-coin', 'icon-thumbs-up', 'icon-paypal');

        $badge_ht = '<select class="badge-select-input" name="user_partner_badge">';
        $badge_ht .= '<option value=""' . ($af_badge == '' ? ' selected="selected"' : null) . '>' . $language['frontend.global.none'] . '</option>';
        foreach ($badges as $badge) {
            $badge_ht .= '<option value="' . $badge . '"' . ($badge == $af_badge ? ' selected="selected"' : null) . '><i class="' . $badge . '"></i> ' . $badge . '</option>';
        }
        $badge_ht .= '</select>';

        $html = '
            <form id="ct-set-form">
                <div id="affiliate-response"></div>
                <div class="clearfix"></div>
                <div class="vs-column full">
                    <div class="row">
                        <div class="left-float lh25 wd140"><label>' . $language['account.overview.partner.badge'] . '</label></div>
                        <div class="left-float selector">
                            ' . $badge_ht . '
                        </div>
                    </div>
                </div>
                <div class="vs-column full">
                    <div class="row">
                        <div class="left-float lh25 wd140"><label>' . $language['account.overview.paypal.email'] . '</label></div>
                        <div class="left-float"><input type="text" name="user_partner_paypal" class="user-partner-paypal" autocomplete="off" value="' . $sub_email . '" onclick="this.focus(); this.select();"></div>
                    </div>
                </div>
            </form>
        ';
        return $html;
    }
    /* channel count statistics */
    public function channelCountStats()
    {
        global $db, $class_database, $language, $cfg;

        $uid = (int) $_SESSION['USER_ID'];
        $s1  = $db->execute(sprintf("SELECT `ch_type`, `ch_views` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $uid));

        $ch_type    = $class_database->singleFieldValue('db_categories', 'ct_name', 'ct_id', $s1->fields['ch_type']);
        $ch_slug    = $class_database->singleFieldValue('db_categories', 'ct_slug', 'ct_id', $s1->fields['ch_type']);
        $ch_views   = VFiles::numFormat($s1->fields['ch_views']);
        $ch_subs    = VFiles::numFormat(VUserpage::getSubCount($uid));
        $ch_follows = VFiles::numFormat(VUserpage::getFollowCount($uid));

        $info_array = array($language['account.overview.chan.type'] => ($ch_type != '' ? $ch_type : '-'),
            $language['account.overview.chan.view']                     => $ch_views,
            $language['account.overview.chan.subs']                     => $ch_subs,
            $language['account.overview.chan.follows']                  => $ch_follows,
        );
        if ($cfg['channel_views'] == 0) {
            unset($info_array[$language['account.overview.chan.view']]);
        }

        if ($cfg['user_subscriptions'] == 0) {
            unset($info_array[$language['account.overview.chan.subs']]);
        }

        $m    = 'ch';
        $html = '
                <div class="">
                    <ul class="' . $m . ' l vs-column thirds"><li class="l1"><i class="icon-user"></i></li><li class="l2">' . $language['account.overview.chan.follows'] . '</li><li class="l3"><i class="icon-upload"></i> ' . $ch_follows . '</li></ul>
                    <ul class="' . $m . ' l vs-column thirds"><li class="l1"><i class="icon-user"></i></li><li class="l2">' . $language['account.overview.chan.subs'] . '</li><li class="l3"><i class="icon-heart"></i> ' . $ch_subs . '</li></ul>
                    <ul class="' . $m . ' l vs-column thirds fit"><li class="l1"><i class="icon-user"></i></li><li class="l2">' . $language['account.overview.chan.view'] . '</li><li class="l3"><i class="icon-eye"></i> ' . $ch_views . '</li></ul>
                </div>
                <div class="clearfix"></div>
    ';

        return $html;
    }
    /* subscription details */
    public function subscriptionStats($p = 0, $uid = '', $be = false)
    {
        global $db, $language, $cfg;

        if ($cfg['paid_memberships'] == 0) {
            return false;
        }

        $nd  = $cfg['numeric_delimiter'];
        $nd  = $nd == '' ? '.' : $nd;
        $sql = sprintf("SELECT A.`pk_id`, A.`pk_usedspace`, A.`pk_usedbw`, A.`pk_total_live`, A.`pk_total_video`, A.`pk_total_short`, A.`pk_total_image`, A.`pk_total_audio`, A.`pk_total_doc`, A.`pk_total_blog`, A.`subscribe_time`, A.`expire_time`, A.`pk_paid`, A.`pk_paid_total`,
                        B.`pk_name`, B.`pk_descr`, B.`pk_space`, B.`pk_bw`, B.`pk_price`, B.`pk_priceunit`, B.`pk_llimit`, B.`pk_alimit`, B.`pk_ilimit`, B.`pk_vlimit`, B.`pk_slimit`, B.`pk_dlimit`, B.`pk_blimit`, B.`pk_period`
                        FROM `db_packusers` A, `db_packtypes` B WHERE A.`usr_id`='%s' AND A.`pk_id`=B.`pk_id` AND B.`pk_active`='1';", ($uid == '' ? intval($_SESSION['USER_ID']) : $uid));

        $rs             = $db->execute($sql);
        $pk_id          = $rs->fields['pk_id'];
        $pk_name        = $rs->fields['pk_name'];
        $pk_expire      = $rs->fields['expire_time'];
        $pk_total_live  = $rs->fields['pk_total_live'];
        $pk_llimit      = $rs->fields['pk_llimit'];
        $pk_llimit      = $pk_llimit == 0 ? '&#8734;' : $pk_llimit;
        $pk_total_video = $rs->fields['pk_total_video'];
        $pk_vlimit      = $rs->fields['pk_vlimit'];
        $pk_vlimit      = $pk_vlimit == 0 ? '&#8734;' : $pk_vlimit;
        $pk_total_short = $rs->fields['pk_total_short'];
        $pk_slimit      = $rs->fields['pk_slimit'];
        $pk_slimit      = $pk_slimit == 0 ? '&#8734;' : $pk_slimit;
        $pk_total_image = $rs->fields['pk_total_image'];
        $pk_ilimit      = $rs->fields['pk_ilimit'];
        $pk_ilimit      = $pk_ilimit == 0 ? '&#8734;' : $pk_ilimit;
        $pk_total_audio = $rs->fields['pk_total_audio'];
        $pk_alimit      = $rs->fields['pk_alimit'];
        $pk_alimit      = $pk_alimit == 0 ? '&#8734;' : $pk_alimit;
        $pk_total_doc   = $rs->fields['pk_total_doc'];
        $pk_dlimit      = $rs->fields['pk_dlimit'];
        $pk_dlimit      = $pk_dlimit == 0 ? '&#8734;' : $pk_dlimit;
        $pk_total_blog  = $rs->fields['pk_total_blog'];
        $pk_blimit      = $rs->fields['pk_blimit'];
        $pk_blimit      = $pk_blimit == 0 ? '&#8734;' : $pk_blimit;
        $pk_price       = $rs->fields['pk_price'];
        $pk_priceunit   = $rs->fields['pk_priceunit'];
        $pk_space       = $rs->fields['pk_space'];
        $pk_space       = $pk_space == 0 ? '&#8734;' : $pk_space;
        $pk_usedspace   = self::numberFormat(array("size" => $rs->fields['pk_usedspace']), 1);
        $pk_bw          = $rs->fields['pk_bw'];
        $pk_bw          = $pk_bw == 0 ? '&#8734;' : $pk_bw;
        $pk_usedbw      = self::numberFormat(array("size" => $rs->fields['pk_usedbw']), 1);
        $pk_paid        = $rs->fields['pk_paid'];
        $pk_paid_total  = $rs->fields['pk_paid_total'];
        $pk_name .= ' / ' . $pk_priceunit . $pk_price . '';

        $mod        = array("live", "video", "short", "image", "audio", "document", "blog");
        $info_array = array($language['account.overview.sub.name'] => ($uid == '' ? $pk_name : VbeMembers::subscriptionList()),
            $language['account.overview.sub.expire']                   => ($uid == '' ? date('F j, o, H:i A', strtotime($pk_expire)) : '<input type="text" name="sub_expire" id="sub-expire" class="sub-expire text-input right-align" value="' . $pk_expire . '" style="margin-top: 5px;">'),
            $language['account.overview.sub.paid']                     => $pk_priceunit . $pk_paid,
            $language['account.overview.sub.paid.total']               => $pk_priceunit . $pk_paid_total,
            $language['account.overview.sub.l.limit']                  => '<span id="pk_used_llimit">' . $pk_total_live . '</span> / <span id="pk_llimit">' . $pk_llimit . '</span>',
            $language['account.overview.sub.v.limit']                  => '<span id="pk_used_vlimit">' . $pk_total_video . '</span> / <span id="pk_vlimit">' . $pk_vlimit . '</span>',
            $language['account.overview.sub.s.limit']                  => '<span id="pk_used_slimit">' . $pk_total_short . '</span> / <span id="pk_slimit">' . $pk_slimit . '</span>',
            $language['account.overview.sub.i.limit']                  => '<span id="pk_used_ilimit">' . $pk_total_image . '</span> / <span id="pk_ilimit">' . $pk_ilimit . '</span>',
            $language['account.overview.sub.a.limit']                  => '<span id="pk_used_alimit">' . $pk_total_audio . '</span> / <span id="pk_alimit">' . $pk_alimit . '</span>',
            $language['account.overview.sub.d.limit']                  => '<span id="pk_used_dlimit">' . $pk_total_doc . '</span> / <span id="pk_dlimit">' . $pk_dlimit . '</span>',
            $language['account.overview.sub.b.limit']                  => '<span id="pk_used_blimit">' . $pk_total_blog . '</span> / <span id="pk_blimit">' . $pk_blimit . '</span>',
            $language['account.overview.sub.space.limit']              => $pk_usedspace . ' / ' . $pk_space . $language['frontend.sizeformat.mb'],
            $language['account.overview.sub.bw.limit']                 => $pk_usedbw . ' / ' . $pk_bw . $language['frontend.sizeformat.mb'],
            '&nbsp;'                                                   => '<a class="font11" href="' . $cfg['main_url'] . '/' . VHref::getKey("renew") . '?t=' . md5($_SESSION['USER_NAME'] . $_SESSION['USER_ID']) . '">' . $language['account.overview.sub.change'] . '</a>',
        );
        if ($uid == '') {
            unset($info_array[$language['account.overview.sub.paid.total']]);
        } else {
            unset($info_array['&nbsp;']);
        }
        /* on upload page, remove some fields from subscription stats */
        if ($p == 1) {
            unset($info_array[$language['account.overview.sub.paid']]);
        }
        foreach ($mod as $m) {
            if ($cfg[$m . "_module"] == 0) {
                unset($info_array[$language["account.overview.sub." . $m[0] . ".limit"]]);
            }
            $s += 1;
        }

        $v0  = (($pk_total_live / (is_numeric($pk_llimit) ? $pk_llimit : 1)) * 100);
        $v0  = $v0 > 100 ? 100 : $v0;
        $v1  = (($pk_total_video / (is_numeric($pk_vlimit) ? $pk_vlimit : 1)) * 100);
        $v1  = $v1 > 100 ? 100 : $v1;
        $v1a = (($pk_total_short / (is_numeric($pk_slimit) ? $pk_slimit : 1)) * 100);
        $v1a = $v1a > 100 ? 100 : $v1a;
        $v2  = (($pk_total_image / (is_numeric($pk_ilimit) ? $pk_ilimit : 1)) * 100);
        $v2  = $v2 > 100 ? 100 : $v2;
        $v3  = (($pk_total_audio / (is_numeric($pk_alimit) ? $pk_alimit : 1)) * 100);
        $v3  = $v3 > 100 ? 100 : $v3;
        $v4  = (($pk_total_doc / (is_numeric($pk_dlimit) ? $pk_dlimit : 1)) * 100);
        $v4  = $v4 > 100 ? 100 : $v4;
        $v4a = (($pk_total_blog / (is_numeric($pk_blimit) ? $pk_blimit : 1)) * 100);
        $v4a = $v4a > 100 ? 100 : $v4a;

        $_s           = explode(' ', $pk_usedspace);
        $pk_usedspace = ($_s[1] == 'kB') ? round(($pk_usedspace / 1024), 2) : $pk_usedspace;
        $v5           = (($pk_usedspace / (is_numeric($pk_space) ? $pk_space : 1)) * 100);
        $v5           = $v5 > 100 ? 100 : $v5;

        $_s        = explode(' ', $pk_usedbw);
        $pk_usedbw = ($_s[1] == 'kB') ? round(($pk_usedbw / 1024), 2) : $pk_usedbw;
        $v6        = (($pk_usedbw / (is_numeric($pk_bw) ? $pk_bw : 1)) * 100);
        $v6        = $v6 > 100 ? 100 : $v6;

        $html = '
                    <div class="account-sub vs-column full">
                        <div>' . ($uid != '' ? '<span class="label account-date">' . $language['backend.menu.members.mem.type'] . '</span>' : null) . $info_array[$language['account.overview.sub.name']] . '</div>
                        <p class="account-date"><span class="label">' . $language['account.overview.sub.expire'] . '</span> ' . $info_array[$language['account.overview.sub.expire']] . '</p>
                        ' . (!$be ? '<div class=""><button name="renew-button" class="sub-renew save-entry-button button-grey search-button form-button" type="button" style="margin-left:0;margin-top:10px" onclick=\'window.location="' . $cfg['main_url'] . '/' . VHref::getKey("renew") . '?t=' . md5($_SESSION['USER_NAME'] . $_SESSION['USER_ID']) . '"\'><span>' . $language['account.overview.sub.change'] . '</span></button></div>' : null) . '
                    </div>
                    <div class="clearfix"></div>
                    <div class="vs-column thirds">' . ($cfg['live_module'] == 1 ? '<div class="pl"><i class="icon-live"></i> ' . $language['account.overview.sub.l.limit'] . '</div><div class="uProgress"><div class="uBar" style="width: ' . ($pk_llimit > 0 ? (int) $v0 : 0) . '%;"></div><div class="uLabel">' . $pk_total_live . ' / ' . $pk_llimit . '</div></div>' : null) . '</div>
                    <div class="vs-column thirds">' . ($cfg['video_module'] == 1 ? '<div class="pl"><i class="icon-video"></i> ' . $language['account.overview.sub.v.limit'] . '</div><div class="uProgress"><div class="uBar" style="width: ' . ($pk_vlimit > 0 ? (int) $v1 : 0) . '%;"></div><div class="uLabel">' . $pk_total_video . ' / ' . $pk_vlimit . '</div></div>' : null) . '</div>
                    <div class="vs-column thirds fit">' . ($cfg['short_module'] == 1 ? '<div class="pl"><i class="icon-mobile"></i> ' . $language['account.overview.sub.s.limit'] . '</div><div class="uProgress"><div class="uBar" style="width: ' . ($pk_slimit > 0 ? (int) $v1a : 0) . '%;"></div><div class="uLabel">' . $pk_total_short . ' / ' . $pk_slimit . '</div></div>' : null) . '</div>
                    <div class="vs-column half">' . ($cfg['image_module'] == 1 ? '<div class="pl"><i class="icon-image"></i> ' . $language['account.overview.sub.i.limit'] . '</div><div class="uProgress"><div class="uBar" style="width: ' . ($pk_ilimit > 0 ? (int) $v2 : 0) . '%;"></div><div class="uLabel">' . $pk_total_image . ' / ' . $pk_ilimit . '</div></div>' : null) . '</div>
                    <div class="vs-column half fit">' . ($cfg['audio_module'] == 1 ? '<div class="pl"><i class="icon-headphones"></i> ' . $language['account.overview.sub.a.limit'] . '</div><div class="uProgress"><div class="uBar" style="width: ' . ($pk_alimit > 0 ? (int) $v3 : 0) . '%;"></div><div class="uLabel">' . $pk_total_audio . ' / ' . $pk_alimit . '</div></div>' : null) . '</div>
                    <div class="vs-column half">' . ($cfg['document_module'] == 1 ? '<div class="pl"><i class="icon-file"></i> ' . $language['account.overview.sub.d.limit'] . '</div><div class="uProgress"><div class="uBar" style="width: ' . ($pk_dlimit > 0 ? (int) $v4 : 0) . '%;"></div><div class="uLabel">' . $pk_total_doc . ' / ' . $pk_dlimit . '</div></div>' : null) . '</div>
                    <div class="vs-column half fit">' . ($cfg['blog_module'] == 1 ? '<div class="pl"><i class="icon-blog"></i> ' . $language['account.overview.sub.b.limit'] . '</div><div class="uProgress"><div class="uBar" style="width: ' . ($pk_blimit > 0 ? (int) $v4a : 0) . '%;"></div><div class="uLabel">' . $pk_total_blog . ' / ' . $pk_blimit . '</div></div>' : null) . '</div>
                    <div class="vs-column half"><div class="pl"><i class="iconBe-pie"></i> ' . $language['account.overview.sub.space.limit'] . '</div><div class="uProgress"><div class="uBar" style="width: ' . ($pk_space > 0 ? (int) $v5 : 0) . '%;"></div><div class="uLabel">' . $pk_usedspace . ' / ' . $pk_space . $language['frontend.sizeformat.mb'] . '</div></div></div>
                    <div class="vs-column half fit"><div class="pl"><i class="icon-upload"></i> ' . $language['account.overview.sub.bw.limit'] . '</div><div class="uProgress"><div class="uBar" style="width: ' . ($pk_bw > 0 ? (int) $v6 : 0) . '%;"></div><div class="uLabel">' . $pk_usedbw . ' / ' . $pk_bw . $language['frontend.sizeformat.mb'] . '</div></div></div>
                    <div class="clearfix"></div>
    ';

        return $html;
    }
    /* check upload/view permissions */
    public function checkPerm($type, $t, $cache_time = false)
    {
        global $class_database, $language, $cfg;

        $p = unserialize($class_database->singleFieldValue('db_accountuser', 'usr_perm', 'usr_id', intval($_SESSION['USER_ID']), $cache_time));

        if ($type == 'upload') {
            switch ($t) {
                case "l":$name = 'live';
                    break;
                case "v":$name = 'video';
                    break;
                case "s":$name = 'short';
                    break;
                case "i":$name = 'image';
                    break;
                case "a":$name = 'audio';
                    break;
                case "d":$name = 'document';
                    break;
                case "b":$name = 'blog';
                    break;
            }
        }
        if (!isset($p["perm_" . $type . "_s"])) {
            $p["perm_" . $type . "_s"] = 1;
        }

        return ($p["perm_" . $type . "_" . $t] == 0 ? $language['upload.err.msg.14'] : (($type == 'upload' and $cfg[$name . "_uploads"] == 0) ? $language['upload.err.msg.14'] : null));
    }
    /* subscribers count statistics */
    public function subsCountStats($m = 'subs')
    {
        global $language, $db, $cfg;

        $uid      = (int) $_SESSION['USER_ID'];
        $currency = $cfg['subscription_payout_currency'];

        $ui = $db->execute(sprintf("SELECT `usr_sub_share`, `usr_sub_perc`, `usr_sub_currency` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $uid));
        if ($ui->fields['usr_sub_share'] == 1) {
            $currency = $ui->fields['usr_sub_currency'];
        }

        $sql = sprintf("SELECT SUM(`pk_paid_share`) AS `current_balance`, COUNT(`pk_paid_share`) AS `current_total` FROM `db_subpayouts` WHERE `usr_id_to`='%s' AND `is_paid`='0';", $uid);
        $rs  = $db->execute($sql);
        $s1  = round($rs->fields['current_balance'], 2);
        $s11 = round($rs->fields['current_total'], 2);

        $sql = sprintf("SELECT SUM(B.`sub_amount`) AS `total_balance`, COUNT(B.`db_id`) AS `total_payouts` FROM `db_subinvoices` B WHERE B.`usr_id`='%s' AND B.`sub_paid`='1';", $uid);
        $rs  = $db->execute($sql);
        $s2  = round($rs->fields['total_balance'], 2);
        $s3  = round($rs->fields['total_payouts'], 2);

        $sql = sprintf("SELECT COUNT(`db_id`) AS `paid_subs_total` FROM `db_subpayouts` WHERE `usr_id_to`='%s' AND `is_paid`='1';", $uid);
        $rs  = $db->execute($sql);
        $s22 = round($rs->fields['paid_subs_total'], 2);

        $html = '
                <div class="">
                    <ul class="' . $m . ' l vs-column thirds"><li class="l1"><i class="icon-coin"></i></li><li class="l2">' . $language['account.overview.subs.c.balance'] . '</li><li class="l3">' . $s1 . ' ' . $currency . ' / ' . $s11 . ' ' . $language['subnav.entry.sub'] . '</li></ul>
                    <ul class="' . $m . ' l vs-column thirds"><li class="l1"><i class="icon-coin"></i></li><li class="l2">' . $language['account.overview.subs.t.balance'] . '</li><li class="l3">' . $s2 . ' ' . $currency . ' / ' . $s22 . ' ' . $language['subnav.entry.sub'] . '</li></ul>
                    <ul class="' . $m . ' l vs-column thirds fit"><li class="l1"><i class="icon-coin"></i></li><li class="l2">' . $language['account.overview.subs.p.balance'] . '</li><li class="l3">' . $s3 . '</li></ul>
                </div>
                <div class="clearfix"></div>
    ';

        return $html;
    }
    /* file count statistics */
    public function fileCountStats($m, $s)
    {
        global $language, $db;

        $sql    = sprintf("SELECT SUM(A.`file_views`) AS `total`, A.`file_key` FROM `db_%sfiles` A WHERE A.`usr_id`='%s'", $m, intval($_SESSION['USER_ID']));
        $rs     = $db->execute($sql);
        $t      = $rs->fields['total'];
        $_pk    = $rs->fields['pk_id'];
        $_count = VFiles::numFormat(VFiles::fileCount('file-menu-entry1', $m));
        $_views = VFiles::numFormat($t);
        $_fav   = VFiles::numFormat(VFiles::fileCount('file-menu-entry2', $m));

        switch ($m) {
            case "live":$l = 'live';
                break;
            case "video":$l = 'vid';
                break;
            case "short":$l = 'short';
                break;
            case "image":$l = 'img';
                break;
            case "audio":$l = 'aud';
                break;
            case "doc":$l = 'doc';
                break;
            case "blog":$l = 'blog';
                break;
        }

        $html = '
                <div class="">
                    <ul class="' . $m . ' l vs-column thirds"><li class="l1"><i class="icon-' . ($m == 'doc' ? 'file' : ($m == 'short' ? 'mobile' : $m)) . '"></i></li><li class="l2">' . $language["account.overview." . $l . ".up"] . '</li><li class="l3"><i class="icon-upload"></i> ' . $_count . '</li></ul>
                    <ul class="' . $m . ' l vs-column thirds"><li class="l1"><i class="icon-' . ($m == 'doc' ? 'file' : ($m == 'short' ? 'mobile' : $m)) . '"></i></li><li class="l2">' . $language["account.overview." . $l . ".fav"] . '</li><li class="l3"><i class="icon-heart"></i> ' . $_fav . '</li></ul>
                    <ul class="' . $m . ' l vs-column thirds fit"><li class="l1"><i class="icon-' . ($m == 'doc' ? 'file' : ($m == 'short' ? 'mobile' : $m)) . '"></i></li><li class="l2">' . $language["account.overview." . $l . ".view"] . '</li><li class="l3"><i class="icon-eye"></i> ' . $_views . '</li></ul>
                </div>
                <div class="clearfix"></div>
    ';

        return $html;
    }
    /* number format */
    public function numberFormat($size, $binfo = '')
    {
        global $cfg, $language;
        $dlm = $cfg['numeric_delimiter'] == '' ? ',' : $cfg['numeric_delimiter'];

        if ($size['size'] < 1024) {return number_format($size['size'], 0, $dlm, $dlm) . ($binfo == 1 ? $language['frontend.sizeformat.bytes'] : null);} elseif ($size['size'] < (1024 * 1024)) {return number_format(($size['size'] / (1024)), 2, $dlm, $dlm) . ($binfo == 1 ? $language['frontend.sizeformat.kb'] : null);} elseif ($size['size'] < (1024 * 1024 * 1024)) {return number_format(($size['size'] / (1024 * 1024)), 2, $dlm, $dlm) . ($binfo == 1 ? $language['frontend.sizeformat.mb'] : null);} else { $size2 = round($size['size'] / (1024 * 1024 * 1024), 1);return number_format(($size['size'] / (1024 * 1024 * 1024)), 2, $dlm, $dlm) . ($binfo == 1 ? $language['frontend.sizeformat.gb'] : null);}
    }
    /* to make various checkboxes selected or not */
    public function entryCheckboxes($act_type, $db_tbl = 'db_trackactivity')
    {
        global $class_database, $cfg;

        if ($db_tbl == 'db_settings') {
            $check = $cfg[$act_type];
        } else {
            $check = $class_database->singleFieldValue($db_tbl, $act_type, 'usr_id', intval($_SESSION['USER_ID']));
        }
        return $checkbox = $check == 1 ? 'checked="checked"' : null;
    }
    /* to make profile email notification checkboxes selected or not */
    public function notificationCheckboxes($be = '')
    {
        global $db, $cfg;

        if ($be == 1) {
            return $checked = ($cfg['backend_notification_signup'] == 0 and $cfg['backend_notification_upload'] == 0 and $cfg['backend_notification_payment'] == 0) ? 0 : 1;
        } else {
            $q              = $db->execute(sprintf("SELECT `usr_mail_filecomment`,`usr_mail_chancomment`,`usr_mail_privmessage`,`usr_mail_friendinv`,`usr_mail_chansub` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 5;", intval($_SESSION['USER_ID'])));
            return $checked = ($q->fields['usr_mail_filecomment'] == 0 and $q->fields['usr_mail_chancomment'] == 0 and $q->fields['usr_mail_privmessage'] == 0 and $q->fields['usr_mail_friendinv'] == 0 and $q->fields['usr_mail_chansub'] == 0) ? 0 : 1;
        }
    }
    /* to make checkboxes disabled if no notifications are set */
    public function disabledCheckboxes($be = '')
    {
        $disabled      = self::notificationCheckboxes($be) == 0 ? 'disabled="disabled"' : null;
        echo $span_css = $disabled != '' ? VGenerate::declareJS('$("span.en-chk-txt").addClass("grayText");') : null;
        return $disabled;
    }
    /* changing email validation and notification */
    public function changeEmailCheck()
    {
        global $cfg, $class_database, $language;

        $email_check = new VValidation;
        $hasher      = new VPasswordHash(8, false);
        $u_fields    = VArrayConfig::cfgSection();
        $u_info      = VUserinfo::getUserInfo($_SESSION['USER_ID']);
        $u_hash      = $u_info['pass'];
        $u_pass      = $u_fields['usr_password'];
        $siteKey     = $cfg['recaptcha_site_key'];
        $secret      = $cfg['recaptcha_secret_key'];

        $error_message = null;
        $error_message = (!$email_check->checkEmailAddress($u_fields['usr_email'])) ? $language['frontend.signup.email.invalid'] : $error_message;
        $error_message = ($cfg['signup_domain_restriction'] == 1 and $error_message == '' and !VIPaccess::emailDomainCheck($u_fields['usr_email'])) ? $language['notif.error.existing.email'] : $error_message;
        $error_message = ($error_message == '' and $hasher->CheckPassword($u_pass, $u_hash) != 1) ? $language['account.error.email.pass'] : $error_message;
        $error_message = ($error_message == '' and VUserinfo::existingEmail($u_fields['usr_email'])) ? $language['account.error.existing.email'] : $error_message;

        if ($error_message == '') {
            if ($cfg['email_change_captcha'] == 1 and $cfg['account_email_verification'] == 1) {
                if ($u_fields['usr_captcha'] == '') {
                    $error_message = $language['notif.error.invalid.request'];
                } else {
                    $recaptcha = new \ReCaptcha\ReCaptcha($secret);
                    $resp      = $recaptcha->verify($u_fields['usr_captcha'], $_SERVER[REM_ADDR]);

                    if ($resp->isSuccess()) {
                    } else {
                        foreach ($resp->getErrorCodes() as $code) {
                            $error_message = $code;
                        }
                    }
                }
            }

            if ($error_message == '') {
                $db_update = $class_database->entryUpdate('db_accountuser', array("usr_verified" => 0, "usr_email" => $u_fields['usr_email']));

                $mail_do = VNotify::queInit('change_email', array($u_fields['usr_email']), '');
            }
        }

        return $error_message;
    }
    /* change email page */
    public function changeEmail()
    {
        global $language, $smarty;

        $u_check       = VArraySection::getArray('change_email');
        $error_message = VForm::checkEmptyFields($u_check[1], $u_check[2]);
        $error_message = $error_message == '' ? self::changeEmailCheck() : $error_message;
        if ($error_message == '') {
            return VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', '', $language['account.notice.email.update'])));
        } else {return VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $error_message, '')));}
    }
    /* delete account, notify admin */
    public function purgeAccount()
    {
        global $class_database, $class_redirect, $language, $smarty, $cfg;

        $u_check       = VArraySection::getArray('purge_account');
        $error_message = VForm::checkEmptyFields($u_check[1], $u_check[2]);
        if ($error_message == '') {
            $hasher        = new VPasswordHash(8, false);
            $u_info        = VUserinfo::getUserInfo($_SESSION['USER_ID']);
            $u_hash        = $u_info['pass'];
            $u_fields      = VArrayConfig::cfgSection();
            $u_pass        = $u_fields['usr_delpass'];
            $pass_chk      = $hasher->CheckPassword($u_pass, $u_hash);
            $error_message = $pass_chk != 1 ? $language['account.error.invalid.pass'] : null;
            if ($error_message == '') {
                /* send mail to admin */
                $mail_do = VNotify::queInit('account_removal', array($cfg['backend_email']), '');
                /* delete from db here */
                $class_database->entryUpdate('db_accountuser', array("usr_deleted" => 1, "usr_active" => 0, "usr_verified" => 0, "usr_status" => 0, "usr_del_reason" => $u_fields['usr_del_reason']));
                /* logout */
                echo VGenerate::declareJS('window.location = "' . $cfg['main_url'] . '/' . VHref::getKey('signout') . '";');
            } else {VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $error_message, '')));}
        } else {VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $error_message, '')));}
    }
    /* changing password */
    public function changePassword()
    {
        global $class_database, $class_filter, $language, $smarty, $cfg, $db;

        $u_check       = VArraySection::getArray('change_password');
        $error_message = VForm::checkEmptyFields($u_check[1], $u_check[2]);

        $ui = $db->execute(sprintf("SELECT `oauth_password` FROM `db_accountuser` WHERE `usr_id`='%s' AND `oauth_uid` > '0' LIMIT 1;", (int) $_SESSION['USER_ID']));
        $up = $ui->fields['oauth_password'];

        if ($error_message == '' and $up == 0) {
            $hasher     = new VPasswordHash(8, false);
            $u_info     = VUserinfo::getUserInfo((int) $_SESSION['USER_ID']);
            $u_hash     = $u_info['pass'];
            $u_fields   = VArrayConfig::cfgSection();
            $u_new_pass = $u_fields['usr_newpass'];
            $u_retype   = $u_fields['usr_retypepass'];

            $enc_pass = $class_filter->clr_str($hasher->HashPassword($u_retype));

            $error_message = (strlen($u_new_pass) < $cfg['signup_min_password'] or strlen($u_new_pass) > $cfg['signup_max_password'] or strlen($u_retype) < $cfg['signup_min_password'] or strlen($u_retype) > $cfg['signup_max_password']) ? $language['notif.error.invalid.pass'] : $error_message;
            $error_message = md5($u_new_pass) != md5($u_retype) ? $language['account.error.retype.pass'] : $error_message;
            $update_passwd = ($error_message == '' and $class_database->entryUpdate('db_accountuser', array("usr_password" => $enc_pass))) ? 1 : 0;
            $show_notice   = ($error_message == '' and $update_passwd == 1) ? VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', '', $language['account.notice.pass.update']))) : VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $error_message, '')));
            $last_update   = ($error_message == '' and $update_passwd == 1) ? $db->execute(sprintf("UPDATE `db_accountuser` SET `oauth_password`='1' WHERE `usr_id`='%s' AND `oauth_uid` > '0' LIMIT 1;", (int) $_SESSION['USER_ID'])) : null;
        } elseif ($error_message == '' and $up == 1) {
            $hasher     = new VPasswordHash(8, false);
            $u_info     = VUserinfo::getUserInfo((int) $_SESSION['USER_ID']);
            $u_hash     = $u_info['pass'];
            $u_fields   = VArrayConfig::cfgSection();
            $u_old_pass = $u_fields['usr_oldpass'];
            $u_new_pass = $u_fields['usr_newpass'];
            $u_retype   = $u_fields['usr_retypepass'];

            $enc_pass = $class_filter->clr_str($hasher->HashPassword($u_retype));
            $oldp_chk = $hasher->CheckPassword($u_old_pass, $u_hash);

            $error_message = (strlen($u_new_pass) < $cfg['signup_min_password'] or strlen($u_new_pass) > $cfg['signup_max_password'] or strlen($u_retype) < $cfg['signup_min_password'] or strlen($u_retype) > $cfg['signup_max_password']) ? $language['notif.error.invalid.pass'] : $error_message;
            $error_message = ($error_message == '' and $oldp_chk != 1) ? $language['account.error.old.pass'] : (md5($u_new_pass) != md5($u_retype) ? $language['account.error.retype.pass'] : $error_message);
            $update_passwd = ($error_message == '' and $class_database->entryUpdate('db_accountuser', array("usr_password" => $enc_pass))) ? 1 : 0;
            $show_notice   = ($error_message == '' and $update_passwd == 1) ? VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', '', $language['account.notice.pass.update']))) : VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $error_message, '')));
        } else {VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $error_message, '')));}
    }
    /* check url format */
    public function checkURL($url)
    {
        if (strpos($url, "http://") === 0 || strpos($url, "https://") === 0) {
        } else {
            $url = 'https://' . $url;
        }

        if (preg_match("/^(http(s?):\/\/{1})((\w+\.){1,})\w{2,}(\/[^\s]*)?$/i", $url)) {return true;} else {return false;}
    }
    /* save profile section changes */
    public function doChanges()
    {
        global $db, $class_database, $class_filter, $language, $smarty;

        $_s                                        = isset($_GET['s']) ? $class_filter->clr_str($_GET['s']) : null;
        $_SESSION[$_SESSION['USER_KEY'] . '_list'] = (intval($_POST['keep_open']) == 1) ? 1 : 0;

        $error_message = ($_s == 'account-menu-entry2' and $_POST['account_profile_about_website'] != '' and !self::checkURL($_POST['account_profile_about_website'])) ? $language['account.error.invalid.url'] : null;
        $fields        = VArrayConfig::cfgSection();

        switch ($_s) {
            case "account-menu-entry4";
                $db_tbl = 'db_accountuser';
                $fields = VArraySection::arrayRemoveKey($fields, "usr_email", "usr_password", "usr_captcha");
                break;
            case "account-menu-entry5";
                $db_tbl = 'db_trackactivity';
                break;
            default:$db_tbl = 'db_accountuser';
                break;
        }
        if ($error_message == '' and $_s == 'account-menu-entry2') {
            $db->execute(sprintf("UPDATE `db_accountuser` SET `ch_dname`='%s' WHERE `usr_id`='%s' LIMIT 1;", $fields['usr_dname'], (int) $_SESSION['USER_ID']));
            if ($db->Affected_Rows() > 0) {
                $_SESSION['USER_DNAME'] = $fields['usr_dname'];
            }
        }

        if ($error_message == '' and $class_database->entryUpdate($db_tbl, $fields)) {
            VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', '', $language['notif.success.request'])));
        } elseif ($error_message != '') {
            VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $error_message, '')));
        }
        $smarty->assign('keep_entries_open', $_SESSION[$_SESSION['USER_KEY'] . '_list']);
        $opened_entry = VGenerate::keepEntryOpen();
    }
    /* get the user profile image */
    public function getProfileImage($usr_id = '', $rnd = true)
    {
        global $cfg, $class_database, $db;

        if ($usr_id == 0) {
            return $cfg['global_images_url'] . '/default-user.png';
        }

        $usr_id = ($usr_id != '' and $usr_id > 0) ? $usr_id : intval($_SESSION['USER_ID']);

        $uu            = $db->execute(sprintf("SELECT `usr_key`, `usr_photo`, `usr_profileinc` FROM `db_accountuser` WHERE `usr_id`='%s' LIMIT 1;", $usr_id));
        $u_info        = array();
        $u_info['key'] = $uu->fields['usr_key'];
        $u_info['inc'] = (int) $uu->fields['usr_profileinc'];
        $usr_photo     = $uu->fields['usr_photo'];

        switch ($usr_photo) {
            case "":
            case "default":
                return $cfg['profile_images_url'] . '/default.jpg';
                break;
            case "file":
                if (is_file($cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . $u_info['inc'] . '.jpg')) {
                    if (is_file($cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . '.jpg')) {
                        unlink($cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . '.jpg');
                    }
                    if (is_file($cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . ($u_info['inc'] - 1) . '.jpg')) {
                        unlink($cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . ($u_info['inc'] - 1) . '.jpg');
                    }
                    // symlink($cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . $u_info['inc'] . '.jpg', $cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . '.jpg');

                    return $cfg['profile_images_url'] . '/' . $u_info['key'] . '/' . $u_info['key'] . $u_info['inc'] . '.jpg';
                } else {
                    if (is_file($cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . '.jpg')) {
                        return $cfg['profile_images_url'] . '/' . $u_info['key'] . '/' . $u_info['key'] . '.jpg';
                    } else {
                        return $cfg['profile_images_url'] . '/default.jpg';
                    }
                }

        }
    }
    /* get the user profile image */
    public function getProfileImage_inc(int $usr_key, $usr_photo = '', int $inc)
    {
        global $cfg, $class_database, $db;

        if ($usr_key == 0) {
            return $cfg['profile_images_url'] . '/default.jpg';
        }

        $u_info        = array();
        $u_info['key'] = $usr_key;
        $u_info['inc'] = $inc;

        switch ($usr_photo) {
            case "":
            case "default":
                return $cfg['profile_images_url'] . '/default.jpg';
                break;
            case "file":
                if (is_file($cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . '.jpg')) {
                    return $cfg['profile_images_url'] . '/' . $u_info['key'] . '/' . $u_info['key'] . '.jpg';
                } elseif (is_file($cfg['profile_images_dir'] . '/' . $u_info['key'] . '/' . $u_info['key'] . $u_info['inc'] . '.jpg')) {
                    return $cfg['profile_images_url'] . '/' . $u_info['key'] . '/' . $u_info['key'] . $u_info['inc'] . '.jpg';
                } else {
                    return $cfg['profile_images_url'] . '/default.jpg';
                }

        }
    }
    /* canceling when changing profile image */
    public function cancelProfileImage()
    {
        global $cfg, $class_filter;

        if ($_POST) {
            $tmp_file = $class_filter->clr_str($_POST['profile_image_temp']);
            $tmp_path = $cfg['profile_images_dir'] . '/' . $tmp_file;
            if (is_file($tmp_path)) {@unlink($tmp_path);}
        }
    }
    /* update db/jq profile image */
    public function updateDBentry($from, $usr_id = '')
    {
        global $db, $cfg;
        $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_photo`='%s', `usr_profileinc`=`usr_profileinc`+1 WHERE `usr_id`='%s' LIMIT 1;", $from, intval($_SESSION['USER_ID'])));
        if ($db->Affected_Rows()) {
      		$_SESSION['USER_PINC'] += 1;
        }
        echo VGenerate::declareJS('$("#own-profile-image").replaceWith("<img id=\"own-profile-image\" title=\"' . $_SESSION['USER_NAME'] . '\" alt=\"' . $_SESSION['USER_NAME'] . '\" src=\"' . self::getProfileImage($_SESSION['USER_ID']) . '\" />"); $.fancybox.close();');
    }
    /* save when changing profile image */
    public function saveProfileImage($user_key = '')
    {
        global $cfg, $class_filter, $class_database;

        $user_key = $user_key == '' ? $class_filter->clr_str($_SESSION['USER_KEY']) : $user_key;

        if ($_POST) {
            $image_from = $class_filter->clr_str($_POST['profile_image_action']);
            $tmp_name   = $class_filter->clr_str($_POST['profile_image_temp']);
            $usr_inc    = (int) $class_database->singleFieldValue('db_accountuser', 'usr_profileinc', 'usr_key', $user_key);

            switch ($image_from) {
                case "new":
                    $link     = $cfg['profile_images_dir'] . '/' . $user_key . '/' . $user_key . '.jpg';
                    $tmp_file = $cfg['profile_images_dir'] . '/' . $user_key . '/' . $tmp_name;
                    $dst_file = $cfg['profile_images_dir'] . '/' . $user_key . '/' . str_replace($user_key, $user_key . $usr_inc + 1, substr($tmp_name, 4));

                    if (rename($tmp_file, $dst_file)) {
                        self::updateDBentry('file', $_SESSION['USER_ID']);
                    }
                    break;
                case "video":break;
                case "default":
                    self::updateDBentry('default');

                    $target = $cfg['profile_images_dir'] . '/' . $user_key . '/' . $user_key . ($usr_inc + 1) . '.jpg';
                    $link   = $cfg['profile_images_dir'] . '/' . $user_key . '/' . $user_key . '.jpg';

                    if (is_file($link)) {
                        unlink($link);
                    }
                    if (is_file($cfg['profile_images_dir'] . '/' . $user_key . '/' . $user_key . ($usr_inc) . '.jpg')) {
                        unlink($cfg['profile_images_dir'] . '/' . $user_key . '/' . $user_key . ($usr_inc) . '.jpg');
                    }

                    copy($cfg['profile_images_dir'] . '/default.jpg', $target);
                    break;
            }
        }
    }
    /* country select list */
    public function countryList()
    {
        include_once 'f_core/config.countries.php';
        $select = '<select name="account_profile_location_country_sel" class="select-input wd350 account-select" onChange="$(\'#input-loc\').val(this.value);">';
        $ct     = self::getProfileDetail('usr_country');

        foreach ($_countries as $value) {
            $selected = $ct == $value ? ' selected="selected"' : null;
            $option .= '<option value="' . $value . '"' . $selected . '>' . $value . '</option>';
        }
        $select .= $option . '</select>';
        $select .= '<input type="hidden" name="account_profile_location_country" class="login-input" id="input-loc" value="' . $ct . '" />';
        $select .= '<input type="hidden" name="account_profile_location_country_tmp" class="login-input no-display" value="' . $ct . '" />';
        return $select;
    }
    /* profile values */
    public function getProfileDetail($get_value, $uid = '')
    {
        global $class_database;
        $for = $uid == '' ? intval($_SESSION['USER_ID']) : $uid;
        return $class_database->singleFieldValue('db_accountuser', $get_value, 'usr_id', $for);
    }
    /* changing profile image, uploading */
    public function changeProfileImage($user_key = '')
    {
        global $cfg, $class_filter, $language;

        $user_key = $user_key == '' ? $class_filter->clr_str($_SESSION['USER_KEY']) : $user_key;

        echo '<span class="no-display">1</span>'; //the weirdest fix EVER, but jquery form plugin fails without it...

        $upload_file_name  = $class_filter->clr_str($_FILES['profile_image']['tmp_name']);
        $upload_file_size  = intval($_FILES['profile_image']['size']);
        $upload_file_limit = $cfg['user_image_max_size'] * 1024 * 1024;
        $upload_file_type  = strtoupper(VFileinfo::getExtension($_FILES['profile_image']['name']));
        $upload_allowed    = explode(',', strtoupper($cfg['user_image_allowed_extensions']));

        $error_message = $upload_file_size > $upload_file_limit ? $language['account.error.filesize'] : null;
        $error_message = ($error_message == '' and !in_array($upload_file_type, $upload_allowed)) ? $language['account.error.allowed'] : $error_message;
        if ($error_message == '') {
            if (strpos($upload_file_name, '.php') !== false or strpos($upload_file_name, '.pl') !== false or strpos($upload_file_name, '.asp') !== false or strpos($upload_file_name, '.htm') !== false or strpos($upload_file_name, '.cgi') !== false or strpos($upload_file_name, '.py') !== false or strpos($upload_file_name, '.sh') !== false or strpos($upload_file_name, '.cin') !== false) {
                $error_message = $language['account.error.allowed'];
            }
        }

        echo $show_error = $error_message != '' ? VGenerate::noticeTpl('', $error_message, '') : null;
        if ($error_message == '') {
            $tmp_file = $cfg['profile_images_dir'] . '/' . $user_key . '/tmp_' . $user_key . '.jpg';
            $tmp_img  = $cfg['profile_images_url'] . '/' . $user_key . '/tmp_' . $user_key . '.jpg';

            if (is_file($tmp_file)) {@unlink($tmp_file);}
            if (rename($upload_file_name, $tmp_file)) {
                if ($upload_file_type == 'JPG' or $upload_file_type == 'JPEG') {
                    self::image_fix_orientation($tmp_file);
                }

                $thumb = PhpThumbFactory::create($tmp_file);
                $thumb->adaptiveResize($cfg['user_image_width'], $cfg['user_image_height']);
                $thumb->save($cfg['profile_images_dir'] . '/' . $user_key . '/tmp_' . $user_key . '.jpg', 'jpg');
            }

            if (filesize($tmp_file) > 0) {
                $image_replace   = '<div class=\"row left-float left-padding25\"><input type=\"hidden\" name=\"profile_image_temp\" value=\"tmp_' . $user_key . '.jpg\" /><img class=\"profile-tmp\" src=\"' . $tmp_img . '?t=' . time() . '\" alt=\"' . $_SESSION['USER_NAME'] . '\" title=\"' . $_SESSION['USER_NAME'] . '\" /></div>';
                $input_replace   = '$("#overview-userinfo-file").replaceWith("' . $image_replace . '");';
                echo $do_replace = $error_message == '' ? VGenerate::declareJS($input_replace) : null;
            }
        }
    }
    /* fix image orientation */
    public static function image_fix_orientation($filename)
    {
        $exif = exif_read_data($filename);
        if (!empty($exif['Orientation'])) {
            $image = imagecreatefromjpeg($filename);
            switch ($exif['Orientation']) {
                case 3:
                    $image = imagerotate($image, 180, 0);
                    break;

                case 6:
                    $image = imagerotate($image, -90, 0);
                    break;

                case 8:
                    $image = imagerotate($image, 90, 0);
                    break;
            }

            imagejpeg($image, $filename, 90);
        }
    }
}
