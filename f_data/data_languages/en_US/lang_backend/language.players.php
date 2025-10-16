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

$language['backend.player.menu.jw']     = 'JW Player';
$language['backend.player.menu.flow']   = 'Flow Player';
$language['backend.player.menu.native'] = 'Native Player';

$language['backend.player.site']     = 'Website Player';
$language['backend.player.embed']    = 'Embedded Player';
$language['backend.player.bottom']   = 'bottom';
$language['backend.player.top']      = 'top';
$language['backend.player.over']     = 'over';
$language['backend.player.none']     = 'none';
$language['backend.player.false']    = 'no';
$language['backend.player.true']     = 'yes';
$language['backend.player.list']     = 'list';
$language['backend.player.always']   = 'always';
$language['backend.player.single']   = 'single';
$language['backend.player.exactfit'] = 'exactfit';
$language['backend.player.uniform']  = 'uniform';
$language['backend.player.fill']     = 'fill';
$language['backend.player.enabled']  = 'enabled';
$language['backend.player.disabled'] = 'disabled';
$language['backend.player.low']      = 'low';
$language['backend.player.medium']   = 'medium';
$language['backend.player.high']     = 'high';
$language['backend.player.html5']    = 'html5';
$language['backend.player.flash']    = 'flash';

$language['backend.player.summary.h3'] = 'Player Configuration (local / embedded)';

$language['backend.player.flow.license']               = 'key';
$language['backend.player.flow.license.tip']           = 'A valid license key removes the Flowplayer branding from the player. For example: "$688345122773207, $334773811075656"';
$language['backend.player.flow.logo']                  = 'logo';
$language['backend.player.flow.logo.tip']              = 'An absolute path to your logo.';
$language['backend.player.flow.autoplay']              = 'autoplay';
$language['backend.player.flow.autoplay.tip']          = 'Will start the playback automatically';
$language['backend.player.flow.loop']                  = 'loop';
$language['backend.player.flow.loop.tip']              = 'Starts playback again from the beginning when the video ends';
$language['backend.player.flow.preload']               = 'preload';
$language['backend.player.flow.preload.tip']           = 'Starts pre-loading the video in the background';
$language['backend.player.flow.disabled']              = 'disabled';
$language['backend.player.flow.disabled.tip']          = 'Whether playback should be forced by disabling the UI. Seeking, pausing etc. is impossible. API still works.';
$language['backend.player.flow.engine']                = 'engine';
$language['backend.player.flow.engine.tip']            = 'The first engine to try. currently supported values are "html5" and "flash"';
$language['backend.player.flow.flashfit']              = 'flashfit';
$language['backend.player.flow.flashfit.tip']          = 'whether video aspect ratio in Flash non-fullscreen mode is preserved. Only set this to true if the container\'s dimensions do not fit the video\'s aspect ratio, e.g. for playlists with clips of different aspect ratio.';
$language['backend.player.flow.fullscreen']            = 'fullscreen';
$language['backend.player.flow.fullscreen.tip']        = 'Whether fullscreen is enabled';
$language['backend.player.flow.keyboard']              = 'keyboard';
$language['backend.player.flow.keyboard.tip']          = 'Use keyboard shortcuts - enabled by default';
$language['backend.player.flow.muted']                 = 'muted';
$language['backend.player.flow.muted.tip']             = 'Whether player should start in muted state';
$language['backend.player.flow.native_fullscreen']     = 'native_fullscreen';
$language['backend.player.flow.native_fullscreen.tip'] = 'Use native fullscreen on mobile webkit browsers (iPad, Android) instead of full browser window. The screen will be bigger but native video controls will be in use instead of customizable Flowplayer controls.';
$language['backend.player.flow.rtmp']                  = 'rtmp';
$language['backend.player.flow.rtmp.tip']              = 'Location of the Flash streaming server. When provided it\'s possible to jump randomly on the video timeline on Flash mode.';
$language['backend.player.flow.splash']                = 'splash';
$language['backend.player.flow.splash.tip']            = 'Enables splash screen';
$language['backend.player.flow.tooltip']               = 'tooltip';
$language['backend.player.flow.tooltip.tip']           = 'Whether "Hit ? for help" tooltip is shown';
$language['backend.player.flow.volume']                = 'volume';
$language['backend.player.flow.volume.tip']            = 'Initial volume level';
$language['backend.player.flow.subs']                  = 'subtitle';
$language['backend.player.flow.subs.tip']              = 'Display subtitles when playing videos';

