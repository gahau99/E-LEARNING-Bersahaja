<?php
session_start();
include "../config.php";

// Hanya guru & admin yang bisa akses
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak! Hanya guru dan admin yang bisa melihat halaman ini.");
}

$result = mysqli_query($conn, "
    SELECT kelas.*, users.username AS nama_guru
    FROM kelas
    LEFT JOIN users ON kelas.id_guru = users.id
");
?>

<h2>Daftar Kelas</h2>
<a href="tambah.php">Tambah Kelas</a> | <a href="../dashboard.php">Dashboard</a>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>Kelas</th><th>Deskripsi</th><th>Kode</th><th>Guru</th><th>Aksi</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['nama_kelas'] ?></td>
        <td><?= $row['deskripsi'] ?></td>
        <td><?= $row['kode_kelas'] ?></td>
        <td><?= $row['nama_guru'] ?? '-' ?></td>
        <td>
            <?php if($_SESSION['role']=='admin' || $_SESSION['id']==$row['id_guru']): ?>
                <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> | 
                <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin?')">Hapus</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
