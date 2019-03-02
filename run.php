#!/usr/bin/php
<?php

if (PHP_VERSION_ID < 70100) {
    die('PHP 7.1 or newer is required to run this script.' . PHP_EOL);
}

spl_autoload_register();
set_include_path(__DIR__ . '/src');

include 'main.php';