$language['backend.player.flow.analytics']          = 'tracking ID';
$language['backend.player.flow.analytics.tip']      = 'Flowplayer tracks following information when user leaves a page: 1. Seconds played. If video was repeated this is more than the duration of the video, 2. Flash or HTML5 / video format, 3. Video title or file name';
$language['backend.player.menu.flow.behavior']      = 'Behavior';
$language['backend.player.menu.flow.behavior.tip']  = 'These settings control the general behavior of the player.';
$language['backend.player.menu.flow.analytics']     = 'Analytics';
$language['backend.player.menu.flow.analytics.tip'] = 'Google Analytics > Content > Events > Top Events > Video / Seconds played. Play around with the "Secondary dimension" dropdown. For a better visual view select the pie chart. If your site already uses or is planning to use Google Analytics it\'s good to view those statistics in the same place.';

$language['backend.player.menu.jw.license']       = 'License Key';
$language['backend.player.menu.jw.license.tip']   = 'if you have purchased the Pro, Premium or Ads edition of JW Player, unlock its features of the by inserting your (server-less) JW Player license key.';
$language['backend.player.menu.jw.layout']        = 'Layout';
$language['backend.player.menu.jw.layout.tip']    = 'These are the options for configuring the layout of the player:';
$language['backend.player.menu.jw.behavior']      = 'Playback';
$language['backend.player.menu.jw.behavior.tip']  = 'These are the options for configuring the setup and playback behavior:';
$language['backend.player.menu.jw.logo']          = 'Logo';
$language['backend.player.menu.jw.logo.tip']      = 'If you have purchased a commercial version of the JW Player, you are able to re-brand it as your own.';
$language['backend.player.menu.jw.sharing']       = 'Sharing';
$language['backend.player.menu.jw.sharing.tip']   = 'Configure the Social Sharing overlay on the Premium/Ads edition.';
$language['backend.player.menu.jw.related']       = 'Related';
$language['backend.player.menu.jw.related.tip']   = 'Configure the Related Videos overlay on the Premium/Ads edition.';
$language['backend.player.menu.jw.analytics']     = 'JW Analytics';
$language['backend.player.menu.jw.analytics.tip'] = 'Configure the free JW Player analytics tracking.';
$language['backend.player.menu.jw.ga']            = 'Google Analytics';
$language['backend.player.menu.jw.ga.tip']        = 'Configure Google Analytics tracking on the Premium/Ads edition.';
$language['backend.player.menu.jw.cap']           = 'Captions';
$language['backend.player.menu.jw.cap.tip']       = 'Configure the captions of rendering area (load captions using a playlist).';
$language['backend.player.menu.jw.adv']           = 'Advertising';
$language['backend.player.menu.jw.adv.tip']       = 'Configure the VAST or IMA Advertising options on the Ads edition.';
$language['backend.player.menu.jw.rc']            = 'Right-click';
$language['backend.player.menu.jw.rc.tip']        = 'If you have purchased a commercial version of the JW Player, you are able to re-brand it as your own.';
$language['backend.player.jw.rc.abouttext']       = 'right-click.abouttext';
$language['backend.player.jw.rc.abouttext.tip']   = 'Text to display in the right-click menu. The default is About JW Player X.x.xxx.';
$language['backend.player.jw.rc.aboutlink']       = 'right-click.aboutlink';
$language['backend.player.jw.rc.aboutlink.tip']   = 'URL to link to when clicking the right-click menu. The default is http://www.longtailvideo.com/jw-player/learn-more.';

$language['backend.player.jw.layout.controls']     = 'controls';
$language['backend.player.jw.layout.controls.tip'] = 'Whether to display the video controls (controlbar, icons and dock). Can be false or true (default).';
$language['backend.player.jw.layout.skin']         = 'skin';
$language['backend.player.jw.layout.skin.tip']     = 'Which skin to use for styling the player (none by default). Is set to the URL of that skin on your site.If you have purchased the Premium or Ads edition of JW Player, you can load each of the 8 premium skins off our CDN by simply inserting the skin name (e.g. skin: "bekle").';
$language['backend.player.vjs.layout.skin.tip']    = 'Which skin to use for styling the player (none by default). You can load skins by simply inserting the skin name (e.g. skin: "bekle").';

