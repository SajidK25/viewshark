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

define('_ISVALID', true);
define('_ISADMIN', true);

include_once 'f_core/config.core.php';

include_once $class_language->setLanguageFile('frontend', 'language.global');
include_once $class_language->setLanguageFile('frontend', 'language.upload');
include_once $class_language->setLanguageFile('frontend', 'language.notifications');
include_once $class_language->setLanguageFile('frontend', 'language.account');
include_once $class_language->setLanguageFile('backend', 'language.import');
include_once $class_language->setLanguageFile('backend', 'language.settings.entries');
include_once $class_language->setLanguageFile('backend', 'language.advertising');

$error_message  = null;
$notice_message = null;
$upload_type    = $_GET["t"] != 'document' ? $class_filter->clr_str($_GET["t"]) : 'doc';
$logged_in      = VLogin::checkBackend(VHref::getKey("be_upload") . '?t=' . $upload_type);
$cfg            = $class_database->getConfigurations('video_player,audio_player,multiple_file_uploads,video_file_types,video_limit,image_file_types,image_limit,audio_file_types,audio_limit,document_file_types,document_limit,file_counts,numeric_delimiter,file_responses,upload_category,import_yt,import_dm,import_vi,short_file_types,short_limit');

$files = new VFiles;

if (isset($_GET["t"])) {
    if ($upload_type != 'video' and $upload_type != 'short' and $upload_type != 'audio' and $upload_type != 'image' and $upload_type != 'doc') {
        header("Location: " . VHref::getKey("be_upload") . '?t=video');
        exit;
    }
}

if ($_POST and $_GET["do"] == 'form-update') {
    foreach ($_POST['ekey'] as $k => $v) {
        $name  = $_POST['efile'][$k];
        $name  = (preg_match('/"/', $name) or preg_match('/`/', $name)) ? str_replace(array('"', '`'), array("'", "'"), $name) : $name;
        $name  = md5($class_filter->clr_str($name));
        $sql   = sprintf("SELECT `file_key` FROM `db_%sfiles` WHERE `file_hash`='%s' ORDER BY `db_id` DESC LIMIT 1;", $upload_type, $name);
        $rs    = $db->execute($sql);
        $key   = $rs->fields['file_key'];
        $title = $class_filter->clr_str($_POST['c_entry_title'][$v]);
        $descr = $class_filter->clr_str($_POST['c_entry_description'][$v]);
        $tags  = VForm::clearTag($_POST['c_entry_tags'][$v]);
        $categ = (int) $_POST['c_entry_category'][$v];

        $a = array('file_title' => $title, 'file_description' => $descr, 'file_tags' => $tags, 'file_category' => $categ);

        foreach ($a as $field => $value) {
            if ($value != '') {
                $db->execute(sprintf("UPDATE `db_%sfiles` SET `%s`='%s' WHERE `file_key`='%s' LIMIT 1;", $upload_type, $field, $value, $key));
            }
        }
    }

    exit;
}

switch ($_GET["do"]) {
    case "autocomplete": //autocomplete username
        $ht = VGenerate::processAutoComplete('upload');
        break;
    default:break;
}

$init_cfg     = ($error_message == '' and $_GET["t"] != '') ? VUpload::initCFG(1) : null;
$display_page = ($_GET["do"] == '' and $_GET["cid"] == '') ? $class_smarty->displayPage('backend', 'backend_tpl_upload', $error_message, $notice_message) : null;
