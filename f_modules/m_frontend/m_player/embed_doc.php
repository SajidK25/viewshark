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
include_once $class_language->setLanguageFile('frontend', 'language.view');

$type     = 'doc';
$file_key = $class_filter->clr_str($_GET[$type[0]]);
$cfg[]    = $class_database->getConfigurations('affiliate_tracking_id');
$mobile   = VHref::isMobile();

$u = $db->execute(sprintf("SELECT
          A.`usr_key`,
          B.`usr_id`,
          B.`file_title`, B.`thumb_cache`
          FROM
          `db_accountuser` A, `db_%sfiles` B
          WHERE
          A.`usr_id`=B.`usr_id` AND
          B.`file_key`='%s'
          LIMIT 1;", $type, $file_key));
$usr_key     = $u->fields["usr_key"];
$title       = $u->fields["file_title"];
$thumb_cache = $u->fields["thumb_cache"];
$thumb_cache = $thumb_cache > 1 ? $thumb_cache : null;
$tmb         = $cfg["media_files_url"] . '/' . $usr_key . '/t/' . $file_key . '/0' . $thumb_cache . '.jpg';

$doc_src = $cfg["media_files_dir"] . '/' . $usr_key . '/d/' . md5($cfg["global_salt_key"] . $file_key) . '.pdf';
$src     = VGenerate::thumbSigned(array("type" => "doc", "server" => "upload", "key" => '/' . $usr_key . '/d/' . md5($cfg["global_salt_key"] . $file_key) . '.pdf'), $file_key, $usr_key, 0, 1);

if (!file_exists($doc_src)) {
    $doc_src = $cfg["media_files_dir"] . '/' . $usr_key . '/d/' . $file_key . '.pdf';
    $src     = VGenerate::thumbSigned(array("type" => "doc", "server" => "upload", "key" => '/' . $usr_key . '/d/' . $file_key . '.pdf'), $file_key, $usr_key, 0, 1);
}

if (!file_exists($doc_src)) {
    exit;
}
?>

<!DOCTYPE html>
<html>
<head profile="http://www.w3.org/2005/10/profile">
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
	<meta name="robots" content="noindex, nofollow">
    <title><?php echo $title; ?></title>
<?php if ($mobile): ?>
	<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/dealfonso/pdfjs-viewer@1.1/pdfjs-viewer.min.css">
	<link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.min.js"></script>
<?php endif;?>
    <script type="text/javascript" src="<?php echo $cfg['javascript_url']; ?>/jquery.min.js"></script>
    <script type="text/javascript">jQuery.migrateMute=true;</script>
    <script type="text/javascript">if(navigator.platform==='iPad'||navigator.platform==='iPhone'||navigator.platform==='iPod'){WebFont.load({google:{families:['Roboto:300,400,500,600,700']}});}</script>
<?php if ($mobile): ?>
	<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/gh/dealfonso/pdfjs-viewer@1.1/pdfjs-viewer.min.js"></script>
    <script defer>
    	var url = "<?php echo $src; ?>";
		var container = document.getElementById('view-player-<?php echo $file_key; ?>');
		$("document").ready(function(){
			var pdfjsLib = window["pdfjs-dist/build/pdf"];
			pdfjsLib.GlobalWorkerOptions.workerSrc = "https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.12.313/pdf.worker.min.js";

			let pdfViewer = new PDFjsViewer($(".pdfjs-viewer"), {
			  onZoomChange: function (zoom) {
			    zoom = parseInt(zoom * 10000) / 100;
			    $(".zoomval").text(zoom + "%");
			  },
			  onActivePageChanged: function (page, pageno) {
			    $(".pageno").text(pageno + "/" + this.getPageCount());
			  }
			});
			pdfViewer.loadDocument(url).then(function () {pdfViewer.setZoom("fit")});

			$("#pdfViewer-first").on("click", function() {pdfViewer.first()});
			$("#pdfViewer-prev").on("click", function() {pdfViewer.prev();return false});
			$("#pdfViewer-next").on("click", function() {pdfViewer.next();return false});
			$("#pdfViewer-last").on("click", function() {pdfViewer.last()});
			$("#pdfViewer-zoomOut").on("click", function() {pdfViewer.setZoom('out')});
			$("#pdfViewer-zoomIn").on("click", function() {pdfViewer.setZoom('in')});
			$("#pdfViewer-zoomWidth").on("click", function() {pdfViewer.setZoom('width')});
			$("#pdfViewer-zoomHeight").on("click", function() {pdfViewer.setZoom('height')});
			$("#pdfViewer-zoomFit").on("click", function() {pdfViewer.setZoom('fit')});
		});
    </script>
<?php endif;?>
    <style>
  	  html,body{width:100%;height:100%;margin:0;padding:0;overflow:hidden}#doc-pagination{display:flex;align-items:center;justify-content:center;margin-bottom:10px}#doc-pagination span#page_wrap{margin:0 15px}
  	  #view-player-<?php echo $file_key; ?>{width:100%;height:100%}
<?php if ($mobile): ?>
  	  #view-player-<?php echo $file_key; ?>{width:100%;height:100%;overflow:scroll !important;position:relative;/*padding-bottom:56.25%;padding-top:0;*/}
	  .pdftoolbar, .pdftoolbar i{font-size:16px;vertical-align:middle}
	  .pdftoolbar span{margin-right:0.5em;margin-left:0.5em;font-size:14px}
	  .pdftoolbar .btn-sm{}
	  .pdftoolbar{width:100%;height:auto;background:#ddd;z-index:100}
<?php endif;?>
  	</style>
</head>
<body>
<?php if ($mobile): ?>
<?php endif;?>
    <div style="width:100%;height:100%;position:relative;"><div id="view-player-<?php echo $file_key; ?>" class="pdfViewer singlePageView" style="">
<?php if ($mobile): ?>
   <div class="pdftoolbar text-center row m-0 p-0">
      <div class="col-12 col-lg-6 my-1">
        <button class="btn btn-secondary btn-sm btn-first" id="pdfViewer-first"><i class="material-icons-outlined">skip_previous</i></button>
        <button class="btn btn-secondary btn-sm btn-prev" id="pdfViewer-prev"><i class="material-icons-outlined">navigate_before</i></button>
        <span class="pageno"></span>
        <button class="btn btn-secondary btn-sm btn-next" id="pdfViewer-next"><i class="material-icons-outlined">navigate_next</i></button>
        <button class="btn btn-secondary btn-sm btn-last" id="pdfViewer-last"><i class="material-icons-outlined">skip_next</i></button>
      </div>
      <div class="col-12 col-lg-6 my-1">
        <button class="btn btn-secondary btn-sm" id="pdfViewer-zoomOut"><i class="material-icons-outlined">zoom_out</i></button>
        <span class="zoomval">100%</span>
        <button class="btn btn-secondary btn-sm" id="pdfViewer-zoomIn"><i class="material-icons-outlined">zoom_in</i></button>
        <button class="btn btn-secondary btn-sm ms-3" id="pdfViewer-zoomWidth"><i class="material-icons-outlined">swap_horiz</i></button>
        <button class="btn btn-secondary btn-sm" id="pdfViewer-zoomHeight"><i class="material-icons-outlined">swap_vert</i></button>
        <button class="btn btn-secondary btn-sm" id="pdfViewer-zoomFit"><i class="material-icons-outlined">fit_screen</i></button>
      </div>
    </div>
    <div class="pdfjs-viewer h-100"></div>
<?php else: ?>
	</div></div>
    <script type="text/javascript">$(document).ready(function(){$("#view-player-<?php echo $file_key; ?>").html('<embed src="<?php echo $src; ?>" width="100%" height="100%">')});</script>
<?php endif;?>
</body>
<?php if ($cfg["google_analytics"] != ''): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?=$cfg["google_analytics"]?>"></script>
    <script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?=$cfg["google_analytics"]?>');</script>
<?php endif;?>
</html>