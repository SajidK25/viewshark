<?php
define('_ISVALID', true);
include_once '../../../f_core/config.core.php';
require_once __DIR__ . '/../config/config.php';

use Donations\WebhookHandler;

$handler = new WebhookHandler();
$handler->handle(); 