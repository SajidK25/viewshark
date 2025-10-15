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

defined('_ISADMIN') or header('Location: /error');

$language['backend.sub.dash.payout.nores']         = 'Sorry, no results were found.';
$language['backend.sub.dash.payout.sublist']       = 'List Subscribers and Figures';
$language['backend.sub.dash.payout.receiving']     = '<b>##USER##</b> is receiving <b>##PERC##%</b> from every subscription.';
$language['backend.sub.dash.payout.subscriptions'] = 'subscriptions';
$language['backend.sub.dash.payout.subscribers']   = 'subscribers';
$language['backend.sub.dash.payout.subpayout']     = 'subscription payout';
$language['backend.sub.dash.payout.paynow']        = 'Pay Now';
$language['backend.sub.dash.payout.paid']          = 'Paid';
$language['backend.sub.dash.payout.subscription']  = 'Subscription: ';
$language['backend.sub.dash.payout.owed']          = 'owed';
$language['backend.sub.dash.payout.owed.total']    = 'Owed Total: ';
$language['backend.sub.dash.payout.min.subs']      = 'Min. Subscriptions';
$language['backend.sub.dash.payout.max.subs']      = 'Max. Subscriptions';
$language['backend.sub.dash.head1.week']           = 'Revenue This Week';
$language['backend.sub.dash.head2.week']           = 'Revenue Last Week';
$language['backend.sub.dash.head.week.nr']         = 'Revenue Week #';
$language['backend.sub.dash.subs.week.nr']         = 'Subscriptions Week #';
$language['backend.sub.dash.head.month.nr']        = 'Revenue in ';
$language['backend.sub.dash.subs.month.nr']        = 'Subscriptions in ';
$language['backend.sub.dash.head3.week']           = 'Subscriptions This Week';
$language['backend.sub.dash.head4.week']           = 'Subscriptions Last Week';
$language['backend.sub.dash.head1.month']          = 'Revenue This Month';
$language['backend.sub.dash.head2.month']          = 'Revenue Last Month';
$language['backend.sub.dash.head3.month']          = 'Subscriptions This Month';
$language['backend.sub.dash.head4.month']          = 'Subscriptions Last Month';
$language['backend.sub.dash.head1.year']           = 'Revenue This Year';
$language['backend.sub.dash.head2.year']           = 'Revenue Last Year';
$language['backend.sub.dash.head3.year']           = 'Subscriptions This Year';
$language['backend.sub.dash.head4.year']           = 'Subscriptions Last Year';
$language['backend.sub.label.total']               = 'Total Revenue';
$language['backend.sub.label.shared']              = 'Shared Revenue';
$language['backend.sub.label.earned']              = 'Earned Revenue';
$language['backend.sub.label.stats']               = 'Revenue Statistics';
$language['backend.sub.label.wnr']                 = 'Week #';
$language['backend.sub.label.ynr']                 = 'Year ';
$language['backend.sub.label.mnr']                 = '';
$language['backend.sub.label.week']                = 'Weekly';
$language['backend.sub.label.month']               = 'Monthly';
$language['backend.sub.label.year']                = 'Yearly';
$language['backend.sub.label.months']              = array(1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May', 6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');

$language['backend.token.dash.subs.week.nr']    = 'Token Purchases Week #';
$language['backend.token.dash.subs.month.nr']   = 'Token Purchases in ';
$language['backend.token.dash.head3.week']      = 'Token Purchases This Week';
$language['backend.token.dash.head4.week']      = 'Token Purchases Last Week';
$language['backend.token.dash.head3.month']     = 'Token Purchases This Month';
$language['backend.token.dash.head4.month']     = 'Token Purchases Last Month';
$language['backend.token.dash.head3.year']      = 'Token Purchases This Year';
$language['backend.token.dash.head4.year']      = 'Token Purchases Last Year';
$language['backend.token.dash.head1.week']      = 'Token Revenue This Week';
$language['backend.token.dash.head2.week']      = 'Token Revenue Last Week';
$language['backend.token.dash.head.week.nr']    = 'Token Revenue Week #';
$language['backend.token.dash.head.month.nr']   = 'Token Revenue in ';
$language['backend.token.dash.head1.month']     = 'Token Revenue This Month';
$language['backend.token.dash.head2.month']     = 'Token Revenue Last Month';
$language['backend.token.dash.head1.year']      = 'Token Revenue This Year';
$language['backend.token.dash.head2.year']      = 'Token Revenue Last Year';
$language['backend.token.dash.payout.min.subs'] = 'Min. Tokens';
$language['backend.token.dash.payout.max.subs'] = 'Max. Tokens';
$language['backend.token.dash.payout.tkpayout'] = 'token payout';
$language['backend.sub.label.estimated']        = 'Estimated Revenue';
$language['backend.sub.label.total.p']          = 'Purchase Total';
$language['backend.tok.dash.subs.month.nr']     = 'Donations in ';
$language['backend.tok.dash.head3.week']        = 'Donations This Week';
$language['backend.tok.dash.head4.week']        = 'Donations Last Week';
$language['backend.tok.dash.head3.year']        = 'Donations This Year';
$language['backend.tok.dash.head4.year']        = 'Donations Last Year';
$language['backend.tok.dash.head3.month']       = 'Donations This Month';
$language['backend.tok.dash.head4.month']       = 'Donations Last Month';
$language['backend.tok.dash.subs.week.nr']      = 'Donations Week #';

$language['backend.sub.dash.payout.total']  = 'purchase';
$language['backend.sub.dash.payout.noaddr'] = 'Payment address has not been set!';
$language['backend.sub.dash.payout.ppfee']  = 'PayPal fee';
