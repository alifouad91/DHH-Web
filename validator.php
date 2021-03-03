<?php
if (!defined('EXECUTION_TOKEN') || !isset($argv[1]) || EXECUTION_TOKEN !== $argv[1]) {
    die();
}

define('C5_ENVIRONMENT_ONLY', true);

require 'index.php';

ini_set('memory_limit', '-1');
set_time_limit(0);
