<?php

$host = "localhost"; # owh localhost
$user = "root";      # root user
$pass = "";      # root password
$db   = "elearning"; # db e-learning

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("Koneksi gagal: " . mysqli_connect_error());
}

// echo "berhasil koneksi ke database";

?>