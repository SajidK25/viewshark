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

$language['backend.import.link']              = 'Grab Tools';
$language['backend.import.feed.h1']           = 'Grab Youtube Video Feeds';
$language['backend.import.channel.h1']        = 'Grab Youtube Channel Feeds';
$language['backend.import.vimeo.h1']          = 'Grab Vimeo User Feeds';
$language['backend.import.dm.h1']             = 'Grab Dailymotion User Feeds';
$language['backend.import.dm.video.h1']       = 'Grab Dailymotion Video Feeds';
$language['backend.import.metacafe.h1']       = 'Grab Metacafe User Feeds';
$language['backend.import.metacafe.video.h1'] = 'Grab Metacafe Video Feeds';
$language['backend.import.embed.h1']          = 'Grab from URL';
$language['backend.import.search.h1']         = 'Search and Embed Files';
$language['backend.import.search.for']        = 'Search for';
$language['backend.import.search.on']         = 'Search on';
$language['backend.import.feed.categ']        = 'Feed Category';
$language['backend.import.feed.region']       = 'Feed Region';
$language['backend.import.feed.type']         = 'Feed Type';
$language['backend.import.feed.time']         = 'Feed Time';
$language['backend.import.feed.sort']         = 'Feed Sort';
$language['backend.import.feed.results']      = 'Feed Results';
$language['backend.import.feed.assign']       = 'Assign Username';
$language['backend.import.feed.assign.c']     = 'Assign Category';
$language['backend.import.feed.channel']      = 'Channel Feed';
$language['backend.import.feed.user']         = 'User Feed';
$language['backend.import.feed.list']         = 'Find Videos';
$language['backend.import.feed.import']       = 'Save Videos';
$language['backend.import.feed.categ.list']   = array(
    1  => 'Film & Animation',
    2  => 'Autos & Vehicles',
    10 => 'Music',
    15 => 'Pets & Animals',
    17 => 'Sports',
    18 => 'Short Movies',
    19 => 'Travel & Events',
    20 => 'Gaming',
    21 => 'Videoblogging',
    22 => 'People & Blogs',
    23 => 'Comedy',
    24 => 'Entertainment',
    25 => 'News & Politics',
    26 => 'Howto & Style',
    27 => 'Education',
    28 => 'Science & Technology',
    30 => 'Movies',
    43 => 'Shows',
    44 => 'Trailers',
);

$language['backend.import.yt.api.key']          = 'YouTube API Key';
$language['backend.import.yt.api.key.tip']      = 'Set the "API key" value from the "Access" tab of the Google Developers Console https://console.developers.google.com/ Please ensure that you have enabled the YouTube Data API for your project.';
$language['backend.import.feed.type.def']       = 'Video Definition';
$language['backend.import.feed.type.def.list']  = 'any,high,standard';
$language['backend.import.feed.type.dim']       = 'Video Dimension';
$language['backend.import.feed.type.dim.list']  = '2d,3d,any';
$language['backend.import.feed.type.dur']       = 'Video Duration';
$language['backend.import.feed.type.dur.list']  = 'any,long,medium,short';
$language['backend.import.feed.type.emb']       = 'Video Embeddable';
$language['backend.import.feed.type.emb.list']  = 'any,true';
$language['backend.import.feed.type.lic']       = 'Video License';
$language['backend.import.feed.type.lic.list']  = 'any,creativeCommon,youtube';
$language['backend.import.feed.type.syn']       = 'Video Syndicated';
$language['backend.import.feed.type.syn.list']  = 'any,true';
$language['backend.import.feed.type.type']      = 'Video Type';
$language['backend.import.feed.type.type.list'] = 'any,episode,movie';
$language['backend.import.feed.type.res']       = 'Results (max 50)';

