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

$language['frontend.global.videos'] = 'Videos ';
$language['frontend.global.images'] = 'Pictures ';
$language['frontend.global.audio']  = 'Audio ';
$language['frontend.global.doc']    = 'Documents ';

$language['files.menu.order']           = 'Order ';
$language['files.menu.add.new']         = 'Add New ##TYPE##';
$language['files.menu.show.type']       = 'Show ##TYPE##';
$language['files.menu.public.type']     = 'Public ##TYPE##';
$language['files.menu.private.type']    = 'Private ##TYPE##';
$language['files.menu.personal.type']   = 'Personal ##TYPE##';
$language['files.menu.recent.type']     = 'Latest ##TYPE##';
$language['files.menu.live.type']       = 'Live ##TYPE##';
$language['files.menu.featured.type']   = 'Featured ##TYPE##';
$language['files.menu.promoted.type']   = 'Promoted ##TYPE##';
$language['files.menu.update.type']     = 'Update ##TYPE##';
$language['files.menu.relevant.type']   = 'Relevant ##TYPE##';
$language['files.menu.viewed.type']     = 'Most Viewed ##TYPE##';
$language['files.menu.active.type']     = 'Most Active ##TYPE##';
$language['files.menu.commented.type']  = 'Most Commented ##TYPE##';
$language['files.menu.responded.type']  = 'Most Responded ##TYPE##';
$language['files.menu.most.liked.type'] = 'Most Liked ##TYPE##';
$language['files.menu.favorited.type']  = 'Top Favorite ##TYPE##';
$language['files.menu.type.comm']       = '##TYPE## Comments';
$language['files.menu.type.resp']       = '##TYPE## Responses';
$language['files.menu.type.lists']      = '##TYPE## Playlists';
$language['files.menu.view1']           = 'Thumb View';
$language['files.menu.view2']           = 'Grid View';
$language['files.menu.view3']           = 'List View';
$language['files.menu.public']          = 'Public ';
$language['files.menu.private']         = 'Private ';
$language['files.menu.personal']        = 'Personal ';
$language['files.menu.relevance']       = 'Relevance ';
$language['files.menu.recent']          = 'Latest ';
$language['files.menu.featured']        = 'Featured ';
$language['files.menu.viewed']          = 'Most Viewed ';
$language['files.menu.commented']       = 'Most Commented ';
$language['files.menu.commented.own']   = 'Commented ';
$language['files.menu.responded']       = 'Most Responded ';
$language['files.menu.responded.own']   = 'Responded ';
$language['files.menu.most.liked']      = 'Most Liked ';
$language['files.menu.most.liked.own']  = 'Liked ';
$language['files.menu.most.active']     = 'Most Active ';
$language['files.menu.watch.later']     = 'Watch Later';
$language['files.menu.watch.in']        = 'In watchlist';
$language['files.menu.watch.login']     = 'Login required';
$language['files.menu.flagged']         = 'Flagged ';
$language['files.menu.live']            = 'Live';
$language['files.menu.l.count']         = 'Login Count ';
$language['files.menu.lv.count']        = 'Stream Count ';
$language['files.menu.v.count']         = 'Video Count ';
$language['files.menu.i.count']         = 'Image Count ';
$language['files.menu.a.count']         = 'Audio Count ';
$language['files.menu.d.count']         = 'Document Count ';
$language['files.menu.b.count']         = 'Blog Count ';

$language['files.menu.l.up']      = 'My Stream Uploads';
$language['files.menu.v.up']      = 'My Video Uploads';
$language['files.menu.i.up']      = 'My Picture Uploads';
$language['files.menu.a.up']      = 'My Audio Uploads';
$language['files.menu.d.up']      = 'My Document Uploads';
$language['files.menu.b.up']      = 'My Blog Entries';
$language['files.menu.up.subs']   = 'Subscription Uploads';
$language['files.menu.up.osub']   = 'Subscriber Uploads';
$language['files.menu.up.follow'] = 'Followed Uploads';

$language['files.menu.most.disliked'] = 'Most Dislikes ';
$language['files.menu.most.live']     = 'Most Streams ';
$language['files.menu.most.videos']   = 'Most Videos ';
$language['files.menu.most.images']   = 'Most Pictures ';
$language['files.menu.most.audios']   = 'Most Audios ';
$language['files.menu.most.doc']      = 'Most Documents ';
$language['files.menu.most.blogs']    = 'Most Blogs ';
$language['files.menu.most.active']   = 'Most Active ';
$language['files.menu.ch.views']      = 'Channel Views ';
$language['files.menu.favorited']     = 'Top Favorites ';
$language['files.menu.favorited.own'] = 'Favorited ';
$language['files.menu.subscribed']    = 'My Subscribers';
$language['files.menu.followers']     = 'My Followers';
$language['files.menu.subscriptions'] = 'My Subscriptions';
$language['files.menu.follows']       = 'Followed Channels';
$language['files.menu.active']        = 'Active ';
$language['files.menu.inactive']      = 'Inactive ';
$language['files.menu.approved']      = 'Approved ';
$language['files.menu.pending']       = 'Pending ';
$language['files.menu.deleted']       = 'Deleted ';
$language['files.menu.longest']       = 'Longest ';
$language['files.menu.shortest']      = 'Shortest ';
$language['files.menu.status']        = 'Status';
$language['files.menu.numbers']       = 'Numbers';
$language['files.menu.length']        = 'Length';
$language['files.menu.promoted']      = 'Promoted';
$language['files.menu.affiliated']    = 'Affiliated';
$language['files.menu.partnered']     = 'Partnered';
$language['files.menu.nocateg']       = 'Uncategorized';
$language['files.menu.sort.ch']       = 'Sort:';
$language['files.menu.sort.pl']       = 'Sort:';
$language['files.menu.sort.v']        = 'Sort:';
$language['files.menu.sort.type']     = 'Type:';

