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

class VPagination
{
    public $items_per_page;
    public $items_total;
    public $current_page;
    public $num_pages;
    public $mid_range;
    public $low;
    public $high;
    public $limit;
    public $return;
    public $default_ipp = 10;
    public $querystring;
    public $paging_link;

    /* pagination setup */
    public function Pagination_OLD()
    {
        global $class_filter;

        $this->current_page   = 1;
        $this->mid_range      = 5;
        $this->items_per_page = (!empty($_GET['ipp'])) ? (int) $_GET['ipp'] : $this->default_ipp;
    }
    /* pagination calcuations */
    public function paginate()
    {
        global $cfg, $language, $class_filter, $upage_id, $ch_cfg, $section, $href;

        if ($_GET['ipp'] == 'all') {
            $this->num_pages      = ceil($this->items_total / $this->default_ipp);
            $this->items_per_page = $this->default_ipp;
        } else {
            if (!is_numeric($this->items_per_page) or $this->items_per_page <= 0) //{// this bracket
            {
                $this->items_per_page = $this->default_ipp;
            }

            $this->num_pages = ceil($this->items_total / $this->items_per_page);
        }

        $this->current_page = (int) $_GET['page']; // must be numeric > 0
        if ($this->current_page < 1 or !is_numeric($this->current_page)) {
            $this->current_page = 1;
        }

        if ($this->current_page > $this->num_pages) {
            $this->current_page = $this->num_pages;
        }

        $prev_page = $this->current_page - 1;
        $next_page = $this->current_page + 1;

        if ($_GET) {
            $args = explode("&", $class_filter->clr_str($_SERVER['QUERY_STRING']));
            foreach ($args as $arg) {
                $keyval = explode("=", $arg);
                if ($keyval[0] != "page" and $keyval[0] != "ipp") {
                    $this->querystring .= "&" . $arg;
                }

            }
        }
        $this->paging_link = ($section == $href['be_files'] and $_GET['u'] != '') ? '&u=' . $class_filter->clr_str($_GET['u']) : null;
        $this->paging_link = ($section == $href['be_members'] and $_GET['u'] != '') ? ($_GET['do'] != '' ? '&do=' . $class_filter->clr_str($_GET['do']) : null) . '&u=' . $class_filter->clr_str($_GET['u']) . ($_GET['a'] != '' ? '&a=' . $class_filter->clr_str($_GET['a']) : null) : $this->paging_link;
        $this->paging_link .= ($section == $href['be_files'] and $_GET['for'] != '') ? '&for=' . $class_filter->clr_str($_GET['for']) : null;
        $this->paging_link .= ($section == $href['playlists'] and $_GET['sort'] != '') ? '?sort=' . $class_filter->clr_str($_GET['sort']) : ((($section == $href['playlists']) and $_GET['sort'] == '') ? '?sort=sort-recent' : null);
        $this->paging_link .= ($section == $href['channels'] and $_GET['sort'] != '') ? '?s=' . $class_filter->clr_str($_GET['s']) . '&sort=' . $class_filter->clr_str($_GET['sort']) : (($section == $href['channels'] and $_GET['sort'] == '') ? '?s=browse-all-entries&sort=sort-recent' : null);
        $this->paging_link .= (($_GET['do'] != '' and
            ($_GET['do'] != 'cb-disable' and
                $_GET['do'] != 'cb-delete' and
                $_GET['do'] != 'cb-label-add' and
                $_GET['do'] != 'cb-label-clear' and
                $_GET['do'] != 'featured-list' and
                $_GET['do'] != 'channel-sort' and
                $_GET['do'] != 'channel-browse' and
                $_GET['do'] != 'channel-list' and
                $_GET['do'] != 'ct-load' and
                $_GET['do'] != 'user-activity')) ? '&do=' . $class_filter->clr_str($_GET['do']) : null) . (($_GET['for'] != '' and $_GET['do'] != 'ct-load') ? '&for=' . $class_filter->clr_str($_GET['for']) : null) . ($_GET['sq'] != '' ? '&sq=' . $class_filter->clr_str($_GET['sq']) : null);

        if ($section == $href['files'] or $section == $href['uploads'] or $section == $href['playlists'] or $section == $href['subscriptions'] or $section == $href['following']) {
            $cjs = 'var view_mode_type = $(".view-mode-type.active").attr("id").replace("view-mode-", "");
                if (jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry7" || jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry8") {
                    var cr = $("ul.cr-tabs li.tab-current a").attr("href").split("-");
                    var type = cr[1];
                    var idnr = null;
                                } else {
                                    var id = jQuery(".content-wrap .content-current .main-view-mode-"+view_mode_type+".active").attr("id");
                                    var type = jQuery(".content-wrap .content-current .main-view-mode-"+view_mode_type+".active").val();

                                    var nr = id.split("-");
                                    var idnr = nr[3];
                                }

                                var type_all = type + "-" + view_mode_type;

                                if (jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry7") {
                                    type_all += "-comments";
                                } else if (jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry8") {
                                    type_all += "-responses";
                                }

                                var c_url   = current_url + menu_section + "?s=" + $(".menu-panel-entry-active").attr("id");

                                if (jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry6" || $("#section-playlists").hasClass("active")) {//isplaylist
                                    //var m = idnr == 1 ? 4 : (idnr == 3 ? 6 : 4);
                                    var m = idnr == 1 ? 4 : (idnr == 3 ? 6 : (idnr == 2 ? 5 : 4));
                                    c_url += "&p=0&m="+m+"&sort="+type+"&t="+view_mode_type;

                                    if ($("#section-playlists").hasClass("active")) {//browse playlists section
                                        c_url += "&pp=1";
                                    }
                                } else {
                                    if (jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry7" || jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry8") {
                                        c_url += "&do=cr-"+cr[1]+"&t="+view_mode_type;
                                    } else {
                                        c_url += "&p=0&m="+idnr+"&sort="+type+"&t="+view_mode_type;
                                    }
                                }


                                if (typeof($("#sq").val()) != "undefined" && $("#sq").val().length > 3) {
                                    c_url += "&sq=" + $("#sq").val();
                                }

                                if (jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry7" || jQuery(".menu-panel-entry-active").attr("id") == "file-menu-entry8") {
                                    p_str = "#main-view-mode-" + type_all + "-list";
                                    p_str = "#siteContent";
                                } else {
                                    p_str = "#main-view-mode-" + idnr + "-" + type_all + "-list";
                                }

                                p_nr = parseInt($("#main-view-mode-" + idnr + "-" + type_all + "-list .pag-wrap a.current").html());

                                $("#edit-mode, #select-mode").removeClass("active");
';

        }

        if ($this->num_pages > 10) {
            $this->return .= ($this->current_page != 1 and $this->items_total >= 10) ? '<a class="paginate paginate-prev">' . $language['frontend.global.previous'] . '</a> ' : '<span class="inactive">' . $language['frontend.global.previous'] . '</span> ';

            if ($section == $href['files'] or $section == $href['playlists'] or $section == $href['subscriptions'] or $section == $href['following']) {
                $this->return .= VGenerate::declareJS('$(document).on("click", ".paginate-prev", function(e){ ' . $cjs . ' var p_url = "' . $this->paging_link . '"; p_url = (("' . $section . '" == "' . $href['be_members'] . '" && typeof($(".fancybox-wrap").html()) == "undefined" && "' . $_GET['do'] . '" == "user-activity") ? "&do="+$(".sort-selected").attr("id")+"&sq="+$("#sq").val() : p_url); var paginate_prev = c_url + p_url + "&page="+(p_nr - 1)+"&ipp=' . $this->items_per_page . '"; e.stopImmediatePropagation();  wrapLoad(paginate_prev, "page"); });');
            } else {
                $this->return .= VGenerate::declareJS('$(".paginate-prev").unbind().on("click", function(){ var p_url = "' . $this->paging_link . '"; p_url = (("' . $section . '" == "' . $href['be_members'] . '" && typeof($(".fancybox-wrap").html()) == "undefined" && "' . $_GET['do'] . '" == "user-activity") ? "&do="+$(".sort-selected").attr("id")+"&sq="+$("#sq").val() : p_url); var paginate_prev = c_url + p_url + "&page=' . $prev_page . '&ipp=' . $this->items_per_page . '"; wrapLoad(paginate_prev); });');
            }

            $this->start_range = $this->current_page - floor($this->mid_range / 2);
            $this->end_range   = $this->current_page + floor($this->mid_range / 2);

            if ($this->start_range <= 0) {
                $this->end_range += abs($this->start_range) + 1;
                $this->start_range = 1;
            }

            if ($this->end_range > $this->num_pages) {
                $this->start_range -= $this->end_range - $this->num_pages;
                $this->end_range = $this->num_pages;
            }
            $this->range = range($this->start_range, $this->end_range);

            for ($i = 1; $i <= $this->num_pages; $i++) {
                if ($this->range[0] > 2 and $i == $this->range[0]) {
                    $this->return .= $language['frontend.global.pagingdots'];
                }

                // loop through all pages. if first, last, or in range, display
                if ($i == 1 or $i == $this->num_pages or in_array($i, $this->range)) {
                    $this->return .= ($i == $this->current_page and $_GET['page'] != 'all') ? '<a class="current paginate-goto' . $i . ($i == $this->current_page ? '-nb' : null) . '" title="' . $language['frontend.global.gotopage'] . ' ' . $i . ' ' . $language['frontend.global.resultsof'] . ' ' . $this->num_pages . '">' . $i . '</a> ' : '<a class="paginate paginate-goto' . $i . '" title="' . $language['frontend.global.gotopage'] . ' ' . $i . ' ' . $language['frontend.global.resultsof'] . ' ' . $this->num_pages . '">' . $i . '</a> ';

                    if ($section == $href['files'] or $section == $href['playlists'] or $section == $href['subscriptions'] or $section == $href['following']) {
                        $js .= '$(document).on("click", ".paginate-goto' . $i . '", function(e){ ' . $cjs . ' var p_url = "' . $this->paging_link . '"; p_url = (("' . $section . '" == "' . $href['be_members'] . '" && typeof($(".fancybox-wrap").html()) == "undefined" && "' . $_GET['do'] . '" == "user-activity") ? "&do="+$(".sort-selected").attr("id")+"&sq="+$("#sq").val() : p_url); var paginate_goto = c_url + p_url + "&page=' . $i . '&ipp=' . $this->items_per_page . '"; e.stopImmediatePropagation(); wrapLoad(paginate_goto, "page"); });';
                    } else {
                        $js .= '$(".paginate-goto' . $i . '").unbind().click(function(){ var p_url = "' . $this->paging_link . '"; p_url = (("' . $section . '" == "' . $href['be_members'] . '" && typeof($(".fancybox-wrap").html()) == "undefined" && "' . $_GET['do'] . '" == "user-activity") ? "&do="+$(".sort-selected").attr("id")+"&sq="+$("#sq").val() : p_url); var paginate_goto = c_url + p_url + "&page=' . $i . '&ipp=' . $this->items_per_page . '"; wrapLoad(paginate_goto); });';
                    }
                }
                if ($this->range[$this->mid_range - 1] < $this->num_pages - 1 and $i == $this->range[$this->mid_range - 1]) {
                    $this->return .= $language['frontend.global.pagingdots'];
                }

            }
            $this->return .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');
            $this->return .= (($this->current_page != $this->num_pages and $this->items_total >= 10) and ($_GET['page'] != 'all')) ? '<a class="paginate paginate-next">' . $language['frontend.global.next'] . '</a>' : '<span class="inactive">' . $language['frontend.global.next'] . '</span>';

            if ($section == $href['files'] or $section == $href['playlists'] or $section == $href['subscriptions'] or $section == $href['following']) {
                $this->return .= VGenerate::DeclareJS('$(document).on("click", ".paginate-next", function(e){ ' . $cjs . ' var p_url = "' . $this->paging_link . '"; p_url = (("' . $section . '" == "' . $href['be_members'] . '" && typeof($(".fancybox-wrap").html()) == "undefined" && "' . $_GET['do'] . '" == "user-activity") ? "&do="+$(".sort-selected").attr("id")+"&sq="+$("#sq").val() : p_url); var paginate_next = c_url + p_url + "&page="+(p_nr + 1)+"&ipp=' . $this->items_per_page . '"; e.stopImmediatePropagation(); wrapLoad(paginate_next, "page"); });');
            } else {
                $this->return .= VGenerate::DeclareJS('$(".paginate-next").unbind().on("click", function(){ var p_url = "' . $this->paging_link . '"; p_url = (("' . $section . '" == "' . $href['be_members'] . '" && typeof($(".fancybox-wrap").html()) == "undefined" && "' . $_GET['do'] . '" == "user-activity") ? "&do="+$(".sort-selected").attr("id")+"&sq="+$("#sq").val() : p_url); var paginate_next = c_url + p_url + "&page=' . $next_page . '&ipp=' . $this->items_per_page . '"; wrapLoad(paginate_next); });');
            }
        } else {
            for ($i = 1; $i <= $this->num_pages; $i++) {
                $this->return .= ($i == $this->current_page) ? '<a class="current paginate-goto' . $i . '-nb">' . $i . '</a> ' : '<a class="paginate paginate-goto' . $i . '" title="' . $language['frontend.global.gotopage'] . ' ' . $i . ' ' . $language['frontend.global.resultsof'] . ' ' . $this->num_pages . '">' . $i . '</a> ';
                if ($section == $href['files'] or $section == $href['playlists'] or $section == $href['subscriptions'] or $section == $href['following']) {
                    $js .= '$(document).on("click", ".paginate-goto' . $i . '", function(e){ ' . $cjs . ' var p_url = "' . $this->paging_link . '"; p_url = (("' . $section . '" == "' . $href['be_members'] . '" && typeof($(".fancybox-wrap").html()) == "undefined" && "' . $_GET['do'] . '" == "user-activity") ? "&do="+$(".sort-selected").attr("id")+"&sq="+$("#sq").val() : p_url); var a;var paginate_goto = c_url + p_url + "&page=' . $i . '&ipp=' . (($section == $href['be_members'] and $_GET['do'] == 'user-activity') ? "" : $this->items_per_page) . '"; e.stopImmediatePropagation(); wrapLoad(paginate_goto, "page"); });';
                } else {
                    $js .= '$(".paginate-goto' . $i . '").unbind().click(function(){ var p_url = "' . $this->paging_link . '"; p_url = (("' . $section . '" == "' . $href['be_members'] . '" && typeof($(".fancybox-wrap").html()) == "undefined" && "' . $_GET['do'] . '" == "user-activity") ? "&do="+$(".sort-selected").attr("id")+"&sq="+$("#sq").val() : p_url); var paginate_goto = c_url + p_url + "&page=' . $i . '&ipp=' . (($section == $href['be_members'] and $_GET['do'] == 'user-activity') ? "" : $this->items_per_page) . '"; wrapLoad(paginate_goto); });';
                }
            }
            $this->return .= VGenerate::DeclareJS($js);
        }

        $this->low   = ($this->current_page - 1) * $this->items_per_page;
        $this->low   = ($this->low < 0) ? 0 : $this->low;
        $this->high  = ($_GET['ipp'] == 'all') ? $this->items_total : ($this->current_page * $this->items_per_page) - 1;
        $this->limit = ($_GET['ipp'] == 'all') ? '' : ' LIMIT ' . $this->low . ',' . $this->items_per_page;
    }
    /* items per page select list */
    public function display_items_per_page()
    {
        global $cfg, $language;

        $items     = '';
        $ipp_array = array(10, 25, 50, 100, 'all');
        foreach ($ipp_array as $ipp_opt) {
            $items .= ($ipp_opt == $_GET['ipp']) ? '<option selected="selected" value="' . $ipp_opt . '">' . $ipp_opt . '</option>' : '<option value="' . $ipp_opt . '">' . $ipp_opt . '</option>';
        }

        $html = '<label>' . $language['frontend.global.itemspp'] . '</label><div class="selector"><select class="paginate select-input ipp-input" name="ipp_select">' . $items . '</select></div>';
        $html .= '<script type="text/javascript">SelectList.init("ipp_select"); $(".ipp-input").on("change", function(){var this_page = \'' . $this->paging_link . '\'; var ipp_menu = c_url + this_page + \'&page=1&ipp=\' + $(this).val(); wrapLoad(ipp_menu);});</script>';

        return $html;
    }
    /* jump to page select list */
    public function display_jump_menu()
    {
        global $cfg, $language;

        $html = '<span rel="tooltip" title="' . $language['frontend.global.pagejump'] . '"><input type="text" name="ipp_jump" class="paginate jump-input" value="' . $this->current_page . '"></span>';
        $html .= '<script type="text/javascript">$(".jump-input").on("change", function(){var this_page = \'' . $this->paging_link . '\'; var jump_menu = c_url + this_page + \'&page=\' + $(this).val() + \'&ipp=\' + ' . $this->items_per_page . '; wrapLoad(jump_menu);});</script>';

        return $html;
    }
    /* show results numbers */
    public function getResultsInfo($page_of, $db_count, $pos, $page = '')
    {
        global $language;

        $js       = '$(".paginate-all").unbind().click(function(){ var paginate_all = c_url + "' . $this->paging_link . '&page=1&ipp=all"; wrapLoad(paginate_all); });';
        $show_all = ($db_count > $this->items_per_page) ? '<a class="paginate paginate-all">(show all)</a>' : null;

        return VGenerate::declareJS($js) . '<div class="place-' . $pos . '-off">&nbsp;' . ($page != 'main' ? $language['frontend.global.results'] . ' ' . ($this->low + 1) . ' - ' . $page_of . ' ' . $language['frontend.global.resultsof'] . ' ' . $db_count : null) . ' ' . $show_all . '</div>';
    }
    /* pagination links */
    public function getPaging($db_count, $pos)
    {
        global $class_filter;
        include 'f_core/config.backend.php';

        $query_string = isset($_SERVER['QUERY_STRING']) ? $class_filter->clr_str($_SERVER['QUERY_STRING']) : null;
        $request_uri  = isset($_SERVER['REQUEST_URI']) ? $class_filter->clr_str($_SERVER['REQUEST_URI']) : null;
        $request_uri  = $query_string != null ? substr($request_uri, 0, strpos($request_uri, '?')) : $request_uri;

        $section = (strpos($request_uri, $backend_access_url)) ? 'be' : 'fe';

        $html = '<ul id="pag-list">';
        $html .= '<li>';
        $html .= '<div class="pag-wrap">';
        $html .= $section == 'fe' ? '<div class="place-right">' . $this->display_pages() . '</div>' : '<div class="' . $pos . '-align ' . $pos . '-padding10 ' . $pos . '-float top-bottom-padding" style="float: right;">' . $this->display_pages() . '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';

        return (($db_count > $this->items_per_page or $_GET['ipp'] > 0) ? $html : null);
    }
    /* jump to page wrap */
    public function getPageJump($pos)
    {
        return '<div class="' . $pos . '-align ' . $pos . '-padding10 ' . $pos . '-float top-bottom-padding" style="float: right;">' . $this->display_jump_menu() . '</div>';
    }
    /* items per page wrap */
    public function getIpp($pos)
    {
        return '<div class="' . $pos . '-align ' . $pos . '-padding10 ' . $pos . '-float top-bottom-padding ipp" style="float: right;">' . $this->display_items_per_page() . '</div>';
    }
    /* displaying pagination links */
    public function display_pages()
    {
        return $this->return;
    }
}
