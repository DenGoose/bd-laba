<?php

session_start();
ob_start();
error_reporting(false);
ini_set('display_errors', 0);
require_once "vendor/autoload.php";

require_once "router.php";
