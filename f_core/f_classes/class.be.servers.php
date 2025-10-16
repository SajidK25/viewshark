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

include_once 'class_ftp/ftp_class.php';
include_once 'class_amazons3/aws-autoloader.php';

use Aws\Common\Credentials\Credentials;
use Aws\S3\S3Client;

class fileTransfer
{
    public $data      = array();
    public $log_clean = false;
    public $type;
    private $log = false;
    private $log_file;
    private $cfg;
    private $db;
    private $cdb;

    public function __construct()
    {
        global $cfg, $db, $class_database;

        $this->log = true;
        $this->cfg = $cfg;
        $this->db  = $db;
        $this->cdb = $class_database;
    }
    public function __destruct()
    {
        // we need to delete the log file here
        if ($this->log_clean === true) {
            // VFile::delete($this->log_file);
        }
    }
    public function load($file, $file_type)
    {
        if (!file_exists($file)) {
            return false;
        }
        $this->type             = $file_type;
        $this->data['location'] = $file;
        $this->data['size']     = filesize($file);

        return true;
    }
    public function doTransfer($file_id, $user_key, $file, $srv_id = 0, $send_thumbs = 0, $exclude_id = 0)
    {
        $this->log("Determining next " . ($send_thumbs == 1 ? "thumb" : "upload") . " server.\n");

        if ($srv_id == 0) {
            $rs = $this->db->execute(sprintf("SELECT *
                            FROM
                            `db_servers`
                            WHERE
                            %s
                            `status`='1' AND
                            `%s`='1' AND
                            (`total_%s` < `server_limit` AND
                            `current_hop` < `file_hop`)
                            ORDER BY `server_priority` DESC LIMIT 1;",
                (($send_thumbs >= 1 and $exclude_id > 0) ? "`server_id`!='" . $exclude_id . "' AND " : null),
                ($send_thumbs >= 1 ? 'upload_' . $this->type[0] . '_thumb' : 'upload_' . $this->type[0] . '_file'), $this->type));
        } else {
            $rs = $this->db->execute(sprintf("SELECT * FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $srv_id));
        }

        $this->log("Uploading to server: " . $rs->fields['server_name'] . " (" . (($rs->fields['server_type'] == 's3' or $rs->fields['server_type'] == 'ws') ? $rs->fields['s3_bucketname'] : $rs->fields['ftp_host']) . ")\n");

        if ($rs->fields['server_id'] == '') {
            $this->db->execute("UPDATE `db_servers` SET `current_hop`='0' WHERE `file_hop`=`current_hop` AND `total_" . $this->type . "`<=`server_limit` AND `status`='1';");
            $ar = $this->db->Affected_Rows();
            $this->log("Trying to reset file hops: " . $ar . " " . ($ar == 1 ? "server" : "servers") . " found." . ($ar == 0 ? " (all servers full or disabled?!)" : null));

            $rs = $this->db->execute(sprintf("SELECT *
                            FROM
                            `db_servers`
                            WHERE
                            %s
                            `status`='1' AND
                            `%s`='1' AND
                            (`total_%s` < `server_limit` AND
                            `current_hop` < `file_hop`)
                            ORDER BY `server_priority` DESC LIMIT 1;",
                (($send_thumbs >= 1 and $exclude_id > 0) ? "`server_id`!='" . $exclude_id . "' AND " : null),
                ($send_thumbs >= 1 ? 'upload_' . $this->type[0] . '_thumb' : 'upload_' . $this->type[0] . '_file'), $this->type));
        } else {
            $this->db->execute(sprintf("UPDATE `db_%stransfers` SET `state`='1' WHERE `file_key`='%s' LIMIT 1;", $this->type, $file_id));
        }

        $this->log("Server Priority: " . $rs->fields['server_priority']);
        $this->log("Server Limit: " . $rs->fields['server_limit']);
        $this->log("Server Total: " . $rs->fields["total_" . $this->type]);
        $this->log("Server Hop: " . $rs->fields['file_hop']);
        $this->log("Current Hop: " . $rs->fields['current_hop'] . "\n\n");

        if (($rs->fields['server_type'] == 's3' or $rs->fields['server_type'] == 'ws') and ($rs->fields["upload_" . $this->type[0] . "_file"] == 1 or $rs->fields["upload_" . $this->type[0] . "_thumb"] == 1)) {
            $awsAccessKey = $rs->fields['s3_accesskey'];
            $awsSecretKey = $rs->fields['s3_secretkey'];
            $bucketName   = $rs->fields['s3_bucketname'];
            $region       = $rs->fields['s3_region'];

            if ($rs->fields['server_type'] == 's3') {
                $regarr = VBeServers::region();
                $ws     = array();
            } elseif ($rs->fields['server_type'] == 'ws') {
                $regarr = array(
                    "us-east-1"    => "US East",
                    "us-west-1"    => "US West",
                    "eu-central-1" => "EU Central",
                );
                $ws = array(
                    //'endpoint' => 'http://s3.wasabisys.com',
                    //'profile' => 'wasabi',
                    'region'  => $region,
                    'version' => 'wasabi',
                );
            }

            $s3 = new S3Client([
                'version'     => 'latest',
                'region'      => $region,
                'credentials' => [
                    'key'    => $awsAccessKey,
                    'secret' => $awsSecretKey,
                ],
            ]);

            ob_start();
            try {
                $proceed = 0;
                $result  = $s3->listBuckets();

                if (is_array($result['Buckets'])) {
                    echo "Successfully logged in using AccessKey and SecretKey\n\n";

                    if ($s3->doesBucketExist($bucketName)) {
                        $proceed = 1;
                        echo "Using bucket {$bucketName}\n\n\n";
                    } else {
                        echo "Setting up bucket {$bucketName}\n\n";
                        $bar = array('Bucket' => $bucketName);
                        $res = $s3->createBucket($bar);

                        if ($res['RequestId']) {
                            $proceed = 1;
                            echo "Bucket {$bucketName} was successfully created\n\n";
                            echo "Bucket region set to " . $regarr[($region == '' ? 'default' : $region)] . "\n\n";
                        } else {
                            echo "Bucket {$bucketName} could not be created. Name already used maybe?\n\n";
                        }
                    }

                    if ($proceed == 1) {
                        $action = 0;
                        $f      = array();
                        $type   = $this->type;
                        $url    = $this->cfg['media_files_dir'];

                        $ds = md5($this->cfg['global_salt_key'] . $file_id);
                        if ($rs->fields["upload_" . $this->type[0] . "_file"] == 1 and $send_thumbs == 0) {
                            switch ($type) {
                                case "video":
                                    $f['360p'] = array(
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.360p.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.360p.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.360p.webm',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.360p.ogv',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.360p.flv');

                                    $f['mob'] = array(
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.mob.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.mob.mp4');

                                    $f['480p'] = array(
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.480p.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.480p.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.480p.webm',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.480p.ogv');

                                    $f['720p'] = array(
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.720p.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.720p.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.720p.webm',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.720p.ogv');

                                    $f['1080p'] = array(
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.1080p.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.1080p.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.1080p.webm',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.1080p.ogv');

                                    break;
                                case "image":
                                    $f['jpg'] = array($url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.jpg', $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.jpg');
                                    break;
                                case "audio":
                                    $f['mp3'] = array($url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.mp3', $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.mp3');
                                    $f['mp4'] = array($url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.mp4', $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.mp4');
                                    break;
                                case "doc":
                                    $f['pdf'] = array($url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.pdf', $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.pdf');
                                    break;
                                case "short":
                                    $f['short'] = array(
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $file_id . '.short.mp4',
                                        $url . '/' . $user_key . '/' . $type[0] . '/' . $ds . '.short.mp4',
                                    );
                                    break;
                            }
                        }

                        if ($rs->fields["upload_" . $this->type[0] . "_thumb"] == 1 and $send_thumbs >= 1) {
                            $t      = array();
                            $tcache = VFiles::thumb_cache($this->type, $file_id);
                            for ($i = 0; $i <= $this->cfg['thumbs_nr']; $i++) {
                                $t[] = $url . '/' . $user_key . '/t/' . $file_id . '/' . $i . $tcache . '.jpg';
                            }

                            $f['tmb'] = $t;
                        } else {
                            echo "upload_" . $this->type[0] . "_thumb -> " . $rs->fields["upload_" . $this->type[0] . "_thumb"];
                            echo "\n";
                            echo '$send_thumbs -> ' . $send_thumbs;
                            echo "\n";
                        }

                        if ($rs->fields["upload_" . $this->type[0] . "_file"] == 1 and $send_thumbs == 0) {
                            $this->db->execute(sprintf("UPDATE `db_%stransfers` SET `upload_server`='%s', `state`='1', `upload_start_time`='%s' WHERE `file_key`='%s' LIMIT 1;", $type, $rs->fields['server_id'], date("Y-m-d H:i:s"), $file_id));
                        }

                        foreach ($f as $fk => $fv) {
                            if ($fk == 'tmb') {
                                $this->db->execute(sprintf("UPDATE `db_%stransfers` SET `thumb_server`='%s', `state`='1', `thumb_start_time`='%s' WHERE `file_key`='%s' LIMIT 1;", $type, $rs->fields['server_id'], date("Y-m-d H:i:s"), $file_id));
                            }
                            $this->db->execute(sprintf("UPDATE `db_%sfiles` SET `%s_server`='%s' WHERE `file_key`='%s' LIMIT 1;", $this->type, ($fk != 'tmb' ? 'upload' : 'thumb'), $rs->fields['server_id'], $file_id));
                            foreach ($fv as $src) {
                                if (file_exists($src)) {
                                    $xp               = $fk != 'tmb' ? explode('.', $src) : explode('/', $src);
                                    $ct               = count($xp);
                                    $profile          = ($fk != 'tmb' and $type == 'video') ? $xp[$ct - 2] . '.' . $xp[$ct - 1] : $xp[$ct - 1];
                                    $result[$profile] = array();

                                    if ($fk == 'tmb') {
                                        list($width, $height, $filetype, $attr) = getimagesize($src);
                                    }

                                    $gs  = md5($this->cfg['global_salt_key'] . $file_id);
                                    $fid = $file_id;
                                    if (strpos($src, $gs) !== false) {
                                        $fid = $gs;
                                    }

                                    echo "location => " . $src . "\n";
                                    echo "size => " . filesize($src) . "\n";
                                    echo ($fk != 'tmb' ? $this->type : "thumb") . " profile => " . $profile . "\n\n";
                                    echo "---------Transfer started " . date("F j, Y, G:i:s") . "\n";

                                    $key              = $fk != 'tmb' ? $s3->encodeKey($user_key . '/' . $this->type[0] . '/' . $fid . '.' . $profile) : $s3->encodeKey($user_key . '/t/' . $file_id . '/' . $profile);
                                    $result[$profile] = $s3->upload($bucketName, $key, fopen($src, 'r'), $rs->fields['s3_fileperm']);

                                    if ($result[$profile]['ObjectURL']) {
                                        echo "Success: " . $result[$profile]['ObjectURL'] . "\n\n";
                                    } else {
                                        echo "Warning: No ObjectURL returned, but transfer might have gotten through.\n";
                                    }

                                    $local_file = $src;
                                    echo "Updating local files: \n\n";
                                    echo ($action == 0 ? "Unlinking" : "Renaming") . " file " . $local_file . "\n";

                                    if ($action == 0) {
                                        $_png = str_replace('.jpg', '.png', $local_file);
                                        if (unlink($local_file)) {
                                            if (file_exists($_png)) {
                                                echo "PNG unlinked $_png\n";
                                                unlink($_png);}
                                            echo "OK\n";
                                        } else {
                                            echo "Failed\n";
                                        }
                                    } else {
                                        if (rename($local_file, $local_file . '.old')) {
                                            echo "OK\n";
                                        } else {
                                            echo "Failed\n";
                                        }
                                    }
                                    echo "\n";
                                    echo "Creating new " . $profile . " dummy file \n";

                                    if (file_put_contents($local_file, ($fk == 'tmb' ? $width . 'x' . $height : $file_id))) {
                                        echo "OK\n\n";
                                    }

                                    echo "---------Transfer ended " . date("F j, Y, G:i:s") . "\n\n\n";

                                    $sql = sprintf("UPDATE `db_%stransfers` SET `%s_end_time`='%s' WHERE `file_key`='%s' LIMIT 1;", $type, ($fk != 'tmb' ? 'upload' : 'thumb'), date("Y-m-d H:i:s"), $file_id);
                                    $this->db->execute($sql);
                                }
                            }
                        }

                        echo "Updating database: \n\n";

                        if ($send_thumbs == 0 or $send_thumbs == 2) {
                            $sql = sprintf("UPDATE `db_%stransfers` SET `state`='2' WHERE `file_key`='%s' LIMIT 1;", $type, $file_id);
                            echo $sql . "\n";
                            $this->db->execute($sql);
                            echo $res = $this->db->Affected_Rows() > 0 ? "OK\n" : "Failed\n";
                            echo "\n";
                        }
                        if ($send_thumbs == 0 or $send_thumbs == 2 or ($send_thumbs == 1 and intval($rs->fields["upload_" . $this->type[0] . "_file"]) != intval($rs->fields["upload_" . $this->type[0] . "_thumb"]))) {
                            $sql = sprintf("UPDATE `db_servers` SET `current_hop`=`current_hop`+1, `total_%s`=`total_%s`+1, `last_used`='%s' WHERE `server_id`='%s' LIMIT 1;",
                                $this->type, $this->type, date("Y-m-d H:i:s"), $rs->fields['server_id']);
                            echo $sql . "\n";
                            $this->db->execute($sql);
                            echo $res = $this->db->Affected_Rows() > 0 ? "OK\n" : "Failed\n";
                            echo "\n";
                        }
                    }
                }
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage();
            }

            $value = ob_get_contents();
            ob_end_clean();

            $this->log($value);

            if ($rs->fields["upload_" . $this->type[0] . "_file"] == 1 and $rs->fields["upload_" . $this->type[0] . "_thumb"] == 1) {
                return;
            }

            if (($rs->fields["upload_" . $this->type[0] . "_file"] == 0 and $send_thumbs == 0) or ($rs->fields["upload_" . $this->type[0] . "_thumb"] == 0 and $send_thumbs >= 1)) {
                $file_type = $this->type;

                switch ($file_type) {
                    case "video":
                        $file_ext = '.360p.mp4';
                        break;
                    case "short":
                        $file_ext = '.short.mp4';
                        break;
                    case "image":
                        $file_ext = '.jpg';
                        break;
                    case "audio":
                        $file_ext = '.mp3';
                        break;
                    case "doc":
                        $file_ext = '.pdf';
                        break;
                }
                $tcache = VFiles::thumb_cache($this->type, $file_id);
                $src    = $rs->fields["upload_" . $this->type[0] . "_file"] == 0 ? $this->cfg['media_files_dir'] . '/' . $user_key . '/' . $this->type[0] . '/' . $file_id . $file_ext : $this->cfg['media_files_dir'] . '/' . $user_key . '/t/' . $file_id . '/0' . $tcache . '.jpg';

                $xfer = new fileTransfer();

                if ($xfer->load($src, $file_type)) {
                    $xfer->log_setup($file_id, true);
                    $xfer->log("Preparing " . ($rs->fields["upload_" . $this->type[0] . "_file"] == 0 ? $this->type : "thumb") . ": " . $src . "\n\n" . $xfer->get_data_string() . "\n");

                    $xfer->doTransfer($file_id, $user_key, $src, 0, ($rs->fields["upload_" . $this->type[0] . "_file"] == 0 ? 0 : 1), $rs->fields['server_id']);
                } else {
                    $xfer->log_setup($file_id, true);
                    $xfer->log("Failed to load file: " . $src);
                }
                return;
            }

            return;

        }

        $ftp_host = $rs->fields['ftp_host'];
        $ftp_port = intval($rs->fields['ftp_port']);
        $ftp_xfer = $rs->fields['ftp_transfer'];
        $ftp_pssv = $rs->fields['ftp_passive'];
        $ftp_user = $rs->fields['ftp_username'];
        $ftp_pass = $rs->fields['ftp_password'];
        $ftp_root = $rs->fields['ftp_root'];

        ob_start();
        $ftp            = new ftp(true);
        $ftp->Verbose   = false;
        $ftp->LocalEcho = true;
        /* set hostname */
        if (!$ftp->SetServer($ftp_host, $ftp_port)) {
            $ftp->quit();
            echo "Error: No server\n";
        }
        /* connect to host */
        if (!$ftp->connect()) {
            echo "Error: Cannot connect\n";
        }
        /* send login */
        if (!$ftp->login($ftp_user, $ftp_pass)) {
            $ftp->quit();
            echo "Error: Login failed\n";
        }
        /* transfer type */
        if (!$ftp->SetType(($ftp_xfer == 'FTP_AUTOASCII' ? FTP_AUTOASCII : ($ftp_xfer == 'FTP_ASCII' ? FTP_ASCII : FTP_BINARY)))) {
            echo $err = "Error: SetType " . $ftp_xfer . " failed!\n";
        }

        if (!$ftp->Passive(false)) {
            echo $err = "Error: Passive mode failed!\n";
        }

        /* main folder check */
        if (!$ftp->is_exists($ftp_root)) {
            echo "Error: Main folder not found " . $ftp_root . "\n";
            echo "Notice: Attempting to create main folder " . $ftp_root . "\n";
            $ftp->mkdir($ftp_root);
            if (!$ftp->is_exists($ftp_root)) {
                echo "Error: Main folder could not be created " . $ftp_root . "\n";
                $ftp->quit();
            } else {
                echo "Notice: Main folder created " . $ftp_root . "\n";
            }
        }
        echo "\n\n";
        /* user folder check */
        $user_dir = $ftp_root . '/' . $user_key;
        if (!$ftp->is_exists($user_dir)) {
            echo "Error: User folder not found " . $user_dir . "\n";
            echo "Notice: Attempting to create user folder " . $user_dir . "\n";

            $ftp->mkdir($user_dir);
            if (!$ftp->is_exists($user_dir)) {
                echo "Error: User folder could not be created " . $user_dir . "\n";
                $ftp->quit();
            } else {
                echo "Notice: User folder created " . $user_dir . "\n";
            }
        }

        $ts   = $this->db->execute(sprintf("SELECT `thumb_server` FROM `db_%stransfers` WHERE `file_key`='%s' LIMIT 1;", $this->type, $file_id));
        $tsrv = $ts->fields['thumb_server'];

        if ($ftp->is_exists($user_dir)) {
            if ($rs->fields["upload_" . $this->type[0] . "_file"] == 1) {
                $ftp->mkdir($user_dir . '/' . $this->type[0]);
            }
            if ($rs->fields["upload_" . $this->type[0] . "_thumb"] == 1) {
                if ($tsrv == 0 or ($tsrv > 0 and $tsrv == $rs->fields['server_id'])) {
                    $ftp->mkdir($user_dir . '/t');
                    $ftp->mkdir($user_dir . '/t/' . $file_id);
                }
            }
        }

        if ($rs->fields["upload_" . $this->type[0] . "_file"] == 1 and $send_thumbs == 0) {
            $ftp->chdir($user_dir . '/' . $this->type[0]);
            echo "\n\n";

            switch ($this->type) {
                case "video":
                    $a1 = array('1080p', '1080pf', '720p', '720pf', '480p', '480pf', 'mob', 'mobf', '360p', '360pf');

                    foreach ($a1 as $bp) {
                        if (self::transferFile($ftp, $rs, $user_key, $file_id, $user_dir, $bp)) {
                        }
                        if (self::transferFile($ftp, $rs, $user_key, $file_id, $user_dir, $bp, 'webm')) {
                        }
                        if (self::transferFile($ftp, $rs, $user_key, $file_id, $user_dir, $bp, 'ogv')) {
                        }
                    }
                    break;
                case "short":
                    $a1 = array('short', 'shortf');

                    foreach ($a1 as $bp) {
                        if (self::transferFile($ftp, $rs, $user_key, $file_id, $user_dir, $bp)) {
                        }
                    }
                    break;
                case "image":
                    $a1 = array('jpg', 'jpgf');

                    foreach ($a1 as $bp) {
                        if (self::transferFile($ftp, $rs, $user_key, $file_id, $user_dir, $this->type, $bp)) {
                        }
                    }
                    break;
                case "audio":
                    $a1 = array('mp3', 'mp3f', 'mp4', 'mp4f');

                    foreach ($a1 as $bp) {
                        if (self::transferFile($ftp, $rs, $user_key, $file_id, $user_dir, $this->type, $bp)) {
                        }
                    }
                    break;
                case "doc":
                    $a1 = array('pdf', 'pdff');

                    foreach ($a1 as $bp) {
                        if (self::transferFile($ftp, $rs, $user_key, $file_id, $user_dir, $this->type, $bp)) {
                        }
                    }
                    break;
            }
        }

        if ($tsrv > 0 and $tsrv != $rs->fields['server_id'] and $send_thumbs >= 1) {
            $file_type = $this->type;
            $tcache    = VFiles::thumb_cache($this->type, $file_id);
            $src       = $this->cfg['media_files_dir'] . '/' . $user_key . '/t/' . $file_id . '/0' . $tcache . '.jpg';
            $xfer      = new fileTransfer();

            if ($xfer->load($src, $file_type)) {
                $xfer->log_setup($file_id, true);
                $xfer->log("Preparing thumbnail: " . $src . "\n\n" . $xfer->get_data_string() . "\n");

                $xfer->doTransfer($file_id, $user_key, $src, $tsrv, 1, $rs->fields['server_id']);
            } else {
                $xfer->log_setup($file_id, true);
                $xfer->log("Failed to load file: " . $src);
            }

            echo "\n---------------------------- end ----------------------------\n";

            $value = ob_get_contents();
            ob_end_clean();

            $this->log($value);

            return;
        }

        if ($send_thumbs >= 1) {
            if ($rs->fields["upload_" . $this->type[0] . "_thumb"] == 1) {
                $ftp->chdir($user_dir . '/t/' . $file_id);
                echo "\n\n";

                self::transferThumb($ftp, $rs, $user_key, $file_id, $user_dir, 'thumb');
            } else {
                $file_type = $this->type;
                $tcache    = VFiles::thumb_cache($this->type, $file_id);
                $src       = $this->cfg['media_files_dir'] . '/' . $user_key . '/t/' . $file_id . '/0' . $tcache . '.jpg';

                $nrs = $this->db->execute(sprintf("SELECT *
                            FROM
                            `db_servers`
                            WHERE
                            `server_id`!='%s' AND
                            `status`='1' AND
                            `upload_%s_thumb`='1' AND
                            (`total_%s` < `server_limit` AND
                            `current_hop` < `file_hop`)
                            ORDER BY `server_priority` DESC LIMIT 1;",
                    $rs->fields['server_id'], $this->type[0], $this->type));

                if (!$nrs->fields['server_id']) {
                    echo "Error: No thumb servers found.\n";
                    $this->db->execute(sprintf("UPDATE `db_servers` SET `current_hop`='0' WHERE `server_id`!='%s' AND `file_hop`=`current_hop` AND `total_%s`<=`server_limit` AND `status`='1';", $rs->fields['server_id'], $this->type));
                    $ar = $this->db->Affected_Rows();
                    $this->log("Trying to reset file hops: " . $ar . " " . ($ar == 1 ? "server" : "servers") . " found." . ($ar == 0 ? " (all servers full or disabled?!)" : null));

                    $nrs = $this->db->execute(sprintf("SELECT *
                            FROM
                            `db_servers`
                            WHERE
                            `server_id`!='%s' AND
                            `status`='1' AND
                            `upload_%s_thumb`='1' AND
                            (`total_%s` < `server_limit` AND
                            `current_hop` < `file_hop`)
                            ORDER BY `server_priority` DESC LIMIT 1;",
                        $rs->fields['server_id'], $this->type[0], $this->type));

                }

                if ($nrs->fields['server_id']) {
                    $xfer = new fileTransfer();

                    if ($xfer->load($src, $file_type)) {
                        $xfer->log_setup($file_id, true);
                        $xfer->log("Preparing thumbnail: " . $src . "\n\n" . $xfer->get_data_string() . "\n");

                        $xfer->doTransfer($file_id, $user_key, $src, $nrs->fields['server_id'], 1, $rs->fields['server_id']);
                    } else {
                        $xfer->log_setup($file_id, true);
                        $xfer->log("Failed to load file: " . $src);
                    }
                }
            }
        }
        echo "\n---------------------------- end ----------------------------\n";

        $value = ob_get_contents();
        ob_end_clean();

        $this->log($value);
    }
    public function transferThumb($ftp, $rs, $user_key, $file_id, $user_dir, $br = 'thumb')
    {
        $action = 0; //0 = delete local file after transferring; 1 = rename local file after transferring;
        $tnr    = $this->cfg['thumbs_nr'];
        $sql    = sprintf("UPDATE `db_%stransfers` SET `state='1' WHERE `file_key`='%s' LIMIT 1;", $this->type, $file_id);
        $this->db->execute($sql);

        for ($i = 0; $i <= $tnr; $i++) {
            $tcache      = VFiles::thumb_cache($this->type, $file_id);
            $local_file  = $this->cfg['media_files_dir'] . '/' . $user_key . '/t/' . $file_id . '/' . $i . $tcache . '.jpg';
            $remote_file = $user_dir . '/t/' . $file_id . '/' . $i . $tcache . '.jpg';

            if (!file_exists($local_file)) {
                return;
            }

            list($width, $height, $type, $attr) = getimagesize($local_file);

            $start_time = date("Y-m-d H:i:s");
            echo "\n\n" . ucfirst($this->type) . " thumbnail: " . $i . $tcache . ".jpg";
            echo "\nTransfer started " . date("F j, Y, G:i:s") . "\n";

            if ($ftp->put($local_file, $remote_file)) {
                $local_size  = filesize($local_file);
                $remote_size = $ftp->filesize($remote_file);
                echo "Transfer ended " . date("F j, Y, G:i:s");
                if ($remote_size == $local_size) {
                    echo "\n\n";
                    echo "Local file size: " . $local_size . "\n";
                    echo "Remote file size: " . $remote_size . "\n";
                    echo "\n\n";

                    echo "Updating local files: \n\n";
                    echo ($action == 0 ? "Unlinking" : "Renaming") . " thumb " . $local_file . "\n";

                    if ($action == 0) {
                        if (unlink($local_file)) {
                            echo "OK\n";
                        } else {
                            echo "Failed\n";
                        }
                    } else {
                        if (rename($local_file, $local_file . '.old')) {
                            echo "OK\n";
                        } else {
                            echo "Failed\n";
                        }
                    }
                    echo "\n";
                    echo "Creating new " . $br . " dummy file \n";
                    if (file_put_contents($local_file, $width . 'x' . $height)) {
                        echo "OK\n\n";
                    }
                } else {
                    echo "\n";
                    echo "Error: File sizes do not match: local (" . $local_size . ") / remote (" . $remote_size . ")";
                }
            } else {
                echo "Error: The file was not transferred\n";
            }

            if ($br == 'thumb' and $i == 0) {
                echo "Updating database: \n\n";
                $sql = sprintf("UPDATE `db_%sfiles` SET `thumb_server`='%s' WHERE `file_key`='%s' LIMIT 1;", $this->type, $rs->fields['server_id'], $file_id);
                echo $sql . "\n";
                $this->db->execute($sql);
                echo $res = $this->db->Affected_Rows() > 0 ? "OK\n" : "Failed\n";
                echo "\n";
                $sql = sprintf("UPDATE `db_%stransfers` SET `thumb_server`='%s', `thumb_start_time`='%s', `thumb_end_time`='%s', `state`='2' WHERE `file_key`='%s' LIMIT 1;", $this->type, $rs->fields['server_id'], $start_time, date("Y-m-d H:i:s"), $file_id);
                echo $sql . "\n";
                $this->db->execute($sql);
                echo $res = $this->db->Affected_Rows() > 0 ? "OK\n" : "Failed\n";
                echo "\n";

                if ($rs->fields["upload_" . $this->type[0] . "_file"] == 0 and $rs->fields["upload_" . $this->type[0] . "_thumb"] == 1) {
                    $sql = sprintf("UPDATE `db_servers` SET `current_hop`=`current_hop`+1, `total_%s`=`total_%s`+1, `last_used`='%s' WHERE `server_id`='%s' LIMIT 1;",
                        $this->type, $this->type, date("Y-m-d H:i:s"), $rs->fields['server_id']);
                    echo $sql . "\n";
                    $this->db->execute($sql);
                    echo $res = $this->db->Affected_Rows() > 0 ? "OK\n" : "Failed\n";
                    echo "\n";
                }
            }
        }
    }
    /* transfer file */
    public function transferFile($ftp, $rs, $user_key, $file_id, $user_dir, $br, $ext = 'mp4')
    {
        $action = 0; //0 = delete local file after transferring; 1 = rename local file after transferring;

        switch ($br) {
            case "image":
            case "audio":
            case "doc":
                $fk          = ($br == 'jpgf' or $br == 'mp3f' or $br == 'mp4f' or $br == 'pdff') ? md5($this->cfg['global_salt_key'] . $file_id) : $file_id;
                $local_file  = $this->cfg['media_files_dir'] . '/' . $user_key . '/' . $this->type[0] . '/' . $fk . '.' . str_replace(array('f', 'pd'), array('', 'pdf'), $br) . '.' . $ext;
                $remote_file = $user_dir . '/' . $this->type[0] . '/' . $fk . '.' . str_replace(array('f', 'pd'), array('', 'pdf'), $br) . '.' . $ext;
                break;
            default:
                $fk          = ($br == '360pf' or $br == '480pf' or $br == '720pf' or $br == '1080pf' or $br == 'mobf') ? md5($this->cfg['global_salt_key'] . $file_id) : $file_id;
                $local_file  = $this->cfg['media_files_dir'] . '/' . $user_key . '/' . $this->type[0] . '/' . $fk . '.' . str_replace('f', '', $br) . '.' . $ext;
                $remote_file = $user_dir . '/' . $this->type[0] . '/' . $fk . '.' . str_replace('f', '', $br) . '.' . $ext;
                break;
        }

        if (!file_exists($local_file)) {
/*        echo "\nLocal file not found: ".$local_file;
echo "\nProfile: ".$br." - Key: ".$fk;
echo "\nID: ".$file_id;
echo "\nSalt: ".$this->cfg['global_salt_key'];*/

            return;
        }

        $start_time = date("Y-m-d H:i:s");
        echo "\nFile profile: " . $ext . "/" . $br;
        echo "\nTransfer started " . date("F j, Y, G:i:s") . "\n";

        if ($ftp->put($local_file, $remote_file)) {
            $local_size  = filesize($local_file);
            $remote_size = $ftp->filesize($remote_file);
            echo "Transfer ended " . date("F j, Y, G:i:s");
            if ($remote_size == $local_size) {
                echo "\n\n";
                echo "Local file size: " . $local_size . "\n";
                echo "Remote file size: " . $remote_size . "\n";
                echo "\n\n";

                if (($br == '360p' and $ext == 'mp4') or $ext == 'jpg' or $ext == 'mp3' or $ext == 'pdf') {
                    echo "Updating database: \n\n";
                    $sql = sprintf("UPDATE `db_%sfiles` SET `upload_server`='%s' WHERE `file_key`='%s' LIMIT 1;", $this->type, $rs->fields['server_id'], $file_id);
                    echo $sql . "\n";
                    $this->db->execute($sql);
                    echo $res = $this->db->Affected_Rows() > 0 ? "OK\n" : "Failed\n";
                    echo "\n";
                    $sql = sprintf("UPDATE `db_%stransfers` SET `upload_server`='%s', `upload_start_time`='%s', `upload_end_time`='%s', `state`='2' WHERE `file_key`='%s' LIMIT 1;", $this->type, $rs->fields['server_id'], $start_time, date("Y-m-d H:i:s"), $file_id);
                    echo $sql . "\n";
                    $this->db->execute($sql);
                    echo $res = $this->db->Affected_Rows() > 0 ? "OK\n" : "Failed\n";
                    echo "\n";
                }
                if ($br == '360pf' and $ext == 'mp4') {
                    echo "Updating database: \n\n";
                    $sql = sprintf("UPDATE `db_%sfiles` SET `upload_server`='%s' WHERE `file_key`='%s' LIMIT 1;", $this->type, $rs->fields['server_id'], $file_id);
                    echo $sql . "\n";
                    $this->db->execute($sql);
                    echo $res = $this->db->Affected_Rows() > 0 ? "OK\n" : "Failed\n";
                    echo "\n";

                    $sql = sprintf("UPDATE `db_servers` SET `current_hop`=`current_hop`+1, `total_%s`=`total_%s`+1, `last_used`='%s' WHERE `server_id`='%s' LIMIT 1;", $this->type, $this->type, date("Y-m-d H:i:s"), $rs->fields['server_id']);
                    echo $sql . "\n";
                    $this->db->execute($sql);
                    echo $res = $this->db->Affected_Rows() > 0 ? "OK\n" : "Failed\n";
                    echo "\n";
                }

                echo "Updating local files: \n\n";
                echo ($action == 0 ? "Unlinking" : "Renaming") . " file " . $local_file . "\n";

                if ($action == 0) {
                    if (unlink($local_file)) {
                        echo "OK\n";
                    } else {
                        echo "Failed\n";
                    }
                } else {
                    if (rename($local_file, $local_file . '.old')) {
                        echo "OK\n";
                    } else {
                        echo "Failed\n";
                    }
                }
                echo "\n";
                echo "Creating new " . $ext . "/" . $br . " dummy file \n";
                if (file_put_contents($local_file, utf8_encode($file_id))) {
                    echo "OK\n\n";
                }

                return true;
            } else {
                echo "\n";
                echo "Error: File sizes do not match: local (" . $local_size . ") / remote (" . $remote_size . ")";
                return false;
            }
        } else {
            echo "Error: The file was not transferred\n";
            return false;
        }
    }
    public function get_data_string()
    {
        $string = null;
        foreach ($this->data as $key => $value) {
            $string .= $key . ' => ' . $value . "\n";
        }
        return $string;
    }
    public function get_data()
    {
        return $this->data;
    }

    public function log_setup($file_id, $log = true)
    {
        if ($log === true) {
            $ddir      = 'log_xfer/' . date("Y.m.d") . '/';
            $full_path = $this->cfg['logging_dir'] . '/' . $ddir . $path . '.' . $this->type . '_' . $file_id . '.log';

            if (!file_exists($full_path)) {
                @touch($full_path);
            }

            if ($ddir != '' and !is_dir($this->cfg['logging_dir'] . '/' . $ddir)) {
                @mkdir($this->cfg['logging_dir'] . '/' . $ddir);
            }

        }
        $this->log      = ($log === true) ? true : false;
        $this->log_file = $full_path;
    }
    public function log($data)
    {
        if ($this->log !== false) {
            if (!file_exists($this->log_file)) {
                @touch($this->log_file);
            }

            VFileinfo::write($this->log_file, $data . "\n", true);
        }
    }
    public function clean()
    {
        $this->data = array();
        if ($this->log_file) {
            // VFile::delete($this->log_file);
        }
        $this->log_file = '';
    }
}

class fileQueue
{
    private $cfg;
    private $db;
    private $cdb;

    public $type;

    public function __construct()
    {
        global $db, $class_database, $cfg;

        $this->db  = $db;
        $this->cdb = $class_database;
        $this->cfg = $cfg;

        $this->cfg = $this->cdb->getConfigurations("server_path_php,pause_video_transfer,pause_short_transfer,pause_image_transfer,pause_audio_transfer,pause_doc_transfer,conversion_video_que,conversion_short_que,conversion_image_que,conversion_audio_que,conversion_doc_que");
    }
    public function load($t)
    {
        $this->type = $t;

        return true;
    }
    public function check()
    {
        $found    = 0;
        $file     = "transfer.php";
        $conv1    = "convert_video.php";
        $conv11   = "convert_short.php";
        $conv2    = "convert_image.php";
        $conv3    = "convert_audio.php";
        $conv4    = "convert_doc.php";
        $conv5    = "ffmpeg";
        $yt       = "youtube-dl";
        $dl       = "download_video.php";
        $commands = array();

        exec("ps ax", $commands);

        if (count($commands) > 0) {
            foreach ($commands as $command) {
                if (strpos($command, $yt) === false and strpos($command, $conv1) === false and strpos($command, $conv11) === false and strpos($command, $conv2) === false and strpos($command, $conv3) === false and strpos($command, $conv4) === false and strpos($command, $conv5) === false and strpos($command, $dl) === false) {
                    //what ?
                    //$found = 0;
                } else {
                    $found++;
                    $in = $conv;
                }
                if ($found == 0) {
                    if (strpos($command, $file) === false) {
                        //what ?
                    } else {
                        $found++;
                        $in = $file;
                    }
                }
            }
        }
        if ($found > 0) {
            echo "Another process is running (" . $in . ").\n";
            return false;
        }
        /* regular process here */
        echo "Made it through all checks, transfer can start (" . $this->type . ").\n";
        return true;
    }
    public function startTransfer()
    {
//            global $class_database;
        /* transfer state:
        0 = idle (waiting to start)
        1 = running
        2 = completed
         */
        if ($this->cfg["pause_" . $this->type . "_transfer"] == 1) {
            return;
        }

        $sql      = sprintf("SELECT `upload_server`, `thumb_server`, `file_key`, `usr_key`, `active` FROM `db_%stransfers` WHERE `upload_start_time`='0000-00-00 00:00:00' AND `upload_end_time`='0000-00-00 00:00:00' AND `state`='0' AND `active`='1' ORDER BY `q_id` DESC LIMIT 1;", $this->type);
        $res      = $this->db->execute($sql);
        $file_key = $res->fields['file_key'];
        $usr_key  = $res->fields['usr_key'];
        $srv_id   = $res->fields['upload_server'];
        $tmb_id   = $res->fields['thumb_server'];
        $active   = $res->fields['active'];
        $active   = $active == '' ? 1 : $active;
        $inq      = 0;

        if ($this->cfg["conversion_" . $this->type . "_que"] == 1) {
            $sql = sprintf("SELECT `q_id` FROM `db_%sque` WHERE `file_key`='%s' AND `state` < 2 LIMIT 1;", $this->type, $file_key);
            $rs  = $this->db->execute($sql);
            $inq = $rs->fields['q_id'];

            if (!$inq and $this->type == "video") {
                $sql = sprintf("SELECT `q_id` FROM `db_%sdl` WHERE `file_key`='%s' AND `state` < 2 LIMIT 1;", $this->type, $file_key);
                $rs  = $this->db->execute($sql);
                $inq = $rs->fields['q_id'];
            }
        }

        if ($active == 0 or $inq > 0) {
            return;
        }

        $sql = sprintf("SELECT `embed_src` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $this->type, $file_key);
        $get = $this->db->execute($sql);
        $src = $get->fields['embed_src'];

        if ($file_key != '' and $usr_key != '') {
            if ($src == 'local') {
                $cmd = sprintf("%s %s/f_modules/m_frontend/m_file/transfer.php %s %s %s %s", $this->cfg['server_path_php'], $this->cfg['main_dir'], $this->type, $file_key, $usr_key, $srv_id);
                exec(escapeshellcmd($cmd) . ' >/dev/null &');
            }

            $send_thumbs = $src == 'local' ? 1 : 2;

            $cmd = sprintf("%s %s/f_modules/m_frontend/m_file/transfer.php %s %s %s %s %s", $this->cfg['server_path_php'], $this->cfg['main_dir'], $this->type, $file_key, $usr_key, $tmb_id, $send_thumbs);
            exec(escapeshellcmd($cmd) . ' >/dev/null &');
        } else {
            $sql = sprintf("SELECT
                            A.`file_key`, A.`upload_server`,
                            B.`usr_key`
                            FROM
                            `db_%sfiles` A, `db_accountuser` B
                            WHERE
                            A.`usr_id`=B.`usr_id` AND
                            A.`upload_server`='0' AND
                            A.`thumb_server`='0'
                            ORDER BY A.`db_id` ASC LIMIT 1;", $this->type);
            $res = $this->db->execute($sql);

            $file_key = $res->fields['file_key'];
            $usr_key  = $res->fields['usr_key'];
            $srv_id   = (int) $res->fields['upload_server'];

            $sql_add = ($srv_id == 0 and $file_key and $usr_key) ? $this->cdb->doInsert('db_' . $this->type . 'transfers', array("upload_server" => $srv_id, "file_key" => $file_key, "usr_key" => $usr_key)) : null;
        }
    }
    public function __destruct()
    {
    }
}

class VbeServers
{
    /* main server details */
    public function mainServerDetails($_dsp = '', $entry_id = '', $db_id = '', $server_status = '', $server_name = '', $server_type = '', $server_priority = '', $server_limit = '', $server_filehop = '', $upload_v_file = '', $upload_v_thumb = '', $upload_i_file = '', $upload_i_thumb = '', $upload_a_file = '', $upload_a_thumb = '', $upload_d_file = '', $upload_d_thumb = '', $ftp_transfer = '', $ftp_passive = '', $ftp_host = '', $ftp_port = '', $ftp_user = '', $ftp_pass = '', $ftp_root = '', $base_url = '', $lighttpd_url = '', $lighttpd_secdownload = '', $lighttpd_prefix = '', $lighttpd_key = '', $s3_bucketname = '', $s3_accesskey = '', $s3_secretkey = '', $s3_region = '', $s3_fileperm = '', $cf_enable = '', $cf_dist = '', $cf_priceclass = '', $surl_enable = '', $surl_expire = '', $keypair_id = '', $keypair_file = '', $upload_s_file = '', $upload_s_thumb = '')
    {
        global $class_filter;

        if ($_POST and ($_GET['do'] == 'add' or $_GET['do'] == 'update')) {
            self::processEntry();
            $entryid                       = (int) $db_id;
            $backend_servers_type          = "backend_servers_type_" . $entryid;
            $backend_servers_transfer      = "backend_servers_transfer_" . $entryid;
            $backend_servers_passive       = "backend_servers_passive_" . $entryid;
            $backend_s3_region             = "backend_s3_region_" . $entryid;
            $backend_servers_cf_priceclass = "backend_servers_cf_priceclass_" . $entryid;

            $server_name          = $class_filter->clr_str($_POST['frontend_global_name']);
            $server_type          = $class_filter->clr_str($_POST[$backend_servers_type]);
            $server_priority      = intval($_POST['backend_servers_priority']);
            $server_limit         = intval($_POST['backend_servers_limit']);
            $server_filehop       = intval($_POST['backend_servers_hop']);
            $upload_v_file        = intval($_POST['backend_servers_ct_video']);
            $upload_v_thumb       = intval($_POST['backend_servers_ct_vthumb']);
            $upload_s_file        = intval($_POST['backend_servers_ct_short']);
            $upload_s_thumb       = intval($_POST['backend_servers_ct_sthumb']);
            $upload_i_file        = intval($_POST['backend_servers_ct_image']);
            $upload_i_thumb       = intval($_POST['backend_servers_ct_ithumb']);
            $upload_a_file        = intval($_POST['backend_servers_ct_audio']);
            $upload_a_thumb       = intval($_POST['backend_servers_ct_athumb']);
            $upload_d_file        = intval($_POST['backend_servers_ct_doc']);
            $upload_d_thumb       = intval($_POST['backend_servers_ct_dthumb']);
            $ftp_transfer         = $class_filter->clr_str($_POST[$backend_servers_transfer]);
            $ftp_passive          = intval($_POST[$backend_servers_passive]);
            $ftp_host             = $class_filter->clr_str($_POST['backend_servers_host']);
            $ftp_port             = $class_filter->clr_str($_POST['backend_servers_port']);
            $ftp_user             = $class_filter->clr_str($_POST['backend_servers_user']);
            $ftp_pass             = $class_filter->clr_str($_POST['backend_servers_pass']);
            $ftp_root             = $class_filter->clr_str($_POST['backend_servers_root']);
            $base_url             = $class_filter->clr_str($_POST['backend_servers_url']);
            $lighttpd_url         = $class_filter->clr_str($_POST['stream_url']);
            $lighttpd_secdownload = $class_filter->clr_str($_POST['stream_secure']);
            $lighttpd_prefix      = $class_filter->clr_str($_POST['stream_prefix']);
            $lighttpd_key         = $class_filter->clr_str($_POST['stream_key']);
            $s3_bucketname        = $class_filter->clr_str($_POST['backend_s3_bucketname']);
            $s3_accesskey         = $class_filter->clr_str($_POST['backend_s3_accesskey']);
            $s3_secretkey         = $class_filter->clr_str($_POST['backend_s3_secretkey']);
            $s3_region            = $class_filter->clr_str($_POST[$backend_s3_region]);
            $s3_fileperm          = $class_filter->clr_str($_POST['backend_s3_fileperm']);
            $cf_enable            = $class_filter->clr_str($_POST['backend_servers_cf_enable']);
            $cf_dist              = $class_filter->clr_str($_POST['backend_servers_cf_dist']);
            $cf_priceclass        = $class_filter->clr_str($_POST[$backend_servers_cf_priceclass]);
            $surl_enable          = intval($_POST['backend_servers_surl_enable']);
            $surl_expire          = intval($_POST['backend_servers_surl_time']);
            $keypair_id           = $class_filter->clr_str($_POST['backend_servers_keypair_id']);
            $keypair_file         = $class_filter->clr_str($_POST['backend_servers_keypair_file']);
        }

        return self::ServerDetails($_dsp, $entry_id, $db_id, $server_status, $server_name, $server_type, $server_priority, $server_limit, $server_filehop, $upload_v_file, $upload_v_thumb, $upload_i_file, $upload_i_thumb, $upload_a_file, $upload_a_thumb, $upload_d_file, $upload_d_thumb, $ftp_transfer, $ftp_passive, $ftp_host, $ftp_port, $ftp_user, $ftp_pass, $ftp_root, $base_url, '', '', '', '', '', '', '', $lighttpd_url, $lighttpd_secdownload, $lighttpd_prefix, $lighttpd_key, $s3_bucketname, $s3_accesskey, $s3_secretkey, $s3_region, $s3_fileperm, $cf_enable, $cf_dist, $cf_priceclass, $surl_enable, $surl_expire, $keypair_id, $keypair_file, $upload_s_file, $upload_s_thumb);
    }
    public function getPostID()
    {
        global $class_filter;

        return $class_filter->clr_str($_POST['hc_id']);
    }
    public function region()
    {
        return array(
            "us-east-1"      => "US East (N. Virginia)",
            "us-east-2"      => "US East (Ohio)",
            "us-west-1"      => "US West (N. California)",
            "us-west-2"      => "US West (Oregon)",
            "ca-central-1"   => "Canada (Central)",
            "eu-west-1"      => "EU (Ireland)",
            "eu-west-2"      => "EU (London)",
            "eu-west-3"      => "EU (Paris)",
            "eu-central-1"   => "EU (Frankfurt)",
            "sa-east-1"      => "South America (São Paulo)",
            "ap-south-1"     => "Asia Pacific (Mumbai)",
            "ap-southeast-1" => "Asia Pacific (Singapore)",
            "ap-southeast-2" => "Asia Pacific (Sydney)",
            "ap-northeast-1" => "Asia Pacific (Tokyo)",
            "ap-northeast-2" => "Asia Pacific (Seoul)",
            "ap-northeast-3" => "Asia Pacific (Osaka-Local)",
        );
    }
    /* server details edit */
    public function ServerDetails($_dsp = '', $entry_id = '', $db_id = '', $server_status = '', $server_name = '', $server_type = '', $server_priority = '', $server_limit = '', $server_filehop = '', $upload_v_file = '', $upload_v_thumb = '', $upload_i_file = '', $upload_i_thumb = '', $upload_a_file = '', $upload_a_thumb = '', $upload_d_file = '', $upload_d_thumb = '', $ftp_transfer = '', $ftp_passive = '', $ftp_host = '', $ftp_port = '', $ftp_user = '', $ftp_pass = '', $ftp_root = '', $base_url = '', $total_video = '', $total_short = '', $total_image = '', $total_audio = '', $total_doc = '', $current_hop = '', $server_last = '', $lighttpd_url = '', $lighttpd_secdownload = '', $lighttpd_prefix = '', $lighttpd_key = '', $s3_bucketname = '', $s3_accesskey = '', $s3_secretkey = '', $s3_region = '', $s3_fileperm = '', $cf_enable = '', $cf_dist = '', $cf_priceclass = '', $surl_enable = '', $surl_expire = '', $keypair_id = '', $keypair_file = '', $upload_s_file = '', $upload_s_thumb = '')
    {
        global $class_filter, $language, $cfg;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $_btn        = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'save-entry-button button-grey search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, '<span>' . ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']) . '</span>'), 'display: inline-block-off;') : null;
        $server_type = ($_GET['do'] == 'add' and $_POST) ? $class_filter->clr_str($_POST['backend_servers_type_' . ((int) $db_id)]) : $server_type;

        $_sel1 = '<select name="backend_servers_transfer_' . ((int) $db_id) . '" class="ad-off backend-select-input wd350">';
        $_sel1 .= '<option' . ($ftp_transfer == 'FTP_AUTOASCII' ? ' selected="selected"' : null) . ' value="FTP_AUTOASCII">' . $language['backend.servers.transfer.auto'] . '</option>';
        $_sel1 .= '<option' . ($ftp_transfer == 'FTP_ASCII' ? ' selected="selected"' : null) . ' value="FTP_ASCII">' . $language['backend.servers.transfer.ascii'] . '</option>';
        $_sel1 .= '<option' . ($ftp_transfer == 'FTP_BINARY' ? ' selected="selected"' : null) . ' value="FTP_BINARY">' . $language['backend.servers.transfer.bin'] . '</option>';
        $_sel1 .= '</select>';
        $_sel2 = '<select name="backend_servers_passive_' . ((int) $db_id) . '" class="ad-off backend-select-input wd350">';
        $_sel2 .= '<option' . ($ftp_passive == 1 ? ' selected="selected"' : null) . ' value="1">' . $language['backend.servers.enabled'] . '</option>';
        $_sel2 .= '<option' . ($ftp_passive == 0 ? ' selected="selected"' : null) . ' value="0">' . $language['backend.servers.disabled'] . '</option>';
        $_sel2 .= '</select>';
        $_sel3 = '<select name="backend_servers_type_' . ((int) $db_id) . '" class="server-type ad-off backend-select-input wd350" rel-id="' . $db_id . '">';
        $_sel3 .= '<option' . ($server_type == 'ftp' ? ' selected="selected"' : null) . ' value="ftp">' . $language['backend.servers.ftp'] . '</option>';
        $_sel3 .= '<option' . ($server_type == 's3' ? ' selected="selected"' : null) . ' value="s3">' . $language['backend.servers.s3'] . '</option>';
        $_sel3 .= '<option' . ($server_type == 'ws' ? ' selected="selected"' : null) . ' value="ws">' . $language['backend.servers.ws'] . '</option>';
        $_sel3 .= '</select>';

        $html .= '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';

        if ($_GET['do'] != 'add') {
            $html .= '<div class="tabs tabs-style-topline">';
            $html .= '<nav><ul class="ul-no-list uactions-list left-float top-bottom-padding bottom-border">';
            $html .= '<li rel-id="' . $db_id . '" class="entry-stats' . (((!$_POST and $_GET['do'] != 'update') or ($_POST and $_GET['do'] == 'update' and $db_id != self::getPostID())) ? ' active' : null) . '"><a class="icon icon-pie" href="javascript:;" onclick="$(\'#entry-stats' . $db_id . '\').show(); $(\'#entry-details' . $db_id . '\').hide();"><span>' . $language['backend.servers.stats'] . '</span></a></li>';
            $html .= '<li rel-id="' . $db_id . '" class="entry-edit' . (($_POST and $_GET['do'] == 'update' and $db_id == self::getPostID()) ? ' active' : null) . '"><a class="icon icon-pencil" href="javascript:;" onclick="$(\'#entry-details' . $db_id . '\').show(); $(\'#entry-stats' . $db_id . '\').hide();"><span>' . $language['backend.servers.details'] . '</span></a></li>';
            $html .= $server_type == 'ftp' ? '<li rel-id="' . $db_id . '" rel="popuprel' . $db_id . '" id="ftp-probe" class="ftp-probe popup"><a class="icon icon-connection" href="javascript:;"><span>' . $language['backend.servers.conn'] . '</span></a></li>' : '<li rel-id="' . $db_id . '" rel="popuprel' . $db_id . '" id="s3-probe" class="s3-probe popup"><a class="icon icon-connection" href="javascript:;"><span>' . $language['backend.servers.s3.conn'] . '</span></a></li>';
            $html .= ($server_type == 's3' and $cf_enable == 1) ? '<li rel-id="' . $db_id . '" rel="popuprel' . $db_id . '" id="cf-test" class="cf-test popup"><a class="icon icon-connection" href="javascript:;"><span>' . $language['backend.cf.test'] . '</span></a></li>' : null;
            $html .= ($server_type == 's3' and $cf_enable == 1) ? '<li rel-id="' . $db_id . '" rel="popuprel' . $db_id . '" id="cf-update" class="cf-update popup"><a class="icon icon-disk" href="javascript:;"><span>' . $language['backend.cf.update'] . '</span></a></li>' : null;
            $html .= '<li rel-id="' . $db_id . '" rel="popuprel' . $db_id . '" id="ftp-list" class="ftp-list popup"><a class="icon icon-list2" href="javascript:;"><span>' . $language['backend.servers.filelist'] . '</span></a></li>';
            $html .= '<li rel-id="' . $db_id . '" rel="popuprel' . $db_id . '" id="ftp-reset" class="ftp-reset popup"><a class="icon icon-switch" href="javascript:;"><span>' . $language['backend.servers.reset'] . '</span></a></li>';
            $html .= '</ul></nav>';
            $html .= '</div>';
        }
        $html .= '<div class="popupbox-be" id="popuprel' . $db_id . '" style="overflow: auto;"></div><div id="fade' . $db_id . '" class="fade"></div>';

        $html .= '<div class="left-float"' . ((($_GET['do'] != 'add' and $_POST['hc_id'] != $db_id) or ($_POST and isset($_GET['do']) and $_GET['do'][0] == 'c' and $_GET['do'][1] == 'b')) ? ' style="display: none;"' : null) . ' id="entry-details' . $db_id . '">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id)));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['frontend.global.estate'] . '</label>' . ($server_status == 1 ? '<span class="conf-green">' . $language['frontend.global.active'] . '</span>' : '<span class="err-red">' . $language['frontend.global.inactive'] . '</span>')));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['frontend.global.name'] . '</label>' . $language['frontend.global.required'], 'left-float', 'frontend_global_name', 'backend-text-input wd350', $server_name);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.servers.priority'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_servers_priority_' . ((int) $db_id), 'backend-text-input wd350', (int) $server_priority);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.servers.limit'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_servers_limit_' . ((int) $db_id), 'backend-text-input wd350', (int) $server_limit);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.servers.hop'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_servers_hop_' . ((int) $db_id), 'backend-text-input wd350', (int) $server_filehop);
        $html .= VGenerate::declareJS('$(document).ready(function(){
        $("#slider-backend_servers_priority_' . ((int) $db_id) . '").noUiSlider({ start: [ ' . (int) $server_priority . ' ], step: 1, range: { \'min\': [ 1 ], \'max\': [ 100 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_servers_priority_' . ((int) $db_id) . '").Link(\'lower\').to($("input[name=\'backend_servers_priority_' . ((int) $db_id) . '\']"));
        $("#slider-backend_servers_limit_' . ((int) $db_id) . '").noUiSlider({ start: [ ' . (int) $server_limit . ' ], step: 1, range: { \'min\': [ 1 ], \'max\': [ 1000000 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_servers_limit_' . ((int) $db_id) . '").Link(\'lower\').to($("input[name=\'backend_servers_limit_' . ((int) $db_id) . '\']"));
        $("#slider-backend_servers_hop_' . ((int) $db_id) . '").noUiSlider({ start: [ ' . (int) $server_filehop . ' ], step: 1, range: { \'min\': [ 1 ], \'max\': [ 1000000 ] }, format:wNumb({ decimals: 0 }) });
                $("#slider-backend_servers_hop_' . ((int) $db_id) . '").Link(\'lower\').to($("input[name=\'backend_servers_hop_' . ((int) $db_id) . '\']"));
                });');

        $html .= '<div class="clearfix">&nbsp;</div>';
        $html .= '<div class="row left-float">' . (VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.servers.content'] . '</label>' . $language['frontend.global.required']));
        if ($cfg['video_module'] == 1) {
            $vhtml = '<div class="left-float wd250 icheck-box">';
            $vhtml .= '<input type="checkbox" value="1" name="backend_servers_ct_video"' . (($upload_v_file == 1 or $upload_v_file == '') ? ' checked="checked"' : null) . ' class="">';
            $vhtml .= '<label>' . $language['backend.servers.content.v'] . '</label></div>';
            $vhtml .= '<div class="left-float wd250 icheck-box">';
            $vhtml .= '<input type="checkbox" value="1" name="backend_servers_ct_vthumb"' . (($upload_v_thumb == 1 or $upload_v_file == '') ? ' checked="checked"' : null) . ' class="">';
            $vhtml .= '<label>' . $language['backend.servers.content.vt'] . '</label>';
            $vhtml .= '</div>';
        }
        if ($cfg['short_module'] == 1) {
            $shtml = '<div class="left-float wd250 icheck-box">';
            $shtml .= '<input type="checkbox" value="1" name="backend_servers_ct_short"' . (($upload_s_file == 1 or $upload_s_file == '') ? ' checked="checked"' : null) . ' class="">';
            $shtml .= '<label>' . $language['backend.servers.content.s'] . '</label></div>';
            $shtml .= '<div class="left-float wd250 icheck-box">';
            $shtml .= '<input type="checkbox" value="1" name="backend_servers_ct_sthumb"' . (($upload_s_thumb == 1 or $upload_s_file == '') ? ' checked="checked"' : null) . ' class="">';
            $shtml .= '<label>' . $language['backend.servers.content.st'] . '</label>';
            $shtml .= '</div>';
        }
        if ($cfg['image_module'] == 1) {
            $ihtml = '<div class="right-float wd250 row no-top-padding icheck-box">';
            $ihtml .= '<input type="checkbox" value="1" name="backend_servers_ct_image"' . ($upload_i_file == 1 ? ' checked="checked"' : null) . ' class="">';
            $ihtml .= '<label>' . $language['backend.servers.content.i'] . '</label></div>';
            $ihtml .= '<div class="left-float wd250 icheck-box">';
            $ihtml .= '<input type="checkbox" value="1" name="backend_servers_ct_ithumb"' . ($upload_i_thumb == 1 ? ' checked="checked"' : null) . ' class="">';
            $ihtml .= '<label>' . $language['backend.servers.content.it'] . '</label>';
            $ihtml .= '</div>';
        }
        if ($cfg['audio_module'] == 1) {
            $ahtml = '<div class="right-float wd250 row no-top-padding icheck-box">';
            $ahtml .= '<input type="checkbox" value="1" name="backend_servers_ct_audio"' . ($upload_a_file == 1 ? ' checked="checked"' : null) . ' class="">';
            $ahtml .= '<label>' . $language['backend.servers.content.a'] . '</label></div>';
            $ahtml .= '<div class="right-float wd250 row no-top-padding icheck-box">';
            $ahtml .= '<input type="checkbox" value="1" name="backend_servers_ct_athumb"' . ($upload_a_thumb == 1 ? ' checked="checked"' : null) . ' class="">';
            $ahtml .= '<label>' . $language['backend.servers.content.at'] . '</label>';
            $ahtml .= '</div>';
        }
        if ($cfg['document_module'] == 1) {
            $dhtml = '<div class="right-float wd250 row no-top-padding icheck-box">';
            $dhtml .= '<input type="checkbox" value="1" name="backend_servers_ct_doc"' . ($upload_d_file == 1 ? ' checked="checked"' : null) . ' class="">';
            $dhtml .= '<label>' . $language['backend.servers.content.d'] . '</label></div>';
            $dhtml .= '<div class="right-float wd250 row no-top-padding icheck-box">';
            $dhtml .= '<input type="checkbox" value="1" name="backend_servers_ct_dthumb"' . ($upload_d_thumb == 1 ? ' checked="checked"' : null) . ' class="">';
            $dhtml .= '<label>' . $language['backend.servers.content.dt'] . '</label>';
            $dhtml .= '</div>';
        }
        $html .= '<div class="vs-column fifths">' . $vhtml . '</div>';
        $html .= '<div class="vs-column fifths">' . $shtml . '</div>';
        $html .= '<div class="vs-column fifths">' . $ihtml . '</div>';
        $html .= '<div class="vs-column fifths">' . $ahtml . '</div>';
        $html .= '<div class="vs-column fifths fit">' . $dhtml . '</div>';
        $html .= '</div>';
        $html .= '<div class="clearfix"></div>';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.servers.type'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel3));
        $html .= VGenerate::declareJS('$(function(){SelectList.init("backend_servers_type_' . ((int) $db_id) . '");});');

        $html .= '<div id="ftp-properties' . $db_id . '" class="' . (($server_type == 's3' or $server_type == 'ws') ? 'no-display' : null) . '">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.servers.transfer'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel1));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.servers.mode'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel2));
        $html .= VGenerate::declareJS('$(function(){SelectList.init("backend_servers_transfer_' . ((int) $db_id) . '");});');
        $html .= VGenerate::declareJS('$(function(){SelectList.init("backend_servers_passive_' . ((int) $db_id) . '");});');
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.servers.host'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_servers_host', 'backend-text-input wd350', $ftp_host);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.servers.port'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_servers_port', 'backend-text-input wd350', $ftp_port);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.servers.user'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_servers_user', 'backend-text-input wd350', $ftp_user);
        $html .= VGenerate::sigleInputEntry('password', 'left-float lh20 wd140', '<label>' . $language['backend.servers.pass'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_servers_pass', 'backend-text-input wd350', $ftp_pass);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.servers.root'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_servers_root', 'backend-text-input wd350', $ftp_root);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.servers.url'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_servers_url', 'backend-text-input wd350', $base_url);
        $html .= '</div>';

        $regarr = self::region();

        $wsregarr = array(
            "us-east-1"    => "US East",
            "us-west-1"    => "US West",
            "eu-central-1" => "EU Central",
        );

        $region = '<select name="backend_s3_region_' . ((int) $db_id) . '" style="display: block;" class="ad-off backend-select-input wd350">';
        foreach ($regarr as $rk => $rn) {
            $region .= '<option value="' . $rk . '"' . ($rk == $s3_region ? ' selected="selected"' : null) . '>' . $rn . '</option>';
        }
        $region .= '</select>';

        $wsregion = '<select name="backend_ws_region_' . ((int) $db_id) . '" style="display: block;" class="ad-off backend-select-input wd350">';
        foreach ($wsregarr as $rk => $rn) {
            $wsregion .= '<option value="' . $rk . '"' . ($rk == $s3_region ? ' selected="selected"' : null) . '>' . $rn . '</option>';
        }
        $wsregion .= '</select>';

        $pclass = array(
            "100" => $language['backend.cf.price.100'],
            "200" => $language['backend.cf.price.200'],
            "All" => $language['backend.cf.price.all'],
        );

        $price_class = '<select name="backend_servers_cf_priceclass_' . ((int) $db_id) . '" style="display: block;" class="ad-off backend-select-input wd250">';
        foreach ($pclass as $rk => $rn) {
            $price_class .= '<option value="' . $rk . '"' . ($rk == $cf_priceclass ? ' selected="selected"' : null) . '>' . $rn . '</option>';
        }
        $price_class .= '</select>';

        $s3perm = '<div class="row no-top-padding left-float icheck-box">';
        $s3perm .= '<input type="radio" value="public-read" name="backend_s3_fileperm"' . (($s3_fileperm == 'public-read' or $s3_fileperm == '') ? ' checked="checked"' : null) . ' class="">';
        $s3perm .= '<label>' . $language['backend.s3.perm.pub'] . '</label><br>';
        $s3perm .= '<input type="radio" value="private" name="backend_s3_fileperm"' . ($s3_fileperm == 'private' ? ' checked="checked"' : null) . ' class="">';
        $s3perm .= '<label>' . $language['backend.s3.perm.priv'] . '</label>';
        $s3perm .= '</div>';

        $wsperm = '<div class="row no-top-padding left-float icheck-box">';
        $wsperm .= '<input type="radio" value="public-read" name="backend_ws_fileperm"' . (($s3_fileperm == 'public-read' or $s3_fileperm == '') ? ' checked="checked"' : null) . ' class="">';
        $wsperm .= '<label>' . $language['backend.s3.perm.pub'] . '</label><br>';
        $wsperm .= '<input type="radio" value="private" name="backend_ws_fileperm"' . ($s3_fileperm == 'private' ? ' checked="checked"' : null) . ' class="">';
        $wsperm .= '<label>' . $language['backend.s3.perm.priv'] . '</label>';
        $wsperm .= '</div>';

        $html .= '<div id="amazons3-properties' . $db_id . '" class="' . (($server_type == 'ftp' or $server_type == 'ws' or ($_GET['do'] == 'add' and !$_POST)) ? 'no-display' : null) . '">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.s3.accesskey'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_s3_accesskey', 'backend-text-input wd350', $s3_accesskey);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.s3.secretkey'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_s3_secretkey', 'backend-text-input wd350', $s3_secretkey);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.s3.bucketname'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_s3_bucketname', 'backend-text-input wd350', $s3_bucketname);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.s3.region'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $region));
        $html .= VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;');
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.s3.perm'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20', '', $s3perm));
        $html .= VGenerate::declareJS('$(function(){SelectList.init("backend_s3_region_' . ((int) $db_id) . '");});');

        $html .= '<div class="row icheck-box">' . (VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;'));
        $html .= '<input type="checkbox" value="1" name="backend_servers_cf_enable"' . ($cf_enable == 1 ? ' checked="checked"' : null) . ' class="" onclick="$(\'#s-cf-opt' . $db_id . '\').toggleClass(\'no-display\'); resizeDelimiter();">';
        $html .= '<label>' . $language['backend.cf.enable'] . '</label>';
        $html .= '</div>';
        $html .= VGenerate::declareJS('$(".icheck-box input").on("ifChanged", function(event){$(this).click();});');

        $html .= '<div id="s-cf-opt' . $db_id . '" class="' . ($cf_enable == 1 ? '' : 'no-display') . '">';

        $html .= '<div class="row no-top-padding icheck-box">';
        $html .= '<input type="radio" value="w" name="backend_servers_cf_dist"' . ($cf_dist == 'w' ? ' checked="checked"' : null) . ' class="">';
        $html .= '<label>' . $language['backend.cf.web'] . '</label>';
        $html .= '</div>';
        $html .= '<div class="row no-top-padding icheck-box">';
        $html .= '<input type="radio" value="r" name="backend_servers_cf_dist"' . ($cf_dist == 'r' ? ' checked="checked"' : null) . ' class="">';
        $html .= '<label>' . $language['backend.cf.rtmp'] . '</label>';
        $html .= '</div>';

        $html .= VGenerate::simpleDivWrap('row', '', (VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;')) . '<label>' . $language['backend.cf.price'] . '</label>' . VGenerate::simpleDivWrap('left-float lh20 selector', '', $price_class));
        $html .= VGenerate::declareJS('$(function(){SelectList.init("backend_servers_cf_priceclass_' . ((int) $db_id) . '");});');
        $html .= '<div class="row icheck-box">' . (VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;'));
        $html .= '<span class="left-float lh20"><input type="checkbox" value="1" name="backend_servers_surl_enable"' . ($surl_enable == 1 ? ' checked="checked"' : null) . ' class="" onclick="$(\'#key-pair-' . $db_id . '\').toggleClass(\'no-display\'); resizeDelimiter();"></span>';
        $html .= '<label>' . $language['backend.cf.surl.enable'] . '</label>';
        $html .= '<input type="text" name="backend_servers_surl_time" value="' . $surl_expire . '" class="backend-text-input wd50" />';
        $html .= '</div>';

        $html .= '<div id="key-pair-' . $db_id . '" class="' . ($surl_enable == 1 ? '' : 'no-display') . '">';
        $html .= '<div class="row">';
        $html .= '<label>' . $language['backend.cf.keypair.id'] . '</label>';
        $html .= '<input type="text" name="backend_servers_keypair_id" value="' . $keypair_id . '" class="backend-text-input wd240" />';
        $html .= '</div>';

        $html .= '<div class="row">';
        $html .= '<label>' . $language['backend.cf.keypair.file'] . '</label>';
        $html .= '<input type="text" name="backend_servers_keypair_file" value="' . $keypair_file . '" class="backend-text-input wd240">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '</div>';

        $html .= '<div id="wasabis3-properties' . $db_id . '" class="' . (($server_type == 'ftp' or $server_type == 's3' or ($_GET['do'] == 'add' and !$_POST)) ? 'no-display' : null) . '">';
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.s3.accesskey'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_ws_accesskey', 'backend-text-input wd350', $s3_accesskey);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.s3.secretkey'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_ws_secretkey', 'backend-text-input wd350', $s3_secretkey);
        $html .= VGenerate::sigleInputEntry('text', 'left-float lh20 wd140', '<label>' . $language['backend.s3.bucketname'] . '</label>' . $language['frontend.global.required'], 'left-float', 'backend_ws_bucketname', 'backend-text-input wd350', $s3_bucketname);
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.s3.region'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $wsregion));
        $html .= VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;');
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.s3.perm'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20', '', $wsperm));
        $html .= VGenerate::declareJS('$(function(){SelectList.init("backend_ws_region_' . ((int) $db_id) . '");});');

        $html .= '<div class="row icheck-box no-display1">' . (VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;'));
        $html .= '<input type="checkbox" value="1" name="backend_servers_ws_cf_enable"' . ($cf_enable == 1 ? ' checked="checked"' : null) . ' class="" onclick="$(\'#ws-s-cf-opt' . $db_id . '\').toggleClass(\'no-display\'); resizeDelimiter();">';
        $html .= '<label>' . $language['backend.cf.enable'] . '</label>';
        $html .= '</div>';

        $html .= '<div id="ws-s-cf-opt' . $db_id . '" class="' . ($cf_enable == 1 ? '' : 'no-display') . '">';

        $html .= '<div class="row no-top-padding icheck-box">';
        $html .= '<input type="radio" value="w" name="backend_servers_ws_cf_dist"' . ($cf_dist == 'w' ? ' checked="checked"' : null) . ' class="">';
        $html .= '<label>' . $language['backend.cf.web'] . '</label>';
        $html .= '</div>';
        $html .= '<div class="row no-top-padding icheck-box">';
        $html .= '<input type="radio" value="r" name="backend_servers_ws_cf_dist"' . ($cf_dist == 'r' ? ' checked="checked"' : null) . ' class="">';
        $html .= '<label>' . $language['backend.cf.rtmp'] . '</label>';
        $html .= '</div>';

        $html .= '<div class="row icheck-box">' . (VGenerate::simpleDivWrap('left-float lh20 wd140', '', '&nbsp;'));
        $html .= '<span class="left-float lh20"><input type="checkbox" value="1" name="backend_servers_ws_surl_enable"' . ($surl_enable == 1 ? ' checked="checked"' : null) . ' class="" onclick="$(\'#ws-key-pair-' . $db_id . '\').toggleClass(\'no-display\'); resizeDelimiter();"></span>';
        $html .= '<label>' . $language['backend.cf.surl.enable'] . '</label>';
        $html .= '<input type="text" name="backend_servers_ws_surl_time" value="' . $surl_expire . '" class="backend-text-input wd50" />';
        $html .= '</div>';

        $html .= '<div id="ws-key-pair-' . $db_id . '" class="' . ($surl_enable == 1 ? '' : 'no-display') . '">';
        $html .= '<div class="row">';
        $html .= '<label>' . $language['backend.cf.keypair.id'] . '</label>';
        $html .= '<input type="text" name="backend_servers_ws_keypair_id" value="' . $keypair_id . '" class="backend-text-input wd240" />';
        $html .= '</div>';

        $html .= '<div class="row">';
        $html .= '<label>' . $language['backend.cf.keypair.file'] . '</label>';
        $html .= '<input type="text" name="backend_servers_ws_keypair_file" value="' . $keypair_file . '" class="backend-text-input wd240">';
        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>';
        $html .= '</div>';

        $html .= '</div>'; //end vs-mask
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20', '', $_btn, 'margin-top: 7px;'));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language['frontend.global.required.items']);
        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</div>';

        if ($_GET['do'] != 'add') {
            $html .= '<div class="left-float left-padding10 top-padding20"' . (($_POST and $_POST['hc_id'] == $db_id and $_GET['do'][0] != 'c' and $_GET['do'][1] != 'b') ? ' style="display: none;"' : null) . ' id="entry-stats' . $db_id . '">';
            $html .= '<div class="vs-column fourths">';
            $html .= '<ul class="entry-details">';
            $html .= '<li><label>' . $language['backend.servers.priority'] . '</label><span class="conf-green">' . $server_priority . '</span></li>';
            $html .= '<li><label>' . $language['backend.servers.limit'] . '</label><span class="conf-green">' . $server_limit . '</span></li>';
            $html .= '<li><label>' . $language['backend.servers.hop'] . '</label><span class="conf-green">' . $server_filehop . '</span></li>';
            $html .= '<li><label>' . $language['backend.servers.hop.c'] . '</label><span class="conf-green">' . $current_hop . '</span></li>';
            $html .= '<li><label>' . $language['backend.servers.total.v'] . '</label><span class="conf-green">' . $total_video . '</span></li>';
            $html .= '<li><label>' . $language['backend.servers.total.s'] . '</label><span class="conf-green">' . $total_short . '</span></li>';
            $html .= '<li><label>' . $language['backend.servers.total.i'] . '</label><span class="conf-green">' . $total_image . '</span></li>';
            $html .= '<li><label>' . $language['backend.servers.total.a'] . '</label><span class="conf-green">' . $total_audio . '</span></li>';
            $html .= '<li><label>' . $language['backend.servers.total.d'] . '</label><span class="conf-green">' . $total_doc . '</span></li>';
            $html .= '<li><label>' . $language['backend.servers.last'] . '</label><span class="conf-green">' . $server_last . '</span></li>';
            $html .= '</ul>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '<div class="clearfix"></div>';
        } else {
            $html .= self::ftpJS();
        }

        $html .= '</form></div>';

        return $html;
    }
    /* pause/resume transfers */
    public function xferPause($state)
    {
        global $cfg, $db, $class_filter;

        $sw   = $state == 'pause' ? 1 : 0;
        $type = $class_filter->clr_str($_GET['t']);

        $res = $db->execute(sprintf("UPDATE `db_settings` SET `cfg_data`='%s' WHERE `cfg_name`='pause_%s_transfer' LIMIT 1;", $sw, $type));

        if ($db->Affected_Rows() > 0) {
            echo VGenerate::declareJS('$(".pause-mode, .resume-mode").parent().toggleClass("no-display");');
        }
    }
    /* add new transfer */
    public function xferAdd()
    {
        global $language, $db, $class_filter, $class_database, $cfg;

        $type = self::xferTypes();

        $_btn = null;

        if ($_POST) {
            $_file = $class_filter->clr_str($_POST["new_" . $type . "_xfer"]);
            if ($_file != '') {
                $sql = sprintf("SELECT A.`file_key`, A.`file_title`, B.`usr_key` FROM `db_%sfiles` A, `db_accountuser` B WHERE A.`file_key`='%s' AND A.`usr_id`=B.`usr_id` LIMIT 1;", $type, $_file);
                $res = $db->execute($sql);

                if ($res->fields['file_key']) {
                    $_xkey = $class_database->singleFieldValue('db_' . $type . 'transfers', 'q_id', 'file_key', $res->fields['file_key']);
                    if ($_xkey != '') {
                        $_xfer = '';
                        echo VGenerate::noticeTpl('', 'Error: Already added to transfer list', '');
                    } else {
                        $_xfer   = $res->fields['file_key'];
                        $_xtitle = $res->fields['file_title'];
                        $_usr    = $res->fields['usr_key'];
                    }
                } else {
                    echo VGenerate::noticeTpl('', 'Error: Invalid title', '');
                }
            } else {
                echo VGenerate::noticeTpl('', 'Error: Invalid title', '');
            }

            $_vs = intval($_POST["new_" . $type . "_server"]);
            $_ts = intval($_POST['new_thumb_server']);

            if ($_xfer != '' and ($_vs > 0 or $_ts > 0)) {
                $_arr = array("upload_server" => $_vs, "thumb_server" => $_ts, "file_key" => $_xfer, "usr_key" => $_usr);
                $_ins = $class_database->doInsert('db_' . $type . 'transfers', $_arr);

                if ($db->Affected_Rows() > 0) {
                    echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
                }
            }
        }

        $_sel0 = '<input type="text" name="new_' . $type . '_xfer_title" value="' . $_xtitle . '">';
        $_sel0 .= '<input type="hidden" name="new_' . $type . '_xfer" value="' . $_xfer . '">';

        $_sel1 = '<select name="new_' . $type . '_server" class="backend-select-input wd350">';
        $_sel1 .= '<option value="">---</option>';
        $vs = $db->execute(sprintf("SELECT `server_id`, `server_type`, `server_name`, `ftp_host`, `s3_bucketname` FROM `db_servers` WHERE `status`='1' AND `upload_%s_file`='1' AND `total_%s`<`server_limit`;", $type[0], $type));
        if ($vs->fields['server_id']) {
            while (!$vs->EOF) {
                $_sel = $_POST["new_" . $type . "_server"] == $vs->fields['server_id'] ? ' selected="selected"' : null;
                $_sel1 .= '<option' . $_sel . ' value="' . $vs->fields['server_id'] . '">' . $vs->fields['server_name'] . ' (' . (($vs->fields['server_type'] == 's3' or $vs->fields['server_type'] == 'ws') ? $vs->fields['s3_bucketname'] : $vs->fields['ftp_host']) . ')</option>';
                $vs->MoveNext();
            }
        }
        $_sel1 .= '</select>';

        $_sel2 = '<select name="new_thumb_server" class="backend-select-input wd350">';
        $_sel2 .= '<option value="">---</option>';
        $vs = $db->execute(sprintf("SELECT `server_id`, `server_type`, `server_name`, `ftp_host`, `s3_bucketname` FROM `db_servers` WHERE `status`='1' AND `upload_%s_thumb`='1' AND `total_%s`<`server_limit`;", $type[0], $type));
        if ($vs->fields['server_id']) {
            while (!$vs->EOF) {
                $_sel = $_POST['new_thumb_server'] == $vs->fields['server_id'] ? ' selected="selected"' : null;
                $_sel2 .= '<option' . $_sel . ' value="' . $vs->fields['server_id'] . '">' . $vs->fields['server_name'] . ' (' . (($vs->fields['server_type'] == 's3' or $vs->fields['server_type'] == 'ws') ? $vs->fields['s3_bucketname'] : $vs->fields['ftp_host']) . ')</option>';
                $vs->MoveNext();
            }
        }
        $_sel2 .= '</select>';

        $html = '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="categ-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language["backend.xfer.new." . $type] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel0));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language["backend.xfer.new." . $type[0] . "up"] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel1));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140', '', '<label>' . $language['backend.xfer.new.thumb'] . '</label>' . $language['frontend.global.required']) . VGenerate::simpleDivWrap('left-float lh20 selector', '', $_sel2));
        $html .= VGenerate::simpleDivWrap('row right-align no-top-padding right-padding20', '', $language['frontend.global.required.items']);
        $html .= '</form></div>';

        $html .= VGenerate::declareJS('$(function(){SelectList.init("new_' . $type . '_xfer"); SelectList.init("new_' . $type . '_server"); SelectList.init("new_thumb_server");});');

        return $html;
    }
    /* get type for transfers */
    public function xferTypes()
    {
        global $class_filter;

        $for = $class_filter->clr_str($_GET['s']);

        switch ($for) {
            case "backend-menu-entry3-sub14":
                $type = 'video';
                break;
            case "backend-menu-entry3-sub19":
                $type = 'short';
                break;
            case "backend-menu-entry3-sub15":
                $type = 'image';
                break;
            case "backend-menu-entry3-sub16":
                $type = 'audio';
                break;
            case "backend-menu-entry3-sub17":
                $type = 'doc';
                break;
            default:
                $type = 'video';
                break;
        }

        return $type;
    }
    /* video transfer details */
    public function xferDetails($_dsp = '', $file_key = '', $entry_id = '', $db_id = '', $state = '', $upload_server = '', $thumb_server = '', $upload_start_time = '', $upload_end_time = '', $thumb_start_time = '', $thumb_end_time = '')
    {
        global $class_filter, $language, $db;

        $_init = VbeEntries::entryInit($_dsp, $db_id, $entry_id);
        $_dsp  = $_init[0];
        $_btn  = $_init[1];

        $_btn = $_GET['do'] != 'add' ? VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'search-button form-button ' . ($_GET['do'] == 'add' ? 'new-entry' : 'update-entry'), '', $entry_id, ($_GET['do'] == 'add' ? $language['frontend.global.savenew'] : $language['frontend.global.saveupdate']))) : null;

        $u   = $db->execute(sprintf("SELECT `server_type`, `server_name`, `ftp_host`, `s3_bucketname` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $upload_server));
        $u1  = $u->fields['server_name'];
        $u2  = $u->fields['ftp_host'];
        $sh1 = $u->fields['server_type'];
        $sn1 = $u->fields['s3_bucketname'];
        $t   = $db->execute(sprintf("SELECT `server_type`, `server_name`, `ftp_host`, `s3_bucketname` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $thumb_server));
        $t1  = $t->fields['server_name'];
        $t2  = $t->fields['ftp_host'];
        $sh2 = $t->fields['server_type'];
        $sn2 = $t->fields['s3_bucketname'];

        $type = self::xferTypes();

        $log      = $cfg['logging_dir'] . '/xfer/' . date("Y.m.d") . '/' . $type . '_' . $file_key . '.log';
        $log_file = $type . '_' . $file_key . '.log';
        $log_ht   = '<a href="javascript:;" rel-id="' . $file_key . '" rel="popuprel' . $file_key . '" class="xfer-log popup" id="xfer-log">' . $log_file . '</a>';

        $html = '<div class="ct-entry-details wdmax left-float bottom-padding10" id="' . $entry_id . '-' . $db_id . '" style="display: ' . $_dsp . ';"><form id="xfer-entry-form' . $db_id . '" method="post" action="" class="entry-form-class">';

        $html .= '<div class="left-float left-padding25">';
        $html .= VGenerate::simpleDivWrap('left-float lh20 wd140', '', VGenerate::entryHiddenInput($db_id));
        $html .= VGenerate::simpleDivWrap('row', '', VGenerate::simpleDivWrap('left-float lh20 wd140 act place-left', '', '<label>' . $language['backend.servers.d.state'] . '</label><span class="conf-green">' . $language["backend.xfer.state." . $state] . '</span>'));
        $html .= '<div class="clearfix"></div>';
        $html .= '<div class="vs-mask">';
        $html .= '<label>' . $language['backend.servers.d.upload.server'] . '</label><span class="conf-green">' . $u1 . ' (' . (($sh1 == 's3' or $sh1 == 'ws') ? $sn1 : $u2) . ')</span><br>';
        $html .= '<label>' . $language['backend.servers.d.thumb.server'] . '</label><span class="conf-green">' . $t1 . ' (' . (($sh2 == 's3' or $sh2 == 'ws') ? $sn2 : $t2) . ')</span><br>';
        $html .= '<label>' . $language["backend.servers.d." . $type[0] . "st"] . '</label><span class="conf-green">' . $upload_start_time . '</span><br>';
        $html .= '<label>' . $language["backend.servers.d." . $type[0] . "et"] . '</label><span class="conf-green">' . $upload_end_time . '</span><br>';
        $html .= '<label>' . $language['backend.servers.d.tst'] . '</label><span class="conf-green">' . $thumb_start_time . '</span><br>';
        $html .= '<label>' . $language['backend.servers.d.tet'] . '</label><span class="conf-green">' . $thumb_end_time . '</span><br>';
        $html .= '<label>' . $language['backend.servers.d.log'] . '</label><span class="conf-green">' . $log_ht . '</span>';
        $html .= '</div>'; //end vs-mask
        $html .= '</div>';

        $html .= '<input type="hidden" name="section_entry_value" value="' . $db_id . '" />';
        $html .= '<input type="hidden" name="section_entry_value" value="' . $entry_id . '-entry-del' . $db_id . '" />';
        $html .= '</form></div>';
        $html .= '<div class="popupbox-be" id="popuprel' . $file_key . '" style="overflow: auto;"></div><div id="fade' . $file_key . '" class="fade"></div>';

        return $html;
    }
    /* show transfer log file */
    public function xferLog()
    {
        global $class_filter, $cfg, $language, $smarty;

        $type     = self::xferTypes();
        $file_key = $class_filter->clr_str($_GET['i']);

        $log_path = $cfg['logging_dir'] . '/log_xfer/*/.' . $type . '_' . $file_key . '.log';

        $html = '';
        foreach (glob($log_path) as $filename) {
            if (file_exists($filename)) {
                $html .= "<span>$filename size " . filesize($filename) . "</span><br /><br />";
            }
        }

        if (file_exists($filename)) {
            $html .= nl2br(file_get_contents($filename));
        } else {
            $html .= 'No log yet!';
        }
        return $html;
    }
    /* entry js */
    public function ftpJS()
    {
        $js = '$("li.popup, a.popup").click(function(){';
        $js .= 'var popupid = $(this).attr("rel");';
        $js .= 'var userid = $(this).attr("rel-id");';
        $js .= 'var lid = $(this).attr("id");';
        $js .= '$.fancybox.open({ wrapCSS: (lid == "cf-update" ? "user-permissions" : ""), type: "ajax", minWidth: "70%", margin: 10, href: current_url + menu_section + "?s="+$(".menu-panel-entry-sub-active").attr("id")+"&do="+lid+"&i="+userid });';
        $js .= '});';
        /* change server type */
        $js .= '$(".server-type").change(function(){';
        $js .= 'var t = $(this); var v = t.val(); var sid = t.attr("rel-id");';
        $js .= 'if(v=="ftp"){$("#ftp-properties"+sid).removeClass("no-display");$("#amazons3-properties"+sid).addClass("no-display");$("#wasabis3-properties"+sid).addClass("no-display");}';
        $js .= 'else if(v=="s3"){$("#ftp-properties"+sid).addClass("no-display");$("#amazons3-properties"+sid).removeClass("no-display");$("#wasabis3-properties"+sid).addClass("no-display");}';
        $js .= 'else if(v=="ws"){$("#ftp-properties"+sid).addClass("no-display");$("#amazons3-properties"+sid).addClass("no-display");$("#wasabis3-properties"+sid).removeClass("no-display");}';
        $js .= '});';

        echo VGenerate::declareJS('$(document).ready(function(){' . $js . '});');
    }
    /* list server videos files and thumbnails */
    public function serverList($server_id, $type, $tbl = 'video')
    {
        global $db, $language, $cfg;

        $for = $tbl[0];

        $sql = sprintf("SELECT
                    A.`file_key`,
                    A.`file_title`,
                    C.`server_type`, C.`url`, C.`lighttpd_url`, C.`s3_bucketname`, C.`cf_enabled`, C.`cf_dist_type`, C.`cf_dist_domain`, C.`cf_signed_expire`, C.`cf_signed_url`, C.`cf_key_pair`, C.`cf_key_file`, C.`s3_accesskey`, C.`s3_secretkey`,
                    D.`usr_key`
                    FROM
                    `db_%sfiles` A, `db_servers` C, `db_accountuser` D
                    WHERE
                    A.`%s_server`='%s' AND
                    C.`server_id`='%s' AND
                    D.`usr_id`=A.`usr_id`
                    ORDER BY A.`upload_date` DESC;", $tbl, $type, $server_id, $server_id);

        $rs = $db->execute($sql);

        switch ($type) {
            case "upload":
                $t1 = $language["backend.xfer.file." . $for];
                $t2 = $language["backend.xfer.file." . $for . ".no"];
                $i1 = 'video_files[]';
                break;
            case "thumb":
                $t1 = $language["backend.xfer.file.t." . $for];
                $t2 = $language["backend.xfer.file.t." . $for . ".no"];
                $i1 = 'thumb_files[]';
                break;
        }

        $html = '
                <div class="blue-inner categories-container">
                    <aside>
                        <nav>
                            <ul class="accordion" id="' . $tbl . '-' . $type . '-accordion">
                                <li class="dcjq-parent-li categories-menu-title"><a href="javascript:;" class="selected dcjq-parent active"><i class="icon-list"></i>' . $t1 . '</a></li>
                                #LI_LOOP#
                            </ul>
                        </nav>
                    </aside>
                </div>
                <script type="text/javascript">
                    jQuery(function () {
    jQuery("#' . $tbl . '-' . $type . '-accordion").dcAccordion({
        eventType: "click",
        autoClose: true,
        autoExpand: true,
        classExpand: "dcjq-current-parent",
        saveState: false,
        disableLink: false,
        showCount: false,
        menuClose: true,
        speed: 200,
        cookie: "' . $tbl . '-' . $type . '-menu-cookie"
    });
});
                </script>';

        if ($rs->fields['file_key']) {
            while (!$rs->EOF) {
                $file_key  = $rs->fields['file_key'];
                $usr_key   = $rs->fields['usr_key'];
                $file_path = array();

                if ($type == 'upload') {
                    $gs = md5($cfg['global_salt_key'] . $file_key);

                    switch ($for) {
                        case "v":
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.360p.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.360p.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.480p.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.480p.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.720p.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.720p.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.1080p.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.1080p.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.mob.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.mob.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.360p.webm';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.480p.webm';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.720p.webm';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.1080p.webm';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.360p.ogv';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.480p.ogv';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.720p.ogv';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.1080p.ogv';
                            break;
                        case "s":
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.short.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.short.mp4';
                            break;
                        case "i":
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.jpg';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.jpg';
                            break;
                        case "a":
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.mp3';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.mp3';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.mp4';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.mp4';
                            break;
                        case "d":
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $file_key . '.pdf';
                            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $for . '/' . $gs . '.pdf';
                            break;
                    }
                } elseif ($type == 'thumb') {
                    for ($i = 0; $i <= $cfg['thumbs_nr']; $i++) {
                        $tcache = VFiles::thumb_cache($type, $file_key);
                        $fp     = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type[0] . '/' . $file_key . '/' . $i . $tcache . '.jpg';
                        if (is_file($fp)) {
                            $file_path[] = $fp;
                        }
                    }
                }

                $sub_menu = '<ul class="inner-menu">';
                foreach ($file_path as $file) {
                    $file_arr  = explode("/", $file);
                    $file_name = $file_arr[count($file_arr) - 1];
                    $url       = VGenerate::fileURL($tbl, $file_key, $type);

                    $file_url = $url . '/' . $rs->fields['usr_key'] . '/' . ($type[0] == 'u' ? $for : 't' . '/' . $file_key) . '/' . $file_name;

                    if (($rs->fields['server_type'] == 's3' or $rs->fields['server_type'] == 'ws') and $rs->fields['cf_enabled'] == 1 and $rs->fields['cf_signed_url'] == 1) {
                        if ($rs->fields['cf_dist_type'] == 'r') {
                            $file_url = strstr($file_url, $rs->fields['usr_key']);

                            $file_url = VbeServers::getS3SignedURL($rs->fields['s3_accesskey'], $rs->fields['s3_secretkey'], $file_url, $rs->fields['s3_bucketname'], $rs->fields['cf_signed_expire'], $rs->fields['server_type']);
                        } else {
                            $file_url = self::getSignedURL($file_url, $rs->fields['cf_signed_expire'], $rs->fields['cf_key_pair'], $rs->fields['cf_key_file']);
                        }
                    }

                    $sub_menu .= file_exists($file) ? '<li><a target="_blank" href="' . $file_url . '">' . $file_name . '</a></li>' : null;
                }
                $sub_menu .= '</ul>';

                $li_loop .= '<li><a href="javascript:;">' . $rs->fields['file_title'] . '</a>' . $sub_menu . '</li>';

                $rs->MoveNext();
            }
        } else {
            $li_loop = null;
            $li_loop = '<li><a href="javascript:;" class="no-files">' . $t2 . '</a></li>';
        }

        return str_replace('#LI_LOOP#', $li_loop, $html);
    }
    /* reset the file counts */
    public function ftpResetCount()
    {
        global $class_filter, $language, $db, $cfg;

        $server_id   = intval($_GET['i']);
        $reset_hop   = intval($_POST['server_reset_hop']);
        $reset_total = intval($_POST['server_reset_total']);
        $reset_date  = intval($_POST['server_reset_date']);
        $a           = 0;

        if ($_POST) {
            if ($reset_hop == 1) {
                $db->execute(sprintf("UPDATE `db_servers` SET `current_hop`='0' WHERE `server_id`='%s' LIMIT 1;", $server_id));
                if ($db->Affected_Rows() > 0) {
                    $a += 1;
                }
            }
            if ($reset_total == 1) {
                $db->execute(sprintf("UPDATE `db_servers` SET `total_video`='0', `total_short`='0', `total_image`='0', `total_audio`='0', `total_doc`='0' WHERE `server_id`='%s' LIMIT 1;", $server_id));
                if ($db->Affected_Rows() > 0) {
                    $a += 1;
                }
            }
            if ($reset_date == 1) {
                $db->execute(sprintf("UPDATE `db_servers` SET `last_used`='0000-00-00 00:00:00' WHERE `server_id`='%s' LIMIT 1;", $server_id));
                if ($db->Affected_Rows() > 0) {
                    $a += 1;
                }
            }

            if ($a > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            }
        }

        return;
    }
    /* reset file counts layout */
    public function ftpReset()
    {
        global $class_filter, $language, $db;

        $server_id = intval($_GET['i']);

        $html = '<div class="row left-float left-padding20 icheck-box" id="count-reset">';
        $html .= '<form id="server-count-form' . $server_id . '" method="post" action="" class="entry-form-class">';
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="checkbox" name="server_reset_hop" value="1" /><label>' . $language['backend.servers.reset.c1'] . '</label>');
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="checkbox" name="server_reset_total" value="1" /><label>' . $language['backend.servers.reset.c2'] . '</label>');
        $html .= VGenerate::simpleDivWrap('row', '', '<input type="checkbox" name="server_reset_date" value="1" /><label>' . $language['backend.servers.reset.c3'] . '</label>');
        $html .= '</form>';
        $html .= '</div><br>';
        $html .= '<div class="row">';
        $html .= VGenerate::simpleDivWrap('right-float left-padding10 no-display', '', '[<a href="javascript:;" class="popup-reset' . $server_id . ' right-align top-padding7">' . $language['backend.servers.reset.sel'] . '</a>]');
        $html .= VGenerate::simpleDivWrap('left-float', '', VGenerate::basicInput('button', 'save_changes', 'button-grey search-button form-button save-entry-button reset-trigger', '', $server_id, '<span>' . $language['backend.servers.reset.sel'] . '</span>') . VGenerate::lb_Cancel(), 'display: inline-block-off;');
        $html .= '</div>';

        $js .= '$("a.popup-reset' . $server_id . '").click(function(){';
        $js .= '$(".fancybox-wrap").mask("");';
        /* update counts */
        $js .= '$.post(current_url + menu_section + "?s="+$(".menu-panel-entry-sub-active").attr("id")+"&do=reset-count&i=' . $server_id . '", $("#server-count-form' . $server_id . '").serialize(),';
        $js .= 'function(data) {';
        $js .= '$("#reset-request' . $server_id . '").html(data);';
        $js .= '$(".fancybox-wrap").unmask();';
        $js .= '});';
        $js .= 'return false;';
        $js .= '});';
        $js .= '$(".reset-trigger").click(function(){ $(this).parent().prev().find("a").trigger("click");});';
        $js .= '$("#count-reset.icheck-box input").each(function () {
                        var self = $(this);
                        self.iCheck({
                                checkboxClass: "icheckbox_square-blue",
                                radioClass: "iradio_square-blue",
                                increaseArea: "20%"
                                //insert: "<div class=\"icheck_line-icon\"></div><label>" + label_text + "</label>"
                        }); });';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* ftp list of uploaded files */
    public function ftpList()
    {
        global $class_filter, $language, $db;

        $server_id = intval($_GET['i']);

        $html .= '<div class="tabs tabs-style-topline tabs-filelist">';
        $html .= '<nav>';
        $html .= '<ul class="ul-no-list uactions-list left-float top-bottom-padding bottom-border">';
        $html .= '<li class="sort-files active" id="sort-video"><a class="icon icon-video" href="javascript:;"><span>Videos</span></a></li>';
        $html .= '<li class="sort-files" id="sort-short"><a class="icon icon-mobile" href="javascript:;"><span>Shorts</span></a></li>';
        $html .= '<li class="sort-files" id="sort-image"><a class="icon icon-image" href="javascript:;"><span>Pictures</span></a></li>';
        $html .= '<li class="sort-files" id="sort-audio"><a class="icon icon-headphones" href="javascript:;"><span>Music</span></a></li>';
        $html .= '<li class="sort-files" id="sort-doc"><a class="icon icon-file" href="javascript:;"><span>Documents</span></a></li>';
        $html .= '</ul>';
        $html .= '</nav>';
        $html .= '</div>';

        $html .= '<div class="sort-wrap" id="sort-video-wrap">';
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'upload', 'video'));
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'thumb', 'video'));
        $html .= '</div>';
        $html .= '<div class="sort-wrap" id="sort-short-wrap" style="display: none;">';
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'upload', 'short'));
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'thumb', 'short'));
        $html .= '</div>';
        $html .= '<div class="sort-wrap" id="sort-image-wrap" style="display: none;">';
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'upload', 'image'));
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'thumb', 'image'));
        $html .= '</div>';
        $html .= '<div class="sort-wrap" id="sort-audio-wrap" style="display: none;">';
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'upload', 'audio'));
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'thumb', 'audio'));
        $html .= '</div>';
        $html .= '<div class="sort-wrap" id="sort-doc-wrap" style="display: none;">';
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'upload', 'doc'));
        $html .= VGenerate::simpleDivWrap('left-float wd300 left-padding10', '', self::serverList($server_id, 'thumb', 'doc'));
        $html .= '</div>';

        /* close popup */
        $js .= '$(".sort-files").click(function(){';
        $js .= '$(".sort-wrap").hide(); $("#" + $(this).attr("id") + "-wrap").show();';
        $js .= '});';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* ftp delete files */
    public function remoteDelete($file_key, $type = 'video')
    {
        global $db, $class_database, $cfg;

        $rs = $db->execute(sprintf("SELECT
                        A.`upload_server`, A.`thumb_server`,
                        B.`usr_key`
                        FROM
                        `db_%sfiles` A, `db_accountuser` B
                        WHERE
                        A.`file_key`='%s' AND
                        A.`usr_id`=B.`usr_id`
                        LIMIT 1;", $type, $file_key));

        $up_server       = $rs->fields['upload_server'];
        $tmb_server      = $rs->fields['thumb_server'];
        $usr_key         = $rs->fields['usr_key'];
        $up_server_type  = $class_database->singleFieldValue('db_servers', 'server_type', 'server_id', $up_server);
        $tmb_server_type = $class_database->singleFieldValue('db_servers', 'server_type', 'server_id', $tmb_server);

        if ($up_server > 0) {
            self::remoteDeleteEntry($up_server_type, $up_server, $file_key, $usr_key, $type[0]);
        }
        if ($tmb_server > 0) {
            self::remoteDeleteEntry($tmb_server_type, $tmb_server, $file_key, $usr_key, 't');
        }

        if ($up_server == $tmb_server) {
            $db->execute(sprintf("UPDATE `db_servers` SET `total_%s`=`total_%s`-1 WHERE `server_id`='%s' LIMIT 1;", $type, $type, $up_server));

            $rs = $db->execute(sprintf("SELECT `current_hop` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $up_server));
            if ($rs->fields['current_hop'] > 0) {
                $db->execute(sprintf("UPDATE `db_servers` SET `current_hop`=`current_hop`-1 WHERE `server_id`='%s' LIMIT 1;", $up_server));
            }
        } else {
            $db->execute(sprintf("UPDATE `db_servers` SET `total_%s`=`total_%s`-1 WHERE `server_id`='%s' LIMIT 1;", $type, $type, $up_server));
            $rs = $db->execute(sprintf("SELECT `current_hop` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $up_server));
            if ($rs->fields['current_hop'] > 0) {
                $db->execute(sprintf("UPDATE `db_servers` SET `current_hop`=`current_hop`-1 WHERE `server_id`='%s' LIMIT 1;", $up_server));
            }

            $db->execute(sprintf("UPDATE `db_servers` SET `total_%s`=`total_%s`-1 WHERE `server_id`='%s' LIMIT 1;", $type, $type, $tmb_server));
            $rs = $db->execute(sprintf("SELECT `current_hop` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $tmb_server));
            if ($rs->fields['current_hop'] > 0) {
                $db->execute(sprintf("UPDATE `db_servers` SET `current_hop`=`current_hop`-1 WHERE `server_id`='%s' LIMIT 1;", $tmb_server));
            }
        }
    }
    /* delete the file from ftp */
    public function remoteDeleteEntry($server_type, $sid, $file_key, $usr_key, $type)
    {
        global $language, $db, $cfg;

        $file_path = array();

        $gs = md5($cfg['global_salt_key'] . $file_key);
        if ($type == 'v') {
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.360p.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.360p.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.480p.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.480p.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.720p.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.720p.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.1080p.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.1080p.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.mob.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.mob.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.360p.webm';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.480p.webm';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.720p.webm';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.1080p.webm';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.360p.ogv';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.480p.ogv';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.720p.ogv';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.1080p.ogv';
        } elseif ($type == 's') {
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.short.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.short.mp4';
        } elseif ($type == 'i') {
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.jpg';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.jpg';
        } elseif ($type == 'a') {
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.mp3';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.mp3';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.mp4';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.mp4';
        } elseif ($type == 'd') {
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '.pdf';
            $file_path[] = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $gs . '.pdf';
        } elseif ($type == 't') {
            for ($i = 0; $i <= $cfg['thumbs_nr']; $i++) {
                $tcache = VFiles::thumb_cache($type, $file_key);
                $fp     = $cfg['media_files_dir'] . '/' . $usr_key . '/' . $type . '/' . $file_key . '/' . $i . $tcache . '.jpg';
                if (is_file($fp)) {
                    $file_path[] = $fp;
                }
            }
        }

        if ($server_type == 's3' or $server_type == 'ws') {
            $key = array();
            $rs  = $db->execute(sprintf("SELECT `s3_accesskey`, `s3_secretkey`, `s3_bucketname`, `s3_region` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $sid));

            $awsAccessKey = $rs->fields['s3_accesskey'];
            $awsSecretKey = $rs->fields['s3_secretkey'];
            $bucketName   = $rs->fields['s3_bucketname'];
            $region       = $rs->fields['s3_region'];

            if ($server_type == 's3') {
                $ws = array();
            } else {
                $ws = array(
                    // 'endpoint' => 'http://s3.wasabisys.com',
                    // 'profile' => 'wasabi',
                    'region'  => 'us-east-1',
                    'version' => 'wasabi',
                );
            }

            $s3 = new S3Client([
                'version'     => 'latest',
                'region'      => $region,
                'credentials' => [
                    'key'    => $awsAccessKey,
                    'secret' => $awsSecretKey,
                ],
            ]);

            try {
                foreach ($file_path as $file) {
                    $prefix = $s3->encodeKey(str_replace($cfg['media_files_dir'] . '/', '', $file));

                    if (file_exists($file)) {
                        $r = $s3->deleteObject(array(
                            'Bucket' => $bucketName,
                            'Key'    => $prefix,
                        ));
                    }
                }

                if ($type == 't') {
                    $prefix = $s3->encodeKey($usr_key . '/' . $type . '/' . $file_key);
                    $r      = $s3->deleteObject(array(
                        'Bucket' => $bucketName,
                        'Key'    => $prefix,
                    ));
                }
                return true;
            } catch (Exception $e) {
                echo "Error: " . $e->getMessage() . "\n";
                echo "awsAccessKey: " . $awsAccessKey . "\n";
                echo "awsSecretKey: " . $awsSecretKey . "\n";
            }
        }

        $rs       = $db->execute(sprintf("SELECT `ftp_host`, `ftp_port`, `ftp_transfer`, `ftp_passive`, `ftp_username`, `ftp_password`, `ftp_root` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $sid));
        $ftp_host = $rs->fields['ftp_host'];
        $ftp_port = intval($rs->fields['ftp_port']);
        $ftp_xfer = $rs->fields['ftp_transfer'];
        $ftp_pssv = $rs->fields['ftp_passive'];
        $ftp_user = $rs->fields['ftp_username'];
        $ftp_pass = $rs->fields['ftp_password'];
        $ftp_root = $rs->fields['ftp_root'];

        ob_start();
        $ftp            = new ftp(true);
        $ftp->Verbose   = false;
        $ftp->LocalEcho = false;

        if (!$ftp->SetServer($ftp_host, $ftp_port)) {
            $ftp->quit();
        }
        if (!$ftp->connect()) {
            $ftp->quit();
        }
        if (!$ftp->login($ftp_user, $ftp_pass)) {
            $ftp->quit();
        }
        if ($ftp) {
            foreach ($file_path as $file) {
                $remote_file = str_replace($cfg['media_files_dir'], $ftp_root, $file);

                if (file_exists($file)) {
                    $ftp->delete($remote_file);
                }
            }
            if ($type == 't') {
                $ftp->rmdir($ftp_root . '/' . $usr_key . '/' . $type . '/' . $file_key . '/');
            }
            $i = ob_get_contents();
            ob_end_clean();
            return true;
        }
        $i = ob_get_contents();
        ob_end_clean();
        return false;
    }
    /* CloudFront create rtmp streaming distribution */
    public function CF_createRTMPDistro($client, $originId, $bucketName, $server_id, $signed_url = 0, $server_name = 'n/a', $priceClass = 'All')
    {
        global $db, $language;

        $result = $client->createStreamingDistribution(array(
            // CallerReference is required
            'CallerReference' => 'rtmp-dist' . $server_id,
            // S3Origin is required
            'S3Origin'        => array(
                // DomainName is required
                'DomainName'           => $bucketName . '.s3.amazonaws.com',
                // OriginAccessIdentity is required
                'OriginAccessIdentity' => 'origin-access-identity/cloudfront/' . $originId,
            ),
            // Aliases is required
            'Aliases'         => array(
                // Quantity is required
                'Quantity' => 0,
//            'Items' => array('string', ... ),
            ),
            // Comment is required
            'Comment'         => $language['backend.cf.dist.for'] . $server_name,
            // Logging is required
            'Logging'         => array(
                // Enabled is required
                'Enabled' => false,
                // Bucket is required
                'Bucket'  => $bucketName,
                // Prefix is required
                'Prefix'  => 'RTMP-logs/',
            ),
            // TrustedSigners is required
            'TrustedSigners'  => array(
                // Enabled is required
                'Enabled'  => ($signed_url == 0 ? false : true),
                // Quantity is required
                'Quantity' => $signed_url,
                'Items'    => ($signed_url == 0 ? '' : array('self')),
            ),
            // PriceClass is required
            'PriceClass'      => 'PriceClass_' . $priceClass,
            // Enabled is required
            'Enabled'         => true,
        ));

        $id       = $result['Id'];
        $status   = $result['Status'];
        $domain   = $result['DomainName'];
        $location = $result['Location'];

        if ($id) {
            $db->execute(sprintf("UPDATE `db_servers` SET `cf_dist_id`='%s', `cf_dist_status`='%s', `cf_dist_domain`='%s', `cf_dist_uri`='%s' WHERE `server_id`='%s' LIMIT 1;", $id, $status, $domain, $location, $server_id));
            echo $language['notif.success.request'] . "<br /><br />";
            echo $language['backend.cf.dist.id'] . $id . "<br /><br />";
            echo $language['backend.cf.dist.status'] . $status . "<br /><br />";
            echo $language['backend.cf.dist.domain'] . $domain . "<br /><br />";
            echo $language['backend.cf.dist.uri'] . $location . "<br /><br />";
        } else {
            echo $language['notif.error.invalid.request'] . "<br />";
        }

    }
    /* CloudFront update RTMP distribution */
    public function CF_updateRTMPDistro($client, $originId, $bucketName, $server_id, $distId, $signed_url = 0, $server_name = 'n/a', $priceClass = 'All')
    {
        global $db, $language;
        $et = $client->getStreamingDistributionConfig(array(
            // Id is required
            'Id' => $distId,
        ));

        $etag = $et['ETag'];

        $result = $client->updateStreamingDistribution(array(
            // CallerReference is required
            'CallerReference' => 'rtmp-dist' . $server_id,
            // S3Origin is required
            'S3Origin'        => array(
                // DomainName is required
                'DomainName'           => "{$bucketName}.s3.amazonaws.com",
                // OriginAccessIdentity is required
                'OriginAccessIdentity' => 'origin-access-identity/cloudfront/' . $originId,
            ),
            // Aliases is required
            'Aliases'         => array(
                // Quantity is required
                'Quantity' => 0,
                'Items'    => array(),
            ),
            // Comment is required
            'Comment'         => $language['backend.cf.dist.for'] . $server_name,
            // Logging is required
            'Logging'         => array(
                // Enabled is required
                'Enabled' => false,
                // Bucket is required
                'Bucket'  => $bucketName,
                // Prefix is required
                'Prefix'  => 'RTMP-logs/',
            ),
            // TrustedSigners is required
            'TrustedSigners'  => array(
                // Enabled is required
                'Enabled'  => ($signed_url == 0 ? false : true),
                // Quantity is required
                'Quantity' => $signed_url,
                'Items'    => ($signed_url == 0 ? array() : array('self')),
            ),

            // PriceClass is required
            'PriceClass'      => 'PriceClass_' . $priceClass,
            // Enabled is required
            'Enabled'         => true,
            // Id is required
            'Id'              => $distId,
            'IfMatch'         => $etag,
        ));

        $id = $result['Id'];

        if ($id) {
            echo VGenerate::declareJS('$(document).ready(function(){$("#lb-wrapper .responsive-accordion-panel.active").prepend("Success");});');
        } else {
            echo VGenerate::declareJS('$(document).ready(function(){$("#lb-wrapper .responsive-accordion-panel.active").prepend("Failed");});');
        }

    }
    /* CloudFront update web distribution */
    public function CF_updateDistro($client, $originId, $bucketName, $server_id, $distId, $signed_url = 0, $server_name = 'n/a', $priceClass = 'All')
    {
        global $db, $language;

        $et = $client->getDistributionConfig(array(
            // Id is required
            'Id' => $distId,
        ));

        $etag = $et['ETag'];

        $result = $client->updateDistribution(array(
            // CallerReference is required
            'CallerReference'      => $bucketName . '-distribution' . $server_id,
            // Aliases is required
            'Aliases'              => array(
                // Quantity is required
                'Quantity' => 0,
                'Items'    => array(),
            ),
            // DefaultRootObject is required
            'DefaultRootObject'    => '',
            // Origins is required
            'Origins'              => array(
                // Quantity is required
                'Quantity' => 1,
                'Items'    => array(
                    array(
                        // Id is required
                        'Id'             => $originId,
                        // DomainName is required
                        'DomainName'     => "{$bucketName}.s3.amazonaws.com",
                        'S3OriginConfig' => array(
                            // OriginAccessIdentity is required
                            'OriginAccessIdentity' => 'origin-access-identity/cloudfront/' . $originId,
                        ),
                    ),
                    // ... repeated
                ),
            ),
            // DefaultCacheBehavior is required
            'DefaultCacheBehavior' => array(
                // TargetOriginId is required
                'TargetOriginId'       => $originId,
                // ForwardedValues is required
                'ForwardedValues'      => array(
                    // QueryString is required
                    'QueryString' => false,
                    // Cookies is required
                    'Cookies'     => array(
                        // Forward is required
                        'Forward'          => 'none',
                        'WhitelistedNames' => array(
                            // Quantity is required
                            'Quantity' => 0,
                            'Items'    => array(),
                        ),
                    ),
                ),

                // TrustedSigners is required
                'TrustedSigners'       => array(
                    // Enabled is required
                    'Enabled'  => ($signed_url == 0 ? false : true),
                    // Quantity is required
                    'Quantity' => $signed_url,
                    'Items'    => ($signed_url == 0 ? array() : array('self')),
                ),
                // ViewerProtocolPolicy is required
                'ViewerProtocolPolicy' => 'allow-all',
                // MinTTL is required
                'MinTTL'               => 3600,
                'AllowedMethods'       => array(
                    // Quantity is required
                    'Quantity' => 2,
                    'Items'    => array('GET', 'HEAD'),
                ),
            ),
            // CacheBehaviors is required
            'CacheBehaviors'       => array(
                // Quantity is required
                'Quantity' => 1,
                'Items'    => array(
                    array(
                        // PathPattern is required
                        'PathPattern'          => '*',
                        // TargetOriginId is required
                        'TargetOriginId'       => $originId,
                        // ForwardedValues is required
                        'ForwardedValues'      => array(
                            // QueryString is required
                            'QueryString' => false,
                            // Cookies is required
                            'Cookies'     => array(
                                // Forward is required
                                'Forward'          => 'none',
                                'WhitelistedNames' => array(
                                    // Quantity is required
                                    'Quantity' => 0,
                                    'Items'    => array(),
                                ),
                            ),
                        ),
                        // TrustedSigners is required
                        'TrustedSigners'       => array(
                            // Enabled is required
                            'Enabled'  => ($signed_url == 0 ? false : true),
                            // Quantity is required
                            'Quantity' => $signed_url,
                            'Items'    => ($signed_url == 0 ? array() : array('self')),
                        ),
                        // ViewerProtocolPolicy is required
                        'ViewerProtocolPolicy' => 'allow-all',
                        // MinTTL is required
                        'MinTTL'               => 3600,
                        'AllowedMethods'       => array(
                            // Quantity is required
                            'Quantity' => 2,
                            'Items'    => array('GET', 'HEAD'),
                        ),
                    ),
                    // ... repeated
                ),
            ),
            'CustomErrorResponses' => array(
                // Quantity is required
                'Quantity' => 0,
                'Items'    => array(
                    // ... repeated
                ),
            ),
            // Comment is required
            'Comment'              => $language['backend.cf.dist.for'] . $server_name,
            // Logging is required
            'Logging'              => array(
                // Enabled is required
                'Enabled'        => false,
                // IncludeCookies is required
                'IncludeCookies' => false,
                // Bucket is required
                'Bucket'         => $bucketName,
                // Prefix is required
                'Prefix'         => 'WEB-logs/',
            ),
            // PriceClass is required
            'PriceClass'           => 'PriceClass_' . $priceClass,
            // Enabled is required
            'Enabled'              => true,

            'ViewerCertificate'    => array(
//        'IAMCertificateId' => '',
                'CloudFrontDefaultCertificate' => true,
            ),
            'Restrictions'         => array(
                // GeoRestriction is required
                'GeoRestriction' => array(
                    // RestrictionType is required
                    'RestrictionType' => 'string',
                    // Quantity is required
                    'Quantity'        => 0,
                    'Items'           => array(),
                ),
            ),
            // Id is required
            'Id'                   => $distId,
            'IfMatch'              => $etag,
        ));

        $id = $result['Id'];

        if ($id) {
            echo VGenerate::declareJS('$(document).ready(function(){$("#lb-wrapper .responsive-accordion-panel.active").prepend("Success");});');
        } else {
            echo VGenerate::declareJS('$(document).ready(function(){$("#lb-wrapper .responsive-accordion-panel.active").prepend("Failed");});');
        }

    }

    /* CloudFront create web distribution */
    public function CF_createDistro($client, $originId, $bucketName, $server_id, $signed_url = 0, $server_name = 'n/a', $priceClass = 'All')
    {
        global $db, $language;

        $result = $client->createDistribution(array(
            'Aliases'              => array('Quantity' => 0),
            'CacheBehaviors'       => array('Quantity' => 0),
            'Comment'              => $language['backend.cf.dist.for'] . $server_name,
            'Enabled'              => true,
            'CallerReference'      => $bucketName . '-distribution' . $server_id,
            'DefaultCacheBehavior' => array(
                'MinTTL'               => 3600,
                'ViewerProtocolPolicy' => 'allow-all',
                'TargetOriginId'       => $originId,
                'TrustedSigners'       => array(
                    'Enabled'  => ($signed_url == 0 ? false : true),
                    'Quantity' => $signed_url,
                    'Items'    => ($signed_url == 0 ? array() : array('self')),
                ),
                'ForwardedValues'      => array(
                    'QueryString' => false,
                    'Cookies'     => array(
                        'Forward' => 'all',
                    ),
                ),
            ),
            'DefaultRootObject'    => '',
            'Logging'              => array(
                'Enabled'        => false,
                'Bucket'         => $bucketName,
                'Prefix'         => 'WEB-logs/',
                'IncludeCookies' => true,
            ),
            'Origins'              => array(
                'Quantity' => 1,
                'Items'    => array(
                    array(
                        'Id'             => $originId,
                        'DomainName'     => "{$bucketName}.s3.amazonaws.com",
                        'S3OriginConfig' => array(
                            'OriginAccessIdentity' => 'origin-access-identity/cloudfront/' . $originId,
                        ),
                    ),
                ),
            ),
            'PriceClass'           => 'PriceClass_' . $priceClass,
        ));

        $id       = $result['Id'];
        $status   = $result['Status'];
        $domain   = $result['DomainName'];
        $location = $result['Location'];

        if ($id) {
            $db->execute(sprintf("UPDATE `db_servers` SET `cf_dist_id`='%s', `cf_dist_status`='%s', `cf_dist_domain`='%s', `cf_dist_uri`='%s' WHERE `server_id`='%s' LIMIT 1;", $id, $status, $domain, $location, $server_id));
            echo $language['notif.success.request'] . "<br /><br />";
            echo $language['backend.cf.dist.id'] . $id . "<br /><br />";
            echo $language['backend.cf.dist.status'] . $status . "<br /><br />";
            echo $language['backend.cf.dist.domain'] . $domain . "<br /><br />";
            echo $language['backend.cf.dist.uri'] . $location . "<br /><br />";
        } else {
            echo $language['notif.error.invalid.request'] . "<br />";
        }
    }
    /* CloudFront status */
    public function CFInfo()
    {
        global $db, $language;

        $server_id = intval($_GET['i']);
        $rs        = $db->execute(sprintf("SELECT `server_name`, `s3_accesskey`, `s3_secretkey`, `s3_bucketname`, `s3_region`, `cf_origin_id`, `cf_dist_type`, `cf_dist_id`, `cf_dist_status`, `cf_dist_domain`, `cf_dist_uri` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $server_id));

        $serverName   = $rs->fields['server_name'];
        $awsAccessKey = $rs->fields['s3_accesskey'];
        $awsSecretKey = $rs->fields['s3_secretkey'];
        $bucketName   = $rs->fields['s3_bucketname'];
        $region       = $rs->fields['s3_region'];
        $originId     = $rs->fields['cf_origin_id'];
        $distId       = $rs->fields['cf_dist_id'];
        $distStatus   = $rs->fields['cf_dist_status'];
        $distDomain   = $rs->fields['cf_dist_domain'];
        $distUri      = $rs->fields['cf_dist_uri'];
        $distType     = $rs->fields['cf_dist_type'];

//    $aws = Aws\Common\Aws::factory();
        //    $cf = $aws->get('CloudFront');
        //    $cf->setCredentials(new Credentials($awsAccessKey, $awsSecretKey));
        $sdk = new Aws\Sdk([
            'region'     => $region,
            'version'    => 'latest',
            'CloudFront' => [
                'region' => $region,
            ],
        ]);
        $cf = $sdk->createCloudflare();

        try {
            if ($_GET['do'] == 'cf-status') {
//get distibution status
                if ($distType == 'w') {
                    $result = $cf->getDistribution(array('Id' => $distId));
                } elseif ($distType == 'r') {
                    $result = $cf->getStreamingDistribution(array('Id' => $distId));
                }
                if ($result['Status']) {
                    $status = $result['Status'];
                    echo '<b>' . $status . '</b>';

                    if ($distStatus != $status) {
                        $db->execute(sprintf("UPDATE `db_servers` SET `cf_dist_status`='%s' WHERE `server_id`='%s' LIMIT 1;", $status, $server_id));
                    }
                }
            } elseif ($_GET['do'] == 'cf-del-origin') {
//delete origin id
                $result = $cf->deleteCloudFrontOriginAccessIdentity(array(
                    'Id' => $originId,
                ));
                if ($result['RequestId']) {
                    echo '<b>deleted</b>';

                    $db->execute(sprintf("UPDATE `db_servers` SET `cf_dist_status`='', `cf_origin_id`='' WHERE `server_id`='%s' LIMIT 1;", $server_id));
                }
            } elseif ($_GET['do'] == 'cf-del-dist') {
//delete distribution
                $result = $cf->deleteDistribution(array(
                    'Id' => $distId,
                ));
                if ($result['RequestId']) {
                    echo '<b>deleted</b>';

                    $db->execute(sprintf("UPDATE `db_servers` SET `cf_dist_id`='', `cf_dist_domain`='', `cf_dist_uri`='' WHERE `server_id`='%s' LIMIT 1;", $server_id));
                }
            } elseif ($_GET['do'] == 's3-delete') {
//delete s3 bucket
                echo "<b>deleted</b>";
                $s3 = $aws->get('s3');

                $s3->setCredentials(new Credentials($awsAccessKey, $awsSecretKey));

                $iterator = $s3->getIterator('ListObjects', array(
                    'Bucket' => $bucketName,
//            'Prefix' => 'myprefix/' // top level pseudo "folder" in bucket
                ));
                $s3->registerStreamWrapper();
                foreach ($iterator as $object) {
                    unlink('s3://' . $bucketName . '/' . $object['Key']);
                }
                $s3->deleteBucket(array('Bucket' => $bucketName));
                $s3->waitUntilBucketNotExists(array('Bucket' => $bucketName));
                $db->execute(sprintf("UPDATE `db_servers` SET `s3_bucketname`='', `s3_region`='' WHERE `server_id`='%s' LIMIT 1;", $server_id));
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage() . "<br /><br />";
        }
    }
    /* CloudFront connection setup */
    public function CFConn($update = 0)
    {
        global $db, $language;

        $ht        = null;
        $server_id = intval($_GET['i']);
        $rs        = $db->execute(sprintf("SELECT `server_name`, `s3_accesskey`, `s3_secretkey`, `s3_bucketname`, `s3_region`, `cf_origin_id`, `cf_dist_type`, `cf_dist_price`, `cf_dist_id`, `cf_dist_status`, `cf_dist_domain`, `cf_dist_uri`, `cf_signed_url` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $server_id));

        $serverName   = $rs->fields['server_name'];
        $awsAccessKey = $rs->fields['s3_accesskey'];
        $awsSecretKey = $rs->fields['s3_secretkey'];
        $bucketName   = $rs->fields['s3_bucketname'];
        $region       = $rs->fields['s3_region'];
        $originId     = $rs->fields['cf_origin_id'];
        $distId       = $rs->fields['cf_dist_id'];
        $distStatus   = $rs->fields['cf_dist_status'];
        $distDomain   = $rs->fields['cf_dist_domain'];
        $distUri      = $rs->fields['cf_dist_uri'];
        $distType     = $rs->fields['cf_dist_type'];
        $distPrice    = $rs->fields['cf_dist_price'];
        $signedURL    = $rs->fields['cf_signed_url'];

        if ($bucketName == '') {
            $ht = $language['backend.s3.no.bucket'];
            return $ht;
        }
/*
$aws = Aws\Common\Aws::factory();
$cf = $aws->get('CloudFront');

$cf->setCredentials(new Credentials($awsAccessKey, $awsSecretKey));
 */
        $sdk = new Aws\Sdk([
            'region'     => $region,
            'version'    => 'latest',
            'CloudFront' => [
                'region' => $region,
            ],
        ]);
        $cf = $sdk->createCloudflare();

        if ($update == 1) {
            try {
                if ($originId == '' or $distId == '') {
                    $ht .= $language['backend.cf.origin.none'];
                } else {
                    if ($distType == 'w') {
                        self::CF_updateDistro($cf, $originId, $bucketName, $server_id, $distId, $signedURL, $serverName, $distPrice);
                    } elseif ($distType == 'r') {
                        self::CF_updateRTMPDistro($cf, $originId, $bucketName, $server_id, $distId, $signedURL, $serverName, $distPrice);
                    }
                }

            } catch (Exception $e) {
                $ht .= "Error: " . $e->getMessage() . "<br /><br />";
            }

        } elseif ($update == 0) {
            try {
                if ($originId == '') {
                    $result = $cf->createCloudFrontOriginAccessIdentity(array(
                        'CallerReference' => 'CFOAI',
                        'Comment'         => 'CFOAI',
                    ));

                    $ht .= $language['backend.cf.origin.set'] . "<br /><br />";

                    if ($result['Id']) {
                        $originId = $result['Id'];

                        $ht .= $language['notif.success.request'] . ": " . $originId . "<br /><br />";
                        $db->execute(sprintf("UPDATE `db_servers` SET `cf_origin_id`='%s' WHERE `server_id`='%s' LIMIT 1;", $result['Id'], $server_id));
                    } else {
                        $ht .= $language['notif.error.invalid.request'] . "<br />";
                    }
                } else {
                    $ht .= $language['backend.cf.origin.found'] . '<span id="origin-id' . $server_id . '">' . $originId . '</span> <span class="no-display">(<a href="javascript:;" class="cf-del-origin">delete</a>)</span><br /><br />';
                }

                if ($distId == '') {
                    $ht .= $language['backend.cf.dist.new'] . "<br /><br />";
                    if ($distType == 'w') {
                        self::CF_createDistro($cf, $originId, $bucketName, $server_id, $signedURL, $serverName, $distPrice);
                    } elseif ($distType == 'r') {
                        self::CF_createRTMPDistro($cf, $originId, $bucketName, $server_id, $signedURL, $serverName, $distPrice);
                    }
                } else {
                    $ht .= $language['backend.cf.dist.found'] . '<span id="dist-id' . $server_id . '">' . $distId . '</span> <span class="no-display">(<a href="javascript:;" class="cf-del-dist">delete</a>)</span><br /><br />';
                    $ht .= $language['backend.cf.dist.status'] . '<span id="dist-status' . $server_id . '">' . $distStatus . "</span> (<a href=\"javascript:;\" class=\"cf-status\">get status from host</a>)<br /><br />";
                    $ht .= $language['backend.cf.dist.domain'] . $distDomain . "<br /><br />";
                    $ht .= $language['backend.cf.dist.uri'] . $distUri . "<br /><br />";
                }
            } catch (Exception $e) {
                $ht .= "Error: " . $e->getMessage() . "<br /><br />";
            }
        }

        $html .= $ht;

        $js .= '$("a.cf-status").click(function(){'; //get status
        $js .= '$(".fancybox-wrap").mask("");';
        $js .= '$.post(current_url + menu_section + "?s="+$(".menu-panel-entry-sub-active").attr("id")+"&do=cf-status&i=' . $server_id . '",';
        $js .= 'function(data) {';
        $js .= '$("#dist-status' . $server_id . '").addClass("bold").html(data);';
        $js .= '$(".fancybox-wrap").unmask();';
        $js .= '});';
        $js .= 'return false;';
        $js .= '});';
        $js .= '$("a.cf-del-origin").click(function(){'; //delete origin
        $js .= 'var del_confirm = confirm("Deleting Origin ID could result in permanent Distribution failure!");';
        $js .= 'if(del_confirm){';
        $js .= '$(".fancybox-wrap").mask("");';
        $js .= '$.post(current_url + menu_section + "?s="+$(".menu-panel-entry-sub-active").attr("id")+"&do=cf-del-origin&i=' . $server_id . '",';
        $js .= 'function(data) {';
        $js .= '$("#origin-id' . $server_id . '").html(data);';
        $js .= '$(".fancybox-wrap").unmask();';
        $js .= '});';
        $js .= 'return false;';
        $js .= '}';
        $js .= '});';
        $js .= '$("a.cf-del-dist").click(function(){'; //delete distribution
        $js .= 'var del_confirm = confirm("Please confirm deleting this Distribution!");';
        $js .= 'if(del_confirm){';
        $js .= '$(".fancybox-wrap").mask("");';
        $js .= '$.post(current_url + menu_section + "?s="+$(".menu-panel-entry-sub-active").attr("id")+"&do=cf-del-dist&i=' . $server_id . '",';
        $js .= 'function(data) {';
        $js .= '$("#dist-id' . $server_id . '").html(data);';
        $js .= '$(".fancybox-wrap").unmask();';
        $js .= '});';
        $js .= 'return false;';
        $js .= '}';
        $js .= '});';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    public function url_safe_base64_encode($value)
    {
        $encoded = base64_encode($value);
        // replace unsafe characters +, = and / with
        // the safe characters -, _ and ~
        return str_replace(
            array('+', '=', '/'),
            array('-', '_', '~'),
            $encoded);
    }
    public function getS3SignedURL($aws_access_key_id, $aws_secret_key, $file_path, $aws_bucket, $timeout, $server_type = 's3')
    {
        $http_verb    = "GET";
        $content_md5  = "";
        $content_type = "";
        $expires      = time() + $timeout;

        $canonicalizedAmzHeaders = "";
        $canonicalizedResource   = '/' . $aws_bucket . '/' . $file_path;

        $stringToSign = $http_verb . "\n" . $content_md5 . "\n" . $content_type . "\n" . $expires . "\n" . $canonicalizedAmzHeaders . $canonicalizedResource;
        $signature    = urlencode(self::hex2b64(self::hmacsha1($aws_secret_key, utf8_encode($stringToSign))));
        $node         = $server_type == 'ws' ? 'wasabisys' : 'amazonaws';
        $url          = "https://$aws_bucket.s3." . $node . ".com/$file_path?AWSAccessKeyId=$aws_access_key_id&Signature=$signature&Expires=$expires";

        return $url;
    }
    public function hmacsha1($key, $data)
    {
        $blocksize = 64;
        $hashfunc  = 'sha1';
        if (strlen($key) > $blocksize) {
            $key = pack('H*', $hashfunc($key));
        }

        $key  = str_pad($key, $blocksize, chr(0x00));
        $ipad = str_repeat(chr(0x36), $blocksize);
        $opad = str_repeat(chr(0x5c), $blocksize);
        $hmac = pack(
            'H*', $hashfunc(
                ($key ^ $opad) . pack(
                    'H*', $hashfunc(
                        ($key ^ $ipad) . $data
                    )
                )
            )
        );
        return bin2hex($hmac);
    }
    public function hex2b64($str)
    {
        $raw = '';
        for ($i = 0; $i < strlen($str); $i += 2) {
            $raw .= chr(hexdec(substr($str, $i, 2)));
        }
        return base64_encode($raw);
    }
    /* create CloudFront signed URL */
    public function getSignedURL($resource, $timeout, $keyPairId, $keyPairFile, $channel = 0, $custom_policy = 1)
    {
//    $custom_policy  = 1; //use custom policy

        $allowIP = $_SERVER[REM_ADDR] . '/32'; //allow IP address or network range, CIDR format

        $expires = time() + $timeout; //Time out in seconds
        if ($custom_policy == 1) {
            $statement = array(
                'Statement' => array(
                    array(
                        'Resource'  => $resource,
                        'Condition' => array(
                            'DateLessThan' => array('AWS:EpochTime' => $expires),
                            'IpAddress'    => array('AWS:SourceIp' => $allowIP)))));

            $json = json_encode($statement);
        } else {
            $json = '{"Statement":[{"Resource":"' . $resource . '","Condition":{"DateLessThan":{"AWS:EpochTime":' . $expires . '}}}]}';
        }

        //Read Cloudfront Private Key Pair
        $fp       = fopen($keyPairFile, "r");
        $priv_key = fread($fp, 8192);
        fclose($fp);

        //Create the private key
        $key = openssl_get_privatekey($priv_key);
        if (!$key) {
            echo "<p>Failed to load private key!</p>";
            return;
        }

        //Sign the policy with the private key
        if (!openssl_sign($json, $signed_policy, $key, OPENSSL_ALGO_SHA1)) {
            echo '<p>Failed to sign policy: ' . openssl_error_string() . '</p>';
            return;
        }

        //Create url safe signed policy
        $base64_signed_policy = base64_encode($signed_policy);
        $signature            = str_replace(array('+', '=', '/'), array('-', '_', '~'), $base64_signed_policy);

        //Construct the URL
        $url = ($channel == 0 ? $resource : null) . '?Expires=' . $expires . '&Signature=' . $signature . '&Key-Pair-Id=' . $keyPairId . ($custom_policy == 1 ? '&Policy=' . self::url_safe_base64_encode($json) : null);

        return $url;
    }
    /* S3 connection check */
    public function S3Conn()
    {
        global $db, $language, $cfg;

        $server_id = intval($_GET['i']);
        $rs        = $db->execute(sprintf("SELECT `server_type`, `s3_accesskey`, `s3_secretkey`, `s3_bucketname`, `s3_region` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $server_id));

        $server_type  = $rs->fields['server_type'];
        $awsAccessKey = $rs->fields['s3_accesskey'];
        $awsSecretKey = $rs->fields['s3_secretkey'];
        $bucketName   = $rs->fields['s3_bucketname'];
        $region       = $rs->fields['s3_region'];

        if ($server_type == 's3') {
            $regarr = self::region();
            $ws     = array();
        } elseif ($server_type == 'ws') {
            $regarr = array(
                "us-east-1"    => "US East",
                "us-west-1"    => "US West",
                "eu-central-1" => "EU Central",
            );
            $ws = array(
                'region'  => $region,
                'version' => 'wasabi',
            );
        }

        $s3 = new S3Client([
            'version'     => 'latest',
            'region'      => $region,
            'credentials' => [
                'key'    => $awsAccessKey,
                'secret' => $awsSecretKey,
            ],
        ]);

        try {
            $result = $s3->listBuckets();

            if (is_array($result['Buckets'])) {
                $ht = VGenerate::simpleDivWrap('row', '', str_replace(array('AccessKey', 'SecretKey'), array('<b>AccessKey</b>', '<b>SecretKey</b>'), $language['backend.s3.logged.in']));

                if ($s3->doesBucketExist($bucketName)) {
                    $ht .= VGenerate::simpleDivWrap('row', '', "Bucket <b>{$bucketName}</b> was not created (already exists)");
                } else {
                    $ht .= VGenerate::simpleDivWrap('row', '', "Bucket name <b>{$bucketName}</b> is available");
                    $bar = array('Bucket' => $bucketName);
                    $res = $s3->createBucket($bar);

                    if ($res['RequestId']) {
                        $ht .= VGenerate::simpleDivWrap('row', '', "Bucket <b>{$bucketName}</b> was successfully created");
                        $ht .= VGenerate::simpleDivWrap('row', '', "Bucket region successfully set to <b>" . $regarr[($region == '' ? 'default' : $region)] . "</b>");
                    } else {
                        $ht .= VGenerate::simpleDivWrap('row', '', "Bucket <b>{$bucketName}</b> could not be created");
                    }
                }
                foreach ($result['Buckets'] as $bucket) {
                    $ht .= $bucket['Name'] == $bucketName ? VGenerate::simpleDivWrap('row', 'bucket-row' . $server_id, "Bucket <b>{$bucketName}</b> is set up and ready to go <span class=\"no-display\">(<a href=\"javascript:;\" class=\"s3-delete\">delete bucket</a>)</span>") : null;
                }
            }
        } catch (Exception $e) {
            $ht .= "Error: " . $e->getMessage();
        }

        $html .= $ht;

        $js .= '$("a.s3-delete").click(function(){'; //delete s3 bucket
        $js .= 'var del_confirm = confirm("This will also delete all bucket contents!");';
        $js .= 'if(del_confirm){';
        $js .= '$(".fancybox-wrap").mask("");';
        $js .= '$.post(current_url + menu_section + "?s="+$(".menu-panel-entry-sub-active").attr("id")+"&do=s3-delete&i=' . $server_id . '",';
        $js .= 'function(data) {';
        $js .= '$("#bucket-row' . $server_id . '").html(data);';
        $js .= '$(".fancybox-wrap").unmask();';
        $js .= '});';
        $js .= 'return false;';
        $js .= '}';
        $js .= '});';

        $html .= VGenerate::declareJS('$(document).ready(function(){' . $js . '});');

        return $html;
    }
    /* ftp connection check */
    public function ftpConn()
    {
        global $language, $db;

        $sid      = intval($_GET['i']);
        $rs       = $db->execute(sprintf("SELECT `ftp_host`, `ftp_port`, `ftp_transfer`, `ftp_passive`, `ftp_username`, `ftp_password`, `ftp_root` FROM `db_servers` WHERE `server_id`='%s' LIMIT 1;", $sid));
        $ftp_host = $rs->fields['ftp_host'];
        $ftp_port = intval($rs->fields['ftp_port']);
        $ftp_xfer = $rs->fields['ftp_transfer'];
        $ftp_pssv = $rs->fields['ftp_passive'];
        $ftp_user = $rs->fields['ftp_username'];
        $ftp_pass = $rs->fields['ftp_password'];
        $ftp_root = $rs->fields['ftp_root'];

        $ftp            = new ftp(true);
        $ftp->Verbose   = false;
        $ftp->LocalEcho = false;

        $html .= "<br>";
        if (!$ftp->SetServer($ftp_host, $ftp_port)) {
            $ftp->quit();
            $html .= "Error: No server<br><br>";
        }
        if (!$ftp->connect()) {
            $html .= "Error: Cannot connect<br><br>";
        }
        if (!$ftp->login($ftp_user, $ftp_pass)) {
            $ftp->quit();
            $html .= "Error: Login failed<br><br>";
        }

        if (!$ftp->SetType(($ftp_xfer == 'FTP_AUTOASCII' ? FTP_AUTOASCII : ($ftp_xfer == 'FTP_ASCII' ? FTP_ASCII : FTP_BINARY)))) {
            $html .= "Error: SetType " . $ftp_xfer . " failed!<br><br>";
        }

        if (!$ftp->Passive(false)) {
            $html .= "Error: Passive mode failed!<br><br>";
        }

        $ftp->chdir($ftp_root);

        $list = $ftp->rawlist($ftp_root, "-la");
        if ($list === false) {
            $html .= "Error: Listing failed!";
        } else {
            $html .= "<br />";
            foreach ($list as $k => $v) {
                $html .= $v;
                $html .= "<br />";
            }
        }
        $ftp->quit();

        return $html;
    }
    /* processing entry */
    public function processEntry()
    {
        global $class_database, $db, $language, $cfg;

        $form           = VArraySection::getArray("up_servers");
        $allowedFields  = $form[1];
        $requiredFields = $form[2];

        $error_message = VForm::checkEmptyFields($allowedFields, $requiredFields);
        if ($error_message != '') {
            echo VGenerate::noticeTpl('', $error_message, '');
        }

        if ($error_message == '') {
            $ct_id                = intval($_POST['hc_id']);
            $server_name          = $form[0]['server_name'];
            $server_type          = $form[0]['server_type'];
            $server_priority      = $form[0]['server_priority'];
            $server_limit         = $form[0]['server_limit'];
            $server_filehop       = $form[0]['file_hop'];
            $upload_v_file        = $form[0]['upload_v_file'];
            $upload_v_thumb       = $form[0]['upload_v_thumb'];
            $upload_s_file        = $form[0]['upload_s_file'];
            $upload_s_thumb       = $form[0]['upload_s_thumb'];
            $upload_i_file        = $form[0]['upload_i_file'];
            $upload_i_thumb       = $form[0]['upload_i_thumb'];
            $upload_a_file        = $form[0]['upload_a_file'];
            $upload_a_thumb       = $form[0]['upload_a_thumb'];
            $upload_d_file        = $form[0]['upload_d_file'];
            $upload_d_thumb       = $form[0]['upload_d_thumb'];
            $ftp_transfer         = $form[0]['ftp_transfer'];
            $ftp_passive          = $form[0]['ftp_passive'];
            $ftp_host             = $form[0]['ftp_host'];
            $ftp_port             = $form[0]['ftp_port'];
            $ftp_user             = $form[0]['ftp_username'];
            $ftp_pass             = $form[0]['ftp_password'];
            $ftp_root             = $form[0]['ftp_root'];
            $base_url             = $form[0]['url'];
            $lighttpd_url         = $form[0]['lighttpd_url'];
            $lighttpd_secdownload = $form[0]['lighttpd_secdownload'];
            $lighttpd_prefix      = $form[0]['lighttpd_prefix'];
            $lighttpd_key         = $form[0]['lighttpd_key'];
            $s3_bucketname        = $form[0]['s3_bucketname'];
            $s3_accesskey         = $form[0]['s3_accesskey'];
            $s3_secretkey         = $form[0]['s3_secretkey'];
            $s3_region            = $form[0]['s3_region'];
            $s3_fileperm          = $form[0]['s3_fileperm'];
            $cf_enable            = $form[0]['cf_enabled'];
            $cf_dist_type         = $form[0]['cf_dist_type'];
            $cf_dist_price        = $form[0]['cf_dist_price'];
            $cf_signed_url        = $form[0]['cf_signed_url'];
            $cf_signed_expire     = $form[0]['cf_signed_expire'];
            $cf_key_pair          = $form[0]['cf_key_pair'];
            $cf_key_file          = $form[0]['cf_key_file'];

            switch ($_GET['do']) {
                case "update":
                    if ($server_type == 'ftp') {
                        $usql = sprintf(", `ftp_host`='%s', `ftp_port`='%s', `ftp_transfer`='%s', `ftp_passive`='%s', `ftp_username`='%s', `ftp_password`='%s', `ftp_root`='%s', `url`='%s', `lighttpd_url`='%s', `lighttpd_secdownload`='%s', `lighttpd_prefix`='%s', `lighttpd_key`='%s'",
                            $ftp_host, $ftp_port, $ftp_transfer, $ftp_passive, $ftp_user, $ftp_pass, $ftp_root, $base_url, $lighttpd_url, $lighttpd_secdownload, $lighttpd_prefix, $lighttpd_key);
                    } elseif ($server_type == 's3' or $server_type == 'ws') {
                        $usql = sprintf(", `s3_bucketname`='%s', `s3_accesskey`='%s', `s3_secretkey`='%s', `s3_region`='%s', `s3_fileperm`='%s', `cf_enabled`='%s',
                        `cf_dist_type`='%s', `cf_dist_price`='%s', `cf_signed_url`='%s', `cf_signed_expire`='%s', `cf_key_pair`='%s', `cf_key_file`='%s'",
                            $s3_bucketname, $s3_accesskey, $s3_secretkey, $s3_region, $s3_fileperm, $cf_enable, $cf_dist_type, $cf_dist_price, $cf_signed_url, $cf_signed_expire, $cf_key_pair, $cf_key_file);
                    }
                    $sql = sprintf("UPDATE `db_servers` SET `server_name`='%s', `server_type`='%s', `server_priority`='%s', `server_limit`='%s', `file_hop`='%s',
            `upload_v_file`='%s', `upload_v_thumb`='%s',
            `upload_s_file`='%s', `upload_s_thumb`='%s',
            `upload_i_file`='%s', `upload_i_thumb`='%s',
            `upload_a_file`='%s', `upload_a_thumb`='%s',
            `upload_d_file`='%s', `upload_d_thumb`='%s' %s WHERE `server_id`='%s' LIMIT 1;",
                        $server_name, $server_type, $server_priority, $server_limit, $server_filehop, $upload_v_file, $upload_v_thumb, $upload_s_file, $upload_s_thumb, $upload_i_file, $upload_i_thumb, $upload_a_file, $upload_a_thumb, $upload_d_file, $upload_d_thumb, $usql, $ct_id);

                    $db->execute($sql);
                    break;
                case "add":
                    $class_database->doInsert('db_servers', $form[0]);
                    break;
            }
            if ($db->Affected_Rows() > 0) {
                echo VGenerate::noticeTpl('', '', $language['notif.success.request']);
            }

        }
    }
    public function popupTpl($tpl)
    {
        global $cfg, $smarty;

        $id = (int) $_GET['i'];

        switch ($tpl) {
            case "ftp-probe":
                $response = 'count-request';
                $icon     = 'icon-connection';
                $title    = 'FTP Connection Test';
                break;
            case "s3-probe":
                $response = 'bucket-request';
                $icon     = 'icon-connection';
                $title    = 'AWS/WS S3 Bucket Setup';
                break;
            case "cf-test":
                $response = 'cf-request';
                $icon     = 'icon-video';
                $title    = 'Amazon CloudFront Distribution Setup';
                break;
            case "cf-update":
                $response = 'cf-request';
                $icon     = 'icon-video';
                $title    = 'Amazon CloudFront Distribution Update';
                break;
            case "cf-status":
                return;
                break;
            case "cf-del-origin":
            case "cf-del-dist":
            case "s3-delete":
                break;
            case "ftp-list":
                $response = 'count-request';
                $icon     = 'icon-list2';
                $title    = 'Files transferred to this server';
                break;
            case "xfer-log":
                $response = 'log-request';
                $icon     = 'icon-console';
                $title    = 'Transfer log';
                break;
            case "ftp-reset":
                $response = 'reset-request';
                $icon     = 'icon-switch';
                $title    = 'Select a count to be reset';
                break;
/*            case "reset-count":
$response = 'reset-request';
break;*/
        }

        $ht = '<div id="lb-wrapper">';
        $ht .= '<div class="entry-list vs-column full">';
        $ht .= '<ul class="responsive-accordion responsive-accordion-default bm-larger">';
        $ht .= '<li>';
        $ht .= '<div>';
        $ht .= '<div class="responsive-accordion-head-off active' . ($tpl == 'xfer-log' ? ' no-display' : null) . '">';
        $ht .= VGenerate::headingArticle($title, $icon);
        $ht .= '<div id="' . $response . $id . '" class="left-float wdmax"></div>';
        $ht .= '</div>';
        $ht .= '<div class="responsive-accordion-panel active">';
        $ht .= '##CONTENT##';
        $ht .= '</div>';
        $ht .= '</div>';
        $ht .= '</li>';
        $ht .= '</ul>';
        $ht .= '</div>';
        $ht .= '</div>';

        return $ht;
    }
}
