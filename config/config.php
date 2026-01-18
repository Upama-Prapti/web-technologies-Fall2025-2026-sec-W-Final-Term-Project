<?php
session_start();

define('BASE_URL', 'http://localhost/blog/');

define('ROOT_PATH', dirname(__DIR__) . '/');
define('UPLOAD_PATH', ROOT_PATH . 'uploaded_img/');

define('ASSETS_URL', 'assets/');
define('UPLOADED_IMG_URL', 'uploaded_img/');

define('SUPERADMIN_USERNAME', 'upama');
define('SUPERADMIN_DEFAULT_PASSWORD', '12345678');

require_once ROOT_PATH . 'config/database.php';
?>
