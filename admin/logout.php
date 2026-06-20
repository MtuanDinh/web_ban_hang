<?php 
    session_start();
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        $_SESSION = array();
        session_destroy();
        header("Location: /web_ban_hang/admin/login.php");
        exit();
    }
    else{
        header("Location: /web_ban_hang/admin/index.php");
        exit();
    }
?>