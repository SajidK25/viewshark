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

$language['backend.menu.entry3.sub20.mp4']  = 'MP4 Encoding';
$language['backend.menu.entry3.sub20.webm'] = 'WEBM Encoding';
$language['backend.menu.entry3.sub20.ogv']  = 'OGV Encoding';
$language['backend.menu.entry3.sub20.mob']  = 'Mobile Encoding';
$language['backend.menu.entry3.sub20.flv']  = 'FLV Encoding';

$language['backend.menu.entry3.sub6.conv.entry']     = 'Image Encoding Settings';
$language['backend.menu.entry3.sub6.conv.entry.tip'] = 'Configure size parameters for output image files.';
$language['backend.menu.entry3.sub6.conv.s1']        = 'Allow any width and height for image files (no resize) ';
$language['backend.menu.entry3.sub6.conv.s3']        = 'Resize images larger than ';

$language['backend.menu.entry6.sub1.conv.path']        = 'Encoding Plugins';
$language['backend.menu.entry6.sub1.conv.path.tip']    = 'Configure the server locations of the encoding modules';
$language['backend.menu.entry6.sub1.conv.path.ffmpeg'] = 'FFmpeg Path';
$language['backend.menu.entry6.sub1.conv.path.yamdi']  = 'Yamdi Path';
$language['backend.menu.entry6.sub1.conv.path.qt']     = 'QT-FastStart Path';
$language['backend.menu.entry6.sub1.conv.path.lame']   = 'LAME Path';
$language['backend.menu.entry6.sub1.conv.path.php']    = 'PHP Path';