$language['backend.player.jw.analytics.cookies']     = 'cookies';
$language['backend.player.jw.analytics.cookies.tip'] = 'By default, JW Player uses cookies to track viewers across different pages on your site. This allows you to see e.g. the number of unique or returning viewers for your videos. Since tracking of cookies requires the explicit consent of viewers in the European Union, it is possible to disable cookie tracking by settings this option to false. Basic events (start, complete) will still be captured.';
$language['backend.player.jw.analytics.enabled']     = 'enabled';
$language['backend.player.jw.analytics.enabled.tip'] = 'The JW Player Analytics service is fast, comprehensive and available for all JW Player users. If you do not want to use this service (for privacy reasons and/or if you have your own analytics), you can disable event tracking by setting this option to false. This setting is not available to users of the Free edition.';

$language['backend.player.jw.ga.idstring']           = 'idstring';
$language['backend.player.jw.ga.idstring.tip']       = 'By default, JW Player sets the action of a play/complete event to the file playlist property. This option allows setting the action to a different playlist item property, like title or mediaid.';
$language['backend.player.jw.ga.trackingobject']     = 'trackingobject';
$language['backend.player.jw.ga.trackingobject.tip'] = 'By default, JW Player presumes the Google Analytics library is available in JavaScript as a global variable called _gaq. This is the variable Google Analytics uses in all its examples and documentation. If you gave the Google Analytics object a different name (e.g. pageTracker), set this option to notify the JW Player.';

$language['backend.player.jw.cap.back']      = 'back';
$language['backend.player.jw.cap.back.tip']  = 'By default, a black background is displayed around the captions. Set this option to false, to discard the background and print a thin black outline around the captions instead. Note this outline will not be available on Internet Explorer 9 in HTML5 mode! It will in IE10 though.';
$language['backend.player.jw.cap.color']     = 'color';
$language['backend.player.jw.cap.color.tip'] = 'Can be any hexadecimal color value, the default is FFFFFF.';
$language['backend.player.jw.cap.size']      = 'fontsize';
$language['backend.player.jw.cap.size.tip']  = 'By default, the captions are displayed using a font size that fits 80 characters per line. Use this option to override this selection with a specific fontsize in pixels (e.g. 20). Note the captions are still scaled up during fullscreen playback.';

