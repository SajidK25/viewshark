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

class VbePlayers
{
    /* file players select lists */
    public function player_selectList($name, $selected = '', $opt)
    {
        $html = '<div class="selector"><select name="' . $name . '" class="select-input wd150">';
        foreach ($opt as $k => $v) {
            $html .= '<option value="' . $k . '"' . ($k == $selected ? ' selected="selected"' : null) . '>' . $v . '</option>';
        }
        $html .= '</select></div>';

        return $html;
    }
    /* get player type */
    public function playerType()
    {
        global $class_filter;

        $_s = $class_filter->clr_str($_GET['s']);
        switch ($_s) {
            case "backend-menu-entry11-sub1":
                $_db = 'vjs_local';
                break;
            case "backend-menu-entry11-sub2":
                $_db = 'vjs_embed';
                break;
            case "backend-menu-entry12-sub1":
                $_db = 'jw_local';
                break;
            case "backend-menu-entry12-sub2":
                $_db = 'jw_embed';
                break;
            case "backend-menu-entry13-sub1":
                $_db = 'flow_local';
                break;
            case "backend-menu-entry13-sub2":
                $_db = 'flow_embed';
                break;
        }
        return $_db;
    }
    /* update configuration */
    public function cfgUpdate()
    {
        global $db;

        $_db = self::playerType();

        $db_array = array();
        $db_array = $_POST;
        unset($db_array['keep_open']);
        unset($db_array['ct_entry']);
        unset($db_array['p_user_key']);
        $sql = sprintf("UPDATE `db_fileplayers` SET `db_config`='%s' WHERE `db_name`='%s' LIMIT 1;", serialize($db_array), $_db);
        $res = $db->execute($sql);

        return $db->Affected_Rows();
    }
    /* tooltip span text */
    public function tooltip_text($replace)
    {
        $tt = ' <span title=\'##TEXT##\' rel="tooltip"> <i class="icon-question"></i> </span>';

        return str_replace('##TEXT##', $replace, $tt);
    }
    /* generate various input sections */
    public function div_setting_input($input_type)
    {
        global $language, $cfg, $smarty, $class_filter, $class_database;

        $name_class = 'left-float lh20 wd140';
        $tip_class  = 'left-float wd400 left-padding10 greyed-out no-display1';
        $wrap_class = 'left-float wrap-entry';
        $js         = '$(".wrap-entry").mouseover(function(){$(this).next().removeClass("no-display");}).mouseout(function(){$(this).next().addClass("no-display");});';
        $_db        = self::playerType();
        $_cfg       = unserialize($class_database->singleFieldValue('db_fileplayers', 'db_config', 'db_name', $_db));

        switch ($input_type) {
            case "jw_license":
                $tt         = '<span title=\'' . $language['backend.player.menu.jw.license.tip'] . '\' rel="tooltip"><i class="icon-tag"></i></span>';
                $input_code = '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.menu.jw.license'] . '</label>' . self::tooltip_text($language['backend.player.menu.jw.license.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_license_key', 'backend-text-input', $_cfg['jw_license_key'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "vjs_layout":
                $input_code = '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.layout.controls'] . '</label>' . self::tooltip_text($language['backend.player.jw.layout.controls.tip']), '', 'span');
                $input_code .= self::player_selectList('vjs_layout_controls', $_cfg['vjs_layout_controls'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.layout.skin'] . '</label>' . self::tooltip_text($language['backend.player.vjs.layout.skin.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_skin', 'backend-text-input', $_cfg['vjs_skin'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "vjs_advertising":
                $input_code = '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.menu.vjs.adv'] . '</label>' . self::tooltip_text($language['backend.player.menu.vjs.adv.tip']), '', 'span');
                $input_code .= self::player_selectList('vjs_advertising', $_cfg['vjs_advertising'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_layout":
                $input_code = '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.layout.controls'] . '</label>' . self::tooltip_text($language['backend.player.jw.layout.controls.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_layout_controls', $_cfg['jw_layout_controls'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.layout.skin'] . '</label>' . self::tooltip_text($language['backend.player.jw.layout.skin.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_skin', 'backend-text-input', $_cfg['jw_skin'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "vjs_behavior";
                $input_code = '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.behavior.autostart'] . '</label>' . self::tooltip_text($language['backend.player.vjs.behavior.autostart.tip']), '', 'span');
                $input_code .= self::player_selectList('vjs_autostart', $_cfg['vjs_autostart'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.behavior.loop'] . '</label>' . self::tooltip_text($language['backend.player.vjs.behavior.loop.tip']), '', 'span');
                $input_code .= self::player_selectList('vjs_loop', $_cfg['vjs_loop'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.behavior.muted'] . '</label>' . self::tooltip_text($language['backend.player.vjs.behavior.muted.tip']), '', 'span');
                $input_code .= self::player_selectList('vjs_muted', $_cfg['vjs_muted'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.behavior.related'] . '</label>' . self::tooltip_text($language['backend.player.vjs.behavior.related.tip']), '', 'span');
                $input_code .= self::player_selectList('vjs_related', $_cfg['vjs_related'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_behavior";
                $input_code = '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.behavior.autostart'] . '</label>' . self::tooltip_text($language['backend.player.jw.behavior.autostart.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_autostart', $_cfg['jw_autostart'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.behavior.fallback'] . '</label>' . self::tooltip_text($language['backend.player.jw.behavior.fallback.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_fallback', $_cfg['jw_fallback'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.behavior.mute'] . '</label>' . self::tooltip_text($language['backend.player.jw.behavior.mute.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_mute', $_cfg['jw_mute'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.behavior.primary'] . '</label>' . self::tooltip_text($language['backend.player.jw.behavior.primary.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_primary', $_cfg['jw_primary'], array('html5' => $language['backend.player.html5'], 'flash' => $language['backend.player.flash']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.behavior.repeat'] . '</label>' . self::tooltip_text($language['backend.player.jw.behavior.repeat.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_repeat', $_cfg['jw_repeat'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.behavior.stretch'] . '</label>' . self::tooltip_text($language['backend.player.jw.behavior.stretch.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_stretching', $_cfg['jw_stretching'], array('none' => $language['backend.player.none'], 'exactfit' => $language['backend.player.exactfit'], 'uniform' => $language['backend.player.uniform'], 'fill' => $language['backend.player.fill']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_logo";
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.logo.file'] . '</label>' . self::tooltip_text($language['backend.player.jw.logo.file.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_logo_file', 'backend-text-input', $_cfg['jw_logo_file'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.logo.link'] . '</label>' . self::tooltip_text($language['backend.player.jw.logo.link.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_logo_link', 'backend-text-input', $_cfg['jw_logo_link'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.logo.hide'] . '</label>' . self::tooltip_text($language['backend.player.jw.logo.hide.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_logo_hide', $_cfg['jw_logo_hide'], array('enabled' => $language['backend.player.enabled'], 'disabled' => $language['backend.player.disabled']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.logo.margin'] . '</label>' . self::tooltip_text($language['backend.player.jw.logo.margin.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_logo_margin', 'backend-text-input', $_cfg['jw_logo_margin'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.logo.position'] . '</label>' . self::tooltip_text($language['backend.player.jw.logo.position.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_logo_position', $_cfg['jw_logo_position'], array('bottom-left' => 'bottom-left', 'bottom-right' => 'bottom-right', 'top-left' => 'top-left', 'top-right' => 'top-right'));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "vjs_logo";
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.logo.image'] . '</label>' . self::tooltip_text($language['backend.player.vjs.logo.image.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_logo_file', 'backend-text-input', $_cfg['vjs_logo_file'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.logo.position'] . '</label>' . self::tooltip_text($language['backend.player.vjs.logo.position.tip']), '', 'span');
                $input_code .= self::player_selectList('vjs_logo_position', $_cfg['vjs_logo_position'], array('bottom-left' => 'bottom-left', 'bottom-right' => 'bottom-right', 'top-left' => 'top-left', 'top-right' => 'top-right'));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.logo.url'] . '</label>' . self::tooltip_text($language['backend.player.vjs.logo.url.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_logo_url', 'backend-text-input', $_cfg['vjs_logo_url'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.logo.fade'] . '</label>' . self::tooltip_text($language['backend.player.vjs.logo.fade.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_logo_fade', 'backend-text-input', $_cfg['vjs_logo_fade'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_rightclick":
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.rc.abouttext'] . '</label>' . self::tooltip_text($language['backend.player.jw.rc.abouttext.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_rc_text', 'backend-text-input', $_cfg['jw_rc_text'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.rc.aboutlink'] . '</label>' . self::tooltip_text($language['backend.player.jw.rc.aboutlink.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_rc_link', 'backend-text-input', $_cfg['jw_rc_link'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "vjs_rightclick":
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.abouttext1'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.abouttext1.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_text1', 'backend-text-input', $_cfg['vjs_rc_text1'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.aboutlink1'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.aboutlink1.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_link1', 'backend-text-input', $_cfg['vjs_rc_link1'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.abouttext2'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.abouttext2.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_text2', 'backend-text-input', $_cfg['vjs_rc_text2'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.aboutlink2'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.aboutlink2.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_link2', 'backend-text-input', $_cfg['vjs_rc_link2'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.abouttext3'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.abouttext3.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_text3', 'backend-text-input', $_cfg['vjs_rc_text3'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.aboutlink3'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.aboutlink3.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_link3', 'backend-text-input', $_cfg['vjs_rc_link3'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.abouttext4'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.abouttext4.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_text4', 'backend-text-input', $_cfg['vjs_rc_text4'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.aboutlink4'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.aboutlink4.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_link4', 'backend-text-input', $_cfg['vjs_rc_link4'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.abouttext5'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.abouttext5.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_text5', 'backend-text-input', $_cfg['vjs_rc_text5'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.vjs.rc.aboutlink5'] . '</label>' . self::tooltip_text($language['backend.player.vjs.rc.aboutlink5.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'vjs_rc_link5', 'backend-text-input', $_cfg['vjs_rc_link5'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_advertising":
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.plugin.enabled'] . '</label>' . self::tooltip_text($language['backend.player.jw.plugin.enabled.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_adv_enabled', $_cfg['jw_adv_enabled'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.adv.msg'] . '</label>' . self::tooltip_text($language['backend.player.jw.adv.msg.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_adv_msg', 'backend-text-input', $_cfg['jw_adv_msg'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_analytics":
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.analytics.enabled'] . '</label>' . self::tooltip_text($language['backend.player.jw.analytics.enabled.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_analytics_enabled', $_cfg['jw_analytics_enabled'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.analytics.cookies'] . '</label>' . self::tooltip_text($language['backend.player.jw.analytics.cookies.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_analytics_cookies', $_cfg['jw_analytics_cookies'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_ga":
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.plugin.enabled'] . '</label>' . self::tooltip_text($language['backend.player.jw.plugin.enabled.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_ga_enabled', $_cfg['jw_ga_enabled'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.ga.idstring'] . '</label>' . self::tooltip_text($language['backend.player.jw.ga.idstring.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_ga_idstring', $_cfg['jw_ga_idstring'], array('file' => 'file', 'title' => 'title', 'mediaid' => 'mediaid'));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.ga.trackingobject'] . '</label>' . self::tooltip_text($language['backend.player.jw.ga.trackingobject.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_ga_trackingobject', 'backend-text-input', $_cfg['jw_ga_trackingobject'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_sharing":
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.plugin.enabled'] . '</label>' . self::tooltip_text($language['backend.player.jw.plugin.enabled.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_share_enabled', $_cfg['jw_share_enabled'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.sharing.link'] . '</label>' . self::tooltip_text($language['backend.player.jw.sharing.link.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_share_link', $_cfg['jw_share_link'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.sharing.code'] . '</label>' . self::tooltip_text($language['backend.player.jw.sharing.code.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_share_code', $_cfg['jw_share_code'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.sharing.head'] . '</label>' . self::tooltip_text($language['backend.player.jw.sharing.head.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_share_head', 'backend-text-input', $_cfg['jw_share_head'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_related":
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.plugin.enabled'] . '</label>' . self::tooltip_text($language['backend.player.jw.plugin.enabled.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_related_enabled', $_cfg['jw_related_enabled'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding-10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.related.file'] . '</label>' . self::tooltip_text($language['backend.player.jw.related.file.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_related_file', 'backend-text-input', $_cfg['jw_related_file'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.related.click'] . '</label>' . self::tooltip_text($language['backend.player.jw.related.click.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_related_onclick', $_cfg['jw_related_onclick'], array('link' => 'link', 'play' => 'play'));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.related.complete'] . '</label>' . self::tooltip_text($language['backend.player.jw.related.complete.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_related_oncomplete', $_cfg['jw_related_oncomplete'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.related.head'] . '</label>' . self::tooltip_text($language['backend.player.jw.related.head.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_related_head', 'backend-text-input', $_cfg['jw_related_head'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "jw_captions":
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.plugin.enabled'] . '</label>' . self::tooltip_text($language['backend.player.jw.plugin.enabled.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_captions_enabled', $_cfg['jw_captions_enabled'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.cap.back'] . '</label>' . self::tooltip_text($language['backend.player.jw.cap.back.tip']), '', 'span');
                $input_code .= self::player_selectList('jw_captions_back', $_cfg['jw_captions_back'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.cap.color'] . '</label>' . self::tooltip_text($language['backend.player.jw.cap.color.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_captions_color', 'backend-text-input', $_cfg['jw_captions_color'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.jw.cap.size'] . '</label>' . self::tooltip_text($language['backend.player.jw.cap.size.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'jw_captions_fontsize', 'backend-text-input', $_cfg['jw_captions_fontsize'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "flow_license":
                $input_code = '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.license'] . '</label>' . self::tooltip_text($language['backend.player.flow.license.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'flow_license', 'backend-text-input', $_cfg['flow_license'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "flow_logo";
                $input_code = '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.logo'] . '</label>' . self::tooltip_text($language['backend.player.flow.logo.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'flow_logo', 'backend-text-input', $_cfg['flow_logo'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "flow_behavior";
                $input_code = '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.engine'] . '</label>' . self::tooltip_text($language['backend.player.flow.engine.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_engine', $_cfg['flow_engine'], array('html5' => $language['backend.player.html5'], 'flash' => $language['backend.player.flash']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.disabled'] . '</label>' . self::tooltip_text($language['backend.player.flow.disabled.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_disabled', $_cfg['flow_disabled'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.autoplay'] . '</label>' . self::tooltip_text($language['backend.player.flow.autoplay.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_autoplay', $_cfg['flow_autoplay'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.fullscreen'] . '</label>' . self::tooltip_text($language['backend.player.flow.fullscreen.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_fullscreen', $_cfg['flow_fullscreen'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.keyboard'] . '</label>' . self::tooltip_text($language['backend.player.flow.keyboard.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_keyboard', $_cfg['flow_keyboard'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.muted'] . '</label>' . self::tooltip_text($language['backend.player.flow.muted.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_muted', $_cfg['flow_muted'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.native_fullscreen'] . '</label>' . self::tooltip_text($language['backend.player.flow.native_fullscreen.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_native_fullscreen', $_cfg['flow_native_fullscreen'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.flashfit'] . '</label>' . self::tooltip_text($language['backend.player.flow.flashfit.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_flashfit', $_cfg['flow_flashfit'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.rtmp'] . '</label>' . self::tooltip_text($language['backend.player.flow.rtmp.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'flow_rtmp', 'backend-text-input', $_cfg['flow_rtmp'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.splash'] . '</label>' . self::tooltip_text($language['backend.player.flow.splash.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_splash', $_cfg['flow_splash'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.tooltip'] . '</label>' . self::tooltip_text($language['backend.player.flow.tooltip.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_tooltip', $_cfg['flow_tooltip'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.volume'] . '</label>' . self::tooltip_text($language['backend.player.flow.volume.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'flow_volume', 'backend-text-input', $_cfg['flow_volume'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                $input_code .= '<div class="row top-padding10">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.subs'] . '</label>' . self::tooltip_text($language['backend.player.flow.subs.tip']), '', 'span');
                $input_code .= self::player_selectList('flow_subtitles', $_cfg['flow_subtitles'], array(1 => $language['backend.player.true'], 0 => $language['backend.player.false']));
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
            case "flow_analytics":
                $input_code .= '<div class="row no-top-padding">';
                $input_code .= '<div class="' . $wrap_class . '">';
                $input_code .= VGenerate::simpleDivWrap($name_class, '', '<label>' . $language['backend.player.flow.analytics'] . '</label>' . self::tooltip_text($language['backend.player.flow.analytics.tip']), '', 'span');
                $input_code .= VGenerate::basicInput('text', 'flow_analytics', 'backend-text-input', $_cfg['flow_analytics'], '');
                $input_code .= '</div>';
                $input_code .= '</div>'; //end row
                break;
        }

        return $input_code;
    }
}
