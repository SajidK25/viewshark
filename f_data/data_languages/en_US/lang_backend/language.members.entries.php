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

$language['backend.menu.members.entry1']          = 'Subscription System';
$language['backend.menu.members.entry1.sub1']     = 'General Setup';
$language['backend.menu.members.entry1.sub2']     = 'Membership Types';
$language['backend.menu.members.entry1.sub3']     = 'Discount Codes';
$language['backend.menu.members.entry1.sub3.tip'] = 'Allow the usage of discount codes on paid memberships or subscriptions.';
$language['backend.menu.members.entry2']          = 'Personalized Channels';
$language['backend.menu.members.entry2.sub2']     = 'Channel Types';
$language['backend.menu.members.entry3']          = 'Account Management';
$language['backend.menu.members.entry3.sub1']     = 'Admin Accounts';
$language['backend.menu.members.entry3.sub2']     = 'User Accounts';

$language['backend.menu.members.entry1.sub1.m1']            = 'Paypal';
$language['backend.menu.members.entry1.sub1.m2']            = 'Moneybookers';
$language['backend.menu.members.entry1.sub1.payments']      = 'Payments Module';
$language['backend.menu.members.entry1.sub1.payments.tip']  = 'Configuration of available payment modules';
$language['backend.menu.members.entry1.sub1.paid']          = 'Paid Memberships';
$language['backend.menu.members.entry1.sub1.paid.tip']      = 'This will allow you to charge your members for registering based on their selected subscription';
$language['backend.menu.members.entry1.sub1.scp']           = 'Enable Custom Payout for ';
$language['backend.menu.members.entry1.sub1.maps']          = 'Enable Geo Maps for ';
$language['backend.menu.members.entry1.sub1.subs']          = 'Paid Subscriptions';
$language['backend.menu.members.entry1.sub1.subs.tip']      = 'This will enable paid subscriptions on user channels';
$language['backend.menu.members.entry1.sub1.perc']          = 'Subscription Revenue Shared Percentage';
$language['backend.menu.members.entry1.sub1.rev']           = 'Subscription Revenue Sharing';
$language['backend.menu.members.entry1.sub1.rev.tip']       = 'Configure the percentage to be shared with partered users from each paid subscription';
$language['backend.menu.members.entry1.sub1.pmeth']         = 'Payment Methods';
$language['backend.menu.members.entry1.sub1.pmeth.tip']     = 'This defines the payment methods that may be used when paying or updating a membership';
$language['backend.menu.members.entry1.sub1.pp']            = 'Paypal Payments';
$language['backend.menu.members.entry1.sub1.pp.tip']        = 'This controls if Paypal payments are allowed';
$language['backend.menu.members.entry1.sub1.threshold']     = 'Subscription Payouts Threshold (per partner)';
$language['backend.menu.members.entry1.sub1.threshold.tip'] = 'Configure the minimum number of subscriptions required to trigger a payout.';
$language['backend.menu.members.entry1.sub1.pp.mail']       = 'Paypal Email';
$language['backend.menu.members.entry1.sub1.pp.mail.tip']   = 'Your Paypal email, where you will receive all subscription payments';
$language['backend.menu.members.entry1.sub1.mb.mail']       = 'Moneybookers Email';
$language['backend.menu.members.entry1.sub1.mb.mail.tip']   = 'Your Moneybookers email, where you will receive all subscription payments';

$language['backend.menu.members.entry1.sub1.pplog']      = 'Paypal Transaction Logging';
$language['backend.menu.members.entry1.sub1.pplog.tip']  = 'This controls logging of all Paypal transaction details and IPN server response';
$language['backend.menu.members.entry1.sub1.ppapi']      = 'Paypal NVP API Credentials';
$language['backend.menu.members.entry1.sub1.ppapi.tip']  = 'Credentials required for performing Express Checkout NVP API operations';
$language['backend.menu.members.entry1.sub1.ppapi.user'] = 'API Username';
$language['backend.menu.members.entry1.sub1.ppapi.pass'] = 'API Password';
$language['backend.menu.members.entry1.sub1.ppapi.sign'] = 'API Signature';

$language['backend.menu.members.entry1.sub1.pp.test']        = 'Paypal Test Mode';
$language['backend.menu.members.entry1.sub1.pp.test.tip']    = 'Allows the usage of Paypal TEST payments. <br />* When using "test mode", it is important to set your own Paypal Sandbox email account (below)';
$language['backend.menu.members.entry1.sub1.pp.sb.mail']     = 'Paypal Sandbox Email';
$language['backend.menu.members.entry1.sub1.pp.sb.mail.tip'] = 'Your Paypal Sandbox email, where you will receive all TEST payments';

