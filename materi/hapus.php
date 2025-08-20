<?php
session_start();
include "../config.php";

// Hanya guru/admin yang boleh
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak!");
}

$id = (int) $_GET['id'];
$result = mysqli_query($conn, "SELECT * FROM materi WHERE id = $id");
$materi = mysqli_fetch_assoc($result);

// Jika materi tidak ditemukan
if (!$materi) {
    die("Materi tidak ditemukan.");
}

// Guru hanya boleh hapus materi yang dia buat
if ($_SESSION['role'] === 'guru' && $materi['dibuat_oleh'] != $_SESSION['id']) {
    die("Anda tidak berhak menghapus materi ini.");
}

// Hapus file materi (jika ada)
if ($materi['file']) {
    $target_file = "../uploads/" . $materi['file'];
    if (file_exists($target_file)) {
        unlink($target_file);
    }
}

// Hapus data dari database
if (mysqli_query($conn, "DELETE FROM materi WHERE id = $id")) {
    header("Location: index.php");
    exit;
} else {
    echo "Error: " . mysqli_error($conn);
}
