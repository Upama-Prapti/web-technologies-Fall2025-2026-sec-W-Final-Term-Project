<?php
session_start();

// Base URL - adjust this to match your actual project URL
define('BASE_URL', 'http://localhost/blog/');

// Paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('UPLOAD_PATH', ROOT_PATH . 'uploaded_img/');

// Asset paths (relative to web root)
define('ASSETS_URL', 'assets/');
define('UPLOADED_IMG_URL', 'uploaded_img/');

// Database config
require_once ROOT_PATH . 'config/database.php';
?>

