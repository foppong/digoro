<?php
ob_start();
session_start();

//include all configuration constants
require_once(dirname(__FILE__) . '/config.php');

//autoloading of classes
function __autoload($class) {
    require_once(dirname(__FILE__) . "/../classes/{$class}.php");
}

//global database connection object
require_once(MYSQL);
$dbObject = MySQLiDbObject::getInstance();

//sanitize all GET and POST requests
InputSanitizer::sanitizeData($_GET);
InputSanitizer::sanitizeData($_POST);
InputSanitizer::sanitizeData($_REQUEST);