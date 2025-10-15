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

/* change below */
$language['manage.channel.menu.general'] = 'General Setup';
$language['manage.channel.menu.modules'] = 'Channel Modules';
$language['manage.channel.menu.art']     = 'Channel Art';
$language['manage.channel.menu.display'] = 'Display Settings';

$language['manage.channel.general.url']     = 'Channel URL';
$language['manage.channel.general.title']   = 'Channel Title';
$language['manage.channel.general.tags']    = 'Channel Tags';
$language['manage.channel.general.descr']   = 'Channel Description';
$language['manage.channel.general.type']    = 'Channel Type';
$language['manage.channel.general.links']   = 'Channel Links';
$language['manage.channel.general.visible'] = 'Make Visible';
$language['manage.channel.general.profile'] = 'Include my personal details on the channel page';

$language['manage.channel.activity.cfg']     = 'Number of recent activities to display';
$language['manage.channel.channels.heading'] = 'Heading Text';
$language['manage.channel.channels.text']    = 'You may list up to 16 channels, in the order you would like them to appear.';

$language['manage.channel.about.title'] = 'Link title (max 30 characters)';
$language['manage.channel.about.url']   = 'URL';

$language['manage.channel.art.tip1'] = 'Use scroll wheel to zoom in/out';
$language['manage.channel.art.tip2'] = 'Single/double click outside crop zone to drag image';

$language['manage.channel.tab.upload']  = 'Upload Photos';
$language['manage.channel.tab.my']      = 'My Photos';
$language['manage.channel.tab.gallery'] = 'Gallery';

$language['manage.channel.upload.txt1'] = 'Upload an image to be used as background for your channel header.';
$language['manage.channel.upload.txt2'] = 'Select a photo';
$language['manage.channel.upload.txt3'] = 'Recommended channel art size: 2560 x 1440<br>Minimum channel image size: 1920 x 1080<br>Max file size: 10 MB';
$language['manage.channel.upload.err1'] = 'The photo you uploaded is too small! It must be at least 2048 pixels wide and 1152 pixels tall.';
$language['manage.channel.upload.err2'] = 'The photo you uploaded is too large! It must be max. 2560 pixels wide and 1440 pixels tall.';

$language['manage.channel.delete.txt1'] = 'Are you sure you want to delete this image?';
$language['manage.channel.delete.txt2'] = 'Note: Deleting is a permanent action and cannot be undone.';
$language['manage.channel.delete.txt3'] = 'Yes, delete it!';

$language['manage.channel.edit.btn1']      = 'Save channel header';
$language['manage.channel.edit.btn2']      = 'Set as default';
$language['manage.channel.edit.btn3']      = 'Upload another photo';
$language['manage.channel.edit.ch.header'] = 'Edit Channel Header';
$language['manage.channel.del.ch.header']  = 'Delete Header Image';

$language['manage.channel.btn.update'] = 'Update Channel';
$language['manage.channel.error.1']    = 'Error: Invalid URL detected!';
$language['manage.channel.error.2']    = 'Error: Please enter an URL address!';
$language['manage.channel.error.3']    = 'Error: Please enter a title for the URL!';
$language['manage.channel.error.4']    = 'Error: The URL could not be validated!';

$language['manage.channel.comm.who']              = 'Comment Permissions:';
$language['manage.channel.comm.spam']             = 'Spam Reports:';
$language['manage.channel.comm.spam.yes']         = 'Allow Spam reporting in comments';
$language['manage.channel.comm.spam.no']          = 'No Spam reporting in comments';
$language['manage.channel.comm.opt.all']          = 'Free for all (comments will be posted immediately)';
$language['manage.channel.comm.opt.friends']      = 'Custom (friends can automatically comment, all others require approval)';
$language['manage.channel.comm.opt.friends.only'] = 'Friends only (only my friends may comment)';
$language['manage.channel.comm.opt.appr']         = 'Approve all (comments will require approval)';
