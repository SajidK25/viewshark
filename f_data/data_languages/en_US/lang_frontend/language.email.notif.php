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

$site_name = $cfg['website_shortname'];

$language['recovery.forgot.password.subject']    = 'Your ' . $site_name . ' Password';
$language['recovery.forgot.username.subject']    = 'Your ' . $site_name . ' Username';
$language['payment.notification.sub.pay.fe']     = $site_name . ' Subscription Payout';
$language['payment.notification.subject.be']     = $site_name . ' Payment Notification from ';
$language['payment.notification.subject.sub.be'] = $site_name . ' Subscription Payment Notification from ';
$language['payment.notification.subject.fe']     = $site_name . ' Membership Update';
$language['payment.notification.subject.sub.fe'] = $site_name . ' Subscription Update';
$language['mail.verif.welcome']                  = 'Welcome to ' . $site_name;
$language['mail.verif.subject']                  = 'Verify your ' . $site_name . ' account';
$language['mail.address.confirm.subject']        = $site_name . ' Email Confirmation';
$language['mail.del.account.subject']            = ' has requested deleting of personal account';
$language['invite.contact.username.subject']     = ' has invited you to become friends';
$language['invite.contact.other.subject']        = ' has invited you to join ' . $site_name;
$language['subscribe.channel.subject']           = ' has subscribed to you on ' . $site_name;
$language['follow.channel.subject']              = ' has followed you on ' . $site_name;
$language['post.comment.subject']                = ' has posted a comment on your channel';
$language['post.comment.file.tpl']               = ' has posted a comment on ';
$language['post.comment.file.subject']           = 'New comment posted on ';
$language['new.upload.subject']                  = ' has uploaded a new ';
$language['new.upload.subject.live']             = ' has started a new ';
$language['new.member.subject']                  = 'New Member Registration on ' . $site_name;
$language['shared.playlist.subject']             = ' has sent you a playlist: ';
$language['shared.file.subject']                 = ' has sent you this ';
$language['file.flagged.subject']                = ' has flagged a new ';
$language['response.file.subject']               = 'New ##TYPE## response to ';
$language['mobile.feedback.subject']             = 'Feedback from your mobile visitors';
$language['footer.contact.form.subject']         = $site_name . ' Contact Form';
$language['affiliate.subject.approved']          = $site_name . ' Affiliate Account Approved';
$language['affiliate.subject.denied']            = $site_name . ' Affiliate Request Denied';
$language['affiliate.subject.closed']            = $site_name . ' Affiliate Account Closed';
$language['affiliate.subject.new']               = $site_name . ' New Affiliate Request';
$language['affiliate.subject.cancel']            = $site_name . ' Cancel Affiliate Request';
$language['partner.subject.approved']            = $site_name . ' Partner Account Approved';
$language['partner.subject.denied']              = $site_name . ' Partner Request Denied';
$language['partner.subject.closed']              = $site_name . ' Partner Account Closed';
$language['partner.subject.new']                 = $site_name . ' New Partner Request';
$language['partner.subject.cancel']              = $site_name . ' Cancel Partner Request';
$language['post.comment.file.subject.reply']     = 'New reply to your comment on ';
$language['post.comment.reply.file.tpl']         = ' has replied to your comment on ';
$language['post.comment.reply.subject']          = ' has replied to a comment you posted on a channel';

$language['mobile.notif.txt1']   = 'The following feedback has been submitted from your mobile platform:';
$language['mobile.notif.txt2']   = 'Name';
$language['mobile.notif.txt3']   = 'Message';
$language['response.notif.txt1'] = 'has posted this ##TYPE## in response to ';

$language['backend.notif.signup.new']   = 'A new user has joined ';
$language['backend.notif.signup.user']  = 'Username';
$language['backend.notif.signup.email'] = 'Email address';
$language['backend.notif.signup.ip']    = 'IP address';

$language['file.flagging.txt1'] = ' has flagged the following ##TYPE## for ';
$language['file.sharing.txt1']  = ' has shared this ##TYPE## with you on ' . $site_name;

