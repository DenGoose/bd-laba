<?php

session_start();
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once "vendor/autoload.php";

require_once "router.php";
