<?php
session_start();
unset($_SESSION['user_client']); 
header("Location: index.php");
exit();
?>