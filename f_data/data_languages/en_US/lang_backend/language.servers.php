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

$language['backend.servers.menu']            = 'Upload Servers';
$language['backend.servers.login']           = 'FTP Login';
$language['backend.servers.host']            = 'FTP Host';
$language['backend.servers.port']            = 'FTP Port';
$language['backend.servers.user']            = 'FTP Username';
$language['backend.servers.pass']            = 'FTP Password';
$language['backend.servers.root']            = 'FTP Root';
$language['backend.servers.conn']            = 'FTP Connection Test';
$language['backend.servers.filelist']        = 'Server File List';
$language['backend.servers.reset']           = 'Reset File Counts';
$language['backend.servers.reset.c1']        = 'Reset hop count';
$language['backend.servers.reset.c2']        = 'Reset total count';
$language['backend.servers.reset.c3']        = 'Clear last used date';
$language['backend.servers.reset.sel']       = 'Reset Selected';
$language['backend.servers.url']             = 'Base URL';
$language['backend.servers.transfer']        = 'Transfer Type';
$language['backend.servers.transfer.auto']   = 'FTP_AUTOASCII';
$language['backend.servers.transfer.ascii']  = 'FTP_ASCII';
$language['backend.servers.transfer.bin']    = 'FTP_BINARY';
$language['backend.servers.mode']            = 'Passive Mode';
$language['backend.servers.enabled']         = 'enabled';
$language['backend.servers.disabled']        = 'disabled';
$language['backend.servers.priority']        = 'Server Priority';
$language['backend.servers.limit']           = 'Server Limit';
$language['backend.servers.hop']             = 'File Hop';
$language['backend.servers.hop.c']           = 'Current Hop';
$language['backend.servers.content']         = 'Uploaded Files';
$language['backend.servers.total.v']         = 'Videos Uploaded';
$language['backend.servers.total.i']         = 'Images Uploaded';
$language['backend.servers.total.a']         = 'Audio Uploaded';
$language['backend.servers.total.d']         = 'Docs Uploaded';
$language['backend.servers.last']            = 'Last Used';
$language['backend.servers.content.v']       = 'Video Files';
$language['backend.servers.content.vt']      = 'Video Thumbnails';
$language['backend.servers.content.i']       = 'Image Files';
$language['backend.servers.content.it']      = 'Image Thumbnails';
$language['backend.servers.content.a']       = 'Audio Files';
$language['backend.servers.content.at']      = 'Audio Thumbnails';
$language['backend.servers.content.d']       = 'Doc. Files';
$language['backend.servers.content.dt']      = 'Doc.Thumbnails';
$language['backend.servers.d.upload.server'] = 'Upload Server: ';
$language['backend.servers.d.thumb.server']  = 'Thumb Server: ';
$language['backend.servers.d.state']         = 'State: ';
$language['backend.servers.d.vst']           = 'Video Start Time: ';
$language['backend.servers.d.vet']           = 'Video End Time: ';
$language['backend.servers.d.ist']           = 'Image Start Time: ';
$language['backend.servers.d.iet']           = 'Image End Time: ';
$language['backend.servers.d.ast']           = 'Audio Start Time: ';
$language['backend.servers.d.aet']           = 'Audio End Time: ';
$language['backend.servers.d.dst']           = 'Doc. Start Time: ';
$language['backend.servers.d.det']           = 'Doc. End Time: ';
$language['backend.servers.d.tst']           = 'Thumb Start Time: ';
$language['backend.servers.d.tet']           = 'Thumb End Time: ';
$language['backend.servers.d.log']           = 'Transfer Log:';
$language['backend.servers.lighttpd']        = 'Enable Lighttpd Streaming';
$language['backend.servers.type']            = 'Server Type';
$language['backend.servers.ftp']             = 'WEB/FTP Server';
$language['backend.servers.s3']              = 'Amazon S3/CloudFront';
$language['backend.servers.ws']              = 'Wasabi Cloud Storage';
$language['backend.servers.s3.conn']         = 'S3 Setup';
$language['backend.servers.stats']           = 'Server Stats';
$language['backend.servers.details']         = 'Edit Server Details';