$language['recovery.forgot.password.h2']   = 'Hi ';
$language['recovery.forgot.password.txt1'] = 'You are receiving this email because a new password was requested for your account on';
$language['recovery.forgot.password.txt2'] = 'If you did not request a new password for this account, ignore this email and continue to use your current password.';
$language['recovery.forgot.password.txt3'] = 'If you do wish to reset your password, please use the following link:';
$language['email.notif.general.txt1']      = 'See you back on ';
$language['email.notif.general.txt2']      = '- The';
$language['email.notif.general.txt3']      = 'Team';
$language['recovery.forgot.username.txt1'] = 'The following username is attached to this email address:';
$language['recovery.forgot.username.txt2'] = 'We hope this helps.';
$language['recovery.general.code']         = 'Code ';

$language['payment.notif.fe.txt1'] = 'Your payment was successfully received! Thank you for subscribing! Your subscription details are as followed:';
$language['payment.notif.fe.txt2'] = 'Package: ';
$language['payment.notif.fe.txt3'] = 'Paid: ';
$language['payment.notif.fe.txt4'] = 'Expires: ';
$language['payment.notif.fe.txt5'] = ' Payment for Membership: ';
$language['payment.notif.be.txt1'] = 'A new payment has been successfully received! Details below:';
$language['payment.notif.be.txt2'] = 'Subscriber: ';
$language['payment.notif.be.txt4'] = 'Channel: ';
$language['payment.notif.be.txt3'] = 'Full Transaction Details below: ';

$language['mail.verif.notif.fe.txt0']  = 'Confirm your email address';
$language['mail.verif.notif.fe.txt1']  = 'confirm your email address';
$language['mail.verif.notif.fe.txt2']  = ' to start participating in the ' . $site_name . ' community!';
$language['mail.verif.notif.fe.txt3']  = 'Thank You for Signing Up, ';
$language['mail.verif.notif.fe.txt4']  = 'You\'ve taken the next step in becoming part of the ' . $site_name . ' community. Now that you\'re a member, you can view files, but to leave comments, rate or upload your own files to the site, you\'ll first need to';
$language['mail.verif.notif.fe.txt5']  = 'If the link does not appear, you can paste the following link into your browser:';
$language['mail.verif.notif.fe.txt6']  = 'Please read <b>Terms of Use</b> and <b>Copyright Information</b> before uploading so that you understand what is allowed on the site.';
$language['mail.verif.notif.fe.txt7']  = 'To get you started, here are some of the fun things you can do with ' . $site_name . ':';
$language['mail.verif.notif.fe.txt8']  = 'Upload and share your files worldwide';
$language['mail.verif.notif.fe.txt9']  = 'Browse millions of original videos uploaded by community members';
$language['mail.verif.notif.fe.txt10'] = 'Find, join and create groups to connect with people who have similar interests';
$language['mail.verif.notif.fe.txt11'] = 'Customize your experience with playlists and subscriptions';
$language['mail.verif.notif.fe.txt12'] = 'Integrate ' . $site_name . ' in other websites using embedding';
$language['mail.verif.notif.fe.txt13'] = 'There\'s a lot more to explore, and more features are always in the works. Thanks for signing up, and we hope you enjoy the site!';
$language['mail.verif.notif.fe.txt14'] = 'You are receiving this email because a ' . $site_name . ' user created an account with this email address. If you are the owner of this email address and did not create the ' . $site_name . ' account, just ignore this message and the account will remain inactive.';
$language['mail.verif.notif.fe.txt15'] = 'Your account is currently pending approval and will be updated within the next hours!';
$language['mail.verif.notif.fe.txt16'] = 'We are glad you have chosen to be a part of our community and we hope you enjoy your stay.';

$language['mail.confirm.email.txt1'] = 'Please <a href="##LINK##">click here</a> to confirm your email.';
$language['mail.confirm.email.txt2'] = 'Once you confirm that this is your email address, you will be able to upload files to ' . $site_name . '.';
$language['mail.confirm.email.txt3'] = 'If the "click here" link is not supported by your email program, click this link or copy/paste it into your web browser: ';

$language['mail.notif.txt1']       = ' has sent you a message: ';
$language['mail.account.del.txt1'] = ' has requested removal of personal account on ' . $site_name . '. The account has been suspended. Use the backend delete functions to permanently remove the username and/or all belonging files.';