$language['backend.player.vjs.behavior.autostart']     = 'autoplay';
$language['backend.player.vjs.behavior.autostart.tip'] = 'Set this to true to automatically start the player on load.';
$language['backend.player.vjs.behavior.loop']          = 'loop';
$language['backend.player.vjs.behavior.loop.tip']      = 'Causes the video to start over as soon as it ends.';
$language['backend.player.vjs.behavior.muted']         = 'muted';
$language['backend.player.vjs.behavior.muted.tip']     = 'Will silence any audio by default.';
$language['backend.player.vjs.behavior.related']       = 'related';
$language['backend.player.vjs.behavior.related.tip']   = 'Show related content at the end of playback';
$language['backend.player.vjs.logo.image']             = 'image';
$language['backend.player.vjs.logo.image.tip']         = 'The URL to the image to be used as the watermark.';
$language['backend.player.vjs.logo.position']          = 'position';
$language['backend.player.vjs.logo.position.tip']      = 'The location to place the watermark (top-left, top-right, bottom-left, bottom-right). Defaults to "top-right"';
$language['backend.player.vjs.logo.url']               = 'url';
$language['backend.player.vjs.logo.url.tip']           = 'A url to be linked to from the watermark. If the user clicks the watermark the video will be paused and the link will open in a new window.';
$language['backend.player.vjs.logo.fade']              = 'fadeTime';
$language['backend.player.vjs.logo.fade.tip']          = 'The amount of time in milliseconds for the initial watermark fade. Defaults to 3000. To make watermark permanently visible, leave empty';
$language['backend.player.menu.vjs.layout']            = 'Layout';
$language['backend.player.menu.vjs.layout.tip']        = 'These are the options for configuring the layout of the player:';
$language['backend.player.menu.vjs.behavior']          = 'Playback';
$language['backend.player.menu.vjs.behavior.tip']      = 'These are the options for configuring the setup and playback behavior';
$language['backend.player.menu.vjs.logo']              = 'Logo/Watermark';
$language['backend.player.menu.vjs.logo.tip']          = 'Brand the player with your image';
$language['backend.player.menu.vjs.rc']                = 'Right Click Menu';
$language['backend.player.menu.vjs.rc.tip']            = 'Set up a Right Click popup menu with custom links';
$language['backend.player.menu.vjs.related']           = 'Related';
$language['backend.player.menu.vjs.related.tip']       = 'Show related content at the end of playback';
$language['backend.player.vjs.rc.abouttext1']          = 'right-click.abouttext-1';
$language['backend.player.vjs.rc.abouttext1.tip']      = 'Text to display in the right-click menu.';
$language['backend.player.vjs.rc.aboutlink1']          = 'right-click.aboutlink-1';
$language['backend.player.vjs.rc.aboutlink1.tip']      = 'URL to link to when clicking the menu item.';
$language['backend.player.vjs.rc.abouttext2']          = 'right-click.abouttext-2';
$language['backend.player.vjs.rc.abouttext2.tip']      = 'Text to display in the right-click menu.';
$language['backend.player.vjs.rc.aboutlink2']          = 'right-click.aboutlink-2';
$language['backend.player.vjs.rc.aboutlink2.tip']      = 'URL to link to when clicking the menu item.';
$language['backend.player.vjs.rc.abouttext3']          = 'right-click.abouttext-3';
$language['backend.player.vjs.rc.abouttext3.tip']      = 'Text to display in the right-click menu.';
$language['backend.player.vjs.rc.aboutlink3']          = 'right-click.aboutlink-3';
$language['backend.player.vjs.rc.aboutlink3.tip']      = 'URL to link to when clicking the menu item.';
$language['backend.player.vjs.rc.abouttext4']          = 'right-click.abouttext-4';
$language['backend.player.vjs.rc.abouttext4.tip']      = 'Text to display in the right-click menu.';
$language['backend.player.vjs.rc.aboutlink4']          = 'right-click.aboutlink-4';
$language['backend.player.vjs.rc.aboutlink4.tip']      = 'URL to link to when clicking the menu item.';
$language['backend.player.vjs.rc.abouttext5']          = 'right-click.abouttext-5';
$language['backend.player.vjs.rc.abouttext5.tip']      = 'Text to display in the right-click menu.';
$language['backend.player.vjs.rc.aboutlink5']          = 'right-click.aboutlink-5';
$language['backend.player.vjs.rc.aboutlink5.tip']      = 'URL to link to when clicking the menu item.';
$language['backend.player.menu.vjs.adv']               = 'Advertising';
$language['backend.player.menu.vjs.adv.tip']           = 'Enable or disable advertising support for VAST and IMA.';

