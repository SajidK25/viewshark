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

//do not change or translate ##TYPE##
//-----------------------------------

$language['notif.error.required.field']        = 'A required field is missing: ';
$language['notif.message.public']              = 'This ##TYPE## is public.';
$language['notif.message.private']             = 'This ##TYPE## is private.';
$language['notif.message.pending']             = 'This ##TYPE## is pending approval. Only you may view it.';
$language['notif.message.personal']            = 'This ##TYPE## is personal. Only you may view it.';
$language['notif.error.deleted.file']          = 'This ##TYPE## has been removed!';
$language['notif.error.available.file']        = 'This ##TYPE## is not available now. Please try later.';
$language['notif.error.invalid.request']       = 'The request could not be processed!';
$language['notif.error.blocked.request']       = 'The request was blocked! Please try later.';
$language['notif.error.invalid.progress']      = 'A request is already in progress! Please try later.';
$language['notif.error.invalid.request.extra'] = ' (or was cancelled by the user)';
$language['notif.error.invalid.path']          = 'The path could not be found: ';
$language['notif.success.request']             = 'Request completed successfully!';
$language['notif.success.feedback']            = 'Thank you for your feedback!';
$language['notif.success.subscribe']           = 'Thank you for subscribing! Your account will be updated shortly.';
$language['notif.success.subscribe.extra']     = 'Thank you for subscribing! Please refresh the page if the chat notification does not show within a minute.';
$language['notif.success.subscribe.cancel']    = 'Your subscription cancellation request was sent! Your account will be updated shortly.';
$language['notif.success.verified']            = 'Congratulations! Your account has been successfully verified.';
$language['notif.mail.alt.body']               = 'To view this message, please use an HTML compatible email viewer!';
$language['notif.no.multiple.select']          = 'No entries selected. Please select one or more entries.';
$language['notif.no.messages.select']          = 'No messages selected. Please select one or more messages.';
$language['notif.no.contacts.select']          = 'No contacts selected. Please select one or more contacts.';
$language['notif.confirm.delete']              = 'Please confirm permanently deleting this entry!';
$language['notif.confirm.delete.type']         = 'Please confirm permanently deleting this ##TYPE##!';
$language['notif.confirm.delete.message']      = 'Please confirm permanently deleting this message!';
$language['notif.confirm.delete.label']        = 'Please confirm permanently deleting this label!';
$language['notif.confirm.delete.multi']        = 'Please confirm permanently deleting the selected entries!';
$language['notif.success.tokens']              = 'Thank you for purchasing tokens! Your account will be updated shortly.';
$language['notif.error.live.exist']            = 'Error: Another live stream is already set up';
