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

$main_dir = realpath(dirname(__FILE__) . '/../../../');

set_time_limit(0);
//ignore_user_abort(1);
set_include_path($main_dir);
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

include_once 'f_core/config.core.php';

if (empty($_SERVER["argv"][1])) {
    exit;
}

$sitemap_basename = 'basename';

$limit = null;
$vidid = null;
$p     = null;
$mp4   = null;
$type  = $class_filter->clr_str($_SERVER["argv"][1]);

if ($type != 'video' and $type != 'short' and $type != 'image') {
    exit;
}

if (isset($_GET['limit'])) {
    $limit = preg_replace("/[^0-9,]/", "", $_GET['limit']);
    if ($limit) {
        $limit = "LIMIT $limit";
    }

}

if (isset($_GET['vid'])) {
    $vidid = preg_replace("/[^0-9]/", "", $_GET['vid']);
    if ($vidid) {
        $vidid = "AND vid_id > $vidid";
    }

}

$skey  = "last_" . $type . "_sitemap";
$cfg[] = $class_database->getConfigurations($skey . ',stream_method,stream_server,stream_lighttpd_secure');
$lvid  = $cfg[$skey];
//$lvid    = 0;//reset, start from id0
$vidid = "AND A.`db_id` > $lvid";

$limit = 'LIMIT 1000';

