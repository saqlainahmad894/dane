<?php
session_start();
$_SESSION = array();
session_destroy();
header("Location: log.php");
exit;
?>
