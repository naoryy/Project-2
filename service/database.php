<?php

$hostname = "localhost";
$username = "root";
$password = "";
$database_name = "project-database";

$db = mysqli_connect($hostname, $username, $password, $database_name);

if($db->connect_error){
    echo "connection failed!";
    die('error!');
}

?>