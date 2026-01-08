<?php
session_start();
session_unset();
session_destroy();
header('location:../index.php?route=admin&action=login');
exit;
?>
