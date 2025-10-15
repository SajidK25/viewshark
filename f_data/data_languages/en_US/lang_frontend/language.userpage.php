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

$language['upage.tab.bulletin'] = 'Public Bulletin';
$language['upage.tab.settings'] = 'Channel Setup';
$language['upage.tab.theme']    = 'Channel Theme';
$language['upage.tab.modules']  = 'Channel Sections';
$language['upage.tab.files']    = 'Display Settings';

$language['upage.text.bull.preview'] = 'Preview';
$language['upage.text.bull.type']    = 'Enter Message';
$language['upage.text.bull.url']     = 'Enter file URL (optional)';
$language['upage.text.bull.post']    = 'Post Bulletin';
$language['upage.text.bull.hint']    = 'Bulletins will be posted on your homepage user activity and on your channel page.';

$language['upage.text.sett.url']       = 'URL: ';
$language['upage.text.sett.title']     = 'Title: ';
$language['upage.text.sett.type']      = 'Channel Type: ';
$language['upage.text.sett.type.err']  = 'Please set your channel type!';
$language['upage.text.sett.visible']   = 'Make Visible: ';
$language['upage.text.sett.tags']      = 'Channel Tags: ';
$language['upage.text.sett.tags.tip1'] = '(space separated)';
$language['upage.text.sett.tags.tip2'] = 'Tags are keywords used to help people find your channel.';

$language['upage.text.theme.new']      = 'new theme';
$language['upage.text.theme.opt.show'] = 'show detailed configuration';
$language['upage.text.theme.opt.hide'] = 'hide detailed configuration';

$language['upage.text.theme.general']        = 'Header/General styling';
$language['upage.text.theme.font']           = 'Font Family';
$language['upage.text.theme.bg.color']       = 'Global Background Color';
$language['upage.text.theme.bg.secton']      = 'Section Content Background Color';
$language['upage.text.theme.div.color']      = 'Header and Channel Container Background Color';
$language['upage.text.theme.div.txt.color']  = 'Header Text Color';
$language['upage.text.theme.div.link.color'] = 'Header Links Color (not Menu)';
$language['upage.text.theme.div.transp']     = 'Header Container Transparency';
$language['upage.text.theme.div.bg.image']   = 'Channel Background Image';
$language['upage.text.theme.div.bg.rpt']     = 'Background Repeat';
$language['upage.text.theme.div.bg.txt']     = 'will be used as channel background';
$language['upage.text.theme.colors']         = 'Channel/Menu Content styling';
$language['upage.text.theme.col.title.text'] = 'Section Titles and Active Menu Link Color';
$language['upage.text.theme.col.link']       = 'Section Links and Menu Links Color';
$language['upage.text.theme.col.body']       = 'Section Text Color';
$language['upage.text.theme.col.transp']     = 'Channel Content Transparency';
$language['upage.text.theme.save']           = 'Save Theme';
$language['upage.text.theme.default']        = 'Make this my default theme';
$language['upage.text.theme.delete']         = 'Permanently remove this theme?';

$language['upage.text.mod.profile']        = 'Profile';
$language['upage.text.mod.comment.single'] = 'Comment';
$language['upage.text.mod.comments']       = 'Comments';
$language['upage.text.mod.subscribers']    = 'Subscribers';
$language['upage.text.mod.followers']      = 'Followers';
$language['upage.text.mod.dates']          = 'Event Dates';
$language['upage.text.mod.other']          = 'Other Channels';
$language['upage.text.mod.subscriptions']  = 'Subscriptions';
$language['upage.text.mod.following']      = 'Following';
$language['upage.text.mod.friends']        = 'Friends';
$language['upage.text.mod.other']          = 'Other Channels';
$language['upage.text.mod.activity']       = 'Recent Activity';
$language['upage.text.alt.up']             = 'Move Up';
$language['upage.text.alt.down']           = 'Move Down';
$language['upage.text.alt.left']           = 'Move Left';
$language['upage.text.alt.right']          = 'Move Right';

$language['upage.section.user.txt'] = 'Your channel viewers will see links here, including "subscribe", "follow" and "add to friends".';

$language['upage.text.files.def.type']      = 'Default Content Type';
$language['upage.text.files.which.ct']      = 'Which content to display?';
$language['upage.text.files.upfiles']       = 'My Uploaded Files';
$language['upage.text.files.fav']           = 'My Favorites';
$language['upage.text.files.plist']         = 'Playlists';
$language['upage.text.files.allview']       = 'Allow "All" view';
$language['upage.text.files.feat.layout']   = 'Featured Layout';
$language['upage.text.files.feat.pv']       = 'Player View';
$language['upage.text.files.feat.gv']       = 'Grid View';
$language['upage.text.files.feat.content']  = 'Featured Content';
$language['upage.text.files.feat.all']      = 'All my files';
$language['upage.text.files.feat.file']     = 'Featured File';
$language['upage.text.files.feat.file.err'] = 'Invalid URL for Featured File';
$language['upage.text.files.feat.auto']     = 'Autoplay my files';
$language['upage.text.files.which.pl']      = 'Which playlists to display?';

$language['upage.text.nav.chan']  = '\'s Channel';
$language['upage.text.nav.all']   = 'All';
$language['upage.text.nav.upl']   = 'Uploads';
$language['upage.text.nav.fav']   = 'Favorites';
$language['upage.text.nav.plist'] = 'Playlists';

