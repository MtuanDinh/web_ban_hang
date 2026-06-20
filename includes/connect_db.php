<?php 
    $db_server = "localhost";
    $db_user = "root";
    $db_pass = "";
    $db_name = "phone_store";
    $conn = "";
    try{
        $conn = mysqli_connect($db_server, $db_user, $db_pass, $db_name);
        mysqli_set_charset($conn, "utf8mb4");
    }
    catch(mysqli_sql_exception $e){
        die("Can't connect to database: {$e->getMessage()}");
    }
?>