$language['backend.import.feed.categ.dm.list']        = 'Animals,Arts,Auto-Moto,College,Film &amp; TV,Funny,Gaming,Gay &amp; Lesbian,Kids,Lifestyle,Music,News &amp; Politics,People &amp; Family,Sexy,Sports,TV,Tech &amp; Science,Travel,Webcam & Vlogs';
$language['backend.import.feed.categ.mc.list']        = 'Art &amp; Animation,Comedy,Cool Commercials,Entertainment,How To,Music &amp; Dance,News &amp; Events,People &amp; Stories,Pets &amp; Animals,Science &amp; Technology,Sports,Travel &amp; Outdoors,Video Games,Wheels &amp; Wings,18+ Only';
$language['backend.import.feed.region.list']          = 'AR,AU,BE,BR,CA,CL,CO,CZ,EG,FR,DE,GB,HK,HU,IN,IE,IL,IT,JP,JO,MY,MX,MA,NL,NZ,PE,PH,PL,RU,SA,SG,ZA,KR,ES,SE,TW,AE,US';
$language['backend.import.feed.region.dm.list']       = 'AR,CA,EN,ES,FR,DE,HU,IT,NL,PL,RO,RU';
$language['backend.import.feed.type.list']            = 'Most Popular';
$language['backend.import.feed.dm.sort.list']         = 'Recent,Visited,Visited-Hour,Visited-Today,Visited-Week,Visited-Month,Commented,Commented-Hour,Commented-Today,Commented-Week,Commented-Month,Rated,Rated-Hour,Rated-Today,Rated-Week,Rated-Month,Ranking';
$language['backend.import.feed.dm.filters.list']      = 'Featured,HD,Official,Creative,Creative-Official,Buzz,Buzz-Premium,3D,Live';
$language['backend.import.feed.time.list']            = 'Today,All time';
$language['backend.import.feed.time.mc.list']         = 'Today,This week,This month,All time';
$language['backend.import.feed.search.list']          = 'Youtube,Dailymotion,Vimeo';
$language['backend.import.feed.results.list']         = '1 - 50,51 - 100,101 - 150,151 - 200,201 - 250,251 - 300,301 - 350,351 - 400,401 - 450,451 - 500';
$language['backend.import.feed.results.channel.list'] = '1 - 50,51 - 100,101 - 150,151 - 200,201 - 250,251 - 300,301 - 350,351 - 400,401 - 450,451 - 500';
$language['backend.import.feed.results.dm.feed']      = '1 - 50,51 - 100,101 - 150,151 - 200,201 - 250,251 - 300,301 - 350,351 - 400,401 - 450,451 - 500';
$language['backend.import.feed.results.mc.feed']      = '1 - 50,51 - 100,101 - 150,151 - 200,201 - 250,251 - 300,301 - 350,351 - 400,401 - 450,451 - 500';
$language['backend.import.feed.results.vimeo.feed']   = '1 - 20,21 - 40,41 - 60';
$language['backend.import.feed.results.feed.list']    = 'Videos,Favorites';
$language['backend.import.feed.results.dm.list']      = 'Videos,Favorites,Features,Subscriptions';
$language['backend.import.feed.results.vimeo.list']   = 'Videos,Likes,Appears In,Subscriptions';
$language['backend.import.prevpage']                  = 'Prev';
$language['backend.import.nextpage']                  = 'Next';
$language['backend.import.currpage']                  = 'Current';
$language['backend.import.category']                  = 'in: ';
$language['backend.import.feed.stop']                 = 'end';
$language['backend.import.select.all.plus']           = 'All [+]';
$language['backend.import.select.all.minus']          = 'None [-]';
$language['backend.import.select.all']                = 'Select All';
$language['backend.import.unselect.all']              = 'Unselect All';
$language['backend.embed.youtube']                    = 'Video URL';
$language['backend.embed.video']                      = 'Embed Video';
$language['backend.embed.video.find']                 = 'Find Video';
$language['backend.embed.confirmed']                  = 'CONFIRMED: Entry added successfully!';
$language['backend.embed.failed']                     = 'FAILED: Duplicate entry ignored!';
$language['backend.embed.category']                   = 'Please assign a category to this video.';
$language['backend.embed.username']                   = 'Please assign a username.';
$language['backend.embed.nosel']                      = 'No entries selected! Please select one or more entries.';

$language['backend.import.embed']         = 'Embed';
$language['backend.import.download']      = 'Download';
$language['backend.import.ask']           = 'Ask';
$language['backend.embed.confirmed.dl']   = 'CONFIRMED: Entry scheduled for download!';
$language['backend.embed.video.save']     = 'Save Video';
$language['backend.embed.video.mode']     = 'Grabber Mode';
$language['backend.embed.video.mode.tip'] = 'Set the mode for saving files. Can be "embed", "download", or "ask".';

$language['backend.import.menu.grabber']               = 'Video Grabber Plugin';
$language['backend.import.menu.grabber.functions']     = 'Active Functions';
$language['backend.import.menu.grabber.functions.tip'] = 'Configure the video grabber supported websites.';
$language['backend.import.menu.grabber.yt.support']    = 'Youtube Support';
$language['backend.import.menu.grabber.dm.support']    = 'Dailymotion Support';
$language['backend.import.menu.grabber.mc.support']    = 'Metacafe Support';
$language['backend.import.menu.grabber.vi.support']    = 'Vimeo Support';
$language['backend.import.menu.yt.list']               = 'Youtube Channel List';
$language['backend.import.menu.yt.list.tip']           = 'Import video feeds from the listed Youtube accounts (one per line).';
$language['backend.import.menu.dm.list']               = 'Dailymotion User List';
$language['backend.import.menu.dm.list.tip']           = 'Import video feeds from the listed Dailymotion accounts (one per line).';
$language['backend.import.menu.mc.list']               = 'Metacafe User List';
$language['backend.import.menu.mc.list.tip']           = 'Import video feeds from the listed Metacafe accounts (one per line).';
$language['backend.import.menu.vi.list']               = 'Vimeo User List';
$language['backend.import.menu.vi.list.tip']           = 'Import video feeds from the listed Vimeo accounts (one per line).';
$language['backend.import.menu.disabled.functions']    = 'Grab functions are currently disabled.';
$language['backend.import.menu.mobile.list']           = 'Mobile Listing';
$language['backend.import.menu.mobile.list.tip']       = 'Configure if embedded videos will be listed in mobile interface';
$language['backend.import.menu.mobile.yt']             = 'List Youtube videos in mobile interface';
$language['backend.import.menu.mobile.dm']             = 'List Dailymotion videos in mobile interface';
$language['backend.import.menu.mobile.vi']             = 'List Vimeo videos in mobile interface';
$language['backend.import.menu.from.yt']               = 'From Youtube ';
$language['backend.import.menu.from.dm']               = 'From Dailymotion ';
$language['backend.import.menu.from.mc']               = 'From Metacafe ';
$language['backend.import.menu.from.vi']               = 'From Vimeo ';

$language['backend.import.perm.allow.embed']      = 'Allow single file embedding';
$language['backend.import.perm.allow.yt.video']   = 'Allow importing Youtube video feeds';
$language['backend.import.perm.allow.yt.channel'] = 'Allow importing Youtube channel feeds';
$language['backend.import.perm.allow.dm.video']   = 'Allow importing Dailymotion video feeds';
$language['backend.import.perm.allow.dm.user']    = 'Allow importing Dailymotion user feeds';
$language['backend.import.perm.allow.mc.video']   = 'Allow importing Metacafe video feeds';
$language['backend.import.perm.allow.mc.user']    = 'Allow importing Metacafe user feeds';
$language['backend.import.perm.allow.vi.user']    = 'Allow importing Vimeo user feeds';
