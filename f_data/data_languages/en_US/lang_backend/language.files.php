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

defined('_ISADMIN') or header('Location: /error');

$language['backend.files.menu.manage'] = 'File Management';
$language['backend.files.menu.video']  = 'Video Files';
$language['backend.files.menu.image']  = 'Image Files';
$language['backend.files.menu.audio']  = 'Audio Files';
$language['backend.files.menu.doc']    = 'Document Files';
$language['backend.files.menu.categ']  = 'By Category';

$language['backend.files.menu.all'] = 'All ';
$language['backend.files.menu.flv'] = 'Hosted/SD ';
$language['backend.files.menu.mp4'] = 'Hosted/HD ';
$language['backend.files.menu.mob'] = 'Hosted/Mobile ';
$language['backend.files.menu.pdf'] = 'Hosted/PDF ';
$language['backend.files.menu.swf'] = 'Hosted/SWF ';

$language['backend.files.menu.preview'] = 'Preview';
$language['backend.files.menu.full']    = 'Full';

$language['backend.files.text.key']         = 'File ID';
$language['backend.files.text.state']       = 'Active: ';
$language['backend.files.text.deleted']     = 'Deleted: ';
$language['backend.files.text.approved']    = 'Approved: ';
$language['backend.files.text.privacy']     = 'Privacy: ';
$language['backend.files.text.upload.date'] = 'Upload date/time';

$language['backend.files.text.ads.video'] = 'Manage video ads';
$language['backend.files.text.ads.audio'] = 'Manage audio ads';
$language['backend.files.text.subs']      = 'Manage subtitles';
$language['backend.files.text.banner']    = 'Manage banner ads';
$language['backend.files.text.paths']     = 'Server file paths';
$language['backend.files.text.log.v']     = 'Video encoding log';
$language['backend.files.text.log.i']     = 'Image encoding log';
$language['backend.files.text.log.a']     = 'Audio encoding log';
$language['backend.files.text.log.d']     = 'Document encoding log';
$language['backend.files.text.prerolls']  = 'Prerolls: ';
$language['backend.files.text.midrolls']  = 'Midrolls: ';
$language['backend.files.text.postrolls'] = 'Postrolls: ';
$language['backend.files.text.subtitles'] = 'Assign subtitles to: ';
$language['backend.files.text.blog']      = 'Edit blog HTML';

$language['backend.files.text.approve.s0'] = 'Notify user subscribers';
$language['backend.files.text.approve.s1'] = 'Send new upload notification to subscribers';
$language['backend.files.text.approve.s2'] = 'Do not send any notification';

$language['backend.files.text.thumbs']  = 'Generate thumbnails';
$language['backend.files.text.preview'] = 'Generate preview';

$language['backend.files.menu.short']   = 'Short Files';
$language['backend.files.text.log.s']   = 'Short encoding log';
$language['backend.files.text.edit.fd'] = 'Edit entry';