$language['playlist.menu.title.asc']  = 'All (A-Z)';
$language['playlist.menu.title.desc'] = 'All (Z-A)';
$language['playlist.menu.public']     = 'Public ';
$language['playlist.menu.private']    = 'Private ';
$language['playlist.menu.personal']   = 'Personal ';
$language['playlist.menu.recent']     = 'Latest ';
$language['playlist.menu.pl.setup']   = 'Playlist Settings';

$language['playlist.section.title.relevant']  = 'Relevant ##TYPE## Playlists';
$language['playlist.section.title.public']    = 'Recent ##TYPE## Playlists';
$language['playlist.section.title.recent']    = 'Latest ##TYPE## Playlists';
$language['playlist.section.title.private']   = 'Private ##TYPE## Playlists';
$language['playlist.section.title.personal']  = 'Personal ##TYPE## Playlists';
$language['playlist.section.title.views']     = 'Most Viewed ##TYPE## Playlists';
$language['playlist.section.title.titleasc']  = 'All ##TYPE## Playlists (A-Z)';
$language['playlist.section.title.titledesc'] = 'All ##TYPE## Playlists (Z-A)';

$language['blog.section.title.relevant']  = 'Relevant Blogs';
$language['blog.section.title.public']    = 'Recent Blogs';
$language['blog.section.title.recent']    = 'Latest Blogs';
$language['blog.section.title.private']   = 'Private Blogs';
$language['blog.section.title.personal']  = 'Personal Blogs';
$language['blog.section.title.views']     = 'Most Viewed Blogs';
$language['blog.section.title.titleasc']  = 'All Blogs (A-Z)';
$language['blog.section.title.titledesc'] = 'All Blogs (Z-A)';

$language['playlist.save.error1'] = 'Error: Empty playlist was not saved!';
$language['playlist.save.error2'] = 'Error: An internal error has occured while saving your playlist!';
$language['playlist.save.error3'] = 'Error: This playlist already exists!';

$language['files.action.new']        = 'New Playlist';
$language['files.action.new.blog']   = 'New Blog';
$language['files.action.new.type']   = 'Playlist Type';
$language['files.action.new.title']  = 'Playlist Title';
$language['files.action.new.descr']  = 'Playlist Description';
$language['files.action.new.tags']   = 'Playlist Tags';
$language['files.action.new.create'] = 'Create Playlist';
$language['files.action.add.to']     = 'Add To';
$language['files.action.private']    = 'Make Private';
$language['files.action.public']     = 'Make Public';
$language['files.action.personal']   = 'Make Personal';
$language['files.action.flagged']    = 'Clear Flagged';

$language['files.action.active']      = 'Make Active';
$language['files.action.inactive']    = 'Make Inactive';
$language['files.action.approve']     = 'Make Approved';
$language['files.action.disapprove']  = 'Make Pending';
$language['files.action.feature']     = 'Make Featured';
$language['files.action.unfeature']   = 'Clear Featured';
$language['files.action.promote']     = 'Make Promoted';
$language['files.action.unpromote']   = 'Clear Promoted';
$language['files.action.del.files']   = 'Delete Files';
$language['files.action.del.confirm'] = 'Confirm Action';
$language['files.action.affiliate']   = 'Make Affiliate';
$language['files.action.unaffiliate'] = 'Clear/Deny Affiliate';
$language['files.action.partner']     = 'Make Partner';
$language['files.action.unpartner']   = 'Clear/Deny Partner';

$language['files.action.watch.add']   = 'Add to Watchlist';
$language['files.action.watch.clear'] = 'Clear Watchlist';
$language['files.action.fav.add']     = 'Add to Favorites';
$language['files.action.fav.clear']   = 'Clear Favorites';
$language['files.action.pl.add']      = 'Add to Playlist';
$language['files.action.pl.clear']    = 'Clear Playlist';
$language['files.action.liked.clear'] = 'Clear Liked';
$language['files.action.hist.clear']  = 'Clear History';

$language['files.menu.l.up'] = 'My Streams';
$language['files.menu.v.up'] = 'My Videos';
$language['files.menu.i.up'] = 'My Pictures';
$language['files.menu.a.up'] = 'My Audios';
$language['files.menu.d.up'] = 'My Documents';
$language['files.menu.b.up'] = 'My Blogs';

$language['files.menu.sort.by']   = 'Sort by';
$language['files.menu.sort.back'] = 'Back';

$language['files.menu.active.filters'] = 'Active filters';
$language['files.menu.results.filter'] = 'Filter Results';

$language['files.menu.s.count']     = 'Short Count ';
$language['files.menu.s.up']        = 'My Shorts';
$language['files.menu.most.shorts'] = 'Most Shorts ';