$language['backend.menu.entry6.sub1.conv.fixed']                = 'Fixed';
$language['backend.menu.entry6.sub1.conv.crf.txt']              = 'CRF<span class="normal">, for 1pass</span> - Auto<span class="normal">, for 2pass</span>';
$language['backend.menu.entry6.sub1.conv.flv']                  = 'FLV / H.263 Settings';
$language['backend.menu.entry6.sub1.conv.flv.tip']              = 'Configure the server parameters for the FLV (standard) encoding';
$language['backend.menu.entry6.sub1.conv.mp3']                  = 'MP3 Settings';
$language['backend.menu.entry6.sub1.conv.mp3.tip']              = 'Configure the server parameters for the MP3 (audio) encoding.';
$language['backend.menu.entry6.sub1.conv.mp3.none']             = 'Do not re-convert MP3 files (only copy to server)';
$language['backend.menu.entry6.sub1.conv.mp4.360p']             = 'MP4 Profile: 360p';
$language['backend.menu.entry6.sub1.conv.mp4.360p.tip']         = 'Configure the server parameters for the MP4 (360p) encoding';
$language['backend.menu.entry6.sub1.conv.mp4.480p']             = 'MP4 Profile: 480p';
$language['backend.menu.entry6.sub1.conv.mp4.480p.tip']         = 'Configure the server parameters for the MP4 (480p) encoding';
$language['backend.menu.entry6.sub1.conv.mp4.720p']             = 'MP4 Profile: 720p';
$language['backend.menu.entry6.sub1.conv.mp4.720p.tip']         = 'Configure the server parameters for the MP4 (720p) encoding';
$language['backend.menu.entry6.sub1.conv.mp4.1080p']            = 'MP4 Profile: 1080p';
$language['backend.menu.entry6.sub1.conv.mp4.1080p.tip']        = 'Configure the server parameters for the MP4 (1080p) encoding';
$language['backend.menu.entry6.sub1.conv.vpx.360p']             = 'WEBM Profile: 360p';
$language['backend.menu.entry6.sub1.conv.vpx.360p.tip']         = 'Configure the server parameters for the WEBM (360p) encoding';
$language['backend.menu.entry6.sub1.conv.vpx.480p']             = 'WEBM Profile: 480p';
$language['backend.menu.entry6.sub1.conv.vpx.480p.tip']         = 'Configure the server parameters for the WEBM (480p) encoding';
$language['backend.menu.entry6.sub1.conv.vpx.720p']             = 'WEBM Profile: 720p';
$language['backend.menu.entry6.sub1.conv.vpx.720p.tip']         = 'Configure the server parameters for the WEBM (720p) encoding';
$language['backend.menu.entry6.sub1.conv.vpx.1080p']            = 'WEBM Profile: 1080p';
$language['backend.menu.entry6.sub1.conv.vpx.1080p.tip']        = 'Configure the server parameters for the WEBM (1080p) encoding';
$language['backend.menu.entry6.sub1.conv.ogv.360p']             = 'OGV Profile: 360p';
$language['backend.menu.entry6.sub1.conv.ogv.360p.tip']         = 'Configure the server parameters for the OGV (360p) encoding';
$language['backend.menu.entry6.sub1.conv.ogv.480p']             = 'OGV Profile: 480p';
$language['backend.menu.entry6.sub1.conv.ogv.480p.tip']         = 'Configure the server parameters for the OGV (480p) encoding';
$language['backend.menu.entry6.sub1.conv.ogv.720p']             = 'OGV Profile: 720p';
$language['backend.menu.entry6.sub1.conv.ogv.720p.tip']         = 'Configure the server parameters for the OGV (720p) encoding';
$language['backend.menu.entry6.sub1.conv.ogv.1080p']            = 'OGV Profile: 1080p';
$language['backend.menu.entry6.sub1.conv.ogv.1080p.tip']        = 'Configure the server parameters for the OGV (1080p) encoding';
$language['backend.menu.entry6.sub1.conv.flv.360p']             = 'FLV Profile: 360p';
$language['backend.menu.entry6.sub1.conv.flv.360p.tip']         = 'Configure the server parameters for the FLV (360p) encoding';
$language['backend.menu.entry6.sub1.conv.flv.480p']             = 'FLV Profile: 480p';
$language['backend.menu.entry6.sub1.conv.flv.480p.tip']         = 'Configure the server parameters for the FLV (480p) encoding';
$language['backend.menu.entry6.sub1.conv.ipad']                 = 'Mobile / H.264 Settings';
$language['backend.menu.entry6.sub1.conv.ipad.tip']             = 'Configure the server parameters for the MP4 (Mobile) encoding';
$language['backend.menu.entry6.sub1.conv.thumbs']               = 'Video Thumbnails';
$language['backend.menu.entry6.sub1.conv.thumbs.tip']           = 'Configure the video thumbnail fles to be rendered';
$language['backend.menu.entry6.sub1.conv.thumbs.prev']          = 'Rotating Previews';
$language['backend.menu.entry6.sub1.conv.thumbs.extract']       = 'Thumbnails to Extract';
$language['backend.menu.entry6.sub1.conv.thumbs.ext']           = 'Thumbnail File Type';
$language['backend.menu.entry6.sub1.conv.thumbs.w']             = 'Thumbnail Width';
$language['backend.menu.entry6.sub1.conv.thumbs.h']             = 'Thumbnail Height';
$language['backend.menu.entry6.sub1.conv.thumbs.ext.gif']       = 'GIF';
$language['backend.menu.entry6.sub1.conv.thumbs.ext.jpg']       = 'JPG';
$language['backend.menu.entry6.sub1.conv.thumbs.ext.png']       = 'PNG';
$language['backend.menu.entry6.sub1.conv.thumbs.extract.mode']  = 'Extract Mode';
$language['backend.menu.entry6.sub1.conv.thumbs.extract.split'] = 'Split';
$language['backend.menu.entry6.sub1.conv.thumbs.extract.cons']  = 'Consecutive';
$language['backend.menu.entry6.sub1.conv.thumbs.extract.rand']  = 'Random';
$language['backend.menu.entry6.sub1.conv.flv.option']           = 'Re-convert FLV files';
$language['backend.menu.entry6.sub1.conv.mp4.option']           = 'Convert to MP4';
$language['backend.menu.entry6.sub1.conv.btrate.method.option'] = 'Bitrate Method';
$language['backend.menu.entry6.sub1.conv.btrate.video.option']  = 'Video Bitrate';
$language['backend.menu.entry6.sub1.conv.btrate.audio.option']  = 'Audio Bitrate';
$language['backend.menu.entry6.sub1.conv.btrate.sample.option'] = 'Audio Sample Rate';
$language['backend.menu.entry6.sub1.conv.fps.option']           = 'FPS';
$language['backend.menu.entry6.sub1.conv.resize.option']        = 'Resize';
$language['backend.menu.entry6.sub1.conv.resize.w.option']      = 'Resize Width';
$language['backend.menu.entry6.sub1.conv.resize.h.option']      = 'Resize Height';
$language['backend.menu.entry6.sub1.conv.fixed']                = 'Fixed';
$language['backend.menu.entry6.sub1.conv.auto']                 = 'Auto';
$language['backend.menu.entry6.sub1.conv.crf']                  = 'CRF';
$language['backend.menu.entry6.sub1.conv.pass']                 = 'Encoding Pass';
$language['backend.menu.entry6.sub1.conv.mp4.1pass']            = '1 (Faster - Medium Quality)';
$language['backend.menu.entry6.sub1.conv.mp4.2pass']            = '2 (Slow - High Quality/no CRF)';
$language['backend.menu.entry6.sub1.conv.mp4.2pass.1']          = '2 (Slow - High Quality/fixed bitrate)';
$language['backend.menu.entry6.sub1.conv.active.a']             = 'Active Encoding';
$language['backend.menu.entry6.sub1.conv.active.a.tip']         = 'Disabling active MP3 encoding implies you will be uploading MP3 files only and these will not get re-converted, but only copied to the server.';
$language['backend.menu.entry6.sub1.conv.active.i']             = 'Active Encoding';
$language['backend.menu.entry6.sub1.conv.active.i.tip']         = 'Disabling active JPG encoding implies the uploaded image files will only be copied to their corresponding server location and not converted to JPG.';
$language['backend.menu.entry6.sub1.conv.active.v']             = 'Active Encoding';
$language['backend.menu.entry6.sub1.conv.active.v.tip']         = 'Disabling active video encoding implies you will be uploading FLV or MP4 files only and these will not get converted, but only copied to the server.';
$language['backend.menu.entry6.sub1.conv.que']                  = 'Encoding Queue';
$language['backend.menu.entry6.sub1.conv.que.tip']              = 'If enabled, the "cron" script could run every 5 or 10 minutes and will check/allow/start only one encoding process at a time (recommended). <br />If disabled, the encoding will be start immediately after uploading (e.g.: one uploaded file means one encoding process started on the server. 7 files uploaded at once, means 7 simultaneous encoding processes will be started on the server).<br />Important: "cron" access is required if enabling this feature.';

$language['backend.menu.entry6.sub4.path.uno']  = 'Unoconv Path';
$language['backend.menu.entry6.sub4.path.conv'] = 'Convert Path';
$language['backend.menu.entry6.sub4.path.pdf']  = 'PDF2SWF Path';
$language['backend.menu.entry6.sub4.swf.off']   = 'Disable PDF2SWF encoding (only embed PDF files)';