$language['backend.menu.members.entry10.sub2.subj.m'] = 'Message Subject';
$language['backend.menu.members.entry10.sub2.subj.e'] = 'Email Subject';
$language['backend.menu.members.entry10.sub2.body.m'] = 'Message Text';
$language['backend.menu.members.entry10.sub2.body.e'] = 'Email Text';
$language['backend.menu.members.entry10.sub2.to.e']   = 'Email to ';
$language['backend.menu.members.entry10.sub2.to.m']   = 'Private Message to ';
$language['backend.menu.members.entry10.sub2.create'] = 'Create/Reset User Folders';
$language['backend.menu.members.entry10.sub2.pm.not'] = 'Send email notification for new private message';

$language['backend.menu.members.entry10.sub2.pu.l'] = 'Allow Live Streams';
$language['backend.menu.members.entry10.sub2.pu.v'] = 'Allow Video Uploads';
$language['backend.menu.members.entry10.sub2.pu.c'] = 'Allow Stream Live Chat';
$language['backend.menu.members.entry10.sub2.pu.r'] = 'Allow Stream Recordings/Vods';
$language['backend.menu.members.entry10.sub2.pu.i'] = 'Allow Image Uploads';
$language['backend.menu.members.entry10.sub2.pu.a'] = 'Allow Audio Uploads';
$language['backend.menu.members.entry10.sub2.pu.d'] = 'Allow Document Uploads';
$language['backend.menu.members.entry10.sub2.pu.b'] = 'Allow Blog Entries';
$language['backend.menu.members.entry10.sub2.pv.l'] = 'Allow Stream Viewing';
$language['backend.menu.members.entry10.sub2.pv.v'] = 'Allow Video Viewing';
$language['backend.menu.members.entry10.sub2.pv.i'] = 'Allow Image Viewing';
$language['backend.menu.members.entry10.sub2.pv.a'] = 'Allow Audio Viewing';
$language['backend.menu.members.entry10.sub2.pv.d'] = 'Allow Document Viewing';
$language['backend.menu.members.entry10.sub2.pv.b'] = 'Allow Blog Viewing';

$language['backend.menu.members.entry10.sub2.reset']          = 'Reset ';
$language['backend.menu.members.entry10.sub2.reset.bw']       = 'Reset Bandwidth';
$language['backend.menu.members.entry10.sub2.update.s']       = 'Update Subscription';
$language['backend.menu.members.entry10.sub2.update.m']       = 'Update Membership';
$language['backend.menu.members.entry10.sub2.update.a']       = 'Update Account';
$language['backend.menu.members.entry10.sub2.f1']             = 'server converted files';
$language['backend.menu.members.entry10.sub2.f2']             = 'user uploaded files';
$language['backend.menu.members.entry10.sub2.f3']             = 'file and channel views';
$language['backend.menu.members.entry3.sub5.nores']           = 'No results';
$language['backend.menu.members.entry3.sub5.ip']              = 'IP Address';
$language['backend.menu.members.entry3.sub5.sort']            = 'Sort Users';
$language['backend.menu.members.entry3.sub5.ip.invalid']      = 'Invalid IP Address';
$language['backend.menu.members.entry1.sub2.entry.name']      = 'Name';
$language['backend.menu.members.entry1.sub2.entry.desc']      = 'Description';
$language['backend.menu.members.entry1.sub2.entry.space']     = 'Disk Quota Limit (MB) [x]';
$language['backend.menu.members.entry1.sub2.entry.bw']        = 'Bandwidth (MB) [x]';
$language['backend.menu.members.entry1.sub2.entry.price']     = 'Price';
$language['backend.menu.members.entry1.sub2.entry.priceunit'] = 'Price Unit';
$language['backend.menu.members.entry1.sub2.entry.alimit']    = 'Audio Limit [x]';
$language['backend.menu.members.entry1.sub2.entry.ilimit']    = 'Image Limit [x]';
$language['backend.menu.members.entry1.sub2.entry.vlimit']    = 'Video Limit [x]';
$language['backend.menu.members.entry1.sub2.entry.dlimit']    = 'Document Limit [x]';
$language['backend.menu.members.entry1.sub2.entry.llimit']    = 'Stream Limit [x]';
$language['backend.menu.members.entry1.sub2.entry.blimit']    = 'Blog Limit [x]';
$language['backend.menu.members.entry1.sub2.entry.period']    = 'Subscription Period';
$language['backend.menu.members.entry1.sub2.entry.unlim']     = '<span class="greyed-out">For entries marked with</span> <span class="normal">[x]</span><span class="greyed-out">, a value of 0 means unlimited</span>';

