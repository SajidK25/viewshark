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

sleep(1);

include_once 'f_core/config.core.php';

$post_name   = 'file';
$upload_type = $_GET["t"] != 'document' ? $class_filter->clr_str($_GET["t"]) : 'doc';

if (isset($_GET["t"])) {
    if ($upload_type != 'video' and $upload_type != 'short' and $upload_type != 'audio' and $upload_type != 'image' and $upload_type != 'doc') {
        error_log('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open input stream."}, "id" : "id"}');
        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open input stream."}, "id" : "id"}');
    }
}

// Make sure file is not cached (as it happens for example on iOS devices)
header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

/*
// Support CORS
header("Access-Control-Allow-Origin: *");
// other CORS headers if any...
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
exit; // finish preflight CORS requests here
}
 */

// 5 minutes execution time
// @set_time_limit(5 * 60);

// Uncomment this one to fake upload time
// usleep(5000);

// Settings
$user_key = $class_filter->clr_str($_GET["u"]);

$targetDir        = $cfg["upload_files_dir"] . '/' . $user_key . '/' . $upload_type[0];
$cleanupTargetDir = true; // Remove old files
$maxFileAge       = 5 * 3600; // Temp file age in seconds

// Create target dir
if (!file_exists($targetDir)) {
    @mkdir($targetDir);
}

// Get a file name
if (isset($_REQUEST["name"])) {
    $fileName = $_REQUEST["name"];
} elseif (!empty($_FILES)) {
    $fileName = $_FILES["file"]["name"];
} else {
    $fileName = uniqid("file_");
}

$fileName = trim(pathinfo($fileName, PATHINFO_FILENAME) . '.' . strtolower(pathinfo($fileName, PATHINFO_EXTENSION)));
$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;

// Chunking might be enabled
$chunk  = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;

// Remove old temp files
if ($cleanupTargetDir) {
    if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
        error_log('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
        die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
    }

    while (($file = readdir($dir)) !== false) {
        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

        // If temp file is current file proceed to the next
        if ($tmpfilePath == "{$filePath}.part") {
            continue;
        }

        // Remove temp file if it is older than the max age and is not the current file
        if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
            @unlink($tmpfilePath);
        }
    }
    closedir($dir);
}

if (strpos(strtolower($fileName), '.php') !== false or strpos(strtolower($fileName), '.phar') !== false or strpos(strtolower($fileName), '.pl') !== false or strpos(strtolower($fileName), '.asp') !== false or strpos(strtolower($fileName), '.htm') !== false or strpos(strtolower($fileName), '.cgi') !== false or strpos(strtolower($fileName), '.py') !== false or strpos(strtolower($fileName), '.sh') !== false or strpos(strtolower($fileName), '.cin') !== false or strpos(strtolower($fileName), '.bin') !== false) {
    error_log('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open input stream."}, "id" : "id"}');
    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open input stream."}, "id" : "id"}');
}

// Open temp file
if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
    error_log('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
}

if (!empty($_FILES)) {
    if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
        error_log('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
    }

    // Read binary input stream and append it to temp file
    if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
        error_log('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    }
} else {
    if (!$in = @fopen("php://input", "rb")) {
        error_log('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
    }
}

while ($buff = fread($in, 4096)) {
    fwrite($out, $buff);
}

@fclose($out);
@fclose($in);

// Check if file has been uploaded
if (!$chunks || $chunk == $chunks - 1) {
    // Strip the temp .part suffix off
    $nfilePath = $targetDir . DIRECTORY_SEPARATOR . filesize("{$filePath}.part") . '.' . strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    rename("{$filePath}.part", $nfilePath);
    if (!file_exists($nfilePath) or filesize($nfilePath) <= 16) {
        error_log('{"jsonrpc" : "2.0", "error" : {"code": 106, "message": "Connection failure. Please check your connection and try again."}, "id" : "id"}');
        die('{"jsonrpc" : "2.0", "error" : {"code": 106, "message": "Connection failure."}, "id" : "id"}');
    }

    if (preg_match('/"/', $nfilePath) or preg_match('/`/', $nfilePath)) {
        $new_filePath = str_replace(array('"', '`'), array("'", "'"), $nfilePath);
        rename($nfilePath, $new_filePath);

        if (!file_exists($new_filePath) or filesize($new_filePath) <= 16) {
            error_log('{"jsonrpc" : "2.0", "error" : {"code": 107, "message": "Connection failure. Please check your connection and try again."}, "id" : "id"}');
            die('{"jsonrpc" : "2.0", "error" : {"code": 107, "message": "Connection failure."}, "id" : "id"}');
        }
    }
}

// Return Success JSON-RPC response
die('{"jsonrpc" : "2.0", "result" : null, "id" : "id"}');