$language['upage.text.profile.views']        = 'Channel Views';
$language['upage.text.profile.joined']       = 'Joined';
$language['upage.text.profile.edit.name']    = 'Name';
$language['upage.text.profile.edit.views']   = 'Total Upload Views';
$language['upage.text.profile.edit.age']     = 'Age';
$language['upage.text.profile.edit.infl']    = 'Influences';
$language['upage.text.profile.edit.style']   = 'Style';
$language['upage.text.profile.edit.last']    = 'Last Signin';
$language['upage.text.profile.edit.site']    = 'Website';
$language['upage.text.profile.edit.descr']   = 'Channel Description';
$language['upage.text.profile.add.friend']   = 'Add to Friends';
$language['upage.text.profile.rem.friend']   = 'Remove Friend';
$language['upage.text.profile.block.user']   = 'Add to Blocklist';
$language['upage.text.profile.unblock.user'] = 'Unblock User';
$language['upage.text.profile.send.message'] = 'Private Message';
$language['upage.text.profile.signin']       = 'You must be logged in. Sign in';
$language['upage.text.profile.edit.url']     = 'link to open';
$language['upage.text.profile.edit.img']     = 'link to image';

$language['upage.text.event.add.datetime']    = 'Event Date and Time';
$language['upage.text.event.add.date']        = 'Event Date';
$language['upage.text.event.add.time']        = 'Event Time';
$language['upage.text.event.add.venue']       = 'Venue';
$language['upage.text.event.add.address']     = 'Address';
$language['upage.text.event.add.city']        = 'City';
$language['upage.text.event.add.zip']         = 'Zip/Postal Code';
$language['upage.text.event.add.country']     = 'Country';
$language['upage.text.event.add.description'] = 'Description';
$language['upage.text.event.add.tickets']     = 'Tickets';
$language['upage.text.event.get.tickets']     = 'Get Tickets';
$language['upage.text.event.delete']          = 'Delete this event?';
$language['upage.text.event.auto.suspend']    = 'Auto suspend expired events';
$language['upage.text.event.show.map']        = 'Show map of event locations';

$language['upage.text.other.add.text']     = 'You may list up to 16 channels, in the order you would like them to appear.';
$language['upage.text.other.num.rows']     = 'Number of rows to display:';
$language['upage.text.other.num.activity'] = 'Number of activities to display:';
$language['upage.text.other.sub.to']       = 'You are subscribed to ';
$language['upage.text.other.follow.to']    = 'You are following ';
$language['upage.text.other.sub.act']      = 'subscribes to ';
$language['upage.text.other.follow.act']   = 'is following ';
$language['upage.text.other.sub.to.more']  = '. Recent activity from this channel will appear on your ' . $cfg['website_shortname'] . ' home page.';
$language['upage.text.other.sub.email.me'] = 'Email me new uploads from this channel';

$language['upage.text.comm.login']            = ' to post comments';
$language['upage.text.comm.act']              = 'comments on ';
$language['upage.text.comm.add']              = 'Add Comment';
$language['upage.text.comm.post']             = 'Post Comment';
$language['upage.text.comm.delete']           = 'Delete this comment?';
$language['upage.text.comm.none']             = 'There are no comments on this channel.';
$language['upage.text.comm.limit']            = 'Your comment posting limit has been reached.';
$language['upage.text.comm.short']            = 'Your comment is too short.';
$language['upage.text.comm.long']             = 'Your comment is too long.';
$language['upage.text.comm.posted.appr']      = 'Your comment will be posted after it has been approved.';
$language['upage.text.comm.who']              = 'Comment Permissions:';
$language['upage.text.comm.spam.action']      = 'spam';
$language['upage.text.comm.spam']             = 'Spam Reports:';
$language['upage.text.comm.spam.total']       = 'user spam reports: ';
$language['upage.text.comm.spam.yes']         = 'Allow Spam reporting in comments';
$language['upage.text.comm.spam.no']          = 'No Spam reporting in comments';
$language['upage.text.comm.opt.all']          = 'Free for all (all comments will be posted immediately)';
$language['upage.text.comm.opt.friends']      = 'Custom (friends can automatically comment, all others require approval.)';
$language['upage.text.comm.opt.friends.only'] = 'Friends only (only my friends may comment.)';
$language['upage.text.comm.opt.appr']         = 'Approve all (all comments will require approval.)';
$language['upage.text.comm.new']              = 'New channel comment';
$language['upage.text.comm.pr']               = '\'s profile';

$language['upage.text.files.sort']   = 'Sort Files';
$language['upage.text.link.load']    = '(load all)';
$language['upage.text.link.more']    = '(more info)';
$language['upage.text.load.more']    = 'Load More';
$language['upage.text.load.all']     = 'Load All';
$language['upage.text.link.pl.back'] = 'Back to Playlists';
$language['upage.text.link.pl.edit'] = 'Edit my Playlist';

$language['upage.act.upload']    = 'has uploaded';
$language['upage.act.favorite']  = 'has favorited';
$language['upage.act.like']      = 'has liked';
$language['upage.act.dislike']   = 'has disliked';
$language['upage.act.comment']   = 'has commented on';
$language['upage.act.subscribe'] = 'has subscribed to';
$language['upage.act.follow']    = 'has followed';
$language['upage.act.bulletin']  = 'has posted';

$language['upage.act.your.ch']   = 'your channel';
$language['upage.act.pmessage']  = 'has sent you a private message';
$language['upage.act.frinvite']  = 'has sent you a friend request';
$language['upage.act.check.pmg'] = 'Check your ##PMSG## Requests section';
$language['upage.act.response']  = 'has posted a ##TYPE## response on';