$language['supported_currency_names'] = 'AUD,CAD,CHF,CZK,DKK,EUR,GBP,HKD,HUF,ILS,JPY,MXN,NOK,NZD,PLN,SEK,SGD,USD';
$language['supported_currency_codes'] = '&#36;,&#36;,CHF,K&#269;,kr,&#128;,&#163;,&#20803;,Ft,&#8362;,&#165;,&#36;,kr,&#36;,z&#322;,kr,&#36;,&#36;';
$language['subscription_periods']     = 'Monthly,Daily,3 Months,6 Months,Yearly,- custom -';
$language['subscription_numbers']     = '30,1,90,180,365,0';

$language['backend.menu.members.entry1.sub3.entry.name']   = 'Name/Code';
$language['backend.menu.members.entry1.sub3.entry.amount'] = 'Amount Deducted';

$language['backend.menu.members.change.email']              = 'Change Email Address for ';
$language['backend.menu.members.change.password']           = 'Change Password for ';
$language['backend.menu.members.activity.for']              = 'Activity for ';
$language['backend.menu.members.perm.for']                  = 'Permissions for ';
$language['backend.menu.members.mem.for']                   = 'Membership for ';
$language['backend.menu.members.mem.type']                  = 'Membership type ';
$language['backend.menu.members.sub.for']                   = 'Subscription for ';
$language['backend.menu.members.sub.type']                  = 'Subscription type ';
$language['backend.menu.members.activity.log']              = 'Logged Activity';
$language['backend.menu.members.server.loc']                = 'Server Locations for ';
$language['backend.menu.members.entry2.sub2.style']         = 'Styles';
$language['backend.menu.members.entry2.sub2.infl']          = 'Influences';
$language['backend.menu.members.entry2.sub2.create.custom'] = 'New Custom Field:';
$language['backend.menu.members.entry2.sub2.opt.text']      = 'Text Label';
$language['backend.menu.members.entry2.sub2.opt.input']     = 'Text Input';
$language['backend.menu.members.entry2.sub2.opt.link']      = 'Link Input';
$language['backend.menu.members.entry2.sub2.opt.select']    = 'Select Input';
$language['backend.menu.members.entry2.sub2.insert']        = 'insert';
$language['backend.menu.members.entry2.sub2.label']         = 'Label:';
$language['backend.menu.members.entry2.sub2.link.name']     = 'Link Name:';
$language['backend.menu.members.entry2.sub2.link.href']     = 'Link Href:';
$language['backend.menu.members.entry2.sub2.value']         = 'Value:';
$language['backend.menu.members.entry2.sub2.input.value']   = 'Input Value:';
$language['backend.menu.members.entry2.sub2.sel.opt']       = 'Select Options:';
$language['backend.menu.members.entry2.sub2.set.text']      = 'will be set when editing channel profile';
$language['backend.menu.members.entry2.sub2.set.img']       = 'Also allow linking to an image';
$language['backend.menu.members.entry2.sub2.this.save']     = 'update this';
$language['backend.menu.members.entry2.sub2.this.save.new'] = 'save new';
$language['backend.menu.members.entry2.sub2.this.remove']   = 'remove this';
$language['backend.menu.members.entry2.sub2.this.cancel']   = 'cancel this';
$language['backend.menu.members.entry2.sub2.del.default']   = 'Cannot delete default account!';
$language['backend.menu.members.entry2.sub2.del.m1']        = 'Delete from database / keep server files (database account removal)';
$language['backend.menu.members.entry2.sub2.del.m2']        = 'Delete from database / delete all files (complete account removal)';
$language['backend.menu.members.entry2.sub2.del.mv']        = 'Delete all video content belonging to ';
$language['backend.menu.members.entry2.sub2.del.mi']        = 'Delete all image content belonging to ';
$language['backend.menu.members.entry2.sub2.del.ma']        = 'Delete all audio content belonging to ';
$language['backend.menu.members.entry2.sub2.del.md']        = 'Delete all document content belonging to ';
$language['backend.menu.members.entry2.sub2.del.mc']        = 'Delete all channel content belonging to ';
$language['backend.menu.members.entry2.sub2.del.acct']      = 'Remove account ';
$language['backend.menu.members.entry2.sub2.no.act']        = 'No activity recorded';

