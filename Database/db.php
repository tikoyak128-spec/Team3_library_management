<?php
    $host = "localhost";
    $user = "root";
    $password = "sinaw11";
    $database = "Library_Management";
    $port = 3308;

    $conn = mysqli_connect($host, $user, $password, $database, $port);

    if (!$conn) {
        die("Connection Failed: " . mysqli_connect_error());
    }
?>