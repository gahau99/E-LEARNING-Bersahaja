<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id'])) {
    die("Akses ditolak! Silakan login.");
}

if (!isset($_GET['id'])) {
    die("ID komentar tidak ditemukan!");
}

$id_komentar = intval($_GET['id']);
$id_user     = $_SESSION['id'];
$role        = $_SESSION['role'];

// Ambil data komentar berdasarkan struktur terbaru
$stmt = $conn->prepare("
    SELECT id, materi_id, tugas_id, tugas_siswa_id, id_user, isi
    FROM komentar 
    WHERE id = ?
");
$stmt->bind_param("i", $id_komentar);
$stmt->execute();
$result = $stmt->get_result();
$komentar = $result->fetch_assoc();
$stmt->close();

if (!$komentar) {
    die("Komentar tidak ditemukan!");
}

// Cek hak akses → pemilik komentar atau admin/guru
if ($komentar['id_user'] != $id_user && !in_array($role, ['admin','guru'])) {
    die("Anda tidak berhak menghapus komentar ini!");
}

// Hapus komentar
$stmt = $conn->prepare("DELETE FROM komentar WHERE id = ?");
$stmt->bind_param("i", $id_komentar);
$stmt->execute();
$stmt->close();

// Redirect kembali ke parent sesuai kolom baru
if (!empty($komentar['materi_id'])) {
    header("Location: ../materi/view.php?id=" . $komentar['materi_id']);
} elseif (!empty($komentar['tugas_id'])) {
    header("Location: ../tugas/view.php?id=" . $komentar['tugas_id']);
} elseif (!empty($komentar['tugas_siswa_id'])) {
    header("Location: ../tugas_siswa/view.php?id=" . $komentar['tugas_siswa_id']);
} else {
    header("Location: index.php");
}
exit;
