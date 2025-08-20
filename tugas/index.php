<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id'])) {
    die("Akses ditolak! Silakan login.");
}

$id_user = $_SESSION['id'];
$role = $_SESSION['role'];

// kalau siswa, langsung redirect ke list.php
if ($role == 'siswa') {
    header("Location: list.php");
    exit;
}

// kalau admin → lihat semua tugas
if ($role == 'admin') {
    $sql = "SELECT t.*, k.nama_kelas, u.username as guru 
            FROM tugas t
            JOIN kelas k ON t.id_kelas = k.id
            JOIN users u ON k.id_guru = u.id
            ORDER BY t.created_at DESC";
}

// kalau guru → lihat tugas kelas yang dia ampu
if ($role == 'guru') {
    $sql = "SELECT t.*, k.nama_kelas 
            FROM tugas t
            JOIN kelas k ON t.id_kelas = k.id
            WHERE k.id_guru = $id_user
            ORDER BY t.created_at DESC";
}

$tugas = mysqli_query($conn, $sql);
?>

<h2>Daftar Tugas</h2>
<a href="tambah.php">+ Tambah Tugas</a> |
<a href="../dashboard.php">Dashboard</a>
<table border="1" cellpadding="5">
    <tr>
        <th>Judul</th>
        <th>Kelas</th>
        <th>Deadline</th>
        <?php if ($role == 'admin'): ?>
            <th>Guru</th>
        <?php endif; ?>
        <th>Aksi</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($tugas)): ?>
        <tr>
            <td><?= htmlspecialchars($row['judul']) ?></td>
            <td><?= htmlspecialchars($row['nama_kelas']) ?></td>
            <td><?= $row['deadline'] ?></td>
            <?php if ($role == 'admin'): ?>
                <td><?= htmlspecialchars($row['guru']) ?></td>
            <?php endif; ?>
            <td>
                <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> |
                <a href="view.php?id=<?= $row['id'] ?>">Lihat</a> |
                <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a> |
                <a href="nilai.php?id=<?= $row['id'] ?>">Beri Nilai</a>
            </td>
        </tr>
    <?php endwhile; ?>
</table>