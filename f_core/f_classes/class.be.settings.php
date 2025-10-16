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

class VbeSettings
{
    /* section list */
    public function sectionList($for)
    {
        global $language, $cfg, $class_database, $db;

        $js = '$(".cfg-entry").mouseover(function(){$(this).addClass("y-bg");}).mouseout(function(){$(this).removeClass("y-bg");});';
        switch ($for) {
            case "personalized_channels":
            case "subscription_system":
            case "account_management":
            case "file_management":
            case "server_configuration":
            case "website_configuration":
                $ht = array();
                break;
            case "vjs_configuration":
                $q      = $db->execute("SELECT `db_config` FROM `db_fileplayers` WHERE `db_id` IN ('5', '6') LIMIT 2;");
                $r      = $q->getrows();
                $_local = unserialize($r[0]['db_config']);
                $_embed = unserialize($r[1]['db_config']);
                $h3     = $language['backend.player.summary.h3'];
                $ht     = array(
                    $language['backend.player.jw.logo.file']           => ($_local['vjs_logo_file'] == '' ? $language['backend.player.none'] : $_local['vjs_logo_file']) . ' / <span class="greyed-out">[' . ($_embed['vjs_logo_file'] == '' ? $language['backend.player.none'] : $_embed['vjs_logo_file']) . ']</span>',
                    $language['backend.player.jw.logo.link']           => ($_local['vjs_logo_link'] == '' ? $language['backend.player.none'] : $_local['vjs_logo_link']) . ' / <span class="greyed-out">[' . ($_embed['vjs_logo_link'] == '' ? $language['backend.player.none'] : $_embed['vjs_logo_link']) . ']</span>',
                    $language['backend.player.jw.logo.position']       => $_local['vjs_logo_position'] . ' / <span class="greyed-out">[' . $_embed['vjs_logo_position'] . ']</span>',

                    $language['backend.player.jw.layout.controlbar.p'] => $_local['jw_controlbar_position'] . ' / <span class="greyed-out">[' . $_embed['jw_controlbar_position'] . ']</span>',
                    $language['backend.player.jw.layout.controlbar.i'] => $_local['jw_controlbar_idle'] . ' / <span class="greyed-out">[' . $_embed['jw_controlbar_idle'] . ']</span>',
                    $language['backend.player.jw.layout.dock']         => $_local['jw_dock'] . ' / <span class="greyed-out">[' . $_embed['jw_dock'] . ']</span>',
                    $language['backend.player.jw.layout.icons']        => $_local['jw_icons'] . ' / <span class="greyed-out">[' . $_embed['jw_icons'] . ']</span>',
                    $language['backend.player.jw.layout.skin']         => ($_local['jw_skin'] == '' ? $language['backend.player.none'] : $_local['jw_skin']) . ' / <span class="greyed-out">[' . ($_embed['jw_skin'] == '' ? $language['backend.player.none'] : $_embed['jw_skin']) . ']</span>',

                    $language['backend.player.jw.behavior.autostart']  => $_local['jw_autostart'] . ' / <span class="greyed-out">[' . $_embed['jw_autostart'] . ']</span>',
                    $language['backend.player.jw.behavior.buffer']     => $_local['jw_buffer'] . ' / <span class="greyed-out">[' . $_embed['jw_buffer'] . ']</span>',
                    $language['backend.player.jw.behavior.mute']       => $_local['jw_mute'] . ' / <span class="greyed-out">[' . $_embed['jw_mute'] . ']</span>',
                    $language['backend.player.jw.behavior.repeat']     => $_local['jw_repeat'] . ' / <span class="greyed-out">[' . $_embed['jw_repeat'] . ']</span>',
                    $language['backend.player.jw.behavior.smooth']     => $_local['jw_smoothing'] . ' / <span class="greyed-out">[' . $_embed['jw_smoothing'] . ']</span>',
                    $language['backend.player.jw.behavior.stretch']    => $_local['jw_stretching'] . ' / <span class="greyed-out">[' . $_embed['jw_stretching'] . ']</span>',
                    $language['backend.player.jw.behavior.volume']     => $_local['jw_volume'] . ' / <span class="greyed-out">[' . $_embed['jw_volume'] . ']</span>',

                    $language['backend.player.jw.colors.backcolor']    => $_local['jw_colors_backcolor'] . ' <span style="width: 5px; height: 5px; border: 1px solid black; background-color: #' . $_local['jw_colors_backcolor'] . ';">&nbsp;&nbsp;&nbsp;&nbsp;</span> / <span class="greyed-out">' . $_embed['jw_colors_backcolor'] . ' <span style="width: 5px; height: 5px; border: 1px solid black; background-color: #' . $_embed['jw_colors_backcolor'] . ';">&nbsp;&nbsp;&nbsp;&nbsp;</span></span>',
                    $language['backend.player.jw.colors.frontcolor']   => $_local['jw_colors_frontcolor'] . ' <span style="width: 5px; height: 5px; border: 1px solid black; background-color: #' . $_local['jw_colors_frontcolor'] . ';">&nbsp;&nbsp;&nbsp;&nbsp;</span> / <span class="greyed-out">' . $_embed['jw_colors_frontcolor'] . ' <span style="width: 5px; height: 5px; border: 1px solid black; background-color: #' . $_embed['jw_colors_frontcolor'] . ';">&nbsp;&nbsp;&nbsp;&nbsp;</span></span>',
                    $language['backend.player.jw.colors.lightcolor']   => $_local['jw_colors_lightcolor'] . ' <span style="width: 5px; height: 5px; border: 1px solid black; background-color: #' . $_local['jw_colors_lightcolor'] . ';">&nbsp;&nbsp;&nbsp;&nbsp;</span> / <span class="greyed-out">' . $_embed['jw_colors_lightcolor'] . ' <span style="width: 5px; height: 5px; border: 1px solid black; background-color: #' . $_embed['jw_colors_lightcolor'] . ';">&nbsp;&nbsp;&nbsp;&nbsp;</span></span>',
                    $language['backend.player.jw.colors.screencolor']  => $_local['jw_colors_screencolor'] . ' <span style="width: 5px; height: 5px; border: 1px solid black; background-color: #' . $_local['jw_colors_screencolor'] . ';">&nbsp;&nbsp;&nbsp;&nbsp;</span> / <span class="greyed-out">' . $_embed['jw_colors_screencolor'] . ' <span style="width: 5px; height: 5px; border: 1px solid black; background-color: #' . $_embed['jw_colors_screencolor'] . ';">&nbsp;&nbsp;&nbsp;&nbsp;</span></span>',
                );
                break;
            case "jw_configuration":
            case "flow_configuration":
                break;
        }
        $html = '<div class="all-paddings10 bottom-margin10 left-float left-padding20">';
        $html .= '<h3 class="bottom-padding10">' . $h3 . '</h3>';
        $html .= '<div class="left-float">';

        foreach ($ht as $k => $v) {
            $val = ($for != 'jw_configuration' and $for != 'flow_configuration' and $for != 'vjs_configuration') ? ($v == '1' ? '<span class="conf-green">' . $language['frontend.global.switchon'] . '</span>' : ($v == '0' ? '<span class="err-red">' . $language['frontend.global.switchoff'] . '</span>' : $v)) : $v;
            $val = substr($v, 0, 2) == 'n:' ? substr($v, 2) : $val;
            $val = $v == 'path' ? (file_exists($k) ? '<span class="conf-green">' . $language['frontend.global.found'] . '</span>' : '<span class="err-red">' . $language['frontend.global.not.found'] . '</span>') : $val;

            $html .= ($k == $language['backend.menu.entry6.total.i'] or
                $k == $language['backend.menu.entry6.total.d'] or
                $k == $language['backend.player.jw.layout.controlbar.p'] or
                $k == $language['backend.player.jw.behavior.autostart'] or
                $k == $language['backend.player.jw.colors.backcolor'] or
                $k == $language['backend.player.menu.flow.logo.url'] or
                $k == $language['backend.player.menu.flow.time'] or
                $k == $language['backend.player.menu.flow.behavior.play'] or
                $k == $language['backend.player.menu.flow.control.bg']
            ) ? '<div class="row">&nbsp;</div>' : null;
            $html .= ($k == $language['backend.menu.entry6.total.a']) ? '</div><div class="left-float left-padding20">' : null;
            $html .= ($k == 's10') ? '</div><div class="left-float left-padding20 wd275">' : null;

            $c1 = (substr($k, 0, 2) == 's1') ? null : VGenerate::simpleDivWrap('left-float wd275 bold greyed-out', '', $k);
            $c1 .= (substr($k, 0, 2) == 's1') ? null : VGenerate::simpleDivWrap('right-float', '', '&nbsp;');
            $c2 = VGenerate::simpleDivWrap('left-float left-padding5 bold', '', $val);
            $_c = $c1 . $c2;

            $html .= VGenerate::simpleDivWrap('row no-top-padding left-float lh20' . (($k[0] == 'b' and ($for != 'jw_configuration')) ? null : ' bottom-border-dotted') . ' cfg-entry', '', $_c);
        }
        $html .= '</div>';
        $html .= '</div>';
        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* conversion settings default values */
    public function conversionSettings_default($type, $key)
    {
        global $language;

        $def_array = array();

        $def_array['conversion_flv_360p']['flv_reencode']  = $language['frontend.global.switchoff'];
        $def_array['conversion_flv_360p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.fixed'];
        $def_array['conversion_flv_360p']['bitrate_video'] = 600;
        $def_array['conversion_flv_360p']['fps']           = 25;
        $def_array['conversion_flv_360p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_flv_360p']['resize_w']      = 630;
        $def_array['conversion_flv_360p']['resize_h']      = 380;
        $def_array['conversion_flv_360p']['encoding']      = 1;
        $def_array['conversion_flv_360p']['bitrate_audio'] = 56;
        $def_array['conversion_flv_360p']['srate_audio']   = 22050;
        $def_array['conversion_flv_360p']['do_conversion'] = $language['frontend.global.switchon'];

        $def_array['conversion_flv_480p']['flv_reencode']  = $language['frontend.global.switchoff'];
        $def_array['conversion_flv_480p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.fixed'];
        $def_array['conversion_flv_480p']['bitrate_video'] = 1500;
        $def_array['conversion_flv_480p']['fps']           = 25;
        $def_array['conversion_flv_480p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_flv_480p']['resize_w']      = 852;
        $def_array['conversion_flv_480p']['resize_h']      = 480;
        $def_array['conversion_flv_480p']['encoding']      = 1;
        $def_array['conversion_flv_480p']['bitrate_audio'] = 128;
        $def_array['conversion_flv_480p']['srate_audio']   = 44100;
        $def_array['conversion_flv_480p']['do_conversion'] = $language['frontend.global.switchon'];

        $def_array['conversion_mp4']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_mp4']['bitrate_video'] = 1500;
        $def_array['conversion_mp4']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_mp4']['resize_w']      = 960;
        $def_array['conversion_mp4']['resize_h']      = 720;
        $def_array['conversion_mp4']['bitrate_audio'] = 128;
        $def_array['conversion_mp4']['srate_audio']   = 44100;
        $def_array['conversion_mp4']['encoding']      = 2;
        $def_array['conversion_mp4']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_mp4_360p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_mp4_360p']['bitrate_video'] = 300;
        $def_array['conversion_mp4_360p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_mp4_360p']['resize_w']      = 640;
        $def_array['conversion_mp4_360p']['resize_h']      = 360;
        $def_array['conversion_mp4_360p']['bitrate_audio'] = 128;
        $def_array['conversion_mp4_360p']['srate_audio']   = 44100;
        $def_array['conversion_mp4_360p']['encoding']      = 1;
        $def_array['conversion_mp4_360p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_ogv_360p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_ogv_360p']['bitrate_video'] = 300;
        $def_array['conversion_ogv_360p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_ogv_360p']['resize_w']      = 640;
        $def_array['conversion_ogv_360p']['resize_h']      = 360;
        $def_array['conversion_ogv_360p']['bitrate_audio'] = 128;
        $def_array['conversion_ogv_360p']['srate_audio']   = 44100;
        $def_array['conversion_ogv_360p']['encoding']      = 1;
        $def_array['conversion_ogv_360p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_vpx_360p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_vpx_360p']['bitrate_video'] = 300;
        $def_array['conversion_vpx_360p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_vpx_360p']['resize_w']      = 640;
        $def_array['conversion_vpx_360p']['resize_h']      = 360;
        $def_array['conversion_vpx_360p']['bitrate_audio'] = 128;
        $def_array['conversion_vpx_360p']['srate_audio']   = 44100;
        $def_array['conversion_vpx_360p']['encoding']      = 1;
        $def_array['conversion_vpx_360p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_mp4_480p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_mp4_480p']['bitrate_video'] = 900;
        $def_array['conversion_mp4_480p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_mp4_480p']['resize_w']      = 852;
        $def_array['conversion_mp4_480p']['resize_h']      = 480;
        $def_array['conversion_mp4_480p']['bitrate_audio'] = 128;
        $def_array['conversion_mp4_480p']['srate_audio']   = 44100;
        $def_array['conversion_mp4_480p']['encoding']      = 1;
        $def_array['conversion_mp4_480p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_ogv_480p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_ogv_480p']['bitrate_video'] = 900;
        $def_array['conversion_ogv_480p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_ogv_480p']['resize_w']      = 852;
        $def_array['conversion_ogv_480p']['resize_h']      = 480;
        $def_array['conversion_ogv_480p']['bitrate_audio'] = 128;
        $def_array['conversion_ogv_480p']['srate_audio']   = 44100;
        $def_array['conversion_ogv_480p']['encoding']      = 1;
        $def_array['conversion_ogv_480p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_vpx_480p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_vpx_480p']['bitrate_video'] = 900;
        $def_array['conversion_vpx_480p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_vpx_480p']['resize_w']      = 852;
        $def_array['conversion_vpx_480p']['resize_h']      = 480;
        $def_array['conversion_vpx_480p']['bitrate_audio'] = 128;
        $def_array['conversion_vpx_480p']['srate_audio']   = 44100;
        $def_array['conversion_vpx_480p']['encoding']      = 1;
        $def_array['conversion_vpx_480p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_mp4_720p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_mp4_720p']['bitrate_video'] = 5000;
        $def_array['conversion_mp4_720p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_mp4_720p']['resize_w']      = 1280;
        $def_array['conversion_mp4_720p']['resize_h']      = 720;
        $def_array['conversion_mp4_720p']['bitrate_audio'] = 128;
        $def_array['conversion_mp4_720p']['srate_audio']   = 44100;
        $def_array['conversion_mp4_720p']['encoding']      = 2;
        $def_array['conversion_mp4_720p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_ogv_720p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_ogv_720p']['bitrate_video'] = 5000;
        $def_array['conversion_ogv_720p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_ogv_720p']['resize_w']      = 1280;
        $def_array['conversion_ogv_720p']['resize_h']      = 720;
        $def_array['conversion_ogv_720p']['bitrate_audio'] = 128;
        $def_array['conversion_ogv_720p']['srate_audio']   = 44100;
        $def_array['conversion_ogv_720p']['encoding']      = 2;
        $def_array['conversion_ogv_720p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_vpx_720p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_vpx_720p']['bitrate_video'] = 5000;
        $def_array['conversion_vpx_720p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_vpx_720p']['resize_w']      = 1280;
        $def_array['conversion_vpx_720p']['resize_h']      = 720;
        $def_array['conversion_vpx_720p']['bitrate_audio'] = 128;
        $def_array['conversion_vpx_720p']['srate_audio']   = 44100;
        $def_array['conversion_vpx_720p']['encoding']      = 2;
        $def_array['conversion_vpx_720p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_mp4_1080p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_mp4_1080p']['bitrate_video'] = 7500;
        $def_array['conversion_mp4_1080p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_mp4_1080p']['resize_w']      = 1920;
        $def_array['conversion_mp4_1080p']['resize_h']      = 1080;
        $def_array['conversion_mp4_1080p']['bitrate_audio'] = 128;
        $def_array['conversion_mp4_1080p']['srate_audio']   = 44100;
        $def_array['conversion_mp4_1080p']['encoding']      = 2;
        $def_array['conversion_mp4_1080p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_ogv_1080p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_ogv_1080p']['bitrate_video'] = 7500;
        $def_array['conversion_ogv_1080p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_ogv_1080p']['resize_w']      = 1920;
        $def_array['conversion_ogv_1080p']['resize_h']      = 1080;
        $def_array['conversion_ogv_1080p']['bitrate_audio'] = 128;
        $def_array['conversion_ogv_1080p']['srate_audio']   = 44100;
        $def_array['conversion_ogv_1080p']['encoding']      = 2;
        $def_array['conversion_ogv_1080p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_vpx_1080p']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.crf.txt'];
        $def_array['conversion_vpx_1080p']['bitrate_video'] = 7500;
        $def_array['conversion_vpx_1080p']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_vpx_1080p']['resize_w']      = 1920;
        $def_array['conversion_vpx_1080p']['resize_h']      = 1080;
        $def_array['conversion_vpx_1080p']['bitrate_audio'] = 128;
        $def_array['conversion_vpx_1080p']['srate_audio']   = 44100;
        $def_array['conversion_vpx_1080p']['encoding']      = 2;
        $def_array['conversion_vpx_1080p']['do_conversion'] = $language['frontend.global.switchoff'];

        $def_array['conversion_mp4_ipad']['bitrate_mt']    = $language['backend.menu.entry6.sub1.conv.fixed'];
        $def_array['conversion_mp4_ipad']['bitrate_video'] = 1000;
        $def_array['conversion_mp4_ipad']['resize']        = $language['frontend.global.switchon'];
        $def_array['conversion_mp4_ipad']['resize_w']      = 640;
        $def_array['conversion_mp4_ipad']['resize_h']      = 480;
        $def_array['conversion_mp4_ipad']['bitrate_audio'] = 128;
        $def_array['conversion_mp4_ipad']['srate_audio']   = 44100;
        $def_array['conversion_mp4_ipad']['encoding']      = 1;
        $def_array['conversion_mp4_ipad']['do_conversion'] = $language['frontend.global.switchoff'];

        return '<div class="lh20 right-float ' . ($key == 'encoding' ? 'wd330' : 'wd430') . ' right-align bottom-border-dotted left-margin5" style="">' . $language['frontend.global.default'] . ': <span class="bold">' . $def_array[$type][$key] . '</span></div>';
    }
    /* conversion settings */
    public function conversionSettings($type, $label)
    {
        global $cfg, $language;

        $sw_on  = $language['frontend.global.switchon'];
        $sw_off = $language['frontend.global.switchoff'];

        $sel_on    = $cfg[$type . '_active'] == 1 ? 'selected' : null;
        $sel_off   = $cfg[$type . '_active'] == 0 ? 'selected' : null;
        $check_on  = $cfg[$type . '_active'] == 1 ? 'checked="checked"' : null;
        $check_off = $cfg[$type . '_active'] == 0 ? 'checked="checked"' : null;

        $flv_on     = $cfg[$type . '_reencode'] == 1 ? 'selected' : null;
        $flv_off    = $cfg[$type . '_reencode'] == 0 ? 'selected' : null;
        $cflv_on    = $cfg[$type . '_reencode'] == 1 ? 'checked="checked"' : null;
        $cflv_off   = $cfg[$type . '_reencode'] == 0 ? 'checked="checked"' : null;
        $res_on     = $cfg[$type . "_resize"] == 1 ? 'selected' : null;
        $res_off    = $cfg[$type . "_resize"] == 0 ? 'selected' : null;
        $rcheck_on  = $cfg[$type . "_resize"] == 1 ? 'checked="checked"' : null;
        $rcheck_off = $cfg[$type . "_resize"] == 0 ? 'checked="checked"' : null;
        foreach ($label as $key => $val) {
            $html .= (($key == 'fps' and $type == 'conversion_flv') or ($key == 'encoding' and $type != 'conversion_flv') or ($key != 'encoding' and $key != 'fps')) ? '<div class="row"><div class="left-float lh20 wd140">' . $val . '</div>' : null;
            $html .= $key == 'do_conversion' ? '<div class="left-float left-padding10">' . VGenerate::entrySwitch($type . '_' . $key, $type . '_' . $key, $sel_on, $sel_off, $sw_on, $sw_off, $type . '_active', $check_on, $check_off) . '</div>' : null;
            $html .= $key == 'bitrate_mt' ? '<div class="selector"><select name="' . $type . '_' . $key . '" class="select-input"><option' . ($cfg[$type . "_" . $key] == 'auto' ? ' selected="selected"' : null) . ' value="auto">' . $language['backend.menu.entry6.sub1.conv.auto'] . '</option><option' . ($cfg[$type . "_" . $key] == 'fixed' ? ' selected="selected"' : null) . ' value="fixed">' . $language['backend.menu.entry6.sub1.conv.fixed'] . '</option>' . ($type == 'conversion_mp4' ? '<option' . ($cfg[$type . "_" . $key] == 'crf' ? ' selected="selected"' : null) . ' value="crf">' . $language['backend.menu.entry6.sub1.conv.crf'] . '</option>' : null) . '</select></div>' : null;
            $html .= $key == 'bitrate_video' ? VGenerate::simpleDivWrap('left-float left-padding10', '', VGenerate::basicInput('text', $type . '_' . $key, 'text-input wd60', $cfg[$type . "_" . $key])) : null;
            $html .= ($key == 'fps' and $type == 'conversion_flv') ? VGenerate::simpleDivWrap('left-float left-padding10', '', VGenerate::basicInput('text', $type . '_' . $key, 'text-input wd60', $cfg[$type . "_" . $key])) : null;
            $html .= $key == 'resize' ? '<div class="left-float left-padding10">' . VGenerate::entrySwitch($type . '_' . $key, $type . '_' . $key, $res_on, $res_off, $sw_on, $sw_off, $type . '_resize', $rcheck_on, $rcheck_off) . '</div>' : null;
            $html .= $key == 'resize_w' ? VGenerate::simpleDivWrap('left-float left-padding10', '', VGenerate::basicInput('text', $type . '_' . $key, 'text-input wd60', $cfg[$type . "_" . $key])) : null;
            $html .= $key == 'resize_h' ? VGenerate::simpleDivWrap('left-float left-padding10', '', VGenerate::basicInput('text', $type . '_' . $key, 'text-input wd60', $cfg[$type . "_" . $key])) : null;
            $html .= ($key == 'encoding' and $type != 'conversion_flv') ? '<div class="selector"><select name="' . $type . '_' . $key . '" class="select-input wd160"><option' . ($cfg[$type . "_" . $key] == '1' ? ' selected="selected"' : null) . ' value="1">' . $language['backend.menu.entry6.sub1.conv.mp4.1pass'] . '</option><option' . ($cfg[$type . "_" . $key] == '2' ? ' selected="selected"' : null) . ' value="2">' . ($type == 'conversion_mp4' ? $language['backend.menu.entry6.sub1.conv.mp4.2pass'] : $language['backend.menu.entry6.sub1.conv.mp4.2pass.1']) . '</option></select></div>' : null;
            $html .= $key == 'bitrate_audio' ? VGenerate::simpleDivWrap('left-float left-padding10', '', VGenerate::basicInput('text', $type . '_' . $key, 'text-input wd60', $cfg[$type . "_" . $key])) : null;
            $html .= $key == 'srate_audio' ? VGenerate::simpleDivWrap('left-float left-padding10', '', VGenerate::basicInput('text', $type . '_' . $key, 'text-input wd60', $cfg[$type . "_" . $key])) : null;
            $html .= (($key == 'fps' and $type == 'conversion_flv') or ($key == 'encoding' and $type != 'conversion_flv') or ($key != 'encoding' and $key != 'fps')) ? '</div>' : null;
        }
        return $html;
    }

    /* write to list config files and increment updated entries nr */
    public function writeCheck($post_var, $update)
    {
        global $class_filter, $language;

        if ($_POST[$post_var] != '' and file_exists($_POST[$post_var . "_path"]) and $_POST[$post_var] != file_get_contents($_POST[$post_var . "_path"])) {
            if (file_put_contents($_POST[$post_var . "_path"], $class_filter->clr_str($_POST[$post_var]))) {
                $update = $update + 1;
            }
        } elseif ($_POST[$post_var . "_path"] != '' and !file_exists($_POST[$post_var . "_path"])) {
            return false;
        }
        return $update;
    }
    /* changing admin password */
    public function beChangePassword()
    {
        global $language, $class_filter, $db;

        $new_pass  = $class_filter->clr_str($_POST['backend_menu_entry2_sub6_admin_new_pass']);
        $conf_pass = $class_filter->clr_str($_POST['backend_menu_entry2_sub6_admin_conf_pass']);

        $f1            = '<span class="underlined">' . $language['backend.menu.entry2.sub6.admin.new.pass'] . '</span>';
        $f2            = '<span class="underlined">' . $language['backend.menu.entry2.sub6.admin.conf.pass'] . '</span>';
        $error_message = $new_pass == '' ? $language['notif.error.required.field'] . $f1 : ($conf_pass == '' ? $language['notif.error.required.field'] . $f2 : ($new_pass != $conf_pass ? $language['notif.error.pass.nomatch'] : null));

        if ($error_message == '') {
            $hasher   = new VPasswordHash(8, false);
            $enc_pass = $hasher->HashPassword($conf_pass);
            $do_pass  = $db->execute(sprintf("UPDATE `db_settings` SET `cfg_data` = '%s' WHERE `cfg_name` = '%s'  LIMIT 1; ", $enc_pass, 'backend_password'));
            if ($db->Affected_Rows() > 0) {
                return '1';
            }
        }
        return $error_message;
    }
    /* update settings and show notice */
    public function doSettings()
    {
        global $class_database, $language, $class_filter, $db;

        $update = 0;
        $n      = 0;
        if ($_GET['s'] != 'backend-menu-entry3-sub13' and $_GET['s'] != 'backend-menu-entry3-sub14' and
            $_GET['s'] != 'backend-menu-entry3-sub15' and
            $_GET['s'] != 'backend-menu-entry3-sub16' and
            $_GET['s'] != 'backend-menu-entry3-sub17' and
            $_GET['s'] != 'backend-menu-entry4-sub2' and
            $_GET['s'] != 'backend-menu-entry4-sub3' and
            $_GET['s'] != 'backend-menu-entry3-sub5' and
            $_GET['s'] != 'backend-menu-entry2-sub16' and
            $_GET['s'] != 'backend-menu-entry2-sub5' and
            $_GET['s'] != 'backend-menu-entry2-sub5v' and
            $_GET['s'] != 'backend-menu-entry2-sub5i' and
            $_GET['s'] != 'backend-menu-entry2-sub5a' and
            $_GET['s'] != 'backend-menu-entry2-sub5d' and
            $_GET['s'] != 'backend-menu-entry2-sub5c' and
            $_GET['s'] != 'backend-menu-entry2-sub5l' and
            $_GET['s'] != 'backend-menu-entry14-sub1' and
            $_GET['s'] != 'backend-menu-entry14-sub2' and
            $_GET['s'] != 'backend-menu-entry14-sub3' and
            $_GET['s'] != 'backend-menu-entry14-sub4' and
            $_GET['s'] != 'backend-menu-entry14-sub5' and
            $_GET['s'] != 'backend-menu-entry14-sub6' and
            $_GET['s'] != 'backend-menu-entry14-sub7' and
            $_GET['s'] != 'backend-menu-entry14-sub8' and
            $_GET['s'] != 'backend-menu-entry16-sub1' and
            $_GET['s'] != 'backend-menu-entry16-sub2' and
            $_GET['s'] != 'backend-menu-entry16-sub3' and
            $_GET['s'] != 'backend-menu-entry16-sub4' and
            $_GET['s'] != 'backend-menu-entry16-sub5' and
            $_GET['s'] != 'backend-menu-entry16-sub6' and
            $_GET['s'] != 'backend-menu-entry16-sub7' and
            $_GET['s'] != 'backend-menu-entry2-sub5b') {
            $keep_open = (intval($_POST['keep_open']) == 1) ? 1 : 0;
            $do_open   = $db->execute(sprintf("UPDATE `db_settings` SET `cfg_data` = '%s' WHERE `cfg_name` = '%s'  LIMIT 1; ", $keep_open, 'keep_entries_open'));
            $update    = ($db->Affected_Rows() > 0) ? ($update + 1) : $update;
        }

        switch ($_GET['s']) {
            case "backend-menu-entry2-sub4": //public area access
                $update = self::writeCheck("backend_menu_entry2_sub4_IPlist", $update);
                break;
            case "backend-menu-entry2-sub6": //backend access
                $err = ($_POST['backend_menu_entry2_sub6_admin_new_pass'] != '' or $_POST['backend_menu_entry2_sub6_admin_conf_pass'] != '') ? self::beChangePassword() : null;
                if ($err != '' and $err != '1') {
                    echo VGenerate::noticeWrap(array($err, '', VGenerate::noticeTpl('', $err, '')));
                    return false;
                } elseif ($err == '1') {$update = $update + 1;}

                $update                 = self::writeCheck("backend_menu_entry2_sub4_IPlist_be", $update);
                $_SESSION['ADMIN_NAME'] = $class_filter->clr_str($_POST['backend_menu_entry2_sub6_admin_user']);
                echo VGenerate::declareJS('$(document).ready(function(){ $("#nav-actions>span").html("' . $_SESSION['ADMIN_NAME'] . '"); });');
                break;
            case "backend-menu-entry2-sub17": //sign up/registration
                $update = self::writeCheck("backend_menu_section_IPlist", $update);
                $update = self::writeCheck("backend_menu_entry1_sub1_maillist", $update);
                $update = self::writeCheck("backend_menu_entry1_sub1_userlist", $update);
                $update = self::writeCheck("backend_menu_entry1_sub1_terms_info", $update);
                break;
            case "backend-menu-entry11-sub1": //videojs player, website player
            case "backend-menu-entry11-sub2": //videojs player, embedded player
            case "backend-menu-entry12-sub1": //jw player, website player
            case "backend-menu-entry12-sub2": //jw player, embedded player
            case "backend-menu-entry13-sub1": //flow player, website player
            case "backend-menu-entry13-sub2": //flow player, embedded player
                $update = $update + VbePlayers::cfgUpdate();
                $n      = 1;
                break;
        }

        $update         = ($_POST) ? ($update + $class_database->settingsUpdate()) : $update;
        $notice_message = ($update > 0) ? VGenerate::noticeWrap(array($error_message, $notice_message, VGenerate::noticeTpl('', '', $language['notif.success.request'] . ' ' . ($n == 0 ? $update . $language['backend.changed.settings'] : null)))) : null;

        return $notice_message;
    }
    /* check path on server */
    public function checkPath($for)
    {
        global $cfg, $language;

        $cfg[$for] = $cfg[$for] == '' ? $for : $cfg[$for];

        if (is_file($for) or is_file($cfg[$for])) {
            exec(sprintf("ls -la \"%s\"", $cfg[$for]), $_out);

            $html = $_out[0] != '' ? $_out[0] . '<br><br>' : '<span class="italic">' . $language['frontend.global.not.found'] . '</span>';

            return $html;
        }
    }
    /* settings for conversion logging */
    public function settings_logConversion($key, $lang)
    {
        global $cfg;

        $cfg_check = $cfg[$key] == 1 ? ' checked="checked"' : null;
        $input_code .= '<div class="icheck-box' . ($key != 'log_doc_conversion' ? ' top-padding10' : ' no-top-padding') . '"><input class="conversion_logging_cb" type="checkbox"' . $cfg_check . ' name="' . $key . '" value="1" /><label>' . $lang . '</label>';

        return $input_code;
    }
    /* settings for delete original uploaded files */
    public function settings_delOriginalFiles($input_type, $lang)
    {
        global $cfg, $language;

        $radio_check1 = $cfg[$input_type] == 1 ? 'checked="checked"' : null;
        $radio_check2 = $cfg[$input_type] == 0 ? 'checked="checked"' : null;
        $input_code .= '<div class="icheck-box"><input type="radio" ' . $radio_check1 . ' name="' . $input_type . '" value="1"><label>' . $language["backend.menu.entry1.sub7." . $lang . ".up.store"] . '</label></div>';
        $input_code .= '<div class="icheck-box"><input type="radio" ' . $radio_check2 . ' name="' . $input_type . '" value="0"><label>' . $language["backend.menu.entry1.sub7." . $lang . ".up.del"] . '</label></div>';

        return $input_code;
    }
    /* edit templates */
    public function tplEdit($for)
    {
        global $cfg, $class_filter, $class_database, $language, $smarty;

        $_f = 0;
        $_n = null;
        $_p = $class_filter->clr_str($_GET['p']);
        switch ($for) {
            case "tpl-edit-mail":
                $directory = $cfg['ww_templates_dir'] . '/tpl_email';
                break;
            case "tpl-edit-page":
                $directory = $cfg['ww_templates_dir'] . '/tpl_page';
                break;
            case "lang-fe":
                $lang_id   = $class_database->singleFieldValue('db_languages', 'lang_id', 'db_id', $class_filter->clr_str($_GET['f']));
                $directory = $cfg['language_dir'] . '/' . $lang_id . '/lang_frontend';
                break;
            case "lang-be":
                $lang_id   = $class_database->singleFieldValue('db_languages', 'lang_id', 'db_id', $class_filter->clr_str($_GET['f']));
                $directory = $cfg['language_dir'] . '/' . $lang_id . '/lang_backend';
                break;
        }
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));

