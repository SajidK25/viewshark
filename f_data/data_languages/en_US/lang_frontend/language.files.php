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

$language['files.menu.myfiles']       = 'Uploads';
$language['files.menu.myfiles.type']  = 'My Uploads';
$language['files.menu.mybcasts']      = 'Streams';
$language['files.menu.mybcasts.type'] = 'My Streams';
$language['files.menu.myfav']         = 'Favorites';
$language['files.menu.myfav.type']    = 'My Favorites';
$language['files.menu.liked']         = 'Likes';
$language['files.menu.liked.type']    = 'My Likes';
$language['files.menu.history']       = 'History';
$language['files.menu.history.type']  = 'My History';
$language['files.menu.watch']         = 'Watchlist';
$language['files.menu.watch.type']    = 'My Watchlist';
$language['files.menu.comments']      = 'Comments';
$language['files.menu.responses']     = 'Responses';
$language['files.menu.manage']        = 'File Manager';
$language['files.menu.mypl']          = 'Playlists';
$language['files.menu.mypl2']         = 'My Playlists';
$language['files.menu.mybl2']         = 'My Blogs';

$language['files.list.views']     = 'Views: ';
$language['files.list.comm']      = 'Comments: ';
$language['files.list.resp']      = 'Responses: ';
$language['files.list.like']      = 'Liked: ';
$language['files.list.dislike']   = 'Disliked: ';
$language['files.list.favorited'] = 'Favorited: ';
$language['files.list.thumb']     = 'Thumbnail';
$language['files.list.nodescr']   = 'no description available';

$language['files.text.file.title']    = 'Title';
$language['files.text.file.descr']    = 'Description';
$language['files.text.file.tags']     = 'Tags';
$language['files.text.file.categ']    = 'Category';
$language['files.text.file.owner']    = 'Owner';
$language['files.text.stream.server'] = 'Stream Server';
$language['files.text.stream.status'] = 'Stream Status';

$language['files.action.sort']           = 'Sort Files: ';
$language['files.option.about']          = 'About';
$language['files.option.live']           = 'Stream Setup';
$language['files.option.blog']           = 'Blog Content';
$language['files.option.thumb']          = 'Thumbnail';
$language['files.option.privacy']        = 'Privacy';
$language['files.option.comments']       = 'Commenting';
$language['files.option.comment.vote']   = 'Comment Voting';
$language['files.option.comment.spam']   = 'Spam Flagging';
$language['files.option.video.response'] = 'Responding';
$language['files.option.rating']         = 'Rating';
$language['files.option.embed']          = 'Embedding';
$language['files.option.thumbnail']      = 'Thumbnail';

$language['files.text.no.live']            = 'Live streaming is not enabled for this account. Settings will not get saved!';
$language['files.text.live.chat']          = 'Enable live chat';
$language['files.text.live.chat.none']     = 'Disable live chat';
$language['files.text.live.vod']           = 'Save the video of this stream';
$language['files.text.live.vod.none']      = 'Do not save this stream';
$language['files.text.live.stream.end']    = 'This live stream has ended. Stream settings are no longer available.';
$language['files.text.new.pl']             = 'New playlist successfully created';
$language['files.text.new.bl']             = 'New blog successfully created';
$language['files.text.public']             = 'Public (anyone can search and view - recommended)';
$language['files.text.personal']           = 'Personal (viewable only by you)';
$language['files.text.private']            = 'Private (viewable by you and your friends)';
$language['files.text.comments.auto']      = 'Allow comments to be added automatically.';
$language['files.text.comments.fronly']    = 'Comments must be approved. Friends can comment automatically.';
$language['files.text.comments.approve']   = 'All comments must be approved.';
$language['files.text.comments.none']      = 'No comments allowed.';
$language['files.text.comments.vote.all']  = 'Allow users to vote on comments.';
$language['files.text.comments.vote.none'] = 'Do not allow comment voting';
$language['files.text.comments.spam.all']  = 'Allow users to flag comments as spam.';
$language['files.text.comments.spam.none'] = 'Do not allow comment spam flagging';
$language['files.text.responses.all']      = 'Allow file responses to be added automatically.';
$language['files.text.responses.approve']  = 'All file responses must be approved.';
$language['files.text.responses.none']     = 'No file responses allowed.';
$language['files.text.rating.all']         = 'Allow this to be rated by members.';
$language['files.text.rating.none']        = 'No file rating allowed';
$language['files.text.embed.all']          = 'Allow external sites to embed this file.';
$language['files.text.embed.none']         = 'Do not allow external sites to embed this file.';
$language['files.text.social.share']       = 'Allow sharing on Facebook, Twitter, etc.';
$language['files.text.social.none']        = 'No social web sharing allowed';
$language['files.text.thumb.sizes']        = 'For best results, please upload images with a width and height of 640x360 or 853x480 or 1280x720 or 1920x1080';
$language['files.text.no.comments']        = '<i class="icon-search"></i> Sorry, no comments were found';
$language['files.text.no.responses']       = '<i class="icon-search"></i> Sorry, no responses were found';
$language['files.text.comments.for']       = 'Sort: ';
$language['files.text.responses.for']      = 'Sort: ';
$language['files.text.ct.sort.approved']   = 'Approved';
$language['files.text.ct.sort.suspended']  = 'Pending';
$language['files.text.ct.sort.today']      = 'Posted Today';
$language['files.text.ct.sort.recent']     = 'Recent';
$language['files.text.no.subscription']    = '- no subscriptions -';
$language['files.text.no.subscriber']      = '- no subscribers -';
$language['files.text.no.follower']        = '- no followers -';
$language['files.text.no.following']       = '- not following -';
$language['files.text.pending.appr']       = 'Your ##TYPE## is currently pending approval. Please stand by while we process your request.';
$language['files.text.unsub.warn1']        = 'You are about to unsubscribe from <b>##USER##</b>. Please confirm if you would like to proceed.';
$language['files.text.unsub.warn2']        = 'Your current subscription will remain active until ';
$language['files.text.unsub.warn3']        = 'This subscription is active until: ';

