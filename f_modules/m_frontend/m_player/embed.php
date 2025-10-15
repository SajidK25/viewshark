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

include_once 'f_core/config.core.php';
include_once 'f_core/f_classes/class.membership.php';

$type     = isset($_GET["a"]) ? 'audio' : (isset($_GET["l"]) ? 'live' : (isset($_GET["s"]) ? 'short' : 'video'));
$file_key = $class_filter->clr_str($_GET[$type[0]]);
$pl_key   = $class_filter->clr_str($_GET["p"]);
$cfg[]    = $class_database->getConfigurations('video_player,audio_player,affiliate_tracking_id');
$_cfg     = unserialize($class_database->singleFieldValue('db_fileplayers', 'db_config', 'db_name', $cfg["video_player"] . '_embed'));

if ($pl_key) {
    $pl       = unserialize($class_database->singleFieldValue('db_' . $type . 'playlists', 'pl_files', 'pl_key', $pl_key));
    $file_key = $pl[0];
}

$u = $db->execute(sprintf("SELECT
                    A.`usr_key`, A.`usr_live`,
                    B.`usr_id`, B.`embed_src`,
                    B.`file_title`, B.`file_category`, B.`thumb_cache`
                    FROM
                    `db_accountuser` A, `db_%sfiles` B
                    WHERE
                    A.`usr_id`=B.`usr_id` AND
                    B.`file_key`='%s'
                    LIMIT 1;", $type, $file_key));
$usr_key     = $u->fields["usr_key"];
$channel_id  = (int) $u->fields["usr_id"];
$title       = $u->fields["file_title"];
$embed_src   = $u->fields["embed_src"];
$live        = $u->fields["usr_live"];
$thumb_cache = $u->fields["thumb_cache"];
$thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;
$mob         = VHref::isMobile();

$tmb = $cfg["media_files_url"] . '/' . $usr_key . '/t/' . $file_key . '/0' . $thumb_cache . '.jpg';

// Membership gating (scaffold): if a tier is required and viewer lacks access, show upsell instead of player
if (VMembership::enabled()) {
    $reqTier = VMembership::getRequiredTierForFile($type, $file_key);
    if ($reqTier && !VMembership::hasAccess((int) $_SESSION['USER_ID'], $channel_id, $reqTier)) {
        $join_url = $cfg['main_url'] . '/' . VHref::getKey('channel') . '/' . $usr_key . '#join';
        echo '<!DOCTYPE html><html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">'
            . '<title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title>'
            . '<style>html,body{height:100%;margin:0} .gate{display:flex;align-items:center;justify-content:center;height:100%;background:#000;color:#fff;text-align:center} .btn{display:inline-block;margin-top:12px;padding:10px 16px;background:#ff4e45;color:#fff;text-decoration:none;border-radius:4px} img{max-width:100%;opacity:.35}</style>'
            . '</head><body><div class="gate"><div><img src="' . htmlspecialchars($tmb, ENT_QUOTES, 'UTF-8') . '" alt=""/><div>Members-only video</div><a class="btn" href="' . $join_url . '" target="_top">Join to watch</a></div></div></body></html>';
        exit;
    }
}

switch ($cfg["video_player"]) {
    case "vjs":
        $tmb_url = VGenerate::thumbSigned($type, $file_key, array($usr_key, $thumb_cache));
        if ($type == 'short') {
            $_cfg["vjs_advertising"] = 0;
        }

        if ($_cfg["vjs_advertising"] == 1 and $embed_src == 'local') {
            $t   = $db->execute(sprintf("SELECT `vjs_ads` FROM `db_%sfiles` WHERE `file_key`='%s' LIMIT 1;", $type, $file_key));
            $ads = $t->fields["vjs_ads"];

            if ($ads != '') {
//found ads assigned to video
                $ar  = unserialize($ads);
                $sql = sprintf("SELECT `ad_id`, `ad_key`, `ad_client`, `ad_tag`, `ad_skip`, `ad_comp_div`, `ad_comp_id`, `ad_comp_w`, `ad_comp_h` FROM `db_vjsadentries` WHERE `ad_type`='dedicated' AND `ad_active`='1'%s AND `ad_id` IN (%s) ORDER BY RAND() LIMIT 1;", ($mob ? " AND `ad_mobile`='1'" : null), ($ar[0] > 0 ? implode(',', $ar) : 0));
                $t   = $db->execute($sql);
            } else {
                //check for category ads
                $t   = $db->execute(sprintf("SELECT `ct_ads` FROM `db_categories` WHERE `ct_id`='%s' LIMIT 1;", $res->fields["file_category"]));
                $ads = $t->fields["ct_ads"];
                if ($ads != '') {
//found ads assigned to category
                    $ar  = unserialize($ads);
                    $sql = sprintf("SELECT `ad_id`, `ad_key`, `ad_client`, `ad_tag`, `ad_skip`, `ad_comp_div`, `ad_comp_id`, `ad_comp_w`, `ad_comp_h` FROM `db_vjsadentries` WHERE `ad_type`='dedicated' AND `ad_active`='1'%s AND `ad_id` IN (%s) ORDER BY RAND() LIMIT 1;", ($mob ? " AND `ad_mobile`='1'" : null), ($ar[0] > 0 ? implode(',', $ar) : 0));
                    $t   = $db->execute($sql);
                } else {
                    //no video ads assigned/generate a random ad
                    $sql = sprintf("SELECT `ad_id`, `ad_key`, `ad_client`, `ad_tag`, `ad_skip`, `ad_comp_div`, `ad_comp_id`, `ad_comp_w`, `ad_comp_h` FROM `db_vjsadentries` WHERE `ad_type`='shared' AND `ad_active`='1'%s ORDER BY RAND() LIMIT 1;", ($mob ? " AND `ad_mobile`='1'" : null));
                    $t   = $db->execute($sql);
                }
            }

            if ($t->fields["ad_id"]) {
                $ad_client      = ($t->fields["ad_client"] == 'custom' ? 'vast' : $t->fields["ad_client"]);
                $ad_tag_url     = ($t->fields["ad_client"] == 'custom' ? $cfg['main_url'] . '/' . VHref::getKey('vast') . '?t=vjs&v=' . $t->fields["ad_key"] : $t->fields["ad_tag"]);
                $ad_skip        = ($t->fields["ad_client"] == 'custom' ? $t->fields["ad_skip"] : false);
                $ad_tag_comp    = $t->fields["ad_comp_div"];
                $ad_tag_comp_id = $t->fields["ad_comp_id"];
                $ad_tag_comp_w  = $t->fields["ad_comp_w"];
                $ad_tag_comp_h  = $t->fields["ad_comp_h"];

                $smarty->assign('ad_client', ($ad_client == 'custom' ? 'vast' : $t->fields["ad_client"]));
            }
        }
        $css       = '<link rel="stylesheet" type="text/css" href="https://vjs.zencdn.net/5.19/video-js.min.css" /><style>html, body { height: 100%; padding: 0; margin: 0; }</style>';
        $css_extra = '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/videojs/videojs-styles.min.css" />';
        $css_extra .= '<link rel="stylesheet" type="text/css" href="' . $cfg['styles_url'] . '/theme/theme.min.css" />';
        if ($_cfg["vjs_advertising"] == 1 and $embed_src == 'local') {
            if ($ad_client == 'vast' or $ad_client == 'custom') {
                $css_extra .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/videojs/videojs.vast.vpaid.min.css" />';
                $css_extra .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/videojs/vast-button.css" />';
            } elseif ($ad_client == 'ima') {
                $css_extra .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/videojs/videojs.ads.css" />';
                $css_extra .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/videojs/videojs.ima.css" />';
            } elseif ($ad_client == 'vamp') {
                $css_extra .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/videojs/videojs.ads.css" />';
                $css_extra .= '<link rel="stylesheet" type="text/css" href="' . $cfg['scripts_url'] . '/shared/videojs/vast-button.css" />';
            }
            if ($ad_tag_comp == 1) {
                $css_extra .= '<script type="text/javascript">var googletag = googletag || {};googletag.cmd = googletag.cmd || [];(function() {var gads = document.createElement("script");gads.async = true;gads.type = "text/javascript";gads.src = "//www.googletagservices.com/tag/js/gpt.js";var node = document.getElementsByTagName("script")[0];node.parentNode.insertBefore(gads, node);})();</script>';
                $css_extra .= '<script type="text/javascript">googletag.cmd.push(function() {googletag.defineSlot("' . $ad_tag_comp_id . '", [' . $ad_tag_comp_w . ', ' . $ad_tag_comp_h . '], "ima-companionDiv").addService(googletag.companionAds()).addService(googletag.pubads());googletag.companionAds().setRefreshUnfilledSlots(true);googletag.pubads().enableVideoAds();googletag.enableServices();});</script>';
            }
        }
        $player_key   = null;
        $player_style = 'width: 100%; height: 100%;';
        $player_br    = null;
        $player_cp    = null;
        $player_js    = 'https://vjs.zencdn.net/5.19/video.min.js';

        $player_js_extra = '<script type="text/javascript">var current_url = "' . $cfg["main_url"] . '/"; var logo="' . $_cfg["vjs_logo_file"] . '"; var logohref="' . $cfg["main_url"] . '/' . VGenerate::fileHref($type[0], $file_key, $title) . '"</script>';
        if ($_cfg["vjs_advertising"] == 1) {
            $player_js_extra .= '<script type="text/javascript">var fk="' . $file_key . '";var ad_skip="' . $ad_skip . '";var em=1;var ad_client="' . ($ad_client == 'custom' ? 'vast' : $ad_client) . '";var adTagUrl="' . $ad_tag_url . '";var compjs="' . ($mob ? 'ads.mob' : ($ad_tag_comp == 1 ? 'ads.comp' : 'ads')) . '";</script>';
            if ($ad_client == 'ima' and $embed_src == 'local') {
                $player_js_extra .= '<script type="text/javascript" src="https://imasdk.googleapis.com/js/sdkloader/ima3.js"></script>';
                $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/videojs.ads.js"></script>';
                $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/videojs.ima.js"></script>';
            } elseif ($ad_client == 'vamp' and $embed_src == 'local') {
                $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/videojs.ads.js"></script>';
                $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/vast-client.js"></script>';
                $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/vmap-client.js"></script>';
                $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/videojs-ad-scheduler.js"></script>';
                $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/ads.vmap.js"></script>';
            } elseif (($ad_client == 'vast' or $ad_client == 'custom') and $embed_src == 'local') {
                $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/videojs_5.vast.vpaid.min.js"></script>';
                $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/ads.vast.js"></script>';
            }
        }
        $player_js_extra .= '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/videojs-scripts.min.js"></script>';
        $player_js_extra .= $live == 1 ? '<script type="text/javascript" src="' . $cfg['scripts_url'] . '/shared/videojs/videojs-hlsjs-plugin.js"></script>' : null;
        $player_js_extra .= $live == 1 ? '<script type="text/javascript">$(window).on("unload", function(){var fd = new FormData();fd.append("c", "' . $file_key . '");navigator.sendBeacon("' . $cfg["main_url"] . '/' . VHref::getKey("viewers") . '?c=' . $file_key . '", fd);});</script>' : null;

        $css_extra .= '<style>
.vjs-has-started:not(.vjs-paused) #play,.vjs-big-play-button{display:none !important}.play-btn{width:40px;height:40px;background:radial-gradient(#06a2cb 50%,rgba(255,255,255,0.15) 52%);border-radius:50%;display:inline-block;margin:auto;position:absolute;overflow:hidden;top:0;right:0;bottom:0;left:0;z-index:2}.play-btn::after{content:\'\';position:absolute;left:50%;top:50%;-webkit-transform:translateX(-40%) translateY(-50%);transform:translateX(-40%) translateY(-50%);width:0;height:0;border-top:8px solid transparent;border-bottom:8px solid transparent;border-left:15px solid #fff;z-index:100;-webkit-transition:all 400ms cubic-bezier(0.55,0.055,0.675,0.19);transition:all 400ms cubic-bezier(0.55,0.055,0.675,0.19)}.play-btn:before{content:\'\';position:absolute;width:50px;height:50px;-webkit-animation-delay:0s;animation-delay:0s;-webkit-animation:pulsate1 2s;animation:pulsate1 2s;-webkit-animation-direction:forwards;animation-direction:forwards;-webkit-animation-iteration-count:infinite;animation-iteration-count:infinite;-webkit-animation-timing-function:steps;animation-timing-function:steps;opacity:9;border-radius:50%;border:2px solid #7abbec;top:-12%;left:-12%;background:solid #7abbec}.play-btn:hover::after{border-left:15px solid #333;-webkit-transform:scale(20);transform:scale(20)}.play-btn:hover::before{content:\'\';position:absolute;left:50%;top:50%;-webkit-transform:translateX(-40%) translateY(-50%);transform:translateX(-40%) translateY(-50%);width:0;height:0;border:0;border-top:10px solid transparent;border-bottom:10px solid transparent;border-left:20px solid #06a2cb;z-index:200;-webkit-animation:none;animation:none;border-radius:0}@-webkit-keyframes pulsate1{0%{-webkit-transform:scale(0.6,0.6);transform:scale(0.6,0.6);opacity:1}100%{-webkit-transform:scale(1,1);transform:scale(1,1);opacity:0}}@keyframes pulsate1{0%{-webkit-transform:scale(0.6,0.6);transform:scale(0.6,0.6);opacity:1}100%{-webkit-transform:scale(1,1);transform:scale(1,1);opacity:0}}@-webkit-keyframes pulsate2{0%{-webkit-transform:scale(0.1,0.1);transform:scale(0.1,0.1);opacity:1}100%{-webkit-transform:scale(1,1);transform:scale(1,1);opacity:0}}@-webkit-keyframes spin{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}@keyframes spin{to{-webkit-transform:rotate(360deg);transform:rotate(360deg)}}.stroke-dotted{opacity:0;stroke-dasharray:4,5;stroke-width:1px;-webkit-transform-origin:50% 50%;transform-origin:50% 50%;-webkit-animation:spin 4s infinite linear;animation:spin 4s infinite linear;-webkit-transition:opacity 1s ease,stroke-width 1s ease;transition:opacity 1s ease,stroke-width 1s ease}.stroke-solid{stroke-dashoffset:0;stroke-dashArray:300;stroke-width:4px;-webkit-transition:stroke-dashoffset 1s ease,opacity 1s ease;transition:stroke-dashoffset 1s ease,opacity 1s ease}.icon{-webkit-transform-origin:50% 50%;transform-origin:50% 50%;-webkit-transition:-webkit-transform 200ms ease-out;transition:-webkit-transform 200ms ease-out;transition:transform 200ms ease-out;transition:transform 200ms ease-out,-webkit-transform 200ms ease-out}#play:hover .stroke-dotted{stroke-width:4px;opacity:1}#play:hover .stroke-solid{opacity:0;stroke-dashoffset:300}#play:hover .icon{-webkit-transform:scale(1.05);transform:scale(1.05)}#play{cursor:pointer;position:absolute;top:50%;left:50%;-webkit-transform:translateY(-50%) translateX(-50%);transform:translateY(-50%) translateX(-50%)}@font-face{font-family:\'icomoonmlm\';src:url(\'../fonts/icomoonmlm.eot\');src:url(\'../fonts/icomoonmlm.eot?#iefix\') format(\'embedded-opentype\'),url(\'../fonts/icomoonmlm.woff\') format(\'woff\'),url(\'../fonts/icomoonmlm.ttf\') format(\'truetype\'),url(\'../fonts/icomoonmlm.svg#icomoon\') format(\'svg\');font-weight:normal;font-style:normal}
.video-js .vjs-progress-control,.vjs-default-skin .vjs-progress-control{height:10px;bottom:36px;background-color:transparent}
.video-js .vjs-control-bar,.vjs-default-skin .vjs-control-bar{background-color:transparent}
.video-js .vjs-progress-control .vjs-slider,.vjs-default-skin .vjs-progress-control .vjs-slider{height:3px}
.video-js .vjs-progress-holder .vjs-play-progress,.video-js .vjs-progress-holder .vjs-load-progress,.video-js .vjs-progress-holder .vjs-load-progress div,.vjs-default-skin .vjs-progress-holder .vjs-play-progress,.vjs-default-skin .vjs-progress-holder .vjs-load-progress,.vjs-default-skin .vjs-progress-holder .vjs-load-progress div{height:3px}
.video-js .vjs-play-progress:before,.vjs-default-skin .vjs-play-progress:before{height:3px}
.vjs-progress-control:hover .vjs-play-progress:before{height:12px}
.video-js button, .video-js .vjs-menu-button{color:#ddd !important}
.video-js button:hover, .video-js .vjs-menu-button:hover{color:#fff !important}
.video-js .vjs-time-control{font-family:"Roboto", Arial;font-size:13px}
.video-js .vjs-fullscreen-control,.vjs-default-skin .vjs-fullscreen-control{margin-right:10px;margin-left:5px}
.vjs-resolution-button .vjs-resolution-button-label,.video-js .vjs-playback-rate .vjs-playback-rate-value,.vjs-default-skin .vjs-playback-rate .vjs-playback-rate-value{line-height:40px;font-family:"Roboto", Arial;font-size:13px;font-weight:500}
.vjs-resolution-button{margin-left:5px !important}
.video-js .vjs-menu, .vjs-default-skin .vjs-menu{bottom:10px;z-index:9999}
.vjs-resolution-button .vjs-menu li{font-family:"Roboto",Arial;font-size:12px}
.vjs-error .vjs-error-display:before,.vjs-menu .vjs-menu-content, .vjs-no-js{font-family:"Roboto",Arial}
.vjs-menu li{padding:0.5em 0}
.vjs-volume-menu-button:hover + .vjs-current-time{left:170px;transition:all .3s;-webkit-transition:all .3s}
.vjs-volume-menu-button:hover + .vjs-current-time + .vjs-time-divider{left:202px;transition:all .3s;-webkit-transition:all .3s}
.vjs-volume-menu-button:hover + .vjs-current-time + .vjs-time-divider + .vjs-duration{left:214px;transition:all .3s;-webkit-transition:all .3s}
.pv-text{position:absolute;width:100%;bottom:75px;text-align:center;display:none}
.pv-text span{position:relative;font-size:14px;padding:15px 25px;border-radius:5px;border:1px solid #666}
.pv-text i{position:absolute;font-size:8px;right:5px;top:7px;cursor:pointer}
.vjs-user-inactive .pv-text{display:none !important}
.vjs-paused.vjs-user-inactive .pv-text{display:block !important}
.vjs-hd{padding-top: 56.25%;width: 100%;position: relative;}
.live-embed{position:absolute;top:0}
</style>
    ';
        break;
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="robots" content="noindex, nofollow">
    <title><?php echo $title; ?></title>
<?php
echo $css;
if (isset($css_extra)) {
    echo $css_extra;
}
?>
    <script type="text/javascript" src="<?php echo $cfg['javascript_url']; ?>/jquery.min.js"></script>
    <script type="text/javascript">jQuery.migrateMute=true;</script>
    <script type="text/javascript">if(navigator.platform==='iPad'||navigator.platform==='iPhone'||navigator.platform==='iPod'){WebFont.load({google:{families:['Roboto:300,400,500,600,700']}});}</script>
    <script type="text/javascript" src="<?php echo $player_js; ?>"></script>
    <script type="text/javascript"><?php echo $player_key; ?></script>
<?php
if (isset($player_js_extra)) {
    echo $player_js_extra;
}
?>
</head>
<body style="overflow: hidden;" class="<?php echo ($mob ? 'touch' : ''); ?>">
    <div style="<?php echo $player_style; ?>" id="embed-player-<?php echo $file_key; ?>"<?php echo $player_cp;
echo $divdata; ?>><?php echo $player_br; ?><?php echo $player_src; ?>
    <?php
if (($_cfg["vjs_advertising"] == 1 and !VHref::isMobile()) or ($_cfg["vjs_advertising"] == 1 and VHref::isMobile() and $embed_src == 'local' and $ad_client == 'vast') or ($_cfg["vjs_advertising"] == 1 and VHref::isMobile() and $embed_src != 'local') or ($_cfg["vjs_advertising"] == 0 and (!VHref::isMobile() or (VHref::isMobile() and ($embed_src == 'local' or $embed_src == 'youtube'))))) {
    $phtml = (($type[0] == 'v' or $type[0] == 'l' or $type[0] == 's') and $cfg["video_player"] == 'vjs') ? '<' . (($type == 'live' or $type == 'short') ? 'video' : $type) . ' id="view-player-' . $file_key . '" oncontextmenu="return false;" style="width: 100%; height: 100%;" preload="none" class="video-js vjs-default-skin vjs-big-play-centered" />' : null;
    $phtml .= ($type[0] == 'a' and $cfg["audio_player"] == 'vjs') ? '<' . $type . ' id="view-player-' . $file_key . '" style="width: 100%; height: 100%;" oncontextmenu="return false;" preload="none" class="video-js vjs-default-skin vjs-big-play-centered" />' : null;
} else {
    $phtml = '<div id="ima-placeholder" class="video-js vjs-big-play-centered" style="background: #000 url(' . $tmb_url . ') no-repeat center center; z-index: 100; background-size: contain; width: 100%; height: 100%"><button class="vjs-big-play-button" type="button" aria-live="polite" title="Play Video" aria-disabled="false"><span class="vjs-control-text">Play Video</span></button></div>';
}
if (($cfg["video_player"] == 'vjs' and ($type[0] == 'v' or $type[0] == 'l' or $type[0] == 's')) or ($cfg["audio_player"] == 'vjs' and $type[0] == 'a')) {
    $first_sub  = null;
    $other_sub  = null;
    $thumb_file = $cfg["media_files_dir"] . '/' . $usr_key . '/t/' . $file_key . '/p/thumbnails.vtt';

    if (file_exists($thumb_file)) {
        $first_sub .= '<track kind="metadata" src="' . $cfg["media_files_url"] . '/' . $usr_key . '/t/' . $file_key . '/p/thumbnails.vtt"></track>';
    }

    $sub_file = $class_database->singleFieldValue('db_' . $type . 'subs', 'vjs_subs', 'file_key', $file_key);

    if ($sub_file != '') {
        $sub_arr = unserialize($sub_file);

        foreach ($sub_arr as $sub_file => $sub_arr) {
            if ($sub_arr['default'] == '1') {
                $first_sub .= '<track kind="captions" default src="' . $cfg["main_url"] . '/f_data/data_subtitles/' . VPlayers::FPsubtitle($sub_file) . '" label="' . $sub_arr['label'] . '" />';
            } else {
                $other_sub .= '<track kind="captions" src="' . $cfg["main_url"] . '/f_data/data_subtitles/' . VPlayers::FPsubtitle($sub_file) . '" label="' . $sub_arr['label'] . '" />';
            }
        }
    }
}
$phtml .= $first_sub . $other_sub;

if ($embed_src == 'local' and $_cfg["vjs_advertising"] == 1) {
    if (($cfg["video_player"] == 'vjs' and ($type[0] == 'v' or $type[0] == 'l')) or ($cfg["audio_player"] == 'vjs' and $type[0] == 'a')) {
        if ($_cfg["vjs_advertising"] == 1 and $ad_client == 'ima') {
            $phtml .= '<div id="ima-companionDiv"><script type="text/javascript">$(document).ready(function(){if (typeof googletag != "undefined") {googletag.cmd.push(function() { googletag.display("ima-companionDiv"); });}});</script></div>';
            $phtml .= '<script type="text/javascript">$(document).ready(function(){var script = document.createElement("script"); script.src = "' . $cfg["scripts_url"] . '/shared/videojs/"+compjs+".js"; script.onload = function () { if (compjs == "ads.comp" || compjs == "ads.mob") {var ads = new Ads();} }; document.head.appendChild(script); }); </script>';
        }
    }
}

echo $phtml;
$b = $cfg["main_url"] . '/' . VHref::getKey("viewers") . '?e=1&' . $type[0] . '=' . $file_key;
?>
    </div>
    <script type="text/javascript">
    <?php
echo (($type == 'video' and (($cfg["video_player"] == 'vjs' and $_cfg["vjs_advertising"] == 0) or ($mob and $ad_client != 'vast') or (!$mob and $ad_client != 'vast') or (strpos($_SERVER["HTTP_REFERER"], $cfg["main_url"]) !== false))) ? '$(document).ready(function(){' : '');
echo ($cfg['video_player'] == 'vjs' ? VPlayers::VJSJS('embed', $usr_key, $file_key) : ($cfg["video_player"] == 'jw' ? VPlayers::JWJS('embed', $usr_key, $file_key) : VPlayers::FPJS('embed', $usr_key, $file_key)));
echo (($type == 'video' and (($cfg["video_player"] == 'vjs' and $_cfg["vjs_advertising"] == 0) or ($mob and $ad_client != 'vast') or (!$mob and $ad_client != 'vast') or (strpos($_SERVER["HTTP_REFERER"], $cfg["main_url"]) !== false))) ? '});' : '');
?>
    </script>
</body>
<?php if ($cfg["google_analytics"] != ''): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?=$cfg["google_analytics"]?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?=$cfg["google_analytics"]?>');</script>
<?php endif;?>
</html>