$sql = sprintf("SELECT
            A.`db_id`, A.`file_key`, A.`upload_date`, A.`file_duration`, A.`file_views`, A.`upload_server`, A.`thumb_server`, A.`embed_src`, A.`embed_key`, A.`file_hd`, A.`thumb_cache`,
            A.`file_title`, A.`file_description`, A.`file_tags`,
            D.`usr_key`
            FROM `db_%sfiles` A, `db_accountuser` D WHERE
            A.`usr_id`=D.`usr_id` AND
            A.`active`='1' AND A.`deleted`='0' AND A.`approved`='1'
            %s ORDER BY A.`db_id` ASC %s;", $type, $vidid, $limit);

$res = $db->execute($sql);

//exit;

$SitemapNode = new VSitemap($cfg['sitemap_dir'] . '/sm_' . $type, $sitemap_basename, $type);

if ($res->fields["file_key"]) {
    while (!$res->EOF) {
        $db_id       = $res->fields["db_id"];
        $vid_id      = $res->fields["file_key"];
        $usr_id      = $res->fields["usr_key"];
        $tmb_srv     = $res->fields["thumb_server"];
        $vid_srv     = $res->fields["upload_server"];
        $db_title    = $res->fields["file_title"];
        $_fsrc       = $res->fields["embed_src"];
        $_furl       = $res->fields["embed_key"];
        $hd          = $res->fields["file_hd"];
        $thumb_cache = $res->fields["thumb_cache"];
        $thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;

        if ($hd == 0) {
            $hd = '';
        }

        $title       = html_convert_entities(htmlspecialchars($db_title));
        $description = html_convert_entities(htmlspecialchars($res->fields["file_description"]));
        $screenshot  = VGenerate::thumbSigned($type, $vid_id, array($usr_id, $thumb_cache), (3600 * 24 * 7), 0);
        $duration    = $res->fields["file_duration"];
        $view_count  = $res->fields["file_views"];
        $tags        = explode(" ", $res->fields["file_tags"]);
        $date        = date(DATE_ATOM, strtotime($res->fields["upload_date"]));

        $url  = VGenerate::fileURL($type, $vid_id, 'upload');
        $link = $cfg["main_url"] . '/' . VGenerate::fileHref($type[0], $vid_id, $db_title);

        $_src = VPlayers::fileSources($type, $usr_id, $vid_id);

        $sql = sprintf("SELECT
                                                A.`server_type`, A.`lighttpd_url`, A.`lighttpd_secdownload`, A.`lighttpd_prefix`, A.`lighttpd_key`, A.`cf_enabled`, A.`cf_dist_type`,
                                                A.`cf_signed_url`, A.`cf_signed_expire`, A.`cf_key_pair`, A.`cf_key_file`, A.`s3_bucketname`, A.`s3_accesskey`, A.`s3_secretkey`,
                                                B.`upload_server`
                                                FROM
                                                `db_servers` A, `db_%sfiles` B
                                                WHERE
                                                B.`file_key`='%s' AND
                                                B.`upload_server` > '0' AND
                                                A.`server_id`=B.`upload_server` LIMIT 1;", $type, $vid_id);
        $srv = $db->execute($sql);

        $cf_signed_url     = $srv->fields["cf_signed_url"];
        $cf_signed_expire  = 3600 * 24 * 2; //$srv->fields["cf_signed_expire"];
        $cf_key_pair       = $srv->fields["cf_key_pair"];
        $cf_key_file       = $srv->fields["cf_key_file"];
        $aws_access_key_id = $srv->fields["s3_accesskey"];
        $aws_secret_key    = $srv->fields["s3_secretkey"];
        $aws_bucket        = $srv->fields["s3_bucketname"];

        if (count($_src) > 0) {
            $_file = array();
            foreach ($_src as $k => $v) {
                if (($srv->fields["lighttpd_url"] != '' and $srv->fields["lighttpd_secdownload"] == 1) or ($cfg["stream_method"] == 2 and $cfg["stream_server"] == 'lighttpd' and $cfg["stream_lighttpd_secure"] == 1)) {
                    $l0   = explode("/", $v[0]);
                    $loc0 = $cfg["media_files_dir"] . '/' . $l0[6] . '/' . $l0[7] . '/' . $l0[8];
                    $l1   = explode("/", $v[1]);
                    $loc1 = $cfg["media_files_dir"] . '/' . $l1[6] . '/' . $l1[7] . '/' . $l1[8];
                    $l2   = explode("/", $v[2]);
                    $loc2 = $cfg["media_files_dir"] . '/' . $l2[6] . '/' . $l2[7] . '/' . $l2[8];
                } elseif ($cfg["stream_method"] == 2 and $cfg["stream_server"] == 'lighttpd' and $cfg["stream_lighttpd_secure"] == 0) {
                    $loc0 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[0]);
                    $loc1 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[1]);
                    $loc2 = str_replace($cfg["stream_lighttpd_url"], $cfg["media_files_dir"], $v[2]);
                } else {
                    $loc0 = str_replace($url, $cfg["media_files_dir"], $v[0]);
                    $loc1 = str_replace($url, $cfg["media_files_dir"], $v[1]);
                    $loc2 = str_replace($url, $cfg["media_files_dir"], $v[2]);

                    if ($srv->fields["server_type"] == 's3' and $srv->fields["cf_enabled"] == 1 and $cf_signed_url == 1) {
                        if ($srv->fields["cf_dist_type"] == 'r') {
                            $v[0] = strstr($v[0], $videos->fields["usr_key"]);
                            $v[1] = strstr($v[1], $videos->fields["usr_key"]);
                            $v[2] = strstr($v[2], $videos->fields["usr_key"]);

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
            $info["key"] = $vid_id;

            switch ($_fsrc) {
                case "youtube":$_url = 'https://www.youtube.com/embed/' . $_furl;
                    break;
                case "vimeo":$_url = 'https://player.vimeo.com/video/' . $_furl;
                    break;
                case "dailymotion":$_url = 'https://www.dailymotion.com/embed/video/' . $_furl;
                    break;
            }
        }

        $config = array(
            'type'   => 'url',
            'params' => array(
                'loc'   => $link,
                'video' => array(
                    'title'                                            => $title,
                    'thumbnail_loc'                                    => $screenshot,
                    'description'                                      => str_replace(array("\r", "\n"), array("", ""), $description),
                    ($_fsrc != 'local' ? 'player_loc' : 'content_loc') => ($_fsrc != 'local' ? $_url : ($hd == '' ? $_file[0] : ($_file[2] != '' ? $_file[2] : $_file[1]))),
                    'publication_date'                                 => $date,
                    'duration'                                         => $duration,
                    'view_count'                                       => $view_count,
                ),
            ),
        );

        foreach ($tags as $i => $tag) {
            $final_tag = trim(html_convert_entities(htmlspecialchars($tag)));

            $config['params']['video']['tag' . $i] = str_replace(array("\r", "\n"), array("", ""), $final_tag);
        }

        $SitemapNode->add_node($config);

        $db->execute(sprintf("UPDATE `db_settings` SET `cfg_data`='%s' WHERE `cfg_name`='%s' LIMIT 1;", $db_id, $skey));

        $res->MoveNext();
    } //endwhile
} //endif

function html_convert_entities($string)
{
    return preg_replace_callback('/&([a-zA-Z][a-zA-Z0-9]+);/',
        'convert_entity', $string);
}

/* Swap HTML named entity with its numeric equivalent. If the entity
 * isn't in the lookup table, this function returns a blank, which
 * destroys the character in the output - this is probably the
 * desired behaviour when producing XML. */
function convert_entity($matches)
{
    static $table = array('quot' => '&#34;',
        'amp'                        => '&#38;',
        'lt'                         => '&#60;',
        'gt'                         => '&#62;',
        'OElig'                      => '&#338;',
        'oelig'                      => '&#339;',
        'Scaron'                     => '&#352;',
        'scaron'                     => '&#353;',
        'Yuml'                       => '&#376;',
        'circ'                       => '&#710;',
        'tilde'                      => '&#732;',
        'ensp'                       => '&#8194;',
        'emsp'                       => '&#8195;',
        'thinsp'                     => '&#8201;',
        'zwnj'                       => '&#8204;',
        'zwj'                        => '&#8205;',
        'lrm'                        => '&#8206;',
        'rlm'                        => '&#8207;',
        'ndash'                      => '&#8211;',
        'mdash'                      => '&#8212;',
        'lsquo'                      => '&#8216;',
        'rsquo'                      => '&#8217;',
        'sbquo'                      => '&#8218;',
        'ldquo'                      => '&#8220;',
        'rdquo'                      => '&#8221;',
        'bdquo'                      => '&#8222;',
        'dagger'                     => '&#8224;',
        'Dagger'                     => '&#8225;',
        'permil'                     => '&#8240;',
        'lsaquo'                     => '&#8249;',
        'rsaquo'                     => '&#8250;',
        'euro'                       => '&#8364;',
        'fnof'                       => '&#402;',
        'Alpha'                      => '&#913;',
        'Beta'                       => '&#914;',
        'Gamma'                      => '&#915;',
        'Delta'                      => '&#916;',
        'Epsilon'                    => '&#917;',
        'Zeta'                       => '&#918;',
        'Eta'                        => '&#919;',
        'Theta'                      => '&#920;',
        'Iota'                       => '&#921;',
        'Kappa'                      => '&#922;',
        'Lambda'                     => '&#923;',
        'Mu'                         => '&#924;',
        'Nu'                         => '&#925;',
        'Xi'                         => '&#926;',
        'Omicron'                    => '&#927;',
        'Pi'                         => '&#928;',
        'Rho'                        => '&#929;',
        'Sigma'                      => '&#931;',
        'Tau'                        => '&#932;',
        'Upsilon'                    => '&#933;',
        'Phi'                        => '&#934;',
        'Chi'                        => '&#935;',
        'Psi'                        => '&#936;',
        'Omega'                      => '&#937;',
        'alpha'                      => '&#945;',
        'beta'                       => '&#946;',
        'gamma'                      => '&#947;',
        'delta'                      => '&#948;',
        'epsilon'                    => '&#949;',
        'zeta'                       => '&#950;',
        'eta'                        => '&#951;',
        'theta'                      => '&#952;',
        'iota'                       => '&#953;',
        'kappa'                      => '&#954;',
        'lambda'                     => '&#955;',
        'mu'                         => '&#956;',
        'nu'                         => '&#957;',
        'xi'                         => '&#958;',
        'omicron'                    => '&#959;',
        'pi'                         => '&#960;',
        'rho'                        => '&#961;',
        'sigmaf'                     => '&#962;',
        'sigma'                      => '&#963;',
        'tau'                        => '&#964;',
        'upsilon'                    => '&#965;',
        'phi'                        => '&#966;',
        'chi'                        => '&#967;',
        'psi'                        => '&#968;',
        'omega'                      => '&#969;',
        'thetasym'                   => '&#977;',
        'upsih'                      => '&#978;',
        'piv'                        => '&#982;',
        'bull'                       => '&#8226;',
        'hellip'                     => '&#8230;',
        'prime'                      => '&#8242;',
        'Prime'                      => '&#8243;',
        'oline'                      => '&#8254;',
        'frasl'                      => '&#8260;',
        'weierp'                     => '&#8472;',
        'image'                      => '&#8465;',
        'real'                       => '&#8476;',
        'trade'                      => '&#8482;',
        'alefsym'                    => '&#8501;',
        'larr'                       => '&#8592;',
        'uarr'                       => '&#8593;',
        'rarr'                       => '&#8594;',
        'darr'                       => '&#8595;',
        'harr'                       => '&#8596;',
        'crarr'                      => '&#8629;',
        'lArr'                       => '&#8656;',
        'uArr'                       => '&#8657;',
        'rArr'                       => '&#8658;',
        'dArr'                       => '&#8659;',
        'hArr'                       => '&#8660;',
        'forall'                     => '&#8704;',
        'part'                       => '&#8706;',
        'exist'                      => '&#8707;',
        'empty'                      => '&#8709;',
        'nabla'                      => '&#8711;',
        'isin'                       => '&#8712;',
        'notin'                      => '&#8713;',
        'ni'                         => '&#8715;',
        'prod'                       => '&#8719;',
        'sum'                        => '&#8721;',
        'minus'                      => '&#8722;',
        'lowast'                     => '&#8727;',
        'radic'                      => '&#8730;',
        'prop'                       => '&#8733;',
        'infin'                      => '&#8734;',
        'ang'                        => '&#8736;',
        'and'                        => '&#8743;',
        'or'                         => '&#8744;',
        'cap'                        => '&#8745;',
        'cup'                        => '&#8746;',
        'int'                        => '&#8747;',
        'there4'                     => '&#8756;',
        'sim'                        => '&#8764;',
        'cong'                       => '&#8773;',
        'asymp'                      => '&#8776;',
        'ne'                         => '&#8800;',
        'equiv'                      => '&#8801;',
        'le'                         => '&#8804;',
        'ge'                         => '&#8805;',
        'sub'                        => '&#8834;',
        'sup'                        => '&#8835;',
        'nsub'                       => '&#8836;',
        'sube'                       => '&#8838;',
        'supe'                       => '&#8839;',
        'oplus'                      => '&#8853;',
        'otimes'                     => '&#8855;',
        'perp'                       => '&#8869;',
        'sdot'                       => '&#8901;',
        'lceil'                      => '&#8968;',
        'rceil'                      => '&#8969;',
        'lfloor'                     => '&#8970;',
        'rfloor'                     => '&#8971;',
        'lang'                       => '&#9001;',
        'rang'                       => '&#9002;',
        'loz'                        => '&#9674;',
        'spades'                     => '&#9824;',
        'clubs'                      => '&#9827;',
        'hearts'                     => '&#9829;',
        'diams'                      => '&#9830;',
        'nbsp'                       => '&#160;',
        'iexcl'                      => '&#161;',
        'cent'                       => '&#162;',
        'pound'                      => '&#163;',
        'curren'                     => '&#164;',
        'yen'                        => '&#165;',
        'brvbar'                     => '&#166;',
        'sect'                       => '&#167;',
        'uml'                        => '&#168;',
        'copy'                       => '&#169;',
        'ordf'                       => '&#170;',
        'laquo'                      => '&#171;',
        'not'                        => '&#172;',
        'shy'                        => '&#173;',
        'reg'                        => '&#174;',
        'macr'                       => '&#175;',
        'deg'                        => '&#176;',
        'plusmn'                     => '&#177;',
        'sup2'                       => '&#178;',
        'sup3'                       => '&#179;',
        'acute'                      => '&#180;',
        'micro'                      => '&#181;',
        'para'                       => '&#182;',
        'middot'                     => '&#183;',
        'cedil'                      => '&#184;',
        'sup1'                       => '&#185;',
        'ordm'                       => '&#186;',
        'raquo'                      => '&#187;',
        'frac14'                     => '&#188;',
        'frac12'                     => '&#189;',
        'frac34'                     => '&#190;',
        'iquest'                     => '&#191;',
        'Agrave'                     => '&#192;',
        'Aacute'                     => '&#193;',
        'Acirc'                      => '&#194;',
        'Atilde'                     => '&#195;',
        'Auml'                       => '&#196;',
        'Aring'                      => '&#197;',
        'AElig'                      => '&#198;',
        'Ccedil'                     => '&#199;',
        'Egrave'                     => '&#200;',
        'Eacute'                     => '&#201;',
        'Ecirc'                      => '&#202;',
        'Euml'                       => '&#203;',
        'Igrave'                     => '&#204;',
        'Iacute'                     => '&#205;',
        'Icirc'                      => '&#206;',
        'Iuml'                       => '&#207;',
        'ETH'                        => '&#208;',
        'Ntilde'                     => '&#209;',
        'Ograve'                     => '&#210;',
        'Oacute'                     => '&#211;',
        'Ocirc'                      => '&#212;',
        'Otilde'                     => '&#213;',
        'Ouml'                       => '&#214;',
        'times'                      => '&#215;',
        'Oslash'                     => '&#216;',
        'Ugrave'                     => '&#217;',
        'Uacute'                     => '&#218;',
        'Ucirc'                      => '&#219;',
        'Uuml'                       => '&#220;',
        'Yacute'                     => '&#221;',
        'THORN'                      => '&#222;',
        'szlig'                      => '&#223;',
        'agrave'                     => '&#224;',
        'aacute'                     => '&#225;',
        'acirc'                      => '&#226;',
        'atilde'                     => '&#227;',
        'auml'                       => '&#228;',
        'aring'                      => '&#229;',
        'aelig'                      => '&#230;',
        'ccedil'                     => '&#231;',
        'egrave'                     => '&#232;',
        'eacute'                     => '&#233;',
        'ecirc'                      => '&#234;',
        'euml'                       => '&#235;',
        'igrave'                     => '&#236;',
        'iacute'                     => '&#237;',
        'icirc'                      => '&#238;',
        'iuml'                       => '&#239;',
        'eth'                        => '&#240;',
        'ntilde'                     => '&#241;',
        'ograve'                     => '&#242;',
        'oacute'                     => '&#243;',
        'ocirc'                      => '&#244;',
        'otilde'                     => '&#245;',
        'ouml'                       => '&#246;',
        'divide'                     => '&#247;',
        'oslash'                     => '&#248;',
        'ugrave'                     => '&#249;',
        'uacute'                     => '&#250;',
        'ucirc'                      => '&#251;',
        'uuml'                       => '&#252;',
        'yacute'                     => '&#253;',
        'thorn'                      => '&#254;',
        'yuml'                       => '&#255;',

    );
    // Entity not found? Destroy it.
    return isset($table[$matches[1]]) ? $table[$matches[1]] : '';
}
