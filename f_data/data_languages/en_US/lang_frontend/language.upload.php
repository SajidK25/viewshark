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

$language['upload.text.user']       = 'Assign Username';
$language['upload.text.assign.tip'] = 'Your upload must be assigned to an existing account';
$language['upload.text.categ']      = 'Category';
$language['upload.text.btn.start']  = 'Start Uploading';
$language['upload.text.h1.select']  = 'Select category & files';
$language['upload.text.h2.select']  = 'Add files to the upload queue and click the start button.';
$language['upload.text.categ.tip']  = 'Tip: Category selection can be changed during file upload';
$language['upload.text.user.tip']   = 'Tip: Username selection can be changed during file upload';
$language['upload.text.filename']   = 'Filename';
$language['upload.text.size']       = 'Size';
$language['upload.text.status']     = 'Status';
$language['upload.text.drag']       = 'Drag files here / Queue limit: ## / ESC to stop upload.';
$language['upload.text.select']     = 'Select File(s)';
$language['upload.text.public']     = 'Your uploads will be made public within a few minutes!';

$language['upload.text.sub.limit']      = 'Subscription Details';
$language['upload.text.left']           = 'upload(s) left';
$language['upload.text.max']            = 'Maximum';
$language['upload.text.max.file']       = 'Maximum file size:';
$language['upload.text.files.text']     = '<b>files</b>';
$language['upload.text.files.uploaded'] = 'files uploaded';
$language['upload.text.file.text']      = 'File';
$language['upload.text.file.is']        = 'is';
$language['upload.text.que']            = 'Upload Que';

$language['upload.text.file.hide']  = 'click to hide';
$language['upload.text.ver.code']   = 'Verification code: ';
$language['upload.text.ver.send']   = 'Send email';
$language['upload.text.responding'] = 'You are responding to&nbsp';
$language['upload.text.r.approved'] = 'Your response will be posted after it has been approved.';

$language['upload.err.nofile']    = 'Error: No file selected.';
$language['upload.err.unable']    = 'Error: Unable to upload ';
$language['upload.err.msg.1']     = 'Error: Maximum file size is ';
$language['upload.err.msg.2']     = 'Error: ' . $language['upload.text.file.text'];
$language['upload.err.msg.3']     = ' is not of valid type: ';
$language['upload.err.msg.5']     = ' already added';
$language['upload.err.msg.6']     = 'Error: Maximum ';
$language['upload.err.msg.7']     = ' file(s) allowed';
$language['upload.err.msg.8']     = 'Invalid verification code';
$language['upload.err.msg.9']     = 'Your subscription ##TYPE## limit has been reached. ';
$language['upload.err.msg.9.1']   = 'The subscription limit has been reached. ';
$language['upload.err.msg.10']    = 'Your bandwidth limit has been reached.';
$language['upload.err.msg.11']    = 'Your disk space limit has been reached.';
$language['upload.err.msg.12']    = 'files have already been uploaded.';
$language['upload.err.msg.13']    = 'No active user accounts could be found.';
$language['upload.err.msg.14']    = 'Uploading is currently disabled! Please try again later.';
$language['upload.err.mail.ver1'] = 'Your email address has not been confirmed and your account permissions have not been activated!';
$language['upload.err.mail.ver2'] = 'If you did not receive the verification email, you can have another one sent below. Simply submit the correct verification code and check your email after a few moments.';

$language['upload.text.mem.limit']   = 'Membership Details';
$language['upload.text.info.shorts'] = 'Short video uploads should not exceed 60 seconds in length. Videos longer than 60 seconds will get automatically truncated.';
