<?php
session_start();
include "../config.php";

// Hanya admin yang bisa reject
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Hanya admin yang bisa reject.");
}

$id = (int) $_GET['id'];

// Update status jadi rejected
if (mysqli_query($conn, "UPDATE materi SET status='rejected' WHERE id=$id")) {
    header("Location: index.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