        foreach ($scanned_directory as $k => $v) {
            if ($_f == 0 and md5($v) == $_p) {
                $_f  = 1;
                $_n  = $v;
                $_ht = file_get_contents($directory . '/' . $v);
            }
        }

        $editor_url = $cfg['scripts_url'] . '/shared/codemirror';

        $html = '<div id="lb-wrapper">';
        $html .= '<article><h3 class="content-title"><i class="icon-pencil"></i> ' . $_n . '</h3><div class="line"></div></article>';
        $html .= '<div class="entry-list vs-column full">';
        $html .= VGenerate::simpleDivWrap('left-float row wdmax no-top-padding', 'tpl-save-update', '');
        $html .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $html .= '<li>';
        $html .= '<div>';
        $html .= '<div class="responsive-accordion-head active d-flex jc-sb ai-center">';
        $html .= VGenerate::simpleDivWrap('', '', 'Edit ' . $_n, '', 'span');
        $html .= '<span rel="tooltip" title="' . $language['frontend.global.savechanges'] . '">';
        $html .= '<i class="iconBe-floppy-disk tpl-save"></i>';
        $html .= '</span>';
        $html .= '</div>';
        $html .= '<div class="responsive-accordion-panel active">';
        $html .= VGenerate::simpleDivWrap('row no-top-padding', '', sprintf("<textarea name=\"tpl_page_code\" id=\"tpl-page-code\" class=\"textarea-input wd680 h400\">%s</textarea>", $_ht));