$language['backend.menu.members.entry2.sub1.views']         = 'Channel View Counting';
$language['backend.menu.members.entry2.sub1.views.tip']     = 'Enable/disable counting of channel views based on unique IP addresses.';
$language['backend.menu.members.entry2.sub1.follows']       = 'User Follows';
$language['backend.menu.members.entry2.sub1.follows.tip']   = 'Allow following between website members.';
$language['backend.menu.members.entry2.sub1.subs']          = 'User Subscriptions';
$language['backend.menu.members.entry2.sub1.subs.tip']      = 'Allow subscribing between website members.';
$language['backend.menu.members.entry2.sub1.section']       = 'Allow Personalized Channels';
$language['backend.menu.members.entry2.sub1.section.tip']   = 'If disabled, any public channel profiles will no longer be accessible.';
$language['backend.menu.members.entry2.sub1.bulletins']     = 'Allow Public Bulletins';
$language['backend.menu.members.entry2.sub1.bulletins.tip'] = '"Public Bulletins" are posted to subcribers and friends homepages, and user channel page.';
$language['backend.menu.members.entry2.sub1.maps']          = 'Events (Google) Map';
$language['backend.menu.members.entry2.sub1.maps.tip']      = 'Enable/disable the use of Google Map to locate and show event locations.';
$language['backend.menu.members.entry2.sub1.avatar']        = 'User Avatars';
$language['backend.menu.members.entry2.sub1.avatar.tip']    = 'Set the allowed image formats which can be uploaded as user avatars and the maximum file size of the user avatar image file.';
$language['backend.menu.members.entry2.sub1.bg']            = 'Channel Header Image';
$language['backend.menu.members.entry2.sub1.bg.tip']        = 'Set the allowed image formats which can be uploaded as channel header background images and the maximum file size of the image file.';
$language['backend.menu.members.entry2.sub1.bgimage']       = 'Allow Channel Header Images';
$language['backend.menu.members.entry2.sub1.bgimage.tip']   = 'If enabled, users will have the option of setting a background image for their channel header.';
$language['backend.menu.members.entry2.sub1.allowed']       = 'Allowed Formats ';
$language['backend.menu.members.entry2.sub1.max']           = 'Max. file size ';
$language['backend.menu.members.entry2.sub1.kb']            = '(kilobytes)';
$language['backend.menu.members.entry10.sub2.mail.ignore']  = 'Ignore existing email account';
$language['backend.menu.members.entry10.sub2.act.datetime'] = 'Date/Time';
$language['backend.menu.members.entry10.sub2.act.active']   = 'Active';
$language['backend.menu.members.entry10.sub2.act.all']      = 'All Activity ';

$language['backend.menu.members.entry2.sub2.aff.mail']  = 'Send Confirmation Email';
$language['backend.menu.members.entry2.sub2.aff.btn1']  = 'Denied Notification';
$language['backend.menu.members.entry2.sub2.aff.btn1a'] = 'Approved Notification';
$language['backend.menu.members.entry2.sub2.aff.btn2']  = 'Terminate Notification';
$language['backend.menu.members.entry2.sub2.aff.btn3']  = 'No Notification';
$language['backend.menu.members.entry2.sub2.aff.text1'] = 'Do you wish to notify the user(s) by email about approving the affiliate account?';
$language['backend.menu.members.entry2.sub2.aff.text2'] = 'Do you wish to notify the user(s) by email about denying or terminating the affiliate account?';
$language['backend.menu.members.entry2.sub2.prt.text1'] = 'Do you wish to notify the user(s) by email about approving the partner account?';
$language['backend.menu.members.entry2.sub2.prt.text2'] = 'Do you wish to notify the user(s) by email about denying or terminating the partner account?';

$language['backend.menu.members.entry1.tok1.threshold']     = 'Token Payouts Threshold (per partner)';
$language['backend.menu.members.entry1.tok1.threshold.tip'] = 'Configure the minimum amount of token required to trigger a payout.';

$language['backend.menu.members.entry10.sub2.pu.s']        = 'Allow Short Video Uploads';
$language['backend.menu.members.entry10.sub2.pv.s']        = 'Allow Short Video Viewing';
$language['backend.menu.members.entry1.sub2.entry.slimit'] = 'Shorts Limit [x]';
$language['backend.menu.members.entry2.sub2.del.ms']       = 'Delete all video shorts content belonging to ';
