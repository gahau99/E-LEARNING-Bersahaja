<?php
$host = "localhost";
$user = "root";
$pass = "root";
$db   = "elearning";

$conn = mysqli_connect($host, $user, $pass, $db);

if(!$conn){
    die("Koneksi gagal: " . mysqli_connect_error());
}

// echo "berhasil koneksi ke database";

?>