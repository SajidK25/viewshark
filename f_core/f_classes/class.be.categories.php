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

class VbeCategories
{
    /* main category details */
    public function mainCategoryDetails($_dsp = '', $entry_id = '', $db_id = '', $ct_state = '', $ct_name = '', $ct_lang = '', $ct_slug = '', $ct_desc = '', $ct_type = '', $ct_featured = '', $ct_sub = '', $ct_menu = '', $ct_icon = '', $ct_ads = '', $ct_banners = '', $ct_index = '', $languages = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            self::processEntry();

            $this_file_type = "this_file_type_" . ((int) $db_id);
            $sub_id         = "sub_id_" . ((int) $db_id);

            $ct_name     = $class_filter->clr_str($_POST['frontend_global_name']);
            $ct_lang     = $_POST['frontend_global_translated'];
            $ct_slug     = $class_filter->clr_str($_POST['frontend_global_slug']);
            $ct_desc     = $class_filter->clr_str($_POST['backend_menu_members_entry1_sub2_entry_desc']);
            $ct_type     = $class_filter->clr_str($_POST[$this_file_type]);
            $ct_icon     = $class_filter->clr_str($_POST['backend_menu_entry2_sub1_categ_icon']);
            $ct_sub      = (int) $_POST[$sub_id];
            $ct_sub      = (int) $_POST['categ_fe_menu'];
            $ct_featured = (int) $_POST['categ_featured'];
            $ct_index    = (int) $_POST['frontend_global_index'];
        }