$language['backend.xfer.menu.v']      = 'Video Transfers';
$language['backend.xfer.menu.i']      = 'Image Transfers';
$language['backend.xfer.menu.a']      = 'Audio Transfers';
$language['backend.xfer.menu.d']      = 'Document Transfers';
$language['backend.xfer.file.v']      = 'Video Files on this server';
$language['backend.xfer.file.v.no']   = 'No Video Files on this server';
$language['backend.xfer.file.i']      = 'Image Files on this server';
$language['backend.xfer.file.i.no']   = 'No Image Files on this server';
$language['backend.xfer.file.a']      = 'Audio Files on this server';
$language['backend.xfer.file.a.no']   = 'No Audio Files on this server';
$language['backend.xfer.file.d']      = 'Doc. Files on this server';
$language['backend.xfer.file.d.no']   = 'No Doc. Files on this server';
$language['backend.xfer.file.t.v']    = 'Video Thumbnails on this server';
$language['backend.xfer.file.t.v.no'] = 'No Video Thumbnails on this server';
$language['backend.xfer.file.t.i']    = 'Image Thumbnails on this server';
$language['backend.xfer.file.t.i.no'] = 'No Image Thumbnails on this server';
$language['backend.xfer.file.t.a']    = 'Audio Thumbnails on this server';
$language['backend.xfer.file.t.a.no'] = 'No Audio Thumbnails on this server';
$language['backend.xfer.file.t.d']    = 'Doc. Thumbnails on this server';
$language['backend.xfer.file.t.d.no'] = 'No Doc. Thumbnails on this server';
$language['backend.xfer.state.0']     = 'Idle - Waiting to start';
$language['backend.xfer.state.0.s']   = 'Idle';
$language['backend.xfer.state.1']     = 'In Progress - Transfer is running';
$language['backend.xfer.state.1.s']   = 'In Progress';
$language['backend.xfer.state.2']     = 'Completed - Transfer completed';
$language['backend.xfer.state.2.s']   = 'Completed';
$language['backend.xfer.new.video']   = 'Video Title';
$language['backend.xfer.new.vup']     = 'Video Server';
$language['backend.xfer.new.image']   = 'Image Title';
$language['backend.xfer.new.iup']     = 'Image Server';
$language['backend.xfer.new.audio']   = 'Audio Title';
$language['backend.xfer.new.aup']     = 'Audio Server';
$language['backend.xfer.new.doc']     = 'Doc. Title';
$language['backend.xfer.new.dup']     = 'Doc. Server';
$language['backend.xfer.new.thumb']   = 'Thumb Server';
$language['backend.xfer.pause']       = 'Pause Transfers';
$language['backend.xfer.resume']      = 'Resume Transfers';

$language['backend.s3.bucketname'] = 'Bucket Name';
$language['backend.s3.region']     = 'Region';
$language['backend.s3.accesskey']  = 'Access Key';
$language['backend.s3.secretkey']  = 'Secret Key';
$language['backend.s3.perm']       = 'File Permissions';
$language['backend.s3.perm.priv']  = 'private';
$language['backend.s3.perm.pub']   = 'public';
$language['backend.s3.no.bucket']  = 'Error: No bucket was found. Please run the S3 Setup first!';
$language['backend.s3.logged.in']  = 'Successfully logged in using AccessKey and SecretKey';

$language['backend.cf.enable']       = 'Enable CloudFront Distribution';
$language['backend.cf.surl.enable']  = 'Enable Signed URLs; expiring in (seconds)';
$language['backend.cf.price']        = 'Price Class';
$language['backend.cf.price.100']    = 'Only US and Europe';
$language['backend.cf.price.200']    = 'Only Us, Europe and Asia';
$language['backend.cf.price.all']    = 'All Edge Locations';
$language['backend.cf.surl.time']    = 'seconds';
$language['backend.cf.keypair.id']   = 'Key Pair ID';
$language['backend.cf.keypair.file'] = 'Key Pair File';
$language['backend.cf.web']          = 'Web Distribution (required for full HTML5 video support or thumbnails)';
$language['backend.cf.rtmp']         = 'RTMP Distribution (recommended for adaptive MP4 video streaming)';
$language['backend.cf.test']         = 'CloudFront Setup';
$language['backend.cf.update']       = 'Update Distribution';
$language['backend.cf.dist.for']     = 'Distribution for ';
$language['backend.cf.dist.id']      = 'Distribution ID: ';
$language['backend.cf.dist.status']  = 'Distribution Status: ';
$language['backend.cf.dist.domain']  = 'Distribution Domain: ';
$language['backend.cf.dist.uri']     = 'Distribution FQURI: ';
$language['backend.cf.origin.none']  = 'OriginID / Distribution not found! Run the CloudFront Setup first.';
$language['backend.cf.origin.set']   = 'Setting up Origin ID';
$language['backend.cf.origin.found'] = 'Found an Origin ID: ';
$language['backend.cf.dist.found']   = 'Found a Distribution ID: ';
$language['backend.cf.dist.new']     = 'Setting up a new distribution';

$language['backend.servers.content.s']  = 'Short Files';
$language['backend.servers.content.st'] = 'Short Thumbnails';
$language['backend.xfer.file.s']        = 'Video Short Files on this server';
$language['backend.xfer.file.s.no']     = 'No Video Short Files on this server';
$language['backend.servers.total.s']    = 'Shorts Uploaded';
$language['backend.servers.d.sst']      = 'Short Start Time: ';
$language['backend.servers.d.set']      = 'Short End Time: ';
$language['backend.xfer.menu.s']        = 'Short Transfers';
$language['backend.xfer.new.short']     = 'Short Title';
$language['backend.xfer.new.sup']       = 'Short Server';
