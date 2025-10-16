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

class VbeFiles
{
    /* categories left menu navigation */
    public function fileCategories($type)
    {
        global $db, $language, $href, $class_database;

        $sql = sprintf("SELECT `ct_id`, `ct_name`, `ct_descr` FROM `db_categories` WHERE `ct_type`='%s' AND `ct_active`='1' AND `sub_id`='0' ORDER BY `ct_name`;", $type);
        $res = $db->execute($sql);

        $i = 0;
        if ($res->fields["ct_id"]) {
            $lis  = array();
            $rep  = array();
            $html = '<ul>';
            while (!$res->EOF) {
                $ct_id   = $res->fields["ct_id"];
                $ct_name = $res->fields["ct_name"];

                $html .= '#lis' . $ct_id . '#';
                $rep[$ct_id] = '#lis' . $ct_id . '#';
                $lis[$ct_id] = '<li><a href="javascript:;" id="backend-menu-entry11-sub' . $type[0] . $ct_id . '" class="#sub#menu-panel-entry-sub be-panel" rel-m="' . $href["be_files"] . '">' . $ct_name . '</a>';

                $sb = $db->execute(sprintf("SELECT `ct_id`, `sub_id`, `ct_name` FROM `db_categories` WHERE `ct_type`='%s' AND `ct_active`='1' AND `sub_id`='%s' ORDER BY `ct_name`;", $type, $ct_id));
                if ($sb->fields["ct_id"]) {
                    $html .= '<ul class="inner-menu2">';
                    while (!$sb->EOF) {
                        if ($sb->fields["sub_id"] == $ct_id) {
                            $lis[$ct_id] = str_replace('#sub#', 'sub_menu ', $lis[$ct_id]);
                        } else {
                        }
                        $ct_id   = $sb->fields["ct_id"];
                        $ct_name = $sb->fields["ct_name"];

                        $html .= '<li><a href="javascript:;" id="backend-menu-entry11-sub' . $type[0] . $ct_id . '" class="menu-panel-entry-sub be-panel" rel-m="' . $href["be_files"] . '"><i class="iconBe-arrow-right in-menu"></i>' . $ct_name . '</a></li>';

                        $sb->MoveNext();
                    }
                    $html .= '</ul>';
                } else {
                    $lis[$ct_id] = '<li><a href="javascript:;" id="backend-menu-entry11-sub' . $type[0] . $ct_id . '" class="menu-panel-entry-sub be-panel" rel-m="' . $href["be_files"] . '"><i class="iconBe-arrow-right in-menu"></i>' . $ct_name . '</a>';
                }
                $html .= '</li>';

                $res->MoveNext();
                $i += 1;
            }
            $html .= '</ul>';

            $newhtml = str_replace($rep, $lis, $html);
        }

        return $newhtml;
    }

    public function getPostID()
    {
        global $class_filter;

        return $class_filter->clr_str($_POST["hc_id"]);
    }

    /* file manager layout */
    public function fileManager($type)
    {
        global $db, $cfg, $language, $class_filter, $class_database, $backend_access_url, $smarty;

        $q  = null;
        $c1 = $_GET["do"] != 'sort-nocateg' ? "AND A.`file_category` > '0'" : null;
        $c2 = $_GET["u"] != '' ? sprintf(" AND A.`usr_id`='%s' ", $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $class_filter->clr_str(substr($_GET["u"], 1)))) : null;
        $c2 .= $_GET["k"] != '' ? sprintf(" AND A.`file_key`='%s' ", $class_filter->clr_str(substr($_GET["k"], 1))) : null;
        $sort_time = $_GET["do"] == '' ? $class_filter->clr_str($_POST["file_sort_div_val"]) : $_GET["do"];
        $sort_upd  = $_GET["fdo"] != '' ? $class_filter->clr_str($_GET["fdo"]) : null;
        $sort_for  = $_GET["for"] == '' ? $class_filter->clr_str($_POST["file_type_div_val"]) : $_GET["for"];
        $sort_act  = $class_filter->clr_str($_GET["a"]);
        $act_count = isset($_POST["current_entry_id"]) ? count($_POST["current_entry_id"]) : 0;

        if (substr($_GET["s"], 0, 20) == 'backend-menu-entry11') {
//browse by category
            $s_arr = explode("-", $class_filter->clr_str($_GET["s"]));
            $s_t   = substr($s_arr[3], 4);
            $c1    = "AND A.`file_category`='" . $s_t . "'";
        }

