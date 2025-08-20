<?php
session_start();
include "../config.php";

// Cek apakah login
if (!isset($_SESSION['id'])) {
    die("Akses ditolak! Anda harus login dulu.");
}

$id_user = $_SESSION['id'];

// Query semua kelas
$result = mysqli_query($conn, "
    SELECT kelas.*, users.username AS nama_guru
    FROM kelas
    LEFT JOIN users ON kelas.id_guru = users.id
");

// Ambil kelas yang sudah diikuti user
$kelas_saya = [];
$res_kelas_saya = mysqli_query($conn, "SELECT id_kelas FROM kelas_siswa WHERE id_siswa=$id_user");
while($row = mysqli_fetch_assoc($res_kelas_saya)) {
    $kelas_saya[] = $row['id_kelas'];
}

?>

<h2>Daftar Kelas Saya</h2>
<a href="../dashboard.php">Dashboard</a>
<table border="1" cellpadding="5">
    <tr>
        <th>ID</th><th>Kelas</th><th>Deskripsi</th><th>Kode</th><th>Guru</th><th>aksi</th>
    </tr>
    <?php while($row = mysqli_fetch_assoc($result)): ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['nama_kelas'] ?></td>
        <td><?= $row['deskripsi'] ?></td>
        <td><?= $row['kode_kelas'] ?></td>
        <td><?= $row['nama_guru'] ?? '-' ?></td>

        <td>
            <?php if (in_array($row['id'], $kelas_saya)): ?>
                ✅ Sudah bergabung
            <?php else: ?>
                <a href="join.php?id=<?= $row['id'] ?>">Gabung</a>
            <?php endif; ?>
        </td>
    </tr>
    <?php endwhile; ?>
</table>
