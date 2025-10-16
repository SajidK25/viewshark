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

$main_dir = realpath(dirname(__FILE__) . '/../../../');
set_include_path($main_dir);

include_once 'f_core/config.core.php';

$ad_key  = $class_filter->clr_str($_GET["v"]);
$ad_type = $class_filter->clr_str($_GET["t"]);

if ($ad_key == '' or $ad_type == '') {
    return;
}

$u = $db->execute(sprintf("SELECT * FROM `db_%sadentries` WHERE `ad_key`='%s' LIMIT 1;", $ad_type, $ad_key));

$ad_duration   = gmdate("H:i:s", $u->fields["ad_duration"]);
$ad_click_url  = $u->fields["ad_click_url"];
$ad_custom_url = $u->fields["ad_custom_url"];
$ad_click_url  = $ad_custom_url != '' ? $ad_custom_url : $u->fields["ad_click_url"];
$ad_file       = $class_database->singleFieldValue('db_jwadcodes', 'db_code', 'db_key', $u->fields["ad_custom"]);
$ad_file_type  = $class_database->singleFieldValue('db_jwadcodes', 'db_type', 'db_key', $u->fields["ad_custom"]);
$ad_file_loc   = $ad_file_type == 'code' ? $ad_file : $cfg["player_url"] . '/ad_files/' . $ad_file;
$ad_ext        = VFileinfo::getExtension($ad_file);
$ad_width      = $u->fields["ad_width"];
$ad_width      = (intval($ad_width) > 0) ? ' width="' . $ad_width . '"' : null;
$ad_height     = $u->fields["ad_height"];
$ad_height     = (intval($ad_height) > 0) ? ' height="' . $ad_height . '"' : null;
$ad_bitrate    = $u->fields["ad_bitrate"];
$ad_bitrate    = (intval($ad_bitrate) > 0) ? ' bitrate="' . $ad_bitrate . '"' : null;
$impr          = $cfg["main_url"] . '/' . VHref::getKey("adv") . '?t=' . $ad_type . '&impression=' . $ad_key;
$ctr           = $cfg["main_url"] . '/' . VHref::getKey("adv") . '?t=' . $ad_type . '&click=' . $ad_key;

$pixel = $cfg["player_url"] . '/ad_files/single-pixel-ar7.gif';

header("Content-type: text/xml");
echo '<?xml version="1.0" encoding="UTF-8" standalone="no"?>';
?>
<VAST version="2.0" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="vast.xsd">
<Ad id="812030">
    <InLine>
      <AdSystem>VS</AdSystem>
      <AdTitle>EasyStream Static VAST Template 2.0</AdTitle>
      <Description>date of revision 10-04-14</Description>
        <Impression id="ft_vast_i">
          <![CDATA[<?php echo $impr; ?>]]>
        </Impression>
        <Impression id="3rdparty">
          <![CDATA[]]>
        </Impression>
        <Impression id="3rdparty">
          <![CDATA[]]>
        </Impression>
        <Creatives>
        <Creative sequence="1">
          <Linear>
            <Duration>00:00:15</Duration>
            <TrackingEvents>
              <Tracking event="start">
                  <![CDATA[<?php echo $pixel; ?>]]>
              </Tracking>
              <Tracking event="midpoint">
                  <![CDATA[<?php echo $pixel; ?>]]>
              </Tracking>
              <Tracking event="firstQuartile">
                  <![CDATA[<?php echo $pixel; ?>]]>
              </Tracking>
              <Tracking event="thirdQuartile">
                  <![CDATA[<?php echo $pixel; ?>]]>
              </Tracking>
              <Tracking event="complete">
                  <![CDATA[<?php echo $pixel; ?>]]>
              </Tracking>
              <Tracking event="mute">
                  <![CDATA[<?php echo $pixel; ?>]]>
              </Tracking>
              <Tracking event="fullscreen">
                  <![CDATA[<?php echo $pixel; ?>]]>
              </Tracking>
            </TrackingEvents>
            <VideoClicks>
              <ClickThrough>
                  <![CDATA[<?php echo $ad_click_url; ?>]]>
              </ClickThrough>
              <ClickTracking><![CDATA[<?php echo $ctr; ?>]]></ClickTracking>
            </VideoClicks>
            <MediaFiles>
              <MediaFile id="1" delivery="progressive" type="video/<?php echo $ad_ext; ?>"<?php echo $ad_bitrate; ?><?php echo $ad_width; ?><?php echo $ad_height; ?>>
                  <![CDATA[<?php echo $ad_file_loc; ?>]]>
              </MediaFile>
            </MediaFiles>
          </Linear>
        </Creative>
        <Creative sequence="1">
          <CompanionAds>
          </CompanionAds>
        </Creative>
      </Creatives>
    </InLine>
  </Ad>
</VAST>