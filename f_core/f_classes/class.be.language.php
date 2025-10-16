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

class VbeLanguage
{
    /* main lang details */
    public function mainLangDetails($_dsp = '', $entry_id = '', $db_id = '', $lang_active = '', $lang_name = '', $lang_flag = '', $lang_id = '', $lang_def = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            if (!self::processLangEntry()) {
                $form      = VArraySection::getArray("lang_types");
                $lang_name = $form[0]['lang_name'];
                $lang_id   = $form[0]['lang_id'];
                $lang_def  = $form[0]['lang_default'];
                $lang_flag = $form[0]['lang_flag'];
            }
        }

        return self::langDetails($_dsp = '', $entry_id = '', $db_id = '', $lang_active = '', $lang_name, $lang_flag, $lang_id, $lang_def);
    }
    /* scan language dirs */
    public function langDirs($lang_id, $db_id, $section)
    {
        global $cfg, $language;

        if ($section == 'frontend') {
            $d = array(
                'language.account.php'        => 'User account/profile settings',
                'language.browse.php'         => 'Browse files section',
                'language.channel.php'        => 'Channel page text',
                'language.email.notif.php'    => 'Email notifications text',
                'language.files.menu.php'     => 'File actions and buttons',
                'language.files.php'          => 'User file management section',
                'language.footer.php'         => 'Footer text',
                'language.global.php'         => 'General text used in multiple sections',
                'language.home.php'           => 'Homepage text',
                'language.manage.channel.php' => 'Channel settings text',
                'language.messages.php'       => 'User contacts, messages, friends',
                'language.notifications.php'  => 'Error and notice messages',
                'language.offline.php'        => 'Offline page text',
                'language.recovery.php'       => 'Username and password recovery',
                'language.respond.php'        => 'Responses related text',
                'language.search.php'         => 'Search section text',
                'language.signin.php'         => 'Login section text',
                'language.signup.php'         => 'Registration page text',
                'language.upload.php'         => 'Upload section text',
                'language.userpage.php'       => 'User channel text',
                'language.view.php'           => 'View files section',
                'language.welcome.php'        => 'Welcome page text',
            );
        } elseif ($section == 'backend') {
            $d = array(
                'language.advertising.php'      => 'Advertising module text',
                'language.conversion.php'       => 'Encoding settings text',
                'language.dashboard.php'        => 'Dashboard text',
                'language.files.php'            => 'File management text',
                'language.import.php'           => 'Video grabber text',
                'language.members.entries.php'  => 'User accounts text',
                'language.players.php'          => 'Player configuration text',
                'language.servers.php'          => 'Content distribution text',
                'language.settings.entries.php' => 'Global settings text',
                'language.signin.php'           => 'Login page text',
                'language.streaming.php'        => 'Streaming settings text',
                'language.subscriber.php'       => 'Subscriber dashboard text',
            );
        }

        $directory         = $cfg['language_dir'] . '/' . $lang_id . '/lang_' . $section . '/';
        $scanned_directory = array_diff(scandir($directory), array('..', '.', '.htaccess', 'old'));
        $i                 = 0;
        $_ht               = null;
        foreach ($scanned_directory as $k => $v) {
            $_ht .= '<div class="list-' . ($i % 2 == 0 ? 'even' : 'odd') . '">';
            $_ht .= '<div class="vs-column thirds centered-text">' . $d[$v] . '</div>';
            $_ht .= '<div class="vs-column thirds centered-text">' . $v . '</div>';
            $_ht .= '<div class="vs-column thirds fit centered-text"><a href="javascript:;" class="black popup" id="' . md5($v) . '" rel-type="' . ($section[0] == 'f' ? 'fe' : 'be') . '" rel-id="' . $db_id . '"><i class="icon-pencil" rel="tooltip" title="' . $language['backend.menu.entry1.sub10.langfile.edit'] . '"></i></a></div>';
            $_ht .= '</div>';
            $_ht .= '<div class="clearfix"></div>';

            $i += 1;
        }
        $_ht .= '<div class="clearfix"></div>';
        return $_ht;
    }
    /* lang details edit */
    public function langDetails($_dsp = '', $entry_id = '', $db_id = '', $lang_active = '', $lang_name = '', $lang_flag = '', $lang_id = '', $lang_def = '')
    {
        global $class_filter, $language;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_sct  = 'lang_types';
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block-off;') : null;

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="lang-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));

        if ($_GET['do'] != 'add') {
            $tabs = '<div class="swiper-ph swiper-ph-main"></div>';
            $tabs .= '<div class="swiper-top swiper-top-main" id="swiper-' . $db_id . '" style="display:' . VGenerate::ssd() . '">';
            $tabs .= '<div class="swiper-button-prev swiper-button-prev-main"' . VGenerate::sso() . '></div>';
            $tabs .= '<div class="swiper-button-next swiper-button-next-main"' . VGenerate::sso() . '></div>';
            $tabs .= '<div class="swiper swiper-main">';
            $tabs .= '<div class="swiper-wrapper">';
            $tabs .= '<div class="swiper-slide swiper-slide-current"><a href="javascript:;" class="icon icon-pencil" onclick="closeDiv(\'edit-be-files' . $db_id . '\'); closeDiv(\'edit-fe-files' . $db_id . '\'); openDiv(\'edit-be-entry' . $db_id . '\'); $(\'#lang-entry-form' . $db_id . ' .swiper-slide\').removeClass(\'swiper-slide-current\'); $(this).parent().addClass(\'swiper-slide-current\')"><span>' . $language['backend.menu.entry1.sub10.lang.edit'] . '</span></a></div>';
            $tabs .= '<div class="swiper-slide"><a href="javascript:;" class="icon icon-pencil" onclick="openDiv(\'edit-fe-files' . $db_id . '\'); closeDiv(\'edit-be-files' . $db_id . '\'); closeDiv(\'edit-be-entry' . $db_id . '\'); $(\'#lang-entry-form' . $db_id . ' .swiper-slide\').removeClass(\'swiper-slide-current\'); $(this).parent().addClass(\'swiper-slide-current\')"><span>' . $language['backend.menu.entry1.sub10.lang.fe'] . '</span></a></div>';
            $tabs .= '<div class="swiper-slide"><a href="javascript:;" class="icon icon-pencil" onclick="openDiv(\'edit-be-files' . $db_id . '\'); closeDiv(\'edit-fe-files' . $db_id . '\'); closeDiv(\'edit-be-entry' . $db_id . '\'); $(\'#lang-entry-form' . $db_id . ' .swiper-slide\').removeClass(\'swiper-slide-current\'); $(this).parent().addClass(\'swiper-slide-current\')"><span>' . $language['backend.menu.entry1.sub10.lang.be'] . '</span></a></div>';
            $tabs .= '</div></div></div>';

            $html .= '<div class="tabs tabs-style-topline">';
            $html .= $tabs;
            $html .= '<nav class="no-display"><ul class="ul-no-list uactions-list">';
            $html .= '<li class="active"><a href="javascript:;" class="icon icon-pencil" onclick="closeDiv(\'edit-be-files' . $db_id . '\'); closeDiv(\'edit-fe-files' . $db_id . '\'); openDiv(\'edit-be-entry' . $db_id . '\');"><span>' . $language['backend.menu.entry1.sub10.lang.edit'] . '</span></a></li>';
            $html .= '<li><a href="javascript:;" class="icon icon-pencil" onclick="openDiv(\'edit-fe-files' . $db_id . '\'); closeDiv(\'edit-be-files' . $db_id . '\'); closeDiv(\'edit-be-entry' . $db_id . '\');"><span>' . $language['backend.menu.entry1.sub10.lang.fe'] . '</span></a></li>';
            $html .= '<li><a href="javascript:;" class="icon icon-pencil" onclick="openDiv(\'edit-be-files' . $db_id . '\'); closeDiv(\'edit-fe-files' . $db_id . '\'); closeDiv(\'edit-be-entry' . $db_id . '\');"><span>' . $language['backend.menu.entry1.sub10.lang.be'] . '</span></a></li>';
            $html .= '</ul></nav></div>';

            $html .= '<div id="edit-be-files' . $db_id . '" style="display: none;">';
            $html .= VGenerate::simpleDivWrap('left-float left-padding10', '', self::langDirs($lang_id, $db_id, 'backend'));
            $html .= '</div>';
            $html .= '<div id="edit-fe-files' . $db_id . '" style="display: none;">';
            $html .= VGenerate::simpleDivWrap('left-float left-padding10', '', self::langDirs($lang_id, $db_id, 'frontend'));
            $html .= '</div>';
        }

        $html .= '<div id="edit-be-entry' . $db_id . '" style="display: block;">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($lang_active == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.menu.entry1.sub10.lang.id'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_menu_entry1_sub10_lang_id', 'backend-text-input wd180', $lang_id);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.name'] . '</label>' . $language['frontend.global.required'], 'left-float', 'frontend_global_name', 'backend-text-input wd180', $lang_name);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.flagicon'] . '</label>' . $language['frontend.global.required'], 'left-float', 'frontend_global_flagicon', 'backend-text-input wd180', $lang_flag);
        $html .= $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('icheck-box', '', '<input type="checkbox" value="1" name="lang_default" class=""' . ($lang_def == 1 ? ' checked="checked"' : null) . '><label>' . $language['backend.menu.entry1.sub10.lang.def'] . '</label>')) : null;
        $html .= '</div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;') . VGenerate::simpleDivWrap('left-float lh20', '', $_btn));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language['frontend.global.required.items']);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';
        $html .= '</form></div>';

        $html .= VGenerate::swiperjs('main', 'swiper-' . (int) $db_id);

        return $html;
    }
    /* processing entry */
    public function processLangEntry()
    {
        global $class_database, $db, $language, $cfg;

        $form           = VArraySection::getArray("lang_types");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $lang_name = $form[0]['lang_name'];
            $lang_id   = $form[0]['lang_id'];
            $lang_def  = $form[0]['lang_default'];
            $lang_flag = $form[0]['lang_flag'];
            $ct_id     = intval($_POST['hc_id']);
            switch ($_GET['do']) {
                case "update":
                    $_lid = $class_database->singleFieldValue('db_languages', 'lang_id', 'db_id', $ct_id);
                    if ($lang_id != $_lid) {
                        $src = $cfg['language_dir'] . '/' . $_lid;
                        $dst = $cfg['language_dir'] . '/' . $lang_id;
                        rename($src, $dst);
                    }
                    if ($lang_def == 1) {
                        $ld = $db->execute(sprintf("UPDATE `db_languages` SET `lang_default`='%s' WHERE `db_id`='%s' LIMIT 1;", $lang_def, $ct_id));
                        if ($db->Affected_Rows() > 0) {
                            $n = 1;
                            $q = sprintf("UPDATE `db_languages` SET `lang_default`='0' WHERE `db_id`!='%s';", $ct_id);
                            $db->execute($q);
                        }
                    }

                    $sql = sprintf("UPDATE `db_languages` SET `lang_id`='%s', `lang_name`='%s', `lang_flag`='%s' WHERE `db_id`='%s' LIMIT 1;", $lang_id, $lang_name, $lang_flag, $ct_id);
                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_languages', $form[0]);
                    $src = $cfg['language_dir'] . '/' . $class_database->singleFieldValue('db_languages', 'lang_id', 'db_id', 1);
                    $dst = $cfg['language_dir'] . '/' . $lang_id;

                    exec(sprintf("cp -ra %s %s", $src, $dst));
                    break;
            }
            if ($db->Affected_Rows() > 0 or $n == 1) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
                return true;
            } else {
                if ($_GET['do'] == 'add') {
                    echo VGenerate::noticeTpl('', $language['backend.menu.entry1.sub10.lang.dup'], '');
                }
                return false;
            }
        }
    }
}
