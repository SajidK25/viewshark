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

$language['msg.entry.inbox']         = 'Inbox';
$language['msg.entry.sent']          = 'Outbox';
$language['msg.entry.friends']       = 'Friends';
$language['msg.entry.adr.book']      = 'Contacts';
$language['msg.entry.fr.invite']     = 'Requests';
$language['msg.entry.blocked.users'] = 'Blocked Users';
$language['msg.entry.spam']          = 'Spam';
$language['msg.entry.pm']            = 'Private Messages';
$language['msg.entry.comments']      = 'Comments';
$language['msg.entry.responses']     = 'Responses';
$language['msg.entry.list']          = 'Contact List';

$language['msg.entry.not.spam'] = 'Not Spam';
$language['msg.btn.compose']    = 'New Private Message';
$language['msg.btn.send']       = 'Send Message';

$language['label.add.new']    = 'Add New Label';
$language['label.rename.new'] = 'Rename Label';
$language['contact.add.new']  = 'New Contact';
$language['contact.edit.ct']  = 'Edit Contact';

$language['msg.label.from']           = 'From';
$language['msg.label.to']             = 'To';
$language['msg.label.subj']           = 'Subject';
$language['msg.label.message']        = 'Message';
$language['msg.label.date']           = 'Date';
$language['msg.label.private']        = '&nbsp;&nbsp;*PR*';
$language['msg.label.private.tip']    = '*PR* = private file';
$language['msg.label.attch.l']        = 'Attach Stream';
$language['msg.label.attch.v']        = 'Attach Video';
$language['msg.label.attch.i']        = 'Attach Image';
$language['msg.label.attch.a']        = 'Attach Audio';
$language['msg.label.attch.d']        = 'Attach Document';
$language['msg.label.attch.b']        = 'Attach Blog';
$language['msg.label.attch.own.l']    = '---- Your Streams ----';
$language['msg.label.attch.own.v']    = '---- Your Videos ----';
$language['msg.label.attch.own.i']    = '---- Your Images ----';
$language['msg.label.attch.own.a']    = '---- Your Audios ----';
$language['msg.label.attch.own.d']    = '---- Your Documents ----';
$language['msg.label.attch.own.b']    = '---- Your Blogs ----';
$language['msg.label.attch.fav.l']    = '---- Your Favorite Streams ----';
$language['msg.label.attch.fav.v']    = '---- Your Favorite Videos ----';
$language['msg.label.attch.fav.i']    = '---- Your Favorite Images ----';
$language['msg.label.attch.fav.a']    = '---- Your Favorite Audios ----';
$language['msg.label.attch.fav.d']    = '---- Your Favorite Documents ----';
$language['msg.label.attch.fav.b']    = '---- Your Favorite Blogs ----';
$language['msg.label.add']            = 'Add to Label';
$language['msg.label.clear']          = 'Clear Label';
$language['msg.friend.add']           = 'Add to Friends';
$language['msg.friend.message']       = $language['msg.btn.send'];
$language['msg.block.sel']            = 'Block Selected';
$language['msg.unblock.sel']          = 'Unblock Selected';
$language['msg.label.reply']          = 'Re: ';
$language['msg.title.reply']          = 'Reply';
$language['msg.details.block']        = 'Block User';
$language['msg.details.spam']         = 'mark as spam';
$language['msg.details.spam.capital'] = 'Mark as Spam';
$language['msg.details.invite.subj']  = 'Friend invitation from ';
$language['msg.details.att.show']     = 'Show Attachment';

$language['contacts.details.selected']  = 'Selected Contacts';
$language['contacts.details.blocked']   = 'Blocked friend';
$language['contacts.add.frstatus']      = 'Friend Status:';
$language['contacts.add.frstatus.tip']  = 'Adding someone as a friend requires their username or email address';
$language['contacts.add.noinvite']      = 'No invitation has been sent';
$language['contacts.add.nofriend']      = 'Not added to Friends';
$language['contacts.add.chan']          = 'Channel:';
$language['contacts.add.chan.tip']      = 'Listing a contact\'s channel requires their username.';
$language['contacts.add.chan.nosub']    = 'Not registered yet';
$language['contacts.add.labels']        = 'Labels: ';
$language['contacts.add.wait']          = 'Sending invite(s)... Please stand by.';
$language['contacts.friends.h2.a']      = 'Already registered?';
$language['contacts.friends.h2.a.tip']  = 'to sign in. Once you\'ve signed in you can confirm the invitation.';
$language['contacts.friends.h2.b']      = 'Need to sign up?';
$language['contacts.friends.h2.b.tip']  = 'to sign up. When you\'re done, click on the invite link again to confirm the invitation.';
$language['contacts.friends.h1.your']   = 'You have invited ##INV## to become friends';
$language['contacts.friends.your.text'] = 'You have sent the invitation ##DATE## and it will expire in ##TIME##';
$language['contacts.friends.since']     = 'Friends since ';
$language['contacts.friends.sent']      = 'Invitation sent ';
$language['contacts.friends.remove']    = 'Remove from Friends';
$language['contacts.invites.ignore']    = 'Ignore Selected';
$language['contacts.invites.approve']   = 'Approve Selected';
$language['contacts.comments.approve']  = 'Suspend Selected';

$language['bl_options']   = '(Block options)';
$language['bl_files']     = 'Block access to viewing files';
$language['bl_channel']   = 'Block channel access';
$language['bl_comments']  = 'Block any comments';
$language['bl_messages']  = 'Block personal messages';
$language['bl_subscribe'] = 'Block subscribing';
$language['bl_follow']    = 'Block following';

$language['err.send.nouser']   = 'No user named ';
$language['err.send.self']     = 'You may not message yourself!';
$language['err.send.multi']    = 'Invalid usernames have been entered!';
$language['err.no.contacts']   = 'Select an entry from your contact list';
$language['err.new.contacts']  = 'Contacts require a valid username or email address.';
$language['err.new.inv.user']  = 'The username you entered is not a known ' . $cfg['website_shortname'] . ' user.';
$language['err.self.contacts'] = 'You may not add yourself as a contact.';
$language['err.block.users']   = 'You may only block contacts who are known ' . $cfg['website_shortname'] . ' users.';
$language['err.no.messages']   = ' is not accepting private messages. Please try later.';

$language['notif.no.messages']      = 'There are no messages in this folder.';
$language['notif.send.success']     = 'Your message has been sent!';
$language['notif.contacts.added']   = 'Contact has been created.';
$language['notif.contacts.updated'] = 'Contact has been updated.';
$language['notif.contacts.present'] = 'Contact has already been added.';
