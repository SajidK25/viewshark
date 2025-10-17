<?php
ini_set("error_reporting", E_ALL & ~E_STRICT & ~E_NOTICE & ~E_DEPRECATED);

define('_ISVALID', true);

/* database */
$dbhost = getenv('DB_HOST') ?: 'db';
$dbname = getenv('DB_NAME') ?: 'easystream';
$dbuser = getenv('DB_USER') ?: 'easystream';
$dbpass = getenv('DB_PASS') ?: 'easystream';
/* main url */
$base = getenv('CRON_BASE_URL') ?: 'http://localhost:8080';
/* cron salt key */
$ssk = getenv('CRON_SSK') ?: 'CHANGE_ME_IN_BACKEND';
