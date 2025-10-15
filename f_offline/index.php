<?php
define('_ISVALID', true);

include_once 'f_core/config.core.php';

define('MAIN', $cfg["main_url"].'/f_offline/');

include_once $class_language->setLanguageFile('frontend', 'language.offline');
include_once $class_language->setLanguageFile('frontend', 'language.global');

$sht	= array();

$pcfg	= $class_database->getConfigurations('offline_mode_settings,offline_mode_until');

$slides	= unserialize($pcfg["offline_mode_settings"]);
if (is_array($slides)) {
	foreach ($slides as $slide) {
		$sht[] = '{src:"'.$slide['url'].'"}';
	}
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="robots" content="noindex">
    <title><?php echo $cfg["head_title"]; ?></title>
    <link rel="icon" type="image/png" href="<?php echo $cfg["main_url"]; ?>/favicon.png">
    <link rel="stylesheet" type="text/css" href="<?php echo $cfg["styles_url"]; ?>/init0.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo MAIN;?>assets/css/bootstrap.min.css" >
    <link rel="stylesheet" type="text/css" href="<?php echo MAIN;?>assets/fonts/line-icons.css">
    <link rel="stylesheet" type="text/css" href="<?php echo MAIN;?>assets/css/slicknav.css">
    <link rel="stylesheet" type="text/css" href="<?php echo MAIN;?>assets/css/menu_sideslide.css">
    <link rel="stylesheet" type="text/css" href="<?php echo MAIN;?>assets/css/vegas.min.css">
    <link rel="stylesheet" type="text/css" href="<?php echo MAIN;?>assets/css/animate.css">
    <link rel="stylesheet" type="text/css" href="<?php echo MAIN;?>assets/css/main.css">
    <link rel="stylesheet" type="text/css" href="<?php echo MAIN;?>assets/css/responsive.css">
    <style>section#logo{text-align:center;margin-top:2em;}.navbar-brand{float:none;margin-right:0}</style>
  </head>
  <body>
    <div class="bg-wraper overlay has-vignette">
      <div id="vslider" class="slider opacity-50 vegas-container" style="height: 983px;"></div>
    </div>
	<section id="logo">
		<a href="" class="navbar-brand"><img src="<?php echo MAIN; ?>assets/img/logo.png" alt=""></a>
	</section>
    <!-- Coundown Section Start -->
    <section class="countdown-timer">
      <div class="container">
        <div class="row text-center">
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="heading-count">
              <h2><?php echo $language["offline.text.launching"]; ?></h2>
            </div>
          </div>
          <div class="col-md-12 col-sm-12 col-xs-12">
            <div class="row time-countdown justify-content-center">
              <div id="clock" class="time-count"></div>
            </div>
            <p>
            <?php echo $language["offline.text.text1"]; ?><br>
            <?php echo $language["offline.text.text2"]; ?>
            </p>
            <div class="social mt-4">
            	<?php echo VGenerate::socialMediaLinks(); ?>
            </div>
          </div>
        </div>
      </div>
    </section>
    <!-- Coundown Section End -->
    <!-- Preloader -->
    <div id="preloader">
      <div class="loader" id="loader-1"></div>
    </div>
    <!-- End Preloader -->
    <script type="text/javascript">
    var ln = [];ln['days'] = '<?php echo $language["frontend.global.days"];?>';ln['hours'] = '<?php echo $language["frontend.global.hours"];?>';ln['minutes'] = '<?php echo $language["frontend.global.minutes"];?>';ln['seconds'] = '<?php echo $language["frontend.global.seconds"];?>';ln['until'] = '<?php echo $pcfg["offline_mode_until"]; ?>';</script>
    <script src="<?php echo MAIN;?>assets/js/jquery-min.js"></script>
    <script src="<?php echo MAIN;?>assets/js/popper.min.js"></script>
    <script src="<?php echo MAIN;?>assets/js/bootstrap.min.js"></script>
    <script src="<?php echo MAIN;?>assets/js/vegas.min.js"></script>
    <script src="<?php echo MAIN;?>assets/js/jquery.countdown.min.js"></script>
    <script src="<?php echo MAIN;?>assets/js/classie.js"></script>
    <script src="<?php echo MAIN;?>assets/js/jquery.nav.js"></script>
    <script src="<?php echo MAIN;?>assets/js/jquery.easing.min.js"></script> 
    <script src="<?php echo MAIN;?>assets/js/wow.js"></script>
    <script src="<?php echo MAIN;?>assets/js/jquery.slicknav.js"></script>
    <script src="<?php echo MAIN;?>assets/js/main.js"></script>
    <script type="text/javascript">
      $("#vslider").vegas({
          timer: false,
          delay: 5000,
          transitionDuration: 2000,
          transition: "blur",
          slides: [
          <?php echo implode(',', $sht); ?>
          ]
      });
    </script>
  </body>
</html>
