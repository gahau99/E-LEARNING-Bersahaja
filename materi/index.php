<?php
session_start();
include "../config.php";

// hanya guru dan admin yang bisa mengakses halaman ini
if (!isset($_SESSION['id']) || !in_array($_SESSION['role'], ['guru','admin'])) {
    die("Akses ditolak!");
}

$result = mysqli_query($conn, "
    SELECT materi.*, kelas.nama_kelas, users.username AS pembuat
    FROM materi
    JOIN kelas ON materi.id_kelas = kelas.id
    JOIN users ON materi.dibuat_oleh = users.id
");
?>

<h2>Daftar Materi</h2>
<a href="tambah.php">Tambah Materi</a> | <a href="../dashboard.php">Dashboard</a>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th>
        <th>Kelas</th>
        <th>Judul</th>
        <th>File</th>
        <th>Pembuat</th>
        <th>Status</th>
        <th>Aksi</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['nama_kelas'] ?></td>
        <td><?= $row['judul'] ?></td>
        <td>
            <?php if($row['file']): ?>
                <a href="view.php?id=<?= $row['id'] ?>" target="_blank">Lihat</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
        <td><?= $row['pembuat'] ?></td>
        <td><?= $row['status'] ?></td>
        <td>
            <!-- Hanya admin yang bisa approve/reject -->
            <?php if($_SESSION['role']=='admin' && $row['status'] === 'pending'): ?>
                <a href="approve.php?id=<?= $row['id'] ?>">Approve</a> | 
                <a href="reject.php?id=<?= $row['id'] ?>">Reject</a> | 
            <?php endif; ?>

            <!-- Admin atau guru yang membuat materi bisa edit/hapus -->
            <?php if($_SESSION['role']=='admin' || $_SESSION['id']==$row['dibuat_oleh']): ?>
                <a href="edit.php?id=<?= $row['id'] ?>">Edit</a> | 
                <a href="hapus.php?id=<?= $row['id'] ?>" onclick="return confirm('Yakin hapus?')">Hapus</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
