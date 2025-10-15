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

defined('_ISADMIN') or header('Location: /error');

$language['backend.adv.menu.website'] = 'Banner Ads';
$language['backend.adv.menu.player']  = 'Player Ads';
$language['backend.adv.menu.groups']  = 'Ad Groups';

$language['backend.adv.jw.client']        = 'Ad Client';
$language['backend.adv.jw.tag']           = 'Ad Tag URL';
$language['backend.adv.jw.duration']      = 'Ad Duration';
$language['backend.adv.jw.pos']           = 'Ad Position';
$language['backend.adv.jw.ad.file']       = 'Ad File';
$language['backend.adv.jw.ad.comp']       = 'Companion Div';
$language['backend.adv.jw.ad.mobile']     = 'Include on mobile';
$language['backend.adv.jw.ad.comp.id']    = 'Network/Unit Path';
$language['backend.adv.jw.ad.comp.w']     = 'Companion Width';
$language['backend.adv.jw.ad.comp.h']     = 'Companion Height';
$language['backend.adv.jw.pre']           = 'Pre-roll';
$language['backend.adv.jw.post']          = 'Post-roll';
$language['backend.adv.jw.offset']        = 'Time Offset';
$language['backend.adv.jw.format']        = 'Ad Format';
$language['backend.adv.jw.linear']        = 'Linear: MP4, WEBM, FLV, VPAID (SWF)';
$language['backend.adv.jw.nonlinear']     = 'Non-linear: VPAID (SWF)';
$language['backend.adv.jw.companion1']    = 'Companion: GIF, JPG, PNG';
$language['backend.adv.jw.companion2']    = 'Companion: HTML, IFRAME';
$language['backend.adv.jw.server']        = 'Ad Server';
$language['backend.adv.jw.stats']         = 'Ad Stats';
$language['backend.adv.jw.server.list']   = 'custom,AdTech,OpenX,SpotXchange,Adify,Adrise,TidalTV,Adap.tv,Eyewonder,Smartclip,VideoPlaza,Lightningcast,Emediate,Smart Ad Server,DoubleClick,Liverail,Oasis,AdForm,AdJuggler,Innovid,Brightroll,Adotube,Microsoft,MediaMind,Zedo,Mov.ad,Telemetry,Zoom.in,24/7 Real Media';
$language['backend.adv.jw.client.vast']   = 'VAST/VPAID';
$language['backend.adv.jw.client.ima']    = 'Google IMA';
$language['backend.adv.jw.client.vmap']   = 'VMAP';
$language['backend.adv.jw.client.custom'] = 'Custom';
$language['backend.adv.jw.skip']          = 'Skippable (seconds)';
$language['backend.adv.jw.tracking']      = 'Tracking Events';
$language['backend.adv.jw.impressions']   = 'Impressions';
$language['backend.adv.jw.clicks']        = 'Clicks';
$language['backend.adv.jw.clickrate']     = 'Click Rate';
$language['backend.adv.jw.clickthrough']  = 'Click Through URL';
$language['backend.adv.jw.clicktracking'] = 'Click Tracking';
$language['backend.adv.jw.events']        = 'start,firstQuartile,midpoint,thirdQuartile,complete,pause,mute,fullscreen';
$language['backend.adv.jw.code']          = 'From URL';
$language['backend.adv.jw.file']          = 'Upload File';
$language['backend.adv.select.for.video'] = 'Assign video ads to: ';
$language['backend.adv.select.for.audio'] = 'Assign audio ads to: ';
$language['backend.adv.banner.for']       = 'Assign banner ads to: ';
$language['backend.adv.fp.cuepoint']      = 'Cuepoint';
$language['backend.adv.fp.css']           = 'CSS Style';

$language['backend.adv.sub.15'] = 'JW Player Ads';
$language['backend.adv.sub.16'] = 'Ad Files';
$language['backend.adv.sub.17'] = 'Flowplayer Ads';
$language['backend.adv.sub.18'] = 'Video JS Ads';

$language['backend.adv.sub.1']  = 'Homepage / Main';
$language['backend.adv.sub.2']  = 'Browse Channels';
$language['backend.adv.sub.3']  = 'Browse Files';
$language['backend.adv.sub.4']  = 'View Files';
$language['backend.adv.sub.5']  = 'View Comments';
$language['backend.adv.sub.6']  = 'View Responses';
$language['backend.adv.sub.7']  = 'View Playlists';
$language['backend.adv.sub.8']  = 'New Response';
$language['backend.adv.sub.9']  = 'Registration';
$language['backend.adv.sub.10'] = 'Sign In';
$language['backend.adv.sub.11'] = 'Searching';
$language['backend.adv.sub.12'] = 'Footer';
$language['backend.adv.sub.13'] = 'Browse Playlists';
$language['backend.adv.sub.14'] = 'Per File';
$language['backend.adv.sub.19'] = 'Per Category';

$language['backend.adv.text.shared']    = 'Shared Ad';
$language['backend.adv.text.dedicated'] = 'Dedicated Ad';
$language['backend.adv.text.rotate']    = 'Rotate Ads';
$language['backend.adv.text.width']     = 'Width';
$language['backend.adv.text.height']    = 'Height';
$language['backend.adv.text.class']     = 'CSS class';
$language['backend.adv.text.style']     = 'CSS style';

$language['backend.adv.text.type']  = 'Ad type';
$language['backend.adv.text.group'] = 'Ad group';
$language['backend.adv.text.code']  = 'Ad code';
$language['backend.adv.text.url']   = 'Ad file URL';
