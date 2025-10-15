<?php
defined('_ISVALID') or header('Location: /error');

/* IPQualityScore check */
$cfg['ipqualityscore']     = false;
$cfg['ipqualityscore_api'] = '';

/* MaxMind country detection */
$cfg['maxmind']         = false;
$cfg['maxmind_db']      = '';
$cfg['maxmind_country'] = false; //or an array('IN', 'PK');//block these countries from posting comments

/* Akismet spam check */
$cfg['akismet']         = false;
$cfg['akismet_api']     = '';
$cfg['akismet_site']    = $cfg['main_url'];
$cfg['akismet_country'] = false; //or an array('IN', 'PK');//check only these countries for spam comments

/* proxycheck-php */
$cfg['proxycheck']     = false;
$cfg['proxycheck_api'] = '';
