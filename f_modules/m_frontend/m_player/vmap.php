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

define('_ISVALID', true);

include_once 'f_core/config.core.php';

$type     = isset($_GET['a']) ? 'audio' : (isset($_GET['l']) ? 'live' : (isset($_GET['s']) ? 'short' : 'video'));
$file_key = $class_filter->clr_str($_GET[$type[0]]);

// Basic VMAP that points to a VAST pre-roll for this request.
// This is a scaffold; a rules engine can fill multiple AdBreaks.
header('Content-type: text/xml');
echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<VMAP xmlns="http://www.iab.net/videosuite/vmap" version="1.0">
  <AdBreak breakType="linear" breakId="preroll" timeOffset="start">
    <AdSource id="1" allowMultipleAds="false" followRedirects="true">
      <VASTAdData>
        <?php
          $vast = $cfg['main_url'] . '/' . VHref::getKey('vast') . '?t=vjs&v=' . htmlspecialchars($file_key, ENT_QUOTES, 'UTF-8');
          echo '<VAST xmlns="http://schemas.xmlsoap.org/ads/vast/3.0"><Ad><InLine><AdSystem>VS</AdSystem><AdTitle>VMAP VAST Wrapper</AdTitle><Creatives><Creative><Linear><TrackingEvents></TrackingEvents><MediaFiles></MediaFiles></Linear></Creative></Creatives></InLine></Ad></VAST>'; // minimal placeholder
        ?>
      </VASTAdData>
      <AdTagURI templateType="vast3">
        <![CDATA[<?php echo $vast; ?>]]>
      </AdTagURI>
    </AdSource>
  </AdBreak>
</VMAP>