$language['files.text.act.sel']         = 'Selection Actions';
$language['files.text.act.edit']        = 'Toggle Edit Mode';
$language['files.text.act.all']         = 'Toggle Select All';
$language['files.text.edit.back']       = 'Back to Media Library';
$language['files.text.pl.back']         = 'Back to Playlists';
$language['files.text.edit.date']       = 'Upload Date';
$language['files.text.edit.filename']   = 'File Name';
$language['files.text.edit.size']       = 'File Size';
$language['files.text.edit.thumb']      = 'New Thumbnail';
$language['files.text.save.thumb']      = 'Update Thumbnail';
$language['files.text.edit.thumb.text'] = 'Upload an image to be used as thumbnail for this file.';
$language['files.text.insert.url.text'] = 'Enter a valid ##TYPE## URL';
$language['files.text.insert.url.file'] = 'File URL';
$language['files.text.insert.url.sel']  = 'Insert Selected';
$language['files.text.insert.url.type'] = 'Insert ##TYPE## to blog';
$language['files.text.insert.from.up']  = 'From My Uploads';
$language['files.text.insert.from.fav'] = 'From My Favorites';
$language['files.text.insert.from.url'] = 'From URL';
$language['files.text.insert.add.to']   = 'Add to blog';

$language['files.text.pl.text.opt']    = 'Playlist Options:';
$language['files.text.pl.tab.share']   = 'Share';
$language['files.text.pl.tab.privacy'] = 'Privacy';
$language['files.text.pl.tab.edit']    = 'Edit Details';
$language['files.text.pl.tab.delete']  = 'Delete Playlist';
$language['files.text.pl.thumb']       = 'Playlist Thumbnail';

$language['files.text.pl.share.url']    = 'Playlist URL';
$language['files.text.pl.share.page']   = 'Go to Playlist Page';
$language['files.text.pl.share.mail']   = 'Email this playlist';
$language['files.text.pl.share.emb1']   = 'Embed code (copy and paste this into a web page) ';
$language['files.text.pl.share.emb2']   = 'Allow others to embed this playlist';
$language['files.text.pl.share.email']  = 'Allow others to email this playlist';
$language['files.text.pl.share.social'] = 'Allow social bookmark sharing';
$language['files.text.pl.share.myct']   = 'Add from contacts:';

$language['files.text.pl.total']       = 'files in playlist';
$language['files.text.pl.created']     = 'Created: ';
$language['files.text.pl.del.txt1']    = 'Are you sure you want to delete ';
$language['files.text.pl.del.txt2']    = 'Note: Deleting playlists is a permanent action and cannot be undone. ';
$language['files.text.pl.del.yes']     = 'Yes, delete it!';
$language['files.text.pl.details.err'] = 'Please set the playlist title.';

$language['files.text.pl.details.embed']   = 'Embed';
$language['files.text.pl.details.edit']    = 'Edit Playlist';
$language['files.text.pl.details.top1']    = 'Playlist details';
$language['files.text.pl.details.top2']    = 'Owner details';
$language['files.text.pl.details.len']     = 'runtime: ';
$language['files.text.pl.details.filespl'] = 'Playlist Files';
$language['files.text.pl.details.sub']     = 'subscribers';
$language['files.text.pl.details.join']    = 'Joined';
$language['files.text.pl.details.guest']   = 'You must be logged in to use this feature';
$language['files.text.none']               = '<i class="icon-search"></i> Sorry, no files available';
$language['files.playlist.none']           = '<i class="icon-search"></i> Sorry, no playlists available';

$language['files.text.subs.opt.1']       = 'Subscription Settings';
$language['files.text.subs.opt.1f']      = 'Follow Settings';
$language['files.text.subs.edit']        = 'Edit subscription';
$language['files.text.follow.edit']      = 'Edit follow';
$language['files.text.subs.cancel']      = 'Cancel subscription for ';
$language['files.text.subs.include']     = 'Notification list includes:';
$language['files.text.subs.opt.all']     = 'All files uploaded, rated, favorited, and commented by ';
$language['files.text.subs.opt.files']   = 'Only files uploaded by ';
$language['files.text.subs.opt.unsub']   = 'Unsubscribe from ';
$language['files.text.subs.opt.uploads'] = 'Email me on new uploads';

$language['files.text.live.help']  = 'For more details on live streaming, please visit the ##URL## help page.';
$language['files.text.nofile.err'] = 'The video could not be opened for processing. Please check your file and retry uploading it.';
$language['files.text.notfound']   = '##TYPE## ID was not found. Please check your URL and try again.';

$language['files.text.pl.thumb.empty'] = 'Playlist is empty. Thumbnails can be selected only from existing playlist items.';
$language['files.text.pl.thumb.order'] = 'Playlist is empty. Add items to playlist first.';

$language['files.option.social.share'] = 'Sharing';
$language['files.text.unsub.warn4']    = 'To unsubscribe, please visit ##USER##\'s channel page.';
$language['files.text.comment.on']     = 'commented on ';
$language['files.text.response.to']    = 'responded to ';

$language['files.text.stream.name']  = 'Long Stream Key (strong encryption)';
$language['files.text.stream.short'] = 'Short Stream Key (standard encryption)';
