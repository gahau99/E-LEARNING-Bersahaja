<?php
session_start();
include "../config.php";

if (!isset($_SESSION['id']) || $_SESSION['role'] !== 'siswa') {
    die("Akses hanya untuk siswa!");
}

$id_user = $_SESSION['id'];

// Ambil kelas yang diikuti siswa
$result = mysqli_query($conn, "
    SELECT materi.*, kelas.nama_kelas
    FROM materi
    JOIN kelas ON materi.id_kelas = kelas.id
    JOIN kelas_siswa ks ON ks.id_kelas = kelas.id
    WHERE ks.id_siswa = $id_user AND materi.status = 'approved'
");
?>

<h2>Materi Saya</h2>
<a href="../dashboard.php">Dashboard</a>

<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>Kelas</th><th>Judul</th><th>Deskripsi</th><th>File</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['nama_kelas'] ?></td>
        <td><?= $row['judul'] ?></td>
        <td><?= $row['deskripsi'] ?></td>
        <td>
            <?php if($row['file']): ?>
                <a href="view.php?id=<?= $row['id'] ?>" target="_blank">Lihat</a>
                <a href="../uploads/<?= $row['file'] ?>" target="_blank">Download</a>
            <?php else: ?>
                -
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>