        return self::categoryDetails($_dsp, $entry_id, $db_id, $ct_state, $ct_name, $ct_lang, $ct_slug, $ct_desc, $ct_type, $ct_featured, $ct_sub, $ct_menu, $ct_icon, $ct_ads, $ct_banners, $ct_index, $languages);
    }
    /* category admin js */
    public static function categJS($_s)
    {
        $js .= '$("a.popup").on("click", function(){';
        $js .= '$("#view-player").replaceWith("");';
        $js .= 'var popupid = $(this).attr("rel");';
        $js .= 'var type = $(this).attr("rel-fkey");';
        $js .= 'var userkey = $(this).attr("rel-ukey");';
        $js .= 'var filehd  = $(this).attr("rel-hd");';

        $js .= 'var mh = 0;';

        $js .= 'if($(this).hasClass("manage-ads")){';
        $js .= 'var url = current_url + "' . VHref::getKey('be_settings') . '" + "?s=' . $_s . '&do=categ-ads&c="+type+"&t="+popupid.replace("popuprel", "");';
        $js .= '} else if($(this).hasClass("manage-banners")) {';
        $js .= 'var url = current_url + "' . VHref::getKey('be_settings') . '" + "?s=' . $_s . '&do=categ-banners&c="+type+"&t="+popupid.replace("popuprel", "");';
        $js .= '}';
        $js .= '$.fancybox.open({ minWidth: "70%", type: "ajax", margin: 10, href: url });';
        $js .= '});';

        return VGenerate::declareJS($js);
    }
    /* update category video/audio/banner ads */
    public static function updateAds($banners = false)
    {
        global $db, $class_filter, $language;

        if (!$_POST) {
            return;
        }

        $ct_id  = (int) $_GET['c'];
        $ct_ads = is_array($_POST['ad_name']) ? serialize($_POST['ad_name']) : '';

        $db->execute(sprintf("UPDATE `db_categories` SET `%s`='%s' WHERE `ct_id`='%s' LIMIT 1;", (!$banners ? 'ct_ads' : 'ct_banners'), $ct_ads, $ct_id));

        if ($db->Affected_Rows() > 0) {
            echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
        } else {
            echo VGenerate::noticeTpl('', $language['notif.error.invalid.request'], '');
        }
    }
    /* manage category video/audio/banner ads */
    public static function manageAds($banners = false)
    {
        global $db, $cfg, $language, $class_database, $class_filter, $smarty;

        if (!isset($_GET['c'])) {
            return;
        }

        $type  = $class_filter->clr_str($_GET['c']);
        $ct_id = (int) $_GET['t'];

        $s  = 0;
        $rs = $db->execute(sprintf("SELECT `ct_name`, `%s` FROM `db_categories` WHERE `ct_id`='%s' LIMIT 1;", (!$banners ? 'ct_ads' : 'ct_banners'), $ct_id));

        if (!$banners) {
            $off = $db->execute(sprintf("SELECT `ad_id`, `ad_name`, `ad_client` FROM `db_%sadentries` WHERE `ad_active`='1';", $cfg[$type . "_player"]));
            $f1  = 'ad_id';
            $f2  = 'ad_name';
        } else {
            $grp = '60,61,62,63,64,65';
            $off = $db->execute(sprintf("SELECT `adv_id`, `adv_name` FROM `db_advbanners` WHERE `adv_active`='1' AND `adv_group` IN (%s);", $grp));
            $f1  = 'adv_id';
            $f2  = 'adv_name';
        }

        $ht = '<div id="lb-wrapper">';
        $ht .= '<div class="entry-list vs-column full">';
        $ht .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $ht .= '<li>';
        $ht .= '<div>';
        $ht .= '<div class="responsive-accordion-head-off active">';
        $ht .= VGenerate::headingArticle((!$banners ? $language["backend.adv.select.for." . $type] : $language['backend.adv.banner.for']) . $rs->fields['ct_name'], 'icon-coin', 1);
        $ht .= '</div>';
        $ht .= '<div class="responsive-accordion-panel active d-column border-0">';
        $ht .= '<div id="ad-update-response' . $ct_id . '"></div>';
        $ht .= '<form id="ad-form' . $ct_id . '" method="post" action="" class="entry-form-class"><input type="hidden" name="submit_ads" value="1">';
        $ht .= '<div class="left-float wd200">';
        if ($off->fields[$f1]) {
            while (!$off->EOF) {
                $_k  = !$banners ? 'ct_ads' : 'ct_banners';
                $_ar = $rs->fields[$_k] != '' ? unserialize($rs->fields[$_k]) : array();
                $ht .= ($s > 0 and ($s % 10) == 0) ? '</div><div class="left-float wd200">' : null;
                $ht .= VGenerate::simpleDivWrap('row top-padding5', '', VGenerate::simpleDivWrap('icheck-box', 'add-rolls', '<input type="checkbox" value="' . $off->fields[$f1] . '" name="ad_name[]" class=""' . (in_array($off->fields[$f1], $_ar) ? ' checked="checked"' : null) . '><label>' . $off->fields[$f2] . '</label>'));

                $s += 1;
                $off->MoveNext();
            }
        } else {
            $ht .= '<p>' . (!$banners ? $language['backend.menu.no.video.banners'] : $language['backend.menu.no.categ.banners']) . '</p>';
        }
        $ht .= '</div>';

        $ht .= VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey update-entry search-button form-button trg-update', '', $ct_id, '<span>' . $language['frontend.global.saveupdate'] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');
        $ht .= '<a class="ad-update left-align top-padding7 no-display" href="javascript:;" rel-key="' . $ct_id . '">' . $language['frontend.global.saveupdate'] . '</a>';
        $ht .= '</form>';
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
        $_js .= 'var ct_id = $(this).attr("rel-key");';
        $_js .= '$(".fancybox-inner").mask(" ");';
        $_js .= '$.post(current_url + "' . VHref::getKey('be_settings') . '" + "?s=' . $class_filter->clr_str($_GET['s']) . '&do=' . (!$banners ? 'ad-update' : 'banner-update') . '&c="+ct_id, $("#ad-form"+ct_id).serialize(), function(data) {';
        $_js .= '$("#ad-update-response"+ct_id).html(data);';
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
    /* category details edit */
    public function categoryDetails($_dsp = '', $entry_id = '', $db_id = '', $ct_state = '', $ct_name = '', $ct_lang = '', $ct_slug = '', $ct_desc = '', $ct_type = '', $ct_featured = '', $ct_sub = '', $ct_menu = '', $ct_icon = '', $ct_ads = '', $ct_banners = '', $ct_index = '', $languages = '')
    {
        global $class_filter, $language;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_date = date('D, m/d/Y, H:i:s A', strtotime($dc_date));
        $_sct  = 'discount_codes';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block-off;') : null;

        switch ($_GET['s']) {
            case "backend-menu-entry2-sub5l":$ct_type = 'live';
                break;
            case "backend-menu-entry2-sub5v":$ct_type = 'video';
                break;
            case "backend-menu-entry2-sub5i":$ct_type = 'image';
                break;
            case "backend-menu-entry2-sub5a":$ct_type = 'audio';
                break;
            case "backend-menu-entry2-sub5d":$ct_type = 'doc';
                break;
            case "backend-menu-entry2-sub5c":$ct_type = 'channel';
                break;
            case "backend-menu-entry2-sub5b":$ct_type = 'blog';
                break;
            default:$ct_type = '';
                $opt             = '<option value="live">' . $language['frontend.global.l'] . '</option><option value="video">' . $language['frontend.global.v'] . '</option><option value="image">' . $language['frontend.global.i'] . '</option><option value="audio">' . $language['frontend.global.a'] . '</option><option value="doc">' . $language['frontend.global.d'] . '</option><option value="blog">' . $language['frontend.global.b'] . '</option><option value="channel">' . $language['frontend.global.c'] . '</option>';
                break;
        }

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        if ($_GET['do'] != 'add') {
            $tabs = '<div class="swiper-ph swiper-ph-main"></div>';
            $tabs .= '<div class="swiper-top swiper-top-main" id="swiper-' . (int) $db_id . '" style="display:' . VGenerate::ssd() . '">';
            $tabs .= '<div class="swiper-button-prev swiper-button-prev-main"' . VGenerate::sso() . '></div>';
            $tabs .= '<div class="swiper-button-next swiper-button-next-main"' . VGenerate::sso() . '></div>';
            $tabs .= '<div class="swiper swiper-main">';
            $tabs .= '<div class="swiper-wrapper">';
            $tabs .= '<div class="swiper-slide swiper-slide-current"><a class="icon icon-pencil manage-details" rel="popuprel' . $db_id . '" rel-fkey="' . $ct_type . '" href="javascript:;" onclick="$(this).parent().addClass(\'active\'); $(this).parent().next().removeClass(\'active\'); $(\'#categ-lang-' . (int) $db_id . '\').hide(); $(\'#categ-details-' . (int) $db_id . '\').show(); $(this).parent().next().removeClass(\'swiper-slide-current\'); $(this).parent().addClass(\'swiper-slide-current\');"><span>' . $language['backend.menu.fm.categ.g'] . '</span></a></div>';
            $tabs .= '<div class="swiper-slide"><a class="icon icon-globe manage-lang" rel="popuprel' . $db_id . '" rel-fkey="' . $ct_type . '" href="javascript:;" onclick="$(this).parent().addClass(\'active\'); $(this).parent().prev().removeClass(\'active\'); $(\'#categ-lang-' . (int) $db_id . '\').show(); $(\'#categ-details-' . (int) $db_id . '\').hide(); $(this).parent().prev().removeClass(\'swiper-slide-current\'); $(this).parent().addClass(\'swiper-slide-current\');"><span>' . $language['backend.menu.fm.categ.t'] . '</span></a></div>';
            $tabs .= ($ct_type == 'video' or $ct_type == 'audio' or $ct_type == 'live') ? '<div class="swiper-slide"><a class="icon icon-coin popup manage-ads" rel="popuprel' . $db_id . '" rel-fkey="' . $ct_type . '" href="javascript:;"><span>' . $language["backend.files.text.ads." . ($ct_type == 'audio' ? 'audio' : 'video')] . '</span></a></div>' : null;
            $tabs .= $ct_type != 'channel' ? '<div class="swiper-slide"><a class="icon icon-coin popup manage-banners" rel="popuprel' . $db_id . '" rel-fkey="' . $ct_type . '" href="javascript:;"><span>' . $language['backend.files.text.banner'] . '</span></a></div>' : null;
            $tabs .= '</div></div></div>';

            $html .= '<div class="tabs tabs-style-topline">';
            $html .= $tabs;
            $html .= '<nav class="no-display"><ul class="ul-no-list ucateg-list left-float top-bottom-padding bottom-border">';
            $html .= '<li class="active"><a class="icon icon-pencil manage-details" rel="popuprel' . $db_id . '" rel-fkey="' . $ct_type . '" href="javascript:;" onclick="$(this).parent().addClass(\'active\'); $(this).parent().next().removeClass(\'active\'); $(\'#categ-lang-' . (int) $db_id . '\').hide(); $(\'#categ-details-' . (int) $db_id . '\').show();"><span>' . $language['backend.menu.fm.categ.g'] . '</span></a></li>';
            $html .= '<li class=""><a class="icon icon-globe manage-lang" rel="popuprel' . $db_id . '" rel-fkey="' . $ct_type . '" href="javascript:;" onclick="$(this).parent().addClass(\'active\'); $(this).parent().prev().removeClass(\'active\'); $(\'#categ-lang-' . (int) $db_id . '\').show(); $(\'#categ-details-' . (int) $db_id . '\').hide();"><span>' . $language['backend.menu.fm.categ.t'] . '</span></a></li>';
            if ($ct_type == 'video' or $ct_type == 'audio' or $ct_type == 'live') {
                $html .= '<li class=""><a class="icon icon-coin popup manage-ads" rel="popuprel' . $db_id . '" rel-fkey="' . $ct_type . '" href="javascript:;"><span>' . $language["backend.files.text.ads." . ($ct_type == 'audio' ? 'audio' : 'video')] . '</span></a></li>';
            }
            if ($ct_type != 'channel') {
                $html .= '<li class=""><a class="icon icon-coin popup manage-banners" rel="popuprel' . $db_id . '" rel-fkey="' . $ct_type . '" href="javascript:;"><span>' . $language['backend.files.text.banner'] . '</span></a></li>';
            }
            $html .= '</ul></nav>';
            $html .= '</div>';
        }
        $html .= '<div id="categ-details-' . (int) $db_id . '">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($ct_state == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.index'] . '</label>' . $language['frontend.global.required'], 'ct-index', 'frontend_global_index', 'backend-text-input wd350', $ct_index);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.name'] . '</label>' . $language['frontend.global.required'], 'ct-name', 'frontend_global_name', 'backend-text-input wd350', $ct_name);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.slug'] . '</label>' . $language['frontend.global.required'], 'left-float', 'frontend_global_slug', 'backend-text-input wd350', $ct_slug);
        $html .= '<div class="no-display-off">' . VGenerate::sigleInputEntry('textarea', 'left-float lh20 wd140', '<label>' . $language['backend.menu.members.entry1.sub2.entry.desc'] . '</label>', 'left-float', 'backend_menu_members_entry1_sub2_entry_desc', 'backend-textarea-input wd350', $ct_desc) . '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.menu.entry2.sub1.categ.type'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', '<select name="this_file_type_' . (int) $db_id . '" class="select-input wd100">' . ($ct_type != '' ? '<option value="' . $ct_type . '">' . $language["frontend.global." . $ct_type[0]] . '</option>' : $opt) . '</select>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.menu.entry2.sub1.categ.parent'] . '</label>') . VGenerate::simpleDivWrap('left-float lh20 selector', '', self::categList((int) $db_id, $ct_sub, $ct_type)));
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.menu.entry2.sub1.categ.icon'] . '</label>', 'left-float', 'backend_menu_entry2_sub1_categ_icon', 'backend-text-input wd350', $ct_icon);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="categ_fe_menu" class=""' . ($ct_menu == 1 ? ' checked="checked"' : null) . '><label>' . $language['backend.menu.entry2.sub1.categ.fe.menu'] . '</label>'));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="categ_featured" class=""' . ($ct_featured == 1 ? ' checked="checked"' : null) . '><label>' . $language['backend.menu.entry2.sub1.categ.fe.feat'] . '</label>'));
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language['frontend.global.required.items']);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '<div id="categ-lang-' . (int) $db_id . '" style="display: none;">';
        $html .= self::categLang($ct_name, (int) $db_id, $ct_lang, $languages);
        $html .= '</div>';
        $html .= '</form></div>';

        $html .= VGenerate::declareJS('$(function(){SelectList.init("this_file_type_' . ((int) $db_id) . '");});');
        $html .= VGenerate::declareJS('$(function(){SelectList.init("sub_id_' . ((int) $db_id) . '");});');
        $html .= VGenerate::swiperjs('main', 'swiper-' . (int) $db_id);

        return $html;
    }
    /* category names, language inputs */
    public static function categLang($ct_name, $db_id, $ct_lang, $lang)
    {
        global $language;

        $html = null;
        $html .= '<div id="categ-lang-response-' . $db_id . '"></div>';

        if ($_GET['do'] != 'add') {
            foreach ($lang as $l) {
                $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $l['lang_name'] . '</label>', ($l['lang_id'] == 'en_US' ? 'lang-name' : ''), 'categ_' . $l['lang_id'], 'backend-text-input wd350', ($l['lang_id'] == 'en_US' ? $ct_name : $ct_lang[$l['lang_id']]));
            }
        }

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-ct-entry' : 'update-ct-entry'), '', 'categ' . $db_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block-off;') : null;
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= '
            <script type="text/javascript">
                $(document).ready(function() {
                $("#btn-categ' . $db_id . '-save_changes").click(function() {
                    var url = current_url + "' . VHref::getKey('be_settings') . '" + "?s=1&do=categ-lang";
                    $("#categ-lang-' . $db_id . '").mask("");
                    $.post(url, $("#categ-entry-form' . $db_id . '").serialize(), function(data){
                        $("#categ-lang-response-' . $db_id . '").html(data);
                        $("#categ-lang-' . $db_id . '").unmask();
                    });
                });
                $("#categ-entry-form' . $db_id . ' .lang-name > input").keyup(function() {
                    $("#categ-entry-form' . $db_id . ' .ct-name > input").val($(this).val());
                });
                $("#categ-entry-form' . $db_id . ' .ct-name > input").keyup(function() {
                    $("#categ-entry-form' . $db_id . ' .lang-name > input").val($(this).val());
                });
                });
            </script>
            ';

        return $html;
    }
    /* save category translations */
    public static function saveCategLang()
    {
        global $db, $class_filter, $language;
        if (!$_POST) {
            return;
        }

        $rs = $db->execute("SELECT `db_id`, `lang_id`, `lang_name` FROM `db_languages` WHERE `lang_active`='1' ORDER BY `lang_default` DESC;");

        if ($rs->fields['db_id']) {
            $t = array();
            while (!$rs->EOF) {
                $lang_id = $rs->fields['lang_id'];
                $ct_id   = (int) $_POST['hc_id'];
                $ct_name = $class_filter->clr_str($_POST["categ_" . $lang_id]);

                if ($lang_id == 'en_US') {
                } else {
                    $t[$lang_id] = $ct_name;
                }
                $rs->MoveNext();
            }
            $db->execute(sprintf("UPDATE `db_categories` SET `ct_name`='%s', `ct_lang`='%s' WHERE `ct_id`='%s' LIMIT 1;", $class_filter->clr_str($_POST['categ_en_US']), serialize($t), $ct_id));

            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            } else {
                echo VGenerate::noticeTpl('', $language['notif.error.invalid.request'], '');
            }
        }
    }
    /* list for subcateg selection */
    public function categList($db_id, $sub_id, $type)
    {
        global $db;

        $sel = null;
        $op  = $type != '' ? '=' : '!=';
        $res = $db->execute(sprintf("SELECT `ct_id`, `sub_id`, `ct_name` FROM `db_categories` WHERE `ct_type`%s'%s' AND `sub_id`='0' AND `ct_active`='1';", $op, $type));

        $sel .= '<select name="sub_id_' . (int) $db_id . '" class="select-input wd350">';
        $sel .= '<option value="0">---</option>';
        if ($res->fields['ct_id']) {
            while (!$res->EOF) {
                $ct_id = $res->fields['ct_id'];
                $sel .= ($db_id != $ct_id) ? '<option value="' . $ct_id . '"' . ($ct_id == $sub_id ? ' selected="selected"' : null) . '>' . $res->fields['ct_name'] . '</option>' : null;
                $res->MoveNext();
            }
        }
        $sel .= '</select>';

        return $sel;
    }

    /* processing entry */
    public function processEntry()
    {
        global $class_database, $db, $language;

        $form           = VArraySection::getArray("public_categories");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $ct_id       = intval($_POST['hc_id']);
            $ct_name     = $form[0]['ct_name'];
            $ct_lang     = $form[0]['ct_lang'];
            $ct_slug     = $form[0]['ct_slug'];
            $ct_descr    = $form[0]['ct_descr'];
            $ct_type     = $form[0]['ct_type'];
            $ct_sub      = $form[0]['sub_id'];
            $ct_menu     = $form[0]['ct_menu'];
            $ct_icon     = $form[0]['ct_icon'];
            $ct_featured = $form[0]['ct_featured'];
            $ct_index    = $form[0]['ct_index'];
            switch ($_GET['do']) {
                case "update":
                    $sql = sprintf("UPDATE `db_categories` SET `ct_index`='%s', `ct_featured`='%s', `ct_icon`='%s', `ct_menu`='%s', `sub_id`='%s', `ct_name`='%s', `ct_lang`='%s', `ct_slug`='%s', `ct_descr`='%s', `ct_type`='%s' WHERE `ct_id`='%s' LIMIT 1;", $ct_index, $ct_featured, $ct_icon, $ct_menu, $ct_sub, $ct_name, $ct_lang, $ct_slug, $ct_descr, $ct_type, $ct_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_categories', $form[0]);
                    break;
            }

            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            }
        }
    }
}
