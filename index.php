<?php
<<<<<<< HEAD

header("location: dashboard/index.php");
exit;
?>
=======
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

header("Location: Authentication/login.php");
exit;
>>>>>>> origin/feature-panharith