        $html .= '<script type="text/javascript">
                var myTextArea = document.getElementById("tpl-page-code");
                var myCodeMirror = CodeMirror.fromTextArea(myTextArea, {lineNumbers: true, mode: "xml", htmlMode: true});
            </script>';

        $html .= '</div>';
        $html .= '<div class="responsive-accordion-head active d-flex jc-sb ai-center">';
        $html .= VGenerate::simpleDivWrap('', '', 'Edit ' . $_n, '', 'span');
        $html .= '<span rel="tooltip" title="' . $language['frontend.global.savechanges'] . '">';
        $html .= '<i class="iconBe-floppy-disk tpl-save"></i>';
        $html .= '</span>';
        $html .= '</div>';
        $html .= '</div>';
        $html .= '</li>';
        $html .= '</ul>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }
    /* save edited templates */
    public function tplSave()
    {
        global $cfg, $class_filter, $class_database, $language;

        $f   = 0;
        $msg = null;

        switch ($_POST['file_tpl']) {
            case "lang-fe":
                $lang_id   = $class_database->singleFieldValue('db_languages', 'lang_id', 'db_id', intval($_POST['lang_id']));
                $directory = $cfg['language_dir'] . '/' . $lang_id . '/lang_frontend';
                break;
            case "lang-be":
                $lang_id   = $class_database->singleFieldValue('db_languages', 'lang_id', 'db_id', intval($_POST['lang_id']));
                $directory = $cfg['language_dir'] . '/' . $lang_id . '/lang_backend';
                break;
            case "tpl-edit-mail":
                $dir       = 'tpl_email';
                $directory = $cfg['ww_templates_dir'] . '/' . $dir;
                break;
            default:
                $dir       = 'tpl_page';
                $directory = $cfg['ww_templates_dir'] . '/' . $dir;
                break;
        }
        $tpl               = $class_filter->clr_str($_POST['file_entry']);
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));

        foreach ($scanned_directory as $k => $v) {
            if ($f == 0 and md5($v) == $tpl) {
                if (file_put_contents($directory . '/' . $v, $_POST['tpl_page_code'])) {
                    $msg = VGenerate::noticeTpl('', '', $language['notif.success.request']);
                } else {
                    $msg = VGenerate::noticeTpl('', $language['backend.menu.entry2.sub9.write.error'], '');
                }
                $f = 1;
            }
        }
        return $msg;
    }
    /* username select list */
    public function username_selectList($name = false)
    {
        global $db;

        $s          = 0;
        $input_name = $name ? 'assign_username_' . $name : 'assign_username';
        $input_id   = str_replace('_', '-', $input_name);
        $res        = $db->execute("SELECT `usr_key`, `usr_user` FROM `db_accountuser` WHERE `usr_status`='1' ORDER BY `usr_user`;");
        $html       = '<select id="' . $input_id . '" name="' . $input_name . '" class="select-input wd260 assign-username">';
        while (!$res->EOF) {
            if ($s == 0) {
                $_SESSION['file_owner'] = $res->fields['usr_key'];
            }
            $html .= '<option value="' . $res->fields['usr_key'] . '">' . $res->fields['usr_user'] . '</option>';
            $res->MoveNext();
            $s += 1;
        }
        $html .= '</select>';

        return $html;
    }
    /* generate various input sections */
    public function div_setting_input($bullet_id, $input_type, $entry_title, $entry_id, $input_name, $input_value, $bottom_border = 1, $section = 'be', $col_type = 'eights')
    {
        global $language, $cfg, $smarty, $class_filter;

        $tooltip_text = '<span title=\'' . ($language[$entry_title . '.tip']) . '\' rel="tooltip">##TTICON##</span>';

        $tooltip_div = $tooltip_text != '' ? VGenerate::simpleDivWrap('right-float font12', '', $tooltip_text) : null;
        $input_value = $input_value == '' ? ($language[$input_value] != '') ? $language[$input_value] : $input_value : $input_value;

        switch ($input_type) {
            case "conversion_flv":$p1 = $language['backend.menu.entry6.sub1.conv.flv.option'];
                break;
            case "conversion_mp4":$p1 = $language['backend.menu.entry6.sub1.conv.mp4.option'];
                break;
            case "conversion_ipad":$p1 = $language['backend.menu.entry6.sub1.conv.mp4.option'];
                break;
        }

        switch ($input_type) {
            case "site_themes":
                $th = array(
                    "blue"   => $language['backend.menu.entry1.sub14.theme.blue'],
                    "green"  => $language['backend.menu.entry1.sub14.theme.green'],
                    "orange" => $language['backend.menu.entry1.sub14.theme.orange'],
                    "purple" => $language['backend.menu.entry1.sub14.theme.purple'],
                    "red"    => $language['backend.menu.entry1.sub14.theme.red'],
                );
                $input_code .= '<div class="row">';
                $input_code .= VGenerate::simpleDivWrap('left-float lh20 wd140', '', $language['backend.menu.entry1.sub14.theme.main']);
                $input_code .= '<select name="site_theme" class="select-input wd200">';
                foreach ($th as $k => $v) {
                    $input_code .= '<option value="' . $k . '"' . ($cfg['site_theme'] == $k ? ' selected="selected"' : null) . '>' . $v . '</option>';
                }
                $input_code .= '</select>';
                $input_code .= '</div>';
                break;
            case "grabber_listing":
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="m_list_yt"' . ($cfg['m_list_yt'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.import.menu.mobile.yt'] . '</label><br>';
                $input_code .= '<input type="checkbox" value="1" name="m_list_dm"' . ($cfg['m_list_dm'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.import.menu.mobile.dm'] . '</label><br>';
                $input_code .= '<input type="checkbox" value="1" name="m_list_vi"' . ($cfg['m_list_vi'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.import.menu.mobile.vi'] . '</label></div>';
                break;
            case "grabber_functions":
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="import_yt"' . ($cfg['import_yt'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.import.menu.grabber.yt.support'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="import_dm"' . ($cfg['import_dm'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.import.menu.grabber.dm.support'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="import_vi"' . ($cfg['import_vi'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.import.menu.grabber.vi.support'] . '</label></div>';
                break;
            case "grabber_mode":
                $input_code .= '<div class="icheck-box"><input type="radio" value="embed" name="import_mode"' . ($cfg['import_mode'] == 'embed' ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.import.embed'] . '</label><br>';
                $input_code .= '<input type="radio" value="download" name="import_mode"' . ($cfg['import_mode'] == 'download' ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.import.download'] . '</label><br>';
                $input_code .= '<input type="radio" value="ask" name="import_mode"' . ($cfg['import_mode'] == 'ask' ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.import.ask'] . '</label></div>';
                break;
            case "jw_license":
            case "jw_layout":
            case "vjs_layout":
            case "jw_behavior":
            case "vjs_behavior":
            case "vjs_advertising":
            case "jw_logo":
            case "vjs_logo":
            case "jw_rightclick":
            case "vjs_rightclick":
            case "jw_sharing":
            case "jw_related":
            case "jw_analytics":
            case "jw_ga":
            case "jw_captions":
            case "jw_advertising":
            case "flow_license":
            case "flow_logo":
            case "flow_behavior":
            case "flow_analytics":
                $input_code = VbePlayers::div_setting_input($input_type);
                break;
            case "streaming_settings":
                $input_code = '<label>' . $language['backend.streaming.method'] . '</label>';
                $input_code .= '<div class="selector">';
                $input_code .= '<select name="stream_method" class="select-input wd200 stream-method">';
                $input_code .= '<option value="progressive"' . ($cfg['stream_method'] == 1 ? ' selected="selected"' : null) . '>' . $language['backend.streaming.method.1'] . '</option>';
                $input_code .= '<option value="pseudostreaming"' . ($cfg['stream_method'] == 2 ? ' selected="selected"' : null) . '>' . $language['backend.streaming.method.2'] . '</option>';
                $input_code .= '<option value="rtmp"' . ($cfg['stream_method'] == 3 ? ' selected="selected"' : null) . '>' . $language['backend.streaming.method.3'] . '</option>';
                $input_code .= '</select>';
                $input_code .= '</div>';

                $input_code .= '<div id="s-server-opt" style="display: ' . ($cfg['stream_method'] == 2 ? 'block' : 'none') . ';">';
                $input_code .= '<label>' . $language['backend.streaming.method.2.server'] . '</label>';
                $input_code .= '<div class="selector">';
                $input_code .= '<select name="stream_server" class="select-input wd200 stream-server">';
                $input_code .= '<option value="apache"' . ($cfg['stream_server'] == 'apache' ? ' selected="selected"' : null) . '>' . $language['backend.streaming.method.2.server.apache'] . '</option>';
                $input_code .= '<option value="lighttpd"' . ($cfg['stream_server'] == 'lighttpd' ? ' selected="selected"' : null) . '>' . $language['backend.streaming.method.2.server.lighttpd'] . '</option>';
                $input_code .= '</select>';
                $input_code .= '</div>';
                $input_code .= '<div id="s-light-opt" style="display: ' . (($cfg['stream_method'] == 2 and $cfg['stream_server'] == 'lighttpd') ? 'block' : 'none') . ';">';
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.method.2.stream.url'] . '</label>', 'left-float', 'stream_url', 'text-input wd200', $cfg['stream_lighttpd_url']);
                $input_code .= '<div class="left-float wd500">';
                $input_code .= VGenerate::simpleDivWrap('left-float lh20 wd140 top-padding5', '', '<label>' . $language['backend.streaming.method.2.stream.secure'] . '</label>');
                $input_code .= '<div class="left-float top-padding5 icheck-box"><input type="radio" value="1" onclick="openDiv(\'s-secure-opt\');" name="stream_secure"' . ($cfg['stream_lighttpd_secure'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['frontend.global.yes'] . '</label><br>';
                $input_code .= '<input type="radio" value="0" onclick="closeDiv(\'s-secure-opt\');" name="stream_secure"' . ($cfg['stream_lighttpd_secure'] == 0 ? ' checked="checked"' : null) . ' class=""><label>' . $language['frontend.global.no'] . '</label></div>';
                $input_code .= '</div>';

                $input_code .= '<div id="s-secure-opt" style="display: ' . (($cfg['stream_method'] == 2 and $cfg['stream_lighttpd_secure'] == 1) ? 'block' : 'none') . ';">';
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.method.2.stream.prefix'] . '</label>', 'left-float', 'stream_prefix', 'text-input wd200', $cfg['stream_lighttpd_prefix']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.streaming.method.2.stream.key'] . '</label>', 'left-float', 'stream_key', 'text-input wd200', $cfg['stream_lighttpd_key']);
                $input_code .= '</div>'; //end s-secure-opt
                $input_code .= '</div>'; //end s-light-opt
                $input_code .= '</div>'; //end s-server-opt
                $input_code .= VGenerate::simpleDivWrap('', 's-rtmp-loc', VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', $language['backend.streaming.method.3.loc'], 'left-float', 'stream_rtmp_location', 'text-input wd200', $cfg['stream_rtmp_location']), 'display: ' . ($cfg['stream_method'] == 3 ? 'block' : 'none') . ';');

                $input_js = '$(".stream-method").change(function(){';
                $input_js .= 'var sel = this.selectedIndex;';
                $input_js .= 'if(sel == 1){closeDiv("s-rtmp-loc"); openDiv("s-server-opt");}';
                $input_js .= 'if(sel == 2){closeDiv("s-server-opt"); openDiv("s-rtmp-loc");}';
                $input_js .= 'if(sel == 0){closeDiv("s-server-opt"); closeDiv("s-rtmp-loc");}';
                $input_js .= '});';
                $input_js .= '$(".stream-server").change(function(){';
                $input_js .= 'var sel = this.selectedIndex;';
                $input_js .= 'if(sel == 0){closeDiv("s-light-opt");}';
                $input_js .= 'if(sel == 1){openDiv("s-server-opt"); openDiv("s-light-opt");}';
                $input_js .= '});';

                $input_code .= VGenerate::declareJS('$(document).ready(function(){' . $input_js . '});');
                break;
            case "video_player":
                $input_code .= '<div class="left-float wd300 icheck-box">';
                $input_code .= '<input type="radio" value="vjs" name="fp_video"' . ($cfg['video_player'] == 'vjs' ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub12.file.vjs'] . '</label><br>';
                $input_code .= '<input type="radio" value="jw" name="fp_video"' . ($cfg['video_player'] == 'jw' ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub12.file.jw'] . '</label><br>';
                $input_code .= '</div>';
                break;
            case "image_player":
                $input_code .= '<div class="left-float wd300 icheck-box">';
                $input_code .= '<input type="radio" value="jq" name="fp_image"' . ($cfg['image_player'] == 'jq' ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub12.file.jq'] . '</label>';
                $input_code .= '</div>';
                break;
            case "audio_player":
                $input_code .= '<div class="left-float wd300 icheck-box">';
                $input_code .= '<input type="radio" value="vjs" name="fp_audio"' . ($cfg['audio_player'] == 'vjs' ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub12.file.vjs'] . '</label><br>';
                $input_code .= '<input type="radio" value="jw" name="fp_audio"' . ($cfg['audio_player'] == 'jw' ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub12.file.jw'] . '</label><br>';
                $input_code .= '</div>';
                break;
            case "document_player":
                $input_code .= '<div class="left-float wd300 icheck-box">';
                $input_code .= '<input type="radio" value="reader" name="fp_doc"' . ($cfg['document_player'] == 'reader' ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub12.file.reader'] . '</label><br>';
                $input_code .= '</div>';
                break;
            case "image_sitemap":
                $file = $cfg['sitemap_dir'] . '/sm_image/sitemap-image.xml';

                $input_code .= '<div class="row no-top-padding left-float wd200">';
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 left-padding5 right-padding5', '<label>' . $language['backend.menu.entry1.sub11.sitemap.max'] . '</label>', 'left-float', 'sm_max_image', 'text-input wd50', $cfg['sitemap_image_max']);
                $input_code .= '<div class="clearfix">&nbsp;</div>';
                $input_code .= VGenerate::simpleDivWrap('', '', '<a href="javascript:;" class="cancel-trigger sitemap-image-rebuild sitemap-button"><span>' . $language['backend.menu.entry1.sub11.sitemap.rebuild.i'] . '</span></a>');
                $input_code .= '</div>';

                $input_code .= '<div class="info-text">';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.name'] . '</label><span class="bold" title="' . $file . '">sitemap-image.xml</span><br />';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.size'] . '</label><span class="bold">' . VUseraccount::numberFormat(array("size" => (file_exists($file) ? filesize($file) : 0)), 1) . '</span><br />';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.date'] . '</label><span class="bold">' . (file_exists($file) ? date('Y-m-d h:i:s', filemtime($file)) : '-') . '</span>';
                $input_code .= '</div>';
                break;

            case "video_sitemap":
                $file = $cfg['sitemap_dir'] . '/sm_video/sitemap-video.xml';

                $input_code .= '<div class="vs-column full">';
                $input_code .= '<div class="icheck-box">';
                $input_code .= '<input type="checkbox" value="1" name="sm_v_hd"' . ($cfg['sitemap_video_hd'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.video.src3'] . '</label>';
                $input_code .= '</div>';
                $input_code .= '</div>';

                $input_code .= '<div class="row no-top-padding left-float wd200">';
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 left-padding5 right-padding5', '<label>' . $language['backend.menu.entry1.sub11.sitemap.max'] . '</label>', 'left-float', 'sm_max_video', 'text-input wd50', $cfg['sitemap_video_max']);
                $input_code .= '<div class="clearfix">&nbsp;</div>';
                $input_code .= VGenerate::simpleDivWrap('', '', '<a href="javascript:;" class="cancel-trigger sitemap-video-rebuild sitemap-button"><span>' . $language['backend.menu.entry1.sub11.sitemap.rebuild.v'] . '</span></a>');
                $input_code .= '</div>';

                $input_code .= '<div class="info-text">';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.name'] . '</label><span class="bold" title="' . $file . '">sitemap-video.xml</span><br />';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.size'] . '</label><span class="bold">' . VUseraccount::numberFormat(array("size" => (file_exists($file) ? filesize($file) : 0)), 1) . '</span><br />';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.date'] . '</label><span class="bold">' . (file_exists($file) ? date('Y-m-d h:i:s', filemtime($file)) : '-') . '</span>';
                $input_code .= '</div>';
                break;
            case "short_sitemap":
                $file = $cfg['sitemap_dir'] . '/sm_short/sitemap-short.xml';

                // $input_code .= '<div class="vs-column full">';
                // $input_code .= '<div class="icheck-box">';
                // $input_code .= '</div>';
                // $input_code .= '</div>';

                $input_code .= '<div class="row no-top-padding left-float wd200">';
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 left-padding5 right-padding5', '<label>' . $language['backend.menu.entry1.sub11.sitemap.max'] . '</label>', 'left-float', 'sm_max_short', 'text-input wd50', $cfg['sitemap_short_max']);
                $input_code .= '<div class="clearfix">&nbsp;</div>';
                $input_code .= VGenerate::simpleDivWrap('', '', '<a href="javascript:;" class="cancel-trigger sitemap-short-rebuild sitemap-button"><span>' . $language['backend.menu.entry1.sub11.sitemap.rebuild.s'] . '</span></a>');
                $input_code .= '</div>';

                $input_code .= '<div class="info-text">';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.name'] . '</label><span class="bold" title="' . $file . '">sitemap-short.xml</span><br />';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.size'] . '</label><span class="bold">' . VUseraccount::numberFormat(array("size" => (file_exists($file) ? filesize($file) : 0)), 1) . '</span><br />';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.date'] . '</label><span class="bold">' . (file_exists($file) ? date('Y-m-d h:i:s', filemtime($file)) : '-') . '</span>';
                $input_code .= '</div>';
                break;
            case "global_sitemap":
                $file = $cfg['sitemap_dir'] . '/sm_global/sitemap.xml';

                $input_code .= '<div class="vs-column thirds">';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_homepage"' . ($cfg['sitemap_global_frontpage'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.home'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_static"' . ($cfg['sitemap_global_content'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.static'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_categ"' . ($cfg['sitemap_global_categories'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.categ'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_users"' . ($cfg['sitemap_global_users'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.users'] . '</label></div>';
                $input_code .= '</div>';
                $input_code .= '<div class="vs-column thirds">';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_live"' . ($cfg['sitemap_global_live'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.live'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_video"' . ($cfg['sitemap_global_video'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.video'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_short"' . ($cfg['sitemap_global_short'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.short'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_image"' . ($cfg['sitemap_global_image'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.image'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_audio"' . ($cfg['sitemap_global_audio'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.audio'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_doc"' . ($cfg['sitemap_global_document'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.doc'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_blog"' . ($cfg['sitemap_global_blog'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.blog'] . '</label></div>';
                $input_code .= '</div>';
                $input_code .= '<div class="vs-column thirds fit">';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_live_pl"' . ($cfg['sitemap_global_live_pl'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.live.pl'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_video_pl"' . ($cfg['sitemap_global_video_pl'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.video.pl'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_short_pl"' . ($cfg['sitemap_global_short_pl'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.short.pl'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_image_pl"' . ($cfg['sitemap_global_image_pl'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.image.pl'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_audio_pl"' . ($cfg['sitemap_global_audio_pl'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.audio.pl'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_doc_pl"' . ($cfg['sitemap_global_document_pl'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.doc.pl'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" value="1" name="sm_blog_pl"' . ($cfg['sitemap_global_blog_pl'] == 1 ? ' checked="checked"' : null) . ' class=""><label>' . $language['backend.menu.entry1.sub11.sitemap.inc.blog.pl'] . '</label></div>';
                $input_code .= '</div><div class="clearfix"></div>';
                $input_code .= '<div class="no-top-padding left-float wd200">';
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 left-padding5 right-padding5', '<label>' . $language['backend.menu.entry1.sub11.sitemap.max'] . '</label>', 'left-float', 'sm_max_entries', 'text-input wd50', $cfg['sitemap_global_max']);
                $input_code .= '<div class="clearfix">&nbsp;</div>';
                $input_code .= VGenerate::simpleDivWrap('', '', '<a href="javascript:;" class="cancel-trigger sitemap-rebuild sitemap-button"><span>' . $language['backend.menu.entry1.sub11.sitemap.rebuild.g'] . '</span></a>');
                $input_code .= '</div>';
                $input_code .= '<div class="info-text">';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.name'] . '</label><span class="bold" title="' . $file . '">sitemap.xml</span><br />';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.size'] . '</label><span class="bold">' . VUseraccount::numberFormat(array("size" => (file_exists($file) ? filesize($file) : 0)), 1) . '</span><br />';
                $input_code .= '<label>' . $language['backend.menu.entry1.sub11.sitemap.i.date'] . '</label><span class="bold">' . (file_exists($file) ? date('Y-m-d h:i:s', filemtime($file)) : '-') . '</span>';
                $input_code .= '</div>';
                break;
            case "email_templates":
                $directory         = $cfg['ww_templates_dir'] . '/tpl_email';
                $scanned_directory = array_diff(scandir($directory), array('..', '.', '.htaccess'));
                $s                 = 1;

                $d = array(
                    'tpl_accountremoval.tpl'                 => 'Delete account request',
                    'tpl_affiliate_cancel.tpl'               => 'Request affiliate cancelation',
                    'tpl_affiliate_confirm.tpl'              => 'Affiliate account confirmation',
                    'tpl_affiliate_denied.tpl'               => 'Affiliate account declined',
                    'tpl_affiliate_request.tpl'              => 'Request affiliate membership',
                    'tpl_channelcomment.tpl'                 => 'Channel comment notification',
                    'tpl_contact.tpl'                        => 'Footer contact email',
                    'tpl_emailchange.tpl'                    => 'Email change request',
                    'tpl_emaildigest.tpl'                    => 'User subscriptions email digest',
                    'tpl_emailverification.tpl'              => 'Account email verification',
                    'tpl_filecomment.tpl'                    => 'File comment notification',
                    'tpl_fileflagging.tpl'                   => 'File flagging notification',
                    'tpl_fileresponse.tpl'                   => 'File response notification',
                    'tpl_follow.tpl'                         => 'User follow notification',
                    'tpl_invitecontact.tpl'                  => 'Invite from contacts',
                    'tpl_inviteuser.tpl'                     => 'User friend request',
                    'tpl_newmember_be.tpl'                   => 'New member registration notification',
                    'tpl_newupload.tpl'                      => 'New upload notification',
                    'tpl_partner_cancel.tpl'                 => 'Request partner cancellation',
                    'tpl_partner_confirm.tpl'                => 'Partner account confirmation',
                    'tpl_partner_denied.tpl'                 => 'Partner account declined',
                    'tpl_partner_request.tpl'                => 'Request partner membership',
                    'tpl_passwordrecovery.tpl'               => 'Password recovery email',
                    'tpl_paymentnotification_affiliate.tpl'  => 'Affiliate payout notification',
                    'tpl_paymentnotification_be.tpl'         => 'New payment notification (user)',
                    'tpl_paymentnotification_fe.tpl'         => 'New payment notification (admin)',
                    'tpl_paymentnotification_subscriber.tpl' => 'Subscription payout notification',
                    'tpl_privatemessage.tpl'                 => 'Private message notification',
                    'tpl_sharefile.tpl'                      => 'Share files email',
                    'tpl_shareplaylist.tpl'                  => 'Share playlists email',
                    'tpl_subscribe.tpl'                      => 'User subscription notification',
                    'tpl_usernamerecovery.tpl'               => 'Username recovery email',
                    'tpl_welcome.tpl'                        => 'Welcome email',
                    'tpl_payoutnotification_token.tpl'       => 'Token payout notification',
                    'tpl_tokendonation_be.tpl'               => 'Token donation notification (admin)',
                    'tpl_tokendonation_fe.tpl'               => 'Token donation notification (user)',
                    'tpl_tokennotification_be.tpl'           => 'Token purchase notification (admin)',
                    'tpl_tokennotification_fe.tpl'           => 'Token purchase notification (user)',
                );

                foreach ($scanned_directory as $k => $v) {
                    $ht .= '<div class="list-' . ($s % 2 == 0 ? 'even' : 'odd') . '">';
                    $ht .= '<div class="vs-column thirds centered-text">' . $d[$v] . '</div>';
                    $ht .= '<div class="vs-column thirds centered-text">' . $v . '</div>';
                    $ht .= '<div class="vs-column thirds fit centered-text"><a href="javascript:;" title="" id="' . md5($v) . '" rel-type="tpl-edit-mail" class="popup black"><i class="icon-pencil" rel="tooltip" title="' . $language['backend.menu.entry1.sub10.tplfile.edit'] . '"></i></a></div>';
                    $ht .= '</div>';
                    $ht .= '<div class="clearfix"></div>';

                    $s += 1;
                }
                $ht .= '<div class="clearfix"></div>';
                $input_code .= $ht;
                break;
            case "footer_templates":
                $directory         = $cfg['ww_templates_dir'] . '/tpl_page';
                $scanned_directory = array_diff(scandir($directory), array('..', '.', '.htaccess'));
                $s                 = 1;

                $d = array(
                    'tpl_about.tpl'      => 'About page',
                    'tpl_adv.tpl'        => 'Empty page',
                    'tpl_blog.tpl'       => 'Default blog page',
                    'tpl_copyright.tpl'  => 'Copyright page',
                    'tpl_dmca.tpl'       => 'DMCA page',
                    'tpl_devel.tpl'      => 'Empty page',
                    'tpl_help.tpl'       => 'Help page',
                    'tpl_phpexample.php' => 'Page with PHP code',
                    'tpl_privacy.tpl'    => 'Privacy page',
                    'tpl_safety.tpl'     => 'Empty page',
                    'tpl_terms.tpl'      => 'Terms page',
                    'tpl_live.tpl'       => 'Live streaming help page',
                    'tpl_partner.tpl'    => 'Partner program help page',
                    'tpl_affiliate.tpl'  => 'Affiliate program help page',
                );

                foreach ($scanned_directory as $k => $v) {
                    $ht .= '<div class="list-' . ($s % 2 == 0 ? 'even' : 'odd') . '">';
                    $ht .= '<div class="vs-column thirds centered-text">' . $v . '</div>';
                    $ht .= '<div class="vs-column thirds centered-text">' . $d[$v] . '</div>';
                    $ht .= '<div class="vs-column thirds fit centered-text"><a href="javascript:;" title="" id="' . md5($v) . '" rel-type="tpl-edit-page" class="popup black"><i class="icon-pencil" rel="tooltip" title="' . $language['backend.menu.entry1.sub10.tplfile.edit'] . '"></i></a></div>';
                    $ht .= '</div>';
                    $ht .= '<div class="clearfix"></div>';

                    $s += 1;
                }
                $ht .= '<div class="clearfix"></div>';
                $input_code .= $ht;
                break;
            case "database_backup":
            case "file_backup":
                break;
            case "thumbs_video":
                $sw_on  = $language['frontend.global.switchon'];
                $sw_off = $language['frontend.global.switchoff'];

                $input_sel = VGenerate::simpleDivWrap('selector', '', '<select name="thumbs_method" class="select-input"><option' . ($cfg['thumbs_method'] == 'cons' ? ' selected="selected"' : null) . ' value="cons">' . $language['backend.menu.entry6.sub1.conv.thumbs.extract.cons'] . '</option><option' . ($cfg['thumbs_method'] == 'split' ? ' selected="selected"' : null) . ' value="split">' . $language['backend.menu.entry6.sub1.conv.thumbs.extract.split'] . '</option><option' . ($cfg['thumbs_method'] == 'rand' ? ' selected="selected"' : null) . ' value="rand">' . $language['backend.menu.entry6.sub1.conv.thumbs.extract.rand'] . '</option></select>');
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.thumbs.extract'] . '</label>', 'left-float wd200', 'thumbs_nr', 'text-input wd60', $cfg['thumbs_nr']);
                $input_code .= '<label>' . $language['backend.menu.entry6.sub1.conv.thumbs.extract.mode'] . '</label>' . $input_sel;
                break;
            case "conversion_image_type":
                $radio_check1 = $cfg[$input_type] == 1 ? 'checked="checked"' : null;
                $radio_check3 = $cfg[$input_type] == 3 ? 'checked="checked"' : null;

                $input_code .= '<div class="icheck-box">';
                $input_code .= '<input type="radio" ' . $radio_check1 . ' name="' . $input_type . '" value="1" onclick="$(\'#resize-options\').hide();"><label>' . $language['backend.menu.entry3.sub6.conv.s1'] . '</label><br>';
                $input_code .= '<input type="radio" ' . $radio_check3 . ' name="' . $input_type . '" value="3" onclick="$(\'#resize-options\').show();"><label>' . $language['backend.menu.entry3.sub6.conv.s3'] . '</label><br>';
                $input_code .= '</div>';
                $input_code .= '<div id="resize-options" style="display: block;">';
                $input_code .= '<div class="vs-column half"><label>width</label>' . VGenerate::basicInput('text', 'thanw', 'backend-text-input wd50 left-margin5', $cfg['conversion_image_from_w'], '') . '</div>';
                $input_code .= '<div class="vs-column half fit"><label>height</label> ' . VGenerate::basicInput('text', 'thanh', 'backend-text-input wd50', $cfg['conversion_image_from_h'], '') . '</div>';
                $input_code .= '<div class="vs-column full"><label>to</label></div>';
                $input_code .= '<div class="vs-column half"><label>width</label> ' . VGenerate::basicInput('text', 'tow', 'backend-text-input wd50', $cfg['conversion_image_to_w'], '') . '</div>';
                $input_code .= '<div class="vs-column half fit"><label>height</label> ' . VGenerate::basicInput('text', 'toh', 'backend-text-input wd50', $cfg['conversion_image_to_h'], '') . '</div>';
                $input_code .= '<div class="clearfix"></div>';
                $input_code .= '</div>';
                break;
            case "conversion_source_video":
                $radio_check1 = $cfg[$input_type] == 1 ? 'checked="checked"' : null;
                $radio_check2 = $cfg[$input_type] == 0 ? 'checked="checked"' : null;

                $input_code .= '<div class="row no-top-padding"><span class="left-float lh20"><input type="radio" ' . $radio_check1 . ' name="' . $input_type . '" value="1" /></span><span class="left-float lh20">' . $language['backend.menu.entry6.sub1.conv.vid.up.store'] . '</span></div>';
                $input_code .= '<div class="row no-top-padding"><span class="left-float lh20"><input type="radio" ' . $radio_check2 . ' name="' . $input_type . '" value="0" /></span><span class="left-float lh20">' . $language['backend.menu.entry6.sub1.conv.vid.up.del'] . '</span></div>';
                break;
            case "conversion_source_short":
                $radio_check1 = $cfg[$input_type] == 1 ? 'checked="checked"' : null;
                $radio_check2 = $cfg[$input_type] == 0 ? 'checked="checked"' : null;

                $input_code .= '<div class="row no-top-padding"><span class="left-float lh20"><input type="radio" ' . $radio_check1 . ' name="' . $input_type . '" value="1" /></span><span class="left-float lh20">' . $language['backend.menu.entry6.sub1.conv.short.up.store'] . '</span></div>';
                $input_code .= '<div class="row no-top-padding"><span class="left-float lh20"><input type="radio" ' . $radio_check2 . ' name="' . $input_type . '" value="0" /></span><span class="left-float lh20">' . $language['backend.menu.entry6.sub1.conv.short.up.del'] . '</span></div>';
                break;
            case "server_paths_video":
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.ffmpeg'] . '</label>', 'left-float', 'server_path_ffmpeg', 'text-input wd200', $cfg['server_path_ffmpeg']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.ffprobe'] . '</label>', 'left-float', 'server_path_ffprobe', 'text-input wd200', $cfg['server_path_ffprobe']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.qt'] . '</label>', 'left-float', 'server_path_qt', 'text-input wd200', $cfg['server_path_qt']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.yamdi'] . '</label>', 'left-float', 'server_path_yamdi', 'text-input wd200', $cfg['server_path_yamdi']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.php'] . '</label>', 'left-float', 'server_path_php', 'text-input wd200', $cfg['server_path_php']);
                $input_code .= self::settings_logConversion('log_video_conversion', '<label>' . $language['backend.menu.entry6.sub6.log.v.conv'] . '</label>');
                break;
            case "server_paths_short":
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.ffmpeg'] . '</label>', 'left-float', 'server_path_ffmpeg', 'text-input wd200', $cfg['server_path_ffmpeg']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.ffprobe'] . '</label>', 'left-float', 'server_path_ffprobe', 'text-input wd200', $cfg['server_path_ffprobe']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.qt'] . '</label>', 'left-float', 'server_path_qt', 'text-input wd200', $cfg['server_path_qt']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.yamdi'] . '</label>', 'left-float', 'server_path_yamdi', 'text-input wd200', $cfg['server_path_yamdi']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.php'] . '</label>', 'left-float', 'server_path_php', 'text-input wd200', $cfg['server_path_php']);
                $input_code .= self::settings_logConversion('log_short_conversion', '<label>' . $language['backend.menu.entry6.sub6.log.v.conv'] . '</label>');
                break;
            case "server_paths_audio":
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.lame'] . '</label>', 'left-float', 'server_path_lame', 'text-input wd200', $cfg['server_path_lame']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.ffmpeg'] . '</label>', 'left-float', 'server_path_ffmpeg', 'text-input wd200', $cfg['server_path_ffmpeg']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.php'] . '</label>', 'left-float', 'server_path_php', 'text-input wd200', $cfg['server_path_php']);
                $input_code .= self::settings_logConversion('log_audio_conversion', '<label>' . $language['backend.menu.entry6.sub6.log.a.conv'] . '</label>');
                break;
            case "server_paths_doc":
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub4.path.uno'] . '</label>', 'left-float', 'server_path_unoconv', 'text-input wd200', $cfg['server_path_unoconv']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub4.path.conv'] . '</label>', 'left-float', 'server_path_convert', 'text-input wd200', $cfg['server_path_convert']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd150', '<label>' . $language['backend.menu.entry6.sub1.conv.path.php'] . '</label>', 'left-float', 'server_path_php', 'text-input wd200', $cfg['server_path_php']);
                $input_code .= self::settings_logConversion('log_doc_conversion', '<label>' . $language['backend.menu.entry6.sub6.log.d.conv'] . '</label>');
                break;
            case "conversion_mp3":
                $check_on = $cfg['conversion_mp3_redo'] == 1 ? ' checked="checked"' : null;

                $input_code .= VGenerate::simpleDivWrap('', '', '<label>' . $language['backend.menu.entry6.sub1.conv.btrate.audio.option'] . '</label>' . VGenerate::basicInput('text', 'conversion_mp3_bitrate_audio', 'backend-text-input wd50', $cfg['conversion_mp3_bitrate'], '')) . '<br>';
                $input_code .= VGenerate::simpleDivWrap('', '', '<label>' . $language['backend.menu.entry6.sub1.conv.btrate.sample.option'] . '</label>' . VGenerate::basicInput('text', 'conversion_mp3_srate_audio', 'backend-text-input wd50', $cfg['conversion_mp3_srate'], ''));
                $input_code .= '<div class="icheck-box">';
                $input_code .= '<input type="checkbox" value="1" name="conversion_mp3_redo"' . $check_on . ' class=""><label>' . $language['backend.menu.entry6.sub1.conv.mp3.none'] . '</label>';
                $input_code .= '</div>';
                break;
            case "conversion_mp4_360p":
            case "conversion_mp4_480p":
            case "conversion_mp4_720p":
            case "conversion_mp4_1080p":
            case "conversion_mp4_ipad":
            case "conversion_vpx_360p":
            case "conversion_vpx_480p":
            case "conversion_vpx_720p":
            case "conversion_vpx_1080p":
            case "conversion_ogv_360p":
            case "conversion_ogv_480p":
            case "conversion_ogv_720p":
            case "conversion_ogv_1080p":
            case "conversion_flv_360p":
            case "conversion_flv_480p":
                $label_array = array(
                    "do_conversion" => $p1,
                    "flv_reencode"  => '<label>' . $language['backend.menu.entry6.sub1.conv.flv.option'] . '</label>',
                    "bitrate_mt"    => '<label>' . $language['backend.menu.entry6.sub1.conv.btrate.method.option'] . '</label>',
                    "bitrate_video" => '<label>' . $language['backend.menu.entry6.sub1.conv.btrate.video.option'] . '</label>',
                    "fps"           => '<label>' . $language['backend.menu.entry6.sub1.conv.fps.option'] . '</label>',
                    "resize"        => '<label>' . $language['backend.menu.entry6.sub1.conv.resize.option'] . '</label>',
                    "resize_w"      => '<label>' . $language['backend.menu.entry6.sub1.conv.resize.w.option'] . '</label>',
                    "resize_h"      => '<label>' . $language['backend.menu.entry6.sub1.conv.resize.h.option'] . '</label>',
                    "encoding"      => '<label>' . $language['backend.menu.entry6.sub1.conv.pass'] . '</label>',
                    "bitrate_audio" => '<label>' . $language['backend.menu.entry6.sub1.conv.btrate.audio.option'] . '</label>',
                    "srate_audio"   => '<label>' . $language['backend.menu.entry6.sub1.conv.btrate.sample.option'] . '</label>',
                );
                if ($input_type != 'conversion_flv_360p' and $input_type != 'conversion_flv_480p') {
                    unset($label_array['flv_reencode']);
                }

                $input_code .= self::conversionSettings($input_type, $label_array);
                break;
            case "pp_api":
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['backend.menu.members.entry1.sub1.ppapi.user'] . '</label>', 'left-float', 'paypal_api_user', 'login-input', $cfg['paypal_api_user']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['backend.menu.members.entry1.sub1.ppapi.pass'] . '</label>', 'left-float', 'paypal_api_pass', 'login-input', $cfg['paypal_api_pass']);
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['backend.menu.members.entry1.sub1.ppapi.sign'] . '</label>', 'left-float', 'paypal_api_sign', 'login-input', $cfg['paypal_api_sign']);
                break;
            case "payout_opts":
                $db_sub = VUseraccount::getProfileDetail('usr_sub_email');
                $db_sub = $_POST ? $class_filter->clr_str($_POST['account_payout_address_sub']) : $db_sub;

                $input_code = '<div class="pinfo"><p>' . $language['account.payout.address.tip'] . '</p></div><br>';
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.payout.address.sub'] . '</label>', 'left-float', 'account_payout_address_sub', 'login-input', $db_sub);
                break;
            case "email_opts":
                $input_code = '<div class="pinfo"><p>' . $language['account.email.address.tip'] . '</p></div><br>';
                $input_code .= '<p><label>' . $language['account.email.address.current'] . '</label><span class="grayText">' . VUserinfo::getUserEmail() . '</span></p>';
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.email.address.new'] . '</label>', 'left-float', 'account_email_address_new', 'login-input', ($class_filter->clr_str($_POST['account_email_address_new'])));
                $input_code .= VGenerate::sigleInputEntry('password', 'left-float lh25 wd140', '<label>' . $language['account.email.address.pass'] . '</label>', 'left-float p-relative', 'account_email_address_pass', 'login-input', '');
                $input_code .= $cfg['email_change_captcha'] == 1 ? '<div class="g-recaptcha" data-sitekey="' . $cfg['recaptcha_site_key'] . '"></div>' : null;
                $input_code .= VGenerate::simpleDivWrap('row left-float wdmax' . ($cfg['email_change_captcha'] == 1 ? ' no-top-padding1' : null), '', VGenerate::simpleDivWrap('left-float wd140 lh20', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float', '', '<button onfocus="blur();" value="1" type="button" class="email-change-button save-entry-button button-grey search-button form-button" name="send_button"><span>' . $language['account.email.address.send'] . '</span></button>'));
                $input_code .= VGenerate::declareJS('var main_url = "' . $cfg['main_url'] . '/"; $("#reload-captcha").bind("click", function () { $("#c-image").attr("src", main_url + "' . VHref::getKey('captcha') . '?extra=5&p=" + new Date().getTime());   });');
                $input_code .= $cfg['email_change_captcha'] == 1 ? '<script type="text/javascript" src="https://www.google.com/recaptcha/api.js"></script>' : null;
                break;
            case "email_notif":
                $input_code = '<div class="icheck-box all-notif"><input type="radio" ' . (VUseraccount::notificationCheckboxes() == 1 ? 'checked="checked"' : null) . ' name="email_notif" id="email_notif_on" value="1" /><label>' . $language['account.email.notif.site.text'] . '</label></div>';
                $input_code .= ($cfg['file_comments'] == 1 or $cfg['file_responses'] == 1) ? '<div class="icheck-box opt"><input type="checkbox" ' . VUseraccount::disabledCheckboxes() . ' ' . VUseraccount::entryCheckboxes('usr_mail_filecomment', 'db_accountuser') . ' name="usr_mail_filecomment" class="en-chk" value="1" /><label><span class="left-float lh20 en-chk-txt">' . $language['account.email.notif.site.ev1'] . '</span></label></div>' : null;
                $input_code .= $cfg['channel_comments'] == 1 ? '<div class="icheck-box opt"><input type="checkbox" ' . VUseraccount::disabledCheckboxes() . ' ' . VUseraccount::entryCheckboxes('usr_mail_chancomment', 'db_accountuser') . ' name="usr_mail_chancomment" class="en-chk" value="1" /><label><span class="left-float lh20 en-chk-txt">' . $language['account.email.notif.site.ev2'] . '</span></label></div>' : null;
                $input_code .= '<div class="icheck-box opt"><input type="checkbox" ' . VUseraccount::disabledCheckboxes() . ' ' . VUseraccount::entryCheckboxes('usr_mail_privmessage', 'db_accountuser') . ' name="usr_mail_privmessage" class="en-chk" value="1" /><label><span class="left-float lh20 en-chk-txt">' . $language['account.email.notif.site.ev3'] . '</span></label></div>';
                $input_code .= $cfg['approve_friends'] == 1 ? '<div class="icheck-box opt"><input type="checkbox" ' . VUseraccount::disabledCheckboxes() . ' ' . VUseraccount::entryCheckboxes('usr_mail_friendinv', 'db_accountuser') . ' name="usr_mail_friendinv" class="en-chk" value="1" /><label><span class="left-float lh20 en-chk-txt">' . $language['account.email.notif.site.ev4'] . '</span></label></div>' : null;
                $input_code .= $cfg['user_follows'] == 1 ? '<div class="icheck-box opt"><input type="checkbox" ' . VUseraccount::disabledCheckboxes() . ' ' . VUseraccount::entryCheckboxes('usr_mail_chanfollow', 'db_accountuser') . ' name="usr_mail_chanfollow" class="en-chk" value="1" /><label><span class="left-float lh20 en-chk-txt">' . $language['account.email.notif.site.ev6'] . '</span></label></div>' : null;
                $input_code .= $cfg['user_subscriptions'] == 1 ? '<div class="icheck-box opt"><input type="checkbox" ' . VUseraccount::disabledCheckboxes() . ' ' . VUseraccount::entryCheckboxes('usr_mail_chansub', 'db_accountuser') . ' name="usr_mail_chansub" class="en-chk" value="1" /><label><span class="left-float lh20 en-chk-txt">' . $language['account.email.notif.site.ev5'] . '</span></label></div>' : null;
                $input_code .= '<div class="icheck-box no-notif"><input type="radio" ' . (VUseraccount::notificationCheckboxes() == 0 ? 'checked="checked"' : null) . ' name="email_notif" id="email_notif_off" value="0" /><label><span class="left-float lh20">' . $language['account.email.notif.site.notext'] . '</span></label></div>';
                $extra_js = '$(".icheck-box.all-notif input").on("ifChecked", function(event){ $(".icheck-box.opt input").iCheck("enable"); });';
                $extra_js .= '$(".icheck-box.notif input").on("ifUnchecked", function(event){ $(".icheck-box.opt input").iCheck("disable"); });';
                $input_code .= VGenerate::declareJS($extra_js . '$("#email_notif_off").bind("click", function() { $("input.en-chk").prop("disabled", true); $("span.en-chk-txt").addClass("grayText"); });  $("#email_notif_on").bind("click", function() { $("input.en-chk").prop("disabled", false); $("span.en-chk-txt").removeClass("grayText"); }); ');
                break;
            case "email_notif_be":
                $input_code .= '<div class="icheck-box"><input type="radio" ' . (($cfg['backend_notification_signup'] == 1 or $cfg['backend_notification_upload'] == 1 or $cfg['backend_notification_payment'] == 1) ? 'checked="checked"' : null) . ' name="email_notif" id="email_notif_on" value="1" /><label>' . $language['account.email.notif.site.text'] . '</label></div>';
                $input_code .= '<div class="icheck-box opt"><input type="checkbox" ' . VUseraccount::disabledCheckboxes(1) . ' ' . VUseraccount::entryCheckboxes('backend_notification_payment', 'db_settings') . ' name="backend_notification_payment" class="en-chk" value="1" /><label class="en-chk-txt">' . $language['backend.menu.entry3.sub1.admin.notif.payments'] . '</label></div>';
                $input_code .= '<div class="icheck-box opt"><input type="checkbox" ' . VUseraccount::disabledCheckboxes(1) . ' ' . VUseraccount::entryCheckboxes('backend_notification_signup', 'db_settings') . ' name="backend_notification_signup" class="en-chk" value="1" /><label class="en-chk-txt">' . $language['backend.menu.entry3.sub1.admin.notif.members'] . '</label></div>';
                $input_code .= '<div class="icheck-box opt"><input type="checkbox" ' . VUseraccount::disabledCheckboxes(1) . ' ' . VUseraccount::entryCheckboxes('backend_notification_upload', 'db_settings') . ' name="backend_notification_upload" class="en-chk" value="1" /><label class="en-chk-txt">' . $language['backend.menu.entry3.sub1.admin.notif.uploads'] . '</label></div>';
                $input_code .= '<div class="icheck-box no-notif"><input type="radio" ' . ((($cfg['backend_notification_signup'] == 0 and $cfg['backend_notification_upload'] == 0 and $cfg['backend_notification_payment'] == 0)) ? 'checked="checked"' : null) . ' name="email_notif" id="email_notif_off" value="0" /><label>' . $language['account.email.notif.site.notext'] . '</label></div>';
                $extra_js = '$(".icheck-box.all-notif input").on("ifChecked", function(event){ $(".icheck-box.opt input").iCheck("enable"); });';
                $extra_js .= '$(".icheck-box.notif input").on("ifUnchecked", function(event){ $(".icheck-box.opt input").iCheck("disable"); });';
                $input_code .= VGenerate::declareJS($extra_js . '$("#email_notif_off").bind("click", function() { $("input.en-chk").prop("disabled", true); $("span.en-chk-txt").addClass("grayText"); });  $("#email_notif_on").bind("click", function() { $("input.en-chk").prop("disabled", false); $("span.en-chk-txt").removeClass("grayText"); }); ');
                break;
            case "email_subs":
                $u_info    = VUserinfo::getUserInfo($_SESSION['USER_ID']);
                $upd_check = $u_info['mail_updates'] == 1 ? 'checked="checked"' : null;
                $input_code .= '<div class="pinfo"><p>' . $cfg['website_shortname'] . $language['account.email.notif.subs.digest'] . '</p></div>';
                $input_code .= $cfg['user_subscriptions'] == 1 ? '<div class="icheck-box"><input type="radio"' . ($u_info['week_updates'] == 2 ? 'checked="checked"' : null) . ' name="send_updates" value="2" /><label>' . $language['account.email.notif.subs.week'] . '</label></div>' : null;
                $input_code .= $cfg['user_subscriptions'] == 1 ? '<div class="icheck-box"><input type="radio"' . ($u_info['week_updates'] == 1 ? 'checked="checked"' : null) . ' name="send_updates" value="1" /><label>' . $language['account.email.notif.subs.day'] . '</label></div>' : null;
                $input_code .= $cfg['user_subscriptions'] == 1 ? '<div class="icheck-box"><input type="radio"' . ($u_info['week_updates'] == 0 ? 'checked="checked"' : null) . ' name="send_updates" value="0" /><label>' . $language['account.email.notif.subs.none'] . '</label></div>' : null;
                $input_code .= '<div class="pinfo' . ($cfg['user_subscriptions'] == 1 ? ' top-padding10' : ' no-top-padding') . '"><br><p>' . $language['account.email.notif.subs.txt'] . '</p></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" ' . $upd_check . ' name="occasional_updates" value="1" /><label>' . $language['account.email.notif.subs.updates'] . '</label></div>';
                break;
            case "activity_sharing":
                global $cfg, $db;
                $ev         = $db->execute(sprintf("SELECT * FROM `db_trackactivity` WHERE `usr_id`='%s' LIMIT 1;", intval($_SESSION['USER_ID'])));
                $input_none = ($cfg['file_rating'] == 0 and $cfg['file_comments'] == 0 and $cfg['file_favorites'] == 0 and $cfg['user_subscriptions'] == 0 and (($cfg['video_module'] == 0 or $cfg['video_uploads'] == 0) and ($cfg['short_module'] == 0 or $cfg['short_uploads'] == 0) and ($cfg['live_uploads'] == 0 or $cfg['live_module'] == 0) and ($cfg['image_module'] == 0 or $cfg['image_uploads'] == 0) and ($cfg['audio_module'] == 0 or $cfg['audio_uploads'] == 0) and ($cfg['document_module'] == 0 or $cfg['document_uploads'] == 0))) ? '<span class="bold">' . $language['account.error.no.sharing'] . '</span>' : null;
                $input_none = ($ev->fields['log_upload'] == 0 and $ev->fields['log_rating'] == 0 and $ev->fields['log_filecomment'] == 0 and $ev->fields['log_fav'] == 0 and $ev->fields['log_subscribing'] == 0 and $ev->fields['log_following'] == 0) ? '<span class="bold">' . $language['account.error.no.sharing'] . '</span>' : $input_none;

                $input_code = '<div class="pinfo"><p>' . $language['account.activity.sharing.txt'] . '</p></div>';
                $input_code .= '<div class=""><label>' . $language['account.activity.sharing.include'] . $input_none . '</label></div>';
                $input_code .= ($ev->fields['log_upload'] == 1 and (($cfg['video_module'] == 1 and $cfg['video_uploads'] == 1) or ($cfg['short_module'] == 1 and $cfg['short_uploads'] == 1) or ($cfg['live_module'] == 1 and $cfg['live_uploads'] == 1) or ($cfg['image_module'] == 1 and $cfg['image_uploads'] == 1) or ($cfg['audio_module'] == 1 and $cfg['audio_uploads'] == 1) or ($cfg['document_module'] == 1 and $cfg['document_uploads'] == 1))) ? '<div class="icheck-box"><input type="checkbox" ' . VUseraccount::entryCheckboxes('share_upload') . ' name="share_upload" value="1" /><label>' . $language['account.activity.sharing.upload'] . '</label></div>' : null;
                $input_code .= ($ev->fields['log_rating'] == 1 and $cfg['file_rating'] == 1) ? '<div class="icheck-box"><input type="checkbox" ' . VUseraccount::entryCheckboxes('share_rating') . ' name="share_rating" value="1" /><label>' . $language['account.activity.sharing.like'] . '</label></div>' : null;
                $input_code .= ($ev->fields['log_filecomment'] == 1 and ($cfg['file_comments'] == 1 or $cfg['channel_comments'] == 1)) ? '<div class="icheck-box"><input type="checkbox" ' . VUseraccount::entryCheckboxes('share_filecomment') . ' name="share_filecomment" value="1" /><label>' . $language['account.activity.sharing.comment'] . '</label></div>' : null;
                $input_code .= ($ev->fields['log_fav'] == 1 and $cfg['file_favorites'] == 1) ? '<div class="icheck-box"><input type="checkbox" ' . VUseraccount::entryCheckboxes('share_fav') . ' name="share_fav" value="1" /><label>' . $language['account.activity.sharing.fav'] . '</label></div>' : null;
                $input_code .= ($ev->fields['log_responding'] == 1 and $cfg['file_responses'] == 1) ? '<div class="icheck-box"><input type="checkbox" ' . VUseraccount::entryCheckboxes('share_responding') . ' name="share_responding" value="1" /><label>' . $language['account.activity.sharing.resp'] . '</label></div>' : null;
                $input_code .= ($ev->fields['log_following'] == 1 and $cfg['user_follows'] == 1) ? '<div class="icheck-box"><input type="checkbox" ' . VUseraccount::entryCheckboxes('share_following') . ' name="share_following" value="1" /><label>' . $language['account.activity.sharing.follows'] . '</label></div>' : null;
                $input_code .= ($ev->fields['log_subscribing'] == 1 and $cfg['user_subscriptions'] == 1) ? '<div class="icheck-box"><input type="checkbox" ' . VUseraccount::entryCheckboxes('share_subscribing') . ' name="share_subscribing" value="1" /><label>' . $language['account.activity.sharing.subs'] . '</label></div>' : null;
                break;
            case "manage_account_pass":
                global $db;

                $ui         = $db->execute(sprintf("SELECT `oauth_password` FROM `db_accountuser` WHERE `usr_id`='%s' AND `oauth_uid` > '0' LIMIT 1;", (int) $_SESSION['USER_ID']));
                $up         = $ui->fields['oauth_password'];
                $input_code = '<div class="row"><span class="left-float lh20"><label>' . $language['account.manage.current.user'] . '</label>: <span class="grayText">' . $_SESSION['USER_NAME'] . '</span></div>';
                $input_code .= $up == 1 ? VGenerate::sigleInputEntry('password', 'left-float lh25 wd140', '<label>' . $language['account.manage.pass.verify'] . '</label>', 'left-float', 'account_manage_pass_verify', 'login-input', '') : null;
                $input_code .= VGenerate::sigleInputEntry('password', 'left-float lh25 wd140', '<label>' . $language['account.manage.pass.new'] . '</label>', 'left-float p-relative', 'account_manage_pass_new', 'login-input', '');
                $input_code .= VGenerate::sigleInputEntry('password', 'left-float lh25 wd140', '<label>' . $language['account.manage.pass.retype'] . '</label>', 'left-float p-relative', 'account_manage_pass_retype', 'login-input', '');
                $input_code .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh20', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float', '', '<button onfocus="blur();" value="1" type="button" class="change-button save-entry-button button-grey search-button form-button" name="change_button"><span>' . $language['account.manage.change.pass'] . '</span></button>'));
                break;
            case "manage_account_delete":
                $input_code = VGenerate::sigleInputEntry('password', 'left-float lh25 wd140', '<label>' . $language['account.manage.curr.pass'] . '</label>', 'left-float p-relative', 'account_manage_curr_pass', 'login-input', '');
                $input_code .= VGenerate::sigleInputEntry('textarea-on', 'left-float lh25 wd140', '<label>' . $language['account.manage.del.reason'] . '</label>', 'left-float', 'account_manage_del_reason', 'ta-input', '');
                $input_code .= '<div class="pinfo"><p>' . $language['account.manage.del.reason.txt'] . '</p></div>';
                $input_code .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh20', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float', '', '<button onfocus="blur();" value="1" type="button" class="purge-button save-entry-button button-grey search-button form-button" name="purge_button"><span>' . $language['account.manage.delete'] . '</span></button>'));
                break;
            case "profile_about":
                $user_theme = VUseraccount::getProfileDetail('usr_theme');
                $input_code = VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.profile.about.displayname'] . '</label>', 'left-float', 'account_profile_about_displayname', 'login-input', VUseraccount::getProfileDetail('usr_dname'));
                $input_code .= '<div class="pinfo"><p>' . $language['account.profile.about.theme'] . '</p></div>';
                $input_code .= '<div class="icheck-box"><input type="radio"' . ($user_theme == 'light' ? ' checked="checked"' : null) . ' name="default_theme" value="light" /><label>' . $language['account.profile.about.theme.light'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="radio"' . ($user_theme == 'dark' ? ' checked="checked"' : null) . ' name="default_theme" value="dark" /><label>' . $language['account.profile.about.theme.dark'] . '</label></div>';
                break;
            case "profile_details":
                $date         = explode('-', VUseraccount::getProfileDetail('usr_birthday'));
                $bdate_months = explode(',', $language['frontend.global.months']);
                foreach ($bdate_months as $mk => $m) {
                    $m_sel .= '<option value="' . (($mk + 1) < 10 ? '0' . ($mk + 1) : ($mk + 1)) . '"' . ($date[1] == ($mk + 1) ? ' selected="selected"' : null) . '>' . $m . '</option>';
                }
                for ($i = 1; $i <= 31; $i++) {
                    $d_sel .= '<option value="' . (($i) < 10 ? '0' . ($i) : ($i)) . '"' . ($date[2] == $i ? ' selected="selected"' : null) . '>' . $i . '</option>';
                }
                for ($y = 2012; $y >= 1900; $y--) {
                    $y_sel .= '<option value="' . $y . '"' . ($date[0] == $y ? ' selected="selected"' : null) . '>' . $y . '</option>';
                }

                $bdate_select = '<div class="row" onclick="$(\'#choices-account_profile_bdate_y_sel,#choices-account_profile_bdate_d_sel\').slideUp();">';
                $bdate_select .= '<select name="account_profile_bdate_m_sel" class="select-input wd100 account-select" onChange="$(\'#M-loc\').val(this.value);">' . $m_sel . '</select>';
                $bdate_select .= '<input type="hidden" name="account_profile_bdate_m" class="login-input" id="M-loc" value="' . $date[1] . '" />';
                $bdate_select .= '<input type="hidden" name="account_profile_bdate_m_tmp" class="login-input no-display" value="' . $date[1] . '" />';
                $bdate_select .= '</div>';

                $bdate_select .= '<div class="row" onclick="$(\'#choices-account_profile_bdate_y_sel,#choices-account_profile_bdate_m_sel\').slideUp();">';
                $bdate_select .= '<select name="account_profile_bdate_d_sel" class="select-input wd50 account-select" onChange="$(\'#D-loc\').val(this.value);">' . $d_sel . '</select>';
                $bdate_select .= '<input type="hidden" name="account_profile_bdate_d" class="login-input" id="D-loc" value="' . $date[2] . '" />';
                $bdate_select .= '<input type="hidden" name="account_profile_bdate_d_tmp" class="login-input no-display" value="' . $date[2] . '" />';
                $bdate_select .= '</div>';

                $bdate_select .= '<div class="row" onclick="$(\'#choices-account_profile_bdate_d_sel,#choices-account_profile_bdate_m_sel\').slideUp();">';
                $bdate_select .= '<select name="account_profile_bdate_y_sel" class="select-input wd75 account-select" onChange="$(\'#Y-loc\').val(this.value);">' . $y_sel . '</select>';
                $bdate_select .= '<input type="hidden" name="account_profile_bdate_y" class="login-input" id="Y-loc" value="' . $date[0] . '" />';
                $bdate_select .= '<input type="hidden" name="account_profile_bdate_y_tmp" class="login-input no-display" value="' . $date[0] . '" />';
                $bdate_select .= '</div>';
                $gender       = VUseraccount::getProfileDetail('usr_gender');
                $relation     = VUseraccount::getProfileDetail('usr_relation');
                $showage      = VUseraccount::getProfileDetail('usr_showage');
                $showage      = $showage == 1 ? $language['account.profile.age.array'][0] : $language['account.profile.age.array'][1];
                $radio_check1 = VUseraccount::getProfileDetail('usr_showage') == 1 ? 'checked="checked"' : null;
                $radio_check2 = VUseraccount::getProfileDetail('usr_showage') == 0 ? 'checked="checked"' : null;

                $input_code = VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.profile.personal.firstname'] . '</label>', 'left-float', 'account_profile_personal_firstname', 'login-input', VUseraccount::getProfileDetail('usr_fname'));
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.profile.personal.lastname'] . '</label>', 'left-float', 'account_profile_personal_lastname', 'login-input', VUseraccount::getProfileDetail('usr_lname'));
                $input_code .= VGenerate::sigleInputEntry('textarea-on', 'left-float lh25 wd140', '<label>' . $language['account.profile.about.describe'] . '</label>', 'left-float', 'account_profile_about_describe', 'ta-input', VUseraccount::getProfileDetail('usr_description'));
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.profile.about.phone'] . '</label>', 'left-float', 'account_profile_about_phone', 'login-input', VUseraccount::getProfileDetail('usr_phone'));
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.profile.about.fax'] . '</label>', 'left-float', 'account_profile_about_fax', 'login-input', VUseraccount::getProfileDetail('usr_fax'));
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.profile.about.website'] . '</label>', 'left-float', 'account_profile_about_website', 'login-input', VUseraccount::getProfileDetail('usr_website'));
                $input_code .= VGenerate::simpleDivWrap('row left-float wdmax no-top-padding', '', VGenerate::simpleDivWrap('left-float wd140 lh20 top-padding10', '', '<label>' . $language['frontend.signup.bday'] . '</label>') . VGenerate::simpleDivWrap('left-float selector', '', $bdate_select));
                $input_code .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh25', '', '<label>' . $language['account.profile.personal.gender'] . '</label>') . VGenerate::simpleDivWrap('left-float selector', '', '<select name="account_profile_personal_gender_sel" class="select-input wd100 account-select" onChange="$(\'#gen-loc\').val(this.value);">' . VGenerate::selectListOptions($language['account.profile.gender.array'], 'usr_gender') . '</select><input type="hidden" name="account_profile_personal_gender" class="login-input" id="gen-loc" value="' . $gender . '" /><input type="hidden" name="account_profile_personal_gender_tmp" class="login-input no-display" value="' . $gender . '" />'));
                $input_code .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh25', '', '<label>' . $language['account.profile.personal.rel'] . '</label>') . VGenerate::simpleDivWrap('left-float selector', '', '<select name="account_profile_personal_rel_sel" class="select-input wd100 account-select" onChange="$(\'#rel-loc\').val(this.value);">' . VGenerate::selectListOptions($language['account.profile.rel.array'], 'usr_relation') . '</select><input type="hidden" name="account_profile_personal_rel" class="login-input" id="rel-loc" value="' . $relation . '" /><input type="hidden" name="account_profile_personal_rel_tmp" class="login-input no-display" value="' . $relation . '" />'));
                $input_code .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh25', '', '<label>' . $language['account.profile.personal.age.yes'] . '</label>') . VGenerate::simpleDivWrap('left-float selector', '', '<select name="account_profile_personal_age_sel" class="select-input wd100 account-select" onChange="if(this.value == 1){$(\'#age-loc\').val(\'' . $language['account.profile.age.array'][0] . '\');}else{$(\'#age-loc\').val(\'' . $language['account.profile.age.array'][1] . '\');}">' . VGenerate::selectListOptions($language['account.profile.age.array'], 'usr_showage') . '</select><input type="hidden" name="account_profile_personal_age" class="login-input" id="age-loc" value="' . $showage . '" /><input type="hidden" name="account_profile_personal_age_tmp" class="login-input no-display" value="' . $showage . '" />'));
                break;
            case "profile_location":
                $input_code = VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.profile.location.town'] . '</label>', 'left-float', 'account_profile_location_town', 'login-input', VUseraccount::getProfileDetail('usr_town'));
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.profile.location.city'] . '</label>', 'left-float', 'account_profile_location_city', 'login-input', VUseraccount::getProfileDetail('usr_city'));
                $input_code .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['account.profile.location.zip'] . '</label>', 'left-float', 'account_profile_location_zip', 'login-input', VUseraccount::getProfileDetail('usr_zip'));
                $input_code .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh25', '', '<label>' . $language['account.profile.location.country'] . '</label>') . VGenerate::simpleDivWrap('left-float selector', '', VUseraccount::countryList()));
                break;
            case "profile_job":
                $input_code = VGenerate::sigleInputEntry('textarea-on', 'left-float lh25 wd140', '<label>' . $language['account.profile.job.occup'] . '</label>', 'left-float', 'account_profile_job_occup', 'ta-input', VUseraccount::getProfileDetail('usr_occupations'));
                $input_code .= VGenerate::sigleInputEntry('textarea-on', 'left-float lh25 wd140', '<label>' . $language['account.profile.job.companies'] . '</label>', 'left-float', 'account_profile_job_companies', 'ta-input', VUseraccount::getProfileDetail('usr_companies'));
                break;
            case "profile_education":
                $input_code = VGenerate::sigleInputEntry('textarea-on', 'left-float lh25 wd140', '<label>' . $language['account.profile.education.school'] . '</label>', 'left-float', 'account_profile_education_school', 'ta-input', VUseraccount::getProfileDetail('usr_schools'));
                break;
            case "profile_interests":
                $input_code = VGenerate::sigleInputEntry('textarea-on', 'left-float lh25 wd140', '<label>' . $language['account.profile.interests'] . '</label>', 'left-float', 'account_profile_interests', 'ta-input', VUseraccount::getProfileDetail('usr_interests'));
                $input_code .= VGenerate::sigleInputEntry('textarea-on', 'left-float lh25 wd140', '<label>' . $language['account.profile.interests.movies'] . '</label>', 'left-float', 'account_profile_interests_movies', 'ta-input', VUseraccount::getProfileDetail('usr_movies'));
                $input_code .= VGenerate::sigleInputEntry('textarea-on', 'left-float lh25 wd140', '<label>' . $language['account.profile.interests.music'] . '</label>', 'left-float', 'account_profile_interests_music', 'ta-input', VUseraccount::getProfileDetail('usr_music'));
                $input_code .= VGenerate::sigleInputEntry('textarea-on', 'left-float lh25 wd140', '<label>' . $language['account.profile.interests.books'] . '</label>', 'left-float', 'account_profile_interests_books', 'ta-input', VUseraccount::getProfileDetail('usr_books'));
                break;
            case "offline_slides":
                $input_code .= VGenerate::offlineSettings();
                break;
            case "social_media_links":
                $sml = unserialize($cfg['social_media_links']);

                $input_tpl = '<div id="sm-#NR#">';
                $input_tpl .= '<div id="url-entry#NR#" class="sm-url-entry">';
                $input_tpl .= '<a href="javascript:;" onclick="$(this).parent().next().stop().slideToggle(200)">#NR#. #V1#</a> - ';
                $input_tpl .= '<label><a href="javascript:;" onclick="$(this).parent().parent().parent().next().stop().detach();$(this).parent().parent().parent().detach()">' . $language['frontend.global.delete.small'] . '</a></label>';
                $input_tpl .= '</div>';
                $input_tpl .= '<div id="url-entry-details#NR#" class="url-entry-details" rel-id="#NR#" style="display:none">';
                $input_tpl .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label style="margin-top:0">' . $language['backend.menu.entry2.sub1.sm.title'] . '</label>', 'left-float', 'sml[#NR#][title]', 'login-input', '#V1#');
                $input_tpl .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['backend.menu.entry2.sub1.sm.url'] . '</label>', 'left-float', 'sml[#NR#][url]', 'login-input', '#V2#');
                $input_tpl .= VGenerate::sigleInputEntry('text', 'left-float lh25 wd140', '<label>' . $language['backend.menu.entry2.sub1.sm.icon'] . '</label>', 'left-float', 'sml[#NR#][icon]', 'login-input', '#V3#');
                $input_tpl .= '</div>';
                $input_tpl .= '</div>';

                $input_code = '<script type="text/javascript">var ht="' . str_replace('"', "'", $input_tpl) . '"</script>';
                $input_code .= '<a href="javascript:;" class="place-right sml-add">' . $language['backend.menu.entry2.sub1.sm.add'] . '</a><div class="clearfix"></div>';
                $input_code .= '<div id="url-entry-details-list">';
                if (isset($sml[1]['title'])) {
                    foreach ($sml as $i => $vals) {
                        $l_title = is_array($sml) ? $sml[$i]['title'] : null;
                        $l_url   = is_array($sml) ? $sml[$i]['url'] : null;
                        $l_icon  = is_array($sml) ? $sml[$i]['icon'] : null;

                        $input_code .= str_replace(array('#NR#', '#V1#', '#V2#', '#V3#'), array($i, $l_title, $l_url, $l_icon), $input_tpl);
                    }
                }
                $input_code .= '</div>';
                break;
            case "text": //text input
            case "text-perm":
            case "password":
                switch ($input_name) {
                    case "backend_menu_entry2_sub3_sessname":
                    case "backend_menu_entry2_sub3_sesslife":
                        $wd_class = 'wd200';
                        break;
                    case "backend_menu_entry1_sub2_be_passrec_link":
                    case "backend_menu_entry1_sub2_fe_passrec_link":
                    case "backend_menu_entry1_sub4_messaging_limit":
                    case "backend_menu_members_entry2_sub1_types":
                    case "backend_menu_entry1_sub6_comments_cons":
                    case "backend_menu_entry1_sub7_file_multi":
                    case "backend_menu_entry1_sub6_comments_cons_c":
                    case "backend_menu_entry1_sub6_comments_cons_f":
                        $wd_class  = 'wd70';
                        $col_class = 'on_off';
                        break;
                    default:
                        $wd_class  = 'wd350';
                        $col_class = 'regular';
                        break;
                }
                $input_code = ($input_name == 'backend_menu_members_entry2_sub1_avatar' or $input_name == 'backend_menu_members_entry2_sub1_bg') ? '<label>' . $language['backend.menu.members.entry2.sub1.allowed'] . '</label>' : null;
                $input_code .= VGenerate::basicInput($input_type, $input_name, 'backend-text-input ' . $wd_class, $input_value, $entry_id . '-input');
                $input_code .= ($input_name == 'backend_menu_members_entry2_sub1_avatar' or $input_name == 'backend_menu_members_entry2_sub1_bg') ? '<label>' . $language['backend.menu.members.entry2.sub1.max'] . ' ' . $language['frontend.sizeformat.mb'] . '</label>' . VGenerate::basicInput($input_type, $input_name . '_size', 'backend-text-input wd70', ($input_name == 'backend_menu_members_entry2_sub1_bg' ? $cfg['channel_bg_max_size'] : $cfg['user_image_max_size']), $entry_id . '-input2') : null;
                break;

            case "textarea": //textarea
                global $cfg;

                switch ($input_name) {
                    case "backend_menu_section_IPlist":$sw_value = $cfg['list_ip_signup'];
                        break;
                    case "backend_menu_entry2_sub4_IPlist":$sw_value = $cfg['list_ip_access'];
                        break;
                    case "backend_menu_entry2_sub4_IPlist_be":$sw_value = $cfg['list_ip_backend'];
                        break;
                    case "backend_menu_entry1_sub1_maillist":$sw_value = $cfg['list_email_domains'];
                        break;
                    case "backend_menu_entry1_sub1_userlist":$sw_value = $cfg['list_reserved_users'];
                        break;
                    case "backend_menu_entry1_sub1_terms_info":$sw_value = $cfg['list_signup_terms'];
                        break;
                }

                $input_off  = ($sw_value != '' and php_sapi_name() != 'cgi' and (VFileinfo::getPermissions($sw_value) != '0666' and VFileinfo::getPermissions($sw_value) != '0777')) ? 'textarea-off' : ($sw_value == '' ? 'textarea' : 'textarea-on');
                $input_code = '<div class="">' . VGenerate::basicInput($input_off, $input_name, 'backend-textarea-input wd350', $input_value, $entry_id . '-input') . '</div>';
                $input_code .= $sw_value != '' ? '<div class="top-padding2">' . VGenerate::basicInput('text-perm', $input_name . '_path', 'backend-text-input wd350', $sw_value) . '</div>' : null;
                break;
            case "site_emails": //email addresses
                $input_code = '<div class="vs-column half"><label>' . $language['backend.menu.entry3.sub1.sitemail'] . '</label>' . VGenerate::basicInput('text', 'backend_menu_entry3_sub1_sitemail', 'backend-text-input wd150', $cfg['website_email']) . '</div>';
                $input_code .= '<div class="vs-column half fit"><label>' . $language['backend.menu.entry3.sub1.fromname'] . '</label>' . VGenerate::basicInput('text', 'backend_menu_entry3_sub1_sitemail_from', 'backend-text-input wd140', $cfg['website_email_fromname']) . '</div>';
                $input_code .= '<div class="vs-column half"><label>' . $language['backend.menu.entry3.sub1.adminmail'] . '</label>' . VGenerate::basicInput('text', 'backend_menu_entry3_sub1_adminmail', 'backend-text-input wd150', $cfg['backend_email']) . '</div>';
                $input_code .= '<div class="vs-column half fit"><label>' . $language['backend.menu.entry3.sub1.fromname'] . '</label>' . VGenerate::basicInput('text', 'backend_menu_entry3_sub1_adminmail_from', 'backend-text-input wd140', $cfg['backend_email_fromname']) . '</div>';
                $input_code .= '<div class="vs-column half"><label>' . $language['backend.menu.entry3.sub1.noreplymail'] . '</label>' . VGenerate::basicInput('text', 'backend_menu_entry3_sub1_noreplymail', 'backend-text-input wd150', $cfg['noreply_email']) . '</div>';
                $input_code .= '<div class="vs-column half fit"><label>' . $language['backend.menu.entry3.sub1.fromname'] . '</label>' . VGenerate::basicInput('text', 'backend_menu_entry3_sub1_noreplymail_from', 'backend-text-input wd140', $cfg['noreply_email_fromname']) . '</div>';
                $input_code .= '<div class="clearfix"></div>';
                break;

            case "pmeth": //payment methods
                $pp_c       = $cfg['paypal_payments'] == 1 ? 'checked="checked"' : null;
                $mb_c       = $cfg['moneybookers_payments'] == 1 ? 'checked="checked"' : null;
                $input_code = VGenerate::simpleDivWrap('row left-float wdmax no-top-padding', '', VGenerate::simpleDivWrap('left-float', '', '<input type="checkbox" ' . $mb_c . ' name="backend_menu_members_entry1_sub1_m2" value="1" />') . VGenerate::simpleDivWrap('left-float lh20', '', $language['backend.menu.members.entry1.sub1.m2']));
                $input_code .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float', '', '<input type="checkbox" ' . $pp_c . ' name="backend_menu_members_entry1_sub1_m1" value="1" />') . VGenerate::simpleDivWrap('left-float lh20', '', $language['backend.menu.members.entry1.sub1.m1']));
                break;

            case "psettings": //payout setup
                $input_code = '<span>Each affiliate will be paid <span id="s-pc-off">' . round((($cfg['affiliate_payout_share'] * $cfg['affiliate_payout_figure']) / 100), 2) . '</span> <span id="s-cr">' . $cfg['affiliate_payout_currency'] . '</span> for every <span id="s-pv">' . $cfg['affiliate_payout_units'] . '</span> unique video views</span>';
                break;

            case "affiliate_requirements": //affiliate requirements
                $input_code = '<div class="selector"><label>' . $language['backend.menu.af.requirements.min'] . '</label><input type="text" class="backend-text-input" name="backend_menu_af_requirements_min" value="' . $cfg['affiliate_requirements_min'] . '"><select name="backend_menu_af_requirements_type" class="backend-select-input"><option value="1"' . ($cfg['affiliate_requirements_type'] == '1' ? ' selected="selected"' : null) . '>' . $language['backend.menu.af.requirements.min.c1'] . '</option><option value="2"' . ($cfg['affiliate_requirements_type'] == '2' ? ' selected="selected"' : null) . '>' . $language['backend.menu.af.requirements.min.c2'] . '</option><option value="3"' . ($cfg['affiliate_requirements_type'] == '3' ? ' selected="selected"' : null) . '>' . $language['backend.menu.af.requirements.min.c3'] . '</option><option value="4"' . ($cfg['affiliate_requirements_type'] == '4' ? ' selected="selected"' : null) . '>' . $language['backend.menu.af.requirements.min.c4'] . '</option></select></div>';
                break;

            case "partner_requirements": //partner requirements
                $input_code = '<div class="selector"><label>' . $language['backend.menu.af.requirements.min'] . '</label><input type="text" class="backend-text-input" name="backend_menu_pt_requirements_min" value="' . $cfg['partner_requirements_min'] . '"><select name="backend_menu_pt_requirements_type" class="backend-select-input"><option value="1"' . ($cfg['partner_requirements_type'] == '1' ? ' selected="selected"' : null) . '>' . $language['backend.menu.af.requirements.min.c1'] . '</option><option value="2"' . ($cfg['partner_requirements_type'] == '2' ? ' selected="selected"' : null) . '>' . $language['backend.menu.af.requirements.min.c2'] . '</option><option value="3"' . ($cfg['partner_requirements_type'] == '3' ? ' selected="selected"' : null) . '>' . $language['backend.menu.af.requirements.min.c3'] . '</option><option value="4"' . ($cfg['partner_requirements_type'] == '4' ? ' selected="selected"' : null) . '>' . $language['backend.menu.af.requirements.min.c4'] . '</option></select></div>';
                break;

            case "uformat": //username format
                global $cfg;

                $dott_check = $cfg['username_format_dott'] == 1 ? 'checked="checked"' : null;
                $dash_check = $cfg['username_format_dash'] == 1 ? 'checked="checked"' : null;
                $usc_check  = $cfg['username_format_underscore'] == 1 ? 'checked="checked"' : null;

                $input_code = '<div class="icheck-box"><input type="checkbox" ' . $dott_check . ' name="backend_menu_entry1_sub1_uformat_t3" value="1" /><label>' . $language['backend.menu.entry1.sub1.uformat.t3'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" ' . $dash_check . ' name="backend_menu_entry1_sub1_uformat_t4" value="1" /><label>' . $language['backend.menu.entry1.sub1.uformat.t4'] . '</label></div>';
                $input_code .= '<div class="icheck-box"><input type="checkbox" ' . $usc_check . ' name="backend_menu_entry1_sub1_uformat_t5" value="1" /><label>' . $language['backend.menu.entry1.sub1.uformat.t5'] . '</label></div>';
                break;

            case "switch": // switch on/off open/closed
            case "switch_types": // switch on/off open/closed
                switch ($input_name) {
                    default:
                        $sw_on  = $language['frontend.global.switchon'];
                        $sw_off = $language['frontend.global.switchoff'];
                        break;
                    case "backend_menu_entry1_sub7_file_category":
                        $sw_on  = 'auto';
                        $sw_off = 'manual';
                        break;
                    case "backend_menu_section_access":
                    case "backend_menu_entry1_sub2_fe_passrec":
                    case "backend_menu_entry1_sub2_be_passrec":
                    case "backend_menu_entry1_sub2_fe_userrec":
                    case "backend_menu_entry1_sub2_be_userrec":
                    case "backend_menu_entry1_sub3_be_signin":
                    case "backend_menu_entry1_sub3_fe_signin":
                        $sw_on  = $language['frontend.global.sopen'];
                        $sw_off = $language['frontend.global.sclosed'];
                        break;
                }
                $sel_on    = $input_value == 1 ? 'selected' : null;
                $sel_off   = $input_value == 0 ? 'selected' : null;
                $check_on  = $input_value == 1 ? 'checked="checked"' : null;
                $check_off = $input_value == 0 ? 'checked="checked"' : null;

                if ($input_name == 'backend_menu_entry1_sub7_file_category') {
                    $sel_on    = $input_value == 'auto' ? 'selected' : null;
                    $sel_off   = $input_value == 'manual' ? 'selected' : null;
                    $check_on  = $input_value == 'auto' ? 'checked="checked"' : null;
                    $check_off = $input_value == 'manual' ? 'checked="checked"' : null;
                }

                switch ($input_name) {
                    case "backend_menu_members_entry1_sub1_pplog":
                        $input_code .= '<div class="row top-padding5">' . VGenerate::basicInput('text-perm', $input_name . '_path', 'backend-text-input wd350', $cfg['paypal_log_file']) . '</div>';
                        break;
                    case "backend_menu_entry2_sub4_email":
                        $input_code .= '<div class="row top-padding5">' . VGenerate::basicInput('text-perm', $input_name . '_path', 'backend-text-input wd350', 'f_data/data_logs/log_mail/' . date("Y.m.d") . '/.mailer.log') . '</div>';
                        break;
                    case "backend_menu_entry1_sub7_file_opt_del":
                        break;
                    case "backend_menu_entry1_sub7_file_opt_down":
                        break;
                }

                $input_code = VGenerate::entrySwitch($entry_id, $entry_title, $sel_on, $sel_off, $sw_on, $sw_off, $input_name, $check_on, $check_off, $col_type, $has_settings);
                break;

            case "minmax": //text inputs for min, max.
                switch ($input_value) {
                    case "comment_length":
                        $min_val  = $cfg['comment_min_length'];
                        $max_val  = $cfg['comment_max_length'];
                        $min_lang = $language['backend.menu.section.minlen'];
                        $max_lang = $language['backend.menu.section.maxlen'];
                        break;
                    case "file_comment_length":
                        $min_val  = $cfg['file_comment_min_length'];
                        $max_val  = $cfg['file_comment_max_length'];
                        $min_lang = $language['backend.menu.section.minlen'];
                        $max_lang = $language['backend.menu.section.maxlen'];
                        break;
                    case "username_length":
                        $min_val  = $cfg['signup_min_username'];
                        $max_val  = $cfg['signup_max_username'];
                        $min_lang = $language['backend.menu.section.minlen'];
                        $max_lang = $language['backend.menu.section.maxlen'];
                        break;
                    case "password_length":
                        $min_val  = $cfg['signup_min_password'];
                        $max_val  = $cfg['signup_max_password'];
                        $min_lang = $language['backend.menu.section.minlen'];
                        $max_lang = $language['backend.menu.section.maxlen'];
                        break;
                    case "signup_age":
                        $min_val  = $cfg['signup_min_age'];
                        $max_val  = $cfg['signup_max_age'];
                        $min_lang = $language['backend.menu.entry1.sub1.datemin'];
                        $max_lang = $language['backend.menu.entry1.sub1.datemax'];
                        break;
                }

                $input_code = '<div class="left-float"><ul class="ul-no-list">';
                $input_code .= '<li class="lh20"><label>' . $min_lang . ': </label>' . VGenerate::basicInput('text', $input_name . '_min', 'backend-text-input wd50', $min_val) . '</li>';
                $input_code .= '<li class="lh20 top-padding5"><label>' . $max_lang . ': </label>' . VGenerate::basicInput('text', $input_name . '_max', 'backend-text-input wd50', $max_val) . '</li>';
                $input_code .= '</ul></div>';
                break;

            case "select": //select lists
                switch ($input_name) {
                    case 'backend_menu_entry1_sub1_captchalevel':$from = $cfg['signup_captcha_level'];
                        break;
                    case 'backend_menu_entry1_sub2_be_passrec_lev':$from = $cfg['backend_password_recovery_captcha_level'];
                        break;
                    case 'backend_menu_entry1_sub2_fe_passrec_lev':$from = $cfg['frontend_password_recovery_captcha_level'];
                        break;
                    case 'backend_menu_entry1_sub2_be_userrec_lev':$from = $cfg['backend_username_recovery_captcha_level'];
                        break;
                    case 'backend_menu_entry1_sub2_fe_userrec_lev':$from = $cfg['frontend_username_recovery_captcha_level'];
                        break;
                    case 'backend_menu_entry1_sub5_em_captcha_lev':$from = $cfg['email_change_captcha_level'];
                        break;
                    case 'backend_menu_entry2_sub6_admin_left':$from = $cfg['backend_leftmenu'];
                        break;
                    case 'backend_menu_af_p_currency':$from = 'USD';
                        break;
                }

                switch ($input_name) {
                    case "backend_menu_af_p_currency":
                        global $class_language;
                        include_once $class_language->setLanguageFile('backend', 'language.members.entries');
                        $_currency = explode(',', $language['supported_currency_names']);
                        foreach ($_currency as $v) {
                            $sel_opts .= '<option value="' . $v . '"' . ($v == $input_value ? ' selected="selected"' : null) . '>' . $v . '</option>';
                        }
                        break;
                    case "backend_menu_entry2_sub3_timezone": //timezone
                        include_once 'f_core/config.timezones.php';

                        $sel_cls = 'wd200 ';
                        foreach ($_timezone as $v) {
                            $sel_opts .= '<option value="' . $v . '"' . ($v == $input_value ? ' selected="selected"' : null) . '>' . $v . '</option>';
                        }
                        break;
                    case "backend_menu_entry3_sub1_mtype": //mail service
                        $sw_on     = $language['frontend.global.switchon'];
                        $sw_off    = $language['frontend.global.switchoff'];
                        $sel_on    = $cfg['mail_smtp_auth'] == 'true' ? 'selected' : null;
                        $sel_off   = $cfg['mail_smtp_auth'] == 'false' ? 'selected' : null;
                        $check_on  = $cfg['mail_smtp_auth'] == 'true' ? 'checked="checked"' : null;
                        $check_off = $cfg['mail_smtp_auth'] == 'false' ? 'checked="checked"' : null;

                        $d_sel_on    = $cfg['mail_debug'] == 1 ? 'selected' : null;
                        $d_sel_off   = $cfg['mail_debug'] == 0 ? 'selected' : null;
                        $d_check_on  = $cfg['mail_debug'] == 1 ? 'checked="checked"' : null;
                        $d_check_off = $cfg['mail_debug'] == 0 ? 'checked="checked"' : null;

                        $sel_opts  = '<option value="phpmail"' . VGenerate::selOptionCheck($cfg['mail_type'], '') . '>' . $language['backend.menu.entry3.sub1.mphp'] . '</option><option value="sendmail"' . VGenerate::selOptionCheck($cfg['mail_type'], 'sendmail') . '>' . $language['backend.menu.entry3.sub1.msendmail'] . '</option><option value="smtp"' . VGenerate::selOptionCheck($cfg['mail_type'], 'smtp') . '>' . $language['backend.menu.entry3.sub1.smtp'] . '</option>';
                        $pref_opts = '<option value="default"' . VGenerate::selOptionCheck($cfg['mail_smtp_prefix'], '') . '>' . $language['backend.menu.entry3.sub1.smtp.pref.def'] . '</option><option value="ssl"' . VGenerate::selOptionCheck($cfg['mail_smtp_prefix'], 'ssl') . '>' . $language['backend.menu.entry3.sub1.smtp.pref.ssl'] . '</option><option value="tls"' . VGenerate::selOptionCheck($cfg['mail_smtp_prefix'], 'tls') . '>' . $language['backend.menu.entry3.sub1.smtp.pref.tls'] . '</option>';

                        $sm_path = VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.menu.entry3.sub1.msmpath'] . '</label>', 'left-float', 'backend_menu_entry3_sub1_msmpath', 'backend-text-input wd300', $cfg['mail_sendmail_path']);
                        $smtp    = VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.menu.entry3.sub1.smtp.host'] . '</label>', 'left-float', 'backend_menu_entry3_sub1_smtp_host', 'backend-text-input wd300', $cfg['mail_smtp_host']);
                        $smtp .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.menu.entry3.sub1.smtp.port'] . '</label>', 'left-float', 'backend_menu_entry3_sub1_smtp_port', 'backend-text-input wd300', $cfg['mail_smtp_port']);
                        $smtp .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh20', '', '<label>' . $language['backend.menu.entry3.sub1.smtp.auth'] . '</label>') . VGenerate::simpleDivWrap('left-float', '', VGenerate::entrySwitch($entry_id, '', $sel_on, $sel_off, $sw_on, $sw_off, 'backend_menu_entry3_sub1_smtp_auth', $check_on, $check_off)));
                        $smtp .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.menu.entry3.sub1.smtp.user'] . '</label>', 'left-float', 'backend_menu_entry3_sub1_smtp_user', 'backend-text-input wd300', $cfg['mail_smtp_username']);
                        $smtp .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh20', '', '<label>' . $language['backend.menu.entry3.sub1.smtp.pass'] . '</label>') . VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('password', 'backend_menu_entry3_sub1_smtp_pass', 'backend-text-input wd300', $language['backend.menu.entry3.sub1.pass'])));
                        $smtp .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh20', '', '<label>' . $language['backend.menu.entry3.sub1.smtp.pref'] . '</label>') . VGenerate::simpleDivWrap('selector', '', '<select name="backend_menu_entry3_sub1_smtp_pref" class="backend-select-input">' . $pref_opts . '</select>'));
                        $smtp .= VGenerate::simpleDivWrap('row left-float wdmax', '', VGenerate::simpleDivWrap('left-float wd140 lh20', '', '<label>' . $language['backend.menu.entry3.sub1.smtp.debug'] . '</label>') . VGenerate::simpleDivWrap('left-float', '', VGenerate::entrySwitch($entry_id . '-debug', '', $d_sel_on, $d_sel_off, $sw_on, $sw_off, 'backend_menu_entry3_sub1_smtp_debug', $d_check_on, $d_check_off)));
                        $_html = VGenerate::simpleDivWrap('left-float' . ($cfg['mail_type'] == 'sendmail' ? '' : ' no-display'), 'sm-path-wrap', $sm_path); //sendmail
                        $_html .= VGenerate::simpleDivWrap('left-float' . ($cfg['mail_type'] == 'smtp' ? '' : ' no-display'), 'smtp-wrap', $smtp); //smtp

                        $js = 'onchange="if(this.selectedIndex == 1) { closeDiv(\'smtp-wrap\'); openDiv(\'sm-path-wrap\'); } else if(this.selectedIndex == 2) { openDiv(\'smtp-wrap\'); closeDiv(\'sm-path-wrap\'); } else if(this.selectedIndex == 0) { closeDiv(\'smtp-wrap\'); closeDiv(\'sm-path-wrap\'); }"';
                        break;

                    case "backend_menu_entry2_sub6_admin_left": //admin left nav
                        $sel_opts = '<option value="group"' . VGenerate::selOptionCheck($from, 'group') . '>' . $language['backend.menu.entry2.sub6.admin.left.group'] . '</option><option value="list"' . VGenerate::selOptionCheck($from, 'list') . '>' . $language['backend.menu.entry2.sub6.admin.left.list'] . '</option>';
                        $sel_cls  = 'wd200 ';
                        $_html    = '';
                        break;

                    default:
                        $sel_opts = '<option value="easy"' . VGenerate::selOptionCheck($from, 'easy') . '>' . $language['backend.menu.entry1.sub1.captchaeasy'] . '</option><option value="normal"' . VGenerate::selOptionCheck($from, 'normal') . '>' . $language['backend.menu.entry1.sub1.captchanorm'] . '</option><option value="hard"' . VGenerate::selOptionCheck($from, 'hard') . '>' . $language['backend.menu.entry1.sub1.captchahard'] . '</option>';
                        $_html    = '';
                        break;
                }

                $input_code = VGenerate::simpleDivWrap('selector', '', '<select name="' . $input_name . '" class="' . $sel_cls . 'backend-select-input" ' . $js . '>' . $sel_opts . '</select>');
                $input_code .= $_html;
                break;
        }

        $switch_val = $section == 'fe' ? 0 : $cfg['keep_entries_open'];
        switch ($switch_val) {
            case "1":$_class = array(" active", "block", "none", "inline");
                $smarty->assign('keep_entries_open', 1);
                break;
            case "0":$_class = array("", "none", "block", "none");
                $smarty->assign('keep_entries_open', 0);
                break;
        }

        switch ($_GET['s']) {
            case "account-menu-entry2":
            case "account-menu-entry4":
            case "account-menu-entry5":
            case "account-menu-entry6":
            case "backend-menu-entry10-sub2":
                $show_tooltip = 0;
                break;
            default:$show_tooltip = 1;
        }

        $bb = $bottom_border == 1 ? 'bottom-border' : ($bottom_border == 2 ? 'double-bottom-border' : null);

        $html = '
        <ul class="responsive-accordion responsive-accordion-default bm-larger">
            <li>
                <div class="responsive-accordion-head jc-sb' . $_class[0] . '">
                    <div class="d-flex">
                        <span>' . $language[$entry_title] . '</span>
                        ' . ($show_tooltip == 1 ? str_replace('##TTICON##', '<i class="responsive-accordion-tip fa-fw iconBe-info"></i>', $tooltip_text) : null) . '
                    </div>
                    <div class="d-flex">
                        <i class="fa fa-chevron-down responsive-accordion-plus fa-fw iconBe-chevron-down" style="display: ' . $_class[2] . ';"></i>
                        <i class="fa fa-chevron-up responsive-accordion-minus fa-fw iconBe-chevron-up" style="display: ' . $_class[3] . ';"></i>
                    </div>
                </div>
                <div class="responsive-accordion-panel' . $_class[0] . '" style="display: ' . $_class[1] . ';">' . VGenerate::simpleDivWrap('ct-entry-details', $entry_id, VGenerate::simpleDivWrap('left-float', '', $input_code), ($_GET['s'] == 'backend-menu-entry3-sub9' ? 'padding-left: 0px;' : null)) . '</div>
            </li>
        </ul>
    ';

        return $html;
    }
}
