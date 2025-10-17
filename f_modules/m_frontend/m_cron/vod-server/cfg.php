<?php
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

define('_ISVALID', true);

/* path to recordings */
$path = getenv('VOD_REC_PATH') ?: '/mnt/rec';
/* main url */
$base = getenv('CRON_BASE_URL') ?: 'http://localhost:8080';
/* cron salt key */
$ssk = getenv('CRON_SSK') ?: 'CHANGE_ME_IN_BACKEND';
