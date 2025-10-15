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

define('_ISVALID', true);

$main_dir = realpath(dirname(__FILE__).'/../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';

$do   = $class_filter->clr_str($_SERVER["argv"][1]);
$type = isset($_SERVER["argv"][2]) ? $class_filter->clr_str($_SERVER["argv"][2]) : 'video';
$for  = $class_filter->clr_str($_SERVER["argv"][3]);

switch ($do) {
    case "ff": //fixfollowers
    case "fs": //fixsubscribers
        $tbl    = $do == "ff" ? "db_followers" : "db_subscribers";
        $field  = $do == "ff" ? "follower_id" : "subscriber_id";
        $tfield = $do == "ff" ? "usr_followcount" : "usr_subcount";
        $rs     = $db->execute(sprintf("SELECT * FROM `%s_old`;", $tbl));

        if ($rs->fields["db_id"] and $rs->fields[$field]) {
            while (!$rs->EOF) {
                $usr_id    = $rs->fields["usr_id"];
                $followers = unserialize($rs->fields[$field]);

                foreach ($followers as $f) {
                    $is_uid = $class_database->singleFieldValue('db_accountuser', 'usr_key', 'usr_id', $f["sub_id"]);

                    if ($is_uid) {
                        $ins = array(
                            "usr_id"           => $usr_id,
                            "sub_id"           => $f["sub_id"],
                            "sub_time"         => $f["sub_time"],
                            "sub_type"         => $f["sub_type"],
                            "mail_new_uploads" => $f["mail_new_uploads"],
                        );

                        if ($class_database->doInsert($tbl, $ins)) {
                            echo sprintf("Added usr_id %s with %s %s\n", $usr_id, $field, $f["sub_id"]);
                            echo "\n";
                        }
                    }
                }

                $tt = $db->execute(sprintf("SELECT COUNT(`db_id`) AS `total` FROM `%s` WHERE `usr_id`='%s';", $tbl, $usr_id));
                $db->execute(sprintf("UPDATE `db_accountuser` SET `%s`='%s' WHERE `usr_id`='%s' LIMIT 1;", $tfield, $tt->fields["total"], $usr_id));

                $rs->MoveNext();
            }
        }
        break;

    case "fdata": //fixhistory, etc
        $tblu = sprintf("db_%sfiles", $type);

        switch ($for) {
            case "history":
                $tbl   = sprintf("db_%shistory", $type);
                $field = "history_list";
                break;

            case "favorites":
                $tbl   = sprintf("db_%sfavorites", $type);
                $field = "fav_list";
                break;

            case "liked":
                $tbl   = sprintf("db_%sliked", $type);
                $field = "liked_list";
                break;

            case "watchlist":
                $tbl   = sprintf("db_%swatchlist", $type);
                $field = "watch_list";
                break;

            case "responses":
                $tbl   = sprintf("db_%sresponses", $type);
                $field = "file_responses";
                break;
        }

        $rs = $db->execute(sprintf("SELECT * FROM `%s_old`;", $tbl));

        if ($rs->fields["db_id"] and $rs->fields[$field]) {
            while (!$rs->EOF) {
                $usr_id = $rs->fields["usr_id"]; //this will be used in general, commented out only for responses
                //$usr_id = null; //for responses we add this back
                $entries = unserialize($rs->fields[$field]);

                foreach ($entries as $entry) {
                    $file_key = $for == 'history' ? $entry[0] : $entry; //this will be used in general, commented out only for responses
                    //$file_key = $for == 'responses' ? $entry["file_key"] : $entry; //for responses we add this back
                    $is_id = $class_database->singleFieldValue($tblu, 'db_id', 'file_key', $file_key);

                    if ($is_id) {
                        $ins = array(
                            "usr_id"        => $usr_id,
                            "file_key"      => ($for == 'responses' ? $rs->fields["file_key"] : $file_key),
                        );
                        if ($for == 'responses') {
                    	    $ins["usr_id"]			= $entry["usr_id"];
                    	    $ins["file_response"]	= $file_key;
                    	    $ins["datetime"]		= $entry["date"];
                    	    $ins["active"]			= $entry["active"];
                        }
                        if ($for == 'history') {
                            $ins["views"] = $entry[1];
                        }
                        if ($class_database->doInsert($tbl, $ins)) {
                            echo sprintf("Added usr_id %s %s %s entries\n", ($for == 'responses' ? $entry["usr_id"] : $usr_id), $field, count($entries));
                            echo "\n";
                        }
                    }
                }

                $rs->MoveNext();
            }
        }
        break;
}
