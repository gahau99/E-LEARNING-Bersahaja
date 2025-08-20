<?php
session_start();
include "../config.php";

// Hanya admin yang bisa approve
if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'admin') {
    die("Akses ditolak! Hanya admin yang bisa approve.");
}

$id = (int) $_GET['id'];

// Update status jadi approved
if (mysqli_query($conn, "UPDATE materi SET status='approved' WHERE id=$id")) {
    header("Location: index.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