        switch ($sort_act) {
            case "cb-active":
                $act_array = array("table" => "db_" . $type . "files", "field" => "active", "value" => 1);
                break;
            case "cb-inactive":
                $act_array = array("table" => "db_" . $type . "files", "field" => "active", "value" => 0);
                break;
            case "cb-approve":
                $js = 'var popupid = "popuprel-cb";';
                $js .= 'var userid = "-cb";';
                $js .= 'var userkey = "sel";';

                $js .= '$(document).unbind().on({click: function () {';
                $js .= '$(".fancybox-wrap").mask("");';
                $js .= '$.post(current_url + menu_section + "?s=' . $class_filter->clr_str($_GET["s"]) . '&do=confirm-approve", $("#usr-approve-form").serialize(), function(data) {';
                $js .= '$("#approve-update").html(data);';
                $js .= '$(".fancybox-wrap").unmask();';
                $js .= '});';
                $js .= '}}, ".confirm-approve");';

                $js .= '$(document).on({click: function () {if ($(this).hasClass("popup") || $(this).parent().hasClass("popup")) {return;}$(".uactions-list li").removeClass("active");$(this).parent().addClass("active");}}, ".uactions-list li a");';

                if (count($_POST["current_entry_id"]) > 0) {
                    $js .= '$.fancybox.open({ type: "ajax", minWidth: "70%", height: "auto", margin: 20, href: current_url + menu_section + "?s=' . $class_filter->clr_str($_GET["s"]) . '&do=sub-mail" });';
                    $js .= '$("#cb-in2").val("' . rawurlencode(self::cbSQL()) . '");';
                }

                $act_array = array("table" => "db_" . $type . "files", "field" => "approved", "value" => 1);
                break;
            case "cb-disapprove":
                $act_array = array("table" => "db_" . $type . "files", "field" => "approved", "value" => 0);
                break;
            case "cb-feature":
                $act_array = array("table" => "db_" . $type . "files", "field" => "is_featured", "value" => 1);
                break;
            case "cb-unfeature":
                $act_array = array("table" => "db_" . $type . "files", "field" => "is_featured", "value" => 0);
                break;
            case "cb-promote":
                $act_array = array("table" => "db_" . $type . "files", "field" => "is_promoted", "value" => 1);
                break;
            case "cb-unpromote":
                $act_array = array("table" => "db_" . $type . "files", "field" => "is_promoted", "value" => 0);
                break;
            case "cb-public":
                $act_array = array("table" => "db_" . $type . "files", "field" => "privacy", "value" => "public");
                break;
            case "cb-private":
                $act_array = array("table" => "db_" . $type . "files", "field" => "privacy", "value" => "private");
                break;
            case "cb-personal":
                $act_array = array("table" => "db_" . $type . "files", "field" => "privacy", "value" => "personal");
                break;
            case "cb-unflag":
                $act_array = array("table" => "db_" . $type . "files", "field" => "file_flag", "value" => 0);
                break;
        }
        /* checkbox actions, except delete */
        if ($sort_act != '' and $sort_act != 'cb-del-files' and $act_count > 0) {
            $sql_array = array();

            foreach ($_POST["current_entry_id"] as $k => $v) {
                $val         = $class_filter->clr_str($v);
                $sel_array[] = sprintf("'%s'", $val);
            }
            $sel_val    = implode(", ", $sel_array);
            $sel_sql    = sprintf("UPDATE `%s` SET `%s`='%s' WHERE `file_key` IN (%s) LIMIT %s;", $act_array["table"], $act_array["field"], $act_array["value"], $sel_val, $act_count);
            $sel_update = $db->execute($sel_sql);
            $notice     = $db->Affected_Rows() > 0 ? $language["notif.success.request"] : null;
        } elseif ($sort_act == 'cb-del-files') {
            $del_action = self::fileDelete(array($type, $act_count));
        }
        /* save/update file details */
        if ($sort_upd == 'update') {
            $f_key         = $class_filter->clr_str($_POST["hc_id"]);
            $f_title       = $class_filter->clr_str($_POST["files_text_file_title"]);
            $f_descr       = $class_filter->clr_str($_POST["files_text_file_descr"]);
            $f_tags        = $class_filter->clr_str(VForm::clearTag($_POST["files_text_file_tags"]));
            $file_category = "file_category_" . $f_key;
            $f_categ       = intval($_POST[$file_category]);
            $f_date        = $class_filter->clr_str($_POST["files_text_file_datetime"]);
            $f_dur         = $class_filter->clr_str($_POST["files_text_file_duration"]);

            $sel_sql = sprintf("UPDATE `db_%sfiles` SET
                        `upload_date`='%s',
                        `file_duration`='%s',
                        `file_title`='%s',
                        `file_description`='%s',
                        `file_tags`='%s',
                        `file_category`='%s'
                        WHERE
                        `file_key`='%s' LIMIT 1;", $type, $f_date, $f_dur, $f_title, $f_descr, $f_tags, $f_categ, $f_key);
            $sel_update = $db->execute($sel_sql);
            $notice     = $db->Affected_Rows() > 0 ? $language["notif.success.request"] : null;
        }
        /* search query */
        $sql_1 = null;
        $sql_2 = null;

        if (strlen($_GET["sq"]) >= 4) {
            $query = trim($_GET["sq"]);
            $rel   = VGenerate::prepare($query);

            $sql_1 = ", MATCH(`file_title`) AGAINST ('" . $rel . "') AS `Relevance` ";
            $sql_2 = "MATCH(`file_title`) AGAINST('" . $rel . "' IN BOOLEAN MODE) AND ";
        }

        $sql = sprintf("SELECT
                                    A.`db_id`, A.`file_key`, A.`old_file_key`, A.`has_preview`, A.`file_views`, A.`file_responses`, A.`file_like`, A.`file_dislike`, A.`file_favorite`, A.`file_comments`, A.`file_flag`, A.`embed_src`,
                                    %s A.`upload_date`, A.`file_name`, A.`file_size`, A.`file_duration`, A.`file_hd`, A.`file_mobile`, A.`is_featured`, A.`is_promoted`, A.`upload_server`, A.`thumb_server`, A.`thumb_cache`,
                                    A.`file_title`, A.`file_description`, A.`file_tags`, A.`file_category`,
                                    A.`file_key`, A.`privacy`, A.`approved`, A.`deleted`, A.`active`,
                                    D.`usr_key`, D.`usr_user`, D.`usr_dname`, D.`ch_title`
                                    %s
                                    FROM
                                    `db_%sfiles` A, `db_accountuser` D
                                    WHERE
                                    %s
                                    A.`usr_id`=D.`usr_id`
                                    %s%s%s", null, $sql_1, $type, $sql_2, $c1, $q, $c2);

        $tsql = sprintf("SELECT
                                    A.`file_key`,
                                    COUNT(*) AS `total`
                                    %s
                                    FROM
                                    `db_%sfiles` A
                                    WHERE
                                    %s
                                    A.`file_key`>0 %s%s%s", $sql_1, $type, $sql_2, $c1, $q, $c2);

        $def_order = ($sql_1 != '' and $sql_2 != '') ? " ORDER BY 'Relevance' DESC" : " ORDER BY A.`upload_date` DESC";
        switch ($sort_for) {
            case "sort-flv":
                $q_for = " AND A.`file_hd`='0' AND A.`embed_src`='local'";
                break;
            case "sort-mp4":
                $q_for = " AND A.`file_hd`='1' AND A.`embed_src`='local'";
                break;
            case "sort-mob":
                $q_for = " AND A.`file_mobile`='1' AND A.`embed_src`='local'";
                break;
            case "sort-pdf":
                $q_for = " AND A.`file_pdf`='1'";
                break;
            case "sort-swf":
                $q_for = " AND A.`file_swf`='1'";
                break;
            default:
                $sf = explode("-", $sort_for);
                if (isset($sf[1]) and $sf[1] != 'all' and $sf[1] != 'local') {
                    $q_for = " AND A.`embed_src`='" . $sf[1] . "'";
                }
                break;
        }
        switch ($sort_time) {
            case "sort-live":
                $q_sort = " AND A.`stream_live`='1'" . $def_order;
                break;
            case "sort-active":
                $q_sort = " AND A.`active`='1'" . $def_order;
                break;
            case "sort-inactive":
                $q_sort = " AND A.`active`='0'" . $def_order;
                break;
            case "sort-deleted":
                $q_sort = " AND A.`deleted`='1'" . $def_order;
                break;
            case "sort-approved":
                $q_sort = " AND A.`approved`='1'" . $def_order;
                break;
            case "sort-pending":
                $q_sort = " AND A.`approved`='0'" . $def_order;
                break;
            case "sort-personal":
                $q_sort = " AND A.`privacy`='personal'" . $def_order;
                break;
            case "sort-private":
                $q_sort = " AND A.`privacy`='private'" . $def_order;
                break;
            case "sort-public":
                $q_sort = " AND A.`privacy`='public'" . $def_order;
                break;
            case "sort-longest":
                $q_sort = " ORDER BY A.`file_duration` DESC";
                break;
            case "sort-shortest":
                $q_sort = " ORDER BY A.`file_duration` ASC";
                break;
            case "sort-views":
                $q_sort = " AND A.`file_views` > 0 ORDER BY A.`file_views` DESC";
                break;
            case "sort-featured":
                $q_sort = " AND A.`is_featured`='1'" . $def_order;
                break;
            case "sort-promoted":
                $q_sort = " AND A.`is_promoted`='1'" . $def_order;
                break;
            case "sort-nocateg":
                $q_sort = " AND A.`file_category`='0'" . $def_order;
                break;
            case "sort-comm":
                $q_sort = " AND A.`file_comments` > 0 ORDER BY A.`file_comments` DESC";
                break;
            case "sort-resp":
                $q_sort = " AND A.`file_responses` > 0 ORDER BY A.`file_responses` DESC";
                break;
            case "sort-liked":
                $q_sort = " AND A.`file_like` > 0 ORDER BY A.`file_like` DESC";
                break;
            case "sort-disliked":
                $q_sort = " AND A.`file_dislike` > 0 ORDER BY A.`file_dislike` DESC";
                break;
            case "sort-fav":
                $q_sort = " AND A.`file_favorite` > 0 ORDER BY A.`file_favorite` DESC";
                break;
            case "sort-flagged":
                $q_sort = " AND A.`file_flag` > '0' ORDER BY A.`file_flag` DESC";
                break;
            case "sort-recent":
            default:
                $q_sort = $def_order;
        }
        $tsql .= $q_for . $q_sort;
        $tres     = $db->execute($tsql);
        $db_count = $tres->fields["total"];
        /* pagination start */
        $pages                 = new VPagination;
        $pages->items_total    = $db_count;
        $pages->mid_range      = 5;
        $pages->items_per_page = isset($_GET["ipp"]) ? (int) $_GET["ipp"] : ((substr($_GET["s"], 0, 20) == 'backend-menu-entry11') ? $cfg["page_be_file_listing_category"] : $cfg["page_be_file_listing_all"]);
        $pages->paginate();

        $final_sql = $sql . $q_for . $q_sort . $pages->limit . ';';
        $res       = $db->execute($final_sql);
        $page_of   = (($pages->high + 1) > $db_count) ? $db_count : ($pages->high + 1);

        $results_text = $pages->getResultsInfo($page_of, $db_count, 'left');
        $paging_links = $pages->getPaging($db_count, 'right');
        $page_jump    = $paging_links != '' ? $pages->getPageJump('left') : null;
        $ipp_select   = $paging_links != '' ? $pages->getIpp('right') : null;
        /* pagination end */
        $entry_from = '#';
        $bullet_id  = 'ct-bullet1';
        $entry_id   = 'ct-entry-details1';
        $bb         = 'bottom-border';
        $do         = 1;

        $html = self::fileCountAll();
        $html .= ($sort_act != '' and $act_count == 0) ? VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', $language["notif.no.multiple.select"], ''))) : ($notice != '' ? VGenerate::noticeWrap(array('', '', VGenerate::noticeTpl('', '', $notice))) : null);
        $html .= $paging_links != '' ? '<div id="paging-top" class="left-float wdmax section-bottom-border paging-bg-lighter" style="display: inline-block;">' . $page_jump . $ipp_select . '</div>' : null;
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger be-files">';
        while (!$res->EOF) {
            $bb          = ($do == $res->recordcount()) ? '' : $bb;
            $_file_key   = $res->fields["file_key"];
            $_user_key   = $res->fields["usr_key"];
            $_user_name  = $res->fields["usr_user"];
            $_user_dname = $res->fields["usr_dname"];
            $_user_ch    = $res->fields["ch_title"];
            $_user_show  = $_user_dname != '' ? $_user_dname : ($_user_ch != '' ? $_user_ch : $_user_name);
            $_title      = $res->fields["file_title"];
            $_descr      = $res->fields["file_description"];
            $_tags       = $res->fields["file_tags"];
            $_ct_id      = $res->fields["file_category"];
            $_privacy    = $res->fields["privacy"];
            $_approved   = $res->fields["approved"];
            $_deleted    = $res->fields["deleted"];
            $_active     = $res->fields["active"];
            $_db_id      = $res->fields["db_id"];
            $_file_name  = $res->fields["file_name"];
            $_file_size  = $res->fields["file_size"];
            $_file_dur   = $res->fields["file_duration"];
            $_file_hd    = $res->fields["file_hd"];
            $_file_mob   = $res->fields["file_mobile"];
            $_file_feat  = $res->fields["is_featured"];
            $_file_promo = $res->fields["is_promoted"];
            $_file_views = $res->fields["file_views"];
            $_file_fav   = $res->fields["file_favorite"];
            $_file_comm  = $res->fields["file_comments"];
            $_file_resp  = $res->fields["file_responses"];
            $_file_like  = $res->fields["file_like"];
            $_file_dis   = $res->fields["file_dislike"];
            $_file_date  = $res->fields["upload_date"];
            $_file_flag  = $res->fields["file_flag"];
            $_file_src   = $res->fields["embed_src"];
            $_up_server  = $res->fields["upload_server"];
            $_tmb_server = $res->fields["thumb_server"];
            $thumb_cache = $res->fields["thumb_cache"];
            $_tmb_cache  = $thumb_cache > 1 ? $thumb_cache : null;
            $_old_key    = $res->fields["old_file_key"];
            $_has_pv     = $res->fields["has_preview"];
            $_is_short   = null;

            switch ($sort_time) {
                case "sort-views":
                    $_info = VFiles::numFormat($_file_views) . ' ' . ($_file_views != 1 ? $language["frontend.global.views"] : $language["frontend.global.view"]);
                    break;
                case "sort-comm":
                    $_info = VFiles::numFormat($_file_comm) . ' ' . ($_file_comm != 1 ? $language["frontend.global.file.comments"] : $language["frontend.global.file.comment"]);
                    break;
                case "sort-resp":
                    $_info = VFiles::numFormat($_file_resp) . ' ' . ($_file_resp != 1 ? $language["frontend.global.file.responses"] : $language["frontend.global.file.response"]);
                    break;
                case "sort-liked":
                    $_info = VFiles::numFormat($_file_like) . ' ' . ($_file_like != 1 ? $language["frontend.global.file.likes"] : $language["frontend.global.file.like"]);
                    break;
                case "sort-disliked":
                    $_info = VFiles::numFormat($_file_dis) . ' ' . ($_file_dis != 1 ? $language["frontend.global.file.dislikes"] : $language["frontend.global.file.dislike"]);
                    break;
                case "sort-fav":
                    $_info = VFiles::numFormat($_file_fav) . ' ' . ($_file_fav != 1 ? $language["frontend.global.file.favs"] : $language["frontend.global.file.fav"]);
                    break;
                case "sort-longest":
                case "sort-shortest":
                    $_info = '[' . VFiles::fileDuration($_file_dur) . ']';
                    break;
                case "sort-flagged":
                    $_info = VFiles::numFormat($_file_flag) . ' ' . ($_file_flag != 1 ? $language["frontend.global.file.favs"] : $language["frontend.global.file.fav"]);
                    break;
                default:
                    $_info = date('M d, Y H:i A', strtotime($_file_date));
                    break;
            }

            $info_array = array($_file_key, $_user_key, $_title, $_descr, $_tags, $_ct_id, $_privacy, $_approved, $_deleted, $_active, $_db_id, $_file_name, $_file_size, $_file_dur, $_file_hd, $_file_mob, $_file_feat, $_file_views, $_file_fav, $_file_comm, $_file_resp, $_file_like, $_file_dis, $_file_date, $_file_flag, $_file_src, $_up_server, $_tmb_server, $_file_promo, $_old_key, $_has_pv, $_is_short, $_tmb_cache);

            $html .= '<li>';
            $html .= '<div class="left-float ct-entry left-margin10 ' . $bb . ' wd94p" id="' . $bullet_id . '-' . $_file_key . '">';
            $html .= '<div class="responsive-accordion-head d-flex d-column ai-start' . (($_POST and $_GET["fdo"] == 'update' and $_file_key == self::getPostID()) ? ' active' : null) . '">';
            $html .= '<div>';
            $html .= '<div class="icheck-box place-left"><input type="checkbox" name="entryid" value="' . $_file_key . '" class="list-check"></div>';
            $html .= VGenerate::simpleDivWrap('entry-title ct-bullet-label place-left right-padding10 link', '', $_title, '');
            $html .= '</div>';
            $html .= '<div class="d-flex ai-center">';
            $html .= VGenerate::simpleDivWrap('entry-type ct-bullet-label place-left greyed-out ', '', $_info, '');
            $html .= VGenerate::simpleDivWrap('entry-owner ct-bullet-label place-left', '', '<span class="greyed-out">' . $language["frontend.global.by"] . '</span> <a href="' . $cfg["main_url"] . '/' . $backend_access_url . '/' . VHref::getKey("be_members") . '?u=' . $_user_key . '" class="browse-files-username" rel="' . $_user_name . '">' . $_user_show . '</a>', '');
            $html .= '</div>';
            $html .= '<div class="place-right expand-entry">';
            $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: ' . (($_POST and $_GET["fdo"] == 'update' and $_file_key == self::getPostID()) ? 'none' : 'block') . ';"></i>';
            $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: ' . (($_POST and $_GET["fdo"] == 'update' and $_file_key == self::getPostID()) ? 'block' : 'none') . ';"></i>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="responsive-accordion-panel' . $_class[0] . '" style="display: ' . (($_POST and $_GET["fdo"] == 'update' and $_file_key == self::getPostID()) ? 'block' : 'none') . ';">';
            $html .= self::fileDetails('block', $type, $entry_id, $info_array);
            $html .= '</div>';
            $html .= '</li>';

            $do = $do + 1;
            @$res->MoveNext();
        }
        $html .= '</ul>';
        $html .= $db_count > 0 ? '<div id="paging-bottom" class="left-float wdmax paging-top-border paging-bg">' . $paging_links . $results_text . '</div>' : '<div class="left-float wdmax center top-bottom-padding">' . $language["files.text.none"] . '</div>';
        $html .= '<div class=""><input type="hidden" name="cb_in" id="cb-in" value="">';
        $html .= '<script type="text/javascript">$(document).ready(function() {$(".uactions-list li a").click(function() {if ($(this).hasClass("popup") || $(this).parent().hasClass("popup")) {return;}$(".uactions-list li").removeClass("active");$(this).parent().addClass("active");});});</script>';
        $js .= '$(document).ready(function(){var clone = $("#paging-top").clone(true); $("#paging-top").detach(); $(clone).insertBefore("#open-close-links");});';

        $js .= '
                    function brs() {
                         ' . ($type[0] == 'b' ? 'setTimeout( function() { jQuery(".fancybox-inner").css({height:"auto",width:"auto"}); }, 2000);' : null) . '
                    }
                    function blogload(file_key, usr_key) {
                        dst = $(".responsive-accordion-panel.active.p-msover a.blogload");

                        $.get(current_url + "' . VHref::getKey("be_files") . '" + "?do=blogload&f="+file_key+"&u="+usr_key , function( data ) {
                            dst.replaceWith(data);
                            $(".responsive-accordion-panel.active.p-msover").css("height", "auto");
                            $(".responsive-accordion-panel.active.p-msover .update-blog-btn").removeClass("hidden");
                            thisresizeDelimiter();
                        });
                    }

                    $(".update-blog-entry").on("click", function() {
                        ht = $(".responsive-accordion-panel.active.p-msover .d-editable").html();
                        ukey = $(this).attr("rel-usr");
                        fkey = $(this).attr("rel-file");

                        $(".responsive-accordion-panel.active.p-msover").mask(" ");
                        $.post( current_url + "' . VHref::getKey("be_files") . '" + "?do=blogsave", { u: ukey, f: fkey, blog_html: encodeURIComponent(ht) }, function(data){
                            $(".responsive-accordion-panel.active.p-msover .blog-load-response").html(data);
                            $(".responsive-accordion-panel.active.p-msover").unmask();
                        });
                    });


                ';

        /* file players popup box */
        $js .= '$("a.popup").on("click", function(e){ e.preventDefault();';
        $js .= '$("#view-player").replaceWith("");';
        $js .= 'var popupid = $(this).attr("rel");';
        $js .= 'var filekey = $(this).attr("rel-fkey");';
        $js .= 'var userkey = $(this).attr("rel-ukey");';
        $js .= 'var filehd  = $(this).attr("rel-hd");';
        $js .= 'var filepv  = (typeof $(this).attr("rel-pv") != "undefined" ? $(this).attr("rel-pv") : 0);';

        $js .= 'var mh = 0;';

        $js .= 'if($(this).hasClass("manage-ads")){';
        $js .= 'var url = current_url + menu_section + "?do=manage-ads&type=' . $type . '&file_key="+filekey;';
        $js .= '} else if($(this).hasClass("manage-banners")) {';
        $js .= 'var url = current_url + menu_section + "?do=manage-banners&type=' . $type . '&file_key="+filekey;';
        $js .= '} else if($(this).hasClass("manage-subs")) {';
        $js .= 'var url = current_url + menu_section + "?do=manage-subs&type=' . $type . '&file_key="+filekey;';
        $js .= '} else if($(this).hasClass("conversion-log")) {';
        $js .= 'var url = current_url + menu_section + "?do=conversion-log&type=' . $type . '&file_key="+filekey;';
        $js .= '} else if($(this).hasClass("server-paths")) {';
        $js .= 'var url = current_url + menu_section + "?do=server-paths&type=' . $type . '&file_key="+filekey;';
        $js .= '} else {';
        $js .= 'mh = 1; var url = current_url + menu_section + "?p=1&' . $type[0] . '=1&type=' . $type . '&section=backend&usr_key=" + userkey + "&file_key=" + filekey + "&hd="+ filehd + "&pv=" + filepv;';
        $js .= '}';

        if ($type[0] == 'i') {
            // $js .= '$(".fancy-img").fancybox({margin: 10});';
        }
        $js .= 'if (mh == 1) {$.fancybox.open({ afterLoad: brs(), wrapCSS: "be-player", width: "auto", minWidth: "80%", "height": "100vh", type: "ajax", margin: 10, href: url });} else {$.fancybox.open({ minWidth: "90%", type: "ajax", margin: 10, href: url });}';
        $js .= '});';

        /* declare JS */
        $html .= VGenerate::declareJS($js);

        return $html;
    }
    /* load players */
    public function playerLoader()
    {
        global $cfg, $class_filter, $language, $class_database, $db;

        $type     = $class_filter->clr_str($_GET["type"]);
        $section  = $class_filter->clr_str($_GET["section"]);
        $usr_key  = $class_filter->clr_str($_GET["usr_key"]);
        $file_key = $class_filter->clr_str($_GET["file_key"]);
        $is_hd    = intval($_GET["hd"]);

        /* player width and height */
        $_p      = VPlayers::playerInit('backend');
        $_width  = $_p[1];
        $_height = $_p[2];

        $i           = $db->execute(sprintf("SELECT `thumb_cache`, `embed_key`, `embed_src`, `embed_url`, `old_file_key` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, $file_key));
        $embed_key   = $i->fields["embed_key"];
        $embed_src   = $i->fields["embed_src"];
        $embed_url   = $i->fields["embed_url"];
        $old_key     = $i->fields["old_file_key"];
        $thumb_cache = $i->fields["thumb_cache"];
        $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;
        $tmb_url     = VGenerate::thumbSigned($type, $file_key, array($usr_key, $thumb_cache), 0, ($type[0] == 'a' ? 0 : 1));

        switch ($type[0]) {
            case "v": //video players
            case "s": //short players
            case "l": //live players
                if ($embed_src == 'local') {
                    switch ($cfg["video_player"]) {
                        case "vjs":
                            $p_ht = VGenerate::declareJS(VPlayers::VJSJS($section, $usr_key, $file_key, $is_hd, '', ''));
                            break;
                    }
                } else {
                    $info        = array();
                    $info["key"] = $embed_key;
                    $info["url"] = $embed_url;

                    $p_ht = VPlayers::playerEmbedCodes($embed_src, $info, $_width, $_height, '', 1);
                }
                break;
            case "i": //image players
                switch ($cfg["image_player"]) {
                    case "jq":
                        $gs      = $old_key == 1 ? $file_key : md5($cfg["global_salt_key"] . $file_key);
                        $img_url = VGenerate::thumbSigned(array("type" => "image", "server" => "upload", "key" => '/' . $usr_key . '/i/' . $gs . '.jpg'), array($file_key, $gs), array($usr_key, $thumb_cache), 0, 1);
                        $p_ht    = '<a class="main-thumb" href="javascript:;" title="' . $vtitle . '" style="display: block; width: 100%;"><center><img src="' . $img_url . '"></center></a>';
                        break;
                    case "jw":
                        $p_ht = VGenerate::declareJS(VPlayers::JWJS($section, $usr_key, $file_key, 0, '', ''));
                        break;
                    case "flow":
                        $p_ht = VGenerate::declareJS(VPlayers::FPJS($section, $usr_key, $file_key, 0, '', ''));
                        break;
                }
                break;
            case "a": //audio players
                switch ($cfg["audio_player"]) {
                    case "vjs":
                        $p_ht = VGenerate::declareJS(VPlayers::VJSJS($section, $usr_key, $file_key, 0, '', ''));
                        break;
                    case "jw":
                        $p_ht = VGenerate::declareJS(VPlayers::JWJS($section, $usr_key, $file_key, 0, '', ''));
                        break;
                    case "flow":
                        $p_ht = VGenerate::declareJS(VPlayers::FPJS($section, $usr_key, $file_key, 0, '', ''));
                        break;
                }
                break;
            case "d": //document players
                switch ($cfg["document_player"]) {
                    case "free":
                        $p_ht = '<div id="view-player-doc">' . VGenerate::declareJS(VPlayers::FREEJS($section, $usr_key, $file_key, 0, '', '')) . '</div>';
                        break;
                    case "reader":
                        $p_ht = VGenerate::declareJS(VPlayers::DOCJS($section, $usr_key, $file_key, 0, '', ''));
                        break;
                }
                break;
            case "b": //blog loader
                $href_key  = 'blogs';
                $blog_tpl  = $cfg["media_files_dir"] . '/' . $usr_key . '/b/' . $file_key . '.tplb';
                $blog_html = null;

                if (file_exists($blog_tpl)) {
                    $blog_html = file_get_contents($blog_tpl);
                    $media     = VGenerate::extract_text($blog_html);

                    if ($media[0]) {
                        foreach ($media as $media_entry) {
                            $a = explode("_", $media_entry);

                            $mtype = $a[1];
                            $mkey  = $a[2];

                            /* embed code player sizes */
                            $ps         = array();
                            $ps[0]["w"] = '50%';
                            $ps[0]["h"] = 500;

                            $ps[1]["w"] = 640;
                            $ps[1]["h"] = 360;

                            $ps[2]["w"] = 853;
                            $ps[2]["h"] = 480;

                            $ps[3]["w"] = 1280;
                            $ps[3]["h"] = 720;

                            switch ($mtype[0]) {
                                case "l":
                                case "s":
                                case "v": //embed code for video and audio is generated from within player cfg after initialization (class.players.php)
                                    $vi         = sprintf("SELECT A.`file_type`, A.`embed_src`, A.`embed_key`, A.`embed_url`, B.`usr_key` FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`file_key`='%s' LIMIT 1;", $mtype, $mkey);
                                    $mrs        = $db->execute($vi);
                                    $msrc       = $mrs->fields["file_type"];
                                    $membed_src = $mrs->fields["embed_src"];
                                    $membed_key = $mrs->fields["embed_key"];
                                    $membed_url = $mrs->fields["embed_url"];
                                    $mukey      = $mrs->fields["usr_key"];

                                    if ($msrc == 'embed') {
                                        $mec = VPlayers::playerEmbedCodes($membed_src, array("key" => $membed_key, "url" => $membed_url), $ps[0]["w"], $ps[0]["h"]);
                                    } else {
                                        $mec = '<iframe id="file-embed-' . md5($mkey) . '" type="text/html" width="' . $ps[0]["w"] . '" height="' . $ps[0]["h"] . '" src="' . $cfg["main_url"] . '/embed?v=' . $mkey . '" frameborder="0" allowfullscreen></iframe>';
                                    }
                                    break;
                                case "a": //embed code for video and audio is generated from within player cfg after initialization (class.players.php)
                                    $mec = '<iframe id="file-embed-' . md5($mkey) . '" type="text/html" width="' . $ps[0]["w"] . '" height="' . $ps[0]["h"] . '" src="' . $cfg["main_url"] . '/embed?a=' . $mkey . '" frameborder="0" allowfullscreen></iframe>';
                                    break;
                                case "d": //embed code for documents is generated from within player cfg after page load (class.players.php)
                                    $vi           = sprintf("SELECT A.`file_type`, A.`thumb_cache`, A.`embed_src`, A.`embed_key`, B.`usr_key` FROM `db_docfiles` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`file_key`='%s' LIMIT 1;", $mkey);
                                    $mrs          = $db->execute($vi);
                                    $mukey        = $mrs->fields["usr_key"];
                                    $mthumb_cache = $mrs->fields["thumb_cache"];
                                    $mthumb_cache = $mthumb_cache > 1 ? $mthumb_cache : null;

                                    $mdoc = VGenerate::thumbSigned(array("type" => "doc", "server" => "upload", "key" => '/' . $mukey . '/d/' . $mkey . '.pdf'), $mkey, array($mukey, $mthumb_cache), 0, 1);
                                    $mec  = '<embed src="' . $mdoc . '" width="' . $ps[0]["w"] . '" height="' . $ps[0]["h"] . '">';
                                    break;
                                case "i":
                                    $vi           = sprintf("SELECT A.`file_type`, A.`thumb_cache`, A.`embed_src`, A.`embed_key`, B.`usr_key` FROM `db_imagefiles` A, `db_accountuser` B WHERE A.`usr_id`=B.`usr_id` AND A.`file_key`='%s' LIMIT 1;", $mkey);
                                    $mrs          = $db->execute($vi);
                                    $mukey        = $mrs->fields["usr_key"];
                                    $mthumb_cache = $mrs->fields["thumb_cache"];
                                    $mthumb_cache = $mthumb_cache > 1 ? $mthumb_cache : null;

                                    switch ($cfg["image_player"]) {
                                        case "jq":
                                            $_js        = null;
                                            $image_link = VGenerate::thumbSigned($mtype, $mkey, array($mukey, $mthumb_cache), 0, 1, 0);
                                            $mec        = '<img src="' . $image_link . '">';
                                            break;
                                    }
                                    break;
                            }

                            $blog_html = str_replace("[" . $media_entry . "]", $mec, $blog_html);
                        }
                    }
                }

                $p_ht = '<div id="view-player-blog" class="lb-margins">' . $blog_html . '</div>';
                break;

        }

        /* player wrapper */
        $divdata    = null;
        $dim_unit_w = (substr($_width, -1) == '%') ? null : 'px';
        $dim_unit_h = (substr($_height, -1) == '%') ? null : 'px';
        $dim        = 'width: ' . $_width . $dim_unit_w . '; height: ' . $_height . $dim_unit_h . ';';

        $html .= '<div id="view-player" class="center' . (((($cfg["video_player"] == 'vjs' or $cfg["audio_player"] == 'vjs') and $embed_src == 'local') ? (($type[0] != 'b' and $type[0] != 'd' and $type[0] != 'i') ? ' vjs-hd' : null) : null)) . '" style="' . $dim . ' overflow: hidden; ' . '">';
        $html .= (($type[0] == 'v' or $type[0] == 's' or $type[0] == 'l') and $cfg["video_player"] == 'vjs' and $embed_src == 'local') ? '<video id="view-player-' . $file_key . '" preload="none" class="video-js vjs-default-skin vjs-big-play-centered" data-setup="{}" />' : null;
        $html .= ($type[0] == 'a' and $cfg["audio_player"] == 'vjs') ? '<' . $type . ' id="view-player-' . $file_key . '" preload="none" class="video-js vjs-default-skin vjs-big-play-centered" data-setup="{}" />' : null;
        $html .= $p_ht;
        $html .= '</div>'; //PLAYER HERE
        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        echo $html;
    }
    /* manage subtitles */
    public function subsManager($file_key)
    {
        global $db, $cfg, $language, $class_database, $class_filter, $smarty;

        $type  = $class_filter->clr_str($_GET["type"]);
        $dbt   = $cfg[$type . "_player"] == 'vjs' ? 'vjs_subs' : ($cfg[$type . "_player"] == 'jw' ? 'jw_subs' : 'fp_subs');
        $title = $class_database->singleFieldValue('db_' . $type . 'files', 'file_title', 'file_key', $file_key);
        $subs  = $class_database->singleFieldValue('db_' . $type . 'subs', $dbt, 'file_key', $file_key);
        $_cfg  = $subs != '' ? unserialize($subs) : null;

        $sub_dir = $cfg["main_dir"] . '/f_data/data_subtitles/';
        $sub_url = $cfg["main_url"] . '/f_data/data_subtitles/';

        $sub_files = array_values(array_diff(scandir($sub_dir), array('..', '.', '.htaccess')));

        if ($sub_files[0]) {
            $s = 0;
            foreach ($sub_files as $k => $sub) {
                $md = md5($sub);
                if ($cfg[$type . "_player"] == 'jw' or $cfg[$type . "_player"] == 'vjs') {
                    $sht .= '<div class="subs-list icheck-box">';
                    $sht .= '<div class="vs-column fourths"><input type="checkbox" value="' . $md . '" name="sub_name[]" class=""' . (($subs != '' and is_array($_cfg[$md])) ? ' checked="checked"' : null) . '><label>' . $sub . '</label></div>';
                    $sht .= '<div class="vs-column fourths"><input type="text" class="backend-text-input wd100" name="sub_label_' . $md . '" value="' . ($_cfg[$md]["label"]) . '" /></div>';
                    $sht .= '<div class="vs-column half fit"><input type="radio" value="' . $md . '" name="sub_default"' . ($_cfg[$md]["default"] == 1 ? ' checked="checked"' : null) . ' /><label>default</label>' . ($_cfg[$md]["default"] == 1 ? '<a href="javascript:;" onclick="$(\'input:radio\').removeAttr(\'checked\');"> (off)</a>' : null) . '</div>';
                    $sht .= '<div class="clearfix"></div>';
                    $sht .= '</div>';
                } else {
                    $sht .= VGenerate::simpleDivWrap('row top-padding5 subs-list icheck-box', '', '<input type="radio" value="' . $md . '" name="sub_name" class=""' . (($subs != '' and $subs == $md) ? ' checked="checked"' : null) . '><label>' . $sub . '</label>' . (($subs != '' and $subs == $md) ? '<a href="javascript:;" onclick="$(\'.subs-list.icheck-box input\').iCheck(\'uncheck\');"> (off)</a>' : null));
                }
                $s += 1;
            }
        } else {
            $sht = 'No subtitles found in ' . $sub_dir;
        }

        $ht = '<div id="lb-wrapper">';
        $ht .= '<div class="entry-list vs-column full">';
        $ht .= '<div id="subs-update-response' . $file_key . '"></div>';
        $ht .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $ht .= '<li>';
        $ht .= '<div>';
        $ht .= '<div class="responsive-accordion-head-off active">';
        $ht .= VGenerate::headingArticle($language["backend.files.text.subtitles"] . $title, 'icon-paragraph-justify');
        $ht .= '</div>';
        $ht .= '<div class="responsive-accordion-panel active d-flex d-column border-0">';
        $ht .= '<form id="subs-form' . $file_key . '" method="post" action="" class="entry-form-class">';
        $ht .= $sht;
        $ht .= '<br>';
        $ht .= '</form>';
        $ht .= '<a class="subs-update left-align top-padding7 no-display" href="javascript:;" rel-key="' . $file_key . '">' . $language["frontend.global.saveupdate"] . '</a>';
        $ht .= VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey update-entry search-button form-button subs-trigger', '', 0, '<span>' . $language["frontend.global.saveupdate"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '</li>';
        $ht .= '</ul>';
        $ht .= '</div>';
        $ht .= '</div>';

        $html .= $ht;
        /* save/update */
        $_js .= '$(".subs-update").click(function(){';
        $_js .= 'var file_key = $(this).attr("rel-key");';
        $_js .= '$(".fancybox-wrap").mask("");';
        $_js .= '$.post(current_url + menu_section + "?do=subs-update&type=' . $type . '&file_key="+file_key, $("#subs-form"+file_key).serialize(), function(data) {';
        $_js .= '$("#subs-update-response"+file_key).html(data);';
        $_js .= '$(".fancybox-wrap").unmask();';
        $_js .= '});';
        $_js .= '});';
        $_js .= '$(".subs-trigger").click(function(){$(".subs-update").trigger("click");});';
        $_js .= '$(".subs-list.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                                //insert: "<div class=\"icheck_line-icon\"></div><label>" + label_text + "</label>"
                        });
                });

        ';
        /* close popup */
        $js .= $_js;

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* manage banners */
    public function bannerManager($file_key)
    {
        global $db, $cfg, $language, $class_database, $class_filter, $smarty;

        $type = $class_filter->clr_str($_GET["type"]);

        $ad_ids = '50, 51, 52, 53, 54, 55';

        $title  = $class_database->singleFieldValue('db_' . $type . 'files', 'file_title', 'file_key', $file_key);
        $ba_ads = $class_database->singleFieldValue('db_' . $type . 'files', 'banner_ads', 'file_key', $file_key);
        $_ba    = $ba_ads != '' ? unserialize($ba_ads) : null;

        $ads = $db->execute(sprintf("SELECT `adv_id`, `adv_name`, `adv_group` FROM `db_advbanners` WHERE `adv_group` IN (%s) AND `adv_active`='1' ORDER BY `adv_group`;", $ad_ids));

        if ($ads->fields["adv_id"]) {
            while (!$ads->EOF) {
                $adv_id  = $ads->fields["adv_id"];
                $grp_id  = $ads->fields["adv_group"];
                $adv_grp = $class_database->singleFieldValue('db_advgroups', 'adv_name', 'db_id', $grp_id);

                $bht .= '<input type="checkbox" value="' . $ads->fields["adv_id"] . '" name="ad_name[]" class=""' . (($ba_ads != '' and in_array($ads->fields["adv_id"], $_ba)) ? ' checked="checked"' : null) . '><label>' . $ads->fields["adv_name"] . ' (' . $adv_grp . ')</label><br>';
                $ads->MoveNext();
            }
        } else {
            $bht = '<p>' . $language["backend.menu.no.categ.file"] . '</p>';
        }

        $ht = '<div id="lb-wrapper">';
        $ht .= '<div class="entry-list vs-column full">';
        $ht .= '<div id="banner-update-response' . $file_key . '"></div>';
        $ht .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $ht .= '<li>';
        $ht .= '<div>';
        $ht .= '<div class="responsive-accordion-head-off active">';
        $ht .= VGenerate::headingArticle($language["backend.adv.banner.for"] . $title, 'icon-' . ($type == 'doc' ? 'file' : ($type == 'blog' ? 'pencil2' : $type)));
        $ht .= '</div>';
        $ht .= '<div class="responsive-accordion-panel active d-flex d-column border-0">';
        $ht .= '<form id="banner-form' . $file_key . '" method="post" action="" class="entry-form-class">';
        $ht .= '<div class="left-float icheck-box banner-sel">';
        $ht .= $bht;
        $ht .= '</div>';
        $ht .= '</form>';
        $ht .= VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey update-entry search-button form-button trg-update', '', $file_key, '<span>' . $language["frontend.global.saveupdate"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');
        $ht .= '<a class="banner-update left-align top-padding7 no-display" href="javascript:;" rel-key="' . $file_key . '">Save/Update</a>';
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '</li>';
        $ht .= '</ul>';
        $ht .= '</div>';
        $ht .= '</div>';

        $html .= $ht;
        /* save/update */
        $_js .= '$(".trg-update").click(function(){';
        $_js .= '$(".banner-update").trigger("click");';
        $_js .= '});';
        $_js .= '$(".banner-update").click(function(){';
        $_js .= 'var file_key = $(this).attr("rel-key");';
        $_js .= '$(".fancybox-wrap").mask("");';
        $_js .= '$.post(current_url + menu_section + "?do=banner-update&type=' . $type . '&file_key="+file_key, $("#banner-form"+file_key).serialize(), function(data) {';
        $_js .= '$("#banner-update-response"+file_key).html(data);';
        $_js .= '$(".fancybox-wrap").unmask();';
        $_js .= '});';
        $_js .= '});';
        $_js .= '$(".banner-sel.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                                //insert: "<div class=\"icheck_line-icon\"></div><label>" + label_text + "</label>"
                        }); });';
        /* close popup */
        $js .= $_js;

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* manage fp ads */
    public function FPadsManager($file_key)
    {
        global $db, $cfg, $language, $class_database, $class_filter, $smarty;

        $s      = 0;
        $title  = $class_database->singleFieldValue('db_videofiles', 'file_title', 'file_key', $file_key);
        $fp_ads = $class_database->singleFieldValue('db_videofiles', 'fp_ads', 'file_key', $file_key);
        $_fp    = $fp_ads != '' ? unserialize($fp_ads) : null;
        $type   = $class_filter->clr_str($_GET["type"]);

        $ads = $db->execute("SELECT `ad_id`, `ad_name`, `ad_cuepoint` FROM `db_fpadentries` WHERE `ad_active`='1';");

        $html = '<div id="lb-wrapper">';
        $html .= '<div class="entry-list vs-column full">';
        $html .= '<div id="ad-update-response' . $file_key . '"></div>';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle($language["backend.adv.select.for." . $type] . $title, 'icon-video');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active d-flex d-column border-0">';
        $html .= '<form id="ad-form' . $file_key . '" method="post" action="" class="entry-form-class">';
        $html .= '<div class="icheck-box" id="ad-rolls">';
        if ($ads->fields["ad_id"]) {
            while (!$ads->EOF) {
                $html .= VGenerate::simpleDivWrap('row top-padding5', '', VGenerate::simpleDivWrap('', '', '<input type="checkbox" value="' . $ads->fields["ad_id"] . '" name="ad_name_mid[]" class=""' . (($fp_ads != '' and in_array($ads->fields["ad_id"], $_fp)) ? ' checked="checked"' : null) . '><label>' . $ads->fields["ad_name"] . ' [' . $ads->fields["ad_cuepoint"] . ']</label>'));

                $s += 1;
                $ads->MoveNext();
            }
        } else {
            $html .= '<p>' . $language["backend.menu.no.video.banners"] . '</p>';
        }
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button trg-update', '', $file_key, '<span>' . $language["frontend.global.saveupdate"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');
        $html .= '<a class="ad-update left-align top-padding7 no-display" href="javascript:;" rel-key="' . $file_key . '">Save/Update</a>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        /* save/update */
        $_js .= '$(".trg-update").click(function(){';
        $_js .= '$(".ad-update").trigger("click");';
        $_js .= '});';
        $_js .= '$(".ad-update").click(function(){';
        $_js .= 'var file_key = $(this).attr("rel-key");';
        $_js .= '$(".fancybox-inner").mask(" ");';
        $_js .= '$.post(current_url + menu_section + "?do=ad-update&type=' . $type . '&file_key="+file_key, $("#ad-form"+file_key).serialize(), function(data) {';
        $_js .= '$("#ad-update-response"+file_key).html(data);';
        $_js .= '$(".fancybox-inner").unmask();';
        $_js .= '});';
        $_js .= '});';
        $_js .= '$("#ad-rolls.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                                //insert: "<div class=\"icheck_line-icon\"></div><label>" + label_text + "</label>"
                        }); });';

        $js .= $_js;

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* manage videojs ads */
    public function VJSadsManager($file_key)
    {
        global $db, $cfg, $language, $class_database, $class_filter, $smarty;

        $s      = 0;
        $type   = $class_filter->clr_str($_GET["type"]);
        $title  = $class_database->singleFieldValue('db_' . $type . 'files', 'file_title', 'file_key', $file_key);
        $jw_ads = $class_database->singleFieldValue('db_' . $type . 'files', 'vjs_ads', 'file_key', $file_key);
        $_jw    = $jw_ads != '' ? unserialize($jw_ads) : null;

        $off = $db->execute("SELECT `ad_id`, `ad_name`, `ad_client` FROM `db_vjsadentries` WHERE `ad_active`='1';");

        $ht = '<div id="lb-wrapper">';
        $ht .= '<div class="entry-list vs-column full">';
        $ht .= '<div id="ad-update-response' . $file_key . '"></div>';
        $ht .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $ht .= '<li>';
        $ht .= '<div>';
        $ht .= '<div class="responsive-accordion-head-off active">';
        $ht .= VGenerate::headingArticle($language["backend.adv.select.for." . $type] . $title, 'icon-video');
        $ht .= '</div>';
        $ht .= '<div class="responsive-accordion-panel active d-flex d-column border-0">';
        $ht .= '<form id="ad-form' . $file_key . '" method="post" action="" class="entry-form-class">';
        $ht .= '<div class="left-float">';
        $ht .= '<div class="left-float wd200">';
        if ($off->fields["ad_id"]) {
            while (!$off->EOF) {
                $ht .= ($s > 0 and ($s % 10) == 0) ? '</div><div class="left-float wd200">' : null;
                $ht .= VGenerate::simpleDivWrap('row top-padding5', '', VGenerate::simpleDivWrap('icheck-box', 'add-rolls', '<input type="checkbox" value="' . $off->fields["ad_id"] . '" name="ad_name[]" class=""' . (($jw_ads != '' and in_array($off->fields["ad_id"], $_jw)) ? ' checked="checked"' : null) . '><label>' . $off->fields["ad_name"] . ' (' . $off->fields["ad_client"] . ')</label>'));

                $s += 1;
                $off->MoveNext();
            }
        } else {
            $ht .= '<p>' . $language["backend.menu.no.video.banners"] . '</p>';
        }
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '</form>';
        $ht .= VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey update-entry search-button form-button trg-update', '', $file_key, '<span>' . $language["frontend.global.saveupdate"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');
        $ht .= '<a class="ad-update left-align top-padding7 no-display" href="javascript:;" rel-key="' . $file_key . '">Save/Update</a>';
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '</li>';
        $ht .= '</ul>';
        $ht .= '</div>';
        $ht .= '</div>';

        $html .= $ht;

        /* save/update */
        $_js .= '$(".trg-update").click(function(){';
        $_js .= '$(".ad-update").trigger("click");';
        $_js .= '});';
        $_js .= '$(".ad-update").click(function(){';
        $_js .= 'var file_key = $(this).attr("rel-key");';
        $_js .= '$(".fancybox-inner").mask(" ");';
        $_js .= '$.post(current_url + menu_section + "?do=ad-update&type=' . $type . '&file_key="+file_key, $("#ad-form"+file_key).serialize(), function(data) {';
        $_js .= '$("#ad-update-response"+file_key).html(data);';
        $_js .= '$(".fancybox-inner").unmask();';
        $_js .= '});';
        $_js .= '});';

        $_js .= '$("#add-rolls.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                                //insert: "<div class=\"icheck_line-icon\"></div><label>" + label_text + "</label>"
                        }); });';
        /* close popup */
        $js .= $_js;

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* manage jw ads */
    public function JWadsManager($file_key)
    {
        global $db, $cfg, $language, $class_database, $class_filter, $smarty;

        $s      = 0;
        $type   = $class_filter->clr_str($_GET["type"]);
        $title  = $class_database->singleFieldValue('db_' . $type . 'files', 'file_title', 'file_key', $file_key);
        $jw_ads = $class_database->singleFieldValue('db_' . $type . 'files', 'jw_ads', 'file_key', $file_key);
        $_jw    = $jw_ads != '' ? unserialize($jw_ads) : null;

        $pre = $db->execute("SELECT `ad_id`, `ad_name`, `ad_client` FROM `db_jwadentries` WHERE `ad_position`='pre' AND `ad_active`='1';");

        if ($pre->fields["ad_id"]) {
            $preroll = '<select name="ad_name_pre" class="backend-select-input wd200">';
            $preroll .= '<option value="0">---</option>';
            while (!$pre->EOF) {
                $preroll .= '<option value="' . $pre->fields["ad_id"] . '"' . (($jw_ads != '' and in_array($pre->fields["ad_id"], $_jw)) ? ' selected="selected"' : null) . '>' . $pre->fields["ad_name"] . ' (' . $pre->fields["ad_client"] . ')</option>';
                $pre->MoveNext();
            }
            $preroll .= '</select>';
        }
        $post = $db->execute("SELECT `ad_id`, `ad_name`, `ad_client` FROM `db_jwadentries` WHERE `ad_position`='post' AND `ad_active`='1';");
        if ($post->fields["ad_id"]) {
            $postroll = '<select name="ad_name_post" class="backend-select-input wd200">';
            $postroll .= '<option value="0">---</option>';
            while (!$post->EOF) {
                $postroll .= '<option value="' . $post->fields["ad_id"] . '"' . (($jw_ads != '' and in_array($post->fields["ad_id"], $_jw)) ? ' selected="selected"' : null) . '>' . $post->fields["ad_name"] . ' (' . $post->fields["ad_client"] . ')</option>';
                $post->MoveNext();
            }
            $postroll .= '</select>';
        }

        $off = $db->execute("SELECT `ad_id`, `ad_name`, `ad_client` FROM `db_jwadentries` WHERE `ad_position`='offset' AND `ad_active`='1';");

        $ht = '<div id="lb-wrapper">';
        $ht .= '<div class="entry-list vs-column full">';
        $ht .= '<div id="ad-update-response' . $file_key . '"></div>';
        $ht .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $ht .= '<li>';
        $ht .= '<div>';
        $ht .= '<div class="responsive-accordion-head-off active border-0">';
        $ht .= VGenerate::headingArticle($language["backend.adv.select.for." . $type] . $title, 'icon-video');
        $ht .= '</div>';
        $ht .= '<div class="responsive-accordion-panel active d-flex d-column">';
        $ht .= '<form id="ad-form' . $file_key . '" method="post" action="" class="entry-form-class">';
        $ht .= '<div class="left-float">';
        $ht .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd100', '', '<label>' . $language["backend.files.text.prerolls"] . '</label>') . VGenerate::simpleDivWrap('left-float lh20 selector', '', $preroll));
        $ht .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd100', '', '<label>' . $language["backend.files.text.postrolls"] . '</label>') . VGenerate::simpleDivWrap('left-float lh20 selector', '', $postroll));

        $ht .= VGenerate::simpleDivWrap('row left-float lh20 wd100', '', '<label>' . $language["backend.files.text.midrolls"] . '</label>');
        $ht .= '<div class="left-float wd200">';
        if ($off->fields["ad_id"]) {
            while (!$off->EOF) {
                $ht .= ($s > 0 and ($s % 10) == 0) ? '</div><div class="left-float wd200">' : null;
                $ht .= VGenerate::simpleDivWrap('row top-padding5', '', VGenerate::simpleDivWrap('icheck-box', 'mid-rolls', '<input type="checkbox" value="' . $off->fields["ad_id"] . '" name="ad_name_mid[]" class=""' . (($jw_ads != '' and in_array($off->fields["ad_id"], $_jw)) ? ' checked="checked"' : null) . '><label>' . $off->fields["ad_name"] . ' (' . $off->fields["ad_client"] . ')</label>'));

                $s += 1;
                $off->MoveNext();
            }
        } else {
            $ht .= '<p>' . $language["backend.menu.no.video.banners"] . '</p>';
        }
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '</form>';
        $ht .= VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey update-entry search-button form-button trg-update', '', $file_key, '<span>' . $language["frontend.global.saveupdate"] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');
        $ht .= '<a class="ad-update left-align top-padding7 no-display" href="javascript:;" rel-key="' . $file_key . '">Save/Update</a>';
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '</li>';
        $ht .= '</ul>';
        $ht .= '</div>';
        $ht .= '</div>';

        $html .= $ht;

        /* save/update */
        $_js .= '$(".trg-update").click(function(){';
        $_js .= '$(".ad-update").trigger("click");';
        $_js .= '});';
        $_js .= '$(".ad-update").click(function(){';
        $_js .= 'var file_key = $(this).attr("rel-key");';
        $_js .= '$(".fancybox-inner").mask(" ");';
        $_js .= '$.post(current_url + menu_section + "?do=ad-update&type=' . $type . '&file_key="+file_key, $("#ad-form"+file_key).serialize(), function(data) {';
        $_js .= '$("#ad-update-response"+file_key).html(data);';
        $_js .= '$(".fancybox-inner").unmask();';
        $_js .= '});';
        $_js .= '});';

        $_js .= '$(function(){SelectList.init("ad_name_pre");});';
        $_js .= '$(function(){SelectList.init("ad_name_post");});';

        $_js .= '$("#mid-rolls.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                                //insert: "<div class=\"icheck_line-icon\"></div><label>" + label_text + "</label>"
                        }); });';
        /* close popup */
        $js .= $_js;

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* update banner and video ads */
    public function adUpdate($file_key, $banner = '')
    {
        global $db, $language, $cfg, $class_filter;

        $type = $class_filter->clr_str($_GET["type"]);

        if ($banner == '' and $cfg["video_player"] != 'vjs') {
            $mid = array();

            $pre    = $class_filter->clr_str($_POST["ad_name_pre"]);
            $post   = $class_filter->clr_str($_POST["ad_name_post"]);
            $mid    = $_POST["ad_name_mid"];
            $mid_ct = count($_POST["ad_name_mid"]);

            $sar = $mid;
            if ($cfg["video_player"] == 'jw') {
                $sar[] = $pre;
                $sar[] = $post;
            }
        } else {
            $sar = $_POST["ad_name"];
        }
        $sar = serialize($sar);
        $sct = ((md5($sar) === md5('a:2:{i:0;s:1:"0";i:1;s:1:"0";}')) or $sar === 'N;') ? 0 : ($mid_ct > 0 ? $mid_ct : count($sar));
        $sql = sprintf("UPDATE `db_%sfiles` SET `%s`='%s' WHERE `file_key`='%s' LIMIT 1;", $type, ($banner == '' ? (($type == 'video' and $cfg["video_player"] == 'flow') ? 'fp' : ((($type == 'video' or $type == 'live' or $type == 'short') and $cfg["video_player"] == 'vjs') ? 'vjs' : $cfg[$type . "_player"])) . '_ads' : 'banner_ads'), ($sct == 0 ? null : $sar), $file_key);
        $res = $db->execute($sql);
        $ar  = $db->Affected_Rows();

        echo ($ar > 0 ? VGenerate::noticeTpl('', '', $language["notif.success.request"]) : null);
    }
    /* update video subtitles */
    public function subsUpdate($file_key)
    {
        global $db, $language, $cfg, $class_database, $class_filter;

        $type = $class_filter->clr_str($_GET["type"]);

        $sar = ($cfg[$type . "_player"] == 'jw' or $cfg[$type . "_player"] == 'vjs') ? serialize($_POST["sub_name"]) : $class_filter->clr_str($_POST["sub_name"]);
        $sct = $sar == 'N;' ? 0 : 1;
        $dbt = $cfg[$type . "_player"] == 'vjs' ? 'vjs_subs' : ($cfg[$type . "_player"] == 'jw' ? 'jw_subs' : 'fp_subs');

        if (($cfg[$type . "_player"] == 'jw' or $cfg[$type . "_player"] == 'vjs') and count($_POST["sub_name"]) > 0) {
            $s = array();
            foreach ($_POST["sub_name"] as $v) {
                $label   = $class_filter->clr_str($_POST["sub_label_" . $v]);
                $default = $class_filter->clr_str($_POST["sub_default"]);
                $s[$v]   = array("label" => $label, "default" => ($default == $v ? 1 : 0));
            }
            $sar = serialize($s);
            $sct = $sar == 'N;' ? 0 : 1;
        }

        $sql = sprintf("UPDATE `db_%ssubs` SET `%s`='%s' WHERE `file_key`='%s' LIMIT 1;", $type, $dbt, ($sct > 0 ? $sar : ""), $file_key);
        $res = $db->execute($sql);
        $ar  = $db->Affected_Rows();

        if (intval($ar) == 0 and $sct > 0) {
            $class_database->doInsert('db_' . $type . 'subs', array("file_key" => $file_key, $dbt => $sar));

            $ar = $db->Affected_Rows();
        }

        echo ($ar > 0 ? VGenerate::noticeTpl('', '', $language["notif.success.request"]) : null);
    }

    /* file details */
    public function fileDetails($_dsp, $type, $entry_id, $info_array)
    {
        global $cfg, $language, $class_database, $db, $smarty;

        $db_id = $info_array[0];
        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_dsp  = $_init[0];

        $_user_key   = $info_array[1];
        $_title      = $info_array[2];
        $_descr      = $info_array[3];
        $_tags       = $info_array[4];
        $_ct_id      = $info_array[5];
        $_privacy    = $info_array[6];
        $_approved   = $info_array[7];
        $_deleted    = $info_array[8];
        $_active     = $info_array[9];
        $_db_id      = $info_array[10];
        $_file_name  = $info_array[11];
        $_file_size  = $info_array[12];
        $_file_dur   = $info_array[13];
        $_file_hd    = $info_array[14];
        $_file_mob   = $info_array[15];
        $_file_feat  = $info_array[16];
        $_file_views = $info_array[17];
        $_file_fav   = $info_array[18];
        $_file_comm  = $info_array[19];
        $_file_resp  = $info_array[20];
        $_file_like  = $info_array[21];
        $_file_dis   = $info_array[22];
        $_file_date  = $info_array[23];
        $_file_flag  = $info_array[24];
        $_file_src   = $info_array[25];
        $_up_server  = $info_array[26];
        $_tmb_server = $info_array[27];
        $_file_promo = $info_array[28];
        $_old_key    = $info_array[29];
        $_has_pv     = $info_array[30];
        $_is_short   = $info_array[31];
        $thumb_cache = $info_array[32];

        $gs         = (($cfg["conversion_" . $type . "_previews"] == 1 and $_has_pv == 1 and $_old_key == 0) or ($cfg["conversion_" . $type . "_previews"] == 0 and $_old_key == 0)) ? md5($cfg["global_salt_key"] . $db_id) : $db_id;
        $a_rel      = 'rel="popuprel' . $db_id . '" rel-fkey="' . $db_id . '" rel-ukey="' . $_user_key . '" rel-hd="' . ($type[0] == 'b' ? 'b' : $_file_hd) . '" class="popup' . ($type[0] == 'i' ? ' fancy-img' : null) . '"';
        $img_url    = $type[0] == 'i' ? VGenerate::thumbSigned(array("type" => "image", "server" => "upload", "key" => '/' . $_user_key . '/i/' . $gs . '.jpg'), array($db_id, $gs), $_user_key, 0, 1) : null;
        $img_url_pv = $type[0] == 'i' ? VGenerate::thumbSigned(array("type" => "image", "server" => "upload", "key" => '/' . $_user_key . '/i/' . $db_id . '.jpg'), array($db_id, $gs), $_user_key, 0, 1) : null;

        $tmb_url = VGenerate::thumbSigned($type, $db_id, array($_user_key, $thumb_cache), 0, 1, 0);

        if (file_exists($cfg["media_files_dir"] . '/' . $_user_key . '/t/' . $db_id . '/1' . $thumb_cache . '.jpg')) {
            $_thumb   = '<a href="' . ($type[0] == 'i' ? $img_url : 'javascript:;') . '" ' . $a_rel . '><img id="' . $db_id . '-thumb1" src="' . $tmb_url . '" />' . (($cfg["conversion_" . $type . "_previews"] == 1 and $_has_pv == 1 and $_old_key == 0) ? $language["backend.files.menu.full"] : null) . '</a>';
            $_thumb_2 = ($cfg["conversion_" . $type . "_previews"] == 1 and $_has_pv == 1 and $_old_key == 0) ? '<a href="' . ($type[0] == 'i' ? $img_url_pv : 'javascript:;') . '" ' . $a_rel . ' rel-pv="1"><img id="' . $db_id . '-thumb2" src="' . $tmb_url . '" />' . $language["backend.files.menu.preview"] . '</a>' : null;
        } else {
            $_thumb = '<div style="display: block;padding: 15px 0;">' . VFileinfo::get_progress($db_id) . '</div>';
        }

        $_btn = VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET["do"] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET["do"] == 'add' ? $language["frontend.global.savenew"] : $language["frontend.global.saveupdate"]) . '</span>'), 'display: inline-block-off;');

        $tabs = '<div class="swiper-ph swiper-ph-main"></div>';
        $tabs .= '<div class="swiper-top swiper-top-main" id="swiper-' . $db_id . '" style="display:' . VGenerate::ssd() . '">';
        $tabs .= '<div class="swiper-button-prev swiper-button-prev-main"' . VGenerate::sso() . '></div>';
        $tabs .= '<div class="swiper-button-next swiper-button-next-main"' . VGenerate::sso() . '></div>';
        $tabs .= '<div class="swiper swiper-main">';
        $tabs .= '<div class="swiper-wrapper">';
        $tabs .= '<div class="swiper-slide' . ((!$_POST or ($_POST and (isset($_GET["do"]) and $_GET["fdo"] != 'update')) or ($_POST and $_GET["fdo"] == 'update' and $db_id != self::getPostID())) ? ' swiper-slide-current' : null) . '"><a class="icon icon-list2" href="javascript:;" onclick="$(\'#entry-stats' . $db_id . '\').show(); $(\'#entry-details' . $db_id . '\').hide(); $(\'#entry-blog' . $db_id . '\').hide(); $(this).parent().next().removeClass(\'swiper-slide-current\'); $(this).parent().addClass(\'swiper-slide-current\');"><span>File stats</span></a></div>';
        $tabs .= '<div class="swiper-slide' . (($_POST and $_GET["fdo"] == 'update' and $db_id == self::getPostID()) ? ' swiper-slide-current' : null) . '"><a class="icon icon-pencil" href="javascript:;" onclick="$(\'#entry-details' . $db_id . '\').show(); $(\'#entry-stats' . $db_id . '\').hide(); $(\'#entry-blog' . $db_id . '\').hide(); $(this).parent().prev().removeClass(\'swiper-slide-current\'); $(this).parent().addClass(\'swiper-slide-current\');"><span>' . $language["backend.files.text.edit.fd"] . '</span></a></div>';
        $tabs .= ($type[0] == 'b') ? '<div class="swiper-slide"><a href="javascript:;" class="icon icon-code" rel="popuprel' . $db_id . '" rel-fkey="' . $db_id . '" onclick="$(\'#entry-details' . $db_id . '\').hide(); $(\'#entry-stats' . $db_id . '\').hide(); $(\'#entry-blog' . $db_id . '\').show();"><span>' . $language["backend.files.text.blog"] . '</span></a></div>' : null;
        $tabs .= (($type[0] == 'v' or $type[0] == 'l') and $_file_src == 'local') ? '<div class="swiper-slide"><a href="javascript:;" class="icon icon-coin popup manage-ads" rel="popuprel' . $db_id . '" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.ads." . ($type == 'audio' ? 'audio' : 'video')] . '</span></a></div>' : null;
        $tabs .= '<div class="swiper-slide"><a href="javascript:;" class="icon icon-coin popup manage-banners" rel="popuprel' . $db_id . '" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.banner"] . '</a></div>';
        $tabs .= ($type[0] == 'v' and $_file_src == 'local') ? '<div class="swiper-slide"><a href="javascript:;" class="icon icon-paragraph-justify popup manage-subs" rel="popuprel' . $db_id . '" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.subs"] . '</span></a></div>' : null;
        $tabs .= (($_file_src == 'local' or ($type[0] != 'v' and $type[0] != 'l')) and $_up_server == 0) ? '<div class="swiper-slide"><a href="javascript:;" class="icon icon-tree popup server-paths" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.paths"] . '</span></a></div>' : null;
        $tabs .= (($_file_src == 'local' and ($type[0] != 'i' and $type[0] != 'b' and $type[0] != 'l')) and $_up_server == 0) ? '<div class="swiper-slide"><a href="javascript:;" class="icon icon-console popup conversion-log" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.log." . $type[0]] . '</span></a></div>' : null;
        $tabs .= '</div></div></div>';

        $html = '<div class="popupbox-be no-display" id="popuprel' . $db_id . '"></div><div id="fade' . $db_id . '" class="fade no-display"></div>';
        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="file-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= '<div class="tabs tabs-style-topline">';
        $html .= $tabs;
        $html .= '<nav class="no-display"><ul class="ul-no-list uactions-list left-float top-bottom-padding bottom-border">';
        $html .= '<li class="' . ((!$_POST or ($_POST and (isset($_GET["do"]) and $_GET["fdo"] != 'update')) or ($_POST and $_GET["fdo"] == 'update' and $db_id != self::getPostID())) ? 'active' : null) . '"><a class="icon icon-list2" href="javascript:;" onclick="$(\'#entry-stats' . $db_id . '\').show(); $(\'#entry-details' . $db_id . '\').hide(); $(\'#entry-blog' . $db_id . '\').hide();"><span>File stats</span></a></li>';
        $html .= '<li class="' . (($_POST and $_GET["fdo"] == 'update' and $db_id == self::getPostID()) ? 'active' : null) . '"><a class="icon icon-pencil" href="javascript:;" onclick="$(\'#entry-details' . $db_id . '\').show(); $(\'#entry-stats' . $db_id . '\').hide(); $(\'#entry-blog' . $db_id . '\').hide();"><span>Edit file details</span></a></li>';

        $html .= ($type[0] == 'b') ? '<li><a href="javascript:;" class="icon icon-code" rel="popuprel' . $db_id . '" rel-fkey="' . $db_id . '" onclick="$(\'#entry-details' . $db_id . '\').hide(); $(\'#entry-stats' . $db_id . '\').hide(); $(\'#entry-blog' . $db_id . '\').show();"><span>' . $language["backend.files.text.blog"] . '</span></a></li>' : null;

        $html .= (($type[0] == 'v' or $type[0] == 'l') and $_file_src == 'local') ? '<li><a href="javascript:;" class="icon icon-coin popup manage-ads" rel="popuprel' . $db_id . '" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.ads." . ($type == 'audio' ? 'audio' : 'video')] . '</span></a></li>' : null;
        $html .= '<li><a href="javascript:;" class="icon icon-coin popup manage-banners" rel="popuprel' . $db_id . '" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.banner"] . '</a></li>';
        $html .= ($type[0] == 'v' and $_file_src == 'local') ? '<li><a href="javascript:;" class="icon icon-paragraph-justify popup manage-subs" rel="popuprel' . $db_id . '" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.subs"] . '</span></a></li>' : null;
        $html .= (($_file_src == 'local' or ($type[0] != 'v' and $type[0] != 'l')) and $_up_server == 0) ? '<li><a href="javascript:;" class="icon icon-tree popup server-paths" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.paths"] . '</span></a></li>' : null;
        $html .= (($_file_src == 'local' and ($type[0] != 'i' and $type[0] != 'b' and $type[0] != 'l')) and $_up_server == 0) ? '<li><a href="javascript:;" class="icon icon-console popup conversion-log" rel-fkey="' . $db_id . '"><span>' . $language["backend.files.text.log." . $type[0]] . '</span></a></li>' : null;
        $html .= '</ul></nav></div>';

        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));

        $html .= '<div class="entry-blog-wrap"' . ((($_GET["do"] != 'add' and $_POST and self::getPostID() != $db_id) or ($_POST and !isset($_GET["a"])) or !$_POST) ? ' style="display: none;"' : '') . ' id="entry-blog' . $db_id . '">';
        if ($type[0] == 'b') {
            $html .= '<div class="blog-load-response"></div>';
            $html .= '<a href="javascript:;" class="blogload" style="color: #444;" onclick="blogload(\'' . $db_id . '\', \'' . $_user_key . '\');">Click Here to load blog content</a>';
            $html .= '<div class="clearfix"></div>';
            $html .= '
                    <div style="display: inline-block; margin-top: 25px;" class="left-float update-blog-btn hidden">
                        <button name="save_changes" rel-usr="' . $_user_key . '" rel-file="' . $db_id . '" class="save-entry-button button-grey search-button form-button update-blog-entry" type="button" value="1" onfocus="blur();">
                            <span>' . $language["frontend.global.saveupdate"] . '</span>
                        </button>
                    </div>
        ';
        }
        $html .= '</div>';

        $html .= '<div class="entry-details-wrap"' . (($_POST and self::getPostID() == $db_id and !isset($_GET["a"])) ? ' style="display: none;"' : '') . ' id="entry-stats' . $db_id . '">';
        $html .= '<div class="vs-column half fit">';
        $html .= VGenerate::simpleDivWrap('row left-float entry-thumbs', $db_id . '-thumbs', $_thumb . $_thumb_2 . $_thumb_3 . (($type[0] == 'v' and $_file_src == 'local' and $_up_server == 0) ? '<div class="clearfix"></div><br><ul class="uactions-list"><li><button onfocus="blur();" value="1" type="button" rel="' . $db_id . '" class="button-grey search-button form-button save-entry-button thumb-reset" id="btn-' . $db_id . '-save_changes" name="save_changes"><span>' . $language["backend.files.text.thumbs"] . '</span></button></li><li><button onfocus="blur();" value="1" type="button" rel="' . $db_id . '" class="button-grey search-button form-button save-entry-button preview-reset" id="btnpv-' . $db_id . '-save_changes" name="save_changes"><span>' . $language["backend.files.text.preview"] . '</span></button></li></ul>' : null));
        $html .= '</div>';
        $html .= '<div class="vs-column fourths">';
        $html .= '<ul class="entry-details">';
        $html .= '<li><label>' . $language["backend.files.text.key"] . '</label>' . VGenerate::simpleDivWrap('left-float lh20 conf-green this-file-key', '', $db_id, '', 'span') . '</li>';
        $html .= '<li><label>' . $language["backend.files.text.state"] . '</label>' . ($_active == 1 ? '<span class="conf-green">' . $language["frontend.global.yes"] . '</span></li>' : '<span class="err-red">' . $language["frontend.global.no"] . '</span></li>');
        $html .= '<li><label>' . $language["backend.files.text.approved"] . '</label>' . ($_approved == 1 ? '<span class="conf-green">' . $language["frontend.global.yes"] . '</span></li>' : '<span class="err-red">' . $language["frontend.global.no"] . '</span></li>');
        $html .= '<li><label>' . $language["backend.files.text.deleted"] . '</label>' . ($_deleted == 0 ? '<span class="conf-green">' . $language["frontend.global.no"] . '</span></li>' : '<span class="err-red">' . $language["frontend.global.yes"] . '</span></li>');
        $html .= '<li><label>' . $language["backend.files.text.privacy"] . '</label>' . ($_privacy == 'public' ? '<span class="conf-green">' . $language["frontend.global." . $_privacy] . '</span></li>' : '<span class="err-red">' . $language["frontend.global." . $_privacy] . '</span></li>');
        $html .= '<li><label>' . $language["files.menu.featured"] . '</label><span class="conf-green">' . ($_file_feat == 1 ? $language["frontend.global.yes"] : $language["frontend.global.no"]) . '</span></li>';
        $html .= '<li><label>' . $language["files.menu.promoted"] . '</label><span class="conf-green">' . ($_file_promo == 1 ? $language["frontend.global.yes"] : $language["frontend.global.no"]) . '</span></li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '<div class="vs-column fourths">';
        $html .= '<ul class="entry-details">';
        $html .= '<li><label>' . $language["frontend.global.views"] . '</label><span class="conf-green">' . VFiles::numFormat($_file_views) . '</span></li>';
        $html .= '<li><label>' . $language["frontend.global.file.comments"] . '</label><span class="conf-green">' . VFiles::numFormat($_file_comm) . '</span></li>';
        $html .= '<li><label>' . $language["frontend.global.file.responses"] . '</label><span class="conf-green">' . VFiles::numFormat($_file_resp) . '</span></li>';
        $html .= '<li><label>' . $language["frontend.global.file.favorited"] . '</label><span class="conf-green">' . VFiles::numFormat($_file_fav) . '</span></li>';
        $html .= '<li><label>' . $language["frontend.global.file.likes"] . '</label><span class="conf-green">' . VFiles::numFormat($_file_like) . '</span></li>';
        $html .= '<li><label>' . $language["frontend.global.file.dislikes"] . '</label><span class="conf-green">' . VFiles::numFormat($_file_dis) . '</span></li>';
        $html .= '<li><label>' . $language["frontend.global.file.flagged"] . '</label><span class="conf-green">' . VFiles::numFormat($_file_flag) . '</span></li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="left-float"' . ((($_GET["do"] != 'add' and $_POST and self::getPostID() != $db_id) or ($_POST and isset($_GET["a"])) or !$_POST) ? ' style="display: none;"' : null) . ' id="entry-details' . $db_id . '">';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["files.text.file.title"] . '</label>' . $language["frontend.global.required"], 'left-float', 'files_text_file_title', 'backend-text-input wd350', $_title);
        $html .= VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language["files.text.file.descr"] . '</label>', 'left-float', 'files_text_file_descr', 'backend-textarea-input wd350', $_descr);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["files.text.file.tags"] . '</label>' . $language["frontend.global.required"], 'left-float', 'files_text_file_tags', 'backend-text-input wd350', $_tags);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language["files.text.file.categ"] . '</label>' . $language["frontend.global.required"]) . VGenerate::simpleDivWrap('left-float lh20 selector', '', VFiles::fileCategorySelect('backend', $type[0] . $_ct_id, $db_id)));
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language["backend.files.text.upload.date"] . '</label>' . $language["frontend.global.required"], 'left-float', 'files_text_file_datetime', 'backend-text-input wd350', $_file_date);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140' . (($type[0] == 'i' or $type[0] == 'd') ? ' no-display' : null), '<label>' . $language["frontend.global.length"] . '</label>' . $language["frontend.global.required"], 'left-float', 'files_text_file_duration', 'backend-text-input wd350' . (($type[0] == 'i' or $type[0] == 'd') ? ' no-display' : null), $_file_dur);
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= '</div>';
        $html .= VGenerate::declareJS('$(function(){SelectList.init("file_category_' . $db_id . '");});');
        $html .= '<div class="clearfix"></div>';

        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</form></div>';

        $html .= VGenerate::swiperjs('main', 'swiper-' . $db_id);

        return $html;
    }
    /* server paths */
    public function serverPaths()
    {
        global $cfg, $smarty, $class_filter, $class_database, $db;

        $db_id = $class_filter->clr_str($_GET["file_key"]);
        $type  = $class_filter->clr_str($_GET["type"]);

        $sql = sprintf("SELECT
                    A.`embed_src`, A.`file_title`, B.`usr_key`
                    FROM `db_%sfiles` A, `db_accountuser` B
                    WHERE
                    A.`usr_id`=B.`usr_id` AND
                    A.`file_key`='%s'
                    LIMIT 1;", $type, $db_id);

        $rs        = $db->execute($sql);
        $_file_src = $rs->fields["embed_src"];
        $_user_key = $rs->fields["usr_key"];
        $_title    = $rs->fields["file_title"];

        $html = null;

        if ($_file_src == 'local' or $type[0] != 'v') {
            $html .= '<div id="lb-wrapper">';
            $html .= '<div class="entry-list vs-column full">';
            $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
            $html .= '<li>';
            $html .= '<div>';
            $html .= '<div class="responsive-accordion-head-off active">';
            $html .= VGenerate::headingArticle('<span>Server paths: ' . $_title . '</span>', 'icon-' . ($type == 'doc' ? 'file' : ($type == 'blog' ? 'pencil2' : $type)));
            $html .= '</div>';
            $html .= '<div class="responsive-accordion-panel active d-flex d-column border-0" style="display: block !important;">';

            $ds = md5($cfg["global_salt_key"] . $db_id);

            switch ($type[0]) {
                case "v":
                    $_flv        = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.360p.flv';
                    $_mp4h       = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.mp4';
                    $_mp4m       = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.mob.mp4';
                    $_mp4mf      = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $ds . '.mob.mp4';
                    $_fsrc       = $class_database->singleFieldValue('db_' . $type . 'files', 'file_name', 'file_key', $db_id);
                    $_src        = $cfg["upload_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $_fsrc;
                    $_mp4_360p   = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.360p.mp4';
                    $_mp4_360pf  = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $ds . '.360p.mp4';
                    $_mp4_480p   = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.480p.mp4';
                    $_mp4_480pf  = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $ds . '.480p.mp4';
                    $_mp4_720p   = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.720p.mp4';
                    $_mp4_720pf  = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $ds . '.720p.mp4';
                    $_mp4_1080p  = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.1080p.mp4';
                    $_mp4_1080pf = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $ds . '.1080p.mp4';
                    $_mp4_prv    = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . md5($db_id . "_preview") . '.mp4';

                    $html .= file_exists($_flv) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_flv)) : null;
                    $html .= file_exists($_mp4h) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4h)) : null;
                    $html .= file_exists($_mp4_360p) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4_360p)) : null;
                    $html .= file_exists($_mp4_360pf) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4_360pf)) : null;
                    $html .= file_exists($_mp4_480p) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4_480p)) : null;
                    $html .= file_exists($_mp4_480pf) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4_480pf)) : null;
                    $html .= file_exists($_mp4_720p) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4_720p)) : null;
                    $html .= file_exists($_mp4_720pf) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4_720pf)) : null;
                    $html .= file_exists($_mp4_1080p) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4_1080p)) : null;
                    $html .= file_exists($_mp4_1080pf) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4_1080pf)) : null;
                    $html .= file_exists($_mp4m) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4m)) : null;
                    $html .= file_exists($_mp4mf) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4mf)) : null;
                    $html .= file_exists($_mp4_prv) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_mp4_prv)) : null;
                    $html .= file_exists($_src) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_src)) : null;
                    break;
                case "i":
                    $_jpg   = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.jpg';
                    $_jpgpf = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $ds . '.jpg';
                    $_fsrc  = $class_database->singleFieldValue('db_' . $type . 'files', 'file_name', 'file_key', $db_id);
                    $_src   = $cfg["upload_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $_fsrc;

                    $html .= file_exists($_jpg) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_jpg)) : null;
                    $html .= file_exists($_jpgpf) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_jpgpf)) : null;
                    $html .= file_exists($_src) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_src)) : null;
                    break;
                case "a":
                    $_mp3   = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.mp3';
                    $_mp3pf = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $ds . '.mp3';
                    $_mp4   = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.mp4';
                    $_mp4pf = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $ds . '.mp4';
                    $_fsrc  = $class_database->singleFieldValue('db_' . $type . 'files', 'file_name', 'file_key', $db_id);
                    $_src   = $cfg["upload_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $_fsrc;

                    $html .= file_exists($_mp3) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_mp3)) : null;
                    $html .= file_exists($_mp3pf) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_mp3pf)) : null;
                    $html .= file_exists($_mp4) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_mp4)) : null;
                    $html .= file_exists($_mp4pf) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_mp4pf)) : null;
                    $html .= file_exists($_src) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_src)) : null;
                    break;
                case "b":
                    $_tplb = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.tplb';
                    $_fsrc = $class_database->singleFieldValue('db_' . $type . 'files', 'file_name', 'file_key', $db_id);
                    $_src  = $cfg["upload_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $_fsrc;

                    $html .= file_exists($_tplb) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_tplb)) : null;
                    $html .= file_exists($_src) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_src)) : null;
                    break;
                case "d":
                    $_pdf   = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.pdf';
                    $_pdfpf = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $ds . '.pdf';
                    $_swf   = $cfg["media_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $db_id . '.swf';
                    $_fsrc  = $class_database->singleFieldValue('db_' . $type . 'files', 'file_name', 'file_key', $db_id);
                    $_src   = $cfg["upload_files_dir"] . '/' . $_user_key . '/' . $type[0] . '/' . $_fsrc;

                    $html .= file_exists($_pdf) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_pdf)) : null;
                    $html .= file_exists($_pdfpf) ? VGenerate::simpleDivWrap('row no-top-padding', '', VbeSettings::checkPath($_pdfpf)) : null;
                    $html .= file_exists($_swf) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_swf)) : null;
                    $html .= file_exists($_src) ? VGenerate::simpleDivWrap('row', '', VbeSettings::checkPath($_src)) : null;
                    break;
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }
    /* reset thumbnails */
    public function thumbReset($video_id, $preview_reset = false)
    {
        include_once 'f_core/f_classes/class.conversion.php';
        global $class_database, $cfg;

        $user_id = $class_database->singleFieldValue('db_videofiles', 'usr_id', 'file_key', $video_id);
        if ($user_id) {
            $type = 'video';
        } else {
            $user_id = $class_database->singleFieldValue('db_shortfiles', 'usr_id', 'file_key', $video_id);

            if ($user_id) {
                $type = 'short';
            }
        }
        $user_key = $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_id', $user_id);

        if ($user_id == '' or $user_key == '') {
            return;
        }

        $vid = md5($cfg["global_salt_key"] . $video_id);
        $cmd = sprintf("%s %s %s %s %s", $cfg["server_path_php"], $cfg["modules_dir_be"] . '/files_tmb.php', $video_id, $user_key, ($preview_reset ? 2 : 0));
        exec($cmd . ' 2>&1', $output);
    }
    public function conversionLog()
    {
        global $cfg, $class_filter, $smarty;

        $type     = $class_filter->clr_str($_GET["type"]);
        $file_key = $class_filter->clr_str($_GET["file_key"]);

        $log_path = $cfg["logging_dir"] . '/log_conv/*/.' . $type . '_' . $file_key . '.log';

        $html = '<div id="lb-wrapper">';
        $html .= '<div class="entry-list vs-column full">';
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head active no-display">';
        $html .= '<span>' . $log_path . '</span>';
        $html .= '<i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: none;"></i>';
        $html .= '<i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: block;"></i>';
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active d-flex d-column border-0">';
        foreach (glob($log_path) as $filename) {
            if (file_exists($filename)) {
                $html .= "<span>$filename size " . filesize($filename) . "</span><br /><br />";
            }
        }
        if (file_exists($filename)) {
            $html .= nl2br(file_get_contents($filename));
        } else {
            $html .= '.' . $type . '_' . $file_key . '.log not found';
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '<script type="text/javascript">' . $smarty->fetch($cfg["javascript_dir_be"] . '/settings-accordion.js') . '</script>';

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
            "sort-live"     => "files.menu.live",
            "sort-active"   => "files.menu.active",
            "sort-inactive" => "files.menu.inactive",
            "sort-approved" => "files.menu.approved",
            "sort-pending"  => "files.menu.pending",
            "sort-flagged"  => "files.menu.flagged",
            "sort-personal" => "playlist.menu.personal",
            "sort-private"  => "playlist.menu.private",
            "sort-public"   => "playlist.menu.public",
            "sort-longest"  => "files.menu.longest",
            "sort-shortest" => "files.menu.shortest",
            "sort-recent"   => "files.menu.recent",
            "sort-promoted" => "files.menu.promoted",
            "sort-nocateg"  => "files.menu.nocateg",
            "sort-featured" => "files.menu.featured",
            "sort-views"    => "files.menu.viewed",
            "sort-comm"     => "files.menu.commented",
            "sort-resp"     => "files.menu.responded",
            "sort-liked"    => "files.menu.most.liked",
            "sort-disliked" => "files.menu.most.disliked",
            "sort-fav"      => "files.menu.favorited",
            "sort-deleted"  => "files.menu.deleted",
        );
        $menu_arr_icons = array(
            "sort-live"     => "icon-live",
            "sort-active"   => "icon-play",
            "sort-inactive" => "icon-stop",
            "sort-approved" => "icon-check",
            "sort-pending"  => "icon-pause",
            "sort-flagged"  => "icon-flag",
            "sort-personal" => "icon-lock",
            "sort-private"  => "icon-key",
            "sort-public"   => "icon-globe",
            "sort-longest"  => "icon-stopwatch",
            "sort-shortest" => "icon-stopwatch",
            "sort-recent"   => "icon-history",
            "sort-promoted" => "icon-bullhorn",
            "sort-nocateg"  => "icon-list2",
            "sort-featured" => "icon-star",
            "sort-views"    => "icon-eye",
            "sort-comm"     => "icon-comment",
            "sort-resp"     => "icon-comments",
            "sort-liked"    => "icon-thumbs-up",
            "sort-disliked" => "icon-thumbs-down",
            "sort-fav"      => "icon-heart",
            "sort-deleted"  => "icon-times",
        );

        $state_arr = array(
            "sort-live"     => "files.menu.live",
            "sort-active"   => "files.menu.active",
            "sort-inactive" => "files.menu.inactive",
            "sort-approved" => "files.menu.approved",
            "sort-pending"  => "files.menu.pending",
            "sort-promoted" => "files.menu.promoted",
            "sort-featured" => "files.menu.featured",
            "sort-flagged"  => "files.menu.flagged",
            "sort-public"   => "playlist.menu.public",
            "sort-private"  => "playlist.menu.private",
            "sort-personal" => "playlist.menu.personal",
            "sort-nocateg"  => "files.menu.nocateg",
            "sort-deleted"  => "files.menu.deleted",
        );
        $state_arr_icons = array(
            "sort-live"     => "icon-live",
            "sort-active"   => "icon-play",
            "sort-inactive" => "icon-stop",
            "sort-approved" => "icon-check",
            "sort-pending"  => "icon-pause",
            "sort-flagged"  => "icon-flag",
            "sort-personal" => "icon-lock",
            "sort-private"  => "icon-key",
            "sort-public"   => "icon-globe",
            "sort-promoted" => "icon-bullhorn",
            "sort-featured" => "icon-star",
            "sort-nocateg"  => "icon-list2",
            "sort-deleted"  => "icon-times",
        );

        $stats_arr = array(
            "sort-recent"   => "files.menu.recent",
            "sort-views"    => "files.menu.viewed",
            "sort-comm"     => "files.menu.commented",
            "sort-resp"     => "files.menu.responded",
            "sort-liked"    => "files.menu.most.liked",
            "sort-disliked" => "files.menu.most.disliked",
            "sort-fav"      => "files.menu.favorited",
        );

        $stats_arr_icons = array(
            "sort-recent"   => "icon-clock",
            "sort-views"    => "icon-eye",
            "sort-comm"     => "icon-comment",
            "sort-resp"     => "icon-comments",
            "sort-liked"    => "icon-thumbs-up",
            "sort-disliked" => "icon-thumbs-up2",
            "sort-fav"      => "icon-heart",
        );

        $length_arr = array(
            "sort-longest"  => "files.menu.longest",
            "sort-shortest" => "files.menu.shortest",
        );
        $length_arr_icons = array(
            "sort-longest"  => "icon-stopwatch",
            "sort-shortest" => "icon-stopwatch",
        );

        if ($_GET["s"] != 'backend-menu-entry6-sub6') {
            unset($menu_arr["sort-live"]);
            unset($menu_arr_icons["sort-live"]);
            unset($state_arr["sort-live"]);
            unset($state_arr_icons["sort-live"]);
        }
        if ($_GET["s"] == 'backend-menu-entry6-sub2' or $_GET["s"] == 'backend-menu-entry6-sub7' or $_GET["s"] == 'backend-menu-entry6-sub4' or $_GET["s"] == 'backend-menu-entry6-sub5') {
            unset($menu_arr["sort-longest"]);
            unset($menu_arr["sort-shortest"]);
        }
        $int_val  = $_POST[str_replace('-', '_', $div_id) . '_val'] == '' ? (($_GET["do"] != '' and array_key_exists($_GET["do"], $menu_arr)) ? $_GET["do"] : 'sort-recent') : $_POST[str_replace('-', '_', $div_id) . '_val'];
        $menu_top = ($_GET["do"] == '' or $language[$menu_arr[$int_val]] == '') ? $language["files.menu.recent"] : $language[$menu_arr[$int_val]];
        unset($menu_arr[$int_val]);

        if (substr($_GET["s"], 0, 20) == 'backend-menu-entry11') {
//browse by category
            $s_arr = explode("-", $class_filter->clr_str($_GET["s"]));
            $s_t   = substr($s_arr[3], 3);
        }

        $m_count = count($menu_arr);
        $html .= '<div class="left-float menu-drop">';
        $html .= '<div id="' . $type . '" class="dl-menuwrapper">';
        $html .= '<span class="dl-trigger sort-trigger" rel="tooltip" title="' . $language["frontend.global.apply.filter"] . '"><span class="sort-selected">' . $menu_top . '</span></span>';
        $html .= '<ul' . ($m_count < 1 ? ' style="display: none;"' : null) . ' class="dl-menu">';
        $html .= '<li class="count2"><a href="javascript:;" rel="nofollow"><i class="icon-filter"></i> ' . $language["files.menu.status"] . '</a>' . self::listMenuActions_subMenu($state_arr, $state_arr_icons) . '</li>';
        $html .= '<li class="count2"><a href="javascript:;" rel="nofollow"><i class="icon-bars"></i> ' . $language["files.menu.numbers"] . '</a>' . self::listMenuActions_subMenu($stats_arr, $stats_arr_icons) . '</li>';
        $html .= ($_GET["s"] != 'backend-menu-entry6-sub2' and $_GET["s"] == 'backend-menu-entry6-sub7' and $_GET["s"] != 'backend-menu-entry6-sub4' and $_GET["s"] != 'backend-menu-entry6-sub5') ? '<li class="count2"><a href="javascript:;" rel="nofollow"><i class="icon-stopwatch"></i>' . $language["files.menu.length"] . '</a>' . self::listMenuActions_subMenu($length_arr, $length_arr_icons) . '</li>' : null;
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="no-display"><input type="hidden" id="' . $div_id . '-val" name="' . str_replace('-', '_', $div_id) . '_val" value="' . ($int_val) . '" /></div>';
        $html .= VGenerate::declareJS('$(function() { $( "#' . $type . '" ).dlmenu({ animationClasses : { classin : "dl-animate-in-5", classout : "dl-animate-out-5" } }); });');

        return $html;
    }
    /* sub menus */
    public function listMenuActions_subMenu($menu_arr, $icon_arr)
    {
        global $language;

        $div_id = 'file-sort-div';
        $html .= '<ul class="dl-submenu">';
        foreach ($menu_arr as $key => $val) {
            $html .= '<li><a href="javascript:;" rel="nofollow" id="' . $key . '" class="count pointer menu-action-type" onclick="$(\'#' . $div_id . '-val\').val($(this).attr(\'id\'));"><i class="' . $icon_arr[$key] . '"></i> ' . $language[$val] . $u . '</a></li>';
        }
        $html .= '</ul>';

        return $html;
    }
    /* list menu actions, file types */
    public function listTypeActions($type)
    {
        global $cfg, $language, $section, $href, $class_filter, $class_database, $db;

        $btn_id = 'sort-file-type';
        $div_id = 'file-type-div';
        $type   = 'file-type-actions';

        if (substr($_GET["s"], 0, 20) == 'backend-menu-entry11') {
//browse by category
            $s_arr = explode("-", $class_filter->clr_str($_GET["s"]));
            $s_t   = substr($s_arr[3], 3);
        }

        if ($_GET["s"] == 'backend-menu-entry6-sub1' or $s_t[0] == 'v') {
            $menu_arr = array(
                "sort-all" => "backend.files.menu.all",
                "sort-flv" => "backend.files.menu.flv",
                "sort-mp4" => "backend.files.menu.mp4",
            );

            $menu_arr_icons = array(
                "sort-all" => "icon-list",
                "sort-flv" => "icon-laptop",
                "sort-mp4" => "icon-screen",
            );

            $rs = $db->execute(sprintf("SELECT `db_id`, `embed_src` FROM `db_videofiles` WHERE `embed_src`!='local' GROUP BY `embed_src`;"));
            if ($rs->fields["db_id"]) {
                while (!$rs->EOF) {
                    $embed_src = $rs->fields["embed_src"];

                    $menu_arr["sort-" . $embed_src]       = ucfirst($embed_src);
                    $menu_arr_icons["sort-" . $embed_src] = 'icon-code';

                    $rs->MoveNext();
                }
            }
        } elseif ($_GET["s"] == 'backend-menu-entry6-sub7' or $s_t[0] == 's') {
            $menu_arr = array(
                "sort-all" => "backend.files.menu.all",
                "sort-mp4" => "backend.files.menu.mp4",
            );

            $menu_arr_icons = array(
                "sort-all" => "icon-list",
                "sort-mp4" => "icon-screen",
            );

            $rs = $db->execute(sprintf("SELECT `db_id`, `embed_src` FROM `db_shortfiles` WHERE `embed_src`!='local' GROUP BY `embed_src`;"));
            if ($rs->fields["db_id"]) {
                while (!$rs->EOF) {
                    $embed_src = $rs->fields["embed_src"];

                    $menu_arr["sort-" . $embed_src]       = ucfirst($embed_src);
                    $menu_arr_icons["sort-" . $embed_src] = 'icon-code';

                    $rs->MoveNext();
                }
            }
        } elseif ($_GET["s"] == 'backend-menu-entry6-sub4' or $s_t[0] == 'd') {
            $menu_arr = array(
                "sort-all" => "backend.files.menu.all",
                "sort-pdf" => "backend.files.menu.pdf",
            );
            $menu_arr_icons = array(
                "sort-all" => "icon-list",
                "sort-pdf" => "icon-file-pdf",
            );
        }
        $int_val  = $_POST[str_replace('-', '_', $div_id) . '_val'] == '' ? (($_GET["for"] != '' and array_key_exists($_GET["for"], $menu_arr)) ? $_GET["for"] : 'sort-all') : $_POST[str_replace('-', '_', $div_id) . '_val'];
        $menu_top = ($_GET["for"] == '' or $language[$menu_arr[$int_val]] == '') ? $language["backend.files.menu.all"] : $language[$menu_arr[$int_val]];
        unset($menu_arr[$int_val]);

        if (!is_array($menu_arr)) {
            $menu_arr = array();
        }

        $m_count = count($menu_arr);

        $html .= '<div class="menu-drop">';
        $html .= '<div id="' . $type . '" class="dl-menuwrapper">';
        $html .= '<span class="dl-trigger sort-trigger" rel="tooltip" title="' . $language["frontend.global.apply.filter"] . '"><span class="sort-selected">' . $menu_top . '</span></span>';
        $html .= '<ul' . ($m_count < 1 ? ' style="display: none;"' : null) . ' class="dl-menu">';

        if ($m_count > 0) {
            foreach ($menu_arr as $key => $val) {
                $html .= '<li><a href="javascript:;" rel="nofollow" id="' . $key . '" class="file-action menu-action-src count pointer hov-class lh25 wd150 left-padding10 norightborder-bottomborder" onclick="$(\'#' . $div_id . '-val\').val($(this).attr(\'id\'));"><i class="' . $menu_arr_icons[$key] . '"></i> ' . (isset($language[$val]) ? $language[$val] : $val) . '</a></li>';
            }
        }
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '<div class="no-display"><input type="hidden" id="' . $div_id . '-val" name="' . str_replace('-', '_', $div_id) . '_val" value="' . ($int_val) . '" /></div>';
        $html .= VGenerate::declareJS('$(function() { $( "#' . $type . '" ).dlmenu({ animationClasses : { classin : "dl-animate-in-5", classout : "dl-animate-out-5" } }); });');

        return $html;
    }
    /* count files */
    public function fileCount($type)
    {
        global $db, $class_filter, $class_database;

        if (isset($_GET["k"])) {
            return 1;
        }

        $_u = $_GET["u"] != '' ? sprintf("WHERE `usr_id`='%s'", $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $class_filter->clr_str(substr($_GET["u"], 1)))) : null;

        $rs = $db->execute(sprintf("SELECT COUNT(*) AS `total` FROM `db_%sfiles` %s;", $type, $_u));

        return $rs->fields["total"];
    }
    /* count all files (discontinued) */
    public function fileCountAll()
    {
        return;

        global $db, $class_filter, $class_database;

        $_u  = $_GET["u"] != '' ? sprintf(" WHERE `usr_id`='%s'", $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $class_filter->clr_str(substr($_GET["u"], 1)))) : null;
        $sql = sprintf("SELECT
                    ( SELECT COUNT( * ) FROM `db_videofiles`%s) AS v_total,
                    ( SELECT COUNT( * ) FROM `db_imagefiles`%s) AS i_total,
                    ( SELECT COUNT( * ) FROM `db_audiofiles`%s) AS a_total,
                    ( SELECT COUNT( * ) FROM `db_docfiles`%s) AS d_total;", $_u, $_u, $_u, $_u);
        $res = $db->execute($sql);

        $js = '$("#backend-menu-entry6-sub1-count").html("' . $res->fields["v_total"] . '");';
        $js .= '$("#backend-menu-entry6-sub2-count").html("' . $res->fields["i_total"] . '");';
        $js .= '$("#backend-menu-entry6-sub3-count").html("' . $res->fields["a_total"] . '");';
        $js .= '$("#backend-menu-entry6-sub4-count").html("' . $res->fields["d_total"] . '");';

        echo VGenerate::declareJS($js);
    }
    /* checkbox selection query */
    public function cbSQL()
    {
        $db_arr = array();
        foreach ($_POST["current_entry_id"] as $k => $v) {
            $db_arr[] = sprintf("'%s'", $v);
        }

        return $db_q = sprintf("`file_key` IN (%s)", implode(', ', $db_arr));
    }
    /* email subscribers after approving files */
    public function subscriptionMailer()
    {
        global $language, $cfg, $smarty;

        $html = '<div id="lb-wrapper">';
        $html .= '<div class="entry-list vs-column full">';
        $html .= VGenerate::simpleDivWrap('row', 'approve-update', '');
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head-off active">';
        $html .= VGenerate::headingArticle($language["backend.files.text.approve.s0"], 'icon-check');
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active">';
        $html .= '<form id="usr-approve-form" method="post" action="" class="entry-form-class">';
        $html .= '<div class="icheck-box">';
        $html .= '<input type="radio" value="1" name="notify_subscribers"><label>' . $language["backend.files.text.approve.s1"] . '</label><br>';
        $html .= '<input type="radio" value="0" name="notify_subscribers"><label>' . $language["backend.files.text.approve.s2"] . '</label>';
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="hidden" name="cb_in2" id="cb-in2" value="" />');
        $html .= '<div class="clearfix"></div><br>';
        $html .= VGenerate::simpleDivWrap('left-float wd140', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button confirm-approve', '', 0, $language["frontend.global.confirm"]), 'display: inline-block-off;');
        $html .= '</div>';
        $html .= '</form>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';
        /* close popup */
        $js = '$("#usr-approve-form .icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                                //insert: "<div class=\"icheck_line-icon\"></div><label>" + label_text + "</label>"
                        }); });
        ';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* confirm sending notification email after approving */
    public function confirmApprove()
    {
        global $class_filter, $db, $language;

        switch ($_GET["s"]) {
            case "backend-menu-entry6-sub1":$type = 'video';
                break;
            case "backend-menu-entry6-sub2":$type = 'image';
                break;
            case "backend-menu-entry6-sub3":$type = 'audio';
                break;
            case "backend-menu-entry6-sub4":$type = 'doc';
                break;
            case "backend-menu-entry6-sub5":$type = 'blog';
                break;
            case "backend-menu-entry6-sub6":$type = 'live';
                break;
            case "backend-menu-entry6-sub7":$type = 'short';
                break;
        }
        $notif = intval($_POST["notify_subscribers"]);
        $cb    = rawurldecode($class_filter->clr_str($_POST["cb_in2"]));

        if ($notif == 1) {
            $sql = sprintf("SELECT `usr_id`, `file_key` FROM `db_%sfiles` WHERE %s;", $type, $cb);
            $res = $db->execute($sql);
            if ($res->fields["usr_id"]) {
                while (!$res->EOF) {
                    $do_notif = VUpload::notifySubscribers($res->fields["usr_id"], $type, $res->fields["file_key"], 1);

                    $res->MoveNext();
                }
            }
        }
        echo $_POST["notify_subscribers"] != '' ? VGenerate::noticeTpl('', '', $language["notif.success.request"]) : null;
    }
    /* delete files */
    public function fileDelete($p)
    {
        global $cfg, $db, $class_filter, $class_database, $language;

        $db_tbl    = $p[0];
        $sel_count = $p[1];

        if ($cfg["paid_memberships"] == 1 and ($cfg["file_delete_method"] == 2 or $cfg["file_delete_method"] == 4)) {
            $sql_sub = sprintf("SELECT `file_size` FROM `db_%sfiles` WHERE (##KEYS##)", $db_tbl);
        }
        switch ($cfg["file_delete_method"]) {
            case "1":
            case "2":
                $sql = sprintf("UPDATE `db_%sfiles` SET `deleted`='1' WHERE (##KEYS##)", $db_tbl);
                break;
            case "3":
            case "4":
                $sql = sprintf("DELETE `files` FROM `db_%sfiles` AS `files` WHERE (##KEYS##)", $db_tbl);
                break;
        }

        if ($sel_count > 0) {
            $s            = 0;
            $db_whr_field = 'file_key';
            $dbf          = ($cfg["file_delete_method"] == 1 or $cfg["file_delete_method"] == 2) ? null : "`files`.";

            for ($i = 0; $i < $sel_count; $i++) {
                $_key    = $class_filter->clr_str($_POST["current_entry_id"][$i]);
                $usr_id  = $class_database->singleFieldValue('db_' . $db_tbl . 'files', 'usr_id', 'file_key', $_key);
                $usr_key = $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_id', $usr_id);
                $del_arr = array($_key);
                $q       = $dbf . "`" . $db_whr_field . "` = '" . $_key . "'";
                $q1      = $cfg["paid_memberships"] == 1 ? "(" . $dbf . "`" . $db_whr_field . "` = '" . $_key . "' AND `is_subscription`='1')" : null;
                $wh_sql  = $q;
                $f_sql   = str_replace("##KEYS##", $wh_sql, $sql);
                /* delete from subscription used space */
                if ($cfg["paid_memberships"] == 1 and ($cfg["file_delete_method"] == 2 or $cfg["file_delete_method"] == 3 or $cfg["file_delete_method"] == 4)) {
                    $wh_sql1   = $q1;
                    $f_sql_sub = str_replace('`files`.', '', str_replace("##KEYS##", $wh_sql1, $sql_sub));
                    $rsub      = $db->execute($f_sql_sub);

                    if ($rsub) {
                        while (!$rsub->EOF) {
                            $db->execute("UPDATE `db_packusers` SET `pk_usedspace`=`pk_usedspace`-" . $rsub->fields["file_size"] . ", `pk_total_" . $db_tbl . "`=`pk_total_" . $db_tbl . "`-1 WHERE `usr_id`='" . $usr_id . "' LIMIT 1;");
                            @$rsub->MoveNext();
                        }
                    }
                }
                //delete from activity
                $del_act = $db->execute("DELETE FROM `db_useractivity` WHERE `act_type` LIKE '%" . $_key . "%';");
                //delete from server and file owner favorites,playlists,history,liked,watchlist
                $del_fav   = VFiles::clearFavPl($del_arr, $db_tbl, $usr_id, $usr_key);
                $do_action = $db->execute($f_sql);
                if ($db->Affected_Rows() > 0) {
                    $s = $s + 1;
                }
                //delete from other user's entries
                $del_other = VFiles::fileDeleteOther($db_tbl, $_key, array(0 => "favorites", 1 => "fav_list"));
                $del_other = VFiles::fileDeleteOther($db_tbl, $_key, array(0 => "history", 1 => "history_list"));
                $del_other = VFiles::fileDeleteOther($db_tbl, $_key, array(0 => "liked", 1 => "liked_list"));
                $del_other = VFiles::fileDeleteOther($db_tbl, $_key, array(0 => "watchlist", 1 => "watch_list"));
            }
            if ($s > 0) {
                echo VGenerate::noticeTpl('', '', $language["notif.success.request"]);
            }
        }
    }
    /* load blog contents */
    public static function blog_loadContent()
    {
        global $language, $cfg, $class_filter, $db;

        $file_key = $class_filter->clr_str($_GET["f"]);
        $usr_key  = $class_filter->clr_str($_GET["u"]);

        $blog_tpl = $cfg["media_files_dir"] . '/' . $usr_key . '/b/' . $file_key . '.tplb';

        if (file_exists($blog_tpl)) {
            $html = '<span class="blog-edit-id no-display">' . $file_key . '</span>';
            $html .= '<div class="d-editable">' . file_get_contents($blog_tpl) . '</div>';

            $html .= '<script type="text/javascript">var _u = current_url + menu_section + "?be=1";' . ($cfg["live_module"] == 1 ? 'var lm=1;' : null) . ($cfg["video_module"] == 1 ? 'var vm=1;' : null) . ($cfg["short_module"] == 1 ? 'var sm=1;' : null) . ($cfg["image_module"] == 1 ? 'var im=1;' : null) . ($cfg["audio_module"] == 1 ? 'var am=1;' : null) . ($cfg["document_module"] == 1 ? 'var dm=1;' : null) . '</script>';
            $html .= '<script type="text/javascript" src="' . $cfg['javascript_url'] . '/tinymce.init.js"></script>';

            echo $html;
        }

    }
    /* insert media into blogs */
    public static function blog_insertMedia()
    {
        global $language, $cfg, $class_filter, $db, $smarty;

        $type = $class_filter->clr_str($_GET["t"]);

        $js = "$.fancybox.close(); if (typeof($('.blog-media-select li.thumb-selected').html()) != 'undefined') { tinymce.activeEditor.insertContent('[media_" . $type . "_'+$('.blog-media-select li.thumb-selected').attr('rel-key')+']'); }";

        $html = '<div class="lb-margins">
                <article>
                    <h3 class="content-title"><i class="icon-' . ($type == 'doc' ? 'file' : $type) . '"></i> ' . str_replace('##TYPE##', $language["frontend.global." . $type[0]], $language["files.text.insert.url.type"]) . '</h3>
                    <div class="line"></div>
                </article>

                <div class="tabs pltabs tabs-style-topline">
                    <nav>
                        <ul id="pl-tabs">
                            ' . ('<li><a href="#section-own" class="icon icon-upload" rel="nofollow"><span>' . $language["files.text.insert.from.up"] . '</span></a></li>') . '
                            ' . ('<li><a href="#section-search" class="icon icon-link" rel="nofollow"><span>' . $language["files.text.insert.from.url"] . '</span></a></li>') . '
                        </ul>
                    </nav>
                    <div class="content-wrap">
                        <section id="section-own">
                            <div>
                                ' . self::blog_mediaList($type, false) . '
                                <div class="clearfix"></div>
                                <center>
                                    <button value="1" type="submit" class="button-grey search-button form-button save-button save-entry-button" name="save_changes" onclick="' . $js . '">
                                        <span>' . $language["files.text.insert.url.sel"] . '</span>
                                    </button>
                                    <a class="link cancel-trigger" href="javascript:;" onclick="$(\'.close-lightbox\').click()"><span>' . $language["frontend.global.cancel"] . '</span></a>
                                </center>
                            </div>
                        </section>
                        <section id="section-search">
                            <div>
                                <form method="post" action="" class="entry-form-class lb-margins">
                                    <label>' . str_replace('##TYPE##', $language["frontend.global." . $type[0]], $language["files.text.insert.url.text"]) . '</label>
                                    <input name="bulletin_file" class="left-float text-input bulletin_file" id="bulletin-file" type="text" onclick="$(this).focus(); $(this).select();">
                                    <button value="1" type="button" class="button-grey search-button form-button save-button save-entry-button find-url" name="save_changes">
                                        <span>' . $language["frontend.global.findit"] . '</span>
                                    </button>
                                </form>
                                <div id="find-url-response"></div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
            ';

        $ht_js = '(function() {[].slice.call(document.querySelectorAll(".tabs.pltabs")).forEach(function (el) {new CBPFWTabs(el);});})();';
        $ht_js .= '
                $(".find-url").on("click", function() {
                    url = current_url + "' . VHref::getKey('be_files') . '" + "?be=1&b=' . $bkey . '&do=find&t=' . $type . '";
                    $(".fancybox-inner").mask(" ");
                    $.post(url, $(".entry-form-class").serialize(), function(data) {
                        $("#find-url-response").html(data);
                        $(".fancybox-inner").unmask();
                    });
                });
                $( "#bulletin-file" ).on( "keydown", function(event) {
                    if(event.which == 13) {
                        event.preventDefault();
                        $(".find-url").click();
                    }
                });
            ';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $ht_js . '});');

        echo $html;
    }
    /* lists of files to insert into blogs */
    private static function blog_mediaList($type = false, $favorites = false)
    {
        global $language, $cfg, $class_filter, $db;

        $html = null;
        $uid  = 0;
        $for  = !$type ? $class_filter->clr_str($_GET["t"]) : $type;

        $sql = sprintf("SELECT
                        A.`thumb_server`, A.`thumb_cache`, A.`file_key`, A.`file_title`, D.`usr_key`
                        FROM
                        `db_%sfiles` A, `db_accountuser` D
                        WHERE
                        A.`usr_id`=D.`usr_id` AND
                        A.`privacy`!='personal' AND
                        A.`active`='1' AND
                        A.`approved`='1' AND
                        A.`deleted`='0' AND
                        A.`usr_id`>'0'
                        %s
                        GROUP BY A.`file_key`;", $for, $q);

        $rs = $db->execute($sql);

        if ($rs->fields["file_key"]) {
            $s = 1;

            $html .= '<ul class="blog-media-select">';
            while (!$rs->EOF) {
                $title        = $rs->fields["file_title"];
                $file_key     = $rs->fields["file_key"];
                $usr_key      = $rs->fields["usr_key"];
                $thumb_server = $rs->fields["thumb_server"];
                $thumb_cache  = $rs->fields["thumb_cache"];
                $thumb_cache  = $thumb_cache > 1 ? $thumb_cache : null;
                $img_src      = self::thumbnail(array($usr_key, $thumb_cache), $file_key, $thumb_server);
                $thumbnail    = '<img class="mediaThumb" src="' . $img_src . '" alt="' . $title . '" rel="tooltip" title="' . $title . '">';

                $html .= '<li rel-key="' . $file_key . '" class="vs-column fifths' . ($s % 5 == 0 ? ' fit' : null) . '" onclick="$(\'.content-current .blog-media-select li\').removeClass(\'thumb-selected\'); $(this).addClass(\'thumb-selected\')">' . $thumbnail . '</li>';

                $rs->MoveNext();
                $s += 1;
            }
            $html .= '</ul>';
        } else {
            $html .= '<p>' . $language["frontend.global.results.none"] . '</p>';
        }

        return $html;
    }
    /* search by url for inserting into blogs */
    public static function blog_findMedia()
    {
        global $language, $cfg, $class_filter, $class_database, $db;

        $type = $class_filter->clr_str($_GET["t"]);

        $main_len = strlen($cfg["main_url"]);
        /* checking file url */
        $ch_file_url = $class_filter->clr_str($_POST["bulletin_file"]);

        if (substr($ch_file_url, 0, $main_len) == $cfg["main_url"]) {
            $url_arr = parse_url($ch_file_url);

            if ($cfg["file_seo_url"] == 1) {
                $a        = explode("/", $url_arr["path"]);
                $b        = count($a);
                $file_key = $a[$b - 2];
                $tbl      = $a[$b - 3];
                $new_key  = $tbl . "=" . $file_key;
            } else {
                $new_key  = substr($url_arr["query"], 0, 18);
                $new_info = explode("=", $new_key);
                $tbl      = $new_info[0];
                $file_key = $new_info[1];
            }

            switch ($tbl) {
                case "l":$db_tbl = 'live';
                    break;
                case "v":$db_tbl = 'video';
                    break;
                case "s":$db_tbl = 'short';
                    break;
                case "i":$db_tbl = 'image';
                    break;
                case "a":$db_tbl = 'audio';
                    break;
                case "d":$db_tbl = 'doc';
                    break;
                case "b":$db_tbl = 'blog-off';
                    break;
            }
            $m = $tbl == 'd' ? 'document' : $db_tbl;

            if ($db_tbl != $type) {
                echo '<p>' . $language["frontend.global.results.none"] . '</p>';
                exit;
            }

            $_uid = $class_database->singleFieldValue('db_' . $db_tbl . 'files', 'usr_id', 'file_key', $file_key);
            $_sql = sprintf("SELECT
                        B.`usr_key`,
                        C.`thumb_server`, C.`thumb_server`,
                        C.`file_title`
                        FROM
                        `db_accountuser` B, `db_%sfiles` C
                        WHERE
                        C.`usr_id`=B.`usr_id` AND
                        C.`file_key`='%s' AND
                        C.`privacy`%s AND
                        C.`approved`='1' AND
                        C.`deleted`='0' AND
                        C.`active`='1'
                        LIMIT 1;", $db_tbl, $file_key, ($_uid == $_SESSION["USER_ID"] ? "!='personal'" : "='public'"));

            $dbc = $db->execute($_sql);

            if ($cfg[$m . "_module"] == 1 and $_uid > 0 and $dbc->fields["usr_id"] == $_uid) {
                $usr_key      = $dbc->fields["usr_key"];
                $thumb_server = $dbc->fields["thumb_server"];
                $title        = $dbc->fields["file_title"];
                $thumb_cache  = $dbc->fields["thumb_cache"];
                $thumb_cache  = $thumb_cache > 1 ? $thumb_cache : null;

                $img_src = self::thumbnail(array($usr_key, $thumb_cache), $file_key, $thumb_server);

                $thumbnail = '<img class="mediaThumb" src="' . $img_src . '" alt="' . $title . '" rel="tooltip" title="' . $title . '">';

                $js = "$.fancybox.close(); if (typeof($('#find-url-response .vs-column').html()) != 'undefined') { tinymce.activeEditor.insertContent('[media_" . $db_tbl . "_" . $file_key . "]'); }";

                echo $html = '
                            <div class="vs-column fourths">' . $thumbnail . '</div>
                            <div class="vs-column three_fourths fit">
                                <div>' . $title . '</div>
                                <div>
                                    <button value="1" type="button" class="button-grey search-button form-button save-button save-entry-button add-url" name="save_changes" onclick="' . $js . '">
                                        <span>' . $language["files.text.insert.add.to"] . '</span>
                                    </button>
                                </div>
                            </div>
                            <div class="clearfix"></div>
                        ';
            } else {
                echo '<p>' . $language["frontend.global.results.none"] . '</p>';
            }
        } else {
            echo '<p>' . $language["frontend.global.results.none"] . '</p>';
        }
    }
    /* adding new blog */
    public static function newBlog()
    {
        global $language, $cfg, $class_filter, $class_database, $db;

        $for           = isset($_GET["t"]) ? $class_filter->clr_str($_GET["t"]) : 'blog';
        $blog_response = isset($_GET["r"]) ? true : false;

        if ($cfg[$for . "_module"] == 1) {
            if ($_POST) {
                $owner  = $class_filter->clr_str($_POST["add_new_owner"]);
                $ukey   = $class_filter->clr_str($_POST["add_new_owner_key"]);
                $title  = $class_filter->clr_str($_POST["add_new_title"]);
                $descr  = $class_filter->clr_str($_POST["add_new_descr"]);
                $tags   = $class_filter->clr_str($_POST["add_new_tags"]);
                $categ  = (int) $_POST["file_category_0"];
                $usr_id = (int) $class_database->singleFieldValue('db_accountuser', 'usr_id', 'usr_key', $ukey);
                $_p     = unserialize($class_database->singleFieldValue('db_accountuser', 'usr_perm', 'usr_id', $usr_id));
                if (!isset($_p["perm_upload_s"])) {
                    $_p["perm_upload_s"] = 1;
                }

                $error = $_p["perm_upload_" . $for[0]] == '0' ? $language["notif.error.invalid.request"] : null;
                $error = $error != '' ? $error : ($owner == '' ? $language["notif.error.required.field"] . $language["files.text.file.owner"] : ($title == '' ? $language["notif.error.required.field"] . $language["files.text.file.title"] : ($tags == '' ? $language["notif.error.required.field"] . $language["files.text.file.tags"] : ($categ == 0 ? $language["notif.error.required.field"] . $language["files.text.file.categ"] : false))));

                if ($error) {
                    echo VGenerate::noticeTpl('', $error, '');
                } else {
                    $db_tbl_info = 'db_' . $for . 'files';
                    $db_tbl_perm = 'db_' . $for . 'files';

                    $fileext     = 'tplb';
                    $filekey     = VUserinfo::generateRandomString(10);
                    $embedkey    = VUserinfo::generateRandomString(10);
                    $usr_key     = $ukey;
                    $db_approved = 1;

                    if ($usr_id == 0) {
                        echo VGenerate::noticeTpl('', $language["notif.error.invalid.request"], '');
                        exit;
                    }

                    $v_info = array(
                        "usr_id"           => $usr_id,
                        "file_key"         => $filekey,
                        "old_file_key"     => 0,
                        "file_type"        => $fileext,
                        "file_name"        => null,
                        "file_size"        => null,
                        "upload_date"      => date("Y-m-d H:i:s"),
                        "is_subscription"  => 0,
                        "file_views"       => 0,
                        "file_comments"    => 0,
                        "file_responses"   => 0,
                        "file_like"        => 0,
                        "file_dislike"     => 0,
                        "embed_key"        => $embedkey,
                        "file_title"       => $title,
                        "file_description" => $descr,
                        "file_tags"        => VForm::clearTag($tags),
                        "file_category"    => $categ,
                        "approved"         => $db_approved,
                        "privacy"          => "public",
                        "comments"         => "all",
                        "comment_votes"    => 1,
                        "rating"           => 1,
                        "responding"       => "all",
                        "embedding"        => 1,
                        "social"           => 1,
                    );

                    $do_db = $class_database->doInsert($db_tbl_info, $v_info);

                    if ($db->Affected_Rows() > 0) {
                        /* file count */
                        $ct_update = $db->execute(sprintf("UPDATE `db_accountuser` SET `usr_%s_count`=`usr_%s_count`+1 WHERE `usr_id`='%s' LIMIT 1;", $for[0], $for[0], $usr_id));
                        /* activity */
                        $log = ($cfg["activity_logging"] == 1 and $action = new VActivity($usr_id, 0)) ? $action->addTo('log_upload', $for . ':' . $filekey) : null;
                        /* end if broadcast details */
                        if ($for == 'live') {
                            $_p = unserialize($class_database->singleFieldValue('db_accountuser', 'usr_perm', 'usr_id', $usr_id));
                            if ($_p["perm_upload_l"] == '1') {
                                $db->execute(sprintf("UPDATE `db_livefiles` SET `stream_key`='%s', `stream_key_active`='1', `stream_vod`='%s', `stream_chat`='%s', `file_duration`='1' WHERE `file_key`='%s' LIMIT 1;", md5($cfg["global_salt_key"] . $usr_id . SK_INC), $_p["perm_live_vod"], $_p["perm_live_chat"], $filekey));
                            }

                            $tmp_file = str_replace($cfg["main_url"], $cfg["main_dir"], VUseraccount::getProfileImage($usr_id, false));
                            if ($tmp_file && is_file($tmp_file)) {
                                $src_folder = $cfg["media_files_dir"] . '/' . $usr_key . '/t/' . $filekey . '/';
                                $conv       = new VDocument();
                                $conv->log_setup($filekey, false);

                                if ($conv->createThumbs_ffmpeg($src_folder, '1', 320, 180, $filekey, $usr_key, $tmp_file)) {}
                                if ($conv->createThumbs_ffmpeg($src_folder, '0', 640, 360, $filekey, $usr_key, $tmp_file)) {}
                            }

                            $js = '$(document).ready(function(){ $("#add-new-blog-form input, #add-new-blog-form textarea").val(""); });';

                            echo VGenerate::declareJS($js);
                            echo VGenerate::noticeTpl('', '', $language["notif.success.request"]);

                            return;
                        }
                        /* copy blog file */
                        $blog_file = $cfg["ww_templates_dir"] . '/tpl_page/tpl_blog.tpl';
                        $blog_tmb  = $cfg["global_images_dir"] . '/default-blog.png';

                        if (file_exists($blog_file)) {
                            $dst_file = $cfg["media_files_dir"] . '/' . $usr_key . '/b/' . $filekey . '.tplb';
                            $dst_dir  = $cfg["media_files_dir"] . '/' . $usr_key . '/t/' . $filekey;
                            $dst_tmb  = $dst_dir . '/1.jpg';

                            if (copy($blog_file, $dst_file)) {
                                if (mkdir($dst_dir, 0777)) {
                                    copy($blog_tmb, $dst_tmb);

                                    echo VGenerate::declareJS('$(".login-input").val("");');

                                    echo VGenerate::noticeTpl('', '', $language["notif.success.request"]);

                                    $notify = VUpload::notifySubscribers($usr_id, $for, $filekey, 1, $usr_key);
                                }
                            }
                        } else {
                            echo VGenerate::noticeTpl('', $language["notif.error.invalid.request"], '');
                        }
                    }
                }
            } else {
                $c = VGenerate::simpleDivWrap('selector be', '', VFiles::fileCategorySelect('upload'));

                $html = '
                <div class="row">
                    <button name="add_new_blog_btn" id="add-new-blog-btn" onclick="postLoad(current_url + \'' . VHref::getKey('be_files') . '\' + \'?s=backend-menu-entry6-' . ($for == 'live' ? 'sub6' : 'sub5') . '&do=new-' . ($for == 'live' ? 'broadcast' : 'blog') . '&t=' . $for . '\', \'#add-new-blog-form\', \'' . $for . '\')" class="save-entry-button button-grey search-button form-button" type="button" value="1"><span>' . $language["frontend.global.savenew"] . '</span></button>
                    <a class="link cancel-trigger" href="javascript:;" onclick="wrapLoad(current_url + \'' . VHref::getKey('be_files') . '\' + \'?s=backend-menu-entry6-' . ($for == 'live' ? 'sub6' : 'sub5') . '\')"><span>' . $language["frontend.global.cancel"] . '</span></a>
                </div>
                <div class="ct-entry-nowrap left-float left-margin10 wd97p" id="mem-add-new-entry-wrapper">
                <div class="ct-entry-details wdmax left-float bottom-padding10" id="mem-add-new-entry-" style="display: block;">
                    <div class="lb-marginss">
                        <form id="add-new-blog-form" method="post" action="" class="entry-form-class">
                            <div id="add-new-blog-response" class=""></div>
                            <label>' . $language["files.text.file.owner"] . ' ' . $language["frontend.global.required"] . '</label>
                            <input type="text" name="add_new_owner" id="add-new-owner-input" class="login-input">
                            <input type="hidden" name="add_new_owner_key" id="add-new-owner-key-input" class="login-input">
                            <label>' . $language["files.text.file.title"] . ' ' . $language["frontend.global.required"] . '</label>
                            <input type="text" name="add_new_title" id="add-new-title-input" class="login-input">
                            <label>' . $language["files.text.file.descr"] . '</label>
                            <textarea name="add_new_descr" id="add-new-descr-input" class="login-input"></textarea>
                            <label>' . $language["files.text.file.tags"] . ' ' . $language["frontend.global.required"] . '</label>
                            <input type="text" name="add_new_tags" id="add-new-tags-input" class="login-input">
                            <label>' . $language["files.text.file.categ"] . ' ' . $language["frontend.global.required"] . '</label>
                            <div id="add-new-categ-input">
                                <div>' . $c . '</div>
                            </div>
                            <br>
                            <label id="nl">' . $language["frontend.global.required.items"] . '</label>
                        </form>
                    </div>
                </div>
                </div>
                <div class="row">
                    <button name="add_new_blog_btn" id="add-new-blog-btn" onclick="postLoad(current_url + \'' . VHref::getKey('be_files') . '\' + \'?s=backend-menu-entry6-' . ($for == 'live' ? 'sub6' : 'sub5') . '&do=new-' . ($for == 'live' ? 'broadcast' : 'blog') . '&t=' . $for . '\', \'#add-new-blog-form\', \'' . $for . '\')" class="save-entry-button button-grey search-button form-button" type="button" value="1"><span>' . $language["frontend.global.savenew"] . '</span></button>
                    <a class="link cancel-trigger" href="javascript:;" onclick="wrapLoad(current_url + \'' . VHref::getKey('be_files') . '\' + \'?s=backend-menu-entry6-' . ($for == 'live' ? 'sub6' : 'sub5') . '\')"><span>' . $language["frontend.global.cancel"] . '</span></a>
                </div>
                ';

                $html .= '  <script type="text/javascript">
                            $(function() { SelectList.init("file_category_0"); enterSubmit("#add-new-blog-form input", "#add-new-blog-btn"); });

                            $("#add-new-owner-input").autocomplete({
                                type: "post",
                                serviceUrl: current_url + \'' . VHref::getKey('be_files') . '\' + \'?s=backend-menu-entry6-sub5&do=new-blog-autocomplete&t=blog\',
                                onSearchStart: function() {
                                    $("#add-new-owner-key-input").val("");
                                },
                                onSelect: function (suggestion) {
                                    $("#add-new-owner-key-input").val(suggestion.data);
                                }
                            });

                        </script>
                    ';

                echo $html;
            }
        }
    }
    public static function blog_saveContent()
    {
        global $language, $cfg, $class_filter, $db;

        $u = 0;

        $usr_key   = $class_filter->clr_str($_POST["u"]);
        $file_key  = $class_filter->clr_str($_POST["f"]);
        $blog_tpl  = $cfg["media_files_dir"] . '/' . $usr_key . '/b/' . $file_key . '.tplb';
        $blog_html = urldecode($_POST["blog_html"]);

        if (!file_exists($blog_tpl)) {
            touch($blog_tpl);
        }
        if (file_exists($blog_tpl)) {
            if (file_put_contents($blog_tpl, $blog_html)) {
                echo VGenerate::noticeTpl('', '', $language["notif.success.request"]);
            }
        }
    }
    /* generate thumbnail url location */
    private static function thumbnail($usr_key, $file_key, $thumb_server = 0)
    {
        global $language, $cfg, $class_filter, $db;

        $type        = $class_filter->clr_str($_GET["t"]);
        $thumb_cache = null;
        $new_key     = $usr_key;
        if (is_array($usr_key)) {
            $thumb_cache = $usr_key[1];
            $usr_key     = $usr_key[0];
        }
        if ($thumb_server > 0) {
            $expires       = 0;
            $custom_policy = 0;
            $nr            = 1;

            return VGenerate::thumbSigned($type, $file_key, $new_key, $expires, $custom_policy, $nr);
        }

        return $cfg["media_files_url"] . '/' . $usr_key . '/t/' . $file_key . '/1' . $thumb_cache . '.jpg';
    }
}
