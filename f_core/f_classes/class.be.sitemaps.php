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

class VbeSitemaps
{
    /* rebuild video, image sitemap */
    public function doFileSitemap($type)
    {
        global $cfg, $db, $class_database;
        $cfg[] = $class_database->getConfigurations('video_player');

        if ($_POST) {
            $entries = VArrayConfig::cfgSection();

            $count  = 1;
            $start  = 0;
            $amount = $entries["sitemap_" . $type . "_max"];

            $enc          = array();
            $index        = array();
            $mod          = $type;
            $current_date = date('Y-m-d');
            $sm_dir       = sprintf("%s/sm_%s", $cfg['sitemap_dir'], $type);
            if (!is_dir($sm_dir)) {
                mkdir($sm_dir, 0777);
                copy($cfg['sitemap_dir'] . '/sm_video/.htaccess', $sm_dir . '/.htaccess');
            }

            exec(sprintf("rm -rf %s", $cfg['sitemap_dir'] . '/sm_' . $type . '/*.xml'));

            $sql = sprintf("SELECT
                            A.`file_key`, A.`file_duration`, A.`file_like`, A.`file_dislike`, A.`file_views`, A.`upload_date`, A.`file_hd`,
                            A.`file_title`, A.`file_description`, A.`file_tags`, A.`file_category`, A.`thumb_cache`,
                            D.`usr_user`, D.`usr_key`,
                            E.`ct_name`
                            FROM `db_%sfiles` A, `db_accountuser` D, `db_categories` E
                            WHERE
                            A.`privacy`='public' AND
                            A.`approved`='1' AND
                            A.`deleted`='0' AND
                            A.`active`='1' AND
                            A.`usr_id`=D.`usr_id` AND
                            A.`file_category`=E.`ct_id`
                            ORDER BY A.`db_id` DESC", $mod);
            $total       = $db->execute(sprintf("SELECT COUNT(*) AS `total` FROM `db_%sfiles` WHERE `privacy`='public' AND `approved`='1' AND `deleted`='0' AND `active`='1';", $mod));
            $total_files = $total->fields['total'];

            while ($start < $total_files) {
                $filename = 'sitemap-' . $type . '-' . $count . '.xml';

                $files = $db->execute($sql . ' LIMIT ' . $start . ', ' . $amount);

                ob_start();
                echo $ht = '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"', "\n";
                echo $ht = "\t", 'xmlns:video="http://www.google.com/schemas/sitemap-' . $type . '/1.1">', "\n";
                /* loop files */
                while (!$files->EOF) {
                    $ht = ($type == 'video' or $type == 'short') ? self::sitemapVideo_entry($files, '', $cfg['video_player'], $type) : self::sitemapImage_entry($files); //file entry
                    $ht .= ($type == 'video' and $files->fields['file_hd'] == 1 and $entries['sitemap_video_hd'] == 1) ? self::sitemapVideo_entry($files, 1, $cfg['video_player']) : null; //mp4 entry

                    $files->MoveNext();
                }
                echo $ht = "\n", '</urlset>';
                $content = ob_get_contents();
                ob_end_clean();
                /* sitemap filename */
                $file    = $cfg['sitemap_dir'] . '/sm_' . $type . '/' . $filename;
                $index[] = array('url' => $cfg['sitemap_url'] . '/sm_' . $type . '/' . $filename, 'lastmod' => $current_date);
                /* write users sitemap */
                if (!VFileinfo::write($file, $content)) {
                    echo $errors = 'Failed to write the ' . $type . ' sitemap file (' . $file . ')!';
                }

                $start = ($start + $amount);
                ++$count;
            }

            /* sitemap index */
            ob_start();
            echo $ht = '<?xml version="1.0" encoding="UTF-8"?>', "\n";
            echo $ht = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
            foreach ($index as $key) {
                echo $ht = "\t", '<sitemap>', "\n";
                echo $ht = "\t\t", '<loc>' . $key['url'] . '</loc>', "\n";
                echo $ht = "\t\t", '<lastmod>' . $key['lastmod'] . '</lastmod>', "\n";
                echo $ht = "\t", '</sitemap>', "\n";
            }
            echo $ht = '</sitemapindex>';

            $content = ob_get_contents();
            ob_end_clean();

            $file = $cfg['sitemap_dir'] . '/sm_' . $type . '/sitemap-' . $type . '.xml';
            if (!VFileinfo::write($file, $content)) {
                $errors = 'Failed to write ' . $type . ' sitemap index file (' . $file . ')!';
            }
        }
    }

    /* image sitemap entry loop */
    public function sitemapImage_entry($images)
    {
        global $cfg;

        $_loc        = $cfg['main_url'] . '/' . VGenerate::fileHref('i', $images->fields['file_key'], '');
        $thumb_cache = $images->fields['thumb_cache'];
        $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;
        $_file       = VGenerate::thumbSigned('image', $images->fields['file_key'], array($images->fields['usr_key'], $thumb_cache), (3600 * 24 * 2), 0);

        echo $ht = "\n\t", '<url>';
        echo $ht = "\n\t\t", '<loc>' . $_loc . '</loc>';
        echo $ht = "\n\t\t", '<image:image>';
        echo $ht = "\n\t\t\t", '<image:loc>' . $_file . '</image:loc>';
        echo $ht = "\n\t\t\t", '<image:title>' . html_entity_decode($images->fields['file_title'], ENT_QUOTES, 'UTF-8') . '</image:title>';
        echo $ht = "\n\t\t\t", '<image:caption>' . html_entity_decode($images->fields['file_description'], ENT_QUOTES, 'UTF-8') . '</image:caption>';
        echo $ht = "\n\t\t", '</imageo:image>';
        echo $ht = "\n\t", '<url>';
    }
    /* video sitemap entry loop */
    public function sitemapVideo_entry($videos, $hd = '', $player = '', $type = 'video')
    {
        global $cfg, $class_database, $db;

        $cfg[]       = $class_database->getConfigurations('stream_method,stream_server,stream_lighttpd_secure');
        $_loc        = $cfg['main_url'] . '/' . VGenerate::fileHref($type[0], $videos->fields['file_key'], $videos->fields['file_title']);
        $thumb_cache = $videos->fields['thumb_cache'];
        $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;
        $_thumb      = VGenerate::thumbSigned($type, $videos->fields['file_key'], array($videos->fields['usr_key'], $thumb_cache), (3600 * 24 * 7), 0);
        $_fsrc       = $class_database->singleFieldValue('db_' . $type . 'files', 'embed_src', 'file_key', $videos->fields['file_key']);

        $_src                         = VPlayers::fileSources($type, $videos->fields['usr_key'], $videos->fields['file_key']);
        $entries['sitemap_video_src'] = 'location';

        $url = VGenerate::fileURL($type, $videos->fields['file_key'], 'upload');
        $sql = sprintf("SELECT
                                                A.`server_type`, A.`lighttpd_url`, A.`lighttpd_secdownload`, A.`lighttpd_prefix`, A.`lighttpd_key`, A.`cf_enabled`, A.`cf_dist_type`,
                                                A.`cf_signed_url`, A.`cf_signed_expire`, A.`cf_key_pair`, A.`cf_key_file`, A.`s3_bucketname`, A.`s3_accesskey`, A.`s3_secretkey`,
                                                B.`upload_server`
                                                FROM
                                                `db_servers` A, `db_%sfiles` B
                                                WHERE
                                                B.`file_key`='%s' AND
                                                B.`upload_server` > '0' AND
                                                A.`server_id`=B.`upload_server` LIMIT 1;", $type, $videos->fields['file_key']);
        $srv = $db->execute($sql);

        $cf_signed_url     = $srv->fields['cf_signed_url'];
        $cf_signed_expire  = 3600 * 24 * 2; //$srv->fields['cf_signed_expire'];
        $cf_key_pair       = $srv->fields['cf_key_pair'];
        $cf_key_file       = $srv->fields['cf_key_file'];
        $aws_access_key_id = $srv->fields['s3_accesskey'];
        $aws_secret_key    = $srv->fields['s3_secretkey'];
        $aws_bucket        = $srv->fields['s3_bucketname'];

        if (count($_src) > 0) {
            $_file = array();
            foreach ($_src as $k => $v) {
                if (($srv->fields['lighttpd_url'] != '' and $srv->fields['lighttpd_secdownload'] == 1) or ($cfg['stream_method'] == 2 and $cfg['stream_server'] == 'lighttpd' and $cfg['stream_lighttpd_secure'] == 1)) {
                    $l0   = explode("/", $v[0]);
                    $loc0 = $cfg['media_files_dir'] . '/' . $l0[6] . '/' . $l0[7] . '/' . $l0[8];
                    $l1   = explode("/", $v[1]);
                    $loc1 = $cfg['media_files_dir'] . '/' . $l1[6] . '/' . $l1[7] . '/' . $l1[8];
                    $l2   = explode("/", $v[2]);
                    $loc2 = $cfg['media_files_dir'] . '/' . $l2[6] . '/' . $l2[7] . '/' . $l2[8];
                } elseif ($cfg['stream_method'] == 2 and $cfg['stream_server'] == 'lighttpd' and $cfg['stream_lighttpd_secure'] == 0) {
                    $loc0 = str_replace($cfg['stream_lighttpd_url'], $cfg['media_files_dir'], $v[0]);
                    $loc1 = str_replace($cfg['stream_lighttpd_url'], $cfg['media_files_dir'], $v[1]);
                    $loc2 = str_replace($cfg['stream_lighttpd_url'], $cfg['media_files_dir'], $v[2]);
                } else {
                    $loc0 = str_replace($url, $cfg['media_files_dir'], $v[0]);
                    $loc1 = str_replace($url, $cfg['media_files_dir'], $v[1]);
                    $loc2 = str_replace($url, $cfg['media_files_dir'], $v[2]);

                    if ($srv->fields['server_type'] == 's3' and $srv->fields['cf_enabled'] == 1 and $cf_signed_url == 1) {
                        if ($srv->fields['cf_dist_type'] == 'r') {
                            $v[0] = strstr($v[0], $videos->fields['usr_key']);
                            $v[1] = strstr($v[1], $videos->fields['usr_key']);
                            $v[2] = strstr($v[2], $videos->fields['usr_key']);

                            $v[0] = VbeServers::getS3SignedURL($aws_access_key_id, $aws_secret_key, $v[0], $aws_bucket, $cf_signed_expire);
                            $v[1] = VbeServers::getS3SignedURL($aws_access_key_id, $aws_secret_key, $v[1], $aws_bucket, $cf_signed_expire);
                            $v[2] = VbeServers::getS3SignedURL($aws_access_key_id, $aws_secret_key, $v[2], $aws_bucket, $cf_signed_expire);
                        } else {
                            $v[0] = VbeServers::getSignedURL($v[0], $cf_signed_expire, $cf_key_pair, $cf_key_file, 0, 0);
                            $v[1] = VbeServers::getSignedURL($v[1], $cf_signed_expire, $cf_key_pair, $cf_key_file, 0, 0);
                            $v[2] = VbeServers::getSignedURL($v[2], $cf_signed_expire, $cf_key_pair, $cf_key_file, 0, 0);
                        }
                    }
                }
                if ($hd == '') {
                    if (file_exists($loc0)) {
                        $_file[] = $v[0];
                    } else {
                        if (file_exists($loc1)) {
                            $_file[] = $v[1];
                        }
                    }
                } else {
                    if (file_exists($loc0)) {
                        $_file[] = $v[0];
                    } else {
                        if (file_exists($loc1)) {
                            $_file[] = $v[1];
                        }
                    }
                }
            }
        }
        if ($_fsrc != 'local') {
            $info        = array();
            $_p          = VPlayers::playerInit('view');
            $width       = $_p[1];
            $height      = $_p[2];
            $info['key'] = $videos->fields['file_key'];

            $p = str_replace(array('&', '<', '>'), array('&amp;', '&lt;', '&gt;'), VPlayers::playerEmbedCodes($_fsrc, $info, $width, $height));
        }

        echo $ht = "\n\t", '<url>';
        echo $ht = "\n\t\t", '<loc>' . $_loc . '</loc>';
        echo $ht = "\n\t\t", '<video:video>';
        echo $ht = "\n\t\t\t", '<video:thumbnail_loc>' . $_thumb . '</video:thumbnail_loc>';
        if ($entries['sitemap_video_src'] == 'location') {
            echo $ht = "\n\t\t\t", '<video:content_loc>' . ($hd == '' ? $_file[0] : ($_file[2] != '' ? $_file[2] : $_file[1])) . '</video:content_loc>'; //location
        } else {
            echo $ht = "\n\t\t\t", '<video:player_loc allow_embed="yes" autoplay="ap=1">' . $p . '</video:player_loc>'; //player
        }
        echo $ht = "\n\t\t\t", '<video:title>' . html_entity_decode($videos->fields['file_title'], ENT_QUOTES, 'UTF-8') . '</video:title>';
        echo $ht = "\n\t\t\t", '<video:description>' . html_entity_decode($videos->fields['file_description'], ENT_QUOTES, 'UTF-8') . '</video:description>';
        echo $ht = "\n\t\t\t", '<video:duration>' . intval($videos->fields['file_duration']) . '</video:duration>';
        echo $ht = "\n\t\t\t", '<video:rating>' . $videos->fields['file_like'] . '.' . $videos->fields['file_dislike'] . '</video:rating>';
        echo $ht = "\n\t\t\t", '<video:view_count>' . $videos->fields['file_views'] . '</video:view_count>';
        echo $ht = "\n\t\t\t", '<video:publication_date>' . date('Y-m-d', strtotime($videos->fields['upload_date'])) . '</video:publication_date>';

        $tags = explode(' ', $videos->fields['file_tags']);
        foreach ($tags as $tag) {
            echo $ht = "\n\t\t\t", '<video:tag>' . html_entity_decode($tag, ENT_QUOTES, 'UTF-8') . '</video:tag>';
        }
        echo $ht = "\n\t\t\t", '<video:category>' . $videos->fields['ct_name'] . '</video:category>';
        echo $ht = "\n\t\t\t", '<video:uploader info="' . $cfg['main_url'] . '/@' . $videos->fields['usr_user'] . '"></video:uploader>';
        echo $ht = "\n\t\t", '</video:video>';
        echo $ht = "\n\t", '</url>';
    }
    /* rebuid global sitemap */
    public function doGlobalSitemap()
    {
        require_once 'f_core/config.footer.php';
        global $cfg, $db, $href;

        if ($_POST) {
            $entries = VArrayConfig::cfgSection();

            $current_date = date('Y-m-d');
            $enc          = array();
            $index        = array();
            $mod          = array("live", "video", "short", "image", "audio", "document", "blog");

            $enc[0] = '<?xml version="1.0" encoding="UTF-8"?><?xml-stylesheet type="text/xsl" href="' . $cfg['sitemap_url'] . '/data/sitemap.xsl"?>';
            $enc[1] = '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
            $enc[2] = '</urlset>';

            exec(sprintf("rm -rf %s", $cfg['sitemap_dir'] . '/sm_global/*.xml'));

            /* global sitemap */
            ob_start();
            echo $ht = $enc[0], "\n";
            echo $ht = $enc[1], "\n";

            /* frontpage */
            $ht = $entries['sitemap_global_frontpage'] == 1 ? self::sitemap_url(array($cfg['main_url'], $current_date, 'daily', '1.0')) : null;
            /* browse video, image, audio, doc */
            if ($entries['sitemap_global_content'] == 1) {
                foreach ($mod as $name) {
                    switch ($name[0]) {
                        case "l":$hr = $href['broadcasts'];
                            break;
                        case "v":$hr = $href['videos'];
                            break;
                        case "s":$hr = $href['shorts'];
                            break;
                        case "i":$hr = $href['images'];
                            break;
                        case "a":$hr = $href['audios'];
                            break;
                        case "d":$hr = $href['documents'];
                            break;
                        case "b":$hr = $href['blogs'];
                            break;
                    }
                    $ht = ($cfg[$name . "_module"] == 1) ? self::sitemap_url(array($cfg['main_url'] . '/' . $hr, $current_date, 'daily', '0.9')) : null;
                }
            }
            /* categories */
            if ($entries['sitemap_global_categories'] == 1) {
                foreach ($mod as $name) {
                    if ($cfg[$name . "_module"] == 1) {
                        switch ($name[0]) {
                            case "l":$hr = $href['broadcasts'];
                                break;
                            case "v":$hr = $href['videos'];
                                break;
                            case "s":$hr = $href['shorts'];
                                break;
                            case "i":$hr = $href['images'];
                                break;
                            case "a":$hr = $href['audios'];
                                break;
                            case "d":$hr = $href['documents'];
                                break;
                            case "b":$hr = $href['blogs'];
                                break;
                        }

                        $categ = $db->execute(sprintf("SELECT `ct_id`, `ct_slug` FROM `db_categories` WHERE `ct_type`='%s' AND `ct_active`='1';", ($name == 'document' ? 'doc' : $name)));
                        if ($categ->fields['ct_id']) {
                            while (!$categ->EOF) {
                                $ht = self::sitemap_url(array($cfg['main_url'] . '/' . $hr . '/' . $categ->fields['ct_slug'], $current_date, 'daily', '0.9'));

                                $categ->MoveNext();
                            }
                        }
                    }
                }
            }
            /* static pages */
            if ($entries['sitemap_global_content'] == 1) {
                $fp = footerPages();
                foreach ($fp as $_fk => $_fv) {
                    $ht = self::sitemap_url(array($cfg['main_url'] . '/' . VHref::getKey("page") . '?t=' . $_fk, $current_date, 'weekly', '0.1'));
                }
            }
            echo $ht = $enc[2];
            $content = ob_get_contents();
            ob_end_clean();
            /* sitemap filename */
            $index[] = array('url' => $cfg['sitemap_url'] . '/sm_global/sitemap-general.xml', 'lastmod' => $current_date);
            $file    = $cfg['sitemap_dir'] . '/sm_global/sitemap-general.xml';
            /* write general sitemap */
            if (!VFileinfo::write($file, $content)) {
                echo $errors = 'Failed to write the general sitemap file (' . $file . ')!';
            }
            /* end global sitemap */

            /* users sitemap */
            if ($entries['sitemap_global_users'] == 1) {
                $count  = 1;
                $start  = 0;
                $amount = $entries['sitemap_global_max'];

                $sql         = "SELECT `usr_key`,`usr_user` FROM `db_accountuser` WHERE `usr_status`='1' ORDER BY `usr_id` DESC";
                $total       = $db->execute("SELECT COUNT(*) AS `total` FROM `db_accountuser` WHERE `usr_status`='1';");
                $total_users = $total->fields['total'];

                if ($total_users > 0) {
                    while ($start < $total_users) {
                        $filename = 'sitemap-users-' . $count . '.xml';
                        $users    = $db->execute($sql . ' LIMIT ' . $start . ', ' . $amount);

                        ob_start();
                        echo $ht = $enc[0], "\n";
                        echo $ht = $enc[1], "\n";
                        while (!$users->EOF) {
                            $ht = self::sitemap_url(array(VHref::channelURL(["username" => $users->fields['usr_user']]), $current_date, 'weekly', '0.3'));
                            $users->MoveNext();
                        }
                        echo $ht = $enc[2];
                        $content = ob_get_contents();
                        ob_end_clean();
                        /* sitemap filename */
                        $file    = $cfg['sitemap_dir'] . '/sm_global/' . $filename;
                        $index[] = array('url' => $cfg['sitemap_url'] . '/sm_global/' . $filename, 'lastmod' => $current_date);
                        /* write users sitemap */
                        if (!VFileinfo::write($file, $content)) {
                            echo $errors = 'Failed to write the users sitemap file (' . $file . ')!';
                        }
                        $start = ($start + $amount);
                        ++$count;
                    }
                }
            } //end users sitemap

            /* video, image, audio, doc sitemap */
            foreach ($mod as $name) {
                if ($cfg[$name . "_module"] == 1 and $entries["sitemap_global_" . $name] == 1) {
                    $count  = 1;
                    $start  = 0;
                    $amount = $entries['sitemap_global_max'];
                    $tbl    = ($name == 'document' ? 'doc' : $name);

                    $sql = sprintf("SELECT
                            A.`file_key`, A.`file_title`
                            FROM `db_%sfiles` A
                            WHERE
                            A.`privacy`='public' AND
                            A.`approved`='1' AND
                            A.`deleted`='0' AND
                            A.`active`='1'
                            ORDER BY A.`db_id` DESC", $tbl);
                    $total       = $db->execute(sprintf("SELECT COUNT(*) AS `total` FROM `db_%sfiles` WHERE `privacy`='public' AND `approved`='1' AND `deleted`='0' AND `active`='1';", ($name == 'document' ? 'doc' : $name)));
                    $total_files = $total->fields['total'];

                    if ($total_files > 0) {
                        while ($start < $total_files) {
                            $filename = 'sitemap-' . $name . '-' . $count . '.xml';
                            $files    = $db->execute($sql . ' LIMIT ' . $start . ', ' . $amount);

                            ob_start();
                            echo $ht = $enc[0], "\n";
                            echo $ht = $enc[1], "\n";
                            while (!$files->EOF) {
                                $ht = self::sitemap_url(array($cfg['main_url'] . '/' . VGenerate::fileHref($name[0], $files->fields['file_key'], $files->fields['file_title']), $current_date, 'weekly', '0.4'));
                                $files->MoveNext();
                            }
                            echo $ht = $enc[2];
                            $content = ob_get_contents();
                            ob_end_clean();
                            /* sitemap filename */
                            $file    = $cfg['sitemap_dir'] . '/sm_global/' . $filename;
                            $index[] = array('url' => $cfg['sitemap_url'] . '/sm_global/' . $filename, 'lastmod' => $current_date);
                            /* write users sitemap */
                            if (!VFileinfo::write($file, $content)) {
                                $errors = 'Failed to write the ' . $name . ' sitemap file (' . $file . ')!';
                            }
                            $start = ($start + $amount);
                            ++$count;
                        }
                    }
                }
            } //end foreach

            /* video, image, audio, doc, blog playlists sitemap */
            foreach ($mod as $name) {
                if ($cfg[$name . "_module"] == 1 and $entries["sitemap_global_" . $name . "_pl"] == 1) {
                    $count  = 1;
                    $start  = 0;
                    $amount = $entries['sitemap_global_max'];

                    $sql      = sprintf("SELECT `pl_key` FROM `db_%splaylists` WHERE `pl_privacy`='public' AND `pl_active`='1' ORDER BY `pl_id` DESC", ($name == 'document' ? 'doc' : $name));
                    $total    = $db->execute(sprintf("SELECT COUNT(*) AS `total` FROM `db_%splaylists` WHERE `pl_privacy`='public' AND `pl_active`='1';", ($name == 'document' ? 'doc' : $name)));
                    $total_pl = $total->fields['total'];

                    if ($total_pl > 0) {
                        while ($start < $total_pl) {
                            $filename = 'sitemap-playlist-' . $name . '-' . $count . '.xml';
                            $pls      = $db->execute($sql . ' LIMIT ' . $start . ', ' . $amount);

                            ob_start();
                            echo $ht = $enc[0], "\n";
                            echo $ht = $enc[1], "\n";
                            while (!$pls->EOF) {
                                $ht = self::sitemap_url(array($cfg['main_url'] . '/' . VHref::getKey("playlist") . '?' . $name[0] . '=' . $pls->fields['pl_key'], $current_date, 'monthly', '0.2'));
                                $pls->MoveNext();
                            }
                            echo $ht = $enc[2];
                            $content = ob_get_contents();
                            ob_end_clean();
                            /* sitemap filename */
                            $file    = $cfg['sitemap_dir'] . '/sm_global/' . $filename;
                            $index[] = array('url' => $cfg['sitemap_url'] . '/sm_global/' . $filename, 'lastmod' => $current_date);
                            /* write users sitemap */
                            if (!VFileinfo::write($file, $content)) {
                                $errors = 'Failed to write the ' . $name . ' playlist sitemap file (' . $file . ')!';
                            }
                            $start = ($start + $amount);
                            ++$count;
                        }
                    }
                }
            } //end foreach

            /* sitemap index */
            ob_start();
            echo $ht = '<?xml version="1.0" encoding="UTF-8"?>', "\n";
            echo $ht = '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', "\n";
            foreach ($index as $key) {
                echo $ht = "\t", '<sitemap>', "\n";
                echo $ht = "\t\t", '<loc>' . $key['url'] . '</loc>', "\n";
                echo $ht = "\t\t", '<lastmod>' . $key['lastmod'] . '</lastmod>', "\n";
                echo $ht = "\t", '</sitemap>', "\n";
            }
            echo $ht = '</sitemapindex>';

            $content = ob_get_contents();
            ob_end_clean();

            $file = $cfg['sitemap_dir'] . '/sm_global/sitemap.xml';
            if (!VFileinfo::write($file, $content)) {
                $errors = 'Failed to write sitemap index file (' . $file . ')!';
            }
        }
    }
    /* sitemap url entry */
    public function sitemap_url($p)
    {
        echo $ht = "\t", '<url>', "\n\t\t";
        echo $ht = '<loc>' . $p[0] . '</loc>', "\n\t\t";
        echo $ht = '<lastmod>' . $p[1] . '</lastmod>', "\n\t\t";
        echo $ht = '<changefreq>' . $p[2] . '</changefreq>', "\n\t\t";
        echo $ht = '<priority>' . $p[3] . '</priority>', "\n\t";
        echo $ht = '</url>', "\n";
    }
}
