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

$language['frontend.signup.h1']               = 'Create New ' . $cfg['website_shortname'] . ' Account';
$language['frontend.signup.fb']               = 'Register with Facebook';
$language['frontend.signup.gp']               = 'Register with Google';
$language['frontend.signup.location']         = 'Location';
$language['frontend.signup.bday']             = 'Date of Birth';
$language['frontend.signup.bdayY']            = 'Date of Birth [Year]';
$language['frontend.signup.bdayM']            = 'Date of Birth [Month]';
$language['frontend.signup.bdayD']            = 'Date of Birth [Day]';
$language['frontend.signup.gender']           = 'Gender';
$language['frontend.signup.genderM']          = 'Male';
$language['frontend.signup.genderF']          = 'Female';
$language['frontend.signup.uinfo']            = 'Your username can only contain letters A-Z or numbers 0-9';
$language['frontend.signup.ucheck']           = 'Check Availability';
$language['frontend.signup.emailadd']         = 'Email Address';
$language['frontend.signup.fbusername']       = 'Set up your username';
$language['frontend.signup.fbcomplete']       = 'To complete the registration process, please choose the username for your ' . $cfg['website_shortname'] . ' account!';
$language['frontend.signup.setpass']          = 'Password';
$language['frontend.signup.setpassagain']     = 'Re-enter password';
$language['frontend.signup.captcha']          = 'Image Verification';
$language['frontend.signup.minimum']          = 'Minimum';
$language['frontend.signup.characters']       = 'characters';
$language['frontend.signup.pstrength']        = 'Password strength:';
$language['frontend.signup.reload']           = '&nbsp;reload';
$language['frontend.signup.ucheck.invalid']   = 'Invalid username';
$language['frontend.signup.email.invalid']    = 'Invalid email address';
$language['frontend.signup.ucheck.available'] = 'Available';
$language['frontend.signup.ucheck.taken']     = 'Taken';
$language['frontend.signup.ucheck.failed']    = 'Invalid';
$language['frontend.signup.extraemail']       = 'I would like to receive occasional product-related email communications that could be of interest to me (this includes updates, newsletters, offers, etc.)';
$language['frontend.signup.accept']           = 'I accept';
$language['frontend.signup.accepted']         = 'I accepted';
$language['frontend.signup.terms']            = 'Terms of Use';
$language['frontend.signup.termstext']        = 'Please review the Terms of Service and Terms of Use below: ';
$language['frontend.signup.agree']            = 'By clicking \'Continue\' below you are agreeing to the <a href="">' . $cfg['website_shortname'] . ' Terms of Use</a>, <a href="">Terms of Service</a> and <a href="">Privacy Policy</a>.';
$language['frontend.signup.create']           = 'Create Account';
$language['frontend.signup.update']           = 'Update Account';
$language['frontend.signup.months']           = $language['frontend.global.months'];
$language['frontend.signup.disabled']         = 'Registration is currently closed. Please try later.';

$language['frontend.membership.type.sel'] = 'Membership Type';
$language['frontend.pkinfo.unlimited']    = '<span class="bold">UNLIMITED</span>';
$language['frontend.pkinfo.upspace']      = ' file upload space';
$language['frontend.pkinfo.bwspace']      = ' bandwidth per month';
$language['frontend.pkinfo.liveallow']    = ' stream(s) allowed';
$language['frontend.pkinfo.blogallow']    = ' blog upload(s) allowed';
$language['frontend.pkinfo.docallow']     = ' document upload(s) allowed';
$language['frontend.pkinfo.vidallow']     = ' video upload(s) allowed';
$language['frontend.pkinfo.imgallow']     = ' image upload(s) allowed';
$language['frontend.pkinfo.audallow']     = ' audio upload(s) allowed';
$language['frontend.pkinfo.freereg']      = 'Registration is <span class="bold">FREE</span>';
$language['frontend.pkinfo.regactive']    = ' and is active ';
$language['frontend.pkinfo.costreg']      = 'Registration costs ';
$language['frontend.pkinfo.summary']      = 'Subscription Summary';
$language['frontend.pkinfo.summary.info'] = 'Your subscription details are listed below. You may continue or cancel this request.';
$language['frontend.pkinfo.pkname']       = 'Subscription Name';
$language['frontend.pkinfo.pkprice']      = 'Subscription Price';
$language['frontend.pkinfo.pkdur1']       = 'Daily,Monthly,3 months,6 months,Yearly';
$language['frontend.pkinfo.pkdur2']       = '1,30,90,180,365';
$language['frontend.pkinfo.pksubperiod']  = 'Subscription Period';
$language['frontend.pkinfo.pkmethods']    = 'Payment Methods';
$language['frontend.pkinfo.paypal']       = 'PayPal';
$language['frontend.pkinfo.pktotal']      = 'Total Price';
$language['frontend.pkinfo.diffsub']      = 'Need a different membership?';
$language['frontend.pkinfo.renew.sub']    = 'Renew Subscription';
$language['frontend.pkinfo.renew']        = 'Renew Membership';
$language['frontend.pkinfo.renew.text']   = 'Your current membership subscription has expired! Please update!';
$language['frontend.pkinfo.expired']      = 'expired: ';
$language['frontend.pkinfo.discount']     = 'Discount Code';

$language['notif.error.invalid.user']         = 'This username may not be used!';
$language['notif.error.accout.noreg']         = 'This account is not registered!';
$language['notif.error.accout.linked']        = 'This account is already linked!';
$language['notif.error.existing.email']       = 'This email may not be used!';
$language['notif.error.invalid.pass']         = 'An invalid password has been used!';
$language['notif.error.nosignup']             = 'We\'re sorry, based on the information you submitted, you are not eligible to register!';
$language['notif.error.nodomain']             = 'Registration is not allowed from this email domain!';
$language['notif.error.pass.nomatch']         = 'Passwords do not match';
$language['notif.error.incorect.captcha']     = 'Captcha verification has failed';
$language['notif.notice.signup.step1']        = 'Your information was accepted for registration. You are one step away from becoming a member!';
$language['notif.notice.signup.success']      = 'You are now registered with ';
$language['notif.notice.signup.approve']      = 'You may log in once your account has been approved!';
$language['notif.notice.signup.success.more'] = '. You may log in now, or update to a paid membership.';
$language['notif.notice.signup.payment']      = 'Please complete your account subscription to unlock all website features!';
