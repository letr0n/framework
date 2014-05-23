<?php

error_reporting(E_ALL | E_STRICT);

date_default_timezone_set('UTC');

mb_language('uni');
mb_regex_encoding('UTF-8');
mb_internal_encoding('UTF-8');

define('MAKO_IS_WINDOWS', (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN'));

require realpath(__DIR__ . '/../src/mako/helpers.php');

require __DIR__ . '/integration/resources/ORMTestCase.php';