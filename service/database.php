<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database_name = "project-database";

$db = mysqli_connect($hostname, $username, $password, $database_name);

if($db->connect_error){
    error_log("DB Connection failed: " . $db->connect_error);
    die('error!');
}

?>