<?php
session_start();
include "../config.php";

// Hanya guru & admin yang bisa akses
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru dan admin yang bisa menghapus kelas.");
}

// Pastikan ada ID
if (!isset($_GET['id'])) {
    die("ID kelas tidak ditemukan.");
}
$id_kelas = (int)$_GET['id'];

// Cek apakah kelas ada
$result = mysqli_query($conn, "SELECT * FROM kelas WHERE id='$id_kelas'");
$kelas = mysqli_fetch_assoc($result);
if (!$kelas) {
    die("Kelas tidak ditemukan.");
}

// Jika guru, pastikan hanya bisa hapus kelas miliknya
if ($_SESSION['role'] == 'guru' && $kelas['id_guru'] != $_SESSION['id']) {
    die("Anda tidak berhak menghapus kelas ini.");
}

// Proses hapus
$sql = "DELETE FROM kelas WHERE id='$id_kelas'";
if (mysqli_query($conn, $sql)) {
    header("Location: index.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