$language['mail.invite.email.txt1'] = 'has invited you to join ' . $site_name . ', a website where you can:';
$language['mail.invite.email.txt2'] = 'Watch videos, add photo albums, create audio playlists';
$language['mail.invite.email.txt3'] = 'Share your favorites with your friends and family';
$language['mail.invite.email.txt4'] = 'Connect with other users who share your interests';
$language['mail.invite.email.txt5'] = 'Upload your files to a worldwide audience';
$language['mail.invite.email.txt6'] = 'You can accept this invitation and add them as a friend by clicking <a href="##LINK##">here</a>, or by following this link:';
$language['mail.invite.email.more'] = ' Becoming friends makes it easier to keep track of what your friends are favoriting, uploading, or rating, and makes it easier to share public or private media.';
$language['mail.invite.email.txt7'] = 'has invited you to become friends on ' . $site_name . '.' . $language['mail.invite.email.more'];
$language['mail.invite.email.txt8'] = 'You can accept or reject this invitation by visiting your Inbox.';

$language['mail.subscribe.email.s.txt1'] = 'Want to return the favor and subscribe to ##USER##?';
$language['mail.subscribe.email.s.txt2'] = 'Just <a href="##CHURL##">visit ##USER##\'s channel</a> and click the "Subscribe" button.';
$language['mail.subscribe.email.s.txt3'] = 'Subscribing to a channel gives you special privileges, such as ad-free viewing, access to full length vods and videos, chatting during subscribers-only mode, and more.';
$language['mail.subscribe.email.s.txt4'] = 'You can see new activity from your subscriptions on <a href="' . $cfg['main_url'] . '">your ' . $site_name . ' homepage</a>.';

$language['mail.subscribe.email.f.txt1'] = 'Want to return the favor and follow ##USER##?';
$language['mail.subscribe.email.f.txt2'] = 'Just <a href="##CHURL##">visit ##USER##\'s channel</a> and click the "Follow" button.';
$language['mail.subscribe.email.f.txt3'] = 'Following channels allows you to connect with other people and be notified when they go live or upload new files.';
$language['mail.subscribe.email.f.txt4'] = 'You can see new activity from your followed channels on <a href="' . $cfg['main_url'] . '">your ' . $site_name . ' homepage</a>.';

$language['mail.upload.email.txt1'] = ' has uploaded a new ';
$language['mail.upload.email.txt2'] = 'You can unsubscribe from notifications for this user by visiting <a href="##SUBURL##">User Subscriptions</a> or <a href="##CHURL##">##USER##</a>\'s channel page.';

$language['mail.share.pl.mailto.email.txt']     = 'Email To:';
$language['mail.share.pl.mailto.email.txt.tip'] = 'Enter email addresses, separated by commas.<br /> Maximum 200 characters.';
$language['mail.share.pl.mailto.email.msg']     = '<b>Add a personal message:</b> (optional)';
$language['mail.share.pl.mailto.txt']           = ' has shared a playlist with you on ' . $site_name;

$language['mail.digest.h1']       = 'Your Personal ' . $cfg['website_shortname'] . ' Digest - ' . date("M d, Y");
$language['mail.digest.latest']   = 'Latest subscription updates';
$language['mail.digest.no.files'] = 'No recent files';

$language['payment.notification.token.subj']     = $site_name . ' Token Purchase';
$language['payment.notification.donate.subj']    = $site_name . ' Token Donation';
$language['payment.notification.token.subj.be']  = $site_name . ' Token Payment Notification from ';
$language['payment.notification.donate.subj.be'] = $site_name . ' Token Donation: ##USER1## donated ##NR## to ##USER2##';
$language['payment.notification.tk.pay.fe']      = $site_name . ' Token Payout';
$language['payment.notif.token.txt1']            = 'Your have successfully purchased ##NR## tokens!';
$language['payment.notif.donate.txt1']           = '##USER## has just donated you ##NR## tokens!';
$language['payment.notif.donate.txt2']           = '##USER1## has just donated ##NR## tokens to ##USER2##!';
$language['payment.notif.token.txt2']            = 'The account ##USER## has successfully purchased ##NR## tokens for ##PAID##.';
$language['account.entry.payout.tk.text']        = 'A new token payment has been issued to your PayPal address! Details below:';
