<?php
session_start();
include "../config.php";

// Hanya admin yang bisa akses
if (!isset($_SESSION['id']) || $_SESSION['role'] != 'admin') {
    die("Akses ditolak! Hanya admin yang bisa menghapus user.");
}

if (!isset($_GET['id'])) {
    die("ID user tidak ditemukan!");
}

$id_user = intval($_GET['id']);

// Cegah admin menghapus dirinya sendiri
if ($id_user == $_SESSION['id']) {
    die("Anda tidak bisa menghapus user Anda sendiri!");
}

// Cek apakah user ada
$stmt = $conn->prepare("SELECT id, username, role FROM users WHERE id = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("User tidak ditemukan!");
}

// Hapus user
$stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
$stmt->bind_param("i", $id_user);
if ($stmt->execute()) {
    $stmt->close();
    header("Location: index.php");
    exit;
} else {
    die("Gagal menghapus user: " . $stmt->error);
}