$language['backend.player.jw.behavior.autostart']     = 'autostart';
$language['backend.player.jw.behavior.autostart.tip'] = 'Set this to true to automatically start the player on load.';
$language['backend.player.jw.behavior.fallback']      = 'fallback';
$language['backend.player.jw.behavior.fallback.tip']  = 'Whether to render a nice download link for the video if HTML5 and/or Flash are not supported. Can be true (a fallback is rendered) or false (the original HTML is not touched). Defaults to true.';
$language['backend.player.jw.behavior.mute']          = 'mute';
$language['backend.player.jw.behavior.mute.tip']      = 'Whether to have the sound muted on startup or not. Can be false (default) or true.';
$language['backend.player.jw.behavior.primary']       = 'primary';
$language['backend.player.jw.behavior.primary.tip']   = 'Which rendering mode to try first for rendering the player. Can be html5 (default) or flash.';
$language['backend.player.jw.behavior.repeat']        = 'repeat';
$language['backend.player.jw.behavior.repeat.tip']    = 'Whether to loop playback of the playlist or not. Can be true (keep playing forever) or false (stop playback when completed). Defaults to false.';
$language['backend.player.jw.behavior.stretch']       = 'stretching';
$language['backend.player.jw.behavior.stretch.tip']   = 'How to resize the poster and video to fit the display. Can be none (keep original dimensions), exactfit (stretch disproportionally), uniform (stretch proportionally; black borders) or fill (stretch proportionally; parts cut off). Defaults to uniform.';
$language['backend.player.jw.logo.file']              = 'logo.file';
$language['backend.player.jw.logo.file.tip']          = 'Location of an external JPG, PNG or GIF image to be used as watermark (e.g. /assets/logo.png). We recommend using 24 bit PNG images with transparency, since they blend nicely with the video.';
$language['backend.player.jw.logo.link']              = 'logo.link';
$language['backend.player.jw.logo.link.tip']          = 'HTTP URL to jump to when the watermark image is clicked (e.g. http://example.com/). If it is not set, a click on the watermark does nothing in particular.';
$language['backend.player.jw.logo.hide']              = 'logo.hide';
$language['backend.player.jw.logo.hide.tip']          = 'By default (false), the logo remains visible all the time. When this option is set to true, the logo will automatically show and hide along with the other player controls.';
$language['backend.player.jw.logo.margin']            = 'logo.margin';
$language['backend.player.jw.logo.margin.tip']        = 'The distance of the logo from the edges of the display. The default is 8 pixels.';
$language['backend.player.jw.logo.position']          = 'logo.position';
$language['backend.player.jw.logo.position.tip']      = 'This sets the corner in which to display the watermark. It can be top-right (the default), top-left, bottom-right or bottom-left. Note the default position is preferred, since the logo won\'t interfere with the controlbar, captions, overlay ads and dock buttons.';

$language['backend.player.jw.adv.client']     = 'client';
$language['backend.player.jw.adv.client.tip'] = 'Set this to vast if you are running VAST/VPAID ads, or to googima if you are running Google IMA ads. Note you cannot mix both ad formats in a single player embed.';
$language['backend.player.jw.adv.tag']        = 'tag';
$language['backend.player.jw.adv.tag.tip']    = 'Set this to the URL of the ad tag that contains the pre-roll ad. Only linear ads can be run as pre-rolls; if you want to run e.g. an overlay you need to set a schedule.';
$language['backend.player.jw.adv.msg']        = 'admessage';
$language['backend.player.jw.adv.msg.tip']    = 'During playback of linear ads (both VAST and IMA), JW Player displays a friendly ad message to notify viewers. The string XX in this option will automatically be replaced with a countdown towards the end of the ad. (Use a language variable name).';

$language['backend.player.jw.plugin.enabled']     = 'enabled';
$language['backend.player.jw.plugin.enabled.tip'] = 'Enabled or disable this plugin.';

$language['backend.player.jw.sharing.link']         = 'link';
$language['backend.player.jw.sharing.link.tip']     = 'URL to display in the video link field. If no link is set, the URL of the current page is used.';
$language['backend.player.jw.sharing.code']         = 'code';
$language['backend.player.jw.sharing.code.tip']     = 'Embed code to display in the embed code field. If no code is set, the field is not shown.';
$language['backend.player.jw.sharing.head']         = 'heading';
$language['backend.player.jw.sharing.head.tip']     = 'Short, instructive text to display at the top of the sharing screen. The default is Share Video. (Use a language variable name).';
$language['backend.player.jw.related.file']         = 'file';
$language['backend.player.jw.related.file.tip']     = 'Location of an RSS file with related videos, e.g. http://example.com/related.xml.';
$language['backend.player.jw.related.click']        = 'onclick';
$language['backend.player.jw.related.click.tip']    = 'This determines what to do when the user clicks a thumbnail: jump to the page URL of the related video (link) or play the related video inline (play). The default is link.';
$language['backend.player.jw.related.complete']     = 'oncomplete';
$language['backend.player.jw.related.complete.tip'] = 'Whether to display the related videos screen when the video is completed. When set to false, the screen does not automatically pop up. It is true by default.';
$language['backend.player.jw.related.head']         = 'heading';
$language['backend.player.jw.related.head.tip']     = 'Single line heading displayed above the grid with related videos. Generally contains a short call-to-action. The default is Related Videos. (Use a language variable name).';
